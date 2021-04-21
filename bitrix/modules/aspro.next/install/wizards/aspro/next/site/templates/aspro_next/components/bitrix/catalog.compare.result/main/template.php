<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$isAjax = ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["ajax_action"]) && $_POST["ajax_action"] == "Y");?>
<div class="bx_compare" id="bx_catalog_compare_block">
<?if ($isAjax){
	$APPLICATION->RestartBuffer();
}?>
<div class="bx_sort_container tabs">
	<!-- noindex -->
	<ul class="tabs-head nav nav-tabs">
		<li <?=(!$arResult["DIFFERENT"] ? 'class="active"' : '');?>>
			<a rel="nofollow" class="sortbutton<? echo (!$arResult["DIFFERENT"] ? ' active' : ''); ?>" data-href="?DIFFERENT=N" rel="nofollow"><?=GetMessage("CATALOG_ALL_CHARACTERISTICS")?></a>
		</li>
		<li <?=($arResult["DIFFERENT"] ? 'class="active"' : '');?>>
			<a rel="nofollow" class="sortbutton diff <? echo ($arResult["DIFFERENT"] ? ' active' : ''); ?>" data-href="?DIFFERENT=Y" rel="nofollow"><?=GetMessage("CATALOG_ONLY_DIFFERENT")?></a>
		</li>
	</ul>
	<!-- noindex -->
	<span class="wrap_remove_button">
		<?$arStr=$arCompareIDs=array();
		if($arResult["ITEMS"])
		{
			foreach($arResult["ITEMS"] as $arItem)
			{
				$arCompareIDs[]=$arItem["ID"];
			}
		}
		$arStr=implode("&ID[]=", $arCompareIDs)?>
		<span class="btn btn-default white grey remove_all_compare icon_close" onclick="CatalogCompareObj.MakeAjaxAction('/catalog/compare.php?action=DELETE_FROM_COMPARE_RESULT&ID[]=<?=$arStr?>', 'Y');"><?=GetMessage("CLEAR_ALL_COMPARE")?></span>
	</span>
</div>
<div class="table_compare wrap_sliders tabs-body">
	<?if($arResult["SHOW_FIELDS"]):?>
		<div class="frame top">
			<div class="wraps">
				<table class="compare_view top">
					<tr>
						<?foreach($arResult["ITEMS"] as &$arElement){?>
							<td>
								<div class="item_block">
									<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arElement['~DELETE_URL'])?>', 'Y');" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
									<?$name = (isset($arElement["OFFER_FIELDS"]["NAME"]) ? $arElement["OFFER_FIELDS"]["NAME"] : $arElement["NAME"]);?>
									<?if($arParams['SKU_DETAIL_ID'] && isset($arElement["OFFER_FIELDS"]["ID"]))
										$arElement["DETAIL_PAGE_URL"] .= '?oid='.$arElement["OFFER_FIELDS"]["ID"];?>
									<div class="image_wrapper_block">
										<?if($arElement["OFFER_FIELDS"]["PREVIEW_PICTURE"]){
												if(is_array($arElement["OFFER_FIELDS"]["PREVIEW_PICTURE"]))
													$img = $arElement["OFFER_FIELDS"]["PREVIEW_PICTURE"];
												else
													$img = CFile::GetFileArray($arElement["OFFER_FIELDS"]["PREVIEW_PICTURE"]);?>
												<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=$img["SRC"]?>" alt="<?=$img["ALT"]?>" title="<?=$img["TITLE"]?>" /></a>
										<?}elseif($arElement["FIELDS"]["PREVIEW_PICTURE"]){
											if(is_array($arElement["FIELDS"]["PREVIEW_PICTURE"])):?>
												<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["SRC"]?>" alt="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["ALT"]?>" title="<?=$arElement["FIELDS"]["PREVIEW_PICTURE"]["TITLE"]?>" /></a>
											<?endif;?>
										<?}elseif($arElement["FIELDS"]["DETAIL_PICTURE"]){
											if(is_array($arElement["FIELDS"]["DETAIL_PICTURE"])):?>
												<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["SRC"]?>" alt="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["ALT"]?>" title="<?=$arElement["FIELDS"]["DETAIL_PICTURE"]["TITLE"]?>" /></a>
											<?endif;
										}else{?>
												<a href="<?=$arElement["DETAIL_PAGE_URL"]?>"><img src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=$arElement["NAME"]?>" title="<?=$arElement["NAME"]?>" /></a>
										<?}?>
									</div>
									<a href="<?=$arElement["DETAIL_PAGE_URL"]?>" class="title"><?=$name;?></a>
									<div class="cost prices clearfix">
										<?
										$frame = $this->createFrame()->begin('');
										$frame->setBrowserStorage(true);
										?>
										<?if (isset($arElement['MIN_PRICE']) && is_array($arElement['MIN_PRICE'])):?>
											<div class="price"><?=(isset($arElement['MIN_PRICE']['SUFFIX']) && $arElement['MIN_PRICE']['SUFFIX'] ? $arElement['MIN_PRICE']['SUFFIX'] : '')?><?=$arElement['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];?></div>
										<?elseif(!empty($arElement['PRICE_MATRIX']) && is_array($arElement['PRICE_MATRIX'])):?>
											<?
											$matrix = $arElement['PRICE_MATRIX'];
											$rows = $matrix['ROWS'];
											$rowsCount = count($rows);
											if($rowsCount > 0):?>
												<div class="price_matrix_block">
													<div class="price_matrix_wrapper">
														<?if (count($rows) > 1):?>
															<?foreach ($rows as $index => $rowData):?>
																<?if (empty($matrix['MIN_PRICES'][$index]))
																	continue;?>
																<div class="price_wrapper_block">
																	<?if($rowData['QUANTITY_FROM'] == 0)
																		$rowData['QUANTITY_FROM'] = '';
																	if($rowData['QUANTITY_TO'] == 0)
																		$rowData['QUANTITY_TO'] = '';
																	?>
																	<div class="price_interval">
																		<?
																		$quantity_from = $rowData['QUANTITY_FROM'];
																		$quantity_to = $rowData['QUANTITY_TO'];
																		$text = ($quantity_to ? ($quantity_from ? $quantity_from.'-'.$quantity_to : '<'.$quantity_to ) : '>'.$quantity_from );
																		?>
																		<?=$text;?><?if($arParams['SHOW_MEASURE'] == 'Y'):?><?=GetMessage('MEASURE_UNIT');?><?endif;?>
																	</div>
																	<div class="price">
																		<span class="values_wrapper">
																			<?
																			$val = '';
																			$format_value = \CCurrencyLang::CurrencyFormat($matrix['MIN_PRICES'][$index]['PRICE'], $matrix['MIN_PRICES'][$index]['CURRENCY']);
																			echo $format_value;
																			?>
																		</span>
																	</div>
																</div>
															<?endforeach;?>
															<?unset($index, $rowData);?>
														<?else:?>
															<?$currentPrice = current($matrix['MIN_PRICES']);
															echo '<div class="price">'.\CCurrencyLang::CurrencyFormat($currentPrice['PRICE'], $currentPrice['CURRENCY']).'</div>';
															unset($currentPrice);?>
														<?endif;?>
													</div>
												</div>
											<?endif;?>
											<?unset($rowsCount, $rows, $matrix);?>
										<?endif;?>
										<?$frame->end();?>
									</div>
								</div>
							</td>
						<?}?>
					</tr>
				</table>
			</div>
		</div>
		<div class="swipeignore compare_wr_inner">
			<div class="wrapp_scrollbar">
				<div class="wr_scrollbar">
					<div class="scrollbar">
						<div class="handle">
							<div class="mousearea"></div>
						</div>
					</div>
				</div>
				<ul class="slider_navigation compare custom_flex">
					<ul class="flex-direction-nav">
						<li class="flex-nav-prev backward"><span class="flex-prev">Previous</span></li>
						<li class="flex-nav-next forward"><span class="flex-next">Next</span></li>
					</ul>
				</ul>
			</div>
		</div>
	<?endif;?>
	<?if($arResult["ALL_FIELDS"] || $arResult["ALL_PROPERTIES"] || $arResult["ALL_OFFER_FIELDS"] || $arResult["ALL_OFFER_PROPERTIES"]):?>
		<div class="swipeignore compare_wr_inner">
		<div class="bx_filtren_container ">
			<ul>
				<?if(!empty($arResult["ALL_FIELDS"])){
					foreach ($arResult["ALL_FIELDS"] as $propCode => $arProp){
						if (!isset($arResult['FIELDS_REQUIRED'][$propCode])){?>
							<li class="btn btn-default white <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
								<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=GetMessage("IBLOCK_FIELD_".$propCode)?></span>
							</li>
						<?}
					}
				}
				if(!empty($arResult["ALL_OFFER_FIELDS"])){
					foreach($arResult["ALL_OFFER_FIELDS"] as $propCode => $arProp){?>
						<li class="btn btn-default white <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
							<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=GetMessage("IBLOCK_FIELD_".$propCode)?></span>
						</li>
					<?}
				}
				if (!empty($arResult["ALL_PROPERTIES"])){
					foreach($arResult["ALL_PROPERTIES"] as $propCode => $arProp){?>
						<li class="btn btn-default white <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
							<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=$arProp["NAME"]?></span>
						</li>
					<?}
				}
				if (!empty($arResult["ALL_OFFER_PROPERTIES"])){
					foreach($arResult["ALL_OFFER_PROPERTIES"] as $propCode => $arProp){?>
						<li class="btn btn-default white <?=($arProp["IS_DELETED"] != "N" ? 'visible' : '');?>">
							<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arProp["ACTION_LINK"])?>')">+<?=$arProp["NAME"]?></span>
						</li>
					<?}
				}?>
			</ul>
		</div>
		</div>
	<?endif;?>

	<?$arUnvisible = array("NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE");?>
	<div class="prop_title_table"></div>

	<?//make conditions array?>
	<?$arShowFileds = $arShowOfferFileds = $arShowProps = $arShowOfferProps = array();?>
	<?if($arResult["SHOW_FIELDS"])
	{
		foreach ($arResult["SHOW_FIELDS"] as $code => $arProp)
		{
			if(!in_array($code, $arUnvisible))
			{
				$showRow = true;
				if(!isset($arResult['FIELDS_REQUIRED'][$code]) || $arResult['DIFFERENT'])
				{
					$arCompare = array();
					foreach($arResult["ITEMS"] as &$arElement)
					{
						$arPropertyValue = $arElement["FIELDS"][$code];
						if(is_array($arPropertyValue))
						{
							sort($arPropertyValue);
							$arPropertyValue = implode(" / ", $arPropertyValue);
						}
						$arCompare[] = $arPropertyValue;
					}
					unset($arElement);
					$showRow = (count(array_unique($arCompare)) > 1);
				}
				if($showRow)
					$arShowFileds[$code] = $arProp;
			}
		}
	}
	if($arResult["SHOW_OFFER_FIELDS"])
	{
		foreach ($arResult["SHOW_OFFER_FIELDS"] as $code => $arProp)
		{
			$showRow = true;
			if ($arResult['DIFFERENT'])
			{
				$arCompare = array();
				foreach($arResult["ITEMS"] as &$arElement)
				{
					$Value = $arElement["OFFER_FIELDS"][$code];
					if(is_array($Value))
					{
						sort($Value);
						$Value = implode(" / ", $Value);
					}
					$arCompare[] = $Value;
				}
				unset($arElement);
				$showRow = (count(array_unique($arCompare)) > 1);
			}
			if ($showRow)
				$arShowOfferFileds[$code] = $arProp;
		}
	}
	if($arResult["SHOW_PROPERTIES"])
	{
		foreach($arResult["SHOW_PROPERTIES"] as $code => $arProperty)
		{
			$showRow = true;
			if($arResult['DIFFERENT'])
			{
				$arCompare = array();
				foreach($arResult["ITEMS"] as &$arElement)
				{
					$arPropertyValue = $arElement["DISPLAY_PROPERTIES"][$code]["VALUE"];
					if(is_array($arPropertyValue))
					{
						sort($arPropertyValue);
						$arPropertyValue = implode(" / ", $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				unset($arElement);
				$showRow = (count(array_unique($arCompare)) > 1);
			}
			if($showRow)
				$arShowProps[$code] = $arProperty;
		}
	}
	if($arResult["SHOW_OFFER_PROPERTIES"])
	{
		foreach($arResult["SHOW_OFFER_PROPERTIES"] as $code=>$arProperty)
		{
			$showRow = true;
			if($arResult['DIFFERENT'])
			{
				$arCompare = array();
				foreach($arResult["ITEMS"] as &$arElement)
				{
					$arPropertyValue = $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["VALUE"];
					if(is_array($arPropertyValue))
					{
						sort($arPropertyValue);
						$arPropertyValue = implode(" / ", $arPropertyValue);
					}
					$arCompare[] = $arPropertyValue;
				}
				unset($arElement);
				$showRow = (count(array_unique($arCompare)) > 1);
			}
			if($showRow)
				$arShowOfferProps[$code] = $arProperty;
		}
	}
	?>

	<?if($arShowFileds || $arShowOfferFileds || $arShowProps || $arShowOfferProps):?>
		<div class="frame props">
			<div class="wraps">
				<table class="data_table_props compare_view">
					<?if($arShowFileds)
					{
						foreach($arShowFileds as $code => $arProp){?>
							<tr>
								<td>
									<?=GetMessage("IBLOCK_FIELD_".$code);?>
									<?if($arResult["ALL_FIELDS"][$code]){?>
										<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_FIELDS"][$code]["ACTION_LINK"])?>')" class="remove"><i></i></span>
									<?}?>
								</td>
								<?foreach($arResult["ITEMS"] as $arElement){?>
									<td valign="top">
										<?=$arElement["FIELDS"][$code];?>
										
									</td>
								<?}
								unset($arElement);?>
							</tr>
						<?}?>
					<?}
					if($arShowOfferFileds){
						foreach ($arShowOfferFileds as $code => $arProp){?>
							<tr>
								<td>
									<?=GetMessage("IBLOCK_OFFER_FIELD_".$code)?>
									<?if($arResult["ALL_OFFER_FIELDS"][$code]){?>
										<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_OFFER_FIELDS"][$code]["ACTION_LINK"])?>')" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
									<?}?>
								</td>
								<?foreach($arResult["ITEMS"] as &$arElement){?>
									<td>
										<?=(is_array($arElement["OFFER_FIELDS"][$code])? implode("/ ", $arElement["OFFER_FIELDS"][$code]): $arElement["OFFER_FIELDS"][$code])?>
									</td>
								<?}
								unset($arElement);
								?>
							</tr>
						<?}
					}?>
					<?
					if($arShowProps){
						foreach ($arShowProps as $code => $arProperty){?>
							<tr>
								<td>
								<?=$arProperty["NAME"]?>
								<?if($arResult["ALL_PROPERTIES"][$code]){?>
									<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_PROPERTIES"][$code]["ACTION_LINK"])?>')" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
								<?}?>
								</td>
								<?foreach($arResult["ITEMS"] as &$arElement){?>
									<td>
										<?=(is_array($arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
									</td>
								<?}
								unset($arElement);
								?>
							</tr>
						<?}
					}?>
					<?if($arShowOfferProps){
						foreach($arShowOfferProps as $code=>$arProperty){?>
							<tr>
								<td>
									<?=$arProperty["NAME"]?>
									<?if($arResult["ALL_OFFER_PROPERTIES"][$code]){?>
										<span onclick="CatalogCompareObj.MakeAjaxAction('<?=CUtil::JSEscape($arResult["ALL_OFFER_PROPERTIES"][$code]["ACTION_LINK"])?>')" class="remove" title="<?=GetMessage("CATALOG_REMOVE_PRODUCT")?>"><i></i></span>
									<?}?>
								</td>
								<?foreach($arResult["ITEMS"] as &$arElement){?>
									<td>
										<?=(is_array($arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])? implode("/ ", $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"]): $arElement["OFFER_DISPLAY_PROPERTIES"][$code]["DISPLAY_VALUE"])?>
									</td>
								<?}
								unset($arElement);
								?>
							</tr>
						<?}
					}?>
				</table>
			</div>
		</div>
	<?endif;?>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(window).on('resize', function(){
			initSly();
			createTableCompare($('.data_table_props:not(.clone)'), $('.prop_title_table'), $('.data_table_props.clone'));
		});
		$(window).resize();
		$('.wraps .item_block .title').sliceHeight({'row': '.compare_view', 'item': '.item_block'});
	})
</script>
<?if ($isAjax){
	die();
}?>
</div>
<script type="text/javascript">
	var CatalogCompareObj = new BX.Iblock.Catalog.CompareClass("bx_catalog_compare_block");
</script>