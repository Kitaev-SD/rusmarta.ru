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

$element_name = $params["ELEMENT_NAME"];
$preview_img = $params["PREVIEW_IMG"];
$captcha_code = $params["CAPTCHA_CODE"];
$notify_id = $params["NOTIFY_ID"];
$notify_url = $params["NOTIFY_URL"];
$arMessage = $params["MESS"];?>

<div class="container">
	<div class="info">
		<div class="image">
			<?if(is_array($preview_img)):?>
				<img src="<?=$preview_img['SRC']?>" width="<?=$preview_img['WIDTH']?>" height="<?=$preview_img['HEIGHT']?>" alt="<?=$element_name?>" />
			<?else:?>
				<img src="<?=SITE_TEMPLATE_PATH?>/images/no-photo.jpg" width="150" height="150" alt="<?=$element_name?>" />
			<?endif?>
		</div>
		<div class="name"><?=$element_name?></div>
	</div>		
	<div class="new_notify_form" id="newNotifyForm">	
		<div id="popup_n_error"></div>
		<div class="row">
			<div class="span1"><?=$arMessage["MFT_EMAIL"]?><span class="mf-req">*</span></div>
			<div class="span2">					
				<input type="text" name="popup_user_email" id="popup_user_email" value="" />
			</div>
			<div class="clr"></div>
		</div>						
		<?if($captcha_code):?>
			<div class="row">
				<div class="span1"><?=$arMessage["MFT_CAPTCHA"];?><span class="mf-req">*</span></div>
				<div class="span2">						
					<input type="text" name="popup_captcha_word" id="popup_captcha_word" maxlength="50" value="" />
					<span id="popup_captcha_img">
						<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$captcha_code?>" width="127" height="30" alt="CAPTCHA" />
					</span>
					<input type="hidden" name="popup_captcha_sid" id="popup_captcha_sid" value="<?=$captcha_code?>" />
				</div>
				<div class="clr"></div>
			</div>
		<?endif;?>
		<input type="hidden" name="popup_notify_url" id="popup_notify_url" value="<?=$notify_url?>" />			
		<div class="submit">
			<button class="btn_buy popdef" name="send_button" onclick="newNotifyFormSubmit();"><?=$arMessage["MFT_BUTTON"];?></button>				
		</div>
	</div>		
</div>

<script type="text/javascript">
	if(!window.newNotifyFormSubmit) {
		function newNotifyFormSubmit() {
			var error = "N";
			var useCaptha = "N";
			BX("popup_n_error").innerHTML = "";

			var sessid = "";
			if(BX("sessid"))
				sessid = BX("sessid").value;
			var data = "sessid=" + sessid + "&ajax=Y";

			if(BX("popup_user_email").value.length == 0) {				
				BX("popup_n_error").innerHTML = "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'><?=$arMessage['MFT_EMPTY_EMAIL'];?></span></span>";
				error = "Y";
			}			

			var reg = /@/i;
			if(BX("popup_user_email").value.length > 0 && !reg.test(BX("popup_user_email").value)) {				
				BX("popup_n_error").innerHTML = "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'><?=$arMessage['MFT_ERR_EMAIL'];?></span></span>";
				error = "Y";
			} else {
				data = data + "&user_mail=" + BX("popup_user_email").value;

				if(BX("popup_captcha_sid") && BX("popup_captcha_word")) {
					data = data + "&captcha_sid=" + BX("popup_captcha_sid").value;
					data = data + "&captcha_word=" + BX("popup_captcha_word").value;
					useCaptha = "Y";
				}
			}

			if(error == "N") {
				var wait = BX.showWait("notify");

				BX.ajax.post("/bitrix/components/bitrix/sale.notice.product/ajax.php", data, function(res) {
					BX.closeWait("notify", wait);

					var rs = eval("(" + res + ")");

					if(rs["ERRORS"].length > 0) {
						if(rs["ERRORS"] == "NOTIFY_ERR_NULL")
							BX("popup_n_error").innerHTML = "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'><?=$arMessage['MFT_EMPTY_EMAIL']?></span></span>";
						else if(rs["ERRORS"] == "NOTIFY_ERR_CAPTHA")
							BX("popup_n_error").innerHTML = "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'><?=$arMessage['MFT_ERR_CAPTCHA']?></span></span>";
						else if(rs["ERRORS"] == "NOTIFY_ERR_MAIL_EXIST") {
							BX("popup_n_error").innerHTML = "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'><?=$arMessage['MFT_ERR_EMAIL_EXIST']?></span></span>";							
							BX("popup_user_email").value = "";							
						} else if(rs["ERRORS"] == "NOTIFY_ERR_REG")
							BX("popup_n_error").innerHTML = "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'><?=$arMessage['MFT_ERR_REG']?></span></span>";
						else
							BX("popup_n_error").innerHTML = "<span class='alertMsg bad'><i class='fa fa-times'></i><span class='text'>" + rs["ERRORS"] + "</span></span>";

						if(useCaptha == "Y") {
							BX.ajax.get("/bitrix/components/bitrix/sale.notice.product/ajax.php?reloadcaptha=Y", "", function(res) {
								BX("popup_captcha_sid").value = res;
								BX("popup_captcha_img").innerHTML = "<img src='/bitrix/tools/captcha.php?captcha_sid=" + res + "' width='127' height='30' alt='CAPTCHA' />";
							});
						}
					} else if(rs["STATUS"] == "Y") {
						notifyProduct(BX("popup_notify_url").value, <?=$notify_id?>);
						BX.NotifySet.popup.close();
					}
				});
			}		
		}
	}
</script>