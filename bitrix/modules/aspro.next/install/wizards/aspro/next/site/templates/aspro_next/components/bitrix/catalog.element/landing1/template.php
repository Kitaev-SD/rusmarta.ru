<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);
use \Bitrix\Main\Localization\Loc;
?>

<?// shema.org meta for itemtype="http://schema.org/NewsArticle"?>
<meta itemprop="headline" content="<?=$name = strip_tags(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['NAME'])?>" />
<meta itemprop="datePublished" content="<?=ConvertDateTime($arResult['ACTIVE_FROM'] ? $arResult['ACTIVE_FROM'] : $arResult['DATE_CREATE'], 'YYYY-MM-DD')?>" />
<meta itemprop="image" content="<?=($arResult['PREVIEW_PICTURE'] ? $arResult['PREVIEW_PICTURE']['SRC'] : ($arResult['DETAIL_PICTURE'] ? $arResult['DETAIL_PICTURE']['SRC'] : false))?>" />
<meta itemprop="description" content="<?=(strlen(strip_tags($arResult['PREVIEW_TEXT'])) ? strip_tags($arResult['PREVIEW_TEXT']) : (strlen(strip_tags($arResult['DETAIL_TEXT'])) ? strip_tags($arResult['DETAIL_TEXT']) : $name))?>" />
<span itemprop="author" itemscope itemtype="https://schema.org/Organization">
	<?
	$logo = CNext::GetFrontParametrValue('LOGO_IMAGE');
	$logoSvg = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'logo.svg');
	$logoPng = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].SITE_DIR.'logo.png');
	?>
	<meta itemprop="name" content="<?=htmlspecialcharsbx($GLOBALS['arSite']['SITE_NAME'])?>" />
	<meta itemprop="logo" content="<?=($logo ? $logo : (file_exists($logoSvg) ? $logoSvg : $logoPng))?>" />
</span>
<span itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
	<meta itemprop="name" content="<?=htmlspecialcharsbx($GLOBALS['arSite']['SITE_NAME'])?>" />
	<meta itemprop="logo" content="<?=($logo ? $logo : (file_exists($logoSvg) ? $logoSvg : $logoPng))?>" />
</span>

<?// shot top banners start?>
<?$bShowTopBanner = (isset($arResult['SECTION_BNR_CONTENT'] ) && $arResult['SECTION_BNR_CONTENT'] == true);?>
<?if($bShowTopBanner):?>
	<?$this->SetViewTarget("section_bnr_content");?>
		<?CNext::ShowTopDetailBanner($arResult, $arParams);?>
	<?$this->EndViewTarget();?>
<?endif;?>
<?// shot top banners end?>


<?if($arResult["PROPERTIES"]["H3_GOODS"]["VALUE"]):?>
	<?$this->SetViewTarget("langing_title");?>
		<hr>
		<h3 class="title_block langing_title_block"><?=$arResult["PROPERTIES"]["H3_GOODS"]["VALUE"];?></h3>
	<?$this->EndViewTarget();?>
<?endif;?>

<?if($arResult['PROPERTIES']['TIZERS']['VALUE']):?>
	<?$GLOBALS["arLandingTizers"] = array("ID" => $arResult['PROPERTIES']['TIZERS']['VALUE']);?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"next",
		array(
			"IBLOCK_TYPE" => "aspro_next_content",
			"IBLOCK_ID" => CNextCache::$arIBlocks[SITE_ID]["aspro_next_content"]["aspro_next_tizers"][0],
			"NEWS_COUNT" => "4",
			"SORT_BY1" => "SORT",
			"SORT_ORDER1" => "ASC",
			"SORT_BY2" => "ID",
			"SORT_ORDER2" => "DESC",
			"FILTER_NAME" => "arLandingTizers",
			"FIELD_CODE" => array(
				0 => "",
				1 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "LINK",
				1 => "",
			),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" =>$arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "N",
			"PREVIEW_TRUNCATE_LEN" => "",
			"ACTIVE_DATE_FORMAT" => "j F Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"PAGER_TEMPLATE" => "",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"COMPONENT_TEMPLATE" => "next",
			"SET_BROWSER_TITLE" => "N",
			"SET_META_KEYWORDS" => "N",
			"SET_META_DESCRIPTION" => "N",
			"SET_LAST_MODIFIED" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"SHOW_404" => "N",
			"MESSAGE_404" => ""
		),
		false, array("HIDE_ICONS" => "Y")
	);?>
<?endif;?>

<div class="row">
	<?$bShowFormQuestion = ($arResult['PROPERTIES']['FORM_QUESTION']['VALUE_XML_ID'] == 'YES');?>
	<?if($arResult["PREVIEW_TEXT"]):?>
		<div class="col-md-<?=($bShowFormQuestion ? 9 : 12)?>">
			<?=$arResult["PREVIEW_TEXT"];?>
		</div>
	<?endif;?>
	<?if($bShowFormQuestion):?>
		<div class="col-md-<?=($arResult["PREVIEW_TEXT"] ? 3 : '3 pull-left')?>">
			<div class="ask_a_question">
				<div class="inner">
					<div class="text-block">
						<?$APPLICATION->IncludeComponent(
							 'bitrix:main.include',
							 '',
							 Array(
								  'AREA_FILE_SHOW' => 'page',
								  'AREA_FILE_SUFFIX' => 'ask',
								  'EDIT_TEMPLATE' => ''
							 )
						);?>
					</div>
				</div>
				<div class="outer">
					<span><span class="btn btn-default btn-lg white transparent animate-load" data-event="jqm" data-param-form_id="ASK" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : Loc::getMessage('S_ASK_QUESTION'))?></span></span></span>
				</div>
			</div>
		</div>
	<?endif;?>
</div>

<?if($arResult["DETAIL_TEXT"]):?>
	<?$this->SetViewTarget("langing_detail_text");?>
		<div class="landing_detail">
			<?=$arResult["DETAIL_TEXT"];?>
		</div>
	<?$this->EndViewTarget();?>
<?endif;?>

<?if($arParams['SHOW_LANDINGS'] !== 'N' && $arResult["PROPERTIES"]["SECTION"]["VALUE"]):?>
	<?$GLOBALS["arLandingSections"] = array("PROPERTY_SECTION" => $arResult["PROPERTIES"]["SECTION"]["VALUE"], "!ID" => $arResult["ID"]);?>
	<?$this->SetViewTarget("langing_sections");?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"landings_list",
		array(
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"NEWS_COUNT" => "999",
			"SHOW_COUNT" => $arParams["LANDING_SECTION_COUNT"],
			"SORT_BY1" => "SORT",
			"SORT_ORDER1" => "ASC",
			"SORT_BY2" => "ID",
			"SORT_ORDER2" => "DESC",
			"FILTER_NAME" => "arLandingSections",
			"FIELD_CODE" => array(
				0 => "",
				1 => "",
			),
			"PROPERTY_CODE" => array(
				0 => "LINK",
				1 => "",
			),
			"CHECK_DATES" => "Y",
			"DETAIL_URL" => "",
			"AJAX_MODE" => "N",
			"AJAX_OPTION_JUMP" => "N",
			"AJAX_OPTION_STYLE" => "Y",
			"AJAX_OPTION_HISTORY" => "N",
			"CACHE_TYPE" =>$arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_FILTER" => "Y",
			"CACHE_GROUPS" => "N",
			"PREVIEW_TRUNCATE_LEN" => "",
			"ACTIVE_DATE_FORMAT" => "j F Y",
			"SET_TITLE" => "N",
			"SET_STATUS_404" => "N",
			"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
			"ADD_SECTIONS_CHAIN" => "N",
			"HIDE_LINK_WHEN_NO_DETAIL" => "N",
			"PARENT_SECTION" => "",
			"PARENT_SECTION_CODE" => "",
			"INCLUDE_SUBSECTIONS" => "Y",
			"PAGER_TEMPLATE" => "",
			"DISPLAY_TOP_PAGER" => "N",
			"DISPLAY_BOTTOM_PAGER" => "N",
			"PAGER_TITLE" => "",
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_DESC_NUMBERING" => "N",
			"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
			"PAGER_SHOW_ALL" => "N",
			"AJAX_OPTION_ADDITIONAL" => "",
			"COMPONENT_TEMPLATE" => "next",
			"SET_BROWSER_TITLE" => "N",
			"SET_META_KEYWORDS" => "N",
			"SET_META_DESCRIPTION" => "N",
			"SET_LAST_MODIFIED" => "N",
			"PAGER_BASE_LINK_ENABLE" => "N",
			"TITLE_BLOCK" => $arParams["LANDING_TITLE"],
			"SHOW_404" => "N",
			"MESSAGE_404" => ""
		),
		false, array("HIDE_ICONS" => "Y")
	);?>
	<?$this->EndViewTarget();?>
<?endif;?>