<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["SECTIONS"]) < 1)
	return;?>

<div class="catalog-section-list">
	<?foreach($arResult["SECTIONS"] as $arSection):
		$bHasChildren = is_array($arSection["CHILDREN"]) && count($arSection["CHILDREN"]) > 0;?>
		<div class="catalog-section">
			<?if($arSection["NAME"] && $arResult["SECTION"]["ID"] != $arSection["ID"]):?>
				<div class="catalog-section-title<?=($bHasChildren ? ' active' : '');?>" style="<?=($bHasChildren ? 'margin:0px 0px 4px 0px;' : 'margin:0px 0px 2px 0px;');?>">
					<a href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>"><?=$arSection["NAME"]?></a>
					<?if($bHasChildren):?>
						<span class="showchild"><i class="fa fa-minus"></i><i class="fa fa-plus"></i></span>
					<?endif;?>
				</div>
			<?endif;
			if($bHasChildren):?>
				<div class="catalog-section-childs">
					<?foreach($arSection["CHILDREN"] as $key => $arChild):?>
						<div class="catalog-section-child">
							<a href="<?=$arChild['SECTION_PAGE_URL']?>" title="<?=$arChild['NAME']?>">
								<span class="child">
									<span class="image">
										<?if(is_array($arChild["PICTURE"])):?>
											<img src="<?=$arChild['PICTURE']['SRC']?>" width="<?=$arChild['PICTURE']['WIDTH']?>" height="<?=$arChild['PICTURE']['HEIGHT']?>" alt="<?=$arChild['NAME']?>" title="<?=$arChild['NAME']?>" />
										<?else:?>
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="50" height="50" alt="<?=$arChild['NAME']?>" title="<?=$arChild['NAME']?>" />
										<?endif;?>
									</span>
									<span class="text-cont">
										<span class="text"><?=$arChild["NAME"]?></span>
									</span>
								</span>
							</a>
						</div>
					<?endforeach;?>
					<div class="clr"></div>
				</div>
			<?endif;?>
		</div>	
	<?endforeach;?>	
</div>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$(".showchild").click(function() {
			var clickitem = $(this);
			clickitem.parent().toggleClass("active");
			clickitem.parent().parent().find(".catalog-section-childs").slideToggle();
		});
	});
	//]]>
</script>