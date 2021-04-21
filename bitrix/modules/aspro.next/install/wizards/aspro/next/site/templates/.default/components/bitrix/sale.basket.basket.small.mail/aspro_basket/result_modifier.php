<?
if(!defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED !== true) die();
if(!function_exists("showItems"))
{
	function showItems($arItems, $arHeaders, $allCurrency, $arParams)
	{
		foreach($arItems as $arItem):
			$arItem["PRICE"] = str_replace("-", "", $arItem["PRICE"]);?>
			<tr>
				<?foreach($arHeaders as $name):?>
					<td style="padding:18px 20px;border-bottom: 1px solid #e7e7e7;color:#666;font-size:13px;vertical-align:top;">
						<?if($name == "NAME"):?>
							<?if($arItem["PREVIEW_PICTURE"])
							{
								$img = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array("width" => 75, "height" => 75), BX_RESIZE_PROPORTIONAL_ALT);
								// $src= str_replace(array("//", ":/"), array("/", "://"), $arParams["SITE_ADDRESS"].$img["src"]);
							}?>
							<div class="item" <?=($arItem["PREVIEW_PICTURE"] ? 'style="position:relative; min-height:75px;font-size:0px;"' : '');?>>
								<?if($arItem["PREVIEW_PICTURE"]):?>
										<div class="img" style="display:inline-block;width:75px;text-align:center;height:75px;vertical-align:top;">
											<img src="<?=$img["src"];?>" alt="<?=$arItem[$name];?>" title="<?=$arItem[$name];?>" style="display: inline-block;vertical-align:middle;max-width:100%;max-height:100%;" />
										</div>
										<div style="padding-left:20px;display: inline-block;width: 107px;font-size:13px;vertical-align:top;">
								<?endif;?>
								<a style="color:<?=$arParams["THEME_COLOR"];?>;" href="<?=(str_replace(array("//", ":/"), array("/", "://"), $arParams["SITE_ADDRESS"].$arItem["DETAIL_PAGE_URL"]));?>"><?=$arItem[$name];?></a>

								<?if($arResult["GRID"]["HEADERS"]):?>
									<div style="color:#888;font-size:12px;padding-top:10px;padding-bottom:10px;">
										<?foreach ($arResult["GRID"]["HEADERS"] as $id => $arHeader)
										{
											if(isset($arItem[$arHeader['id']]) && !empty($arItem[$arHeader['id']])):?>
												<div><?=$arHeader['name']?>:&nbsp;<?=$arItem[$arHeader['id']]?></div>
											<?endif;
										}?>
									</div>
								<?endif;?>

								<?if($arItem["PREVIEW_PICTURE"]):?>
									</div>
								<?endif;?>
							</div>
						<?elseif($name == "DISCOUNT"):?>
							<?=CAllCurrencyLang::CurrencyFormat($arItem["PRICE"], $allCurrency);?>
						<?elseif($name == "SUMM"):?>
							<div style="font-weight:600;font-size:14px;color:#333333;"><?=CAllCurrencyLang::CurrencyFormat($arItem["PRICE"]*$arItem["QUANTITY"], $allCurrency);?></div>
						<?else:?>
							<?=$arItem[$name];?>
						<?endif;?>
					</td>
				<?endforeach;?>
			</tr>
		<?endforeach;
	}
}
?>