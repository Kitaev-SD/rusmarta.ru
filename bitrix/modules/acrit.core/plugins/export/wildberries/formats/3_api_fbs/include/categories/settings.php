<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Export\Plugins\OzonRuHelpers\CategoryTable as Category;
	
$intIBlockID = $arParams['IBLOCK_ID'];
$arIBlockParams = $arParams['IBLOCK_PARAMS'];
?>

<tr id="tr_CATEGORIES_SELECT">
	<td>
		<?=Helper::showHint(static::getMessage('CATEGORIES_SELECT_DESC'));?>
		<?=static::getMessage('CATEGORIES_SELECT');?>
	</td>
	<td>
		<div>
			<input type="button" value="<?=static::getMessage('CATEGORIES_SELECT_BUTTON');?>"
				data-role="acrit_wb_categories_select"
				data-popup-title="<?=static::getMessage('CATEGORIES_SELECT_POPUP_TITLE');?>" />
		</div>
	</td>
</tr>

<tr id="tr_CATEGORIES_LIST">
	<td></td>
	<td>
		<?
		if(empty($arIBlockParams['CATEGORIES_LIST'])){
			$arIBlockParams['CATEGORIES_LIST'] = [''];
		}
		?>
		<div data-role="acrit_wb_categories_list">
			<?foreach($arIBlockParams['CATEGORIES_LIST'] as $strCategoryName):?>
				<div data-role="acrit_wb_categories_item">
					<input type="hidden" name="iblockparams[<?=$intIBlockID;?>][CATEGORIES_LIST][]" 
						value="<?=htmlspecialcharsbx($strCategoryName);?>">
					<div data-role="acrit_wb_categories_item_name"><?=htmlspecialcharsbx($strCategoryName);?></div>
					<div data-role="acrit_wb_categories_item_delete">
						<a class="acrit-inline-link"><?=static::getMessage('CATEGORIES_SELECT_DELETE');?></a>
					</div>
				</div>
			<?endforeach?>
		</div>
	</td>
</tr>

<tr class="heading"><td colspan="2"><?=static::getMessage('HEADING_ATTRIBUTES');?></td></tr>

<tr id="tr_CATEGORIES_UPDATE_ATTRIBUTES">
	<td>
		<?=Helper::showHint(static::getMessage('CAT_UPDATE_ATTR_DESC'));?>
		<?=static::getMessage('CAT_UPDATE_ATTR_NAME');?>
	</td>
	<td>
		<input type="button" value="<?=static::getMessage('CAT_UPDATE_ATTR_BTN_START');?>"
			data-role="categories-update-attributes-start" />
		<input type="button" value="<?=static::getMessage('CAT_UPDATE_ATTR_BTN_STOP');?>"
			data-role="categories-update-attributes-stop" class="hidden" />
		<div data-role="categories-update-attributes-loader" class="hidden"></div>
	</td>
</tr>
<tr id="tr_CATEGORIES_UPDATE_ATTRIBUTES_STATUS" style="display:none;">
	<td></td>
	<td>
		<div data-role="categories-update-attributes-result"></div>
	</td>
</tr>
