<?function ShowOptions($optionCode, $arOption, $arParentOption = array()){
	$isRow = (isset($arOption['IS_ROW']) && $arOption['IS_ROW'] == 'Y');
	$isPreview = (isset($arOption['PREVIEW']) && $arOption['PREVIEW']);
	if($arOption['TYPE'] == 'checkbox'):?>
		<?$isChecked = ($arOption['VALUE'] == 'Y');?>
		<?if($isRow):?>
			<div class="<?=(isset($arOption['ROW_CLASS']) && $arOption['ROW_CLASS'] ? $arOption['ROW_CLASS'] : '');?>">
				<div class="link-item animation-boxs <?=(isset($arOption['POSITION_BLOCK']) && $arOption['POSITION_BLOCK'] ? $arOption['POSITION_BLOCK'] : '');?> <?=(!$isChecked ? 'disabled' : '');?>">
		<?endif;?>
			<?ob_start();?>
				<?if((!isset($arOption['HIDE_TITLE']) || $arOption['HIDE_TITLE'] != 'Y') && (isset($arOption['TITLE']) && $arOption['TITLE'])):?><span><?=$arOption['TITLE']?></span><?endif;?>
			<?$title = ob_get_contents();
			ob_end_clean();?>

			<?ob_start();?>
				<input type="checkbox" id="<?=$optionCode?>" <?=((isset($arOption['SMALL_TOGGLE']) && $arOption['SMALL_TOGGLE']) ? "data-height=22" : "");?> class="custom-switch" name="<?=$optionCode?>" value="<?=$arOption['VALUE']?>" <?=($isChecked ? "checked" : "");?> />
			<?$input = ob_get_contents();
			ob_end_clean();?>
			<?if(isset($arOption['IMG']) && $arOption['IMG']):?>
				<?=$title;?>
				<div class="img"><img src="<?=$arOption['IMG'];?>" alt="<?=$arOption['TITLE']?>" title="<?=$arOption['TITLE']?>"/></div>
				<div class="input"><?=$input;?></div>
			<?elseif(isset($arOption['GROUP']) && $arOption['GROUP']):?>
				<span class="inner-table-block"><?=$title;?></span>
				<span class="inner-table-block"><?=$input;?></span>
			<?else:?>
				<?=$input;?>
			<?endif;?>
		<?if($isRow):?>
				</div>
			</div>
		<?endif;?>
	<?elseif($arOption['TYPE'] == 'selectbox' || $arOption['TYPE'] == 'multiselectbox'):?>
		<input type="hidden" id="<?=$optionCode?>" name="<?=$optionCode?>" value="<?=$arOption['VALUE']?>" />
		<?if(isset($arOption['GROUPS']) && $arOption['GROUPS'] == 'Y'):?>
			<?
			$arGroups = array();
			foreach($arOption['LIST'] as $variantCode => $arVariant)
			{
				if(isset($arVariant['HIDE']) && $arVariant['HIDE'] == 'Y') continue;
				$group = ((isset($arVariant['GROUP']) && $arVariant['GROUP']) ? $arVariant['GROUP'] : GetMessage('NO_GROUP'));
				$arGroups[$group]['LIST'][$variantCode] = array(
					'TITLE' => ((isset($arVariant['VALUE']) && $arVariant['VALUE']) ? $arVariant['VALUE'] : $arVariant['TITLE']),
					'CURRENT' => ((isset($arVariant['CURRENT']) && $arVariant['CURRENT']) ? $arVariant['CURRENT'] : 'N')
				);
			}
			if($arGroups)
			{
				foreach($arGroups as $key => $arGroup)
				{?>
					<div class="group">
						<div class="title"><?=$key;?></div>
						<div class="values">
							<div class="inner-values">
								<?foreach($arGroup['LIST'] as $variantCode => $arVariant):?>
									<span data-option-id="<?=$optionCode?>" data-option-value="<?=$variantCode?>" class="link-item animation-boxs <?=$arVariant['CURRENT'] == 'Y' ? 'current' : ''?>">
										<?if(isset($arVariant['IMG']) && $arVariant['IMG']):?>
											<span><img src="<?=$arVariant['IMG'];?>" alt="<?=$arVariant['TITLE']?>" title="<?=$arVariant['TITLE']?>"/></span>
										<?endif;?>
										<?if(!isset($arVariant['HIDE_TITLE']) || $arVariant['HIDE_TITLE'] != 'Y'):?><span><?=$arVariant['TITLE']?></span><?endif;?>
									</span>
								<?endforeach;?>
							</div>
						</div>
					</div>
				<?}
			}?>	
		<?else:?>
			<?if($isRow):?>
				<div class="rows">
			<?endif;?>
			<?foreach($arOption['LIST'] as $variantCode => $arVariant):
				if(isset($arVariant['HIDE']) && $arVariant['HIDE'] == 'Y') continue;?>
				<?if($isRow):?>
					<div class="<?=(isset($arVariant['ROW_CLASS']) && $arVariant['ROW_CLASS'] ? $arVariant['ROW_CLASS'] : '');?>">
				<?endif;?>
				<span <?=($isPreview && (isset($arOption['PREVIEW']['SCROLL_BLOCK']) && $arOption['PREVIEW']['SCROLL_BLOCK']) ? "data-option-type='".$arOption['PREVIEW']['SCROLL_BLOCK']."'" : "");?> <?=($isPreview && isset($arOption['PREVIEW']['URL']) ? "data-option-url='".SITE_DIR.$arOption['PREVIEW']['URL']."'" : "");?> data-option-id="<?=$optionCode?>" data-option-value="<?=$variantCode?>" class="link-item animation-boxs <?=(isset($arVariant['POSITION_BLOCK']) && $arVariant['POSITION_BLOCK'] ? $arVariant['POSITION_BLOCK'] : '');?> <?=$arVariant['CURRENT'] == 'Y' ? 'current' : ''?>">

					<?ob_start();?>
						<?if((!isset($arVariant['HIDE_TITLE']) || $arVariant['HIDE_TITLE'] != 'Y') && (isset($arVariant['TITLE']) && $arVariant['TITLE'])):?><span><?=$arVariant['TITLE']?></span><?endif;?>
					<?$title = ob_get_contents();
					ob_end_clean();?>

					<?ob_start();?>
						<span><img src="<?=$arVariant['IMG'];?>" alt="<?=$arVariant['TITLE']?>" title="<?=$arVariant['TITLE']?>"/></span>
					<?$img = ob_get_contents();
					ob_end_clean();?>

					<?if(isset($arVariant['IMG']) && $arVariant['IMG']):?>
						<?if(isset($arVariant['POSITION_TITLE']) && $arVariant['POSITION_TITLE']):?>
							<?if($arVariant['POSITION_TITLE'] == 'left'):?>
								<span class="inner-table-block" <?=((isset($arVariant['TITLE_WIDTH']) && $arVariant['TITLE_WIDTH']) ? "style='width:".$arVariant['TITLE_WIDTH']."'" : "");?>><?=$title;?></span>
								<span class="inner-table-block"><?=$img;?></span>
							<?endif;?>
						<?else:?>
							<span class="title"><?=$title;?></span>
							<?=$img;?>
						<?endif;?>
					<?else:?>
						<?=$title;?>
					<?endif;?>

				</span>
				<?if($isRow):?>
					</div>
				<?endif;?>
			<?endforeach;?>
			<?if($isRow):?>
				</div>
			<?endif;?>
		<?endif;?>
	<?elseif($arOption['TYPE'] == 'text'):?>
		<input type="text" class="form-control" id="<?=$optionCode?>" <?=((isset($arOption['PARAMS']) && isset($arOption['PARAMS']['WIDTH'])) ? 'style="width:'.$arOption['PARAMS']['WIDTH'].'"' : '');?> name="<?=$optionCode?>" value="<?=$arOption['VALUE']?>" />
	<?elseif($arOption['TYPE'] == 'textarea'):?>
		<?// text here?>
	<?endif;?>
<?}?>

<?function ShowOptionsTitle($optionCode, $arOption){?>
	<?if(!isset($arOption['HIDE_TITLE']) || $arOption['HIDE_TITLE'] != 'Y'):?>
		<div class="title <?=((isset($arOption['TOP_BORDER']) && $arOption['TOP_BORDER'] == 'Y') ? 'with-border' : '');?>"><?=$arOption['TITLE'];?><?=((isset($arOption['HINT']) && $arOption['HINT']) ? "<span class='tooltip-link' data-placement='bottom' data-trigger='click' data-toggle='tooltip' data-original-title='".$arOption['HINT']."'>?</span>" : "");?></div>
	<?endif;?>
<?}?>