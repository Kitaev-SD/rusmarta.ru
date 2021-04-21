<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if(empty($arResult))
	return;?>

<ul>
	<?foreach($arResult as $itemIdex => $arItem):?>
		<li>
			<a href="<?=$arItem["LINK"]?>"><span><?=$arItem["TEXT"]?></span></a>
		</li>
	<?endforeach;?>
</ul>