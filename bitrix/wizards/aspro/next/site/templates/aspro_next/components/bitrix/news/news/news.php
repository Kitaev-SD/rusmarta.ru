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
	if($arParams['USE_RSS'] !== 'N')
		CNext::ShowRSSIcon($arResult['FOLDER'].$arResult['URL_TEMPLATES']['rss']);
	?>

	<?$arItems = CNextCache::CIBLockElement_GetList(array('SORT' => 'ASC', 'NAME' => 'ASC', 'CACHE' => array('TAG' => CNextCache::GetIBlockCacheTag($arParams['IBLOCK_ID']))), $arItemFilter, false, false, array('ID', 'NAME', 'ACTIVE_FROM'));
	$arYears = array();
	if($arItems)
	{
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
			if($arParams['USE_FILTER'] != 'N')
			{
				rsort($arYears);
				$bHasYear = (isset($_GET['year']) && (int)$_GET['year']);
				$year = ($bHasYear ? (int)$_GET['year'] : 0);?>
				<div class="head-block top">
					<div class="bottom_border"></div>
					<div class="item-link <?=($bHasYear ? '' : 'active');?>">
						<div class="title">
							<?if($bHasYear):?>
								<a class="btn-inline black" href="<?=$arResult['FOLDER'];?>"><?=GetMessage('ALL_TIME');?></a>
							<?else:?>
								<span class="btn-inline black"><?=GetMessage('ALL_TIME');?></span>
							<?endif;?>
						</div>
					</div>
					<?foreach($arYears as $value):
						$bSelected = ($bHasYear && $value == $year);?>
						<div class="item-link <?=($bSelected ? 'active' : '');?>">
							<div class="title btn-inline black">
								<?if($bSelected):?>
									<span class="btn-inline black"><?=$value;?></span>
								<?else:?>
									<a class="btn-inline black" href="<?=$APPLICATION->GetCurPageParam('year='.$value, array('year'));?>"><?=$value;?></a>
								<?endif;?>
							</div>
						</div>
					<?endforeach;?>
				</div>
				<?
				if($bHasYear)
				{
					$GLOBALS[$arParams["FILTER_NAME"]][] = array(
						">=DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".$year, "DD.MM.YYYY"),
						"<DATE_ACTIVE_FROM" => ConvertDateTime("01.01.".($year+1), "DD.MM.YYYY"),
					);
				}?>
			<?}
		}
	}?>

	<?global $arTheme, $isMenu;?>

	<?if(!$isMenu):?>
		<div class="sub_container fixed_wrapper">
		<div class="row">
			<div class="col-md-9">
	<?endif;?>
	<?if((isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest") || (strtolower($_REQUEST['ajax']) == 'y'))
	{
		$APPLICATION->RestartBuffer();
	}?>
	<?// section elements?>
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
			<div class="col-md-3  with-padding-left hidden-xs hidden-sm">
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
<?endif;?>