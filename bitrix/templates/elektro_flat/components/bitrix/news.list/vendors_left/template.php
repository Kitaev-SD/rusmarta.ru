<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(count($arResult["ITEMS"]) < 1)
	return;
?>

<div class="vendors-list">
	<?foreach($arResult["ITEMS"] as $arItem):?>
		<p class="vendors-item">
			<a rel="nofollow" href="<?echo $arItem["DETAIL_PAGE_URL"]?>"><?echo $arItem["NAME"]?></a>
		</p>
	<?endforeach;?>
</div>
<a class="all" href="<?=SITE_DIR?>vendors/"><?=GetMessage("ALL_VENDORS")?></a>