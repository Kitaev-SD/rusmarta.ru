<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?
if($arResult["SEARCH"] )
{
	global $arRegion, $arTheme;
	foreach($arResult["SEARCH"] as $key => $arSearch)
	{
		if(strpos($arSearch["URL_WO_PARAMS"], "#YEAR#") !== false)
		{
			if($arSearch["DATE_CHANGE"])
			{
				if($arDateTime = ParseDateTime($arSearch["DATE_CHANGE"], FORMAT_DATETIME))
				{
					$url = str_replace("#YEAR#", $arDateTime['YYYY'], $arSearch["URL_WO_PARAMS"]);
					if($arResult["NAV_RESULT"]->url_add_params)
						$url.= "?".implode("&", $arResult["NAV_RESULT"]->url_add_params);
					$arResult["SEARCH"][$key]["URL"] = $url;
				}
			}
		}
		if($arSearch['MODULE_ID'] == 'iblock')
		{
			if(strpos($arSearch['URL'], $arTheme['CATALOG_PAGE_URL']['VALUE']) !== false)
				unset($arResult["SEARCH"][$key]);
			if($arRegion)
			{
				$arRegionProps = array();
				$rsPropRegion = CIBlockElement::GetProperty($arSearch["PARAM2"], $arSearch["ITEM_ID"], array("sort" => "asc"), Array("CODE"=>"LINK_REGION"));
				while($arPropRegion = $rsPropRegion->Fetch())
				{
					if($arPropRegion['VALUE'])
						$arRegionProps[] = $arPropRegion['VALUE'];
				}
				if($arRegionProps)
				{
					if(!in_array($arRegion['ID'], $arRegionProps))
					{
						unset($arResult["SEARCH"][$key]);
					}
				}
			}
		}
	}
}
?>