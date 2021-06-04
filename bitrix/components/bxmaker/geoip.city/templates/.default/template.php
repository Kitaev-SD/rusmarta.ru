<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	$this->setFrameMode(true);

	$randString = $this->randString();

	$COMPONENT_NAME = 'BXMAKER.GEOIP.CITY';

	$oManager = \Bxmaker\GeoIP\Manager::getInstance();


?>

<div class="bxmaker__geoip__city bxmaker__geoip__city--default js-bxmaker__geoip__city" id="bxmaker__geoip__city-id<?= $randString; ?>"
	 data-debug="<?= $arResult['DEBUG']; ?>"
	 data-subdomain-on="<?= $arParams['SUBDOMAIN_ON']; ?>"
	 data-base-domain="<?= $arParams['BASE_DOMAIN']; ?>"
	 data-sub-domain="<?= $arParams['SUB_DOMAIN']; ?>"
	 data-cookie-prefix="<?= $arParams['COOKIE_PREFIX']; ?>"
	 data-reload="<?= $arParams['RELOAD_PAGE']; ?>"
	 data-search-show="<?= $arParams['SEARCH_SHOW']; ?>"
	 data-favorite-show="<?= $arParams['FAVORITE_SHOW']; ?>"
	 data-use-yandex="<?= $arResult['USE_YANDEX']; ?>"
	 data-use-yandex-search="<?= $arResult['USE_YANDEX_SEARCH']; ?>"
	 data-yandex-search-skip-words="<?= $oManager->getPreparedForHtmlAttr($arResult['YANDEX_SEARCH_SKIP_WORDS']); ?>"
	 data-msg-empty-result="<?= $oManager->getPreparedForHtmlAttr($arParams['MSG_EMPTY_RESULT']); ?>"
	 data-key="<?= $randString; ?>">


	<? if ($arParams['CITY_SHOW'] == 'Y'): ?>
		<? $APPLICATION->IncludeComponent(
			"bxmaker:geoip.city.line",
			".default",
			array(
				"COMPONENT_TEMPLATE"   => ".default",
				"CACHE_TYPE"           => $arParams['CACHE_TYPE'],
				"CACHE_TIME"           => $arParams['CACHE_TIME'],
				"COMPOSITE_FRAME_MODE" => $arParams['COMPOSITE_FRAME_MODE'],
				"COMPOSITE_FRAME_TYPE" => $arParams['COMPOSITE_FRAME_TYPE'],
				"CITY_LABEL"           => $arParams['CITY_LABEL'],
				"QUESTION_SHOW"        => $arParams['QUESTION_SHOW'],
				"QUESTION_TEXT"        => $arParams['QUESTION_TEXT'],
				"INFO_SHOW"            => $arParams['INFO_SHOW'],
				"INFO_TEXT"            => $arParams['INFO_TEXT'],
				"BTN_EDIT"             => $arParams['BTN_EDIT'],
			),
			$component,
			array('HIDE_ICON' => 'Y')); ?>
	<? endif; ?>


	<div class="popup-window popup-window-with-titlebar pop-up city-change bxmaker__geoip__popup js-bxmaker__geoip__popup" id="bxmaker__geoip__popup-id<?= $randString; ?>" style="z-index: 11000; position: absolute; display: none;">
		<div class="bxmaker__geoip__popup-background js-bxmaker__geoip__popup-background"></div>
		<div class="bxmaker__geoip__popup-content js-bxmaker__geoip__popup-content">
			<div class="popup-window-titlebar" id="popup-window-titlebar-cityChange">
				<span>Ваш регион доставки</span>
			</div>
			<div id="popup-window-content-cityChange" class="popup-window-content">
				<div id="sls-45360" class="bx-sls">
					<div class="dropdown-block bx-ui-sls-input-block">
						<i class="fa fa-search dropdown-icon"></i>
						<input type="text" autocomplete="off" name="LOCATION" value="" class="dropdown-field" placeholder="  " style="display: none;">
						<div class="bx-ui-sls-container" style="margin: 0px; padding: 0px; border: none; position: relative;">
							<input class="bx-ui-sls-fake" type="text" name="city" value="" placeholder="Найдите свой город" autocomplete="off">
							<span class="bxmaker__geoip__popup-search-clean js-bxmaker__geoip__popup-search-clean"><i class="fa fa-times-circle bx-ui-sls-clear" title="Отменить выбор" aria-hidden="true" style="display: block;"></i></span>
						</div>
						<div class="bxmaker__geoip__popup-search-options js-bxmaker__geoip__popup-search-options"></div>
						<div class="dropdown-fade2white"></div>
						<i class="fa fa-spinner fa-pulse bx-ui-sls-loader"></i>
						<i class="fa fa-times-circle bx-ui-sls-clear" title=" " aria-hidden="true" style="display: none;"></i>
						<div class="bx-ui-sls-pane" style="overflow: hidden auto;"><div class="bx-ui-sls-variants"></div></div>
					</div>
					<div class="submit">
						<button class="btn_buy popdef" id="selectCity" name="select-city">Выбрать город</button>
						<script>
							selectCity.onclick = function() {
								document.cookie = "contacts=1; path=/";
								window.location.reload();
							}
						</script>
					</div>
				</div>
				<div class="block-info">
					<div class="block-info__title">Не нашли свой город?</div>
					<div class="block-info__text">Осуществляем доставку по всей России. Срок доставки от 2-3 дней в зависимости от вашего региона.</div>
				</div>
			</div>
			<span class="js-bxmaker__geoip__popup-close popup-window-close-icon popup-window-titlebar-close-icon" style="right: -10px; top: -10px;"><i class="fa fa-times"></i></span>
		</div>
	</div>

	<?/*
	<div class="bxmaker__geoip__popup js-bxmaker__geoip__popup" id="bxmaker__geoip__popup-id<?= $randString; ?>">
		<div class="bxmaker__geoip__popup-background js-bxmaker__geoip__popup-background"></div>

		<div class="bxmaker__geoip__popup-content js-bxmaker__geoip__popup-content">
			<div class="bxmaker__geoip__popup-close js-bxmaker__geoip__popup-close">&times;</div>
			<div class="bxmaker__geoip__popup-header">
				<?= $arParams['POPUP_LABEL']; ?>
			</div>

			<div class="bxmaker__geoip__popup-search">
				<input type="text" name="city" value="" placeholder="<?= $arParams['INPUT_LABEL']; ?>" autocomplete="off">
				<span class="bxmaker__geoip__popup-search-clean js-bxmaker__geoip__popup-search-clean">&times;</span>
				<div class="bxmaker__geoip__popup-search-options js-bxmaker__geoip__popup-search-options"></div>
			</div>


			<div class="bxmaker__geoip__popup-options">
				<?
					$iColRows = ceil(count($arResult['ITEMS']) / 3);
				?>
				<div class="bxmaker__geoip__popup-options-col">
					<?
						$i = -1;
						foreach ($arResult['ITEMS'] as $item) {

							if (++$i > 0 && $i % $iColRows == 0) {
								echo '</div><div class="bxmaker__geoip__popup-options-col ">';
							}

							echo '<div class="bxmaker__geoip__popup-option ' . ($item['MARK'] ? 'bxmaker__geoip__popup-option--bold' : '') . ' js-bxmaker__geoip__popup-option  "	data-id="' . $item['ID'] . '"><span>' . $item['NAME'] . '</span></div>';
						}
					?>
				</div>
			</div>
		</div>
	</div>
	*/?>


</div>