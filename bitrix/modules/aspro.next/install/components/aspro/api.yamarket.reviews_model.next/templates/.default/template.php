<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?use \Bitrix\Main\Localization\Loc;?>
<?$frame = $this->createFrame()->begin(Loc::getMessage('REVIEWS_LOAD'));?>

<div class="model_reviews_wrapper">
	<?//ajax response?>
	<div class="js_reviews" id="ya_reviews"></div>
	<?//info block?>
	<div class="pager_block">
		<span class="prev disabled">&lt; <?=Loc::getMessage('PREV_LINK');?></span>
		<span class="pager_info disabled">| <span class="load" style="display:inline-block;"><?=Loc::getMessage('REVIEWS_LOAD');?></span><span class="num" style="display:none"><?=Loc::getMessage('CURRENT_PAGE');?> <span>1</span></span> |</span>
		<span class="next disabled"><?=Loc::getMessage('NEXT_LINK');?> &gt;</span>
	</div>
	<a class="more_yalink" target="_blank" href="https://market.yandex.ru/product/<?=$arParams["YANDEX_MODEL_ID"];?>/reviews"><?=Loc::getMessage('MORE_LINK');?></a>
</div>

<script type="text/javascript">
	BX.message({
		AJAX_PATH_YM: "<?=$this->__folder;?>",
		PARAMS_YM: <?=CUtil::PhpToJSObject($arParams);?>,
		SITE_ID: "<?=SITE_ID;?>"
	})
	BX.ready(function(){getYMReviewsModel();})
</script>

<?$frame->end();?>