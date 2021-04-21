<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$strMainId = $this->getEditAreaId($arResult["PRODUCT_ID"]);
$jsObject = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainId);
$paramsForJs = array(
	"buttonId" => $arResult["BUTTON_ID"],
	"jsObject" => $jsObject,
	"alreadySubscribed" => $arResult["ALREADY_SUBSCRIBED"],
	"productId" => $arResult["PRODUCT_ID"],
	"buttonClass" => htmlspecialcharsbx($arResult["BUTTON_CLASS"]),
	"urlListSubscriptions" => "/",
);
$templateData = $paramsForJs;?>

<div class="catalog-subscribe">
	<button type="button" id="<?=htmlspecialcharsbx($arResult['BUTTON_ID'])?>" class="<?=htmlspecialcharsbx($arResult['BUTTON_CLASS'])?>" data-item="<?=htmlspecialcharsbx($arResult['PRODUCT_ID'])?>"><i class="fa fa-envelope"></i><?=Loc::getMessage("CPST_SUBSCRIBE_BUTTON_NAME")?></button>
	<input type="hidden" id="<?=htmlspecialcharsbx($arResult['BUTTON_ID'])?>_hidden">
</div>

<script type="text/javascript">	
	BX.message({
		CPST_SUBSCRIBE_POPUP_TITLE: "<?=GetMessageJS('CPST_SUBSCRIBE_POPUP_TITLE');?>",
		CPST_SUBSCRIBE_BUTTON_NAME: "<?=GetMessageJS('CPST_SUBSCRIBE_BUTTON_NAME');?>",
		CPST_SUBSCRIBE_BUTTON_CLOSE: "<?=GetMessageJS('CPST_SUBSCRIBE_BUTTON_CLOSE');?>",
		CPST_SUBSCRIBE_MANY_CONTACT_NOTIFY: "<?=GetMessageJS('CPST_SUBSCRIBE_MANY_CONTACT_NOTIFY');?>",
		CPST_SUBSCRIBE_LABLE_CONTACT_INPUT: "<?=GetMessageJS('CPST_SUBSCRIBE_LABLE_CONTACT_INPUT');?>",
		CPST_SUBSCRIBE_VALIDATE_UNKNOW_ERROR: "<?=GetMessageJS('CPST_SUBSCRIBE_VALIDATE_UNKNOW_ERROR');?>",
		CPST_SUBSCRIBE_VALIDATE_ERROR_EMPTY_FIELD: "<?=GetMessageJS('CPST_SUBSCRIBE_VALIDATE_ERROR_EMPTY_FIELD');?>",
		CPST_SUBSCRIBE_VALIDATE_ERROR: "<?=GetMessageJS('CPST_SUBSCRIBE_VALIDATE_ERROR');?>",
		CPST_SUBSCRIBE_CAPTCHA_TITLE: "<?=GetMessageJS('CPST_SUBSCRIBE_CAPTCHA_TITLE');?>",
		CPST_STATUS_SUCCESS: "<?=GetMessageJS('CPST_STATUS_SUCCESS');?>",
		CPST_STATUS_ERROR: "<?=GetMessageJS('CPST_STATUS_ERROR');?>",
		CPST_ENTER_WORD_PICTURE: "<?=GetMessageJS('CPST_ENTER_WORD_PICTURE');?>",
		CPST_TITLE_ALREADY_SUBSCRIBED: "<?=GetMessageJS('CPST_TITLE_ALREADY_SUBSCRIBED');?>",
		CPST_POPUP_SUBSCRIBED_TITLE: "<?=GetMessageJS('CPST_POPUP_SUBSCRIBED_TITLE');?>",
		CPST_POPUP_SUBSCRIBED_TEXT: "<?=GetMessageJS('CPST_POPUP_SUBSCRIBED_TEXT');?>",
		CPST_SUBSCRIBE_BUTTON_SEND: "<?=GetMessageJS('CPST_SUBSCRIBE_BUTTON_SEND');?>"
	});
	var <?=$jsObject?> = new JCCatalogProductSubscribe(<?=CUtil::phpToJSObject($paramsForJs, false, true)?>);
</script>