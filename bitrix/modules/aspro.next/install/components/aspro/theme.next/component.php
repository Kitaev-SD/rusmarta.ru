<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<?if(!\Bitrix\Main\Loader::includeModule('aspro.next')):?>
	<div class='alert alert-warning'><?=GetMessage('ASPRO_NEXT_MODULE_NOT_INSTALLED')?></div>
	<?die;?>
<?endif;?>

<?require_once('function.php');?>

<?
$arResult = array();

$arFrontParametrs = CNext::GetFrontParametrsValues(SITE_ID);
foreach(CNext::$arParametrsList as $blockCode => $arBlock)
{
	foreach($arBlock['OPTIONS'] as $optionCode => $arOption)
	{
		$arResult[$optionCode] = $arOption;
		$arResult[$optionCode]['VALUE'] = $arFrontParametrs[$optionCode];
		$arResult[$optionCode]['TYPE_BLOCK'] = $blockCode;

		if(isset($arResult[$optionCode]['SUB_PARAMS']) && $arResult[$optionCode]['SUB_PARAMS']) //nested params
		{
			if($arResult[$optionCode]['LIST'])
			{
				foreach($arResult[$optionCode]['LIST'] as $key => $arListOption)
				{
					if($arResult[$optionCode]['SUB_PARAMS'][$key])
					{
						foreach($arResult[$optionCode]['SUB_PARAMS'][$key] as $key2 => $arSubOptions)
						{
							if($arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['TYPE'] == 'selectbox')
							{
								foreach($arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['LIST'] as $key3 => $value)
								{
									if($arFrontParametrs[$key.'_'.$key2] == $value)
										$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['LIST'][$key3]['CURRENT'] = 'Y';	
								}
							}
							else
							{
								$arResult[$optionCode]['SUB_PARAMS'][$key][$key2]['VALUE'] = $arFrontParametrs[$key.'_'.$key2];								
							}
						}
					}
				}
			}
		}

		if(isset($arResult[$optionCode]['DEPENDENT_PARAMS']) && $arResult[$optionCode]['DEPENDENT_PARAMS']) //dependent params
		{
			foreach($arResult[$optionCode]['DEPENDENT_PARAMS'] as $key => $arListOption)
			{
				$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['VALUE'] = $arFrontParametrs[$key];
				if(isset($arListOption['LIST']) && isset($arListOption['LIST']))
				{
					foreach($arListOption['LIST'] as $variantCode => $variant)
					{
						if(!is_array($variant))
							$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode] = array('TITLE' => $variant);
						if($arFrontParametrs[$key] == $variantCode)
							$arResult[$optionCode]['DEPENDENT_PARAMS'][$key]['LIST'][$variantCode]['CURRENT'] = 'Y';
					}
				}
			}
		}

		// CURRENT for compatibility with old versions
		if($arResult[$optionCode]['LIST'])
		{
			foreach($arResult[$optionCode]['LIST'] as $variantCode => $variantTitle)
			{
				if(!is_array($variantTitle))
					$arResult[$optionCode]['LIST'][$variantCode] = array('TITLE' => $variantTitle);
				if($arResult[$optionCode]['VALUE'] == $variantCode)
					$arResult[$optionCode]['LIST'][$variantCode]['CURRENT'] = 'Y';
			}
		}
	}
}

if($arResult)
{
	$arGroups = array();
	foreach($arResult as $optionCode => $arOption)
	{
		
		if((isset($arOption['GROUP']) && $arOption['GROUP'])) //set groups option
		{
			$arGroups[$arOption['GROUP']]['TITLE'] = GetMessage($arOption['GROUP']);
			$arGroups[$arOption['GROUP']]['THEME'] = $arOption['THEME'];
			$arGroups[$arOption['GROUP']]['GROUPS_EXT'] = 'Y';
			$arGroups[$arOption['GROUP']]['TYPE_BLOCK'] = $arOption['TYPE_BLOCK'];
			$arGroups[$arOption['GROUP']]['OPTIONS'][$optionCode] = $arOption;
			unset($arResult[$optionCode]);

			if(isset($arOption['GROUP_HINT']) && $arOption['GROUP_HINT']) //set group hint
				$arGroups[$arOption['GROUP']]['HINT'] = $arOption['GROUP_HINT'];
		}
	}
	if($arGroups)
		$arResult = array_merge($arResult, $arGroups);
}?>

<?
$themeDir = strToLower($arResult['BASE_COLOR']['VALUE'].($arResult['BASE_COLOR']['VALUE'] !== 'CUSTOM' ? '' : '_'.SITE_ID));
$themeBgDir = strToLower($arResult['BGCOLOR_THEME']['VALUE'].($arResult['BGCOLOR_THEME']['VALUE'] !== 'CUSTOM' ? '' : '_'.SITE_ID));

$bIndexBot = (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Lighthouse') !== false); // is indexed yandex/google bot

$active = ($arResult['THEME_SWITCHER']['VALUE'] == 'Y' && !$bIndexBot);
$arResult['CAN_SAVE'] = ($GLOBALS['USER']->IsAdmin() && ((isset($_SESSION['THEME']) && $_SESSION['THEME']) && (isset($_SESSION['THEME'][SITE_ID]) && $_SESSION['THEME'][SITE_ID])));

// $APPLICATION->AddHeadString(CNext::GetBannerStyle($arResult['BANNER_WIDTH']['VALUE'], $arResult['TOP_MENU']['VALUE']), true);

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/themes/'.$themeDir.'/theme.css', true);
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/bg_color/'.$themeBgDir.'/bgcolors.css', true);

$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/width-'.$arResult['PAGE_WIDTH']['VALUE'].'.css', true);
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/font-'.$arResult['FONT_STYLE']['VALUE'].'.css', true);?>

<?
if(!$bIndexBot)
{
	if($active && ((!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') && (strtolower($_REQUEST['ajax']) != 'y')))
	{
		\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/spectrum.js');
		$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH.'/js/on-off-switch.js');
		$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/spectrum.css');
		$this->IncludeComponentTemplate();
	}
}
else
{
	\Bitrix\Main\Data\StaticHtmlCache::getInstance()->markNonCacheable();
}

if(!$bIndexBot)
	$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH.'/css/custom.css', true);

$file = \Bitrix\Main\Application::getDocumentRoot().'/bitrix/components/aspro/theme.next/css/user_font_'.SITE_ID.'.css';

if($arResult['CUSTOM_FONT']['VALUE'] && \Bitrix\Main\IO\File::isFileExists($file))
{
	$APPLICATION->SetAdditionalCSS($componentPath.'/css/user_font_'.SITE_ID.'.css', true);
}

return $arResult;?>
