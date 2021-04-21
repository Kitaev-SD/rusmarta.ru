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
																<h2>%HEADER_ORDER_INFO%</h2>
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
															%TEXT_ORDER_INFO%
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
								"bitrix:sale.personal.order.detail.mail",
								"aspro_full_info",
								Array(
									"ACTIVE_DATE_FORMAT" => "d.m.Y",
									"CACHE_TIME" => "3600",
									"CACHE_TYPE" => "A",
									"COMPOSITE_FRAME_MODE" => "A",
									"COMPOSITE_FRAME_TYPE" => "AUTO",
									"CUSTOM_SELECT_PROPS" => array("NAME", "QUANTITY"),
									"ID" => "{#ORDER_ID#}",
									"PATH_TO_CANCEL" => "/personal/cancel/ORDER_ID",
									"PATH_TO_LIST" => "",
									"PATH_TO_PAYMENT" => "payment.php",
									"PICTURE_HEIGHT" => "110",
									"PICTURE_RESAMPLE_TYPE" => "1",
									"PICTURE_WIDTH" => "110",
									"PROP_1" => array(),
									"PROP_2" => array(),
									"SHOW_ORDER_BASE" => "Y",
									"SHOW_ORDER_BASKET" => "Y",
									"SHOW_ORDER_BUYER" => "Y",
									"SHOW_ORDER_DELIVERY" => "Y",
									"SHOW_ORDER_PARAMS" => "Y",
									"SHOW_ORDER_PAYMENT" => "Y",
									"SHOW_ORDER_SUM" => "Y",
									"SHOW_ORDER_USER" => "Y",									
									"SITE_ID" => "%SITE_ID%",
									"THEME_COLOR" => "%THEME_COLOR%",
									"PERSONAL_PAGE" => "/personal/",
									"SHOW_PERSONAL" => "Y",
								)
							);?>
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
															%TEXT_ORDER_INFO_BOTTOM%
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
															<a class="bxBlockContentButton" style="background:%THEME_COLOR%" title="%MORE_ITEMS%" href="/catalog/" target="_blank">
																%MORE_ITEMS%
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