<?
foreach($arResult['ITEMS'] as $key => $arItem){
	CNext::getFieldImageData($arResult['ITEMS'][$key], array('PREVIEW_PICTURE'));
}
?>