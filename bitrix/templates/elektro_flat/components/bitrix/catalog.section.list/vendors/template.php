<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["SECTIONS"]) < 1)
	return;?>

<div id="catalog-section-list" class="catalog-section-list">
	<?foreach($arResult["SECTIONS"] as $arSection) {
		$bHasChildren = isset($arSection["CHILDREN"]) && !empty($arSection["CHILDREN"]);?>
		<div class="catalog-section">
			<div class="catalog-section-title<?=($bHasChildren && $arParams['HIDE_SECTION'] != 'Y' ? ' active' : '');?>" style="<?=($bHasChildren ? 'margin:0px 0px 4px 0px;' : 'margin:0px 0px 2px 0px;');?>">
				<a target="_blank" href="<?=$arSection['SECTION_PAGE_URL']?>" title="<?=$arSection['NAME']?>"><?=$arSection["NAME"]?></a>
				<?if($bHasChildren) {?>
					<span class="showchild"><i class="fa<?=($arParams['HIDE_SECTION'] != 'Y' ? ' fa-minus' : ' fa-plus')?>"></i></span>
				<?}?>
			</div>
			<?if($bHasChildren) {?>
				<div class="catalog-section-childs"<?=($arParams["HIDE_SECTION"] == "Y" ? " style='display: none;'" : "")?>>
					<?foreach($arSection["CHILDREN"] as $key => $arChild) {?>
						<div class="catalog-section-child">
							<a target="_blank" href="<?=$arChild['SECTION_PAGE_URL']?>" title="<?=$arChild['NAME']?>">
								<span class="child">
									<span class="graph">
										<?if(!empty($arChild["UF_ICON"])) {?>
											<i class="<?=$arChild['UF_ICON']?>" aria-hidden="true"></i>
										<?} elseif(is_array($arChild["PICTURE"])) {?>								
											<img src="<?=$arChild['PICTURE']['SRC']?>" width="<?=$arChild['PICTURE']['WIDTH']?>" height="<?=$arChild['PICTURE']['HEIGHT']?>" alt="<?=$arChild['NAME']?>" title="<?=$arChild['NAME']?>" />
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

<script type="text/javascript">
	//<![CDATA[
	BX.ready(function() {
		BX.bindDelegate(BX("catalog-section-list"), "click", {className: "showchild"}, function() {
			BX.toggleClass(this.parentNode, ["active", ""]);
			
			var currIcon = BX.findChild(this, {tagName: "i"}, true, false);
			if(!!currIcon)
				BX.toggleClass(currIcon, ["fa-minus", "fa-plus"]);
			
			var currItemsCont = BX.findChild(this.parentNode.parentNode, {className: "catalog-section-childs"}, true, false);
			if(!!currItemsCont)
				$(currItemsCont).slideToggle();
		});
	});
	//]]>
</script>