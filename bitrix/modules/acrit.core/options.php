<?
namespace Acrit\Core;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log,
	\Acrit\Core\Options;

# Include module
$strModuleId = pathinfo(__DIR__, PATHINFO_BASENAME);
\Bitrix\Main\Loader::includeModule($strModuleId);
Helper::loadMessages(__FILE__);

# Check rights
$strRight = $APPLICATION->getGroupRight($strModuleId);
if($strRight < 'R'){
	return;
}

# Tabs
$arTabs = [
	[
		'DIV' => 'general',
		'TAB' => Helper::getMessage('ACRIT_CORE_TAB_GENERAL_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_CORE_TAB_GENERAL_DESC'),
		'OPTIONS' => [
			'core/php.php',
			'core/log.php',
			'core/discount_recalculation.php',
			'core/google_tagmanager.php',
			'core/google_pagespeed.php',
			'core/remarketing.php',
			'core/menu.php',
			'core/updates.php',
			'core/misc.php',
		],
	], [
		'DIV' => 'log',
		'TAB' => Helper::getMessage('ACRIT_CORE_TAB_LOG_NAME'),
		'TITLE' => Helper::getMessage('ACRIT_CORE_TAB_LOG_DESC'),
		'CALLBACK' => function($obOptions, $arTab){
			?>
				<tr>
					<td>
						<?=Log::getInstance($obOptions->getModuleId())->showLog();?>
					</td>
				</tr>
			<?
		},
	], [
		'DIV' => 'rights',
		'TAB' => Helper::getMessage('MAIN_TAB_RIGHTS'),
		'TITLE' => Helper::getMessage('MAIN_TAB_TITLE_RIGHTS'),
		'RIGHTS' => true,
	],
];

# Display all
$obOptions = new Options($strModuleId, $arTabs, [
	'DISABLED' => $strRight <= 'R',
]);

?>