<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);?>

<div class="store-horizontal-wrap">
	<ul class="store-horizontal">
		<li<?=($APPLICATION->GetCurPage(true) == SITE_DIR."index.php" ? " class='active'" : "");?>><a href="<?=SITE_DIR?>"><?=GetMessage("MENU_HOME")?></a></li>
		<?if(!empty($arResult)):
			$previousLevel = 0;					
			foreach($arResult as $arItem):
				if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):
					echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
				endif;
				if($arItem["IS_PARENT"]):?>
					<li class="dropdown<?=($arItem['SELECTED'] ? ' active' : '');?>">
						<a href="<?=$arItem['LINK']?>"><?=$arItem["TEXT"]?></a>
						<ul class="dropdown-menu">
				<?else:?>
					<li<?=$arItem["SELECTED"] ? " class='active'" : ""?>>
						<a href="<?=$arItem['LINK']?>"><?=$arItem["TEXT"]?></a>
					</li>
				<?endif;
				$previousLevel = $arItem["DEPTH_LEVEL"];						
			endforeach;
			if($previousLevel > 1):
				echo str_repeat("</ul></li>", ($previousLevel - 1));
			endif;
		endif;?>
	</ul>
</div>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		//MOREMENU//
		$(".top-menu ul.store-horizontal").moreMenu();

		//DROPDOWN//	
		$(".top-menu ul.store-horizontal .dropdown:not(.more)").on({		
			mouseenter: function() {
				var menu = $(this).closest(".store-horizontal"),
					menuWidth = menu.outerWidth(),
					menuLeft = menu.offset().left,
					menuRight = menuLeft + menuWidth,
					isParentDropdownMenu = $(this).closest(".dropdown-menu"),					
					dropdownMenu = $(this).children(".dropdown-menu"),
					dropdownMenuWidth = dropdownMenu.outerWidth(),					
					dropdownMenuLeft = isParentDropdownMenu.length > 0 ? $(this).offset().left + $(this).outerWidth() : $(this).offset().left,
					dropdownMenuRight = dropdownMenuLeft + dropdownMenuWidth;
				if(dropdownMenuRight > menuRight) {
					if(isParentDropdownMenu.length > 0) {
						dropdownMenu.css({"left": "auto", "right": "100%"});
					} else {
						dropdownMenu.css({"right": "0"});
					}
				}
				$(this).children(".dropdown-menu").stop(true, true).delay(200).fadeIn(150);
			},
			mouseleave: function() {
				$(this).children(".dropdown-menu").stop(true, true).delay(200).fadeOut(150);
			}
		});
	});
	//]]>
</script>