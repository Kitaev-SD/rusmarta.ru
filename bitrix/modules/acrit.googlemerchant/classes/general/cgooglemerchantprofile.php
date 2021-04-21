<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UserTable;
use Bitrix\Catalog;
use Bitrix\Highloadblock\HighloadBlockTable;

Loc::loadMessages( __FILE__ );

require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php" );

class CGooglemerchantProfile{
    private $typeLocation = "/../../lib/types";

    public function GetIBlockTypes( $lid, $catalogOnly = true, $hideOffers = true ){
        CModule::IncludeModule( "iblock" );

        $arTypeAll = array();
        $dbIBlock = CIBlock::GetList(
            array(
                "IBLOCK_TYPE" => "ASC",
                "NAME"        => "ASC"
            ),
            array(
                "LID" => $lid
            )
        );

        while( $arIBlock = $dbIBlock->Fetch() ){
            if( true == $catalogOnly ){
                if( CModule::IncludeModule( "catalog" ) && CCatalog::GetByID( $arIBlock["ID"] ) ){
                    if( ( $hideOffers == true ) && CCatalogSKU::GetInfoByOfferIBlock( $arIBlock["ID"] ) ){
                        continue;
                    }
                    $arTypeIblock[] = $arIBlock;
                }
            }
            else{
                $arTypeIblock[] = $arIBlock;
            }
        }

        unset( $dbIBlock );

        foreach( $arTypeIblock as $arIBlock ){
            $arType = CIBlockType::GetByIDLang( $arIBlock["IBLOCK_TYPE_ID"], LANG );
            $arTypeAll[$arType["ID"]]["ID"] = $arType["ID"];
            $arTypeAll[$arType["ID"]]["NAME"] = "{$arType["NAME"]} [{$arType["ID"]}]";
            $arTypeAll[$arType["ID"]]["IBLOCK"][$arIBlock["ID"]] = "{$arIBlock["NAME"]} [{$arIBlock["ID"]}]";
            unset( $arType );
        }

        unset( $arTypeIblock );

        return $arTypeAll;
    }

    public function GetSections( $ib, $subsection = false, $bShort = false ){
        CModule::IncludeModule( "iblock" );

        if( !is_array( $ib ) || empty( $ib ) )
            return array();

        $arFilter = array( "IBLOCK_ID" => $ib );
        if( !$subsection )
            $arFilter["DEPTH_LEVEL"] = 1;

        $dbSection = CIBlockSection::GetList(
            array(
                "LEFT_MARGIN" => "ASC"
            ),
            $arFilter
        );

        $sections = array();
        while( $arSection = $dbSection->Fetch() ){
            if( $bShort ){
                $sections[$arSection["ID"]] = array(
                    "ID"        => $arSection["ID"],
                    "NAME"      => $arSection["NAME"],
                    "LEVEL"     => $arSection["DEPTH_LEVEL"],
                    "PARENT"    => $arSection["IBLOCK_SECTION_ID"],
                    "PARENT_1"  => $arSection["ID"],
                    "IBLOCK_ID" => $arSection["IBLOCK_ID"]
                );
            }
            else{
                $sections[$arSection["DEPTH_LEVEL"]][$arSection["ID"]] = array(
                    "ID"        => $arSection["ID"],
                    "NAME"      => "{$arSection["NAME"]} [{$arSection["ID"]}]",
                    "LEVEL"     => $arSection["DEPTH_LEVEL"],
                    "PARENT"    => $arSection["IBLOCK_SECTION_ID"],
                    "PARENT_1"  => $arSection["ID"],
                    "IBLOCK_ID" => $arSection["IBLOCK_ID"]
                );
            }
        }

        unset( $dbSection );
        ksort( $sections );

        if( !$bShort ){
            foreach( $sections as $depth => &$depthSections ){
                if( $depth == 1 )
                    continue;

                foreach( $depthSections as $id => &$current ){
                    $current["NAME"] = implode( " / ", array(
                        $sections[$depth - 1][$current["PARENT"]]["NAME"],
                        $current["NAME"]
                    ) );

                    $current["PARENT_1"] = $sections[$depth - 1][$current["PARENT"]]["PARENT_1"];
                }
            }
        }
        else{
            foreach( $sections as $id => $current ){
                if( intval( $current["PARENT"] ) > 0 ){
                    if( !isset( $sections[$current["PARENT"]]["CHILDS"] ) ){
                        $sections[$current["PARENT"]]["CHILDS"] = array();
                    }

                    $sections[$current["PARENT"]]["CHILDS"][$current["ID"]] = $current;
                }
            }
        }

        return $sections;
    }

    public function GetIBlockTreeShowType( $lid, $catalogOnly = true, $hideOffers = true, $subsection = false, $bflags = false ){
        $arIblockTypes = self::GetIBlockTypes(
            $lid,
            $catalogOnly,
            $hideOffers
        );

        foreach( $arIblockTypes as $iblockTypeIndex => $arIblockTypeData ){
            foreach( $arIblockTypeData["IBLOCK"] as $iblockIndex => $iblockName ){
                $arIblockTypes[$iblockTypeIndex]["IBLOCK"][$iblockIndex] = array();
                $arIblockTypes[$iblockTypeIndex]["IBLOCK"][$iblockIndex]["NAME"] = $iblockName;
                $arIblockTypes[$iblockTypeIndex]["IBLOCK"][$iblockIndex]["SECTIONS"] = self::GetSections( array( $iblockIndex ), $subsection, $bflags );
            }
        }

        return $arIblockTypes;
    }

    public function GetProfileData(){
        return "";
    }

    function GetTypes(){
        $typeFiles = scandir( __DIR__.$this->typeLocation );
        $typeFiles = CGooglemerchantProfile::PathFilter( $typeFiles );
        foreach( $typeFiles as $file ){
            require( __DIR__.$this->typeLocation."/".$file );
        }

        return $profileTypes;
    }

    function GetIBlockFields(){
        @CModule::IncludeModule( "iblock" );
        $fields = CIBlock::GetFieldsDefaults();

        return $fields;
    }

    function GetSiteEnconding( $arProfile ){
        $sCharset = "windows-1251";
        if( is_array( $arProfile ) && !empty( $arProfile ) ){
            $dbSite = CSite::GetList(
                $by = "sort",
                $order = "asc",
                array(
                    "ACTIVE" => "Y",
                    "DOMAIN" => "%".$arProfile["DOMAIN_NAME"],
                )
            );

            $arProcessSite = false;
            if( $arSite = $dbSite->Fetch() ){
                $sCharset = $arSite["CHARSET"];
            }
        }

        return $sCharset;
    }

    function GetDefaults( $profileCode = false ){
        $type = $this->GetTypes();

        $firstType = false;
        foreach( $type as $code => $value ){
            if( $profileCode ){
                if( $code == $profileCode ){
                    $firstType = $value;
                }
            }
            else{
                if( !$firstType ){
                    $firstType = $value;
                }
                if( $code == "ym_simple" ){
                    $firstType = $value;
                }
            }
            $value["CODE"] = $code;
        }
        $type = $firstType;

        $ex = explode( "www.", $_SERVER["SERVER_NAME"] );

        $dbSite = CSite::GetList(
            $by = "sort",
            $order = "asc",
            array(
                "ACTIVE" => "Y",
                "DOMAIN" => "%".( ( $ex[1] ) ? $ex[1] : $ex[0] ),
            )
        );

        $arProcessSite = false;
        if( $arSite = $dbSite->Fetch() ){
            $arProcessSite = $arSite;
        }

        $arCatalogs = array();
        if( CModule::IncludeModule( "catalog" ) ){
            $dbCatalog = CCatalog::GetList(
                array(),
                array(),
                false,
                false,
                array()
            );

            if( $dbCatalog->SelectedRowsCount() ){
                $arCatalogs["IBLOCK_TYPE"] = array();
                $arCatalogs["IBLOCK"] = array();

                while( $arCatalog = $dbCatalog->Fetch() ){
                    $arCatalogs["IBLOCK_TYPE"][] = $arCatalog["IBLOCK_TYPE_ID"];
                    $arCatalogs["IBLOCK"][] = $arCatalog["IBLOCK_ID"];
                }

                $arCatalogs["IBLOCK_TYPE"] = array_unique( $arCatalogs["IBLOCK_TYPE"] );
                sort( $arCatalogs["IBLOCK_TYPE"] );

                $arCatalogs["IBLOCK"] = array_unique( $arCatalogs["IBLOCK"] );
                sort( $arCatalogs["IBLOCK"] );

                $dbCatalogSection = CIBlockSection::GetList(
                    array(),
                    array(
                        "IBLOCK_ID" => $arCatalogs["IBLOCK"]
                    ),
                    false
                );


                if( $dbCatalogSection->SelectedRowsCount() ){
                    $arCatalogs["SECTIONS"] = array();

                    while( $arCatalogSection = $dbCatalogSection->Fetch() ){
                        $arCatalogs["SECTIONS"][] = $arCatalogSection["ID"];
                    }

                    $arCatalogs["SECTIONS"] = array_unique( $arCatalogs["SECTIONS"] );
                    sort( $arCatalogs["SECTIONS"] );
                }
            }

        }

        $arDefaultData = array(
            "ACTIVE"                  => "Y",
            "TYPE"                    => $type["CODE"],
            "VIEW_CATALOG"            => "Y",
            "USE_SKU"                 => "Y",
            "LID"                     => $arProcessSite["LID"],
            "FORMAT"                  => $type["FORMAT"],
            "OFFER_TEMPLATE"          => $type["ITEMS_FORMAT"],
            "XMLDATA"                 => $type["FIELDS"],
            "CURRENCY_TEMPLATE"       => $type["CURRENCIES"],
            "CATEGORY_TEMPLATE"       => $type["SECTIONS"],
            "CATEGORY_INNER_TEMPLATE" => $type["SECTIONS_INNER"],
            "DATEFORMAT"              => $type["DATEFORMAT"],
            "ENCODING"                => !$type["ENCODING"] ? "cp1251" : $type["ENCODING"],
            "DOMAIN_NAME"             => $arProcessSite["SERVER_NAME"],
            "SITE_NAME"               => $arProcessSite["SITE_NAME"],
            "DESCRIPTION"             => $arProcessSite["NAME"],
        );

        if( is_array( $arCatalogs ) && !empty( $arCatalogs ) ){
            $arDefaultData["IBLOCK_TYPE_ID"] = $arCatalogs["IBLOCK_TYPE"];
            $arDefaultData["IBLOCK_ID"] = $arCatalogs["IBLOCK"];
            if( is_array( $arCatalogs["SECTIONS"] ) && !empty( $arCatalogs["SECTIONS"] ) ){
                $arDefaultData["CATEGORY"] = $arCatalogs["SECTIONS"];
            }
        }

        return $arDefaultData;
    }

    private function createFieldsetRecursive( $ibFields ){
        $options = array();
        foreach( $ibFields as $field ){
            if( isset( $field["children"] ) ){
                $options[] = "<optgroup label=\"{$field["label"]}\">";
                $options_ext = $this->createFieldsetRecursive( $field["children"] );
                $options = array_merge( $options, $options_ext );
                $options[] = "</optgroup>";
            }
            else{
                if( empty( $field["label"] ) )
                    continue;

                $options[$field["controlId"]] = "<option value=\"{$field["controlId"]}\" #SELECTED#>{$field["label"]}</option>";
            }
        }

        return $options;
    }

    function createFieldset(){
        $ibFields = array( CAcritGooglemerchantCondCtrlIBlockFields::GetControlShow() );
        $ibProps = CAcritGooglemerchantCondCtrlIBlockProps::GetControlShow();
        $ibFields = array_merge( $ibFields, $ibProps );
        $options = $this->createFieldsetRecursive( $ibFields );

        return $options;
    }

    function selectFieldset( $options, $value ){
        foreach( $options as $id => &$opt ){
            $selected = $value == $id ? 'selected="selected"' : "";
            $opt = str_replace( "#SELECTED#", $selected, $opt );
        }

        return $options;
    }

    public static function getFieldsetResolve( $IBLOCK, $options, $value ){
        if( !is_array( $IBLOCK ) ){
            $k = $IBLOCK;
            $IBLOCK = array();
            $IBLOCK[] = $k;
        }

        foreach( $IBLOCK as $iblockId ){
            if( isset( $options[$iblockId]["RESOLVE"]["PROPERTY"][$value] ) ){
                return $options[$iblockId]["RESOLVE"]["PROPERTY"][$value];
            }
            elseif( isset( $options[$iblockId]["RESOLVE"]["FIELDS"][$value] ) ){
                return $options[$iblockId]["RESOLVE"]["FIELDS"][$value];
            }
            else{
                return false;
            }
        }
    }

    function createFieldsetResolve( $PROPERTY, $TYPE = "" ){
        if( ( $PROPERTY["PROPERTY_TYPE"] == "S" ) && $PROPERTY["USER_TYPE"] == "UserID" ){
            global $USER;
            $ID = $USER->GetID();
            $rsUser = CUser::GetByID( $ID );
            $arUser = $rsUser->Fetch();
            $arProps = array();
            //UF
            $arUserFields = array();
            $dbSectionUserFields = CUserTypeEntity::GetList(
                array(),
                array(
                    "ENTITY_ID" => "USER",
                    "LANG"      => LANGUAGE_ID
                )
            );

            while( $arSectionUserFields = $dbSectionUserFields->Fetch() ){
                $arUserFields[$arSectionUserFields["FIELD_NAME"]] = $arSectionUserFields;
            }
            // end UF

            if( count( $arUser ) ){
                foreach( array( "NAME", "SECOND_NAME", "LAST_NAME", "EMAIL" ) as $value ){
                    if( !strlen( $name = GetMessage( "ACRIT_GOOGLEMERCHANT_".$value ) ) )
                        $name = $value;

                    $arProps["LINK_S_USERID"]["PROPERTY"]["VALUES"][] = array( "NAME" => $name, "CODE" => $value );
                }
            }

            foreach( $arUser as $propName => $val ){
                if( strncmp( $propName, "PERSONAL_", strlen( "PERSONAL_" ) ) == 0 ){
                    if( !strlen( $name = GetMessage( "ACRIT_GOOGLEMERCHANT_".$propName ) ) )
                        $name = $propName;

                    $arProps["LINK_S_USERID"]["PERSONAL"]["VALUES"][] = array( "NAME" => $name, "CODE" => $propName );
                }
                elseif( strncmp( $propName, "WORK_", strlen( "WORK_" ) ) == 0 ){
                    if( !strlen( $name = GetMessage( "ACRIT_GOOGLEMERCHANT_".$propName ) ) )
                        $name = $propName;

                    $arProps["LINK_S_USERID"]["WORK"]["VALUES"][] = array( "NAME" => $name, "CODE" => $propName );
                }
                elseif( strncmp( $propName, "UF_", strlen( "UF_" ) ) == 0 ){
                    $userField = $arUserFields[$propName];
                    $userFieldTitle = ( strlen( $userField["EDIT_FORM_LABEL"] ) > 0 ) ? $userField["FIELD_NAME"].": ".$userField["EDIT_FORM_LABEL"] : $userField["FIELD_NAME"];

                    $arProps["LINK_S_USERID"]["UF"]["VALUES"][] = array( "NAME" => $userFieldTitle, "CODE" => $propName );
                }
            }

            foreach( $arProps["LINK_S_USERID"] as $n => $values ){
                if( count( $values ) ){
                    if( !strlen( $name = GetMessage( "ACRIT_GOOGLEMERCHANT_".$n ) ) ) $name = $n;
                    $arProps["LINK_S_USERID"][$n]["NAME"] = $name;
                }
            }

            if( count( $arProps ) ){
                return array( 0 => $arProps );
            }
        }

        return false;
    }

    function createFieldsetResolve1( $PROPERTY, $TYPE = "" ){
        global $USER;
        switch( $PROPERTY["PROPERTY_TYPE"] ){
            case "S":
                if( $PROPERTY["USER_TYPE"] == "UserID" ){
                    $ID = $USER->GetID();
                    $rsUser = CUser::GetByID( $ID );
                    $arUser = $rsUser->Fetch();
                    $arProps = array();
                    foreach( $arUser as $propName => $val ){
                        if( strncmp( $propName, "PERSONAL_", $len = strlen( "PERSONAL_" ) ) == 0 ){
                            $arProps["LINK_S_USERID_PERSONAL"][] = array( "NAME" => $propName, "CODE" => $propName );
                        }
                        if( strpos( $propName, "WORK_" ) !== false ){
                            $arProps["LINK_S_USERID_WORK"][] = array( "NAME" => $propName, "CODE" => $propName );
                        }
                    }

                    if( count( $arProps ) > 0 ){
                        foreach( array( "NAME", "SECOND_NAME", "LAST_NAME", "EMAIL" ) as $v ){
                            $arProps["LINK_S_USERID"][] = array( "NAME" => $v, "CODE" => $v );
                        }

                        return array( 0 => $arProps );
                    }
                }
                break;
        }

        return false;
    }

    function createFieldset2( $IBLOCK, $SKU = false, $PRICE = false ){
        $filedNames = CIBlock::GetFieldsDefaults();
        $Properties = array(
            "ID"                  => GetMessage( "ACRIT_GOOGLEMERCHANT_IDENTIFIKACIONNYY_NO" ),
            "EXTERNAL_ID"         => GetMessage( "ACRIT_GOOGLEMERCHANT_IDENTIFIKACIONNYY_EXTERNAL_ID" ),
            "NAME"                => GetMessage( "ACRIT_GOOGLEMERCHANT_NAIMENOVANIE" ),
            "CODE"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SIMVOLQNYY_KOD" ),
            "ACTIVE"              => GetMessage( "ACRIT_GOOGLEMERCHANT_AKTIVNOSTQ" ),
            "DETAIL_PAGE_URL"     => GetMessage( "ACRIT_GOOGLEMERCHANT_SSYLKA_NA_DETALQNUU" ),
            "DATE_ACTIVE_FROM"    => GetMessage( "ACRIT_GOOGLEMERCHANT_DATA_NACALA_AKTIVNOS" ),
            "DATE_ACTIVE_TO"      => GetMessage( "ACRIT_GOOGLEMERCHANT_DATA_OKONCANIA_AKTIV" ),
            "PREVIEW_TEXT"        => GetMessage( "ACRIT_GOOGLEMERCHANT_TEKST_ANONSA" ),
            "PREVIEW_PICTURE"     => GetMessage( "ACRIT_GOOGLEMERCHANT_IZOBRAJENIE_ANONSA" ),
            "DETAIL_TEXT"         => GetMessage( "ACRIT_GOOGLEMERCHANT_DETALQNOE_OPISANIE" ),
            "DETAIL_PICTURE"      => GetMessage( "ACRIT_GOOGLEMERCHANT_DETALQNOE_IZOBRAJENI" ),
            "IBLOCK_ID"           => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_IBLOCK_ID" ),
            "IBLOCK_CODE"         => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_IBLOCK_CODE" ),
            "IBLOCK_SECTION_ID"   => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_IBLOCK_SECTION_ID" ),
            "IBLOCK_SECTION_NAME" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_IBLOCK_SECTION_NAME" ),
            "SECTION.EXTERNAL_ID" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_SECTION.EXTERNAL_ID" ),
            "SECTION.DESCRIPTION" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_SECTION.DESCRIPTION" ),
            "SECTION.PICTURE" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_SECTION.PICTURE" ),
            "SECTION.DETAIL_PICTURE" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_SECTION.DETAIL_PICTURE" ),
            "CREATED_BY"          => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_CREATED_BY" ),
            "CREATED_USER_NAME"   => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_CREATED_USER_NAME" ),
            "TIMESTAMP_X"         => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_TIMESTAMP_X" ),
            "MODIFIED_BY"         => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_MODIFIED_BY" ),
            "USER_NAME"           => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_USER_NAME" ),
        );

        if( !is_array( $IBLOCK ) ){
            $k = $IBLOCK;
            $IBLOCK = array();
            $IBLOCK[] = $k;
        }

        foreach( $IBLOCK as $iblockId ){
            $dbSectionUserFields = CUserTypeEntity::GetList(
                array(),
                array(
                    "ENTITY_ID" => "IBLOCK_".$iblockId."_SECTION",
                    "LANG"      => LANGUAGE_ID
                )
            );

            while( $arSectionUserFields = $dbSectionUserFields->Fetch() ){
                if( !array_key_exists( $arSectionUserFields["FIELD_NAME"], $Properties ) ){
                    $userFieldTitle = ( strlen( $arSectionUserFields["EDIT_FORM_LABEL"] ) > 0 ) ? $arSectionUserFields["FIELD_NAME"].": ".$arSectionUserFields["EDIT_FORM_LABEL"] : $arSectionUserFields["FIELD_NAME"];
                    $Properties[$arSectionUserFields["FIELD_NAME"]] = $userFieldTitle;

                    if( $resolve = $this->createFieldsetResolve( $arSectionUserFields, "FIELDS" ) )
                        $arResolve[$arSectionUserFields["FIELD_NAME"]] = $resolve;
                }
            }
        }

        if( is_array( $IBLOCK ) && sizeof( $IBLOCK ) > 0 ){
            foreach( $IBLOCK as $iblock ){
                $arIBlock[$iblock]["FIELDS"] = $Properties;
                $arIBlock[$iblock]["RESOLVE"]["FIELDS"] = $arResolve;
                $res = CIBlock::GetByID( $iblock );
                if( $ar_res = $res->GetNext() )
                    $arIBlock[$iblock]["NAME"] = $ar_res["NAME"];

                $intIBlockID = $iblock;
                if( ( $SKU == true ) && CModule::IncludeModule( "catalog" ) ){
                    $arOffers = CCatalogSKU::GetInfoByProductIBlock( $intIBlockID );
                    if( !empty( $arOffers["IBLOCK_ID"] ) ){
                        $intOfferIBlockID = $arOffers["IBLOCK_ID"];
                        $strPerm = "D";
                        $rsOfferIBlocks = CIBlock::GetByID( $intOfferIBlockID );
                        if( $arOfferIBlock = $rsOfferIBlocks->Fetch() ){
                            $bBadBlock = !CIBlockRights::UserHasRightTo( $intOfferIBlockID, $intOfferIBlockID, "iblock_admin_display" );
                            if( $bBadBlock ){
                                echo GetMessage( "ERR_NO_ACCESS_IBLOCK_SKU" );
                            }
                        }
                        $boolOffers = true;
                    }
                    else{
                        $boolOffers = false;
                    }
                }
                else{
                    $boolOffers = false;
                }

                // get iblock properties
                $dbProps = CIBlockProperty::GetList(
                    array(
                        "name" => "asc"
                    ),
                    array(
                        "IBLOCK_ID" => $intIBlockID,
                        "ACTIVE"    => "Y"
                    )
                );

                $arIBlock[$iblock]["PROPERTY"] = array();
                $arIBlock[$iblock]["HL"] = array();
                $arIBlock[$iblock]["OFFERS_PROPERTY"] = array();

                $arProptss = array();

                while( $arProp = $dbProps->Fetch() ){
                    $arIBlock[$iblock]["PROPERTY"][$arProp["ID"]] = $arProp;
                    if( $resolve = $this->createFieldsetResolve( $arProp, "PROPERTY" ) )
                        $arIBlock[$iblock]["RESOLVE"]["PROPERTY"][$iblock."-PROPERTY-".$arProp["ID"]] = $resolve;

                    if( ( $arProp["PROPERTY_TYPE"] == "S" ) && ( $arProp["USER_TYPE"] == "directory" ) ){
                        if( isset( $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"] ) && ( strlen( $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"] ) > 0 ) ){
                            $arHLBlock = HighloadBlockTable::getList(
                                array(
                                    "filter" => array(
                                        "TABLE_NAME" => $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]
                                    )
                                )
                            )->fetch();

                            $dbEntityHLFields = CUserTypeEntity::GetList(
                                array(
                                    "ID" => "ASC"
                                ),
                                array(
                                    "ENTITY_ID" => "HLBLOCK_".$arHLBlock["ID"],
                                    "LANG" => LANGUAGE_ID
                                )
                            );

                            while( $arEntityHLFieldsRow = $dbEntityHLFields->Fetch() ){
                                if( strlen( trim( $arEntityHLFieldsRow["EDIT_FORM_LABEL"] ) ) > 0 ){
                                    $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".trim( $arEntityHLFieldsRow["EDIT_FORM_LABEL"] );
                                }
                                elseif( strlen( trim( $arEntityHLFieldsRow["LIST_COLUMN_LABEL"] ) ) > 0 ){
                                    $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".trim( $arEntityHLFieldsRow["LIST_COLUMN_LABEL"] );
                                }
                                elseif( strlen( trim( $arEntityHLFieldsRow["LIST_FILTER_LABEL"] ) ) > 0 ){
                                    $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".trim( $arEntityHLFieldsRow["LIST_FILTER_LABEL"] );
                                }
                                else{
                                    $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".$arEntityHLFieldsRow["FIELD_NAME"];
                                }
                                $arEntityHLFieldsRow["HL"] = "Y";
                                $arEntityHLFieldsRow["HL_PROP"] = $arProp["ID"];
                                $arEntityHLFieldsRow["HLBLOCK_ID"] = $arHLBlock["ID"];

                                $arIBlock[$iblock]["PROPERTY"][$arEntityHLFieldsRow["HLBLOCK_ID"]."_".$arEntityHLFieldsRow["FIELD_NAME"]] = $arEntityHLFieldsRow;
                            }
                        }
                    }
                }

                if( $boolOffers ){
                    $rsProps = CIBlockProperty::GetList(
                        array(
                            "SORT" => "ASC"
                        ),
                        array(
                            "IBLOCK_ID" => $intOfferIBlockID,
                            "ACTIVE"    => "Y"
                        )
                    );

                    while( $arProp = $rsProps->Fetch() ){
                        $arIBlock[$iblock]["OFFERS_PROPERTY"][$arProp["ID"]] = $arProp;
                        if( is_array( $arSelectedPropTypes ) && in_array( $arProp["PROPERTY_TYPE"], $arSelectedPropTypes ) ){
                            $arSelectOfferProps[] = $arProp["ID"];
                        }

                        if( $arProp["PROPERTY_TYPE"] == "L" ){
                            $arProp["VALUES"] = array();
                            $rsPropEnums = CIBlockProperty::GetPropertyEnum(
                                $arProp["ID"],
                                array(
                                    "sort" => "asc"
                                ),
                                array(
                                    "IBLOCK_ID" => $intOfferIBlockID
                                )
                            );
                            while( $arPropEnum = $rsPropEnums->Fetch() ){
                                $arProp["VALUES"][$arPropEnum["ID"]] = $arPropEnum["VALUE"];
                            }
                        }
                        elseif( ( $arProp["PROPERTY_TYPE"] == "S" ) && ( $arProp["USER_TYPE"] == "directory" ) ){
                            if( isset( $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"] ) && ( strlen( $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"] ) > 0 ) ){
                                $arHLBlock = HighloadBlockTable::getList(
                                    array(
                                        "filter" => array(
                                            "TABLE_NAME" => $arProp["USER_TYPE_SETTINGS"]["TABLE_NAME"]
                                        )
                                    )
                                )->fetch();

                                $dbEntityHLFields = CUserTypeEntity::GetList(
                                    array(
                                        "ID" => "ASC"
                                    ),
                                    array(
                                        "ENTITY_ID" => "HLBLOCK_".$arHLBlock["ID"],
                                        "LANG" => LANGUAGE_ID
                                    )
                                );

                                while( $arEntityHLFieldsRow = $dbEntityHLFields->Fetch() ){
                                    if( strlen( trim( $arEntityHLFieldsRow["EDIT_FORM_LABEL"] ) ) > 0 ){
                                        $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".trim( $arEntityHLFieldsRow["EDIT_FORM_LABEL"] );
                                    }
                                    elseif( strlen( trim( $arEntityHLFieldsRow["LIST_COLUMN_LABEL"] ) ) > 0 ){
                                        $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".trim( $arEntityHLFieldsRow["LIST_COLUMN_LABEL"] );
                                    }
                                    elseif( strlen( trim( $arEntityHLFieldsRow["LIST_FILTER_LABEL"] ) ) > 0 ){
                                        $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".trim( $arEntityHLFieldsRow["LIST_FILTER_LABEL"] );
                                    }
                                    else{
                                        $arEntityHLFieldsRow["NAME"] = "HL: ".$arProp["NAME"].".".$arEntityHLFieldsRow["FIELD_NAME"];
                                    }
                                    $arEntityHLFieldsRow["HL"] = "Y";
                                    $arEntityHLFieldsRow["HL_PROP"] = $arProp["ID"];
                                    $arEntityHLFieldsRow["HLBLOCK_ID"] = $arHLBlock["ID"];

                                    $arIBlock[$iblock]["OFFERS_PROPERTY"][$arEntityHLFieldsRow["HLBLOCK_ID"]."_".$arEntityHLFieldsRow["FIELD_NAME"]] = $arEntityHLFieldsRow;
                                }
                            }
                        }
                    }
                }
            }
        }

        if( @CModule::IncludeModule( "catalog" ) ){
            $arIBlock["CATALOG"] = array();
            
            $dbProfile = new CGooglemerchantProfileDB();
            $arCatalogProductFields = $dbProfile->GetCatalogProductFields();
            $idKey = array_search( "ID", $arCatalogProductFields );
            
            if( $idKey !== false ){
                unset( $arCatalogProductFields[$idKey] );
            }
            
            foreach( $arCatalogProductFields as $catalogProductField ){
                $arIBlock["CATALOG"][$catalogProductField] = GetMessage( "ACRIT_GOOGLEMERCHANT_".$catalogProductField."_FIELD" );
            }
            
            /*$arIBlock["CATALOG"]["QUANTITY"] = GetMessage( "ACRIT_GOOGLEMERCHANT_KOLICESTVO" );
            $arIBlock["CATALOG"]["QUANTITY_RESERVED"] = GetMessage( "ACRIT_GOOGLEMERCHANT_QUANTITY_RESERVED" );
            $arIBlock["CATALOG"]["WEIGHT"] = GetMessage( "ACRIT_GOOGLEMERCHANT_WEIGHT" );
            $arIBlock["CATALOG"]["WIDTH"] = GetMessage( "ACRIT_GOOGLEMERCHANT_WIDTH" );
            $arIBlock["CATALOG"]["LENGTH"] = GetMessage( "ACRIT_GOOGLEMERCHANT_LENGTH" );
            $arIBlock["CATALOG"]["HEIGHT"] = GetMessage( "ACRIT_GOOGLEMERCHANT_HEIGHT" );
						*/
            $arIBlock["CATALOG"]["BARCODE"] = GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_BARCODE" );
						/*
            $arIBlock["CATALOG"]["PURCHASING_PRICE"] = GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PURCHASING_PRICE" );
            $arIBlock["PRICES"]["PURCHASING_PRICE"] = GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PURCHASING_PRICE" );*/

            $p = GetCatalogGroups( ( $b = "SORT" ), ( $o = "ASC" ) );
            while( $p1 = $p->Fetch() ){
                $arIBlock["CATALOG"]["PRICE_".$p1["ID"]] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PRICE" ).")";
                $arIBlock["CATALOG"]["PRICE_".$p1["ID"]."_WD"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PRICE_WITH_DISCOUNT" ).")";
                $arIBlock["CATALOG"]["PRICE_".$p1["ID"]."_D"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PRICE_DISCOUNT" ).")";
                $arIBlock["CATALOG"]["PRICE_".$p1["ID"]."_CURRENCY"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_CURRNECY" ).")";
                $arIBlock["PRICES"]["PRICE_".$p1["ID"]] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PRICE" ).")";
                $arIBlock["PRICES"]["PRICE_".$p1["ID"]."_WD"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PRICE_WITH_DISCOUNT" ).")";
                $arIBlock["PRICES"]["PRICE_".$p1["ID"]."_D"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_PRICE_DISCOUNT" ).")";
                $arIBlock["PRICES"]["PRICE_".$p1["ID"]."_CURRENCY"] = $p1["NAME_LANG"]." (".GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_CURRNECY" ).")";
            }
        }

        return $arIBlock;
    }

    function selectFieldset2( $arIBlock, $value, &$selectedPropertyType = null ){
        $options = array();
        $fieldsSetup = false;
        if( is_array( $arIBlock ) ){
            foreach( $arIBlock as $IB_ID => $arFields ){
                if( is_array( $arFields["FIELDS"] ) && ( sizeof( $arFields["FIELDS"] ) > 0 ) && !$fieldsSetup ){
                    $options[] = '<optgroup label="'.GetMessage( "ACRIT_GOOGLEMERCHANT_POLA" ).'">';
                    foreach( $arFields["FIELDS"] as $idf => $fields ){
                        $selected = $idf == $value ? 'selected="selected"' : '';
                        $options[] = "<option value=\"$idf\" $selected>$fields</option>";
                    }
                    $options[] = "</optgroup>";
                    $fieldsSetup = true;
                }
                if( is_array( $arFields["PROPERTY"] ) && sizeof( $arFields["PROPERTY"] ) > 0 ){
                    $options[] = '<optgroup label="'.GetMessage( "ACRIT_GOOGLEMERCHANT_SVOYSTVA" ).$arFields["NAME"].'">';
                    foreach( $arFields["PROPERTY"] as $fields ){
                        $selected = $IB_ID."-PROPERTY-".$fields["ID"] == $value ? 'selected="selected"' : "";
                        $options[] = "<option value=\"$IB_ID-PROPERTY-{$fields["ID"]}\" $selected>{$fields["NAME"]} [{$fields["ID"]}]</option>";
                    }
                    $options[] = "</optgroup>";
                }
                if( is_array( $arFields["OFFERS_PROPERTY"] ) && sizeof( $arFields["OFFERS_PROPERTY"] ) > 0 ){
                    $options[] = '<optgroup label="'.GetMessage( "ACRIT_GOOGLEMERCHANT_SVOYSTVA1" ).$arFields["NAME"].'">';
                    foreach( $arFields["OFFERS_PROPERTY"] as $fields ){
                        $selected = $IB_ID."-PROPERTY-".$fields["ID"] == $value ? 'selected="selected"' : "";
                        $options[] = "<option value=\"$IB_ID-PROPERTY-{$fields["ID"]}\" $selected>{$fields["NAME"]} [{$fields["ID"]}]</option>";
                    }
                    $options[] = "</optgroup>";
                }
                if( is_array( $arFields["LINK_S_USERID"] ) && sizeof( $arFields["LINK_S_USERID"] ) > 0 ){
                    $options[] = "<option>".GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" )."</option>";

                    foreach( $arFields["LINK_S_USERID"] as $group ){
                        $options[] = '<optgroup label="'.$group["NAME"].'">';
                        foreach( $group["VALUES"] as $fields ){
                            $selected = $fields["CODE"] == $value ? 'selected="selected"' : "";
                            $options[] = "<option value=\"{$fields["CODE"]}\" $selected>{$fields["NAME"]}</option>";
                        }
                        $options[] = "</optgroup>";
                    }
                }

                $ibd = $IB_ID;
            }
            if( is_array( $arIBlock["CATALOG"] ) && sizeof( $arIBlock["CATALOG"] ) > 0 ){
                $options[] = '<optgroup label="'.GetMessage( "ACRIT_GOOGLEMERCHANT_SVOYSTVA_TORGOVOGO_K" ).$arFields["NAME"].'">';
                foreach( $arIBlock["CATALOG"] as $idf => $fields ){
                    $selected = "CATALOG-".$idf == $value ? 'selected="selected"' : "";
                    $dataProperty = ( isset( $arIBlock["PRICES"] ) && is_array( $arIBlock["PRICES"] ) && array_key_exists( $idf, $arIBlock["PRICES"] ) ) ? 'data-property="price"' : "";

                    if( $selected != "" && ( isset( $arIBlock["PRICES"] ) && is_array( $arIBlock["PRICES"] ) && array_key_exists( $idf, $arIBlock["PRICES"] ) ) ){
                        $selectedPropertyType = "price";
                    }

                    $options[] = "<option value=\"CATALOG-$idf\" $selected $dataProperty>$fields</option>";
                }
                $options[] = "</optgroup>";
            }
        }

        return $options;
    }

    public function createBlockFieldset( $arIBlock ){
        $options = array();

        $fieldsSetup = false;
        if( is_array( $arIBlock ) ){
            foreach( $arIBlock as $IB_ID => $arFields ){
                if( is_array( $arFields["FIELDS"] ) && ( sizeof( $arFields["FIELDS"] ) > 0 ) && !$fieldsSetup ){
                    $options["FIELDS"] = array( GetMessage( "ACRIT_GOOGLEMERCHANT_POLA" ) );
                    $optionsFields = array();
                    foreach( $arFields["FIELDS"] as $idf => $fields ){
                        $optionsFields[$idf] = $fields;
                    }
                    $options["FIELDS"][1] = $optionsFields;
                }

                if( is_array( $arFields["PROPERTY"] ) && sizeof( $arFields["PROPERTY"] ) > 0 ){
                    $options["PROPERTY"][$IB_ID] = array( GetMessage( "ACRIT_GOOGLEMERCHANT_SVOYSTVA" ).$arFields["NAME"] );
                    $optionsProperty = array();

                    foreach( $arFields["PROPERTY"] as $fields ){
                        if( isset( $fields["HL"] ) && ( $fields["HL"] == "Y" ) ){
                            $optionsProperty[$fields["HLBLOCK_ID"]."-PROPERTYHL-".$fields["ID"]."-".$fields["HL_PROP"]."-".$fields["FIELD_NAME"]] = $fields["NAME"]." [".$fields["FIELD_NAME"]."]";
                        }
                        else{
                            $optionsProperty[$IB_ID."-PROPERTY-".$fields["ID"]] = $fields["NAME"]." [".$fields["ID"]."]";
                        }
                    }
                    $options["PROPERTY"][$IB_ID][1] = $optionsProperty;
                }

                if( is_array( $arFields["OFFERS_PROPERTY"] ) && sizeof( $arFields["OFFERS_PROPERTY"] ) > 0 ){
                    $options["OFFERS_PROPERTY"] = array( GetMessage( "ACRIT_GOOGLEMERCHANT_SVOYSTVA1" ).$arFields["NAME"] );
                    $optionsOffersProperty = array();
                    foreach( $arFields["OFFERS_PROPERTY"] as $fields ){
                        if( isset( $fields["HL"] ) && ( $fields["HL"] == "Y" ) ){
                            $optionsOffersProperty[$fields["HLBLOCK_ID"]."-PROPERTYHL-".$fields["ID"]."-".$fields["HL_PROP"]."-".$fields["FIELD_NAME"]] = $fields["NAME"]." [".$fields["FIELD_NAME"]."]";
                        }
                        else{
                            $optionsOffersProperty[$fields["IBLOCK_ID"]."-PROPERTY-".$fields["ID"]] = $fields["NAME"]." [".$fields["ID"]."]";
                        }
                    }
                    $options["OFFERS_PROPERTY"][1] = $optionsOffersProperty;
                }

            }
            if( is_array( $arIBlock["CATALOG"] ) && sizeof( $arIBlock["CATALOG"] ) > 0 ){
                $options["CATALOG"] = array( GetMessage( "ACRIT_GOOGLEMERCHANT_SVOYSTVA_TORGOVOGO_K" ).$arFields["NAME"] );
                $optionsCatalog = array();
                foreach( $arIBlock["CATALOG"] as $idf => $fields ){
                    $optionsCatalog["CATALOG-".$idf] = $fields;
                }
                $options["CATALOG"][1] = $optionsCatalog;
            }
        }

        return $options;
    }

    public function createLinearFieldset( $arIBlock ){
        $options = array();

        $fieldsSetup = false;
        if( is_array( $arIBlock ) ){
            foreach( $arIBlock as $IB_ID => $arFields ){
                if( is_array( $arFields["FIELDS"] ) && ( sizeof( $arFields["FIELDS"] ) > 0 ) && !$fieldsSetup ){
                    foreach( $arFields["FIELDS"] as $idf => $fields ){
                        $options[$idf] = $fields;
                    }
                }

                if( is_array( $arFields["PROPERTY"] ) && sizeof( $arFields["PROPERTY"] ) > 0 ){
                    foreach( $arFields["PROPERTY"] as $fields ){
                        if( isset( $fields["HL"] ) && ( $fields["HL"] == "Y" ) ){
                            $options[$fields["HLBLOCK_ID"]."-PROPERTYHL-".$fields["ID"]."-".$fields["HL_PROP"]."-".$fields["FIELD_NAME"]] = $fields["NAME"]." [".$fields["FIELD_NAME"]."]";
                        }
                        else{
                            $options[$IB_ID."-PROPERTY-".$fields["ID"]] = $fields["NAME"]." [".$fields["ID"]."]";
                        }
                    }
                }

                if( is_array( $arFields["OFFERS_PROPERTY"] ) && sizeof( $arFields["OFFERS_PROPERTY"] ) > 0 ){
                    foreach( $arFields["OFFERS_PROPERTY"] as $fields ){
                        if( isset( $fields["HL"] ) && ( $fields["HL"] == "Y" ) ){
                            $options[$fields["HLBLOCK_ID"]."-PROPERTYHL-".$fields["ID"]."-".$fields["HL_PROP"]."-".$fields["FIELD_NAME"]] = $fields["NAME"]." [".$fields["FIELD_NAME"]."]";
                        }
                        else{
                            $options[$IB_ID."-PROPERTY-".$fields["ID"]] = $fields["NAME"]." [".$fields["ID"]."]";
                        }
                    }
                }
            }
            if( is_array( $arIBlock["CATALOG"] ) && sizeof( $arIBlock["CATALOG"] ) > 0 ){
                foreach( $arIBlock["CATALOG"] as $idf => $fields ){
                    $options["CATALOG-".$idf] = $fields;
                }
            }
        }

        return $options;
    }

    public function GetCurrencyRate(){
        $rates = array(
            "SITE" => GetMessage( "ACRIT_GOOGLEMERCHANT_CURRENCYRATE_SITE" ),
            "CBRF" => GetMessage( "ACRIT_GOOGLEMERCHANT_CURRENCYRATE_CBRF" ),
            "NBU"  => GetMessage( "ACRIT_GOOGLEMERCHANT_CURRENCYRATE_NBU" ),
            "NBK"  => GetMessage( "ACRIT_GOOGLEMERCHANT_CURRENCYRATE_NBK" ),
            "NBB"  => GetMessage( "ACRIT_GOOGLEMERCHANT_CURRENCYRATE_NBB" ),
        );

        return $rates;
    }

    public static function LoadCurrencyRates( $bank = array() ){
        $arResult = array();

        $DATA = array(
            "CBRF" => array(
                "URL"      => "www.cbr.ru",
                "LINK"     => "/scripts/XML_daily.asp",
                "PORT"     => "80",
                "ITEMS"    => array( "ValCurs", "#", "Valute" ),
                "CURRENCY" => array( "#", "CharCode", 0, "#" ),
                "RATE"     => array( "#", "Nominal", 0, "#" ),
                "VALUE"    => array( "#", "Value", 0, "#" )
            ),
            "NBU"  => array(
                "URL"      => "bank-ua.com",
                "LINK"     => "/export/currrate.xml",
                "PORT"     => "80",
                "ITEMS"    => array( "chapter", "#", "item" ),
                "CURRENCY" => array( "#", "char3", 0, "#" ),
                "RATE"     => array( "#", "size", 0, "#" ),
                "VALUE"    => array( "#", "rate", 0, "#" )
            ),
            "NBK"  => array(
                "URL"      => "www.nationalbank.kz",
                "LINK"     => "/rss/rates_all.xml",
                "PORT"     => "80",
                "ITEMS"    => array( "rss", "#", "channel", 0, "#", "item" ),
                "CURRENCY" => array( "#", "title", 0, "#" ),
                "RATE"     => array( "#", "quant", 0, "#" ),
                "VALUE"    => array( "#", "description", 0, "#" )
            ),
            "NBB"  => array(
                "URL"      => "www.nbrb.by",
                "LINK"     => "/Services/XmlExRates.aspx",
                "PORT"     => "80",
                "ITEMS"    => array( "DailyExRates", "#", "Currency" ),
                "CURRENCY" => array( "#", "CharCode", 0, "#" ),
                "RATE"     => array( "#", "Scale", 0, "#" ),
                "VALUE"    => array( "#", "Rate", 0, "#" )
            ),
        );

        if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/catalog_export/currencyRates.json" ) ){
            $rates = json_decode( file_get_contents( $_SERVER["DOCUMENT_ROOT"]."/bitrix/catalog_export/currencyRates.json" ) );
            $rates = get_object_vars( $rates );
            if( ( strtotime( date( "Y-m-d H:i:s" ) ) - $rates["time"] ) > 3600 ){
                if( sizeof( $bank ) <= 0 )
                    $bank = array_keys( $DATA );

                require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php" );

                foreach( $bank as $bnk ){
                    $strQueryText = QueryGetData(
                        $DATA[$bnk]["URL"],
                        $DATA[$bnk]["PORT"],
                        $DATA[$bnk]["LINK"],
                        "",
                        $error_number,
                        $error_text
                    );

                    if( strlen( $strQueryText ) > 0 ){
                        $objXML = new CDataXML();
                        $objXML->LoadString( $strQueryText );
                        $arData = $objXML->GetArray();
                        for( $i = 0; $i < sizeof( $DATA[$bnk]["ITEMS"] ); $i++ ){
                            $arData = $arData[$DATA[$bnk]["ITEMS"][$i]];
                        }

                        if( is_array( $arData ) && count( $arData ) > 0 ){
                            for( $j1 = 0; $j1 < count( $arData ); $j1++ ){
                                for( $i = 0; $i < sizeof( $DATA[$bnk]["VALUE"] ); $i++ ){
                                    if( $i == 0 ){
                                        $q1 = $arData[$j1][$DATA[$bnk]["VALUE"][$i]];
                                    }
                                    elseif( $i > 0 ){
                                        $q1 = $q1[$DATA[$bnk]["VALUE"][$i]];
                                    }
                                }
                                $arCurrValue = str_replace( ",", ".", $q1 );
                                for( $i = 0; $i < sizeof( $DATA[$bnk]["CURRENCY"] ); $i++ ){
                                    if( $i == 0 ){
                                        $currency1 = $arData[$j1][$DATA[$bnk]["CURRENCY"][$i]];
                                    }
                                    elseif( $i > 0 ){
                                        $currency1 = $currency1[$DATA[$bnk]["CURRENCY"][$i]];
                                    }
                                }
                                for( $i = 0; $i < sizeof( $DATA[$bnk]["RATE"] ); $i++ ){
                                    if( $i == 0 ){
                                        $rate = $arData[$j1][$DATA[$bnk]["RATE"][$i]];
                                    }
                                    elseif( $i > 0 ){
                                        $rate = $rate[$DATA[$bnk]["RATE"][$i]];
                                    }
                                }
                                $curr = DoubleVal( $arCurrValue );
                                if( sizeof( $currency ) > 0 ){
                                    if( in_array( $currency1, $currency ) )
                                        $arResult[$bnk][] = array(
                                            "RATE"     => round( $curr, 2 ),
                                            "RATE_CNT" => $rate,
                                            "CURRENCY" => $currency1,
                                        );
                                }
                                else{
                                    $arResult[$bnk][] = array(
                                        "RATE"     => round( $curr, 2 ),
                                        "RATE_CNT" => $rate,
                                        "CURRENCY" => $currency1,
                                    );
                                }
                            }
                        }
                    }
                }
                $arResultNew = array();
                foreach( $arResult as $rate => $value ){
                    foreach( $value as $currency ){
                        $arResultNew[$rate][$currency["CURRENCY"]] = $currency;
                    }
                }
                $arResult = $arResultNew;
                $arResult["CBRF"]["RUB"] = array(
                    "CURRENCY" => "RUB",
                    "RATE"     => 1,
                    "RATE_CNT" => 1
                );
                unset( $arResultNew );

                $put = array(
                    "time"  => strtotime( date( "Y-m-d H:i:s" ) ),
                    "rates" => $arResult
                );

                $f = fopen( $_SERVER["DOCUMENT_ROOT"]."/bitrix/catalog_export/currencyRates.json", "w+" );
                fputs( $f, json_encode( $put ) );
                fclose( $f );

                unset( $put );
            }
            else{
                foreach( $rates["rates"] as $bank => $currency ){
                    $currency = get_object_vars( $currency );
                    foreach( $currency as $currencyName => $currencyRates ){
                        $currencyRates = get_object_vars( $currencyRates );
                        $arResult[$bank][$currencyName] = $currencyRates;
                    }
                }
            }
            unset( $rates );
        }
        else{
            if( sizeof( $bank ) <= 0 )
                $bank = array_keys( $DATA );

            require_once( $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/xml.php" );

            foreach( $bank as $bnk ){
                $strQueryText = QueryGetData(
                    $DATA[$bnk]["URL"],
                    $DATA[$bnk]["PORT"],
                    $DATA[$bnk]["LINK"],
                    "",
                    $error_number,
                    $error_text
                );

                if( strlen( $strQueryText ) > 0 ){
                    $objXML = new CDataXML();
                    $objXML->LoadString( $strQueryText );
                    $arData = $objXML->GetArray();
                    for( $i = 0; $i < sizeof( $DATA[$bnk]["ITEMS"] ); $i++ ){
                        $arData = $arData[$DATA[$bnk]["ITEMS"][$i]];
                    }

                    if( is_array( $arData ) && count( $arData ) > 0 ){
                        for( $j1 = 0; $j1 < count( $arData ); $j1++ ){
                            for( $i = 0; $i < sizeof( $DATA[$bnk]["VALUE"] ); $i++ ){
                                if( $i == 0 ){
                                    $q1 = $arData[$j1][$DATA[$bnk]["VALUE"][$i]];
                                }
                                elseif( $i > 0 ){
                                    $q1 = $q1[$DATA[$bnk]["VALUE"][$i]];
                                }
                            }
                            $arCurrValue = str_replace( ",", ".", $q1 );
                            for( $i = 0; $i < sizeof( $DATA[$bnk]["CURRENCY"] ); $i++ ){
                                if( $i == 0 ){
                                    $currency1 = $arData[$j1][$DATA[$bnk]["CURRENCY"][$i]];
                                }
                                elseif( $i > 0 ){
                                    $currency1 = $currency1[$DATA[$bnk]["CURRENCY"][$i]];
                                }
                            }
                            for( $i = 0; $i < sizeof( $DATA[$bnk]["RATE"] ); $i++ ){
                                if( $i == 0 ){
                                    $rate = $arData[$j1][$DATA[$bnk]["RATE"][$i]];
                                }
                                elseif( $i > 0 ){
                                    $rate = $rate[$DATA[$bnk]["RATE"][$i]];
                                }
                            }
                            $curr = DoubleVal( $arCurrValue );
                            if( sizeof( $currency ) > 0 ){
                                if( in_array( $currency1, $currency ) )
                                    $arResult[$bnk][] = array(
                                        "RATE"     => round( $curr, 2 ),
                                        "RATE_CNT" => $rate,
                                        "CURRENCY" => $currency1,
                                    );
                            }
                            else{
                                $arResult[$bnk][] = array(
                                    "RATE"     => round( $curr, 2 ),
                                    "RATE_CNT" => $rate,
                                    "CURRENCY" => $currency1,
                                );
                            }
                        }
                    }
                }
            }
            $arResultNew = array();
            foreach( $arResult as $rate => $value ){
                foreach( $value as $currency ){
                    $arResultNew[$rate][$currency["CURRENCY"]] = $currency;
                }
            }
            $arResult = $arResultNew;
            $arResult["CBRF"]["RUB"] = array(
                "CURRENCY" => "RUB",
                "RATE"     => 1,
                "RATE_CNT" => 1
            );
            unset( $arResultNew );

            $put = array(
                "time"  => strtotime( date( "Y-m-d H:i:s" ) ),
                "rates" => $arResult
            );

            $f = fopen( $_SERVER["DOCUMENT_ROOT"]."/bitrix/catalog_export/currencyRates.json", "w+" );
            fputs( $f, json_encode( $put ) );
            fclose( $f );

            unset( $put );
        }

        return $arResult;
    }

    public function PrepareIBlock( $arIBlock = array(), $bUseSKU = fasle ){
        $excludeIBlock = array();
        if( !is_array( $arIBlock ) )
            $arIBlock = array();

        $catalogSKU = array();
        if( $bUseSKU ){
            foreach( $arIBlock as $iblocID ){
                if( $iblock = CCatalog::GetByID( $iblocID ) ){
                    if( intval( $iblock["OFFERS_IBLOCK_ID"] ) > 0 )
                        $catalogSKU[] = $iblock["OFFERS_IBLOCK_ID"];
                }
            }
        }

        $arIblocks = array_merge( $arIBlock, $catalogSKU );
        $arIblocks = array_unique( $arIblocks );

        return $arIblocks;
    }

    public function GetIblockShowType(){
        return array( "hierarchical", "tree", "checkbox" );
    }

    public function GetFileExportType(){
        return array( "xml", "csv" );
    }

    public function GetCronTaskMode(){
        return array( "php", "curl" );
    }

    public function GetRunType(){
        return array( "comp", "cron" );
    }

    function memoryUsage( $usage, $base_memory_usage = 0 ){
        printf( "Bytes diff: %d\n", $usage - $base_memory_usage );
    }

    public static function PathFilterHandler( $value ){
        if( $value == "." || $value == ".." )
            return false;

        return $value;
    }

    public static function PathFilter( $arPath ){
        if( !is_array( $arPath ) )
            return array();

        return array_filter( $arPath, array( self, "PathFilterHandler" ) );
    }

    public function GetSchemeName(){
        $schemes = array(
            "NAME_SKU"       => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_NAME" ),
            "NAME_OFFER"     => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_PRODUCT_NAME" ),
            "NAME_OFFER_SKU" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_PRODUCT_NAME" ),
        );

        return $schemes;
    }

    public function GetSchemePreviewText(){
        $schemes = array(
            "PREVIEW_TEXT_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_PREVIEW_OFFER_NAME" ),
            "PREVIEW_TEXT_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_PREVIEW_PRODUCT_NAME" ),
            "PREVIEW_TEXT_OFFER_SKU"          => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_PREVIEW_OFFER_PRODUCT_NAME" ),
            "PREVIEW_TEXT_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeDetailText(){
        $schemes = array(
            "DETAIL_TEXT_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_NAME" ),
            "DETAIL_TEXT_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_NAME" ),
            "DETAIL_TEXT_OFFER_SKU"          => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_PRODUCT_NAME" ),
            "DETAIL_TEXT_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemePreviewPicture(){
        $schemes = array(
            "PREVIEW_PICTURE_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_PREVIEW_PICTURE" ),
            "PREVIEW_PICTURE_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_PREVIEW_PICTURE" ),
            "PREVIEW_PICTURE_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeDetailPicture(){
        $schemes = array(
            "DETAIL_PICTURE_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_DETAIL_PICTURE" ),
            "DETAIL_PICTURE_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_DETAIL_PICTURE" ),
            "DETAIL_PICTURE_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeQuantity(){
        $schemes = array(
            "CATALOG_QUANTITY_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_QUANTITY" ),
            "CATALOG_QUANTITY_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_QUANTITY" ),
            "CATALOG_QUANTITY_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeQuantityReserved(){
        $schemes = array(
            "CATALOG_QUANTITY_RESERVED_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_QUANTITY_RESERVED" ),
            "CATALOG_QUANTITY_RESERVED_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_QUANTITY_RESERVED" ),
            "CATALOG_QUANTITY_RESERVED_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeWeight(){
        $schemes = array(
            "CATALOG_WEIGHT_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_WEIGHT" ),
            "CATALOG_WEIGHT_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_WEIGHT" ),
            "CATALOG_WEIGHT_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeWidth(){
        $schemes = array(
            "CATALOG_WIDTH_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_WIDTH" ),
            "CATALOG_WIDTH_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_WIDTH" ),
            "CATALOG_WIDTH_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeLength(){
        $schemes = array(
            "CATALOG_LENGTH_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_LENGTH" ),
            "CATALOG_LENGTH_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_LENGTH" ),
            "CATALOG_LENGTH_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemeHeight(){
        $schemes = array(
            "CATALOG_HEIGHT_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_HEIGHT" ),
            "CATALOG_HEIGHT_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_HEIGHT" ),
            "CATALOG_HEIGHT_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function GetSchemePurchasingPrice(){
        $schemes = array(
            "CATALOG_PURCHASING_PRICE_SKU"                => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_OFFER_PURCHASING_PRICE" ),
            "CATALOG_PURCHASING_PRICE_OFFER"              => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_DETAIL_PRODUCT_PURCHASING_PRICE" ),
            "CATALOG_PURCHASING_PRICE_OFFER_IF_SKU_EMPTY" => GetMessage( "ACRIT_GOOGLEMERCHANT_SCHEME_OFFER_IF_SKU_EMPTY" ),
        );

        return $schemes;
    }

    public function  GetDefaultSelected( $schemeValue, $arProfileValue ){
        $default = "OFFER_IF_SKU_EMPTY";
        if( empty( $arProfileValue ) ){
            if( substr_compare( $schemeValue, $default, "-".strlen( $default ) ) == 0 ){
                return $schemeValue;
            }
        }

        return $arProfileValue;
    }
}