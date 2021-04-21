<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
global $arTheme, $arRegion;
$arRegions = CNextRegionality::getRegions();
if($arRegion)
	$bPhone = ($arRegion['PHONES'] ? true : false);
else
	$bPhone = ((int)$arTheme['HEADER_PHONES'] ? true : false);
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
?>

<div class="header-v13 header-wrapper">
	<div class="mega_fixed_menu">
		<div class="maxwidth-theme">
			<div class="row">
				<div class="col-md-12">
					<div class="menu-only">
						<nav class="mega-menu">
							<?=CNext::showIconSvg("close dark", SITE_TEMPLATE_PATH."/images/svg/Close.svg");?>
							<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
								array(
									"COMPONENT_TEMPLATE" => ".default",
									"PATH" => SITE_DIR."include/menu/menu.top.php",
									"AREA_FILE_SHOW" => "file",
									"AREA_FILE_SUFFIX" => "",
									"AREA_FILE_RECURSIVE" => "Y",
									"EDIT_TEMPLATE" => "include_area.php"
								),
								false, array("HIDE_ICONS" => "Y")
							);?>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="logo_and_menu-row">
		<div class="logo-row">
			<div class="maxwidth-theme">
				<div class="row">
					<div class="burger pull-left"><?=CNext::showIconSvg("burger dark", SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?></div>
					<div class="logo-block col-md-2 col-sm-3">
						<div class="logo<?=$logoClass?>">
							<?=CNext::ShowLogo();?>
						</div>
					</div>
					<?if($arTheme['ORDER_BASKET_VIEW']['VALUE'] !== 'NORMAL'):?>
						<div class="col-md-2 hidden-sm hidden-xs">
							<div class="top-description">
								<?$APPLICATION->IncludeFile(SITE_DIR."include/top_page/slogan.php", array(), array(
										"MODE" => "html",
										"NAME" => "Text in title",
										"TEMPLATE" => "include_area.php",
									)
								);?>
							</div>
						</div>
					<?endif;?>
					<?if($arRegions):?>
						<div class="inline-block pull-left">
							<div class="top-description">
								<?$APPLICATION->IncludeComponent("bitrix:main.include", ".default",
									array(
										"COMPONENT_TEMPLATE" => ".default",
										"PATH" => SITE_DIR."include/top_page/regionality.list.php",
										"AREA_FILE_SHOW" => "file",
										"AREA_FILE_SUFFIX" => "",
										"AREA_FILE_RECURSIVE" => "Y",
										"EDIT_TEMPLATE" => "include_area.php"
									),
									false
								);?>
							</div>
						</div>
					<?endif;?>
					<div class="right-icons pull-right">
						<div class="pull-right show-fixed">
							<div class="wrap_icon">
								<button class="top-btn inline-search-show twosmallfont">
									<?=CNext::showIconSvg("search big", SITE_TEMPLATE_PATH."/images/svg/Search_big_black.svg");?>
								</button>
							</div>
						</div>
						<div class="pull-right block-link">
							<?=CNext::ShowBasketWithCompareLink('with_price', 'big', true, 'wrap_icon inner-table-block baskets');?>
						</div>
						<div class="pull-right">
							<div class="wrap_icon inner-table-block">
								<?=CNext::showCabinetLink(true, true, 'big', true);?>
							</div>
						</div>
						<div class="pull-right">
							<div class="wrap_icon inner-table-block">
								<div class="phone-block">
									<?CNext::ShowHeaderPhones();?>
									<?if($arTheme['SHOW_CALLBACK']['VALUE'] == 'Y'):?>
										<span><span class="callback-block animate-load twosmallfont colored" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?=GetMessage("CALLBACK")?></span></span>
									<?endif;?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><?// class=logo-row?>
	</div>
	<div class="line-row visible-xs"></div>
</div>