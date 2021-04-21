<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$cartId = "cart_line".$component->getNextNumber();
$arParams["cartId"] = $cartId;?>

<script type="text/javascript">
	var <?=$cartId?> = new BitrixSmallCart;
</script>

<div class="cart_line" id="<?=$cartId?>">
	<?$frame = $this->createFrame("cart_line")->begin("");	
		require(realpath(dirname(__FILE__))."/ajax_template.php");	
	$frame->end();?>
</div>

<script type="text/javascript">
	<?=$cartId?>.siteId       = "<?=SITE_ID?>";
	<?=$cartId?>.cartId       = "<?=$cartId?>";
	<?=$cartId?>.ajaxPath     = "<?=$componentPath?>/ajax.php";	
	<?=$cartId?>.templateName = "<?=$templateName?>";
	<?=$cartId?>.arParams     =  <?=CUtil::PhpToJSObject($arParams)?>;
	<?=$cartId?>.activate();
</script>