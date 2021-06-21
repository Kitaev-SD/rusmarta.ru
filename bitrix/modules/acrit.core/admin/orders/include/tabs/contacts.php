<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_CONTACTS', Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_HEADING'));

// Block for tags management
$obTabControl->BeginCustomField('PROFILE[CONTACTS][table_compare]', Loc::getMessage('ACRIT_CRM_CONTACTS_TBLCMPR'));
$ext_fields = $obPlugin->getContactFields();
$orders_fields = OrdersInfo::getUserFields();
?>
    <tr id="tr_contacts_table_compare">
        <td colspan="2">
            <table class="adm-list-table" id="acrit_imp_agents_list">
                <thead>
                <tr class="adm-list-table-header">
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_DEAL');?></div></td>
                    <td class="adm-list-table-cell"><div class="adm-list-table-cell-inner"><?=Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_ORDER');?></div></td>
                </tr>
                </thead>
                <tbody>
                <?foreach ($orders_fields as $k => $orders_field):?>
                <tr class="adm-list-table-row action-add-row" id="acrit_imp_agents_add">
                    <td class="adm-list-table-cell"><?=$orders_field['name'];?> (<?=$k;?>)</td>
                    <td class="adm-list-table-cell">
                        <select class="custom-select" name="PROFILE[CONTACTS][table_compare][<?=$k;?>]">
                            <option value=""><?=Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_NO');?></option>
                            <?foreach ($ext_fields as $fields_group):?>
                            <optgroup label="<?=$fields_group['title'];?>">
	                            <?foreach ($fields_group['items'] as $field):?>
                                <option value="<?=$field['id'];?>"<?=$arProfile['CONTACTS']['table_compare'][$k]==$field['id']?' selected':'';?>><?=$field['name'];?> (<?=$field['id'];?>)</option>
	                            <?endforeach;?>
                            </optgroup>
                            <?endforeach;?>
                        </select>
                    </td>
                </tr>
                <?endforeach;?>
                <tr class="adm-list-table-row action-add-row" id="acrit_imp_agents_add">
                    <td class="adm-list-table-cell"><label for="field_contacts_email_def"><?=Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_EMAIL_DEF');?></label><?=Helper::showHint(Loc::getMessage('ACRIT_CRM_TAB_CONTACTS_EMAIL_DEF_HINT'));?></td>
                    <td class="adm-list-table-cell">
                        <input type="text" name="PROFILE[CONTACTS][email_def]" id="field_contacts_email_def" size="50" maxlength="255" data-role="profile-name"
                               data-default-name="<?=Loc::getMessage('ACRIT_EXP_FIELD_CODE_DEFAULT');?>"
	                           <?if($intProfileID):?>data-custom-name="true"<?endif?>
                               value="<?=htmlspecialcharsbx($arProfile['CONTACTS']['email_def']);?>" />
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
<?
$obTabControl->EndCustomField('PROFILE[CONTACTS][table_compare]');
?>