<?require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/aspro.next/classes/general/mailing_functions.php");?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?=$arSite['CHARSET'];?>" />
		<style type="text/css">
			.wrapper_block{margin:0;padding:0;background:#f2f2f2;font-family:Arial, sans-serif;font-size:13px;color:#525c70;line-height:20px;}
			#bxStylistHeader, .bxBlockContentButton{background:<?=$bg_color;?>;}
		</style>
	</head>
	<body style="height:100% !important;margin:0;padding:0;font-family: Arial, Helvetica, sans-serif;width:100% !important;background: #f9f9f9;min-width:600px !important;">
		<!-- mail-wrap -->
		<div class="mail-wrap <?=$type_color;?>" style="width:100%;max-width: 600px;margin: 20px auto;overflow:hidden;border-radius: 3px;border:1px solid #dedede;background: #FFFFFF;">
			<center>
				<!-- top content -->
					<table class="mail-grid"   width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td id="bxStylistHeader" style="border-bottom:1px solid #dedede;margin-bottom:20px;padding-top: 26px;padding-right: 30px;padding-bottom: 26px;padding-left: 30px;background:#fff">
								<!-- 2 cell -->
								<table class="mail-grid"   width="100%" border="0" cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td class="wrap_header" style="border-radius:2px 2px 0px 0px;font-size:0px;">
											<div class="inner_blocks" style="display:inline-block;vertical-align:middle;font-size:12px;width: auto !important;">
												<table class="mail-grid-cell" border="0" cellspacing="0" cellpadding="0" width="270px">
													<tr>
														<td data-bx-block-editor-place="leftColumnHeader">
															<!-- content -->
															<div data-bx-block-editor-block-type="image">
																<table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockImage">
																	<tbody class="bxBlockOut">
																	<tr>
																		<td valign="top" class="bxBlockInn bxBlockInnImage">
																			<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
																				<tbody>
																				<tr>
																					<td valign="top" class="bxBlockPadding bxBlockContentImage" style="text-align: left">
																						<div style="max-width: 190px;">
																							<a href="/" class="link_img_logo" style="display: inline-block;zoom: 1;vertical-align: middle;margin: 0;max-width: 190px;">
																								<img style="max-width:100%;background:<?=$bg_color_logo;?>;display:block;" data-bx-editor-def-image="0" src="<?=$logo_src;?>" class="bxImage">
																							</a>
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
															<!-- /content -->
														</td>
													</tr>
												</table>
											</div>
											<div class="inner_blocks"  style="display:inline-block;vertical-align:middle;font-size:12px;width: auto !important;">
												<table class="mail-grid-cell" border="0" cellspacing="0" cellpadding="0" width="270px">
													<tr>
														<td data-bx-block-editor-place="rightColumnHeader" class="phone">
															<!-- content -->
															<div data-bx-block-editor-block-type="text">
																<table border="0" cellpadding="0" cellspacing="0" width="100%" class="bxBlockText">
																	<tbody class="bxBlockOut">
																	<tr>
																		<td valign="top" class="bxBlockInn bxBlockInnText">
																			<table align="left" border="0" cellpadding="0" cellspacing="0" width="100%">
																				<tbody>
																				<tr>
																					<td valign="top" class="bxBlockPadding bxBlockContentText" style="font-size: 20px;text-align: right;vertical-align: middle;">
																						<div class="phone_block" style="position: relative;text-align: right !important;padding-top: 0px !important;">
																							<div class="phone_outer" style="border-radius: 100%;color: #000;background-color: <?=$theme_color;?>;font-size: 16px;margin: 2px 5px 0;width: 35px;height: auto;text-align: center;line-height: 26px;padding-top: 9px;padding-bottom: 0px;display: inline-block;vertical-align: top;">
																								<div class="phone" style="background:url(<?=$bg_image;?>) <?=$bg_phone_position;?> no-repeat;margin-right:0px;display: inline-block;width: 13px;height:20px;vertical-align: top;"></div>
																							</div>
																							<div class="phone_text" style="display: inline-block;vertical-align: top;">
																								<?=str_replace("<a", "<a style='text-decoration:none;color:#1d2029;font-size:18px;display: block;word-wrap: break-word;'", $phone);?>
																							</div>
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
															<!-- /content -->
														</td>
													</tr>
												</table>
											</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				<!-- /top content -->
				
				<!-- main content -->
					<div class="main_content" style="padding-top:20px;padding-bottom:20px;text-align:left;padding-left:20px;padding-right:20px;">