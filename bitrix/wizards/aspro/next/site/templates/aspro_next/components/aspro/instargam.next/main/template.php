<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);?>
<?if(isset($_POST["AJAX_REQUEST_INSTAGRAM"]) && $_POST["AJAX_REQUEST_INSTAGRAM"] == "Y"):
	$inst=new CInstargramNext($arParams["TOKEN"], \Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_ITEMS_COUNT", 8));
	$arInstagramPosts=$inst->getInstagramPosts();
	$arInstagramUser=$inst->getInstagramUser();
	if($arInstagramPosts && !$arInstagramPosts["meta"]["error_message"]):?>
		<?$obParser = new CTextParser;
		$text_length = \Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_TEXT_LENGTH", 400);?>
		<div class="item-views front blocks">
			<div class="top_block">
				<h3 class="title_block"><?=($arParams["TITLE"] ? $arParams["TITLE"] : GetMessage("TITLE"));?></h3>
				<a href="https://www.instagram.com/<?=$arInstagramUser['data']['username']?>/" target="_blank"><?=GetMessage('INSTAGRAM_ALL_ITEMS');?></a>
			</div>
			<div class="instagram clearfix">
				<?$iCountSlide = \Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_ITEMS_VISIBLE", 4);?>
				<div class="items row1 flexbox1 flexslider" data-plugin-options='{"animation": "slide", "move": 0, "directionNav": true, "itemMargin":0, "controlNav" :false, "animationLoop": true, "slideshow": false, "slideshowSpeed": 5000, "animationSpeed": 900, "counts": [<?=$iCountSlide;?>,4,3,2,1]}'>
					<ul class="slides row flexbox">
						<?foreach ($arInstagramPosts['data'] as $arItem):?>
							<li class="item col-<?=$iCountSlide;?>">
								<div class="image" style="background:url(<?=$arItem['images']['standard_resolution']['url'];?>) center center/cover no-repeat;"><a href="<?=$arItem['link']?>" target="_blank"><div class="title"><div><?=($obParser->html_cut($arItem['caption']['text'], $text_length));?></div></div></a></div>
							</li>
						<?endforeach;?>
					</ul>
				</div>
			</div>
		</div>
	<?endif;?>
<?else:?>
	<div class="instagram_wrapper wide_<?=\Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_WIDE_BLOCK", "N");?>">
		<div class="maxwidth-theme">
			<div class="instagram_ajax loader_circle"></div>
		</div>
	</div>
<?endif;?>