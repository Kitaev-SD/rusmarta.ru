<?
/*********************************************************************************
���������������� ������� ������ eDost (��� ���������� ������ ���� �� ��������������)

��� ����������� � ����� 'edost_const.php' ������ ���� ����������� ���������:
define('DELIVERY_EDOST_FUNCTION', 'Y');
*********************************************************************************/
CModule::IncludeModule('sale');	
class edost_function {

	// ���������� ����� �������� ��������
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
		$order - ������������ ������ �������� � ����������� �������
		$config - ��������� ������

		return false; // ���������� ���������� �������
		return array('hide' => true); // ��������� ������ (�� ������������ ������ �� ������, �� ��������� ������)
		return array('data' => array( ������ �������� )); // �������� ������ � �������� ��������� �������� 'data' (������ ������ ��������������� ��������� eDost)
*/

//		echo '<br><b>BeforeCalculate - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';

//		$_SESSION['EDOST']['LOCATION_TO'] = CDeliveryEDOST::GetEdostLocation($order['LOCATION_TO']);
//		echo '<br>SERVER[REQUEST_URI]:'.$_SERVER['REQUEST_URI'];
//		$_SESSION['EDOST']['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
//		unset($_SESSION['EDOST']['office_default']); // �������� ��������� �� ����� �����
//		$order['LOCATION_TO'] = 10000000000;

/*
		// ������� ����������� ����� ��� ��������� �������������� (������ ��������� �������)
		$ar = array('5979', '5980'); // CODE ��������������
		if (in_array($order['LOCATION_TO'], $ar)) {
			$order['location'] = CDeliveryEDOST::GetEdostLocation($order['LOCATION_TO']);
			if ($order['location'] === false) return false;

			return array(
				'sizetocm' => '1', // ����������� ��������� ��������� �������� � ����������
				'data' => array(
					9 => array( // ����� "���� ��������"
						'id' => 5,
						'price' => 400,
						'priceinfo' => 0,
						'pricecash' => 500,
						'transfer' => 0,
						'day' => '3-4 ���',
						'insurance' => 0,
						'company' => '���� ��������',
						'name' => '�������-��������',
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
		// �������� �� � ������ �� ������� eDost (��������, ����� � �������� ��������� �������� � ������ �������, � ��������� �������� ����� �������� � ����������� �� ��������������� ����������)
		$config['id'] = '12345';
		$config['ps'] = 'aaaaa';
*/

		// ��������� ������ �� �������� ���������� ������
//		if (strpos($_SERVER['REQUEST_URI'], '/personal/order/make') === 0) return array('hide' => true);

		// ��������� ������ � �������� ������
//		if (strpos($_SERVER['REQUEST_URI'], '/catalog') === 0 || strpos($_SERVER['REQUEST_URI'], '/bitrix/components/edost/catalogdelivery') === 0) return array('hide' => true);

/*
		// ��������� ������ ��� ��������� ��������������
		$ar = array(5979, 5980); // ID ��������������
		if (in_array($order['LOCATION_TO'], $ar)) return array('hide' => true);
*/

		return false;

	}

	// ���������� ����� ��������� ���������� ������ � ����� �������� �� ������ eDost
	public static function BeforeCalculateRequest(&$order, &$config) {
/*
		$order - ���������������� ������ �������� � ����������� �������
		$config - ��������� ������

		return false; // ���������� ���������� �������
		return array('hide' => true); // ��������� ������ (�� ������������ ������ �� ������, �� ��������� ������)
		return array('data' => array( ������ �������� )); // �������� ������ � �������� ��������� �������� 'data' (������ ������ ��������������� ��������� eDost)

		������ ������������ �� ����������:
			$order['LOCATION_TO'] - �� �������������� ��������
			$order['LOCATION_ZIP'] - �������� ������ (���� ������, ����� �� ���������� �� ������ �������)
			$order['WEIGHT'] - ��� ������ � �������
			$order['PRICE'] - ���� ������ � ������
			$order['size'] - ������ � ���������� ������ (������� ��������� ������ ��������� � ������������ � ������ �������� eDost)
				��������������: �� ������ �������� ������ ���� ������������� �� ����������� - ������: $order['size'] = array(30, 10, 20);  sort($order['size']);
*/

//		echo '<br><b>BeforeCalculateRequest - arOrder:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';

//		$order['size'] = array(10, 20, 30);
//		$order['LOCATION_TO'] = 1;
//		$order['WEIGHT'] = 500;
//		$order['WEIGHT'] += 32000;
//		$order['PRICE'] = 1000;

/*
		// ���������� �������������� ������� �� ��������� eDost (������ ������������ ������� �� ���� �������� $order['LOCATION_TO'])
		$order['location'] = array(
		    'country' => 0, // ��� ������ ��������� eDost (0 - ������)
		    'region' => 59, // ��� ������� ��������� eDost
		    'city' => '�����', // �������� ������ � ��������� win
		);
//		$order['LOCATION_TO'] = 100; // ���� 'LOCATION_TO' �� �������, ����� ��� ���������� ������ �����������, ����������� ������ ���� �������� ���������� ��� �������������� (����� ����)
*/

/*
		// �������� ��� �� �������� ��� ��������� ��������������
		$ar = array(5979, 5980); // ID ��������������
		if (in_array($order['LOCATION_TO'], $ar)) $order['WEIGHT'] += 1000;
*/

		return false;

	}

	// ���������� ����� ������� ��������
	public static function AfterCalculate($order, $config, &$result) {
/*
		$order - ���������������� ������ �������� � ����������� �������
		$config - ��������� ������
		$result - ��������� �������
*/

if (empty($result['cache']) && !empty($result['data'])) $result['data_original'] = $result['data']; // ���������� ������������� �������
else if (isset($result['data_original'])) $result['data'] = $result['data_original'];

/* ������ - 300 */

if ($order['location']['id']=='1796'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*������ - 350*/

if ($order['location']['id']=='91'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 350;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 350;}
}

		/*��������� - 300*/

if ($order['location']['id']=='1370'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*������������ - 300*/

if ($order['location']['id']=='2441'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*������ �������� - 300*/

if ($order['location']['id']=='1936'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*��������� - 300*/

if ($order['location']['id']=='2592'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*������ - 300*/

if ($order['location']['id']=='2069'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 300;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 300;}
}

		/*���� - 350*/

if ($order['location']['id']=='2897'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 350;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 350;}
}

		/*����������� - 350*/

if ($order['location']['id']=='2852'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 350;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 350;}
}

		/*�����-��������� - 150*/

if ($order['location']['id']=='92'){
foreach ($result['data'] as $k => $v) if ($v['format'] == 'office') { $result['data'][$k]['price'] = 150;
if ($v['pricecash'] != -1) $result['data'][$k]['pricecash'] = 150;}
}

//		echo '<br><b>AfterCalculate - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterCalculate - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';
//AddMessage2Log(print_r($result, true));

/*
		// ����� ����� / �������� �� ���� (���������� ���������� 'DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE' = 'Y')
		if (empty($result['cache'])) {
			// ������ �������� � ������� eDost (���������������� ����� ������ ����� ������������)
			echo 'new';
		}
		else {
			// ������ ��������� �� ���� �������� (���������������� ����� ������ ������ �� �����, �� ��������� ��� �� ���� ���������� ������������ �������)
			echo 'cache';
		}
*/

/*
		// ��� ������� ������� �� ������ ���������� � ������ ����������, ������� ������ ���� ������������� ���� �� ������ "��������� 4" (� ������ ����������� ����� ������� ����� ���� �� ���������)
		if (empty($result['cache']) && !empty($result['data'])) $result['data_original'] = $result['data']; // ���������� ������������� �������
		else if (isset($result['data_original'])) $result['data'] = $result['data_original'];

		if (!empty($result['data'])) {
			$company = 5; // ��� "����"
			$shop = 's4'; // ��� "��������� 4"
			$ar = array();
			if (!empty($order['ITEMS'])) foreach ($order['ITEMS'] as $v) $ar[] = $v['PRODUCT_ID'];
			if (!empty($ar)) {
				$store = false;
				$ar = CCatalogStore::GetList(array('SORT' => 'ASC'), array('PRODUCT_ID' => $ar, 'ACTIVE' => 'Y'), false, false, array('ID', 'TITLE', 'ADDRESS', 'PRODUCT_AMOUNT'));
				while ($v = $ar->fetch()) if ($v['TITLE'] == $order['location']['bitrix']['city']) { // �������� ������ (TITLE) ������ ��������������� �������� ������ ����������!!!
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
		// �������� ������ �������� � �������� ������ (������ ����� ���������� ��� ���������� �� "�������� �� �����", "�� ������ ������" � �.�.)
		if (strpos($_SERVER['REQUEST_URI'], '/catalog') === 0 || strpos($_SERVER['REQUEST_URI'], '/bitrix/components/edost/catalogdelivery') === 0)
			if (!empty($result['data'])) foreach ($result['data'] as $k => $v) $result['data'][$k]['format'] = '';
*/

/*
		// � ������ �������� �������� '���' �� '������� ���'
		if (empty($result['cache']) && !empty($result['data'])) foreach ($result['data'] as $k => $v) if (!empty($v['day'])) {
			$result['data'][$k]['day'] = str_replace(array('����', '���', '����'), array('������� ����', '������� ���', '������� ����'), $v['day']);
		}
*/

/*
		// ���������� ��������� �������� �� ����� (��� ����� � EMS)
		$id = array(1, 2, 3, 61, 62, 68); // id ������� ��������� eDost
		if (empty($result['cache']) && !empty($result['data'])) foreach ($result['data'] as $k => $v)
			if (in_array($v['id'], $id) && $v['price'] > 0) {
				$result['data'][$k]['priceinfo'] = $v['price'];
				$result['data'][$k]['price'] = 0;
			}
*/

		// �������� �� ������� ������ "DPD (parcel �� ������ ������)" (��� 91)
//		if (isset($result['data']['91'])) unset($result['data']['91']);

/*
		// 50% ������ �� ����� "������ 1" (��� 61) ��� ������ � �������-����������� (��������������: ������������ ����� ������� - ��� ����� ���������� �� �������� ����� �������� � ����������)
		if (isset($result['data']['61'])) {
			if (empty($result['cache'])) $result['data']['61']['price_original'] = $result['data']['61']['price'];
			$p = $result['data']['61']['price_original'];
			if (date('N') >= 6) $p = round($p*0.5);
			$result['data']['61']['price'] = $p;
		}
*/

/*
		// ��������� ��������� �������� ������ "PickPoint"
		$id = 57; // PickPoint
		if (empty($result['cache']) && isset($result['data'][$id])) {
			// ��������� ������������� ��������� �������� ��� ��������� ��������������
			$ar = array(5979, 5980); // ID ��������������
			if (in_array($order['LOCATION_TO'], $ar)) {
				$result['data'][$id]['price'] = 250; // ��������� ��������
				$result['data'][$id]['pricecash'] = 250; // ��������� �������� ��� ���������� ������� (-1 - ��������� ���������� ������)
			}

			// ���������� ������������ ��������� ��� ������� ������ � ����� 5
			$result['data'][$id]['priceoffice'] = array(
				5 => array(
					'type' => 5,
					'price' => $result['data'][$id]['price'] + 100, // ����������� ���� �������� + 100 ���.
					'priceinfo' => 0,
					'pricecash' => 800, // �������
				),
			);
		}
*/
	}


	// ���������� ����� ��������� ������ �� ������� ������
	public static function BeforeGetOffice($order, &$company) {
/*
		$order - ��������� ������
		$company - ���� eDost �������� �������� ��� ������� ��������� ��������� ������
*/
//		echo '<br><b>AfterGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterGetOffice - company:</b> <pre style="font-size: 12px">'.print_r($company, true).'</pre>';

	}


	// ���������� ����� �������� ������ �� ������� ������
	public static function AfterGetOffice($order, &$result) {
/*
		$order - ��������� ������
		$result - ������ ������
*/
//		echo '<br><b>AfterGetOffice - order:</b> <pre style="font-size: 12px">'.print_r($order, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';
//		echo '<br><b>AfterGetOffice - result:</b> <pre style="font-size: 12px">'.print_r(CDeliveryEDOST::$result, true).'</pre>';

/*
		// ������� ������ ���� � ����� "��������� 1"
		if (empty($result['cache'])) {
			$from = 5; // ��� "����"
			$to = 's1'; // ��� "��������� 1"
			if (!empty($result['data'][$from])) {
				$result['data'][$to] = $result['data'][$from];
				unset($result['data'][$from]);
				if (!empty($result['limit'])) foreach ($result['limit'] as $k => $v) if ($v['company_id'] == $from) $result['limit'][$k]['company_id'] = $to;
			}
		}
*/

		// �������� ������ ������ ������ '��������� 1' (��� 's1')
//		if (isset($result['data']['s1'])) unset($result['data']['s1']);

/*
		// ��������� ������ ������ ��� ������ '��������� 1' (��� 's1')
		if (empty($result['cache'])) $result['data']['s1'] = array(
			'12345A12345' => array(
				'id' => '12345A12345',
				'code' => '',
				'name' => '�� �����',
				'address' => '������, ��. ��������� ������, �. 6, ����. 1',
				'address2' => '��. 5',
				'tel' => '+7-123-123-45-67',
				'schedule' => '� 10 �� 20, ��� ��������',
				'gps' => '37.592311,55.596037',
				'type' => 3,
				'metro' => '',
			),
		);
*/
	}


	// ���������� ����� �������� ���������� (�������� ������, ������� ��� ������ � �.�.)
	public static function AfterGetDocument($setting, &$result) {
/*
		$setting - ��������� ������
		$result - ���������
*/
//		echo '<br><b>AfterGetDocument - setting:</b> <pre style="font-size: 12px">'.print_r($setting, true).'</pre>';
//		echo '<br><b>AfterGetDocument - result:</b> <pre style="font-size: 12px">'.print_r($result, true).'</pre>';

/*
		// ���������� ����� ����������� � �.116 (�� ��������� ���� ����������� ���������� �� �������� �������� ���� ��������)
		$result['data']['116']['data'] = str_replace(
			array('%company_name%', '%company_address%', '%company_zip%'),
			array('��� "�������"', '��. ��������� ������, �. 6, �. ������', '101000'),
			$result['data']['116']['data']
		);
*/
	}

}
?>