<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

	use Bitrix\Main\Localization\Loc as Loc;

	$COMPONENT_NAME = 'BXMAKER.GEOIP.DELIVERY';

	$randString = $this->randString();

	$oManager = \BXmaker\GeoIP\Manager::getInstance();

?>
<? if ($arParams['AJAX'] != 'Y'): ?>
    <div class="bxmaker__geoip__delivery bxmaker__geoip__delivery--default js-bxmaker__geoip__delivery preloader"
         id="bxmaker__geoip__delivery-id<?= $randString; ?>"
         data-template="<?= $templateName; ?>"
         data-debug="<?= $arResult['DEBUG']; ?>"
         data-subdomain-on="<?= $arParams['SUBDOMAIN_ON']; ?>"
         data-base-domain="<?= $arParams['BASE_DOMAIN']; ?>"
         data-cookie-prefix="<?= $arParams['COOKIE_PREFIX']; ?>"
         data-product-id="<?= $arParams['PRODUCT_ID']; ?>"
         data-img-show="<?= $arParams['IMG_SHOW']; ?>"
         data-img-width="<?= $arParams['IMG_WIDTH']; ?>"
         data-img-heigth="<?= $arParams['IMG_HEIGHT']; ?>"
         data-show-parent="<?= $arParams['SHOW_PARENT']; ?>"
         data-key="<?= $randString; ?>">


		<? $frame = $this->createFrame('bxmaker__geoip__delivery-id' . $randString, false)->begin(); ?>

		<? if (strlen(trim($arParams['PROLOG'])) > 0): ?>
            <div class="bxmaker__geoip__delivery-prolog">
				<?= preg_replace('/#CITY#/', '<span class="bxmaker__geoip__delivery-city js-bxmaker__geoip__delivery-city">' . $arResult['DEFAULT_CITY'] . '</span>', trim($arParams['PROLOG'])); ?>
            </div>
		<? endif; ?>

        <div class="bxmaker__geoip__delivery-preloader bxmaker__geoip__delivery-preloader--hide"></div>

        <table class="bxmaker__geoip__delivery-box js-bxmaker__geoip__delivery-box" data-city="<?=$arParams['CITY'];?>" data-location="<?=$arParams['LOCATION'];?>">
			<? if (count($arResult['ITEMS'])): ?>
				<? foreach ($arResult['ITEMS'] as $delivery): ?>

                        <tr class="bxmaker__geoip__delivery-box-item  bxmaker__geoip__delivery-box-item--<?= $delivery['ID'] ?>">
							<?php
								$img = false;
								if (intval($delivery['LOGOTIP']) && $arParams['IMG_SHOW'] == 'Y') {
									$img = CFile::ResizeImageGet($delivery['LOGOTIP'], array('width' => $arParams['IMG_WIDTH'], 'height' => $arParams['IMG_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
								}
							?>
                            <td class="bxmaker__geoip__delivery-box-item-name <?= ($img ? ' bxmaker__geoip__delivery-box-item-name--image ' : ''); ?>">

								<? if ($img): ?>
                                        <img class="bxmaker__geoip__delivery-box-item-name-img" src="<?= $img['src']; ?>" alt="<?= $delivery['NAME']; ?>"/>
								<? endif; ?>

                                <div class="bxmaker__geoip__delivery-box-item-name-text">
                                    <?if($arParams['SHOW_PARENT'] == 'Y' && !!$delivery['PARENT_NAME'])
                                    {
                                        echo $delivery['PARENT_NAME'] . ' <span>(' .  $delivery['NAME'] . ') </span>';
                                    }
                                    else {
										echo $delivery['NAME'];
                                    }
                                    ?>
                                </div>


								<? if (!!$delivery['DESCRIPTION']): ?>
                                    <div class="bxmaker__geoip__delivery-box-item-more">
                                        <div class="bxmaker__geoip__delivery-box-item-more-content"><?= $delivery['DESCRIPTION'] ?></div>
                                    </div>
								<? endif; ?>
                            </td>
                            <td class="bxmaker__geoip__delivery-box-item-period">
								<?=(!!trim($delivery['PERIOD_TEXT']) ? $delivery['PERIOD_TEXT'] : '-');?>
                            </td>
                            <td class="bxmaker__geoip__delivery-box-item-price">
								<?=$delivery['PRICE_FORMATED'] ?>
                            </td>
                        </tr>


				<? endforeach; ?>
			<? else: ?>
                <tr class="bxmaker__geoip__delivery-box-item bxmaker__geoip__delivery-box-item--empty">
                    <td><?= GetMessage($COMPONENT_NAME . 'EMPTY') ?></td>
                </tr>
			<? endif; ?>
        </table>


		<? if (strlen(trim($arParams['EPILOG'])) > 0): ?>
            <div class="bxmaker__geoip__delivery-epilog">
				<?= preg_replace('/#CITY#/', '<span class="bxmaker__geoip__delivery-city js-bxmaker__geoip__delivery-city">' . $arResult['DEFAULT_CITY'] . '</span>', trim($arParams['~EPILOG'])); ?>
            </div>
		<? endif; ?>


		<? $frame->beginStub(); ?>

		<? if (strlen(trim($arParams['PROLOG'])) > 0): ?>
            <div class="bxmaker__geoip__delivery-prolog">
				<?= preg_replace('/#CITY#/', '<span class="bxmaker__geoip__delivery-city js-bxmaker__geoip__delivery-city">' . $arResult['DEFAULT_CITY'] . '</span>', trim($arParams['PROLOG'])); ?>
            </div>
		<? endif; ?>

        <div class="bxmaker__geoip__delivery-preloader bxmaker__geoip__delivery-preloader--hide"></div>
        <table class="bxmaker__geoip__delivery-box js-bxmaker__geoip__delivery-box" data-city="" data-location=""></table>

		<? if (strlen(trim($arParams['EPILOG'])) > 0): ?>
            <div class="bxmaker__geoip__delivery-epilog">
				<?= preg_replace('/#CITY#/', '<span class="bxmaker__geoip__delivery-city js-bxmaker__geoip__delivery-city">' . $arResult['DEFAULT_CITY'] . '</span>', trim($arParams['~EPILOG'])); ?>
            </div>
		<? endif; ?>


        <? $frame->end(); ?>
    </div>
<? else: ?>


    <table class="bxmaker__geoip__delivery-box js-bxmaker__geoip__delivery-box" data-city="<?=$arParams['CITY'];?>" data-location="<?=$arParams['LOCATION'];?>"  >
		<? if (count($arResult['ITEMS'])): ?>
			<? foreach ($arResult['ITEMS'] as $delivery): ?>

                <tr class="bxmaker__geoip__delivery-box-item  bxmaker__geoip__delivery-box-item--<?= $delivery['ID'] ?>">
					<?php
						$img = false;
						if (intval($delivery['LOGOTIP']) && $arParams['IMG_SHOW'] == 'Y') {
							$img = CFile::ResizeImageGet($delivery['LOGOTIP'], array('width' => $arParams['IMG_WIDTH'], 'height' => $arParams['IMG_HEIGHT']), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
						}
					?>
                    <td class="bxmaker__geoip__delivery-box-item-name <?= ($img ? ' bxmaker__geoip__delivery-box-item-name--image ' : ''); ?>">

						<? if ($img): ?>
                            <img class="bxmaker__geoip__delivery-box-item-name-img" src="<?= $img['src']; ?>" alt="<?= $delivery['NAME']; ?>"/>
						<? endif; ?>

                        <div class="bxmaker__geoip__delivery-box-item-name-text">
							<?if($arParams['SHOW_PARENT'] == 'Y' && !!$delivery['PARENT_NAME'])
							{
								echo $delivery['PARENT_NAME'] . ' <span>(' .  $delivery['NAME'] . ') </span>';
							}
							else {
								echo $delivery['NAME'];
							}
							?>
                        </div>


						<? if (!!$delivery['DESCRIPTION']): ?>
                            <div class="bxmaker__geoip__delivery-box-item-more">
                                <div class="bxmaker__geoip__delivery-box-item-more-content"><?= $delivery['DESCRIPTION'] ?></div>
                            </div>
						<? endif; ?>
                    </td>
                    <td class="bxmaker__geoip__delivery-box-item-period">
						<?=$oManager->getDeliveryPeriodFormat($delivery['PERIOD_FROM'], $delivery['PERIOD_TO']); ?>
                    </td>
                    <td class="bxmaker__geoip__delivery-box-item-price">
						<?=$delivery['PRICE_FORMATED'] ?>
                    </td>
                </tr>


			<? endforeach; ?>
		<? else: ?>
            <tr class="bxmaker__geoip__delivery-box-item bxmaker__geoip__delivery-box-item--empty">
                <td><?= GetMessage($COMPONENT_NAME . 'EMPTY') ?></td>
            </tr>
		<? endif; ?>
    </table>

<? endif; ?>



