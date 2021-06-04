<?
namespace Acrit\Core\Export\Plugins;

?>

<input type="text" name="PROFILE[PARAMS][AUTH_PHONE]" size="26" maxlength="20" spellcheck="false"
	data-role="acrit_exp_wildberries_auth_phone" value="<?=htmlspecialcharsbx($this->arParams['AUTH_PHONE']);?>" 
	placeholder="<?=static::getMessage('AUTH_PHONE');?>" />
<input type="button" data-role="acrit_exp_wildberries_auth_button" value="<?=static::getMessage('AUTH_BUTTON');?>">