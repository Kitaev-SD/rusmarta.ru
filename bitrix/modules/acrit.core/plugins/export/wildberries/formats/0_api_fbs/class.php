<?
/**
 * Acrit Core: wildberries
 * @documentation https://suppliers.wildberries.ru/remote-wh-site/api-content.html
 */

namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Json,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Export\UniversalPlugin,
	\Acrit\Core\Export\ExternalIdTable as ExternalId,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\Api,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\ImageTable as Image,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\CategoryTable as Category,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\AttributeTable as Attribute,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\TaskTable as Task,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\HistoryTable as History,
	\Acrit\Core\Export\Plugins\WildberriesHelpers\HistoryStockTable as HistoryStock;
	

class WildberriesV2 extends UniversalPlugin {
	
	const DATE_UPDATED = '2021-04-09';
	const ATTRIBUTE_ID = 'attribute_%s';

	const ADDIN_TYPE_CARD = 'C';
	const ADDIN_TYPE_VARIATION = 'V';
	const ADDIN_TYPE_NOMENCLATURE = 'N';

	const TMP_UUID_PREFIX = 'tmp_uuid___';
	const TMP_NOMENCLATURE_ADDIN = '_nomenclatures_addin';
	const TMP_PRODUCT_VARIATION = '_variation';

	protected static $bSubclass = true;
	
	# Basic settings
	protected $bOffersPreprocess = true;
	protected $arSupportedFormats = ['JSON']; // Формат выгрузки - JSON
	protected $bApi = true; // Выгружаем не в файл, а по АПИ
	protected $bCategoriesExport = false; // Из-за особенностей WB запрещаем обычную работу с категориями
	protected $bCategoriesList = false; // В плагине доступен список категорий, необходимо для работы со списком категорий
	protected $bCategoriesUpdate = false; // Разрешаем обновлять категории
	protected $bCategoriesStrict = false; // На озоне важно указывать только «озоновские» категории
	protected $bCategoryCustomName = false; // Добавляем возможность использовать значение «Использовать поля товаров» в опции «Источник названий категорий»
	protected $arSupportedEncoding = [self::UTF8];
	protected $intExportPerStep = 50; // 50 товаров за 1 шаг
	
	# API class
	protected $API;
	
	# Cache
	protected $arCacheRequiredAttributes = [];
	protected $arCacheDictionaryAttributes = [];
	
	# Misc
	protected $strTokenTmp = null;
	protected $arParsedAttributes = [];
	protected $bOriginalProcessElement = false;
	protected $bOriginalProcessOffers = false;
	protected $arDataMoreTmp = [];
	protected $bSkipExportImages = false;
	
	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileId, $intIBlockId){
		$bOffer = Helper::isOffersIBlock($intIBlockId);
		# Add common attributes
		$arResult['HEADER_GENERAL'] = [];
		$arResult['object'] = ['CONST' => '', 'REQUIRED' => true];
		if(!$bOffer || $this->isOffersNewMode()){
			$arResult['vendorCode'] = ['FIELD' => 'PROPERTY_ARTNUMBER', 'REQUIRED' => true];
			$arResult['countryProduction'] = ['FIELD' => $bOffer ? 'PARENT.PROPERTY_COUNTRY' : 'PROPERTY_COUNTRY', 
				'REQUIRED' => true, 'ALLOWED_VALUES_CUSTOM' => true];
		}
		$arResult['barcode'] = ['FIELD' => 'CATALOG_BARCODE'];
		if($this->arParams['EXPORT_STOCKS'] == 'Y'){
			foreach($this->getStores(false) as $intStoreId => $strStoreName){
				$arResult['stock_'.$intStoreId] = [
					'FIELD' => 'CATALOG_QUANTITY',
					'NAME' => static::getMessage('FIELD_STOCK', [
						'#STORE_ID#' => $intStoreId,
						'#STORE_NAME#' => $strStoreName,
					]),
					'DESCRIPTION' => static::getMessage('FIELD_STOCK_DESCRIPTION'),
				];
			}
		}
		# Add special attributes (depends on category)
		$arData = $this->getDataForFields($intIBlockId);
		if(is_array($arData)){
			foreach($arData as $strCategoryName => $arAttributes){
				$arResult['HEADER_CATEGORY_'.md5($strCategoryName)] = [
					'NAME' => $strCategoryName,
					'NORMAL_CASE' => true,
				];
				foreach($arAttributes as $arAttribute){
					$strAttributeId = sprintf(static::ATTRIBUTE_ID, $arAttribute['HASH']);
					$arField = [
						'NAME' => $arAttribute['NAME'],
						'NAME_SUFFIX' => $arAttribute['NAME_SUFFIX'],
						'DISPLAY_CODE' => $strAttributeId,
						'REQUIRED' => count($arData) == 1 && $arAttribute['IS_REQUIRED'] == 'Y',
						'CUSTOM_REQUIRED' => $arAttribute['IS_REQUIRED'] == 'Y',
						'PARAMS' => [],
					];
					if($arAttribute['MAX_COUNT']){
						$arField['MULTIPLE'] = true;
						$arField['MAX_COUNT'] = $arAttribute['MAX_COUNT'];
						$arField['PARAMS']['MULTIPLE'] = 'multiple';
					}
					if($arAttribute['DICTIONARY']){
						$arField['ALLOWED_VALUES_CUSTOM'] = true;
					}
					$this->guessDefaultValue($arField, $arAttribute, $intIBlockId);
					$this->addDescriptions($arField, $arAttribute, $intIBlockId);
					$arResult[$strAttributeId] = $arField;
				}
			}
		}
		#
		return $arResult;
	}

	/**
	 *	Is it need to offers preprocess? (see plugin 'sorokonogka')
	 */
	public function isOffersPreprocess(){
		return !$this->isOffersNewMode();
	}
	
	/**
	 *	Check plugin use new mode for offers (each offer as a product)
	 */
	public function isOffersNewMode(){
		return $this->arParams['OFFERS_NEW_MODE'] == 'Y';
	}

	/**
	 * Get stores from profile params
	 */
	protected function getStores($bAddEmpty=false){
		$arStocks = $this->arParams['STOCKS'];
		if(!is_array($arStocks)){
			$arStocks = [];
		}
		if(!is_array($arStocks['ID'])){
			$arStocks['ID'] = [];
		}
		if(!is_array($arStocks['NAME'])){
			$arStocks['NAME'] = [];
		}
		$arStocks = array_combine($arStocks['ID'], $arStocks['NAME']);
		foreach($arStocks as $intStoreId => $strStoreName){
			if(!is_numeric($intStoreId) || $intStoreId <= 0){
				unset($arStocks[$intStoreId]);
			}
		}
		if($bAddEmpty && empty($arStocks)){
			$arStocks[''] = '';
		}
		return $arStocks;
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
		$bOffer = is_array($arCatalog) && $arCatalog['PRODUCT_IBLOCK_ID'];
		$intMainIBlockId = $bOffer ? $arCatalog['PRODUCT_IBLOCK_ID'] : $intIBlockId;
		$arUsedCategories = $this->getUsedCategories($intMainIBlockId);
		if(!empty($arUsedCategories)){
			$arUsedCategories = array_values($arUsedCategories);
			$arSort = ['SORT' => 'ASC', 'TYPE' => 'ASC', 'NAME' => 'ASC'];
			$arFilter = ['=CATEGORY_NAME' => $arUsedCategories];
			$arSelect = ['*'];
			if($bOffer && !$this->isOffersNewMode()){
				$arFilter['TYPE'] = static::ADDIN_TYPE_VARIATION;
			}
			// else{
			// 	$arFilter['TYPE'] = [static::ADDIN_TYPE_CARD, static::ADDIN_TYPE_NOMENCLATURE];
			// }
			$resAttributes = Attribute::getList([
				'order' => $arSort,
				'filter' => $arFilter,
				'select' => $arSelect,
			]);
			while($arAttribute = $resAttributes->fetch()){
				if(strlen($arAttribute['UNIT'])){
					$arAttribute['NAME_SUFFIX'] .= ', '.$arAttribute['UNIT'];
				}
				if($arAttribute['USE_ONLY_DICTIONARY_VALUES'] == 'Y'){
					$arAttribute['NAME_SUFFIX'] .= static::getMessage('REF');
				}
				// if($arAttribute['TYPE'] == static::ADDIN_TYPE_NOMENCLATURE){
				// 	$arAttribute['NAME_SUFFIX'] .= static::getMessage('FIELD_FOR_NOMENCLATURE');
				// }
				if($arAttribute['TYPE'] == static::ADDIN_TYPE_VARIATION){
					if(!$bOffer || $bOffer && $this->isOffersNewMode()){
						$arAttribute['CUSTOM_REQUIRED'] = $arAttribute['IS_REQUIRED'] == 'Y' ? 'Y' : 'N';
						$arAttribute['IS_REQUIRED'] = 'N';
						$arAttribute['NAME'] .= static::getMessage('FOR_VARIATION');
					}
				}
				$arResult[$arAttribute['CATEGORY_NAME']][] = $arAttribute;
			}
		}
		return $arResult;
	}

	/**
	 * Include js with jquery.inputMask
	 */
	public function includeJs(){
		return '<script src="/bitrix/js/'.ACRIT_CORE.'/jquery.inputmask5/dist/jquery.inputmask.min.js"></script>'
			.PHP_EOL.parent::includeJs();
	}
	
	/**
	 *	Try to guess default value
	 */
	protected function guessDefaultValue(&$arField, $arAttribute, $intIBlockId){
		$bOffer = Helper::isOffersIBlock($intIBlockId);
		switch($arAttribute['NAME']){
			case static::getMessage('GUESS_BRAND'):
				$arField['FIELD'] = $bOffer ? ['PARENT.PROPERTY_BRAND', 'PARENT.PROPERTY_BRAND_FER'] 
				: ['PROPERTY_BRAND', 'PROPERTY_BRAND_REF'];
				break;
			case static::getMessage('GUESS_DESCRIPTION'):
				$arField['FIELD'] = ['DETAIL_TEXT'];
				$arField['PARAMS'] = ['MAXLENGTH' => 'Y', 'MAXLENGTH_value' => '1000', 
					'HTMLSPECIALCHARS' => 'skip', 'REPLACE' => [
						'from' => [' '],
						'to' => [' '],
						'use_regexp' => [],
						'modifier' => [],
						'case_sensitive' => []
					]];
				$arField['FIELD_PARAMS'] = ['HTMLSPECIALCHARS' => 'skip'];
				break;
			case static::getMessage('GUESS_TNVED'):
				$arField['FIELD'] = $bOffer ? ['PARENT.PROPERTY_TNVED'] : ['PROPERTY_TNVED'];
				break;
			case static::getMessage('CUSTOM_ATTR_PHOTO'):
				$arField['FIELD'] = ['DETAIL_PICTURE', 'PROPERTY_MORE_PHOTO', 'PROPERTY_PHOTOS'];
				$arField['PARAMS'] = ['MULTIPLE' => 'multiple'];
				$arField['FIELD_PARAMS'] = ['MULTIPLE' => 'multiple'];
				break;
		}
	}
	
	/**
	 *	Add descriptions
	 */
	protected function addDescriptions(&$arField, $arAttribute, $intIBlockId){
		$strHint = 'DESC_';
		switch($arAttribute['NAME']){
			case static::getMessage('CUSTOM_ATTR_PHOTO'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_PHOTO');
				break;
			case static::getMessage('CUSTOM_ATTR_KEYWORDS'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_KEYWORDS');
				break;
			case static::getMessage('CUSTOM_ATTR_DESCRIPTION'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_DESCRIPTION');
				break;
			case static::getMessage('CUSTOM_ATTR_INGREDIENTS'):
				$arField['DESCRIPTION'] .= static::getMessage($strHint.'CUSTOM_ATTR_INGREDIENTS');
				break;
		}
	}
	
	/**
	 *	Include own classes and files
	 */
	public function includeClasses(){
		Helper::includeJsPopupHint();
		require_once __DIR__.'/include/classes/api.php';
		require_once __DIR__.'/include/classes/attribute.php';
		require_once __DIR__.'/include/classes/category.php';
		require_once __DIR__.'/include/classes/image.php';
		require_once __DIR__.'/include/classes/task.php';
		require_once __DIR__.'/include/classes/history.php';
		require_once __DIR__.'/include/classes/historystock.php';
		require_once __DIR__.'/include/db_table_create.php';
	}
	
	/**
	 *	Handler for setProfileArray
	 */
	protected function onSetProfileArray(){
		if(!$this->API){
			$this->API = new Api($this->intProfileId, $this->strModuleId);
		}
	}
	
	/**
	 *	Custom block in subtab 'Categories'
	 */
	public function categoriesCustomActions($intIBlockID, $arIBlockParams){
		return $this->includeHtml(__DIR__.'/include/categories/settings.php', [
			'IBLOCK_ID' => $intIBlockID,
			'IBLOCK_PARAMS' => $arIBlockParams,
		]);
	}
	
	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['SUPPLIER_ID'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/supplier_id.php'),
			'SORT' => 100,
		];
		$arSettings['AUTHORIZATION'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/authorization.php'),
			'SORT' => 130,
		];
		$arSettings['OFFERS_NEW_MODE'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/offers_new_mode.php'),
			'SORT' => 150,
		];
		$arSettings['EXPORT_STOCKS'] = [
			'HTML' => $this->includeHtml(__DIR__.'/include/settings/export_stocks.php'),
			'SORT' => 160,
		];
	}
	
	/**
	 *	Handle custom ajax
	 */
	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch($strAction){
			case 'login':
				$this->login($arParams, $arJsonResult);
				break;
			case 'login_confirm':
				$this->loginConfirm($arParams, $arJsonResult);
				break;
			case 'token_check':
				$this->tokenCheck($arParams, $arJsonResult);
				break;
			case 'token_clear':
				$this->tokenClear($arParams, $arJsonResult);
				break;
			case 'popup_categories_add':
				$this->displayPopupCategoryAdd($arParams, $arJsonResult);
				break;
			case 'category_attributes_update':
				$this->ajaxUpdateAttributes($arParams, $arJsonResult);
				break;
			case 'allowed_values_custom':
				$this->getAllowedValuesContent($arParams, $arJsonResult);
				break;
			// case 'stock_token_check':
			// 	$this->stockTokenCheck($arParams, $arJsonResult);
			// 	break;
			case 'refresh_tasks_list':
				$arJsonResult['HTML'] = $this->getLogContent($strLogCustomTitle=false, $arParams['GET']);
				break;
			case 'history_item_json_preview':
				$arJsonResult['HTML'] = $this->getHistoryItemJsonPreview($arParams['GET']);
				break;
			case 'task_stocks_json_preview':
				$arJsonResult['HTML'] = $this->getTaskStocksJsonPreview($arParams['GET']);
				break;
		}
	}

	/**
	 * Check token is actual
	 */
	public function tokenCheck($arParams, &$arJsonResult){
		$strSupplierId = $arParams['GET']['supplier_id'];
		$strAuthToken = $arParams['GET']['auth_token'];
		# Execute content API method
		$arJsonRequest = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'supplierID' => $strSupplierId,
				'filter' => [
					'order' => ['column' => 'createdAt', 'order' => 'desc'],
				],
				'query' => [
					'limit' => 1,
					'offset' => 0,
				],
			],
		];
		$arQueryResult = $this->API->execute('/card/list', $arJsonRequest, [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken($strAuthToken),
			],
		]);
		$arJsonResult['Request'] = $arJsonRequest;
		$arJsonResult['Response'] = $arQueryResult;
		$arJsonResult['Success'] = is_array($arQueryResult['result']);
	}

	/**
	 * Check stock token is actual
	 */
	// public function stockTokenCheck($arParams, &$arJsonResult){
	// 	$strStockToken = $arParams['GET']['stock_token'];
	// 	# Execute content API method
	// 	$intFakeId = 1234567890000;
	// 	$arJsonRequest = [
	// 		'token' => $strStockToken,
	// 		'data' => [
	// 			[
	// 				'nmId' => $intFakeId,
	// 				'stocks' => [
	// 					[
	// 						'chrtId' => $intFakeId,
	// 						'price' => 0,
	// 						'quantity' => 0,
	// 						'storeId' => 0,
	// 					],
	// 				],
	// 			]
	// 		],
	// 	];
	// 	$arParams = [
	// 		'METHOD' => 'POST',
	// 		'HEADER' => [
	// 			'Content-Type' => 'application/json',
	// 		],
	// 		'CONTENT' => Json::encode($arJson),
	// 		'TIMEOUT' => 30,
	// 		'HOST' => 'https://wbxgate.wildberries.ru',
	// 	];
	// 	$strUrl = '/stocks';
	// 	$strResponse = $this->API->execute($strUrl, $arJsonRequest, $arParams);
	// 	$arJsonResult['Code'] = $this->API->getResponseCode();
	// 	$arJsonResult['Request'] = $arJsonRequest;
	// 	$arJsonResult['Response'] = $strResponse;
	// 	$arJsonResult['Success'] = !!$strResponse['success'];
	// }

	/**
	 * Do refresh cookie token
	 */
	protected function getToken($strRefreshToken=null){
		if(is_null($this->strTokenTmp)){
			$bCheckMode = !is_null($strRefreshToken);
			$arJsonRequest = [
				'token' => $bCheckMode ? $strRefreshToken : $this->getRefreshToken(),
			];
			$arQueryResult = $this->API->execute('/passport/api/v2/auth/login', $arJsonRequest, [
				'METHOD' => 'POST',
				'SKIP_ERRORS' => true,
			]);
			$strCookieToken = $this->API->getCookieWbToken();
			if(!$bCheckMode){
				$this->setCookieToken($strCookieToken);
			}
			$this->strTokenTmp = $strCookieToken;
		}
		return $this->strTokenTmp;
	}

	/**
	 * Get auth token from profile settings
	 */
	protected function getAuthToken($strAuthToken=null){
		return strlen($strAuthToken) ? $strAuthToken : $this->arParams['AUTH_TOKEN'];
	}

	/**
	 * Get supplierID from profile settings
	 */
	protected function getSupplierId(){
		return $this->arParams['SUPPLIER_ID'];
	}

	/**
	 * Clear token
	 */
	public function tokenClear($arParams, &$arJsonResult){
		Helper::deleteOption($this->strModuleId, $this->getCookieTokenName());
		Helper::deleteOption($this->strModuleId, $this->getRefreshTokenName());
		$arJsonResult['Success'] = true;
	}

	/**
	 * Get param name for store refresh token
	 */
	public function getRefreshTokenName(){
		return sprintf('token_refresh_%s_%s', $this->getCode(), $this->intProfileId);
	}

	/**
	 * Get param name for store common refresh token
	 */
	public function getRefreshTokenNameCommon(){
		return sprintf('token_refresh_%s_common', $this->getCode());
	}

	/**
	 * Get refresh token for current profile
	 */
	public function getRefreshToken(){
		$tokenCommon = Helper::getOption($this->strModuleId, $this->getRefreshTokenNameCommon());
		$tokenProfile = Helper::getOption($this->strModuleId, $this->getRefreshTokenName());
		if(strlen($tokenProfile)){
			return $tokenProfile;
		}
		return $tokenCommon;
	}

	/**
	 * Saverefresh  token for current profile
	 */
	public function setRefreshToken($strToken){
		Helper::deleteOption($this->strModuleId, $this->getCookieTokenName());
		if($this->intProfileId){
			return Helper::setOption($this->strModuleId, $this->getRefreshTokenName(), $strToken);
		}
		return Helper::setOption($this->strModuleId, $this->getRefreshTokenNameCommon(), $strToken);
	}

	/**
	 * Get param name for store token
	 */
	public function getCookieTokenName(){
		return sprintf('token_cookie_%s_%s', $this->getCode(), $this->intProfileId);
	}

	/**
	 * Get token for current profile
	 */
	public function getCookieToken(){
		return Helper::getOption($this->strModuleId, $this->getCookieTokenName());
	}

	/**
	 * Save token for current profile
	 */
	public function setCookieToken($strToken){
		return Helper::setOption($this->strModuleId, $this->getCookieTokenName(), $strToken);
	}
	
	/**
	 *	Update category attritbutes and dictionaries
	 */
	protected function ajaxUpdateAttributes($arParams, &$arJsonResult){
		$arSession = &$_SESSION['ACRIT_WB_CAT_ATTR_UPDATE'];
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
				'CATEGORY_NAME' => false,
				'ATTRIBUTE_ID' => false,
				'ATTRIBUTE_NAME' => false,
				'SUB_INDEX' => 0,
			];
			$arSession['COUNT'] = count($arSession['CATEGORIES']);
			$arJsonResult['Continue'] = true;
			if($arSession['FORCED']){
				foreach($arSession['CATEGORIES'] as $strCategoryName){
					Attribute::deleteByFilter(['CATEGORY_NAME' => $strCategoryName]);
				}
			}
		}
		else{
			$arJsonResult['Action'] = 'Categories';
			foreach($arSession['CATEGORIES'] as $key1 => $strCategoryName){
				$arSession['ATTRIBUTE_NAME'] = false;
				$this->updateCategoryAttrubutes($strCategoryName, $arSession['ID']);
				$arSession['INDEX']++;
				$arSession['SUB_INDEX'] = 0;
				$arSession['CATEGORY_NAME'] = $strCategoryName;
				unset($arSession['CATEGORIES'][$key1]);
			}
			#
			$arJsonResult['Continue'] = true;
			if(empty($arSession['CATEGORIES'])){
				$arSession['FINISHED'] = true;
				$arJsonResult['Continue'] = false;
			}
		}
		$arJsonResult['SessionId'] = $arSession['ID'];
		$arJsonResult['Count'] = $arSession['COUNT'];
		$arJsonResult['Index'] = $arSession['INDEX'];
		$arJsonResult['Percent'] = $arSession['COUNT'] == 0 ? 0 : round($arSession['INDEX'] * 100 / $arSession['COUNT']);
		$arJsonResult['CategoryName'] = $arSession['CATEGORY_NAME'];
		ob_start();
		require __DIR__.'/include/categories/status.php';
		$arJsonResult['Html'] = ob_get_clean();
	}
	
	/**
	 *	Get used WB categories from redefinitions
	 */
	protected function getUsedCategories($intIBlockId=null, $bJustIds=false){
		$arResult = [];
		$arIBlockParams = $this->arProfile['IBLOCKS'][$intIBlockId]['PARAMS'];
		if(is_array($arIBlockParams['CATEGORIES_LIST'])){
			foreach($arIBlockParams['CATEGORIES_LIST'] as $strCategoryName){
				if(Helper::strlen($strCategoryName)){
					$arResult[$strCategoryName] = $strCategoryName;
				}
			}
		}
		if($bJustIds){
			$arResult = array_keys($arResult);
		}
		return $arResult;
	}
	
	/**
	 *	Update attributes for single category
	 */
	protected function updateCategoryAttrubutes($strCategoryName, $strSessionId){
		$arResult = [];
		$strCategoryNameUrl = $strCategoryName;
		if(!Helper::isUtf()){
			$strCategoryNameUrl = Helper::convertEncoding($strCategoryNameUrl, 'CP1251', 'UTF-8');
		}
		$strUrl = '/ns/characteristics-configurator-api/content-configurator/api/v1/config/get/object/translated';
		$strUrl .= '?'.http_build_query([
			'name' => $strCategoryNameUrl,
			'lang' => 'ru',
		]);
		$arQueryResult = $this->API->execute($strUrl, null, [
			'METHOD' => 'GET',
			'SKIP_ERRORS' => true,
			'HOST' => 'https://content-suppliers.wildberries.ru'
		]);
		if($arQueryResult['data']){
			$arSources = [
				static::ADDIN_TYPE_CARD => [
					'data' => $arQueryResult['data']['addin'],
				],
				static::ADDIN_TYPE_VARIATION => [
					'data' => $arQueryResult['data']['nomenclature']['variation']['addin'],
				],
				static::ADDIN_TYPE_NOMENCLATURE => [
					'data' => $arQueryResult['data']['nomenclature']['addin'],
				],
			];
			$this->correctAttributes($arSources);
			foreach($arSources as $strSourceType => $arSource){
				if($arSource['data']){
					foreach($arSource['data'] as $arItem){
						$arFields = [
							'CATEGORY_NAME' => $strCategoryName,
							'HASH' => $this->getAttributeHash($strCategoryName, $arItem['type']),
							'NAME' => $arItem['type'],
							'TYPE' => $strSourceType,
							'SORT' => is_numeric($arItem['sort']) && $arItem['sort'] > 0 ? $arItem['sort'] : 100,
							'USE_ONLY_DICTIONARY_VALUES' => $arItem['useOnlyDictionaryValues'] == 1 ? 'Y' : 'N',
							'MAX_COUNT' => $arItem['maxCount'],
							'IS_REQUIRED' => $arItem['required'] == 1 ? 'Y' : 'N',
							'IS_AVAILABLE' => $arItem['isAvailable'] == 1 ? 'Y' : 'N',
							'IS_NUMBER' => $arItem['isNumber'] == 1 ? 'Y' : 'N',
							'UNIT' => is_array($arItem['units']) ? reset($arItem['units']) : null,
							'UNITS' => implode(',', $arItem['units']),
							'DICTIONARY' => $arItem['dictionary'],
							'SESSION_ID' => $strSessionId,
							'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
						];
						$arFilter = [
							'HASH' => $arFields['HASH'],
						];
						$arSelect = [
							'ID',
						];
						$resItem = Attribute::getList(['filter' => $arFilter, 'select' => $arSelect]);
						if($arDbItem = $resItem->fetch()){
							Attribute::update($arDbItem['ID'], $arFields);
						}
						else{
							Attribute::add($arFields);
						}
						$arResult[] = $arFields;
					}
				}
			}
			Attribute::deleteByFilter([
				'=CATEGORY_NAME' => $strCategoryName,
				'!SESSION_ID' => $strSessionId,
			]);
		}
		return $arResult;
	}

	/**
	 * Correct attributes on import from WB
	 */
	protected function correctAttributes(&$arSources){
		# Add retail price (for variations addin)
		if(is_array($arSources[static::ADDIN_TYPE_VARIATION]['data'])){
			$arPriceAttribute = [
				'isAvailable' => 1,
				'required' => 1,
				'isNumber' => 1,
				'useOnlyDictionaryValues' => 0,
				'type' => static::getMessage('CUSTOM_ATTR_PRICE'),
				'units' => [static::getMessage('CUSTOM_ATTR_PRICE_UNIT')],
				'sort' => 10,
			];
			array_unshift($arSources[static::ADDIN_TYPE_VARIATION]['data'], $arPriceAttribute);
		}
		# Add photo
		if(is_array($arSources[static::ADDIN_TYPE_NOMENCLATURE]['data'])){
			$arPhotoAttribute = [
				'isAvailable' => 1,
				'required' => 0,
				'useOnlyDictionaryValues' => 0,
				'maxCount' => 10,
				'type' => static::getMessage('CUSTOM_ATTR_PHOTO'),
			];
			array_unshift($arSources[static::ADDIN_TYPE_NOMENCLATURE]['data'], $arPhotoAttribute);
		}
		# Additional images: set multiple
		if(is_array($arSources[static::ADDIN_TYPE_NOMENCLATURE]['data'])){
			foreach($arSources[static::ADDIN_TYPE_NOMENCLATURE]['data'] as $key => &$arItem){
				if($arItem['type'] == static::getMessage('CUSTOM_ATTR_ADDITIONAL_COLORS')){
					$arItem['maxCount'] = 10;
				}
			}
			unset($arItem);
		}
		# Add keywords
		if(is_array($arSources[static::ADDIN_TYPE_CARD]['data'])){
			$arKeywordsAttribute = [
				'isAvailable' => 1,
				'required' => 0,
				'useOnlyDictionaryValues' => 0,
				'maxCount' => 16,
				'type' => static::getMessage('CUSTOM_ATTR_KEYWORDS'),
			];
			array_unshift($arSources[static::ADDIN_TYPE_CARD]['data'], $arKeywordsAttribute);
		}
	}
	
	/**
	 *	Get hash for attribute
	 */
	protected function getAttributeHash($strCategoryName, $strAttributeName){
		return Helper::substr(md5(implode('|', [$strCategoryName, $strAttributeName])), 0, 16);
	}
	
	/**
	 *	Check attribute required (used in processElement)
	 */
	protected function isAttributeRequired($strCategoryName, $strAttributeName){
		if(!is_array($this->arCacheRequiredAttributes[$strCategoryName])){
			$this->arCacheRequiredAttributes[$strCategoryName] = [];
			$resQuery = Attribute::getList([
				'filter' => ['CATEGORY_NAME' => $strCategoryName, 'IS_REQUIRED' => 'Y'],
				'select' => ['NAME'],
			]);
			while($arItem = $resQuery->fetch()){
				$this->arCacheRequiredAttributes[$strCategoryName][$arItem['NAME']] = true;
			}
		}
		return isset($this->arCacheRequiredAttributes[$strCategoryName][$strAttributeName]);
	}
	
	/**
	 *	Parse attribute id: attribute_3aa264514530447a
	 */
	protected function parseAttribute($strAttributeId){
		$strPattern = static::ATTRIBUTE_ID;
		$strPattern = str_replace('%s', '([A-z0-9]+)', $strPattern);
		$strPattern = sprintf('#^%s$#', $strPattern);
		if(preg_match($strPattern, $strAttributeId, $arMatch)){
			$strAttributeHash = $arMatch[1];
			if(!array_key_exists($strAttributeHash, $this->arParsedAttributes)){
				$arQuery = [
					'filter' => ['HASH' => $strAttributeHash],
				];
				if($arAttribute = Attribute::getList($arQuery)->fetch()){
					unset($arAttribute['TIMESTAMP_X']);
					$arAttribute['ATTRIBUTE_ID'] = $strAttributeId;
					$this->arParsedAttributes[$strAttributeHash] = $arAttribute;
				}
			}
			if(array_key_exists($strAttributeHash, $this->arParsedAttributes)){
				return $this->arParsedAttributes[$strAttributeHash];
			}
		}
		return false;
	}
	
	/**
	 *	Handler on generate json for single product
	 */
	protected function onUpBuildJson(&$arItem, &$arElement, &$arFields, &$arElementSections, &$arDataMore){
		$arResult = $arItem;
		$bOffer = Helper::isOffersIBlock($arElement['IBLOCK_ID']);
		if($bOffer && isset($arResult['_OFFER_PREPROCESS'])){
			unset($arResult['_OFFER_PREPROCESS']);
		}
		# Detect category name
		$strCategoryName = trim($arFields['object']);
		$this->handler('onWbFbsGetCategoryName', [&$strCategoryName, &$arResult, &$arElement, &$arFields, 
			&$arElementSections]);
		if(!$bOffer){
			if(!$strCategoryName){
				return [
					'ERRORS' => [static::getMessage('ERROR_EMPTY_PRODUCT_CATEGORY', [
						'#ELEMENT_ID#' => $arElement['ID'],
					])],
				];
			}
			$arResult['object'] = $strCategoryName;
		}
		# Check empty required fields (for each category)
		if(!$bOffer){
			if($arErrors = $this->checkRequiredFields($arElement['IBLOCK_ID'], $arFields, $strCategoryName)){
				return [
					'ERRORS' => $arErrors,
				];
			}
		}
		# Add tmp field _id both for product and offers
		$this->addTmpIds($arResult, $arElement);
		# Process attributes
		$this->processItemAttributes($arResult, $arElement, $arDataMore, $strCategoryName, $bOffer);
		# Process nomenclature
		if($this->isOffersNewMode()){
			$this->processNomenclature_NewMode($arResult, $bOffer);
		}
		else{
			$this->processNomenclature_Default($arResult, $bOffer);
		}
		# Collect externals_id
		$this->collectResultIdentifiersBefore($arResult, $arElement, $arDataMore, $bOffer);
		# Prepare stocks
		$this->moveStocksFromJsonToMoreData($arResult, $arDataMore, $bOffer);
		# Clear tmp fields _id
		$this->clearTmpIds($arResult, $bOffer);
		# Replace $arItem
		$arItem = $arResult;
	}

	/**
	 * Move all fields stock_*** to $arMoreData
	 */
	protected function moveStocksFromJsonToMoreData(&$arItem, &$arDataMore, $bOffer){
		if(!$bOffer || $this->isOffersNewMode()){
			# Move stocks from card to variations, stocks are available only in variations
			foreach($arItem as $key => $value){
				if(preg_match('#^stock_(\d+)$#', $key, $arMatch)){
					foreach($arItem['nomenclatures'][0]['variations'] as &$arVariation){
						if(!isset($arVariation[$key]) || !Helper::strlen($arVariation[$key])){
							$arVariation[$key] = $value;
						}
					}
					unset($arItem[$key], $arVariation);
				}
			}
			# Prepare nmId for further export stocks
			if($arItem['nomenclatures'][0]['nmId']){
				$arDataMore['nmId'] = $arItem['nomenclatures'][0]['nmId'];
			}
			# Move stocks to $arDataMore
			$arDataMore['stocks'] = [];
			if(is_array($arItem['nomenclatures'][0]['variations'])){
				foreach($arItem['nomenclatures'][0]['variations'] as $key => &$arVariation){
					$chrtId = $arVariation['chrtId'];
					foreach($arVariation as $field => $value){
						if(preg_match('#^stock_(\d+)$#', $field, $arMatch)){
							$arDataMore['stocks'][] = [
								'barcode' => $arVariation['barcode'],
								'stock' => intVal($value),
								'warehouseId' => intVal($arMatch[1]),
							];
							unset($arVariation[$field]);
						}
					}
				}
				unset($arVariation);
			}
		}
	}

	/**
	 * Add _id for both product and offers
	 */
	protected function addTmpIds(&$arItem, $arElement){
		$arItem = array_merge(['_id' => intVal($arElement['ID'])], $arItem);
	}

	/**
	 * Remove all _id for both product and offers
	 */
	protected function clearTmpIds(&$arItem, $bOffer=false){
		if($bOffer && !$this->isOffersNewMode()){
			return;
		}
		unset($arItem['_id']);
		if(is_array($arItem['nomenclatures'][0]['variations'])){
			foreach($arItem['nomenclatures'][0]['variations'] as $key => &$arVariation){
				unset($arVariation['_id']);
			}
			unset($arVariation);
		}
	}

	/**
	 * @param mixed $strFilename
	 */
	protected function detectImageType($strFilename){
		$type = 'image/jpeg';
		$ext = toLower(pathinfo($strFilename, PATHINFO_EXTENSION));
		switch($ext){
			case 'PNG':
				$type = 'image/png';
				break;
			case 'GIF':
				$type = 'image/gif';
				break;
			case 'BMP':
				$type = 'image/bmp';
				break;
			case 'WEBP':
				$type = 'image/webp';
				break;
		}
		return $type;
	}

		
	/**
	 * Upload single image
	 *
	 * @param $strFile relative path to file
	 * @param $strUuid UUID
	 */
	protected function uploadImage($intElementId, $strFileBinary, $url, $strImageType, $strUuid){
		if(!strlen($strFileBinary)){
			return;
		}
		#
		$strBoundary = 'boundaryImage';
		$EOL = "\r\n";
		#
		$strContent = $EOL;
		$strContent .= sprintf('--%s', $strBoundary).$EOL;
		$strContent .= 'Content-Disposition: form-data; name="uploadfile"; filename="image.jpg"'.$EOL;
		$strContent .= 'Content-Type: '.$strImageType.$EOL.$EOL;
		$strContent .= $strFileBinary.$EOL;
		$strContent .= sprintf('--%s--', $strBoundary);
		#
		$arHeader = [
			'Content-Type' => sprintf('multipart/form-data; boundary=%s', $strBoundary),
			'Authorization' => $this->getAuthToken(),
			'X-File-Id' => $strUuid,
			'X-Supplier-ID' => $this->getSupplierId(),
			'Accept' => 'application/json',
			'Connection' => 'keep-alive',
		];
		#
		$arRequest = [
			'METHOD' => 'POST',
			'HEADER' => $arHeader,
			'CONTENT' => $strContent,
			'SKIP_HTTPS_CHECK' => true,
			'RETURN_TEXT' => true,
			'ADD_SYSTEM_HEADERS' => false,
			'ELEMENT_ID' => $intElementId,
			'USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.116 Safari/537.36',
		];
		#
		$strJsonResponse = $this->API->execute('/card/upload/file/multipart', null, $arRequest);
		$strResponseCode = $this->API->getResponseCode();
		$arLogRequest = $arRequest;
		if($strResponseCode == 200){
			$strResponse = implode($EOL, $this->API->getHeaders()).$EOL.$EOL.$strJsonResponse;
			if($arImage = $this->saveImage($intElementId, $url, $strUuid, $strImageType, $strResponse)){
				return $arImage;
			}
		}
		else{
			$this->addToLog(static::getMessage('LOG_IMAGE_UPLOAD_ERROR', [
				'#ELEMENT_ID#' => $intElementId,
				'#UUID#' => $strUuid,
				'#URL#' => $url,
				'#RESPONSE_CODE#' => $strResponseCode,
				'#RESPONSE#' => $strJsonResponse,
			]));
		}
		return false;
	}
		
	/**
	 * Process attributes
	 * @param array $arItem
	 * @param boolean $bOffer
	 */
	protected function processItemAttributes(&$arItem, $arElement, &$arDataMore, $strCategoryName, $bOffer=false){
		$arAddin = [];
		$arAddinNomenklature = [];
		$arAddinVariantions = []; // For product only!
		foreach($arItem as $strField => $arValues){
			if($arAttribute = $this->parseAttribute($strField)){
				unset($arItem[$strField]);
				if($this->bSkipExportImages){
					if($arAttribute['NAME'] == static::getMessage('CUSTOM_ATTR_PHOTO')){
						continue;
					}
				}
				if(!Helper::isEmpty($arValues)){
					if(is_string($strCategoryName) && $arAttribute['CATEGORY_NAME'] != $strCategoryName){
						continue;
					}
					if(!is_array($arValues)){
						$arValues = [$arValues];
					}
					$arValuesTmp = [];
					foreach($arValues as $strValue){
						$strKey = 'value';
						if($arAttribute['IS_NUMBER'] == 'Y'){
							$strKey = 'count';
							$strValue = floatVal($strValue);
						}
						$arValueItem = [
							$strKey => $strValue,
						];
						if(strlen($arAttribute['UNIT'])){
							$arValueItem['units'] = $arAttribute['UNIT'];
						}
						# Custom data
						if($arAttribute['NAME'] == static::getMessage('CUSTOM_ATTR_PHOTO')){
							if(!$this->processImage($arElement['ID'], $arValueItem, $arDataMore)){
								unset($arValueItem);
							}
						}
						elseif($arAttribute['NAME'] == static::getMessage('CUSTOM_ATTR_PRICE')){
							unset($arValueItem['units']);
						}
						# Add to result values
						if(is_array($arValueItem)){
							$arValuesTmp[] = $arValueItem;
						}
					}
					if($arAttribute['NAME'] == static::getMessage('CUSTOM_ATTR_INGREDIENTS')){
						$this->processIngredients($arElement['ID'], $arValuesTmp);
					}
					$arValues = $arValuesTmp;
					$arAddinItem = [
						'type' => $arAttribute['NAME'],
						'params' => $arValues,
					];
					if($arAttribute['TYPE'] == static::ADDIN_TYPE_NOMENCLATURE){
						$arAddinNomenklature[] = $arAddinItem;
					}
					elseif($arAttribute['TYPE'] == static::ADDIN_TYPE_VARIATION && (!$bOffer || $this->isOffersNewMode())){
						$arAddinVariantions[] = $arAddinItem;
					}
					else{
						$arAddin[] = $arAddinItem;
					}
				}
			}
		}
		if(!empty($arAddin)){
			$arItem['addin'] = $arAddin;
		}
		if(!empty($arAddinNomenklature)){
			$arItem[static::TMP_NOMENCLATURE_ADDIN] = $arAddinNomenklature;
		}
		if($bOffer && !$this->isOffersNewMode()){
			unset($arItem['object']);
		}
		# If product export in variations too
		if($this->bOriginalProcessElement && !$bOffer || $this->isOffersNewMode()){
			$arVariation = [];
			if($arItem['barcode']){
				$arVariation['barcode'] = $arItem['barcode'];
			}
			unset($arItem['barcode']);
			if(!empty($arAddinVariantions)){
				$arVariation['addin'] = $arAddinVariantions;
			}
			if(!empty($arVariation)){
				$arItem[static::TMP_PRODUCT_VARIATION] = $arVariation;
			}
		}
	}
	
	protected function processNomenclature_Default(array &$arItem, $bOffer=false){
		if($bOffer){
			return;
		}
		# Check offers exists
		$arOffers = [];
		if(array_key_exists('_OFFER_PREPROCESS', $arItem)){
			# Prepare offers data
			if(!empty($arItem['_OFFER_PREPROCESS'])){
				foreach($arItem['_OFFER_PREPROCESS'] as $arOffer){
					try{
						$arOffers[] = Json::decode($arOffer['DATA']);
					}
					catch(\Exception $obError){}
				}
			}
			unset($arItem['_OFFER_PREPROCESS']);
		}
		# Init nomenclature
		$arNomenclature = [];
		$arCopyFromItem = ['vendorCode'];
		foreach($arCopyFromItem as $strField){
			if(array_key_exists($strField, $arItem)){
				$arNomenclature[$strField] = $arItem[$strField];
			}
		}
		$arVariations = [];
		# Process found offers
		if($this->bOriginalProcessOffers){
			if(!empty($arOffers)){
				foreach($arOffers as $arOffer){
					$arVariations[] = $arOffer;
				}
			}
		}
		# Put product as variation
		if(array_key_exists(static::TMP_PRODUCT_VARIATION, $arItem)){
			$arVariations = array_merge([$arItem[static::TMP_PRODUCT_VARIATION]], $arVariations);
			unset($arItem[static::TMP_PRODUCT_VARIATION]);
		}
		# Put offers to main product
		$arNomenclature['variations'] = $arVariations;
		# Put nomenklature addin (from)
		if(array_key_exists(static::TMP_NOMENCLATURE_ADDIN, $arItem)){
			$arNomenclature['addin'] = $arItem[static::TMP_NOMENCLATURE_ADDIN];
			unset($arItem[static::TMP_NOMENCLATURE_ADDIN]);
		}
		# Put all nomenclatures
		$arItem['nomenclatures'] = [$arNomenclature];
		# Remove trash
		if(!$bOffer){
			unset($arItem['vendorCode']);
			unset($arItem['barcode']);
		}
	}

	protected function processNomenclature_NewMode(array &$arItem, $bOffer=false){
		# Init nomenclature
		$arNomenclature = [];
		$arCopyFromItem = ['vendorCode'];
		foreach($arCopyFromItem as $strField){
			if(array_key_exists($strField, $arItem)){
				$arNomenclature[$strField] = $arItem[$strField];
			}
		}
		$arVariations = [];
		# Put product as variation
		if(array_key_exists(static::TMP_PRODUCT_VARIATION, $arItem)){
			$arVariations = array_merge([$arItem[static::TMP_PRODUCT_VARIATION]], $arVariations);
			unset($arItem[static::TMP_PRODUCT_VARIATION]);
		}
		# Put offers to main product
		$arNomenclature['variations'] = $arVariations;
		# Put nomenklature addin (from)
		if(array_key_exists(static::TMP_NOMENCLATURE_ADDIN, $arItem)){
			$arNomenclature['addin'] = $arItem[static::TMP_NOMENCLATURE_ADDIN];
			unset($arItem[static::TMP_NOMENCLATURE_ADDIN]);
		}
		# Put all nomenclatures
		$arItem['nomenclatures'] = [$arNomenclature];
		# Remove trash
		unset($arItem['vendorCode']);
		unset($arItem['barcode']);
	}
	
	/**
	 *	Check empty required fields (for each category)
	 */
	protected function checkRequiredFields($intIBlockId, $arFields, $strCategoryName){
		$arEmptyRequiredFields = [];
		$arFieldsAll = $this->getFieldsCached($this->intProfileId, $intIBlockId, true);
		foreach($arFields as $strField => $mValue){
			if($arFieldsAll[$strField]){
				$bEmpty = Helper::isEmpty($mValue, $arFieldsAll[$strField]->isSimpleEmptyMode());
				if($bEmpty && $arFieldsAll[$strField]->isCustomRequired()){
					$arAttribute = static::parseAttribute($strField);
					if(is_array($arAttribute)){
						if($this->isAttributeRequired($arAttribute['CATEGORY_NAME'], $arAttribute['NAME'])){
							if(!is_array($arEmptyRequiredFields[$arAttribute['CATEGORY_NAME']])){
								$arEmptyRequiredFields[$arAttribute['CATEGORY_NAME']] = [];
							}
							$arEmptyRequiredFields[$arAttribute['CATEGORY_NAME']][] = $arFieldsAll[$strField]->getName();
						}
					}
				}
			}
		}
		if(!empty($arEmptyRequiredFields[$strCategoryName])){
			$arErrors = [];
			$arErrors[] = static::getMessage('ERROR_EMPTY_REQUIRED_FIELDS', [
				'#CATEGORY#' => $strCategoryName,
				'#FIELDS#' => implode(', ', $arEmptyRequiredFields[$strCategoryName]),
				]);
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
	 * Process each image in result item
	 *
	 * @param array $arItem
	 */
	protected function processImage($intElementId, &$arValueItem, &$arDataMore){
		if(is_array($arValueItem) && !empty($arValueItem['value'])){
			$strImageBinary = null;
			$strImageType = null;
			$url = $arValueItem['value'];
			if(is_string($url)){
				# Check file exists (file caching)
				// if($arImage = $this->checkImage($intElementId, $url)){
				// 	$arValueItem['value'] = $arImage['UUID'];
				// 	$arValueItem['units'] = $arImage['TYPE'];
				// 	return true;
				// }
				# Prepare file data
				$strSiteUrl = Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y', '/');
				if(Helper::strpos($url, $strSiteUrl) === 0){
					$strFile = Helper::substr($url, strlen($strSiteUrl) - 1);
					$strImageBinary = file_get_contents(Helper::root().$strFile);
					$strImageType = $this->detectImageType($url);
				}
				else{
					if(strlen($strResponse = HttpRequest::get($url))){
						$strImageBinary = $strResponse;
						$strImageType = $this->detectImageType($url);
					}
					unset($strResponse);
				}
			}
			# Upload file
			if(is_string($strImageBinary) && strlen($strImageBinary)){
				$strUuid = Helper::generateUuid();
				if($this->getPreviewMode()){
					$arImage = [
						'MODULE_ID' => $this->strModuleId,
						'PROFILE_ID' => $this->intProfileId,
						'ELEMENT_ID' => $intElementId,
						'UUID' => $strUuid,
						'URL' => $url,
						'TYPE' => $strImageType,
					];
					$arValueItem['value'] = $strUuid;
					$arValueItem['units'] = $strImageType;
					$arDataMore['imagesInfo'][] = $arImage;
					return $arImage;
				}
				else{
					if($arImage = $this->uploadImage($intElementId, $strImageBinary, $url, $strImageType, $strUuid)){
						$arValueItem['value'] = $strUuid;
						$arValueItem['units'] = $strImageType;
						return $arImage;
					}
				}
			}
		}
		return false;
	}

	/**
	 * Check image is already sent
	 * @param mixed $intElementId
	 * @param mixed $strUrl
	 */
	protected function checkImage($intElementId, $strUrl){
		$arQuery = [
			'filter' => [
				'MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'ELEMENT_ID' => $intElementId,
				'URL' => $strUrl,
			],
		];
		if($arImage = Image::getList($arQuery)->fetch()){
			$arImage['TIMESTAMP_X'] = $arImage['TIMESTAMP_X']->toString();
			return $arImage;
		}
		return false;
	}

	/**
	 * Save image to acrit_wb_image
	 * @param $intElementId
	 * @param $strUrl
	 * @param $strUuid
	 * @param $strImageType
	 */
	protected function saveImage($intElementId, $strUrl, $strUuid, $strImageType, $strResponse){
		$obResult = Image::add([
			'MODULE_ID' => $this->strModuleId,
			'PROFILE_ID' => $this->intProfileId,
			'ELEMENT_ID' => $intElementId,
			'UUID' => $strUuid,
			'URL' => $strUrl,
			'TYPE' => $strImageType,
			'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime(),
		]);
		if($obResult->isSuccess()){
			return $this->checkImage($intElementId, $strUrl);
		}
		return false;
	}

	/**
	 * Process attribute 'Ingredients'
	 */
	protected function processIngredients($intElementId, &$arValues){
		if(!is_array($arValues)){
			return;
		}
		$ingredients = implode(', ', array_column($arValues, 'value'));
		$ingredients = trim($ingredients);
		$ingredients = trim($ingredients, '.,;');
		$ingredients = str_replace(';', ',', $ingredients);
		$arIngredientsRaw = Helper::explodeValues($ingredients);
		$arIngredients = [];
		foreach($arIngredientsRaw as $key => $strItem){
			if(strlen(trim($strItem))){
				# Hlopok 100%
				if(preg_match('#^(.*?)[\W]*([\d]+)[\W]*$#s'.BX_UTF_PCRE_MODIFIER, $strItem, $arMatch)){
					if(empty($arMatch[1])){
						$arMatch[1] = $arMatch[0];
					}
					$arIngredients[] = [
						'value' => $arMatch[1],
						'count' => intVal($arMatch[2]),
					];
				}
				# 100% Hlopok
				elseif(preg_match('#^[\W]*([\d]+)[\W]*(.*?)$#s'.BX_UTF_PCRE_MODIFIER, $strItem, $arMatch)){
					$arIngredients[] = [
						'value' => $arMatch[2],
						'count' => intVal($arMatch[1]),
					];
				}
				# Other: pkh, plastik
				else{
					$arIngredients[] = [
						'value' => $strItem,
						'count' => round(100 / count($arIngredientsRaw)),
					];
				}
			}
		}
		# Check result
		if(!empty($arIngredients)){
			$bSummIs100 = array_sum(array_column($arIngredients, 'count')) == 100;
			if(!$bSummIs100){
				$this->addToLog(static::getMessage('ERROR_INGREDIENTS_SUMM', ['#ELEMENT_ID#' => $intElementId]));
				// Share 100% between all items
				$items = count($arIngredients); // 6
				$count = floor(100 / $items); // 16
				$arIngredients = array_map(function($arItem)use($count){
					$arItem['count'] = $count;
					return $arItem;
				}, $arIngredients);
				$delta = 100 - $items * $count; // 4 => + 1 to first 4 items
				if($delta > 0){
					for($i = 0; $i < $delta; $i++){
						$arIngredients[$i]['count']++;
					}
				}
			}
		}
		# Replace result
		$arValues = $arIngredients;
	}
	
	/**
	 *	Add custom substep for export step
	 */
	protected function onUpGetExportSteps(&$arExportSteps, &$arSession){
		$arUnsetSteps = ['EXPORT_HEADER', 'EXPORT_FOOTER', 'REPLACE_FILE', 'REPLACE_TMP_FILES'];
		foreach($arUnsetSteps as $strStep){
			unset($arExportSteps[$strStep]);
		}
	}
	
	/**
	 *	Add custom step
	 */
	protected function onUpGetSteps(&$arSteps){
		if($this->arParams['EXPORT_STOCKS'] == 'Y'){
			$arSteps['WB_PAUSE_BEFORE_STOCKS'] = [
				'NAME' => static::getMessage('STEP_PAUSE_BEFORE_STOCKS'),
				'SORT' => 5000,
				'FUNC' => [$this, 'stepPauseBeforeStocks'],
			];
			$arSteps['WB_EXPORT_STOCKS'] = [
				'NAME' => static::getMessage('STEP_EXPORT_STOCKS'),
				'SORT' => 5010,
				'FUNC' => [$this, 'stepExportStocks'],
			];
		}
	}
	
	
	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		#return Exporter::RESULT_SUCCESS;
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
		$arExportItems = $this->getExportDataItems(null, null);
		foreach($arExportItems as $arItem){
			$result = $this->exportItem($arItem, $arSession);
			if($result === Exporter::RESULT_ERROR){
				return Exporter::RESULT_ERROR;
			}
			elseif($result === Exporter::RESULT_CONTINUE){
				if(!Exporter::getInstance($this->strModuleId)->haveTime()){
					return Exporter::RESULT_CONTINUE;
				}
			}
		}
		if(count($arExportItems) == $this->intExportPerStep){
			return Exporter::RESULT_CONTINUE;
		}
		return Exporter::RESULT_SUCCESS;
	}

	/**
	 * Export (create or update) single product
	 * @param $arItem
	 */
	protected function exportItem($arItem, &$arSession){
		try{
			$arItemJson = Json::decode($arItem['DATA']);
		}
		catch(\Exception $obException){
			print 'Export item error: wrong JSON.';
			return Exporter::RESULT_ERROR;
		}
		if(is_array($arItemJson) && !empty($arItemJson)){
			# Check mode - create | update
			$bUpdate = strlen($arItemJson['id']) ? true : false;
			$strMethod = '/card/create';
			if($bUpdate){
				$strMethod = '/card/update';
				# Remove barcodes (because of new problem from 10.06.2021: not found cause: map[supplier:00000000-0000-0000-0000-000000000000])
				foreach($arItemJson['nomenclatures'][0]['variations'] as $key => $arVariation){
					unset($arItemJson['nomenclatures'][0]['variations'][$key]['barcode']);
				}
			}
			$arJson = [
				'id' => Helper::generateUuid(),
				'jsonrpc' => '2.0',
				'params' => [
					'supplierId' => $this->getSupplierId(),
					'card' => $arItemJson,
				],
			];
			$this->addToLog(static::getMessage('LOG_ELEMENT', [
				'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
				'#VENDOR_CODE#' => $arItemJson['supplierVendorCode'],
				'#METHOD#' => $strMethod,
			]));
			$arQueryResult = $this->API->execute($strMethod, $arJson, [
				'METHOD' => 'POST',
				'HEADER' => [
					'Authorization' => $this->getAuthToken(),
				],
				'ELEMENT_ID' => $arItem['ELEMENT_ID'],
			]);
			$bSent = $arQueryResult['id'] == $arJson['id'] && $arQueryResult['result'] === [] && !$arQueryResult['error'];
			$this->writeHistory($arItem['ELEMENT_ID'], $arJson, $bSent, $arQueryResult, $arSession);
			if($bSent){
				$this->setDataItemExported($arItem['ID']);
				return Exporter::RESULT_SUCCESS;
			}
			else{
				$this->addToLog(static::getMessage('LOG_ELEMENT_DEBUG', [
					'#ELEMENT_ID#' => $arItem['ELEMENT_ID'],
					'#VENDOR_CODE#' => $arItemJson['supplierVendorCode'],
					'#METHOD#' => $strMethod,
					'#JSON#' => print_r($arJson, true),
					'#RESULT#' => print_r($arQueryResult, true),
				]), true);
				# Display error
				require __DIR__.'/include/popup/error.php';
				return Exporter::RESULT_ERROR;
			}
		}
		print 'Export item error.';
		return Exporter::RESULT_ERROR;
	}
	
	/**
	 * Write task & history
	 */
	protected function writeHistory($intElementId, $arJson, $bSent, $arQueryResult, &$arSession){
		# Add task
		if(!isset($arSession['TASK_ID'])){
			$arTask = [
				'MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'TASK_UUID' => Helper::generateUuid(),
				'SUPPLIER_ID' => $this->getSupplierId(),
				'SESSION_ID' => session_id(),
				'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
			];
			if($intTaskId = Task::add($arTask)->getId()){
				$arSession['TASK_UUID'] = $arTask['TASK_UUID'];
				$arSession['TASK_ID'] = $intTaskId;
			}
		}
		# Add history
		if($arSession['TASK_ID']){
			$arCard = &$arJson['params']['card'];
			$obHistoryResult = History::add([
				'MODULE_ID' => $this->strModuleId,
				'PROFILE_ID' => $this->intProfileId,
				'TASK_ID' => $arSession['TASK_ID'],
				'REQUEST_ID' => $arJson['id'],
				'SUPPLIER_VENDOR_CODE' => $arCard['supplierVendorCode'],
				'CARD_ID' => $arCard['id'],
				'IMT_ID' => $arCard['imtId'],
				'NOMENCLATURE_ID' => $arCard['nomenclatures'][0]['id'],
				'NM_ID' => $arCard['nomenclatures'][0]['nmId'],
				'VENDOR_CODE' => $arCard['nomenclatures'][0]['vendorCode'],
				'CHRT_ID' => implode(',', array_column($arCard['nomenclatures'][0]['variations'], 'chrtId')),
				'BARCODE' => implode(',', array_column($arCard['nomenclatures'][0]['variations'], 'barcode')),
				'ELEMENT_ID' => $intElementId,
				'JSON' => Json::encode($arJson),
				'RESPONSE' => Json::encode($arQueryResult, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
				'SUCCESS' => $bSent ? 'Y' : 'N',
				'SESSION_ID' => session_id(),
				'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
			]);
			if($obHistoryResult->isSuccess()){
				$arQuery = ['filter' => ['ID' => $arSession['TASK_ID']], 'select' => ['ID', 'PRODUCTS_COUNT']];
				if($arTask = Task::getList($arQuery)->fetch()){
					Task::update($arTask['ID'], ['PRODUCTS_COUNT' => $arTask['PRODUCTS_COUNT'] + 1]);
				}
			}
		}
	}

	/**
	 * Put identifiers (saved external ids) to JSON on generate: supplierVendorCode, barcode, imtId, nmId, chrtId
	 * @param $supplierVendorCode
	 */
	protected function collectResultIdentifiersBefore(&$arResult, $arElement, $arDataMore, $bOffer=false){
		# Save external values
		$arExternalId = [
			'PROFILE_ID' => $this->intProfileId,
			'IBLOCK_ID' => $arElement['IBLOCK_ID'],
			'ELEMENT_ID' => $arElement['ID'],
		];
		if($bOffer && !$this->isOffersNewMode()){
			$arExternalId['EXTERNAL_ID'] = null;
			$arExternalId['EXTERNAL_VALUE'] = $arResult['barcode'];
			Helper::call($this->strModuleId, 'ExternalId', 'set', array_values($arExternalId));
		}
		else{
			$supplierVendorCode = null;
			# First, search normal external_id
			$arExternalId['MODE'] = ExternalId::EXT_MODE_ELEMENT_ID;
			if($arExistExternalId = Helper::call($this->strModuleId, 'ExternalId', 'getExt', array_values($arExternalId))){
				$supplierVendorCode = $arExistExternalId['EXTERNAL_VALUE'];
			}
			# If not found, create it
			else{
				unset($arExternalId['MODE']);
				$arExternalId['EXTERNAL_ID'] = null;
				$arExternalId['EXTERNAL_VALUE'] = Helper::generateUuid();
				$arResult['supplierVendorCode'] = $arExternalId['EXTERNAL_VALUE'];
				$supplierVendorCode = $arExternalId['EXTERNAL_VALUE'];
				Helper::call($this->strModuleId, 'ExternalId', 'set', array_values($arExternalId));
			}
			unset($arResult['supplierVendorCode']);
			$arResult = array_merge(['supplierVendorCode' => $supplierVendorCode], $arResult);
		}
		# Prepare, actualize data from server
		if(!$bOffer || $this->isOffersNewMode()){
			$this->getCardBySupplierVendorCode($arResult['supplierVendorCode'], $arElement['ID']);
		}
		# Load externals id by values
		$arExternalId = [
			'PROFILE_ID' => $this->intProfileId,
			'IBLOCK_ID' => $arElement['IBLOCK_ID'],
			'SEARCH_VALUE' => $arElement['ID'],
		];
		if($arExistExternalId = Helper::call($this->strModuleId, 'ExternalId', 'getExt', array_values($arExternalId))){
			$externalData = $arExistExternalId['EXTERNAL_DATA'];
			$externalData = strlen($externalData) ? Json::decode($externalData) : [];
			# New offers mode
			if($this->isOffersNewMode()){
				if($externalData['imtId'] > 0){
					$arResult = array_merge([
						'id' => $externalData['id'],
						'imtId' => $externalData['imtId'],
					], $arResult);
				}
				if($externalData['nmId'] > 0){
					$arResult['nomenclatures'][0] = array_merge([
						'id' => $externalData['nomenclatureId'],
						'nmId' => $externalData['nmId'],
					], $arResult['nomenclatures'][0]);
				}
				if($externalData['chrtId'] > 0){
					$arResult['nomenclatures'][0]['variations'][0] = array_merge([
						'chrtId' => $externalData['chrtId']
					], $arResult['nomenclatures'][0]['variations'][0]);
				}
			}
			# Normal mode
			else{
				if($bOffer){
					if($externalData['chrtId'] > 0){
						$arResult = array_merge(['chrtId' => $externalData['chrtId']], $arResult);
					}
				}
				else{
					if($externalData['imtId'] > 0){
						$arResult = array_merge([
							'id' => $externalData['id'],
							'imtId' => $externalData['imtId'],
						], $arResult);
					}
					if($externalData['nmId'] > 0){
						$arResult['nomenclatures'][0] = array_merge([
							'id' => $externalData['nomenclatureId'],
							'nmId' => $externalData['nmId'],
						], $arResult['nomenclatures'][0]);
					}
				}
			}
		}
	}

	/**
	 * Collect imtId, nmId, chrtId
	 * @param $supplierVendorCode
	 */
	protected function collectResultIdentifiersAfter($card){
		# Save values for card and nomenclature
		if(Helper::strlen($card['supplierVendorCode'])){
			$arExternalId = [
				'PROFILE_ID' => $this->intProfileId,
				'IBLOCK_ID' => null,
				'SEARCH_VALUE' => $card['supplierVendorCode'],
				'MODE' => ExternalId::EXT_MODE_EXTERNAL_VALUE,
			];
			if($arExistExternalId = Helper::call($this->strModuleId, 'ExternalId', 'getExt', array_values($arExternalId))){
				$nomenclature = reset($card['nomenclatures']);
				$arExternalId = [
					'PROFILE_ID' => $arExistExternalId['PROFILE_ID'],
					'IBLOCK_ID' => $arExistExternalId['IBLOCK_ID'],
					'ELEMENT_ID' => $arExistExternalId['ELEMENT_ID'],
					'EXTERNAL_ID' => $card['imtId'],
					'EXTERNAL_VALUE' => null, // do not save this value because it already saved!!
					'EXTERNAL_DATA' => Json::encode([
						'id' => $card['id'],
						'imtId' => $card['imtId'],
						'userId' => $card['userId'],
						'imtSupplierId' => $card['imtSupplierId'],
						'nomenclatureId' => $nomenclature['id'],
						'nmId' => $nomenclature['nmId'],
						'chrtId' => $nomenclature['variations'][0]['chrtId'],
					]),
				];
				Helper::call($this->strModuleId, 'ExternalId', 'set', array_values($arExternalId));
				# Save values for offers
				if(is_array($nomenclature['variations'])){
					foreach($nomenclature['variations'] as $arVariation){
						if(is_array($arVariation['barcodes']) && !empty($arVariation['barcodes'])){
							$barcode = $arVariation['barcodes'];
							if(is_array($barcode)){
								$barcode = reset($barcode);
							}
							$arExternalId = [
								'PROFILE_ID' => $this->intProfileId,
								'IBLOCK_ID' => null,
								'SEARCH_VALUE' => $barcode,
								'MODE' => ExternalId::EXT_MODE_EXTERNAL_VALUE,
							];
							if($arExistExternalId = Helper::call($this->strModuleId, 'ExternalId', 'getExt', array_values($arExternalId))){
								$arExternalId = [
									'PROFILE_ID' => $arExistExternalId['PROFILE_ID'],
									'IBLOCK_ID' => $arExistExternalId['IBLOCK_ID'],
									'ELEMENT_ID' => $arExistExternalId['ELEMENT_ID'],
									'EXTERNAL_ID' => $arVariation['chrtId'],
									'EXTERNAL_VALUE' => null, // do not save this value because it already saved!!
									'EXTERNAL_DATA' => Json::encode([
										'id' => $arVariation['id'],
										'chrtId' => $arVariation['chrtId'],
									]),
								];
								Helper::call($this->strModuleId, 'ExternalId', 'set', array_values($arExternalId));
							}
						}
					}
				}
			}
		}
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
	 * Correct $bProcessElement and $bProcessOffers
	 **/
	public function getProcessEntities(&$bProcessElement, &$bProcessOffers, $arProfile, $intIBlockID, $arElement){
		$this->bOriginalProcessElement = $bProcessElement;
		$this->bOriginalProcessOffers = $bProcessOffers;
		if($arCatalog = Helper::getCatalogArray($intIBlockID)){ # If iblock has no offers, set element=true, offer=false
			if(!$arCatalog['OFFERS_IBLOCK_ID']){
				$this->bOriginalProcessElement = true;
				$this->bOriginalProcessOffers = false;
			}
		}
		if(!$this->isOffersNewMode()){
			$bProcessElement = true;
		}
	}
	
	/**
	 *	Display status for one task
	 */
	protected function displayTaskStatus($arTask){
		ob_start();
		$strFile = __DIR__.'/include/tasks/status.php';
		Helper::loadMessages($strFile);
		require $strFile;
		return ob_get_clean();
	}
	
	/**
	 *	Search allowed values
	 */
	protected function getAllowedValuesContent($arParams, &$arJsonResult){
		ob_start();
		$strField = $arParams['GET']['field'];
		if($arAttribute = $this->parseAttribute($strField)){
			require __DIR__.'/include/popup/allowed_values.php';
		}
		elseif($strField == 'countryProduction'){
			$arAttribute = [
				'DICTIONARY' => '/countries',
			];
			require __DIR__.'/include/popup/allowed_values.php';
		}
		else{
			print Helper::showError(static::getMessage('ERROR_PARSE_ATTRIBUTE', ['#ATTRIBUTE#' => $strField]));
		}
		$arJsonResult['HTML'] = ob_get_clean();
	}
	
	/**
	 *	
	 */
	protected function getAllowedValuesFilteredContent($arGet){
		ob_start();
		$strField = $arGet['field'];
		if($arAttribute = $this->parseAttribute($strField)){
			require __DIR__.'/include/popup/allowed_values_filtered.php';
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
		$arParams = [
			'TASK_ID' => $arGet['task_id'],
			'ALLOW_COPY' => true,
		];
		return $this->displayPopupJson(Task::getList($arQuery)->fetch(), $arParams);
	}
	
	/**
	 *	Ajax: preview history item
	 */
	protected function getHistoryItemJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'ID' => $arGet['history_item_id'],
			],
			'select' => [
				'JSON',
				'RESPONSE',
				'SUCCESS',
			],
		];
		$arParams = [
			'TASK_ID' => $arGet['task_id'],
			'ALLOW_COPY' => true,
			'SHOW_STOCKS' => true,
		];
		return $this->displayPopupJson(History::getList($arQuery)->fetch(), $arParams);
	}
	
	/**
	 *	Get html for popup for preview history item JSON
	 */
	protected function displayPopupJson($arArray, $arParams=[]){
		if(is_array($arArray) && strlen('JSON')){
			$strFile = __DIR__.'/include/popup/json.php';
			Helper::loadMessages($strFile);
			ob_start();
			$strJson = &$arArray['JSON'];
			require $strFile;
			return ob_get_clean();
		}
		return static::getMessage('ERROR_JSON_NOT_FOUND');
	}

	/**
	 *	Ajax: preview history item
	 */
	protected function getTaskStocksJsonPreview($arGet){
		$arQuery = [
			'filter' => [
				'ID' => $arGet['task_id'],
			],
			'select' => [
				'STOCKS_REQUEST',
				'STOCKS_RESPONSE',
				'STOCKS_RESPONSE_CODE',
			],
		];
		$arParams = [
			'ALLOW_COPY' => true,
		];
		return $this->displayPopupTaskStocksJson(Task::getList($arQuery)->fetch(), $arParams);
	}
	
	/**
	 *	Get html for popup for preview history item JSON
	 */
	protected function displayPopupTaskStocksJson($arTask, $arParams=[]){
		if(is_array($arTask)){
			$strFile = __DIR__.'/include/popup/task_stocks.php';
			Helper::loadMessages($strFile);
			ob_start();
			require $strFile;
			return ob_get_clean();
		}
		return static::getMessage('ERROR_JSON_NOT_FOUND');
	}
	
	/**
	 *	Display popup via AJAX: category add
	 */
	protected function displayPopupCategoryAdd($arParams, &$arJsonResult){
		$strFile = __DIR__.'/include/popup/category_add.php';
		Helper::loadMessages($strFile);
		ob_start();
		require $strFile;
		$arJsonResult['HTML'] = ob_get_clean();
	}

	/**
	 * Get card by supplierVendorCode
	 * @param $supplierVendorCode - UUID-code
	 */
	protected function getCardBySupplierVendorCode($supplierVendorCode, $intElementId=null){
		$result = false;
		$filter = [];
		$filter = [
			'column' => 'supplierVendorCode',
			'search' => $supplierVendorCode,
		];
		$arJson = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'supplierID' => $this->getSupplierId(),
				'filter' => [
					'find' => [$filter],
					'order' => ['column' => 'createdAt', 'order' => 'asc'],
				],
				'query' => [
					'limit' => 10,
					'offset' => 0,
				],
			],
		];
		$arRequest = [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken(),
			],
			'ELEMENT_ID' => $intElementId,
		];
		$queryResult = $this->API->execute('/card/list', $arJson, $arRequest);
		if(is_array($queryResult) && is_array($queryResult['result']['cards']) && !empty($queryResult['result']['cards'])){
			foreach($queryResult['result']['cards'] as $card){
				if($card['supplierVendorCode'] == $supplierVendorCode){
					$this->collectResultIdentifiersAfter($card);
					$result = $card;
				}
			}
		}
		else{
			$this->clearExternalForSupplierVendorCode($supplierVendorCode);
		}
		return $result;
	}

	/**
	 * Get card by imtId (supplierVendorCode)
	 * @param mixed $imtID
	 */
	protected function getCardByImtId($imtID){
		$result = false;
		$filter = [];
		$filter = [
			'column' => 'supplierVendorCode',
			'search' => $supplierVendorCode,
		];
		$arJson = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'imtID' => $imtID,
				'supplierID' => $this->getSupplierId(),
			],
		];
		$arRequest = [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken(),
			],
		];
		return $this->API->execute('/card/cardByImtID', $arJson, $arRequest);
	}

	/**
	 * Get card list
	 * @param mixed $imtID
	 */
	protected function getCardList(){
		$result = false;
		$arJson = [
			'id' => Helper::generateUuid(),
			'jsonrpc' => '2.0',
			'params' => [
				'supplierID' => $this->getSupplierId(),
				'filter' => [
					'order' => ['column' => 'createdAt', 'order' => 'desc'],
				],
				'query' => [
					'limit' => 10,
					'offset' => 0,
				],
			],
		];
		$arRequest = [
			'METHOD' => 'POST',
			'SKIP_ERRORS' => true,
			'HEADER' => [
				'Authorization' => $this->getAuthToken(),
			],
		];
		return $this->API->execute('/card/list', $arJson, $arRequest);
	}

	/**
	 * Clear values
	 * @param $supplierVendorCode
	 */
	protected function clearExternalForSupplierVendorCode($supplierVendorCode){
		$arRemove = [];
		$arExternalId = [
			'PROFILE_ID' => $this->intProfileId,
			'IBLOCK_ID' => null,
			'SEARCH_VALUE' => $supplierVendorCode,
			'MODE' => ExternalId::EXT_MODE_EXTERNAL_VALUE,
		];
		if($arExistExternalId = Helper::call($this->strModuleId, 'ExternalId', 'getExt', array_values($arExternalId))){
			$arRemove[] = $arExistExternalId['ELEMENT_ID'];
			if($arCatalog = Helper::getCatalogArray($arExistExternalId['IBLOCK_ID'])){
				if($arCatalog['OFFERS_IBLOCK_ID']){
					$arFilter = [
						'IBLOCK_ID' => $arCatalog['OFFERS_IBLOCK_ID'],
						'PROPERTY_'.$arCatalog['OFFERS_PROPERTY_ID'] => $arExistExternalId['ELEMENT_ID'],
					];
					$resOffers = \CIBlockElement::getList([], $arFilter, false, false, ['ID']);
					while($arOffer = $resOffers->fetch()){
						$arRemove[] = $arOffer['ID'];
					}
				}
			}
		}
		if(!empty($arRemove)){
			$arQuery = [
				'filter' => [
					'ELEMENT_ID' => $arRemove,
				],
				'select' => [
					'ID',
				],
			];
			$resExternals = Helper::call($this->strModuleId, 'ExternalId', 'getList', [$arQuery]);
			while($arExternal = $resExternals->fetch()){
				Helper::call($this->strModuleId, 'ExternalId', 'update', [$arExternal['ID'], [
					'EXTERNAL_ID' => false,
					'EXTERNAL_DATA' => false,
				]]);
			}
		}
	}
	
	/**
	 *	Handler for format file open link
	 */
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		return $this->getExtFileOpenLink('https://suppliers-portal.wildberries.ru/goods/products-card/', 
			Helper::getMessage('ACRIT_EXP_FILE_OPEN_EXTERNAL'));
	}

	/**
	 * @param mixed $intProfileID
	 * @param mixed $arData
	 */
	public function stepPauseBeforeStocks($intProfileID, $arData){
		#sleep(10);
		return Exporter::RESULT_SUCCESS;
	}
	
	/**
	 * @param mixed $intProfileID
	 * @param mixed $arData
	 */
	public function stepExportStocks($intProfileID, $arData){
		$intTaskId = $arData['SESSION']['EXPORT']['TASK_ID'];
		# Prepare stocks
		$arStocks = [];
		$arExportItems = $this->getExportDataItems(null, ['ID', 'ELEMENT_ID', 'DATA_MORE', '_SKIP_DATA_FIELD'], true, true);
		foreach($arExportItems as $arItem){
			if($arDataMore = unserialize($arItem['DATA_MORE'])){
				if(is_array($arDataMore['stocks']) && !empty($arDataMore['stocks'])){
					$arStocks = array_merge($arStocks, $arDataMore['stocks']);
				}
			}
		}
		# Export prepared stocks
		if(!empty($arStocks)){
			$intStocksCount = count($arStocks);
			// $arSendStocks = [
			// 	'token' => $this->arParams['STOCK_TOKEN'],
			// 	'data' => $arStocks,
			// ];
			$arSendStocks = $arStocks;
			$arParams = [
				'METHOD' => 'POST',
				'HEADER' => [
					'Content-Type' => 'application/json',
					'Authorization' => $this->getAuthToken($strAuthToken),
				],
				'TIMEOUT' => 30,
				'HOST' => 'https://suppliers-api.wildberries.ru',
			];
			$strUrl = '/api/v2/stocks';
			$arJsonResponse = $this->API->execute($strUrl, $arSendStocks, $arParams);
			$intResponseCode = $this->API->getResponseCode();
			$bSuccess = $intResponseCode == 200;
			# Save general stocks info to task
			if($intTaskId){
				Task::update($intTaskId, [
					'STOCKS_REQUEST' => Json::encode($arSendStocks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
					'STOCKS_RESPONSE' => Json::encode($arJsonResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
					'STOCKS_RESPONSE_CODE' => $intResponseCode,
				]);
			}
			# Save info about stocks for each product
			if($bSuccess){
				$this->addToLog(static::getMessage('LOG_STOCKS_EXPORTED', [
					'#COUNT#' => $intStocksCount,
				]), true);
				# Save stocks to history
				if($intTaskId){
					foreach($arStocks as $arStockItem){
						foreach($arStockItem['stocks'] as $arStock){
							HistoryStock::add([
								'MODULE_ID' => $this->strModuleId,
								'PROFILE_ID' => $this->intProfileId,
								'TASK_ID' => $intTaskId,
								'NM_ID' => $arStockItem['nmId'],
								'CHRT_ID' => $arStock['chrtId'],
								'PRICE' => $arStock['price'],
								'QUANTITY' => $arStock['quantity'],
								'STORE_ID' => $arStock['storeId'],
								'SUCCESS' => $bSuccess ? 'Y' : 'N',
								'SESSION_ID' => session_id(),
								'TIMESTAMP_X' => new \Bitrix\Main\Type\Datetime,
							]);
						}
					}
				}
			}
			else{
				$strMessage = static::getMessage('LOG_STOCKS_ERROR', [
					'#STOCKS#' => print_r($arStocks, true),
					'#RESPONSE_CODE#' => $this->API->getResponseCode(true),
					'#HEADERS#' => print_r($this->API->getHeaders(), true),
					'#CONTENT#' => print_r($strResponse, true),
				]);
				$this->addToLog($strMessage);
				print Helper::showError(static::getMessage('LOG_STOCKS_ERROR_TITLE'), $strMessage);
				return Exporter::RESULT_ERROR;
			}
		}
		return Exporter::RESULT_SUCCESS;
	}

}

?>