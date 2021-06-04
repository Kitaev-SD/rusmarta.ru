<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\SystemException;
use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!$params['error']): ?>
    <FORM ACTION="<?php echo $params['request']->PaymentURL; ?>" METHOD="get">
        <INPUT TYPE="SUBMIT" VALUE="<? echo GetMessage("SALE_TINKOFF_PAYBUTTON_NAME") ?>">
    </FORM>
<?php else: ?>
    <b><?php echo $params['error']; ?></b>
<?php endif; ?>
