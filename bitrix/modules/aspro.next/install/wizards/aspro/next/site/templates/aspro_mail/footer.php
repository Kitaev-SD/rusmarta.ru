				</div>
				<!-- /main content -->
				
				<!-- bottom content -->
					<div>
						<?global $arIblocks;
						$tizer_iblock_id = ($arIblocks["aspro_next_content"]["aspro_next_tizers"][0] ? $arIblocks["aspro_next_content"]["aspro_next_tizers"][0] : 0);
						?>
						<div data-bx-block-editor-block-type="component">
							<?EventMessageThemeCompiler::includeComponent(
								"bitrix:news.list.mail",
								"aspro_tizers",
								Array(
									"ACTIVE_DATE_FORMAT" => "d.m.Y",
									"CACHE_FILTER" => "N",
									"CACHE_GROUPS" => "N",
									"CACHE_TIME" => "3600",
									"CACHE_TYPE" => "A",
									"CHECK_DATES" => "Y",
									"COMPOSITE_FRAME_MODE" => "A",
									"COMPOSITE_FRAME_TYPE" => "AUTO",
									"DETAIL_URL" => "",
									"DISPLAY_DATE" => "Y",
									"DISPLAY_NAME" => "Y",
									"DISPLAY_PICTURE" => "Y",
									"DISPLAY_PREVIEW_TEXT" => "Y",
									"FIELD_CODE" => array("", ""),
									"FILTER_NAME" => "",
									"HIDE_LINK_WHEN_NO_DETAIL" => "N",
									"FROM_TEMPLATE" => "Y",
									//"SITE_ADDRESS" => "%SITE_ADDRESS%",
									"IBLOCK_ID" => $tizer_iblock_id,
									"IBLOCK_TYPE" => "aspro_next_content",
									"INCLUDE_SUBSECTIONS" => "Y",
									"NEWS_COUNT" => "5",
									"PARENT_SECTION" => "",
									"PARENT_SECTION_CODE" => "",
									"PREVENT_SEND_IF_NO_NEWS" => "Y",
									"PREVIEW_TRUNCATE_LEN" => "",
									"PROPERTY_CODE" => array("", ""),
									"SENDER_CHAIN_ID" => "{#SENDER_CHAIN_ID#}",
									"SORT_BY1" => "SORT",
									"SORT_BY2" => "ID",
									"SORT_ORDER1" => "ASC",
									"SORT_ORDER2" => "DESC"
								),
								false
							);?>
						</div>
					</div>
				<!-- /bottom content -->
			</center>
		</div>
		<!-- /mail-wrap -->
		
		<!-- footer -->
			<div style="max-width: 600px;min-width:600px !important;width:100% !important;text-align:center;margin:0px auto;">
				<?global $copyright, $vk_social, $fb_social, $odn_social, $tw_social, $mail_social, $inst_social, $g_social, $y_social;?>
				<div style="color:#666666;font-size:13px;padding:10px 20px 0px 20px;"><?=date("Y")." ".$copyright;?></div>
				<div style="padding-bottom:30px;">
					<?EventMessageThemeCompiler::includeComponent(
						"aspro:social.info.next", 
						"mail", 
						array(
							"CACHE_TYPE" => "A",
							"CACHE_TIME" => "86400",
							"CACHE_GROUPS" => "N",
							"COMPONENT_TEMPLATE" => "mail",
						),
						false
					);?>
				</div>
			</div>
		<!-- /footer -->
	</body>
</html>