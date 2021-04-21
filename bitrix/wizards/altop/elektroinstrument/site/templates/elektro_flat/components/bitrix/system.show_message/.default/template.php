<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<span class="alertMsg <?=($arParams["STYLE"] == "errortext" ? "bad" : ($arParams["STYLE"] == "infotext" ? "info" : "good"));?>">	
	<i class="fa fa-<?=($arParams['STYLE'] == 'errortext' ? 'exclamation-triangle' : ($arParams['STYLE'] == 'infotext' ? 'info' : 'check'));?>" aria-hidden="true"></i>
	<span class="text"><?=$arParams["MESSAGE"]?></span>
</span>