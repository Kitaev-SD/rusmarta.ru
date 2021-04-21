<?define("NOT_CHECK_PERMISSIONS", true);

$this->setFrameMode(true);

use Bitrix\Main\Localization\Loc;?>

<div class="info">
	<div class="image">
		<?if(is_array($arResult["ELEMENT"]["PREVIEW_PICTURE"])):?>
			<img src="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['SRC']?>" width="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arResult['ELEMENT']['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arResult['ELEMENT']['NAME']?>" title="<?=$arResult['ELEMENT']['NAME']?>" />
		<?else:?>
			<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arResult['ELEMENT']['NAME']?>" title="<?=$arResult['ELEMENT']['NAME']?>" />
		<?endif?>
	</div>
	<div class="name"><?=$arResult["ELEMENT"]["NAME"]?></div>
</div>
<form action="<?=$this->__component->__path?>/script.php" id="<?=$arParams['ELEMENT_AREA_ID']?>_form">
	<span class="alert"></span>
	<?foreach($arParams["PROPERTIES"] as $arCode):?>
		<div class="row">
			<div class="span1"><?=Loc::getMessage("CATALOG_REVIEWS_".$arCode)?><span class="mf-req">*</span></div>
			<div class="span2">
				<?if($arCode != "MESSAGE"):?>
					<input type="text" name="<?=$arCode?>" value="<?=($arCode == 'NAME' ? $arResult['USER']['NAME'] : '');?>" />
				<?else:?>
					<textarea name="<?=$arCode?>" rows="3"></textarea>
				<?endif;?>
			</div>
		</div>
	<?endforeach;
	if($arParams["USE_CAPTCHA"] == "Y"):?>
		<div class="row">
			<div class="span1"><?=Loc::getMessage("CATALOG_REVIEWS_CAPTCHA")?><span class="mf-req">*</span></div>
			<div class="span2">					
				<input type="text" name="CAPTCHA_WORD" maxlength="5" value="" />			
				<img src="" width="127" height="30" alt="CAPTCHA" style="display:none;" />
				<input type="hidden" name="CAPTCHA_SID" value="" />					
			</div>
		</div>
	<?endif;?>
	<input type="hidden" name="PARAMS_STRING" value="<?=$arParams['PARAMS_STRING']?>" />
	<input type="hidden" name="IBLOCK_STRING" value="<?=$arResult['IBLOCK']['STRING']?>" />
	<input type="hidden" name="ELEMENT_STRING" value="<?=$arResult['ELEMENT']['STRING']?>" />	
	<div class="submit">
		<button type="button" id="<?=$arParams['ELEMENT_AREA_ID']?>_btn" class="btn_buy popdef"><?=Loc::getMessage("CATALOG_REVIEWS_SEND")?></button>
	</div>
</form>

<script type="text/javascript">
	//FORM_SUBMIT//
	BX.bind(BX("<?=$arParams['ELEMENT_AREA_ID']?>_btn"), "click", BX.delegate(BX.ReviewFormSubmit, BX));
</script>