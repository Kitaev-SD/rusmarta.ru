<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

$refreshToken = $this->intProfileId ? $this->getRefreshToken() : '';

?>

<div>
	<input readonly type="text" value="<?=htmlspecialcharsbx($refreshToken);?>" size="26"
		placeholder="<?=static::getMessage('TOKEN_EMPTY');?>"
		data-role="acrit_exp_wildberries_refresh_token" />
	<span data-role="acrit_exp_wildberries_token_status"></span>
	<input type="button" value="<?=static::getMessage('TOKEN_CHECK');?>"
		data-role="acrit_exp_wildberries_export_stock_token" />
	<input type="button" value="<?=static::getMessage('TOKEN_CLEAR');?>"
		data-role="acrit_exp_wildberries_token_clear"
		data-confirm="<?=static::getMessage('TOKEN_CLEAR_CONFIRM');?>" />
</div>

<input type="hidden" data-role="acrit_exp_wildberries_token_error_supplier_id" 
	value="<?=static::getMessage('TOKEN_ERROR_SUPPLIER_ID');?>" />
<input type="hidden" data-role="acrit_exp_wildberries_token_error_refresh_token" 
	value="<?=static::getMessage('TOKEN_ERROR_REFRESH_TOKEN');?>" />
