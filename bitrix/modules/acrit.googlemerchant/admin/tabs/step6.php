<?php

IncludeModuleLangFile( __FILE__ );

$obMarketCategory = new CGooglemerchantMarketDB();
$marketCategory = $obMarketCategory->GetList();
if( !is_array( $marketCategory ) )
    $marketCategory = array();

$categoriesNew = array();
foreach( $categories as $depth ){
    $categoriesNew = array_merge( $categoriesNew, $depth );
}

$categories = $categoriesNew;
unset( $categoriesNew );

asort( $categories );

$bUseMarketCategory = $arProfile["USE_MARKET_CATEGORY"] == "Y" ? 'checked="checked"' : "";
$bChangeMarketCategory = $arProfile["CHANGE_MARKET_CATEGORY"] == "Y" ? 'checked="checked"' : "";
$bUseCategoryRedefineTag = $arProfile["SETUP"]["USE_CATEGORY_REDEFINE_TAG"] == "Y" ? 'checked="checked"' : "";

$marketCategoryFirstItem = reset( $marketCategory );
$marketCategoryLastItem = end( $marketCategory );

$iActualCategoryType = $marketCategoryFirstItem["id"];

$arMarketCategoryList = array();
foreach( $marketCategory as $marketCategoryItem ){
    $arMarketCategoryList[$marketCategoryItem["id"]] = $marketCategoryItem;
}
?>

<tr>
    <td width="40%" class="adm-detaell-l">
        <span id="hint_PROFILE[USE_MARKET_CATEGORY]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[USE_MARKET_CATEGORY]' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_USE_MARKETCATEGORY_HELP" )?>' );</script>
        <label for="PROFILE[USE_MARKET_CATEGORY]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_USE_MARKETCATEGORY" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[USE_MARKET_CATEGORY]" value="Y" <?=$bUseMarketCategory?> >
        <i><?=GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_USE_MARKETCATEGORY_DESC" )?></i>
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detaell-l">
        <span id="hint_PROFILE[CHANGE_MARKET_CATEGORY]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[CHANGE_MARKET_CATEGORY]' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_CHANGE_MARKETCATEGORY_HELP" )?>' );</script>
        <label for="PROFILE[CHANGE_MARKET_CATEGORY]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_CHANGE_MARKETCATEGORY" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[CHANGE_MARKET_CATEGORY]" value="Y" <?=$bChangeMarketCategory?> >
    </td>
</tr>
<tr>
    <td width="40%" class="adm-detaell-l">
        <span id="hint_PROFILE[SETUP][USE_CATEGORY_REDEFINE_TAG]"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_PROFILE[SETUP][USE_CATEGORY_REDEFINE_TAG]' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_USE_CATEGORY_REDEFINE_TAG_HELP" )?>' );</script>
        <label for="PROFILE[SETUP][USE_CATEGORY_REDEFINE_TAG]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_STEP1_USE_CATEGORY_REDEFINE_TAG" )?></label>
    </td>
    <td width="60%" class="adm-detail-content-cell-r">
        <input type="checkbox" name="PROFILE[SETUP][USE_CATEGORY_REDEFINE_TAG]" value="Y" <?=$bUseCategoryRedefineTag?> onclick="showCategoryRedefineTagField()" >
        <select name="PROFILE[SETUP][CATEGORY_REDEFINE_TAG]" id="select_category_redefine_tag" <?if( $bUseCategoryRedefineTag == "" ):?>style="display: none;"<?endif?>>
            <?foreach( $arProfile["XMLDATA"] as $id => $field ){?>
                <?$selected = ( $arProfile["SETUP"]["CATEGORY_REDEFINE_TAG"] && ( $field["CODE"] == $arProfile["SETUP"]["CATEGORY_REDEFINE_TAG"] ) ) ? 'selected="selected"' : ""?>
                <option value="<?=$field["CODE"]?>" <?=$selected?>><?=$field["CODE"]?></option>
            <?}?>
        </select>
    </td>
</tr>

<?$selectedMarketCategory = ( intval( $arProfile["MARKET_CATEGORY"]["CATEGORY"] ) > 0 ) ? $arProfile["MARKET_CATEGORY"]["CATEGORY"] : $iActualCategoryType;?>
<tr>
    <td id="market_category_select">
        <select name="PROFILE[MARKET_CATEGORY][CATEGORY]" onchange="ChangeMarketCategory( this.value )">
            <?foreach( $arMarketCategoryList as $catIndex => $cat ){?>
                <?$selected = ( $arProfile["MARKET_CATEGORY"]["CATEGORY"] && ( $cat["id"] == $arProfile["MARKET_CATEGORY"]["CATEGORY"] ) ) ? 'selected="selected"' : ( ( !$arProfile["MARKET_CATEGORY"]["CATEGORY"] && ( $catIndex == $iActualCategoryType ) ) ? 'selected="selected"' :  "" )?>
                <option value="<?=$cat["id"]?>" <?=$selected?>><?=$cat["name"]?></option>
            <?}?>
        </select>
    </td>
    <td>
        <a id="market_category_edit_btn" class="adm-btn" onclick="ShowMarketForm( 'edit' )"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_EDIT" )?></a>
        <a id="market_category_delete_btn" class="adm-btn adm-btn-red" onclick="DeleteMarketCategoryType( <?=$arProfile["ID"];?> )"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DELETE" )?></a>
        <a class="adm-btn adm-btn-save" onclick="ShowMarketForm( 'add' )"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_ADD" )?></a>
    </td>
</tr>
<tr>
    <td colspan="2">
        <div id="category_add">
            <input type="hidden" name="PROFILE[MARKET_CATEGORY_ID]" />
            <table>
                <tr>
                    <td><input type="text" name="PROFILE[MARKET_CATEGORY_NAME]" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_NAME" )?>"/></td>
                </tr>
                <tr>
                    <td><textarea name="PROFILE[MARKET_CATEGORY_DATA]" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA" )?>" size="20"></textarea></td>
                </tr>
                <tr>
                    <td>
                        <a class="adm-btn save adm-btn-save" onclick="SaveMarketForm()"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_SAVE" )?></a>
                        <a class="adm-btn back" onclick="HideMarketForm()"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_BACK" )?></a>
                    </td>
                </tr>
            </table>
            <br/><br/><br/>
        </div>
    </td>
</tr>

<?$arMarketCategoryList = explode( PHP_EOL, $arMarketCategoryList[$selectedMarketCategory]["data"] );

$validCategories = array();
foreach( $arMarketCategoryList as $market ){
    if( is_array( $arProfile["MARKET_CATEGORY"]["CATEGORY_LIST"] ) ){
        foreach( $arProfile["MARKET_CATEGORY"]["CATEGORY_LIST"] as $catId => $catValue ){
            if(  trim( $catValue ) == trim( $market ) )
                $validCategories[] = $catId;
        }
    }
}?>

<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DESCRIPTION" );?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2">
        <table width="100%" id="market_category_data">
            <?foreach( $categories as $cat ){
                if( $arProfile["CHECK_INCLUDE"] == "Y" ){
                    if( !in_array( $cat["ID"], $arProfile["CATEGORY"] ) )
                        continue;
                }
                else{
                    if( !in_array( $cat["PARENT_1"], $arProfile["CATEGORY"] ) )
                        continue;
                }?>
                <tr>
                    <td width="40%">
                        <label form="PROFILE[MARKET_CATEGORY][CATEGORY_LIST][<?=$cat["ID"]?>]"><?=$cat["NAME"]?></label>
                    </td>
                    <td>
                        <?$catVal = "";
                        if( in_array( $cat["ID"], $validCategories ) )
                            $catVal = $arProfile["MARKET_CATEGORY"]["CATEGORY_LIST"][$cat["ID"]];?>
                        <input type="text" value="<?=$catVal?>" name="PROFILE[MARKET_CATEGORY][CATEGORY_LIST][<?=$cat["ID"]?>]" />
                        <span class="field-edit" onclick="ShowMarketCategoryList(<?=$cat["ID"]?>)" style="cursor: pointer; background: #9ec710 !important;" title="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA_SELECT_SECTION" );?>"></span>
                    </td>
                </tr>
            <?}?>
        </table>
        <div id="market_category_list" style="display: none">
            <input onkeyup="FilterMarketCategoryList( this, 'market_category_list' )" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA_WINDOW_PLACEHOLDER" );?>">
            <select onclick="SetMarketCategory( this.value )" size="25">
                <option></option>
                <?foreach( $arMarketCategoryList as $marketCat ){?>
                    <option data-search="<?=strtolower( $marketCat );?>"><?=$marketCat?></option>
                <?}?>
            </select>
        </div>
    </td>
</tr>