<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
if(method_exists($this, 'createFrame')) $this->createFrame()->begin(GetMessage('COMPOSITE_LOADING'));
if($this->__folder)
	$pathToTemplateFolder = $this->__folder ;
else
	$pathToTemplateFolder = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', dirname(__FILE__));

?>

<div class="yandex_market_reviews_store">
	<? if($arParams["HEAD"] && $arParams["HEAD_SIZE"] && $arParams["HEAD"] != ""):
		echo '<' . $arParams["HEAD_SIZE"] . '>' . $arParams["HEAD"] . '</' . $arParams["HEAD_SIZE"] . '>' . PHP_EOL;
	endif ?>
	<div class="reviews" id="yrms_reviews">
	</div>
	<a class="prev disabled" href="javascript:void(0)">&lt; <?=GetMessage('PREV');?></a>
	<a class="page disabled">| <span class="load"><?=GetMessage('LOAD');?></span><span class="num"><?=GetMessage('PAGE');?> <span>1</span></span> |</a>
	<a class="next" href="javascript:void(0)"><?=GetMessage('NEXT');?> &gt;</a>
	<a class="link" href='http://market.yandex.ru/shop/<?=$arParams["SHOPID"];?>/reviews'><?=GetMessage('LINK');?></a>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		updateYRMS(1, true, '<?=$pathToTemplateFolder;?>','<?=$arParams["COUNT"];?>');
	})
</script>
