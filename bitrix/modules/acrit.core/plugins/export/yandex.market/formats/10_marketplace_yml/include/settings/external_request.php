<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<div>
	<input type="hidden" name="PROFILE[EXTERNAL_REQUEST]" value="N" />
	<label>
		<input type="checkbox" name="PROFILE[EXTERNAL_REQUEST]" value="Y"
			<?if($this->arProfile['EXTERNAL_REQUEST'] == 'Y'):?> checked="Y"<?endif?>
			data-role="acrit_exp_yandex_marketplace_external_request" />
		<span><?=static::getMessage('EXTERNAL_REQUEST_CHECKBOX');?></span>
	</label>
	<?=Helper::showHint(static::getMessage('EXTERNAL_REQUEST_HINT'));?>
</div>

<div data-role="acrit_exp_yandex_marketplace_external_request_wrapper">
	<div style="padding-top:8px;">
		<div style="margin-bottom:6px;">
			<input type="text" name="PROFILE[EXTERNAL_REQUEST_URL]" size="60" maxlength="255"
				placeholder="<?=static::getMessage('EXTERNAL_REQUEST_URL');?>"
				value="<?=htmlspecialcharsbx($this->arProfile['EXTERNAL_REQUEST_URL']);?>" />
		</div>
		<?if(Helper::getOption(ACRIT_CORE, 'allow_external_request') != 'Y'):?>
			<?=Helper::showNote(static::getMessage('EXTERNAL_REQUEST_OFF', [
				'#LANGUAGE_ID#' => LANGUAGE_ID,
				'#MODULE_ID#' => ACRIT_CORE,
			]), true);?>
		<?endif?>
	</div>
</div>
