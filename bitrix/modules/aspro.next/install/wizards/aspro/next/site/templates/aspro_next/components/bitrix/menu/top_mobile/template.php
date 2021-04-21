<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?$this->setFrameMode(true);?>
<?$isCatalogMenuExpanded = isset($arParams["CATALOG_MENU_EXPANDED"]) && $arParams["CATALOG_MENU_EXPANDED"] === "Y";?>
<?if($arResult):?>
	<div class="menu top">
		<ul class="top">
			<?foreach($arResult as $arItem):?>
				<?$bShowChilds = $arParams['MAX_LEVEL'] > 1;?>
				<?$bParent = $arItem['CHILD'] && $bShowChilds;?>
				<?
				if($isCatalogMenuExpanded){
				    if(isset($arItem['PARAMS']['CLASS']) && strripos($arItem['PARAMS']['CLASS'], 'catalog') !==false) {
						show_top_mobile_li($arItem, $arParams, false, array('a' => 'parent-catalog'));

						if($bParent){
							foreach($arItem['CHILD'] as $arSubItem) {
								$bShowChilds = $arParams['MAX_LEVEL'] > $arSubItem['DEPTH_LEVEL'];
								$bParent = $arSubItem['CHILD'] && $bShowChilds;
							    show_top_mobile_li($arSubItem, $arParams, $bParent, array('a' => 'not-weight'));
							}
						}
				    }
				    else{
				    	show_top_mobile_li($arItem, $arParams, $bParent);
				    }
				}
				else{
					show_top_mobile_li($arItem, $arParams, $bParent);
				}
				?>
			<?endforeach;?>
		</ul>
	</div>
<?endif;?>