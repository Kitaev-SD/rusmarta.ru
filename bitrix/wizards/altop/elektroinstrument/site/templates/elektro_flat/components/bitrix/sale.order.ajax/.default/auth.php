<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<script type="text/javascript">
	function ChangeGenerate(val) {
		if(val) {
			document.getElementById("sof_choose_login").style.display='none';
		} else {
			document.getElementById("sof_choose_login").style.display='block';
			document.getElementById("NEW_GENERATE_N").checked = true;
		}
		try{document.order_reg_form.NEW_LOGIN.focus();}catch(e){}
	}
</script>

<table class="sale_order_full">
	<tr>
		<td width="50%" valign="top">
			<table class="sale_order_full_table">
				<form method="post" action="<?=$arParams["PATH_TO_ORDER"]?>" name="order_auth_form">
					<?=bitrix_sessid_post();
					foreach($arResult["POST"] as $key => $value) {?>
						<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
					<?}?>
					<tr>
						<td>
							<?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
								<b><?=GetMessage("STOF_2REG")?></b>
							<?endif;?>
						</td>
					</tr>					
					<tr>
						<td>
							<?=GetMessage("STOF_LOGIN")?> <span class="sof-req">*</span>
							<br />
							<input type="text" name="USER_LOGIN" maxlength="30" size="30" value="<?=$arResult["AUTH"]["USER_LOGIN"]?>"/>
						</td>
					</tr>
					<tr>
						<td>
							<?=GetMessage("STOF_PASSWORD")?> <span class="sof-req">*</span><br />
							<input type="password" name="USER_PASSWORD" maxlength="30" size="30"/>
						</td>
					</tr>
					<tr>
						<td>
							<button type="submit" name="submit" class="btn_buy popdef" value="<?=GetMessage("STOF_NEXT_STEP")?>"><?=GetMessage("STOF_NEXT_STEP")?></button>
							<input type="hidden" name="do_authorize" value="Y">
						</td>
					</tr>
				</form>
				<tr>
					<td>
						<a class="btn_buy apuo forgot" href="<?=$arParams["PATH_TO_AUTH"]?>?forgot_password=yes&back_url=<?=urlencode($arParams["PATH_TO_ORDER"]); ?>"><?=GetMessage("STOF_FORGET_PASSWORD")?></a>
					</td>
				</tr>
				<tr>
					<td>
						<p class="login_as"><?=GetMessage("STOF_LOGIN_AS_USER")?></p>
						<?if($arResult["AUTH_SERVICES"]):?>							
							<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons", 
								array(
									"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
									"SUFFIX"=>"form", 
								), 
								false, 
								array("HIDE_ICONS"=>"Y")
							);?>
							<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
								array(
									"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
									"AUTH_URL" => $arParams["PATH_TO_ORDER"],
									"POST" => $arResult["POST"],
									"POPUP" => "Y",
									"SUFFIX" => "form",
								),
								false,
								array("HIDE_ICONS" => "Y")
							);?>
						<?endif?>														
					</td>
				</tr>
			</table>
			<br /><br />
		</td>
		<td width="50%" valign="top">
			<?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
				<form method="post" action="<?=$arParams["PATH_TO_ORDER"]?>" name="order_reg_form">
					<?=bitrix_sessid_post();					
					foreach($arResult["POST"] as $key => $value) {?>
						<input type="hidden" name="<?=$key?>" value="<?=$value?>" />
					<?}?>
					<table class="sale_order_full_table">
						<tr>
							<td>
								<?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
									<b><?=GetMessage("STOF_2NEW")?></b>
								<?endif;?>
							</td>
						</tr>
						<tr>
							<td>
								<?=GetMessage("STOF_NAME")?> <span class="sof-req">*</span>
								<br />
								<input type="text" name="NEW_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_NAME"]?>"/>
							</td>
						</tr>
						<tr>
							<td>
								<?=GetMessage("STOF_LASTNAME")?> <span class="sof-req">*</span>
								<br />
								<input type="text" name="NEW_LAST_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_LAST_NAME"]?>"/>
							</td>
						</tr>
						<tr>
							<td>
								E-Mail <span class="sof-req">*</span>
								<br />
								<input type="text" name="NEW_EMAIL" size="40" value="<?=$arResult["AUTH"]["NEW_EMAIL"]?>"/>
							</td>
						</tr>
						<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
							<tr>
								<td>
									<input type="radio" id="NEW_GENERATE_N" name="NEW_GENERATE" value="N" OnClick="ChangeGenerate(false)"<?if ($_POST["NEW_GENERATE"] == "N") echo " checked";?>> <label for="NEW_GENERATE_N"><?=GetMessage("STOF_MY_PASSWORD")?></label>
								</td>
							</tr>
						<?endif;?>
						<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
							<tr>
								<td>
									<div id="sof_choose_login">
										<table>
						<?endif;?>
						<tr>
							<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
								<td width="0%"></td>
							<?endif;?>
							<td>
								<?=GetMessage("STOF_LOGIN")?> <span class="sof-req">*</span>
								<br />
								<input type="text" name="NEW_LOGIN" size="30" value="<?=$arResult["AUTH"]["NEW_LOGIN"]?>"/>
							</td>
						</tr>
						<tr>
							<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
								<td width="0%"></td>
							<?endif;?>
							<td>
								<?=GetMessage("STOF_PASSWORD")?> <span class="sof-req">*</span>
								<br />
								<input type="password" name="NEW_PASSWORD" size="30"/>
							</td>
						</tr>
						<tr>
							<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
								<td width="0%"></td>
							<?endif;?>
							<td>
								<?=GetMessage("STOF_RE_PASSWORD")?> <span class="sof-req">*</span>
								<br />
								<input type="password" name="NEW_PASSWORD_CONFIRM" size="30"/>
							</td>
						</tr>
						<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
										</table>
									</div>
								</td>
							</tr>
						<?endif;?>
						<?if($arResult["AUTH"]["new_user_registration_email_confirmation"] != "Y"):?>
							<tr>
								<td>
									<input type="radio" id="NEW_GENERATE_Y" name="NEW_GENERATE" value="Y" OnClick="ChangeGenerate(true)"<?if ($POST["NEW_GENERATE"] != "N") echo " checked";?>> <label for="NEW_GENERATE_Y"><?=GetMessage("STOF_SYS_PASSWORD")?></label>
									<script language="JavaScript">
										ChangeGenerate(<?= (($_POST["NEW_GENERATE"] != "N") ? "true" : "false") ?>);
									</script>
								</td>
							</tr>
						<?endif;?>
						<?if($arResult["AUTH"]["captcha_registration"] == "Y") {?>
							<tr>
								<td>
									<br />
									<b><?=GetMessage("CAPTCHA_REGF_TITLE")?></b>
								</td>
							</tr>
							<tr>
								<td>
									<input type="hidden" name="captcha_sid" value="<?=$arResult["AUTH"]["capCode"]?>">
									<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["AUTH"]["capCode"]?>" width="180" height="40" alt="CAPTCHA">
								</td>
							</tr>
							<tr valign="middle">
								<td>
									<span class="sof-req">*</span><?=GetMessage("CAPTCHA_REGF_PROMT")?>:<br />
									<input type="text" name="captcha_word" size="30" maxlength="50" value=""/>
								</td>
							</tr>
						<?}?>
						<tr>
							<td>
								<button type="submit" name="submit" class="btn_buy popdef" value="<?=GetMessage("STOF_NEXT_STEP")?>"><?=GetMessage("STOF_NEXT_STEP")?></button>
								<input type="hidden" name="do_register" value="Y">
							</td>
						</tr>
					</table>
				</form>
			<?endif;?>
		</td>
	</tr>
</table>
<br /><br />
<?=GetMessage("STOF_REQUIED_FIELDS_NOTE")?>
<br /><br />
<?if($arResult["AUTH"]["new_user_registration"]=="Y"):?>
	<?=GetMessage("STOF_EMAIL_NOTE")?>
	<br /><br />
<?endif;?>
<?=GetMessage("STOF_PRIVATE_NOTES")?>