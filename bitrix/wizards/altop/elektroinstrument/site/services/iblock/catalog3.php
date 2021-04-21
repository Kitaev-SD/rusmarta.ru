<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock") || !CModule::IncludeModule("catalog"))
	return;

if(COption::GetOptionString("elektroinstrument", "wizard_installed", "N", WIZARD_SITE_ID) == "Y" && !WIZARD_INSTALL_DEMO_DATA)
	return;

//update iblocks, user fields, demo discount, related properties
if($_SESSION["WIZARD_CATALOG_IBLOCK_ID"]) {
	$IBLOCK_CATALOG_ID = $_SESSION["WIZARD_CATALOG_IBLOCK_ID"];
	unset($_SESSION["WIZARD_CATALOG_IBLOCK_ID"]);
}

if($_SESSION["WIZARD_OFFERS_IBLOCK_ID"]) {
	$IBLOCK_OFFERS_ID = $_SESSION["WIZARD_OFFERS_IBLOCK_ID"];
	unset($_SESSION["WIZARD_OFFERS_IBLOCK_ID"]);
}

if($IBLOCK_OFFERS_ID > 0) {
	$iblockCodeOffers = "offers_".WIZARD_SITE_ID;
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = array(
		"ACTIVE" => "Y",		
		"CODE" => $iblockCodeOffers, 
		"XML_ID" => $iblockCodeOffers,
		"FIELDS" => array(
			"PREVIEW_PICTURE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"FROM_DETAIL" => "Y",					
					"SCALE" => "Y",
					"WIDTH" => "178",
					"HEIGHT" => "178",
					"IGNORE_ERRORS" => "N",
					"METHOD" => "resample",
					"COMPRESSION" => 95,
					"DELETE_WITH_DETAIL" => "N",
					"UPDATE_WITH_DETAIL" => "N"
				)
			)
		)
	);
	$iblock->Update($IBLOCK_OFFERS_ID, $arFields);
}

if($IBLOCK_CATALOG_ID > 0) {
	$iblockCode = "catalog_".WIZARD_SITE_ID;
	//IBlock fields
	$iblock = new CIBlock;
	$arFields = array(
		"ACTIVE" => "Y",		
		"CODE" => $iblockCode, 
		"XML_ID" => $iblockCode,
		"FIELDS" => array(
			"PREVIEW_PICTURE" => array(
				"IS_REQUIRED" => "N",
				"DEFAULT_VALUE" => array(
					"FROM_DETAIL" => "Y",					
					"SCALE" => "Y",
					"WIDTH" => "178",
					"HEIGHT" => "178",
					"IGNORE_ERRORS" => "N",
					"METHOD" => "resample",
					"COMPRESSION" => 95,
					"DELETE_WITH_DETAIL" => "N",
					"UPDATE_WITH_DETAIL" => "N"
				)
			),			
			"CODE" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => array(
					"UNIQUE" => "Y",
					"TRANSLITERATION" => "Y",
					"TRANS_LEN" => 100,
					"TRANS_CASE" => "L",
					"TRANS_SPACE" => "-",
					"TRANS_OTHER" => "-",
					"TRANS_EAT" => "Y",
					"USE_GOOGLE" => "N"
				)
			),
			"SECTION_CODE" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => array(
					"UNIQUE" => "Y",
					"TRANSLITERATION" => "Y",
					"TRANS_LEN" => 100,
					"TRANS_CASE" => "L",
					"TRANS_SPACE" => "-",
					"TRANS_OTHER" => "-",
					"TRANS_EAT" => "Y",
					"USE_GOOGLE" => "N",
				)
			)
		)
	);
	$iblock->Update($IBLOCK_CATALOG_ID, $arFields);

	if($IBLOCK_OFFERS_ID > 0) {
		$ID_SKU = CCatalog::LinkSKUIBlock($IBLOCK_CATALOG_ID, $IBLOCK_OFFERS_ID);
		$rsCatalogs = CCatalog::GetList(
			array(),
			array("IBLOCK_ID" => $IBLOCK_OFFERS_ID),
			false,
			false,
			array("IBLOCK_ID")
		);
		if($arCatalog = $rsCatalogs->Fetch()) {
			CCatalog::Update($IBLOCK_OFFERS_ID, array("PRODUCT_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "SKU_PROPERTY_ID" => $ID_SKU));
		} else {
			CCatalog::Add(array("IBLOCK_ID" => $IBLOCK_OFFERS_ID, "PRODUCT_IBLOCK_ID" => $IBLOCK_CATALOG_ID, "SKU_PROPERTY_ID" => $ID_SKU));
		}
	}

	if(!CCatalog::GetByID($IBLOCK_CATALOG_ID))
		CCatalog::Add(array("IBLOCK_ID" => $IBLOCK_CATALOG_ID));

	//create facet index
	$index = \Bitrix\Iblock\PropertyIndex\Manager::createIndexer($IBLOCK_CATALOG_ID);
	$index->startIndex();
	$index->continueIndex(0);
	$index->endIndex();

	$count = \Bitrix\Iblock\ElementTable::getCount(
		array(
			"=IBLOCK_ID" => $IBLOCK_CATALOG_ID,
			"=WF_PARENT_ELEMENT_ID" => null
		)
	);
	if($count > 0) {
		$catalogReindex = new CCatalogProductAvailable("", 0, 0);
		$catalogReindex->initStep($count, 0, 0);
		$catalogReindex->setParams(array("IBLOCK_ID" => $IBLOCK_CATALOG_ID));
		$catalogReindex->run();
		unset($catalogReindex);
	}

	if($IBLOCK_OFFERS_ID > 0) {
		$index = \Bitrix\Iblock\PropertyIndex\Manager::createIndexer($IBLOCK_OFFERS_ID);
		$index->startIndex();
		$index->continueIndex(0);
		$index->endIndex();

		$count = \Bitrix\Iblock\ElementTable::getCount(
			array(
				"=IBLOCK_ID" => $IBLOCK_OFFERS_ID,
				"=WF_PARENT_ELEMENT_ID" => null
			)
		);
		if($count > 0) {
			$catalogReindex = new CCatalogProductAvailable("", 0, 0);
			$catalogReindex->initStep($count, 0, 0);
			$catalogReindex->setParams(array("IBLOCK_ID" => $IBLOCK_OFFERS_ID));
			$catalogReindex->run();
			unset($catalogReindex);
		}
	}

	\Bitrix\Iblock\PropertyIndex\Manager::checkAdminNotification();
	
	//user fields for sections	
	$arLanguages = Array();
	$rsLanguage = CLanguage::GetList($by, $order, array());
	while($arLanguage = $rsLanguage->Fetch())
		$arLanguages[] = $arLanguage["LID"];
		
	$arUserFields = array("UF_ICON", "UF_VIEW_COLLECTION", "UF_BROWSER_TITLE", "UF_KEYWORDS", "UF_META_DESCRIPTION", "UF_SECTION_TITLE", "UF_SECTION_TITLE_H1", "UF_BACKGROUND_IMAGE", "UF_YOUTUBE_BG", "UF_BANNER", "UF_BANNER_URL", "UF_PREVIEW", "UF_VIEW", "UF_ADVANTAGES");
	foreach($arUserFields as $userField) {
		$arLabelNames = Array();
		foreach($arLanguages as $languageID) {
			WizardServices::IncludeServiceLang("property_names.php", $languageID);
			$arLabelNames[$languageID] = GetMessage($userField);
		}
		
		$arProperty["EDIT_FORM_LABEL"] = $arLabelNames;
		$arProperty["LIST_COLUMN_LABEL"] = $arLabelNames;
		$arProperty["LIST_FILTER_LABEL"] = $arLabelNames;
		
		$dbRes = CUserTypeEntity::GetList(array(), array("ENTITY_ID" => "IBLOCK_".$IBLOCK_CATALOG_ID."_SECTION", "FIELD_NAME" => $userField));
		if($arRes = $dbRes->Fetch()) {
			$userType = new CUserTypeEntity();
			$userType->Update($arRes["ID"], $arProperty);
		}
	}
	
	//demo discount
	if(\Bitrix\Main\Loader::includeModule("sale")) {
		$properties10 = CIBlockProperty::GetPropertyEnum("DISCOUNT", array(), array("IBLOCK_ID" => $IBLOCK_CATALOG_ID, "!VALUE" => false));
		if($propFields10 = $properties10->GetNext()) {
			$arProp10["ID"] = $propFields10["PROPERTY_ID"];
			$arProp10["VALUE"] = $propFields10["ID"];
		}

		$properties20 = CIBlockProperty::GetPropertyEnum("TIME_BUY", array(), array("IBLOCK_ID" => $IBLOCK_CATALOG_ID, "!VALUE" => false));
		if($propFields20 = $properties20->GetNext()) {
			$arProp20["ID"] = $propFields20["PROPERTY_ID"];
			$arProp20["VALUE"] = $propFields20["ID"];
		}

		$rsSections = CIBlockSection::GetList(array(), array("IBLOCK_ID" => $IBLOCK_CATALOG_ID, "CODE" => array("akkumuljatornye-shurupoverty-i-dreli", "firmennye-mayki")), false, array("ID", "CODE", "IBLOCK_ID"));
		while($dbSection = $rsSections->GetNext()) {
			$arSections[$dbSection["CODE"]] = $dbSection["ID"];
		}

		$userGroupIds = array();
		$groupIterator = \Bitrix\Main\GroupTable::getList(array(
			"select" => array("ID")
		));
		while($group = $groupIterator->fetch()) {
			$userGroupIds[] = $group["ID"];
		}

		$arFields10 = array (
			"LID" => WIZARD_SITE_ID,
			"NAME" => GetMessage("WIZ_DISCOUNT_10"),
			"ACTIVE_FROM" => "",
			"ACTIVE_TO" => "",
			"ACTIVE" => "Y",
			"SORT" => "100",
			"PRIORITY" => "11",
			"LAST_DISCOUNT" => "Y",
			"XML_ID" => "",	
			"CONDITIONS" => array(
				"CLASS_ID" => "CondGroup",
				"DATA" => array(
					"All" => "AND",
					"True" => "True"
				),
				"CHILDREN" => array(
					array(
						"CLASS_ID" => "CondBsktProductGroup",
						"DATA" => array(
							"Found" => "Found",
							"All" => "AND"
						),
						"CHILDREN" => array(
							array(
								"CLASS_ID" => "CondIBProp:".$IBLOCK_CATALOG_ID.":".$arProp10["ID"],
								"DATA" => array(
									"logic" => "Equal",
									"value" => $arProp10["VALUE"]
								)
							)
						)
					)
				)
			),
			"ACTIONS" => array(
				"CLASS_ID" => "CondGroup",
				"DATA" => array(
					"All" => "AND"
				),
				"CHILDREN" => array(
					array(
						"CLASS_ID" => "ActSaleBsktGrp",
						"DATA" => array(
							"Type" => "Discount",
							"Value" => 10,
							"Unit" => "Perc",
							"Max" => 0,
							"All" => "AND",
							"True" => "True"
						),
						"CHILDREN" => array(
							array(
								"CLASS_ID" => "CondIBProp:".$IBLOCK_CATALOG_ID.":".$arProp10["ID"],
								"DATA" => array(
									"logic" => "Equal",
									"value" => $arProp10["VALUE"]
								)
							)
						)
					)
				)
			),
			"USER_GROUPS" => $userGroupIds
		);
		CSaleDiscount::Add($arFields10);

		$arFields20 = array (
			"LID" => WIZARD_SITE_ID,
			"NAME" => GetMessage("WIZ_DISCOUNT_20"),
			"ACTIVE_FROM" => "",
			"ACTIVE_TO" => ConvertTimeStamp(time() + 86400 * 100, "FULL"),
			"ACTIVE" => "Y",
			"SORT" => "100",
			"PRIORITY" => "12",
			"LAST_DISCOUNT" => "Y",
			"XML_ID" => "",	
			"CONDITIONS" => array(
				"CLASS_ID" => "CondGroup",
				"DATA" => array(
					"All" => "AND",
					"True" => "True"
				),
				"CHILDREN" => array(
					array(
						"CLASS_ID" => "CondBsktProductGroup",
						"DATA" => array(
							"Found" => "Found",
							"All" => "AND"
						),
						"CHILDREN" => array(
							array(
								"CLASS_ID" => "CondIBProp:".$IBLOCK_CATALOG_ID.":".$arProp20["ID"],
								"DATA" => array(
									"logic" => "Equal",
									"value" => $arProp20["VALUE"]
								)
							)
						)
					)
				)
			),
			"ACTIONS" => array(
				"CLASS_ID" => "CondGroup",
				"DATA" => array(
					"All" => "AND"
				),
				"CHILDREN" => array(
					array(
						"CLASS_ID" => "ActSaleBsktGrp",
						"DATA" => array(
							"Type" => "Discount",
							"Value" => 20,
							"Unit" => "Perc",
							"Max" => 0,
							"All" => "AND",
							"True" => "True"
						),
						"CHILDREN" => array(
							array(
								"CLASS_ID" => "CondIBProp:".$IBLOCK_CATALOG_ID.":".$arProp20["ID"],
								"DATA" => array(
									"logic" => "Equal",
									"value" => $arProp20["VALUE"]
								)
							)
						)
					)
				)
			),
			"USER_GROUPS" => $userGroupIds
		);
		CSaleDiscount::Add($arFields20);
		
		$arGifts = array(
			"LID" => WIZARD_SITE_ID,
			"NAME" => GetMessage("WIZ_DISCOUNT_GIFTS"),
			"ACTIVE_FROM" => "",
			"ACTIVE_TO" => "",
			"ACTIVE" => "Y",
			"SORT" => "100",
			"PRIORITY" => "13",
			"LAST_DISCOUNT" => "N",
			"XML_ID" => "",	
			"CONDITIONS" => array(
				"CLASS_ID" => "CondGroup",
				"DATA" => array(
					"All" => "AND",
					"True" => "True"
				),
				"CHILDREN" => array(
					array(
						"CLASS_ID" => "CondBsktProductGroup",
						"DATA" => array(
							"Found" => "Found",
							"All" => "OR"
						),
						"CHILDREN" => array(
							array(
								"CLASS_ID" => "CondIBSection",
								"DATA" => array(
									"logic" => "Equal",
									"value" => $arSections["akkumuljatornye-shurupoverty-i-dreli"]
								)
							)
						)
					)
				)
			),
			"ACTIONS" => array(
				"CLASS_ID" => "CondGroup",
				"DATA" => array(
					"All" => "AND"
				),
				"CHILDREN" => array(
					array(
						"CLASS_ID" => "GiftCondGroup",
						"DATA" => array(
							"All" => "AND"
						),
						"CHILDREN" => array(
							array(
								"CLASS_ID" => "GifterCondIBSection",
								"DATA" => array(
									"Type" => "one",
									"Value" => $arSections["firmennye-mayki"]
								)
							)
						)
					)
				)
			),
			"USER_GROUPS" => $userGroupIds
		);
		CSaleDiscount::Add($arGifts);
	}
	
	//Related properties
	$arProp4Link = array(
		"VERSIONS_PERFORMANCE" => "colors",
		"MANUFACTURER" => "vendors",
		"GIFT" => "gifts",
		"ACCESSORIES" => "catalog"
	);

	$arProp4LinkSF = array(
		"VERSIONS_PERFORMANCE" => "N",
		"MANUFACTURER" => "Y",
		"GIFT" => "N",
		"ACCESSORIES" => "N"
	);

	$dbProp = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	while($arProp = $dbProp->Fetch()){
		if(!array_key_exists($arProp["CODE"], $arProp4Link))
			continue;

		$rsIblock = CIBlock::GetList(array(), array("CODE" => $arProp4Link[$arProp["CODE"]]."_".WIZARD_SITE_ID, "XML_ID" => $arProp4Link[$arProp["CODE"]]."_".WIZARD_SITE_ID, "TYPE" => "catalog"));
		if($arIblock = $rsIblock->Fetch()){
			$arFieldsUpdate = Array(
				"LINK_IBLOCK_ID" => $arIblock["ID"],
				"IBLOCK_ID" => $IBLOCK_CATALOG_ID,
				"SMART_FILTER" => $arProp4LinkSF[$arProp["CODE"]]
			);

			$ibp = new CIBlockProperty;
			if(!$ibp->Update($arProp["ID"], $arFieldsUpdate))
				return;
		}
	}

	//iblock user fields
	$arProperty = array();
	$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	while($arProp = $dbProperty->Fetch()) {
		$arProperty[$arProp["CODE"]] = $arProp["ID"];
	}

	//list user options
	CUserOptions::SetOption("list", "tbl_iblock_section_".md5("catalog.".$IBLOCK_CATALOG_ID), array(
		"columns" => "NAME, UF_BACKGROUND_IMAGE, UF_BANNER, UF_BANNER_URL, UF_PREVIEW, UF_VIEW, UF_ADVANTAGES, ACTIVE, SORT, TIMESTAMP_X, ID",
		"by" => "timestamp_x",
		"order" => "desc",
		"page_size" => "20"
	));	

	CUserOptions::SetOption("list", "tbl_iblock_element_".md5("catalog.".$IBLOCK_CATALOG_ID), array(
		"columns" => "CATALOG_TYPE, NAME, PROPERTY_".$arProperty["ARTNUMBER"].", PREVIEW_PICTURE, DETAIL_PICTURE, CATALOG_QUANTITY, CATALOG_GROUP_1, ACTIVE, SORT, TIMESTAMP_X, ID",
		"by" => "timestamp_x",
		"order" => "desc",
		"page_size" => "20"
	));

	CUserOptions::SetOption("list", "tbl_catalog_section_".md5("catalog.".$IBLOCK_CATALOG_ID), array(
		"columns" => "NAME, UF_BACKGROUND_IMAGE, UF_BANNER, UF_BANNER_URL, UF_PREVIEW, UF_VIEW, UF_ADVANTAGES, ACTIVE, SORT, TIMESTAMP_X, ID",
		"by" => "timestamp_x",
		"order" => "desc",
		"page_size" => "20"
	));

	CUserOptions::SetOption("list", "tbl_product_admin_".md5("catalog.".$IBLOCK_CATALOG_ID), array(
		"columns" => "CATALOG_TYPE, NAME, PROPERTY_".$arProperty["ARTNUMBER"].", PREVIEW_PICTURE, DETAIL_PICTURE, CATALOG_QUANTITY, CATALOG_GROUP_1, ACTIVE, SORT, TIMESTAMP_X, ID",
		"by" => "timestamp_x",
		"order" => "desc",
		"page_size" => "20"
	));

	if($IBLOCK_OFFERS_ID > 0) {
		$arProp4LinkOffers = array(
			"COLOR" => "colors"
		);

		$arProp4LinkSFOffers = array(
			"COLOR" => "Y"
		);

		$dbPropOffers = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $IBLOCK_OFFERS_ID));
		while($arPropOffers = $dbPropOffers->Fetch()){
			if(!array_key_exists($arPropOffers["CODE"], $arProp4LinkOffers))
				continue;

			$rsIblockOffers = CIBlock::GetList(array(), array("CODE" => $arProp4LinkOffers[$arPropOffers["CODE"]]."_".WIZARD_SITE_ID, "XML_ID" => $arProp4LinkOffers[$arPropOffers["CODE"]]."_".WIZARD_SITE_ID, "TYPE" => "catalog"));
			if($arIblockOffers = $rsIblockOffers->Fetch()){
				$arFieldsUpdateOffers = Array(
					"LINK_IBLOCK_ID" => $arIblockOffers["ID"],
					"IBLOCK_ID" => $IBLOCK_OFFERS_ID,
					"SMART_FILTER" => $arProp4LinkSFOffers[$arPropOffers["CODE"]]
				);

				$ibpOffers = new CIBlockProperty;
				if(!$ibpOffers->Update($arPropOffers["ID"], $arFieldsUpdateOffers))
					return;
			}
		}

		//iblock user fields
		$arProperty = array();
		$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $IBLOCK_OFFERS_ID));
		while($arProp = $dbProperty->Fetch()) {
			$arProperty[$arProp["CODE"]] = $arProp["ID"];
		}

		//list user options
		CUserOptions::SetOption("list", "tbl_iblock_element_".md5("catalog.".$IBLOCK_OFFERS_ID), array(
			"columns" => "CATALOG_TYPE, NAME, PROPERTY_".$arProperty["ARTNUMBER"].", PREVIEW_PICTURE, DETAIL_PICTURE, CATALOG_QUANTITY, CATALOG_GROUP_1, ACTIVE, SORT, TIMESTAMP_X, ID",
			"by" => "timestamp_x",
			"order" => "desc",
			"page_size" => "20"
		));
	}

	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/.left.menu_ext.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/ajax/compare_line.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/catalog/index.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/discount.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/footer_compare.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/header_search.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/linked.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/newproduct.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/promotions_products.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));	
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/recommend.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	$dbProperty = CIBlockProperty::GetPropertyEnum("THIS_COLLECTION", array(), array("IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	if($arProp = $dbProperty->GetNext()) {
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/recommend.php", array("ITEMS_PROPERTY_THIS_COLLECTION" => $arProp["PROPERTY_ID"], "ITEMS_PROPERTY_THIS_COLLECTION_VALUE" => $arProp["ID"]));
	}
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/saleleader.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/sections.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/slider_left.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/viewed_products.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	if($IBLOCK_OFFERS_ID > 0)
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/include/viewed_products.php", array("OFFERS_IBLOCK_ID" => $IBLOCK_OFFERS_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/personal/cart/index.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/personal/order/make/index.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	if($IBLOCK_OFFERS_ID > 0)
		CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/personal/order/make/index.php", array("OFFERS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
	CWizardUtil::ReplaceMacros(WIZARD_SITE_PATH."/vendors/index.php", array("ITEMS_IBLOCK_ID" => $IBLOCK_CATALOG_ID));
}?>