<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */

use Bitrix\Main\Localization\Loc;

$skuTemplate = array();
?>
<? if(!empty($arResult['ITEMS'])):?>
	<div class="tizers_block" style="font-size:0px;margin:20px 0px 0px;text-align:center;border-top:1px solid #dedede;padding-top:8px;<?=($arParams["FROM_TEMPLATE"] == "Y" ? 'padding-left:30px;padding-right:30px;' : '')?>">
		<?foreach($arResult['ITEMS'] as $arItem):?>
			<?$name = strip_tags($arItem["~NAME"], "<br><br/>");?>
			<?$name_img = strip_tags($arItem["~NAME"]);?>
			<div class="item" style="display: inline-block;vertical-align: middle;font-size: 0px;width: 33.33%;color: #000;box-sizing: border-box;padding: 0px 5px 0px 0px;-moz-transition: all 0.1s ease;-o-transition: all 0.1s ease;-ms-transition: all 0.1s ease;transition: all 0.1s ease;margin: 0px 0px 59px;margin: 15px 0px 15px;white-space: nowrap;">
				<?if($arItem["PROPERTIES"]["LINK"]["VALUE"]){?>
					<?
					$url = $arItem["PROPERTIES"]["LINK"]["VALUE"];
					if(strpos($arItem["PROPERTIES"]["LINK"]["VALUE"], "http") === false &&  strpos($arItem["PROPERTIES"]["LINK"]["VALUE"], "https") === false)
						$url = $arParams["SITE_ADDRESS"]."/".$arItem["PROPERTIES"]["LINK"]["VALUE"];
					?>
					<a class="name" href="<?=str_replace(array("//", ":/"), array("/", "://"), $url);?>" style="font-size: 12px;text-decoration: none;color: #000;line-height: 16px;display: block;">
				<?}?>
				<?if($arItem["PREVIEW_PICTURE"]["SRC"]){?>
					<div class="img" style="max-width: 60px;margin: 0px 20px 0px 0px;white-space:normal;font-size: 12px;display: inline-block;vertical-align: middle;"><img src="<?=$arItem["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$name_img;?>" title="<?=$name_img;?>"/></div>
				<?}?>
				<div class="title" style="width: 54%;text-align: left;margin: -6px 0px 0px;white-space: normal;font-size: 12px;display: inline-block;vertical-align: middle;">
					<?=$name;?>
				</div>
				<?if($arItem["PROPERTIES"]["LINK"]["VALUE"]){?>
					</a>
				<?}?>
			</div>
		<?endforeach;?>
	</div>	
<? endif ?>