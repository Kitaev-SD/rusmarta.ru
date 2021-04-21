<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$strTitle = !empty($arResult["USER"]["FIO"]) ? $arResult["USER"]["FIO"] : $arResult["USER"]["LOGIN"];?>

<a class="personal-user" href="<?=$arParams['PATH_TO_PERSONAL']?>">
	<span class="personal-user__image-wrap">
		<span class="personal-user__image">
			<?if(is_array($arResult["USER"]["PERSONAL_PHOTO"])):?>
				<img src="<?=$arResult['USER']['PERSONAL_PHOTO']['SRC']?>" width="<?=$arResult['USER']['PERSONAL_PHOTO']['WIDTH']?>" height="<?=$arResult['USER']['PERSONAL_PHOTO']['HEIGHT']?>" alt="<?=$strTitle?>" title="<?=$strTitle?>" />
			<?else:?>
				<img src="<?=SITE_TEMPLATE_PATH?>/images/userpic.jpg" width="90" height="90" alt="<?=$strTitle?>" title="<?=$strTitle?>" />
			<?endif;?>
		</span>
	</span>
	<span class="personal-user__title"><?=$strTitle?></span>
</a>