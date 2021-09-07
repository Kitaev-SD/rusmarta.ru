<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($obPlugin->getTabComment('products')) {
    $obTabControl->BeginCustomField('PROFILE[PRODUCTS][tab_message]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_TAB_MESSAGE'));
?>
    <tr>
        <td colspan="2">
            <div id="acrit-module-update-notifier">
                <div class="acrit-exp-note-compact">
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message"><?=$obPlugin->getTabComment('products');?></div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
<?
    $obTabControl->EndCustomField('PROFILE[PRODUCTS][tab_message]');
}

if ($obPlugin->hasProducts()) {
    $obTabControl->AddSection('HEADING_PRODUCTS_TBLCMPR', Loc::getMessage('ACRIT_CRM_TAB_PRODUCTS_HEADING'));
    $obTabControl->BeginCustomField('PROFILE[PRODUCTS][search_fields]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_SEARCH_FIELD'));
    $iblock_list = Products::getIblockList(true);
    $ext_search_field = $obPlugin->getIdField();
?>
<?if($ext_search_field):?>
    <tr>
        <td colspan="2">
            <div id="acrit-module-update-notifier">
                <div class="acrit-exp-note-compact">
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message"><?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_SEARCH_EXT_ID', ['#ID#' => $ext_search_field['id'], '#NAME#' => $ext_search_field['name']]);?></div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
<?endif;?>
    <?if($ext_search_field):?>
    <tr>
        <td colspan="2">
            <div id="acrit-module-update-notifier">
                <div class="acrit-exp-note-compact">
                    <div class="adm-info-message-wrap">
                        <div class="adm-info-message"><?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_SEARCH_EXT_ID', ['#ID#' => $ext_search_field['id'], '#NAME#' => $ext_search_field['name']]);?></div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <?endif;?>
    <tr id="tr_products_search_fields">
        <td>
            <label for="field_products_search_fields">
	            <span id="field_products_search_fields_hint"></span>
                <?=$obTabControl->GetCustomLabelHTML()?>
            <label>
            <script>BX.hint_replace(BX('field_products_search_fields_hint'), '<?= \CUtil::JSEscape(Loc::getMessage('ACRIT_CRM_TAB_BASIC_SEARCH_FIELD_HINT'))?>');</script>
        </td>
        <td>
            <table class="adm-list-table" id="acrit_imp_agents_list">
                <thead>
                <tr class="adm-list-table-header">
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_PRODUCTS_ORDER');?></div></td>
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_PRODUCTS_STORE');?></div></td>
                </tr>
                </thead>
                <tbody>
		        <?foreach ($iblock_list as $iblock):
			        $fields_list = Products::getFieldsForID($iblock['id']);
                    ?>
                    <tr class="adm-list-table-row action-add-row" id="acrit_imp_agents_add">
                        <td class="adm-list-table-cell"><?=$iblock['name'];?> [<?=$iblock['id'];?>]</td>
                        <td class="adm-list-table-cell">
                            <select class="custom-select" name="PROFILE[PRODUCTS][search_fields][<?=$iblock['id'];?>]">">
                                <option value=""><?=Loc::getMessage('ACRIT_CRM_TAB_PRODUCTS_IBLOCK_IDS_NO');?></option>
						        <?foreach ($fields_list as $fields_group):?>
                                <optgroup label="<?=$fields_group['title'];?>">
                                    <?foreach ($fields_group['items'] as $field):?>
                                    <option value="<?=$field['id'];?>"<?=$arProfile['PRODUCTS']['search_fields'][$iblock['id']]==$field['id']?' selected':'';?>><?=$field['name'];?> (<?=$field['id'];?>)</option>
                                    <?endforeach;?>
                                </optgroup>
						        <?endforeach;?>
                            </select>
                        </td>
                    </tr>
		        <?endforeach;?>
                </tbody>
            </table>
        </td>
    </tr>
<?
    $obTabControl->EndCustomField('PROFILE[PRODUCTS][search_fields]');
}

/*// Block for compare table of products
$obTabControl->BeginCustomField('PROFILE[PRODUCTS][table_compare]', Loc::getMessage('ACRIT_CRM_PRODUCTS_TBLCMPR'));
?>
    <tr id="TR_PRODUCTS_TABLE_COMPARE">
        <td>
        </td>
    </tr>
<?
$obTabControl->EndCustomField('PROFILE[PRODUCTS][table_compare]');*/

?>