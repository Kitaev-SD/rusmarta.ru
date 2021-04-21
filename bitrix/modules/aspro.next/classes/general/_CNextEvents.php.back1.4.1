<?
if(!defined('ASPRO_NEXT_MODULE_ID'))
	define('ASPRO_NEXT_MODULE_ID', 'aspro.next');

use \Bitrix\Main\Localization\Loc,
	Bitrix\Main\Application,
	\Bitrix\Main\Config\Option,
	Bitrix\Main\IO\File,
	Bitrix\Main\Page\Asset;
Loc::loadMessages(__FILE__);

class CNextEvents{
	const moduleID = ASPRO_NEXT_MODULE_ID;
	const partnerName	= "aspro";
    const solutionName	= "next";
    const wizardID		= "aspro:next";

	function ShowPanel(){
    }

	function BeforeSendEvent(\Bitrix\Main\Event $event){
		if(isset($_REQUEST["ONE_CLICK_BUY"]) && method_exists('\Bitrix\Sale\Compatible\EventCompatibility', 'setDisableMailSend')){
			\Bitrix\Sale\Compatible\EventCompatibility::setDisableMailSend(true);
			if(method_exists('\Bitrix\Sale\Notify', 'setNotifyDisable'))
				\Bitrix\Sale\Notify::setNotifyDisable(true);
		}
	}

	public static function OnBeforeEventAddHandler(&$event, &$lid, &$arFields, &$message_id){
		if(\Bitrix\Main\Loader::includeModule(self::moduleID))
		{
			if($arCurrentRegion = CNextRegionality::getCurrentRegion())
			{
				$arFields['REGION_ID'] = $arCurrentRegion['ID'];
				$arFields['REGION_MAIN_DOMAIN'] = $arCurrentRegion['PROPERTY_MAIN_DOMAIN_VALUE'];
				$arFields['REGION_MAIN_DOMAIN_RAW'] = (CMain::IsHTTPS() ? 'https://' : 'http://').$arCurrentRegion['PROPERTY_MAIN_DOMAIN_VALUE'];
				$arFields['REGION_ADDRESS'] = $arCurrentRegion['PROPERTY_ADDRESS_VALUE']['TEXT'];
				$arFields['REGION_EMAIL'] = implode(', ', $arCurrentRegion['PROPERTY_EMAIL_VALUE']);
				$arFields['REGION_PHONE'] = implode(', ', $arCurrentRegion['PHONES']);

				$arTagSeoMarks = array();
				foreach($arCurrentRegion as $key => $value)
				{
					if(strpos($key, 'PROPERTY_REGION_TAG') !== false && strpos($key, '_VALUE_ID') === false)
					{
						$tag_name = str_replace(array('PROPERTY_', '_VALUE'), '', $key);
						$arTagSeoMarks['#'.$tag_name.'#'] = $key;
					}
				}

				if($arTagSeoMarks)
					CNextRegionality::addSeoMarks($arTagSeoMarks);

				foreach(CNextRegionality::$arSeoMarks as $mark => $field)
				{
					$mark = str_replace('#', '', $mark);
					if(is_array($arCurrentRegion[$field]))
						$arFields[$mark] = $arCurrentRegion[$field]['TEXT'];
					else
						$arFields[$mark] = $arCurrentRegion[$field];
				}
			}
		}
	}

	public static function fixRegionMailFields(&$arFields, $regionID = null){
		$arCurrentRegion = array();
	}

	function OnFindSocialservicesUserHandler($arFields){
		// check for user with email
		if($arFields['EMAIL'])
		{
			$arUser = CUser::GetList($by = 'ID', $ord = 'ASC', array('EMAIL' => $arFields['EMAIL'], 'ACTIVE' => 'Y'), array('NAV_PARAMS' => array("nTopCount" => "1")))->fetch();
			if($arUser)
			{
				if($arFields['PERSONAL_PHOTO'])
				{

					/*if(!$arUser['PERSONAL_PHOTO'])
					{
						$arUpdateFields = Array(
							'PERSONAL_PHOTO' => $arFields['PERSONAL_PHOTO'],
						);
						$user->Update($arUser['ID'], $arUpdateFields);
					}
					else
					{*/
						$code = 'UF_'.strtoupper($arFields['EXTERNAL_AUTH_ID']);
						$arUserFieldUserImg = CUserTypeEntity::GetList(array(), array('ENTITY_ID' => 'USER', 'FIELD_NAME' => $code))->Fetch();
						if(!$arUserFieldUserImg)
						{
							$arFieldsUser = array(
								"FIELD_NAME" => $code,
								"USER_TYPE_ID" => "file",
								"XML_ID" => $code,
								"SORT" => 100,
								"MULTIPLE" => "N",
								"MANDATORY" => "N",
								"SHOW_FILTER" => "N",
								"SHOW_IN_LIST" => "Y",
								"EDIT_IN_LIST" => "Y",
								"IS_SEARCHABLE" => "N",
								"SETTINGS" => array(
									"DISPLAY" => "LIST",
									"LIST_HEIGHT" => 5,
								)
							);
							$arLangs = array(
								"EDIT_FORM_LABEL" => array(
									"ru" => $code,
									"en" => $code,
								),
								"LIST_COLUMN_LABEL" => array(
									"ru" => $code,
									"en" => $code,
								)
							);

							$ob = new CUserTypeEntity();
							$FIELD_ID = $ob->Add(array_merge($arFieldsUser, array('ENTITY_ID' => 'USER'), $arLangs));

						}
						$user = new CUser;
						$arUpdateFields = Array(
							$code => $arFields['PERSONAL_PHOTO'],
						);
						$user->Update($arUser['ID'], $arUpdateFields);
					//}
				}
				return $arUser['ID'];
			}
		}
		return false;
	}

	function OnAfterSocServUserAddHandler( $arFields ){
		if($arFields["EMAIL"]){
			global $USER;
			$userEmail=$USER->GetEmail();
			$email=(is_null($userEmail) ? $arFields["EMAIL"] : $userEmail );
			//$resUser = CUser::GetList(($by="ID"), ($order="asc"), array("=EMAIL" => $arFields["EMAIL"]), array("FIELDS" => array("ID")));
			$resUser = CUser::GetList(($by="ID"), ($order="asc"), array("=EMAIL" => $email), array("FIELDS" => array("ID")));
			$arUserAlreadyExist = $resUser->Fetch();

			if($arUserAlreadyExist["ID"]){
				\Bitrix\Main\Loader::includeModule('socialservices');
				global $USER;
				if($resUser->SelectedRowsCount()>1){
					CSocServAuthDB::Update($arFields["ID"], array("USER_ID" => $arUserAlreadyExist["ID"], "CAN_DELETE" => "Y"));
					CUser::Delete($arFields["USER_ID"]);
					$USER->Authorize($arUserAlreadyExist["ID"]);
				}else{
					$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
					if($def_group!=""){
						$GROUP_ID = explode(",", $def_group);
						$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
					}else{
						$arPolicy = $USER->GetGroupPolicy(array());
					}
					$password_min_length = (int)$arPolicy["PASSWORD_LENGTH"];
					if($password_min_length <= 0)
						$password_min_length = 6;
					$password_chars = array(
						"abcdefghijklnmopqrstuvwxyz",
						"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
						"0123456789",
					);
					if($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
						$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";
					$NEW_PASSWORD = $NEW_PASSWORD_CONFIRM = randString($password_min_length+2, $password_chars);

					$user = new CUser;
					$arFieldsUser = Array(
					  "NAME"              => $arFields["NAME"],
					  "LAST_NAME"         => $arFields["LAST_NAME"],
					  "EMAIL"             => $arFields["EMAIL"],
					  "LOGIN"             => $arFields["EMAIL"],
					  "GROUP_ID"          => $GROUP_ID,
					  "PASSWORD"          => $NEW_PASSWORD,
					  "CONFIRM_PASSWORD"  => $NEW_PASSWORD_CONFIRM,
					);
					unset($arFields["LOGIN"]);
					unset($arFields["PASSWORD"]);
					unset($arFields["EXTERNAL_AUTH_ID"]);
					unset($arFields["XML_ID"]);
					$arAddFields = array();
					$arAddFields = array_merge($arFieldsUser, $arFields);
					if(isset($arAddFields["PERSONAL_PHOTO"]) && $arAddFields["PERSONAL_PHOTO"])
					{
						$arPic = CFile::MakeFileArray($arFields["PERSONAL_PHOTO"]);
						$arAddFields["PERSONAL_PHOTO"] = $arPic;
					}

					//if($arUserAlreadyExist["ID"]!=$arFields["USER_ID"]){
						$ID = $user->Add($arAddFields);
						//$ID = $user->Add($arFieldsUser);
						CSocServAuthDB::Update($arFields["ID"], array("USER_ID" => $ID, "CAN_DELETE" => "Y"));
						CUser::Delete($arFields["USER_ID"]);
						$USER->Authorize($ID);
					//}
				}
			}
		}
	}

	function OnSaleComponentOrderProperties(&$arUserResult, $arRequest, $arParams, $arResult){
		if($arUserResult['ORDER_PROP'])
		{
			$arPhoneProp = CSaleOrderProps::GetList(
				array('SORT' => 'ASC'),
				array(
						'PERSON_TYPE_ID' => $arUserResult['PERSON_TYPE_ID'],
						'IS_PHONE' => 'Y',
					),
				false,
				false,
				array()
			)->fetch(); // get phone prop
			if($arPhoneProp && $arParams['USE_PHONE_NORMALIZATION'] !='N')
			{
				global $USER;
				if($arUserResult['ORDER_PROP'][$arPhoneProp['ID']])
				{
					if($_REQUEST['order']['ORDER_PROP_'.$arPhoneProp['ID']])
					{
						$arUserResult['ORDER_PROP'][$arPhoneProp['ID']] = $_REQUEST['order']['ORDER_PROP_'.$arPhoneProp['ID']];
					}
					else
					{
						if($arUserResult['PROFILE_ID']) //get phone from user profile
						{
							$arUserPropValue = CSaleOrderUserPropsValue::GetList(
								array('ID' => 'ASC'),
								array('USER_PROPS_ID' => $arUserResult['PROFILE_ID'], 'ORDER_PROPS_ID' => $arPhoneProp['ID'])
							)->fetch();
							if($arUserPropValue['VALUE'])
							{
								$arUserResult['ORDER_PROP'][$arPhoneProp['ID']] = $arUserPropValue['VALUE'];
							}
						}
						elseif($USER->isAuthorized()) //get phone from user field
						{
							$rsUser = CUser::GetByID($USER->GetID());
							if($arUser = $rsUser->Fetch())
							{
								if(!empty($arUser['PERSONAL_PHONE']))
								{
									$value = $arUser['PERSONAL_PHONE'];
								}
								elseif(!empty($arUser['PERSONAL_MOBILE']))
								{
									$value = $arUser['PERSONAL_MOBILE'];
								}
							}
							if($value)
								$arUserResult['ORDER_PROP'][$arPhoneProp['ID']] = $value;
						}
						if($arUserResult['ORDER_PROP'][$arPhoneProp['ID']]) // add + mark for correct mask
						{
							$mask = \Bitrix\Main\Config\Option::get('aspro.next', 'PHONE_MASK', '+7 (999) 999-99-99');
							if(strpos($arUserResult['ORDER_PROP'][$arPhoneProp['ID']], '+') === false && strpos($mask, '+') !== false)
							{
								$arUserResult['ORDER_PROP'][$arPhoneProp['ID']] = '+'.$arUserResult['ORDER_PROP'][$arPhoneProp['ID']];
							}
						}
					}
				}
			}
		}
	}

	function OnSaleComponentOrderOneStepComplete($ID, $arOrder, $arParams){
		$arOrderProps = array();
		$resOrder = CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $ID));
		while($item = $resOrder->fetch())
		{
			$arOrderProps[$item['CODE']] = $item;
		}
		$arPhoneProp = CSaleOrderProps::GetList(
			array('SORT' => 'ASC'),
			array(
					'PERSON_TYPE_ID' => $arOrder['PERSON_TYPE_ID'],
					'IS_PHONE' => 'Y',
				),
			false,
			false,
			array()
		)->fetch(); // get phone prop
		if($arPhoneProp && $arParams['USE_PHONE_NORMALIZATION'] !='N')
		{
			if($arOrderProps[$arPhoneProp['CODE']])
			{
				if($arOrderProps[$arPhoneProp['CODE']]['VALUE'])
				{
					if($_REQUEST['ORDER_PROP_'.$arOrderProps[$arPhoneProp['CODE']]['ORDER_PROPS_ID']])
					{
						CSaleOrderPropsValue::Update($arOrderProps[$arPhoneProp['CODE']]['ID'], array('VALUE'=>$_REQUEST['ORDER_PROP_'.$arOrderProps[$arPhoneProp['CODE']]['ORDER_PROPS_ID']])); // set phone order prop
						$arUserProps = CSaleOrderUserProps::GetList(
							array('DATE_UPDATE' => 'DESC'),
							array('USER_ID' => $arOrder['USER_ID'], 'PERSON_TYPE_ID' => $arOrder['PERSON_TYPE_ID'])
						)->fetch(); // get user profile info

						if($arUserProps)
						{
							$arUserPropValue = CSaleOrderUserPropsValue::GetList(
								array('ID' => 'ASC'),
								array('USER_PROPS_ID' => $arUserProps['ID'], 'ORDER_PROPS_ID' => $arOrderProps[$arPhoneProp['CODE']]['ORDER_PROPS_ID'])
							)->fetch(); // get phone from user prop
							if($arUserPropValue['VALUE'])
							{
								CSaleOrderUserPropsValue::Update($arUserPropValue['ID'], array('VALUE'=>$_REQUEST['ORDER_PROP_'.$arOrderProps[$arPhoneProp['CODE']]['ORDER_PROPS_ID']])); //set phone in user profile
							}
						}
					}
				}
			}
		}
	}

	function correctInstall(){
		if(COption::GetOptionString(self::moduleID, "WIZARD_DEMO_INSTALLED") == "Y"){
			if(CModule::IncludeModule("main")){
				require_once($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/classes/general/wizard.php");
				@set_time_limit(0);
				if(!CWizardUtil::DeleteWizard(self::wizardID)){if(!DeleteDirFilesEx($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/".self::partnerName."/".self::solutionName."/")){self::removeDirectory($_SERVER["DOCUMENT_ROOT"]."/bitrix/wizards/".self::partnerName."/".self::solutionName."/");}}
				UnRegisterModuleDependences("main", "OnBeforeProlog", self::moduleID, get_class(), "correctInstall");
				COption::SetOptionString(self::moduleID, "WIZARD_DEMO_INSTALLED", "N");
			}
		}
	}

	function OnBeforeUserUpdateHandler(&$arFields){
		$bTmpUser = false;
		$bAdminSection = (defined('ADMIN_SECTION') && ADMIN_SECTION === true);

		if(strlen($arFields["NAME"]))
			$arFields["NAME"] = trim($arFields["NAME"]);

		$siteID = SITE_ID;

		if($bAdminSection)
	    {
	    	// include CMainPage
	        require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
	        // get site_id by host
	        $CMainPage = new \CMainPage();
	        $siteID = $CMainPage->GetSiteByHost();
	        if(!$siteID)
	            $siteID = "s1";

			$sOneFIO = COption::GetOptionString(ASPRO_NEXT_MODULE_ID, 'PERSONAL_ONEFIO', 'Y', $siteID);
			$sChangeLogin = COption::GetOptionString(ASPRO_NEXT_MODULE_ID, 'LOGIN_EQUAL_EMAIL', 'Y', $siteID);
        }
		else
		{
			$arFrontParametrs = CNext::GetFrontParametrsValues($siteID);
			$sOneFIO = $arFrontParametrs['PERSONAL_ONEFIO'];
			$sChangeLogin = $arFrontParametrs['LOGIN_EQUAL_EMAIL'];
		}

		if(strlen($arFields["NAME"]) && !strlen($arFields["LAST_NAME"]) && !strlen($arFields["SECOND_NAME"])){
			if($sOneFIO !== 'N')
			{
				$arName = explode(' ', $arFields["NAME"]);

				if($arName){
					$arFields["NAME"] = "";
					$arFields["SECOND_NAME"] = "";
					foreach($arName as $i => $name){
						if(!$i){
							$arFields["LAST_NAME"] = $name;
						}
						else{
							if(!strlen($arFields["NAME"])){
								$arFields["NAME"] = $name;
							}
							elseif(!strlen($arFields["SECOND_NAME"])){
								$arFields["SECOND_NAME"] = $name;
							}
						}
					}
				}
			}
		}
		if($_REQUEST["confirmorder"]=="Y"  && !strlen($arFields["SECOND_NAME"]) && $_REQUEST["ORDER_PROP_1"]){
			$arNames = explode(' ', $_REQUEST["ORDER_PROP_1"]);
			if($arNames[2]){
				$arFields["SECOND_NAME"]=$arNames[2];
			}
		}

		if(isset($_REQUEST["soa-action"]) && $_REQUEST["soa-action"] == "saveOrderAjax") // set correct phone in user field
		{
			$arPhoneProp = CSaleOrderProps::GetList(
				array('SORT' => 'ASC'),
				array(
						'PERSON_TYPE_ID' => $_REQUEST['PERSON_TYPE'],
						'IS_PHONE' => 'Y',
					),
				false,
				false,
				array()
			)->fetch();
			if($arPhoneProp)
			{
				if($_REQUEST['ORDER_PROP_'.$arPhoneProp['ID']])
				{
					$arFields["PERSONAL_PHONE"] = $_REQUEST['ORDER_PROP_'.$arPhoneProp['ID']];
				}
			}
		}

		if(strlen($arFields["EMAIL"]))
		{
			if($sChangeLogin != "N")
			{
				$bEmailError = false;

				if(\Bitrix\Main\Config\Option::get('main', 'new_user_email_uniq_check', 'N') == 'Y')
				{
					$rsUser = CUser::GetList($by = "ID", $order = "ASC", array("=EMAIL" => $arFields["EMAIL"], "!ID" => $arFields["ID"]));
					if(!$bEmailError = intval($rsUser->SelectedRowsCount()) > 0)
					{
						$rsUser = CUser::GetList($by = "ID", $order = "ASC", array("LOGIN_EQUAL" => $arFields["EMAIL"], "!ID" => $arFields["ID"]));
						$bEmailError = intval($rsUser->SelectedRowsCount()) > 0;
					}
				}

				if($bEmailError){
					global $APPLICATION;
					$APPLICATION->throwException(Loc::getMessage("EMAIL_IS_ALREADY_EXISTS", array("#EMAIL#" => $arFields["EMAIL"])));
					return false;
				}
				else{
					// !admin
					if (!isset($GLOBALS["USER"]) || !is_object($GLOBALS["USER"])){
						$bTmpUser = True;
						$GLOBALS["USER"] = new \CUser;
					}

					if($bAdminSection)
					{
						if(isset($arFields['ID']) && $arFields['ID'])
						{
							if(!in_array(1, CUser::GetUserGroup($arFields['ID'])))
								$arFields['LOGIN'] = $arFields['EMAIL'];
						}
						elseif(isset($arFields['GROUP_ID']) && $arFields['GROUP_ID'])
						{
							$arUserGroups = array();
							$arTmpGroups = (array)$arFields['GROUP_ID'];
							foreach($arTmpGroups as $arGroup)
							{
								if(is_array($arGroup))
									$arUserGroups[] = $arGroup['GROUP_ID'];
								else
									$arUserGroups[] = $arGroup;
							}

							if(count(array_intersect($arUserGroups, array(1)))<=0)
								$arFields['LOGIN'] = $arFields['EMAIL'];
						}
						else
							$arFields['LOGIN'] = $arFields['EMAIL'];
					}
					else
					{
						if(!$GLOBALS['USER']->IsAdmin())
							$arFields["LOGIN"] = $arFields["EMAIL"];
					}
				}
			}
			else
			{
				if(!$arFields["LOGIN"] || $arFields["LOGIN"] == 1)
				{
					$newLogin = $arFields['EMAIL'];
					$pos = strpos($newLogin, '@');
					if ($pos !== false)
						$newLogin = substr($newLogin, 0, $pos);

					if (strlen($newLogin) > 47)
						$newLogin = substr($newLogin, 0, 47);

					if (strlen($newLogin) < 3)
						$newLogin .= '_';

					if (strlen($newLogin) < 3)
						$newLogin .= '_';
					$arFields["LOGIN"] = $newLogin;
				}
			}
		}

		if ($bTmpUser)
			unset($GLOBALS["USER"]);

		return $arFields;
	}

	static function InsertCounters(&$html){
	}

	static function clearBasketCacheHandler($orderID, $arFields, $arParams = array()){
		CNextCache::ClearCacheByTag('sale_basket');
		unset($_SESSION['ASPRO_BASKET_COUNTERS']);
		if(isset($arFields) && $arFields)
		{
			if(isset($arFields["ID"]) && $arFields["ID"])
			{
				\Bitrix\Main\Loader::includeModule("sale");
				global $USER;
				$USER_ID = ($USER_ID = $USER->GetID()) ? $USER_ID : 0;
				$arUser = $arUser = CNextCache::CUser_GetList(array("SORT" => "ASC", "CACHE" => array("MULTI" => "N", "TAG" => CNextCache::GetUserCacheTag($USER_ID))), array("ID" => $USER_ID), array("FIELDS" => array("ID", "PERSONAL_PHONE")));
				if(!$arUser["PERSONAL_PHONE"])
				{
					$rsOrder = CSaleOrderPropsValue::GetList(array(), array("ORDER_ID" => $arFields["ID"]));
					$arOrderProps = array();
					while($item = $rsOrder->Fetch())
					{
						$arOrderProps[$item["CODE"]] = $item;
					}
					if(isset($arOrderProps["PHONE"]) && $arOrderProps["PHONE"] && (isset($arOrderProps["PHONE"]["VALUE"]) && $arOrderProps["PHONE"]["VALUE"]))
					{
						$user = new CUser;
						$fields = Array(
							"PERSONAL_PHONE" => $arOrderProps["PHONE"]["VALUE"],
						);
						$user->Update($arUser["ID"], $fields);
					}

				}
			}
		}
	}

	static function DoIBlockAfterSave($arg1, $arg2 = false){
		$ELEMENT_ID = false;
		$IBLOCK_ID = false;
		$OFFERS_IBLOCK_ID = false;
		$OFFERS_PROPERTY_ID = false;
		if (CModule::IncludeModule('currency'))
			$strDefaultCurrency = CCurrency::GetBaseCurrency();

		//Check for catalog event
		if(is_array($arg2) && $arg2["PRODUCT_ID"] > 0){
			//Get iblock element
			$rsPriceElement = CIBlockElement::GetList(
				array(),
				array(
					"ID" => $arg2["PRODUCT_ID"],
				),
				false,
				false,
				array("ID", "IBLOCK_ID")
			);
			if($arPriceElement = $rsPriceElement->Fetch()){
				$arCatalog = CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
				if(is_array($arCatalog)){
					//Check if it is offers iblock
					if($arCatalog["OFFERS"] == "Y"){
						//Find product element
						$rsElement = CIBlockElement::GetProperty(
							$arPriceElement["IBLOCK_ID"],
							$arPriceElement["ID"],
							"sort",
							"asc",
							array("ID" => $arCatalog["SKU_PROPERTY_ID"])
						);
						$arElement = $rsElement->Fetch();
						if($arElement && $arElement["VALUE"] > 0)
						{
							$ELEMENT_ID = $arElement["VALUE"];
							$IBLOCK_ID = $arCatalog["PRODUCT_IBLOCK_ID"];
							$OFFERS_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
							$OFFERS_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
						}
					}
					//or iblock which has offers
					elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0){
						$ELEMENT_ID = $arPriceElement["ID"];
						$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
						$OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
						$OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
					}
					//or it's regular catalog
					else{
						$ELEMENT_ID = $arPriceElement["ID"];
						$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
						$OFFERS_IBLOCK_ID = false;
						$OFFERS_PROPERTY_ID = false;
					}
				}
			}
		}
		//Check for iblock event
		elseif(is_array($arg1) && $arg1["ID"] > 0 && $arg1["IBLOCK_ID"] > 0){
			$IBLOCK_ID = $arg1["IBLOCK_ID"];

			//Check if iblock has offers
			$arOffers = CIBlockPriceTools::GetOffersIBlock($arg1["IBLOCK_ID"]);
			if(is_array($arOffers)){
				$ELEMENT_ID = $arg1["ID"];
				$OFFERS_IBLOCK_ID = $arOffers["OFFERS_IBLOCK_ID"];
				$OFFERS_PROPERTY_ID = $arOffers["OFFERS_PROPERTY_ID"];
			}
			else{
				if(self::isLandingSearchIblock($IBLOCK_ID)){
					$arLandingSearchMetaHash =
					$arLandingSearchMetaData =
					$arLandingSearchQuery = array();
					$urlCondition = $queryReplacement = $queryExample = '';

					$dbRes = CIBlockElement::GetProperty(
						$IBLOCK_ID,
						$arg1['ID'],
						array('id' => 'asc'),
						array('CODE' => 'QUERY')
					);
					while($arSeoSearchElementQuery = $dbRes->Fetch()){
						if(strlen($query = trim($arSeoSearchElementQuery['VALUE']))){
							list($query, $hash, $arData) = \Aspro\Next\SearchQuery::getSentenceMeta($query);
							$arLandingSearchQuery[] = $query;
							$arLandingSearchMetaHash[] = $hash;
							$arLandingSearchMetaData[] = serialize($arData);
						}
					}

					// get value of property QUERY_REPLACEMENT
					$dbRes = CIBlockElement::GetProperty(
						$IBLOCK_ID,
						$arg1['ID'],
						array('id' => 'asc'),
						array('CODE' => 'QUERY_REPLACEMENT')
					);
					$arPropertyQueryReplacement = $dbRes->Fetch();
					$queryReplacement = trim($arPropertyQueryReplacement['VALUE']);

					if($arLandingSearchQuery){
						if(strlen($queryExample = \Aspro\Next\SearchQuery::getSentenceExampleQuery(reset($arLandingSearchQuery), LANG))){
							// check value of property URL_CONDITION
							$dbRes = CIBlockElement::GetProperty(
								$IBLOCK_ID,
								$arg1['ID'],
								array('id' => 'asc'),
								array('CODE' => 'URL_CONDITION')
							);
							if($arPropertyUrlCondition = $dbRes->Fetch()){
								$urlCondition = ltrim(trim($arPropertyUrlCondition['VALUE']), '/');
							}
						}
					}

					$arUpdateFields = array(
						'QUERY' => $arLandingSearchQuery,
						'META_HASH' => $arLandingSearchMetaHash,
						'META_DATA' => $arLandingSearchMetaData,
						'URL_CONDITION' => strlen($urlCondition) ? '/'.$urlCondition : '',
						'QUERY_REPLACEMENT' => $queryReplacement,
					);

					// update values
					CIBlockElement::SetPropertyValuesEx(
						$arg1['ID'],
						$IBLOCK_ID,
						$arUpdateFields
					);

					if(CNextCache::$arIBlocksInfo[$IBLOCK_ID]){
						$arSitesLids = CNextCache::$arIBlocksInfo[$IBLOCK_ID]['LID'];

						// search and remove urlrewrite item
						$searchRule = 'ls='.$arg1['ID'];
						$searchCondition = strlen($urlCondition) ? '#^/'.$urlCondition.'#' : false;
						foreach($arSitesLids as $siteId){
							if($arUrlRewrites = \Bitrix\Main\UrlRewriter::getList($siteId, array('ID' => 'bitrix:catalog'))){
								foreach($arUrlRewrites as $arUrlRewrite){
									if($arUrlRewrite['RULE'] && strpos($arUrlRewrite['RULE'], $searchRule) !== false){
										\Bitrix\Main\UrlRewriter::delete($siteId, array('CONDITION' => $arUrlRewrite['CONDITION']));
									}

									if($searchCondition && $arUrlRewrite['CONDITION'] === $searchCondition){
										\Bitrix\Main\UrlRewriter::delete($siteId, array('CONDITION' => $arUrlRewrite['CONDITION']));
									}
								}
							}
						}

						// add new urlrewrite condition item
						if(strlen($urlCondition)){
							$cntActive = CIBlockElement::GetList(
								array(),
								array(
									'ID' => $arg1['ID'],
									'ACTIVE' => 'Y',
								),
								array()
							);

							if($cntActive){
								static $arCacheSites;
								if(!isset($arCacheSites)){
									$arCacheSites = array();
								}

								foreach($arSitesLids as $siteId){
									$arSite = $arCacheSites[$siteId];
									if(!isset($arSite)){
										$dbSite = CSite::GetByID($siteId);
										$arCacheSites[$siteId] = $arSite = $dbSite->Fetch();
									}

									if($arSite){
										$siteDir = $arSite['DIR'];
										
										// catalog iblock id
										if(defined('URLREWRITE_SEARCH_LANDING_CONDITION_CATALOG_IBLOCK_ID_'.$siteId)){
											$condIblockId = constant('URLREWRITE_SEARCH_LANDING_CONDITION_CATALOG_IBLOCK_ID_'.$siteId);
										}
										if(!$condIblockId){
											$condIblockId = \Bitrix\Main\Config\Option::get(
												self::moduleID,
												'CATALOG_IBLOCK_ID',
												CNextCache::$arIBlocks[$siteId]['aspro_next_catalog']['aspro_next_catalog'][0],
												$siteId
											);
										}
										
										if(isset(CNextCache::$arIBlocksInfo[$condIblockId])){
											$pathFile = str_replace(array('#SITE_DIR#', 'index.php'), array($siteDir, ''), CNextCache::$arIBlocksInfo[$condIblockId]['LIST_PAGE_URL']).'index.php';
											\Bitrix\Main\UrlRewriter::add(
												$siteId,
												array(
													'CONDITION' => '#^/'.$urlCondition.'#',
													'ID' => 'bitrix:catalog',
													'PATH' => $pathFile,
													'RULE' => 'q='.urlencode($queryExample).'&ls='.$arg1['ID']
												)
											);
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if($ELEMENT_ID){
			static $arPropCache = array();
			static $arPropArray=array();

			if(!array_key_exists($IBLOCK_ID, $arPropCache)){
				//Check for MINIMAL_PRICE property
				$rsProperty = CIBlockProperty::GetByID("MINIMUM_PRICE", $IBLOCK_ID);
				$arProperty = $rsProperty->Fetch();
				if($arProperty){
					$arPropCache[$IBLOCK_ID] = $arProperty["ID"];
					$arPropArray["MINIMUM_PRICE"]=$arProperty["ID"];
				}else{
					$arPropCache[$IBLOCK_ID] = false;
				}
				$rsProperty = CIBlockProperty::GetByID("IN_STOCK", $IBLOCK_ID);
				$arProperty = $rsProperty->Fetch();
				if($arProperty){
					$arPropCache[$IBLOCK_ID] = $arProperty["ID"];
					$arPropArray["IN_STOCK"]=$arProperty["ID"];
				}else{
					if(!$arPropCache[$IBLOCK_ID])
						$arPropCache[$IBLOCK_ID] = false;
				}
			}

			if($arPropCache[$IBLOCK_ID]){
				//Compose elements filter
				if($OFFERS_IBLOCK_ID){
					$rsOffers = CIBlockElement::GetList(
						array(),
						array(
							"IBLOCK_ID" => $OFFERS_IBLOCK_ID,
							"PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID,
							"ACTIVE" => "Y"
						),
						false,
						false,
						array("ID")
					);
					while($arOffer = $rsOffers->Fetch())
						$arProductID[] = $arOffer["ID"];

					if (!is_array($arProductID))
						$arProductID = array($ELEMENT_ID);
				}
				else
					$arProductID = array($ELEMENT_ID);

				if($arPropArray["MINIMUM_PRICE"]){
					$minPrice = false;
					$maxPrice = false;
					//Get prices
					$rsPrices = CPrice::GetList(
						array(),
						array(
							"PRODUCT_ID" => $arProductID,
						)
					);
					while($arPrice = $rsPrices->Fetch()){
						if (CModule::IncludeModule('currency') && $strDefaultCurrency != $arPrice['CURRENCY'])
							$arPrice["PRICE"] = CCurrencyRates::ConvertCurrency($arPrice["PRICE"], $arPrice["CURRENCY"], $strDefaultCurrency);

						$PRICE = $arPrice["PRICE"];

						if($minPrice === false || $minPrice > $PRICE)
							$minPrice = $PRICE;

						if($maxPrice === false || $maxPrice < $PRICE)
							$maxPrice = $PRICE;
					}

					//Save found minimal price into property
					if($minPrice !== false){
						CIBlockElement::SetPropertyValuesEx(
							$ELEMENT_ID,
							$IBLOCK_ID,
							array(
								"MINIMUM_PRICE" => $minPrice,
								"MAXIMUM_PRICE" => $maxPrice,
							)
						);
					}
				}
				if($arPropArray["IN_STOCK"]){
					$quantity=0;
					$rsQuantity = CCatalogProduct::GetList(
				        array("QUANTITY" => "DESC"),
				        array("ID" => $arProductID),
				        false,
				        false,
				        array("QUANTITY")
				    );
					while($arQuantity = $rsQuantity->Fetch()){
						if($arQuantity["QUANTITY"]>0)
							$quantity+=$arQuantity["QUANTITY"];
					}
					if($quantity>0){
						$rsPropStock = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"IN_STOCK"));
						if($arPropStock=$rsPropStock->Fetch()){
							$idProp=$arPropStock["ID"];
						}

						CIBlockElement::SetPropertyValuesEx(
							$ELEMENT_ID,
							$IBLOCK_ID,
							array(
								"IN_STOCK" => $idProp,
							)
						);
					}else{
						CIBlockElement::SetPropertyValuesEx(
							$ELEMENT_ID,
							$IBLOCK_ID,
							array(
								"IN_STOCK" => "",
							)
						);
					}
					if(class_exists('\Bitrix\Iblock\PropertyIndex\Manager')){
						\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ELEMENT_ID);
					}
				}
			}
		}
	}

	static function DoIBlockElementAfterDelete($arFields){
		$IBLOCK_ID = $arFields['IBLOCK_ID'];

		if(self::isLandingSearchIblock($IBLOCK_ID)){
			$ID = $arFields['ID'];

			if(CNextCache::$arIBlocksInfo[$IBLOCK_ID]){
				$arSitesLids = CNextCache::$arIBlocksInfo[$IBLOCK_ID]['LID'];

				// search and remove urlrewrite item
				$searchRule = 'ls='.$ID;
				foreach($arSitesLids as $siteId){
					if($arUrlRewrites = \Bitrix\Main\UrlRewriter::getList($siteId, array('ID' => 'bitrix:catalog'))){
						foreach($arUrlRewrites as $arUrlRewrite){
							if($arUrlRewrite['RULE'] && strpos($arUrlRewrite['RULE'], $searchRule) !== false){
								\Bitrix\Main\UrlRewriter::delete($siteId, array('CONDITION' => $arUrlRewrite['CONDITION']));
							}
						}
					}
				}
			}
		}
	}

	static public function isLandingSearchIblock($IBLOCK_ID){
		return isset(CNextCache::$arIBlocksInfo[$IBLOCK_ID]) && strpos(CNextCache::$arIBlocksInfo[$IBLOCK_ID]['CODE'], 'aspro_next_search') !== false;
	}

	protected static $handlerDisallow = 0;

	public static function disableHandler()
	{
	  self::$handlerDisallow--;
	}

	public static function enableHandler()
	{
	  self::$handlerDisallow++;
	}

	public static function isEnabledHandler()
	{
	  return (self::$handlerDisallow >= 0);
	}

	static function setStoreProductHandler($ID, $arFields){
		static $stores_quantity_product, $updateFromCatalog;
		$arProduct = CCatalogStoreProduct::GetList(array(), array('ID' => $ID), false, false, array('PRODUCT_ID'))->Fetch();
		if($arProduct['PRODUCT_ID'] && \Bitrix\Main\Config\Option::get(self::moduleID, "EVENT_SYNC", "N") == "Y")
		{
			if(isset($arFields['AMOUNT']) && $arFields['AMOUNT'])
				$stores_quantity_product += $arFields['AMOUNT'];

			if($updateFromCatalog !== NULL)
			{
				/*set flag*/
    	   		self::disableHandler();
    	   	}

			CCatalogProduct::Update($arProduct['PRODUCT_ID'], array("QUANTITY" => $stores_quantity_product));

			if($updateFromCatalog !== NULL)
			{
				/*unset flag*/
				self::enableHandler();
			}
		}
	}

	static function setStockProduct($ID, $arFields){
		/*check flag*/
		if (!self::isEnabledHandler())
           return;

       	/*set flag*/
       	self::disableHandler();

		//Get iblock element
		$rsPriceElement = CIBlockElement::GetList(
			array(),
			array(
				"ID" => $ID,
			),
			false,
			false,
			array("ID", "IBLOCK_ID")
		);

		if($arPriceElement = $rsPriceElement->Fetch()){
			$arCatalog = CCatalog::GetByID($arPriceElement["IBLOCK_ID"]);
			if(is_array($arCatalog)){
				//Check if it is offers iblock
				if($arCatalog["OFFERS"] == "Y"){
					//Find product element
					$rsElement = CIBlockElement::GetProperty(
						$arPriceElement["IBLOCK_ID"],
						$arPriceElement["ID"],
						"sort",
						"asc",
						array("ID" => $arCatalog["SKU_PROPERTY_ID"])
					);
					$arElement = $rsElement->Fetch();
					if($arElement && $arElement["VALUE"] > 0)
					{
						$ELEMENT_ID = $arElement["VALUE"];
						$IBLOCK_ID = $arCatalog["PRODUCT_IBLOCK_ID"];
						$OFFERS_IBLOCK_ID = $arCatalog["IBLOCK_ID"];
						$OFFERS_PROPERTY_ID = $arCatalog["SKU_PROPERTY_ID"];
					}
				}
				//or iblock which has offers
				elseif($arCatalog["OFFERS_IBLOCK_ID"] > 0){
					$ELEMENT_ID = $arPriceElement["ID"];
					$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
					$OFFERS_IBLOCK_ID = $arCatalog["OFFERS_IBLOCK_ID"];
					$OFFERS_PROPERTY_ID = $arCatalog["OFFERS_PROPERTY_ID"];
				}
				//or it's regular catalog
				else{
					$ELEMENT_ID = $arPriceElement["ID"];
					$IBLOCK_ID = $arPriceElement["IBLOCK_ID"];
					$OFFERS_IBLOCK_ID = false;
					$OFFERS_PROPERTY_ID = false;
				}
			}
		}
		if($ELEMENT_ID){
			static $arPropCache = array();
			static $arPropArray=array();

			if(!array_key_exists($IBLOCK_ID, $arPropCache)){
				//Check for IN_STOCK property
				$rsProperty = CIBlockProperty::GetByID("IN_STOCK", $IBLOCK_ID);
				$arProperty = $rsProperty->Fetch();
				if($arProperty){
					$arPropCache[$IBLOCK_ID] = $arProperty["ID"];
					$arPropArray["IN_STOCK"]=$arProperty["ID"];
				}else{
					if(!$arPropCache[$IBLOCK_ID])
						$arPropCache[$IBLOCK_ID] = false;
				}
			}
			if($arPropCache[$IBLOCK_ID]){
				//Compose elements filter
				$arProductID = array();
				if($OFFERS_IBLOCK_ID){
					$rsOffers = CIBlockElement::GetList(
						array(),
						array(
							"IBLOCK_ID" => $OFFERS_IBLOCK_ID,
							"PROPERTY_".$OFFERS_PROPERTY_ID => $ELEMENT_ID,
							"ACTIVE" => "Y"
						),
						false,
						false,
						array("ID")
					);
					while($arOffer = $rsOffers->Fetch())
						$arProductID[] = $arOffer["ID"];

					if (!$arProductID)
						$arProductID = array($ELEMENT_ID);
				}
				else
					$arProductID = array($ELEMENT_ID);


				if($arPropArray["IN_STOCK"]){
					/* sync quantity product by stores start */
					if($arProductID && \Bitrix\Main\Config\Option::get('catalog', 'default_use_store_control', 'N') == 'N' && \Bitrix\Main\Config\Option::get(self::moduleID, "EVENT_SYNC", "N") == "Y")
					{
						static $bStores;
						if(class_exists('CCatalogStore')){
							if(!$bStores)
							{
								$dbRes = CCatalogStore::GetList(array(), array(), false, false, array());
								if($c = $dbRes->SelectedRowsCount()){
									$bStores = true;
								}
							}
						}
						if($bStores)
						{
							static $updateFromCatalog;
							$updateFromCatalog = true;

							foreach($arProductID as $id)
							{
								$quantity_stores = 0;
								$rsStore = CCatalogStore::GetList(array(), array('PRODUCT_ID' => $id), false, false, array('ID', 'PRODUCT_AMOUNT'));
								while($arStore = $rsStore->Fetch())
								{
									$quantity_stores += $arStore['PRODUCT_AMOUNT'];
								}
								CCatalogProduct::Update($id, array("QUANTITY" => $quantity_stores));
							}
						}
					}
					/* sync quantity product by stores end */

					$quantity=0;
					$rsQuantity = CCatalogProduct::GetList(
				        array("QUANTITY" => "DESC"),
				        array("ID" => $arProductID),
				        false,
				        false,
				        array("QUANTITY")
				    );
					while($arQuantity = $rsQuantity->Fetch()){
						if($arQuantity["QUANTITY"]>0)
							$quantity+=$arQuantity["QUANTITY"];
					}
					if($quantity>0){
						$rsPropStock = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"IN_STOCK"));
						if($arPropStock=$rsPropStock->Fetch()){
							$idProp=$arPropStock["ID"];
						}

						CIBlockElement::SetPropertyValuesEx(
							$ELEMENT_ID,
							$IBLOCK_ID,
							array(
								"IN_STOCK" => $idProp,
							)
						);
					}else{
						CIBlockElement::SetPropertyValuesEx(
							$ELEMENT_ID,
							$IBLOCK_ID,
							array(
								"IN_STOCK" => "",
							)
						);
					}
					if(class_exists('\Bitrix\Iblock\PropertyIndex\Manager')){
						\Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($IBLOCK_ID, $ELEMENT_ID);
					}
				}
			}
		}

		/*unset flag*/
		self::enableHandler();
	}

	static function CurrencyFormatHandler($price, $currency){
		if(!defined('ADMIN_SECTION') && !CSite::inDir(SITE_DIR.'personal/orders'))
		{
			$arCurFormat = CCurrencyLang::GetFormatDescription($currency);

			$intDecimals = $arCurFormat['DECIMALS'];
		    if (CCurrencyLang::isAllowUseHideZero() && $arCurFormat['HIDE_ZERO'] == 'Y')
		    {
		        if (round($price, $arCurFormat["DECIMALS"]) == round($price, 0))
		            $intDecimals = 0;
		    }
		    $price = number_format($price, $intDecimals, $arCurFormat['DEC_POINT'], $arCurFormat['THOUSANDS_SEP']);
		    if ($arCurFormat['THOUSANDS_VARIANT'] == CCurrencyLang::SEP_NBSPACE)
		        $price = str_replace(' ', '&nbsp;', $price);
		    $arFormatString = explode('#', $arCurFormat['FORMAT_STRING']);
		    $arFormatString[1] = '<span class=\'price_currency\'>'.$arFormatString[1].'</span>';
			$arCurFormat['FORMAT_STRING'] = '#'.$arFormatString[1];

		    return preg_replace('/(^|[^&])#/', '${1}'.'<span class=\'price_value\'>'.$price.'</span>', $arCurFormat['FORMAT_STRING']);
		}
	}

	public static function OnBeforeChangeFileHandler($path, $site){
		if(
			defined('ADMIN_SECTION')
			 && $_SERVER['REQUEST_METHOD'] === 'POST'
			 && isset($_REQUEST['component_name'])
			 && $_REQUEST['component_name'] === 'bitrix:catalog'
			 && isset($_REQUEST['src_site'])
			 && ($siteId = $_REQUEST['src_site'])
		){
			$_SESSION['saved'] = array(
				$siteId => array()
			);

			// search and remove urlrewrite item
			$searchRule = '&ls=';
			if($arUrlRewrites = \Bitrix\Main\UrlRewriter::getList($siteId, array('ID' => 'bitrix:catalog'))){
				foreach($arUrlRewrites as $arUrlRewrite){
					if($arUrlRewrite['RULE'] && strpos($arUrlRewrite['RULE'], $searchRule) !== false){
						$_SESSION['saved'][$siteId][] = array(
							'CONDITION' => $arUrlRewrite['CONDITION'],
							'ID' => $arUrlRewrite['ID'],
							'PATH' => $arUrlRewrite['PATH'],
							'RULE' => $arUrlRewrite['RULE'],
						);

						\Bitrix\Main\UrlRewriter::delete($siteId, array('CONDITION' => $arUrlRewrite['CONDITION']));
					}
				}
			}
		}

		return true;
	}

	public static function OnChangeFileHandler($path, $site){
		if(
			defined('ADMIN_SECTION')
			 && $_SERVER['REQUEST_METHOD'] === 'POST'
			 && isset($_REQUEST['component_name'])
			 && $_REQUEST['component_name'] === 'bitrix:catalog'
			 && isset($_REQUEST['src_site'])
			 && ($siteId = $_REQUEST['src_site'])
			 && isset($_SESSION['saved'])
			 && $_SESSION['saved']
		){
			foreach($_SESSION['saved'] as $siteId => $arUrlRewrites){
				foreach($arUrlRewrites as $arUrlRewrite){
					\Bitrix\Main\UrlRewriter::add($siteId, $arUrlRewrite);
				}
			}

			unset($_SESSION['saved']);
		}

		return true;
	}

	static function OnEndBufferContentHandler(&$content)
	{
		if(!defined('ADMIN_SECTION') && !defined('WIZARD_SITE_ID'))
		{
			global $SECTION_BNR_CONTENT, $arRegion, $APPLICATION;

			// if((strpos($APPLICATION->GetCurPage(), 'ajax') === false && strpos($APPLICATION->GetCurPage(), 'bitrix') === false))
			// {
				foreach(CNextRegionality::$arSeoMarks as $mark => $field)
				{
					if(strpos($content, $mark) !== false)
					{
						if($arRegion)
						{
							if(is_array($arRegion[$field])){
								$content = str_replace(array($mark, str_replace('#REGION_TAG_', '#REGION_STRIP_TAG_', $mark)), array($arRegion[$field]['TEXT'], strip_tags($arRegion[$field]['TEXT'])), $content);
							}
							else{
								$content = str_replace(array($mark, str_replace('#REGION_TAG_', '#REGION_STRIP_TAG_', $mark)), array($arRegion[$field], strip_tags($arRegion[$field])), $content);
							}
						}
						else{
							$content = str_replace(array($mark, str_replace('#REGION_TAG_', '#REGION_STRIP_TAG_', $mark)), '', $content);
						}
					}
				}
			// }

			//replace text/javascript for html5 validation w3c
			$content = str_replace(' type="text/javascript"', '', $content);
			$content = str_replace(' type=\'text/javascript\'', '', $content);
			$content = str_replace(' type="text/css"', '', $content);
			$content = str_replace(' type=\'text/css\'', '', $content);
			$content = str_replace(' charset="utf-8"', '', $content);

			if($SECTION_BNR_CONTENT)
			{
				$start = strpos($content, '<!--title_content-->');
				if($start>0)
				{
					$end = strpos($content, '<!--end-title_content-->');

					if(($end>0) && ($end>$start))
					{
						if(defined("BX_UTF") && BX_UTF === true)
							$content = CNext::utf8_substr_replace($content, "", $start, $end-$start);
						else
							$content = substr_replace($content, "", $start, $end-$start);
					}
				}
				$content = str_replace("body class=\"", "body class=\"with_banners ", $content);
			}

			//process recaptcha
			if(\Aspro\Functions\CAsproNextReCaptcha::checkRecaptchaActive())
			{
				$count = 0;
				$contentReplace = preg_replace_callback(
					'!(<img\s[^>]*?src[^>]*?=[^>]*?)(\/bitrix\/tools\/captcha\.php\?(captcha_code|captcha_sid)=[0-9a-z]+)([^>]*?>)!',
					function ($arImage)
					{
						//replace src and style
						$arImage = array(
							'tag' => $arImage[1],
							'src' => $arImage[2],
							'tail' => $arImage[4],
						);

						return \Aspro\Functions\CAsproNextReCaptcha::callbackReplaceImage($arImage);
					},
					$html,
					-1,
					$count
				);

				if($count <= 0 || !$contentReplace)
					return;

				$html = $contentReplace;
				unset($contentReplace);

				$captcha_public_key = \Aspro\Functions\CAsproNextReCaptcha::getPublicKey();

				$ind = 0;
				while ($ind++ <= $count)
				{
					$uniqueId = randString(4);
					$html = preg_replace(
						'!<input\s[^>]*?name[^>]*?=[^>]*?captcha_word[^>]*?>!',
						"<div id='recaptcha-$uniqueId'
						class='g-recaptcha'
						data-sitekey='$captcha_public_key'></div>
					<script type='text/javascript' data-skip-moving='true'>
						if(typeof renderRecaptchaById !== 'undefined')
							renderRecaptchaById('recaptcha-$uniqueId');
					</script>", $html, 1
					);
				}

				$arSearchMessages = array(
					\Bitrix\Main\Localization\Loc::getMessage('FORM_CAPRCHE_TITLE_RECAPTCHA'),
					\Bitrix\Main\Localization\Loc::getMessage('FORM_CAPRCHE_TITLE_RECAPTCHA2'),
					\Bitrix\Main\Localization\Loc::getMessage('FORM_CAPRCHE_TITLE_RECAPTCHA3'),
				);

				$html = str_replace($arSearchMessages, \Bitrix\Main\Localization\Loc::getMessage('FORM_GENERAL_RECAPTCHA'), $html);
			}
		}
	}

	public static function OnPageStartHandler(){
		if(defined("ADMIN_SECTION")){
			return;
		}

		// check search landing with url condition
		if($_SERVER['SCRIPT_NAME'] === '/bitrix/urlrewrite.php' && isset($_REQUEST['ls']) && isset($_REQUEST['q'])){
			if($bLandingWithUrlCondition = intval($_REQUEST['ls']) > 0 && strlen($_REQUEST['q'])){
				$context = \Bitrix\Main\Context::getCurrent();
				$server = $context->getServer();
		        $server_array = $server->toArray();
		        $server_array['REQUEST_URI'] = $_SERVER['REQUEST_URI'] = $_SERVER['REAL_FILE_PATH'].'?'.str_replace(urlencode(urlencode($_REQUEST['q'])), urlencode($_REQUEST['q']), $_SERVER['QUERY_STRING']);
		        $server->set($server_array);
                $context->initialize(new \Bitrix\Main\HttpRequest($server, $_GET, $_POST, $_FILES, $_COOKIE), $context->getResponse(), $server);
                $GLOBALS['APPLICATION']->reinitPath();
				$GLOBALS['APPLICATION']->SetCurPage($_SERVER['REAL_FILE_PATH'], str_replace(urlencode(urlencode($_REQUEST['q'])), urlencode($_REQUEST['q']), $_SERVER['QUERY_STRING']));
			}
		}

		if(!\Aspro\Functions\CAsproNextReCaptcha::checkRecaptchaActive()){
			return;
		}

		$captcha_public_key = \Aspro\Functions\CAsproNextReCaptcha::getPublicKey();
		$assets = Asset::getInstance();

		$arCaptchaProp = array();
		$arCaptchaProp['recaptchaColor'] = strtolower(Option::get(self::moduleID, 'GOOGLE_RECAPTCHA_COLOR', 'LIGHT'));
		$arCaptchaProp['recaptchaLogoShow'] = strtolower(Option::get(self::moduleID, 'GOOGLE_RECAPTCHA_SHOW_LOGO', 'Y'));
		$arCaptchaProp['recaptchaSize'] = strtolower(Option::get(self::moduleID, 'GOOGLE_RECAPTCHA_SIZE', 'NORMAL'));
		$arCaptchaProp['recaptchaBadge'] = strtolower(Option::get(self::moduleID, 'GOOGLE_RECAPTCHA_BADGE', 'BOTTOMRIGHT'));
		$arCaptchaProp['recaptchaLang'] = LANGUAGE_ID;

		//add global object asproRecaptcha
		$scripts = "<script type='text/javascript' data-skip-moving='true'>";
		$scripts .= "window['asproRecaptcha'] = {params: ".\CUtil::PhpToJsObject($arCaptchaProp).",key: '".$captcha_public_key."'};";
		$scripts .= "</script>";
		$assets->addString($scripts);

		//add scripts
		$scriptsDir = $_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.self::moduleID.'/captcha/';
		$scriptsPath = File::isFileExists($scriptsDir.'recaptcha.min.js')? $scriptsDir.'recaptcha.min.js' : $scriptsDir.'recaptcha.js';
		$scriptCode = File::getFileContents($scriptsPath);
		$scripts = "<script type='text/javascript' data-skip-moving='true'>".$scriptCode."</script>";
		$assets->addString($scripts);

		$scriptsPath = File::isFileExists($scriptsDir . 'replacescript.min.js') ? $scriptsDir . 'replacescript.min.js' : $scriptsDir . 'replacescript.js';
		$scriptCode = File::getFileContents($scriptsPath);
		$scripts = "<script type='text/javascript' data-skip-moving='true'>".$scriptCode."</script>";
		$assets->addString($scripts);

		//process post request
		$application = Application::getInstance();
		$request = $application->getContext()->getRequest();
		$arPostData = $request->getPostList()->toArray();

		$needReInit = false;

		if($arPostData['g-recaptcha-response'])
		{
			if($code = \Aspro\Functions\CAsproNextReCaptcha::getCodeByPostList($arPostData))
			{
				$_REQUEST['captcha_word'] = $_POST['captcha_word'] = $code;
				$needReInit = true;
			}
		}

		foreach($arPostData as $key => $arPost)
		{
			if(!is_array($arPost) || !$arPost['g-recaptcha-response'])
				continue;

			if($code = \Aspro\Functions\CAsproNextReCaptcha::getCodeByPostList($arPost))
			{
				$_REQUEST[$key]['captcha_word'] = $_POST[$key]['captcha_word'] = $code;
				$needReInit = true;
			}
		}

		if($needReInit)
		{
			\Aspro\Functions\CAsproNextReCaptcha::reInitContext($application, $request);
		}
	}

	static function OnIBlockPropertyBuildListStoresHandler(){
		return array(
			"PROPERTY_TYPE"      => "S",
			"USER_TYPE"         => "SAsproListStores",
			"DESCRIPTION"      => Loc::getMessage("STORES_LINK_PROP_TITLE"),
			"GetPropertyFieldHtml"   => array("CNextEvents", "GetPropertyFieldHtmlStoresHandler"),
			"GetPropertyFieldHtmlMulty"   => array("CNextEvents", "GetPropertyFieldHtmlStoresHandlerMulty"),
		);
	}

	static function GetPropertyFieldHtmlStoresHandler($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(\Bitrix\Main\Loader::includeModule('catalog'))
		{
			$cache["STORES"] = array();
			$rsStore = CCatalogStore::GetList( array("SORT" => "ASC"), array() );
			while($arStore = $rsStore->GetNext())
			{
				$cache["STORES"][] = $arStore;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
			if($arProperty['MULTIPLE'] == 'Y')
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			else
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';

			$html .= '<option value="component" '.($val == "component" ? 'selected' : '').'>'.Loc::getMessage("FROM_COMPONENTS_TITLE").'</option>';
			foreach($cache["STORES"] as $arStore)
			{
				$html .= '<option value="'.$arStore["ID"].'"';
				if($val == $arStore["~ID"])
					$html .= ' selected';
				$html .= '>'.$arStore["TITLE"].'</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}

	static function GetPropertyFieldHtmlStoresHandlerMulty($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(\Bitrix\Main\Loader::includeModule('catalog'))
		{
			$cache["STORES"] = array();
			$rsStore = CCatalogStore::GetList( array("SORT" => "ASC"), array() );
			while($arStore = $rsStore->GetNext())
			{
				$cache["STORES"][] = $arStore;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$arValues = array();
			if($value && is_array($value))
			{
				foreach($value as $arValue)
				{
					$arValues[] = $arValue["VALUE"];
				}
			}
			else
				$arValues[] = $arProperty["DEFAULT_VALUE"];

			if($arProperty['MULTIPLE'] == 'Y')
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			else
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';

			$html .= '<option value="component" '.(in_array("component", $arValues) ? 'selected' : '').'>'.Loc::getMessage("FROM_COMPONENTS_TITLE").'</option>';
			foreach($cache["STORES"] as $arStore)
			{
				$html .= '<option value="'.$arStore["ID"].'"';
				if(in_array($arStore["~ID"], $arValues))
					$html .= ' selected';
				$html .= '>'.$arStore["TITLE"].'</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}

	static function OnIBlockPropertyBuildListLocationsHandler(){
		return array(
			"PROPERTY_TYPE"      => "S",
			"USER_TYPE"         => "SAsproListLocations",
			"DESCRIPTION"      => Loc::getMessage("LOCATIONS_LINK_PROP_TITLE"),
			"GetPropertyFieldHtml"   => array("CNextEvents", "GetPropertyFieldHtmlLocationsHandler"),
		);
	}

	static function GetPropertyFieldHtmlLocationsHandler($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(\Bitrix\Main\Loader::includeModule('sale'))
		{
			$cache["LOCATIONS"] = array();
			$rsLoc = CSaleLocation::GetList(array("CITY_NAME" => "ASC"), array());
			while($arLoc = $rsLoc->GetNext())
			{
				if($arLoc["CITY_NAME"])
					$cache["LOCATIONS"][$arLoc["ID"]] = $arLoc;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
			$html = '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">
			<option value="" >-</option>';
			foreach($cache["LOCATIONS"] as $arLocation)
			{
				$html .= '<option value="'.$arLocation["ID"].'"';
				if($val == $arLocation["~ID"])
					$html .= ' selected';
				$html .= '>'.$arLocation["CITY_NAME"].'</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}

	static function OnIBlockPropertyBuildListPricesHandler(){
		return array(
			"PROPERTY_TYPE"      => "S",
			"USER_TYPE"         => "SAsproListPrices",
			"DESCRIPTION"      => Loc::getMessage("PRICES_LINK_PROP_TITLE"),
			"GetPropertyFieldHtml"   => array("CNextEvents", "GetPropertyFieldHtmlPricesHandler"),
			"GetPropertyFieldHtmlMulty"   => array("CNextEvents", "GetPropertyFieldHtmlPricesHandlerMulty"),
		);
	}

	static function GetPropertyFieldHtmlPricesHandler($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(\Bitrix\Main\Loader::includeModule('catalog'))
		{
			$cache["PRICE"] = array();
			$rsPrice = CCatalogGroup::GetList( array("SORT" => "ASC"), array() );
			while($arPrice = $rsPrice->GetNext())
			{
				$cache["PRICE"][] = $arPrice;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$val = ($value["VALUE"] ? $value["VALUE"] : $arProperty["DEFAULT_VALUE"]);
			$html = '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">
			<option value="component" '.($val == "component" ? 'selected' : '').'>'.Loc::getMessage("FROM_COMPONENTS_TITLE").'</option>';
			foreach($cache["PRICE"] as $arPrice)
			{
				$html .= '<option value="'.$arPrice["ID"].'"';
				if($val == $arPrice["~ID"])
					$html .= ' selected';
				$html .= '>'.$arPrice["NAME"].'</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}

	static function GetPropertyFieldHtmlPricesHandlerMulty($arProperty, $value, $strHTMLControlName){
		static $cache = array();
		$html = '';
		if(\Bitrix\Main\Loader::includeModule('catalog'))
		{
			$cache["PRICE"] = array();
			$rsPrice = CCatalogGroup::GetList( array("SORT" => "ASC"), array() );
			while($arPrice = $rsPrice->GetNext())
			{
				$cache["PRICE"][] = $arPrice;
			}

			$varName = str_replace("VALUE", "DESCRIPTION", $strHTMLControlName["VALUE"]);
			$arValues = array();
			if($value && is_array($value))
			{
				foreach($value as $arValue)
				{
					$arValues[] = $arValue["VALUE"];
				}
			}
			else
				$arValues[] = $arProperty["DEFAULT_VALUE"];

			if($arProperty['MULTIPLE'] == 'Y')
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'[]" multiple size="6" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';
			else
				$html .= '<select name="'.$strHTMLControlName["VALUE"].'" onchange="document.getElementById(\'DESCR_'.$varName.'\').value=this.options[this.selectedIndex].text">';

			$html .= '<option value="component" '.(in_array("component", $arValues) ? 'selected' : '').'>'.Loc::getMessage("FROM_COMPONENTS_TITLE").'</option>';
			foreach($cache["PRICE"] as $arPrice)
			{
				$html .= '<option value="'.$arPrice["ID"].'"';
				if(in_array($arPrice["~ID"], $arValues))
					$html .= ' selected';
				$html .= '>'.$arPrice["NAME"].'</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}

	static function OnIBlockPropertyBuildCustomFilterHandler(){
		return array(
			'PROPERTY_TYPE'      => 'S',
			'USER_TYPE'         => 'SAsproCustomFilter',
			'DESCRIPTION'      => Loc::getMessage('CUSTOM_FILTER_PROP_TITLE'),
			'GetPropertyFieldHtml'   => array('CNextEvents', 'GetPropertyFieldHtmlCustomFilterHandler'),
			'GetSettingsHTML'   => array('CNextEvents', 'GetSettingsHTMLCustomFilterHandler'),
			'PrepareSettings'   => array('CNextEvents', 'PrepareSettingsCustomFilterHandler'),
		);
	}

	static function GetPropertyFieldHtmlCustomFilterHandler($arProperty, $value, $strHTMLControlName){
		static $cache, $jsFile;

		if(!isset($cache)){
			$cache = array();
			$GLOBALS['APPLICATION']->AddHeadScript('/bitrix/components/bitrix/catalog.section/settings/filter_conditions/script.js');
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].($jsFile = '/bitrix/components/bitrix/catalog.section/settings/filter_conditions/script.js'))){
				unset($jsFile);
			}
		}

		if($jsFile){
			if(\Bitrix\Main\Loader::includeModule('fileman')){
				$val = strlen($value['VALUE']) ? $value['VALUE'] : '[]';

				$iblockId = isset($arProperty['USER_TYPE_SETTINGS']) && isset($arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID']) ? $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'] : 0;

				if(!isset($cache[$iblockId])){
					if(\Bitrix\Main\Loader::includeModule('catalog')){
						$arInfo = \CCatalogSKU::GetInfoByProductIBlock($iblockId);
						$offersIblockId = $cache[$iblockId] = $arInfo ? $arInfo['IBLOCK_ID'] : false;
					}
				}
				else{
					$offersIblockId = $cache[$iblockId];
				}

				$html = '<input type="hidden" id="'.$strHTMLControlName['VALUE'].'" name="'.$strHTMLControlName['VALUE'].'" value="'.htmlspecialcharsbx(is_array($val) ? reset($val) : $val).'" data-bx-property-id="'.$arProperty['CODE'].'" data-bx-comp-prop="true" />';
				$html .= "\n".'<script>'.
					'var tv = BX(\'tr_PROPERTY_'.$arProperty['ID'].'\');'.
					'if(tv){'.
						'var iv = BX(\''.$strHTMLControlName['VALUE'].'\');'.
						'if(iv){'.
							'var td = BX.findParent(iv, {tag: \'td\'});'.
							'if(td){'.
								'var tdd = BX.findChildren(td, {tag: \'div\'}, true);'.
								'if(tdd){'.
									'for(var i in tdd){'.
										'BX.cleanNode(tdd[i]);'.
									'}'.
								'}'.
								'initFilterConditionsControl({'.
									'data: \'{"iblockId":'.$iblockId.($offersIblockId ? ',"offersIblockId":'.$offersIblockId : '').'}\','.
									'oCont: td,'.
									'oInput: iv,'.
									'propertyID: \''.$arProperty['CODE'].'\','.
									'propertyParams: {'.
										'DEFAULT: \'\','.
										'ID: \''.$arProperty['CODE'].'\','.
										'JS_DATA: \'{"iblockId":'.$iblockId.($offersIblockId ? ',"offersIblockId":'.$offersIblockId : '').'}\','.
										'JS_EVENT: \'initFilterConditionsControl\','.
										'JS_FILE: \'/bitrix/components/bitrix/catalog.section/settings/filter_conditions/script.js\','.
										'NAME: \''.Loc::getMessage('CUSTOM_FILTER_PROP_NAME').'\','.
										'JS_MESSAGES: \'{"invalid": "'.Loc::getMessage('CUSTOM_FILTER_PROP_INVALID').'"}\','.
										'MULTIPLE: \'N\','.
										'PARENT: \'DATA_SOURCE\','.
										'ROWS: 0,'.
										'TOOLTIP: \'\','.
										'TYPE: \'CUSTOM\','.
										'_propId: \''.$strHTMLControlName['VALUE'].'\''.
									'}'.
								'});'.
							'}'.
						'}'.
					'}'.
					'</script>';
			}
		}
		else{
			$html = '<input type="text" id="'.$strHTMLControlName['VALUE'].'" name="'.$strHTMLControlName['VALUE'].'" value="'.htmlspecialcharsbx(is_array($val) ? reset($val) : $val).'" data-bx-property-id="'.$arProperty['CODE'].'" data-bx-comp-prop="true" />';
		}

		return $html;
	}

	function PrepareSettingsCustomFilterHandler($arFields){
		$arFields['USER_TYPE_SETTINGS']['IBLOCK_ID'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IBLOCK_ID']) ? intval($arFields['USER_TYPE_SETTINGS']['IBLOCK_ID']) : false;

		$arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID'] = isset($arFields['USER_TYPE_SETTINGS']) && isset($arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID']) ? trim($arFields['USER_TYPE_SETTINGS']['IBLOCK_TYPE_ID']) : false;

		$arFields['FILTRABLE'] = $arFields['SMART_FILTER'] = $arFields['SEARCHABLE'] = $arFields['MULTIPLE'] = 'N';

        return $arFields;
	}

	function GetSettingsHTMLCustomFilterHandler($arProperty, $strHTMLControlName, &$arPropertyFields){
		$arPropertyFields = array(
            'HIDE' => array(
            	'FILTRABLE',
            	'DEFAULT_VALUE',
            	'SEARCHABLE',
            	'MULTIPLE_CNT',
            	'COL_COUNT',
            	'MULTIPLE',
            	'WITH_DESCRIPTION',
            	'FILTER_HINT',
            ),
            'SET' => array(
            	'FILTRABLE' => 'N',
            	'SEARCHABLE' => 'N',
            	'MULTIPLE_CNT' => '1',
            	'MULTIPLE' => 'N',
            	'WITH_DESCRIPTION' => 'N',
            ),
        );

		$iblockId = $arProperty['USER_TYPE_SETTINGS']['IBLOCK_ID'];
		$b_f = ($arProperty['PROPERTY_TYPE'] == 'G' || ($arProperty['PROPERTY_TYPE'] == 'E' && $arProperty['USER_TYPE'] == BT_UT_SKU_CODE) ? array('!ID' => $iblockId) : array());
		$html = '<td width="40%">'.GetMessage('BT_ADM_IEP_PROP_LINK_IBLOCK').'</td>'.
			'<td>'.GetIBlockDropDownList($iblockId, $strHTMLControlName['NAME'].'[IBLOCK_TYPE_ID]', $strHTMLControlName['NAME'].'[IBLOCK_ID]', $b_f, 'class="adm-detail-iblock-types"', 'class="adm-detail-iblock-list"').'</td>';

		return $html;
	}

	function OnBeforeBasketUpdateHandler($ID, &$arFields){
		/*if((int)$arFields["ORDER_ID"] <= 0)
		{

		}*/
	}

	function OnGetOptimalPriceHandler($intProductID, $quantity = 1, $arUserGroups = array(), $renewal = "N", $priceList = array(), $siteID = false, $arDiscountCoupons = false){
		global $APPLICATION, $arRegion;
		static $priceTypeCache = array();
		if(!$arRegion)
		{
			if(\Bitrix\Main\Loader::includeModule('aspro.next'))
			{
				$arRegion = CNextRegionality::getCurrentRegion(); //get current region from regionality module
			}
		}
		if($arRegion)
		{
			static $resultCurrency, $arPricesID;

			$intProductID = (int)$intProductID;
			if ($intProductID <= 0)
			{
				$APPLICATION->ThrowException(Loc::getMessage("BT_MOD_CATALOG_PROD_ERR_PRODUCT_ID_ABSENT"), "NO_PRODUCT_ID");
				return false;
			}

			$quantity = (float)$quantity;
			if ($quantity <= 0)
			{
				$APPLICATION->ThrowException(Loc::getMessage("BT_MOD_CATALOG_PROD_ERR_QUANTITY_ABSENT"), "NO_QUANTITY");
				return false;
			}

			$intIBlockID = (int)CIBlockElement::GetIBlockByID($intProductID);
			if($intIBlockID <= 0)
			{
				$APPLICATION->ThrowException(
					Loc::getMessage(
						'BT_MOD_CATALOG_PROD_ERR_ELEMENT_ID_NOT_FOUND',
						array('#ID#' => $intProductID)
					),
					'NO_ELEMENT'
				);
				return false;
			}

			if(class_exists('\Bitrix\Sale\Internals\SiteCurrencyTable'))
				$resultCurrency = \Bitrix\Sale\Internals\SiteCurrencyTable::getSiteCurrency(($siteID ? $siteID : SITE_ID));

			if($resultCurrency === NULL)
				$resultCurrency = \Bitrix\Currency\CurrencyManager::getBaseCurrency();

			if(empty($resultCurrency))
			{
				$APPLICATION->ThrowException(Loc::getMessage("BT_MOD_CATALOG_PROD_ERR_NO_BASE_CURRENCY"), "NO_BASE_CURRENCY");
				return false;
			}

			if($arPricesID === NULL)
			{
				$arPricesID = array();
				if($arRegion['LIST_PRICES'])
				{
					foreach($arRegion['LIST_PRICES'] as $arPrice)
					{
						if(is_array($arPrice))
						{
							if($arPrice['CAN_BUY'] == 'Y')
								$arPricesID[] = $arPrice['ID'];
						}
					}
				}
				$strRegionPrices = reset($arRegion['LIST_PRICES']);

				if(!$arPricesID && ($strRegionPrices == 'component' || $strRegionPrices == ''))
				{
					 if (!is_array($arUserGroups) && (int)$arUserGroups.'|' == (string)$arUserGroups.'|')
			            $arUserGroups = array((int)$arUserGroups);

			        if (!is_array($arUserGroups))
			            $arUserGroups = array();

			        if (!in_array(2, $arUserGroups))
			            $arUserGroups[] = 2;
			        \Bitrix\Main\Type\Collection::normalizeArrayValuesByInt($arUserGroups);

					$cacheKey = 'U'.implode('_', $arUserGroups);
		            if (!isset($priceTypeCache[$cacheKey]))
		            {
		                $priceTypeCache[$cacheKey] = array();
		                $priceIterator = \Bitrix\Catalog\GroupAccessTable::getList(array(
		                    'select' => array('CATALOG_GROUP_ID'),
		                    'filter' => array('@GROUP_ID' => $arUserGroups, '=ACCESS' => \Bitrix\Catalog\GroupAccessTable::ACCESS_BUY),
		                    'order' => array('CATALOG_GROUP_ID' => 'ASC')
		                ));
		                while ($priceType = $priceIterator->fetch())
		                {
		                    $priceTypeId = (int)$priceType['CATALOG_GROUP_ID'];
		                    $priceTypeCache[$cacheKey][$priceTypeId] = $priceTypeId;
		                    unset($priceTypeId);
		                }
		                unset($priceType, $priceIterator);
		            }
		            if (empty($priceTypeCache[$cacheKey]))
		                return false;
		            $arPricesID = $priceTypeCache[$cacheKey];
				}
			}
			if($arPricesID)
			{
				if(!isset($priceList) || !is_array($priceList))
					$priceList = array();

				/*if($arRegion['LIST_STORES'] && reset($arRegion['LIST_STORES']) != 'component') // check product quantity
				{
					$quantity_stores = 0;
					$arSelect = array('ID', 'PRODUCT_AMOUNT');
					$arFilter = array(
						'ID' => $arRegion['LIST_STORES'],
						'PRODUCT_ID' => $intProductID,
					);
					$rsStore = CCatalogStore::GetList(array(), $arFilter, false, false, $arSelect);
					while($arStore = $rsStore->Fetch())
					{
						$quantity_stores += $arStore['PRODUCT_AMOUNT'];
					}
					if(!$quantity_stores)
						return false;
				}*/

				$arSelect = array('ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY');
				$arFilter = array(
					'=PRODUCT_ID' => $intProductID,
					'@CATALOG_GROUP_ID' => $arPricesID,
					array(
						'LOGIC' => 'OR',
						'<=QUANTITY_FROM' => $quantity,
						'=QUANTITY_FROM' => null
					),
					array(
						'LOGIC' => 'OR',
						'>=QUANTITY_TO' => $quantity,
						'=QUANTITY_TO' => null
					)
				);
				if(empty($priceList))
				{
					if(class_exists('\Bitrix\Catalog\PriceTable'))
					{
						$iterator = \Bitrix\Catalog\PriceTable::getList(array(
							'select' => $arSelect,
							'filter' => $arFilter
						));
					}
					else
					{
						$iterator = CPrice::GetList(array(), $arFilter, false, false, $arSelect);
					}
					while($row = $iterator->fetch())
					{
						$row['ELEMENT_IBLOCK_ID'] = $intIBlockID;
						$priceList[] = $row;
					}
					unset($row);
				}
				else
				{
					foreach(array_keys($priceList) as $priceIndex)
						$priceList[$priceIndex]['ELEMENT_IBLOCK_ID'] = $intIBlockID;
					unset($priceIndex);
				}

				$iterator = CCatalogProduct::GetVATInfo($intProductID);
				if($vat = $iterator->Fetch())
					$vat['RATE'] = (float)$vat['RATE'] * 0.01;
				else
					$vat = array('RATE' => 0.0, 'VAT_INCLUDED' => 'N');
				unset($iterator);

				if (\CCatalogProduct::getUseDiscount())
				{
					if ($arDiscountCoupons === false)
						$arDiscountCoupons = CCatalogDiscountCoupon::GetCoupons();
				}

				$boolDiscountVat = true;
				$isNeedDiscounts = \CCatalogProduct::getUseDiscount();

				foreach($priceList as $priceData)
				{
					$priceData['VAT_RATE'] = $vat['RATE'];
					$priceData['VAT_INCLUDED'] = $vat['VAT_INCLUDED'];

					$currentPrice = $priceData['PRICE'];
					if($boolDiscountVat)
					{
						if($priceData['VAT_INCLUDED'] == 'N')
							$currentPrice *= (1 + $priceData['VAT_RATE']);
					}
					else
					{
						if($priceData['VAT_INCLUDED'] == 'Y')
							$currentPrice /= (1 + $priceData['VAT_RATE']);
					}

					if($priceData['CURRENCY'] != $resultCurrency)
						$currentPrice = CCurrencyRates::ConvertCurrency($currentPrice, $priceData['CURRENCY'], $resultCurrency);
					$currentPrice = roundEx($currentPrice, CATALOG_VALUE_PRECISION);

					$result = array(
						'BASE_PRICE' => $currentPrice,
						'COMPARE_PRICE' => $currentPrice,
						'PRICE' => $currentPrice,
						'CURRENCY' => $resultCurrency,
						'DISCOUNT_LIST' => array(),
						'USE_ROUND' => true,
						'RAW_PRICE' => $priceData
					);
					if($isNeedDiscounts) // discount operation
					{
						$arDiscounts = CCatalogDiscount::GetDiscount(
							$intProductID,
							$intIBlockID,
							$priceData['CATALOG_GROUP_ID'],
							$arUserGroups,
							$renewal,
							$siteID,
							$arDiscountCoupons
						);

						$discountResult = CCatalogDiscount::applyDiscountList($currentPrice, $resultCurrency, $arDiscounts);
						unset($arDiscounts);
						if ($discountResult === false)
							return false;
						$result['PRICE'] = $discountResult['PRICE'];
						$result['COMPARE_PRICE'] = $discountResult['PRICE'];
						$result['DISCOUNT_LIST'] = $discountResult['DISCOUNT_LIST'];
						unset($discountResult);
					}

					if($boolDiscountVat)
					{
						if('N' == $priceData['VAT_INCLUDED'])
						{
							$result['PRICE'] /= (1 + $priceData['VAT_RATE']);
							$result['COMPARE_PRICE'] /= (1 + $priceData['VAT_RATE']);
							$result['BASE_PRICE'] /= (1 + $priceData['VAT_RATE']);
						}
					}
					else
					{
						if ('Y' == $priceData['VAT_INCLUDED'])
						{
							$result['PRICE'] *= (1 + $priceData['VAT_RATE']);
							$result['COMPARE_PRICE'] *= (1 + $priceData['VAT_RATE']);
							$result['BASE_PRICE'] *= (1 + $priceData['VAT_RATE']);
						}
					}

					$result['UNROUND_PRICE'] = $result['PRICE'];
					if ($result['USE_ROUND'])
					{
						if(class_exists('\Bitrix\Catalog\Product\Price') && method_exists('\Bitrix\Catalog\Product\Price', 'roundPrice'))
						{
							$result['PRICE'] = \Bitrix\Catalog\Product\Price::roundPrice(
								$priceData['CATALOG_GROUP_ID'],
								$result['PRICE'],
								$resultCurrency
							);
						}
						$result['COMPARE_PRICE'] = $result['PRICE'];
					}

					if(empty($result['DISCOUNT_LIST']))
					{
						$result['BASE_PRICE'] = $result['PRICE'];
					}
					elseif(roundEx($result['BASE_PRICE'], 2) - roundEx($result['PRICE'], 2) < 0.01)
					{
						$result['BASE_PRICE'] = $result['PRICE'];
						$result['DISCOUNT_PRICE'] = array();
					}

					if(empty($minimalPrice) || $minimalPrice['COMPARE_PRICE'] > $result['COMPARE_PRICE'])
					{
						$minimalPrice = $result;
					}

					unset($currentPrice, $result);
				}
				unset($priceData);
				unset($vat);

				$discountValue = ($minimalPrice['BASE_PRICE'] > $minimalPrice['PRICE'] ? $minimalPrice['BASE_PRICE'] - $minimalPrice['PRICE'] : 0);

				$arResult = array(
					'PRICE' => $minimalPrice['RAW_PRICE'],
					'RESULT_PRICE' => array(
						'PRICE_TYPE_ID' => $minimalPrice['RAW_PRICE']['CATALOG_GROUP_ID'],
						'BASE_PRICE' => $minimalPrice['BASE_PRICE'],
						'DISCOUNT_PRICE' => $minimalPrice['PRICE'],
						'UNROUND_DISCOUNT_PRICE' => $minimalPrice['UNROUND_PRICE'],
						'CURRENCY' => $resultCurrency,
						'DISCOUNT' => $discountValue,
						'PERCENT' => (
							$minimalPrice['BASE_PRICE'] > 0 && $discountValue > 0
							? roundEx((100*$discountValue)/$minimalPrice['BASE_PRICE'], CATALOG_VALUE_PRECISION)
							: 0
						),
						'VAT_RATE' => $minimalPrice['RAW_PRICE']['VAT_RATE'],
						'VAT_INCLUDED' => $minimalPrice['RAW_PRICE']['VAT_INCLUDED']
					),
					'DISCOUNT_PRICE' => $minimalPrice['PRICE'],
					'DISCOUNT' => array(),
					'DISCOUNT_LIST' => array(),
					'PRODUCT_ID' => $intProductID
				);
				if(!empty($minimalPrice['DISCOUNT_LIST']))
				{
					reset($minimalPrice['DISCOUNT_LIST']);
					$arResult['DISCOUNT'] = current($minimalPrice['DISCOUNT_LIST']);
					$arResult['DISCOUNT_LIST'] = $minimalPrice['DISCOUNT_LIST'];
				}
				unset($minimalPrice);

				return $arResult;
			}
			else
				return false;
		}
		else
			return true;
	}

	static function OnRegionUpdateHandler($arFields){
		$arIBlock = CIBlock::GetList(array(), array("ID" => $arFields["IBLOCK_ID"]))->Fetch();
		if(isset(CNextCache::$arIBlocks[$arIBlock['LID']]['aspro_next_regionality']['aspro_next_regions'][0]) && CNextCache::$arIBlocks[$arIBlock['LID']]['aspro_next_regionality']['aspro_next_regions'][0])
			$iRegionIBlockID = CNextCache::$arIBlocks[$arIBlock['LID']]['aspro_next_regionality']['aspro_next_regions'][0];
		else
			return;
		if($iRegionIBlockID == $arFields['IBLOCK_ID'])
		{
			$arSite = CSite::GetList($by, $sort, array("ACTIVE"=>"Y", "ID" =>  $arIBlock['LID']))->Fetch();
			$arSite['DIR'] = str_replace('//', '/', '/'.$arSite['DIR']);
			if(!strlen($arSite['DOC_ROOT'])){
				$arSite['DOC_ROOT'] = $_SERVER['DOCUMENT_ROOT'];
			}
			$arSite['DOC_ROOT'] = str_replace('//', '/', $arSite['DOC_ROOT'].'/');
			$siteDir = str_replace('//', '/', $arSite['DOC_ROOT'].$arSite['DIR']);

			$arProperty = CIBlockElement::GetProperty($arFields["IBLOCK_ID"], $arFields["ID"], "sort", "asc", array("CODE" => "MAIN_DOMAIN"))->Fetch();
			$xml_file = (isset($arFields["SITE_MAP"]) && $arFields["SITE_MAP"] ? $arFields["SITE_MAP"] : "sitemap.xml");
			if($arProperty["VALUE"])
			{
				if(file_exists($siteDir.'robots.txt'))
				{
					copy($siteDir.'robots.txt', $siteDir.'aspro_regions/robots/robots_'.$arProperty["VALUE"].'.txt' );
					$arFile = file($siteDir.'aspro_regions/robots/robots_'.$arProperty["VALUE"].'.txt');
					foreach($arFile as $key => $str)
					{
						if(strpos($str, "Host" ) !== false)
							$arFile[$key] = "Host: ".(CMain::isHTTPS() ? "https://" : "http://").$arProperty["VALUE"]."\r\n";
						if(strpos($str, "Sitemap" ) !== false)
							$arFile[$key] = "Sitemap: ".(CMain::isHTTPS() ? "https://" : "http://").$arProperty["VALUE"]."/".$xml_file."\r\n";
					}
					$strr = implode("", $arFile);
					file_put_contents($siteDir.'aspro_regions/robots/robots_'.$arProperty["VALUE"].'.txt', $strr);
				}
			}
		}
	}

	static function onBeforeResultAddHandler($WEB_FORM_ID, &$arFields, &$arrVALUES){
		if(!defined('ADMIN_SECTION'))
		{
			global $APPLICATION;
			$arTheme = CNext::GetFrontParametrsValues(SITE_ID);

			if($arTheme['HIDDEN_CAPTCHA'] == 'Y' && $arrVALUES['nspm'] && !isset($arrVALUES['captcha_sid']))
		    	$APPLICATION->ThrowException(Loc::getMessage('ERROR_FORM_CAPTCHA'));

		  	if($arTheme['SHOW_LICENCE'] == 'Y' && ((!isset($arrVALUES['licenses_popup']) || !$arrVALUES['licenses_popup']) && (!isset($arrVALUES['licenses_inline']) || !$arrVALUES['licenses_inline'])))
		    	$APPLICATION->ThrowException(Loc::getMessage('ERROR_FORM_LICENSE'));
		}
	}

	static function OnSaleComponentOrderPropertiesHandler(&$arFields){
		global $arRegion;

		if($arRegion && $_SERVER['REQUEST_METHOD'] != 'POST')
		{
			if($arRegion['LOCATION'])
			{
	    		$arLocationProp = CSaleOrderProps::GetList(
			        array('SORT' => 'ASC'),
			        array(
			                'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID'],
			                'TYPE' => 'LOCATION',
			                'IS_LOCATION' => 'Y',
			        ),
			        false,
			        false,
			        array('ID')
			    )->Fetch();
			    if($arLocationProp)
			    {
					$arFields['ORDER_PROP'][$arLocationProp['ID']] = CSaleLocation::getLocationCODEbyID($arRegion['LOCATION']);
					$arLocationZipProp = CSaleOrderProps::GetList(
				        array('SORT' => 'ASC'),
				        array(
				                'PERSON_TYPE_ID' => $arFields['PERSON_TYPE_ID'],
				                'CODE' => 'ZIP',
				        ),
				        false,
				        false,
				        array('ID')
				    )->Fetch();
				    if($arLocationZipProp)
				    {
						$rsLocaction = CSaleLocation::GetLocationZIP($arRegion['LOCATION']);
		    			$arLocation = $rsLocaction->Fetch();
		    			if($arLocation['ZIP'])
		    				$arFields['ORDER_PROP'][$arLocationZipProp['ID']] = $arLocation['ZIP'];
		    		}
			    }
			}
		}
	}

	static function OnBeforeSubscriptionAddHandler(&$arFields){
		if(!defined('ADMIN_SECTION'))
		{
			global $APPLICATION;
			$arTheme = CNext::GetFrontParametrsValues(SITE_ID);
			if($arTheme['SHOW_LICENCE'] == 'Y' && (isset($_REQUEST['check_condition']) && $_REQUEST['check_condition'] == 'YES') && !isset($_REQUEST['licenses_subscribe']))
			{
				$APPLICATION->ThrowException(Loc::getMessage('ERROR_FORM_LICENSE'));
				return false;
			}
		}
	}

	static function onAfterResultAddHandler($WEB_FORM_ID, $RESULT_ID){
		if(Option::get(self::moduleID, 'AUTOMATE_SEND_FLOWLU', 'Y') == 'Y')
			\Aspro\Functions\CAsproNext::sendLeadCrmFromForm($WEB_FORM_ID, $RESULT_ID, 'FLOWLU');
		if(Option::get(self::moduleID, 'AUTOMATE_SEND_AMO_CRM', 'Y') == 'Y')
			\Aspro\Functions\CAsproNext::sendLeadCrmFromForm($WEB_FORM_ID, $RESULT_ID, 'AMO_CRM');
	}
}