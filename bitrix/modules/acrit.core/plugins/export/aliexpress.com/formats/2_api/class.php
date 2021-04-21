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

class AliexpressComApi extends AliexpressCom {
	
	const DATE_UPDATED = '2021-02-18';
	const APP_KEY = '30433054';
	const SECRET_KEY = '17ec1f9a551165193fffcc8c3ffd614c';

	protected static $bSubclass = true;
	
	# General
	protected $arSupportedFormats = ['JSON'];
	protected $arSupportedEncoding = [self::UTF8];
	protected $bApi = true; // Выгружаем не в файл, а по АПИ
//	protected $arSupportedCurrencies = ['RUB'];
//	protected $bCategoryCustomName = true;
	protected $intExportPerStep = 50; // 50 товаров за 1 шаг
	
	# Basic settings
	protected $bAdditionalFields = true;
	protected $bCategoriesExport = true;


	/**
	 *	Settings
	 */
	protected function onUpShowSettings(&$arSettings){
		unset($arSettings['FILENAME']);
		$arSettings['TOKEN'] = $this->includeHtml(__DIR__ . '/include/settings/token.php');
		$arSettings['SECTIONS'] = $this->includeHtml(__DIR__ . '/include/settings/sections.php');
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
			case 'connection_check':
				$token = $arParams['POST']['token'];
				$res = $this->checkConnection($token, $message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
			case 'get_sections':
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
	 *	Get available fields for current plugin
	 */
	public function getUniversalFields($intProfileID, $intIBlockID){
		$arResult = [];
		// General fields
		$arResult['HEADER_GENERAL'] = [];
		$arResult['sku_code'] = ['FIELD' => 'sku_code', 'REQUIRED' => true];
		$arResult['locale'] = ['FIELD' => 'locale', 'REQUIRED' => true];
		$arResult['title'] = ['FIELD' => 'title', 'REQUIRED' => true];
		$arResult['description'] = ['FIELD' => 'description', 'REQUIRED' => true];
		$arResult['brand_name'] = ['FIELD' => 'brand_name', 'REQUIRED' => true];
		$arResult['images'] = ['FIELD' => 'images', 'MULTIPLE' => true, 'REQUIRED' => true]; //TODO multiple
		$arResult['product_units_type'] = ['FIELD' => 'product_units_type', 'REQUIRED' => true];
		$arResult['inventory_deduction_strategy'] = ['FIELD' => 'inventory_deduction_strategy', 'REQUIRED' => true];
		$arResult['inventory'] = ['FIELD' => 'inventory'];
		$arResult['price'] = ['FIELD' => 'price'];
		$arResult['discount_price'] = ['FIELD' => 'discount_price'];
		$arResult['package_weight'] = ['FIELD' => 'package_weight', 'REQUIRED' => true];
		$arResult['package_length'] = ['FIELD' => 'package_length', 'REQUIRED' => true];
		$arResult['package_height'] = ['FIELD' => 'package_height', 'REQUIRED' => true];
		$arResult['package_width'] = ['FIELD' => 'package_width', 'REQUIRED' => true];
		$arResult['shipping_preparation_time'] = ['FIELD' => 'shipping_preparation_time', 'REQUIRED' => true];
		$arResult['shipping_template_id'] = ['FIELD' => 'shipping_template_id', 'REQUIRED' => true];
		$arResult['service_template_id'] = ['FIELD' => 'service_template_id', 'REQUIRED' => true];
		// Schema info
		$category_id = $this->getAliCategoryId();
		$arSchema = self::getAliProductSchema($category_id);
		// Hint: product_units_type
		$arVariants = [];
		foreach ($arSchema['properties']['product_units_type']['oneOf'] as $arVariant) {
			$arVariants[] = $arVariant['const'] . ' (' . $arVariant['title'] . ')';
		}
		$strVariants = implode(', ', $arVariants);
		$arResult['product_units_type']['DESCRIPTION'] = static::getMessage('F_HINT_product_units_type', ['#VARIANTS#' => $strVariants]);
		// Hint: locale
		$arVariants = [];
		foreach ($arSchema['properties']['locale']['oneOf'] as $arVariant) {
			$arVariants[] = $arVariant['const'] . ' (' . $arVariant['title'] . ')';
		}
		$strVariants = implode(', ', $arVariants);
		$arResult['locale']['DESCRIPTION'] = static::getMessage('F_HINT_locale', ['#VARIANTS#' => $strVariants]);
		// Hint: shipping_template_id
		$arVariants = [];
		foreach ($arSchema['properties']['shipping_template_id']['oneOf'] as $arVariant) {
			$arVariants[] = $arVariant['const'] . ' (' . $arVariant['title'] . ')';
		}
		$strVariants = implode(', ', $arVariants);
		$arResult['shipping_template_id']['DESCRIPTION'] = static::getMessage('F_HINT_shipping_template_id', ['#VARIANTS#' => $strVariants]);
		// Hint: service_template_id
		$arVariants = [];
		foreach ($arSchema['properties']['service_template_id']['oneOf'] as $arVariant) {
			$arVariants[] = $arVariant['const'] . ' (' . $arVariant['title'] . ')';
		}
		$strVariants = implode(', ', $arVariants);
		$arResult['service_template_id']['DESCRIPTION'] = static::getMessage('F_HINT_service_template_id', ['#VARIANTS#' => $strVariants]);
		// Category fields
//		echo '<pre>'; print_r($arSchema); echo '</pre>';
//		$arExclProps = ['category_id'];
//		foreach ($arSchema['properties'] as $key => $arProp) {
//			if (!in_array($key, $arExclProps)) {
//				$required = in_array($key, $arSchema['required']) ? true : false;
//				$arResult[$key] = [
//					'FIELD'       => $key,
//					'REQUIRED'    => $required,
//					'DESCRIPTION' => $arProp['description'],
//					'NAME'        => $arProp['title']
//				];
//			}
//		}
//		echo '<pre>'; print_r($arSchema); echo '</pre>';
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
		$arItems = $this->getExportDataItems(null, null, $this->intExportPerStep);
		if ( ! empty($arItems)) {
			$arJsonItems = [];
			$arDataMore = [];
			foreach ($arItems as $arItem) {
				$arEncodedItem = Json::decode($arItem['DATA']);
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
				$arProductFields['sku_info_list'] = [
					'sku_code'       => $arEncodedItem['sku_code'],
					'inventory'      => $arEncodedItem['inventory'],
					'price'          => $arEncodedItem['price'],
					'discount_price' => $arEncodedItem['discount_price'],
//					'sku_attributes' => [
//						'Size' => [
//							'alias' => 'Uni',
//							'value' => '200003528',
//						],
//					],
				];
				$arProductFields['inventory_deduction_strategy'] = 'payment_success_deduct';
				$arProductFields['package_weight'] = $arEncodedItem['package_weight'];
				$arProductFields['package_length'] = $arEncodedItem['package_length'];
				$arProductFields['package_height'] = $arEncodedItem['package_height'];
				$arProductFields['package_width'] = $arEncodedItem['package_width'];
				$arProductFields['shipping_preparation_time'] = $arEncodedItem['shipping_preparation_time'];
				$arProductFields['shipping_template_id'] = $arEncodedItem['shipping_template_id'];
				$arProductFields['service_template_id'] = $arEncodedItem['service_template_id'];
				$arProductFields['category_attributes'] = [];
				$this->addToLog(print_r($arProductFields, true), true);

				$product_id = self::getAliProductIdBySku($arEncodedItem['sku_code']);
				if ( ! $product_id) {
					$result = $this->addAliProduct($arProductFields);
					$res_product_id = $result['result']['product_id'];
				} else {
					$result = $this->updateAliProduct($product_id, $arProductFields);
					$res_product_id = $result['product_id'];
				}
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
		$c = new \TopClient;
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
		$c = new \TopClient;
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
		$c = new \TopClient;
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
		$c = new \TopClient;
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
		$c = new \TopClient;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionProductSchemaGetRequest;
		$req->setAliexpressCategoryId($category_id);
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		$schema = json_decode($resp['result']['schema'], true);
		return $schema;
	}

	/**
	 * Get categories list
	 */

	public function getAliCategories($parent_category_id) {
		$list = [];
		$token = $this->arProfile['PARAMS']['TOKEN'];
		$c = new \TopClient;
		$c->appkey = self::APP_KEY;
		$c->secretKey = self::SECRET_KEY;
		$req = new \AliexpressSolutionSellerCategoryTreeQueryRequest;
		$req->setCategoryId($parent_category_id);
		$req->setFilterNoPermission("true");
		$resp = $c->execute($req, $token);
		$resp = json_decode(json_encode($resp), true);
		if ($resp['is_success'] && !empty($resp['children_category_list']['category_info'])) {
			foreach ($resp['children_category_list']['category_info'] as $item) {
				$lang = json_decode($item['multi_language_names'], true);
				$list[] = [
					'id' => $item['children_category_id'],
					'name' => $lang['ru'] ? $lang['ru'] : $lang['en'],
				];
			}
		}
		return $list;
	}
}
