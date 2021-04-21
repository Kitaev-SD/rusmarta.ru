<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?\Bitrix\Main\Loader::includeModule("sale");?>
<?if(!isset($arParams))
CSaleBasket::UpdateBasketPrices(CSaleBasket::GetBasketUserID());?>
<?$APPLICATION->IncludeComponent( "bitrix:sale.basket.basket.line", "normal", Array(
	"PATH_TO_BASKET" => CNext::GetFrontParametrValue("BASKET_PAGE_URL"), 
	"PATH_TO_ORDER" => CNext::GetFrontParametrValue("ORDER_PAGE_URL"), 
	"SHOW_DELAY" => "Y", 
	"SHOW_PRODUCTS"=>"Y",
	"SHOW_EMPTY_VALUES" => "Y",
	"SHOW_NOTAVAIL" => "N",
	"SHOW_SUBSCRIBE" => "N",
	"SHOW_IMAGE" => "Y",
	"SHOW_PRICE" => "Y",
	"SHOW_SUMMARY" => "Y",
	"SHOW_NUM_PRODUCTS" => "Y",
	"SHOW_TOTAL_PRICE" => "Y",
	"SHOW_ACTUAL" => (isset($_POST['ACTUAL']) && $_POST['ACTUAL'] == 'Y' ? 'Y' : 'N'),
	"HIDE_ON_BASKET_PAGES" => "Y"
) );?>