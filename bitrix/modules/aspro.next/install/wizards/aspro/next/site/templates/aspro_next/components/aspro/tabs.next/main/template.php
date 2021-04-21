<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);

	$class_block="s_".$this->randString();
	$arTab=array();
	$col=4;
	if($arParams["LINE_ELEMENT_COUNT"]>=3 && $arParams["LINE_ELEMENT_COUNT"]<4)
		$col=3;
	if($arResult["SHOW_SLIDER_PROP"]){?>
		<div class="tab_slider_wrapp specials <?=$class_block;?> best_block clearfix" itemscope itemtype="http://schema.org/WebPage">
			<?$arParams['SET_TITLE'] = 'N';
			$arTmp = reset($arResult["TABS"]);
			$arParams["FILTER_HIT_PROP"] = $arTmp["CODE"];
			$arParamsTmp = urlencode(serialize($arParams));?>
			<span class='request-data' data-value='<?=$arParamsTmp?>'></span>
			<div class="top_blocks">
				<?if($arParams["NAME_BLOCK"]):?>
					<div class="title_wrapper"><div class="title_block sm"><?=$arParams["NAME_BLOCK"];?></div></div>
				<?endif;?>
				<ul class="tabs ajax">
					<?$i=1;
					foreach($arResult["TABS"] as $code => $arTab):?>
						<li data-code="<?=$code?>" <?=($i==1 ? "class='cur clicked'" : "")?>><span><?=$arTab["TITLE"];?></span></li>
						<?$i++;?>
					<?endforeach;?>
					<li class="stretch"></li>
				</ul>
			</div>
			<ul class="tabs_content">
				<?$j=1;?>
				<?foreach($arResult["TABS"] as $code => $arTab){?>
					<?
					$arTab["FILTER"] = $arTab["FILTER"] ? CNext::makeElementFilterInRegion($arTab["FILTER"]) : array();
					?>
					<li class="tab <?=$code?>_wrapp <?=($j == 1 ? "cur opacity1" : "");?>" data-code="<?=$code?>" data-col="<?=$col;?>" data-filter="<?=($arTab["FILTER"] ? urlencode(serialize($arTab["FILTER"])) : '');?>">
						<div class="tabs_slider <?=$code?>_slides wr">
							<?if($j++ == 1)
							{
								if($arTab["FILTER"])
									$GLOBALS[$arParams["FILTER_NAME"]] = $arTab["FILTER"];

								include(str_replace("//", "/", $_SERVER["DOCUMENT_ROOT"].SITE_DIR."include/mainpage/comp_catalog_ajax.php"));
							}?>
						</div>
					</li>
				<?}?>
			</ul>
		</div>
	<?}?>