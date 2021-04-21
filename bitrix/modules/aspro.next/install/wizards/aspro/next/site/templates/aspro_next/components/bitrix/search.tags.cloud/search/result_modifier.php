<?if(!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();?>
<?if(is_array($arResult['SEARCH']) && !empty($arResult['SEARCH']))
{
	$path = $APPLICATION->GetCurPageParam("", array(), true);
	foreach($arResult['SEARCH'] as $key => $arItem)
	{
		if(isset($_GET['tags']) && $_GET['tags'])
		{
			if(urlencode($arItem['NAME']) == $_GET['tags'])
				$arResult['SEARCH'][$key]['ACTIVE'] = 'Y';
		}

	}
}?>