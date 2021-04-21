<?define("NOT_CHECK_PERMISSIONS", true);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Loader,
	Bitrix\Main\Application,
	Bitrix\Main\Text\Encoding;

if(!Loader::includeModule("catalog"))
	return;

$request = Application::getInstance()->getContext()->getRequest();

$params = $request->getPost("arParams");
if(SITE_CHARSET != "utf-8")
	$params = Encoding::convertEncoding($params, "utf-8", SITE_CHARSET);

$arElement = unserialize(gzuncompress(stripslashes(base64_decode(strtr($params["ELEMENT"], "-_,", "+/=")))));
if(!is_array($arElement))
	return;

$arResult["SKU_PROPS"] = unserialize(gzuncompress(stripslashes(base64_decode(strtr($params["SKU_PROPS"], "-_,", "+/=")))));
$arMessage = $params["MESS"];
$arParams = unserialize(gzuncompress(stripslashes(base64_decode(strtr($params["PARAMS"], "-_,", "+/=")))));
$arSetting = unserialize(gzuncompress(stripslashes(base64_decode(strtr($params["SETTINGS"], "-_,", "+/=")))));
$strMainID = $params["STR_MAIN_ID"];
$arItemIDs = array(
	"ID" => $strMainID,
	"PICT" => $strMainID."_picture",
	"PRICE" => $strMainID."_price",
	"BUY" => $strMainID."_buy",
	"PROP_DIV" => $strMainID."_sku_tree",
	"PROP" => $strMainID."_prop_",
	"SELECT_PROP_DIV" => $strMainID."_propdiv",
	"SELECT_PROP" => $strMainID."_select_prop_",
	"BTN_BUY" => $strMainID."_btn_buy"
);
$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

//PREVIEW_PICTURE_ALT//
$strAlt = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"] : $arElement["NAME"]);

//PREVIEW_PICTURE_TITLE//
$strTitle = (isset($arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"]) && $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] != "" ? $arElement["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"] : $arElement["NAME"]);

//JS//?>
<script type="text/javascript">
	BX.ready(function() {
		<?//OFFERS_LIST_PROPS//
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):
			foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
				props = BX.findChildren(BX("catalog-offer-item-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>"), {className: "catalog-item-prop"}, true);
				if(!!props && 0 < props.length) {
					for(i = 0; i < props.length; i++) {
						if(!BX.hasClass(props[i], "empty")) {
							BX("catalog-item-props-mob-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>").appendChild(BX.create(
								"DIV",
								{
									props: {
										className: "catalog-item-prop"
									},
									html: props[i].innerHTML
								}
							));
						}
					}
				}
			<?endforeach;
		endif;
		
		//QUANTITY//?>
		qntItems = BX.findChildren(BX("<?=$arItemIDs['ID']?>"), {className: "quantity"}, true);			
		if(!!qntItems && 0 < qntItems.length) {
			for(i = 0; i < qntItems.length; i++) {					
				qntItems[i].value = BX("quantity_<?=$arItemIDs['ID']?>").value;
			}
		}

		//DISABLE_FORM_SUBMIT_ENTER//
		$(".add2basket_form").on("keyup keypress", function(e) {
			var keyCode = e.keyCode || e.which;
			if(keyCode === 13) {
				e.preventDefault();
				return false;
			}
		});

		//FANCYBOX//
		$(".fancybox").fancybox({
			"transitionIn": "elastic",
			"transitionOut": "elastic",
			"speedIn": 600,
			"speedOut": 200,
			"overlayShow": false,
			"cyclic" : true,
			"padding": 20,
			"titlePosition": "over",
			"onComplete": function() {
				$("#fancybox-title").css({"top":"100%", "bottom":"auto"});
			} 
		});
	});
</script>

<div id="<?=$strMainID?>_info" class="item_info">	
	<div class="item_image" id="<?=$arItemIDs['PICT']?>">
		<?//OFFERS_IMAGE//
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
			foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
				<div id="img_<?=$arItemIDs['ID']?>_<?=$arOffer['ID']?>" class="img hidden">
					<?if(is_array($arOffer["PREVIEW_PICTURE"])):?>
						<img src="<?=$arOffer['PREVIEW_PICTURE']['SRC']?>" width="<?=$arOffer['PREVIEW_PICTURE']["WIDTH"]?>" height="<?=$arOffer['PREVIEW_PICTURE']["HEIGHT"]?>" alt="<?=(isset($arOffer['NAME']) && !empty($arOffer['NAME']) ? $arOffer['NAME'] : $arElement['NAME']);?>" title="<?=(isset($arOffer['NAME']) && !empty($arOffer['NAME']) ? $arOffer['NAME'] : $arElement['NAME']);?>" />
					<?else:?>
						<img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
					<?endif;?>
				</div>
			<?endforeach;
		//ITEM_IMAGE//
		else:?>
			<div class="img">
				<?if(is_array($arElement["PREVIEW_PICTURE"])):?>
					<img src="<?=$arElement["PREVIEW_PICTURE"]["SRC"]?>" width="<?=$arElement["PREVIEW_PICTURE"]["WIDTH"]?>" height="<?=$arElement["PREVIEW_PICTURE"]["HEIGHT"]?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
				<?else:?>
					<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
				<?endif;?>
			</div>
		<?endif;
		//ITEM_NAME//?>
		<div class="item_name">
			<?=$arElement["NAME"]?>
		</div>
	</div>
	<div class="item_block<?=($arSetting['REFERENCE_PRICE']['VALUE'] == 'Y' && !empty($arSetting['REFERENCE_PRICE_COEF']['VALUE']) ? ' reference' : '').(isset($arElement['OFFERS']) && !empty($arElement['OFFERS']) && $arSetting['OFFERS_VIEW']['VALUE'] == 'LIST' ? ' offers-list' : '');?>">
		<?//OFFERS_PROPS//
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] != "LIST"):
			$arSkuProps = array();?>
			<table class="offer_block" id="<?=$arItemIDs['PROP_DIV'];?>">			
				<?foreach($arResult["SKU_PROPS"] as $arProp) {
					if(!isset($arElement["OFFERS_PROP"][$arProp["CODE"]]))
						continue;
					$arSkuProps[] = array(
						"ID" => $arProp["ID"],
						"SHOW_MODE" => $arProp["SHOW_MODE"]
					);?>	
					<tr class="<?=$arProp['CODE']?>" id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_cont">					
						<td class="h3"><?=htmlspecialcharsex($arProp["NAME"]);?></td>
						<td class="props">
							<ul id="<?=$arItemIDs['PROP'].$arProp['ID'];?>_list" class="<?=$arProp['CODE']?><?=$arProp['SHOW_MODE'] == 'PICT' ? ' COLOR' : '';?>">
								<?foreach($arProp["VALUES"] as $arOneValue) {									
									if(!isset($arElement["SKU_TREE_VALUES"][$arProp["ID"]][$arOneValue["ID"]]))
										continue;
									$arOneValue["NAME"] = htmlspecialcharsbx($arOneValue["NAME"]);?>
									<li data-treevalue="<?=$arProp['ID'].'_'.$arOneValue['ID'];?>" data-onevalue="<?=$arOneValue['ID'];?>" style="display:none;">
										<span title="<?=$arOneValue['NAME'];?>">
											<?if("TEXT" == $arProp["SHOW_MODE"]) {
												echo $arOneValue["NAME"];
											} elseif("PICT" == $arProp["SHOW_MODE"]) {
												if(is_array($arOneValue["PICT"])):?>
													<img src="<?=$arOneValue['PICT']['SRC']?>" width="<?=$arOneValue['PICT']['WIDTH']?>" height="<?=$arOneValue['PICT']['HEIGHT']?>" alt="<?=$arOneValue['NAME']?>" title="<?=$arOneValue['NAME']?>" />
												<?else:?>
													<i style="background:#<?=$arOneValue['HEX']?>"></i>
												<?endif;
											}?>
										</span>
									</li>
								<?}?>
							</ul>
							<div class="bx_slide_left" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_left" data-treevalue="<?=$arProp['ID']?>"></div>
							<div class="bx_slide_right" style="display:none;" id="<?=$arItemIDs['PROP'].$arProp['ID']?>_right" data-treevalue="<?=$arProp['ID']?>"></div>
						</td>
					</tr>
				<?}
				unset($arProp);?>
			</table>
		<?endif;
		//SELECT_PROPS//
		if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])):
			$arSelProps = array();?>
			<table class="offer_block" id="<?=$arItemIDs['SELECT_PROP_DIV'];?>">
				<?foreach($arElement["SELECT_PROPS"] as $key => $arProp):
					$arSelProps[] = array(
						"ID" => $arProp["ID"]
					);?>
					<tr class="<?=$arProp['CODE']?>" id="<?=$arItemIDs['SELECT_PROP'].$arProp['ID'];?>">
						<td class="h3"><?=htmlspecialcharsex($arProp["NAME"]);?></td>
						<td class="props">		
							<ul class="<?=$arProp['CODE']?>">
								<?$props = array();
								foreach($arProp["DISPLAY_VALUE"] as $arOneValue) {
									$props[$key] = array(
										"NAME" => $arProp["NAME"],
										"CODE" => $arProp["CODE"],
										"VALUE" => strip_tags($arOneValue)
									);
									$props[$key] = !empty($props[$key]) ? strtr(base64_encode(addslashes(gzcompress(serialize($props[$key]),9))), '+/=', '-_,') : "";?>
									<li data-select-onevalue="<?=$props[$key]?>">
										<span title="<?=$arOneValue;?>"><?=$arOneValue?></span>
									</li>
								<?}?>
							</ul>
							<div class="clr"></div>
						</td>
					</tr>
				<?endforeach;
				unset($arProp);?>
			</table>
		<?endif;
		//OFFERS_LIST//		
		if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"]) && $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):?>
			<div class="catalog-detail-offers-list">
				<div class="h3"><?=$arMessage["CATALOG_ELEMENT_OFFERS_LIST"]?></div>
				<div class="offers-items">
					<div class="thead">
						<div class="offers-items-image"><?=$arMessage["CATALOG_ELEMENT_OFFERS_LIST_IMAGE"]?></div>
						<div class="offers-items-name"><?=$arMessage["CATALOG_ELEMENT_OFFERS_LIST_NAME"]?></div>
						<?$i = 1;										
						foreach($arResult["SKU_PROPS"] as $arProp):											
							if(!isset($arElement["OFFERS_PROP"][$arProp["CODE"]]))
								continue;
							if($i > 3)
								continue;?>						
							<div class="offers-items-prop"><?=htmlspecialcharsex($arProp["NAME"]);?></div>
							<?$i++;											
						endforeach;
						unset($arProp);?>
						<div class="offers-items-price"></div>
						<div class="offers-items-buy"><?=$arMessage["CATALOG_ELEMENT_OFFERS_LIST_PRICE"]?></div>
					</div>
					<div class="tbody">
						<?foreach($arElement["OFFERS"] as $keyOffer => $arOffer):							
							$sticker = "";
							if($arOffer["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"] > 0) {
								$sticker .= "<span class='discount'>-".$arOffer["MIN_PRICE"]["DISCOUNT_DIFF_PERCENT"]."%</span>";	
							}?>
							<div class="catalog-item" id="catalog-offer-item-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" data-offer-num="<?=$keyOffer?>">
								<div class="catalog-item-info">							
									<?//OFFERS_LIST_IMAGE//?>
									<div class="catalog-item-image-cont">
										<div class="catalog-item-image">
											<a rel="lightbox" class="fancybox" href="<?=(is_array($arOffer['PREVIEW_PICTURE']) ? $arOffer['DETAIL_PICTURE']['SRC'] : $arElement['DETAIL_PICTURE']['SRC']);?>">
												<?if(is_array($arOffer["PREVIEW_PICTURE"])):?>
													<img src="<?=$arOffer['PREVIEW_PICTURE']['SRC']?>" width="<?=$arOffer['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arOffer['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=(isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME'];?>" title="<?=(isset($arOffer['NAME']) && !empty($arOffer['NAME'])) ? $arOffer['NAME'] : $arElement['NAME'];?>" />
												<?else:?>
													<img src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$strAlt?>" title="<?=$strTitle?>" />
												<?endif;?>
												<div class="sticker">
													<?=$sticker?>
												</div>
												<div class="zoom"><i class="fa fa-search-plus"></i></div>
											</a>
										</div>
									</div>
									<?//OFFERS_LIST_NAME_ARTNUMBER//?>
									<div class="catalog-item-title">
										<?//OFFERS_LIST_NAME//?>
										<span class="name"><?=(isset($arOffer["NAME"]) && !empty($arOffer["NAME"])) ? $arOffer["NAME"] : $arElement["NAME"];?></span>
										<?//OFFERS_LIST_ARTNUMBER//?>
										<span class="article"><?=$arMessage["CATALOG_ELEMENT_ARTNUMBER"]?><?=!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-";?></span>
									</div>
									<?//OFFERS_LIST_PROPS//
									$i = 1;
									foreach($arResult["SKU_PROPS"] as $arProp):									
										if(!isset($arElement["OFFERS_PROP"][$arProp["CODE"]]))
											continue;
										if($i > 3)
											continue;?>	
										<div class="catalog-item-prop<?=(!isset($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) || empty($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) ? ' empty' : '');?>">
											<?if(isset($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]]) && !empty($arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]])):
												$v = $arOffer["DISPLAY_PROPERTIES"][$arProp["CODE"]];
												if($arProp["SHOW_MODE"] == "TEXT"):
													echo strip_tags($v["DISPLAY_VALUE"]);
												elseif($arProp["SHOW_MODE"] == "PICT"):?>
													<span class="prop_cont">
														<span class="prop" title="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>">
															<?if(is_array($arProp["VALUES"][$v["VALUE"]]["PICT"])):?>
																<img src="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['SRC']?>" width="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['WIDTH']?>" height="<?=$arProp['VALUES'][$v['VALUE']]['PICT']['HEIGHT']?>" alt="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>" title="<?=$arProp['VALUES'][$v['VALUE']]['NAME']?>" />
															<?else:?>
																<i style="background:#<?=$arProp['VALUES'][$v['VALUE']]['HEX']?>"></i>
															<?endif;?>
														</span>
													</span>
												<?endif;
											endif;?>
										</div>
										<?$i++;
									endforeach;
									unset($arProp);
									//OFFERS_LIST_PRICE//?>
									<div class="item-price">
										<?foreach($arOffer["PRICES"] as $code => $arPrice):
											if($arPrice["MIN_PRICE"] == "Y"):
												if($arPrice["CAN_ACCESS"]):
													
													$price = CCurrencyLang::GetCurrencyFormat($arPrice["CURRENCY"], "ru");
													if(empty($price["THOUSANDS_SEP"])):
														$price["THOUSANDS_SEP"] = " ";
													endif;
													$price["REFERENCE_DECIMALS"] = $price["DECIMALS"];
													if($price["HIDE_ZERO"] == "Y"):
														if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):
															if(round($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], 0)):
																$price["REFERENCE_DECIMALS"] = 0;
															endif;
														endif;
														if(round($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"]) == round($arPrice["DISCOUNT_VALUE"], 0)):
															$price["DECIMALS"] = 0;
														endif;
													endif;
													$currency = str_replace("# ", " ", $price["FORMAT_STRING"]);

													if($arPrice["DISCOUNT_VALUE"] <= 0):?>							
														<span class="catalog-item-no-price">
															<span class="unit">
																<?=$arMessage["CATALOG_ELEMENT_NO_PRICE"]?>
																<br />
																<span><?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? $arMessage["CATALOG_ELEMENT_UNIT"]." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?></span>
															</span>
														</span>
													<?else:?>
														<span class="catalog-item-price">
															<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
															<span class="unit">
																<?=$currency?>
																<span><?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? $arMessage["CATALOG_ELEMENT_UNIT"]." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?></span>
															</span>
															<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
																<span class="catalog-item-price-reference">
																	<?=number_format($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $price["REFERENCE_DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
																	<span><?=$currency?></span>
																</span>
															<?endif;?>
														</span>
														<?if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
															<span class="catalog-item-price-old">
																<?=$arPrice["PRINT_VALUE"];?>
															</span>
															<span class="catalog-item-price-percent">
																<?=$arMessage['CATALOG_ELEMENT_SKIDKA']?>
																<br />
																<?=$arPrice["PRINT_DISCOUNT_DIFF"]?>
															</span>
														<?endif;											
													endif;
												endif;
											endif;
										endforeach;?>
									</div>
									<?//OFFERS_LIST_MOBILE_PROPS//
									if(!empty($arOffer["DISPLAY_PROPERTIES"])):?>
										<div id="catalog-item-props-mob-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" class="catalog-item-props-mob"></div>
									<?endif;
									//OFFERS_LIST_AVAILABILITY_BUY//?>
									<div class="buy_more">
										<?//OFFERS_LIST_AVAILABILITY//?>
										<div class="available">
											<?if($arOffer["CAN_BUY"]):?>
												<div class="avl">
													<i class="fa fa-check-circle"></i>
													<span>
														<?=$arMessage["CATALOG_ELEMENT_AVAILABLE"];
														if($arOffer["CATALOG_QUANTITY_TRACE"] == "Y"):
															if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
																echo " ".$arOffer["CATALOG_QUANTITY"];
															endif;
														endif;?>
													</span>
												</div>
											<?elseif(!$arOffer["CAN_BUY"]):?>
												<div class="not_avl">
													<i class="fa fa-times-circle"></i>
													<span><?=$arMessage["CATALOG_ELEMENT_NOT_AVAILABLE"]?></span>
												</div>
											<?endif;?>
										</div>
										<div class="clr"></div>											
										<?//OFFERS_LIST_BUY//
										if($arOffer["CAN_BUY"]):
											if($arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
												//OFFERS_LIST_ASK_PRICE//?>
												<a class="btn_buy apuo" id="ask_price_anch_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span class="short"><?=$arMessage["CATALOG_ELEMENT_ASK_PRICE_SHORT"]?></span></a>
											<?else:?>
												<div class="add2basket_block">
													<?//OFFERS_LIST_DELAY//
													foreach($arOffer["PRICES"] as $code => $arPrice):
														if($arPrice["MIN_PRICE"] == "Y"):
															$props = array();
															if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):	
																$props[] = array(
																	"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
																	"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
																	"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
																);
															endif;
															foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
																$props[] = array(
																	"NAME" => $propOffer["NAME"],
																	"CODE" => $propOffer["CODE"],
																	"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
																);
															}
															$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
															<div class="delay">
																<a href="javascript:void(0)" id="catalog-item-delay-<?=$arItemIDs['ID'].'-'.$arOffer['ID']?>" class="catalog-item-delay" onclick="return addToDelay('<?=$arOffer["ID"]?>', '<?=$arOffer["CATALOG_MEASURE_RATIO"]?>', '<?=$props?>', '', 'catalog-item-delay-<?=$arItemIDs['ID']."-".$arOffer["ID"]?>', '<?=SITE_DIR?>')" rel="nofollow"><i class="fa fa-heart-o"></i><i class="fa fa-check"></i></a>
															</div>
														<?endif;
													endforeach;
													//OFFERS_LIST_BUY_FORM//?>
													<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
														<div class="qnt_cont">
															<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value > <?=$arOffer["CATALOG_MEASURE_RATIO"]?>) BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value)-<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
															<input type="text" id="quantity_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['CATALOG_MEASURE_RATIO']?>"/>
															<a href="javascript:void(0)" class="plus" onclick="BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value)+<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
														</div>
														<input type="hidden" name="ID" class="offer_id" value="<?=$arOffer['ID']?>" />
														<?$props = array();
														if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):	
															$props[] = array(
																"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
																"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
																"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
															);
														endif;
														foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
															$props[] = array(
																"NAME" => $propOffer["NAME"],
																"CODE" => $propOffer["CODE"],
																"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
															);
														}
														$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
														<input type="hidden" name="PROPS" value="<?=$props?>" />
														<?if(!empty($arElement["SELECT_PROPS"])):?>
															<input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="" />
														<?endif;?>
														<button type="button" id="<?=$arItemIDs['BTN_BUY']?>" class="btn_buy" name="add2basket" value="<?=$arMessage['CATALOG_ELEMENT_ADD_TO_CART']?>"><i class="fa fa-shopping-cart"></i></button>
													</form>
													<?//OFFERS_LIST_BUY_ONE_CLICK//?>
													<button name="boc_anch" id="boc_anch_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" class="btn_buy boc_anch" value="<?=$arMessage['CATALOG_ELEMENT_BOC']?>"><i class="fa fa-bolt"></i><?=$arMessage['CATALOG_ELEMENT_BOC_SHORT']?></button>
												</div>
											<?endif;
										elseif(!$arOffer["CAN_BUY"]):
											//OFFERS_LIST_UNDER_ORDER//?>
											<a class="btn_buy apuo" id="under_order_anch_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span class="short"><?=$arMessage["CATALOG_ELEMENT_UNDER_ORDER"]?></span></a>
										<?endif;?>										
									</div>										
								</div>
							</div>							
						<?endforeach;?>
					</div>
				</div>
			</div>
		<?//OFFERS_ITEM//
		else:
			//OFFERS_ITEM_PRICE//?>
			<div class="catalog_price" id="<?=$arItemIDs['PRICE'];?>">
				<?//OFFERS_PRICE//
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
					foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
						<div id="price_<?=$arItemIDs['ID']?>_<?=$arOffer['ID']?>" class="price hidden">
							<?foreach($arOffer["PRICES"] as $code => $arPrice):
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
											<span class="no-price">
												<?=$arMessage["CATALOG_ELEMENT_NO_PRICE"]?>
												<?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? $arMessage["CATALOG_ELEMENT_UNIT"]." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?>
											</span>
										<?else:
											if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>				
												<span class="price-old">
													<?=$arPrice["PRINT_VALUE"];?>
												</span>
												<span class="price-percent">
													<?=$arMessage["CATALOG_ELEMENT_SKIDKA"]." ".$arPrice["PRINT_DISCOUNT_DIFF"];?>
												</span>
											<?endif;?>
											<span class="price-normal">
												<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
												<span class="unit">
													<?=$currency?>
													<?=(!empty($arOffer["CATALOG_MEASURE_NAME"])) ? $arMessage["CATALOG_ELEMENT_UNIT"]." ".$arOffer["CATALOG_MEASURE_NAME"] : "";?>
												</span>
											</span>
											<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
												<span class="price-reference">
													<?=CCurrencyLang::CurrencyFormat($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arPrice["CURRENCY"], true);?>
												</span>
											<?endif;
										endif;												
									endif;
								endif;
							endforeach;
							//OFFERS_AVAILABILITY//?>
							<div class="available">
								<?if($arOffer["CAN_BUY"]):?>													
									<div class="avl">
										<i class="fa fa-check-circle"></i>
										<span>
											<?=$arMessage["CATALOG_ELEMENT_AVAILABLE"];
											if($arOffer["CATALOG_QUANTITY_TRACE"] == "Y"):
												if(in_array("PRODUCT_QUANTITY", $arSetting["GENERAL_SETTINGS"]["VALUE"])):
													echo " ".$arOffer["CATALOG_QUANTITY"];
												endif;
											endif;?>
										</span>
									</div>
								<?elseif(!$arOffer["CAN_BUY"]):?>												
									<div class="not_avl">
										<i class="fa fa-times-circle"></i>
										<span><?=$arMessage["CATALOG_ELEMENT_NOT_AVAILABLE"]?></span>
									</div>
								<?endif;?>
							</div>
						</div>
					<?endforeach;
				//ITEM_PRICE//
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

								if($arPrice["DISCOUNT_VALUE"] < $arPrice["VALUE"]):?>
									<span class="price-old">
										<?=$arPrice["PRINT_VALUE"];?>
									</span>
									<span class="price-percent">
										<?=$arMessage["CATALOG_ELEMENT_SKIDKA"]." ".$arPrice["PRINT_DISCOUNT_DIFF"];?>
									</span>
								<?endif;?>
								<span class="price-normal">
									<?=number_format($arPrice["DISCOUNT_VALUE"], $price["DECIMALS"], $price["DEC_POINT"], $price["THOUSANDS_SEP"]);?>
									<span class="unit">
										<?=$currency?>
										<?=(!empty($arElement["CATALOG_MEASURE_NAME"])) ? $arMessage["CATALOG_ELEMENT_UNIT"]." ".$arElement["CATALOG_MEASURE_NAME"] : "";?>
									</span>
								</span>
								<?if($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"])):?>
									<span class="price-reference">
										<?=CCurrencyLang::CurrencyFormat($arPrice["DISCOUNT_VALUE"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arPrice["CURRENCY"], true);?>
									</span>
								<?endif;
							endif;
						endif;
					endforeach;
					//ITEM_AVAILABILITY//?>
					<div class="available">
						<?if($arElement["CAN_BUY"]):?>												
							<div class="avl">
								<i class="fa fa-check-circle"></i>
								<span>
									<?=$arMessage["CATALOG_ELEMENT_AVAILABLE"];
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
								<span><?=$arMessage["CATALOG_ELEMENT_NOT_AVAILABLE"]?></span>
							</div>
						<?endif;?>
					</div>
				<?endif;?>
			</div>
			<?//OFFERS_ITEM_BUY//?>
			<div class="catalog_buy_more" id="<?=$arItemIDs['BUY'];?>">
				<?//OFFERS_BUY//
				if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
					foreach($arElement["OFFERS"] as $key_off => $arOffer):?>
						<div id="buy_more_<?=$arItemIDs['ID']?>_<?=$arOffer['ID']?>" class="buy_more hidden">
							<?if($arOffer["CAN_BUY"]):											
								if($arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
									//OFFERS_ASK_PRICE//?>
									<a class="btn_buy apuo" id="ask_price_anch_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-comment-o"></i><span><?=$arMessage["CATALOG_ELEMENT_ASK_PRICE_FULL"]?></span></a>
								<?else:?>											
									<div class="add2basket_block">
										<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
											<div class="qnt_cont">
												<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value > <?=$arOffer["CATALOG_MEASURE_RATIO"]?>) BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value)-<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
												<input type="text" id="quantity_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" name="quantity" class="quantity" value="<?=$arOffer['CATALOG_MEASURE_RATIO']?>"/>
												<a href="javascript:void(0)" class="plus" onclick="BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value = parseFloat(BX('quantity_<?=$arItemIDs["ID"]."_".$arOffer["ID"]?>').value)+<?=$arOffer["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
											</div>
											<input type="hidden" name="ID" class="offer_id" value="<?=$arOffer["ID"]?>" />
											<?$props = array();
											if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):		
												$props[] = array(
													"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
													"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
													"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
												);
											endif;
											foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
												$props[] = array(
													"NAME" => $propOffer["NAME"],
													"CODE" => $propOffer["CODE"],
													"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
												);
											}
											$props = !empty($props) ? strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,') : "";?>
											<input type="hidden" name="PROPS" value="<?=$props?>" />
											<?if(!empty($arElement["SELECT_PROPS"])):?>
												<input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" value="" />
											<?endif;?>
											<button type="button" class="btn_buy" name="add2basket" value="<?=$arMessage['CATALOG_ELEMENT_ADD_TO_CART']?>"><i class="fa fa-shopping-cart"></i><span><?=$arMessage["CATALOG_ELEMENT_ADD_TO_CART"]?></span></button>
										</form>
									</div>
								<?endif;
							elseif(!$arOffer["CAN_BUY"]):
								//OFFERS_UNDER_ORDER//?>
								<a class="btn_buy apuo" id="under_order_anch_<?=$arItemIDs['ID'].'_'.$arOffer['ID']?>" href="javascript:void(0)" rel="nofollow"><i class="fa fa-clock-o"></i><span><?=$arMessage["CATALOG_ELEMENT_UNDER_ORDER"]?></span></a>
							<?endif;?>
						</div>
					<?endforeach;
				//ITEM_BUY//
				else:?>
					<div class="buy_more">
						<?if($arElement["CAN_BUY"]):?>
							<div class="add2basket_block">
								<form action="<?=SITE_DIR?>ajax/add2basket.php" class="add2basket_form">
									<div class="qnt_cont">
										<a href="javascript:void(0)" class="minus" onclick="if (BX('quantity_select_<?=$arItemIDs["ID"]?>').value > <?=$arElement["CATALOG_MEASURE_RATIO"]?>) BX('quantity_select_<?=$arItemIDs["ID"]?>').value = parseFloat(BX('quantity_select_<?=$arItemIDs["ID"]?>').value)-<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>-</span></a>
										<input type="text" id="quantity_select_<?=$arItemIDs['ID']?>" name="quantity" class="quantity" value="<?=$arElement['CATALOG_MEASURE_RATIO']?>"/>
										<a href="javascript:void(0)" class="plus" onclick="BX('quantity_select_<?=$arItemIDs["ID"]?>').value = parseFloat(BX('quantity_select_<?=$arItemIDs["ID"]?>').value)+<?=$arElement["CATALOG_MEASURE_RATIO"]?>;"><span>+</span></a>
									</div>
									<input type="hidden" name="ID" class="id" value="<?=$arElement['ID']?>" />
									<?if(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"])):
										$props = array();
										$props[] = array(
											"NAME" => $arElement["PROPERTIES"]["ARTNUMBER"]["NAME"],
											"CODE" => $arElement["PROPERTIES"]["ARTNUMBER"]["CODE"],
											"VALUE" => $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]
										);												
										$props = strtr(base64_encode(addslashes(gzcompress(serialize($props),9))), '+/=', '-_,');?>
										<input type="hidden" name="PROPS" value="<?=$props?>" />
									<?endif;?>
									<input type="hidden" name="SELECT_PROPS" id="select_props_<?=$arItemIDs['ID']?>" value="" />
									<button type="button" id="<?=$arItemIDs['BTN_BUY']?>" class="btn_buy" name="add2basket" value="<?=$arMessage['CATALOG_ELEMENT_ADD_TO_CART']?>"><i class="fa fa-shopping-cart"></i><span><?=$arMessage["CATALOG_ELEMENT_ADD_TO_CART"]?></span></button>
								</form>
							</div>
						<?endif;?>
					</div>
				<?endif;?>
			</div>
		<?endif;?>		
	</div>
</div>

<?if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
	foreach($arElement["OFFERS"] as $key => $arOffer):		
		$offerName = isset($arOffer["NAME"]) && !empty($arOffer["NAME"]) ? $arOffer["NAME"] : $arElement["NAME"];
		$properties = array();
		foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
			$properties[] = $propOffer["NAME"].": ".strip_tags($propOffer["DISPLAY_VALUE"]);
		}
		$properties = implode("; ", $properties);
		$elementName = !empty($properties) ? $offerName." (".$properties.")" : $offerName;
		
		if($arOffer["CAN_BUY"] && $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] <= 0):
			//ASK_PRICE//
			global $arAskPriceFilter;
			$arAskPriceFilter = array(
				"ELEMENT_ID" => $arOffer["ID"],
				"ELEMENT_AREA_ID" => $arItemIDs["ID"]."_".$arOffer["ID"],
				"ELEMENT_NAME" => $elementName,
				"BUTTON_ID" => "ask_price_anch_".$arItemIDs["ID"]."_".$arOffer["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_ask_price.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?elseif($arOffer["CAN_BUY"] && $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] > 0 && $arSetting["OFFERS_VIEW"]["VALUE"] == "LIST"):
			//BUY_ONE_CLICK//
			$properties = array();
			if(!empty($arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"])):		
				$properties[] = array(
					"NAME" => $arOffer["PROPERTIES"]["ARTNUMBER"]["NAME"],
					"CODE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["CODE"],
					"VALUE" => $arOffer["PROPERTIES"]["ARTNUMBER"]["VALUE"]
				);																
			endif;
			foreach($arOffer["DISPLAY_PROPERTIES"] as $propOffer) {
				$properties[] = array(
					"NAME" => $propOffer["NAME"],
					"CODE" => $propOffer["CODE"],
					"VALUE" => strip_tags($propOffer["DISPLAY_VALUE"])
				);
			}
			$properties = !empty($properties) ? strtr(base64_encode(addslashes(gzcompress(serialize($properties),9))), '+/=', '-_,') : "";
			global $arBuyOneClickFilter;
			$arBuyOneClickFilter = array(
				"ELEMENT_ID" => $arOffer["ID"],
				"ELEMENT_AREA_ID" => $arItemIDs["ID"]."_".$arOffer["ID"],
				"ELEMENT_PROPS" => $properties,
				"SELECT_PROP_DIV" => $arItemIDs["SELECT_PROP_DIV"],
				"BUY_MODE" => "ONE",
				"BUTTON_ID" => "boc_anch_".$arItemIDs["ID"]."_".$arOffer["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_buy_one_click.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?elseif(!$arOffer["CAN_BUY"]):
			//UNDER_ORDER//
			global $arUnderOrderFilter;
			$arUnderOrderFilter = array(
				"ELEMENT_ID" => $arOffer["ID"],
				"ELEMENT_AREA_ID" => $arItemIDs["ID"]."_".$arOffer["ID"],
				"ELEMENT_NAME" => $elementName,
				"BUTTON_ID" => "under_order_anch_".$arItemIDs["ID"]."_".$arOffer["ID"],
				"HIDE_ICONS" => "Y"
			);?>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/form_under_order.php"), false, array("HIDE_ICONS" => "Y"));?>
		<?endif;
	endforeach;
endif;

if(isset($arElement["OFFERS"]) && !empty($arElement["OFFERS"])):
	$arJSParams = array(
		"PRODUCT_TYPE" => $arElement["CATALOG_TYPE"],
		"VISUAL" => array(
			"ID" => $arItemIDs["ID"],
			"PICT_ID" => $arItemIDs["PICT"],
			"PRICE_ID" => $arItemIDs["PRICE"],
			"BUY_ID" => $arItemIDs["BUY"],
			"TREE_ID" => $arItemIDs["PROP_DIV"],
			"TREE_ITEM_ID" => $arItemIDs["PROP"],			
		),
		"PRODUCT" => array(
			"ID" => $arElement["ID"],
			"NAME" => $arElement["NAME"],
			"PICT" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
		),		
		"OFFERS_VIEW" => $arSetting["OFFERS_VIEW"]["VALUE"],
		"OFFERS" => $arElement["JS_OFFERS"],
		"OFFER_SELECTED" => $arElement["OFFERS_SELECTED"],
		"TREE_PROPS" => $arSkuProps
	);
else:
	$arJSParams = array(
		"PRODUCT_TYPE" => $arElement["CATALOG_TYPE"],
		"VISUAL" => array(
			"ID" => $arItemIDs["ID"],
			"BTN_BUY_ID" => $arItemIDs["BTN_BUY"],
		),
		"PRODUCT" => array(
			"ID" => $arElement["ID"],
			"NAME" => $arElement["NAME"],
			"PICT" => is_array($arElement["PREVIEW_PICTURE"]) ? $arElement["PREVIEW_PICTURE"] : array("SRC" => SITE_TEMPLATE_PATH."/images/no-photo.jpg", "WIDTH" => 150, "HEIGHT" => 150),
		)
	);
endif;				

if(isset($arElement["SELECT_PROPS"]) && !empty($arElement["SELECT_PROPS"])):
	$arJSParams["VISUAL"]["SELECT_PROP_ID"] = $arItemIDs["SELECT_PROP_DIV"];
	$arJSParams["VISUAL"]["SELECT_PROP_ITEM_ID"] = $arItemIDs["SELECT_PROP"];
	$arJSParams["SELECT_PROPS"] = $arSelProps;
endif;?>				

<script type="text/javascript">
	var <?=$strObName;?> = new JCCatalogSection(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
</script>