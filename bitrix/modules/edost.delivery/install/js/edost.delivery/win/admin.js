// edost.admin
// edost.package
// edost.register
// edost_SetOffice

if (typeof edost.admin === "undefined") edost.admin = new function() {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C
	var control_start = true
	var error_data = {'22': 'cбой на сервере службы доставки', '25': 'сбой подключения к серверу службы доставки', '27': 'не задан почтовый индекс', '28': 'не задан адрес', '29': 'не задан номер заказа',
		'30': 'не заданы данные получателя', '31': 'не задан телефон', '32': 'не задан email', '33': 'не заданы данные заказа', '34': 'не задан вес', '35': 'не заданы габариты', '36': 'не заданы данные упаковки',
		'37': 'не верный формат номера сдачи', '38': 'пропущено поле коментарий', '39': 'даты сдач не совпадают', '40': 'нет данных о сдаче', '41': 'заказ не в сдаче', '42': 'не смогли удалить сдачу',
		'43': 'не смогли изменить дату сдачи', '44': 'не смогли удалить всю сдачу (только часть)', '45': 'не смогли переместить из сдачи в заказ', '46': 'не смогли создать заказ без ШПИ',
		'47': 'не верный формат даты сдачи', '48': 'не верный тип тарифа', '49': 'не верный тариф', '51': 'не верный код страны', '52': 'не верный код региона', '55': 'не задан регион доставки',
		'56': 'не задан город доставки', '57': 'не задан тариф', '58': 'не задан статус оплаты', '59': 'не задан внутренний идентификатор заказа в магазине', '60': 'не задан флаг',
		'61': 'не передали все параметры', '64': 'заказ уже передан в службу доставки', '65': 'не смогли получить данные о сдаче', '66': 'не смогли зарегистрировать сдачу в почтовом отделении',
		'67': 'не смогли удалить заказ', '69': 'не смогли добавить в сдачу', '70': 'не смогли включить в сдачу', '71': 'печать бланков для данного вида отправления на текущем этапе оформления недоступна',
		'72': 'невозможно создать бланк (заказ не найден в системе службы доставки)', '73': 'не смогли оформить заказ', '74': 'превышен суточный лимит запросов к API службы доставки', '75': 'внутренняя ошибка',
		'76': 'населенный пункт не обслуживается', '77': 'не верное значение НДС', '78': 'не задан номер дома', '79': 'не верный тип документа для печати бланков', '80': 'в упаковке содержится не известный товар',
		'81': 'не смогли удалить заказ, попробуйте выполнить операцию позже', '82': 'данный заказ уже оформлен (для повторного оформления заказ необходимо вручную удалить в личном кабинете службы доставки)',
		'84': 'на указанную дату и адрес уже существует заявка на вызов курьера', '85': 'не смогли оформить заявку на вызов курьера', '86': 'указанный заказ не оформлен', '87': 'не смогли изменить профили',
		'88': 'сегодня на указанное время вызов курьера невозможен (необходимо указать более позднее время или перенести вызов на другой день)', '89': 'время ожидания курьера выходит за допустимый диапазон (с 09:00 до 18:00)',
		'92': 'не смогли определить тариф', '93': 'не верный почтовый индекс'}

	this.param = (window.edost_admin_param ? edost_admin_param : false)
	this.crm_timer = false
	this.changed = false
	this.CRMEditor = false
	this.delivery_id = false
	this.data = ''
	this.control_data = []
	this.shipment = false
	this.shipment_edit = (window.location.href.indexOf('sale_order_shipment_edit.php?') > 0 ? true : false)
	this.order_create = (window.location.href.indexOf('sale_order_create.php?') > 0 ? true : false)
	this.order_view = (window.location.href.indexOf('sale_order_view.php?') > 0 ? true : false)
	this.office_open = false
	this.alarm = true
	this.reload = false
	this.delivery_shop = false
	this.user = false
	this.location_warning_timer
	this.update_shipment = false
	this.zero_tariff = 0


	this.error = function(c) {
		var s = 'код ' + c;
		if (error_data[c]) s = error_data[c];
		return '<div class="edost_warning" style="font-size: 20px;">ошибка</div><div style="font-size: 16px;">' + s + '</div>';
	}

	// проверка параметров отправления (edost_CheckPackage)
	this.check_package = function(tariff, weight, size, batch_oversize) {

		var size = [size[0] != undefined ? size[0] : 0, size[1] != undefined ? size[1] : 0, size[2] != undefined ? size[2] : 0];

		var r = true;
		weight = String(weight).replace(/,/g, '.').replace(/[^0-9.]/g, '')*1;
		for (var i = 0; i < size.length; i++) size[i] = String(size[i]).replace(/,/g, '.').replace(/[^0-9.]/g, '')*1;
		for (var i2 = 0; i2 <= 1; i2++) for (var i = 0; i < size.length-1; i++) if (size[i] > size[i+1]) {
			var s = size[i];
			size[i] = size[i+1];
			size[i+1] = s;
		}

		if (tariff == 1) {
			if (weight > 2.5) r = 'weight';
		}

		if (tariff == 2) {
			oversize = (weight >= 10 || size[0] > 60 || size[1] > 60 || size[2] > 60 || size[0]*1 + size[1]*1 + size[2]*1 > 120 ? 1 : 0);

			if (weight > 50) r = 'weight';
			else if (size[0]*1 + size[1]*1 + size[2]*1 > 300) r = 'size';
			else if (batch_oversize === 0 && oversize === 1) r = 'oversize_big';
			else if (batch_oversize === 1 && oversize === 0) r = 'oversize_small';
		}

		if (tariff == 3) {
			if (weight >= 31.5) r = 'weight';
			else if (size[0] > 150 || size[1] > 150 || size[2] > 150 || size[0]*1 + size[1]*2 + size[2]*2 > 300) r = 'size';
		}

		// СДЭК - посылка
		if (tariff == 37 || tariff == 38) {
			if (weight > 30) r = 'weight';
		}

		// СДЭК - экономичная посылка
		if (tariff == 35 || tariff == 76) {
			if (weight > 50) r = 'weight';
		}

		if (tariff == 65 || tariff == 76) {
			if (weight > 55) r = 'weight';
		}

		if (r === 'weight') r = ['Превышен максимально допустимый вес!', 'уменьшить вес или разделить заказ на две отправки'];
		else if (r === 'size') r = ['Превышены максимально допустимые габариты!', 'уменьшить габариты или разделить заказ на две отправки'];
		else if (r === 'oversize_big') r = ['Слишком большие габариты для выбранной сдачи!', 'выбрать сдачу с негабаритными отправлениями (или новую)'];
		else if (r === 'oversize_small') r = ['Слишком маленькие габариты для выбранной сдачи!', 'выбрать сдачу с габаритными отправлениями (или новую)'];

		return r;

	}


	// формирование списка с тарифами
	this.tariff_select = function(data, profile_id, set_profile) {
//		console.log(data);

		var s = '';
		var checked = false;
		var tariff_price = -1;
		var tariff_pricecash = -1;
		var optgroup = false;
		self.zero_tariff = data.zero_tariff;
		if (data.format != undefined) for (var i = 0; i < data.format.length; i++) {
			var v = data.format[i];
			if (v.head !== undefined) {
				if (optgroup) s += '</optgroup>';
				optgroup = true;
				s += '<optgroup label="' + v.head + '">';
				continue;
			}

			if (v.office_mode == 'all') {
				s += '<option style="color: #55A;" value="' + data.zero_tariff + '" data-edost_profile="all" data-edost_company_id="" data-edost_day="" data-edost_office_address_full="" data-edost_price="0" data-edost_office_mode="all">Все пункты самовывоза на одной карте</option>';
				continue;
			}

			if (v.id == profile_id) {
				checked = true;
				if (v.price != undefined) tariff_price = v.price;
				if (v.pricecash != undefined) tariff_pricecash = v.pricecash;
			}

//			if (v.pricetotal_formatted == undefined) v.pricetotal_formatted = v.price_formatted;

			if (v.pricetotal_formatted === '0') v.pricetotal_formatted = 'Бесплатно!';
			if (v.pricecash_formatted === '0') v.pricecash_formatted = 'Бесплатно!';

			s += '<option value="' + (v.id != '' ? v.id : data.zero_tariff) + '" ' + (v.id == profile_id || !set_profile && v.checked ? ' selected="selected"' : '');
			if (v.transfer_formatted != undefined && v.transfer_formatted != 0) s += ' data-edost_transfer_formatted="' + v.transfer_formatted + '"';

			var ar = ['profile', 'company_id', 'day', 'office_address_full', 'office_id', 'office_detailed', 'price', 'pricecash', 'office_mode', 'priceinfo_formatted', 'error', 'tracking_example', 'tracking_format'];
			for (var u = 0; u < ar.length; u++) if (v[ ar[u] ] != undefined) s += ' data-edost_' + ar[u] + '="' + v[ ar[u] ] + '"';

			s += '>' + v.title + (v.pricetotal_formatted != undefined ? ' - ' + v.pricetotal_formatted : '') + (v.pricecash_formatted != undefined ? ' (' + v.pricecash_formatted + ')' : '');
			s += '</option>';
		}

		if (optgroup) s += '</optgroup>';

		return {"option": s, "checked": checked, "tariff_price": tariff_price, "tariff_pricecash": tariff_pricecash};

	}

	this.set_alarm = function(data, profile_id, select, profile_name, price, cod, div_start) {
		var r = '';
		var s = {
			'zero_tariff': 'Внимание!<br>При оформлении заказа доставка не была рассчитана.',
			'no_profile': 'Внимание!<br>При оформлении заказа был выбран тариф "%profile%" (%price% руб.), но в данный момент расчет доставки по этому тарифу невозможен, или закрылся выбранный пункт выдачи/терминал/постамат (или в заказе удален его адрес).',
			'price_change': 'Внимание!<br>Сохраненная стоимость доставки (%price% руб.) отличается от рассчитанной по текущим тарифам (%price2% руб.)'
		};
		if (profile_id != 0 && self.alarm) {
			if (profile_id == data.zero_tariff) r = s.zero_tariff;
			else if (!select.checked && !edost.admin.order_create) r = s.no_profile.replace(/%profile%/g, profile_name).replace(/%price%/g, price);
			else if (select.checked && select.tariff_price != -1 && (!cod && price != select.tariff_price || cod && price != select.tariff_pricecash && select.tariff_pricecash != -1)) {
				self.alarm = 'price_change';
				r = s.price_change.replace(/%profile%/g, profile_name).replace(/%price%/g, price).replace(/%price2%/g, cod ? select.tariff_pricecash : select.tariff_price);
			}
		}
		if (r != '') r = div_start + r + '</div>';
		return r;
	}

	this.post = function(url, data, callback) {
		if (url == 'admin') url = '/bitrix/admin/edost.php';
		if (url == 'ajax') url = self.param.ajax_file;
		if (url == 'location') url = self.param.locations_path + '/edost_location.php';
		BX.ajax.post(url, data, callback);
	}


	this.admin_start = function() {
	//	function edost_InsertJS() {

		if (self.param.crm) {
			if (self.crm_timer != undefined) window.clearInterval(self.crm_timer);
			self.crm_timer = window.setInterval('edost.admin.insert_crm()', 100);
			return;
		}

		if (self.order_view) {
			var s = document.querySelectorAll('div[data-id="buyer"] td.adm-detail-content-cell-r div');
			if (s) for (var i = 0; i < s.length; i++) {
				var v = s[i].innerHTML;

				// подпись "индекс определен приблизительно"
				if (v.length == 7 && v.replace(/[^0-9.]/g, '').length == 7 && v.replace(/[^0-9]/g, '').length == 6 && v.substr(-1, 1) == '.') {
					s[i].innerHTML = v + ' <span style="color: #F00;">индекс определен приблизительно</span>';
				}

				// вывод пункта выдачи
				if (self.param.office && self.param.office.address_full == v) {
					var c = 'код пункта выдачи:';
					v = self.param.office.address_formatted;
					if (v.indexOf(c) > 0) v = v.replace(c, '<b>код филиала:') + '</b>';
					s[i].innerHTML = v;
				}
			}
		}

		if (!BX.Sale || !self.shipment_edit && !self.order_create) return;

		self.insert_shipment_edit(false);

		// кнопки "сохранить" и "применить"
		var ar = ['save', 'apply'];
		for (var i = 0; i < ar.length; i++) {
			var e = BX.findChild(document, {attribute: {'name': ar[i]}}, true);
			if (e) e.onclick = new Function('', 'return edost.admin.check_prop("' + ar[i] + '");');
		}

		self.change_delivery('head');
		window.setInterval('edost.admin.draw_control("update")', 1000);

		if (self.param.edost_locations) self.insert_location();

	}


	// поставновка на контроль, снятие с котроля и изменение флага + отчистка списка 'changed'
	this.control = function(id, flag, e) {

		self.update_shipment = true;
		var post = '';

		if (flag == 'changed_delete') {
			BX('control_changed_div').style.display = 'none';
		}
		else if (e != undefined) {
			if (flag == 'delete' || flag == 'paid') {
				e.style.display = 'none';
				e = BX.findNextSibling(e);
				e.style.display = 'inline';
			}
			else if (flag == 'delete_register') edost.register.delete('order|' + id);
			else if (flag == 'order_date') {
				edost.register.delete('order|' + id);
				post += '&date=' + encodeURIComponent(e);
			}
			else if (flag == 'order_batch_delete') edost.register.delete('batch|' + id);
			else if (flag == 'batch_delete') {
				e.style.display = 'none';

				e = BX.findNextSibling(e);
				if (e) e.style.display = 'inline';

				e = BX('edost_batch_button_' + id);
				if (e) e.style.display = 'none';

				e = BX('edost_batch_print_' + id);
				if (e) e.style.display = 'none';

				var e = BX('edost_batch_' + id);
				if (e) e.parentNode.remove();

				var ar = document.getElementsByName('edost_batch_' + id);
				if (ar) for (var i = 0; i < ar.length; i++) edost.register.delete('order|' + ar[i].value);
			}
		}
		else if (flag == 'auto' || flag == 'auto_off') {
			self.update_shipment = false;
			var auto_off = (flag == 'auto_off' ? true : false);

			id = id.toString().split(',');
			var a = 0;
			var id_new = [];
			for (var i = 0; i < id.length; i++) {
				var e = BX('edost_shipment_' + id[i] + '_value');
				if (!e) return;

				if (auto_off && e.value == 1) id_new.push(id[i]);

				if (i == 0) {
					a = (e.value == 1 || flag == 'auto_off' ? 0 : 1);
					flag = (a ? 'new' : 'old');
				}
				e.value = a;

				var e = BX('edost_shipment_' + id[i] + '_img');
				if (e) e.className = 'edost_control_button_new' + (a ? '_active' : '');
			}

			id = (auto_off ? id_new.join(',') : id.join(','));
		}
		else {
			if (self.shipment_edit || self.order_create) {
				var e = BX('edost_shipment_' + id + '_flag');
				var f = e.value;

				if (flag == 'delete') self.control_data[1].count++;
				else if (flag == 'add') self.control_data[1].count--;

				if (flag == 'delete') f = 0;
				else if (flag == 'new') f = (f == 3 ? 4 : 2);
				else if (flag == 'old') f = (f == 4 ? 3 : 1);
				else if (flag == 'special') f = (f == 2 ? 4 : 3);
				else if (flag == 'normal') f = (f == 4 ? 2 : 1);
				else f = 1;

				self.draw_control(id, f, f == 0 ? false : true, '', 1);

				return;
			}

			var e = BX('edost_control_head_' + id);
			if (e) {
				index = e.parentNode.parentNode.id.split('_')[2];
				self.draw_control(id, 'loading', '', '', index);
			}
		}

		self.post('ajax', 'mode=control&id=' + id + '&flag=' + flag + post, function(r) {
			if (r != 'OK') alert(r);
			if (!edost.admin.update_shipment) return;
			edost.admin.shipment = false;
			edost.admin.insert_control('timer');
		});

	}

	// вывод строки контроля (с кнопками)
	this.draw_control = function(id, flag, control, status_full, index, register, package) {

		var crm = self.param.crm;

		if (index == undefined) index = 1;

		var e = BX('edost_shipment_' + index + '_tr');

		if (self.param.crm) {
			var e2 = BX('edost_package_' + index + '_tr');
			if (e2) BX.remove(e2);
		}
		else if (id === 'update') {
			// скрыть строку контроля, если нет номера накладной
			if (e) {
				var ar = document.getElementsByName('SHIPMENT[' + index + '][TRACKING_NUMBER]');
				if (ar) {
					var a = (ar[0].value != '' ? true : false);
					a = true;
					e.style.display = (a ? 'table-row' : 'none');
				}
			}

			return;
		}

		if (e) BX.remove(e);


		var package_formatted = false;
		if (package) package_formatted = package.package_formatted + (package.option_formatted ? '<br>' + package.option_formatted : '');

		if (crm) {
			var e = document.querySelector('div[data-cid="SHIPMENT"] div.crm-entity-widget-content-block-inner');
			if (!e) return;
		}
		else {
			var e = false;
			if (!self.shipment_edit && !self.order_create) e = BX('TRACKING_NUMBER_' + index + '_EDIT');
			else {
				e = document.getElementsByName('SHIPMENT[' + index + '][TRACKING_NUMBER]');
				if (e) e = e[0];
			}

			if (!e) return;

			e = BX.findParent(e, {'tag': 'tr'});
			e = BX.findParent(e);

			var E_package = BX('edost_package_' + index + '_tr');
			if (!E_package && package) {
				var s = '';
				s += '<td class="adm-detail-content-cell-l">Параметры отправления:</td>';
				s += '<td class="adm-detail-content-cell-r tal">' + package_formatted + '</td>';
				e.appendChild( BX.create('tr', {'props': {'id': 'edost_package_' + index + '_tr', 'innerHTML': s}}) );
			}
			if (id == undefined) return;
		}


		var small = false;
		if (crm) small = (e.offsetWidth < 450 ? true : false);
		else small = (BX.pos(e).width < 1040 ? true : false);


		var E_width = e.offsetWidth;
		var flag_special = (flag == 3 || flag == 4 ? true : false);
		var flag_new = (flag == 2 || flag == 4 ? true : false);
		var register_active = (register != undefined && register != 0 ? true : false);

		var s = '';

		if (!crm) s += '<td class="adm-detail-content-cell-l">';

		var a = true;

		s += '<div id="edost_control_head_' + id + '">';
		if (flag !== 'loading') {
			if (self.shipment_edit || self.order_create) s += '<input id="edost_shipment_' + id + '_flag" name="edost_shipment_flag" type="hidden" value="' + (flag != undefined ? flag : '0') + '">';

			if (!control) {
				if (self.control_data[index].count > 0) s += '<span class="edost_control_button edost_control_button_add" onclick="edost.admin.control(' + id + ', \'add\'' + ')">поставить на контроль</span>&nbsp;<br><span style="font-size: 11px;">доступно: <b>' + self.control_data[index].count + '</b></span>&nbsp;';
				else if (!self.control_data[index].shop_id) a = false;
				else s += '<a href="' + self.param.edost_path + self.control_data[index].shop_id + '" target="_blank">докупить заказов:</a>';
			}
			else {
				s += '<div class="edost_control_head" style="' + (flag_special ? ' font-size: 15px;' : '') + (crm ? ' background: #F88;' : '') + '">';

				var color = (!crm ? '#eef5f5' : '#FFF');
				if (!register_active) {
					if (small) s += '<div style="height: 18px; background: ' + color + '; padding: 0 5px 0 5px; float: left;"><img class="edost_control_button_new' + (flag_new ? '_active' : '') + '" src="' + self.param.img_path + '/control_new.png" border="0" onclick="edost.admin.control(' + id + ', \'' + (flag_new ? 'old' : 'new') + '\'' + ')" title="' + (flag_new ? 'снять отметку' : 'поставить отметку') + '">&nbsp;</div>';
					else if (flag_new) s += '<div style="color: #000; font-size: 12px; border-width: 2px 0 2px 0; border-color: #000; border-style: solid; background: #FB0; padding: 0 5px 0 5px; height: 14px; float: left;">' + (small ? '!!!' : 'новый') + '</div><div style="width: 5px; height: 18px; float: left; background:' + color + ';">&nbsp;</div>';

					if (flag_special) s += '<div class="edost_control_special edost_control_special_' + (small ? 'small' : 'big') + ' edost_control_special_left"></div>';
				}

				if (register_active) s += 'на оформлении';
				else if (flag_special) s += (small ? 'особый' : 'на особом контроле');
				else s += (small ? 'контроль' : 'на контроле');

				if (flag_special) s += '<div class="edost_control_special edost_control_special_' + (small ? 'small' : 'big') + ' edost_control_special_right"></div>';

				s += '</div>';

				if (!register_active) {
					s += '&nbsp;';

					if (!small) s += '<span class="edost_control_button edost_control_button_low" style="float: left;" onclick="edost.admin.control(' + id + ', \'' + (flag_new ? 'old' : 'new') + '\'' + ')">' + (flag_new ? 'снять отметку' : 'поставить отметку') + '</span>';

					var ar = [];
					if (!flag_special) ar.push('<span class="edost_control_button edost_control_button_low" onclick="edost.admin.control(' + id + ', \'special\')">' + (small ? 'на особый' : 'на особый контроль') + '</span>');
					else ar.push('<span class="edost_control_button edost_control_button_low" onclick="edost.admin.control(' + id + ', \'normal\')">' + (small ? 'обычный' : 'на обычный контроль') + '</span>');
					ar.push('<span class="edost_control_button edost_control_button_low" onclick="edost.admin.control(' + id + ', \'delete\')">' + (small ? 'снять' : 'снять с контроля') + '</span>');

					if (!crm) s += ar.join('&nbsp;&nbsp;|&nbsp;&nbsp;');
					else s += '<div style="float: right; padding-left: 10px;">' + ar.join('&nbsp;&nbsp;|&nbsp;&nbsp;') + '</div>';
				}
			}
		}
		s += '</div>';
		if (!crm) s += '</td>';

		if (!crm) s += '<td class="adm-detail-content-cell-r tal">';
		else s += '<div style="clear: both;' + (status_full != '' ? ' padding-top: 10px;' : '') + '">';
		if (flag === 'loading') s += edost.loading20;
		else {
			if (register_active) s += '<a class="edost_print_link" href="/bitrix/admin/edost.php?lang=' + self.param.language_id + '&type=register&control=search_shipment&search=' + id +'">открыть</a>';
			else s += (status_full != '' ? status_full : '<div id="edost_control_' + id + '"></div>');
		}
		if (!crm) s += '</td>';
		else s += '</div>';

		if (!crm) e.appendChild( BX.create('tr', {'props': {'id': 'edost_shipment_' + index + '_tr', 'innerHTML': s}}) );
		else {
			if (a) e.appendChild( BX.create('div', {'props': {'id': 'edost_shipment_' + index + '_tr', 'className': 'crm-entity-widget-content-block-inner-box', 'style': 'margin-top: 10px;', 'innerHTML': s}}) );
			if (package_formatted) e.appendChild( BX.create('div', {'props': {'id': 'edost_package_' + index + '_tr', 'className': 'crm-entity-widget-content-block', 'style': 'margin-top: 5px;', 'innerHTML': '<span style="color: #80868e; font-size: 13px;">Параметры отправления: </span>' + package_formatted}}) );
		}

	}


	this.setting = function(mode, param, value) {

		if (mode == 'profile_setting_new' || mode == 'profile_setting_change') mode = 'profile_setting';

		var post = 'mode=' + mode;
		if (param == 'get') post += '&id=' + value;
		if (param == 'save' && value != '') post += '&data=' + encodeURIComponent(value);
		if (param == 'post') post += '&' + value;

		edost.window.resize('loading');
		self.post('ajax', post, function(r) { edost.window.resize('set', r); });

	}


	// установка параметров в админке + запись куки + обновление блока контроля (edost_SetParam)
	this.set_param = function(param, value, value2) {
//		alert(param + ' | ' + value);

		// переход со страницы контроля на страницу оформления
		if (param == 'control' && value == 'register') {
			if (window.history && window.history.pushState) window.history.pushState(null, null, '/bitrix/admin/edost.php?lang=' + self.param.language_id + '&type=register');
			H('edost_control_data_div', edost.loading);
			window.location.reload();
			return;
		}

		var post = '';
		var get = '';
		var company = '';
		var type = param.split('_')[0];
		if (value === true) value = 'Y';
		if (value === false) value = 'N';

		var main = false;
		if (value != 'reload' && value != 'reload_full')
			if (param == 'control' || param == 'register') main = true;
			else {
				edost.cookie(['admin', param], value);

				// блок настроек на странице контроля заказов и оформления доставки
				if (param == 'control_setting' || param == 'register_setting') {
					var a = (value == 'Y' ? true : false);
					BX('control_setting_show').style.display = (!a ? 'inline' : 'none');
					BX('control_setting_hide').style.display = (a ? 'inline' : 'none');
					BX('control_setting').style.display = (a ? 'block' : 'none');
					return;
				}

				value = '';

				if (location.href.indexOf('type=register&control=search') > 0) {
					main = true;
					value = location.href.split('&control=')[1];
				}
			}

		if (main) {
			// поиск на странице контроля и оформления
			var s = value.split('_');
			if (s[0] == 'search') post = get = '&search=' + encodeURIComponent(edost_search_value);
			else if (s[0] == 'history') {
				var e = BX('edost_history');
				post = get = '&id=' + e.value;
			}


			if (param == 'register')
				if (s[0] == 'company') {
					company = s[1];
					value = (value2 != undefined ? value2 : '');
				}
				else company = edost.get('company');
		}

		var e = BX('edost_data_div');
		if (!e) return;

		var p = BX.pos(e);
		var p2 = BX.GetWindowScrollPos();
		var x = p.top - p2.scrollTop;
		if (x < 0) window.scrollBy(0, x);

		H('edost_control_data_div', edost.loading);

		if (!main) {
			if (value == 'reload_full') {
				value = '';
				post += '&clear_cache=Y';
				e2 = BX('edost_reload');
				if (e2) e2.style.display = 'none';
			}
			if (param == 'register' && company == '') company = edost.get('company');

			value = edost.get('control');
			if (value == 'history') value += '&id=' + edost.get('id');
			if (value == 'search_order' || value == 'search_shipment') value += '&search=' + edost.get('search');
		}
		else if (window.history && window.history.pushState) window.history.pushState(null, null, '/bitrix/admin/edost.php?lang=' + self.param.language_id + '&type=' + type + (company != '' ? '&company=' + company : '') + (value != '' ? '&control=' + value : '') + get);

		post = 'type=' + type + '&ajax=Y' + (value != '' ? '&control=' + value : '') + (company != '' ? '&company=' + company : '') + post;
		self.post('admin', post, function(r) {
			e.innerHTML = r;
			if (param.split('_')[0] == 'register') edost.register.active_all_update();
		});

	}


	// поиск по идентификатору отправления
	this.search = function(id, param, event) {

		var e = BX(id);
		if (!e) return;

		var s = id.split('_');
		var type = s[0];
		s.splice(0, 1);
		var value = s.join('_');

		if (param == 'start') {
			if (edost_search_timer != undefined) window.clearInterval(edost_search_timer);
			edost_search_value = edost.filter(e.value);
			edost_search_timer = window.setInterval("edost.admin.search('" + id + "')", 100);
			return;
		}

		if (param == 'hide') {
			if (edost_search_timer != undefined) window.clearInterval(edost_search_timer);
			return;
		}

		if (param == 'keydown') {
			if (event.keyCode == 38 || event.keyCode == 13) if (event.preventDefault) event.preventDefault(); else event.returnValue = false;
			if (event.keyCode == 13) if (e.value != '') self.set_param(type, value);
			return;
		}

		var v = edost.filter(e.value, 'array'); // удаление лишних символов и формирование массива с фразами
		if (value == 'search') for (var i = 0; i < v.length; i++) if (v[i].length < 3) { v.splice(i, 1); i--; } // удаление фраз длиной менее 3 букв

		// проверка на изменение в поисковом запросе
		var v2 = edost_search_value.split(',');
		if (v.length == v2.length) {
			var a = false;
			for (var i = 0; i < v.length; i++) if (v[i] != v2[i]) a = true;
			if (!a) return;
		}

//			if (edost_search_value == e.value && (value != 'search' || e.value.length < 3)) return;
//			if (edost_search_value == e.value || e.value.length < 1) return;
//			if (e.value.length < 1 || value == 'search' && e.value.length < 3) return;

		if (v[0].length == 0) return;

		edost_search_value = v.join(',');
		self.set_param(type, value);

	}


	// вывод примера и формата накладной доставки
	this.set_tracking = function(example, format, index, visible) {

		if (index == undefined) index = 1;
		if (visible == undefined) visible = true;

		var e = BX('edost_tracking_example_' + index);
		if (example == 'update') {
			if (e) e.style.display = (visible ? 'block' : 'none');
			return;
		}
		if (e) BX.remove(e);

		var ar = document.getElementsByName('SHIPMENT[' + index + '][TRACKING_NUMBER]');
		if (ar) for (var i = 0; i < ar.length; i++) {
			var e = BX.findParent(ar[i]);
			var s = '';
			if (example) s += '<span style="color: #888;">Пример идентификатора:</span> ' + example;
			if (format) s += (s != '' ? '<br>' : '') + '<span style="color: #888;">Формат идентификатора:</span> ' + format;
			if (e) e.appendChild( BX.create('div', {'props': {'id': 'edost_tracking_example_' + index, 'style': 'padding-top: 5px;' + (!visible ? ' display: none;' : ''), 'innerHTML': s}}) );
		}

	}

	// установка иконки тарифа
	this.set_ico = function(id, company_id, shipment_id, index) {

		if (!id) id = 0;
		if (self.param.template_ico == 'C') {
			if (id == 0 || company_id == '') company_id = 0;
			else if (id >= 31 && id <= 34) company_id = 'v' + (id - 30);
			id = 'company/' + company_id;
		}

		if (self.param.crm && id) {
			var e = document.querySelector('div[data-cid="DELIVERY_LOGO"] img');
			if (e) {
				e.src = self.param.ico_path + '/' + id + '.gif';
				e.width = '80';
				e.height = '80';
			}
			return;
		}

		if (!self.shipment_edit && !self.order_create) return;

		if (index == undefined) index = 1;

		var e = BX('delivery_service_logo_' + index);

		if (self.param.template_ico == 'C') {
			if (e) e.style = 'background: url("' + self.param.ico_path + '/company/' + company_id + '.gif"); background-size: contain;';
		}
		else {
			if (e) e.style.background = 'url("' + self.param.ico_path + '/big/' + id + '.gif")';
		}

		if (!shipment_id) return;

		var e = BX('sale-admin-order-icon-shipment-' + shipment_id);
		if (e) e.style.background = 'url("' + self.param.ico_path + '/' + id + '.gif")';

	}




	// интеграция кода в страницу редактирования отгрузки
	this.insert_shipment_edit = function(update) {

		var e = BX('SHIPMENT_ID_1');

		if (self.order_create) {
			var E_paysystem = BX('PAY_SYSTEM_ID_1');
			if (E_paysystem && !E_paysystem.onchange) E_paysystem.onchange = new Function('', 'edost.admin.change_delivery(true)');
			if (!e && self.param.edost_locations) self.update_location('address');
		}

		if (!e) return;

		var id = e.value;
		if (update == undefined) update = true;
		self.office_open = false;

		// получение отформатированных тарифов edost
		self.post('ajax', 'mode=order_edit&id=' + id, function(data) {
			var E_delivery = BX('DELIVERY_1');
			var E_profile = BX('PROFILE_1');
			var E_price = BX('PRICE_DELIVERY_1');

			if (!E_delivery || !E_profile) {
				if (edost.admin.param.edost_locations) edost.admin.update_location('address');
				return;
			}

			var module_id = E_delivery.value;
			var profile_id = E_profile.value;
			var price = (E_price ? E_price.value : 0);

			if (profile_id != 0) {
				var e = E_profile.options[E_profile.selectedIndex];
				var profile_name = e.text;
			}

			var e = E('edost_payment');
			var payment_index = (e ? e.selectedIndex : false);

			E_profile = BX.findParent(E_profile);

			var e = BX('edost_office_data');
			if (e) e.value = data;

			edost.admin.data = data = (window.JSON && window.JSON.parse ? JSON.parse(data) : eval('(' + data + ')'));

			var e = BX('edost_paysystem_tr');
			if (e) BX.remove(e);
			var e = BX('edost_shipment_tr');
			if (e) BX.remove(e);

			if (module_id == data.module_id) {
				edost.admin.shipment = false;
				edost.admin.insert_control();
			}
			else {
				edost.admin.draw_control();
				if (edost.admin.param.edost_locations) edost.admin.update_location('address');
				return;
			}

			// формирование списка с тарифами
			var select = edost.admin.tariff_select(data, profile_id);
			E_profile.innerHTML = '<select id="PROFILE_1" class="adm-bus-select" name="SHIPMENT[1][PROFILE]" onchange="edost.admin.change_delivery()">' + select.option + '</select>';

			// отключение блока "Расчетная стоимость доставки" (цена с кнопкой "применить")
			var e = BX('shipment_container_1');
			if (e) {
				e = BX.findChild(e, {'tag': 'tr', 'class': 'row_set_new_delivery_price'}, true);
				if (e) e.style.display = 'none';
			}

			var e = BX('BLOCK_PROFILES_1');
			if (e) {
				var e = BX.findParent(e);
				var cod = false;

				// выбор способа оплаты
				if (edost.admin.param.payment_select) {
					var s = '<td class="adm-detail-content-cell-l fwb">Способ оплаты:</td><td class="adm-detail-content-cell-r">' + edost.admin.param.payment_select + '</td>';
					e.appendChild( BX.create('tr', {'props': {'id': 'edost_paysystem_tr', 'innerHTML': s}}) );
					var e2 = E('edost_payment');
					if (e2) {
						if (payment_index !== false) e2.options[payment_index].selected = true;
						if (e2.selectedIndex != -1 && e2.options[e2.selectedIndex].getAttribute('data-edost_cod') == 'Y') cod = true;
					}
				}

				// поле для адреса пункта выдачи + дополнительная информация (error, warning, наценки за наложку, отформатированный адрес пункта выдачи)
				var s = '<td><input id="edost_address" name="edost_address" type="hidden" value=""></td><td id="edost_shipment_td">';
				s += (!edost.admin.param.edost_locations ? '<div id="edost_office_address"></div>' : '');
				s += '<div id="edost_delivery_info"></div>';
				s += self.set_alarm(data, profile_id, select, profile_name, price, cod, '<div id="edost_alarm" style="padding-top: 10px; font-weight: bold; color: #e60;">');
				s += '</td>';

				e.appendChild( BX.create('tr', {'props': {'id': 'edost_shipment_tr', 'innerHTML': s}}) );
			}

			// включение блока выбора местоположений и адреса доставки
			if (!edost.admin.order_create) {
				var e = BX('edost_location_admin_city_div');
				if (e) e.style.display = 'block';
				var e = BX('edost_location_admin_address_div');
				if (e) e.style.display = 'block';
				var e = BX('edost_location_admin_passport_div');
				if (e) e.style.display = 'block';
			}

			edost.admin.change_delivery(update);
			if (edost.admin.param.edost_locations) edost.admin.update_location('address');
		});

	}


	// выбор тарифа в выпадающем списке
	this.change_delivery = function(update) {
//		console.log('change_delivery: ' + update);

		var E_office = BX('edost_office_address');
		var E_info = BX('edost_delivery_info');
		var E_alarm = BX('edost_alarm');
		var E_admin_city = BX('edost_location_admin_city_div');
		var E_admin_address = BX('edost_location_admin_address_div');
		var E_admin_passport = BX('edost_location_admin_passport_div');
		var E_location_address = BX('edost_location_address_div');
		var E_delivery = BX('DELIVERY_1');

		// проверка на доставку битрикса
		if (self.delivery_shop !== 'location_updated') self.delivery_shop = true;
		if (E_delivery) for (var i = 0; i < self.param.edost_id.length; i++) if (E_delivery.value == self.param.edost_id[i]) { self.delivery_shop = false; break; }

		// замена названия доставки на 'eDost'
		if (update === 'head') {
			var ar2 = BX.findChildren(E_delivery, {'tag': 'option'}, true);
			for (var i = 0; i < ar2.length; i++) for (var i2 = 0; i2 < self.param.edost_id.length; i2++) if (ar2[i].value == self.param.edost_id[i2]) { ar2[i].innerHTML = '[' + ar2[i].value + '] eDost'; break; }
			return;
		}

		// обновление списка доставок
		if (update === 'service') {
	        // перезагрузка местоположения при смене пользователя
			if (self.param.edost_locations && self.order_create) {
				var s = '';
	        	var ar = ['USER_ID', 'BUYER_PROFILE_ID', 'PERSON_TYPE_ID'];
				for (var i = 0; i < ar.length; i++) {
					var e = BX(ar[i]);
					s += (e ? e.value : '') + '_';
				}
				if (self.user != s) {
					var s2 = self.user;
					self.user = s;
					if (s2 !== false) {
						H('edost_location_city_div', edost.loading);
						window.setTimeout('edost.admin.insert_location(true)', 1000);
					}
					return;
				}
			}

			self.alarm = false;
			self.set_tracking('update', '', 1, self.delivery_shop ? false : true);
			if (E_office) E_office.style.display = 'none';
			if (E_info) E_info.style.display = 'none';
			if (E_alarm) E_alarm.style.display = 'none';
			if (!self.order_create) {
				if (E_admin_city) E_admin_city.style.display = 'none';
				if (E_admin_address) E_admin_address.style.display = 'none';
				if (E_admin_passport) E_admin_passport.style.display = 'none';
			}
			else if (self.param.edost_locations) {
				if (E_location_address) E_location_address.style.display = 'block';
				if (self.delivery_shop) window.self.update_location('address', false, 0);
			}
			return;
		}
		else if (self.alarm === 'price_change') self.alarm = false;
		else if (update === undefined && !self.alarm && E_alarm) E_alarm.style.display = 'none';

		var reload = false;
		if (update === 'reload') {
			update = true;
			reload = true;
		}

		var request = false;
		if (update == undefined) {
			update = true;
			request = true;
			self.office_open = true;
		}

		var office_window = true;
		if (update == 'office_esc') {
			update = false;
			office_window = false;
		}

		if (self.delivery_shop) return;

		var e = BX('PROFILE_1');
		if (!e) return;

		var E_element = e.options[e.selectedIndex];
		var E_address = BX('edost_address');
		if (!E_element || !E_address) return;

		var cod = false;
		if (self.order_create) {
			var e = BX('PAY_SYSTEM_ID_1');
			if (e && e.value != 0) for (var i = 0; i < self.param.payment_code.length; i++) if (self.param.payment_code[i] == e.value) cod = true;
		}
		else {
			var e = E('edost_payment');
			if (e && edost.data(e.options[e.selectedIndex], 'edost_cod') == 'Y') cod = true;
		}

		var company_id = edost.data(E_element, 'edost_company_id');
		var company_id = edost.data(E_element, 'edost_company_id');
		var profile = edost.data(E_element, 'edost_profile');
		var price = edost.data(E_element, 'edost_price');
		var pricecash = edost.data(E_element, 'edost_pricecash');
//		var address = edost.data(E_element, 'edost_address');
		var address = edost.data(E_element, 'edost_office_address_full');
		var office_id = edost.data(E_element, 'edost_office_id');
		var office_detailed = edost.data(E_element, 'edost_office_detailed');

		var tariff_id = (profile ? Math.ceil(profile/2) : 0);
		if (cod) price = (pricecash != undefined ? pricecash : 0);

		if (update) {
//			var e = BX('BASE_PRICE_DELIVERY_1');

			var e = BX('PRICE_DELIVERY_1');
			if (e) e.value = price;

			var e = BX('CALCULATED_PRICE_1');
			if (e) e.value = price;

			var e = BX('CUSTOM_PRICE_DELIVERY_1');
			if (e) e.value = 'Y';
		}

		self.set_ico(tariff_id, company_id);

		if (!self.order_create && E_admin_passport) {
			var a = false;
			for (var i = 0; i < self.param.passport_required.length; i++) if (tariff_id == self.param.passport_required[i]) { a = true; break; }
			E_admin_passport.style.display = (a ? 'block' : 'none');
		}

		if (self.reload) {
			self.reload = false;
			BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());
			return;
		}

		var v1 = edost.data(E_element, 'edost_tracking_example');
		var v2 = edost.data(E_element, 'edost_tracking_format');
		self.set_tracking(v1, v2);

		// вывод error, warning, pricecash и priceinfo
		var s = '';
		var error = '';

		if (cod) {
			if (pricecash != undefined) {
				var v = edost.data(E_element, 'edost_transfer_formatted');
				if (v) s += '<div style="padding-top: 5px; color: #F00;">' + self.param.sign_transfer.replace('%transfer%', v) + '</div>';
			}
			else if (profile > 0) error += '<span style="padding: 2px 8px; background: #F00; color: #FFF;">Для выбранного способа доставки наложенный платеж недоступен!!!</span>';
		}

		var v = edost.data(E_element, 'edost_day');
		if (v) s += '<div style="padding-top: 5px;"><span style="color: #888;">Срок доставки:</span> ' + v + '</div>';

		var v = edost.data(E_element, 'edost_priceinfo_formatted');
		if (v) s += '<div style="padding-top: 5px;">' + self.param.priceinfo_warning_bitrix.replace('%price_info%', v) + '</div>';

		var v = edost.data(E_element, 'edost_error');
		if (v) error += '<div style="padding-top: 5px;">' + v + '</div>';

		if (self.data.warning) error += '<div style="padding-top: 5px;">' + self.data.warning + '</div>';

		E_info.innerHTML = (error != '' ? '<div style="padding-top: 5px; color: #F00; font-weight: bold; font-size: 12px;">' + error + '</div>' : '') + s;

		if (!self.order_create && update && self.param.edost_locations) self.update_location('address');

		// вывод адреса
		E_address.value = (address != undefined ? address : '');
		E_office.style.display = (address != undefined ? 'block' : 'none');

		if (E_location_address) E_location_address.style.display = (address != undefined && profile ? 'none' : 'block');

		if (address != undefined && profile) {
			if (address == 'new') s = edost.loading20;
			else {
				var ar = address.split(', код филиала: ');
				if (ar[1] == undefined)	s = '<b style="color: #F00;">Не выбрана точка самовывоза</b>';
				else {
					s = (office_id && office_detailed !== '' && office_detailed !== 'N' ? ' (<a class="edost_link" href="' + office_detailed + '" target="_blank">показать на карте</a>)' : '');
					s = '<b style="color: #00A;">' + ar[0].replace(': ', '</b>' + s + '<br>').replace(', телефон:', '<br>').replace(', часы работы:', '<br>');
					var code = ar[1].split('/');
					if (code[0] != '' && code[0] != 'S' && code[0] != 'T' && ar[0].indexOf('Почтовое отделение') == -1) s += '<br><b>код филиала: ' + code[0] + '</b>';
				}
				var v = edost.data(E_element, 'edost_office_mode');
				if (v) s += (s != '' ? '<br>' : '') + '<span style="cursor: pointer; color: #A00; font-size: 14px; font-weight: bold;" onclick="edost_office.window(\'' + v + '\');">' + (ar[1] == undefined ? 'выбрать...' : 'выбрать другой...') + '</span>';
			}
			E_office.innerHTML = s;

			if (office_id) {
				if (address == 'new') {
					// отправка на сервер выбранного пункта выдачи
					var e = BX('SHIPMENT_ID_1');
					if (e) self.post('ajax', 'mode=order_edit&id=' + e.value + '&office_id=' + office_id + '&profile=' + profile, function(r) {
						if (reload) edost.admin.reload = true;
						self.update_order();
					});
					return;
				}

				if (!(self.order_create && request)) return;
			}
		}

		if (office_window && self.office_open && edost.in_array(profile, edost.office.office_mode)) {
			request = false;
			edost_office.window(profile, true);
		}
		self.office_open = true;


		if (self.order_create && request) BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());

	}




	// интеграция данных отгрузки (проверка на возможность постановки на контроль)
	this.insert_control = function(param) {

		var shipment = [];
		var shipment_id = [];

		// поиск блоков с отгрузками
		if (self.param.crm) {
			var data = self.get_data_crm();
			for (var i = 0; i < data.shipment.length; i++) {
				var v = data.shipment[i];
				var delivery_id = data.shipment_id;
				shipment.push({'i': i, 'id': v.ID, 'allow_delivery': v.ALLOW_DELIVERY, 'tracking_number': edost.trim(v.TRACKING_NUMBER)});
				shipment_id.push(v.ID);
			}
		}
		else {
			for (var i = 1; i < 100; i++) {
				var e = BX('SHIPMENT_ID_' + i);
				if (!e) break;
				if (e.value != '') {
					var id = e.value;
					var e = BX('STATUS_ALLOW_DELIVERY_' + i); // BX('STATUS_DEDUCTED_' + i),  BX('STATUS_SHIPMENT_' + i);
					var e2 = BX('TRACKING_NUMBER_' + i + '_EDIT');
					shipment.push({i: i, id: id, allow_delivery: e ? e.value : '', tracking_number: e2 ? edost.trim(e2.value) : ''});
					shipment_id.push(id);
				}
			}

		}

		if (param === 'update') {
			if (control_start) control_start = false;
			else param = 'timer';
		}
		if (param === 'timer') {
			for (var i = 0; i < shipment_id.length; i++) self.draw_control(shipment_id[i], 'loading', '', '', i+1);
			window.setTimeout('edost.admin.insert_control()', 2500);
			return;
		}

		// проверка на изменение параметров
		var a = false;
		if (self.shipment === false || shipment.length != self.shipment.length) a = true;
		else for (var i = 0; i < shipment.length; i++)
			if (shipment[i].id != self.shipment[i].id || shipment[i].allow_delivery != self.shipment[i].allow_delivery || shipment[i].tracking_number != self.shipment[i].tracking_number) { a = true; break; }
		if (!a) return;

		self.shipment = shipment;
		if (shipment_id.length == 0) return;

		self.post('ajax', 'mode=control&id=' + shipment_id.join(','), function(r) {
			r = edost.json(r);

			for (var i = 0; i < shipment_id.length; i++) {
				var v = false, p = false;
				if (r.data) for (var i2 = 0; i2 < r.data.length; i2++) if (r.data[i2].id == shipment_id[i]) { v = r.data[i2]; break; }
				if (r.package) for (var i2 = 0; i2 < r.package.length; i2++) if (r.package[i2].id == shipment_id[i]) { p = r.package[i2]; break; }

				var index = i + 1;

				if (edost.admin.param.crm) {
					var e = document.querySelector('div[data-cid="SHIPMENT"]');
					var e2 = BX('edost_shipment_flag_start');
					if (e && !e2) e.appendChild( BX.create('input', {'props': {'type': 'hidden', 'name': 'edost_shipment_flag_start', 'value': v.flag != undefined ? v.flag : 0}}) );
				}
				else {
					if (p) {
						// скрыть оригинальное поле "PACKAGE"
						var s = document.querySelectorAll('div[data-id="buyer"] td.adm-detail-content-cell-r div');
						if (s) for (var i2 = 0; i2 < s.length; i2++) if (s[i2].innerHTML == p.prop) { BX.findParent(s[i2], {'tag': 'tr'}).style.display = 'none'; break; }
					}

					// открыть блок с подробной информацией по отгрузке на странице просмотра заказа + переход на отгрузку по якорю со страницы контроля заказов
					var e = BX('SHIPMENT_SECTION_' + index);
					if (e && e.style.display == 'none') {
						var a = false;
						var s = edost.get('edost_link').split('_');
						if (!s[1]) a = true; else if (v && s[1] == v.id || p && s[1] == p.id) a = 'scroll';
						if (a) {
							e.style.display = 'block';
							if (a === 'scroll') edost.scroll('shipment_container_' + index, {'top': 100, 'speed': 1});
							edost.D('SHIPMENT_SECTION_SHORT_' + index, false);
							edost.H('SHIPMENT_SECTION_' + index + '_TOGGLE', 'Свернуть');
						}
					}

					if (edost.admin.shipment_edit || edost.admin.order_create) {
						var e = BX('shipment_container_' + index);
						var e2 = BX('edost_shipment_flag_start');
						if (e && !e2) e.appendChild( BX.create('input', {'props': {'type': 'hidden', 'name': 'edost_shipment_flag_start', 'value': v.flag != undefined ? v.flag : 0}}) );
					}
				}

				edost.admin.control_data[index] = {'count': v.control_count ? v.control_count : 0, 'shop_id': v.shop_id ? v.shop_id : 0};
				edost.admin.draw_control(v.id, v.flag, v.control, v.status_full, index, v.register, p);

				if (v === false) continue;

				if (!edost.admin.param.crm && !edost.admin.shipment_edit && !edost.admin.order_create) edost.admin.set_tracking(v.tracking_example, v.tracking_format, index, false);
			}
		});

	}


	// проверка на заполнение полей при нажатии кнопки "Сохранить" и "Применить"
	this.check_prop = function(name) {

		var r = false;

		if (self.param.edost_locations) {
			var E_zip = BX('edost_zip');
			var E_country = BX('edost_country');
			var E_location = BX('edost_shop_LOCATION');
			var address_display = D('edost_location_address_div');

			if (E_location && E_location.value && E_zip && (E_zip.type != 'hidden' && address_display != 'none') && E_country && E_country.value.split('_')[0] == 0 && E_zip.value.replace(/ /g, '') == '' && !self.delivery_shop) r = "Не указан почтовый индекс!\nСохранение заказа невозможно!";
		}

		var e = BX('edost_address');
		if (e && e.value == '') {
			var e = BX('PROFILE_1');
			if (e && e.selectedIndex != -1) {
//				var s = e.options[e.selectedIndex].getAttribute('data-edost_address');
				var s = e.options[e.selectedIndex].getAttribute('data-edost_office_address_full');
				if (s != undefined && s == '') r = "Не выбрана точка самовывоза!\nСохранение заказа невозможно!";
			}
		}

		if (r) {
			alert(r);

			window.setTimeout(function(name) {
				var e = BX.findChild(document, {attribute: {'name': '' + name + ''}}, true);
				if (e) {
					e.disabled = false;
					e.classList.remove('adm-btn-load');
				}
				var e = BX.findChild(document, {attribute: {'class': name == 'apply' ? 'adm-btn-load-img' : 'adm-btn-load-img-green'}}, true);
				if (e) e.remove();
			}, 500, name);

			return false;
		}

		return true;

	}




	// для модуля местоположений --------------------
	this.update_delivery = function() {

		self.alarm = false;

		self.update_order();

		var location = BX('edost_shop_LOCATION').value;
		var city2 = BX('edost_city2');

		self.post('location', 'type=html&mode=city&ajax=Y&edost_delivery=Y&id=' + location + '&edost_city2=' + (city2 ? encodeURIComponent(city2.value) : ''), function(r) {
			BX('edost_location_city_div').innerHTML = r;
			BX('edost_location_city_loading').innerHTML = '';
			BX('edost_location_address_loading').innerHTML = '';
			var e = BX('edost_location_zip_hint'); if (e) e.innerHTML = '';
			var e = BX('edost_zip'); if (e) e.blur();

			if (edost.admin.param.crm) {
				var e = BX('edost_location_admin_city_div');
				if (e) edost.admin.param.edost_admin_field_city = e.innerHTML;
			}
		});

	}


	this.update_location = function(mode, reload, location) {

		if (mode === 'save') {
			self.shipment = false;

			var ar = ['address', 'passport'];
			for (var i2 = 0; i2 < ar.length; i2++) {
				var e = BX('edost_location_admin_' + ar[i2] + '_div');
				if (!e) continue;

				var s = e.querySelectorAll('input');
				var u = {};
				if (s) for (var i = 0; i < s.length; i++) if (s[i].name) u[s[i].name] = s[i].value;
				self.param['edost_admin_field_' + ar[i2] + '_input'] = u;
			}

			return;
		}


		if (self.delivery_shop === 'location_updated') return;
		if (self.delivery_shop === true && location === 0) self.delivery_shop = 'location_updated';

		var post = [];
		var ar = [['site_id', 'SITE_ID'], ['person_type', 'PERSON_TYPE_ID'], ['user_id', 'USER_ID'], ['profile_id', 'BUYER_PROFILE_ID']];
		for (var i = 0; i < ar.length; i++) {
			var e = BX(ar[i][1]);
			if (e) post.push(ar[i][0] + '=' + encodeURIComponent(e.value));
		}
		post = post.join('&');

//		console.log('update_location ' + mode + ' | ' + reload + ' | ' + location);
//		console.log(self.param);

		if (mode == 'address') {
			if (self.param.crm) {
				var data = self.get_data_crm();
				var delivery_id = data.profile_id;
			}
			else {
				var e = BX('PROFILE_1');
				var delivery_id = (e ? e.value : '');
			}

			if (location == undefined) location = BX('edost_shop_LOCATION').value;

			var ar = BX.findChildren(BX('edost_location_address_div'), {'tag': 'input'}, true);
			var prop2 = [];
			for (var i = 0; i < ar.length; i++) prop2.push(ar[i].name + '=' + encodeURIComponent(ar[i].value));

			post += (post != '' ? '&' : '') + (!reload ? 'ajax=Y&' : '') + 'delivery_id=' + delivery_id + '&' + prop2.join('&');
		}

		if (location == undefined) location = 0;

		self.post('location', 'type=html&admin=Y&mode=' + mode + '&edost_delivery=Y&id=' + location + '&' + post, function(r) {
			BX(mode == 'city' ? 'edost_location_admin_city_td' : 'edost_location_address_div').innerHTML = r;
			if (edost.admin.order_create && mode == 'city' && reload) BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());
			if (mode == 'address') {
				if (edost.admin.location_warning_timer != undefined) window.clearInterval(edost.admin.location_warning_timer);
				edost.admin.location_warning_timer = window.setInterval("edost.admin.update_location_warning()", 500);

				if (edost.admin.order_create) {
					var e = BX('edost_shop_LOCATION');
					var e2 = BX('edost_shop_LOCATION_CODE');
					if (e && e2) e.name = 'edost_shop_LOCATION';
				}
			}
		});

	}

	this.update_location_warning = function() {

		var street_required = E('edost_street_required');
		var area = E('edost_area');
		var a = (street_required && street_required.value != '' && area && area.value == 'Y' ? true : false);
		var e = E('edost_street_warning');

		if (!a && !e) return;

		if (!e) {
			var street = E('edost_street');
			if (!street) return;

			street.parentNode.parentNode.appendChild( BX.create('div', {'props': {'id': 'edost_street_warning', 'style': 'color: #F00; font-weight: bold; text-align: center; margin: 0 auto; padding-top: 5px; max-width: 500px;', 'innerHTML': 'улицу необходимо выбирать из списка с подсказками, иначе не будет работать проверка на удаленный регион и стоимость доставки может быть рассчитана некорректно'}}) );
			var e = E('edost_street_warning');
		}

		if (!e || a && e.style.display == 'block' || !a && e.style.display == 'none') return;

		e.style.display = (a ? 'block' : 'none');

	}

	this.insert_location = function(reload) {

		self.delivery_shop = false;

		if (!reload) {
			var e = BX('BLOCK_DELIVERY_SERVICE_1');
			if (e) e = BX.findParent(e, {'tag': 'div', 'class': 'adm-bus-pay-section-right'});

			if (!self.order_create) {
				var e2 = BX('edost_location_admin_city_div');
				if (e2) {
					var s = e2.innerHTML;
					BX.remove(e2);
					e.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_city_div', 'style': 'display: none;', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), e.children[0]);
				}
			}

			var e2 = BX('edost_location_admin_passport_div');
			if (e2) {
				var s = e2.innerHTML;
				BX.remove(e2);
				e.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_passport_div', 'style': !self.order_create ? 'display: none;' : '', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), e.children[2]);
			}

			var e2 = BX('edost_location_admin_address_div');
			if (e2) {
				var s = e2.innerHTML;
				BX.remove(e2);
				e.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_address_div', 'style': !self.order_create ? 'display: none;' : '', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), e.children[2]);
			}

			if (!self.order_create) return;
		}

		var prop_div = BX('order_properties_container');

		if (!reload) {
			var E_location = false;
			for (var i = 0; i < self.param.prop_location.length; i++) {
				var e = BX.findChild(prop_div, {'attribute': {'name': 'PROPERTIES[' + self.param.prop_location[i] + ']'}}, true);
				if (e != undefined) { E_location = e; break; }
			}
			if (!E_location) {
				window.setTimeout('edost.admin.insert_location(' + (reload ? 'true' : 'false') + ')', 100);
				return;
			}
		}

		var e = BX.findParent(prop_div);

		if (reload) {
			self.update_location('address', true);
			self.update_location('city', true);
		}
		else {
			var e2 = BX('edost_location_admin_city_div');
			var s = e2.innerHTML;
			BX.remove(e2);
			e.insertBefore( BX.create('div', {'props': {'id': 'edost_location_admin_city_div', 'className': 'adm-bus-table-container caption border', 'innerHTML': s}}), BX.findNextSibling(prop_div));
		}

		// удаление полей битрикса
		for (var i = 0; i < self.param.prop_remove.length; i++) {
			var e = BX.findChild(prop_div, {'attribute': {'name': 'PROPERTIES[' + self.param.prop_remove[i] + ']'}}, true);
			if (!e) continue;
			e = BX.findParent(e, {'tag': 'tr'});
			if (!e) continue;

			var e2 = BX.findParent(e);
			BX.remove(e);
			if (e2.children.length == 0) {
				var e = BX.findParent(e2);
				if (e) BX.remove(e);
			}
		}

	}
// для модуля местоположений --------------------




	this.get_data_crm = function(mode) {

		var r = {};

		var id = BX.Crm.EntityEditor.defaultInstance._id;
		var c = BX.Crm.EntityEditor.items[id];


		if (c._settings.model._data.SHIPMENT) {
			r.shipment = c._settings.model._data.SHIPMENT;
			r.view = true;
		}
		else {
			c = c._activeControls;

			var a = false;
			for (var i = 0; i < c.length; i++) if (c[i]._id == "DELIVERY_ID" || c[i]._id == "main" || c[i]._id == "PRICE_DELIVERY_WITH_CURRENCY") { c = c[i]; a = true; break; }
			if (!a) r.view = true;
			else {
				if (self.changed) BX.Crm.EntityEditor.items[id]._activeControls[0]._isChanged = true;
				self.changed = true;

				r.id = id;
				r.c = c;

				r.delivery_id = c._settings.model._data.DELIVERY_SELECTOR_DELIVERY_ID;
				r.profile_id = c._settings.model._data.DELIVERY_SELECTOR_PROFILE_ID;
				r.price = c._settings.model._data.PRICE_DELIVERY;

				r.ALLOW_DELIVERY = (c._settings.model._data.ALLOW_DELIVERY == 'Y' ? 'Y' : '');
				r.TRACKING_NUMBER = c._settings.model._data.TRACKING_NUMBER;

				r.edost_active = (edost.in_array(r.delivery_id, self.param.edost_id) ? true : false);
				r.shipment_id = BX.Crm.EntityEditor.defaultInstance._settings.model._settings.data.ID;

				if (mode === 'full') {
					r.delivery_input = document.querySelector('input[name="DELIVERY_ID"]');
					if (r.delivery_input) {
						r.delivery_select = r.delivery_input.parentNode.querySelector('select');
						if (r.delivery_select && r.edost_active) {
							var e = document.querySelector('#bitrix_profile_select');
							if (!e) e = r.delivery_select.parentNode.parentNode.nextSibling.querySelector('select');
							r.profile_select = e;
							r.edost_profile_select = document.querySelector('#edost_profile_select');
						}
					}

					r.price_calculated_div = document.querySelector('div[data-cid="PRICE_DELIVERY_CALCULATED_WITH_CURRENCY"]');

					var e = document.querySelector('div[data-cid="PRICE_DELIVERY_WITH_CURRENCY"]');
					if (e) r.price_span = e.querySelector('span.crm-entity-widget-content-block-colums-right');

					r.price_input = document.querySelector('input[name="PRICE_DELIVERY"]');

				}
			}
		}

		return r;

	}


	this.set_block_crm = function(e, position, id, html) {

		var data = {
			'city': {
				'head': 'Местоположение',
				'style': '#edost_location_city_div { padding-bottom: 8px; } #edost_city, input.edost_input { display: inline-block; padding: 10px 9px; width: 100%; border: 1px solid #c4c7cc; border-radius: 1px; color: #424956; font: 15px/17px "Helvetica Neue",Helvetica,Arial,sans-serif; box-sizing: border-box; }',
				'get_html': true
			},
			'payment': {
				'head': 'Способ оплаты',
			},
			'info': {
				'head': '',
			},
			'passport': {
				'head': 'Паспортные данные',
				'get_html': true
			},
			'address': {
				'head': 'Адрес доставки',
				'get_html': true
			},
		};

		data = data[id];


		var id2 = id;
		id = 'edost_admin_field_' + id;

		if (data.get_html) {
			if (self.param[id] === undefined) {
				var e2 = BX('edost_location_admin_' + id2 + '_div');
				if (e2) {
					self.param[id] = '<span id="edost_location_admin_' + id2 + '_div">' + e2.innerHTML + '</span>';
					BX.remove(e2);
				}
			}
			if (self.param[id] === undefined) return;
			html = self.param[id];
		}

		if (BX(id)) return;

		var s = '<span id="' + id + '" class="fields enumeration field-wrap" ' + (data.head != '' ? 'style="margin-top: 20px;"' : '') + '>' +
			(data.style ? '<style>' + data.style + '</style>' : '') +
			'<div class="crm-entity-widget-content-block-title" style="margin-bottom: 2px;"><span class="crm-entity-widget-content-block-title-text">' + data.head + '</span></div>' +
			'<span class="fields enumeration field-item">' + html + '</span>' +
			'</span>';

		e.insertAdjacentHTML(position, s)

		// восстановление адресных полей с базовых на данные полученные после нажатия кнопки "сохранить"
		if ((id2 == 'address' || id2 == 'passport') && self.param['edost_admin_field_' + id2 + '_input']) {
			var s = self.param['edost_admin_field_' + id2 + '_input'];
			for (var k in s) {
				e = BX(k);
				if (e) e.value = s[k];
			}
		}

	}

	this.insert_crm = function(param, profile_id) {

		if (param == 'new' || param == 'update') self.delivery_id = false;

		var data = self.get_data_crm();

		if (data.shipment) {
			self.insert_control();

			// удаление полей битрикса
			for (var i = 0; i < self.param.prop_remove.length; i++) {
				var e = document.querySelector('div[data-cid="PROPERTY_' + self.param.prop_remove[i] + '"]');
				if (!e) continue;
				if (e.style.display == 'none') break;
				e.style.display = 'none';
			}

			return;
		}
		else if (data.view) {
			// режим просмотра доставки
			edost_SetTariffNameCRM();
			self.delivery_id = false;
			return;
		}


		if (self.delivery_id !== false && self.delivery_id == data.delivery_id && self.CRMEditor == data.c._id) return;
		self.delivery_id = data.delivery_id;
		self.CRMEditor = data.c._id;

		var e = BX('edost_profile_select');
		if (e) return;

		var data = self.get_data_crm('full');
		if (!data.delivery_input || !data.delivery_select) return;

		// замена названия доставки на "eDost"
		for (var i = 0; i < data.delivery_select.options.length; i++) if (edost.in_array(data.delivery_select.options[i].value, self.param.edost_id)) {
			data.delivery_select.options[i].innerHTML = 'eDost';
			data.delivery_select.options[i].id = 'edost_delivery_option';
			break;
		}

		if (!data.edost_active || !data.profile_select) {
			self.alarm = false;

			var ar = ['city', 'address', 'passport', 'payment', 'info'];
			for (var i = 0; i < ar.length; i++) {
				var e = E('edost_admin_field_' + ar[i]);
				if (e) e.remove();

			}
			return;
		}

		// получение отформатированных тарифов edost
		self.post('ajax', 'mode=order_edit&id=' + data.shipment_id, function(r) {
			var data = edost.admin.get_data_crm('full');
			if (!data.edost_active) return;

			var price = data.price;

			if (data.profile_id != 0) {
				var e = data.profile_select.options[data.profile_select.selectedIndex];
				var profile_name = e.text;
			}

			V('edost_office_data', r);

			self.data = (window.JSON && window.JSON.parse ? JSON.parse(r) : eval('(' + r + ')'));
//			console.log(self.data);

			// формирование списка с тарифами
			var select = edost.admin.tariff_select(self.data, profile_id ? profile_id : data.profile_id, true);
			data.profile_select.parentNode.appendChild( BX.create('select', {'props': {'id': 'edost_profile_select', 'name': 'edost_profile_select', 'onchange': new Function('', 'edost.admin.change_delivery_crm(true)'), 'innerHTML': select.option}}) );
			data.profile_select.style.display = 'none';
			data.profile_select.id = 'bitrix_profile_select';

			// перевод названия заголовка на русский
			var e = data.profile_select.parentNode.parentNode.querySelector('div.crm-entity-widget-content-block-title span.crm-entity-widget-content-block-title-text');
			if (e && e.innerHTML == 'deliveryProfile') e.innerHTML = 'Тариф';

			var e = data.profile_select;

			// выбор способа оплаты
			var cod = '';
			if (edost.admin.param.payment_select) {
				edost.admin.set_block_crm(e.parentNode.parentNode, 'afterend', 'payment', edost.admin.param.payment_select.replace('edost.admin.change_delivery()', 'edost.admin.change_delivery_crm(true)'));
				var e = E('edost_payment');
				if (e && edost.data(e.options[e.selectedIndex], 'edost_cod') == 'Y') cod = true;
			}

			var e2 = BX('edost_admin_field_payment');
			e = (e2 ? e2 : e.parentNode.parentNode);

			// дополнительные поля edost.locations
			if (edost.admin.param.edost_locations) {
				edost.admin.delivery_shop = false;

				var E_select = BX('edost_profile_select').parentNode.parentNode;
				edost.admin.set_block_crm(E_select, 'beforebegin', 'city');

				if (e2) E_select = e2;
				edost.admin.set_block_crm(E_select, 'afterend', 'passport');
				edost.admin.set_block_crm(E_select, 'afterend', 'address');
			}

			// поле для адреса пункта выдачи + дополнительная информация (error, warning, наценки за наложку, отформатированный адрес пункта выдачи)
			var s = '';
			s = '<input id="edost_post_manual" name="edost_post_manual" type="hidden" value="">';

			var e2 = document.querySelector('input[name="CUSTOM_PRICE_DELIVERY"]');
			if (!e2) s += '<input name="CUSTOM_PRICE_DELIVERY" type="hidden" value="Y">';

			s += '<input id="edost_address" name="edost_address" type="hidden" value="">';
			if (!edost.admin.param.edost_locations) s += '<div id="edost_office_address"></div>';
			s += '<div id="edost_delivery_info"></div>';
			s += self.set_alarm(data, profile_id ? profile_id : data.profile_id, select, profile_name, price, cod, '<div id="edost_alarm" style="padding-top: 10px; color: #e60;">');
			edost.admin.set_block_crm(e, 'afterend', 'info', s);

			edost.admin.change_delivery_crm(param == 'update' ? true : false);
		});

	}


	this.update_order = function() {

		if (!self.param.crm) {
			if (self.order_create) BX.Sale.Admin.OrderAjaxer.sendRequest(BX.Sale.Admin.OrderEditPage.ajaxRequests.refreshOrderData());
			else BX.Sale.Admin.OrderShipment.prototype.getDeliveryPrice();
		}
		else {
			var data = self.get_data_crm('full')
			data.edost_profile_select.remove();

			var s = document.createEvent('HTMLEvents');
			s.initEvent('change', true, true);
			data.delivery_select.dispatchEvent(s);

			self.insert_crm('update', data.profile_id);
		}

	}


	// выбор тарифа в выпадающем списке
	this.change_delivery_crm = function(update) {

		var data = self.get_data_crm('full');
		if (!data.edost_active) return;

		var cod = false;
		var e = E('edost_payment');
		if (e && edost.data(e.options[e.selectedIndex], 'edost_cod') == 'Y') cod = true;

		var e = data.edost_profile_select;
		if (!e) return;
		e = e.options[e.selectedIndex];
		var E_element = e;
		var company_id = edost.data(e, 'edost_company_id');
		var profile = edost.data(e, 'edost_profile');
		var price = edost.data(e, 'edost_price');
		var pricecash = edost.data(e, 'edost_pricecash');
//		var address = edost.data(e, 'edost_address');
		var address = edost.data(e, 'edost_office_address_full');
		var office_id = edost.data(e, 'edost_office_id');
		var office_detailed = edost.data(e, 'edost_office_detailed');

		var tariff_id = (profile ? Math.ceil(profile/2) : 0);
		if (cod) price = (pricecash != undefined ? pricecash : 0);

		var E_address = BX('edost_address');

		if (update) {
			self.alarm = false;

			BX.Crm.EntityEditor.items[data.id]._activeControls[0]._isChanged = true;
			self.changed = true;

			price = price.toString();

			data.c._settings.model._data.DELIVERY_SELECTOR_PROFILE_ID = data.delivery_input.value = data.edost_profile_select.value;

			var price_formatted = BX.Currency.Editor.getFormattedValue(price, BX.Crm.EntityEditor.items[data.id].getModel().getField('CURRENCY', ''));

			if (data.price_calculated_div) data.price_calculated_div.style.display = "none";

			var c = BX.Crm.EntityEditor.items[data.id];
			c._model._data.PRICE_DELIVERY = price;
			c._model._data.FORMATTED_PRICE_DELIVERY = price_formatted;

			if (data.price_span) data.price_span.innerHTML = price_formatted + '<input name="PRICE_DELIVERY" type="hidden" value="' + price + '">';
			else if (data.price_input) {
				var e2 = data.price_input.nextSibling;
				if (e2 && e2.tagName == 'INPUT') {
					data.price_input.value = price;
					e2.value = price_formatted;
				}
			}
		}

		var e = document.querySelector('input[name="DELIVERY_ID"]');
		if (!e) return;

		var id = BX.Crm.EntityEditor.defaultInstance._id;
		var shipment_id = BX.Crm.EntityEditor.defaultInstance._settings.model._settings.data.ID;
		var order_id = BX.Crm.EntityEditor.defaultInstance._settings.model._settings.data.ORDER_ID
		var site_id = BX.Crm.EntityEditor.defaultInstance._settings.model._settings.data.SITE_ID

		var e = document.querySelector('input[name="DELIVERY_ID"]');
		if (!e) return;

		var E_office = BX('edost_office_address');
		var E_info = BX('edost_delivery_info');
		var E_alarm = BX('edost_alarm');

		var E_admin_city = BX('edost_admin_field_city');
		var E_admin_address = BX('edost_admin_field_address');
		var E_admin_passport = BX('edost_admin_field_passport');
		var E_location_address = BX('edost_location_address_div');

		if (E_admin_passport) {
			var a = false;
			for (var i = 0; i < self.param.passport_required.length; i++) if (tariff_id == self.param.passport_required[i]) { a = true; break; }
			E_admin_passport.style.display = (a ? 'block' : 'none');
		}


		if (!self.alarm && E_alarm) E_alarm.style.display = 'none';

		self.set_ico(tariff_id, company_id);

		// вывод error, warning, pricecash и priceinfo
		var s = '';
		var error = '';

		if (cod) {
			if (pricecash != undefined) {
				var v = edost.data(E_element, 'edost_transfer_formatted');
				if (v) s += '<div style="padding-top: 5px; color: #F00;">' + self.param.sign_transfer.replace('%transfer%', v) + '</div>';
			}
			else if (profile > 0) error += '<span style="padding: 2px 8px; background: #F00; color: #FFF;">Для выбранного способа доставки наложенный платеж недоступен!!!</span>';
		}

		var v = edost.data(E_element, 'edost_day');
		if (v) s += '<div style="padding-top: 5px;"><span style="color: #888;">Срок доставки:</span> ' + v + '</div>';

		var v = edost.data(E_element, 'edost_priceinfo_formatted');
		if (v) s += '<div style="padding-top: 5px;">' + self.param.priceinfo_warning_bitrix.replace('%price_info%', v) + '</div>';

		var v = edost.data(E_element, 'edost_error');
		if (v) error += '<div style="padding-top: 5px;">' + v + '</div>';

		if (self.data.warning) error += '<div style="padding-top: 5px;">' + self.data.warning + '</div>';

		if (E_info) E_info.innerHTML = (error != '' ? '<div style="padding-top: 5px; color: #F00; font-weight: bold; font-size: 12px;">' + error + '</div>' : '') + s;

		if (!self.order_create && self.param.edost_locations) self.update_location('address');

		// вывод адреса
		E_address.value = (address != undefined ? address : '');
		if (E_office) E_office.style.display = (address != undefined ? 'block' : 'none');

		if (E_location_address) E_location_address.style.display = (address != undefined && profile ? 'none' : 'block');
		if (address != undefined && profile) {
			if (address == 'new') s = edost.loading;
			else {
				var ar = address.split(', код филиала: ');
				if (ar[1] == undefined)	s = '<b style="color: #F00;">Не выбрана точка самовывоза</b>';
				else {
					s = (office_id && office_detailed !== '' && office_detailed !== 'N' ? ' (<a class="edost_link" href="' + office_detailed + '" target="_blank">показать на карте</a>)' : '');
					s = '<b style="color: #00A;">' + ar[0].replace(': ', '</b>' + s + '<br>').replace(', телефон:', '<br>').replace(', часы работы:', '<br>');
					var code = ar[1].split('/');
					if (code[0] != '' && code[0] != 'S' && code[0] != 'T' && ar[0].indexOf('Почтовое отделение') == -1) s += '<br><b>код филиала: ' + code[0] + '</b>';
				}
				var v = edost.data(E_element, 'edost_office_mode');
				if (v) s += (s != '' ? '<br>' : '') + '<span style="cursor: pointer; color: #A00; font-size: 14px; font-weight: bold;" onclick="edost_office.window(\'' + v + '\');">' + (ar[1] == undefined ? 'выбрать...' : 'выбрать другой...') + '</span>';
			}
			if (E_office) E_office.innerHTML = s;

			if (office_id) {
				if (address == 'new') {
					// отправка на сервер выбранного пункта выдачи
					self.post('ajax', 'mode=order_edit&id=' + data.shipment_id + '&office_id=' + office_id + '&profile=' + profile, function(r) {
						if (reload) edost.admin.reload = true;
						edost.admin.update_order();
					});
					return;
				}
				if (!(self.order_create && request)) return;
			}
		}

		var reload = false;
		if (update === 'reload') {
			update = true;
			reload = true;
		}

		var request = false;
		if (update == undefined) {
			update = true;
			request = true;
			self.office_open = true;
		}

		var office_window = true;
		if (update == 'office_esc') {
			update = false;
			office_window = false;
		}

		if (office_window && self.office_open && edost.in_array(profile, edost.office.office_mode)) {
			request = false;
			edost_office.window(profile, true);
		}
		self.office_open = true;

	}

}




edost.package = new function() {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C
	var item, box, item_mode, E_ajax, input_timer, E_input, input_value, input_active, input_position, scroll = [0, 0];
	var input_function = 'type="input" onfocus="edost.package.input_focus(this)" onblur="edost.package.input_blur()" onkeydown="edost.package.input_keydown(this, event)"';
	var mouse = {'move': false, 'checkbox': false};
	var edost_package_onkeydown_backup = 'free', edost_package_overflow_backup = 'free', edost_package_onresize_backup = 'free';

	this.id = ''
	this.tariff = ''

	// загрузка параметров при первом открытии
	this.open = function(id) {
		self.id = id;

		var e = BX('edost_package_error_' + self.id.split('_')[1]);
		self.tariff = (e ? edost.data(e, 'tariff') : false);

		var e = E('edost_package_' + id + '_item_data');
		if (e) {
			item = (window.JSON && window.JSON.parse ? JSON.parse(e.value) : eval('(' + e.value + ')'));
			for (var i = 0; i < item.length; i++) {
				item[i].hide = (item[i].hide == 1 ? true : false);
				item[i].value = [];
				item[i].remain = item[i].QUANTITY;
			}
		}

		var e = E('edost_package_' + id + '_box_data');
		if (e) {
			box = [{"item": []}];

			ar = e.value.split(':');
			var n = ar.length;
			for (var i = 0; i < n; i++) {
				s = ar[i].split(',');
				var v = {"shipment_id": s[0], "weight": s[1], "size": s[2].split('x'), "insurance": s[3], "cod": s[4], "item": []};

				var u = s[5].split('/')
				for (var i2 = 0; i2 < u.length; i2++) {
					s = u[i2].split(';');
   	                v.item.push({"id": s[0], "quantity": s[1]});
				}

				if (n == 1) box[0] = v;
				else box.push(v);
			}

			for (var i = 0; i < box.length; i++)
				for (var i2 = 0; i2 < box[i].item.length; i2++) {
					var v = box[i].item[i2];
					for (var i3 = 0; i3 < item.length; i3++) if (item[i3].ID == v.id) {
						box[i].item[i2] = [i3, v.quantity, v.quantity];

						item[i3].value.push([i, v.quantity]);
						item[i3].remain -= v.quantity;
					}
				}

			for (var i = 0; i < item.length; i++) {
				var v = item[i];
				if (v.remain > 0) {
					var n = v.value.length - 1;
					if (v.remain == 1) {
						if (n > 0 && v.value[n][1] == 0) v.value[n][1] = 1;
						else v.value.push([0, 1]);
						v.remain = 0;
					}
					else if (n < 0 || v.value[n][1] != 0) v.value.push([0, 0]);
				}
				if (v.value.length == 0) v.value = [[0, v.QUANTITY == 1 ? 1 : 0]];
			}

			for (var i = 0; i < item.length; i++) if (!item[i].hide && item[i].remain > 0) box[0].item.push([i, item[i].remain, item[i].remain]);

			if (item_mode === undefined) item_mode = (edost.admin.param.cookie.register_package_item == 'Y' ? true : false);

//			self.update_box('start');
		}

		self.window(true);
	}

	// сохранение
	this.save = function(data, param) {
		var draw_package = false;

		if (data == undefined) {
			// места
			draw_package = true;

			for (var i = 1; i < box.length; i++) if (box[i] && !box[i].hide && box[i].error) return;

			var option = false;
			var id = self.id.split('_')[1];
			var order_id = self.id.split('_')[0];

			var e = document.querySelector('#edost_option_' + id + ' .edost_service_button');
			if (e) {
				var p = edost.window.get_param(e);
				if (p.service && p.service.length != 0) option = p.service.join(',');
			}

			var s = [];
			for (var i = 1; i < box.length; i++) if (box[i] && !box[i].hide && box[i].item.length > 0) {
				var s2 = [];
				for (var i2 = 0; i2 < box[i].item.length; i2++) s2.push(item[ box[i].item[i2][0] ].ID + ';' + box[i].item[i2][1]);
				s.push(id + ',' + box[i].weight + ',' + box[i].size.join('x') + ',,,' + s2.join('/'));
			}
            data = s.join(':');

			E_ajax = E('edost_package_' + id);
		}
		else {
			// опции
			var option = 'Y';
			var id = param.id;
			var order_id = param.order_id;
			E_ajax = E('edost_option_' + id);
		}

		if (!E_ajax) return;

		E(E_ajax, {'html': edost.loading20, 'height': E_ajax.offsetHeight + 'px'});
		edost.admin.post('ajax', 'mode=package&order_id=' + order_id + '&id=' + id + '&data=' + encodeURIComponent(data) + (option ? '&option=' + option : ''), function(r) {
			E(E_ajax, {'html': r, 'height': 'auto'});
			edost.register.active_all_update();
		});

		if (draw_package) self.window(false);
	}

	// изменение режима распределения: по местам / по списку
	this.mode = function(value, value2) {
		if (value == 'update') {
			var e = E('edost_package_setting_item');
			e.className = (item_mode ? 'edost_button_big_on' : 'edost_button_big_off');
			if (value2 == 'start') return;
			self.window(true);
		}
		else {
			item_mode = (value == 'item' ? true : false);
			edost.cookie(['admin', 'register_package_item'], value == 'item' ? 'Y' : 'N');
			self.mode('update');
		}
	}

	this.resize = function(value, value2) {

		var browser_w = (edost.browser('w') == 0 ? document.body.clientWidth : edost.browser('w'));
		var browser_h = (edost.browser('h') == 0 ? document.body.clientHeight : edost.browser('h'));

		browser_width = browser_w;
		browser_height = browser_h;

		window_w = 1000;
		window_h = (browser_h > 1100 ? 1000 : 800);

		var fullscreen = false;
		if (window_w > browser_w-100 || window_h > browser_h-100) {
			fullscreen = true;
			window_w = browser_w;
			window_h = browser_h;
		}

		document.body.style.overflow = (fullscreen ? 'hidden' : edost_package_overflow_backup);

		var e = E('edost_package_window');
		e.style.width = window_w + 'px';
		e.style.height = window_h + 'px';
		e.style.left = Math.round((browser_w - window_w)*0.5) + 'px';
		e.style.top = Math.round((browser_h - window_h)*0.5) + 'px';
		e.style.borderRadius = (fullscreen ? 0 : '8px');

		var E_item = E('edost_package_window_item', {'display': item_mode, 'height': 'auto'});
		D('edost_package_window_item_head', item_mode);

		var h = 0;
		var h0, h1, h2;

		var e2 = E('edost_package_window_box');
		if (item_mode) h = Math.round(e.offsetHeight - E_item.getBoundingClientRect().top + e.getBoundingClientRect().top - 20);
		else h = Math.round(e.offsetHeight - e2.getBoundingClientRect().top + e.getBoundingClientRect().top - 20);

		E(['div.edost_window_close'], {
			'position': 'absolute',
			'margin': (fullscreen ? '8px 8px 0 1px' : '4px 4px 0 1px'),
			'left': window_w - (fullscreen ? 32 : 29) + 'px'
		});

		var E_button = E('edost_package_window_button');
		D(E_button, item_mode && H(E_button) != '' ? true : false);

		var E_box = E('edost_package_box_div');
		E_box.style.width = (browser_w > 1050 && !fullscreen ? '50%' : '490px');
		E_box.style.height = 'auto';
		E_box.style.display = (item_mode ? 'none' : 'inline-block');
		if (scroll[0] > 0) E_box.scrollTop = scroll[0];

		if (item_mode) {
			h0 = E_item.offsetHeight;
			if (h0 > h*0.5) h0 = Math.round(h*0.5);
			h2 = h - h0 - E_button.offsetHeight - 40;
			E_item.style.height = h0 + 'px';
		}
		else {
			if (browser_w > 1050) h1 = h2 = h;
			else {
				h1 = E_box.offsetHeight;
				if (h1 > h/2) h1 = Math.round(h/2);
				h2 = h - h1;
			}
			E_box.style.height = h1 + 'px';
		}
		var e = E('edost_package_box_div_in');
		e.style.marginRight = (E_box.scrollHeight == E_box.offsetHeight ? '17px' : '');
		e.style.marginLeft = '17px';

		var E_box2 = E('edost_package_box_div2');
		var w2 = (browser_w > 1050 && !fullscreen ? '50%' : '490px');
		E_box2.style.width = (item_mode ? '100%' : w2);
		E_box2.style.height = h2 + 'px';

		if (scroll[1] > 0) E_box2.scrollTop = scroll[1];

		var e = E('edost_package_box_div2_in');
		e.style.marginRight = (E_box2.scrollHeight == E_box2.offsetHeight ? '17px' : '');
		e.style.marginLeft = '17px';

		var a = (browser_w > 600 ? true : false);
		C('edost_package_window_head_div_right', a ? 'edost_package_window_head edost_package_window_head_delimiter' : 'edost_package_window_head2');
		C('edost_package_window_head_div_left', a ? 'edost_package_window_head' : '');

		scroll = [0, 0];

	}


	// сброс распределения
	this.clear = function(value, value2) {
		for (var i = 0; i < item.length; i++) if (item[i] && (value === undefined || i == value)) {
			v = item[i];
			v.value = [[0, 0]];
			v.remain = v.QUANTITY;
		}
		self.window(true);
	}

	// разбить товар по местам
	this.cut = function(value, value2) {
		v2 = value.split('|');
		value = v2[0];
		value2 = v2[1]*1;

		v = item[value];
           if (value2 > v.remain*1) value2 = v.remain;

		var n = Math.floor(v.remain / value2);
		for (var i = 0; i < v.value.length; i++) if (v.value[i][1] == 0) { v.value[i][1] = value2; n--; }
		if (n > 0) for (var i = 0; i < n; i++) v.value.push([0, value2]);

		var n = v.remain % value2;
		if (n != 0)	v.value.push([0, n]);

		v.remain = 0;

		self.window(true);
	}

	// установить вес товаров в поле для веса коробки
	this.set_weight = function(value, value2) {
		v = box[value];
		v.weight = v.item_weight;

		self.window(true);
	}

	// поместить оставшиеся товары в свободную ячейку
	this.remain = function(value, value2) {
		if (item[value] && item[value].remain > 0) {
			v = item[value];
			for (var i = 0; i < v.value.length; i++) if (v.value[i][1] == 0) {
				v.value[i][1] = v.remain;
				v.remain = 0;
				break;
			}
		}

		self.window(true);
	}

	// поместить оставшиеся товары в место
	this.put = function(value, value2) {
		value = value.split('|')[1];
		for (var i = 0; i < item.length; i++) {
			v = item[i];
			if (!v.hide && v.remain > 0) {
				var a = false;
				for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0 && v.value[i2][1] == 0) { a = i2; break; }
				if (a === false) a = v.value.length - 1;
				v.value[a] = [value, v.remain];
				v.remain = 0;
			}
		}

		self.window(true);
	}

	// поместить товары у которых задано количество в место
	this.put2 = function(value, value2) {
		value = value.split('|')[1];
		for (var i = 0; i < item.length; i++) {
			var v = item[i];
			if (!v.hide) for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0 && v.value[i2][1] != 0) v.value[i2][0] = value;
		}
		self.window(true);
	}

	// разместить оставшиеся товары по указанным местам
	this.put3 = function(value, value2) {
		for (var i = 0; i < item.length; i++) {
			var v = item[i];
			if (!v.hide) for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] != 0 && v.value[i2][1] == 0) {
				v.value[i2][1] = v.remain;
				v.remain = 0;
				break;
			}
		}

		self.window(true);
	}

	// переложить из одного места в другое
	this.put4 = function(value, value2) {
		s = value.split('|');
		var f = s[0];
		var t = s[1];
		var u = (s[2] != undefined ? s[2]*1 : false);

		var v = box[f];
		for (var i = 0; i < v.item.length; i++) if (u === false || i === u) {
			var E_active = E('box_' + f + '_active_' + i);

			var checked = E_active.checked;
			if (u === false && !checked && t != f) continue;
			var count = (!checked && t == f ? 0 : v.item[i][1]*1);
			if (count > v.item[i][2]*1) count = v.item[i][2]*1;

			var v2 = item[ v.item[i][0] ];
			var ar = [];
			var n = 0; // товаров в остальных местах
			var w = 0; // товаров в месте "куда"
			for (var i2 = 0; i2 < v2.value.length; i2++)
				if (v2.value[i2][0] == 0 || v2.value[i2][0] == f || v2.value[i2][0] == t) {
					if (t != 0 && v2.value[i2][0] == t) w += v2.value[i2][1]*1;
				}
				else {
					n += v2.value[i2][1]*1;
					ar.push(v2.value[i2]);
				}

			if (t == f) w = 0;
			if (t > 0 && count != 0) {
				ar.push([t, count + w]);
				n += w;
			}
			if (f > 0 && count < v.item[i][2]*1) {
				ar.push([t != f ? f : 0, v.item[i][2] - count]);
				n += v.item[i][2] - count;
			}

			s = n + w + (t != 0 ? count : 0);
				if (t != f && s < v2.QUANTITY*1) ar.push([0, 0]);

			v2.value = ar;
		}

		self.window(true);
	}

	// разнести по местам начиная с
	this.put5 = function(value, value2) {
		var s = value.split('|');
		var v = item[s[0]];
		var n = s[1];
		for (var i = 0; i < v.value.length; i++) if (v.value[i][0] == 0) {
			v.value[i][0] = n;
			n++;
		}

		self.window(true);
	}

	// разделить/собрать комплект
	this.pack = function(value, value2) {
		for (var i = 0; i < item.length; i++) {
			v = item[i];
			var a = (!value2 ? true : false);
			if (v.ID == value) v.hide = !a;
			if (v.SET_PARENT_ID == value) v.hide = a;
		}

		self.window(true);
	}


	// обновление кнопок и выделение ячеек зеленым/красным в списке товаров
	this.update_item_list = function(value, value2) {
		for (var i = 0; i < item.length; i++) if (!item[i].hide && (value == undefined || value == i)) {
			var v = item[i];

			var e = E('edost_package_item_' + i + '_button');
			e.innerHTML = self.draw_button(i);

			var c = 'on', count_green = false;
			if (v.remain < 0) c = 'error'
			else if (v.remain == 0) {
				c = 'green';
				count_green = true;
			}
			for (var i2 = 0; i2 < v.value.length; i2++) {
				var e2 = E('edost_package_input_' + i + '_count_' + i2);
				if (e2) e2.className = 'edost_package_' + (count_green && v.value[i2][1] == 0 ? 'error' : c);
			}

			var a = true;
			for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0) a = false;
			var c = (a && v.remain == 0 ? 'green' : 'on');
			for (var i2 = 0; i2 < v.value.length; i2++) {
				var e2 = E('edost_package_input_' + i + '_box_' + i2);
				if (e2) e2.className = 'edost_package_' + (v.value[i2][0] > 100 || count_green && v.value[i2][1] == 0 ? 'error' : c);
			}
		}
	}

	// добавление/удаление ячеек в списке товаров
	this.update_item = function(value, value2) {
		var code = (value ? value.getAttribute('data-code').split('_') : false);
		var v = (code !== false ? item[code[0]] : false);

		if (code[1] == 'count') {
			var count = v.value[code[2]][1];

			var n = v.QUANTITY;
			var a = true;
			for (var i = 0; i < v.value.length; i++) {
				if (v.value[i][1] == 0) a = false;
				n -= v.value[i][1];
			}
			v.remain = n;

			var e = E_input.parentNode.parentNode.parentNode;

			var e2 = BX.findNextSibling(e);
			if (e2) {
				e2.innerHTML = edost.digit(v.remain);
				e2.style.color = (v.remain < 0 ? '#F00' : '#000');
				e2.style.cursor = (v.remain == 0 ? 'default' : 'pointer');
			}

			if (count != 0 && n > 0 && a) {
				v.value.push([0, 0]);

				var e2 = document.createElement('DIV');
				e2.style = 'padding: 2px 0;';
				e2.innerHTML = self.draw_item_input(code[0] + '|' + (v.value.length-1));
				e.appendChild(e2);
			}
		}

		// удаление пустых ячеек
		var ar = [];
		for (var i = 0; i < v.value.length; i++) {
			if (v.value[i][0] == 0 && v.value[i][1] == 0 && (v.remain == 0 || i != 0 && v.value[i][0] == 0 && v.value[i][1] == 0 && v.value[i-1][0] == 0 && v.value[i-1][1] == 0)) {
				var e = BX('edost_package_input_' + code[0] + '_count_' + i);
				if (e) {
					if (e.id == document.activeElement.id) self.input_keydown(e, {'keyCode': 38});
					e.parentNode.parentNode.remove();
				}
			}
			else ar.push(v.value[i]);
		}
		v.value = ar;

		self.update_item_list();
		self.update_save();
		self.window('update_box');
	}


	// вывод кнопки "сохранить"
	this.update_save = function(value, value2) {
		var a = true, error = false;
		for (var i = 0; i < item.length; i++) if (!item[i].hide) {
			var v = item[i];
			if (v.remain != 0) a = false;
			for (var i2 = 0; i2 < v.value.length; i2++) if (v.value[i2][0] == 0) a = false;
			if (!a) break;
		}
		if (a) for (var i = 1; i < box.length; i++) if (box[i]) {
			var v = box[i];
			if (!v.hide && v.item.length > 0 && (v.error || v.weight == 0)) {
				a = false;
				if (v.error) error = true;
			}
		}
		var c = (a ? '#0A0' : '#AAA');
		if (error) c = '#FAA';
		var e2 = E('button_package_window_save');
		if (e2) e2.style.background = c;
	}

	// проверка на вес и габариты
	this.check = function(value, value2) {
		if (!self.tariff) return;

		for (var i = 1; i < box.length; i++) if (box[i] && !box[i].hide && (value === undefined || i == value)) {
			var v = box[i];
			var s = edost.admin.check_package(self.tariff, v.weight, v.size);
			v.error = (s === true ? false : true);
			var e = E('edost_package_box_error_' + i);
			if (e) e.innerHTML = (s === true ? '' : '<div style="padding-top: 5px;">' + s[0] + '</div>');
		}
	}

	// обработка ячеек
	this.input_update = function(value, value2) {
		if (!E_input || E_input.value == input_value) return;

		input_value = E_input.value;
		var code = E_input.getAttribute('data-code').split('_');
		var p = input_value.replace(/,/g, '.').replace(/[^0-9.]/g, '');
		var p2 = p.split('.')[0]; // целое число
		p = Math.round(p*1000)/1000;
		if (isNaN(p)) p = 0;

		if (code[2] == 'weight' || code[2] == 'size') {
			code[1] = code[1]*1;
			code[3] = code[3]*1;

			var v = box[ code[1] ];

			if (code[2] == 'weight') v.weight = p;
			else v.size[ code[3] ] = p;

			var a = (p == 0 ? true : false);
			var s = (E_input.getAttribute('data-code').indexOf('_size_') > 0 ? '_error2' : '_error');
			E_input.className = E_input.className.replace(a ? '_on' : s, a ? s : '_on');

			self.check(code[1]);
			self.update_save();

			return;
		}

		if (code[0] == 'box') {
			if (code[2] == 'count') self.update_box_item(E_input);
			return;
		}

		if (code[1] == 'box' || code[1] == 'count') {
			v = item[code[0]];
			v.value[code[2]][code[1] == 'box' ? 0 : 1] = (code[1] == 'box' ? p2 : p)
			self.update_item(E_input);
		}
	}
	this.input_focus = function(value, value2) {
		E_input = value;
		input_value = value.value;
		if (input_timer != undefined) window.clearInterval(input_timer);
		input_timer = window.setInterval('edost.package.input_update()', 200);

		self.update_box_item(E_input, 'focus');
	}
	this.input_blur = function(value, value2) {
		input_timer = window.clearInterval(input_timer);
		self.input_update();

		input_active = [-1, -1, -1, -1];
		self.update_box_item(E_input);
	}
	this.input_keydown = function(value, value2) {
		var event = value2;

		if (event.keyCode == 38 || event.keyCode == 13)
			if (event.preventDefault) event.preventDefault(); else event.returnValue = false;

		var x = 0, y = 0, id, s = [];

		if (event.keyCode == 37 && E_input.selectionStart == 0) x = -1;
		if (event.keyCode == 39 && E_input.selectionStart == E_input.value.length) x = 1;
		if (event.keyCode == 38) y = -1;
		if (event.keyCode == 40) y = 1;

		if (x != 0 || y != 0 || event.keyCode == 13) {
			id = E_input.id.split('_');
			s = E_input.getAttribute('data-code').split('_');
			v = item[s[0] === 'box' ? s[1] : s[0]];
		}

		if (event.keyCode == 13) {
			if (s[1] == 'cut' || s[1] == 'put' || s[1] == 'put2' || s[2] == 'put4' || s[1] == 'put5') {
				var manual = E_input.value*1;
				if (isNaN(manual) || manual <= 0) return;

				if (s[2] == 'put4') self.put4(s[1] + '|' + manual);
				else edost.package[s[1]]((s[1] == 'cut' || s[1] == 'put5' ? s[0] : '') + '|' + manual);

				return;
			}

			if (s[2] == 'weight' || s[2] == 'size' || s[2] == 'count') x = 1;
			if (s[1] == 'box') x = 2;
			if (s[1] == 'count') {
				y = 1;
				s[1] = 'box';
			}
		}

		if (s[0] !== 'box') {
			if (x != 0) {
				if (x > 0 && s[1] == 'box' && (E_input.selectionStart == E_input.value.length || x == 2)) s[1] = 'count';
				else if (x < 0 && s[1] == 'count' && E_input.selectionStart == 0) s[1] = 'box';
				else return;
			}
			if (y != 0) {
				for (var i = 0; i < item.length; i++) {
					s[2] = s[2]*1 + y;
					if (s[2] < 0) {
						s[0]--;
						if (s[0] < 0) return;
						s[2] = item[s[0]].value.length-1;
					}
					else if (s[2] > item[s[0]].value.length-1) {
						s[0]++;
						if (s[0] > item.length-1) return;
						s[2] = 0;
					}
					if (!item[s[0]].hide) break;
				}
			}
		}
		if (x != 0 || y != 0) {
			if (s[0] === 'box') {
				for (var i2 = 0; i2 < 2; i2++) {
					if (i2 == 0) {
						id[3] = id[3]*1 + x*1;
						id[4] = id[4]*1 + y*1;
					}
					else {
						id[3] = 1;
						id[4] = id[4]*1 + x*1;
					}

					if (self.focus(id.join('_'), event)) return;
				}
			}
			else self.focus('edost_package_input_' + s.join('_'), event);
		}
	}
	this.input_id = function(x, y) {
		if (x == 0 && y == 0) input_position = {'x': 0, 'y': 0};
		if (y > 0) input_position.x = 0;
		input_position.x += x;
		input_position.y += y;
		return input_position.x + '_' + input_position.y;
	}


	// фокусировка на указанном input
	this.focus = function(value, value2) {
		var event = value2;
		var e = E(value);
		if (e) {
			if (event.preventDefault) event.preventDefault(); else event.returnValue = false;
			e.focus();
			e.setSelectionRange(-1, -1, 'none');
			return true;
		}
		else return false;
	}

	// обновление места
	this.update_box_item = function(value, value2) {
		var code = value.getAttribute('data-code').split('_');
		if (code[2] == 'weight' || code[2] == 'size') return;

		var v = box[code[1]];
//		if (code[2] == 'count') var item = item[ v.item[code[3]][0] ];

		if (value2 == 'focus') input_active = code;

		if (code[2] == 'count') {
			var e = E('box_' + code[1] + '_active_' + code[3]);
			v.item[code[3]][1] = value.value*1;
			e.checked = (value.value == 0 ? false : true);
		}

		if (!v) return;

		var a = false;
		for (var i = 0; i < v.item.length; i++) {
			var E_table = E('box_' + code[1] + '_table_' + i);
			var E_active = E('box_' + code[1] + '_active_' + i);
			var E_count = BX.findChild(E_table, {'tag': 'input', 'attribute': {'type': 'input'}}, true);

			var checked = E_active.checked;

			if (!checked || v.item[i][1]*1 != v.item[i][2]*1) a = true;

			var c = ['edost_package_box_item'];
			var error = (checked && v.item[i][1]*1 > v.item[i][2]*1 ? true : false);
			var active = (input_active && code[1] == input_active[1] && i == input_active[3] ? true : false);
			if (active) c.push('edost_package_box_item_active');
			if (checked) { if (!error) c.push('edost_package_box_item_on'); }
			else if (!active) c.push('edost_package_box_item_off');
			E_count.className = (error ? 'edost_package_error' : 'edost_package_on');
			E_table.className = c.join(' ');
		}

		var e = E('box_' + code[1] + '_save');
		if (e) e.style.display = (a ? 'inline' : 'none');
	}

	// вывод кнопок в списке товаров
	this.draw_button = function(value, value2) {
		v = item[value];
		var s = '';

           if (v.QUANTITY > 1) {
			if (v.remain > 1) s += self.draw_button2({"head": "разбить по", "start": 1, "end": 5, "if": v.remain, "param": "cut", "value": value});

			var a = true;
			var n = 1;
			for (var i = 0; i < v.value.length; i++)
				if (v.value[i][0] == 0) a = false;
				else if (v.value[i][0]*1 >= n) n = v.value[i][0]*1 + 1;

			if (v.remain == 0 && !a) s += self.draw_button2({"head": "разнести по местам с", "start": n, "end": n+4, "param": "put5", "value": value});

			if (v.value.length > 1 || v.value[0][0] != 0 || v.value[0][1] != 0) s += '<span style="float: right;" class="edost_control_button edost_control_button_low" onclick="edost.package.clear(' + value + ')">сбросить</span>';
		}

		return s;
	}

	// вывод блока с цифровыми кнопками и ячейкой для ручного ввода
	this.draw_button2 = function(value, value2) {
		var v = value;
		s = '<div class="edost_package_button"' + (v.style ? ' style="' + v.style + '"' : '') + '>';
		s += '<span class="edost_package_button_head">' + v.head + '</span> ';
		var n = 0;
		for (var i = v.start; i <= v.end; i++) {
			if ((!v.if || v.if > i) && (!v.if2 || i != v.value)) {
				s += '<span class="edost_package_button edost_control_button edost_control_button_white" onclick="edost.package[\'' + v.param + '\'](\'' + v.value + '|' + i + '\')">' + (i == 0 ? '&nbsp;&nbsp;' : i) + '</span>';
				n++;
			}
			if (v.if2 && n == v.end) break;
		}
		if (v.if2) self.input_id(0, 1);
		if (!v.if || v.if > v.end) s += '<input' + (v.if2 ? ' id="edost_input_2_' + self.input_id(1, 0) + '"' : '') + ' data-code="' + (v.if2 ? 'box_' : '') + v.value + '_' + v.param + '" value="" ' + input_function + '>';
		s += '</div>';

		return s;
	}

	// вывод общих кнопок для списка товаров
	this.draw_button3 = function(value, value2) {
		var e2 = E('edost_package_window_button');
		if (!e2) return;

		var n = 0, n2 = 0, n3 = 0;
		for (var i = 0; i < item.length; i++) if (!item[i].hide) {
			var v = item[i];
			n2 += v.remain*1;
			for (var i2 = 0; i2 < v.value.length; i2++) {
				if (v.value[i2][0] == 0 && v.value[i2][1] != 0) n3 += v.value[i2][1]*1;
				if (v.value[i2][0] != 0 && v.value[i2][1] == 0) n++;
			}
		}

		var s = '';
		if (n2 > 0) s += self.draw_button2({"head": "поместить оставшиеся товары в место", "start": 1, "end": 5, "param": "put", "value": ""});
		if (n3 > 0) s += self.draw_button2({"head": "поместить товары у которых указано количество в место", "start": 1, "end": 5, "param": "put2", "value": ""});
		if (n > 0) s += '<span style="margin: 0 40px;" class="edost_package_button edost_control_button edost_control_button_white" onclick="edost.package.put3(\'\')">разместить оставшиеся товары по указанным местам</span>';
		if (s != '') s = edost.admin.param.delimiter + '<div style="padding: 20px; 0">' + s + '</div>';
		e2.innerHTML = s;
		e2.style.display = (item_mode && s != '' ? 'block' : 'none');
	}


	// вывод блока с ячейками в списке с товарами
	this.draw_item_input = function(value, value2) {
		var s = '';
		value = value.split('|');
		v = item[value[0]];
		if (value[1] == 'all') {
			for (var i = 0; i < v.value.length; i++) s += '<div style="padding: 2px 0;">' + self.draw_item_input(value[0] + '|' + i) + '</div>';
		}
		else {
			var id = value[0] + '_box_' + value[1];
			s += '<div class="edost_package_item_value">';
			s += '<input id="edost_package_input_' + id + '" data-code="' + id + '" value="' + edost.digit(v.value[value[1]][0]) + '" ' + input_function + '>';
			s += '</div>'

			var id = value[0] + '_count_' + value[1];
			s += '<div class="edost_package_item_value">';
			s += '<input id="edost_package_input_' + id + '" data-code="' + id + '" value="' + edost.digit(v.value[value[1]][1]) + '" ' + input_function + '>';
			s += '</div>'
		}

		return s;
	}

	// вывод подписи "разделить/собрать комплект"
	this.draw_set = function(value, value2) {
		var v = value.split('|');

		var s = '';
		if (v[1] === 'set') s += '<span class="edost_control_button edost_control_button_low" onclick="edost.package.pack(' + v[0] + ', true)">разделить комплект</span>';
		else if (v[1]) s += '<span class="edost_control_button edost_control_button_low" onclick="edost.package.pack(' + v[1] + ')">собрать комплект обратно</span>';
		if (s != '') {
			if (v[2] == 'box') s = '<div style="padding-left: 18px;">' + s + '</div>';
			else s = '<br>' + s;
		}

		return s;
	}

	// вывод списка товаров
	this.draw_item = function(value, value2) {
		var e = E('edost_package_window_item');
		if (!e) return;

		if (value == 'all') {
			var e2 = E('edost_package_window_item_head');
			if (!e2) return;

			s = '<table width="100%" border="0" cellpadding="4" cellspacing="0" style="text-align: center;">';
			s += '<tr style="font-size: 12px;">';
			s += '<td width="80"></td>';
			s += '<td width="250"></td>';
			s += '<td width="50">всего</td>';
			s += '<td width="120" style="font-weight: bold;">';
				s += '<div class="edost_package_item_value">место</div>';
				s += '<div class="edost_package_item_value"> шт.</div>';
			s += '</td>';
			s += '<td width="50">осталось</td>';
			s += '<td><span style="float: right;" class="edost_control_button edost_control_button_low" onclick="edost.package.clear()">сбросить все</span></td>';
			s += '</tr>';
			s += '</table>';
			e2.innerHTML = s;

			e.innerHTML = '';
			for (var i = 0; i < item.length; i++) self.draw_item(i);
		}
		else {
			v = item[value];

			if (v.hide) return;

			var s = '';
			if (value != 0) s += edost.admin.param.delimiter;
			s += '<table id="edost_package_item_' + value + '_table" width="100%" border="0" cellpadding="4" cellspacing="0" style="text-align: center;">';
			s += '<tr>';
			s += '<td width="330" style="text-align: left; padding-left: 10px;">' + v.NAME;
				s += '<br><div class="edost_package_item_weight">' + v.package_weight + ' кг</div>';
				s += '<div class="edost_package_item_size">' + (v.package_size ? v.package_size + ' см' : '') + '</div>';
				s += '<div class="edost_package_item_volume">' + (v.package_volume > 0 ? v.package_volume + ' м<sup>3</sup>' : '') + '</div>';
				s += self.draw_set(v.ID + '|' + v.SET_PARENT_ID);
			s += '</td>';
			s += '<td width="50">' + v.QUANTITY + '</td>';
			s += '<td width="120">' + self.draw_item_input(value + '|all') + '</td>';
			s += '<td id="edost_package_item_' + value + '_remain" width="50" style="font-weight: bold; cursor: ' + (v.remain == 0 ? 'default' : 'pointer') + ';" onclick="edost.package.remain(\'' + value + '\')">' + (v.remain > 0 ? v.remain : '') + '</td>';
			s += '<td id="edost_package_item_' + value + '_button" style="text-align: left;">' + self.draw_button(value) + '</td>';
			s += '</tr></table>';
			e.innerHTML += s;
		}
	}

	// вывод мест
	this.draw_box = function(value, value2) {
		if (value == 'all') {
			var e = E('edost_package_window_box');
			if (!e) return;

			self.input_id(0, 0);

			var e2 = E('edost_package_window');
			var h = e2.offsetHeight - E('edost_package_window_box').getBoundingClientRect().top + e2.getBoundingClientRect().top - 20;

			var s = '';
			if (item_mode) s = edost.admin.param.delimiter + '<div style="height: 20px;"></div>';
			s += '<div id="edost_package_box_div"><div id="edost_package_box_div_in">' + self.draw_box(0) + '</div></div>';
			s += '<div id="edost_package_box_div2"><div id="edost_package_box_div2_in">';
			var n = 0;
			for (var i = 1; i < box.length; i++) if (box[i] && !box[i].hide) {
				s += self.draw_box(i);
				if (box[i].item.length > 0) n++;
			}
			if (n == 0) s += '<div class="edost_package_help">' + (item_mode ? 'укажите номер места и количество товаров в данном месте <br> для переменщения по ячейкам можно использовать клавиши <div>&#8593</div>, <div>&#8595</div>, <div>&#8592</div>, <div>&#8594</div>, <div>Enter</div>' : 'укажите количество и мышкой перетащите товар в его новое место <br> <span style="font-size: 16px;">или</span> <br> отметьте галочками интресующие товары, укажите их количество и нажмите по квадратику с номером места') + '</div>';
			s += '</div></div>';

			e.innerHTML = s;

			self.resize();
		}
		else {
			v = box[value];

			if (v.hide || item_mode && value == 0) return '';

			var s = '';

			var c = '';
			if (value == 0) c = 'edost_package_box_0';
			else if (v.item.length == 0) c = 'edost_package_box_empty';
			else c = 'edost_package_box_normal';

			v.item_weight = 0;
			var volume = 0;
			if (v.item.length > 0) for (var i = 0; i < v.item.length; i++) {
				var v2 = item[v.item[i][0]];
				v.item_weight += v2.package_weight * v.item[i][1];
			}
			var n = 100;
			if (v.item_weight >= 10) n = 10;
			else if (v.item_weight >= 100) n = 1;
			v.item_weight = Math.round(v.item_weight*n)/n;

			s += '<div class="edost_package_box ' + c + '" data-code="' + value + '">';
			s += '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';

			s += '<td class="edost_package_box_head">' + (value == 0 ? 'нераспределено ' : '<div>место</div>' + value) + '</td>';
			if (value == 0) s += '</tr><tr>';
			s += '<td style="padding: 8px;">';

			s += '<div style="text-align: center;">';
			if (value != 0 && v.item.length > 0) {
				if (v.item_weight > 0) s += '<span class="edost_package_box_weight" onclick="edost.package.set_weight(\'' + value + '\', this)">' + v.item_weight + ' кг</span>';
				self.input_id(0, 1);
				s += '<input id="edost_input_2_' + self.input_id(1, 0) + '" class="' + (v.weight == 0 ? 'edost_package_error' : 'edost_package_on') + '" data-code="box_' + value + '_weight" style="height: 18px; width: 35px;" value="' + edost.digit(v.weight) + '" autocomplete="off" ' + input_function + '> кг';
				for (var i2 = 0; i2 <= 2; i2++) s += '<input id="edost_input_2_' + self.input_id(1, 0) + '" class="' + (v.size[i2] == 0 ? 'edost_package_error2' : 'edost_package_on') + '" data-code="box_' + value + '_size_' + i2 + '" style="height: 18px; width: 35px;' + (i2 == 0 ? ' margin-left: 10px;' : '') + '" value="' + edost.digit(v.size[i2]) + '" ' + input_function + '> ' + (i2 != 2 ? ' x ' : '');
				s += ' см';
			}
			s += '</div>';

			s += '<div id="edost_package_box_error_' + value + '" style="color: #F00;"></div>';

			s += '<div style="padding-top: 5px; text-align: center;">'
			if (v.item.length > 0) {
				s += '<div style="padding-top: 5px;">';
				for (var i = 0; i < v.item.length; i++) {
					v2 = item[v.item[i][0]];
					if (i != 0) s += edost.admin.param.delimiter;
					s += '<table id="box_' + value + '_table_' + i + '" class="edost_package_box_item" width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
					s += '<td>';
					if (!item_mode) s += '<div class="checkbox">';
					var code = 'box_' + value + '_active_' + i;
					if (!item_mode) s += '<input id="' + code + '" data-code="' + code + '" class="edost_package_checkbox" type="checkbox" onclick="edost.package.update_box_item(this)"' + (v.item[i][1] > 0 ? ' checked=""' : '') + '> ';
					if (!item_mode) s += '<label data-code="box_' + value + '_name_' + i + '" class="edost_package_move" for="' + code + '">';
					s += (!item_mode ? '<b>' + v2.NAME + '</b>' : v2.NAME);
					if (!item_mode) s += '</label>';
					if (!item_mode) s += self.draw_set(v2.ID + '|' + v2.SET_PARENT_ID + '|' + 'box');
					if (!item_mode) s += '</div>';
					s += '</td>';
					s += '<td width="40" align="right" class="edost_package_box_item_all" style="' + (!item_mode ? ' color: #888;' : ' font-weight: bold;') + '">' + edost.digit(v.item[i][2]) + '</td>';
					if (!item_mode) {
						self.input_id(0, 1);
						s += '<td class="edost_package_box_item_count"><input id="edost_input_2_' + self.input_id(1, 0) + '" data-code="box_' + value + '_count_' + i + '" value="' + edost.digit(v.item[i][1]) + '" ' + input_function + '></td>';
                   	}
					s += '</tr></table>';
				}
				s += '</div>';

				if (!item_mode) {
					s += self.draw_button2({"head": "перенести выделенные в место", "start": 0, "end": 3, "if2": true, "param": "put4", "value": value, "style": "padding-top: 10px; margin: 0px;"});
					if (value != 0) s += '<span id="box_' + value + '_save" style="padding-left: 10px; display: none;" class="edost_control_button edost_control_button_green" onclick="edost.package.put4(\'' + value  + '|' + value + '\')">сохранить</span>';
				}
			}
			s += '</div>';

			s += '</td>';

			s += '</tr></table>';
			s += '</div>'

			return s;
		}
	}


	// перетаскивание товаров между местами + выделение галочек товаров одним движением
	this.onmousedown = function(event) {
		if (mouse.move || mouse.checkbox || event.which != 1) return;

		var ar = ['move', 'checkbox'];
		for (var i = 0; i < ar.length; i++) {
			var e = event.target.closest('.edost_package_' + ar[i]);
			if (!e) continue;
			var c = edost.data(e, 'code').split('_');
			mouse[ar[i]] = {"e": e, "x": event.pageX, "y": event.pageY, "box": c[1], "item": c[3]};
			if (e.type == 'checkbox') mouse[ar[i]].active = e.checked;
			if (ar[i] == 'move') mouse[ar[i]].table = E(c.join('_').replace('_name_', '_table_'))
		}
	}
	this.onmousemove = function(event) {
		if (mouse.checkbox)	{
			if (Math.abs(event.pageX - mouse.checkbox.x) < 5 && Math.abs(event.pageY - mouse.checkbox.y) < 5) return;

			if (mouse.checkbox.e.checked == mouse.checkbox.active) mouse.checkbox.e.checked = !mouse.checkbox.active;

			var e = event.target.closest('.edost_package_checkbox');
			var c = (e ? edost.data(e, 'code').split('_') : false);
			if (c[1] == mouse.checkbox.box && c[3] != mouse.checkbox.item && e.checked == mouse.checkbox.active) e.checked = !mouse.checkbox.active;
		}

		if (!mouse.move) return;

		if (!mouse.move.e2) {
			if (Math.abs(event.pageX - mouse.move.x) < 5 && Math.abs(event.pageY - mouse.move.y) < 5) return;

			mouse.move.table.className = 'edost_package_box_item edost_package_box_item_move';
			mouse.move.e2 = BX.create('div', {'props': {'innerHTML': mouse.move.e.innerHTML + ' (' + box[mouse.move.box].item[mouse.move.item][1] + ' шт.)'}});
			mouse.move.e2.className = 'edost_package_box_move_active';

			var e = E('edost_package_window_data');
			e.appendChild(mouse.move.e2);
			var coords = mouse.move.e.getBoundingClientRect();

			mouse.move.shiftX = mouse.move.x - coords.left;
			mouse.move.shiftY = mouse.move.y - coords.top;
		}

		mouse.move.e2.style.left = Math.round(event.pageX - mouse.move.shiftX) + 'px';
		mouse.move.e2.style.top = Math.round(event.pageY - mouse.move.shiftY) + 'px';

		var e = event.target.closest('.edost_package_box');
		var c = (e ? edost.data(e, 'code') : false);
		if (mouse.move.input && mouse.move.input !== c && mouse.move.input !== false) {
			mouse.move.inputE.classList.remove('edost_package_box_move');
			mouse.move.input = false;
		}
		if (c !== false && c != mouse.move.box) {
			mouse.move.input = c;
			mouse.move.inputE = e;
			mouse.move.inputE.classList.add('edost_package_box_move');
		}

		return false;
	}
	this.onmouseup = function(event) {
		if (mouse.move && mouse.move.e2) mouse.move.e2.remove();

		if (mouse.move)
			if (mouse.move.inputE && mouse.move.input !== false) self.put4(mouse.move.box + '|' + mouse.move.input + '|' + mouse.move.item);
			else if (mouse.move.table) mouse.move.table.className = 'edost_package_box_item';

		mouse.move = false;
		mouse.checkbox = false;
	}


	this.window = function(param) {

		// генерация мест по списку товаров

		// обновление остатков товаров
		for (var i = 0; i < item.length; i++) {
			var v = item[i];
			var n = 0;
			for (var i2 = 0; i2 < v.value.length; i2++) n += v.value[i2][1]*1;
			v.remain = v.QUANTITY - n;
		}

		if (item_mode) self.draw_button3();

		for (var i = 0; i < box.length; i++) if (box[i]) {
			box[i].item = [];
			box[i].hide = true;
		}

		var ar = [];
		for (var i = 0; i < item.length; i++) if (!item[i].hide) for (var i2 = 0; i2 < item[i].value.length; i2++) {
			var id = item[i].value[i2][0];
			var count = item[i].value[i2][1];

			if (id == 0) continue;
			if (id > 100) id = 100;

			if (!box[id]) box[id] = {"id": id, "hide": false, "weight": 0, "size": [0, 0, 0], "item": []};

			var a = false;
			for (var u = 0; u < box[id].item.length; u++) if (box[id].item[u][0] == i) {
				box[id].item[u][1] = box[id].item[u][1]*1 + count*1;
				a = u;
				break;
			}
			if (a === false) {
				box[id].item.push([i, count, 0]);
				a = box[id].item.length - 1;
			}

			box[id].hide = false;
		}

		for (var i = 0; i < box.length; i++) if (box[i])
			for (var i2 = 0; i2 < box[i].item.length; i2++) box[i].item[i2][2] = box[i].item[i2][1];

		box[0] = {"id": 0, "hide": false, "item": []};
		for (var i = 0; i < item.length; i++) if (!item[i].hide) {
			var n = 0;
			for (var i2 = 0; i2 < item[i].value.length; i2++) if (item[i].value[i2][0] > 0) n += item[i].value[i2][1]*1;
			n = item[i].QUANTITY - n;
			if (n > 0) box[0].item.push([i, n, n]);
		}

		// добавление пустого места для ручного распределения
		if (!item_mode) {
			var n = 0;
			for (var i = box.length-1; i >= 0; i--) if (box[i] && !box[i].hide) { n = i+1; break; }
			if (n <= 0) n = 1;
			box[n] = {"id": n, "hide": false, "weight": 0, "size": [0, 0, 0], "item": []};
		}

		// удаление веса и габаритов у пустых мест
		for (var i2 = 0; i2 < box.length; i2++) if (box[i2] && box[i2].item.length == 0) {
			box[i2].weight = 0;
			box[i2].size = [0, 0, 0];
		}

		if (param === 'update_box') {
			self.draw_box('all');
			return;
		}

		if (!param) {
			document.onkeydown = edost_package_onkeydown_backup;
			document.body.style.overflow = edost_package_overflow_backup;
			window.onresize = edost_package_onresize_backup;
			edost_package_onkeydown_backup = 'free';
		}
		else if (edost_package_onkeydown_backup == 'free') {
			edost_package_onkeydown_backup = document.onkeydown;
			edost_package_overflow_backup = document.body.style.overflow;
			edost_package_onresize_backup = window.onresize;
			document.onkeydown = new Function('event', 'if (event.keyCode == 27) edost.package.window(false);');
			window.onresize = new Function('', 'edost.package.resize()');
		}

		// генерация окна
		var e = E('edost_package_window');
		if (!e) {
			edost.create('WINDOW', 'edost_package_window_fon', {'class': 'edost_office_window_fon', 'onclick': new Function('', 'edost.package.window(false)')});

			var s = '';
			s += edost.close.replace('%onclick%', "edost.package.window(false)");
			s += '<div id="edost_office_window_head" class="edost_office_window_head" style="font-size: 20px; padding-top: 6px;">';
				s += '<div id="edost_package_window_head_div_left" style="text-align: center;">';
				s += '<div id="edost_package_setting_item">';
				s += 'Распределение товаров<div style="height: 5px;"></div>';
				s += '<span class="edost_control_button edost_control_button_light edost_control_button_low edost_button_big_off" onclick="edost.package.mode(\'box\')">по местам</span>';
				s += '<span style="margin-left: 20px;" class="edost_control_button edost_control_button_light edost_control_button_low edost_button_big_on" onclick="edost.package.mode(\'item\')">по списку</span>';
				s += '</div>';
				s += '</div>';
				s += '<div id="edost_package_window_head_div_right" style="text-align: center;">';
				s += '<div id="button_package_window_save" class="edost_register_button" style="margin: 0 auto; line-height: 15px; cursor: pointer; background: #0A0; border-radius: 5px; width: 160px; height: 35px; color: #FFF;" onclick="edost.package.save()">';
				s += '<div style="position: absolute; margin: 0 auto; width: 160px; text-align: center; padding-top: 10px; font-size: 20px;">сохранить</div>';
				s += '</div>';
				s += '</div>';
			s += '</div>';
			s += '<div id="edost_package_window_data" class="edost" style="padding: 12px 20px 20px 20px; -moz-user-select: none; -webkit-user-select: none; user-select: none;">';
				s += '<div id="edost_package_window_item_head" class="edost" style="background: #DAF3FF;"></div>';
				s += '<div id="edost_package_window_item" class="edost" style="padding: 0 0 10px 0; overflow: auto;"></div>';
				s += '<div id="edost_package_window_button" class="edost"></div>';
				s += '<div id="edost_package_window_box" class="edost" style="text-align: center;"></div>';
			s += '</div>';
			edost.create('WINDOW', 'edost_package_window', {'class': 'edost_office_window edost_device_pc', 'html': s}, {'border': 'none'});

			self.mode('update', 'start');

			var e = E('edost_package_window_data');
			if (e) {
				e.onmousedown = self.onmousedown;
				e.onmousemove = self.onmousemove;
				document.onmouseup = self.onmouseup;
			}
		}


		var display = (!param ? 'none' : 'block');

		var package_data = E('edost_package_window_data');
		if (!package_data) return;

		var e = E('edost_package_window');
		if (!e) return;
		e.style.display = display;

		var e2 = E('edost_package_window_fon');
		if (e2) e2.style.display = display;

		if (!param) return;

		var E_box = E('edost_package_box_div');
		var E_box2 = E('edost_package_box_div2');
		if (E_box && E_box2) scroll = [E_box.scrollTop, E_box2.scrollTop];

		self.draw_item('all');
		self.update_item_list();
		self.draw_box('all');
		self.check();
		self.update_save();
		self.resize();
	}

}




// оформление доставки
edost.register = new function() {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C
	var ajax_set = '', E_transfer, transfer_timer = 0, transfer_time_start = 0, transfer_time_end = 0, transfer_position = 0, transfer_set = -1, transfer_history = false;
	var input_timer, E_input, input_value, input_active;

	this.delete = function(value, value2) {

		var s = value.split('|');
		var name = s[0];
		var id = s[1];
		var hide = (s[2] ? true : false);

		var e = BX('register_button');
		if (e) e.style.display = 'none';
		var e = BX('register_button2');
		if (e) e.style.display = 'block';

		if (name == 'order') {
			self.delete('register|' + id);
			self.delete('batch|' + id);

			var e = BX('edost_shipment_' + id);
			if (!e) return;
			var e = e.parentNode.parentNode;
			var ar = e.children[2].getElementsByTagName('IMG');
			for (var i = 0; i < ar.length; i++) if (ar[i].classList.contains('edost_register_on') || ar[i].classList.contains('edost_register_off')) {
				ar[i].className = 'edost_register_disabled3';
				ar[i].onclick = '';
			}
			var ar = BX.findChildren(e, {'tag': 'input', 'attribute': {'type': 'checkbox'}}, true);
			for (var i = 0; i < ar.length; i++) {
				var e2 = BX.findNextSibling(ar[i]);
				e2.remove();
				ar[i].remove();
			}

			var e = e.parentNode.parentNode.parentNode;
			self.active_all_update(e);

			return;
		}

		var e = BX('edost_register_button_' + id);
		if (e) e.style.display = 'none';

		var e = BX('edost_register_company');
		var company = (e ? e.value : '');

		var e = BX('edost_' + name + '_img_' + id);
		if (e) {
			var s = e.src.split('control_' + name + '_');
			e.src = s[0] + 'control_' + (company == 23 ? name : 'batch') + '_' + 'delete.png';
		}

		D('edost_' + name + '_delete_' + id, false);

	}

	this.button_active = function(value, value2) {

		var s = value;
		var name = s[0];
		var a = (s[1] ? true : false);

		if (name != 'all') var id = [name];
		else {
			var id = [];
			var ar = document.getElementsByClassName('edost_register_button');
			for (var i = 0; i < ar.length; i++) id.push(ar[i].id);
		}
		for (var i = 0; i < id.length; i++) {
			var e = BX(id[i]);
			if (e) e.className = 'edost_register_button ' + (!a ? 'edost_register_button_disabled' : '');
			var e = BX(id[i] + '_disabled');
			if (e) e.style.display = (!a ? 'block' : 'none');
		}

	}

	this.local_button_active = function(value, value2) {
		for (var i = 0; i < value.length; i++) {
			var e = BX('edost_register_button_' + value[i][0] + '_' + value[i][1]);
			if (!e) continue;
			e.style.display = (value[i][2] ? 'inline' : 'none');
			e = e.previousSibling;
			if (e && e.className == 'edost_register_button_delimiter') e.style.display = (!value[i-1][2] || !value[i][2] ? 'none' : 'inline');
		}
	}

	this.transfer_status = function(value, value2) {
		var color = (value[1] != undefined ? value[1] : '888');
		value = value[0];

		if (color == 'red') color = 'F00';
		if (color == 'green') color = '0A0';

		var s = value;
		for (var k in edost.admin.param.register_status) if (value == k) s = edost.admin.param.register_status[k];

		var e = BX('edost_transfer_status');
		if (e) e.innerHTML = '<span style="font-size: 16px; color: #' + color + ';">' + s + '</span>';
	}
	this.transfer_start = function(value, value2) {
		E_transfer = BX('edost_transfer_bar');

		var s = new Date();
		transfer_time_start = s.getTime();
		transfer_position = 0;
		transfer_set = -1;
		transfer_history = false;
		transfer_time_end = 0;

		if (transfer_timer != undefined) window.clearInterval(transfer_timer);
		transfer_timer = window.setInterval('edost.register.transfer()', 40);
	}
	this.transfer_stop = function(value, value2) {
		if (transfer_timer != undefined) window.clearInterval(transfer_timer);
		if (value != undefined && value != '') {
			var s = '<b>' + value + '</b> <br> <span style="font-size: 16px; font-weight: normal; display: inline-block; margin-top: 5px;" class="edost_control_button edost_control_button_low" onclick="edost.register.transfer_stop(); edost.admin.set_param(\'register\', \'reload\');">закрыть</span>';
			self.transfer_status([s, 'red']);
		}
		else {
			D('edost_transfer_fon', false);
			D('edost_transfer', false);
		}
	}
	this.transfer = function(value, value2) {
		var s = new Date();
		var time = (s.getTime() - transfer_time_start)/100;

		if (transfer_time_end !== 0) {
			var time = (transfer_time_end - transfer_time_start)/100 + (s.getTime() - transfer_time_end)/15;
		}

		var data_E = BX('edost_main_div');
		var fon_E = BX('edost_transfer_fon');
		var transfer_E = BX('edost_transfer');
		if (data_E && fon_E && transfer_E) {
			fon_E.style.width = data_E.offsetWidth + 'px';
			fon_E.style.height = data_E.offsetHeight + 'px';
			fon_E.style.display = 'block';
			transfer_E.style.width = (data_E.offsetWidth-122) + 'px';
			transfer_E.style.display = 'block';
			var browser_h = (edost.browser('h') == 0 ? document.body.clientHeight : edost.browser('h'));
			transfer_E.style.top = Math.round((browser_h - transfer_E.offsetHeight)*0.5) + 'px';
		}

		if (transfer_position == 0 || transfer_set == 1 && transfer_position == 1 && time > 25 || transfer_time_end === 0 && transfer_set == 2 && transfer_position == 2 && time > 75) {
			transfer_position++;
			if (transfer_position == 1) self.transfer_status(['transfer']);
			if (transfer_position > 1) self.button('update');
		}
		if (time > 100) {
			var s = '';
			if (transfer_set == -1) s = edost.admin.param.control_error.timeout + '!'; // ответ не получен (превышен лимит ожидания)
			else if (!transfer_history) s = edost.admin.param.control_error.no_data + '!'; // во время обработки данных произошел сбой
			self.transfer_stop(s);

			transfer_set = -2;
			if (s == '') edost.admin.set_param('register', 'history');

			return;
		}

		E_transfer.innerHTML = '<div style="background: #0eb3ff; height: 10px; width: ' + time + '%;">&nbsp;</div>';
	}

	this.help = function(value, value2) {
		var s = '';
		for (var k in edost.admin.param.register_button) if (value == 'button_' + k) s = edost.admin.param.register_button[k].help;
		H('edost_help', s);
		D('edost_help_default', s == '' ? 'inline' : 'none');
	}

	this.input_start = function(value, value2) {
		e = E(value) || document.querySelector('input[name="' + value + '"]');
		if (!e) return;
		E_input = e;
		input_value = e.value;
	}
	this.input_update = function(value, value2) {
		if (E_input && E_input.value != input_value) {
			var id = E_input.id || E_input.name;
			input_value = E_input.value;
			if (id.indexOf('_date') > 0) {
				var date = new Date();
				var v = E_input.value.split('.');
				var a = true;
				if (v[0] <= 31 && v[1] && v[1] <= 12 && v[2] && !v[3]) {
					var u = new Date(v[2], v[1]-1, v[0]);
					var n = new Date(date.getFullYear(), date.getMonth(), date.getDate());
					if (u && u.valueOf() >= n.valueOf() && v[0] == u.getDate() && v[1] == u.getMonth()+1 && v[2] == u.getFullYear()) a = false;
				}
				if (id.indexOf('window_') == 0) {
					if (E_input.value == edost.window.param.batch_date) a = true;
					edost.window.resize('error', a);
				}
				var s = '_error';
			}
			else {
				var a = (E_input.value == 0 ? true : false);
				var s = (E_input.getAttribute('data-code').indexOf('_size_') > 0 ? '_error2' : '_error');
			}
			E_input.className = E_input.className.replace(a ? '_on' : s, a ? s : '_on');

			self.active_all_update();
		}
	}
	this.input_focus = function(value, value2) {
		E_input = value;
		input_value = value.value;
		if (input_timer != undefined) window.clearInterval(input_timer);
		input_timer = window.setInterval('edost.register.input_update()', 200);
	}
	this.input_blur = function(value, value2) {
		input_timer = window.clearInterval(input_timer);
		self.input_update();
	}

	this.active_change = function(value, value2) {
		var a = true;
		if (value2 == undefined) a = (value.className.indexOf('_on') > 0 ? true : false);
		if (value2 == 'on') a = false;
		value.className = value.className.replace(a ? '_on' : '_off', a ? '_off' : '_on');
		return !a;
	}

	this.check_batch_date = function(value, value2) {
		var e = BX('edost_batch_div');
		if (e) {
			var e = BX('edost_batch');
			if (!e || e.value == 'new') {
				var e = BX('edost_batch_date');
				if (e && e.className.indexOf('_error') > 0) return false;
			}
		}
		return true;
	}

	this.active_all_update = function(value, value2) {
		// глобальное обновление

		// размер главной формы
		var e = BX('edost_main_div');
		if (e) e.className = 'edost_main_div_size' + (edost.cookie(['admin', 'register_item']) == 'Y' ? '2' : '');

		// обновление глобальной галочки выделения
		if (value) {
			var a = false;
			var ar = BX.findChildren(value, {'tag': 'input', 'attribute': {'type': 'checkbox'}}, true);
			for (var i = 0; i < ar.length; i++) if (ar[i].type != 'hidden' && ar[i].checked) { a = true; break; }
			BX(value.id + '_active').checked = a;
		}

		// ссылки на печать
		if (1 == 2) {
		var e = BX('edost_print');
		if (e) {
			var print = '<div style="color: #888; padding-bottom: 5px;">ссылки на печать для активных заказов / бланков:</div>';

			var doc = self.get_doc('all');
			print += self.get_print_link([doc, 'все документы', 'все ярлыки']);

			e.parentNode.style.display = (doc.count != 0 ? 'block' : 'none');

			var order = [];
			var batch = [];
			var ar = document.getElementsByClassName('edost_shipment');
			if (ar.length > 1) for (var i = 0; i < ar.length; i++) {
				if (!ar[i].checked) continue;

				var order_code = ar[i].getAttribute('data-code');
				var id = ar[i].id.split('edost_shipment_')[1];
				var doc = self.get_doc(id);
                   if (doc.count == 0) continue;

				var s = self.get_print_link([doc, order_code]);

				var e2 = BX('edost_batch_shipment_' + id);
				if (e2) {
					var batch_id = e2.name;
					var a = false;
					for (var i2 = 0; i2 < batch.length; i2++) if (batch[i2][0] == batch_id) {
						a = true;
						batch[i2][1].normal = batch[i2][1].normal.concat(doc.normal);
						batch[i2][1].label = batch[i2][1].label.concat(doc.label);
						batch[i2][5].normal = batch[i2][5].normal.concat(doc.normal);
						batch[i2][5].label = batch[i2][5].label.concat(doc.label);
						batch[i2][2].push(s);
						break;
					}
					if (!a) {
						var e2 = BX('edost_batch_name_' + e2.name.split('edost_batch_')[1]);
						if (e2) {
							if (e2.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.id != 'edost_shipment_register_complete_batch_full') doc2 = doc;
							else doc2 = {
								'normal': [].concat([id + '_103'], doc.normal),
								'label': doc.label,
								'count': doc.count + 1,
							};
							batch.push([batch_id, doc2, [s], e2.value, self.get_print_link([[id + '_103'], 'ф.103']), doc]);
						}
					}
				}
				else order.push(s);
			}

			for (var i = 0; i < batch.length; i++) {
				print += '<div style="padding-top: ' + (i == 0 ? '10' : '5') + 'px">';
				print += batch[i][3] + ': ' + self.get_print_link([batch[i][1], 'документы']);
//				print += ''<br>'' + batch[i][4] + ', ' + self.get_print_link([batch[i][5], 'заказы']) + '<br>';
//				if (batch[i][2].length != 1) print += 'заказ № ' + batch[i][2].join(', ');
				print += '</div>';
			}

			if (order.length != 0) print += '<div style="padding-top: 10px">заказ № ' + order.join(', ') + '<div>';

			e.innerHTML = print;
		}
		}


		var warning = [];
		var warning_add = '';
		var batch_div = false;
		var batch_count = 0;
		var batch_date = self.check_batch_date();
		var order_count = 0;

		// проверка на флаги заполненности профилей и требования опций
		var call = false;
		var flag_error = false;
		var E_call = BX('edost_call');
		if (E_call) {
			call = (E_call.checked ? 'Y' : 'N');
			var E_shop = BX('edost_profile_shop');
               if (E_shop) var E_option = E_shop.options[E_shop.selectedIndex];

			var s = (E_shop && E_shop.value ? ['change', E_shop.value, 'заполнить'] : ['new', 0, 'создать новый профиль']);
			var profile_button = '(<span class="edost_link" onclick="edost.window.set(\'profile_setting_' + s[0] + '\', \'type=shop;local=1;id=' + s[1] + '\')">' + s[2] + '</span>)';

			// вызов курьера
			if (call == 'Y' && (!E_shop || !E_option.getAttribute('data-flag_call'))) warning.push('для услуги "вызов курьера", заполнить в профиле магазина поля откуда курьеру забирать груз (город, адрес, телефон, время ожидания курьера) и указать ФИО сотрудника, отвечающего за сдачу/приемку груза ' + profile_button);
		}

		// заказы готовые к оформлению
		var e = BX('edost_shipment_register_new');
		if (e) {
			// определение типа выбранной сдачи (негабарит)
			var oversize = false;
			var e2 = BX('edost_batch');
			if (e2) {
				var e2 = e2.options[e2.selectedIndex];
				var o = e2.getAttribute('data-oversize');
				if (o != undefined) oversize = o*1;
			}

			// распечатать без оформления
			var doc = self.get_doc('print_no_register');
			self.button_active(['button_print_no_register', doc.count != 0 ? 1 : 0]);

			// оформить
			var zero_weight = 0;
			var warning_main = {};
			var control_count = [];
			var ar = e.querySelectorAll('.edost_register_active')
			if (ar) for (var i = 0; i < ar.length; i++) {
				var id = edost.data(ar[i], 'code').split('_')[0];
				var warning_local = {"string": []};
				var register_active = false;
				var register_active2 = false;
				var batch_active = false;
				var control_active = true;
				if (ar[i].className.indexOf('_on') > 0) {
					order_count++;
					var E_main = ar[i].parentNode.parentNode.parentNode;

					var ar2 = E_main.getElementsByClassName('edost_batch_active');
					for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { batch_count++; batch_active = true; }

					var weight = 0;
					var ar2 = E_main.getElementsByClassName('edost_package_weight');
					for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { weight = ar2[i2].value; register_active = true; } else zero_weight++;

					var size = [];
					var ar2 = E_main.getElementsByClassName('edost_package_size');
					for (var i2 = 0; i2 < ar2.length; i2++) size.push(ar2[i2].value);

					s = E_main.querySelector('.edost_service_button');
					if (s) {
						var p = edost.window.get_param(s);

						var E_props = BX('edost_props_' + id);
						var props = edost.window.get_param(E_props);

						// реверс (СДЭК)
						if (E_call && p.company == 5 && edost.in_array(48, p.service) && (!E_shop || !E_option.getAttribute('data-flag_reverse'))) warning_local.reverse = true;

						// SMS (Почта)
						if (props.phone == '' && p.company == 23 && edost.in_array(1, p.service)) warning_local.sms = true;
					}

					var e2 = BX('edost_package_error_' + id);
					if (e2) {
						if (e2.getAttribute('data-box')*1 > 1) {
							var s = e2.getAttribute('data-error');
							if (s != '') warning_local[s] = true;
							else {
								register_active = true;
								register_active2 = true;
							}
						}
						else {
							var s = true;
							var tariff = e2.getAttribute('data-tariff');
							if (tariff != undefined) s = edost.admin.check_package(tariff, weight, size, oversize);
							if (s === true) register_active2 = true; else warning_add = s[1];
							e2.innerHTML = (s === true ? '' : '<div style="padding-top: 5px;">' + s[0] + '</div>');
						}
					}

					var c = ar[i].getAttribute('data-control').split('_');
					var k = -1;
					for (var i2 = 0; i2 < control_count.length; i2++) if (control_count[i2][0] == c[0]) { k = i2; break; }
					if (k == -1) { control_count.push([c[0], c[1], 0]); k = 0; }
					control_count[k][2]++;
					if (control_count[k][1] < control_count[k][2]) control_active = false;
				}

				for (var k in warning_local) warning_main[k] = warning_local[k];
				if (warning_local.reverse) warning_local.string.push('не заполнены данные в профиле магазина');
				if (warning_local.sms) warning_local.string.push('не указан телефон покупателя');
				if (warning_local.package_pack) warning_local.string.push('распределение по местам не завершено');
				if (warning_local.package_weight) warning_local.string.push('не задан вес мест');

				H('edost_register_warning_' + id, warning_local.string.join('<br>'));

				self.local_button_active([[id, 'register', control_active && register_active && register_active2 && (call && batch_date || !call && (!batch_active || batch_date)) && warning_local.string.length == 0]]);
			}

			var control_active = true;
			for (var i2 = 0; i2 < control_count.length; i2++) if (control_count[i2][1] < control_count[i2][2]) {
				control_active = false;
				warning.push('докупить заказов: <a class="edost_link2" href="http://edost.ru/shop_edit.php?p=5&s=' + control_count[i2][0] + '" target="_blank">' + (control_count[i2][2] - control_count[i2][1]) + '</a>');
				break;
			}

			if (zero_weight > 0) warning.push('заполнить ячейки с весом отправлений');
			if (warning_main.package_pack) warning.push('завершить распределение товаров по местам');
			if (warning_main.package_weight) warning.push('задать вес у мест отправлений');
			if (warning_add != '') warning.push(warning_add);
			if (warning_main.reverse) warning.push('для услуги "реверс", заполнить в профиле магазина наименование юр.лица, адрес, телефон и указать ФИО сотрудника, отвечающего за сдачу/приемку груза ' + profile_button);
			if (warning_main.sms) warning.push('для услуги "SMS уведомление получателя", указать телефон покупателя');

			self.button_active(['button_register', order_count == 0 || !control_active || call && !batch_date || warning.length > 0 ? 0 : 1]);
			self.button_active(['button_register_print', order_count == 0 ? 0 : 1]);
		}

		// на сдачу
		var e = BX('edost_shipment_register_complete');
		if (e) {
			var button_batch_active = false;
			var ar = e.getElementsByClassName('edost_batch_active');
			for (var i = 0; i < ar.length; i++) {
				var c = ar[i].getAttribute('data-code');
				id = c.split('_')[0];

				var batch_active = false;
				if (ar[i].className.indexOf('_on') > 0) {
					order_count++;
					batch_count++;
					button_batch_active = batch_active = true;
				}

				var print_active = false;
				var ar2 = ar[i].parentNode.parentNode.parentNode.getElementsByClassName('edost_doc');
				for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { print_active = true; break }

				self.local_button_active([[id, 'print', print_active], [id, 'batch', batch_active && batch_date]]);
			}
			self.button_active(['button_batch', button_batch_active && batch_date ? 1 : 0]);
		}

		// регистрация в отделении
		var e = BX('edost_shipment_register_complete_batch');
		if (e) {
			var button_office_active = false;
			var ar = e.getElementsByClassName('edost_batch_office');
			for (var i = 0; i < ar.length; i++) if (ar[i].checked) button_office_active = true;
			self.button_active(['button_office', button_office_active ? 1 : 0]);
		}

		// попробовать оформить еще раз
		var e = BX('edost_shipment_warning_red');
		if (e) {
			var a = false;
			var ar = e.getElementsByClassName('edost_shipment');
			for (var i = 0; i < ar.length; i++) if (ar[i].checked) { a = true; break; }
			self.button_active(['button_register_repeat', a ? 1 : 0]);
		}

		if (!batch_date && (batch_count != 0 || call)) warning.push('ввести корректную дату сдачи');

		var e = BX('edost_warning');
		if (e) {
			e.style.display = (warning.length > 0 ? 'block' : 'none');
			if (warning.length > 0) {
				for (var i = 0; i < warning.length; i++) warning[i] = '<span style="color: #555;">' + (warning.length > 1 ? '<b>' + (i+1) + '.</b> ' : '') + warning[i] + '</span>';
				e.innerHTML = '<b>для оформления доставки требуется:</b><div style="height: 5px;"></div>' + warning.join('<div style="height: 5px;"></div>');
			}
		}

		// сдачи почты
		if (!call) {
			var e = BX('edost_batch_div');
			if (e) {
				var ar = document.getElementsByClassName('edost_batch_active');
				for (var i = 0; i < ar.length; i++) if (ar[i].className.indexOf('_on') > 0) { batch_div = true; break; }
				e.style.display = (batch_div ? 'block' : 'none');

				var batch_E = BX('edost_batch');
				if (batch_E) {
					var e = BX('edost_batch_reset_div');
					e.style.display = (!batch_div && batch_E.value != 'new' ? 'block' : 'none');
				}
			}
		}

		for (var u = 0; u < 2; u++) {
			var e = BX('edost_shipment_register_complete_batch' + (u == 1 ? '_full' : ''));
			if (!e) continue;
			// распечатать (локальная кнопка)
			var ar = e.getElementsByClassName('edost_shipment');
			for (var i = 0; i < ar.length; i++) {
				var id = ar[i].id.split('edost_shipment_')[1];

				var a = false;
				var ar2 = ar[i].parentNode.parentNode.getElementsByClassName('edost_doc');
				for (var i2 = 0; i2 < ar2.length; i2++) if (ar2[i2].className.indexOf('_on') > 0) { a = true; break }

				var e2 = BX('edost_register_button_' + id + '_print');
				if (e2) e2.style.display = (a ? 'block' : 'none');
			}
		}

		self.batch_update();
	}

	// включение заказов в новую или существующую сдачу
	this.batch_update = function(value, value2) {

		var batch_E = BX('edost_batch');
		if (!batch_E) return;
		var e = batch_E.options[batch_E.selectedIndex];
		if (!e) return;

		var v = e.value;
		var o = edost.data(e, 'order');
		if (o != undefined) o = o.split(',');

		var e2 = BX('edost_batch_date_span');
		if (e2) e2.style.display = (v == 'new' ? 'inline' : 'none');

		var order = [];
		var order_active = [];
		var ar = document.getElementsByClassName('edost_batch_active');
		for (var i = 0; i < ar.length; i++) if (ar[i].className.indexOf('_on') > 0 || ar[i].className.indexOf('_off') > 0) {
			var c = ar[i].getAttribute('data-code').split('_')[0];
			order.push(c);
			if (ar[i].className.indexOf('_on') > 0) order_active.push(c);
		}

		for (var i = 0; i < batch_E.options.length; i++) {
			var s = batch_E.options[i].getAttribute('data-order');
			if (s == undefined) continue;
			s = s.split(',');
			var a = false;
			for (var i2 = 0; i2 < s.length; i2++) {
				for (var i3 = 0; i3 < order_active.length; i3++) if (order_active[i3] == s[i2]) { a = true; break; }
				if (a) break;
			}
			batch_E.options[i].style.display = (a ? 'block' : 'none');
		}

		for (var i = 0; i < order.length; i++) {
			var e2 = BX('edost_shipment_' + order[i]);
			var e3 = BX('edost_batch_disabled_' + order[i]);
			var a = (v == 'new' ? true : false);
			if (!a) for (var i2 = 0; i2 < o.length; i2++) if (o[i2] == order[i]) { a = true; break; }
			if (!a && e2.checked) {
				e2.checked = false;
				self.active_main('edost_shipment_' + order[i]);
			}
			if (value == 'set') if (a && !e2.checked) {
				e2.checked = true;
				self.active_main('edost_shipment_' + order[i]);
			}
			e3.style.display = (!a ? 'block' : 'none');
		}
	}

	this.active_all = function(value, value2) {
		var e = BX(value);
		var a2 = (e.checked ? true : false);
		var id = e.id.substr(0, e.id.length - 7);

		// глобальная галочки выделения
		var ar = BX.findChildren(BX(id), {'tag': 'input', 'attribute': {'type': 'checkbox'}}, true);
		for (var i = 0; i < ar.length; i++) {
			ar[i].checked = a2;
			self.active_main(ar[i].id, true);
		}

		var e = BX(value.id.split('_active')[0]);
		if (e) self.active_all_update(e);
	}

	this.active_batch_all = function(value, value2) {
		var e = BX(value);
		var a2 = (e.checked ? true : false);

		// галочка сдачи
		var ar = document.getElementsByName(value);
		if (ar) for (var i = 0; i < ar.length; i++) {
			var e = BX('edost_shipment_' + ar[i].value);
			e.checked = a2;
			self.active_main(e.id, true);
		}

		self.active_all_update();
	}

	this.active_main = function(value, value2) {
		// галочка заказа
		var e = E(value);
		var a2 = (e.checked ? true : false);
		var id = e.id.split('_')[2];

		if (e.id.indexOf('edost_shipment') == 0) {
			var ar = e.parentNode.parentNode.children[2].getElementsByTagName('IMG');
			for (var i = 0; i < ar.length; i++) self.active_change(ar[i], a2 ? 'on' : 'off');

			var ar = e.parentNode.parentNode.children[2].getElementsByClassName('edost_package');
			for (var i = 0; i < ar.length; i++) ar[i].readOnly = (!a2 ? true : false);
		}

		if (!value2) {
			var e = e.parentNode.parentNode.parentNode.parentNode.parentNode;
			self.active_all_update(e);
		}
	}

	// включение/выключение команд (печать бланков, отгрузка, оформление доставки)
	this.active = function(value, value2) {

		var active = self.active_change(value);

		var c = value.getAttribute('data-code');
		var s = c.split('_');
		var shipment = s[0];
		var name = s[1];
		var required = (BX('register_required_' + shipment) ? true : false)
		var id = 'edost_shipment_' + shipment;

		var a = true;
		var ar = value.parentNode.parentNode.getElementsByTagName('IMG');
		for (var i = 0; i < ar.length; i++) {
			var active2 = (ar[i].className.indexOf('_on') > 0 ? true : false);
			var s = ar[i].getAttribute('data-code');
			var name2 = (s ? s.split('_')[1] : '');

			if (name == 'register' && name2 == 'doc' && (active && !active2 || !active && active2) ||
				name == 'register' && name2 == 'batch' && (active && !active2 || !active && active2) ||
				name == 'doc' && required && name2 == 'register' && active && !active2 ||
				name == 'batch' && name2 == 'register' && active && !active2 ||
				name == 'batch' && name2 == 'doc' && (active && !active2)
				) active2 = self.active_change(ar[i]);

			if (active2) a = false;
		}

		var a2 = false;
		var e = BX(id);
		if (active) e.checked = true;
		else if (a) e.checked = false;

		var ar = e.parentNode.parentNode.children[2].getElementsByClassName('edost_package');
		for (var i = 0; i < ar.length; i++) ar[i].readOnly = (!e.checked ? true : false);

		var e = value.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
		self.active_all_update(e);
	}

	this.get_doc = function(value, value2) {

		var r = {'normal': [], 'label': [], 'count': 0, 'full': false};
		ar = [];
		if (value === 'all') {
			id = ['edost_shipment_register_complete', 'edost_shipment_register_complete_batch', 'edost_shipment_register_complete_batch_full'];
			for (var i = 0; i < id.length; i++) {
				var e = BX(id[i]);
				if (!e) continue;

				doc = e.getElementsByClassName('edost_doc');
				for (var i2 = 0; i2 < doc.length; i2++) ar.push(doc[i2]);

				if (id[i] == 'edost_shipment_register_complete_batch_full') {
					doc = e.getElementsByClassName('edost_batch');
					for (var i2 = 0; i2 < doc.length; i2++) if (doc[i2].checked) {
						r.normal.push(doc[i2].id.split('edost_batch_')[1] + '_103');
						r.count++;
					}
				}
			}
		}
		else if (value === 'print_no_register') {
			var e = BX('edost_shipment_register_new');
			if (e) ar = e.getElementsByClassName('edost_doc_print_no_register');
		}
		else {
			var e = BX('edost_shipment_' + value).parentNode.parentNode;
			ar = e.getElementsByClassName('edost_doc');
		}
		for (var i = 0; i < ar.length; i++) if (ar[i].className.indexOf('_on') > 0) {
			var c = ar[i].getAttribute('data-code').split('_');
			var m = ar[i].getAttribute('data-mode');
			if (c[2] == 'order' || c[2] == 'package') r.full = true;
			var id = c[0] + '_' + c[2];
			if (m == 'normal') r.normal.push(id); else r.label.push(id);
			r.count++;
		}
		return r;
	}

	this.search = function(value, value2) {
		edost_search_value = value;
		edost.admin.set_param('register', 'search_shipment');
	}

	this.get_print_link = function(value, value2) {
		var link = '<a class="edost_print_link" href="/bitrix/admin/edost.php?type=print&mode=%mode%&doc=%doc%" target="_blank">%name%<sup style="font-size: 12px;">%count%</sup></a>';
		var s = [];
		if (value[0].normal == undefined) s.push(link.replace(/%doc%/g, value[0].join('|')).replace(/%mode%/g, 'normal').replace(/%name%/g, value[1]).replace(/%count%/g, value[0].length > 1 ? ' ' + value[0].length + '' : ''));
		else {
			if (value[0].normal.length != 0) s.push(link.replace(/%doc%/g, value[0].normal.join('|')).replace(/%mode%/g, 'normal').replace(/%name%/g, value[1]).replace(/%count%/g, value[0].normal.length > 1 ? ' ' + value[0].normal.length + '' : ''));
			if (value[0].label.length != 0) s.push(link.replace(/%doc%/g, value[0].label.join('|')).replace(/%mode%/g, 'label').replace(/%name%/g, value[2] != undefined ? value[2] : 'ярлыки').replace(/%count%/g, value[0].label.length > 1 ? ' ' + value[0].label.length + '' : ''));
		}
		if (s.length > 1) return '<div>' + s.join(' / ') + ' <span style="font-size: 15px; color: #AAA;">(<span style="font-size: 15px; font-weight: normal;" class="edost_control_button edost_control_button_low" onclick="edost.register.print([this, \'link\'])">открыть</span>)</span></div>';
		else return s[0];
	}

	this.print = function(value, value2) {
		var id = (value[0] != undefined ? value[0] : false);
		var type = (value[1] != undefined ? value[1] : '');
		var name = (value[2] != undefined ? value[2] : '');
		var full = false;

		var s = [[], []];

		if (type == 'batch' || type == 'batch_general') s[0].push(id + '_' + (name ? name : 'batch'));
		if (type == 'batch_order') full = true;

		if (type == 'link') {
			var e = id.parentNode.parentNode;
			if (!e) return;

			var ar = e.getElementsByTagName('A');
			if (ar) for (var i = 0; i < ar.length; i++) ar[i].click();

			return;
		}

		if (type == '') {
			c = self.get_doc(id);
			s[0] = c.normal;
			s[1] = c.label;
			full = c.full;
		}
		else if (type == 'batch' || type == 'batch_order') {
			var ar = document.getElementsByName('edost_batch_' + id);
			if (ar) for (var i = 0; i < ar.length; i++) {
				var c = self.get_doc(ar[i].value);
				if (c.normal.length > 0) s[0] = (s[0].length == 0 ? c.normal : s[0].concat(c.normal));
				if (c.label.length > 0) s[1] = (s[1].length == 0 ? c.label : s[1].concat(c.label));
			}
		}

		if (s[0].length == 0 && s[1].length == 0) alert('не выбрано ни одного бланка для печати');
		else for (var i = 0; i < s.length; i++) if (s[i].length > 0) {
			window.open('/bitrix/admin/edost.php?type=print&mode=' + (i == 0 ? 'normal' : 'label') + '&doc=' + s[i].join('|'), '_blank');
			if (id !== 'print_no_register' && !full) break; // отключение печати по типам !!!
		}
	}

	this.button = function(value, value2) {

		var s = value.split('|');
		var value = s[0];
		var id = (s[1] ? s[1] : false);
		var set = '', post = '';

		if (value != undefined) {
			var s = value.split('button_');
			post += '&button=' + (s[1] != undefined ? s[1] : s[0]);
		}

		if (value == 'button_register' || value == 'button_batch') {
			var ar = ['batch', 'batch_date', 'call', 'profile_shop', 'profile_delivery'];
			for (var i = 0; i < ar.length; i++) {
				var e = BX('edost_' + ar[i]);
				if (e) {
					post += '&' + ar[i] + '=';
					if (e.type == 'checkbox') post += (e.checked ? 'Y' : 'N');
					else post += e.value;
				}
			}
		}

		if (value == 'update') {
			set = ajax_set;
			post += '&count=' + (transfer_position - 1);
		}
		else {
			var s = [];

			if (id) {
				var e = BX('edost_shipment_' + id);
				if (e) e = e.parentNode.parentNode;
			}
			else if (value == 'button_register_repeat') var e = BX('edost_shipment_warning_red');
			else if (value == 'button_batch') var e = BX('edost_shipment_register_complete');
			else if (value == 'button_office') var e = BX('edost_shipment_register_complete_batch');
			else e = BX('edost_shipment_register_new');

			if (value == 'button_date') {
				s.push(id + '_date');
				post += '&date=' + encodeURIComponent(value2);
			}
			else if (value == 'button_office') {
				if (id !== false) s.push(id + '_office');
				else {
					var ar = e.getElementsByClassName('edost_batch_office');
					for (var i = 0; i < ar.length; i++) if (ar[i].checked) s.push(ar[i].id.split('edost_batch_')[1] + '_office');
				}
			}
			else if (value == 'button_print_no_register') {
				var doc = self.get_doc('print_no_register');
				for (var i = 0; i < doc.normal.length; i++) s.push(doc.normal[i].replace('_', '_doc_'));
				for (var i = 0; i < doc.label.length; i++) s.push(doc.label[i].replace('_', '_doc_'));
			}
			else {
				if (value == 'button_register_repeat') {
					var ar = e.getElementsByClassName('edost_register_repeat');
					for (var i = 0; i < ar.length; i++) {
						var c = ar[i].getAttribute('data-code');
						var c2 = c.split('_');
						var e2 = BX('edost_shipment_' + c2[0]);
						if (e2 && e2.checked) s.push(c);
					}
				}
				else {
					var ar = e.getElementsByClassName('edost_register_on');
					for (var i = 0; i < ar.length; i++) {
						var c = ar[i].getAttribute('data-code');
						s.push(c);
					}
				}
				var ar = e.getElementsByClassName('edost_package_on');
				for (var i = 0; i < ar.length; i++) if (!ar[i].readOnly) {
					var c = ar[i].getAttribute('data-code');
					s.push(c + '_' + ar[i].value.replace(/,/g, '.').replace(/[^0-9.]/g, ''));
				}
			}
			ajax_set = set = s.join('|');
		}
//		alert(set + ' | ' + post);

		var date = new Date();
		var ar = [date.getDate(), date.getMonth() + 1, date.getFullYear(), date.getHours(), date.getMinutes()];
		for (var i = 0; i < ar.length; i++) if (ar[i] < 10) ar[i] = '0' + ar[i];
		post += '&time=' + encodeURIComponent(ar.slice(0, 3).join('.') + ' ' + ar.slice(3, 5).join(':'));

		var status = '';
		if (value == 'button_register' || value == 'button_register_repeat' || value == 'button_batch' || value == 'button_office' || value == 'button_date') {
			status = 'transfer_end';
			self.transfer_start();

			var e = BX('edost_batch_div');
			if (e) e.style.display = 'none';
		}

		if (value == 'button_print_no_register') self.button_active(['button_print_no_register', 0]);

		edost.admin.post('admin', 'type=register&ajax=Y&set=' + set + post, function(res) {
			if (transfer_set == -2) return;

			if (res == '') return;
			res = (window.JSON && window.JSON.parse ? JSON.parse(res) : eval('(' + res + ')'));

			if (res.history) {
				transfer_history = true;
				BX('edost_history_select').innerHTML = res.history;
				BX('edost_history_div').style.display = 'block';
			}

			if (res.error) {
				self.transfer_stop(res.error);
				return;
			}

			if (value == 'button_print_no_register') {
				self.button_active(['button_print_no_register', 1]);
				self.print(['print_no_register']);
			}

			if (status == 'transfer_end') self.transfer_status(['transfer_end']);
			if (value == 'update' && (res.register_full != undefined || transfer_position >= 3)) self.transfer_status(['receive']);

			if (res.register_full != undefined) {
				var s = new Date();
				transfer_time_end = s.getTime();
			}

			transfer_set = transfer_position;
		});
	}

}


// вывод блока после загрузки всех скриптов
edost.D(['#adm-workarea #edost_data_div'], true);


// выбор пункта выдачи
function edost_SetOffice(profile, id, cod, mode) {

	edost.admin.alarm = false;

	var f = (edost.admin.param.crm ? edost.admin.change_delivery_crm : edost.admin.change_delivery);

	var v = '';
	if (profile === 'post_manual') { edost.admin.update_order(); return; }
	else if (id === undefined) { f('office_esc'); return; }

	v = profile.split('_');
	if (v[1] == undefined) return;

	var e = BX(edost.admin.param.crm ? 'edost_profile_select' : 'PROFILE_1');
	if (!e) return;

	var E_element = e.options[e.selectedIndex];
	e.value = E_element.value = v[1];

	edost.E(E_element, {data: {'edost_profile': v[0], 'edost_office_address_full': 'new', 'edost_office_id': id}});

	var p = edost.admin.point = edost.office.point(id);
	if (p && p.city && edost.E('edost_country')) {
		edost.admin.post('ajax', 'mode=order_edit&id=' + (edost.admin.param.crm ? edost.admin.get_data_crm('full').shipment_id : edost.value('SHIPMENT_ID_1')) + '&office_id=' + id + '&profile=' + v[0], function(r) {
			var p = edost.admin.point;
			var a = (p.mode == 'post' ? true : false);
			edost.location.set('', true, p.city, edost.value('edost_region'), edost.value('edost_country').split('_')[0], a ? false : true, p.city, a ? p.code : false);
		});
		return;
	}

	f(edost.admin.order_create ? 'reload' : true);

}


// поддержка edost.locations 2.1.0
edost_UpdateDelivery = edost.admin.update_delivery;
