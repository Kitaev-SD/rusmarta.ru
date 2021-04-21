<?
if($arResult['ITEMS'])
{
	foreach($arResult['ITEMS'] as $i => $arItem)
	{
		CNext::getFieldImageData($arResult['ITEMS'][$i], array('PREVIEW_PICTURE'));
	}
	
}
?>