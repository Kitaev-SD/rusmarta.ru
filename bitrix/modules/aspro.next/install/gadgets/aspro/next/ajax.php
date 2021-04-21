<?
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

if($_REQUEST && isset($_REQUEST['mid'])){
	if(strlen($moduleID = htmlspecialcharsbx($_REQUEST['mid']))){
		require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/classes/general/update_client_partner.php');
		__IncludeLang(dirname(__FILE__).'/lang/'.LANGUAGE_ID.'/index.php');

		if(!function_exists('_vail_')){
			function _vail_($count, $arStr, $bStrOnly = false) {
				$ost10 = $count % 10;
				$ost100 = $count % 100;
				if(!$count || !$ost10 || ($ost100 > 10 && $ost100 < 20))
					return (!$bStrOnly ? intval($count).' ' : '').$arStr[2];
				if($ost10 == 1)
					return (!$bStrOnly ? intval($count).' ' : '').$arStr[0];
				if($ost10 > 1 && $ost10 < 5)
					return (!$bStrOnly ? intval($count).' ' : '').$arStr[1];
				return (!$bStrOnly ? intval($count).' ' : '').$arStr[2];
			}
		}

		$arInfo = array();
		$arRequestedModules = array('aspro.'.$moduleID);
		$arUpdateList = CUpdateClientPartner::GetUpdatesList($errorMessage, LANG, 'Y', $arRequestedModules, array('fullmoduleinfo' => 'Y'));
		if($arUpdateList && isset($arUpdateList['MODULE'])){
			foreach($arUpdateList['MODULE'] as $arModule){
				if($arModule['@']['ID'] === 'aspro.'.$moduleID){
					$arInfo = $arModule['@'];
					break;
				}
			}
		}

		if(!$arInfo && $arUpdateList && isset($arUpdateList['ERROR']) && isset($arUpdateList['CLIENT'])){
			if($arUpdateList['ERROR'][0]['@']['TYPE'] === 'SYS_ERROR_AB_ACTIVE'){
				$arInfo = $arUpdateList['CLIENT'][0]['@'];
			}
		}
		?>
		<?if($arInfo):?>
			<?
			if($dateSupportTo = strtotime($arInfo['DATE_TO'])){
				$dateSupportTo += 86399;
				$bSupportActive = $dateSupportTo >= time();
				$bSupportLess14 = $bSupportActive && ($dateSupportTo - 1209599 < time());
				$bSupportExpired = $dateSupportTo < time();
			}
			?>
			<?if($bSupportLess14 || !$bSupportActive):?>
				<?if($bSupportActive):?>
					<?$cnt = floor(($dateSupportTo - time()) / 86400)?>
					<div class="aspro-gadgets-title2 pink"><?=GetMessage('GD_ASPRO_EXPIRED_SOON', array('#DAYS_STR#' => ($cnt ? GetMessage('GD_ASPRO_THROUGH')._vail_($cnt, array(GetMessage('GD_ASPRO_DAYS0'), GetMessage('GD_ASPRO_DAYS1'), GetMessage('GD_ASPRO_DAYS2'))) : GetMessage('GD_ASPRO_DAYS0_TODAY'))))?></div>
				<?else:?>
					<div class="aspro-gadgets-title2 pink"><?=GetMessage('GD_ASPRO_EXPIRED')?></div>
				<?endif;?>
				<div class="aspro-gadget-bottom">
					<a class="aspro-gadgets-button" href="/bitrix/admin/aspro.<?=$moduleID?>_mc.php">
						<div class="aspro-gadgets-button-lamp"></div>
						<div class="aspro-gadgets-button-text"><?=GetMessage('GD_ASPRO_GET_MORE')?></div>
					</a>
					<a class="aspro-gadgets-button aspro-gadgets-button-buy" href="https://aspro.ru/support_marketplace/" target="_blank">
						<div class="aspro-gadgets-button-text"><?=GetMessage('GD_ASPRO_BUY')?></div>
					</a>
				</div>
			<?else:?>
				<div class="aspro-gadgets-title2"><?=GetMessage('GD_ASPRO_DATE_SUPPORT_TO', array('#DATE#' => date('d.m.Y', $dateSupportTo)))?></div>
				<div class="aspro-gadget-bottom">
					<a class="aspro-gadgets-button" href="/bitrix/admin/aspro.<?=$moduleID?>_mc.php">
						<div class="aspro-gadgets-button-lamp"></div>
						<div class="aspro-gadgets-button-text"><?=GetMessage('GD_ASPRO_GET_MORE')?></div>
					</a>
					<span class="aspro-gadgets-desc"><?=GetMessage('GD_ASPRO_DESCRIPTION')?></span>
				</div>
			<?endif;?>
		<?else:?>
			<?if($arUpdateList && isset($arUpdateList['ERROR'])):?>
				<div class="aspro-gadgets-title2 pink"><?=$arUpdateList['ERROR'][0]['#'].' ('.$arUpdateList['ERROR'][0]['@']['TYPE'].')'?></div>
			<?else:?>
				<div class="aspro-gadgets-title2 pink"><?=GetMessage('GD_ASPRO_ERROR')?></div>
			<?endif;?>
		<?endif;?>
		<?
	}
}
?>