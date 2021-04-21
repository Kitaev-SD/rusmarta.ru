<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if(strlen($arResult["ERROR_MESSAGE"]) > 0)
	ShowError($arResult["ERROR_MESSAGE"]);

if(count($arResult["STORES"]) > 0) {
	foreach($arResult["STORES"] as $pid => $arProperty) {?>
		<div class="catalog-detail-store" style="display: <?=($arParams['SHOW_EMPTY_STORE'] == 'N' && isset($arProperty['REAL_AMOUNT']) && $arProperty['REAL_AMOUNT'] <= 0 ? 'none' : '')?>;">			
			<span class="name">
				<?=$arProperty["TITLE"].(isset($arProperty["PHONE"]) ? GetMessage("S_PHONE").$arProperty["PHONE"] : "").(isset($arProperty["SCHEDULE"]) ? GetMessage("S_SCHEDULE").$arProperty["SCHEDULE"] : "");?>
				<?=($arParams["SHOW_GENERAL_STORE_INFORMATION"] == "Y") ? GetMessage("S_BALANCE") : "";?>				
			</span>
			<span class="val"><?=$arProperty["AMOUNT"]?></span>
		</div>
	<?}
}?>