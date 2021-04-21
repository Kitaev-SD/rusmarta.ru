<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
if(!CModule::IncludeModule('form')) return;
if(!CModule::IncludeModule('main')) return;

$dbSite = CSite::GetByID(WIZARD_SITE_ID);
if($arSite = $dbSite->Fetch()){
	$lang = $arSite['LANGUAGE_ID'];
}

if(strlen($lang) <= 0){
	$lang = 'ru';
}

WizardServices::IncludeServiceLang('forms.php', $lang);

$eventTypeExists = false;
$db_res = CEventType::GetList(array("TYPE_ID" => "SMS_USER_AUTH_CODE"));
if($db_res){
	$count = $db_res->SelectedRowsCount();
	if($count > 0){
		$eventTypeExists = true;
	}
}
if(!$eventTypeExists){
	$oEventType = new CEventType();
	$arFields = array(
		'LID' => $lang,
		'EVENT_NAME' => 'SMS_USER_AUTH_CODE',
		'EVENT_TYPE' => 'sms',
		'NAME' => GetMessage('PHONE_AUTH_EVENT_NAME_'.$lang),
		'DESCRIPTION' => GetMessage('PHONE_AUTH_EVENT_DESCRIPTION_'.$lang),
	);
	$oEventTypeSrcID = $oEventType->Add($arFields);

	if($oEventTypeSrcID){
		unset($oEventType);
		$oEventType = new CEventType;
		$arFields['LID'] = ($lang === 'ru' ? 'en' : 'ru');
		$arFields['NAME'] = GetMessage('PHONE_AUTH_EVENT_NAME_'.($lang === 'ru' ? 'en' : 'ru'));
		$arFields['DESCRIPTION'] = GetMessage('PHONE_AUTH_EVENT_DESCRIPTION_'.($lang === 'ru' ? 'en' : 'ru'));
		$oEventType->Add($arFields);
	}
}

$arSmsTemplate = \Bitrix\Main\Sms\TemplateTable::getList(array(
	'filter' => array('EVENT_NAME' => 'SMS_USER_AUTH_CODE')
))->fetch();
if(!$arSmsTemplate){
	$entity = \Bitrix\Main\Sms\TemplateTable::getEntity();
	$template = $entity->createObject();
	$template->setEventName('SMS_USER_AUTH_CODE');
	$template->set('ACTIVE', 'Y');
	$template->set('SENDER', '#DEFAULT_SENDER#');
	$template->set('RECEIVER', '#USER_PHONE#');
	$template->set('MESSAGE', GetMessage('PHONE_AUTH_TEMPLATE_MESSAGE'));
	$template->save();
}

$arSmsTemplate = \Bitrix\Main\Sms\TemplateTable::getList(array(
	'filter' => array('EVENT_NAME' => 'SMS_USER_AUTH_CODE')
))->fetch();
if($arSmsTemplate){
	$template = \Bitrix\Main\Sms\TemplateTable::getById($arSmsTemplate['ID'])->fetchObject();
	$template->fillSites();
	$templateSites = $template->getSites();
	$arTemplateSitesIDs = $templateSites ? $templateSites->getLidList() : array();
	if(!in_array(WIZARD_SITE_ID, $arTemplateSitesIDs)){
		$site = \Bitrix\Main\SiteTable::getEntity()->wakeUpObject(WIZARD_SITE_ID);
		$template->addToSites($site);
		$saveResult = $template->save();
	}
}

$arSmsTemplate = \Bitrix\Main\Sms\TemplateTable::getList(array(
	'filter' => array('EVENT_NAME' => 'SMS_USER_RESTORE_PASSWORD')
))->fetch();
if($arSmsTemplate){
	$template = \Bitrix\Main\Sms\TemplateTable::getById($arSmsTemplate['ID'])->fetchObject();
	$template->fillSites();
	$templateSites = $template->getSites();
	$arTemplateSitesIDs = $templateSites ? $templateSites->getLidList() : array();
	if(!in_array(WIZARD_SITE_ID, $arTemplateSitesIDs)){
		$site = \Bitrix\Main\SiteTable::getEntity()->wakeUpObject(WIZARD_SITE_ID);
		$template->addToSites($site);
		$saveResult = $template->save();
	}
}

$arSmsTemplate = \Bitrix\Main\Sms\TemplateTable::getList(array(
	'filter' => array('EVENT_NAME' => 'SMS_USER_CONFIRM_NUMBER')
))->fetch();
if($arSmsTemplate){
	$template = \Bitrix\Main\Sms\TemplateTable::getById($arSmsTemplate['ID'])->fetchObject();
	$template->fillSites();
	$templateSites = $template->getSites();
	$arTemplateSitesIDs = $templateSites ? $templateSites->getLidList() : array();
	if(!in_array(WIZARD_SITE_ID, $arTemplateSitesIDs)){
		$site = \Bitrix\Main\SiteTable::getEntity()->wakeUpObject(WIZARD_SITE_ID);
		$template->addToSites($site);
		$saveResult = $template->save();
	}
}
?>