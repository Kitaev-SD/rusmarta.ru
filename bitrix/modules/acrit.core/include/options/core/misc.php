<?
namespace Acrit\Core\Export;

use
	\Acrit\Core\Helper,
	\Acrit\Core\Log;

Helper::loadMessages(__FILE__);

return [
	'NAME' => Helper::getMessage('ACRIT_CORE_TAB_GENERAL_GROUP_MISC'),
	'OPTIONS' => [
		'allow_external_request' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_ALLOW_EXTERNAL_REQUEST'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_ALLOW_EXTERNAL_REQUEST_HINT'),
			'TYPE' => 'checkbox',
		],
		'bitrixcloud_monitoring' => [
			'NAME' => Helper::getMessage('ACRIT_CORE_OPTION_BITRIXCLOUD_MONITORING'),
			'HINT' => Helper::getMessage('ACRIT_CORE_OPTION_BITRIXCLOUD_MONITORING_HINT'),
			'TYPE' => 'checkbox',
			'CALLBACK_ENABLE' => function($obOptions, $arOption, $strOption){
				if(!\Bitrix\Main\Loader::includeModule('bitrixcloud')){
					return false;
				}
			},
			'CALLBACK_SAVE' => function($obOptions, $arOption, $strOption){
				if($arOption['VALUE_OLD'] != $arOption['VALUE_NEW']){
					if($arOption['VALUE_NEW'] == 'Y'){
						$bResult = \Acrit\Core\Helper::startBitrixCloudMonitoring('admin@acrit.ru');
						if(!$bResult && Helper::strlen(Helper::getLastMonitoringError())){
							Log::getInstance(ACRIT_CORE)->add(Helper::getMessage('ACRIT_CORE_OPTION_BITRIXCLOUD_MONITORING_ERROR_ON', [
								'#ERROR#' => Helper::getLastMonitoringError(),
							]));
						}
					}
					elseif($arOption['VALUE_NEW'] == 'N'){
						$bResult = \Acrit\Core\Helper::stopBitrixCloudMonitoring('admin@acrit.ru');
						if(!$bResult && Helper::strlen(Helper::getLastMonitoringError())){
							Log::getInstance(ACRIT_CORE)->add(Helper::getMessage('ACRIT_CORE_OPTION_BITRIXCLOUD_MONITORING_ERROR_OFF', [
								'#ERROR#' => Helper::getLastMonitoringError(),
							]));
						}
					}
					if(!$bResult){
						Helper::setOption(ACRIT_CORE, $strOption, $arOption['VALUE_OLD']);
					}
				}
			},
		],
	],
];
?>