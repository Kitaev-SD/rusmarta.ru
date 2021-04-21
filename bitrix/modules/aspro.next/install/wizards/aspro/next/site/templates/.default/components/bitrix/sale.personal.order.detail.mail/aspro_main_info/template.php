<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?use \Bitrix\Main\Localization\Loc;?>

<?if($arParams["SHOW_ORDER_BASE"]=='Y' && (strlen($arResult["ERROR_MESSAGE"]) || $arResult["ACCOUNT_NUMBER"])):?>
	<div class="border_wrapper" style="color:#888888;font-size:0px;border:1px solid #e7e7e7;padding: 19px 28px 17px;margin-top:10px;margin-bottom:10px;">
		<?if(strlen($arResult["ERROR_MESSAGE"])):?>
			<div style="font-size:14px;color:#c00;"><?=ShowError($arResult["ERROR_MESSAGE"]);?></div>
		<?else:?>	
			<?$allCurrency = CSaleLang::GetLangCurrency($arParams["SITE_ID"]);?>
			<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;">
				<div><?=Loc::getMessage('SPOD_ORDER_NUMBER');?></div>
				<div style="font-size:18px;color:#1d2029;font-weight:600;padding-top:5px;">¹<?=$arResult["ACCOUNT_NUMBER"]?></div>
			</div>
			<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;">
				<div><?=Loc::getMessage('SPOD_ORDER_DATA');?></div>
				<div style="font-size:18px;color:#1d2029;font-weight:600;padding-top:5px;"><?=$arResult["DATE_INSERT_FORMATED"]?></div>
			</div>
			<div style="display:inline-block;vertical-align:top;font-size:14px;width:33.33%;">
				<div><?=Loc::getMessage('SPOD_ORDER_SUMM');?></div>
				<div style="font-size:18px;color:#1d2029;font-weight:600;padding-top:5px;"><?=CAllCurrencyLang::CurrencyFormat($arResult["PRICE"], $allCurrency);?></div>
			</div>
		<?endif;?>
	</div>
<?endif;?>