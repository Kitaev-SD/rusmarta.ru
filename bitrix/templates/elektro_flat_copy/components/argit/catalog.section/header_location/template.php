<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;

/**
 * @global CMain $APPLICATION
 * @var array $arParams
 * @var array $arResult
 * @var CatalogSectionComponent $component
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $componentPath
 */

use Bitrix\Main\Application,
    Bitrix\Main\Text\Encoding;

$request = Application::getInstance()->getContext()->getRequest();
$locationCity = $_COOKIE['bxmaker_geoip_2_3_8_s1_city'];
//$locationCity = $request->getCookie("GEOLOCATION_CITY");

if (SITE_CHARSET != "utf-8")
    $locationCity = Encoding::convertEncoding($locationCity, "utf-8", SITE_CHARSET);

$this->setFrameMode(true);
if (!empty($arResult['ITEMS'])):
    ?>    <div class="hhead">
        <span><a rel="nofollow" href="<?=$arParams['SEF_RULE']?>"><?= Loc::getMessage("HEADER_DELIVERY_PVZ_CITY") ?></a></span><?//= $locationCity ?>
    </div>
    <? foreach ($arResult['ITEMS'] as $item): ?><?
    $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
    $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BCT_ELEMENT_DELETE_CONFIRM')));

    $productTitle = isset($item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) && $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] != ''
        ? $item['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
        : $item['NAME'];

    $address = $item['PROPERTIES']['ADDRESS']['VALUE'];
//$address = explode(', ', $address, 2);
//   $address = $address[1];
    ?>
    <div class="haddr_item" id="<?= $this->GetEditAreaId($item['ID']); ?>">
        <a rel="nofollow" href="<?=$item['DETAIL_PAGE_URL']?>">
            <span class="glyphicon glyphicon-map-marker"></span><?= $address ?>
        </a>
    </div>
<? endforeach; ?>
    <div class="haddr_item more">
        <a rel="nofollow" href="<?=$arParams['SEF_RULE']?>"><span class="glyphicon glyphicon-map-marker"></span><?= Loc::getMessage("HEADER_DELIVERY_MORE") ?></a>
    </div>
    <?
else:
    ?><div><?= Loc::getMessage("HEADER_DELIVERY_RUSSIA_POST_EMC") ?></div><?
endif;