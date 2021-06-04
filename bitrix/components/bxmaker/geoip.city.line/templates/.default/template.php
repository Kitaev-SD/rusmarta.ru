<?    if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;


	$this->setFrameMode(true);

	$randString = $this->randString();
	$BXMAKER_COMPONENT_NAME = 'BXMAKER.GEOIP.CITY.LINE';

	$oManager = \Bxmaker\GeoIP\Manager::getInstance();

?>


<div class="bxmaker__geoip__city__line  bxmaker__geoip__city__line--default js-bxmaker__geoip__city__line"
	 id="bxmaker__geoip__city__line-id<?=$randString;?>"
	 data-question-show="<?=$arParams['QUESTION_SHOW'];?>"
	 data-info-show="<?=$arParams['INFO_SHOW'];?>"
	 data-debug="<?=$arResult['DEBUG'];?>"
     data-subdomain-on="<?= $arParams['SUBDOMAIN_ON']; ?>"
     data-base-domain="<?= $arParams['BASE_DOMAIN']; ?>"
     data-cookie-prefix="<?= $arParams['COOKIE_PREFIX']; ?>"
	 data-fade-timeout="200"
	 data-tooltip-timeout="500"
	 data-key="<?=$randString;?>" >

	<?/*
	<span class="bxmaker__geoip__city__line-label"><?=$arParams['~CITY_LABEL'];?></span>
	*/?>

	<div class="bxmaker__geoip__city__line-context js-bxmaker__geoip__city__line-context geolocation__link">
		<i class="fa fa-map-marker" aria-hidden="true"></i>
		<span class="bxmaker__geoip__city__line-name js-bxmaker__geoip__city__line-name js-bxmaker__geoip__city__line-city"><?=$arResult['CITY_DEFAULT']?></span>


		<div id="cityConfirm" class="js-bxmaker__geoip__city__line-question popup-window pop-up city-confirm" style="z-index: 1100; position: absolute;display:none;">
			<div id="popup-window-content-cityConfirm" class="popup-window-content">
				<div class="your-city">
					<div class="your-city__label">Ваш город</div>
					<div class="your-city__val js-city"><?=$arResult['CITY_DEFAULT']?>?</div>
				</div>
			</div>
			<span class="popup-window-close-icon js-bxmaker__geoip__city__line-question-btn-yes" style="right: -10px; top: -10px;">
				<i class="fa fa-times"></i>
			</span>
			<div class="popup-window-buttons">
				<button name="cityConfirmYes" class="btn_buy popdef js-bxmaker__geoip__city__line-question-btn-yes">ДА</button>
				<button name="cityConfirmChange" class="btn_buy apuo js-bxmaker__geoip__city__line-question-btn-no">Выбрать другой город</button>
			</div>
		</div>
		<?/*
		<div class="bxmaker__geoip__city__line-question js-bxmaker__geoip__city__line-question">
			<div class="bxmaker__geoip__city__line-question-text">
				<?= preg_replace('/#CITY#/','<span class="js-bxmaker__geoip__city__line-city">'.$arResult['CITY_DEFAULT'].'</span>', $arParams['~QUESTION_TEXT']);?>
			</div>
			<div class="bxmaker__geoip__city__line-question-btn-box">
				<div class="bxmaker__geoip__city__line-question-btn-no js-bxmaker__geoip__city__line-question-btn-no"><?= Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_NO'); ?></div>
				<div class="bxmaker__geoip__city__line-question-btn-yes js-bxmaker__geoip__city__line-question-btn-yes"><?= Loc::getMessage($BXMAKER_COMPONENT_NAME . 'BTN_YES'); ?></div>
			</div>
		</div>
		*/?>

		<?/*
		<div class="bxmaker__geoip__city__line-info js-bxmaker__geoip__city__line-info">
			<div class="bxmaker__geoip__city__line-info-content">
				<?=$arParams['~INFO_TEXT'];?>
			</div>
			<div class="bxmaker__geoip__city__line-info-btn-box">
				<div class="bxmaker__geoip__city__line-info-btn js-bxmaker__geoip__city__line-info-btn"><?=$arParams['~BTN_EDIT'];?></div>
			</div>
		</div>
		*/?>

	</div>
	<div id="telephone-html" class="telephone"></div>
</div>
