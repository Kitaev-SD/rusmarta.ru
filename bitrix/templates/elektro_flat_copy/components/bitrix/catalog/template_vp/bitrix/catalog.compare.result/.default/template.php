<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;

$itemsCnt = count($arResult["ITEMS"]);
$delUrlID = "";

foreach($arResult["ITEMS"] as $arElement):
	$delUrlID .= "&ID[]=".$arElement["ID"];
endforeach;

//COMPARE_LIST//?>
<div class="compare-list-result">
	<div class="sort tabfilter">
		<div class="sorttext"><?=GetMessage("CATALOG_CHARACTERISTICS_LABEL")?>:</div>
		<?if($arResult["DIFFERENT"]):?>
			<a class="sortbutton" href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("DIFFERENT=N",array("DIFFERENT")))?>" rel="nofollow">
				<span class="def"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS_MOBILE")?></span>
			</a>
			<a class="sortbutton current" href="javascript:void(0)">
				<span class="def"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ONLY_DIFFERENT_MOBILE")?></span>
			</a>
		<?else:?>
			<a class="sortbutton current" href="javascript:void(0)">
				<span class="def"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS_MOBILE")?></span>
			</a>
			<a class="sortbutton" href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("DIFFERENT=Y",array("DIFFERENT")))?>" rel="nofollow">
				<span class="def"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></span>
				<span class="mob"><?=GetMessage("CATALOG_ONLY_DIFFERENT_MOBILE")?></span>
			</a>
		<?endif;?>
	</div>
	<?$i = 0;?>
	<div class="compare-grid">
		<?if($itemsCnt > 4):?>
			<table class="compare-grid" style="width:<?=($itemsCnt*25 + 25)?>%; table-layout: fixed;">
		<?else:?>
			<table class="compare-grid">
				<col />
				<col span="<?=$itemsCnt?>" width="<?=round(100/$itemsCnt)?>%" />
		<?endif;?>
		<tbody>
			<?//COMPARE_FIELDS//
			$i++;
			foreach($arResult["ITEMS"][0]["FIELDS"] as $key_field => $field):?>
				<tr>
					<td class="compare-property"></td>
					<?foreach($arResult["ITEMS"] as $key => $arElement):?>
						<td>
							<?switch($key_field):
								//COMPARE_NAME//
								case "NAME":?>
									<a class="compare-title" href="<?=$arElement['DETAIL_PAGE_URL']?>"><?=$arElement[$key_field]?></a>
								<?break;
								//COMPARE_PREVIEW_PICTURE//
								case "PREVIEW_PICTURE":								
									if(is_array($arElement["FIELDS"][$key_field])):?>
										<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
											<img src="<?=$arElement['FIELDS'][$key_field]['SRC']?>" width="<?=$arElement['FIELDS'][$key_field]['WIDTH']?>" height="<?=$arElement['FIELDS'][$key_field]['HEIGHT']?>" alt="<?=$arElement['FIELDS'][$key_field]['ALT']?>" title="<?=$arElement['FIELDS'][$key_field]['TITLE']?>" />
										</a>
									<?else:?>
										<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
											<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" />
										</a>
									<?endif;
								break;								
								//COMPARE_FIELD//
								default:
									echo $arElement["FIELDS"][$key_field];
								break;
							endswitch;?>
						</td>
					<?endforeach;?>
				</tr>
				<?$i++;
			endforeach;
			//COMPARE_DELETE//?>			
			<tr class="compare-delete">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement):?>
					<td>
						<a class="btn_buy apuo compare-delete-item" href="<?=htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID']."&ID[]=".$arElement['ID'],array("action", "IBLOCK_ID", "ID")))?>" title="<?=GetMessage('CATALOG_REMOVE_PRODUCT')?>"><i class="fa fa-trash-o"></i><?=GetMessage("CATALOG_REMOVE_PRODUCT")?></a>
					</td>
				<?endforeach;?>
			</tr>
			<?//COMPARE_PROPERTIES//
			foreach($arResult["SHOW_PROPERTIES"] as $key_prop => $arProperty):
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
				if($diff || !$arResult["DIFFERENT"]):?>
					<tr<?if($i%2 == 0) echo ' class="alt"';?>>
						<?if(!empty($arProperty["VALUE"])) {?>
							<td class="compare-property"><?=$arProperty["NAME"]?></td>
							<?foreach($arResult["ITEMS"] as $key => $arElement):
								if($diff):?>
									<td>
										<?if($key_prop == "MANUFACTURER"):
											if(is_array($arElement["PROPERTIES"][$key_prop]["PREVIEW_PICTURE"])):?>
												<img src="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" title="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" style="margin:0px 0px 3px 0px;" />
												<br />
											<?endif;
										endif;?>
										<?=(is_array($arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) ? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) : $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]);?>
									</td>
								<?else:?>
									<td>
										<?if($key_prop == "MANUFACTURER"):
											if(is_array($arElement["PROPERTIES"][$key_prop]["PREVIEW_PICTURE"])):?>
												<img src="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PROPERTIES'][$key_prop]['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" title="<?=$arElement['PROPERTIES'][$key_prop]['NAME']?>" style="margin:0px 0px 3px 0px;" />
												<br />
											<?endif;
										endif;?>
										<?=(is_array($arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) ? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]) : $arElement["DISPLAY_PROPERTIES"][$key_prop]["DISPLAY_VALUE"]);?>
									</td>
								<?endif;
							endforeach;?>
						<?}?>
					</tr>
					<?$i++;
				endif;
			endforeach;
			//OFFERS_COMPARE_PRICE//?>
			<tr class="price">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement):?>
					<td>
						<?//OFFERS_PRICE//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
							$price = CCurrencyLang::GetCurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], "ru");
							if(empty($price["THOUSANDS_SEP"])):
								$price["THOUSANDS_SEP"] = " ";
							endif;							
							if($price["HIDE_ZERO"] == "Y"):								
								if(round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], 0)):
									$price["DECIMALS"] = 0;
								endif;
							endif;
							$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);
							
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):?>
								<span class="item-no-price">
									<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>									
									<span class="unit">
										<span><?=(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
									</span>
								</span>
							<?else:
								if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] < $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["VALUE"]):?>	
									<span class="catalog-item-price-old">
										<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_VALUE"];?>								
									</span>
									<span class="catalog-item-price-percent">									
										<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PRINT_DISCOUNT_DIFF"];?>
									</span>
								<?endif;?>
								<span class="catalog-item-price">
									<?=($arElement["TOTAL_OFFERS"]["FROM"] == "Y") ? "<span class='from'>".GetMessage("CATALOG_ELEMENT_FROM")."</span>" : "";?>
									<?=number_format($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
									<span class="unit">
										<?=$currency?>
										<span><?=(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_NAME"] : "";?></span>
									</span>
								</span>
								<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
									<span class="catalog-item-price-reference">
										<?=CCurrencyLang::CurrencyFormat($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CURRENCY"], true);?>
									</span>
								<?endif;
							endif;
							//OFFERS_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["TOTAL_OFFERS"]["QUANTITY"] > 0 || $arElement["CATALOG_QUANTITY_TRACE"] == "N"):?>
									<div class="avl">
										<i class="fa fa-check-circle"></i>							
										<span>
											<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
											if($arElement["CATALOG_QUANTITY_TRACE"] == "Y"):
												if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
													echo " ".$arElement["TOTAL_OFFERS"]["QUANTITY"];
												endif;
											endif;?>
										</span>
									</div>
								<?else:?>
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?endif;?>
							</div>		
						<?//COMPARE_PRICE//
						else:
							foreach($arElement["PRICES"] as $code => $arPrice):
								if($arPrice["MIN_PRICE"] == "Y"):
									if($arPrice["CAN_ACCESS"]):
													
										$price = CCurrencyLang::GetCurrencyFormat($arPrice["CURRENCY"], "ru");
										if(empty($price["THOUSANDS_SEP"])):
											$price["THOUSANDS_SEP"] = " ";
										endif;										
										if($price["HIDE_ZERO"] == "Y"):											
											if(round($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"], 0)):
												$price["DECIMALS"] = 0;
											endif;
										endif;
										$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);
													
										if($arPrice["DISCOUNT_VALUE"] <= 0):?>
											<span class="item-no-price">
												<?=GetMessage("CATALOG_ELEMENT_NO_PRICE")?>											
												<span class="unit">
													<span><?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["CATALOG_MEASURE_NAME"] : "";?></span>
												</span>
											</span>
										<?else:
											if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>									
												<span class="catalog-item-price-old">
													<?=$arPrice["PRINT_VALUE"];?>													
												</span>
												<span class="catalog-item-price-percent">
													<?=GetMessage("CATALOG_ELEMENT_SKIDKA")." ".$arPrice["PRINT_DISCOUNT_DIFF"];?>
												</span>
											<?endif;?>
											<span class="catalog-item-price">
												<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>		
												<span class="unit">
													<?=$currency?>
													<span><?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? GetMessage("CATALOG_ELEMENT_UNIT")." ".$arElement["CATALOG_MEASURE_NAME"] : "";?></span>
												</span>
											</span>
											<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
												<span class="catalog-item-price-reference">
													<?=CCurrencyLang::CurrencyFormat($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arPrice["CURRENCY"], true);?>
												</span>
											<?endif;
										endif;									
									endif;
								endif;
							endforeach;
							//COMPARE_AVAILABILITY//?>
							<div class="available">
								<?if($arElement["CAN_BUY"]):?>
									<div class="avl">
										<i class="fa fa-check-circle"></i>
										<span>
											<?=GetMessage("CATALOG_ELEMENT_AVAILABLE");
											if($arElement["CATALOG_QUANTITY_TRACE"] == "Y"):
												if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
													echo " ".$arElement["CATALOG_QUANTITY"];
												endif;
											endif;?>
										</span>
									</div>
								<?elseif(!$arElement["CAN_BUY"]):?>
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE")?></span>
									</div>
								<?endif;?>
							</div>
						<?endif;?>
					</td>
				<?endforeach;?>
			</tr>
			<?//OFFERS_COMPARE_BUY//?>
			<tr class="buy">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement):
					$strMainID = $this->GetEditAreaId($arElement["ID"]);
					$arItemIDs = array(
						"ID" => $strMainID,
						"BTN_BUY" => $strMainID."_btn_buy"
					);?>
					<td>
						<div class="buy_more">
							<?//OFFERS_BUY//
							if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):?>
								<div class="add2basket_block">
									<form action="<?=$APPLICATION->GetCurPage()?>" class="add2basket_form">
										<div class="qnt_cont">
											<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_<?=$arItemIDs["ID"]?>').value > <?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>) BX('quantity_<?=$arItemIDs["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]?>').value)-<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
											<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['TOTAL_OFFERS']['MIN_PRICE']['CATALOG_MEASURE_RATIO']?>"/>
											<a href="javascript:void(0)" class="plus" onclick="BX('quantity_<?=$arItemIDs["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]?>').value)+<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
										</div>
										<button type="button" class="btn_buy" name="add2basket" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>" onclick="OpenPropsPopup('<?=$arItemIDs["ID"]?>'<?=($arSetting["OFFERS_VIEW"]["VALUE"] == "LIST" ? ", true" : "");?>);"><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
									</form>
								</div>
							<?//COMPARE_BUY//
							else:?>
								<div class="add2basket_block">
									<?if($arElement["CAN_BUY"]):
										if($arElement["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
											//COMPARE_ASK_PRICE//?>
											<a class="btn_buy apuo" id="ask_price_anch_<?=$arItemIDs['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span><?=GetMessage("CATALOG_ELEMENT_ASK_PRICE_FULL")?></span></a>
										<?else:
											if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])):?>
												<form action="<?=$APPLICATION->GetCurPage()?>" class="add2basket_form">
											<?else:?>									
												<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
											<?endif;?>
												<div class="qnt_cont">
													<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_<?=$arItemIDs["ID"]?>').value > <?=$arElement["CATALOG_MEASURE_RATIO"]?>) BX('quantity_<?=$arItemIDs["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]?>').value)-<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
													<input type="text" id="quantity_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['CATALOG_MEASURE_RATIO']?>"/>
													<a href="javascript:void(0)" class="plus" onclick="BX('quantity_<?=$arItemIDs["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]?>').value)+<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
												</div>
												<?if(!isset($arElement["SELECT_PROPS"]) || empty($arElement["SELECT_PROPS"])):?>
													<input type="hidden" name="ID" value="<?=$arElement['ID']?>"/>				
													<?if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])):
														$props = array();
														$props[] = array(
															"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
															"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
															"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
														);												
														$props = strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,');?>
														<input type="hidden" name="PROPS" value="<?=$props?>" />
													<?endif;
												endif;?>
												<button type="button" class="btn_buy" name="add2basket" value="<?=GetMessage('CATALOG_ELEMENT_ADD_TO_CART')?>"<?=(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"]) ? " onclick=\"OpenPropsPopup('".$arItemIDs["ID"]."')\"" : " id='".$arItemIDs["BTN_BUY"]."'");?>><i class="fa fa-shopping-cart"></i><span><?=GetMessage("CATALOG_ELEMENT_ADD_TO_CART")?></span></button>
											</form>												
										<?endif;										
									elseif(!$arElement["CAN_BUY"]):
										//COMPARE_UNDER_ORDER//?>
										<a class="btn_buy apuo" id="under_order_anch_<?=$arItemIDs['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span><?=GetMessage("CATALOG_ELEMENT_UNDER_ORDER")?></span></a>
									<?endif;?>
								</div>
							<?endif;?>							
						</div>
					</td>
				<?endforeach;?>
			</tr>
			<?//OFFERS_COMPARE_DELAY//?>
			<tr class="delay">
				<td class="compare-property"></td>
				<?foreach($arResult["ITEMS"] as $key => $arElement):
					$strMainID = $this->GetEditAreaId($arElement["ID"]);
					$arItemIDs = array(
						"ID" => $strMainID
					);?>
					<td align="center">
						<?//OFFERS_DELAY//
						if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
							if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["CAN_BUY"]):
								if($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISCOUNT_VALUE"] > 0):
									$props = array();
									if(!empty($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"])):		
										$props[] = array(
											"NAME" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["NAME"],
											"CODE" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["CODE"],
											"VALUE" => $arElement["TOTAL_OFFERS"]["MIN_PRICE"]["PROPERTIES"]["ARTNUMBER"]["VALUE"]
										);																
									endif;
									foreach($arElement["TOTAL_OFFERS"]["MIN_PRICE"]["DISPLAY_PROPERTIES"] as $propOffer) {
										$props[] = array(
											"NAME" => $propOffer["NAME"],
											"CODE" => $propOffer["CODE"],
											"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
										);
									}
									$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>		
									<div class="delay">
										<a href="javascript:void(0)" id="catalog-item-delay-min-<?=$arItemIDs['ID'].'-'.$arElement['TOTAL_OFFERS']['MIN_PRICE']['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-min-<?=$arItemIDs['ID'].'-'.$arElement["TOTAL_OFFERS"]["MIN_PRICE"]["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><span class="delay_cont"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span class="delay_text"><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></span></a>
									</div>
								<?endif;
							endif;
						//COMPARE_DELAY//
						else:
							if($arElement["CAN_BUY"]):
								foreach($arElement["PRICES"] as $code => $arPrice):
									if($arPrice["MIN_PRICE"] == "Y"):
										if($arPrice["DISCOUNT_VALUE"] > 0):
											$props = "";
											if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])):		
												$props = array();
												$props[] = array(
													"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
													"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
													"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
												);
												$props = strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,');
											endif;?>
											<div class="delay">
												<a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arElement["ID"]?>', 'quantity_<?=$arItemIDs["ID"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><span class="delay_cont"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i><span class="delay_text"><?=GetMessage('CATALOG_ELEMENT_ADD_TO_DELAY')?></span></span></a>
											</div>
										<?endif;
									endif;
								endforeach;
							endif;
						endif;?>
					</td>
				<?endforeach;?>
			</tr>
		</tbody>
		</table>
	</div>
	<?if(strlen($delUrlID) > 0):
		$delUrl = htmlspecialchars($APPLICATION->GetCurPageParam("action=DELETE_FROM_COMPARE_RESULT&IBLOCK_ID=".$arParams['IBLOCK_ID'].$delUrlID,array("action", "IBLOCK_ID", "ID")));?>
		<a class="btn_buy apuo compare-delete-item-all" href="<?=$delUrl?>"><i class="fa fa-trash-o"></i><?=GetMessage("CATALOG_DELETE_ALL")?></a>
	<?endif;?>		
</div>

<?foreach($arResult["ITEMS"] as $key => $arElement):
	$strMainID = $this->GetEditAreaId($arElement["ID"]);
	if(!isset($arElement["OFFERS"]) || (isset($arElement["OFFERS"]) && empty($arElement["OFFERS"]))):
		if($arElement["CAN_BUY"] && $arElement["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
			//ASK_PRICE//
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $arElement["ID"],
				"ELEMENT_AREA_ID" => $strMainID,
				"ELEMENT_NAME" => $arElement["NAME"],
				"BUTTON_ID" => "ask_price_anch_".$strMainID,
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?elseif(!$arElement["CAN_BUY"]):
			//UNDER_ORDER//
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $arElement["ID"],
				"ELEMENT_AREA_ID" => $strMainID,
				"ELEMENT_NAME" => $arElement["NAME"],
				"BUTTON_ID" => "under_order_anch_".$strMainID,
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?endif;
	endif;
endforeach;

//POPUP_JS//	
$popupParams["MESS"] = array(	
	"CATALOG_ELEMENT_ARTNUMBER" => GetMessage("CATALOG_ELEMENT_ARTNUMBER"),
	"CATALOG_ELEMENT_NO_PRICE" => GetMessage("CATALOG_ELEMENT_NO_PRICE"),
	"CATALOG_ELEMENT_SKIDKA" => GetMessage("CATALOG_ELEMENT_SKIDKA"),
	"CATALOG_ELEMENT_UNIT" => GetMessage("CATALOG_ELEMENT_UNIT"),
	"CATALOG_ELEMENT_AVAILABLE" => GetMessage("CATALOG_ELEMENT_AVAILABLE"),
	"CATALOG_ELEMENT_NOT_AVAILABLE" => GetMessage("CATALOG_ELEMENT_NOT_AVAILABLE"),
	"CATALOG_ELEMENT_ADD_TO_CART" => GetMessage("CATALOG_ELEMENT_ADD_TO_CART"),
	"CATALOG_ELEMENT_ADDED" => GetMessage("CATALOG_ELEMENT_ADDED"),
	"CATALOG_ELEMENT_ASK_PRICE_FULL" => GetMessage("CATALOG_ELEMENT_ASK_PRICE_FULL"),
	"CATALOG_ELEMENT_ASK_PRICE_SHORT" => GetMessage("CATALOG_ELEMENT_ASK_PRICE_SHORT"),
	"CATALOG_ELEMENT_UNDER_ORDER" => GetMessage("CATALOG_ELEMENT_UNDER_ORDER"),									
	"CATALOG_ELEMENT_OFFERS_LIST" => GetMessage("CATALOG_ELEMENT_OFFERS_LIST"),
	"CATALOG_ELEMENT_OFFERS_LIST_IMAGE" => GetMessage("CATALOG_ELEMENT_OFFERS_LIST_IMAGE"),
	"CATALOG_ELEMENT_OFFERS_LIST_NAME" => GetMessage("CATALOG_ELEMENT_OFFERS_LIST_NAME"),
	"CATALOG_ELEMENT_OFFERS_LIST_PRICE" => GetMessage("CATALOG_ELEMENT_OFFERS_LIST_PRICE"),
	"CATALOG_ELEMENT_BOC_SHORT" => GetMessage("CATALOG_ELEMENT_BOC_SHORT")
);
$popupParams["SKU_PROPS"] = strtr(base64_encode(addslashes(gzcompress(serialize($arResult["SKU_PROPS"]),9))), '+/=', '-_,');	
$popupParams["PARAMS"] = strtr(base64_encode(addslashes(gzcompress(serialize($arParams),9))), '+/=', '-_,');	
$popupParams["SETTINGS"] = strtr(base64_encode(addslashes(gzcompress(serialize($arSetting),9))), '+/=', '-_,');	
foreach($arResult["ITEMS"] as $key => $arElement):
	$strMainID = $this->GetEditAreaId($arElement["ID"]);
	$arItemIDs = array(
		"ID" => $strMainID,				
		"BTN_BUY" => $strMainID."_btn_buy"
	);
	$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);			
	if($arElement["OFFERS"] || $arElement["SELECT_PROPS"]):
		//POPUP//
		$popupParams["STR_MAIN_ID"] = $strMainID;
		$popupParams["ELEMENT"] = strtr(base64_encode(addslashes(gzcompress(serialize($arElement),9))), '+/=', '-_,');?>
		<script type="text/javascript">
			if(!window.arSetParams) {
				window.arSetParams = [{'<?=$arItemIDs["ID"]?>' : <?=CUtil::PhpToJSObject($popupParams)?>}];
			} else {
				window.arSetParams.push({'<?=$arItemIDs["ID"]?>' : <?=CUtil::PhpToJSObject($popupParams)?>});
			}
		</script>
	<?else:
		//JS//
		$arJSParams = array(
			"PRODUCT_TYPE" => $arElement["CATALOG_TYPE"],
			"VISUAL" => array(
				"ID" => $arItemIDs["ID"],
				"BTN_BUY_ID" => $arItemIDs["BTN_BUY"],
			),
			"PRODUCT" => array(
				"ID" => $arElement["ID"],
				"NAME" => $arElement["NAME"],
				"PICT" => is_array($arElement["FIELDS"]["PREVIEW_PICTURE"]) ? $arElement["FIELDS"]["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
			)
		);?>
		<script type="text/javascript">
			var <?=$strObName;?> = new JCCatalogSection(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		</script>			
	<?endif;
endforeach;

//JS//?>	
<script type="text/javascript">
	BX.message({			
		COMPARE_ADDITEMINCART_ADDED: "<?=GetMessageJS('CATALOG_ELEMENT_ADDED')?>",
		COMPARE_POPUP_WINDOW_TITLE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_TITLE')?>",			
		COMPARE_POPUP_WINDOW_BTN_CLOSE: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_CLOSE')?>",
		COMPARE_POPUP_WINDOW_BTN_ORDER: "<?=GetMessageJS('CATALOG_ELEMENT_ADDITEMINCART_BTN_ORDER')?>",
		COMPARE_SITE_DIR: "<?=SITE_DIR?>"
	});
	if(!window.OpenPropsPopup) {
		function OpenPropsPopup(visual_id, offers_list) {
			offers_list = offers_list || false;

			if(window.arSetParams) {
				for(var obj in window.arSetParams) {
					if(window.arSetParams.hasOwnProperty(obj)) {
						for(var obj2 in window.arSetParams[obj]) {
							if(window.arSetParams[obj].hasOwnProperty(obj2)) {
								if(obj2 == visual_id)
									var curSetParams = window.arSetParams[obj][obj2]
							}
						}
					}
				}
			}
			BX.PropsSet =
			{			
				popup: null,
				arParams: {}
			};
			BX.PropsSet.popup = BX.PopupWindowManager.create(visual_id, null, {
				autoHide: offers_list == true ? false : true,
				offsetLeft: 0,
				offsetTop: 0,
				overlay: {
					opacity: 100
				},
				draggable: false,
				closeByEsc: false,
				closeIcon: { right : "-10px", top : "-10px"},
				titleBar: {content: BX.create("span", {html: "<?=GetMessage('CATALOG_ELEMENT_MORE_OPTIONS')?>"})},
				content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",
				events: {
					onAfterPopupShow: function()
					{													
						if(!BX(visual_id + "_info")) {
							BX.ajax.post(
								"<?=$this->GetFolder();?>/popup.php",
								{							
									arParams:curSetParams
								},
								BX.delegate(function(result)
								{
									this.setContent(result);
									var windowSize =  BX.GetWindowInnerSize(),
									windowScroll = BX.GetWindowScrollPos(),
									popupHeight = BX(visual_id).offsetHeight;
									BX(visual_id).style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
								},
								this)
							);
						} else {
							qntItems = BX.findChildren(BX(visual_id), {className: "quantity"}, true);
							if(!!qntItems && 0 < qntItems.length) {
								for(i = 0; i < qntItems.length; i++) {					
									qntItems[i].value = BX("quantity_" + visual_id).value;
								}
							}
						}
					}
				}
			});			
			BX.addClass(BX(visual_id), "pop-up more_options");
			if(offers_list == true) {
				BX.addClass(BX(visual_id), "offers-list");
			}
			close = BX.findChildren(BX(visual_id), {className: "popup-window-close-icon"}, true);
			if(!!close && 0 < close.length) {
				for(i = 0; i < close.length; i++) {					
					close[i].innerHTML = "<i class='fa fa-times'></i>";
				}
			}
			BX.PropsSet.popup.show();
		}
	}
</script>