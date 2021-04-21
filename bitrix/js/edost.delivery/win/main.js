var edost = new function() {
	var self = this;
	var js_path = 'edostimg.ru/shop/';
	var key_map = {"q":"й","w":"ц","e":"у","r":"к","t":"е","y":"н","u":"г","i":"ш","o":"щ","p":"з","[":"х","]":"ъ","a":"ф","s":"ы","d":"в","f":"а","g":"п","h":"р","j":"о","k":"л","l":"д",";":"ж","\'":"э","z":"я","x":"ч","c":"с","v":"м","b":"и","n":"т","m":"ь",",":"б",".":"ю","/":"."}
	var mask_country = -1

	this.backup_data = {}
	this.scroll_data = {}
	this.protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://')
	this.close = '<div class="edost_window_close" onclick="%onclick%"><svg class="edost_window_close" viewBox="0 0 88 88" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><g transform="matrix(1,0,0,1,-20.116,-20.4011)"><g transform="matrix(0.707107,0.707107,-0.707107,0.707107,64.0579,-26.3666)"><path d="M52.044,52.204l0,-37.983l23.875,0l0,37.983l37.858,0l0,23.875l-37.858,0l0,37.983l-23.875,0l0,-37.983l-38.108,0l0,-23.875l38.108,0Z" style="fill:rgb(145,145,145);"/></g></g></svg></div>'
	this.loading_svg = '<svg class="edost_loading" viewBox="0 0 256 256" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><path class="edost_loading_anim_1" d="M128,4C134.627,4 140,9.373 140,16L140,52C140,58.627 134.627,64 128,64C121.373,64 116,58.627 116,52L116,16C116,9.373 121.373,4 128,4Z"/><path class="edost_loading_anim_2" d="M191.305,20.998C197.044,24.312 199.011,31.651 195.697,37.39L177.697,68.567C174.383,74.307 167.044,76.273 161.305,72.959C155.565,69.646 153.599,62.307 156.912,56.567L174.912,25.39C178.226,19.651 185.565,17.684 191.305,20.998Z"/><path class="edost_loading_anim_3" d="M236.282,65.973C239.595,71.713 237.629,79.052 231.889,82.366L200.712,100.366C194.973,103.679 187.634,101.713 184.32,95.973C181.006,90.234 182.973,82.895 188.712,79.581L219.889,61.581C225.629,58.267 232.968,60.234 236.282,65.973Z"/><path class="edost_loading_anim_4" d="M252,128C252,134.627 246.627,140 240,140L204,140C197.373,140 192,134.627 192,128C192,121.373 197.373,116 204,116L240,116C246.627,116 252,121.373 252,128Z"/><path class="edost_loading_anim_5" d="M235.981,191C232.667,196.74 225.328,198.706 219.588,195.392L188.412,177.392C182.672,174.079 180.706,166.74 184.019,161C187.333,155.26 194.672,153.294 200.412,156.608L231.588,174.608C237.328,177.921 239.294,185.26 235.981,191Z"/><path class="edost_loading_anim_6" d="M189,235.981C183.26,239.294 175.921,237.328 172.608,231.588L154.608,200.412C151.294,194.672 153.26,187.333 159,184.019C164.74,180.706 172.079,182.672 175.392,188.412L193.392,219.588C196.706,225.328 194.74,232.667 189,235.981Z"/><path class="edost_loading_anim_7" d="M128,252C121.373,252 116,246.627 116,240L116,204C116,197.373 121.373,192 128,192C134.627,192 140,197.373 140,204L140,240C140,246.627 134.627,252 128,252Z"/><path class="edost_loading_anim_8" d="M65,235.981C59.26,232.667 57.294,225.328 60.608,219.588L78.608,188.412C81.921,182.672 89.26,180.706 95,184.019C100.74,187.333 102.706,194.672 99.392,200.412L81.392,231.588C78.079,237.328 70.74,239.294 65,235.981Z"/><path class="edost_loading_anim_9" d="M20.019,189C16.706,183.26 18.672,175.921 24.412,172.608L55.588,154.608C61.328,151.294 68.667,153.26 71.981,159C75.294,164.74 73.328,172.079 67.588,175.392L36.412,193.392C30.672,196.706 23.333,194.74 20.019,189Z"/><path class="edost_loading_anim_10" d="M4,128C4,121.373 9.373,116 16,116L52,116C58.627,116 64,121.373 64,128C64,134.627 58.627,140 52,140L16,140C9.373,140 4,134.627 4,128Z"/><path class="edost_loading_anim_11" d="M20.019,67C23.333,61.26 30.672,59.294 36.412,62.608L67.588,80.608C73.328,83.921 75.294,91.26 71.981,97C68.667,102.74 61.328,104.706 55.588,101.392L24.412,83.392C18.672,80.079 16.706,72.74 20.019,67Z"/><path class="edost_loading_anim_12" d="M65,20.019C70.74,16.706 78.079,18.672 81.392,24.412L99.392,55.588C102.706,61.328 100.74,68.667 95,71.981C89.26,75.294 81.921,73.328 78.608,67.588L60.608,36.412C57.294,30.672 59.26,23.333 65,20.019Z"/></svg>'
	this.loading = '<div style="text-align: center;">' + self.loading_svg + '</div>'
	this.loading20 = '<span class="edost_loading_20">' + self.loading_svg + '</span>'
	this.loading128 = '<div class="edost_loading_128" style="margin: 20px; text-align: center;">' + self.loading_svg + '</div>'

	this.template_2019 = false
	this.template_ico = 'T'
	this.template_compact = 'off';
	this.template_priority = false
	this.template_no_insurance = false
	this.template_preload_prop = false
	this.ico_path = '';
	this.window_scroll_disable = false
	this.mobile = false

	this.address_id = ['edost_city2', 'edost_street', 'edost_house_1', 'edost_house_2', 'edost_door_1']
	this.tel = {
		'mask': {
			'0': {'c': '7', 'f': [3,3,2,2], 'n': 'Россия'},
			'14': {'c': '374', 'f': [2,2,2,2], 'n': 'Армения'},
			'21': {'c': '375', 'f': [2,3,2,2], 'n': 'Беларусь'},
			'85': {'c': '7', 'f': [3,3,2,2], 'n': 'Казахстан', 's': '7'},
			'108': {'c': '996', 'f': [2,3,2,2], 'n': 'Кыргызстан'},
			'212': {'c': '380', 'f': [2,3,2,2], 'n': 'Украина'},
			'M': {'n': 'другая страна...'},
		},
		'format_data': [['-', '-', '-'], [' (', ') ', '-'], [' (', ') ', ' '], [' ', ' ', ' ']],
	}

	this.in_array = function(v, ar) {
		if (ar) for (var i = 0; i < ar.length; i++) if (v == ar[i]) return true;
		return false;
	}

	this.filter = function(s, param) {
		s = s.replace(/;/g, ',').replace(/ /g, ',').replace(/^,+|,+$/gm, '').replace(/,+/g, ',');
		if (param == 'array') s = s.split(',');
		return s;
	}

	this.json = function(s) {
		if (!s) return false;
		return (window.JSON && window.JSON.parse ? JSON.parse(s) : eval('(' + s + ')'));
	}

	this.trim = function(s, space) {
		s = s.replace(/^\s+|\s+$/gm, '');
		if (space) s = s.replace(/\s+/g, ' ');
		return s;
	}

	this.digit = function(v) {
		if (v == 0) v = '';
		return v;
	}

	this.browser = function(s) {
		if (self.mobile) return window[s == 'w' ? 'innerWidth' : 'innerHeight'];
		else return document.documentElement[s == 'w' ? 'clientWidth' : 'clientHeight'];
	}

	// конвертирование английской раскладки в русскую
	this.ru = function(s, mode) {

		var n_en = s.replace(/[^A-Za-z]/g, '').length;
		var n_ru = s.replace(/[^А-Яа-я]/g, '').length;

		var r = '';
		if (n_ru != 0 && n_en == 0) r = s;
		else for (var i = 0; i < s.length; i++) {
			var v = s[i];
			var u = v.toLowerCase();
			if (key_map[u] != undefined) v = key_map[u];
			r += v;
		}

		if (mode === 'full') {
			r = r.replace(/[ё]/g, 'е');
			r = r.replace(/[^А-Яа-я0-9 ,.]/g, ' ');
			r = self.trim(r, true);
		}

		return r;

	}

	// получение параметра в адресе документа "...?name=1&..."
	this.get = function(name) {
//function edost_UrlParam(name) {
		var s = window.location.search.split('&' + name + '=');
        if (s[1] == undefined) s = window.location.search.split('?' + name + '=');
		return (s[1] != undefined ? s[1].split('&')[0] : '');
	}

	this.create = function(tagName, id, p, s) {
		if (tagName == 'WINDOW') {
			tagName = 'DIV';
			if (!p) p = {};
			if (!p.class) p.class = id;
			if (!p.E) p.E = document.body;
			if (!s) s = {};
			if (!s.display) s.display = 'none';
		}
		var e = document.createElement(tagName);
		if (id) e.id = id;
		if (p) for (var k in p) if (k != 'E') if (k == 'html') e.innerHTML = p[k]; else if (k == 'class') e.className = p[k]; else e[k] = p[k];
		if (s) for (var k in s) e.style[k] = s[k];
		if (p && p.E) p.E.appendChild(e);
		return e;
	}

	var E = this.E = function(o, v) {
		if (o) if (typeof o !== 'object') o = document.getElementById(o);
		else if (!o.tagName) o = (o[1] ? o[0] : document).querySelector(o[1] ? o[1] : o[0]);

		var r = undefined;
		if (!o || !o.tagName) o = false;
		if (v) {
			if (typeof v !== 'object') v = {'get': v};
			if (v.get == 'html') r = '';
			if (o) {
				if (v.display === false || v.display === 0) v.display = 'none';
				else if (v.display === true || v.display === 1) v.display = 'block';

				for (var k in v) if (k != 'param') {
					var get = false;
					if (k == 'get') { k = v[k]; get = true; }
					var s = self.in_array(k, ['height', 'width', 'opacity', 'display', 'padding', 'margin', 'position', 'left', 'right', 'top', 'bottom']);

					var k2 = k;
					if (k == 'html') k2 = 'innerHTML';
					else if (k == 'class') k2 = 'className';

					if (get) {
						if (k == 'data') r = o.getAttribute('data-' + v.param); else r = (s ? o.style[k2] : o[k2]);
					}
					else {
						if (k == 'data') { for (var u in v.data) o.setAttribute('data-' + u, v.data[u]); }
						else if (s) o.style[k2] = v[k];
						else {
							if (k == 'value' && o.tagName == 'SELECT' && !o.querySelector('option[value="' + v[k] + '"]')) continue;
							if (k == 'class' && typeof v.class === 'object') {
								var c = v.class[0];
								var a = (v.class[1] ? v.class[1] : 0);
								if (c[a] == '' || !o.classList.contains(c[a])) {
									for (var i = 0; i < c.length; i++) if (i != a && c[i] != '' && o.classList.contains(c[i])) o.classList.remove(c[i]);
									if (c[a] != '') o.classList.add(c[a]);
								}
								continue;
							}
							o[k2] = v[k];
						}
					}
				}
			}
		}
		return (r !== undefined ? r : o);
	}
	var V = this.V = this.value = function(o, v) {
		return E(o, v === undefined ? 'value' : {'value': v});
	}
	var D = this.D = this.display = function(o, v) {
		return E(o, v === undefined ? 'display' : {'display': v});
	}
	var H = this.H = this.html = function(o, v) {
		return E(o, v === undefined ? 'html' : {'html': v});
	}
	var C = this.C = this.class = function(o, list, active) {
		var c = (active === undefined ? list : [list, active]);
		return E(o, list === undefined ? 'class' : {'class': c});
	}
	this.data = function(o, n, v) {
		if (v === undefined) var s = {'get': 'data', 'param': n};
		else { var s = {'data': {}}; s.data[n] = v; }
		return E(o, s);
	}

	this.clone = function(o) {
		var v = {};
		for (var p in o) {
			if (o[p] instanceof Array) {
				v[p] = [];
				for (var i = 0; i < o[p].length; i++) v[p][i] = o[p][i];
			}
			else v[p] = o[p];
		}
		return v;
	}

	this.scroll = function(e, p) {

		if (e) {
			if (typeof e !== 'object') e = E(e);
			if (!e) return;

			var p = (typeof p === 'object' ? p : {});
			var rect = e.getBoundingClientRect();
			var browser_h = edost.browser('h');

			if (p.top) p.position = rect.top - p.top;
			else
				if (rect.top < 150) p.position = rect.top - 150;
				else if (rect.top > browser_h - 250) p.position = rect.top - browser_h + 250;
				else p.position = 0;

			self.scroll_data = p;
		}

		p = self.scroll_data;
		if (p.position != 0) {
			var s = (p.speed ? p.speed : 0.2);
			var m = 200*s;
			var y = p.position * s;
			if (Math.abs(y) > m) y = (y < 0 ? -m : m);
			window.scrollBy(0, y);
			p.position -= y;
			if (Math.abs(p.position) > 1) {
				window.setTimeout('edost.scroll()', 25);
				return;
			}
		}
		if (p.function) window.setTimeout(p.function, 1);

	}

	this.backup = function(param, recovery, loading) {
		if (param === 'location') param = ['edost_zip', 'edost_zip_full', 'edost_shop_ZIP', 'edost_location_city_div'];
		if (param === 'location_header') param = ['edost_location_header_city', 'edost_catalogdelivery_inside_city', 'edost_catalogdelivery_window_city'];
		for (var i = 0; i < param.length; i++) {
			var v = param[i];
			var e = E(v);
			if (!e) continue;
			var p = (e.tagName == 'INPUT' ? 'value' : 'innerHTML');
			if (recovery) e[p] = (self.backup_data[v] ? self.backup_data[v] : '');
			else {
				self.backup_data[v] = e[p];
				if (loading) e[p] = loading;
			}
		}
	}

	// подключение js скриптов
	this.js = function(name) {

		var e = E('edost_main_js');
		var path = self.data(e, 'path');
		var file = self.data(e, 'file');
		var version = self.data(e, 'version');
		var key = (e ? e.src.split('?a=')[1] : false);

		if (file) file = file.split(',');

		if (name == undefined) {
			name = [];
			var s = ['main.css', 'office.js', 'location.js', 'admin.js', 'location_city', 'pickpoint'];
			if (file !== '') for (var i = 0; i < s.length; i++) if (!file && !self.in_array(s[i], ['location_city', 'pickpoint']) || self.in_array(s[i], file)) name.push(s[i]);
		}

		var E_head = document.head;
		for (var i = 0; i < name.length; i++) {
			var v = name[i];

			var script = (v.indexOf('.js') > 0 ? true : false);
			var id = 'edost_' + v.replace('.', '_');
			var src = (path ? path : self.protocol + js_path) + (version ? version + '/' : '') + v + (key ? '?a=' + key : '');
			var charset = (!path ? 'utf-8' : false);

			// список городов для профилей оформления
			if (v == 'location_city') {
				script = true;
				id += '_js';
				charset = 'windows-1251';
				src = self.protocol + 'edostimg.ru/js/location_data.js?v=4';
			}

			// виджет PickPoint
			if (v == 'pickpoint') {
				script = true;
				id += '_js';
				charset = 'utf-8';
				src = self.protocol + 'pickpoint.ru/select/postamat.js';
			}

			var e = E(id);
			if (e) continue;

			var e = document.createElement(script ? 'SCRIPT' : 'LINK');
			e.id = id;
			e.type = (script ? 'text/javascript' : 'text/css');

			if (script) {
				e.src = src;
				if (charset) e.charset = charset;
			}
			else {
				e.href = src;
				e.rel = 'stylesheet';
			}

			E_head.appendChild(e);
		}

	}
	self.js();


	this.cookie = function(name, value) { // set_cookie, get_cookie

		// упакованные куки [имя куки, имя параметра] - параметры загружаются из edost[имя куки].param.cookie, в куки записываются через '|'
		if (Array.isArray(name)) {
			var n = 'edost_' + name[0], k = {};
			if (edost[name[0]]) k = edost[name[0]].param.cookie;
			name = name[1];

			var r = [];
			var c = self.cookie(n).split('|');

			var i = 0;
			for (var s in k) {
				if (name == s) {
					if (value === undefined) return c[i];
					r.push(value);
				}
				else r.push(c[i] != undefined ? c[i] : '');
				i++;
			}

			if (value !== undefined) self.cookie(n, r.join('|'));

			return;
		}

		if (value === undefined) {
			var r = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
			return (r ? decodeURIComponent(r[2]) : '');
		}

		document.cookie = name + '=' + value + '; path=/; expires=Thu, 01-Jan-2050 00:00:01 GMT';

	}


	this.run = function(name, param) {
		var a = true;
		var f = window;
		var u = name.split('.');
		var n = u.length - 1;
		for (var i = 0; i <= n; i++) if (!f[ u[i] ] || (i == n && f.temp)) a = false; else f = f[ u[i] ];
		var s = [];
		if (param != undefined) for (var i = 0; i < param.length; i++) s.push("'" + param[i].replace(/'/g, '\\\'') + "'");
		s = s.join(',');

//		if (a) console.log("edost main RUN ======== " + name + "(" + s + ")");
//		else console.log("edost main repeat ======== edost.run('" + name + "'" + (param != undefined ? ", [" + s + "]" : '') + ")");

		if (a) window.setTimeout(name + "(" + s + ")", 1);
		else window.setTimeout("edost.run('" + name + "'" + (param != undefined ? ", [" + s + "]" : '') + ")", 500);
	}


	this.mask = function(param, e, event) {

		if (!self.tel.country) return;

		if (param === 'start') {
			var s = document.querySelectorAll('input[type="tel"]');
			if (s) for (var i = 0; i < s.length; i++) {
				v = s[i];

				if (v.getAttribute('data-country') || v.offsetHeight == 0) continue;

//				v.placeholder = '+7-9xx-xxx-xx-xx"';

				v.onkeydown = new Function('edost.input("keydown", this, event);');
				v.onblur = new Function((self.template_preload_prop ? 'submitProp(this); ' : '') + 'edost.input("blur", this);');
				if (self.template_preload_prop) v.onchange = '';

				if (edost.mobile) v.ontouchend = new Function('E_mask = this; window.setTimeout("edost.mask(\'click\', E_mask)", 100)' );
				else {
					v.onclick = new Function('edost.mask("click", this);');
					v.onmousemove = new Function('edost.mask("mouse", this, event);');
				}

				self.mask('parse', v, 'start');
			}

			return;
		}
		if (param === 'model') {
			return {'s': e, 'n': e.replace(/[^0-9]/g, ' '), 'm': e.replace(/[^0-9+_]/g, ' '), 'x': e.length};
		}
		if (param === 'parse') {
			var start = (event === 'start' ? true : false);
			var s = (event !== undefined && !start ? event[0] : e.value);
			if (s.replace(/[^0-9]/g, '').length == 11 && s[0] == '8') s = '+7' + s.substr(1);
			var n = s.replace(/[^0-9+]/g, '');

			var c = '';
			if (!start && event && event[1] != undefined) c = event[1];
			else {
				for (var k in self.tel.mask) if (k != 'M' && self.in_array(k, self.tel.country)) {
					var m = self.tel.mask[k];
					if (n.indexOf('+' + m.c + (m.s ? m.s : '')) == 0) c = k;
				}
				if (n.length <= 2 || c !== '' && n == '+' + self.tel.mask[c].c) c = 'start';
				if (c === '' || c == 'start') {
					if (c != 'start' && n.length != 0 && self.tel.manual) c = 'M';
					else {
						c = V('edost_country').split('_')[0];
						if (!self.in_array(c, self.tel.country) && self.tel.manual) c = 'M';
						s = '+';
					}
				}
			}

			if (c == 'M') r = s;
			else {
				var m = self.tel.mask[c];
				var r = '+' + m.c;
				s = n.substr(1 + m.c.length);
				for (var i = 0; i < m.f.length; i++) {
					var v = s.substr(0, m.f[i]);
					r += self.tel.format[i < 2 ? i : 2] + v;
					for (var i2 = 0; i2 < m.f[i]-v.length; i2++) r += '_';
					s = s.substr(m.f[i]);
				}
			}

			self.data(e, 'country', c);
			if (self.tel.country.length > 1) {
				self.class(e, ['', 'edost_tel_flag'], c == 'M' ? 0 : 1);
				self.mask('flag', e, c);
			}

			e.value = r;

			return r;
		}
		if (param === 'mouse') {
			self.class(e, ['', 'edost_tel_flag_on'], event.offsetX > 30 ? 0 : 1);
			return;
		}
		if (param === 'country' || param === 'country_show') {
			e = E([e.parentNode, 'div.edost_tel_country']);
			if (param === 'country') return e;
			return (!e || e.style.display == 'none' ? false : true);
		}
		if (param === 'flag') {
			e.style.backgroundImage = (event != 'M' ? "url('" + self.ico_path + 'flag/' + event  + '.gif' + "')" : 'none');
			return;
		}
		if (param === 'set') {
			if (!e) return;

			mask_blur = false;
			self.mask('close', e);

			var c = self.data(e, 'country');
			var e2 = e.parentNode.parentNode.querySelector('input[type="tel"]');

			if (c == 'M') s = '+';
			else {
				var s = e2.value;
				var m = self.tel.mask[c];
				var p = s.indexOf(self.tel.format[0]);
				if (p > 0) s = '+' + m.c + s.substr(p);
			}
			self.mask('parse', e2, [s, c]);

			self.class(e2, ['', 'edost_tel_flag'], c == 'M' ? 0 : 1);

			E_mask = e2;
			window.setTimeout('edost.mask("focus")', 100);

			return;
		}
		if (param === 'focus') {
			if (!E_mask) return;
			e = E_mask;
			e.focus();
		}
		if (param === 'open') {
			if (self.tel.country.length <= 1) return;

			mask_blur = false;
			var e2 = self.mask('country', e);
			if (e2) e2.style.display = 'block';
			else {
				var s = '';
				if (self.tel.country) for (var i = 0; i < self.tel.country.length; i++) {
					var k = self.tel.country[i];
					var v = self.tel.mask[k];
					s += '<div data-country="' + k + '" onmousedown="' + 'edost.mask(\'set\', this)">';
					if (k != 'M') s += '<img src="' + self.ico_path + 'flag/' + k + '.gif" width="30" height="21" border="0">';
					s += '<span' + (k == 'M' ? ' style="display: inline-block; padding: 0 0 3px 34px;"' : '') + '>' + v.n + '</span>';
					if (k != 'M') s += ' <span>+' + v.c + '</span>';
					s += '</div>';
				}
				edost.create('DIV', '', {'class': 'edost_tel_country', 'html': s, 'E': e.parentNode});
			}

			param = 'redraw';
		}
		if (param === 'close' || param === 'close_blur') {
			mask_country = -1;

			if (e) { if (!self.mask('country_show', e)) return; }
			else if (!mask_blur) return;

			if (param === 'close_blur') {
				mask_blur = true;
				window.setTimeout('edost.mask("close")', 100);
			}
			else {
				var s = (e ? e.parentNode.parentNode : document).querySelectorAll('div.edost_tel_country');
				if (s) for (var i = 0; i < s.length; i++) s[i].style.display = 'none';
			}

			return;
		}
		if (param === 'redraw') {
			var s = e.parentNode.querySelectorAll('div.edost_tel_country div');
				if (s) for (var i = 0; i < s.length; i++) {
				self.class(s[i], ['', 'edost_tel_country_active'], mask_country == i ? 1 : 0);
			}
			return;
		}

		var r = self.mask('model', e.value);
		var key = (event && event.keyCode ? event.keyCode : event);
		var p = e.selectionStart;
		var n = -1;
		var keydown = true;
		var country_list = '';
		var country = self.data(e, 'country');
		var country_change = (self.in_array(key, [38, 40, 13]) ? true : false);

		if (param === 'focus') p = 0;

		if (country != 'M') {
			if (param == 'click') {
				for (var i = p; i >= 1; i--) if (r.s[i] != undefined)
					if (r.n[i] == ' ') n = i;
					else {
						if (i != p && r.m[i+1] == ' ') n++;
						break;
					}
			}
			else if (key === 8) {
				if (r.m[p-1] == ' ') key = 37;
			}
			else if (!key) {
				var s = r.s;

				// в поле вставлен новый телефон
				var u = s.substr(1).indexOf('+');
				if (u != -1) {
					s = self.mask('parse', e, [s.substr(u+1)]);
					key = 35;
				}

				country = self.data(e, 'country');
				var m = self.tel.mask[country];
				var ms = m.f;
				s = self.trim(s.replace(/[^0-9_]/g, ' '), true).split(' ').splice(1);

				r = '+' + m.c;
				for (var i = 0; i < ms.length; i++) {
					var x = ms[i]*1;
					var u = (s[i] !== undefined ? s[i] : '');
					u = u.replace(/[^0-9]/g, '');
					var ux = u.length;
					for (var i2 = 0; i2 < x - ux; i2++) u += '_';
					if (ux > x) {
						if (i != ms.length - 1) s[i+1] = u.substr(x) + (s[i+1] !== 'undefined' ? s[i+1] : '');
						u = u.substr(0, x);
					}
					r += self.tel.format[i < 2 ? i : 2] + u;
				}

				e.value = r;
				r = self.mask('model', r);
				n = p;
			}

			p = (n != -1 ? n : e.selectionStart);

			if (key == 39) p++;
			if (key == 37) p--;

			if (r.m[p] == '_') {
				if (key == 37) for (var i = p-1; i >= 0; i--) if (r.m[i] != '_') { n = i + 2; break; }
				if (key == 39) if (r.n[p-1] == ' ') for (var i = p; i <= r.x; i++) if (r.m[i] != '_') { n = (r.m[i+1] == ' ' ? i+1 : i); break; }
			}
			if (r.m[p] == ' ') {
				if (!key) n = p + (r.m[p+1] == ' ' ? 2 : 1);
				if (key == 39) n = p + (r.m[p+1] == ' ' ? 1 : 0);;
				if (key == 37) for (var i = p - (r.m[p] == ' ' ? 2 : 1); i >= 0; i--) if (r.m[i] != '_') { n = i + (r.m[i+1] == ' ' ? 1 : 2); break; }
			}

			p = (n != -1 ? n : e.selectionStart);

			var ps = pe = pen = 0;
			for (var i = 1; i < r.x; i++) if (r.m[i] == ' ') { ps = i + (r.m[i+1] == ' ' ? 2 : 1); break; }
			for (var i = r.x-1; i >= 0; i--) if (r.m[i] == ' ' || r.n[i] != ' ') { pe = i + 1; break; }
			for (var i = r.x-1; i >= 0; i--) if (r.s[i] == '_') pen = i; else if (r.n[i] != ' ') break;

			if (key == 36 || p < ps) { // home + курсор на коде страны
				n = ps;
				keydown = false;
				if (param === 'click' && self.mask('country_show', e)) country_list = 'close';
				else if (param !== 'focus') country_list = 'open';
			}
			if (param === 'click' && p >= ps) country_list = 'close';

			if (key == 35) { n = (pen == 0 ? pe : pen); keydown = false; } // end + курсор за текстом
			else if (p > pe || p == pe && r.s[p] == '_' && key == 39) { n = pe; keydown = false; }

			if (n != -1) e.setSelectionRange(n, n);
		}
		else {
			if (key == 36 || (key == 37 || key == 8) && p == 0) { if (param !== 'focus') country_list = 'open'; } // home + курсор на коде страны
		}

		var count = self.tel.country.length;
		if (count > 1) {
			var e2 = self.mask('country', e);
			var a = (e2 && e2.style.display != 'none' ? true : false);
		}
		if (self.in_array(key, [38, 40, 13])) {
			keydown = false;
			if (count > 1)
				if (!a) {
					if (key == 40) {
						mask_country = 0;
						country_list = 'open';
					}
				}
				else if (key == 13) self.mask('set', E([e.parentNode, 'div.edost_tel_country .edost_tel_country_active']));
				else {
					mask_country += (key == 40 ? 1 : -1);
					if (mask_country < 0) country_list = 'close';
					else {
						if (mask_country > count - 1) mask_country = count - 1;
        				self.mask('redraw', e);
					}
				}
		}

		if (country_list == '' && !country_change && param != 'click') country_list = 'close';
		if (country_list != '') self.mask(country_list, e);

		return keydown;

	}

	this.input = function(param, e, event) {

		if (param == 'focus') {
			if (self.mobile) {
				var rect = e.getBoundingClientRect();
				mobile_jump = rect.top - 10;
				self.window.resize();
			}
			return;
		}
		if (param == 'blur') {
			mobile_jump = false;
			self.mask('close_blur', e);
			if (self.mobile) self.window.resize();
			return;
		}
		if (param == 'keydown') {
			if (e.type == 'tel') {
				if (event.ctrlKey === true || event.shiftKey === true) return;

				var a = false;
				if (self.in_array(event.keyCode, [8, 9, 27, 46, 35, 36, 37, 38, 39, 40, 13])) a = true;
				else if (event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode >= 96 && event.keyCode <= 105) a = true;
				else if (self.data(e, 'country') == 'M' && self.in_array(event.keyCode, [32, 107, 109, 187, 189])) a = true;

				if (!a || !self.mask('keydown', e, event)) if (event.preventDefault) event.preventDefault(); else event.returnValue = false;
			}

			return;
		}

		if (param == 'update') {
			if (e.value != '' && e.classList.contains('edost_prop_error')) {
				e.classList.remove('edost_prop_error');

				if (self.in_array(e.id, self.address_id)) for (var i = 0; i < self.address_id.length; i++) {
					var e2 = E(self.address_id[i]);
					if (e2) e2.classList.remove('edost_prop_error');
				}

		        var id = self.data(e, 'error_id');
		        if (id) {
			        var s = document.querySelector('div[data-error_id="' + id + '"]');
					if (s) s.innerHTML = '';
				}
			}
			self.window.resize();
		}

		if (e.type == 'tel') self.mask(false, e);
//			if (self.tel.country) self.mask(false, e);
//			else e.value = e.value.replace(/^\s*/, '').replace(/[^0-9+\)\( -_]/g, '');

	}

	this.submit = function() {
		if (window.submitForm) submitForm();
		else if (window.BX && BX.Sale && BX.Sale.OrderAjaxComponent && BX.Sale.OrderAjaxComponent.sendRequest) BX.Sale.OrderAjaxComponent.sendRequest();
		else if (window.edost.admin.update_delivery) edost.admin.update_delivery();
	}

	this.post = function(url, data, callback) {
//		if (url == 'admin') url = '/bitrix/admin/edost.php';
//		if (url == 'ajax') url = self.param.ajax_file;
		if (url == 'location') url = '/bitrix/components/edost/locations/edost_location.php';
		BX.ajax.post(url, data, callback);
	}


/*
	// поиск позиции и переменной в строке по цепочке соответствий
	this.string_pos_val = function(s, ar, position, before) {
//	function edost_StringPosVal(s, ar, position, before) {
		var v = '', p = 0, p2 = 0;

		for (var i = 0; i < ar.length; i++)
			if (ar[i] == 'VALUE') p2 = p;
			else {
				p = s.indexOf(ar[i], p);
				if (p == -1) break;
				if (p2 != 0) { v = s.substr(p2, p-p2).replace(/^\s+|\s+$/gm, ''); break; }
				if (!before) p += ar[i].length;
			}

		return (position != undefined ? p : [p, v]);
	}

	// замена в строке по цепочке соответствий (s - строка, s2 - вставить, ar - цепочка соответствий, ar2 - цепочка соответствий для переменной)
	this.insert_string = function(s, s2, ar, ar2, before) {
//	function edost_InsertString(s, s2, ar, ar2, before) {
		if (ar2 != undefined && ar2 != false) {
			v = self.string_pos_val(s, ar2);
			if (v[1] != '') s2 = s2.replace('%value%', v[1]);
		}

		var p = self.string_pos_val(s, ar, true, before);
		if (p > 0) s = s.substr(0, p) + s2 + s.substr(p);

		return s;
	}
*/
}




edost.window = new function() {
//	var edost_window_create = function(main_function, div_name) {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C
	var main_function = 'edost.window'
	var main_id = main_function.replace('.', '_'), fon_id = main_id + '_fon', head_id = main_id + '_head', loading_id = main_id + '_loading', name_id = main_id + '_name', button_id = main_id + '_button', save_id = main_id + '_save', close_id = main_id + '_close', data_id = main_id + '_data', frame_id = main_id + '_frame'
	var format, param_profile, onkeydown_backup = 'free', overflow_backup = false, scroll_backup = false, data_backup = false, onclose = '', window_width = 0, data_width = 0, browser_width = 0, browser_height = 0
	var onclose_set_office_start, mobile_jump = false, head_color_default = '#888', loading = false, scroll_reset = false, window_scroll = false, request = false
	var alarm_position = 0, error_id = 0
	var arguments_backup = false, window_id = false, E_mask, mask_blur = false

	this.copy_id = false
	this.timer = false
	this.timer_resize = false;
	this.inside = false
	this.cod = false
	this.mode = ''
	this.param = {}
	this.option = false
	this.save = false
	this.register_reload_onclose = false
	this.close_onset = false
	this.reload = false
	this.head = ''

	this.config_data = {
		"post": {
			"width": 520,
		},
		"door": {
			"width": 520,
		},
		"delivery": {
			"width": 550,
		},
		"tariff": {
			"width": 550,
		},
		"paysystem": {
			"width": 550,
		},
		"agreement": {
			"width": 650,
		},
		"agreement_fast": {
			"width": 650,
		},
		"fast": {
			"width": 580,
			"landscape_head_width": 120
		},
		"option_setting": {
			"loading": true,
			"save": 1
		},
		"profile_setting": {
			"head": "Профили организаций",
			"loading": true,
			"save": 2
		},
		"profile_setting_new": {
			"head": "Новый профиль",
			"head_color": "#F00",
			"loading": true,
			"save": 1
		},
		"profile_setting_change": {
			"head": "Редактирование профиля",
			"head_color": "#0088D2",
			"loading": true,
			"save": 1
		},
		"option": {
			"head": "Опции доставки",
			"width": 340,
			"save": 1,
			"small_height": true
		},
		"register_delete": {
			"head": "Исключение заказа из сдачи",
			"width": 400,
			"small_height": true
		},
		"register_delete2": {
			"confirm": "Отменить оформление заказа?",
			"save": 4,
			"small_height": true
		},
		"order_batch_delete": {
			"confirm": "Исключить заказ из сдачи?",
			"save": 4,
			"small_height": true
		},
		"batch_date": {
			"head": "Изменение даты сдачи",
			"width": 320,
			"small_height": true
		},
		"profile": {
			"head": "Профили сдачи",
			"width": 470,
			"loading": true,
			"save": 1,
			"small_height": true
		},
		"call": {
			"confirm": "Вызвать курьера?",
			"loading": true,
			"save": 4,
			"small_height": true
		},
		"batch_delete": {
			"confirm": "Удалить сдачу и отменить оформление всех входящих в нее заказов?",
			"save": 4,
			"small_height": true
		},
		"call_profile": {
			"confirm": '<div style="font-size: 15px;">Для вызова курьера необходимо заполнить в профиле магазина поля откуда курьеру забирать груз (город, адрес, телефон, время ожидания курьера) и указать ФИО сотрудника, отвечающего за сдачу/приемку груза.</div>' +
					   '<div class="edost_button_base edost_button_date" style="background: #08C; font-size: 14px; padding: 4px 8px 5px 8px; line-height: 14px; margin-top: 20px;" onclick="' + main_function + '.set(\'profile\', \'old\')">перейти к профилям сдачи</div>',
			"small_height": true
		},
		"package_detail": {
			"no_padding": true,
			"head": "Места",
			"width": 460
		},
		"frame": {
			"width": 1100,
			"up": true,
			"no_padding": true,
			"landscape_head_width": 100,
			"small_height": true
		}
	}

	this.config = function(param) {
		var r = (param === undefined ? {} : false);
		// глобальные
		if (self.config_data[self.mode])
			 if (param === undefined) r = self.config_data[self.mode];
			 else if (self.config_data[self.mode][param]) r = self.config_data[self.mode][param];
		// из параметров
		var key = ['head', 'width', 'confirm', 'save', 'small_height', 'landscape_head_width', 'no_padding'];
		for (var i = 0; i < key.length; i++) if (self.param[key[i]] !== undefined)
			if (param === undefined) r[key[i]] = self.param[key[i]];
			else if (param === key[i]) r = self.param[key[i]];
		// дефолтные по параметрам
		var s = {'confirm': {'save': 4, 'small_height': true}};
		for (var k in s) if (self.param[k]) for (var k2 in s[k])
			if (param === undefined) r[k2] = s[k][k2];
			else if (param == k2) r = s[k][k2];
		return r;
	}

	this.error = function(e, v, type) {
		var error = '';
		if (type == 'tel') {
			var s = 0;
			var c = edost.data(e, 'country');
			if (c) {
				if (c == 'M') {
					var n = v.replace(/[^0-9]/g, '').length;
					if (n < 2) s = 1; else if (n < 6) s = 2;
				}
				else if (v.indexOf('_') != -1) {
					s = v.replace(/[^0-9+]/g, ' ').match(/\d+/g);
					s = (s && s.length > 1 ? 2 : 1);
				}
				if (s != 0) error = 'пожалуйста, укажите телефон' + (s == 2 ? ' полностью' : '');
			}
		}
		if (!v) {
			if (type == 'agreement') error = 'необходимо согласиться и поставить галочку';
			else if (type == 'passport') error = 'пожалуйста, заполните паспортные данные';
			else if (type == 'address') error = 'пожалуйста, укажите адрес';
			else if (type == 'street') error = 'пожалуйста, выберите улицу (проспект, проезд) из списка с подсказками';
			else if (type == 'zip') error = 'пожалуйста, укажите почтовый индекс';
			else error = 'пожалуйста, заполните данное поле';
		}
		if (type == 'email' && v != '' && (v.indexOf('@') == -1 || v.indexOf('.') == -1)) error = 'неверный формат';
		if (error) {
			if (type == 'zip' && V('edost_zip_required') !== 'Y') return false;

			C(e, ['', 'edost_prop_error'], 1);
			e.oninput = new Function('event', main_function + '.input("update", this, event)');

			var e2 = e;
			if (type == 'passport') {
				e2 = E('edost_location_passport_div');
				if (!e2) return false;
			}
			if (edost.in_array(type, ['address', 'street', 'zip'])) {
				e2 = E('edost_location_address_div');
				if (!e2) return false;
			}

			var s = e2.parentNode.getElementsByTagName('DIV');
			if (s) for (var i = 0; i < s.length; i++) if (s[i].classList.contains('edost_prop_error')) {
				var id = edost.data(s[i], 'error_id');
				if (!id) {
					error_id++;
					id = error_id;
					edost.data(s[i], 'error_id', id);
				}
				edost.data(e, 'error_id', id);
				s[i].innerHTML = '<span class="edost_prop_error">' + error + '</span>';
			}

			return true;
		}
        return false;
	}

	this.props = function(e) {

		var r = true;
		var first = false;
		var fast = (e ? true : false);

		if (!fast) {
			e = E('order_auth');
			if (!e) e = E('ORDER_FORM');
		}

		if (e) {
			var ar = e.getElementsByTagName('INPUT');
            if (ar) {
				var e2 = E('edost_address_hide');
				var address = (e2 && e2.value != 'Y' ? true : false);

				for (var i = 0; i < ar.length; i++) if (fast || ar[i].id && ar[i].type != 'hidden' && (ar[i].name.indexOf('ORDER_PROP_') == 0 || edost.in_array(ar[i].id, ['edost_agreement', 'edost_passport_2', 'edost_passport_3', 'edost_passport_4', 'edost_zip']))) {
					type = ar[i].getAttribute('data-type');
					if (!type && edost.in_array(ar[i].id, ['edost_passport_2', 'edost_passport_3', 'edost_passport_4'])) type = 'passport';
					if (!type && address && ar[i].id == 'edost_zip') type = 'zip';
					if (!type) continue;

					v = (type == 'agreement' ? ar[i].checked : edost.trim(ar[i].value, true));
					if (self.error(ar[i], v, type)) {
						r = false;
						if (!first) first = ar[i];
					}
				}

				if (address) {
					var a = {};
					var city2_required = E('edost_city2_required');
					for (var i = 0; i < ar.length; i++) if (edost.in_array(ar[i].id, edost.address_id)) a[ar[i].id] = (edost.trim(ar[i].value, true) != '' ? true : false);
					var a_city2 = (city2_required && city2_required.value == 'Y' && !a.edost_city2 ? true : false);
					var a_street = (!a.edost_street && !a.edost_house_1 && !a.edost_house_2 && !a.edost_door_1 ? true : false);
					if (a_city2 || a_street) {
						for (var i = 0; i < ar.length; i++) if (a_city2 && ar[i].id == 'edost_city2' || a_street && ar[i].id != 'edost_city2' && edost.in_array(ar[i].id, edost.address_id)) if (self.error(ar[i], '', 'address')) {
							r = false;
							if (!first) first = ar[i];
						}
					}
					else {
						var street_required = E('edost_street_required');
						var area = E('edost_area');
						if (street_required && street_required.value != '' && area && area.value == 'Y') {
							var street = E('edost_street');
							if (street && self.error(street, '', 'street')) {
								r = false;
								if (!first) first = street;
							}
						}
					}
				}
			}
		}

		var s = document.querySelectorAll('div.edost_prop_error span');
		if (s) for (var i = 0; i < s.length; i++) C(s[i], ['', 'edost_prop_blink'], 0);
		if (!r && first) self.alarm(fast ? 'window' : first);

		return r;

	}

	this.copy = function(e, save) {

		if (!e) return false;

		var r = true;

		var ar = e.getElementsByTagName('INPUT');
		if (ar) for (var i = 0; i < ar.length; i++) {
			name = ar[i].getAttribute('data-name');
			if (!name) continue;

			type = ar[i].type;
			s = ar[i].getAttribute('data-type');
			if (s) type = s;

			var e2 = E(name);
			if (!e2) continue;

            if (save) {
				if (type == 'checkbox') e2.checked = ar[i].checked;
				else e2.value = edost.trim(ar[i].value, true);

				if (type == 'tel') edost.mask('parse', e2);
				if (edost.template_preload_prop) submitProp(e2);
            }
            else {
            	if (type == 'checkbox') ar[i].checked = e2.checked;
            	else ar[i].value = e2.value;
            }
		}

		if (save) r = self.props(e);

		if (!r) self.resize();

		return r;

	}

	this.alarm = function(e) {
		if (e && e !== 'window') { edost.scroll(e, {"function": main_function + '.alarm("")'}); return; }
        var s = document.querySelectorAll((e === 'window' ? '#edost_window ' : '') + 'div.edost_prop_error span');
		if (s) for (var i = 0; i < s.length; i++) C(s[i], ['', 'edost_prop_blink'], 1);
	}

	this.agreement = function(param, fast) {
		if (window.edost_Agreement) { edost_Agreement(param, fast); return; }

		if (param == 'fast') self.set('fast', 'head=Быстрое оформление заказа;class=edost_window_form');
		else if (param == 'request') self.set('agreement' + (fast ? '_fast' : ''), 'head=Согласие на обработку персональных данных;class=edost_window_form');
		else if (param == 'set') {
			var e = E('edost_agreement');
			if (e) { if (!fast) e.click(); e.checked = true; }
			if (fast) self.agreement('fast');
		}
		else if (param == 'unset') E('edost_agreement', {'checked': false});
		else if (param == 'submit') {
			if (fast) V('edost_fast', 'Y')
			submitForm('Y');
		}
	}

	this.submit = function(value, param) {

		var mode = self.mode;

		if (self.param.function) {
			var f = new Function('param', 'var e = (param.e ? param.e: false); ' + self.param.function);
			f(self.param);
			self.set('close_full');
			return;
		}

		if (mode == 'batch_delete') {
			edost.admin.control(self.param.id, 'batch_delete', self.param.e);
			self.set('close_full');
			return;
		}
		if (mode == 'call') {
			request = true;
			edost.admin.setting('batch', 'post', 'id=' + self.param.id + '&call=1&save=Y');
			return;
		}
		if (mode == 'profile') {
			if (self.register_reload_onclose) {
				request = true;
				edost.admin.setting('batch', 'post', 'id=' + self.param.id + '&profile_shop=' + self.param.profile_shop + '&profile_delivery=' + self.param.profile_delivery + '&save=Y');
			}
			else self.set('close_full');
			return;
		}
		if (mode == 'batch_date') {
			if (self.param.date_error) return;
			var e = document.querySelector('input[name="window_edost_register_date"]');
			//var e = E('window_edost_register_date');
			if (e) edost.register.button('button_date|' + self.param.id, e.value)
			self.set('close_full');
			return;
		}
		if (mode == 'order_batch_delete') {
			edost.admin.control(self.param.id, 'order_batch_delete', true);
			self.set('close_full');
			return;
		}
		if (mode == 'register_delete2') {
			edost.admin.control(self.param.id, 'delete_register', true);
			self.set('close_full');
			return;
		}
		if (mode == 'register_delete') {
			if (value == 'delete') edost.admin.control(self.param.id, 'delete_register', true);
			if (value == 'date') {
				if (self.param.date_error) return;
//				var e = E('window_edost_register_date');
				var e = document.querySelector('input[name="window_edost_register_date"]');
				if (e) edost.admin.control(self.param.id, 'order_date', e.value);
			}
			self.set('close_full');
			return;
		}
		if (mode == 'profile_setting') {
			self.set('profile_setting_new');
			return;
		}
		if (edost.in_array(mode, ['profile_setting_new', 'profile_setting_change'])) {
			var e = E('edost_profile_id');
			var id = (e ? e.value : '');

			var post = [];
	        var s = document.querySelectorAll('#' + main_id + ' input, #' + main_id + ' select');
			if (s) for (var i = 0; i < s.length; i++) {
				if (s[i].type == 'checkbox' && !s[i].checked) continue;
				var v = '';
				if (s[i].type == 'checkbox') v = 1;
				else v = edost.trim(s[i].value.replace(/\=/g, ' ').replace(/\|/g, ' '), true);
				post.push(s[i].name + '=' + v);
			}
			post = post.join('|');

			self.register_reload_onclose = self.reload = true;
			if (data_backup.mode == 'profile') {
				self.close_onset = true;
				post += '|local=1';
			}

			edost.admin.setting(mode, 'save', post);
			return;
		}
		if (mode == 'option') {
			var post = [];
			var e = E(data_id + '_div');
	        var s = e.querySelectorAll('div.edost_option_service input');
			if (s) for (var i = 0; i < s.length; i++) {
				var v = s[i].getAttribute('data-id');
				if (s[i].checked) post.push(v);
			}

			self.set('close_full');
			edost.package.save(post.join(','), self.param);

			return;
		}
		if (mode == 'option_setting') {
			var e = E(main_id);
    	    var post = [];
    	    var s = e.querySelectorAll('input[type="radio"]:checked');
			if (s) for (var i = 0; i < s.length; i++) {
				var name = s[i].name.replace('edost_service_', '');
				post.push(name + ',' + s[i].value);
			}

			self.register_reload_onclose = true;
			edost.admin.setting(mode, 'save', post.join(':'));

			return;
		}

		// согласие
		if (mode == 'agreement' || mode == 'agreement_fast') {
			self.set('close_full');
			self.agreement('set', mode == 'agreement_fast' ? true : false);
			return;
		}
		// быстрое оформление заказа
		if (mode == 'fast') {
			var e = E(data_id);
			if (!e || !self.copy(e, true)) return;
			self.set('close_full');
			self.agreement('submit', true);
			return;
		}

		var cod = (self.cod ? self.cod : false);

		if (param != undefined) {
			edost.office.window(param);
			return;
		}

		if (value) {
			self.set('close_full');
			E(value, {'checked': true});
		}

		if (cod) E(cod, {'disabled': false, 'checked': true});

		if (!value) return;

		if (value.indexOf('PAY_SYSTEM') > 0) changePaySystem();
		else submitForm();

    }

	this.get_param = function(v) {
		var r = {};
		var o = false;
		if (typeof v === 'object') {
			o = (!v.getBoundingClientRect ? true : false);
			if (o) r = v; else r.e = v;
			if (r.e) {
				var p = r.e.getBoundingClientRect();
				r.position = [p.left + p.width*0.5, p.top];
				if (!r.position[0] && self.param.position) r.position = self.param.position;
				if (r.position[0] == 0 && r.position[1] == 0) r.position = null;
				v = edost.data(r.e, 'param');
			}
		}
		var s = (v ? v.split(';') : '');
		if (s) for (var i = 0; i < s.length; i++) {
			var s2 = s[i].split('=');
			if (s2[0] == 'service') s2[1] = (s2[1] ? s2[1].split(',') : []);
			if (s2[0] == 'href') s2[1] = decodeURIComponent(s2[1]);
			r[s2[0]] = s2[1];
		}
		return r;
	}

	this.set = function(mode, param) {
//			alert(param + ' | ' + mode + ' [' + self.mode + ' | ' + data_backup.mode + ']');

		loading = false;

		var show = (!edost.in_array(mode, ['close_full', 'close', 'esc']) ? true : false);

		self.copy_id = '';
		if (edost.in_array(mode, ['close', 'esc']) && data_backup !== false && data_backup.mode != self.mode && !request) {
			// восстановление основного окна при закрытии вторичного
			show = true;

			H(data_id, data_backup.html);

			mode = data_backup.mode;
			self.param = data_backup.param;
			window_scroll = data_backup.scroll;

			if (!self.reload) self.copy_id = false;

			if (mode == 'profile' && param && self.reload) {
				var s = param.split('|');
				self.param['profile_' + s[0]] = s[1];
			}

			self.reload = false;
		}
		else if (show) {
			// бэкап основного окна при открытии вторичного
			if (mode && self.mode && mode != self.mode && !edost.in_array(self.mode, ['paysystem', 'call_profile'])) {
				var e = E(data_id);
				data_backup = {"html": H(e), "scroll": (e ? e.scrollTop : 0), "mode": self.mode, "param": edost.clone(self.param)};
				H(e, '');
			}

			if (param !== 'old') self.param = self.get_param(param);
			self.copy_id = mode;
			if (mode == 'option') self.copy_id += '_' + self.param.company;
			if (mode == 'package_detail') self.copy_id += '_' + self.param.id;

			window_scroll = false;
			scroll_reset = true;
		}

		if (edost.in_array(mode, ['close_full']) && self.mode == 'agreement') self.agreement('unset');
		if (!show && self.register_reload_onclose && edost.in_array(self.mde, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile'])) edost.admin.set_param('register', 'reload');

		if (mode == 'fast') {
			var s = document.querySelectorAll('div.edost_prop_error');
			if (s) for (var i = 0; i < s.length; i++) s[i].innerHTML = '';
		}
		if (mode == 'agreement' && self.mode == 'fast') mode = 'agreement_fast';

		if (show) self.mode = mode;
        var config = self.config();

		var head = (self.param.head ? self.param.head : '');
		window_width = 0;

		var head_color = '';
		if (show) {
			if (config.confirm) window_width = 340;
			else if (config.width) window_width = config.width;
			else if (self.param.width) window_width = self.param.width;

			if (config.head) head = config.head;
			if (config.head_color) head_color = config.head_color;
			self.head = head;
		}

		if (self.mode == 'agreement_fast') {
			var e = E(data_id);
			if (e) self.copy(e, true);
		}
		self.save = (self.config('save') ? true : false);

		if (self.param.cod_id) self.cod = self.param.cod_id;
		data_width = 0;

		if (!show) {
			if (edost.template_2019) edost.resize.scroll('recover', scroll_backup);

		    document.onkeydown = onkeydown_backup;
		    onkeydown_backup = 'free';
			data_backup = false;
			self.mode = '';
			self.register_reload_onclose = self.close_onset = self.reload = false;
            self.cod = false;
		}
		else {
			if (onkeydown_backup == 'free') {
	    		if (edost.template_2019) scroll_backup = edost.resize.scroll('save');

			    onkeydown_backup = document.onkeydown;
				document.onkeydown = new Function('event', 'if (event.keyCode == 27) ' + main_function + '.set("esc");');
			}
		}

		var E_main = E(main_id);
		if (!E_main) {
			edost.create('WINDOW', fon_id, {'html': '<div id="' + loading_id + '">' + edost.loading + '</div>', 'onclick': new Function('', main_function + '.set("close")')});

			var s = '';
			s += edost.close.replace('%onclick%', main_function + ".set('close')");
			s += '<div id="' + head_id + '">';
			s += '<div id="' + name_id + '"></div>'
			s += '<div id="' + button_id + '" style="display: none;"><div class="edost_button_window_close" style="display: inline-block;" onclick="' + main_function + '.set(\'close\');">закрыть</div></div>';
			s += '</div>';
			s += '<div id="' + data_id + '"></div>';
			s += '<div id="' + save_id + '">';
			s += '<div class="edost_button_div">';
			s += '<div class="edost_button_left">';
			s += '<div class="edost_button_save" onclick="' + main_function + '.submit();">сохранить</div>';
			s += '<div class="edost_button_new" onclick="' + main_function + '.submit(0);">создать</div>';
			s += '<div class="edost_button_yes" onclick="' + main_function + '.submit();">да</div>';
			s += '</div>';
			s += '<div class="edost_button_right">';
			s += '<div class="edost_button_cancel" onclick="' + main_function + '.set(\'close\');">отменить</div>';
			s += '<div class="edost_button_close" onclick="' + main_function + '.set(\'close\');">закрыть</div>';
			s += '<div class="edost_button_no" onclick="' + main_function + '.set(\'close\');">нет</div>';
			s += '</div>';
			s += '</div>';
			s += '</div>';
			E_main = edost.create('WINDOW', main_id, {'html': s});
			if (!E_main) return;
		}

		var copy_id = (self.copy_id ? 'edost_' + self.copy_id + '_div' : '');

		var c = ' ' + (copy_id && copy_id == 'edost_paysystem_div' ? copy_id : 'edost_delivery_div');

		var E_head = E(name_id);
		if (E_head) {
			E_head.innerHTML = (head ? head.replace('с оплатой при получении', '<span style="display: inline-block;">с оплатой при получении</span>') : '');
			E_head.className = c + '_head';
			E_head.style.color = head_color;
		}

		var s = ['edost_window edost_compact_window_main'];
		if (self.param.class) s.push(self.param.class); else s.push('edost_window_main ' + (self.cod ? 'edost_compact_tariff_cod_main' : 'edost_compact_tariff_main'));
		if (self.mode == 'option' && !self.param.depend_count) s.push('edost_option_service_depend_count_hide');
		if (self.mode == 'option' && !self.param.depend_62) s.push('edost_option_service_depend_62_hide');
		if (config.no_padding) s.push('edost_window_data_no_padding');
		if (config.confirm) s.push('edost_confirm');
		if (self.mode == 'fast') s.push('edost_window_fast');
		E_main.className = s.join(' ');

		var display = (!show ? 'none' : 'block');

		var E_data = E(data_id);
		if (!E_data) return;
		E_data.className = 'edost edost_window_data' + c;

		E_main.style.display = display;
		E_main.style.zIndex = (config.up ? '10582' : '');

		var e = E(fon_id);
		if (e) {
			e.style.display = display;
			e.style.zIndex = (config.up ? '10580' : '');
		}

		var resize = true;
		if (config.confirm) {
			// текстовые данные
			E_data.innerHTML = config.confirm;
		}
		else if (edost.in_array(self.mode, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile']) && self.copy_id !== false) {
			// загрузка данных извне
			resize = false;
			var v = '';
			if (self.param.type || self.mode == 'profile_setting_new') v = 0;
			if (self.param.local && self.param.change) v = data_backup.param['profile_' + self.param.type];
			else if (edost.in_array(self.mode, ['profile', 'profile_setting_change'])) v = self.param.id;
			E_data.innerHTML = '';
			edost.admin.setting(self.mode, 'get', v);
		}
		else if (self.mode == 'frame') {
			// страница пункта выдачи
			E_data.innerHTML = '<iframe id="' + frame_id + '" style="display: block;" src="' + self.param.href + '" frameborder="0" width="100%"></iframe>';
		}
		else if (copy_id) {
			// копирование данных из документа
			var e = E(copy_id);
			if (e) {
				if (!show) E_data.innerHTML = '';
				else {
					var s = '';
					s += '<div id="' + data_id +  '_width"></div>';
					s += '<div id="' + data_id +  '_div">';
					s += e.innerHTML;

					if (edost.in_array(self.mode, ['register_delete', 'batch_date'])) s = s.replace(/edost_register_date/g, 'window_edost_register_date');
					if (self.mode == 'fast' || self.mode == 'agreement' || self.mode == 'agreement_fast') s = s.replace(/submitForm\('Y'\)/g, 'window.' + main_function + '.submit(this)');
					if (self.mode == 'fast') s = s.replace(/edost_agreement_2/g, 'window_edost_agreement_2'); //.replace(/\'agreement\'/g, '\'agreement_fast');
					else if (self.mode == 'agreement' || self.mode == 'agreement_fast') s = s.replace(/id=\"edost_agreement_text/g, 'id="window_edost_agreement_text'); // '
					else s = s.replace(/submitForm/g, 'return; submitForm').replace(/changePaySystem/g, 'return; changePaySystem').replace(/\"ID_DELIVERY_/g, '"window_ID_DELIVERY_').replace(/\"ID_PAY_SYSTEM_ID_/g, '"window_ID_PAY_SYSTEM_ID_');    //"

					s += '</div>';
					s += '<div id="' + data_id +  '_buffer"></div>';
					E_data.innerHTML = s;
				}
			}
		}


		if (show) {
			if (edost.in_array(self.mode, ['register_delete', 'batch_date'])) {
//				var e = E('window_edost_register_date');
				var e = document.querySelector('input[name="window_edost_register_date"]');
				if (e && e.value == self.param.batch_date) e.value = edost.data(e, 'date');
			}

			if (self.mode == 'profile' && resize) self.resize('set_profile', E_data);

			if (self.mode == 'option') {
		        var s = E_data.querySelectorAll('div.edost_option_service input');
				if (s) for (var i = 0; i < s.length; i++) {
					var v = s[i].getAttribute('data-id');
					s[i].checked = (edost.in_array(v, self.param.service) ? true : false);
				}
			}
			if (self.mode == 'fast') self.copy(E_data);
		}

		if (!show) return;

		var e = E(main_id);
		if (!e || e.style.display == 'none') return;

		if (resize) self.resize();
		edost.mask('start');

//		self.fit();
//		if (self.timer_resize != undefined) window.clearInterval(self.timer_resize);
//		self.timer_resize = window.setInterval(main_function + '.fit("resize")', 400);

	}


	// установка размера окна
	this.resize = function(param, value) {

        var config = self.config();

		if (param == 'loading') loading = true;

		if (param == 'change') {
			if (self.mode == 'profile') self.register_reload_onclose = true;
		}

		if (param == 'set_profile') {
			if (self.param.profile_shop != undefined) for (var i = 0; i <= 1; i++) {
				var s = 'profile_' + (i == 0 ? 'shop' : 'delivery');
				V([value, 'select[name="edost_' + s + '"]'], self.param[s]);
			}
			return;
		}

		if (edost.in_array(self.mode, ['register_delete', 'batch_date']) && param == 'error') self.param.date_error = value;

		if (edost.in_array(self.mode, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile', 'call']) && param == 'set') {
			loading = false;

			if (self.close_onset) { self.close_onset = false; self.set('close', value); return; }
			if (value === 'close') { self.set('close'); return; }

			if (request) {
				request = false;
				var s = value.split('error:');
				if (!s[1]) {
					if (self.mode == 'call') {
						D('edost_call_button_' + self.param.id, false);
						D('edost_call_' + self.param.id, true);
					}
					self.set('close_full');
					return;
				}
				value = edost.admin.error(s[1]);
				self.mode = 'error';
				self.save = false;
			}

			var E_data = E(data_id);
			if (E_data) E_data.innerHTML = value;

			if (self.mode == 'profile') self.resize('set_profile', E_data);
			if (self.mode.indexOf('profile_setting') == 0 && E_data) {
				// генерация списка городов для выбора
				locations.sort(function (a, b) {
				  if (a[3] > b[3]) return 1;
				  if (a[3] < b[3]) return -1;
				  return 0;
				});
	    	    var ar = E_data.querySelectorAll('input.edost_field_city');
				if (ar) for (var i = 0; i < ar.length; i++) {
					var s = '';
					s += '<option value=""' + (!ar[i].value ? ' selected' : '') + '>не задан</option>';
					for (var i2 = 0; i2 < locations.length; i2++) if (locations[i2][1] == 0 && locations[i2][4] == 3) {
						var p = locations[i2];
						s += '<option value="' + p[0] + '"' + (p[0] == ar[i].value ? ' selected' : '') + '>' + p[3] + (!edost.in_array(p[3], ['Москва', 'Санкт-Петербург', 'Севастополь']) ? ' (' + regions[0][p[2]] + ')' : '') + '</option>';
					}
					s = '<select name="' + ar[i].name + '" style="width: 100%;">' + s + '</select>';
					ar[i].parentNode.innerHTML = s;
				}

				if (self.param.type == 'delivery') {
					var e2 = E_data.querySelector('select[name="type"]');
					if (e2) e2.value = self.param.company;
				}
			}
		}

		var e = E(main_id);
		if (!e || e.style.display == 'none') return;

		// размер окна браузера
		var browser_w = edost.browser('w');
		var browser_h = edost.browser('h');

		browser_width = browser_w;
		browser_height = browser_h;

		window_w = (window_width != 0 ? window_width : 600);
		window_h = 500;

		var fullscreen = false;
		if (window_w > browser_w-100 || window_h > browser_h-100) {
			fullscreen = true;
			window_w = browser_w;
			window_h = browser_h;
		}

		e.style.width = window_w + 'px';

		var E_data = E(data_id);
		var top = E_data.scrollTop;
		var landscape = (fullscreen && browser_width > 500 && browser_height < 450 && browser_width > browser_height ? true : false);
		var mobile = (fullscreen || edost.mobile ? true : false);
		var landscape_head_width = (config.landscape_head_width ? config.landscape_head_width : 110);
		var fullscreen_save = (fullscreen && config.save ? true : false);

		// адаптация контента
		var E_width = E(data_id);
		if (E_width && window_w != data_width) {
			data_width = window_w;
			edost.resize.update('window', window_w);
		}

		var c = 0;
		if (self.mode == 'fast') {
			var w = window_w - (landscape ? landscape_head_width : 0)
			c = (w > 350 ? 1 : 2);
		}
		C(e, ['', 'edost_props_normal', 'edost_props_small'], c);

		C(e, ['edost_window_normal', 'edost_window_fullscreen'], fullscreen ? 1 : 0);

		if (param !== 'loading') {
			if (self.mode == 'profile') {
				for (var i = 0; i <= 1; i++) {
					var s = 'profile_' + (i == 0 ? 'shop' : 'delivery');
					self.param[s] = V([E_data, 'select[name="edost_' + s + '"]']);
					C(e, ['', 'edost_' + s + '_change_main'], self.param[s] ? 1 : 0);
				}
			}
			if (self.mode.indexOf('profile_setting') == 0) {
				C(e, ['', 'edost_field_new'], self.mode == 'profile_setting_new' ? 1 : 0);

				var e2 = e.querySelector('select[name="type"]');
				var c = (e2 && e2.value == 'shop' ? 1 : 2);
				C(e, ['', 'edost_field_shop', 'edost_field_company'], c);

				var e2 = e.querySelector('select[name="mode"]');
				var c = (c != 2 || e2 && e2.value == 'N' ? 0 : 1);
				C(e, ['', 'edost_field_contract'], c);
			}
		}

		var c = 0;
		if (edost.mobile)
			if (!fullscreen) c = 1;
			else if (browser_width < browser_height && browser_width < 450 && browser_height < 700 || browser_width > browser_height && browser_width < 700 && browser_height < 450) c = 3;
			else c = 2;
		C(e, ['edost_device_pc', 'edost_device_tablet', 'edost_device_tablet_small', 'edost_device_phone'], c);
		var device = c;

		var c = 0;
		if (mobile) c = (landscape ? 2 : 1);
		C(e, ['', 'edost_window_mobile', 'edost_window_landscape'], c);

		var E_head = E(head_id);
		var E_name = E(name_id);
		var E_button = E(button_id);
		var E_buffer = E(data_id + '_buffer');
		var E_save = E(save_id);
		var E_frame = E(frame_id);

		E_save.style.display = (E_save && self.save ? 'block' : 'none');
		C(e, ['', 'edost_button_save_main', 'edost_button_new_main', 'edost_button_close_main', 'edost_button_yes_main',], config.save);

		if (self.save) {
			E_save.style.width = '';
			E_save.style.marginLeft = '';
		}

		if (edost.in_array(self.mode, ['register_delete', 'batch_date'])) C(e, ['', 'edost_button_date_error'], self.param.date_error ? 1 : 0);
		if (self.mode == 'batch_date') C(e, ['', 'edost_call_warning_main'], self.param.call ? 1 : 0);

		C(e, ['', 'edost_error'], self.mode == 'error' ? 1 : 0);

		e.style.opacity = (loading && config.loading ? '0.01' : 1);

		var agreement = (self.mode == 'agreement' || self.mode == 'agreement_fast' ? true : false);
		if (agreement) {
			landscape_head_width = 140;
			var E_agreement = E('window_edost_agreement_text');
			if (!E_agreement) agreement = false;
		}

		if (E_buffer) E_buffer.style.height = 0;

		var jump = (mobile_jump !== false && fullscreen ? mobile_jump + 500 : 0);

		if (landscape) {
			E_data.style.width = (window_w - landscape_head_width) + 'px';
			E_data.style.height = 'auto';
			E_data.style.marginLeft = E_head.style.width = landscape_head_width + 'px';
			E_name.style.width = 'auto';
			E_head.style.height = '100%';

			if (E_frame) E_frame.style.height = window_h + 'px';

			var h_data = E_data.offsetHeight;
			var h_name = E_name.offsetHeight;
			var h_button = E_button.offsetHeight;
			var h = (h_name + h_button) + 16;
			var h_save = (self.save ? E_save.offsetHeight : 0);

			if (agreement) E_agreement.style.height = (window_h - 120) + 'px';

			var top = (h_data >= window_h ? 0 : Math.round((window_h - h_data)*0.5) + 'px');
			var height = window_h + jump + 'px';

			if (fullscreen_save) {
				h = h_data;
				top = Math.round((window_h - h_data - h_save)*0.5) + 'px';
				height = h_data + 'px';
				E_save.style.width = (window_w - landscape_head_width) + 'px';
				E_save.style.marginLeft = landscape_head_width + 'px';
			}

			E_data.style.marginTop = top;
			E_data.style.height = height;
			E_name.style.marginTop = Math.round((window_h - h)*0.5) + 'px';
		}
		else {
			E_name.style.marginTop = 0;
			E_data.style.width = 'auto';
			E_data.style.marginLeft = E_data.style.marginTop = 0;

			if (agreement) {
				var h = window_h - 180;
				if (device <= 1 && h > 350) h = 350;
				E_agreement.style.height = h + 'px';
			}

			if (!fullscreen || self.mode == 'fast' && mobile || fullscreen_save) E_data.style.height = 'auto';

			var s = 24;
			var w = browser_w;
			if (w < 240 || agreement && w < 310) s = 16;
			else if (w < 350) s = 18;
			else if (w < 600) s = 20;
			else if (w < 900) s = 22;
			E_head.style.fontSize = E_head.style.lineHeight = s + 'px';

			if (mobile) {
				E_head.style.width = window_w + 'px';
				E_name.style.width = (window_w - 90) + 'px';

				var h_name = E_name.offsetHeight;
				var h_button = E_button.offsetHeight;
				var h = (h_name > h_button ? h_name : h_button) + 16;
				E_head.style.height = h + 'px';

				if (E_frame) E_frame.style.height = (window_h - h) + 'px';

				E_name.style.top = Math.round((h - h_name)*0.5) + 'px';
				E_button.style.top = Math.round((h - h_button)*0.5) + 'px';
			}
			else {
				E_head.style.width = E_head.style.height = E_name.style.width = 'auto';
				if (E_frame) E_frame.style.height = (browser_height < 900 ? browser_height-100 : 800) + 'px';
			}

			if (self.save) E_save.style.height = (config.small_height ? 60 : 70) + 'px';

			var h_head = E_head.offsetHeight;
			var h_data = E_data.offsetHeight;
			var h_save = (self.save ? E_save.offsetHeight : 0);

			if (h_head == 0) h_head = 24;
			if (fullscreen) {
				var h = window_h - h_head - h_save, h2 = 0;

				if (fullscreen_save) h = h_data;
				if (mobile && mobile_jump === false && self.mode == 'fast' && h_data + 40 > h) {
					h2 = h_data + 40 - h;
					if (h2 > 40) h2 = 40;
				}

				if (h2 != 0) E_buffer.style.height = h2 + 'px';
				E_data.style.height = h + jump + 'px';
			}
			else {
				var h = h_head + (!mobile ? 15 : 5) + h_save;
				if (E_frame) h = 0;
				window_h = h_data + h;
				if (window_h + 100 > browser_h) {
					window_h = browser_h - 100;
					E_data.style.height = (window_h - h) + 'px';
				}
			}
		}

		e.style.borderRadius = (fullscreen ? 0 : '8px');
		e.style.width = window_w + 'px';
		e.style.height = window_h + jump + 'px';

		if (self.param.position && !fullscreen) {
			var x = Math.round(self.param.position[0] - window_w*0.5);
			var y = Math.round(self.param.position[1] - window_h + 80);
		}
		else {
			var x = Math.round((browser_w - window_w)*0.5);
			var y = Math.round((browser_h - window_h)*0.5);

		}

		if (!fullscreen && mobile_jump === false) {
			if (y < 15) y = 15;
			if (y*1 + window_h*1 > browser_height - 15) y = browser_height - 15 - window_h;
			if (x < 15) x = 15;
			if (x*1 + window_w*1 > browser_width - 40) x = browser_width - 40 - window_w;
		}

		if (mobile_jump !== false) {
			e.style.position = 'absolute';
			var h = - (!fullscreen ? 70 : mobile_jump) + (!landscape ? 20 : 0);
			if (!landscape && !fullscreen && browser_height > 800) h = 20;
			e.style.top = (edost.resize.get_scroll('y') + h) + 'px';
		}
		else {
			e.style.position = '';
			e.style.top = y + 'px';
		}

		e.style.left = x + 'px';

		if (window_scroll !== false) top = window_scroll; else if (scroll_reset) top = 0;
		E_data.scrollTop = top;
		scroll_reset = false;

		var E_loading = E(loading_id);
		if (E_loading) {
			E_loading.style.display = (loading ? 'block' : 'none');

			if (loading) {
				E_loading.style.width = e.style.width;
				E_loading.style.height = e.style.height;
				E_loading.style.left = e.style.left;
				E_loading.style.top = e.style.top;

		        var e2 = E_loading.querySelector('div');
		        if (e2) e2.style.paddingTop = Math.round((window_h)*0.5 - 32) + 'px';
			}
		}
	}

}




edost.resize = new function() {
//	var edost_resize = new function() {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C
	var timer = false, template_width = 0, E_template_width = false, drawing = false
	var template_width_main, template_browser_height, scroll_Y, window_scroll_X, window_scroll_Y, double_update = false, template_param = {"mode": "full", "width": 0, "width2": 0, "fixed": 0, "top": 0}
	var	button_window_scroll_Y = false, button_window_width = 0, button_window_height = 0, window_scroll_disable = false
	var data = []
	var phone_width = 0, header_query = '', header_height = 0, header_width = 0, sticky = true, button_timer
	var loading_id = 'edost_data_loading';

	this.init = false
	this.param = {}
	this.device = ''
	this.os = ''
	this.count = ''
	this.browser_width = 0
	this.browser_height = 0
	this.window_scroll_disable = false

	this.get_device = function() {

		function c(f) { return (s.indexOf(f) !== -1 ? true : false); }

		var r = '';
		var os = '';
		var s = window.navigator.userAgent.toLowerCase();

//		window.matchMedia('(pointer:coarse)').matches // !!!!!

		if (c('blackberry') || c('bb10') || c('rim')) os = 'blackberry';
		else if (c('windows')) os = 'windows';
		else if (c('iphone') || c('ipod') || c('ipad')) os = 'ios';
		else if (c('android')) os = 'android';
		else if ((c('(mobile;') || c('(tablet;')) && c('; rv:')) os = 'fxos';

		self.os = os;

		if (os == 'android' && c('mobile') || os != 'windows' && c('iphone') || c('ipod') || os == 'windows' && c('phone') || os == 'blackberry' && !c('tablet') || os == 'fxos' && c('mobile') || c('meego')) self.device = 'phone';
		else if (c('ipad') || navigator.platform === 'MacIntel' && navigator.maxTouchPoints && navigator.maxTouchPoints > 1 || os == 'android' && !c('mobile') || os == 'blackberry' && c('tablet') || os == 'windows' && c('touch') || os == 'fxos' && c('tablet')) self.device = 'tablet';

		edost.mobile = (self.device == 'phone' || self.device == 'tablet' ? true : false);

		if (/MSIE 10/i.test(navigator.userAgent) || /MSIE 9/i.test(navigator.userAgent) || /rv:11.0/i.test(navigator.userAgent)) sticky = false;

	}
	self.get_device();

	this.bar = function(param) {

		if (param == 'start' || param == 'timer') button_window_scroll_Y = -1;
		if (param == 'timer') {
			if (button_timer != undefined) window.clearInterval(button_timer);
			var button_timer = window.setInterval("edost.resize.bar()", 40);
			return;
		}

		var s = self.get_scroll('y');
		var w = edost.browser('w');
		var h = edost.browser('h');

		if (!edost.in_array(param, ['loading', 'save', 'start']) && button_window_scroll_Y == s && button_window_width == w && button_window_height == h) return;

		button_window_scroll_Y = s;
		button_window_width = w;
		button_window_height = h;

		var E_data = E('edost_data_div');
		var E_bar = E('edost_bar');
		if (!E_data || !E_bar) return;

		var rect = E_data.getBoundingClientRect();

		if (param === 'loading') {
			// загрузка нового контента
			E_data.innerHTML = edost.loading;
			if (rect.top < 0) window.scrollBy(0, rect.top - 80);
			return;
		}
		if (edost.in_array(param, ['save', 'start'])) {
			// сохранение с блокировкой блока
			var E_fon = E(loading_id + '_fon');
			if (!E_fon && param == 'save') {
				E_data.insertAdjacentHTML('beforebegin', '<div id="' + loading_id + '_fon" style="position: absolute; background: #FFF; opacity: 0.6; z-index: 4;"><div id="' + loading_id + '">' + edost.loading + '</div></div>');
	            E_fon = E(loading_id + '_fon');
			}
            if (E_fon) {
				E_fon.style.display = (param == 'save' ? 'block' : 'none');
				if (param == 'save') {
					E_loading = E(loading_id);
					E_fon.style.width = E_loading.style.width = E_data.offsetWidth + 'px';
					E_fon.style.height = E_data.offsetHeight + 'px';
					var y = (rect.top < 0 ? -rect.top : 0);
					var y2 = (rect.bottom < h ? rect.bottom : h);
					if (rect.top > 0) y2 -= rect.top;
					E_loading.style.paddingTop = Math.round(y + y2*0.5 - 32) + 'px';
				}
            }
			return;
		}

		var up = (rect.bottom > h ? true : false);
		C(E_bar, ['', 'edost_bar_up'], up ? 1 : 0);
		E_bar.style.width = (up ? (rect.width-2) + 'px' : '');
		E_bar.style.left = (up ? (rect.left+1) + 'px' : '');

	}

	this.start = function(param) {

		if (param !== undefined) self.param = param;

        scroll_Y = -1;
        template_width_main = -1;
		template_width = -1;
		template_browser_height = -1;
		window_scroll_disable = false;
		double_update = false;
		self.count = 0;

		if (timer != undefined) window.clearInterval(timer);

		var e = E('edost_template_width_div');
		E_template_width = (e ? e : false);

		var s = '';
		if (self.param.data) s = self.param.data;
		else {
			var e = E('edost_template_data');
			if (!e) return;
			s = e.value;
		}

		data = []
		var ar = s.split('|');
		for (var i = 0; i < ar.length; i++) {
			var s = ar[i].split(':');
			var u = s[0].split('-');
			var v = {"id": u[1] ? u[1] : '', "type": u[0], "name": s[1], "width": u[2] ? u[2] : ''};
			s = s.slice(2);
			var c = [];
			for (var i2 = 0; i2 < s.length; i2++) if (!(i2%2)) {
				s[i2] = s[i2].split(',');
				c.push(s[i2][0]);
			}
			v.param = s;
			v.class = c;
			data.push(v);
		}

        if (!self.init) {
			self.init = true;

			var e = E('edost_template_2019');
			if (e) {
				edost.template_2019 = true;
				edost.template_ico = edost.data(e, 'ico');
				edost.template_priority = edost.data(e, 'priority');
				edost.template_compact = edost.data(e, 'compact');
				edost.template_no_insurance = edost.data(e, 'no_insurance');
				edost.template_preload_prop = edost.data(e, 'preload_prop');

				var s = edost.data(e, 'ico_path');
				edost.ico_path = (s ? s : '') + (s && s.substr(-1) != '/' ? '/' : '');

				var s = edost.json(edost.data(e, 'tel'));
				if (s && s.country && (s.country.length > 1 || s.country[0] != 'M')) { edost.tel.country = s.country; edost.tel.format = edost.tel.format_data[s.format_id]; edost.tel.manual = edost.in_array('M', s.country); }

				var s = edost.data(e, 'window_scroll_disable');
				self.window_scroll_disable = (!s || s != 'N' ? true : false);

				if (edost.template_compact == 'S') {
					var e2 = E('ORDER_FORM');
					if (e2) {
						C(e2, ['edost_compact_main', 'edost_supercompact_main'], edost.mobile ? 1 : 0);
						C(e2, ['edost_compact_main2', 'edost_supercompact_main2'], edost.mobile ? 1 : 0);
					}
				}

				var s = (e.value != '' ? e.value.split(':') : '');
				if (s[1]) {
					s = s[1].split('|');
					phone_width = (s[0] && s[0]*1 > 0 ? s[0]*1 : 0);
					header_query = (s[1] ? s[1] : '');
					header_height = (s[2] && s[2]*1 > 0 ? s[2]*1 : 0);
					header_width = (s[3] && s[3]*1 > 0 ? s[3]*1 : 0);
				}
			}

			if (edost.template_2019 || self.param.set_event) {
				window.addEventListener('resize', edost.resize.update);
				window.addEventListener('scroll', edost.resize.update);
//				window.addEventListener('mousewheel', edost.resize.update);
				if (edost.mobile) window.addEventListener('orientationchange', edost.resize.update);
			}
		}

		drawing = false;
		if (edost.template_2019 || self.param.set_event) {
			edost.resize.update();
			edost.mask('start');
		}
		else {
			if (timer != undefined) window.clearInterval(timer);
			timer = window.setInterval("edost.resize.update()", 200);
		}

	}

	this.get_scroll = function(s) {
		if (s == 'x') return (window.pageXOffset != undefined ? window.pageXOffset : (document.documentElement && document.documentElement.scrollLeft || document.body && document.body.scrollLeft || 0) - document.documentElement.clientLeft);
		if (s == 'y') return (window.pageXOffset != undefined ? window.pageYOffset : (document.documentElement && document.documentElement.scrollTop || document.body && document.body.scrollTop || 0) - document.documentElement.clientTop);
	}

	this.scroll = function(param, value) {
		if (!self.window_scroll_disable) return false;
		if (param == 'recover') {
			window_scroll_disable = value;
			if (!window_scroll_disable) window.scrollTo(window_scroll_X, window_scroll_Y);
		}
		if (param == 'save') {
			var r = window_scroll_disable;
			window_scroll_disable = true;
			window_scroll_X = self.get_scroll('x');
			window_scroll_Y = self.get_scroll('y');
			return r;
		}
	}

	this.update = function(id, width) {

		// блокировка прокрутки главного окна
		if (self.window_scroll_disable && window_scroll_disable) window.scrollTo(window_scroll_X, window_scroll_Y);

		if (typeof id === 'object') id = undefined;
		var update_param = false;

		var jump = 0;
		if (header_width == 0 || edost.browser('w') < header_width) {
			jump = header_height;
			if (header_query != '') {
				jump = 0;
				var E_header = document.querySelector(header_query);
				if (E_header) {
					var rect = E_header.getBoundingClientRect();
					jump = (rect.bottom > 0 ? rect.bottom + header_height : 0);
				}
			}
		}

		// включение/выключение компактного блока "итого"
		if (edost.template_2019 && id == undefined) {
			var e = E('order_form_content');
			if (e) {
				var w = e.offsetWidth;
				var browser_h = edost.browser('h');
				if (w != template_width_main || template_browser_height != browser_h) {
					template_width_main = w;
					template_browser_height = browser_h;
					scroll_Y = -1;
					update_param = true;

					var e = E('order_form_main');
					var e2 = E('order_form_total');
					var E2_div = E('order_form_total_div');
					var E_form = E('ORDER_FORM');
					if (e && e2 && E2_div && E_form) {
						var ar = ['edost_template_total_full', 'edost_template_total_small', 'edost_template_total_off']; // все в компактном блоке, компактный блок без товаров, компактный блок отключен
						var a = (phone_width > 0 && edost.browser('w') < phone_width ? false : true);
						var c = (template_width_main > 700 && a ? 0 : 2);
						if (c == 0) {
							var w2 = (template_width_main > 800 ? 250 : 230);
							var w = template_width_main - w2 - 20;
							e.style.width = w + 'px';
							e2.style.width = w2 + 'px';
							e2.style.display = 'block';

							if (sticky) E2_div.style = 'width: ' + w2 + 'px; position: sticky; position: -webkit-sticky; top: ' + (10 + jump*1) + 'px;';
							else E2_div.style.width = w2 + 'px';

							var E_cart = E('order_total_cart');
							var E_cart_count = E('order_total_cart_count');
							if (!E_cart) { if (E_cart_count) c = 1; }
							else if (E_cart_count) {
								C(E_form, ar, 0);
								var h = E2_div.offsetHeight + jump;
								if (h > template_browser_height - 40 || h > e.offsetHeight) c = 1;
								if (double_update === false) double_update = 1;
							}

							template_param.width = w;
							template_param.width2 = w2;
						}
						else {
							e.style.width = '100%';
							e2.style.display = 'none';

							template_param.width = 0;
							template_param.width2 = 0;
						}
						C(E_form, ar, c);
						template_param.mode = ar[c].substr(21);
					}
				}
			}
		}

		var p = self.param;
		var browser_w = edost.browser('w');
		var browser_h = edost.browser('h');
		var browser_resize = (self.browser_width != browser_w || self.browser_height != browser_h ? true : false);
		self.browser_width = browser_w;
		self.browser_height = browser_h;

		if (p.device) {
			var e = document.body;
			var landscape = (browser_w > 500 && browser_h < 450 && browser_w > browser_h ? true : false);

			var c = 0;
			if (edost.mobile)
				if (browser_w > 900 && browser_h > 850) c = 1;
				else if (browser_w < browser_h && browser_w < 450 || browser_w > browser_h && browser_h < 450) c = 3;
				else c = 2;
			C(e, ['device_pc', 'device_tablet', 'device_tablet_small', 'device_phone'], c);

			var c = 0;
			if (edost.mobile) c = (landscape ? 2 : 1);
			C(e, ['', 'mobile', 'landscape'], c);
		}

		// левый и правый блок
		var get_size = true;
		if (p.left && p.right) {
			get_size = false;

			edost.window.resize();

			var E_left = E(p.left.id);
			var E_right = E(p.right.id);
			if (E_left && E_right) {
				E_left.style.height = E_right.style.height = 'auto';

				var E_main = E_left.parentNode;

				var w = p.left.width + p.right.width;
				var w2 = self.browser_width - w;
				var s = [];
				if (w2 > 200) s = [p.left.width + 'px', p.right.width + 'px', w + 'px'];
				else if (w2 > -100) s = [(self.browser_width - p.right.width) + 'px', p.right.width + 'px', 'none'];
				else s = ['100%', false, 'none'];

				E_left.style.width = s[0];
				E_right.style.display = (s[1] ? 'block' : 'none');
				if (s[1]) E_right.style.width = s[1];
				E_main.style.maxWidth = s[2];

				var e = document.body;
				C(e, ['right_show', 'right_hide'], s[1] ? 0 : 1);

				if (p.header) {
					var E_header = E(p.header.id_div);
					if (E_header) E_header.style.maxWidth = s[2];
				}
			}
		}

		var w = 0;
		var resize = true;
		if (id != undefined) w = width;
		else if (get_size) {
			if (drawing && !edost.template_2019) return;

			var browser_w = edost.browser('w');
			var browser_h = edost.browser('h');

			if (browser_resize) {
				edost.window.resize();
				if (edost.office && !edost.office.temp) {
					edost.office.resize('redraw');
					if (edost.office2.inside) edost.office2.resize('redraw');
				}
			}

			if (E_template_width) {
				w = E_template_width.offsetWidth;
				if (w == 0) {
					self.start();
					return;
				}
				if (w == template_width) resize = false;
				else {
					template_width = w;
					drawing = true;
					e = E('edost_template_width');
					if (e) e.value = w;
				}
			}
		}
		if (resize) {
			self.count++;
			for (var i = 0; i < data.length; i++) {
				var v = data[i];

				var w2 = w;

				if (v.width == 'screen') w2 = self.browser_width;
				else if (v.width == 'scrollY') w2 = edost.resize.get_scroll('y');
				else if (v.width != '') {
					var e = E(v.width);
					if (!e) continue;
					w2 = e.offsetWidth;
				}

				var c = '';
				var active = 0;
				for (var i2 = 0; i2 < v.param.length; i2++)
					if (!(i2%2)) {
						c = v.param[i2];
						active = Math.floor(i2/2);
					}
					else if (v.param[i2] < w2) break;

				if (id != undefined && v.id != id || id == undefined && v.id != '') continue;

				var ar = false;
				if (v.type == 'id') var ar = [E(v.name)];
				if (v.type == 'body') var ar = [document.body];
				if (v.type == 'name') var ar = document.getElementsByName(v.name);
				if (v.type == 'class' || v.type == 'ico' || v.type == 'ico_row') var ar = document.getElementsByClassName(v.name);
				if (ar) for (var i2 = 0; i2 < ar.length; i2++) if (ar[i2]) {
					var v2 = ar[i2];

					if (v.type == 'ico') {
						var x = v2.getAttribute('data-width');
						if (x) {
							x -= c[1];
							v2.width = x;
						}
					}

					if (v.type == 'ico_row') {
						var n = c[0];
						if (n == 'auto') {
							var n = 0;
							var ar2 = v2.parentNode.parentNode.children;
							if (ar2) for (var i3 = 0; i3 < ar2.length; i3++) if (ar2[i3].style.display != 'none' && ar2[i3].getAttribute('name') != 'edost_description') n++;
						}
						v2.rowSpan = n;
						continue;
					}

					C(v2, v.class, active);
				}
			}
		}

		if (id == undefined) drawing = false;

		if (p.footer && E_left) {
			var e = E(p.footer.id);
			if (e) {
				var h = e.offsetHeight*1 + E_left.offsetHeight*1;
				if (p.header) h += E(p.header.id).offsetHeight*1;
				C(e, ['footer_inside', ''], browser_h > h ? 0 : 1);
			}
		}

		// прокрутка компактного блока "итого"
		if (!sticky && edost.template_2019 && id == undefined) {
			var y = self.get_scroll('y');
			if (y != scroll_Y) {
				scroll_Y = y;
				update_param = true;

				var e = E('order_form_main');
				var e2 = E('order_form_total_div');
				if (e && e2) {
					var rect = e.getBoundingClientRect();
					var top = Math.round(rect.top) + scroll_Y;

					var main_height = e.offsetHeight;
					var browser_h = edost.browser('h');
					var margin_top = 10;
					var up = margin_top + jump;
					var h = e2.offsetHeight;

					if (top < scroll_Y + up && h < main_height) {
						var h2 = Math.round(rect.height - 20);
						var y = top + h2 - (scroll_Y + h);

						if (y > up) y = up;
						e2.style.position = 'fixed';
						e2.style.top = y + 'px';

						template_param.fixed = 1;
						template_param.top = y;
					}
					else {
						e2.style.position = '';
						e2.style.top = margin_top + 'px';

						template_param.fixed = 0;
						template_param.top = margin_top;
					}
				}
			}
		}

		if (sticky && edost.template_2019 && id == undefined) {
			var e = E('order_form_main');
			var e2 = E('order_form_total');
			if (e && e2) e2.style.height = (e.offsetHeight - 20) + 'px';
		}

		if (update_param) V('edost_template_2019', template_param.mode + '|' + template_param.width + '|' + template_param.width2 + '|' + template_param.fixed + '|' + template_param.top);

		if (double_update == 2) double_update = false;
		if (double_update == 1) {
			double_update = 2;
			template_width_main = -1;
			self.update();
		}

	}

}




edost.location = new function() {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C
	var main_function = 'edost.location'

//	edost_city_backup
//	var city_backup = [['edost_location_header_city', ''], ['edost_catalogdelivery_inside_city', ''], ['edost_catalogdelivery_window_city', '']];

	this.zip_value = ''


	// подключение скрипта с данными
	this.add_data = function() {
//		if (self.loading_data == 1 || E('edost_location_data_script')) return;
		self.loading_data = 1;
		edost.js(['location_data.js']);
/*
		var E = document.getElementById('edost_location_script');
		var src = (E ? E.getAttribute('data-src') : false);

		var E = document.body;
		var E2 = document.createElement('SCRIPT');
		E2.id = 'edost_location_data_script';
		E2.type = 'text/javascript';
		if (!src) E2.charset = 'utf-8';
		E2.src = (src ? src : data_link) + data_file;
		E.appendChild(E2);
*/
	}


	this.header = function(id, edost_delivery, loading) {
//	function edost_SetLocationHeader(id, edost_delivery, loading) {
//		console.log(id + ' | ' + loading);

		if (id === 'click') {
			var e = E('edost_location_header');
			if (e) e.click();
			return;
		}

		if (loading) {
			edost_location.window('loading'); // !!!!!
			edost_location.window(); // !!!!!
			edost.backup('location_header', true)
			return;
		}

		if (E('edost_location_city_div')) {
			self.set(id);
			return;
		}

		edost.backup('location_header', false, edost.loading20);

		var param = 'type=html&header=Y&id=' + id + '&edost_delivery=Y' + '&edost_catalogdelivery=' + (window.edost_catalogdelivery ? 'Y' : 'N') + '&template=' + V('edost_location_template');
		edost.post('location', param, function(r) {
/*		BX.ajax.post('<?=$arResult['component_path']?>/edost_location.php', param, function(r) { */
			var e = edost.E('edost_location_header_load');
			if (e) e.innerHTML = r;
			else {
				var e = edost.E('edost_location_header_div');
				if (e) edost.create('WINDOW', 'edost_location_header_load', {'html': r, 'E': e.parentNode});
			}
			edost.location.header(id, true);
		});

	}




	this.zip = function(value, full, submit) {
//		function edost_SetZip(value, full, submit) {

		self.zip_warning('reset');

		var reset = false, original = false;
		if (full == 'original') {
			full = false;
			original = true;
		}
		else if (full == 'reset') {
			full = false;
			reset = true;
		}
		else if (full == undefined) {
			full = true;
			submit = true;
		}

		var country = V('edost_country').split('_');
		V('edost_zip_full', value != '' && full || country[1] != undefined && country[0] != 0 ? 'Y' : '');

		var E_zip = E('edost_zip');
		if (E_zip && !original && (full || reset)) E_zip.value = value;

		var e = E('edost_shop_ZIP');
		if (!e || !reset && (e.value == value || e.value != '' && e.value != '.' && value == '')) return;
		e.value = value;

		if (!submit) return;

		if (E_zip && E_zip.type != 'hidden') self.zip_warning('submit');
		else self.loading('edost_location_address_loading', 'submit');

		edost_location.disable();
		self.cookie();
		edost.submit();

		return true;

	}

	this.zip_warning = function(s) {
//		function edost_SetZipWarning(s) {

		var id = 'edost_location_zip_hint';
		if (s == 'checking') self.loading(id, 'проверка индекса...');
		else if (s == 'submit') self.loading(id, 'submit');
		else {
			if (s != '') {
				var v = (s == '2' ? 2 : 1);
				V('edost_zip_warning', s == 'reset' ? '' : v);
			}

			if (s == '1') s = 'Почтового отделения с указанным индексом НЕ существует!';
			else if (s == '2') s = 'В вашем регионе НЕ найдено почтового отделения с указанным индексом!';
			else if (s == 'digit') s = 'В индексе должны быть только цифры!';
			else if (s == 'format') s = 'В индексе должно быть 6 цифр!';
			else if (s == 'reset') s = '';

			if (s != '') s = '<div class="edost_location_warning">' + s + '</div>';
			H(id, s);
		}

	}

	this.city2 = function(value) {
//		function edost_SetCity2(value) {
		V('edost_city2', value);
	}


	this.loading = function(id, s, update) {
//		function edost_SetLoading(id, s, update) {

		if (id == 'header')	{
			H('edost_location_header_city', 'обновление...');
			return;
		}

		if (s == 'submit') s = 'расчет доставки...';
/*
		if (update || update === undefined) {
			edost_catalogdelivery.calculate('loading_location');

			var e = E('edost_catalogdelivery_inside_city_head');
			if (e) {
				e.style.display = 'none';

				var e = E('edost_catalogdelivery_inside');
				if (e) e.innerHTML = '<div style="text-align: center;"><img style="vertical-align: top;" src="/bitrix/components/edost/catalogdelivery/images/loading.gif" width="64" height="64" border="0"></div>';
			}

			H('edost_catalogdelivery_inside_city', '');
			D('edost_catalogdelivery_inside_detailed', false);
		}
*/
		var c = {'html': '<div class="edost_loading edost_loading_16">' + edost.loading_svg + '<span>' + s + '</span></div>'};
		if (id != 'edost_location_zip_hint') c.display = (s == '' ? false : true);
		E(id, c);

	}

	this.cookie = function(id, zip, zip_full, city2) {
//	this.set_cookie = function(id, zip, zip_full, city2) {

		if (id == undefined) {
			var e = E('edost_shop_LOCATION');
			if (!e) return;
			id = e.value;
		}
		if (zip == undefined) zip = V('edost_shop_ZIP');
		if (zip_full == undefined) zip_full = (V('edost_zip_full') == 'Y' ? true : false);
		if (city2 == undefined) city2 = V('edost_city2');

		edost.cookie('edost_location', encodeURIComponent(zip) + (zip != '' && !zip_full ? '.' : '') + '|' + encodeURIComponent(city2));

	}


//	function edost_SetLocationID(id, header, zip) {
	this.set_id = function(id, header, zip) {
//		console.log('========= NEW ============ edost_SetLocationID: ' + id + ' | ' + header + ' | ' + zip);

		self.loading('edost_location_city_loading', 'submit');

		if (zip === 'get_zip') {
			edost.post('location', 'type=html&mode=get_zip&id=' + id, function(r) {
				V('edost_zip_full', '');
				V('edost_shop_ZIP', r);
				edost.location.set_id(id, header, r);
			});

			return;
		}

		var e = E('edost_shop_LOCATION');
		if (!e) return;

		e.value = id;
		if (header === 'set') self.city2('');
		edost_location.disable();
		if (zip) self.cookie(id, zip); else self.cookie(id);
		edost_location.window('close');

		if ((header || header === undefined) && E('edost_location_header_div')) {
			self.loading('header');
			edost.post('location', 'type=html&header=Y&current=Y', function(r) { edost.html('edost_location_header_div', r); });
		}

		edost.submit();

	}

//	function edost_SetLocation(id, edost_delivery, city, region_id, country_id, get_zip, value_string) {
	this.set = function(id, edost_delivery, city, region_id, country_id, get_zip, value_string, zip) {
//		console.log(id + ' | ' + edost_delivery + ' | ' + city + ' | ' + region_id + ' | ' + country_id + ' | ' + get_zip + ' | ' + value_string);
		edost_delivery = true; // !!!!!

		self.zip_value = zip;

//		<? if (!$arResult['get_zip']) echo 'get_zip = false;'; ?>

		edost_location.window(country_id != undefined ? 'close' : 'loading');

		var header = (E('edost_location_header_div') ? true : false);

		id = id.split('|');
		var select = (id[1] != undefined ? id[1] : false);
		id = id[0].split('_');
		var set = (id[1] != undefined && id[1] == 'set' ? true : false);
		id = id[0];

		if (select !== false) for (var i = select*1+1; i <= 5; i++) D('edost_location_' + i + '_select', false);

		if (set || edost_delivery && country_id != undefined) {
			edost_location.disable();
			H('edost_city_hint', '');
			var e = E('edost_country');
			if (e && e.tagName == 'SELECT') e.disabled = true;
		}

		if (value_string && !header) {
			var e2 = E('edost_city_div');
			var e3 = E('edost_city');
			var e4 = E('edost_location_city_div');
			if (e2 && e3 && e4) {
				e2.style.display = 'none';
				e2.insertAdjacentHTML('beforebegin', '<input class="' + e3.className + '" value="' + value_string + '" readonly type="text">');
			}
		}

		if (set || edost_delivery && id != '' && country_id != undefined) {
			self.zip('', 'reset');
			self.city2('');
			self.set_id(id);
			return;
		}

		var change = (!edost_delivery && select === false || edost_delivery && country_id == undefined ? true : false);
		if (change) {
			D('edost_location_city_zip_div', false);
			D('edost_location_address_div', false);
			D('edost_location_address_head', false);
			self.zip('', 'reset');
		}

		if (!header) self.loading('edost_location_city_' + (change ? 'div' : 'loading'), country_id == undefined ? 'загрузка...' : 'submit');
		else if (country_id == undefined) {
			var e = E('edost_location_city_div');
			if (e) for (var i = 0; i < e.children.length; i++) if (e.children[i].className != 'edost_city_name') { e.removeChild(e.children[i]); i--; }
		}

		if (country_id != undefined) self.loading('header');

		var param = 'type=html&id=' + id + '&edost_delivery=' + (edost_delivery ? 'Y' : 'N') + '&template=' + V('edost_location_template');
		if (country_id != undefined) param += '&country=' + encodeURIComponent(country_id) + '&' + 'region=' + encodeURIComponent(region_id) + '&city=' + encodeURIComponent(city) + (get_zip === false ? '&get_zip=N' : '');
		edost.post('location', param, function(r) {
			edost.location.loading('edost_location_city_loading', '', country_id != undefined ? true : false);

			if (r.indexOf('{') == 0) {
				var v = edost.json(r);
				if (v.error_string != undefined) {
					if (window.edost_SetTemplateLocation) {
						edost_SetTemplateLocation('error', v.error_string);
						return;
					}

					r = '<div class="edost_location_warning">' + v.error_string + '</div>';
/*
					<? if ($arResult['edost_catalogdelivery'] && $arResult['edost_delivery']) { ?>
					edost_catalogdelivery.calculate('error', v.error_string);

					var e = edost.E('edost_catalogdelivery_inside');
					if (e) e.innerHTML = '<div style="text-align: center; color: #F00;">' + v.error_string + '</div>';
					<? } ?>
*/
				}
				else {
					if (self.zip_value !== false) { v.zip = self.zip_value; v.zip_full = true; }
					self.city2(v.city2 != undefined ? city : '');
					if (edost_delivery && v.zip) edost.location.zip(v.zip, v.zip_full != undefined ? true : false);

					var e = edost.E('edost_location_header_div');
					var header_update = (e && v.header ? true : false);
					if (header_update) e.innerHTML = v.header;

					edost.location.set_id(v.id, !header_update);

					return;
				}
			}

			var e = edost.E('edost_location_city_div');
			if (!e) e = edost.E('edost_catalogdelivery_window_city');
			if (e)
				if (!header) {
					edost.H(e, r);

					var e = edost.E('edost_city');
					if (e && e.type != 'hidden') e.focus();
				}
				else {
					var e2 = edost.E('edost_location_header_data');
					if (e2) edost.H(e2, r); else edost.create('WINDOW', 'edost_location_header_data', {'html': r, 'E': e});
//					e.insertAdjacentHTML('beforeend', '<div id="edost_location_header_data" style="display: none;">' + r + '</div>');
				}

			if (country_id == undefined) edost_location.window();
		});

	}

}


// отложенный запуск
edost.register = {'temp': true, 'active_all_update': function() { edost.run('edost.register.active_all_update'); }}
edost.office = {'temp': true, 'set': function(param) { edost.run('edost.office.set', [param]); }}
edost.office2 = {'temp': true, 'set': function(param) { edost.run('edost.office2.set', [param]); }}

// поддержка старых функций
edost.office.window = edost.office.set;
edost.office2.window = edost.office2.set;
edost.window.input = edost.input;
var edost_office = edost.office, edost_office2 = edost.office2, edost_window = edost.window, edost_resize = edost.resize;
edost_resize.change_class = edost.class;
