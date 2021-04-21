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

<div class="header-v15 header-wrapper">
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
					<div class="col-md-5">
						<div class="burger pull-left"><?=CNext::showIconSvg("burger dark", SITE_TEMPLATE_PATH."/images/svg/Burger_big_white.svg");?></div>
						<?if($arRegions):?>
							<div class="wrap_icon inner-table-block">
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
						<div class="wrap_icon inner-table-block">
							<div class="phone-block">
								<?if($bPhone):?>
									<?CNext::ShowHeaderPhones('lg');?>
								<?endif?>
								<?if($arTheme['SHOW_CALLBACK']['VALUE'] == 'Y'):?>
									<div class="inline-block">
										<span class="callback-block animate-load twosmallfont colored" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?=GetMessage("CALLBACK")?></span>
									</div>
								<?endif;?>
							</div>
						</div>
					</div>
				
					<div class="logo-block col-lg-3 col-md-2 text-center">
						<div class="logo<?=$logoClass?>">
							<?=CNext::ShowLogo();?>
						</div>
					</div>
					<div class="right-icons pull-right">
						<div class="pull-right show-fixed">
							<div class="wrap_icon">
								<button class="top-btn inline-search-show twosmallfont">
									<?=CNext::showIconSvg("search big", SITE_TEMPLATE_PATH."/images/svg/Search_big_black.svg");?>
								</button>
							</div>
						</div>
						<div class="pull-right">
							<div class="wrap_icon wrap_basket">
								<?=CNext::ShowBasketWithCompareLink('', 'big');?>
							</div>
						</div>
						<div class="pull-right">
							<div class="wrap_icon wrap_cabinet">
								<?=CNext::showCabinetLink(true, false, 'big');?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><?// class=logo-row?>
	</div>
	<div class="line-row visible-xs"></div>
</div>