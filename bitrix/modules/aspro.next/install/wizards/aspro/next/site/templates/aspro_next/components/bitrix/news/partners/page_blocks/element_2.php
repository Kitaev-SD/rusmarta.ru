<?global $arTheme, $arRegion;?>
<?$APPLICATION->IncludeComponent(
	"bitrix:news.detail",
	"partners",
	Array(
		"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
		"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
		"SHOW_GALLERY" => $arParams["SHOW_GALLERY"],
		"T_GALLERY" => $arParams["T_GALLERY"],
		"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
		"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
		"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
		"IBLOCK_ID" => $arParams["IBLOCK_ID"],
		"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
		"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
		"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
		"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
		"META_KEYWORDS" => $arParams["META_KEYWORDS"],
		"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
		"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
		"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
		"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
		"SET_TITLE" => $arParams["SET_TITLE"],
		"SET_STATUS_404" => $arParams["SET_STATUS_404"],
		"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
		"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
		"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
		"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
		"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
		"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
		"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
		"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
		"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
		"SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
		"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
		"CHECK_DATES" => $arParams["CHECK_DATES"],
		"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
		"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
		"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
		"USE_SHARE" 			=> $arParams["USE_SHARE"],
		"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
		"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
		"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
		"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
		"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
	),
	$component
);?>

<? // link goods?>
<?if($arParams["SHOW_LINKED_PRODUCTS"] == "Y" && strlen($arParams["LINKED_PRODUCTS_PROPERTY"])):?>
	<?
	$catalogIBlockID = ($arParams["IBLOCK_CATALOG_ID"] ? $arParams["IBLOCK_CATALOG_ID"] : $arTheme["CATALOG_IBLOCK_ID"]["VALUE"]);

	$arItemsFilter = array("IBLOCK_ID" => $catalogIBlockID, "ACTIVE"=>"Y", "PROPERTY_".$arParams["LINKED_PRODUCTS_PROPERTY"] => $arElement["ID"], 'SECTION_GLOBAL_ACTIVE' => 'Y');
	$arItems = CNextCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"Y", "TAG" => CNextCache::GetIBlockCacheTag($arTheme["CATALOG_IBLOCK_ID"]["VALUE"]))), $arItemsFilter, false, false, array("ID", "IBLOCK_ID", "IBLOCK_SECTION_ID"));

	if($arItems)
	{
		$arSectionsID = array();
		foreach($arItems as $arItem)
		{
			if($arItem["IBLOCK_SECTION_ID"])
			{
				if(is_array($arItem["IBLOCK_SECTION_ID"]))
					$arSectionsID = array_merge($arSectionsID, $arItem["IBLOCK_SECTION_ID"]);
				else
					$arSectionsID[] = $arItem["IBLOCK_SECTION_ID"];
			}
		}

		if($arSectionsID){
			$arSectionsID = array_unique($arSectionsID);
		}

		if($arSectionsID):?>
			<div class="wraps goods-block with-padding">
				<h5><?=str_replace("#BRAND_NAME#", $arElement["NAME"], (strlen($arParams['T_GOODS_SECTION']) ? $arParams['T_GOODS_SECTION'] : GetMessage('T_GOODS_SECTION')))?></h5>
				<?$GLOBALS["arBrandSections"] = array("ID" => $arSectionsID);?>
				<?$APPLICATION->IncludeComponent(
					"aspro:catalog.section.list.next",
					"front_sections_only",
					array(
						"IBLOCK_TYPE" => "aspro_next_catalog",
						"IBLOCK_ID" => $arTheme["CATALOG_IBLOCK_ID"]["VALUE"],
						"CACHE_TYPE" => "A",
						"CACHE_TIME" => "36000000",
						"CACHE_FILTER" => "Y",
						"CACHE_GROUPS" => "N",
						"COUNT_ELEMENTS" => "N",
						"FILTER_NAME" => "arBrandSections",
						"TOP_DEPTH" => $arParams["DEPTH_LEVEL_BRAND"],
						"SECTION_URL" => "",
						"VIEW_MODE" => "",
						"SHOW_PARENT_NAME" => "N",
						"USE_FILTER_SECTION" => "Y",
						"BRAND_NAME" => $arElement["NAME"],
						"BRAND_CODE" => $arElement["CODE"],
						"HIDE_SECTION_NAME" => "N",
						"ADD_SECTIONS_CHAIN" => "N",
						"SHOW_SECTIONS_LIST_PREVIEW" => "N",
						"SECTIONS_LIST_PREVIEW_PROPERTY" => "N",
						"SECTIONS_LIST_PREVIEW_DESCRIPTION" => "N",
						"SHOW_SECTION_LIST_PICTURES" => "N",
						"DISPLAY_PANEL" => "N",
						"COMPONENT_TEMPLATE" => "front_sections_theme",
						"SECTION_ID" => "",
						"SECTION_CODE" => "",
						"SECTION_FIELDS" => array(
							0 => "",
							1 => "",
						),
						"SECTION_USER_FIELDS" => array(
							0 => "",
							1 => "",
						),
					),
					false, array("HIDE_ICONS" => "Y")
				);?>
			</div>
		<?endif;
	}?>
<?endif;?>