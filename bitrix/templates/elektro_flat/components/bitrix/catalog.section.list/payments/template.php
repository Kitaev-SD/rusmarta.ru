<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);

if(count($arResult["SECTIONS"]) < 1)
	return;?>

<div class="payments-section-list">
	<?$TOP_DEPTH = $arResult["SECTION"]["DEPTH_LEVEL"];
	$CURRENT_DEPTH = $TOP_DEPTH;
	$number = 1;

	foreach($arResult["SECTIONS"] as $arSection):		
		if($CURRENT_DEPTH < $arSection["DEPTH_LEVEL"]):
			echo "\n",str_repeat("\t", $arSection["DEPTH_LEVEL"] - $TOP_DEPTH),"<ul>";
		elseif($CURRENT_DEPTH == $arSection["DEPTH_LEVEL"]):
			echo "</li>";
		else:
			while($CURRENT_DEPTH > $arSection["DEPTH_LEVEL"]) {
				echo "</li>";
				echo "\n",str_repeat("\t", $CURRENT_DEPTH - $TOP_DEPTH),"</ul>","\n",str_repeat("\t", $CURRENT_DEPTH - $TOP_DEPTH - 1);
				$CURRENT_DEPTH--;
			}
			echo "\n",str_repeat("\t", $CURRENT_DEPTH - $TOP_DEPTH),"</li>";
		endif;		
		
		echo "\n",str_repeat("\t", $arSection["DEPTH_LEVEL"] - $TOP_DEPTH);?>
		
		<li><div class="payment-section-title"><?=$arSection["NAME"]?></div>
		<?if(is_array($arSection["ITEMS"]) && count($arSection["ITEMS"]) > 0):?>
			<div class="payment-items">
				<?foreach($arSection["ITEMS"] as $arItem):?>
					<div class="payment-item">								
						<div class="payment-item-info">
							<div class="payment-item-block">
								<div class="payment-item-number"><?=$number?></div>
								<div class="payment-item-title"><?=$arItem["NAME"]?></div>
							</div>
							<?if(!empty($arItem["PREVIEW_TEXT"])):?>
								<div class="payment-item-descr"><?=$arItem["PREVIEW_TEXT"]?></div>
							<?endif;?>
						</div>
						<div class="payment-item-logo<?=(!is_array($arItem["LOGO_1"]) && !is_array($arItem["LOGO_2"])) ? ' no-logo' : ''?>">
							<?if(is_array($arItem["LOGO_1"]) && count($arItem["LOGO_1"]) > 0):?>
								<img src="<?=$arItem['LOGO_1']['SRC']?>" width="<?=$arItem['LOGO_1']['WIDTH']?>" height="<?=$arItem['LOGO_1']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
							<?endif;
							if(is_array($arItem["LOGO_2"]) && count($arItem["LOGO_2"]) > 0):?>
								<img src="<?=$arItem['LOGO_2']['SRC']?>" width="<?=$arItem['LOGO_2']['WIDTH']?>" height="<?=$arItem['LOGO_2']['HEIGHT']?>" alt="<?=$arItem['NAME']?>" />
							<?endif;?>
						</div>
						<div class="payment-item-url<?=empty($arItem['PROPERTY_URL_VALUE']) ? ' no-url' : ''?>">
							<?if(!empty($arItem["PROPERTY_URL_VALUE"])):?>
								<a target="_blank" href="<?=$arItem['PROPERTY_URL_VALUE']?>" title="<?=$arItem['NAME']?>" rel="nofollow"><?=GetMessage("PAYMENT_ITEM_URL")?></a>
							<?endif;?>
						</div>
					</div>
					<?$number++;
				endforeach;?>
			</div>
		<?endif;
		
		$CURRENT_DEPTH = $arSection["DEPTH_LEVEL"];
	endforeach;

	while($CURRENT_DEPTH > $TOP_DEPTH) {
		echo "</li>";
		echo "\n",str_repeat("\t", $CURRENT_DEPTH - $TOP_DEPTH),"</ul>","\n",str_repeat("\t", $CURRENT_DEPTH - $TOP_DEPTH - 1);
		$CURRENT_DEPTH--;
	}?>
</div>