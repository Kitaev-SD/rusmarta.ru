<?php
IncludeModuleLangFile( __FILE__ );

$options = $obProfileUtils->createFieldset2( $arProfile["IBLOCK_ID"], true );
$arLinearFieldsList = $obProfileUtils->createLinearFieldset( $options );
$arBlockFieldsList = $obProfileUtils->createBlockFieldset( $options );

$arFieldType = array(
    "none" => GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONST" ),
    "complex" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_COMPLEX" ),
    "composite" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE" )
);

$arFieldTypeComplex = array(
    "none" => GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONST" ),
);

$arFieldTypeComposite = array(
    "none" => GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ),
    "field" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_FIELD" ),
    "const" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONST" ),
);

$arRoundModes = array(
    "none" => GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ),
    "UP" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_UP" ),
    "DOWN" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_DOWN" ),
    "EVEN" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_EVEN" ),
    "ODD" => GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_MODE_ODD" )
);
$idCnt = 0;
?>
<tr class="heading" align="center">
    <td colspan="2"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_HEADER" )?></td>
</tr>
<tr align="center">
    <td colspan="2">
        <?=BeginNote();?>
        <?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_DESCRIPTION" )?>
        <?=EndNote();?>
    </td>
</tr>
<tr>
    <td colspan="2" align="center">
        <div id="select_fields" style="display: none;">
            <option></option>
            <?foreach( $arBlockFieldsList as $blockFieldsGroupIndex => $blockFieldsGroup ){?>
                <?if( is_array( current( $blockFieldsGroup ) ) ){
                    foreach( $blockFieldsGroup as $blockFieldsGroupDataIndex => $blockFieldsGroupData ){?>
                        <optgroup label="<?=$blockFieldsGroupData[0];?>">
                            <?foreach( $blockFieldsGroupData[1] as $blockFieldsItemIndex => $blockFieldsItem ){?>
                                <option valcode="<?=$blockFieldsItem?>" value="<?=$blockFieldsItemIndex?>" data-search="<?=strtolower( $blockFieldsItem );?>"><?=$blockFieldsItem?></option>
                            <?}?>
                        </optgroup>
                    <?}
                }
                else{?>
                    <optgroup label="<?=$blockFieldsGroup[0];?>">
                        <?foreach( $blockFieldsGroup[1] as $blockFieldsItemIndex => $blockFieldsItem ){?>
                            <option valcode="<?=$blockFieldsItem?>"  value="<?=$blockFieldsItemIndex?>" data-search="<?=strtolower( $blockFieldsItem );?>"><?=$blockFieldsItem?></option>
                        <?}?>
                    </optgroup>
                <?}?>
            <?}?>
        </div>
        <table id="fieldset-container" cellpadding="0" cellspacing="0" width="100%">
            <?foreach( $arProfile["XMLDATA"] as $id => $field ){
                $selectedPropertyType = null;
                $useCondition = $field["USE_CONDITION"] == "Y" ? 'checked="checked"' : "";
                $hideCondition = $useCondition ? "" : "hide";
                $hideComplexBlock = $field["TYPE"] == "complex" ? "" : "hide";
                $hideCompositeBlock = $field["TYPE"] == "composite" ? "" : "hide";
                $compositeTrueDivider = $field["COMPOSITE_TRUE_DIVIDER"];
                $compositeFalseDivider = $field["COMPOSITE_FALSE_DIVIDER"];

                $hideConstBlock = $field["TYPE"] == "const" ? "" : "hide";
                $hideFieldBlock = ( ( ( $field["TYPE"] != "field" ) && ( !$hideConstBlock || !$hideComplexBlock ) ) || ( $field["TYPE"] == "composite" ) || ( $field["TYPE"] == "none" ) ||  !$field["TYPE"] ) ? "hide" : "";
                $hideResolveBlock = CGooglemerchantProfile::getFieldsetResolve( $arProfile["IBLOCK_ID"], $options, $field["VALUE"] ) !== false ? "" : "hide";

                $hideComplexConstTrueBlock = $field["COMPLEX_TRUE_TYPE"] == "const" ? "" : "hide";
                $hideComplexFieldTrueBlock = ( ( $field["COMPLEX_TRUE_TYPE"] != "field" ) && !$hideComplexConstTrueBlock ) || $field["COMPLEX_TRUE_TYPE"] == "none" || !$field["COMPLEX_TRUE_TYPE"] ? "hide" : "";

                $hideComplexConstFalseBlock = $field["COMPLEX_FALSE_TYPE"] == "const" ? "" : "hide";
                $hideComplexFieldFalseBlock = ( ( $field["COMPLEX_FALSE_TYPE"] != "field" ) && !$hideComplexConstFalseBlock ) || $field["COMPLEX_FALSE_TYPE"] == "none" || !$field["COMPLEX_FALSE_TYPE"] ? "hide" : "";

                $required = $field["REQUIRED"] == "Y" ? 'checked="checked"' : "";
                $processLogic = $field["PROCESS_LOGIC"] == "N" ? "" : 'checked="checked"';
                $deleteOnEmpty = $field["DELETE_ONEMPTY"] == "N" ? "" : 'checked="checked"';
                $deleteOnEmptyForce = $field["DELETE_ONEMPTY_FORCE"] == "N" ? "" : 'checked="checked"';
                $deleteOnEmptyRowForce = $field["DELETE_ONEMPTY_ROWFORCE"] == "N" ? "" : 'checked="checked"';
                $htmlEncode = $field["HTML_ENCODE"] == "N" ? "" : 'checked="checked"';
                $htmlEncodeCut = $field["HTML_ENCODE_CUT"] == "Y" ? 'checked="checked"' : "";
                $htmlToTxt = $field["HTML_TO_TXT"] == "N" ? "" : 'checked="checked"';
                $skipUntermElement = $field["SKIP_UNTERM_ELEMENT"] == "N" ? "" : 'checked="checked"';
                $exportRowCategoryParentList = $field["EXPORT_ROWCATEGORY_PARENT_LIST"] == "Y" ? 'checked="checked"' : "";
                $multipropToString = $field["MULTIPROP_TO_STRING"] == "Y" ? 'checked="checked"' : "";
                $urlEncode = $field["URL_ENCODE"] == "Y" ? 'checked="checked"' : "";
                $convertCase = $field["CONVERT_CASE"] == "Y" ? 'checked="checked"' : "";
                $textLimit = $field["TEXT_LIMIT"];
                $multiPropLimit = $field["MULTIPROP_LIMIT"];
                $multiPropFormat = $field["MULTIPROP_FORMAT"];
                $multiPropDivider = $field["MULTIPROP_DIVIDER"];
                $roundPrecision = $field["ROUND"]["PRECISION"];
                $roundMode = $field["ROUND"]["MODE"];
                $bitrixRoundMode = $field["BITRIX_ROUND_MODE"] == "Y" ? 'checked="checked"' : "";
                $calculateMinimum = $field["MINIMUM_OFFER_PRICE"] == "Y" ? 'checked="checked"' : "";?>
                <tr class="fieldset-item" data-id="<?=$idCnt++?>">
                    <td>
                        <label for="PROFILE[XMLDATA][<?=$id?>]"><?=$field["NAME"]?></label>
                        <input type="hidden" name="PROFILE[XMLDATA][<?=$id?>][NAME]" value="<?=$field["NAME"]?>" />
                    </td>
                    <td colspan="2" style="position: relative" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[XMLDATA][<?=$id?>][CODE]" value="<?=$field["CODE"]?>" readonly="readonly" />
                        <select name="PROFILE[XMLDATA][<?=$id?>][TYPE]" onchange="ShowConvalueBlock( this )" data-id="<?=$id?>">
                            <?foreach( $arFieldType as $typeId => $typeName ){?>
                                <?$selected = $typeId == $field["TYPE"] ? 'selected="selected"' : "";?>
                                <option value="<?=$typeId?>" <?=$selected?>><?=$typeName?></option>
                            <?}?>
                        </select>

                        <input name="PROFILE[XMLDATA][<?=$id?>][VALUE]" data-id="<?=$id?>" type="hidden" value="<?=$field["VALUE"]?>" />
                        <input class="field-block <?=$hideFieldBlock?>" readonly="readonly" name="PROFILE[XMLDATA][<?=$id?>][VALUE_SHOW]" data-id="<?=$id?>" type="text" title="<?=$arLinearFieldsList[$field["VALUE"]];?>" value="<?=( isset( $arLinearFieldsList[$field["VALUE"]] ) ? $arLinearFieldsList[$field["VALUE"]] : GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ) );?>" />
                        <span class="field-edit fieldselecter <?=$hideFieldBlock?>" onclick="ShowFieldsList( 'field-block-', '<?=$id?>', false, '<?=$field["VALUE"]?>' )" title="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_LIST_DATA_SELECT" );?>"></span>

                        <div id="field-block-<?=$id?>-list" class="fields-popup-list" style="display: none">
                            <input onkeyup="FilterFieldsList( this, 'field-block-<?=$id?>-list' )" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA_WINDOW_PLACEHOLDER" );?>" value="<?=( isset( $arLinearFieldsList[$field["VALUE"]] ) ? $arLinearFieldsList[$field["VALUE"]] : "" )?>">
                            <select onchange="SetFieldsListField( this.value, this.options[this.selectedIndex].getAttribute( 'valcode' ), 'VALUE', false, false )" size="25">
                            </select>
                        </div>

                        <select class="resolve-block <?=$hideResolveBlock?>" name="PROFILE[XMLDATA][<?=$id?>][RESOLVE]">
                            <option value="">--<?=GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" )?>--</option>
                                <?if( ( $resolve = CGooglemerchantProfile::getFieldsetResolve( $arProfile["IBLOCK_ID"], $options, $field["VALUE"] ) ) !== false ){
                                    $opt = $obProfileUtils->selectFieldset2($resolve, $field["RESOLVE"]);
                                    echo implode( "\n", $opt );
                                    unset( $opt );
                                }?>
                        </select>
                        <div class="const-block <?=$hideConstBlock?>">
                            <?$hideContvalueFalse = !$useCondition ? "hide" : "";?>
                            <?$showPlaceholder = !$hideContvalueFalse ? "placeholder" : "data-placeholder";?>
                            <textarea name="PROFILE[XMLDATA][<?=$id?>][CONTVALUE_TRUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_TRUE" )?>"><?=$field["CONTVALUE_TRUE"]?></textarea>
                            <textarea name="PROFILE[XMLDATA][<?=$id?>][CONTVALUE_FALSE]" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_FALSE" )?>" class="<?=$hideContvalueFalse?>"><?=$field["CONTVALUE_FALSE"]?></textarea>
                        </div>
                        <div class="complex-block-container <?=$hideComplexBlock?>">
                            <div class="complex-block">
                                <?$hideComplexFalse = !$useCondition ? "hide" : "";?>
                                <?$showPlaceholder = !$hideComplexFalse ? "placeholder" : "data-placeholder";?>

                                <div>
                                    <select name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_TYPE]" onchange="ShowConvalueBlockComplex( this )" data-id="<?=$id?>">
                                        <?foreach( $arFieldTypeComplex as $typeComplexId => $typeNameComplex ){?>
                                            <?$selectedComplex = $typeComplexId == $field["COMPLEX_TRUE_TYPE"] ? 'selected="selected"' : "";?>
                                            <option value="<?=$typeComplexId?>" <?=$selectedComplex?>><?=$typeNameComplex?></option>
                                        <?}?>
                                    </select>

                                    <input name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_VALUE]" data-id="<?=$id?>" type="hidden" value="<?=$field["COMPLEX_TRUE_VALUE"]?>" />
                                    <input class="field-block-complex <?=$hideComplexFieldTrueBlock?>" readonly="readonly" name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_VALUE_SHOW]" data-id="<?=$id?>" type="text" title="<?=$arLinearFieldsList[$field["COMPLEX_TRUE_VALUE"]];?>" value="<?=( isset( $arLinearFieldsList[$field["COMPLEX_TRUE_VALUE"]] ) ? $arLinearFieldsList[$field["COMPLEX_TRUE_VALUE"]] : GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ) );?>" />
                                    <span class="field-edit-complex fieldselecter <?=$hideComplexFieldTrueBlock?>" onclick="ShowFieldsList( 'field-block-complex-', '<?=$id?>', false, '<?=$field["COMPLEX_TRUE_VALUE"]?>' )" title="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_LIST_DATA_SELECT" );?>"></span>

                                    <div id="field-block-complex-<?=$id?>-list" class="fields-popup-list" style="display: none">
                                        <input onkeyup="FilterFieldsList( this, 'field-block-complex-<?=$id?>-list' )" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA_WINDOW_PLACEHOLDER" );?>" value="<?=( isset( $arLinearFieldsList[$field["COMPLEX_TRUE_VALUE"]] ) ? $arLinearFieldsList[$field["COMPLEX_TRUE_VALUE"]] : "" )?>">
                                        <select onchange="SetFieldsListField( this.value, this.options[this.selectedIndex].getAttribute( 'valcode' ), 'COMPLEX_TRUE_VALUE', false, false )" size="25">
                                        </select>
                                    </div>

                                    <div class="const-block-complex <?=$hideComplexConstTrueBlock?>">
                                        <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_TRUE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_TRUE" )?>"><?=$field["COMPLEX_TRUE_CONTVALUE"]?></textarea>
                                    </div>
                                </div>
                                <div class="<?=$hideComplexFalse?>">
                                    <select name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_TYPE]" onchange="ShowConvalueBlockComplexFalse( this )" data-id="<?=$id?>">
                                        <?foreach( $arFieldTypeComplex as $typeComplexId => $typeNameComplex ){?>
                                            <?$selectedComplex = $typeComplexId == $field["COMPLEX_FALSE_TYPE"] ? 'selected="selected"' : "";?>
                                            <option value="<?=$typeComplexId?>" <?=$selectedComplex?>><?=$typeNameComplex?></option>
                                        <?}?>
                                    </select>

                                    <input name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_VALUE]" data-id="<?=$id?>" type="hidden" value="<?=$field["COMPLEX_FALSE_VALUE"]?>" />
                                    <input class="field-block-complex-false <?=$hideComplexFieldFalseBlock?>" readonly="readonly" name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_VALUE_SHOW]" data-id="<?=$id?>" type="text" title="<?=$arLinearFieldsList[$field["COMPLEX_FALSE_VALUE"]];?>" value="<?=( isset( $arLinearFieldsList[$field["COMPLEX_FALSE_VALUE"]] ) ? $arLinearFieldsList[$field["COMPLEX_FALSE_VALUE"]] : GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ) );?>" />
                                    <span class="field-edit-complex-false fieldselecter <?=$hideComplexFieldFalseBlock?>" onclick="ShowFieldsList( 'field-block-complex-false-', '<?=$id?>', false, '<?=$field["COMPLEX_FALSE_VALUE"]?>' )" title="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_LIST_DATA_SELECT" );?>"></span>

                                    <div id="field-block-complex-false-<?=$id?>-list" class="fields-popup-list" style="display: none">
                                        <input onkeyup="FilterFieldsList( this, 'field-block-complex-false-<?=$id?>-list' )" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA_WINDOW_PLACEHOLDER" );?>" value="<?=( isset( $arLinearFieldsList[$field["COMPLEX_FALSE_VALUE"]] ) ? $arLinearFieldsList[$field["COMPLEX_FALSE_VALUE"]] : "" )?>">
                                        <select onchange="SetFieldsListField( this.value, this.options[this.selectedIndex].getAttribute( 'valcode' ), 'COMPLEX_FALSE_VALUE', false, false )" size="25">
                                        </select>
                                    </div>

                                    <div class="const-block-complex-false <?=$hideComplexConstFalseBlock?>">
                                        <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPLEX_FALSE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_TRUE" )?>"><?=$field["COMPLEX_FALSE_CONTVALUE"]?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="composite-block-container <?=$hideCompositeBlock?>" style="width: 100%;">
                            <div class="composite-block" style="margin: 10px 0px 0px 0px; width: 100%;">
                                <?$hideCompositeFalse = !$useCondition ? "hide" : "";?>
                                <?$showPlaceholder = !$hideCompositeFalse ? "placeholder" : "data-placeholder";?>

                                <div style="width: 100%;">
                                    <div class="composite-divider" style="width: 100%;">
                                        <span id="hint_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER_HELP" )?>' );</script>
                                        <label for="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE_DIVIDER]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER" )?></label><br/>
                                        <input type="text" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE_DIVIDER]" value="<?=$compositeTrueDivider;?>" style="width: 420px;" />
                                    </div>
                                    <div class="composite-data-area-true" style="margin: 10px 0px 0px 0px; width: 100%;">
                                        <?if( is_array( $arProfile["XMLDATA"][$id]["COMPOSITE_TRUE"] ) && !empty( $arProfile["XMLDATA"][$id]["COMPOSITE_TRUE"] ) ){
                                            foreach( $arProfile["XMLDATA"][$id]["COMPOSITE_TRUE"] as $compositeTrueId => $arCompositeTrue ){?>
                                                <div class="composite-data-item" data-id="<?=$compositeTrueId?>" style="margin: 10px 0px 0px 0px;">
                                                    <select name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE][<?=$compositeTrueId?>][COMPOSITE_TRUE_TYPE]" onchange="ShowConvalueBlockComposite( this )" data-id="<?=$id?>" style="width: 430px;">
                                                        <?foreach( $arFieldTypeComposite as $typeCompositeId => $typeNameComposite ){?>
                                                            <?$selectedComposite = ( ( $typeCompositeId == $arCompositeTrue["COMPOSITE_TRUE_TYPE"] ) ? 'selected="selected"' : "" );
                                                            $hideCompositeConstTrueBlock = ( ( $typeCompositeId == $arCompositeTrue["COMPOSITE_TRUE_TYPE"] ) && ( $typeCompositeId == "const" ) ) ? "" : "hide";
                                                            $hideCompositeFieldTrueBlock = ( ( $typeCompositeId != "field" ) && !$hideCompositeConstTrueBlock ) || $typeCompositeId == "none" || !$typeCompositeId ? "hide" : "";?>
                                                            <option value="<?=$typeCompositeId?>" <?=$selectedComposite?>><?=$typeNameComposite?></option>
                                                        <?}?>
                                                    </select>
                                                    <span class="composite-data-item-delete">&times</span><br/>

                                                    <input name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE][<?=$compositeTrueId?>][COMPOSITE_TRUE_VALUE]" data-id="<?=$id?>" type="hidden" value="<?=$arCompositeTrue["COMPOSITE_TRUE_VALUE"]?>" />
                                                    <input class="field-block-composite <?=$hideCompositeFieldTrueBlock?>" readonly="readonly" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE][<?=$compositeTrueId?>][COMPOSITE_TRUE_VALUE_SHOW]" data-id="<?=$id?>" type="text" title="<?=$arLinearFieldsList[$arCompositeTrue["COMPOSITE_TRUE_VALUE"]];?>" value="<?=( isset( $arLinearFieldsList[$arCompositeTrue["COMPOSITE_TRUE_VALUE"]] ) ? $arLinearFieldsList[$arCompositeTrue["COMPOSITE_TRUE_VALUE"]] : GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ) );?>" />
                                                    <span class="field-edit-composite fieldselecter <?=$hideCompositeFieldTrueBlock?>" onclick="ShowFieldsList( 'field-block-composite-', '<?=$id?>', '<?=$compositeTrueId?>', '<?=$arCompositeTrue["COMPOSITE_TRUE_VALUE"]?>' )" title="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_LIST_DATA_SELECT" );?>"></span>

                                                    <div id="field-block-composite-<?=$id?>-<?=$compositeTrueId?>-list" class="fields-popup-list" style="display: none">
                                                        <input onkeyup="FilterFieldsList( this, 'field-block-composite-<?=$id?>-<?=$compositeTrueId?>-list' )" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA_WINDOW_PLACEHOLDER" );?>" value="<?=( isset( $arLinearFieldsList[$arCompositeTrue["COMPOSITE_TRUE_VALUE"]] ) ? $arLinearFieldsList[$arCompositeTrue["COMPOSITE_TRUE_VALUE"]] : "" )?>">
                                                        <select onchange="SetFieldsListField( this.value, this.options[this.selectedIndex].getAttribute( 'valcode' ), 'COMPOSITE_TRUE', '<?=$compositeTrueId?>', 'COMPOSITE_TRUE_VALUE' )" size="25">
                                                        </select>
                                                    </div>

                                                    <div class="const-block-composite <?=$hideCompositeConstTrueBlock?>">
                                                        <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_TRUE][<?=$compositeTrueId?>][COMPOSITE_TRUE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_TRUE" )?>" style="width: 420px;"><?=$arCompositeTrue["COMPOSITE_TRUE_CONTVALUE"]?></textarea>
                                                    </div>
                                                </div>
                                            <?}
                                        }?>
                                    </div>
                                    <div class="composite-add-field-button truenode" data-id="<?=$idCnt?>" data-row-id="<?=$id?>" style="margin: 10px 0px 0px 0px;">
                                        <button class="adm-btn" onclick="CompositeFieldsetAdd( this ); return false;">
                                            <?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ADD_PART_TO_COMPOSITE_FIELD" );?>
                                        </button>
                                    </div>
                                </div>
                                <div class="composite-data-area-false-container <?=$hideCompositeFalse?>" style="width: 100%; margin: 20px 0px 0px 0px;">
                                    <div class="composite-divider" style="width: 100%;">
                                        <span id="hint_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER_HELP" )?>' );</script>
                                        <label for="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE_DIVIDER]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_COMPOSITE_DIVIDER" )?></label><br/>
                                        <input type="text" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE_DIVIDER]" value="<?=$compositeFalseDivider;?>" style="width: 420px;" />
                                    </div>
                                    <div class="composite-data-area-false" style="margin: 10px 0px 0px 0px; width: 100%;">
                                        <?if( is_array( $arProfile["XMLDATA"][$id]["COMPOSITE_FALSE"] ) && !empty( $arProfile["XMLDATA"][$id]["COMPOSITE_FALSE"] ) ){
                                            foreach( $arProfile["XMLDATA"][$id]["COMPOSITE_FALSE"] as $compositeFalseId => $arCompositeFalse ){?>
                                                <div class="composite-data-item" data-id="<?=$compositeFalseId?>" style="margin: 10px 0px 0px 0px;">
                                                    <select name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE][<?=$compositeFalseId?>][COMPOSITE_FALSE_TYPE]" onchange="ShowConvalueBlockComposite( this )" data-id="<?=$id?>" style="width: 430px;">
                                                        <?foreach( $arFieldTypeComposite as $typeCompositeId => $typeNameComposite ){?>
                                                            <?$selectedComposite = ( ( $typeCompositeId == $arCompositeFalse["COMPOSITE_FALSE_TYPE"] ) ? 'selected="selected"' : "" );
                                                            $hideCompositeConstFalseBlock = ( ( $typeCompositeId == $arCompositeFalse["COMPOSITE_FALSE_TYPE"] ) && ( $typeCompositeId == "const" ) ) ? "" : "hide";
                                                            $hideCompositeFieldFalseBlock = ( ( $typeCompositeId != "field" ) && !$hideCompositeConstFalseBlock ) || $typeCompositeId == "none" || !$typeCompositeId ? "hide" : "";?>
                                                            <option value="<?=$typeCompositeId?>" <?=$selectedComposite?>><?=$typeNameComposite?></option>
                                                        <?}?>
                                                    </select>
                                                    <span class="composite-data-item-delete">&times</span><br/>

                                                    <input name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE][<?=$compositeFalseId?>][COMPOSITE_FALSE_VALUE]" data-id="<?=$id?>" type="hidden" value="<?=$arCompositeFalse["COMPOSITE_FALSE_VALUE"]?>" />
                                                    <input class="field-block-composite <?=$hideCompositeFieldFalseBlock?>" readonly="readonly" name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE][<?=$compositeFalseId?>][COMPOSITE_FALSE_VALUE_SHOW]" data-id="<?=$id?>" type="text" title="<?=$arLinearFieldsList[$arCompositeFalse["COMPOSITE_FALSE_VALUE"]];?>" value="<?=( isset( $arLinearFieldsList[$arCompositeFalse["COMPOSITE_FALSE_VALUE"]] ) ? $arLinearFieldsList[$arCompositeFalse["COMPOSITE_FALSE_VALUE"]] : GetMessage( "ACRIT_GOOGLEMERCHANT_NE_VYBRANO" ) );?>" />
                                                    <span class="field-edit-composite fieldselecter <?=$hideCompositeFieldFalseBlock?>" onclick="ShowFieldsList( 'field-block-composite-', '<?=$id?>', '<?=$compositeFalseId?>', '<?=$arCompositeTrue["COMPOSITE_FALSE_VALUE"]?>' )" title="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELD_LIST_DATA_SELECT" );?>"></span>

                                                    <div id="field-block-composite-<?=$id?>-<?=$compositeFalseId?>-list" class="fields-popup-list" style="display: none">
                                                        <input onkeyup="FilterFieldsList( this, 'field-block-composite-<?=$id?>-<?=$compositeFalseId?>-list' )" placeholder="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_MARKET_CATEGORY_DATA_WINDOW_PLACEHOLDER" );?>" value="<?=( isset( $arLinearFieldsList[$arCompositeFalse["COMPOSITE_FALSE_VALUE"]] ) ? $arLinearFieldsList[$arCompositeFalse["COMPOSITE_FALSE_VALUE"]] : "" )?>">
                                                        <select onchange="SetFieldsListField( this.value, this.options[this.selectedIndex].getAttribute( 'valcode' ), 'COMPOSITE_FALSE', '<?=$compositeFalseId?>', 'COMPOSITE_FALSE_VALUE' )" size="25">
                                                        </select>
                                                    </div>

                                                    <div class="const-block-composite <?=$hideCompositeConstFalseBlock?>">
                                                        <textarea name="PROFILE[XMLDATA][<?=$id?>][COMPOSITE_FALSE][<?=$compositeFalseId?>][COMPOSITE_FALSE_CONTVALUE]" <?=$showPlaceholder?>="<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_FALSE" )?>" style="width: 420px;"><?=$arCompositeFalse["COMPOSITE_FALSE_CONTVALUE"]?></textarea>
                                                    </div>
                                                </div>
                                            <?}
                                        }?>
                                    </div>
                                    <div class="composite-add-field-button falsenode" data-id="<?=$idCnt?>" data-row-id="<?=$id?>" style="margin: 10px 0px 0px 0px;">
                                        <button class="adm-btn" onclick="CompositeFieldsetAdd( this ); return false;">
                                            <?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ADD_PART_TO_COMPOSITE_FIELD" );?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <span class="fieldset-item-delete">&times</span>
                        <div style="margin: 10px 0px 10px 15px;">
                            <span id="hint_GOOGLEMERCHANT_FIELDSET_REQUIRED"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_REQUIRED' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_REQUIRED_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][REQUIRED]" value="Y" <?=$required?> />
                            <label for="PROFILE[XMLDATA][<?=$id?>][REQUIRED]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_REQUIRED" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

							<?if(CModule::includeModule('catalog')):?>
								<span id="hint_GOOGLEMERCHANT_FIELDSET_CONDITION"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_CONDITION' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_HELP" )?>' );</script>
								<input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][USE_CONDITION]" <?=$useCondition?> value="Y" data-id="<?=$id?>" onclick="ShowConditionBlock( this, '<?=$idCnt?>' )"/>
								<label for="PROFILE[XMLDATA][<?=$id?>][USE_CONDITION]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION" )?></label>
								<div style="height: 5px;">&nbsp;</div>
							<?endif?>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_PROCESS_LOGIC"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_PROCESS_LOGIC' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_PROCESS_LOGIC_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][PROCESS_LOGIC]" <?=$processLogic?> value="Y"/>
                            <label for="PROFILE[XMLDATA][<?=$id?>][PROCESS_LOGIC]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_PROCESS_LOGIC" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY]" <?=$deleteOnEmpty?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_FORCE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_FORCE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_FORCE_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY_FORCE]" <?=$deleteOnEmptyForce?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY_FORCE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_FORCE" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ROWFORCE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ROWFORCE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ROWFORCE_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY_ROWFORCE]" <?=$deleteOnEmptyRowForce?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][DELETE_ONEMPTY_ROWFORCE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_DELETE_ONEMPTY_ROWFORCE" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_URL_ENCODE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_URL_ENCODE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_URL_ENCODE_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][URL_ENCODE]" <?=$urlEncode?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][URL_ENCODE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_URL_ENCODE" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_CONVERT_CASE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_CONVERT_CASE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_CASE_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][CONVERT_CASE]" <?=$convertCase?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][CONVERT_CASE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_CASE" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE]" <?=$htmlEncode?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_CUT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_CUT' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_CUT_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE_CUT]" <?=$htmlEncodeCut?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][HTML_ENCODE_CUT]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_ENCODE_CUT" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_HTML_TO_TXT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_HTML_TO_TXT' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_TO_TXT_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][HTML_TO_TXT]" <?=$htmlToTxt?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][HTML_TO_TXT]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_HTML_TO_TXT" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_SKIP_UNTERM_ELEMENT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_SKIP_UNTERM_ELEMENT' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_SKIP_UNTERM_ELEMENT_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][SKIP_UNTERM_ELEMENT]" <?=$skipUntermElement?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][SKIP_UNTERM_ELEMENT]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_SKIP_UNTERM_ELEMENT" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_EXPORT_ROWCATEGORY_PARENT_LIST"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_EXPORT_ROWCATEGORY_PARENT_LIST' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_EXPORT_ROWCATEGORY_PARENT_LIST_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][EXPORT_ROWCATEGORY_PARENT_LIST]" <?=$exportRowCategoryParentList?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][EXPORT_ROWCATEGORY_PARENT_LIST]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_EXPORT_ROWCATEGORY_PARENT_LIST" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_TO_STRING"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_TO_STRING' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_TO_STRING_HELP" )?>' );</script>
                            <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_TO_STRING]" <?=$multipropToString?> value="Y">
                            <label for="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_TO_STRING]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_TO_STRING" )?></label>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_DIVIDER"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_DIVIDER' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_DIVIDER_HELP" )?>' );</script>
                            <label for="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_DIVIDER]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_DIVIDER" )?></label><br/>
                            <input type="text" name="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_DIVIDER]" value="<?=$multiPropDivider;?>" />

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_LIMIT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_LIMIT' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_LIMIT_HELP" )?>' );</script>
                            <label for="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_LIMIT]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_LIMIT" )?></label><br/>
                            <input type="text" name="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_LIMIT]" value="<?=$multiPropLimit;?>" />

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_FORMAT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_MULTIPROP_FORMAT' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_FORMAT_HELP" )?>' );</script>
                            <label for="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_FORMAT]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MULTIPROP_FORMAT" )?></label><br/>
                            <input type="text" name="PROFILE[XMLDATA][<?=$id?>][MULTIPROP_FORMAT]" value="<?=$multiPropFormat;?>" />

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_TEXT_LIMIT"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_TEXT_LIMIT' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_TEXT_LIMIT_HELP" )?>' );</script>
                            <label for="PROFILE[XMLDATA][<?=$id?>][TEXT_LIMIT]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_TEXT_LIMIT" )?></label><br/>
                            <input type="text" name="PROFILE[XMLDATA][<?=$id?>][TEXT_LIMIT]" value="<?=$textLimit;?>" />

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_ROUND_PRECISION"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_ROUND_PRECISION' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_PRECISION_HELP" )?>' );</script>
                            <label for="PROFILE[XMLDATA][<?=$id?>][ROUND][PRECISION]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_ROUND_PRECISION" )?></label><br/>
                            <input type="text" maxlength="5" size="5" name="PROFILE[XMLDATA][<?=$id?>][ROUND][PRECISION]" value="<?=$roundPrecision;?>" />

                            <select name="PROFILE[XMLDATA][<?=$id?>][ROUND][MODE]">
                                <?foreach( $arRoundModes as $typeId => $typeName ){?>
                                    <?$selected = $typeId == $roundMode ? 'selected="selected"' : "";?>
                                    <option value="<?=$typeId?>" <?=$selected?>><?=$typeName?></option>
                                <?}?>
                            </select>

                            <div style="height: 5px;">&nbsp;</div>

                            <div class="bitrix_round_mode" data-dependent="price" <?if( stripos( $field["VALUE"], "PRICE" ) !== false ):?><?else:?>style="display: none"<?endif;?>>
                                <div style="height: 5px;">&nbsp;</div>
                                <span id="hint_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE_HELP" )?>' );</script>
                                <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][BITRIX_ROUND_MODE]" <?=$bitrixRoundMode?> value="Y">
                                <label for="PROFILE[XMLDATA][<?=$id?>][BITRIX_ROUND_MODE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_BITRIX_ROUND_MODE" )?></label>
                            </div>

                            <div style="height: 5px;">&nbsp;</div>

                            <div class="minimum_offer_price" data-dependent="price" <?if( stripos( $field["VALUE"], "PRICE" ) !== false ):?><?else:?>style="display: none"<?endif;?>>
                                <div style="height: 5px;">&nbsp;</div>
                                <span id="hint_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_HELP" )?>' );</script>
                                <input type="checkbox" name="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE]" <?=$calculateMinimum?> value="Y" onchange="ShowMinimumOfferPriceCode( this )">
                                <label for="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE" )?></label>

                                <div class="minimum_offer_price_code" <?if( strlen( $calculateMinimum ) ):?><?else:?>style="display: none"<?endif;?>>
                                    <div style="height: 5px;">&nbsp;</div>
                                    <span id="hint_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_CODE"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_CODE' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_CODE_HELP" )?>' );</script>
                                    <label for="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE_CODE]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_MINIMUM_OFFER_PRICE_CODE" )?></label>
                                    <div>
                                    <input type="text" maxlength="20" size="20" name="PROFILE[XMLDATA][<?=$id?>][MINIMUM_OFFER_PRICE_CODE]" value="<?=$field["MINIMUM_OFFER_PRICE_CODE"]?>">
                                    </div>
                                </div>
                            </div>

                            <div style="height: 5px;">&nbsp;</div>

                            <span id="hint_GOOGLEMERCHANT_FIELDSET_CONVERT_DATA"></span><script type="text/javascript">BX.hint_replace( BX( 'hint_GOOGLEMERCHANT_FIELDSET_CONVERT_DATA' ), '<?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_DATA_HELP" )?>' );</script>
                            <label for="PROFILE[XMLDATA][<?=$id?>][CONVERT_DATA]"><?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_DATA" )?></label><br/>
                            <table id="fieldset-convert-fieldset-container<?=$id?>" cellpadding="0" cellspacing="0" width="100%">
                                <?if( is_array( $field["CONVERT_DATA"] ) ){
                                    foreach( $field["CONVERT_DATA"] as $convertDataId => $fields ){?>
                                        <tr class="convert-fieldset-item" data-id="<?=$convertDataId?>">
                                            <td align="right">
                                                <input type="text" name="PROFILE[XMLDATA][<?=$id?>][CONVERT_DATA][<?=$convertDataId?>][0]" value="<?=htmlspecialcharsbx( $fields[0], ENT_QUOTES );?>" />
                                            </td>
                                            <td align="left" style="position: relative" class="adm-detail-content-cell-r">
                                                <input type="text" name="PROFILE[XMLDATA][<?=$id?>][CONVERT_DATA][<?=$convertDataId?>][1]" value="<?=htmlspecialcharsbx( $fields[1], ENT_QUOTES );?>" />
                                                <span class="fieldset-item-delete">&times</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <hr style="opacity: 0.2;">
                                            </td>
                                        </tr>
                                    <?}
                                }?>
                            </table>

                            <div data-row-id="<?=$id?>" style="margin: 0 auto; padding: 10px; text-align: center;">
                                <button class="adm-btn" onclick="FieldsetConvertFieldsetAdd( this ); return false;">
                                    <?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONVERT_CONDITION_ADD" )?>
                                </button>
                            </div>
                        </div>
                        <div id="PROFILE_XMLDATA_<?=$id?>_CONDITION" class="condition-block <?=$hideCondition?>">
                            <?if( $field["USE_CONDITION"] == "Y" && CModule::IncludeModule( "catalog" ) ){
                                $obCond = new CAcritGooglemerchantCatalogCond();
                                CAcritGooglemerchantProps::$arIBlockFilter = $obProfileUtils->PrepareIBlock( $arProfile["IBLOCK_ID"], $arProfile["USE_SKU"] );
                                $boolCond = $obCond->Init(
                                    BT_COND_MODE_DEFAULT,
                                    BT_COND_BUILD_CATALOG,
                                    array(
                                        "FORM_NAME" => "googlemerchant_form",
                                        "CONT_ID" => "PROFILE_XMLDATA_".$id."_CONDITION",
                                        "JS_NAME" => "JSCatCond_field_".$idCnt,
                                        "PREFIX" => "PROFILE[XMLDATA][".$id."][CONDITION]"
                                    )
                                );
                                if( !$boolCond ){
                                    if( $ex = $APPLICATION->GetException() ){
                                        echo $ex->GetString()."<br/>";
                                    }
                                }
																if(!$fieldsCheck) {
																	$field["CONDITION"] = $obCond->Parse( $field["CONDITION"] );
																}
                                $obCond->Show( $field["CONDITION"] );
                            }?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <hr style="opacity: 0.2;">
                    </td>
                </tr>
            <?}?>
        </table>
    </td>
</tr>
<tr>
    <td colspan="2" align="center" id="fieldset-item-add-button">
        <button class="adm-btn" onclick="FieldsetAdd( this ); return false;">
            <?=GetMessage( "ACRIT_GOOGLEMERCHANT_FIELDSET_CONDITION_ADD" );?>
        </button>
    </td>
</tr>