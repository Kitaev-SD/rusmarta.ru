var edost_location = new function() {
	var self = this;
	var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://')
	var key_map = {"ё":"е","q":"й","w":"ц","e":"у","r":"к","t":"е","y":"н","u":"г","i":"ш","o":"щ","p":"з","[":"х","]":"ъ","a":"ф","s":"ы","d":"в","f":"а","g":"п","h":"р","j":"о","k":"л","l":"д",";":"ж","\'":"э","z":"я","x":"ч","c":"с","v":"м","b":"и","n":"т","m":"ь",",":"б",".":"ю","/":"."}
	var city_street = ['Москва','Саратов','Ростов-на-Дону','Казань','Волгоград','Астрахань','Новосибирск','Санкт-Петербург','Краснодар','Омск','Челябинск','Уфа','Пермь','Воронеж','Улан-Удэ','Пенза','Екатеринбург','Грозный','Нижний Новгород','Хабаровск','Тюмень','Шахты','Севастополь','Кемерово','Рыбинск','Симферополь','Иваново','Прокопьевск','Красноярск','Ярославль','Выборг','Калининград','Ижевск','Ульяновск','Курск','Самара','Чита','Оренбург','Орск','Магнитогорск','Липецк','Тверь','Томск','Иркутск','Брянск','Владивосток','Киселевск','Барнаул','Сызрань','Старый Оскол','Хасавюрт','Тула','Керчь','Ставрополь','Элиста','Белгород','Копейск','Орел','Ленинск-Кузнецкий','Южно-Сахалинск','Смоленск','Гудермес','Нижний Тагил','Новороссийск','Новошахтинск','Калуга','Таганрог','Тамбов','Рязань','Псков','Анжеро-Судженск','Комсомольск-на-Амуре','Кострома','Чебоксары','Артем','Архангельск','Новокузнецк'];
	var loading_small = '<span class="edost_loading_small"><img src="' + protocol + 'edostimg.ru/img/site/loading_small.gif" border="0" width="20" height="20"> <span>Поиск...</span></span>'
	var loading_big = '<div class="edost_loading_big"><img src="' + protocol + 'edostimg.ru/img/site/loading.gif" border="0" width="64" height="64"></div>'
	var data_link = protocol + 'edostimg.ru/shop/';
	var data_file = 'location_data.js?a=4';
	var loading_data_last = 0, disable = false, ajax_id = 0, timer = false, browser_width = 0, browser_height = 0, city_width = 0, onkeydown_backup = 'free'
	var develop = ''

	this.window_width = 800
	this.window_height = 600
	this.loading = false
	this.loading_data = 0
	this.error = false
	this.device = ''
	this.header = false
	this.mode = ''
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
			data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
			cache_id: -1,
			cache: []
		},
		city: {
			type: "city",
			preload: true,
			word: true,
			ignore: ['г','п','д','пос','район','обл','область','н','а','в','а/я','аал','аул','въезд','высел','гп','днп','дп','жилзона','жилрайон','микрорайон','жт','заезд','кольцо','кп','н/п','нп','обл','округ','п/о','п/р','п/ст','пгт','пл-ка','полустанок','промзона','проток','р-н','рзд','рп','с','с/а','с/мо','с/о','с/п','с/с','сквер','сл','снт','спуск','ст','ст-ца','станица','стр','тер','у','уч-к','ф/х','х','ш'],
			ignore_inside: ['тов', 'ово', 'род', 'ики', 'ург'],
			short: [['мск', 'Москва', 77], ['спб', 'Санкт-Петербург', 78], ['питер', 'Санкт-Петербург', 78]],
			hint: 'а если название указано верно, но искомого все еще нет, тогда подпишите название вашего района или региона',
			warning: 'не найдено ни одного подходящего населенного пункта - проверьте написание...<br>и убедитесь, что начинаете ввод именно с названия населенного пункта, а не региона, района и т.п.',
			value: false,
			index: -1,
			loading: false,
			data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
		},
		street: {
			type: "street",
			ignore: ['б-р','бул','бульвар','пр-кт','просп','проспект','мкр','мк-рн','микрорайон','ул','улица','туп','тупик','наб','набережная','ал','аллея','пер','переулок','линия','пр','пр-зд','проезд','проулок','просека','автодорога','пл','площадь','массив','кв-л','квартал','тракт','ряды','ш'],
			warning: 'не найдено ни одной улицы, проспекта, переулка и т.д. с таким названием - проверьте написание...<br>если указываете сокращенный вариант названия, тогда попробуйте указать полный (и наоборот)',
			value: false,
			index: -1,
			loading: false,
			data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
		}
	}

	this.trim = function(s, space) {
		s = s.replace(/^\s+|\s+$/gm, '');
		if (space) s = s.replace(/\s+/g, ' '); // удаление дублирующих пробелов
		return s;
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


	// подключение скрипта с данными *****
	this.add_data = function() {
		if (self.loading_data == 1 || document.getElementById('edost_location_data_script')) return;

		self.loading_data = 1;

		var E = document.getElementById('edost_location_script');
		var src = (E ? E.getAttribute('data-src') : false);

		var E = document.body;
		var E2 = document.createElement('SCRIPT');
		E2.id = 'edost_location_data_script';
		E2.type = 'text/javascript';
		if (!src) E2.charset = 'utf-8';
		E2.src = (src ? src : data_link) + data_file;
		E.appendChild(E2);
	}


	this.resize = function() {

		var E = document.getElementById('edost_city');
		if (!E) return;

		var browser_w = (document.documentElement.clientWidth == 0 ? document.body.clientWidth : document.documentElement.clientWidth);
		var browser_h = (document.documentElement.clientHeight == 0 ? document.body.clientHeight : document.documentElement.clientHeight);
		var city_w = E.offsetWidth;

		if (browser_w == browser_width && browser_h == browser_height && city_w == city_width) return;

		browser_width = browser_w;
		browser_height = browser_h;
		city_width = city_w;

		var E_data = document.getElementById('edost_location_city_window_data');
		if (!E_data || E_data.innerHTML == '') return;

		var fullscreen = true;
		var head_h = 0;
		if (self.device == 'phone') {
			var s = 16;
			if (browser_w > 500) s = 22; else if (browser_w > 350) s = 18;
			var E = document.getElementById('edost_location_city_window_head');
			if (E) {
				E.style.fontSize = s + 'px';
				head_h = E.offsetHeight;
			}
		}
		else {
			if (browser_w > 1000 && browser_h > 700) fullscreen = false;

			E_data.style.padding = (!fullscreen ? '20px' : '10px 0 0 0');
			head_h += (!fullscreen ? 40 : 10);

			var E = document.getElementById('edost_location_city_window_head');
			if (E) {
				E.style.fontSize = (browser_w > 500 ? '24px' : '18px');
				head_h += E.offsetHeight;
			}

			var E = document.getElementById('edost_location_city_window');
			if (!fullscreen) {
				E.classList.add('edost_location_city_window_border');
				E.style.width = self.window_width + 'px';
				E.style.height = self.window_height + 'px';
				E.style.left = Math.round((browser_w - self.window_width)*0.5) + 'px';
				E.style.top = Math.round((browser_h - self.window_height)*0.5) + 'px';
				browser_h = 600;
			}
			else {
				E.classList.remove('edost_location_city_window_border');
				E.style.width = 'auto';
				E.style.height = 'auto';
				E.style.left = 0;
				E.style.top = 0;
			}
		}

		var E = document.getElementById('edost_city');
		if (E) head_h += E.offsetHeight;
		var E = document.getElementById('edost_country_list_div');
		if (E) head_h += E.offsetHeight;
		else {
			var E = document.getElementById('edost_country');
			if (E && E.type != 'hidden') head_h += E.offsetHeight;
		}

		var E = document.getElementById('edost_maincity_div');
		if (E) {
			E.style.display = (fullscreen ? 'none' : 'block');
			if (!fullscreen) {
				var h = browser_h - head_h;
				var E2 = document.getElementById('edost_city_hint');
				if (E2) h -= E2.offsetHeight;

				E.style.paddingTop = '0';
				var h2 = E.offsetHeight;
				if (h2 > 0) E.style.paddingTop = Math.round((h - h2 - 10)*0.5) + 'px';
			}
		}

		var E = document.getElementById('edost_city_suggest_div');
		if (!E) return;

		E.style.width = city_w + 'px';
		E.style.height = 'auto';
		E.style.overflowY = 'visible';
		var h = browser_h - head_h;
		if (E.offsetHeight > h) {
			E.style.height = (h - 25) + 'px';
			E.style.overflowY = 'auto';
		}

	}

	// отдельное окно для ввода города (для маленького экрана и города в шапке сайта)
	this.window = function(param) {
//		alert(param + ' | ' + self.header);

		var E = document.getElementById('edost_location_header_div');
		if (E) self.header = true;

		var E = document.getElementById('edost_location_city_template_div');
		if (E) self.mode = 'template';

		if (self.device != 'phone' && !self.header && self.mode != 'template') return;

		var hide = (param == 'close' ? true : false);

		if (param == 'loading') edost.backup('location');
		if (param == 'back') {
			edost.backup('location', true);
			if (self.mode == 'template') edost_SetTemplateLocation('back');
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
			document.onkeydown = new Function('event', 'if (event.keyCode == 27) window.edost_location.window("back");');
		}

		var E = document.getElementById('edost_location_city_window');
		if (!E) {
			if (hide) return;

			var E = document.body;

			var E2 = document.createElement('DIV');
			E2.className = 'edost_location_city_window_fon';
			E2.id = 'edost_location_city_window_fon';
			E2.style.display = 'none';
			E2.onclick = new Function('', 'window.edost_location.window("back")');
			E.appendChild(E2);

			var E2 = document.createElement('DIV');
			E2.className = 'edost_location_city_window';
			E2.id = 'edost_location_city_window';
			E2.style.display = 'none';

			var s = '';
			if (self.device != 'phone') s += '<div class="edost_location_city_window_close" onclick="window.edost_location.window(\'back\');"></div>';
			s += '<div id="edost_location_city_window_head" style="font-size: 22px; color: #AAA; padding-bottom: 4px;">Выбор местоположения доставки</div><div id="edost_location_city_window_data"></div><div id="edost_location_city_window_suggest"></div>';
			if (self.device == 'phone') s += '<div class="edost_button_info" style="margin: 40px auto 0 auto;" onclick="edost_location.window(\'back\')">отмена</div>';
			E2.innerHTML = s;

			E.appendChild(E2);
		}

		var display = (hide ? 'none' : 'block');

		if (hide && timer != undefined) window.clearInterval(timer);

		var E = document.getElementById('edost_location_city_window');
		if (!E || hide && E.style.display == 'none') return;
		E.style.display = display;

		var E = document.getElementById('edost_location_city_window_fon');
		if (E) E.style.display = display;

		var E = document.getElementById('edost_location_city_window_data');

		if (param == 'back') {
			E.innerHTML = '';
			return;
		}

		if (param == 'loading') {
			if (self.mode == 'template') edost_SetTemplateLocation('loading');
			E.innerHTML = loading_big;
			self.resize();
			return;
		}

		var E2 = document.getElementById('edost_location_city_div');
		if (!E2) return;

		if (param == 'close') {
			if (self.header) {
				if (self.mode == 'template') edost_SetTemplateLocation('set');

				E2.style.display = 'none';
				var E2 = document.getElementById('edost_location_header_data');
				E2.innerHTML = E.innerHTML;
				E.innerHTML = '';
				edost_SetLoading('edost_location_city_loading', 'submit');
			}
			else {
				if (self.mode == 'template') edost_SetTemplateLocation('set');
				var E3 = document.getElementById('edost_country');
				var i = (E3 ? E3.selectedIndex : 0);
				E2.innerHTML = E.innerHTML;
				E.innerHTML = '';
				var E3 = document.getElementById('edost_country');
				if (E3) E3.selectedIndex = i;
			}
		}
		else {
			if (self.header) var E2 = document.getElementById('edost_location_header_data');

			E.innerHTML = E2.innerHTML;
			E2.innerHTML = '';

			var E = document.getElementById('edost_city');
			if (E && E.type != 'hidden') E.focus();

			self.resize();
			timer = window.setInterval("window.edost_location.resize()", 200);
		}

	}


	// отключение скрипта
	this.disable = function() {
		ajax_id++;
		disable = true;
		self.loading = false;
		for (k in self.load) {
			var load = self.load[k];
			load.suggest_now = -1;
			load.loading = false;
			if (load.suggest_timer != undefined) window.clearInterval(load.suggest_timer);
			var E = document.getElementById('edost_' + k + '_suggest_div');
			if (E) E.style.display = 'none';
		}
	}


	// поиск индекса загруженного списка
	this.index = function(name, s, write) {

		var load = self.load[name];
		var n = s.length - load.start;
		if (n >= load.data.length) n = load.data.length - 1;

		if (write) return n;

		for (var i = n; i >= 0; i--)
			if (load.data[i] && load.data[i].name !== undefined && (load.data[i].name === '' || s.indexOf(load.data[i].name) == 0) && (i == n || load.data[i].full))
				if (!load.location || self.compare_city(load.data[i].location, load.location))
					if (name != 'zip' || load.data[i].location[3] == load.location[3]) return i;

		return -1;

	}


	// конвертирование английской раскладки в русскую
	this.ru = function(s) {
		var r = '';
		for (var i = 0; i < s.length; i++) r += (key_map[s[i]] != undefined ? key_map[s[i]] : s[i]);
		return r;
	}


	// сравнение населенных пунктов
	this.compare_city = function(s1, s2) {
		return (s1[0] == s2[0] && s1[1] == s2[1] && s1[2] == s2[2] ? true : false);
	}


	// распаковка элементов массива второго уровня из строки
	this.parse_array = function(ar) {
		r = [];
		for (var i = 0; i < ar.length; i++) r.push( ar[i].split('|') );
		return r;
	}


	// запись выбранного местоположения в куки
	this.set_cookie = function(id, zip, zip_full, city2) {

		if (id == undefined) {
			var E = document.getElementById('edost_shop_LOCATION');
			if (!E) return;
			id = E.value;
		}
		if (zip == undefined) {
			var E = document.getElementById('edost_shop_ZIP');
			zip = (E ? E.value : '');
		}
		if (zip_full == undefined) {
			var E = document.getElementById('edost_zip_full');
			zip_full = (E && E.value == 'Y' ? true : false);
		}
		if (city2 == undefined) {
			var E = document.getElementById('edost_city2');
			city2 = (E ? E.value : '');
		}

		document.cookie = 'edost_location=' + id + '|' + encodeURIComponent(zip) + (zip != '' && !zip_full ? '.' : '') + '|' + encodeURIComponent(city2) + '; path=/; expires=Thu, 01-Jan-2050 00:00:01 GMT';

	}


	// установка метки 'город с районами'
	this.set_area = function(value, load) {
		var E = document.getElementById('edost_area');
		if (E) E.value = (value ? 'Y' : '');
		if (load) self.load['street'].area = value;
	}


	// установка значения
	this.set_value = function(id, value) {
//		alert(id + ' - ' + value);

		var name = id.split('_')[1];
		var load = self.load[name];

		if (value == undefined && load.suggest === false) return;

		var ar = (value == undefined ? load.suggest : value.split('|'));
		var value_string = ar.shift();
		load.value = value = ar;
		load.suggest_now = -1;
		load.string = value[0];

		if (name == 'city') {
/*
Екатеринбург,	 66
д. Осипово,		 50,	0,		0
с. Екатериновка, 25,	0,		7
Минск,			 5,		,		234
Минская область, ,		,		5
*/
			if (value[2] == undefined) value[2] = 0;
			else if (value[2] === '') value[1] = '';

			self.location([value[0], value[1], value[2], '', '']);
			edost_SetLocation(load.index != -1 && load.data[load.index].original && value[3] ? value[3] : '', true, value[0], value[1], value[2], true, value_string);
		}
		else if (name == 'street') {
/*
проезд 1-й,				  600016,  район Заводской, г. Артем
туп. 1-й Усадебный,		  600014,
проезд 1-й Коллективный,  ,
*/
//			load.string_start = value[0];
//			load.suggest_set = false;
			self.location(['', '', '', value[0], value[1]]);

			// внесение индекса улицы в предзагрузку
			if (value[1] != '') self.set_zip_load(value[1]);
/*
			self.load['zip'].data[0] = {
				'name': '',
				'full': true,
				'location': self.location(),
				'data': [[value[1]]]
			};
*/
			// улица принадлежит отдаленному району города
			if (value[2]) {
				edost_SetZip(value[1], true);
				edost_SetLocation('', true, value[2], load.location[1], load.location[2], false, value_string);
				return;
			}

			if (edost_SetZip(value[1])) return;
		}
		else if (name == 'zip') {
			edost_SetZip(value[0]);
		}
		else {
			var E = document.getElementById(id);
			if (E) E.value = value;
		}

	}


	// чтение/запись текущего местоположения ('value' == undefined - чтение)
	this.location = function(value) {
//		if (value) alert(value);

		var E_city = document.getElementById('edost_city');
		var E_region = document.getElementById('edost_region');
		var E_country = document.getElementById('edost_country');
		var E_street = document.getElementById('edost_street');
		var E_zip = document.getElementById('edost_zip');

		if (value == undefined) return [E_city ? E_city.value : '', E_region ? E_region.value : '', E_country ? E_country.value : false, E_street ? E_street.value : '', E_zip ? E_zip.value : ''];

		if (value[2] !== '') {
			E_city.value = value[0];
			E_region.value = value[1];
//			E_country.value = value[2];
			if (E_country.tagName != 'SELECT') E_country.value = value[2];

			self.load['city'].string = 'none';

			self.set_area(false, true);
		}
		else if (value[0] != '' && value[1] == '') E_city.value = value[0];

		if (E_street) {
			E_street.value = value[3];
			if (value[3]) self.set_area(false);
		}

		if (E_zip) {
			E_zip.value = value[4];
			edost_SetZipWarning('reset');
		}

	}


	// внесение индекса в предзагрузку
	this.set_zip_load = function(value) {

		if (value == undefined) {
			var E = document.getElementById('edost_zip_full');
			if (E && E.value == 'Y') {
				var E = document.getElementById('edost_shop_ZIP');
				if (E && E.value != '') value = E.value;
			}
		}

		if (value == undefined) return;

		self.load['zip'].data[0] = {
			'name': '',
			'full': true,
			'location': self.location(),
			'data': [[value]]
		};

	}


	// обработка результата ajax запроса
	this.ajax = function(name, value, result) {
//		alert(name + ' / ' + value + ' / ' + result);

		if (disable) return;

		name = name.split('|');
		if (name[1] != ajax_id) return;
		var param = (name[2] != undefined ? name[2] : '');
		name = name[0];

		var load = self.load[name];
		var index = self.index(name, value, true);
		var result = (window.JSON && window.JSON.parse ? JSON.parse(result) : eval('(' + result + ')'));

		if (name != 'zip' && result.error) self.error = result.error;

		if (param == 'check_zip') {
			var warning = (result.warning ? result.warning : '');

//			load.string_start = value;
			load.cache_id++;
 			if (load.cache_id >= 10) load.cache_id = 0;
			load.cache[load.cache_id] = {"value": value, "location": load.location, "result": warning};

			if (warning != '') edost_SetZipWarning(warning);
			else if (edost_SetZip(value)) return;
		}
		else {
			load.data[index] = {"name": value, "full": (result.full ? true : false), "data": self.parse_array(result.data), "location": load.location, "original": (result.original ? true : false)};
			if (result.region) load.data[index].region = self.parse_array(result.region);
			if (name == 'street') {
				self.set_area(result.area ? true : false, true);
				load.warning_disable = (value == '' && load.data[index].full && load.data[index].data.length == 0 ? true : false);
			}
			if (name == 'zip' && load.data[index].data.length == 0) self.set_zip_load();
		}

		load.loading = self.loading = false;
		self.suggest(name, 'redraw');

	}


	// проверка страны на доступность подсказок через сервис eDost
	this.edost_suggest = function(country) {
		country = (country !== false ? country.split('_')[0] : false);
		var E = document.getElementById('edost_suggest_country');
		var ar = (E ? E.value.split(',') : [0]);
		for (var i = 0; i < ar.length; i++) if (ar[i] === country) return true;
		return false;
	}


	// подсказки
	this.suggest = function(id, param) {

		var name = id.split('_');
		if (name[1] != undefined) name = name[1];
		else {
			name = id;
			id = 'edost_' + id;
		}

		var load = self.load[name];
		if (!load) return;

		var E = document.getElementById(id);
		if (!E) return;

		var value = E.value;
		var text_width = E.offsetWidth;

		if (param == 'start') {
//			develop += name + '-' + param + '; ';
			self.disable();
			disable = false;
//			load.loading = self.loading = false;
			load.suggest_now = 1;
			load.suggest_set = true;
			load.string = load.string_start = value;

			if (name == 'city' || name == 'street' || name == 'zip') self.suggest(id, 'redraw');

			if (load.suggest_timer != undefined) window.clearInterval(load.suggest_timer);
			load.suggest_timer = window.setInterval("edost_location.suggest('" + id + "')", 100);

			return;
		}

		if (disable) return;

		if (param == 'hide') {
			if (disable || load.suggest_timer == undefined) return;

			ajax_id++;

			window.clearInterval(load.suggest_timer);
			load.suggest_now = -1;
			window.setTimeout("window.edost_location.suggest('" + id + "', 'hide2')", 100);

			return;
		}

//zip-start; zip-hide;  street-start; zip-hide2; street-hide; zip-start;
//zip-start; zip-hide;  street-start; zip-hide2; street-hide; street-hide2; zip-start;

		if (param == 'hide2') {
			if (disable || load.suggest_now != -1) return;

			if (name == 'zip' && value.replace(/[^0-9]/g, '').length != 6 && self.edost_suggest(self.location()[2])) edost_SetZipWarning('format');

			var E = document.getElementById(id + '_suggest_div');
			if (E) E.style.display = 'none';

			return;
		}


		if (self.loading_data == 2 && loading_data_last != 2) load.suggest_now = 1;
		else {
			if (value == load.string && param != 'redraw') return;
			if (value != load.string) {
				load.suggest_now = 1;
				load.suggest_set = true;
			}
		}
		loading_data_last = self.loading_data;
		load.string = value;

		if (name == 'street' && load.area && value != load.string_start && load.string_start != '') {
			load.string_start = '';
			self.set_area(true);
		}

		if (load.suggest_now == -1 && param != 'redraw') return;


		var full = true;
		var bold_count = 0;
		var preload = [0];
		var suggest = [], suggest2 = [];
		var region = [];
		var value_original = self.trim(value);
		var value_original2 = value_original.toLowerCase();
		value = value.toLowerCase().replace(/[ё]/g, 'е');
		if (value.length > 0 && (value.replace(/[^a-z]/g, '').length > 0 || value.replace(/[^\[\];',\.]/g, '').length == value.length)) value = self.ru(value); //'

		value = value.replace(/[^а-я0-9.,-]/g, ' '); // удаление недопустимых символов
		var value_full = value = self.trim(value, true);

		// удаление префиксов (ул, д, пос, ...)
		if (load.ignore && value.length > 1) {
			value = ' ' + value.replace(/ /g, '  ').replace(/,/g, ', ').replace(/\./g, '. ') + ' ';
			for (var i = 0; i < load.ignore.length; i++) value = value.replace(new RegExp(' ' + load.ignore[i] + '[ .]', 'g'), ' ');
			value = self.trim(value, true);
		}

		var value_length = value.length;

		// разбивка фразы на слова
		var values = [];
		var ar = self.trim(value.replace(/[,.]/g, ' '), true).split(' ');
		for (var i = 0; i < ar.length; i++) values.push((values.length > 0 ? ' ' : '') + ar[i]);
		var values_length = values.length;

		var location = self.location();
		if (name == 'city') { location[0] = ''; location[1] = ''; }
		load.location = location;

		var edost_suggest = self.edost_suggest(location[2]);
		load.start = (name == 'city' && edost_suggest ? 3 : 0); // количество символов с которого загружаются подсказки

		if ((name == 'city' || name == 'metro') && self.loading_data == 0) edost_location.add_data();

		if (name == 'zip' && !edost_suggest) edost_SetZip(value_original, 'original');

		// поиск метро
		if (name == 'metro' && self.loading_data == 2)
			for (var i = 0; i < edost_location_data.metro.length; i++) if (location[0] == edost_location_data.metro[i][0])
				for (var i2 = 1; i2 < edost_location_data.metro[i].length; i2++) {
					var p = edost_location_data.metro[i][i2].toLowerCase().indexOf(value);
					if (p == 0 && value_length <= 2 || p >= 0 && value_length > 2) suggest.push([edost_location_data.metro[i][i2]]);
				}

		// поиск сокращений (мск, спб, ...)
		if (load.short) for (var i = 0; i < load.short.length; i++) if (value == load.short[i][0]) suggest.push([load.short[i][1], load.short[i][2]]);

		// поиск городов и населенных пунктов по списку предзагрузки
		if (name == 'city' && edost_suggest && self.loading_data == 2 && value_full.length > 0) {
			var p1 = false;
			var p2 = false;
			var s1 = value.substr(0, 1);
			var s2 = value.substr(1, 1);
			for (var i = 0; i < edost_location_data.sign.length; i++) {
				if (s1 == edost_location_data.sign[i]) p1 = i;
				if (s2 == edost_location_data.sign[i]) p2 = i;
			}

			if (p1 !== false) {
				if (p2 !== false) preload = edost_location_data.load[p1][p2];
				else for (var i = 0; i < edost_location_data.load[p1].length; i++) if (edost_location_data.load[p1][i].length > 1) { preload = edost_location_data.load[p1][i]; break; }

				if (value_length <= 2 && !preload[0]) full = false;

				// поиск городов
				var ar = [];
				for (var i = 0; i < edost_location_data.city.length; i++) {
					var s = edost_location_data.city[i][0].toLowerCase();
					var p = s.indexOf(value_full);
					var p2 = s.indexOf(value);
					if (p == 0 || p2 == 0) suggest.push(edost_location_data.city[i]);
					else if ((p > 0 || p2 > 0) && value_length > 2) {
						var a = true;
						if (load.ignore_inside) for (var i2 = 0; i2 < load.ignore_inside.length; i2++) if (value == load.ignore_inside[i2]) { a = false; break; }
						if (a) ar.push(edost_location_data.city[i]);
					}
				}
				suggest = suggest.concat(ar);
				bold_count = suggest.length;

				// поиск населенных пунктов
				if (value_length > 0 && (value_length <= 2 || preload[0] == 1 || values_length > 1)) {
//					if (preload[0] != 1) full = false;
					for (var i = 1; i < preload.length; i++) {
						if (value_length <= 2 && values_length == 1) {
							suggest2.push(preload[i]);
							continue;
						}

						var n = 0;
						var s = preload[i][0].toLowerCase();
						var p = s.indexOf(',');
						var s2 = ' ' + edost_location_data.region[0][ preload[i][1] ].toLowerCase();
						for (var i2 = 0; i2 < values_length; i2++) {
							var p2 = s.indexOf(values[i2]);
							if (p2 >= 0 && (i2 > 0 || p2 < p || p == -1) || i2 > 0 && s2.indexOf(values[i2]) >= 0) n++;
						}
						if (n >= values_length) suggest2.push(preload[i]);
					}
				}
			}
		}


		if (load.data != undefined) {
			// загрузка списка с сервера
			if (preload[0] == 0 && (!self.error && value_length >= load.start || !edost_suggest) && !self.loading && !disable) {
				var a = true;
				var v = (name == 'street' || name == 'zip' ? '' : value);

				if ((name == 'city' || name == 'metro') && self.loading_data != 2) a = false;
				else if ((name == 'street' || name == 'zip') && !edost_suggest) a = false;
				else if (name == 'zip') {
					if (!location[0] || !location[1] || !location[3]) a = false;
					if (location[0] && location[1] && !location[3]) self.set_zip_load();
				}
				else if (name == 'street') for (var i = 0; i < city_street.length; i++) if (location[0] == city_street[i]) {
					if (value_length == 0) a = false;
					else {
						// загрузка только по первым буквам
						var n = (value_length <= 3 ? value_length : 3);
						for (var i2 = 1; i2 <= n; i2++) {
							var v = value.substr(0, i2);
							var index = self.index(name, v);
							if (index < 0 || load.data[index].data && load.data[index].full) break;
						}
					}
					break;
				}

				if (a) {
					var i = self.index(name, v);
					if (i >= 0 && load.word && values_length > 1 && values[0].length > 2) {
						v = values[0];
						i = self.index(name, v);
					}
					if (i == -1) {
						self.loading = load.loading = true;
						v = v.substr(0, 14);
						var s = '&country=' + encodeURIComponent(location[2]);
						if (name == 'street' || name == 'zip') s += '&region=' + encodeURIComponent(location[1]) + '&city=' + encodeURIComponent(location[0]);
						if (name == 'zip') s += '&street=' + encodeURIComponent(location[3]);
						edost_LocationAjax(name + '|' + ajax_id, v, 'type=' + load.type + '&value=' + encodeURIComponent(v) + s);
					}
				}
			}

			// поиск по загруженному списку
			if (value_length >= load.start) {
				load.index = self.index(name, value);
				if (load.index >= 0 && load.data[load.index].data) {
					if (!load.data[load.index].full) full = false;
					if (load.data[load.index].region) region = load.data[load.index].region;

					if (name == 'zip') suggest2 = load.data[load.index].data;
					else for (var i = 0; i < load.data[load.index].data.length; i++) {
						var a = false;
						var s = load.data[load.index].data[i][0].toLowerCase().replace(/\.\s/g, '*').replace(/[.]/g, ' ').replace(/[*]/g, '. ');
						var p = s.indexOf(value);
						var p2 = s.indexOf(value_original2);
						if (edost_suggest && (p >= 0 || p2 >= 0) || !edost_suggest && ((p == 0 || p2 == 0) && value_length <= 2 || (p >= 0 || p2 >= 0) && value_length > 2)) {
							a = true;
							if (name == 'street') suggest.push(load.data[load.index].data[i]);
							else suggest2.push(load.data[load.index].data[i]);
						}
						if (!a && name == 'street' && values_length > 1) {
							s = ' ' + s;
							var n = 0;
							for (var i2 = 0; i2 < values_length; i2++) if (s.indexOf((i2 == 0 ? ' ' : '') + values[i2]) >= 0) {
								n++;
							}
							if (n == values_length) suggest.push(load.data[load.index].data[i]);
							else if (n > 0) suggest2.push(load.data[load.index].data[i]);
						}
					}
				}
			}

			// поиск по загруженному списку (для двух и более слов)
			if (load.word && values_length > 1 && values[0].length >= 2 && value_length > 2 && edost_suggest && self.loading_data == 2) {
				for (var i3 = 0; i3 <= 1; i3++) {
					if (i3 == 0) var i = self.index(name, values[0]); else i = load.index;
					var data = (i != -1 ? load.data[i] : false);
					if (data !== false) for (var i = 0; i < data.data.length; i++) {
						var n = 0;
						var v = data.data[i];
						var s = v[0].toLowerCase();
						var s2 = ' ' + edost_location_data.region[0][ v[1] ].toLowerCase();
						for (var i2 = 1; i2 < values_length; i2++) if (s.indexOf(values[i2]) > 5 || s2.indexOf(values[i2]) >= 0) n++;
						var a = (n >= values_length - 1 ? true : false);
						if (a) for (var i2 = 0; i2 < suggest2.length; i2++) if (suggest2[i2][0] == v[0] && suggest2[i2][1] == v[1]) { a = false; break; }
						if (a) suggest2.push(v);
					}
				}
			}

			// сортировка населенных пунктов по федеральным округам
			if (name == 'city' && edost_suggest && self.loading_data == 2) {
				var n = suggest2.length;
				if (n > 1) {
					var ar = [];
					for (var i = 0; i < edost_location_data.region_fed.length; i++)
						for (var i2 = 0; i2 < edost_location_data.region_fed[i].region.length; i2++)
							for (var i3 = 0; i3 < n; i3++) if (suggest2[i3][1] == edost_location_data.region_fed[i].region[i2]) ar.push([suggest2[i3][0], suggest2[i3][1], 0, i]);
					suggest2 = ar;
				}
			}
		}

		suggest = suggest.concat(suggest2);


		// проверка индекса
		if (name == 'zip' && edost_suggest) {
			if (value_length != value.replace(/[^0-9]/g, '').length) edost_SetZipWarning('digit');
			else if (value_length > 6) edost_SetZipWarning(1);
			else if (value_length < 6) edost_SetZipWarning('');
			else {
				var a = true;
				for (var i = 0; i < suggest.length; i++) if (suggest[i][0] == value) {
					a = false;
					if (edost_SetZip(suggest[i][0])) return;
					break;
				}
				for (var i = 0; i < load.cache.length; i++) if (load.cache[i].value == value && self.compare_city(load.cache[i].location, load.location)) {
					a = false;
					edost_SetZipWarning(load.cache[i].result);
					if (load.cache[i].result == '' && edost_SetZip(value)) return;
					break;
				}
				if (a && value != load.string_start && !self.error && !self.loading && !disable) {
					load.string_start = '';
					self.loading = true;
//					self.loading = load.loading = true;
					edost_SetZipWarning('checking');
					s = '&country=' + encodeURIComponent(location[2]) + '&region=' + encodeURIComponent(location[1]) + '&city=' + encodeURIComponent(location[0]);
					edost_LocationAjax(name + '|' + ajax_id + '|check_zip', value, 'type=check_zip&zip=' + value + s);
				}
			}
		}


		// вывод списка
		browser_width = 0;
		var hint_disable = false;
		var n = suggest.length;
//		if (name == 'street' && value == load.string_start && load.string_start != '') n = 0;
//		if (!load.suggest_set) n = 0;
		if (n > 15) {
			n = 15;
			full = false;
		}
		if (load.suggest_now < 1) load.suggest_now = 1;
		if (load.suggest_now > n) load.suggest_now = n;

		// автоматический выбор, если название введено полностью и в подсказках есть только один вариант выбора
		if (n == 1 && load.string == suggest[0][0] && (name == 'street' || name == 'zip' || name == 'metro')) {
			n = 0;
			hint_disable = true;
			if (name == 'street' && load.area) load.suggest = suggest[0];
		}

		// генерация списка подсказок
		var s = '';
		var fed = -1;
		var browser_w = (document.documentElement.clientWidth == 0 ? document.body.clientWidth : document.documentElement.clientWidth);
		load.suggest = false;
		for (var i = 0; i < n; i++) {
			var active = false;
			if (i == load.suggest_now - 1) active = true;

			// заголовок с названием федерального округа
			if (name == 'city' && edost_suggest && suggest[i][3] != undefined && suggest[i][3] != fed && self.loading_data == 2) {
				fed = suggest[i][3];
				s += '<div class="edost_suggest_head">' + edost_location_data.region_fed[fed].name + '</div>';
			}

			var s1 = suggest[i][0];
			var s2 = '';
			if (name == 'city' && suggest[i][1] != undefined && self.loading_data == 2) {
				if (edost_suggest) s2 = edost_location_data.region[0][ suggest[i][1] ];
				else for (var i2 = 0; i2 < region.length; i2++) if (region[i2][1] == suggest[i][1]) { s2 = region[i2][0]; break; }

				if (s1.indexOf(', г. ' + s2) > 0) s2 = '';
			}
			if (name == 'street' && suggest[i][2]) {
				var p = suggest[i][2].indexOf(', г. ');
				s2 = (p >= 0 ? suggest[i][2].substr(0, p) : suggest[i][2]);
			}
			if (s1 == s2) s2 = '';

			var ar = [s1 + (s2 != '' ? ' (' + s2 + ')' : '')];
			for (var i2 = 0; i2 < suggest[i].length; i2++) ar.push(suggest[i][i2]);
//			var ar = suggest[i];
//			ar.unshift(s1 + (s2 != '' ? ' (' + s2 + ')' : ''));

			s += '<div class="edost_suggest_value' + (active ? ' edost_suggest_active' : '') + (i < bold_count ? ' edost_suggest_bold' : '') + '" onmousedown="edost_location.set_value(\'' + id + '\', \'' + ar.join('|') + '\');">' + s1;
			if (s2 != '') s += (self.device == 'phone' && browser_w < 800 ? '<br><span>' + s2 + '</span>' : ' <span> (' + s2 + ')</span>');
			s += '</div>';

			if (active) load.suggest = ar;
		}
		s += (load.data && load.loading || self.loading_data != 2 && (name == 'city' || name == 'metro') ? loading_small : '');

		// подписи
		var hint = '';
		var warning = false;
		if (!hint_disable && self.loading_data == 2) {
//			if (n == 15 && (value_length == 1 || !full)) hint = 'продолжайте набирать название, чтобы увидеть больше вариантов';
			if (!full) {
				hint = 'продолжайте набирать название, чтобы увидеть больше вариантов...';
				if (value_length > 2 && load.hint) hint += '<br>' + load.hint;
			}
			if (value_length > 0 && n == 0 && !load.loading && !load.warning_disable) {
				if (edost_suggest) {
					if (load.warning) hint = load.warning;
				}
				else if (name == 'city') hint = 'не найдено ни одного подходящего города' + (region.length > 0 ? '<br>если вашего города нет в списке, тогда впишите и выберите ваш регион' : '');
				warning = true;
			}
		}

		var E_suggest = document.getElementById(id + '_suggest_div');
		if (!E_suggest) return;

//		if (!disable && (hint != '' || name == 'zip' && n > 0 || s != '' && value_length > 0 && (n > 0 || load.loading))) {
		if (!disable && (hint != '' || name == 'zip' && n > 0 || s != '' && (value_length > 0 && (n > 0 || self.loading_data != 2) || load.loading))) {
//			E_suggest.style.minWidth = text_width + 'px';
			E_suggest.style.width = text_width + 'px';
			E_suggest.style.display = 'block';

			var E_data = document.getElementById(id + '_suggest_data');
			if (!E_data) {
				var E2 = document.createElement('DIV');
				E2.className = 'edost_suggest_data';
				E2.id = id + '_suggest_data';
				E_suggest.appendChild(E2);
				E_data = document.getElementById(id + '_suggest_data');
			}

			var E_hint = document.getElementById(id + '_suggest_hint');
			if (!E_hint) {
				var E2 = document.createElement('DIV');
//				E2.className = 'edost_suggest_hint';
				E2.id = id + '_suggest_hint';
				E_suggest.appendChild(E2);
				E_hint = document.getElementById(id + '_suggest_hint');
			}

			E_data.style.display = (!warning ? 'block' : 'none');
			E_data.innerHTML = s;

			self.resize();

			E_hint.innerHTML = hint;
			E_hint.className = (warning ? 'edost_suggest_warning' : 'edost_suggest_hint');
			E_hint.style.display = (hint != '' ? 'block' : 'none');
		}
		else E_suggest.style.display = 'none';

	}


	// обработка нажатий
	this.keydown = function(id, event) {

		var name = id.split('_')[1];
		var load = self.load[name];
		var redraw = false;

		if (event.keyCode == 38 || event.keyCode == 13)
			if (event.preventDefault) event.preventDefault(); else event.returnValue = false;

		if (event.keyCode == 38) {
			load.suggest_now--;
			redraw = true;
		}
		if (event.keyCode == 40) {
			load.suggest_now++;
			redraw = true;
		}

		if (event.keyCode == 13) {
			self.set_value(id);
			load.suggest_set = false;
			redraw = true;

			if (name = 'street') {
				var E = document.getElementById('edost_street');
				if (E) {
					E.blur();
					return;
				}
			}

		}

		if (redraw) self.suggest(id, 'redraw');

	};

}

edost_location.get_device();
