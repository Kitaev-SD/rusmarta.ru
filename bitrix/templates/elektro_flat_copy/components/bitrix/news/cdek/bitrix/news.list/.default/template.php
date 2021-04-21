<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

use \Bitrix\Main\Localization\Loc;

$yandex_lat = false;
$yandex_lon = false;
$PLACEMARKS = array();
$count = 0;

?>
<div id="cdek">
    <? if (!empty($arResult["ITEMS"])): ?>
        <div class="contactList">
            <div>
                <? foreach ($arResult["ITEMS"] as $k => $arItem):
					//print_r($arItem);
                    $coords = $arItem['PROPERTIES']['COORDS']['VALUE'];
                    if (!empty($coords)) {
                        $coords = explode(',', $coords, 2);
                        $lat = $coords[0];
                        $log = $coords[1];
                        $yandex_lat += $lat;
                        $yandex_lon += $log;
                        $text = $arItem['PROPERTIES']['ADDRESS']['VALUE'];
                        $PLACEMARKS[] = array(
                            'LON' => $log,
                            'LAT' => $lat,
                            'TEXT' => $text,
                        );
                        $count++;
                    }

                    $address = $arItem['PROPERTIES']['ADDRESS']['VALUE'];
                    $phone = $arItem['PROPERTIES']['PHONE']['VALUE'];
                    $time = nl2br($arItem['PROPERTIES']['OPERATION_TIME']['VALUE']['TEXT']);
                    $weight = $arItem['PROPERTIES']['WEIGHT']['VALUE'];
                    if ($k == 0) {
                        ?>
                        <? if (!empty($address)): ?>
                            <p><strong><?= Loc::getMessage("NEWS_DELIVERY_ADDRESS") ?></strong><?= $address ?></p>
                        <? endif; ?>
                        <? if (!empty($phone)): ?>
                            <p><strong><?= Loc::getMessage("NEWS_DELIVERY_PHONE") ?></strong><?= $phone?></p>
                        <? endif; ?>
                        <? if (!empty($time)): ?>
                            <p><strong><?= Loc::getMessage("NEWS_DELIVERY_OPERATION_TIME") ?></strong><br /><?= $time ?></p>
                        <? endif; ?>
                        <? if (!empty($weight)): ?>
                            <p><strong><?= Loc::getMessage("NEWS_DELIVERY_WEIGHT") ?></strong><?= $weight ?></p>
                        <? endif; ?>
                        <?
                        if (count($arResult["ITEMS"]) > 1) {
                            ?><h2><?= Loc::getMessage("NEWS_DELIVERY_MORE_PVZ") ?></h2><?
                        }
                    } else {
                        ?>
                        <div class="contact hidetoggler">
                            <a href="#"><?= $address ?></a>
                            <div style="display: none;">
                                <? if (!empty($address)): ?>
                                    <p><strong><?= Loc::getMessage("NEWS_DELIVERY_ADDRESS") ?></strong><?= $address ?></p>
                                <? endif; ?>
                                <? if (!empty($phone)): ?>
                                    <p><strong><?= Loc::getMessage("NEWS_DELIVERY_PHONE") ?></strong><?= $phone?></p>
                                <? endif; ?>
                                <? if (!empty($time)): ?>
                                    <p><strong><?= Loc::getMessage("NEWS_DELIVERY_OPERATION_TIME") ?></strong><br /><?= $time ?></p>
                                <? endif; ?>
                                <? if (!empty($weight)): ?>
                                    <p><strong><?= Loc::getMessage("NEWS_DELIVERY_WEIGHT") ?></strong><?= $weight ?></p>
                                <? endif; ?>
                            </div>
                        </div>
                        <?
                    }
                    ?>
                <? endforeach; ?>
            </div>
        </div>
        <div class="contactMap">
            <?
            if ($count > 0) {
                $yandex_lat = $yandex_lat / $count;
                $yandex_lon = $yandex_lon / $count;
            }
            $map_data = array(
                'yandex_lat' => $yandex_lat,
                'yandex_lon' => $yandex_lon,
                'yandex_scale' => 11,
                'PLACEMARKS' => $PLACEMARKS,
            );
			
            if ($count > 0):
                $APPLICATION->IncludeComponent(
                    "bitrix:map.yandex.view",
                    "",
                    Array(
                        "COMPOSITE_FRAME_MODE" => "A",
                        "COMPOSITE_FRAME_TYPE" => "AUTO",
                        "CONTROLS" => array("ZOOM", "MINIMAP", "SCALELINE"),
                        "INIT_MAP_TYPE" => "MAP",
                        "MAP_DATA" => serialize($map_data),
                        "MAP_HEIGHT" => "500",
                        "MAP_ID" => "",
                        "MAP_WIDTH" => "500",
                        "OPTIONS" => array("ENABLE_DBLCLICK_ZOOM", "ENABLE_DRAGGING")
                    )
                );
            endif; ?>
        </div>
    <? else: ?>
        <?= Loc::getMessage("NEWS_DELIVERY_RUSSIA_POST_EMC") ?>
    <? endif; ?>
</div>

