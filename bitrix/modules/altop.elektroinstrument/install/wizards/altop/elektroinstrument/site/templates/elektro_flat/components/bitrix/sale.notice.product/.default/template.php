<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$this->setFrameMode(true);

if($arResult["STATUS"] == "Y"):?>
	<span class="url_notify" id="url_notify_<?=$arParams['NOTIFY_ID']?>">
		<span class="alertMsg good">
			<i class="fa fa-check"></i>
			<span class="text"><?=GetMessage("MFT_NOTIFY_MESSAGE")?></span>
		</span>
	</span>
<?elseif($arResult["STATUS"] == "N"):?>
	<span class="url_notify" id="url_notify_<?=$arParams['NOTIFY_ID']?>">
		<a class="btn_buy notify_anch" href="javascript:void(0)" onclick="notifyProduct('<?=$arResult["NOTIFY_URL"]?>', <?=$arParams['NOTIFY_ID']?>);"><i class="fa fa-envelope"></i><?=GetMessage("MFT_NOTIFY");?></a>		
	</span>
<?elseif($arResult["STATUS"] == "R"):?>
	<span class="url_notify" id="url_notify_<?=$arParams['NOTIFY_ID']?>">
		<a class="btn_buy notify_anch" id="notify_product_<?=$arParams['NOTIFY_ID']?>" href="javascript:void(0)" onclick="OpenNotifyPopup();"><i class="fa fa-envelope"></i><?=GetMessage("MFT_NOTIFY");?></a>
	</span>
<?endif;

$popupParams["ELEMENT_NAME"] = $arResult["ELEMENT_NAME"];
$popupParams["PREVIEW_IMG"] = $arResult["PREVIEW_IMG"];
$popupParams["CAPTCHA_CODE"] = $arResult["CAPTCHA_CODE"];
$popupParams["NOTIFY_ID"] = $arParams["NOTIFY_ID"];
$popupParams["NOTIFY_URL"] = $arResult["NOTIFY_URL"];
$popupParams["MESS"] = array(	
	"MFT_EMAIL" => GetMessage("MFT_NOTIFY_EMAIL"),
	"MFT_CAPTCHA" => GetMessage("MFT_NOTIFY_CAPTCHA"),
	"MFT_BUTTON" => GetMessage("MFT_NOTIFY_BUTTON"),
	"MFT_EMPTY_EMAIL" => GetMessage("MFT_NOTIFY_EMPTY_EMAIL"),
	"MFT_ERR_EMAIL" => GetMessage("MFT_NOTIFY_ERR_EMAIL"),
	"MFT_ERR_CAPTCHA" => GetMessage("MFT_NOTIFY_ERR_CAPTCHA"),
	"MFT_ERR_EMAIL_EXIST" => GetMessage("MFT_NOTIFY_ERR_EMAIL_EXIST"),
	"MFT_ERR_REG" => GetMessage("MFT_NOTIFY_ERR_REG")
);?>

<script type="text/javascript">
	if(!window.notifyProduct) {
		function notifyProduct(url, id) {
			BX.showWait();

			BX.ajax.post(url, "", function(res) {
				BX.closeWait();			
				
				if(BX("url_notify_" + id))				
					BX("url_notify_" + id).innerHTML = "<span class='alertMsg good'><i class='fa fa-check'></i><span class='text'><?=GetMessage('MFT_NOTIFY_MESSAGE')?></span></span>";		
			});
		}
	}
	if(!window.OpenNotifyPopup) {
		function OpenNotifyPopup() {		
			BX.NotifySet =
			{			
				popup: null,
				arParams: {}
			};
			BX.NotifySet.popup = BX.PopupWindowManager.create("notify", null, {
				autoHide: true,
				offsetLeft: 0,
				offsetTop: 0,			
				overlay: {
					opacity: 100
				},
				draggable: false,
				closeByEsc: false,
				closeIcon: { right : "-10px", top : "-10px"},
				titleBar: {content: BX.create("span", {html: "<?=GetMessage('MFT_NOTIFY')?>"})},
				content: "<div class='popup-window-wait'><i class='fa fa-spinner fa-pulse'></i></div>",			
				events: {
					onAfterPopupShow: function()
					{
						if(!BX("newNotifyForm")) {
							BX.ajax.post(
								"<?=$this->GetFolder();?>/popup.php",
								{							
									arParams: <?=CUtil::PhpToJSObject($popupParams)?>
								},
								BX.delegate(function(result)
								{
									this.setContent(result);
									var windowSize =  BX.GetWindowInnerSize(),
									windowScroll = BX.GetWindowScrollPos(),
									popupHeight = BX("notify").offsetHeight;
									BX("notify").style.top = windowSize.innerHeight/2 - popupHeight/2 + windowScroll.scrollTop + "px";
								},
								this)
							);
						}
					}
				}			
			});
			
			BX.addClass(BX("notify"), "pop-up notify");
			close = BX.findChildren(BX("notify"), {className: "popup-window-close-icon"}, true);
			if(!!close && 0 < close.length) {
				for(i = 0; i < close.length; i++) {					
					close[i].innerHTML = "<i class='fa fa-times'></i>";
				}
			}

			BX.NotifySet.popup.show();		
		}
	}
</script>