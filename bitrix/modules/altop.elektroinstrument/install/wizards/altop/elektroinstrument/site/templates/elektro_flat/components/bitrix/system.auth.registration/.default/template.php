<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);?>

<div class="content-form register-form" id="register-form">
	<div class="fields">
		<?ShowMessage($arParams["~AUTH_RESULT"]);?>
		<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y" && is_array($arParams["AUTH_RESULT"]) &&  $arParams["AUTH_RESULT"]["TYPE"] === "OK"):?>
			<div class="field"><?echo GetMessage("AUTH_EMAIL_SENT")?></div>
		<?else:?>
			<?if($arResult["USE_EMAIL_CONFIRMATION"] === "Y"):?>
				<div class="field"><?echo GetMessage("AUTH_EMAIL_WILL_BE_SENT")?></div>
			<?endif?>
			<!--noindex-->
			<form method="post" action="<?=$arResult["AUTH_URL"]?>" name="bform">
				<?if(strlen($arResult["BACKURL"]) > 0) {?>
					<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
				<?}?>
				<input type="hidden" name="AUTH_FORM" value="Y" />
				<input type="hidden" name="TYPE" value="REGISTRATION" />
				<div class="field">
					<label class="field-title"><?=GetMessage("AUTH_NAME")?></label>
					<div class="form-input">
						<input type="text" name="USER_NAME" maxlength="50" value="<?=$arResult["USER_NAME"]?>" />
					</div>
				</div>
				<div class="field">
					<label class="field-title"><?=GetMessage("AUTH_LAST_NAME")?></label>
					<div class="form-input">
						<input type="text" name="USER_LAST_NAME" maxlength="50" value="<?=$arResult["USER_LAST_NAME"]?>" />
					</div>
				</div>
				<div class="field">
					<label class="field-title"><?=GetMessage("AUTH_LOGIN_MIN")?><span class="starrequired">*</span></label>
					<div class="form-input">
						<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["USER_LOGIN"]?>" />
					</div>
					<div class="description">&mdash; <?=GetMessage("LOGIN_REQUIREMENTS")?></div>
				</div>
				<div class="field">
					<label class="field-title"><?=GetMessage("AUTH_PASSWORD_REQ")?><span class="starrequired">*</span></label>
					<div class="form-input">
						<input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" />
					</div>
					<div class="description">&mdash; <?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></div>
				</div>
				<div class="field">
					<label class="field-title"><?=GetMessage("AUTH_CONFIRM")?><span class="starrequired">*</span></label>
					<div class="form-input">
						<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>" />
					</div>
				</div>
				<div class="field">
					<label class="field-title">E-Mail<span class="starrequired">*</span></label>
					<div class="form-input">
						<input type="text" name="USER_EMAIL" maxlength="255" value="<?=$arResult["USER_EMAIL"]?>" />
					</div>
				</div>
				<?//User properties//?>
				<?if($arResult["USER_PROPERTIES"]["SHOW"] == "Y"):?>
					<div class="field"><?=strLen(trim($arParams["USER_PROPERTY_NAME"])) > 0 ? $arParams["USER_PROPERTY_NAME"] : GetMessage("USER_TYPE_EDIT_TAB")?></div>
					<?foreach($arResult["USER_PROPERTIES"]["DATA"] as $FIELD_NAME => $arUserField):?>
						<div class="field">
							<label class="field-title">
								<?=$arUserField["EDIT_FORM_LABEL"]?><?if ($arUserField["MANDATORY"]=="Y"):?><span class="required">*</span><?endif;?>
							</label>
							<div class="form-input">
								<?$APPLICATION->IncludeComponent(
									"bitrix:system.field.edit",
									$arUserField["USER_TYPE"]["USER_TYPE_ID"],
									array("bVarsFromForm" => $arResult["bVarsFromForm"], "arUserField" => $arUserField, "form_name" => "bform"), null, array("HIDE_ICONS"=>"Y"));?>
							</div>
						</div>
					<?endforeach;?>
				<?endif;?>
				<? //CAPTCHA//
				if($arResult["USE_CAPTCHA"] == "Y") {?>
					<div class="field">
						<label class="field-title"><?=GetMessage("CAPTCHA_REGF_PROMT")?><span class="starrequired">*</span></label>
						<div class="form-input">
							<input type="text" name="captcha_word" maxlength="50" value="" />
							<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
							<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="127" height="30" alt="CAPTCHA" />
							<div class="clr"></div>
						</div>						
					</div>
				<?}
				//AGREEMENT//
				if($arSetting["SHOW_PERSONAL_DATA"] == "Y"){?>
					<div class="hint_agreement">
						<input type="hidden" name="PERSONAL_DATA" id="PERSONAL_DATA_register" value="N">
						<div class="checkbox">
							<span class="input-checkbox" id="input-checkbox_register"></span>
						</div>	
						<div class="label">
							<?=$arSetting["TEXT_PERSONAL_DATA"]?>
						</div>
					</div>
				<?}?>	
				<div class="field field-button">
					<button type="submit" id="submit" name="Register" class="btn_buy popdef" value="<?=GetMessage("AUTH_REGISTER")?>"><?=GetMessage("AUTH_REGISTER")?></button>	
				</div>
			</form>
			<!--/noindex-->
			<script type="text/javascript">
				document.bform.USER_NAME.focus();
			</script>
		<?endif?>
	</div>
</div>
<script>
	//CHEKED//
	BX.bind(BX("input-checkbox_register"),"click",function(){
		if(!BX.hasClass(BX("input-checkbox_register"),"cheked")){
			BX.addClass(BX("input-checkbox_register"),"cheked");
			BX.adjust(BX("input-checkbox_register"),{
				children:[
					BX.create("i",{
						props:{
							className:"fa fa-check"
						}
					})
				]
			});
			BX.adjust(BX("PERSONAL_DATA_register"),{
				props:{
					"value":"Y"
				}
			});
		} else {
			BX.removeClass(BX("input-checkbox_register"),"cheked");
			BX.remove(BX.findChild(BX("input-checkbox_register"),{
				className:"fa fa-check"
			}));
			BX.adjust(BX("PERSONAL_DATA_register"),{
				props:{
					"value":"N"
				}
			});
		}
	});
		
	//SUBMIT//
	BX.bind(BX("submit"),"click",function(e){
		//OTHER_FIELDS//
		var fields = BX.findChildren(BX("register-form"),{"class":"form-input"},true);
		var alert = Array();
		var name = Array();
		var text;
		for(var i in fields){
			name[i] = BX.findChild(BX(fields[i])).name;
			if(name[i] == "USER_LOGIN") {
				text = "<?=GetMessage("NOT_FIELD_LOGIN")?>"
			} 
			else if(name[i] == "USER_PASSWORD") {
				text = "<?=GetMessage("NOT_FIELD_PASSWORD")?>"
			} 
			else if(name[i] == "USER_CONFIRM_PASSWORD") {
				text = "<?=GetMessage("NOT_FIELD_CONFIRM_PASSWORD")?>"
			}
			else if(name[i] == "USER_EMAIL") {
				text = "<?=GetMessage("NOT_FIELD_EMAIL")?>"
			}
			<?if($arResult["USE_CAPTCHA"] == "Y") {?>
				else if(name[i] == "captcha_word") {
					text = "<?=GetMessage("NOT_FIELD_CAPTCHA")?>"
				}
			<?}?>
			if(name[i] == "USER_LOGIN" || name[i] == "USER_PASSWORD" || name[i] == "USER_CONFIRM_PASSWORD" || name[i] == "USER_EMAIL" || name[i] == "captcha_word") {
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
				if(BX.findChild(BX(fields[i])).value == "") {
					if(!BX(name[i]))
						BX("register-form").insertBefore(alert[i],BX.findChild(BX("register-form")));
				} else {
					BX.remove(BX(name[i]));
				}
			}
		}
		//PERSONAL_DATA//
		var alertPersonal;
		if(BX("PERSONAL_DATA_register").value == "N"){
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
				BX("register-form").insertBefore(alertPersonal,BX.findChild(BX("register-form")));
		} else {
			BX.remove(BX("PERSONAL_DATA_BAD"));
			alertPersonal = null;
		}
	
		if(BX(name[i]) || BX(alertPersonal)) {
			BX.PreventDefault(e);
		}
	});
</script>