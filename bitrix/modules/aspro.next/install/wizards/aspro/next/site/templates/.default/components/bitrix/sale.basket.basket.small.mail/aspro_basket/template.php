<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if ($arResult["ShowReady"]=="Y" || $arResult["ShowDelay"]=="Y" || $arResult["ShowSubscribe"]=="Y" || $arResult["ShowNotAvail"]=="Y"):
	$arHeaders = array("NAME", "DISCOUNT", "QUANTITY", "SUMM");
	foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader):
		$arHeader["name"] = (isset($arHeader["name"]) ? (string)$arHeader["name"] : '');
		if ($arHeader["name"] == '')
		{
			$arResult["GRID"]["HEADERS"][$id]["name"] = GetMessage("SALE_".$arHeader["id"]);
			if(strlen($arResult["GRID"]["HEADERS"][$id]["name"])==0)
				$arResult["GRID"]["HEADERS"][$id]["name"] = GetMessage("SALE_".str_replace("_FORMATED", "", $arHeader["id"]));
		}
		if(in_array(str_replace("_FORMATED", "", $arHeader["id"]), $arHeaders))
			unset($arResult["GRID"]["HEADERS"][$id]);
	endforeach;
// echo "<pre>"; print_R($arResult); echo "</pre>";
	$title = ($arParams["TITLE"] ? $arParams["TITLE"] : GetMessage("FORGOT_BASKET") );
	$allCurrency = CSaleLang::GetLangCurrency($arParams["SITE_ID"]);
	$summ = 0;
	if($arResult["ITEMS"]["AnDelCanBuy"] || $arResult["ITEMS"]["DelDelCanBuy"] || $arResult["ITEMS"]["nAnCanBuy"] || $arResult["ITEMS"]["ProdSubscribe"]):?>
		<div class="wrapper_block" style="padding:0px;">
			<div style="color:#1d2029;font-size:18px;padding: 22px 0px 18px;font-weight:600;"><?=$title;?></div>
			<div style="border: 1px solid #e7e7e7;border-bottom-width:0px;">
				<table class="sale_basket_small" style="margin: 0;padding: 0;min-width: 100%;border-collapse: collapse;">
					<thead>
						<tr style="background:#f7f7f7;">
							<?foreach($arHeaders as $name):?>
								<td style="white-space:nowrap;border-bottom: 1px solid #e7e7e7;padding: 7px 9px 8px 21px;font-size: 12px;line-height: 20px;background: none;color: #888;<?=($name == "NAME" ? 'width:45%' : '');?>"><?=GetMessage("SALE_HEADER_".$name);?></td>
							<?endforeach;?>
						</tr>
					</thead>
					<tbody>
						<?if($arResult["ShowReady"]=="Y" && $arResult["ITEMS"]["AnDelCanBuy"]):?>
							<?=showItems($arResult["ITEMS"]["AnDelCanBuy"], $arHeaders, $allCurrency, $arParams);?>
						<?endif;?>
						<?if ($arResult["ShowDelay"]=="Y" && $arResult["ITEMS"]["DelDelCanBuy"]):?>
							<tr><td colspan="4" style="padding:13px 20px 0px;font-size: 14px;font-weight:600;"><?= GetMessage("TSBS_DELAY") ?></td></tr>
							<?=showItems($arResult["ITEMS"]["DelDelCanBuy"], $arHeaders, $allCurrency, $arParams);?>
						<?endif;?>
						<?if ($arResult["ShowSubscribe"]=="Y" && $arResult["ITEMS"]["ProdSubscribe"]):?>
							<tr><td colspan="4" style="padding:13px 20px 0px;font-size: 14px;font-weight:600;"><?= GetMessage("TSBS_SUBSCRIBE") ?></td></tr>
							<?=showItems($arResult["ITEMS"]["ProdSubscribe"], $arHeaders, $allCurrency, $arParams);?>
						<?endif;?>
						<?if ($arResult["ShowNotAvail"]=="Y" && $arResult["ITEMS"]["nAnCanBuy"]):?>
							<tr><td colspan="4" style="padding:13px 20px 0px;font-size: 14px;font-weight:600;"><?= GetMessage("TSBS_UNAVAIL") ?></td></tr>
							<?=showItems($arResult["ITEMS"]["nAnCanBuy"], $arHeaders, $allCurrency, $arParams);?>
						<?endif;?>
					</tbody>
				</table>
			</div>
			<?foreach($arResult["ITEMS"] as $key => $arItemsGroup)
			{
				foreach($arItemsGroup as $key => $arItem)
				{
					$arItem["PRICE"] = str_replace("-", "", $arItem["PRICE"]);
					if($arItem["PRICE"])
						$summ += $arItem["PRICE"];
				}
			}?>
			<div style="text-align:center;">
				<div style="font-weight:600;color:#333333;font-size:16px;padding: 27px 0px 17px;"><?= GetMessage("SALE_TOTAL") ?> <?=CAllCurrencyLang::CurrencyFormat($summ, $allCurrency);?></div>
				<?if('' != $arParams["PATH_TO_BASKET"]):?>
					<a href="<?=$arParams["PATH_TO_BASKET"]?>" style="color:#fff;background:<?=$arParams["THEME_COLOR"];?>;padding:11px 19px;border-radius:3px;display: inline-block;">
						<?=($arParams["BASKET_PAGE"] ? $arParams["BASKET_PAGE"] : GetMessage("TSBS_2BASKET"));?>						
					</a>
				<?endif;?>
			</div>
		</div>
	<?endif;
endif;?>