<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Контакты: адрес, телефон, почта – Rusmarta");
$APPLICATION->SetPageProperty("description", "Вся контактная информация компании. Сигнализация и видеооборудование, системы видеонаблюдения с доставкой по всей России в интернет-магазине Rusmarta.ru");
$APPLICATION->SetTitle("Контакты");

use Bitrix\Main\Application,
    Bitrix\Main\Text\Encoding,
    Bitrix\Main\Localization\Loc;

	header('Cache-Control: private, no-cache="set-cookie"');
	header('Expires: 0');
	header('Pragma: no-cache');

//$request = Application::getInstance()->getContext()->getRequest();
//$locationCity = $request->getCookie("GEOLOCATION_CITY");
$locationCity = $_COOKIE['bxmaker_geoip_2_3_8_s1_city'];

if (SITE_CHARSET != "utf-8")
$locationCity = Encoding::convertEncoding($locationCity, "utf-8", SITE_CHARSET);

global $arrPVZCdek;
$arrPVZCdek = array(
    "PROPERTY_CITY" => $locationCity
);

if (!empty($locationCity)){
  //$APPLICATION->SetTitle("Контакты в г. $locationCity");
}

if (!empty($locationCity)):
?>
<script>
	function get_cookie ( cookie_name )
	{
	var results = document.cookie.match ( '(^|;) ?' + cookie_name + '=([^;]*)(;|$)' );
	
	if ( results )
		return ( unescape ( results[2] ) );
	else
		return null;
	}

	var x = get_cookie ( "contacts" );

	console.log(document.cookie);

	if (x == 1) {
		document.cookie = "contacts=0; path=/";
		window.location.reload();
	}
</script>
<h2>Пункты выдачи заказов в г. <?echo $locationCity;?></h2>
 <?$APPLICATION->IncludeComponent(
	"bitrix:news",
	"cdek",
	Array(
		"ADD_ELEMENT_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"BROWSER_TITLE" => "-",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "N",
		"CHECK_DATES" => "N",
		"COMPONENT_TEMPLATE" => "cdek",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DETAIL_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "N",
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_FIELD_CODE" => array(0=>"",1=>"",),
		"DETAIL_PAGER_SHOW_ALL" => "N",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PROPERTY_CODE" => array(0=>"ADDRESS",1=>"OPERATION_TIME",2=>"CITY",3=>"ZIP",4=>"COORDS",5=>"WEIGHT",6=>"PHONE",7=>"",),
		"DETAIL_SET_CANONICAL_URL" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"DISPLAY_DATE" => "Y",
		"DISPLAY_NAME" => "N",
		"DISPLAY_PICTURE" => "Y",
		"DISPLAY_PREVIEW_TEXT" => "Y",
		"DISPLAY_TOP_PAGER" => "N",
		"FILTER_FIELD_CODE" => array(0=>"",1=>"",),
		"FILTER_NAME" => "arrPVZCdek",
		"FILTER_PROPERTY_CODE" => array(0=>"ADDRESS",1=>"OPERATION_TIME",2=>"CITY",3=>"ZIP",4=>"COORDS",5=>"WEIGHT",6=>"PHONE",7=>"",),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"IBLOCK_ID" => "27",
		"IBLOCK_TYPE" => "content",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"LIST_ACTIVE_DATE_FORMAT" => "d.m.Y",
		"LIST_FIELD_CODE" => array(0=>"",1=>"",),
		"LIST_PROPERTY_CODE" => array(0=>"ADDRESS",1=>"OPERATION_TIME",2=>"CITY",3=>"ZIP",4=>"COORDS",5=>"WEIGHT",6=>"PHONE",7=>"",),
		"MESSAGE_404" => "",
		"META_DESCRIPTION" => "-",
		"META_KEYWORDS" => "-",
		"NEWS_COUNT" => "1000",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "0",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => ".default",
		"PAGER_TITLE" => "Новости",
		"PREVIEW_TRUNCATE_LEN" => "",
		"SEF_FOLDER" => "/contacts/",
		"SEF_MODE" => "Y",
		"SEF_URL_TEMPLATES" => array("news"=>"","section"=>"","detail"=>"#ELEMENT_CODE#/",),
		"SET_LAST_MODIFIED" => "N",
		"SET_STATUS_404" => "N",
		"SET_TITLE" => "N",
		"SHOW_404" => "N",
		"SORT_BY1" => "NAME",
		"SORT_BY2" => "SORT",
		"SORT_ORDER1" => "ASC",
		"SORT_ORDER2" => "ASC",
		"STRICT_SECTION_CHECK" => "N",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "Y",
		"USE_PERMISSIONS" => "N",
		"USE_RATING" => "N",
		"USE_REVIEW" => "N",
		"USE_RSS" => "N",
		"USE_SEARCH" => "N"
	)
);?> <? endif; ?>
<div style="clear: both;">
 <br>
 <br>
</div>
<h2>Контакты офиса в Москве</h2>
 <b> </b>
<div>
</div>
<table cellpadding="1" cellspacing="1">
<tbody>
<tr>
	<td>
 <b>Фактический адрес</b>: <br>
		 Москва, Семёновский переулок, 15<br>
		 Бизнес центр "Семёновский 15", офис 113 (на проходной, чтобы вас пропустили нужно сказать что идете в Русмарту).&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<b><br>
 </b><b><br>
 </b><b>Время работы:</b><br>
		 Пн-Пт: 10.30-19.00<br>
		 Сб: 10.00-17.00 (заказы на сайте принимаются круглосуточно)<br>
 <br>
		<br>
 <br>
 <br>
 <br>
 <br>
	</td>
	<td>
		 <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A1f822ebdd0bfd3a5dc38938aaf52815f42aad13bffea993490427e67432922d9&amp;source=constructor" width="500" height="350" frameborder="0"></iframe>
	</td>
</tr>
</tbody>
</table>
<p>
</p>
<h2>Контакты офиса в Санкт-Петербурге</h2>
 <b> </b>
<div>
</div>
<table cellpadding="1" cellspacing="1">
<tbody>
<tr>
	<td>
 <b>Фактический адрес</b>: <br>
		 195269, Санкт-Петербург, м.Гражданский проспект <br>
		 ул. Учительская д.23, Бизнес центр Атолл, офис 473. (на проходной позвонить)<br>
 <br>
 <b>Время работы:</b><br>
		 Пн-Пт: 09.30-19.00&nbsp;<br>
		 Сб: 10.00-17.00 (заказы на сайте принимаются круглосуточно)<br>
 <br>
 <b>Телефоны:</b>&nbsp;<br>
		 +7 (812) 309-76-71 (Отдел продаж, многоканальный)&nbsp;&nbsp;<br>
		 +7 (800) 333-53-51 (Бесплатная линия по России)&nbsp;&nbsp; <br>
 <b><br>
		 Электронная почта:&nbsp;<a href="mailto:info@rusmarta.ru"></a><a href="mailto:info@rusmarta.ru">info@rusmarta.ru</a> </b><b><br>
 </b>
	</td>
	<td>
		 <iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3Ac1636a7d4b60ee06654e5cd2ffd929ae0cf19905124ae00006cd28eee38b04c0&amp;source=constructor" width="500" height="350" frameborder="0"></iframe>
	</td>
</tr>
</tbody>
</table>
<h2><br>
 </h2>
 <b><br>
 </b><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php") ?>