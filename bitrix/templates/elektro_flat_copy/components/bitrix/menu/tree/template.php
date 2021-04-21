<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

    $this->setFrameMode(true);

    if (count($arResult) < 1)
        return;

    global $arSetting;

    $count=0;
	
	//echo"<pre>"; print_r(  $arResult ); echo "</pre>";    
?>

<ul class="left-menu">
    <? $previousLevel = 0;
        foreach ($arResult as $arItem){
        if ($previousLevel && $arItem["DEPTH_LEVEL"] < $previousLevel) {
            $count=0;
            echo str_repeat("</ul></li>", ($previousLevel - $arItem["DEPTH_LEVEL"]));
        }
        if ($arItem["IS_PARENT"]){

        if ($arItem["DEPTH_LEVEL"] == 2){
    ?>


    <li class="last parent<? if ($arItem['SELECTED']): ?> selected<? endif ?>">
        <a class="last2" href="<?= $arItem['LINK'] ?>">
          <span class="child">
						<span class="graph">
							<? if (!empty($arItem["PARAMS"]["ICON"])) { ?>
                                <i class="<?= $arItem['PARAMS']['ICON'] ?>" aria-hidden="true"></i>
                            <? } elseif (is_array($arItem["PICTURE"])) { ?>
                                <img src="<?= $arItem['PICTURE']['SRC'] ?>" width="<?= $arItem['PICTURE']['WIDTH'] ?>"
                                     height="<?= $arItem['PICTURE']['HEIGHT'] ?>" alt="<?= $arItem['TEXT'] ?>"
                                     title="<?= $arItem['TEXT'] ?>"/>
                            <? } else { ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/no-photo.jpg" width="50" height="50"
                                     alt="<?= $arItem['TEXT'] ?>" title="<?= $arItem['TEXT'] ?>"/>
                                <?
                            } ?>
						</span>
						<span class="text-cont">
							<span class="text"><?= $arItem["TEXT"] ?></span>
						</span>
					</span>
        </a>
        <? if ($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER") { ?>
            <span class="arrow"></span>
        <? } ?>
        <ul class="submenu">
            <?$lenght=0;?>

            <? } else{ ?>
            <li class="parent<? if ($arItem['SELECTED']): ?> selected<? endif ?>">
                <a href="<?= $arItem['LINK'] ?>">
                    <?= $arItem["TEXT"] ?><? if ($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT") { ?><span
                            class="arrow"></span><? } ?>
                </a>
                <? if ($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER") { ?>
                    <span class="arrow"></span>
                <? } ?>
                <ul class="submenu">
                    <? }
                    ?>

                    <?
                        } else {
                        if ($arItem["PERMISSION"] > "D"){
                    ?>
                    <? if ($arItem["DEPTH_LEVEL"] < 2){
                        ?>
                        <li<? if ($arItem["SELECTED"]): ?> class="selected"<? endif ?>>
                            <a href="<?= $arItem['LINK'] ?>"><?= $arItem["TEXT"] ?></a>
                        </li>
                    <? }else{
                    ?>
                    <? if ($arItem["DEPTH_LEVEL"] == 2){
                        ?>
                       <li class="last4"> <div class="catalog-section-child">
                            <a href="<?= $arItem['LINK'] ?>" title="<?= $arItem['TEXT'] ?>">
					<span class="child">
						<span class="graph">
							<? if (!empty($arItem["PARAMS"]["ICON"])) { ?>
                                <i class="<?= $arItem['PARAMS']['ICON'] ?>" aria-hidden="true"></i>
                            <? } elseif (is_array($arItem["PICTURE"])) { ?>
                                <img src="<?= $arItem['PICTURE']['SRC'] ?>" width="<?= $arItem['PICTURE']['WIDTH'] ?>"
                                     height="<?= $arItem['PICTURE']['HEIGHT'] ?>" alt="<?= $arItem['TEXT'] ?>"
                                     title="<?= $arItem['TEXT'] ?>"/>
                            <? } else { ?>
                                <img src="<?= SITE_TEMPLATE_PATH ?>/images/no-photo.jpg" width="50" height="50"
                                     alt="<?= $arItem['TEXT'] ?>" title="<?= $arItem['TEXT'] ?>"/>
                                <?
                            } ?>
						</span>
						<span class="text-cont">
							<span class="text"><?= $arItem["TEXT"] ?></span>
						</span>
					</span>
                            </a>
                        </div></li>
                    <? }else{
                                    $lenght+=strlen(trim($arItem["TEXT"]));
                                    ?>
                                    <input type="hidden" value="<?=$lenght?>">

                                    <?               

                                    if($count>0 && $lenght>=23){
                                        echo "<br>";
                                        $lenght=0;
                                    }

                                    $lenght+=strlen(trim($arItem["TEXT"]));

                                   // echo strlen($arItem["TEXT"]);
                                    ?>
                         <span <? if ($arItem["SELECTED"]){ ?> class="selected"<? } ?>>
                               <a href="<?= $arItem['LINK'] ?>">
                                 <?= $arItem["TEXT"] ?>
                              </a>
                         </span>
                                    <?if($lenght>=23){
                                     
                                    }else{?>
                                        &nbsp;
                                   <? }?>
                        <?
                                    $count++;
                    } ?>
                        <? } ?>
                        <?
                            }
                            }
                            $previousLevel = $arItem["DEPTH_LEVEL"];
                            }
                            if ($previousLevel > 1) {
                                echo str_repeat("</ul></li>", ($previousLevel - 1));
                            } ?>
                </ul>

                <script type="text/javascript">
                    //<![CDATA[
                    $(function () {
                        <?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>
                        $(".top-catalog ul.left-menu").moreMenu();
                        <?endif;?>
                        $("ul.left-menu").children(".parent").on({
                            mouseenter: function () {
                                <?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT"):?>
                                var pos = $(this).position(),
                                    dropdownMenu = $(this).children(".submenu"),
                                    dropdownMenuLeft = pos.left + $(this).width() + 9 + "px",
                                    dropdownMenuTop = pos.top - 5 + "px";
                                if (pos.top + dropdownMenu.outerHeight() > $(window).height() + $(window).scrollTop() - 46) {
                                    dropdownMenuTop = pos.top - dropdownMenu.outerHeight() + $(this).outerHeight() + 5;
                                    dropdownMenuTop = (dropdownMenuTop < 0 ? $(window).scrollTop() : dropdownMenuTop) + "px";
                                }
                                dropdownMenu.css({"left": dropdownMenuLeft, "top": dropdownMenuTop, "z-index": "9999"});
                                dropdownMenu.stop(true, true).delay(200).fadeIn(150);
                                <?elseif($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>
                                var pos = $(this).position(),
                                    menu = $(this).closest(".left-menu"),
                                    dropdownMenu = $(this).children(".submenu"),
                                    dropdownMenuLeft = pos.left + "px",
                                    dropdownMenuTop = pos.top + $(this).height() + 13 + "px",
                                    arrow = $(this).children(".arrow"),
                                    arrowLeft = pos.left + ($(this).width() / 2) + "px",
                                    arrowTop = pos.top + $(this).height() + 3 + "px";
                                if (menu.width() - pos.left < dropdownMenu.width()) {
                                    dropdownMenu.css({
                                        "left": "auto",
                                        "right": "10px",
                                        "top": dropdownMenuTop,
                                        "z-index": "9999"
                                    });
                                    arrow.css({"left": arrowLeft, "top": arrowTop});
                                } else {
                                    dropdownMenu.css({
                                        "left": dropdownMenuLeft,
                                        "right": "auto",
                                        "top": dropdownMenuTop,
                                        "z-index": "9999"
                                    });
                                    arrow.css({"left": arrowLeft, "top": arrowTop});
                                }
                                dropdownMenu.stop(true, true).delay(200).fadeIn(150);
                                arrow.stop(true, true).delay(200).fadeIn(150);
                                <?endif;?>
                            },
                            mouseleave: function () {
                                $(this).children(".submenu").stop(true, true).delay(200).fadeOut(150);
                                <?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>
                                $(this).children(".arrow").stop(true, true).delay(200).fadeOut(150);
                                <?endif;?>
                            }
                        });
                    });
                    //]]>
                </script>