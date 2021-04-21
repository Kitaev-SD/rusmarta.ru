<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();

use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

Option::set("sale", "SHOP_SITE_".WIZARD_SITE_ID, WIZARD_SITE_ID);

Option::set("main", "captcha_registration", "N");
Option::set("iblock", "use_htmledit", "Y");
Option::set("fileman", "propstypes", serialize(array("description"=>Loc::getMessage("MAIN_OPT_DESCRIPTION"), "keywords"=>Loc::getMessage("MAIN_OPT_KEYWORDS"), "title"=>Loc::getMessage("MAIN_OPT_TITLE"), "keywords_inner"=>Loc::getMessage("MAIN_OPT_KEYWORDS_INNER"), "viewed_show"=>GetMessage("MAIN_OPT_VIEWED_SHOW"), "HIDETITLE" => Loc::getMessage("MAIN_OPT_HIDETITLE"), "WIDE_PAGE" => Loc::getMessage("MAIN_OPT_FULLWIDTH"), "HIDE_LEFT_BLOCK" => Loc::getMessage("MAIN_OPT_MENU"), "BLOG_PAGE" => Loc::getMessage("BLOG_PAGE_TITLE"), "MENU_SHOW_SECTIONS" => Loc::getMessage("MENU_SHOW_SECTIONS_TITLE"), "MENU_SHOW_ELEMENTS" => Loc::getMessage("MENU_SHOW_ELEMENTS_TITLE"))), WIZARD_SITE_ID);
Option::set("search", "suggest_save_days", 250);
Option::set("search", "use_tf_cache", "Y");
Option::set("search", "use_word_distance", "Y");
Option::set("search", "use_social_rating", "Y");

Option::set("form", "SIMPLE", "N");

// social auth services
if (Option::get("socialservices", "auth_services") == ""){
	$bRu = (LANGUAGE_ID == 'ru');
	$arServices = array(
		"VKontakte" => "Y",
		"MyMailRu" => "Y",
		"Twitter" => "Y",
		"Facebook" => "Y",
		"Livejournal" => "Y",
		"YandexOpenID" => ($bRu? "Y":"N"),
		"Rambler" => ($bRu? "Y":"N"),
		"MailRuOpenID" => ($bRu? "Y":"N"),
		"Liveinternet" => ($bRu? "Y":"N"),
		"Blogger" => "N",
		"OpenID" => "Y",
		"LiveID" => "N",
	);
	Option::set("socialservices", "auth_services", serialize($arServices));
}

Option::set("socialservices", "auth_services", serialize($arServices));
Option::set("aspro.next", "WIZARD_SITE_ID", WIZARD_SITE_ID, WIZARD_SITE_ID);
Option::set("aspro.next", "SITE_INSTALLED", "Y", WIZARD_SITE_ID);
Option::set("aspro.next", "USE_FILTERS", "Y", WIZARD_SITE_ID);

// subscribe to products - set active notify for sites
$notifyOption = Option::get("sale", "subscribe_prod", "");
$arNotify = unserialize($notifyOption);
if($arNotify){
	foreach($arNotify as $siteID => $notify){
		if($siteID == WIZARD_SITE_ID){
			$arNotify[$siteID]['use'] = 'Y';
			$arNotify[$siteID]['del_after'] = $arNotify[$siteID]['del_after'] > 0 ? $arNotify[$siteID]['del_after'] : 30;
		}
	}
	Option::set("sale", "subscribe_prod", serialize($arNotify));
}

// get DB charset
$sql='SHOW VARIABLES LIKE "character_set_database";';
if(method_exists('\Bitrix\Main\Application', 'getConnection')){
	$db=\Bitrix\Main\Application::getConnection();
	$arResult = $db->query($sql)->fetch();
	$isUTF8 = $arResult['Value'] == 'utf8';
}elseif(defined("BX_USE_MYSQLI") && BX_USE_MYSQLI === true){
	if($result = @mysqli_query($sql)){
		$arResult = mysql_fetch_row($result);
		$isUTF8 = $arResult[1] == 'utf8';
	}
}elseif($result = @mysql_query($sql)){
	$arResult = mysql_fetch_row($result);
	$isUTF8 = $arResult[1] == 'utf8';
}

// new options
Option::set("aspro.next", "ORDER_BASKET_VIEW", "FLY", WIZARD_SITE_ID);
Option::set("aspro.next", "INDEX_TYPE", "index1", WIZARD_SITE_ID);
Option::set("aspro.next", "STORES_SOURCE", "IBLOCK", WIZARD_SITE_ID);
Option::set("aspro.next", "TYPE_SKU", "TYPE_1", WIZARD_SITE_ID);
Option::set("aspro.next", "MAX_DEPTH_MENU", "3", WIZARD_SITE_ID);
Option::set("aspro.next", "FILTER_VIEW", "VERTICAL", WIZARD_SITE_ID);
Option::set("aspro.next", "USE_FAST_VIEW_PAGE_DETAIL", "fast_view_1", WIZARD_SITE_ID);
Option::set("aspro.next", "SHOW_BASKET_ONADDTOCART", "Y", WIZARD_SITE_ID);
Option::set("aspro.next", "USE_PRODUCT_QUANTITY_LIST", "Y", WIZARD_SITE_ID);
Option::set("aspro.next", "USE_PRODUCT_QUANTITY_DETAIL", "Y", WIZARD_SITE_ID);
Option::set("aspro.next", "BUYNOPRICEGGOODS", "NOTHING", WIZARD_SITE_ID);
Option::set("aspro.next", "BUYMISSINGGOODS", "ADD", WIZARD_SITE_ID);
Option::set("aspro.next", "EXPRESSION_ORDER_BUTTON", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Под заказ') : 'Под заказ'), WIZARD_SITE_ID);

$DefaultGroupID = 0;
$rsGroups = CGroup::GetList($by = "id", $order = "asc", array("ACTIVE" => "Y"));
while($arItem = $rsGroups->Fetch()){
	if($arItem["ANONYMOUS"] == "Y"){
		$DefaultGroupID = $arItem["ID"];
		break;
	}
}

Option::set("aspro.next", "MOBILE_FILTER_COMPACT", 'Y', WIZARD_SITE_ID);
Option::set("aspro.next", "MOBILE_CATALOG_LIST_ELEMENTS_COMPACT", 'Y', WIZARD_SITE_ID);
Option::set("aspro.next", "SHOW_QUANTITY_FOR_GROUPS", $DefaultGroupID, WIZARD_SITE_ID);
Option::set("aspro.next", "SHOW_QUANTITY_COUNT_FOR_GROUPS", $DefaultGroupID, WIZARD_SITE_ID);
Option::set("aspro.next", "EXPRESSION_FOR_EXISTS", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Есть в наличии') : 'Есть в наличии'), WIZARD_SITE_ID);
Option::set("aspro.next", "EXPRESSION_FOR_NOTEXISTS", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Нет в наличии') : 'Нет в наличии'), WIZARD_SITE_ID);
Option::set("aspro.next", "USE_WORD_EXPRESSION", "Y", WIZARD_SITE_ID);
Option::set("aspro.next", "MAX_AMOUNT", 10, WIZARD_SITE_ID);
Option::set("aspro.next", "MIN_AMOUNT", 2, WIZARD_SITE_ID);
Option::set("aspro.next", "USE_REGIONALITY", "N", WIZARD_SITE_ID);
Option::set("aspro.next", "REGIONALITY_TYPE", "ONE_DOMAIN", WIZARD_SITE_ID);
Option::set("aspro.next", "SHOW_REGION_CONTACT", "Y", WIZARD_SITE_ID);
Option::set("aspro.next", "PRINT_BUTTON", "N", WIZARD_SITE_ID);
Option::set("aspro.next", "SHOW_BASKET_ON_PAGES", "N", WIZARD_SITE_ID);
Option::set("aspro.next", "VIEWED_TEMPLATE", "HORIZONTAL", WIZARD_SITE_ID);
Option::set("aspro.next", "VIEWED_TYPE", "LOCAL", WIZARD_SITE_ID);
Option::set("aspro.next", "ADV_SIDE", "Y", WIZARD_SITE_ID);
Option::set("aspro.next", "EXPRESSION_FOR_MIN", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Мало') : 'Мало'), WIZARD_SITE_ID);
Option::set("aspro.next", "EXPRESSION_FOR_MID", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Достаточно') : 'Достаточно'), WIZARD_SITE_ID);
Option::set("aspro.next", "EXPRESSION_FOR_MAX", ($isUTF8 ? iconv('CP1251', 'UTF-8', 'Много') : 'Много'), WIZARD_SITE_ID);
Option::set("aspro.next", "MIN_ORDER_PRICE_TEXT", ($isUTF8 ? iconv('CP1251', 'UTF-8', '<b>Минимальная сумма заказа #PRICE#</b><br/>Пожалуйста, добавьте еще товаров в корзину') : '<b>Минимальная сумма заказа #PRICE#</b><br/>Пожалуйста, добавьте еще товаров в корзину'), WIZARD_SITE_ID);

// enable composite
if(class_exists("CHTMLPagesCache")){
	if(method_exists("CHTMLPagesCache", "GetOptions")){
		if($arHTMLCacheOptions = CHTMLPagesCache::GetOptions()){
			if($arHTMLCacheOptions["COMPOSITE"] !== "Y"){
				$arDomains = array();

				$arSites = array();
				$dbRes = CSite::GetList($by="sort", $order="desc", array("ACTIVE" => "Y"));
				while($item = $dbRes->Fetch()){
					$arSites[$item["LID"]] = $item;
				}

				if($arSites){
					foreach($arSites as $arSite){
						if(strlen($serverName = trim($arSite["SERVER_NAME"], " \t\n\r"))){
							$arDomains[$serverName] = $serverName;
						}
						if(strlen($arSite["DOMAINS"])){
							foreach(explode("\n", $arSite["DOMAINS"]) as $domain){
								if(strlen($domain = trim($domain, " \t\n\r"))){
									$arDomains[$domain] = $domain;
								}
							}
						}
					}
				}

				if(!$arDomains){
					$arDomains[$_SERVER["SERVER_NAME"]] = $_SERVER["SERVER_NAME"];
				}

				if(!$arHTMLCacheOptions["GROUPS"]){
					$arHTMLCacheOptions["GROUPS"] = array();
				}
				$rsGroups = CGroup::GetList(($by="id"), ($order="asc"), array());
				while($arGroup = $rsGroups->Fetch()){
					if($arGroup["ID"] > 2){
						if(in_array($arGroup["STRING_ID"], array("RATING_VOTE_AUTHORITY", "RATING_VOTE")) && !in_array($arGroup["ID"], $arHTMLCacheOptions["GROUPS"])){
							$arHTMLCacheOptions["GROUPS"][] = $arGroup["ID"];
						}
					}
				}

				$arHTMLCacheOptions["COMPOSITE"] = "Y";
				$arHTMLCacheOptions["DOMAINS"] = array_merge((array)$arHTMLCacheOptions["DOMAINS"], (array)$arDomains);
				CHTMLPagesCache::SetEnabled(true);
				CHTMLPagesCache::SetOptions($arHTMLCacheOptions);
				bx_accelerator_reset();
			}
		}
	}
}?>