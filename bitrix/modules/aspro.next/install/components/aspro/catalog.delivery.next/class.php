<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Result;
use CNextCache as Cache;

if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
Loc::loadMessages(__FILE__);

class CAsproCatalogDeliveryNext extends CBitrixComponent{
	const MODULE_ID = 'aspro.next';

	protected $action;
	protected $isPreview;
	protected $isAjax;
	protected $userId;
	protected $sessionVar;
	protected $order;
	protected $lastOrder;
	protected $arErrors = array();

	public function hasError(){
		return boolval($this->arErrors);
	}

	protected function setError($message){
		if($message instanceof Result){
			$errors = $message->getErrorMessages();
		}
		else{
			$errors = array($message);
		}

		foreach($errors as $error){
			if(!in_array($error, $this->arErrors, true)){
				$this->arErrors[] = $error;
			}
		}

		return false;
	}

	protected function includeModules(){
		if(!\Bitrix\Main\Loader::includeModule('sale')){
			$this->setError(Loc::getMessage('CD_MODULE_SALE_NOT_INSTALL'));
		}

		if(!\Bitrix\Main\Loader::includeModule('currency')){
			$this->setError(Loc::getMessage('CD_MODULE_CURRENCY_NOT_INSTALL'));
		}

		if(!\Bitrix\Main\Loader::includeModule('catalog')){
			$this->setError(Loc::getMessage('CD_MODULE_CATALOG_NOT_INSTALL'));
		}
	}

	public function onPrepareComponentParams($arParams){
		global $APPLICATION;

		if(isset($arParams['CUSTOM_SITE_ID'])){
			$this->setSiteId($arParams['CUSTOM_SITE_ID']);
		}

		if(isset($arParams['CUSTOM_LANGUAGE_ID'])){
			$this->setLanguageId($arParams['CUSTOM_LANGUAGE_ID']);
		}

		$siteId = $this->getSiteId();
		$languageId = $this->getLanguageId();

		// BASE PARAMETERS
		$arParams['SET_PAGE_TITLE'] = !isset($arParams['SET_PAGE_TITLE']) || $arParams['SET_PAGE_TITLE'] !== 'N' ? 'Y' : 'N';
		$arParams['DELIVERY_NO_SESSION'] = !isset($arParams['DELIVERY_NO_SESSION']) || $arParams['DELIVERY_NO_SESSION'] !== 'N' ? 'Y' : 'N';
		$arParams['DELIVERY_WITHOUT_PAY_SYSTEM'] = !isset($arParams['DELIVERY_WITHOUT_PAY_SYSTEM']) || $arParams['DELIVERY_WITHOUT_PAY_SYSTEM'] !== 'N' ? 'Y' : 'N';
		$arParams['PAY_FROM_ACCOUNT'] = isset($arParams['PAY_FROM_ACCOUNT']) && $arParams['PAY_FROM_ACCOUNT'] === 'Y' ? 'Y' : 'N';
		$arParams['SPOT_LOCATION_BY_GEOIP'] = !isset($arParams['SPOT_LOCATION_BY_GEOIP']) || $arParams['SPOT_LOCATION_BY_GEOIP'] !== 'N' ? 'Y' : 'N';
		$arParams['USE_LAST_ORDER_DATA'] = !isset($arParams['USE_LAST_ORDER_DATA']) || $arParams['USE_LAST_ORDER_DATA'] !== 'N' ? 'Y' : 'N';
		$arParams['USE_PROFILE_LOCATION'] = isset($arParams['USE_PROFILE_LOCATION']) && $arParams['USE_PROFILE_LOCATION'] === 'Y' ? 'Y' : 'N';
		$arParams['SAVE_IN_SESSION'] = !isset($arParams['SAVE_IN_SESSION']) || $arParams['SAVE_IN_SESSION'] !== 'N' ? 'Y' : 'N';

		// VISUAL PARAMETERS
		$arParams['SHOW_LOCATION_SOURCE'] = isset($arParams['SHOW_LOCATION_SOURCE']) && $arParams['SHOW_LOCATION_SOURCE'] === 'Y' ? 'Y' : 'N';
		if(isset($arParams['CHANGEABLE_FIELDS']) && is_array($arParams['CHANGEABLE_FIELDS'])){
			$arTmp = array_intersect(array('LOCATION', 'QUANTITY', 'PERSON_TYPE', 'PAY_SYSTEM', 'ADD_BASKET'), $arParams['CHANGEABLE_FIELDS']);
			$arParams['CHANGEABLE_FIELDS'] = array_values($arTmp);
		}
		else{
			$arParams['CHANGEABLE_FIELDS'] = array('LOCATION', 'QUANTITY', 'ADD_BASKET');
		}
		$arParams['SHOW_DELIVERY_PARENT_NAMES'] = !isset($arParams['SHOW_DELIVERY_PARENT_NAMES']) || $arParams['SHOW_DELIVERY_PARENT_NAMES'] !== 'N' ? 'Y' : 'N';
		$arParams['SHOW_MESSAGE_ON_CALCULATE_ERROR'] = !isset($arParams['SHOW_MESSAGE_ON_CALCULATE_ERROR']) || $arParams['SHOW_MESSAGE_ON_CALCULATE_ERROR'] !== 'N' ? 'Y' : 'N';
		if($arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'] = $arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'] ? $arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'] : array()){
			$arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'] = is_array($arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID']) ? $arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'] : array($arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID']);
			foreach($arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'] as $i => $deliveryId){
				if(intval($deliveryId) > 0){
					$arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'][$i] = intval($deliveryId);
				}
				else{
					unset($arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'][$i]);
				}
			}

			$arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID'] = array_values($arParams['PREVIEW_SHOW_DELIVERY_PARENT_ID']);
		}

		// DEFAULT PARAMETERS
		$arParams['PRODUCT_ID'] = isset($arParams['PRODUCT_ID']) ? intval(trim($arParams['PRODUCT_ID'])) : 0;
		$arParams['PRODUCT_QUANTITY'] = isset($arParams['PRODUCT_QUANTITY']) ? floatval(trim($arParams['PRODUCT_QUANTITY'])) : 1;
		$arParams['LOCATION_CODE'] = isset($arParams['LOCATION_CODE']) ? strval(trim($arParams['LOCATION_CODE'])) : '';
		$arParams['USER_PROFILE_ID'] = isset($arParams['USER_PROFILE_ID']) ? intval(trim($arParams['USER_PROFILE_ID'])) : 0;
		$arParams['PERSON_TYPE_ID'] = isset($arParams['PERSON_TYPE_ID']) ? intval(trim($arParams['PERSON_TYPE_ID'])) : 0;
		$arParams['PAY_SYSTEM_ID'] = isset($arParams['PAY_SYSTEM_ID']) ? intval(trim($arParams['PAY_SYSTEM_ID'])): 0;
		if($arParams['DELIVERY_ID'] = $arParams['DELIVERY_ID'] ? $arParams['DELIVERY_ID'] : array()){
			$arParams['DELIVERY_ID'] = is_array($arParams['DELIVERY_ID']) ? $arParams['DELIVERY_ID'] : array($arParams['DELIVERY_ID']);
			foreach($arParams['DELIVERY_ID'] as $i => $deliveryId){
				if(intval($deliveryId) > 0){
					$arParams['DELIVERY_ID'][$i] = intval($deliveryId);
				}
				else{
					unset($arParams['DELIVERY_ID'][$i]);
				}
			}

			$arParams['DELIVERY_ID'] = array_values($arParams['DELIVERY_ID']);
		}
		$arParams['ADD_BASKET'] = isset($arParams['ADD_BASKET']) && $arParams['ADD_BASKET'] === 'Y' ? 'Y' : 'N';
		$arParams['BUYER_STORE_ID'] = isset($arParams['BUYER_STORE_ID']) ? intval(trim($arParams['BUYER_STORE_ID'])) : 0;

		// MESSAGES PARAMETERS
		$arParams['USE_CUSTOM_MESSAGES'] = isset($arParams['USE_CUSTOM_MESSAGES']) && $arParams['USE_CUSTOM_MESSAGES'] === 'Y' ? 'Y' : 'N';
		if($arParams['USE_CUSTOM_MESSAGES'] === 'Y'){
			$arParams['MESS_DELIVERY_PAGE_TITLE'] = isset($arParams['MESS_DELIVERY_PAGE_TITLE']) && strlen(trim($arParams['MESS_DELIVERY_PAGE_TITLE'])) ? trim($arParams['MESS_DELIVERY_PAGE_TITLE']) : Loc::getMessage('CD_MESS_DELIVERY_PAGE_TITLE_DEFAULT');
			$arParams['MESS_DELIVERY_DETAIL_TITLE'] = isset($arParams['MESS_DELIVERY_DETAIL_TITLE']) && strlen(trim($arParams['MESS_DELIVERY_DETAIL_TITLE'])) ? trim($arParams['MESS_DELIVERY_DETAIL_TITLE']) : Loc::getMessage('CD_MESS_DELIVERY_DETAIL_TITLE_DEFAULT');
			$arParams['MESS_DELIVERY_PREVIEW_TITLE'] = isset($arParams['MESS_DELIVERY_PREVIEW_TITLE']) && strlen(trim($arParams['MESS_DELIVERY_PREVIEW_TITLE'])) ? trim($arParams['MESS_DELIVERY_PREVIEW_TITLE']) : Loc::getMessage('CD_MESS_DELIVERY_PREVIEW_TITLE_DEFAULT');
			$arParams['MESS_DELIVERY_PREVIEW_MORE_TITLE'] = isset($arParams['MESS_DELIVERY_PREVIEW_MORE_TITLE']) && strlen(trim($arParams['MESS_DELIVERY_PREVIEW_MORE_TITLE'])) ? trim($arParams['MESS_DELIVERY_PREVIEW_MORE_TITLE']) : Loc::getMessage('CD_MESS_DELIVERY_PREVIEW_MORE_TITLE_DEFAULT');
			$arParams['MESS_DELIVERY_CALC_ERROR_TITLE'] = isset($arParams['MESS_DELIVERY_CALC_ERROR_TITLE']) && strlen(trim($arParams['MESS_DELIVERY_CALC_ERROR_TITLE'])) ? trim($arParams['MESS_DELIVERY_CALC_ERROR_TITLE']) : Loc::getMessage('CD_MESS_DELIVERY_CALC_ERROR_TITLE_DEFAULT');
			$arParams['MESS_DELIVERY_CALC_ERROR_TEXT'] = isset($arParams['MESS_DELIVERY_CALC_ERROR_TEXT']) && strlen(trim($arParams['MESS_DELIVERY_CALC_ERROR_TEXT'])) ? trim($arParams['MESS_DELIVERY_CALC_ERROR_TEXT']) : Loc::getMessage('CD_MESS_DELIVERY_CALC_ERROR_TEXT_DEFAULT');
		}

		$signer = new \Bitrix\Main\Security\Sign\Signer;
		$signedParams = $signer->sign(base64_encode(serialize($arParams)), 'catalog.delivery.next');

		$this->isAjax = $this->request->isPost() && $this->request['is_ajax_post'] === 'Y';
		$this->isPreview = $this->request->isPost() && $this->request['is_preview'] === 'Y';
		$this->sessionVar = $this->{'__name'};

		$this->arResult = array(
			'RAND' => $this->isPreview ? $this->request['index'] : randString(5),
			'PRODUCT_ID' => $arParams['PRODUCT_ID'],
			'PRODUCT_QUANTITY' => $arParams['PRODUCT_QUANTITY'],
			'ADD_BASKET' => $arParams['ADD_BASKET'],
			'PERSON_TYPE_ID' => $arParams['PERSON_TYPE_ID'],
			'PAY_SYSTEM_ID' => $arParams['PAY_SYSTEM_ID'],
			'DELIVERY_ID' => $arParams['DELIVERY_ID'],
			'BUYER_STORE_ID' => $arParams['BUYER_STORE_ID'],
			'USER_PROFILE_ID' => $arParams['USER_PROFILE_ID'],
			'PRODUCT' => array(),
			'LOCATION' => array(),
			'LOCATION_SOURCE' => '',
			'USER_PROFILES' => array(),
			'PERSON_TYPE' => array(),
			'PAY_SYSTEM' => array(),
			'DELIVERY' => array(),
			'ACTION_URL' => $_SERVER['SCRIPT_NAME'],
			'AJAX_URL' => $this->getPath().'/ajax.php',
			'IS_AJAX' => $this->isAjax ? 'Y' : 'N',
			'IS_PREVIEW' => $this->isPreview ? 'Y' : 'N',
			'SIGNED_PARAMS' => $signedParams,
			'SITE_ID' => $siteId,
			'LANGUAGE_ID' => $languageId,
			'ERROR' => array(),
			'MESSAGES' => array(
				'PAGE_TITLE' => $arParams['USE_CUSTOM_MESSAGES'] === 'Y' ? $arParams['MESS_DELIVERY_PAGE_TITLE'] : Loc::getMessage('CD_MESS_DELIVERY_PAGE_TITLE_DEFAULT'),
				'DETAIL_TITLE' => $arParams['USE_CUSTOM_MESSAGES'] === 'Y' ? $arParams['MESS_DELIVERY_DETAIL_TITLE'] : Loc::getMessage('CD_MESS_DELIVERY_DETAIL_TITLE_DEFAULT'),
				'PREVIEW_TITLE' => $arParams['USE_CUSTOM_MESSAGES'] === 'Y' ? $arParams['MESS_DELIVERY_PREVIEW_TITLE'] : Loc::getMessage('CD_MESS_DELIVERY_PREVIEW_TITLE_DEFAULT'),
				'PREVIEW_MORE_TITLE' => $arParams['USE_CUSTOM_MESSAGES'] === 'Y' ? $arParams['MESS_DELIVERY_PREVIEW_MORE_TITLE'] : Loc::getMessage('CD_MESS_DELIVERY_PREVIEW_MORE_TITLE_DEFAULT'),
				'CALC_ERROR_TITLE' => $arParams['USE_CUSTOM_MESSAGES'] === 'Y' ? $arParams['MESS_DELIVERY_CALC_ERROR_TITLE'] : Loc::getMessage('CD_MESS_DELIVERY_CALC_ERROR_TITLE_DEFAULT'),
				'CALC_ERROR_TEXT' => $arParams['USE_CUSTOM_MESSAGES'] === 'Y' ? $arParams['MESS_DELIVERY_CALC_ERROR_TEXT'] : Loc::getMessage('CD_MESS_DELIVERY_CALC_ERROR_TEXT_DEFAULT'),
			),
		);

		if(strlen($arParams['LOCATION_CODE'])){
			if($this->arResult['LOCATION'] = self::getLocationByCode($arParams['LOCATION_CODE'], $languageId)){
				$this->arResult['LOCATION_SOURCE'] = 'params';
			}
		}

		$this->arErrors =& $this->arResult['ERROR'];

		return $arParams;
	}

	protected function prepareAction(){
		$action = 'processOrder';

		return $action;
	}

	protected function actionExists($action){
		return is_callable(array($this, $action.'Action'));
	}

	protected function doAction($action){
		if($this->actionExists($action)){
			$this->{$action.'Action'}();
		}
	}

	public function executeComponent(){
		if($this->isAjax){
			$GLOBALS['APPLICATION']->RestartBuffer();
		}

		$this->action = $this->prepareAction();
		Sale\Compatible\DiscountCompatibility::stopUsageCompatible();
		$this->doAction($this->action);
		Sale\Compatible\DiscountCompatibility::revertUsageCompatible();

		if($this->isPreview){
			$this->includeComponentTemplate('preview');
		}
		else{
			$this->includeComponentTemplate();
		}

		if($this->isAjax){
			$GLOBALS['APPLICATION']->FinalActions();
			die();
		}
		else{
			if($this->arParams['SET_PAGE_TITLE'] === 'Y'){
				$GLOBALS['APPLICATION']->SetTitle($this->arResult['MESSAGES']['PAGE_TITLE']);
			}
		}
	}

	protected function makeAjaxResult(){
		if($this->isAjax){
			$request =& $this->request;

			if(
				$this->arParams['SAVE_IN_SESSION'] === 'Y' &&
				isset($_SESSION[$this->sessionVar]) &&
				is_array($_SESSION[$this->sessionVar]) &&
				$_SESSION[$this->sessionVar] &&
				$_SESSION[$this->sessionVar]['USER_PARAMS']
			){
				$arSessionUserParams = $_SESSION[$this->sessionVar]['USER_PARAMS'];
			}
			else{
				$arSessionUserParams = array();
			}

			$productId = $request->get('PRODUCT');
			if(intval($productId) > 0){
				$this->arResult['PRODUCT_ID'] = intval($productId);
			}

			if(in_array('QUANTITY', $this->arParams['CHANGEABLE_FIELDS'])){
				$productQuantity = $request->get('QUANTITY');
				if(floatval($productQuantity) > 0){
					$this->arResult['PRODUCT_QUANTITY'] = floatval($productQuantity);
				}
			}

			if(in_array('ADD_BASKET', $this->arParams['CHANGEABLE_FIELDS'])){
				$addBasket = $request->get('ADD_BASKET');
				if(!empty($addBasket)){
					$this->arResult['ADD_BASKET'] = $addBasket === 'Y' ? 'Y' : 'N';
				}
				else{
					$this->arResult['ADD_BASKET'] = 'N';
				}

				$arSessionUserParams['ADD_BASKET'] = $this->arResult['ADD_BASKET'];
			}

			if(in_array('LOCATION', $this->arParams['CHANGEABLE_FIELDS'])){
				$isLocationChanged = $request->get('LOCATION_CHANGED');
				if($isLocationChanged === 'Y'){
					$locationCode = trim($request->get('LOCATION'));
					if(strlen($locationCode)){
						if($this->arResult['LOCATION'] = self::getLocationByCode($locationCode, $this->getLanguageId())){
							$this->arResult['LOCATION_SOURCE'] = 'request';
						}
					}

					$arSessionUserParams['LOCATION'] = $this->arResult['LOCATION'];
				}
			}

			if(in_array('PERSON_TYPE', $this->arParams['CHANGEABLE_FIELDS'])){
				$personTypeId = $request->get('PERSON_TYPE');
				if(intval($personTypeId) > 0){
					$this->arResult['PERSON_TYPE_ID'] = intval($personTypeId);
				}

				$arSessionUserParams['PERSON_TYPE_ID'] = $this->arResult['PERSON_TYPE_ID'];
			}

			if(in_array('PAY_SYSTEM', $this->arParams['CHANGEABLE_FIELDS'])){
				$paySystemId = $request->get('PAY_SYSTEM');
				if(intval($paySystemId) > 0 || (!$paySystemId && $this->arParams['DELIVERY_WITHOUT_PAY_SYSTEM'] === 'Y')){
					$this->arResult['PAY_SYSTEM_ID'] = intval($paySystemId);
				}

				$arSessionUserParams['PAY_SYSTEM_ID'] = $this->arResult['PAY_SYSTEM_ID'];
			}

			$arDeliveryId = $request->get('DELIVERY');
			if($arDeliveryId = is_array($arDeliveryId) ? $arDeliveryId : array($arDeliveryId)){
				foreach($arDeliveryId as $i => $deliveryId){
					if(intval($deliveryId) > 0){
						$arDeliveryId[$i] = intval($deliveryId);
					}
					else{
						unset($arDeliveryId[$i]);
					}
				}

				$this->arResult['DELIVERY_ID'] = array_values($arDeliveryId);
			}
			$arSessionUserParams['DELIVERY_ID'] = $this->arResult['DELIVERY_ID'];

			$buyerStore = $request->get('BUYER_STORE');
			if(intval($buyerStore) > 0){
				$this->arResult['BUYER_STORE_ID'] = intval($buyerStore);
			}
			$arSessionUserParams['BUYER_STORE_ID'] = $this->arResult['BUYER_STORE_ID'];

			if($this->arParams['SAVE_IN_SESSION'] === 'Y'){
				$_SESSION[$this->sessionVar]['USER_PARAMS'] = $arSessionUserParams;
			}
			else{
				unset($_SESSION[$this->sessionVar]['USER_PARAMS']);
			}

			$calculate = $request->get('CALCULATE');
			if(empty($calculate) || $calculate === 'N'){
				die();
			}
		}
	}

	protected function processOrderAction(){
		$this->includeModules();

		if($this->initUserResult()){
			if($this->initProduct()){
				$this->createOrder();
			}
		}
	}

	protected function createOrder(){
		Sale\DiscountCouponsManager::init(Sale\DiscountCouponsManager::MODE_CLIENT, array('userId' => $this->userId));

		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		$orderClassName = $registry->getOrderClassName();
		$this->order = $orderClassName::create($this->getSiteId(), $this->userId);

		$this->initPersonType();
		$this->initBasket();
		$this->initProperties();
		$shipment = $this->initShipment();

		$this->initPaymentBefore();
		$this->initDelivery($shipment);
		if($this->getD2P() === 'd2p'){
			$this->initPaymentAfter();
		}
	}

	protected function getD2P(){
		return $this->arParams['DELIVERY_WITHOUT_PAY_SYSTEM'] === 'Y' && (!in_array('PAY_SYSTEM', $this->arParams['CHANGEABLE_FIELDS']) || !$this->arResult['PAY_SYSTEM_ID']) ? 'd2p' : 'p2d';
	}

	public static function getLocationByCode($locationCode, $languageId = false){
		$languageId = $languageId ? $languageId : $this->getLanguageId();

		$arLocation = \Bitrix\Sale\Location\LocationTable::getByCode(
			$locationCode,
			array(
				'filter' => array(
					'=NAME.LANGUAGE_ID' => $languageId,
				),
				'select' => array(
					'*',
					'IS_PARENT',
					'TYPE_CODE' => 'TYPE.CODE',
					'LOCATION_NAME' => 'NAME.NAME',
				)
			)
		)->fetch();

		if($arLocation){
			$arLocation['ZIP'] = '';
			$rsZipList = \CSaleLocation::GetLocationZIP($arLocation['ID']);
			if($arZip = $rsZipList->Fetch()){
				$arLocation['ZIP'] = $arZip['ZIP'];
			}

			return $arLocation;
		}

        return array();
	}

	protected function initGeoIp(){
		if($this->arParams['SPOT_LOCATION_BY_GEOIP'] === 'Y'){
			if($ipAddress = \Bitrix\Main\Service\GeoIp\Manager::getRealIp()){
				$languageId = $this->getLanguageId();
				if(strlen($locationCode = Cache::SaleGeoIp_GetLocationCode($ipAddress, $languageId))){
					if($this->arResult['LOCATION'] = self::getLocationByCode($locationCode, $languageId)){
						$this->arResult['LOCATION_SOURCE'] = 'geoIp';
					}
				}
			}
		}
	}

	protected function getLastOrderData(){
		$lastOrderData = array();

		$registry = Sale\Registry::getInstance(Sale\Registry::REGISTRY_TYPE_ORDER);
		$orderClassName = $registry->getOrderClassName();

		$filter = array(
			'order' => array(
				'ID' => 'DESC',
			),
			'filter' => array(
				'USER_ID' => $this->userId,
				'LID' => $this->getSiteId(),
			),
			'select' => array(
				'ID',
			),
			'limit' => 1,
		);

		if($arOrder = $orderClassName::getList($filter)->fetch()){
			$this->lastOrder = $orderClassName::load($arOrder['ID']);
			$lastOrderData['PERSON_TYPE_ID'] = $this->lastOrder->getPersonTypeId();

			if($payment = $this->getExternalPayment($this->lastOrder)){
				$lastOrderData['PAY_SYSTEM_ID'] = $payment->getPaymentSystemId();
			}

			if($shipment = $this->getCurrentShipment($this->lastOrder)){
				$lastOrderData['DELIVERY_ID'] = $shipment->getDeliveryId();
				$lastOrderData['BUYER_STORE_ID'] = $shipment->getStoreId();
			}

			$propertyCollection = $this->lastOrder->getPropertyCollection();
	        if($propertyLocation = $propertyCollection->getDeliveryLocation()){
        		if(strlen($locationCode = $propertyLocation->getValue())){
	        		$lastOrderData['LOCATION'] = self::getLocationByCode($locationCode, $this->getLanguageId());
        		}

	        	if($propertyZip = $propertyCollection->getDeliveryLocationZip()){
	        		$locationZip = $propertyZip->getValue();
	        		$lastOrderData['LOCATION']['ZIP'] = $locationZip;
	        	}
	        }
		}

		return $lastOrderData;
	}

	protected function initLastOrderData(){
		if($this->arParams['USE_LAST_ORDER_DATA'] === 'Y' && $GLOBALS['USER']->IsAuthorized()){
			$lastOrderData = $this->getLastOrderData();

			if(!empty($lastOrderData)){
				if(!empty($lastOrderData['PERSON_TYPE_ID'])){
					$this->arResult['PERSON_TYPE_ID'] = $lastOrderData['PERSON_TYPE_ID'];
				}

				if(!empty($lastOrderData['PAY_SYSTEM_ID'])){
					if(!($this->arParams['DELIVERY_WITHOUT_PAY_SYSTEM'] === 'Y' && !in_array('PAY_SYSTEM', $this->arParams['CHANGEABLE_FIELDS']))){
						$this->arResult['PAY_SYSTEM_ID'] = $lastOrderData['PAY_SYSTEM_ID'];
					}
				}

				if(!empty($lastOrderData['DELIVERY_ID'])){
					$this->arResult['DELIVERY_ID'] = array($lastOrderData['DELIVERY_ID']);
				}

				if(!empty($lastOrderData['BUYER_STORE'])){
					$this->arResult['BUYER_STORE_ID'] = $lastOrderData['BUYER_STORE'];
				}

				if(!empty($lastOrderData['LOCATION'])){
					if($this->arResult['LOCATION'] = $lastOrderData['LOCATION']){
						$this->arResult['LOCATION_SOURCE'] = 'lastOrder';
					}
				}
			}
		}
	}

	protected function initSessionUserParams(){
		if(
			$this->arParams['SAVE_IN_SESSION'] === 'Y' &&
			isset($_SESSION[$this->sessionVar]) &&
			is_array($_SESSION[$this->sessionVar]) &&
			$_SESSION[$this->sessionVar] &&
			$_SESSION[$this->sessionVar]['USER_PARAMS']
		){
			$arSessionUserParams = $_SESSION[$this->sessionVar]['USER_PARAMS'];

			if(in_array('ADD_BASKET', $this->arParams['CHANGEABLE_FIELDS'])){
				if(!empty($arSessionUserParams['ADD_BASKET'])){
					$this->arResult['ADD_BASKET'] = $arSessionUserParams['ADD_BASKET'];
				}
			}

			if(in_array('LOCATION', $this->arParams['CHANGEABLE_FIELDS'])){
				if(!empty($arSessionUserParams['LOCATION'])){
					if($this->arResult['LOCATION'] = $arSessionUserParams['LOCATION']){
						$this->arResult['LOCATION_SOURCE'] = 'session';
					}
				}
			}

			if(in_array('PERSON_TYPE', $this->arParams['CHANGEABLE_FIELDS'])){
				if(!empty($arSessionUserParams['PERSON_TYPE_ID'])){
					$this->arResult['PERSON_TYPE_ID'] = $arSessionUserParams['PERSON_TYPE_ID'];
				}
			}

			if(in_array('PAY_SYSTEM', $this->arParams['CHANGEABLE_FIELDS'])){
				if(isset($arSessionUserParams['PAY_SYSTEM_ID'])){
					$this->arResult['PAY_SYSTEM_ID'] = $arSessionUserParams['PAY_SYSTEM_ID'];
				}
			}

			if(isset($arSessionUserParams['DELIVERY_ID'])){
				$this->arResult['DELIVERY_ID'] = $arSessionUserParams['DELIVERY_ID'];
			}

			if(!empty($arSessionUserParams['BUYER_STORE'])){
				$this->arResult['BUYER_STORE_ID'] = $arSessionUserParams['BUYER_STORE'];
			}
		}
	}

	protected function initUserProfiles(){
		$userProfileId = intval($this->arResult['USER_PROFILE_ID']);

		$dbUserProfiles = CSaleOrderUserProps::GetList(
			array(
				'DATE_UPDATE' => 'DESC'
			),
			array(
				'PERSON_TYPE_ID' => $this->arResult['PERSON_TYPE_ID'],
				'USER_ID' => $this->userId,
			)
		);
		while($arUserProfile = $dbUserProfiles->GetNext()){
			$this->arResult['USER_PROFILES'][$arUserProfile['ID']] = $arUserProfile;
		}

		foreach($this->arResult['USER_PROFILES'] as &$arUserProfile){
			if($userProfileId == $arUserProfile['ID'] || !in_array($userProfileId, $this->arResult['USER_PROFILES'])){
				$userProfileId = $arUserProfile['ID'];
				$this->arResult['USER_PROFILE_ID'] = $arUserProfile['ID'];
				$arUserProfile['CHECKED'] = 'Y';
			}
		}
		unset($arUserProfile);
	}

	protected function initProduct(){
		if(!$this->arResult['PRODUCT_ID']){
			return $this->setError(Loc::getMessage('CD_ERROR_PRODUCT_ID'));
		}

		$dbRes = \CIBlockElement::GetList(
			array(),
			array(
				'ID' => $this->arResult['PRODUCT_ID'],
			),
			false,
			false,
			array(
				'ID',
				'IBLOCK_ID',
				'IBLOCK_SECTION_ID',
				'PREVIEW_PICTURE',
				'NAME',
				'DETAIL_PAGE_URL',
			)
		);
		if($arProduct = $dbRes->GetNext()){
			if($arProductCatalog = \CCatalogProduct::GetByID($this->arResult['PRODUCT_ID'])){
				$arProduct['TYPE'] = $arProductCatalog['TYPE'];
				$arProduct['LENGTH'] = $arProductCatalog['LENGTH'];
				$arProduct['WIDTH'] = $arProductCatalog['WIDTH'];
				$arProduct['HEIGHT'] = $arProductCatalog['HEIGHT'];

				if($arProductMeasureRatio = \Bitrix\Catalog\ProductTable::getCurrentRatioWithMeasure($this->arResult['PRODUCT_ID'])){
					$arProduct = array_merge($arProduct, $arProductMeasureRatio[$this->arResult['PRODUCT_ID']]);
				}
			}

			if(!$arProduct['TYPE']){
				$arProduct['TYPE'] = 1;
			}

			if(!$arProduct['RATIO']){
				$arProduct['RATIO'] = 1;
			}
			$arProduct['RATIO_IS_FLOAT'] = is_double($arProduct['RATIO']);

			$this->arResult['PRODUCT'] = $arProduct;

			if($arProduct['RATIO_IS_FLOAT']){
				$this->arResult['PRODUCT_QUANTITY'] = floatval($this->arResult['PRODUCT_QUANTITY']);
			}
			else{
				$this->arResult['PRODUCT_QUANTITY'] = intval($this->arResult['PRODUCT_QUANTITY']);
			}
			if($this->arResult['PRODUCT_QUANTITY'] > $arProduct['RATIO']){
				$diff = fmod($this->arResult['PRODUCT_QUANTITY'], $arProduct['RATIO']);
				if($diff > 0){
					$this->arResult['PRODUCT_QUANTITY'] -= $diff;
				}
			}
			else{
				$this->arResult['PRODUCT_QUANTITY'] = $arProduct['RATIO'];
			}

			foreach(GetModuleEvents(self::MODULE_ID, 'OnCatalogDeliveryComponentInitProduct', true) as $arEvent){
				ExecuteModuleEventEx($arEvent, array(&$this->arResult, &$this->arParams, $this->request));
			}
		}
		else{
			return $this->setError(Loc::getMessage('CD_ERROR_PRODUCT'));
		}

		return true;
	}

	protected function initUserResult(){
		$this->userId = $GLOBALS['USER']->GetID();
		if(!$this->userId){
			$this->userId = \CSaleUser::GetAnonymousUserID();
		}

		// create fuser if is not exists
		Sale\Fuser::getId();

		$this->initGeoIp();

		$this->initLastOrderData();

		$this->initSessionUserParams();

		if($this->isAjax){
			if($this->arParams['DELIVERY_NO_SESSION'] !== 'Y' || check_bitrix_sessid()){
				$this->makeAjaxResult();
			}
			else{
				return $this->setError(Loc::getMessage('CD_SESSID_ERROR'));
			}
		}

		$this->initUserProfiles();

		foreach(GetModuleEvents(self::MODULE_ID, 'OnCatalogDeliveryComponentInitUserResult', true) as $arEvent){
			ExecuteModuleEventEx($arEvent, array(&$this->arResult, &$this->arParams, $this->request));
		}

		return true;
	}

	protected function initPersonType(){
		$personTypeId = intval($this->arResult['PERSON_TYPE_ID']);
		$personTypes = \Bitrix\Sale\PersonType::load($this->getSiteId());

		if($personTypes){
			foreach($personTypes as $personType){
				if($personTypeId === intval($personType['ID']) || !array_key_exists($personTypeId, $personTypes)){
					$personTypeId = intval($personType['ID']);
					$this->order->setPersonTypeId($personTypeId);
					$this->arResult['PERSON_TYPE_ID'] = $personTypeId;
					$personType['CHECKED'] = 'Y';
				}

				$this->arResult['PERSON_TYPE'][$personType['ID']] = $personType;
			}
		}
		else{
			return $this->setError(Loc::getMessage('CD_ERROR_PERSON_TYPE'));
		}

		return $this->arResult['PERSON_TYPE'];
	}

	protected function initBasket(){
		$cntBasketItems = 0;
		$siteId = false; // !!! not $this->getSiteId(); !!!

		$basket = Sale\Basket::create($siteId);
		$basketItem = $basket->createItem('catalog', $this->arResult['PRODUCT_ID']);
		$basketItem->setFields(array(
	        'QUANTITY' => $this->arResult['PRODUCT_QUANTITY'],
	        'CURRENCY' => \Bitrix\Currency\CurrencyManager::getBaseCurrency(),
	        'LID' => $siteId,
	        'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider',
	    ));
	    $basket->save();
	    ++$cntBasketItems;

		if($this->arResult['ADD_BASKET'] === 'Y'){
			$basketStorage = Sale\Basket\Storage::getInstance(Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
			$currentBasket = $basketStorage->getBasket();

			$result = $currentBasket->refresh();
			if($result->isSuccess()){
				$currentBasket->save();
			}

			$availableBasket = $currentBasket->getOrderableItems();
			$basketItems = $availableBasket->getBasketItems();
			foreach($basketItems as $basketItem){
				$basket->addItem($basketItem);
				++$cntBasketItems;
			}
		}

	    $this->order->setBasket($basket);

	    return $cntBasketItems;
	}

	protected function initProperties(){
		$propertyCollection = $this->order->getPropertyCollection();
        if($propertyLocation = $propertyCollection->getDeliveryLocation()){
    		$locationCode = $propertyLocation->getValue();
    		$languageId = $this->getLanguageId();

    		if($this->arParams['USE_PROFILE_LOCATION'] === 'Y'){
    			if(
    				$this->arParams['SAVE_IN_SESSION'] !== 'Y' ||
    				!isset($_SESSION[$this->sessionVar]) ||
    				!is_array($_SESSION[$this->sessionVar]) ||
    				!$_SESSION[$this->sessionVar] ||
    				!$_SESSION[$this->sessionVar]['USER_PARAMS']
    			){
					if($this->arResult['USER_PROFILE_ID']){
						$profileProperties = Sale\OrderUserProperties::getProfileValues($this->arResult['USER_PROFILE_ID']);
					}

		    		if(isset($profileProperties[$propertyLocation->getProperty()['ID']])){
		    			if(strlen($locationCode = $profileProperties[$propertyLocation->getProperty()['ID']])){
			    			if($this->arResult['LOCATION'] = self::getLocationByCode($locationCode, $languageId)){
				    			$this->arResult['LOCATION_SOURCE'] = 'profile';
			    			}
		    			}
		    		}
    			}
			}

    		if($this->arResult['LOCATION']){
	            $propertyLocation->setValue($this->arResult['LOCATION']['CODE']);
    		}
    		else{
    			if(strlen($locationCode)){
    				if($this->arResult['LOCATION'] = self::getLocationByCode($locationCode, $languageId)){
	    				$this->arResult['LOCATION_SOURCE'] = 'property';
    				}
    			}
    			else{
    				if(!in_array('LOCATION', $this->arParams['CHANGEABLE_FIELDS'])){
						return $this->setError(Loc::getMessage('CD_ERROR_LOCATION_CODE'));
    				}
    			}
    		}

        	if($propertyZip = $propertyCollection->getDeliveryLocationZip()){
        		$locationZip = $propertyZip->getValue();

        		if($this->arResult['LOCATION']['ZIP']){
        			$propertyZip->setValue($this->arResult['LOCATION']['ZIP']);
        		}
        		else{
        			if($locationZip){
	        			$this->arResult['LOCATION']['ZIP'] = $locationZip;
        			}
        		}
        	}
        }

		return true;
	}

	protected function initShipment(){
		$shipmentCollection = $this->order->getShipmentCollection();
		$shipment = $shipmentCollection->createItem();
		$shipmentItemCollection = $shipment->getShipmentItemCollection();
		$shipment->setField('CURRENCY', $this->order->getCurrency());

		foreach($this->order->getBasket() as $item){
			$shipmentItem = $shipmentItemCollection->createItem($item);
			$shipmentItem->setQuantity($item->getQuantity());
		}

		return $shipment;
	}

	protected function getCurrentShipment(Sale\Order $order){
		foreach($order->getShipmentCollection() as $shipment){
			if(!$shipment->isSystem()){
				return $shipment;
			}
		}

		return null;
	}

	protected function getInnerPayment(Sale\Order $order){
		foreach($order->getPaymentCollection() as $payment){
			if($payment->getPaymentSystemId() == Sale\PaySystem\Manager::getInnerPaySystemId())
				return $payment;
		}

		return null;
	}

	protected function getExternalPayment(Sale\Order $order){
		foreach($order->getPaymentCollection() as $payment){
			if($payment->getPaymentSystemId() != Sale\PaySystem\Manager::getInnerPaySystemId()){
				return $payment;
			}
		}

		return null;
	}

	protected function initPaymentBefore(){
		$paySystemId = intval($this->arResult['PAY_SYSTEM_ID']);

		if($this->arParams['DELIVERY_WITHOUT_PAY_SYSTEM'] === 'Y'){
			$this->arResult['PAY_SYSTEM'][0] = array(
				'ID' => 0,
				'NAME' => Loc::getMessage('CD_ANY'),
				'SORT' => 0,
            	'DESCRIPTION' => '',
            	'LOGOTIP' => array(),
			);
		}

		$arFilter = array(
			'ACTIVE' => 'Y',
		);
		if($this->arParams['PAY_FROM_ACCOUNT'] !== 'Y'){
			$arFilter['!ID'] = Sale\PaySystem\Manager::getInnerPaySystemId();
		}

		$res = Sale\Internals\PaySystemActionTable::getList(
            array(
            	'order' => array(
            		'SORT' => 'asc',
            		'ID' => 'asc',
            	),
                'filter' => $arFilter,
                'select' => array(
                	'ID',
                	'NAME',
                	'SORT',
                	'DESCRIPTION',
                	'LOGOTIP',
                ),
            )
        );
        while($paysystem = $res->fetch()){
            if($paysystem['LOGOTIP'] > 0){
                $paysystem['LOGOTIP'] = CFile::GetByID($paysystem['LOGOTIP'])->Fetch();
                $paysystem['LOGOTIP']['SRC'] = CFile::GetFileSRC($paysystem['LOGOTIP']);
            }
            else{
            	$paysystem['LOGOTIP'] = array();
            }

            $this->arResult['PAY_SYSTEM'][$paysystem['ID']] = $paysystem;
        }

		if($this->arResult['PAY_SYSTEM']){
	        $paymentCollection = $this->order->getPaymentCollection();
	        foreach($paymentCollection as $payment){
	        	break;
	        }
	        if(!$payment){
		        $payment = $paymentCollection->createItem();
	        }

			$innerPayment = $this->getInnerPayment($this->order);
			$remainingSum = empty($innerPayment) ? $this->order->getPrice() : $this->order->getPrice() - $innerPayment->getSum();

			$payment->setField('SUM', $remainingSum);
			$extPaySystemList = Sale\PaySystem\Manager::getListWithRestrictions($payment);
			if($this->arParams['PAY_FROM_ACCOUNT'] !== 'Y'){
				$innerPaySystemtId = Sale\PaySystem\Manager::getInnerPaySystemId();
				unset($extPaySystemList[$innerPaySystemtId]);
			}

			foreach($this->arResult['PAY_SYSTEM'] as $i => &$paysystem){
				if($paysystem['ID'] && !array_key_exists($paysystem['ID'], $extPaySystemList)){
					unset($this->arResult['PAY_SYSTEM'][$i]);
					continue;
				}

				if($paySystemId === intval($paysystem['ID']) || !array_key_exists($paySystemId, $this->arResult['PAY_SYSTEM'])){
					$paySystemId = intval($paysystem['ID']);

					if($paySystemId){
						$paySystemService = \Bitrix\Sale\PaySystem\Manager::getObjectById($paySystemId);
						$payment->setFields(array(
							'PAY_SYSTEM_ID' => $paySystemService->getField('PAY_SYSTEM_ID'),
							'PAY_SYSTEM_NAME' => $paySystemService->getField('NAME'),
						));
					}

					$this->arResult['PAY_SYSTEM_ID'] = $paySystemId;
					$paysystem['CHECKED'] = 'Y';
				}
			}
			unset($paysystem);
		}

        return $this->arResult['PAY_SYSTEM_ID'];
	}

	protected function initPaymentAfter(){
		if($this->arResult['PAY_SYSTEM'] && $this->arResult['DELIVERY']){
			$shipmentCollection = $this->order->getShipmentCollection();
			foreach($shipmentCollection as $shipment){
				if(!$shipment->isSystem()){
					break;
				}
			}
			if(!$shipment){
				$shipment = $shipmentCollection->createItem();
			}

	        $paymentCollection = $this->order->getPaymentCollection();
	        foreach($paymentCollection as $payment){
	        	break;
	        }
	        if(!$payment){
		        $payment = $paymentCollection->createItem();
	        }
			$innerPayment = $this->getInnerPayment($this->order);

			foreach($this->arResult['DELIVERY'] as &$arDelivery){
				$shipment->setFields(array(
					'DELIVERY_ID' => $arDelivery['ID'],
				));

				$remainingSum = empty($innerPayment) ? $this->order->getPrice() : $this->order->getPrice() - $innerPayment->getSum();
				$payment->setField('SUM', $remainingSum);
				$extPaySystemList = Sale\PaySystem\Manager::getListWithRestrictions($payment);
				if($this->arParams['PAY_FROM_ACCOUNT'] !== 'Y'){
					$innerPaySystemtId = Sale\PaySystem\Manager::getInnerPaySystemId();
					unset($extPaySystemList[$innerPaySystemtId]);
				}

				$arDelivery['PAY_SYSTEM'] = array_keys($extPaySystemList);
			}
			unset($arDelivery);
		}

        return $this->arResult['PAY_SYSTEM_ID'];
	}

	protected function hasAnyActiveDelivery(){
		$filter = array(
            'order' => array('ID' => 'asc'),
            'filter' => array(
                'ACTIVE'    => 'Y',
            ),
            'select' => array(
                'ID',
            ),
            'limit' => 1,
        );
        if($delivery = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId())){
        	$filter['filter']['!ID'] = $delivery['ID'];
        }

        if($arDelivery = \Bitrix\Sale\Delivery\Services\Table::getList($filter)->fetch()){
        	return true;
        }

        return false;
	}

	protected function initDelivery(\Bitrix\Sale\Shipment $shipment){
		if($this->hasAnyActiveDelivery()){
			$arDeliveryServiceAll = Delivery\Services\Manager::getRestrictedObjectsList($shipment);
			if(!empty($arDeliveryServiceAll)){
				if($this->arResult['DELIVERY_ID']){
					foreach($this->arResult['DELIVERY_ID'] as $i => $deliveryId){
						if(!isset($arDeliveryServiceAll[$deliveryId])){
							unset($this->arResult['DELIVERY_ID'][$i]);
						}
					}

					$this->arResult['DELIVERY_ID'] = array_values($this->arResult['DELIVERY_ID']);
				}

				foreach($arDeliveryServiceAll as $deliveryObj){
					if($deliveryObj->isProfile() && $this->arParams['SHOW_DELIVERY_PARENT_NAMES'] === 'Y'){
						$name = $deliveryObj->getNameWithParent();
					}
					else{
						$name = $deliveryObj->getName();
					}

					$logotip = intval($deliveryObj->getLogotip());
					if($logotip > 0){
						$arLogotip = \CFile::GetFileArray($logotip);
					}

					$arDelivery = array(
						'ID' => $deliveryObj->getId(),
						'PARENT_ID' => $deliveryObj->getParentId(),
						'NAME' => $name,
						'OWN_NAME' => $deliveryObj->getName(),
						'PARENT_NAME' => $deliveryObj->isProfile() ? $deliveryObj->getParentService()->getName() : $deliveryObj->getName(),
						'DESCRIPTION' => $deliveryObj->getDescription(),
						'SORT' => $deliveryObj->getSort(),
						'STORE' => Delivery\ExtraServices\Manager::getStoresList($deliveryObj->getId()),
						'LOGOTIP' => $logotip > 0 ? $arLogotip : false,
					);

					if(in_array($deliveryObj->getId(), $this->arResult['DELIVERY_ID'])){
						$arDelivery['CHECKED'] = 'Y';
					}

					$shipment->setFields(array(
						'DELIVERY_ID' => $arDelivery['ID'],
						'DELIVERY_NAME' => $name,
						'CURRENCY' => $this->order->getCurrency(),
					));

					if(!empty($arDelivery['STORE'])){
						if($this->arResult['BUYER_STORE_ID'] <= 0 || !in_array($this->arResult['BUYER_STORE_ID'], $arDelivery['STORE'])){
							$this->arResult['BUYER_STORE_ID'] = current($arDelivery['STORE']);
						}

						$shipment->setStoreId($this->arResult['BUYER_STORE_ID']);
					}

					$calculationResult = $this->order->getShipmentCollection()->calculateDelivery();
					if($calculationResult->isSuccess()){
						$calcDeliveries = $calculationResult->get('CALCULATED_DELIVERIES');
						$calcResult = reset($calcDeliveries);
					}
					else{
						$calcResult = new Delivery\CalculationResult();
						$calcResult->addErrors($calculationResult->getErrors());
					}

					if($calcResult->isSuccess()){
						$arDelivery['PRICE'] = Sale\PriceMaths::roundPrecision($calcResult->getPrice());
						$arDelivery['PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['PRICE'], $this->order->getCurrency());

						$currentCalcDeliveryPrice = Sale\PriceMaths::roundPrecision($this->order->getDeliveryPrice());
						if($currentCalcDeliveryPrice >= 0 && $arDelivery['PRICE'] != $currentCalcDeliveryPrice){
							$arDelivery['DELIVERY_DISCOUNT_PRICE'] = $currentCalcDeliveryPrice;
							$arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['DELIVERY_DISCOUNT_PRICE'], $this->order->getCurrency());
						}

						if(strlen($calcResult->getPeriodDescription())){
							$arDelivery['PERIOD_TEXT'] = $calcResult->getPeriodDescription();
							$arDelivery['PERIOD_FROM'] = $calcResult->getPeriodFrom();
							$arDelivery['PERIOD_TO'] = $calcResult->getPeriodTo();
							$arDelivery['PERIOD_TYPE'] = $calcResult->getPeriodTo();
						}
					}
					else{
						if($this->arParams['SHOW_MESSAGE_ON_CALCULATE_ERROR'] === 'Y'){
							$arDelivery['CALCULATE_ERRORS'] = implode('<br />',
								array(
									'<span style="font-weight:bold;">'.$this->arResult['MESSAGES']['CALC_ERROR_TITLE'].'</span>',
									'<span>'.$this->arResult['MESSAGES']['CALC_ERROR_TEXT'].'</span>',
								)
							);
						}
						else{
							if(count($calcResult->getErrorMessages())){
								foreach($calcResult->getErrorMessages() as $message){
									$arDelivery['CALCULATE_ERRORS'] .= $message.'<br>';
								}
							}
							else{
								$arDelivery['CALCULATE_ERRORS'] = Loc::getMessage('CD_DELIVERY_CALCULATE_ERROR');
							}
						}
					}

					$this->arResult['DELIVERY'][$arDelivery['ID']] = $arDelivery;
				}
			}
			else{
				// $delivery = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
				// $shipment->setFields(array(
				// 	'DELIVERY_ID' => $delivery['ID'],
				// 	'DELIVERY_NAME' => $delivery['NAME'],
				// 	'CURRENCY' => $this->order->getCurrency(),
				// ));

				// $shipmentCollection = $shipment->getCollection();
				// $calculationResult = $shipmentCollection->calculateDelivery();
			}

			return true;
		}
		else{
			return $this->setError(Loc::getMessage('CD_ERROR_DELIVERY'));
		}
	}
}
?>