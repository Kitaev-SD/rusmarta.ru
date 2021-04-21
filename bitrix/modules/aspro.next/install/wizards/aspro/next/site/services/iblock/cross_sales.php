<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;
if(!CModule::IncludeModule("catalog")) return;

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";

$iblockShortCODE = "cross_sales";
$iblockXMLFile = WIZARD_SERVICE_RELATIVE_PATH."/xml/".LANGUAGE_ID."/".$iblockShortCODE.".xml";
$iblockTYPE = "aspro_next_catalog";
$iblockXMLID = "aspro_next_".$iblockShortCODE."_".WIZARD_SITE_ID;
$iblockCODE = "aspro_next_".$iblockShortCODE;
$iblockID = false;

set_time_limit(0);

$rsIBlock = CIBlock::GetList(array(), array("XML_ID" => $iblockXMLID, "TYPE" => $iblockTYPE));
if ($arIBlock = $rsIBlock->Fetch()) {
	$iblockID = $arIBlock["ID"];
	if (WIZARD_INSTALL_DEMO_DATA) {
		// delete if already exist & need install demo
		CIBlock::Delete($arIBlock["ID"]);
		$iblockID = false;
	}
}

if(WIZARD_INSTALL_DEMO_DATA){
	if(!$iblockID){
		$skuIBlockID = false;
		if($catalogIBlockID = CNextCache::$arIBlocks[WIZARD_SITE_ID]['aspro_next_catalog']['aspro_next_catalog'][0]){
			if($arSku = CCatalogSKU::GetInfoByProductIBlock($catalogIBlockID)){
				$skuIBlockID = $arSku['IBLOCK_ID'];
			}

			$rakivinaSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'rakoviny_umyvalniki',
				),
				false,
				false,
				array('ID')
			);

			$smesitelSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'smesiteli',
				),
				false,
				false,
				array('ID')
			);

			$fotoSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'fotoapparaty',
				),
				false,
				false,
				array('ID')
			);

			$obektivSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'obektivy',
				),
				false,
				false,
				array('ID')
			);
		}

		// add new iblock
		$permissions = array("1" => "X", "2" => "R");
		$dbGroup = CGroup::GetList($by = "", $order = "", array("STRING_ID" => "content_editor"));
		if($arGroup = $dbGroup->Fetch()){
			$permissions[$arGroup["ID"]] = "W";
		};

		// replace macros IN_XML_SITE_ID & IN_XML_SITE_DIR in xml file - for correct url links to site
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back")){
			@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back", $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile);
		}
		@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back");
		CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_SITE_DIR" => WIZARD_SITE_DIR));
		CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_SITE_ID" => WIZARD_SITE_ID));

		if($catalogIBlockID){
			CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_CATALOG_IBLOCK_ID" => $catalogIBlockID));
			if($rakivinaSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_RAKOVINA_SECTION_ID" => $rakivinaSectionID));
			}
			if($smesitelSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_SMESITEL_SECTION_ID" => $smesitelSectionID));
			}
			if($fotoSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_FOTO_SECTION_ID" => $fotoSectionID));
			}
			if($obektivSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_OBECTIV_SECTION_ID" => $obektivSectionID));
			}
		}

		if($skuIBlockID){
			CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_SKU_IBLOCK_ID" => $skuIBlockID));
		}

		$iblockID = WizardServices::ImportIBlockFromXML($iblockXMLFile, $iblockCODE, $iblockTYPE, WIZARD_SITE_ID, $permissions);
		if(file_exists($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back")){
			@copy($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile.".back", $_SERVER["DOCUMENT_ROOT"].$iblockXMLFile);
		}
		if ($iblockID < 1)	return;

		// iblock fields
		$iblock = new CIBlock;
		$arFields = array(
			"ACTIVE" => "Y",
			"CODE" => $iblockCODE,
			"XML_ID" => $iblockXMLID,
			"EDIT_FILE_AFTER" => "",
			"FIELDS" => array(
				"IBLOCK_SECTION" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "Array",
				),
				"ACTIVE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE"=> "Y",
				),
				"ACTIVE_FROM" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"ACTIVE_TO" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"SORT" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "0",
				),
				"NAME" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "",
				),
				"PREVIEW_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"FROM_DETAIL" => "Y",
						"SCALE" => "Y",
						"WIDTH" => "800",
						"HEIGHT" => "800",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 75,
						"DELETE_WITH_DETAIL" => "Y",
						"UPDATE_WITH_DETAIL" => "Y",
					),
				),
				"PREVIEW_TEXT_TYPE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "text",
				),
				"PREVIEW_TEXT" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"DETAIL_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"SCALE" => "Y",
						"WIDTH" => "2000",
						"HEIGHT" => "2000",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 75,
					),
				),
				"DETAIL_TEXT_TYPE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "text",
				),
				"DETAIL_TEXT" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"XML_ID" =>  array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"CODE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"UNIQUE" => "N",
						"TRANSLITERATION" => "Y",
						"TRANS_LEN" => 100,
						"TRANS_CASE" => "L",
						"TRANS_SPACE" => "_",
						"TRANS_OTHER" => "_",
						"TRANS_EAT" => "Y",
						"USE_GOOGLE" => "N",
					),
				),
				"TAGS" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"SECTION_NAME" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "",
				),
				"SECTION_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"FROM_DETAIL" => "N",
						"SCALE" => "N",
						"WIDTH" => "",
						"HEIGHT" => "",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 75,
						"DELETE_WITH_DETAIL" => "N",
						"UPDATE_WITH_DETAIL" => "N",
					),
				),
				"SECTION_DESCRIPTION_TYPE" => array(
					"IS_REQUIRED" => "Y",
					"DEFAULT_VALUE" => "text",
				),
				"SECTION_DESCRIPTION" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"SECTION_DETAIL_PICTURE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"SCALE" => "N",
						"WIDTH" => "",
						"HEIGHT" => "",
						"IGNORE_ERRORS" => "N",
						"METHOD" => "resample",
						"COMPRESSION" => 75,
					),
				),
				"SECTION_XML_ID" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => "",
				),
				"SECTION_CODE" => array(
					"IS_REQUIRED" => "N",
					"DEFAULT_VALUE" => array(
						"UNIQUE" => "N",
						"TRANSLITERATION" => "Y",
						"TRANS_LEN" => 100,
						"TRANS_CASE" => "L",
						"TRANS_SPACE" => "_",
						"TRANS_OTHER" => "_",
						"TRANS_EAT" => "Y",
						"USE_GOOGLE" => "N",
					),
				),
			),
			"INDEX_SECTION" => "N",
			"INDEX_ELEMENT" => "N",
		);

		$iblock->Update($iblockID, $arFields);
	}
	else{
		// attach iblock to site
		$arSites = array();
		$db_res = CIBlock::GetSite($iblockID);
		while ($res = $db_res->Fetch())
			$arSites[] = $res["LID"];
		if (!in_array(WIZARD_SITE_ID, $arSites)){
			$arSites[] = WIZARD_SITE_ID;
			$iblock = new CIBlock;
			$iblock->Update($iblockID, array("LID" => $arSites));
		}
	}

	// iblock user fields
	$dbSite = CSite::GetByID(WIZARD_SITE_ID);
	if($arSite = $dbSite -> Fetch()) $lang = $arSite["LANGUAGE_ID"];
	if(!strlen($lang)) $lang = "ru";
	WizardServices::IncludeServiceLang("editform_useroptions.php", $lang);
	$arProperty = array();
	$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $iblockID));
	while($arProp = $dbProperty->Fetch())
		$arProperty[$arProp["CODE"]] = $arProp["ID"];

	// properties hints
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["PRIORITY"], array("HINT" => GetMessage("WZD_OPTION_375_HINT")));
	unset($ibp);
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["SORT"], array("HINT" => GetMessage("WZD_OPTION_376_HINT")));
	unset($ibp);

	// edit form user oprions
	CUserOptions::SetOption("form", "form_element_".$iblockID, array(
		"tabs" => 'edit1--#--'.GetMessage("WZD_OPTION_382").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_2").'--,--ACTIVE_FROM--#--'.GetMessage("WZD_OPTION_50").'--,--ACTIVE_TO--#--'.GetMessage("WZD_OPTION_52").'--,--NAME--#--'.GetMessage("WZD_OPTION_54").'--,--IBLOCK_ELEMENT_PROP_VALUE--#--'.GetMessage("WZD_OPTION_130").'--,--PROPERTY_'.$arProperty["SHOW_PLACE"].'--#--'.GetMessage("WZD_OPTION_374").'--,--PROPERTY_'.$arProperty["PRIORITY"].'--#--'.GetMessage("WZD_OPTION_375").'--,--PROPERTY_'.$arProperty["SORT"].'--#--'.GetMessage("WZD_OPTION_376").'--,--PROPERTY_'.$arProperty["LAST_LEVEL_RULE"].'--#--'.GetMessage("WZD_OPTION_377").'--,--PROPERTY_'.$arProperty["LAST_RULE"].'--#--'.GetMessage("WZD_OPTION_378").'--;--ñedit1--#--'.GetMessage("WZD_OPTION_383").'--,--PROPERTY_'.$arProperty["PRODUCTS_FILTER"].'--#--'.GetMessage("WZD_OPTION_379").'--,--PROPERTY_'.$arProperty["EXT_PRODUCTS_FILTER"].'--#--'.GetMessage("WZD_OPTION_380").'--;--ñedit2--#--'.GetMessage("WZD_OPTION_384").'--,--PROPERTY_'.$arProperty["USER_GROUPS"].'--#--'.GetMessage("WZD_OPTION_381").'--,--PROPERTY_'.$arProperty["LINK_REGION"].'--#--'.GetMessage("WZD_OPTION_310").'--;--edit2--#--'.GetMessage("WZD_OPTION_82").'--,--SECTIONS--#--'.GetMessage("WZD_OPTION_82").'--;--',
	));

	// list user options
	CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockTYPE.".".$iblockID), array(
		'columns' => 'ACTIVE,NAME,PROPERTY_'.$arProperty["SHOW_PLACE"].',PROPERTY_'.$arProperty["PRIORITY"].',PROPERTY_'.$arProperty["SORT"].',PROPERTY_'.$arProperty["LAST_LEVEL_RULE"].',PROPERTY_'.$arProperty["LAST_RULE"].',SORT,ID', 'by' => 'sort', 'order' => 'asc', 'page_size' => '20',
	));

	if(class_exists('\Bitrix\Main\Grid\Options')){
		$options = new \Bitrix\Main\Grid\Options('tbl_iblock_list_'.md5($iblockTYPE.".".$iblockID));
		if(method_exists($options, 'setColumns')){
			$options->setColumns('ACTIVE,NAME,PROPERTY_'.$arProperty["SHOW_PLACE"].',PROPERTY_'.$arProperty["PRIORITY"].',PROPERTY_'.$arProperty["SORT"].',PROPERTY_'.$arProperty["LAST_LEVEL_RULE"].',PROPERTY_'.$arProperty["LAST_RULE"].',SORT,ID');
		}
		if(method_exists($options, 'setSorting')){
			$options->setSorting('sort', 'asc');
		}
		if(method_exists($options, 'setPageSize')){
			$options->setPageSize(20);
		}
		if(method_exists($options, 'setDefaultView') && method_exists($options, 'getCurrentOptions')){
			$options->setDefaultView($options->getCurrentOptions());
		}
		if(method_exists($options, 'save')){
			$options->save();
		}
	}
}

if($iblockID){
	// replace macros IBLOCK_TYPE & IBLOCK_ID & IBLOCK_CODE
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_CROSSSALES_ID" => $iblockID));
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_CROSSSALES_CODE" => $iblockCODE));
	CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_CROSSSALES_ID" => $iblockID));
	CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_CROSSSALES_CODE" => $iblockCODE));
}
?>