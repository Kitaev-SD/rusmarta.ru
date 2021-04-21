<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;

global $USER;

$availablePages = array();

if($arParams["SHOW_ORDER_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arResult["PATH_TO_ORDERS"],
		"name" => Loc::getMessage("SPS_ORDER_PAGE_NAME"),
		"icon" => "<i class='fa fa-list' aria-hidden='true'></i>",
		"count" => $arResult["ORDERS"]["CURRENT"]["COUNT"]
	);
}

if($arParams["SHOW_BASKET_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arParams["PATH_TO_BASKET"],
		"name" => Loc::getMessage("SPS_BASKET_PAGE_NAME"),
		"icon" => "<i class='fa fa-shopping-cart' aria-hidden='true'></i>",
		"count" => $arResult["BASKET"]["AnDelCanBuy"]["COUNT"]
	);
	$delimeter = ($arParams["SEF_MODE"] === "Y") ? "?" : "&";
	$availablePages[] = array(
		"path" => $arParams["PATH_TO_BASKET"].$delimeter."delay=Y",
		"name" => Loc::getMessage("SPS_DELAY_PAGE_NAME"),
		"icon" => "<i class='fa fa-heart' aria-hidden='true'></i>",
		"count" => $arResult["BASKET"]["DelDelCanBuy"]["COUNT"]
	);
}

if($arParams["SHOW_SUBSCRIBE_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arResult["PATH_TO_SUBSCRIBE"],
		"name" => Loc::getMessage("SPS_SUBSCRIBE_PAGE_NAME"),
		"icon" => "<i class='fa fa-clock-o' aria-hidden='true'></i>",
		"count" => $arResult["SUBSCRIBE"]["COUNT"]
	);
}

if($arParams["SHOW_ORDER_PAGE"] === "Y") {
	$delimeter = ($arParams["SEF_MODE"] === "Y") ? "?" : "&";
	$availablePages[] = array(
		"path" => $arResult["PATH_TO_ORDERS"].$delimeter."filter_history=Y",
		"name" => Loc::getMessage("SPS_ORDER_PAGE_HISTORY"),
		"icon" => "<i class='fa fa-list-alt' aria-hidden='true'></i>",
		"count" => $arResult["ORDERS"]["HISTORY"]["COUNT"]
	);
}

if($arParams["SHOW_PRIVATE_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arResult["PATH_TO_PRIVATE"],
		"name" => Loc::getMessage("SPS_PERSONAL_PAGE_NAME"),
		"icon" => "<i class='fa fa-user' aria-hidden='true'></i>"
	);
}

if($arParams["SHOW_ACCOUNT_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arResult["PATH_TO_ACCOUNT"],
		"name" => Loc::getMessage("SPS_ACCOUNT_PAGE_NAME"),
		"icon" => "<i class='fa fa-credit-card' aria-hidden='true'></i>",
		"sum" => $arResult["ACCOUNT"]["SUM"]
	);
}

if($arParams["SHOW_PROFILE_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arResult["PATH_TO_PROFILE"],
		"name" => Loc::getMessage("SPS_PROFILE_PAGE_NAME"),
		"icon" => "<i class='fa fa-users' aria-hidden='true'></i>"
	);
}

if($arParams["SHOW_EMAIL_SUBSCRIBE_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arParams["PATH_TO_EMAIL_SUBSCRIBE"],
		"name" => Loc::getMessage("SPS_EMAIL_SUBSCRIBE_PAGE_NAME"),
		"icon" => "<i class='fa fa-envelope-o' aria-hidden='true'></i>"
	);
}

if($arParams["SHOW_CONTACT_PAGE"] === "Y") {
	$availablePages[] = array(
		"path" => $arParams["PATH_TO_CONTACT"],
		"name" => Loc::getMessage("SPS_CONTACT_PAGE_NAME"),
		"icon" => "<i class='fa fa-info-circle' aria-hidden='true'></i>"
	);
}

if($arParams["SHOW_EXIT_PAGE"] === "Y" && $USER->IsAuthorized()) {	
	global $APPLICATION;
	$availablePages[] = array(
		"path" => $APPLICATION->GetCurPageParam("logout=yes", Array("logout")),
		"name" => Loc::getMessage("SPS_EXIT_PAGE_NAME"),
		"icon" => "<i class='fa fa-sign-out' aria-hidden='true'></i>"
	);
}

$customPagesList = CUtil::JsObjectToPhp($arParams["~CUSTOM_PAGES"]);
if($customPagesList) {
	foreach($customPagesList as $page) {
		$availablePages[] = array(
			"path" => $page[0],
			"name" => $page[1],
			"icon" => (strlen($page[2])) ? "<i class='fa ".htmlspecialcharsbx($page[2])."' aria-hidden='true'></i>" : ""
		);
	}
}

if(empty($availablePages)) {
	ShowError(Loc::getMessage("SPS_ERROR_NOT_CHOSEN_ELEMENT"));
} else {?>
	<div class="sale-personal-section__list">		
		<?foreach($availablePages as $blockElement) {?>
			<div class="sale-personal-section__item">
				<a class="sale-personal-section__item-link" href="<?=htmlspecialcharsbx($blockElement['path'])?>">
					<span class="sale-personal-section__item-icon">
						<?=$blockElement["icon"];
						if(isset($blockElement["sum"])) {?>
							<span class="sale-personal-section__item-sum-wrap">
								<span class="sale-personal-section__item-sum"><?=$blockElement["sum"]?></span>
							</span>
						<?}?>
					</span>
					<span class="sale-personal-section__item-title">
						<?=htmlspecialcharsbx($blockElement["name"]);
						if(isset($blockElement["count"])) {?>
							<span class="sale-personal-section__item-count"><?=$blockElement["count"]?></span>
						<?}?>
					</span>
				</a>
			</div>
		<?}?>			
	</div>
<?}?>