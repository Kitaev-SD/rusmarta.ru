<?php
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

?>

<style>
.acrit_core_export_results{}
</style>
<div>
	<table class="acrit_core_export_results" data-role="acrit_core_export_results">
		<tbody>
			<?foreach($arResult['ITEMS'] as $strCode => $arItem):?>
				<?
				if($arItem['BOLD']){
					$arItem['NAME'] = sprintf('<b>%s</b>', $arItem['NAME']);
				}
				?>
				<tr data-code="<?=$strCode;?>" data-type="<?=$arItem['TYPE'];?>" 
					title="<?=htmlspecialcharsbx($arItem['HINT']);?>">
					<td class="acrit_core_export_results_name"><?=$arItem['NAME'];?>:</td>
					<td class="acrit_core_export_results_text"><?=$arItem['TEXT'];?></td>
				</tr>
			<?endforeach?>
		</tbody>
	</table>
</div>

<br/>

<div>
	<?=preg_replace('#title=".*?"#s', '', $this->showFileOpenLink(false, false, true));?>
</div>

<br/>

<div>
	<?$strUrl = sprintf('/bitrix/admin/%s_new_edit.php?ID=%d&lang=%s', str_replace('.', '_', $this->strModuleId), 
		$this->arProfile['ID'], LANGUAGE_ID);?>
	<a href="<?=Helper::siteUrl($this->arProfile['DOMAIN'], $this->arProfile['IS_HTTPS'] == 'Y', $strUrl)?>"
		class="acrit-exp-file-open-link"><?=Helper::getMessage('ACRIT_EXP_RESULTS_GO_TO_PROFILE', 
			['#PROFILE_ID#' => $this->arProfile['ID']]);?></a>
</div>