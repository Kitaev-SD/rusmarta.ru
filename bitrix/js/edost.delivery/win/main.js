var edost = new function() {
	var self = this;
	var js_path = 'edostimg.ru/shop/'
	var key_map = {"q":"й","w":"ц","e":"у","r":"к","t":"е","y":"н","u":"г","i":"ш","o":"щ","p":"з","[":"х","]":"ъ","a":"ф","s":"ы","d":"в","f":"а","g":"п","h":"р","j":"о","k":"л","l":"д",";":"ж","\'":"э","z":"я","x":"ч","c":"с","v":"м","b":"и","n":"т","m":"ь",",":"б",".":"ю","/":".","{":"Х","}":"Ъ","<":"Б",">":"Ю",":":"Ж",'"':"Э"}
	var mask_country = -1
	var E_while = {}
	var hint_id = 'edost_hint_window', hint_timer, hint_pause = 0
	var backup_loading = {}
	var cookie_data = {'shop': {'id': 0, 'site': '', 'function': 0, 'office': 0}}

	this.backup_data = {}
	this.scroll_data = {}
	this.suggest_data = {'package_type': ['запчасти', 'косметика', 'личные вещи', 'оборудование', 'обувь', 'мебель', 'одежда', 'пищевая добавка', 'спорттовары', 'текстиль', 'ТНП', 'хозтовары']}
	this.https = (document.location.protocol == 'https:' || document.location.hostname == 'localhost')
	this.protocol = (self.https ? 'https://' : 'http://')
	this.remove_svg = '<svg class="edost_remove" onclick="%onclick%" viewBox="0 0 88 88" version="1.1" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:1.5;"><path d="M44,44l36,-36c-0.12,0.122 -18.06,18.062 -35.999,36.001l35.999,35.999c0,0 -18,-17.999 -36,-35.998c-18,17.999 -36,35.998 -36,35.998l35.999,-35.999c-17.939,-17.939 -35.879,-35.879 -35.999,-36.001l36,36Z" style="fill:none;stroke:#f00;stroke-width:15px;"></path></svg>'
	this.close = '<div class="edost_window_close" onclick="%onclick%"><svg class="edost_window_close" viewBox="0 0 88 88" version="1.1" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><g transform="matrix(1,0,0,1,-20.116,-20.4011)"><g transform="matrix(0.707107,0.707107,-0.707107,0.707107,64.0579,-26.3666)"><path d="M52.044,52.204l0,-37.983l23.875,0l0,37.983l37.858,0l0,23.875l-37.858,0l0,37.983l-23.875,0l0,-37.983l-38.108,0l0,-23.875l38.108,0Z" style="fill:rgb(145,145,145);"></path></g></g></svg></div>'
	this.loading_svg = '<svg class="edost_loading" viewBox="0 0 256 256" version="1.1" xml:space="preserve" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><path class="edost_loading_anim_1" d="M128,4C134.627,4 140,9.373 140,16L140,52C140,58.627 134.627,64 128,64C121.373,64 116,58.627 116,52L116,16C116,9.373 121.373,4 128,4Z"></path><path class="edost_loading_anim_2" d="M191.305,20.998C197.044,24.312 199.011,31.651 195.697,37.39L177.697,68.567C174.383,74.307 167.044,76.273 161.305,72.959C155.565,69.646 153.599,62.307 156.912,56.567L174.912,25.39C178.226,19.651 185.565,17.684 191.305,20.998Z"></path><path class="edost_loading_anim_3" d="M236.282,65.973C239.595,71.713 237.629,79.052 231.889,82.366L200.712,100.366C194.973,103.679 187.634,101.713 184.32,95.973C181.006,90.234 182.973,82.895 188.712,79.581L219.889,61.581C225.629,58.267 232.968,60.234 236.282,65.973Z"></path><path class="edost_loading_anim_4" d="M252,128C252,134.627 246.627,140 240,140L204,140C197.373,140 192,134.627 192,128C192,121.373 197.373,116 204,116L240,116C246.627,116 252,121.373 252,128Z"></path><path class="edost_loading_anim_5" d="M235.981,191C232.667,196.74 225.328,198.706 219.588,195.392L188.412,177.392C182.672,174.079 180.706,166.74 184.019,161C187.333,155.26 194.672,153.294 200.412,156.608L231.588,174.608C237.328,177.921 239.294,185.26 235.981,191Z"></path><path class="edost_loading_anim_6" d="M189,235.981C183.26,239.294 175.921,237.328 172.608,231.588L154.608,200.412C151.294,194.672 153.26,187.333 159,184.019C164.74,180.706 172.079,182.672 175.392,188.412L193.392,219.588C196.706,225.328 194.74,232.667 189,235.981Z"></path><path class="edost_loading_anim_7" d="M128,252C121.373,252 116,246.627 116,240L116,204C116,197.373 121.373,192 128,192C134.627,192 140,197.373 140,204L140,240C140,246.627 134.627,252 128,252Z"></path><path class="edost_loading_anim_8" d="M65,235.981C59.26,232.667 57.294,225.328 60.608,219.588L78.608,188.412C81.921,182.672 89.26,180.706 95,184.019C100.74,187.333 102.706,194.672 99.392,200.412L81.392,231.588C78.079,237.328 70.74,239.294 65,235.981Z"></path><path class="edost_loading_anim_9" d="M20.019,189C16.706,183.26 18.672,175.921 24.412,172.608L55.588,154.608C61.328,151.294 68.667,153.26 71.981,159C75.294,164.74 73.328,172.079 67.588,175.392L36.412,193.392C30.672,196.706 23.333,194.74 20.019,189Z"></path><path class="edost_loading_anim_10" d="M4,128C4,121.373 9.373,116 16,116L52,116C58.627,116 64,121.373 64,128C64,134.627 58.627,140 52,140L16,140C9.373,140 4,134.627 4,128Z"></path><path class="edost_loading_anim_11" d="M20.019,67C23.333,61.26 30.672,59.294 36.412,62.608L67.588,80.608C73.328,83.921 75.294,91.26 71.981,97C68.667,102.74 61.328,104.706 55.588,101.392L24.412,83.392C18.672,80.079 16.706,72.74 20.019,67Z"></path><path class="edost_loading_anim_12" d="M65,20.019C70.74,16.706 78.079,18.672 81.392,24.412L99.392,55.588C102.706,61.328 100.74,68.667 95,71.981C89.26,75.294 81.921,73.328 78.608,67.588L60.608,36.412C57.294,30.672 59.26,23.333 65,20.019Z"></path></svg>'
	this.loading = '<div style="text-align: center;">' + self.loading_svg + '</div>'
	this.loading20 = '<span class="edost_loading_20">' + self.loading_svg + '</span>'
	this.loading64 = '<div class="edost_loading_64" style="margin: 20px; text-align: center;">' + self.loading_svg + '</div>'
	this.loading128 = '<div class="edost_loading_128" style="margin: 20px; text-align: center;">' + self.loading_svg + '</div>'
	this.delimiter = '<div class="edost_delimiter"></div>';

	this.template_2019 = false
	this.template_ico = 'T'
	this.template_compact = 'off';
	this.template_priority = false
	this.template_no_insurance = false
	this.template_preload_prop = false
	this.ico_path = '';
	this.window_scroll_disable = false
	this.mobile = false
	this.mobile_jump = false
	this.fed_city = ['Москва', 'Санкт-Петербург', 'Севастополь']
	this.key_lang = 'ru'

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

	var A = this.A = this.in_array = function(v, s, p) {
		if (s) for (var i = 0; i < s.length; i++) if (v == s[i]) return (p ? i : true);
		return false;
	}
	var I = this.I = function(s, k, v, n) {
		if (s) for (var i = 0; i < s.length; i++) if (v == s[i][k]) return i;
		if (n != undefined) { s.push(n); return (s.length - 1) };
		return false;
	}

	this.filter = function(s, p, a) {
		u = {
			'I': /[^0-9 ]/g,
			'I2': /[^0-9]/g,
			'F': /[^0-9 ,.]/g,
			'gps': /[^0-9 ,.]/g,
			'code': /[^a-zA-Z0-9а-яА-ЯЁё._ -]/g,
			'name': /[^a-zA-Z0-9а-яА-ЯЁё. \-]/g,
			'address': /[^a-zA-Z0-9а-яА-ЯЁё.,()\/ -]/g,
			'address2': /[^a-zA-Z0-9а-яА-ЯЁё.,\/ -]/g,
			'tel': /[^0-9а-яА-ЯЁё.+(), -]/g,
			'schedule': /[^0-9а-яА-ЯЁё.:\n -]/g,
			'info': /[^a-zA-Z0-9а-яА-ЯЁё._",№!:()\/\n -]/g,
			'metro': /[^0-9а-яА-ЯЁё() -]/g //"
		}
		var o = false, f = (A(p, ['I', 'F']) ? 1 : 0);
		if (p == 'S') { var n = 0; for (var i = 0; i < a; i++) if (s[i] == ' ') n++; return n; }
		if (typeof s === 'object') { o = s; s = V(o); var s2 = s; }
		if (p == 'ru_normal') s = self.ru(s, 'normal', o);
		else if (u[p]) {
			s = s.replace(u[p], '');
			if (p == 'F') s = s.replace(/,/g, '.');
		}
		else if (p == 'search') s = s.replace(/;/g, ',').replace(/ /g, ',').replace(/^,+|,+$/gm, '').replace(/,+/g, ',');
		if (o) {
			var i = o.selectionStart, k = (a && a.keyCode ? a.keyCode : ''), u = (s.length != s2.length);
			if (k && i != o.selectionEnd) return;
			a = false;
			if (f && k === 8 && s[i-1] == ' ') i--;
			if (f && k === 46 && s[i] == ' ') i++;
			V(o, s);
			if (u) i--;
			else if (f && !k) {
                var r = s.replace(/[^0-9.]/g, '').split('.'), v = r[0];
				s = '';
				W(Math.ceil(r[0].length/3), function() { var n = v.length; s = (n > 3 ? v.substr(n-3) : v) + (s != '' ? ' ' : '') + s; v = v.substr(0, n-3); });
				if (r[1] != undefined) s += '.' + r[1];
				V(o, s);
				if (s == s2) i = -1;
				else {
					var n = self.filter(s, 'S', i) - self.filter(s2, 'S', i);
					if (n > 0) i++; else if (n < 0) i--;
				}
			}
			if (i >= 0) o.setSelectionRange(i, i);
		}
		if (a) s = s.split(',');
		return s;
	}

	this.sort = function(s, p, c) {
		s.sort(function(a, b) {
			if (c == undefined && p == undefined) return (a < b ? -1 : 1);
			if (c != undefined) return (a[p][c] < b[p][c] ? -1 : 1);
			return (a[p] < b[p] ? -1 : 1);
		});
	}

	this.json = function(s) {
		if (!s || s[0] != '{') return false;
		return (window.JSON && window.JSON.parse ? JSON.parse(s) : eval('(' + s + ')'));
	}

	this.trim = function(s, space) {
		if (s) {
			s = s.replace(/^\s+|\s+$/gm, '');
			if (space) s = s.replace(/\s+/g, ' ');
		}
		return s;
	}

	this.digit = function(v) {
		if (v == 0) v = '';
		return v;
	}

	this.browser = function(s) {
		if (s == undefined) return {'w': self.browser('w'), 'h': self.browser('h')};
		if (self.mobile) return window[s == 'w' ? 'innerWidth' : 'innerHeight'];
		else return document.documentElement[s == 'w' ? 'clientWidth' : 'clientHeight'];
	}

	// конвертирование английской раскладки в русскую
	this.ru = function(s, param, o) {

		if (self.mobile) return s;

		var p = -1;
		var a = (s && s.key != undefined); // определение языка по событию из keydown
		if (a) {
			o = s;
			s = o.key;
			if (s.length > 1) return;
			P(o.target, 'key', o.target.selectionStart + 1);
		}
		else if (o) p = P(o, 'key');

		var n_en = s.replace(/[^A-Za-z\[\{\]\}\;\:\'\"\,\<\.\>]/g, '').length; //'
		var n_ru = s.replace(/[^А-Яа-яЁё]/g, '').length;

		if ((a && s.length == 1 || !a) && s.replace(/[^A-Za-z]/g, '').length > 0 || a && o.target.value.length == 0 && n_en > 0) self.key_lang = 'en';
		if (a && s.length == 1 && s.replace(/[^А-Яа-яЁё]/g, '').length > 0) self.key_lang = 'ru';
		if (a) return;

		var r = '';
		if (self.key_lang == 'ru' || !p) r = s;
		else W(s.length, function(i) {
			var v = s[i];
			var u = v.toLowerCase();
			if (p == -1 || i == p-1) if (key_map[u] != undefined) v = (v != u ? key_map[u].toUpperCase() : key_map[u]);
			r += v;
		});
		if (p != -1) P(o, 'key', '');
		if (A(param, ['full', 'normal'])) {
			r = r.replace(/[ё]/g, 'е');
			r = r.replace(/[^А-Яа-я0-9 ,.-]/g, ' ');
			r = r.replace(/  /g, ' ');
			if (param == 'full') r = self.trim(r, true);
		}
		return r;

	}

	// получение параметра в адресе документа "...?name=1&..."
	this.get = function(name) {
		var s = window.location.search.split('&' + name + '=');
        if (s[1] == undefined) s = window.location.search.split('?' + name + '=');
		return (s[1] != undefined ? s[1].split('&')[0] : '');
	}

	this.create = function(tagName, id, p, s) {
		if (tagName == 'WINDOW') {
			tagName = 'DIV';
			if (!p) p = {};
			if (p.class === undefined) p.class = id;
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
	this.remove = function(o) {
		if (!o) return;
		var e = E(o);
		if (e) if (e.remove) e.remove(); else e.parentNode.removeChild(e);
	}
	this.move = function(o, u, p) {
		o = E(o); u = E(u);
		if (!o || !u) return;
		H(u, (p == 'add' ? H(u) : '') + H(o));
		self.remove(o);
	}

	var E = this.E = function(o, v, w) {
		if (w) {
			if (w === true) w = 1;
			if (!E_while[w]) E_while[w] = {'s': [], 'i': 0};
			var u = E_while[w];
			if (o === '' && v === 'break') { u.i = u.s.length; return; }
			if (o === '' && v === 'index') return u.i-1;
		}
		if (v === 'all' || v === 'form') {
			if (o) {
				if (!w || w && u.i == 0) {
					var s = (typeof o !== 'object' ? o : o[1]);
					if (v == 'form') s = s + ' input, ' + s + ' select, ' + s + ' textarea';
					if (typeof o !== 'object') o = document.querySelectorAll(s); else o = o[0].querySelectorAll(s);
					if (w) u.s = o;
				}
				if (w) do {
					o = (u.s && u.s[u.i] ? u.s[u.i] : false);
					if (o) u.i++; else { E_while[w] = false; break; }
				} while (v === 'form' && o && !o.name);
			}
			return o;
		}

		if (o) if (typeof o !== 'object') o = document.getElementById(o);
		else if (!o.tagName && o[0]) o = (o[1] ? o[0] : document).querySelector(o[1] ? o[1] : o[0]);

		var r = undefined;
		if (!o || !o.tagName) o = false;
		if (v) {
			if (typeof v !== 'object') v = {'get': v};
			if (v.get == 'html') r = '';
			if (v.get == 'offsetHeight') r = 0;
			if (o) {
				if (v.display === false || v.display === 0) v.display = 'none';
				else if (v.display === true || v.display === 1) v.display = 'block';

				for (var k in v) if (k != 'param') {
					var get = false;
					if (k == 'get') { k = v[k]; get = true; }
					var s = A(k, ['width', 'height', 'maxWidth', 'opacity', 'display', 'padding', 'margin', 'marginTop', 'position', 'left', 'right', 'top', 'bottom', 'color', 'borderColor', 'background', 'cursor', 'fontSize', 'fontWeight', 'textAlign']);

					var k2 = k;
					if (k == 'html') k2 = 'innerHTML';
					else if (k == 'class') k2 = 'className';
					else if (k == 'value' && o.tagName == 'INPUT' && o.type == 'checkbox') k2 = 'checked';

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
								if (!c[a] || !o.classList.contains(c[a])) {
									for (var i = 0; i < c.length; i++) if (i != a && c[i] && o.classList.contains(c[i])) o.classList.remove(c[i]);
									if (c[a]) o.classList.add(c[a]);
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
/*
	W('query', function(v) {...
	W([parent <element>, 'query'], function(v) {...
	W(number, function(i) {...
	W([... <not element>, ..., ...], function(v,i) {...
*/
	var W = this.W = function(o, p, w) {
		if (p === 'post') {
			var r = [];
			W(o, w ? w : '', function(v) {
				if (!v.name || v.tagName == 'INPUT' && v.type == 'radio' && !v.checked) return;
				var s = V(v);
				if (s === true) s = 1;
				if (s === false) s = '';
				if (s === '' && v.type == 'checkbox') return;
				r.push(v.name + '=' + encodeURIComponent(s));
			});
			return r.join('&');
		}
		if (o === 'break') E('', 'break', p ? p : 1);
		else if (o === 'index') return E('', 'index', p ? p : 1);
		else {
			var s = '';
			if (w && typeof w === 'function') { s = p; p = w; w = 0; };
			if (typeof p === 'function') {
				var n = (typeof o === 'number' ? o : 0);
				if (n || typeof o === 'object' && (o[0] == undefined || !o[0].tagName)) {
		          	if (n) { for (var i = 0; i < n; i++) if (p(i) === false) break; }
		          	else for (var i = 0; i < o.length; i++) if (p(o[i], i) === false) break;
		          	return;
				}
				var v, k = 0;
				for (k in E_while) if (!E_while[k]) { w = k; break; }
				if (!w) w = k*1 + 1;
				while (v = W(o, s, w)) if (p(v, W('index', w)) === false) W('break', w);
			}
			else return E(o, p ? p : 'all', w ? w : 1);
		}
	}
	var N = this.N = function(o, p, n) {
		o = E(o);
		if (o) {
			var f = '';
			if (p === 'L') p = 'previousElementSibling'; // 'previousSibling';
			else if (p === 'R') p = 'nextElementSibling'; // 'nextSibling';
			else {
				if (p) if (A(p, ['TABLE', 'TBODY', 'TR', 'TD', 'DIV', 'SPAN'])) f = p; else n = p;
				p = 'parentNode';
			}
			if (!n) n = 1;
			while (o && o[p] && (f && o.tagName != f || !f && n > 0)) { o = o[p]; n--; }
		}
		return o;
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
	var C = this.C = this.class = function(o, v, a) {
		var i = (a === 'in' || a === 'contains');
		if (!i) {
			if (a === true) a = 1; else if (a === false) a = 0;
			var c = (a === undefined ? v : [v, a]);
		}
		var r = E(o, v === undefined || i ? 'class' : {'class': c});
		if (!i) return r;
		else if (i === 'in') return (r && r.indexOf(v) >= 0);
		else return (r && A(v, r.split(' ')));
	}
	var P = this.P = this.data = function(o, n, v) {
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
			e = E(e);
			if (!e) return;

			var p = (typeof p === 'object' ? p : {});
			var rect = e.getBoundingClientRect();
			var h = edost.browser('h');
			var m = (p.margin ? p.margin : 150);

			if (p.top) p.position = rect.top - p.top;
			else
				if (rect.top < m) p.position = rect.top - m;
				else if (rect.top > h - 250) p.position = rect.top - h + m;
				else p.position = 0;

			if (!p.speed && Math.abs(p.position) > 1500) p.speed = 0.8;

			self.scroll_data = p;
		}

		p = self.scroll_data;
		if (p.position != 0) {
			var s = (p.speed ? p.speed : 0.2);
			var m = 200*s;
			var y = p.position * s;
			if (Math.abs(y) > m) y = (y < 0 ? -m : m);

			var e = (p.window ? E('edost_window_data') : window);
			if (e) e.scrollBy(0, y);

			p.position -= y;
			if (Math.abs(p.position) > 1) { window.setTimeout('edost.scroll()', 25); return; }
		}
		if (p.function) window.setTimeout(p.function, 1);

	}

	this.alarm = function(e, q, c, w, a) {
		if (q === true) { w = q; q = ''; }
		if (!q) q = 'div.edost_prop_error span';
		if (!c) c = 'edost_prop_blink';
		var s = "edost.alarm('', '" + q + "', '" + c + "', " + (w ? 1 : 0);
//		if (e && e !== 'window') { edost.scroll(e, {'function': s + ')'}); return; }
		if (e) { edost.scroll(e, {'function': s + ')', 'window': w}); return; }
//		while (v = W((e === 'window' ? '#edost_window ' : '') + q)) C(v, [c], a ? 1 : 0);
		while (v = W((w ? '#edost_window ' : '') + q)) C(v, [c], a ? 1 : 0);
		if (!a) window.setTimeout(s + ', 1)', 2000);
	}

	this.hint = function(e, p) {

		var param = (typeof e === 'object' ? self.param(e) : {});
		if (e === 'start') {
			self.hint('timer');
			while (v = E('.edost_hint_link', 'all', true)) self.hint(v, 'event');
		}
		else if (e === 'timer') {
			if (hint_timer) hint_timer = window.clearInterval(hint_timer);
		}
		else if (p === 'event') {
			if (param.inside || param.click) {
				if (!e.onclick) { e.onclick = new Function('edost.hint(this, "' + (param.inside ? 'inside' : 'click') + '")'); e.style.cursor = 'pointer'; }
			}
			else if (!e.onmouseenter) {
				e.onmouseenter = new Function('edost.hint(this, 1)');
				e.onmouseleave = new Function('edost.hint(this, 0)');
			}
		}
		else if (e.id === hint_id) {
			hint_pause = p;
		}
		else if (e === 'hide' || e === 'close') {
			if (e === 'hide' && hint_pause) return;
			self.hint('timer');
			E(hint_id, {'html': '', 'display': false});
		}
		else if (e === 'window') {
			return (D('edost_window') == 'block' ? true : false);
		}
		else if (p === 'inside') {
			e = N(e,'R');
			D(e, D(e) != 'block');
			if (self.hint('window')) edost.window.resize();
		}
		else {
			var o = E(hint_id);
			if (!o) {
				o = self.create('WINDOW', hint_id);
				self.hint(o, 'event');
			}
			var h = H(N(e,'R'));
			if (p === 'click') h = self.close.replace('%onclick%', "edost.hint('close')") + h;
			if (h != H(o)) {
				o.style.cssText = '';
				E(o, {'html': h, 'display': true, 'class': 'edost_hint_window ' + (param.class ? param.class : 'edost_hint_normal')});
				var a = self.hint('window');
				if (!a) { if (!param.style) param.style = {}; param.style.position = 'absolute'; }
				if (param.style) E(o, param.style);
				var r = e.getBoundingClientRect();
				var s = self.browser(), w = o.offsetWidth, h = o.offsetHeigth, w2 = 0, m = [20, 0], shift_right = false;
				if (param.shift) {
					m = param.shift.split(',');
					if (m[0] === 'right') {
						shift_right = true;
						m[0] = r.width + 10;
						m[1] = 0;
					}
					else {
						if (m[1] == undefined) m[1] = m[0];
						m[0] = (m[0] === 'center' ? -Math.round(w*0.5 - r.width*0.5) : m[0]*1);
					}
				}
				var x = r.left + m[0], y = r.top + (!a ? edost.resize.get_scroll('y') : 0) + m[1]*1;
				if (w > s.w - 20) { w2 = s.w - 10; x = 5; }
				else {
					if (x < 0) x = 0;
					if (x + w > s.w) { x -= x + w - s.w + 10; if (!m[1]) y += 25; };
					if (y + h > s.h) y -= y + h - s.h + 10;
				}
				E(o, {'left': x + 'px', 'top': y + 'px'});
				if (!(param.style && param.style.width && !w2)) E(o, {'width': w2 ? w2 + 'px' : ''});
//				if (shift_right) D(o, r.left + m[0] + w < s.w);
				if (p === 'click') return;
			}
			if (p === 'click') E(o, {'display': false, 'html': ''});
			else {
				self.hint('timer');
				if (p) hint_pause = 0; else hint_timer = window.setInterval('edost.hint("hide")', 100);
			}
		}

	}

	this.backup = function(param, recovery, loading) {
		if (param === 'location') param = ['edost_zip', 'edost_zip_full', 'edost_shop_ZIP', 'edost_location_city_div'];
		if (param === 'location_header') param = ['edost_location_header_city', 'edost_catalogdelivery_inside_city', 'edost_catalogdelivery_window_city'];
		for (var i = 0; i < param.length; i++) {
			var v = param[i];
			var e = E(v);
			if (!e) continue;
			var p = (e.tagName == 'INPUT' ? 'value' : 'innerHTML');
			if (recovery) {
				e[p] = (self.backup_data[v] ? self.backup_data[v] : '');
				backup_loading[v] = '';
			}
			else {
				if (!backup_loading[v]) self.backup_data[v] = e[p];
				if (loading && e.tagName != 'INPUT') { e[p] = loading; backup_loading[v] = loading; }
			}
		}
	}

	// подключение скриптов
	this.js = function(name) {

		var e = E('edost_main_js');
		var path = P(e, 'path');
		var file = P(e, 'file');
		var version = P(e, 'version');
		var key = (e ? e.src.split('?a=')[1] : false);
		var charset_main = (e && e.charset ? e.charset : '');

		if (file) file = file.split(',');

		if (name == undefined) {
			name = [];
			var s = ['main.css', 'office.js', 'location.js', 'admin.js', 'location_city', 'pickpoint'];
			if (file !== '') for (var i = 0; i < s.length; i++) if (!file && !A(s[i], ['location_city', 'pickpoint']) || A(s[i], file)) name.push(s[i]);
		}
		else if (typeof name !== 'object') name = [name];

		var E_head = document.head;
		for (var i = 0; i < name.length; i++) {
			var v = name[i];

			var k = key;
			if (!path && v == 'location_data.js') k = '5';
			k = (k ? '?a=' + k : '');

			var script = (v.indexOf('.js') > 0 ? true : false);
			var id = 'edost_' + v.replace('.', '_');
			var src = (path ? path : self.protocol + js_path) + (version ? version + '/' : '') + v + k;
			var charset = (!path || charset_main == 'utf-8' ? 'utf-8' : false);

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

	this.cookie = function(s, v) {

//		if (Array.isArray(s)) {
		if (typeof s === 'object') {
			var k = {}, o = s[0];
			if (cookie_data[o]) k = cookie_data[o];
			else {
				if (edost[o] && edost[o].param) k = edost[o].param.cookie;
				o = 'edost_' + o;
			}
			s = s[1];
			var r = [], i = 0, c = self.cookie(o).split('|');
			for (var u in k) {
				var w = (c[i] != undefined ? c[i] : '');
				if (s === undefined) k[u] = w;
				else if (s != u) r.push(w);
				else {
					if (v === undefined) return w;
					r.push(v.replace(/\|/g, ''));
				}
				i++;
			}
			if (s === undefined) return k;
			if (v !== undefined) self.cookie(o, r.join('|'));
			return;
		}

		if (v === undefined) {
			var r = document.cookie.match('(^|;) ?' + s + '=([^;]*)(;|$)');
			return (r ? decodeURIComponent(r[2]) : '');
		}

		document.cookie = s + '=' + encodeURIComponent(v) + '; path=/; SameSite=Strict; expires=Thu, 01-Jan-2050 00:00:01 GMT';

	}

	this.run = function(name, p) {
		var a = true;
		var f = window;
		var u = name.split('.');
		var n = u.length - 1;
		for (var i = 0; i <= n; i++) if (!f[ u[i] ] || (i == n && f.temp)) a = false; else f = f[ u[i] ];
		var s = [];
		if (p != undefined) W(p, function(v) {
			if (v == undefined) v = 'undefined';
			else if (typeof v != 'boolean') v = "'" + v.replace(/'/g, '\\\'') + "'";
			s.push(v);
		});
		s = s.join(',');

//		if (a) console.log("edost main RUN ======== " + name + "(" + s + ")");
//		else console.log("edost main repeat ======== edost.run('" + name + "'" + (p != undefined ? ", [" + s + "]" : '') + ")");

		if (a) window.setTimeout(name + "(" + s + ")", 1);
		else window.setTimeout("edost.run('" + name + "'" + (p != undefined ? ", [" + s + "]" : '') + ")", 500);
	}

	this.number = function(i) {
		return (i && i !== true ? String(i).replace(/,/g, '.').replace(/[^0-9.]/g, '')*1 : 0);
	}

	this.param = function(v, o) {
		var r = {};
		if (o === 'post') {
			if (v) v = v.replace(/[&]/g, ';');
			o = false;
		}
		if (v && typeof v === 'object') {
			if (!v.getBoundingClientRect) r = v; else r.e = v;
			if (r.e) {
				var u = r.e.getBoundingClientRect();
				r.position = [u.left + u.width*0.5, u.top];
				if (!r.position[0] && self.param.position) r.position = self.param.position;
				if (r.position[0] == 0 && r.position[1] == 0) r.position = null;
				v = P(r.e, 'param');
			}
		}
		if (v) W(v.split(';'), function(v) {
			v = v.split('=');
			if (v[0] == 'style') r['style'] = {};
			if (v[1] === undefined) return;
			v[0] = self.trim(v[0]);
			v[1] = self.trim(v[1]);
			if (v[0] == 'service') v[1] = (v[1] ? v[1].split(',') : []);
			if (v[0] == 'href') v[1] = decodeURIComponent(v[1]);
			if (r.style) r.style[v[0]] = v[1]; else r[v[0]] = v[1];
		});
		if (o) {
			s = self.param(P(o, 'param'));
			for (k in s) if (!r[k]) r[k] = s[k];
		}
		return r;
	}

	this.jump = function(o, p) {
		o = E(o);
		if (!o) return;
		var a = 0;
		if (p && p.keyCode == 13) p = 'input';
		if (p === 'input') W('input[type="text"]', function(v) { if (v.id == o.id) a = 1; else if (a && D(v.parentNode) !== 'none') { v.focus(); return false; } });
	}

	this.mask = function(param, e, event) {

		if (!self.tel.country) return;

		if (param === 'start') {
			var s = E('input[type="tel"]', 'all');
			if (s) for (var i = 0; i < s.length; i++) {
				v = s[i];

				if (P(v, 'country') || v.offsetHeight == 0) continue;

//				v.placeholder = '+7-9xx-xxx-xx-xx"';

				v.onkeydown = new Function('event', 'edost.input("keydown", this, event)');
				v.onblur = new Function((self.template_preload_prop ? 'submitProp(this); ' : '') + 'edost.input("blur", this)');
				if (self.template_preload_prop) v.onchange = '';

				if (edost.mobile) v.ontouchend = new Function('E_mask = this; window.setTimeout("edost.mask(\'click\', E_mask)", 100)' );
				else {
					v.onclick = new Function('edost.mask("click", this);');
					v.onmousemove = new Function('event', 'edost.mask("mouse", this, event)');
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
			if (start && s.indexOf('+') == -1) s = '+' + s;
			var n = s.replace(/[^0-9+]/g, '');

			var c = '';
			if (!start && event && event[1] != undefined) c = event[1];
			else {
				for (var k in self.tel.mask) if (k != 'M' && A(k, self.tel.country)) {
					var m = self.tel.mask[k];
					if (n.indexOf('+' + m.c + (m.s ? m.s : '')) == 0) c = k;
				}
				if (n.length <= 2 || c !== '' && n == '+' + self.tel.mask[c].c) c = 'start';
				if (c === '' || c == 'start') {
					if (c != 'start' && n.length != 0 && self.tel.manual) c = 'M';
					else {
						c = V('edost_country');
						c = (c ? c.split('_')[0] : '0');
						if (!A(c, self.tel.country) && self.tel.manual) c = 'M';
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

			P(e, 'country', c);
			if (self.tel.country.length > 1) {
				C(e, ['', 'edost_tel_flag'], c == 'M' ? 0 : 1);
				self.mask('flag', e, c);
			}

			e.value = r;

			return r;
		}
		if (param === 'mouse') {
			if (event) C(e, ['edost_tel_flag_on'], event.offsetX > 30);
			return;
		}
		if (param === 'country' || param === 'country_show') {
			e = E([N(e), 'div.edost_tel_country']);
			if (param === 'country') return e;
			return (!e || D(e) == 'none' ? false : true);
		}
		if (param === 'flag') {
			e.style.backgroundImage = (event != 'M' ? "url('" + self.ico_path + 'flag/' + event  + '.gif' + "')" : 'none');
			return;
		}
		if (param === 'set') {
			if (!e) return;

			mask_blur = false;
			self.mask('close', e);

			var c = P(e, 'country');
			var e2 = E([N(e,2), 'input[type="tel"]']);

			if (c == 'M') s = '+';
			else {
				var s = e2.value;
				var m = self.tel.mask[c];
				var p = s.indexOf(self.tel.format[0]);
				if (p > 0) s = '+' + m.c + s.substr(p);
			}
			self.mask('parse', e2, [s, c]);

			C(e2, ['', 'edost_tel_flag'], c == 'M' ? 0 : 1);

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
				edost.create('DIV', '', {'class': 'edost_tel_country', 'html': s, 'E': N(e)});
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
			else while (v = W([e ? N(e,2) : document, 'div.edost_tel_country'])) D(v, false);

			return;
		}
		if (param === 'redraw') {
			W([N(e), 'div.edost_tel_country div'], function(v, i) { C(v, ['edost_tel_country_active'], mask_country == i ? 0 : 1) });
			return;
		}

		var r = self.mask('model', e.value);
		var key = (event && event.keyCode ? event.keyCode : event);
		var p = e.selectionStart;
		var n = -1;
		var keydown = true;
		var country_list = '';
		var country = P(e, 'country');
		var country_change = (A(key, [38, 40, 13]) ? true : false);

		if (param === 'focus') p = 0;

		if (country != 'M') {
			if (param == 'click') {
				for (var i = p; i >= 1; i--) if (r.s[i] != undefined)
					if (r.n[i] == ' ') n = i;
					else { if (i != p && r.m[i+1] == ' ') n++; break; }
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

				country = P(e, 'country');
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
			if (key == 36 || A(key, [37, 8]) && p == 0) { if (param !== 'focus') country_list = 'open'; } // home + курсор на коде страны
		}

		var count = self.tel.country.length;
		if (count > 1) {
			var e2 = self.mask('country', e);
			var a = (e2 && e2.style.display != 'none' ? true : false);
		}
		if (A(key, [38, 40, 13])) {
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
				self.mobile_jump = rect.top - 10;
				self.window.resize();
			}
			return;
		}
		if (param == 'blur') {
			self.mobile_jump = false;
			self.mask('close_blur', e);
			if (self.mobile) self.window.resize();
			return;
		}
		if (param == 'keydown') {
			if (e.type == 'tel') {
				if (event.ctrlKey === true || event.shiftKey === true) return;

				var a = false;
				if (A(event.keyCode, [8, 9, 27, 46, 35, 36, 37, 38, 39, 40, 13])) a = true;
				else if (event.keyCode >= 48 && event.keyCode <= 57 || event.keyCode >= 96 && event.keyCode <= 105) a = true;
				else if (P(e, 'country') == 'M' && A(event.keyCode, [32, 107, 109, 187, 189])) a = true;

				if (!a || !self.mask('keydown', e, event)) if (event.preventDefault) event.preventDefault(); else event.returnValue = false;
			}
			return;
		}
		if (param == 'update') {
			if (e && e.value != '' && e.classList.contains('edost_prop_error')) {
				C(e, ['edost_prop_error'], 1);
				if (A(e.id, self.address_id)) W(self.address_id, function(v) { C(v, ['edost_prop_error'], 1); });
				var id = P(e, 'error_id');
				if (id) H(['div[data-error_id="' + id + '"]'], '');
			}
			self.window.resize();
		}
		if (e.type == 'tel') self.mask(false, e);

	}

	this.submit = function() {
		if (window.submitForm) submitForm();
		else if (window.BX && BX.Sale && BX.Sale.OrderAjaxComponent && BX.Sale.OrderAjaxComponent.sendRequest) BX.Sale.OrderAjaxComponent.sendRequest();
		else if (edost.admin && edost.admin.update_delivery) edost.admin.update_delivery();
		else if (window.edost_catalogdelivery) edost_catalogdelivery.calculate();
		else if (E('edost_catalogdelivery_param')) edost.catalogdelivery.calculate();
	}

	this.post = function(u, p, c) {
		if (u == 'location') u = '/bitrix/components/edost/locations/ajax.php';
		if (u == 'catalogdelivery') u = '/bitrix/components/edost/catalogdelivery/ajax.php';
		if (window.BX && BX.ajax) BX.ajax.post(u, p, c);
		else if (window.fetch) fetch(u, {method: 'post', headers: {'Content-type': 'application/x-www-form-urlencoded'}, body: p}).then(function(r) { const s = edost.param(r.headers.get('content-type')); r.arrayBuffer().then(function(r) { const v = new DataView(r); const w = new TextDecoder(s.charset ? s.charset : 'utf-8'); c(w.decode(v)); }); });
		else { var x = new XMLHttpRequest(); x.onreadystatechange = function() { if (this.readyState == 4) c(this.responseText); }; x.open('POST', u, true); x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded'); x.send(p); }
	}

}




edost.window = new function() {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C, A = edost.A, I = edost.I, P = edost.P, W = edost.W, N = edost.N
	var main_function = 'edost.window'
	var main_id = main_function.replace('.', '_'), fon_id = main_id + '_fon', head_id = main_id + '_head', loading_id = main_id + '_loading', name_id = main_id + '_name', button_id = main_id + '_button', save_id = main_id + '_save', close_id = main_id + '_close', data_id = main_id + '_data', frame_id = main_id + '_frame'
	var format, param_profile, onkeydown_backup = 'free', overflow_backup = false, scroll_backup = false, data_backup = false, onclose = '', window_width = 0, data_width = 0, browser_width = 0, browser_height = 0
	var onclose_set_office_start, head_color_default = '#888', loading = false, scroll_reset = false, window_scroll = false, request = false
	var alarm_position = 0, error_id = 0, save_error = false
//	var arguments_backup = false, window_id = false,
	var E_mask, mask_blur = false
	var depend = ['count', 'cod']
	var head_ico = '#edost_window .edost_ico_window'

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
			"landscape_head_width": 120,
		},
		"window_location": {
			"head": 'Выбор местоположения доставки',
			"class": 'edost_window_location',
			"width": 750,
			"height": 750,
			"landscape_head_width": 155,
		},
		"frame": {
			"width": 1100,
			"up": true,
			"no_padding": true,
			"landscape_head_width": 100,
			"small_height": true,
		}
	}

	this.filter = function(s) {
		return s.replace(/ id=\"/g, ' id="window_'); // '
	}

	this.config = function(param) {
		var r = (param === undefined ? {} : false);
		// глобальные
		if (self.config_data[self.mode])
			 if (param === undefined) r = self.config_data[self.mode];
			 else if (self.config_data[self.mode][param]) r = self.config_data[self.mode][param];
		// из параметров
		W(['head', 'width', 'height', 'confirm', 'save', 'small_height', 'landscape_head_width', 'no_padding', 'class'], function(v) {
			if (self.param[v] === undefined) return;
			if (param === undefined) r[v] = self.param[v];
			else if (param === v) r = self.param[v];
		});
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
			var c = P(e, 'country');
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

			C(e, ['edost_prop_error'], 0);
			e.oninput = new Function('event', main_function + '.input("update", this, event)');

			var e2 = e;
			if (type == 'passport') {
				e2 = E('edost_location_passport_div');
				if (!e2) return false;
			}
			if (A(type, ['address', 'street', 'zip'])) {
				e2 = E('edost_location_address_div');
				if (!e2) return false;
			}

			var s = N(e2).getElementsByTagName('DIV');
			if (s) for (var i = 0; i < s.length; i++) if (s[i].classList.contains('edost_prop_error')) {
				var id = P(s[i], 'error_id');
				if (!id) {
					error_id++;
					id = error_id;
					P(s[i], 'error_id', id);
				}
				P(e, 'error_id', id);
				H(s[i], '<span class="edost_prop_error">' + error + '</span>');
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
			var e2 = E('edost_address_hide');
			var address = (e2 && e2.value != 'Y' ? true : false);

			W([e, 'input'], function(v) {
				if (fast || v.id && v.type != 'hidden' && (v.name.indexOf('ORDER_PROP_') == 0 || A(v.id, ['edost_agreement', 'edost_passport_2', 'edost_passport_3', 'edost_passport_4', 'edost_zip']))) {
					type = P(v, 'type');
					if (!type && A(v.id, ['edost_passport_2', 'edost_passport_3', 'edost_passport_4'])) type = 'passport';
					if (!type && address && v.id == 'edost_zip') type = 'zip';
					if (!type) return;

					var u = (type == 'agreement' ? v.checked : edost.trim(v.value, true));
					if (self.error(v, u, type)) {
						r = false;
						if (!first) first = v;
					}
				}
            });

			if (address) {
				var a = {};
				var city2_required = E('edost_city2_required');
				W([e, 'input'], function(v) { if (A(v.id, edost.address_id)) a[v.id] = (edost.trim(v.value, true) != '' ? true : false); });
				var a_city2 = (city2_required && city2_required.value == 'Y' && !a.edost_city2 ? true : false);
				var a_street = (!a.edost_street && !a.edost_house_1 && !a.edost_house_2 && !a.edost_door_1 ? true : false);
				if (a_city2 || a_street) {
					W([e, 'input'], function(v) {
						if (a_city2 && v.id == 'edost_city2' || a_street && v.id != 'edost_city2' && A(v.id, edost.address_id)) if (self.error(v, '', 'address')) {
							r = false;
							if (!first) first = v;
						}
					});
				}
				else {
					var street_required = E('edost_street_required');
					var area = E('edost_area');
					if (street_required && street_required.value != '' && V(area) == 'Y') {
						var street = E('edost_street');
						if (street && self.error(street, '', 'street')) {
							r = false;
							if (!first) first = street;
						}
					}
				}
			}
		}

		W('div.edost_prop_error span', function(v) { C(v, ['edost_prop_blink'], 1) });

//		if (!r && first) edost.alarm(fast ? 'window' : first);
		if (!r && first) edost.alarm(first, fast);

		return r;

	}

	this.copy = function(e, save) {

		if (!e) return false;

		var r = true;

		W([e, 'input'], function(v) {
			name = P(v, 'name');
			if (!name) return;

			type = v.type;
			s = P(v, 'type');
			if (s) type = s;

			var e2 = E(name);
			if (!e2) return;

            if (save) {
				if (type == 'checkbox') e2.checked = v.checked;
				else e2.value = edost.trim(v.value, true);

				if (type == 'tel') edost.mask('parse', e2);
				if (edost.template_preload_prop) submitProp(e2);
            }
            else {
            	if (type == 'checkbox') v.checked = e2.checked;
            	else v.value = e2.value;
            }
		});

		if (save) r = self.props(e);

		if (!r) self.resize();

		return r;

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

	this.field = function(o, p) {
        // при включении поля, отключать другие по списку из data-off
        if (A(self.mode, ['option', 'option_setting']) && p == 'off') {
			var s = P(o, p).split(',');
			if (!o.checked) return;
			if (o.type == 'checkbox') { while (v = W([N(o,3), 'input'])) if (A(P(v, 'id'), s)) v.checked = false; }
			else while (v = W([N(o,4), 'input[value="2"]'])) if (A(P(v, 'id'), s) && v.checked) E([N(v,2), 'input[value="1"]'], {'checked': true});
		}
	}

	this.submit = function(value, param) {

		var mode = self.mode;

		if (save_error) return;

		if (self.param.function) {
			var f = new Function('param', 'var e = (param.e ? param.e : false); ' + self.param.function);
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
				var e = E(['input[name="window_edost_register_date"]']);
				if (e) edost.admin.control(self.param.id, 'order_date', V(e));
			}
			self.set('close_full');
			return;
		}
		if (mode == 'profile_setting') {
			self.set('profile_setting_new');
			return;
		}
		if (A(mode, ['profile_setting_new', 'profile_setting_change'])) {
			var post = [];
			W('#' + main_id + ' input, #' + main_id + ' select', function(v) {
				if (v.type == 'checkbox' && !v.checked) return;
				post.push(v.name + '=' + (v.type == 'checkbox' ? 1 : edost.trim(V(v).replace(/\=/g, ' ').replace(/\|/g, ' '), true)));
			});
			self.register_reload_onclose = self.reload = true;
			if (data_backup.mode == 'profile') {
				self.close_onset = true;
				post.push('local=1');
			}
			edost.admin.setting(mode, 'save', post.join('|'));
			return;
		}
		if (mode == 'option') {
			var post = [];
			W([E(data_id + '_div'), 'div.edost_option_service input'], function(v) { if (v.checked) post.push(P(v, 'id')); });
			self.set('close_full');
			edost.package.save(post.join(','), self.param);
			return;
		}
		if (mode == 'option_setting') {
    	    var post = [];
			W([E(main_id), 'input[type="radio"]:checked'], function(v) { post.push(v.name.replace('edost_service_', '') + ',' + v.value); });
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

		var c = false;
		if (value) {
			self.set('close_full');
			var e = E(value);
			if (e) {
				e.checked = true;
				if (e.onclick && e.onclick.toString().indexOf('location.set_city(') > 0) c = true;
			}
		}

		if (cod) E(cod, {'disabled': false, 'checked': true});

		if (c) { e.click(); return; }

		if (!value) return;

		if (value.indexOf('PAY_SYSTEM') > 0) changePaySystem();
		else submitForm();

    }

	this.set = function(mode, param, data) {

		if (mode === 'error') {
			if (save_error != param) {
				save_error = param;
				self.resize();
			}
			return;
		}

		loading = false;
		var show = (!A(mode, ['close_full', 'close', 'esc']) ? true : false);
		if (!show) edost.remove([head_ico]);

		self.copy_id = '';
		if (A(mode, ['close', 'esc']) && data_backup !== false && data_backup.mode != self.mode && !request) {
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
			if (mode && self.mode && mode != self.mode && !A(self.mode, ['paysystem', 'call_profile'])) {
				var e = E(data_id);
				data_backup = {"html": H(e), "scroll": (e ? e.scrollTop : 0), "mode": self.mode, "param": edost.clone(self.param)};
				H(e, '');
			}

			self.copy_id = mode;
			if (param !== 'old') self.param = edost.param(param, 'edost_' + mode + '_div');
			if (mode == 'option') self.copy_id += '_' + self.param.company;
			if (mode == 'package_detail') self.copy_id += '_' + self.param.id;

			window_scroll = false;
			scroll_reset = true;
			save_error = false;
		}

		if (A(mode, ['close_full']) && self.mode == 'agreement') self.agreement('unset');
		if (!show && self.register_reload_onclose && A(self.mode, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile'])) edost.admin.set_param('register', 'reload');
		if (A(mode, ['close', 'esc']) && self.mode == 'window_location') edost.location.window('back');

		if (mode == 'fast') W('div.edost_prop_error', function(v) { H(v, ''); });
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
			s += '<div class="edost_button_save" onclick="' + main_function + '.submit();"></div>';
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

		E(name_id, {'class': c + '_head', 'color': head_color, 'html': (head ? head.replace('с оплатой при получении', '<span style="display: inline-block;">с оплатой при получении</span>') : '') + (data && data.head_button ? data.head_button : '')});

		var s = ['edost_window edost_compact_window_main'];
		if (config.class) s.push(config.class); else s.push('edost_window_main ' + (self.cod ? 'edost_compact_tariff_cod_main' : 'edost_compact_tariff_main'));
		if (self.mode == 'option') for (var i = 0; i < depend.length; i++) if (!self.param['depend_' + depend[i]]) s.push('edost_option_service_depend_' + depend[i] + '_hide');
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
			H(E_data, config.confirm);
		}
		else if (A(self.mode, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile']) && self.copy_id !== false) {
			// загрузка данных извне
			resize = false;
			var v = '';
			if (self.param.type || self.mode == 'profile_setting_new') v = 0;
			if (self.param.local && self.param.change) v = data_backup.param['profile_' + self.param.type];
			else if (A(self.mode, ['profile', 'profile_setting_change'])) v = self.param.id;
			H(E_data, '');
			edost.admin.setting(self.mode, 'get', v);
		}
		else if (self.mode == 'frame') {
			// страница пункта выдачи
			H(E_data, '<iframe id="' + frame_id + '" style="display: block;" src="' + self.param.href + '" frameborder="0" width="100%"></iframe>');
		}
		else if (data && data.html) H(E_data, data.html);
		else if (copy_id) {
			// копирование данных из документа
			var e = E(copy_id);
			if (e) {
				if (!show) H(E_data, '');
				else {
					var s = '';
					s += '<div id="' + data_id +  '_width"></div>';
					s += '<div id="' + data_id +  '_div">';
					s += H(e);

					if (A(self.mode, ['register_delete', 'batch_date'])) s = s.replace(/edost_register_date/g, 'window_edost_register_date').replace('%no_api_checkbox%', edost.admin.no_api_checkbox);
					if (A(self.mode, ['fast', 'agreement', 'agreement_fast'])) s = s.replace(/submitForm\('Y'\)/g, 'window.' + main_function + '.submit(this)');
					if (self.mode == 'fast') s = s.replace(/edost_agreement_2/g, 'window_edost_agreement_2'); //.replace(/\'agreement\'/g, '\'agreement_fast');
					else if (A(self.mode, ['agreement', 'agreement_fast'])) s = s.replace(/id=\"edost_agreement_text/g, 'id="window_edost_agreement_text'); // '
					else s = s.replace(/submitForm/g, 'return; submitForm').replace(/changePaySystem/g, 'return; changePaySystem').replace(/\"ID_DELIVERY_/g, '"window_ID_DELIVERY_').replace(/\"ID_PAY_SYSTEM_ID_/g, '"window_ID_PAY_SYSTEM_ID_'); //"

					s += '</div>';
					s += '<div id="' + data_id +  '_buffer"></div>';
					H(E_data, s);
				}
			}
		}

		if (show) {
			if (A(self.mode, ['register_delete', 'batch_date'])) {
//				var e = E('window_edost_register_date');
				var e = document.querySelector('input[name="window_edost_register_date"]');
				if (e && e.value == self.param.batch_date) e.value = P(e, 'date');
			}
			if (self.mode == 'profile' && resize) self.resize('set_profile', E_data);
			if (self.mode == 'option') W([E_main, '.edost_option_service input'], function(v) {
				v.checked = (A(P(v, 'id'), self.param.service) ? true : false);
				var p = P(v, 'tariff'); if (p) D(N(v,2), !A(self.param.tariff, p.split(',')) ? 0 : '');
				var p = P(v, 'postamat'); if (p) D(N(v,2), p == 'N' && self.param.postamat || p == 'Y' && !self.param.postamat ? 0 : '');
			});

			if (self.mode == 'fast') self.copy(E_data);
		}

		if (!show) return;

		var e = E(main_id);
		if (!e || e.style.display == 'none') return;

		if (resize) self.resize();
		edost.mask('start');
		edost.hint('start');

//		self.fit();
//		if (self.timer_resize != undefined) window.clearInterval(self.timer_resize);
//		self.timer_resize = window.setInterval(main_function + '.fit("resize")', 400);

	}


	// установка размера окна
	this.resize = function(param, value) {

        var config = self.config();

		if (param == 'loading') loading = true;
		if (param == 'redraw') data_width = 0;

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

		if (A(self.mode, ['register_delete', 'batch_date']) && param == 'error') self.param.date_error = value;

		if (A(self.mode, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile', 'call']) && param == 'set') {
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
			if (E_data) {
				H(E_data, value);
				edost.hint('start');
			}

			if (self.mode == 'profile') self.resize('set_profile', E_data);
			if (self.mode.indexOf('profile_setting') == 0 && E_data) {
				// генерация списка городов для выбора
				edost.sort(locations, 3);
	    	    var ar = E_data.querySelectorAll('input.edost_field_city');
				if (ar) for (var i = 0; i < ar.length; i++) {
					var s = '';
					s += '<option value=""' + (!ar[i].value ? ' selected' : '') + '>не задан</option>';
					for (var i2 = 0; i2 < locations.length; i2++) if (locations[i2][1] == 0 && locations[i2][4] == 3) {
						var p = locations[i2];
						s += '<option value="' + p[0] + '"' + (p[0] == ar[i].value ? ' selected' : '') + '>' + p[3] + (!A(p[3], edost.fed_city) ? ' (' + regions[0][p[2]] + ')' : '') + '</option>';
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
				C(e, ['edost_field_new'], self.mode != 'profile_setting_new');

				var s = E([e, 'select[name="type"]']);
				var id = V(s), path = P(s, 'path'), img = E([head_ico]);
				if (img && (!id || id == 'shop' || img.src.indexOf('/' + id + '.gif') == -1)) { edost.remove(img); img = false; }
				if (!img && id && id != 'shop') E('edost_window_head').insertAdjacentHTML('beforebegin', '<img class="edost_ico_window" src="' + path + id + '.gif" border="0">')

				W([e, 'div.edost_field, div.edost_field_delimiter, div.edost_field_name span, .edost_hint, div.edost_field option'], function(v) {
					var p = P(v, 'type');
					if (p) p = p.split(',');
					var a = (!p || A(id, p) || id != 'shop' && A('company', p));
					if (a && C(v) == 'edost_hint') a = 'inline';
					D(v, a);
				});
				W([e, 'div.edost_field select'], function(v) {
					if (D(v.options[v.selectedIndex]) == 'none') W(v.options.length, function(i) { if (D(v.options[i]) != 'none') { v.selectedIndex = i; return false; } });
				});

				var c = (c != 1 || V([e, 'select[name="mode"]']) == 'N' ? 0 : 1);
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
		var E_form_head = E([E_data, '.edost_window_form_head']);
		var E_data_head = E(data_id + '_head');
		var E_data_data = E(data_id + '_data');

		D(E_save, self.save ? true : false);
		C(e, ['', 'edost_button_save_main', 'edost_button_new_main', 'edost_button_close_main', 'edost_button_yes_main'], config.save ? config.save : 0);

		if (self.save) {
			E_save.style.width = '';
			E_save.style.marginLeft = '';
			C([e, '.edost_button_save'], ['edost_button_error'], !save_error);
			H([e, '.edost_button_save'], self.param.button_ok ? 'принять' : 'сохранить');
		}

		if (A(self.mode, ['register_delete', 'batch_date'])) C(e, ['', 'edost_button_date_error'], self.param.date_error ? 1 : 0);
		if (self.mode == 'batch_date') C(e, ['', 'edost_call_warning_main'], self.param.call ? 1 : 0);

		C(e, ['', 'edost_error'], self.mode == 'error' ? 1 : 0);

		e.style.opacity = (loading && config.loading ? '0.01' : 1);

		var agreement = (self.mode == 'agreement' || self.mode == 'agreement_fast' ? true : false);
		if (agreement) {
			landscape_head_width = 140;
			var E_agreement = E('window_edost_agreement_text');
			if (!E_agreement) agreement = false;
		}


		if (self.mode == 'window_location') {
			var x = 0;
			var c = E('edost_city');
			var h = E('edost_city_hint');
			if (c && h) {
				var rect = c.getBoundingClientRect();
				x = (h.offsetWidth > rect.width ? 0 : Math.round(rect.left + 5));
				h.style.left = x + 'px';
			}

			var a = (D('edost_city_suggest_div') != 'none' ? false : true);

			D('edost_city_hint', a);
		}


		if (E_buffer) E_buffer.style.height = 0;

		var jump = (edost.mobile_jump !== false && fullscreen ? edost.mobile_jump + 500 : 0);

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

			if (agreement) E_agreement.style.height = (window_h - 100 - E(E_form_head, 'offsetHeight')) + 'px';

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
//			if (E_data_head && E_data_data) E_data_data.style.height = 'auto';
			E_data.style.marginLeft = E_data.style.marginTop = 0;

			if (agreement) {
				var h = window_h - 180;
				if (device <= 1 && h > 350) h = 350;
				E_agreement.style.height = (h - E(E_form_head, 'offsetHeight')) + 'px';
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
				if (mobile && edost.mobile_jump === false && self.mode == 'fast' && h_data + 40 > h) {
					h2 = h_data + 40 - h;
					if (h2 > 40) h2 = 40;
				}

				if (h2 != 0) E_buffer.style.height = h2 + 'px';
				E_data.style.height = h + jump + 'px';
				if (E_data_head && E_data_data) E_data_data.style.height = (h + jump - E_data_head.offsetHeight - (mobile ? 25 : 50)) + 'px';
			}
			else {
				var h = h_head + (!mobile ? 15 : 5) + h_save;
				if (E_frame) h = 0;
				window_h = (config.height ? config.height : h_data)*1 + h*1;
				var a = false;
				if (window_h + 100 > browser_h) { a = true; window_h = browser_h - 100; }
//				else if (self.save && self.param.height) a = true;
				else if (self.param.height) a = true;
				if (a) E_data.style.height = (window_h - h) + 'px';
				if (E_data_head && E_data_data) E_data_data.style.height = (E_data.offsetHeight - E_data_head.offsetHeight - (mobile ? 25 : 50)) + 'px';
			}
		}


		if (self.mode == 'window_location') {
			var s = ['edost_country_list_div', 'edost_city', 'edost_city_hint'], h = 0;
			W(s, function(v) { h += E(['#edost_window #' + v], 'offsetHeight'); });
			h = window_h - h - E_head.offsetHeight - 80;

			// перенос стран в select на телефонах
			var c = E(['#edost_country_list_div.edost_L2_phone .edost_L2_select']);
			if (c && c.selectedIndex >= 0) {
				var u = c.options[c.selectedIndex];
				if (E(['.edost_device_phone'])) { if (C(u, 'edost_L2_select_other', 'in')) c.selectedIndex = (self.param.selectedIndex ? self.param.selectedIndex : 1); }
				else if (C(u, 'edost_L2_select_hide', 'in')) { self.param.selectedIndex = c.selectedIndex; u.selected = false; }
			}

			var c = E(['#edost_window .edost_L2_suggest_div']);
			if (c) {
				c.style.height = h + 'px';
				c.style.overflow = 'auto';
			}

			// сдвиг прокрутки по активной записи
			var u = E(['#edost_window .edost_L2_suggest_div']);
			var u2 = E(['#edost_window .edost_L2_suggest_active']);
			if (u && u2) {
				var p = u.getBoundingClientRect();
				var p2 = u2.getBoundingClientRect();
				if (p2.bottom > p.bottom) u.scrollTop += p2.bottom - p.bottom + 8;
				else if (p2.top < p.top) u.scrollTop -= p.top - p2.top + 8;
			}
		}


		e.style.borderRadius = (fullscreen ? 0 : '8px');
		e.style.width = window_w + 'px';
		e.style.height = window_h + jump + 'px';

		if (self.param.position && !fullscreen) {
			var x = Math.round(self.param.position[0] - window_w*0.5);
			var y = Math.round(self.param.position[1] - (self.param.position_center ? window_h*0.5 : window_h - 80));
		}
		else {
			var x = Math.round((browser_w - window_w)*0.5);
			var y = Math.round((browser_h - window_h)*0.5);

		}

		if (!fullscreen && edost.mobile_jump === false) {
			if (y < 15) y = 15;
			if (y*1 + window_h*1 > browser_height - 15) y = browser_height - 15 - window_h;
			if (x < 15) x = 15;
			if (x*1 + window_w*1 > browser_width - 40) x = browser_width - 40 - window_w;
		}

		if (edost.mobile_jump !== false) {
			e.style.position = 'absolute';
			var h = - (!fullscreen ? 70 : edost.mobile_jump) + (!landscape ? 20 : 0);
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
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C, A = edost.A, I = edost.I, P = edost.P, W = edost.W, N = edost.N
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

//		window.matchMedia('(pointer:coarse)').matches

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

		if (A(param, ['start', 'timer'])) button_window_scroll_Y = -1;
		if (param == 'timer') {
			if (button_timer != undefined) window.clearInterval(button_timer);
			var button_timer = window.setInterval("edost.resize.bar()", 40);
			return;
		}

		var s = self.get_scroll('y');
		var w = edost.browser('w');
		var h = edost.browser('h');

		if (!A(param, ['loading', 'save', 'start', 'update']) && button_window_scroll_Y == s && button_window_width == w && button_window_height == h) return;

		button_window_scroll_Y = s;
		button_window_width = w;
		button_window_height = h;

		var E_data = E('edost_data_div');
		var E_bar = E('edost_bar');
		if (!E_data || !E_bar) return;

		var rect = E_data.getBoundingClientRect();

		if (param === 'loading') {
			// загрузка нового контента
			H(E_data, edost.loading);
			if (rect.top < 0) window.scrollBy(0, rect.top - 80);
			return;
		}
		if (A(param, ['save', 'start'])) {
			// сохранение с блокировкой блока
			var E_fon = E(loading_id + '_fon');
			if (!E_fon && param == 'save') {
				E_data.insertAdjacentHTML('beforebegin', '<div id="' + loading_id + '_fon" style="position: absolute; background: #FFF; opacity: 0.6; z-index: 4;"><div id="' + loading_id + '">' + edost.loading + '</div></div>');
	            E_fon = E(loading_id + '_fon');
			}
            if (E_fon) {
				D(E_fon, param == 'save');
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

		var a = (rect.bottom > h ? true : false);
		C(E_bar, ['edost_bar_up'], a ? 0 : 1);
		E(E_bar, {'width': a ? (rect.width-2) + 'px' : '', 'left': a ? (rect.left+1) + 'px' : ''});

	}

	this.start = function(param) {
		var add = false;
		if (param === 'add') {
			if (self.init) return;
			add = true;
		}

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
		if (!add)
			if (self.param.data) s = self.param.data;
			else {
				var e = E('edost_template_data');
				if (!e) return;
				s = e.value;
			}
		data = [];
		if (s != '') {
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
		}

        if (!self.init) {
			self.init = true;
			var e = E('edost_template_2019');
			if (e) {
				edost.template_2019 = true;
				edost.template_ico = P(e, 'ico');
				edost.template_priority = P(e, 'priority');
				edost.template_compact = P(e, 'compact');
				edost.template_no_insurance = P(e, 'no_insurance');
				edost.template_preload_prop = P(e, 'preload_prop');

				var s = P(e, 'ico_path');
				edost.ico_path = (s ? s : '') + (s && s.substr(-1) != '/' ? '/' : '');

				var s = edost.json(P(e, 'tel'));
				if (s && s.country && (s.country.length > 1 || s.country[0] != 'M')) { edost.tel.country = s.country; edost.tel.format = edost.tel.format_data[s.format_id]; edost.tel.manual = A('M', s.country); }

				var s = P(e, 'window_scroll_disable');
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

			if (edost.template_2019 || self.param.set_event || add) {
				window.addEventListener('resize', edost.resize.update);
				window.addEventListener('scroll', edost.resize.update);
//				window.addEventListener('mousewheel', edost.resize.update);
				if (edost.mobile) window.addEventListener('orientationchange', edost.resize.update);
			}
		}

		drawing = false;
		if (edost.template_2019 || self.param.set_event || add) {
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
//		console.log('edost.resize.update: ' + id + ' | ' + width);

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
							e2.style.width = (w2 + 20) + 'px';
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
						D(['.edost_order_inside'], c == 2);
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

				var E_main = N(E_left);
				var E_menu = (p.menu ? E(p.menu.id) : false);

				var m = (E_menu && (self.browser_width - p.left.width > 0) ? p.menu.width : 0), m2 = 0;

				var w = p.left.width + p.right.width;
				var w2 = self.browser_width - w;
				var s;
				if (w2 > 200 && !E_menu) s = [p.left.width, p.right.width + 'px', w + 'px'];
				else if (w2 > -100) s = [(self.browser_width - p.right.width), p.right.width + 'px', 'none'];
				else s = [0, 0, 'none'];

				if (!s[0]) s[0] = (m ? '100%' : 'calc(100% - ' + m + 'px)');
				else {
					if (m) {
						m2 = s[0] - p.left.width - m;
						if (m2 < 0) m2 = 0;
						if (m2 > m) m = m2 = Math.round((m + m2)/2);
					}
					s[0] = (s[0] - m - m2) + 'px';
				}

				E_left.style.width = s[0];
				E_left.style.marginLeft = m + 'px';
				E_left.style.marginRight = m2 + 'px';
				E_right.style.display = (s[1] ? 'block' : 'none');
				if (s[1]) E_right.style.width = s[1];
				E_main.style.maxWidth = s[2];
				if (E_menu && p.header) E_menu.style.top = (m ? E(p.header.id, 'offsetHeight') : 0) + 'px';

				var e = document.body;
				C(e, ['right_show', 'right_hide'], s[1] ? 0 : 1);
				if (E_menu) C(e, ['menu_inside'], m ? 1 : 0);

				if (p.header) E(p.header.id_div, {'maxWidth': w2 > 200 ? w + 'px' : ''});
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
					if (!E('edost_catalogdelivery_load')) self.start();
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
//console.log('================= ' + w + ' | ' +  id + ' | ' + resize);
//console.log(data);
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

				var c = '', active = 0;
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
				if (A(v.type, ['class', 'ico', 'ico_row'])) var ar = document.getElementsByClassName(v.name);

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

		// закладки тарифов
		var e = E('edost_bookmark_main');
		if (e) {
			C('edost_delivery_div', ['edost_bookmark_normal', 'edost_bookmark_small'], 0);
			var w = e.offsetWidth, w2 = 0;
			W([e, '.edost_bookmark_button'], function(v) { w2 += v.offsetWidth; });
			C('edost_delivery_div', ['edost_bookmark_normal', 'edost_bookmark_small'], w2 > w - 50 ? 1 : 0);
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
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C, A = edost.A, I = edost.I, P = edost.P, W = edost.W, N = edost.N
	var main_function = 'edost.location'
	var fed_active = -1, region_active = -1, city_full = false, city_filter = false, gps_active = false, loading = false
	var loading_data_last = 0, disable = false, ajax_id = 0, browser_width = 0, browser_height = 0, city_width = 0, onkeydown_backup = 'free', ru_active = false, zip_full = '', city2 = ''
	var city_street = ['Москва','Саратов','Ростов-на-Дону','Казань','Волгоград','Астрахань','Новосибирск','Краснодар','Санкт-Петербург','Омск','Пермь','Уфа','Челябинск','Воронеж','Грозный','Пенза','Екатеринбург','Улан-Удэ','Нижний Новгород','Хабаровск','Севастополь','Тюмень','Шахты','Симферополь','Рыбинск','Кемерово','Иваново','Прокопьевск','Оренбург','Красноярск','Ярославль','Ижевск','Калининград','Выборг','Курск','Ульяновск','Самара','Иркутск','Чита','Орск','Томск','Липецк','Магнитогорск','Хасавюрт','Махачкала','Тверь','Владивосток','Барнаул','Брянск','Киселевск','Старый Оскол','Сызрань','Сочи','Якутск','Тула','Ставрополь','Керчь','Копейск','Смоленск','Элиста','Белгород','Орел','Ленинск-Кузнецкий','Южно-Сахалинск','Новороссийск','Гудермес','Нижний Тагил','Курган','Калуга','Новошахтинск','Рязань','Таганрог','Кострома','Комсомольск-на-Амуре','Тамбов','Псков','Анжеро-Судженск','Домодедово','Чебоксары','Владимир','Артем','Архангельск','Миасс','Киров']

	this.zip_value = ''
	this.window_width = 800
	this.window_height = 600
	this.loading_data = 0
	this.error = false
	this.device = ''
	this.load = {
		metro: {
			type: "metro",
			ignore: ['м', 'мет', 'метро', 'ст', 'стан', 'станция', 'см', 'мс'],
			value: false,
		},
		zip: {
			type: "zip",
			value: false,
			index: -1,
			loading: false,
			data: [],
			cache_id: -1,
			cache: []
		},
		city: {
			type: "city",
			preload: true,
			ignore: ['г','п','д','пос','район','обл','город','область','им','имени','н','а','в','а/я','аал','аул','въезд','высел','гп','днп','дп','жилзона','жилрайон','микрорайон','жт','заезд','кольцо','кп','н/п','нп','обл','округ','п/о','п/р','п/ст','пгт','пл-ка','полустанок','промзона','проток','р-н','рзд','рп','с','с/а','с/мо','с/о','с/п','с/с','сквер','сл','снт','спуск','ст','ст-ца','станица','стр','тер','у','уч-к','ф/х','х','ш'],
			ignore_inside: ['тов', 'ово', 'род', 'ики', 'ург', 'яя'],
			short: [['мск', 'Москва', 77], ['спб', 'Санкт-Петербург', 78], ['питер', 'Санкт-Петербург', 78]],
			copy: {'85': [['астана', 'нур-султан']]},
			hint: 'а если название указано верно, но искомого все еще нет, тогда подпишите название вашего района или региона',
			warning: '<div>совпадений не найдено</div><div>пожалуйста, проверьте написание насленного пункта в строке поиска... <span style="display: inline-block;">и убедитесь, что начинаете ввод именно с названия населенного пункта, а не со страны, региона, района, улицы, дома и т.п.</span></div>',
			warning2: '<div>совпадений не найдено</div><div>пожалуйста, проверьте написание насленного пункта в строке поиска... %region%</div>',
			value: false,
			index: -1,
			loading: false,
			data: []
		},
		street: {
			type: "street",
			ignore: ['б-р','бул','пр-д','бульвар','пр-кт','просп','проспект','мкр','мк-рн','микрорайон','ул','улица','туп','тупик','наб','набережная','ал','аллея','пер','переулок','линия','пр','пр-зд','проезд','проулок','просека','автодорога','пл','площадь','массив','кв-л','квартал','тракт','ряды','ш'],
			warning: 'не найдено ни одной улицы, проспекта, переулка и т.д. с таким названием, пожалуйста, проверьте написание... <span style="display: inline-block;">если указываете сокращенный вариант названия, тогда попробуйте указать полный (и наоборот)</span>',
			value: false,
			index: -1,
			loading: false,
			data: []
		},
		list: {
			type: "list",
			value: false,
			index: -1,
			loading: false,
			data: []
		}
	}

	this.fed = function(e) {

		if (P(e, 'type') == 'region') {
			region_active = e.value;
			if (region_active != -1) fed_active = -1;
			if (city_filter !== 'fed' && (!city_full || city_filter)) city_filter = 'update';
		}
		else {
			if (city_filter && fed_active >= 0 && e.value == -1) city_filter = 'update';
			fed_active = e.value;
			region_active = -1;
			if (!city_full || city_filter) city_filter = 'update';
		}

		C(['.edost_L2_filter select[data-type="fed"]'], ['edost_L2_filter_active'], fed_active >= 0 ? 0 : 1);
		C(['.edost_L2_filter select[data-type="region"]'], ['edost_L2_filter_active'], region_active >= 0 ? 0 : 1);

		self.suggest('city', 'redraw');

	}

	this.header_set = function(id, p) {

		if (id === 'click') {
			var e = E('edost_location_header');
			if (e) e.click();
			return;
		}

		if (p) {
			self.window();
			self.hint();
			edost.resize.start('add');
			edost.backup('location_header', true);
			return;
		}

		if (E('edost_location_city_div')) {
			self.set(id);
			return;
		}

		edost.backup('location_header', false, edost.loading20);

		var param = 'type=html&header=Y&id=' + id + '&template=' + V('edost_location_template');
		edost.post('location', param, function(r) {
			var e = E('edost_location_header_load');
			if (e) H(e, r);
			else {
				var e = E('edost_location_header_div');
				if (e) edost.create('WINDOW', 'edost_location_header_load', {'html': r, 'E': e.parentNode});
			}
			self.header_set(id, true);
		});

	}

	this.header_insert = function() {

		var v = V('edost_location_header_insert');
		var e = E('edost_location_header');
		var e2 = E('edost_location_header_city');

		if (!e) return;

		if (v) {
			if (e && e2) {
				v = v.split('|');
				e.click = v[1];
				H(e2, v[0]);
			}
			return;
		}

		window.setTimeout(main_function + '.header_insert()', 100);

	}

	this.hint = function(id) {

		if (!id) {
			id = V('edost_country');
			if (id === false) return;
			id = id.split('_')[0];
		}

		var s = '';
		if (id == 0) {
			s = 'введите название вашего населенного пункта <div>например, <b>Москва</b> или <b>Омский, Омский район</b></div>';
			if (edost.https) {
				s += '<div class="edost_delimiter" style="margin-top: 25px;"></div>';
				s += '<div class="edost_L2_gps" onclick="edost.location.gps()">найти местоположение<br>по GPS координатам</div>';
				s += '<div class="edost_L2_gps_hint">чтобы сработал поиск по GPS, после нажатия кнопки, потребуется разрешить передачу своего местоположения (появится системное сообщение браузера)</div>';
			}
		}
		else {
			var u = {21: '<b>Минск</b>', 85: '<b>Нур-Султан</b>', 14: '<b>Ереван</b>', 108: '<b>Бишкек</b>', 212: '<b>Киев</b>'};
			s = 'введите название города' + (u[id] ? '<div>например, ' + u[id] + '</div>' : '');
		}

		H('edost_city_hint', s);

	}

	this.gps = function() {

		navigator.geolocation.getCurrentPosition(function(position) {
			var p = [position.coords.longitude, position.coords.latitude];
/*
			p = [60.581457, 56.824075]; // Екатеринбург
			p = [37.389318, 55.671173]; // Москва
*/
			W(p, function(v,i) { p[i] = v.toString().substr(0, 10); });

			H(['.edost_L2_city_hint'], '<div style="padding-top: 40px;">' + edost.loading + '</div>');
			s = 'type=gps&value=' + p.join(',');
			edost.post('location', s, new Function('r', main_function + '.ajax("city", "' + ajax_id + '", "gps", "' + s + '", r)'));
		}, function(error) {
			var s = '';
			if (error.code == error.PERMISSION_DENIED) s = "Нет доступа к местоположению";
			else if (error.code == error.POSITION_UNAVAILABLE) s = "Информация о местоположении недоступна";
			else if (error.code == error.TIMEOUT) s = "Не удалось определить местоположение пользователя";
			else s = "Неизвестная ошибка";
			if (s != '') alert('Ошибка: ' + s + '!' + (error.message ? "\n" + error.message : ''));
		});

	}

	this.city_focus = function(a) {
		if (!a && edost.mobile) return;
		var e = E('edost_city');
		if (e && e.type != 'hidden') window.setTimeout(function() { e.focus(); }, edost.mobile ? 200 : 1);
	}

	this.country = function(e) {

		var a = (e.tagName == 'SELECT');
		if (a) edost.window.param.selectedIndex = e.selectedIndex;
		var v = (a ? V(e) : P(e, 'id'));
		if (v == 0) { a = false; v = P(['#edost_country_list_div .edost_L2_country_active'], 'id'); }
		v = v.split('_');
		var id_edost = v[0];
		var set = (v[2] != undefined && v[2] == 'set' ? v[1] : false);
		v = v[0] + '_' + v[1];

		self.value(['', '', v, '', '']);

		if (set) {
			D('edost_city_div', false);
			self.set_id(set)
			return;
		}

		var s = E(['#edost_country_list_div .edost_L2_select']);
		W('#edost_country_list_div .edost_L2_country_active', function(u) {
			var c = P(u, 'id');
			if (c == v && s) V(s, 0);
			C(u, 'edost_L2_country_active' + (c == v ? '' : ' edost_L2_country_active_off'));
		});

		self.hint(id_edost);
		self.city_focus(true);

	}

	this.zip = function(value, full, submit) {

		var c = V('edost_country');
		if (!c) return;

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

		c = c.split('_');
		self.zip_full(value != '' && full || c[1] != undefined && c[0] != 0 ? 'Y' : '');

		var E_zip = E('edost_zip');
		if (E_zip && !original && (full || reset)) V(E_zip, value);

		var e = E('edost_shop_ZIP');
		if (!e || !reset && (e.value == value || e.value != '' && e.value != '.' && value == '')) return;
		V(e, value);

		if (!submit) return;

		if (E_zip && E_zip.type != 'hidden') self.zip_warning('submit');
		else self.loading('edost_location_address_loading', 'submit');

		self.disable();
		self.cookie();
		edost.submit();

		return true;

	}

	this.zip_warning = function(s) {

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

			if (s != '') s = '<div class="edost_L2_warning">' + s + '</div>';
			H(id, s);
		}

	}

	this.city2 = function(v) {
		if (v == undefined) {
			var e = E('edost_city2');
			return (e === false ? city2 : V(e));
		}
		V('edost_city2', v);
		city2 = v;
	}

	this.zip_full = function(v) {
		if (v == undefined) {
			var e = E('edost_zip_full');
			return (e === false ? zip_full : V(e));
		}
		V('edost_zip_full', v);
		zip_full = v
	}

	this.loading = function(id, s, update) {

		if (id == 'header')	{
			H('edost_location_header_city', '...');
			return;
		}

		if (s == 'submit') s = 'расчет доставки...';

		if (window.edost_RunScript) {
			if (update || update === undefined) {
				if (window.edost_catalogdelivery) edost_catalogdelivery.calculate('loading_location'); else edost.catalogdelivery.calculate('loading_location');

				var e = E('edost_catalogdelivery_inside_city_head');
				if (e) {
					D(e, false);
					H('edost_catalogdelivery_inside', window.edost_catalogdelivery ? '<div style="text-align: center;"><img style="vertical-align: top;" src="/bitrix/components/edost/catalogdelivery/images/loading.gif" width="64" height="64" border="0"></div>' : edost.loading64);
				}

				H('edost_catalogdelivery_inside_city', '');
				D('edost_catalogdelivery_inside_detailed', false);
			}
		}
		else {
			var c = {'html': '<div class="edost_loading edost_loading_16">' + edost.loading_svg + '<span style="padding-left: 4px;">' + s + '</span></div>'};
			if (id != 'edost_location_zip_hint') c.display = (s == '' ? false : true);
			E(id, c);
		}

	}

	this.cookie = function(i, s, f, c) {

		if (i == undefined) {
			var e = E('edost_shop_LOCATION');
			if (!e) return;
			i = V(e);
		}
		if (s == undefined) s = V('edost_shop_ZIP');
		if (f == undefined) f = (self.zip_full() === 'Y');
		if (c == undefined) c = self.city2();

//		edost.cookie('edost_location', i + '|' + (s ? encodeURIComponent(s) : '') + (s && !f ? '.' : '') + '|' + (c ? encodeURIComponent(c) : ''));
		edost.cookie('edost_location', i + '|' + s + (s && !f ? '.' : '') + '|' + c);

	}

	this.set_id = function(id, header, zip) {

		self.loading('edost_location_city_loading', 'submit');

		if (zip === 'get_zip') {
			edost.post('location', 'type=html&mode=get_zip&id=' + id, function(r) {
				self.zip_full('');
				V('edost_shop_ZIP', r);
				self.set_id(id, header, r);
			});
			return;
		}

		var e = E('edost_shop_LOCATION');
		if (!e) return;

		V(e, id);
		if (header === 'set') self.city2('');
		self.disable();
		if (zip) self.cookie(id, zip); else self.cookie(id);
		self.window('close');

		if ((header || header === undefined) && E('edost_location_header_div')) {
			self.loading('header');
			edost.post('location', 'type=html&header=Y&current=Y', function(r) { H('edost_location_header_div', r); });
		}

		edost.submit();

	}

	this.set_city = function(city, mode, code) {
		var c = city.split(';');
		var a = (mode == 'post' ? true : false);
		self.set('', true, c[0], c[1] ? c[1] : V('edost_region'), c[2] !== undefined && c[2] !== '' ? c[2] : V('edost_country').split('_')[0], a ? false : true, c[0], a ? code : false);
		if (window.loadingForm) loadingForm();
	}

	this.set = function(id, edost_delivery, city, region_id, country_id, get_zip, value_string, zip) {

		if (window.edost_SetLocation) { edost_SetLocation(id, true, city, region_id, country_id, get_zip, value_string, zip); return; } // поддержка старых функций

		edost_delivery = true; // !!!!!
		self.zip_value = zip;

//		<? if (!$arResult['get_zip']) echo 'get_zip = false;'; ?> // !!!!!

		self.window(country_id != undefined ? 'close' : 'loading');

		var header = (E('edost_location_header_div') ? true : false);

		if (!id) id = '';
		id = id.split('|');
		var select = (id[1] != undefined ? id[1] : false);
		id = id[0].split('_');
		var set = (id[1] != undefined && id[1] == 'set' ? true : false);
		id = id[0];

		if (select !== false) for (var i = select*1+1; i <= 5; i++) D('edost_location_' + i + '_select', false);

		if (set || edost_delivery && country_id != undefined) {
			self.disable();
			H('edost_city_hint', '');
			var e = E('edost_country');
			if (e && e.tagName == 'SELECT') e.disabled = true;
		}

		if (value_string && !header) {
			var e2 = E('edost_city_div');
			var e3 = E('edost_city');
			var e4 = E('edost_location_city_div');
			if (e2 && e3 && e4) {
				D(e2, false);
				D(e4, false);
//				e2.insertAdjacentHTML('beforebegin', '<input class="' + e3.className + '" value="' + value_string + '" readonly type="text">');
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
			if (e) for (var i = 0; i < e.children.length; i++) if (C(e.children[i]) != 'edost_L2_city_name') { e.removeChild(e.children[i]); i--; }
		}

		if (country_id != undefined) self.loading('header');

		var param = 'type=html&id=' + id + '&edost_delivery=' + (edost_delivery ? 'Y' : 'N') + '&template=' + V('edost_location_template');
		if (country_id != undefined) param += '&country=' + encodeURIComponent(country_id) + '&' + 'region=' + encodeURIComponent(region_id) + '&city=' + encodeURIComponent(city) + (get_zip === false ? '&get_zip=N' : '');
		edost.post('location', param, function(r) {
			self.loading('edost_location_city_loading', '', country_id != undefined ? true : false);

			if (r.indexOf('{') == 0) {
				var v = edost.json(r);
				if (v.error_string != undefined) {
					if (window.edost_SetTemplateLocation) { edost_SetTemplateLocation('error', v.error_string); return; }
					r = '<div class="edost_L2_warning">' + v.error_string + '</div>';

					if (window.edost_RunScript) {
						if (window.edost_catalogdelivery) edost_catalogdelivery.calculate('error', v.error_string); else edost.catalogdelivery.calculate('error', v.error_string);
						H('edost_catalogdelivery_inside', '<div style="text-align: center; color: #F00;">' + v.error_string + '</div>');
					}
				}
				else {
					if (self.zip_value) { v.zip = self.zip_value; v.zip_full = true; }
					self.city2(v.city2 != undefined ? city : '');
					if (edost_delivery && v.zip) self.zip(v.zip, v.zip_full != undefined ? true : false);

					var e = E('edost_location_header_div');
					var header_update = (e && v.header ? true : false);
					if (header_update) e.innerHTML = v.header;

					self.set_id(v.id, !header_update);

					return;
				}
			}

			var e = E('edost_location_city_div');
			if (!e) e = E('edost_catalogdelivery_window_city');
			if (e)
				if (!header) {
					H(e, r);
					D(e, true);
					self.city_focus();
				}
				else {
					var e2 = E('edost_location_header_data');
					if (e2) H(e2, r); else edost.create('WINDOW', 'edost_location_header_data', {'html': r, 'E': e});
				}

			if (country_id == undefined) {
				self.window();
				self.hint();
			}
		});

	}

	// отключение скрипта
	this.disable = function() {

		if (window.edost_location && edost_location.disable) { edost_location.disable(); return; } // поддержка старых функций

		ajax_id++;
		disable = true;
		loading = false;
		for (k in self.load) {
			var load = self.load[k];
			load.suggest_now = -1;
			load.loading = false;
			if (load.suggest_timer != undefined) window.clearInterval(load.suggest_timer);
			D('edost_' + k + '_suggest_div', false);
		}

	}

	// поиск индекса загруженного списка
	this.index = function(name, s, write) {

		var load = self.load[name];

		if (write) {
			var i = load.data.length;
			load.data.push(0);
			return i;
		}

		var fed = fed_active;
		var region = region_active;

		if (fed == -1 && region != -1) W(self.data.region_fed, function(v,i) { if (A(region, v.region)) { fed = i; return false; } });

		var r = [-1, -1, -1];
		var n = s.length;
		for (var i2 = n; i2 >= 0; i2--) {
			var v = s.substr(0, i2);
			W(load.data, function(u,i) {
				if (!u || u.name == undefined) return;
				if (u.name == v && (name != 'city'
					|| u.fed == -1 && u.region == -1 && u.full
					|| fed == -1 && u.fed == -1 && region == -1 && u.region == -1 && (u.full || u.overlimit)
					|| u.fed != -1 && u.fed == fed && u.region == -1
					|| u.fed == -1 && u.region != -1 && u.region == region) && (i2 == n || u.full)) {
					if ((!load.location || self.compare(u.location, load.location)) && (name != 'zip' || u.location[3] == load.location[3])) {
						var k = 2;
						if (region != -1 && u.region != -1) k = 0;
						else if (fed != -1 && u.fed != -1) k = 1;
						r[k] = i;
					}
				}
			});
		}
		for (var i = 0; i < 3; i++) if (r[i] != -1) return r[i];
		return -1;

	}

	// сравнение населенных пунктов
	this.compare = function(s1, s2) {
		return (s1[0] == s2[0] && s1[1] == s2[1] && s1[2] == s2[2] ? true : false);
	}

	// распаковка элементов массива
	this.parse_array = function(s) {
		r = [];
		W(s, function(v) { r.push( v.split('|') ); });
		return r;
	}

	// установка метки 'город с районами'
	this.set_area = function(value, load) {
		V('edost_area', value ? 'Y' : '');
		if (load) self.load['street'].area = value;
	}

	// установка значения
	this.set_value = function(id, value) {

		var v = self.name(id);
		if (!v) return;
		var name = v.name, e = v.e;

//		var name = id.split('_')[1];
		var load = self.load[name];

		if (value == undefined && load.suggest === false) return;

		var ar = (value == undefined ? load.suggest : value.split('|'));
		var value_string = ar.shift();
		load.value = value = ar;
		load.suggest_now = -1;
		load.string = value[0];

		if (name == 'city') {
			if (value[2] == undefined) value[2] = 0;
			else if (value[2] === '') value[1] = '';

			self.value([value[0], value[1], value[2], '', '']);
			self.set(load.index != -1 && load.data[load.index].original && value[3] ? value[3] : '', true, value[0], value[1], value[2], true, value_string);
		}
		else if (name == 'street') {
			self.value(['', '', '', value[0], value[1]]);

			// внесение индекса улицы в предзагрузку
			if (value[1] != '') self.set_zip_load(value[1]);

			// улица принадлежит отдаленному району города
			if (value[2]) {
				self.zip(value[1], true);
				self.set('', true, value[2], load.location[1], load.location[2], false, value_string);
				return;
			}

			if (edost.template_preload_prop) submitProp(E('edost_street'));

			if (self.zip(value[1])) return;
		}
		else if (name == 'zip') self.zip(value[0]);
		else if (name == 'list') V(v.e, value);
		else V(id, value);

	}

	// чтение/запись текущего местоположения
	this.value = function(v) {

		var city = E('edost_city');
		var region = E('edost_region');
		var country = E('edost_country');
		var street = E('edost_street');
		var zip = E('edost_zip');

		if (v === undefined) return [city ? V(city) : '', region ? V(region) : '', V(country), street ? V(street) : '', zip ? V(zip) : ''];

		if (v[2] !== '') {
			V(city, v[0]);
			V(region, v[1]);
			if (country.tagName != 'SELECT') V(country, v[2]);
			self.load['city'].string = 'none';
			self.set_area(false, true);
		}
		else if (v[0] != '' && v[1] == '') V(city, v[0]);

		if (street) {
			V(street, v[3]);
			if (v[3]) self.set_area(false);
		}

		if (zip) {
			V(zip, v[4]);
			self.zip_warning('reset');
		}

	}

	// внесение индекса в предзагрузку
	this.set_zip_load = function(v) {

		if (v === undefined && self.zip_full() === 'Y') {
			var s = V('edost_shop_ZIP');
			if (s) v = s;
		}
		if (v !== undefined) self.load['zip'].data[0] = {'name': '', 'full': true, 'location': self.value(), 'data': [[v]]};

	}

	// обработка результата ajax запроса
	this.ajax = function(s, id, v, p, r) {

		if (disable || id != ajax_id) return;

		p = edost.param(p, 'post');

		var load = self.load[s];
		var index = self.index(s, v, true);
		var r = edost.json(r);

		if (s != 'zip' && r.error) self.error = r.error;

		if (p.type == 'check_zip') {
			var warning = (r.warning ? r.warning : '');

			load.cache_id++;
 			if (load.cache_id >= 10) load.cache_id = 0;
			load.cache[load.cache_id] = {'value': v, 'location': load.location, 'result': warning};

			if (warning != '') self.zip_warning(warning);
			else if (self.zip(v)) return;
		}
		else {
			load.data[index] = {'name': v, 'full': (r.full ? true : false), 'overlimit': (r.overlimit ? true : false), 'fed': (p.type == 'city' && p.fed != undefined ? p.fed : -1), 'region': (p.type == 'city' && p.region != undefined ? p.region : -1), 'data': self.parse_array(r.data), 'location': load.location, 'original': (r.original ? true : false)};
			if (r.region) load.data[index].region_list = self.parse_array(r.region);
			if (s == 'street') {
				self.set_area(r.area ? true : false, true);
				load.warning_disable = (v == '' && load.data[index].full && load.data[index].data.length == 0 ? true : false);
			}
			if (s == 'zip' && load.data[index].data.length == 0) self.set_zip_load();
		}

		if (p.type == 'gps') {
			gps_active = true;
			var f = {77: 'Москва', 78: 'Санкт-Петербург', 92: 'Севастополь'};
			W(load.data[index].data, function(v,i) { for (var k in f) if (v[0] == f[k]) load.data[index].data[i][1] = k; });
		}

		if (p.type == 'city') {
			if (p.fed != undefined) city_filter = 'fed';
			else if (p.region != undefined) city_filter = 'region';
			else if (city_filter) city_filter = (r.full ? false : true);
		}

		load.loading = loading = false;
		self.suggest(s, 'redraw');

	}

	// проверка страны на доступность подсказок через сервис eDost
	this.edost_suggest = function(country) {
		country = (country !== false ? country.split('_')[0] : false);
		return (country == 0);
	}

	this.name = function(v, p) {
		var r = {};
		if (typeof v === 'object') {
			if (C(v, 'edost_L2_suggest_value', 'contains')) v = E([N(v,3), 'input']);
			var s = P(v, 'suggest');
			if (s) {
				r.name = 'list';
				r.list = s;
				r.e = v;
				v = false;
			}
			else {
				r.e = v;
				v = v.id;
			}
		}
		if (v) {
			var s = v.split('_');
			if (s[1] != undefined) r.name = s[1]; else { r.name = v; v = 'edost_' + v; }
			if (!r.e) r.e = E(v);
			if (!r.e) return false;
		}
		if (p == 'div') {
			var e = false;
			if (r.e.id) e = E(r.e.id + '_suggest_div');
			if (!e) e = E([N(r.e), '.edost_L2_suggest_div']);
			return e;
		}
		return r;
	}

	// подсказки
	this.suggest = function(id, param) {

		var v = self.name(id);
		if (!v) return;
		var name = v.name, list = v.list, e = v.e;
		var load = self.load[name];
		if (!load) return;

		var value = V(e);
		var text_width = e.offsetWidth;
		if (param == 'start') {
			self.disable();
			disable = false;
			load.suggest_now = 1;
			load.suggest_set = true;
			load.string = load.string_start = value;
			fed_active = region_active = -1;
			city_filter = false;

			ru_active = true;
			if (A(name, ['street', 'zip']) && value != '') ru_active = false;
			if (A(name, ['city', 'street', 'zip'])) self.suggest(id, 'redraw');

			if (edost.mobile && id == 'edost_city') {
				var rect = e.getBoundingClientRect();
				edost.mobile_jump = rect.top - 10;
				edost.window.resize();
			}

			return;
		}

		if (disable) return;

		if (param == 'hide') {
			if (disable) return;

			ajax_id++;

			load.suggest_now = -1;
//			window.setTimeout(main_function + ".suggest('" + id + "', 'hide2')", 100);
			window.setTimeout(function() { self.suggest(id, 'hide2') }, 100);

			if (edost.mobile && id == 'edost_city') {
				edost.mobile_jump = false;
				edost.window.resize();
			}

			return;
		}

		if (param == 'hide2') {
			if (disable || load.suggest_now != -1) return;
			if (name == 'zip' && value.replace(/[^0-9]/g, '').length != 6 && self.edost_suggest(self.value()[2])) self.zip_warning('format');
//			if (id != 'edost_city') D(id + '_suggest_div', false);
//			if (id != 'edost_city') D(typeof id === 'object' ? [N(id), '.edost_L2_suggest_div'] : id + '_suggest_div', 0);
			if (id != 'edost_city') D(self.name(id, 'div'), 0);
			return;
		}

		if (self.loading_data == 2 && loading_data_last != 2) load.suggest_now = 1;
		else {
			if (value != load.string) {
				load.suggest_now = 1;
				load.suggest_set = true;
				city_filter = false;
			}
		}
		loading_data_last = self.loading_data;
		load.string = value;

		if (name == 'street' && load.area && value != load.string_start && load.string_start != '') {
			load.string_start = '';
			self.set_area(true);
		}

		var full = true, overlimit = false, redraw = false;
		var bold_count = 0;
		var preload = [0];
		var suggest = [], suggest2 = [], region_list = [];
		var value_original = edost.trim(value);
		var value_original2 = value_original.toLowerCase();
		if (value.length > 0 && ru_active && edost.key_lang == 'en') value = edost.filter(e, 'ru_normal');
		value = value.toLowerCase().replace(/[ё]/g, 'е').replace(/[^а-я0-9.,-]/g, ' '); // удаление недопустимых символов
		value = edost.trim(value, true);

		// проверка на название региона в поиске
		if (name == 'city' && self.data && self.data.region2) {
			var a = false;
			var u = value.replace(/[.]/g, ' . ').replace(/[,]/g, ' , ').split(' ');
			if (u.length >= 2) {
				var s = {'карачаево-черкесия': 'карачаево-черкесская', 'кабардино-балкария': 'кабардино-балкарская', 'хмао': 'ханты-мансийский', 'янао': 'ямало-ненецкий'};
				W(u, function(v,i) { if (s[v]) { u[i] = s[v]; a = true; } });
			}
			if (u.length >= 3) {
				W(['республика', 'край', 'ао', 'область', 'респ', 'обл'], function(v) {
					var k = A(v, u, 1);
					if (k === false) return;
					W(u, function(v2,i) {
						if (i == k) return;
						W(self.data.region2, function(r,n) { if (r.indexOf(' ' + v2) >= 0) { u[k] = ''; a = true; return false; } });
					});
				});
			}
			if (a) value = edost.trim(u.join(' ').replace(/ \./g, '.').replace(/ \,/g, ','), true);
		}

		var value_full = value;

		// удаление префиксов (ул, д, пос, ...)
		if (load.ignore && value.length > 1 && (value.split(' ').length > 1 || value.indexOf('.') > 0)) {
			value = ' ' + value.replace(/ /g, '  ').replace(/,/g, ', ').replace(/\./g, '. ') + ' ';
			W(load.ignore, function(v) { value = value.replace(new RegExp(' ' + v + '[ .]', 'g'), ' '); });
			value = edost.trim(value, true);
		}

		// разбивка фразы на слова
		var values = [], values2 = [], values_full = [];
		W(edost.trim(value.replace(/[,.]/g, ' '), true).split(' '), function(v) { values.push((values.length > 0 ? ' ' : '') + v); });
		W(edost.trim(value_full.replace(/[,.]/g, ' '), true).split(' '), function(v) { if (!edost_suggest || v.length > 1) values_full.push(v); });
		W(edost.trim(value.replace(/[,.]/g, ' '), true).split(' '), function(v) { if (!edost_suggest && v.length != 0 || v.length > 1) values2.push(v); });
		var values_length = values.length;

		var value_search = value;
		if (name == 'city') {
			var u = [];
//			var a = false;
//			W(values, function(v) { if (edost.trim(v).length > 2) a = true; });
//			W(values, function(v) { var s = edost.trim(v); if (s.length > (a ? 1 : 2)) u.push(s); });
			W(values, function(v) { var s = edost.trim(v); if (s.length > 2) u.push(s); });
			value_search = u.join(' ');
		}

		var values_trim = [];
		W(values, function(v) { values_trim.push(edost.trim(v)); });

		var location = self.value();
		if (name == 'city') { location[0] = ''; location[1] = ''; }
		load.location = location;
		if (name == 'city') country = (location[2] ? location[2].split('_')[0] : -1);

		var edost_suggest = self.edost_suggest(location[2]);
		load.start = (name == 'city' && edost_suggest ? 3 : 0); // количество символов с которого загружаются подсказки

		if (A(name, ['city', 'metro', 'street']) && self.loading_data == 0) {
			self.loading_data = 1;
			edost.js(['location_data.js']);
		}

		// подготовка базы регионов для поиска
		if (name == 'city' && self.data && !self.data.region2) {
			var s = [];
			W(self.data.region[0], function(v) {
				if (v) { v = ' ' + v.toLowerCase().replace('(', ' '); W(['республика', 'край', ' ао', 'область', '(', ')', ' - '], function(u) { v = v.replace(u, ' '); }); v = ' ' + edost.trim(v, true) }
				s.push(v)
			});
			self.data.region2 = s;

			var s = [];
			W(self.data.city, function(v) { s.push(' ' + v[0].toLowerCase() + self.data.region2[v[1]]); });
			self.data.city2 = s;
		}

		if (name == 'zip' && !edost_suggest) self.zip(value_original, 'original');

		// поиск по списку
		if (name == 'list') {
			W(edost.suggest_data[list], function(v) {
				var p = v.toLowerCase().indexOf(value_search);
				if (p == 0 && value_search.length <= 2 || p >= 0 && value_search.length > 2) suggest.push([v]);
			});
		}

		// поиск метро
		if (name == 'metro' && self.loading_data == 2) W(self.data.metro, function(v) {
			if (v[0] != location[0]) for (var i = 1; i < v.length; i++) {
				var p = v[i].toLowerCase().indexOf(value_search);
				if (p == 0 && value_search.length <= 2 || p >= 0 && value_search.length > 2) suggest.push([v[i]]);
			}
		});

		// поиск сокращений (мск, спб, ...)
		if (load.short) W(load.short, function(v) { if (value_full == v[0]) suggest.push([v[1], v[2]]); });

		// поиск городов и населенных пунктов по списку предзагрузки
		if (name == 'city' && edost_suggest && self.loading_data == 2 && self.data.city2 && value_full.length > 0) {
			// поиск городов
			var u = [];
			var n = values_full.length;
			W(self.data.city, function(v,i) {
				var k = 0, s = self.data.city2[i];
				if (n == 1) {
					if (s.indexOf(' ' + value_full) == 0) suggest.push(v);
					else if (v[0].toLowerCase().indexOf(value_full) > 0) {
						var a = true;
						if (load.ignore_inside) W(load.ignore_inside, function(v) { if (value_full == v) { a = false; return false; } });
						if (a) u.push(v);
					}
				}
				else {
					W(values_full, function(v2) {
						if (v2.length <= 1) return;
						var p = 0, s2 = s;
						while ((p = s2.indexOf(' ' + v2)) >= 0) { k++; s2 = s2.substr(p + v2.length + 1); }
					});
					if (n == k) suggest.push(v);
				}
			});
			suggest = suggest.concat(u);
			bold_count = suggest.length;

			// поиск двухбуквенных населенных пунктов
			var p1 = false, p2 = false;
			var s1 = value.substr(0, 1);
			var s2 = value.substr(1, 1);
			W(self.data.sign, function(v, i) { if (s1 == v) p1 = i; if (s2 == v) p2 = i; });
			if (p1 !== false) {
				if (p2 !== false) preload = self.data.load[p1][p2];
				else W(self.data.load[p1], function(v) { if (v.length > 1) { preload = v; return false; } });

				if (value.length <= 2 && !preload[0]) full = false;
			}
			if (p1 !== false) {
				if (value.length > 0 && (value.length <= 2 || preload[0] == 1 || values_length > 1)) for (var i = 1; i < preload.length; i++) {
					if (value.length <= 2 && values_length == 1) {
						suggest2.push(preload[i]);
						continue;
					}
					var n = 0;
					var s = preload[i][0].toLowerCase();
					var p = s.indexOf(',');
					var s2 = ' ' + self.data.region[0][ preload[i][1] ].toLowerCase();
					W(values, function(v, i2) { var p2 = s.indexOf(v); if (p2 >= 0 && (i2 > 0 || p2 < p || p == -1) || i2 > 0 && s2.indexOf(v) >= 0) n++; });
					if (n >= values_length) suggest2.push(preload[i]);
				}
			}
		}

		if (load.data != undefined) {
			// загрузка списка с сервера
			if ((preload[0] == 0 || value_search.length > 2) && (!self.error && value_search.length >= load.start || !edost_suggest) && !loading && !disable && name != 'list') {
				var a = true;
				var post = (A(name, ['street', 'zip']) ? '' : value_search);

				if ((A(name, ['city', 'metro'])) && self.loading_data != 2) a = false;
				else if (A(name, ['street', 'zip']) && !edost_suggest) a = false;
				else if (name == 'zip') {
					if (!location[0] || !location[1] || !location[3]) a = false;
					if (location[0] && location[1] && !location[3]) self.set_zip_load();
				}
				else if (name == 'street') W(city_street, function(v) {
					if (location[0] != v) return;
					if (value_search.length == 0) a = false;
					else {
						// загрузка только по первым буквам
						var n = (value_search.length <= 3 ? value_search.length : 3);
						for (var i = 1; i <= n; i++) {
							post = value_search.substr(0, i);
							var index = self.index(name, post);
							if (index < 0 || load.data[index].data && load.data[index].full) break;
						}
					}
					return false;
				});

				if (a) {
					var i = -1;
					if (!(name == 'city' && city_filter === 'update')) i = self.index(name, post);
					if (i == -1) {
						if (name == 'city' && fed_active == -1 && region_active == -1 && A(post, ['ека', 'мос','кра','крас','красн','красно','нов','новы','ново','кал','вол','ниж','нижн','вор','воро','рос','ста','бал','сам','пер','каз','бел','сер','кур','кир','чер','гор','горо','город','кор','лен','сев','зел','ива','пет','под','кол'])) {
							// отключение загрузки для слов с 'overlimit'
							i = self.index(name, post, true);
							load.data[i] = {'name': post, 'full': false, 'overlimit': true, 'fed': -1, 'region': -1, 'data': [], 'location': load.location, 'original': false};
						}
						else {
							loading = load.loading = true;
							post = post.substr(0, 40);
							var p = '';
							var s = '&country=' + encodeURIComponent(location[2]);
							if (A(name, ['street', 'zip'])) s += '&region=' + encodeURIComponent(location[1]) + '&city=' + encodeURIComponent(location[0]);
							if (name == 'zip') s += '&street=' + encodeURIComponent(location[3]);
							if (name == 'city' && (city_filter === 'update' || fed_active >= 0 || region_active >= 0)) {
								city_filter = 'loading';
								if (region_active > 0) s += '&region=' + region_active;
								else if (fed_active >= 0) s += '&fed=' + fed_active;
							}

							s = 'type=' + load.type + '&value=' + encodeURIComponent(post) + s;                                        // value
							edost.post('location', s, new Function('r', main_function + '.ajax("' + name + '", "' + ajax_id + '", "' + post + '", "' + s + '", r)'));
						}
					}
				}
			}

			// вывод местоположений найденных по gps
			var gps_show = (name == 'city' && edost_suggest && gps_active && V(e) == '');
			if (gps_show) {
				value_search = '';
				var i = self.index(name, 'gps');
				if (load.data[i]) suggest2 = load.data[i].data;
			}

			// поиск по загруженному списку
			var city_filter_active = false;
			var city_low = {};
			if (value_search.length >= load.start && (name != 'street' || value_search.length > 0)) {
				i = load.index = self.index(name, value_search);
				var f = {}, data = [];
				if (i >= 0 && load.data[i].data) {
					var v = load.data[i];
					data = v.data;
					if (!v.full) full = false;
					if (v.overlimit) overlimit = true;
					city_filter_active = (v.fed >= 0 || v.region >= 0 ? true : false);
					if (v.region_list) region_list = v.region_list;
				}
				if (data.length > 0) {
					if (name == 'zip') {
						if (value_search == '') f[0] = data;
						else W(data, function(v) { if (v[0].indexOf(value_search) >= 0) { if (!f[0]) f[0] = []; f[0].push(v); } });
					}
					else W(data, function(v) {
						var k = -1, n = 0;
						var s = ' ' + v[0].toLowerCase().replace(/\.\s/g, '*').replace(/[.]/g, ' ').replace(/[*]/g, '. ').replace(/[,]/g, ' ');
						if (name == 'city' && edost_suggest) s += self.data.region2[v[1]];
//						W(name == 'city' ? values_full : values_trim, function(v) {
						W(name == 'city' ? values2 : values_trim, function(v) {
							var p = 0, s2 = s, a = 0;
							while ((p = s2.indexOf(' ' + v)) >= 0) { if (!a) { a = 1; n++; } k += 2; s2 = s2.substr(p + v.length + 1); }
						});
						if (load.copy && load.copy[country]) W(load.copy[country], function(c) {
							W(values2, function(v) { if (v.length >= 3 && (c[0].indexOf(v) == 0 && s.indexOf(' ' + c[1]) >= 0 || c[1].indexOf(v) == 0 && s.indexOf(' ' + c[0]) >= 0)) k++; });
						});
						if (name == 'city' && edost_suggest) {
							if (k >= 0) W(['д.', 'с.'], function(v) { if (s.indexOf(' ' + v + ' ') == 0) { k++; return false; } });
							if (k >= 0) W([', г. '], function(v) { if (s.indexOf(' ' + v + ' ') > 0) { k++; return false; } });
							if (k >= 0) W(['рп', 'пгт', 'п.'], function(v) { if (s.indexOf(' ' + v + ' ') == 0) { k += 2; return false; } });
							if (k >= 0) W(['район'], function(v) { if (s.indexOf(' ' + v + ' ') == 0) { k += 3; return false; } });
							if (k > 0) W(['гск', 'тер.', 'дор.', 'снт', 'зона'], function(v) { if (s.indexOf(' ' + v + ' ') >= 0) { k -= 1; return false; } });
							if (k > 1) W(['гаражный массив'], function(v) { if (s.indexOf(' ' + v + ' ') >= 0) { k -= 3; return false; } });
							if (k > 0 && n == values2.length) k++;
						}
						if (k >= 0) {
							if (!f[k]) f[k] = [];
							f[k].push(v);
						}
					});
				}
				if (name == 'city' && edost_suggest) for (var k in f) edost.sort(f[k], 0, 'length'); // сортировка названий по длине
				var a = 1;
				for (var i = 25; i >= 0; i--) if (f[i]) {
					if (fed_active == -1 && region_active == -1 && !a) W(f[i], function(v) { city_low[v[0]] = 1; });
					a = 0;
					suggest2 = suggest2.concat(f[i]);
				}
			}

			// сортировка населенных пунктов по федеральным округам
			if (name == 'city' && edost_suggest && self.loading_data == 2 && suggest2.length > 0) {
				var f = [];
				W(suggest2, function(s) { W(self.data.region_fed, function(r,ri) { if (A(s[1], r.region)) {
					var k = -1;
					W(f, function(v,vi) { if (v.id == ri) { k = vi; return false; } });
					if (k == -1) { k = f.length; f.push({'id': ri, 'data': []}); }
					f[k].data.push([s[0], s[1], 0, ri]);
					return false;
				} }) });
	          	var u = [];
				W(f, function(v,i) { u = u.concat(v.data) });
	          	suggest2 = u;
			}
		}
		suggest = suggest.concat(suggest2);

		// проверка индекса
		if (name == 'zip' && edost_suggest) {
			if (value_search.length != value_search.replace(/[^0-9]/g, '').length) self.zip_warning('digit');
			else if (value_search.length > 6) self.zip_warning(1);
			else if (value_search.length < 6) self.zip_warning('');
			else {
				var a = 1;
				W(suggest, function(v) { if (v[0] == value_search) { a = (self.zip(v[0]) ? 2 : 0); return false; } });
				if (a == 2) return;
				W(load.cache, function(v) { if (v.value == value_search && self.compare(v.location, load.location)) { self.zip_warning(v.result); a = (v.result == '' && self.zip(value_search) ? 2 : 0); return false; } });
				if (a == 2) return;
				if (a && value_search != load.string_start && !self.error && !loading && !disable) {
					load.string_start = '';
					loading = true;
					self.zip_warning('checking');
					var s = 'type=check_zip&zip=' + value_search + '&country=' + encodeURIComponent(location[2]) + '&region=' + encodeURIComponent(location[1]) + '&city=' + encodeURIComponent(location[0]);
					edost.post('location', s, new Function('r', main_function + '.ajax("' + name + '", "' + ajax_id + '", "' + value_search + '", "' + s + '", r)'));
				}
				else redraw = true;
			}
		}

		// вывод списка
		browser_width = 0;
		var hint_disable = false;
		var n = suggest.length;
		if (name != 'city' && n > 15) { n = 15; full = false; }
		if (load.suggest_now < 1) load.suggest_now = 1;
		if (load.suggest_now > n) load.suggest_now = n;

		// автоматический выбор, если название введено полностью и в подсказках есть только один вариант выбора
		if (n == 1 && load.string == suggest[0][0] && A(name, ['street', 'zip', 'metro', 'list'])) {
			redraw = true;
			n = 0;
			hint_disable = true;
			if (name == 'street' && load.area) load.suggest = suggest[0];
		}

		// генерация списка подсказок
		load.suggest = false;
		var s = '', f = -1, fed = [], fed_id = {}, region_id = {}, count = 0, list_i = 0;
		for (var i = 0; i < n; i++) {
			var v = suggest[i];
			var active = (list_i == load.suggest_now - 1 ? true : false);

			// заголовок с названием федерального округа
			if (name == 'city' && edost_suggest && v[3] != undefined && v[3] != f && self.loading_data == 2) f = v[3];

			var s1 = v[0], s2 = '', region_i = -1;
			if (name == 'city' && v[1] != undefined && self.loading_data == 2) {
				if (edost_suggest) {
					region_i = v[1];
					s2 = self.data.region[0][ v[1] ];
				}
				else W(region_list, function(u) { if (u[1] == v[1]) { s2 = u[0]; return false; } });
				if (s2 != '') s1 = s1.replace(', г. ' + s2, '');
			}
			if (name == 'street' && v[2]) {
				var p = v[2].indexOf(', г. ');
				s2 = (p >= 0 ? v[2].substr(0, p) : v[2]);
			}
			if (s1 == s2) s2 = '';

			var w = [s1 + (s2 != '' ? ' (' + s2 + ')' : '')];
			w = w.concat(v);

			var u = '';
			var c = ' onmousedown="' + main_function + '.set_value(' + (typeof id === 'object' ? 'this' : "'" + id + "'") + ', \'' + w.join('|') + '\');"'
			if (name == 'city') u += edost.delimiter;
			u += '<div class="edost_L2_suggest_value %active%' + (i < bold_count ? ' edost_L2_suggest_bold' : '') + (city_low[v[0]] ? ' edost_L2_suggest_low' : '') + '"' + (name != 'city' ? c + ' style="cursor: pointer;"' : '') + '>';
			if (name == 'city') u += '<div class="edost_button_get"' + c + '><span>выбрать</span></div>';
			u += (s2 == '' ? '<div ' + (name == 'city' ? 'style="margin-top: 10px;"' : '') + '>' + s1 + '</div>' : s1);
			if (s2 != '') u += '<br><span>' + s2 + '</span>';
			u += '</div>';

			if (f == -1) {
				s += u.replace('%active%', active ? 'edost_L2_suggest_active' : '');
				if (active) load.suggest = w;
				list_i++;
			}
			else {
				count++;
				var k = I(fed, 'id', f, {'id': f, 'name': self.data.region_fed[f].name, 'data': []});
				fed_id[f] = true;
				if (region_i != -1) {
					region_id[region_i] = true;
					if (region_active != -1 && region_active != region_i) continue;
				}
				fed[k].data.push({'name': u, 'data': w});
			}
		}

		// подписи
		var hint = '', warning = false;
		if (!hint_disable && self.loading_data == 2) {
			if (!full && name != 'city') {
				hint = 'продолжайте набирать название, чтобы увидеть больше вариантов...';
				if (value_search.length > 2 && load.hint) hint += '<br>' + load.hint;
			}
			if (value_search.length > 0 && n == 0 && !load.loading && !load.warning_disable && full && !city_filter_active) {
				var v = 'warning' + (edost_suggest ? '' : '2');
				if (load[v]) hint = load[v].replace('%region%', region_list.length > 0 ? '<br>а если название указано верно, тогда вместо населенного пункта, пожалуйста, впишите и выберите ваш регион' : '');
				warning = true;
			}
		}

		var hint_hide = false, s2 = '';
		if (name == 'city') city_full = full;

//		var found = [0, 0];

		// вывод списков фильтрации по округу и региону
		if (name == 'city' && city_filter !== 'loading')
			if (count > 0 || !full && value_search.length > 2 || city_filter || city_filter_active) {
				var a = (!full || city_filter || city_filter_active || count > 100 && values_length <= 1 && !(fed_active == -1 && region_active == -1 && fed.length == 1) ? true : false);

	          	// выпадающий список округов и регионов
				var w = '';
				if (a) {
					var c = (load.index != -1 ? load.data[load.index] : false);
					if (fed_active == -1 && region_active != -1) W(self.data.region_fed, function(v,i) { if (A(region_active, v.region)) { fed_active = i; return false; } });

		          	var u = '<option style="color: #F00;" value="-1">не выбран</option>';
		          	W(self.data.region_fed, function(v,i) { if ((!full || c && (c.fed != -1 || c.region != -1) || fed_id[i]) && v.name != 'Казахстан') u += '<option value="' + i + '"' + (i == fed_active ? ' selected' : '') + '>' + v.name.replace(' округ', '') + '</option>'; });
					w += '<div class="edost_L2_filter">';
					w += '<div><span>федеральный округ</span><select data-type="fed" ' + (fed_active >= 0 ? 'class="edost_L2_filter_active"' : '') + ' onchange="' + main_function + '.fed(this)">' + u + '</select></div>';
		          	var f = self.data.region_fed[fed_active];
					var m = [], m2 = [];
		          	W(self.data.region[0], function(v, i) {                                // !city_filter
						if (v == '' || fed_active != -1 && f && !A(i, f.region) || full && !region_id[i]) return;
						if (v.indexOf('Республика') == 0) v = v.substr(11);
						(A(v, edost.fed_city) ? m2 : m).push({'name': v, 'id': i});
					});
					edost.sort(m, 'name');
					edost.sort(m2, 'name');
					m = m2.concat(m);
		          	var u = '<option style="color: #F00;" value="-1">не выбран</option>';
		          	W(m, function(v) { u += '<option value="' + v.id + '"' + (v.id == region_active ? ' selected' : '') + '>' + v.name + '</option>'; });
					w += '<div class="edost_L2_filter_or"> ИЛИ </div><div><span>регион</span><select data-type="region" ' + (region_active >= 0 ? 'class="edost_L2_filter_active"' : '') + ' onchange="' + main_function + '.fed(this)">' + u + '</select></div>';
					w += '</div>';
					if (m.length == 1) {
						if (region_active != -1) { region_active = -1; self.suggest(id, param); return; }
						a = false;
					}
				}

				if (a && fed_active == -1 && region_active == -1) s2 += '<div class="edost_L2_filter_note">найдено ' + (s != '' ? 'еще ' : '') + 'более 100 соответствий <br> ' + (s != '' ? 'если вашего населенного пункта нет выше, тогда' : '') +' для уточнения поиска, пожалуйста, выберите ваш</div>';
				s2 += '<div ' + (s2 == '' && s != '' ? ' style="padding-top: 20px;"' : '') + '>';
				if (a) s2 += w;

	          	var city_count = 0;
	          	W(fed, function(v) {
					if (v.data.length == 0 || a && v.id != fed_active) return;
					if (!a) {
						s2 += '<div class="edost_L2_fed_main">';
						s2 += v.name;
					}
					s2 += '<div class="edost_L2_fed">';
		          	W(v.data, function(v2) {
						var active = (list_i == load.suggest_now - 1 ? true : false);
						list_i++;
						if (active) load.suggest = v2.data;
						s2 += v2.name.replace('%active%', active ? ' edost_L2_suggest_active' : '');
						city_count++;
					});
					s2 += edost.delimiter;
					if (hint != '')	{
						hint_hide = true;
						s2 += '<div class="edost_L2_suggest_hint">' + hint + '</div>';
					}
					s2 += '</div>';
					if (!a) s2 += '</div>';
				});

				if (city_filter && city_count == 0 && (fed_active >= 0 || region_active >= 0)) s2 += '<div class="edost_L2_suggest_warning" style="padding-top: 10px;">' + load.warning + '</div>';

				s2 += '</div>';
			}
			else if (!full && value_search.length <= 2) hint = 'продолжайте набирать название, чтобы увидеть больше вариантов...';
		if (s != '') s += edost.delimiter;
		if (load.data && load.loading || self.loading_data != 2 && A(name, ['city', 'metro'])) s += '<div style="' + (name == 'city' ? 'padding-top: 40px;' : 'padding: 4px;') + '">' + (name == 'city' ? edost.loading : edost.loading20) + '</div>';
		if (gps_show) s += '<div class="edost_L2_gps_head">местоположения найденные по GPS координатам</div>';

		s = s + s2;

		if (list_i != 0 && load.suggest_now > list_i) {
			load.suggest_now = list_i;
			self.suggest(name, 'redraw');
			return;
		}

		if (hint_hide) hint = '';

		var w = false;
		W(2, function(i) {
			w = self.name(e, 'div');
			if (w) return false; else if (i == 0 && !w) e.insertAdjacentHTML('afterend', '<br><div class="edost_L2_suggest_div"></div>');
		});
		if (!w) return;

		if (redraw || !disable && (gps_show || city_filter || hint != '' || name == 'street' && value_search.length > 0 || name == 'zip' && n > 0 || s != '' && ((suggest.length > 0 || overlimit) && (s2 != 0 || n > 0 || self.loading_data != 2) || load.loading))) {
			if (name != 'city') w.style.minWidth = (text_width - 1) + 'px';
			D(w, 'inline-block');

			var E_data = E([w, '.edost_L2_suggest_data']);
			if (!E_data) E_data = edost.create('DIV', '', {'class': 'edost_L2_suggest_data', 'E': w});

			var E_hint = E([w, '.edost_L2_suggest_hint, .edost_L2_suggest_warning']);
			if (!E_hint) E_hint = edost.create('DIV', '', {'class': 'edost_L2_suggest_hint', 'E': w});

			E(E_data, {'display': !warning, 'html': s});
			E(E_hint, {'html': hint, 'class': warning ? 'edost_L2_suggest_warning' : 'edost_L2_suggest_hint', 'display': hint != ''});
		}
		else if (value_search == '' || name == 'list') D(w, false);

		edost.window.resize();

	}

	this.keydown = function(id, event) {

		edost.ru(event);

		var v = self.name(id);
		if (!v) return;
		var name = v.name;

//		var name = id.split('_')[1];
		var load = self.load[name];
		var redraw = false;
		var c = event.keyCode;

		if (A(c, [38, 13])) if (event.preventDefault) event.preventDefault(); else event.returnValue = false;
		if (c == 38) { load.suggest_now--; redraw = true; }
		if (c == 40) { load.suggest_now++; redraw = true; }
		if (c == 13) {
			self.set_value(id);
			load.suggest_set = false;
			redraw = true;
			if (A(name, ['street', 'metro', 'zip'])) {
				var e = E('edost_' + name);
				if (e) { e.blur(); return; }
			}
		}

		if (redraw) self.suggest(id, 'redraw');

	};

	this.window = function(param) {

		if (window.edost_location && edost_location.window) { edost_location.window(param); return; } // поддержка старых функций

		var template = (window.edost_SetTemplateLocation ? true : false);
		var hide = (param == 'close' ? true : false);
		var header = (E('edost_location_header_div') ? true : false);

		if (param == 'loading') edost.backup('location');
		if (param == 'back') {
			edost.backup('location', true);
			if (template) edost_SetTemplateLocation('back'); else D(['#edost_location_address_n3 #edost_location_address_div'], true);
			hide = true;
		}

		if (hide) {
		    if (onkeydown_backup != 'free') {
				document.onkeydown = onkeydown_backup;
				onkeydown_backup = 'free';
			}
		}
		else if (onkeydown_backup == 'free') {
		    onkeydown_backup = document.onkeydown;
			document.onkeydown = new Function('event', 'if (event.keyCode == 27) ' + main_function + '.window("back");');
		}

		var e = E('edost_window_location_div');
		if (!e) {
			if (hide) return;
			edost.create('WINDOW', 'edost_window_location_div', {'html': '<div class="edost_location_city_window_data"></div><div id="edost_location_city_window_suggest"></div>', 'class': ''});
		}

		var display = (hide ? 'none' : 'block');
		edost.window.set(!hide ? 'window_location' : 'close_full');

		var e = E('edost_window_location_div');
		if (!e || hide && D(e) == 'none') return;
		D(e, display);

		var e = E(['#edost_window .edost_location_city_window_data']);
		if (param == 'back') { H(e, ''); return; }
		if (param == 'loading') {
			if (template) edost_SetTemplateLocation('loading');
			H(e, edost.loading128);
			edost.window.resize();
			return;
		}

		var e2 = E('edost_location_city_div');
		if (!e2) return;

		if (param == 'close') {
			if (template) edost_SetTemplateLocation('set');
			if (header) {
				D(e2, false);
				var e2 = E('edost_location_header_data');
				H(e2, H(e));
				H(e, '');
				self.loading('edost_location_city_loading', 'submit');
			}
			else {
				var c = E('edost_country');
				var i = (c ? c.selectedIndex : 0);
				H(e2, H(e));
				H(e, '');
				var c = E('edost_country');
				if (c) c.selectedIndex = i;
			}
		}
		else {
			if (header) e2 = E('edost_location_header_data');
			H(e, H(e2));
			H(e2, '');
			self.city_focus();
			edost.window.resize();
		}

	}

}




edost.catalogdelivery = new function() {
	var self = this, E = edost.E, V = edost.V, D = edost.D, H = edost.H, C = edost.C, A = edost.A, I = edost.I, P = edost.P, W = edost.W, N = edost.N
	var main_function = 'edost.catalogdelivery'
	var clear_white, size = {auto: true, x: 650, y: 80}, show_quantity, show_add_cart, add_cart, show_button, info = '', error = '<div class="edost_warning2">Расчет недоступен</div>'
	var template_format = false, template_block_type = false, template_map_inside = false
	var onkeydown_backup = 'free', window_top = 0, browser_width = 0, browser_height = 0, start_h = 0
	var inside = false
	var quantity_timer
	var window_data = ''

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

	this.preview = function(p) {
		var r = false;
		W('#edost_catalogdelivery_inside, .edost_C2_preview_data', function(v) {
			if (p === true) { r = true; return false; }
//			if (p.html !== undefined)
			E(v, p);
		});
		return r;
	}

	// расчет доставки
	this.calculate = function(param, s1, s2) {

		var e = E('edost_bookmark');
		if (e) self.bookmark = V(e);

		if (param == 'preview') {
			if (!self.preview(true)) return;

			self.set_window_param();
			self.product_id = s1;
			self.product_name = s2;

			D('edost_catalogdelivery_inside_city_head', false);
		}
		else if (param) {
			var a = false;
			if (param == 'loading') {
				var e = E('edost_catalogdelivery_window_city');
				if (e && H(e) == '') a = true;
			}
			if (param == 'loading_location') { a = true; param = 'loading'; }
			if (a) D('edost_catalogdelivery_window_city_div', false);

			var e = E('edost_catalogdelivery_data');
			if (!e) return;

			if (param == 'loading')	H(e, self.loading);
			else if (param == 'error') H(e, '<div class="edost_warning edost_warning_big">' + s1 + '</div>');

			return;
		}

		start_h = 0;

		self.tariff();

	}


	// расчет по таймеру
	this.timer = function(param) {

		if (param == 'start') {
			if (quantity_timer != undefined) window.clearInterval(quantity_timer);
			quantity_timer = window.setInterval(main_function + ".tariff('quantity')", 300);
		}
		else {
			if (quantity_timer != undefined) quantity_timer = window.clearInterval(quantity_timer);
		}

	}


	// вывод ошибки
	this.set_error = function() {
		H('edost_catalogdelivery_window_error', '<div style="' + (!inside ? 'text-align: center; ' : '') + 'padding-top: 20px;">' + error + '</div>');
	}


	// параметры окна
	this.set_window_param = function(param, data) {
//		alert('set_window_param: ' + param + ' | ' + data);

		if (param == 'inside') inside = true;

		// загрузка параметров из html
		var e = E('edost_catalogdelivery_param');
		if (e && e.value) self.param_string = e.value;

		if (!data) data = V('edost_catalogdelivery_window_param');
		v = data.split('|');

		size = {auto: (v[0] == 'N' ? false : true), x: (!v[1] || v[1] < 400 ? 650 : v[1]), y: (!v[2] || v[2] < 80 ? 80 : v[2])};
		clear_white = (v[3] == 'Y' ? true : false);
		show_quantity = (v[4] != undefined && v[4] == 'N' ? false : true);
		show_add_cart = (v[5] != undefined && v[5] == 'N' ? false : true);
		show_button = (v[7] != undefined && v[7] == 'Y' ? true : false);
		if (v[8]) info = v[8].replace(/&quot;/g, '"');
		if (v[9]) error = v[9].replace(/&quot;/g, '"');

		self.loading = '<div class="edost_map_loading2">' + edost.loading128 + '</div>';
		self.loading_small = edost.loading20;

		template_format = (v[12] != undefined && v[12] == 'Y' ? true : false);
		template_block_type = (v[13] != undefined && v[13] == 'Y' ? true : false);
		template_map_inside = (v[14] != undefined && v[14] == 'Y' ? true : false);

		add_cart = (edost.cookie('edost.catalogdelivery').split('|manual=')[0] == 1 ? true : false);

		self.set_error();

	}


	// заполнение блока с расчетом, города и ссылки на детальную информацию
	this.set_data = function(tariff_data, location_data, city, detailed) {
//		alert('set_data: ' + tariff_data + ' | ' + location_data);

		self.set_window_param();
		self.location_data = location_data;
		if (tariff_data === 'FALSE') self.set_error();

		var link_begin = '<span class="edost_link" onclick="edost.catalogdelivery.window(';
		var link_end = '</span>';

		var e = E('edost_catalogdelivery_window_city');
		if (e) {
			if (location_data == 'GETCITY') edost.catalogdelivery.window('getcity');
			else H(e, location_data);
		}

		if (inside) return;

		H('edost_catalogdelivery_inside_city', link_begin + "'getcity');\">" + city + link_end);
		self.preview({'html': tariff_data === 'FALSE' ? error : tariff_data, 'display': tariff_data != ''});
//		E('edost_catalogdelivery_inside', {'html': tariff_data === 'FALSE' ? error : tariff_data, 'display': tariff_data != ''});
		E('edost_catalogdelivery_inside_detailed', {'html': link_begin + ');">подробнее...' + link_end, 'display': detailed == 'Y'});

		if (self.product_id == 0) {
			var e = E('edost_catalogdelivery_product_id');
			if (e) self.product_id = V(e);
		}

		edost.window.resize();
//		edost.catalogdelivery.resize(true);

	}

	this.submit = function(id, mode, profile, cod) {
		if (!profile && mode) edost.window.set('close');
		self.calculate('loading');
		edost.post('catalogdelivery', (profile ? 'set_office' : 'set_tariff') + '=Y&id=' + id + '&mode=' + mode + '&profile=' + profile + '&cod=' + cod, function(r) {
			self.calculate();
		});
	}

	this.set = function(v) {
		H('edost_catalogdelivery_data', edost.window.filter(v));
		edost.remove('edost_window_data_head_bookmark');
		v = E('window_edost_bookmark_main');
		if (!v) return;
		edost.create('DIV', 'edost_window_data_head_bookmark', {'html': H(v), 'E': E('edost_window_data_head')});
		edost.remove(v);
	}

	this.window = function(param, product_id, product_name) {
//		alert(param + ' - ' + product_id);

		if (param == 'getcity') {
//			edost.backup('location_header', false, false);
			if (window.edost_ExternalLocation) { edost_ExternalLocation(); return; }
			if (self.load_location('edost_location')) return;
		}

		if (self.loading == '') self.set_window_param();

		if (product_id == undefined) product_id = '';
		if (product_name == undefined) product_name = '';

		if (param == 'esc') param = 'close';
		if (param == 'inside') {
			inside = true;
			param = '';
		}

/*
		if (inside) {
			var e = E('edost_catalogdelivery_window_fon');
			if (e) {
				edost.remove(e);
				edost.remove('edost_catalogdelivery_window');
			}
		}
		else {
			if (onkeydown_backup === 'free') {
			    onkeydown_backup = document.onkeydown;
				document.onkeydown = new Function('event', 'if (event.keyCode == 27) edost.catalogdelivery.window("esc");');
			}
			else if (param == 'close') {
			    document.onkeydown = onkeydown_backup;
			    onkeydown_backup = 'free';
			}
		}
*/
		// интеграция окна

//		var e = E('edost_catalogdelivery_window');
//		if (!e) {
			if (!inside) {
/*
				var e = document.body;
				var E2 = document.createElement('DIV');
				E2.className = 'edost_catalogdelivery_window_fon';
				E2.id = 'edost_catalogdelivery_window_fon';
				E2.style.display = 'none';
				E2.onclick = new Function('', 'edost.catalogdelivery.window("close")');
				e.appendChild(E2);
*/
			}
			else var e = E('edost_catalogdelivery_inside');
/*
			var E2 = document.createElement('DIV');
			if (!inside) E2.className = 'edost_catalogdelivery_window2';
			E2.id = 'edost_catalogdelivery_window';
			E2.style.display = 'none';
*/
			var s = '';
//			s += '<div id="edost_catalogdelivery_window_head_div" class="edost_catalogdelivery_window_normal" style="padding-bottom: 10px;">';
			s += '<div id="edost_window_data_head" class="edost_catalogdelivery_window_normal">';
/*
			if (!inside) {
				s += '<div style="' + (!clear_white ? 'height: 28px; ' : '') + 'text-align: center; padding-bottom: 5px;" class="edost_catalogdelivery_window_head">';
				s += '<div class="edost_catalogdelivery_window_close2" onclick="edost.catalogdelivery.window(\'close\')"></div>';
				s += '<span id="edost_catalogdelivery_window_head"></span>';
				s += '</div>';
			}
*/
			if (info) s += '<div style="text-align: center; padding: ' + (clear_white ? '0px 10px 5px 10px;' : '10px 10px 0px 10px;') + '"><div id="edost_catalogdelivery_window_info">' + info + '</div></div>';

			s += '<div id="edost_catalogdelivery_window_city_div" class="edost_catalogdelivery_window_city_div" style="padding: 0 10px 20px 10px;">';

//			s += '<div id="edost_catalogdelivery_window_city_head" style="margin: 2px 5px 0 0; text-align: right; ' + (show_quantity || show_add_cart ? '' : 'color: #AAA;') + '">местоположение:</div>';
			s += '<div id="edost_catalogdelivery_window_city" style="">' + self.location_data + '</div>';

			if (show_quantity || show_add_cart) {
				s += '<div id="edost_catalogdelivery_window_quantity_div" style="padding-top: 5px;">';
				if (show_quantity) s += '<div id="edost_catalogdelivery_window_quantity_head" style="display: inline-block; margin: 2px 5px 0 0; text-align: right;">количество: </div>';
				if (show_quantity) s += '<div style="display: inline-block;"><input id="edost_catalogdelivery_quantity" value="1" size="4" style="vertical-align: middle; width: 40px;" onfocus="edost.catalogdelivery.timer(\'start\')" onblur="edost.catalogdelivery.timer(\'\')"></div>';
				if (show_add_cart) s += '<div style="display: inline-block;"><input type="checkbox" id="edost_catalogdelivery_cart"' + (add_cart ? ' checked=""': '') + ' style="margin-left: ' + (show_quantity ? '30' : '0') + 'px; vertical-align: middle;" onclick="' + main_function + '.tariff()"> <label style="vertical-align: middle;" for="edost_catalogdelivery_cart"><span id="edost_catalogdelivery_cart_name">учитывать товары в корзине</span><span id="edost_catalogdelivery_cart_name2">корзина</span></label></div>';
				s += '</div>';
			}

			s += '</div>';

			s += '</div>';

			s += '<div id="edost_window_data_data">';
			s += '<div id="edost_catalogdelivery_data">';
			s += '<input type="hidden" id="edost_catalogdelivery_window_no_data" value="">';
			s += '</div>';
			s += '</div>';

			if (show_button && !inside) {
				s += '<div class="edost_catalogdelivery_button edost_catalogdelivery_button_bar" style="width: 120px;" onclick="edost.catalogdelivery.window(\'close\');">Закрыть</div>';
				if (show_quantity || show_add_cart) s += '<div class="edost_catalogdelivery_button edost_catalogdelivery_button_bar" style="width: 150px;" onclick="' + main_function + '.tariff()">Пересчитать</div>';
			}

			window_data = s;
//			E2.innerHTML = s;

//			if (e) {
//				if (inside) H(e, '');
//				e.appendChild(E2);
//			}
//		}

/*
		var display = (param == 'close' ? 'none' : 'block');
		if (inside) display = 'block';

		var e = E('edost_catalogdelivery_window');
		if (!e) return;
		e.style.display = display;
*/
//		var e = E('edost_catalogdelivery_window_fon');
//		if (e) e.style.display = display;

//		if (param == 'close') return;

		if (product_name == '') {
			var e = E('edost_catalogdelivery_product_name');
			if (e) product_name = e.value;
		}
		if (product_name == '') product_name = self.product_name;
		if (product_name != '') {
			self.product_name = product_name;
//			var e = E('edost_catalogdelivery_window_head');
//			if (e) {
//				var s = (clear_white ? 50 : 70);
//				if (product_name.length > s) product_name = product_name.substring(0, s) + '...';
//				e.innerHTML = (clear_white ? '<span style="display: inline-block; overflow: hidden;">' + self.head + '</span>' : '') + '<span id="edost_catalogdelivery_window_head_product_name">' + (clear_white ? ': ' : '') + product_name + '</span>';
//				e.innerHTML = '<span style="display: inline-block; overflow: hidden;">' + self.head + '</span><span id="edost_catalogdelivery_window_head_product_name">: ' + product_name + '</span>';
//			}
		}

//		if (button) u['head_button'] = ' ' + (button === true ? '<span class="button_small" onclick="set_function(\'check\')">выделить</span> <span class="button_small" onclick="set_function(\'uncheck\')">сбросить</span>' : button);
//'id=' + param + ';head=' + H(['#function_' + param + '_div .head']) + ';' + f[param + '_window'] + ';save=1;class=window_' + param + (param != 'location' ? ';small_height=1' : '') + ';function=set_function("save", "' + param + '")
//		var s = (clear_white ? 50 : 70);
		if (product_name.length > 50) product_name = product_name.substring(0, 35) + '...';
//		s = '<span style="display: inline-block; overflow: hidden;">' + self.head + '</span><span id="edost_catalogdelivery_window_head_product_name">: ' + product_name + '</span>';

//.edost_full_delivery_normal .edost_resize_day2, .edost_full_delivery_normal2 .edost_resize_day2

		var w = 750, h = 750, c = 'edost_C2_window'; //'edost_full_main';
		if (self.param_string.indexOf('template(compact') >= 0) { w = 650; h = 650; c += ' edost_compact_main edost_compact_main2'}

		edost.window.set('catalogdelivery', 'head=Расчет доставки' + (!edost.mobile ? ': ' + product_name : '') + ';width=' + w + ';height=' + h + (c != '' ? ';class=' + c : ''), {'html': window_data});

		var product_id_old = self.product_id;
		if (product_id == '') {
			var e = E('edost_catalogdelivery_product_id');
			if (e) product_id = e.value;
		}
		if (product_id != '') self.product_id = product_id;

		if (param == 'getcity') {
			H('edost_catalogdelivery_data', '');
//			self.load_location('start');
		}
		else {
			var e = E('edost_catalogdelivery_window_no_data');
			if (e || product_id_old > 0 && self.product_id != product_id_old) {
//				var e = E('edost_catalogdelivery_LOCATION');
				var u = E('edost_catalogdelivery_load');
				if (u) self.set(H(u));
				else {
					var e = E('edost_shop_LOCATION');
					if (!e) {
						D('edost_catalogdelivery_window_city_table', false);
						D('edost_catalogdelivery_window_info', false);
					}
					self.tariff();
				}
			}
		}

//		if (D('edost_office_div') != 'none' && window.edost.office && edost.office.map) edost.office.map.container.fitToViewport();

		if (E('window_edost_office_inside') || E('edost_office_inside')) {
			edost.office2.map = false;
			edost.office2.set('inside'); // !!!!!
		}

		edost.window.resize('redraw');

//		if (!inside) {
//			self.resize(true);
//			timer = window.setInterval("edost.catalogdelivery.resize()", 200);
//		}

	}

	this.tariff = function(param) {

		var e = E('edost_catalogdelivery_quantity');
		var quantity = (e ? e.value : 1);

		if (param == 'quantity') {
			if (quantity != edost.catalogdelivery.quantity && quantity > 0) edost.catalogdelivery.quantity = quantity;
			else return;
		}

		var post = '';
		var param = edost.catalogdelivery.param_string;

		var e = E('edost_shop_LOCATION');
		var id = (e ? e.value : 0);

		var e = E('edost_shop_ZIP');
		if (!e) e = E('edost_catalogdelivery_zip'); // manual
		var zip = (e ? e.value : '');

		var e = E('edost_city2');
		var city2 = encodeURIComponent(e ? e.value : '');

		var e = E('edost_bookmark');
		var bookmark = (e ? e.value : edost.catalogdelivery.bookmark);

//<?		if ($mode == 'manual' && !$edost_locations) { ?>
//		if (!(id > 0)) {
//			edost.catalogdelivery.calculate('error', '<?=$error['head'].$error['location']?>');
//			return;
//		}
//<?		} ?>

		var c = edost.cookie('edost_catalogdelivery');
		c = c.split('|manual=');
		var c1 = c[0];
		var c2 = (c[1] ? c[1] : '');
/*
<?		if ($mode == 'manual') { ?>
		post += '&manual=Y';

		c2 = [];
		var ar = ['weight', 'price', 'size1', 'size2', 'size3'];
		for (var i = 0; i < ar.length; i++) {
			var e = E('edost_catalogdelivery_' + ar[i]);
			var v = (e ? encodeURIComponent(e.value.replace(/[^0-9,.]/g, '').replace(/,/g, '.')) : '');
			if (e) post += '&' + ar[i] + '=' + v;
			if (ar[i] == 'weight' && v == 0) {
				edost.catalogdelivery.calculate('error', '<?=$error['head'].$error['weight']?>');
				return;
			}
			c2.push(v);
		}
		c2 = c2.join('|');
<?		} else { ?>
*/
		var e = E('edost_catalogdelivery_cart');
		var add_cart = c1 = (e && e.checked ? 1 : 0); // при встроенном расчете галочка "включить корзину" сбрасывается !!!!!
		post = '&product=' + edost.catalogdelivery.product_id + '&quantity=' + quantity + '&addcart=' + add_cart;

		var e = E('edost_catalogdelivery_product_price_' + edost.catalogdelivery.product_id);
		if (e) post += '&price=' + e.value;
//<?		} ?>

		edost.cookie('edost_catalogdelivery', c1 + '|manual=' + c2);

//<?		if ($edost_locations) { ?>
		post += '&edost_locations=Y';
//<?		} else { ?>
//		if (id) edost.cookie('edost_location', id + '|' + zip + '|');
//<?		} ?>

		var e = E('edost_template_width');
		if (e && e.value > 0) post += '&edost_template_width=' + e.value;

		self.calculate('loading');
//		BX.ajax.post('<?=$arResult['component_path']?>/ajax.php', 'mode=window&param=' + param + '&id=' + id + '&zip=' + zip + '&city2=' + city2 + '&bookmark=' + bookmark + post, function(r) {
		edost.post('catalogdelivery', 'mode=window&param=' + param + '&id=' + id + '&zip=' + zip + '&city2=' + city2 + '&bookmark=' + bookmark + post, function(r) {
	        var e = E('edost_catalogdelivery_load');
	        if (!e) e = edost.create('DIV', 'edost_catalogdelivery_load', {'E': document.body}, {'display': 'none'});
	        H(e, r);

			self.set(r);
//			H('edost_catalogdelivery_data', edost.window.filter(r));
			D('edost_catalogdelivery_window_city_div', true);

// || E('edost_office_inside')
			if (E('window_edost_office_inside')) {
				if (edost.office2 && edost.office2.map) edost.office2.map = false;
				edost.office2.set('inside'); // !!!!!
//				if (edost.office2.inside) edost.office2.resize('redraw');
			}

			edost.window.resize('redraw');
		});

	}


	this.load_location = function(param, id) {
//	function edost_LoadLocation(param, id) {
//		console.log('===== self.load_location: ' + param + ' | ' + id);

		var edost_locations = E('edost_locations');

		if (id == undefined) id = V('edost_shop_LOCATION');

		var return_value = false;

		if (edost_locations && param == 'edost_location') {
			if (E('edost_location_header')) {
				edost.location.header_set('click');
				return true;
			}
			if (!E('edost_shop_LOCATION')) edost.backup('location_header', false, self.loading_small);
            return_value = true;
		}

		var id_default = 0;
		var s = self.param_string;
		var p = s.indexOf('location_id_default(');
		if (p >= 0) id_default = s.substr(p + 20).split(')')[0];

		var e = E('edost_catalogdelivery_window_city');
		if (e && param == 'start') {
			var s = self.loading_small;
			if (edost_locations) s = '<div class="edost_catalogdelivery_window_city">' + s + '</div>';
			H(e, s);
		}

		edost.post('catalogdelivery', 'location=Y&id=' + id + '&default=' + id_default + (edost_locations ? '&edost_locations=Y' : ''), function(r) {
			if (!edost_locations) H(e, r);
			else {
				edost.create('DIV', 'edost_location_header_hidden', {'html': r, 'E': document.body}, {'display': 'none'});
				edost.location.header_set('click');
				edost.backup('location_header', true);
			}
		});

		return return_value;

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
edost_resize.change_class = edost.C;
