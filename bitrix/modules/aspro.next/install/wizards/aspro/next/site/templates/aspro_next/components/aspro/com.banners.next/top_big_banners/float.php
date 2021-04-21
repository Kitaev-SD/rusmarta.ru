<?$isUrl=(strlen($arItem["PROPERTIES"]["URL_STRING"]["VALUE"]) ? true : false);?>
<div class="item normal_block">
	<div class="item_inner">
		<?$arItem["FORMAT_NAME"]=strip_tags($arItem["~NAME"]);?>
		<?if($isUrl){?>
			<a href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" class="opacity_block1 dark_block_animate" title="<?=$arItem["FORMAT_NAME"];?>" <?=($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"] ? "target='".$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]."'" : "");?>></a>
		<?}?>
		<?if($arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"] != "image"):?>
			<?$class_position_block = $class_text_block = '';
			if(isset($arItem["PROPERTIES"]["TEXT_POSITION"]) && $arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"])
				$class_position_block = $arItem["PROPERTIES"]["TEXT_POSITION"]["VALUE_XML_ID"].'_blocks';									
			if(isset($arItem["PROPERTIES"]["TEXTCOLOR"]) && $arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"])
				$class_text_block = $arItem["PROPERTIES"]["TEXTCOLOR"]["VALUE_XML_ID"].'_text';
			?>
			<div class="wrap_tizer  <?=$class_position_block;?> <?=$class_text_block;?>">
				<div class="wrapper_inner_tizer">
					<div class="wr_block">
						<span class="wrap_outer title">
							<?if($isUrl){?>
								<?if($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]):?>
									<a class="outer_text" href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>" <?=($arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"] ? "target='".$arItem["PROPERTIES"]["TARGETS"]["VALUE_XML_ID"]."'" : "");?>>
								<?else:?>
									<a class="outer_text" href="<?=$arItem["PROPERTIES"]["URL_STRING"]["VALUE"]?>">
								<?endif;?>
							<?}else{?>
								<span class="outer_text">
							<?}?>
								<span class="inner_text">
									<?=strip_tags($arItem["~NAME"], "<br><br/>");?>
								</span>
							<?if($isUrl){?>
								</a>
							<?}else{?>
								</span>
							<?}?>
						</span>
					</div>
					<?if($arItem["PREVIEW_TEXT"]):?>
						<div class="preview"><?=$arItem["PREVIEW_TEXT"];?></div>
					<?endif;?>
				</div>
			</div>
		<?endif;?>
		<div class="scale_block_animate img_block" style="background-image:url('<?=($arItem["DETAIL_PICTURE"]["SRC"] ? $arItem["DETAIL_PICTURE"]["SRC"] : $arItem["PREVIEW_PICTURE"]["SRC"])?>')"></div>
	</div>
</div>