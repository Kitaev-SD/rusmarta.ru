<?
namespace Acrit\GoogleMerchant;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Options;

# Include module
$strModuleId = pathinfo(__DIR__, PATHINFO_BASENAME);
\Bitrix\Main\Loader::includeModule($strModuleId);
Helper::loadMessages(__FILE__);

# Check rights
$strRight = $APPLICATION->getGroupRight($strModuleId);
if($strRight == 'D'){
	return;
}

# Old core
$bOldCoreDisabled = Helper::getOption($strModuleId, 'disable_old_core') == 'Y';
if(!$bOldCoreDisabled){
	\CJSCore::init('jquery');
	$APPLICATION->AddHeadScript('/bitrix/js/iblock/iblock_edit.js', true);
	$APPLICATION->AddHeadScript('/bitrix/js/'.$strModuleId.'/main.js', true);
}
if(!$bOldCoreDisabled){
	$bExistBitrixCloudMonitoring = \CGooglemerchantInformer::CheckBitrixCloudMonitoring(GetMessage("SC_SET_BITRIX_CLOUD_MONITORING_EMAIL"));
	\AcritLicence::Show();
}

# Tabs
$arTabs = [
	[
		'DIV' => 'general',
		'TAB' => Helper::getMessage('ACRIT_CORE_TAB_GENERAL_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_CORE_TAB_GENERAL_DESC'),
		'OPTIONS' => [
			'core/common.php',
			'export/multithread.php',
			'core/log.php',
			'export/old_core.php',
			'export/email.php',
			'export/misc.php',
		],
	], [
		'DIV' => 'log',
		'TAB' => Helper::getMessage('ACRIT_CORE_TAB_LOG_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_CORE_TAB_LOG_DESC'),
		'CALLBACK' => function($obOptions, $arTab){
			?>
				<tr>
					<td>
						<?=Log::getInstance($obOptions->getModuleId())->showLog();?>
					</td>
				</tr>
			<?
		},
	], [
		'DIV' => 'rights',
		'TAB' => Helper::getMessage('MAIN_TAB_RIGHTS'),
		'TITLE' => Helper::getMessage('MAIN_TAB_TITLE_RIGHTS'),
		'RIGHTS' => true,
	], [
		'DIV' => 'agents',
		'TAB' => Helper::getMessage('ACRIT_CORE_TAB_AGENTS_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_CORE_TAB_AGENTS_DESC'),
		'CALLBACK' => function($obOptions, $arTab){
			?>
				<tr>
					<td class="heading" colspan="2"><?=GetMessage("SC_SET_CRON_AGENTS_OPTIONS");?></td>
				</tr>
				<tr>
					<td colspan="2" class="adm-detail-content-cell" align="center">
						<a class="adm-btn adm-btn-save" onclick="javascript:SetCronAgentOptions();"><?=GetMessage("ACRIT_GOOGLEMERCHANT_SET_CRON_AGENTS_OPTIONS_BUTTON")?></a>
					</td>
				</tr>
				<tr>
					<td class="heading" colspan="2"><?=GetMessage("SC_SET_CRON_AGENTS_INFO");?></td>
				</tr>
				<tr>
					<td colspan="2">
						<?=GetMessage("ACRIT_GOOGLEMERCHANT_SET_CRON_AGENTS_OPTIONS_STEPS");?>
					</td>
				</tr>
			<?
		},
	], [
		'DIV' => 'support',
		'TAB' => Helper::getMessage('ACRIT_CORE_TAB_SUPPORT_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_CORE_TAB_SUPPORT_DESC'),
		'CALLBACK' => function($obOptions, $arTab){
			?>
				<tr>
					<td class="heading" colspan="2"><?=GetMessage("SC_MARKET_CATEGORIES");?></td>
				</tr>
				<tr>
					<td colspan="2" class="adm-detail-content-cell" align="center">
						<a class="adm-btn adm-btn-save" onclick="javascript:UpdateMarketCategories();">
							<?=GetMessage("ACRIT_GOOGLEMERCHANT_MARKET_CATEGORIES_UPDATE_BUTTON");?>
						</a>
					</td>
				</tr>
				<tr>
					<td class="heading" colspan="2"><?=GetMessage("SC_SET_BITRIX_CLOUD_MONITORING_OPTIONS");?></td>
				</tr>
				<tr>
					<td colspan="2" class="adm-detail-content-cell" align="center">
						<a class="adm-btn adm-btn-save" onclick="javascript:UpdateBitrixCloudMonitoring('<?=GetMessage("SC_SET_BITRIX_CLOUD_MONITORING_EMAIL");?>');">
						<?if($bExistBitrixCloudMonitoring){
							echo GetMessage("ACRIT_GOOGLEMERCHANT_SET_BITRIX_CLOUD_MONITORING_BUTTON_OFF");
						}
						else{
							echo GetMessage("ACRIT_GOOGLEMERCHANT_SET_BITRIX_CLOUD_MONITORING_BUTTON_ON");
						}?>
						</a>
					</td>
				</tr>
				<tr>
					<td class="heading" colspan="2"><?=GetMessage("SC_FRM_1");?></td>
				</tr>
				<tr>
					<td valign="top" class="adm-detail-content-cell-l">
						<span class="required">*</span><?=GetMessage("SC_FRM_2");?><br/>
						<small><?=GetMessage("SC_FRM_3");?></small>
					</td>
					<td valign="top" class="adm-detail-content-cell-r">
						<?$arAutoProblemsToSupportMessage = is_array($arAutoProblemsToSupportMessage) ? $arAutoProblemsToSupportMessage : array();?>
						<textarea cols="60" rows="6" name="ticket_text_proxy" id="ticket_text_proxy"><?=htmlspecialcharsbx(implode("\n", $arAutoProblemsToSupportMessage));?></textarea>
					</td>
				</tr>
				<tr>
					<td class="adm-detail-content-cell-l"></td>
					<td class="adm-detail-content-cell-r">
						<input type="button" value="<?=GetMessage("SC_FRM_4");?>" onclick="SubmitToSupport()" name="submit_button">
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?=BeginNote();?>
						<?=GetMessage("SC_TXT_1");?> <a href="<?=GetMessage("A_SUPPORT_URL");?>"><?=GetMessage("A_SUPPORT_URL");?></a>
						<?=EndNote();?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?=GetMessage("ACRIT_GOOGLEMERCHANT_RECOMMENDS");?>
					</td>
				</tr>
			<?
		},
	],
];

if($bOldCoreDisabled){
	$arTabs = array_slice($arTabs, 0, -2);
}

# Display all
$obOptions = new Options($strModuleId, $arTabs, [
	'DISABLED' => $strRight <= 'R',
]);

if(!$bOldCoreDisabled){
	?>
		<form target="_blank" name="fticket" action="<?=GetMessage("A_SUPPORT_URL");?>" method="post" accept-charset="windows-1251">
			<input type="hidden" name="send_ticket" value="Y">
			<input type="hidden" name="ticket_title" value="<?=GetMessage("SC_RUS_L1")." ".htmlspecialcharsbx(\CAcritGooglemerchantTools::GetHttpHost());?>">
			<input type="hidden" name="ticket_text" value="Y">
		</form>
		<script type="text/javascript">
			function SubmitToSupport(){
				var frm = document.forms.fticket;
				frm.ticket_text.value = BX('ticket_text_proxy').value;
				if(frm.ticket_text.value == ''){
					alert('<?=GetMessage("SC_NOT_FILLED")?>');
					return;
				}
				frm.submit();
			}
		</script>
	<?
}