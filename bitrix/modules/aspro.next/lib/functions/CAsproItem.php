<?
namespace Aspro\Functions;

use Bitrix\Main\Application;
use Bitrix\Main\Web\DOM\Document;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\DOM\CssParser;
use Bitrix\Main\Text\HtmlFilter;
use Bitrix\Main\IO\File;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule('catalog');

if(!defined('FUNCTION_MODULE_ID'))
	define('FUNCTION_MODULE_ID', 'aspro.next');

if(!class_exists("CAsproItem"))
{
	class CAsproItem{

		public static function getCurrentPrice($price_field, $arPrice, $bFromPrice = true){
			$val = '';
			if($bFromPrice)
			{
				$format_value = \CCurrencyLang::CurrencyFormat($arPrice[$price_field], $arPrice['CURRENCY'], false);
				if(isset($arPrice["PRINT_".$price_field])){
					if(strpos($arPrice["PRINT_".$price_field], $format_value) !== false):
						$val = str_replace($format_value, '<span class="price_value">'.$format_value.'</span><span class="price_currency">', $arPrice["PRINT_".$price_field].'</span>');
					else:
						$val = $arPrice["PRINT_".$price_field];
					endif;
				}
				else{
					$val = '<span class="price_value">'.\CCurrencyLang::CurrencyFormat($arPrice[$price_field], $arPrice['CURRENCY']).'</span><span class="price_currency">';
				}
			}
			else
			{
				$format_value_raw = \CCurrencyLang::CurrencyFormat($price_field, $arPrice['CURRENCY']);
				$format_value = \CCurrencyLang::CurrencyFormat($price_field, $arPrice['CURRENCY'], false);

				if(strpos($format_value_raw, $format_value) !== false):
					$val = str_replace($format_value, '<span class="price_value">'.$format_value.'</span><span class="price_currency">', $format_value_raw.'</span>');
				else:
					$val = $format_value_raw;
				endif;
			}

			return $val;
		}

		public static function showItemPrices($arParams = array(), $arPrices = array(), $strMeasure = '', &$price_id = 0, $bShort = 'N'){
			$price_id = 0;
			if((is_array($arParams) && $arParams)&& (is_array($arPrices) && $arPrices))
			{
				ob_start();

				$sDiscountPrices = \Bitrix\Main\Config\Option::get(FUNCTION_MODULE_ID, 'DISCOUNT_PRICE');
				$arDiscountPrices = array();
				if($sDiscountPrices)
					$arDiscountPrices = array_flip(explode(',', $sDiscountPrices));

				$iCountPriceGroup = 0;
				foreach($arPrices as $key => $arPrice)
				{
					if($arPrice['CAN_ACCESS'])
						$iCountPriceGroup++;
				}
				foreach($arPrices as $key => $arPrice){?>
					<?if($arPrice["CAN_ACCESS"]){
						if($arPrice["MIN_PRICE"] == "Y"){
							$price_id = $arPrice["PRICE_ID"];
						}?>
						<?$arPriceGroup = \CCatalogGroup::GetByID($arPrice["PRICE_ID"]);?>
						<?if($iCountPriceGroup > 1):?>
							<div class="price_group <?=$arPriceGroup['XML_ID']?>"><div class="price_name"><?=$arPriceGroup["NAME_LANG"]?></div>
						<?endif;?>
						<div class="price_matrix_wrapper <?=($arDiscountPrices ? (isset($arDiscountPrices[$arPriceGroup['ID']]) ? 'strike_block' : '') : '');?>">
							<?if($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"]){?>
								<div class="price"  data-currency="<?=$arPrice["CURRENCY"];?>" data-value="<?=$arPrice["DISCOUNT_VALUE"];?>" <?=($bMinPrice ? ' itemprop="offers" itemscope itemtype="http://schema.org/Offer"' : '')?>>
									<?if($bMinPrice):?>
										<meta itemprop="price" content="<?=($arPrice['DISCOUNT_VALUE'] ? $arPrice['DISCOUNT_VALUE'] : $arPrice['VALUE'])?>" />
										<meta itemprop="priceCurrency" content="<?=$arPrice['CURRENCY']?>" />
										<link itemprop="availability" href="http://schema.org/<?=($arPrice['CAN_BUY'] ? 'InStock' : 'OutOfStock')?>" />
									<?endif;?>
									<?if(strlen($arPrice["PRINT_DISCOUNT_VALUE"])):?>
										<span class="values_wrapper">
											<?=self::getCurrentPrice("DISCOUNT_VALUE", $arPrice);?>
										</span><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?><span class="price_measure">/<?=$strMeasure?></span><?endif;?>
									<?endif;?>
								</div>
								<?if($arParams["SHOW_OLD_PRICE"]=="Y"):?>
									<div class="price discount" data-currency="<?=$arPrice["CURRENCY"];?>" data-value="<?=$arPrice["VALUE"];?>">
										<span class="values_wrapper"><?=self::getCurrentPrice("VALUE", $arPrice);?></span>
									</div>
								<?endif;?>
								<?if($arParams["SHOW_DISCOUNT_PERCENT"]=="Y"){?>
									<div class="sale_block">
										<div class="sale_wrapper">
											<?if($bShort == 'Y'):?>
												<span class="title"><?=GetMessage("CATALOG_ECONOMY");?></span>
												<div class="text"><span class="values_wrapper"><?=self::getCurrentPrice("DISCOUNT_DIFF", $arPrice);?></span></div>
											<?else:?>
												<?
												if(isset($arPrice["DISCOUNT_DIFF_PERCENT"]))
													$percent = $arPrice["DISCOUNT_DIFF_PERCENT"];
												else
													$percent=round(($arPrice["DISCOUNT_DIFF"]/$arPrice["VALUE"])*100, 2);?>
												<?if($percent && $percent<100){?>
													<div class="value">-<span><?=$percent;?></span>%</div>
												<?}?>
												<div class="text"><?=GetMessage("CATALOG_ECONOMY");?> <span class="values_wrapper"><?=self::getCurrentPrice("DISCOUNT_DIFF", $arPrice);?></span></div>
											<?endif;?>
											<div class="clearfix"></div>
										</div>
									</div>
								<?}?>
							<?}else{?>
								<div class="price" data-currency="<?=$arPrice["CURRENCY"];?>" data-value="<?=$arPrice["VALUE"];?>">
									<?if(strlen($arPrice["PRINT_VALUE"])):?>
										<span class="values_wrapper"><?=self::getCurrentPrice("VALUE", $arPrice);?></span><?if (($arParams["SHOW_MEASURE"]=="Y") && $strMeasure):?><span class="price_measure">/<?=$strMeasure?></span><?endif;?>
									<?endif;?>
								</div>
							<?}?>
						</div>
						<?if($iCountPriceGroup > 1):?>
							</div>
						<?endif;?>
					<?}?>
				<?}

				$html = ob_get_contents();
				ob_end_clean();

				foreach(GetModuleEvents(FUNCTION_MODULE_ID, 'OnAsproItemShowItemPrices', true) as $arEvent) // event for manipulation item prices
					ExecuteModuleEventEx($arEvent, array($arParams, $arPrices, $strMeasure, &$price_id, $bShort, &$html));

				echo $html;
			}
		}
	}
}?>