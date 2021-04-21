<? if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
global $APPLICATION;
/** @var array $arResult */

CJSCore::Init(array('popup'));
use Bitrix\Main\Localization\Loc;

/** @var \Bitrix\MessageService\Sender\Sms\Twilio $sender */
$sender = $arResult['sender'];

$messageSuffix = (defined('ADMIN_SECTION') && ADMIN_SECTION === true) ? '_ADMIN' : '';

if ($sender->isRegistered())
{
	if (defined('SITE_TEMPLATE_ID') && SITE_TEMPLATE_ID === 'bitrix24')
	{
		$bodyClass = $APPLICATION->GetPageProperty('BodyClass');
		$APPLICATION->SetPageProperty('BodyClass', ($bodyClass ? $bodyClass.' ' : '').'pagetitle-toolbar-field-view flexible-layout crm-toolbar crm-pagetitle-view');
	}

	if (!defined('ADMIN_SECTION'))
	{
		$this->SetViewTarget('inside_pagetitle', 10000);
	}

	?><div id="messageservice_toolbar" class="pagetitle-container pagetitle-align-right-container">
    <br>
	
	</div><?
	if (!defined('ADMIN_SECTION'))
	{
		$this->EndViewTarget();
	}
}
?>
<div class="sms-settings">
	<h2 class="sms-settings-title"><?= Loc::getMessage("MESSAGESERVICE_CONFIG_SENDER_SMS_TITLE")?></h2>
	<h3 class="sms-settings-title-paragraph"><?= Loc::getMessage("MESSAGESERVICE_CONFIG_SENDER_SMS_TITLE_2")?></h3>
	<div class="sms-settings-cover-container">
		<div class="sms-settings-cover"></div>
	</div>
	
	<div class="sms-settings-border"></div>
	<h3 class="sms-settings-title-paragraph"><?= Loc::getMessage("MESSAGESERVICE_CONFIG_SENDER_SMS_FEATURES_TITLE")?></h3>
	<div class="sms-settings-description">
		<p><?= Loc::getMessage("MESSAGESERVICE_CONFIG_SENDER_SMS_FEATURES_LIST_DESCRIPTION".$messageSuffix)?></p>
		
	</div>
    <br>
</div>
