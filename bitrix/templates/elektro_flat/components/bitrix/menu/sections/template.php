<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult) < 1)
	return;

global $arSetting;?>

<ul class="left-menu">
	<?$previousLevel = 0;	
	foreach($arResult as $arItem):		
		if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):
			echo str_repeat("</div></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
		endif;
		if($arItem["DEPTH_LEVEL"] == 1):
			if($arItem["IS_PARENT"]):?>
				<li class="parent<?if($arItem["SELECTED"]):?> selected<?endif?>">
					<a href="<?=$arItem['LINK']?>"><?=$arItem["TEXT"]?><?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT"):?><span class="arrow"></span><?endif;?></a>
					<?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?><span class="arrow"></span><?endif;?>
					<div class="catalog-section-childs">
			<?else:?>
				<li<?if($arItem["SELECTED"]):?> class="selected"<?endif?>>
					<a href="<?=$arItem['LINK']?>"><?=$arItem["TEXT"]?></a>
				</li>
			<?endif;
		elseif($arItem["DEPTH_LEVEL"] == 2):?>
			<div class="catalog-section-child">
				<a href="<?=$arItem['LINK']?>" title="<?=$arItem['TEXT']?>">
					<span class="child">
						<span class="image">
							<?if(is_array($arItem["PICTURE"])):?>
								<img src="<?=$arItem['PICTURE']['SRC']?>" width="<?=$arItem['PICTURE']['WIDTH']?>" height="<?=$arItem['PICTURE']['HEIGHT']?>" alt="<?=$arItem['TEXT']?>" />
							<?else:?>
								<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="50" height="50" alt="<?=$arItem['TEXT']?>" />
							<?endif;?>
						</span>						
						<span class="text-cont">
							<span class="text"><?=$arItem["TEXT"]?></span>
						</span>
					</span>
				</a>
			</div>
		<?else:
			continue;
		endif;
		$previousLevel = $arItem["DEPTH_LEVEL"];		
	endforeach;	
	if($previousLevel > 1):
		echo str_repeat("</div></li>", ($previousLevel-1));
	endif?>
</ul>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		<?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>			
			$(".top-catalog ul.left-menu").moreMenu();
		<?endif;?>
		$("ul.left-menu").children(".parent").on({
			mouseenter: function() {
				<?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT"):?>
					var pos = $(this).position(),
						dropdownMenu = $(this).children(".catalog-section-childs"),
						dropdownMenuLeft = pos.left + $(this).width() + 9 + "px",
						dropdownMenuTop = pos.top - 5 + "px";
					dropdownMenu.css({"left": dropdownMenuLeft, "top": dropdownMenuTop});
					dropdownMenu.stop(true, true).delay(200).fadeIn(150);
				<?elseif($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>
					var pos = $(this).position(),
						menu = $(this).closest(".left-menu"),
						dropdownMenu = $(this).children(".catalog-section-childs"),
						dropdownMenuLeft = pos.left + "px",
						dropdownMenuTop = pos.top + $(this).height() + 13 + "px",
						arrow = $(this).children(".arrow"),
						arrowLeft = pos.left + ($(this).width() / 2) + "px",
						arrowTop = pos.top + $(this).height() + 3 + "px";
					if(menu.width() - pos.left < dropdownMenu.width()) {
						dropdownMenu.css({"left": "auto", "right": "10px", "top": dropdownMenuTop});
						arrow.css({"left": arrowLeft, "top": arrowTop});
					} else {
						dropdownMenu.css({"left": dropdownMenuLeft, "right": "auto", "top": dropdownMenuTop});
						arrow.css({"left": arrowLeft, "top": arrowTop});
					}
					dropdownMenu.stop(true, true).delay(200).fadeIn(150);
					arrow.stop(true, true).delay(200).fadeIn(150);
				<?endif;?>
			},
			mouseleave: function() {
				$(this).children(".catalog-section-childs").stop(true, true).delay(200).fadeOut(150);
				<?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>
					$(this).children(".arrow").stop(true, true).delay(200).fadeOut(150);
				<?endif;?>
			}
		});
	});
	//]]>
</script>