<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?

// параметры модуля местоположений (edost.locations)
if (!empty($arResult['edost']['locations_installed'])) {
	$param['edost_locations'] = array(
		'ID' => $delivery_location,
		'DELIVERY_ID' => $delivery_id,
		'PROP' => $arResult['edost']['order_prop'],
		'PROP2' => $arResult['edost']['order_prop2'],

		'PARAM' => array(
//			'edost_delivery' => false, // false - отключить использование модуля edost.delivery (перейти на выбор местоположений через выпадающие списки), только для быстрой проверки - для постоянного использования требуется включение блокировки в константах !!!
			'input' => (!empty($arResult['edost']['location_input']) ? $arResult['edost']['location_input'] : 0), // ID местоположения с которого включится режим выбора (если не задано или равно "0", тогда модуль работает в автоматическом режиме)
			'zip_in_city' => (isset($arResult['edost']['order_prop']['ZIP']) && ($arResult['edost']['order_prop']['ZIP']['value'] === '' || $warning && $address_hide) ? true : false), // true - выводить индекс в блоке с местоположением
			'address' => 'N', // присвоить собственный адрес самовывоза ('N' - стандартная работа)
			'preload_prop' => ($arParams['USE_PRELOAD_PROP'] == 'Y' ? true : false),
//			'loading' => 'loading_small_f2.gif', // иконка загрузки при расчете доставки и проверке индекса - лежит в папке bitrix/components/edost/locations/images/

/*
			// предупреждения (если указаны, тогда заменяют значения по умолчанию)
			'zip_warning' => array(
				1 => 'Такого индекса НЕ существует!',
				2 => 'Индекс НЕ соответствует региону!',
				'digit' => 'Должны быть только цифры!',
			),
*/
			// модификация полей адреса (если указаны, тогда заменяют значения по умолчанию)
			'address_field' => array(
/*
				'street' => array(
					'width' => 320, // длина поля в пикселях
				),
				'house_1' => array(
					'enter' => true, // true - добавить перед полем 'ввод'
				),
*/
/*
				'city2' => array(
					'name' => 'Населенный пункт', // название поля
					'width' => 200, // длина поля в пикселях
					'max' => 50, // допустимое количество символов
					'enter' => true, // true - добавить перед полем 'ввод'
					'style' => 'color: #F00; width: 200px;', // полностью заменяет все стили (и дргие параметры)
				),
				'street' => array(
					'delimiter' => true, // установить после поля разделитель
					'delimiter_style' => 'width: 20px', // собственный стиль для разделителя
				),
//				'house_1', 'house_2', 'house_3', 'house_4', 'door_1', 'door_2' // остальные поля
*/
				'city2' => array(
					'delimiter' => false, // установить после поля разделитель
//					'enter' => false, // true - добавить перед полем 'ввод'
				),
				'street' => array(
					'enter' => false, // true - добавить перед полем 'ввод'
					'delimiter_style' => 'width: 0;', // собственный стиль для разделителя
				),
				'house_3' => array(
					'hidden' => true,
				),
				'house_4' => array(
					'hidden' => true,
				),
				'house_2' => array(
//					'delimiter' => false, // установить после поля разделитель
					'delimiter_style' => 'width: 0px', // собственный стиль для разделителя
				),
				'door_1' => array(
//					'delimiter_style' => 'width: 0px', // собственный стиль для разделителя
				),
				'door_2' => array(
					'hidden' => true,
//					'delimiter' => false, // установить после поля разделитель
//					'delimiter_style' => 'width: 0;', // собственный стиль для разделителя
				),

				'zip' => array(
					'enter' => false,
				),

				'passport_2' => array(
					'width' => 80, // длина поля в пикселях
				),
				'passport_3' => array(
					'width' => 180, // длина поля в пикселях
					'disable' => ($arParams['PASSPORT_SMALL'] == 'Y' ? true : false),
				),
				'passport_4' => array(
					'width' => 80, // длина поля в пикселях
					'disable' => ($arParams['PASSPORT_SMALL'] == 'Y' ? true : false),
				),

				// ограничения по выводу полей
				'city2_required' => array(
//					'value' => 'Y', // '' - не обязательно, 'Y' - обязательно (по умолчанию)
				),
				'street_required' => array(
					'value' => (empty($arParams['LOCATION_AREA']) || $arParams['LOCATION_AREA'] == 'Y' ? 'A' : ''), // '' - не обязательно, 'Y' - обязательно, 'A' - работа с модулем edost.delivery с обязательным выбором улицы из списка подсказок для городов с отдаленными районами (по умолчанию)
				),
				'zip_required' => array(
//					'value' => (in_array($delivery_id, array(10, 20)) ? 'Y' : ''), // '' - поле не выводится, 'S' - поле выводится, 'Y' - должно быть обязательно заполнено, 'A' - работа с модулем edost.delivery (по умолчанию)
				),
				'metro_required' => array(
//					'value' => (in_array($delivery_id, array(10, 20)) ? 'S' : ''), // '' - поле не выводится, 'S' - поле выводится, 'A' - работа с модулем edost.delivery (по умолчанию)
				),
				'passport_required' => array(
//					'value' => (in_array($delivery_id, array(10, 20)) ? 'Y' : ''), // '' - поле не выводится, 'Y' - должно быть обязательно заполнено, 'A' - работа с модулем edost.delivery (по умолчанию)
				),
			),
		),
	);

?>
	<script type="text/javascript">
		function edost_SetTemplateLocation(param, data) {

			if (param === 'error') {
				loadingForm(false);
				edost.H('edost_location_city_template_div', '<div class="edost_delivery_loading" style="color: #F00;">' + data + '</div>');
			}
			else if (param === 'back') {
				edost.H('edost_location_city_template_div', '');
				edost.C('edost_location_div', ['edost_location_button_hide'], 1);
				edost.D('edost_location_city_div', true);
				edost.D('edost_location_address_div', true);
			}
			else if (param === 'set') {
				loadingForm();
				edost.H('edost_location_city_template_div', '<div class="edost_delivery_loading"><?=GetMessage('SOA_TEMPL_LOADING')?></div>');
				edost.D('edost_location_city_div', false);
			}
			else if (param === 'click') {
				var E = edost.E('edost_location_address_div');
				if (!E) {
					alert('<?=GetMessage('SOA_TEMPL_ADDRESS_ERROR')?>');
					return;
				}
				s = edost.D(E);
				edost.W('.edost_city_link, .edost_L2_city_link', function(v) { v.click(); } );
				edost.D(E, s);
			}
			else if (param === 'new') {
				edost.location.set('0', true);
			}
			else if (param === 'loading') {
				edost.C('edost_location_div', ['edost_location_button_hide'], 0);

				var E = edost.E('edost_location_city_template_div');
				var E2 = edost.E('edost_location_city_div');
				if (E && E2) {
					E.innerHTML = E2.innerHTML;
					for (var i = 0; i < E.children.length; i++) if (!edost.A(E.children[i].className, ['edost_flag', 'edost_city_name', 'edost_L2_city_name'])) { E.removeChild(E.children[i]); i--; }
					edost.D(E, true);
					edost.D(E2, false);
				}
			}

		}
	</script>
<?
}


if (!function_exists('DrawInput')) {
	function DrawInput($v, $fast = false, $submit = false, $change = false) {
		$type = $inputmode = '';
		$a = ($fast || isset($v['REQUIED_FORMATED']) && $v['REQUIED_FORMATED'] === 'Y' ? true : false);
		if ($a) {
			if ($v['IS_PROFILE_NAME'] == 'Y' || $v['CODE'] == 'FIO' || $v['CODE'] == 'COMPANY') $type = ($v['CODE'] == 'COMPANY' ? 'organization' : 'name');
			else if ($v['IS_EMAIL'] == 'Y' || $v['CODE'] == 'EMAIL') $type = 'email';
			else if ($v['IS_PHONE'] == 'Y' || $v['CODE'] == 'PHONE') $type = 'tel';

			if ($type == '') $type = 'field';
		}

		if ($submit) $change = 'submitForm()';
		else if ($change) $change = 'submitProp(this)';
		$onchange = ($change ? 'onchange="'.$change.'"' : '');

		$onfocus = $img = '';
		if ($fast) $onfocus = 'onfocus="window.edost.input(\'focus\', this)" onblur="window.edost.input(\'blur\', this)"';
?>
		<div class="edost_prop_error"></div>
		<input type="<?=($type == 'tel' ? $type : 'text')?>" <?=(!empty($v['inputmode']) ? 'inputmode="'.$v['inputmode'].'"' : '')?> <?=(in_array($type, array('email', 'tel', 'name', 'organization')) ? 'autocomplete="'.$type.'"' : '')?> autocorrect="off" spellcheck="false" <?=($type == 'email' ? 'autocapitalize="off"' : '')?> maxlength="250" size="<?=$v['SIZE1']?>" value="<?=$v['VALUE']?>" <?=($fast ? 'data-name="'.$v['FIELD_NAME'].'"' : 'name="'.$v['FIELD_NAME'].'" id="'.$v['FIELD_NAME'].'"')?> data-type="<?=$type?>" oninput="window.edost.input('update', this, event)" <?=$onchange?> <?=$onfocus?>>
<?
	}
}

if (!function_exists('DrawPropHead')) {
	function DrawPropHead($mono, $head = '', $required = false, $hide = false, $id = '', $top = false, $class = '') {
		if ($mono) return;
		if (!empty($head)) {
			$required = ($required === true || $required === 'Y' ? true : false);
			echo '<div class="edost_prop_div'.($class ? ' '.$class : '').'"'.($id != '' ? ' id="'.$id.'"' : '').($hide ? ' style="display: none;"' : '').'>
			<div class="edost_prop_head"'.($top !== false ? ' style="vertical-align: top; padding-top: '.$top.'px;"' : '').'>'.$head.($required ? '<span>*</span>' : '').'</div>
			<div class="edost_prop">';
		}
		else echo '</div></div>';
	}
}

if (!function_exists('PrintPropsForm')) {
	function PrintPropsForm($head, $field, $arResult = false, $arParams = false, $param) {

		$arSource = array();

		$head_compact = '';
		if (is_array($head)) {
			$head_compact = $head[1];
			$head = $head[0];
		}

		$field = array_fill_keys($field, false);
		$count = 0;
		foreach ($field as $f_key => $f) {
			if ($f_key == 'profile') {
			}
			if ($f_key == 'person_type') {
			}
			if ($f_key == 'props_Y') {
				if (empty($arResult['ORDER_PROP']['USER_PROPS_Y'])) unset($field[$f_key]);
				else {
					$field[$f_key] = $arResult['ORDER_PROP']['USER_PROPS_Y'];
					$count += count($field[$f_key]);
				}
			}
			if ($f_key == 'props_N') {
				if (empty($arResult['ORDER_PROP']['USER_PROPS_N'])) unset($field[$f_key]);
				else {
					$field[$f_key] = $arResult['ORDER_PROP']['USER_PROPS_N'];
					$count += count($field[$f_key]);
				}
			}
			if ($f_key == 'props_related') {
				if (empty($arResult['ORDER_PROP']['RELATED'])) unset($field[$f_key]);
				else {
					$field[$f_key] = $arResult['ORDER_PROP']['RELATED'];
					$count += count($field[$f_key]);
				}
			}
			if ($f_key == 'location') {
				if (!$param['edost_locations']) unset($field[$f_key]);
			}
			if ($f_key == 'address') {
				if (!$param['edost_locations'] || !isset($arResult['edost']['order_prop']['ADDRESS'])) unset($field[$f_key]);
			}
			if ($f_key == 'passport') {
				if (!$param['edost_locations'] || !isset($arResult['edost']['order_prop']['PASSPORT'])) unset($field[$f_key]);
			}
		}

		// удаление системных полей модуля бонусов
		if (!empty($arResult['JS_DATA']['LOGICTIM_BONUS'])) foreach ($field as $f_key => $f) if (!empty($f) && in_array($f_key, array('props_Y', 'props_N', 'props_related'))) {
			foreach ($f as $k => $v) if (in_array($v['CODE'], array('LOGICTIM_ADD_BONUS', 'LOGICTIM_PAYMENT_BONUS'))) unset($field[$f_key][$k]);
			if (empty($field[$f_key])) unset($field[$f_key]);
		}

		if (empty($field)) return;

		if (isset($param['mono'])) $mono = $param['mono'];
		else $mono = (count($field) == 1 && $count <= 1 ? true : false);

		$locationTemplate = (!empty($arParams['TEMPLATE_LOCATION']) ? $arParams['TEMPLATE_LOCATION'] : '.default');
		$preload_prop = (empty($arParams['USE_PRELOAD_PROP']) || $arParams['USE_PRELOAD_PROP'] == 'Y' ? true : false);

		$id = (!empty($param['id']['main']) ? $param['id']['main'] : '');
		if ($id == '' && $mono) foreach ($field as $f_key => $f) if ($f_key == 'passport') $id = 'edost_location_passport_div_main';

?>
	<div class="edost_main edost_template_div<?=(!empty($param['class']['main']) ? ' '.$param['class']['main'] : '')?><?=($id == 'edost_location_div' && $param['delivery_location'] === false ? ' edost_active_no' : '')?>"<?=($id != '' ? ' id="'.$id.'"' : '')?> style="<?=(!empty($param['hide']['main']) ? 'display: none;' : '')?><?=(!$param['edost_locations'] ? ' overflow: visible' : '')?>">
<?		if ($id == 'edost_location_div')
			if ($param['delivery_location'] === false) $head = '';
			else if ($param['compact'] != '') { ?>
		<div class="edost_window_hide edost_compact_hide">
			<div class="edost_button_big edost_button_head" onclick="edost_SetTemplateLocation('click')">
				<span><?=GetMessage('SOA_TEMPL_CHANGE_BUTTON')?></span>
			</div>
		</div>
<?			}

		if (!empty($head)) { ?>
		<h4 <?=(!empty($head_compact) ? 'class="edost_compact_head"' : '')?>><?=$head?></h4>
<?		if (!empty($head_compact)) { ?>
		<h4 class="edost_compact_head2"><?=$head_compact?></h4>
<?		} ?>
<?		} ?>
		<div class="edost_div">
<?
		foreach ($field as $f_key => $f) {
			$arSource = (in_array($f_key, array('props_Y', 'props_N', 'props_related')) ? $f : false);

			$id = '';
			if ($f_key == 'passport') $id = 'edost_location_passport_div_main';

			$hide = (!empty($param['hide'][$f_key]) ? true : false);

			// профиль покупателя
			if ($f_key == 'profile' && !empty($arResult['ORDER_PROP']['USER_PROFILES'])) { $hide = ($arParams['ALLOW_USER_PROFILES'] != 'Y' ? true : false);
				foreach($arResult['ORDER_PROP']['USER_PROFILES'] as $arUserProfiles) if ($arUserProfiles['CHECKED'] == 'Y') { ?>
					<input type="hidden" name="PROFILE_ID_OLD" value="<?=$arUserProfiles['ID']?>">
<?				}
				if ($arParams['ALLOW_NEW_PROFILE'] == 'Y') {
				DrawPropHead($mono, GetMessage('SOA_TEMPL_PROP_CHOOSE'), false, $hide); ?>
					<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
						<option value="0"><?=GetMessage('SOA_TEMPL_PROP_NEW_PROFILE')?></option>
<?						foreach($arResult['ORDER_PROP']['USER_PROFILES'] as $arUserProfiles) { ?>
							<option value="<?=$arUserProfiles['ID']?>"<?=($arUserProfiles['CHECKED'] == 'Y' ? ' selected' : '')?>><?=$arUserProfiles['NAME']?></option>
<?						} ?>
					</select>
<?				DrawPropHead($mono);
				} else {
				DrawPropHead($mono, GetMessage('SOA_TEMPL_EXISTING_PROFILE'), false, $hide);
					if (count($arResult['ORDER_PROP']['USER_PROFILES']) == 1) {
						foreach($arResult['ORDER_PROP']['USER_PROFILES'] as $arUserProfiles) { ?>
							<strong><?=$arUserProfiles['NAME']?></strong>
							<input type="hidden" name="PROFILE_ID" id="ID_PROFILE_ID" value="<?=$arUserProfiles['ID']?>">
<?						}
					} else { ?>
						<select name="PROFILE_ID" id="ID_PROFILE_ID" onChange="SetContact(this.value)">
<?							foreach($arResult['ORDER_PROP']['USER_PROFILES'] as $arUserProfiles) { ?>
								<option value="<?=$arUserProfiles['ID']?>"<?=($arUserProfiles['CHECKED'] == 'Y' ? ' selected' : '')?>><?=$arUserProfiles['NAME']?></option>
<?							} ?>
						</select>
<?					}
				DrawPropHead($mono);
				}
			}

			// тип плательщика
			if ($f_key == 'person_type') { $hide = (count($arResult['PERSON_TYPE']) <= 1 ? true : false);
				DrawPropHead($mono, GetMessage('SOA_TEMPL_PERSON_TYPE'), false, $hide);
				if (count($arResult['PERSON_TYPE']) > 1) {
					foreach($arResult['PERSON_TYPE'] as $v) { ?>
						<div class="edost_person_type<?=($v['CHECKED'] == 'Y' ? ' edost_person_type_active' : '')?>" onclick="this.children[0].checked = true; submitForm();">
							<input type="radio" id="PERSON_TYPE_<?=$v['ID']?>" name="PERSON_TYPE" value="<?=$v['ID']?>"<?=($v['CHECKED'] == 'Y' ? ' checked="checked"' : '')?>> <label for="PERSON_TYPE_<?=$v['ID']?>"><?=$v['NAME']?></label>
						</div>
<?					} ?>
					<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$arResult['USER_VALS']['PERSON_TYPE_ID']?>">
<?				} else {
					if (intval($arResult['USER_VALS']['PERSON_TYPE_ID']) > 0) { ?>
						<input type="text" name="PERSON_TYPE" value="<?=intval($arResult['USER_VALS']['PERSON_TYPE_ID'])?>">
						<input type="text" name="PERSON_TYPE_OLD" value="<?=intval($arResult['USER_VALS']['PERSON_TYPE_ID'])?>">
<?					} else foreach($arResult['PERSON_TYPE'] as $v) { ?>
						<input type="hidden" id="PERSON_TYPE" name="PERSON_TYPE" value="<?=$v['ID']?>">
						<input type="hidden" name="PERSON_TYPE_OLD" value="<?=$v['ID']?>">
<?					}
				}
				DrawPropHead($mono);
			}

			// местоположение (edost.locations)
			if ($f_key == 'location') {
				$a = ($param['delivery_location'] !== false ? true : false); ?>
				<div>
					<div class="<?=($a ? 'edost_supercompact_hide edost_resize_button edost_change_button2 edost_button_big2' : 'edost_button_big edost_button_big_red edost_button_big_active')?>" onclick="edost_SetTemplateLocation('<?=($a ? 'click' : 'new')?>')">
						<span><?=GetMessage($a ? 'SOA_TEMPL_CHANGE_LOCATION' : 'SOA_TEMPL_SET_LOCATION')?></span>
					</div>
				</div>

				<div id="edost_location_city_template_div" style="display: none;"></div>
<?				$GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'city') + $param['edost_locations'], null, array('HIDE_ICONS' => 'Y'));

				if ($a) { ?>
				<div class="edost_resize_button2 edost_supercompact_hide">
					<div class="edost_change_button edost_button_big2" onclick="edost_SetTemplateLocation('click')">
						<span><?=GetMessage('SOA_TEMPL_CHANGE_LOCATION')?></span>
					</div>
				</div>
<?				}
			}

			// адрес (edost.locations)
			if ($f_key == 'address') {
				DrawPropHead($mono, GetMessage('SOA_TEMPL_ADDRESS_HEAD'), true, $hide, '', 25);
				echo '<div class="edost_prop_error"></div>';
				$GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'address') + $param['edost_locations'], null, array('HIDE_ICONS' => 'Y'));
				DrawPropHead($mono);
			}

			// паспортные данные (edost.locations)
			if ($f_key == 'passport') {
				DrawPropHead($mono, GetMessage('SOA_TEMPL_PASSPORT_HEAD'), true, $hide, 'edost_location_passport_div_main', 15);
				echo '<div class="edost_prop_error"></div>';
				$GLOBALS['APPLICATION']->IncludeComponent('edost:locations', '', array('MODE' => 'passport') + $param['edost_locations'], null, array('HIDE_ICONS' => 'Y'));
				echo '<div style="font-size: 13px; color: #800; padding-top: 5px;">'.GetMessage('SOA_TEMPL_PASSPORT_WARNING').'</div>';
				DrawPropHead($mono);
			}

			// комментарий к заказу
			if ($f_key == 'comment') {
				DrawPropHead($mono, GetMessage('SOA_TEMPL_SUM_COMMENTS'), false, false, '', 0);
				echo '<textarea name="ORDER_DESCRIPTION" id="ORDER_DESCRIPTION" style="width: 100%; max-width: 100%; min-height: 120px;" '.($preload_prop ? 'onchange="submitProp(this)"' : '').'>'.$arResult['USER_VALS']['ORDER_DESCRIPTION'].'</textarea>';
				DrawPropHead($mono);
			}

			$zip_warning = (empty($arResult['edost']['locations_installed']) && !empty($arResult['edost']['format']['warning']) ? $arResult['edost']['format']['warning'] : false);
			if (!empty($arSource)) foreach ($arSource as $v) {
				if (!empty($param['disable']) && in_array($v['CODE'], $param['disable'])) continue;
				if (!empty($param['enable']) && !in_array($v['CODE'], $param['enable'])) continue;

				$top = false;
				if ($v['TYPE'] == 'TEXTAREA') $top = 0;
				if ($v['IS_ZIP'] == 'Y' && $zip_warning) $top = 5;
				if ($v['TYPE'] == 'LOCATION') $top = 9;
				DrawPropHead($mono, $v['NAME'], $v['REQUIED_FORMATED'], false, '', $top, $v['TYPE'] == 'LOCATION' ? 'bitrix_location' : '');

				if ($v['TYPE'] == 'CHECKBOX') { ?>
					<input type="hidden" name="<?=$v['FIELD_NAME']?>" value="">
					<input type="checkbox" name="<?=$v['FIELD_NAME']?>" id="<?=$v['FIELD_NAME']?>" value="Y"<?=($v['CHECKED']=='Y' ? ' checked' : '')?>>
<?				}
				else if ($v['TYPE'] == 'TEXT') {
					$a = ($v['IS_ZIP'] == 'Y' && empty($arResult['edost']['locations_installed']) ? true : false);
					if ($a && isset($arResult['edost']['location']['country']) && in_array($arResult['edost']['location']['country'], array(0, 85, 21, 14, 108))) $v['inputmode'] = 'numeric';
					DrawInput($v, false, $a, $preload_prop);
					if ($v['IS_ZIP'] == 'Y' && $zip_warning) echo '<div class="edost_warning" style="padding-top: 2px;">'.$zip_warning.'</div>';
				}
				else if ($v['TYPE'] == 'SELECT') { ?>
					<select name="<?=$v['FIELD_NAME']?>" id="<?=$v['FIELD_NAME']?>" size="<?=$v['SIZE1']?>">
<?						foreach($v['VARIANTS'] as $arVariants) { ?>
							<option value="<?=$arVariants['VALUE']?>"<?=($arVariants['SELECTED'] == 'Y' ? ' selected' : '')?>><?=$arVariants['NAME']?></option>
<?						} ?>
					</select>
<?				}
				else if ($v['TYPE'] == 'MULTISELECT') { ?>
					<select multiple name="<?=$v['FIELD_NAME']?>" id="<?=$v['FIELD_NAME']?>" size="<?=$v['SIZE1']?>">
<?						foreach($v["VARIANTS"] as $arVariants) { ?>
							<option value="<?=$arVariants['VALUE']?>"<?=($arVariants['SELECTED'] == 'Y' ? ' selected' : '')?>><?=$arVariants['NAME']?></option>
<?						} ?>
					</select>
<?  			}
				else if ($v['TYPE'] == 'TEXTAREA') {
					$rows = ($v['SIZE2'] > 10) ? 4 : $v['SIZE2']; ?>
					<textarea rows="<?=$rows?>" cols="<?=$v['SIZE1']?>" name="<?=$v['FIELD_NAME']?>" id="<?=$v['FIELD_NAME']?>" <?=($preload_prop ? 'onchange="submitProp(this)"' : '')?>><?=$v['VALUE']?></textarea>
<?				}
				else if ($v['TYPE'] == 'DATE') { ?>
					<input type="text" name="<?=$v['FIELD_NAME']?>" id="<?=$v['FIELD_NAME']?>" value="<?=$v['VALUE']?>" readonly onclick="BX.calendar({node:this, field:'<?=$v['FIELD_NAME']?>', form:'', bTime:false, bHideTime:false});">
<?				}
				else if ($v['TYPE'] == 'LOCATION') {
					$value = 0;
					if (is_array($v['VARIANTS']) && count($v['VARIANTS']) > 0) foreach ($v['VARIANTS'] as $arVariant) if ($arVariant['SELECTED'] == 'Y') { $value = $arVariant['ID']; break; }

					$locationTemplateP = $locationTemplate == 'popup' ? 'search' : 'steps';
					$locationTemplateP = $_REQUEST['PERMANENT_MODE_STEPS'] == 1 ? 'steps' : $locationTemplateP;

					if ($locationTemplateP == 'steps') { ?>
						<input type="hidden" id="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($v['ID'])?>]" name="LOCATION_ALT_PROP_DISPLAY_MANUAL[<?=intval($v['ID'])?>]" value="<?=($_REQUEST['LOCATION_ALT_PROP_DISPLAY_MANUAL'][intval($v['ID'])] ? '1' : '0')?>" />
<?					} ?>

<?					CSaleLocation::proxySaleAjaxLocationsComponent(array(
						'AJAX_CALL' => 'N',
						'COUNTRY_INPUT_NAME' => 'COUNTRY',
						'REGION_INPUT_NAME' => 'REGION',
						'CITY_INPUT_NAME' => $v['FIELD_NAME'],
						'CITY_OUT_LOCATION' => 'Y',
						'LOCATION_VALUE' => $value,
						'ORDER_PROPS_ID' => $v['ID'],
						'ONCITYCHANGE' => ($v['IS_LOCATION'] == 'Y' || $v['IS_LOCATION4TAX'] == 'Y') ? 'submitForm()' : '',
						'SIZE1' => $v['SIZE1'],
					),
					array(
						'ID' => $value,
						'CODE' => '',
						'SHOW_DEFAULT_LOCATIONS' => 'Y',
						'JS_CALLBACK' => 'submitFormProxy',
						'JS_CONTROL_DEFERRED_INIT' => intval($v['ID']),
						'JS_CONTROL_GLOBAL_ID' => intval($v['ID']),
						'DISABLE_KEYBOARD_INPUT' => 'Y',
						'PRECACHE_LAST_LEVEL' => 'Y',
						'PRESELECT_TREE_TRUNK' => 'Y',
						'SUPPRESS_ERRORS' => 'Y'
					),
					$locationTemplateP, true, 'location-block-wrapper');

					if (strlen(trim($v['DESCRIPTION'])) > 0) { ?>
						<div class="bx_description"><?=$v['DESCRIPTION']?></div>
<?					}
				}
				elseif ($v['TYPE'] == 'RADIO') {
					if (is_array($v['VARIANTS'])) foreach($v['VARIANTS'] as $arVariants) { ?>
						<input type="radio" name="<?=$v['FIELD_NAME']?>" id="<?=$v['FIELD_NAME']?>_<?=$arVariants['VALUE']?>" value="<?=$arVariants['VALUE']?>" <?=($arVariants['CHECKED'] == 'Y' ? ' checked' : '')?>>
						<label for="<?=$v['FIELD_NAME']?>_<?=$arVariants['VALUE']?>"><?=$arVariants['NAME']?></label><br>
<?					}
				}
				else if ($v['TYPE'] == 'FILE') {
					$s = '';
					$name = 'ORDER_PROP_'.$v['ID'];
					if ($v['MULTIPLE'] == 'N') {
						$s = "<label for=\"\"><input type=\"file\" size=\"".$v["SIZE1"]."\" value=\"".$v["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
					}
					else {
						$s = '
						<script type="text/javascript">
							function addControl(item) {
								var current_name = item.id.split("[")[0],
									current_id = item.id.split("[")[1].replace("[", "").replace("]", ""),
									next_id = parseInt(current_id) + 1;

								var newInput = document.createElement("input");
								newInput.type = "file";
								newInput.name = current_name + "[" + next_id + "]";
								newInput.id = current_name + "[" + next_id + "]";
								newInput.onchange = function() { addControl(this); };

								var br = document.createElement("br");
								var br2 = document.createElement("br");

								BX(item.id).parentNode.appendChild(br);
								BX(item.id).parentNode.appendChild(br2);
								BX(item.id).parentNode.appendChild(newInput);
							}
						</script>
						';

						$s .= "<label for=\"\"><input type=\"file\" size=\"".$v["SIZE1"]."\" value=\"".$v["VALUE"]."\" name=\"".$name."[0]\" id=\"".$name."[0]\"></label>";
						$s .= "<br/><br/>";
						$s .= "<label for=\"\"><input type=\"file\" size=\"".$v["SIZE1"]."\" value=\"".$v["VALUE"]."\" name=\"".$name."[1]\" id=\"".$name."[1]\" onChange=\"javascript:addControl(this);\"></label>";
					}
					echo $s;
				}

				DrawPropHead($mono);

				if (empty($param['edost_locations'])) if ($v['TYPE'] == 'LOCATION' || intval($v['IS_ALTERNATE_LOCATION_FOR']) || intval($v['CAN_HAVE_ALTERNATE_LOCATION']) || $v['IS_ZIP'] == 'Y') {
					$propertyAttributes = array('type' => $v['TYPE'], 'valueSource' => $v['SOURCE'] == 'DEFAULT' ? 'default' : 'form');

					if(intval($v['IS_ALTERNATE_LOCATION_FOR'])) $propertyAttributes['isAltLocationFor'] = intval($v['IS_ALTERNATE_LOCATION_FOR']);
					if(intval($v['CAN_HAVE_ALTERNATE_LOCATION'])) $propertyAttributes['altLocationPropId'] = intval($v['CAN_HAVE_ALTERNATE_LOCATION']);
					if($v['IS_ZIP'] == 'Y') $propertyAttributes['isZip'] = true;
?>
					<script>
						(window.top.BX || BX).saleOrderAjax.addPropertyDesc(<?=CUtil::PhpToJSObject(array('id' => intval($v['ID']), 'attributes' => $propertyAttributes))?>);
					</script>
<?				}

			}
	    }

?>
	</div>
</div>
<?

	}
}
?>