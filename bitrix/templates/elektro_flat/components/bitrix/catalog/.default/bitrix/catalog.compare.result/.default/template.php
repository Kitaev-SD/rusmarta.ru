<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$curPage = $APPLICATION->GetCurPage();

global $arSetting;
$inProductQnt = in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"]);

$itemsCnt = count($arResult["ITEMS"]);
$delUrlID = "";

foreach($arResult["ITEMS"] as $arElement) {
	$delUrlID .= "&ID[]=".$arElement["ID"];
}

//COMPARE_LIST//?>
<div class="compare-list-result">
	<div class="sort tabfilter">
		<div class="sorttext"><?=GetMessage("CATALOG_CHARACTERISTICS_LABEL")?>:</div>
		<?if($arResult["DIFFERENT"]) {?>
			<a class="sortbutton" href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow">
				<span class="def"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS_MOBILE")?></span>
			</a>
			<a class="sortbutton current" href="javascript:void(0)">
				<span class="def"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ONLY_DIFFERENT_MOBILE")?></span>
			</a>
		<?} else {?>
			<a class="sortbutton current" href="javascript:void(0)">
				<span class="def"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS_MOBILE")?></span>
			</a>
			<a class="sortbutton" href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow">
				<span class="def"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ONLY_DIFFERENT_MOBILE")?></span>
			</a>
		<?}?>
	</div>
	<?$i = 0;?>
	<div class="compare-grid">
		<?if($itemsCnt > 4) {?>
			<table class="compare-grid" style="width:<?=($itemsCnt*25 + 25)?>%; table-layout: fixed;">
		<?} else {?>
			<table class="compare-grid">
				<col />
				<col span="<?=$itemsCnt?>" width="<?=round(100/$itemsCnt)?>%" />
		<?}?>
		<tbody>
			<?//COMPARE_FIELDS//
			$i++;
			foreach($arResult["ITEMS"][0]["FIELDS"] as $key_field => $field) {?>				
				<tr>
					<td class="compare-property"></td>
					<?foreach($arResult["ITEMS"] as $key => $arElement) {?>
						<td>
							<?switch($key_field) {
								//COMPARE_NAME//
								case "NAME":?>
									<a class="compare-title" href="<?=$arElement['DETAIL_PAGE_URL']?>"><?=$arElement[$key_field]?></a>
								<?break;
								//COMPARE_PREVIEW_PICTURE//
								case "PREVIEW_PICTURE":								
									if(is_array($arElement["FIELDS"][$key_field])) {?>
										<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
											<img src="<?=$arElement['FIELDS'][$key_field]['SRC']?>" width="<?=$arElement['FIELDS'][$key_field]['WIDTH']?>" height="<?=$arElement['FIELDS'][$key_field]['HEIGHT']?>" alt="<?=$arElement['FIELDS'][$key_field]['ALT']?>" title="<?=$arElement['FIELDS'][$key_field]['TITLE']?>" />
										</a>
									<?} else {?>
										<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" />
										</a>
									<?}
								break;								
								//COMPARE_FIELD//
								default:
									echo $arElement["FIELDS"][$key_field];
								break;
							}?>
						</td>
					<?}?>
				</tr>
				<?$i++;
			}
			//COMPARE_DELETE//?>			
			<tr class="compare-delete">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement) {?>
					<td>
						<a class="btn_buy apuo compare-delete-item" href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID']."&ID[]=".$arElement['ID'],array("action", "IBLOCK_ID", "ID")))?>" title="<?=GetMessage('CATALOG_REMOVE_PRODUCT')?>"><i class="fa fa-trash-o"></i><?=GetMessage("CATALOG_REMOVE_PRODUCT")?></a>
					</td>
				<?}?>
			</tr>
			<?//COMPARE_PROPERTIES//
			foreach($arResult["SHOW_PROPERTIES"] as $key_prop => $arProperty) {
				$arCompare = Array();
				foreach($arResult["ITEMS"] as $key => $arElement) {
					$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$key_prop]["VALUE"];
					if(is_array($arPropertyValue)) {
						sort($arPropertyValue);
						$arPropertyValue = implode(" / ", $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				$diff = (count(array_unique($arCompare)) > 1 ? true : false);
				if($diff || !$arResult["DIFFERENT"]) {?>
					<tr<?if($i%2 == 0) echo ' class="alt"';?>>
						<?if(!empty($arProperty["VALUE"])) {?>
							<td class="compare-property"><?=$arProperty["NAME"]?></td>
							<?foreach($arResult["ITEMS"] as $key => $arElement) {
								if($diff) {?>
									<td>
										<?if($key_prop == "MANUFACTURER") {
											if(is_array($arElement["PROPERTIES"][$key_prop]["PREVIEW_PICTURE"])) {?>
												<img src="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" title="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" style="margin:0px 0px 3px 0px;" />
												<br />
											<?}
										}?>
										<?=(is_array($arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) ? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) : $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]);?>
									</td>
								<?} else {?>
									<td>
										<?if($key_prop == "MANUFACTURER") {
											if(is_array($arElement["PROPERTIES"][$key_prop]["PREVIEW_PICTURE"])) {?>
												<img src="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" title="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" style="margin:0px 0px 3px 0px;" />
												<br />
											<?}
										}?>
										<?=(is_array($arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) ? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) : $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]);?>
									</td>
								<?}
							}?>
						<?}?>
					</tr>
					<?$i++;
				}
			}
			//OFFERS_COMPARE_PRICE//?>
			<tr class="price">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement) {
					//PRICES//
					$price = $currency = false;
					if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
						$price = CCurrencyLang::GetCurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
						if($price["HIDE_ZERO"] == "Y")
							if(round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], 0))
								$price["DECIMALS"] = 0;
					} else {
						$price = CCurrencyLang::GetCurrencyFormat($arElement["MIN_PRICE"]["CURRENCY"], LANGUAGE_ID);
						if($price["HIDE_ZERO"] == "Y")
							if(round($arElement["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arElement["MIN_PRICE"]["DISCOUNT_VALUE"], 0))
								$price["DECIMALS"] = 0;
					}
					if(empty($price["THOUSANDS_SEP"]))
						$price["THOUSANDS_SEP"] = " ";
					$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);?>
					<td>
						<?//OFFERS_PRICE//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0) {?>
								<span class="item-no-price">
									<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>									
									<span class="unit">
										<span><?=(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
									</span>
								</span>
							<?} else {
								if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] < $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["VALUE"]) {?>	
									<span class="catalog-item-price-old">
										<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_VALUE"];?>								
									</span>
									<span class="catalog-item-price-percent">									
										<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"];?>
									</span>
								<?}?>
								<span class="catalog-item-price">
									<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y") ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span>" : "";?>
									<?=number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
									<span class="unit">
										<?=$currency?>
										<span><?=(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
									</span>
								</span>
								<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
									<span class="catalog-item-price-reference">
										<?=CCurrencyLang::CurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
									</span>
								<?}
							}
							//OFFERS_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 || !$arElement["CHECK_QUANTITY"]) {?>
									 <?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>                      
                                        <div class="avl">
                                            <i class="fa fa-check-circle"></i>
                                            <span>
                                                <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : GetMessage("CATALOG_ELEMENT_AVAILABLE")) . ' ';
                                                if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                    if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 && $inProductQnt) {
                                                        if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arElement["TOTAL_OFFERS"]["QUANTITY"])
                                                            echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW");
                                                        else
                                                            echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY");
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
										<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?}?>
							</div>		
						<?//COMPARE_PRICE//
						} else {
							if($arElement["MIN_PRICE"]["CAN_ACCESS"]) {
								if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0) {?>
									<span class="item-no-price">
										<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>											
										<span class="unit">
											<span><?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["CATALOG_MEASURE_NAME"] : "";?></span>
										</span>
									</span>
								<?} else {
									if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] < $arElement["MIN_PRICE"]["VALUE"]) {?>									
										<span class="catalog-item-price-old">
											<?=$arElement["MIN_PRICE"]["PRINT_VALUE"];?>													
										</span>
										<span class="catalog-item-price-percent">
											<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arElement["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"];?>
										</span>
									<?}?>
									<span class="catalog-item-price">
										<?=number_format($arElement["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>		
										<span class="unit">
											<?=$currency?>
											<span><?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["CATALOG_MEASURE_NAME"] : "";?></span>
										</span>
									</span>
									<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])) {?>
										<span class="catalog-item-price-reference">
											<?=CCurrencyLang::CurrencyFormat($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["MIN_PRICE"]["CURRENCY"], true);?>
										</span>
									<?}
								}
							}                            
							//COMPARE_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["CAN_BUY"]) {?>									
                                    <?if($arParams['SHOW_MAX_QUANTITY'] !== 'N') { ?>
                                        <div class="avl">
                                            <i class="fa fa-check-circle"></i>
                                            <span>
                                                <?=(!empty($arParams["MESS_SHOW_MAX_QUANTITY"]) ? $arParams["MESS_SHOW_MAX_QUANTITY"] : GetMessage("CATALOG_ELEMENT_AVAILABLE")) . ' ';
                                                if($arParams['SHOW_MAX_QUANTITY'] === 'M') {
                                                    if($arElement["CHECK_QUANTITY"] && $inProductQnt) {
                                                        if($arParams['RELATIVE_QUANTITY_FACTOR'] > $arElement["CATALOG_QUANTITY"])
                                                            echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_FEW");
                                                        else
                                                            echo GetMessage("CATALOG_ELEMENT_RELATIVE_QUANTITY_MANY");
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
										<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?}?>
							</div>
						<?}?>
					</td>
				<?}?>
			</tr>
			<?//OFFERS_COMPARE_BUY//?>
			<tr class="buy">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement) {
					$arItemIDs = array(
						"ID" => $arElement["STR_MAIN_ID"],
						"POPUP_BTN" => $arElement["STR_MAIN_ID"]."_popup_btn",
						"PROPS_BTN" => $arElement["STR_MAIN_ID"]."_props_btn",
						"BTN_BUY" => $arElement["STR_MAIN_ID"]."_btn_buy"
					);?>
					<td>
						<div class="buy_more">
							<?//OFFERS_BUY//
							if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {?>
								<div class="add2basket_block">
									<form action="<?=$curPage?>" class="add2basket_form">
										<div class="qnt_cont">
											<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
											<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['TOTAL_OFFERS']['MIN_PRICE']['CATALOG_MEASURE_RATIO']?>"/>
											<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
										</div>
										<button type="button" id="<?=$arItemIDs['PROPS_BTN']?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
									</form>
								</div>
							<?//COMPARE_BUY//
							} else {?>
								<div class="add2basket_block">
									<?if($arElement["CAN_BUY"]) {
										if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0) {
											//COMPARE_ASK_PRICE//?>
											<a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="ask_price"><i class="fa fa-comment-o"></i><span><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_FULL")?></span></a>
										<?} else {
											if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])) {?>
												<form action="<?=$curPage?>" class="add2basket_form">
											<?} else {?>									
												<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
											<?}?>
												<div class="qnt_cont">
													<a href="javascript:void(0)" class="minus" id="quantity_minus_<?=$arItemIDs['ID']?>"><span>-</span></a>
													<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['CATALOG_MEASURE_RATIO']?>"/>
													<a href="javascript:void(0)" class="plus" id="quantity_plus_<?=$arItemIDs['ID']?>"><span>+</span></a>
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
												<button type="button" id="<?=(isset($arElement['SELECT_PROPS']) && !empty($arElement['SELECT_PROPS']) ? $arItemIDs['PROPS_BTN'] : $arItemIDs['BTN_BUY']);?>" class="btn_buy" name="add2basket"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
											</form>												
										<?}										
									} elseif(!$arElement["CAN_BUY"]) {
										//COMPARE_UNDER_ORDER//?>
										<a id="<?=$arItemIDs['POPUP_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow" data-action="under_order"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
									<?}?>
								</div>
							<?}?>							
						</div>
					</td>
				<?}?>
			</tr>
			<?//OFFERS_COMPARE_DELAY//?>
			<tr class="delay">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement) {
					$arItemIDs = array(
						"ID" => $arElement["STR_MAIN_ID"]
					);?>
					<td align="center">
						<?//OFFERS_DELAY//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) {
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CAN_BUY"] && $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] > 0) {
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
									<a href="javascript:void(0)" id="catalog-item-delay-min-<?=$arItemIDs['ID'].'-'.$arElement['TOTAL_OFFERS']['MIN_PRICE']['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-min-<?=$arItemIDs["ID"]."-".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><span class="delay_cont"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span class="delay_text"><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></span></a>
								</div>
							<?}
						//COMPARE_DELAY//
						} else {
							if($arElement["CAN_BUY"] && $arElement["MIN_PRICE"]["DISCOUNT_VALUE"] > 0) {
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
									<a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><span class="delay_cont"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span class="delay_text"><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></span></a>
								</div>
							<?}
						}?>
					</td>
				<?}?>
			</tr>
		</tbody>
		</table>
	</div>
	<?if(strlen($delUrlID) > 0) {
		$delUrl = htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID'].$delUrlID,array("action", "IBLOCK_ID", "ID")));?>
		<a class="btn_buy apuo compare-delete-item-all" href="<?=$delUrl?>"><i class="fa fa-trash-o"></i><?=GetMessage("CATALOG_DELETE_ALL")?></a>
	<?}?>		
</div>

<?$signer = new \Bitrix\Main\Security\Sign\Signer;
$signedParams = $signer->sign(base64_encode(serialize($arParams)), "catalog.compare.result");

//JS//?>
<script type="text/javascript">
	BX.ready(function() {
		BX.message({			
			COMPARE_ADDITEMINCART_ADDED: "<?=GetMessageJS('CATALOG_ELEMENT_ADDED')?>",
			COMPARE_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_TITLE')?>",			
			COMPARE_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
			COMPARE_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
			COMPARE_SITE_DIR: "<?=SITE_DIR?>",
			COMPARE_POPUP_WINDOW_MORE_OPTIONS: "<?=GetMessageJS('CATALOG_ELEMENT_MORE_OPTIONS')?>",			
			COMPARE_COMPONENT_TEMPLATE: "<?=$this->GetFolder();?>",
			COMPARE_OFFERS_VIEW: "<?=$arSetting['OFFERS_VIEW']['VALUE']?>",
			COMPARE_COMPONENT_PARAMS: "<?=CUtil::JSEscape($signedParams)?>"
		});	
		<?foreach($arResult["ITEMS"] as $key => $arElement) {
			if((isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])) || $arElement["SELECT_PROPS"]) {				
				$arJSParams = array(					
					"VISUAL" => array(
						"ID" => $arElement["STR_MAIN_ID"],
						"PROPS_BTN_ID" => $arElement["STR_MAIN_ID"]."_props_btn"
					),
					"PRODUCT" => array(
						"ID" => $arElement["ID"],
						"CHECK_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CHECK_QUANTITY"] : $arElement["CHECK_QUANTITY"],
						"QUANTITY_FLOAT" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? is_double($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]) : is_double($arElement["CATALOG_MEASURE_RATIO"]),
						"MAX_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_QUANTITY"] : $arElement["CATALOG_QUANTITY"],
						"STEP_QUANTITY" => isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) ? $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"] : $arElement["CATALOG_MEASURE_RATIO"]
					)
				);
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]))
					$arJSParams["OFFER"]["ID"] = $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"];
			} else {
				$arJSParams = array(					
					"VISUAL" => array(
						"ID" => $arElement["STR_MAIN_ID"],
						"POPUP_BTN_ID" => $arElement["STR_MAIN_ID"]."_popup_btn",
						"BTN_BUY_ID" => $arElement["STR_MAIN_ID"]."_btn_buy"
					),
					"PRODUCT" => array(
						"ID" => $arElement["ID"],
						"NAME" => $arElement["NAME"],
						"PICT" => is_array($arElement["FIELDS"]["PREVIEW_PICTURE"]) ? $arElement["FIELDS"]["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
						"CHECK_QUANTITY" => $arElement["CHECK_QUANTITY"],						
						"QUANTITY_FLOAT" => is_double($arElement["CATALOG_MEASURE_RATIO"]),
						"MAX_QUANTITY" => $arElement["CATALOG_QUANTITY"],
						"STEP_QUANTITY" => $arElement["CATALOG_MEASURE_RATIO"]
					)
				);
			}
			$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $arElement["STR_MAIN_ID"]);?>
			var <?=$strObName?> = new JCCatalogSection(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		<?}?>
	});
</script>