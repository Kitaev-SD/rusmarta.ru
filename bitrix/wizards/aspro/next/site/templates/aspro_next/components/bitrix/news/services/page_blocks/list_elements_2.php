<?if($arSeoItem):?>
	<div class="seo_block">
		<?if($arSeoItem["DETAIL_PICTURE"]):?>
			<img src="<?=CFile::GetPath($arSeoItem["DETAIL_PICTURE"]);?>" alt="" title="" class="img-responsive"/>
		<?endif;?>

		<?$APPLICATION->ShowViewContent('sotbit_seometa_top_desc');?>

		<?if($arSeoItem["PREVIEW_TEXT"]):?>
			<?=$arSeoItem["PREVIEW_TEXT"]?>
		<?endif;?>
		<?if($arSeoItem["PROPERTY_FORM_QUESTION_VALUE"]):?>
			<table class="order-block noicons">
				<tbody>
					<tr>
						<td class="col-md-9 col-sm-8 col-xs-7 valign">
							<div class="text">
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
						</td>
						<td class="col-md-3 col-sm-4 col-xs-5 valign">
							<div class="btns">
								<span><span class="btn btn-default btn-lg white transparent animate-load" data-event="jqm" data-param-form_id="ASK" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : GetMessage('S_ASK_QUESTION'))?></span></span></span>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		<?endif;?>
		<?if($arSeoItem["PROPERTY_TIZERS_VALUE"]):?>
			<?$GLOBALS["arLandingTizers"] = array("ID" => $arSeoItem["PROPERTY_TIZERS_VALUE"]);?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"next",
				array(
					"IBLOCK_TYPE" => "aspro_next_content",
					"IBLOCK_ID" => $arParams["TIZERS_IBLOCK_ID"],
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
		<?$APPLICATION->ShowViewContent('sotbit_seometa_add_desc');?>
	</div>
<?endif;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.list",
	"news-project",
	Array(
		"S_ASK_QUESTION" => $arParams["S_ASK_QUESTION"],
		"S_ORDER_SERVICE" => $arParams["S_ORDER_SERVICE"],
		"T_GALLERY" => $arParams["T_GALLERY"],
		"T_DOCS" => $arParams["T_DOCS"],
		"T_GOODS" => $arParams["T_GOODS"],
		"T_SERVICES" => $arParams["T_SERVICES"],
		"T_PROJECTS" => $arParams["T_PROJECTS"],
		"T_REVIEWS" => $arParams["T_REVIEWS"],
		"T_STAFF" => $arParams["T_STAFF"],
		"COUNT_IN_LINE" => $arParams["COUNT_IN_LINE"],
		"SHOW_SECTION_DESCRIPTION" => $arParams["SHOW_SECTION_DESCRIPTION"],
		"LINE_ELEMENT_COUNT_LIST" => $arParams["LINE_ELEMENT_COUNT_LIST"],
		"SHOW_IMAGE" => $arParams["SHOW_IMAGE"],
		"IMAGE_POSITION" => $arParams["IMAGE_POSITION"],
		"IBLOCK_TYPE"	=>	$arParams["IBLOCK_TYPE"],
		"IBLOCK_ID"	=>	$arParams["IBLOCK_ID"],
		"NEWS_COUNT"	=>	$arParams["NEWS_COUNT"],
		"SORT_BY1"	=>	$arParams["SORT_BY1"],
		"SORT_ORDER1"	=>	$arParams["SORT_ORDER1"],
		"SORT_BY2"	=>	$arParams["SORT_BY2"],
		"SORT_ORDER2"	=>	$arParams["SORT_ORDER2"],
		"FIELD_CODE"	=>	$arParams["LIST_FIELD_CODE"],
		"PROPERTY_CODE"	=>	$arParams["LIST_PROPERTY_CODE"],
		"DISPLAY_PANEL"	=>	$arParams["DISPLAY_PANEL"],
		"SET_TITLE"	=>	$arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN"	=>	$arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN"	=>	$arParams["ADD_SECTIONS_CHAIN"],
		"CACHE_TYPE"	=>	$arParams["CACHE_TYPE"],
		"CACHE_TIME"	=>	$arParams["CACHE_TIME"],
		"CACHE_FILTER"	=>	$arParams["CACHE_FILTER"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"DISPLAY_TOP_PAGER"	=>	$arParams["DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER"	=>	$arParams["DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE"	=>	$arParams["PAGER_TITLE"],
		"PAGER_TEMPLATE"	=>	$arParams["PAGER_TEMPLATE"],
		"PAGER_SHOW_ALWAYS"	=>	$arParams["PAGER_SHOW_ALWAYS"],
		"PAGER_DESC_NUMBERING"	=>	$arParams["PAGER_DESC_NUMBERING"],
		"PAGER_DESC_NUMBERING_CACHE_TIME"	=>	$arParams["PAGER_DESC_NUMBERING_CACHE_TIME"],
		"PAGER_SHOW_ALL" => $arParams["PAGER_SHOW_ALL"],
		"DISPLAY_DATE"	=>	$arParams["DISPLAY_DATE"],
		"DISPLAY_NAME"	=>	$arParams["DISPLAY_NAME"],
		"DISPLAY_PICTURE"	=>	$arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT"	=>	$arParams["DISPLAY_PREVIEW_TEXT"],
		"PREVIEW_TRUNCATE_LEN"	=>	$arParams["PREVIEW_TRUNCATE_LEN"],
		"ACTIVE_DATE_FORMAT"	=>	$arParams["LIST_ACTIVE_DATE_FORMAT"],
		"USE_PERMISSIONS"	=>	$arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS"	=>	$arParams["GROUP_PERMISSIONS"],
		"FILTER_NAME"	=>	$arParams["FILTER_NAME"],
		"HIDE_LINK_WHEN_NO_DETAIL"	=>	$arParams["HIDE_LINK_WHEN_NO_DETAIL"],
		"CHECK_DATES"	=>	$arParams["CHECK_DATES"],
		"PARENT_SECTION"	=>	$arResult["VARIABLES"]["SECTION_ID"],
		"PARENT_SECTION_CODE"	=>	$arResult["VARIABLES"]["SECTION_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"IBLOCK_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"INCLUDE_SUBSECTIONS" => "N",
		"SHOW_DETAIL_LINK" => $arParams["SHOW_DETAIL_LINK"],
	),
	$component
);?>
<?if(!$arSeoItem):?>
	<?$APPLICATION->ShowViewContent('sotbit_seometa_bottom_desc');?>
	<?$APPLICATION->ShowViewContent('sotbit_seometa_add_desc');?>

<?else:?>
	<?if($arSeoItem["DETAIL_TEXT"]):?>
		<?=$arSeoItem["DETAIL_TEXT"];?>
	<?endif;?>

	<?$APPLICATION->ShowViewContent('sotbit_seometa_bottom_desc');?>

<?endif;?>
<?if($arSeoItems):?>
	<?
	$arLandingFilter = array();
	if($arSeoItem)
	{
		$arTmpRegionsLanding[] = $iLandingItemID;
		$arLandingFilter = array(
			array(
				"LOGIC" => "OR",
				array("PROPERTY_SECTION_SERVICES" => false),
				array("PROPERTY_SECTION_SERVICES" => $arSeoItem["PROPERTY_SECTION_SERVICES_VALUE"] ? $arSeoItem["PROPERTY_SECTION_SERVICES_VALUE"] : $arSection["ID"]),
			),
			"!ID" => $arTmpRegionsLanding,
		);
	}
	else
	{
		$arLandingFilter = array(
			array(
				"LOGIC" => "OR",
				array("PROPERTY_SECTION_SERVICES" => false),
				array("PROPERTY_SECTION_SERVICES" => $arSection["ID"]),
			),
			"!ID" => $arTmpRegionsLanding,
		);
	}
	?>
	<?$GLOBALS["arLandingSections"] = $arLandingFilter;?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.list",
		"landings_list",
		array(
			"IBLOCK_TYPE" => "aspro_next_catalog",
			"IBLOCK_ID" => $arParams["LANDING_IBLOCK_ID"],
			"NEWS_COUNT" => "999",
			"SHOW_COUNT" => $arParams["LANDING_SECTION_COUNT"],
			"COMPARE_FIELD" => "FILTER_URL",
			"COMPARE_PROP" => "Y",
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
			"TITLE_BLOCK" => ($arParams["LANDING_TITLE"] ? $arParams["LANDING_TITLE"] : GetMessage("POPULAR_CATEGORYS")),
			"SHOW_404" => "N",
			"MESSAGE_404" => ""
		),
		false, array("HIDE_ICONS" => "Y")
	);?>
<?endif;?>

<?
if($arSeoItem)
{
	$langing_seo_h1 = ($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] != "" ? $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_PAGE_TITLE"] : $arSeoItem["NAME"]);

	$APPLICATION->SetTitle($langing_seo_h1);

	if($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"])
		$APPLICATION->SetPageProperty("title", $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_TITLE"]);
	else
		$APPLICATION->SetPageProperty("title", $arSeoItem["NAME"].$postfix);

	if($arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"])
		$APPLICATION->SetPageProperty("description", $arSeoItem["IPROPERTY_VALUES"]["ELEMENT_META_DESCRIPTION"]);

	if($arSeoItem["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS'])
		$APPLICATION->SetPageProperty("keywords", $arSeoItem["IPROPERTY_VALUES"]['ELEMENT_META_KEYWORDS']);
	?>
<?}?>