<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?use \Bitrix\Main\Localization\Loc;?>

<?$frame = $this->createFrame()->begin(Loc::getMessage('REVIEWS_LOAD'));?>
<?if($arResult["ERROR"]):?>
	<div class="error">
		<?if(is_array($arResult["ERROR"])):?>
			<?foreach($arResult["ERROR"] as $error):?>
				<?ShowError($error);?>
			<?endforeach;?>
		<?else:?>
			<?ShowError($arResult["ERROR"]);?>
		<?endif;?>
	</div>
<?elseif($arResult["ITEMS"]):?>
	<div class="items" data-counts="<?=$arResult["TOTAL_ITEMS"];?>">
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<div class="item" itemprop="review" itemscope="itemscope" itemtype="http://schema.org/Review">
				<div class="top_block_wrapper">
					<div class="n-product-review-user_profile">
						<?if($arItem["anonymous"]):?>
							<span class="n-product-review-user_name" itemprop="author">
								<?=Loc::getMessage('ANONYMOUS_USER');?>
							</span>
						<?else:?>
							<?if($arItem["authorInfo"] && (isset($arItem["authorInfo"]["avatarUrl"]) && $arItem["authorInfo"]["avatarUrl"])):?>
								<div class="img">
									<img class="n-product-review-user_avatar" src="<?=$arItem["authorInfo"]["avatarUrl"]?>" alt="<?=$arItem["author"]?>">
								</div>
							<?endif;?>
							<span class="n-product-review-user_name" itemprop="author"><?=$arItem["author"]?></span>
							<?if($arItem["authorInfo"]["grades"] > 1):?>
								<span class="number_reviews"><?=\Aspro\Functions\CAsproNext::declOfNum($arItem["authorInfo"]["grades"], array(Loc::getMessage("ONEW_REVIEW"), Loc::getMessage("TWO_REVIEW"), Loc::getMessage("THREE_REVIEW")));?></span>
							<?endif;?>
						<?endif;?>
					</div>
				</div>
				<div class="middle_block_wrapper">
					<span itemprop="reviewRating" itemscope="itemscope" itemtype="http://schema.org/Rating">
					<meta itemprop="ratingValue" content="1"><meta itemprop="bestRating" content="5"></span>
					<div class="rating hint i-bem rating_size_m rating_border_yes" date-rate="<?=$arItem["grade"];?>">
						<div class="rating_value"><?=$arItem["grade"]?></div><div class="rating__corner"><div class="rating__triangle"></div></div>
					</div>
					<span class="n-product-review-item__rating-label"><?=Loc::getMessage("YMGRADE_".$arItem["grade"])?></span>
					<span class="n-product-review-item__delivery"><?=Loc::getMessage("USAGE_TIME", array("#TIME#" => \Aspro\Functions\CAsproNext::formatUsageTime($arItem["usageTime"])))?></span>
				</div>
				<?if($arItem["pro"]):?>
					<div class="middle_block_wrapper">
						<div class="n-product-review-item__title"><?=Loc::getMessage("YMPROFIT")?></div>
						<div class="n-product-review-item__text"><?=$arItem["pro"];?></div>
					</div>
				<?endif;?>
				<?if($arItem["contra"]):?>
					<div class="middle_block_wrapper">
						<div class="n-product-review-item__title"><?=Loc::getMessage("YMFAIL")?></div>
						<div class="n-product-review-item__text"><?=$arItem["contra"];?></div>
					</div>
				<?endif;?>
				<?if($arItem["text"]):?>
					<div class="middle_block_wrapper">
						<div class="n-product-review-item__title"><?=Loc::getMessage("YMCOMMENT")?></div>
						<div class="n-product-review-item__text"><?=$arItem["text"];?></div>
					</div>
				<?endif;?>
				<div class="n-product-review-item__footer">
					<?
					$dateFormat = "j F Y";
					if(date("Y") == CIBlockFormatProperties::DateFormat("Y", $arItem["date"]))
						$dateFormat = "j F";
					$dateReview = CIBlockFormatProperties::DateFormat($dateFormat, $arItem["date"]);
					?>
					<span class="n-product-review-item__date-region"><?=$dateReview;?></span>
					<div class="n-review-voting manotice manotice_type_popup i-bem n-review-voting_active_yes manotice_js_inited">
						<span class="n-review-voting__tip"><?=Loc::getMessage("YM_USEFUL");?></span>
						<div class="n-review-voting__plus"><span class="image" title="<?=Loc::getMessage("YES");?>"></span><span class="n-review-voting__num"><?=(int)$arItem["agree"];?></span></div>
						<div class="n-review-voting__minus"><span class="image" title="<?=Loc::getMessage("NO");?>"></span><span class="n-review-voting__num"><?=(int)$arItem["reject"];?></span></div>
					</div>
				</div>
			</div>
		<?endforeach;?>
	</div>
<?endif;?>
<?$frame->end();?>