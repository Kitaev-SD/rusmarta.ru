<?
/*********************************************************************************
Обработчик расчета доставки калькулятора eDost.ru
Версия 2.5.5, 05.05.2021
Автор: ООО "Айсден"

Компании доставки и параметры расчета задаются в личном кабинете eDost.ru (требуется регистрация: http://edost.ru/reg.php)

Получение данных о доставке по номеру заказа:
--------------------------------------------------------------------------------
$order = \Bitrix\Sale\Order::load(97);
$shipment = false;
foreach ($order->getShipmentCollection() as $v) if (!$v->isSystem()) $shipment = $v;
if ($shipment !== false) {
	$delivery_id = $shipment->getDeliveryId();
	$price = $shipment->getPrice();
	$tariff = array('DELIVERY_ID' => $delivery_id, 'VALUE' => $price);
	$service = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($delivery_id);
	if ($service) {
		$tariff['NAME'] = $service->getName();
		$p = $service->calculate($shipment);
		$tariff['TRANSIT'] = ($p ? $p->getPeriodDescription() : '');
	}
	echo print_r($tariff, true);
}
--------------------------------------------------------------------------------

// загрузка данных из класса модуля для тарифа с кодом "5" EMS Почта России (перед вызовом должен быть произведен расчет доставки!!!)
--------------------------------------------------------------------------------
if (class_exists('CDeliveryEDOST')) {
	$edost_tariff = CDeliveryEDOST::GetEdostTariff(5);
	echo '<pre>'.print_r($edost_tariff, true).'</pre>';
}
--------------------------------------------------------------------------------

Получение названия тарифа доставки по ID:
--------------------------------------------------------------------------------
$services = \Bitrix\Sale\Delivery\Services\Table::getList(array('filter' => array('=ACTIVE' => 'Y', '=ID' => $DELIVERY_ID)));
while ($v = $services->fetch()) echo '<br><b>profile</b> <pre style="font-size: 12px">'.print_r($v, true).'</pre>';
--------------------------------------------------------------------------------

Получение полного адреса (улица, дом, квартира, город, регион) по номеру заказа:
--------------------------------------------------------------------------------
if (!class_exists('edost_class')) require_once($_SERVER['DOCUMENT_ROOT'].getLocalPath('modules/edost.delivery/classes/general/delivery_edost.php'));
$props = edost_class::GetProps(593, array('no_payment'));
echo 'address_full: '.$props['address_full'];
//echo print_r($props, true);
--------------------------------------------------------------------------------

Коды тарифов:
--------------------------------------------------------------------------------
Код битрикса = код edost * 2 - 1
и +1, если нужен тариф со страховкой

коды edost: http://www.edost.ru/kln/help.html#DeliveryCode

Пример:
edost:1 - Почта России (отправление 1-го класса)
edost:2 - Почта России (отправление 1-го класса) со страховкой
edost:3 - Почта России (наземная посылка)
edost:4 - Почта России (наземная посылка) со страховкой
edost:5 - EMS Почта России
--------------------------------------------------------------------------------


*********************************************************************************/


CModule::IncludeModule('sale');

IncludeModuleLangFile(__FILE__);

include_once 'edost_const.php';
if (defined('DELIVERY_EDOST_FUNCTION') && DELIVERY_EDOST_FUNCTION == 'Y') include_once 'edost_function.php';

define('DELIVERY_EDOST_TARIFF_COUNT', 83); // количество тарифов доставки доступных в модуле (для контроля версий - не менять!)
define('DELIVERY_EDOST_SERVER', 'api.edost.ru'); // сервер расчета доставки
define('DELIVERY_EDOST_SERVER_ZIP', 'edostzip.ru'); // справочный сервер
define('DELIVERY_EDOST_SERVER_RESERVE', 'xn--d1ab2amf.xn--p1ai'); // дополнительный сервер (едост.рф)
define('DELIVERY_EDOST_SERVER_RESERVE2', 'edost.net'); // дополнительный сервер

class CDeliveryEDOST {
	public static $result = null;
	public static $result_full = array();
	public static $automatic = false;
	public static $config = false;
	public static $print_field = null;
	public static $admin_order = false;

	public static $setting_key = array(
		'id' => '', 'ps' => '', 'host' => '', 'hide_error' => 'N', 'show_zero_tariff' => 'N',
		'map' => 'Y', 'cod_status' => '', 'send_zip' => 'Y', 'hide_payment' => 'Y', 'sort_ascending' => 'N',
		'template' => 'N3', 'template_format' => 'odt', 'template_block' => 'off', 'template_block_type' => 'none', 'template_cod' => 'td', 'template_autoselect_office' => 'N', 'autoselect' => 'Y',
		'admin' => 'Y', 'template_map_inside' => 'N',
		'control' => 'Y', 'control_auto' => 'Y', 'control_status_arrived' => '', 'control_status_completed' => 'F', 'control_status_completed_cod' => 'F',
		'browser' => 'ie', 'register_status' => '',
		'sale_discount' => 'N', 'sale_discount_cod' => 'off',
		'edost_discount' => 'Y', 'template_ico' => 'C', 'template_script' => 'Y',
		'package' => 1, 'postmap' => 'N',
		'office_near' => 'N', 'office_unsupported' => 'N', 'office_unsupported_fix' => '', 'office_unsupported_percent' => '', 'office_tel' => 'Y',
	);
	public static $setting_param_key = array('zero_tariff' => 0, 'module_id' => 0);

	public static $tariff_shop = array(35,56,57,58, 31,32,33,34);
	public static $zip_required = array(1,2,3,61,62,68,69,70,71,72,73,74,77,79,43); // почта, EMS + курьер boxberry (43)
	public static $passport_required = array(14,16,15,22,39,40,41,42,48,49,50,51,52,53,54,55,59,60,63,64); // ТК
	public static $post = array(1,2,61,68,69,70,71,72,73,74,79); // доставка в почтовые отделения (для контроля)
	public static $office = array(9,14,16,22,29,36,37,39,40,41,42,46,63,65,75,78,80,81,82); // доставка в пункты выдачи (для контроля)
	public static $register_tariff = array(23 => array(1,2,3,61,62,77,78,79), 5 => array(37,75,9,7,65, 38,76,6,8,10,17,66), 30 => array(36,43), 19 => array(16,49)); // тарифы для которых возможно оформление доставки: почта, СДЭК, boxberry
	public static $register_no_required = array(1,2); // для печати бланков требуется оформление доставки (для оформления)
	public static $office_key = array('shop', 'office', 'terminal', 'postmap');
	public static $company_shop = array('s1', 's2', 's3', 's4');
	public static $postamat = array(5, 6, 10, 11, 12, 14, 15); // пункты выдачи с типом "постамат"

	public static $country_code = array(0 => "Россия", 1 => "Австралия", 2 => "Австрия", 3 => "Азербайджан", 4 => "Албания", 5 => "Алжир", 6 => "Американское Самоа", 7 => "Ангилья", 8 => "Англия", 9 => "Ангола", 10 => "Андорра", 11 => "Антигуа и Барбуда", 12 => "Антильские острова", 13 => "Аргентина", 14 => "Армения", 15 => "Аруба", 16 => "Афганистан", 17 => "Багамские острова", 18 => "Бангладеш", 19 => "Барбадос", 20 => "Бахрейн", 21 => "Беларусь", 22 => "Белиз", 23 => "Бельгия", 24 => "Бенин", 25 => "Бермудские острова", 26 => "Болгария", 27 => "Боливия", 28 => "Бонайре", 29 => "Босния и Герцеговина", 30 => "Ботсвана", 31 => "Бразилия", 32 => "Бруней", 33 => "Буркина Фасо", 34 => "Бурунди", 35 => "Бутан", 36 => "Валлис и Футуна острова", 37 => "Вануату", 38 => "Великобритания", 39 => "Венгрия", 40 => "Венесуэла", 41 => "Виргинские острова (Британские)", 42 => "Виргинские острова (США)", 43 => "Восточный Тимор", 44 => "Вьетнам", 45 => "Габон", 46 => "Гаити", 47 => "Гайана", 48 => "Гамбия", 49 => "Гана", 50 => "Гваделупа", 51 => "Гватемала", 52 => "Гвинея", 53 => "Гвинея Экваториальная", 54 => "Гвинея-Бисау", 55 => "Германия", 56 => "Гернси (Нормандские острова)", 57 => "Гибралтар", 58 => "Гондурас", 59 => "Гонконг", 60 => "Гренада", 61 => "Гренландия", 62 => "Греция", 63 => "Грузия", 64 => "Гуам", 65 => "Дания", 66 => "Джерси (Нормандские острова)", 67 => "Джибути", 68 => "Доминика", 69 => "Доминиканская респ.", 70 => "Египет", 71 => "Замбия", 72 => "Зеленого Мыса острова (Кабо-Верде)", 73 => "Зимбабве", 74 => "Израиль", 75 => "Индия", 76 => "Индонезия", 77 => "Иордания", 78 => "Ирак", 79 => "Иран", 80 => "Ирландия", 81 => "Исландия", 82 => "Испания", 83 => "Италия", 84 => "Йемен", 85 => "Казахстан", 86 => "Каймановы острова", 87 => "Камбоджа", 88 => "Камерун", 89 => "Канада", 90 => "Канарские острова", 91 => "Катар", 92 => "Кения", 93 => "Кипр", 94 => "Кирибати", 95 => "Китайская Народная Республика", 96 => "Колумбия", 97 => "Коморские острова", 98 => "Конго", 99 => "Конго, Демократическая респ.", 100 => "Корея, Северная", 101 => "Корея, Южная", 102 => "Косово", 103 => "Коста-Рика", 104 => "Кот-д'Ивуар", 105 => "Куба", 106 => "Кувейт", 107 => "Кука острова", 108 => "Кыргызстан", 109 => "Кюрасао", 110 => "Лаос", 111 => "Латвия", 112 => "Лесото", 113 => "Либерия", 114 => "Ливан", 115 => "Ливия", 116 => "Литва", 117 => "Лихтенштейн", 118 => "Люксембург", 119 => "Маврикий", 120 => "Мавритания", 121 => "Мадагаскар", 122 => "Майотта", 123 => "Макао", 124 => "Македония", 125 => "Малави", 126 => "Малайзия", 127 => "Мали", 128 => "Мальдивские острова", 129 => "Мальта", 130 => "Марокко", 131 => "Мартиника", 132 => "Маршалловы острова", 133 => "Мексика", 134 => "Микронезия", 135 => "Мозамбик", 136 => "Молдова", 137 => "Монако", 138 => "Монголия", 139 => "Монтсеррат", 140 => "Мьянма", 141 => "Намибия", 142 => "Науру", 143 => "Невис", 144 => "Непал", 145 => "Нигер", 146 => "Нигерия", 147 => "Нидерланды (Голландия)", 148 => "Никарагуа", 149 => "Ниуэ", 150 => "Новая Зеландия", 151 => "Новая Каледония", 152 => "Норвегия", 153 => "Объединенные Арабские Эмираты", 154 => "Оман", 155 => "Пакистан", 156 => "Палау", 157 => "Панама", 158 => "Папуа-Новая Гвинея", 159 => "Парагвай", 160 => "Перу", 161 => "Польша", 162 => "Португалия", 163 => "Пуэрто-Рико", 164 => "Реюньон", 165 => "Руанда", 166 => "Румыния", 167 => "Сайпан", 168 => "Сальвадор", 169 => "Самоа", 170 => "Сан-Марино", 171 => "Сан-Томе и Принсипи", 172 => "Саудовская Аравия", 173 => "Свазиленд", 174 => "Северная Ирландия", 175 => "Сейшельские острова", 176 => "Сен-Бартельми", 177 => "Сенегал", 178 => "Сент-Винсент", 179 => "Сент-Китс", 180 => "Сент-Кристофер", 181 => "Сент-Люсия", 182 => "Сент-Маартен", 183 => "Сент-Мартин", 184 => "Сент-Юстас", 185 => "Сербия", 186 => "Сингапур", 187 => "Сирия", 188 => "Словакия", 189 => "Словения", 190 => "Соломоновы острова", 191 => "Сомали", 192 => "Сомалилэнд", 193 => "Судан", 194 => "Суринам", 195 => "США", 196 => "Сьерра-Леоне", 197 => "Таджикистан", 198 => "Таиланд", 199 => "Таити", 200 => "Тайвань", 201 => "Танзания", 202 => "Того", 203 => "Тонга", 204 => "Тринидад и Тобаго", 205 => "Тувалу", 206 => "Тунис", 207 => "Туркменистан", 208 => "Туркс и Кайкос", 209 => "Турция", 210 => "Уганда", 211 => "Узбекистан", 212 => "Украина", 213 => "Уругвай", 214 => "Уэльс", 215 => "Фарерские острова", 216 => "Фиджи", 217 => "Филиппины", 218 => "Финляндия", 219 => "Фолклендские (Мальвинские) острова", 220 => "Франция", 221 => "Французская Гвиана", 222 => "Французская Полинезия", 223 => "Хорватия", 224 => "Центральная Африканская Респ.", 225 => "Чад", 226 => "Черногория", 227 => "Чехия", 228 => "Чили", 229 => "Швейцария", 230 => "Швеция", 231 => "Шотландия", 232 => "Шри-Ланка", 233 => "Эквадор", 234 => "Эритрея", 235 => "Эстония", 236 => "Эфиопия", 237 => "ЮАР", 238 => "Ямайка", 239 => "Япония");
	public static $region_code = array(
		0 => array(22 => 'Алтайский край', 28 => 'Амурская область', 29 => 'Архангельская область', 30 => 'Астраханская область', 31 => 'Белгородская область', 32 => 'Брянская область', 33 => 'Владимирская область', 34 => 'Волгоградская область', 35 => 'Вологодская область', 36 => 'Воронежская область', 79 => 'Еврейская АО', 75 => 'Забайкальский край', 37 => 'Ивановская область', 38 => 'Иркутская область', 7 => 'Кабардино-Балкарская Республика', 39 => 'Калининградская область', 40 => 'Калужская область', 41 => 'Камчатский край', 9 => 'Карачаево-Черкесская Республика', 42 => 'Кемеровская область', 43 => 'Кировская область', 44 => 'Костромская область', 23 => 'Краснодарский край', 24 => 'Красноярский край', 45 => 'Курганская область', 46 => 'Курская область', 47 => 'Ленинградская область', 48 => 'Липецкая область', 49 => 'Магаданская область', 50 => 'Московская область', 51 => 'Мурманская область', 83 => 'Ненецкий АО', 52 => 'Нижегородская область', 53 => 'Новгородская область', 54 => 'Новосибирская область', 55 => 'Омская область', 56 => 'Оренбургская область', 57 => 'Орловская область', 58 => 'Пензенская область', 59 => 'Пермский край', 25 => 'Приморский край', 60 => 'Псковская область', 1 => 'Республика Адыгея', 4 => 'Республика Алтай', 2 => 'Республика Башкортостан', 3 => 'Республика Бурятия', 5 => 'Республика Дагестан', 6 => 'Республика Ингушетия', 8 => 'Республика Калмыкия', 10 => 'Республика Карелия', 11 => 'Республика Коми', 12 => 'Республика Марий Эл', 13 => 'Республика Мордовия', 14 => 'Республика Саха (Якутия)', 15 => 'Республика Северная Осетия - Алания', 16 => 'Республика Татарстан', 17 => 'Республика Тыва', 19 => 'Республика Хакасия', 61 => 'Ростовская область', 62 => 'Рязанская область', 63 => 'Самарская область', 64 => 'Саратовская область', 65 => 'Сахалинская область', 66 => 'Свердловская область', 67 => 'Смоленская область', 26 => 'Ставропольский край', 68 => 'Тамбовская область', 69 => 'Тверская область', 70 => 'Томская область', 71 => 'Тульская область', 72 => 'Тюменская область', 18 => 'Удмуртская Республика', 73 => 'Ульяновская область', 27 => 'Хабаровский край', 86 => 'Ханты-Мансийский АО', 74 => 'Челябинская область', 20 => 'Чеченская Республика', 21 => 'Чувашская Республика', 87 => 'Чукотский АО', 89 => 'Ямало-Ненецкий АО', 76 => 'Ярославская область', 90 => 'Байконур', 91 => 'Республика Крым', 77 => 'Москва', 78 => 'Санкт-Петербург', 92 => 'Севастополь'),
	);
	public static $region_code2 = array(
		0 => array(22 => 'Алтайский край', 28 => 'Амурская область', 29 => 'Архангельская область', 30 => 'Астраханская область', 31 => 'Белгородская область', 32 => 'Брянская область', 33 => 'Владимирская область', 34 => 'Волгоградская область', 35 => 'Вологодская область', 36 => 'Воронежская область', 79 => 'Еврейская АО', 75 => 'Забайкальский край', 37 => 'Ивановская область', 38 => 'Иркутская область', 7 => 'Кабардино-Балкарская Республика', 39 => 'Калининградская область', 40 => 'Калужская область', 41 => 'Камчатский край', 9 => 'Карачаево-Черкесская Республика', 42 => 'Кемеровская область', 43 => 'Кировская область', 44 => 'Костромская область', 23 => 'Краснодарский край', 24 => 'Красноярский край', 45 => 'Курганская область', 46 => 'Курская область', 47 => 'Ленинградская область', 48 => 'Липецкая область', 49 => 'Магаданская область', 50 => 'Московская область', 51 => 'Мурманская область', 83 => 'Ненецкий АО', 52 => 'Нижегородская область', 53 => 'Новгородская область', 54 => 'Новосибирская область', 55 => 'Омская область', 56 => 'Оренбургская область', 57 => 'Орловская область', 58 => 'Пензенская область', 59 => 'Пермский край', 25 => 'Приморский край', 60 => 'Псковская область', 1 => 'Республика Адыгея', 4 => 'Республика Алтай', 2 => 'Республика Башкортостан', 3 => 'Республика Бурятия', 5 => 'Республика Дагестан', 6 => 'Республика Ингушетия', 8 => 'Республика Калмыкия', 10 => 'Республика Карелия', 11 => 'Республика Коми', 12 => 'Республика Марий Эл', 13 => 'Республика Мордовия', 14 => 'Республика Саха (Якутия)', 15 => 'Республика Северная Осетия - Алания', 16 => 'Республика Татарстан', 17 => 'Республика Тыва', 19 => 'Республика Хакасия', 61 => 'Ростовская область', 62 => 'Рязанская область', 63 => 'Самарская область', 64 => 'Саратовская область', 65 => 'Сахалинская область', 66 => 'Свердловская область', 67 => 'Смоленская область', 26 => 'Ставропольский край', 68 => 'Тамбовская область', 69 => 'Тверская область', 70 => 'Томская область', 71 => 'Тульская область', 72 => 'Тюменская область', 18 => 'Удмуртская Республика', 73 => 'Ульяновская область', 27 => 'Хабаровский край', 86 => 'Ханты-Мансийский АО', 74 => 'Челябинская область', 20 => 'Чеченская Республика', 21 => 'Чувашская Республика', 87 => 'Чукотский АО', 89 => 'Ямало-Ненецкий АО', 76 => 'Ярославская область', 90 => 'Байконур', 91 => 'Республика Крым', 77 => 'Москва', 78 => 'Санкт-Петербург', 92 => 'Севастополь'),

		85 => array(1 => 'Акмолинская область', 2 => 'Актюбинская область', 3 => 'Алматинская область', 4 => 'Атырауская область', 5 => 'Восточно-Казахстанская область', 6 => 'Жамбылская область', 7 => 'Западно-Казахстанская область', 8 => 'Карагандинская область', 9 => 'Костанайская область', 10 => 'Кызылординская область', 11 => 'Мангистауская область', 12 => 'Павлодарская область', 13 => 'Северо-Казахстанская область', 14 => 'Туркестанская область', 15 => 'Нур-Султан', 16 => 'Алматы'),
		21 => array(1 => 'Брестская область', 2 => 'Витебская область', 3 => 'Гомельская область', 4 => 'Гродненская область', 5 => 'Минская область', 6 => 'Могилевская область', 7 => 'Минск'),
		14 => array(1 => 'Арагацотнская область', 2 => 'Араратская область', 3 => 'Армавирская область', 4 => 'Вайоцдзорская область', 5 => 'Гехаркуникская область', 6 => 'Котайкская область', 7 => 'Лорийская область', 8 => 'Сюникская область', 9 => 'Тавушская область', 10 => 'Ширакская область', 11 => 'Ереван'),
		108 => array(1 => 'Баткенская область', 2 => 'Джалал-Абадская область', 3 => 'Иссык-Кульская область', 4 => 'Нарынская область', 5 => 'Ошская область', 6 => 'Таласская область', 7 => 'Чуйская область', 8 => 'Бишкек', 9 => 'Ош'),
	);

	public static $fed_city = array(
		'id' => array(77, 78, 92,  15, 16,  7,  8, 9), // 11
		'name' => array('Москва', 'Санкт-Петербург', 'Севастополь',  'Нур-Султан', 'Алматы',  'Минск',  'Бишкек', 'Ош'), // 'Ереван'
		'region' => array(50, 47, 91,  1, 3,  5,  7, 5),
	);

	public static $no_region_city = array('Хабаровск', 'Екатеринбург', 'Новосибирск', 'Нижний Новгород', 'Набережные Челны', 'Новокузнецк', 'Нижний Тагил', 'Ярославль', 'Ялта', 'Уфа', 'Ульяновск', 'Казань', 'Красноярск', 'Краснодар', 'Кемерово', 'Калининград', 'Курск', 'Калуга', 'Воронеж', 'Волгоград', 'Владивосток', 'Владимир', 'Вологда', 'Астрахань', 'Архангельск', 'Пермь', 'Пенза', 'Ростов-на-Дону', 'Рязань', 'Омск', 'Оренбург', 'Орел', 'Липецк', 'Челябинск', 'Чебоксары', 'Чита', 'Череповец', 'Санкт-Петербург', 'Самара', 'Саратов', 'Ставрополь', 'Севастополь', 'Сочи', 'Смоленск', 'Сургут', 'Москва', 'Магнитогорск', 'Мурманск', 'Ижевск', 'Иркутск', 'Иваново', 'Тольятти', 'Томск', 'Тула', 'Тверь', 'Барнаул', 'Брянск', 'Белгород', 'Биробиджан', 'Майкоп', 'Горно-Алтайск', 'Улан-Удэ', 'Махачкала', 'Магас', 'Нальчик', 'Элиста', 'Черкесск', 'Петрозаводск', 'Сыктывкар', 'Симферополь', 'Йошкар-Ола', 'Саранск', 'Якутск', 'Владикавказ', 'Кызыл', 'Абакан', 'Грозный', 'Петропавловск-Камчатский', 'Курган', 'Магадан', 'Великий Новгород', 'Псков', 'Южно-Сахалинск', 'Тамбов', 'Тюмень', 'Нарьян-Мар', 'Ханты-Мансийск', 'Анадырь', 'Салехард');

	public static $country_flag = array(0, 21, 85, 212, 14, 108);

	public static $country_edost = array('Конго, Демократическая респ.', 'Корея, Северная', 'Корея, Южная', 'Беларусь', 'Молдова', 'Россия', 'Россия', 'Россия', 'Россия');
	public static $country_bitrix = array('Конго Демократическая респ.', 'Корея Северная', 'Корея Южная', 'Белоруссия', 'Молдавия', 'РОССИЯ', 'Российская Федерация', 'РОССИЙСКАЯ ФЕДЕРАЦИЯ', 'Russia');

	public static $region_edost = array(
		0 => array('Амурская область', 'Архангельская область', 'Астраханская область', 'Белгородская область', 'Брянская область', 'Владимирская область', 'Волгоградская область', 'Вологодская область', 'Воронежская область', 'Еврейская АО', 'Ивановская область', 'Иркутская область', 'Кабардино-Балкарская Республика', 'Калининградская область', 'Калужская область', 'Карачаево-Черкесская Республика', 'Кемеровская область', 'Кировская область', 'Костромская область', 'Курганская область', 'Курская область', 'Ленинградская область', 'Липецкая область', 'Магаданская область', 'Московская область', 'Мурманская область', 'Нижегородская область', 'Новгородская область', 'Новосибирская область', 'Омская область', 'Оренбургская область', 'Орловская область', 'Пензенская область', 'Псковская область', 'Республика Адыгея', 'Республика Алтай', 'Республика Башкортостан', 'Республика Бурятия', 'Республика Дагестан', 'Республика Ингушетия', 'Республика Калмыкия', 'Республика Карелия', 'Республика Коми', 'Республика Марий Эл', 'Республика Мордовия', 'Республика Саха (Якутия)', 'Республика Северная Осетия - Алания', 'Республика Татарстан', 'Республика Тыва', 'Республика Хакасия', 'Ростовская область', 'Рязанская область', 'Самарская область', 'Саратовская область', 'Сахалинская область', 'Свердловская область', 'Смоленская область', 'Тамбовская область', 'Тверская область', 'Томская область', 'Тульская область', 'Тюменская область', 'Удмуртская Республика', 'Ульяновская область', 'Ханты-Мансийский АО', 'Челябинская область', 'Чеченская Республика', 'Чувашская Республика', 'Ярославская область', 'Республика Крым', 'Республика Крым', 'Ямало-Ненецкий АО', 'Чукотский АО', 'Еврейская АО', 'Республика Северная Осетия - Алания', 'Ненецкий АО', 'Ханты-Мансийский АО', 'Москва', 'Москва', 'Санкт-Петербург', 'Санкт-Петербург', 'Севастополь', 'Севастополь'),
		85 => array('Жамбылская область', 'Нур-Султан', 'Нур-Султан', 'Нур-Султан', 'Алматы', 'Туркестанская область'),
		108 => array('Бишкек', 'Ош'),
	);
	public static $region_bitrix = array(
		0 => array('Амурская обл', 'Архангельская обл', 'Астраханская обл', 'Белгородская обл', 'Брянская обл', 'Владимирская обл', 'Волгоградская обл', 'Вологодская обл', 'Воронежская обл', 'Еврейская Аобл', 'Ивановская обл', 'Иркутская обл', 'Кабардино-Балкарская Респ', 'Калининградская обл', 'Калужская обл', 'Карачаево-Черкесская Респ', 'Кемеровская обл', 'Кировская обл', 'Костромская обл', 'Курганская обл', 'Курская обл', 'Ленинградская обл', 'Липецкая обл', 'Магаданская обл', 'Московская обл', 'Мурманская обл', 'Нижегородская обл', 'Новгородская обл', 'Новосибирская обл', 'Омская обл', 'Оренбургская обл', 'Орловская обл', 'Пензенская обл', 'Псковская обл', 'Адыгея Респ', 'Алтай Респ', 'Башкортостан Респ', 'Бурятия Респ', 'Дагестан Респ', 'Ингушетия Респ', 'Калмыкия Респ', 'Карелия Респ', 'Коми Респ', 'Марий Эл Респ', 'Мордовия Респ', 'Саха /Якутия/ Респ', 'Северная Осетия - Алания Респ', 'Татарстан Респ', 'Тыва Респ', 'Хакасия Респ', 'Ростовская обл', 'Рязанская обл', 'Самарская обл', 'Саратовская обл', 'Сахалинская обл', 'Свердловская обл', 'Смоленская обл', 'Тамбовская обл', 'Тверская обл', 'Томская обл', 'Тульская обл', 'Тюменская обл', 'Удмуртская Респ', 'Ульяновская обл', 'Ханты-Мансийский Автономный округ - Югра АО', 'Челябинская обл', 'Чеченская Респ', 'Чувашская Респ', 'Ярославская обл', 'Крым Респ', 'Крым', 'Ямало-Ненецкий автономный округ', 'Чукотский автономный округ', 'Еврейская автономная область', 'Республика Северная Осетия-Алания', 'Ненецкий автономный округ', 'Ханты-Мансийский автономный округ', 'Москва - регион', 'Москва (регион)', 'Санкт-Петербург - регион', 'Санкт-Петербург (регион)', 'Севастополь - регион', 'Севастополь (регион)'),
		85 => array('Жамбыльская область', 'Астана (регион)', 'Астана', 'Нур-Султан (регион)', 'Алматы (регион)', 'Южно-Казахстанская область'),
		108 => array('Бишкек (регион)', 'Ош (регион)'),
	);

	function Init() {

		$profile = array();
		$base_currency = self::GetRUB();

		$error = GetMessage('EDOST_DELIVERY_ERROR');
		$tariff = GetMessage('EDOST_DELIVERY_TARIFF');

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/edost.php' && empty($_SESSION['EDOST']['admin_profile_init'])) $mode = 'setting'; // настройки модуля

		if ($mode != 'setting')	{
			// нулевой тариф "Стоимость доставки будет предоставлена позже"
			$profile['0'] = array( // при создании профиля вместо строки '0' битрикс записывает в базу цифру '0' !!!
				'TITLE' => $error['zero_tariff'],
				'DESCRIPTION' => '',
				'RESTRICTIONS_WEIGHT' => array(0),
				'RESTRICTIONS_SUM' => array(0),
			);

			$insurance = array('', '_insurance');
			for ($i = 1; $i <= DELIVERY_EDOST_TARIFF_COUNT; $i++) foreach ($insurance as $k => $v) $profile[$i*2-1+$k] = array(
				'TITLE' => (isset($tariff['title'.$v][$i]) ? $tariff['title'.$v][$i] : ''),
				'DESCRIPTION' => (isset($tariff['description'.$v][$i]) ? $tariff['description'.$v][$i] : ''),
				'RESTRICTIONS_WEIGHT' => array(0),
				'RESTRICTIONS_SUM' => array(0),
			);
		}

		return array(
			'SID' => 'edost',
			'NAME' => $tariff['module_name'],
			'DESCRIPTION' => '',
			'DESCRIPTION_INNER' => $tariff['module_description_inner'],
			'BASE_CURRENCY' => $base_currency,
			'HANDLER' => __FILE__,
			'DBGETSETTINGS' => array('CDeliveryEDOST', 'GetSettings'),
			'DBSETSETTINGS' => array('CDeliveryEDOST', 'SetSettings'),
			'GETCONFIG' => array('CDeliveryEDOST', 'GetConfig'),
			'COMPABILITY' => array('CDeliveryEDOST', 'Compability'),
			'CALCULATOR' => array('CDeliveryEDOST', 'Calculate'),
			'PROFILES' => $profile
		);

	}

	function GetConfig() {

		$data = GetMessage('EDOST_DELIVERY_CONFIG');

		// порядок сортировки
		$s = array(
			'id', 'ps', 'host', 'hide_error', 'show_zero_tariff', 'map', 'postmap', 'office_near', 'office_unsupported', 'office_unsupported_fix', 'office_unsupported_percent', 'office_tel', 'template_script', 'send_zip', 'autoselect', 'hide_payment', 'sort_ascending', 'admin', 'cod_status', 'edost_discount', 'sale_discount', 'sale_discount_cod', 'package',
			'control', 'control_auto', 'register_status', 'control_status_arrived', 'control_status_completed', 'control_status_completed_cod', 'browser',
			'template', 'template_ico', 'template_format', 'template_block', 'template_block_type', 'template_cod', 'template_autoselect_office', 'template_map_inside'
		);
		$field = array_fill_keys($s, '');

		foreach ($field as $k => $v) {
			$s = (isset($data['field'][$k]) ? $data['field'][$k] : false);
			$v = array('GROUP' => 'all');
			$v['TYPE'] = (isset($s['TYPE']) ? $s['TYPE'] : 'DROPDOWN');
			$v['TITLE'] = (isset($s['TITLE']) ? $s['TITLE'] : '');
			$v['DEFAULT'] = (isset(self::$setting_key[$k]) ? self::$setting_key[$k] : '');
			if ($v['TYPE'] == 'DROPDOWN') $v['VALUES'] = (isset($s['VALUES']) ? $s['VALUES'] : array());
			$field[$k] = $v;
		}
//		echo '<br><b>field:</b> <pre style="font-size: 12px">'.print_r($field, true).'</pre>';

		// список статусов заказа
		$status = array('' => $data['no_change']);
		$ar = CSaleStatus::GetList(array('SORT' => 'ASC'), array('LID' => LANGUAGE_ID), false, false, array('ID', 'NAME'));
		while ($v = $ar->Fetch()) $status[$v['ID']] = '['.$v['ID'].'] '.str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['NAME']);
		$field['cod_status']['VALUES'] = $field['control_status_arrived']['VALUES'] = $field['control_status_completed']['VALUES'] = $field['control_status_completed_cod']['VALUES'] = $field['register_status']['VALUES'] = $status;

		return array(
			'CONFIG_GROUPS' => array('all' => $data['head']),
			'CONFIG' => $field,
		);

	}

	function GetSettings($strSettings) {
		return self::ParseConfig($strSettings);
	}

	function SetSettings($arSettings) {

		$r = array();
		foreach (self::$setting_key as $k => $v) $r[] = (isset($arSettings[$k]) ? $arSettings[$k] : $v);
		$r = implode(';', $r);

		// сохранение на стандартной странице редактирования модуля (индивидуальные настройки для различных сайтов НЕ поддерживаются!!!)
		if (strpos($_SERVER['REQUEST_URI'], 'sale_delivery_handler_edit.php') !== false) {
			$ar = array('all' => $r);
			\Bitrix\Main\Config\Option::set('edost.delivery', 'module_setting', serialize($ar));
		}

		return $r;

	}

	function BitrixCalculate(&$arConfig, &$arOrder, $param = 'calculate') {

		$mode = '';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		if ($s == '/bitrix/admin/sale_order_view.php') $mode = 'order_view'; // просмотр заказа
		if ($s == '/bitrix/admin/sale_order_shipment_edit.php') $mode = 'shipment_edit'; // редактирование отгрузки
		if ($s == '/bitrix/admin/sale_order_create.php') $mode = 'order_create'; // новый заказ
		if ($s == '/bitrix/admin/sale_order_ajax.php') $mode = 'order_ajax'; // изменение разрешения доставки, отгрузки и идентификатора отправления
		if (strpos($s, '/shop/orders/shipment/details/') !== false) $mode = 'crm_shipment'; // CRM редактирование отгрузки
		if (strpos($s, '/bitrix/components/bitrix/crm.order.') !== false) $mode = 'crm_ajax'; // CRM ajax

		// отключение расчета на странице просмотра заказа в админке (битрикс доставку рассчитывает, но результат ни на что не влияет!)
		if ($param == 'calculate' && $mode == 'order_ajax' && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'getOrderTails') return false;

		$admin = (in_array($mode, array('crm_shipment', 'crm_ajax', 'shipment_edit', 'order_ajax')) && !empty($arConfig['admin']['VALUE']) && $arConfig['admin']['VALUE'] == 'Y' ? true : false);

		if ($admin) {
			$config = CDeliveryEDOST::GetEdostConfig($arOrder['SITE_ID']);
			if (empty($config['param']['zero_tariff']) || empty($config['param']['module_id'])) $admin = false;
			else {
				$arConfig['hide_error']['VALUE'] = 'N';
				$arConfig['show_zero_tariff']['VALUE'] = 'N';
			}

			// изменение местоположения при редактировании отгрузки с модулем edost.locations
			$p = false;
			if ($mode == 'order_ajax' && !empty($_REQUEST['formData'])) $p = $_REQUEST['formData'];
			if ($mode == 'crm_ajax' && !empty($_REQUEST['FORM_DATA'])) $p = $_REQUEST['FORM_DATA'];
			if (!empty($p['edost_shop_LOCATION'])) $arOrder['LOCATION_TO'] = edost_class::GetLocationID($p['edost_shop_LOCATION']);
			if (!empty($p['edost_shop_ZIP'])) $arOrder['LOCATION_ZIP'] = $p['edost_shop_ZIP'];
		}

		// расчет доставки
		$data = self::EdostCalculate($arOrder, $arConfig);

		// отключение форматирования тарифов в админке при повторном расчете
		if ($admin) {
			$o = self::FilterOrder($arOrder);
			if (!empty(self::$admin_order) && !self::OrderChanged($o, self::$admin_order)) $admin = false; else self::$admin_order = $o;
		}

		if ($mode == 'order_create' && !empty($_REQUEST['ID'])) {
			$props = edost_class::GetProps(intval($_REQUEST['ID']), array('no_payment'));
			if (!empty($props['office'])) $_SESSION['EDOST']['admin_order_edit_office'] = array(0 => array('id' => 'edost:'.$props['office']['profile'], 'profile' => $props['office']['profile'], 'office_id' => $props['office']['id']));
			else if (isset($_SESSION['EDOST']['admin_order_edit_office'][0])) unset($_SESSION['EDOST']['admin_order_edit_office'][0]);
		}

		// форматирование тарифов при редактировании отгрузки в админке
		if ($admin) {
			$shipment_id = 0;
			if ($mode == 'crm_shipment') {
				$w = explode('/', $s);
				if (isset($w[5])) $shipment_id = intval($w[5]);
			}

			$id = 0;
			if (!empty($_REQUEST['shipment_id'])) $id = $_REQUEST['shipment_id'];
			else if (!empty($_REQUEST['formData']['SHIPMENT']['1']['SHIPMENT_ID'])) $id = $_REQUEST['formData']['SHIPMENT']['1']['SHIPMENT_ID'];
			else if (!empty($_REQUEST['FORM_DATA']['PRODUCT_COMPONENT_DATA']['params']['SHIPMENT_ID'])) $id = $_REQUEST['FORM_DATA']['PRODUCT_COMPONENT_DATA']['params']['SHIPMENT_ID'];
			else if (!empty($shipment_id)) $id = $shipment_id;

			if ($id == '') $id = 0;

			$ar = array();
			if (!empty($data['data'])) {
				CDeliveryEDOST::GetAutomatic();
				foreach ($data['data'] as $k => $v) foreach (CDeliveryEDOST::$automatic as $k2 => $v2) if ($v2['parent_id'] == $config['param']['module_id'] && $v2['profile'] == $k) {
					$ar[$k2] = array('ID' => $k2, 'NAME' => $v2['name'], 'DESCRIPTION' => '');
					break;
				}
			}

			// загрузка активного офиса из адреса при первом открытии
			$props = false;
			if (!empty($_GET['order_id'])) $props = edost_class::GetProps($_GET['order_id'], array('no_payment'));
			else if ($mode == 'crm_shipment' && $id != 0) $props = edost_class::GetProps($id, array('shipment', 'no_payment'));
			if (!empty($props['office'])) $_SESSION['EDOST']['admin_order_edit_office'][$id] = array('id' => 'edost:'.$props['office']['profile'], 'profile' => $props['office']['profile'], 'office_id' => $props['office']['id']);

			if (!isset($_SESSION['EDOST']['admin_order_edit_office'][$id]) && !isset($_SESSION['EDOST']['admin_order_edit_office'][0])) $active = false;
			else {
				$i = (isset($_SESSION['EDOST']['admin_order_edit_office'][$id]) ? $id : 0);
				$active = $_SESSION['EDOST']['admin_order_edit_office'][$i];
			}

			define('DELIVERY_EDOST_PRICE_FORMATTED', 'Y'); // форматирование цены по стандарту eDost
			$c = array('hide_error' => 'N', 'show_zero_tariff' => 'N', 'template' => 'Y', 'map' => 'Y', 'template_format' => 'odt', 'template_block' => 'all', 'template_block_type' => 'none', 'template_cod' => 'off', 'template_map_inside' => 'N', 'NAME_NO_CHANGE' => true, 'NO_INSURANCE' => 'Y', 'ADD_ZERO_TARIFF' => true, 'NO_POST_MAIN' => 'Y', 'NO_POST_CITY_UNSET' => true);
			if (!empty($_REQUEST['formData']['edost_post_manual']) || !empty($_REQUEST['FORM_DATA']['edost_post_manual'])) $c['POST_MANUAL'] = true;
			$format = edost_class::FormatTariff($ar, self::GetRUB(), $arOrder, $active, $c);
//			echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';

			$tracking = edost_class::GetTracking($arOrder['SITE_ID']);

			$ar = array();
			if (!empty($format['data'])) foreach ($format['data'] as $f_key => $f) if (!empty($f['tariff'])) {
				if ($f['head'] != '') $ar[] = array('head' => $f['head']);
				foreach ($f['tariff'] as $k => $v) if (isset($v['id'])) {
					$v['id'] = edost_class::GetBitrixID($v);
					$v['title'] = edost_class::GetTitle($v, true);
					if (isset($v['head'])) unset($v['head']);
					if (isset($v['office_mode']) && empty($v['office_map'])) unset($v['office_mode']);
					if (isset($v['transfer_formatted'])) $v['transfer_formatted'] = strip_tags($v['transfer_formatted']);
					if (!isset($v['pricetotal_formatted'])) $v['pricetotal_formatted'] = strip_tags($v['price_formatted']);
					if (!empty($tracking['data'][$v['company_id']])) {
						$v['tracking_example'] = $tracking['data'][$v['company_id']]['example'];
						$v['tracking_format'] = $tracking['data'][$v['company_id']]['format'];
					}
					$ar[] = $v;
				}
				if ($f_key == 'office' && count($f['tariff']) > 1) $ar[] = array('id' => '', 'office_mode' => 'all');
			}

			$yandex_api_key = \Bitrix\Main\Config\Option::Get('fileman', 'yandex_map_api_key', '');

			$json = '{"ico_path": "/bitrix/images/delivery_edost_img", "yandex_api_key": "'.(!empty($yandex_api_key) ? $yandex_api_key : '').'", "template_ico": "'.(!empty($arConfig['template_ico']['VALUE']) ? $arConfig['template_ico']['VALUE'] : 'C').'", '.
				'"format": '.edost_class::GetJson($ar, array('head', 'id', 'profile', 'title', 'day', 'price', 'pricetotal', 'pricetotal_formatted', 'pricecash', 'pricecash_formatted', 'priceinfo_formatted', 'transfer_formatted', 'checked', 'office_id', 'office_mode', 'office_address_full', 'error', 'tracking_example', 'tracking_format', 'office_detailed', 'company_id'), true, false).
				', "module_id": '.$config['param']['module_id'].', "zero_tariff": '.$config['param']['zero_tariff'].
				(isset($format['map_json']) ? ', '.$format['map_json'] : '').
				(!empty($format['warning']) ? ', "warning": "'.$format['warning'].'"' : '').'}';

			$_SESSION['EDOST']['admin_order_edit'][$id] = $json;
		}

		return $data;

	}


	function Calculate($profile, $arConfig, $arOrder, $STEP) {
//		echo '<br><b>Calculate:</b> <pre style="font-size: 12px">'.print_r($arOrder, true).'</pre>';
//		echo '<br><b>Calculate:</b> <pre style="font-size: 12px">'.print_r($arConfig, true).'</pre>';
//		echo '<br><b>_REQUEST:</b> <pre style="font-size: 12px">'.print_r($_REQUEST, true).'</pre>';

		if ($STEP >= 3) {
			$error = GetMessage('EDOST_DELIVERY_ERROR');
			return array('RESULT' => 'ERROR', 'TEXT' => $error['connect']);
		}

		$data = self::BitrixCalculate($arConfig, $arOrder);

		// вывод результата
		if (isset($data['data'][$profile])) {
			$v = $data['data'][$profile];
			if ($v['id'] <= DELIVERY_EDOST_TARIFF_COUNT) return array('RESULT' => 'OK', 'VALUE' => $v['price'], 'TRANSIT' => $v['day']);
		}

		return array('RESULT' => 'OK', 'VALUE' => 0, 'TRANSIT' => '');

	}

	function Compability($arOrder, $arConfig) {
//		echo '<br><b>Compability:</b> <pre style="font-size: 12px">'.print_r($arOrder, true).'</pre>';
//		echo '<br><b>Compability:</b> <pre style="font-size: 12px">'.print_r($arConfig, true).'</pre>';

		$r = array();
		$data = self::BitrixCalculate($arConfig, $arOrder, 'compability');
		if (!empty($data['data'])) foreach ($data['data'] as $k => $v) $r[] = $k;

		// нулевой тариф "Стоимость доставки будет предоставлена позже"
		if (count($r) == 0 && empty($data['hide']) && ($arConfig['hide_error']['VALUE'] != 'Y' || $arConfig['show_zero_tariff']['VALUE'] == 'Y')) $r = array(0);

		return $r;

	}


	// загрузка модулей eDost и ограничений по сайтам ($site_id = false - удалено)
	public static function GetModule() {

		// привязка модулей к сайтам
		$restriction_site = array();
		$ar = \Bitrix\Sale\Internals\ServiceRestrictionTable::getList(array('filter' => array('=CLASS_NAME' => '\Bitrix\Sale\Delivery\Restrictions\BySite')));
		while ($v = $ar->fetch()) $restriction_site[$v['SERVICE_ID']] = $v;

		// модули доставки
		$module = array();
		$ar = \Bitrix\Sale\Delivery\Services\Table::GetList(array('filter' => array('=CODE' => 'edost')));
		while ($v = $ar->fetch()) $module[$v['ID']] = $v;

		return array(
			'module' => $module,
			'restriction_site' => $restriction_site,
		);

	}


	// получение кода местоположения стандарта eDost по названию страны (или коду страны eDost и названию региона)
	public static function GetEdostLocationID($country, $region = '', $convert_charset = true) {

		if ($country === '') return false;
		if ($convert_charset) {
			if ($region === '') $country = $GLOBALS['APPLICATION']->ConvertCharset($country, LANG_CHARSET, 'windows-1251');
			else {
				$region = $GLOBALS['APPLICATION']->ConvertCharset($region, LANG_CHARSET, 'windows-1251');
				$region = str_replace('ё', 'е', $region);
			}
		}
		if ($region === '') {
			$i = array_search($country, self::$country_bitrix);
			if ($i !== false) $country = self::$country_edost[$i];
			return array_search($country, self::$country_code);
		}
		if (!empty(self::$region_code2[$country])) {
			if (isset(self::$region_bitrix[$country])) {
				$i = array_search($region, self::$region_bitrix[$country]);
				if ($i !== false) $region = self::$region_edost[$country][$i];
			}
			return array_search($region, self::$region_code2[$country]);
		}
		return false;

	}


	// получение местоположения стандарта eDost по id местоположения битрикса
	public static function GetEdostLocation($id) {

		if (empty($id)) return false;

		if (substr($id, 0, 1) === '0') $id = CSaleLocation::getLocationIDbyCODE($id);

//		unset($_SESSION['EDOST']['location']);
		if (!empty($_SESSION['EDOST']['location']['id'])) {
			$v = $_SESSION['EDOST']['location'];
			if ($id == $v['id']) return $v;
		}

		$r = array('id' => $id, 'country' => '', 'region' => '', 'city' => '', 'country_name' => '', 'region_name' => '', 'bitrix' => array('country' => '', 'region' => '', 'city' => ''));

		$data = array();
		$ar = \Bitrix\Sale\Location\LocationTable::getList(array(
		    'filter' => array('=ID' => $id, '=PARENTS.NAME.LANGUAGE_ID' => 'ru'),
		    'select' => array('LCODE' => 'PARENTS.CODE', 'LID' => 'PARENTS.ID', 'LNAME' => 'PARENTS.NAME.NAME', 'LTYPE' => 'PARENTS.TYPE.CODE'),
		    'order' => array('PARENTS.DEPTH_LEVEL' => 'desc')
		));
		$ar->addReplacedAliases(array('LCODE' => 'code', 'LID' => 'id', 'LNAME' => 'name', 'LTYPE' => 'type'));
		while ($v = $ar->fetch()) $data[] = $v;

		$location = array();
		foreach ($data as $k => $v) if (in_array($v['type'], array('COUNTRY', 'REGION', 'SUBREGION', 'CITY', 'VILLAGE'))) {
			if ($v['type'] == 'COUNTRY') $location['country'] = $v;
			if (in_array($v['type'], array('REGION', 'CITY'))) $location['region'] = $v;
			if (!isset($location['city']) && in_array($v['type'], array('SUBREGION', 'CITY', 'VILLAGE'))) $location['city'] = $v;
		}

		$country = (!empty($location['country']['name']) ? self::GetEdostLocationID($location['country']['name']) : false);
		if ($country === false) return false;

		$r['country'] = $country;
		$r['country_name'] = $GLOBALS['APPLICATION']->ConvertCharset(self::$country_code[$country], 'windows-1251', LANG_CHARSET);
		$r['bitrix']['country'] = $location['country']['id'];

		if (!empty(self::$region_code2[$country])) {
			$r['city'] = (!empty($location['city']['name']) ? $location['city']['name'] : '');
			$r['bitrix']['city'] = $r['city'];
			$r['city'] = $GLOBALS['APPLICATION']->ConvertCharset($r['city'], LANG_CHARSET, 'windows-1251');
			$r['city'] = str_replace('ё', 'е', $r['city']);
			if (in_array($r['city'], array('Астана', 'Нур-Султан (Астана)'))) $r['city'] = 'Нур-Султан';

			$region = (!empty($location['region']['name']) ? $location['region']['name'] : '');
			if ($region != '') $region = self::GetEdostLocationID($country, $region);
			else if ($r['city'] == '') $region = false;
			else $region = self::GetEdostLocationID($country, $r['city'], false); // города федерального значения (без регионов)

			if ($region === false) return false;

			$r['region'] = $region;
			$r['region_name'] = $GLOBALS['APPLICATION']->ConvertCharset(self::$region_code2[$country][$region], 'windows-1251', LANG_CHARSET);
			$r['bitrix']['region'] = $location['region']['id'];
		}
		else {
			$r['region_name'] = (!empty($location['region']['name']) ? $location['region']['name'] : '');
			$r['bitrix']['city'] = (!empty($location['city']['name']) ? $location['city']['name'] : '');
		}

		$_SESSION['EDOST']['location'] = $r;

		return $r;

	}


	public static function ParseConfig($s, $name = 'main') {

		if ($name == 'main') $key = self::$setting_key;
		else $key = self::$setting_param_key;

		$r = array();
		$ar = explode(';', $s);
		$i = 0;
		foreach ($key as $k => $v) {
			$r[$k] = (isset($ar[$i]) ? $ar[$i] : $v);
			$i++;
		}
		return $r;

	}


	// загрузка настроек модуля edost из 'option' или из строки $data
	public static function GetEdostConfig($site_id, $data = false, $search = false) {

		if ($search) {
			$r = false;
			if (isset($data[$site_id])) $r = $data[$site_id];
			else if (isset($data['all'])) $r = $data['all'];
			return $r;
		}

		$r = array();
		$first = false;

		if ($data !== false) $r['all'] = $data;
		else {
			if (!empty(self::$config)) $s = self::$config;
			else $s = self::$config = \Bitrix\Main\Config\Option::get('edost.delivery', 'module_setting');
			if ($s != '') $r = unserialize($s);
//			echo '<br><b>file config:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';
		}

		foreach ($r as $k => $v) {
			$v = explode(';param=', $v);
			$r[$k] = self::ParseConfig($v[0]);
			$r[$k]['param'] = self::ParseConfig(!empty($v[1]) ? $v[1] : '', 'param');
			if ($first === false) $first = $r[$k];
		}

		if ($site_id !== 'all')
			if ($site_id === '') $r = $first;
			else if (isset($r['all'])) $r = $r['all'];
			else if (isset($r[$site_id])) $r = $r[$site_id];
			else $r = false;

		return $r;

	}

	// получение тарифа по коду битрикса
	public static function GetEdostTariff($profile, $office = false, $config = false) {

		$type2 = '';
		$options = $office_key = 0;
		if (isset($office['type2'])) $type2 = $office['type2'];
		if (isset($office['office_type2'])) $type2 = $office['office_type2'];
		if (isset($office['options'])) $options = $office['options'];
		if (isset($office['office_options'])) $options = $office['office_options'];
		if (isset($office['office_key'])) $office_key = $office['office_key'];

		$data = self::$result;
		if (isset($data['data'][$profile])) {
			$v = $data['data'][$profile];
			if ($v['id'] <= DELIVERY_EDOST_TARIFF_COUNT) {
				if (isset($v['priceoffice'][$type2])) {
					$v['priceoffice_active'] = true;
					foreach ($v['priceoffice'][$type2] as $k2 => $v2) if ($k2 != 'type') $v[$k2] = $v2;
				}

				if (edost_class::AddUnsupported($v, $options, $config)) $v['priceoffice_active'] = true;
				if (edost_class::CodDisable($options)) $v['pricecash'] = -1;
				if ($office_key != 0) $v['office_key'] = $office_key;

				return $v;
			}
		}

		// тариф не найден - вывод ошибки
		return array(
			'error' => self::GetEdostError(isset($data['error']) ? $data['error'] : 0),
			'price' => 0
		);

	}


	// получение типа вывода поля (для модуля edost.locations)
	public static function GetPropRequired($id, $prop) {

		$tariff = false;
		CDeliveryEDOST::GetAutomatic();
		if (isset(CDeliveryEDOST::$automatic[$id])) $tariff = CDeliveryEDOST::$automatic[$id];
		if ($tariff === false || $tariff['automatic'] !== 'edost') return '';

		$profile = $tariff['profile'];
		$tariff = ceil(intval($profile) / 2);

		if ($prop == 'zip') return (in_array($tariff, CDeliveryEDOST::$zip_required) ? 'Y' : '');
		if ($prop == 'metro') return (in_array($tariff, array(31, 32, 33, 34)) ? 'S' : '');
		if ($prop == 'passport') return (in_array($tariff, CDeliveryEDOST::$passport_required) ? 'Y' : '');

		return '';

	}


	// получение ошибки калькулятора по коду
	public static function GetEdostError($id, $type = 'delivery') {

		$error = GetMessage('EDOST_DELIVERY_ERROR');
		$r = $error['head'].($type == 'office' ? $error['office'] : '');

		if (isset($error[$id.'_'.$type])) $r .= $error[$id.'_'.$type];
		else if (isset($error[$id])) $r .= $error[$id];
		else $r .= $error['no_delivery'];

		$r .= '!';
		return $r;

	}


	// получение предупреждений калькулятора
	public static function GetEdostWarning($id = false, $head = true) {

		$r = '';
		if ($id === false) $data = self::$result;
		if ($id !== false || !empty($data['warning'])) {
			$warning = GetMessage('EDOST_DELIVERY_WARNING');
			if ($id !== false) {
				if (!empty($warning[$id])) $r .= $warning[$id];
			}
			else {
				foreach ($data['warning'] as $v) if (!empty($warning[$v])) $r .= $warning[$v].'<br>';
				if ($r != '') $r = ($head ? $warning[0].'<br>' : '').$r;
			}
		}
		return $r;

	}


	// обработка товаров (вес по умолчанию, загрузка свойств + сложение веса, габаритов и стоимости)
	public static function SetItem(&$items, $total = false) {

		$weight_default = (defined('DELIVERY_EDOST_WEIGHT_DEFAULT') ? DELIVERY_EDOST_WEIGHT_DEFAULT : 0);
		$weight_from_main_product = (defined('DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT') && DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT == 'Y' ? true : false);
		$property_from_main_product = (defined('DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT') && DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT == 'Y' ? true : false);

		$prop = array();
		$prop_size = array();
		$prop_get = array('ID', 'NAME');
		$ar = array('WEIGHT', 'VOLUME', 'LENGTH', 'WIDTH', 'HEIGHT', 'SIZE');
		foreach ($ar as $v) if (defined('DELIVERY_EDOST_'.$v.'_PROPERTY_NAME')) {
			$s = constant('DELIVERY_EDOST_'.$v.'_PROPERTY_NAME');
			$prop[$v] = 'PROPERTY_'.$s.'_VALUE';
			if (in_array($v, array('LENGTH', 'WIDTH', 'HEIGHT'))) $prop_size[] = $prop[$v];
			$prop_get[] = 'PROPERTY_'.$s;
		}
		if (count($prop_size) <= 1) unset($prop_size);

		$prop['MEASURE'] = (defined('DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE') ? DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE : 'G');
		$prop['RATIO'] = (defined('DELIVERY_EDOST_VOLUME_PROPERTY_RATIO') ? DELIVERY_EDOST_VOLUME_PROPERTY_RATIO : 1);
		$prop['SIZE_DELIMITER'] = (defined('DELIVERY_EDOST_SIZE_DELIMITER_PROPERTY_NAME') ? DELIVERY_EDOST_SIZE_DELIMITER_PROPERTY_NAME : 'x');

		if ($total) {
			$currency = CSaleLang::GetLangCurrency(isset($order['SITE_ID']) ? $order['SITE_ID'] : SITE_ID);
			$base_currency = self::GetRUB();

			$total = array(
				'weight_zero' => false,
				'weight' => 0,
				'price' => 0,
				'package' => array(),
			);
		}

		foreach ($items as $k => $item) {
//			echo '<br><b>edost module - item:</b> <pre style="font-size: 12px">'.print_r($item, true).'</pre>';

			if (empty($item['TYPE']) && !empty($item['SET_PARENT_ID'])) continue; // товары из комплекта

			$weight = (isset($item['WEIGHT']) && $item['WEIGHT'] > 0 ? $item['WEIGHT'] : 0);
			$s = (isset($item['DIMENSIONS']) ? $item['DIMENSIONS'] : '');
			if (!is_array($s) && substr($s, 0, 5) === 'a:3:{') $s = unserialize($s);
			$s = array((isset($s['LENGTH']) ? $s['LENGTH'] : 0), (isset($s['WIDTH']) ? $s['WIDTH'] : 0), (isset($s['HEIGHT']) ? $s['HEIGHT'] : 0));

			// использовать новый интерфейс IBXSaleProductProvider !!!!!
			if (isset($item['MODULE']) && isset($item['CALLBACK_FUNC']) && strlen($item['CALLBACK_FUNC']) > 0) {
				CSaleBasket::UpdatePrice($item['ID'], $item['CALLBACK_FUNC'], $item['MODULE'], $item['PRODUCT_ID'], $item['QUANTITY']);
				$item = CSaleBasket::GetByID($item['ID']);
			}

			// получение данных из главного товара по id торгового предложения (включается в константах)
			if (isset($item['PRODUCT_ID']) && ($weight_from_main_product || $property_from_main_product)) {
				$main_product = CCatalogSku::GetProductInfo($item['PRODUCT_ID']);
				if (isset($main_product['ID']) && $main_product['ID'] > 0) {
					if ($weight_from_main_product && $weight == 0) {
						$v = CCatalogProduct::GetByID($main_product['ID']);
						if (isset($v['WEIGHT']) && $v['WEIGHT'] > 0) $weight = $v['WEIGHT'];
					}

					if ($property_from_main_product) $item['PRODUCT_ID'] = $main_product['ID'];
				}
			}

			// загрузка свойств товара, если не задан вес или габариты (включается в константах)
			$get_weight = (isset($prop['WEIGHT']) && $weight == 0 ? true : false);
			$get_size = ((!empty($prop_size) || isset($prop['SIZE']) || isset($prop['VOLUME'])) && ($s[0] == 0 || $s[1] == 0 || $s[2] == 0) ? true : false);
			if ($get_weight || $get_size) {
				$ar = CIBlockElement::GetById($item['PRODUCT_ID']);
				$v = $ar->Fetch();
				$ar = CIBlockElement::GetList(array(), array('ID' => $item['PRODUCT_ID'], 'IBLOCK_ID' => $v['IBLOCK_ID']), false, array('nPageSize' => 5), $prop_get);
				if ($v = $ar->GetNext()) {
					if ($get_weight && isset($v[$prop['WEIGHT']])) {
					    self::CommaToPoint($v[$prop['WEIGHT']]);
					    if ($v[$prop['WEIGHT']] > 0) {
						    $weight = $v[$prop['WEIGHT']];
						    if ($prop['MEASURE'] == 'KG') $weight = $weight*1000;
						}
					}
					if ($get_size) {
						$s = array(0, 0, 0);
						if (!empty($prop_size)) foreach ($prop_size as $k2 => $v2) if (isset($v[$v2])) {
							self::CommaToPoint($v[$v2]);
							if ($v[$v2] > 0) $s[$k2] = $v[$v2];
						}

						// габариты заданы одной строкой
						if (isset($prop['SIZE']) && isset($v[$prop['SIZE']]) && $s[0] == 0 && $s[1] == 0 && $s[2] == 0) {
							$s2 = explode($prop['SIZE_DELIMITER'], $v[$prop['SIZE']]);
							foreach ($s2 as $k2 => $v2) {
								self::CommaToPoint($v2);
								if (empty($v2)) $s2 = false; else $s2[$k2] = $v2;
							}
							if ($s2 !== false && count($s2) == 3) $s = $s2;
						}

						// если габаритов нет, но задан объем, тогда габариты вычисляются из объема
						if (isset($prop['VOLUME']) && isset($v[$prop['VOLUME']]) && $s[0] == 0 && $s[1] == 0 && $s[2] == 0) {
							self::CommaToPoint($v[$prop['VOLUME']]);
							$volume = ($v[$prop['VOLUME']] > 0 ? $v[$prop['VOLUME']] : 0);
							$s[0] = $s[1] = $s[2] = pow($volume, 1/3) * $prop['RATIO'];
						}
					}
				}
			}

			// если задано только два размера, тогда считается, что это труба (длина и диаметр)
			if ($s[0] > 0 && $s[1] > 0 && $s[2] == 0) $s[2] = $s[1];
			if ($s[0] > 0 && $s[2] > 0 && $s[1] == 0) $s[1] = $s[2];

			if ($weight == 0) $weight = $weight_default;

			$item['WEIGHT'] = $weight;
			$item['size'] = $s;

			if ($weight == 0) $total['weight_zero'] = true;
			$weight = $weight * $item['QUANTITY'];

			if (!empty($total)) {
				edost_class::PackItem($total, $s, $item['QUANTITY']);
				$total['weight'] += $weight;
				$total['price'] += CCurrencyRates::ConvertCurrency($item['PRICE'], isset($item['CURRENCY']) ? $item['CURRENCY'] : $currency, $base_currency) * $item['QUANTITY'];
			}

			$items[$k] = $item;
		}

		if (!empty($total)) return $total;

	}


	// фильтрация параметров заказа (для дальнейшего сравнения)
	public static function FilterOrder($o) {

		$ar = array('MAX_DIMENSIONS');
		$ar_items = array('PRICE', 'CURRENCY', 'WEIGHT', 'QUANTITY', 'DELAY', 'CAN_BUY', 'DIMENSIONS', 'NAME');
		foreach ($ar as $v) if (isset($o[$v])) unset($o[$v]);
		if (!empty($o['ITEMS'])) foreach ($o['ITEMS'] as $k => $v) {
			$s = array();
			foreach ($ar_items as $v2) if (isset($v[$v2])) {
				$u = $v[$v2];
				if ($v2 == 'PRICE') $u = round($u, 2);
				else if ($v2 == 'WEIGHT') $u = round($u);
				else if ($v2 == 'DIMENSIONS' && !empty($u)) {
					if (!is_array($u) && substr($u, 0, 5) === 'a:3:{') $u = unserialize($u);
					if (is_array($u)) $u = implode('x', $u);
				}
				$s[$v2] = $u;
			}
			$o['ITEMS'][$k] = $s;
		}

		return $o;

	}

	// сравнение заказов
	public static function OrderChanged($o, $o2) {

		$r = false;
		$s = array($o, $o2);
		foreach ($s as $k => $v) {
			$i = '';
			if (isset($v['ITEMS'])) {
				if (!empty($v['ITEMS'])) foreach ($v['ITEMS'] as $k2 => $v2) {
					if (isset($v2['NAME'])) unset($v2['NAME']);
					$v['ITEMS'][$k2] = implode('|', $v2);
				}
				$i = implode(':', $v['ITEMS']);
				unset($v['ITEMS']);
			}
			$s[$k] = implode('|', $v).':'.$i;
		}
		if ($s[0] != $s[1]) $r = true;
		return $r;

	}


	// расчет доставки
	public static function EdostCalculate($order, $bitrix_config) {
//		echo '<br><b>order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';

		$order['original'] = self::FilterOrder($order);

		if (!isset($order['NO_LOCAL_CACHE']) || $order['NO_LOCAL_CACHE'] != 'Y') foreach (self::$result_full as $v) if (!self::OrderChanged($order['original'], $v['order']['original'])) { self::$result = $v; return $v; }

		$config = array();
		foreach ($bitrix_config as $k => $v) $config[$k] = (is_array($v) ? $v['VALUE'] : $v); // если элемент является массивом, значит параметры в формате битрикса

		if (class_exists('edost_function') && method_exists('edost_function', 'BeforeCalculate')) {
			$v = edost_function::BeforeCalculate($order, $config);
			if ($v !== false && is_array($v)) return self::SetResult($v, $order, $config);
		}

		$cart = (!isset($order['CART']) ? 'Y' : $order['CART']);
		$write_log = (defined('DELIVERY_EDOST_WRITE_LOG') && DELIVERY_EDOST_WRITE_LOG == 1 ? true : false);

		// получение данных по товарам в $order['ITEMS'] ИЛИ в корзине ИЛИ по коду заказа
		$items = array();
		if ($cart != 'N' && CModule::IncludeModule('iblock')) {
			if (isset($order['ITEMS'])) {
				// товары из списка
				if (is_array($order['ITEMS']) && count($order['ITEMS']) > 0) foreach ($order['ITEMS'] as $v)
					if ((!isset($v['CAN_BUY']) || $v['CAN_BUY'] == 'Y') && (!isset($v['DELAY']) || $v['DELAY'] == 'N') && !empty($v['QUANTITY'])) $items[] = $v;
			}
			else {
				// товары из корзины ИЛИ заказа
				if (!empty($order['ORDER_ID']) && !empty($order['SITE_ID'])) $filter = array('ORDER_ID' => $order['ORDER_ID'], 'LID' => $order['SITE_ID']);
				else $filter = array('FUSER_ID' => CSaleBasket::GetBasketUserID(), 'LID' => SITE_ID, 'ORDER_ID' => 'NULL');

				$ar = CSaleBasket::GetList(array('NAME' => 'ASC', 'ID' => 'ASC'), $filter, false, false, array('ID', 'CALLBACK_FUNC', 'MODULE', 'PRODUCT_ID', 'QUANTITY', 'DELAY', 'CAN_BUY', 'PRICE', 'WEIGHT'));
				while ($v = $ar->Fetch()) if ($v['CAN_BUY'] == 'Y' && $v['DELAY'] == 'N' && !empty($v['QUANTITY'])) $items[] = $v;
			}
		}
		$total = self::SetItem($items, true);
//		echo '<br><b>total:</b> <pre style="font-size: 12px">'.print_r($total, true).'</pre>';
//		echo '<br><b>items:</b> <pre style="font-size: 12px">'.print_r($items, true).'</pre>';

		if (defined('DELIVERY_EDOST_IGNORE_ZERO_WEIGHT') && DELIVERY_EDOST_IGNORE_ZERO_WEIGHT == 'Y') $total['weight_zero'] = false;

		if ($cart == 'Y') {
			if ($total['weight_zero']) $order['WEIGHT'] = 0;
			else if ($total['weight'] > 0) $order['WEIGHT'] = $total['weight'];

			if ($total['price'] > 0) $order['PRICE'] = $total['price'];
		}
		else {
			$s = array(
				isset($order['LENGTH']) && $order['LENGTH'] > 0 ? $order['LENGTH'] : 0,
				isset($order['WIDTH']) && $order['WIDTH'] > 0 ? $order['WIDTH'] : 0,
				isset($order['HEIGHT']) && $order['HEIGHT'] > 0 ? $order['HEIGHT'] : 0
			);
			$quantity = (isset($order['QUANTITY']) && intval($order['QUANTITY']) > 0 ? intval($order['QUANTITY']) : 1);

			$order['WEIGHT'] = $order['WEIGHT'] * $quantity;
			$order['PRICE'] = $order['PRICE'] * $quantity;

			if ($cart != 'DOUBLE') $total['package'] = array();
			else {
				if ($total['weight_zero']) $order['WEIGHT'] = 0;
				else {
					$order['WEIGHT'] += $total['weight'];
					$order['PRICE'] += $total['price'];
				}
			}

			edost_class::PackItem($total, $s, $quantity);
		}

		$p = intval($config['package']);
		if ($p <= 0 || $p > 5) $p = 1;
		$order['size'] = edost_class::PackOrder($order['WEIGHT'] > 0 ? $total['package'] : false, $p);

		$s = '';
		if (!(isset($config['send_zip']) && $config['send_zip'] == 'N') && isset($order['LOCATION_ZIP'])) {
			$s = substr($order['LOCATION_ZIP'], 0, 8);
			if ($s == '0') $s = ''; // обход ошибки битрикса (на странице редактирования заказа используется функция intval, поэтому пустой или нецифровой индекс заменяется нулем)
			if ($s == '.') $s = ''; // точка вместо индекса - обход требований битрикса обязательного ввода индекса
			else if (strlen($s) == 7 && strlen(preg_replace("/[^0-9]/i", "", $s)) == 6 && substr($s, -1) == '.') $s = substr($s, 0, 6); // точка в конце индекса - индекс определен примерно
		}
		$order['LOCATION_ZIP'] = $s;

		if (class_exists('edost_function') && method_exists('edost_function', 'BeforeCalculateRequest')) {
			$v = edost_function::BeforeCalculateRequest($order, $config);
			if ($v !== false && is_array($v)) return self::SetResult($v, $order, $config);
		}

		$weight = round($order['WEIGHT']*0.001, 3);
		if (!($weight > 0)) return self::SetResult(array('error' => empty($order['ITEMS']) ? 'no_item' : 11), $order, $config); // у товаров не задан вес

		if (!isset($order['location'])) {
			if (empty($order['LOCATION_TO'])) return self::SetResult(array('error' => 'no_location'), $order, $config); // не указано местоположение
			$order['location'] = self::GetEdostLocation($order['LOCATION_TO']);
		}
		if ($order['location'] === false) return self::SetResult(array('error' => 5), $order, $config); // в выбранное местоположение расчет доставки не производится

		// загрузка старого расчета из кэша
		$cache_id = self::GetCacheID('delivery|'.$config['id'].'|'.$order['LOCATION_FROM'].'|'.$order['LOCATION_TO'].'|'.$order['WEIGHT'].'|'.ceil($order['PRICE']).'|'.implode('|', $order['size']).'|'.$order['LOCATION_ZIP']);
		$cache = new CPHPCache();
		if ($cache->InitCache(DELIVERY_EDOST_CACHE_LIFETIME, $cache_id, '/')) {
			$r = $cache->GetVars();
			$r['cache'] = true;
			if (defined('DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE') && DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE == 'Y') if (class_exists('edost_function') && method_exists('edost_function', 'AfterCalculate')) edost_function::AfterCalculate($order, $config, $r);
			return self::SetResult($r, $order, $config);
		}

		// запрос на сервер расчета
		$ar = array();
		$ar[] = 'country='.$order['location']['country'];
		$ar[] = 'region='.$order['location']['region'];
		$ar[] = 'city='.urlencode($order['location']['city']);
		$ar[] = 'weight='.urlencode($weight);
		$ar[] = 'insurance='.urlencode($order['PRICE']);
		$ar[] = 'size='.urlencode(implode('|', $order['size']));
		if ($order['LOCATION_ZIP'] !== '') $ar[] = 'zip='.urlencode($order['LOCATION_ZIP']);
		$r = edost_class::RequestData($config['host'], $config['id'], $config['ps'], implode('&', $ar), 'delivery');

		if (class_exists('edost_function') && method_exists('edost_function', 'AfterCalculate')) edost_function::AfterCalculate($order, $config, $r);

		// сохранение расчета в лог файл
		if ($write_log) {
			$s = '';
			if (isset($r['error'])) $s = self::GetEdostError($r['error']);
			else if (!empty($r['data'])) $s = edost_class::implode2(array("\r\n", ' | ', ' : ', ' , '), $r['data']);
			self::WriteLog($order['location']['country'].', '.$order['location']['region'].', '.$GLOBALS['APPLICATION']->ConvertCharset($order['location']['city'], 'windows-1251', LANG_CHARSET).', '.$order['LOCATION_ZIP'].', '.$weight.' kg, '.$order['PRICE'].' rub, '.implode(' x ', $order['size']).' - '.date("Y.m.d H:i:s")."\r\n\r\n".$s);
		}

		if (!isset($r['error'])) {
			$cache->StartDataCache();
			$cache->EndDataCache($r);
		}

		return self::SetResult($r, $order, $config);

	}


	// установка результата в переменную класса
	public static function SetResult($data, $order, $config) {

		$k = (isset($data['sizetocm']) ? $data['sizetocm'] : 0); // коэффициент пересчета габаритов магазина в сантиметры (учитывая размерность в личном кабинете edost)
		$size = (isset($order['size']) ? $order['size'] : array(0, 0, 0));

		$data['order'] = array(
			'location' => (isset($order['location']) ? $order['location'] : false),
			'zip' => $order['LOCATION_ZIP'],
			'weight' => round($order['WEIGHT']*0.001, 3),
			'price' => $order['PRICE'],
			'size1' => ceil($size[0] * $k),
			'size2' => ceil($size[1] * $k),
			'size3' => ceil($size[2] * $k),
			'sizesum' => ceil(($size[0] + $size[1] + $size[2]) * $k),
			'config' => $config,
			'original' => $order['original'],
		);

		self::$result = $data;

		$a = true;
		foreach (self::$result_full as $v) if (!self::OrderChanged($v['order']['original'], $order['original'])) { $a = false; break; }
		if ($a) self::$result_full[] = $data;

		return $data;

	}


	// получение нулевого тарифа
	public static function GetZeroTariff($config) {

		self::GetAutomatic();

		$id = (!empty($config['param']['zero_tariff']) ?  $config['param']['zero_tariff'] : false);
		if (empty($id)) foreach (self::$automatic as $v) if ($v['code'] == 'edost:0') { $id = $v['id']; break; }

		return (!empty(self::$automatic[$id]) ? self::$automatic[$id] : false);

	}


	// получение данных автоматизированных тарифов ($delivery = true - получить ID доставок eDost без профилей)
	public static function GetAutomatic($delivery = false) {

		if (!$delivery && self::$automatic !== false) return;

		$services = \Bitrix\Sale\Delivery\Services\Table::getList(array('filter' => array('=ACTIVE' => 'Y', '=CLASS_NAME' => '\Bitrix\Sale\Delivery\Services\Automatic'.(!$delivery ? 'Profile' : ''))));
		$r = array();
		while ($v = $services->fetch())
			if ($delivery) {
				if ($v['CODE'] == 'edost') $r[] = $v['ID'];
			}
			else {
				$s = explode(':', $v['CODE']);
				if (!isset($s[1])) continue;
				$r[$v['ID']] = array('id' => $v['ID'], 'parent_id' => $v['PARENT_ID'], 'code' => $v['CODE'], 'automatic' => $s[0], 'profile' => $s[1], 'name' => $v['NAME'], 'description' => $v['DESCRIPTION'], 'sort' => $v['SORT']);
			}

		if ($delivery) return $r;

		self::$automatic = $r;

	}


	// получение профиля edost по коду или ID доставки
	public static function GetEdostProfile($id, $control = false) {

		if (empty($id)) return false;

		$r = false;

		$s = explode(':', $id);
		if ($s[0] === 'edost' && isset($s[1])) $r = $s[1];

		if ($r === false) {
			self::GetAutomatic();
			if (isset(self::$automatic[$id]) && self::$automatic[$id]['automatic'] == 'edost') $r = self::$automatic[$id]['profile'];
		}

		if ($r === false) return false;

		$tariff = ceil(intval($r) / 2);
		if ($control && ($tariff == 0 || in_array($tariff, self::$tariff_shop))) return false;

		$r = array(
			'tariff' => $tariff,
			'profile' => $r,
			'title' => self::$automatic[$id]['name'],
		);
		$r['insurance'] = ($r['tariff']*2 - $r['profile'] == 0 ? true : false);
		if ($control) {
			$sign = GetMessage('EDOST_DELIVERY_SIGN');
			$s = edost_class::ParseName(self::$automatic[$id]['name'], '', '', $sign['insurance']);
			$r['company'] = $s['company'];
			$r['name'] = $s['name'];
		}

		return $r;

	}


	// получение отгрузки
	public static function GetShipment($order) {

		$r = false;
		foreach ($order->getShipmentCollection() as $v) if (!$v->isSystem()) $r = $v;

		return $r;

	}

	// получение оплаты
	public static function GetPayment($order, $inner = false) {

		$r = false;
		if ($inner === 'new') {
			foreach ($order->getPaymentCollection() as $v) $v->delete();
			$r = $order->getPaymentCollection()->createItem();
		}
		else {
			$inner_id = \Bitrix\Sale\PaySystem\Manager::getInnerPaySystemId();
			foreach ($order->getPaymentCollection() as $v) {
				$id = $v->getPaymentSystemId();
				if ($inner && $id == $inner_id || !$inner && $id != $inner_id) { $r = $v; break; }
			}
		}

		return $r;

	}


	// загрузка параметров со страницы "Настройка печатных форм" (bitrix/admin/sale_report_edit.php)
	public static function GetPrintField($key = false) {

		if (self::$print_field != null) return ($key !== false && isset(self::$print_field[$key]) ? self::$print_field[$key] : self::$print_field);

		$r = array();

		$ar = '';
		$n = intval(COption::GetOptionInt('sale', 'reports_count'));
		if (!($n > 0)) $ar = COption::GetOptionString('sale', 'reports');
		else for ($i = 1; $i <= $n; $i++) $ar .= COption::GetOptionString('sale', 'reports'.$i);
		$shop = unserialize($ar);
//		echo '<br><b>shop:</b><pre style="font-size: 12px">'.print_r($shop, true).'</pre>';

		// реквизиты  отправителя
		$ar = array_fill_keys(array('INDEX', 'COMPANY_NAME', 'ADDRESS', 'CITY', 'PHONE', 'INN', 'KPP', 'KSCH', 'RSCH_BANK', 'RSCH_CITY', 'RSCH', 'BIK'), '');
		foreach ($ar as $k => $v) $ar[$k] = (isset($shop[$k]['TYPE']) && $shop[$k]['TYPE'] == '' ? $shop[$k]['VALUE'] : '');
		$r['shop'] = array(
			'name' => $ar['COMPANY_NAME'],
			'address' => $ar['ADDRESS'],
			'address_full' => $ar['ADDRESS'].($ar['ADDRESS'] != '' && $ar['CITY'] != '' ? ', ' : '').$ar['CITY'],
			'city' => $ar['CITY'],
			'zip' => $ar['INDEX'],
			'phone' => $ar['PHONE'],
			'inn' => $ar['INN'],
			'kpp' => $ar['KPP'],
			'ksch' => $ar['KSCH'],
			'bank' => $ar['RSCH_BANK'],
			'bank_city' => $ar['RSCH_CITY'],
			'rsch' => $ar['RSCH'],
			'bik' => $ar['BIK'],
		);

		// ключи свойств покупателя
		$ar = array_fill_keys(array('BUYER_COMPANY_NAME', 'BUYER_FIRST_NAME', 'BUYER_SECOND_NAME', 'BUYER_LAST_NAME', 'BUYER_ADDRESS', 'BUYER_CITY', 'BUYER_INDEX'), '');
		foreach ($ar as $k => $v) $ar[$k] = (isset($shop[$k]['TYPE']) && $shop[$k]['TYPE'] == 'PROPERTY' ? $shop[$k]['VALUE'] : '');
		$r['user'] = array(
			'company' => $ar['BUYER_COMPANY_NAME'],
			'first' => $ar['BUYER_FIRST_NAME'],
			'middle' => $ar['BUYER_SECOND_NAME'],
			'last' => $ar['BUYER_LAST_NAME'],
			'address' => $ar['BUYER_ADDRESS'],
			'city' => $ar['BUYER_CITY'],
			'zip' => $ar['BUYER_INDEX'],
		);
//		echo '<br><b>r:</b><pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		self::$print_field = $r;

		return ($key !== false && isset($r[$key]) ? $r[$key] : $r);

	}


	public static function GetRUB() {
		$currency = 'RUB';
		if (CCurrency::GetByID('RUR')) $currency = 'RUR';
		if (CCurrency::GetByID('RUB')) $currency = 'RUB';
		return $currency;
	}

	public static function GetProtocol() {
		return (\Bitrix\Main\Application::getInstance()->getContext()->getRequest()->isHttps() || !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ? 'https://' : 'http://');
	}

	public static function GetCacheID($s) {
		return 'sale|20.5.0|edost|'.(defined('DELIVERY_EDOST_CACHE_KEY') ? DELIVERY_EDOST_CACHE_KEY.'|' : '').$s;
	}

	public static function CommaToPoint(&$n) {
		if (!empty($n)) {
			$s = preg_replace("/[^0-9,. ]/i", "|", $n);
			$s = explode('|', $s);
			$n = str_replace(',', '.', preg_replace("/[^0-9,.]/i", "", $s[0]));
		}
	}

	public static function WriteLog($data) {
		$fp = fopen(dirname(__FILE__)."/edost.log", "a");
		fwrite($fp, "\r\n==========================================\r\n");
		fwrite($fp, $data);
		fclose($fp);
	}

}


AddEventHandler('sale', 'onSaleDeliveryHandlersBuildList', array('CDeliveryEDOST', 'Init'));


class edost_class {
	public static $version = '2.5.5';
	public static $error = false;
	public static $cod_paysystem = null;
	public static $control_key = array('id', 'flag', 'tariff', 'tracking_code', 'country', 'region', 'city', 'order_paid',  'zip', 'address_data', 'order_number', 'user_data', 'phone', 'email', 'comment', 'basket_data', 'package_data', 'batch_data');
	public static $delimiter = array(array(',', ':'), array(';', '/'));
	public static $delimiter2 = array('size' => array('x', array(0,0,0)), 'DIMENSIONS' => array('x', array(0,0,0)), 'service' => array('/', array()), 'doc' => array(';', array()), 'marking_code' => array('/', array()));
	public static $data_key = array(
		'user' => array('company', 'name', 'name_first', 'name_middle', 'name_last', 'passport', 'companytype', 'account', 'secure', 'contract', 'appointment', 'represented', 'basis', 'vat', 'inn', 'format', 'token', 'zip', 'online_balance', 'batch_format'),
		'address' => array('address', 'street', 'house_1', 'house_2', 'house_3', 'house_4', 'door_1', 'door_2', 'city2', 'id', 'code', 'city_id', 'phone', 'lunch', 'call', 'comment'),
		'basket' => array('id', 'product_id', 'name', 'quantity', 'price', 'vat', 'vat_rate', 'weight', 'size', 'set' => array('name'), 'info', 'marking_code'),
		'basket2' => array('ID', 'PRODUCT_ID', 'NAME', 'QUANTITY', 'PRICE', 'VAT', 'VAT_RATE', 'WEIGHT', 'size', 'set' => array('NAME'), 'INFO_DATA', 'marking_code'),
		'package' => array('weight', 'size', 'insurance', 'cod', 'item' => array('id', 'quantity'), 'service', 'comment', 'type'),
		'package2' => array('shipment_id', 'weight', 'size', 'insurance', 'cod', 'item' => array('id', 'quantity'), 'service', 'comment', 'type'),
		'option' => array('id', 'service' => array('id', 'value')),
		'batch_first' => array('date', 'number', 'type', 'call', 'profile_shop', 'profile_delivery'),
	);
	public static $depend = array('count', 'cod');
	public static $cod_key = array('pricecod', 'pricecod_formatted', 'pricecash', 'pricecash_formatted', 'transfer', 'transfer_formatted', 'cod_tariff', 'codplus', 'codplus_formatted', 'pricecashplus', 'pricecashplus_formatted', 'pricecod_original', 'pricecod_original_formatted', 'pricecash_original', 'pricecash_original_formatted', 'codplus_original', 'codplus_original_formatted', 'cod', 'compact_cod', 'compact_link_cod', 'compact_head_cod');
	public static $schedule_code = array(
		' ' => 'system_1', '!' => 'system_2', '#' => 'system_3', '$' => 'system_4',
		'%' => 'пн.', '&' => 'вт.', '(' => 'ср.', ')' => 'чт.', '*' => 'пт.', '+' => 'сб.', ',' => 'вс.', '-' => 'обед', '.' => 'выходной', '/' => '-',
		'0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, ':' => 10, ';' => 11, '<' => 12, '=' => 13, '>' => 14, '?' => 15, '@' => 16, 'A' => 17, 'B' => 18, 'C' => 19, 'D' => 20, 'E' => 21, 'F' => 22, 'G' => 23, 'H' => 24,
		'I' => '#без выходных', 'J' => '%/*90C0', 'K' => '%/*:0C0', 'L' => '%/*:0D0', 'M' => '-=0>0', 'N' => '%/*90B0', 'O' => '->0?0', 'P' => '%/*90D0', 'Q' => '+:0A0', 'R' => '+:0@0', 'S' => '+:0?0', 'T' => '+:0B0', 'U' => '+:0>0', 'V' => '%/*;0D0', 'W' => '%/*80D0', 'X' => '%/*;0C0', 'Y' => '%/*:0E0', 'Z' => '%/*:0B0', '[' => '+90B0', ']' => '+;0@0', '^' => '%/*80C0', '_' => '+/,:0A0', 'a' => '%/*:0C6', 'b' => ',:0A0', 'c' => '%/*90E0', 'd' => '+;0?0', 'e' => ',:0@0', 'f' => '+;0B0', 'g' => '%/*80B0', 'h' => '+90A0', 'i' => '+/,:0B0', 'j' => ',:0?0', 'k' => ',:0B0', 'l' => '+/,;0B0', 'm' => '+/,:0@0', 'n' => '+;0A0', 'o' => '%/*90A0', 'p' => '+90?0', 'q' => '+/,90B0', 'r' => '-<0<6', 's' => '+/,:0?0', 't' => '%/*86C0', 'u' => ',:0C0', 'v' => '+/,:0D0', 'w' => '%/*96B0', 'x' => '-<0=0', 'y' => '+:0C0', 'z' => ',;0A0', '{' => '%/+:0C0', '}' => '-=0=6', '~' => '+90>0', 'А' => '%/+:0D0', 'Б' => '+:0=0', 'В' => '%/*90B6', 'Г' => '%/*86D0', 'Д' => '80G0!I', 'Е' => '90E0!I', 'Ж' => 'L!i', 'З' => ':0D0!I', 'И' => '80F0!I', 'Й' => ':0E0!I', 'К' => 'K!R', 'Л' => ':0F0!I', 'М' => '#круглосуточно!I', 'Н' => '90D0!I', 'О' => '90F0!I', 'П' => '90G0!I', 'Р' => '80D0!I', 'С' => ':0C0!I', 'Т' => 'P!i', 'У' => '90C0!I', 'Ф' => 'L!Q', 'Х' => 'J!R', 'Ц' => 'А!u', 'Ч' => '80E0!I', 'Ш' => 'K!S', 'Щ' => '70G0!I', 'Ъ' => '70F0!I', 'Ы' => 'А', 'Ь' => 'K!Q', 'Э' => '80H0!I', 'Ю' => 'L!+/,:0C0', 'Я' => 'N!S', 'а' => '80C0!I', 'б' => ';0E0!I', 'в' => 'L!T', 'г' => 'J!S', 'д' => 'L!m', 'е' => '90B0!I', 'ж' => 'J!Q', 'з' => '96E6!I', 'и' => ':0G0!I', 'й' => '%/+90C0', 'к' => 'L!_', 'л' => 'K!_', 'м' => 'K!U', 'н' => 'J!U', 'о' => ';0D0!I', 'п' => 'K!T', 'р' => 'L!R', 'с' => 'K!i', 'т' => 'N!R', 'у' => 'J!q', 'ф' => '%/*80A0', 'х' => 'Y!+/,;0D0', 'ц' => 'N!M', 'ч' => 'K!m', 'ш' => 'P!+/,90C0', 'щ' => 'N!p', 'ъ' => ':0C6!I'
	);
	public static $schedule_key_original = '#';
	public static $schedule_key_enter = '!';
	public static $limit_code = array(' ' => 0, '!' => 1, '#' => 2, '$' => 3, '%' => 4, '&' => 5, '(' => 6, ')' => 7, '*' => 8, '+' => 9, ',' => 10, '-' => 11, '.' => 12, '/' => 13, '0' => 14, '1' => 15, '2' => 16, '3' => 17, '4' => 18, '5' => 19, '6' => 20, '7' => 21, '8' => 22, '9' => 23, ':' => 24, ';' => 25, '<' => 26, '=' => 27, '>' => 28, '?' => 29, '@' => 30, 'A' => 31, 'B' => 32, 'C' => 34, 'D' => 36, 'E' => 38, 'F' => 40, 'G' => 42, 'H' => 44, 'I' => 46, 'J' => 48, 'K' => 50, 'L' => 52, 'M' => 54, 'N' => 56, 'O' => 58, 'P' => 60, 'Q' => 62, 'R' => 64, 'S' => 66, 'T' => 68, 'U' => 70, 'V' => 72, 'W' => 74, 'X' => 76, 'Y' => 78, 'Z' => 80, '[' => 82, ']' => 84, '^' => 86, '_' => 88, 'a' => 90, 'b' => 92, 'c' => 94, 'd' => 96, 'e' => 98, 'f' => 100, 'g' => 105, 'h' => 110, 'i' => 115, 'j' => 120, 'k' => 125, 'l' => 130, 'm' => 135, 'n' => 140, 'o' => 145, 'p' => 150, 'q' => 155, 'r' => 160, 's' => 165, 't' => 170, 'u' => 175, 'v' => 180, 'w' => 185, 'x' => 190, 'y' => 195, 'z' => 200, '{' => 205, '}' => 210, '~' => 215, 'А' => 220, 'Б' => 225, 'В' => 230, 'Г' => 235, 'Д' => 240, 'Е' => 245, 'Ж' => 250, 'З' => 260, 'И' => 270, 'Й' => 280, 'К' => 290, 'Л' => 300, 'М' => 310, 'Н' => 320, 'О' => 330, 'П' => 340, 'Р' => 350, 'С' => 360, 'Т' => 370, 'У' => 380, 'Ф' => 390, 'Х' => 400, 'Ц' => 410, 'Ч' => 420, 'Ш' => 430, 'Щ' => 440, 'Ъ' => 450, 'Ы' => 460, 'Ь' => 470, 'Э' => 480, 'Ю' => 490, 'Я' => 500, 'а' => 600, 'б' => 700, 'в' => 800, 'г' => 900, 'д' => 1000, 'е' => 1100, 'ж' => 1200, 'з' => 1300, 'и' => 1400, 'й' => 1500, 'к' => 1600, 'л' => 1700, 'м' => 1800, 'н' => 1900, 'о' => 2000, 'п' => 2100, 'р' => 2200, 'с' => 2300, 'т' => 2400, 'у' => 2500, 'ф' => 2600, 'х' => 2700, 'ц' => 2800, 'ч' => 2900, 'ш' => 3000, 'щ' => 4000, 'ъ' => 5000, 'ы' => 6000, 'ь' => 7000, 'э' => 8000, 'ю' => 9000, 'я' => 10000);
	public static $limit_code2 = array(' ' => 0, '!' => 1, '#' => 2, '$' => 3, '%' => 4, '&' => 5, '(' => 6, ')' => 7, '*' => 8, '+' => 9, ',' => 10, '-' => 11, '.' => 12, '/' => 13, '0' => 14, '1' => 15, '2' => 16, '3' => 17, '4' => 18, '5' => 19, '6' => 20, '7' => 21, '8' => 22, '9' => 23, ':' => 24, ';' => 25, '<' => 26, '=' => 27, '>' => 28, '?' => 29, '@' => 30, 'A' => 31, 'B' => 32, 'C' => 33, 'D' => 34, 'E' => 35, 'F' => 36, 'G' => 37, 'H' => 38, 'I' => 39, 'J' => 40, 'K' => 41, 'L' => 42, 'M' => 43, 'N' => 44, 'O' => 45, 'P' => 46, 'Q' => 47, 'R' => 48, 'S' => 49, 'T' => 50, 'U' => 51, 'V' => 52, 'W' => 53, 'X' => 54, 'Y' => 55, 'Z' => 56, '[' => 57, ']' => 58, '^' => 59, '_' => 60, 'a' => 61, 'b' => 62, 'c' => 63, 'd' => 64, 'e' => 65, 'f' => 66, 'g' => 67, 'h' => 68, 'i' => 69, 'j' => 70, 'k' => 71, 'l' => 72, 'm' => 73, 'n' => 74, 'o' => 75, 'p' => 76, 'q' => 77, 'r' => 78, 's' => 79, 't' => 80, 'u' => 81, 'v' => 82, 'w' => 83, 'x' => 84, 'y' => 85, 'z' => 86, '{' => 87, '}' => 88, '~' => 89, 'А' => 90, 'Б' => 91, 'В' => 92, 'Г' => 93, 'Д' => 94, 'Е' => 95, 'Ж' => 96, 'З' => 97, 'И' => 98, 'Й' => 99, 'К' => 100, 'Л' => 101, 'М' => 102, 'Н' => 103, 'О' => 104, 'П' => 105, 'Р' => 106, 'С' => 107, 'Т' => 108, 'У' => 109, 'Ф' => 110, 'Х' => 111, 'Ц' => 112, 'Ч' => 113, 'Ш' => 114, 'Щ' => 115, 'Ъ' => 116, 'Ы' => 117, 'Ь' => 118, 'Э' => 119, 'Ю' => 120, 'Я' => 121, 'а' => 122, 'б' => 123, 'в' => 124, 'г' => 125, 'д' => 126, 'е' => 127, 'ж' => 128, 'з' => 129, 'и' => 130, 'й' => 131, 'к' => 132, 'л' => 133, 'м' => 134, 'н' => 135, 'о' => 136, 'п' => 137, 'р' => 138, 'с' => 139, 'т' => 140, 'у' => 141, 'ф' => 142, 'х' => 143, 'ц' => 144, 'ч' => 145, 'ш' => 146, 'щ' => 147, 'ъ' => 148, 'ы' => 149, 'ь' => 150, 'э' => 151, 'ю' => 152, 'я' => 153);
	public static $limit_code_string = array('%' => ',@KK', '&' => 'C!Я', '(' => 'K!Я', ')' => 'f!Я', '*' => 'A vK', '+' => '1FNQ', ',' => '1BGH', '-' => 'A vf', '.' => '1CFQ', '/' => 'A v1', '0' => '1DFQ', '1' => '1DGH', '2' => '1 vK', '3' => '1CFI', '4' => '177G', '5' => '1NNQ', '6' => '1DDP', '7' => '17EG', '8' => '1-GH', '9' => '1DDQ', ':' => '@zzzzf', ';' => ',pppp1', '<' => ',fffv1', '=' => '1zzzzf', '>' => '@pppzf', '?' => '6zzzzf', '@' => '   Ж');
	public static $limit_key = array('weight_to', 'size1', 'size2', 'size3', 'sizesum', 'price', 'weight_from', 'tariff');


	public static function RequestError($code, $msg, $file, $line) {
		self::$error = true;
		return true;
	}

	// запрос на сервер edost
	public static function RequestData($url, $id, $ps, $post, $type) {

		if ($type != 'print') {
			if ($id === '' || $ps === '') return array('error' => 12);
			if (intval($id) == 0) return array('error' => 3);
		}
		if ($post === '') return array('error' => 4);

		$api2 = (in_array($type, array('delivery', 'control', 'detail', 'print')) ? true : false);
		$auto = ($url == '' ? true : false);
		$server_default = ($api2 ? DELIVERY_EDOST_SERVER : DELIVERY_EDOST_SERVER_ZIP);
		$server = ($auto ? COption::GetOptionString('edost.delivery', $api2 ? 'server' : 'server_zip', $server_default) : $url);
		if ($server == '') $server = $server_default;
		$url = 'http://'.$server.'/'.($api2 ? 'api2.php' : 'api.php');

		if ($type != 'print') $post = 'id='.$id.'&p='.$ps.'&version='.self::$version.'&'.$post;
		$parse_url = parse_url($url);
		$path = $parse_url['path'];
		$host = $parse_url['host'];

		self::$error = false;
		set_error_handler(array('edost_class', 'RequestError'));

		$fp = fsockopen($host, 80, $errno, $errstr, 4); // 4 - максимальное время запроса
		restore_error_handler();
//		echo '<br>error: '.($fp ? 'fsockopen TRUE' : 'fsockopen FALSE').' | '.(self::$error ? 'self::error TRUE' : 'self::error FALSE').' | '.$errno.' - '.$errstr;

		if ($errno == 13 || self::$error || !$fp) $r = array('error' => 14); // настройки сервера не позволяют отправить запрос на расчет
		else {
			$out =	"POST ".$path." HTTP/1.0\r\n".
					"Host: ".$host."\r\n".
					"Referer: ".$url."\r\n".
					"Content-Type: application/x-www-form-urlencoded\r\n".
					"Content-Length: ".strlen($post)."\r\n\r\n".
					$post."\r\n\r\n";

			fputs($fp, $out);
			$r = '';
			while ($gets = fgets($fp, 512)) $r .= $gets;
			fclose($fp);

//			echo '<br>----------------<br>'.$out.'<br>----------------'; // !!!!!
//			echo '<br><br>response from server (original): ----------------<br>'.$GLOBALS['APPLICATION']->ConvertCharset($r, 'windows-1251', LANG_CHARSET).'<br>----------------';
//			die();
//			if (!is_array($_SESSION['EDOST']['request'])) $_SESSION['EDOST']['request'] = array();
//			$_SESSION['EDOST']['develop'][] = array('out' => $out, 'response' => $GLOBALS['APPLICATION']->ConvertCharset($r, 'windows-1251', LANG_CHARSET));

			$r = stristr($r, 'api_data:', false);
			if ($r === false) $r = array('error' => 8); // сервер расчета не отвечает
			else {
				$r = substr($r, 9);
				if (!in_array($type, array('develop', 'print'))) $r = self::ParseData($r, $type);
			}

			// принудительное переключение на загрузку скриптов с сервера eDost или с сервера магазина
			if (!isset($r['error']) && $type == 'delivery') {
				$script = COption::GetOptionString('edost.delivery', 'script', '');
				if (!empty($r['script'])) {
					if (in_array($r['script'], array('Y', 'N')) && $r['script'] != $script) COption::SetOptionString('edost.delivery', 'script', $r['script']);
				}
				else {
					if (!empty($script)) COption::SetOptionString('edost.delivery', 'script', '');
				}
			}
		}
//		$_SESSION['EDOST']['develop'][] = $r; // !!!!!
//		echo '<br><b>request result:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>'; // !!!!!

		// переключение на второй стандартный сервер, если первый не отвечает
		if (isset($r['error']) && in_array($r['error'], array(8, 14)) && $auto) {
			$server_new = '';
			$ar = array($server_default, DELIVERY_EDOST_SERVER_RESERVE, DELIVERY_EDOST_SERVER_RESERVE2);
			for ($i = 0; $i < count($ar)-1; $i++) if ($ar[$i] == $server) { $server_new = $ar[$i+1]; break; }
			if ($server_new == '') $server_new = $server_default;
			COption::SetOptionString('edost.delivery', ($api2 ? 'server' : 'server_zip'), $server_new);
		}

		return $r;

	}


	public static function SetPropsCode($props) {

		$r = array();
		$ar = CSaleOrderProps::GetList(array(), array(), false, false, array('ID', 'CODE', 'IS_PHONE'));
		while ($v = $ar->GetNext()) if (array_key_exists($v['ID'], $props)) {
			$type = '';
			if (!empty($v['IS_PHONE']) && $v['IS_PHONE'] == 'Y') $type = 'tel';
			$r[$v['CODE']] = array('id' => $v['ID'], 'value' => isset($props[$v['ID']]) ? $props[$v['ID']] : '', 'type' => $type);
		}
		return $r;

	}


	// загрузка свойств заказа + оплаты ($param: 'order' - передан объект заказа,  'no_payment' - не загружать оплаты и не определять наложку,  'no_location' - не загружать местоположение,  'shipment' - поиск по ид отгрузки,  'shipment_all' - загружать отгузки с любыми модулями доставки, 'office_link' - название пункта выдачи ссылкой,  'field' - заполнить поля для детальной информации по контролю)
	public static function GetProps($id, $param = array(), $shipment_id = false) {

		if (empty($id)) return;

		$r = array();
		$order = false;
		$field = array();

		if (in_array('shipment', $param)) {
			// поиск заказа по id отгрузки + загрузка дополнительных полей
			$ar = edost_class::GetShipmentData($id, array('control' => false, 'edost' => in_array('shipment_all', $param) ? false : true));
			foreach ($ar as $v) {
				$order = \Bitrix\Sale\Order::load($v['order_id']);
				$r += array(
					'delivery_id' => $v['delivery_id'],
					'tracking_code' => $v['tracking_code'],
					'allow_delivery' => $v['allow_delivery'],
				);
				break;
			}
		}
		else if (in_array('order', $param)) $order = $id;
		else $order = \Bitrix\Sale\Order::load($id);

		if (empty($order)) return false;

		$r['order_paid'] = $order->isPaid();

		// свойства заказа
		$code_payer = 'FIO';
		$code_phone = 'PHONE';
		$code_email = 'EMAIL';
		$code_address = 'ADDRESS';
		$prop = array();
		$ar = $order->getPropertyCollection();
		foreach ($ar->getGroups() as $v) foreach ($ar->getGroupProperties($v['ID']) as $v2) {
			$p = $v2->getProperty();
			$prop[$p['CODE']] = array(
				'value' => $v2->getValue(),
				'id' => $p['ID'],
//				'is_payer' => ($p['IS_PAYER'] == 'Y' ? true : false),
//				'is_phone' => ($p['IS_PHONE'] == 'Y' ? true : false),
//				'is_address' => ($p['IS_ADDRESS'] == 'Y' ? true : false),
			);
			if ($p['IS_PAYER'] == 'Y') $code_payer = $p['CODE'];
			if ($p['IS_PHONE'] == 'Y') $code_phone = $p['CODE'];
			if ($p['IS_ADDRESS'] == 'Y') $code_address = $p['CODE'];
			if ($p['IS_EMAIL'] == 'Y') $code_email = $p['CODE'];
		}
		$r['prop'] = $prop;
//		echo '<br><b>prop:</b> <pre style="font-size: 12px">'.print_r($prop, true).'</pre>';

		$user_field = CDeliveryEDOST::GetPrintField('user');

		$s = '';
		if (!empty($prop['COMPANY'])) $s = $prop['COMPANY']['value'];
		else if (!empty($prop[$user_field['company']])) $s = $prop[$user_field['company']]['value'];
		$r['company'] = $s;

		$r['passport'] = (!empty($prop['PASSPORT']) ? $prop['PASSPORT']['value'] : '');
//		if (isset($prop['PASSPORT'])) $r['passport_enabled'] = true;

		if (isset($prop['PACKAGE'])) {
			$p = self::UnPackDataArray($prop['PACKAGE']['value'], 'package2');
			if (!empty($p) && !empty($shipment_id)) foreach ($p as $k => $v) if ($v['shipment_id'] != $shipment_id) unset($p[$k]);
			$r['package'] = $p;
//			echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($p, true).'</pre>';
		}

		$s = '';
		if (!empty($prop[$code_payer])) $s = $prop[$code_payer]['value'];
		else if (!empty($prop['CONTACT_PERSON'])) $s = $prop['CONTACT_PERSON']['value'];
		else {
			$key = array('first', 'middle', 'last');
			$a = true;
			foreach ($key as $v) if (empty($user_field[$v]) || !isset($prop[$user_field[$v]])) { $a = false; break; }
			if ($a) {
				foreach ($key as $v) $r['name_part'][$v] = $prop[$user_field[$v]]['value'];
				$s = $r['name_part']['last'].' '.$r['name_part']['first'].' '.$r['name_part']['middle'];
			}
		}
		$r['name'] = $s;

		$r['phone'] = (!empty($prop[$code_phone]) ? $prop[$code_phone]['value'] : '');
		$r['email'] = (!empty($prop[$code_email]) ? $prop[$code_email]['value'] : '');
		$r['zip'] = (!empty($prop['ZIP']) ? $prop['ZIP']['value'] : '');

		$address = (!empty($prop['ADDRESS']) ? $prop['ADDRESS']['value'] : '');
		$office = self::ParseOfficeAddress($address);

		if (!in_array('no_location', $param)) {
			$location_name = $location_name2 = '';
			$location_code = (!empty($prop['LOCATION']) ? $prop['LOCATION']['value'] : 0);
			$location_data = false;
			$location_edost = CDeliveryEDOST::GetEdostLocation($location_code);
			if (!empty($location_edost)) {
				$city = (!empty($prop['CITY']) ? $prop['CITY']['value'] : '');
				$location = array('country' => (!empty($location_edost['country_name']) ? $location_edost['country_name'] : ''));
				$location_data = array('country' => $location_edost['country'], 'region' => $location_edost['region'], 'city' => $location_edost['city']);
				if (CModule::IncludeModule('edost.locations') && method_exists('CLocationsEDOST', 'ParseAddress')) {
					if ($address != '') {
						$s = CLocationsEDOST::ParseAddress($address);
						$r['address_part'] = $s;
						if (empty($s['city2'])) $r['address_part']['city2'] = $city;
						else {
							$city = $s['city2'];
							$s = explode('; ', $address);
							$address = $s[0];
						}
					}
					$location = CLocationsEDOST::GetData(CSaleLocation::getLocationIDbyCODE($location_code), $city, true);
				}
				if (!empty($city)) $location_data['city'] = $GLOBALS['APPLICATION']->ConvertCharset($city, LANG_CHARSET, 'windows-1251');
				if (isset($r['address_part']['city2'])) unset($r['address_part']['city2']);

				$s = $s2 = '';
				if (!empty($location)) {
					$location['region'] = (!empty($location_edost['region_name']) ? $location_edost['region_name'] : '');
					$location['city'] = (!empty($location_edost['bitrix']['city']) ? $location_edost['bitrix']['city'] : $city);
					if ($location_data['country'] == 0) $location['show_country'] = false;

					$s = $s2 = $location['city'];
					if (!empty($location['region'])) {
						if ($s == '') $s = $s2 = $location['region'];
						else {
							if (!in_array($location_data['city'], CDeliveryEDOST::$no_region_city)) $s .= ' ('.$location['region'].')';
							$s2 .= ', '.$location['region'];
						}
					}
					if ($s == '' || !empty($location['country']) && !empty($location['show_country'])) {
						$s .= ($s != '' ? ', ' : '').$location['country'];
						$s2 .= ($s2 != '' ? ', ' : '').$location['country'];
					}
				}
				$location_name = $s;
				$location_name2 = $s2;
			}
			$r += array(
				'location_name' => $location_name, // полное название
				'location_code' => $location_code, // код
				'location_data' => $location_data, // данные для модуля eDost
			);

			$r['address_full'] = $address.(empty($office) ? ', '.$location_name2 : '');
		}
		$r['address'] = $address; // адрес с удаленным городом (если нет 'no_location') или оригинальная запись по офису

		// определние платежной системы + наложенного платежа + загрузка списка оплат
		if (!in_array('no_payment', $param)) {
			$ar = $order->getPaymentCollection();
			if (count($ar) == 1) {
				$payment = $ar->rewind();
				if (!empty($payment)) {
					if (in_array('order_payment', $param)) {
						$r['payment_id'] = $payment->getId();
						$payment_cod = self::GetCodPaySystem();
						$paysystem_id = $payment->getPaymentSystemId();
						if (!empty($paysystem_id) && in_array($paysystem_id, $payment_cod)) {
							$r['cod'] = true;
							if ($payment->isPaid()) $r['paid'] = true;
						}
					}
					else {
						$ar = \Bitrix\Sale\PaySystem\Manager::getListWithRestrictions($payment, \Bitrix\Sale\Services\PaySystem\Restrictions\Manager::MODE_MANAGER);
						if (!empty($ar)) {
							$paysystem_list = array();
							$paysystem_id = $payment->getPaymentSystemId();
							$cod = false;
							$a = false;
							foreach ($ar as $v) {
								$s = array('ID' => $v['ID'], 'NAME' => str_replace(array('<', '>'), array('&lt;', '&gt;'), $v['NAME']));
								if ($v['ID'] == $paysystem_id) $s['checked'] = true;
								if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') {
							    	$a = true;
							    	$s['cod'] = true;
							    	if (!empty($s['checked'])) $cod = true;
							    }
							    $paysystem_list[] = $s;
							}
		                    if ($a) {
								if ($cod) {
									$r['cod'] = true;
									if ($payment->isPaid()) $r['paid'] = true;
								}
								$r['paysystem_list'] = $paysystem_list;
								$r['payment_id'] = $payment->getId();
	//							$r['paysystem_name'] = $payment->getPaymentSystemName();
		                    }
						}
					}
				}
			}
		}

		// форматирование адреса под офис
		if (!empty($office)) {
			if (class_exists('edost_function') && method_exists('edost_function', 'AfterGetOrderOffice')) edost_function::AfterGetOrderOffice($order, $office);

			$r['office'] = $office;
			if (in_array($office['type'], CDeliveryEDOST::$postamat)) $r['postamat'] = true;

			if (!empty($office['address_formatted'])) $s = $office['address_formatted'];
			else {
				$sign = GetMessage('EDOST_DELIVERY_SIGN');
				$protocol = CDeliveryEDOST::GetProtocol();
				$img_path = $protocol.'edostimg.ru/img/site';
				$link = (in_array('office_link', $param) ? true : false);

				$s = $office['head'];

				$s2 = '';
				if (!isset($office['detailed']) || $office['detailed'] !== 'N') $s2 = ' text-decoration: none;" href="'.edost_class::GetOfficeLink($office).'" target="_blank"';

				$code_head = '';
				foreach ($sign['code_head'] as $v) if (is_array($v) && isset($v[1]) && strpos($s, $v[0]) === 0) { $code_head = $v[1]; break; }

				if ($link) {
					$s = (!empty($s2) ? '<a style="font-weight: bold; '.$s2.'>'.$s.'</a>' : '<b>'.$s.'</b>');
					$s .= ' <img class="edost_control_button_new_active" style="vertical-align: middle;" src="'.$img_path.'/control_show.png" border="0" onclick="edost_ShowDetail(this, \'address_show\')"><div style="display: none;">';
				}
				else $s = '<b>'.$s.'</b>'.(!empty($s2) ? ' (<a style="'.$s2.'>'.$sign['map'].'</a>)' : '').'<br>';

				$s .= $office['address'];
				if (!empty($office['tel'])) $s .= '<br>'.$office['tel'];
				if (!empty($office['schedule'])) $s .= '<br>'.str_replace(', ', '<br>', $office['schedule']);
				if (!empty($code_head) && !empty($office['code']) && !in_array($office['code'], array('S', 'T'))) {
					if ($link) $field = array(array('name' => $sign['code_head']['code'].$code_head, 'value' => $office['code'], 'admin' => true, 'bold' => true));
					else $s .= '<br>'.$sign['code_head']['code2'].$code_head.': '.$office['code'];
				}
				if ($link) $s .= '</div>';
			}

			$address = $s;
		}
		$r['address_formatted'] = $address;

		// поля для детальной информации по контролю
		if (in_array('field', $param)) {
			$ar = array();
			$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
			if (!empty($r['tracking_code'])) $ar[] = array('name' => $control_sign['tracking_head'], 'value' => $r['tracking_code'], 'admin' => true, 'bold' => true);
			if (!empty($r['phone'])) $ar[] = array('name' => $control_sign['phone_head'], 'value' => $r['phone'], 'admin' => true, 'bold' => true);
			if (!empty($r['address_formatted'])) $ar[] = array('name' => $control_sign['address_head'], 'value' => $r['address_formatted'], 'admin' => true, 'bold' => empty($office) ? true : false);
			$r['field'] = array_merge($ar, $field);
		}

//		echo '<br><b>props</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// получение данных отгрузок с возможностью контроля ($shipment - элемент/массив объектов 'shipment' или id отгрузок,  $user_id - если указано, отгрузки фильтруются по пользователю, только при id отгрузок)
	public static function GetShipmentData($shipment, $param = array()) {

		$register_tariff = edost_class::GetRegisterTariff();

		$day = (!empty($param['day']) ? $param['day'] : false);
		if ($day) $shipment = array(); else if (empty($shipment)) return false;

		$r = array();
		$id = array();
		if (!is_array($shipment)) $shipment = array($shipment);

		foreach ($shipment as $k => $v) if (!is_object($v)) {
			$v = intval($v);
			if ($v == 0) continue;
			$id[] = $v;
			unset($shipment[$k]);
		}

		// загрузка отгрузок по id
		if ($day || !empty($id)) {
			$filter = array('!SYSTEM' => 'Y');

			if ($day) {
				$date = new \Bitrix\Main\Type\DateTime;
				$filter['>=ORDER.DATE_INSERT'] = $date->add('-'.$day.' days');
				$filter['=ALLOW_DELIVERY'] = 'Y';
				$filter['!CANCELED'] = 'Y';
				$filter['!ORDER.CANCELED'] = 'Y';
				$filter['=TRACKING_NUMBER'] = ''; // закомментировать для тестирования !!!!!
			}
			else if (!empty($param['order'])) $filter['=ORDER.ID'] = $id;
			else $filter['=ID'] = $id;

			if (!empty($param['user_id'])) $filter['=ORDER.USER_ID'] = $param['user_id'];

			$ar = \Bitrix\Sale\Internals\ShipmentTable::getList(array(
				'select' => array('ID', 'ORDER.LID', 'ORDER.PRICE', 'ORDER.CURRENCY', 'ORDER.SUM_PAID', 'ORDER.ACCOUNT_NUMBER', 'ORDER.DATE_INSERT', 'ORDER.DATE_STATUS', 'ORDER.STATUS_ID', 'ORDER.PAYED', 'ORDER_ID', 'DELIVERY_ID', 'ACCOUNT_NUMBER', 'STATUS_ID', 'ALLOW_DELIVERY', 'DEDUCTED', 'CANCELED', 'TRACKING_NUMBER', 'TRACKING_STATUS', 'TRACKING_DESCRIPTION', 'DELIVERY_NAME', 'COMMENTS', 'ORDER.COMMENTS', 'CURRENCY', 'PRICE_DELIVERY', 'ORDER.CANCELED', 'ORDER.USER_DESCRIPTION'),
				'filter' => $filter,
				'limit' => 10000,
			));
			while ($v = $ar->fetch()) {
				$s = array('ALLOW_DELIVERY', 'DEDUCTED', 'CANCELED', 'SALE_INTERNALS_SHIPMENT_ORDER_PAYED', 'SALE_INTERNALS_SHIPMENT_ORDER_CANCELED');
				foreach ($s as $k) $v[$k] = ($v[$k] == 'Y' ? true : false);
				$shipment[] = $v;
			}
		}

		foreach ($shipment as $k => $v) {
			$o = (is_object($v) ? true : false);
			$delivery_id = ($o ? $v->getDeliveryId() : $v['DELIVERY_ID']);

			$tariff = array();
			if (!isset($param['edost']) || $param['edost']) {
				$tariff = CDeliveryEDOST::GetEdostProfile($delivery_id, isset($param['control']) ? $param['control'] : true);
				if ($tariff === false) continue;
			}

			// добавление кода компании доставки (для оформления)
			if (isset($tariff['tariff']) && isset($register_tariff[$tariff['tariff']])) $tariff['company_id'] = $register_tariff[$tariff['tariff']];

			if ($o) $order = $v->getCollection()->getOrder();

			$id = ($o ? $v->getId() : $v['ID']);
			$r[$id] = $tariff + array(
				'id' => $id,
				'tracking_code' => ($o ? trim($v->getField('TRACKING_NUMBER')) : trim($v['TRACKING_NUMBER'])),
				'delivery_id' => $delivery_id,
				'account_number' => ($o ? $v->getField('ACCOUNT_NUMBER') : $v['ACCOUNT_NUMBER']),

				'delivery_currency' => ($o ? $order->getField('CURRENCY') : $v['CURRENCY']),
				'delivery_price' => ($o ? $v->getField('PRICE_DELIVERY') : $v['PRICE_DELIVERY']),
//				'delivery_price' => ($o ? $order->getField('PRICE_DELIVERY') : $v['PRICE_DELIVERY']), // загрузка стоимости доставки из заказа (старый вариант)

				'site_id' => ($o ? $order->getSiteId() : $v['SALE_INTERNALS_SHIPMENT_ORDER_LID']),
				'order_id' => ($o ? $order->getId() : $v['ORDER_ID']),
				'order_number' => ($o ? $order->getField('ACCOUNT_NUMBER') : $v['SALE_INTERNALS_SHIPMENT_ORDER_ACCOUNT_NUMBER']),
				'order_paid' => ($o ? $order->isPaid() : $v['SALE_INTERNALS_SHIPMENT_ORDER_PAYED']),
				'order_price' => ($o ? $order->getField('PRICE') : $v['SALE_INTERNALS_SHIPMENT_ORDER_PRICE']),
				'order_currency' => ($o ? $order->getField('CURRENCY') : $v['SALE_INTERNALS_SHIPMENT_ORDER_CURRENCY']),
				'order_sum_paid' => ($o ? $order->getField('SUM_PAID') : $v['SALE_INTERNALS_SHIPMENT_ORDER_SUM_PAID']),
				'order_status' => ($o ? $order->getField('STATUS_ID') : $v['SALE_INTERNALS_SHIPMENT_ORDER_STATUS_ID']),

				'order_canceled' => ($o ? $order->getField('CANCELED') : $v['SALE_INTERNALS_SHIPMENT_ORDER_CANCELED']),
				'order_date' => ($o ? $order->getField('DATE_INSERT') : $v['SALE_INTERNALS_SHIPMENT_ORDER_DATE_INSERT']),
				'order_status_date' => ($o ? $order->getField('DATE_STATUS') : $v['SALE_INTERNALS_SHIPMENT_ORDER_DATE_STATUS']),
				'order_user_description' => ($o ? $order->getField('USER_DESCRIPTION') : $v['SALE_INTERNALS_SHIPMENT_ORDER_USER_DESCRIPTION']),
//				'order_comments' => ($o ? $order->getField('COMMENTS') : $v['SALE_INTERNALS_SHIPMENT_ORDER_COMMENTS']),
//				'location_id' => ($o ? $order->getDeliveryLocation() : ''),

				'allow_delivery' => ($o ? $v->isAllowDelivery() : $v['ALLOW_DELIVERY']),
				'deducted' => ($o ? $v->isShipped() : $v['DEDUCTED']), // отгружен
				'canceled' => ($o ? $v->isCanceled() : $v['CANCELED']),
//				'comments' => ($o ? '' : $v['COMMENTS']),
			);
			if ($o) $r[$id]['order'] = $order;
		}

//		echo '<br><b>GetShipmentData:</b> <pre style="font-size: 12px">'.print_r($r[470], true).'</pre>';

		return $r;

	}


	// определение наложенного платежа и статуса оплаты (для всех заказов сразу, чтобы не загружать каждую оплату по отдельности)
	public static function AddPaymentData(&$data) {

		$payment_cod = self::GetCodPaySystem();

		$ar = array();
		foreach ($data as $k => $v) $ar[$v['order_id']] = $v['order_id'];
		$ar = \Bitrix\Sale\Internals\PaymentTable::getList(array('select' => array('ID', 'ORDER_ID', 'PAY_SYSTEM_ID', 'PAID', 'SUM', 'CURRENCY', 'PAY_SYSTEM.NAME'), 'filter' => array('ORDER_ID' => $ar)));
		$ar = $ar->fetchAll();
		foreach ($data as $k => $v) {
			$n = $id = 0;
			$paid = false;
			foreach ($ar as $v2) if (!empty($v2['PAY_SYSTEM_ID']) && $v2['ORDER_ID'] == $v['order_id']) {
//				echo '<br><b>PaymentTable:</b> <pre style="font-size: 12px">'.print_r($v2, true).'</pre>';
				$p = array(
					'id' => $v2['PAY_SYSTEM_ID'],
					'name' => $v2['SALE_INTERNALS_PAYMENT_PAY_SYSTEM_NAME'],
					'paid' => ($v2['PAID'] == 'Y' ? true : false),
					'currency' => $v2['CURRENCY'],
				);
				$p += self::GetPrice('sum', $v2['SUM'], $v2['CURRENCY'], '', false);

				if (in_array($v2['PAY_SYSTEM_ID'], $payment_cod)) {
					$n++;
					$id = $v2['ID'];
					if ($v2['PAID'] == 'Y') $paid = true;
					$p['cod'] = true;
				}

				$data[$k]['payment'][$v2['ID']] = $p;
			}
			if ($n == 1) {
				// в заказе допускается только одна оплата с наложенным платежом
				$data[$k]['cod'] = $id;
				if ($paid) $data[$k]['cod_paid'] = true;
			}
		}

	}


	// добавление товаров
	public static function AddBasketData(&$data) {

		if (empty($data)) return;

		$base_currency = CDeliveryEDOST::GetRUB();

		$basket = array();
		$ar = \Bitrix\Sale\Internals\ShipmentItemTable::getList(array('filter' => array('ORDER_DELIVERY_ID' => array_keys($data)), 'select' => array('BASKET_ID', 'ORDER_DELIVERY_ID')));
		while ($v = $ar->fetch()) $basket[$v['BASKET_ID']] = $v['ORDER_DELIVERY_ID'];

		$ar = \Bitrix\Sale\Basket::getList(array('filter' => array('ID' => array_keys($basket))));
		while ($v = $ar->fetch()) if ($v['CAN_BUY'] == 'Y' && $v['DELAY'] == 'N' && !empty($v['QUANTITY'])) $data[$basket[$v['ID']]]['basket'][$v['ID']] = $v;
//		echo '<br><b>basket:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

		$prop = $marking = array();
		if (CModule::IncludeModule('iblock')) {
			$s = $s2 = array();
			foreach ($data as $k => $v) if (!empty($v['basket'])) foreach ($v['basket'] as $k2 => $v2) {
				$s[$v2['PRODUCT_ID']] = $v2['PRODUCT_ID'];
//				if (!empty($v2['MARKING_CODE_GROUP'])) $marking[$k][$k2] = $v2['ID'];
			}
			if (!empty($s)) {
				$ar = CIBlockElement::GetList(array(), array('ID' => $s), false, false, array('ID', 'LID', 'PROPERTY_ARTNUMBER'));
				while ($v = $ar->fetch()) $prop[$v['ID'].'_'.$v['LID']] = $v;
			}
		}
//		echo '<br><b>prop:</b><pre style="font-size: 12px">'.print_r($prop, true).'</pre>';
//		echo '<br><b>marking:</b><pre style="font-size: 12px">'.print_r($marking, true).'</pre>';

		// расчет стоимости товаров + распределение товаров из комплекта
		foreach ($data as $k => $v) {
			$price = 0;
			$basket_count = 0;

			if (!empty($v['basket'])) {
				CDeliveryEDOST::SetItem($v['basket']);
				foreach ($v['basket'] as $k2 => $v2) {
					$v2['package_weight'] = round($v2['WEIGHT']/1000, 3);

					// поля для окна "распределение по местам" (выводятся только для информации)
					$s = $v2['size'];
					foreach ($s as $k3 => $v3) $s[$k3] = round($v3/10, 1); // габариты в магазине могут быть НЕ в миллиметрах !!!
					$v2['package_size'] = implode('x', $s);
					$v2['package_volume'] = round($s[0]*$s[1]*$s[2]/10000)/100;

					$v2['QUANTITY'] = round($v2['QUANTITY']*1000)/1000;
					$v2['WEIGHT'] = round($v2['WEIGHT']);

					$v2['PRICE'] = round(edost_class::GetPrice('value', $v2['PRICE'], $v2['CURRENCY'], $base_currency, false)*100)/100; // в 'PRICE' содержится итоговая цена (если в настройках товара не стоит галочка "НДС включен в цену", тогда автоматом прибавляется НДС)
					$v2['VAT_RATE'] = ($v2['VAT_INCLUDED'] == 'Y' ? round($v2['VAT_RATE']*100) : ''); // у всех товаров в заказе 'VAT_INCLUDED' == 'Y', процент НДС содержится в 'VAT_RATE', нет отметки для различия "0% НДС" и "Без НДС" (хранится просто нулевая ставка)

					$key = $v2['PRODUCT_ID'].'_'.$v2['LID'];
					$v2['article'] = (!empty($prop[$key]['PROPERTY_ARTNUMBER_VALUE']) ? $prop[$key]['PROPERTY_ARTNUMBER_VALUE'] : '');

					$v['basket'][$k2] = $v2;
				}

				foreach ($v['basket'] as $k2 => $v2) {
					if (empty($v2['TYPE'])) $basket_count += $v2['QUANTITY'];

					if (empty($v2['TYPE']) && !empty($v2['SET_PARENT_ID']) && isset($v['basket'][$v2['SET_PARENT_ID']])) {
						if (empty($v['basket'][$v2['SET_PARENT_ID']]['set'])) $v['basket'][$v2['SET_PARENT_ID']]['set'] = array();
						$v2['ID'] = $v2['SET_PARENT_ID'].'_'.$v2['ID'];
						$v['basket'][$v2['SET_PARENT_ID']]['set'][$v2['ID']] = $v2;
						unset($v['basket'][$k2]);
					}
					else {
						$v['basket'][$k2] += self::GetPrice('price_total', $v2['PRICE']*$v2['QUANTITY'], $base_currency, $v['order_currency'], false);
						$price += $v['basket'][$k2]['price_total'];
					}
				}

				// изменение стоимости товаров в комплекте, чтобы в сумме они были равны стоимости всего комплекта
				foreach ($v['basket'] as $k2 => $v2) if (!empty($v2['set'])) {
					$p = $v2['PRICE']*$v2['QUANTITY'];
					$n = $v2['QUANTITY'];

					$p2 = 0;
					$n2 = 0;
					foreach ($v2['set'] as $s_key => $s) {
						$p2 += $s['PRICE']*$s['QUANTITY'];
						$n2 += $s['QUANTITY'];
					}

					if ($p == 0 || $p == $p2 || $n2 == 0) continue;

					if ($p2 == 0) {
						$m = ($p >= $n2 ? floor($p/$n2) : floor($p/$n2*100)/100);
						$u = $p - $m*$n2;
						foreach ($v2['set'] as $s_key => $s) $v2['set'][$s_key]['PRICE'] = $m;
					}
					else {
						$u = 0;
						$w = $p/$p2;
						foreach ($v2['set'] as $s_key => $s) {
							$s['PRICE'] = floor($s['PRICE']*$w*100/100);
							if ($s['PRICE'] > 1) $s['PRICE'] = floor($s['PRICE']);
							$u += $s['PRICE']*$s['QUANTITY'];
							$v2['set'][$s_key] = $s;
						}
						$u = $p - $u;
					}
					if ($u != 0) foreach ($v2['set'] as $s_key => $s) if (!empty($s['QUANTITY'])) { $v2['set'][$s_key]['PRICE'] += $u/$s['QUANTITY']; break; }

					$v['basket'][$k2] = $v2;
				}
			}

			$v += self::GetPrice('basket_price', $price, $v['order_currency'], '', false);
			$v['depend_count'] = ($basket_count > 1 ? true : false); // зависимость от количества товаров: true - можно редактировать места и подключить опцию частичной доставки
//			if ($v['tariff'] == 62) $v['depend_62'] = true; // зависимость от тарифа курьер онлайн: true - можно подключить опцию почты "проверки комплектности" (4)
			if (!empty($v['cod'])) $v['depend_cod'] = true; // зависимость от наложенного платежа: true - можно подключить опцию СДЭК "обязательная оплата доставки" (1000)

			$data[$k] = $v;
		}
//		echo '<br><b>basket:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

	}


	// сохранение параметров упаковки
	public static function SavePackage($order_id, $id, $data, $mode = 'full') {

		$order = \Bitrix\Sale\Order::load($order_id);
		$props = edost_class::GetProps($order, array('order', 'no_payment'));

		if (!isset($props['package'])) return;

		if (!is_array($data)) $data = array();
		$package = (!empty($props['package']) ? $props['package'] : array());
		$zero = array('shipment_id' => $id, 'weight' => 0, 'size' => array(0, 0, 0));

		if ($mode == 'option') {
			if (empty($package)) $package[] = $zero;
			foreach ($package as $k => $v) if ($v['shipment_id'] == $id) $package[$k]['service'] = $data;
		}
		else {
			if ($mode == 'package') {
				$service = false;
				foreach ($package as $k => $v) if ($v['shipment_id'] == $id && isset($v['service'])) { $service = $v['service']; break; }
				if ($service !== false) {
					if (empty($data)) $data[] = $zero;
					foreach ($data as $k => $v) $data[$k]['service'] = $service;
				}
			}
			foreach ($package as $k => $v) if ($v['shipment_id'] == $id) unset($package[$k]);
			$package = array_merge($package, $data);
		}

//		echo '<br><b>package:</b> <pre style="font-size: 12px">'.print_r($package, true).'</pre>';

		$ar = $order->getPropertyCollection();
		foreach ($ar->getGroups() as $g) foreach ($ar->getGroupProperties($g['ID']) as $v2) if ($v2->getField('CODE') == 'PACKAGE') {
			$v2->setValue($GLOBALS['APPLICATION']->ConvertCharset(edost_class::PackDataArray($package, 'package2'), 'windows-1251', LANG_CHARSET));
			$v2->save();
			break;
		}

	}


	// получение параметра $_REQUEST
	public static function GetRequest($name, $length = 15) {
		$r = false;
		if (isset($_REQUEST[$name])) $r = $_REQUEST[$name];
		else if (isset($_REQUEST['order'][$name])) $r = $_REQUEST['order'][$name];
		if ($r !== false && $r !== '') $r = substr($r, 0, $length);
		return $r;
	}


	// запись полей местоположения в заказ
	public static function SaveOrderLocation(&$order, $mode = '', $tariff, $data, $edost_locations) {

		$address = (!empty($data['ADDRESS']) ? $data['ADDRESS'] : '');
		$location = (!empty($data['LOCATION']) ? $data['LOCATION'] : '');

		$props = edost_class::GetProps($order, array('order'));
		$prop = $props['prop'];
		if ($edost_locations) {
			$prop2 = CLocationsEDOST::GetProp2();
			$passport = CLocationsEDOST::SetAddress($prop2, false, false, 'PASSPORT');
			if ($mode == 'admin' && empty($location)) $location = CSaleLocation::getLocationCODEbyID(intval($prop['LOCATION']['value']));
		}

		$office = ($tariff !== false ? edost_class::ParseOfficeAddress($address) : false);

		if (empty($office))
			if (empty($edost_locations)) $address = false;
			else $address = CLocationsEDOST::SetAddress($prop2, isset($prop['CITY']), isset($prop['METRO']));

		$zip_original =(!empty($prop['ZIP']) ? $prop['ZIP']['value'] : '');
		$zip = self::GetRequest('edost_shop_ZIP', 10);
        if ($zip === false) $zip = $zip_original;

		$zip_full = (!empty($zip) && empty($prop2['zip_full']) ? false : true);

		$post = false;
		if (!empty($office['post'])) {
			$post = true;
			$zip = $office['id'];
			$zip_full =	true;
		}

		if (!$zip_full && !isset($prop['ZIP_AUTO']) && isset($props['location_data']['country']) && isset(CDeliveryEDOST::$region_code[$props['location_data']['country']])) $zip .= (substr($zip, -1) !== '.' ? '.' : '');
		if ($zip == '') $zip = '.';

		$zip_auto = (!$zip_full ? 'Y' : '');

		$s = array();
		if ($edost_locations || $post) {
			$s['ZIP'] = $zip;
			$s['ZIP_AUTO'] = $zip_auto;
		}
		if ($mode == 'admin') {
			if ($address !== false)	$s['ADDRESS'] = $address;

			if ($edost_locations) {
				if (!empty($location)) $s['LOCATION'] = $location;
				$s['CITY'] = (!empty($prop2['city2']) ? $prop2['city2'] : '');
				$s['METRO'] = (!empty($prop2['metro']) && !empty($prop2['metro_required']) ? $prop2['metro'] : '');
				$s['PASSPORT'] = $passport;
			}
		}

		$p = array();
		if (strlen($zip) != strlen($zip_original)) $p['save'] = array('ZIP' => 'clear');

		if (empty($s)) return;

		self::OrderProperty($order, $s, $p);

	}


	// запись свойств заказа
	public static function OrderProperty(&$order, $data, $param = false) {

		if (!empty($param['save']) && !is_array($param['save'])) $param['save'] = array_fill_keys(array_keys($data), $param['save']);

		$ar = $order->getPropertyCollection();
		foreach ($ar->getGroups() as $g) foreach ($ar->getGroupProperties($g['ID']) as $v) {
			$k = $v->getField('CODE');
			if (!isset($data[$k])) continue;

			$s = $data[$k];
			if ($k == 'PACKAGE') $s = edost_class::PackDataArray($s, 'package2');

			$save = (!empty($param['save'][$k]) ? $param['save'][$k] : '');
			if ($save === 'clear') {
				$v->setValue('');
//				$v->save();
			}
			$v->setValue($s);
			if ($save === 'full') $v->save();
		}

	}


	// определение ID платежных систем с обработчиком наложенного платежа eDost
	public static function GetCodPaySystem() {
		if (self::$cod_paysystem == null) {
			self::$cod_paysystem = array();
			$ar = \Bitrix\Sale\Internals\PaySystemActionTable::getList(array('select' => array('ID', 'NAME', 'SORT', 'DESCRIPTION', 'ACTIVE', 'ACTION_FILE')));
			while ($v = $ar->fetch()) if (substr($v['ACTION_FILE'], -11) == 'edostpaycod') self::$cod_paysystem[] = $v['ID'];
		}
		return self::$cod_paysystem;
	}


	// определение ид магазина в системе eDost и количество заказов доступных для контроля
	public static function AddControlCount(&$data, $c) {
		if (empty($data)) return;
		$config = CDeliveryEDOST::GetEdostConfig('all');
		foreach ($data as $k => $v) {
			$s = CDeliveryEDOST::GetEdostConfig($v['site_id'], $config, true);
			$shop_id = (isset($s['id']) ? $s['id'] : '');
			$data[$k]['shop_id'] = $shop_id;
			$data[$k]['control_count'] = (isset($c['control'][$shop_id]['count']) ? $c['control'][$shop_id]['count'] : 0);
		}
	}


	// добавление данных необходимых для оформления доставки
	public static function AddRegisterData(&$data, $print_doc = false) {

		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
		$doc = $control_sign['doc'];

		$base_currency = CDeliveryEDOST::GetRUB();
		$shop = CDeliveryEDOST::GetPrintField('shop');
		$cookie = self::GetCookie();
		$option = self::GetRegisterOption();
		$register_tariff = self::GetRegisterTariff();

		self::AddPaymentData($data);

		self::AddBasketData($data);
		foreach ($data as $k => $v) if (empty($v['basket'])) unset($data[$k]);

        // единицы измерения, отличающиеся от штук, переносить в название товара
		if ($cookie['register_pcs'] == 'Y') foreach ($data as $k => $v) foreach ($v['basket'] as $k2 => $v2) if ($v2['MEASURE_CODE'] != 796) {
			$v2['NAME'] .= ' ('.$v2['QUANTITY'].' '.$v2['MEASURE_NAME'].')';
			$v2['PRICE'] = $v2['PRICE']*$v2['QUANTITY'];
			$v2['BASE_PRICE'] = $v2['BASE_PRICE']*$v2['QUANTITY'];
			$v2['QUANTITY'] = 1;
			$v2['MEASURE_CODE'] = 796;
			$v2['MEASURE_NAME'] = trim($control_sign['quantity']);
			$data[$k]['basket'][$k2] = $v2;
		}

		// определение составного заказа (с двумя и более отгрузками)
		if (!empty($data)) {
			$o = $ar = array();
			foreach ($data as $v) $ar[] = $v['order_id'];
			$ar = \Bitrix\Sale\Internals\ShipmentTable::getList(array('select' => array('ID', 'ORDER_ID'), 'filter' => array('=ORDER_ID' => $ar, '!SYSTEM' => 'Y'), 'limit' => 1000));
			while ($v = $ar->fetch()) $o[$v['ORDER_ID']][] = $v['ID'];
			foreach ($data as $k => $v) if (isset($o[$v['order_id']]) && count($o[$v['order_id']]) > 1) $data[$k]['part'] = true;
		}

		foreach ($data as $k => $v) {
			$v['shop_data'] = $shop;

			$cod = (!empty($v['cod']) ? $v['payment'][$v['cod']] : false);
			if (!empty($v['part'])) $cod = false; // отключение наложенного платежа, если в заказе несколько отгрузок

			$insurance = (!empty($v['insurance']) ? true : false);
			if ($cod) $insurance = true;

			$ar = array();
			foreach ($doc as $v2) {
				if (isset($v2['company_id'])) {
					if ($v2['id'] == 'batch' || !in_array($v['company_id'], $v2['company_id'])) continue;
				}
				else if (!isset($v2['delivery'])) continue;
				else if (!is_array($v2['delivery']) || empty($v['tariff']) || !in_array($v['tariff'], $v2['delivery'])) continue;
				if ($v2['id'] == '107') {
					if (!$insurance || $cookie['register_print_107'] == 'no' || $cookie['register_print_107'] == 'c' && !$cod) continue;
				}
	            else if ($v2['cod'] && !$cod) continue;

				$ar[] = $v2['id'];
			}
			$v['doc'] = $ar;

			$v['props'] = $p = edost_class::GetProps($v['order_id'], array('no_payment'), $v['id']);

			// проверка полей
			$e = array();
			if (empty($p['location_data'])) $e['city'] = true;
			else {
				if ($v['company_id'] == 23) { // почта
					if (empty($p['zip']) || $p['zip'] == '.' || strlen($p['zip']) != 6) $e['zip'] = true;
				}
				if ($v['company_id'] == 5) { // сдэк
					if (empty($p['phone'])) $e['phone'] = true;
				}

				if (empty($p['location_data']['city'])) $e['city'] = true;

				$s = '';
				if (!isset($p['name_part'])) $s .= trim($p['name']);
				else foreach ($p['name_part'] as $k2 => $v2) $s .= trim($v2);
				$s .= trim($p['company']);
				if (empty($s)) $e['name'] = true;

				$s = '';
				if (!empty($p['office']) || !isset($p['address_part'])) $s .= trim($p['address']);
				else foreach ($p['address_part'] as $k2 => $v2) $s .= trim($v2);
				if (empty($s)) $e['address'] = true;

				if (in_array($v['tariff'], CDeliveryEDOST::$office) && empty($v['props']['office'])) $e['office'] = true;
			}
			if (!empty($e)) $v['field_error'] = $e;

			// стоимость заказа для объявленной ценности и наложенного платежа (для документов и оформления доставки)
			$price = 0;
			if ($cod) {
				$price = self::GetPrice('value', $cod['sum'], $cod['currency'], $base_currency);
				$v['cod_formatted'] = self::GetPrice('formatted', $cod['sum'], $cod['currency'], '', false);
			}
			else if (!empty($v['basket_price'])) $price = self::GetPrice('value', $v['basket_price'], $v['order_currency'], $base_currency);

			if ($insurance) $v['insurance_price'] = $price;
			if ($cod) $v['cod_price'] = $price;

			$o = $option[$v['company_id']];
//		echo '<br><b>O=============</b><pre style="font-size: 12px">'.print_r($o, true).'</pre>';
//		echo '<br><b>V=============</b><pre style="font-size: 12px">'.print_r($v, true).'</pre>';
			foreach ($o['service'] as $k2 => $v2) {
				foreach (self::$depend as $key) if (!empty($v2['depend_'.$key]) && empty($v['depend_'.$key])) { unset($o['service'][$k2]); break; }
				if (!empty($v2['depend_tariff']) && !in_array($v['tariff'], $v2['depend_tariff'])) unset($o['service'][$k2]);
				if (!empty($v2['depend_postamat']) && ($v2['depend_postamat'] == 'N' && !empty($v['props']['postamat']) || $v2['depend_postamat'] == 'Y' && empty($v['props']['postamat']))) unset($o['service'][$k2]);
			}

			if (!empty($v['props']['package']) && ($v['company_id'] != 23 || count($v['props']['package']) <= 1)) {
				$package = $v['props']['package'];
				if (count($package) == 1) {
					$package[0]['item'] = array();
					foreach ($v['basket'] as $v2) $package[0]['item'][$v2['ID']] = array('id' => $v2['ID'], 'quantity' => $v2['QUANTITY']);
				}
			}
			else {
				$p = array(
					'new' => true,
					'shipment_id' => $v['id'],
					'weight' => 0,
					'size' => array(0, 0, 0),
					'item' => array(),
					'service' => array(),
				);
				foreach ($v['basket'] as $v2) $p['item'][] = array('id' => $v2['ID'], 'quantity' => $v2['QUANTITY']);
				foreach ($o['service'] as $v2) if ($v2['value'] == 2) $p['service'][] = $v2['id'];
				$package = array($p);
			}

			foreach ($package as $p_key => $p) {
				if (!empty($p['service'])) foreach ($p['service'] as $k2 => $v2) if (empty($o['service'][$v2])) { unset($p['service'][$k2]); break; }

				$p['insurance'] = ($insurance ? $price : 0);
				$p['cod'] = ($cod ? $price : 0);

				$s = '';
				if (!empty($p['weight']) || !empty($p['size'][0])) {
					$s = '<span style="font-weight: bold;">'.(!empty($p['weight']) ? $p['weight'] : '0').'</span> '.$control_sign['kg'];
					if (!empty($p['size'][0])) {
						$s .= ', ';
						foreach ($p['size'] as $k2 => $v2) $s .= '<span style="font-weight: bold;">'.(!empty($p['size'][$k2]) ? $p['size'][$k2] : '0').'</span>'.($k2 != 2 ? 'x' : '');
						$s .= ' '.$control_sign['cm'];
					}
				}
				$p['param_formatted'] = $s;

				$package[$p_key] = $p;
			}

//		echo '<br><b>package:</b> <pre style="font-size: 12px">'.print_r($package, true).'</pre>';
			$n = count($package);
			if ($n > 0) {
				$basket_name = array();
				foreach ($v['basket'] as $k2 => $v2) {
					$basket_name[$k2] = $v2['NAME'];
					foreach ($v2['set'] as $s_key => $s) $basket_name[$s_key] = $s['NAME'];
				}

				$s = '';
				if ($n == 1) $s = $package[0]['param_formatted'];
				else {
					$weight = $volume = 0;
					$package_detail = array();
					foreach ($package as $p_key => $p) {
						$weight += $p['weight'];
						$volume += $p['size'][0]*$p['size'][1]*$p['size'][2]/1000000;

						$item = array();
						foreach ($p['item'] as $i_key => $i) if (!empty($basket_name[$i_key])) $item[] = $basket_name[$i_key] . ($i['quantity'] > 1 ? ' (<b>'.$i['quantity'].$control_sign['quantity'].'</b>)' : '');
						$package_detail[$p_key] = '<div class="edost_package_box edost_package_box_normal"><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>'.
						'<td class="edost_package_box_head"><div>'.$control_sign['package']['head'].'</div>'.($p_key+1).'</td>'.
						'<td style="padding: 8px;"><div style="font-size: 15px; padding-bottom: 5px;">'.$p['param_formatted'].'</div>'.(!empty($item) ? implode('<br>', $item) : '<span style="opacity: 0.5;">'.$control_sign['package']['no_item']).'</span></td>'.
						'<tr></table></div>';
					}

					$volume = round($volume*100)/100;
					$s = '<span style="cursor: pointer;" data-param="id='.$v['id'].'" onclick="edost.window.set(\'package_detail\', this)"><b>'.self::draw_string('box', $n).'</b> (<b>'.$weight.'</b> '.$control_sign['kg'].($volume > 0 ? ', <b>'.$volume.'</b> '.$control_sign['meter'].'<sup>3</sup>' : '').')</span>';
					$s .= '<div id="edost_package_detail_'.$v['id'].'_div" style="display: none;">'.implode(' ', $package_detail).'</div>';
				}
				$v['package_formatted'] = $s;
			}

			$s = array();
			if (!empty($package[0]['service'])) foreach ($package[0]['service'] as $v2) if (isset($o['service'][$v2])) $s[$v2] = $o['service'][$v2]['name'];
			$active = false;
			foreach ($o['service'] as $v2) if ($v2['value'] != 0) $active = true;
			$v['option'] = array('service' => array_keys($s), 'service_formatted' => implode(', ', $s), 'service_active' => !empty($s) || $active ? true : false);

			$v['props']['package'] = $package;

			$data[$k] = $v;
		}
//		echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

	}


	// получение контролируемых отгрузок с данными или только данные для отгрузок с возможностью контроля и 'allow_delivery' ($id - элемент/массив id отгрузок  или  фильтрация по id пользователя при $mode == 'user')
	public static function GetControlShipment($id, $mode = '') {

		if (empty($id)) return false;

		$r = self::Control();

		if ($mode == 'user') {
			if (empty($r['data'])) return false;
			$ar = self::GetShipmentData(array_keys($r['data']), array('user_id' => $id));
			foreach ($r['data'] as $k => $v) if (!empty($ar[$k])) $r['data'][$k] += $ar[$k]; else unset($r['data'][$k]);
		}
		else {
			if (!is_array($id)) $id = array($id);
			$ar = self::GetShipmentData($id);
			if (!empty($r['data'])) foreach ($r['data'] as $k => $v)
				if (empty($ar[$k])) unset($r['data'][$k]);
				else {
					$r['data'][$k] += $ar[$k];
					$r['data'][$k]['control'] = true;
					$i = array_search($k, $id);
					if ($i !== false) unset($id[$i]);
				}

			if (!empty($id)) {
				if (empty($r['data'])) $r['data'] = array();
				foreach ($id as $k) if (!empty($ar[$k]) && $ar[$k]['allow_delivery'] && !empty($ar[$k]['tracking_code'])) $r['data'][$k] = $ar[$k];
			}
		}

		if (!empty($r['data'])) self::AddControlCount($r['data'], $r);

//		echo '<br><b>GetControlShipment:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// детальная информация по контролируемому заказу
	public static function GetControlDetail($id) {

		$id = intval($id);
		$v = self::GetShipmentData($id);
  		if (empty($v[$id])) return false;

  		$v = $v[$id];

		$cache = new CPHPCache();
		if ($cache->InitCache(300, CDeliveryEDOST::GetCacheID('detail|'.$id), '/')) $r = $cache->GetVars();
		else {
			$config = CDeliveryEDOST::GetEdostConfig($v['site_id']);
			$r = self::RequestData('', $config['id'], $config['ps'], 'type=control&mode=detail&data='.$id.'|end|', 'detail');
			if (!isset($r['error'])) {
				$cache->StartDataCache();
				$cache->EndDataCache($r);
			}
		}

//		echo '<br><b>'.$id.'</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// генерация html блока с ячейками для ввода веса и габаритов или ссылки на редактирование мест + опции доставки ($param: 'full' - генерация блока, 'package' - только упаковка, 'option' - только опции)
	public static function GetPackageString($data, $param = 'full', $change = true) {
//		echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

		$r = '';
		$error_code = '';
		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
		$format = self::GetFormat($data);
		$cookie = edost_class::GetCookie();

		// список с товарами (для мест)
		if ($param == 'full' || $param == 'package') {
			if ($param == 'full') $r .= '<div id="edost_package_'.$data['id'].'">';

			$ar = array();
			foreach ($data['basket'] as $k2 => $v2) {
				$v2['hide'] = 0;
				if (!empty($v2['set'])) {
					foreach ($v2['set'] as $v3) foreach ($data['props']['package'] as $v4) if (isset($v4['item'][ $v3['ID'] ])) { $v2['hide'] = 1; break; }
					$v2['SET_PARENT_ID'] = 'set';
					foreach ($v2['set'] as $v3) {
						if ($v2['hide'] == 0) $v3['hide'] = 1;
						$ar[$v3['ID']] = $v3;
					}
				}
				$ar[$k2] = $v2;
			}
			foreach ($ar as $k => $v) $ar[$k]['box_quantity'] = 0;

			$item = edost_class::GetJson($ar, array('ID', 'SET_PARENT_ID', 'NAME', 'QUANTITY', 'package_weight', 'package_size', 'package_volume', 'hide'), true, false);
			$box = edost_class::PackDataArray($data['props']['package'], 'package2');

			$count = count($data['props']['package']);
			if ($count == 1) {
				$p = $data['props']['package'][0];
				$input_update = 'oninput="edost.register.input(this)"';
				$r .= '<input class="edost_package edost_package_weight '.($p['weight'] == 0 ? 'edost_package_error' : 'edost_package_on').'" data-code="'.$data['id'].'_package_0_weight" style="height: 18px; width: 35px;" value="'.($p['weight'] != 0 ? $p['weight'] : '').'" type="input" '.$input_update.'> '.$control_sign['kg'];
				foreach ($p['size'] as $k2 => $v2) $r .= '<input class="edost_package edost_package_size '.($p['size'][$k2] == 0 ? 'edost_package_error2' : 'edost_package_on').'" data-code="'.$data['id'].'_package_0_size_'.$k2.'" style="height: 18px;'.($k2 == 0 ? ' margin-left: 10px;' : '').' width: 35px;" value="'.($p['size'][$k2] != 0 ? $p['size'][$k2] : '').'" type="input" '.$input_update.'> '.($k2 != 2 ? ' x ' : '');
				$r .= ' '.$control_sign['cm'];
			}
			else {
				$n = 0;
				$weight = false;
				foreach ($data['props']['package'] as $v) {
					if (empty($v['weight'])) $weight = true;
					foreach ($v['item'] as $v2) if (isset($ar[$v2['id']])) $ar[$v2['id']]['box_quantity'] += $v2['quantity'];
				}
				if ($weight) $error_code = 'package_weight';
				foreach ($ar as $k => $v) if (!$v['hide'] && $v['box_quantity'] != $v['QUANTITY']) { $error_code = 'package_pack'; break; }
					$s = '<span style="cursor: pointer;" onclick="edost.window.set(\'package_detail_'.$v['id'].'\')"><b>'.self::draw_string('box', $n).'</b> (<b>'.$weight.'</b> '.$control_sign['kg'].($volume > 0 ? ', <b>'.$volume.'</b> '.$control_sign['meter'].'<sup>3</sup>' : '').')</span>';
				if (!empty($data['package_formatted'])) $r .= ($error_code != '' ? '<span style="color: #F88;">' : '').str_replace(array('cursor: pointer;', 'onclick="edost.window.set(\'package_detail\', this)"'), '', $data['package_formatted']).($error_code != '' ? '</span>' : '');
			}

			if ($change && $data['company_id'] != 23 && (!empty($data['depend_count']) || $error_code != '')) $r .= ' <span class="edost_control_button edost_control_button_low" style="padding-left: 5px;" onclick="edost.package.open(\''.$data['order_id'].'_'.$data['id'].'\', this)">'.($count == 1 ? $control_sign['button']['package_change'] : $control_sign['button']['package_change2']).'</span>';

			$r .= '<input id="edost_package_error_'.$data['id'].'" type="hidden" data-error="'.$error_code.'" data-box="'.$count.'" data-tariff="'.$data['tariff'].'">';
			$r .= '<input id="edost_package_'.$data['order_id'].'_'.$data['id'].'_item_data" type="hidden" value=\''.$item.'\'>';
			$r .= '<input id="edost_package_'.$data['order_id'].'_'.$data['id'].'_box_data" type="hidden" value=\''.$box.'\'>';

			if ($param == 'full') $r .= '</div>';
		}

		if (in_array($data['company_id'], array(19)) && ($param == 'full' || $param == 'type')) {
			$c = (!empty($data['props']['package'][0]['type']) ? $data['props']['package'][0]['type'] : $cookie['register_package_type']);
			$c = trim($c);
			$r .= '<div class="edost_package_type">';
			$r .= '<span>'.$control_sign['package']['type_head'].':</span> ';
			if ($change) {
				$r .= '<span class="edost_service_button edost_control_button edost_control_button_low" style="display: inline-block;" onclick="edost.register.button(this)">'.($c != '' ? $c : $control_sign['button']['type_change']).'</span>';
				$r .= '<span style="display: none;"><input class="edost_suggest_list" data-suggest="package_type" data-id="'.$data['id'].'" value="'.$c.'" type="input" maxlength="20"></span>';
			}
			else $r .= $c;
			$r .= '</div>';
		}

		if ($param == 'full' || $param == 'option') if (!empty($data['option']['service_active'])) {
			$count_service = count($data['option']['service']);
            $a = ($count_service > 0 ? true : false);

			if ($param == 'full') $r .= '<div id="edost_option_'.$data['id'].'" class="edost_service">';
			$r .= '<div class="edost_package_service">';
			if ($a) $r .= '<span style="color: #888;">'.$control_sign['package']['option_head'].':</span> <span class="edost_service">'.$data['option']['service_formatted'].'</span>';
			if ($change) {
				$s = array();
				foreach (self::$depend as $key) if (!empty($data['depend_'.$key])) $s[] = 'depend_'.$key.'=1';
				$r .= ($a ? ' (' : '').'<span class="edost_service_button edost_control_button edost_control_button_low" data-param="id='.$data['id'].';order_id='.$data['order_id'].';company='.$data['company_id'].';tariff='.$data['tariff'].';'.(!empty($data['props']['postamat']) ? 'postamat=Y;' : '').'service='.implode(',', $data['option']['service']).(!empty($s) ? ';'.implode(';', $s) : '').'" onclick="edost.window.set(\'option\', this)">'.($count_service == 0 ? $control_sign['button']['option_change'] : $control_sign['button']['option_change2']).'</span>'.($a ? ')' : '');
			}
			$r .= '</div>';
			if ($param == 'full') $r .= '</div>';
		}

		if ((!in_array($data['company_id'], array(19)) || $format == 'door') && ($param == 'full' || $param == 'comment')) {
			$c = (!empty($data['props']['package'][0]['comment']) ? $data['props']['package'][0]['comment'] : '');
			if ($c == '' && $cookie['register_user_description'] == 'Y') $c = $data['order_user_description'];
			$c = trim($c);
			$door = ($format == 'door' ? '_door' : '');
			$h = '<span style="color: #888;">'.$control_sign['package']['comment_head'.$door].':</span> ';
			$r .= '<div class="edost_package_comment">';
			if ($change) {
				if ($c == '') $r .= '<span class="edost_service_button edost_control_button edost_control_button_low" style="display: inline-block;" onclick="edost.register.button(this)">'.$control_sign['button']['comment'.$door.'_change'].'</span>';
				$r .= '<div'.($c == '' ? ' style="display: none;"' : '').'><div>'.$h.'</div><textarea data-id="'.$data['id'].'">'.$c.'</textarea></div>';
			}
			else if ($c != '') $r .= $h.$c;
			$r .= '</div>';
		}

		return $r;

	}


	// генерация html блока со статусом контроля: сокращенного, глобального со ссылкой "подробнее...", всего списка ($string_length - длина строки при сокращенном выводе)
	public static function GetControlString($data, $string_length = 0) {
//		echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($data, true).'</pre>';

		if (empty($data)) return '';

		$r = '';
		$detail = true;
		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');

		if (isset($data['id'])) {
			$detail = false;
			$data = array($data);
		}

		$count = count($data);
		$max = 16; // допустимое количество строк для ссылки "показать все..."

		$i = 0;
		foreach ($data as $k => $v) {
			$i++;
			if (!isset($v['status'])) return false;

			$s = '';
			$status = (!empty($v['register']) ? $control_sign['status'][1] : $v['status_string']);
			if (empty($status) && !empty($control_sign['status'][$v['status']])) $status = $control_sign['status'][$v['status']];

			if (!empty($v['status_date'])) $s .= '<span class="edost_control_date">'.$v['status_date'].'</span>';
			if (!$string_length && $detail && !empty($v['status_time'])) $s .= ' <span class="edost_control_time">'.$v['status_time'].'</span>';

			if ($string_length) return (!empty($v['status_date']) ? $v['status_date'].' - ' : '').self::limit_string($status, $string_length);

			$color = '';
			if (empty($v['register'])) {
				if (in_array($v['status'], array(4, 5, 7))) $color = 'green';
				if (!empty($v['status_warning']))
					if ($v['status_warning'] == 1) $color = 'pink';
					else if ($v['status_warning'] == 2) $color = 'red';
					else if ($v['status_warning'] == 3) $color = 'orange';
			}
			if ($color != '') $color = ' edost_control_color_'.$color;

			if (!empty($v['status_info']))
				if (!$detail) $status .= ' ('.str_replace(array(' (', ') ', '(', ')'), array(', ', ', ', ', ', ''), $v['status_info']).')';
				else $status .= '<br><span class="edost_control_color_light" style="font-size: 12px;">'.$v['status_info'].'</span>';

			$v['status'] = ''; // закомментировать для тестирования (вывод кодов статусов контроля) !!!!!

			$status = ($v['status'] !== '' ? $v['status'].' - ' : '').'<span class="edost_control_status'.$color.'">'.$status.'</span>';
			if (!$detail) $s .= '&nbsp;&nbsp;'.$status;
			else $s = '<div class="edost_control_td1">'.$s.'</div>'.'<div class="edost_control_td2">'.$status.'</div>';

			if (!$detail) $c = '';
			else if ($k == 0) $c = 'first';
			else if ($k % 2 == 1) $c = 'odd';
			else $c = 'even';

			if ($count > $max && $i == $max-5) $r .= '<span class="edost_link edost_control_detail" style="float: left;" onclick="edost_ShowDetail(this, \'all\')">'.$control_sign['detail_all'].'</span><div style="display: none;">';
			$r .= '<div class="edost_control_string'.($c != '' ? '_'.$c : '').($k == 0 ? ' edost_control_string_bold' : '').'">'.$s.'</div>';
		}
		if ($count > $max) $r .= '</div>';

		if (!$detail && !$string_length && !empty($data[0]['id']) && empty($data[0]['register'])) {
			if (!empty($data[0]['status']) && !in_array($data[0]['status'], array(13, 14, 25))) $r = '<div id="edost_control_'.$data[0]['id'].'_string">'.$r.'<span class="edost_link edost_control_detail" onclick="edost_ShowDetail('.$data[0]['id'].')">'.$control_sign['detail'].'</span></div><div id="edost_control_'.$data[0]['id'].'_detail" style="padding: 10px 0 0 0; display: none;"></div>';
			$r = '<div class="edost_control" id="edost_control_'.$data[0]['id'].'">'.$r.'</div>';
		}

		return $r;

	}


	// история (последние операции)
	public static function History($history = false, $add = false, $name = 'register') {

		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');

		if ($history === false) {
			$history = array(
				'name' => $name,
				'cache' => new CPHPCache(),
				'cache_id' => CDeliveryEDOST::GetCacheID('history_'.$name),
				'cache_time' => 86400*15,
				'data' => array(),
				'select' => '',
			);
			if (!empty($_SESSION['EDOST']['history_'.$name])) $history['data'] = $_SESSION['EDOST']['history_'.$name];
			else if ($history['cache']->InitCache($history['cache_time'], $history['cache_id'], '/')) $history['data'] = $history['cache']->GetVars();
		}
		else {
			if ($add !== false) {
				$n = count($add['code_data']);
				$code = $add['mode'].':'.implode('|', $add['code_data']);

				$s = '';
				$i = 0;
				foreach ($add['code_data'] as $v) {
					$i++;
					if ($i > 8 && $n > 10) {
			            $s .= ', ... ('.$control_sign['total'].' '.self::draw_string(in_array($add['mode'], array('office', 'date')) ? $control_sign['batch'] : $control_sign['order'], $n).')';
						break;
					}
					$s .= ($s != '' ? ', ' : '').$v;
				}
				$s = (in_array($add['mode'], array('office', 'date')) ? $control_sign['batch_prefix'] : $control_sign['order']).' '.$control_sign['order_prefix'] . $s;

				$first = -1;
				foreach ($history['data'] as $k => $v) {
					if ($first == -1) $first = $k;
					if ($v['code'] == $code) unset($history['data'][$k]);
				}

				if (count($history['data']) >= 20 && $first >= 0) unset($history['data'][$first]);

				$add['name'] = $s;
				$add['code'] = $code;

				$history['data'][] = $add;
			}

			$_SESSION['EDOST']['history_'.$history['name']] = $history['data'];
			if (!$history['cache']->InitCache(1, $history['cache_id'], '/')) {
				$history['cache']->StartDataCache();
				$history['cache']->EndDataCache($history['data']);
			}
		}

//		if (!edost_class::GetCache()) return false;

		if (!empty($history['data'])) {
			foreach ($history['data'] as $k => $v) $history['data'][$k]['id'] = $k;
			$s = '';
			$ar = array_reverse($history['data']);
			foreach ($ar as $v) $s .= '<option value="'.$v['id'].'">'.(isset($control_sign['history'][$v['mode']]) ? $control_sign['history'][$v['mode']].' - ' : '').$v['time'].' - '.$v['name'].'</option>';
			$history['select'] = '<select style="height: 20px; padding: 1px;" id="edost_history">'.$s.'</select>';
		}

//		echo '<br><b>history:</b><pre style="font-size: 12px">'.print_r($history, true).'</pre>';

		return $history;

	}


	// проверка работоспособности кэша магазина
	public static function GetCache() {

		$date = date('dmY');
		$v = COption::GetOptionString('edost.delivery', 'cache', '');

		if ($v == $date.'|Y') return true;
		if ($v == $date.'|N') return false;
		if (!empty($_REQUEST['clear_cache']) && $_REQUEST['clear_cache'] == 'Y') return true;

		$v = explode('|', $v);
		$v = (isset($v[1]) ? intval($v[1]) : 0);

		$cache = new CPHPCache();
		if ($cache->InitCache(86400, CDeliveryEDOST::GetCacheID('cache'), '/')) $v = 'Y';
		else {
			$v++;
			if ($v > 3) $v = 'N';
			$cache->StartDataCache();
			$cache->EndDataCache('data');
		}

		COption::SetOptionString('edost.delivery', 'cache', $date.'|'.$v);

		return ($v !== 'N' ? true : false);

	}


	// получение списка тарифов доступных для оформления
	public static function GetRegisterTariff() {
		$r = array();
		foreach (CDeliveryEDOST::$register_tariff as $k => $v) { $r += array_fill_keys($v, $k); }
		return $r;
	}


	// загрузка опций оформления (для админки)
	public static function GetRegisterOption($id = false, $new = false, $html = false) {

		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
		$r = $control_sign['register_option'];
		$ico_path = '/bitrix/images/delivery_edost_img';

		if (!empty($new)) $new = edost_class::UnPackDataArray($new, array('id', 'value'));

		$s = \Bitrix\Main\Config\Option::get('edost.delivery', 'register_option');
		$save = (!empty($s) ? edost_class::UnPackDataArray($s, 'option') : false);
		foreach ($r as $k => $v) {
			foreach ($v['service'] as $k2 => $v2) {
				$value = false;
				if (!empty($new) && isset($new[$k.'_'.$k2])) $value = $new[$k.'_'.$k2]['value'];
				else if (!empty($save) && isset($save[$k]['service'][$k2])) $value = $save[$k]['service'][$k2]['value'];
				if ($value !== false) $v['service'][$k2]['value'] = $value;
			}
			$r[$k] = $v;
		}

		if (!empty($new)) \Bitrix\Main\Config\Option::set('edost.delivery', 'register_option', edost_class::PackDataArray($r, 'option'));

		if ($html) {
			if (!empty($new)) echo 'close';
			else {
				$protocol = CDeliveryEDOST::GetProtocol();
				$img_path = $protocol.'edostimg.ru/img/site';
				$i = 0;
				foreach ($r as $k => $v) {
					if ($i != 0) echo '<div class="edost_delimiter" style="margin: 20px;"></div>';
					$i++; ?>
					<div class="edost_option">
						<div style="text-align: center; padding: 10px 0 5px 0;">
							<img class="edost_option" src="<?=$ico_path?>/company/<?=$v['id']?>.gif" border="0">
							<span class="edost_option"><?=$v['name']?></span>
						</div>

						<div class="edost_field_delimiter" style="margin-top: 10px;"><?=$control_sign['package']['option_head2']?></div>
<?						foreach ($v['service'] as $s) { ?>
						<div class="edost_option_service">
							<div class="edost_option_service_name"><?=(!empty($s['hint']) ? '<img class="edost_hint_link" data-param="shift=center,20" src="'.$img_path.'/hint.svg" border="0"><div class="edost_hint_data">'.$s['hint'].'</div> ' : '')?><span><?=$s['name']?></span></div>
							<div class="edost_option_service_value">
<?								foreach ($control_sign['setting']['service'] as $k2 => $v2) { $a = ($k2 == 2 && !empty($s['off']) ? true : false); ?>
								<label>
									<? /* <?=($a ? 'onclick="edost.window.field(this, \'off\')"' : '')?> */ ?>
									<input style="margin: 0px;" name="edost_service_<?=$k?>_<?=$s['id']?>" data-id="<?=$s['id']?>" type="radio" <?=($k2 == $s['value'] ? 'checked=""' : '')?> <?=($a ? 'data-off="'.implode(',', $s['off']).'"' : '')?> value="<?=$k2?>">
									<span class="edost_option_radio_<?=$k2?>"><?=$v2?></span>
								</label>
<?								} ?>
							</div>
						</div>
<?						} ?>
					</div>
<?				}
			}
        }

//		echo '<br><b>save:</b> <pre style="font-size: 12px">'.print_r($save, true).'</pre>';
//		echo '<br><b>new:</b> <pre style="font-size: 12px">'.print_r($new, true).'</pre>';
//		echo '<br><b>r:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// загрузка профилей оформления (для админки)
	public static function GetRegisterProfile($param = false) {

		$id = (isset($param['id']) ? $param['id'] : false);
		$new = false;
		$delete = false;
		$main = (!empty($param['main']) ? true : false);

		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
		$profile = $control_sign['register_profile'];
		$ico_path = '/bitrix/images/delivery_edost_img';
//		$shop_hide = array('mode', 'account', 'secure', 'contract', 'format', 'vat', 'head_doc_company');
//		$company_hide = array('phone', 'city', 'address', 'head_seller', 'company_seller', 'companytype_seller', 'inn_seller', 'address_seller', 'phone_seller', 'head_call', 'city_call', 'address_call', 'phone_call', 'time_lunch_call', 'time_call', 'comment_call', 'head_doc_shop');
		$new_hide = array('delete');
		$update = array('type', 'mode');
		$local = false;

		if (!empty($param['new'])) {
			$new = $param['new'];
			$ar = array();
			$s = explode('|', $new);
			foreach ($s as $v) {
				$c = explode('=', $v);
				$value = (isset($c[1]) ? $c[1] : '');
				$c = explode(':', $c[0]);
				$key = $c[0];
				$key2 = (isset($c[1]) ? $c[1] : false);
				if (strpos($key, 'time') === 0) {
					$value = intval($value);
					if ($value < 0) $value = 0;
					$max = ($key2 % 2 == 0 ? 24 : 59);
					if ($value > $max) $value = $max;
				}
				if ($key == 'local') $local = true;
				else if ($key == 'id' || isset($profile[$key])) if ($key2 !== false) $ar[$key][$key2] = $value; else $ar[$key] = $value;
			}
			$new = $ar;
		}


		$r = array();
		$s = \Bitrix\Main\Config\Option::get('edost.delivery', 'register_profile');
		if ($s != '') $r = unserialize($s);

		$id_new = (empty($r['id']) ? 1 : $r['id']);
		$r = (!empty($r['data']) ? $r['data'] : array());
		foreach ($r as $k => $v) {
			$s = \Bitrix\Main\Config\Option::get('edost.delivery', 'register_profile_'.$k);
			$r[$k] = ($s != '' ? unserialize($s) : array());
		}
//		echo '<br><b>RegisterProfile:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		if (!empty($new) && isset($new['id'])) {
			$id = $new['id'];
			if (!isset($r[$id])) {
				$id = $new['id'] = $id_new;
				$id_new++;
			}
			else if (!empty($new['delete'])) {
				unset($r[$id]);
				$delete = true;
			}
			if (!$delete) {
				// флаги заполненности полей (если флаг установлен, значит все необходимые поля заполнены)
				$ar = array(
					'call' => array('type' => 'shop', 'data' => array('city_call', 'address_call', 'phone_call', 'time_call', 'name')),
					'reverse' => array('type' => 'shop', 'data' => array('company', 'address', 'phone', 'name')),
				);
				foreach ($ar as $k => $v) if ($v['type'] == $new['type']) {
					$a = true;
					foreach ($v['data'] as $v2) {
						if (strpos($v2, 'address') === 0) {
							if (empty($new[$v2][0]) || empty($new[$v2][1]) && empty($new[$v2][2])) $a = false;
						}
						else if (strpos($v2, 'time') === 0) {
							if (empty($new[$v2][0]) && empty($new[$v2][1]) || empty($new[$v2][2]) && empty($new[$v2][3])) $a = false;
						}
						else if (empty($new[$v2])) $a = false;
						if (!$a) break;
					}
					$new['flag_'.$k] = $a;
				}
				$r[$id] = $new;
				\Bitrix\Main\Config\Option::set('edost.delivery', 'register_profile_'.$id, serialize($new));
			}
			else \Bitrix\Main\Config\Option::delete('edost.delivery', array('name' => 'register_profile_'.$id));

			$ar = array('id' => $id_new, 'data' => array_fill_keys(array_keys($r), array()));
			\Bitrix\Main\Config\Option::set('edost.delivery', 'register_profile', serialize($ar));

			if ($local) $local = ($new['type'] == 'shop' ? 'shop' : 'delivery').'|'.$id;
			$id = '';
		}

		// выпадающие списки для выбора профиля
		if (isset($param['company'])) {
			$flag = array('flag_call', 'flag_reverse'); // флаги наличия в профилях необходимых данных (для вызова курьера и опции реверса у СДЭК)
			for ($i = 1; $i <= 2; $i++) {
				if ($i == 1 && in_array($param['company'], array(23, 30))) continue; // отключение выбора профиля магазина при оформлении

				$type = ($i == 1 ? 'shop' : 'delivery');
				if (isset($param['profile_'.$type])) $a = $param['profile_'.$type];
				else {
					$a = false;
					foreach ($r as $k => $v)  if ($i == 1 && $v['type'] === 'shop' || $i == 2 && $v['type'] == $param['company']) {
						if (!$a) $a = $k;
						if ($v['active'] == 2) $a = $k;
					}
				}

				$s = '';
				foreach ($r as $k => $v) if ($i == 1 && $v['type'] === 'shop' || $i == 2 && $v['type'] == $param['company']) {
					$name = $v['code'];
					if ($name == '') $name = $v['company'];
					if ($name == '') $name = 'ID: '.$k;

					$s2 = array();
					foreach ($flag as $flag_key) if (!empty($v[$flag_key])) $s2[] = 'data-'.$flag_key.'="Y"';

					$p = array();
					$key = array('batch_format'); // параметры, которые используются в js
					foreach ($key as $v2) if (isset($v[$v2])) $p[] = $v2.'='.$v[$v2];

					$s .= '<option value="'.$k.'" '.($k == $a ? 'selected' : '').(!empty($s2) ? ' '.implode(' ', $s2) : '').(!empty($p) ? ' data-param="'.implode(';', $p).'"' : '').'>'.$name.'</option>';
				}

				$update = ($main ? "edost.register.active_all_update()" : "edost.window.resize('change')");

				$data_param = 'type='.$type.';company='.$param['company'].';local=1';

				if ($main) $button = '';
				else {
					$button = '<span class="edost_profile_'.$type.'_change" style="display: none;"><span class="edost_control_button edost_control_button_low edost_profile_change" data-param="'.$data_param.';change=1" onclick="edost.window.set(\'profile_setting_change\', this)">'.$control_sign['button']['change'].'</span>&nbsp;|&nbsp;</span>';
					$button .= '<span class="edost_control_button edost_control_button_low" data-param="'.$data_param.'" onclick="edost.window.set(\'profile_setting_new\', this)">'.$control_sign['button']['profile_new_small'].'</span>';
					$button = '<div style="color: #888;">'.$button.'</div>';
				}

				if ($s == '') $s = '<span class="edost_link" data-param="'.$data_param.'" onclick="edost.window.set(\'profile_setting_new\', this)">'.$control_sign['button']['profile_new_small'].' '.$control_sign['profile_'.$type].'</span>';
				else $s = $control_sign['profile_'.$type].' <select '.($main ? 'id' : 'name').'="edost_profile_'.$type.'" style="'.($main ? 'max-width: 280px;' : 'width: 210px;').' font-size: 16px; padding: 3px; margin: 0;" onchange="'.$update.'"><option value="" style="color: #AAA;">'.$control_sign['profile_no'].'</option>'.$s.'</select>'.$button;

				$s = '<div style="'.($main ? 'padding: 8px;' : 'text-align: right;').(!$main && $i == 2 ? 'padding-top: 10px;' : '').'">'.$s.'</div>';

				echo $s;
			}
			return;
		}

		// настройки
		if (isset($param['setting']) && $id !== false) {
			if ($local) echo $local;
			else if (count($r) == 0 && $delete || $new) echo 'close';
			else if ($id === '' && count($r) != 0) {
				$ar = array();
				if (!empty($r)) foreach ($r as $k => $v) {
					$name = '';
					$key = array('code', 'company', 'name');
					foreach ($key as $v2) if (empty($name)) $name = $v[$v2];
					if (empty($name)) $name = $control_sign['profile_'.($type == 'shop' ? 'shop' : 'delivery')];

					$s = '';
					$s .= '<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
					$s .= '<td style="'.($v['active'] == 0 ? 'opacity: 0.5;' : '').($v['active'] == 2 ? ' font-weight: bold;' : '').'">'.($v['type'] != 'shop' ? '<img class="edost_ico edost_ico_profile" src="'.$ico_path.'/company/'.$v['type'].'.gif" border="0">' : '').$name.'</td>';
					$s .= '<td width="125px; text-align: right;">'.'<div class="edost_office_button edost_office_button_blue" style="color: #FFF;" onclick="edost.window.set(\'profile_setting_change\', \'id='.$k.'\')">'.$control_sign['button']['change'].'</div>'.'</td>';
					$s .= '</tr></table>';
					$ar[] = $s;
				}
				echo implode('<div class="edost_office_address_delimiter" style="margin: 10px 0;"></div>', $ar);
			}
			else {
		        $id = intval($id);
				$protocol = CDeliveryEDOST::GetProtocol();
				$img_path = $protocol.'edostimg.ru/img/site';

				echo '<input id="edost_profile_id" name="id" data-count="'.count($r).'" type="hidden" value="'.$id.'">';
//		echo '<br><b>RegisterProfile:</b> <pre style="font-size: 12px">'.print_r($profile, true).'</pre>';
				foreach ($profile as $k => $v) if ($id != 0 || $k != 'delete') {

//					echo 'ID: '.$id;
//					echo ($new ? 'NEW' : 'FALSE');
//					echo ($delete ? 'DELETE' : '-');

                    $type = 'data-type="'.(!empty($v['type']) ? implode(',',$v['type']) : '').'"';
//					$c = (in_array($k, $shop_hide) ? 'edost_field_shop_hide' : '').' '.(in_array($k, $company_hide) ? 'edost_field_company_hide' : '').' '.(in_array($k, $new_hide) ? 'edost_field_new_hide' : '').' '.(in_array($k, array('contract')) ? 'edost_field_contract' : '');
//					$c = (in_array($k, $new_hide) ? 'edost_field_new_hide' : '').' '.(in_array($k, array('contract')) ? 'edost_field_contract' : '');
					$c = (in_array($k, array('contract')) ? 'edost_field_contract' : '');
					if (isset($v['delimiter'])) {
						echo '<div class="edost_field_delimiter '.$c.'" '.$type.'>'.$v['delimiter'];
						if (!empty($v['hint'])) {
							if (!is_array($v['hint'])) $v['hint'] = array('' => $v['hint']);
							foreach ($v['hint'] as $k2 => $v2) echo ' <span class="edost_hint"'.($k2 ? ' data-type="'.$k2.'"' : '').'><img class="edost_hint_link" data-param="inside=Y" src="'.$img_path.'/hint.svg" border="0"><div class="edost_hint_data'.(!empty($v['hint_warning']) ? ' edost_hint_warning' : '').'">'.$v2.'</div></span>';
						}
						echo '</div>';
						continue;
					} ?>
					<div class="edost_field <?=$c?>" style="<?=($k == 'delete' ? 'padding-top: 12px; margin-bottom: 0;' : '')?>" <?=$type?>>
						<div class="edost_field_name">
<?							if (!empty($v['name']))
								if (!is_array($v['name'])) echo $v['name'].':';
								else foreach ($v['name'] as $k2 => $v2) echo '<span data-type="'.$k2.'">'.$v2.':</span>'; ?>
						</div>
						<div class="edost_field_value">
<?							if (strpos($k, 'city') === 0) {
								echo '<input class="edost_field_city" type="hidden" name="'.$k.'" value="'.(isset($r[$id][$k]) ? $r[$id][$k] : '').'">';
							}
							else if (strpos($k, 'address') === 0) {
								$s = (isset($r[$id][$k]) ? $r[$id][$k] : false);
								for ($i = 0; $i < 3; $i++) {
									echo ($i == 0 ? '' : '&nbsp;&nbsp;&nbsp;').$control_sign['address'][$i].' ';
									echo '<input style="width: '.($i == 0 ? 156 : 45).'px;" type="text" name="'.$k.':'.$i.'" value="'.(isset($s[$i]) ? $s[$i] : '').'">';
								}
							}
							else if (strpos($k, 'time') === 0) {
								$s = (isset($r[$id][$k]) ? $r[$id][$k] : false);
								$zero = array(empty($s[0]) && empty($s[1]) ? true : false, empty($s[2]) && empty($s[3]) ? true : false);
								for ($i = 0; $i < 4; $i++) {
									echo '<input style="width: 30px;" type="text" name="'.$k.':'.$i.'" value="'.(isset($s[$i]) && !$zero[$i >> 1] ? self::draw_number('time', $s[$i]) : '').'">';
									if ($i == 0 || $i == 2) echo ' : ';
									else if ($i == 1) echo '&nbsp;&nbsp;-&nbsp;&nbsp;';
								}
							}
							else if (!empty($v['checkbox'])) {
								$code = 'edost_register_profile_'.$k;
								echo '<input id="'.$code.'" name="'.$k.'" style="width: auto; vertical-align: middle;" type="checkbox"> <label for="'.$code.'">'.$v['checkbox'].'<label>';
							}
							else if (!empty($v['values'])) {
								$s = '';
								foreach ($v['values'] as $k2 => $v2) {
									if (!is_array($v2))	$s .= '<option value="'.$k2.'"'.(isset($r[$id][$k]) && $k2 == $r[$id][$k] ? ' selected' : '').'>'.$v2.'</option>';
									else foreach ($v2 as $u_key => $u) $s .= '<option data-type="'.$k2.'" value="'.$u_key.'"'.(isset($r[$id][$k]) && $u_key == $r[$id][$k] ? ' selected' : '').'>'.$u.'</option>';
								}
								echo '<select name="'.$k.'"'.(in_array($k, $update) ? ' onchange="edost.window.resize()"' : '').($k == 'type' ? ' data-path="'.$ico_path.'/company/"' : '').'>'.$s.'</select>';
							}
							else {
								echo '<input type="text" name="'.$k.'" value="'.(isset($r[$id][$k]) ? str_replace(array('"', "'"), array('&quot;', '&quot;'), $r[$id][$k]) : '').'">';
							} ?>
						</div>
<?						if (!empty($v['hint'])) echo '<div class="edost_field_hint">'.$v['hint'].'</div>'; ?>
					</div>
<?				}
			}
		}

//		echo '<br><b>r:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// загрузка локальных настроек из cookie (для админки)
	public static function GetCookie() {

		$ar = (isset($_COOKIE['edost_admin']) && $_COOKIE['edost_admin'] != '' ? explode('|', $GLOBALS['APPLICATION']->ConvertCharset($_COOKIE['edost_admin'], 'utf-8', LANG_CHARSET)) : array());

		$r = array(
			'filter_days' => '5', // заказы оформленные за последние 'filter_days' дней
			'docs_active' => '', // активные документы для ручной печати
			'setting_active' => 'module', // активная настройка (module, paysystem, document)
			'setting_tariff_show' => 'N', // редактировать названия тарифов (Y, N)
			'admin_type' => '', // последняя просмотренная страница
			'control_day_delay' => '5', // превышен срок доставки
			'control_day_office' => '2', // лежат в пункте выдачи
			'control_day_complete' => '15', // ожидают зачисления наложки
			'control_show_total' => 'N', // заказы не требующие внимания
			'control_setting' => 'N', // выводить блок с настройками контроля
			'control_delete' => 'Y', // выводить кнопку "снять с контроля" для выданных заказов
			'control_paid' => 'Y', // выводить кнопку "зачислить платеж" для выданных заказов с наложенным платежом
			'control_changed' => 'Y', // выводить список с заказами, у которых сегодня изменился статус
			'control_complete_delay' => 'N', // выводить на сколько превышен срок доставки у выполненных заказов
			'register_setting' => 'N', // выводить блок с настройками оформления заказа
			'register_day_insert' => '5', // заказы за последние
			'register_item' => 'N', // выводить список товаров
			'register_status' => 'N', // выводить статус заказа
			'register_no_label' => 'N', // печатать ярлыки на обычной бумаге
			'register_no_batch' => 'N', // не ставить отметку "на сдачу"
			'register_batch_date' => 1, // для новых заказов дату сдачи устанавливать на
			'register_batch_date_skip_weekend' => 'Y', // переносить дата сдачи на понедельник, если она выпала на субботу или воскресенье
			'register_print_107' => 'no', // печать бланков описи
			'register_package_item' => 'N', // режима распределения упаковки: по местам / по списку
			'register_print_label_format' => 'A4', // размер бумаги для этикеток (СДЭК)
			'register_tariff_name' => 'N', // выводить название тарифа
			'register_order_number' => 'N', // выводить number заказа
			'register_pcs' => 'N', // единицы измерения, отличающиеся от штук, переносить в название товара
			'register_package_type' => '', // описание (характер) груза по умолчанию
			'register_user_description' => 'Y', // загружать комментарий из заказа
		);

		$i = 0;
		foreach ($r as $k => $v) {
			$w = (isset($ar[$i]) && $ar[$i] !== '' ? $ar[$i] : '');
			if ($w && !in_array($k, array('register_package_type'))) $w = preg_replace("/[^0-9a-z_|-]/i", "", $w);
			$r[$k] = ($w ? $w : $v);
			$i++;
		}

		$r['docs_active'] = ($r['docs_active'] != '' ? explode('-', $r['docs_active']) : array());

		return $r;

	}


	// получение примеров и формата кодов отправлений
	public static function GetTracking($site_id = false) {

		if (!self::GetCache()) return false;

		$cache = new CPHPCache();
		if ($cache->InitCache(86400*5, CDeliveryEDOST::GetCacheID('tracking'), '/')) return $cache->GetVars();

		$config = CDeliveryEDOST::GetEdostConfig($site_id !== false ? $site_id : SITE_ID);
		$r = edost_class::RequestData('', $config['id'], $config['ps'], 'type=tracking', 'tracking');
//		echo '<br><b>GetTracking:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		if (!isset($r['error'])) {
			$cache->StartDataCache();
			$cache->EndDataCache($r);
		}

		return $r;

	}


	// изменение параметров заказа
	public static function OrderUpdate($data, $param) {

		if (empty($data['order_id'])) return;

		$order = \Bitrix\Sale\Order::load($data['order_id']);
		if (!$order) return;

		if (class_exists('edost_function') && method_exists('edost_function', 'BeforeOrderUpdate')) edost_function::BeforeOrderUpdate($order, $data, $param);

		$a = false;
		if (!empty($param['status'])) {
			$a = true;
			$order->setField('STATUS_ID', $param['status']);
		}
		if (!empty($data['id']) && isset($param['tracking_code'])) {
			$a = true;
			$s = $order->getShipmentCollection()->getItemById($data['id']);
			if ($s) $s->setField('TRACKING_NUMBER', $param['tracking_code']);
		}
		if (!empty($param['payment_id']) && !empty($param['paid'])) {
			$a = true;
			$s = $order->getPaymentCollection()->getItemById($param['payment_id']);
			if ($s) $s->setPaid('Y');
		}
		if ($a) $order->save();

		return $param;

	}


	// заказы с измененным статусом за последние 28 часов: добавление + удаление всего списка + загрузка всего списка ($id - ид добавляемой отгрузки)
	public static function ControlChanged($id = false) {

		if ($id === 'delete') {
			COption::SetOptionString('edost.delivery', 'control_changed', '');
			return false;
		}

		$time = time();
		$r = COption::GetOptionString('edost.delivery', 'control_changed', '');
		$r = ($r != '' ? unserialize($r) : array());

		// удаление старых записей
		if (!empty($r)) foreach ($r as $k => $v) if ($time - $v > 100800) unset($r[$k]);

		if (!empty($id)) {
			$r[$id] = $time;
			COption::SetOptionString('edost.delivery', 'control_changed', serialize($r));
		}

		return $r;

	}


	// контроль заказов: добавление или изменение данных + загрузка всего списка ($shipment - элемент/массив объектов 'shipment' или id отгрузок,  $flag - код или команда: add - добавить, delete - удалить, new - отметка 'новый', old - отметка 'старый', special - на особый контроль,  $data - данные контроля, вместо загрузки оригинальных данных с сервера)
	public static function Control($shipment = false, $param = false) {

		$flag = (isset($param['flag']) ? $param['flag'] : false);
		$get = (isset($param['get']) ? $param['get'] : false); // загрузить данные контроля для указанных отгрузок

		$cache = new CPHPCache();
		$cache_id = CDeliveryEDOST::GetCacheID('control');
		if ($shipment === 'clear_cache_flag') {
			$_SESSION['EDOST']['control_clear_cache'] = true;
			return;
		}
		if ($shipment === 'clear_cache') {
			$shipment = false;
			$a = false;
			if (!empty($_SESSION['EDOST']['control_clear_cache'])) {
				$a = true;
//				echo 'clear cache';
				unset($_SESSION['EDOST']['control_clear_cache']);
			}
			if (!empty($_SESSION['EDOST']['control_add_time']) && time() - $_SESSION['EDOST']['control_add_time'] > 60) {
				$a = true;
//				echo 'clear cache =============TIME=============';
				unset($_SESSION['EDOST']['control_add_time']);
			}
			if ($a) $cache->Clean($cache_id, '/');
		}

		// загрузка данных контроля по id отгрузок
		if ($get) {
			if (empty($shipment)) return;
			$id = array();
			foreach ($shipment as $k => $v) $id[$v['site_id']][] = $v['id'];
//			echo '<br><b>id:</b><pre style="font-size: 12px">'.print_r($id, true).'</pre>';
			$shipment = false;
		}

		$register = false;
		if ($shipment !== false) {
			if (!empty($shipment) && is_array($shipment)) foreach ($shipment as $k => $v) if (is_array($v) && isset($v['props']['package'])) { $register = true; break; }
			if (!$register) $shipment = self::GetShipmentData($shipment);
		}
		if (isset($param['batch'])) {
			// запрос на изменение профилей и вызова курьера
			edost_class::AddRegisterData($shipment);
			$register = true;
		}
//		echo '<br><b>shipment:</b><pre style="font-size: 12px">'.print_r($shipment, true).'</pre>';

		$mode = 'add';
		if ($flag !== false) {
			if (empty($flag)) {
				$mode = 'delete';
				$flag = 0;
			}
			else if (intval($flag) == 0) {
				$mode = $flag;
				$flag = false;
			}
		}

		$r = array();
		$add = array();
		$reload = false;
		$save = false;
		$add_error = false;

		if (!empty($shipment)) {
			$c = (isset($param['data']) ? $param['data'] : self::Control());

			$key = self::$control_key;
			if (in_array($mode, array('delete', 'delete_register', 'order_batch_delete', 'batch_delete', 'batch_date', 'batch_office', 'order_date', 'call', 'new', 'old', 'special', 'normal'))) $key = array_slice($key, 0, 2);
			else if (!$register) $key = array_slice($key, 0, 8);

			// разбор отгрузок по id магазина в системе eDost
			$ar = array();
			foreach ($shipment as $k => $v) {
				if (isset($c['data'][$k])) $v = $v + $c['data'][$k];
				else if ($mode == 'delete') continue;

				if ($mode == 'add') {
					if (!empty($v['props'])) $p = $v['props'];
					else if (empty($v['order'])) $p = edost_class::GetProps($v['order_id'], array('no_payment'));
					else {
						$p = edost_class::GetProps($v['order'], array('order', 'no_payment'));
						unset($v['order']);
					}

					if (!empty($p['location_data'])) $v += $p['location_data'];
					if (isset($param['batch']) && !empty($v['batch'])) $v['batch'] = array_merge($v['batch'], $param['batch']);

					if ($register) $v = self::PackRegisterData($v); // данные для оформления доставки
//					echo '<br><b>data:</b><pre style="font-size: 12px">'.print_r($v, true).'</pre>'; // !!!!!
//					die();
				}

				$s = CDeliveryEDOST::GetEdostConfig($v['site_id']);
				if (!isset($ar[$s['id']])) $ar[$s['id']] = array('config' => $s, 'data' => array());
				$ar[$s['id']]['data'][] = $v;
			}

			// отправка данных на сервер контроля
			foreach ($ar as $shop) {
				$config = $shop['config'];
				$shipment = $shop['data'];

				foreach ($shipment as $k => $v) {
					if ($flag !== false) $f = $flag;
					else {
						$f = (!empty($v['flag']) ? $v['flag'] : 1);

						if ($mode == 'delete') $f = 0;
						else if ($mode == 'delete_register') $f = 6;
						else if ($mode == 'order_batch_delete') $f = 7; // исключить заказ из сдачи
						else if ($mode == 'batch_delete') $f = 8; // удалить сдачу со всеми заказами
						else if ($mode == 'batch_date') $f = 9; // изменить дату сдачи
						else if ($mode == 'batch_office') $f = 10; // зарегистрировать сдачу в отделении
						else if ($mode == 'order_date') $f = 11; // перенести заказ на другую дату
						else if ($mode == 'new') $f = ($f == 3 ? 4 : 2);
						else if ($mode == 'old') $f = ($f == 4 ? 3 : 1);
						else if ($mode == 'special') $f = ($f == 2 ? 4 : 3);
						else if ($mode == 'normal') $f = ($f == 4 ? 2 : 1);
					}
//					echo '=='.$mode.'-'.$f;
/*
					register:
					0 - новый заказ
					2 - в процессе оформления
					4 - заказ оформлен
					5 - заказ оформлен и зарегистрирован в отделении
					6 - удаление заказа
					7 - исключение из сдачи
					8 - удаление сдачи со всеми заказами
					9 - в процессе изменения даты сдачи
					10 - в процессе регистрации в отделении
					11 - перенести заказ на другую дату
*/
					$shipment[$k]['flag'] = $f;
					$shipment[$k]['shop_id'] = $config['id'];
				}
//				echo '<br><b>mode: '.$mode.':</b> <pre style="font-size: 12px">'.print_r($shipment, true).'</pre>';

				$s = 'add';
				if (isset($param['batch'])) $s = (isset($param['batch']['call']) ? 'call' : 'profile');
				$s = 'type=control&mode='.$s.'&data='.edost_class::PackData($shipment, $key);
				if (!empty($param['no_api'])) $s .= '&no_api=1';
				if (isset($param['date'])) $s .= '&date='.urlencode($param['date']);
				$data = edost_class::RequestData($config['host'], $config['id'], $config['ps'], $s, 'control');
//				echo '<br><b>post|'.$s.'===== mode: '.$mode.' ('.$config['id'].'):</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//				die();

				if (isset($data['error'])) {
					$reload = true;
					$add_error = $data['error'];
				}
				else {
					// удаление в отгрузке идентификатора отправления при отмене оформления
					if (in_array($mode, array('delete_register', 'batch_delete'))) {
						$o = $shipment;
						if ($mode == 'batch_delete') {
							$s = array();
							if (!empty($c['data'])) foreach ($shipment as $v) foreach ($c['data'] as $v2) if ($v2['id'] != $v['id'] && $v2['batch_code'] == $v['batch_code']) $s[] = $v2['id'];
							if (!empty($s)) {
								$s = self::GetShipmentData($s);
								if (!empty($s)) foreach ($s as $v) $o[] = $v;
							}
						}
						foreach ($o as $k => $v) self::OrderUpdate($v, array('function' => 'register_cancel', 'tracking_code' => ''));
					}

					if (empty($_SESSION['EDOST']['control_add_time'])) $_SESSION['EDOST']['control_add_time'] = time();
					foreach ($shipment as $v) $add[$v['id']] = $v;
				}
			}

			// подготовка данных для локальной базы (чтобы не тратить время на загрузку данных с сервера)
			foreach ($add as $k => $v) {
				$ar = array(
					'flag' => 1,
					'status' => 0,
					'status_warning' => 0,
					'status_string' => '',
					'status_info' => '',
				);
				if ($mode == 'add') {
					$v['status_date'] = date('d.m.Y');
					$v['status_time'] = '';

					if (!$register) $s = 0; else $s = (!empty($v['register']) ? $v['register'] : 2);
					$v['register'] = $s;
				}
				if ($mode == 'batch_office') {
					$v['register'] = 10;
					$v['status'] = 0;
					$v['status_warning'] = 0;
					$v['status_string'] = '';
					$v['status_info'] = '';
				}
				$add[$k] = $v + $ar;
			}
//			echo '<br><b>'.$mode.':</b> <pre style="font-size: 12px">'.print_r($add, true).'</pre>';
		}

		if ($cache->InitCache(3600, $cache_id, '/')) $r = $cache->GetVars();
		else if (empty($add)) $reload = true;

		// добавление данных в локальную базу
		if (!empty($add) && !$reload) {
			$r = $c;
			foreach ($add as $k => $v) {
				if ($mode == 'delete') {
					if ($v['status'] == 0) $r['control'][$v['shop_id']]['count']++;
					if (isset($r['data'][$k])) unset($r['data'][$k]);
				}
				else {
					if ($mode == 'add') $r['control'][$v['shop_id']]['count']--;
					$r['data'][$k] = $v;
				}
			}
			$save = true;
		}

		if ($reload || $get) {
			$c = false;
			$config = CDeliveryEDOST::GetEdostConfig('all');
			foreach ($config as $v) if (!empty($v['id']) && !empty($v['ps'])) { $c = $v; break; }
			if ($get && !empty($r['data'])) {
				// загрузка заказов по id с сервера контроля
				foreach ($config as $k => $v)
					if (isset($id[$k]) || $k === 'all') $config[$k]['order_id'] = implode(',', $id[$k]);
					else unset($config[$k]);
				$s = 'type=control&mode=get&data='.edost_class::PackData($config, array('id', 'ps', 'order_id'));
				$r2 = edost_class::RequestData($c['host'], $c['id'], $c['ps'], $s, 'control');
				if (!empty($r2['data'])) foreach ($r2['data'] as $k => $v) $r['data'][$k] = $v;
				if (isset($r2['error'])) $r['error'] = $r2['error'];
			}
			else {
				// загрузка всех заказов с сервера контроля
				$s = 'type=control&mode=get&data='.edost_class::PackData($config, array('id', 'ps'));
				$r = $r2 = edost_class::RequestData($c['host'], $c['id'], $c['ps'], $s, 'control');
				if (!empty($_SESSION['EDOST']['control_add_time']) && !isset($r['error'])) unset($_SESSION['EDOST']['control_add_time']);
//				echo '<br><b>control reload ('.count($r['data']).'):</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';
			}
			$save = (!isset($r['error']) ? true : false);

			// обновление статуса заказа (после вручения или прибытия в пункт выдачи) + сохранение идентификатора отправления и изменение статуса заказа (после оформления)
			if (!empty($r['data'])) {
				$ar = array();
				foreach ($r2['data'] as $k => $v) if (in_array($v['register'], array(4, 5)) && $v['tracking_code'] != '' || $v['day_office'] >= 1 || $v['status'] == 5) $ar[] = $k;
				$ar = self::GetShipmentData($ar);
				if (!empty($ar)) foreach ($ar as $k => $v) {
					$c = $r['data'][$k];
					$config = CDeliveryEDOST::GetEdostConfig($v['site_id']);

					if (in_array($c['register'], array(4, 5)) && $c['tracking_code'] != '') {
						if ($v['tracking_code'] != $c['tracking_code']) self::OrderUpdate($v, array(
							'function' => 'register',
							'status' => (!empty($config['register_status']) && $v['order_status'] != $config['register_status'] ? $config['register_status'] : ''),
							'tracking_code' => $c['tracking_code'],
						));
						continue;
					}

					$v += $c;

					$status = array(
						'arrived' => $config['control_status_arrived'],
						'completed' => $config['control_status_completed'],
						'completed_cod' => $config['control_status_completed_cod'],
					);

					$s = false;
					if ($v['status'] == 5) {
						if (empty($status['completed']) && empty($status['completed_cod'])) continue;
						if (!empty($status['completed']) && $status['completed'] == $v['order_status'] || !empty($status['completed_cod']) && $status['completed_cod'] == $v['order_status']) continue;
						$props = edost_class::GetProps($v['order_id']);
						if (!empty($props['cod'])) {
							if (!empty($status['completed_cod'])) $s = $status['completed_cod'];
						}
						else if (!empty($status['completed'])) $s = $status['completed'];
					}
					else {
						if (empty($status['arrived']) || $status['arrived'] == $v['order_status']) continue;
						$s = $status['arrived'];
					}
					if ($s !== false) {
						$p = self::OrderUpdate($v, array('function' => 'control', 'status' => $s));
						if (!empty($p['status'])) self::ControlChanged($v['id']);
					}
				}
			}
		}

		if ($save) {
			$cache->Clean($cache_id, '/');
			$cache->StartDataCache();
			$cache->EndDataCache($r);
		}

//		echo '<br><pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		// добавление кода компании доставки (для оформления)
		if (!empty($r['data'])) {
			$register_tariff = edost_class::GetRegisterTariff();
			foreach ($r['data'] as $k => $v) if (empty($v['company_id']) && isset($register_tariff[$v['tariff']])) {
				$v['company_id'] = $register_tariff[$v['tariff']];
				if (!empty($v['batch_code'])) $v['batch_code'] .= '_'.$v['company_id'];
				$r['data'][$k] = $v;
			}
		}

		if (!$get) {
			// изменение register по ключевому заказу (для массовых операций с заказами в сдаче)
			$key = array(8, 9, 10);
			if (!empty($r['data'])) foreach ($r['data'] as $k => $v) if (!empty($v['batch_code']))
				foreach ($r['data'] as $k2 => $v2) if ($v2['batch_code'] == $v['batch_code']) {
					$ar = array();
					if ($k2 != $k && in_array($v['register'], $key) && !in_array($v2['register'], $key) && $v2['batch_code'] == $v['batch_code']) $ar = array('status', 'status_warning', 'status_string', 'status_info', 'status_date', 'status_time', 'register');
					if ($k2 != $k && $v['register'] == 4 && $v2['register'] == 4 && $v['status'] == 43 && $v2['status'] != $v['status']) $ar = array('status', 'status_warning', 'status_string');
					if (!empty($ar)) foreach ($ar as $v3) $r['data'][$k2][$v3] = $v[$v3];
				}

			// расчет количества заказов по каждой группе + определение 'new', 'special' и 'complete'
			$now = edost_class::time(date('d.m.Y'));
			$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
			$count = array_fill_keys(array_keys($control_sign['count_head']), 0);
			$count2 = array_fill_keys(array_keys($control_sign['count_head_register']), 0);
			if (!empty($r['data'])) foreach ($r['data'] as $k => $v) {
				$v['new'] = ($v['flag'] == 2 || $v['flag'] == 4 ? true : false);
				$v['special'] = ($v['flag'] == 3 || $v['flag'] == 4 ? true : false);
				$v['complete'] = (in_array($v['register'], array(4, 5)) ? true : false);
				if (!empty($v['register']) && !empty($v['company_id']) && $v['register'] == 4 && in_array($v['company_id'], array(5, 30, 19))) $v['register'] = 5;
				$r['data'][$k] = $v;

				if (empty($v['register'])) {
					$count['total']++;

					if (!empty($v['new'])) $count['new']++;
					if (!empty($v['special'])) $count['special']++;
					if ($v['status_warning'] == 1) $count['warning_pink']++;
					if ($v['status_warning'] == 2) $count['warning_red']++;
					if ($v['status_warning'] == 3) $count['warning_orange']++;
					if ($v['status'] != 5 && $v['day_delay'] >= 1) $count['delay']++;
					if ($v['status'] != 5 && $v['day_office'] >= 1) $count['office']++;

					if ($v['status'] == 0) $count['add']++;
				}
				else {
					$count['register']++;

					if (in_array($v['register'], array(4, 5)) && !empty($v['batch_code']) && edost_class::time($v['batch']['date']) < $now) {
						$r['data'][$k]['batch_20'] = true;
						$r['data'][$k]['complete'] = false;
					}
				}
			}
			$r['count'] = $count;
			$r['count_register'] = $count2;
		}

		if (!empty($add_error)) $r['add_error'] = $add_error;

//		echo '<br><b>control:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		return $r;

	}


	// загрузка офисов
	public static function GetOffice($order, $company) {

		if (!isset($order['location']['country']) || empty($company)) return false;

		if (class_exists('edost_function') && method_exists('edost_function', 'BeforeGetOffice')) edost_function::BeforeGetOffice($order, $company);

		$data = array();
		$location = $order['location'];
		$config = $order['config'];
		$company = implode(',', $company);

		$city = $location['city'];
		if (empty($city) && $config['postmap'] == 'Y' && !empty($location['city2'])) $city = $location['city2'];

		$cache_id = CDeliveryEDOST::GetCacheID('office|'.$location['id'].'|'.$city.'|'.$company);
		$cache = new CPHPCache();
		if ($cache->InitCache(86400, $cache_id, '/')) {
			$data = $cache->GetVars();
			$data['cache'] = true;
			if (defined('DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE') && DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE == 'Y') if (class_exists('edost_function') && method_exists('edost_function', 'AfterGetOffice')) {
				edost_function::AfterGetOffice($order, $data);
				if (!isset($data['error'])) self::AddOfficeParam($data);
			}
		}
		else {
			$ar = array();
			$ar[] = 'type=office';
			$ar[] = 'country='.$location['country'];
			$ar[] = 'region='.$location['region'];
			$ar[] = 'city='.urlencode($city);
			$ar[] = 'company='.urlencode($company);

			// загрузка почтовых отделений
			if ($config['map'] == 'Y' && $config['postmap'] == 'Y') {
				$ar[] = 'post=1';
				$ar[] = 'zip='.preg_replace("/[^0-9]/i", "", $order['zip']);
			}

			// выбор пунктов выдачи из ближайших населенных пунктов
			if ($config['map'] == 'Y' && $config['office_near'] == 'Y') $ar[] = 'near='.($config['office_unsupported'] == 'Y' ? 2 : 1);

			if (!empty($order['pickpoint_widget'])) $ar[] = 'pickpoint=1'; // получить вместо офисов код города для виджета PickPoint (шаблон Visual)
			$data = self::RequestData('', $config['id'], $config['ps'], implode('&', $ar), 'office');

			if ($config['office_tel'] != 'Y' && !empty($data['data'])) foreach ($data['data'] as $k => $v) foreach ($v as $p_key => $p) $data['data'][$k][$p_key]['tel'] = '';

			if (class_exists('edost_function') && method_exists('edost_function', 'AfterGetOffice')) edost_function::AfterGetOffice($order, $data);

			if (!isset($data['error'])) {
				self::AddOfficeParam($data);
				$cache->StartDataCache();
				$cache->EndDataCache($data);
			}
		}
//		echo '<br><b>get office:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';

		// ограничение по параметрам заказа + загрузка дефолтных 'schedule', 'tel' и 'limit[tariff]'
		if (!empty($data['data'])) foreach ($data['data'] as $k => $v) {
			$office = (!empty($data['office'][$k]) ? $data['office'][$k] : false);
			$office_limit = (!empty($office['limit']) ? $office['limit'] : false);
			$office_schedule = (!empty($office['schedule']) ? $office['schedule'] : false);
			$office_tel = (!empty($office['tel']) && $config['office_tel'] == 'Y' ? $office['tel'] : false);
			$office_type2 = (!empty($office['type2']) ? true : false);

			foreach ($v as $p_key => $p) {
				if (empty($p['schedule']) && $office_schedule !== false) $p['schedule'] = $office_schedule;
				if (empty($p['tel']) && $office_tel !== false) $p['tel'] = $office_tel;
				if (!$office_type2) $p['type2'] = $p['type'];

				$limit = (!empty($office_limit[$p['type']]) ? $office_limit[$p['type']] : array());
				if (empty($p['limit'])) $p['limit'] = $limit;
				else if (!empty($limit['tariff'])) {
					if (!isset($p['limit']['tariff'])) $p['limit']['tariff'] = 0;
					$p['limit']['tariff'] |= $limit['tariff'];
				}
				$limit = $p['limit'];

				$data['data'][$k][$p_key] = $p;

				if (empty($limit) || count($limit) == 1 && isset($limit['tariff'])) continue;

				$a = false;
				$volume = $order['size1']*$order['size2']*$order['size3'];
				$weight = $order['weight'];

				if ($volume != 0 && !empty($limit['volume_weight'])) {
					$w = $volume/$limit['volume_weight'];
					if ($w > $weight) $weight = $w;
				}

				if (!empty($limit['weight_from']) && $weight < $limit['weight_from'] || !empty($limit['weight_to']) && $weight > $limit['weight_to']) $a = true;
				if (!empty($limit['sizemax']) && $order['size3'] > $limit['sizemax']) $a = true;
				if (!empty($limit['volume']) && $volume > $limit['volume']*1000000) $a = true;

				$ar = array('size1', 'size2', 'size3', 'sizesum');
				foreach ($ar as $s) if (!empty($limit[$s]) && $order[$s] > $limit[$s]) $a = true;

				if ($a) unset($data['data'][$k][$p_key]);
				else if (!empty($limit['price'])) $data['data'][$k][$p_key]['codmax'] = intval($limit['price'] - $order['price'] - 1);
			}
		}
//		echo '<br><b>get office:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';

		return $data;

	}

	public static function AddOfficeParam(&$data) {
		foreach ($data['data'] as $k => $v) {
			// перенос офисов с 'city' в конец списка
			$s = array();
			foreach ($v as $k2 => $v2) if (!empty($v2['city'])) {
				$s[$k2] = $v2;
				unset($v[$k2]);
			}
			if (!empty($s)) $v += $s;

			foreach ($v as $k2 => $v2) {
//				$v2['address_full2'] = $v2['address'].($v2['address2'] != '' ? ', ' : '').$v2['address2'];
				$city = '';
				if (!empty($v2['city'])) {
					$s = explode(';', $v2['city']);
					if (strpos($v2['address'], $s[0].',') === false) $city = $s[0];
				}
				$v2['address_full'] = ($city ? $city.', ' : '').$v2['address'].($v2['address2'] != '' ? ', ' : '').$v2['address2'];
				$v2['cod_disable'] = self::CodDisable($v2['options']);
				if (empty($v2['code'])) $v2['code'] = $v2['id'];
				$v[$k2] = $v2;
			}

			$data['data'][$k] = $v;
		}
	}

	// надбавка на неподдерживаемые пункты выдачи
	public static function AddUnsupported(&$v, $options, $config, $currency = false) {
		if (!empty($config[0]) && $config[0] === '+') {
			$k = $config[1];
			if (!isset($v[$k]) || $v[$k] == -1) return;

			$u = $k.'_discount';
			$a = (!empty($v[$u]) && ($k == 'price' || $k == 'pricecash') ? true : false);
			if ($a) $v[$k] = $v[$u][0];
			$v[$k] = round($v[$k]*(1 + $options[1]/100) + $options[0]);
			if ($a) $v[$k] = edost_class::SetDiscount($v[$k], $v[$u][0], $v[$u][1]);

			if ($currency) $v[$k.'_formatted'] = self::GetPrice('formatted', $v[$k], '', $currency);
			return;
		}
		if ($options & 512) {
			$s = array(!empty($config['office_unsupported_fix']) ? intval($config['office_unsupported_fix']) : 0, !empty($config['office_unsupported_percent']) ? intval($config['office_unsupported_percent']) : 0);
			if ($s[0] || $s[1]) {
				self::AddUnsupported($v, $s, array('+', !empty($v['priceinfo']) ? 'priceinfo' : 'price'), $currency);
				self::AddUnsupported($v, $s, array('+', 'pricecash'), $currency);
				if (isset($v['pricetotal'])) $v = array_merge($v, self::GetPrice('pricetotal', $v['price'] + (!empty($v['priceinfo']) ? $v['priceinfo'] : 0), '', $currency));
				if (isset($v['pricecod'])) $v = array_merge($v, self::GetPrice('pricecod', $v['pricecash'] + (!empty($v['transfer']) ? $v['transfer'] : 0), '', $currency));

				$key = array('price', 'pricetotal', 'pricecod', 'pricecash');
				foreach ($key as $p) if (isset($v[$p.'_original']) && isset($v[$p])) {
					self::AddUnsupported($v, $s, array('+', $p.'_original'), $currency);
					if ($v[$p.'_original'] - $v[$p] <= 5) {
						unset($v[$p.'_original']);
						if (isset($v[$p.'_original_formatted'])) unset($v[$p.'_original_formatted']);
					}
				}

				return true;
			}
		}
		return false;
	}

	public static function CodDisable($options) {
		return (($options & 6) == 2 ? true : false); // запрет на оплату наличными и невозможна оплата картой (0 - только налиные, 4 - наличные и карта, 6 - только карта, 2 - нет оплаты при получении)
	}


	// форматирование тарифов
	public static function FormatTariff($bitrix_data, $currency, $order, $active, $config = array()) {
//		echo '<br><b>FormatTariff order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>FormatTariff bitrix_data:</b> <pre style="font-size: 12px">'.print_r($bitrix_data, true).'</pre>';
//		echo '<br><b>FormatTariff config:</b> <pre style="font-size: 12px">'.print_r($config, true).'</pre>';

		$r = array();
		$data = array();
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$rename = GetMessage('EDOST_DELIVERY_RENAME');
		$base_currency = CDeliveryEDOST::GetRUB();
		$edost_order = (isset(CDeliveryEDOST::$result['order']) ? CDeliveryEDOST::$result['order'] : array());

		if (!empty($edost_order['config'])) $config = $config + $edost_order['config'];
		foreach (CDeliveryEDOST::$setting_key as $k => $v) if (empty($config[$k])) $config[$k] = $v;
		$template_2019 = (!empty($config['COMPACT']) ? true : false);
		if (!$template_2019) {
			$config['COMPACT'] = 'off';
			$config['PRIORITY'] = '';
		}
		if ($config['COMPACT'] == 'off') $config['PRIORITY'] = 'P';
		if ($config['template_block_type'] == 'bookmark2') $config['template_block_type'] = 'bookmark1'; // отключение старой функции вывода дешевых тарифов через закладки
		if (!empty($config['CATALOGDELIVERY'])) {
			if (!empty($config['SHOW_ERROR'])) {
				$config['hide_error'] = 'N';
				$config['show_zero_tariff'] = 'Y';
			}
			else {
				$config['hide_error'] = 'Y';
				$config['show_zero_tariff'] = 'N';
			}
		}
		if ($config['template'] != 'Y') {
			$config['template_autoselect_office'] = 'N';
			$config['template_format'] = 'off';
			$config['template_cod'] = 'off';
		}
		if ($config['COMPACT'] != 'off') {
			if ($config['template_format'] == 'off') $config['template_format'] = 'odt';
			$config['template_block'] = 'all';
			$config['template_block_type'] = 'none';
			$config['template_cod'] = 'td';
			$config['template_map_inside'] = 'N';
		}
		else {
			if ($config['template_autoselect_office'] == 'Y') $config['template_map_inside'] = 'N';
			if ($config['template_format'] == 'off') $config['template_block'] = 'off';
			if ($config['template_block'] == 'off') {
				$config['template_block_type'] = 'none';
				$config['template_map_inside'] = 'N';
			}
			else if ($config['template_block'] != 'all' && $config['template_block_type'] == 'bookmark1') $config['template_block'] = 'auto2';
			if (empty($config['template_map_inside'])) $config['template_map_inside'] = 'N';
			if ($config['template_map_inside'] == 'Y') {
				$config['template_block'] = 'all';
				$config['NO_POST_MAIN'] = 'Y';
			}
		}
		if ($config['map'] != 'Y' && $config['template'] != 'Y') $config['postmap'] = 'N';

		// получение city2 и zip для выбора почтовых отделений на карте
		$edost_locations = false;
		if ($config['postmap'] == 'Y' && ($_SERVER['REQUEST_METHOD'] == 'POST' || !empty($order['bitrix']) || !empty($order['ID'])) && CModule::IncludeModule('edost.locations')) { // class_exists('CEdostLocationsModifySaleOrderAjax')
			$edost_locations = true;
			$c = $config;
			if (!empty($order['bitrix'])) $c['ORDER'] = $order['bitrix'];
			if (!empty($order['ID'])) $c['ORDER_ID'] = $order['ID'];
			$prop2 = self::GetProp2(false, $c);
			if (!empty($prop2['city2'])) $edost_order['location']['city2'] = $GLOBALS['APPLICATION']->ConvertCharset($prop2['city2'], LANG_CHARSET, 'windows-1251');
			if (!empty($prop2['zip'])) $edost_order['location']['zip'] = $prop2['zip'];
			if (!empty($prop2['zip_full'])) $edost_order['location']['zip_full'] = $prop2['zip_full'];
//			echo '<br><b>arResult:</b> <pre style="font-size: 12px">'.print_r($edost_order['location'], true).'</pre>';
		}
		$edost_order['config'] = $config;

		$office_get = $edost_tariff = array();
		$edost_enabled = $post_enabled = $edost_error = false;
		$edost_bitrix_sort = -1;
		$bookmark = ($config['template_block_type'] == 'bookmark1' ? true : false);
		$show_error = (!isset($config['SHOW_ERROR']) || $config['SHOW_ERROR'] ? true : false);
		$no_insurance = (!empty($config['NO_INSURANCE']) && $config['NO_INSURANCE'] == 'Y' ? true : false); // не выводить подпись "со страховкой" + не удалять "со страховкой" из названия тарифа
		$shipment = $order_clone = false;
		$bitrix_17 = (version_compare(SM_VERSION, '17.5.0') >= 0 ? true : false);
		$order_key = (empty($config['CLONE']) && !empty($edost_order['location']['bitrix']) ? implode('|', $edost_order['location']['bitrix']).'|'.(isset($edost_order['location']['city2']) ? $edost_order['location']['city2'] : '').'|'.$edost_order['weight'].'|'.$edost_order['size1'].'|'.$edost_order['size2'].'|'.$edost_order['size3'].'|' : '');

		$shop_main = ($config['COMPACT'] != 'off' && !empty($config['SHOP_MAIN']) && $config['SHOP_MAIN'] == 'Y' ? true : false);
		$post_manual = ($config['postmap'] == 'Y' && !empty($config['POST_MANUAL']) ? true : false); // ручной ввод почтового индекса
		$post_main = ($config['template'] == 'Y' && $config['postmap'] == 'Y' && (empty($config['NO_POST_MAIN']) || $config['NO_POST_MAIN'] != 'Y') ? true : false);
		$post_small = ($config['postmap'] == 'Y' && !empty($config['POST_SMALL']) && $config['POST_SMALL'] == 'Y' ? true : false);
		$post_office_full = false; // в пунктах выдачи есть только почтовые отделения

//		$unsupported_fix = (!empty($config['office_unsupported_fix']) ? intval($config['office_unsupported_fix']) : 0);
//		$unsupported_percent = (!empty($config['office_unsupported_percent']) ? intval($config['office_unsupported_percent']) : 0);

		// сохранение и восстановление выбора для тарифов под закладками
		if (!empty($active['bookmark'])) {
			$s = explode('_', $active['bookmark']);
			if (isset($s[1]) && $s[1] == 's') $active = (isset($_SESSION['EDOST']['delivery_default'][$s[0]]) ? $_SESSION['EDOST']['delivery_default'][$s[0]] : array('id' => '', 'bookmark' => $s[0]));
			else $_SESSION['EDOST']['delivery_default'][$s[0]] = $active;
		}

		// поддержка старого формата с 'PROFILES'
		$ar = array();
		if (!empty($bitrix_data) && is_array($bitrix_data)) foreach ($bitrix_data as $k => $v)
			if (empty($v['PROFILES'])) $ar[] = $v;
			else foreach ($v['PROFILES'] as $k2 => $v2) {
				$v2['NAME'] = $v2['TITLE'];
				$v2['CODE'] = $k.':'.$k2;
				if (!empty($v['LOGOTIP'])) $v2['LOGOTIP'] = $v['LOGOTIP'];
				if ($k !== 'edost') $v2['company'] = $v['TITLE'];
				$ar[] = $v2;
			}
		$bitrix_data = $ar;
//		echo '<br><b>bitrix_data code:</b> <pre style="font-size: 12px">'.print_r($bitrix_data, true).'</pre>';

		// перевод массива тарифов битрикса в собственный формат
		foreach ($bitrix_data as $delivery_key => $delivery) {
			$v = array('name' => '', 'automatic' => '');
			$sort = (isset($delivery['SORT']) ? $delivery['SORT'] : $delivery_key);

			$code = (isset($delivery['CODE']) ? $delivery['CODE'] : '');
			if ($code == '') {
				CDeliveryEDOST::GetAutomatic();
				if (isset(CDeliveryEDOST::$automatic[$delivery['ID']])) $code = CDeliveryEDOST::$automatic[$delivery['ID']]['code'];
			}
			if ($code != '') {
				$s = explode(':', $code);
				if (isset($s[1])) {
					$v['automatic'] = $s[0];
					$v['profile'] = $s[1];
				}
			}

			$v['id'] = $delivery['ID'];

			if (!empty($delivery['OWN_NAME'])) $delivery['NAME'] = $delivery['OWN_NAME'];
			else if ($v['automatic'] == 'edost') {
				CDeliveryEDOST::GetAutomatic();
				$delivery['NAME'] = CDeliveryEDOST::$automatic[$delivery['ID']]['name'];
			}
			$v['name_save'] = $delivery['NAME'];

			if ($v['automatic'] == 'edost') {
				$edost_enabled = true;
				$edost_bitrix_sort = $sort;

				$tariff = CDeliveryEDOST::GetEdostTariff($v['profile']);

				// если от магазина пришел тариф, по которому нет данных в классе, тогда он игнорируется
				if (isset($tariff['error']) && $v['profile'] != 0) continue;

				if (isset($tariff['format'])) {
					$v['format_original'] = $tariff['format'];

					// перенос "до магазина", "до терминала" и "до подъезда" в общие группы
					if ($config['template_map_inside'] == 'Y' || $config['COMPACT'] != 'off') {
						$ar = array('office' => array('terminal'), 'door' => array('house'));
						if (!$shop_main) $ar['office'][] = 'shop';
						foreach ($ar as $k2 => $v2) if ($k2 == 'office' || $config['COMPACT'] != 'off') if (in_array($tariff['format'], $v2)) { $tariff['format'] = $k2; break; }
					}

					if (!empty($config['NO_HOUSE']) && $tariff['format'] == 'house') $tariff['format'] = 'door';

					if ($config['postmap'] == 'Y' && $tariff['format'] == 'post') {
						$post_enabled = true;
						$tariff['format'] = 'postmap';
					}

					if (isset($active['profile']) && $active['profile'] == $v['profile']) $active['format'] = $tariff['format'];
				}

				$v = array_merge($v, self::ParseName($delivery['NAME'], '', $delivery['DESCRIPTION'], !$no_insurance ? $sign['insurance'] : ''));

				if (!isset($tariff['error'])) {
					if (!empty($config['COD_DISABLE'])) {
						$tariff['pricecash'] = -1;
						$tariff['transfer'] = 0;
						if (isset($tariff['priceoriginal']['pricecash'])) $tariff['priceoriginal']['pricecash'] = -1;
					}

					$v['tariff_id'] = $v['ico'] = $tariff['id'];
					$edost_tariff[ $v['tariff_id'] ] = true;

					if ($config['template_ico'] == 'T2' && !empty($delivery['LOGOTIP']['SRC'])) $v['ico'] = $delivery['LOGOTIP']['SRC'];
					else if ($config['template_ico'] == 'C') $v['company_ico'] = self::GetCompanyIco(!empty($tariff['company_id']) ? $tariff['company_id'] : 0, $v['tariff_id']);

					$ar = array('day', 'insurance', 'company_id', 'format', 'sort');
					foreach ($ar as $k) $v[$k] = $tariff[$k];
//					if (!empty($tariff['priceoriginal'])) $v['priceoriginal'] = $tariff['priceoriginal'];

					if ($v['tariff_id'] == 3 && empty($edost_order['location']['bitrix']['city'])) $v['warning'] = $sign['ems_warning'];

					$price_discount = false;
					if (isset($delivery['DELIVERY_DISCOUNT_PRICE'])) $price_discount = $delivery['DELIVERY_DISCOUNT_PRICE'];
					else if (!empty($order['bitrix']) && $config['sale_discount'] == 'Y') {
						if (empty($order_clone)) $order_clone = $order['bitrix']->createClone();
						$order_clone->isStartField();
						$shipment_clone = CDeliveryEDOST::GetShipment($order_clone);
						if (!empty($shipment_clone)) {
							$shipment_clone->setField('CUSTOM_PRICE_DELIVERY', 'N');
							$shipment_clone->setField('DELIVERY_ID', $v['id']);
							$order_clone->getShipmentCollection()->calculateDelivery(); // берется первая отгрузка
							$order_clone->doFinalAction(true);
							$p = $order_clone->getDeliveryPrice();
							if ($p >= 0) $price_discount = $p;
						}
					}
					if ($price_discount !== false) {
						$price_discount = self::GetPrice('value', $price_discount, $currency, $base_currency);
						$price_original = $tariff['price'];
					}

					if (in_array($v['format'], CDeliveryEDOST::$office_key)) $office_get[$v['company_id']] = $v['company_id'];

					$v_save = $v;
					$s = array();
					if (!empty($tariff['priceoffice'])) foreach ($tariff['priceoffice'] as $v2) {
						$o = $tariff;
						$o['to_office'] = $v2['type'];
						$key = array('price', 'priceinfo', 'pricecash', 'priceoriginal');
						foreach ($key as $p) if (isset($v2[$p])) $o[$p] = $v2[$p];
						if (!empty($config['COD_DISABLE'])) {
							$o['pricecash'] = -1;
							if (isset($o['priceoriginal']['pricecash'])) $o['priceoriginal']['pricecash'] = -1;
						}
						$s[] = $o;
					}
					$s[] = $tariff;
					foreach ($s as $tariff) {
						$v = $v_save;

						// скидки из личного кабинета eDost
						if ($config['edost_discount'] == 'Y' && !empty($tariff['priceoriginal']['price'])) {
							if ($tariff['priceoriginal']['price'] - $tariff['price'] > 5) {
								$v += self::GetPrice('price_original', $tariff['priceoriginal']['price'], $base_currency, $currency);
								$v += self::GetPrice('pricetotal_original', $tariff['priceoriginal']['price'] + $tariff['priceinfo'], $base_currency, $currency);
							}
							if ($tariff['pricecash'] >= 0 && isset($tariff['priceoriginal']['pricecash']) && $tariff['priceoriginal']['pricecash'] > 0 && ($tariff['priceoriginal']['pricecash'] - $tariff['pricecash'] > 5)) {
								$v += self::GetPrice('pricecod_original', $tariff['priceoriginal']['pricecash'] + $tariff['transfer'], $base_currency, $currency);
								$v += self::GetPrice('pricecash_original', $tariff['priceoriginal']['pricecash'], $base_currency, $currency);
							}
						}

						// скидки битрикса
						if ($price_discount !== false) {
							$v['price_discount'] = array($price_original, $price_discount);

							if ($tariff['pricecash'] > 0 && $config['sale_discount_cod'] != 'off') {
								$pricecash_discount = edost_class::SetDiscount($tariff['pricecash'], $price_original, $price_discount, $config['sale_discount_cod']);
								$v['pricecash_discount'] = array($tariff['pricecash'], $pricecash_discount);
								if ($tariff['pricecash'] - $pricecash_discount > 5) {
									$v += self::GetPrice('pricecod_original', $tariff['pricecash'] + $tariff['transfer'], $base_currency, $currency);
									$v += self::GetPrice('pricecash_original', $tariff['pricecash'], $base_currency, $currency);
								}
								$tariff['pricecash'] = $pricecash_discount;
							}

							if ($tariff['price'] > 0 && $price_original - $price_discount > 5) {
								$v += self::GetPrice('price_original', $tariff['price'], $base_currency, $currency);
								$v += self::GetPrice('pricetotal_original', $tariff['price'] + $tariff['priceinfo'], $base_currency, $currency);
							}
							$tariff['price'] = (isset($tariff['to_office']) ? edost_class::SetDiscount($tariff['price'], $price_original, $price_discount) : $price_discount);
	                	}

						$v += self::GetPrice('price', $tariff['price'], $base_currency, $currency);
						$v += self::GetPrice('pricetotal', $tariff['price'] + $tariff['priceinfo'], $base_currency, $currency);

						if ($tariff['priceinfo'] > 0) $v += self::GetPrice('priceinfo', $tariff['priceinfo'], $base_currency, $currency);
						if ($tariff['pricecash'] >= 0) {
							$v += self::GetPrice('pricecod', $tariff['pricecash'] + $tariff['transfer'], $base_currency, $currency);
							$v += self::GetPrice('pricecash', $tariff['pricecash'], $base_currency, $currency);
							$v += self::GetPrice('transfer', $tariff['transfer'], $base_currency, $currency);
						}

						if (isset($tariff['to_office'])) {
							$v['to_office'] = $tariff['to_office'];
							$data[] = $v;
						}
					}
				}
				else {
					if (!$show_error) continue;

					$edost_error = true;
					$v['error'] = ($config['hide_error'] != 'Y' ? $tariff['error'] : '');
					$v['price'] = 0;
					$v['ico'] = 0;
					if ($config['template_ico'] == 'C') $v['company_ico'] = 0;
				}
			}
			else {
				$v = array_merge($v, self::ParseName($delivery['NAME'], isset($delivery['company']) ? $delivery['company'] : '', $delivery['DESCRIPTION']));
				$v['bitrix_sort'] = $sort;
				if (!empty($delivery['LOGOTIP']['SRC'])) $v['ico'] = $delivery['LOGOTIP']['SRC'];

				if (!in_array($config['template'], array('Y', 'N2'))) $v['price'] = (isset($delivery['PRICE']) ? floatval($delivery['PRICE']) : 0);
				else {
					$tariff = array();
					$sale_discount = ($config['sale_discount'] == 'Y' ? true : false);
					$price_original = false;

					if (isset($delivery['CALCULATE_ERRORS'])) $tariff = array('ERROR' => $delivery['CALCULATE_ERRORS']);
					else if (isset($delivery['PRICE']) || isset($delivery['DELIVERY_DISCOUNT_PRICE'])) {
						$tariff += array('VALUE' => isset($delivery['DELIVERY_DISCOUNT_PRICE']) ? $delivery['DELIVERY_DISCOUNT_PRICE'] : $delivery['PRICE']);
						if (isset($delivery['DELIVERY_DISCOUNT_PRICE'])) $price_original = $delivery['PRICE'];
						if (isset($delivery['PERIOD_TEXT'])) $tariff += array('TRANSIT' => $delivery['PERIOD_TEXT']);
						if (isset($delivery['PERIOD'])) $tariff += $delivery['PERIOD'];
					}
					else if (isset($order['WEIGHT']) || !empty($order['bitrix'])) { // 'WEIGHT' - набор параметров (старый формат),  'bitrix' - объект заказа (новый формат)
						$p = false;
						$service = \Bitrix\Sale\Delivery\Services\Manager::getObjectById($delivery['ID']);

						if (empty($order['bitrix'])) {
							if ($shipment === false) $shipment = CSaleDelivery::convertOrderOldToNew($order);
							$sale_discount = false;
						}
						else {
							if (empty($order_clone)) $order_clone = $order['bitrix']->createClone();
							$order_clone->isStartField();
							$shipment = CDeliveryEDOST::GetShipment($order_clone);
							if (get_class($service) === 'Sale\Handlers\Delivery\AdditionalProfile') $sale_discount = true;
						}

						if (!$sale_discount) $p = $service->calculate($shipment);
						else {
							// расчет с учетом скидок из правил работы с корзиной

							if (!empty($shipment)) {
								$shipment->setField('CUSTOM_PRICE_DELIVERY', 'N');
								$shipment->setField('DELIVERY_ID', $v['id']);

								$ar = $order_clone->getShipmentCollection()->calculateDelivery();
								if ($show_error || !$bitrix_17) $p = $service->calculate($shipment);
								else if ($ar->isSuccess()) {
									$p = $ar->get('CALCULATED_DELIVERIES');
									$p = reset($p);
								}

								$order_clone->doFinalAction(true);
							}
						}

						if (empty($p)) $tariff += array('ERROR' => $sign['delivery_error']);
						else if ($p->isSuccess()) {
							$tariff += array('VALUE' => $p->getPrice(), 'TRANSIT' => $p->getPeriodDescription());
							if (!$service->isProfile()) {
								$s = $service->getConfig();
								if (!empty($s['MAIN']['ITEMS']['PERIOD']['ITEMS'])) {
									$s = $s['MAIN']['ITEMS']['PERIOD']['ITEMS'];
									$tariff += array('PERIOD_FROM' => $s['FROM']['VALUE'], 'PERIOD_TO' => $s['TO']['VALUE'], 'PERIOD_TYPE' => $s['TYPE']['VALUE']);
								}
							}
							if ($sale_discount) {
								$s = $order_clone->getDeliveryPrice();
								if ($s >= 0) {
									$price_original = $tariff['VALUE'];
									$tariff['VALUE'] = $s;
								}
							}
						}
						else {
							$error = $p->getErrorMessages();
							if (empty($error)) $error = array($sign['delivery_error']);
							$tariff += array('ERROR' => implode('<br>', $error));
						}
					}

					if (isset($tariff['VALUE'])) {
						$v += self::GetPrice('price', $tariff['VALUE'], '', $currency);
						if ($price_original !== false && ($price_original - $tariff['VALUE'] > 5)) {
							$v += self::GetPrice('price_original', $price_original, '', $currency);
							$v += self::GetPrice('pricetotal_original', $price_original, '', $currency);
						}

						if (isset($tariff['PERIOD_TYPE'])) $v['day'] = self::GetDay($tariff['PERIOD_FROM'], $tariff['PERIOD_TO'], $tariff['PERIOD_TYPE']);
						else if (!empty($tariff['TRANSIT'])) {
							$s = self::ParseDay($tariff['TRANSIT'], $sign['to']);
							$v['day'] = self::GetDay($s[0], $s[1]);
						}
					}
					else {
						if (!$show_error) continue;

						$v['error'] = (isset($tariff['ERROR']) ? $tariff['ERROR'] : '');
						$v['price'] = 0;
					}
				}
			}

			$data[] = $v;
		}
//		echo '<br><b>DELIVERY start:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';
//		echo '<br><b>automatic:</b> <pre style="font-size: 12px">'.print_r(CDeliveryEDOST::$automatic, true).'</pre>';


		// создание для наложенного платежа отдельных тарифов
		foreach ($data as $k => $v) $data[$k]['cod_tariff'] = false;
		if ($config['template_cod'] == 'tr') {
			$ar = array();
			foreach ($data as $k => $v) if ($v['automatic'] == 'edost' && isset($v['pricecash'])) {
				$a = true;
				foreach ($ar as $k2 => $v2) if ($v2['tariff_id'] == $v['tariff_id'] && (!isset($v2['to_office']) && !isset($v['to_office']) || isset($v2['to_office']) && isset($v['to_office']) && $v2['to_office'] == $v['to_office'])) {
					$a = false;
					if ($v2['sort'] <= $v['sort']) $ar[$k2]['sort'] = $v['sort'] + 1;
				}
				if (!$a) continue;

				$v['cod_tariff'] = true;
				$v['sort']++;
				$v['price'] = $v['pricetotal'] = $v['pricecash'];
				$v['price_formatted'] = $v['pricetotal_formatted'] = $v['pricecash_formatted'];
				if ($v['insurance'] == 0 && !in_array($v['tariff_id'], CDeliveryEDOST::$tariff_shop)) $v['insurance'] = 1; // обязательная страховка
				if (!empty($v['transfer'])) $v['warning'] = str_replace('%transfer%', $v['transfer_formatted'], $sign['transfer']);

				if (isset($v['price_original'])) {
					$v['price_original'] = $v['pricetotal_original'] = $v['pricecod_original'];
					$v['price_original_formatted'] = $v['pricetotal_original_formatted'] = $v['pricecod_original_formatted'];
				}

				$ar[] = $v;
			}
			if (!empty($ar)) $data = array_merge($data, $ar);
		}
//		echo '<br><b>DELIVERY start + cod:</b> <pre style="font-size: 12px">'.print_r($data, true).'</pre>';


		// удаление нулевого тарифа, если есть другие способы доставки
		if ($edost_error && $config['hide_error'] == 'Y' && count($data) > 1)
			foreach ($data as $k => $v) if ($v['automatic'] == 'edost') unset($data[$k]);


		// восстановление офиса из профиля покупателя
		if (isset($_SESSION['EDOST']['office_default']['profile'])) {
			$ar = $_SESSION['EDOST']['office_default']['profile'];
			foreach ($data as $k => $v)
				if ($v['automatic'] == 'edost' && $v['profile'] == $ar['profile'] && $v['cod_tariff'] == $ar['cod_tariff'] && !isset($_SESSION['EDOST']['office_default'][$v['format']]))
					$_SESSION['EDOST']['office_default'][$v['format']] = $ar;
			unset($_SESSION['EDOST']['office_default']['profile']);
		}


		// установка глобальных кодов сортировки + формат для тарифов битрикса
		foreach ($data as $k => $v)
			if ($v['automatic'] == 'edost') $data[$k]['sort'] += $edost_bitrix_sort*1000;
			else if (isset($v['bitrix_sort'])) {
				$data[$k]['sort'] = ($v['bitrix_sort'] + ($v['bitrix_sort'] < $edost_bitrix_sort ? 0 : 1))*1000 + $k;
				$data[$k]['format'] = 'bitrix_'.($v['bitrix_sort'] < $edost_bitrix_sort ? 1 : 2);
			}


		// сортировка
		if ($config['template'] != 'Y' || $config['template_format'] == 'off') $sorted = false;
		else {
			self::SortTariff($data, $config);
			$sorted = true;
		}


		// группы тарифов
		$ar = array(
			'odt' => array('shop', 'office', 'terminal', 'door', 'house', 'postmap', 'post', 'general'),
			'dot' => array('door', 'house', 'shop', 'office', 'terminal', 'postmap', 'post', 'general'),
			'tod' => array('postmap', 'post', 'shop', 'office', 'terminal', 'door', 'house', 'general'),
		);
		$ar = (isset($ar[$config['template_format']]) ? $ar[$config['template_format']] : $ar['odt']);
		$ar = array_merge(array('bitrix_1'), $ar, array('bitrix_2'));
		$format = array_fill_keys($ar, '');
		$format_data = GetMessage('EDOST_DELIVERY_FORMAT');
		foreach ($format as $f_key => $f) {
			$f = (isset($format_data[$f_key]) ?  $format_data[$f_key] : array());
			if (!isset($f['name'])) $f['name'] = '';
			$f['data'] = array();
			$format[$f_key] = $f;
		};

		// распределение тарифов по группам
		foreach ($data as $k => $v) {
			$f_key = ($config['template'] == 'Y' && !empty($v['format']) && isset($format[$v['format']]) ? $v['format'] : 'general');
			$format[$f_key]['data'][] = $v;
		}
//		echo '<br><b>FORMAT start:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// модификация названий тарифов
		$hide = array();
		foreach ($sign['hide'] as $v) $hide[] = '- '.$v;
		$hide = array_merge($hide, $sign['hide']);
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			// удаление названия тарифа, если у всех тарифов компании одинаковые названия (или тариф только один)
			if ($config['template'] == 'Y' && empty($config['NAME_NO_CHANGE'])) {
				$n = count($f['data']);
				for ($i = 0; $i < $n; $i++) if (!isset($f['data'][$i]['deleted'])) {
					$p = $p2 = 0;
					for ($i2 = $i+1; $i2 < $n; $i2++) if ($f['data'][$i]['company'] == $f['data'][$i2]['company']) {
						$p++;
						if ($f['data'][$i]['name'] == $f['data'][$i2]['name']) $p2++;
						$f['data'][$i2]['deleted'] = true;
					}
					if ($p == $p2) for ($i2 = $i; $i2 < $n; $i2++) if ($f['data'][$i]['company'] == $f['data'][$i2]['company']) $format[$f_key]['data'][$i2]['name'] = '';
				}
			}

			// удаление из названия тарифа текста 'курьером до двери', 'до пункта выдачи', ...
			if (empty($config['NAME_NO_CHANGE']) || in_array($f_key, array('office', 'terminal', 'post', 'postmap'))) {
				foreach ($format[$f_key]['data'] as $k => $v) if ($v['name'] != '' && in_array($v['format'], array('door', 'office', 'terminal', 'house', 'post', 'postmap'))) {
					$s = trim(str_replace($hide, '', $v['name']));
					if ($config['template'] == 'Y') $format[$f_key]['data'][$k]['name'] = $s;
				}
			}
		}
//		echo '<br><b>FORMAT name:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// фирменный виджет PickPoint
		if ($config['map'] == 'Y' && !in_array($config['template'], array('Y', 'N3')) && (!defined('DELIVERY_EDOST_PICKPOINT_WIDGET') || DELIVERY_EDOST_PICKPOINT_WIDGET == 'Y')) $r['pickpoint_widget'] = $edost_order['pickpoint_widget'] = true;

//		echo '<br><b>office:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';

		// загрузка офисов с сервера edost
		$office = $office_param = array();
		$office_error = false;
		if ($config['map'] == 'Y' || $config['template'] != 'Y') {
			$office = self::GetOffice($edost_order, $office_get);
//		echo '<br><b>get office:</b> <pre style="font-size: 12px">'.print_r($office, true).'</pre>';
			if (isset($office['error']) && $office['error'] != 5) $office_error = $office['error'];
			if (!empty($r['pickpoint_widget']) && !empty($office['pickpointmap'])) $r['pickpointmap'] = $office['pickpointmap'];
			if (!empty($office['office'])) $office_param = $office['office'];
			$office = (!empty($office['data']) ? $office['data'] : array());

			// удаление офисов для которых нет тарифов по type2
			$s = array();
			foreach ($format as $f_key => $f) if (!empty($f['data'])) foreach ($f['data'] as $k => $v) if (!empty($office_param[ $v['company_id'] ]['type2']) && isset($v['to_office'])) $s[$v['company_id']][$v['to_office']] = true;
			foreach ($s as $k => $v) if (!empty($office[$k])) foreach ($office[$k] as $o_key => $o) if ($o['type2'] !== '' && !isset($v[$o['type2']])) unset($office[$k][$o_key]);

			if ($post_enabled) {
				$key = false;
				if ($post_manual || $post_small && !empty($edost_order['location']['city2']) || !isset($office[23]) || count($office[23]) < 2) $key = 'post'; // перенос почтовых тарифов из 'postmap' в 'post' (если почтовых отделений меньше двух или включен ручной выбор)
				else if (!$post_main && $config['COMPACT'] != 'off') {
					// перенос почтовых тарифов из 'postmap' в 'office' (по настройке в шаблоне eDost)
					$key = 'office';
					if (count($office) == 1) {
						$post_office_full = true;
						$format['office']['name'] = $format['postmap']['name'];
					}
				}
				if ($key) {
					if ($config['template'] == 'Y') {
						foreach ($format['postmap']['data'] as $v) {
							$v['format'] = $key;
							$format[$key]['data'][] = $v;
							if (isset($active['profile']) && $active['profile'] == $v['profile']) $active['format'] = $key;
						}
						$format['postmap']['data'] = array();
					}
					else {
						foreach ($format['general']['data'] as $k => $v) if ($v['format'] == 'postmap') $format['general']['data'][$k]['format'] = 'post';
					}
				}
			}

			// разделение офисов по тарифам + добавление 'office_key'
			if (!empty($office)) {
				$tariff = array();
				foreach ($format as $f_key => $f) if (!empty($f['data'])) foreach ($f['data'] as $k => $v) if (!empty($v['tariff_id']) && in_array($v['format'], CDeliveryEDOST::$office_key)) {
					$tariff[$v['tariff_id']] = $v['tariff_id'];
					$format[$f_key]['data'][$k]['office_key'] = $v['company_id'];
				}
				if (!empty($office_param)) foreach ($office_param as $p_key => $p) if (!empty($p['tariff']) && !empty($office[$p_key])) {
					$s = $office[$p_key];
					unset($office[$p_key]);
					foreach ($p['tariff'] as $k2 => $v2) if (count(array_intersect($tariff, $v2)) != 0) {
						$office_key = $p_key.'_'.$k2;
						foreach ($s as $k => $v) if (!empty($v['limit']['tariff']) && ($v['limit']['tariff'] & (1 << $k2)) != 0) $office[$office_key][$k] = $v;
						if (!empty($office[$office_key]))
							foreach ($format as $f_key => $f) if (!empty($f['data'])) foreach ($f['data'] as $k => $v) if (!empty($v['tariff_id']) && in_array($v['tariff_id'], $v2))
								$format[$f_key]['data'][$k]['office_key'] = $office_key;
					}
				}
			}

			if ($config['template'] != 'Y') $r['office'] = $office;
		}
//		echo '<br><b>office:</b> <pre style="font-size: 12px">'.print_r($office, true).'</pre>';


		// тарифы с наложенным платежом без страховки у которых есть аналог со страховкой
		foreach ($format as $f_key => $f) if (!empty($f['data'])) foreach ($f['data'] as $k => $v) if (isset($v['pricecod']) && $v['insurance'] == 1)
			foreach ($f['data'] as $k2 => $v2) if ($k != $k2 && isset($v2['pricecod']) && $v['tariff_id'] == $v2['tariff_id'] && $v2['insurance'] != 1) {
				if ($v['pricecod'] >= $v2['pricecod']) $format[$f_key]['data'][$k]['cod_hide'] = true;
				else $format[$f_key]['data'][$k2]['cod_hide'] = true;
				break;
			}

		$delivery_bonus = (!empty($config['DELIVERY_BONUS']) || $config['PRIORITY'] == 'B' || $config['PRIORITY'] == 'C' ? true : false);
		$cod_filter = (!empty($config['COD_FILTER']) ? true : false);
		$cod_filter_zero_tariff = ($config['PRIORITY'] == 'C' && (empty($config['COD_FILTER_ZERO_TARIFF']) || $config['COD_FILTER_ZERO_TARIFF'] == 'Y') ? true : false);
		$zero_tariff = ($config['hide_error'] != 'Y' || $config['show_zero_tariff'] == 'Y' ? true : false);

		if ($delivery_bonus || $cod_filter) {
			$office_id = array();
			$tariff_id = array('delete' => array(), 'cod' => array());

			// офисы без наложки
			foreach ($format as $f_key => $f) if (in_array($f_key, CDeliveryEDOST::$office_key) && !empty($f['data'])) foreach ($f['data'] as $k => $v) {
				$id = $v['office_key'];
				if (!isset($office[$id])) continue;

				// превышена максимально допустимая сумма перевода или невозможна оплата при получении
				if (isset($v['pricecash'])) foreach ($office[$id] as $o_key => $o) if (isset($o['codmax']) && $v['pricecash'] > $o['codmax'] || !empty($o['cod_disable'])) $office_id[$o_key] = array($id, $o_key);

				// эксклюзивный тариф
				if (!isset($v['pricecash']) && isset($v['to_office'])) foreach ($office[$id] as $o_key => $o) if ($o['type2'] == $v['to_office']) $office_id[$o_key] = array($id, $o_key);
			}

			// тарифы с наложкой                                                                                                                                      && !isset($v['error'])
			foreach ($format as $f_key => $f) if (!empty($f['data'])) foreach ($f['data'] as $k => $v) $tariff_id[(!isset($v['pricecash']) || !empty($v['cod_hide'])) ? 'delete' : 'cod'][] = array($f_key, $k);

			// бонусы предоплаты
			if ($delivery_bonus) $r['bonus'] = array('cod' => array(), 'normal' => array());
		}

		// сравнение тарифов с наложкой и без наложки (для приоритета оплаты при получении и вывода бонусов у способов оплаты)
		for ($i_compare = 1; $i_compare <= 3; $i_compare++) {
			if ($i_compare == 2 && !$delivery_bonus || $i_compare == 3 && !($cod_filter && !empty($r['cod_delete']))) break;

//			$_SESSION['EDOST']['run22aa2'][] = 'i_compare '.$i_compare;

			if ($i_compare == 1) $original = array($format, $office, $active);
			else { $format = $original[0]; $office = $original[1]; $active = $original[2]; }

			// удаление офисов и тарифов без наложки + замена 'price' на 'pricecod'
			if ($i_compare == 1 && $cod_filter && !$delivery_bonus || $i_compare == 1 && !$cod_filter && $delivery_bonus || $i_compare == 2 && $cod_filter) {
				foreach ($office_id as $k) unset($office[$k[0]][$k[1]]);
				foreach ($tariff_id['delete'] as $k) unset($format[$k[0]]['data'][$k[1]]);

				$n = count($tariff_id['cod']);
				foreach ($tariff_id['cod'] as $i => $k) {
					$v = $format[$k[0]]['data'][$k[1]];

					$v['price'] = $v['pricetotal'] = $v['pricecod'];
					$v['price_formatted'] = $v['pricetotal_formatted'] = $v['pricecod_formatted'];

					if (isset($v['price_original']))
						if (!isset($v['pricecod_original'])) {
							unset($v['price_original']);
							unset($v['price_original_formatted']);
							unset($v['pricetotal_original']);
							unset($v['pricetotal_original_formatted']);
						}
						else {
							$v['price_original'] = $v['pricetotal_original'] = $v['pricecod_original'];
							$v['price_original_formatted'] = $v['pricetotal_original_formatted'] = $v['pricecod_original_formatted'];
						}

					unset($v['pricecod']);
					$format[$k[0]]['data'][$k[1]] = $v;
				}
			}
//			echo '<br><b>FORMAT name:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';
//			echo '<br><b>office_id:</b> <pre style="font-size: 12px">'.print_r($office_id, true).'</pre>';
//			echo '<br><b>tariff_id:</b> <pre style="font-size: 12px">'.print_r($tariff_id, true).'</pre>';
//			echo '<br><b>tariff_id:</b> <pre style="font-size: 12px">'.print_r($active, true).'</pre>';

			$active_id = (isset($active['id']) ? $active['id'] : '');
			$active_profile = (isset($active['profile']) ? $active['profile'] : '');
			$active_cod = (!empty($active['cod_tariff']) ? true : false);
			$active_bookmark = (!empty($active['bookmark']) ? $active['bookmark'] : '');

			$ar = (isset($_SESSION['EDOST']['office_default']) ? $_SESSION['EDOST']['office_default'] : array());
			if (isset($active['format']) && !empty($active['office_id'])) {
				$ar[$active['format']] = $ar['all'] = array('id' => $active['office_id'], 'profile' => $active['profile'], 'cod_tariff' => $active_cod);
				if ($i_compare == 1 && !$delivery_bonus || $i_compare == 2) $_SESSION['EDOST']['office_default'] = $ar;
			}
			// отключение выбранного офиса, если он находится в соседнем населенном пункте (city != '')
			if ($edost_locations) {
				$key = array('postmap', 'office', 'shop');
				foreach ($key as $k) if (!empty($ar[$k]) && ($k == 'postmap' && empty($config['NO_POST_CITY_UNSET']) || in_array($k, array('office', 'shop'))))
					foreach ($office as $v) if (!empty($v[ $ar[$k]['id'] ]['city'])) {
						if (!empty($ar['all']) && $ar['all']['id'] == $ar[$k]['id']) unset($ar['all']);
						if (!empty($active['office_id']) && $active['office_id'] == $ar[$k]['id']) $active_id = false;
						unset($ar[$k]);
						break;
					}
			}
			$active_office = $ar;

			$active = false; // активный тариф

			// проверка на существование выбранных офисов + определение 'type'
			foreach ($active_office as $k => $v) foreach ($office as $o) if (isset($o[$v['id']])) {
				$active_office[$k]['type'] = $o[$v['id']]['type'];
				$active_office[$k]['type2'] = $o[$v['id']]['type2'];
				break;
			}

			// удаление тарифов без офисов для стандартного шаблона
			if ($config['template'] != 'Y') foreach ($format['general']['data'] as $k => $v) if (isset($v['format']) && in_array($v['format'], CDeliveryEDOST::$office_key))
				if (($v['company_id'] != 26 || empty($r['pickpoint_widget'])) && empty($office[$v['office_key']]) || $v['company_id'] == 26 && !empty($r['pickpoint_widget']) && empty($r['pickpointmap'])) unset($format['general']['data'][$k]);

			// удаление тарифов без офисов для шаблона eDost + выделение активного тарифа (эксклюзивного)
			foreach ($format as $f_key => $f) if (in_array($f_key, CDeliveryEDOST::$office_key) && !empty($f['data'])) {
				// количество офисов у каждого тарифа (сначала с эксклюзивной ценой, затем остальные)
				$office_count = array();
				$office_count_total = 0;
				for ($i = 0; $i <= 1; $i++) foreach ($f['data'] as $k => $v) {
					$id = $v['office_key'];
					if (!isset($office_count[$id])) {
						$office_count[$id]['total'] = (isset($office[$id]) ? count($office[$id]) : 0);
						$office_count_total += $office_count[$id]['total'];
					}

					if ($i == 0 && isset($v['to_office'])) {
						$n = 0;
						if (isset($office[$id])) foreach ($office[$id] as $o) if ($o['type2'] == $v['to_office']) $n++;
						$f['data'][$k]['office_count'] = $n;
						$office_count[$id][$v['to_office']] = $n;

						// выделение активного тарифа (эксклюзивного)
						if ($n > 0 && isset($active_office[$f_key]['type2']) && $v['profile'] == $active_office[$f_key]['profile'] && $v['cod_tariff'] == $active_office[$f_key]['cod_tariff'] && $v['to_office'] == $active_office[$f_key]['type2']) {
							if (self::GetBitrixID($v) == $active_id) {
								$f['data'][$k]['checked'] = true;
								$active = $v;
							}
							$active_office[$f_key]['tariff_key'] = $k;
						}
					}
					else if ($i == 1 && !isset($v['to_office'])) {
						$n = $office_count[$id]['total'];
						foreach ($office_count[$id] as $k2 => $v2) if ($k2 !== 'total') $n -= $v2;
						$f['data'][$k]['office_count'] = $n;
					}
				}

				foreach ($f['data'] as $k => $v) if ($v['office_count'] == 0) unset($f['data'][$k]);
				if ($office_count_total > 0) $f['office_count'] = $office_count_total;

				$format[$f_key] = $f;
			}

			// отключение наложенного платежа
			if ($config['PRIORITY'] == 'C') {
				$count = 0;
				foreach ($format as $f_key => $f) if (!empty($f['data'])) $count += count($f['data']);
				if ($count == 0 && ($i_compare == 1 && !$cod_filter || $i_compare == 2 && $cod_filter) && (!$zero_tariff || !$cod_filter_zero_tariff)) $r['cod_delete'] = true;
			}

			// поиск бонусов (количество пунктов выдачи и самые минимальные цены по группам для обычной доставки и с наложенным платежом)
			if ($delivery_bonus)
				if ($i_compare == 1) $format2 = $format;
				else if ($i_compare == 2) {
					$ar = ($cod_filter ? array($format, $format2) : array($format2, $format)); // первый элемент с наложкой
					for ($i = 0; $i <= 1; $i++) {
						$office_count = 0;
						foreach ($ar[$i] as $f_key => $f) if (!empty($f['data'])) {
							$office_count += (!empty($f['office_count']) ? $f['office_count'] : 0);
							$min = -1;
							foreach ($f['data'] as $k => $v) if (isset($v['pricetotal']) && ($min == -1 || $v['pricetotal'] < $min)) $min = $v['pricetotal'];
							$ar[$i][$f_key]['min'] = $min;
							if ($min >= 0) $r['bonus'][$i == 0 ? 'cod' : 'normal'][$f_key] = $min;
						}
						if ($office_count != 0) $r['bonus'][$i == 0 ? 'cod' : 'normal']['office_count'] = $office_count;
					}
				}
		}
		if (!empty($r['cod_delete'])) $cod_filter = false;


		// выделение активного тарифа (не эксклюзивного)
		foreach ($format as $f_key => $f) foreach ($f['data'] as $k => $v) if (!isset($v['to_office'])) {
			if ($active === false && self::GetBitrixID($v) == $active_id && ($v['automatic'] !== 'edost' || $v['cod_tariff'] == $active_cod)) {
				$format[$f_key]['data'][$k]['checked'] = true;
				$active = $v;
			}
			if (isset($active_office[$f_key]['type2']) && !isset($active_office[$f_key]['tariff_key']) && $v['profile'] == $active_office[$f_key]['profile'] && $v['cod_tariff'] == $active_office[$f_key]['cod_tariff']) $active_office[$f_key]['tariff_key'] = $k;
		}
//		echo '<br><b>active:</b> <pre style="font-size: 12px">'.print_r($active, true).'</pre>';
//		echo '<br><b>active_office:</b> <pre style="font-size: 12px">'.print_r($active_office, true).'</pre>';


		// проверка на наличие 'priceinfo'
		foreach ($format as $f_key => $f) if (!empty($f['data']))
			foreach ($f['data'] as $k => $v) if (isset($v['priceinfo'])) $priceinfo = true;


		// данные для карты
		if ($config['map'] == 'Y' && !empty($office)) {
			if ($order_key != '') {
				$map_key = $order_key.$edost_order['price'].'|'.(!empty($config['COD_FILTER']) ? 'cod_filter' : '').'|'.(!empty($config['PAY_SYSTEM_ID']) ? $config['PAY_SYSTEM_ID'] : '').'|'.(!empty($config['COD_DISABLE']) ? $config['COD_DISABLE'] : '').'|'.($post_manual ? 'Y' : '');
				if (!isset($_SESSION['EDOST']['map_key']) || $_SESSION['EDOST']['map_key'] != $map_key) $r['map_update'] = true;
				$_SESSION['EDOST']['map_key'] = $map_key;
			}

			$s = array('format' => $format, 'office' => $office, 'config' => $config, 'location' => $edost_order['location'], 'sorted' => $sorted, 'currency' => $currency);
			$r['map_json'] = self::GetOfficeJson($s);

			if (!empty($config['MAP_DATA'])) $r['map_data'] = $s;
		}

		// упаковка группы с офисами в один тариф (фиксированный или с выбором на карте)
		$office_main = array('office');
		if ($post_main) $office_main[] = 'postmap';
		if ($shop_main) $office_main[] = 'shop';
		foreach ($office_main as $i_key) {
			$tariff_count = $office_count = 0;
			$ico = false;

			$f2 = $format[$i_key];
			$f2['data'] = array();
			$f2['office_count'] = 0;
			$f2['head'] = $sign['bookmark'][$i_key];
			foreach ($format as $f_key => $f) if (isset($f['office_count']) && ($i_key == 'office' && ($f_key != 'postmap' || !$post_main) && ($f_key != 'shop' || !$shop_main) || $f_key == $i_key)) {
				$n = count($f['data']);
				$tariff_count += $n;
				$office_count += $f['office_count'];

				// наличие активного тарифа
				$checked = false;
				foreach ($f['data'] as $v) if (isset($v['checked'])) { $checked = true; break; }

				self::FormatRange($f, $currency, $config['template_cod'] != 'off' ? true : false);
				if ($ico === false && !empty($f['ico'])) $ico = $f['ico'];

				// установка общего офиса интегрированной карты по уже выбранному из группы
				if (($config['template_map_inside'] == 'Y' || $config['COMPACT'] != 'off') && empty($active_office['all']) && isset($active_office[$f_key]['tariff_key'])) $active_office['all'] = $active_office[$f_key];

				$zip = ($config['postmap'] == 'Y' && !empty($edost_order['location']['zip_full']) && !empty($edost_order['location']['zip']) && ($f_key == 'postmap' || $f_key == 'office' && $post_office_full) ? $edost_order['location']['zip'] : false);

				// выделение единственного офиса (или самого первого, если включено в настройках модуля 'template_autoselect_office')
				if (!isset($active_office[$f_key]['tariff_key']) && ($f['office_count'] == 1 && ($n == 1 || $config['COMPACT'] != 'off') || $config['template_autoselect_office'] == 'Y' || $zip)) {
					$k = $f['min']['key'];
					$v = $f['data'][$k];
					$id = false;
					if ($zip) {
						if (!empty($office[$v['office_key']][$zip])) $id = $zip;
					}
					else if (isset($v['to_office'])) {
						foreach ($office[$v['office_key']] as $o) if ($o['type2'] == $v['to_office']) { $id = $o['id']; break; }
					}
					else foreach ($office[$v['office_key']] as $o) {
						$a = true;
						foreach ($f['data'] as $k2 => $v2) if ($k2 !== $k && $v2['company_id'] == $v['company_id'] && isset($v2['to_office']) && $v2['to_office'] == $o['type2']) $a = false;
						if ($a) { $id = $o['id']; break; }
					}
					if (!$zip || $id) $active_office[$f_key] = array('id' => $id, 'profile' => $v['profile'], 'cod_tariff' => $v['cod_tariff'], 'type' => $office[$v['office_key']][$id]['type'], 'type2' => $office[$v['office_key']][$id]['type2'], 'tariff_key' => $k);
				}

				// генерация тарифа без выбранного пункта выдачи
				$sort = 0;
				$company_id = false;
				$no_free = false;
				foreach ($f['data'] as $k => $v) {
					if ($sort == 0) $sort = $v['sort'];
					if (!empty($v['no_free'])) $no_free = true;

					if ($company_id === false) {
						$company_id = $v['company_id'];
						$tariff = $v;
					}
					else if ($v['company_id'] != $company_id) {
						$company_id = false;
						break;
					}
				}
				$office_link = $f['get'];
				$v = array(
					'id' => '',
					'automatic' => 'edost',
					'profile' => $f_key,
					'company' => (!empty($company_id) ? $tariff['company'] : ''),
					'name' => '',
					'description' => '',
					'ico' => (!empty($company_id) ? $tariff['ico'] : 35),
					'company_id' => (!empty($company_id) ? $company_id : ''),
					'format' => $f_key,
					'sort' => $sort,
					'price' => $f['price']['max']['value'],
					'price_formatted' => self::GetRange($f['price']),
					'price_long' => ($f['price']['min']['value'] == $f['price']['max']['value'] ? 'normal' : 'light'),
					'day' => '',
					'office_map' => 'get',
					'office_mode' => $f_key,
					'office_link' => $office_link,
					'office_link2' => $sign['get'],
					'office_count' => 0,
					'office_address_full' => '',
					'cod_tariff' => false,
				);
				if ($config['template_ico'] == 'C') $v['company_ico'] = (!empty($company_id) ? $company_id : 's1');
				if ($no_free) $v['no_free'] = true;
				if ($f['pricecod']['max']['value'] >= 0) {
					if ($config['COMPACT'] != 'off') {
						$v['pricecod'] = $f['pricecod']['min']['value'];
						$v['pricecod_formatted'] = ($f['pricecod']['min']['value'] != $f['pricecod']['max']['value'] ? $sign['from'] : '') . $f['pricecod']['min']['formatted'];
					}
					else {
						$v['pricecod'] = $f['pricecod']['max']['value'];
						$v['pricecod_formatted'] = self::GetRange($f['pricecod']);
					}
				}
				$v_get = $v;

				$cod = 'start';
				if (isset($active_office[$f_key]['tariff_key'])) {
					// тариф с активным пунктом выдачи

					$p = $active_office[$f_key];
					$v = $f['data'][$p['tariff_key']];
					$o = $office[$v['office_key']][$p['id']];

					if ($f['office_count'] != 1 || $n != 1) {
						$v['office_map'] = 'change';
						$v['office_link'] = $sign[$f_key == 'postmap' ? 'change_postmap' : 'change'];
						foreach ($f['data'] as $v2) { $v['sort'] = $v2['sort']; break; }
					}
					else {
						$v['office_link'] = $sign['map'];
						$v['office_link2'] = $sign['get'];
					}

					$post = ($v['company_id'] == 23 && $o['type'] == 1 ? true : false);

					$v['office_mode'] = $f_key;
					$v['office_id'] = $o['id'];
					$v['office_type'] = $o['type'];
					$v['office_type2'] = $o['type2'];
					$v['office_options'] = $o['options'];
					$v['office_city'] = $o['city'];
					$v['office_address'] = self::GetOfficeAddress($o, $v, false);
					$v['office_address_full'] = self::GetOfficeAddress($o, $v);
					$v['office_detailed'] = edost_class::GetOfficeLink($o);

					if ($o['options'] & 256) $v['warning'] = $sign['full_warning'];

					edost_class::AddUnsupported($v, $o['options'], $config, $currency);

					// отключение наложенного платежа, если превышена максимально допустимая сумма перевода или невозможна оплата при получении для выбранного офиса
					if (isset($v['pricecash']) && (isset($o['codmax']) && $v['pricecash'] > $o['codmax'] || !empty($o['cod_disable']))) {
						$ar = array('pricecash', 'pricecash_formatted', 'pricecod', 'pricecod_formatted');
						foreach ($ar as $v2) unset($v[$v2]);
					}

					// выделение тарифа, выбранного покупателем при 'template_map_inside' + отключение встроенной карты
					if ($config['template_map_inside'] == 'Y' && !empty($active_office['all']['id']) && $active_office['all']['id'] == $p['id']) {
						$v['checked_inside'] = true;
						$config['template_map_inside'] = 'tariff';
					}

					if (isset($v['checked']) && (in_array($config['template_map_inside'], array('Y', 'tariff')) && empty($v['checked_inside']) || $edost_locations && !empty($v['office_city']))) unset($v['checked']);
					if (isset($v['checked'])) {
						$active = $v;
					}
					else if ($checked) {
						$active_id = '';
						$active = false;
					}

					$cod = (isset($v['pricecash']) ? true : false);
				}
				else {
					// тариф без выбранного пункта выдачи

					$v = $v_get;

					if ($checked) {
						$active_id = '';
						$active = false;
					}

					if ($active_profile === $f_key) {
						$v['checked'] = true;
						$active = $v;
					}

					if ($config['PRIORITY'] == 'B' && !empty($r['bonus']['cod']['office_count'])) $v['compact_cod'] = true;
				}

				if ($config['PRIORITY'] == 'B' && !empty($r['bonus']['cod']['office_count'])) {
					$v_get['compact_cod_copy'] = true;
					$v_get['compact_cod'] = true;
					$v_get['compact_head'] = $sign['compact_head'][$v['format']];
					$v_get['compact_link'] = $sign['compact_'.$f_key.'_get'];
					if ($r['bonus']['cod']['office_count'] != 1 && $office_count > 1) {
						$v_get['compact_link_cod'] = $sign['compact_'.$f_key.'_get'];
						$v_get['compact_head_cod'] = $v_get['compact_head'];
					}
					$f2['data'][] = $v_get;
				}

				self::FormatHead($v, $f['name'], $config);

				$v['pricehead'] = $f['pricehead'];
				$v['dayhead'] = $f['day'];

				if (!isset($f2['min']) || $f['min']['price'] < $f2['min']['price']) {
					$f2['min'] = $f['min'];
					$f2['min']['key'] = count($f2['data']);
				}

				$f2['data'][] = $v;
				$f2['office_count'] += $f['office_count'];
				$format[$f_key]['data'] = array();
			}

			if ($config['template_map_inside'] == 'tariff' && !empty($f2['data'])) {
				if ($tariff_count > 1 || $office_count > 1) foreach ($f2['data'] as $k => $v) {
					// добавление 'выбрать другой...' для всех тарифов
					$v['office_map'] = 'change';
					$v['office_link'] = $sign[$f_key == 'postmap' ? 'change_postmap' : 'change'];
					$f2['data'][$k]	= $v;
				}
			}
			if ($config['template_map_inside'] == 'Y' && $active_bookmark == 'show') {
				if ($tariff_count == 1 && $office_count == 1) {
					// выделение тарифа, когда нет выбора + отключение встроенной карты
					foreach ($f2['data'] as $k => $v) $f2['data'][$k]['checked_inside'] = true;
					$config['template_map_inside'] = 'tariff';
				}
				else {
					// сброс выбранного офиса, если активна интегрированная карта
					foreach ($f2['data'] as $k => $v) if (isset($v['office_id'])) {
						unset($v['office_id']);
						$v['profile'] = $v['office_mode'];
						$v['id'] = $v['office_address_full'] = $v['office_detailed'] = '';
						$f2['data'][$k] = $v;
					}
				}
			}

			// суммирование диапазона цен для заголовка группы
			$pricehead = $day = false;
			foreach ($f2['data'] as $k => $v) if (isset($v['pricehead'])) {
				$pricehead = self::AddRange($pricehead, $v['pricehead']);
				$day = self::AddRange($day, $v['dayhead']);
				unset($f2['data'][$k]['pricehead']);
				unset($f2['data'][$k]['dayhead']);
			}
			$f2['pricehead'] = $pricehead;
			$f2['day'] = $day;
			if ($ico !== false) $f2['ico'] = $ico;

	//		$format['office'] = $f2;

			$format[$i_key] = $f2;
		}
//		echo '<br><b>FORMAT RESULT:</b> <pre style="font-size: 12px">'.print_r($format['office'], true).'</pre>';
//		echo '<br><b>active_office:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// перемещение групп в общий список 'general'
		$count_format = 0;
		$count_tariff = 0;
		$count_edost = 0;
		$count_bitrix = 0;
		$auto = false;

		if ($config['template_block'] == 'auto2') {
			$n = ($bookmark ? 1 : 2);
			foreach ($format as $f_key => $f) if (!in_array($f_key, array('general', 'bitrix_1', 'bitrix_2')) && count($f['data']) > $n) $auto = true;
		}
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			$count_format++;
			foreach ($f['data'] as $v) if (empty($v['compact_cod_copy'])) $count_tariff++;
		}
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			if ($f_key == 'general') {
				$format[$f_key]['pack'] = 'normal';
				$count_edost++;
			}
			else if (in_array($f_key, array('bitrix_1', 'bitrix_2'))) {
				$format[$f_key]['pack'] = 'normal';
				$count_bitrix++;
			}
			else if ($config['COMPACT'] == 'off' && empty($config['CATALOGDELIVERY_INSIDE']) && ($count_format == 1 && $count_tariff <= 2 || $config['template_format'] == 'off' || $config['template_block'] == 'off' || ($config['template_block'] == 'auto1' && count($f['data']) <= 2) || ($config['template_block'] == 'auto2' && !$auto))) {
				$format[$f_key]['pack'] = 'head';
				$count_edost++;
			}
		}
		$bitrix = false;
		if ($config['template_block'] == 'off' || $count_format == 1 && !($config['template_map_inside'] == 'Y' && !empty($format['office']['data'])) || $count_edost > 1 || $count_bitrix > 0) {
			$f2 = $format['general'];
			$f2['data'] = array();
			foreach ($format as $f_key => $f) if (isset($f['pack'])) {
				if ($f['pack'] == 'normal') $data = $f['data'];
				else if ($f['pack'] == 'head') {
					$data = array();
					foreach ($f['data'] as $k => $v) {
						self::FormatHead($v, $f['name'], $config);
						$data[] = $v;
					}
				}

				if (count($f2['data']) != 0 && $config['template_format'] != 'off' && !($f_key == 'bitrix_2' && $bitrix)) $f2['data'][] = array('delimiter' => true);
				$f2['data'] = array_merge($f2['data'], $data);
				$format[$f_key]['data'] = array();

				$bitrix = ($f_key == 'bitrix_1' ? true : false);
			}
			$format['general'] = $f2;
		}
//		echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// наличие наложенного платежа в блоках
		$cod = false;
		if ($config['template_cod'] != 'off') foreach ($format as $f_key => $f) foreach ($f['data'] as $v) if (isset($v['pricecod'])) {
			$format[$f_key]['cod'] = true;
			$cod = true;
			break;
		}


		// подпись предупреждений для блока "до подъезда"
		if (!empty($format['house']['data'])) {
			$f = $format['house'];
			$c = ($template_2019 ? '_compact' : '');

			$count = count($f['data']);
			$count_priceinfo = 0;
			foreach ($f['data'] as $v) if (isset($v['priceinfo'])) $count_priceinfo++;

			$p = -1;
			foreach ($f['data'] as $v) {
				if (!isset($v['priceinfo'])) $p = -1;
				else if ($p < 0) {
					$p = $v['price'];
					$p_formatted = $v['price_formatted'];
				}
				else if ($p != $v['price']) $p = -1;
				if ($p < 0) break;
			}

			// общие предупреждения в заголовке
			$f['warning'] = $sign['house_warning'];
			if ($count == $count_priceinfo) {
				$f['warning'] .= ($f['warning'] != '' ? '<br>' : '').$sign['priceinfo_warning'.$c];
				if ($p > 0) $f['description'] = str_replace('%price%', $p_formatted, $sign['priceinfo_description'.$c]);
			}

			// предупреждения у тарифов
			foreach ($f['data'] as $k => $v) if (isset($v['priceinfo'])) {
				if ($count != $count_priceinfo) $v['warning'] = $sign['priceinfo_warning'.$c];
				if (!$template_2019 && $p < 0 && $v['price'] > 0) $v['description'] = str_replace('%price%', $v['price_formatted'].(isset($v['price_original']) ? ' <span class="edost_price_original">('.$v['price_original_formatted'].')</span> ' : ''), $sign['priceinfo_description'.$c]).($v['description'] != '' ? '<br>' : '').$v['description'];
				$f['data'][$k] = $v;
			}

			$format['house'] = $f;
		}


		// сортировка
		if (!$sorted) self::SortTariff($format['general']['data'], $config);

		// добавление нулевого тарифа (если нет других тарифов или есть ошибка загрузки офисов)
		if ($zero_tariff && (!$cod_filter || $cod_filter_zero_tariff)) {
			$count = 0;
			$count_edost = 0;
			foreach ($format as $f_key => $f) foreach ($f['data'] as $v) if (isset($v['id'])) {
				$count++;
				if ($v['automatic'] == 'edost') $count_edost++;
			}

			if ($count == 0 && (!empty($bitrix_data) || !empty($config['ADD_ZERO_TARIFF'])) || $config['hide_error'] != 'Y' && ($office_error !== false || $edost_enabled && $count_edost == 0)) {
				$error = '';
				if ($config['hide_error'] != 'Y') {
					$error_code = (!empty(CDeliveryEDOST::$result['error']) ? CDeliveryEDOST::$result['error'] : 0);

					if ($office_error !== false) $error = CDeliveryEDOST::GetEdostError($office_error, 'office');
					else if (!empty($config['ADD_ZERO_TARIFF'])) $error = CDeliveryEDOST::GetEdostError($error_code);
					else if ($edost_enabled && $count_edost == 0) $error = CDeliveryEDOST::GetEdostError(0);

					// вывод в админке списка товаров без веса
					if (!empty($config['ADD_ZERO_TARIFF']) && $error_code == 11 && !empty($edost_order['original']['ITEMS'])) foreach ($edost_order['original']['ITEMS'] as $k => $v) if (empty($v['WEIGHT'])) $error .= '<br>- '.$v['NAME'];
				}

				$tariff = CDeliveryEDOST::GetZeroTariff($config);
				if (!empty($tariff)) {
					$v = array(
						'id' => $tariff['id'],
						'automatic' => 'edost',
						'profile' => 0,
						'name' => '',
						'company' => $tariff['name'],
						'description' => $tariff['description'],
						'error' => $error,
						'price' => 0,
						'ico' => 0,
						'cod_tariff' => false,
					);
					if ($config['template_ico'] == 'C') $v['company_ico'] = 0;
					if ($tariff['id'] == $active_id) {
						$active = $v;
						$v['checked'] = true;
					}
					$format['general']['data'][] = $v;
				}
			}
		}


		// форматирование стоимости для заголовка + поиск самого дешевого тарифа в группе
		foreach ($format as $f_key => $f) if (!empty($f['data']) && !isset($f['pricehead'])) {
			self::FormatRange($f, $currency, $config['template_cod'] != 'off' ? true : false);
			$format[$f_key] = $f;
		}
//		echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($format, true).'</pre>';


		// сброс выбранной закладки, если группа недоступна + сброс активного тарифа, если выбрана другая группа
		if ($config['template_block_type'] == 'bookmark1' && $active_bookmark != '') {
			if (empty($format[$active_bookmark]['data'])) $active_bookmark = '';
			else if ($active !== false) foreach ($format as $f_key => $f) if (!empty($f['data']) && $f_key != $active_bookmark) {
				foreach ($f['data'] as $k => $v) if (isset($v['checked'])) {
					unset($format[$f_key]['data'][$k]['checked']);
					$active_id = '';
					$active = false;
					break;
				}
				if ($active === false) break;
			}
		}

		// включение автовыбора, если доступен только один тариф
		if ($active === false && $config['autoselect'] != 'Y') {
			$count_all = 0;
			foreach ($format as $f_key => $f) if (!empty($f['data'])) {
				$count = 0;
				foreach ($f['data'] as $k => $v) if (isset($v['id']) && empty($v['compact_cod_copy']) && (!$edost_locations || empty($v['office_city']))) $count++;
				$count_all += $count;
				if ($config['template_block_type'] == 'bookmark1' && $f_key == $active_bookmark && $count == 1) $config['autoselect'] = 'Y';
			}
			if ($count_all == 1) $config['autoselect'] = 'Y';
		}


		// выбор первой доставки, если ничего не выбрано
		$key = false;
		if ($active === false && $key === false && $config['autoselect'] == 'Y') {
			$i = false;
			if ($config['template_block_type'] == 'bookmark1' && !empty($format[$active_bookmark]['data'])) {
				$i = $active_bookmark;
				foreach ($format[$i]['data'] as $k => $v) if (!$edost_locations || empty($v['office_city'])) if (isset($v['id']) && empty($v['compact_cod_copy']) && ($config['template_map_inside'] != 'tariff' || !isset($v['office_mode']) || !empty($v['checked_inside']))) { $key = array($i, $k); break; }
			}
			else foreach ($format as $f_key => $f) if (!empty($f['data'])) {
				$i = $f_key;
				foreach ($format[$i]['data'] as $k => $v) if (!$edost_locations || empty($v['office_city'])) if (isset($v['id']) && empty($v['compact_cod_copy']) && ($config['template_map_inside'] != 'tariff' || !isset($v['office_mode']) || !empty($v['checked_inside']))) { $key = array($i, $k); break; }
				if ($key !== false) break;
			}
		}
		if ($key !== false) {
			$active = $format[$key[0]]['data'][$key[1]];
			$active_id = self::GetBitrixID($active);
			$format[$key[0]]['data'][$key[1]]['checked'] = true;
		}


		// вывод предупреждения для курьера boxberry "на указанный адрес доставка не производится"
		if ($order_key != '') {
			if (isset($edost_tariff[43])) $_SESSION['EDOST']['tariff_key'][43] = (!empty($active['tariff_id']) && $active['tariff_id'] == 43 ? $order_key : '');
			else if (!empty($_SESSION['EDOST']['tariff_key'][43]) && $_SESSION['EDOST']['tariff_key'][43] == $order_key) {
				$r['address_tariff_disable'][43] = true;
				$_SESSION['EDOST']['tariff_key'][43] = '';
			}
		}


		// упаковка групп тарифов в один общий массив
		$data = array();
		$day = false;
		$count_tariff = 0;
		$count_office = 0;
		$count_bookmark = 0;
		$count_bookmark_cod = 0;
		$supercompact_format = false;
		if ($bookmark) foreach ($format as $f_key => $f) if (!empty($f['data']) && $f_key != 'general') {
			$count_bookmark++;
			if ($config['template_block_type'] == 'bookmark1' && isset($f['cod']) || isset($f['min']['pricecash'])) $count_bookmark_cod++;
		}
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			if ($supercompact_format === false) $supercompact_format = $f_key;
			else { $supercompact_format = false; break; }
		}
		if ($cod_filter) foreach ($format as $f_key => $f) if (!empty($f['data'])) foreach ($f['data'] as $k => $v) if (!empty($v['insurance'])) $format[$f_key]['data'][$k]['insurance'] = 0;
		foreach ($format as $f_key => $f) if (!empty($f['data'])) {
			if ($f_key == 'general' && count($data) == 0) $head = '';
			else if ($count_bookmark > 1) $head = (isset($sign['bookmark'][$f_key]) ? $sign['bookmark'][$f_key] : '');
			else $head = (isset($f['head']) ? $f['head'] : $f['name']);

			$insurance = ($config['COMPACT'] == 'off' && !in_array($f_key, array('office', 'postmap', 'general')) && self::FormatInsurance($f) ? $sign['insurance_head'] : ''); // общая надпись "страховка включена во все тарифы"

			$compact = false;
			$compact_cod = false;
			$supercompact = false;
			$compact_link = false;
			if ($config['COMPACT'] != 'off') {
				$compact = $f['min']['key'];
				if (!empty($_SESSION['EDOST']['compact_tariff'][$f_key])) foreach ($f['data'] as $k => $v) if (isset($v['id']) && $v['id'] == $_SESSION['EDOST']['compact_tariff'][$f_key]) $compact = $k;

				$min = false;
				$count_cod = 0;
				foreach ($f['data'] as $k => $v) if (isset($v['pricecash'])) {
					if (empty($v['cod_hide'])) $count_cod++;
					if ($min === false || $v['pricecash'] < $f['data'][$min]['pricecash']) $min = $k;
				}
				if ($min !== false) $compact_cod = $min;

				$office_change = false;
				$tariff_count = 0;
				foreach ($f['data'] as $k => $v) if (isset($v['id'])) {
					if (empty($v['compact_cod_copy'])) {
						$tariff_count++;
						if (in_array($f_key, $office_main) && !empty($v['office_map'])) $office_change = true;
					}
					if (!empty($v['checked'])) {
						$_SESSION['EDOST']['compact_tariff'][$f_key] = $v['id'];
						$compact = $supercompact = $k;
					}
				}
				$compact_link = ($tariff_count > 1 || $office_change ? true : false);
			}

			$ar = array();
			foreach ($f['data'] as $k => $v) {
//				if ($config['COMPACT'] != 'off' && $f_key != 'general') self::FormatHead($v, $f['name'], $f_key == 'office' ? false : true, true, $config);
				if ($config['COMPACT'] != 'off' && $f_key != 'general') self::FormatHead($v, $f['name'], $config);

				if (isset($v['id'])) {
					if (empty($v['compact_cod_copy'])) {
						$count_tariff++;
						if (!empty($v['office_count'])) $count_office += $v['office_count'];
					}
//					echo '======'.$count_tariff;
					if (!empty($v['checked']) && $active_bookmark == '') $active_bookmark = $f_key;
					$v['html_id'] = self::GetHtmlID($v);
					$v['html_value'] = self::GetHtmlValue($v);
					if ($config['template'] == 'Y') $v['name'] = self::RenameTariff($v['name'], $rename['name']);
					$v['insurance'] = (isset($v['insurance']) && $v['insurance'] == 1 ? $sign['insurance'] : '');
					if (!isset($v['priceinfo']) && isset($v['price']) && $v['price'] == 0 && !isset($v['error'])) $v['free'] = $sign['free'];
					if (isset($v['pricecod']) && $v['pricecod'] == 0) $v['cod_free'] = $sign['free'];
					if (!empty($v['day'])) $day = true;

					if (!empty($v['pricecod'])) $v += self::GetPrice('codplus', $v['pricecod'] - $v['price'], '', $currency); // выводится только доплата за наложку 'codplus'

					if ($compact === $k || $compact_cod === $k || $f_key == 'general') {
						if (!empty($v['pricecod_original'])) $v += self::GetPrice('codplus_original', $v['pricecod_original'] - $v['price_original'], '', $currency); // выводится только доплата за наложку 'codplus'
						if ($compact === $k || $f_key == 'general') {
							$v['compact'] = true;
							if ($supercompact === $k) $v['supercompact'] = true;
						}
						if ($compact_link && $f_key != 'general') {
							$v['compact_head'] = $sign['compact_head'][$v['format']];
							$key = ($post_office_full ? 'postmap' : $f_key);
							$v['compact_link'] = (!empty($v['office_map']) && $f['office_count'] != 1 ? $sign['compact_'.$key.'_'.$v['office_map']] : $sign['change_company']);

							if ($count_cod > 1 || in_array($f_key, $office_main) && !empty($r['bonus']['cod']['office_count']) && $r['bonus']['cod']['office_count'] != 1) {
								if (!isset($v['compact_link_cod'])) $v['compact_link_cod'] = (!empty($v['office_map']) && $f['office_count'] != 1 ? $sign['compact_'.$key.'_'.$v['office_map']] : $sign['change_company']);
								$v['compact_head_cod'] = $v['compact_head'];
							}
						}
						if ($compact_cod === $k) $v['compact_cod'] = true;
					}

					if ($template_2019) {
						$note = array();

						$s = $sign['cod_warning'];
						if ($config['PRIORITY'] == 'C') {
							if ($cod_filter && isset($v['pricecash']) && !empty($v['transfer']) && !empty($v['checked'])) $note[] = str_replace(array('%pricecash%', '%transfer%'), array($v['pricecash_formatted'], $v['transfer_formatted']), $s[!empty($v['pricecash']) ? 'full' : 'transfer'][0]);
						}
						else if (isset($v['pricecash'])) $v += edost_class::GetPrice('pricecashplus', $v['pricecash'] - $v['price'], '', $currency);

						if (!empty($v['price']) && !empty($v['priceinfo']) && !empty($v['checked'])) $note[] = str_replace(array('%price%', '%priceinfo%'), array($v['price_formatted'], $v['priceinfo_formatted']), $sign['priceinfo_warning_compact2']);

						// служебное описание для активной доставки (в описании доставки текст экранирован тэгами [active] ... [/active])
						if (!empty($v['description'])) {
							$s = edost_class::service_string('active', $v['description']);
							if (!empty($s) && !empty($v['checked'])) $note[] = $s;
						}

						if (!empty($note)) $v['note'] = implode('<br>', $note);
					}
				}
				$ar[] = $v;
			}

			$data[$f_key] = array(
				'head' => $head,
				'cod' => (isset($f['cod']) ? true : false),
				'description' => (isset($f['description']) ? $f['description'] : ''),
				'warning' => (isset($f['warning']) ? $f['warning'] : ''),
				'insurance' => $insurance,
				'tariff' => $ar,
			);
			if ($config['template_block_type'] == 'bookmark1' || $config['COMPACT'] != 'off') {
				if ($f['pricehead']['min']['value'] == -1) $data[$f_key]['price_formatted'] = '';
				else {
					$data[$f_key]['price_formatted'] = self::GetRange($f['pricehead']);
					if (empty($data[$f_key]['price_formatted'])) $data[$f_key]['free'] = $sign['free'];

					// сокращенный вариант для карточки товара
					if (empty($f['pricehead']['min']['value'])) $data[$f_key]['short']['free'] = $sign['free'];
					else $data[$f_key]['short']['price_formatted'] = ($f['pricehead']['min']['value'] != $f['pricehead']['max']['value'] ? $sign['from'] : '') . $f['pricehead']['min']['formatted'];
				}
				$data[$f_key]['short']['price_range'] = $f['pricehead'];
				if (empty($f['pricehead']['min']['formatted'])) $data[$f_key]['short']['price_range']['min']['formatted'] = $sign['free'];

//				if (!empty($f['day'])) $data[$f_key]['short']['day'] = self::GetDay(round(($f['day']['min']['value'] + $f['day']['max']['value'])/2));
				if (!empty($f['day'])) {
					$data[$f_key]['short']['day'] = self::GetDay($f['day']['min']['value'], $f['day']['max']['value']);
					$data[$f_key]['short']['day_range'] = $f['day'];
				}
				if (!empty($f['ico'])) $data[$f_key]['short']['ico'] = $f['ico'];
			}
			if ($config['COMPACT'] != 'off') {
				if ($f['min']['price'] == 0) $f['min']['free'] = $sign['free'];

				if (in_array($f_key, $office_main)) {
					$k = $f['min']['key'];
					$ar = array('office_map', 'office_link', 'office_mode', 'office_type', 'office_type2', 'office_options', 'office_city', 'office_address');
					foreach ($ar as $v) if (isset($f['data'][$k][$v])) $f['min'][$v] = $f['data'][$k][$v];
				}

				$data[$f_key]['min'] = $f['min'];
			}
		}

		$r['data'] = $data;
		$r['count'] = $count_tariff;
		$r['count_office'] = $count_office;

		$r['cod'] = ($count_tariff == 1 || $config['template_cod'] != 'td' ? false : $cod); // есть тарифы с наложенным платежом и включен вывод в отдельной колонке
		$r['cod_bookmark'] = ($config['template_cod'] != 'off' && $count_bookmark > 1 && $count_bookmark != $count_bookmark_cod ? true : false); // подписывать в закладках "+ возможна оплата при получении"
		$r['cod_tariff'] = ($config['template_cod'] == 'tr' ? true : false); // включен вывод наложенного платежа отдельным тарифом
		$r['priceinfo'] = $priceinfo; // есть тарифы с предупреждением
		$r['day'] = $day; // есть тарифы со сроком доставки
		$r['border'] = ($config['template_block_type'] == 'border' && count($data) > 1 ? true : false); // блок с обводкой
		$r['warning'] = CDeliveryEDOST::GetEdostWarning(false, $template_2019 ? false : true);
		if (in_array($config['template_map_inside'], array('Y', 'tariff'))) $r['map_inside'] = $config['template_map_inside'];
		if ($config['COMPACT'] != 'off') $r['compact'] = $config['COMPACT'];
		$r['priority'] = $config['PRIORITY'];
		$r['company_ico'] = $config['template_ico'];
		if ($supercompact_format !== false) $r['supercompact_format'] = $supercompact_format;

		if (!empty($config['CATALOGDELIVERY_INSIDE'])) $r['bookmark'] = 1;
		else if ($count_bookmark > 1) $r['bookmark'] = str_replace('bookmark', '', $config['template_block_type']); // выводить закладки или дешевые тарифы

		$r['active'] = array(
			'id' => (!empty($active) ? $active_id : ''),
			'automatic' => (isset($active['automatic']) ? $active['automatic'] : ''),
			'profile' => (isset($active['profile']) ? $active['profile'] : ''),
			'cod' => (isset($active['pricecash']) ? true : false),
			'cod_tariff' => (!empty($active['cod_tariff']) ? true : false),
			'bookmark' => $active_bookmark,
			'name' => (isset($active['name_save']) ? $active['name_save'] : ''),
		);
		if (isset($active['office_type'])) $r['active']['office_type'] = $active['office_type'];
		if (isset($active['office_type2'])) $r['active']['office_type2'] = $active['office_type2'];
		if (isset($active['office_options'])) $r['active']['office_options'] = $active['office_options'];
		if (isset($active['office_city'])) $r['active']['office_city'] = $active['office_city'];
		if (isset($active['office_id'])) $r['active']['office_id'] = $active['office_id'];
		if (isset($active['office_key'])) $r['active']['office_key'] = $active['office_key'];
		if (isset($active['office_address_full'])) $r['active']['address'] = $active['office_address_full'];

		if (isset($active['pricecash'])) {
			$r['active'] += edost_class::GetPrice('pricecashplus', $active['pricecash'] - $active['price'], '', $currency);
			if (isset($active['transfer'])) {
				$r['active']['transfer'] = $active['transfer'];
				$r['active']['transfer_formatted'] = $active['transfer_formatted'];
				if (!empty($active['transfer']) && !empty($r['active']['pricecashplus'])) $r['active'] += edost_class::GetPrice('codtotal', $r['active']['pricecashplus'] + $active['transfer'], '', $currency);
				if ($template_2019 && $config['PRIORITY'] != 'C' && !empty($active['transfer'])) {
					$s = $sign['cod_warning'];
					$r['active']['cod_note'] = str_replace(array('%pricecashplus%', '%transfer%'), array($r['active']['pricecashplus_formatted'], $r['active']['transfer_formatted']), $s[!empty($r['active']['pricecashplus']) ? 'full' : 'transfer'][1]);
				}
			}
		}

//		echo '<br><b>FORMAT RESULT:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';

		if (class_exists('edost_function') && method_exists('edost_function', 'AfterFormatTariff')) edost_function::AfterFormatTariff($bitrix_data, $currency, $order, $config, $r);

		return $r;

	}


	// если все тарифы в группе со страховкой, тогда параметр 'insurance' удаляется и возвращается true
	public static function FormatInsurance(&$f) {

		$n = count($f['data']);
		if ($n <= 1) return false;

		$i = 0;
		foreach ($f['data'] as $v) if (!empty($v['insurance'])) $i++;

		if ($i != $n) return false;
		else {
			foreach ($f['data'] as $k => $v) unset($f['data'][$k]['insurance']);
			return true;
		}

	}


	// добавление наложки в основной расчет ($format) из дополнительного ($format_cod)
	public static function AddCod(&$format, $format_cod, $cod_tariff = false) {

		self::AddCodData($format['data'], $format_cod['data'], $cod_tariff);
		self::array_add($format['active'], $format_cod['active'], self::$cod_key);
		$format['cod'] = $format_cod['cod'];

		if (!empty($format['map_data']) && !empty($format_cod['map_data']))	{
			self::AddCodData($format['map_data']['format'], $format_cod['map_data']['format'], $cod_tariff, 'data');
			$format['map_json'] = self::GetOfficeJson($format['map_data']);
		}

	}

	// добавление тарифов наложки в основной расчет ($format) из дополнительного ($format_cod)
	public static function AddCodData(&$format, $format_cod, $cod_tariff = false, $tariff_key = 'tariff') {

		$s = $s2 = array();
		foreach ($format as $f_key => $f) if (!empty($f[$tariff_key])) foreach ($f[$tariff_key] as $k => $v) $s[$v['id'].(!empty($v['cod_tariff']) ? '_Y' : '')] = array($f_key, $k);
		foreach ($format_cod as $f_key => $f) if (!empty($f[$tariff_key])) foreach ($f[$tariff_key] as $k => $v) $s2[$v['id'].(!empty($v['cod_tariff']) ? '_Y' : '')] = array($f_key, $k);

		if ($cod_tariff) {
			foreach ($s as $k => $v) if (!isset($s2[$k])) unset($format[$v[0]][$tariff_key][$v[1]]);
			else if (!empty($format[$v[0]][$tariff_key][$v[1]]['cod_tariff'])) {
				$v2 = $s2[$k];
				$format[$v[0]][$tariff_key][$v[1]] = $format_cod[$v2[0]][$tariff_key][$v2[1]];
			}
		}
		else {
			foreach ($s as $k => $v) if (isset($s2[$k])) {
				$v2 = $s2[$k];
				self::array_add($format[$v[0]][$tariff_key][$v[1]], $format_cod[$v2[0]][$tariff_key][$v2[1]], self::$cod_key);
			}
			foreach ($format as $f_key => $f) if (!empty($format_cod[$f_key])) $format[$f_key]['cod'] = $format_cod[$f_key]['cod'];
		}

	}


	// упаковка в json пунктов выдачи
	public static function GetOfficeJson($param) {

		$rename = GetMessage('EDOST_DELIVERY_RENAME');

		// перенос в общую группу одинаковых офисов из разных office_key (5_0, 5_1, ...)
		$office_tariff = array();
		foreach ($param['office'] as $k => $v) {
			$c = explode('_', $k);
			if (isset($c[1])) $office_tariff[$c[0]][$k] = $k;
		}
		foreach ($office_tariff as $k => $v) if (count($v) <= 1) unset($office_tariff[$k]);
		else {
			$u = array();
			foreach ($v as $f) { $u = $param['office'][$f]; break; }
			foreach ($u as $o_key => $o) {
				$a = true;
				foreach ($v as $f2) {
					if (!isset($param['office'][$f2][$o_key])) {
						unset($u[$o_key]);
						$a = false;
						break;
					}
		            if (!$a) break;
				}
				if ($a) foreach ($v as $f2) unset($param['office'][$f2][$o_key]);
			}
			$param['office'][$k] = $u;
		}
//		echo '<br><b>old_values:</b> <pre style="font-size: 12px">'.print_r($param['office'], true).'</pre>';
//		echo '<br><b>old_values:</b> <pre style="font-size: 12px">'.print_r($office_tariff, true).'</pre>';

		$point = $p = array();
		foreach ($param['office'] as $k => $v) {
			$s = array();
			foreach ($v as $k2 => $v2)
				if (isset($p[$k2])) $s[$k2] = array('id' => $v2['id']);
				else $p[$k2] = $s[$k2] = $v2;
			$point[] = '{"company_id": "'.$k.'", "data": '.self::GetJson($s, array('id', 'name', 'address', 'schedule', 'gps', 'type', 'metro', 'codmax', 'detailed', 'code', 'options', 'city', 'type2'), true, true, true).'}';
		}

		$office_key = CDeliveryEDOST::$office_key;
		if (in_array($param['config']['post'], array('Y', 'O'))) $office_key[] = 'post';

		$tariff = array();
		foreach ($param['format'] as $f_key => $f) if (!empty($f['data']) && (isset($f['office_count']) || $f_key == 'general' || in_array($param['config']['post'], array('Y', 'O')) && $f_key == 'post')) {
			self::FormatInsurance($f); // удаление 'со страховкой', если в группе все тарифы со страховкой
			if (!$param['sorted']) self::SortTariff($f['data'], $param['config']);
			foreach ($f['data'] as $k => $v) if (isset($v['format']) && in_array($v['format'], $office_key)) {
				if ($f_key == 'general') {
					$v['name'] = '';
					$v['insurance'] = '';
				}
				if ($param['config']['template_cod'] == 'tr') $v['cod_tariff'] = ($v['cod_tariff'] ? 'Y' : 'N'); else $v['cod_tariff'] = '';
				$v['profile'] = $v['profile'].'_'.$v['id'];
//				$v['price'] = $v['pricetotal'];
//				$v['price_formatted'] = $v['pricetotal_formatted'];
				if (isset($v['pricecod'])) $v += self::GetPrice('codplus', $v['pricecod'] - $v['pricetotal'], '', $param['currency']); // на карте выводится только доплата за наложку 'codplus'
				$v['company'] = self::RenameTariff($v['company'], $rename['company']);
				if (!empty($v['no_free']) && $v['pricetotal'] == 0) $v['pricetotal_formatted'] = '';

				// копия тарифов для общей группы офисов одной компании (из разных office_key)
				if (empty($v['to_office']) && !empty($office_tariff[$v['company_id']])) {
	                $u = $v;
					$u['office_key'] = $u['company_id'];
					$tariff[] = $u;
				}

				$tariff[] = $v;
			}
		}
//		echo '<br><b>old_values:</b> <pre style="font-size: 12px">'.print_r($param['format'], true).'</pre>';
		return '"city": "'.$param['location']['bitrix']['city'].'", '.
				'"region": "'.($param['location']['region_name'] != $param['location']['bitrix']['city'] ? $param['location']['region_name'].', ' : '').$param['location']['country_name'].'", '.
				'"unsupported": '.(!empty($param['config']['office_unsupported_fix']) || !empty($param['config']['office_unsupported_percent']) ? 1 : 0).', '.
				'"point": ['.implode(', ', $point).'], '.
				'"tariff": '.self::GetJson($tariff, array('profile', 'company', 'name', 'tariff_id', 'pricetotal', 'pricetotal_formatted', 'pricecash', 'codplus', 'codplus_formatted', 'day', 'insurance', 'to_office', 'company_id', 'format', 'cod_tariff', 'ico', 'format_original', 'pricetotal_original_formatted', 'pricecod', 'pricecod_formatted', 'pricecod_original_formatted', 'office_key'));
	}


	// загрузка prop2 из профиля покупателя или из 'POST'
	public static function GetProp2($prop, $param = false) {
//echo 'GetProp2<br>';
		if (!CModule::IncludeModule('edost.locations')) return false;
//		echo '<br><b>==============param:</b> <pre style="font-size: 12px">'.print_r($param, true).'</pre>';
		$s = $GLOBALS['APPLICATION']->GetCurPage();
		$convert_charset = (strpos($s, '/ajax.php') !== false ? true : false);

		$set_prop2 = '';
		if (isset($param['set_prop2'])) $set_prop2 = ($param['set_prop2'] ? 'Y' : 'N');

		$prop2 = array();
//echo '=========== set_prop2: '.$set_prop2;
		if ($_SERVER['REQUEST_METHOD'] != 'POST' || $set_prop2 == 'Y') {
//echo '<br>GET =============';
			// разбор старого адреса из профиля покупателя
			$props = false;
			if (isset($param['ORDER']))	$props = self::GetProps($param['ORDER'], array('order', 'no_payment'));
			else if (isset($param['ORDER_ID'])) $props = self::GetProps($param['ORDER_ID'], array('no_payment'));
			$prop = (!empty($props['prop']) ? $props['prop'] : $prop);

			$prop2 = CLocationsEDOST::SetProp2($prop);

//			echo '<br><b>prop:</b> <pre style="font-size: 12px">'.print_r($prop, true).'</pre>';
//			echo '<br><b>prop2:</b> <pre style="font-size: 12px">'.print_r($prop2, true).'</pre>';
		}
//		echo '<br><b>prop:</b> <pre style="font-size: 12px">'.print_r($prop, true).'</pre>';
//		echo '<br><b>prop2:</b> <pre style="font-size: 12px">'.print_r($prop2, true).'</pre>';
		if (empty($prop2) || $set_prop2 == 'N') {
			// загрузка полей из POST
			$s = CLocationsEDOST::GetProp2($convert_charset);
			$prop2 = array_merge($prop2, $s);
		}

		return $prop2;

	}


	// упаковка в json по заданным ключам
	public static function GetJson($data, $key, $array = true, $pack = true, $pack_full = false) {

		if (!$array) $data = array($data);
		else if (!is_array($data) || count($data) == 0) return '[]';

		if ($key === false) foreach ($data as $v) { $key = array_keys($v); break; }

		if ($pack && $pack_full) $key = array_reverse($key);

		$s = array();
		foreach ($data as $v) {
			$s2 = array();
			if ($pack) {
				if ($pack_full) {
					foreach ($key as $v2) if (isset($v[$v2]) && $v[$v2] !== '' || !empty($s2)) $s2[] = (isset($v[$v2]) ? str_replace(array('"', "'"), array('', ''), $v[$v2]) : '');
					$s2 = array_reverse($s2);
				}
				else {
					foreach ($key as $v2) $s2[] = (isset($v[$v2]) ? str_replace(array('"', "'"), array('', ''), $v[$v2]) : '');
				}
				$s[] = '"'.implode('|', $s2).'"';
			}
            else {
				foreach ($v as $k2 => $v2) if (in_array($k2, $key))
					if (!is_array($v2)) $s2[] = '"'.$k2.'": "'.str_replace(array('"', "\t"), array('\"', ' '), $v2).'"';
					else if ($k2 === 'size') $s2[] = '"'.$k2.'": ['.implode(',', $v2).']';
					else $s2[] = '"'.$k2.'": '.self::GetJson($v2, false, true, false);
				$s[] = '{'.implode(', ', $s2).'}';
			}
		}

		if (!$array) return $s[0];
		else return '['.implode(',', $s).']';

	}


	// разбор упакованного массива (1,2,... : 3,4,... : ...)
	public static function ParseArray($array, $id, &$data, $level = 0) {

		if (in_array($id, array('field', 'control'))) $array = $GLOBALS['APPLICATION']->ConvertCharset(substr($array, 0, 200000), 'windows-1251', LANG_CHARSET);
		else if (in_array($id, array('office', 'limit'))) $array = substr($array, 0, 200000);
		else $array = preg_replace("/[^0-9.:,;\/-]/i", "", substr($array, 0, 20000));
		if ($array == '') return;

		if ($id == 'priceoffice') $key = array('type', 'price', 'priceinfo', 'pricecash', 'priceoriginal');
		else if ($id == 'priceoriginal') $key = array('price', 'pricecash');
		else if ($id == 'field') $key = array('name', 'value');
		else if ($id == 'control') $key = array('id', 'count', 'site');
		else if ($id == 'office') $key = array('company_id', 'schedule', 'limit', 'tariff', 'tel', 'type2');
		else if ($id == 'limit') $key = array('limit_pack');
		else if ($id == 'tariff') $key = array('tariff_array');
		else return;

		$key_count = count($key);
		$default = array_fill_keys($key, 0);
		if ($id == 'priceoffice') {
			$default['pricecash'] = -1;
			unset($default['priceoriginal']);
		}
		if ($id == 'priceoriginal') unset($default['pricecash']);
		if ($id == 'office') $default = array('company_id' => 0, 'schedule' => '', 'limit' => array(), 'tariff' => array(), 'tel' => '', 'type2' => '');

		$r = array();
		$delimiter = self::$delimiter[$level];
		$array = explode($delimiter[1], $array);
		foreach ($array as $v) {
			$v = explode($delimiter[0], $v);
			if ($v[0] == '' || !isset($v[1]) && !in_array($id, array('priceoriginal', 'limit', 'tariff'))) continue;

			$ar = $default;
			foreach ($v as $k2 => $v2) if ($k2 < $key_count && $v2 !== '')
				if (in_array($key[$k2], array('priceoriginal', 'limit', 'tariff'))) self::ParseArray($v2, $key[$k2], $ar, 1);
				else if ($key[$k2] == 'schedule') $ar[$key[$k2]] = $GLOBALS['APPLICATION']->ConvertCharset(substr(self::UnPackSchedule(self::UnPackDataFilter($v2)), 0, 80), 'windows-1251', LANG_CHARSET);
				else if ($key[$k2] == 'tel') $ar[$key[$k2]] = $GLOBALS['APPLICATION']->ConvertCharset(substr(self::UnPackDataFilter($v2), 0, 80), 'windows-1251', LANG_CHARSET);
				else if ($key[$k2] == 'tariff_array') $ar = explode('-', $v2);
				else if ($key[$k2] == 'limit_pack') {
					$ar = self::UnPackLimit(self::UnPackDataFilter($v2), true);
					$v[0] = $ar['type'];
					unset($ar['type']);
				}
				else $ar[$key[$k2]] = str_replace(array('%c', '%t'), array(',', ':'), $v2);

			if ($id == 'priceoriginal') $r = $ar;
			else if (in_array($id, array('priceoffice', 'control', 'office', 'limit'))) $r[$v[0]] = $ar;
			else $r[] = $ar;
		}
		if (!empty($r)) $data[$id] = $r;

	}

	// разбор ответа сервера
	public static function ParseData($data, $type = 'delivery') {

		if ($type == 'delivery') $key = array('id', 'price', 'priceinfo', 'pricecash', 'priceoffice', 'transfer', 'day', 'insurance', 'company', 'name', 'format', 'company_id', 'priceoriginal');
		else if ($type == 'document') $key = array('id', 'data', 'data2', 'name', 'size', 'quantity', 'mode', 'cod', 'delivery', 'length', 'space');
		else if ($type == 'office') $key = array('id', 'code', 'name', 'address', 'address2', 'tel', 'schedule', 'gps', 'type', 'metro', 'options', 'limit', 'city', 'type2');
		else if ($type == 'location') $key = array('city', 'region', 'country');
		else if ($type == 'location_street') $key = array('street', 'zip', 'city');
		else if ($type == 'location_zip') $key = array('zip');
		else if ($type == 'location_robot') $key = array('ip_from', 'ip_to');
		else if ($type == 'control') $key = array('id', 'flag', 'tariff', 'tracking_code', 'status', 'status_warning', 'status_string', 'status_info', 'status_date', 'status_time', 'day_arrival', 'day_delay', 'day_office', 'register', 'batch');
		else if ($type == 'detail') $key = array('status', 'status_warning', 'status_string', 'status_info', 'status_date', 'status_time');
		else if ($type == 'tracking') $key = array('id', 'tariff', 'example', 'format');

		else if ($type == 'param') $key = array();
		else return array('error' => 4);

		$r = array();
		$key_count = count($key);
		$data = explode('|', $data);

		// общие параметры: error=2;warning=1;sizetocm=1;...
		$p = explode(';', $data[0]);
		foreach ($p as $v) {
			$s = explode('=', $v);
			$s[0] = preg_replace("/[^0-9_a-z]/i", "", substr($s[0], 0, 20));
			if (isset($s[1]) && $s[0] != '')
				if (in_array($s[0], array('field', 'control', 'office'))) self::ParseArray($s[1], $s[0], $r);
				else if ($s[0] == 'warning') $r[$s[0]] = explode(':', $s[1]);
				else $r[$s[0]] = $s[1];
		}

		if (isset($r['error']) || $key_count == 0) return $r;

		$r['data'] = array();
		$array_id = '';
		$sort = 0;
		foreach ($data as $k => $v) if ($k == 0 || $v == 'end') {
			if ($k != 0 && isset($parse[$key[0]]) && ($key_count == 1 || isset($parse[$key[1]]))) {
				$sort++;
				if ($type == 'delivery') {
					$profile = $parse['id']*2 + ($parse['insurance'] == 1 ? 0 : -1);
					$parse['profile'] = $profile;
					$parse['sort'] = $sort*2;
					if ($profile > 0) $r['data'][$profile] = $parse;
				}
				else if ($array_id !== '') $r['data'][$array_id][$parse['id']] = $parse;
				else if (isset($parse['id'])) $r['data'][$parse['id']] = $parse;
				else $r['data'][] = $parse;
			}
			$i = 0;
			$parse = array();
		}
		else if ($v === 'key') $array_id = 'get';
		else if ($array_id === 'get') $array_id = $v;
		else if ($i < $key_count) {
			$p = $key[$i];
			$i++;

			if ($type == 'delivery') {
				if (in_array($p, array('day', 'company', 'name'))) $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 80), 'windows-1251', LANG_CHARSET);
				else if (in_array($p, array('price', 'priceinfo', 'pricecash', 'transfer'))) {
					$v = preg_replace("/[^0-9.-]/i", "", substr($v, 0, 11));
					if ($v === '') $v = ($p == 'pricecash' ? -1 : 0);
				}
				else if (in_array($p, array('id', 'insurance'))) $v = intval($v);
				else if ($p == 'company_id') $v = preg_replace("/[^a-z0-9]/i", "", substr($v, 0, 3));
				else if ($p == 'format') $v = preg_replace("/[^a-z]/i", "", substr($v, 0, 10));
				else if ($p == 'priceoffice') {
					self::ParseArray($v, $p, $parse);
					continue;
				}
				else if ($p == 'priceoriginal') {
					self::ParseArray($v, $p, $parse);
					continue;
				}
			}

			if ($type == 'document') {
				if ($p == 'insurance' || $p == 'cod') $v = ($v == 1 ? true : false);
				else if ($p == 'delivery') $v = ($v != '' ? explode(',', $v) : false);
				else if ($p == 'size') $v = explode('x', $v);
				else if ($p == 'length' || $p == 'space') {
					$v = explode(',', $v);
					$o = array();
					foreach ($v as $s) if ($s != '') {
						$s = explode('=', $s);
						if ($s[0] != '') $o[$s[0]] = (isset($s[1]) ? intval($s[1]) : 0);
					}
					$v = $o;
				}
			}

			if ($type == 'office') {
				if ($p == 'type') $v = intval($v);
				else if (in_array($p, array('id', 'gps', 'type2'))) $v = preg_replace("/[^a-z0-9.,]/i", "", substr($v, 0, 30));
				else if ($p == 'schedule') $v = $GLOBALS['APPLICATION']->ConvertCharset(self::UnPackSchedule(substr($v, 0, 160)), 'windows-1251', LANG_CHARSET);
				else if ($p == 'limit') $v = self::UnPackLimit(substr($v, 0, 15));
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(trim(substr($v, 0, 160)), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'location') {
				if ($p == 'country' || $p == 'region') $v = intval($v);
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 160), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'location_street') {
				if (in_array($p, array('street', 'city'))) $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 160), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'location_zip') {
				$v = preg_replace("/[^0-9]/i", "", substr($v, 0, 6));
			}

			if ($type == 'location_robot') {
				$v = preg_replace("/[^0-9.]/i", "", substr($v, 0, 15));
			}

			if ($type == 'control' || $type == 'detail') {
				if (in_array($p, array('id', 'flag', 'status', 'tariff', 'status_warning', 'day_arrival', 'day_delay', 'day_office'))) $v = intval($v);
				else if ($p == 'batch') {
					$v = self::UnPackDataArray($v, 'batch');
					if (!empty($v['date']) && !empty($v['number'])) $parse['batch_code'] = $v['date'].'_'.$v['number'];
				}
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 500), 'windows-1251', LANG_CHARSET);
			}

			if ($type == 'tracking') {
				if (in_array($p, array('company_id'))) $v = intval($v);
				else if ($p == 'tariff') $v = explode(',', $v);
				else $v = $GLOBALS['APPLICATION']->ConvertCharset(substr($v, 0, 500), 'windows-1251', LANG_CHARSET);
			}

			$parse[$p] = $v;
		}

		return $r;

	}


	// получение id тарифа стандарта битрикса (без дополнительных параметров eDost)
	public static function GetBitrixID($v) {
		if ($v['automatic'] !== $v['id']) return $v['id'];
		return $v['automatic'].':'.$v['profile'];
	}
	// получение id тарифа для html
	public static function GetHtmlID($v) {
		if ($v['automatic'] == 'edost') {
			if (isset($v['office_mode'])) $s = $v['office_mode'];
			else $s = $v['profile'].($v['cod_tariff'] ? '_Y' : '');
			return 'edost_'.$s;
		}
		if ($v['automatic'] !== $v['id']) return $v['id'];
		return $v['automatic'].'_'.$v['profile'];
	}
	// получение value тарифа для html
	public static function GetHtmlValue($v) {
		if ($v['automatic'] == 'edost') {
			$value = 'edost:'.$v['profile'].($v['id'] != '' && $v['automatic'] !== $v['id'] ? '_'.$v['id'] : '');
			if (isset($v['office_id']) || $v['cod_tariff']) $value .=  ':'.(isset($v['office_id']) ? $v['office_id'] : '').':'.($v['cod_tariff'] ? 'Y' : '');
		}
		else $value = self::GetBitrixID($v);
		return $value;
	}
	// получение title тарифа
	public static function GetTitle($v, $full = false) {
		$r = ($full && isset($v['head']) && !isset($v['company_head']) ? $v['head'] : $v['company']);
		$s = $v['name'];
		if ($full && $v['insurance'] != '' && strpos($s, $v['insurance']) === false) $s .= ($s != '' ? ' ' : '').$v['insurance'];
		return $r.($s != '' ? ' ('.$s.')' : '');
	}

	// разбор названия на компанию доставки и тариф + удаление пустых '<br>' в описании + удаление 'со страховкой'
	public static function ParseName($s, $company = '', $description = '', $insurance = '') {

		$r = array('name' => '');
		$c = ", \n\r\t\v\0";

		$o = $s;
		if ($insurance != '') $s = str_replace($insurance, '', $s);
		if ($company != '' && strpos($s, $company) !== false) $company = '';
		if ($company != '') {
			$r['company'] = trim($company, $c);
			$r['name'] = trim($s, $c);
		}
		else {
			$s = explode('(', $s);
			$r['company'] = trim($s[0], $c);
			if (isset($s[1])) {
				$s = explode(')', $s[1]);
				$r['name'] = trim($s[0], $c);
			}

			// оригинальное название тарифа
			$o = explode('(', $o);
			if (isset($o[1])) {
				$o = explode(')', $o[1]);
				$r['name_original'] = trim($o[0], $c);
			}
		}

		$s = trim($description);
		if ($s === '<br>' || $s === '<br />') $s = '';
		if (strpos($s, '[no_free]') !== false) {
			$r['no_free'] = true;
			$s = str_replace('[no_free]', '', $s);
		}
		$r['description'] = $s;

		return $r;

	}

	// получение стоимости в заданной валюте - числом и строкой в отформатированном виде ($key == 'value' - возвращается только значение,  $key == 'formatted' - возвращается только отформатированная строка)
	public static function GetPrice($key, $price, $currency, $currency_result = '', $bitrix = true) {

		$r = array();
		if ($price == '') $price = 0;

		if ($bitrix && defined('DELIVERY_EDOST_PRICE_FORMATTED') && DELIVERY_EDOST_PRICE_FORMATTED == 'Y') $bitrix = false;

		if ($currency_result == '') $currency_result = $currency;
		$r[$key] = ($currency !== '' && $currency != $currency_result ? CCurrencyRates::ConvertCurrency($price, $currency, $currency_result) : $price);
		$r[$key] = \Bitrix\Sale\PriceMaths::roundPrecision($r[$key]);

		if ($key != 'value') {
			if ($price == '0') $v = '0';
			else if ($bitrix) $v = SaleFormatCurrency($r[$key], $currency_result);
			else {
				$f = CCurrencyLang::GetFormatDescription($currency !== '' ? $currency : $currency_result);
//				echo '<br><b>ar:</b><pre style="font-size: 12px">'.print_r($ar, true).'</pre>';

				$v = round($r[$key], 2);

				$s = ' '.$v;
				$p = strpos($s, '.');
				if ($p > 0) {
					$d2 = strlen($s) - $p - 1;
					if ($d2 < $d) $d = $d2;
				}
				else $d = 0;

				$v = str_replace('#', number_format($v, $d, '.', ' '), $f['FORMAT_STRING']);
			}
			$r[$key.'_formatted'] = $v;
		}

		if ($key == 'value') return $r[$key];
		if ($key == 'formatted') return $r[$key.'_formatted'];
		return $r;

	}

	// получение из строки '5-8 дней' диапазона array(5,8)
	public static function ParseDay($s, $to = '') {
		$s = explode('<a ', $s); // модуль boxberry подписывает ссылку на выбор пунктов выдачи!
		$s = $s[0];
		$s = explode('(', $s); // модуль DPD подписывает название тарифа и ссылку на выбор пунктов выдачи!
		$s = $s[0];
		if (!empty($to)) $s = str_replace(array('—', $to), '-', $s); // замена длинного тире и ' до '
		$s = preg_replace("/[^0-9-]/i", "", $s);
		$s = explode('-', $s);
		$s[0] = intval($s[0]);
		$s[1] = (!empty($s[1]) ? intval($s[1]) : -1);
		return $s;
	}

	// получение срока доставки вида '5-8 дней'
	public static function GetDay($from = '', $to = '', $name = 'D') {

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$from = intval($from);
		$to = intval($to);
		if (!in_array($name, array('D', 'H', 'M', 'MIN'))) $name = 'D';

		$r = '';
		$n = 0;

		if ($from > 0 || $to > 0) {
			$n = $from;
			$r .= $from;
		}
		if ($to > 0 && $to != $from) {
			$n = $to;
			$r .= ($r != '' ? '-' : '').$to;
		}

		if ($n == 0) return '';

		$s = '';
		$ar = $sign['day'];
		if ($n >= 11 && $n <= 19) $s = $ar[$name][2];
		else {
			$n = $n % 10;
			if ($n == 1) $s = $ar[$name][0];
			else if ($n >= 2 && $n <= 4) $s = $ar[$name][1];
			else $s = $ar[$name][2];
		}

		return $r.' '.$s;

	}


	// сортировка тарифов
	public static function SortTariff(&$data, $config) {

		if (count($data) <= 1) return;

		$sort_max = 0;
		foreach ($data as $k => $v) {
			if (empty($v['sort'])) $data[$k]['sort'] = $v['sort'] = 0;
			if ($v['sort'] > $sort_max) $sort_max = $v['sort'];
		}

		$ar = array();
		foreach ($data as $k => $v) {
			if ($config['sort_ascending'] == 'Y') {
				// по стоимости доставки
				$i = ((isset($v['price']) ? floatval($v['price']) : 0) + (isset($v['priceinfo']) ? floatval($v['priceinfo']) : 0))*1000 + (!empty($sort_max) ? 5*$v['sort']/$sort_max : 0);
				$ar[] = $i;
				if ($config['template'] == 'N3') $data[$k]['sort'] = round($i*1000);
			}
			else {
				// по коду сортировки
				$ar[] = $v['sort'];
			}
		}
		array_multisort($ar, SORT_ASC, SORT_NUMERIC, $data);

	}

	// получение адреса офиса (если передан $tariff, тогда формируется полный адрес с телефонами, расписанием работы и т.д.)
	public static function GetOfficeAddress($office, $tariff = false, $full = true) {

		if ($tariff === false) $full = false;
		$post = (!empty($tariff['company_id']) && $tariff['company_id'] == 23 && $office['type'] == 1 ? true : false);

		$r = '';
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$metro = ($office['metro'] != '' ? $sign['metro'].$office['metro'] : '');
		$r = $office['name'];
		$r .= ($r != '' && $metro != '' ? ', ' : '').$metro;
		$r = ($r != '' ? ' ('.$r.')' : '');

		if (!$full) {
			$c = '';
			if (!empty($office['city'])) { $c = explode(';', $office['city']); $c = $c[0]; }
			if ($post) $s = $office['code'].', '.($c != '' ? $c : $office['address']);
			else {
				$s = $office['address'];
				if ($c != '' && strpos($s, $c) === false) $s = $c.', '.$s;
			}
			return $s.$r;
		}

		$shop = (in_array($tariff['company_id'], CDeliveryEDOST::$company_shop) ? true : false);
		$shop_company_default = (in_array($tariff['company'], $sign['shop_company_default']) ? true : false);

		$c = $office['code'];
		if ($c == '') $c = ($shop ? 'S' : 'T');

		if (in_array($office['type'], CDeliveryEDOST::$postamat)) $head = $sign['postamat']['name'];
		else if ($post) $head = $sign['post']['name'].' '.$office['id'];
		else {
			$key = $tariff['format'];
			if ($office['type'] == 2) $key = 'terminal'; else if ($key == 'terminal') $key = 'office';
			$head = $sign[$key];
		}

		$s = array();
		$s[] = $head.(!$shop_company_default && $tariff['company_id'] != 23 && $tariff['format'] != 'shop' ? ' '.$tariff['company'] : '').': '.$office['address_full'] . $r;
		if ($office['tel'] != '') $s[] = $sign['tel'].': '.$office['tel'];
		if ($office['schedule'] != '') $s[] = $sign['schedule'].': '.$office['schedule'];
		$s[] = $sign['code'].': '.$c.'/'.$office['id'].'/'.$office['type'].(isset($office['type2']) && $office['type2'] !== '' ? '_'.$office['type2'] : '').(!empty($office['options']) ? '-'.$office['options'] : '').'/'.$tariff['profile'].(!empty($tariff['cod_tariff']) ? '-Y' : '');
		$r = implode(', ', $s);

		return $r;

	}

	// получение данных офиса из адреса (результат: false - офиса нет,  true - офис есть, но без данных,  array - данные офиса)
	public static function ParseOfficeAddress($address) {

		$sign = GetMessage('EDOST_DELIVERY_SIGN');

		$s = explode(', '.$sign['code'].': ', $address);
		if (empty($s[1])) return false;

		$s1 = explode(':', $s[0]);
		$head = $s1[0];

		$s1 = explode($head.': ', $s[0]);
		$s1 = $s1[1];

		$address = $tel = $schedule = '';
		$ar = array(', '.$sign['schedule'].': ', ', '.$sign['tel'].': ');
		foreach ($ar as $k => $v) {
			$s2 = explode($v, $s1);
			if (!empty($s2[1])) {
				if ($k == 0) $schedule = $s2[1];
				else $tel = $s2[1];
				$s1 = $s2[0];
			}
		}
		$address = $s1;

		$s = explode('/', $s[1]);
		if (empty($s[3])) return true;
		$profile = explode('-', $s[3]);

		$v = explode('-', $s[2]);
		$options = (!empty($v[1]) ? intval($v[1]) : 0);
		$v = explode('_', $v[0]);
		$type = intval($v[0]);
		$type2 = (isset($v[1]) ? $v[1] : '');

		$r = array(
			'code' => $s[0],
			'id' => preg_replace("/[^0-9A]/i", "", substr($s[1], 0, 20)),
			'type' => $type,
			'type2' => $type2,
			'options' => $options,
			'profile' => intval($profile[0]),
			'cod_tariff' => (!empty($profile[1]) && $profile[1] == 'Y' ? true : false),
			'head' => $head,
			'address' => $address,
			'tel' => $tel,
			'schedule' => $schedule,
		);
		if (strpos($head, $sign['post']['name']) === 0) $r['post'] = true;
//			echo '<br><b>format:</b> <pre style="font-size: 12px">'.print_r($r, true).'</pre>';
//			die();
		return $r;

	}


	// замена названий по массиву соответствий $data
	public static function RenameTariff($s, $data) {
		if ($s != '' && isset($data[1])) {
			$i = array_search($s, $data[0]);
			if ($i !== false) $s = $data[1][$i];
		}
		return $s;
	}


	// форматирование тарифа для вывода в блоке 'general'
	public static function FormatHead(&$v, $head, $config = false) {

		$compact = ($config['COMPACT'] != 'off' ? true : false);

		if (isset($v['head'])) return;

		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$format_data = GetMessage('EDOST_DELIVERY_FORMAT');

		$v['head'] = $head;
		if (isset($v['format_original']) && $v['format_original'] == 'shop') $v['head'] = $format_data['shop']['name'];
		else if (isset($v['office_type']))
			if (in_array($v['office_type'], CDeliveryEDOST::$postamat)) $v['head'] = $sign['postamat']['head'];
			else if ($v['company_id'] == 23 && $v['office_type'] == 1) $v['head'] = $sign['post']['head'];
			else if ($v['office_type'] == 2) $v['head'] = $format_data['terminal']['name'];

		if (!$compact) {
			$shop_company_default = (in_array($v['company'], $sign['shop_company_default']) ? true : false);
			$a = false;
			if (isset($v['office_count']) && !isset($v['compact']) && ($config === false || $config['template_block'] != 'off')) $a = true;
			else if ($v['format'] != 'shop' && !$shop_company_default) $v['company_head'] = $sign['delivery_company']; // вывод названия службы доставки отдельной строкой (кроме тарифов Курьер)
			if ($a && $v['format'] != 'shop' && !$shop_company_default && $v['company_id'] != 23) {
				$rename = GetMessage('EDOST_DELIVERY_RENAME');
				$v['head'] .= ' '.self::RenameTariff($v['company'], $rename['company']); // добавление названия компании к заголовку (для тарифов с офисами)
			}
		}

		if ($compact) $compact = '_compact';

		// подпись предупреждений
		$w = array();
		if ($v['format'] == 'terminal' && isset($v['office_count']) && $v['office_count'] > 1) $w[] = $sign['terminal_warning'];
		if ($v['format'] == 'house') $w[] = $sign['house_warning'];
		if (isset($v['priceinfo'])) {
			$w[] = $sign['priceinfo_warning'.$compact];
			if (($config === false || $config['template'] != 'Y') && $v['price'] > 0) $v['description'] = str_replace('%price%', $v['price_formatted'], $sign['priceinfo_description'.$compact]).(!empty($v['description']) ? '<br>'.$v['description'] : '').$config['template'];
		}
		if (!empty($w)) $v['warning'] = implode('<br>', $w);

	}


	// перенос скидки из правил работы с корзиной на эксклюзивный тариф и наложку
	public static function SetDiscount($value, $price, $price_discount, $sale_discount_cod = '') {

		if ($value == -1) return $value;

		if ($value == $price) $r = $price_discount;
		if ($price == 0) $r = $value;
		else if ($sale_discount_cod == 'P') $r = $value - ($price - $price_discount);
		else if ($sale_discount_cod == 'F' || $sale_discount_cod == '') $r = round($value * $price_discount / $price);
		else $r = $value;

		if ($r < 0) $r = 0;

		return $r;
	}


	// получение диапазона цены: от 'минимальная' до 'максимальная' (от 100 руб. до 200 руб.) + поиск самого дешевого тарифа
	public static function FormatRange(&$format, $currency, $cod) {
		$price = $pricecod = $day = $day2 = self::SetRange();
		$ico = '';
		$min = false;
		foreach ($format['data'] as $k => $v) if (isset($v['id']) && !isset($v['error'])) {
			if ($ico == '' && !empty($v['ico'])) $ico = $v['ico'];

			$p = $v['price'] + (isset($v['priceinfo']) ? $v['priceinfo'] : 0);
			if ($min === false || $p < $min['price']) $min = array('price' => $p, 'key' => $k);
			$price = self::SetRange($price, $p);

			if (!empty($v['day'])) {
				$s = preg_replace("/[^0-9-]/i", "", $v['day']);
				$s = explode('-', $s);

				$day = self::SetRange($day, $s[0]);
				if (!empty($s[1])) $day = self::SetRange($day, $s[1]);
			}

			if ($cod && isset($v['pricecod'])) $pricecod = self::SetRange($pricecod, $v['pricecod'], $v['pricecod_formatted']);
		}
		if ($min !== false) {
			$v = $min + $format['data'][$min['key']];
			$v['price_formatted'] = self::GetPrice('formatted', $v['price'], '', $currency);
			$format['min'] = $v;
		}
		$price['min']['formatted'] = self::GetPrice('formatted', $price['min']['value'], '', $currency);
		$price['max']['formatted'] = self::GetPrice('formatted', $price['max']['value'], '', $currency);
		$format['price'] = $price;
		$format['pricecod'] = $pricecod;
		$format['pricehead'] = self::AddRange($price, $pricecod);
		$format['day'] = $day; //self::GetDay($day['min']['value'], $day['max']['value']);
		$format['ico'] = $ico;
	}
	public static function SetRange($range = false, $value = 0, $formatted = '') {
		if ($range === false) return array('min' => array('value' => -1, 'formatted' => ''), 'max' => array('value' => -1, 'formatted' => ''));
		if ($range['min']['value'] == -1 || $value < $range['min']['value']) $range['min'] = array('value' => $value, 'formatted' => $formatted);
		if ($range['max']['value'] == -1 || $value > $range['max']['value']) $range['max'] = array('value' => $value, 'formatted' => $formatted);
		return $range;
	}
	public static function AddRange($range = false, $range2) {
		if ($range === false) return $range2;
		if ($range2['min']['value'] >= 0) $range = self::SetRange($range, $range2['min']['value'], $range2['min']['formatted']);
		if ($range2['max']['value'] >= 0) $range = self::SetRange($range, $range2['max']['value'], $range2['max']['formatted']);
		return $range;
	}
	public static function GetRange($range) {
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$a = ($range['min']['value'] != $range['max']['value'] ? true : false);
		$r = ($a && $range['min']['value'] !== '0' ? '<br>' : '');
		$r = ($a ? $sign['from'] . $range['min']['formatted'] . $r . $sign['to'] : '') . $range['max']['formatted'];
		return $r;
	}


	// получение бонусов обычной доставки по сравнению с наложенным платежом
	public static function GetDeliveryBonus($data, $cod, $template_format) {

		$r = array();
		$sign = GetMessage('EDOST_DELIVERY_SIGN');
		$bonus = $sign['bonus'];

		$ar = array(
			'odt' => array('office', 'door', 'post'),
			'dot' => array('door', 'office', 'post'),
			'tod' => array('post', 'office', 'door'),
		);
		$ar = (isset($ar[$template_format]) ? $ar[$template_format] : $ar['odt']);

		foreach ($data as $k => $v) foreach ($ar as $v2) if (!isset($v[$v2]) || $v[$v2] === '') $data[$k][$v2] = -1;

		$c = $data[$cod];
		if (!isset($c['office_count'])) $c['office_count'] = 0;
		foreach ($data as $k => $v) if ($k != $cod) {
			if (!isset($v['office_count'])) $v['office_count'] = 0;

			$s = array();

			$u = array('free' => array(), 'lower' => array(), 'exists' => array());
			foreach ($ar as $f) {
				if ($v[$f] == 0 && $c[$f] != 0) $u['free'][] = $bonus[$f];
				else if ($v[$f] > 0 && $c[$f] > 0 && $v[$f] < $c[$f]*0.9) $u['lower'][] = $bonus[$f];
				else if ($v[$f] > 0 && $c[$f] < 0) $u['exists'][] = $bonus[$f];
			}
			foreach ($u as $k2 => $v2) if (!empty($v2)) $s[] = $bonus[$k2].' '.self::GetStringList($v2);

			if (count($s) < 3) {
				$n = $v['office_count'] - $c['office_count'];
				if ($n > 0 && $c['office_count'] != 0) $s[] = '+'.self::draw_string('office', $n);
			}

			if (!empty($s)) $r[$k] = $s;
		}

		return $r;

	}


	// перевод массива в строку вида "1, 2 и 3"
	public static function GetStringList($ar) {
        $r = '';
		if (!empty($ar)) {
			$sign = GetMessage('EDOST_DELIVERY_SIGN');
			$r = $ar[0];
			$n = count($ar) - 1;
			for ($i = 1; $i <= $n; $i++) $r .= ($i != $n ? ', ' : $sign['and']).$ar[$i];
		}
		return $r;
	}


	// получение ссылки на офис
	public static function GetOfficeLink($office) {
		$protocol = CDeliveryEDOST::GetProtocol();
		$s = (!empty($office['detailed']) ? str_replace('%id%', $office['id'], $office['detailed']) : $protocol.'edost.ru/office.php?c='.$office['id']);
		return ($s == 'N' ? '' : $s);
	}


	// упаковка данных регистрации
	public static function PackRegisterData($v) {

		if (!isset($v['props'])) return;

		$register_profile = self::GetRegisterProfile();
		$shop = (!empty($v['batch']['profile_shop']) && isset($register_profile[$v['batch']['profile_shop']]) ? $register_profile[$v['batch']['profile_shop']] : false);
		$delivery = (!empty($v['batch']['profile_delivery']) && isset($register_profile[$v['batch']['profile_delivery']]) ? $register_profile[$v['batch']['profile_delivery']] : false);

		$p = $v['props'];

		if (!isset($v['country']) && !empty($p['location_data'])) $v += $p['location_data'];

		if ($shop !== false) {
			$shop_user = array(
				'company' => $shop['company'],
				'inn' => $shop['inn'],
				'passport' => $shop['passport'],
				'appointment' => $shop['appointment'],
				'name' => $shop['name'],
				'represented' => $shop['represented'],
				'basis' => $shop['basis'],
			);
			$shop_address = array(
				'city_id' => $shop['city'],
				'street' => $shop['address'][0],
				'house_1' => $shop['address'][1],
				'door_1' => $shop['address'][2],
				'phone' => $shop['phone'],
			);
		}
		else {
			$s = $v['shop_data'];
			$bank = array();
			$bank_key = array('rsch', 'ksch', 'bank', 'bik');
			foreach ($bank_key as $v2) $bank[] = str_replace('/', ' ', $s[$v2]);
			$shop_user = array('company' => $s['name'], 'name' => $s['inn'], 'name_first' => implode('/', $bank), 'name_middle' => $s['phone']);
			$shop_address = array('address' => $s['address_full'], 'street' => $s['zip']);
			$seller_user = array();
			$seller_address = array();
		}

		$s = array();
		if (!isset($p['name_part'])) $s['name'] = $p['name'];
		else foreach ($p['name_part'] as $k2 => $v2) $s['name_'.$k2] = $v2;
		$s['company'] = $p['company'];
		$s['passport'] = $p['passport'];
		$user = array($s, $shop_user);
		$u = array();
		$ar = array('company', 'account', 'secure', 'token', 'contract', 'format', 'vat', 'appointment', 'name', 'represented', 'basis', 'zip', 'online_balance', 'batch_format');
		foreach ($ar as $v2) $u[$v2] = (isset($delivery[$v2]) ? $delivery[$v2] : '');
		$user[2] = $u;
		if ($shop !== false) {
			$user[3] = array(
				'company' => $shop['company_seller'],
				'companytype' => $shop['companytype_seller'],
				'inn' => $shop['inn_seller'],
			);
		}
		$v['user_data'] = self::PackDataArray($user, 'user'); // [покупатель, магазин, служба доставки, истинный продавец]

		$v['phone'] = $p['phone'];
		$v['zip'] = $p['zip'];
		$v['email'] = $p['email'];

		$s = array();
		if (!empty($p['office'])) $s = array('id' => $p['office']['id'], 'code' => $p['office']['code']);
		else if (!isset($p['address_part'])) $s['address'] = $p['address'];
		else $s += $p['address_part'];

		$address = array($s, $shop_address);
		if ($shop !== false) {
			$address[2] = array(
				'city_id' => $shop['city_call'],
				'street' => $shop['address_call'][0],
				'house_1' => $shop['address_call'][1],
				'door_1' => $shop['address_call'][2],
				'phone' => $shop['phone_call'],
				'lunch' => implode('.', $shop['time_lunch_call']),
				'call' => implode('.', $shop['time_call']),
				'comment' => $shop['comment_call'],
			);
			$address[3] = array(
				'street' => $shop['address_seller'][0],
				'house_1' => $shop['address_seller'][1],
				'door_1' => $shop['address_seller'][2],
				'phone' => $shop['phone_seller'],
			);
		}
		$v['address_data'] = self::PackDataArray($address, 'address'); // [покупатель, магазин, откуда забирать груз, истинный продавец]

		if (!empty($v['basket'])) {
			// загрузка маркировочных кодов

			// !!!!!
			// тестовый маркировочный код
			// [BARCODE_INFO][1][BARCODE][0][MARKING_CODE] = 010290000042703721lmLGsXQsTZps91802992sOAoOnzm8RoAfWTzzLcyU9P4JXMhwjwGg9Ctqw30TXjBFit8s5GGgH1VK/sSaLcvC+c961T5kkbHPbANCO0ssQ==
/*
			foreach ($v['basket'] as $k2 => $v2) {
				$v['basket'][$k2]['barcode'] = 'barcode';
				$v['basket'][$k2]['marking_code'] = array('010290000042703721lmLGsXQsTZps91802992sOAoOnzm8RoAfWTzzLcyU9P4JXMhwjwGg9Ctqw30TXjBFit8s5GGgH1VK/sSaLcvC+c961T5kkbHPbANCO0ssQ==', 'marking_code2');
				break;
			}
*/
			$a = false;
			foreach ($v['basket'] as $v2) if (!empty($v2['MARKING_CODE_GROUP'])) { $a = true; break; }
			if ($a) {
				 $order = \Bitrix\Sale\Order::load($v['order_id']);
				 if ($order) {
				 	$shipment = $order->getShipmentCollection()->getItemById($v['id']);
					if ($shipment) {
						$shipmentItemCollection = $shipment->getShipmentItemCollection();
						foreach ($shipmentItemCollection as $item) {
							$id = $item->getBasketId();
							if (!isset($v['basket'][$id])) continue;
//							$v['basket'][$id]['ORDER_DELIVERY_BASKET_ID'] = $item->getId();
							$itemStoreCollection = $item->getShipmentItemStoreCollection();
							foreach ($itemStoreCollection as $barcode) {
								$v['basket']['barcode'] = $barcode->getId();
								if (!isset($v['basket'][$id]['marking_code'])) $v['basket'][$id]['marking_code'] = array();
								$v['basket'][$id]['marking_code'][] = $barcode->getMarkingCode();
							}
						}
					}
				}
			}

			$ar = array();
			foreach ($v['basket'] as $v2) {
				$ar[$v2['ID']] = $v2;
				foreach ($v2['set'] as $s) $ar[$s['ID']] = $s;
			}
			foreach ($ar as $k2 => $v2) if (!empty($v2['article'])) $ar[$k2]['PRODUCT_ID'] = $v2['article'];
			$v['basket'] = $ar;
		}

		$v['basket_data'] = self::PackDataArray($v['basket'], 'basket2');
		$v['package_data'] = self::PackDataArray($p['package'], 'package');
		$v['batch_data'] = self::PackDataArray($v['batch'], 'batch');

		return $v;

	}

	// упаковка данных в одну строку
	public static function PackData($data, $key) {

		$r = '';
		$key_count = count($key) - 1;

		foreach ($data as $v) {
			$s = 'end|';
			$start = false;
			for ($i = $key_count; $i >= 0; $i--) {
				if (!$start && isset($v[$key[$i]])) $start = true;
				if ($start) {
					$p = (isset($v[$key[$i]]) ? $v[$key[$i]] : '');
					if ($p != '') {
						if (in_array($key[$i], array('tracking_code'))) $p = $GLOBALS['APPLICATION']->ConvertCharset($p, LANG_CHARSET, 'windows-1251');
						$p = urlencode(str_replace('|', '', $p));
					}
					$s = ($p !== 'end' ? $p : '').'|'.$s;
				}
			}
			$r .= $s;
		}

		return $r;

	}

	// упаковка массива в одну строку
	public static function PackDataArray($data, $key, $level = 0) {

		$delimiter = self::$delimiter[$level];

		if (!is_array($key)) {
			$key = explode('_', $key);
			$key = $key[0];
			if (isset(self::$data_key[$key.'_first'])) {
				$key = self::$data_key[$key.'_first'];
				$data = array($data);
			}
			else if (isset(self::$data_key[$key])) $key = self::$data_key[$key];
			else return '';
		}

		$r = array();
		foreach ($data as $v) {
	        $s = array();
			foreach ($key as $k2 => $v2) {
				if (isset(self::$delimiter2[$v2])) {
					if (!empty($v[$v2])) foreach ($v[$v2] as $u_key => $u) $v[$v2][$u_key] = self::PackDataFilter($u);
					$s[] = (!empty($v[$v2]) ? implode(self::$delimiter2[$v2][0], $v[$v2]) : '');
				}
				else if (is_array($v2)) $s[] = (isset($v[$k2]) ? self::PackDataArray($v[$k2], $v2, $level + 1) : '');
				else $s[] = (isset($v[$v2]) ? self::PackDataFilter($v[$v2]) : '');
			}
			$r[] = implode($delimiter[0], $s);
		}
		$r = implode($delimiter[1], $r);

		return $r;

	}

	// подготовка данных к упаковке
	public static function PackDataFilter($s) {
		$s = $GLOBALS['APPLICATION']->ConvertCharset($s, LANG_CHARSET, 'windows-1251');
		if ($s === 'end') $s = '%8';
		else if ($s === 'key') $s = '%9';
		else $s = str_replace(array('%', ',', ':', '|', '/', '=', ';'), array('%1', '%2', '%3', '%4', '%5', '%6', '%7'), $s);
		return $s;
	}
	// подготовка данных к распаковке
	public static function UnPackDataFilter($s) {
		if ($s === '%8') $s = 'end';
		else if ($s === '%9') $s = 'key';
		else $s = str_replace(array('%2', '%3', '%4', '%5', '%6', '%7', '%1'), array(',', ':', '|', '/', '=', ';', '%'), $s);
		return $s;
	}

	// распаковка массива
	public static function UnPackDataArray($s, $key, $level = 0) {

		if (empty($s) || empty($key)) return '';

		$first = false;
		if (!is_array($key)) {
			$key = explode('_', $key);
			$key = $key[0];
			if (isset(self::$data_key[$key.'_first'])) {
				$key = self::$data_key[$key.'_first'];
				$first = true;
			}
			else if (isset(self::$data_key[$key])) $key = self::$data_key[$key];
			else return '';
		}

		$delimiter = self::$delimiter[$level];

		$r = array();

		$s = explode($delimiter[1], $s);
		foreach ($s as $k => $v) {
			$v = explode($delimiter[0], $v);
			$id = (isset($key[0]) && $key[0] === 'id' ? $v[0] : $k);
			$i = 0;
			foreach ($key as $k2 => $v2) {
				if (!isset($v[$i])) break;
				if (is_array($v2)) $r[$id][$k2] = self::UnPackDataArray($v[$i], $v2, 1);
				else {
					if (!isset(self::$delimiter2[$v2])) $p = self::UnPackDataFilter($v[$i]);
					else if (empty($v[$i])) $p = self::$delimiter2[$v2][1];
					else {
						$p = explode(self::$delimiter2[$v2][0], $v[$i]);
						foreach ($p as $w => $u) $p[$w] = self::UnPackDataFilter($u);
					}
					$r[$id][$v2] = $p;
				}
				$i++;
			}
		}

		if ($first && !empty($r)) foreach ($r as $k => $v) { $r = $v; break; }

		return $r;

	}


	// распаковка ограничений
	public static function UnPackLimit($s, $type = false) {

		$r = array();
		$start = 0;
		$limit_key = self::$limit_key;
		if ($type !== false) {
			$start = 1;
			$limit_key = array_merge(array('type'), $limit_key);
		}
		$count = count($limit_key);
		$s = str_split($s);
		if (count($s) == 1) $s = (isset(self::$limit_code_string[$s[0]]) ? str_split(self::$limit_code_string[$s[0]]) : array());
		$k = -1;
		foreach ($s as $v) {
			$k++;
			if ($k >= $count) break;
			$key = $limit_key[$k];
			if ($key == 'tariff') $v = (isset(self::$limit_code2[$v]) ? self::$limit_code2[$v] : 0); else $v = (isset(self::$limit_code[$v]) ? self::$limit_code[$v] : 0);
			if ($key == 'weight_to') {
				if ($v == 1) {
					$k += 6;
					$v = 0;
				}
				else if ($v > 0 && $v < 5) return array();
			}
			if ($key == 'size1') {
				if ($v == 0) $k += 2;
				else if ($v == 1 || $v == 2) {
					$limit_key[2 + $start] = ($v == 1 ? 'volume_weight' : 'volume');
					$limit_key[3 + $start] = 'sizemax';
					$v = 0;
				}
				else if ($v < 5) return array();
			}
			if ($v == 0) continue;
			if ($key == 'price') $v = $v*1000;
			if ($key == 'volume_weight') $v = $v*10;
			if ($key == 'volume') $v = round($v/1000, 2);
			$r[$key] = $v;
		}
		return $r;

	}


	// распаковка 'schedule'
	public static function UnPackSchedule($s) {

		if ($s === '') return '';

		$r = array();

		if (isset(self::$schedule_code[$s])) {
			$s = self::$schedule_code[$s];
			if (in_array($s, array('system_1', 'system_2', 'system_3', 'system_4'))) $s = '';
		}

		$s = explode(self::$schedule_key_enter, $s);
		foreach ($s as $v) {
			$i = -1;
			if (isset(self::$schedule_code[$v])) $v = self::$schedule_code[$v];
			if (substr($v, 0, 1) == self::$schedule_key_original) $v = substr($v, 1);
			else {
				$u = str_split($v);
				$v = '';
				foreach ($u as $o) if (isset(self::$schedule_code[$o])) {
					if ($o == '.') $v .= ' ';
					$o = self::$schedule_code[$o];
					if (is_int($o)) {
						$i++;
						if ($i == 1 || $i == 3) $o = $o*5;
						if ($o <= 9) $o = '0'.$o;
						if ($i == 0) $o = ($v != '' ? ' ' : '').'с '.$o.':';
						if ($i == 2) $o = ' до '.$o.':';
					}
					$v .= $o;
				}
			}
			$r[] = $v;
		}

//		return implode("\n", $r);
		return implode(", ", $r);

	}


	// определение формата доставки по id тарифа
	public static function GetFormat($v) {
		$r = '';
		if (isset($v['tariff']))
			if (in_array($v['tariff'], CDeliveryEDOST::$post)) $r = 'post';
			else if (in_array($v['tariff'], CDeliveryEDOST::$office)) $r = 'office';
			else if (!in_array($v['tariff'], array(1, 2, 61, 68, 69, 70, 71, 72, 73, 74))) $r = 'door';
		return $r;
	}

	// иконка и описание тарифа для контроля и оформления
	public static function TariffHead($v, $ico_path = '/bitrix/images/delivery_edost_img') {

		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
?>
		<img class="edost_ico edost_ico_company_small" style="padding: 0 3px 0 0;<?=(empty($v['company_id']) ? ' width: 28px;' : '')?>" src="<?=$ico_path.(!empty($v['company_id']) ? '/company/'.$v['company_id'] : '/small/'.$v['tariff'])?>.gif" border="0" title="<?=$v['title']?>">
<?
		if (!empty($v['name_short'])) echo $v['name_short'].'<br>';

//		echo '<br><b>arResult[DELIVERY]:</b> <pre style="font-size: 12px">'.print_r($v, true).'</pre>';

		$f = self::GetFormat($v);
		$s = '';
		if ($f == 'post') $s = 'style="color: #b600ff; cursor: default;">'.$control_sign['post'];
		else if ($f == 'office') {
			if (!isset($v['props']) || !empty($v['props']['office'])) {
				if (isset($v['props']) && in_array($v['props']['office']['type'], CDeliveryEDOST::$postamat)) $s = $control_sign['postamat'];
				else $s = $control_sign['office'];
				$s = 'style="color: #b600ff; cursor: default;">'.$s;
			}
		}
		else if ($f == 'door') $s = 'style="color: #ff008b; cursor: default;">'.$control_sign['door'];

		if ($s != '')
			if (empty($v['address_short'])) echo '<b '.$s.'</b>';
			else echo '<b class="edost_hint_link" data-param="click=Y;shift=center,20;style;width=280px" '.$s.'</b><div class="edost_hint_data">'.$v['address_short'].'</div>';

		if (!empty($v['delivery_price_formatted'])) echo ($s != '' ? '&nbsp;&nbsp;' : '').'<span style="color: #555; font-weight: bold;">'.$v['delivery_price_formatted'].'</span>';

		if (!empty($v['cod']))
			if (!empty($v['cod_formatted'])) echo '<br><span style="color: #b59422; font-weight: bold;"> '.$control_sign['cod'].' '.$v['cod_formatted'].'</span>';
			else echo ' <b style="color: #b59422;">'.$control_sign['cod'].'</b>';

	}

	// иконки компаний доставки в заголовке для страницы контроля/оформления
	public static function ControlHead($data, $param) {

		$control_sign = GetMessage('EDOST_DELIVERY_CONTROL');
		$option = $control_sign['register_option'];
		$head = $control_sign['count_head'.($param['type'] == 'register' ? '_register' : '')];
		$color = array('warning_pink' => 'pink', 'warning_red' => 'red', 'warning_orange' => 'orange', 'complete' => 'green', 'register_new' => 'blue', 'register_complete' => 'orange', 'register_complete_batch' => 'purple', 'register_complete_batch_full' => 'green', 'batch_20' => 'light2', 'add' => 'light2', 'register' => 'light2', 'transfer' => 'light2', 'delete' => 'light2', 'complete_paid' => 'purple', 'complete_paid2' => 'purple');
		$company = (isset($param['company']) ? $param['company'] : 0);

		if ($company == 5) $head['register_complete_batch_full'] = $head['register_complete'];
		if (in_array($company, array(30,19))) $head['register_complete_batch_full'] = $head['register_complete_batch'];

		$ico = array();
		if ($param['type'] == 'register' && !empty($data)) {
			$ico = array_fill_keys(array_keys(CDeliveryEDOST::$register_tariff), '');
			$count = array_fill_keys(array_keys(CDeliveryEDOST::$register_tariff), array_fill_keys(array_keys($head), 0));

			$first = ($company !== false ? $company : 0);
			$first2 = 0;
			foreach ($data as $k => $v) if (!empty($v['company_id'])) {
				$id = $v['company_id'];
				if (isset($param['control'][$k])) $v += $param['control'][$k];

				if (empty($v['register'])) {
					if (empty($first)) $first = $id;

					$count[$id]['register_new']++;
				}
				else {
					if (empty($first2)) $first2 = $id;

					$count[$id]['total']++;
					if (!empty($v['new'])) $count[$id]['new']++;
					if (!empty($v['special'])) $count[$id]['special']++;
					if ($v['status_warning'] == 1) $count[$id]['warning_pink']++;
					if ($v['status_warning'] == 3) $count[$id]['warning_orange']++;

					if ($v['status'] == 20) $count[$id]['status_20']++;
					else if ($v['status_warning'] == 2) $count[$id]['warning_red']++;
					else if (in_array($v['register'], array(2, 9, 10))) $count[$id]['transfer']++;

					if (in_array($v['register'], array(4, 5))) {
						if (!empty($v['batch_code'])) {
							if (!empty($v['batch_20'])) $count[$id]['batch_20']++;
							else if ($v['register'] == 4) $count[$id]['register_complete_batch']++;
							else $count[$id]['register_complete_batch_full']++;
						}
						else $count[$id]['register_complete']++;
					}
					if (in_array($v['register'], array(6, 7, 8))) $count[$id]['delete']++;
				}
			}

			if (empty($first)) $first = $first2;
			if ($company !== false) $company = $first;
			$param['count'] = $count[$first];

//			echo '<br><b>count:</b><pre style="font-size: 12px; text-align: left;">'.print_r($count, true).'</pre>';
		}

		if (!empty($data)) foreach ($data as $k => $v) if (empty($data[$k]['set'])) {
			$n = 1;
			$data[$k]['set'] = true;
			foreach ($data as $k2 => $v2) if (empty($data[$k2]['set']) && $v['company'] == $v2['company']) {
				$data[$k2]['set'] = true;
				$n++;
			}
			if ($param['type'] == 'register') $ico[$v['company_id']] =
				'<div class="edost_button_company edost_button_company_'.($company == $v['company_id'] ? 'on' : 'off').'" onclick="edost.admin.set_param(\''.$param['type'].'\', \'company_'.$v['company_id'].'\''.(empty($count[$v['company_id']]['register_new']) ? ', \'total\'' : '').')">'.
				(!empty($count[$v['company_id']]['register_new']) ? '<div><div class="edost_register_new" style="margin-left: '.(!empty($option[$v['company_id']]['link_shift']) ? $option[$v['company_id']]['link_shift'].'px' : '100px').'">'.$count[$v['company_id']]['register_new'].'</div></div>' : '').
				'<img class="edost_ico" src="'.$param['path'].'/company/'.$v['company_id'].'.gif" border="0">'.
				'<span>'.$option[$v['company_id']]['name'].'</span>'.
				'</div>';
			else $ico[] =
				'<div style="display: inline-block;" class="edost_control_link" onclick="edost.admin.set_param(\''.$param['type'].'\', \'company_'.$v['tariff'].'\')">'.
				'<img class="edost_ico edost_ico_company_small" style="width: '.(!empty($v['company_id']) ? 32 : 64).'px; height: 32px;'.($param['active'] == 'company' && $company == $v['company'] ? 'padding: 4px; margin: 2px 5px 2px 0; border: 2px solid #e0e0e0; border-radius: 10px;' : 'margin: 0 2px 0 0;').'" src="'.$param['path'].(!empty($v['company_id']) ? '/company/'.$v['company_id'] : '/'.$v['tariff']).'.gif" border="0" title="'.$v['company'].'">'.
				'<span style="vertical-align: middle; font-weight: bold;">'.($n != 1 ? $n : '').'&nbsp;</span>'.
				'</div>&nbsp;&nbsp;';
		}
		foreach ($ico as $k => $v) if (empty($v)) unset($ico[$k]);
		if ($param['type'] == 'register' && !empty($data)) {
			foreach ($ico as $k2 => $v2) {
				$c = $count[$k2];
				$s = '';
				$n = 0;
				foreach ($head as $k => $v) if (!empty($c[$k]) != 0 && !in_array($k, array('register_new'))) {
					if ($k != 'total') $n++;
					else if ($n == 1) break;

					$s .= '<div class="edost_control_link edost_control_color_'.(isset($color[$k]) ? $color[$k] : 'no').'" onclick="edost.admin.set_param(\''.$param['type'].'\', \'company_'.$k2.'\', \''.$k.'\')">'.
							'<div style="display: inline-block; min-width: 20px; font-weight: bold;">'.$c[$k].'</div>'.
						'</div>';
				}
				if ($s != '') $ico[$k2] .= '<div style="display: inline-block; vertical-align: middle;">'.$s.'</div>';
			}
		}
		$n = ($param['type'] == 'register' ? 0 : 1);
		$ico_count = count($ico);
		$ico = ($ico_count > $n ? implode('', $ico) : '');
		if ($param['type'] == 'register') $ico .= '<input id="edost_register_company" type="hidden" value="'.$company.'">';

		$count_list = $count_list2 = '';
		if (($param['type'] != 'register' || $company !== false) && !empty($param['count'])) foreach ($head as $k => $v) if (!empty($param['count'][$k]) != 0) {
			if ($k == 'new') $style = 'border-width: 0 0 1px 0; border-color: #888; border-style: solid;';
			else if ($k == 'special') $style = 'border-width: 0 0 1px 0; border-color: #888; border-style: solid;';
			else $style = '';

			$s = '<div style="display: inline-block;'.($k == $param['active'] ? ' background: #DDD;' : '').($style != '' ? ' '.$style : '').'" class="edost_control_link'.(isset($color[$k]) ? ' edost_control_color_'.$color[$k] : '').'" onclick="edost.admin.set_param(\''.$param['type'].'\', '.($param['type'] == 'register' ? '\'company_'.$company.'\', ' : '').'\''.$k.'\')">'.
					'<div style="display: inline-block; width: 230px; text-align: right; font-weight: bold;">'.$v.':</div> '.
					'<div style="display: inline-block; min-width: 40px; font-weight: bold;">'.$param['count'][$k].'</div>'.
				'</div><br>';

			if (in_array($k, array('special', 'delay', 'office', 'add', 'register', 'transfer', 'delete', 'complete', 'complete_paid', 'total', 'status_20'))) $count_list2 .= $s; else $count_list .= $s;
		}

		return array('count_list' => $count_list, 'count_list2' => $count_list2, 'ico' => $ico, 'ico_count' => $ico_count, 'company' => $company);

	}


	// упаковка одного товара
	public static function PackItem(&$total, $s, $quantity) {

		if (empty($s) || !($s[0] > 0 && $s[1] > 0 && $s[2] > 0 && $quantity > 0)) return false;

		sort($s); // сортировка габаритов по возрастанию

		if ($quantity == 1) $p = array($s[0], $s[1], $s[2]);
		else if ($quantity > 1000) {
			$v = round(pow($s[0]*$s[1]*$s[2]*$quantity, 1/3), 3);
			$p = array($v, $v, $v);
		}
		else {
			$x1 = $y1 = $z1 = $l = 0;
			$max1 = floor(sqrt($quantity));
			for ($y = 1; $y <= $max1; $y++) {
				$i = ceil($quantity / $y);
				$max2 = floor(sqrt($i));

				for ($z = 1; $z <= $max2; $z++) {
					$x = ceil($i/$z);

					$l2 = $x*$s[0] + $y*$s[1] + $z*$s[2];
					if ($l == 0 || $l2 < $l) {
						$l = $l2;
						$x1 = $x;
						$y1 = $y;
						$z1 = $z;
					}
				}
			}
			$p = array($x1*$s[0], $y1*$s[1], $z1*$s[2]);
		}

		$total['package'][] = array('size' => $s, 'quantity' => $quantity, 'volume' => $s[0]*$s[1]*$s[2]*$quantity, 'pack' => $p);

		return true;

	}

	// упаковка разных товаров
	public static function PackItems($p) {

		if (empty($p)) return array(0, 0, 0);

		// сортировка габаритов по убыванию + расчет суммы габаритов
		foreach ($p as $k => $v) {
			rsort($v);
			$v['sum'] = $v[0] + $v[1] + $v[2];
			$p[$k] = $v;
		}

		$n = count($p);
		for ($i3 = 1; $i3 < $n; $i3++) {
			// сортировка товаров по возрастанию
			$s = array();
			foreach ($p as $v) $s[] = $v['sum'];
			array_multisort($s, SORT_ASC, SORT_NUMERIC, $p);

			// упаковка двух самых маленьких товаров
			$w = array($p[$i3][0], $p[$i3][1], $p[$i3][2]);
			if ($p[$i3-1][0] > $w[0]) $w[0] = $p[$i3-1][0];
			if ($p[$i3-1][1] > $w[1]) $w[1] = $p[$i3-1][1];
			$w[2] = $w[2] + $p[$i3-1][2];
			rsort($w);
			$w['sum'] = $w[0] + $w[1] + $w[2];
			$p[$i3] = $w;
			$p[$i3-1]['sum'] = 0;
		}

		$r = array(round($p[$n-1][0], 3), round($p[$n-1][1], 3), round($p[$n-1][2], 3));
		sort($r); // сортировка габаритов по возрастанию

		return $r;

	}

	// упаковка заказа (оптимизация + учет степени уплотнения: $power = 1 - без уплотнения, 5 - расчет по объему)
	public static function PackOrder($p, $power) {

		$r = array(0, 0, 0);
		if (empty($p)) return $r;

		if (count($p) == 1 && $p[0]['quantity'] <= 2) $r = $p[0]['pack'];
		else {
			$s = array();
			foreach ($p as $v) $s[] = $v['volume'];
			array_multisort($s, SORT_ASC, SORT_NUMERIC, $p);

			$q = 50; // количество товаров, которые можно упаковать в одну коробку
			$n = ceil(count($p) / $q);
			$new = array();
			for ($i = 0; $i < $n; $i++) {
				$s = array_slice($p, $i*$q, $q);
				$volume = 0;
				$pack = array();
				$size = array(0, 0, 0);
				foreach ($s as $v) {
					$volume += $v['volume'];
					sort($v['size']);
					foreach ($v['size'] as $k2 => $v2) if ($v2 > $size[$k2]) $size[$k2] = $v2;
					$pack[] = $v['pack'];
				}
				$pack = self::PackItems($pack);
				$new[] = array(
					'size' => $size,
					'quantity' => 1,
					'volume' => $volume,
					'pack' => $pack,
				);
			}

			if (count($new) > 1) return self::PackOrder($new, $power); // повторная упаковка, если получилось больше одной коробки

			if (!empty($new[0]['pack'])) {
				$r = $pack;

				// учет степени уплотнения
				if ($power != 1 && $volume != 0 && $size[0] > 0 && $size[1] > 0 && $size[2] > 0) {
					$v = $volume + ($r[0]*$r[1]*$r[2] - $volume)*(5 - $power)*0.25;
					$s = round(pow($v, 1/3));
					$s = array(0, $s, $s);
					if ($size[2] > $s[2]) {
						$s[2] = $size[2];
						$s[1] = round(pow($v / $s[2], 0.5));
					}
					if ($size[1] > $s[1]) $s[1] = $size[1];
					$s[0] = round($v / ($s[1] * $s[2]) + 0.5);
					$r = $s;
				}
			}
		}

		sort($r);

		return $r;

	}

	// данные для подключения скриптов и стилей
	public static function GetScriptData($config, $file = array()) {

		$script = COption::GetOptionString('edost.delivery', 'script', '');
		if (!empty($script)) $config['template_script'] = $script;

		$protocol = CDeliveryEDOST::GetProtocol();
		$win = (strtoupper(LANG_CHARSET) != 'UTF-8' ? 'win/' : '');
		$server = (!isset($config['template_script']) || $config['template_script'] == 'Y' ? true : false);
		$key = ($server ? date('dmY') : self::$version); //.rand();
		$path = ($server ? $protocol.'edostimg.ru/shop/'.self::$version.'/' : '/bitrix/js/edost.delivery/'.$win);
		$charset = ($server ? ' charset="utf-8"' : '');

		return array(
			'protocol' => $protocol,
			'version' => self::$version,
			'server' => $server,
			'path' => $path,
			'file' => 'main.js?a='.$key,
			'main' => '<script id="edost_main_js" type="text/javascript" src="'.$path.'main.js?a='.$key.'"'.$charset.(!$server ? ' data-path="'.$path.'"' : ' data-version="'.self::$version.'"').(!empty($file) ? ' data-file="'.implode(',', $file).'"' : '').'></script>',
			'css' => $path.'main.css?a='.$key,
		);

	}

	// получение иконки компании
	public static function GetCompanyIco($company_id, $tariff_id) {
		$r = intval($company_id);
		if ($tariff_id == 35) $r = 's1';
		if ($tariff_id >= 56 && $tariff_id <= 58) $r = 's'.($tariff_id - 54);
		if ($tariff_id >= 31 && $tariff_id <= 34) $r = 'v'.($tariff_id - 30);
		return $r;
	}

	// получение тарифов компаний
	public static function GetCompanyTariff($new = false, $filter = false) {

		if (!empty($new)) {
			\Bitrix\Main\Config\Option::set('edost.delivery', 'company_tariff', serialize($new));
			return;
		}

		$r = \Bitrix\Main\Config\Option::get('edost.delivery', 'company_tariff');
		$r = ($r != '' ? unserialize($r) : array());

		if ($filter) {
			$s = array();
			if (!empty($r)) foreach ($r as $k => $v) foreach ($v as $k2 => $v2) $s[$k2] = $k;
			$r = $s;
		}

		return $r;

	}


	// получение местоположения из куки
	public static function GetLocationIdCode($id) {
		if (empty($id)) return array('id' => 0, 'code' => '');
		else return (substr($id, 0, 1) !== '0' ? array('id' => intval($id), 'code' => CSaleLocation::getLocationCODEbyID($id)) : array('id' => CSaleLocation::getLocationIDbyCODE($id), 'code' => $id));
	}
	public static function GetLocationCode($id) {
		if ($id && substr($id, 0, 1) !== '0') $id = CSaleLocation::getLocationCODEbyID(intval($id));
		return $id;
	}
	public static function GetLocationID($id) {
		if ($id && substr($id, 0, 1) === '0') $id = CSaleLocation::getLocationIDbyCODE($id);
		return $id;
	}
	public static function LocationCookie($param = '') {
		$c = (isset($_COOKIE['edost_location']) ? substr($_COOKIE['edost_location'], 0, 250) : '');
		if ($param  == 'string') return $c;
		if ($param  == 'change') return ($c && !empty($_SESSION['EDOST']['location_cookie']) && $c != $_SESSION['EDOST']['location_cookie'] ? true : false);
		$r = false;
		if ($c) {
			$s = explode('|', $c); // id|zip|city2
			if (isset($s[2])) {
				$id = $s[0];
				$zip = preg_replace("/[^0-9.]/i", "", $s[1]);
				$r = array('city2' => $GLOBALS['APPLICATION']->ConvertCharset($s[2], 'utf-8', LANG_CHARSET));
				if (substr($zip, -1) == '.') $zip = substr($zip, 0, -1); else if ($zip != '') $r['zip_full'] = true;
				$r['zip'] = $zip;
				$r += self::GetLocationIdCode($id);
			}
		}
		return $r;
	}

	// конвертация многомерных массивов в строку ($delimiter - массив разделителей по уровням массива)
	public static function implode2($delimiter, $data, $n = 0) {

		if (empty($data)) return '';
		if (!is_array($data)) return $data;

		$s = '';
		$n++;

		$a = $delimiter;
		if (is_array($a)) {
			if (isset($a[$n-1])) $a = $a[$n-1];
			else if (isset($a[count($a)-1])) $a = $a[count($a)-1];
			else $a = '';
		}

		$s = array();
		foreach ($data as $v) $s[] = (is_array($v) ? self::implode2($delimiter, $v, $n) : $v);
		$s = implode($a, $s);

		return $s;

	}

	// слияние многомерных массивов
	public static function array_merge_recursive2(&$s1, $s2) {
		foreach ($s2 as $k => $v) {
			if (!isset($s1[$k])) $s1[$k] = $v;
			else if (is_array($v)) self::array_merge_recursive2($s1[$k], $v);
		}
	}

	// перенос элементов + удаление отсутствующих
	public static function array_add(&$s1, $s2, $key) {
		foreach ($key as $k)
			if (isset($s2[$k])) $s1[$k] = $s2[$k];
			else if (isset($s1[$k])) unset($s1[$k]);
	}

	// преобразование даты из строки во время
	public static function time($s) {
		$s = explode('.', $s);
		return (isset($s[2]) ? mktime(0, 0, 0, $s[1], $s[0], $s[2]) : 0);
	}

	// вывод чисел
	public static function draw_number($name, $n) {
		if ($name == 'time') if (strlen($n) == 1) $n = '0'.$n;
		return $n;
	}

	// вывод значений со склонением
	public static function draw_string($name, $n) {

		$sign = GetMessage('EDOST_DELIVERY_CONTROL');
		$ar = $sign['string'];

		$s = '';
		if ($n >= 11 && $n <= 19) $s = $ar[$name][2];
		else {
			$x = $n % 10;
			if ($x == 1) $s = $ar[$name][0];
			else if ($x >= 2 && $x <= 4) $s = $ar[$name][1];
			else $s = $ar[$name][2];
		}

		return $n.' '.$s;

	}

	// ограничение длины строки ($html == true - вывод оригинальной и ограниченной строк для html c заменой кавычек)
	public static function limit_string($s, $max, $html = false) {

		$o = $s;
		$n = (function_exists('mb_strlen') ? mb_strlen($s, LANG_CHARSET) : strlen($s));
		if ($n > $max) $s = (function_exists('mb_substr') ? mb_substr($s, 0, $max, LANG_CHARSET) : substr($s, 0, $max)).'...';

		if (!$html) return $s;

		$ar = array($o, $s);
		foreach ($ar as $k => $v) $ar[$k] = str_replace(array('"', "'"), array('&quot;', '&quot;'), $v);
		return $ar;

	}

	public static function service_string($name, &$s, $active = false) {
		$r = '';
		$s = explode('['.$name.']', $s);
		if (!empty($s[1])) {
			$u = explode('[/'.$name.']', $s[1]);
			if (isset($u[1])) {
				$r = trim($u[0]);
				$s[0] .= ($active ? $r : '').$u[1];
			}
		}
		$s = trim($s[0]);
		return $r;
	}

}

?>