<?Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("footer-subscribe");?>
	<?if(\Bitrix\Main\ModuleManager::isModuleInstalled("subscribe")):?>
		<div class="subscribe-block-wrapper">
			<div class="maxwidth-theme">
				<div class="row">
					<div class="col-md-12">
						<div class="outer-wrapper">
							<div class="inner-wrapper">
								<div class="row">
									<div class="col-md-4 hidden-sm text">
									<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/left_subscribe_text.php", Array(), Array(
												"MODE" => "php",
												"NAME" => "Subscribe text",
											)
										);?>
									</div>
									<div class="col-md-8 col-sm-12">
										<?$APPLICATION->IncludeComponent(
											"bitrix:subscribe.edit", 
											"footer", 
											array(
												"AJAX_MODE" => "N",
												"AJAX_OPTION_ADDITIONAL" => "",
												"AJAX_OPTION_HISTORY" => "N",
												"AJAX_OPTION_JUMP" => "N",
												"AJAX_OPTION_SHADOW" => "Y",
												"AJAX_OPTION_STYLE" => "Y",
												"ALLOW_ANONYMOUS" => "Y",
												"CACHE_TIME" => "36000000",
												"CACHE_TYPE" => "A",
												"COMPOSITE_FRAME_MODE" => "A",
												"COMPOSITE_FRAME_TYPE" => "AUTO",
												"PAGE" => "personal/subscribe/",
												"SET_TITLE" => "N",
												"SHOW_AUTH_LINKS" => "N",
												"SHOW_HIDDEN" => "N",
												"COMPONENT_TEMPLATE" => "footer"
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
	<?endif;?>
<?Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("footer-subscribe", "");?>