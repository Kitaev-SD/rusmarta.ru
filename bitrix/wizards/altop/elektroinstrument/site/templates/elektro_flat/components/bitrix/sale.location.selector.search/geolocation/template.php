<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Location;

Loc::loadMessages(__FILE__);

if(!empty($arResult["ERRORS"]["FATAL"])):?>
	<div class="bx-ui-sls-error-fatal-message">
		<?foreach($arResult["ERRORS"]["FATAL"] as $error):
			ShowError($error);
		endforeach;?>
	</div>
<?else:
	CJSCore::Init();
	$GLOBALS["APPLICATION"]->AddHeadScript("/bitrix/js/sale/core_ui_widget.js");
	$GLOBALS["APPLICATION"]->AddHeadScript("/bitrix/js/sale/core_ui_etc.js");
	$GLOBALS["APPLICATION"]->AddHeadScript("/bitrix/js/sale/core_ui_autocomplete.js");?>

	<div id="sls-<?=$arResult['RANDOM_TAG']?>" class="bx-sls">
		<div class="dropdown-block bx-ui-sls-input-block">
			<i class="fa fa-search dropdown-icon"></i>			
			<input type="text" autocomplete="off" name="<?=$arParams['INPUT_NAME']?>" value="<?=$arResult['VALUE']?>" class="dropdown-field" placeholder="<?=Loc::getMessage('SALE_SLS_INPUT_SOME')?>" />
			<div class="dropdown-fade2white"></div>			
			<i class="fa fa-spinner fa-pulse bx-ui-sls-loader"></i>
			<i class="fa fa-times-circle bx-ui-sls-clear" title="<?=Loc::getMessage('SALE_SLS_CLEAR_SELECTION')?>" aria-hidden="true"></i>		
			<div class="bx-ui-sls-pane"></div>
		</div>
		<?if(is_array($arResult["DEFAULT_LOCATIONS"]) && !empty($arResult["DEFAULT_LOCATIONS"])):?>
			<div class="bx-ui-sls-quick-locations quick-locations">
				<div class="quick-locations__title"><?=Loc::getMessage('SALE_SLS_CHOOSE')?></div>				
				<ul class="quick-locations__values">					
					<?foreach($arResult["DEFAULT_LOCATIONS"] as $lid => $loc):?>
						<li class="quick-locations__val" data-id="<?=intval($loc['ID'])?>"><?=htmlspecialcharsbx($loc["NAME"])?></li>
					<?endforeach;?>						
				</ul>
			</div>
		<?endif?>
		<div class="submit">
			<button class="btn_buy popdef" id="selectCity" name="select-city"><?=Loc::getMessage("SALE_SLS_SELECT_CITY")?></button>
		</div>
		<script type="text/html" data-template-id="bx-ui-sls-error">
			<div class="bx-ui-sls-error">
				<div></div>
				{{message}}
			</div>
		</script>
		<script type="text/html" data-template-id="bx-ui-sls-dropdown-item">
			<div class="dropdown-item bx-ui-sls-variant">
				<span class="dropdown-item-text">{{display_wrapped}}</span>				
			</div>
		</script>		
		<?if(!$arParams["SUPPRESS_ERRORS"]):
			if(!empty($arResult["ERRORS"]["NONFATAL"])):?>
				<div class="bx-ui-sls-error-message">
					<?foreach($arResult["ERRORS"]["NONFATAL"] as $error):
						ShowError($error);
					endforeach;?>
				</div>
			<?endif;
		endif;?>		
	</div>

	<script>
		if(!window.BX && top.BX)
			window.BX = top.BX;

		<?if(strlen($arParams["JS_CONTROL_DEFERRED_INIT"])):?>
			if(typeof window.BX.locationsDeferred == "undefined") window.BX.locationsDeferred = {};
			window.BX.locationsDeferred["<?=$arParams['JS_CONTROL_DEFERRED_INIT']?>"] = function(){
		<?endif;

			if(strlen($arParams["JS_CONTROL_GLOBAL_ID"])):?>
				if(typeof window.BX.locationSelectors == "undefined") window.BX.locationSelectors = {};
				window.BX.locationSelectors["<?=$arParams['JS_CONTROL_GLOBAL_ID']?>"] = 
			<?endif;?>

			new BX.Sale.component.location.selector.search(<?=CUtil::PhpToJSObject(array(
				// common
				"scope" => "sls-".$arResult["RANDOM_TAG"],
				"source" => $this->__component->getPath()."/get.php",
				"query" => array(
					"FILTER" => array(
						"EXCLUDE_ID" => intval($arParams["EXCLUDE_SUBTREE"]),
						"SITE_ID" => $arParams["FILTER_BY_SITE"] && !empty($arParams["FILTER_SITE_ID"]) ? $arParams["FILTER_SITE_ID"] : ""
					),
					"BEHAVIOUR" => array(
						"SEARCH_BY_PRIMARY" => $arParams["SEARCH_BY_PRIMARY"] ? "1" : "0",
						"LANGUAGE_ID" => LANGUAGE_ID
					),
				),
				"selectedItem" => !empty($arResult["LOCATION"]) ? $arResult["LOCATION"]["VALUE"] : false,
				"knownItems" => $arResult["KNOWN_ITEMS"],
				"provideLinkBy" => $arParams["PROVIDE_LINK_BY"],
				"messages" => array(
					"nothingFound" => Loc::getMessage("SALE_SLS_NOTHING_FOUND"),
					"error" => Loc::getMessage("SALE_SLS_ERROR_OCCURED"),
				),
				// "js logic"-related part
				"callback" => $arParams["JS_CALLBACK"],
				"useSpawn" => $arParams["USE_JS_SPAWN"] == "Y",
				"initializeByGlobalEvent" => $arParams["INITIALIZE_BY_GLOBAL_EVENT"],
				"globalEventScope" => $arParams["GLOBAL_EVENT_SCOPE"],
				// specific
				"pathNames" => $arResult["PATH_NAMES"], // deprecated
				"types" => $arResult["TYPES"],
			), false, false, true)?>);

		<?if(strlen($arParams["JS_CONTROL_DEFERRED_INIT"])):?>
			};
		<?endif;?>
	</script>
<?endif;?>