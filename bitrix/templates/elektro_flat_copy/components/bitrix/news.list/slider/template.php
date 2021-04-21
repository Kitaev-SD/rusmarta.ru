<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1) return;
?>

<div class="anythingContainer anythingContainer_<?=$arParams['SLIDER_ASPECT_RATIO']?>">
	<ul class="anythingSlider">
		<?foreach($arResult['ITEMS'] as $arItem) {
			if(!empty($arItem['PROPERTIES']['PRODUCT_LINK']['VALUE'])) {
				$locationProduct = ($arItem['PROPERTIES']['PRODUCT_LOCATION']['VALUE_XML_ID'] == 'right'? 'right': 'left');
				?>
				<li class="anythingSliderLi anythingSliderLi_<?=$arParams['SLIDER_ASPECT_RATIO']?>">
					<a 
						href="<?=(!empty($arItem['PROPERTIES']['URL']['VALUE'])? $arItem['PROPERTIES']['URL']['VALUE']: 'javascript:void(0)')?>" 
						<?=(!empty($arItem['PROPERTIES']['URL']['VALUE']) && !empty($arItem['PROPERTIES']['OPEN_URL']['VALUE'])? 'target="_blank"': '')?>
						<?if(!empty($arItem['PROPERTIES']['BACKGROUND_DIM_COLOR']['VALUE'])) {?>
							style="background-color: #<?=$arItem['PROPERTIES']['BACKGROUND_DIM_COLOR']['VALUE']?>" 
						<?}?>
					>
						<?
						$sBlockInfo = "
							<div class=\"slide-card-container information\">
								<div class=\"slide-card\">
									<div class=\"slide-info\">
										<div class=\"slide-info-title\">{$arItem['NAME']}</div>
						";
						
						if(!empty($arItem['PREVIEW_TEXT']))
							$sBlockInfo .= "
								<div class=\"slide-info-text\">{$arItem['PREVIEW_TEXT']}</div>
							";
						
						if(!empty($arItem["SETTING"]["URL"]))
							$sBlockInfo .= "
								<div class=\"slide-info-button\">
									<button class=\"btn_buy\"><i class=\"fa fa-angle-right\" aria-hidden=\"true\"></i><span>Подробнее</span></button>
								</div>
							";
						
						$sBlockInfo .= "
									</div>
								</div>
							</div>
						";
						
						if($locationProduct == 'right') {
							echo $sBlockInfo;
						}
						
						$APPLICATION->IncludeComponent(
							"bitrix:catalog.element",
							"slide",
							Array(
								"ELEMENT_ID" => $arItem['PROPERTIES']['PRODUCT_LINK']['VALUE'],
								"IBLOCK_ID" => $arItem['PROPERTIES']['PRODUCT_LINK']['LINK_IBLOCK_ID'],
								"CACHE_TYPE" => $arParams['CACHE_TYPE'],
								"CACHE_TIME" => $arParams['CACHE_TIME'],
								"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
								"PRICE_CODE" => $arParams['PRICE_CODE'],
								"PRICE_VAT_INCLUDE" => $arParams['PRICE_VAT_INCLUDE'],
								"CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
								"CURRENCY_ID" => $arParams['CURRENCY_ID'],
								"SET_TITLE" => "N",
								"SET_BROWSER_TITLE" => "N",
								"SET_META_KEYWORDS" => "N",
								"SET_META_DESCRIPTION" => "N",
								"ADD_SECTIONS_CHAIN" => "N",
								"USE_ELEMENT_COUNTER" => "N",
								"COMPATIBLE_MODE" => "N"
							),
							false,
							array("HIDE_ICONS" => "Y")
						);
						
						if($locationProduct != 'right') {
							echo $sBlockInfo;
						}?>
						<span 
							class="slide-bg"
							<?if(!empty($arItem['PICTURE_PREVIEW']['SRC'])) {?>
								style="background: url(<?=$arItem['PICTURE_PREVIEW']['SRC']?>) center center / cover no-repeat; height: 100%;<?=(!empty($arItem['PROPERTIES']['BACKGROUND_DIM_COLOR']['VALUE'])? ' opacity: 0.15': '')?>" 
							<?}?>
						></span>
					</a>
				</li>
			<?} else {
				$sImgUrl = (!empty($arItem['PROPERTIES']['PREVIEW_YOUTUBE']['VALUE']) && !empty($arItem['PROPERTIES']['CODE_YOUTUBE']['VALUE'])? "//img.youtube.com/vi/{$arItem['PROPERTIES']['CODE_YOUTUBE']['VALUE']}/maxresdefault.jpg": $arItem['PICTURE_PREVIEW']['SRC']);
				$sAutoPlay = (!empty($arItem['PROPERTIES']['AUTOMATIC_PLAYBACK']['VALUE'])? 'true': 'false');
				$sAutoPlaySlide = ($arParams['SLIDER_AUTOPLAY'] === 'Y'? 'true': 'false');
				?>
				<li class="anythingSliderLi anythingSliderLi_<?=$arParams['SLIDER_ASPECT_RATIO']?>" <?=(!empty($arItem['PROPERTIES']['CODE_YOUTUBE']['VALUE'])? " data-prop=\"{idVideo: 'video_{$arItem['ID']}', autoPlay: {$sAutoPlay}, autoPlaySlide: {$sAutoPlaySlide}}\"": '')?>>
					<?if(!empty($arItem['PROPERTIES']['CODE_YOUTUBE']['VALUE'])) {?>
						<div 
							id="video_<?=$arItem['ID']?>_load" 
							class="video-slide-overlay-load" 
							<?=($sAutoPlay == 'true'? 'style="display:none;"': '')?>
						>
							<span class="video-icon-loading">
								<i class="fa fa-circle" aria-hidden="true"></i>
								<i class="fa fa-circle" aria-hidden="true"></i>
								<i class="fa fa-circle" aria-hidden="true"></i>
							</span>
						</div>
						<div 
							id="video_<?=$arItem['ID']?>_play" 
							class="video-slide-overlay" 
							onclick="$('#video_<?=$arItem['ID']?>').YTPPlay();" 
							style="display:none;"
						>
							<span class="video-icon-play"><i class="fa fa-play-circle-o" aria-hidden="true"></i></span>
						</div>
						<div id="video_<?=$arItem['ID']?>_vob" class="video-options-buttons" style="display:none;">
							<button 
								id="video_<?=$arItem['ID']?>_pause" 
								class="btn-video-pause" 
								onclick="$('#video_<?=$arItem['ID']?>').YTPPause();" 
							>
								<i class="fa fa-pause" aria-hidden="true"></i>
							</button>
							<?if(empty($arItem['PROPERTIES']['MUTE_AUDIO']['VALUE'])) {?>
								<button 
									id="video_<?=$arItem['ID']?>_mute" 
									class="btn-video-mute" 
									onclick="$('#video_<?=$arItem['ID']?>').YTPMute();"
								>
									<i class="fa fa-volume-up" aria-hidden="true"></i>
								</button>
								<button 
									id="video_<?=$arItem['ID']?>_unmute" 
									class="btn-video-unmute" 
									onclick="$('#video_<?=$arItem['ID']?>').YTPUnmute();" 
									style="display:none;"
								>
									<i class="fa fa-volume-off" aria-hidden="true"></i>
								</button>
							<?}?>
						</div>
					<?}?>
					<a 
						<?=(!empty($arItem['PROPERTIES']['CODE_YOUTUBE']['VALUE'])? "id=\"video_{$arItem['ID']}\"": '')?> 
						href="<?=(!empty($arItem['PROPERTIES']['URL']['VALUE'])? $arItem['PROPERTIES']['URL']['VALUE']: 'javascript:void(0)')?>" 
						<?=(!empty($arItem['PROPERTIES']['URL']['VALUE']) && !empty($arItem["PROPERTIES"]["OPEN_URL"]['VALUE'])? 'target="_blank"': '')?> 
						<?if(!empty($sImgUrl)) {?>
							style="background:url(<?=$sImgUrl?>) center center no-repeat; background-size:cover;" 
						<?}?>
						<?if(!empty($arItem['PROPERTIES']['CODE_YOUTUBE']['VALUE'])) {?>
							data-property="{videoURL: '<?=$arItem['PROPERTIES']['CODE_YOUTUBE']['VALUE']?>', mute: <?=(!empty($arItem['PROPERTIES']['MUTE_AUDIO']['VALUE'])? 'true': 'false')?>, showControls: false, quality: 'default', opacity: 1, containment: 'self', optimizeDisplay: true, loop: <?=($sAutoPlay == 'true'? 'true': 1)?>, startAt: 0, remember_last_time: false, autoPlay: <?=$sAutoPlay?>, addRaster: false, gaTrack: false}" 
						<?}?>
					></a>
				</li>
			<?}?>
		<?}?>
	</ul>
</div>
