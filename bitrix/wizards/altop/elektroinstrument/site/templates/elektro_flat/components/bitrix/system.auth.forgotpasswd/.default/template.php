<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$arSetting = CElektroinstrument::GetFrontParametrsValues(SITE_ID);?>
<div class="content-form forgot-form" id="forgot-form">
	<div class="fields">
		<?ShowMessage($arParams["~AUTH_RESULT"]);?>
		<form name="bform" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
			<?if(strlen($arResult["BACKURL"]) > 0) {?>
				<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
			<?}?>
			<input type="hidden" name="AUTH_FORM" value="Y">
			<input type="hidden" name="TYPE" value="SEND_PWD">
			<div class="field">
				<?=GetMessage("AUTH_FORGOT_PASSWORD_1")?>
			</div>
			<div class="field">
				<label class="field-title"><?=GetMessage("AUTH_LOGIN")?></label>
				<div class="form-input">
					<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />
				</div>
			</div>
			<div class="field">
				<label class="field-title">E-Mail</label>
				<div class="form-input">
					<input type="text" name="USER_EMAIL" maxlength="255" />
				</div>
			</div>
			<?if($arResult["USE_CAPTCHA"]):?>
				<div class="field">
					<label class="field-title"><?=GetMessage("AUTH_CAPTCHA")?></label>
					<div class="form-input">
						<input type="text" name="captcha_word" maxlength="50" value="" />
						<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="127" height="30" alt="CAPTCHA" />
						<div class="clr"></div>
					</div>
				</div>
			<?endif;?>
			<?if($arSetting["SHOW_PERSONAL_DATA"] == "Y"){?>
				<div id="hint_agreement" class="hint_agreement">
					<input type="hidden" name="PERSONAL_DATA" id="PERSONAL_DATA" value="N">
					<div class="checkbox">
						<span class="input-checkbox" id="input-checkbox"></span>
					</div>	
					<div class="label">
						<?=$arSetting["TEXT_PERSONAL_DATA"]?>
					</div>
				</div>
			<?}?>	
			<div class="field field-button">
				<button type="submit" id="submit" name="send_account_info" class="btn_buy popdef" value="<?=GetMessage("AUTH_SEND")?>"><?=GetMessage("AUTH_SEND")?></button>	
			</div>
			<div class="field">
				<a class="btn_buy boc_anch" href="<?=$arResult["AUTH_AUTH_URL"]?>"><i class="fa fa-user"></i><?=GetMessage("AUTH_AUTH")?></a>
			</div> 
		</form>
		<script type="text/javascript">
			document.bform.USER_LOGIN.focus();
		</script>
	</div>
</div>
<script>
	//CHEKED//
	BX.bind(BX("input-checkbox"),"click",function(){
		if(!BX.hasClass(BX("input-checkbox"),"cheked")){
			BX.addClass(BX("input-checkbox"),"cheked");
			BX.adjust(BX("input-checkbox"),{
				children:[
					BX.create("i",{
						props:{
							className:"fa fa-check"
						}
					})
				]
			});
			BX.adjust(BX("PERSONAL_DATA"),{
				props:{
					"value":"Y"
				}
			});
		} else {
			BX.removeClass(BX("input-checkbox"),"cheked");
			BX.remove(BX.findChild(BX("input-checkbox"),{
				className:"fa fa-check"
			}));
			BX.adjust(BX("PERSONAL_DATA"),{
				props:{
					"value":"N"
				}
			});
		}
	});
		
	//SUBMIT//
	BX.bind(BX("submit"),"click",function(){
		if(BX("PERSONAL_DATA").value == "N"){
			var alert = BX.create("span",{
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
			BX("forgot-form").insertBefore(alert,BX.findChild(BX("forgot-form"),{"class":"fields"}));
			event.preventDefault();
		} else {
			BX.remove(BX("PERSONAL_DATA_BAD"));
		}
	});
</script>