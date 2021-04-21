<?
$isInline = strpos($_SERVER['SCRIPT_NAME'], '/ajax/') === false ? 'Y' : 'N';
$isPreview = isset($_POST['is_preview']) && $_POST['is_preview'] === 'Y' ? 'Y' : 'N';
$isPopup = $isInline === 'N' && $isPreview === 'N' ? 'Y' : 'N';

$productId = isset($_REQUEST['product_id']) && intval($_REQUEST['product_id']) > 0 ? intval($_REQUEST['product_id']) : false;
$quantity = isset($_REQUEST['quantity']) && floatval($_REQUEST['quantity']) > 0 ? floatval($_REQUEST['quantity']) : 0;

if($isInline === 'N'){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

	if($isPopup === 'Y'){
		$GLOBALS['APPLICATION']->ShowAjaxHead();
		$areaIndex = 1000;
	}
	else{
		$areaIndex = isset($_POST['index']) && intval($_POST['index']) > 0 ? intval($_POST['index']) : 1001;
	}

	if($GLOBALS['APPLICATION']->GetShowIncludeAreas()){
		$GLOBALS['APPLICATION']->editArea = new CEditArea();
		$GLOBALS['APPLICATION']->editArea->includeAreaIndex = array(0 => $areaIndex);
		if($isPopup === 'Y'){
			?><style>.bx-core-adm-dialog, div.bx-component-opener, .bx-core-popup-menu{z-index:3001 !important;}</style><?
		}
	}
}
?>
<?if($isPopup === 'Y'):?><a href="#" class="close jqmClose"><i></i></a><?endif;?>
<?$APPLICATION->IncludeComponent(
	"aspro:catalog.delivery.next",
	".default",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"SET_PAGE_TITLE" => "Y",
		"DELIVERY_NO_SESSION" => "Y",
		"DELIVERY_WITHOUT_PAY_SYSTEM" => "Y",
		"PAY_FROM_ACCOUNT" => "N",
		"SPOT_LOCATION_BY_GEOIP" => "Y",
		"USE_LAST_ORDER_DATA" => "Y",
		"USE_PROFILE_LOCATION" => "N",
		"SAVE_IN_SESSION" => "Y",
		"SHOW_LOCATION_SOURCE" => "N",
		"CHANGEABLE_FIELDS" => array(
			0 => "LOCATION",
			1 => "QUANTITY",
			2 => "ADD_BASKET",
		),
		"SHOW_DELIVERY_PARENT_NAMES" => "Y",
		"SHOW_MESSAGE_ON_CALCULATE_ERROR" => "Y",
		"PREVIEW_SHOW_DELIVERY_PARENT_ID" => array(
			0 => "2",
			1 => "3",
		),
		"PRODUCT_ID" => $productId,
		"PRODUCT_QUANTITY" => $quantity,
		"LOCATION_CODE" => "",
		"USER_PROFILE_ID" => "",
		"PERSON_TYPE_ID" => "",
		"PAY_SYSTEM_ID" => "",
		"DELIVERY_ID" => "",
		"ADD_BASKET" => "N",
		"BUYER_STORE_ID" => "",
		"USE_CUSTOM_MESSAGES" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
<?
if($isInline === 'N'){
	require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_after.php');
}
?>