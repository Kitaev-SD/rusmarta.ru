<?    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	$this->setFrameMode(true);


	$TPL_ID = 'BXMAKER.GEOIP.EPILOG.DEFAULT_';

	$oManager = \Bxmaker\GeoIP\Manager::getInstance();


?>

<div class="c-bxmaker_ipgeo_epilog_default-box">


	<div class="bxmaker_ipgeo_epilog_city_change_box">
		<div class="bg_box"></div>
		<div class="main_box">
			<div class="btn_close">&times;</div>
			<div class="header">
				<?= $oManager->getParam('CITY_CHANGE_BOX_NAME', Loc::getMessage($TPL_ID . 'CITY_CHANGE_BOX_NAME')); ?>
			</div>


			<div class="city_search_box">

				<input type="text" name="city" value=""
					   placeholder="<?= $oManager->getParam('CITY_CHANGE_BOX_INPUT_PLACEHOLDER', Loc::getMessage($TPL_ID . 'CITY_CHANGE_BOX_INPUT_PLACEHOLDER')); ?>"
					   autocomplete="off">

				<span class="btn_clear_input">&times;</span>

				<div class="search_options_box">

				</div>
			</div>


			<div class="set_options">
				<?
					$iColRows = ceil(count($arResult['ITEMS']) / 3);
				?>

				<div class="co4">
					<?
						$i = -1;
						foreach ($arResult['ITEMS'] as $item) {
							if (++$i > 0 && $i % $iColRows == 0) {
								echo '</div><div class="co4 ">';
							}
							echo '<div class="item"
							data-location="' . $item['location'] . '"
							data-city="' . $item['city'] . '"
							data-region="' . $item['region'] . '"
							data-district="' . $item['district'] . '"
							data-district="' . $item['district'] . '"
							data-country="' . $item['country'] . '"
							data-yandex="' . $item['yandex'] . '"
							><span>' . $item['city'] . '</span></div>';
						}
					?>
				</div>
				<div class="clearfix"></div>

			</div>

		</div>
	</div>

</div>

<script type="text/javascript">
	BX.message({
		"BXMAKER_GEOIP_PARAMS_MODULE_SALE": "<?=(IsModuleInstalled('sale')  ? 'Y' : 'N');?>",
		"BXMAKER_GEOIP_PARAMS_DEBUG": "<?=$oManager->getParam('DEBUG', 'N');?>",
		"BXMAKER_GEOIP_PARAMS_YANDEX_GEOLOCATION_ON": "<?=$oManager->getParam('YANDEX_GEOLOCATION_ON', 'Y');?>",
		"BXMAKER_GEOIP_PARAMS_CITY_CHANGE_PAGE_RELOAD": "<?=$oManager->getParam('CITY_CHANGE_PAGE_RELOAD', 'N');?>",
		"BXMAKER_GEOIP_PARAMS_CITY_QUESTION_ON": "<?=$oManager->getParam('CITY_QUESTION_ON', 'Y');?>",
		"BXMAKER_GEOIP_PARAMS_DEFAULT_CITY": "<?=$oManager->getParam('DEFAULT_CITY', Loc::getMessage($TPL_ID. 'DEFAULT_CITY'));?>",
		"BXMAKER_GEOIP_PARAMS_DELIVERY_INFO_ON": "<?=$oManager->getParam('DELIVERY_INFO_ON', 'Y');?>",
		"BXMAKER_GEOIP_PARAMS_DELIVERY_PAGE": "<?=$oManager->getParam('DELIVERY_PAGE', '/delivery/');?>",
		"BXMAKER_GEOIP_PARAMS_SKIP_CITY_WORDS": "<?=$oManager->getParam('SKIP_CITY_WORDS',Loc::getMessage($TPL_ID. 'SKIP_CITY_WORDS'));?>",
		"BXMAKER_GEOIP_PARAMS_COUNTRY_LIST": "<?=$oManager->getParam('COUNTRY_LIST',Loc::getMessage($TPL_ID. 'COUNTRY_LIST'));?>",
		"BXMAKER_GEOIP_PARAMS_EMPTY_RESULT": "<?=Loc::getMessage($TPL_ID. 'EMPTY_RESULT');?>",

	});
</script>