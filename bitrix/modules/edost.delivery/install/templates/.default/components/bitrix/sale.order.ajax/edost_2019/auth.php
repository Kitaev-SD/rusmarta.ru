<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<script>
function ChangeGenerate(val) {
	if(val) {
		document.getElementById("sof_choose_login").style.display='none';
	}
	else {
		document.getElementById("sof_choose_login").style.display='block';
		document.getElementById("NEW_GENERATE_N").checked = true;
	}

	try { document.order_reg_form.NEW_LOGIN.focus(); } catch(e) {}
}
</script>

<?
	$old = GetMessage('STOF_2REG');
	$new = GetMessage('STOF_2NEW');
?>

<div id="order_auth" style="text-align: center;">
	<div id="edost_template_width_div"></div>

	<div id="order_auth_old_div">
	<div class="edost edost_main edost_template_div">
<?		if ($arResult['AUTH']['new_user_registration'] == 'Y') echo '<h4 class="edost_compact_head" style="text-align: center;">'.$old[0].'</h4><h4 class="edost_compact_head2" style="text-align: center;">'.$old[1].'</h4>'; ?>

		<div class="edost_div" style="padding: 0 20px 20px 20px;">
		<form id="order_auth_old" style="padding: 0;" method="post" action="" name="order_auth_form">
			<?=bitrix_sessid_post()?>
<?			foreach ($arResult["POST"] as $key => $value) echo '<input type="hidden" name="'.$key.'" value="'.$value.'">'; ?>

			<div class="edost_prop_div">
				<div class="edost_prop_head"><?=GetMessage("STOF_LOGIN")?></div>
				<div class="edost_prop"><input type="text" name="USER_LOGIN" maxlength="30" value="<?=$arResult["AUTH"]["USER_LOGIN"]?>"></div>
			</div>

			<div class="edost_prop_div">
				<div class="edost_prop_head"><?=GetMessage("STOF_PASSWORD")?></div>
				<div class="edost_prop"><input type="password" name="USER_PASSWORD" maxlength="30"></div>
			</div>

			<div><a href="<?=$arParams["PATH_TO_AUTH"]?>?forgot_password=yes&back_url=<?= urlencode($APPLICATION->GetCurPageParam()); ?>"><?echo GetMessage("STOF_FORGET_PASSWORD")?></a></div>

			<div style="padding-top: 15px; text-align: center;">
				<div class="edost_button_big" style="display: inline-block; padding: 10px; min-width: 120px;" onclick="BX('order_auth_old').submit()"><span style="font-size: 20px; line-height: 20px;"><?=GetMessage('STOF_OLD')?></span></div>
				<input type="hidden" name="do_authorize" value="Y">
			</div>
		</form>
		</div>
	</div>
	</div>


<?	if ($arResult['AUTH']['new_user_registration'] == 'Y') {
		$no_email_confirmation = ($arResult['AUTH']['new_user_registration_email_confirmation'] != 'Y' ? true : false); ?>
	<div id="order_auth_new_div">
	<div class="edost edost_main edost_template_div">
<?		if ($arResult['AUTH']['new_user_registration'] == 'Y') echo '<h4 class="edost_compact_head" style="text-align: center;">'.$new[0].'</h4><h4 class="edost_compact_head2" style="text-align: center;">'.$new[1].'</h4>'; ?>
		<div class="edost_div" style="padding: 0 20px 20px 20px;">
		<form id="order_auth_new" method="post" action="" name="order_reg_form">
			<?=bitrix_sessid_post()?>
<?			foreach ($arResult["POST"] as $key => $value) echo '<input type="hidden" name="'.$key.'" value="'.$value.'">'; ?>

			<div class="edost_prop_div">
				<div class="edost_prop_head"><?echo GetMessage("STOF_NAME")?></div>
				<div class="edost_prop"><input type="text" name="NEW_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_NAME"]?>"></div>
			</div>
			<div class="edost_prop_div">
				<div class="edost_prop_head"><?echo GetMessage("STOF_LASTNAME")?></div>
				<div class="edost_prop"><input type="text" name="NEW_LAST_NAME" size="40" value="<?=$arResult["AUTH"]["NEW_LAST_NAME"]?>"></div>
			</div>
			<div class="edost_prop_div">
				<div class="edost_prop_head">E-Mail</div>
				<div class="edost_prop"><input type="text" name="NEW_EMAIL" size="40" value="<?=$arResult["AUTH"]["NEW_EMAIL"]?>"></div>
			</div>

<?			if ($no_email_confirmation) { ?>
			<input type="radio" id="NEW_GENERATE_Y" name="NEW_GENERATE" value="Y" OnClick="ChangeGenerate(true)"<?if ($POST["NEW_GENERATE"] != "N") echo " checked";?>> <label for="NEW_GENERATE_Y"><?echo GetMessage("STOF_SYS_PASSWORD")?></label> <br>
			<input type="radio" id="NEW_GENERATE_N" name="NEW_GENERATE" value="N" OnClick="ChangeGenerate(false)"<?if ($_POST["NEW_GENERATE"] == "N") echo " checked";?>> <label for="NEW_GENERATE_N"><?echo GetMessage("STOF_MY_PASSWORD")?></label>
			<div id="sof_choose_login">
<?			} ?>

			<div class="edost_prop_div">
				<div class="edost_prop_head"><?=GetMessage("STOF_LOGIN")?></div>
				<div class="edost_prop"><input type="text" name="NEW_LOGIN" size="30" value="<?=$arResult["AUTH"]["NEW_LOGIN"]?>"></div>
			</div>
			<div class="edost_prop_div">
				<div class="edost_prop_head"><?=GetMessage("STOF_PASSWORD")?></div>
				<div class="edost_prop"><input type="password" name="NEW_PASSWORD" size="30"></div>
			</div>
			<div class="edost_prop_div">
				<div class="edost_prop_head"><?=GetMessage("STOF_RE_PASSWORD")?></div>
				<div class="edost_prop"><input type="password" name="NEW_PASSWORD_CONFIRM" size="30"></div>
			</div>

<?			if ($no_email_confirmation) { ?>
			</div>
			<script language="JavaScript">
				ChangeGenerate(<?=($_POST["NEW_GENERATE"] != "N" ? "true" : "false")?>);
			</script>
<?			}
			if ($arResult['AUTH']['captcha_registration'] == 'Y') { ?>
					<div style="color: #888; font-size: 16px; text-align: center;">
						<div style="padding: 15px 0 5px 0;"><?=GetMessage("CAPTCHA_REGF_TITLE")?></div>
						<input type="hidden" name="captcha_sid" value="<?=$arResult["AUTH"]["capCode"]?>">
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["AUTH"]["capCode"]?>" width="180" height="40" alt="CAPTCHA">
					</div>
			<div class="edost_prop_div">
				<div class="edost_prop_head"><?=GetMessage("CAPTCHA_REGF_PROMT")?></div>
				<div class="edost_prop"><input type="text" name="captcha_word" size="30" maxlength="50" value=""></div>
			</div>
<?			} ?>

			<?=$agreement_label?>

			<div style="padding-top: 15px; text-align: center;">
				<div class="edost_button_big" style="display: inline-block; padding: 10px; min-width: 220px;" onclick="if (window.edost_window.props()) BX('order_auth_new').submit()"><span style="font-size: 20px; line-height: 20px;"><?=GetMessage('STOF_NEW')?></span></div>
				<input type="hidden" name="do_register" value="Y">
			</div>
		</form>
		</div>
	</div>
	</div>
<?	} ?>

</div>


<div style="padding-top: 20px;">
<?	if ($arResult['AUTH']['new_user_registration'] == 'Y') echo GetMessage('STOF_EMAIL_NOTE').'<br><br>'; ?>
	<?=GetMessage('STOF_PRIVATE_NOTES')?>
</div>

<input id="edost_template_2019" name="edost_template_2019" data-ico="" data-compact="" data-priority="" value="" type="hidden">
<script type="text/javascript">
	if (window.edost_resize) edost_resize.start();
</script>
