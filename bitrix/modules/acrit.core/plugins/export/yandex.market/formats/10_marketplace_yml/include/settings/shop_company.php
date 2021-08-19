<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][SHOP_COMPANY]" value="<?=htmlspecialcharsbx($this->arParams['SHOP_COMPANY']);?>"
		data-role="acrit_exp_yandex_marketplace_shop_company" size="50" />
</div>
