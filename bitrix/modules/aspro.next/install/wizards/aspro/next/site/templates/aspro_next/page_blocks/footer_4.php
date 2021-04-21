<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<div class="footer_inner <?=($arTheme["SHOW_BG_BLOCK"]["VALUE"] == "Y" ? "fill" : "no_fill");?> footer-grey">
	<?$APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		Array(
			"AREA_FILE_SHOW" => "file",
			"PATH" => SITE_DIR."include/footer/subscribe.php",
			"EDIT_TEMPLATE" => "include_area.php"
		)
	);?>
	<div class="bottom_wrapper">
		<div class="maxwidth-theme items">
			<div class="row bottom-middle">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-4 col-sm-3">
							<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
								"ROOT_MENU_TYPE" => "bottom_company",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "3600000",
								"MENU_CACHE_USE_GROUPS" => "N",
								"CACHE_SELECTED_ITEMS" => "N",
								"MENU_CACHE_GET_VARS" => array(
								),
								"MAX_LEVEL" => "1",
								"USE_EXT" => "N",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "Y"
								),
								false
							);?>
						</div>
						<div class="col-md-4 col-sm-3">
							<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
								"ROOT_MENU_TYPE" => "bottom_info",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "3600000",
								"MENU_CACHE_USE_GROUPS" => "N",
								"CACHE_SELECTED_ITEMS" => "N",
								"MENU_CACHE_GET_VARS" => array(
								),
								"MAX_LEVEL" => "1",
								"CHILD_MENU_TYPE" => "left",
								"USE_EXT" => "N",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "Y"
								),
								false
							);?>
						</div>
						<div class="col-md-4 col-sm-3">
							<?$APPLICATION->IncludeComponent("bitrix:menu", "bottom", array(
								"ROOT_MENU_TYPE" => "bottom_help",
								"MENU_CACHE_TYPE" => "A",
								"MENU_CACHE_TIME" => "3600000",
								"MENU_CACHE_USE_GROUPS" => "N",
								"CACHE_SELECTED_ITEMS" => "N",
								"MENU_CACHE_GET_VARS" => array(
								),
								"MAX_LEVEL" => "1",
								"CHILD_MENU_TYPE" => "left",
								"USE_EXT" => "N",
								"DELAY" => "N",
								"ALLOW_MULTI_SELECT" => "Y"
								),
								false
							);?>
						</div>
					</div>
	 			</div>
				<div class="col-md-4 contact-block">
					<div class="row">
						<div class="col-md-9 col-md-offset-2">
							<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/contacts-title.php", array(), array(
									"MODE" => "html",
									"NAME" => "Title",
									"TEMPLATE" => "include_area.php",
								)
							);?>
							<div class="info">
								<div class="row">
									<div class="col-md-12 col-sm-4">
										<?CNext::ShowHeaderPhones('', true);?>
									</div>
									<div class="col-md-12 col-sm-4">
										<?CNext::showEmail('email blocks');?>
									</div>
									<div class="col-md-12 col-sm-4">
										<?CNext::showAddress('address blocks');?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				</div>
				<div class="bottom-under">
					<div class="row">
						<div class="col-md-12 outer-wrapper">
							<div class="inner-wrapper row">
								<div class="copy-block">
									<div class="copy">
										<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/copy/copyright.php", Array(), Array(
												"MODE" => "php",
												"NAME" => "Copyright",
												"TEMPLATE" => "include_area.php",
											)
										);?>
									</div>
									<div class="print-block"><?=CNext::ShowPrintLink();?></div>
									<div id="bx-composite-banner"></div>
								</div>
								<div class="social-block">
									<?$APPLICATION->IncludeComponent(
										"aspro:social.info.next",
										".default",
										array(
											"CACHE_TYPE" => "A",
											"CACHE_TIME" => "3600000",
											"CACHE_GROUPS" => "N",
											"COMPONENT_TEMPLATE" => ".default"
										),
										false
									);?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>