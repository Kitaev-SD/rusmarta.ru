<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

#$arAvailableFields = ProfileIBlock::getAvailableElementFields($intIBlockID);
$arAvailableFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockID]);
if($intIBlockParentID) {
	#$arAvailableParentFields = ProfileIBlock::getAvailableElementFields($intIBlockParentID);
	$arAvailableParentFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockParentID]);
}
if($intIBlockOffersID) {
	#$arAvailableOfferFields = ProfileIBlock::getAvailableElementFields($intIBlockOffersID);
	$arAvailableOfferFields = Helper::call($strModuleId, 'ProfileIBlock', 'getAvailableElementFields', [$intIBlockOffersID]);
}

$arQuery = [
	'filter' => [
		'PROFILE_ID' => $intProfileID,
		'IBLOCK_ID' => $intIBlockID,

	],
	'select' => ['DEFAULT_FIELD'],
];
$arExistFields = [];
$resAdditionalFields = Helper::call($strModuleId, 'AdditionalField', 'getList', [$arQuery]);
while($arAdditionalField = $resAdditionalFields->fetch()){
	$arExistFields[$arAdditionalField['DEFAULT_FIELD']] = true;
}

?>
<style>
select[data-role="select-additional-fields"] option[data-exists=Y]{
	background-color:#c7e4c7;
}
</style>
<select data-role="select-additional-fields" size="14" multiple="multiple" style="height:100%; width:100%;">
	<optgroup label="<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_IBLOCK_CURRENT');?>">
		<?foreach($arAvailableFields['properties']['ITEMS'] as $arItem):?>
			<?
			$arMore = array(
				$arItem['ID'],
				$arItem['CODE'],
				$arItem['DATA']['PROPERTY_TYPE'].($arItem['DATA']['USER_TYPE']?':'.$arItem['DATA']['USER_TYPE']:''),
			);
			$strValueId = $arItem['ID'];
			$strPrefix = $arItem['IS_PROPERTY'] ? 'PROPERTY_' : '';
			$strCode = $strPrefix.$arItem['CODE'];
			$bExists = !!$arExistFields[$strCode];
			$strExists = $bExists ? ' data-exists="Y"' : '';
			?>
			<option value="<?=$strValueId;?>" data-code="<?=$strCode;?>"<?=$strExists;?>><?=$arItem['NAME'];?> [<?=implode(', ', $arMore);?>]</option>
		<?endforeach?>
	</optgroup>
	<?if($intIBlockParentID):?>
		<optgroup label="<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_IBLOCK_PARENT');?>">
			<?foreach($arAvailableParentFields['properties']['ITEMS'] as $arItem):?>
				<?
				$arMore = array(
					$arItem['ID'],
					$arItem['CODE'],
					$arItem['DATA']['PROPERTY_TYPE'].($arItem['DATA']['USER_TYPE']?':'.$arItem['DATA']['USER_TYPE']:''),
				);
				$strValueId = sprintf('PARENT.%s', $arItem['ID']);
				$strPrefix = $arItem['IS_PROPERTY'] ? 'PROPERTY_' : '';
				$strCode = sprintf('PARENT.%s', $strPrefix.$arItem['CODE']);
				$bExists = !!$arExistFields[$strCode];
				$strExists = $bExists ? ' data-exists="Y"' : '';
				?>
				<option value="<?=$strValueId;?>" data-code="<?=$strCode;?>"<?=$strExists;?>><?=$arItem['NAME'];?> [<?=implode(', ', $arMore);?>]</option>
			<?endforeach?>
		</optgroup>
	<?endif?>
	<?if($intIBlockOffersID):?>
		<optgroup label="<?=Loc::getMessage('ACRIT_EXP_POPUP_ADDITIONAL_FIELDS_IBLOCK_OFFERS');?>">
			<?foreach($arAvailableOfferFields['properties']['ITEMS'] as $arItem):?>
				<?
				$arMore = array(
					$arItem['ID'],
					$arItem['CODE'],
					$arItem['DATA']['PROPERTY_TYPE'].($arItem['DATA']['USER_TYPE']?':'.$arItem['DATA']['USER_TYPE']:''),
				);
				$strValueId = sprintf('OFFER.%s', $arItem['ID']);
				$strPrefix = $arItem['IS_PROPERTY'] ? 'PROPERTY_' : '';
				$strCode = sprintf('OFFER.%s', $strPrefix.$arItem['CODE']);
				$bExists = !!$arExistFields[$strCode];
				$strExists = $bExists ? ' data-exists="Y"' : '';
				?>
				<option value="<?=$strValueId;?>" data-code="<?=$strCode;?>"<?=$strExists;?>><?=$arItem['NAME'];?> [<?=implode(', ', $arMore);?>]</option>
			<?endforeach?>
		</optgroup>
	<?endif?>
</select>
