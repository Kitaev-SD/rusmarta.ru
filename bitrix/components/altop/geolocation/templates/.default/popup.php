<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();

$arParams = $request->getPost("arParams");
if(!empty($arParams))
	$arParams = unserialize(gzuncompress(stripslashes(base64_decode(strtr($arParams, '-_,', '+/=')))));

$locationId = $request->getCookie("GEOLOCATION_LOCATION_ID");?>

<?$APPLICATION->IncludeComponent("bitrix:sale.location.selector.search", "geolocation",
	array(
		"COMPONENT_TEMPLATE" => ".default",
		"ID" => $locationId,
		"CODE" => "",
		"INPUT_NAME" => "LOCATION",
		"PROVIDE_LINK_BY" => "id",
		"JSCONTROL_GLOBAL_ID" => "",
		"JS_CALLBACK" => "",
		"FILTER_BY_SITE" => "Y",
		"SHOW_DEFAULT_LOCATIONS" => $arParams["SHOW_DEFAULT_LOCATIONS"],
		"CACHE_TYPE" => $arParams["CACHE_TYPE"],
		"CACHE_TIME" => $arParams["CACHE_TIME"],
		"FILTER_SITE_ID" => SITE_ID,
		"INITIALIZE_BY_GLOBAL_EVENT" => "",
		"SUPPRESS_ERRORS" => "N"	
	),
	false
);?>

<?if($arParams["SHOW_TEXT_BLOCK"] == "Y"):?>
	<div class="block-info">
		<?if($arParams["SHOW_TEXT_BLOCK_TITLE"] == "Y"):?>
			<div class="block-info__title"><?=$arParams["TEXT_BLOCK_TITLE"]?></div>
		<?endif;?>
		<div class="block-info__text">
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", 
				array(
					"AREA_FILE_SHOW" => "file",
					"PATH" => SITE_DIR."include/geolocation_descr.php"
				),
				false
			);?>
		</div>
	</div>
<?endif;?>

<script type="text/javascript">
	//SET_LOCATION//
	BX.SetLocation = function() {
		BX.ajax.post(
			BX.message("GEOLOCATION_COMPONENT_PATH") + "/ajax.php",
			{
				arParams: BX.message("GEOLOCATION_PARAMS"),
				sessid: BX.bitrix_sessid(),
				action: "setLocation",
				locationId: BX.findChild(BX("cityChange"), {tagName: "input", className: "dropdown-field"}, true, false).value
			},
			function(result) {
				window.location.reload();
			}
		);		
	}
	BX.bind(BX("selectCity"), "click", BX.delegate(BX.SetLocation, BX));
</script>