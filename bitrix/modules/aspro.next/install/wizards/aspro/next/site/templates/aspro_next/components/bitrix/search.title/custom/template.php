<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?$this->setFrameMode(true);?>
<?
$INPUT_ID = trim($arParams["~INPUT_ID"]);
if(strlen($INPUT_ID) <= 0)
	$INPUT_ID = "title-search-input";
$INPUT_ID = CUtil::JSEscape($INPUT_ID);
$CONTAINER_ID = trim($arParams["~CONTAINER_ID"]);
if(strlen($CONTAINER_ID) <= 0)
	$CONTAINER_ID = "title-search";
$CONTAINER_ID = CUtil::JSEscape($CONTAINER_ID);

global $isFixedTopSearch;
$INPUT_ID_TMP = $INPUT_ID;
if(isset($isFixedTopSearch) && $isFixedTopSearch)
{
	$CONTAINER_ID .= 'tf';
	$INPUT_ID .= 'tf';
}
?>
<?if($arParams["SHOW_INPUT"] !== "N"):?>
	<div class="inline-search-block fixed with-close big">
		<div class="maxwidth-theme">
			<div class="col-md-12">
				<div class="search-wrapper">
					<div id="<?=$CONTAINER_ID?>">
						<form action="<?=$arResult["FORM_ACTION"]?>" class="search">
							<div class="search-input-div">
								<input class="search-input" id="<?=$INPUT_ID?>" type="text" name="q" value="" placeholder="<?=GetMessage("CT_BST_SEARCH_BUTTON")?>" size="20" maxlength="50" autocomplete="off" />
							</div>
							<div class="search-button-div">
								<button class="btn btn-search btn-default bold btn-lg" type="submit" name="s" value="<?=GetMessage("CT_BST_SEARCH_BUTTON2")?>"><?=GetMessage("CT_BST_SEARCH_BUTTON2")?></button>
								<span class="close-block inline-search-hide"><span class="svg svg-close close-icons"></span></span>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
<?endif;?>
<script type="text/javascript">
	var jsControl = new JCTitleSearch2({
		//'WAIT_IMAGE': '/bitrix/themes/.default/images/wait.gif',
		'AJAX_PAGE' : '<?=CUtil::JSEscape(POST_FORM_ACTION_URI)?>',
		'CONTAINER_ID': '<?=$CONTAINER_ID?>',
		'INPUT_ID': '<?=$INPUT_ID?>',
		'INPUT_ID_TMP': '<?=$INPUT_ID_TMP?>',
		'MIN_QUERY_LEN': 2
	});
</script>