<?
$bUseMap = CNext::GetFrontParametrValue('CONTACTS_USE_MAP', SITE_ID) != 'N';
$bUseFeedback = CNext::GetFrontParametrValue('CONTACTS_USE_FEEDBACK', SITE_ID) != 'N';
?>
<div itemscope itemtype="http://schema.org/Organization">
	<?if($bUseMap):?>
		<div class="contacts-page-map">
			<?$APPLICATION->IncludeFile("/include/contacts-site-map.php", Array(), Array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => "Карта"));?>
		</div>
	<?endif;?>

	<div class="contacts contacts-page-map-overlay maxwidth-theme">
		<div class="contacts-wrapper">
			<div class="row">
				<div class="col-md-3 col-sm-6">
				<?CNext::showContactAddr('Адрес');?>
			</div>
			<div class="col-md-3 col-sm-6">
				<?CNext::showContactPhones('Телефон');?>
			</div>
			<div class="col-md-3 col-sm-6">
				<?CNext::showContactEmail('E-mail');?>
			</div>
			<div class="col-md-3 col-sm-6">
				<?CNext::showContactSchedule('Режим работы');?>
			</div>
			</div>
		</div>
	</div>

	<?//hidden text for validate microdata?>
	<div class="hidden">
		<?global $arSite;?>
		<span itemprop="name"><?=$arSite["NAME"];?></span>
	</div>

	<div class="row1">
		<div class="contacts maxwidth-theme <?=($bUseMap ? 'top-cart' : '');?>">
			<div class="cols-md-12" itemprop="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/contacts-about.php", Array(), Array("MODE" => "html", "NAME" => "Contacts about"));?>
			</div>
		</div>
	</div>
</div>
<?if($bUseFeedback):?>
	<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("contacts-form-block");?>
	<?global $arTheme;?>
	<?$APPLICATION->IncludeComponent("bitrix:form.result.new", "inline",
		Array(
			"WEB_FORM_ID" => "#FORM_FEEDBACK_ID#",
			"IGNORE_CUSTOM_TEMPLATE" => "N",
			"USE_EXTENDED_ERRORS" => "Y",
			"SEF_MODE" => "N",
			"CACHE_TYPE" => "A",
			"CACHE_TIME" => "3600000",
			"LIST_URL" => "",
			"EDIT_URL" => "",
			"SUCCESS_URL" => "?send=ok",
			"SHOW_LICENCE" => $arTheme["SHOW_LICENCE"]["VALUE"],
			"HIDDEN_CAPTCHA" => CNext::GetFrontParametrValue('HIDDEN_CAPTCHA'),
			"CHAIN_ITEM_TEXT" => "",
			"CHAIN_ITEM_LINK" => "",
			"VARIABLE_ALIASES" => Array(
				"WEB_FORM_ID" => "WEB_FORM_ID",
				"RESULT_ID" => "RESULT_ID"
			)
		)
	);?>
	<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("contacts-form-block", "");?>
<?endif;?>