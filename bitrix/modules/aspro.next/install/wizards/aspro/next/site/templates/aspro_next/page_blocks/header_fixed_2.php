<?
global $arTheme, $arRegion;
$logoClass = ($arTheme['COLORED_LOGO']['VALUE'] !== 'Y' ? '' : ' colored');
?>
<div class="maxwidth-theme">
	<div class="logo-row v2 row margin0 menu-row">
		<div class="inner-table-block nopadding logo-block">
			<div class="logo<?=$logoClass?>">
				<?=CNext::ShowLogo();?>
			</div>
		</div>
		<div class="inner-table-block menu-block">
			<div class="navs table-menu js-nav">
				<nav class="mega-menu sliced">
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
		<div class="inner-table-block nopadding small-block">
			<div class="wrap_icon wrap_cabinet">
				<?=CNext::ShowCabinetLink(true, false, 'big');?>
			</div>
		</div>
		<?=CNext::ShowBasketWithCompareLink('inner-table-block nopadding', 'big');?>
		<div class="inner-table-block small-block nopadding inline-search-show" data-type_search="fixed">
			<div class="search-block top-btn"><i class="svg svg-search lg"></i></div>
		</div>
	</div>
</div>