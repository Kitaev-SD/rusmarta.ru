<?
if($arResult['ITEMS'])
{
	$arAllSections = CNext::GetSections($arResult['ITEMS'], $arParams);

	$bHasImg = false;
	foreach($arResult['ITEMS'] as $key_item => $arItem)
	{
		CNext::getFieldImageData($arItem, array('PREVIEW_PICTURE'));
		if($arItem['PREVIEW_PICTURE'])
			$bHasImg = true;
		if($arItem['PROPERTIES'])
		{
			foreach($arItem['PROPERTIES'] as $key_prop => $arProperty)
			{
				if($arProperty["USER_TYPE"]=="directory" && isset($arProperty["USER_TYPE_SETTINGS"]["TABLE_NAME"])) // get values from highload
				{
					$rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter'=>array('=TABLE_NAME'=>$arProperty["USER_TYPE_SETTINGS"]["TABLE_NAME"])));
			        if ($arData = $rsData->fetch()){
			            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
			            $entityDataClass = $entity->getDataClass();
			            $arFilter = array(
			                'filter' => array(
			                    '=UF_XML_ID' => $arProperty["VALUE"]
			                )
			            );
			            $rsValues = $entityDataClass::getList($arFilter);
			            while($arValue = $rsValues->fetch())
			            {
			            	$arResult['ITEMS'][$key_item]['PROPERTIES'][$key_prop]['FORMAT'][] = $arValue;
			            }
			        }
				}
			}
		}
	}

	if(isset($arAllSections['ALL_SECTIONS']) && $arAllSections['ALL_SECTIONS'])
	{
		foreach($arAllSections['ALL_SECTIONS'] as $key => $arSection)
		{
			$bHasChild = (isset($arSection['CHILD_IDS']) && $arSection['CHILD_IDS']); // has child sections
			foreach($arResult['ITEMS'] as $arItem)
			{
				$SID = ($arItem['IBLOCK_SECTION_ID'] ? $arItem['IBLOCK_SECTION_ID'] : 0);
				if($bHasChild)
				{
					if($arSection['CHILD_IDS'][$SID])
						$arAllSections['ALL_SECTIONS'][$key]['ITEMS'][$arItem['ID']] = $arItem;
				}
				elseif($arAllSections['ALL_SECTIONS'][$SID])
				{
					$arAllSections['ALL_SECTIONS'][$SID]['ITEMS'][$arItem['ID']] = $arItem;
				}
			}
		}
		$arResult['SECTIONS'] = $arAllSections['ALL_SECTIONS'];
	}
	else
	{
		$arResult['SECTIONS'][0]['ITEMS'] = $arResult['ITEMS'];
	}
	$arResult['ITEMS_HAS_IMG'] = $bHasImg;
}
?>