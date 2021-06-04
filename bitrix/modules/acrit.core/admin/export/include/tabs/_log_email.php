<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

$strDefault = Helper::getOption($strModuleId, 'send_email') == 'Y' ? 'Y' : 'N';
$strDefault = Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_SEND_'.$strDefault);

?>
<div data-role="profile-email-wrapper">
	<table class="adm-detail-content-table edit-table" id="general_edit_table" style="opacity: 1;">
		<tbody>
			<tr id="tr_SEND_EMAIL">
				<td class="adm-detail-content-cell-l" width="40%">
					<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_SEND_EMAIL_HINT'));?>
					<?=Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_SEND_EMAIL')?>:
				</td>
				<td class="acrit-exp-select-wrapper adm-detail-content-cell-r">
					<?
					$arOptions = [
						'D' => Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_SEND_D', ['#DEFAULT#' => $strDefault]),
						'Y' => Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_SEND_Y'),
						'C' => Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_SEND_C'),
						'N' => Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_SEND_N'),
					];
					$arOptions = array(
						'REFERENCE' => array_values($arOptions),
						'REFERENCE_ID' => array_keys($arOptions),
					);
					print selectBoxFromArray('PROFILE[PARAMS][SEND_EMAIL]', $arOptions, 
						$arProfile['PARAMS']['SEND_EMAIL'], '', 'data-role="acrit_exp_profile_send_email"', ''
					);
					?>
				</td>
			</tr>
			<tr id="tr_ADMIN_EMAIL">
				<td class="adm-detail-content-cell-l" width="40%">
					<?=Helper::showHint(Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_ADMIN_EMAIL_HINT'));?>
					<?=Helper::getMessage('ACRIT_EXP_TAB_LOG_HISTORY_ADMIN_EMAIL')?>:
				</td>
				<td class="adm-detail-content-cell-r">
					<input type="text" name="PROFILE[PARAMS][ADMIN_EMAIL]" 
						value="<?=htmlspecialcharsbx($arProfile['PARAMS']['ADMIN_EMAIL']);?>" size="50"
						data-role="acrit_exp_profile_admin_email" />
				</td>
			</tr>
		</tbody>
	</table>
</div>