<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<h2><?=GetMessage("SOA_TEMPL_PAY_SYSTEM")?></h2>
<div class="order-info">
	<div class="order-info_in">
		<table>
			<?if($arResult["PAY_FROM_ACCOUNT"]=="Y") {?>
				<tr>
					<td valign="top">
						<input type="hidden" name="PAY_CURRENT_ACCOUNT" value="N">
						<input type="checkbox" name="PAY_CURRENT_ACCOUNT" id="PAY_CURRENT_ACCOUNT" value="Y"<?if($arResult["USER_VALS"]["PAY_CURRENT_ACCOUNT"]=="Y") echo " checked=\"checked\"";?> onChange="submitForm()">
					</td>
					<td valign="top">
						<div class="name">
							<?=GetMessage("SOA_TEMPL_PAY_ACCOUNT")?>
						</div>
						<p>
							<span><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT1")." <b>".$arResult["CURRENT_BUDGET_FORMATED"].($arSetting["REFERENCE_PRICE"]["VALUE"] == "Y" && !empty($arSetting["REFERENCE_PRICE_COEF"]["VALUE"]) ? " (".CCurrencyLang::CurrencyFormat($arResult["USER_ACCOUNT"]["CURRENT_BUDGET"] * $arSetting["REFERENCE_PRICE_COEF"]["VALUE"], $arResult["USER_ACCOUNT"]["CURRENCY"], true).")" : "");?></b></span>
							<br />
							<?if($arParams["ONLY_FULL_PAY_FROM_ACCOUNT"] == "Y"):?>
								<span><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT3")?></span>
							<?else:?>
								<span><?=GetMessage("SOA_TEMPL_PAY_ACCOUNT2")?></span>
							<?endif;?>
						</p>
					</td>
				</tr>
			<?}?>
			<?foreach($arResult["PAY_SYSTEM"] as $arPaySystem) {
				if(count($arResult["PAY_SYSTEM"]) == 1) {?>
					<tr>
						<td valign="top">
							<?if(!empty($arPaySystem["PSA_LOGOTIP"]["SRC"])):?>
								<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" width="<?=$arPaySystem["PSA_LOGOTIP"]["WIDTH"]?>" height="<?=$arPaySystem["PSA_LOGOTIP"]["HEIGHT"]?>" />
							<?endif;?>
						</td>
						<td valign="top">
							<input type="hidden" name="PAY_SYSTEM_ID" value="<?=$arPaySystem["ID"]?>">
							<?if($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
								<div class="name">									
									<?=$arPaySystem["PSA_NAME"];?>
								</div>
							<?endif;
							if(strlen($arPaySystem["DESCRIPTION"]) > 0):?>
								<p><?=$arPaySystem["DESCRIPTION"]?></p>
							<?endif;?>
						</td>
					</tr>
				<? } else { ?>
					<tr>
						<td valign="top">
							<input type="radio" id="ID_PAY_SYSTEM_ID_<?= $arPaySystem["ID"] ?>" name="PAY_SYSTEM_ID" value="<?= $arPaySystem["ID"] ?>"<?if ($arPaySystem["CHECKED"]=="Y") echo " checked=\"checked\"";?> onclick="submitForm();">
						</td>
						<td valign="top">
							<label for="ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>" onclick="BX('ID_PAY_SYSTEM_ID_<?=$arPaySystem["ID"]?>').checked=true;submitForm();">
								<table>
									<tr>
										<td valign="top">
											<?if(!empty($arPaySystem["PSA_LOGOTIP"]["SRC"])):?>
												<img src="<?=$arPaySystem["PSA_LOGOTIP"]["SRC"]?>" width="<?=$arPaySystem["PSA_LOGOTIP"]["WIDTH"]?>" height="<?=$arPaySystem["PSA_LOGOTIP"]["HEIGHT"]?>" />
											<?endif;?>
										</td>
										<td valign="top">
											<?if($arParams["SHOW_PAYMENT_SERVICES_NAMES"] != "N"):?>
												<div class="name">
													<?=$arPaySystem["PSA_NAME"]?>
												</div>
											<?endif;
											if(strlen($arPaySystem["DESCRIPTION"]) > 0):?>
												<p><?=$arPaySystem["DESCRIPTION"]?></p>
											<?endif;?>
										</td>
									</tr>
								</table>
							</label>
						</td>
					</tr>
				<? }
			}?>
		</table>
	</div>
</div>