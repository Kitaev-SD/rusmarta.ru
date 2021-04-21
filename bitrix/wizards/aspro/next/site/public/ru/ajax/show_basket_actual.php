<?define("STATISTIC_SKIP_ACTIVITY_CHECK", "true");?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<div id="basket_preload">
<?include_once("action_basket.php");?>
<?$APPLICATION->IncludeComponent( "bitrix:sale.basket.basket.line", "actual", array("BY_AJAX" => "Y", "SHOW_DELAY" => "Y", "SHOW_PRODUCTS"=>"Y","SHOW_EMPTY_VALUES" => "Y",), false, array("HIDE_ICONS" =>"Y") );?>
</div>