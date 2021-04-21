<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
// geting section items count and section [ID, NAME]
$arItemFilter = CNext::GetCurrentSectionElementFilter($arResult["VARIABLES"], $arParams);
$arSectionFilter = CNext::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arSectionFilter['CHECK_PERMISSIONS'] = 'Y';
	$arSectionFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$arSection = CNextCache::CIblockSection_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arSectionFilter, false, array('ID', 'DESCRIPTION', 'PICTURE', 'DETAIL_PICTURE'), true);
CNext::AddMeta(
	array(
		'og:description' => $arSection['DESCRIPTION'],
		'og:image' => (($arSection['PICTURE'] || $arSection['DETAIL_PICTURE']) ? CFile::GetPath(($arSection['PICTURE'] ? $arSection['PICTURE'] : $arSection['DETAIL_PICTURE'])) : false),
	)
);
?>
<?if(!$arSection && $arParams['SET_STATUS_404'] !== 'Y'):?>

	<?// get element start
	$arItemElementFilter = CNext::GetCurrentElementFilter($arResult['VARIABLES'], $arParams);
	if($arItemElementFilter)
	{
		foreach($arItemElementFilter as $key => $value)
		{
			if($key == 'SECTION_CODE')
			{
				$arItemElementFilter['CODE'] = $value;
				$arResult['VARIABLES']['ELEMENT_CODE'] = $value;
				unset($arItemElementFilter[$key]);
				unset($arResult['VARIABLES']['SECTION_CODE']);
			}
			if($key == 'SECTION_ID')
			{
				$arItemElementFilter['ID'] = $value;
				$arResult['VARIABLES']['ELEMENT_ID'] = $value;
				unset($arItemElementFilter[$key]);
				unset($arResult['VARIABLES']['SECTION_ID']);
			}
		}
		$arItemElementFilter['SECTION_ID'] = $arResult['VARIABLES']['SECTION_ID'] = 0;
	}
	if($arParams['CACHE_GROUPS'] == 'Y')
	{
		$arItemElementFilter['CHECK_PERMISSIONS'] = 'Y';
		$arItemElementFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
	}
	$arElement = CNextCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'N')), $arItemElementFilter, false, false, array('ID', 'PREVIEW_TEXT', 'IBLOCK_SECTION_ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
	if($arElement)
	{
		include_once('detail.php');
		return ;
	}
	// get element end?>
	<?\Bitrix\Iblock\Component\Tools::process404(
		trim($arParams["MESSAGE_404"]) ?: GetMessage("ELEMENT_NOTFOUND")
		,true
		,true
		,$arParams["SHOW_404"] === "Y"
		,$arParams["FILE_404"]
	);
	return;?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$arSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CNext::goto404Page();?>
<?else:?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N'){
		CNext::ShowRSSIcon(CComponentEngine::makePathFromTemplate($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss_section'], array_map('urlencode', $arResult['VARIABLES'])));
	}?>
	
	<?$arSections = CNextCache::CIBLockSection_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'GROUP' => array('ID'), 'MULTI' => 'N', 'URL_TEMPLATE' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'])), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'DEPTH_LEVEL' => 1, 'ACTIVE' => 'Y', 'CNT_ACTIVE' => "Y"), false, array('ID', 'SECTION_PAGE_URL'));
	?>
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
	foreach($arSections as $key => $arItem)
	{
		$arElements = CNextCache::CIblockElement_GetList(array('CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']), 'MULTI' => 'Y')), array_merge($arItemFilter, array("SECTION_ID" => $arItem["ID"])), false, false, array('ID', 'TAGS', 'IBLOCK_SECTION_ID'));
		if(!$arElements)
			unset($arSections[$key]);
		else
		{
			foreach($arElements as $arElement)
			{
				if($arElement['TAGS'] && $arElement['IBLOCK_SECTION_ID'] == $arSection['ID'])
				{
					$arTags[] = explode(',', $arElement['TAGS']);
				}
			}
			$arSections[$key]['ELEMENT_COUNT'] = count($arElements);
		}
	}
	if(!$arSections[$arSection['ID']])
	{
		\Bitrix\Iblock\Component\Tools::process404(
			trim($arParams["MESSAGE_404"]) ?: GetMessage("ELEMENT_NOTFOUND")
			,true
			,true
			,$arParams["SHOW_404"] === "Y"
			,$arParams["FILE_404"]
		);
		return;
	}
	?>
	<?
	// edit/add/delete buttons for edit mode
	$arSectionButtons = CIBlock::GetPanelButtons($arSection['IBLOCK_ID'], 0, $arSection['ID'], array('SESSID' => false, 'CATALOG' => true));
	$this->AddEditAction($arSection['ID'], $arSectionButtons['edit']['edit_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_EDIT'));
	$this->AddDeleteAction($arSection['ID'], $arSectionButtons['edit']['delete_section']['ACTION_URL'], CIBlock::GetArrayByID($arSection['IBLOCK_ID'], 'SECTION_DELETE'), array('CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
	?>
	<div class="main-section-wrapper" id="<?=$this->GetEditAreaId($arSection['ID'])?>">
		<?$this->__component->__template->SetViewTarget('under_sidebar_content');?>
			<?if($arSections):?>
				<div class="fill-block container-block">
					<div class="title-block-middle"><?=GetMessage('CATEGORY');?></div>
					<?
					$cur_page = $GLOBALS['APPLICATION']->GetCurPage(true);
					$cur_page_no_index = $GLOBALS['APPLICATION']->GetCurPage(false);
					?>
					<ul class="categorys">
						<?foreach($arSections as $arRootSection):
							if(isset($arRootSection['NAME']) && $arRootSection['NAME']):?>
								<li><a href="<?=$arRootSection['SECTION_PAGE_URL'];?>" class="dark-color <?=(CMenu::IsItemSelected($arRootSection['SECTION_PAGE_URL'], $cur_page, $cur_page_no_index) ? 'active' : '');?>"><span class="text"><?=$arRootSection['NAME'];?></span><span class="count"><?=$arRootSection['ELEMENT_COUNT'];?></span></a></li>
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
					"FILTER_NAME" => "",
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
				), $component
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
	</div>
<?endif;?>