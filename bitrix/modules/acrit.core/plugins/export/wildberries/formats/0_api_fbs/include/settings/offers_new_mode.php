<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<?
	$arOffersMode = [
		'N' => static::getMessage('OFFERS_NEW_MODE_GENERAL'),
		'Y' => static::getMessage('OFFERS_NEW_MODE_PRODUCT'),
		'X' => static::getMessage('OFFERS_NEW_MODE_NOMENCLATURE'),
	];
	$arOffersMode = array(
		'REFERENCE' => array_values($arOffersMode),
		'REFERENCE_ID' => array_keys($arOffersMode),
	);
	print SelectBoxFromArray('PROFILE[PARAMS][OFFERS_NEW_MODE]', $arOffersMode,
		$this->arProfile['PARAMS']['OFFERS_NEW_MODE'], '', 'id="acrit_exp_plugin_new_offers_mode"');
	?>
</div>
