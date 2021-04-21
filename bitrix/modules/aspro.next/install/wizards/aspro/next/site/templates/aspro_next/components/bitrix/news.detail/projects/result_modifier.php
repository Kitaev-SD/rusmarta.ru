<?
if(is_array($arResult['DETAIL_PICTURE'])){
	CNext::getFieldImageData($arResult, array('DETAIL_PICTURE'));
	$arResult['GALLERY'][] = array(
		'DETAIL' => $arResult['DETAIL_PICTURE'],
		'PREVIEW' => CFile::ResizeImageGet($arResult['DETAIL_PICTURE'] , array('width' => 1000, 'height' => 1000), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
		'TITLE' => (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE'] : $arResult['NAME'])),
		'ALT' => (strlen($arResult['DETAIL_PICTURE']['DESCRIPTION']) ? $arResult['DETAIL_PICTURE']['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT'] : $arResult['NAME'])),
	);
}
if($arResult['DISPLAY_PROPERTIES']){
	$arResult['VIDEO'] = array();

	if($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'] && is_array($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'])){
		foreach($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'] as $img){
			$arResult['GALLERY'][] = array(
				'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
				'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 1000, 'height' => 1000), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
				'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
				'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
			);
		}
	}
}

if(!empty($arResult['PROPERTIES']['GALLEY_BIG']['VALUE'])){
	foreach($arResult['PROPERTIES']['GALLEY_BIG']['VALUE'] as $img){
		$arResult['GALLERY_BIG'][] = array(
			'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
			'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 1500, 'height' => 1500), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
			'THUMB' => CFile::ResizeImageGet($img , array('width' => 60, 'height' => 60), BX_RESIZE_IMAGE_EXACT, true),
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
		);
	}
}
$arResult['DISPLAY_PROPERTIES_FORMATTED'] = CNext::PrepareItemProps($arResult['DISPLAY_PROPERTIES']);

$arResult['COMPANY'] = array();
if($arResult['DISPLAY_PROPERTIES']['LINK_COMPANY']['VALUE'])
{
	$arCompany = CCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'N', 'TAG' => CCache::GetIBlockCacheTag($arResult['PROPERTIES']['LINK_COMPANY']['LINK_IBLOCK_ID']))), array('IBLOCK_ID' => $arResult['PROPERTIES']['LINK_COMPANY']['LINK_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ID' => $arResult['DISPLAY_PROPERTIES']['LINK_COMPANY']['VALUE']), false, false, array('ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE', 'DETAIL_TEXT', 'DETAIL_TEXT_TYPE', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'PROPERTY_SITE', 'PROPERTY_SLOGAN'));
	if($arCompany){
		if($arCompany['PREVIEW_PICTURE'] || $arCompany['DETAIL_PICTURE']){
			$arCompany['IMAGE-BIG'] = CFile::ResizeImageGet(($arCompany['PREVIEW_PICTURE'] ? $arCompany['PREVIEW_PICTURE'] : $arCompany['DETAIL_PICTURE']), array('width' => 191, 'height' => 125), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
		}
	}
	$arResult['COMPANY'] = $arCompany;
}

if(isset($arResult['PROPERTIES']['BNR_TOP']) && $arResult['PROPERTIES']['BNR_TOP']['VALUE'] == 'Y')
{
	$cp = $this->__component;
	if(is_object($cp))
	{
		$cp->arResult['SECTION_BNR_CONTENT'] = true;
	    $cp->SetResultCacheKeys( array('SECTION_BNR_CONTENT') );
	}
}
?>