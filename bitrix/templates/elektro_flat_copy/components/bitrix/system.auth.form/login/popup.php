<?require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$APPLICATION->ShowAjaxHead();
$APPLICATION->AddHeadScript("/bitrix/js/main/dd.js");

use Bitrix\Main\Loader,
	Bitrix\Main\Application,
	Bitrix\Main\Text\Encoding;

$request = Application::getInstance()->getContext()->getRequest();

$params = $request->getPost("arParams");
if(SITE_CHARSET != "utf-8")
	$params = Encoding::convertEncoding($params, "utf-8", SITE_CHARSET);

$arResult = $params["RESULT"];
$arMessage = $params["MESS"];?>

<div class="login-form" id="loginForm">
	<div class="fields">
		<form name="form_auth" method="post" target="_top" action="<?=SITE_DIR?>personal/private/">
			<input type="hidden" name="AUTH_FORM" value="Y"/>
			<input type="hidden" name="TYPE" value="AUTH"/>
			<?if(strlen($arResult["BACKURL"]) > 0):?>
				<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>"/>
			<?endif?>
			<?if(isset($arResult["POST"]) && is_array($arResult["POST"])) foreach($arResult["POST"] as $key => $value) {?>
				<input type="hidden" name="<?=$key?>" value="<?=$value?>"/>
			<?}?>
			<div class="field">
				<input type="text" name="USER_LOGIN" maxlength="50" placeholder="<?=$arMessage['AUTH_LOGIN']?>" value="" class="input-field"/>
			</div>	
			<div class="field">
				<input type="password" name="USER_PASSWORD" maxlength="50" placeholder="<?=$arMessage['AUTH_PASSWORD']?>" value="" class="input-field"/>
			</div>
			<div class="field field-button">
				<button type="submit" name="Login" class="btn_buy popdef" value="<?=$arMessage['LOGIN']?>"><?=$arMessage["LOGIN"]?></button>
			</div>
			<div class="field">
				<a class="btn_buy apuo forgot" href="<?=SITE_DIR?>personal/private/?forgot_password=yes" rel="nofollow"><?=$arMessage["AUTH_FORGOT_PASSWORD"]?></a>
			</div>
			<div class="field" style="margin:0px;">
				<a class="btn_buy apuo reg" href="<?=SITE_DIR?>personal/private/?register=yes" rel="nofollow"><?=$arMessage["AUTH_REGISTRATION"]?></a>
			</div>
		</form>
		<script type="text/javascript">
			<?if(strlen($arResult["LAST_LOGIN"]) > 0) {?>
				try {
					document.form_auth.USER_PASSWORD.focus();
				} catch(e) {}
			<?} else {?>
				try {
					document.form_auth.USER_LOGIN.focus();
				} catch(e) {}
			<?}?>
		</script>
	</div>
	<?
	$arResult["AUTH_SERVICES"] = false;
	if(CModule::IncludeModule("socialservices")) {
		$oAuthManager = new CSocServAuthManager();
		$arServices = $oAuthManager->GetActiveAuthServices($arResult);
		if(!empty($arServices)) $arResult["AUTH_SERVICES"] = $arServices;
	}
	?>
	<?if($arResult["AUTH_SERVICES"] && COption::GetOptionString("main", "allow_socserv_authorization", "Y") != "N") {?>
		<p class="login_as"><?=$arMessage["LOGIN_AS_USER"]?></p>
		<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "icons", 
			array(
				"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
				"SUFFIX" => "form", 
			), 
			$component, 
			array("HIDE_ICONS"=>"Y")
		);?>
		<?$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "",
			array(
				"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
				"AUTH_URL" => $arResult["AUTH_URL"],
				"POST" => $arResult["POST"],
				"POPUP" => "Y",
				"SUFFIX" => "form",
			),
			$component,
			array("HIDE_ICONS"=>"Y")
		);?>
	<?}?>					
</div>