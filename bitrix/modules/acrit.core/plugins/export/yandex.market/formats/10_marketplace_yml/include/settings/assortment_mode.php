<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

$arModes = [
	'old' => static::getMessage('ASSORTMENT_MODE_OLD'),
	'new' => static::getMessage('ASSORTMENT_MODE_NEW'),
];
$arModes = [
	'reference' => array_values($arModes),
	'reference_id' => array_keys($arModes),
];
return selectBoxFromArray('PROFILE[PARAMS][ASSORTMENT_MODE]', $arModes, $this->arParams['ASSORTMENT_MODE'], false,
	'data-role="assortment-mode"');
