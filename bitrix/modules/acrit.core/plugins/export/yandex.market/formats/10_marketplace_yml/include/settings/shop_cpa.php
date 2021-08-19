<?
namespace Acrit\Core\Export\Plugins;

use
	\Acrit\Core\Helper;

Helper::loadMessages();

?>

<div>
	<input type="text" name="PROFILE[PARAMS][SHOP_CPA]" value="<?=htmlspecialcharsbx($this->arParams['SHOP_CPA']);?>"
		data-role="acrit_exp_yandex_marketplace_shop_cpa" size="3" maxlength="1" />
</div>
