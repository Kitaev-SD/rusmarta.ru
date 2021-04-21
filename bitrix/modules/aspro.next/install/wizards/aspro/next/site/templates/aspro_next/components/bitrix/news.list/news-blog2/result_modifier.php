<?if(!defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED !== true) die()?>
<?if($arResult['ITEMS'])
{
	$qntyItems = count($arResult['ITEMS']);

	$group = 0;
	$arItemsTmp = array();
	$isWideBlock = false;
	foreach($arResult['ITEMS'] as $key => $arItem) // slice array by 3 elements
	{
		$arItem['DETAIL_PAGE_URL'] = CNext::FormatNewsUrl($arItem);
		if($arItem['PROPERTIES']['BIG_BLOCK']['VALUE'] == 'Y')
			$isWideBlock = true;

		if($key%3 == 0 && $key)
			++$group;
		$arItemsTmp[$group]['BIG_BLOCK'] = 'N';
		$arItemsTmp[$group]['ITEMS'][] = $arItem;		
	}
	
	if(!$isWideBlock)
	{
		$arItemsTmp[0]['BIG_BLOCK'] = 'Y';
		$arItemsTmp[0]['ITEMS'][0]['PROPERTIES']['BIG_BLOCK']['VALUE'] = 'Y';
	}	

	foreach($arItemsTmp as $key => $arItems) // check wide block
	{
		foreach($arItems['ITEMS'] as $keyTmp => $arItem)
		{
			if($arItem['PROPERTIES']['BIG_BLOCK']['VALUE'] == 'Y')
				$arItemsTmp[$key]['BIG_BLOCK'] = 'Y';
		}
	}
	

	foreach($arItemsTmp as $key => $arItems)
	{
		$arWideBlock = array();
		if($arItems['BIG_BLOCK'] == 'Y')
		{
			foreach($arItems['ITEMS'] as $keyTmp => $arItem) // unset wide item
			{
				if($arItem['PROPERTIES']['BIG_BLOCK']['VALUE'] == 'Y' && !$arWideBlock)
				{
					$arItem['CLASS'] = 'col-md-8 col-sm-8';
					$arItem['CLASS_WIDE'] = 'Y';
					$arWideBlock = $arItem;
					unset($arItemsTmp[$key]['ITEMS'][$keyTmp]);
				}
				else
				{
					$arItemsTmp[$key]['ITEMS'][$keyTmp]['CLASS'] = 'col-item';
				}
			}
			if($arItemsTmp[$key]['ITEMS']) // format div in short block
			{
				$arItemsTmp[$key]['ITEMS'] = array_values($arItemsTmp[$key]['ITEMS']);
				$cnt_items = count($arItemsTmp[$key]['ITEMS']);
				foreach($arItemsTmp[$key]['ITEMS'] as $keyTmp2 => $arTmpItem2)
				{
					if(!$keyTmp2)
						$arItemsTmp[$key]['ITEMS'][$keyTmp2]['START_DIV'] = 'Y';
					if($cnt_items == ++$keyTmp2)
						$arItemsTmp[$key]['ITEMS'][--$keyTmp2]['END_DIV'] = 'Y';
				}
			}
			if($arWideBlock['PROPERTIES']['POSITION_BLOCK']['VALUE_XML_ID'] == 'right')// push to start or end array wide item 
				array_push($arItemsTmp[$key]['ITEMS'], $arWideBlock);
			else
				array_unshift($arItemsTmp[$key]['ITEMS'], $arWideBlock);
		}
	}
	$arResult['ITEMS'] = $arItemsTmp;
}?>