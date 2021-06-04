<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
    \Bitrix\Main\Page\Asset;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_STAGES_TABLE_COMPARE', Loc::getMessage('ACRIT_CRM_TAB_STAGES_HEADING'));

// Block for tags management
$obTabControl->BeginCustomField('PROFILE[STAGES][table_compare]', Loc::getMessage('ACRIT_CRM_STAGES_TBLCMPR'));

Asset::getInstance()->addString('<style>
.acrit-crm-table-compare-item { margin-bottom: 5px; }
</style>');

Asset::getInstance()->addString('<script>
$(function() {
    $(".stages-add-item").click(function() {
        let row = $(this).parent("td").find(".acrit-crm-table-compare-row");
        let item = row.find(".acrit-crm-table-compare-item").eq(0);
        row.append(item.clone());
        return false;
    });
});
</script>');

$ext_status_list = $obPlugin->getStatuses();
$store_status_list = OrdersInfo::getStatuses();
?>
    <tr id="tr_stages_table_compare">
        <td>
            <table class="adm-list-table" id="acrit_orders_stages_table_compare">
                <thead>
                <tr class="adm-list-table-header">
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_STAGES_ORDER');?></div></td>
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_STAGES_DEAL');?></div></td>
                </tr>
                </thead>
                <tbody>
                <?foreach ($store_status_list as $site_status):
                    $variants = (array)$arProfile['STAGES']['table_compare'][$site_status['id']];
	                $variants = !empty($variants) ? $variants : [''];
                ?>
                <tr class="adm-list-table-row action-add-row" id="acrit_imp_agents_add">
                    <td class="adm-list-table-cell"><?=$site_status['name'];?> (<?=$site_status['id'];?>)</td>
                    <td class="adm-list-table-cell">
                        <div class="acrit-crm-table-compare-row">
	                        <?foreach ($variants as $variant):?>
                            <div class="acrit-crm-table-compare-item">
                                <select class="custom-select" name="PROFILE[STAGES][table_compare][<?=$site_status['id'];?>][]">
                                    <option value=""><?=Loc::getMessage('ACRIT_CRM_TAB_STAGES_NO');?></option>
                                    <?foreach ($ext_status_list as $ext_status):?>
                                    <option value="<?=$ext_status['id'];?>"<?=$ext_status['id']==$variant?' selected':'';?>><?=$ext_status['name'];?> (<?=$ext_status['id'];?>)</option>
                                    <?endforeach;?>
                                </select>
                            </div>
	                        <?endforeach;?>
                        </div>
                        <a href="#" class="stages-add-item"><?=Loc::getMessage('ACRIT_CRM_TAB_STAGES_ADD');?></a>
                    </td>
                </tr>
	            <?endforeach;?>
                </tbody>
            </table>
        </td>
    </tr>
<?
$obTabControl->EndCustomField('PROFILE[STAGES][table_compare]');

$obTabControl->AddSection('HEADING_STAGES_TABLE_CANCEL', Loc::getMessage('ACRIT_CRM_TAB_STAGES_TABLE_CANCEL'));

// Block for tags management
$obTabControl->BeginCustomField('PROFILE[STAGES][table_cancel]', Loc::getMessage('ACRIT_CRM_TAB_STAGES_TABLE_CANCEL'));

Asset::getInstance()->addString('<style>
#tr_stages_table_cancel td { text-align: center; }
.stages-table-cancel { background: #fff; padding: 15px; display: inline-block; text-align: left; }
</style>');
?>
    <tr id="tr_stages_table_cancel">
        <td>
            <div class="stages-table-cancel">
	            <?foreach ($ext_status_list as $store_status):?>
                <p><input type="checkbox" id="checkbox_<?=$store_status['id'];?>" name="PROFILE[STAGES][table_cancel][]" value="<?=$store_status['id'];?>"<?=in_array($store_status['id'], $arProfile['STAGES']['table_cancel'])?' checked':'';?>> <label for="checkbox_<?=$store_status['id'];?>">
                    <?=$store_status['name'];?> (<?=$store_status['id'];?>)
                </label></p>
	            <?endforeach;?>
            </div>
        </td>
    </tr>
<?
$obTabControl->EndCustomField('PROFILE[STAGES][table_cancel]');
