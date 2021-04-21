<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;?>

<div class="banners-main">	
	<?$width = 0;
	foreach($arResult["ITEMS"] as $arItem):
		if(!isset($arItem["DISPLAY_PROPERTIES"]["WIDTH"]))
			continue;
		if($width == 0)
			echo "<div class='banners-main__row'>";?>
		<a class="banners-main__item" href="<?=(!empty($arItem['DISPLAY_PROPERTIES']['URL']) ? $arItem['DISPLAY_PROPERTIES']['URL']['VALUE'] : 'javascript:void(0)');?>"<?=(!empty($arItem["DISPLAY_PROPERTIES"]["WIDTH"]) ? " style='width:".$arItem["DISPLAY_PROPERTIES"]["WIDTH"]["VALUE"]."%;'" : "");?>>			
			<span class="banners-main__item-image"<?=(is_array($arItem["PREVIEW_PICTURE"]) ? " style=\"background-image:url('".$arItem["PREVIEW_PICTURE"]["SRC"]."');\"" : "");?>></span>
			<span class="banners-main__item-block-wrap">
				<span class="banners-main__item-block">
					<?if(!empty($arItem["DISPLAY_PROPERTIES"]["BUTTON_TEXT"])):?>
						<span class="banners-main__item-btn">
					<?endif;?>					
					<span class="banners-main__item-text<?=($arItem['DISPLAY_PROPERTIES']['WIDTH']['VALUE'] == '25' ? ' small' : '');?>"><?=$arItem["NAME"]?></span>					
					<?if(!empty($arItem["DISPLAY_PROPERTIES"]["BUTTON_TEXT"])):?>
						<button name="banners-main__item-button" class="btn_buy"><?=$arItem["DISPLAY_PROPERTIES"]["BUTTON_TEXT"]["VALUE"]?></button>
						</span>
					<?endif;?>				
				</span>				
			</span>
		</a>
		<?$width += $arItem["DISPLAY_PROPERTIES"]["WIDTH"]["VALUE"];
		if($width == 100):
			echo "</div>";
			$width = 0;
		endif;
	endforeach;
	if($width > 0 && $width < 100):
		echo "<a class='banners-main__item' href='javascript:void(0)'></a></div>";
	endif;?>
</div>