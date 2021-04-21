<?
$moduleClass = "CElektroinstrument";
$moduleID = "altop.elektroinstrument";
IncludeModuleLangFile(__FILE__);

//initialize module parametrs list and default values
$moduleClass::$arParametrsList = array(
	"MAIN" => array(
		"TITLE" => GetMessage("MAIN_OPTIONS"),
		"OPTIONS" => array(
			"SHOW_SETTINGS_PANEL" => array(
				"TITLE" => GetMessage("SHOW_SETTINGS_PANEL"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"COLOR_SCHEME" => array(
				"TITLE" => GetMessage("COLOR_SCHEME"), 
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"MAROON" => array("COLOR" => "#b21001", "TITLE" => GetMessage("COLOR_SCHEME_MAROON")),
					"ORANGE" => array("COLOR" => "#ff6634", "TITLE" => GetMessage("COLOR_SCHEME_ORANGE")),
					"YELLOW" => array("COLOR" => "#fde138", "TITLE" => GetMessage("COLOR_SCHEME_YELLOW")),
					"STRONG_YELLOW" => array("COLOR" => "#9dc21a", "TITLE" => GetMessage("COLOR_SCHEME_STRONG_YELLOW")),
					"GREEN" => array("COLOR" => "#349933", "TITLE" => GetMessage("COLOR_SCHEME_GREEN")),
					"CYAN" => array("COLOR" => "#1cbec1", "TITLE" => GetMessage("COLOR_SCHEME_CYAN")),
					"BLUE" => array("COLOR" => "#0198ff", "TITLE" => GetMessage("COLOR_SCHEME_BLUE")),
					"DARK_BLUE" => array("COLOR" => "#346699", "TITLE" => GetMessage("COLOR_SCHEME_DARK_BLUE")),
					"STRONG_BLUE" => array("COLOR" => "#2c5aba", "TITLE" => GetMessage("COLOR_SCHEME_STRONG_BLUE")),
					"VIOLET" => array("COLOR" => "#66339a", "TITLE" => GetMessage("COLOR_SCHEME_VIOLET")),
					"PINK" => array("COLOR" => "#cd3367", "TITLE" => GetMessage("COLOR_SCHEME_PINK")),
					"GRAY" => array("COLOR" => "#8b92a5", "TITLE" => GetMessage("COLOR_SCHEME_GRAY")),
					"CUSTOM" => array("COLOR" => "", "TITLE" => GetMessage("COLOR_SCHEME_CUSTOM"))
				),
				"DEFAULT" => "YELLOW",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"COLOR_SCHEME_CUSTOM" => array(
				"TITLE" => GetMessage("COLOR_SCHEME_CUSTOM"), 
				"TYPE" => "text", 
				"DEFAULT" => "#fde037",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"SITE_BACKGROUND" => array(
				"TITLE" => GetMessage("SITE_BACKGROUND"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"SITE_BACKGROUND_COLOR" => array(
				"TITLE" => GetMessage("SITE_BACKGROUND_COLOR"), 
				"TYPE" => "text", 
				"DEFAULT" => "#edeef8",
				"IN_SETTINGS_PANEL" => "N"
			),
			"SITE_BACKGROUND_PICTURE" => array(
				"TITLE" => GetMessage("SITE_BACKGROUND_PICTURE"),
				"TYPE" => "file",
				"DEFAULT" => COption::GetOptionString($moduleID, "SITE_BACKGROUND_TREE"),				
				"IN_SETTINGS_PANEL" => "N"
			),
			"SITE_BACKGROUND_REPEAT_X" => array(
				"TITLE" => GetMessage("SITE_BACKGROUND_REPEAT_X"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"SITE_BACKGROUND_REPEAT_Y" => array(
				"TITLE" => GetMessage("SITE_BACKGROUND_REPEAT_Y"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"SITE_BACKGROUND_FIXED" => array(
				"TITLE" => GetMessage("SITE_BACKGROUND_FIXED"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"CATALOG_LOCATION" => array(
				"TITLE" => GetMessage("CATALOG_LOCATION"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"LEFT" => GetMessage("CATALOG_LOCATION_LEFT"),
					"HEADER" => GetMessage("CATALOG_LOCATION_HEADER")
				),
				"DEFAULT" => "LEFT",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"CATALOG_VIEW" => array(
				"TITLE" => GetMessage("CATALOG_VIEW"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"TWO_LEVELS" => GetMessage("CATALOG_VIEW_TWO_LEVELS"),
					"FOUR_LEVELS" => GetMessage("CATALOG_VIEW_FOUR_LEVELS")
				),
				"DEFAULT" => "TWO_LEVELS",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"CART_LOCATION" => array(
				"TITLE" => GetMessage("CART_LOCATION"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"BOTTOM" => GetMessage("CART_LOCATION_BOTTOM"),
					"TOP" => GetMessage("CART_LOCATION_TOP"),
					"RIGHT" => GetMessage("CART_LOCATION_RIGHT"),
					"LEFT" => GetMessage("CART_LOCATION_LEFT")
				),
				"DEFAULT" => "BOTTOM",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"HOME_PAGE" => array(
				"TITLE" => GetMessage("HOME_PAGE"),
				"TYPE" => "multiselectbox",
				"LIST" => array(					
					"SLIDER" => GetMessage("HOME_PAGE_SLIDER"),
					"ADVANTAGES" => GetMessage("HOME_PAGE_ADVANTAGES"),
					"PROMOTIONS" => GetMessage("HOME_PAGE_PROMOTIONS"),
					"BANNERS" => GetMessage("HOME_PAGE_BANNERS"),					
					"TABS" => GetMessage("HOME_PAGE_TABS"),
					"RECOMMEND" => GetMessage("HOME_PAGE_RECOMMEND"),
					"CONTENT" => GetMessage("HOME_PAGE_CONTENT")
				),
				"DEFAULT" => array("SLIDER", "ADVANTAGES", "PROMOTIONS", "BANNERS", "TABS", "RECOMMEND", "CONTENT"),
				"IN_SETTINGS_PANEL" => "Y"
			),
			"SMART_FILTER_LOCATION" => array(
				"TITLE" => GetMessage("SMART_FILTER_LOCATION"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"VERTICAL" => GetMessage("SMART_FILTER_LOCATION_VERTICAL"),
					"HORIZONTAL" => GetMessage("SMART_FILTER_LOCATION_HORIZONTAL")
				),
				"DEFAULT" => "HORIZONTAL",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"SMART_FILTER_VISIBILITY" => array(
				"TITLE" => GetMessage("SMART_FILTER_VISIBILITY"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"EXPAND" => GetMessage("SMART_FILTER_VISIBILITY_EXPAND"),
					"COLLAPSE" => GetMessage("SMART_FILTER_VISIBILITY_COLLAPSE"),
					"DISABLE" => GetMessage("SMART_FILTER_VISIBILITY_DISABLE")
				),
				"DEFAULT" => "EXPAND",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"PRODUCT_TABLE_VIEW" => array(
				"TITLE" => GetMessage("PRODUCT_TABLE_VIEW"),
				"TYPE" => "multiselectbox",
				"LIST" => array(
					"ARTNUMBER" => GetMessage("PRODUCT_TABLE_VIEW_ARTNUMBER"),
					"RATING" => GetMessage("PRODUCT_TABLE_VIEW_RATING"),
					"PREVIEW_TEXT" => GetMessage("PRODUCT_TABLE_VIEW_PREVIEW_TEXT"),
					"OLD_PRICE" => GetMessage("PRODUCT_TABLE_VIEW_OLD_PRICE"),
					"PERCENT_PRICE" => GetMessage("PRODUCT_TABLE_VIEW_PERCENT_PRICE"),
					"MIN_PRICE" => GetMessage("PRODUCT_TABLE_VIEW_MIN_PRICE")
				),
				"DEFAULT" => array("ARTNUMBER", "RATING", "PREVIEW_TEXT", "OLD_PRICE", "PERCENT_PRICE", "MIN_PRICE"),
				"IN_SETTINGS_PANEL" => "Y"
			),
			"CATALOG_DETAIL" => array(
				"TITLE" => GetMessage("CATALOG_DETAIL"),
				"TYPE" => "multiselectbox",
				"LIST" => array(
					"ADVANTAGES" => GetMessage("CATALOG_DETAIL_ADVANTAGES"),
					"BUTTON_BOC" => GetMessage("CATALOG_DETAIL_BUTTON_BOC"),
					"BUTTON_CHEAPER" => GetMessage("CATALOG_DETAIL_BUTTON_CHEAPER"),
					"BUTTON_PAYMENTS" => GetMessage("CATALOG_DETAIL_BUTTON_PAYMENTS"),
					"BUTTON_CREDIT" => GetMessage("CATALOG_DETAIL_BUTTON_CREDIT"),
					"BUTTON_DELIVERY" => GetMessage("CATALOG_DETAIL_BUTTON_DELIVERY")
				),
				"DEFAULT" => array("ADVANTAGES", "BUTTON_BOC", "BUTTON_CHEAPER", "BUTTON_PAYMENTS", "BUTTON_CREDIT", "BUTTON_DELIVERY"),
				"IN_SETTINGS_PANEL" => "Y"
			),
			"OFFERS_VIEW" => array(
				"TITLE" => GetMessage("OFFERS_VIEW"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"PROPS" => GetMessage("OFFERS_VIEW_PROPS"),
					"LIST" => GetMessage("OFFERS_VIEW_LIST")
				),
				"DEFAULT" => "PROPS",
				"IN_SETTINGS_PANEL" => "Y"
			),
			"VENDORS_VIEW" => array(
				"TITLE" => GetMessage("VENDORS_VIEW"),
				"TYPE" => "selectbox", 
				"LIST" => array(					
					"PRODUCTS" => GetMessage("VENDORS_VIEW_PRODUCTS"),
					"SECTIONS_PRODUCTS" => GetMessage("VENDORS_VIEW_SECTIONS_PRODUCTS"),
					"SECTIONS" => GetMessage("VENDORS_VIEW_SECTIONS")
				),
				"DEFAULT" => "SECTIONS",
				"IN_SETTINGS_PANEL" => "Y"
			),		
			"GENERAL_SETTINGS" => array(
				"TITLE" => GetMessage("GENERAL_SETTINGS"),
				"TYPE" => "multiselectbox",
				"LIST" => array(					
					"PRODUCT_QUANTITY" => GetMessage("GENERAL_SETTINGS_PRODUCT_QUANTITY"),
					"BUTTON_BOC" => GetMessage("GENERAL_SETTINGS_BUTTON_BOC"),
					"PRICE_RATIO" => GetMessage("GENERAL_SETTINGS_PRICE_RATIO"),
					"OFFERS_LINK_SHOW" => GetMessage("GENERAL_SETTINGS_OFFERS_LINK_SHOW"),
					"FALLING_SNOW" => GetMessage("GENERAL_SETTINGS_FALLING_SNOW")				
				),
				"DEFAULT" => array("PRODUCT_QUANTITY", "BUTTON_BOC", "SHOW_PERSONAL_DATA"),
				"IN_SETTINGS_PANEL" => "Y"
			),
            "ORDER_MIN_PRICE" => array(
				"TITLE" => GetMessage("ORDER_MIN_PRICE"), 
				"TYPE" => "number", 
				"DEFAULT" => "",
				"IN_SETTINGS_PANEL" => "N"
			),
			"REFERENCE_PRICE" => array(
				"TITLE" => GetMessage("REFERENCE_PRICE"),
				"TYPE" => "checkbox",
				"DEFAULT" => "N",
				"IN_SETTINGS_PANEL" => "N"
			),
			"REFERENCE_PRICE_COEF" => array(
				"TITLE" => GetMessage("REFERENCE_PRICE_COEF"), 
				"TYPE" => "number", 
				"DEFAULT" => "10000",
				"IN_SETTINGS_PANEL" => "N"
			),
            "NAME_BUTTON_TO_CART" => array(
				"TITLE" => GetMessage("NAME_BUTTON_TO_CART"),
				"TYPE" => "text", 
				"DEFAULT" => GetMessage("BUTTON_TO_CART"),
				"IN_SETTINGS_PANEL" => "N"
			),
		)
	),
	"GEOLOCATION" => array(
		"TITLE" => GetMessage("GEOLOCATION_OPTIONS"),
		"OPTIONS" => array(
			"USE_GEOLOCATION" => array(
				"TITLE" => GetMessage("USE_GEOLOCATION"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"GEOLOCATION_REGIONAL_CONTACTS" => array(
				"TITLE" => GetMessage("GEOLOCATION_REGIONAL_CONTACTS"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"GEOLOCATION_DELIVERY" => array(
				"TITLE" => GetMessage("GEOLOCATION_DELIVERY"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"GEOLOCATION_ORDER_CITY" => array(
				"TITLE" => GetMessage("GEOLOCATION_ORDER_CITY"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	),
	"FORMS" => array(
		"TITLE" => GetMessage("FORMS_OPTIONS"),
		"OPTIONS" => array(			
			"FORMS_USE_CAPTCHA" => array(
				"TITLE" => GetMessage("FORMS_USE_CAPTCHA"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"FORMS_PHONE_MASK" => array(
				"TITLE" => GetMessage("FORMS_PHONE_MASK"),				
				"TYPE" => "text",
				"DEFAULT" => "+9{1,3} (9{2,3}) 999-99-99",
				"IN_SETTINGS_PANEL" => "N"				
			),
			"FORMS_VALIDATE_PHONE_MASK" => array(
				"TITLE" => GetMessage("FORMS_VALIDATE_PHONE_MASK"),
				"TYPE" => "text",
				"SIZE" => "40",
				"DEFAULT" => "/[+][0-9]{1,3} [(][0-9]{2,3}[)] [0-9]{3}[-][0-9]{2}[-][0-9]{2}$/i",
				"IN_SETTINGS_PANEL" => "N"
			),
			"CATALOG_REVIEWS_PRE_MODERATION" => array(
				"TITLE" => GetMessage("CATALOG_REVIEWS_PRE_MODERATION"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
		)
	),
	"PERSONAL_DATA" => array(
		"TITLE" => GetMessage("PERSONAL_DATA"),
		"OPTIONS" => array(
			"SHOW_PERSONAL_DATA" => array(
				"TITLE" => GetMessage("PERSONAL_DATA_SHOW_PERSONAL_DATA"),
				"TYPE" => "checkbox",
				"DEFAULT" => "Y",
				"IN_SETTINGS_PANEL" => "N"
			),
			"TEXT_PERSONAL_DATA" => array(
				"TITLE" => GetMessage("PERSONAL_DATA_TEXT_PERSONAL_DATA"),
				"TYPE" => "textarea",
				"COLS" => "50",
				"ROWS" => "5",
				"DEFAULT" => GetMessage("DEFAULT_PERSONAL_DATA_TEXT"),
				"IN_SETTINGS_PANEL" => "N"
			)
		)
	)   
);?>