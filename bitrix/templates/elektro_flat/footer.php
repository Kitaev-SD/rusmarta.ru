<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);?>
							</div>							
						</div>
						<?if($APPLICATION->GetCurPage(true)== SITE_DIR."index.php"):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DIR."include/vendors_bottom.php",
									"AREA_FILE_RECURSIVE" => "N",
									"EDIT_MODE" => "html",
								),
								false,
								array("HIDE_ICONS" => "Y")
							);?>
						<?endif;?>
						<?if(!CSite::InDir('/news/')):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DIR."include/news_bottom.php",
									"AREA_FILE_RECURSIVE" => "N",
									"EDIT_MODE" => "html",
								),
								false,
								array("HIDE_ICONS" => "Y")
							);?>
						<?endif;?>
						<?if(!CSite::InDir('/reviews/')):?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", "",
								array(
									"AREA_FILE_SHOW" => "file",
									"PATH" => SITE_DIR."include/reviews_bottom.php",
									"AREA_FILE_RECURSIVE" => "N",
									"EDIT_MODE" => "html",
								),
								false,
								array("HIDE_ICONS" => "Y")
							);?>
						<?endif;?>
					</div>
					<?$APPLICATION->IncludeComponent("bitrix:subscribe.form", "bottom", 
						array(
							"USE_PERSONALIZATION" => "Y",	
							"PAGE" => SITE_DIR."personal/mailings/",
							"SHOW_HIDDEN" => "N",
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "36000000",
							"CACHE_NOTES" => ""
						),
						false
					);?>					
				</div>
			</div>
			<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/viewed_products.php"), false);?>
			<footer>
				<div class="center<?=($arSetting['SITE_BACKGROUND']['VALUE'] == 'Y' ? ' inner' : '');?>">
					<div class="footer_menu_soc_pay">
						<div class="footer_menu">
							<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"bottom", 
	array(
		"ROOT_MENU_TYPE" => "footer1",
		"MENU_CACHE_TYPE" => "Y",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"CACHE_SELECTED_ITEMS" => "N",
		"COMPONENT_TEMPLATE" => "bottom",
		"DELAY" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
							<?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"bottom", 
	array(
		"ROOT_MENU_TYPE" => "footer2",
		"MENU_CACHE_TYPE" => "Y",
		"MENU_CACHE_TIME" => "36000000",
		"MENU_CACHE_USE_GROUPS" => "N",
		"MENU_CACHE_GET_VARS" => array(
		),
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "",
		"USE_EXT" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"CACHE_SELECTED_ITEMS" => "N",
		"COMPONENT_TEMPLATE" => "bottom",
		"DELAY" => "N",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO"
	),
	false
);?>
							<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", 
								array(
									"ROOT_MENU_TYPE" => "footer3",
									"MENU_CACHE_TYPE" => "A",
									"MENU_CACHE_TIME" => "36000000",
									"MENU_CACHE_USE_GROUPS" => "Y",
									"MENU_CACHE_GET_VARS" => array(),
									"MAX_LEVEL" => "1",
									"CHILD_MENU_TYPE" => "",
									"USE_EXT" => "N",
									"ALLOW_MULTI_SELECT" => "N",
									"CACHE_SELECTED_ITEMS" => "N"
								),
								false
							);?>
							<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", 
								array(
									"ROOT_MENU_TYPE" => "footer4",
									"MENU_CACHE_TYPE" => "A",
									"MENU_CACHE_TIME" => "36000000",
									"MENU_CACHE_USE_GROUPS" => "Y",
									"MENU_CACHE_GET_VARS" => array(),
									"MAX_LEVEL" => "1",
									"CHILD_MENU_TYPE" => "",
									"USE_EXT" => "N",
									"ALLOW_MULTI_SELECT" => "N",
									"CACHE_SELECTED_ITEMS" => "N"
								),
								false
							);?>
						</div>
						<div class="footer_soc_pay">							
							<div class="footer_soc">
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/join_us.php"), false, array("HIDE_ICONS" => "Y"));?>
							</div>
							<div class="footer_pay">
								<?global $arPayIcFilter;
								$arPayIcFilter = array();?>
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/payments_icons.php"), false, array("HIDE_ICONS" => "Y"));?>
							</div>
						</div>
					</div>
					<div class="footer-bottom">						
						<div class="footer-bottom__blocks">
							<div class="footer-bottom__block-wrap fb-left">
								<div class="footer-bottom__block footer-bottom__copyright">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "template4", Array(
	"AREA_FILE_SHOW" => "file",	
		"PATH" => SITE_DIR."include/copyright.php",	
	false
));?>
								</div>
								<div class="footer-bottom__block footer-bottom__links">
									<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", 
										array(
											"ROOT_MENU_TYPE" => "bottom",
											"MENU_CACHE_TYPE" => "A",
											"MENU_CACHE_TIME" => "36000000",
											"MENU_CACHE_USE_GROUPS" => "Y",
											"MENU_CACHE_GET_VARS" => array(),
											"MAX_LEVEL" => "1",
											"CHILD_MENU_TYPE" => "",
											"USE_EXT" => "N",
											"ALLOW_MULTI_SELECT" => "N",
											"CACHE_SELECTED_ITEMS" => "N"
										),
										false
									);?>
								</div>
							</div>
						</div>
						<div class="footer-bottom__blocks">							
							<div class="footer-bottom__block-wrap fb-right">
								<div class="footer-bottom__block footer-bottom__counter">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/counter_1.php"), false);?>
								</div>
								<div class="footer-bottom__block footer-bottom__counter">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/counter_2.php"), false);?>
								</div>
								<div class="footer-bottom__block footer-bottom__design">
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/developer.php"), false);?>
								</div>
							</div>
						</div>						
					</div>
					<div class="foot_panel_all">
						<div class="foot_panel">
							<div class="foot_panel_1">
								<?$APPLICATION->IncludeComponent("bitrix:system.auth.form", "login",
									array(
										"REGISTER_URL" => SITE_DIR."personal/private/",
										"FORGOT_PASSWORD_URL" => SITE_DIR."personal/private/",
										"PROFILE_URL" => SITE_DIR."personal/private/",
										"SHOW_ERRORS" => "N" 
									 ),
									 false,
									 array("HIDE_ICONS" => "Y")
								);?>
								<?$APPLICATION->IncludeComponent("bitrix:main.include", "", 
									array(
										"AREA_FILE_SHOW" => "file", 
										"PATH" => SITE_DIR."include/footer_compare.php"
									),
									false,
									array("HIDE_ICONS" => "Y")
								);?>
								<?$APPLICATION->IncludeComponent("altop:sale.basket.delay", ".default", 
									array(
										"PATH_TO_DELAY" => SITE_DIR."personal/cart/?delay=Y",
									),
									false,
									array("HIDE_ICONS" => "Y")
								);?>
							</div>
							<div class="foot_panel_2">
								<?$APPLICATION->IncludeComponent("bitrix:sale.basket.basket.line", ".default", 
									array(
										"PATH_TO_BASKET" => SITE_DIR."personal/cart/",
										"PATH_TO_ORDER" => SITE_DIR."personal/order/make/",
										"HIDE_ON_BASKET_PAGES" => "N",
									),
									false,
									array("HIDE_ICONS" => "Y")
								);?>								
							</div>
						</div>
					</div>
				</div>
			</footer>
			<?if($arSetting["SITE_BACKGROUND"]["VALUE"] == "Y"):?>
				</div>
			<?endif;?>
		</div>
	</div>


<!-- Oneretarget container-->
<script type="text/javascript">
   (function (w,d) {
       var ts = d.createElement("script");
       ts.type = "text/javascript";
       ts.async = true;
       var domain = window.location.hostname;
       ts.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//tag.oneretarget.com/7477_" + domain + ".js?v=1";
       var f = function () { var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ts, s); };
       if (w.opera == "[object Opera]") { d.addEventListener("DOMContentLoaded", f, false); } else { f(); }
   })(window, document);
</script>
	<!-- /Oneretarget container--> 


<!-- Calc HDD -->

<div class="remodal" data-remodal-id="modal__calc">
		<button data-remodal-action="close" class="remodal-close"></button>
		<div class="hdd___calc">
			<div class="hdd___calc-header">
				<div class="hdd___calc-header-title">����������� ������� HDD ��� ��������� ���������������</div>
				<div class="hdd___calc-header-title_sub">�����������, ����� ������� ���� HDD ���������� �������� ��� ������ ���������.</div>
			</div>
			<div class="hdd___calc-content">
				<div class="hdd___calc-content-item">
					<div class="hdd___calc-content-item-title">���������� ������:</div>
					<div class="custom__select">
						<div class="custom__select-current" data-val="2700">1�� (1280x720p)</div>
						<div class="custom__select-content">
							<div data-val="2700" class="custom__select-content-option">1�� (1280x720p)</div>
							<div data-val="6075" class="custom__select-content-option">2�� (1920x1080p)</div>
							<div data-val="9216" class="custom__select-content-option">3�� (2048x1536)</div>
							<div data-val="14580" class="custom__select-content-option">5�� (2592x1920)</div>
						</div>
					</div>
				</div>
				<div class="hdd___calc-content-item">
					<div class="hdd___calc-content-item-title">������ ������:</div>
					<ul class="custom__radio h264">
						<li data-val="74.9" class="current__item">H.264</li>
						<li data-val="81,5">H.265</li>
						<li data-val="85,5">H.265+</li>
					</ul>
				</div>
				<div class="hdd___calc-content-item">
					<div class="hdd___calc-content-item-title">�������� ����� ������:</div>
					<ul class="custom__radio daymode">
						<li data-val="8">8 ����� � �����</li>
						<li data-val="12" class="current__item">12 ����� � �����</li>
						<li data-val="24">�������������</li>
					</ul>
				</div>
				<div class="hdd___calc-content-item">
					<div class="hdd___calc-content-item-title">�������� ������:</div>
					<ul class="custom__radio fps">
						<li data-val="5">5 ������/c.</li>
						<li data-val="10">10 ������/c.</li>
						<li data-val="15">15 ������/c.</li>
						<li data-val="20" class="current__item">20 ������/c.</li>
						<li data-val="25">25 ������/c.</li>
					</ul>
				</div>
				<br>
				<div class="hdd___calc-content-item">
					<div class="hdd___calc-content-item-row">
						<div class="hdd___calc-content-item-row-item">
							<div class="hdd___calc-content-item-title">���������� �����:</div>
							<input type="number" min="1" max="32" name="kamera" size="3" maxlength="50" value="">
							<div class="hdd___calc-content-item-error"></div>
						</div>
						<div class="hdd___calc-content-item-row-item">
							<div class="hdd___calc-content-item-title">������� ������ (����):</div>
							<input type="number" min="1" max="365" name="arhiv" size="3" maxlength="50" value="">
							<div class="hdd___calc-content-item-error"></div>
						</div>
					</div>
				</div>
				<div class="hdd___calc-content-item">
					<div class="hdd___calc-order-result"></div>
					<button class="hdd___calc-order">����������</button>
				</div>
			</div>
		</div>
	</div>
<!-- Calc HDD END -->
<div class="modal_no_cookie modal_no_cookie_second">
<div class="modal_no_cookie_close">&times;</div>
	�������������� ��� ���������� ������� ����������, <a href="https://yandex.ru/support/common/browsers-settings/browsers-cookies.html" target="_blank">��������� ��������� cookie</a> � ����� ��������
</div>
<!-- Regmarkets -->

<script >
function run_regmarkets() {
	var regmarkets_script = document.createElement('script');
	regmarkets_script.setAttribute("type", "text/javascript"); 
	regmarkets_script.setAttribute("src", "https://regmarkets.ru/js/r17.js?v=1"); 
	regmarkets_script.setAttribute("async", "");
}
setTimeout(run_regmarkets, 3000);
</script>

<!-- /Regmarkets -->

<!-- Roistat Begin -->
<script>
function run_roistat() {
	(function(w, d, s, h, id) {
		w.roistatProjectId = id; w.roistatHost = h;
		var p = d.location.protocol == "https:" ? "https://" : "http://";
		var u = /^.*roistat_visit=[^;]+(.*)?$/.test(d.cookie) ? "/dist/module.js?v=1" : "/api/site/1.0/"+id+"/init?referrer="+encodeURIComponent(d.location.href);
		var js = d.createElement(s); js.charset="UTF-8"; js.async = 1; js.src = p+h+u; var js2 = d.getElementsByTagName(s)[0]; js2.parentNode.insertBefore(js, js2);
	})
	(window, document, 'script', 'cloud.roistat.com', '9f4ad8a4374d7bf79c7198033ec9efd2');
}
setTimeout(run_roistat, 5000);
</script>
<!-- Roistat End -->

<!-- Pixel VK -->
<script type="text/javascript">

function run_vk() {
! function() {
		var t = document.createElement("script");
		t.type = "text/javascript", t.async = !0, t.src = "https://vk.com/js/api/openapi.js?162", t.onload = function() {
			VK.Retargeting.Init("VK-RTRG-431265-egnQ4"), VK.Retargeting.Hit()
		}, document.head.appendChild(t)
	}();
}
setTimeout(run_vk, 5000);
</script>

<noscript><img src="https://vk.com/rtrg?p=VK-RTRG-431265-egnQ4" style="position:fixed; left:-999px;" alt=""/></noscript>
<!-- Pixel VK END -->

<? 
$path = str_replace('//', '/', $_SERVER['DOCUMENT_ROOT'].'/'.SITE_DIR.'include/');
$file = $path.'invis-counter.php';
@include_once $file;
?>


<!-- lazy load  -->
<script>
	var images = document.querySelectorAll('img.data-lazy-src, iframe.data-lazy-src');
	var backgrounds = document.querySelectorAll('span.data-lazy-background, a.data-lazy-background');

	var options = {
		root: null,
		rootMargin: '0px',
		threshold: 0.1,
	}

	function handleImg(myImg, observer) {
		myImg.forEach(function(myImgSingle) {
			if (myImgSingle.intersectionRatio > 0) {
				loadImage(myImgSingle.target);
			}
		})
	}

	function handleSpan(elements, observer) {
		elements.forEach(function(element) {
			if (element.intersectionRatio > 0) {
				loadElementBack(element.target);
			}
		})
	}

	function loadImage(image) {
		if (image.hasAttribute('data-lazy-src')) {
			image.src = image.getAttribute('data-lazy-src');
			image.removeAttribute('data-lazy-src');
			image.style.opacity = 1;
		}
	}

	function loadElementBack(element) {
		if (element.hasAttribute('data-lazy-background')) {
			element.setAttribute('style', element.getAttribute('data-lazy-background'));
			element.setAttribute('style', element.getAttribute('data-lazy-background'));
			element.removeAttribute('data-lazy-background');
			element.style.opacity = 1;
		}
	}
 
	var observer = new IntersectionObserver(handleImg, options);
	var observer2 = new IntersectionObserver(handleSpan, options);

	images.forEach(function(img) {
		observer.observe(img);
	})

	backgrounds.forEach(function(span) {
		observer2.observe(span);
	})
</script>


</body>
</html>