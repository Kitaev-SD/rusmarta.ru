<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="content-form changepswd-form">
	<div class="fields">
		<?ShowMessage($arParams["~AUTH_RESULT"]);?>
		<form method="post" action="<?=$arResult["AUTH_FORM"]?>" name="bform">
			<?if(strlen($arResult["BACKURL"]) > 0):?>
				<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
			<?endif?>
			<input type="hidden" name="AUTH_FORM" value="Y">
			<input type="hidden" name="TYPE" value="CHANGE_PWD">
			<div class="field">
				<label class="field-title"><?=GetMessage("AUTH_LOGIN")?><span class="starrequired">*</span></label>
				<div class="form-input">
					<input type="text" name="USER_LOGIN" maxlength="50" value="<?=$arResult["LAST_LOGIN"]?>" />
				</div>
			</div>
			<div class="field">
				<label class="field-title"><?=GetMessage("AUTH_CHECKWORD")?><span class="starrequired">*</span></label>
				<div class="form-input">
					<input type="text" name="USER_CHECKWORD" maxlength="50" value="<?=$arResult["USER_CHECKWORD"]?>" />
				</div>
			</div>
			<div class="field">
				<label class="field-title"><?=GetMessage("AUTH_NEW_PASSWORD_REQ")?><span class="starrequired">*</span></label>
				<div class="form-input">
					<input type="password" name="USER_PASSWORD" maxlength="50" value="<?=$arResult["USER_PASSWORD"]?>" />
				</div>
				<div class="description">&mdash; <?echo $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"];?></div>
			</div>
			<div class="field">
				<label class="field-title"><?=GetMessage("AUTH_NEW_PASSWORD_CONFIRM")?><span class="starrequired">*</span></label>
				<div class="form-input">
					<input type="password" name="USER_CONFIRM_PASSWORD" maxlength="50" value="<?=$arResult["USER_CONFIRM_PASSWORD"]?>"  />
				</div>
			</div>
			<?if($arResult["USE_CAPTCHA"]):?>
				<div class="field">
					<label class="field-title"><?=GetMessage("AUTH_CAPTCHA")?><span class="starrequired">*</span></label>
					<div class="form-input">
						<input type="text" name="captcha_word" maxlength="50" value="" />
						<input type="hidden" name="captcha_sid" value="<?=$arResult["CAPTCHA_CODE"]?>" />
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult["CAPTCHA_CODE"]?>" width="127" height="30" alt="CAPTCHA" />
						<div class="clr"></div>
					</div>
				</div>
			<?endif;?>
			<div class="field field-button">
				<button type="submit" name="change_pwd" class="btn_buy popdef" value="<?=GetMessage("AUTH_CHANGE")?>"><?=GetMessage("AUTH_CHANGE")?></button>	
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