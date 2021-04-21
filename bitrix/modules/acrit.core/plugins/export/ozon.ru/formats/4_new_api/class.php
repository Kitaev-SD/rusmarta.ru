<?
/**
 * Acrit Core: ozon.ru plugin
 * @documentation https://docs.ozon.ru/api/seller
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\Api,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\CategoryTable as Category,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\AttributeTable as Attribute,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\AttributeValueTable as AttributeValue,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\TaskTable as Task,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\HistoryTable as History;
	

class OzonRuV2 extends UniversalPlugin {
	
	const DATE_UPDATED = '2021-03-05';
	const CACHE_VALID_TIME = 7*24*60*60; // Too many values => we would not update often
	const AJAX_STEP_TIME = 5; // 5 seconds to every ajax step
	const ATTRIBUTE_ID = 'attribute_%s_%s';
	
	const GROUPED_CODE = 'GROUPED';

	protected static $bSubclass = true;
	
	# Basic settings
	protected $arSupportedFormats = ['JSON']; // Формат выгрузки - JSON
	protected $bApi = true; // Выгружаем не в файл, а по АПИ
	protected $bCategoriesExport = true; // Нужно чтобы в целом была возможность работать с категориями, хотя категории отдельно не выгружаются
	protected $bCategoriesList = true; // В плагине доступен список категорий, необходимо для работы со списком категорий
	protected $bCategoriesUpdate = true; // Разрешаем обновлять категории
	protected $bCategoriesStrict = true; // На озоне важно указывать только «озоновские» категории
	protected $bCategoryCustomName = true; // Добавляем возможность использовать значение «Использовать поля товаров» в опции «Источник названий категорий»
	protected $intExportPerStep = 50; // 50 товаров за 1 шаг
	
	# Misc
	protected $strCategoryLevelDelimiter = ' / '; // Символ(ы) для разделения категорий разных уровней (пример: Авто / Оборудование / Магнитолы)
	
	# API class
	protected $API;
	
	# Cache
	protected $arCacheRequiredAttributes = [];
	protected $arCacheDictionaryAttributes = [];
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		# Add common attributes
		$arResult['HEADER_GENERAL'] = [];
		$arResult['offer_id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
		$arResult['name'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
		$arResult['images'] = ['FIELD' => ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'], 'MULTIPLE' => true, 'REQUIRED' => true];
		$arResult['image_group_id'] = ['CONST' => ''];
		$arResult['pdf_list'] = ['FIELD' => 'PROPERTY_PDF', 'MULTIPLE' => true,];
		$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT', 'REQUIRED' => true];
		$arResult['old_price'] = ['FIELD' => 'CATALOG_PRICE_1'];
		$arResult['premium_price'] = [];
		$arResult['vat'] = ['FIELD' => 'CATALOG_VAT_VALUE_FLOAT'];
		$arResult['stock'] = [];
		#$arResult['warehouse_id'] = ['CONST' => ''];
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		$arResult['depth'] = ['FIELD' => 'CATALOG_LENGTH', 'REQUIRED' => true];
		$arResult['width'] = ['FIELD' => 'CATALOG_WIDTH', 'REQUIRED' => true];
		$arResult['height'] = ['FIELD' => 'CATALOG_HEIGHT', 'REQUIRED' => true];
		$arResult['dimension_unit'] = ['CONST' => 'mm', 'REQUIRED' => true];
		$arResult['weight'] = ['FIELD' => 'CATALOG_WEIGHT', 'REQUIRED' => true];
		$arResult['weight_unit'] = ['CONST' => 'g', 'REQUIRED' => true];
		$arResult['category_id'] = ['REQUIRED' => !!$this->bAdmin];
		# Add special attributes (depends on category)
		$arData = $this->getDataForFields($intIBlockId);
		foreach($arData as $arCategory){
			$arResult['HEADER_CATEGORY_'.$arCategory['CATEGORY_ID']] = [
				'NAME' => $this->formatCategoryName($arCategory['CATEGORY_ID'], $arCategory['NAME']),
				'NORMAL_CASE' => true,
			];
			foreach($arCategory['ATTRIBUTES'] as $arAttribute){
				$strAttributeId = sprintf(static::ATTRIBUTE_ID, $arCategory['CATEGORY_ID'], $arAttribute['ATTRIBUTE_ID']);
				
				$arField = [
					'NAME' => $arAttribute['NAME'],
					'DISPLAY_CODE' => 'attribute_'.$arAttribute['ATTRIBUTE_ID'],
					'DESCRIPTION' => $this->descriptionToHint($arAttribute['DESCRIPTION']),
					'REQUIRED' => count($arData) == 1 && $arAttribute['IS_REQUIRED'] == 'Y' 
						|| $arAttribute['FORCE_REQUIRED'] == 'Y',
					'MULTIPLE' => true,
					'CUSTOM_REQUIRED' => $arAttribute['IS_REQUIRED'] == 'Y',
					'PARAMS' => ['MULTIPLE' => 'multiple'],
				];
				if($arAttribute['DICTIONARY_ID']){
					$arField['ALLOWED_VALUES_CUSTOM'] = true;
				}
				$this->guessDefaultValue($arField, $arAttribute);
				$arResult[$strAttributeId] = $arField;
			}
		}
		#
		return $arResult;
	}
	
	/**
	 *	Convert simple description text to hint
	 */
	protected function descriptionToHint($strDescription){
		$strDescription = htmlspecialcharsbx($strDescription);
		$strPattern = '#((http|https|ftp|ftps)://[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,6}(\/\S*)?)#';
		$strReplace = '<a href="$1" target="_blank">$1</a>';
		$strDescription = preg_replace($strPattern, $strReplace, $strDescription);
		$strDescription = nl2br($strDescription);
		return $strDescription;
	}
	
	/**
	 *	Prepare fields data for getUniversalFields()
	 */
	protected function getDataForFields($intIBlockId){
		$arResult = [];
		$arCatalog = Helper::getCatalogArray($intIBlockId);
		$intMainIBlockId = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'] 
			? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		$arUsedCategories = $this->getUsedCategories($intMainIBlockId);
		$arUsedCategoriesId = array_keys($arUsedCategories);
		$arNotRequiredAttributes = $this->arProfile['IBLOCKS'][$intMainIBlockId]['PARAMS']['ATTRIBUTES_CANCEL_REQUIRED'];
		$arNotRequiredAttributes = Helper::explodeValues($arNotRequiredAttributes);
		$arNotRequiredAttributes = array_filter($arNotRequiredAttributes);
		if(!empty($arUsedCategoriesId)){
			$resCategories = Category::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'NAME'],
			]);
			while($arCategory = $resCategories->fetch()){
				$arCategory['ATTRIBUTES'] = [];
				$arResult[$arCategory['CATEGORY_ID']] = $arCategory;
			}
			$resAttributes = Attribute::getList([
				'order' => ['NAME' => 'ASC'],
				'filter' => ['CATEGORY_ID' => $arUsedCategoriesId],
				'select' => ['ID', 'CATEGORY_ID', 'ATTRIBUTE_ID', 'DICTIONARY_ID', 'NAME', 'DESCRIPTION', 'TYPE', 
					'IS_COLLECTION', 'IS_REQUIRED', 'GROUP_ID', 'GROUP_NAME'],
			]);
			while($arAttribute = $resAttributes->fetch()){
				if(in_array($arAttribute['ATTRIBUTE_ID'], $arNotRequiredAttributes)){
					$arAttribute['IS_REQUIRED'] = 'N';
				}
				$arResult[$arAttribute['CATEGORY_ID']]['ATTRIBUTES'][$arAttribute['ATTRIBUTE_ID']] = $arAttribute;
			}
		}
		# Group
		if(!empty($arResult) && $this->isAttributesGrouped($intMainIBlockId)){
			# Find common attributes
			$arCommonAttributes = [];
			foreach($arResult as $arGroup){
				$arCommonAttributes[] = $arGroup['ATTRIBUTES'];
			}
			$arCommonAttributes = call_user_func_array('array_intersect_key', $arCommonAttributes);
			foreach($arCommonAttributes as $intAttrId => $arAttr){
				if(!$this->isAttributeDictionaryCommon($intAttrId)){
					unset($arCommonAttributes[$intAttrId]);
				}
			}
			# Check required
			foreach($arCommonAttributes as $intAttrId => $arAttr){
				foreach($arResult as $arGroup){
					if($arGroup['ATTRIBUTES'][$intAttrId]['IS_REQUIRED'] == 'Y'){
						$arCommonAttributes[$intAttrId]['IS_REQUIRED'] = 'Y';
						$arCommonAttributes[$intAttrId]['FORCE_REQUIRED'] = 'Y';
					}
				}
			}
			# Remove common attributes from categories
			if(!empty($arCommonAttributes)){
				foreach($arResult as $intGroupId => $arGroup){
					foreach($arCommonAttributes as $intAttrId => $arAttr){
						if(isset($arGroup['ATTRIBUTES'][$intAttrId])){
							unset($arResult[$intGroupId]['ATTRIBUTES'][$intAttrId]);
						}
					}
				}
			}
			# Create new virtual category
			if(!empty($arCommonAttributes)){
				$arResult = array_merge([
					static::GROUPED_CODE => [
						'ID' => false,
						'CATEGORY_ID' => static::GROUPED_CODE,
						'NAME' => static::getMessage('GROUPED_ATTRIBUTES_HEADER'),
						'ATTRIBUTES' => $arCommonAttributes,
					],
				], $arResult);
			}
		}
		return $arResult;
	}
	
	/**
	 *	Check if attributes are grouped for iblock
	 */
	protected function isAttributesGrouped($intIBlockId){
		return $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS']['GROUP_ATTRIBUTES'] == 'Y';
	}
	
	/**
	 *	Try to guess default value
	 */
	protected function guessDefaultValue(&$arField, $arAttribute){
		if($arAttribute['TYPE'] == 'ImageURL'){
			$arField['FIELD'] = ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'];
			$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
			$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_BRAND')){
			$arField['FIELD'] = ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF'];
		}
		elseif($arAttribute['NAME'] == static::getMessage('GUESS_GROUP')){
			$arField['FIELD'] = ['ID'];
		}
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__.'/include/classes/api.php';
		require_once __DIR__.'/include/classes/attribute.php';
		require_once __DIR__.'/include/classes/attributevalue.php';
		require_once __DIR__.'/include/classes/category.php';
		require_once __DIR__.'/include/classes/task.php';
		require_once __DIR__.'/include/classes/history.php';
		require_once __DIR__.'/include/db_table_create.php';
	}
	
	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		if(!$this->API){
			$this->API = new OzonRuHelpers\Api($this->arParams['CLIENT_ID'], $this->arParams['API_KEY'], $this->intProfileId,
				$this->strModuleId);
		}
	}
	
	/**
	 *	Get saved categories, if not exists - download it
	 */
	public function getCategoriesList($intProfileId){
		$intLastUpdateTime = $this->getCategoriesDate();
		if(!$intLastUpdateTime || $intLastUpdateTime <= time() - 24*60*60){
			$this->updateCategories($this->intProfileId);
		}
		$arResult = [];
		$resCategories = Category::getList(['order' => ['NAME' => 'ASC'], 'select' => ['CATEGORY_ID', 'NAME']]);
		while($arCategory = $resCategories->fetch()){
			$arResult[$arCategory['CATEGORY_ID']] = $arCategory['NAME'];
		}
		// Sort categories, but numerical (such a "18+") in the end of the list
		uasort($arResult, function($a, $b){
			if(is_numeric(substr($a['NAME'], 0, 1)) xor is_numeric(substr($b['NAME'], 0, 1))){
				return strnatcmp($a['NAME'], $b['NAME']) * -1;
			}
			return strnatcmp($a['NAME'], $b['NAME']);
		});
		array_walk($arResult, function(&$value, $key) {
			$value = $this->formatCategoryName($key, $value);
		});
		#array_walk($arResult, function(&$value, $key) {$value = sprintf('[%s] %s', $key, $value);});
		unset($resCategories, $arCategory);
		return $arResult;
	}

	/**
	 *	Get categories date update
	 */
	public function getCategoriesDate(){
		$resCategory = Category::getList(['order' => ['TIMESTAMP_X' => 'DESC'], 'select' => ['TIMESTAMP_X'], 'limit' => 1]);
		if($arCategory = $resCategory->fetch()){
			if(is_object($arCategory['TIMESTAMP_X'])){
				return $arCategory['TIMESTAMP_X']->getTimeStamp();
			}
		}
		unset($resCategory, $arCategory);
		return false;
	}
	
	/**
	 *	Update categories from server using API
	 */
	public function updateCategories($intProfileId){
		$strCommand = '/v1/categories/tree';
		$arJsonResponse = $this->API->execute($strCommand);
		$strSessionId = session_id();
		if(is_array($arJsonResponse['result'])){
			$this->processUpdatedCategory($arJsonResponse['result'], $strSessionId);
		}
		else{
			$strLogMessage = static::getMessage('ERROR_CATEGORIES_EMPTY_ANSWER', ['#URL#' => $strCommand]);
			$this->addToLog($strLogMessage);
		}
		Category::deleteByFilter([
			'!SESSION_ID' => $strSessionId,
		]);
		return true;
	}
	
	/**
	 *	Convert categories tree to plain list (recursively)
	 */
	protected function processUpdatedCategory($arCategoriesCurrent, $strSessionId, $arName=[], $bRecurred=false){
		if(is_array($arCategoriesCurrent)){
			foreach($arCategoriesCurrent as $arCategory){
				$arNameChain = array_merge($arName, [$arCategory['category_id'] => $arCategory['title']]);
				if(!empty($arCategory['children'])){
					$this->processUpdatedCategory($arCategory['children'], $strSessionId, $arNameChain, true);
				}
				else{
					$arFields = [
						'CATEGORY_ID' => $arCategory['category_id'],
						'NAME' => implode($this->strCategoryLevelDelimiter, $arNameChain),
						'SESSION_ID' => session_id(),
						'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
					];
					$arFilter = [
						'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					];
					$resDBItem = Category::getList(['filter' => $arFilter, 'select' => ['ID']]);
					if($arDbItem = $resDBItem->fetch()){
						Category::update($arDbItem['ID'], $arFields);
					}
					else{
						Category::add($arFields);
					}
				}
			}
		}
	}
	
	/**
	 *	Custom block in subtab 'Categories'
	 */
	public function categoriesCustomActions($intIBlockID, $arIBlockParams){
		return $this->includeHtml(__DIR__.'/include/attribute_update/settings.php', [
			'IBLOCK_ID' => $intIBlockID,
			'IBLOCK_PARAMS' => $arIBlockParams,
		]);
	}
	
	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['CLIENT_ID'] = $this->includeHtml(__DIR__.'/include/settings/client_id.php');
		$arSettings['API_KEY'] = $this->includeHtml(__DIR__.'/include/settings/api_key.php');
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'check_access':
				$this->checkAccess($arParams, $arJsonResult);
				break;
			case 'category_attributes_update':
				$this->ajaxUpdateCategories($arParams, $arJsonResult);
				break;
			case 'refresh_tasks_list':
				$arJsonResult['HTML'] = $this->getLogContent($strLogCustomTitle=false, $arParams['GET']);
				break;
			case 'update_task_status':
				$arJsonResult['HTML'] = $this->updateTaskStatus($arParams['GET']['task_id'], $arJsonResult);
				break;
			case 'allowed_values_custom':
				$arJsonResult['HTML'] = $this->getAllowedValuesContent($arParams['GET']);
				break;
			case 'allowed_values_filter':
				$arJsonResult['HTML'] = $this->getAllowedValuesFilteredContent($arParams['GET']);
				break;
			case 'task_json_preview':
				$arJsonResult['HTML'] = $this->getTaskJsonPreview($arParams['GET']);
				break;
			case 'history_item_json_preview':
				$arJsonResult['HTML'] = $this->getHistoryItemJsonPreview($arParams['GET']);
				break;
		}
	}
	
	/**
	 *	Check clientId and apiKey (for info only)
	 */
	protected function checkAccess($arParams, &$arJsonResult){
		$arJsonResult['Success'] = false;
		$strClientId = $arParams['GET']['client_id'];
		$strApiKey = $arParams['GET']['api_key'];
		$arJsonRequest = [
			'offer_id' => '#ACRIT_CHECK#',
		];
		$obApi = new OzonRuHelpers\Api($strClientId, $strApiKey, $this->intProfileId, $this->strModuleId);
		$arQueryResult = $obApi->execute('/v2/product/info', $arJsonRequest, ['METHOD' => 'POST', 'SKIP_ERRORS' => true]);
		unset($obApi);
		if($arQueryResult['error']['code'] == 'NOT_FOUND_ERROR'){
			$arJsonResult['Success'] = true;
			$arJsonResult['Message'] = static::getMessage('MESSAGE_CHECK_ACCESS_SUCCESS');
		}
		else{
			$arJsonResult['Message'] = static::getMessage('MESSAGE_CHECK_ACCESS_DENIED');
		}
	}
	
	/**
	 *	Update category attritbutes and dictionaries
	 */
	protected function ajaxUpdateCategories($arParams, &$arJsonResult){
		$arSession = &$_SESSION['ACRIT_EXP_OZON_CAT_ATTR_UPDATE'];
		$arPost = &$arParams['POST'];
		$bStart = false;
		if($arPost['start'] == 'Y'){
			$bStart = true;
			$arJsonResult['Action'] = 'Start';
			$arSession = [
				'ID' => session_id(),
				'CATEGORIES' => $this->getUsedCategories($arPost['iblock_id'], true),
				'ATTRIBUTES' => [],
				'COUNT' => 0,
				'INDEX' => 0,
				'CATEGORY_ID' => false,
				'CATEGORY_NAME' => false,
				'ATTRIBUTE_ID' => false,
				'ATTRIBUTE_NAME' => false,
				'SUB_INDEX' => 0,
				'FORCED' => $arPost['force'] == 'Y',
				'JUST_ATTR' => $arPost['just_attr'] == 'Y',
				#'SUB_COUNT' => 0, // Unfortunately, ozon does not provide this information :(
			];
			$arSession['COUNT'] = count($arSession['CATEGORIES']);
			$arJsonResult['Continue'] = true;
			if($arSession['FORCED']){
				foreach($arSession['CATEGORIES'] as $intCategoryId){
					Attribute::deleteByFilter(['CATEGORY_ID' => $intCategoryId]);
					AttributeValue::deleteByFilter(['CATEGORY_ID' => $intCategoryId]);
				}
			}
		}
		else{
			# Update values if it in queue
			if(!empty($arSession['ATTRIBUTES'])){
				$arJsonResult['Action'] = 'Attributes';
				do{
					foreach($arSession['ATTRIBUTES'] as $intAttrId => $arAttr){
						$arAttr['START_TIME'] = isset($arAttr['TIME_START']) ? $arAttr['TIME_START'] : microtime(true);
						$arAttr['COUNT_SUCCESS'] = isset($arAttr['COUNT_SUCCESS']) ? $arAttr['COUNT_SUCCESS'] : 0;
						$arAttr['ID'] = $intAttrId;
						$arSession['ATTRIBUTE_ID'] = $arAttr['ID'];
						$arSession['ATTRIBUTE_NAME'] = $arAttr['NAME'];
						$arSession['ATTRIBUTE_DICTIONARY_ID'] = $arAttr['DIC'];
						$arUpdateResult = $this->updateAttrubuteValues($arAttr, $arSession['ID']);
						$arAttr['COUNT_SUCCESS'] += $arUpdateResult['COUNT_SUCCESS'];
						if($arUpdateResult['CONTINUE'] && $arUpdateResult['LAST_ID']){
							$arAttr['LAST_ID'] = $arUpdateResult['LAST_ID'];
							$arSession['ATTRIBUTES'][$intAttrId] = $arAttr;
							$arSession['SUB_INDEX'] += $arUpdateResult['COUNT_SUCCESS'];
						}
						else{
							# Save some metainfo to attribute
							$arAttributeFields = [
								'LAST_VALUES_COUNT' => $arAttr['COUNT_SUCCESS'],
								'LAST_VALUES_DATETIME' => new \Bitrix\Main\Type\Datetime(),
								'LAST_VALUES_ELAPSED_TIME' => microtime(true) - $arAttr['START_TIME'],
							];
							$arFilter = [
								'CATEGORY_ID' => $arAttr['CAT'],
								'ATTRIBUTE_ID' => $intAttrId,
							];
							$resDbAttribute = Attribute::getList(['filter' => $arFilter, 'select' => ['ID']]);
							if($arDbAttribute = $resDbAttribute->fetch()){
								Attribute::update($arDbAttribute['ID'], $arAttributeFields);
							}
							# Inc index
							$arSession['ATTRIBUTE_INDEX']++;
							# Clear temporary data
							$arSession['SUB_INDEX'] = 0;
							unset($arSession['ATTRIBUTES'][$intAttrId]);
						}
						break;
					}
				} while($this->ajaxHaveTime());
			}
			# Update attribtutes if it in queue
			else{
				$arJsonResult['Action'] = 'Categories';
				foreach($arSession['CATEGORIES'] as $key1 => $intCategoryId){
					$arSession['ATTRIBUTE_ID'] = false;
					$arSession['ATTRIBUTE_NAME'] = false;
					$arAttributes = $this->updateCategoryAttrubutes($intCategoryId, $arSession['ID']);
					if($arAttributes){
						foreach($arAttributes as $key2 => $arAttr){
							if($arAttr['DICTIONARY_ID']){
								$arAttributes[$key2] = [
									'CAT' => $arAttr['CATEGORY_ID'],
									'NAME' => $arAttr['NAME'],
									'DIC' => $arAttr['DICTIONARY_ID'],
								];
							}
							else{
								unset($arAttributes[$key2]);
							}
						}
						if($arSession['JUST_ATTR'] != 'Y'){
							$arSession['ATTRIBUTES'] = $arAttributes;
							$arSession['ATTRIBUTE_ID'] = $arAttr['ID'];
							$arSession['ATTRIBUTE_NAME'] = $arAttr['NAME'];
							$arSession['ATTRIBUTE_DICTIONARY_ID'] = $arAttr['DICTIONARY_ID'];
							$arSession['ATTRIBUTE_INDEX'] = 1;
							$arSession['ATTRIBUTE_COUNT'] = count($arAttributes);
						}
					}
					$arSession['INDEX']++;
					$arSession['SUB_INDEX'] = 0;
					$arSession['CATEGORY_ID'] = $intCategoryId;
					$arSession['CATEGORY_NAME'] = $this->getCategoryName($intCategoryId);
					unset($arSession['CATEGORIES'][$key1]);
					if(!$this->ajaxHaveTime()){
						break;
					}
				}
			}
			$arJsonResult['Continue'] = true;
			if(empty($arSession['CATEGORIES']) && empty($arSession['ATTRIBUTES'])){
				$arSession['FINISHED'] = true;
				$arJsonResult['Continue'] = false;
			}
		}
		$arJsonResult['SessionId'] = $arSession['ID'];
		$arJsonResult['Count'] = $arSession['COUNT'];
		$arJsonResult['Index'] = $arSession['INDEX'];
		$arJsonResult['Percent'] = $arSession['COUNT'] == 0 ? 0 : round($arSession['INDEX'] * 100 / $arSession['COUNT']);
		$arJsonResult['CategoryId'] = $arSession['CATEGORY_ID'];
		$arJsonResult['CategoryName'] = $arSession['CATEGORY_NAME'];
		$arJsonResult['AttributeId'] = $arSession['ATTRIBUTE_ID'];
		$arJsonResult['AttributeName'] = $arSession['ATTRIBUTE_NAME'];
		$arJsonResult['AttributeIndex'] = $arSession['ATTRIBUTE_INDEX'];
		$arJsonResult['AttributeCount'] = $arSession['ATTRIBUTE_COUNT'];
		$arJsonResult['AttributeDictionaryId'] = $arSession['ATTRIBUTE_DICTIONARY_ID'];
		$arJsonResult['SubIndex'] = $arSession['SUB_INDEX'];
		ob_start();
		require __DIR__.'/include/attribute_update/status.php';
		$arJsonResult['Html'] = ob_get_clean();
	}
	
	/**
	 *	Check time for ajax step
	 */
	protected function ajaxHaveTime(){
		return false;
		return microtime(true) - $this->fTimeStart < static::AJAX_STEP_TIME;
	}
	
	/**
	 *	Get used ozon categories from redefinitions
	 */
	protected function getUsedCategories($intIBlockId=null, $bJustIds=false){
		$arResult = [];
		$arIBlockParams = $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS'];
		if($arIBlockParams['CATEGORIES_ALTERNATIVE'] == 'Y'){
			if(is_array($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'])){
				foreach($arIBlockParams['CATEGORIES_ALTERNATIVE_LIST'] as $intCategoryId){
					if(is_numeric($intCategoryId)){
						$arResult[$intCategoryId] = $this->getCategoryName($intCategoryId);
					}
				}
			}
		}
		else{
			$arRedefinitions = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getForProfile', 
				[$this->intProfileId, $intIBlockId]);
			$arSelectedCategories = Exporter::getInvolvedSectionsID($intIBlockId, 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_ID'], 
				$this->arProfile['IBLOCKS'][$intIBlockId]['SECTIONS_MODE']);
			$arUsedCategories = array_intersect_key($arRedefinitions, array_flip($arSelectedCategories));
			foreach($arUsedCategories as $strCategoryName){
				if(strlen($strCategoryName)){
					$strCategoryId = $this->parseCategoryId($strCategoryName);
					$arResult[$strCategoryId] = $strCategoryName;
				}
			}
		}
		if($bJustIds){
			$arResult = array_keys($arResult);
		}
		return $arResult;
	}
	
	/**
	 *	"[17034083] Category 1 / Category 2 / Product name" => 17034083, &name => "Category 1 / Category 2 / Product name"
	 */
	protected function parseCategoryId(&$strCategoryName){
		if(strlen($strCategoryName) && preg_match('#^\[(\d+)\][\s]*(.*?)$#', $strCategoryName, $arMatch)){
			$strCategoryName = $arMatch[2];
			return $arMatch[1];
		}
		return false;
	}
	
	/**
	 *	
	 */
	protected function getCategoryName($intCategoryId){
		$strResult = '';
		$resCategory = Category::getList(['filter' => ['CATEGORY_ID' => $intCategoryId], 'select' => ['NAME']]);
		if($arCategory = $resCategory->fetch()){
			$strResult = $arCategory['NAME'];
		}
		return $strResult;
	}
	
	/**
	 *	Format category name with ID
	 */
	protected function formatCategoryName($intCategoryId, $strCategoryName=null){
		if(!strlen($strCategoryName) && $intCategoryId > 0){
			$strCategoryName = $this->getCategoryName($intCategoryId);
		}
		if(!is_numeric($intCategoryId) || !$intCategoryId){
			return $strCategoryName;
		}
		return sprintf('[%d] %s', $intCategoryId, $strCategoryName);
	}
	
	/**
	 *	Update attributes for single category
	 */
	protected function updateCategoryAttrubutes($intCategoryId, $strSessionId){
		$strCommand = '/v2/category/attribute';
		$arJsonRequest = [
			'category_id' => $intCategoryId,
		];
		$arJsonResponse = $this->API->execute($strCommand, $arJsonRequest, ['METHOD' => 'POST']);
		if(is_array($arJsonResponse['result']) && !empty($arJsonResponse['result'])){
			$arResult = [];
			foreach($arJsonResponse['result'] as $arItem){
				$arFields = [
					'CATEGORY_ID' => $intCategoryId,
					'ATTRIBUTE_ID' => $arItem['id'],
					'DICTIONARY_ID' => $arItem['dictionary_id'],
					'NAME' => $arItem['name'],
					'DESCRIPTION' => $arItem['description'],
					'TYPE' => $arItem['type'],
					'IS_COLLECTION' => $arItem['is_collection'] == 1 ? 'Y' : 'N',
					'IS_REQUIRED' => $arItem['is_required'] == 1 ? 'Y' : 'N',
					'GROUP_ID' => $arItem['group_id'],
					'GROUP_NAME' => $arItem['group_name'],
					'SESSION_ID' => $strSessionId,
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
				];
				$arFilter = [
					'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
				];
				$arSelect = [
					'ID',
					'LAST_VALUES_COUNT',
					'LAST_VALUES_DATETIME',
				];
				$resDBItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
				if($arDbItem = $resDBItem->fetch()){
					Attribute::update($arDbItem['ID'], $arFields);
					$arFields['LAST_VALUES_COUNT'] = $arDbItem['LAST_VALUES_COUNT'];
					if(is_object($arDbItem['LAST_VALUES_DATETIME'])){
						$bActual = microtime(true) - $arDbItem['LAST_VALUES_DATETIME']->getTimestamp() < static::CACHE_VALID_TIME;
						if($bActual){
							unset($arFields);
						}
					}
				}
				else{
					Attribute::add($arFields);
				}
				if($arFields){
					$arResult[$arItem['id']] = $arFields;
				}
			}
			Attribute::deleteByFilter([
				'CATEGORY_ID' => $intCategoryId,
				'!SESSION_ID' => $strSessionId,
			]);
			unset($arJsonResponse['result']);
			return $arResult;
		}
		return false;
	}
	
	/**
	 *	Update dictionary
	 *	@return true if process is not finished (by has_next)
	 */
	protected function updateAttrubuteValues($arAttr, $strSessionId){
		$arResult = [
			'LAST_ID' => false,
			'CONTINUE' => false,
			'COUNT_SUCCESS' => 0,
		];
		$strCommand = '/v2/category/attribute/values';
		$intLimit = intVal(Helper::getOption($this->strModuleId, 'ozon_new_api_step_size'));
		if($intLimit <= 0){
			$intLimit = 5000; // max tested allowed value - 5000, but ozon support recommends 1000
		}
		$arJsonRequest = [
			'category_id' => $arAttr['CAT'],
			'attribute_id' => $arAttr['ID'],
			'limit' => $intLimit,
		];
		if($arAttr['LAST_ID']){
			$arJsonRequest['last_value_id'] = $arAttr['LAST_ID'];
		}
		$arJsonResponse = $this->API->execute($strCommand, $arJsonRequest, ['METHOD' => 'POST']);
		if(is_array($arJsonResponse['result'])){
			$strSaveData = '';
			\Bitrix\Main\Application::getConnection()->startTransaction();
			foreach($arJsonResponse['result'] as $arItem){
				$arFields = [
					'CATEGORY_ID' => $arAttr['CAT'],
					'ATTRIBUTE_ID' => $arAttr['ID'],
					'DICTIONARY_ID' => $arAttr['DIC'],
					'VALUE_ID' => $arItem['id'],
					'VALUE' => $arItem['value'],
					'SESSION_ID' => $strSessionId,
					'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
				];
				$arFilter = [
					'CATEGORY_ID' => $arFields['CATEGORY_ID'],
					'ATTRIBUTE_ID' => $arFields['ATTRIBUTE_ID'],
					'DICTIONARY_ID' => $arFields['DICTIONARY_ID'],
					'VALUE_ID' => $arFields['VALUE_ID'],
				];
				if($this->isAttributeDictionaryCommon($arAttr['ID'])){
					unset($arFields['CATEGORY_ID'], $arFilter['CATEGORY_ID']);
				}
				$resDBItem = AttributeValue::getList(['filter' => $arFilter, 'select' => ['ID']]);
				if($arDbItem = $resDBItem->fetch()){
					AttributeValue::update($arDbItem['ID'], $arFields);
				}
				else{
					AttributeValue::add($arFields);
				}
				$arResult['LAST_ID'] = $arItem['id'];
				$arResult['COUNT_SUCCESS']++;
			}
			\Bitrix\Main\Application::getConnection()->commitTransaction();
			if(!$arJsonResponse['has_next']){
				$arDeleteFilter = [
					'CATEGORY_ID' => $arAttr['CAT'],
					'ATTRIBUTE_ID' => $arAttr['ID'],
					'!SESSION_ID' => $strSessionId,
				];
				if($this->isAttributeDictionaryCommon($arAttr['ID'])){
					unset($arDeleteFilter['CATEGORY_ID']);
				}
				AttributeValue::deleteByFilter($arDeleteFilter);
			}
			else {
				$arResult['CONTINUE'] = true;
			}
		}
		return $arResult;
	}
	
	/**
	 *	Check atribute required (used in processElement)
	 */
	protected function isAttributeRequired($intCategoryId, $intAttributeId){
		if(!is_array($this->arCacheRequiredAttributes[$intCategoryId])){
			$this->arCacheRequiredAttributes[$intCategoryId] = [];
			$resQuery = Attribute::getList([
				'filter' => ['CATEGORY_ID' => $intCategoryId, 'IS_REQUIRED' => 'Y'],
				'select' => ['ATTRIBUTE_ID'],
			]);
			while($arItem = $resQuery->fetch()){
				$this->arCacheRequiredAttributes[$intCategoryId][$arItem['ATTRIBUTE_ID']] = true;
			}
		}
		return isset($this->arCacheRequiredAttributes[$intCategoryId][$intAttributeId]);
	}
	
	/**
	 *	Check atribute is a dictionary
	 *	@return false || dictionary_id
	 */
	protected function isAttributeDictionary($intCategoryId, $intAttributeId){
		if(!is_array($this->arCacheDictionaryAttributes[$intCategoryId])){
			$this->arCacheDictionaryAttributes[$intCategoryId] = [];
			$resQuery = Attribute::getList([
				'filter' => ['CATEGORY_ID' => $intCategoryId, '>DICTIONARY_ID' => 0],
				'select' => ['ATTRIBUTE_ID', 'DICTIONARY_ID'],
			]);
			while($arItem = $resQuery->fetch()){
				$this->arCacheDictionaryAttributes[$intCategoryId][$arItem['ATTRIBUTE_ID']] = $arItem['DICTIONARY_ID'];
			}
		}
		$intDictionaryId = $this->arCacheDictionaryAttributes[$intCategoryId][$intAttributeId];
		return $intDictionaryId > 0 ? $intDictionaryId : false;
	}
	
	/**
	 *	Check if dictionary has same values: all attributes but Type and Commercial types [by support, 2020-08-05]
	 */
	protected function isAttributeDictionaryCommon($intAttributeId){
		$arUniqueDictionaries = [
			8229, // Type
			9461, // Commercial type
		];
		return !in_array($intAttributeId, $arUniqueDictionaries);
	}
	
	/**
	 *	Parse attribute id: attribute_1231231_213
	 */
	protected function parseAttributeId($strAttributeId){
		$strPattern = static::ATTRIBUTE_ID;
		$strPattern = str_replace('%s', '([A-z0-9]+)', $strPattern);
		$strPattern = sprintf('#^%s$#', $strPattern);
		if(preg_match($strPattern, $strAttributeId, $arMatch)){
			return [
				'CATEGORY_ID' => $arMatch[1],
				'ATTRIBUTE_ID' => $arMatch[2],
			];
		}
		return false;
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		$arIBlockParams = $this->getIBlockParams($arElement['IBLOCK_ID']);
		# Correct int/string types for height, depth, price, vat, ...
		$this->correctFieldTypes($arItem, $arElement, $arFields, $arElementSections, $arDataMore);
		#
		$arDataMore = ['STOCKS' => []];
		# Transfer stock from main data to DATA_MORE
		$arTransferParams = [ # Param => (whether to preserve param in $arFields)
			'offer_id' => true,
			'stock' => false,
			#'warehouse_id' => false,
		];
		foreach($arTransferParams as $strParam => $bPreserve){
			if(array_key_exists($strParam, $arFields)){
				$arDataMore['STOCKS'][$strParam] = $arFields[$strParam];
				if(!$bPreserve){
					unset($arFields[$strParam]);
					unset($arItem[$strParam]);
				}
			}
		}
		# Check consider reserved stock
		if($arIBlockParams['OZON_CONSIDER_RESERVED_STOCK'] == 'Y'){
			$arProductInfo = $this->getOzonProductInfo($arFields['offer_id']);
			if($arProductInfo['stocks']){
				$arDataMore['STOCKS']['stock_reserved'] = $arProductInfo['stocks']['reserved'];
			}
		}
		# Detect category id
		$intProductCategoryId = false;
		if($arIBlockParams['CATEGORIES_ALTERNATIVE'] == 'Y'){
			$intProductCategoryId = $arFields['category_id'];
		}
		$this->handler('onOzonNewApiGetCategoryId', [&$intProductCategoryId, &$arItem, &$arElement, &$arFields, 
			&$arElementSections]);
		if(!$intProductCategoryId){
			$intProductSectionId = false;
			$intProductSectionId = intVal(reset($arElementSections));
			if(!$intProductSectionId){
				return [
					'ERRORS' => [static::getMessage('ERROR_WRONG_PRODUCT_SECTION', [
						'#ELEMENT_ID#' => $arElement['ID'],
					])],
				];
			}
			$arQuery = [
				'filter' => ['PROFILE_ID' => $this->intProfileId, 'SECTION_ID' => $intProductSectionId],
				'select' => ['SECTION_NAME'],
			];
			if($arRedefinition = Helper::call($this->strModuleId, 'CategoryRedefinition', 'getList', [$arQuery])->fetch()){
				$intProductCategoryId = $this->parseCategoryId($arRedefinition['SECTION_NAME']);
			}
			if(!$intProductCategoryId){
				return [
					'ERRORS' => [static::getMessage('ERROR_WRONG_PRODUCT_CATEGORY', [
						'#ELEMENT_ID#' => $arElement['ID'],
					])],
				];
			}
		}
		$arItem['category_id'] = intVal($intProductCategoryId);
		# Prepare images
		if(is_array($arItem['images'])){
			$arItem['images'] = array_values($arItem['images']);
		}
		# Prepare PDF
		$strSiteUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y');
		$intIndex = 0;
		foreach($arItem['pdf_list'] as $key => $mPdf){
			if(is_numeric($mPdf) && $mPdf > 0){
				if($arFile = \CFile::getFileArray($mPdf)){
					$mPdf = [
						'index' => $intIndex++,
						'name' => strlen($arFile['DESCRIPTION']) ? $arFile['DESCRIPTION'] : 
							pathinfo($arFile['ORIGINAL_NAME'], PATHINFO_FILENAME),
						'src_url' => $strSiteUrl.$arFile['SRC'],
					];
				}
				else{
					unset($mPdf);
				}
			}
			else{
				$mPdf = [
					'index' => $intIndex++,
					'name' => pathinfo($mPdf, PATHINFO_FILENAME),
					'src_url' => $mPdf,
				];
			}
			if(!is_array($mPdf)){
				unset($arItem['pdf_list'][$key]);
				continue;
			}
			$arItem['pdf_list'][$key] = $mPdf;
		}
		# Remove attributes for other categories
		foreach($arFields as $strField => $mValue){
			if($arAttribute = $this->parseAttributeId($strField)){
				if($arAttribute['CATEGORY_ID'] != static::GROUPED_CODE && $arAttribute['CATEGORY_ID'] != $intProductCategoryId){
					unset($arFields[$strField]);
					unset($arItem[$strField]);
				}
			}
		}
		# Check empty required fields (for each category)
		if($arErrors = $this->checkRequiredFields($arElement['IBLOCK_ID'], $arFields, $intProductCategoryId)){
			return [
				'ERRORS' => $arErrors,
			];
		}
		# Transform some fields
		if($arItem['old_price'] == $arItem['price']){
			$arItem['old_price'] = '';
		}
		$arNumericToFloat = ['depth', 'height', 'width', 'weight'];
		foreach($arNumericToFloat as $strField){
			if(is_numeric($arItem[$strField])){
				$arItem[$strField] = floatVal($arItem[$strField]);
			}
			elseif(empty($arItem[$strField])){
				$arItem[$strField] = 0;
			}
		}
		$arUnsetEmptyFields = ['pdf_list', 'image_group_id', 'old_price', 'premium_price'];
		foreach($arUnsetEmptyFields as $strField){
			$arField = $arItem[$strField];
			if(is_string($arField) && !strlen($arField) || is_array($arField) && empty($arField)){
				unset($arItem[$strField]);
			}
		}
		# Transform attributes
		$arAttributes = [];
		foreach($arItem as $strField => $mValue){
			if($arAttribute = $this->parseAttributeId($strField)){
				if(!Helper::isEmpty($mValue)){
					$arValues = [];
					$mValue = is_array($mValue) ? $mValue : [$mValue];
					$intDictionaryId = $this->isAttributeDictionary($arAttribute['CATEGORY_ID'], $arAttribute['ATTRIBUTE_ID']);
					foreach($mValue as $strValue){
						$strValue = strVal($strValue);
						if($intDictionaryId){
							$arValuesFilter = [
								'CATEGORY_ID' => $arAttribute['CATEGORY_ID'],
								'ATTRIBUTE_ID' => $arAttribute['ATTRIBUTE_ID'],
								'VALUE' => $strValue,
							];
							if($this->isAttributeDictionaryCommon($arAttribute['ATTRIBUTE_ID'])){
								unset($arValuesFilter['CATEGORY_ID']);
							}
							$resDictionaryValue = AttributeValue::getList([
								'filter' => $arValuesFilter,
								'select' => ['VALUE_ID']
							]);
							if($arDictionaryValue = $resDictionaryValue->fetch()){
								$arValues[] = [
									'dictionary_value_id' => intVal($arDictionaryValue['VALUE_ID']),
									'value' => $strValue,
								];
							}
							else{
								$strAttributeName = $arAttribute['ATTRIBUTE_ID'];
								$resDbAttribute = Attribute::getList([
									'filter' => [
										'CATEGORY_ID' => $arAttribute['CATEGORY_ID'],
										'ATTRIBUTE_ID' => $arAttribute['ATTRIBUTE_ID'],
									],
									'select' => ['NAME'],
								]);
								if($arDbAttribute = $resDbAttribute->fetch()){
									$strAttributeName = sprintf('[%d] %s', $arAttribute['ATTRIBUTE_ID'], $arDbAttribute['NAME']);
								}
								return [
									'ERRORS' => [static::getMessage('ERROR_WRONG_DICTIONARY_VALUE', [
										'#ELEMENT_ID#' => $arElement['ID'],
										'#VALUE#' => htmlspecialcharsbx($strValue),
										'#ATTRIBUTE#' => $strAttributeName,
									])],
								];
							}
						}
						else{
							$arValues[] = [
								'value' => $strValue,
							];
						}
					}
					$arAttributes[] = [
						'id' => intVal($arAttribute['ATTRIBUTE_ID']),
						'values' => $arValues,
					];
				}
				unset($arItem[$strField]);
			}
		}
		$arItem['attributes'] = $arAttributes;
	}
	
	/**
	 *	Correct value types (for example, "vat": 0 => "vat": "0")
	 */
	protected function correctFieldTypes(&$arItem, $arElement, $arFields, $arElementSections, $arDataMore){
		$arStringFields = ['offer_id', 'price', 'old_price', 'premium_price', 'vat'];
		foreach($arStringFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = strVal($arItem[$strKey]);
			}
		}
		$arFloatFields = ['depth', 'height', 'weight', 'width'];
		foreach($arFloatFields as $strKey){
			if(array_key_exists($strKey, $arItem)){
				$arItem[$strKey] = floatVal($arItem[$strKey]);
			}
		}
	}
	
	/**
	 *	Check empty required fields (for each category)
	 */
	protected function checkRequiredFields($intIBlockId, $arFields){
		$arEmptyRequiredFields = [];
		$arFieldsAll = $this->getFieldsCached($this->intProfileId, $intIBlockId, true);
		foreach($arFields as $strField => $mValue){
			if($arFieldsAll[$strField]){
				$bEmpty = Helper::isEmpty($mValue, $arFieldsAll[$strField]->isSimpleEmptyMode());
				if($bEmpty && $arFieldsAll[$strField]->isCustomRequired()){
					$arAttributeId = static::parseAttributeId($strField);
					if(is_array($arAttributeId)){
						if($this->isAttributeRequired($arAttributeId['CATEGORY_ID'], $arAttributeId['ATTRIBUTE_ID'])){
							if(!is_array($arEmptyRequiredFields[$arAttributeId['CATEGORY_ID']])){
								$arEmptyRequiredFields[$arAttributeId['CATEGORY_ID']] = [];
							}
							$arEmptyRequiredFields[$arAttributeId['CATEGORY_ID']][] = $arFieldsAll[$strField]->getName();
						}
					}
				}
			}
		}
		if(!empty($arEmptyRequiredFields)){
			$arErrors = [];
			foreach($arEmptyRequiredFields as $intCategoryId => $arErrorFields){
				$resCategory = Category::getList(['filter' => ['CATEGORY_ID' => $intCategoryId], 'select' => ['NAME']]);
				if($arCategory = $resCategory->fetch()){
					$arErrors[] = static::getMessage('ERROR_EMPTY_REQUIRED_FIELDS', [
						'#CATEGORY#' => $this->formatCategoryName($intCategoryId, $arCategory['NAME']),
						'#FIELDS#' => implode(', ', $arErrorFields),
					]);
				}
			}
			return $arErrors;
		}
		return false;
	}
	
	/**
	 *	Cancel save json to file
	 */
	protected function onUpJsonExportItem(&$arItem, &$strJson, &$arSession, &$bWrite){
		$bWrite = false;
	}
	
	/**
	 *	Add custom step
	 */
	protected function onUpGetExportSteps(&$arExportSteps, &$arSession){
		$arUnsetSteps = ['EXPORT_HEADER', 'EXPORT_FOOTER', 'REPLACE_FILE', 'REPLACE_TMP_FILES'];
		foreach($arUnsetSteps as $strStep){
			unset($arExportSteps[$strStep]);
		}
	}
	
	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		$mResult = Exporter::RESULT_ERROR;
		if($this->bCron){
			do{
				$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
			}
			while($mResult === Exporter::RESULT_CONTINUE);
		}
		else{
			$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
		}
		return $mResult;
	}
	
	/**
	 *	Export data by API (one step)
	 */
	protected function stepExport_ExportApi_Step(&$arSession, $arStep){
		$arItems = $this->getExportDataItems(null, null, $this->intExportPerStep);
		if(!empty($arItems)){
			$arJsonItems = [];
			$arDataMore = [];
			foreach($arItems as $arItem){
				$arEncodedItem = Json::decode($arItem['DATA']);
				$arJsonItems[$arItem['ELEMENT_ID']] = $arEncodedItem;
				$arDataMore[$arItem['ELEMENT_ID']] = unserialize($arItem['DATA_MORE']);
			}
			$arJsonItemsFull = [
				'items' => array_values($arJsonItems),
			];
			$arItemsId = array_column($arJsonItems, 'offer_id');
			$arResult = $this->API->execute('/v2/product/import', $arJsonItemsFull, ['METHOD' => 'POST']);
			if(is_array($arResult) && $arResult['result']['task_id']){
				$intTaskId = $arResult['result']['task_id'];
				$obDate = new \Bitrix\Main\Type\Datetime();
				$this->addToLog('Task created: '.$intTaskId, true);
				# Save state
				foreach($arItems as $arItem){
					$this->setDataItemExported($arItem['ID']);
					$arSession['INDEX']++;
				}
				# Add task
				$strJsonFullRaw = Json::encode($arJsonItemsFull);
				if(!Helper::isUtf()){
					$strJsonFullRaw = Helper::convertEncoding($strJsonFullRaw, 'UTF-8', 'CP1251');
				}
				$arTask = [
					'MODULE_ID' => $this->strModuleId,
					'PROFILE_ID' => $this->intProfileId,
					'TASK_ID' => $intTaskId,
					'PRODUCTS_COUNT' => count($arItemsId),
					'JSON' => $strJsonFullRaw,
					'SESSION_ID' => session_id(),
					'TIMESTAMP_X' => $obDate,
				];
				Task::add($arTask);
				# Add task items to history
				foreach($arJsonItems as $intElementId => $arItem){
					$strJson = Json::encode($arItem);
					if(!Helper::isUtf()){
						$strJson = Helper::convertEncoding($strJson, 'UTF-8', 'CP1251');
					}
					History::add([
						'MODULE_ID' => $this->strModuleId,
						'PROFILE_ID' => $this->intProfileId,
						'TASK_ID' => $intTaskId,
						'OFFER_ID' => $arItem['offer_id'],
						'ELEMENT_ID' => $intElementId,
						'JSON' => $strJson,
						'SESSION_ID' => session_id(),
						'TIMESTAMP_X' => $obDate,
					]);
				}
				# Continue load: export stocks
				$arStocks = [];
				if(is_array($arDataMore)){
					foreach($arDataMore as $arStock){
						if($arStock['STOCKS'] && strlen($arStock['STOCKS']['stock'])){
							$arStockItem = [
								'offer_id' => strVal($arStock['STOCKS']['offer_id']),
								'stock' => intVal($arStock['STOCKS']['stock']),
							];
							#if(strlen($arStock['STOCKS']['warehouse_id'])){
							#	$arStockItem['warehouse_id'] = intVal($arStock['STOCKS']['warehouse_id']);
							#}
							$arStocks[] = $arStockItem;
							unset($arStockItem);
						}
					}
				}
				$this->addToLog($arDataMore);
				$this->addToLog($arStocks);
				if(!empty($arStocks)){
					$X = $this->sendStocks($intTaskId, $arStocks);
					$this->addToLog($X);
				}
				$this->addToLog('Stock exported');
			}
			else{
				$strError = isset($arResult['error']) ? print_r($arResult['error'], 1) : null;
				if(is_null($strError)){
					if($arResult['result']['task_id'] === 0){
						$strError = static::getMessage('ERROR_EXPORT_ITEMS_BY_API_TASK_0');
					}
				}
				$strLogMessage = static::getMessage('ERROR_EXPORT_ITEMS_BY_API', ['#ERROR#' => $strError]);
				$this->addToLog($strLogMessage);
				$this->addToLog('JSON: '.Json::encode($arJsonItemsFull));
				$this->addToLog('Items: '.implode(', ', $arItemsId), true);
				# Display error
				require __DIR__.'/include/popup_error.php';
				return Exporter::RESULT_ERROR;
			}
			return Exporter::RESULT_CONTINUE;
		}
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 *	Send stocks data via API and save results to table acrit_ozon_history
	 */
	protected function sendStocks($intTaskId, array $arSendStocks){
		$arStocksFull = ['stocks' => $arSendStocks];
		$arStockResult = $this->API->execute('/v1/products/stocks', $arStocksFull, ['METHOD' => 'POST']);
		if(is_array($arStockResult['result']) && !empty($arStockResult['result'])){
			foreach($arStockResult['result'] as $arResultStock){
				if(strlen($arResultStock['offer_id'])){
					$arFilter = [
						'PROFILE_ID' => $this->intProfileId,
						'TASK_ID' => $intTaskId,
						'OFFER_ID' => $arResultStock['offer_id'],
					];
					$arSelect = ['ID'];
					if($arHistoryItem = History::getList(['filter' => $arFilter, 'select' => $arSelect])->fetch()){
						$bUpdated = !!$arResultStock['updated'];
						$arErrors = $arResultStock['errors'];
						if(is_array($arErrors)){
							$arErrors = array_map(function($arItem){
								return sprintf('[%s] %s', $arItem['code'], $arItem['message']);
							}, $arErrors);
						}
						$arHistoryStockFields = [
							'STOCK_VALUE' => 0,
							'STOCK_UPDATED' => $bUpdated ? 'Y' : 'N',
							'STOCK_ERRORS' => implode("\n", $arErrors),
						];
						foreach($arSendStocks as $arSendStock){
							if($arSendStock['offer_id'] == $arResultStock['offer_id']){
								$arHistoryStockFields['STOCK_VALUE'] = $arSendStock['stock'];
								break;
							}
						}
						History::update($arHistoryItem['ID'], $arHistoryStockFields);
					}
				}
			}
		}
		else{
			$this->addToLog('Error send stocks: '.print_r($arStockResult, true));
		}
		return $arStockResult;
	}
	
	/**
	*	Show notices
	*/
	public function showMessages(){
		print Helper::showNote(static::getMessage('NOTICE_SUPPORT'), true);
	}
	
	/**
	 *	Show custom data at tab 'Log'
	 */
	public function getLogContent(&$strLogCustomTitle, $arGet){
		ob_start();
		require __DIR__.'/include/tasks/log.php';
		return ob_get_clean();
	}
	
	/**
	 *	Handle click on button 'update'
	 */
	protected function updateTaskStatus($intTaskId, &$arJsonResult){
		$strResultHtml = '';
		$intTaskId = intVal($intTaskId);
		if($intTaskId){
			if($arTask = $this->updateSingleTaskStatus($intTaskId)){
				$arJsonResult['StatusUpdateDatetime'] = $arTask['STATUS_DATETIME']->toString();
				#$arJsonResult['Task'] = $arTask;
				$strResultHtml = $this->displayTaskStatus($arTask);
			}
		}
		return $strResultHtml;
	}
	
	/**
	 *	Update status for one task
	 *	@return $arTask - array of task
	 */
	protected function updateSingleTaskStatus($intTaskId){
		$mResult = false;
		$intTaskId = intVal($intTaskId);
		if($intTaskId){
			$arJson = $this->API->execute('/v1/product/import/info', ['task_id' => $intTaskId], ['METHOD' => 'POST']);
			$arFilter = ['TASK_ID' => $intTaskId, 'PROFILE_ID' => $this->intProfileId];
			if($arTask = Task::getList(['filter' => $arFilter])->fetch()){
				$arCount = [
					'Status' => [],
					'Count' => $arJson['result']['total'],
				];
				foreach($arJson['result']['items'] as $arItem){
					$strStatus = ucFirst($arItem['status']);
					if(!isset($arCount['Status'][$strStatus])){
						$arCount['Status'][$strStatus] = 0;
					}
					$arCount['Status'][$strStatus]++;
				}
				$strStatusData = serialize($arCount);
				#
				$obDate = new \Bitrix\Main\Type\Datetime();
				$arUpdateFields = [
					'STATUS' => $strStatusData,
					'STATUS_DATETIME' => $obDate,
				];
				$obResult = Task::update($arTask['ID'], $arUpdateFields);
				if($obResult->isSuccess()){
					foreach($arJson['result']['items'] as $arItem){
						$resHistoryItem = History::getList([
							'filter' => [
								'TASK_ID' => $intTaskId,
								'OFFER_ID' => $arItem['offer_id'],
							],
							'select' => ['ID', 'ELEMENT_ID', 'STATUS'],
						]);
						if($arHistoryItem = $resHistoryItem->fetch()){
							History::update($arHistoryItem['ID'], [
								'PRODUCT_ID' => $arItem['product_id'],
								'STATUS' => $arItem['status'],
								'STATUS_DATETIME' => $obDate,
							]);
							$this->checkSetImported($arHistoryItem, $arItem['status'], $arItem['offer_id']);
						}
					}
					$mResult = array_merge($arTask, $arUpdateFields);
				}
			}
		}
		return $mResult;
	}
	
	/**
	 *	Display status for one task
	 */
	protected function displayTaskStatus($arTask){
		$strResultHtml = '';
		$arStatus = unserialize($arTask['STATUS']);
		if(is_array($arStatus)){
			ob_start();
			$strFile = __DIR__.'/include/tasks/status.php';
			Helper::loadMessages($strFile);
			require $strFile;
			$strResultHtml = ob_get_clean();
		}
		return $strResultHtml;
	}
	
	/**
	 *	
	 */
	protected function getAllowedValuesContent($arGet){
		ob_start();
		$strField = $arGet['field'];
		if($arAttribute = $this->parseAttributeId($strField)){
			require __DIR__.'/include/allowed_values/popup.php';
		}
		else{
			print Helper::showError(static::getMessage('ERROR_PARSE_ATTRIBUTE', ['#ATTRIBUTE#' => $strField]));
		}
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function getAllowedValuesFilteredContent($arGet){
		ob_start();
		$strField = $arGet['field'];
		if($arAttribute = $this->parseAttributeId($strField)){
			require __DIR__.'/include/allowed_values/filtered.php';
		}
		return ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function getTaskJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'TASK_ID' => $arGet['task_id'],
			],
			'select' => [
				'JSON',
			],
		];
		return $this->displayPopupJson(Task::getList($arQuery)->fetch(), 'JSON', ['ALLOW_COPY' => true]);
	}
	
	/**
	 *	
	 */
	protected function getHistoryItemJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'ID' => $arGet['history_item_id'],
			],
			'select' => [
				'JSON',
			],
		];
		return $this->displayPopupJson(History::getList($arQuery)->fetch(), 'JSON', ['ALLOW_COPY' => true]);
	}
	
	/**
	 *	
	 */
	protected function displayPopupJson($arArray, $strKey, $arParams=[]){
		if(is_array($arArray) && strlen($strKey)){
			$strFile = __DIR__.'/include/popup_json.php';
			Helper::loadMessages($strFile);
			ob_start();
			$strJson = &$arArray[$strKey];
			require $strFile;
			return ob_get_clean();
		}
		return static::getMessage('ERROR_JSON_NOT_FOUND');
	}
	
	/**
	 *	Modify teachers
	 */
	public function addTeachers(&$arTeachers){
		$arOzonTeacher = $this->getTeacher();
		if(is_array($arOzonTeacher['STEPS'])){
			foreach($arOzonTeacher['STEPS'] as $strStep => $arStep){
				if(is_null($arStep)){
					unset($arOzonTeacher['STEPS'][$strStep]);
				}
			}
		}
		$arTeachers[] = $arOzonTeacher;
	}
	
	/**
	 *	Modify default teacher
	 */
	public function modifyDefaultTeacher(&$arDefaultTeacher){
		$arOzonTeacher = $this->getTeacher();
		if(is_array($arOzonTeacher['STEPS'])){
			foreach($arOzonTeacher['STEPS'] as $strStep => $arStep){
				if(is_null($arStep)){
					unset($arDefaultTeacher['STEPS'][$strStep]);
				}
				elseif(is_array($arStep) && is_array($arDefaultTeacher['STEPS'][$strStep])){
					$arDefaultTeacher['STEPS'][$strStep] = array_merge($arDefaultTeacher['STEPS'][$strStep], $arStep);
					continue;
				}
				else{
					$strAfterKey = $arStep['AFTER'];
					unset($arStep['AFTER']);
					$this->teaacherAddItem($arDefaultTeacher['STEPS'], $strStep, $arStep, $strAfterKey);
				}
			}
		}
	}
	
	/**
	 *	Get teacher array fron include/teacher.php
	 *	This will be used in $this->addTeachers() and $this->modifyDefaultTeacher();
	 */
	protected function getTeacher(){
		return require __DIR__.'/include/teacher.php';
	}
	
	/**
	 *	Show options on general subtab (bottom)
	 */
	public function addHtmlOptionsGeneralBottom($intIBlockID){
		$strCode = static::getCode();
		$arIBlockParams = $this->getIBlockParams($intIBlockID);
		?>
		<tr class="heading"><td colspan="2"><?=static::getMessage('GENERAL_SETTINGS_HEADER_STOCK');?></td></tr>
		<tr>
			<td width="40%">
				<?=Helper::showHint(static::getMessage('GENERAL_SETTINGS_HINT_CONSIDER_RESERVED_STOCK'));?>
				<?=static::getMessage('GENERAL_SETTINGS_CONSIDER_RESERVED_STOCK');?>:
			</td>
			<td width="60%">
				<div>
					<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][OZON_CONSIDER_RESERVED_STOCK]" value="N" />
					<input type="checkbox" name="iblockparams[<?=$intIBlockID;?>][OZON_CONSIDER_RESERVED_STOCK]" value="Y" 
						data-role="ozon_consider_reserved_stock"
						<?if($arIBlockParams['OZON_CONSIDER_RESERVED_STOCK']=='Y'):?>checked="checked"<?endif?>
						id="checkbox_OZON_CONSIDER_RESERVED_STOCK"
					/>
				</div>
			</td>
		</tr>
		<?
	}

	/**
	 *	Get product data from Ozon by API
	 */
	public function getOzonProductInfo($strOfferId){
		$arQuery = [
			'offer_id' => $strOfferId,
		];
		$arResult = $this->API->execute('/v2/product/info', $arQuery, ['METHOD' => 'POST']);
		return is_array($arResult['result']) ? $arResult['result'] : [];
	}
	
	/**
	 *	Check to set imported for item
	 *	$arHistoryItem contains ID, ELEMENT_ID, STATUS
	 */
	protected function checkSetImported(array $arHistoryItem, $strNewStatus, $strOfferId){
		#$strOldStatus = $arHistoryItem['STATUS'];
		# ToDo
	}

}

?>