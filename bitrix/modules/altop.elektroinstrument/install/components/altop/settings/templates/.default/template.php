<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(false);

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Config\Option;?>

<div class="style-switcher <?=$_COOKIE['styleSwitcher'] == 'open' ? 'active' : ''?>">
	<div class="header">
		<?=Loc::getMessage("THEME_MODIFY")?><span class="switch"><i class="fa fa-cog"></i></span>		
	</div>
	<form action="javascript:void(0)<?/*=$APPLICATION->GetCurPage()*/?>" method="POST" name="style-switcher">
		<?=bitrix_sessid_post();
		$i = 1;
		foreach($arResult as $optionCode => $arOption):			
			if($arOption["IN_SETTINGS_PANEL"] == "Y"):
				if($optionCode == "COLOR_SCHEME_CUSTOM" || $optionCode == "SMART_FILTER_VISIBILITY"):
					continue;
				else:?>
					<div class="block">					
						<div class="block-title">
							<span><?=$optionCode == "SMART_FILTER_LOCATION" ? Loc::getMessage("SMART_FILTER") : $arOption["TITLE"]?></span>
							<a class="plus" id="plus-minus-<?=$optionCode?>" href="javascript:void(0)"><i class="fa fa-plus-circle"></i><i class="fa fa-minus-circle"></i></a>
						</div>
						<div class="options" id="options-<?=$optionCode?>" style="display:none;">							
							<?$k = 1;
							if($optionCode == "COLOR_SCHEME"):
								foreach($arOption["LIST"] as $colorCode => $arColor):
									if($colorCode !== "CUSTOM"):?>
										<div class="custom-forms colors" data-color="<?=$arColor['COLOR']?>">
											<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arColor["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$colorCode?>" />
											<label for="option-<?=$i?>-<?=$k?>" title="<?=$arColor['TITLE']?>">
												<i class="fa fa-check" style="background:<?=$arColor['COLOR']?>;"></i>
											</label>
										</div>
										<?$k++;
									endif;
								endforeach;?>
								<div class="clr"></div>								
								<div class="color-scheme-custom">
									<?foreach($arOption["LIST"] as $colorCode => $arColor):
										if($colorCode == "CUSTOM"):?>											
											<div class="custom-forms colors" data-color="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM']['VALUE']) > 0) ? $arResult['COLOR_SCHEME_CUSTOM']['VALUE'] : $arResult['COLOR_SCHEME_CUSTOM']['DEFAULT']?>">
												<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arColor["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$colorCode?>" />
												<label for="option-<?=$i?>-<?=$k?>" title="<?=$arColor['TITLE']?>">
													<i class="fa fa-check" style="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM']['VALUE']) > 0) ? 'background:'.$arResult['COLOR_SCHEME_CUSTOM']['VALUE'].';' : 'background:'.$arResult['COLOR_SCHEME_CUSTOM']['DEFAULT'].';'?>"></i>
												</label>
											</div>
											<input type="text" id="option-color-scheme-custom" name="COLOR_SCHEME_CUSTOM" maxlength="7" value="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM']['VALUE']) > 0) ? $arResult['COLOR_SCHEME_CUSTOM']['VALUE'] : $arResult['COLOR_SCHEME_CUSTOM']['DEFAULT']?>" />
											<button type="button" name="palette_button" class="btn_buy apuo"><i class="fa fa-eyedropper"></i><span><?=Loc::getMessage("PALETTE")?></span></button>
											<?$k++;
										endif;
									endforeach;?>
									<div class="clr"></div>
								</div>
							<?elseif($optionCode == "SITE_BACKGROUND"):?>
								<div class="custom-forms colors">
									<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>"<?=($arOption["VALUE"] == "N" ? " checked='checked'" : "");?> value="N" />
									<label for="option-<?=$i?>-<?=$k?>" title="<?=Loc::getMessage('SITE_BACKGROUND_OFF')?>">
										<i class="fa fa-check" style="background-image:url('<?=$this->GetFolder();?>/images/bg-off-24.jpg');"></i>
									</label>
								</div>
								<?$k++;
								foreach($arParams["SITE_BACKGROUNDS"] as $arSiteBg):?>
									<div class="custom-forms colors">
										<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>"<?=($arOption["VALUE"] == "Y" && $arResult["SITE_BACKGROUND_PICTURE"]["VALUE"] > 0 && $arResult["SITE_BACKGROUND_PICTURE"]["VALUE"] == Option::get($arParams["MODULE_ID"], "SITE_BACKGROUND_".$arSiteBg) ? " checked='checked'" : "");?> value="<?=$arSiteBg?>" />
										<label for="option-<?=$i?>-<?=$k?>" title="<?=Loc::getMessage('SITE_BACKGROUND_'.$arSiteBg)?>">
											<i class="fa fa-check" style="background-image:url('<?=$this->GetFolder();?>/images/<?=mb_strtolower($arSiteBg)?>-24.jpg');"></i>
										</label>
									</div>								
									<?$k++;
								endforeach;?>												
								<div class="clr"></div>
							<?else:
								if($arOption["TYPE"] == "selectbox"):							
									foreach($arOption["LIST"] as $variantCode => $arVariant):?>								
										<div class="custom-forms">
											<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arVariant["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$variantCode?>" />
											<label for="option-<?=$i?>-<?=$k?>"><?=$arVariant["TITLE"]?></label>
										</div>
										<?$k++;
									endforeach;									
									if($optionCode == "SMART_FILTER_LOCATION"):
										foreach($arResult as $optionCode => $arOption):
											if($arOption["IN_SETTINGS_PANEL"] == "Y"):
												if($optionCode == "SMART_FILTER_VISIBILITY"):
													foreach($arOption["LIST"] as $variantCode => $arVariant):?>						
														<div class="custom-forms">
															<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arVariant["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$variantCode?>" />
															<label for="option-<?=$i?>-<?=$k?>"><?=$arVariant["TITLE"]?></label>
														</div>
														<?$k++;
													endforeach;
												endif;
											endif;
										endforeach;
									endif;?>									
									<div class="clr"></div>
								<?elseif($arOption["TYPE"] == "multiselectbox"):							
									foreach($arOption["LIST"] as $variantCode => $arVariant):?>								
										<div class="custom-forms option">
											<input type="checkbox" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>[]" <?=$arVariant["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$variantCode?>" />
											<label for="option-<?=$i?>-<?=$k?>"><span class="check-cont"><span class="check"><i class="fa fa-check"></i></span></span><span class="check-title"><?=$arVariant["TITLE"]?></span></label>
										</div>
										<?$k++;
									endforeach;
								/***deprecated	
								elseif($optionCode == "SHOW_PERSONAL_DATA"):
									foreach($arOption["CHEKBOX"] as $variantCode => $arVariant):?>
										<div class="custom-forms option" name="<?=$optionCode?>">
											<input type="checkbox" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arVariant["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$arVariant["CURRENT"]?>" />
											<label for="option-<?=$i?>-<?=$k?>"><span class="check-cont"><span class="check"><i class="fa fa-check"></i></span></span><span class="check-title"><?=$arVariant["TITLE"]?></span></label>
										</div>
										<?$k++;
									endforeach;
								***/
								endif;
							endif;?>
						</div>						
					</div>
					<?$i++;
				endif;
			else:?>
				<input type="hidden" name="<?=$optionCode?>" value="<?=strtr(base64_encode(serialize($arOption['VALUE'])), '+/=', '-_,')?>" />
			<?endif;			
		endforeach;?>
		<input type="hidden" name="SITE_BACKGROUNDS" value="<?=strtr(base64_encode(serialize($arParams["SITE_BACKGROUNDS"])), '+/=', '-_,')?>" />
		<div class="reset">
			<div class="text"><?=Loc::getMessage("MORE_SETTINGS")?></div>
			<button type="button" name="reset_button" class="btn_buy apuo"><i class="fa fa-repeat"></i><span><?=Loc::getMessage("THEME_RESET")?></span></button>
		</div>
	</form>
	
	<script type="text/javascript">
		$(function() {
			if($.cookie("styleSwitcher") == "open") {
				$(".style-switcher").addClass("active");
			}
			
			$(".style-switcher .switch").hover(function(e) {
				$(".fa-cog").addClass("fa-spin");
			}, function() {
				$(".fa-cog").removeClass("fa-spin");
			});
			
			$(".style-switcher .switch").click(function(e) {
				e.preventDefault();
				var styleswitcher = $(this).closest(".style-switcher");
				if(styleswitcher.hasClass("active")) {
					styleswitcher.animate({right: "-" + styleswitcher.outerWidth() + "px"}, 300).removeClass("active");
					$.removeCookie("styleSwitcher", {path: "/"});
				} else {
					styleswitcher.animate({right: "0"}, 300).addClass("active");
					var pos = styleswitcher.offset().top;
					if($(window).scrollTop() > pos){
						$("html, body").animate({scrollTop: pos}, 500);
					}
					$.cookie("styleSwitcher", "open", {path: "/"});
				}
			});
			
			<?foreach($arResult as $optionCode => $arOption):
				if($arOption["IN_SETTINGS_PANEL"] == "Y"):?>
					if($.cookie("plus-minus-<?=$optionCode?>") == "open") {
						$("#plus-minus-<?=$optionCode?>").removeClass().addClass("minus");
						$(".style-switcher .block #options-<?=$optionCode?>").show();
					}	
						
					$("#plus-minus-<?=$optionCode?>").click(function() {
						var clickitem = $(this);
						if(clickitem.hasClass("plus")) {
							clickitem.removeClass().addClass("minus");
							$.cookie("plus-minus-<?=$optionCode?>", "open", {path: "/"});
						} else {
							clickitem.removeClass().addClass("plus");
							$.removeCookie("plus-minus-<?=$optionCode?>", {path: "/"});
						}
						$(".style-switcher .block #options-<?=$optionCode?>").slideToggle();
					});
				<?endif;
			endforeach;?>
			
			var curColor = $(".colors.custom-forms.active").data("color");				
				customColorDiv = $(".color-scheme-custom .colors.custom-forms i"),
				customColorInput = $(".color-scheme-custom input[id=option-color-scheme-custom]"),
				paletteButton = $(".color-scheme-custom button[name=palette_button]"),
				formSwitcher = $("form[name=style-switcher]");

			paletteButton.spectrum({				
				clickoutFiresChange: false,
				cancelText: "<i class='fa fa-times'></i>",
				chooseText: "<?=Loc::getMessage('PALETTE_CHOOSE_COLOR')?>",
				containerClassName:"palette_cont",				
				move: function(color) {
					var colorCode = color.toHexString();					
					customColorDiv.attr("style", "background:" + colorCode + ";");
					customColorInput.val(colorCode);
				},
				hide: function(color) {
					var colorCode = color.toHexString();
					customColorDiv.attr("style", "background:" + colorCode + ";");
					customColorInput.val(colorCode);
				},
				change: function(color) {
					customColorDiv.parent().parent().find("input").attr("checked", "checked");					
					formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
					formSwitcher.submit();					
				}
			});			
					
			if(curColor != undefined && curColor.length > 0) {
				paletteButton.spectrum("set", curColor);
				customColorDiv.attr("style", "background:" + curColor + ";");
				customColorInput.val(curColor);
			}
			
			customColorInput.change(function() {				
				var colorCode = $(this).val();
				if(colorCode.length > 0) {
					colorCode = colorCode.replace(/#/g, "");
					if(colorCode.length < 3) {
						for($i = 0, $l = 6 - colorCode.length; $i < $l; ++$i) {
							colorCode = colorCode + "0";
						}					
					}
					colorCode = "#" + colorCode;
					$(this).val(colorCode);
					customColorDiv.attr("style", "background:" + colorCode + ";");
				} else {
					if(curColor != undefined && curColor.length > 0) {
						$(this).val(curColor);
						customColorDiv.attr("style", "background:" + curColor + ";");
					}
				}
			});
			
			$(".color-scheme-custom").on("keypress", "input[id=option-color-scheme-custom]", function(e) {
				if(e.keyCode == 13){	
					e.preventDefault();
					$(this).parents(".color-scheme-custom").find(".colors.custom-forms input").attr("checked", "checked");
					formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
					formSwitcher.submit();					
				}
			});
			
			$("div.custom-forms[name='SHOW_PERSONAL_DATA']").click(function(){
				if($(this).hasClass("active")) {
					$(this).children("input").val("N");
				} else {
					$(this).children("input").val("Y");
				}
				console.log($(this).children("input").val());
			});
			
			$(".style-switcher .reset button[name=reset_button]").click(function(e) {
				formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
				formSwitcher.append("<input type='hidden' name='THEME' value='default' />");
				formSwitcher.submit();				
			});
			
			$(".style-switcher .options input[type=radio], .style-switcher .options input[type=checkbox]").click(function(e) {		
				formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
				formSwitcher.submit();				
			});

			formSwitcher.submit(function(e) {
				e.preventDefault();

				var $form = $(e.target);
				
				$.ajax({
					url: '<?=$componentPath?>/ajax.php',
					type: "POST",
					data: $form.serialize(),					
					success: function() {
						location.reload();
					}
				});
			});
		});
	</script>	
</div>