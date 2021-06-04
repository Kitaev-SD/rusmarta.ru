<?
define("CDEK_IBLOCK_ID", 27);
// Функция дебага
function pr($var, $die = false, $all = false)
{
    global $USER;
    if (($USER->GetID() == 4) || ($all == true)) {
        $bt = debug_backtrace();
        $bt = $bt[0];
        ?>
        <div style='font-size:9pt; color:#000; background:#fff; border:1px dashed #000;'>
            <div style='padding:3px 5px; background:#99CCFF; font-weight:bold;'>File: <?= $bt["file"] ?>
                [<?= $bt["line"] ?>]
            </div>
            <?
            if ($var === 0) {
                echo '<pre>пусто</pre>';
                var_dump($var);
            } else {
                echo '<pre>';
                print_r($var);
                echo '</pre>';
            }
            ?>
        </div>
        <?
        if ($die) {
            die();
        }
    }
}
//ROISTAT start
use Bitrix\Main\Event;
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('iblock', 'OnAfterIBlockElementAdd', 'rsOnFormAdd');
function rsOnFormAdd($arFields)
{
    if($arFields['IBLOCK_ID'] == 2) {
        $formName = 'Заказать товар';
        $name     = $arFields['PROPERTY_VALUES']['NAME'];
        $phone    = $arFields['PROPERTY_VALUES']['PHONE'];
        $comment  = "Вопрос по товару '{$arFields['PROPERTY_VALUES']['PRODUCT']}'.";
        $comment .= (isset($arFields['PROPERTY_VALUES']['TIME']) && !empty($arFields['PROPERTY_VALUES']['TIME'])) ? "Время: {$arFields['PROPERTY_VALUES']['TIME']}." : 'Время: не указано';
        $comment .= (isset($arFields['PROPERTY_VALUES']['MESSAGE']['VALUE']['TEXT']) && !empty($arFields['PROPERTY_VALUES']['MESSAGE']['VALUE']['TEXT'])) ? "Комментарий: {$arFields['PROPERTY_VALUES']['MESSAGE']['VALUE']['TEXT']}." : 'Комментарий: не указан';
        // Send
        $data = array(
            'key'     => 'ODkzODM6NjcxMjQ6MThlOThkOGE0YTFlMDY3ZWRjNDFhMDgzOTlkN2IzZGU=',
            'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
            'title'   => iconv(SITE_CHARSET, 'utf-8', $formName),
            'name'    => iconv(SITE_CHARSET, 'utf-8', $name),
            'phone'   => $phone,
            'comment' => iconv(SITE_CHARSET, 'utf-8', $comment),
            'fields'  => array(
                'form' => iconv(SITE_CHARSET, 'utf-8', $formName),
                'site'    => 'rusmarta.ru',
                'google_id' => '{googleClientId}',
            )
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => "https://rusmarta.ru/roistat.php?" . http_build_query($data),
            CURLOPT_USERAGENT      => 'Roistat Request'
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
    }
}
if ($_POST['PostAction'] == 'Add' && !empty($_POST['EMAIL'])) {
    $data = array(
        'key'     => 'ODkzODM6NjcxMjQ6MThlOThkOGE0YTFlMDY3ZWRjNDFhMDgzOTlkN2IzZGU=',
        'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
        'title'   => iconv(SITE_CHARSET, 'utf-8', 'Подписаться'),
        'email'   => $_POST['EMAIL'],
        'is_skip_sending' => 1,
        'fields'  => array(
            'form' => iconv(SITE_CHARSET, 'utf-8', 'Подписаться'),
            'site'    => 'rusmarta.ru',
            'google_id' => '{googleClientId}',
        )
    );
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL            => "https://rusmarta.ru/roistat.php?" . http_build_query($data),
        CURLOPT_USERAGENT      => 'Roistat Request'
    ));
    $resp = curl_exec($curl);
    curl_close($curl);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, 'https://cloud.roistat.com/api/proxy/1.0/leads/add?' . http_build_query($data));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
	$out = curl_exec($curl);
	curl_close($curl);
}
$eventManager->addEventHandler('main', 'OnBeforeEventAdd', 'SendFormToRoistat');
function SendFormToRoistat(&$event, &$lid, &$arFields)
{
    if ($event == 'SALE_NEW_ORDER') {
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/roistat/log.log', print_r($arFields['ORDER_ID'], 1) . PHP_EOL . print_r($_COOKIE,1) . PHP_EOL, FILE_APPEND | LOCK_EX);
        $data = array(
            'key'     => 'ODkzODM6NjcxMjQ6MThlOThkOGE0YTFlMDY3ZWRjNDFhMDgzOTlkN2IzZGU=',
            'roistat' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : 'nocookie',
            'title'   => iconv(SITE_CHARSET, 'utf-8', 'Купить'),
            'name'    => isset($_POST['NAME'])  ? $_POST['NAME']                                : iconv(SITE_CHARSET, 'utf-8', $_POST['ORDER_PROP_1']),
            'phone'   => isset($_POST['PHONE']) ? iconv(SITE_CHARSET, 'utf-8', $_POST['PHONE']) : iconv(SITE_CHARSET, 'utf-8', $_POST['ORDER_PROP_3']),
            'email'   => isset($_POST['EMAIL']) ? iconv(SITE_CHARSET, 'utf-8', $_POST['EMAIL']) : iconv(SITE_CHARSET, 'utf-8', $_POST['ORDER_PROP_2']),
            'comment' => iconv(SITE_CHARSET, 'utf-8', $arFields['ORDER_LIST']),
            'is_skip_sending' => 1,
            'fields'  => array(
                'form'    => iconv(SITE_CHARSET, 'utf-8', 'Купить'),
                'order'   => $arFields['ORDER_ID'],
                'product' => iconv(SITE_CHARSET, 'utf-8', $arFields['ORDER_LIST']),
                'marker'  => isset($_COOKIE['roistat_marker']) ? $_COOKIE['roistat_marker'] : 'nomarker',
                'site'    => 'rusmarta.ru',
                'google_id' => '{googleClientId}',
            )
        );
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL            => "https://rusmarta.ru/roistat.php?" . http_build_query($data),
            CURLOPT_USERAGENT      => 'Roistat Request'
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://cloud.roistat.com/api/proxy/1.0/leads/add?' . http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
		$out = curl_exec($curl);
		curl_close($curl);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/roistat/test.log', print_r(array(
            'event' => 'OnBeforeEventAdd',
            'post' => $_POST,
            'get' => $_GET,
            'cookie' => $_COOKIE,
        ), true), FILE_APPEND);
    }
}
$eventManager->addEventHandler('sale', 'OnSaleOrderSaved', 'rsOnAddOrder'); 
function rsOnAddOrder(Event $event) {
    if(!$event->getParameter('IS_NEW')) return;

    file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/roistat/test.log', print_r(array(
        'event' => 'OnSaleOrderSaved',
        'post' => $_POST,
        'get' => $_GET,
        'cookie' => $_COOKIE,
    ), true), FILE_APPEND);
}
//ROISTAT end
AddEventHandler("main", "OnEndBufferContent", "ChangeMyContent");
function ChangeMyContent(&$content) {
	$srcs = array(
		'upload/medialibrary/e4f/e4f182757ac310b060955c54e475f4c5.jpg',
		'images/cms/data/knopki/garant-market2111.png',
		'skins/irzonline/img/r_location.png',
		'skins/irzonline/img/r_plugin.png',
		'skins/irzonline/img/r_geo.png',
		'images/cms/data/triggeri/ic2.png',
		'images/cms/data/triggeri/ic4.png',
		'skins/irzonline/img/r_mileage_count.png',
		'images/cms/data/triggeri/ic3.png',
		'images/cms/data/triggeri/ic1.png',
	);
	$srcs_preg = '('.implode('|', array_map(function ($v) { return preg_quote($v, '#'); }, $srcs)).')';
	$content = preg_replace('#\<img[^\>]*src\=["\'][^"\']*'.$srcs_preg.'["\'][^\>]*\>#', '', $content);
}
//-- Добавление обработчика события
AddEventHandler("sale", "OnOrderNewSendEmail", "bxModifySaleMails");
//-- Собственно обработчик события
function bxModifySaleMails($orderID, &$eventName, &$arFields)
{
    $arOrder = CSaleOrder::GetByID($orderID);
    //-- получаем телефоны и адрес
    $order_props = CSaleOrderPropsValue::GetOrderProps($orderID);
    $phone="";
    $index = "";
    $country_name = "";
    $city_name = "";
    $address = "";
    while ($arProps = $order_props->Fetch())
    {
        if ($arProps["CODE"] == "PHONE")
        {
            $phone = htmlspecialchars($arProps["VALUE"]);
        }
        if ($arProps["CODE"] == "LOCATION")
        {
            $arLocs = CSaleLocation::GetByID($arProps["VALUE"]);
            $country_name =  $arLocs["COUNTRY_NAME_ORIG"];
            $city_name = $arLocs["CITY_NAME_ORIG"];
        }
        if ($arProps["CODE"] == "INDEX")
        {
            $index = $arProps["VALUE"];
        }

        if ($arProps["CODE"] == "ADDRESS")
        {
            $address = $arProps["VALUE"];
        }
    }
    $full_address = $index.", ".$country_name." ".$city_name.", ".$address;
    //-- получаем название службы доставки
    $arDeliv = CSaleDelivery::GetByID($arOrder["DELIVERY_ID"]);
    $delivery_name = "";
    if ($arDeliv)
    {
        $delivery_name = $arDeliv["NAME"];
    }
    //-- генерируем срок достаки
    $delivery_period_type = "";

    switch ($arDeliv["PERIOD_TYPE"]) {
        case "D":
            $delivery_period_type = "дней";
            break;
        case "H":
            $delivery_period_type = "часов";
            break;
        case "M":
            $delivery_period_type = "месяцев";
            break;
    }
    //-- получаем название платежной системы
    $arPaySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
    $pay_system_name = "";
    if ($arPaySystem)
    {
        $pay_system_name = $arPaySystem["NAME"];
    }
    //-- добавляем новые поля в массив результатов
    $arFields["ORDER_DESCRIPTION"] = $arOrder["USER_DESCRIPTION"];
    $arFields["PHONE"] =  $phone;
    $arFields["DELIVERY_NAME"] =  $delivery_name;
    $arFields["DELIVERY_PRICE"] =  $arOrder["PRICE_DELIVERY"];
    $arFields["DELIVERY_PERIOD_TYPE"] =  $delivery_period_type;
    $arFields["DELIVERY_PERIOD_FROM"] =  $arDeliv["PERIOD_FROM"];
    $arFields["DELIVERY_PERIOD_TO"] =  $arDeliv["PERIOD_TO"];
    $arFields["FULL_ADDRESS"] = $full_address;
    $arFields["PAY_SYSTEM_NAME"] =  $pay_system_name;
}
?>