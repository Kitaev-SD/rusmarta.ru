<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(intval($arParams['CACHE_TIME']) < 0){
	$arParams['CACHE_TIME'] = 36000;
}

if(!strlen($arParams['DEFAULT_CURRENCY'])){
	$arParams['DEFAULT_CURRENCY'] = COption::GetOptionString('sale', 'default_currency', 'RUB');
}

if(empty($arParams['PROPERTIES'])){
	$arParams['PROPERTIES'] = array('FIO', 'PHONE');
}

if(empty($arParams['REQUIRED'])){
	$arParams['REQUIRED'] = array('FIO', 'PHONE');
}

// get phone auth params
list($bPhoneAuthSupported, $bPhoneAuthShow, $bPhoneAuthRequired, $bPhoneAuthUse) = Aspro\Next\PhoneAuth::getOptions();

if($bPhoneAuthSupported && $bPhoneAuthShow){
	echo CJSCore::Init('phone_auth', true);

	if($bPhoneAuthRequired){
		if(!in_array('PHONE', $arParams['REQUIRED'])){
			$arParams['REQUIRED'][] = 'PHONE';
		}
	}
}

$arParams['ELEMENT_ID'] = intval($arParams['ELEMENT_ID']);

$arResult = array(
	'ERRORS' => array(),
	'SCRIPT_PATH' => $this->{'__path'},
	'USER_ID' => false,
	'USER_FIO' => '',
	'USER_EMAIL' => '',
	'USER_PHONE' => '',
);

global $USER;
if($arResult['USER_ID'] = $USER->GetID()){
	$dbUser = $USER->GetByID($arResult['USER_ID']);
	$arUser = $dbUser->Fetch();
	$arResult['USER_FIO'] = $USER->GetFullName();
	$arResult['USER_EMAIL'] = $USER->GetEmail();
	$arResult['USER_PHONE'] = (!empty($arUser['PERSONAL_PHONE']) ? $arUser['PERSONAL_PHONE'] : $arUser['PERSONAL_MOBILE']);
}

$arParams['INLINE_FORM'] = (isset($_GET['form_id']) && $_GET['form_id'] == 'ocb');

if(\Bitrix\Main\Config\Option::get('aspro.next', 'ONE_CLICK_BUY_CAPTCHA', 'N') == 'Y')
{
	$arParams['CACHE_TYPE'] = 'N';
}


if($this->StartResultCache()){
	/*get order props*/
	CModule::IncludeModule('sale');
	$arProps = array();
	$rsProps = CSaleOrderProps::GetList(
        array("SORT" => "ASC"),
        array(
                "ACTIVE" => "Y",
				"CODE" => $arParams["PROPERTIES"],
				"PERSON_TYPE_ID" => $arParams["DEFAULT_PERSON_TYPE"]
            )
    );
    while($arProp = $rsProps->Fetch())
    {
		if($arProp["CODE"] && ($arProp["TYPE"]=="TEXT" || $arProp["TYPE"]=="TEXTAREA" || $arProp["TYPE"]=="FILE"))
		{
	    	$arProps[$arProp["CODE"]]["TYPE"] = $arProp["TYPE"];
	    	$arProps[$arProp["CODE"]]["MULTIPLE"] = $arProp["MULTIPLE"];
	    	$arProps[$arProp["CODE"]]["TITLE"] = $arProp["NAME"];
		}
    }
    $arResult["PROPS"] = $arProps;

    $this->SetResultCacheKeys(array(
	   'SCRIPT_PATH',
	   'PROPS',
	));

	$this->IncludeComponentTemplate();
}
?>