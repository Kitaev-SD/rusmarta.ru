<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?// intro text?>
<div class="text_before_items">
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
			"AREA_FILE_SHOW" => "page",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => ""
		)
	);?>
</div>
<?
$arItemFilter = CNext::GetIBlockAllElementsFilter($arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arItemFilter['CHECK_PERMISSIONS'] = 'Y';
	$arItemFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$itemsCnt = CNextCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());?>

<?if(!$itemsCnt):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_EMPTY")?></div>
<?else:?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CNext::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
	}
	?>
	<?$arSections = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N', 'URL_TEMPLATE' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'])), array_merge($arItemFilter, array('DEPTH_LEVEL' => 1, 'CNT_ACTIVE' => "Y")), false, array('ID', 'SECTION_PAGE_URL'));?>
	
	<?
	if(isset($arItemFilter['CODE']))
	{
		unset($arItemFilter['CODE']);
		unset($arItemFilter['SECTION_CODE']);
	}
	if(isset($arItemFilter['ID']))
	{
		unset($arItemFilter['ID']);
		unset($arItemFilter['SECTION_ID']);
	}
	?>
	<?
	$arTags = array();
	if($arSections)
	{
		foreach($arSections as $key => $arSection)
		{
			$arElements = CNextCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'Y')), array_merge($arItemFilter, array("SECTION_ID" => $arSection["ID"])), false, false, array('ID', 'TAGS'));
			if(!$arElements)
				unset($arSections[$key]);
			else
			{
				foreach($arElements as $arElement)
				{
					if($arElement['TAGS'])
					{
						$arTags[] = explode(',', $arElement['TAGS']);
					}
				}
				$arSections[$key]['ELEMENT_COUNT'] = count($arElements);
			}
		}
	}
	else
	{
		$arElements = CNextCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'Y')), $arItemFilter, false, false, array('ID', 'TAGS'));
		
		foreach($arElements as $arElement)
		{
			if($arElement['TAGS'])
			{
				$arTags[] = explode(',', $arElement['TAGS']);
			}
		}
	}
	?>
	<?$this->__component->__template->SetViewTarget('under_sidebar_content');?>
		<?if($arSections):?>
			<div class="fill-block container-block">
				<div class="title-block-middle"><?=GetMessage('CATEGORY');?></div>
				<ul class="categorys">
					<?foreach($arSections as $arSection):
						if(isset($arSection['NAME']) && $arSection['NAME']):?>
							<li><a href="<?=$arSection['SECTION_PAGE_URL'];?>" class="dark-color"><span class="text"><?=$arSection['NAME'];?></span><span class="count"><?=$arSection['ELEMENT_COUNT'];?></span></a></li>
						<?endif;?>
					<?endforeach;?>
				</ul>
			</div>
		<?endif;?>
		<?$APPLICATION->IncludeComponent(
			"bitrix:search.tags.cloud",
			"main",
			Array(
				"CACHE_TIME" => "86400",
				"CACHE_TYPE" => "A",
				"CHECK_DATES" => "Y",
				"COLOR_NEW" => "3E74E6",
				"COLOR_OLD" => "C0C0C0",
				"COLOR_TYPE" => "N",
				"TAGS_ELEMENT" => $arTags,
				"FILTER_NAME" => $arParams["FILTER_NAME"],
				"FONT_MAX" => "50",
				"FONT_MIN" => "10",
				"PAGE_ELEMENTS" => "150",
				"PERIOD" => "",
				"PERIOD_NEW_TAGS" => "",
				"SHOW_CHAIN" => "N",
				"SORT" => "NAME",
				"TAGS_INHERIT" => "Y",
				"URL_SEARCH" => SITE_DIR."search/index.php",
				"WIDTH" => "100%",
				"arrFILTER" => array("iblock_aspro_next_content"),
				"arrFILTER_iblock_aspro_next_content" => array($arParams["IBLOCK_ID"])
			), $component, array('HIDE_ICONS' => 'Y')
		);?>
	<?$this->__component->__template->EndViewTarget();?>

	<?global $arTheme;?>
	<?// section elements?>
	<?if((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") || (strtolower($_REQUEST['ajax']) == 'y'))
	{
		$APPLICATION->RestartBuffer();
	}?>
	<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["BLOG_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
	<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>

	<?if((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") || (strtolower($_REQUEST['ajax']) == 'y'))
	{
		die();
	}?>
<?endif;?>