<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$randomString = $this->randString();

$APPLICATION->setTitle(Loc::getMessage("CPSL_SUBSCRIBE_PAGE_TITLE"));

if(!$arResult["USER_ID"] && !isset($arParams["GUEST_ACCESS"])) {
	$contactTypeCount = count($arResult["CONTACT_TYPES"]);
	$authStyle = "display: block;";
	$identificationStyle = "display: none;";
	if(!empty($_GET["result"])) {
		$authStyle = "display: none;";
		$identificationStyle = "display: block;";
	}?>	
	<span class="alertMsg bad">	
		<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
		<span class="text"><?=Loc::getMessage("CPSL_SUBSCRIBE_PAGE_AUTHORIZE")?></span>
	</span>
	<div class="catalog-subscriber-forms">
		<div id="catalog-subscriber-auth-form" style="<?=$authStyle?>">
			<?$APPLICATION->authForm("", false, false, "N", false);?>		
		</div>
		<div id="catalog-subscriber-identification-form" class="" style="<?=$identificationStyle?>">		
			<h2><?=Loc::getMessage("CPSL_HEADLINE_FORM_SEND_CODE")?></h2>			
			<div class="catalog-subscriber-identification-form-wrap">
				<div class="catalog-subscriber-identification-form">
					<form method="post">
						<?=bitrix_sessid_post()?>
						<input type="hidden" name="siteId" value="<?=SITE_ID?>">
						<?if($contactTypeCount > 1):?>
							<div class="form-group">
								<label for="contactType"><?=Loc::getMessage("CPSL_CONTACT_TYPE_SELECTION")?></label>
								<select id="contactType" class="form-control" name="contactType">
									<?foreach($arResult["CONTACT_TYPES"] as $contactTypeData):?>
										<option value="<?=intval($contactTypeData['ID'])?>">
											<?=htmlspecialcharsbx($contactTypeData["NAME"])?></option>
									<?endforeach;?>
								</select>
							</div>
						<?endif;?>
						<div class="form-group">
							<?$contactLable = Loc::getMessage("CPSL_CONTACT_TYPE_NAME");
							$contactTypeId = 0;
							if($contactTypeCount == 1) {
								$contactType = current($arResult["CONTACT_TYPES"]);
								$contactLable = $contactType["NAME"];
								$contactTypeId = $contactType["ID"];
							}?>
							<label for="contactInput"><?=htmlspecialcharsbx($contactLable)?></label>
							<br />
							<input type="text" class="form-control" name="userContact" id="contactInput">
							<input type="hidden" name="subscriberIdentification" value="Y">
							<?if($contactTypeId):?>
								<input type="hidden" name="contactType" value="<?=$contactTypeId?>">
							<?endif;?>
						</div>
						<button type="submit" class="btn_buy popdef"><?=Loc::getMessage("CPSL_BUTTON_SUBMIT_CODE")?></button>
					</form>
				</div>
			</div>	
			<h2><?=Loc::getMessage("CPSL_HEADLINE_FORM_FOR_ACCESSING")?></h2>
			<div class="catalog-subscriber-identification-form-wrap">
				<div class="catalog-subscriber-identification-form">
					<form method="post">
						<?=bitrix_sessid_post()?>
						<div class="form-group">
							<label for="contactInput"><?=htmlspecialcharsbx($contactLable)?></label>
							<br />
							<input type="text" class="form-control" name="userContact" id="contactInput" value="<?=(!empty($_GET['contact']) ? htmlspecialcharsbx(urldecode($_GET['contact'])): '');?>">
						</div>
						<div class="form-group">
							<label for="token"><?=Loc::getMessage("CPSL_CODE_LABLE")?></label>
							<br />
							<input type="text" class="form-control" name="subscribeToken" id="token">
							<input type="hidden" name="accessCodeVerification" value="Y">
						</div>
						<button type="submit" class="btn_buy popdef"><?=Loc::getMessage("CPSL_BUTTON_SUBMIT_ACCESS")?></button>
					</form>
				</div>
			</div>
		</div>
	</div>	
	<script type="text/javascript">
		BX.ready(function() {
			if(BX("cpsl-auth")) {
				BX.bind(BX("cpsl-auth"), "click", BX.delegate(showAuthForm, this));
				BX.bind(BX("cpsl-identification"), "click", BX.delegate(showAuthForm, this));
			}
			function showAuthForm() {
				var formType = BX.proxy_context.id.replace("cpsl-", "");
				var authForm = BX("catalog-subscriber-auth-form"),
					codeForm = BX("catalog-subscriber-identification-form");
				if(!authForm || !codeForm || !BX("catalog-subscriber-"+formType+"-form")) return;

				BX.style(authForm, "display", "none");
				BX.style(codeForm, "display", "none");
				BX.style(BX("catalog-subscriber-"+formType+"-form"), "display", "");
			}
		});
	</script>
<?}

if(!empty($_GET["result"]) && !empty($_GET["message"])) {
	$successNotify = strpos($_GET["result"], "Ok") ? true : false;
	$postfix = $successNotify ? "Ok" : "Fail";
	$popupTitle = Loc::getMessage("CPSL_SUBSCRIBE_POPUP_TITLE_".strtoupper(str_replace($postfix, "", $_GET["result"])));
	$arJSParams = array(
		"NOTIFY_USER" => true,
		"NOTIFY_POPUP_TITLE" => $popupTitle,
		"NOTIFY_SUCCESS" => $successNotify,
		"NOTIFY_MESSAGE" => urldecode($_GET["message"]),
	);?>	
	<script type="text/javascript">
		var <?="jaClass_".$randomString;?> = new JCCatalogProductSubscribeList(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
	</script>
<?}

if(count($arResult["ITEMS"]) < 1 && isset($arParams["GUEST_ACCESS"])) {	
	ShowNote(GetMessage("CPSL_SUBSCRIBE_NOT_FOUND"), "infotext");
	return;
}

global $arSetting;?>

<div class="catalog-item-cards catalog-subscribe-items">
	<?foreach($arResult["ITEMS"] as $key => $arElement) {		
		$arItemIDs = array(
			"ID" => $arElement["STR_MAIN_ID"],			
			"UNSUBSCRIBE_BTN" => $arElement["STR_MAIN_ID"]."_unsubscribe_btn"
		);
		
		//ITEM_DISPLAY_PROPERTIES//
		if(!empty($arElement["DISPLAY_PROPERTIES"])) {
			$shortProperties = array();
			$properties = array();
			foreach($arElement["DISPLAY_PROPERTIES"] as $arOneProp) {
				$shortProperties[] = strip_tags($arOneProp["DISPLAY_VALUE"]);
				$properties[] = $arOneProp["NAME"].": ".strip_tags($arOneProp["DISPLAY_VALUE"]);
			}
			$shortProperties = implode(", ", $shortProperties);
			$properties = implode("; ", $properties);										
			$itemShortName = strip_tags($arElement["NAME"])." (".$shortProperties.")";
			$itemName = strip_tags($arElement["NAME"])." (".$properties.")";
		} else {
			$itemShortName = strip_tags($arElement["NAME"]);
			$itemName = strip_tags($arElement["NAME"]);
		}?>
		<div class="catalog-item-card">
			<div class="catalog-item-info">
				<?//ITEM_IMAGE//?>
				<div class="item-image-cont">
					<div class="item-image">
						<?if(is_array($arElement["PREVIEW_PICTURE"])) {?>
							<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
								<img class="item_img" src="<?=$arElement['PREVIEW_PICTURE']['SRC']?>" width="<?=$arElement['PREVIEW_PICTURE']['WIDTH']?>" height="<?=$arElement['PREVIEW_PICTURE']['HEIGHT']?>" alt="<?=$arElement['NAME']?>" title="<?=$arElement['NAME']?>" />
							</a>
						<?} else {?>
							<a href="<?=$arElement['DETAIL_PAGE_URL']?>">
								<img class="item_img" src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$arElement['NAME']?>" title="<?=$arElement['NAME']?>" />
							</a>
						<?}?>
					</div>
				</div>
				<?//ITEM_TITLE//?>
				<div class="item-all-title">
					<a class="item-title" href="<?=$arElement['DETAIL_PAGE_URL']?>" title="<?=$itemName?>">
						<?=$itemShortName?>
					</a>
				</div>
				<?//ITEM_ARTICLE//?>
				<?if(in_array("ARTNUMBER", $arSetting["PRODUCT_TABLE_VIEW"]["VALUE"])):?>
					<div class="item-article">
						<?=GetMessage("CPSL_TPL_MESS_ARTNUMBER").(!empty($arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"]) ? $arElement["PROPERTIES"]["ARTNUMBER"]["VALUE"] : "-");?>
					</div>
				<?endif;?>
				<?//ITEM_UNSUBSCRIBE//?>
				<div class="buy_more">
					<div class="add2basket_block">
						<a id="<?=$arItemIDs['UNSUBSCRIBE_BTN']?>" class="btn_buy apuo" href="javascript:void(0)" rel="nofollow"><i class="fa fa-eye-slash"></i><span><?=GetMessage("CPSL_TPL_MESS_BTN_UNSUBSCRIBE")?></span></a>
					</div>				
				</div>
			</div>
		</div>
	<?}?>
</div>

<?//JS//?>
<script type="text/javascript">
	BX.ready(function() {
		BX.message({		
			CPSL_STATUS_SUCCESS: "<?=GetMessageJS('CPSL_STATUS_SUCCESS');?>",
			CPSL_STATUS_ERROR: "<?=GetMessageJS('CPSL_STATUS_ERROR');?>",
			CPSL_TITLE_UNSUBSCRIBE: "<?=GetMessageJS('CPSL_SUBSCRIBE_POPUP_TITLE_UNSUBSCRIBE');?>"
		});
		<?foreach($arResult["ITEMS"] as $key => $arElement) {
			$arJSParams = array(				
				"VISUAL" => array(
					"ID" => $arElement["STR_MAIN_ID"],
					"UNSUBSCRIBE_BTN_ID" => $arElement["STR_MAIN_ID"]."_unsubscribe_btn"
				),				
				"PRODUCT" => array(
					"ID" => $arElement["ID"],					
					"LIST_SUBSCRIBE_ID" => $arParams["LIST_SUBSCRIPTIONS"]
				)
			);
			$strObName = "ob".preg_replace("/[^a-zA-Z0-9_]/", "x", $arElement["STR_MAIN_ID"]);?>
			var <?=$strObName;?> = new JCCatalogProductSubscribeList(<?=CUtil::PhpToJSObject($arJSParams, false, true);?>);
		<?}?>
	});
</script>