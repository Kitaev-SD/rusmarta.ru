<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>
<?if($arResult['SECTIONS']):?>
	<div class="sections_wrapper <?=(isset($arParams["COMPACT_VIEW_MOBILE"]) && $arParams["COMPACT_VIEW_MOBILE"]=="Y") ? "compact-view-mobile" : ""?>">
		<?if($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"]):?>
			<div class="top_block">
				<h3 class="title_block"><?=$arParams["TITLE_BLOCK"];?></h3>
				<a href="<?=SITE_DIR.$arParams["ALL_URL"];?>"><?=$arParams["TITLE_BLOCK_ALL"] ;?></a>
			</div>
		<?endif;?>
		<?include_once($arParams['TEMPLATE'].'.php');?>
	</div>
<?endif;?>