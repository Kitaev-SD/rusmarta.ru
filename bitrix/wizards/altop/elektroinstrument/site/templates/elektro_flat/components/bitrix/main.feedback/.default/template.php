<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true) die();?>
<?$arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);?>

<div class="mfeedback" id="mfeedback">
	<?$frame = $this->createFrame("mfeedback")->begin();
		if(!empty($arResult["ERROR_MESSAGE"])) {
			foreach($arResult["ERROR_MESSAGE"] as $v)
				ShowError($v);
		}
		if(strlen($arResult["OK_MESSAGE"]) > 0) {
			ShowMessage(array("TYPE" => "OK", "MESSAGE" => $arResult["OK_MESSAGE"]));
		}?>
		<form action="<?=$APPLICATION->GetCurPage()?>" method="POST">
			<?=bitrix_sessid_post()?>
			<div class="row">
				<div class="span1">
					<?=GetMessage("MFT_NAME")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("NAME", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</div>
				<div class="span2">
					<input type="text" name="user_name" value="<?=$arResult["AUTHOR_NAME"]?>">
				</div>
				<div class="clr"></div>
			</div>
			<div class="row">
				<div class="span1">
					<?=GetMessage("MFT_EMAIL")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("EMAIL", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</div>
				<div class="span2">
					<input type="text" name="user_email" value="<?=$arResult["AUTHOR_EMAIL"]?>">
				</div>
				<div class="clr"></div>
			</div>
			<div class="row">
				<div class="span1">
					<?=GetMessage("MFT_MESSAGE")?><?if(empty($arParams["REQUIRED_FIELDS"]) || in_array("MESSAGE", $arParams["REQUIRED_FIELDS"])):?><span class="mf-req">*</span><?endif?>
				</div>
				<div class="span2">
					<textarea name="MESSAGE" rows="5" cols="40"><?=$arResult["MESSAGE"]?></textarea>
				</div>
				<div class="clr"></div>
			</div>
			<?if($arParams["USE_CAPTCHA"] == "Y"):?>
				<div class="row">
					<div class="span1">
						<?=GetMessage("MFT_CAPTCHA")?><span class="mf-req">*</span>
					</div>
					<div class="span2">
						<input type="text" name="captcha_word" size="30" maxlength="50" value="" />
						<input type="hidden" name="captcha_sid" value="<?=$arResult["capCode"]?>" />						
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["capCode"]?>" width="127" height="30" alt="CAPTCHA" />
					</div>
					<div class="clr"></div>
				</div>
			<?endif;?>
			<? //AGREEMENT//
			if($arSetting["SHOW_PERSONAL_DATA"] == "Y"){?>
				<div class="hint_agreement">
					<input type="hidden" name="PERSONAL_DATA" id="PERSONAL_DATA_mfeedback" value="N">
					<div class="checkbox">
						<span class="input-checkbox" id="input-checkbox_mfeedback"></span>
					</div>	
					<div class="label">
						<?=$arSetting["TEXT_PERSONAL_DATA"]?>
					</div>
				</div>
			<?}?>	
			<div class="submit">
				<input type="hidden" name="PARAMS_HASH" value="<?=$arResult["PARAMS_HASH"]?>">
				<button type="submit" name="submit" class="btn_buy popdef" id="submit" value="<?=GetMessage("MFT_SUBMIT")?>"><?=GetMessage("MFT_SUBMIT")?></button>
			</div>
		</form>
	<?$frame->end();?>
	<div class="clr"></div>
</div>
<script>
	//CHEKED//
	BX.bind(BX("input-checkbox_mfeedback"),"click",function(){
		if(!BX.hasClass(BX("input-checkbox_mfeedback"),"cheked")){
			BX.addClass(BX("input-checkbox_mfeedback"),"cheked");
			BX.adjust(BX("input-checkbox_mfeedback"),{
				children:[
					BX.create("i",{
						props:{
							className:"fa fa-check"
						}
					})
				]
			});
			BX.adjust(BX("PERSONAL_DATA_mfeedback"),{
				props:{
					"value":"Y"
				}
			});
		} else {
			BX.removeClass(BX("input-checkbox_mfeedback"),"cheked");
			BX.remove(BX.findChild(BX("input-checkbox_mfeedback"),{
				className:"fa fa-check"
			}));
			BX.adjust(BX("PERSONAL_DATA_mfeedback"),{
				props:{
					"value":"N"
				}
			});
		}
	});
		
	//SUBMIT//
	BX.bind(BX("submit"),"click",function(e){
		//OTHER_FIELDS//
		var fields = BX.findChildren(BX("mfeedback"),{"class":"span2"},true);
		var alert = Array();
		var name = Array();
		var text;
		for(var i in fields){
			name[i] = BX.findChild(BX(fields[i])).name;
			if(name[i] == "user_name") {
				text = "<?=GetMessage("NOT_FIELD_NAME")?>"
			} 
			else if(name[i] == "user_email") {
				text = "<?=GetMessage("NOT_FIELD_EMAIL")?>"
			} 
			else if(name[i] == "MESSAGE") {
				text = "<?=GetMessage("NOT_FIELD_MESSAGE")?>"
			}
			if(BX.findChild(BX(fields[i])).value == "") {
				alert[i] = BX.create("span",{
					props:{
						className:"alertMsg bad",
						id:name[i]
					},
					children:[
						BX.create("i",{
							props:{
								className:"fa fa-exclamation-triangle"
							}
						}),
						BX.create("span",{
							props:{
								className:"text"
							},
							text:text
						})
					]
				});
				if(!BX(name[i]))
					BX("mfeedback").insertBefore(alert[i],BX.findChild(BX("mfeedback")));
			} else {
				BX.remove(BX(name[i]));
			}
		}
		//PERSONAL_DATA//
		var alertPersonal;
		if(BX("PERSONAL_DATA_mfeedback").value == "N"){
			alertPersonal = BX.create("span",{
				props:{
					className:"alertMsg bad",
					id:"PERSONAL_DATA_BAD"
				},
				children:[
					BX.create("i",{
						props:{
							className:"fa fa-exclamation-triangle"
						}
					}),
					BX.create("span",{
						props:{
							className:"text"
						},
						html:"<?=GetMessage("NOT_FIELD_PERSONAL_DATA")?>"
					})
				]
			});
			if(!BX("PERSONAL_DATA_BAD"))
				BX("mfeedback").insertBefore(alertPersonal,BX.findChild(BX("mfeedback")));
		} else {
			BX.remove(BX("PERSONAL_DATA_BAD"));
		}

		if(alert.length > 0 || BX(alertPersonal)) {
			BX.PreventDefault(e);
		}
	});
</script>