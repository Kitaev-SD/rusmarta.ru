<?

if($arResult['ITEMS']){
	
	$cur_page=$APPLICATION->GetCurPage();
	foreach($arResult['ITEMS'] as $key => $arItem){		
		/* Check items for current banner position */
		if ($arItem['PROPERTIES']['POSITION']['VALUE_XML_ID']!==$arParams['POSITION']) {
			unset($arResult['ITEMS'][$key]);
			continue;
		}
		
		if (is_array($arItem['PROPERTIES']['SHOW_PAGE']['VALUE']) || is_array($arItem['PROPERTIES']['SHOW_SECTION']['VALUE'])) {
			$arResult['ITEMS'][$key]['DELETE']=true;
		}
		
		/* Check pages rules */
		if (is_array($arItem['PROPERTIES']['SHOW_PAGE']['VALUE'])) {						
			foreach ($arItem['PROPERTIES']['SHOW_PAGE']['VALUE'] as $page) {
				if ($page==$cur_page) {
					$arResult['ITEMS'][$key]['DELETE']=false;
					break;
				}
			}			
		}
		
		/* Check section rules */
		if (is_array($arItem['PROPERTIES']['SHOW_SECTION']['VALUE'])) {						
			foreach ($arItem['PROPERTIES']['SHOW_SECTION']['VALUE'] as $section) {				
				if(strpos($cur_page, $section) === 0) {
					$arResult['ITEMS'][$key]['DELETE']=false;
					break;
				}
			}
		}

		if ($arResult['ITEMS'][$key]['DELETE']==true) {
			unset($arResult['ITEMS'][$key]);
			continue;
		}
	}	
	
	if($arParams['SHOW_ALL_ELEMENTS'] != 'Y')
	{
		/* Get One random banner */
		if (count($arResult['ITEMS'])>1) {
			$arItem = $arResult['ITEMS'][array_rand($arResult['ITEMS'])];		
			unset($arResult['ITEMS']);
			$arResult['ITEMS']=array();
			$arResult['ITEMS'][0]=$arItem;
		}
	}
}

?>