<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/********************vp*********************/
if ( $_SERVER['REQUEST_URI'] != strtolower( $_SERVER['REQUEST_URI']) ) { //Редирект url на нижний регистр
    header('Location: https://'.$_SERVER['HTTP_HOST'] . 
            strtolower($_SERVER['REQUEST_URI']), true, 301);
    exit();
}
/*****************************************/
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
Loc::loadMessages(__FILE__);?>

 <?=LANGUAGE_ID?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?/*vp*/?> <!-- Google Tag Manager --> <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-M32PPZL');</script> <!-- End Google Tag Manager --> <!-- Код тега Google analytics --> <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-59688382-1', 'auto');
  ga('send', 'pageview');

</script> <?/*vp*/?> <?=SITE_TEMPLATE_PATH?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>/favicon.ico" /&gt; <?=SITE_TEMPLATE_PATH?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>/images/apple-touch-icon-114.png" /&gt; <?=SITE_TEMPLATE_PATH?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>/images/apple-touch-icon-114.png" /&gt; <?=SITE_TEMPLATE_PATH?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>/images/apple-touch-icon-144.png" /&gt; <?=SITE_TEMPLATE_PATH?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>/images/apple-touch-icon-144.png" /&gt; <?=$APPLICATION->ShowTitle();?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"/&gt; <?=$APPLICATION->ShowProperty("description");?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"/&gt; <?=$APPLICATION->ShowProperty("ogtype");?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"/&gt; <?=(CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.$APPLICATION->GetCurPage();?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>" /&gt; <?=$APPLICATION->ShowProperty("ogimage");?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?=$APPLICATION->ShowProperty("ogimagewidth");?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>" /&gt; <?=$APPLICATION->ShowProperty("ogimageheight");?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>" /&gt; <?=$APPLICATION->ShowProperty("ogimage")?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>" /&gt; <?$APPLICATION->SetPageProperty("ogtype", "website");
	$APPLICATION->SetPageProperty("ogimage", (CMain::IsHTTPS()? 'https' : 'http')."://".SITE_SERVER_NAME.SITE_TEMPLATE_PATH."/images/apple-touch-icon-144.png");
	$APPLICATION->SetPageProperty("ogimagewidth", "144");
	$APPLICATION->SetPageProperty("ogimageheight", "144");	
	Asset::getInstance()->addCss("https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
	if(!CModule::IncludeModule("altop.elastofont"))
		Asset::getInstance()->addCss("https://d1azc1qln24ryf.cloudfront.net/130672/ELASTOFONT/style-cf.css?xk463o");
	Asset::getInstance()->addCss("https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=latin,cyrillic-ext");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/colors.css");	
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/js/anythingslider/slider.css");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/js/custom-forms/custom-forms.css");
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox-1.3.1.css");	
	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/js/spectrum/spectrum.css");

/*vp*/
    	Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/css/geolocation.css");
/*vp*/

/*calchdd*/
   Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/components/aviweb/calchdd/css/remodal.css");
   Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/components/aviweb/calchdd/css/remodal-default-theme.css");
   Asset::getInstance()->addCss(SITE_TEMPLATE_PATH."/components/aviweb/calchdd/css/style.css");
   Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/components/aviweb/calchdd/js/jquery.min.js");
   Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/components/aviweb/calchdd/js/remodal.min.js");
   Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/components/aviweb/calchdd/js/script.js");
/*calchdd*/
	
	CJSCore::Init(array("jquery", "popup"));	
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.cookie.js");	
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/moremenu.js");
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/jquery.inputmask.bundle.min.js");		
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/anythingslider/jquery.easing.1.2.js");
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/anythingslider/jquery.anythingslider.min.js");
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/custom-forms/jquery.custom-forms.js");
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/fancybox/jquery.fancybox-1.3.1.pack.js");	
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/spectrum/spectrum.js");	
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/countUp.min.js");	
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/countdown/jquery.plugin.js");
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/countdown/jquery.countdown.js");	
	Asset::getInstance()->addString("
		<script type='text/javascript'>
			$(function() {
				$.countdown.regionalOptions['ru'] = {
					labels: ['".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_YEAR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_MONTH")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_WEEK")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_DAY")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_HOUR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_MIN")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_SEC")."'],
					labels1: ['".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS1_YEAR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS1_MONTH")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS1_WEEK")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS1_DAY")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS1_HOUR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_MIN")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_SEC")."'],
					labels2: ['".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS2_YEAR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS2_MONTH")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS2_WEEK")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS2_DAY")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS2_HOUR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_MIN")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_LABELS_SEC")."'],
					compactLabels: ['".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_YEAR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_MONTH")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_WEEK")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_DAY")."'],
					compactLabels1: ['".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS1_YEAR")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_MONTH")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_WEEK")."', '".Loc::getMessage("COUNTDOWN_REGIONAL_COMPACT_LABELS_DAY")."'],
					whichLabels: function(amount) {
						var units = amount % 10;
						var tens = Math.floor((amount % 100) / 10);
						return (amount == 1 ? 1 : (units >= 2 && units <= 4 && tens != 1 ? 2 : (units == 1 && tens != 1 ? 1 : 0)));
					},
					digits: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
					timeSeparator: ':',
					isRTL: false
				};
				$.countdown.setDefaults($.countdown.regionalOptions['ru']);
			});
		</script>
	");	
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/main.js");
	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/script.js");

/*vp*/
    	Asset::getInstance()->addJs(SITE_TEMPLATE_PATH."/js/geolocation.js");
/*vp*/

	$APPLICATION->ShowHead();?> <?if(CModule::IncludeModule("altop.elektroinstrument")) {CElektroinstrument::getBackground(SITE_ID);}?> <?=$APPLICATION->ShowProperty("bgClass")?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span><?=$APPLICATION->ShowProperty("backgroundColor")?><?=$APPLICATION->ShowProperty("backgroundImage")?>&gt; <?global $arSetting;?> <?$arSetting = $APPLICATION->IncludeComponent("altop:settings", "", array(), false, array("HIDE_ICONS" => "Y"));?>
<div class="bx-panel<span id=" title="Код PHP: &lt;?=($arSetting['CART_LOCATION']['VALUE'] == 'TOP') ? ' clvt' : ''?&gt;">
	<?=($arSetting['CART_LOCATION']['VALUE'] == 'TOP') ? ' clvt' : ''?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?$APPLICATION->ShowPanel();?>
</div>
<div class="bx-include-empty">
	 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => ""
	)
);?>
</div>
<div class="body<span id=" title="Код PHP: &lt;?=($arSetting['CATALOG_LOCATION']['VALUE'] == 'HEADER') ? ' clvh' : ''?&gt;">
	<?=($arSetting['CATALOG_LOCATION']['VALUE'] == 'HEADER') ? ' clvh' : ''?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span><?=($arSetting['CART_LOCATION']['VALUE'] == 'TOP') ? ' clvt' : ''?><?=($arSetting['CART_LOCATION']['VALUE'] == 'RIGHT') ? ' clvr' : ''?><?=($arSetting['CART_LOCATION']['VALUE'] == 'LEFT') ? ' clvl' : ''?>"&gt;
	<div class="page-wrapper">
		 <?if($arSetting["SITE_BACKGROUND"]["VALUE"] == "Y"):?>
		<div class="center outer">
			 <?endif;
			if($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>
			<div class="top-menu">
				<div class="center<span id=" title="Код PHP: &lt;?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?&gt;">
					<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/top_menu.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
				</div>
			</div>
			 <?endif;?>
			<div>
				<div class="center<span id=" title="Код PHP: &lt;?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?&gt;">
					<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?/*vp*/?>
					<div class="header_1">
						<div class="logo">
							 <? if ($APPLICATION->GetCurPage(false) === '/'): ?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/company_logo.php"
	)
);?> <? else: ?> <a href="/">
							<?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/company_logo.php"
	)
);?> </a>
							<? endif; ?>
						</div>
					</div>
					<div class="header_2_loc">
						 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/header_location.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
					</div>
					<div class="header_2">
						 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/header_search.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
					</div>
					<div class="header_3">
						<div class="schedule">
							 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/schedule.php"
	)
);?> <a id="" class="btn_buy apuo callback_anch" href="#callbackwidget"><span class="cont"><i class="fa fa-phone"></i><span class="text"><?=Loc::getMessage("ALTOP_CALL_BACK")?></span></span></a>
						</div>
					</div>
					<div class="header_4">
						<div class="contacts">
							 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/geolocation.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/form_callback.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
						</div>
					</div>
					 <?/*vp*/?>
				</div>
			</div>
			 <?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT"):?>
			<div class="top-menu">
				<div class="center<span id=" title="Код PHP: &lt;?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?&gt;">
					<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/top_menu.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
				</div>
			</div>
			 <?elseif($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?>
			<div class="top-catalog">
				<div class="center<span id=" title="Код PHP: &lt;?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?&gt;">
					<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; 
					<?$APPLICATION->IncludeComponent("bitrix:menu", "tree" /*"={$arSetting["CATALOG_VIEW"]["VALUE"]=="FOUR_LEVELS"?"tree":"sections"}"*/,
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CACHE_SELECTED_ITEMS" => "N",
		"CHILD_MENU_TYPE" => "left",
		"DELAY" => "N",
		"MAX_LEVEL" => "4",
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "left",
		"USE_EXT" => "Y"
	)
);?>
				</div>
			</div>
			 <?endif;?>
			<div class="top_panel">
				<div class="center<span id=" title="Код PHP: &lt;?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?&gt;">
					<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt;
					<div class="panel_1">
						 <?$APPLICATION->IncludeComponent("bitrix:main.include","",	Array(
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/sections.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
					</div>
					<div class="panel_2">
						 <?$APPLICATION->IncludeComponent("bitrix:menu","panel",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CACHE_SELECTED_ITEMS" => "N",
		"CHILD_MENU_TYPE" => "topchild",
		"MAX_LEVEL" => "3",
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "top",
		"USE_EXT" => "N"
	)
);?>
					</div>
					<div class="panel_3">
						<ul class="contacts-vertical">
							<li> <a class="showcontacts" href="javascript:void(0)"><i class="fa fa-phone"></i></a> </li>
						</ul>
					</div>
					<div class="panel_4">
						<ul class="search-vertical">
							<li> <a class="showsearch" href="javascript:void(0)"><i class="fa fa-search"></i></a> </li>
						</ul>
					</div>
				</div>
			</div>
			<div class="content-wrapper">
				<div class="center<span id=" title="Код PHP: &lt;?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?&gt;">
					<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt;
					<div class="content">
						 <?$inOrderPage = CSite::InDir("/personal/order/make/");
						if(!$inOrderPage):?>
						<div class="left-column">
							 <?if($APPLICATION->GetDirProperty("PERSONAL_SECTION")):?>
							<div class="h3">
								<?=Loc::getMessage("PERSONAL_HEADER");?>
							</div>
							 <?$APPLICATION->IncludeComponent(
	"altop:user",
	".default",
	Array(
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"PATH_TO_PERSONAL" => SITE_DIR."personal/"
	)
);?> <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"tree",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CACHE_SELECTED_ITEMS" => "N",
		"CHILD_MENU_TYPE" => "personal",
		"DELAY" => "N",
		"MAX_LEVEL" => "1",
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "personal",
		"USE_EXT" => "Y"
	)
);?> <?if($USER->IsAuthorized()):?> <a class="personal-exit" href="<?=$APPLICATION->GetCurPageParam('logout=yes', array('logout'));?>"><?=Loc::getMessage("PERSONAL_EXIT");?></a>
							<?endif;
								else:
									if($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT"):?>
							<div class="h3">
								<?=Loc::getMessage("BASE_HEADER");?>
							</div>
							 <?$APPLICATION->IncludeComponent(
	"bitrix:menu",
	"sections",
	Array(
		"ALLOW_MULTI_SELECT" => "N",
		"CACHE_SELECTED_ITEMS" => "N",
		"CHILD_MENU_TYPE" => "left",
		"COMPONENT_TEMPLATE" => "sections",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DELAY" => "N",
		"MAX_LEVEL" => "4",
		"MENU_CACHE_GET_VARS" => array(),
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_TYPE" => "A",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"ROOT_MENU_TYPE" => "left",
		"USE_EXT" => "Y"
	)
);?> <?endif;
								endif;
								if($arSetting["SMART_FILTER_LOCATION"]["VALUE"] == "VERTICAL"):
									$APPLICATION->ShowViewContent("filter_vertical");
								endif;
								if($arSetting["CATALOG_LOCATION"]["VALUE"] == "HEADER"):?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/banners_left.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?if($APPLICATION->GetCurPage(true)!= SITE_DIR."index.php") {?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/slider_left.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?}?> <?endif;?>
							<ul class="new_leader_disc">
								<li> <a class="new" href="<?=SITE_DIR?>market/newproduct/"> <span class="icon"><?=Loc::getMessage("CR_TITLE_ICON_NEWPRODUCT")?></span> <span class="text"><?=Loc::getMessage("CR_TITLE_NEWPRODUCT")?></span> </a> </li>
								<li> <a class="hit" href="<?=SITE_DIR?>market/saleleader/"> <span class="icon"><?=Loc::getMessage("CR_TITLE_ICON_SALELEADER")?></span> <span class="text"><?=Loc::getMessage("CR_TITLE_SALELEADER")?></span> </a> </li>
								<li> <a class="discount" href="<?=SITE_DIR?>market/discount/"> <span class="icon"><?=Loc::getMessage("CR_TITLE_ICON_DISCOUNT")?></span> <span class="text"><?=Loc::getMessage("CR_TITLE_DISCOUNT")?></span> </a> </li>
							</ul>
							 <?if($arSetting["CATALOG_LOCATION"]["VALUE"] == "LEFT"):?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/banners_left.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?if($APPLICATION->GetCurPage(true)!= SITE_DIR."index.php") {?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/slider_left.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?}?> <?endif;?>
							<div class="vendors">
								<div class="h3">
									<?=Loc::getMessage("MANUFACTURERS");?>
								</div>
								 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/vendors_left.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
							</div>
							<div class="subscribe">
								<div class="h3">
									<?=Loc::getMessage("SUBSCRIBE");?>
								</div>
								<p>
									<?=Loc::getMessage("SUBSCRIBE_TEXT");?>
								</p>
								 <?$APPLICATION->IncludeComponent(
	"bitrix:subscribe.form",
	"left",
	Array(
		"CACHE_NOTES" => "",
		"CACHE_TIME" => "36000000",
		"CACHE_TYPE" => "A",
		"PAGE" => SITE_DIR."personal/mailings/",
		"SHOW_HIDDEN" => "N",
		"USE_PERSONALIZATION" => "Y"
	)
);?>
							</div>
							 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/news_left.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/reviews_left.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
						</div>
						 <?endif;?>
						<div class="workarea<span id=" title="Код PHP: &lt;?=($inOrderPage ? ' workarea-order' : '');?&gt;">
							<?=($inOrderPage ? ' workarea-order' : '');?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?if($APPLICATION->GetCurPage(true)== SITE_DIR."index.php"):
								if(in_array("SLIDER", $arSetting["HOME_PAGE"]["VALUE"])):?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/slider.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?endif;
								if(in_array("ADVANTAGES", $arSetting["HOME_PAGE"]["VALUE"])):
									global $arAdvFilter;
									$arAdvFilter = array(
										"!PROPERTY_SHOW_HOME" => false
									);?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/advantages.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?endif;
								if(in_array("PROMOTIONS", $arSetting["HOME_PAGE"]["VALUE"])):?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/promotions.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?endif;
								if(in_array("BANNERS", $arSetting["HOME_PAGE"]["VALUE"])):?> <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/banners_main.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <?endif;								
								if(in_array("TABS", $arSetting["HOME_PAGE"]["VALUE"])):?>
							<div class="tabs-wrap tabs-main">
								<ul class="tabs">
									 <?if(in_array("RECOMMEND", $arSetting["HOME_PAGE"]["VALUE"])):?>
									<li class="tabs__tab recommend"> <a href="javascript:void(0)"><?=Loc::getMessage("CR_TITLE_RECOMMEND")?></a> </li>
									 <?endif;?>
									<li class="tabs__tab new"> <a href="javascript:void(0)"><?=Loc::getMessage("CR_TITLE_NEWPRODUCT")?></a> </li>
									<li class="tabs__tab hit"> <a href="javascript:void(0)"><?=Loc::getMessage("CR_TITLE_SALELEADER")?></a> </li>
									<li class="tabs__tab discount"> <a href="javascript:void(0)"><?=Loc::getMessage("CR_TITLE_DISCOUNT")?></a> </li>
								</ul>
								 <?if(in_array("RECOMMEND", $arSetting["HOME_PAGE"]["VALUE"])):?>
								<div class="tabs__box recommend">
									 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/recommend.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
								</div>
								 <?endif;?>
								<div class="tabs__box new">
									<div class="catalog-top">
										 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/newproduct.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <a class="all" href="<?=SITE_DIR?>market/newproduct/"><?=Loc::getMessage("CR_TITLE_ALL_NEWPRODUCT");?></a>
									</div>
								</div>
								<div class="tabs__box hit">
									<div class="catalog-top">
										 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/saleleader.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <a class="all" href="<?=SITE_DIR?>market/saleleader/"><?=Loc::getMessage("CR_TITLE_ALL_SALELEADER");?></a>
									</div>
								</div>
								<div class="tabs__box discount">
									<div class="catalog-top">
										 <?$APPLICATION->IncludeComponent(
	"bitrix:main.include",
	"",
	Array(
		"AREA_FILE_RECURSIVE" => "N",
		"AREA_FILE_SHOW" => "file",
		"EDIT_MODE" => "html",
		"PATH" => SITE_DIR."include/discount.php"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?> <a class="all" href="<?=SITE_DIR?>market/discount/"><?=Loc::getMessage("CR_TITLE_ALL_DISCOUNT");?></a>
									</div>
								</div>
								<div class="clr">
								</div>
							</div>
							 <?endif;
							endif;?>
							<div class="body_text" title="Код PHP: &lt;?=($APPLICATION-&gt;GetCurPage(true) == SITE_DIR.'index.php') ? 'padding:0px 15px;' : 'padding:0px;';?...">
								<?=($APPLICATION->GetCurPage(true) == SITE_DIR.'index.php') ? 'padding:0px 15px;' : 'padding:0px;';?><span class="bxhtmled-surrogate-inner"><span class="bxhtmled-right-side-item-icon"></span><span class="bxhtmled-comp-lable" unselectable="on" spellcheck="false">Код PHP</span></span>"&gt; <?if($APPLICATION->GetCurPage(true)!= SITE_DIR."index.php"):?>
								<div class="breadcrumb-share">
									<div id="navigation" class="breadcrumb">
										 <?$APPLICATION->IncludeComponent(
	"bitrix:breadcrumb",
	".default",
	Array(
		"PATH" => "",
		"SITE_ID" => "-",
		"START_FROM" => "0"
	),
false,
Array(
	'HIDE_ICONS' => 'Y'
)
);?>
									</div>
									<div class="share">
										 <script type="text/javascript" async src="//yastatic.net/share/share.js" charset="utf-8"></script>
										<div class="yashare-auto-init" data-yasharel10n="ru" data-yasharetype="small" data-yasharequickservices="vkontakte,facebook,twitter,odnoklassniki" data-yasharetheme="counter">
										</div>
									</div>
								</div>
								<h1 id="pagetitle"><?=$APPLICATION->ShowTitle(false);?></h1>
								 <?endif;?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>