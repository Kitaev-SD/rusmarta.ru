<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

global $arSetting;

if($arSetting["SMART_FILTER_LOCATION"]["VALUE"] == "VERTICAL"):
	$this->SetViewTarget("filter_vertical");		
endif;

CJSCore::Init(array("fx"));?>

<script type="text/javascript">
	//<![CDATA[
	$(function() {
		$(".showfilter").click(function() {
			var clickitem = $(this);
			clickitem.parent().toggleClass("active");
			$(".filter").slideToggle();
		});
		$(".catalog_item_toogle_filter_hidden").click(function() {
			$(".catalog_item_toogle_filter").removeClass("active");			
			$(".filter").slideToggle();
		});
		<?if($arSetting["SMART_FILTER_LOCATION"]["VALUE"] == "VERTICAL"):?>
			$(window).resize(function() {
				var filter = $(".filter"),
					currentWidth = $(".center").width();

				if(currentWidth > "768") {
					if(!filter.hasClass("vertical")) {
						filter.find(".catalog_item_toogle_filter_hidden").hide();
						filter.find("button[id=set_filter]").before(filter.find(".catalog_item_toogle_filter_reset, #modef"));		
						<?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT"):?>
							$(".left-column").find(".left-menu").after($(".catalog_item_toogle_filter").addClass("vertical"), filter.addClass("vertical"));
						<?else:?>
							$(".left-column").prepend($(".catalog_item_toogle_filter").addClass("vertical"), filter.addClass("vertical"));
						<?endif;?>
						$(".filter_indent").addClass("vertical");
					}
				} else {
					if(filter.hasClass("vertical")) {
						filter.find(".catalog_item_toogle_filter_hidden").show();
						filter.find("button[id=set_filter]").after(filter.find(".catalog_item_toogle_filter_reset, #modef"));		
						$(".filter_indent").before($(".catalog_item_toogle_filter").removeClass("vertical"), filter.removeClass("vertical"));						
						$(".filter_indent").removeClass("vertical");
					}
				}
			});
			$(window).resize();
		<?endif;?>
	});
	
	//SHOW_FILTER_HINT//
	function showFilterHint(target, hint) {		
		BX.FilterHint = {
			popup: null
		};
		BX.FilterHint.popup = BX.PopupWindowManager.create("filterHint", null, {
			autoHide: true,
			offsetLeft: 0,
			offsetTop: 0,
			overlay: {
				opacity: 100
			},
			draggable: false,
			closeByEsc: false,
			closeIcon: { right : "-10px", top : "-10px"},			
			titleBar: false
		});
		BX.FilterHint.popup.setContent(hint);
		
		BX.addClass(BX("filterHint"), "pop-up filter-hint");
		close = BX.findChildren(BX("filterHint"), {className: "popup-window-close-icon"}, true);
		if(!!close && 0 < close.length) {
			for(i = 0; i < close.length; i++) {					
				close[i].innerHTML = "<i class='fa fa-times'></i>";
			}
		}
		
		BX.remove(BX("popup-window-overlay-filterHint"));
		target.parentNode.appendChild(BX("filterHint"));
		
		BX.FilterHint.popup.show();
	};
	//]]>
</script>

<div class="catalog_item_toogle_filter<?=($arSetting['SMART_FILTER_LOCATION']['VALUE'] == 'VERTICAL') ? ' vertical' : '';?><?=($arSetting['SMART_FILTER_VISIBILITY']['VALUE'] == 'EXPAND') ? ' active' : '';?>">
	<a class="showfilter" href="javascript:void(0)"><span><?=GetMessage("FILTER_DISPLAY_HIDDEN")?></span><i class="fa fa-minus"></i><i class="fa fa-plus"></i></a>
</div>
<div class="filter<?=($arSetting['SMART_FILTER_LOCATION']['VALUE'] == 'VERTICAL') ? ' vertical' : '';?>"<?=($arSetting["SMART_FILTER_VISIBILITY"]["VALUE"] == "EXPAND") ? " style='display:block;'" : "";?>>
	<form name="<?=$arResult["FILTER_NAME"]."_form"?>" action="<?=$arResult["FORM_ACTION"]?>" method="get">
		<?foreach($arResult["HIDDEN"] as $arItem):?>
			<input type="hidden" name="<?=$arItem["CONTROL_NAME"]?>" id="<?=$arItem["CONTROL_ID"]?>" value="<?=$arItem["HTML_VALUE"]?>" />
		<?endforeach;?>
		<table>
			<?/***PRICES***/
			foreach($arResult["ITEMS"] as $key => $arItem):
				$key = $arItem["ENCODED_ID"];
				if(isset($arItem["PRICE"])):					
					if($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0)
						continue;?>
					
					<tr class="bx_filter_box active">						
						<td class="bx_filter_box_name">
							<div class="sect__name">
								<div class="sect__text" onclick="smartFilter.hideFilterProps(this)">
									<span><?=$arItem["NAME"]?></span>
								</div>
								<div class="sect__arrow">
									<i class="fa fa-angle-left"></i>
									<i class="fa fa-angle-up"></i>
								</div>
							</div>
							<div class="bx_filter_container_modef_popup"></div>
						</td>
						<td class="bx_filter_slider">							
							<div class="bx_filter_block">
								<?$presicion = 2;
								if(Bitrix\Main\Loader::includeModule("currency")) {
									$res = CCurrencyLang::GetFormatDescription($arItem["VALUES"]["MIN"]["CURRENCY"]);
									$currency = str_replace("# ", " ", $res["FORMAT_STRING"]);
									$presicion = $res['DECIMALS'];
								}?>
								<div class="price from">
									<span><?=GetMessage("PRICE_FROM")?></span>
									<input class="min-price" type="text" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" placeholder="<?=number_format($arItem["VALUES"]["MIN"]["VALUE"], $precision, ".", "")?>" onkeyup="smartFilter.keyup(this)" />
								</div>
								<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
									<div class="bx-ui-slider-pricebar-vd" style="left:0; right:0;" id="colorUnavailableActive_<?=$key?>"></div>
									<div class="bx-ui-slider-pricebar-vn" style="left:0; right:0;" id="colorAvailableInactive_<?=$key?>"></div>
									<div class="bx-ui-slider-pricebar-v" style="left:0; right:0;" id="colorAvailableActive_<?=$key?>"></div>
									<div class="bx_ui_slider_range" id="drag_tracker_<?=$key?>"  style="left:0%; right:0%;">
										<a class="bx_ui_slider_handle left" style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"><i class="fa fa-angle-left"></i></a>
										<a class="bx_ui_slider_handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"><i class="fa fa-angle-right"></i></a>
									</div>
								</div>
								<div class="price to">
									<span><?=GetMessage("PRICE_TO")?></span>
									<input class="max-price" type="text" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" placeholder="<?=number_format($arItem["VALUES"]["MAX"]["VALUE"], $precision, ".", "")?>" onkeyup="smartFilter.keyup(this)" />
									<span><?=$currency?></span>
								</div>
								<?$arJsParams = array(
									"leftSlider" => 'left_slider_'.$key,
									"rightSlider" => 'right_slider_'.$key,
									"tracker" => "drag_tracker_".$key,
									"trackerWrap" => "drag_track_".$key,
									"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
									"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
									"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
									"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
									"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
									"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
									"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
									"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
									"precision" => $presicion,
									"colorUnavailableActive" => 'colorUnavailableActive_'.$key,
									"colorAvailableActive" => 'colorAvailableActive_'.$key,
									"colorAvailableInactive" => 'colorAvailableInactive_'.$key,
								);?>
								<script type="text/javascript">
									BX.ready(function(){
										window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
									});
								</script>
							</div>
						</td>
					</tr>
				<?endif;
			endforeach;
						
			/***NEW_SALELEADER_DISCOUNT***/
			$nsd = array();
			foreach($arResult["ITEMS"] as $key => $arItem):
				if(empty($arItem["VALUES"]) || isset($arItem["PRICE"]))
					continue;

				if($arItem["CODE"] == "NEWPRODUCT")
					$nsd[] = $arItem["CODE"];
				if($arItem["CODE"] == "SALELEADER")
					$nsd[] = $arItem["CODE"];
				if($arItem["CODE"] == "DISCOUNT")
					$nsd[] = $arItem["CODE"];
			endforeach;
									
			if(!empty($nsd)):?>
				<tr class="bx_filter_box active">					
					<td class="bx_filter_box_name">
						<div class="sect__name">
							<div class="sect__text" onclick="smartFilter.hideFilterProps(this)">
								<span><?=GetMessage("PRODUCT_TYPE")?></span>
							</div>
							<div class="sect__arrow">
								<i class="fa fa-angle-left"></i>
								<i class="fa fa-angle-up"></i>
							</div>
						</div>
						<div class="bx_filter_container_modef_popup"></div>
					</td>
					<td>						
						<div class="bx_filter_block">
							<?foreach($arResult["ITEMS"] as $key => $arItem):
								if(in_array($arItem["CODE"], $nsd)):
									foreach($arItem['VALUES'] as $val => $arOption):?>
										<div class="custom-forms">
											<input type="checkbox" id="<?=$arOption['CONTROL_ID']?>" name="<?=$arOption['CONTROL_NAME']?>" <?=$arOption["CHECKED"] ? "checked=\"checked\"" : ""?> <?=$arOption["DISABLED"] ? "disabled=\"disabled\"" : ""?> value="<?=$arOption['HTML_VALUE']?>" onclick="smartFilter.click(this)" />
											<label data-role="label_<?=$arOption['CONTROL_ID']?>" <?=$arOption["DISABLED"] ? "class=\"disabled\"" : ""?> for="<?=$arOption['CONTROL_ID']?>"><?=$arItem["NAME"]?><?if($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($arOption["ELEMENT_COUNT"])):?><span class="count" data-role="count_<?=$arOption["CONTROL_ID"]?>"><?=$arOption["ELEMENT_COUNT"]?></span><?endif;?></label>
										</div>
									<?endforeach;
								endif;
							endforeach;?>
						</div>
					</td>
				</tr>
			<?endif;

			/***OTHER_PROPERTIES***/
			foreach($arResult["ITEMS"] as $key => $arItem):
				if(empty($arItem["VALUES"]) || isset($arItem["PRICE"]))
					continue;

				if(($arItem["DISPLAY_TYPE"] == "A" || $arItem["DISPLAY_TYPE"] == "B") && ($arItem["VALUES"]["MAX"]["VALUE"] - $arItem["VALUES"]["MIN"]["VALUE"] <= 0))
					continue;

				if(in_array($arItem["CODE"], $nsd))
					continue;?>

				<tr class="bx_filter_box<?=($arItem['DISPLAY_EXPANDED']== 'Y') ? ' active' : ''?>">					
					<td class="bx_filter_box_name">
						<div class="sect__name">
							<div class="sect__text" onclick="smartFilter.hideFilterProps(this)">
								<span><?=$arItem["NAME"]?></span>
							</div>							
							<div class="sect__arrow">
								<i class="fa <?=($arItem['DISPLAY_EXPANDED']== 'Y') ? 'fa-angle-left' : 'fa-angle-right'?>"></i>
								<i class="fa <?=($arItem['DISPLAY_EXPANDED']== 'Y') ? 'fa-angle-up' : 'fa-angle-down'?>"></i>
							</div>
							<?if(!empty($arItem["FILTER_HINT"])):?>
								<div class="sect__hint-wrap">
									<div class="sect__hint">
										<a class="sect__hint-link" href="javascript:void(0);" onclick="showFilterHint(this, '<?=$arItem['FILTER_HINT']?>');"><i class="fa fa-question-circle-o"></i></a>
									</div>
								</div>
							<?endif;?>
						</div>
						<div class="bx_filter_container_modef_popup"></div>
					</td>
					<td<?=$arItem["DISPLAY_TYPE"] == "A" ? " class='bx_filter_slider'" : "";?>>						
						<div class="bx_filter_block">
							<?$arCur = current($arItem["VALUES"]);
							switch($arItem["DISPLAY_TYPE"]) {
								case "A": //NUMBERS_WITH_SLIDER ?>
									<div class="price from">
										<span><?=GetMessage("PRICE_FROM")?></span>
										<input class="min-price" type="text" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" placeholder="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>" onkeyup="smartFilter.keyup(this)" />
									</div>
									<div class="bx_ui_slider_track" id="drag_track_<?=$key?>">
										<div class="bx-ui-slider-pricebar-vd" style="left:0; right:0;" id="colorUnavailableActive_<?=$key?>"></div>
										<div class="bx-ui-slider-pricebar-vn" style="left:0; right:0;" id="colorAvailableInactive_<?=$key?>"></div>
										<div class="bx-ui-slider-pricebar-v" style="left:0; right:0;" id="colorAvailableActive_<?=$key?>"></div>
										<div class="bx_ui_slider_range" id="drag_tracker_<?=$key?>"  style="left:0%; right:0%;">
											<a class="bx_ui_slider_handle left"  style="left:0;" href="javascript:void(0)" id="left_slider_<?=$key?>"><i class="fa fa-angle-left"></i></a>
											<a class="bx_ui_slider_handle right" style="right:0;" href="javascript:void(0)" id="right_slider_<?=$key?>"><i class="fa fa-angle-right"></i></a>
										</div>
									</div>
									<div class="price to">
										<span><?=GetMessage("PRICE_TO")?></span>
										<input class="max-price" type="text" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" placeholder="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>" onkeyup="smartFilter.keyup(this)" />
									</div>
									<?$arJsParams = array(
										"leftSlider" => 'left_slider_'.$key,
										"rightSlider" => 'right_slider_'.$key,
										"tracker" => "drag_tracker_".$key,
										"trackerWrap" => "drag_track_".$key,
										"minInputId" => $arItem["VALUES"]["MIN"]["CONTROL_ID"],
										"maxInputId" => $arItem["VALUES"]["MAX"]["CONTROL_ID"],
										"minPrice" => $arItem["VALUES"]["MIN"]["VALUE"],
										"maxPrice" => $arItem["VALUES"]["MAX"]["VALUE"],
										"curMinPrice" => $arItem["VALUES"]["MIN"]["HTML_VALUE"],
										"curMaxPrice" => $arItem["VALUES"]["MAX"]["HTML_VALUE"],
										"fltMinPrice" => intval($arItem["VALUES"]["MIN"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MIN"]["FILTERED_VALUE"] : $arItem["VALUES"]["MIN"]["VALUE"] ,
										"fltMaxPrice" => intval($arItem["VALUES"]["MAX"]["FILTERED_VALUE"]) ? $arItem["VALUES"]["MAX"]["FILTERED_VALUE"] : $arItem["VALUES"]["MAX"]["VALUE"],
										"precision" => $arItem["DECIMALS"] ? $arItem["DECIMALS"] : 0,
										"colorUnavailableActive" => 'colorUnavailableActive_'.$key,
										"colorAvailableActive" => 'colorAvailableActive_'.$key,
										"colorAvailableInactive" => 'colorAvailableInactive_'.$key,
									);?>
									<script type="text/javascript">
										BX.ready(function(){
											window['trackBar<?=$key?>'] = new BX.Iblock.SmartFilter(<?=CUtil::PhpToJSObject($arJsParams)?>);
										});
									</script>
									<?break;
								case "B": //NUMBERS ?>
									<div class="price from" style="margin-right:15px;">
										<span><?=GetMessage("PRICE_FROM")?></span>
										<input class="min-price" type="text" name="<?=$arItem["VALUES"]["MIN"]["CONTROL_NAME"]?>" id="<?=$arItem["VALUES"]["MIN"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"]?>" placeholder="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>" onkeyup="smartFilter.keyup(this)" />
									</div>
									<div class="price to">
										<span><?=GetMessage("PRICE_TO")?></span>
										<input class="max-price" type="text" name="<?=$arItem["VALUES"]["MAX"]["CONTROL_NAME"]?>" id="<?=$arItem["VALUES"]["MAX"]["CONTROL_ID"]?>" value="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"]?>" placeholder="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>" onkeyup="smartFilter.keyup(this)" />
									</div>
									<?break;
								case "G": //CHECKBOXES_WITH_PICTURES
									break;
								case "H": //CHECKBOXES_WITH_PICTURES_AND_LABELS
									break;
								case "P": //DROPDOWN
									$checkedItemExist = false;?>
									<div class="bx_filter_select_container">
										<div class="bx_filter_select_block" onclick="smartFilter.showDropDownPopup(this, '<?=CUtil::JSEscape($key)?>')">
											<div class="bx_filter_select_text" data-role="currentOption">
												<?foreach($arItem["VALUES"] as $val => $arOption) {
													if($arOption["CHECKED"]) {
														echo $arOption["VALUE"];
														$checkedItemExist = true;
													}
												}
												if(!$checkedItemExist) {
													echo GetMessage("CT_BCSF_FILTER_ALL");
												}?>
											</div>
											<div class="bx_filter_select_arrow"></div>
											<input style="display:none" type="radio" name="<?=$arCur["CONTROL_NAME_ALT"]?>" id="<?="all_".$arCur["CONTROL_ID"]?>" value="" />
											<?foreach($arItem["VALUES"] as $val => $arOption):?>
												<input style="display:none" type="radio" name="<?=$arOption["CONTROL_NAME_ALT"]?>" id="<?=$arOption["CONTROL_ID"]?>" value="<?=$arOption["HTML_VALUE_ALT"]?>" <?=$arOption["CHECKED"] ? "checked=\"checked\"" : ""?> />
											<?endforeach?>
											<div class="bx_filter_select_popup" data-role="dropdownContent" style="display:none;">
												<ul>
													<li>
														<label for="<?="all_".$arCur["CONTROL_ID"]?>" class="bx_filter_param_label" data-role="label_<?="all_".$arCur["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape("all_".$arCur["CONTROL_ID"])?>')">
															<?=GetMessage("CT_BCSF_FILTER_ALL");?>
														</label>
													</li>
													<?foreach($arItem["VALUES"] as $val => $arOption):
														$class = "";
														if($arOption["CHECKED"])
															$class.= " selected";
														if($arOption["DISABLED"])
															$class.= " disabled";?>
														<li>
															<label for="<?=$arOption["CONTROL_ID"]?>" class="bx_filter_param_label<?=$class?>" data-role="label_<?=$arOption["CONTROL_ID"]?>" onclick="smartFilter.selectDropDownItem(this, '<?=CUtil::JSEscape($arOption["CONTROL_ID"])?>')">
																<?=$arOption["VALUE"]?>
															</label>
														</li>
													<?endforeach?>
												</ul>
											</div>
										</div>
									</div>
									<?break;
								case "R": //DROPDOWN_WITH_PICTURES_AND_LABELS
									break;
								case "K": //RADIO_BUTTONS ?>
									<div class="custom-forms">
										<input type="radio" id="<?="all_".$arCur['CONTROL_ID']?>" name="<?=$arCur['CONTROL_NAME_ALT']?>" value="" onclick="smartFilter.click(this)" />
										<label for="<?="all_".$arCur['CONTROL_ID']?>">
											<?=GetMessage("CT_BCSF_FILTER_ALL")?>
										</label>
									</div>
									<?foreach($arItem["VALUES"] as $val => $arOption):?>										
										<div class="custom-forms">
											<input type="radio" id="<?=$arOption['CONTROL_ID']?>" name="<?=$arOption['CONTROL_NAME_ALT']?>" <?=$arOption["CHECKED"] ? "checked=\"checked\"" : ""?> <?=$arOption["DISABLED"] ? "disabled=\"disabled\"" : ""?> value="<?=$arOption['HTML_VALUE_ALT']?>" onclick="smartFilter.click(this)" />
											<label data-role="label_<?=$arOption['CONTROL_ID']?>" <?=$arOption["DISABLED"] ? "class=\"disabled\"" : ""?> for="<?=$arOption['CONTROL_ID']?>"><?=$arOption["VALUE"]?><?if($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($arOption["ELEMENT_COUNT"])):?><span class="count" data-role="count_<?=$arOption["CONTROL_ID"]?>"><?=$arOption["ELEMENT_COUNT"]?></span><?endif;?></label>
										</div>
									<?endforeach;
									break;
								default: //CHECKBOXES
									foreach($arItem["VALUES"] as $val => $arOption):?>
										<div class="custom-forms <?=($arItem['CODE'] == 'COLOR') ? 'colors' : ''?>">
											<input type="checkbox" id="<?=$arOption['CONTROL_ID']?>" name="<?=$arOption['CONTROL_NAME']?>" <?=$arOption["CHECKED"] ? "checked=\"checked\"" : ""?> <?=$arOption["DISABLED"] ? "disabled=\"disabled\"" : ""?> value="<?=$arOption['HTML_VALUE']?>" onclick="smartFilter.click(this)" />
											<?if($arItem["CODE"] != "COLOR"):?>
												<label data-role="label_<?=$arOption['CONTROL_ID']?>" <?=$arOption["DISABLED"] ? "class=\"disabled\"" : ""?> for="<?=$arOption['CONTROL_ID']?>"><?=$arOption["VALUE"]?><?if($arParams["DISPLAY_ELEMENT_COUNT"] !== "N" && isset($arOption["ELEMENT_COUNT"])):?><span class="count" data-role="count_<?=$arOption["CONTROL_ID"]?>"><?=$arOption["ELEMENT_COUNT"]?></span><?endif;?></label>
											<?elseif($arItem["CODE"] == "COLOR"):
												if(is_array($arOption["PICT"])):?>
													<label data-role="label_<?=$arOption['CONTROL_ID']?>" for="<?=$arOption['CONTROL_ID']?>" <?=$arOption["DISABLED"] ? "class=\"disabled\"" : ""?> title="<?=$arOption['NAME']?>">
														<img src="<?=$arOption['PICT']['SRC']?>" width="<?=$arOption['PICT']['WIDTH']?>" height="<?=$arOption['PICT']['HEIGHT']?>" alt="<?=$arOption['NAME']?>" title="<?=$arOption['NAME']?>" />
													</label>
												<?else:?>
													<label data-role="label_<?=$arOption['CONTROL_ID']?>" for="<?=$arOption['CONTROL_ID']?>" <?=$arOption["DISABLED"] ? "class=\"disabled\"" : ""?> title="<?=$arOption['NAME']?>">
														<i style="background:#<?=$arOption['HEX']?>;"></i>
													</label>
												<?endif;
											endif;?>
										</div>
									<?endforeach;
							}?>
						</div>
					</td>
				</tr>
			<?endforeach;?>
		</table>
		<div class="clr"></div>
		<div class="submit">
			<a href="javascript:void(0)" class="catalog_item_toogle_filter_hidden"<?=($arSetting["SMART_FILTER_LOCATION"]["VALUE"] == "VERTICAL") ? " style='display:none;'" : "";?>><?=GetMessage("FILTER_HIDDEN")?></a>			
			<?if($arSetting["SMART_FILTER_LOCATION"]["VALUE"] == "HORIZONTAL"):?>
				<button type="submit" name="set_filter" id="set_filter" class="btn_buy popdef" value="y"><?=GetMessage("FILTER_SET")?></button>
			<?endif;			
			if($arParams["SEF_MODE"] == "Y"):?>
				<a id="del_filter" class="catalog_item_toogle_filter_reset" href="javascript:void(0)" rel="nofollow"><?=GetMessage("FILTER_RESET")?></a>
			<?else:
				if(isset($_REQUEST["set_filter"]) && $_REQUEST["set_filter"] == "y"):?>
					<a class="catalog_item_toogle_filter_reset" href="<?=$arResult['FORM_ACTION']?>" rel="nofollow"><?=GetMessage("FILTER_RESET")?></a>
				<?endif;
			endif;?>			
			<div id="modef" <?if(!isset($arResult["ELEMENT_COUNT"])) echo "style=\"display:none\"";?>>
				<?=GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>				
			</div>
			<div id="modef_popup" class="bx_filter_popup" <?if(!isset($arResult["ELEMENT_COUNT"])) echo "style=\"display:none\"";?>>
				<?=GetMessage("CT_BCSF_FILTER_COUNT", array("#ELEMENT_COUNT#" => '<span id="modef_popup_num">'.intval($arResult["ELEMENT_COUNT"]).'</span>'));?>
				<span class="arrow"></span>
				<a href="<?=$arResult["FILTER_URL"]?>" rel="nofollow"><?=GetMessage("FILTER_SET")?></a>
			</div>			
			<?if($arSetting["SMART_FILTER_LOCATION"]["VALUE"] == "VERTICAL"):?>
				<button type="submit" name="set_filter" id="set_filter" class="btn_buy popdef" value="y"><?=GetMessage("FILTER_SET")?></button>
			<?endif;?>
			<div class="clr"></div>
		</div>
	</form>
</div>

<script>
	var smartFilter = new JCSmartFilter('<?=CUtil::JSEscape($arResult["FORM_ACTION"])?>', <?=CUtil::PhpToJSObject($arResult["JS_FILTER_PARAMS"])?>);
</script>

<?if($arSetting["SMART_FILTER_LOCATION"]["VALUE"] == "VERTICAL"):
	$this->EndViewTarget();		
endif;?>