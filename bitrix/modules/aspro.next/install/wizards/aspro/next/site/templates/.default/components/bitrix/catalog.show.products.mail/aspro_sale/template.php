<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;

$skuTemplate = array();
?>
<? if(!empty($arResult['ITEMS'])):?>
	<div class="products_block" style="margin:20px 0px 0px;text-align:center;padding-top:8px;">
		<?$count = count($arResult['ITEMS']);
		$i = 1;?>
		<?foreach($arResult['ITEMS'] as $arItem):?>
			<div class="item" style="font-size:0px;color: #000;box-sizing: border-box;text-align:left;padding:0px 0px 25px;margin-bottom:25px;<?=($i == $count ? 'margin-bottom:0px;' : 'border-bottom:1px solid #dedede;')?><?=($i == 1 ? 'margin-top:0px;' : 'margin-top:25px;')?>">
				<?if($arItem["PREVIEW_PICTURE"]["SRC"]):
					$img = CFile::ResizeImageGet($arItem["~PREVIEW_PICTURE"], array("width" => 130, "height" => 130), BX_RESIZE_PROPORTIONAL_ALT);?>
					<div class="img" style="max-width: 130px;margin:0px;font-size: 12px;width: 22%;display: inline-block;vertical-align: top;"><img src="<?=$img["src"]?>" alt="<?=$arItem["NAME"];?>" title="<?=$arItem["NAME"];?>"/></div>
					<div class="right-data" style="padding: 0 30px 2px;margin-left: 0;display: inline-block;vertical-align: top;width: 78%;box-sizing: border-box;">
				<?endif;?>
					<div style="font-size:12px;">
						<?if( $arItem["PROPERTIES"]["PERIOD"]["VALUE"] ){?>
							<div class="date_small" style="color:#888888;"><?=$arItem["PROPERTIES"]["PERIOD"]["VALUE"]?></div>
						<?}elseif($arItem["DISPLAY_ACTIVE_FROM"]){?>
							<div class="date_small" style="color:#888888;"><?=$arItem["DISPLAY_ACTIVE_FROM"]?></div>
						<?}?>
						<div class="item-title" style="font-size:15px;font-weight:600;padding:7px 0px 7px;"><a href="<?=$arItem["DETAIL_PAGE_URL"]?>" style="color:<?=$arParams["THEME_COLOR"];?>"><span><?=$arItem["NAME"]?></span></a></div>
						<div class="preview-text" style="color:#666666;font-size:14px;"><?=$arItem["PREVIEW_TEXT"]?></div>
					</div>
				<?if($arItem["PREVIEW_PICTURE"]["SRC"]):?>
					</div>
				<?endif;?>
				<?$i++;?>
			</div>
		<?endforeach;?>
		<?if($arParams["SALE_PAGE"] && $arParams["SHOW_SALE"] == "Y"):?>
			<div style="text-align:center;padding-bottom:10px;">
				<a href="<?=$arParams["SALE_PAGE"]?>" style="color:#fff;background:<?=$arParams["THEME_COLOR"];?>;padding:11px 19px;border-radius:3px;display: inline-block;"><?= GetMessage("SALE_PAGE") ?></a>
			</div>
		<?endif;?>
	</div>	
<? endif ?>