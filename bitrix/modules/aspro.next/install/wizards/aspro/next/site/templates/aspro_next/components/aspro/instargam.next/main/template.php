<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);?>
<?
global $USER;

if(!is_object($USER)){
    $USER = new CUser();
}

?>
<?if(isset($_POST["AJAX_REQUEST_INSTAGRAM"]) && $_POST["AJAX_REQUEST_INSTAGRAM"] == "Y"):
	$inst=new CInstargramNext($arParams["TOKEN"], \Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_ITEMS_COUNT", 8));
	$arInstagramPosts=$inst->getInstagramPosts();
	$arInstagramUser=$arInstagramPosts['data'][0]['username'];?>

	<?if($arInstagramPosts["error"]["message"] && $USER->IsAdmin()):?>
		<br>
		<div class="alert alert-danger">
			<strong>Error:</strong> <?=$arInstagramPosts["error"]["message"]?>
		</div>
	<?else:?>
		<?if($arInstagramPosts && !$arInstagramPosts["error"]["message"]):?>
			<?$obParser = new CTextParser;
			$text_length = \Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_TEXT_LENGTH", 400);?>
			<?if($arInstagramPosts['data']):?>
				<div class="item-views front blocks">
					<div class="top_block">
						<h3 class="title_block"><?=($arParams["TITLE"] ? $arParams["TITLE"] : GetMessage("TITLE"));?></h3>
						<a href="https://www.instagram.com/<?=$arInstagramUser?>/" target="_blank"><?=GetMessage('INSTAGRAM_ALL_ITEMS');?></a>
					</div>
					<div class="instagram clearfix">
						<?$iCountSlide = \Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_ITEMS_VISIBLE", 4);?>
						<div class="items row1 flexbox1 flexslider" data-plugin-options='{"animation": "slide", "move": 0, "directionNav": true, "itemMargin":0, "controlNav" :false, "animationLoop": true, "slideshow": false, "slideshowSpeed": 5000, "animationSpeed": 900, "counts": [<?=$iCountSlide;?>,4,3,2,1]}'>
							<ul class="slides row flexbox">
								<?foreach ($arInstagramPosts['data'] as $arItem):?>
									<?$arItem['IMAGE'] = $arItem['thumbnail_url'] ? $arItem['thumbnail_url'] : $arItem['media_url'];?>
									<li class="item col-<?=$iCountSlide;?>">
										<div class="image" style="background:url(<?=$arItem['IMAGE'];?>) center center/cover no-repeat;"><a href="<?=$arItem['permalink']?>" target="_blank" class="scroll-title"><div class="title"><div><?=$arItem['caption'];?></div></div></a></div>
									</li>
								<?endforeach;?>
							</ul>
						</div>
					</div>
				</div>
			<?endif;?>
		<?endif;?>
	<?endif;?>
<?else:?>
	<div class="instagram_wrapper wide_<?=\Bitrix\Main\Config\Option::get("aspro.next", "INSTAGRAMM_WIDE_BLOCK", "N");?>">
		<div class="maxwidth-theme">
			<div class="instagram_ajax loader_circle"></div>
		</div>
	</div>
<?endif;?>