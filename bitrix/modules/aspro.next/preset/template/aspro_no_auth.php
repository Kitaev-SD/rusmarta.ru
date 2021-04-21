<table class="mail-grid" width="600" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
			<table class="mail-grid-cell"   width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
				<tr>
					<td data-bx-block-editor-place="body">

						<!-- content title -->
						<div data-bx-block-editor-block-type="text">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockText">
								<tbody class="bxBlockOut">
									<tr>
										<td valign="top" class="bxBlockInn bxBlockInnText bxColored">
											<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
												<tbody>
													<tr>
														<td valign="top" class="bxBlockPadding1 bxBlockContentText bxBlockHeader">													
															<div class="title">
																<h2>%HEADER_NO_AUTH%</h2>
															</div>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<!-- /content title -->

						<div data-bx-block-editor-block-type="text">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockText">
								<tbody class="bxBlockOut">
									<tr>
										<td valign="top" class="bxBlockInn bxBlockInnText">
											<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
												<tbody>
													<tr>
														<td valign="top" class="bxBlockPadding bxBlockContentText">
															%TEXT_NO_AUTH_UNDER%
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div data-bx-block-editor-block-type="button">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockButton">
								<tbody class="bxBlockOut">
									<tr>
										<td valign="top" class="bxBlockPadding bxBlockInn bxBlockInnButton">
											<table align="center" border="0" cellpadding="0" cellspacing="0" class="bxBlockContentButtonEdge">
												<tbody>
													<tr>
														<td valign="top">
															<a class="bxBlockContentButton" style="background:%THEME_COLOR%" title="%SHOW_CATALOG_LINK%" href="/catalog/" target="_blank">
																%SHOW_CATALOG_LINK%
															</a>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div data-bx-block-editor-block-type="text">
							<table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockText">
								<tbody class="bxBlockOut">
									<tr>
										<td valign="top" class="bxBlockInn bxBlockInnText">
											<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
												<tbody>
													<tr>
														<td valign="top" class="bxBlockPadding bxBlockContentText">
															%TEXT_NO_AUTH_BOTTOM%
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</div>

						<div data-bx-block-editor-block-type="component">
							<?EventMessageThemeCompiler::includeComponent(
								"bitrix:news.list.mail",
								"aspro_sale",
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
									"SITE_ADDRESS" => "%SITE_ADDRESS%",
									"IBLOCK_ID" => "%SALE_IBLOCK_ID%",
									"IBLOCK_TYPE" => "aspro_next_content",
									"INCLUDE_SUBSECTIONS" => "Y",
									"NEWS_COUNT" => "5",
									"PARENT_SECTION" => "",
									"PARENT_SECTION_CODE" => "",
									"PREVENT_SEND_IF_NO_NEWS" => "N",
									"PREVIEW_TRUNCATE_LEN" => "",
									"PROPERTY_CODE" => array("", ""),
									"SENDER_CHAIN_ID" => "{#SENDER_CHAIN_ID#}",
									"SORT_BY1" => "ACTIVE_FROM",
									"SORT_BY2" => "SORT",
									"SORT_ORDER1" => "DESC",
									"SORT_ORDER2" => "ASC",
									"THEME_COLOR" => "%THEME_COLOR%",
									"SALE_PAGE" => "/sale/",
									"SHOW_SALE" => "Y",
								),
								false
							);?>
						</div>

						<div data-bx-block-editor-block-type="component">
							<?EventMessageThemeCompiler::includeComponent(
								"bitrix:sale.basket.basket.small.mail",
								"aspro_init",
								Array(
									"COLUMNS_LIST" => array("NAME"),
									"COMPONENT_TEMPLATE" => ".default",
									"PATH_TO_BASKET" => "/basket/",
									"PATH_TO_ORDER" => "/order/",
									"SHOW_DELAY" => "Y",
									"SHOW_NOTAVAIL" => "Y",
									"SHOW_SUBSCRIBE" => "Y",
									"SITE_ID" => "%SITE_ID%",
									"SITE_ADDRESS" => "%SITE_ADDRESS%",
									"THEME_COLOR" => "%THEME_COLOR%",
									"SHOW_SUBSCRIBE" => "Y",
									"USER_ID" => "{#USER_ID#}"
								)
							);?>
						</div>

						<div data-bx-block-editor-block-type="component">
							<?EventMessageThemeCompiler::includeComponent(
								"bitrix:sale.bigdata.personal.mail",
								"aspro_bigdata",
								Array(
									"COMPOSITE_FRAME_MODE" => "A",
									"COMPOSITE_FRAME_TYPE" => "AUTO",
									"SITE_ID" => "%SITE_ID%",
									"SITE_ADDRESS" => "%SITE_ADDRESS%",
									"THEME_COLOR" => "%THEME_COLOR%",
									"TITLE" => "%NO_AUTH_GOODS_TITLE%",
									"CATALOG_PAGE" => "/catalog/",
									"SHOW_CATALOG" => "Y",
									"USER_ID" => "{#USER_ID#}"
								)
							);?>
						</div>
						
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
									"SITE_ADDRESS" => "%SITE_ADDRESS%",
									"IBLOCK_ID" => "%TIZER_IBLOCK_ID%",
									"IBLOCK_TYPE" => "aspro_next_content",
									"INCLUDE_SUBSECTIONS" => "Y",
									"NEWS_COUNT" => "3",
									"PARENT_SECTION" => "",
									"PARENT_SECTION_CODE" => "",
									"PREVENT_SEND_IF_NO_NEWS" => "N",
									"PREVIEW_TRUNCATE_LEN" => "",
									"PROPERTY_CODE" => array("", ""),
									"SENDER_CHAIN_ID" => "{#SENDER_CHAIN_ID#}",
									"SORT_BY1" => "ACTIVE_FROM",
									"SORT_BY2" => "SORT",
									"SORT_ORDER1" => "DESC",
									"SORT_ORDER2" => "ASC",
								),
								false
							);?>
						</div>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>