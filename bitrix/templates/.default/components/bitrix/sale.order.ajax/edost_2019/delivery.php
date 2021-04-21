<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<? if (isset($arResult['edost']['format'])) { ?>
<?
	$data = (isset($arResult['edost']) ? $arResult['edost'] : false);
	$no_insurance = ($arParams['NO_INSURANCE'] == 'Y' ? true : false);
	$calculate_function = (!empty($data['calculate_function']) ? $data['calculate_function'] : "submitForm()");
	$cod_head_bookmark2 = str_replace('<br>', ' ', $sign['cod_head_bookmark']);
//	$ico_loading_map_inside = '<div class="edost_map_loading"><img src="'.$ico_path.'/loading_small.gif" border="0" width="64" height="64"></div>'; // иконка загрузки для интегрированной карты

	if ($edost_catalogdelivery) $ico_default = (!empty($param['ico_default']) ? $param['ico_default'] : $arResult['component_path'].'/images/logo-default-d.gif');
	else $ico_default = $templateFolder.'/images/logo-default-d.gif';

	$map_inside = (!$edost_catalogdelivery && empty($data['map_inside']) || empty($data['format']['map_inside']) || $data['format']['map_inside'] == 'N' ? '' : $data['format']['map_inside']);

	$tariff_price_hide = array(); // трифы без вывода стоимости доставки (сравнивается параметр 'html_id')

	$office_main = array('office', 'postmap', 'shop');
	$office_main2 = array();
	foreach ($office_main as $v) $office_main2[$v] = 'edost_'.$v;

	// собственное описание для групп пунктов выдачи
//	$sign['office_description']['shop'] = ''; // адреса магазинов
//	$sign['office_description']['office'] = ''; // пункты выдачи
//	$sign['office_description']['terminal'] = ''; // терминалы
?>

<script type="text/javascript">
	function edost_SetBookmark(id, bookmark) {

		var start = false;
		if (bookmark == undefined) bookmark = '';
		if (id == 'start') {
			start = true;
			E2 = document.getElementById('edost_bookmark');
			if (E2) id = E2.value;
			if (id == '') return;
		}

		var ar = ['office', 'door', 'house', 'post', 'postmap', 'general', 'show'];
		for (var i = 0; i < ar.length; i++) {
			var E = document.getElementById('edost_' + ar[i] + '_div');
			var E_map = (ar[i] == 'office' ? document.getElementById('edost_' + ar[i] + '_map_div') : false);
			var E2 = document.getElementById('edost_' + ar[i] + '_td');
			if (!E && !E2) continue;

			var E3 = document.getElementById('edost_' + ar[i] + '_td_bottom');
			var show = (ar[i] == id ? true : false);
			if (E2) {
				E2.className = 'edost_bookmark edost_active_' + (show ? 'on' : 'off');
				var E5 = document.getElementById('edost_' + ar[i] + '_td2');
				if (E5) E5.className = 'edost_bookmark edost_active_' + (show ? 'on' : 'off');
			}
			if (E3) {
				E3.className = 'edost_active_fon_' + (show ? 'on' : 'off');
				var E5 = document.getElementById('edost_' + ar[i] + '_td_bottom2');
				if (E5) E5.className = 'edost_active_fon_' + (show ? 'on' : 'off');
			}
<?			if (!$edost_catalogdelivery) { ?>
			if (E)
				if (!start) E.style.display = 'none';
				else if (bookmark == 1) E.style.display = (show ? '' : 'none');
<?			} else { ?>
			if (E) E.style.display = (show ? '' : 'none');
<?			} ?>
			if (E_map) E_map.style.display = E.style.display;
		}

		var E = document.getElementById('edost_bookmark_delimiter');
		if (E) E.className = 'edost_active_fon_on';

		if (!start) {
			var E = document.getElementById('edost_bookmark_loading');
			if (E) {
				E.innerHTML = '<span class="edost_template_color"><?=$sign['loading2']?></span>';
				E.style.display = 'block';
			}

			var E = document.getElementById('edost_bookmark_info');
			if (E) E.style.display = 'none';

			E = document.getElementById('edost_bookmark');
			if (E) E.value = id + '_s';

<?			if (!$edost_catalogdelivery) { ?>
			<?=$calculate_function?>;
<?			} ?>
		}

<?		if ($edost_catalogdelivery && $map_inside == 'Y') { ?>
		if (id == 'office') edost_RunScript('map_inside');
<?		} ?>

<?		if ($edost_catalogdelivery && $mode != 'manual') { ?>
		edost_catalogdelivery.resize(true);
<?		} ?>
	}
</script>

<? if (!empty($data['format']['data'])) { ?>
<?
	$active = (!empty($data['format']['active']['id']) ? true : false);
?>
<div id="edost_delivery_div" style="<?=($edost_catalogdelivery ? 'margin: 10px 0 0 0;' : '')?>" class="edost edost_main <?=(!$active ? 'edost_active_no' : '')?> <?=(!$edost_catalogdelivery ? ' edost_template_div' : '')?><?=$resize['cod']?><?=$resize['bookmark_cod']?><?=$resize['delimiter']?><?=$resize['bookmark']?><?=$resize['map']?>">

<?
	$border = (!empty($data['format']['border']) ? true : false);
	$cod = (!empty($data['format']['cod']) && $compact == '' && $priority != 'C' ? true : false);
	$cod_bookmark = (!empty($data['format']['cod_bookmark']) ? true : false);
	$top = ($border ? 15 : 40);

	$bookmark = (!empty($data['format']['bookmark']) ? $data['format']['bookmark'] : '');
	$bookmark_id = (!empty($data['format']['active']['bookmark']) ? $data['format']['active']['bookmark'] : '');

	$compact = (!empty($data['format']['compact']) ? $data['format']['compact'] : '');

	$hide_radio = false; //($data['format']['count'] == 1 && $compact == '' ? true : false);

	if ($arParams['FONT_BIG'] == 'Y') $ico_width = ($hide_radio || $edost_catalogdelivery ? '70' : '95');
	else $ico_width = ($hide_radio || $edost_catalogdelivery ? '40' : '64');

	$day_width = ($data['format']['day'] ? 80 : 10);
	$price_width = ($arParams['FONT_BIG'] == 'Y' && $compact == '' ? 95 : 85);
	$cod_width = ($arParams['COD_LIGHT'] == 'Y' && $compact == '' ? 95 : 90);

	$supercompact_format = (!empty($data['format']['supercompact_format']) ? $data['format']['supercompact_format'] : false);
	$head = GetMessage('SOA_TEMPL_DELIVERY');

	if ($cod_tariff) {
		$sign['price_head'] = '<span class="edost_payment_normal2">'.str_replace('<br>', ' ', $sign['price_head']).'</span>';
		$sign['cod_head'] = '<span class="edost_payment_cod2">'.str_replace('<br>', ' ', $sign['cod_head']).'</span>';
	}

	$head_button = ($compact != '' && $data['format']['count'] != 1 ? true : false);
?>

<? if (!$edost_catalogdelivery) { ?>
<?	if ($head_button) { ?>
	<div class="edost_window_hide edost_compact_hide">
		<div id="edost_get_delivery_button" class="edost_button_big <?=($active ? 'edost_button_head' : 'edost_button_big_red edost_button_big_active')?>" onclick="edost.window.set('<?=($supercompact_format !== false ? $supercompact_format : 'delivery')?>', 'head=<?=GetMessage('SOA_TEMPL_DELIVERY_HEAD')?>;class=<?=($supercompact_format !== false ? '' : 'edost_compact_main edost_compact_main2')?>')">
			<span><?=GetMessage($active ? 'SOA_TEMPL_CHANGE_BUTTON' : 'SOA_TEMPL_DELIVERY_SET')?></span>
		</div>
	</div>
<?	} ?>
	<h4 class="edost_compact_head<?=(!$active ? ' edost_supercompact_hide' : '')?>"><?=$head[0]?></h4>
	<h4 class="edost_compact_head2<?=(!$active ? ' edost_supercompact_hide' : '')?>"><?=$head[1]?></h4>
<? } ?>

	<div class="edost_div <?=($head_button && !$active ? 'edost_supercompact_hide' : '')?>">

<?	if ($bookmark != '') { ?>
	<div id="edost_bookmark_div" class="edost_compact_hide edost_supercompact_hide">
		<input id="edost_bookmark" name="edost_bookmark" value="<?=$bookmark_id?>" type="hidden">

		<div id="edost_bookmark_tariff2" class="edost_bookmark2 edost_format edost_resize_bookmark2" style="text-align: center;">
<?			$id = false;
			foreach ($data['format']['data'] as $f_key => $f) if ($bookmark !== 2 || $f_key !== 'general') { $id = $f_key; ?>
				<div id="edost_<?=$id?>_td2" class="edost_bookmark edost_active" width="110" style="" onclick="edost_SetBookmark('<?=$id?>')">
					<div class="edost_bookmark_head" style=""><?=$f['head']?></div>
<?				if ($f_key != 'show') { ?>
					<div style="padding-top: 2px;">
<?						if (isset($f['short']['free']) || isset($f['min']['free'])) { ?>
						<span class="edost_format_price edost_price_free" style=""><?=(isset($f['short']['free']) ? $f['short']['free'] : $f['min']['free'])?></span>
<?						} else if (isset($f['short']['price_formatted']) || isset($f['min']['price_formatted'])) { ?>
						<span class="edost_format_price edost_price"<?=(isset($f['short']['price_formatted']) ? ' style="color: #888;"' : '')?>><?=(isset($f['short']['price_formatted']) ? $f['short']['price_formatted'] : $f['min']['price_formatted'])?></span>
<?						} ?>
<?						if (!empty($f['min']['day'])) { ?>
						<br><span class="edost_format_price edost_day"><?=(!empty($f['min']['day']) ? $f['min']['day'] : '')?></span>
<?						} ?>

<?						if ($cod_bookmark && ($bookmark == 1 && $f['cod'] || $bookmark == 2 && (!$cod_tariff && isset($f['min']['pricecash']) || $f['min']['cod_tariff']))) { ?>
							<div class="edost_price_head edost_payment" style="padding-top: 2px;"><?=$sign['cod_head_bookmark']?></div>
<?						} ?>
					</div>
<?				} ?>
				</div>
<?			} ?>

<?			if ($bookmark == 1) { ?>
			<div class="edost_format edost_resize_bookmark2">
				<div style="height: 4px; background: #888; margin: 10px 0;"></div>
			</div>
<?			} ?>
		</div>

		<div id="edost_bookmark_tariff" class="edost_resize_bookmark">
		<table id="edost_bookmark_table" class="edost_bookmark" cellpadding="0" cellspacing="0" border="0">
			<tr>
<?			foreach ($data['format']['data'] as $f_key => $f) if ($bookmark !== 2 || $f_key !== 'general') { $id = $f_key; ?>
				<td id="edost_<?=$id?>_td" class="edost_active" width="110" style="padding-bottom: 5px;" onclick="edost_SetBookmark('<?=$id?>')">
					<img src="<?=$ico_path.'/'.$f_key.'.gif'?>" border="0">
					<br>
					<span class="edost_bookmark"><?=$f['head']?></span>
					<br>
<?				if ($f_key != 'show') { ?>
					<div>
<?						if (isset($f['free']) || isset($f['min']['free'])) { ?>
						<span class="edost_format_price edost_price_free" style=""><?=(isset($f['free']) ? $f['free'] : $f['min']['free'])?></span>
<?						} else if (isset($f['price_formatted']) || isset($f['min']['price_formatted'])) { ?>
						<span class="edost_format_price edost_price"<?=(isset($f['price_formatted']) ? ' style="color: #888;"' : '')?>><?=(isset($f['price_formatted']) ? $f['price_formatted'] : $f['min']['price_formatted'])?></span>
<?						} ?>

<?						if (!empty($f['min']['day'])) { ?>
						<br><span class="edost_format_price edost_day"><?=(!empty($f['min']['day']) ? $f['min']['day'] : '')?></span>
<?						} ?>

<?						if ($cod_bookmark && ($bookmark == 1 && $f['cod'] || $bookmark == 2 && (!$cod_tariff && isset($f['min']['pricecash']) || $f['min']['cod_tariff']))) { ?>
							<div class="edost_price_head edost_payment" style="padding-top: 4px;"><?=$sign['cod_head_bookmark']?></div>
<?						} ?>
					</div>
<?				} ?>
				</td>
				<td width="25"></td>
<?			} ?>
			</tr>
<?			if ($bookmark == 1) { ?>
			<tr>
<?				foreach ($data['format']['data'] as $f_key => $f) { $id = $f_key; ?>
				<td id="edost_<?=$id?>_td_bottom" style="height: 10px;"></td>
				<td></td>
<?				} ?>
			</tr>
			<tr>
				<td id="edost_bookmark_delimiter" colspan="10" style="height: 5px;"></td>
			</tr>
<?			} ?>
		</table>
		</div>


<?		if (!$edost_catalogdelivery) { ?>
		<div id="edost_bookmark_loading" style="padding-top: 0px; display: none;"></div>
<?		} ?>
<?		if ($bookmark_id == 'show') echo '<div style="height: 20px;"></div>'; ?>
	</div>
<?	} ?>

<?
	if ($bookmark == 2 && $bookmark_id != '' && $bookmark_id != 'show') foreach ($data['format']['data'] as $f_key => $f) if (!empty($f['tariff'])) foreach ($f['tariff'] as $v) if (!empty($v['checked'])) {
		$description = array();
		if (!empty($f['description'])) $description[] = $f['description'];
		if (!empty($v['description'])) $description[] = $v['description'];

		$warning = array();
		if (!empty($f['warning'])) $warning[] = $f['warning'];
		if (!empty($v['error'])) $warning[] = $v['error'];
		if (!empty($v['warning'])) $warning[] = $v['warning'];

		if (!empty($description) || !empty($warning) || !empty($v['office_address'])) {
			echo '<div id="edost_bookmark_info" class="edost_resize_bookmark" style="margin-top: 15px; padding: 12px 12px 0 12px; border-color: #DD8; border-style: solid; border-width: 1px 0; background: #FFD;">';
?>
<?			if (!empty($v['office_address'])) { ?>
				<div style="padding-bottom: 12px;">
					<span class="edost_format_address_head"><?=$sign['address2']?>: </span>
					<span class="edost_format_address"><?=$v['office_address']?></span>
<?					if (!isset($v['office_detailed']) || $v['office_detailed'] !== '') { ?>
					<a class="edost_link" href="<?=(!empty($v['office_detailed']) ? $v['office_detailed'] : 'http://edost.ru/office.php?c='.$v['office_id'])?>" target="_blank"><?=$sign['map']?></a>
<?					} ?>
				</div>
<?			} ?>
<?
			if (!empty($warning)) echo '<div class="edost_warning edost_format_info">'.implode('<br>', $warning).'</div>';
			if (!empty($description)) echo '<div class="edost_format_info">'.implode('<br>', $description).'</div>';
			echo '</div>';
		}
	}
?>

	<div id="edost_tariff_div">
<?
	$i = 0;
	$compact_i = 0;
	$compact_cod_i = 0;
	foreach ($data['format']['data'] as $f_key => $f) if (!empty($f['tariff'])) {
		$display = ($bookmark == 1 && $bookmark_id != $f_key || $bookmark == 2 && $bookmark_id != 'show' ? ' display: none;' : '');
		$margin = ' margin: '.($i != 0 && $bookmark != 1 && $compact == '' ? $top.'px' : '0').' 0 0 0;';
		$map = ($map_inside == 'Y' && $f_key == 'office' ? true : false);

		if ($cod) $cod_td = true;

		if ($bookmark == 1) $head = '';
		else $head = ($f['head'] != '' ? '<div class="edost_format_head edost_window_hide">'.$f['head'].'</div>' : '');

		if ($map) {
?>
	<div id="edost_<?=$f_key?>_map_div" class="edost_resize_map<?=(!$border || $f['head'] == '' ? ' edost_format' : ' edost_format_border')?>" style="100%; margin: <?=($i != 0 && $bookmark != 1 ? $top.'px' : '0')?> 0 0 0;<?=$display?>">
<?
		if ($head != '') {
			echo '<div>';
			echo $head.'<div style="padding: 8px 0 0 0;"></div>';
			echo '<div style="padding: 3px 0 0 0;"></div>';
			echo '</div>';
		}

		echo '<div id="edost_office_inside" class="edost_office_inside edost_resize_map" style="height: 450px;"></div>';
		echo '<div id="edost_office_detailed" class="edost_office_detailed edost_resize_map"><span class="edost_format_link_big" onclick="edost.office.set(\'all\');">'.$sign['detailed_office'].'</span></div>';
?>
	</div>
<?		} ?>


	<div id="edost_<?=$f_key?>_div" data-cod="<?=($cod && $f['cod'] ? 'Y' : 'N')?>" class="edost_compact_div <?=($map ? 'edost_resize_map2 ' : '')?><?=(!$border || $f['head'] == '' ? ' edost_format' : ' edost_format_border')?>" style="<?=$margin.$display?>">
<?
		$i++;

		if ($bookmark == 1) echo '<div class="edost_resize_bookmark" style="height: 8px;"></div>';

		if ($cod && $f['cod']) {
			echo '<div class="edost_compact_hide edost_supercompact_hide'.($head == '' ? ' edost_resize_cod' : '').'">';
			echo '<table class="edost_format_head" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>';
			echo '<td>'.($head != '' ? $head : '&nbsp;').'</td>';
			echo '<td class="edost_format_head edost_resize_cod" width="'.$price_width.'"><div class="edost_price_head edost_price_head_color">'.$sign['price_head'].'</div></td>';
			echo '<td class="edost_format_head edost_resize_cod" width="'.$cod_width.'"><div class="edost_price_head edost_payment">'.$sign['cod_head'].'</div></td>';
			if ($compact != '') echo '<td class="edost_supercompact_hide edost_order_hide" width="130"></td>';
			echo '</tr></table>';
			echo '<div style="padding: 8px 0 0 0;"></div>';
			echo '</div>';
		}
		else if ($head != '') {
			echo '<div class="edost_compact_hide edost_supercompact_hide">';
			echo $head.'<div style="padding: 8px 0 0 0;"></div>';
			echo '<div style="padding: 3px 0 0 0;"></div>';
			echo '</div>';
		}

		if ($f['warning'] != '') echo '<div class="edost_compact_hide edost_supercompact_hide edost_warning edost_format_info">'.$f['warning'].'</div>';
		if ($f['description'] != '') echo '<div class="edost_compact_hide edost_supercompact_hide edost_format_info">'.$f['description'].'</div>';
		if (!$no_insurance && $f['insurance'] != '') echo '<div class="edost_compact_hide edost_supercompact_hide edost_format_info"><span class="edost_insurance">'.$f['insurance'].'</span></div>';

		$i2 = 0;
		$i2_cod = 0;
		foreach ($f['tariff'] as $k => $v) {
			if (!empty($v['checked'])) $active_tariff = $v;

			if (isset($v['delimiter'])) {
				if ($compact == '') {
					echo '<div class="edost_compact_hide edost_supercompact_hide '.(!$edost_catalogdelivery ? 'edost_resize_delimiter ' : '').'edost_delimiter edost_delimiter_mb'.($edost_catalogdelivery ? '2' : '').'"></div>';
					if (!$edost_catalogdelivery) echo '<div class="edost_resize_delimiter2 edost_delimiter edost_delimiter_mb2"></div>';
					$i2 = 0;
				}
				continue;
			}

			$display = (!$map && isset($v['office_mode']) && ($map_inside == 'Y' || $map_inside == 'tariff' && empty($v['checked_inside'])) ? ' style="display: none;"' : '');

			$c = array();
			if ($compact != '') {
				$s = '';
				if (empty($v['compact']) && empty($v['compact_cod'])) $s = 'hide';
				else if (empty($v['compact']) && !empty($v['compact_cod'])) $s = 'nocod_hide';
				else if (!empty($v['compact']) && empty($v['compact_cod'])) $s = 'cod_hide';
				if (!empty($s)) $c[] = 'edost_compact_'.$s;
				if (empty($v['supercompact'])) $c[] = 'edost_supercompact_hide';
				if (!isset($v['pricecod']) || !empty($v['cod_hide'])) $c[] = 'edost_compact_tariff_cod_hide';
			}
			$tariff_class = (!empty($c) ? implode(' ', $c) : '');

			$c = array('edost_delimiter edost_delimiter_format');
			if ($compact != '') {
				if (!empty($tariff_class)) $c[] = $tariff_class;
				if (!empty($v['supercompact'])) $c[] = 'edost_supercompact_hide';
				if (!empty($v['compact']) && $compact_i == 0) $c[] = 'edost_compact_first';
				if (!empty($v['compact_cod']) && $compact_cod_i == 0) $c[] = 'edost_compact_cod_first';
				if (isset($v['pricecod']) && empty($v['cod_hide']) && $i2_cod == 0) $c[] = 'edost_compact_tariff_cod_first';
				if ($i2 == 0) $c[] = 'edost_compact_tariff_first';
			}
			if ($i2 != 0 || $compact != '') {
				echo '<div'.($map || $map_inside == '' || !isset($v['office_mode']) ? '' : ' id="edost_delimiter_'.$f_key.'" style="display: none;"').'>';
				echo '<div class="'.implode(' ', $c).'"></div>';
				echo '</div>';
			}
			$i2++;
			if (!empty($v['compact'])) $compact_i++;
			if (!empty($v['compact_cod'])) $compact_cod_i++;
			if (isset($v['pricecod']) && empty($v['cod_hide'])) $i2_cod++;

			$id = 'ID_DELIVERY_'.$v['html_id'] . ($compact != '' && in_array($v['html_id'], $office_main2) && $k != 0 ? '_2' : '');
			$value = $v['html_value'];
			$office_map = (isset($v['office_map']) ? $v['office_map'] : '');
//			$onclick = 'submitForm('.($office_map == 'get' && $v['format'] != 'post' ? "'office', '".$v['office_mode']."'" : "'update'").')';
			$onclick = 'submitForm('.($office_map == 'get' ? "'office', '".$v['office_mode']."'" : "'update'").')';
//			$onclick_get = "window.edost.window.submit('".$id."'".($office_map == 'get' && $v['format'] != 'post' ? ", '".$v['office_mode']."'" : '').")";
			$onclick_get = "window.edost.window.submit('".$id."'".($office_map == 'get' ? ", '".$v['office_mode']."'" : '').")";
			$price_long = (isset($v['price_long']) ? $v['price_long'] : '');
			$codplus = (($compact == '' || $priority == 'B') && isset($v['pricecash']) && !$cod_tariff ? true : false);
			$office_detailed = (!empty($v['office_detailed']) ? '<div class="edost_button_detailed" onclick="edost.office.info(0, \''.$v['office_detailed'].'\')">'.$v['office_link'].'</div>' : false);
			$insurance = (!$no_insurance && $v['insurance'] != '' && (!$cod_tariff || empty($v['cod_tariff'])) ? true : false);
			$button_red = ($office_map == 'get' && $v['format'] != 'post' ? 'edost_button_big_red' : '');

			if (isset($v['company_ico']) && $v['company_ico'] !== '') $ico = $ico_path.'/company/'.$v['company_ico'].'.gif';
			else if (isset($v['ico']) && $v['ico'] !== '') $ico = (strlen($v['ico']) <= 3 ? $ico_path.'/'.$v['ico'].'.gif' : $v['ico']);
			else $ico = (!empty($ico_default) ? $ico_default : false);

			if (isset($v['office_mode']) && $office_map == 'get' && !empty($sign['office_description'][$v['office_mode']]) && $compact == '') $v['description'] = $sign['office_description'][$v['office_mode']];
//			if (isset($v['office_mode'])) echo '<div id="edost_address_'.$v['office_mode'].'_loading"></div>';

			$row = $resize['ico_row'];
			if ($row == 'auto') {
				$row = 1;
				if (isset($v['company_head'])) $row++;
				if (!empty($v['office_address'])) $row++;
			}

			if (isset($v['free'])) {
			    $w = explode('_', $v['html_value']);
				if (!empty($v['description']) && strpos($v['description'], '[no_free]') !== false) {
					if (!empty($v['description'])) $v['description'] = str_replace('[no_free]', '', $v['description']);
					$v['free'] = '';
					if (!empty($v['checked'])) $active_tariff['free'] = '';
				}
			}
			if (!empty($v['description']) && strpos($v['description'], '[address=') !== false) {
				$s = explode('[address=', $v['description']);
				$v['description'] = $s[0];
			}
			if (!empty($v['description'])) $v['description'] = str_replace('[passport]', '', $v['description']);
?>

		<div class="edost_format_tariff_main <?=$tariff_class?> <?=(!empty($v['checked']) ? ' edost_main_active edost_main_fon' : '')?>">
		<table class="edost_format_tariff" <?=($office_map != '' && isset($v['office_mode']) ? 'id="edost_address_'.$v['office_mode'].'"' : '')?> width="100%" cellpadding="0" cellspacing="0" border="0"<?=$display?>>
			<tr>
				<td class="edost_resize_show edost_resize_ico<?=$resize['ico'][0]?>" width="<?=($ico_width - $resize['ico'][1])?>" data-width="<?=$ico_width?>" rowspan="<?=$row?>">
<?					if (!$edost_catalogdelivery) { ?>
					<input class="edost_format_radio edost_supercompact_hide" <?=($hide_radio ? 'style="display: none;"' : '')?> type="radio" id="<?=$id?>" name="DELIVERY_ID" value="<?=$value?>" <?=(!empty($v['checked']) ? 'checked="checked"' : '')?> onclick="<?=$onclick?>">
<?					} ?>

<?					if ($ico !== false) { ?>
					<label class="edost_format_radio" for="<?=$id?>"><img class="edost_ico" src="<?=$ico?>" border="0"></label>
<?					} else { ?>
					<div class="edost_ico"></div>
<?					} ?>

<?					if ($compact != '') { ?>
					<label for="<?=$id?>">
					<span class="edost_resize_show edost_format_tariff" style="text-align: left; display: none;"><?=(isset($v['head']) ? $v['head'] : $v['company'])?>
<?					if (!empty($v['automatic']) && $v['automatic'] != 'edost') { ?>
					<span class="edost_format_name"><br><?=$v['name']?></span>
<?					} ?>
<?					if ($insurance) { ?>
					<span class="edost_insurance edost_compact_cod_hide edost_compact_tariff_cod_hide"><br><?=$v['insurance']?></span>
<?					} ?>
					</span>

					<? /* компактный вывод адреса пункта выдачи */ ?>
<?					if (!empty($v['office_address'])) { ?>
					<br>
					<div class="edost_resize_show edost_format_tariff" style="display: none; padding-bottom: 5px;">
						<span class="edost_format_tariff2"><?=$v['office_address']?></span>
<?						if ($office_map == '' && $office_detailed) echo $office_detailed; ?>
					</div>
<?					} ?>

					</label>
<?					} ?>
				</td>

				<td class="edost_format_tariff edost_resize_tariff_show">
					<label for="<?=$id?>">

<?					if ($compact != '') { ?>
					<img class="edost_ico edost_ico2" src="<?=$ico?>" border="0">
<?					} ?>

					<span class="edost_format_tariff edost_window_hide"><?=(isset($v['head']) ? $v['head'] : $v['company'])?></span>

<?					if ($compact != '') { ?>
					<span class="edost_format_tariff edost_compact_hide edost_supercompact_hide"><?=$v['company']?></span>
<?					} ?>

<?					if ($v['name'] != '') { ?>
<?						if (!isset($v['company_head']) && $compact == '') { ?>
						<span class="edost_format_name edost_resize_day">(<?=$v['name']?>)</span>
<?						} else if (!empty($v['automatic']) && $v['automatic'] != 'edost') { ?>
						<span class="edost_format_name"><br><?=$v['name']?></span>
<?						} ?>
<?						if (!isset($v['company_head'])) { ?>
						<span class="edost_format_name edost_compact_hide edost_supercompact_hide <?=($compact == '' ? 'edost_resize_day2' : '')?>"><br><?=$v['name']?></span>
<?						} ?>
<?					} ?>

<?					if ($insurance) { ?>
					<span class="edost_insurance edost_compact_cod_hide edost_compact_tariff_cod_hide"><br><?=$v['insurance']?></span>
<?					} ?>

<?					if ($cod_tariff && $office_map == 'get' && isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
					<span class="edost_payment_cod2"><?=$cod_head_bookmark2?></span>
<?					} ?>

<?					if ($cod_tariff && $v['automatic'] == 'edost' && $v['profile'] != 0 && ($office_map == '' || !empty($v['office_address']))) { ?>
						<br><?=(empty($v['cod_tariff']) ? $sign['price_head'] : $sign['cod_head'])?>
<?					} ?>
					</label>

<?					if (isset($v['company_head']) && !empty($v['company']) && $compact == '') { ?>
					<div class="edost_resize_description2 edost_window_hide" style="<?=($cod_tariff ? ' padding-top: 2px;"' : '')?>">
						<span class="edost_format_company_head edost_compact_hide edost_supercompact_hide"><?=$v['company_head']?>: </span>
						<span class="edost_format_company"><?=$v['company']?></span>
						<?=($v['name'] != '' ? '<span class="edost_format_company_name"> ('.$v['name'].')</span>' : '')?>
					</div>
<?					} ?>

					<? /* компактный вывод адреса пункта выдачи */ ?>
<?					if (!empty($v['office_address']) && $compact != '') { ?>
					<div class="edost_window_hide" style="<?=($cod_tariff && $office_map != 'get' ? ' padding-top: 2px;' : '')?>">
						<span class="edost_format_tariff2"><?=$v['office_address']?></span>
<?						if ($office_map == '' && $office_detailed) echo $office_detailed; ?>
					</div>
<?					} ?>

					<? /* компактная кнопка "другой тариф..." */ ?>
<?					if (!empty($v['compact_link']) && ($office_map != '' || !in_array($f_key, $office_main))) { ?>
					<div class="edost_change_button edost_window_hide edost_supercompact_hide edost_order_hide edost_compact_cod_hide edost_compact_tariff_cod_hide edost_button_big2 <?=$button_red?>" style="width: auto; max-width: 200px; margin: 5px 0 0 0;" onclick="<?=(in_array($office_map, array('get', 'change')) ? "edost.office.set('".$v['office_mode']."', false)" : "edost.window.set('".$f_key."', 'head=".$v['compact_head']."')")?>">
						<span><?=$v['compact_link']?></span>
					</div>
<?					} ?>
<?					if (!empty($v['compact_link_cod']) && ($office_map != '' || !in_array($f_key, $office_main))) { ?>
					<div class="edost_change_button edost_window_hide edost_supercompact_hide edost_order_hide edost_compact_nocod_hide edost_compact_tariff_nocod_hide edost_button_big2 <?=$button_red?>" style="width: auto; max-width: <?=($v['format'] == 'postmap' ? 230 : '200')?>px; margin: 5px 0 0 0;" onclick="<?=(in_array($office_map, array('get', 'change')) ? "edost.office.set('".$v['office_mode']."', false)" : "edost.window.set('".$f_key."', 'head=".$v['compact_head_cod']."')")?>">
						<span><?=$v['compact_link_cod']?></span>
					</div>
<?					} ?>
<?					if (!empty($v['compact_link']) && $office_map == 'get') { ?>
					<div class="edost_change_button edost_window_hide edost_compact_hide edost_button_big2 <?=$button_red?>" style="<?=($office_map != 'get' ? 'width: auto; max-width: 200px;' : '')?> margin: 5px 0 0 0;" onclick="<?=(in_array($office_map, array('get', 'change')) ? "edost.office.set('".$v['office_mode']."', false)" : "edost.window.set('".$f_key."', 'head=".$v['compact_head']."')")?>">
						<span><?=$v['compact_link']?></span>
					</div>
<?					} ?>

<?					if (!empty($v['office_address'])) { ?>
					<div class="<?=($compact != '' ? 'edost_resize_description2' : 'edost_resize_address')?> edost_compact_hide edost_supercompact_hide" style="<?=($cod_tariff && $office_map != 'get' ? ' padding-top: 2px;' : '')?>">
						<span class="edost_format_address_head"><?=$sign['address']?>: </span>
						<span class="edost_format_address"><?=$v['office_address']?></span>

<?						if ($office_map == 'change') { ?>
						<br><span class="edost_format_link" onclick="edost.office.set('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?						} else if ($office_detailed) echo $office_detailed; ?>
					</div>
<?					} ?>

<?					if ($office_map == 'get' && $compact == '') { ?>
					<br><span class="edost_format_link_big<?=(!empty($v['office_link2']) ? ' edost_resize_day' : '')?> edost_compact_hide edost_supercompact_hide" onclick="edost.office.set('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?					if (!empty($v['office_link2'])) { ?>
					<span class="edost_format_link_big edost_resize_day2 edost_compact_hide edost_supercompact_hide" onclick="edost.office.set('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link2']?></span>
<?					} ?>
<?					} ?>
				</td>

<?				if ($price_long === '' && !isset($v['error'])) { ?>
				<td class="edost_format_price edost_resize_day edost_compact_hide edost_supercompact_hide edost_window_hide" width="<?=$day_width?>" align="center">
					<label for="<?=$id?>"><span class="edost_format_price edost_day"><?=(!empty($v['day']) ? $v['day'] : '')?></span></label>
				</td>
<?				} ?>

<?				if ($compact == '') $s = 'width="'.($price_width + ($price_long !== '' ? 30 : 0)).'" align="right"';
				else $s = 'width="105" style="text-align: center; vertical-align: middle;"'; ?>
				<td class="edost_format_price edost_resize_show edost_resize_tariff_show2 edost_resize_price" <?=$s?>>
<?					if (!in_array($v['html_id'], $tariff_price_hide)) { ?>
						<label for="<?=$id?>" class="edost_compact_cod_hide edost_compact_tariff_cod_hide">
<?						if ($compact != '' && $office_map == 'get' && empty($v['office_address'])) { ?>
<?							if (isset($f['short']['free']) || isset($f['min']['free'])) { ?>
							<span class="edost_format_price edost_price_free" style=""><?=(isset($f['short']['free']) ? $f['short']['free'] : $f['min']['free'])?></span>
<?							} else if (isset($f['short']['price_formatted']) || isset($f['min']['price_formatted'])) { ?>
							<span class="edost_format_price edost_price"<?=(isset($f['short']['price_formatted']) ? ' style="color: #888;"' : '')?>><?=(isset($f['short']['price_formatted']) ? $f['short']['price_formatted'] : $f['min']['price_formatted'])?></span>
<?							} ?>
<?						} else { ?>
<?							if (isset($v['free'])) { ?>
							<span class="edost_format_price edost_price_free" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=$v['free']?></span>
<?							} else { ?>
							<span class="edost_format_price edost_price" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"> <?=(isset($v['priceinfo_formatted']) ? $v['pricetotal_formatted'] : $v['price_formatted'])?></span>
<?							} ?>

<?							if (isset($v['pricetotal_original'])) { ?>
							<span class="edost_format_price edost_price_original"><?=$v['pricetotal_original_formatted']?></span>
<?							} ?>
<?						} ?>
						</label>

<?						if (isset($v['pricecod']) && $v['pricecod'] >= 0 && $compact != '' && $priority != 'C') { ?>
						<label for="<?=$id?>" class="edost_compact_nocod_hide edost_compact_tariff_nocod_hide edost_supercompact_hide" style="margin: 0;">
<?							if (isset($v['cod_free'])) { ?>
							<span class="edost_format_price edost_price_free" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=$v['cod_free']?></span>
<?							} else { ?>
							<span class="edost_format_price edost_price" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"> <?=$v['pricecod_formatted']?></span>
<?							} ?>

<?							if (isset($v['pricecod_original']) && !isset($v['priceinfo_formatted'])) { ?>
							<div><span class="edost_format_price edost_price_original"><?=$v['pricecod_original_formatted']?></span></div>
<?							} ?>
						</label>
<?						} ?>
<?					} ?>

<?					if (!empty($v['day'])) { ?>
					<div class="<?=($compact == '' ? 'edost_resize_day2' : '')?>"><label for="<?=$id?>" class="" style="margin: 0;"><span class="edost_format_price edost_day"><?=$v['day']?></span></label></div>
<?					} ?>
				</td>
                                                                                         <? /* edost_compact_cod_hide */ ?>
				<td class="edost_resize_button edost_window_hide edost_supercompact_hide edost_compact_window_hide <?=($office_map == 'get' ? 'edost_office_get' : '')?>" align="right">
<?				if (!empty($v['compact_link'])) { ?>
					<div class="edost_button_big2 <?=(!empty($v['checked']) ? $button_red : '')?>" onclick="<?=(in_array($office_map, array('get', 'change')) ? "edost.office.set('".$v['office_mode']."')" : "edost.window.set('".$f_key."', 'head=".$v['compact_head']."')")?>">
						<span class="edost_window_hide"><?=$v['compact_link']?></span>
					</div>
<?				} ?>
				</td>

<?				if ($cod_td && $compact == '' && !isset($v['error'])) { ?>
				<td class="edost_format_price edost_resize_cod edost_compact_hide edost_supercompact_hide" width="<?=$cod_width?>" align="right">
<?					if (isset($v['pricecod']) && $v['pricecod'] >= 0) { ?>
					<label for="<?=$id?>"><span class="edost_price_head edost_payment"><?=(isset($v['cod_free']) ? $v['cod_free'] : $v['pricecod_formatted'])?></span></label>
<?					} ?>

<?					if (isset($v['pricecod_original'])) { ?>
					<br><span class="edost_format_price edost_price_original"><?=$v['pricecod_original_formatted']?></span>
<?					} ?>
				</td>
<?				} ?>

<?				if ($compact != '') { ?>
				<td class="edost_resize_tariff_show2 edost_supercompact_hide edost_order_hide edost_button_get" width="110" align="center"> <? /* edost_compact_nocod_hide */ ?>
					<div class="edost_button_get" onclick="<?=$onclick_get?>"><span><?=GetMessage('SOA_TEMPL_GET')?></span></div>
				</td>
<?				} ?>
			</tr>

<?			if (isset($v['company_head']) && !empty($v['company']) && $compact == '') { ?>
			<tr class="edost_resize_description edost_window_hide edost_compact_hide edost_supercompact_hide">
				<td colspan="5" class="edost_description" style="padding-top: 2px;">
					<span class="edost_format_company_head"><?=$v['company_head']?>: </span>
					<span class="edost_format_company"><?=$v['company']?></span>
					<?=($v['name'] != '' ? '<span class="edost_format_company_name"> ('.$v['name'].')</span>' : '')?>
				</td>
			</tr>
<?			} ?>

<?			if (!empty($v['office_address'])) { ?>
			<tr class="<?=($compact != '' ? 'edost_resize_description' : 'edost_resize_address2')?> edost_compact_hide edost_supercompact_hide">
				<td colspan="5"<?=($cod_tariff && $office_map != 'get' ? ' style="padding-top: 2px;"' : '')?>>
					<span class="edost_format_address_head"><?=$sign['address']?>: </span>
					<span class="edost_format_address"><?=$v['office_address']?></span>

<?					if ($office_map == 'change') { ?>
					<br><span class="edost_format_link<?=(empty($v['supercompact']) ? ' edost_compact_hide edost_supercompact_hide' : '')?>" onclick="edost.office.set('<?=($map_inside ? 'all' : $v['office_mode'])?>');"><?=$v['office_link']?></span>
<?					} else if ($office_detailed) echo str_replace('class="edost_button_detailed"', 'class="edost_button_detailed edost_compact_hide edost_supercompact_hide"', $office_detailed); ?>
				</td>
			</tr>
<?			} ?>

<?			if ($compact == '') { $s = 'width="'.($price_width + ($price_long !== '' ? 30 : 0)).'" align="right"'; ?>
			<tr>
				<td colspan="5" class="edost_format_price edost_resize_price2" <?=$s?>>
<?					if (!empty($v['day'])) { ?>
					<div><label for="<?=$id?>" style="margin: 0; vertical-align: middle;"><span class="edost_format_price edost_day"><?=$v['day']?></span></label></div>
<?					} ?>
					<div style="vertical-align: middle;">
<?					if (!in_array($v['html_id'], $tariff_price_hide)) { ?>
						<label for="<?=$id?>" class="edost_compact_cod_hide edost_compact_tariff_cod_hide">
<?							if (isset($v['free'])) { ?>
							<span class="edost_format_price edost_price_free" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"><?=$v['free']?></span>
<?							} else { ?>
							<span class="edost_format_price edost_price" style="<?=($price_long == 'light' ? 'opacity: 0.5;' : '')?>"> <?=(isset($v['priceinfo_formatted']) ? $v['pricetotal_formatted'] : $v['price_formatted'])?></span>
<?							} ?>

<?							if (isset($v['pricetotal_original'])) { ?>
							<br><span class="edost_format_price edost_price_original"><?=$v['pricetotal_original_formatted']?></span>
<?							} ?>
						</label>
<?					} ?>
                    </div>
				</td>
			</tr>
<?			} ?>

<?			if (!empty($v['description']) || !empty($v['warning']) || !empty($v['error']) || !empty($v['note']) || $codplus) { ?>
			<tr name="edost_description">
				<td colspan="6" class="edost_resize_show edost_resize_tariff_show edost_description"<?=($hide_radio ? ' style="padding: 0;"' : '')?>>
<?					if (!empty($v['error'])) { ?>
					<div class="edost_format_description edost_warning"><b><?=$v['error']?></b></div>
<?					} ?>

<?					if (!empty($v['warning'])) { ?>
					<div class="edost_format_description edost_warning"><?=$v['warning']?></div>
<?					} ?>

<?					if ($codplus) { ?>
					<div class="edost_payment edost_compact_hide edost_supercompact_hide edost_compact_tariff_cod_hide edost_resize_cod2 edost_format_description"><?=GetMessage('SOA_TEMPL_COD_TARIFF').($office_map != 'get' && !empty($v['codplus_formatted']) ? ' <span class="edost_bracket">(</span><span class="edost_codplus">+ '.$v['codplus_formatted'].'</span><span class="edost_bracket">)</span>' : '')?></div>
<?					} ?>

<?					if (!empty($v['note'])) { ?>
						<div class="edost_format_description edost_window_hide edost_note_active"><?=$v['note']?></div>
<?					} ?>

<?					if (!empty($v['description'])) { ?>
					<div class="edost_format_description edost_description"><?=nl2br($v['description'])?></div>
<?					} ?>
				</td>
			</tr>
<?			} ?>


<?			if (!empty($v['compact_link'])) { ?>
			<tr class="edost_resize_button2">
				<td colspan="5" class="edost_resize_show edost_button edost_window_hide edost_supercompact_hide edost_compact_window_hide" align="right" style="padding-top: 5px;">
					<? /* компактный вывод адреса пункта выдачи */ ?>
<?					if (!empty($v['office_address']) && $compact != '') { ?>
					<div class="edost_format_tariff2 edost_supercompact_hide edost_window_hide">
						<span class="edost_format_tariff2"><?=$v['office_address']?></span>
					</div>
<?					} ?>

					<div class="edost_supercompact_hide edost_change_button edost_button_big2 <?=(!empty($v['checked']) ? $button_red : '')?>" onclick="<?=(in_array($office_map, array('get', 'change')) ? "edost.office.set('".$v['office_mode']."')" : "edost.window.set('".$f_key."', 'head=".$v['compact_head']."')")?>">
						<span class="edost_window_hide"><?=$v['compact_link']?></span>
					</div>
				</td>
			</tr>
<?			} ?>

		</table>
		</div>
<?		} ?>
	</div>
<?	} ?>
	</div>


<?	if (!$edost_catalogdelivery && isset($arResult['BUYER_STORE'])) { ?>
	<input name="BUYER_STORE" id="BUYER_STORE" value="<?=$arResult['BUYER_STORE']?>" type="hidden">
<?	} ?>

<?	if (!empty($data['format']['active']['id'])) { ?>
	<input id="edost_delivery_id" value="<?=$data['format']['active']['id']?>" type="hidden">
<?	} ?>

<?	if (!empty($_REQUEST['edost_office_data_parsed']) && empty($data['format']['map_update'])) { ?>
	<input id="edost_office_data" autocomplete="off" value='parsed' type="hidden">
<?	} else if (!empty($data['format']['map_json'])) { ?>
	<input id="edost_office_data" autocomplete="off" value='{"ico_path": "<?=$ico_path?>", "yandex_api_key": "<?=(!empty($data['yandex_api_key']) ? $data['yandex_api_key'] : '')?>", <?=$data['format']['map_json']?>}' type="hidden">
<?	} ?>
	<input id="edost_office_data_parsed" name="edost_office_data_parsed" autocomplete="off" value="" type="hidden">

<?	if ($edost_catalogdelivery && $map_inside != '') { ?>
	<script type="text/javascript">
		if (window.edost && edost.office) edost.office.map = false;
		if (window.edost && edost.office2) edost.office2.map = false;
<?		if ($map_inside == 'Y' && $bookmark == '') { ?>
		edost_RunScript('map_inside');
<?		} ?>
	</script>
<?	} ?>

<? if (isset($ico_loading_map_inside)) { ?>
	<script type="text/javascript">
		if (window.edost && edost.office2) edost.office2.loading_inside = '<?=$ico_loading_map_inside?>';
	</script>
<? } ?>

<? if ($bookmark != '') { ?>
	<script type="text/javascript">
		edost_SetBookmark('start', '<?=$bookmark?>');
	</script>
<? } ?>

	</div>
</div>
<? } ?>

<? if (!empty($data['format']['address_tariff_disable'][43])) echo '<div class="edost edost_main edost_order_error">'.GetMessage('SOA_TEMPL_ADDRESS_TARIFF_DISABLE_43').'</div>'; ?>

<? } ?>
