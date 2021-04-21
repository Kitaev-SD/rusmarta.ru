<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult) < 1)
	return;?>

<ul class="store-vertical">
	<li>
		<a href="javascript:void(0)" class="showsubmenu"><?=GetMessage("MENU")?></a>
		<ul class="submenu" style="display:none;">
			<li>
				<a href="<?=SITE_DIR?>" <?if($APPLICATION->GetCurPage(true)== SITE_DIR."index.php") echo "class='root-item-selected'";?>><?=GetMessage("MENU_HOME")?></a>
			</li>
			<?$previousLevel = 0;
			foreach($arResult as $arItem):

				if($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):
					echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
				endif;

				if($arItem["IS_PARENT"]):?>
					<li <?if($arItem["SELECTED"]):?>class="item-selected"<?endif?>>
						<span class="text">
							<a href="<?=$arItem["LINK"]?>" class="root-item<?if($arItem["SELECTED"]):?>-selected<?endif?>"><?=$arItem["TEXT"]?></a>
							<span class="showchild"><i class="fa fa-plus-circle"></i><i class="fa fa-minus-circle"></i></span>
						</span>
						<ul style="display:none;">
				<?else:
					if($arItem["PERMISSION"] > "D"):?>
						<li>
							<a href="<?=$arItem["LINK"]?>" class="root-item<?if($arItem["SELECTED"]):?>-selected<?endif?>"><?=$arItem["TEXT"]?></a>
						</li>
					<?else:?>
						<li>
							<a href="javascript:void(0)" class="root-item<?if($arItem["SELECTED"]):?>-selected<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a>
						</li>
					<?endif;
				endif;

				$previousLevel = $arItem["DEPTH_LEVEL"];
					
			endforeach;

			if($previousLevel > 1):
				echo str_repeat("</ul></li>", ($previousLevel-1));
			endif;?>
		</ul>
	</li>
</ul>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$('.showsubmenu').click(function() {
			var clickitem = $(this);
			if(clickitem.parent('li').hasClass('')) {
				clickitem.parent('li').addClass('active');
			} else {
				clickitem.parent('li').removeClass('active');
			}
			
			if($('.showsection').parent('li').hasClass('active')) {
				$('.showsection').parent('li').removeClass('active');
				$('.showsection').parent('li').find('.catalog-section-list').css({'display':'none'});
			}
			
			if($('.showcontacts').parent('li').hasClass('active')) {
				$('.showcontacts').parent('li').removeClass('active');
				$('.header_4').css({'display':'none'});
			}
			
			if($('.showsearch').parent('li').hasClass('active')) {
				$('.showsearch').parent('li').removeClass('active');
				$('.header_2').css({'display':'none'});
				$('div.title-search-result').css({'display':'none'});
			}

			clickitem.parent('li').find('ul.submenu').slideToggle();
		});
		
		var lis = $('.submenu').find('li');
		for(var i = 0; i < lis.length; i++) {
			if($(lis[i]).hasClass('item-selected')) {
				$(lis[i]).addClass('active');
				var ul = $(lis[i]).find('ul:first');
				$(ul).css({display: 'block'});
			}
		}
		
		$('.showchild').click(function() {
			var clickitem = $(this);
			if(clickitem.parent('span').parent('li').hasClass('active')) {
				clickitem.parent('span').parent('li').removeClass('active');
			} else {
				clickitem.parent('span').parent('li').addClass('active');
			}
			clickitem.parent('span').parent('li').find('ul:first').slideToggle();
		});
	});
	//]]>
</script>