<?
/**
 * Acrit Core: Aliexpress plugin
 * @documentation https://developers.aliexpress.com/en/doc.htm?spm=a219a.7386653.0.0.4a549b71WVEp1c&docId=109760&docType=1
 */

namespace Acrit\Core\Export\Plugins;

require_once __DIR__.'/include/sdk/TopSdk.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Export\Exporter,
	\Acrit\Core\Log,
	\Acrit\Core\Helper,
	\Acrit\Core\Xml,
	\Acrit\Core\Json,
	\Acrit\Core\Export\UniversalPlugin;
use PhpOffice\PhpSpreadsheet\Exception;

class AliexpressComApi extends AliexpressCom {

	const DATE_UPDATED = '2021-05-12';
	const APP_KEY = '30433054';
	const SECRET_KEY = '17ec1f9a551165193fffcc8c3ffd614c';

	protected static $bSubclass = true;

	# General
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $bApi = true;
//	protected $arSupportedCurrencies = ['RUB'];
//	protected $bCategoryCustomName = true;
	protected $intExportPerStep = 50;
	protected $bOffersPreprocess = true;

	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;


	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['TOKEN'] = $this->includeHtml(__DIR__ . '/include/settings/token.php');
		if ($this->arParams['TOKEN']) {
			$arSettings['SECTIONS'] = $this->includeHtml(__DIR__ . '/include/settings/sections.php');
		}
//		$res = $this->getAliProduct(1005002334869782);
//		echo '<pre>'; print_r($res); echo '</pre>';
	}

	public function getTokenLink() {
		$link = "https://oauth.aliexpress.com/authorize?response_type=code&client_id=".self::APP_KEY."&state=&view=web&sp=ae";
		return $link;
	}


	/**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		parent::ajaxAction($strAction, $arParams, $arJsonResult);
		#$arProfile = Profile::getProfiles($arParams['PROFILE_ID']);
		$strVkGroupId = strval($this->arProfile['PARAMS']['GROUP_ID']);
		switch ($strAction) {
			case 'check_connection':
				$token = $arParams['POST']['token'];
				$res = $this->checkConnection($token, $message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
			case 'get_sections':
				$selected_categs = $arParams['POST']['selected_categs'];
				if (!$selected_categs || empty($selected_categs)) {
					$selected_categs = $this->arProfile['PARAMS']['SECTION'];
				}
				$arCategLists = $this->getAliCategoryLists($selected_categs);
				$arJsonResult['lists'] = $arCategLists;
				$arJsonResult['result'] = 'ok';
				break;
		}
	}


	/**
	 *	Get selected section id
	 */
	public function getAliCategoryId() {
		$arSections = $this->arProfile['PARAMS']['SECTION'];
		$arSections = array_diff($arSections, ['']);
		$category_id = $arSections[count($arSections) - 1];
		return $category_id;
	}

	/**
	 * Correct $bProcessElement and $bProcessOffers
	 **/
	public function getProcessEntities(&$bProcessElement, &$bProcessOffers, $arProfile, $intIBlockID, $arElement){
		$this->bOriginalProcessElement = $bProcessElement;
		$this->bOriginalProcessOffers = $bProcessOffers;
		$bProcessElement = true;
	}


	/**
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		// Schema info
		$category_id = $this->getAliCategoryId();
		try {
			$arSchema = self::getAliProductSchema($category_id);
		} catch (\Exception $e) {
			echo '<div class="adm-info-message-wrap"><div class="adm-info-message" style="color:red">' . static::getMessage('LOAD_ERROR') . ': ' . $e->getMessage() . ' (' . $e->getCode() . ')' . '</div></div>';
		}
//			echo '<pre>'; print_r($arSchema); echo '</pre>';
//		$res = self::getAliProduct(1005002524642762);
//		echo '<pre>'; print_r($res); echo '</pre>';
//		if (empty($arSchema)) {
//			echo '<div class="adm-info-message-wrap"><div class="adm-info-message" style="color:red">' . static::getMessage('LOAD_WARNING') . '</div></div>';
//		}
		// Iblock offers mode
		$arCatalog = Helper::getCatalogArray($intIBlockID);
		if ($arCatalog['OFFERS'] == 'Y') {
			$bIBOffersMode = $this->arProfile['IBLOCKS'][$arCatalog['PRODUCT_IBLOCK_ID']]['PARAMS']['OFFERS_MODE'];
		}
		else {
			$bIBOffersMode = $this->arProfile['IBLOCKS'][$intIBlockID]['PARAMS']['OFFERS_MODE'];
		}
		$bOffers = Helper::isOffersIBlock($intIBlockID);
		if ($bIBOffersMode != 'all' || !$bOffers) {
			// General fields
			$arResult['HEADER_GENERAL'] = [];
			$arResult['sku_code'] = ['FIELD' => 'PROPERTY_ARTNUMBER', 'REQUIRED' => true];
			$arResult['locale'] = ['CONST' => 'ru_RU', 'REQUIRED' => true];
			$arResult['title'] = ['FIELD' => 'NAME', 'REQUIRED' => true];
			$arResult['description'] = ['FIELD' => 'DETAIL_TEXT', 'REQUIRED' => true];
			$arResult['brand_name'] = ['CONST' => '', 'REQUIRED' => true];
			$arResult['images'] = ['FIELD'    => 'PROPERTY_MORE_PHOTO',
			                       'MULTIPLE' => true,
			                       'REQUIRED' => true
			]; //TODO multiple
			$arResult['product_units_type'] = ['CONST' => '100000015', 'REQUIRED' => true];
			$arResult['inventory_deduction_strategy'] = ['CONST' => 'place_order_withhold', 'REQUIRED' => true];
			$arResult['inventory'] = ['FIELD' => 'CATALOG_QUANTITY_RESERVED'];
			$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1'];
			$arResult['discount_price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
			$arResult['package_weight'] = ['REQUIRED' => true];
			$arResult['package_length'] = ['REQUIRED' => true];
			$arResult['package_height'] = ['REQUIRED' => true];
			$arResult['package_width'] = ['REQUIRED' => true];
			$arResult['shipping_preparation_time'] = ['CONST' => '', 'REQUIRED' => true];
			$arResult['shipping_template_id'] = ['CONST' => '', 'REQUIRED' => true];
			$arResult['service_template_id'] = ['CONST' => '', 'REQUIRED' => true];
			// Default values
			$arResult['description']['PARAMS']['HTMLSPECIALCHARS'] = 'skip';
			// Hint: product_units_type
			$arResult['inventory_deduction_strategy']['ALLOWED_VALUES'] = [
				'place_order_withhold'   => static::getMessage('SETTINGS_VALUES_inventory_deduction_strategy_withhold'),
				'payment_success_deduct' => static::getMessage('SETTINGS_VALUES_inventory_deduction_strategy_deduct'),
			];
			$arResult['inventory_deduction_strategy']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['inventory_deduction_strategy']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			$variants = [];
			foreach ($arSchema['properties']['product_units_type']['oneOf'] as $arVariant) {
				$variants[$arVariant['const']] = $arVariant['title'];
			}
			$arResult['product_units_type']['ALLOWED_VALUES'] = $variants;
			$arResult['product_units_type']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['product_units_type']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			// Hint: locale
			$variants = [];
			foreach ($arSchema['properties']['locale']['oneOf'] as $arVariant) {
				$variants[$arVariant['const']] = $arVariant['title'];
			}
			$arResult['locale']['ALLOWED_VALUES'] = $variants;
			$arResult['locale']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['locale']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			// Hint: shipping_template_id
			$variants = [];
			foreach ($arSchema['properties']['shipping_template_id']['oneOf'] as $arVariant) {
				$variants[$arVariant['const']] = $arVariant['title'];
			}
			$arResult['shipping_template_id']['ALLOWED_VALUES'] = $variants;
			$arResult['shipping_template_id']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['shipping_template_id']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			// Hint: service_template_id
			$variants = [];
			foreach ($arSchema['properties']['service_template_id']['oneOf'] as $arVariant) {
				$variants[$arVariant['const']] = $arVariant['title'];
			}
			$arResult['service_template_id']['ALLOWED_VALUES'] = $variants;
			$arResult['service_template_id']['ALLOWED_VALUES_USE_SELECT'] = true;
			$arResult['service_template_id']['ALLOWED_VALUES_ASSOCIATIVE'] = true;
			// Category fields
			$arCategInfo = $arSchema['properties']['category_attributes'];
//		echo '<pre>'; print_r($arCategInfo); echo '</pre>';
			foreach ($arCategInfo['properties'] as $s_name => $arProp) {
				$s_key = 'categ_attribs: ' . str_replace('.', '_dot_', $s_name);
				if ($arProp['properties']['value']['type'] == 'array') {
					$s_key .= ' (multi)';
				}
				$required = in_array($s_name, $arCategInfo['required']) ? true : false;
				$arResult[$s_key] = [
					'NAME'     => $arProp['title'],
					'CONST'    => '',
					'REQUIRED' => $required,
				];
				$variants = [];
//				if ($arProp['properties']['value']['type'] == 'string') {
				if (isset($arProp['properties']['value']['oneOf'])) {
					$variants[''] = '';
					foreach ($arProp['properties']['value']['oneOf'] as $arVariant) {
						$variants[$arVariant['const']] = $arVariant['title'];
					}
				} elseif (isset($arProp['properties']['value']['items']['oneOf'])) {
					$variants[''] = '';
					foreach ($arProp['properties']['value']['items']['oneOf'] as $arVariant) {
						$variants[$arVariant['const']] = $arVariant['title'];
					}
				}
//				}
				if ( ! empty($variants)) {
					$arResult[$s_key]['ALLOWED_VALUES'] = $variants;
					$arResult[$s_key]['ALLOWED_VALUES_USE_SELECT'] = true;
					$arResult[$s_key]['ALLOWED_VALUES_ASSOCIATIVE'] = true;
				}
			}
			// Category SKU attributes
			$arCategSkuAttributes = $arSchema['properties']['sku_info_list']['items']['properties']['sku_attributes'];
			foreach ($arCategSkuAttributes['properties'] as $s_name => $arProp) {
				$s_key = 'sku_attribs: ' . str_replace('.', '_dot_', $s_name);
				$required = in_array($s_name, $arCategSkuAttributes['required']) ? true : false;
				$arResult[$s_key] = [
					'NAME'     => $arProp['title'],
					'CONST'    => '',
					'REQUIRED' => $required,
				];
				$variants = [];
				if (isset($arProp['properties']['value']['oneOf'])) {
					$variants[''] = '';
					foreach ($arProp['properties']['value']['oneOf'] as $arVariant) {
						$variants[$arVariant['const']] = $arVariant['title'];
					}
				} elseif (isset($arProp['properties']['value']['items']['oneOf'])) {
					$variants[''] = '';
					foreach ($arProp['properties']['value']['items']['oneOf'] as $arVariant) {
						$variants[$arVariant['const']] = $arVariant['title'];
					}
				}
				if ( ! empty($variants)) {
					$arResult[$s_key]['ALLOWED_VALUES'] = $variants;
					$arResult[$s_key]['ALLOWED_VALUES_USE_SELECT'] = true;
					$arResult[$s_key]['ALLOWED_VALUES_ASSOCIATIVE'] = true;
					$variants_descr = [];
					foreach ($variants as $key => $name) {
						if ($key) {
							$variants_descr[] = $name . ' (' . $key . ')';
						}
					}
					$arResult[$s_key]['DESCRIPTION'] = implode(', ', $variants_descr);
				}
			}
			// Category SKU attributes
//			if ($bIBOffersMode != 'all' || $bOffers) {
//				$list = $this->getCategorySkuAttribs($this->getAliCategoryId());
//				foreach ($list as $attr) {
//					$s_key = 'sku_attribs: ' . str_replace('.', '_dot_', $attr['aliexpress_sku_name']);
//					$required = $attr['required'];
//					$arResult[$s_key] = [
//						'NAME'     => $attr['aliexpress_sku_name'],
//						'CONST'    => '',
//						'REQUIRED' => $required,
//					];
//					$variants = [];
//					if ($attr['aliexpress_sku_value_list']['sku_value_simplified_info_dto']) {
//						$resp_values = $attr['aliexpress_sku_value_list']['sku_value_simplified_info_dto'];
//						$values = ($resp_values[0]) ? $resp_values : [$resp_values];
//						$variants[''] = '';
//						foreach ($values as $value) {
//							$variants[$value['aliexpress_sku_value_name']] = $value['aliexpress_sku_value_name'];
//						}
//					}
//					if ( ! empty($variants)) {
//						$arResult[$s_key]['ALLOWED_VALUES'] = $variants;
//						$arResult[$s_key]['ALLOWED_VALUES_USE_SELECT'] = true;
//						$arResult[$s_key]['ALLOWED_VALUES_ASSOCIATIVE'] = true;
//					}
//				}
//			}
		}
		else {
			$arResult['price'] = ['FIELD' => 'CATALOG_PRICE_1'];
			$arResult['discount_price'] = ['FIELD' => 'CATALOG_PRICE_1__WITH_DISCOUNT'];
			$arResult['inventory'] = ['FIELD' => 'CATALOG_QUANTITY_RESERVED'];
			$arResult['sku_code'] = ['FIELD' => 'PROPERTY_ARTNUMBER', 'REQUIRED' => true];
			// Category SKU attributes
			$arCategSkuAttributes = $arSchema['properties']['sku_info_list']['items']['properties']['sku_attributes'];
			foreach ($arCategSkuAttributes['properties'] as $s_name => $arProp) {
				$s_key = 'sku_attribs: ' . str_replace('.', '_dot_', $s_name);
				$required = in_array($s_name, $arCategSkuAttributes['required']) ? true : false;
				$arResult[$s_key] = [
					'NAME'     => $arProp['title'],
					'CONST'    => '',
					'REQUIRED' => $required,
				];
				$variants = [];
				if (isset($arProp['properties']['value']['oneOf'])) {
					$variants[''] = '';
					foreach ($arProp['properties']['value']['oneOf'] as $arVariant) {
						$variants[$arVariant['const']] = $arVariant['title'];
					}
				} elseif (isset($arProp['properties']['value']['items']['oneOf'])) {
					$variants[''] = '';
					foreach ($arProp['properties']['value']['items']['oneOf'] as $arVariant) {
						$variants[$arVariant['const']] = $arVariant['title'];
					}
				}
				if ( ! empty($variants)) {
					$arResult[$s_key]['ALLOWED_VALUES'] = $variants;
					$arResult[$s_key]['ALLOWED_VALUES_USE_SELECT'] = true;
					$arResult[$s_key]['ALLOWED_VALUES_ASSOCIATIVE'] = true;
					$variants_descr = [];
					foreach ($variants as $key => $name) {
						if ($key) {
							$variants_descr[] = $name . ' (' . $key . ')';
						}
					}
					$arResult[$s_key]['DESCRIPTION'] = implode(', ', $variants_descr);
				}
			}
		}
		return $arResult;
	}

	protected function processElement_BuildJson($arElement, $arFields, $arElementSections, $intMainIBlockId) {
		$arResult = parent::processElement_BuildJson($arElement, $arFields, $arElementSections, $intMainIBlockId);
		# Add additional fields
		$arResult['DATA_MORE']['ADDITIONAL_FIELDS'] = [];
		$intIBlockId = $arElement['IBLOCK_ID'];
		$arAdditionalFields = Helper::call($this->strModuleId, 'AdditionalField', 'getListForProfileIBlock',
			[$this->intProfileId, $intIBlockId]);
		if (is_array($arAdditionalFields) && !empty($arAdditionalFields)) {
			foreach ($arAdditionalFields as $arAdditionalField) {
				if (isset($arFields[$arAdditionalField['FIELD']])) {
					$arResult['DATA_MORE']['ADDITIONAL_FIELDS'][$arAdditionalField['NAME']] = $arFields[$arAdditionalField['FIELD']];
				}
			}
		}
		return $arResult;
	}


	/**
	 *	Export data by API (step-by-step if cron, or one step if manual)
	 */
	protected function stepExport_ExportApi(&$arSession, $arStep){
		$mResult = Exporter::RESULT_ERROR;
		if ($this->bCron) {
			do {
				$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
			}
			while ($mResult === Exporter::RESULT_CONTINUE);
		}
		else {
			$mResult = $this->stepExport_ExportApi_Step($arSession, $arStep);
		}
		return $mResult;
	}

	/**
	 *	Export data by API (one step)
	 */
	protected function stepExport_ExportApi_Step(&$arSession, $arStep){
		$arItems = $this->getExportDataItems(null, null);
		if ( ! empty($arItems)) {
			$arJsonItems = [];
			$arDataMore = [];
			foreach ($arItems as $arItem) {
				$arEncodedItem = Json::decode($arItem['DATA']);
				$arDataMore = unserialize($arItem['DATA_MORE']);
				$category_id = $this->getAliCategoryId();

				$arProductFields = [];
				$arProductFields['category_id'] = $category_id;
				$arProductFields['brand_name'] = $arEncodedItem['brand_name'];
				$arProductFields['locale'] = $arEncodedItem['locale'];
				$arProductFields['product_units_type'] = $arEncodedItem['product_units_type'];
				$arProductFields['title_multi_language_list'] = [
					'locale' => $arEncodedItem['locale'],
					'title'  => $arEncodedItem['title'],
				];
				$arProductFields['description_multi_language_list'] = [
					'locale'      => $arEncodedItem['locale'],
					'module_list' => [
						'type' => 'html',
						'html' => [
							'content' => $arEncodedItem['description'],
						],
					],
				];
				$arProductFields['image_url_list'] = $arEncodedItem['images'];
				$arProductFields['inventory_deduction_strategy'] = 'payment_success_deduct';
				$arProductFields['package_weight'] = $arEncodedItem['package_weight'];
				$arProductFields['package_length'] = $arEncodedItem['package_length'];
				$arProductFields['package_height'] = $arEncodedItem['package_height'];
				$arProductFields['package_width'] = $arEncodedItem['package_width'];
				$arProductFields['shipping_preparation_time'] = $arEncodedItem['shipping_preparation_time'];
				$arProductFields['shipping_template_id'] = $arEncodedItem['shipping_template_id'];
				$arProductFields['service_template_id'] = $arEncodedItem['service_template_id'];
				// Add category attributes
				$arProductFields['category_attributes'] = [];
				foreach ($arEncodedItem as $key => $value) {
					if (strpos($key, 'categ_attribs: ') === 0 && $value) {
						$f_key = str_replace('categ_attribs: ', '', $key);
						$f_key = str_replace('_dot_', '.', $f_key);
						if (strpos($key, ' (multi)') !== false) {
							$f_key = str_replace(' (multi)', '', $f_key);
							$arProductFields['category_attributes'][$f_key]['value'][] = $value;
						}
						else {
							$arProductFields['category_attributes'][$f_key]['value'] = $value;
						}
					}
				}
				// Add sku attributes
				$arProductFields['sku_info_list'] = [];
				$sku_code = '';
				if (isset($arEncodedItem['_OFFER_PREPROCESS']) && !empty($arEncodedItem['_OFFER_PREPROCESS'])) {
					foreach ($arEncodedItem['_OFFER_PREPROCESS'] as $arOfferItem) {
						$arOfferEncodedItem = Json::decode($arOfferItem['DATA']);
						$sku_code = $arOfferEncodedItem['sku_code'];
						$this->addToLog('arOfferEncodedItem ' . print_r($arOfferEncodedItem, true), true);
						$arSkuItem = [
							'sku_code'       => $arOfferEncodedItem['sku_code'],
							'inventory'      => $arOfferEncodedItem['inventory'],
							'price'          => $arOfferEncodedItem['price'],
							'discount_price' => $arOfferEncodedItem['discount_price'],
						];
						$sku_attribs = [];
						foreach ($arOfferEncodedItem as $key => $value) {
							if (strpos($key, 'sku_attribs: ') === 0 && $value) {
								$f_key = str_replace('sku_attribs: ', '', $key);
								$f_key = str_replace('_dot_', '.', $f_key);
								$sku_attribs[$f_key] = $value;
							}
						}
						if (!empty($sku_attribs)) {
							$arSkuItem['sku_attributes'] = [];
							foreach ($sku_attribs as $name => $value) {
								$arSkuItem['sku_attributes'][$name]['value'] = $value;
							}
						}
						$arProductFields['sku_info_list'][] = $arSkuItem;
					}
				}
				else {
					$arProductFields['sku_info_list'][0] = [
//							'sku_attributes' => [
//								'Belt Length' => [
//									'value' => $vals_tmp[$i]
//								],
//								'Color' => [
//									'value' => $vals_tmp2[$j]
//								],
//							],
						'sku_code'       => $arEncodedItem['sku_code'],
						'inventory'      => $arEncodedItem['inventory'],
						'price'          => $arEncodedItem['price'],
						'discount_price' => $arEncodedItem['discount_price'],
					];
					$sku_code = $arEncodedItem['sku_code'];
					$sku_attribs = [];
					foreach ($arEncodedItem as $key => $value) {
						if (strpos($key, 'sku_attribs: ') === 0 && $value) {
							$f_key = str_replace('sku_attribs: ', '', $key);
							$f_key = str_replace('_dot_', '.', $f_key);
							$sku_attribs[$f_key] = $value;
						}
					}
					if ( ! empty($sku_attribs)) {
						$arProductFields['sku_info_list'][0]['sku_attributes_list'] = [];
						foreach ($sku_attribs as $name => $value) {
							$arProductFields['sku_info_list'][0]['sku_attributes_list'][$name]['value'] = $value;
//								$arProductFields['sku_info_list'][0]['sku_attributes_list'][$name]['value'] = $vals_tmp[$i];
						}
					}
				}
				// Add users additional fields
				if (!empty($arDataMore['ADDITIONAL_FIELDS'])) {
					$arProductFields['user_defined_attribute_list'] = [];
					foreach ($arDataMore['ADDITIONAL_FIELDS'] as $field_name => $field_value) {
						$arProductFields['user_defined_attribute_list'][] = [
							'attribute_value' => $field_value,
							'attribute_name' => $field_name,
						];
					}
				}
				$this->addToLog('arEncodedItem ' . print_r($arEncodedItem, true), true);
				$this->addToLog('arProductFields ' . print_r($arProductFields, true), true);
				$this->addToLog('arProductFields json ' . json_encode($arProductFields), true);

				$this->addToLog('sku_code ' . print_r($sku_code, true), true);
				$product_id = self::getAliProductIdBySku($sku_code);
				$this->addToLog('product_id ' . $product_id, true);
				if ( ! $product_id) {
					$result = $this->addAliProduct($arProductFields);
					$res_product_id = $result['result']['product_id'];
				} else {
					$result = $this->updateAliProduct($product_id, $arProductFields);
					$res_product_id = $result['product_id'];
				}
				$this->addToLog('result ' . print_r($result, true), true);
				// Save step result
				$this->setDataItemExported($arItem['ID']);
				$arSession['INDEX']++;
				// Product added successfully
				if ($res_product_id > 0) {
				} // Error
				else {
					$error_msg = 'Error:';
					$error_msg .= ' ' . $result['msg'] . ' [code ' . $result['code'] . ']';
					if ($result['sub_msg']) {
						$error_msg .= ' (' . $result['sub_msg'] . ' [sub code ' . $result['sub_code'] . '])';
					}
					$this->addToLog($error_msg);

					return Exporter::RESULT_ERROR;
				}
			}
			return Exporter::RESULT_CONTINUE;
		}
		return Exporter::RESULT_SUCCESS;
	}


	/**
	 * Check connection
	 */

	public function checkConnection($token, &$message) {
		$result = false;
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionOrderGetRequest;
		$param0 = new \OrderQuery;
		$param0->create_date_start = "2010-01-01 00:00:00";
		$param0->page_size = "1";
		$param0->current_page = "20";
		$req->setParam0(json_encode($param0));
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		if ($resp['code']) {
			$message = static::getMessage('CHECK_ERROR') . $resp['msg'] . ' [' . $resp['code'] . ']';
		}
		else {
			$result = true;
			$message = static::getMessage('CHECK_SUCCESS');
		}
		return $result;
	}

	/**
	 * Get products list
	 */

	public function getAliProductIdBySku($sku_code) {
		$product_id = false;
		$filter['product_status_type'] = '';
		$filter['sku_code'] = $sku_code;
		$list = self::getAliProductList($filter);
		if (!empty($list)) {
			$product_id = $list[0]['product_id'];
		}
		return $product_id;
	}

	/**
	 * Get products list
	 */

	public function getAliProductList($filter=[], $page=1, $page_size=50) {
		$list = [];
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionProductListGetRequest;
		$search_params = [
			'page_size' => $page_size,
			'current_page' => $page,
		];
		$req->setAeopAEProductListQuery(json_encode(array_merge($search_params, $filter)));
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		if ($resp['result']['aeop_a_e_product_display_d_t_o_list']['item_display_dto'][0]) {
			$list = $resp['result']['aeop_a_e_product_display_d_t_o_list']['item_display_dto'];
		}
		elseif ($resp['result']['aeop_a_e_product_display_d_t_o_list']['item_display_dto']) {
			$list[] = $resp['result']['aeop_a_e_product_display_d_t_o_list']['item_display_dto'];
		}
		return $list;
	}

	/**
	 * Add the product
	 */

	public function addAliProduct($fields) {
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionSchemaProductInstancePostRequest;
		$req->setProductInstanceRequest(json_encode($fields));
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		return $resp;
	}

	/**
	 * Update the product
	 */

	public function updateAliProduct($ali_product_id, $fields) {
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionSchemaProductFullUpdateRequest;
		$fields['aliexpress_product_id'] = $ali_product_id;
		$req->setSchemaFullUpdateRequest(json_encode($fields));
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		return $resp;
	}

	/**
	 * Get schema for the product
	 */

	public function getAliProductSchema($category_id) {
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionProductSchemaGetRequest;
		$req->setAliexpressCategoryId($category_id);
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		if ($resp['code']) {
			throw new \Exception($resp['msg'], $resp['code']);
		}
		$schema = json_decode($resp['result']['schema'], true);
		return $schema;
	}

	/**
	 * Get categories list
	 */

	public function getAliCategories($parent_category_id) {
		$list = [];
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionSellerCategoryTreeQueryRequest;
		$req->setCategoryId($parent_category_id);
		$req->setFilterNoPermission("true");
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		if ($resp['is_success'] && !empty($resp['children_category_list']['category_info'])) {
			$categ_list = ($resp['children_category_list']['category_info'][0]) ? $resp['children_category_list']['category_info'] : [$resp['children_category_list']['category_info']];
			foreach ($categ_list as $item) {
				$lang = json_decode($item['multi_language_names'], true);
				$name = $lang['ru'] ? $lang['ru'] : $lang['en'];
				if (!Helper::isUtf()){
					$name = Helper::convertEncoding($name, 'UTF-8', 'CP1251');
				}
				$list[] = [
					'id' => $item['children_category_id'],
					'name' => $name,
				];
			}
		}
		return $list;
	}

	/**
	 * Get categories lists
	 */

	public function getAliCategoryLists($arSelected) {
		$arCategLists = [];
		if (!is_array($arSelected) || empty($arSelected)) {
			$arSelected = [];
		}
		$arSList = AliexpressComApi::getAliCategories(0);
		foreach ($arSelected as $section_id) {
			$arSIDs = [];
			foreach ($arSList as $item) {
				$arSIDs[] = $item['id'];
			}
			if (in_array($section_id, $arSIDs)) {
				$arCategList = [];
				foreach ($arSList as $item) {
					$arCategItem = $item;
					if ($item['id'] == $section_id) {
						$arCategItem['selected'] = true;
					}
					$arCategList[] = $arCategItem;
				}
				$arCategLists[] = $arCategList;
				$arSList = $this->getAliCategories($section_id);
			}
		}
		if (!empty($arSList)) {
			$arCategList = [];
			foreach ($arSList as $item) {
				$arCategItem = $item;
				if ($item['id'] == $section_id) {
					$arCategItem['selected'] = true;
				}
				$arCategList[] = $arCategItem;
			}
			$arCategLists[] = $arCategList;
		}
		return $arCategLists;
	}

	/**
	 * Get sku attributes
	 */

	public function getCategorySkuAttribs($ali_category_id) {
		$list = [];
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionSkuAttributeQueryRequest;
		$query_sku_attribute_info_request = new \SkuAttributeInfoQueryRequest;
		$query_sku_attribute_info_request->aliexpress_category_id = $ali_category_id;
//		$query_sku_attribute_info_request->category_id="11112222";
		$req->setQuerySkuAttributeInfoRequest(json_encode($query_sku_attribute_info_request));
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		$resp_list = $resp['result']['supporting_sku_attribute_list']['supported_sku_attribute_dto'];
		$resp_list = ($resp_list[0]) ? $resp_list : [$resp_list];
		if (is_array($resp_list) && !empty($resp_list)) {
			$list = $resp_list;
		}
		return $list;
	}

	/**
	 * Get product info
	 */

	public function getAliProduct($product_id) {
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClientMod;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionProductInfoGetRequest;
		$req->setProductId($product_id);
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		return $resp;
	}

	
	/**
	 *	Handler for format file open link
	 */
	protected function onGetFileOpenLink(&$strFile, &$strTitle, $bSingle=false){
		return $this->getExtFileOpenLink('https://seller.aliexpress.ru/', 
			Helper::getMessage('ACRIT_EXP_FILE_OPEN_EXTERNAL'));
	}
	
}
