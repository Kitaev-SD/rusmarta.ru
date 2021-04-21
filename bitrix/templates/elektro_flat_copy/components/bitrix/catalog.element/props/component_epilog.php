<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;

//SET_CURRENCIES//
if(!empty($templateData["CURRENCIES"])) {
	$loadCurrency = Loader::includeModule("currency");
	
	CJSCore::Init(array("currency"));?>
	
	<script type="text/javascript">
		BX.Currency.setCurrencies(<?=$templateData["CURRENCIES"]?>);
	</script>
<?}?>