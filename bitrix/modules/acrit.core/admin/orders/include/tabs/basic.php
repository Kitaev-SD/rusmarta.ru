<?
namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$obTabControl->AddSection('HEADING_BASIC', Loc::getMessage('ACRIT_CRM_TAB_BASIC_HEADING'));
// Order ID field
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][orderid_field]', Loc::getMessage('ACRIT_CRM_GENERAL_ORDERID_FIELD'));
$orderid_fields = OrdersInfo::getOrderExtIDFields($arProfile);
?>
    <tr id="tr_ORDERID_FIELD">
        <td>
            <label for="field_CONNECT_DATA_ORDERID_FIELD"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][orderid_field]" id="field_CONNECT_DATA_ORDERID_FIELD">
				<?foreach ($orderid_fields as $orderid_field):?>
                <option value="<?=$orderid_field['id'];?>"<?=$arProfile['CONNECT_DATA']['orderid_field'] == $orderid_field['id']?' selected':'';?>><?=$orderid_field['name'];?> [<?=$orderid_field['id'];?>]</option>
				<?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][orderid_field]');

// Code
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][source_id]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_SOURCE_ID'));
?>
    <tr id="tr_connect_data_source_id">
        <td>
            <label for="field_connect_data_source_id"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <input type="text" name="PROFILE[CONNECT_DATA][source_id]" size="50" maxlength="255" data-role="profile-name"
                   data-default-name="<?=Loc::getMessage('ACRIT_EXP_FIELD_CODE_DEFAULT');?>"
			       <?if($intProfileID):?>data-custom-name="true"<?endif?>
                   value="<?=htmlspecialcharsbx($arProfile['CONNECT_DATA']['source_id']);?>" />
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][source_id]');

// Default buyer
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][buyer]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_BUYER'));
//$list = OrdersInfo::getUsers();
$ajax_link = '/bitrix/admin/'.str_replace('.', '_', $strModuleId).'_orders_ajax.php';
$user_sel = false;
if ($arProfile['CONNECT_DATA']['buyer']) {
	$user_sel = OrdersInfo::getUser((int)$arProfile['CONNECT_DATA']['buyer']);
}
?>
    <tr id="tr_connect_data_buyer">
        <td>
            <label for="field_connect_data_buyer"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select class="connect-data-user-search" name="PROFILE[CONNECT_DATA][buyer]">
	            <?if($user_sel):?>
                <option value="<?=$user_sel['id'];?>"><?=$user_sel['name'];?>, <?=$user_sel['code'];?> [<?=$user_sel['id'];?>]</option>
	            <?endif;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][buyer]');

// Default payment type
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][pay_type]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_PAY_TYPE'));
$list = OrdersInfo::getPersonTypes();
?>
    <tr id="tr_connect_data_pay_type">
        <td>
            <label for="field_connect_data_pay_type"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select name="PROFILE[CONNECT_DATA][pay_type]">
	            <?foreach ($list as $item):?>
                <option value="<?=$item['id'];?>"<?=$arProfile['CONNECT_DATA']['pay_type']==$item['id']?' selected':'';?>><?=$item['name'];?> [<?=$item['id'];?>]</option>
	            <?endforeach;?>
            </select>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][pay_type]');

// Default responsible user
$obTabControl->BeginCustomField('PROFILE[CONNECT_DATA][responsible]', Loc::getMessage('ACRIT_CRM_TAB_BASIC_RESPONSIBLE'));
//$list = OrdersInfo::getUsers('СОК');
$user_sel = false;
if ($arProfile['CONNECT_DATA']['responsible']) {
	$user_sel = OrdersInfo::getUser((int)$arProfile['CONNECT_DATA']['responsible']);
}
?>
    <tr id="tr_connect_data_responsible">
        <td>
            <label for="field_connect_data_responsible"><?=$obTabControl->GetCustomLabelHTML()?><label>
        </td>
        <td>
            <select class="connect-data-user-search" name="PROFILE[CONNECT_DATA][responsible]">
                <?if($user_sel):?>
                <option value="<?=$user_sel['id'];?>"><?=$user_sel['name'];?>, <?=$user_sel['code'];?> [<?=$user_sel['id'];?>]</option>
                <?endif;?>
            </select>

            <script>
                $(document).ready(function() {
                    $('.connect-data-user-search').select2({
                        minimumInputLength: 3,
                        width: '390',
                        placeholder: '<?=Loc::getMessage('ACRIT_CRM_TAB_BASIC_USER_SEARCH_PLACEHOLDER');?>',
                        language: 'ru',
                        ajax: {
                            url: "<?=$ajax_link;?>",
                            delay: 250,
                            dataType: 'json',
                            data: function (params) {
                                return {
                                    action: 'find_users',
                                    q: params.term,
                                };
                            },
                            processResults: function (data) {
                                var arr = []
                                $.each(data, function (index, value) {
                                    arr.push({
                                        id: index,
                                        text: value
                                    })
                                })
                                return {
                                    results: arr
                                };
                            },
                        }
                    });
                });
            </script>
        </td>
    </tr>
	<?
$obTabControl->EndCustomField('PROFILE[CONNECT_DATA][responsible]');
