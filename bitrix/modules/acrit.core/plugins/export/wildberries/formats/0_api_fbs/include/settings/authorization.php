<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$authToken = $this->intProfileId ? $this->getAuthToken() : '';
?>

<div style="display:inline-block;">
	<input type="text" value="<?=htmlspecialcharsbx($authToken);?>" name="PROFILE[PARAMS][AUTH_TOKEN]" size="40"
		placeholder="<?=static::getMessage('TOKEN_EMPTY');?>"
		data-role="acrit_exp_wildberries_auth_token" />
	<span class="acrit_exp_wildberries_token_status" data-role="acrit_exp_wildberries_auth_token_status"></span>
	<input type="button" value="<?=static::getMessage('TOKEN_CHECK');?>"
		data-role="acrit_exp_wildberries_token_check" />
</div>
<div data-role="acrit_exp_wildberries_token_get" style="display:inline-block;">
	<a href="https://suppliers-portal.wildberries.ru/supplier-settings/access-to-new-api" target="_blank" >
		<?=static::getMessage('TOKEN_GET');?>
	</a>
</div>

<input type="hidden" data-role="acrit_exp_wildberries_token_error_supplier_id" 
	value="<?=static::getMessage('TOKEN_ERROR_SUPPLIER_ID');?>" />
