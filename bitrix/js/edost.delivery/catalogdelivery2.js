var edost_catalogdelivery = new function() {
	var self = this;
	var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://')
	var clear_white, size = {auto: true, x: 650, y: 80}, show_quantity, show_add_cart, add_cart, show_button, info = '', error = '<div class="edost_warning2">Расчет недоступен</div>'
	var template_format = false, template_block_type = false, template_map_inside = false
	var onkeydown_backup = 'free', window_top = 0, browser_width = 0, browser_height = 0, start_h = 0
	var inside = false
	var quantity_timer

	this.param_string = ''
	this.product_id = 0
	this.product_name = ''
	this.quantity = 1
	this.head = 'Расчет доставки'
	this.loading = ''
	this.loading_small = ''
	this.bookmark = ''
	this.location_data = '';
	this.window_width = 700
	this.window_height = 700
	this.city_backup = [['edost_catalogdelivery_inside_city', ''], ['edost_catalogdelivery_window_city', '']];


	this.get_cookie = function(name) {
		var r = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
		return (r ? decodeURIComponent(r[2]) : '');
	}

	this.set_cookie = function(value) {
		document.cookie = value + '; path=/; expires=Thu, 01-Jan-2050 00:00:01 GMT';
	}

	this.get_device = function() {

		function c(f) { return (s.indexOf(f) !== -1 ? true : false); }

		var r = '';
		var os = '';
		var s = window.navigator.userAgent.toLowerCase();

		if (c('blackberry') || c('bb10') || c('rim')) os = 'blackberry';
		else if (c('windows')) os = 'windows';
		else if (c('iphone') || c('ipod') || c('ipad')) os = 'ios';
		else if (c('android')) os = 'android';
		else if ((c('(mobile;') || c('(tablet;')) && c('; rv:')) os = 'fxos';

		if (os == 'android' && c('mobile') || os != 'windows' && c('iphone') || c('ipod') || os == 'windows' && c('phone') || os == 'blackberry' && !c('tablet') || os == 'fxos' && c('mobile') || c('meego')) self.device = 'phone';
		else if (c('ipad') || os == 'android' && !c('mobile') || os == 'blackberry' && c('tablet') || os == 'windows' && c('touch') || os == 'fxos' && c('tablet')) self.device = 'tablet';

	}


	// расчет доставки
	this.calculate = function(param, s1, s2) {

		var E = document.getElementById('edost_bookmark');
		if (E) self.bookmark = E.value;

		if (param == 'preview') {
			var E = document.getElementById('edost_catalogdelivery_inside');
			if (!E) return;

			self.set_window_param();
			self.product_id = s1;
			self.product_name = s2;

			var E = document.getElementById('edost_catalogdelivery_inside_city_head');
			if (E) E.style.display = 'none';
		}
		else if (param) {
			var a = false;
			if (param == 'loading') {
				var E = document.getElementById('edost_catalogdelivery_window_city');
				if (E && E.innerHTML == '') a = true;
			}
			if (param == 'loading_location') { a = true; param = 'loading'; }
			if (a) {
				var E = document.getElementById('edost_catalogdelivery_window_city_div');
				if (E) E.style.display = 'none';
			}

			var E = document.getElementById('edost_catalogdelivery_data');
			if (!E) return;

			if (param == 'loading')	E.innerHTML = self.loading;
			else if (param == 'error') E.innerHTML = '<div class="edost_warning edost_warning_big">' + s1 + '</div>';

			return;
		}

		start_h = 0;

		edost_GetTariff();

	}


	// расчет по таймеру
	this.timer = function(param) {

		if (param == 'start') {
			if (quantity_timer != undefined) window.clearInterval(quantity_timer);
			quantity_timer = window.setInterval("edost_GetTariff('quantity')", 300);
		}
		else {
			if (quantity_timer != undefined) quantity_timer = window.clearInterval(quantity_timer);
		}

	}


	// вывод ошибки
	this.set_error = function() {
		var E = document.getElementById('edost_catalogdelivery_window_error');
		if (E) E.innerHTML = '<div style="' + (!inside ? 'text-align: center; ' : '') + 'padding-top: 20px;">' + error + '</div>';
	}


	// параметры окна
	this.set_window_param = function(param, data) {
//		alert('set_window_param: ' + param + ' | ' + data);

		if (param == 'inside') inside = true;

		// загрузка параметров из html
		var E = document.getElementById('edost_catalogdelivery_param');
		if (E && E.value) self.param_string = E.value;

		if (!data) {
			var E = document.getElementById('edost_catalogdelivery_window_param');
			data = (E ? E.value : '');
		}
		v = data.split('|');

		size = {auto: (v[0] == 'N' ? false : true), x: (!v[1] || v[1] < 400 ? 650 : v[1]), y: (!v[2] || v[2] < 80 ? 80 : v[2])};
		clear_white = (v[3] == 'Y' ? true : false);
		show_quantity = (v[4] != undefined && v[4] == 'N' ? false : true);
		show_add_cart = (v[5] != undefined && v[5] == 'N' ? false : true);
		show_button = (v[7] != undefined && v[7] == 'Y' ? true : false);
		if (v[8]) info = v[8].replace(/&quot;/g, '"');
		if (v[9]) error = v[9].replace(/&quot;/g, '"');

		self.loading = '<div class="edost_map_loading2"><img src="' + (v[10] ? v[10] : protocol + 'edostimg.ru/img/site/loading.gif') + '" border="0" width="64" height="64"></div>';
		self.loading_small = '<img style="vertical-align: top;" src="' + (v[11] ? v[11] : protocol + 'edostimg.ru/img/site/loading_small.gif') + '" border="0" width="20" height="20">';

		template_format = (v[12] != undefined && v[12] == 'Y' ? true : false);
		template_block_type = (v[13] != undefined && v[13] == 'Y' ? true : false);
		template_map_inside = (v[14] != undefined && v[14] == 'Y' ? true : false);

		add_cart = (self.get_cookie('edost_catalogdelivery').split('|manual=')[0] == 1 ? true : false);

		self.set_error();

	}


	// заполнение блока с расчетом, города и ссылки на детальную информацию
	this.set_data = function(tariff_data, location_data, city, detailed) {
//		alert('set_data: ' + tariff_data + ' | ' + location_data);

		self.set_window_param();
		self.location_data = location_data;
		if (tariff_data === 'FALSE') self.set_error();

		var link_begin = '<span class="edost_link" onclick="window.edost_catalogdelivery.window(';
		var link_end = '</span>';

		var E = document.getElementById('edost_catalogdelivery_window_city');
		if (E) {
			if (location_data == 'GETCITY') edost_catalogdelivery.window('getcity');
			else E.innerHTML = location_data;
		}

		if (inside) return;

		var E = document.getElementById('edost_catalogdelivery_inside_city');
		if (E) E.innerHTML = link_begin + "'getcity');\">" + city + link_end;

		var E = document.getElementById('edost_catalogdelivery_inside');
		if (E) {
			E.innerHTML = (tariff_data === 'FALSE' ? error : tariff_data);
			E.style.display = (tariff_data == '' ? 'none' : 'block');
		}

		var E = document.getElementById('edost_catalogdelivery_inside_detailed');
		if (E) {
			E.innerHTML = link_begin + ');">подробнее...' + link_end;
			E.style.display = (detailed == 'Y' ? 'block' : 'none');
		}

		if (self.product_id == 0) {
			var E = document.getElementById('edost_catalogdelivery_product_id');
			if (E) self.product_id = E.value;
		}

		edost_catalogdelivery.resize(true);

	}


	this.resize = function(update) {

		if (inside) return;

		if (update) {
			var E = document.getElementById('edost_catalogdelivery_window_city_table');
			if (E) E.style.display = 'block';
			var E = document.getElementById('edost_catalogdelivery_window_info');
			if (E) E.style.display = 'block';
			var E = document.getElementById('edost_catalogdelivery_inside_city_head');
			if (E) E.style.display = 'inline';
		}

		var browser_w = (document.documentElement.clientWidth == 0 ? document.body.clientWidth : document.documentElement.clientWidth);
		var browser_h = (document.documentElement.clientHeight == 0 ? document.body.clientHeight : document.documentElement.clientHeight);

		if (browser_w == browser_width && browser_h == browser_height && update == undefined) return;

		browser_width = browser_w;
		browser_height = browser_h;

		var E = document.getElementById('edost_catalogdelivery_window');
		var E2 = document.getElementById('edost_catalogdelivery_data');
		var E_tariff = document.getElementById('edost_tariff_div');
		var E_head = document.getElementById('edost_catalogdelivery_window_head_div');
		var E_city = document.getElementById('edost_catalogdelivery_window_city_div');
		if (!(E && E2 && E.style.display != 'none')) return;

		var fullscreen = true;
		var h_max = (template_format && (template_block_type || template_map_inside) ? 782 : 520);
		self.window_width = (browser_w-40 < 750 ? browser_w-40 : 750);
		self.window_height = (browser_h-20 < h_max ? browser_h-20 : h_max);
		if (self.device != 'phone') if (browser_w > 1000 && browser_h > self.window_height + 40) fullscreen = false;
		if (!E_tariff && !fullscreen) {
			self.window_width = 580;
			self.window_height = 350;
		}

		var E5 = document.getElementById('edost_catalogdelivery_window_head_product_name');
		E5.style.display = (browser_w > 790 ? 'inline-block' : 'none');
		E5.style.maxWidth = (self.window_width - 350) + 'px';

		var E5 = document.getElementById('edost_catalogdelivery_window_head');
		E5.style.fontSize = (browser_w > 300 ? '17px' : '15px');

		var a = (browser_w > 600 ? true : false);
		E_head.className = (a ? 'edost_catalogdelivery_window_normal' : 'edost_catalogdelivery_window_small');

		var E5 = document.getElementById('edost_office_td');
		var E6 = document.getElementById('edost_office_map_div');
		var map_inside = (E5 && E6 ? true : false);

		if (E_tariff) {
			E_tariff.style.height = 'auto';
			E_tariff.style.overflowY = 'auto';
//			E_tariff.style.overflowY = 'visible';
			E_tariff.style.paddingRight = (self.device != 'phone' ? '10px' : '0');
		}
		E.style.overflowY = 'hidden';

		for (var step = 0; step <= 1; step++) {
			if (step == 0) {
				E2.style.overflowY = 'hidden';
				E2.style.height = '100%';
			}

			if (step == 1) {
				var E6 = document.getElementById('edost_bookmark_div');
				var bookmark_h = (E6 ? E6.offsetHeight : 0);
				var head_h = (E_head ? E_head.offsetHeight : 0);

				var h = E2.offsetHeight;
				var tariff_h = (E_tariff ? E_tariff.offsetHeight : 0);

				var window_w = E.offsetWidth;
				var window_h = E.offsetHeight;

				if (E_tariff) {
					var tariff_w = 580;
					var tariff_h2 = 0;
					if (map_inside) {
						var E6 = document.getElementById('edost_office_inside');
						if (E6) tariff_h2 = E6.style.height.split('px')[0]*1 + 30;
					}
					else {
						var ar = ['office', 'door', 'house', 'post', 'general', 'show'];
						for (var i = 0; i < ar.length; i++) {
							var E5 = document.getElementById('edost_' + ar[i] + '_div');
							if (!E5) continue;
							var w2 = E5.style.maxWidth.split('px')[0];
							if (w2 > tariff_w) tariff_w = w2;

							var w2 = E5.offsetHeight;
							if (w2 > tariff_h2) tariff_h2 = w2;
						}

						var E5 = document.getElementById('edost_bookmark_table');
						var bookmark_w = (E5 ? E5.offsetWidth : 0);
						if (bookmark_w > tariff_w) tariff_w = bookmark_w;
					}

					var w2 = 0;
					if (tariff_h2 > 0 && bookmark_h > 0) w2 = tariff_h2;
					else if (tariff_h > 0) w2 = tariff_h;
					else w2 = 700;
					w2 += bookmark_h + head_h + (fullscreen ? 50 : 40);
					if (w2 > 700 && !map_inside || w2 > 782 && map_inside) w2 = 782;
					if (!fullscreen && w2 > h_max && w2 > browser_h-20) w2 = h_max;

					if (tariff_w < 580) tariff_w = 580;
					if (bookmark_h > 0 && tariff_w < 730) tariff_w = 730;

					self.window_width = window_w = (map_inside ? 750 : tariff_w);
					self.window_height = window_h = w2;
				}

				if (!fullscreen) {
					var E6 = document.getElementById('edost_catalogdelivery_window_city_head');
					var w5 = (E6 ? E6.offsetWidth : 0);

					var E6 = document.getElementById('edost_catalogdelivery_window_city');
					if (E6) E6.style.maxWidth = (window_w - w5 - 60) + 'px';
				}
			}

			if (!fullscreen) {
				E.classList.add('edost_catalogdelivery_window2_border');
				E.style.width = self.window_width + 'px';
				E.style.height = self.window_height + 'px';
				E.style.left = Math.round((browser_w - self.window_width)*0.5) + 'px';

				if (step == 1) {
					var x = Math.round((browser_h - self.window_height)*0.5);
    	            if (start_h != 0 && Math.abs(start_h - x) < 80 && (start_h + self.window_height < browser_h-20)) x = start_h;
					E.style.top = x + 'px';
					start_h = x;
				}

				if (step == 1) browser_h = self.window_height;

				if (clear_white) {
					E_head.style.width = E2.style.width = 'auto';
					E_head.style.margin = E2.style.margin = '0';
				}
				else {
					E_city.style.width = E2.style.width = 'auto';
					E_city.style.margin = E2.style.margin = '0';
				}

				if (E_tariff) E_tariff.className = '';
			}
			else {
				E.classList.remove('edost_catalogdelivery_window2_border');
				E.style.width = 'auto';
				E.style.height = 'auto';
				E.style.left = 0;
				E.style.top = 0;

				if (browser_w - 50 < self.window_width) {
					if (clear_white) {
						E_head.style.width = E2.style.width = 'auto';
						E_head.style.margin = E2.style.margin = '0';
					}
					else {
						E_city.style.width = E2.style.width = 'auto';
						E_city.style.margin = E2.style.margin = '0';
					}

					if (E_tariff) E_tariff.className = '';
				}
				else {
					if (clear_white) {
						E_head.style.width = E2.style.width = self.window_width + 'px';
						E_head.style.margin = E2.style.margin = '0 auto';
					}
					else {
						E_city.style.width = E2.style.width = self.window_width + 'px';
						E_city.style.margin = E2.style.margin = '0 auto';
					}

					if (E_tariff) E_tariff.className = 'edost_catalogdelivery_window_fullscreen_normal';
				}
			}

			if (step == 0 && window.edost_resize) edost_resize.update();
			else {
				var h2 = browser_h - bookmark_h - head_h - (fullscreen ? 50 : 40);
				var h3 = browser_h - head_h - (fullscreen ? 50 : 40);
				var window_h2 = h2;

				if (E_tariff) {
					var max_h = browser_h - 100;

					if (tariff_h > window_h2)
						if (window_h2 > 200) {
							E2.style.overflowY = 'hidden';
							E_tariff.style.height = (h2*1 + 10)*1 + 'px';
						}
						else {
							E2.style.overflowY = 'auto';
							E2.style.height = (window_h2*1 + bookmark_h*1 + 25) + 'px';
						}
				}
			}
		}

	}


	this.window = function(param, product_id, product_name) {
//		alert(param + ' - ' + product_id);

		if (param == 'getcity') {
			if (window.edost_ExternalLocation) { edost_ExternalLocation(); return; }
			if (edost_LoadLocation('edost_location')) return;
		}

		if (self.loading == '') self.set_window_param();

		if (product_id == undefined) product_id = '';
		if (product_name == undefined) product_name = '';

		if (param == 'esc') param = 'close';
		if (param == 'inside') {
			inside = true;
			param = '';
		}

		if (inside) {
			var E = document.getElementById('edost_catalogdelivery_window_fon');
			if (E) {
				E.remove();
				var E = document.getElementById('edost_catalogdelivery_window');
				if (E) E.remove();
			}
		}
		else {
			if (onkeydown_backup === 'free') {
			    onkeydown_backup = document.onkeydown;
				document.onkeydown = new Function('event', 'if (event.keyCode == 27) edost_catalogdelivery.window("esc");');
			}
			else if (param == 'close') {
			    document.onkeydown = onkeydown_backup;
			    onkeydown_backup = 'free';
			}
		}

		// интеграция окна
		var E = document.getElementById('edost_catalogdelivery_window');
		if (!E) {
			if (!inside) {
				var E = document.body;
				var E2 = document.createElement('DIV');
				E2.className = 'edost_catalogdelivery_window_fon';
				E2.id = 'edost_catalogdelivery_window_fon';
				E2.style.display = 'none';
				E2.onclick = new Function('', 'edost_catalogdelivery.window("close")');
				E.appendChild(E2);
			}
			else var E = document.getElementById('edost_catalogdelivery_inside');

			var E2 = document.createElement('DIV');
			if (!inside) E2.className = 'edost_catalogdelivery_window2';
			E2.id = 'edost_catalogdelivery_window';
			E2.style.display = 'none';

			var s = '';
			s += '<div id="edost_catalogdelivery_window_head_div" class="edost_catalogdelivery_window_normal" style="padding-bottom: 10px;">';

			if (!inside) {
				s += '<div style="' + (!clear_white ? 'height: 28px; ' : '') + 'text-align: center; padding-bottom: 5px;" class="edost_catalogdelivery_window_head">';
				s += '<div class="edost_catalogdelivery_window_close2" onclick="window.edost_catalogdelivery.window(\'close\')"></div>';
				s += '<span id="edost_catalogdelivery_window_head"></span>';
				s += '</div>';
			}

			if (info) s += '<div style="text-align: center; padding: ' + (clear_white ? '0px 10px 5px 10px;' : '10px 10px 0px 10px;') + '"><div id="edost_catalogdelivery_window_info">' + info + '</div></div>';

			s += '<div id="edost_catalogdelivery_window_city_div" class="edost_catalogdelivery_window_city_div" style="padding: ' + (clear_white ? 0 : 10)  + 'px 10px 0 10px;">';

			s += '<div id="edost_catalogdelivery_window_city_head" style="margin: 2px 5px 0 0; text-align: right; ' + (show_quantity || show_add_cart ? '' : 'color: #AAA;') + '">Местоположение:</div>';
			s += '<div id="edost_catalogdelivery_window_city" style="">' + self.location_data + '</div>';

			if (show_quantity || show_add_cart) {
				s += '<div id="edost_catalogdelivery_window_quantity_div" style="padding-top: 5px;">';
				if (show_quantity) s += '<div id="edost_catalogdelivery_window_quantity_head" style="display: inline-block; margin: 2px 5px 0 0; text-align: right;">Количество:</div>';
				if (show_quantity) s += '<div style="display: inline-block;"><input id="edost_catalogdelivery_quantity" value="1" size="4" style="vertical-align: middle; width: 40px;" onfocus="edost_catalogdelivery.timer(\'start\')" onblur="edost_catalogdelivery.timer(\'\')"></div>';
				if (show_add_cart) s += '<div style="display: inline-block;"><input type="checkbox" id="edost_catalogdelivery_cart"' + (add_cart ? ' checked=""': '') + ' style="margin-left: ' + (show_quantity ? '30' : '0') + 'px; vertical-align: middle;" onclick="edost_GetTariff();"> <label style="vertical-align: middle;" for="edost_catalogdelivery_cart"><span id="edost_catalogdelivery_cart_name">Учитывать товары в корзине</span><span id="edost_catalogdelivery_cart_name2">корзина</span></label></div>';
				s += '</div>';
			}

			s += '</div>';

			s += '</div>';

			s += '<div id="edost_catalogdelivery_data" style="padding: 8px 8px 8px 8px;">';
			s += '<input type="hidden" id="edost_catalogdelivery_window_no_data" value="">';
			s += '</div>';

			if (show_button && !inside) {
			s += '<div class="edost_catalogdelivery_button edost_catalogdelivery_button_bar" style="width: 120px;" onclick="window.edost_catalogdelivery.window(\'close\');">Закрыть</div>';
			if (show_quantity || show_add_cart) s += '<div class="edost_catalogdelivery_button edost_catalogdelivery_button_bar" style="width: 150px;" onclick="edost_GetTariff();">Пересчитать</div>';
			}

			E2.innerHTML = s;

			if (E) {
				if (inside) E.innerHTML = '';
				E.appendChild(E2);
			}
		}

		var display = (param == 'close' ? 'none' : 'block');
		if (inside) display = 'block';

		var E = document.getElementById('edost_catalogdelivery_window');
		if (!E) return;
		E.style.display = display;

		var E = document.getElementById('edost_catalogdelivery_window_fon');
		if (E) E.style.display = display;

		if (param == 'close') return;

		if (product_name == '') {
			var E = document.getElementById('edost_catalogdelivery_product_name');
			if (E) product_name = E.value;
		}
		if (product_name == '') product_name = self.product_name;
		if (product_name != '') {
			self.product_name = product_name;
			var E = document.getElementById('edost_catalogdelivery_window_head');
			if (E) {
//				var s = (clear_white ? 50 : 70);
//				if (product_name.length > s) product_name = product_name.substring(0, s) + '...';
//				E.innerHTML = (clear_white ? '<span style="display: inline-block; overflow: hidden;">' + self.head + '</span>' : '') + '<span id="edost_catalogdelivery_window_head_product_name">' + (clear_white ? ': ' : '') + product_name + '</span>';
				E.innerHTML = '<span style="display: inline-block; overflow: hidden;">' + self.head + '</span><span id="edost_catalogdelivery_window_head_product_name">: ' + product_name + '</span>';
			}
		}

		var product_id_old = self.product_id;
		if (product_id == '') {
			var E = document.getElementById('edost_catalogdelivery_product_id');
			if (E) product_id = E.value;
		}
		if (product_id != '') self.product_id = product_id;


		if (param == 'getcity') {
			document.getElementById('edost_catalogdelivery_data').innerHTML = '';
			edost_LoadLocation('start');
		}
		else {
			var E = document.getElementById('edost_catalogdelivery_window_no_data');
			if (E || product_id_old > 0 && self.product_id != product_id_old) {
//				var E = document.getElementById('edost_catalogdelivery_LOCATION');
				var E = document.getElementById('edost_shop_LOCATION');
				if (!E) {
					var E = document.getElementById('edost_catalogdelivery_window_city_table');
					if (E) E.style.display = 'none';
					var E = document.getElementById('edost_catalogdelivery_window_info');
					if (E) E.style.display = 'none';
				}

				edost_GetTariff();
			}
		}

		var E = document.getElementById('edost_office_div');
		if (E && E.style.display != 'none' && window.edost_office && edost_office.map) edost_office.map.container.fitToViewport();

		if (!inside) {
			self.resize(true);
			timer = window.setInterval("window.edost_catalogdelivery.resize()", 200);
		}

	}

}

edost_catalogdelivery.get_device();

// поддержка старых функций
function edost_catalogdelivery_show(product_id, product_name, param) { edost_catalogdelivery.window(param, product_id, product_name) }