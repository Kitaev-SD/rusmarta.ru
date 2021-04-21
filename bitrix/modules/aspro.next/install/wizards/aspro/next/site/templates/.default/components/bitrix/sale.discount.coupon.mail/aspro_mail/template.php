<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
?>
<div class="border_wrapper" style="text-align:center;color:#666666;font-size:16px;border:1px solid #e7e7e7;padding: 19px 10px 17px;margin-top:10px;margin-bottom:10px;">
	<?=\Bitrix\Main\Localization\Loc::getMessage("COUPON_TEXT");?><span style="font-weight:600;font-size:18px;color:#1d2029;padding-left:10px;"><?=$arResult['COUPON'];?></span>
</div>