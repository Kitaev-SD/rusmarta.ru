<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

$mode = $arResult['mode'];
$control = GetMessage('EDOST_DELIVERY_CONTROL');
$param = (isset($arResult['param']) ? $arResult['param'] : false);
$shipment = (!empty($arResult['shipment']) ? $arResult['shipment'] : array());
//echo '<br><b>template param</b> <pre style="font-size: 12px">'.print_r($param, true).'</pre>';
//echo '<br><b>shipment</b> <pre style="font-size: 12px">'.print_r($shipment, true).'</pre>';

// список с заказами покупателя (в шапке сайта)
if ($mode == 'user_order') {
	if (!empty($param['user_order_max'])) $shipment = array_slice($shipment, 0, $param['user_order_max']);

	$head = '';
	if (count($shipment) == 1) $head = (isset($param['user_order_head']) ? $param['user_order_head'] : $control['user_order_head'].':');
	else $head = (isset($param['user_order_head2']) ? $param['user_order_head2'] : $control['user_order_head2'].':');
	if ($head != '') $head .= '<br>';

	echo '<div'.(!empty($param['div_class']) ? ' class="'.$param['div_class'].'"' : '').(!empty($param['div_style']) ? ' style="'.$param['div_style'].'"' : '').'>';
	echo $head;
	foreach ($shipment as $k => $v) {
		$order_number = $v['order_number'];
		if (!empty($param['order_number_replace'])) $order_number = str_replace($param['order_number_replace'][0], $param['order_number_replace'][1], $order_number);
		$link = (!empty($param['order_detail_link']) ? str_replace(array('%order_id%', '%order_number%'), array($v['order_id'], $order_number), $param['order_detail_link']) : '/personal/order/detail/'.$order_number.'/');
		echo '<a'.(!empty($param['a_class']) ? ' class="'.$param['a_class'].'"' : '').''.(!empty($param['a_style']) ? ' style="'.$param['a_style'].'"' : '').' href="'.$link.'#shipment_'.$v['id'].'" target="_blank">';
		echo (empty($param['user_order_number_hide']) ? $v['order_number'].': ' : '').$v['status_short'];
		echo '</a><br>';
	}
	echo '</div>';
}

?>