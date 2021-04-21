<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;

if(!empty($arParams["ELEMENT_AREA_ID"])):?>
	<div class="reviews-collapse reviews-minimized">
		<a class="btn_buy apuo reviews-collapse-link" id="catalogReviewAnch" href="javascript:void(0)" rel="nofollow"><i class="fa fa-pencil"></i><span class="full"><?=Loc::getMessage("CATALOG_REVIEWS_TITLE_FULL")?></span><span class="short"><?=Loc::getMessage("CATALOG_REVIEWS_TITLE_SHORT")?></span></a>
	</div>
	<?$arJSParams = array(
		"iblockType" => $arParams["IBLOCK_TYPE"],
		"iblockId" => $arParams["IBLOCK_ID"],
		"elementId" => $arParams["ELEMENT_ID"],
		"jsId" => $arParams["ELEMENT_AREA_ID"],
		"commentUrl" => $APPLICATION->GetCurPage(),
		"cacheType" => $arParams["CACHE_TYPE"],
		"cacheTime" => $arParams["CACHE_TIME"],		
		"popupPath" => $this->GetFolder()."/popup.php",
		"messages" => array(
			"POPUP_TITLE" => Loc::getMessage("CATALOG_REVIEWS_TITLE_FULL")
		)
	);?>
	<script type="text/javascript">		
		new BX.Catalog.Reviews(<?=CUtil::PhpToJSObject($arJSParams, false, true, true)?>);
	</script>
<?endif;

if(count($arResult["ITEMS"]) <= 0)
	return;?>
	
<div class="catalog-reviews-list" data-count="<?=$arResult['ITEMS_COUNT']?>">
	<?foreach($arResult["ITEMS"] as $key => $arElement):?>
		<div class="catalog-review">
			<div class="catalog-review__col catalog-review__userpic-wrap">
				<div class="catalog-review__userpic">
					<?if(is_array($arElement["CREATED_USER_PERSONAL_PHOTO"])):?>
						<img src="<?=$arElement['CREATED_USER_PERSONAL_PHOTO']['SRC']?>" width="<?=$arElement['CREATED_USER_PERSONAL_PHOTO']['WIDTH']?>" height="<?=$arElement['CREATED_USER_PERSONAL_PHOTO']['HEIGHT']?>" alt="userpic" title="userpic" />
					<?else:?>
						<img src="<?=SITE_TEMPLATE_PATH?>/images/userpic.jpg" width="57" height="57" alt="userpic" title="userpic" />
					<?endif;?>
				</div>
			</div>
			<div class="catalog-review__col">
				<span class="catalog-review__name"><?=$arElement["PROPERTIES"]["USER_ID"]["VALUE"]?></span>
				<span class="catalog-review__date"><?=($arElement["DATE_ACTIVE_FROM"] ? $arElement["DATE_ACTIVE_FROM"] : $arElement["DATE_CREATE"])?></span>
				<span class="catalog-review__text"><?=$arElement["DETAIL_TEXT"]?></span>
				<?if(!empty($arElement['PREVIEW_TEXT'])) {?>
					<div class="catalog-review__report">
						<div class="catalog-review__report-title">
							<?=Loc::getMessage('CATALOG_REVIEWS_TITLE_REPORT', array('#SITE#' => SITE_SERVER_NAME))?>
						</div>
						<span class="catalog-review__report-text">
							<?=$arElement['PREVIEW_TEXT']?>
						</span>
					</div>
				<?}?>
			</div>
		</div>
	<?endforeach;?>
</div>

<?=$arResult["NAV_STRING"];?>