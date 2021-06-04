<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$strTokenUrl = 'https://suppliers-portal.ozon.ru/marketplace-pass/api-access';

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][STOCK_AND_PRICE]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][STOCK_AND_PRICE]" value="Y"
			<?if($this->arParams['STOCK_AND_PRICE'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_ozon_stock_and_price" />
		<span><?=static::getMessage('STOCK_AND_PRICE_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('STOCK_AND_PRICE_HINT'));?>
</div>

