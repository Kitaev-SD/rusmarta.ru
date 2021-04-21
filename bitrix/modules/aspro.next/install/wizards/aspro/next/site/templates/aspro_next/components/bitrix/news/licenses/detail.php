<?if( !defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true ) die();?>
<?$this->setFrameMode(true);?>
<?
use \Bitrix\Main\Localization\Loc;
// get element
$arItemFilter = CNext::GetCurrentElementFilter($arResult["VARIABLES"], $arParams);
$arElement = CNextCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arItemFilter, false, false, array("ID", 'PREVIEW_TEXT', "IBLOCK_SECTION_ID", 'DETAIL_PICTURE', 'DETAIL_PAGE_URL', 'PROPERTY_LINK_PROJECTS', 'PROPERTY_LINK_REVIEWS', 'PROPERTY_DOCUMENTS'));
?>
<?if(!$arElement && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("ELEMENT_NOTFOUND")?></div>
<?elseif(!$arElement && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CNext::goto404Page();?>
<?else:?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CNext::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
	}?>
	<?CNext::AddMeta(
		array(
			'og:description' => $arElement['PREVIEW_TEXT'],
			'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
		)
	);?>
	<?if($arParams["USE_SHARE"] == "Y" && $arElement):?>
		<div class="share top <?=($arParams['USE_RSS'] !== 'N' ? 'rss-block' : '');?>">
			<div class="shares-block">
				<script type="text/javascript" src="//yastatic.net/share2/share.js" async="async" charset="utf-8"></script>
				<div class="ya-share2" data-services="vkontakte,facebook,twitter,viber,whatsapp,odnoklassniki,moimir"></div>
			</div>
		</div>
		<style type="text/css">h1{padding-right:300px;}</style>
		<script type="text/javascript">
			$('h1').addClass('shares');
			$(document).ready(function(){
				if($('a.rss').length)
					$('a.rss').after($('.share.top'));
				else
					$('h1').before($('.share.top'));
			})
		</script>
		<?if($arParams['USE_RSS'] !== 'N'):?>
			<style type="text/css">body h1{padding-right:360px;}</style>
		<?endif;?>
	<?endif;?>
	<?$APPLICATION->IncludeComponent(
		"bitrix:news.detail",
		"partners",
		Array(
			"DISPLAY_DATE" => $arParams["DISPLAY_DATE"],
			"DISPLAY_NAME" => $arParams["DISPLAY_NAME"],
			"DISPLAY_PICTURE" => $arParams["DISPLAY_PICTURE"],
			"DISPLAY_PREVIEW_TEXT" => $arParams["DISPLAY_PREVIEW_TEXT"],
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
			"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
			"DETAIL_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
			"SECTION_URL"	=>	$arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"META_KEYWORDS" => $arParams["META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
			"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
			"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
			"ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
			"GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
			"DISPLAY_TOP_PAGER" => $arParams["DETAIL_DISPLAY_TOP_PAGER"],
			"DISPLAY_BOTTOM_PAGER" => $arParams["DETAIL_DISPLAY_BOTTOM_PAGER"],
			"PAGER_TITLE" => $arParams["DETAIL_PAGER_TITLE"],
			"PAGER_SHOW_ALWAYS" => "N",
			"PAGER_TEMPLATE" => $arParams["DETAIL_PAGER_TEMPLATE"],
			"PAGER_SHOW_ALL" => $arParams["DETAIL_PAGER_SHOW_ALL"],
			"CHECK_DATES" => $arParams["CHECK_DATES"],
			"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
			"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
			"IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
			"USE_SHARE" 			=> $arParams["USE_SHARE"],
			"SHARE_HIDE" 			=> $arParams["SHARE_HIDE"],
			"SHARE_TEMPLATE" 		=> $arParams["SHARE_TEMPLATE"],
			"SHARE_HANDLERS" 		=> $arParams["SHARE_HANDLERS"],
			"SHARE_SHORTEN_URL_LOGIN"	=> $arParams["SHARE_SHORTEN_URL_LOGIN"],
			"SHARE_SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
		),
		$component
	);?>
	<?/*
	if(is_array($arElement["IBLOCK_SECTION_ID"]) && count($arElement["IBLOCK_SECTION_ID"]) > 1){
		CNext::CheckAdditionalChainInMultiLevel($arResult, $arParams, $arElement);
	}*/
	?>
	<?// reviews links?>
	<?if(in_array('LINK_REVIEWS', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_LINK_REVIEWS_VALUE']):?>
		<div class="wraps reviews-block">
			<hr />
			<h5><?=(strlen($arParams['T_REVIEWS']) ? $arParams['T_REVIEWS'] : GetMessage('T_REVIEWS'))?></h5>
			<?global $arrrFilter; $arrrFilter = array("ID" => $arElement["PROPERTY_LINK_REVIEWS_VALUE"]);?>
			<?$APPLICATION->IncludeComponent("bitrix:news.list", "reviews", array(
				"IBLOCK_TYPE" => "aspro_digital_content",
				"IBLOCK_ID" => CNextCache::$arIBlocks[SITE_ID]["aspro_digital_content"]["aspro_digital_reviews"][0],
				"NEWS_COUNT" => "20",
				"SORT_BY1" => "ACTIVE_FROM",
				"SORT_ORDER1" => "DESC",
				"SORT_BY2" => "SORT",
				"SORT_ORDER2" => "ASC",
				"FILTER_NAME" => "arrrFilter",
				"FIELD_CODE" => array(
					0 => "NAME",
					1 => "PREVIEW_TEXT",
					2 => "PREVIEW_PICTURE",
					3 => "",
				),
				"PROPERTY_CODE" => array(
					0 => "DOCUMENTS",
					1 => "POST",
				),
				"CHECK_DATES" => "Y",
				"DETAIL_URL" => "",
				"AJAX_MODE" => "N",
				"AJAX_OPTION_JUMP" => "N",
				"AJAX_OPTION_STYLE" => "Y",
				"AJAX_OPTION_HISTORY" => "N",
				"CACHE_TYPE" => "A",
				"CACHE_TIME" => "36000000",
				"CACHE_FILTER" => "Y",
				"CACHE_GROUPS" => "N",
				"PREVIEW_TRUNCATE_LEN" => "",
				"ACTIVE_DATE_FORMAT" => "d.m.Y",
				"SET_TITLE" => "N",
				"SET_STATUS_404" => "N",
				"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
				"ADD_SECTIONS_CHAIN" => "N",
				"HIDE_LINK_WHEN_NO_DETAIL" => "N",
				"PARENT_SECTION" => "",
				"PARENT_SECTION_CODE" => "",
				"INCLUDE_SUBSECTIONS" => "Y",
				"PAGER_TEMPLATE" => ".default",
				"DISPLAY_TOP_PAGER" => "N",
				"DISPLAY_BOTTOM_PAGER" => "Y",
				"PAGER_TITLE" => "Новости",
				"PAGER_SHOW_ALWAYS" => "N",
				"PAGER_DESC_NUMBERING" => "N",
				"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
				"PAGER_SHOW_ALL" => "N",
				"VIEW_TYPE" => "list",
				"SHOW_TABS" => "N",
				"SHOW_IMAGE" => "Y",
				"SHOW_NAME" => "Y",
				"SHOW_DETAIL" => "Y",
				"IMAGE_POSITION" => "left",
				"COUNT_IN_LINE" => "3",
				"AJAX_OPTION_ADDITIONAL" => ""
				),
			false, array("HIDE_ICONS" => "Y")
			);?>
		</div>
	<?endif;?>

	<?if(in_array('LINK_PROJECTS', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_LINK_PROJECTS_VALUE']):?>
		<div class="wraps projects-block">
			<hr />
			<h5><?=(strlen($arParams['T_PROJECTS']) ? $arParams['T_PROJECTS'] : GetMessage('T_PROJECTS'))?></h5>
			<?$GLOBALS['arrProjectFilter'] = array('ID' => $arElement['PROPERTY_LINK_PROJECTS_VALUE']);?>
			<?$APPLICATION->IncludeComponent(
				"bitrix:news.list",
				"items-row",
				array(
					"IBLOCK_TYPE" => "aspro_digital_content",
					"IBLOCK_ID" => CNextCache::$arIBlocks[SITE_ID]["aspro_digital_content"]["aspro_digital_projects"][0],
					"NEWS_COUNT" => "20",
					"SORT_BY1" => "SORT",
					"SORT_ORDER1" => "ASC",
					"SORT_BY2" => "ID",
					"SORT_ORDER2" => "DESC",
					"FILTER_NAME" => "arrProjectFilter",
					"FIELD_CODE" => array(
						0 => "NAME",
						1 => "PREVIEW_TEXT",
						2 => "PREVIEW_PICTURE",
						3 => "",
					),
					"PROPERTY_CODE" => array(
						0 => "LINK",
						1 => "",
					),
					"CHECK_DATES" => "Y",
					"DETAIL_URL" => "",
					"AJAX_MODE" => "N",
					"AJAX_OPTION_JUMP" => "N",
					"AJAX_OPTION_STYLE" => "Y",
					"AJAX_OPTION_HISTORY" => "N",
					"CACHE_TYPE" => "A",
					"CACHE_TIME" => "36000000",
					"CACHE_FILTER" => "Y",
					"CACHE_GROUPS" => "N",
					"PREVIEW_TRUNCATE_LEN" => "",
					"ACTIVE_DATE_FORMAT" => "d.m.Y",
					"SET_TITLE" => "N",
					"SET_STATUS_404" => "N",
					"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
					"ADD_SECTIONS_CHAIN" => "N",
					"HIDE_LINK_WHEN_NO_DETAIL" => "N",
					"PARENT_SECTION" => "",
					"PARENT_SECTION_CODE" => "",
					"INCLUDE_SUBSECTIONS" => "Y",
					"PAGER_TEMPLATE" => ".default",
					"DISPLAY_TOP_PAGER" => "N",
					"DISPLAY_BOTTOM_PAGER" => "Y",
					"PAGER_TITLE" => "Новости",
					"PAGER_SHOW_ALWAYS" => "N",
					"PAGER_DESC_NUMBERING" => "N",
					"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
					"PAGER_SHOW_ALL" => "N",
					"VIEW_TYPE" => "list",
					"IMAGE_POSITION" => "left",
					"COUNT_IN_LINE" => "3",
					"SHOW_TITLE" => "Y",
					"T_PROJECTS" => ($arParams["T_PROJECTS"] ? $arParams["T_PROJECTS"] : Loc::getMessage("T_PROJECTS")),
					"AJAX_OPTION_ADDITIONAL" => ""
				),
				false, array("HIDE_ICONS" => "Y")
			);?>
		</div>
	<?endif;?>

	<?// docs files?>
	<?if(in_array('DOCUMENTS', $arParams['DETAIL_PROPERTY_CODE']) && $arElement['PROPERTY_DOCUMENTS_VALUE']):?>
		<div class="wraps docs-block">
			<hr />
			<h5><?=(strlen($arParams['T_DOCS']) ? $arParams['T_DOCS'] : Loc::getMessage('T_DOCS'))?></h5>
			<div class="row">
				<?foreach($arElement['PROPERTY_DOCUMENTS_VALUE'] as $docID):?>
					<?$arItem = CNext::get_file_info($docID);?>
					<div class="col-md-4">
						<?
						$fileName = substr($arItem['ORIGINAL_NAME'], 0, strrpos($arItem['ORIGINAL_NAME'], '.'));
						$fileTitle = (strlen($arItem['DESCRIPTION']) ? $arItem['DESCRIPTION'] : $fileName);

						?>
						<div class="blocks clearfix <?=$arItem["TYPE"];?>">
							<div class="inner-wrapper">
								<a href="<?=$arItem['SRC']?>" class="dark-color text" target="_blank"><?=$fileTitle?></a>
								<div class="filesize"><?=CNext::filesize_format($arItem['FILE_SIZE']);?></div>
							</div>
						</div>
					</div>
				<?endforeach;?>
			</div>
		</div>
	<?endif;?>

<?endif;?>
<div style="clear:both"></div>
<hr class="bottoms" />
<div class="row">
	<div class="col-md-6 share">
		<?if($arParams["USE_SHARE"] == "Y" && $arElement):?>
			<div class="shares-block">
				<span class="text"><?=GetMessage('SHARE_TEXT')?></span>
				<script type="text/javascript" src="//yastatic.net/share2/share.js" async="async" charset="utf-8"></script>
				<div class="ya-share2" data-services="vkontakte,facebook,twitter,viber,whatsapp,odnoklassniki,moimir"></div>
			</div>
		<?endif;?>
	</div>
	<div class="col-md-6">
		<a class="back-url url-block" href="<?=$arResult['FOLDER'].$arResult['URL_TEMPLATES']['news']?>"><i class="fa fa-angle-left"></i><span><?=GetMessage('BACK_LINK')?></span></a>
	</div>
</div>