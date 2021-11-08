<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;
$isPreviewImg = is_array($arResult["PREVIEW_IMG"]);
$isDetailImg = is_array($arResult["DETAIL_IMG"]);
$inAdvantages = in_array("ADVANTAGES", $arSetting["CATALOG_DETAIL"]["VALUE"]);
$inProductQnt = in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inOffersLinkShow = in_array("OFFERS_LINK_SHOW", $arSetting["GENERAL_SETTINGS"]["VALUE"]);
$inBtnBoc = in_array("BUTTON_BOC", $arSetting["CATALOG_DETAIL"]["VALUE"]);
$inBtnCheaper = in_array("BUTTON_CHEAPER", $arSetting["CATALOG_DETAIL"]["VALUE"]);
$inBtnPayments = in_array("BUTTON_PAYMENTS", $arSetting["CATALOG_DETAIL"]["VALUE"]);
$inBtnCredit = in_array("BUTTON_CREDIT", $arSetting["CATALOG_DETAIL"]["VALUE"]);
$inBtnDelivery = in_array("BUTTON_DELIVERY", $arSetting["CATALOG_DETAIL"]["VALUE"]);
$inOldPrice = in_array("OLD_PRICE", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"]);
$inPriceRatio = in_array("PRICE_RATIO", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

$strMainID = $arResult["STR_MAIN_ID"];
$arItemIDs = array(
    "ID" => $strMainID,
    "PICT" => $strMainID."_picture",
    "PRICE" => $strMainID."_price",
    "BUY" => $strMainID."_buy",
    "SUBSCRIBE" => $strMainID."_subscribe",
    "DELAY" => $strMainID."_delay",
    "DELIVERY" => $strMainID."_geolocation_delivery",
    "ARTICLE" => $strMainID."_article",
    "PROPERTIES" => $strMainID."_properties",
    "CONSTRUCTOR" => $strMainID."_constructor",
    "STORE" => $strMainID."_store",
    "PROP_DIV" => $strMainID."_skudiv",
    "PROP" => $strMainID."_prop_",
    "SELECT_PROP_DIV" => $strMainID."_propdiv",
    "SELECT_PROP" => $strMainID."_select_prop_",
    "POPUP_BTN" => $strMainID."_popup_btn",
    "BTN_BUY" => $strMainID."_btn_buy",
    "PRICE_MATRIX_BTN" => $strMainID."_price_ranges_btn"
);
$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

$templateData = array(
    "CURRENCIES" => CUtil::PhpToJSObject($arResult["CURRENCIES"], false, true, true),
    "JS_OBJ" => $strObName
);

// �������� �� canonical ��������
$url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if ($url != $arResult["CANONICAL_PAGE_URL"]) {
    LocalRedirect($arResult["CANONICAL_PAGE_URL"]);
}

//JS//?>

<?
/*vp*/
$VALUES = array();
for ($i = 1; $i <= 4; $i++) {
    $res = CIBlockElement::GetProperty($arParams["IBLOCK_ID"], $arResult["ID"], "sort", "asc", array("ACTIVE" => "Y", "CODE" => "TAKZHE_V_KOMPLEKTE_".$i));
    while($arTmp = $res->Fetch()) {
        $VALUES[$i][] = $arTmp['VALUE'];
    }
}
/* end vp*/
?>

<script type="text/javascript">
    BX.ready(function() {
        //DETAIL_SUBSCRIBE//
        if(!!BX("catalog-subscribe-from"))
            BX("<?=$arItemIDs['SUBSCRIBE']?>").appendChild(BX.style(BX("catalog-subscribe-from"), "display", ""));

        //DETAIL_GEOLOCATION_DELIVERY//
        if(!!BX("geolocation-delivery-from"))
            BX("<?=$arItemIDs['DELIVERY']?>").appendChild(BX.style(BX("geolocation-delivery-from"), "display", ""));

        //OFFERS_LIST_PROPS//
        <?if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST") {
        foreach($arResult["OFFERS"] as $key_off => $arOffer) {?>
        props = BX.findChildren(BX("catalog-offer-item-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>"), {className: "catalog-item-prop"}, true);
        if(!!props && 0 < props.length) {
            for(i = 0; i < props.length; i++) {
                if(!BX.hasClass(props[i], "empty")) {
                    BX("catalog-item-props-mob-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>").appendChild(BX.create(
                        "DIV",
                        {
                            props: {
                                className: "catalog-item-prop"
                            },
                            html: props[i].innerHTML
                        }
                    ));
                }
            }
        }
        <?}
        }?>

        //DETAIL_CONSTRUCTOR//
        if(!!BX("set-constructor-from"))
            BX("<?=$arItemIDs['CONSTRUCTOR']?>").appendChild(BX.style(BX("set-constructor-from"), "display", ""));

        //TABS//
        <?if($arParams["AJAX_OPTION_HISTORY"] !== "Y") {?>
        var tabIndex = window.location.hash.replace("#tab", "") - 1;
        if(tabIndex != -1){
            $(".tabs__tab").eq(tabIndex).click();
            $(".tabs__tab").removeClass('current');
            $(".tabs__box").hide();
            $(".tabs__tab").eq(tabIndex).addClass('current');
            $(".tabs__box").eq(tabIndex).show();

            /* var destination = $('.tabs-catalog-detail').offset().top;
             if ($.browser.safari) {
                 $('body').animate({ scrollTop: destination }, 1100); //1100 - ��������
             } else {
                 $('html').animate({ scrollTop: destination }, 1100);
             }*/
        }

        $(".tabs__tab a[href*=#tab]").click(function() {
            var tabIndex = $(this).attr("href").replace(/(.*)#tab/, "") - 1;
            $(".tabs__tab").eq(tabIndex).click();
        });
        <?} else {?>
        $(".tabs__tab a[href*=#tab]").each(function(){
            $(this).removeAttr("href");
        });
        var tabIndex = window.location.hash.replace("#tab", "")-1;
        if(tabIndex != -1) {
            $(".tabs__box").hide()
            $(".tabs__box").eq(tabIndex).show();
            $(".tabs__tab").removeClass("current");
            $(".tabs__tab").eq(tabIndex).addClass("current");
        }
        <?}?>

        //ACCESSORIES//
        if(!!BX("accessories-to"))
            BX("accessories-to").appendChild(BX.style(BX("accessories-from"), "display", ""));

        //REVIEWS//
        BX("catalog-reviews-to").appendChild(BX.style(BX("catalog-reviews-from"), "display", ""));
        var tabReviewsCount = BX.findChild(BX("<?=$arItemIDs['ID']?>"), {"className": "reviews_count"}, true, false),
            catalogReviewsList = BX.findChild(BX("catalog-reviews-to"), {"className": "catalog-reviews-list"}, true, false);
        if(!!catalogReviewsList)
            var catalogReviewsCount = catalogReviewsList.getAttribute("data-count");
        tabReviewsCount.innerHTML = "(" + (!!catalogReviewsCount ? catalogReviewsCount : 0) + ")";

        //STORES//
        if(!!BX("catalog-detail-stores-from"))
            BX("<?=$arItemIDs['STORE']?>").appendChild(BX.style(BX("catalog-detail-stores-from"), "display", ""));

        //FANCYBOX//
        $(".fancybox").fancybox({
            "transitionIn": "elastic",
            "transitionOut": "elastic",
            "speedIn": 600,
            "speedOut": 200,
            "overlayShow": false,
            "cyclic" : true,
            "padding": 20,
            "titlePosition": "over",
            "onComplete": function() {
                $("#fancybox-title").css({"top":"100%", "bottom":"auto"});
            }
        });
    });
</script>

<?//NEW_HIT_DISCOUNT_TIME_BUY//
$sticker = "";
$timeBuy = "";
if(array_key_exists("PROPERTIES", $arResult) && is_array($arResult["PROPERTIES"])) {
    //FREEDOST//
    if(array_key_exists("FREEDOST", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["FREEDOST"]["VALUE"] == false):
        $sticker .= "<span class='freedost'>".GetMessage("CATALOG_ELEMENT_FREEDOST")."</span>";
    endif;
    //GAR1//
    if(array_key_exists("GARANT1", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["GARANT1"]["VALUE"] == false):
        $sticker .= "<span class='gar1'>".GetMessage("CATALOG_ELEMENT_GARANT1")."</span>";
    endif;
    //GAR3//
    if(array_key_exists("GARANT3", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["GARANT3"]["VALUE"] == false):
        $sticker .= "<span class='gar3'>".GetMessage("CATALOG_ELEMENT_GARANT3")."</span>";
    endif;
    //POE//
    if(array_key_exists("POE", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["POE"]["VALUE"] == false):
        $sticker .= "<span class='poe'>".GetMessage("CATALOG_ELEMENT_POE")."</span>";
    endif;
    //SEN//
    if(array_key_exists("SEN", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["SEN"]["VALUE"] == false):
        $sticker .= "<span class='sen'>".GetMessage("CATALOG_ELEMENT_SEN")."</span>";
    endif;
    //AUTO//
    if(array_key_exists("AUTO", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["AUTO"]["VALUE"] == false):
        $sticker .= "<span class='auto'>".GetMessage("CATALOG_ELEMENT_AUTO")."</span>";
    endif;
	//sticker_avtonom//
	if(array_key_exists("sticker_avtonom", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["sticker_avtonom"]["VALUE"] == false):
        $sticker .= "<span class='sticker_avtonom'>".GetMessage("CATALOG_ELEMENT_sticker_avtonom")."</span>";
    endif;
	//sticker_ZigBee//
	if(array_key_exists("sticker_zb", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["sticker_zb"]["VALUE"] == false):
        $sticker .= "<span class='sticker_zb'>".GetMessage("CATALOG_ELEMENT_sticker_zb")."</span>";
    endif;
	//sticker_ahd//
	if(array_key_exists("sticker_ahd", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["sticker_ahd"]["VALUE"] == false):
        $sticker .= "<span class='sticker_ahd'>".GetMessage("CATALOG_ELEMENT_sticker_ahd")."</span>";
    endif;
	//sticker_ip//
	if(array_key_exists("sticker_ip", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["sticker_ip"]["VALUE"] == false):
        $sticker .= "<span class='sticker_ip'>".GetMessage("CATALOG_ELEMENT_sticker_ip")."</span>";
    endif;
	//sticker_wifi//
	if(array_key_exists("sticker_wifi", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["sticker_wifi"]["VALUE"] == false):
        $sticker .= "<span class='sticker_wifi'>".GetMessage("CATALOG_ELEMENT_sticker_wifi")."</span>";
    endif;
	//sticker_lte//
	if(array_key_exists("sticker_lte", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["sticker_lte"]["VALUE"] == false):
        $sticker .= "<span class='sticker_lte'>".GetMessage("CATALOG_ELEMENT_sticker_lte")."</span>";
    endif;
	//sticker_GSM//
	if(array_key_exists("sticker_gsm", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["sticker_gsm"]["VALUE"] == false):
        $sticker .= "<span class='sticker_gsm'>".GetMessage("CATALOG_ELEMENT_sticker_gsm")."</span>";
    endif;
	//SPEZ5//
    if(array_key_exists("SPEZ5MP", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["SPEZ5MP"]["VALUE"] == false):
        $sticker .= "<span class='spez5'>".GetMessage("CATALOG_ELEMENT_SPEZ5MP")."</span>";
    endif;
	//SPEZ8MP//
    if(array_key_exists("SPEZ8MP", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["SPEZ8MP"]["VALUE"] == false):
        $sticker .= "<span class='spez8mp'>".GetMessage("CATALOG_ELEMENT_SPEZ8MP")."</span>";
    endif;
	//NEW//
    if(array_key_exists("NEWPRODUCT", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["NEWPRODUCT"]["VALUE"] == false)
        $sticker .= "<span class='new'>".GetMessage("CATALOG_ELEMENT_NEWPRODUCT")."</span>";
    //HIT//
    if(array_key_exists("SALELEADER", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["SALELEADER"]["VALUE"] == false)
        $sticker .= "<span class='hit'>".GetMessage("CATALOG_ELEMENT_SALELEADER")."</span>";
    //SPEZ//
    if(array_key_exists("SPEZPEDLOG", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["SPEZPEDLOG"]["VALUE"] == false):
        $sticker .= "<span class='spez'>".GetMessage("CATALOG_ELEMENT_SPEZPEDLOG")."</span>";
    endif;
    //DISCOUNT//
    if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
        if($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST") {
            if($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"] > 0)
                $sticker .= "<span class='discount'>-".$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"]."%</span>";
            else
                if(array_key_exists("DISCOUNT", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
                    $sticker .= "<span class='discount'>%</span>";
        }
    } else {
        if($arResult["MIN_PRICE"]["PERCENT"] > 0)
            $sticker .= "<span class='discount'>-".$arResult["MIN_PRICE"]["PERCENT"]."%</span>";
        else
            if(array_key_exists("DISCOUNT", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["DISCOUNT"]["VALUE"] == false)
                $sticker .= "<span class='discount'>%</span>";
    }
    //TIME_BUY//
    if(array_key_exists("TIME_BUY", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
        if(!empty($arResult["CURRENT_DISCOUNT"]["ACTIVE_TO"]))
            if((isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) || ((!isset($arResult["OFFERS"]) || empty($arResult["OFFERS"])) && $arResult["CAN_BUY"]))
                $timeBuy = "<span class='time_buy_figure'></span><span class='time_buy_text'>".GetMessage("CATALOG_ELEMENT_TIME_BUY")."</span>";
    }
}

//DETAIL_PICTURE_ALT//
$strAlt = (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] != "" ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"] : $arResult["NAME"]);

//DETAIL_PICTURE_TITLE//
$strTitle = (isset($arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"]) && $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] != "" ? $arResult["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"] : $arResult["NAME"]);
//if($_REQUEST['da']){echo'<pre>';print_r($arResult);echo'</pre>';}

//CATALOG_DETAIL//?>
<script>
    gtag('event', 'view_item', {
        'send_to': 'AW-977520268',
        'ecomm_pagetype': 'product',
        'ecomm_prodid': '<?=$arResult['ID']?>', // ������������� ������
        'ecomm_totalvalue': '<?=$arResult['PRICES']['BASE']['DISCOUNT_VALUE_VAT']?>', // ��������� ������
        'non_interaction': true
    });
</script>


<div id="<?=$arItemIDs['ID']?>" class="catalog-detail-element" <?/*itemscope itemtype="http://schema.org/Product"*/?>>
    <?/*<meta content="<?=$arResult['NAME']?>" itemprop="name" />*/?>
    <div class="catalog-detail">
        <div class="column first">
            <div class="catalog-detail-pictures">
                <?//OFFERS_DETAIL_PICTURE//?>
                <div class="catalog-detail-picture" id="<?=$arItemIDs['PICT']?>">
                    <?//OFFERS_PICTURE//
                    if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") {
                    foreach($arResult["OFFERS"] as $key => $arOffer) {
                    $isOfferDetailImg = is_array($arOffer["DETAIL_IMG"]);
                    $offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];?>
                    <div id="detail_picture_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" class="detail_picture<?=($key == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
                        <?/*<meta content="<?=($isOfferDetailImg ? $arOffer['DETAIL_PICTURE']['SRC'] : $isDetailImg ? $arResult['DETAIL_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/no-photo.jpg');?>" itemprop="image" />*/?>
                        <?if($isOfferDetailImg || $isDetailImg) {?>
                        <a <?=($key == $arResult['OFFERS_SELECTED'] ? 'rel="lightbox" ' : '');?>class="catalog-detail-images fancybox" id="catalog-detail-images-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" href="<?=($isOfferDetailImg ? $arOffer['DETAIL_PICTURE']['SRC'] : $arResult['DETAIL_PICTURE']['SRC']);?>">
                            <?} else {?>
                            <div class="catalog-detail-images">
                                <?}
                                if($isOfferDetailImg) {?>
                                    <img class="data-lazy-src" data-lazy-src="<?=$arOffer['DETAIL_IMG']['SRC']?>" width="<?=$arOffer['DETAIL_IMG']['WIDTH']?>" height="<?=$arOffer['DETAIL_IMG']['HEIGHT']?>" alt="<?=$offerName?>" title="<?=$offerName?>" />
                                <?} elseif($isDetailImg) {?>
                                    <img class="data-lazy-src" data-lazy-src="<?=$arResult['DETAIL_IMG']['SRC']?>" width="<?=$arResult['DETAIL_IMG']['WIDTH']?>" height="<?=$arResult['DETAIL_IMG']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
                                <?} else {?>
                                    <img class="data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
                                <?}?>
                                <div class="time_buy_sticker">
                                    <?=$timeBuy?>
                                </div>
                                <div class="sticker">
                                    <?=$sticker;
                                    if($arOffer["MIN_PRICE"]["PERCENT"] > 0) {?>
                                        <span class="discount">-<?=$arOffer["MIN_PRICE"]["PERCENT"]?>%</span>
                                    <?} else {
                                        if(array_key_exists("DISCOUNT", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["DISCOUNT"]["VALUE"] == false) {?>
                                            <span class="discount">%</span>
                                        <?}
                                    }?>
                                </div>
                                <?if(is_array($arResult["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
                                    <img class="manufacturer data-lazy-src" data-lazy-src="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arResult['PROPERTIES']['MANUFACTURER']['NAME']?>" />
                                <?}?>
                                <?=($isOfferDetailImg || $isDetailImg ? "</a>" : "</div>");?>
                            </div>
                            <?}
                            unset($offerName, $isOfferDetailImg);
                            //DETAIL_PICTURE//
                            } else {?>
                            <div class="detail_picture">
                                <?/*<meta content="<?=($isDetailImg ? $arResult['DETAIL_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/no-photo.jpg');?>" itemprop="image" />*/?>
                                <?if($isDetailImg) {?>
                                <a rel="lightbox" class="catalog-detail-images fancybox" href="<?=$arResult['DETAIL_PICTURE']['SRC']?>">
                                    <img class="data-lazy-src" data-lazy-src="<?=$arResult['DETAIL_IMG']['SRC']?>" width="<?=$arResult['DETAIL_IMG']['WIDTH']?>" height="<?=$arResult['DETAIL_IMG']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
                                    <?} else {?>
                                    <div class="catalog-detail-images">
                                        <img class="data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
                                        <?}?>
                                        <div class="time_buy_sticker">
                                            <?=$timeBuy?>
                                        </div>
                                        <div class="sticker">
                                            <?=$sticker?>
                                        </div>
                                        <?if(is_array($arResult["PROPERTIES"]["MANUFACTURER"]["PREVIEW_PICTURE"])) {?>
                                            <img class="manufacturer data-lazy-src" data-lazy-src="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['PROPERTIES']['MANUFACTURER']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['PROPERTIES']['MANUFACTURER']['NAME']?>" title="<?=$arResult['PROPERTIES']['MANUFACTURER']['NAME']?>" />
                                        <?}?>
                                        <?=($isDetailImg ? "</a>" : "</div>");?>
                                    </div>
                                    <?}?>
                            </div>
                            <?//DETAIL_VIDEO_MORE_PHOTO//

                            if(!empty($arResult["PROPERTIES"]["VIDEO"]) || count($arResult["MORE_PHOTO"]) > 0) {?>
                                <div class="clr"></div>
                                <div class="more_photo">
                                    <ul>
                                        <?if(!empty($arResult["PROPERTIES"]["VIDEO"]["VALUE"])) {?>
                                            <li class="catalog-detail-video" style="<?=($arParams['DISPLAY_MORE_PHOTO_WIDTH'] ? 'width:'.$arParams['DISPLAY_MORE_PHOTO_WIDTH'].'px;' : '').($arParams['DISPLAY_MORE_PHOTO_HEIGHT'] ? 'height:'.$arParams['DISPLAY_MORE_PHOTO_HEIGHT'].'px;' : '');?>">
                                                <a rel="lightbox" class="catalog-detail-images fancybox" href="#video">
                                                    <i class="fa fa-play-circle-o"></i>
                                                    <span><?=GetMessage("CATALOG_ELEMENT_VIDEO")?></span>
                                                </a>
                                                <div id="video" style="overflow:hidden;">
                                                    <?=$arResult["PROPERTIES"]["VIDEO"]["~VALUE"]["TEXT"];?>
                                                </div>
                                            </li>
                                        <?}
                                        if(count($arResult["MORE_PHOTO"]) > 0) {
                                            foreach($arResult["MORE_PHOTO"] as $PHOTO) {?>

                                                <li style="<?=($arParams['DISPLAY_MORE_PHOTO_WIDTH'] ? 'width:'.$arParams['DISPLAY_MORE_PHOTO_WIDTH'].'px;' : '').($arParams['DISPLAY_MORE_PHOTO_HEIGHT'] ? 'height:'.$arParams['DISPLAY_MORE_PHOTO_HEIGHT'].'px;' : '');?>">
                                                    <a rel="lightbox" class="catalog-detail-images fancybox" href="<?=$PHOTO['SRC']?>">
                                                        <img class="data-lazy-src" data-lazy-src="<?=$PHOTO['PREVIEW']['SRC']?>" width="<?=$PHOTO['PREVIEW']['WIDTH']?>" height="<?=$PHOTO['PREVIEW']['HEIGHT']?>" alt="<?=$arResult['NAME']?>" title="<?=$arResult['NAME']?>" />
                                                    </a>
                                                </li>
                                            <?}
                                        }
                                        /*vp*/
                                        /*for ($i = 1; $i <= 4; $i++) {
                                            foreach ($VALUES[$i] as &$value) {
                                                $res = CIBlockElement::GetByID($value);
                                                if($ar_res = $res->GetNext()){
                                                    if($ar_res["PREVIEW_PICTURE"]){?>
                                                        <li style="<?=($arParams['DISPLAY_MORE_PHOTO_WIDTH'] ? 'width:'.$arParams['DISPLAY_MORE_PHOTO_WIDTH'].'px;' : '').($arParams['DISPLAY_MORE_PHOTO_HEIGHT'] ? 'height:'.$arParams['DISPLAY_MORE_PHOTO_HEIGHT'].'px;' : '');?>">
                                                            <a rel="lightbox" class="catalog-detail-images fancybox" href="<?=CFile::GetPath((string)$ar_res["PREVIEW_PICTURE"]);?>">
                                                                <? //echo CFile::ShowImage($ar_res["PREVIEW_PICTURE"], 86, 86, 'title="'.$ar_res["NAME"].'  alt="'.$ar_res["NAME"].' "', "", false);?>
                                                                <img class="data-lazy-src" data-lazy-src="<?=CFile::GetPath((string)$ar_res["PREVIEW_PICTURE"]);?>" width="86" height="86" alt="<?=$ar_res['NAME']?>" title="<?=$ar_res['NAME']?>" />
                                                            </a>
                                                        </li>
                                                    <?}
                                                }
                                            }
                                        }*/
                                        /* end vp*/
                                        ?>
                                    </ul>
                                </div>
                            <?}?>
                    </div>
                </div>
                <div class="column second">
                    <div class="catalog-detail">
                        <div class="article_rating">
                            <?//OFFERS_DETAIL_ARTICLE//?>
                            <div class="catalog-detail-article" id="<?=$arItemIDs['ARTICLE']?>">
                                <?//OFFERS_ARTICLE//
                                if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") {
                                    foreach($arResult["OFFERS"] as $key => $arOffer) {?>
                                        <div id="article_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" class="article<?=($key == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
                                            <?=GetMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?>
                                        </div>
                                    <?}
                                    //DETAIL_ARTICLE//
                                } else {?>
                                    <div class="article">
                                        <?=GetMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?>
                                    </div>
                                <?}?>
                            </div>
                            <?//DETAIL_RATING//?>
                            <div class="rating"
                                <?/*if($arResult["PROPERTIES"]["vote_count"]["VALUE"]):?>
                                    itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"
                                <?endif;*/?>
                            >
                                <?$frame = $this->createFrame("vote")->begin("");?>
                                <?$APPLICATION->IncludeComponent("bitrix:iblock.vote", "ajax",
                                    Array(
                                        "DISPLAY_AS_RATING" => "vote_avg",
                                        "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                        "ELEMENT_ID" => $arResult["ID"],
                                        "ELEMENT_CODE" => "",
                                        "MAX_VOTE" => "5",
                                        "VOTE_NAMES" => array("1","2","3","4","5"),
                                        "SET_STATUS_404" => "N",
                                        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                        "CACHE_TIME" => $arParams["CACHE_TIME"],
                                        "CACHE_NOTES" => "",
                                        "READ_ONLY" => "N"
                                    ),
                                    false,
                                    array("HIDE_ICONS" => "Y")
                                );?>
                                <?$frame->end();
                                if($arResult["PROPERTIES"]["vote_count"]["VALUE"]) {?>
                                    <?/*<meta content="<?=round($arResult['PROPERTIES']['vote_sum']['VALUE']/$arResult['PROPERTIES']['vote_count']['VALUE'], 2);?>" itemprop="ratingValue" />*/?>
                                    <?/*<meta content="<?=$arResult['PROPERTIES']['vote_count']['VALUE']?>" itemprop="ratingCount" />*/?>
                                <?} /*else {?>
							<meta content="0" itemprop="ratingValue" />
							<meta content="0" itemprop="ratingCount"  />
						<?}*/?>
                            </div>
                        </div>
                        <?//DETAIL_PREVIEW_TEXT//
                        if(!empty($arResult["PREVIEW_TEXT"])) {?>
                            <div class="catalog-detail-preview-text" <?/*itemprop="description"*/?>>
                                <?=$arResult["PREVIEW_TEXT"]?>
                            </div>
                        <?}

                        /*vp*/
                        if(sizeof($VALUES) > 0) {?>
                            <div class="catalog-detail-preview-text more">
                                <style>
                                    label[for^="more"]{
                                        padding-left: 28px;
                                        display: inline-block;
                                        float: left;
                                        z-index: 9;
                                        margin-top: 0;
                                        position: relative;
                                        cursor: pointer;
                                    }
                                    input[name^="more"]{
                                        float: left;
                                        z-index: 10;
                                        position: absolute;
                                        cursor: pointer;
                                    }
                                    .check-box-more{
                                        display: inline-block;
                                        width: 100%;
                                        position: relative;
                                        padding: 7px 0 7px 0;
                                    }
                                    /*.check-box-more + .separator{
                                        border-top: 1px solid #a0a4bc;
                                        margin: 10px 0;
                                }*/
                                    .advanced-price-value{
                                        color: #8184a1;
                                    }
                                    .real-prod{
                                        display: none;
                                    }
                                    .check-b.final-prod + label{
                                        top: -2px;
                                        position: relative;
                                    }
                                </style>

                                <?
                                for ($i = 1; $i <= 4; $i++) {
                                    $finalSum = 10;
                                    echo '<div class="box-more">';
                                foreach ($VALUES[$i] as &$value) {
                                if(!is_numeric($value)&& ($value!='')){
                                    ?>
                                    <div class="check-box-more">
                                        <input class="check-b final-prod" type="checkbox" name="komplekt<?print_r($i);?>" id="komplekt<?print_r($i);?>" />
                                        <label for="komplekt<?print_r($i);?>">
                                            <? $alternativ = explode("[]", $value);?>
                                            <? $alternativ = $alternativ[1];?>
                                            <?if(!$alternativ):?>
                                                <span><?print_r($value);?></span>
                                            <?else:?>
                                                <span><?=$value;?></span>
                                            <?endif;?>
                                            <span class="advanced-price-value">+ <?=GetMessage('CATALOG_RUR');?></span>
                                        </label>
                                    </div>
                                    <?
                                    continue;
                                };
                                    $res = CIBlockElement::GetByID($value);
                                if($ar_res = $res->GetNext()){
                                    $optimalPrice = CCatalogProduct::GetOptimalPrice($ar_res["ID"], 1, $USER->GetUserGroupArray(), "N");
                                    $finalSum += $optimalPrice["DISCOUNT_PRICE"];
                                    ?>
                                    <div class="check-box-more real-prod" >
                                        <input class="check-b" type="checkbox" ids="<?print_r($ar_res["ID"]);?>" o-price="<?=$optimalPrice["DISCOUNT_PRICE"];?>" name="more<?print_r($ar_res["ID"]);?>" id="more<?print_r($ar_res["ID"]);?>"/>
                                        <label for="more<?print_r($ar_res["ID"]);?>">
                                            <? $alternativ = explode("[]", $value);?>
                                            <? $alternativ = $alternativ[1];?>
                                            <?if(!$alternativ):?>
                                                <span>&laquo;<?print_r($ar_res["NAME"]);?>&raquo;</span>
                                            <?else:?>
                                                <span><?=$alternativ;?></span>
                                            <?endif;?>
                                            <span class="advanced-price-value">+ <?=$optimalPrice["DISCOUNT_PRICE"];?><?=GetMessage('CATALOG_RUR');?></span>
                                        </label>
                                    </div>
                                <?}
                                }?>
                                    <script type="text/javascript">
										$('label[for="komplekt<?=$i?>"] .advanced-price-value').text("+ <?=$finalSum;?> <?=GetMessage('CATALOG_RUR');?>");
                                    </script>

                                    <? if(count($VALUES[$i+1]) > 1) echo '<div class="separator"></div>';
                                    echo '</div>';
                                }?>
                            </div>
                        <?}
                        /* end vp*/

                        //DETAIL_GIFT//
                        if(!empty($arResult["PROPERTIES"]["GIFT"]["FULL_VALUE"])) {?>
                            <div class="catalog-detail-gift">
                                <div class="h3"><?=$arResult["PROPERTIES"]["GIFT"]["NAME"]?></div>
                                <?foreach($arResult["PROPERTIES"]["GIFT"]["FULL_VALUE"] as $key => $arGift) {?>
                                    <div class="gift-item">
                                        <div class="gift-image-cont">
                                            <div class="gift-image">
                                                <div class="gift-image-col">
                                                    <?if(is_array($arGift["PREVIEW_PICTURE"])) {?>
                                                        <img class="data-lazy-src" data-lazy-src="<?=$arGift['PREVIEW_PICTURE']['SRC']?>" width="<?=$arGift['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arGift['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arGift['NAME']?>" title="<?=$arGift['NAME']?>" />
                                                    <?} else {?>
                                                        <img class="data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="70" height="70" alt="<?=$arGift['NAME']?>" title="<?=$arGift['NAME']?>" />
                                                    <?}?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="gift-text"><?=$arGift["NAME"]?></div>
                                    </div>
                                <?}?>
                            </div>
                        <?}
                        //OFFERS_SELECT_PROPS//
                        if((isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") || (isset($arResult["SELECT_PROPS"]) && !empty($arResult["SELECT_PROPS"]))) {?>
                            <div class="catalog-detail-offers-cont">
                                <?//OFFERS_PROPS//
                                if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") {
                                    $arSkuProps = array();?>
                                    <div class="catalog-detail-offers" id="<?=$arItemIDs['PROP_DIV'];?>">
                                        <?foreach($arResult["SKU_PROPS"] as &$arProp) {
                                            if(!isset($arResult["OFFERS_PROP"][$arProp["CODE"]]))
                                                continue;
                                            $arSkuProps[] = array(
                                                "ID" => $arProp["ID"],
                                                "SHOW_MODE" => $arProp["SHOW_MODE"]
                                            );?>
                                            <div class="offer_block" id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_cont">
                                                <div class="h3"><?=htmlspecialcharsex($arProp["NAME"]);?></div>
                                                <ul id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_list" class="<?=$arProp['CODE']?><?=$arProp['SHOW_MODE'] == 'PICT' ? ' COLOR' : '';?>">
                                                    <?foreach($arProp["VALUES"] as $arOneValue) {
                                                        $arOneValue["NAME"] = htmlspecialcharsbx($arOneValue["NAME"]);?>
                                                        <li data-treevalue="<?=$arProp['ID'].'_'.$arOneValue['ID'];?>" data-onevalue="<?=$arOneValue['ID'];?>" style="display:none;">
													<span title="<?=$arOneValue['NAME'];?>">
														<?if("TEXT" == $arProp["SHOW_MODE"]) {
                                                            echo $arOneValue["NAME"];
                                                        } elseif("PICT" == $arProp["SHOW_MODE"]) {
                                                            if(is_array($arOneValue["PICT"])) {?>
                                                                <img class="data-lazy-src" data-lazy-src="<?=$arOneValue['PICT']['SRC']?>" width="<?=$arOneValue['PICT']['WIDTH']?>" height="<?=$arOneValue['PICT']['HEIGHT']?>" alt="<?=$arOneValue['NAME']?>" title="<?=$arOneValue['NAME']?>" />
                                                            <?} else {?>
                                                                <i style="background:#<?=$arOneValue['HEX']?>"></i>
                                                            <?}
                                                        }?>
													</span>
                                                        </li>
                                                    <?}?>
                                                </ul>
                                                <div class="bx_slide_left" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_left" data-treevalue="<?=$arProp['ID']?>"></div>
                                                <div class="bx_slide_right" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_right" data-treevalue="<?=$arProp['ID']?>"></div>
                                            </div>
                                        <?}
                                        unset($arProp);?>
                                    </div>
                                <?}
                                //SELECT_PROPS//
                                if(isset($arResult["SELECT_PROPS"]) && !empty($arResult["SELECT_PROPS"])) {
                                    $arSelProps = array();?>
                                    <div class="catalog-detail-offers" id="<?=$arItemIDs['SELECT_PROP_DIV'];?>">
                                        <?foreach($arResult["SELECT_PROPS"] as $key => &$arProp) {
                                            $arSelProps[] = array(
                                                "ID" => $arProp["ID"]
                                            );?>
                                            <div class="offer_block" id="<?=$arItemIDs['SELECT_PROP'].$arProp['ID'];?>">
                                                <div class="h3"><?=htmlspecialcharsex($arProp["NAME"]);?></div>
                                                <ul class="<?=$arProp['CODE']?>">
                                                    <?$props = array();
                                                    foreach($arProp["DISPLAY_VALUE"] as $arOneValue) {
                                                        $props[$key] = array(
                                                            "NAME" => $arProp["NAME"],
                                                            "CODE" => $arProp["CODE"],
                                                            "VALUE" => strip_tags($arOneValue)
                                                        );
                                                        $props[$key] = !empty($props[$key]) ? strtr(base64_encode(serialize($props[$key])), "+/=", "-_,") : "";?>
                                                        <li data-select-onevalue="<?=$props[$key]?>">
                                                            <span title="<?=$arOneValue;?>"><?=$arOneValue?></span>
                                                        </li>
                                                    <?}?>
                                                </ul>
                                            </div>
                                        <?}
                                        unset($arProp);?>
                                    </div>
                                <?}?>
                            </div>
                        <?}
                        //DETAIL_ADVANTAGES//
                        if($inAdvantages && !empty($arResult["ADVANTAGES"])) {
                            global $arAdvFilter;
                            $arAdvFilter = array(
                                "ID" => $arResult["ADVANTAGES"],
                                "HIDE_ICONS" => "Y"
                            );?>
                            <?$APPLICATION->IncludeComponent("bitrix:main.include", "",
                                Array(
                                    "AREA_FILE_SHOW" => "file",
                                    "PATH" => SITE_DIR."include/advantages.php",
                                    "AREA_FILE_RECURSIVE" => "N",
                                    "EDIT_MODE" => "html",
                                ),
                                false,
                                Array("HIDE_ICONS" => "Y")
                            );?>
                        <?}?>
                        <div class="column three">
                            <div class="price_buy_detail" <?/*itemprop="offers" itemscope itemtype="http://schema.org/Offer"*/?>>
                                <?//OFFERS_DETAIL_PRICE//?>
                                <div class="catalog-detail-price" id="<?=$arItemIDs['PRICE'];?>">
                                    <?//OFFERS_PRICE//
                                    if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
                                        if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") {
                                            foreach($arResult["OFFERS"] as $key => $arOffer) {?>
                                                <div id="detail_price_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" class="detail_price<?=($key == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
                                                    <?if($arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
                                                        <span class="catalog-detail-item-no-price">
													<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
                                                    <?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arOffer["CATALOG_MEASURE_RATIO"] : "1")." ".$arOffer["CATALOG_MEASURE_NAME"];?>
												</span>
                                                    <?} else {
                                                        if($arOffer["MIN_PRICE"]["RATIO_PRICE"] < $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
                                                            <span class="catalog-detail-item-price-old">
														<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
													</span>
                                                            <span class="catalog-detail-item-price-percent">
														<?=GetMessage('CATALOG_ELEMENT_SKIDKA')." ".$arOffer["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
													</span>
                                                        <?}?>
                                                        <span class="catalog-detail-item-price">
													<span class="catalog-detail-item-price-current">
														<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_PRICE"]?>
													</span>
													<span class="unit">
														<?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arOffer["CATALOG_MEASURE_RATIO"] : "1")." ".$arOffer["CATALOG_MEASURE_NAME"];?>
													</span>
												</span>
                                                        <?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
                                                            <span class="catalog-detail-item-price-reference">
														<?=CCurrencyLang::CurrencyFormat($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arOffer["MIN_PRICE"]["CURRENCY"], true);?>
													</span>
                                                        <?}
                                                    }?>
                                                    <?/*<meta id="Origin" itemprop="price" content="<?=$arOffer['MIN_PRICE']['RATIO_PRICE']?>" />
                                                    <meta itemprop="priceCurrency" content="<?=$arOffer['MIN_PRICE']['CURRENCY']?>" />*/?>
                                                    <?//OFFERS_PRICE_RANGES//
                                                    if($arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
                                                        <div class="catalog-detail-price-ranges">
                                                            <?$i = 0;
                                                            foreach($arOffer["ITEM_QUANTITY_RANGES"] as $range) {
                                                                if($range["HASH"] !== "ZERO-INF") {
                                                                    $itemPrice = false;
                                                                    foreach($arOffer["ITEM_PRICES"] as $itemPrice) {
                                                                        if($itemPrice["QUANTITY_HASH"] === $range["HASH"]) {
                                                                            break;
                                                                        }
                                                                    }

                                                                    if($itemPrice) {?>
                                                                        <div class="catalog-detail-price-ranges__row">
                                                                            <div class="catalog-detail-price-ranges__sort">
                                                                                <?if(is_infinite($range["SORT_TO"])) {
                                                                                    echo GetMessage("CATALOG_ELEMENT_FROM")." ".$range["SORT_FROM"];
                                                                                } else {
                                                                                    echo $range["SORT_FROM"]." - ".$range["SORT_TO"];
                                                                                }?>
                                                                            </div>
                                                                            <div class="catalog-detail-price-ranges__dots"></div>
                                                                            <div class="catalog-detail-price-ranges__price"><?=$arOffer["ITEM_PRICES"][$i]["RATIO_PRICE"]?></div>
                                                                            <span class="unit">
																		<?$arCurFormat = CCurrencyLang::GetCurrencyFormat($arResult['TOTAL_OFFERS']['MIN_PRICE']['CURRENCY'], LANGUAGE_ID);
                                                                        $currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
                                                                        ?>
                                                                        <?=$currency?>
																	</span>
                                                                        </div>
                                                                        <?$i++;
                                                                    }
                                                                }
                                                            }?>
                                                        </div>
                                                        <?unset($itemPrice, $range);
                                                    }
                                                    //OTHER_PRICE//
                                                    if(count($arOffer["PRICE_MATRIX_SHOW"]["COLS"]) > 1) {?>
                                                        <div class="catalog-detail-price-ranges other-price">
                                                            <?foreach($arOffer["PRICE_MATRIX_SHOW"]["COLS"] as $key_matrix => $item) {
                                                                $priceMatrix[$key_matrix] = $arOffer["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix];
                                                                $oneRange = array_pop($priceMatrix[$key_matrix]);
                                                                array_push($priceMatrix[$key_matrix], $oneRange);
                                                                $countRange = count($arOffer["PRICE_MATRIX_SHOW"]["MATRIX"][$key_matrix]);?>
                                                                <div class="catalog-detail-price-ranges__row">
                                                                    <div class="catalog-detail-price-ranges__sort">
                                                                        <?=$item["NAME_LANG"]?>
                                                                    </div>
                                                                    <div class="catalog-detail-price-ranges__dots"></div>
                                                                    <?if($countRange > 1) {?>
                                                                        <span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM");?></span>
                                                                    <?}?>
                                                                    <div class="catalog-detail-price-ranges__price"><?=$oneRange["DISCOUNT_PRICE"]?></div>
                                                                    <span class="unit"><?=$oneRange["PRINT_CURRENCY"]?></span>
                                                                    <?if($countRange > 1):?>
                                                                        <span class="catalog-item-price-ranges-wrap">
																	<a id="<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>_<?=$key_matrix?>" data-key="<?=$key_matrix?>"  class="catalog-item-price-ranges" href="javascript:void(0);">
																		<i class="fa fa-question-circle-o" ></i>
																	</a>
																</span>
                                                                        <?$arResult["ID_PRICE_MATRIX_BTN"][$key][$key_matrix] = $arItemIDs['ID'].'_'.$arOffer['ID']."_".$key_matrix;
                                                                    endif;?>
                                                                </div>
                                                                <?unset($countRange);
                                                            }?>
                                                        </div>
                                                    <?}
                                                    //OFFERS_AVAILABILITY//?>
                                                    <div class="available">
                                                        <?if($arOffer["CAN_BUY"]) {?>
                                                            <?/*<meta content="InStock" itemprop="availability" />*/?>
                                                            <div class="avl">
                                                                <i class="fa fa-check-circle"></i>
                                                                <span>
															<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
                                                            if($arOffer["CHECK_QUANTITY"] && $inProductQnt)
                                                                echo " ".$arOffer["CATALOG_QUANTITY"];?>
														</span>
                                                            </div>
                                                        <?} elseif(!$arOffer["CAN_BUY"]) {?>
                                                            <?/*<meta content="OutOfStock" itemprop="availability" />*/?>
                                                            <div class="not_avl">
                                                                <i class="fa fa-times-circle"></i>
                                                                <span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
                                                            </div>
                                                        <?}?>
                                                    </div>
                                                </div>
                                            <?}
                                            //OFFERS_LIST_PRICE//
                                        } elseif($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST") {?>
                                            <div class="detail_price">
                                                <?if($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
                                                    <span class="catalog-detail-item-no-price">
												<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
                                                <?=GetMessage("CATALOG_ELEMENT_UNIT")." ".$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]." ".$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?>
											</span>
                                                <?} else {
                                                    if($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] < $arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
                                                        <span class="catalog-detail-item-price-old">
													<?=$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
												</span>
                                                        <span class="catalog-detail-item-price-percent">
													<?=GetMessage('CATALOG_ELEMENT_SKIDKA')." ".$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
												</span>
                                                    <?}?>
                                                    <span class="catalog-detail-item-price">
												<?=($arResult["TOTAL_OFFERS"]["FROM"] == "Y" ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span> " : "").$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_PRICE"];?>
												<span class="unit">
													<?=GetMessage("CATALOG_ELEMENT_UNIT")." ".$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]." ".$arResult["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];?>
												</span>
											</span>
                                                    <?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
                                                        <span class="catalog-detail-item-price-reference">
													<?=CCurrencyLang::CurrencyFormat($arResult["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
												</span>
                                                    <?}
                                                }?>
                                                <?/*<meta id="Origin" itemprop="price" content="<?=$arResult['TOTAL_OFFERS']['MIN_PRICE']['RATIO_PRICE']?>" />*/?>
                                                <?/*<meta itemprop="priceCurrency" content="<?=$arResult['TOTAL_OFFERS']['MIN_PRICE']['CURRENCY']?>" />*/?>
                                                <?//OFFERS_LIST_AVAILABILITY//?>
                                                <div class="available">
                                                    <?if($arResult["TOTAL_OFFERS"]["QUANTITY"] > 0 || !$arResult["CHECK_QUANTITY"]) {?>
                                                        <?/*<meta content="InStock" itemprop="availability" />*/?>
                                                        <div class="avl">
                                                            <i class="fa fa-check-circle"></i>
                                                            <span>
														<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
                                                        if($arResult["TOTAL_OFFERS"]["QUANTITY"] > 0 && $inProductQnt)
                                                            echo " ".$arResult["TOTAL_OFFERS"]["QUANTITY"];?>
													</span>
                                                        </div>
                                                    <?} else {?>
                                                        <?/*<meta content="OutOfStock" itemprop="availability" />*/?>
                                                        <div class="not_avl">
                                                            <i class="fa fa-times-circle"></i>
                                                            <span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
                                                        </div>
                                                    <?}?>
                                                </div>
                                            </div>
                                        <?}
                                        //OFFERS_TIME_BUY_QUANTITY//
                                        if(array_key_exists("TIME_BUY", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
                                            if(!empty($arResult["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
                                                if($arResult["TOTAL_OFFERS"]["QUANTITY"] > 0) {
                                                    $startQnt = $arResult["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arResult["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arResult["TOTAL_OFFERS"]["QUANTITY"];
                                                    $currQnt = $arResult["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arResult["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arResult["TOTAL_OFFERS"]["QUANTITY"];
                                                    $currQntPercent = round($currQnt * 100 / $startQnt);
                                                } else {
                                                    $currQntPercent = 100;
                                                }?>

                                                <div class="progress_bar_block">
                                                    <span class="progress_bar_title"><?=GetMessage("CATALOG_ELEMENT_QUANTITY_PERCENT")?></span>
                                                    <div class="progress_bar_cont">
                                                        <div class="progress_bar_bg">
                                                            <div class="progress_bar_line" style="width:<?=$currQntPercent?>%;"></div>
                                                        </div>
                                                    </div>
                                                    <span class="progress_bar_percent"><?=$currQntPercent?>%</span>
                                                </div>
                                            <?}
                                        }
                                        //DETAIL_PRICE//
                                    } else {
                                        if($arResult["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
                                            <span class="catalog-detail-item-no-price">
										<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
                                        <?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arResult["CATALOG_MEASURE_RATIO"] : "1")." ".$arResult["CATALOG_MEASURE_NAME"];?>
									</span>
                                        <?} else {
                                            if($arResult["MIN_PRICE"]["RATIO_PRICE"] < $arResult["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
                                                <span class="catalog-detail-item-price-old">
											<?=$arResult["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
										</span>
                                                <span class="catalog-detail-item-price-percent">
											<?=GetMessage('CATALOG_ELEMENT_SKIDKA')." ".$arResult["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"];?>
										</span>
                                            <?}?>
                                            <span class="catalog-detail-item-price">
										<span class="catalog-detail-item-price-current">
											<?=$arResult["MIN_PRICE"]["PRINT_RATIO_PRICE"]?>
										</span>
										<span class="unit">
											<?=GetMessage("CATALOG_ELEMENT_UNIT")." ".(($inPriceRatio) ? $arResult["CATALOG_MEASURE_RATIO"] : "1")." ".$arResult["CATALOG_MEASURE_NAME"];?>
										</span>
									</span>
                                            <?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
                                                <span class="catalog-detail-item-price-reference">
											<?=CCurrencyLang::CurrencyFormat($arResult["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["MIN_PRICE"]["CURRENCY"], true);?>
										</span>
                                            <?}
                                        }?>
                                        <?/*<meta id="Origin" itemprop="price" value="<?=$arResult['MIN_PRICE']['RATIO_PRICE']?>" content="<?=$arResult['MIN_PRICE']['RATIO_PRICE']?>" />
                                        <meta itemprop="priceCurrency" content="<?=$arResult['MIN_PRICE']['CURRENCY']?>" />*/?>
                                        <?//DETAIL_PRICE_RANGES//
                                        if($arParams["USE_PRICE_COUNT"] && count($arResult["ITEM_QUANTITY_RANGES"]) > 1) {?>
                                            <div class="catalog-detail-price-ranges">
                                                <?$i = 0;
                                                foreach($arResult["ITEM_QUANTITY_RANGES"] as $range) {
                                                    if($range["HASH"] !== "ZERO-INF") {
                                                        $itemPrice = false;
                                                        foreach($arResult["ITEM_PRICES"] as $itemPrice) {
                                                            if($itemPrice["QUANTITY_HASH"] === $range["HASH"]) {
                                                                break;
                                                            }
                                                        }
                                                        if($itemPrice) {?>
                                                            <div class="catalog-detail-price-ranges__row">
                                                                <div class="catalog-detail-price-ranges__sort">
                                                                    <?if(is_infinite($range["SORT_TO"])) {
                                                                        echo GetMessage("CATALOG_ELEMENT_FROM")." ".$range["SORT_FROM"];
                                                                    } else {
                                                                        echo $range["SORT_FROM"]." - ".$range["SORT_TO"];
                                                                    }?>
                                                                </div>
                                                                <div class="catalog-detail-price-ranges__dots"></div>
                                                                <div class="catalog-detail-price-ranges__price"><?=$arResult["ITEM_PRICES"][$i]["RATIO_PRICE"]?></div>
                                                                <span class="unit">
															<?$arCurFormat = CCurrencyLang::GetCurrencyFormat($arResult['MIN_PRICE']['CURRENCY'], LANGUAGE_ID);
                                                            $currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);
                                                            ?>
                                                            <?=$currency?>
														</span>
                                                            </div>
                                                            <?$i++;
                                                        }
                                                    }
                                                }?>
                                            </div>
                                            <?unset($itemPrice, $range);
                                        }?>
                                        <?//OTHER_PRICE//?>
                                        <?if(count($arResult["PRICE_MATRIX_SHOW"]["COLS"]) > 1) {?>
                                            <div class="catalog-detail-price-ranges other-price">
                                                <?foreach($arResult["PRICE_MATRIX_SHOW"]["COLS"] as $key => $item) {
                                                    $priceMatrix[$key] = $arResult["PRICE_MATRIX_SHOW"]["MATRIX"][$key];
                                                    $oneRange = array_pop($priceMatrix[$key]);
                                                    array_push($priceMatrix[$key], $oneRange);
                                                    $countRange = count($arResult["PRICE_MATRIX_SHOW"]["MATRIX"][$key]);?>
                                                    <div class="catalog-detail-price-ranges__row">
                                                        <div class="catalog-detail-price-ranges__sort">
                                                            <?=$item["NAME_LANG"]?>
                                                        </div>
                                                        <div class="catalog-detail-price-ranges__dots"></div>
                                                        <?if($countRange > 1) {?>
                                                            <span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM");?></span>
                                                        <?}?>
                                                        <div class="catalog-detail-price-ranges__price"><?=$oneRange["DISCOUNT_PRICE"]?></div>
                                                        <span class="unit"><?=$oneRange["PRINT_CURRENCY"]?></span>
                                                        <?if($countRange > 1):?>
                                                            <span class="catalog-item-price-ranges-wrap">
														<a id="<?=$arItemIDs['PRICE_MATRIX_BTN']?>_<?=$key?>" data-key="<?=$key?>"  class="catalog-item-price-ranges" href="javascript:void(0);">
															<i class="fa fa-question-circle-o" ></i>
														</a>
													</span>
                                                            <?$arIdPriceMatrix[$key] = $arItemIDs['PRICE_MATRIX_BTN']."_".$key;
                                                        endif;?>
                                                    </div>
                                                    <?unset($countRange);
                                                }
                                                ?>
                                            </div>
                                        <?}
                                        //DETAIL_AVAILABILITY//?>
                                        <div class="available">
                                            <?if($arResult["CAN_BUY"]) {?>
                                                <?/*<meta content="InStock" itemprop="availability" />*/?>
                                                <div class="avl">
                                                    <i class="fa fa-check-circle"></i>
                                                    <span>
												<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
                                                if($arResult["CHECK_QUANTITY"] && $inProductQnt)
                                                    echo " ".$arResult["CATALOG_QUANTITY"];?>
											</span>
                                                </div>
                                            <?} elseif(!$arResult["CAN_BUY"]) {?>
                                                <?/*<meta content="OutOfStock" itemprop="availability" />*/?>
                                                <div class="not_avl">
                                                    <i class="fa fa-times-circle"></i>
                                                    <span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
                                                </div>
                                            <?}?>
                                        </div>
                                        <?//DETAIL_TIME_BUY_QUANTITY//
                                        if(array_key_exists("TIME_BUY", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
                                            if(!empty($arResult["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
                                                if($arResult["CAN_BUY"]) {
                                                    if($arResult["CHECK_QUANTITY"]) {
                                                        $startQnt = $arResult["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arResult["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arResult["CATALOG_QUANTITY"];
                                                        $currQnt = $arResult["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arResult["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arResult["CATALOG_QUANTITY"];
                                                        $currQntPercent = round($currQnt * 100 / $startQnt);
                                                    } else {
                                                        $currQntPercent = 100;
                                                    }?>

                                                    <div class="progress_bar_block">
                                                        <span class="progress_bar_title"><?=GetMessage("CATALOG_ELEMENT_QUANTITY_PERCENT")?></span>
                                                        <div class="progress_bar_cont">
                                                            <div class="progress_bar_bg">
                                                                <div class="progress_bar_line" style="width:<?=$currQntPercent?>%;"></div>
                                                            </div>
                                                        </div>
                                                        <span class="progress_bar_percent"><?=$currQntPercent?>%</span>
                                                    </div>
                                                <?}
                                            }
                                        }
                                    }?>
                                </div>
                                <?//OFFERS_DETAIL_TIME_BUY_TIMER_BUY//?>
                                <div class="catalog-detail-buy" id="<?=$arItemIDs['BUY'];?>">
                                    <?if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
                                        //OFFERS_TIME_BUY_TIMER//
                                    if(array_key_exists("TIME_BUY", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
                                    if(!empty($arResult["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
                                        $new_date = ParseDateTime($arResult["CURRENT_DISCOUNT"]["ACTIVE_TO"], FORMAT_DATETIME);?>
                                        <script type="text/javascript">
                                            $(function() {
                                                $("#time_buy_timer_<?=$arItemIDs['ID']?>").countdown({
                                                    until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
                                                    format: "DHMS",
                                                    expiryText: "<div class='over'><?=GetMessage('CATALOG_ELEMENT_TIME_BUY_EXPIRY')?></div>"
                                                });
                                            });
                                        </script>
                                        <div class="time_buy_cont">
                                            <div class="time_buy_clock">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                            <div class="time_buy_timer" id="time_buy_timer_<?=$arItemIDs['ID']?>"></div>
                                        </div>
                                    <?}
                                    }
                                    //OFFERS_BUY//
                                    if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") {
                                    foreach($arResult["OFFERS"] as $key => $arOffer) {?>
                                        <div id="buy_more_detail_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" class="buy_more_detail<?=($key == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
                                            <?$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];
                                            $properties = array();
                                            foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
                                                if($propOffer["PROPERTY_TYPE"] != "S")
                                                    $properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
                                            }
                                            $properties = implode("; ", $properties);
                                            $elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;
                                            if($arOffer["CAN_BUY"]) {
                                                if($arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) {
                                                    //OFFERS_ASK_PRICE//?>
                                                    <form action="javascript:void(0)">
                                                        <input type="hidden" name="ACTION" value="ask_price" />
                                                        <input type="hidden" name="NAME" value="<?=$elementName?>" />
                                                        <button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo_detail"><i class="fa fa-comment-o"></i><span><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE")?></span></button>
                                                    </form>
                                                <?} else {?>
                                                    <div class="add2basket_block">
                                                        <form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
                                                            <div class="qnt_cont">
                                                                <a href="javascript:void(0)" class="minus"><span>-</span></a>
                                                                <input type="text" id="quantity_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['MIN_PRICE']['MIN_QUANTITY']?>" />
                                                                <a href="javascript:void(0)" class="plus"><span>+</span></a>
                                                            </div>
                                                            <input type="hidden" name="ID" class="offer_id" value="<?=$arOffer['ID']?>" />
                                                            <?
                                                            $props = array();
                                                            if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
                                                                $props[] = array(
                                                                    "NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
                                                                    "CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
                                                                    "VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
                                                                );
                                                            }
                                                            foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
                                                                if($propOffer["PROPERTY_TYPE"] != "S") {
                                                                    $props[] = array(
                                                                        "NAME" => $propOffer["NAME"],
                                                                        "CODE" => $propOffer["CODE"],
                                                                        "VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
                                                                    );
                                                                }
                                                            }
                                                            $props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
                                                            <input type="hidden" name="PROPS" id="props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="<?=$props?>" />
                                                            <?if(!empty($arResult["SELECT_PROPS"])) {?>
                                                                <input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="" />
                                                            <?}?>
                                                            <button onclick="ym(22745254,'reachGoal','addcart'); ga('send', 'event', 'addcart', 'click'); add2cart_gtag_el();" type="button" class="btn_buy detail" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?></span></button>
                                                        </form>
                                                        <?//OFFERS_BUY_ONE_CLICK//
                                                        if($inBtnBoc) {?>
                                                            <button onclick="ym(22745254,'reachGoal','byoneclick'); ga('send', 'event', 'byoneclick', 'click');" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy boc_anch" data-action="boc"><i class="fa fa-bolt"></i><span><?=GetMessage('CATALOG_ELEMENT_BOC')?></span></button>
                                                        <?}
                                                        //OFFERS_CHEAPER
                                                        if($inBtnCheaper) {?>
                                                            <form action="javascript:void(0)" class="cheaper_form">
                                                                <input type="hidden" name="ACTION" value="cheaper" />
                                                                <input type="hidden" name="NAME" value="<?=$elementName?>" />
                                                                <input type="hidden" name="PRICE" value="<?=$arOffer['MIN_PRICE']['PRINT_RATIO_PRICE']?>" />
                                                                <button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo cheaper_anch"><i class="fa fa-commenting-o"></i><span><?=GetMessage('CATALOG_ELEMENT_CHEAPER')?></span></button>
                                                            </form>
                                                        <?}?>
                                                    </div>
                                                <?}
                                            } elseif(!$arOffer["CAN_BUY"]) {
                                                //OFFERS_UNDER_ORDER?>
                                                <form action="javascript:void(0)" class="apuo_form">
                                                    <input type="hidden" name="ACTION" value="under_order" />
                                                    <input type="hidden" name="NAME" value="<?=$elementName?>" />
                                                    <button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo_detail"><i class="fa fa-clock-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></button>
                                                </form>
                                            <?}?>
                                        </div>
                                    <?}
                                        //OFFERS_LIST_BUY//
                                    } elseif($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST") {?>
                                        <div class="buy_more_detail">
                                            <script type="text/javascript">
                                                $(function() {
                                                    $("button[name=choose_offer]").click(function() {
                                                        var destination = $("#catalog-detail-offers-list").offset().top;
                                                        $("html:not(:animated),body:not(:animated)").animate({scrollTop: destination}, 500);
                                                        return false;
                                                    });
                                                });
                                            </script>
                                            <button onclick="ym(22745254,'reachGoal','addcart'); ga('send', 'event', 'addcart', 'click'); add2cart_gtag_el();" class="btn_buy detail" name="choose_offer"><?=GetMessage('CATALOG_ELEMENT_CHOOSE_OFFER')?></button>
                                        </div>
                                    <?}
                                    } else {
                                    //DETAIL_TIME_BUY_TIMER//
                                    if(array_key_exists("TIME_BUY", $arResult["PROPERTIES"]) && !$arResult["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
                                    if(!empty($arResult["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
                                    if($arResult["CAN_BUY"]) {
                                    $new_date = ParseDateTime($arResult["CURRENT_DISCOUNT"]["ACTIVE_TO"], FORMAT_DATETIME);?>
                                        <script type="text/javascript">
                                            $(function() {
                                                $("#time_buy_timer_<?=$arItemIDs['ID']?>").countdown({
                                                    until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
                                                    format: "DHMS",
                                                    expiryText: "<div class='over'><?=GetMessage('CATALOG_ELEMENT_TIME_BUY_EXPIRY')?></div>"
                                                });
                                            });
                                        </script>
                                        <div class="time_buy_cont">
                                            <div class="time_buy_clock">
                                                <i class="fa fa-clock-o"></i>
                                            </div>
                                            <div class="time_buy_timer" id="time_buy_timer_<?=$arItemIDs['ID']?>"></div>
                                        </div>
                                    <?}
                                    }
                                    }
                                    //DETAIL_BUY//?>
                                        <div class="buy_more_detail">
                                            <?if($arResult["CAN_BUY"]) {
                                                if($arResult["MIN_PRICE"]["RATIO_PRICE"] <= 0) {
                                                    //DETAIL_ASK_PRICE//?>
                                                    <a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo_detail" href="javascript:void(0)" rel="nofollow" data-action="ask_price"><i class="fa fa-comment-o"></i><span><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE")?></span></a>
                                                <?} else {?>
                                                    <form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
                                                        <div class="qnt_cont">
                                                            <a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
                                                            <input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arResult['MIN_PRICE']['MIN_QUANTITY']?>"/>
                                                            <a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
                                                        </div>
                                                        <input type="hidden" name="ID" class="id" value="<?=$arResult['ID']?>" />
                                                        <?$props = array();
                                                        if(!empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
                                                            $props[] = array(
                                                                "NAME" => $arResult["PROPERTIES"]["ARTNUMBER"]["NAME"],
                                                                "CODE" => $arResult["PROPERTIES"]["ARTNUMBER"]["CODE"],
                                                                "VALUE" => $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]
                                                            );
                                                            $props = strtr(base64_encode(serialize($props)), "+/=", "-_,");?>
                                                            <input type="hidden" name="PROPS" id="props_<?=$arItemIDs['ID']?>" value="<?=$props?>" />
                                                        <?}
                                                        if(!empty($arResult["SELECT_PROPS"])) {?>
                                                            <input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID']?>" value="" />
                                                        <?}?>
                                                        <button onclick="ym(22745254,'reachGoal','addcart'); ga('send', 'event', 'addcart', 'click'); add2cart_gtag_el();" type="button" id="<?=$arItemIDs['BTN_BUY']?>" class="btn_buy detail" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?></span></button>
                                                    </form>
                                                    <?//DETAIL_BUY_ONE_CLICK//
                                                    if($inBtnBoc) {?>
                                                        <button onclick="ym(22745254,'reachGoal','byoneclick'); ga('send', 'event', 'byoneclick', 'click');" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy boc_anch" data-action="boc"><i class="fa fa-bolt"></i><span><?=GetMessage('CATALOG_ELEMENT_BOC')?></span></button>
                                                    <?}
                                                    //DETAIL_CHEAPER
                                                    if($inBtnCheaper) {?>
                                                        <a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo cheaper_anch" href="javascript:void(0)" rel="nofollow" data-action="cheaper"><i class="fa fa-commenting-o"></i><span><?=GetMessage('CATALOG_ELEMENT_CHEAPER')?></span></a>
                                                    <?}
                                                }
                                            } elseif(!$arResult["CAN_BUY"]) {
                                                //DETAIL_UNDER_ORDER//?>
                                                <a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo_detail" href="javascript:void(0)" rel="nofollow" data-action="under_order"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
                                            <?}?>
                                        </div>
                                    <?}?>
                                </div>
                                <?//DETAIL_SUBSCRIBE//?>
                                <div id="<?=$arItemIDs['SUBSCRIBE']?>"></div>
                                <?//COMPARE_DELAY//?>
                                <div class="compare_delay">
                                    <?//DETAIL_COMPARE//
                                    if($arParams["DISPLAY_COMPARE"] == "Y") {?>
                                        <div class="compare">
                                            <a href="javascript:void(0)" class="catalog-item-compare" id="catalog_add2compare_link_<?=$arItemIDs['ID']?>" onclick="return addToCompare('<?=$arResult["COMPARE_URL"]?>', 'catalog_add2compare_link_<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>');" rel="nofollow"><span class="compare_cont"><i class="fa fa-bar-chart"></i><i class="fa fa-check"></i><span class="compare_text"><?=GetMessage('CATALOG_ELEMENT_ADD_TO_COMPARE')?></span></span></a>
                                        </div>
                                    <?}?>
                                    <div class="catalog-detail-delay" id="<?=$arItemIDs['DELAY']?>">
                                        <?//OFFERS_DELAY//
                                        if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
                                            if($arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") {
                                                foreach($arResult["OFFERS"] as $key => $arOffer) {
                                                    if($arOffer["CAN_BUY"] && $arOffer["MIN_PRICE"]["RATIO_PRICE"] > 0) {
                                                        $props = array();
                                                        if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
                                                            $props[] = array(
                                                                "NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
                                                                "CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
                                                                "VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
                                                            );
                                                        }
                                                        foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
                                                            if($propOffer["PROPERTY_TYPE"] != "S") {
                                                                $props[] = array(
                                                                    "NAME" => $propOffer["NAME"],
                                                                    "CODE" => $propOffer["CODE"],
                                                                    "VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
                                                                );
                                                            }
                                                        }
                                                        $props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
                                                        <div id="delay_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" class="delay<?=($key == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
                                                            <a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" class="catalog-item-delay" onclick="ym(22745254,'reachGoal','aside'); ga('send', 'event', 'delay', 'click'); return addToDelay('<?=$arOffer["ID"]?>', 'quantity_<?=$arItemIDs['ID'].'_'.$arOffer["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs['ID'].'-'.$arOffer["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><span class="delay_cont"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span class="delay_text"><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></span></a>
                                                        </div>
                                                    <?}
                                                }
                                            }
                                            //DETAIL_DELAY//
                                        } else {
                                            if($arResult["CAN_BUY"] && $arResult["MIN_PRICE"]["RATIO_PRICE"] > 0) {
                                                $props = array();
                                                if(!empty($arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
                                                    $props[] = array(
                                                        "NAME" => $arResult["PROPERTIES"]["ARTNUMBER"]["NAME"],
                                                        "CODE" => $arResult["PROPERTIES"]["ARTNUMBER"]["CODE"],
                                                        "VALUE" => $arResult["PROPERTIES"]["ARTNUMBER"]["VALUE"]
                                                    );
                                                    $props = strtr(base64_encode(serialize($props)), "+/=", "-_,");
                                                }?>
                                                <div class="delay">
                                                    <a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID']?>" class="catalog-item-delay" onclick="ym(22745254,'reachGoal','aside'); ga('send', 'event', 'delay', 'click'); return addToDelay('<?=$arResult["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><span class="delay_cont"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span class="delay_text"><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></span></a>
                                                </div>
                                            <?}
                                        }?>
                                    </div>
                                </div>
                                <?//DETAIL_DELIVERY//
                                if(!empty($arResult["PROPERTIES"]["DELIVERY"]["VALUE"])) {?>
                                    <div class="catalog-detail-delivery">
                                        <span class="name"><?=$arResult["PROPERTIES"]["DELIVERY"]["NAME"]?></span>
                                        <span class="val"><?=$arResult["PROPERTIES"]["DELIVERY"]["VALUE"]?></span>
                                    </div>
                                <?}
                                //DETAIL_PAYMENTS//
                                global $arPayIcFilter;
                                $arPayIcFilter = array(
                                    "!PROPERTY_SHOW_PRODUCT_DETAIL" => false,
                                    "HIDE_ICONS" => "Y"
                                );?>
                                <?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/payments_icons.php"), false, array("HIDE_ICONS" => "Y"));?>
                                <?//DETAIL_BUTTONS//
                                if($inBtnPayments || $inBtnCredit) {?>
                                    <div class="catalog-detail-buttons">
                                        <?if($inBtnPayments) {?>
                                            <a rel="nofollow" target="_blank" href="<?=!empty($arParams['BUTTON_PAYMENTS_HREF']) ? $arParams['BUTTON_PAYMENTS_HREF'] : 'javascript:void(0)'?>" class="btn_buy apuo pcd"><i class="fa fa-credit-card"></i><span><?=GetMessage('CATALOG_ELEMENT_BUTTON_PAYMENTS')?></span></a>
                                        <?}
                                        if($inBtnCredit) {?>
                                            <a rel="nofollow" target="_blank" href="<?=!empty($arParams['BUTTON_CREDIT_HREF']) ? $arParams['BUTTON_CREDIT_HREF'] : 'javascript:void(0)'?>" class="btn_buy apuo pcd"><i class="fa fa-percent"></i><span><?=GetMessage('CATALOG_ELEMENT_BUTTON_CREDIT')?></span></a>
                                        <?}?>
                                    </div>
                                <?}
                                //DETAIL_GEOLOCATION_DELIVERY//?>
                                <div id="<?=$arItemIDs['DELIVERY']?>"></div>
                                <?//DETAIL_BUTTONS//
                                if($inBtnDelivery) {?>
                                    <div class="catalog-detail-buttons">
                                        <a rel="nofollow" target="_blank" href="<?=!empty($arParams['BUTTON_DELIVERY_HREF']) ? $arParams['BUTTON_DELIVERY_HREF'] : 'javascript:void(0)'?>" class="btn_buy apuo pcd"><i class="fa fa-truck"></i><span><?=GetMessage('CATALOG_ELEMENT_BUTTON_DELIVERY')?></span></a>
                                    </div>
                                <?}?>
                            </div>
                        </div>
                    </div>
                    <?//OFFERS_DETAIL_PROPERTIES//?>
                    <div id="<?=$arItemIDs['PROPERTIES']?>">
                        <?$sPropOffers = false;
                        if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") {
                            foreach($arResult["OFFERS"] as $key => $arOffer) {
                                if(!empty($arOffer["DISPLAY_S_PROPERTIES"])) {
                                    $sPropOffers = true;
                                    break;
                                }
                            }
                        }
                        if(!empty($arResult["DISPLAY_PROPERTIES"]) || !empty($sPropOffers)) {?>
                            <div class="catalog-detail-properties">
                                <div class="h4"><?=GetMessage("CATALOG_ELEMENT_PROPERTIES")?></div>
                                <?//DETAIL_PROPERTIES//
                                if(!empty($arResult["DISPLAY_PROPERTIES"])) {
                                    foreach($arResult["DISPLAY_PROPERTIES"] as $k => $v) {?>
                                        <div class="catalog-detail-property">
                                            <div class="name"><?=$v["NAME"]?></div>
                                            <?if(!empty($v["FILTER_HINT"])) {?>
                                                <div class="hint-wrap">
                                                    <a class="hint" href="javascript:void(0);" onclick="showDetailPropertyFilterHint(this, '<?=$v['FILTER_HINT']?>');"><i class="fa fa-question-circle-o"></i></a>
                                                </div>
                                            <?}?>
                                            <div class="dots"></div>
                                            <div class="val"><?=is_array($v["DISPLAY_VALUE"]) ? implode(", ", $v["DISPLAY_VALUE"]) : $v["DISPLAY_VALUE"];?></div>
                                        </div>
                                    <?}
                                }
                                //OFFERS_PROPERTIES//
                                if(!empty($sPropOffers)) {
                                    foreach($arResult["OFFERS"] as $key => $arOffer) {?>
                                        <div id="offer-property_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" class="offer-property<?=($key == $arResult['OFFERS_SELECTED'] ? '' : ' hidden');?>">
                                            <?if(isset($arOffer["DISPLAY_S_PROPERTIES"]) && is_array($arOffer["DISPLAY_S_PROPERTIES"])) foreach($arOffer["DISPLAY_S_PROPERTIES"] as $k => $v) {?>
                                                <div class="catalog-detail-property">
                                                    <div class="name"><?=$v["NAME"]?></div>
                                                    <?if(!empty($v["FILTER_HINT"])) {?>
                                                        <div class="hint-wrap">
                                                            <a class="hint" href="javascript:void(0);" onclick="showDetailPropertyFilterHint(this, '<?=$v['FILTER_HINT']?>');"><i class="fa fa-question-circle-o"></i></a>
                                                        </div>
                                                    <?}?>
                                                    <div class="dots"></div>
                                                    <div class="val"><?=$v["VALUE"]?></div>
                                                </div>
                                            <?}?>
                                        </div>
                                    <?}
                                }?>
                            </div>
                        <?}?>
                    </div>
                </div>
            </div>
            <?//OFFERS_LIST//
            if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST") {?>
            <div id="catalog-detail-offers-list" class="catalog-detail-offers-list">
                <div class="h3"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST")?></div>
                <div class="offers-items">
                    <div class="thead">
                        <div class="offers-items-image"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST_IMAGE")?></div>
                        <div class="offers-items-name"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST_NAME")?></div>
                        <?$i = 1;
                        foreach($arResult["SKU_PROPS"] as $arProp) {
                            if(!isset($arResult["OFFERS_PROP"][$arProp["CODE"]]))
                                continue;
                            if($i > 3)
                                continue;?>
                            <div class="offers-items-prop"><?=htmlspecialcharsex($arProp["NAME"]);?></div>
                            <?$i++;
                        }?>
                        <div class="offers-items-price"></div>
                        <div class="offers-items-buy"><?=GetMessage("CATALOG_ELEMENT_OFFERS_LIST_PRICE")?></div>
                    </div>
                    <div class="tbody">
                        <?foreach($arResult["OFFERS"] as $keyOffer => $arOffer) {
                        $sticker = "";
                        if($arOffer["MIN_PRICE"]["PERCENT"] > 0) {
                            $sticker .= "<span class='discount'>-".$arOffer["MIN_PRICE"]["PERCENT"]."%</span>";
                        }
                        $isOfferPreviewImg = is_array($arOffer["PREVIEW_IMG"]);
                        $offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arResult["NAME"];?>
                        <div class="catalog-item" id="catalog-offer-item-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" data-offer-num="<?=$keyOffer?>" data-link="<?=$arOffer['ID']?>">
                            <div class="catalog-item-info">
                                <?//OFFERS_LIST_IMAGE//?>
                                <div class="catalog-item-image-cont">
                                    <div class="catalog-item-image">
                                        <?if($isOfferPreviewImg || $isPreviewImg) {?>
                                        <a rel="lightbox" class="fancybox" href="<?=($isOfferPreviewImg ? $arOffer['DETAIL_PICTURE']['SRC'] : $arResult['DETAIL_PICTURE']['SRC']);?>">
                                            <?} else {?>
                                            <div>
                                                <?}
                                                if($isOfferPreviewImg) {?>
                                                    <img class="data-lazy-src" data-lazy-src="<?=$arOffer['PREVIEW_IMG']['SRC']?>" width="<?=$arOffer['PREVIEW_IMG']['WIDTH']?>" height="<?=$arOffer['PREVIEW_IMG']['HEIGHT']?>" alt="<?=$offerName?>" title="<?=$offerName?>" />
                                                <?} elseif($isPreviewImg) {?>
                                                    <img class="data-lazy-src" data-lazy-src="<?=$arResult['PREVIEW_IMG']['SRC']?>" width="<?=$arResult['PREVIEW_IMG']['WIDTH']?>" height="<?=$arResult['PREVIEW_IMG']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
                                                <?} else {?>
                                                    <img class="data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="72" height="72" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
                                                <?}?>
                                                <div class="sticker">
                                                    <?=$sticker?>
                                                </div>
                                                <?if($isOfferPreviewImg || $isPreviewImg) {?>
                                                    <div class="zoom"><i class="fa fa-search-plus"></i></div>
                                                <?}?>
                                                <?=($isOfferPreviewImg || $isPreviewImg ? "</a>" : "</div>");?>
                                            </div>
                                    </div>
                                    <?//OFFERS_LIST_NAME_ARTNUMBER//?>
                                    <div class="catalog-item-title">
                                        <?//OFFERS_LIST_NAME//?>
                                        <span class="name"><?=$offerName?></span>
                                        <?//OFFERS_LIST_ARTNUMBER//?>
                                        <span class="article"><?=GetMessage("CATALOG_ELEMENT_ARTNUMBER")?><?=!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?></span>
                                    </div>
                                    <?//OFFERS_LIST_PROPS//
                                    $i = 1;
                                    foreach($arResult["SKU_PROPS"] as $arProp) {
                                        if(!isset($arResult["OFFERS_PROP"][$arProp["CODE"]]))
                                            continue;
                                        if($i > 3)
                                            continue;?>
                                        <div class="catalog-item-prop<?=(!isset($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) || empty($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) ? ' empty' : '');?>">
                                            <?if(isset($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) && !empty($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]])) {
                                                $v = $arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]];
                                                if($arProp["SHOW_MODE"] == "TEXT") {
                                                    echo strip_tags($v["DISPLAY_VALUE"]);
                                                } elseif($arProp["SHOW_MODE"] == "PICT") {?>
                                                    <span class="prop_cont">
													<span class="prop" title="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>">
														<?if(is_array($arProp["VALUES"][$v["VALUE"]]["PICT"])) {?>
                                                            <img class="data-lazy-src" data-lazy-src="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['SRC']?>" width="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['WIDTH']?>" height="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['HEIGHT']?>" alt="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>" title="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>" />
                                                        <?} else {?>
                                                            <i style="background:#<?=$arProp['VALUES'][$v['VALUE']]['HEX']?>"></i>
                                                        <?}?>
													</span>
												</span>
                                                <?}
                                            }?>
                                        </div>
                                        <?$i++;
                                    }
                                    unset($arProp);
                                    //OFFERS_LIST_PRICE//?>
                                    <div class="item-price">
                                        <?$arCurFormat = CCurrencyLang::GetCurrencyFormat($arOffer["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
                                        if(empty($arCurFormat["THOUSANDS_SEP"])) {
                                            $arCurFormat["THOUSANDS_SEP"] = " ";
                                        }
                                        $arCurFormat["REFERENCE_DECIMALS"] = $arCurFormat["DECIMALS"];
                                        if($arCurFormat["HIDE_ZERO"] == "Y") {
                                            if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {
                                                if(round($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arCurFormat["DECIMALS"]) == round($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)) {
                                                    $arCurFormat["REFERENCE_DECIMALS"] = 0;
                                                }
                                            }
                                            if(round($arOffer["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"]) == round($arOffer["MIN_PRICE"]["RATIO_PRICE"], 0)) {
                                                $arCurFormat["DECIMALS"] = 0;
                                            }
                                        }
                                        $currency = str_replace("# ", " ", $arCurFormat["FORMAT_STRING"]);

                                        if($arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
                                            <span class="catalog-item-no-price">
											<span class="unit">
												<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>
												<br />
												<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["CATALOG_MEASURE_RATIO"]." ".$arOffer["CATALOG_MEASURE_NAME"];?></span>
											</span>
										</span>
                                        <?} else {?>
                                            <span class="catalog-item-price">
											<?if(count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
                                                <span class="from"><?=GetMessage("CATALOG_ELEMENT_FROM")?></span>
                                            <?}
                                            echo number_format($arOffer["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);
                                            if($arParams["USE_PRICE_COUNT"] && count($arOffer["ITEM_QUANTITY_RANGES"]) > 1) {?>
                                                <span class="catalog-item-price-ranges-wrap">
													<a class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
												</span>
                                            <?}?>
                                                <?if(count($arOffer["PRICE_MATRIX_SHOW"]["COLS"]) > 1 && count($arOffer["ITEM_QUANTITY_RANGES"]) <= 1) {?>
                                                    <span class="catalog-item-price-ranges-wrap">
													<a class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
												</span>
                                                <?}?>
											<span class="unit">
												<?=$currency?>
												<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".$arOffer["CATALOG_MEASURE_RATIO"]." ".$arOffer["CATALOG_MEASURE_NAME"];?></span>
											</span>
											<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
                                                <span class="catalog-item-price-reference">
													<?=number_format($arOffer["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arCurFormat["REFERENCE_DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>
													<span><?=$currency?></span>
												</span>
                                            <?}?>
										</span>
                                            <?if($arOffer["MIN_PRICE"]["RATIO_PRICE"] < $arOffer["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
                                                <span class="catalog-item-price-old">
												<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
											</span>
                                                <span class="catalog-item-price-percent">
												<?=GetMessage('CATALOG_ELEMENT_SKIDKA')?>
												<br />
												<?=$arOffer["MIN_PRICE"]["PRINT_RATIO_DISCOUNT"]?>
											</span>
                                            <?}
                                        }?>
                                    </div>
                                    <?//OFFERS_LIST_MOBILE_PROPS//
                                    if(!empty($arOffer["DISPLAY_PROPERTIES"])) {?>
                                        <div id="catalog-item-props-mob-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" class="catalog-item-props-mob"></div>
                                    <?}
                                    //OFFERS_LIST_AVAILABILITY_BUY//?>
                                    <div class="buy_more<?=(!$inBtnBoc) ? " no-one-click" : ""?>">
                                        <?//OFFERS_LIST_AVAILABILITY//?>
                                        <div class="available" 444>
                                            <?if($arOffer["CAN_BUY"]) {?>
                                                <div class="avl">
                                                    <i class="fa fa-check-circle"></i>
                                                    <span>
													<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
                                                    if($arOffer["CHECK_QUANTITY"] && $inProductQnt)
                                                        echo " ".$arOffer["CATALOG_QUANTITY"];?>
												</span>
                                                </div>
                                            <?} elseif(!$arOffer["CAN_BUY"]) {?>
                                                <div class="not_avl">
                                                    <i class="fa fa-times-circle"></i>
                                                    <span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
                                                </div>
                                            <?}?>
                                        </div>
                                        <div class="clr"></div>
                                        <?//OFFERS_LIST_BUY//
                                        if($arOffer["CAN_BUY"]) {
                                            if($arOffer["MIN_PRICE"]["RATIO_PRICE"] <= 0) {
                                                //OFFERS_LIST_ASK_PRICE//?>
                                                <form action="javascript:void(0)" class="apuo_form">
                                                    <input type="hidden" name="ACTION" value="ask_price" />
                                                    <?$properties = array();
                                                    foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
                                                        if($propOffer["PROPERTY_TYPE"] != "S")
                                                            $properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
                                                    }
                                                    $properties = implode("; ", $properties);
                                                    $elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;?>
                                                    <input type="hidden" name="NAME" value="<?=$elementName?>" />
                                                    <button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-comment-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT")?></span></button>
                                                </form>
                                            <?} else {
                                                $props = array();
                                                if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
                                                    $props[] = array(
                                                        "NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
                                                        "CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
                                                        "VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
                                                    );
                                                }
                                                foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
                                                    if($propOffer["PROPERTY_TYPE"] != "S") {
                                                        $props[] = array(
                                                            "NAME" => $propOffer["NAME"],
                                                            "CODE" => $propOffer["CODE"],
                                                            "VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
                                                        );
                                                    }
                                                }
                                                $props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
                                                <div class="add2basket_block">
                                                    <?//OFFERS_LIST_DELAY//?>
                                                    <div class="delay">
                                                        <a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arOffer["ID"]?>', 'quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs["ID"]."-".$arOffer["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
                                                    </div>
                                                    <?//OFFERS_LIST_BUY_FORM//?>
                                                    <form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
                                                        <div class="qnt_cont">
                                                            <a href="javascript:void(0)" class="minus"><span>-</span></a>
                                                            <input type="text" id="quantity_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['MIN_PRICE']['MIN_QUANTITY']?>" />
                                                            <a href="javascript:void(0)" class="plus"><span>+</span></a>
                                                        </div>
                                                        <input type="hidden" name="ID" class="offer_id" value="<?=$arOffer['ID']?>" />
                                                        <input type="hidden" name="PROPS" id="props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="<?=$props?>" />
                                                        <?if(!empty($arResult["SELECT_PROPS"])) {?>
                                                            <input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="" />
                                                        <?}?>
                                                        <button type="button" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i></button>
                                                    </form>
                                                    <?//OFFERS_LIST_BUY_ONE_CLICK//?>
                                                    <?if($inBtnBoc){?>
                                                        <button onclick="ym(22745254,'reachGoal','byoneclick'); ga('send', 'event', 'byoneclick', 'click');" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy boc_anch" data-action="boc"><i class="fa fa-bolt"></i><span><?=GetMessage("CATALOG_ELEMENT_BOC_SHORT")?></span></button>
                                                    <?}?>
                                                </div>
                                            <?}
                                        } elseif(!$arOffer["CAN_BUY"]) {
                                            //OFFERS_LIST_UNDER_ORDER//?>
                                            <form action="javascript:void(0)" class="apuo_form">
                                                <input type="hidden" name="ACTION" value="under_order" />
                                                <?$properties = array();
                                                foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
                                                    if($propOffer["PROPERTY_TYPE"] != "S")
                                                        $properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
                                                }
                                                $properties = implode("; ", $properties);
                                                $elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;?>
                                                <input type="hidden" name="NAME" value="<?=$elementName?>" />
                                                <button type="button" id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo"><i class="fa fa-clock-o"></i><span class="short"><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER_SHORT")?></span></button>
                                            </form>
                                        <?}?>
                                    </div>
                                </div>
                            </div>
                            <?}?>
                        </div>
                    </div>
                </div>
                <?}
                //DETAIL_KIT_ITEMS//
                if(count($arResult["KIT_ITEMS"]) > 0) {?>
                    <div class="kit-items">
                        <div class="h3"><?=GetMessage("CATALOG_ELEMENT_KIT_ITEMS")?></div>
                        <div class="catalog-item-cards">
                            <?foreach($arResult["KIT_ITEMS"] as $key => $arItem) {?>
                                <div class="catalog-item-card">
                                    <div class="catalog-item-info">
                                        <?//KIT_ITEM_IMAGE//?>
                                        <div class="item-image-cont">
                                            <div class="item-image">
                                                <?if(is_array($arItem["PREVIEW_PICTURE"])) {?>
                                                    <a rel="nofollow" href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                                        <img class="item_img data-lazy-src" data-lazy-src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" width="<?=$arItem['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arItem['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" />
                                                    </a>
                                                <?} else {?>
                                                    <a href="<?=$arItem['DETAIL_PAGE_URL']?>">
                                                        <img class="item_img data-lazy-src" data-lazy-src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arItem['NAME']?>" title="<?=$arItem['NAME']?>" />
                                                    </a>
                                                <?}?>
                                            </div>
                                        </div>
                                        <?//KIT_ITEM_TITLE//?>
                                        <div class="item-all-title">
                                            <a class="item-title" href="<?=$arItem['DETAIL_PAGE_URL']?>" title="<?=$arItem['NAME']?>">
                                                <?=$arItem["NAME"]?>
                                            </a>
                                        </div>
                                        <?//KIT_ITEM_PRICE//?>
                                        <div class="item-price-cont<?=(!$inOldPrice ? ' one' : '').($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? ' reference' : '');?>">
                                            <?$price = CCurrencyLang::GetCurrencyFormat($arItem["PRICE_CURRENCY"], LANGUAGE_ID);
                                            if(empty($price["THOUSANDS_SEP"])) {
                                                $price["THOUSANDS_SEP"] = " ";
                                            }
                                            if($price["HIDE_ZERO"] == "Y") {
                                                if(round($arItem["PRICE_DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arItem["PRICE_DISCOUNT_VALUE"], 0)) {
                                                    $price["DECIMALS"] = 0;
                                                }
                                            }
                                            $currency = str_replace("# ", " ", $price["FORMAT_STRING"]);?>

                                            <div class="item-price">
                                                <?if($inOldPrice) {
                                                    if($arItem["PRICE_DISCOUNT_VALUE"] < $arItem["PRICE_VALUE"]) {?>
                                                        <span class="catalog-item-price-old">
												<?=CCurrencyLang::CurrencyFormat($arItem["PRICE_VALUE"] * $arItem["BASKET_QUANTITY"], $arItem["PRICE_CURRENCY"], true);?>
											</span>
                                                    <?}
                                                }?>
                                                <span class="catalog-item-price">
										<?=number_format($arItem["PRICE_DISCOUNT_VALUE"] * $arItem["BASKET_QUANTITY"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
										<span class="unit">
											<?=$currency?>
											<span><?=GetMessage("CATALOG_ELEMENT_UNIT")." ".($arItem["BASKET_QUANTITY"] > 0 && $arItem["BASKET_QUANTITY"] != 1 ? $arItem["BASKET_QUANTITY"]." " : "").$arItem["MEASURE"]["SYMBOL_RUS"];?></span>
										</span>
									</span>
                                                <?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
                                                    <span class="catalog-item-price-reference">
											<?=CCurrencyLang::CurrencyFormat($arItem["PRICE_DISCOUNT_VALUE"] * $arItem["BASKET_QUANTITY"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arItem["PRICE_CURRENCY"], true);?>
										</span>
                                                <?}?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?}?>
                        </div>
                        <div class="clr"></div>
                    </div>
                <?}
                //DETAIL_CONSTRUCTOR//?>
                <div id="<?=$arItemIDs['CONSTRUCTOR']?>"></div>
                <?//DETAIL_TABS//?>
                <div class="tabs-wrap tabs-catalog-detail">
                    <ul class="tabs">
                        <?$i = 1;?>
                        <li class="tabs__tab current">
                            <a href="#tab<?=$i?>"><span><?=GetMessage("CATALOG_ELEMENT_FULL_DESCRIPTION")?></span></a>
                        </li>
                        <?$i++;
                        if(!empty($arResult["PROPERTIES"]["FREE_TAB"]["VALUE"])) {?>
                            <li class="tabs__tab">
                                <a href="#tab<?=$i?>"><span><?=$arResult["PROPERTIES"]["FREE_TAB"]["NAME"]?></span></a>
                            </li>
                            <?$i++;
                        }
                        if(!empty($arResult["PROPERTIES"]["DOWNLOADS"]["VALUE"])) {?>
                            <li class="tabs__tab">
                                <a href="#tab<?=$i?>"><span><?=$arResult["PROPERTIES"]["DOWNLOADS"]["NAME"]?></span></a>
                            </li>
                            <?$i++;
                        }
                        if(!empty($arResult["PROPERTIES"]["ACCESSORIES"]["VALUE"])) {?>
                            <li class="tabs__tab">
                                <a href="#tab<?=$i?>"><span><?=$arResult["PROPERTIES"]["ACCESSORIES"]["NAME"]?></span></a>
                            </li>
                            <?$i++;
                        }
                        if(!empty($arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"])) {?>
                            <li class="tabs__tab">
                                <a href="#tab<?=$i?>"><span><?=$arResult["PROPERTIES"]["FILES_DOCS"]["NAME"]?></span></a>
                            </li>
                            <?$i++;
                        }?>
                        <li class="tabs__tab">
                            <a href="#tab<?=$i?>"><span><?=GetMessage("CATALOG_ELEMENT_REVIEWS")?> <span class="reviews_count"></span></span></a>
                        </li>
                        <?$i++;
                        if($arParams["USE_STORE"] == "Y" && ((isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") || (!isset($arResult["OFFERS"]) || empty($arResult["OFFERS"])))) {?>
                            <li class="tabs__tab">
                                <a href="#tab<?=$i?>"><span><?=GetMessage("CATALOG_ELEMENT_SHOPS")?></span></a>
                            </li>
                        <?}?>
                    </ul>
                    <?//DETAIL_TEXT_TAB//?>
                    <div class="tabs__box" style="display:block;">
                        <div class="tabs__box-content">
                            <?=$arResult["DETAIL_TEXT"];?>
                        </div>
                    </div>
                    <?//FREE_TAB//
                    if(!empty($arResult["PROPERTIES"]["FREE_TAB"]["VALUE"])) {?>
                        <div class="tabs__box">
                            <div class="tabs__box-content">
                                <?=$arResult["PROPERTIES"]["FREE_TAB"]["~VALUE"]["TEXT"];?>
                            </div>
                        </div>
                    <?}
                    //DOWNLOAD//
                    if(!empty($arResult["PROPERTIES"]["DOWNLOADS"]["VALUE"])) {?>
                        <div class="tabs__box">
                            <div class="tabs__box-content">
                                <?=$arResult["PROPERTIES"]["DOWNLOADS"]["~VALUE"]["TEXT"];?>
                            </div>
                        </div>
                    <?}
                    //ACCESSORIES_TAB//
                    if(!empty($arResult["PROPERTIES"]["ACCESSORIES"]["VALUE"])) {?>
                        <div class="tabs__box" id="accessories-to"></div>
                    <?}
                    //FILES_DOCS_TAB//
                    if(!empty($arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"])) {?>
                        <div class="tabs__box">
                            <div class="catalog-detail-files-docs"><!--
				---><?foreach($arResult["PROPERTIES"]["FILES_DOCS"]["FULL_VALUE"] as $key => $arDoc) {?><!--
					---><div class="files-docs-item-cont">
                                    <a class="files-docs-item" href="<?=$arDoc['SRC']?>" target="_blank">
                                        <div class="files-docs-icon">
                                            <?if($arDoc["TYPE"] == "doc" || $arDoc["TYPE"] == "docx" || $arDoc["TYPE"] == "rtf") {?>
                                                <i class="fa fa-file-word-o"></i>
                                            <?} elseif($arDoc["TYPE"] == "xls" || $arDoc["TYPE"] == "xlsx") {?>
                                                <i class="fa fa-file-excel-o"></i>
                                            <?} elseif($arDoc["TYPE"] == "pdf") {?>
                                                <i class="fa fa-file-pdf-o"></i>
                                            <?} elseif($arDoc["TYPE"] == "rar" || $arDoc["TYPE"] == "zip" || $arDoc["TYPE"] == "gzip") {?>
                                                <i class="fa fa-file-archive-o"></i>
                                            <?} elseif($arDoc["TYPE"] == "jpg" || $arDoc["TYPE"] == "jpeg" || $arDoc["TYPE"] == "png" || $arDoc["TYPE"] == "gif") {?>
                                                <i class="fa fa-file-image-o"></i>
                                            <?} elseif($arDoc["TYPE"] == "ppt" || $arDoc["TYPE"] == "pptx") {?>
                                                <i class="fa fa-file-powerpoint-o"></i>
                                            <?} elseif($arDoc["TYPE"] == "txt") {?>
                                                <i class="fa fa-file-text-o"></i>
                                            <?} else {?>
                                                <i class="fa fa-file-o"></i>
                                            <?}?>
                                        </div>
                                        <div class="files-docs-block">
                                            <span class="files-docs-name"><?=!empty($arDoc["DESCRIPTION"]) ? $arDoc["DESCRIPTION"] : $arDoc["NAME"]?></span>
                                            <span class="files-docs-size"><?=GetMessage("CATALOG_ELEMENT_SIZE").$arDoc["SIZE"]?></span>
                                        </div>
                                    </a>
                                </div><!--
				---><?}?><!--
			---></div>
                        </div>
                    <?}
                    //REVIEWS_TAB//?>
                    <div class="tabs__box" id="catalog-reviews" <?/*Стандартный вывод отзывов шаблона битрикс id="catalog-reviews-to"*/?>>
                        <?#Вывод отзовов через модуль cracle?>
                        <?$APPLICATION-> IncludeComponent( "cackle.reviews", ".default", array( "CHANNEL_ID" => $arResult["ID"] ), false);?>
                    </div>
                    <?//STORES_TAB//
                    if($arParams["USE_STORE"] == "Y" && ((isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST") || (!isset($arResult["OFFERS"]) || empty($arResult["OFFERS"])))) {?>
                        <div class="tabs__box">
                            <div id="<?=$arItemIDs['STORE'];?>"></div>
                        </div>
                    <?}?>
                </div>
                <div class="clr"></div>
            </div>

            <?if(isset($arResult["OFFERS"]) && !empty($arResult["OFFERS"])) {
                $arJSParams = array(
                    "CONFIG" => array(
                        "USE_CATALOG" => $arResult["CATALOG"],
                        "USE_SUBSCRIBE" => $arResult["CATALOG_SUBSCRIBE"],
                        "USE_CAPTCHA" => $arResult["USE_CAPTCHA"],
                        "USE_STORE" => $arParams["USE_STORE"],
                        "REFERENCE_PRICE_COEF" => $arSetting["REFERENCE_PRICE_COEF"]["VALUE"],
                        "USE_GEOLOCATION" => $arSetting["USE_GEOLOCATION"]["VALUE"],
                        "GEOLOCATION_DELIVERY" => $arSetting["GEOLOCATION_DELIVERY"]["VALUE"],
                    ),
                    "PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
                    "VISUAL" => array(
                        "ID" => $arItemIDs["ID"],
                        "PICT_ID" => $arItemIDs["PICT"],
                        "PRICE_ID" => $arItemIDs["PRICE"],
                        "BUY_ID" => $arItemIDs["BUY"],
                        "SUBSCRIBE_ID" => $arItemIDs["SUBSCRIBE"],
                        "DELAY_ID" => $arItemIDs["DELAY"],
                        "DELIVERY_ID" => $arItemIDs["DELIVERY"],
                        "ARTICLE_ID" => $arItemIDs["ARTICLE"],
                        "PROPERTIES_ID" => $arItemIDs["PROPERTIES"],
                        "CONSTRUCTOR_ID" => $arItemIDs["CONSTRUCTOR"],
                        "STORE_ID" => $arItemIDs["STORE"],
                        "TREE_ID" => $arItemIDs["PROP_DIV"],
                        "TREE_ITEM_ID" => $arItemIDs["PROP"],
                        "POPUP_BTN_ID" => $arItemIDs["POPUP_BTN"],
                        "PRICE_MATRIX_BTN_ID" => is_array($arResult["ID_PRICE_MATRIX_BTN"]) ? $arResult["ID_PRICE_MATRIX_BTN"] : ""
                    ),
                    "PRODUCT" => array(
                        "ID" => $arResult["ID"],
                        "NAME" => $arResult["~NAME"],
                        "PICT" => is_array($arResult["PREVIEW_IMG"]) ? $arResult["PREVIEW_IMG"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150)
                    ),
                    "OFFERS_VIEW" => $arSetting["OFFERS_VIEW"]["VALUE"],
                    "OFFERS_LINK_SHOW" => $inOffersLinkShow,
                    "OFFERS" => $arResult["JS_OFFERS"],
                    "OFFER_SELECTED" => $arResult["OFFERS_SELECTED"],
                    "TREE_PROPS" => $arSkuProps
                );
            } else {
                $arJSParams = array(
                    "CONFIG" => array(
                        "USE_CATALOG" => $arResult["CATALOG"],
                        "REFERENCE_PRICE_COEF" => $arSetting["REFERENCE_PRICE_COEF"]["VALUE"],
                        "USE_GEOLOCATION" => $arSetting["USE_GEOLOCATION"]["VALUE"],
                        "GEOLOCATION_DELIVERY" => $arSetting["GEOLOCATION_DELIVERY"]["VALUE"],
                    ),
                    "PRODUCT_TYPE" => $arResult["CATALOG_TYPE"],
                    "VISUAL" => array(
                        "ID" => $arItemIDs["ID"],
                        "POPUP_BTN_ID" => $arItemIDs["POPUP_BTN"],
                        "BTN_BUY_ID" => $arItemIDs["BTN_BUY"],
                        "PRICE_MATRIX_BTN_ID" => $arIdPriceMatrix
                    ),
                    "PRODUCT" => array(
                        "ID" => $arResult["ID"],
                        "NAME" => $arResult["~NAME"],
                        "PICT" => is_array($arResult["PREVIEW_IMG"]) ? $arResult["PREVIEW_IMG"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
                        "ITEM_PRICE_MODE" => $arResult["ITEM_PRICE_MODE"],
                        "ITEM_PRICES" => $arResult["ITEM_PRICES"],
                        "ITEM_PRICE_SELECTED" => $arResult["ITEM_PRICE_SELECTED"],
                        "ITEM_QUANTITY_RANGES" => $arResult["ITEM_QUANTITY_RANGES"],
                        "ITEM_QUANTITY_RANGE_SELECTED" => $arResult["ITEM_QUANTITY_RANGE_SELECTED"],
                        "CHECK_QUANTITY" => $arResult["CHECK_QUANTITY"],
                        "QUANTITY_FLOAT" => is_double($arResult["CATALOG_MEASURE_RATIO"]),
                        "MAX_QUANTITY" => $arResult["CATALOG_QUANTITY"],
                        "STEP_QUANTITY" => $arResult["CATALOG_MEASURE_RATIO"],
                        "PRICE_MATRIX" => $arResult["PRICE_MATRIX_SHOW"]["MATRIX"]
                    )
                );
            }

            if(isset($arResult["SELECT_PROPS"]) && !empty($arResult["SELECT_PROPS"])) {
                $arJSParams["VISUAL"]["SELECT_PROP_ID"] = $arItemIDs["SELECT_PROP_DIV"];
                $arJSParams["VISUAL"]["SELECT_PROP_ITEM_ID"] = $arItemIDs["SELECT_PROP"];
                $arJSParams["SELECT_PROPS"] = $arSelProps;
            }?>

            <script type="text/javascript">
                BX.message({
                    DETAIL_ELEMENT_SKIDKA: "<?=GetMessageJS('CATALOG_ELEMENT_SKIDKA')?>",
                    DETAIL_ELEMENT_FROM: "<?=GetMessageJS('CATALOG_ELEMENT_FROM')?>",
                    DETAIL_ADDITEMINCART_ADDED: "<?=GetMessageJS('CATALOG_ELEMENT_ADDED')?>",
                    DETAIL_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_TITLE')?>",
                    DETAIL_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
                    DETAIL_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
                    DETAIL_SITE_ID: "<?=SITE_ID;?>",
                    DETAIL_SITE_DIR: "<?=SITE_DIR?>",
                    DETAIL_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
                    DETAIL_COMPONENT_PARAMS: "<?=CUtil::PhpToJSObject($arParams, false, true)?>",
                    SETTING_PRODUCT: "<?=CUtil::PhpToJSObject($arSetting, false, true)?>"
                });
                var <?=$strObName;?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);

                //SHOW_DETAIL_PROPERTY_FILTER_HINT//
                if(!window.showDetailPropertyFilterHint) {
                    function showDetailPropertyFilterHint(target, hint) {
                        BX.DetailPropertyFilterHint = {
                            popup: null
                        };
                        BX.DetailPropertyFilterHint.popup = BX.PopupWindowManager.create("detailPropertyFilterHint", null, {
                            autoHide: true,
                            offsetLeft: 0,
                            offsetTop: 0,
                            draggable: false,
                            closeByEsc: false,
                            className: "pop-up filter-hint",
                            closeIcon: { right : "-10px", top : "-10px"},
                            titleBar: false
                        });
                        BX.DetailPropertyFilterHint.popup.setContent(hint);

                        var close = BX.findChild(BX("detailPropertyFilterHint"), {className: "popup-window-close-icon"}, true, false);
                        if(!!close)
                            close.innerHTML = "<i class='fa fa-times'></i>";

                        target.parentNode.appendChild(BX("detailPropertyFilterHint"));

                        BX.DetailPropertyFilterHint.popup.show();
                    }
                }

                /*vp*/
                jQuery(".check-b").prop('checked',false);
                jQuery('.catalog-detail-buy .add2basket_form').prepend('<input type="hidden" name="IDS"  id="Ids" value="">');
                jQuery(".final-prod.check-b").change(function() {
                    if(jQuery(this).is(":checked")) {
                        jQuery(this).parents('.box-more').eq(0).find('.real-prod .check-b').click();
                    }else{
                        jQuery(this).parents('.box-more').eq(0).find('.real-prod .check-b').click();
                    }
                })
                jQuery(".real-prod .check-b").change(function() {
                    if(jQuery(this).is(":checked")) {
                        jQuery('#Origin').attr("value", parseInt(jQuery('#Origin').attr("value")) + parseInt(jQuery(this).attr('o-price')));
                        str = jQuery('#Origin').attr("value").toString();
                        str = str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
                        jQuery('.catalog-detail-item-price-current').html(str+"<?=GetMessage('CATALOG_RUR');?>");
                        jQuery('#Ids').val(jQuery('#Ids').val()+","+jQuery(this).attr('ids'));
                    }else{
                        jQuery('#Origin').attr("value", parseInt(jQuery('#Origin').attr("value")) - parseInt(jQuery(this).attr('o-price')));
                        str = jQuery('#Origin').attr("value").toString();
                        str = str.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
                        jQuery('.catalog-detail-item-price-current').html(str+"<?=GetMessage('CATALOG_RUR');?>");
                        jQuery('#Ids').val(jQuery('#Ids').val().replace(","+jQuery(this).attr('ids'), ''));
                    }
                });
                /*end vp*/

            </script>
            <script>
                gtag('event', 'view_item', {
                    'send_to': 'AW-977520268',
                    'value': <?=$arResult["MIN_PRICE"]["PRICE"]?>,
                    'items': [{
                        'id': '<?=$arResult["ID"]?>',
                        'google_business_vertical': 'retail'
                    }]
                });
                function add2cart_gtag_el(){
                    console.log('add2cart_gtag_el');
                    gtag('event', 'add_to_cart', {
                        'send_to': 'AW-977520268',
                        'value': <?=$arResult["MIN_PRICE"]["PRICE"]?>,
                        'items': [{
                            'id': '<?=$arResult["ID"]?>',
                            'google_business_vertical': 'retail'
                        }]
                    });
                }
            </script>