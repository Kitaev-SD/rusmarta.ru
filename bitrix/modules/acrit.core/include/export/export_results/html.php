<?php
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

?>

<table class="acrit_core_export_results" data-role="acrit_core_export_results">
	<tbody>
		<?foreach($arResult['ITEMS'] as $strCode => $arItem):?>
			<?
			if($arItem['BOLD']){
				$arItem['NAME'] = sprintf('<b>%s</b>', $arItem['NAME']);
			}
			?>
			<tr data-code="<?=$strCode;?>" data-type="<?=$arItem['TYPE'];?>">
				<td class="acrit_core_export_results_name"><?=$arItem['NAME'];?>:</td>
				<td class="acrit_core_export_results_text"><?=$arItem['TEXT'];?></td>
				<td class="acrit_core_export_results_hint"><?=Helper::showHint($arItem['HINT']);?></td>
			</tr>
		<?endforeach?>
	</tbody>
</table>

<?=$this->showFileOpenLink(false, false, true);?>
