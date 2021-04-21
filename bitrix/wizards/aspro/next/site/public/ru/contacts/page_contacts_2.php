<?
$bUseMap = CNext::GetFrontParametrValue('CONTACTS_USE_MAP', SITE_ID) != 'N';
$bUseFeedback = CNext::GetFrontParametrValue('CONTACTS_USE_FEEDBACK', SITE_ID) != 'N';
?>

<?CNext::ShowPageType('page_title');?>

<div class="contacts maxwidth-theme" itemscope itemtype="http://schema.org/Organization">
	<div class="row">
		<div class="<?=($bUseMap ? 'col-md-4' : 'col-md-12')?>">
			<div>
				<span itemprop="description"><?$APPLICATION->IncludeFile(SITE_DIR."include/contacts-about.php", Array(), Array("MODE" => "html", "NAME" => "Contacts about"));?></span>
			</div>
			<br />
			<br />
			<table>
				<tbody>
					<?CNext::showContactAddr('Адрес', false);?>
					<?CNext::showContactPhones('Телефон', false);?>
					<?CNext::showContactEmail('E-mail', false);?>
					<?CNext::showContactSchedule('Режим работы', false);?>
				</tbody>
			</table>
		</div>
		<?if($bUseMap):?>
			<div class="col-md-8">
				<?$APPLICATION->IncludeFile("/include/contacts-site-map.php", Array(), Array("MODE" => "html", "TEMPLATE" => "include_area.php", "NAME" => "Карта"));?>
			</div>
		<?endif;?>
	</div>

	<?//hidden text for validate microdata?>
	<div class="hidden">
		<?global $arSite;?>
		<span itemprop="name"><?=$arSite["NAME"];?></span>
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