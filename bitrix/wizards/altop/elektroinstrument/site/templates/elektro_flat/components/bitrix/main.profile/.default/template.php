<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?=ShowError($arResult["strProfileError"]);?>
<?if($arResult['DATA_SAVED'] == 'Y')
	echo ShowNote(GetMessage('PROFILE_DATA_SAVED'));?>

<div class="workarea personal">
	<form method="post" name="form1" action="<?=$arResult["FORM_TARGET"]?>" enctype="multipart/form-data">
		<?=$arResult["BX_SESSION_CHECK"]?>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="hidden" name="ID" value=<?=$arResult["ID"]?> />
		<input type="hidden" name="LOGIN" value=<?=$arResult["arUser"]["LOGIN"]?> />
		<input type="hidden" name="EMAIL" value=<?=$arResult["arUser"]["EMAIL"]?> />

		<h2><?=GetMessage("LEGEND_PROFILE")?></h2>
		<div class="personal-info">
			<div class="personal-info_in">
				<?=GetMessage('NAME')?><br>
				<input type="text" name="NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["NAME"]?>" />
				<br><br>
				
				<?=GetMessage('LAST_NAME')?><br>
				<input type="text" name="LAST_NAME" maxlength="50" class="input_text_style" value="<?=$arResult["arUser"]["LAST_NAME"]?>" />
				<br><br>

				<?=GetMessage('PERSONAL_PHOTO')?><br>
				<?if(empty($arResult["arUser"]["PERSONAL_PHOTO"])):?>
					<input type="file" name="PERSONAL_PHOTO" size="20" class="typefile" />
				<?else:?>
					<table border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="middle" style="padding:0px 10px 0px 0px;">
								<img src="<?=$arResult["arUser"]["PERSONAL_IMG"]["SRC"]?>" width="<?=$arResult["arUser"]["PERSONAL_IMG"]["WIDTH"]?>" height="<?=$arResult["arUser"]["PERSONAL_IMG"]["HEIGHT"]?>" />
							</td>
							<td valign="middle">
								<input type="file" name="PERSONAL_PHOTO" size="20" class="typefile" />
							</td>
						</tr>
					</table>
				<?endif;?>
			</div>
		</div>

		<h2><?=GetMessage("MAIN_PSWD")?></h2>
		<div class="personal-info">
			<div class="personal-info_in">
				<?=GetMessage('NEW_PASSWORD_REQ')?><br>
				<input type="password" name="NEW_PASSWORD" maxlength="50" class="input_text_style" value="" autocomplete="off" />
				<br><br>

				<?=GetMessage('NEW_PASSWORD_CONFIRM')?><br>
				<input type="password" name="NEW_PASSWORD_CONFIRM" maxlength="50" class="input_text_style" value="" autocomplete="off" />
			</div>
		</div>

		<button type="submit" name="save" class="btn_buy popdef bt3" value="<?=GetMessage('MAIN_SAVE')?>"><?=GetMessage("MAIN_SAVE")?></button>
	</form>
</div>
<div class="clr"></div>
<br />
<?if($arResult["SOCSERV_ENABLED"]) {
	$APPLICATION->IncludeComponent("bitrix:socserv.auth.split", ".default", 
		array(
			"SHOW_PROFILES" => "Y",
			"ALLOW_DELETE" => "Y"
		),
		false
	);
}?>