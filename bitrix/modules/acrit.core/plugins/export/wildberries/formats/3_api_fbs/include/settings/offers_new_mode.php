<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[PARAMS][OFFERS_NEW_MODE]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[PARAMS][OFFERS_NEW_MODE]" value="Y"
			<?if($this->arParams['OFFERS_NEW_MODE'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_wildberries_offers_new_mode" />
		<span><?=static::getMessage('OFFERS_NEW_MODE_CHECKBOX');?></span>
		<?=Helper::showHint(static::getMessage('OFFERS_NEW_MODE_HINT'));?>
	</label>
</div>
