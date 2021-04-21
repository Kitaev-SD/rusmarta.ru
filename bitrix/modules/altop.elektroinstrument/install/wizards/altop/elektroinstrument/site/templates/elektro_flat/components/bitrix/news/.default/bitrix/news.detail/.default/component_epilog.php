<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult["PROPERTY_LINKED_ID"])):
	global $arLinkPrFilter;
	$arLinkPrFilter = array(
		"ID" => $arResult["PROPERTY_LINKED_ID"]
	);
	$bxajaxid = $_REQUEST["bxajaxid"];
	if(!empty($bxajaxid)) {
		//JS//?>	
		<script type="text/javascript">
			//<![CDATA[
			BX.ready(function() {
				//ITEMS_HEIGHT//
				var itemsTable = $(".filtered-items .catalog-item-card");
				if(!!itemsTable && itemsTable.length > 0) {
					$(window).resize(function() {
						adjustItemHeight(itemsTable);
					});
					adjustItemHeight(itemsTable);
				}
			});
			//]]>
		</script>
	<?}?>
	<div class="news-detail__products">
		<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR."include/linked.php"
			),
			false,
			array("HIDE_ICONS" => "Y")
		);?>
	</div>
<?endif;?>

<?
$APPLICATION->SetPageProperty("ogtype", "article");
if(is_array($arResult["DETAIL_PICTURE"])):
	$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$arResult['DETAIL_PICTURE']['SRC']);
	$APPLICATION->SetPageProperty("ogimagewidth", $arResult["DETAIL_PICTURE"]["WIDTH"]);
	$APPLICATION->SetPageProperty("ogimageheight", $arResult["DETAIL_PICTURE"]["HEIGHT"]);
endif;
?>