<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$colorFooter =  'footer-'.$arTheme['SIMPLE_BASKET']['DEPENDENT_PARAMS']['FOOTER_SIMPLE_COLOR']['VALUE'];?>
<div class="footer_inner <?=($arTheme["SHOW_BG_BLOCK"]["VALUE"] == "Y" ? "fill" : "no_fill");?> <?=$colorFooter?> ext_view">
	<div class="bottom_wrapper">
		<div class="wrapper_inner">
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
							<div class="pull-right pay_system_icons">
								<span class="">
									<?$APPLICATION->IncludeFile(SITE_DIR."include/footer/copy/pay_system_icons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage("PHONE"), "TEMPLATE" => "include_area.php",));?>
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
