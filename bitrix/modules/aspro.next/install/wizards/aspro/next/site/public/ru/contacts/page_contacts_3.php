<?$bUseFeedback = CNext::GetFrontParametrValue('CONTACTS_USE_FEEDBACK', SITE_ID) != 'N';?>
<?$APPLICATION->ShowViewContent('yandex_map');?>
<div itemscope itemtype="http://schema.org/Organization">
	<div class="contacts contacts-page-map-inline  maxwidth-theme">
		<div class="contacts-wrapper">
			<div class="row">
				<div class="<?=($bUseFeedback ? 'col-md-4' : 'col-md-6')?>">
					<?CNext::showContactAddr('Центральный офис');?>
				</div>
				<div class="<?=($bUseFeedback ? 'col-md-3' : 'col-md-3')?>">
					<?CNext::showContactPhones('Справочная служба');?>
				</div>
				<div class="<?=($bUseFeedback ? 'col-md-3' : 'col-md-3')?>">
					<?CNext::showContactEmail('E-mail');?>
				</div>
				<?if($bUseFeedback):?>
					<div class="col-md-2">
						<button data-event="jqm" data-param-form_id="ASK" data-name="order_services" class="btn btn-default white btn-block"><span>Задать вопрос</span></button>
					</div>
				<?endif;?>
			</div>

			<?//hidden text for validate microdata?>
			<div class="hidden">
				<?global $arSite;?>
				<span itemprop="name"><?=$arSite["NAME"];?></span>
			</div>
		</div>
	</div>

	<div class="contacts maxwidth-theme">
		<div class="row">
			<div class="col-md-12" itemprop="description">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/contacts-regions-title.php", Array(), Array("MODE" => "html", "NAME" => "Regions"));?>
			</div>
		</div>
	</div>
</div>


<?$APPLICATION->IncludeComponent(
	"bitrix:news", 
	"contacts", 
	array(
		"IBLOCK_TYPE" => "aspro_next_content",
		"IBLOCK_ID" => "#IBLOCK_SHOPS_ID#",
		"NEWS_COUNT" => "20",
		"USE_SEARCH" => "N",
		"USE_RSS" => "Y",
		"USE_RATING" => "N",
		"USE_CATEGORIES" => "N",
		"USE_FILTER" => "Y",
		"SORT_BY1" => "ACTIVE_FROM",
		"SORT_ORDER1" => "DESC",
		"SORT_BY2" => "SORT",
		"SORT_ORDER2" => "ASC",
		"CHECK_DATES" => "Y",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "#SITE_DIR#contacts/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "100000",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "N",
		"SET_TITLE" => "Y",
		"SET_STATUS_404" => "N",
		"INCLUDE_IBLOCK_INTO_CHAIN" => "N",
		"ADD_SECTIONS_CHAIN" => "N",
		"USE_PERMISSIONS" => "N",
		"PREVIEW_TRUNCATE_LEN" => "",
		"LIST_ACTIVE_DATE_FORMAT" => "j F Y",
		"LIST_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_TEXT",
			2 => "PREVIEW_PICTURE",
			3 => "DATE_ACTIVE_FROM",
			4 => "",
		),
		"LIST_PROPERTY_CODE" => array(
			0 => "",
			1 => "PERIOD",
			2 => "REDIRECT",
			3 => "",
		),
		"HIDE_LINK_WHEN_NO_DETAIL" => "N",
		"DISPLAY_NAME" => "N",
		"META_KEYWORDS" => "-",
		"META_DESCRIPTION" => "-",
		"BROWSER_TITLE" => "-",
		"DETAIL_ACTIVE_DATE_FORMAT" => "j F Y",
		"DETAIL_FIELD_CODE" => array(
			0 => "PREVIEW_TEXT",
			1 => "DETAIL_TEXT",
			2 => "DETAIL_PICTURE",
			3 => "DATE_ACTIVE_FROM",
			4 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "EMAIL",
			1 => "ADDRESS",
			2 => "MAP",
			3 => "METRO",
			4 => "SCHEDULE",
			5 => "PHONE",
			6 => "MORE_PHOTOS",
			7 => "PHOTOS",
			8 => "DOCUMENTS",
			9 => "",
		),
		"DETAIL_DISPLAY_TOP_PAGER" => "N",
		"DETAIL_DISPLAY_BOTTOM_PAGER" => "Y",
		"DETAIL_PAGER_TITLE" => "Страница",
		"DETAIL_PAGER_TEMPLATE" => "",
		"DETAIL_PAGER_SHOW_ALL" => "Y",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Новости",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"IMAGE_POSITION" => "left",
		"USE_SHARE" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"USE_REVIEW" => "N",
		"ADD_ELEMENT_CHAIN" => "Y",
		"SHOW_DETAIL_LINK" => "Y",
		"S_ASK_QUESTION" => "",
		"S_ORDER_SERVISE" => "",
		"T_GALLERY" => "",
		"T_DOCS" => "",
		"T_GOODS" => "",
		"T_SERVICES" => "",
		"T_STUDY" => "",
		"COMPONENT_TEMPLATE" => "contacts",
		"SET_LAST_MODIFIED" => "N",
		"T_VIDEO" => "",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"NUM_NEWS" => "20",
		"NUM_DAYS" => "30",
		"YANDEX" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"SECTIONS_TYPE_VIEW" => "sections_1",
		"SECTION_TYPE_VIEW" => "section_1",
		"SECTION_ELEMENTS_TYPE_VIEW" => "list_elements_2",
		"ELEMENT_TYPE_VIEW" => "element_1",
		"S_ORDER_SERVICE" => "",
		"T_PROJECTS" => "",
		"T_REVIEWS" => "",
		"T_STAFF" => "",
		"IMAGE_CATALOG_POSITION" => "left",
		"SHOW_SECTION_PREVIEW_DESCRIPTION" => "Y",
		"SHOW_SECTION_DESCRIPTION" => "Y",
		"LINE_ELEMENT_COUNT" => "3",
		"LINE_ELEMENT_COUNT_LIST" => "3",
		"SHOW_CHILD_SECTIONS" => "N",
		"GALLERY_TYPE" => "small",
		"INCLUDE_SUBSECTIONS" => "Y",
		"FORM_ID_ORDER_SERVISE" => "",
		"T_NEXT_LINK" => "",
		"T_PREV_LINK" => "",
		"SHOW_NEXT_ELEMENT" => "N",
		"IMAGE_WIDE" => "N",
		"SHOW_FILTER_DATE" => "Y",
		"FILTER_NAME" => "arFilterNews",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"DETAIL_STRICT_SECTION_CHECK" => "N",
		"VIEW_TYPE" => "list",
		"SHOW_TABS" => "Y",
		"SHOW_ASK_QUESTION_BLOCK" => "Y",
		"SHOW_TOP_MAP" => "Y",
		"STRICT_SECTION_CHECK" => "N",
		"SEF_URL_TEMPLATES" => array(
			"news" => "",
			"section" => "",
			"detail" => "stores/#ELEMENT_ID#/",
			"rss" => "rss/",
			"rss_section" => "#SECTION_ID#/rss/",
		)
	),
	false
);?>