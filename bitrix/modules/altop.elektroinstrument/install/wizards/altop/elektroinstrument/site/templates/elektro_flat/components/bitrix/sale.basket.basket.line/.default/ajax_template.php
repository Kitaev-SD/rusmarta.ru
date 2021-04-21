<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->IncludeLangFile("template.php");?>

<a href="<?=$arParams['PATH_TO_BASKET']?>" class="cart" title="<?=GetMessage('TSBS')?>" rel="nofollow">
	<i class="fa fa-shopping-cart"></i>
	<span class="text"><?=GetMessage("TSBS")?></span>
	<span class="qnt_cont">
		<span class="qnt"><?=$arResult["QUANTITY"]?></span>
	</span>	
</a>				
<span class="sum_cont">
	<span class="sum" data-sum="<?=$arResult['SUM']?>" data-separator="<?=$arResult['THOUSANDS_SEP']?>" data-decimal="<?=$arResult['DECIMALS']?>" data-dec-point="<?=$arResult['DEC_POINT']?>">
		<span id="cartCounter"><?=$arResult["SUM_FORMATED"]?></span>
		<span class="curr"><?=$arResult["CURRENCY"]?></span>
	</span>
</span>		
<div class="oformit_cont">
	<?if(!CSite::InDir($arParams["PATH_TO_BASKET"]) && !CSite::InDir($arParams["PATH_TO_ORDER"]) && IntVal($arResult["QUANTITY"]) > 0):?>
		<form action="<?=$arParams['PATH_TO_BASKET']?>" method="post">
			<button name="oformit" class="btn_buy popdef oformit" value="<?=GetMessage('BASKET_LINE_CHECKOUT')?>"><?=GetMessage("BASKET_LINE_CHECKOUT")?></button>
		</form>
	<?else:?>
		<div class="btn_buy oformit dsbl"><?=GetMessage("BASKET_LINE_CHECKOUT")?></div>
	<?endif;?>
</div>