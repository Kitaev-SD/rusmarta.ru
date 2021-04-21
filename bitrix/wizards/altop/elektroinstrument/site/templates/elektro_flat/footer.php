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
							<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", 
								array(
									"ROOT_MENU_TYPE" => "footer1",
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
									"ROOT_MENU_TYPE" => "footer2",
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
									<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array("AREA_FILE_SHOW" => "file", "PATH" => SITE_DIR."include/copyright.php"), false);?>
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
</body>
</html>