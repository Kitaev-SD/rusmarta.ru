<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["SECTIONS"]) < 1)
	return;?>

<ul class="section-vertical">
	<li>
		<a href="javascript:void(0)" class="showsection"><i class="fa fa-bars"></i><span><?=GetMessage("CATALOG")?></span></a>
		<div class="catalog-section-list" style="display:none;">
			<?foreach($arResult["SECTIONS"] as $arSection) {
				$bHasChildren = is_array($arSection["CHILDREN"]) && count($arSection["CHILDREN"]) > 0;?>
				<div class="catalog-section">
					<?if($arSection["NAME"] && $arResult["SECTION"]["ID"] != $arSection["ID"]) {?>
						<div class="catalog-section-title" style="<?=($bHasChildren ? 'margin:0px 0px 4px 0px;' : 'margin:0px 0px 2px 0px;');?>">
							<a href="<?=$arSection['SECTION_PAGE_URL']?>"><?=$arSection["NAME"]?></a>
							<?if($bHasChildren) {?>
								<span class="showsectionchild"><i class="fa fa-minus"></i><i class="fa fa-plus"></i><i class="fa fa-minus-circle"></i><i class="fa fa-plus-circle"></i></span>
							<?}?>
						</div>
					<?}
					if($bHasChildren) {?>
						<div class="catalog-section-childs" style="display:none;">
							<?foreach($arSection["CHILDREN"] as $key => $arChild) {?>
								<div class="catalog-section-child">
									<a href="<?=$arChild['SECTION_PAGE_URL']?>" title="<?=$arChild['NAME']?>">
										<span class="child">
											<span class="graph">
												<?if(!empty($arChild["UF_ICON"])) {?>
													<i class="<?=$arChild['UF_ICON']?>" aria-hidden="true"></i>
												<?} elseif(is_array($arChild["PICTURE_PREVIEW"])) {?>								
													<img src="<?=$arChild['PICTURE_PREVIEW']['SRC']?>" width="<?=$arChild['PICTURE_PREVIEW']['WIDTH']?>" height="<?=$arChild['PICTURE_PREVIEW']['HEIGHT']?>" alt="<?=$arChild['NAME']?>" title="<?=$arChild['NAME']?>" />
												<?} else {?>
													<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="50" height="50" alt="<?=$arChild['NAME']?>" title="<?=$arChild['NAME']?>" />
												<?}?>
											</span>
											<span class="text-cont">
												<span class="text"><?=$arChild["NAME"]?></span>
											</span>
										</span>
									</a>
								</div>
							<?}?>
							<div class="clr"></div>
						</div>
					<?}?>
				</div>			
			<?}?>
		</div>
	</li>
</ul>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$('.showsection').click(function() {
			var clickitem = $(this);
			if(clickitem.parent('li').hasClass('')) {
				clickitem.parent('li').addClass('active');
			} else {
				clickitem.parent('li').removeClass('active');
			}
			
			if($('.showsubmenu').parent('li').hasClass('active')) {
				$('.showsubmenu').parent('li').removeClass('active');
				$('.showsubmenu').parent('li').find('ul.submenu').css({'display':'none'});
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

			clickitem.parent('li').find('.catalog-section-list').slideToggle();
		});
		$('.showsectionchild').click(function() {
			var clickitem = $(this);
			if(clickitem.parent('div').hasClass('active')) {
				clickitem.parent('div').removeClass('active');
			} else {
				clickitem.parent('div').addClass('active');
			}
			clickitem.parent('div').parent('div').find('.catalog-section-childs').slideToggle();
		});
	});
	//]]>
</script>