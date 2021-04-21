<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?><?if (!empty($arResult)):?>
    <div class='content__left_menu'>
        <div class="content__left_menu__header">
        <i class="content__left_menu__header-bars fa bars"></i>
        <span class="content__left_menu__header-title"></span>
        <i class="content__left_menu__header-down fa chevron-down"></i>
    </div>
        <ul id="vertical-multilevel-menu" class="vertical-multilevel-menu">

    <?php
    $previousLevel = 0;

    foreach($arResult as $arItem):?>
            <?if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel):?>
                    <?=str_repeat("</ul>", ($previousLevel - $arItem["DEPTH_LEVEL"]));?>
            <?endif?>

            <?if ($arItem["IS_PARENT"]):?>

                    <?if ($arItem["DEPTH_LEVEL"] == 1):?>
        <?php 
//showme_var($arItem);
?>
			<li class="<?if ($arItem['SELECTED']):?>question-section--active<?else:?>question-section <?endif?> <?if ($arItem["IS_PARENT"]):?>parent<?endif?>"><a href="<?=$arItem['LINK']?>" class="<?if ($arItem['SELECTED']):?>question-name question-name--active<?else:?>question-name<?endif?>"><?=$arItem["TEXT"]?><span class="plus-minus<?if ($arItem['SELECTED']):?> down<?else:?> up<?endif?>"></span></a></li>
                                    <ul class="<?if ($arItem['SELECTED']):?>question-element-list--active<?else:?>question-element-list<?endif?>">
                    <?else:?>
                            <li class="<?if ($arItem['SELECTED']):?>question-element-list__item--active<?else:?>question-element-list__item<?endif?>"><a href="<?=$arItem["LINK"]?>" class="parent<?if ($arItem['SELECTED']):?> item-selected<?endif?>"><?=$arItem["TEXT"]?></a></li>
                                    <ul>
                    <?endif?>

            <?else:?>  
                    <?if ($arItem["PERMISSION"] > "D"):?>

                            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                                    <li class="<?if ($arItem['SELECTED']):?>question-section--almost-active<?else:?>question-section<?endif?>"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem['SELECTED']):?>question-name question-name--active<?else:?>question-name<?endif?>"><?=$arItem["TEXT"]?></a></li>
                            <?else:?>
                                    <li class="<?if ($arItem['SELECTED']):?>question-element-list__item--active<?else:?>question-element-list__item<?endif?>"><a href="<?=$arItem['LINK']?>" <?if ($arItem["SELECTED"]):?> class="item-selected"<?endif?>><?=$arItem["TEXT"]?></a></li>
                            <?endif?>

                    <?else:?>

                            <?if ($arItem["DEPTH_LEVEL"] == 1):?>
                                    <li class="<?if ($arItem['SELECTED']):?>question-section--almost-active<?else:?>question-section<?endif?>"><a href="<?=$arItem["LINK"]?>" class="<?if ($arItem['SELECTED']):?>question-name question-name--active<?else:?>question-name<?endif?>" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
                            <?else:?>
                                    <li class="<?if ($arItem['SELECTED']):?>question-element-list__item--active<?else:?>question-element-list__item<?endif?>"><a href="" class="denied" title="<?=GetMessage("MENU_ITEM_ACCESS_DENIED")?>"><?=$arItem["TEXT"]?></a></li>
                            <?endif?>

                    <?endif?>

            <?endif?>

            <?$previousLevel = $arItem["DEPTH_LEVEL"];?>

    <?endforeach?>

    <?if ($previousLevel > 1)://close last item tags?>
            <?=str_repeat("</ul>", ($previousLevel-1) );?>
    <?endif?>

    </ul>
</div>
<?endif?>
