<?
/*********************************************************************************
Пользовательские функции модуля eDost (при обновлении данный файл не переписывается)

Для подключения в файле 'edost_const.php' должна быть установлена константа:
define('DELIVERY_EDOST_FUNCTION', 'Y');
*********************************************************************************/
CModule::IncludeModule('sale');	
class edost_function {

	// вызывается перед расчетом доставки
	public static function BeforeCalculate(&$order, &$config) {


/*
echo "<!--";
$ar = CSaleDeliveryHandler::GetList(array('SORT' => 'ASC'), array('COMPABILITY' => $order));
while ($v = $ar->Fetch())
	foreach ($v['PROFILES'] as $profile_id => $profile)
		echo '<b>'.$v['SID'].':'.$profile_id.'</b> - '.$profile['TITLE'].($profile['DESCRIPTION'] !== '' ? ' ('.$profile['DESCRIPTION'].')' : '').'<br>';
echo "-->";
echo '<pre style="display:none">'.print_r($order, true).'</pre>';
*/
	//AddMessage2Log(print_r($config , true));
/*
		$order - оригинальный массив битрикса с параметрами расчета
		$config - настройки модуля

		return false; // продолжить выполнение расчета
		return array('hide' => true); // отключить модуль (не производится запрос на сервер, не выводится ошибка)
		return array('data' => array( тарифы доставки )); // сбросить расчет и заменить результат массивом 'data' (формат должен соответствовать стандарту eDost)
*/

//		echo '<br><b>BeforeCalculate - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';

//		$_SESSION['EDOST']['LOCATION_TO'] = CDeliveryEDOST::GetEdostLocation($order['LOCATION_TO']);
//		echo '<br>SERVER[REQUEST_URI]:'.$_SERVER['REQUEST_URI'];
//		$_SESSION['EDOST']['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
//		unset($_SESSION['EDOST']['office_default']); // сбросить выбранные на карте офисы
//		$order['LOCATION_TO'] = 10000000000;

/*
		// вывести собственный тариф для указанных местоположений (вместо реального расчета)
		$ar = array('5979', '5980'); // CODE местоположений
		if (in_array($order['LOCATION_TO'], $ar)) {
			$order['location'] = CDeliveryEDOST::GetEdostLocation($order['LOCATION_TO']);
			if ($order['location'] === false) return false;

			return array(
				'sizetocm' => '1', // коэффициент пересчета габаритов магазина в сантиметры
				'data' => array(
					9 => array( // тариф "СПСР Экспресс"
						'id' => 5,
						'price' => 400,
						'priceinfo' => 0,
						'pricecash' => 500,
						'transfer' => 0,
						'day' => '3-4 дня',
						'insurance' => 0,
						'company' => 'СПСР Экспресс',
						'name' => 'пеликан-стандарт',
						'format' => 'door',
						'company_id' => 1,
						'city' => '',
						'profile' => 9,
						'sort' => 4,
					)
				)
			);
		}
*/

/*
		// изменить ид и пароль от сервера eDost (например, когда у магазина несколько филиалов в разных городах, и требуется изменять город отправки в зависимости от местонахождения покупателя)
		$config['id'] = '12345';
		$config['ps'] = 'aaaaa';
*/

		// отключить модуль на странице оформления заказа
//		if (strpos($_SERVER['REQUEST_URI'], '/personal/order/make') === 0) return array('hide' => true);

		// отключить модуль в карточке товара
//		if (strpos($_SERVER['REQUEST_URI'], '/catalog') === 0 || strpos($_SERVER['REQUEST_URI'], '/bitrix/components/edost/catalogdelivery') === 0) return array('hide' => true);

/*
		// отключить модуль для указанных местоположений
		$ar = array(5979, 5980); // ID местоположений
		if (in_array($order['LOCATION_TO'], $ar)) return array('hide' => true);
*/

		return false;

	}

	// вызывается после обработки параметров заказа и перед запросом на сервер eDost
	public static function BeforeCalculateRequest(&$order, &$config) {
/*
		$order - модифицированный массив битрикса с параметрами расчета
		$config - настройки модуля

		return false; // продолжить выполнение расчета
		return array('hide' => true); // отключить модуль (не производится запрос на сервер, не выводится ошибка)
		return array('data' => array( тарифы доставки )); // сбросить расчет и заменить результат массивом 'data' (формат должен соответствовать стандарту eDost)

		расчет производится по параметрам:
			$order['LOCATION_TO'] - ид местоположения битрикса
			$order['LOCATION_ZIP'] - почтовый индекс (если пустой, тогда не передается на сервер расчета)
			$order['WEIGHT'] - вес заказа в граммах
			$order['PRICE'] - цена заказа в рублях
			$order['size'] - массив с габаритами заказа (единица измерения должна совпадать с размерностью в личном кабинете eDost)
				Предупреждение: на выходе габариты должны быть отсортированы по возрастанию - пример: $order['size'] = array(30, 10, 20);  sort($order['size']);
*/

//		echo '<br><b>BeforeCalculateRequest - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';

//		$order['size'] = array(10, 20, 30);
//		$order['LOCATION_TO'] = 1;
//		$order['WEIGHT'] = 500;
//		$order['WEIGHT'] += 32000;
//		$order['PRICE'] = 1000;

/*
		// установить местоположение расчета по стандарту eDost (вместо стандартного расчета по коду битрикса $order['LOCATION_TO'])
		$order['location'] = array(
		    'country' => 0, // код страны стандарта eDost (0 - Россия)
		    'region' => 59, // код региона стандарта eDost
		    'city' => 'Пермь', // название города в кодировке win
		);
//		$order['LOCATION_TO'] = 100; // если 'LOCATION_TO' не передан, тогда для корректной работы кэширования, обязательно должен быть присвоен уникальный код местоположения (можно свой)
*/

/*
		// добавить вес на упаковку для указанных местоположений
		$ar = array(5979, 5980); // ID местоположений
		if (in_array($order['LOCATION_TO'], $ar)) $order['WEIGHT'] += 1000;
*/

		return false;

	}

	// вызывается после расчета доставки
	public static function AfterCalculate($order, $config, &$result) {
/*
		$order - модифицированный массив битрикса с параметрами расчета
		$config - настройки модуля
		$result - результат расчета
*/

if (empty($result['cache']) && !empty($result['data'])) $result['data_original'] = $result['data']; // сохранение оригинального расчета
else if (isset($result['data_original'])) $result['data'] = $result['data_original'];

/* Казань - 300 */

if ($order['location']['id']=='1796'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*Москва - 350*/

if ($order['location']['id']=='91'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 350;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 350;}
}

		/*Краснодар - 300*/

if ($order['location']['id']=='1370'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*Екатеринбург - 300*/

if ($order['location']['id']=='2441'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*Нижний Новгород - 300*/

if ($order['location']['id']=='1936'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*Челябинск - 300*/

if ($order['location']['id']=='2592'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*Самара - 300*/

if ($order['location']['id']=='2069'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*Омск - 350*/

if ($order['location']['id']=='2897'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 350;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 350;}
}

		/*Новосибирск - 350*/

if ($order['location']['id']=='2852'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 350;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 350;}
}

		/*Санкт-Петербург - 150*/

if ($order['location']['id']=='92'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 150;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 150;}
}

//		echo '<br><b>AfterCalculate - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterCalculate - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';
//AddMessage2Log(print_r($result, true));

/*
		// новый вызов / загрузка из кэша (включается константой 'DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE' = 'Y')
		if (empty($result['cache'])) {
			// данные получены с сервера eDost (модифицированные здесь данные будут закэшированы)
			echo 'new';
		}
		else {
			// данные загружены из кэша магазина (модифицированные здесь данные пойдут на вывод, но следующий раз из кэша загрузится оригинальный вариант)
			echo 'cache';
		}
*/

/*
		// при наличии товаров на складе поставщика в городе покупателя, пунктам выдачи СДЭК присваивается цена из тарифа "Самовывоз 4" (в заказе сохраняется самый дешевый тариф СДЭК из доступных)
		if (empty($result['cache']) && !empty($result['data'])) $result['data_original'] = $result['data']; // сохранение оригинального расчета
		else if (isset($result['data_original'])) $result['data'] = $result['data_original'];

		if (!empty($result['data'])) {
			$company = 5; // код "СДЭК"
			$shop = 's4'; // код "Самовывоз 4"
			$ar = array();
			if (!empty($order['ITEMS'])) foreach ($order['ITEMS'] as $v) $ar[] = $v['PRODUCT_ID'];
			if (!empty($ar)) {
				$store = false;
				$ar = CCatalogStore::GetList(array('SORT' => 'ASC'), array('PRODUCT_ID' => $ar, 'ACTIVE' => 'Y'), false, false, array('ID', 'TITLE', 'ADDRESS', 'PRODUCT_AMOUNT'));
				while ($v = $ar->fetch()) if ($v['TITLE'] == $order['location']['bitrix']['city']) { // название склада (TITLE) должно соответствовать названию города покупателя!!!
					if (!empty($v['PRODUCT_AMOUNT'])) $store = true;
					else { $store = false; break; }
				}
				if ($store) {
					$shop_k = false;
					foreach ($result['data'] as $k => $v) if ($v['company_id'] == $shop) $shop_k = $k;
					if ($shop_k !== false) {
						$p = false;
						foreach ($result['data'] as $k => $v) if ($v['company_id'] == $company && $v['format'] == 'office' && ($p === false || $v['price'] < $p)) { $company_k = $k; $p = $v['price']; }
						if ($p !== false) {
							$ar = array('price', 'pricecash', 'day', 'insurance');
							foreach ($ar as $v) $result['data'][$company_k][$v] = $result['data'][$shop_k][$v];
							foreach ($result['data'] as $k => $v) if ($v['company_id'] == $company && $v['format'] == 'office' && $k != $company_k) unset($result['data'][$k]);
						}
					}
				}
			}
		}
*/

/*
		// сбросить формат доставки в карточке товара (тарифы будут выводиться без разделения на "Курьером до двери", "До пункта выдачи" и т.д.)
		if (strpos($_SERVER['REQUEST_URI'], '/catalog') === 0 || strpos($_SERVER['REQUEST_URI'], '/bitrix/components/edost/catalogdelivery') === 0)
			if (!empty($result['data'])) foreach ($result['data'] as $k => $v) $result['data'][$k]['format'] = '';
*/

/*
		// в строке доставки заменить 'дни' на 'рабочие дни'
		if (empty($result['cache']) && !empty($result['data'])) foreach ($result['data'] as $k => $v) if (!empty($v['day'])) {
			$result['data'][$k]['day'] = str_replace(array('день', 'дня', 'дней'), array('рабочий день', 'рабочих дня', 'рабочих дней'), $v['day']);
		}
*/

/*
		// исключение стоимости доставки из итого (для почты и EMS)
		$id = array(1, 2, 3, 61, 62, 68); // id тарифов стандарта eDost
		if (empty($result['cache']) && !empty($result['data'])) foreach ($result['data'] as $k => $v)
			if (in_array($v['id'], $id) && $v['price'] > 0) {
				$result['data'][$k]['priceinfo'] = $v['price'];
				$result['data'][$k]['price'] = 0;
			}
*/

		// удаление из расчета тарифа "DPD (parcel до пункта выдачи)" (код 91)
//		if (isset($result['data']['91'])) unset($result['data']['91']);

/*
		// 50% скидка на тариф "Курьер 1" (код 61) при заказе в субботу-воскресенье (предупреждение: используется время сервера - оно может отличаться от часового пояса магазина и покупателя)
		if (isset($result['data']['61'])) {
			if (empty($result['cache'])) $result['data']['61']['price_original'] = $result['data']['61']['price'];
			$p = $result['data']['61']['price_original'];
			if (date('N') >= 6) $p = round($p*0.5);
			$result['data']['61']['price'] = $p;
		}
*/

/*
		// изменение стоимости доставки тарифа "PickPoint"
		$id = 57; // PickPoint
		if (empty($result['cache']) && isset($result['data'][$id])) {
			// установка фиксированной стоимости доставки для указанных местоположений
			$ar = array(5979, 5980); // ID местоположений
			if (in_array($order['LOCATION_TO'], $ar)) {
				$result['data'][$id]['price'] = 250; // стоимость доставки
				$result['data'][$id]['pricecash'] = 250; // стоимость доставки при наложенном платеже (-1 - отключить наложенный платеж)
			}

			// установить эксклюзивную стоимость для пунктов выдачи с типом 5
			$result['data'][$id]['priceoffice'] = array(
				5 => array(
					'type' => 5,
					'price' => $result['data'][$id]['price'] + 100, // стандартная цена доставки + 100 руб.
					'priceinfo' => 0,
					'pricecash' => 800, // наложка
				),
			);
		}
*/
	}


	// вызывается перед загрузкой данных по пунктам выдачи
	public static function BeforeGetOffice($order, &$company) {
/*
		$order - параметры заказа
		$company - коды eDost компаний доставки для которых требуется загрузить данные
*/
//		echo '<br><b>AfterGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterGetOffice - company:</b> <pre style="font-size: 12px">'.print_r($company, true).'</pre>';

	}


	// вызывается после загрузки данных по пунктам выдачи
	public static function AfterGetOffice($order, &$result) {
/*
		$order - параметры заказа
		$result - пункты выдачи
*/
//		echo '<br><b>AfterGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r(CDeliveryEDOST::$result, true).'</pre>';

/*
		// перенос офисов СДЭК в тариф "Самовывоз 1"
		if (empty($result['cache'])) {
			$from = 5; // код "СДЭК"
			$to = 's1'; // код "Самовывоз 1"
			if (!empty($result['data'][$from])) {
				$result['data'][$to] = $result['data'][$from];
				unset($result['data'][$from]);
				if (!empty($result['limit'])) foreach ($result['limit'] as $k => $v) if ($v['company_id'] == $from) $result['limit'][$k]['company_id'] = $to;
			}
		}
*/

		// удаление пункта выдачи тарифа 'Самовывоз 1' (код 's1')
//		if (isset($result['data']['s1'])) unset($result['data']['s1']);

/*
		// генерация пункта выдачи для тарифа 'Самовывоз 1' (код 's1')
		if (empty($result['cache'])) $result['data']['s1'] = array(
			'12345A12345' => array(
				'id' => '12345A12345',
				'code' => '',
				'name' => 'ТЦ Калач',
				'address' => 'Москва, ул. Академика Янгеля, д. 6, корп. 1',
				'address2' => 'оф. 5',
				'tel' => '+7-123-123-45-67',
				'schedule' => 'с 10 до 20, без выходных',
				'gps' => '37.592311,55.596037',
				'type' => 3,
				'metro' => '',
			),
		);
*/
	}


	// вызывается после загрузки документов (почтовые бланки, шаблоны для печати и т.д.)
	public static function AfterGetDocument($setting, &$result) {
/*
		$setting - настройки печати
		$result - документы
*/
//		echo '<br><b>AfterGetDocument - setting:</b> <pre style="font-size: 12px">'.print_r($setting, true).'</pre>';
//		echo '<br><b>AfterGetDocument - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';

/*
		// заполнение полей отправителя в ф.116 (по умолчанию поля заполняются значениями из настроек печатных форм битрикса)
		$result['data']['116']['data'] = str_replace(
			array('%company_name%', '%company_address%', '%company_zip%'),
			array('ООО "Ромашка"', 'ул. Академика Янгеля, д. 6, г. Москва', '101000'),
			$result['data']['116']['data']
		);
*/
	}

}
?>