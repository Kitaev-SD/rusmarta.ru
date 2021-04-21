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

$iblockShortCODE = "search";
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

			$vannaSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'vanny',
				),
				false,
				false,
				array('ID')
			);

			$dreliSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'dreli',
				),
				false,
				false,
				array('ID')
			);

			$perforatorySectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'perforatory',
				),
				false,
				false,
				array('ID')
			);

			$elinstumentsSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'elektroinstrument',
				),
				false,
				false,
				array('ID')
			);

			$akkumSectionID = CNextCache::CIBlockSection_GetList(
				array(
					'CACHE' => array(
						'TAG' => CNextCache::GetIBlockCacheTag($catalogIBlockID),
						'MULTI' => 'N',
						'RESULT' => array('ID'),
					)
				),
				array(
					'IBLOCK_ID' => $catalogIBlockID,
					'CODE' => 'akkumulyatory',
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
			if($vannaSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_VANNA_SECTION_ID" => $vannaSectionID));
			}
			if($dreliSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_DRELI_SECTION_ID" => $dreliSectionID));
			}
			if($perforatorySectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_PERFORATORY_SECTION_ID" => $perforatorySectionID));
			}
			if($elinstumentsSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_ELINSTRUMENTS_SECTION_ID" => $elinstumentsSectionID));
			}
			if($akkumSectionID){
				CWizardUtil::ReplaceMacros($_SERVER["DOCUMENT_ROOT"].$iblockXMLFile, Array("IN_XML_AKKUM_SECTION_ID" => $akkumSectionID));
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
					"DEFAULT_VALUE" => "=today",
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

	$arIBlocksTmp = CNextCache::CIBlock_GetList(array("CACHE" => array("TAG" => "iblocks")), array("ACTIVE" => "Y", "CHECK_PERMISSIONS" => "N"));
	CNextCache::$arIBlocks = CNextCache::GroupArrayBy($arIBlocksTmp, array("GROUP" => array("LID", "IBLOCK_TYPE_ID", "CODE"), "MULTI" => "Y", "RESULT" => array("ID")));
	CNextCache::$arIBlocksInfo = CNextCache::GroupArrayBy($arIBlocksTmp, array("GROUP" => array("ID")));

	// upadate elements for update event (need to add urlrewrite items && correct serialized some property values)
	$arIDByXML_ID = array();
	$dbRes = CIBlockElement::GetList(array(), array('IBLOCK_ID' => $iblockID), false, false, array('ID', 'XML_ID'));
	while($arElement = $dbRes->Fetch()){
		$arIDByXML_ID[$arElement['XML_ID']] = $arElement['ID'];
		$el = new CIBlockElement;
		$el->Update($arElement['ID'], array('ACTIVE' => 'N'));
		$el->Update($arElement['ID'], array('ACTIVE' => 'Y'));
		unset($el);
	}

	foreach($arIDByXML_ID as $_XML_ID => $_ID){
		if($_XML_ID == 3362){
			CIBlockElement::SetPropertyValuesEx($_ID, $iblockID, array('SIMILAR' => $arIDByXML_ID[3363]));
		}
		elseif($_XML_ID == 3363){
			CIBlockElement::SetPropertyValuesEx($_ID, $iblockID, array('SIMILAR' => $arIDByXML_ID[3362]));
		}
		elseif($_XML_ID == 3364){
			CIBlockElement::SetPropertyValuesEx($_ID, $iblockID, array('SIMILAR' => $arIDByXML_ID[3365]));
		}
		elseif($_XML_ID == 3365){
			CIBlockElement::SetPropertyValuesEx($_ID, $iblockID, array('SIMILAR' => $arIDByXML_ID[3364]));
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
	$ibp->Update($arProperty["REDIRECT_URL"], array("HINT" => GetMessage("WZD_OPTION_325_HINT")));
	unset($ibp);
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["QUERY_REPLACEMENT"], array("HINT" => GetMessage("WZD_OPTION_331_HINT")));
	unset($ibp);
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["CUSTOM_FILTER"], array("HINT" => GetMessage("WZD_OPTION_332_HINT")));
	unset($ibp);
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["URL_CONDITION"], array("HINT" => GetMessage("WZD_OPTION_334_HINT")));
	unset($ibp);
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["I_SKU_PAGE_TITLE"], array("HINT" => GetMessage("WZD_OPTION_367_HINT")));
	unset($ibp);
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["I_SKU_PREVIEW_PICTURE_FILE_TITLE"], array("HINT" => GetMessage("WZD_OPTION_368_HINT")));
	unset($ibp);
	$ibp = new CIBlockProperty;
	$ibp->Update($arProperty["I_SKU_PREVIEW_PICTURE_FILE_ALT"], array("HINT" => GetMessage("WZD_OPTION_369_HINT")));
	unset($ibp);

	// edit form user oprions
	CUserOptions::SetOption("form", "form_element_".$iblockID, array(
		"tabs" => 'edit1--#--'.GetMessage("WZD_OPTION_90").'--,--ACTIVE--#--'.GetMessage("WZD_OPTION_2").'--,--NAME--#--'.GetMessage("WZD_OPTION_54").'--,--XML_ID--#--'.GetMessage("WZD_OPTION_219").'--,--SORT--#--'.GetMessage("WZD_OPTION_44").'--,--IBLOCK_ELEMENT_PROP_VALUE--#--'.GetMessage("WZD_OPTION_130").'--,--PROPERTY_'.$arProperty["QUERY"].'--#--'.GetMessage("WZD_OPTION_324").'--,--PROPERTY_'.$arProperty["IS_INDEX"].'--#--'.GetMessage("WZD_OPTION_323").'--,--PROPERTY_'.$arProperty["IS_SEARCH_TITLE"].'--#--'.GetMessage("WZD_OPTION_361").'--,--PROPERTY_'.$arProperty["URL_CONDITION"].'--#--'.GetMessage("WZD_OPTION_334").'--,--PROPERTY_'.$arProperty["REDIRECT_URL"].'--#--'.GetMessage("WZD_OPTION_325").'--,--PROPERTY_'.$arProperty["QUERY_REPLACEMENT"].'--#--'.GetMessage("WZD_OPTION_331").'--,--PROPERTY_'.$arProperty["CUSTOM_FILTER"].'--#--'.GetMessage("WZD_OPTION_332").'--,--PROPERTY_'.$arProperty["CUSTOM_FILTER_TYPE"].'--#--'.GetMessage("WZD_OPTION_333").'--,--PROPERTY_'.$arProperty["SIMILAR"].'--#--'.GetMessage("WZD_OPTION_326").'--,--PROPERTY_'.$arProperty["LINK_REGION"].'--#--'.GetMessage("WZD_OPTION_310").'--;--cedit1--#--'.GetMessage("WZD_OPTION_108").'--,--PROPERTY_'.$arProperty["HIDE_QUERY_INPUT"].'--#--'.GetMessage("WZD_OPTION_370").'--,--PROPERTY_'.$arProperty["FORM_QUESTION"].'--#--'.GetMessage("WZD_OPTION_327").'--,--PROPERTY_'.$arProperty["H3_GOODS"].'--#--'.GetMessage("WZD_OPTION_300").'--,--PROPERTY_'.$arProperty["TIZERS"].'--#--'.GetMessage("WZD_OPTION_292").'--,--DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_122").'--,--PREVIEW_TEXT--#--'.GetMessage("WZD_OPTION_14").'--,--DETAIL_TEXT--#--'.GetMessage("WZD_OPTION_110").'--;--edit14--#--'.GetMessage("WZD_OPTION_18").'--,--IPROPERTY_TEMPLATES_ELEMENT_META_TITLE--#--'.GetMessage("WZD_OPTION_20").'--,--IPROPERTY_TEMPLATES_ELEMENT_META_KEYWORDS--#--'.GetMessage("WZD_OPTION_22").'--,--IPROPERTY_TEMPLATES_ELEMENT_META_DESCRIPTION--#--'.GetMessage("WZD_OPTION_24").'--,--IPROPERTY_TEMPLATES_ELEMENT_PAGE_TITLE--#--'.GetMessage("WZD_OPTION_26").'--,--IPROPERTY_TEMPLATES_ELEMENTS_PREVIEW_PICTURE--#--'.GetMessage("WZD_OPTION_28").'--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_ALT--#--'.GetMessage("WZD_OPTION_30").'--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_TITLE--#--'.GetMessage("WZD_OPTION_32").'--,--IPROPERTY_TEMPLATES_ELEMENT_PREVIEW_PICTURE_FILE_NAME--#--'.GetMessage("WZD_OPTION_34").'--,--IPROPERTY_TEMPLATES_ELEMENTS_DETAIL_PICTURE--#--'.GetMessage("WZD_OPTION_36").'--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_ALT--#--'.GetMessage("WZD_OPTION_30").'--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_TITLE--#--'.GetMessage("WZD_OPTION_32").'--,--IPROPERTY_TEMPLATES_ELEMENT_DETAIL_PICTURE_FILE_NAME--#--'.GetMessage("WZD_OPTION_34").'--,--SEO_ADDITIONAL--#--'.GetMessage("WZD_OPTION_42").'--,--TAGS--#--'.GetMessage("WZD_OPTION_46").'--,--IPROPERTY_TEMPLATES_ELEMENTS--#--'.GetMessage("WZD_OPTION_362").'--,--PROPERTY_'.$arProperty["I_ELEMENT_PAGE_TITLE"].'--#--'.GetMessage("WZD_OPTION_363").'--,--PROPERTY_'.$arProperty["I_ELEMENT_PREVIEW_PICTURE_FILE_TITLE"].'--#--'.GetMessage("WZD_OPTION_364").'--,--PROPERTY_'.$arProperty["I_ELEMENT_PREVIEW_PICTURE_FILE_ALT"].'--#--'.GetMessage("WZD_OPTION_365").'--,--IPROPERTY_TEMPLATES_SKU--#--'.GetMessage("WZD_OPTION_366").'--,--PROPERTY_'.$arProperty["I_SKU_PAGE_TITLE"].'--#--'.GetMessage("WZD_OPTION_367").'--,--PROPERTY_'.$arProperty["I_SKU_PREVIEW_PICTURE_FILE_TITLE"].'--#--'.GetMessage("WZD_OPTION_368").'--,--PROPERTY_'.$arProperty["I_SKU_PREVIEW_PICTURE_FILE_ALT"].'--#--'.GetMessage("WZD_OPTION_369").'--;--edit2--#--'.GetMessage("WZD_OPTION_82").'--,--SECTIONS--#--'.GetMessage("WZD_OPTION_82").'--;--cedit2--#--'.GetMessage("WZD_OPTION_328").'--,--PROPERTY_'.$arProperty["META_HASH"].'--#--'.GetMessage("WZD_OPTION_329").'--,--PROPERTY_'.$arProperty["META_DATA"].'--#--'.GetMessage("WZD_OPTION_330").'--;--;--',
	));
	// list user options
	CUserOptions::SetOption("list", "tbl_iblock_list_".md5($iblockTYPE.".".$iblockID), array(
		'columns' => 'ACTIVE,NAME,PROPERTY_'.$arProperty["QUERY"].',PROPERTY_'.$arProperty["URL_CONDITION"].',PROPERTY_'.$arProperty["REDIRECT_URL"].',PROPERTY_'.$arProperty["QUERY_REPLACEMENT"].',PROPERTY_'.$arProperty["IS_INDEX"].',SORT,ID', 'by' => 'sort', 'order' => 'asc', 'page_size' => '20',
	));
	if(class_exists('\Bitrix\Main\Grid\Options')){
		$options = new \Bitrix\Main\Grid\Options('tbl_iblock_list_'.md5($iblockTYPE.".".$iblockID));
		if(method_exists($options, 'setColumns')){
			$options->setColumns('ACTIVE,NAME,PROPERTY_'.$arProperty["QUERY"].',PROPERTY_'.$arProperty["URL_CONDITION"].',PROPERTY_'.$arProperty["REDIRECT_URL"].',PROPERTY_'.$arProperty["QUERY_REPLACEMENT"].',PROPERTY_'.$arProperty["IS_INDEX"].',SORT,ID');
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
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_LANDING_SEARCH_ID" => $iblockID));
	CWizardUtil::ReplaceMacrosRecursive(WIZARD_SITE_PATH, Array("IBLOCK_LANDING_SEARCH_CODE" => $iblockCODE));
	CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_LANDING_SEARCH_ID" => $iblockID));
	CWizardUtil::ReplaceMacrosRecursive($bitrixTemplateDir, Array("IBLOCK_LANDING_SEARCH_CODE" => $iblockCODE));
}
?>