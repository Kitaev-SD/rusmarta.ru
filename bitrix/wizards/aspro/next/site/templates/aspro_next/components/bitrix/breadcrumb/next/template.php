<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$strReturn = '';
if($arResult){
	\Bitrix\Main\Loader::includeModule("iblock");
	global $NextSectionID;
	$cnt = count($arResult);
	$lastindex = $cnt - 1;
	if(\Bitrix\Main\Loader::includeModule('aspro.next'))
	{
		global $arTheme;
		$bShowCatalogSubsections = ($arTheme["SHOW_BREADCRUMBS_CATALOG_SUBSECTIONS"]["VALUE"] == "Y");
	}

	for($index = 0; $index < $cnt; ++$index){
		$arSubSections = array();
		$arItem = $arResult[$index];
		$title = htmlspecialcharsex($arItem["TITLE"]);
		$bLast = $index == $lastindex;
		if($NextSectionID && $bShowCatalogSubsections){
			$arSubSections = CNext::getChainNeighbors($NextSectionID, $arItem['LINK']);
		}
		if($index){
			$strReturn .= '<span class="separator">-</span>';
		}
		if($arItem["LINK"] <> "" && $arItem['LINK'] != GetPagePath() && $arItem['LINK']."index.php" != GetPagePath() || $arSubSections){
			$strReturn .= '<div class="bx-breadcrumb-item'.($arSubSections ? ' drop' : '').($bLast ? ' cat_last' : '').'" id="bx_breadcrumb_'.$index.'" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
			if($arSubSections){
				if($index == ($cnt-1)):
					$strReturn .= '<span class="number" itemprop="item">';
				else:
					$strReturn .= '<a class="number" href="'.$arItem["LINK"].'" itemprop="item">';
				endif;
				$strReturn .=($arSubSections ? '<span itemprop="name">'.$title.'</span><b class="space"></b><span class="separator'.($bLast ? ' cat_last' : '').'"></span>' : '<span>'.$title.'</span>');
				$strReturn .= '<meta itemprop="position" content="'.($index + 1).'">';
				if($index == ($cnt-1)):
					$strReturn .= '</span>';
				else:
					$strReturn .= '</a>';
				endif;
				$strReturn .= '<div class="dropdown_wrapp"><div class="dropdown">';
					foreach($arSubSections as $arSubSection){
						$strReturn .= '<a class="dark_link" href="'.$arSubSection["LINK"].'">'.$arSubSection["NAME"].'</a>';
					}
				$strReturn .= '</div></div>';
			}
			else{
				$strReturn .= '<a href="'.$arItem["LINK"].'" title="'.$title.'" itemprop="item"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index + 1).'"></a>';
			}
			$strReturn .= '</div>';
		}
		else{
			$strReturn .= '<span itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="item"><span itemprop="name">'.$title.'</span><meta itemprop="position" content="'.($index + 1).'"></span></span>';
		}
	}

	return '<div class="breadcrumbs" id="navigation" itemscope="" itemtype="http://schema.org/BreadcrumbList">'.$strReturn.'</div>';
}
else{
	return $strReturn;
}
?>