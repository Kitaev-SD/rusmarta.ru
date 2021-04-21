<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?$this->setFrameMode(true);?>
<?if($arResult['ITEMS']):?>
	<div class="top_big_one_banner <?=($arResult['HAS_CHILD_BANNERS'] ? 'with_childs' : '');?>" style="overflow: hidden;">
		<?include_once('slider.php');?>
	</div>
<?endif;?>