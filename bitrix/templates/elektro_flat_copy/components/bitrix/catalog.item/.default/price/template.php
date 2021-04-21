<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !==true) die();

use \Bitrix\Main\Localization\Loc;?>

<div class="catalog-item-info">
	<?//ITEM_PREVIEW_PICTURE//?>
	<div class="catalog-item-image-cont">
		<div class="catalog-item-image">
			<meta content="<?=(is_array($arElement['PREVIEW_PICTURE']) ? $arElement['PREVIEW_PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/no-photo.jpg');?>" itemprop="image" />
			<a rel="nofollow" href="<?=$arElement['DETAIL_PAGE_URL']?>">
				<?if(is_array($arElement["PREVIEW_PICTURE"])) {?>
					<img class="item_img" src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
				<?} else {?>
					<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
				<?}?>																
				<span class="sticker">
					<?=$sticker?>
				</span>								
			</a>							
		</div>
        </div> 
	<?//ITEM_TITLE//?>
	<div class="catalog-item-title<?=$class?>">
		<a href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$arElement['NAME']?>" itemprop="url">
			<span itemprop="name"><?=$arElement["NAME"]?></span>
		</a>
	</div>
	<meta content="<?=strip_tags($arElement['PREVIEW_TEXT'])?>" itemprop="description" />					
	<?//TIME_BUY//
	if(array_key_exists("TIME_BUY", $arElement["PROPERTIES"]) && !$arElement["PROPERTIES"]["TIME_BUY"]["VALUE"] == false) {
		if(!empty($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"])) {
			$showBar = false;													
			if($haveOffers) {
				if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0) {
					$showBar = true;									
					$startQnt = $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arElement["TOTAL_OFFERS"]["QUANTITY"];	
					$currQnt = $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arElement["TOTAL_OFFERS"]["QUANTITY"];		
					$currQntPercent = round($currQnt * 100 / $startQnt);
				} else {
					$showBar = true;
					$currQntPercent = 100;
				}
			} else {
				if($arElement["CAN_BUY"]) {
					if($arElement["CHECK_QUANTITY"]) {
						$showBar = true;
						$startQnt = $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_FROM"]["VALUE"] : $arElement["CATALOG_QUANTITY"];
						$currQnt = $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] ? $arElement["PROPERTIES"]["TIME_BUY_TO"]["VALUE"] : $arElement["CATALOG_QUANTITY"];
						$currQntPercent = round($currQnt * 100 / $startQnt);
					} else {
						$showBar = true;
						$currQntPercent = 100;
					}
				}
			}
			if($showBar == true) {?>								
				<div class="item_time_buy">										
					<div class="progress_bar_bg">
						<div class="progress_bar_line" style="width:<?=$currQntPercent?>%;"></div>
					</div>											
					<?$new_date = ParseDateTime($arElement["CURRENT_DISCOUNT"]["ACTIVE_TO"], FORMAT_DATETIME);?>
					<script type="text/javascript">												
						$(function() {														
							$("#time_buy_timer_<?=$itemIds['ID']?>").countdown({
								until: new Date(<?=$new_date["YYYY"]?>, <?=$new_date["MM"]?> - 1, <?=$new_date["DD"]?>, <?=$new_date["HH"]?>, <?=$new_date["MI"]?>),
								format: "DHMS",
								expiryText: "<div class='over'><?=Loc::getMessage('CT_BCS_ELEMENT_TIME_BUY_EXPIRY')?></div>"
							});
						});												
					</script>
					<div class="time_buy_cont">
						<div class="time_buy_clock">
							<i class="fa fa-clock-o"></i>
						</div>
						<div class="time_buy_timer" id="time_buy_timer_<?=$itemIds['ID']?>"></div>
					</div>
				</div>								
			<?}
		}
	}
	//TOTAL_OFFERS_ITEM_PRICE//?>
	<div class="item-price<?=$class?>" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
		<?//TOTAL_OFFERS_PRICE//
		if($haveOffers) {
			if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>								
				<span class="catalog-item-no-price">
					<?=Loc::getMessage("CT_BCS_ELEMENT_NO_PRICE")?>
				</span>								
			<?} else {
				if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] < $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>
					<span class="catalog-item-price-old">
						<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
					</span>
					<span class="catalog-item-price-percent">
						<?="-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PERCENT"]."%";?>
					</span>
				<?}?>
				<span class="catalog-item-price<?=($arElement['TOTAL_OFFERS']['MIN_PRICE']['RATIO_PRICE'] < $arElement['TOTAL_OFFERS']['MIN_PRICE']['RATIO_BASE_PRICE']) ? '-discount' : '';?>">
					<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y" ? "<span class='from'>".Loc::getMessage("CT_BCS_ELEMENT_FROM")."</span> " : "").number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>									
					<span><?=$currency?></span>
					<?if($arParams["USE_PRICE_COUNT"] && count($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_QUANTITY_RANGES"]) > 1) {?>
						<span class="catalog-item-price-ranges-wrap">
							<a id="<?=$itemIds['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
						</span>
					<?}
					if(count($arElement["TOTAL_OFFERS"]["MATRIX"]["MATRIX"]) > 1 && count($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ITEM_QUANTITY_RANGES"]) <= 1) {?>
						<span class="catalog-item-price-ranges-wrap">
							<a id="<?=$itemIds['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
						</span>
					<?}
					if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
						<span class="catalog-item-price-reference">
							<?=CCurrencyLang::CurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
						</span>
					<?}?>
				</span>							
			<?}?>
			<meta itemprop="price" content="<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"]?>" />
			<meta itemprop="priceCurrency" content="<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"]?>" />
			<?if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0) {?>
				<meta content="InStock" itemprop="availability" />									
			<?} else {?>
				<meta content="OutOfStock" itemprop="availability" />
			<?}
		//ITEM_PRICE//
		} else {
			if($arElement["MIN_PRICE"]["RATIO_PRICE"] <= 0) {?>
				<span class="catalog-item-no-price">
					<?=Loc::getMessage("CT_BCS_ELEMENT_NO_PRICE")?>
				</span>											
			<?} else {
				if($arElement["MIN_PRICE"]["RATIO_PRICE"] < $arElement["MIN_PRICE"]["RATIO_BASE_PRICE"]) {?>								
					<span class="catalog-item-price-old">
						<?=$arElement["MIN_PRICE"]["PRINT_RATIO_BASE_PRICE"];?>
					</span>
					<span class="catalog-item-price-percent">
						<?="-".$arElement["MIN_PRICE"]["PERCENT"]."%";?>
					</span>
				<?}?>
				<span class="catalog-item-price<?=($arElement["MIN_PRICE"]['RATIO_PRICE'] < $arElement["MIN_PRICE"]['RATIO_BASE_PRICE']) ? '-discount' : '';?>">		
					<?if(count($arElement["ITEM_QUANTITY_RANGES"]) > 1 && $inMinPrice) {?>
						<span class="from"><?=Loc::getMessage("CT_BCS_ELEMENT_FROM")?></span>
					<?}
					echo number_format($arElement["MIN_PRICE"]["RATIO_PRICE"], $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);?>									
					<span><?=$currency?></span>
					<?if($arParams["USE_PRICE_COUNT"] && count($arElement["ITEM_QUANTITY_RANGES"]) > 1) {?>
						<span class="catalog-item-price-ranges-wrap">
							<a id="<?=$itemIds['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
						</span>
					<?}
					if(count($arElement["PRICE_MATRIX"]["MATRIX"]) > 1 && count($arElement["ITEM_QUANTITY_RANGES"]) <= 1) {?>
						<span class="catalog-item-price-ranges-wrap">
							<a id="<?=$itemIds['PRICE_RANGES_BTN']?>" class="catalog-item-price-ranges" href="javascript:void(0);"><i class="fa fa-question-circle-o"></i></a>
						</span>
					<?}
					if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
						<span class="catalog-item-price-reference">
							<?=CCurrencyLang::CurrencyFormat($arElement["MIN_PRICE"]["RATIO_PRICE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["MIN_PRICE"]["CURRENCY"], true);?>
						</span>
					<?}?>
				</span>															
			<?}?>
			<meta itemprop="price" content="<?=$arElement["MIN_PRICE"]["RATIO_PRICE"]?>" />
			<meta itemprop="priceCurrency" content="<?=$arElement["MIN_PRICE"]["CURRENCY"]?>" />
			<?if($arElement["CAN_BUY"]) {?>
				<meta content="InStock" itemprop="availability" />
			<?} elseif(!$arElement["CAN_BUY"]) {?>
				<meta content="OutOfStock" itemprop="availability" />									
			<?}
		}?>
	</div>
	<?//TOTAL_OFFERS_ITEM_UNIT//?>
	<span class="unit">
		<?//TOTAL_OFFERS_UNIT//
		if($haveOffers) {
			echo $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
		//ITEM_UNIT//
		} else {
			echo $arElement["CATALOG_MEASURE_RATIO"]." ".$arElement["CATALOG_MEASURE_NAME"];
		}?>
	</span>
	<?//TOTAL_OFFERS_ITEM_AVAILABILITY//?>
	<div class="available">
		<?//TOTAL_OFFERS_AVAILABILITY//
		if($haveOffers) {						
			if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 || !$arElement["CHECK_QUANTITY"]) {?>
				<?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>                     
                    <div class="avl">
                        <i class="fa fa-check-circle"></i>
                        <span>
                            <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : Loc::getMessage("CT_BCS_ELEMENT_AVAILABLE")) . ' ';
                            if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 && $inProductQnt) {
                                    if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arElement["TOTAL_OFFERS"]["QUANTITY"])
                                        echo Loc::getMessage("CT_BCS_ELEMENT_RELATIVE_QUANTITY_FEW");
                                    else
                                        echo Loc::getMessage("CT_BCS_ELEMENT_RELATIVE_QUANTITY_MANY");
                                }
                            } else {
                                if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 && $inProductQnt)
                                    echo " " . $arElement["TOTAL_OFFERS"]["QUANTITY"];
                            }?>
                        </span>
                    </div>
                <?}?>  
			<?} else {?>
				<div class="not_avl">
					<i class="fa fa-times-circle"></i>
					<span><?=Loc::getMessage("CT_BCS_ELEMENT_NOT_AVAILABLE")?></span>
				</div>
			<?}
		//ITEM_AVAILABILITY//
		} else {							
			if($arElement["CAN_BUY"]) {?>								
				<?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>
                    <div class="avl">
                        <i class="fa fa-check-circle"></i>
                        <span>
                            <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : Loc::getMessage("CT_BCS_ELEMENT_AVAILABLE")) . ' ';
                            if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                if($arElement["CHECK_QUANTITY"] && $inProductQnt) {
                                    if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arElement["CATALOG_QUANTITY"])
                                        echo Loc::getMessage("CT_BCS_ELEMENT_RELATIVE_QUANTITY_FEW");
                                    else
                                        echo Loc::getMessage("CT_BCS_ELEMENT_RELATIVE_QUANTITY_MANY");
                                }
                            } else {
                                if($arElement["CHECK_QUANTITY"] && $inProductQnt)
                                    echo " " . $arElement["CATALOG_QUANTITY"];
                            }?>
                        </span>
                    </div>
                <?}?>                
			<?} elseif(!$arElement["CAN_BUY"]) {?>								
				<div class="not_avl">
					<i class="fa fa-times-circle"></i>
					<span><?=Loc::getMessage("CT_BCS_ELEMENT_NOT_AVAILABLE")?></span>
				</div>
			<?}
		}?>
	</div>						
	<?//OFFERS_ITEM_BUY//?>
	<div class="buy_more">
		<?//OFFERS_BUY//
		if($haveOffers) {?>								
			<div class="add2basket_block">
				<form action="javascript:void(0)" class="add2basket_form">
					<div class="qnt_cont">
						<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$itemIds['ID']?>"><span>-</span></a>
						<input type="text" id="quantity_<?=$itemIds['ID']?>" name="quantity" class="quantity" value="<?=$arElement['TOTAL_OFFERS']['MIN_PRICE']['MIN_QUANTITY']?>"/>
						<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$itemIds['ID']?>"><span>+</span></a>
					</div>
					<button type="button" id="<?=$itemIds['PROPS_BTN']?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i></button>
				</form>
			</div>
		<?//ITEM_BUY//
		} else {
			if($arElement["CAN_BUY"]) {
				if($arElement["MIN_PRICE"]["RATIO_PRICE"] <= 0) {
					//ITEM_ASK_PRICE//?>
					<a id="<?=$itemIds['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="ask_price"><i class="fa fa-comment-o"></i><span><?=Loc::getMessage("CT_BCS_ELEMENT_ASK_PRICE_SHORT")?></span></a>
				<?} else {?>
					<div class="add2basket_block">
						<?if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])) {?>
							<form action="javascript:void(0)" class="add2basket_form">
						<?} else {?>
							<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
						<?}?>
							<div class="qnt_cont">
								<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$itemIds['ID']?>"><span>-</span></a>
								<input type="text" id="quantity_<?=$itemIds['ID']?>" name="quantity" class="quantity" value="<?=$arElement['MIN_PRICE']['MIN_QUANTITY']?>"/>
								<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$itemIds['ID']?>"><span>+</span></a>
							</div>
							<?if(!isset($arElement["SELECT_PROPS"]) || empty($arElement["SELECT_PROPS"])) {?>
								<input type="hidden" name="ID" value="<?=$arElement['ID']?>"/>
								<?if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
									$props = array();
									$props[] = array(
										"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
										"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
										"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
									);												
									$props = strtr(base64_encode(serialize($props)), "+/=", "-_,");?>
									<input type="hidden" name="PROPS" value="<?=$props?>" />
								<?}
							}?>
							<button type="button" id="<?=(isset($arElement['SELECT_PROPS']) && !empty($arElement['SELECT_PROPS']) ? $itemIds['PROPS_BTN'] : $itemIds['BTN_BUY']);?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i></button>
						</form>
					</div>
				<?}
			} elseif(!$arElement["CAN_BUY"]) {
				//ITEM_UNDER_ORDER//?>
				<a id="<?=$itemIds['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="under_order"><i class="fa fa-clock-o"></i><span><?=Loc::getMessage("CT_BCS_ELEMENT_UNDER_ORDER")?></span></a>
			<?}
		}?>						
		<?//ITEM_COMPARE//
		if($arParams["DISPLAY_COMPARE"]=="Y") {?>
			<div class="compare">
				<a href="javascript:void(0)" class="catalog-item-compare" id="catalog_add2compare_link_<?=$itemIds['ID']?>" onclick="return addToCompare('<?=$arElement["COMPARE_URL"]?>', 'catalog_add2compare_link_<?=$itemIds["ID"]?>', '<?=SITE_DIR?>');" title="<?=Loc::getMessage('CT_BCS_ELEMENT_ADD_TO_COMPARE')?>" rel="nofollow"><i class="fa fa-bar-chart"></i><i class="fa fa-check"></i></a>
			</div>
		<?}
		//OFFERS_DELAY//
		if($haveOffers) {
			if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CAN_BUY"] && $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["RATIO_PRICE"] > 0) {
				$props = array();
				if(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {
					$props[] = array(
						"NAME" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["NAME"],
						"CODE" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["CODE"],
						"VALUE" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"]
					);																
				}
				foreach($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISPLAY_PROPERTIES"] as $propOffer) {
					if($propOffer["PROPERTY_TYPE"] != "S") {
						$props[] = array(
							"NAME" => $propOffer["NAME"],
							"CODE" => $propOffer["CODE"],
							"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
						);
					}
				}
				$props = !empty($props) ? strtr(base64_encode(serialize($props)), "+/=", "-_,") : "";?>
				<div class="delay">
					<a href="javascript:void(0)" id="catalog-item-delay-min-<?=$itemIds['ID'].'-'.$arElement['TOTAL_OFFERS']['MIN_PRICE']['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', 'quantity_<?=$itemIds["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-min-<?=$itemIds["ID"]."-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=SITE_DIR?>')" title="<?=Loc::getMessage('CT_BCS_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
				</div>
			<?}
		//ITEM_DELAY//
		} else {
			if($arElement["CAN_BUY"] && $arElement["MIN_PRICE"]["RATIO_PRICE"] > 0) {							
				$props = "";
				if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])) {		
					$props = array();
					$props[] = array(
						"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
						"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
						"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
					);
					$props = strtr(base64_encode(serialize($props)), "+/=", "-_,");
				}?>
				<div class="delay">
					<a href="javascript:void(0)" id="catalog-item-delay-<?=$itemIds['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["ID"]?>', 'quantity_<?=$itemIds["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$itemIds["ID"]?>', '<?=SITE_DIR?>')" title="<?=Loc::getMessage('CT_BCS_ELEMENT_ADD_TO_DELAY')?>" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
				</div>
			<?}
		}?>
	</div>
</div>