<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>

<?if(!CModule::IncludeModule("sale") || !CModule::IncludeModule("catalog") || !CModule::IncludeModule("iblock")) {
	return;
}

if(intval($_REQUEST["id"]) <= 0) {
	return;
}

$qnt = floatval($_REQUEST["qnt"]);

$arItemParams = array();
if(isset($_REQUEST["props"]) && !empty($_REQUEST["props"])):
	$arItemParamsBefore = unserialize(base64_decode(strtr($_REQUEST["props"], "-_,", "+/=")));
	foreach($arItemParamsBefore as $arProp):
		$arItemParams[] = $arProp;
	endforeach;
endif;
if(isset($_REQUEST["select_props"]) && !empty($_REQUEST["select_props"])):
	$select_props = explode("||", $_REQUEST["select_props"]);
	foreach($select_props as $arSelProp):
		$arItemParams[] = unserialize(base64_decode(strtr($arSelProp, "-_,", "+/=")));
	endforeach;
endif;

$arFields = array("DELAY" => "Y");

$resBasket = CSaleBasket::GetList(
	array(), 
	array(
		"PRODUCT_ID" => intval($_REQUEST["id"]),
		"FUSER_ID" => CSaleBasket::GetBasketUserID(),
		"LID" => SITE_ID,
		"ORDER_ID" => "NULL"
	), 
	false, 
	false, 
	array("ID")
);

if($ar = $resBasket->Fetch()):
	
	CSaleBasket::Update($ar["ID"], $arFields);
	
else:
	
	Add2BasketByProductID(intval($_REQUEST["id"]), $qnt, $arItemParams);
	$resBasket2 = CSaleBasket::GetList(
		array(), 
		array(
			"PRODUCT_ID" => intval($_REQUEST["id"]),
			"FUSER_ID" => CSaleBasket::GetBasketUserID(),
			"LID" => SITE_ID,
			"ORDER_ID" => "NULL"
		), 
		false, 
		false, 
		array("ID")
	);
	while($ar2 = $resBasket2->Fetch()) {
		CSaleBasket::Update($ar2["ID"], $arFields);
	}

endif;?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>