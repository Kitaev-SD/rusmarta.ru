<?
global $arTheme, $arRegion;
if($arRegion)
	$bPhone = ($arRegion['PHONES'] ? true : false);
else
	$bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
?>
<div class="maxwidth-theme">
	<div class="inner-table-block top-block-item pull-left">
		<div class="phone-block">
			<?if($bPhone):?>
				<div class="inline-block">
					<?CNext::ShowHeaderPhones();?>
				</div>
			<?endif?>
			<?if($arTheme['SHOW_CALLBACK']['VALUE'] == 'Y'):?>
				<div class="inline-block">
					<span class="callback-block animate-load colored" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?=GetMessage("CALLBACK")?></span>
				</div>
			<?endif?>
		</div>
	</div>
	<div class="top-block-item pull-right">
		<?=CNext::ShowBasketWithCompareLink('', '', true, 'top-btn inner-table-block', true);?>
	</div>
</div>