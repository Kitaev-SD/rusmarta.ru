<?
CNext::getFieldImageData($arResult, array('DETAIL_PICTURE'));
if($arResult['DISPLAY_PROPERTIES']){
	$arResult['GALLERY'] = array();
	$arResult['VIDEO'] = array();

	if($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'] && is_array($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'])){
		foreach($arResult['DISPLAY_PROPERTIES']['PHOTOS']['VALUE'] as $img){
			$arResult['GALLERY'][] = array(
				'DETAIL' => ($arPhoto = CFile::GetFileArray($img)),
				'PREVIEW' => CFile::ResizeImageGet($img, array('width' => 1500, 'height' => 1500), BX_RESIZE_PROPORTIONAL_ALT, true),
				'THUMB' => CFile::ResizeImageGet($img, array('width' => 60, 'height' => 60), BX_RESIZE_IMAGE_EXACT, true),
				'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['TITLE']) ? $arResult['DETAIL_PICTURE']['TITLE']  :(strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME']))),
				'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arResult['DETAIL_PICTURE']['ALT']) ? $arResult['DETAIL_PICTURE']['ALT']  : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME']))),
			);
		}
	}

	foreach($arResult['DISPLAY_PROPERTIES'] as $i => $arProp){
		if($arProp['VALUE'] || strlen($arProp['VALUE'])){
			if($arProp['USER_TYPE'] == 'video'){
				if (count($arProp['PROPERTY_VALUE_ID']) > 1) {
					foreach($arProp['VALUE'] as $val){
						if($val['path']){
							$arResult['VIDEO'][] = $val;
						}
					}
				}
				elseif($arProp['VALUE']['path']){
					$arResult['VIDEO'][] = $arProp['VALUE'];
				}
				unset($arResult['DISPLAY_PROPERTIES'][$i]);
			}
		}
	}
}

$arResult['COMPANY'] = array();
if($arResult['DISPLAY_PROPERTIES']['LINK_COMPANY']['VALUE'])
{
	$arCompany = CNextCache::CIBLockElement_GetList(array('CACHE' => array('MULTI' =>'N', 'TAG' => CNextCache::GetIBlockCacheTag($arResult['PROPERTIES']['LINK_COMPANY']['LINK_IBLOCK_ID']))), array('IBLOCK_ID' => $arResult['PROPERTIES']['LINK_COMPANY']['LINK_IBLOCK_ID'], 'ACTIVE'=>'Y', 'ID' => $arResult['DISPLAY_PROPERTIES']['LINK_COMPANY']['VALUE']), false, false, array('ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_TEXT_TYPE', 'DETAIL_TEXT', 'DETAIL_TEXT_TYPE', 'PREVIEW_PICTURE', 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'PROPERTY_SITE', 'PROPERTY_SLOGAN'));
	if($arCompany){
		if($arCompany['PREVIEW_PICTURE'] || $arCompany['DETAIL_PICTURE']){
			$arCompany['IMAGE-BIG'] = CFile::ResizeImageGet(($arCompany['PREVIEW_PICTURE'] ? $arCompany['PREVIEW_PICTURE'] : $arCompany['DETAIL_PICTURE']), array('width' => 191, 'height' => 125), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true);
		}
	}
	$arResult['COMPANY'] = $arCompany;
}

if(isset($arResult['PROPERTIES']['BNR_TOP']) && $arResult['PROPERTIES']['BNR_TOP']['VALUE_XML_ID'] == 'YES')
{
	$cp = $this->__component;
	if(is_object($cp))
	{
		$cp->arResult['SECTION_BNR_CONTENT'] = true;
	    $cp->SetResultCacheKeys( array('SECTION_BNR_CONTENT') );
	}
}
$arResult['DISPLAY_PROPERTIES_FORMATTED'] = CNext::PrepareItemProps($arResult['DISPLAY_PROPERTIES']);

?>