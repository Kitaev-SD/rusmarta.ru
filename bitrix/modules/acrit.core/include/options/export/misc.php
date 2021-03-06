<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_MISC'),
	'OPTIONS' => [
		'check_lock' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CHECK_LOCK'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_CHECK_LOCK_HINT'),
			'TYPE' => 'checkbox',
		],
		'delete_element_data_while_exports' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_DELETE_ELEMENT_DATA_WHILE_EXPORTS'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_DELETE_ELEMENT_DATA_WHILE_EXPORTS_HINT'),
			'TYPE' => 'checkbox',
		],
		'show_export_file_basename' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_EXPORT_FILE_BASENAME'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_EXPORT_FILE_BASENAME_HINT'),
			'TYPE' => 'checkbox',
		],
		'show_export_file_with_uniq_argument' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_EXPORT_FILE_WITH_UNIQ_ARGUMENT'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_EXPORT_FILE_WITH_UNIQ_ARGUMENT_HINT'),
			'TYPE' => 'checkbox',
		],
		'show_iblock_multiple_notice' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_IBLOCK_MULTIPLE_NOTICE'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_SHOW_IBLOCK_MULTIPLE_NOTICE_HINT'),
			'TYPE' => 'checkbox',
		],
		'categories_depth' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_CATEGORIES_DEPTH'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_CATEGORIES_DEPTH_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'MAXLENGTH="1"',
		],
		'history_count' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_HISTORY_COUNT'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_HISTORY_COUNT_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'MAXLENGTH="6"',
		],
		'auto_clean_history' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_AUTO_CLEAN_HISTORY'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_AUTO_CLEAN_HISTORY_HINT'),
			'TYPE' => 'checkbox',
			'HEAD_DATA' => function($obOptions, $arOption, $strOption){
				$strModuleIdU = $obOptions->getModuleIdUnderlined();
				?>
				<script>
				$(document).on('change', '#<?=$strModuleIdU;?>_row_option_auto_clean_history input[type=checkbox]', function(e){
					let inputs = $('#<?=$strModuleIdU;?>_row_option_auto_clean_history_days');
					inputs.toggle($(this).is(':checked') && !$(this).is('[disabled]'));
				});
				$(document).ready(function(){
					$('#<?=$strModuleIdU;?>_row_option_auto_clean_history input[type=checkbox]').trigger('change');
				});
				</script>
				<?
			},
			'CALLBACK_SAVE' => function($obOptions, $arOption, $strOption){
				if($arOption['VALUE_OLD'] != $arOption['VALUE_NEW']){
					$arAgent = [
						'MODULE_ID' => ACRIT_CORE,
						'FUNC' => \Acrit\Core\Export\Cleaner::getAgentName($obOptions->getModuleId()),
					];
					if($arOption['VALUE_NEW'] == 'Y'){
						Helper::addAgent($arAgent, true);
					}
					else{
						Helper::removeAgent($arAgent);
					}
				}
			},
		],
		'auto_clean_history_days' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_AUTO_CLEAN_HISTORY_DAYS'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_AUTO_CLEAN_HISTORY_DAYS_HINT'),
			'TYPE' => 'text',
			'CALLBACK_SAVE' => function($obOptions, $arOption, $strOption){
				if(Helper::getOption($obOptions->getModuleId(), 'auto_clean_history') == 'Y'){
					if($arOption['VALUE_OLD'] != $arOption['VALUE_NEW']){
						$arAgent = [
							'MODULE_ID' => ACRIT_CORE,
							'FUNC' => \Acrit\Core\Export\Cleaner::getAgentName($obOptions->getModuleId()),
						];
						Helper::removeAgent($arAgent);
						Helper::addAgent($arAgent, true);
					}
				}
			},
		],
	],
];
	
?>