<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?
    $isCatalogMenuExpanded = false;
    if(isset($arParams["CATALOG_MENU_EXPANDED"]) && $arParams["CATALOG_MENU_EXPANDED"] == "Y") {
	$isCatalogMenuExpanded = true;
    }
?>
<?if($arResult):?>
	<div class="menu top">
		<ul class="top">
			<?foreach($arResult as $arItem):?>
				<?$bShowChilds = $arParams['MAX_LEVEL'] > 1;?>
				<?$bParent = $arItem['CHILD'] && $bShowChilds;?>
				<?
				    if($isCatalogMenuExpanded && $bParent && isset($arItem["PARAMS"]["CLASS"]) && strripos($arItem["PARAMS"]["CLASS"], "catalog")!==false ) {
					$bParent = false;
					show_top_mobile_li($arItem, $bParent, array("a"=>"parent-catalog"));
				    }
				    else {
					show_top_mobile_li($arItem, $bParent);
				    }				    

				    if($isCatalogMenuExpanded && $arParams['MAX_LEVEL'] > 1 && $arItem['CHILD'] && $arItem["LINK"]=="/catalog/") {
					foreach ($arItem['CHILD'] as $arSubItem) {
					    show_top_mobile_li($arSubItem, true, array("a"=>"not-weight"));
					}
				    }
				?>
			<?endforeach;?>
		</ul>
	</div>
<?endif;?>