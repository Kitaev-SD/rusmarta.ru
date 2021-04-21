<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);

$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "altop_search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

if($arParams["SHOW_INPUT"] !== "N"):?>
	<div id="<?=$CONTAINER_ID?>" class="form-box">
		<form action="<?=$arResult['FORM_ACTION']?>">
			<i class="fa fa-search"></i>
			<input type="text" name="q" id="<?=$INPUT_ID?>" class="" maxlength="50" autocomplete="off" placeholder="<?=GetMessage('ALTOP_CATALOG_SEARCH')?>" value="" />
			<input type="submit" name="submit" class="" value="<?=GetMessage('ALTOP_SEARCH_BUTTON')?>" />
		</form>
	</div>
<?endif?>

<script type="text/javascript">
	var jsControl = new JCTitleSearch({
		'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
		'AJAX_PAGE' : '<?=POST_FORM_ACTION_URI?>',
		'CONTAINER_ID': '<?=$CONTAINER_ID?>',
		'INPUT_ID': '<?=$INPUT_ID?>',
		'MIN_QUERY_LEN': 3
	});
</script>