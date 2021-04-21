<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	$COMPONENT_NAME = 'BXMAKER.GEOIP.MESSAGE';

	$randString = $this->randString();


	if ($arParams['AJAX'] != 'Y') { ?>

        <div class="bxmaker__geoip__message bxmaker__geoip__message--default js-bxmaker__geoip__message preloader"
             id="bxmaker__geoip__message-id<?= $randString; ?>"
             data-timeoffset="<?= date('Z'); ?>"
             data-template="<?= $templateName; ?>"
             data-type="<?= $arParams['TYPE']; ?>"
             data-debug="<?= $arResult['DEBUG']; ?>"
             data-subdomain-on="<?= $arParams['SUBDOMAIN_ON']; ?>"
             data-base-domain="<?= $arParams['BASE_DOMAIN']; ?>"
             data-cookie-prefix="<?= $arParams['COOKIE_PREFIX']; ?>"
             data-key="<?= $randString; ?>">

			<? $frame = $this->createFrame('bxmaker__geoip__message-id' . $randString, false)->begin(); ?>

			<? if ($arResult['CURRENT']['TIME'] == 'Y'): ?>
                <div class="js-bxmaker__geoip__message-value-default">
					<?= $arResult['DEFAULT']['MESSAGE']; ?>
                </div>
                <div class="js-bxmaker__geoip__message-value-current">
					<?= $arResult['CURRENT']['MESSAGE']; ?>
                </div>
			<?endif; ?>

            <div class="bxmaker__geoip__message-value js-bxmaker__geoip__message-value v1"
                 data-location="<?= $arParams['LOCATION']; ?>"
                 data-city="<?= $arParams['CITY']; ?>"
                 data-time="<?= $arResult['CURRENT']['TIME']; ?>"
                 data-timestart="<?= $arResult['CURRENT']['START']; ?>"
                 data-timestop="<?= $arResult['CURRENT']['STOP']; ?>">
				<?= $arResult['CURRENT']['MESSAGE']; ?>
            </div>


			<? $frame->beginStub(); ?>

            <div class="bxmaker__geoip__message-value  js-bxmaker__geoip__message-value v2"
                 data-location="<?= $arParams['LOCATION']; ?>"
                 data-city="<?= $arParams['CITY']; ?>"
                 data-time="N">
				<?= $arResult['DEFAULT']['MESSAGE']; ?>
            </div>

			<? $frame->end(); ?>
        </div>
		<?
	}
	else {
		?>
		<? if ($arResult['CURRENT']['TIME'] == 'Y'): ?>
            <div class="js-bxmaker__geoip__message-value-default">
				<?= $arResult['DEFAULT']['MESSAGE']; ?>
            </div>
            <div class="js-bxmaker__geoip__message-value-current">
				<?= $arResult['CURRENT']['MESSAGE']; ?>
            </div>
		<?endif; ?>

        <div class="bxmaker__geoip__message-value  js-bxmaker__geoip__message-value v3"
             data-location="<?= $arParams['LOCATION']; ?>"
             data-city="<?= $arParams['CITY']; ?>"
             data-time="<?= $arResult['CURRENT']['TIME']; ?>"
             data-timestart="<?= $arResult['CURRENT']['START']; ?>"
             data-timestop="<?= $arResult['CURRENT']['STOP']; ?>">
			<?= $arResult['CURRENT']['MESSAGE']; ?>
        </div>
		<?
	}
