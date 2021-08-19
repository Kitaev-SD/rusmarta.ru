<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][SHOP_NAME]" value="<?=htmlspecialcharsbx($this->arParams['SHOP_NAME']);?>"
		data-role="acrit_exp_yandex_marketplace_shop_name" size="50" maxlength="20" />
</div>
