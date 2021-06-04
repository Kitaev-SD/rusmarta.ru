<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

?>

<input type="text" name="PROFILE[PARAMS][SUPPLIER_ID]" size="40" maxlength="36" spellcheck="false"
	data-role="acrit_exp_wildberries_supplier_id" value="<?=htmlspecialcharsbx($this->arParams['SUPPLIER_ID']);?>" 
	placeholder="<?=static::getMessage('SUPPLIER_ID');?>" />