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
<div class="header-wrapper header-simple-2" id="mobileheadersimple">
	<div class="logo_and_menu-row logo-block">
		<div class="logo-row">
      <div id="headerSimple">
  			<div class="maxwidth-theme col-md-12">
  				<div class="row mobile-block-simple">
						<div class="back-mobile-arrow visible-xs pull-left">
							<div class="arrow-back">
								<?=CNext::showIconSvg("arrow-back", SITE_TEMPLATE_PATH."/images/svg/m_cart_arrow.svg");?>
							</div>
						</div>
  					<?if($arRegions):?>
  						<div class="inline-block pull-left regions_padding hidden-xs">
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
  					<div class="logo-block col-md-2 col-sm-3 text-center header-simple-center">
  						<div class="logo<?=$logoClass?>">
  							<?=CNext::ShowLogo();?>
  						</div>
  					</div>
  					<div class="right-icons pull-right">
  						<div class="phone-block with_btn hidden-xs">
  							<?if($bPhone):?>
  								<div class="inner-table-block">
  									<?CNext::ShowHeaderPhones();?>
  									<div class="schedule">
  										<?$APPLICATION->IncludeFile(SITE_DIR."include/header-schedule.php", array(), array("MODE" => "html","NAME" => GetMessage('HEADER_SCHEDULE'),"TEMPLATE" => "include_area.php"));?>
  									</div>
  								</div>
  							<?endif?>
  							<?if($arTheme['SHOW_CALLBACK']['VALUE'] == 'Y'):?>
  								<div class="inner-table-block">
  									<span class="callback-block animate-load twosmallfont colored white btn-default btn" data-event="jqm" data-param-form_id="CALLBACK" data-name="callback"><?=GetMessage("CALLBACK")?></span>
  								</div>
  							<?endif;?>
  						</div>
							<div class="pull-right  visible-xs">
								<div class="wrap_icon wrap_phones">
									<?CNext::ShowHeaderMobilePhones("big");?>
								</div>
							</div>
  					</div>
  				</div>
  			</div>
      </div>
		</div><?// class=logo-row?>
	</div>
	<div class="line-row hidden-xs"></div>
</div>
