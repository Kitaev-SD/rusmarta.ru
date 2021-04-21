<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
//$this->setFrameMode(true);
?>
<div class="border_wrapper" style="color:#888888;font-size:14px;border:1px solid #e7e7e7;padding: 19px 20px 17px;margin-top:10px;margin-bottom:10px;">
	<?foreach($arResult["ITEMS"] as $item):
		if($arParams["DISPLAY_EMPTY"] != "Y" && !$item['VALUE']) continue;
	?>
		<div style="padding-bottom:5px;">
			<?if($arParams["DISPLAY_NAME"]!="N"):?><?=htmlspecialcharsbx($item['NAME'])?>: <?endif;?>
			<span style="color:#333333;"><?=htmlspecialcharsbx($item['VALUE'])?></span>
		</div>
	<?endforeach;?>
</div>