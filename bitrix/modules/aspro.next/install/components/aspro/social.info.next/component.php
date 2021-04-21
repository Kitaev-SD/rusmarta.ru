<?
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if(!isset($arParams['CACHE_TIME']))
	$arParams['CACHE_TIME'] = 36000000;
if(\Bitrix\Main\Loader::includeModule('aspro.next'))
{
	if($this->getTemplateName() == 'mail')
	{
		// include CMainPage
		require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
		// get site_id by host
		$obMainPage = new CMainPage();
		$site_id = $obMainPage->GetSiteByHost();
		if(!$site_id || $site_id == "ru")
		    $site_id = "s1";
		$arFrontParametrs = CNext::GetFrontParametrsValues($site_id);
	}
	else
		$arFrontParametrs = CNext::GetFrontParametrsValues(SITE_ID);
	$arResult['SOCIAL_VK'] = $arFrontParametrs['SOCIAL_VK'];
	$arResult['SOCIAL_FACEBOOK'] = $arFrontParametrs['SOCIAL_FACEBOOK'];
	$arResult['SOCIAL_TWITTER'] = $arFrontParametrs['SOCIAL_TWITTER'];
	$arResult['SOCIAL_INSTAGRAM'] = $arFrontParametrs['SOCIAL_INSTAGRAM'];
	$arResult['SOCIAL_TELEGRAM'] = $arFrontParametrs['SOCIAL_TELEGRAM'];
	$arResult['SOCIAL_ODNOKLASSNIKI'] = $arFrontParametrs['SOCIAL_ODNOKLASSNIKI'];
	$arResult['SOCIAL_GOOGLEPLUS'] = $arFrontParametrs['SOCIAL_GOOGLEPLUS'];
	$arResult['SOCIAL_YOUTUBE'] = $arFrontParametrs['SOCIAL_YOUTUBE'];
	$arResult['SOCIAL_MAIL'] = $arFrontParametrs['SOCIAL_MAIL'];

	if($this->StartResultCache(false, array(($arParams['CACHE_GROUPS'] === 'N'? false : $USER->GetGroups()), $arResult, $bUSER_HAVE_ACCESS, $arNavigation))){
		$this->SetResultCacheKeys(array(
			'SOCIAL_VK',
			'SOCIAL_FACEBOOK',
			'SOCIAL_TWITTER',
			'SOCIAL_INSTAGRAM',
			'SOCIAL_ODNOKLASSNIKI',
			'SOCIAL_GOOGLEPLUS',
			'SOCIAL_YOUTUBE',
			'SOCIAL_MAIL',
		));

		$this->IncludeComponentTemplate();
	}
}
?>
