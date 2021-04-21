<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?
$arSectionFilter = CNext::GetCurrentSectionFilter($arResult["VARIABLES"], $arParams);

if($arParams['CACHE_GROUPS'] == 'Y')
{
	$arSectionFilter['CHECK_PERMISSIONS'] = 'Y';
	$arSectionFilter['GROUPS'] = $GLOBALS["USER"]->GetGroups();
}

$title_news = GetMessage('CURRENT_NEWS', array('#YEAR#' => $arResult['VARIABLES']['YEAR']));
?>

<?$arItems = CNextCache::CIBLockElement_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'), false, false, array('ID', 'NAME', 'ACTIVE_FROM'));
$arYears = array();
if($arItems)
{
	$bFoundSection = false;
	foreach($arItems as $arItem)
	{
		if($arItem['ACTIVE_FROM'])
		{
			if($arDateTime = ParseDateTime($arItem['ACTIVE_FROM'], FORMAT_DATETIME))
				$arYears[$arDateTime['YYYY']] = $arDateTime['YYYY'];
		}
	}
	if($arYears)
	{
		if(((isset($arResult['VARIABLES']['YEAR'])) && $arResult['VARIABLES']['YEAR']) && $arYears[$arResult['VARIABLES']['YEAR']])
			$bFoundSection = true;

		if($arParams['USE_FILTER'] != 'N')
		{
			rsort($arYears);?>			
			<div class="head-block top">
				<div class="bottom_border"></div>
				<div class="item-link">
					<div class="title">
						<a class="btn-inline black" href="<?=$arResult['FOLDER'];?>"><?=GetMessage('ALL_TIME');?></a>
					</div>
				</div>
				<?foreach($arYears as $year):?>
					<?$bSelected = ($year == $arResult['VARIABLES']['YEAR']);?>
					<div class="item-link <?=($bSelected ? 'active' : '');?>">
						<div class="title btn-inline black">
							<?if($bSelected):?>
								<span class="btn-inline black"><?=$year;?></span>
							<?else:?>
								<a class="btn-inline black" href="<?=$arResult['FOLDER'].str_replace('#YEAR#', $year, $arResult['URL_TEMPLATES']['section']);?>"><?=$year;?></a>
							<?endif;?>
						</div>
					</div>
				<?endforeach;?>
			</div>
		<?}
	}
}?>

<?if(!$bFoundSection && $arParams['SET_STATUS_404'] !== 'Y'):?>
	<div class="alert alert-warning"><?=GetMessage("SECTION_NOTFOUND")?></div>
<?elseif(!$bFoundSection && $arParams['SET_STATUS_404'] === 'Y'):?>
	<?CNext::goto404Page();?>
<?else:?>
	<?// rss
	if($arParams['USE_RSS'] !== 'N')
		CNext::ShowRSSIcon(CComponentEngine::makePathFromTemplate($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss_section'], array_map('urlencode', $arResult['VARIABLES'])));
	?>

	<?global $arTheme, $isMenu;?>

	<?if(!$isMenu):?>
		<div class="sub_container fixed_wrapper">
		<div class="row">
			<div class="col-md-9">
	<?endif;?>

	<?// section elements?>
	<?$GLOBALS[$arParams["FILTER_NAME"]] = array(
		">DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$arResult["VARIABLES"]["YEAR"], "DD.MM.YYYY"),
		"<=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".(intval($arResult["VARIABLES"]["YEAR"])+1), "DD.MM.YYYY"),
	);?>
	<?if((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") || (strtolower($_REQUEST['ajax']) == 'y'))
	{
		$APPLICATION->RestartBuffer();
	}?>
	<?$sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["NEWS_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]);?>
	<?@include_once('page_blocks/'.$sViewElementsTemplate.'.php');?>
	<?if((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") || (strtolower($_REQUEST['ajax']) == 'y'))
	{
		die();
	}?>
	<?// ask block?>
	<?ob_start();?>
		<div class="ask_a_question">
			<div class="inner">
				<div class="text-block">
					<?$APPLICATION->IncludeComponent(
						 'bitrix:main.include',
						 '',
						 Array(
							  'AREA_FILE_SHOW' => 'page',
							  'AREA_FILE_SUFFIX' => 'ask',
							  'EDIT_TEMPLATE' => ''
						 )
					);?>
				</div>
			</div>
			<div class="outer">
				<span><span class="btn btn-default btn-lg white animate-load" data-event="jqm" data-param-form_id="ASK" data-name="question"><span><?=(strlen($arParams['S_ASK_QUESTION']) ? $arParams['S_ASK_QUESTION'] : GetMessage('S_ASK_QUESTION'))?></span></span></span>
			</div>
		</div>
	<?$html = ob_get_contents();?>
	<?ob_end_clean();?>

	<?if(!$isMenu):?>
			</div>
			<div class="col-md-3 with-padding-left hidden-xs hidden-sm">
				<div class="fixed_block_fix"></div>
				<div class="ask_a_question_wrapper">
					<?=$html;?>
				</div>
			</div>
		</div>
		</div>
	<?else:?>
		<?$this->SetViewTarget('under_sidebar_content');?>
			<?=$html;?>
		<?$this->EndViewTarget();?>
	<?endif;?>

	<?$APPLICATION->SetTitle($title_news);?>
	<?$APPLICATION->AddChainItem($title_news);?>
<?endif;?>