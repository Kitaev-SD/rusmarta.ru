<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;	

$arParams["IBLOCK_ID"] = "#ITEMS_IBLOCK_ID#";
$arParams["DEPTH_LEVEL"] = "4";
$arParams["CACHE_TIME"] = "36000000";

$arResult["SECTIONS"] = array();
$arResult["ELEMENT_LINKS"] = array();

$arOrder = array(
	"left_margin" => "asc",
);

$arFilter = array(
	"IBLOCK_ID" => $arParams["IBLOCK_ID"],
	"GLOBAL_ACTIVE" => "Y",
	"IBLOCK_ACTIVE" => "Y",
	"<="."DEPTH_LEVEL" => $arParams["DEPTH_LEVEL"],
);

$arSelect = array("ID", "IBLOCK_ID", "NAME", "PICTURE", "DEPTH_LEVEL", "SECTION_PAGE_URL", "UF_ICON");

$rsSections = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);		

while($arSection = $rsSections->GetNext()) {
	$arResult["SECTIONS"][] = array(
		"ID" => $arSection["ID"],
		"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
		"~NAME" => $arSection["~NAME"],
		"~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
		"PICTURE" => $arSection["PICTURE"],
		"ICON" => $arSection["UF_ICON"]
	);
	$arResult["ELEMENT_LINKS"][$arSection["ID"]] = array();
}

$aMenuLinksExt = array();
$menuIndex = 0;
$previousDepthLevel = 1;
foreach($arResult["SECTIONS"] as $arSection) {
	if($menuIndex > 0)
		$aMenuLinksExt[$menuIndex - 1][3]["IS_PARENT"] = $arSection["DEPTH_LEVEL"] > $previousDepthLevel;
	$previousDepthLevel = $arSection["DEPTH_LEVEL"];

	$arResult["ELEMENT_LINKS"][$arSection["ID"]][] = urldecode($arSection["~SECTION_PAGE_URL"]);
	$aMenuLinksExt[$menuIndex++] = array(
		htmlspecialcharsbx($arSection["~NAME"]),
		$arSection["~SECTION_PAGE_URL"],
		$arResult["ELEMENT_LINKS"][$arSection["ID"]],
		array(
			"FROM_IBLOCK" => true,
			"IS_PARENT" => false,
			"DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
			"PICTURE" => $arSection["PICTURE"],
			"ICON" => $arSection["ICON"]
		)
	);
}

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);?>