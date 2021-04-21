<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

if(!empty($arResult["errorMessage"])) {
	if(!is_array($arResult["errorMessage"])) {
		ShowError($arResult["errorMessage"]);
	} else {
		foreach($arResult["errorMessage"] as $errorMessage) {
			ShowError($errorMessage);
		}
	}
} else {
	if($arParams['REFRESHED_COMPONENT_MODE'] === 'Y') {
		$wrapperId = str_shuffle(substr($arResult['SIGNED_PARAMS'],0,10));?>
		<div class="bx-sap" id="bx-sap<?=$wrapperId?>">						
			<?if($arParams['SELL_VALUES_FROM_VAR'] != 'Y') {
				if($arParams['SELL_SHOW_FIXED_VALUES'] === 'Y') {?>						
					<div class="sale-acountpay-block">
						<div class="sale-acountpay-title"><?=Loc::getMessage("SAP_FIXED_PAYMENT")?></div>
						<div class="sale-acountpay-fixedpay-container">
							<div class="sale-acountpay-fixedpay-list">
								<?foreach ($arParams["SELL_TOTAL"] as $valueChanging) {?>
									<div class="sale-acountpay-fixedpay-item">
										<?=CUtil::JSEscape(htmlspecialcharsbx($valueChanging))?>
									</div>
								<?}?>
							</div>
							<div class="clr"></div>
						</div>
					</div>						
				<?}?>				
				<div class="sale-acountpay-block">
					<div class="sale-acountpay-title"><?=Loc::getMessage("SAP_SUM")?></div>
					<div class="sale-acountpay-form">						
						<?$inputElement = "<input type='text' class='sale-acountpay-input' name='".CUtil::JSEscape(htmlspecialcharsbx($arParams["VAR"]))."' placeholder='0.00' value='0.00'".($arParams["SELL_USER_INPUT"] === "N" ? " disabled" : "")." />";
						$tempCurrencyRow = trim(str_replace("#", "", $arResult["FORMATED_CURRENCY"]));
						$labelWrapper = "<label>".$tempCurrencyRow."</label>";
						$currencyRow = str_replace($tempCurrencyRow, $labelWrapper, $arResult["FORMATED_CURRENCY"]);
						$currencyRow = str_replace("#", $inputElement, $currencyRow);
						echo $currencyRow;?>						
					</div>
				</div>				
			<?} else {			
				if($arParams['SELL_SHOW_RESULT_SUM'] === 'Y') {?>			
					<div class="sale-acountpay-block">
						<div class="sale-acountpay-title"><?=Loc::getMessage("SAP_SUM")?></div>
						<div class="sale-acountpay-sum"><?=SaleFormatCurrency($arResult["SELL_VAR_PRICE_VALUE"], $arParams['SELL_CURRENCY'])?></div>
					</div>									
				<?}?>				
				<input type="hidden" name="<?=CUtil::JSEscape(htmlspecialcharsbx($arParams["VAR"]))?>" value="<?=CUtil::JSEscape(htmlspecialcharsbx($arResult["SELL_VAR_PRICE_VALUE"]))?>" />			
			<?}?>
			<div class="sale-acountpay-block">
				<div class="sale-acountpay-title"><?=Loc::getMessage("SAP_TYPE_PAYMENT_TITLE")?></div>
				<div class="sale-acountpay-pp-container">
					<div class="sale-acountpay-pp">
						<?foreach($arResult['PAYSYSTEMS_LIST'] as $key => $paySystem) {?>							
							<div class="sale-acountpay-pp-company<?=($key == 0 ? ' bx-selected' : '');?>">
								<div class="sale-acountpay-pp-company-graf-container">
									<div class="sale-acountpay-pp-company-graf">
										<div class="sale-acountpay-pp-company-checkbox">
											<input type="checkbox" class="sale-acountpay-pp-company-input" id="paySystemId" name="PAY_SYSTEM_ID" value="<?=$paySystem['ID']?>"<?=($key == 0 ? " checked='checked'" : "");?> />
											<label for="paySystemId"><i class="fa fa-check" aria-hidden="true"></i></label>
										</div>
										<div class="sale-acountpay-pp-company-image">
											<img src="<?=(isset($paySystem['LOGOTIP']) && !empty($paySystem['LOGOTIP']) ? $paySystem['LOGOTIP'] : SITE_TEMPLATE_PATH.'/images/no-photo.jpg');?>" alt="<?=$paySystem['NAME']?>" title="<?=$paySystem['NAME']?>" />
										</div>										
										<?if(!empty($paySystem["DESCRIPTION"])) {?>
											<div class="sale-acountpay-pp-company-descr">
												<label><i class="fa fa-info" aria-hidden="true"></i></label>
												<div class="pop-up pp-descr"><?=$paySystem['DESCRIPTION']?></div>
											</div>
										<?}?>
									</div>
								</div>
								<div class="sale-acountpay-pp-company-smalltitle">
									<?=CUtil::JSEscape(htmlspecialcharsbx($paySystem['NAME']))?>
								</div>
							</div>
						<?}?>
					</div>
				</div>
			</div>
			<button type="button" class="btn_buy popdef sale-acountpay-btn"><?=Loc::getMessage("SAP_BUTTON")?></button>
		</div>
		<?$javascriptParams = array(
			"alertMessages" => array("wrongInput" => Loc::getMessage('SAP_ERROR_INPUT')),
			"url" => CUtil::JSEscape($this->__component->GetPath().'/ajax.php'),
			"templateFolder" => CUtil::JSEscape($templateFolder),
			"signedParams" => $arResult['SIGNED_PARAMS'],
			"wrapperId" => $wrapperId
		);
		$javascriptParams = CUtil::PhpToJSObject($javascriptParams);?>
		<script>
			var sc = new BX.saleAccountPay(<?=$javascriptParams?>);
		</script>
	<?} else {?>		
		<form method="post" name="buyMoney" action="">
			<?foreach($arResult["AMOUNT_TO_SHOW"] as $value) {?>
				<input type="radio" name="<?=CUtil::JSEscape(htmlspecialcharsbx($arParams["VAR"]))?>" value="<?=$value["ID"]?>" id="<?=CUtil::JSEscape(htmlspecialcharsbx($arParams["VAR"])).$value["ID"]?>" />
				<label for="<?=CUtil::JSEscape(htmlspecialcharsbx($arParams["VAR"])).$value["ID"]?>"><?=$value["NAME"]?></label>
				<br />
			<?}?>
			<input type="submit" class="btn_buy popdef sale-acountpay-btn" name="button" value="<?=GetMessage("SAP_BUTTON")?>">
		</form>
	<?}
}?>