<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$strTokenUrl = 'https://suppliers-portal.ozon.ru/marketplace-pass/api-access';

?>

<div style="margin-bottom:10px;">
	<input type="hidden" name="PROFILE[PARAMS][ZERO_PRICE_OLD]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][ZERO_PRICE_OLD]" value="Y"
			<?if($this->arParams['ZERO_PRICE_OLD'] != 'N'):?> checked="Y"<?endif?>
			data-role="acrit_exp_ozon_zero_price_old" />
		<span><?=static::getMessage('ZERO_PRICE_OLD_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('ZERO_PRICE_OLD_HINT'));?>
</div>

<div>
	<input type="hidden" name="PROFILE[PARAMS][ZERO_PRICE_PREMIUM]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][ZERO_PRICE_PREMIUM]" value="Y"
			<?if($this->arParams['ZERO_PRICE_PREMIUM'] != 'N'):?> checked="Y"<?endif?>
			data-role="acrit_exp_ozon_zero_price_premium" />
		<span><?=static::getMessage('ZERO_PRICE_PREMIUM_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('ZERO_PRICE_PREMIUM_HINT'));?>
</div>

