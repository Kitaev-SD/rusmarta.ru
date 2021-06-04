<?
namespace Acrit\Core\Export;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

return [
	'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_GROUP_EMAIL'),
	'OPTIONS' => [
		'send_email' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_SEND_EMAIL'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_SEND_EMAIL_HINT'),
			'TYPE' => 'checkbox',
			'HEAD_DATA' => function($obOptions, $arOption, $strOption){
				$strModuleIdU = $obOptions->getModuleIdUnderlined();
				?>
				<script>
				$(document).on('change', '#<?=$strModuleIdU;?>_row_option_send_email input[type=checkbox]', function(e){
					let inputs = $('#<?=$strModuleIdU;?>_row_option_admin_email');
					inputs.toggle($(this).is(':checked') && !$(this).is('[disabled]'));
				});
				$(document).ready(function(){
					$('#<?=$strModuleIdU;?>_row_option_send_email input[type=checkbox]').trigger('change');
				});
				</script>
				<?
			},
		],
		'admin_email' => [
			'NAME' => Loc::getMessage('ACRIT_CORE_OPTION_ADMIN_EMAIL'),
			'HINT' => Loc::getMessage('ACRIT_CORE_OPTION_ADMIN_EMAIL_HINT'),
			'TYPE' => 'text',
			'ATTR' => 'size="50"',
		],
	],
];
	
?>