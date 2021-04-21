<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="content-form confirm-form">
	<div class="fields">
		<div class="field"><?echo $arResult["MESSAGE_TEXT"]?></div>
		<?switch($arResult["MESSAGE_CODE"]) {
			case "E01":
				break;
			case "E02":
				break;
			case "E03":
				break;
			case "E04":
				break;
			case "E05":
				break;
			case "E06":
				break;
			case "E07":
				break;
		}?>
		<?if($arResult["SHOW_FORM"]):?>
			<form method="post" action="<?echo $arResult["FORM_ACTION"]?>">
				<div class="field">
					<label class="field-title"><?echo GetMessage("CT_BSAC_LOGIN")?></label>
					<div class="form-input">
						<input type="text" name="<?echo $arParams["LOGIN"]?>" maxlength="50" value="<?echo (strlen($arResult["LOGIN"]) > 0? $arResult["LOGIN"]: $arResult["USER"]["LOGIN"])?>" size="17" />
					</div>
				</div>
				<div class="field">
					<label class="field-title"><?echo GetMessage("CT_BSAC_CONFIRM_CODE")?></label>
					<div class="form-input">
						<input type="text" name="<?echo $arParams["CONFIRM_CODE"]?>" maxlength="50" value="<?echo $arResult["CONFIRM_CODE"]?>" size="17" />
					</div>
				</div>
				<div class="field field-button">
					<button type="submit" name="submit" class="btn_buy popdef" value="<?=GetMessage("CT_BSAC_CONFIRM")?>"><?=GetMessage("CT_BSAC_CONFIRM")?></button>	
				</div>
				<input type="hidden" name="<?echo $arParams["USER_ID"]?>" value="<?echo $arResult["USER_ID"]?>" />
			</form>
		<?elseif(!$USER->IsAuthorized()):?>
			<?$APPLICATION->IncludeComponent("bitrix:system.auth.authorize", "", array());?>
		<?endif?>
	</div>
</div>