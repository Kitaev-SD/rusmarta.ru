<div class="mobilemenu-v2 downdrop scroller">
	<div class="wrap">
		<?$APPLICATION->IncludeComponent(
			"bitrix:menu",
			"top_mobile",
			Array(
				"COMPONENT_TEMPLATE" => "top_mobile",
				"MENU_CACHE_TIME" => "3600000",
				"MENU_CACHE_TYPE" => "A",
				"MENU_CACHE_USE_GROUPS" => "N",
				"MENU_CACHE_GET_VARS" => array(
				),
				"DELAY" => "N",
				"MAX_LEVEL" => \Bitrix\Main\Config\Option::get("aspro.next", "MAX_DEPTH_MENU", 2),
				"ALLOW_MULTI_SELECT" => "Y",
				"ROOT_MENU_TYPE" => "top_content_multilevel",
				"CHILD_MENU_TYPE" => "left",
				"CACHE_SELECTED_ITEMS" => "N",
				"ALLOW_MULTI_SELECT" => "Y",
				"USE_EXT" => "Y"
			)
		);?>
		<?
		// show regions
		CNext::ShowMobileRegions();
		?>
	</div>
</div>