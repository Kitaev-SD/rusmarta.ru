<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$frame = $this->createFrame("already_seen")->begin("");
	if(!empty($arResult["ITEMS"])):
		global $arSetting;?>
		<div class="already_seen">
			<div class="center<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?>">
				<div class="h3"><?=GetMessage("CATALOG_ALREADY_SEEN")?></div>
				<ul>
					<?foreach($arResult["ITEMS"] as $key => $arItem):?>
						<li>
							<a href="<?=$arItem['DETAIL_PAGE_URL']?>">
								<span><?=$arItem["NAME"]?></span>
								<?if(is_array($arItem["PICTURE"])):?>
									<img src="<?=$arItem['PICTURE']['SRC']?>" alt="<?=$arItem['NAME']?>"/>
								<?else:?>
									<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="68px" height="68px" alt="<?=$arItem['NAME']?>"/>
								<?endif;?>
							</a>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	<?endif;
$frame->end();?>