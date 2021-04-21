<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="order-cancel">	
	<?if(strlen($arResult["ERROR_MESSAGE"]) <= 0) {?>
		<p><?=str_replace("#URL_TO#", $arResult["URL_TO_DETAIL"], str_replace("#ID#", $arResult["ACCOUNT_NUMBER"], GetMessage("SALE_CANCEL_ORDER")));?> <b><?=GetMessage("SALE_CANCEL_ORDER3")?></b></p>
		<p><?=GetMessage("SALE_CANCEL_ORDER4")?>:</p>
		<form method="post" action="<?=POST_FORM_ACTION_URI?>">
			<?=bitrix_sessid_post()?>
			<input type="hidden" name="ID" value="<?=$arResult["ID"]?>" />			
			<textarea name="REASON_CANCELED" rows="3" cols="30"></textarea>			
			<input type="hidden" name="CANCEL" value="Y" />			
			<button type="submit" name="action" class="btn_buy popdef bt3" value="<?=GetMessage('SALE_CANCEL_ORDER_BTN')?>"><?=GetMessage("SALE_CANCEL_ORDER_BTN")?></button>
			<div class="clr"></div>
		</form>
	<?} else {
		echo ShowError($arResult["ERROR_MESSAGE"]);
	}?>
</div>