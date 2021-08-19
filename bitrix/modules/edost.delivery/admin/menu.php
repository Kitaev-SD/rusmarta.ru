<?
$right = $APPLICATION->GetGroupRight('sale'); // [D] закрыт,  [U] обработка заказов,  [W] полный доступ
if ($right == 'D') return false;

if (!empty($_GET['edost_disable']) && $_GET['edost_disable'] == 'Y') return;

$s = getLocalPath('modules/edost.delivery/classes/general/delivery_edost.php');
if (empty($s)) return;

$s2 = getLocalPath('modules/edost.delivery/admin/edost.php');
if (empty($s2)) return;
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].$s2);

if (!class_exists('edost_class')) require_once($_SERVER['DOCUMENT_ROOT'].$s);

$admin_sign = GetMessage('EDOST_ADMIN');
$key = array('control', 'register', 'setting', 'paysystem');
$show = array_fill_keys($key, false);

if ($right == 'W') {
	$show['setting'] = true;
	$show['paysystem'] = true;
}

$config = CDeliveryEDOST::GetEdostConfig('all');
if (!empty($config)) foreach ($config as $v) if (!empty($v['id']) && !empty($v['ps'])) {
	if ($v['control'] == 'Y') {
		$show['control'] = true;
		$show['register'] = true;
	}
}

$aMenu = array(
	'parent_menu' => 'global_menu_store', // 'global_menu_settings' - раздел 'настройки'
	'section' => 'edost_delivery',
	'sort' => 105,
	'text' => 'eDost',
	'url' => 'edost.php?lang='.LANGUAGE_ID,
	'icon' => 'edost_menu_icon',
	'page_icon' => 'edost_page_icon',
	'items_id' => 'edost',
//	'module_id' => 'edost.delivery', // идентификатор модуля, к которому относится меню
	'more_url' => array('edost.php'),
	'items' => array()
);

foreach ($key as $v) if ($show[$v]) $aMenu['items'][] = array(
	'text' => $admin_sign['title'][$v],
	'url' => 'edost.php?lang='.LANGUAGE_ID.'&type='.$v,
	'more_url'  => array(),
);

return $aMenu;
?>