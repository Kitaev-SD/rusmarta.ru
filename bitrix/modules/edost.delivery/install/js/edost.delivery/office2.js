var edost_office_create = function(name) {
	var self = this;
	var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://')
	var geo = false, format, param_profile, onkeydown_backup = 'free', scroll_backup = false, onclose = '', map_loading = false, api21 = false, browser_width = 0, browser_height = 0
	var address_data = [], address_draw = false, address_count = [0, 0], address_draw_html = false, address_limit = true, address_search = [], address_search_now = -1, address_now = -1, address_width = 0, address_height = 0, address_balloon = false, address_scroll_reset = false
	var search_value = '', search_value_code = '', search_values = [], search_value_original = '', search_value_min = 5, metro_near_disable = false, metro_near = false, metro_far = false, distance_near_max = 550, ico_path = '', balloon_draw, resize_update = 0, resize_count = 0, cod_info = false
	var param_start, onclose_set_office_start, update_map = false, office_active = 'all', address_filter = false, address_filter_gps = false, address_filter_value = '', address_filter_mode = '', metro_key = -1, point_active_geo = false, point_active = false
	var resize_start = false, search_start = false
	var balloon_width = 0, balloon_map_active = false, show_price = true, show_price_address = true, address_get_data = [], city = '', yandex_api_key = '', balloon_main = false, near_hide = false, mobile_jump = false
	var delimiter_address = '<div class="edost_office_address_delimiter"></div>'
	var delimiter_address2 = '<div class="edost_office_address_delimiter2"></div>'
	var delimiter_balloon = '<div class="edost_office_balloon_delimiter"></div>'
	var delimiter_balloon2 = '<div class="edost_office_balloon_delimiter2"></div>'
	var free_ico = 'Бесплатно!', near_show = 'показать ближайшие', near_search = 'найти ближайшие', office_show = 'все тарифы'
	var key_map = {"q":"й","w":"ц","e":"у","r":"к","t":"е","y":"н","u":"г","i":"ш","o":"щ","p":"з","[":"х","]":"ъ","a":"ф","s":"ы","d":"в","f":"а","g":"п","h":"р","j":"о","k":"л","l":"д",";":"ж","\'":"э","z":"я","x":"ч","c":"с","v":"м","b":"и","n":"т","m":"ь",",":"б",".":"ю","/":"."}
	var ignore = ['б-р','пр-кт','мк-рн','микрорайон','у','ул','улица','туп','тупик','ал','пр','пр-зд','просека','автодорога','пл','массив','кв-л','квартал','тракт','ряды','д','просп','проспект','ш','шоссе','бульвар']
	var metro_data = [['Москва', ['Новокосино',55.7451,37.8641,0],['Новогиреево',55.7522,37.8146,0],['Перово',55.751,37.7842,0],['Шоссе Энтузиастов',55.7581,37.7517,0],['Авиамоторная',55.7519,37.7174,0],['Площадь Ильича',55.7471,37.6807,0],['Марксистская',55.7407,37.656,0],['Третьяковская',55.7411,37.6261,0],['Петровский парк',55.7923,37.5595,0],['ЦСКА',55.7864,37.535,0],['Хорошевская',55.7764,37.5198,0],['Шелепиха',55.7572,37.5257,0],['Деловой центр',55.7491,37.5395,0],['Парк Победы',55.7365,37.5144,0],['Минская',55.7232,37.5038,0],['Ломоносовский проспект',55.7055,37.5225,0],['Раменки',55.6961,37.505,0],['Мичуринский проспект',55.6888,37.485,0],['Озерная',55.6698,37.4495,0],['Говорово ',55.6588,37.4174,0],['Солнцево',55.649,37.3911,0],['Боровское шоссе',55.647,37.3701,0],['Новопеределкино',55.6385,37.3544,0],['Рассказовка',55.6324,37.3328,0],['Ховрино',55.8777,37.4877,1],['Беломорская',55.8651,37.4764,1],['Речной вокзал',55.8542,37.4767,1],['Водный стадион',55.839,37.4875,1],['Войковская',55.8189,37.4978,1],['Сокол',55.8056,37.5152,1],['Аэропорт',55.8004,37.5305,1],['Динамо',55.7897,37.5582,1],['Белорусская',55.7774,37.5821,1],['Маяковская',55.7698,37.5962,1],['Тверская',55.7653,37.6039,1],['Театральная',55.7588,37.6177,1],['Новокузнецкая',55.7424,37.6293,1],['Павелецкая',55.7297,37.6387,1],['Автозаводская',55.7066,37.657,1],['Технопарк',55.695,37.6642,1],['Коломенская',55.6774,37.6637,1],['Каширская',55.6557,37.6497,1],['Кантемировская',55.6361,37.6562,1],['Царицыно',55.621,37.6696,1],['Орехово',55.6127,37.6952,1],['Домодедовская',55.6101,37.7171,1],['Красногвардейская',55.6141,37.7427,1],['Алма-Атинская',55.6335,37.7657,1],['Медведково',55.8881,37.6616,2],['Бабушкинская',55.8706,37.6643,2],['Свиблово',55.8556,37.6534,2],['Ботанический сад',55.8446,37.6378,2],['ВДНХ',55.8196,37.6408,2],['Алексеевская',55.8078,37.6387,2],['Рижская',55.7925,37.6361,2],['Проспект Мира',55.7818,37.6332,2],['Сухаревская',55.7723,37.6329,2],['Тургеневская',55.7654,37.6367,2],['Китай-город',55.7565,37.6313,2],['Третьяковская',55.7407,37.6256,2],['Октябрьская',55.7312,37.6129,2],['Шаболовская',55.7188,37.6079,2],['Ленинский проспект',55.7068,37.585,2],['Академическая',55.6871,37.5723,2],['Профсоюзная',55.6777,37.5626,2],['Новые Черемушки',55.6701,37.5545,2],['Калужская',55.6567,37.5401,2],['Беляево',55.6424,37.5261,2],['Коньково',55.6319,37.5192,2],['Теплый Стан',55.6187,37.5059,2],['Ясенево',55.6062,37.5334,2],['Новоясеневская',55.6019,37.553,2],['Бульвар Рокоссовского',55.8149,37.7322,3],['Черкизовская',55.8028,37.7449,3],['Преображенская площадь',55.7963,37.7136,3],['Сокольники',55.7893,37.6799,3],['Красносельская',55.78,37.6661,3],['Комсомольская',55.7741,37.6546,3],['Красные ворота',55.7683,37.6478,3],['Чистые пруды',55.765,37.6383,3],['Лубянка',55.7599,37.6253,3],['Охотный ряд',55.7572,37.6151,3],['Библиотека им.Ленина',55.7521,37.6104,3],['Кропоткинская',55.7453,37.6042,3],['Парк культуры',55.7362,37.595,3],['Фрунзенская',55.7275,37.5802,3],['Спортивная',55.7224,37.562,3],['Воробьевы горы',55.7092,37.5573,3],['Университет',55.6933,37.5345,3],['Проспект Вернадского',55.6765,37.5046,3],['Юго-Западная',55.6631,37.4829,3],['Тропарево',55.6459,37.4725,3],['Румянцево',55.633,37.4419,3],['Саларьево',55.6227,37.424,3],['Филатов Луг',55.601,37.4082,3],['Прокшино',55.5864,37.4335,3],['Ольховая',55.5692,37.4588,3],['Коммунарка',55.5599,37.4691,3],['Щелковская',55.81,37.7983,4],['Первомайская',55.7944,37.7994,4],['Измайловская',55.7877,37.7799,4],['Партизанская',55.7884,37.7488,4],['Семеновская',55.7831,37.7193,4],['Электрозаводская',55.7821,37.7053,4],['Бауманская',55.7724,37.679,4],['Площадь Революции',55.7567,37.6224,4],['Курская',55.7586,37.659,4],['Арбатская',55.7523,37.6035,4],['Смоленская',55.7477,37.5838,4],['Киевская',55.7431,37.5641,4],['Парк Победы',55.7357,37.5169,4],['Славянский бульвар',55.7295,37.471,4],['Кунцевская',55.7306,37.4451,4],['Молодежная',55.7414,37.4156,4],['Крылатское',55.7568,37.4081,4],['Строгино',55.8038,37.4024,4],['Мякинино',55.8233,37.3852,4],['Волоколамская',55.8352,37.3825,4],['Митино',55.8461,37.3612,4],['Пятницкое шоссе',55.8536,37.3531,4],['Кунцевская',55.7308,37.4468,5],['Пионерская',55.736,37.4667,5],['Филевский парк',55.7397,37.4839,5],['Багратионовская',55.7435,37.497,5],['Фили',55.7468,37.514,5],['Кутузовская',55.7405,37.5341,5],['Студенческая',55.7388,37.5484,5],['Киевская',55.7432,37.5654,5],['Смоленская',55.7491,37.5822,5],['Арбатская',55.7521,37.6016,5],['Александровский сад',55.7523,37.6088,5],['Выставочная',55.7502,37.5426,5],['Международная',55.7483,37.5333,5],['Алтуфьево',55.899,37.5865,6],['Бибирево',55.8839,37.603,6],['Отрадное',55.8643,37.6051,6],['Владыкино',55.8482,37.5905,6],['Петровско-Разумовская',55.8366,37.5755,6],['Тимирязевская',55.8187,37.5745,6],['Дмитровская',55.8081,37.5817,6],['Савеловская',55.7941,37.5872,6],['Менделеевская',55.782,37.5991,6],['Цветной бульвар',55.7717,37.6205,6],['Чеховская',55.7657,37.6085,6],['Боровицкая',55.7504,37.6093,6],['Полянка',55.7368,37.6186,6],['Серпуховская',55.7265,37.6248,6],['Тульская',55.7096,37.6226,6],['Нагатинская',55.6821,37.6209,6],['Нагорная',55.673,37.6104,6],['Нахимовский проспект',55.6624,37.6053,6],['Севастопольская',55.6515,37.5981,6],['Чертановская',55.6405,37.6061,6],['Южная',55.6224,37.609,6],['Пражская',55.611,37.6024,6],['Улица Академика Янгеля',55.5968,37.6015,6],['Аннино',55.5835,37.597,6],['Бульвар Дмитрия Донского',55.5682,37.5769,6],['Планерная',55.8597,37.4368,7],['Сходненская',55.8493,37.4408,7],['Тушинская',55.8255,37.437,7],['Спартак',55.8182,37.4352,7],['Щукинская',55.8094,37.4632,7],['Октябрьское поле',55.7936,37.4933,7],['Полежаевская',55.7772,37.5179,7],['Беговая',55.7735,37.5455,7],['Улица 1905 года',55.7639,37.5623,7],['Баррикадная',55.7608,37.5812,7],['Пушкинская',55.7656,37.6044,7],['Кузнецкий мост',55.7615,37.6244,7],['Китай-город',55.7544,37.6339,7],['Таганская',55.7395,37.6536,7],['Пролетарская',55.7315,37.6669,7],['Волгоградский проспект',55.7255,37.6852,7],['Текстильщики',55.7092,37.7321,7],['Кузьминки',55.7055,37.7633,7],['Рязанский проспект',55.7161,37.7927,7],['Выхино',55.716,37.8168,7],['Лермонтовский проспект',55.702,37.851,7],['Жулебино',55.6847,37.8558,7],['Котельники',55.6743,37.8582,7],['Новослободская',55.7796,37.6013,8],['Проспект Мира',55.7796,37.6336,8],['Комсомольская',55.7757,37.6548,8],['Курская',55.7586,37.6611,8],['Таганская',55.7424,37.6533,8],['Павелецкая',55.7314,37.6363,8],['Добрынинская',55.729,37.6225,8],['Октябрьская',55.7293,37.611,8],['Парк культуры',55.7352,37.5931,8],['Киевская',55.7436,37.5674,8],['Краснопресненская',55.7604,37.5771,8],['Белорусская',55.7752,37.5823,8],['Селигерская',55.8648,37.5501,9],['Верхние Лихоборы',55.8557,37.5628,9],['Окружная',55.8489,37.5711,9],['Петровско-Разумовская',55.8367,37.5756,9],['Фонвизинская',55.8228,37.5881,9],['Бутырская ',55.8133,37.6028,9],['Марьина Роща',55.7937,37.6162,9],['Достоевская',55.7817,37.6139,9],['Трубная',55.7677,37.6219,9],['Сретенский бульвар',55.7661,37.6357,9],['Чкаловская',55.756,37.6593,9],['Римская',55.747,37.68,9],['Крестьянская застава',55.7323,37.6653,9],['Дубровка',55.7181,37.6763,9],['Кожуховская',55.7062,37.6854,9],['Печатники',55.6929,37.7283,9],['Волжская',55.6904,37.7543,9],['Люблино',55.6766,37.7616,9],['Братиславская',55.6588,37.7484,9],['Марьино',55.6492,37.7438,9],['Борисово',55.6325,37.7433,9],['Шипиловская',55.6217,37.7436,9],['Зябликово',55.6119,37.7453,9],['Каширская',55.6543,37.6477,10],['Варшавская',55.6533,37.6195,10],['Каховская',55.6529,37.5966,10],['Бунинская аллея',55.538,37.5159,11],['Улица Горчакова',55.5423,37.5321,11],['Бульвар Адмирала Ушакова',55.5452,37.5423,11],['Улица Скобелевская',55.5481,37.5527,11],['Улица Старокачаловская',55.5692,37.5761,11],['Лесопарковая',55.5817,37.5778,11],['Битцевский Парк',55.6001,37.5561,11],['Окружная',55.8489,37.5711,12],['Владыкино',55.8472,37.5919,12],['Ботанический сад',55.8456,37.6403,12],['Ростокино',55.8394,37.6678,12],['Белокаменная',55.83,37.7006,12],['Бульвар Рокоссовского',55.8172,37.7369,12],['Локомотив',55.8032,37.7457,12],['Измайлово',55.7886,37.7428,12],['Соколиная Гора',55.77,37.7453,12],['Шоссе Энтузиастов',55.7586,37.7485,12],['Андроновка',55.7411,37.7344,12],['Нижегородская',55.7322,37.7283,12],['Новохохловская',55.7239,37.7161,12],['Угрешская',55.7183,37.6978,12],['Дубровка',55.7127,37.6778,12],['Автозаводская',55.7063,37.6631,12],['ЗИЛ',55.6983,37.6483,12],['Верхние Котлы',55.69,37.6189,12],['Крымская',55.69,37.605,12],['Площадь Гагарина',55.7069,37.5858,12],['Лужники',55.7203,37.5631,12],['Кутузовская',55.7408,37.5333,12],['Деловой центр',55.7472,37.5322,12],['Шелепиха',55.7575,37.5256,12],['Хорошево',55.7772,37.5072,12],['Зорге',55.7878,37.5044,12],['Панфиловская',55.7992,37.4989,12],['Стрешнево',55.8136,37.4869,12],['Балтийская',55.8258,37.4961,12],['Коптево',55.8396,37.52,12],['Лихоборы',55.8472,37.5514,12],['Тимирязевская',55.819,37.5789,13],['Улица Милашенкова',55.8219,37.5912,13],['Телецентр',55.8218,37.609,13],['Улица Академика Королева',55.8218,37.6272,13],['Выставочный центр',55.8241,37.6385,13],['Улица Сергея Эйзенштейна',55.8293,37.645,13],['Петровский парк',55.7923,37.5595,14],['ЦСКА',55.7864,37.535,14],['Хорошевская',55.7764,37.5198,14],['Шелепиха',55.7572,37.5257,14],['Деловой центр',55.7491,37.5395,14],['Косино',55.7033,37.8511,15],['Улица Дмитриевского',55.71,37.879,15],['Лухмановская',55.7083,37.9004,15],['Некрасовка',55.7029,37.9264,15]],['Санкт-Петербург', ['Девяткино',60.0502,30.443,16],['Гражданский проспект',60.035,30.4182,16],['Академическая',60.0128,30.396,16],['Политехническая',60.0089,30.3709,16],['Площадь Мужества',59.9998,30.3662,16],['Лесная',59.9849,30.3443,16],['Выборгская',59.9709,30.3474,16],['Площадь Ленина',59.9556,30.3561,16],['Чернышевская',59.9445,30.3599,16],['Площадь Восстания',59.9303,30.3611,16],['Владимирская',59.9276,30.3479,16],['Пушкинская',59.9207,30.3296,16],['Технологический институт',59.9165,30.3185,16],['Балтийская',59.9072,30.2996,16],['Нарвская',59.9012,30.2749,16],['Кировский завод',59.8797,30.2619,16],['Автово',59.8673,30.2613,16],['Ленинский проспект',59.8512,30.2683,16],['Проспект Ветеранов',59.8421,30.2506,16],['Парнас',60.067,30.3338,17],['Проспект Просвещения',60.0515,30.3325,17],['Озерки',60.0371,30.3215,17],['Удельная',60.0167,30.3156,17],['Пионерская',60.0025,30.2968,17],['Черная речка',59.9855,30.3008,17],['Петроградская',59.9664,30.3113,17],['Горьковская',59.9561,30.3189,17],['Невский проспект',59.9354,30.3271,17],['Сенная площадь',59.9271,30.3203,17],['Технологический институт 2',59.9165,30.3185,17],['Фрунзенская',59.9063,30.3175,17],['Московские ворота',59.8918,30.3179,17],['Электросила',59.8792,30.3187,17],['Парк Победы',59.8663,30.3218,17],['Московская',59.8489,30.3215,17],['Звездная',59.8332,30.3494,17],['Купчино',59.8298,30.3757,17],['Беговая',59.9872,30.2025,18],['Новокрестовская',59.9716,30.2117,18],['Приморская',59.9485,30.2345,18],['Василеостровская',59.9426,30.2783,18],['Гостиный двор',59.9339,30.3334,18],['Маяковская',59.9314,30.3546,18],['Площадь Александра Невского 1',59.9244,30.385,18],['Елизаровская',59.8967,30.4237,18],['Ломоносовская',59.8773,30.4417,18],['Пролетарская',59.8652,30.4703,18],['Обухово',59.8487,30.4577,18],['Рыбацкое',59.831,30.5013,18],['Спасская',59.9271,30.3203,19],['Достоевская',59.9282,30.346,19],['Лиговский проспект',59.9208,30.3551,19],['Площадь Александра Невского 2',59.9236,30.3834,19],['Новочеркасская',59.9291,30.4119,19],['Ладожская',59.9324,30.4393,19],['Проспект Большевиков',59.9198,30.4668,19],['Улица Дыбенко',59.9074,30.4833,19],['Комендантский проспект',60.0086,30.2587,20],['Старая Деревня',59.9894,30.2552,20],['Крестовский остров',59.9718,30.2594,20],['Чкаловская',59.961,30.292,20],['Спортивная',59.952,30.2913,20],['Адмиралтейская',59.9359,30.3152,20],['Садовая',59.9267,30.3178,20],['Звенигородская',59.9207,30.3296,20],['Обводный Канал',59.9147,30.3482,20],['Волковская',59.896,30.3575,20],['Бухарестская',59.8838,30.3689,20],['Международная',59.8702,30.3793,20],['Проспект Славы',59.8565,30.395,20],['Дунайская',59.8399, 30.411,20],['Шушары',59.82,30.4328,20]],['Минск', ['Уручье',53.9453,27.6878,21],['Борисовский тракт',53.9385,27.6659,21],['Восток',53.9345,27.6515,21],['Московская',53.928,27.6278,21],['Парк Челюскинцев',53.9242,27.6136,21],['Академия наук',53.9219,27.5991,21],['Площадь Якуба Коласа',53.9154,27.5833,21],['Площадь Победы',53.9086,27.5751,21],['Октябрьская',53.9016,27.5611,21],['Площадь Ленина',53.8939,27.548,21],['Институт Культуры',53.8859,27.5389,21],['Грушевка',53.8867,27.5148,21],['Михалово',53.8767,27.4969,21],['Петровщина',53.8646,27.4858,21],['Малиновка',53.8497,27.4747,21],['Каменная Горка',53.9068,27.4376,22],['Кунцевщина',53.9062,27.4539,22],['Спортивная',53.9085,27.4808,22],['Пушкинская',53.9095,27.4955,22],['Молодежная',53.9065,27.5213,22],['Фрунзенская',53.9053,27.5393,22],['Немига',53.9056,27.5542,22],['Купаловская',53.9014,27.5612,22],['Первомайская',53.8938,27.5702,22],['Пролетарская',53.8897,27.5855,22],['Тракторный завод',53.89,27.6144,22],['Партизанская',53.8758,27.629,22],['Автозаводская',53.8689,27.6488,22],['Могилевская',53.8619,27.6744,22]],['Казань', ['Авиастроительная ',55.8289,49.0814,23],['Северный вокзал ',55.8415,49.0818,23],['Яшьлек (Юность)',55.8278,49.0829,23],['Козья слобода',55.8176,49.0976,23],['Кремлевская',55.7952,49.1054,23],['Площадь Тукая',55.7872,49.1221,23],['Суконная слобода',55.7771,49.1423,23],['Аметьево',55.7653,49.1651,23],['Горки',55.7608,49.1897,23],['Проспект Победы',55.7501,49.2077,23],['Дубравная ',55.7425,49.2197,23]],['Екатеринбург', ['Проспект Космонавтов',56.9004,60.6139,24],['Уралмаш',56.8877,60.6142,24],['Машиностроителей',56.8785,60.6122,24],['Уральская',56.8581,60.6008,24],['Динамо',56.8478,60.5994,24],['Площадь 1905 года',56.838,60.5973,24],['Геологическая',56.8267,60.6038,24],['Бажовская',56.838,60.5973,24],['Чкаловская',56.8085,60.6107,24],['Ботаническая',56.7975,60.6334,24]],['Нижний Новгород', ['Горьковская',56.3139,43.9948,25],['Московская',56.3211,43.9458,25],['Чкаловская',56.3106,43.9369,25],['Ленинская',56.2978,43.9373,25],['Заречная',56.2851,43.9275,25],['Двигатель Революции',56.2771,43.922,25],['Пролетарская',56.2669,43.9141,25],['Автозаводская',56.2572,43.9024,25],['Комсомольская',56.2527,43.8899,25],['Кировская',56.2474,43.8767,25],['Парк Культуры',56.242,43.8582,25],['Стрелка ',56.3343,43.9597,26],['Московская 2',56.3211,43.9458,26],['Канавинская',56.3203,43.9274,26],['Бурнаковская',56.3257,43.9119,26],['Буревестник',56.3338,43.8928,26]],['Алматы', ['Райымбек батыра',43.2712,76.9448,27],['Жибек Жолы',43.2602,76.9461,27],['Алмалы',43.2513,76.9455,27],['Абая',43.2425,76.9496,27],['Байконур',43.2404,76.9277,27],['Драмтеатр имени Ауэзова',43.2404,76.9175,27],['Алатау',43.239,76.8976,27],['Сайран',43.2362,76.8764,27],['Москва',43.23,76.867,27]],['Новосибирск', ['Заельцовская',55.0593,82.9126,28],['Гагаринская',55.0511,82.9148,28],['Красный проспект',55.041,82.9174,28],['Площадь Ленина',55.0299,82.9207,28],['Октябрьская',55.0188,82.939,28],['Речной вокзал',55.0087,82.9383,28],['Студенческая',54.9891,82.9066,28],['площадь Карла Маркса',54.9829,82.8931,28],['Площадь Гарина-Михайловского',55.0359,82.8978,29],['Сибирская',55.0422,82.9192,29],['Маршала Покрышкина',55.0436,82.9356,29],['Березовая роща',55.0432,82.9529,29],['Золотая нива',55.0379,82.976,29]],['Самара', ['Алабинская',53.2097,50.1344,30],['Российская',53.2114,50.1502,30],['Московская',53.2038,50.1598,30],['Гагаринская',53.2004,50.1766,30],['Спортивная',53.2011,50.1993,30],['Советская',53.2017,50.2207,30],['Победа',53.2073,50.2364,30],['Безымянка',53.213,50.2489,30],['Кировская',53.2114,50.2698,30],['Юнгородок',53.2127,50.283,30]]]
	var metro_color = ['FFCD1C','4FB04F','F07E24','E42313','0072BA','1EBCEF','ADACAC','943E90','915133','BED12C','88CDCF','BAC8E8','F9BCD1','006DA8','88CDCF','CC0066','D6083B','0078C9','009A49','EA7125','702785','0521C3','BF0808','CD0505','0A6F20','D80707','0071BC','CD0505','CD0505','0A6F20','CD0505']
	var postamat_mode = ['postamat', 'postamat_qiwi'];

	this.map = false
	this.map_save = false
	this.map_active = false
	this.map_bottom = false
	this.data = false
	this.repeat = []
	this.timer = false
	this.timer_resize = false
	this.timer_inside = false
	this.data_string = ''
	this.data_parsed = false
	this.loading_inside = ''
	this.inside = false
	this.cod = false
	this.cod_filter = false
	this.balloon_active = false
	this.fullscreen = false
	this.landscape = false
	this.head_class = 0
	this.address_param_show = true
	this.detailed = false;
	this.close = '<div class="edost_window_close" onclick="%onclick%"><svg class="edost_window_close" viewBox="0 0 88 88" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><g transform="matrix(1,0,0,1,-20.116,-20.4011)"><g transform="matrix(0.707107,0.707107,-0.707107,0.707107,64.0579,-26.3666)"><path d="M52.044,52.204l0,-37.983l23.875,0l0,37.983l37.858,0l0,23.875l-37.858,0l0,37.983l-23.875,0l0,-37.983l-38.108,0l0,-23.875l38.108,0Z" style="fill:rgb(145,145,145);"/></g></g></svg></div>'
	this.loading = '<div style="text-align: center;"><svg class="edost_loading" viewBox="0 0 256 256" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><path class="edost_loading_anim_1" d="M128,4C134.627,4 140,9.373 140,16L140,52C140,58.627 134.627,64 128,64C121.373,64 116,58.627 116,52L116,16C116,9.373 121.373,4 128,4Z"/><path class="edost_loading_anim_2" d="M191.305,20.998C197.044,24.312 199.011,31.651 195.697,37.39L177.697,68.567C174.383,74.307 167.044,76.273 161.305,72.959C155.565,69.646 153.599,62.307 156.912,56.567L174.912,25.39C178.226,19.651 185.565,17.684 191.305,20.998Z"/><path class="edost_loading_anim_3" d="M236.282,65.973C239.595,71.713 237.629,79.052 231.889,82.366L200.712,100.366C194.973,103.679 187.634,101.713 184.32,95.973C181.006,90.234 182.973,82.895 188.712,79.581L219.889,61.581C225.629,58.267 232.968,60.234 236.282,65.973Z"/><path class="edost_loading_anim_4" d="M252,128C252,134.627 246.627,140 240,140L204,140C197.373,140 192,134.627 192,128C192,121.373 197.373,116 204,116L240,116C246.627,116 252,121.373 252,128Z"/><path class="edost_loading_anim_5" d="M235.981,191C232.667,196.74 225.328,198.706 219.588,195.392L188.412,177.392C182.672,174.079 180.706,166.74 184.019,161C187.333,155.26 194.672,153.294 200.412,156.608L231.588,174.608C237.328,177.921 239.294,185.26 235.981,191Z"/><path class="edost_loading_anim_6" d="M189,235.981C183.26,239.294 175.921,237.328 172.608,231.588L154.608,200.412C151.294,194.672 153.26,187.333 159,184.019C164.74,180.706 172.079,182.672 175.392,188.412L193.392,219.588C196.706,225.328 194.74,232.667 189,235.981Z"/><path class="edost_loading_anim_7" d="M128,252C121.373,252 116,246.627 116,240L116,204C116,197.373 121.373,192 128,192C134.627,192 140,197.373 140,204L140,240C140,246.627 134.627,252 128,252Z"/><path class="edost_loading_anim_8" d="M65,235.981C59.26,232.667 57.294,225.328 60.608,219.588L78.608,188.412C81.921,182.672 89.26,180.706 95,184.019C100.74,187.333 102.706,194.672 99.392,200.412L81.392,231.588C78.079,237.328 70.74,239.294 65,235.981Z"/><path class="edost_loading_anim_9" d="M20.019,189C16.706,183.26 18.672,175.921 24.412,172.608L55.588,154.608C61.328,151.294 68.667,153.26 71.981,159C75.294,164.74 73.328,172.079 67.588,175.392L36.412,193.392C30.672,196.706 23.333,194.74 20.019,189Z"/><path class="edost_loading_anim_10" d="M4,128C4,121.373 9.373,116 16,116L52,116C58.627,116 64,121.373 64,128C64,134.627 58.627,140 52,140L16,140C9.373,140 4,134.627 4,128Z"/><path class="edost_loading_anim_11" d="M20.019,67C23.333,61.26 30.672,59.294 36.412,62.608L67.588,80.608C73.328,83.921 75.294,91.26 71.981,97C68.667,102.74 61.328,104.706 55.588,101.392L24.412,83.392C18.672,80.079 16.706,72.74 20.019,67Z"/><path class="edost_loading_anim_12" d="M65,20.019C70.74,16.706 78.079,18.672 81.392,24.412L99.392,55.588C102.706,61.328 100.74,68.667 95,71.981C89.26,75.294 81.921,73.328 78.608,67.588L60.608,36.412C57.294,30.672 59.26,23.333 65,20.019Z"/></svg></div>'


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

	this.trim = function(s, space) {
		s = s.replace(/^\s+|\s+$/gm, '');
		if (space) s = s.replace(/\s+/g, ' ');
		return s;
	}

	// конвертирование английской раскладки в русскую
	this.ru = function(s) {
		var r = '';
		for (var i = 0; i < s.length; i++) r += (key_map[s[i]] != undefined ? key_map[s[i]] : s[i]);
		return r;
	}

	// расстояние между gps координатами
	this.distance = function(p, p2) {
		function toRad(x) { return x * Math.PI / 180; }

		var lon1 = p[1];
		var lat1 = p[0];

		var lon2 = p2[1];
		var lat2 = p2[0];

		var R = 6378.1370;

		var x1 = lat2 - lat1;
		var dLat = toRad(x1);
		var x2 = lon2 - lon1;
		var dLon = toRad(x2)
		var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
		var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
		var d = R * c;

		return Math.round(d*1000);
	}

	// добавление в область новой точки
	this.bounds_add = function(v, x, y) {
		if (v === false) var v = [[x, y], [x, y]];
		else {
			if (x < v[0][0]) v[0][0] = x;
			else if (x > v[1][0]) v[1][0] = x;

			if (y < v[0][1]) v[0][1] = y;
            else if (y > v[1][1]) v[1][1] = y;
		}
		return v;
	}

	// иконка
	this.ico = function(company_id, mode, map) {
		var s = '';
        var postamat = (edost_window.in_array(mode, postamat_mode) ? true : false);

		if (map) s = protocol + 'edostimg.ru/img/companymap/';
		else if (postamat) s = protocol + 'edostimg.ru/img/companyico/';
		else s = ico_path + 'company/';

		if (company_id != 0 && postamat) s += mode;
		else {
			s += (map && company_id && company_id.substr(0, 1) == 's' ? 0 : company_id);
			if ( map && mode == 'post' && company_id != 0) s += '-2';
		}

		return s + '.gif';
	}


	this.window = function(param, onclose_set_office) {
//		alert('edost_office.window: ' + param + ' | ' + onclose_set_office);

		var office_data = document.getElementById('edost_office_data');
		if (!office_data) return;

		if (param == 'parse') {
			if (office_data.value != 'parsed') {
				edost_office.data_string = edost_office2.data_string = '';
				edost_office.data_parsed = edost_office2.data_parsed = false;
				self.data_string = office_data.value;
				office_data.value = 'parsed';
			}
			var E = document.getElementById('edost_office_data_parsed');
			if (E) E.value = 'Y';
			return;
		}

		if (onclose_set_office) {
			var E = document.getElementById('ORDER_FORM');
			if (E && E.classList.contains('edost_supercompact_main')) return;
		}

		var param_start = (param ? param : '');

		search_start = false;

		var resize = (resize_start ? true : false);
		if (resize_count != edost_resize.count) {
			resize = true;
			resize_count = edost_resize.count;
		}

		if (edost_resize.template_2019 && edost_window.in_array(edost_resize.template_compact, ['Y', 'S', 'S2']) && param == 'office') {
			if (edost_window.cod) {
				// окно открыто из оплат с фильтром по наложенному платежу
				if (self.cod_filter == 'close_0') param = 'cod_update';
				self.cod_filter = self.cod = true;
			}
			else if (self.cod_filter == 'close_0' || self.cod_filter == 'close_1') {
				if (self.cod_filter == 'close_1') {
					param = 'cod_update';
					self.cod = false;
				}
				self.cod_filter = false;
			}
		}

		var cod_update = false;
		if (param == 'cod_update') {
			param = 'show';
			cod_update = true;
			self.balloon('close');

			if (self.cod_filter) self.cod = true;
			else {
				var E = document.getElementById('edost_office_window_cod');
				if (E) self.cod = (E.checked ? true : false);
			}
		}

		var office_start = false;
		if (param == 'shop' || param == 'office' || param == 'terminal' || param == 'all' || param.substr(0, 7) == 'profile' || param == 'inside') {
			office_start = true;
			onclose_set_office_start = onclose_set_office;
		}

		if (!edost_resize.template_2019 && office_start && (self.map || self.map_save)) {
			if (self.map === self.map_save) self.map.destroy();
			else {
				if (self.map) self.map.destroy();
				if (self.map_save) self.map_save.destroy();
			}
			self.map = self.map_save = false;
			update_map = true;
		}

		if (param.substr(0, 7) == 'profile') {
			param_profile = param.substr(8);
			param = 'profile';
		}

		if (param == 'inside') {
			self.inside = true;
			param = 'all';
		}
		else if (param != 'show') {
			self.inside = false;
		}

		if (onclose_set_office == true) onclose = param;

		var office_format = format;
		if (param == 'shop' || param == 'office' || param == 'terminal' || param == 'all' || param == 'profile') {
			format = param;
			if (param_profile) format += '_' + param_profile;
			param = 'show'
		}

		if (param == 'esc') {
			if (!self.balloon_active) param = 'close';
			else {
				if (edost_window.mode == 'frame') edost_window.set('close'); else self.balloon('close');
				return;
			}
		}

		if (!self.inside)
			if (param != 'show') {
				if (edost_resize.template_2019) {
					edost_resize.scroll('recover', scroll_backup);
					self.cod_filter = 'close_' + (self.cod_filter ? 1 : 0);
				}
			    document.onkeydown = onkeydown_backup;
			    resize_count = edost_resize.count;
			    onkeydown_backup = 'free';

				if (self.timer_resize != undefined) window.clearInterval(self.timer_resize);
			}
			else if (onkeydown_backup == 'free') {
	    		if (edost_resize.template_2019) scroll_backup = edost_resize.scroll('save');
			    onkeydown_backup = document.onkeydown;
				document.onkeydown = new Function('event', 'if (event.keyCode == 27) ' + name + '.window("esc");');
			}

		// интеграция окна
		if (self.inside) {
			var E = document.getElementById('edost_office_inside');
			if (!E) return;
			var E2 = document.getElementById('edost_office_inside_head');
			if (!E2) E.innerHTML = '<div id="edost_office_inside_head" class="edost_office_inside_head"></div><div id="edost_office_inside_map"></div>';
		}
		else {
			var E = document.getElementById('edost_office_window');
			if (!E) {
				var E = document.body;

				var E2 = document.createElement('DIV');
				E2.className = 'edost_office_window_fon';
				E2.id = 'edost_office_window_fon';
				E2.style.display = 'none';
				E2.onclick = new Function('', name + '.window("close")');
				E.appendChild(E2);

				var E2 = document.createElement('DIV');
				E2.className = 'edost_office_window';
				E2.id = 'edost_office_window';
				E2.style.display = 'none';
				var s = '';
				s += self.close.replace('%onclick%', name + ".window('close')");
				s += '<div id="edost_office_window_head"><div id="edost_office_window_head_data">Выбор пункта выдачи <span style="display: inline-block;">и тарифа доставки</span></div></div>';
				s += '<div id="edost_office_window_head_tariff" class="edost_office_window_head"></div>';
				s += '<div><div id="edost_office_window_address" class="edost" style2222="width: 35%; float2222: left;"></div><div style="position: fixed; width2222: 60%; float2222: left;" id="edost_office_window_map"></div></div>';
				E2.innerHTML = s;
				E.appendChild(E2);
			}
		}

		// balloon
		var E = document.getElementById('edost_office_balloon');
		if (!E) {
			var E = document.body;

			var E2 = document.createElement('DIV');
			E2.className = 'edost_office_balloon_fon';
			E2.id = 'edost_office_balloon_fon';
			E2.style.display = 'none';
			E2.onclick = new Function('', name + '.balloon("close")');
			E.appendChild(E2);

			var E2 = document.createElement('DIV');
			E2.className = 'edost_office_balloon';
			E2.id = 'edost_office_balloon';
			E2.style.display = 'none';
			E2.innerHTML = '<div id="edost_office_balloon_head" class="edost"></div><div id="edost_office_balloon_data" class="edost"></div>';
			E.appendChild(E2);
		}

		var display = (param != 'show' ? 'none' : 'block');

		var E = document.getElementById(self.inside ? 'edost_office_inside' : 'edost_office_window');
		if (!E) return;
		E.style.display = display;

		if (!self.inside) {
			var E = document.getElementById('edost_office_window_fon');
			if (E) E.style.display = display;
		}

		if (param == 'close' && onclose != '') {
			var s = onclose;
			onclose = '';
			edost_SetOffice(s);
		}
		if (param != 'show') {
			self.balloon('close');
			return;
		}

		// подготовка данных при первом запуске
		if (office_data.value != 'parsed' || !self.data_parsed || office_format != format || cod_update) {
			if (office_data.value != 'parsed') {
				edost_office.data_string = edost_office2.data_string = '';
				edost_office.data_parsed = edost_office2.data_parsed = false;
				self.cod = false;
				self.data_string = office_data.value;
				office_data.value = 'parsed';
			}
			else if (!self.data_parsed && !self.data_string) {
				self.data_parsed = true;
				self.data_string = (name == 'edost_office' ? edost_office2.data_string : edost_office.data_string);
			}

			var v = (window.JSON && window.JSON.parse ? JSON.parse(self.data_string) : eval('(' + self.data_string + ')'));

			if (city != v.city) self.address('clear');

			update_map = true;
			self.data = [];
			self.data_parsed = true;
			var tariff = [];
			var point = v.point;
			ico_path = v.ico_path + (v.ico_path.substr(-1) != '/' ? '/' : '');
			city = v.city;
			region = v.region;
			yandex_api_key = (v.yandex_api_key ? v.yandex_api_key : '');
			var cod_tariff = false;
			address_draw = false;
			address_draw_html = false;
			if (param_start !== 'cod_update') address_filter = false;
			address_data = [];
			resize = true;
			office_active = 'all';
			address_limit = true;
			metro_far = false;
			mobile_jump = false;
			metro_near_disable = false;
			if (v.template_ico) edost_resize.template_ico = v.template_ico;
			if (edost_resize.template_priority == 'P') self.cod = false;

			metro_key = -1;
			if (city != '') for (var k = 0; k < metro_data.length; k++) if (metro_data[k][0] == city) {
				metro_key = k;
				if (city == 'Москва' || city == 'Санкт-Петербург') metro_far = true;
				break;
			}

			// распаковка и поиск активных тарифов (format: 'shop' - самовывоз из магазина,  'office' - пункты выдачи,  'terminal' - терминалы ТК)
			for (var i = 0; i < v.tariff.length; i++) {
				var ar = v.tariff[i].split('|');
				if (ar[13] == undefined) continue;
				var p = {
					"profile": ar[0], "company": ar[1], "name": ar[2], "tariff_id": ar[3], "price": ar[4], "price_formatted": ar[5], "pricecash": ar[6],
					"codplus": ar[7], "codplus_formatted": ar[8], "day": ar[9], "insurance": ar[10], "to_office": ar[11], "company_id": ar[12], "format": ar[13],
					"cod_tariff": (ar[14] != undefined ? ar[14] : ''),
					"ico": (ar[15] != undefined ? ar[15] : ''), "format_original": (ar[16] != undefined ? ar[16] : ''), "price_original_formatted": (ar[17] != undefined ? ar[17] : ''),
					"pricecod": (ar[18] != undefined ? ar[18] : ''), "pricecod_formatted": (ar[19] != undefined ? ar[19] : ''), "pricecod_original_formatted": (ar[20] != undefined ? ar[20] : ''),
					"office_key": (ar[21] != undefined ? ar[21] : '')
				};
				if (!p.office_key) p.office_key = p.company_id;
				if (p.format == format || format == 'all' || format == 'profile_' + p.profile) tariff.push(p);
			}
//			edost_ShowData(tariff, '', 20);

			// распаковка офисов
			for (var i = 0; i < point.length; i++) {
				var p = [];
				var company_id = point[i].company_id.split('_')[0];
				for (var i2 = 0; i2 < point[i].data.length; i2++) {
					var ar = point[i].data[i2].split('|');
					if (ar[7] == undefined) continue;
					var v = {
						"id": ar[0], "name": ar[1], "address": ar[2], "schedule": ar[3].replace(/,/g, '<br>'), "gps": ar[4].split(','), "type": ar[5], "metro": ar[6], "codmax": ar[7],
						"detailed": (ar[8] != undefined ? ar[8] : false),
						"code": (ar[9] != undefined ? ar[9] : ''),
						"options": (ar[10] != undefined ? ar[10] : 0)
					};

					v.mode = false;
					if (company_id == 23 && v.type == 1) v.mode = 'post';
					else if (edost_window.in_array(v.type, [5, 6, 11, 12])) v.mode = 'postamat';
					else if (edost_window.in_array(v.type, [10])) v.mode = 'postamat_qiwi';

					if (v.code == '') v.code = v.id;
					v.code2 = v.code.toLowerCase();

					self.detailed = (ar[10] !== undefined ? true : false);

					p.push(v);
				}
				point[i].data = p;
			}
//			edost_ShowData(point, '', 20);

			// разделение тарифов по группам (по службам доставки и эксклюзивным ценам)
			var office = [];
			for (var i = 0; i < tariff.length; i++) {
				var v = tariff[i];

				var u = -1;
				for (var i2 = 0; i2 < office.length; i2++) if (v.office_key == office[i2].office_key && v.to_office == office[i2].to_office) {
					u = i2;
					break;
				}

				if (u == -1) {
					var r = {"company": v.company, "company_id": v.company_id, "office_key": v.office_key, "ico": (v.ico != '' ? v.ico : v.tariff_id), "to_office": v.to_office, "format": v.format, "format_original": v.format_original, "point": [], "button": "", "price_count": 0, "button2_info": "", "button_cod": "", "cod": true, "head_tariff": "", "geo": false, "price_length": 0};

					var s = v.company;
					if (v.format_original == 'shop') s = 'Магазин';
					else if (v.company_id.substr(0, 1) == 's' && v.company.substr(0, 9) == 'Самовывоз') s = '';
					r.header = s;

					u = office.length;
					office[u] = r;
				}

				if (v.codplus == '') office[u].cod = false;
				else if (office[u].codplus_max == undefined || v.codplus*1 > office[u].codplus_max[0]*1) office[u].codplus_max = [v.codplus, v.codplus_formatted];

				var price = 0, price_formatted = '', price_original = '';
				if (office[u].price == undefined || v.price*1 < office[u].price[0]*1) office[u].price = v.price;
				if (!self.cod) {
					if (office[u].price_min == undefined || v.price*1 < office[u].price_min[0]*1) office[u].price_min = [v.price, v.price_formatted];
					if (office[u].price_max == undefined || v.price*1 > office[u].price_max[0]*1) office[u].price_max = [v.price, v.price_formatted];
					price = v.price;
					price_formatted = v.price_formatted;
					if (v.price_original_formatted) price_original = v.price_original_formatted;
				}
				else {
					if (office[u].price_min == undefined || v.pricecod*1 < office[u].price_min[0]*1) office[u].price_min = [v.pricecod, v.pricecod_formatted];
					if (office[u].price_max == undefined || v.pricecod*1 > office[u].price_max[0]*1) office[u].price_max = [v.pricecod, v.pricecod_formatted];
					price = v.pricecod;
					price_formatted = v.pricecod_formatted;
					if (v.pricecod_original_formatted) price_original = v.pricecod_original_formatted;
				}

				price_formatted_span = (price_formatted == 0 ? '<span class="edost_price_free">' + free_ico + '</span>' : '<span class="edost_price">' + price_formatted + '</span>');
				office[u].price_length = (price_formatted == 0 ? free_ico.length : price_formatted.length);

				if (v.pricecash !== '' && (office[u].pricecash_max == undefined || v.pricecash*1 > office[u].pricecash_max*1)) office[u].pricecash_max = v.pricecash;

				if (v.cod_tariff != '') cod_tariff = true;

				if (v.cod_tariff != 'Y' && office[u].price_min[0] == price) office[u].head_tariff = price_formatted_span + '<br>' + '<span class="edost_day">' + v.day + '</span>';

				var c = 'edost_SetOffice(\'' + v.profile + '\', \'%office%\', \'' + v.cod_tariff + '\', \'' + v.format + '\')';

				var p = '<span>' + price_formatted_span + '</span>';
				if (price_original) p += '<span class="edost_format_price edost_price_original">' + price_original + '</span>';

				if (v.day != '') p += (!price_original ? '<br>' : '') + '<span class="edost_day">' + v.day + '</span>';

				var s = [];
				if (v.name != '') s.push('<span class="edost_tariff">' + v.name.replace('/', ' ') + '</span>');
				if (v.insurance == 1 && v.cod_tariff != 'Y') s.push('<span class="edost_insurance">со страховкой</span>');
				if (v.cod_tariff != '') s.push('<div class="edost_payment_map"><span class="edost_payment_' + (v.cod_tariff == 'N' ? 'normal2' : 'cod2') + '">' + (v.cod_tariff == 'N' ? 'с предоплатой заказа' : 'с оплатой при получении') + '</span></div>');
				s = s.join('<br>');

				var cod = '';
				if ((edost_resize.template_priority == 'B' || !edost_resize.template_2019) && !cod_tariff && v.codplus !== '') {
					cod = '<tr class="edost_cod"><td colspan="3">';
					if (!self.cod) cod += '<div class="edost_payment">возможна оплата за заказ при получении %payment_type%' + (v.codplus != 0 ? '<br><span>+ ' + v.codplus_formatted + '</span>' : '') + '</div>';
					else {
						cod += '<div class="edost_payment">при получении заказ можно оплатить %payment_type%</div>';
						if (v.codplus != 0) cod += '<div class="edost_payment_green">при предоплате заказа <span>' + (v.price_formatted == 0 ? 'доставка <b>бесплатная!</b>' : 'доставка дешевле на ' + v.codplus_formatted) + '</span></div>';
					}
					cod += '</tr>';
				}

				var button = '<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr>'
					+ '<td class="edost_balloon_tariff">%head% <br> ' + s + '</td>'
					+ '<td class="edost_balloon_price">' + p + '</td>'
					+ '<td class="edost_balloon_get"><div class="edost_button_get" onclick="' + name + '.window(\'close\'); ' + c + '"><span>выбрать</span></div></td>'
					+ '</tr>' + cod + '%warning%</table>';

				office[u].price_count++;
				office[u].button += (office[u].button != '' ? delimiter_balloon : '') + button;
				if (v.cod_tariff != 'Y') office[u].button_cod += (office[u].button_cod != '' ? delimiter_balloon : '') + button;
				office[u].button2_info = '';
			}
//			edost_ShowData(office, '', 20);

			// добавление копии группы тарифов для офисов без наложенного платежа
			if (edost_resize.template_priority != 'P') {
				var ar = [];
				for (var i = 0; i < office.length; i++) {
					if (office[i].cod) {
						var a = true;
						for (var i2 = 0; i2 < office.length; i2++) if (i != i2 && office[i].office_key == office[i2].office_key && office[i].button_cod == office[i2].button_cod && !office[i2].cod) { a = false; break; }
						if (a) {
							var v = self.clone(office[i]);
							v.cod = false;
							v.copy = true;
							v.button = office[i].button_cod;
							ar.push(v);
						}
					}

					var v = self.clone(office[i]);
					ar.push(v);
				}
				office = ar;
			}

			// прикрепление офисов к группам тарифов (сначала офисы с эксклюзивной ценой, потом - все остальные)
			for (var n = 0; n <= 1; n++) {
				for (var i = 0; i < office.length; i++) if (edost_resize.template_priority == 'P' || n == 0 && office[i].to_office != '' || n == 1 && office[i].to_office == '')
					for (var u = 0; u < point.length; u++) if (point[u].company_id == office[i].office_key)
						for (var u2 = 0; u2 < point[u].data.length; u2++) if (point[u].data[u2] != 'none') {
							var v = point[u].data[u2];

							if (edost_resize.template_priority == 'P') {
								v.cod = false;
								office[i].point.push(v);
								point[u].data[u2] = 'none';
								continue;
							}

							if (n == 0 && point[u].data[u2].type != office[i].to_office) continue;

							v.cod = (office[i].copy ? true : office[i].cod);
							if (v.cod && v.codmax !== '' && office[i].pricecash_max*1 > v.codmax*1) v.cod = false;
							if (v.cod && ((v.options & 6) == 2)) v.cod = false; // запрет на оплату наличными и невозможна оплата картой (0 - налиные, 4 - наличные и карта, 6 - карта, 2 - нет оплаты при получении)

							if (n == 1 && office[i].copy && v.cod) continue;

							var a = true;
							if (office[i].cod && !v.cod)
								for (var i2 = 0; i2 < office.length; i2++) if (i != i2 && office[i].office_key == office[i2].office_key && office[i].button_cod == office[i2].button_cod && !office[i2].cod) {
									if (self.inside || !self.cod) office[i2].point.push(v);
									a = false;
									break;
								}
							if (a && (self.inside || !self.cod || v.cod)) office[i].point.push(v);
							point[u].data[u2] = 'none';
						}
				if (edost_resize.template_priority == 'P') break;
			}
//			edost_ShowData(office, '', 20);

			// объединение групп одной компании с тарифами без наложки
			for (var i = 0; i < office.length-1; i++) if (office[i].office_key == office[i+1].office_key && !office[i].cod && !office[i+1].cod && office[i].point.length > 0 && office[i+1].point.length > 0 && office[i].price == office[i+1].price) {
				office[i].point = office[i].point.concat(office[i+1].point);
				office[i+1].point = [];
			}

			// подпись о возможности наложки в заголовке с группами тарифов
			cod_info = false;
			if (!self.inside && edost_resize.template_priority != 'P') {
				var n = 0, n2 = 0;
				for (var i = 0; i < office.length; i++) if (office[i].point.length > 0) {
					n2++;
					if (office[i].cod) n++;
				}
				if (n != 0) {
					for (var i = 0; i < office.length; i++) if (office[i].cod) office[i].head_cod = '<div class="edost_payment">+ оплата при получении</div>';
					if (!self.cod_filter) cod_info = true;
				}
				else if (self.cod) {
					self.cod = false;
					self.window('cod_update');
					return;
				}
			}

			// заголовок с группами тарифов
			var s = '';
			var price_min = -1;
			var n = office.length;
			var n_start = n;
			for (var i = 0; i < n; i++) if (office[i].point.length > 0) {
				var v = office[i];

				if (price_min == -1 || v.price_min[0]*1 < price_min*1) price_min = v.price_min[0];

				s += '<td id="' + name + '_price_td_' + i + '" class="edost_active_on" onclick="' + name + '.set_map(' + i + ');">'
				s += '<div class="edost_ico_header">';

				if (edost_resize.template_ico == 'C') s += '<img class="edost_ico edost_ico_company_normal" src="' + self.ico(office[i].company_id) + '" border="0">';
				else if (edost_resize.template_ico == 'T') s += '<img class="edost_ico_normal" src="' + ico_path + office[i].ico + '.gif" border="0">';
				else s += '<img class="edost_ico_95" src="' + office[i].ico + '" border="0">';

				if (edost_resize.template_ico == 'C') s += '<span class="edost_office_tariff_head">' + v.header + '</span>';
				s += '</div>';
				s += '<div class="edost_office_tariff">' + office[i].head_tariff + '</div>';
				if (office[i].head_cod) s += office[i].head_cod;
				s += '</td>';

				if (n > 1) s += '<td width="8" class="edost_office_head_delimiter"></td><td width="8"></td>';
			}
			else {
				office.splice(i, 1); i--; n--; // удаление группы без пунктов выдачи
			}

			show_price = (office.length <= 1 || office.length == 2 && office[0].office_key == office[1].office_key ? false : true);

            show_price_address = false;
			for (var i = 1; i < office.length; i++) if (office[i-1].price != office[i].price) show_price_address = true;

			if (office.length == 1 && !self.cod) cod_info = false;

			if (!self.inside) {
				var E = document.getElementById('edost_office_window_head_tariff');
				if (!show_price) s = '';
				else {
					s = '<table class="edost_office_head" cellpadding="0" cellspacing="0" border="0"><tr>' + s;
					s += '<td width="120" class="edost_office_head_all2"><div id="' + name + '_price_td_all" style="display: none;" onclick="' + name + '.set_map(\'all\');"><span>показать все</span></div></td>';
					s += '</tr></table>';
				}
				E.innerHTML = s;
			}

			// объединение близко расположенных офисов в один адрес
			var s = {};
			for (var i = 0; i < office.length; i++) {
				var v = office[i];
				for (var i2 = 0; i2 < v.point.length; i2++) {
					var p = v.point[i2];
					var n = 100000;
					var k = Math.round(p.gps[0]*n) + '|' + Math.round(p.gps[1]*n);
					if (!s[k]) s[k] = [];
					s[k].push([i, i2]);
				}
			}
			for (var k in s) {
				v = s[k];
				var n = v.length;
				if (n > 1) {
					var p = false;
					for (var i = 0; i < n; i++) {
						var u = v[i];
						var p2 = office[ u[0] ].point[ u[1] ];

						if (p === false) p = p2;
						else if (p.address != p2.address) office[ u[0] ].point[ u[1] ].address = p.address;
					}
				}
			}

			// поиск одинаковых адресов у разных служб доставки / групп тарифов (repeat_individual = true - у каждого офиса свой заголовок с временем работы)
			var s = {};
			for (var i = 0; i < office.length; i++) {
				var v = office[i];
				for (var i2 = 0; i2 < v.point.length; i2++) {
					var p = v.point[i2];
					var k = p.address;
					if (!s[k]) s[k] = {
						"point_key": [],
						"company_id": [],
						"name": p.name,
						"name2": p.name.toLowerCase(),
						"address": p.address,
						"address2": p.address.toLowerCase(),
						"cod": cod_info && v.cod ? true : false,
						"gps": p.gps,
						"metro_near": [],
						"price": [],
						"price_min": [-1, 0]
					};
					s[k].point_key.push([i, i2]);
					s[k].company_id.push(v.company_id);
					s[k].price.push([v.price_min, v.price_count]);
					if (s[k].price_min[0] === -1 || v.price_min[0]*1 < s[k].price_min[0][0]*1) s[k].price_min = [v.price_min, s[k].price_min[1] + v.price_count];
				}
			}
			var key = -1;
			for (var k in s) {
				v = s[k];
				var n = v.point_key.length;
				if (n > 1) {
					key++;
					var repeat_individual = false;
					var p = false;
					for (var i = 0; i < n; i++) {
						var u = v.point_key[i];
						var p2 = office[ u[0] ].point[ u[1] ];
						office[ u[0] ].point[ u[1] ].repeat = key;

						if (p === false) p = p2;
						else if (p.mode == 'post' && p.code != p2.code || p.schedule != p2.schedule || p.mode != p2.mode) repeat_individual = true;
					}
					if (repeat_individual) for (var i = 0; i < n; i++) {
						var u = v.point_key[i];
						office[ u[0] ].point[ u[1] ].repeat_individual = true;
					}

					// поиск одинаковых адресов внутри одной службы доставки / группы тарифов
					for (var i = 0; i < n; i++) {
						var u = v.point_key[i];
						if (office[ u[0] ].point[ u[1] ].repeat_company == undefined) for (var i2 = i+1; i2 < n; i2++) if (u[0] == v.point_key[i2][0]) {
							var u2 = v.point_key[i2];
							office[ u[0] ].point[ u[1] ].repeat_company = 'main';
							office[ u2[0] ].point[ u2[1] ].repeat_company = true;
						}
					}
				}
				address_data.push(v);
			}

			// выделение жирным дешевой доставки
			var n = 0;
			for (var i = 0; i < office.length; i++)
				if (office[i].price_min[0]*1 > price_min*1 + 50*1) office[i].bold = false;
				else {
					office[i].bold = true;
					n++;
				}
			if (n == office.length) for (var i = 0; i < office.length; i++) office[i].bold = false;

            // поиск ближайших станций метро
			var near_count = 0;
			if (metro_key != -1) {
				var bounds = false;
				for (var k = 1; k < metro_data[metro_key].length; k++) bounds = self.bounds_add(bounds, metro_data[metro_key][k][1], metro_data[metro_key][k][2]);

				if (!metro_data[metro_key][1][4]) for (var k = 1; k < metro_data[metro_key].length; k++) metro_data[metro_key][k][4] = metro_data[metro_key][k][0].toLowerCase();

				var size_x = Math.round(self.distance(bounds[0], [bounds[1][0], bounds[0][1]]) / 1000);
				var size_y = Math.round(self.distance(bounds[0], [bounds[0][0], bounds[1][1]]) / 1000);

				var matrix = [];
				for (var i = 0; i <= size_x; i++) matrix[i] = [];

				var k_x = size_x/(bounds[1][0] - bounds[0][0]);
				var k_y = size_y/(bounds[1][1] - bounds[0][1]);

				for (var k = 1; k < metro_data[metro_key].length; k++) {
					var v = metro_data[metro_key][k];

					var x = Math.round((v[1] - bounds[0][0])*k_x);
					var y = Math.round((v[2] - bounds[0][1])*k_y);

					if (x < 0) x = 0;
					if (y < 0) y = 0;
					if (x > size_x) x = size_x;
					if (y > size_y) y = size_y;

					if (!matrix[x][y]) matrix[x][y] = [];
					matrix[x][y].push(k);
				}

				for (var k = 0; k < address_data.length; k++) {
					var v = address_data[k];

					var x0 = Math.round((v.gps[1] - bounds[0][0])*k_x);
					var y0 = Math.round((v.gps[0] - bounds[0][1])*k_y);

					for (var i = -2; i <= 2; i++) for (var i2 = -2; i2 <= 2; i2++) {
						var x = x0 + i;
						var y = y0 + i2;
						if (x < 0 || y < 0 || x > size_x || y > size_y) continue;

						if (matrix[x][y]) for (var m = 0; m < matrix[x][y].length; m++) {
							var key = matrix[x][y][m];
							var m2 = metro_data[metro_key][key];
							var distance = self.distance([m2[1], m2[2]], [v.gps[1], v.gps[0]]);
							if (metro_far || !metro_far && distance < distance_near_max*2) address_data[k].metro_near.push([key, distance]);
						}
					}

					var n = address_data[k].metro_near.length;
					for (var i = 0; i < n-1; i++) for (var i2 = 0; i2 < n-1; i2++) if (address_data[k].metro_near[i2][1] > address_data[k].metro_near[i2+1][1]) {
						var m = address_data[k].metro_near[i2];
						address_data[k].metro_near[i2] = address_data[k].metro_near[i2+1];
						address_data[k].metro_near[i2+1] = m;
					}

					for (var i = 0; i < address_data[k].metro_near.length; i++) if (address_data[k].metro_near[i][1] <= distance_near_max) { near_count++; break; }

					// если рядом нет станци, ищется самая блищайшая
					if (metro_far && address_data[k].metro_near.length == 0) {
						var ar = [0, -1];
						for (var i = 1; i < metro_data[metro_key].length; i++) {
							var distance = self.distance([metro_data[metro_key][i][1], metro_data[metro_key][i][2]], [v.gps[1], v.gps[0]]);
							if (ar[1] == -1 || distance < ar[1]) ar = [i, distance];
						}
						address_data[k].metro_near.push(ar);
					}
				}
			}
			if (address_data.length == near_count) metro_near_disable = true;

			self.data = office;
			self.repeat = repeat;
		}

		balloon_main = (!self.inside && !edost_office2.inside && self.data.length == 1 && self.data[0].point.length == 1 && (!self.cod_filter && !self.cod || self.cod_filter) ? true : false);
		near_hide = (address_data.length <= 1 ? true : false);

		if (self.map && update_map && !balloon_main) {
			update_map = false
			var office = self.data;
			var repeat = self.repeat;

			// удаление с карты старых меток
			if (api21) self.map.geoObjects.removeAll();
			else self.map.geoObjects.each(function(v) { self.map.geoObjects.remove(v); });

			geo = new ymaps.Clusterer({preset: api21 ? 'islands#invertedDarkBlueClusterIcons' : 'twirl#invertedBlueClusterIcons', groupByCoordinates: false, clusterDisableClickZoom: false, zoomMargin: 100}); // maxZoom: 10

			// размещение меток на карте
			var repeat = [];
			for (var i = 0; i < office.length; i++) {
				var v = office[i];
				var point = [], point2 = [];
				for (var i2 = 0; i2 < v.point.length; i2++) {
					var p = v.point[i2];

					if (p.repeat_company === true) continue;

					var ico_price = (v.price_min[1] == 0 ? free_ico : v.price_min[1]);
					var ico = {iconImageHref: self.ico(p.repeat_company === 'main' ? 0 : v.company_id, p.mode, true), iconImageSize: [36, 36], iconImageOffset: [-12, -36]};
					if (api21) ico.iconLayout = 'default#image';

					var placemark = new ymaps.Placemark([p.gps[1], p.gps[0]], {}, ico);
					if (show_price) placemark.properties.set('iconContent', '<div class="edost_ico_price' + (v.bold ? ' edost_ico_price_big' : '') + '">' + ico_price + '</div>');
					placemark.properties.set('office', [i, i2]);
					placemark.events.add('click', function (e) { self.balloon(e) });

					if (p.repeat == undefined) point.push(placemark);
					else {
						point2.push(placemark);

						// отдельная группа меток для офисов с одинаковыми адресами всех служб доставки
						var u = p.repeat;
						if (repeat[u] == undefined) repeat[u] = {"point": p, "price_min": v.price_min, "bold": v.bold, "key": [i, i2]};
						if (v.bold) repeat[u].bold = v.bold;
						if (v.price_min[0]*1 < repeat[u].price_min[0]*1) repeat[u].price_min = v.price_min;
					}
				}

				self.data[i].geo = new ymaps.Clusterer({preset: api21 ? 'islands#invertedDarkBlueClusterIcons' : 'twirl#invertedBlueClusterIcons', groupByCoordinates: false, clusterDisableClickZoom: false, zoomMargin: 100});
				self.data[i].geo.add(point);
				self.data[i].geo.add(point2);
				geo.add(point);
			}

			// размещение на карте меток для офисов с одинаковыми адресами всех служб доставки
			var point = [];
			for (var i = 0; i < repeat.length; i++) if (repeat[i] != undefined) {
				var v = repeat[i];

				var ico_price = (v.price_min[1] == 0 ? free_ico : v.price_min[1]);

				var ico = {iconImageHref: protocol + 'edostimg.ru/img/companymap/0.gif', iconImageSize: [36, 36], iconImageOffset: [-12, -36]};
				if (api21) ico.iconLayout = 'default#image';

				var placemark = new ymaps.Placemark([v.point.gps[1], v.point.gps[0]], {}, ico);
				if (show_price) placemark.properties.set('iconContent', '<div class="edost_ico_price' + (v.bold ? ' edost_ico_price_big' : '') + '">' + ico_price + '</div>');
				placemark.properties.set('office', v.key);
				placemark.events.add('click', function (e) { self.balloon(e) });

				point.push(placemark);
			}
			geo.add(point);

			self.set_map('init');
			if (cod_update && address_filter !== false) self.address('search', 'repeat');
		}


		if (resize) self.resize();
		if (edost_resize.template_2019) {
			if (!self.inside) {
				var E = document.getElementById('edost_office_window_search');
				if (E && E.type != 'hidden' && !edost_resize.mobile && !cod_update) E.focus();
			}
		}
		if (!edost_resize.template_2019) { // || edost_resize.mobile
			if (self.timer_resize != undefined) window.clearInterval(self.timer_resize);
//			if (!edost_resize.init)
			self.timer_resize = window.setInterval(name + '.resize("resize")', 400);
		}

		if (balloon_main) {
			if (!self.inside) {
				var E = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window'));
				if (E) E.style.display = 'none';
			}

			self.balloon(0);
			return;
		}

		// карта
		if (self.map) self.map.container.fitToViewport();
		else {
			// подключение карты
			var E = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_map');
			if (E) {
				var s = '<div style="padding: 100px 0 0 0;">' + self.loading + '</div>';
				if (self.inside)
					if (window.edost_catalogdelivery && edost_catalogdelivery.loading != '') s = edost_catalogdelivery.loading;
					else if (self.loading_inside != '') s = self.loading_inside;

				E.innerHTML = s;
				self.add_map();
			}
		}

	}


	// установка размера окна и элементов
	this.resize = function(param) {

//		if (param == 'resize' && !edost_resize.template_2019 && edost_resize.init && self.timer_resize != undefined) {
//			window.clearInterval(self.timer_resize);
//			return;
//		}

		if (param == 'mobile_jump_off') {
			mobile_jump = false;
			if (search_start) return;
		}

		resize_start = true;
		var E = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window'));
		if (!E || E.style.display == 'none' && !balloon_main) return;
		resize_start = false;

		// размер окна браузера
		var browser_w = window.innerWidth;
		var browser_h = window.innerHeight;

		if (!edost_resize.template_2019) {
			if (param == 'resize') {
				if (Math.round(browser_width - browser_w) == 0 && Math.round(browser_height - browser_h) == 0) {
					resize_update++;
					if (resize_update > 3) return;
				}
				else resize_update = 1;
			}
			else resize_update = 1;
		}

		browser_width = browser_w;
		browser_height = browser_h;

		if (self.inside) {
			var E2 = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_head');
			var E3 = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_map');
			if (!E2 || !E3) return;

			var window_w = E.offsetWidth;
			var window_h = E.offsetHeight;
			var head_h = E2.offsetHeight;
			var max_w = browser_w - 100;

			window_w = max_w;
			if (window_w > 1200) {
				window_w = (browser_h > 960 ? Math.round(browser_h*1.25) : 1200);
				if (window_w > max_w) window_w = max_w;
			}
			if (window_h == 0) return;

			E3.style.height = window_h - head_h - 2 + 'px';

//			if (self.map) self.map.container.fitToViewport();
		}
		else {
			var E_head = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_head');
			var E_tariff = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_head_tariff');
			var E_map = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_map');
			var E_address = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_address');
			if (!E_head || !E_tariff || !E_map || !E_address) return;

//			E.style.opacity = (balloon_main ? '0.01' : '1');

			var window_w = E.offsetWidth;
			var window_h = E.offsetHeight;
			var max_w = browser_w;

			self.landscape = false;
			self.fullscreen = false;

			var map = true;
			if (edost_resize.mobile || browser_w < 1300 || browser_h < 800) {
				self.fullscreen = true;
				window_w = browser_w;
				window_h = browser_h;

				var a = (browser_w > browser_h ? true : false);
				var h = browser_h - 900;
				if (h < 0) h = 0;
				if (!a && browser_w < 640 + h*0.8  || a && browser_h < 550) map = false;
				if (a && browser_w > 500 && browser_h < 550) self.landscape = true;
			}
			else {
				window_w = browser_w - 100;
				window_h = browser_h - 100;
				if (browser_w > 1500) window_w = 1300 + Math.round((browser_w - 1500)*0.3);
				if (window_h > 1000) window_h = 1000;
			}
			if (window_h == 0) return;

			self.map_active = map;
			self.map_bottom = (!map && browser_w < browser_h && browser_h > 650 ? true : false);

			E.style.width = window_w + 'px';
			E.style.height = window_h*(self.fullscreen ? 2 : 1) + 'px';

			if (self.fullscreen) {
				var x = 0;
				var y = 0;
			}
			else {
				var x = Math.round((browser_w - window_w)*0.5);
				var y = Math.round((browser_h - window_h)*0.5);
			}

			E.style.left = x + 'px';

			if (mobile_jump !== false) {
				E.style.position = 'absolute';
				E.style.top = (edost_resize.get_scroll('y') - mobile_jump) + 'px';
			}
			else {
				E.style.position = '';
				E.style.top = y + 'px';
			}

			edost_resize.change_class(E, ['', 'edost_office_address_fullscreen'], map ? 0 : 1);
			edost_resize.change_class(E, ['edost_office_window_normal', 'edost_office_fullscreen'], self.fullscreen ? 1 : 0);
			edost_resize.change_class(E, ['', 'edost_office_landscape'], self.landscape && browser_h < 550 ? 1 : 0);
			edost_resize.change_class(E, ['', 'edost_office_bottom_map'], self.map_bottom ? 1 : 0);
			edost_resize.change_class(E, ['', 'edost_office_jump'], mobile_jump !== false ? 1 : 0);
			edost_resize.change_class(E, ['', 'edost_office_search_point'], (address_filter !== false || search_start) && address_filter_value != '' ? 1 : 0);

			// определение стиля для тарифов в шапке
			var c = 4;
			if (show_price && self.data.length > 1 && map && !(self.landscape && (browser_h < 450 || browser_h < 600 && edost_resize.template_ico == 'T2'))) {
				var n = 0, n2 = 0, s = 0, s2 = 0, ico;

				if (edost_resize.template_ico == 'C') ico = 35;
				else if (edost_resize.template_ico == 'T') ico = 60;
				else ico = 95;

				for (var i = 0; i < self.data.length; i++) {
					var cod = (cod_info && self.data[i].cod ? 1 : 0);
					var header = (edost_resize.template_ico == 'C' ? self.data[i].header.length * 8 : 0);
					n += ico + header + 80 + 20;
					n2 += (header > 80 ? header : 80) + 20;
					s += Math.max(self.data[i].price_length*5, ico, cod ? 70 : 0) + 20;
					s2 += (cod && ico < 65 ? 65 : ico) + 20;
				}

				if (n == 0) c = 4;
				else if (n + 200 < window_w) c = 0;
				else if (n2 + 200 < window_w) c = 1;
				else if (s + 140 < window_w) c = 2;
				else if (s2 + 140 < window_w) c = 3;

				if (self.landscape && browser_h < 650 && c < 2) c = 2;
			}
			edost_resize.change_class(E_tariff, ['edost_office_tariff_normal', 'edost_office_tariff_normal2', 'edost_office_tariff_small', 'edost_office_tariff_small2', 'edost_office_tariff_hide'], c);
			self.head_class = c;


			var s = 24;
			var w = (self.landscape ? browser_w*0.5 : browser_w);
			if (w < 300) s = 16;
			else if (w < 350) s = 18;
			else if (w < 600) s = 20;
			else if (w < 900) s = 22;
			E_head.style.marginBottom = (c >= 3 ? '5px' : 0);
			E_head.style.fontSize = s + 'px';

			var c = 0;
			if (edost_resize.mobile)
				if (!self.fullscreen) c = 1;
				else if (browser_width < browser_height && (browser_width < 450 || browser_height < 700) || browser_width > browser_height && (browser_width < 700 || browser_height < 450)) c = 3;
				else c = 2;
			var ar = ['edost_device_pc', 'edost_device_tablet', 'edost_device_tablet_small', 'edost_device_phone'];
			var device = ar[c].substring(13);
			edost_resize.change_class(E_address, ar, c);
			edost_resize.change_class(E, ar, c);

			var head_h = E_tariff.offsetHeight + (E_head ? E_head.offsetHeight : 0) + (c == 3 ? 5 : 0);

			var h = window_h - head_h - 2 + (mobile_jump !== false ? mobile_jump : 0);

			if (self.map_bottom) self.map_bottom = Math.round((h - 70 - 60)*0.5);

			address_height = h;
			E_address.style.height = h + 'px';

			var w = 0;
			if (!map) w = false;
			else if (window_w < 700) w = 320;
			else if (window_w < 900) w = 350;
			else if (window_w < 1100) w = 400;
			else w = 450;
			address_width = (w ? w + 'px' : '100%');
			E_address.style.width = address_width;

			if (map) {
				var rect = E_address.getBoundingClientRect();
				E_map.style.top = rect.top + 'px';
				E_map.style.left = (rect.left + rect.width) + 'px';
				E_map.style.width = (window_w - w - (self.fullscreen ? 0 : 7) - (self.fullscreen && device == 'pc' ? 18 : 0)) + 'px';
				E_map.style.height = (address_height + 2 - (self.fullscreen ? 0 : 7) - (!self.fullscreen && self.head_class == 4 ? 5 : 0)) + 'px';
			}
			else if (self.map_bottom) {
				E_map.style.top = (window_h - self.map_bottom + (mobile_jump !== false ? mobile_jump : 0)) + 'px';
				E_map.style.left = 0;
				E_map.style.height = self.map_bottom + 'px';
			}

			if (!search_start) self.address('redraw');

			var E_head = document.getElementById('edost_office_address_head');
			var E_main = document.getElementById('edost_office_address_main');
			var E_window_head_data = document.getElementById('edost_office_window_head_data');
			var E_head_data = document.getElementById('edost_office_address_head_data');
			var E_close2 = document.getElementById('edost_office_address_close');
			var E_search = document.getElementById('edost_office_window_search');
			var E_hint = document.getElementById('edost_office_window_search_hint');
			var E_point = document.getElementById('edost_office_search_point');
			if (E_head && E_main && E_close2) {
				if (E_point && address_filter_value != '') E_point.innerHTML =
					'<div class="edost_office_address_filter">' +
					'<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td class="edost_office_address" onclick="window.edost_office.address(\'click\', \'' + i + '|metro\', this)">' +
					'точка поиска <span>' + address_filter_value + '</span>' +
					'</td><td width="70" style="text-align: right;">' +
					'<div class="edost_office_button edost_office_button_blue_white" style="width: 62px; padding: 4px 4px; margin: 0 0 0 10px;" onclick="edost_office.address(\'clear\', \'resize\');"><span style="color: #888; font-size: 14px; font-weight: normal;">сбросить</span></div>' +
					'</td></tr></table>' +
					'</div>';

				if (self.landscape) {
			        var h = address_height - E_head.offsetHeight;
					E_head.style.height = (address_height*2 - 0) + 'px';

					E_main.style.height = browser_height + 'px';
					E_main.style.width = (Math.round(browser_width*0.5) - 21) + 'px';
					E_search.style.width = (Math.round(browser_width*0.5) - 75) + 'px';

					var h = window_h - 80;
					var h2 = E_head_data.offsetHeight + E_window_head_data.offsetHeight + 40;
					if (h < h2) h = h2;
					E_window_head_data.style.width = '';
					E_close2.style.top = h + 'px';
					E_close2.style.left = Math.round((window_w*0.5 - 80)*0.5) + 'px';
				}
				else {
					E_head.style.height = 'auto';

					E_window_head_data.style.height = 'auto';
					E_window_head_data.style.width = (self.fullscreen ? (window_w - 100) + 'px' : '');

					var a = (device == 'tablet_small' && address_width == '100%' && E_window_head_data.offsetHeight < 40 ? true : false);
					E_window_head_data.style.lineHeight = (a ? '32px' : '');
					if (a) E_window_head_data.style.height = 30 + 'px';

					var h = address_height - E_head.offsetHeight;

					E_main.style.height = (h - 8) + (self.fullscreen ? 10 : 0) - self.map_bottom + 'px';
					E_main.style.marginBottom = (self.map_bottom ? self.map_bottom : 0) + 'px';
					E_main.style.width = 'auto';

					E_search.style.width = (E_main.offsetWidth - 75) + 'px';

					if (address_width == '100%') {
						E_close2.style.top = 8 + 'px';
						E_close2.style.left = (browser_width - 88) + 'px';
						E_search.style.width = (browser_width - 95) + 'px';
					}
					else if (self.fullscreen) {
						E_close2.style.top = 3 + 'px';
						E_close2.style.left = (browser_width - 88) + 'px';
					}
				}

				var E_hint = document.getElementById('edost_office_window_search_hint');
				if (E_hint && browser_width) {
					E_hint.innerHTML = 'введите адрес (' + (metro_key != -1 ? 'метро или ' : '') + 'улицу, дом)';
					var rect = E_search.getBoundingClientRect();
					var a = (rect.width < (metro_key != -1 ? 235 : 180) ? true : false);
					E_hint.style.width = (a ? E_main.offsetWidth + 'px' : '');

					var x = 62;
					if (self.fullscreen) x = (a ? 10 : Math.round(rect.left + 5));
					E_hint.style.left = x + 'px';
				}
			}
		}

		self.balloon('redraw');
		if (!edost_resize.template_2019) {
			if (edost_window.mode == 'frame') edost_window.resize();
			if (self.map) self.map.container.fitToViewport();
		}

	}


	// адреса пунктов выдачи + поиск
	this.address = function(param, param2, event) {

		if (self.inside && !edost_window.in_array(param, ['draw_metro', 'draw_distance'])) return;

		var warning = [];
		if (param == 'limit') {
			address_limit = false;
			param = 'redraw';
			if (param2) param2.parentNode.innerHTML = self.loading;
		}
		else if (param == 'point') {
			// поиск ближайших пунктов выдачи
			self.balloon('close');
			self.address('clear_active');
			address_scroll_reset = true;

			var gps = param2;
			var address = -1;

			address_filter = [];
			address_filter_gps = gps;

			var ar = [];
			for (var i = 0; i < address_data.length; i++) {
				var v = address_data[i];
				var key = -1;
				if (office_active !== 'all') {
					for (var i2 = 0; i2 < v.point_key.length; i2++) if (v.point_key[i2][0] == office_active) { key = i2; break; }
	                if (key == -1) continue;
				}
				var p = self.distance(gps, [v.gps[1], v.gps[0]]);
				ar.push([i, p]);
			}

			if (ar.length > 0) {
				for (var i = 0; i < ar.length-1; i++) for (var i2 = 0; i2 < ar.length-1; i2++) if (ar[i2][1]*1 > ar[i2+1][1]*1) {
					var s = ar[i2];
					ar[i2] = ar[i2+1];
					ar[i2+1] = s;
				}

				var distance = ar[0][1];
				if (distance == 0 && ar.length > 1) distance = ar[1][1];
				if (distance < distance_near_max) distance = distance_near_max;
				distance *= (event ? 1.5 : 1.2);

//				for (var i = 0; i < ar.length; i++) if (i == 0 || ar[i][1] <= distance_near_max * (event ? 2 : 1) || !event && ar[i][1] < distance*1.5) address_filter.push([ar[i][0], ar[i][1]]);
				for (var i = 0; i < ar.length; i++) if (i == 0 || ar[i][1] <= distance) address_filter.push([ar[i][0], ar[i][1]]);
			}

			if (self.map && address_filter.length > 0) {
				var bounds = false;
				for (var i = 0; i < address_filter.length; i++) {
					var v = address_data[ address_filter[i][0] ];
					bounds = self.bounds_add(bounds, v.gps[1], v.gps[0]);
				}
				if (bounds !== false) {
					self.map.setBounds(bounds, {checkZoomRange: false});
					var z = self.map.getZoom();
					if (z == 0) z = 11;
					if (z > 18) z = 18;
					self.map.setZoom(z - 1);
				}
			}

            self.resize();

			param = 'filter';
		}
		else if (param == 'search') {
			// запрос на получение координат по строке поиска
			if (param2 === 'repeat' && address_filter !== false && address_filter_gps !== false) {
				self.address('point', address_filter_gps);
				return;
			}
			else if (param2 != undefined) {
				var v = address_data[param2];
				address_filter_value = v.address;
				address_filter_mode = '';
				self.address('point', [v.gps[1], v.gps[0]], true);
				return;
			}

			var E = document.getElementById('edost_office_window_search');
			if (metro_key != -1) {
				var v = metro_data[metro_key];
				for (var i = 1; i < v.length; i++) if (v[i][4] == search_value) {
					address_filter_value = 'м. ' + v[i][0];
					address_filter_mode = 'metro';
					self.address('point', [v[i][1], v[i][2]]);
					return;
				}
			}

			if (!window.ymaps) warning.push('нет доступа к серверу поиска');
			else {
				var s = search_value;

				// проверка на повторный запрос
				for (var i = 0; i < address_get_data.length; i++) if (address_get_data[i][0] == search_value) {
					address_filter_value = search_value;
					address_filter_mode = '';
					self.address('point', address_get_data[i][1]);
					return;
				}

				var E_main = document.getElementById('edost_office_address_main');
				if (E_main) E_main.innerHTML = '<div style="padding: 20px 0 40px 0;">' + self.loading + '</div>' + '<div class="edost_office_button edost_office_button_light" style="display: block;" onclick="edost_office.address(\'clear\', \'resize\');"><span>отменить поиск</span></div>';

				search_start = true;
				mobile_jump = false;
				address_filter_value = search_value;
				self.resize();

				s += ', ' + city + ', ' + region;
				ymaps.geocode(s, {results: 1}).then(function (r) {
					if (search_value == '' || !search_start) return;
					search_start = false;

//					alert('получен ответ');
					var firstGeoObject = r.geoObjects.get(0); // первый результат геокодирования
					var gps = firstGeoObject.geometry.getCoordinates();

					address_get_data.push([search_value, gps]);
					address_filter_value = search_value;
					address_filter_mode = '';
					self.address('point', gps);
				});

				return;
			}
		}
		else if (param == 'cod_update') {
			var E = document.getElementById('edost_office_address_main');
			if (E) E.innerHTML = '<div style="padding: 20px 0 0 0;">' + self.loading + '</div>';
			window.setTimeout(name + ".window('cod_update')", 250);
			return;
		}
		else if (param == 'clear') {
			var E = document.getElementById('edost_office_window_search');
			if (E) E.value = '';
			search_value = '';
			search_value_code = '';
			search_values = [];
			address_filter = false;
			address_filter_value = '';
			address_filter_gps = false;
			address_filter_mode = '';
			address_limit = true;
			search_start = false;
			self.balloon('close');
			self.address('clear_active');

			if (param2 == 'resize') edost_office.resize();
		}
		else if (param == 'clear_active') {
			if (self.map && point_active !== false && point_active_geo !== false) self.map.geoObjects.remove(point_active_geo);
			point_active = false;
			return;
		}
		else if (param == 'click_active') {
			if (point_active !== false) self.address('set_center', point_active);
			return;
		}
		else if (param == 'set_center') {
			if (!self.map) return;
			self.map.setCenter(param2);
			self.map.setZoom(event == 'metro' ? 14 : 16);
			var p = self.map.getGlobalPixelCenter();
			if (self.balloon_active && !self.fullscreen) self.map.setGlobalPixelCenter([p[0] - Math.round(balloon_width*0.5 - 27*1.5) - 20 , p[1]]);
			return;
		}
		else if (param == 'clear_now') {
			var ar = document.getElementsByClassName('edost_office_address_active');
			if (ar) for (var i = 0; i < ar.length; i++) if (ar[i]) ar[i].classList.remove('edost_office_address_active');
			return;
		}
		else if (param == 'set_now') {
			// выделение адреса
			if (param2) param2.classList.add('edost_office_address_active');
			return;
		}
		else if (param == 'set_point_active') {
			// стрелка над активной меткой
			if (!self.map) return;
			self.address('clear_active');
			point_active = param2;
			if (point_active_geo === false) {
				var ico = {iconImageHref: protocol + 'edostimg.ru/img/site/point_active.svg', iconImageSize: [64, 64], iconImageOffset: [-26, -105]};
				if (api21) ico.iconLayout = 'default#image';
			    point_active_geo = new ymaps.Placemark(param2, {}, ico);
				point_active_geo.events.add('click', function (e) { self.address('click_active', e) });
			}
	        point_active_geo.geometry.setCoordinates(param2);
			self.map.geoObjects.add(point_active_geo);
			return;
		}
		else if (param == 'click' || param == 'open') {
			var s = param2 + '|';
			s = s.split('|');
			if (s[1] == 'metro') {
				self.balloon('close');

				var gps = [metro_data[metro_key][s[0]][1], metro_data[metro_key][s[0]][2]];
				if (param == 'open') {

					var E_near = document.getElementById('edost_office_metro_near');
					if (E_near) E_near.checked = false;

					address_filter_value = 'м. ' + metro_data[metro_key][s[0]][0];
					address_filter_mode = 'metro';
					self.address('point', gps, true);
				}
				else self.address('set_center', gps, 'metro');

				if (param == 'click') {
					self.address('clear_now');
					var E = event.parentNode.parentNode.parentNode.parentNode;
					self.address('set_now', E);
				}

				return;
			}

			balloon_map_active = false;
			if (param == 'open') self.balloon(param2, 'address');
			else self.balloon('close');

			if (self.map) {
				var p = [address_data[param2].gps[1], address_data[param2].gps[0]];
				self.address('set_point_active', p);
				self.address('set_center', p);
			}

			self.address('clear_now');
			var E = event.parentNode.parentNode.parentNode.parentNode;
			if (param == 'open') E = E.parentNode;
			self.address('set_now', E);

			return;
		}
		else if (param == 'draw_metro') {
			if (param2[0] == -1) return '';
			var m = metro_data[metro_key][ param2[0] ];
			var c = metro_color[m[3]];
			var s = '';
			s += '<div class="edost_metro_main">';
			s += '<div class="edost_metro" style="border: 1px solid #' + c + ';"><div style="background: #' + c + ';">м.</div> <span>' + m[0] + '</span></div>';
			if (param2[1] != -1) s += self.address('draw_distance', [param2[1], 'metro']);
			s += '</div>';
			return s;
		}
		else if (param == 'draw_distance') {
			if (param2[0] == -1) return '';
			var distance = param2[0];
			var metro = (param2[1] == 'metro' ? true : false);

			distance = Math.round(distance/100)*100;
			var info = (metro ? '' : 'от точки поиска');

			if (distance <= distance_near_max) c = '0A0';
			else if (distance < 2000) c = '888';
			else if (distance < 8000) c = 'A00';
			else c = 'F00';

			if (distance == 0) {
				distance = (metro ? 'рядом со станцией' : 'в точке поиска');;
				info = '';
			}
			else if (distance < 200) {
				distance = (metro ? 'рядом со станцией' : 'рядом с точкой поиска');;
				info = '';
			}
			else if (distance < 1000) distance += ' м';
			else if (distance < 5000) distance = Math.round(distance/100)/10 + ' км';
			else if (distance < 20000) distance = Math.round(distance/1000) + ' км';
			else if (distance < 50000) distance = Math.round(distance/5000)*5 + ' км';
			else distance = Math.round(distance/10000)*10 + ' км';

			if (info != '') info = ' <span>' + info + '</span>';

			var s = '';
			if (!metro) s += '<div class="edost_metro_main">';
			s += '<div class="edost_metro edost_distance" style="border: 1px solid #' + c + ';	' + (info == '' ? ' padding-right: 0;' : '') + '"><div style="background: #' + c + ';">' + distance + '</div>' + info + '</div>';
			if (!metro) s += '</div>';
			return s;
		}
		else if (param == 'search_focus') {
			if (edost_resize.mobile) {
				var rect = param2.getBoundingClientRect();
				mobile_jump = rect.top - 10;
				address_scroll_reset = true;
				self.resize();
			}
			return;
		}
		else if (param == 'search_blur') {
			if (edost_resize.mobile) window.setTimeout(name + ".resize('mobile_jump_off')", 150);
			return;
		}
		else if (param == 'search_keydown' || param == 'search_keyup') {
			if (event.keyCode == 38 || event.keyCode == 13)
				if (event.preventDefault) event.preventDefault(); else event.returnValue = false;

			var move = 0;
			if (param == 'search_keydown') {
				if (event.keyCode == 13 && address_search_now != -1) {
					if (!address_search[address_search_now]) return;
					var v = address_search[address_search_now];
					var i = v[0];
					var gps = (v[1] == 'metro' ? [metro_data[metro_key][i][1], metro_data[metro_key][i][2]] : [address_data[i].gps[1], address_data[i].gps[0]]);
					if (v[1] != 'metro') self.address('set_point_active', gps);
					self.address('set_center', gps, v[1] == 'metro' ? 'metro' : '');
				}
				if (event.keyCode == 38) move = -1;
				if (event.keyCode == 40) move = 1;
				if (move != 0) {
					address_search_now += move;
					if (address_search_now < 0) address_search_now = 0;
					if (address_search_now > address_search.length-1) address_search_now = address_search.length-1;

					self.address('clear_now');
					var E = document.getElementById('edost_office_search_' + address_search_now);
					self.address('set_now', E);
				}
			}

			var E = param2;
			var value = E.value;
			var value_code = self.trim(E.value, true).toLowerCase();
			var s1 = '', s2 = '';

			value = value.toLowerCase().replace(/[ё]/g, 'е');
			if (value.replace(/[^a-z]/g, '').length > 0) value = self.ru(value);
			value = value.replace(/[^а-я0-9.,-]/g, ' ');
			value = search_value_original = self.trim(value, true);

			// удаление префиксов (ул, д, ...)
			if (ignore && value.length > 1) {
				value = ' ' + value.replace(/ /g, '  ').replace(/,/g, ', ').replace(/\./g, '. ') + ' ';
				for (var i = 0; i < ignore.length; i++) value = value.replace(new RegExp(' ' + ignore[i] + '[ .]', 'g'), ' ');
				value = self.trim(value, true);
			}

			if (search_value != '' && value == '') {
				address_search_now = -1;
				address_now = -1;
			}

			if (search_value == value && search_value_code == value_code) return;
			search_value = value;
			search_value_code = value_code;

			address_filter = false;
			address_filter_gps = false;
			address_limit = true;

			// разбивка фразы на слова
			search_values = [];
			var ar = self.trim(value.replace(/[,.-]/g, ' '), true).split(' ');
			for (var i = 0; i < ar.length; i++) if (ar[i] != '') {
				// удаление префиксов (ул, д, ...) рядом с цифрами
				if (ar[i].replace(/[^0-9]/g, '').length > 0) for (var i2 = 0; i2 < ignore.length; i2++) if (ar[i].search(new RegExp(ignore[i2] + '[0-9]', 'g')) >= 0) {
					ar[i] = ar[i].substr(ignore[i2].length);
					break;
				}

				// удаление повторов
				var a = false;
				for (var i2 = 0; i2 < i; i2++) if (ar[i] == ar[i2]) { a = true; break; }
				if (a) continue;

				search_values.push(ar[i]); //search_values.push((search_values.length > 0 ? ' ' : '') + ar[i]);
			}
		}

		var E = document.getElementById('edost_office_window_address');
		if (!E) return;

		var E_head = document.getElementById('edost_office_address_head');
		if (!E_head) {
			// генерация блоков при первом запуске
			s = '';
			s += '<div id="edost_office_address_head">';
				s += '<div id="edost_office_address_head_data">';
					s += '<div id="edost_office_address_close" class="edost_button_window_close" style="display: inline-block;" onclick="' + name + '.window(\'close\');">закрыть</div>';
					s += '<div class="edost_office_search_div">';                       // type="search"
						s += 'поиск: <input id="edost_office_window_search" maxlength="50" type="text" spellcheck="false" onkeydown="window.edost_office.address(\'search_keydown\', this, event)" onkeyup="window.edost_office.address(\'search_keyup\', this, event)" onfocus="window.edost_office.address(\'search_focus\', this)" onblur="window.edost_office.address(\'search_blur\', this)">';
						s += '<div id="edost_office_window_search_hint"></div>';
					s += '</div>';
					s += '<div id="edost_office_search_point" style="display: none;"></div>';
					s += '<div id="edost_office_address_param"></div>';
				s += '</div>';
			s += '</div>';
			s += '<div id="edost_office_address_main"></div>';
			E.innerHTML = s;
			E_head = document.getElementById('edost_office_address_head');
		}
		var E_main = document.getElementById('edost_office_address_main');
		var E_window_head_data = document.getElementById('edost_office_window_head_data');
		var E_head_data = document.getElementById('edost_office_address_head_data');
		var E_close2 = document.getElementById('edost_office_address_close');
		var E_search = document.getElementById('edost_office_window_search');
		var E_hint = document.getElementById('edost_office_window_search_hint');
		if (!E_head || !E_main || !E_close2) return;

		if (metro_key != -1) {
			var E_near = document.getElementById('edost_office_metro_near');
			metro_near = (E_near && E_near.checked ? true : false);
		}

		var E_param = document.getElementById('edost_office_address_param');
		if (E_param) {
			var s = '';
			if (cod_info && !self.cod_filter) s += '<div class="edost_checkbox' + (self.cod ? ' edost_checkbox_active' : '') + '"><input class="edost_checkbox" type="checkbox" id="edost_office_window_cod"' + (self.cod ? ' checked=""' : '') + ' onclick="' + name + '.address(\'cod_update\')"> <label for="edost_office_window_cod">только с оплатой при получении</span></div>';
			if (metro_key != -1 && !metro_near_disable) s += '<div class="edost_checkbox' + (metro_near ? ' edost_checkbox_active' : '') + '"><input class="edost_checkbox" type="checkbox" id="edost_office_metro_near"' + (metro_near ? ' checked=""' : '') + ' onclick="' + name + '.address();"> <label for="edost_office_metro_near">недалеко от станции метро</span></div>';
			E_param.innerHTML = s;
		}

		var count = 0;
		var top_main = (warning.length > 0 || mobile_jump !== false ? 0 : E_main.scrollTop);
		var values_length = search_values.length;
		var r_count = [0, 0, 0];

		if ((param == 'redraw' || param == 'redraw_timer') && address_draw !== false) {
			r = address_draw;
			r_count = address_count;
		}
		else {
			address_search = [];
			var code_search_active = (search_value_code.indexOf(' ') == -1 && search_value_code.indexOf(',') == -1 && search_value_code.length >= 2 && search_value_code.length <= 10 ? true : false);

	        var ar = [], ar2 = [];
			if (address_filter !== false) {
				for (var i = 0; i < address_filter.length; i++) ar.push(address_filter[i]);
			}
			else for (var i = 0; i < address_data.length; i++) {
				var v = address_data[i];

				var search_values_type = [];
				for (var i2 = 0; i2 < values_length; i2++) {
					var s = '';
					if (search_values[i2].replace(/[^0-9]/g, '').length > 0) s = 'digit';
					else if (search_values[i2].length >= 3) s = 'long';
					search_values_type.push(s);
				}

				var metro_search = [];
				var main = true, n = 0, n2 = 0, n_digit = 0, code_search = false;

				// поиск по коду
				if (code_search_active) for (var i2 = 0; i2 < v.point_key.length; i2++) {
					var k = v.point_key[i2];
					if (self.data[ k[0] ].point[ k[1] ].code2.indexOf(search_value_code) >= 0) {
						code_search = [self.data[ k[0] ].company_id, self.data[ k[0] ].point[ k[1] ].code, self.data[ k[0] ].point[ k[1] ].mode];
						main = true;
						r_count[0]++;
						break;
					}
				}

				// поиск по адресу и метро
				if (code_search === false && values_length > 0) {
					main = false;
					for (var i2 = 0; i2 < values_length; i2++) {
						if ((values_length == 1 || search_values_type[i2] == 'digit' || search_values_type[i2] == 'long') && (v.name2.indexOf(search_values[i2]) >= 0 || v.address2.indexOf(search_values[i2]) >= 0)) {
							if (search_values_type[i2] == 'digit') n_digit++;
							n++
						}
						if (search_values[i2].length >= 3) for (var m = 0; m < v.metro_near.length; m++) if (metro_data[metro_key][ v.metro_near[m][0] ][4].indexOf(search_values[i2]) >= 0) { n2++; metro_search.push(m); break; }
					}
					if (n + n2 == 0) continue;
					if (values_length >= 2 && n_digit == n) continue;

					if (n == values_length || values_length >= 3 && n >= 2) {
						main = true;
						r_count[0]++;
					}
					else {
						r_count[1]++;
//						if (n2 != 0 && n == 0) r_count[2]++;
					}
				}

				if (main) ar.push([i, -1, metro_search, code_search]);
				else ar2.push([i, -1, metro_search, code_search]);
			}
			ar = ar.concat(ar2);

			var r = [];
			for (var k = 0; k < ar.length; k++) {
				var i = ar[k][0];
				var distance = ar[k][1];
				var v = address_data[i];
				var metro_search = (ar[k][2] ? ar[k][2] : []);
				var code_search = (ar[k][3] ? ar[k][3] : false);

				var key = -1;
				if (office_active !== 'all') {
					for (var i2 = 0; i2 < v.point_key.length; i2++) if (v.point_key[i2][0] == office_active) { key = i2; break; }
	                if (key == -1) continue;
				}

				var near = false;
				for (var i2 = 0; i2 < v.metro_near.length; i2++) if (v.metro_near[i2][1] <= distance_near_max) near = i2;
				if (metro_near && near === false) continue;

				r.push([i, distance, metro_search, key, near, code_search]);
			}
			address_draw = r;
			address_count = r_count;
		}

		address_search = [];

		// метро
		var metro_html = '';
		var m = [], m2 = [];
		if (address_filter === false && metro_key != -1 && values_length > 0) for (var i = 1; i < metro_data[metro_key].length; i++) {
			var n = 0;
			for (var i2 = 0; i2 < values_length; i2++) if (search_values[i2].length < 3 && metro_data[metro_key][i][4].indexOf(search_values[i2]) == 0 || search_values[i2].length >= 3 && metro_data[metro_key][i][4].indexOf(search_values[i2]) >= 0) n++;
			if (n == 0) continue;

			if (n == values_length) m.push(i); else m2.push(i);
		}
		var metro_count = m.length;
		var metro_count2 = m2.length;
		m = m.concat(m2);

		var ar = [];
		for (var k = 0; k < m.length; k++) {
			var i = m[k];
			var v = metro_data[metro_key][i];

			address_search.push([i, 'metro']);
			var search_i = address_search.length - 1;

			var s = '';
			s += '<div id="edost_office_search_' + search_i + '" class="edost_office_address' + (address_search_now == search_i ? ' edost_office_address_active' : '') + '">';
			s += '<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td class="edost_office_address" onclick="window.edost_office.address(\'click\', \'' + i + '|metro\', this)">';
			s += self.address('draw_metro', [i, -1]);
			s += '</td><td width="85" style="text-align: right;">';
			s += '<div class="edost_button_search" onclick="edost_office.address(\'open\', \'' + i + '|metro\', this)"><span>' + near_show + '</span></div>';
			s += '</td></tr></table>';
			s += '</div>';
			ar.push(s);
		}
		metro_html = ar.join(delimiter_address);

		// пункты выдачи
		var r_html = '';
		var r_length = r.length;
		var r_limit = false;
		if (r_length > 50 && address_limit) {
			r_length = 40;
			r_limit = true;
		}
		for (var k = 0; k < r_length; k++) {
			var i = r[k][0];
			var distance = r[k][1];
			var metro_search = r[k][2];
			var key = r[k][3];
			var near = r[k][4];
			var code_search = r[k][5];
			var v = address_data[i];

			var s = '';
			if (count > 0) s += delimiter_address;

			count++;
			address_search.push([i]);
			var search_i = address_search.length - 1;

			price = (key >= 0 ? v.price[key][0][1] : v.price_min[0][1]);
			if (price === '0') price = '<span class="edost_price_free">' + free_ico + '</span>';

			s += '<div id="edost_office_search_' + search_i + '" class="edost_office_address' + (address_search_now == search_i ? ' edost_office_address_active' : '') + '">';
			s += '<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td class="edost_office_address" onclick="window.edost_office.address(\'click\', ' + i + ', this)">';

			for (var i2 = 0; i2 < v.company_id.length; i2++) {
				var p = self.data[ v.point_key[i2][0] ].point[ v.point_key[i2][1] ];
				s += '<img class="edost_ico_company_small" src="' + self.ico(v.company_id[i2], p.mode) + '" border="0">';
			}

			if (self.data[ v.point_key[0][0] ].format_original == 'shop') s += ' <span class="edost_office_shop">Магазин: </span>';
			s += ' ' + v.address;

			var s2 = [];
			if (v.name != '') s2.push(v.name);
			if (s2.length > 0) s += ' (' + s2.join(', ') + ')';

			if (address_filter_mode != 'metro') for (var i2 = 0; i2 < v.metro_near.length; i2++) {
				var a = false;
				for (var ms = 0; ms < metro_search.length; ms++) if (metro_search[ms] == i2) { a = true; break; }
				if (i2 == 0) a = true;
//				if (address_filter_mode == 'metro' && address_filter_value == 'м. ' + metro_data[metro_key][v.metro_near[i2][0]][0]) a = true;
//				if (near === false && (i2 == 0 || v.metro_near[i2][1] - v.metro_near[0][1] <= 200)) a = true;
//				if (near !== false && (i2 <= near || v.metro_near[i2][1] - v.metro_near[near][1] <= 200)) a = true;
				if (!a) continue;
				s += self.address('draw_metro', [v.metro_near[i2][0], v.metro_near[i2][1]]);
			}
			s += self.address('draw_distance', [distance, '', v.metro]);

			if (code_search !== false) s += '<div class="edost_code">' + (v.company_id.length == 1 && code_search[2] == 'post' ? 'индекс' : 'код пункта') + (v.company_id.length > 1 ? ' <img class="edost_ico_company_small" src="' + self.ico(code_search[0], code_search[2]) + '" border="0">' : '') + ': ' + code_search[1] + '</div>';

			s += '</td><td width="65" style="text-align: center;">';
			if (show_price_address) s += '<div class="edost_address_price">' + price + '</div>';
			s += '<div class="edost_button_open" onclick="window.edost_office.address(\'open\', ' + i + ', this)"><span>открыть</span></div>';
			s += '</td></tr></table>';
			s += '</div>';

			r_html += s;
		}

		var search_result = (mobile_jump !== false && search_value.length >= 2 ? true : false);
		var search_button = (search_value.length > search_value_min ? '<div class="edost_office_button edost_office_button_blue" style="display: block; width: 150px; margin: 10px auto;" onclick="edost_office.address(\'search\');"><span style="color: #EEE;">найти по адресу</span><div style="color: #FFF; font-size: 18px; padding-top: 2px;">' + search_value_original + '</div></div>' : '');

		if (count == 0 && warning.length == 0 && !search_result && metro_html == '') warning.push('совпадений не найдено');

		var hint = [];
		if (r_limit) hint.push('<div class="edost_office_button edost_office_button_light" onclick="edost_office.address(\'limit\', this);"><span>показать все</span></div>');
		if (self.data.length > 1 && office_active !== 'all') {                 // && address_filter[0][1] > distance_near_max
			if (self.head_class == 4 || count == 0 || address_filter !== false) hint.push('<div class="edost_office_button edost_office_button_red" onclick="edost_office.set_map(\'all\')"><span>' + (self.head_class != 4 ? 'провести поиск по всем пунктам выдачи' : 'показать все компании') + '</span></div>');
		}
		if (!search_result && address_filter === false && search_value.length > 0) {
			if (search_value.length > search_value_min) hint.push(search_button);
			else hint.push('<div style="color: #888; text-align: center; font-size: 16px;">попробуйте указать ' + (metro_key != -1 ? 'название станции метро или ' : '') + 'более точный адрес и нажать кнопку "найти по адресу"</div>');
		}
		if (values_length > 0 || address_filter !== false) hint.push('<div class="edost_office_button edost_office_button_red" onclick="edost_office.address(\'clear\', \'resize\');"><span>сбросить поиск</span></div>');

//		if (address_filter !== false && address_filter_value != '') r_html = '<div class="edost_office_address_filter">точка поиска <span>' + address_filter_value + '</span></div>' + r_html;

		var h = 0;
		if (self.fullscreen) h = (self.map_bottom ? 20 : 60);

		var ar = [];
		if (search_result) {
			var c = [], c2 = '';
			if (r_count[0] > 0 || r_count[1] > 0 || metro_count > 0 || metro_count2 > 0) {
				c = ['<span style="color: #AAA;">найдено совпадений</span>'];
				if (metro_key != -1) c.push('станций метро: ' + '<b>' + metro_count + '</b>' + (metro_count2 > 0 ? '+' + metro_count2 : ''));
				c.push('пунктов выдачи: ' + '<b>' + r_count[0] + '</b>' + (r_count[1] > 0 ? '+' + r_count[1] : ''));
				c2 += '<div class="edost_button_search" style="width: 80px;" onclick=""><span>перейти<br>к выбору</span></div>';
			}

			var s = '';
			if (c.length == 0) {
				if (search_value.length <= search_value_min) s += '<div style="color: #F55; text-align: center;">совпадений не найдено <br>укажите полный адрес для точного поиска</div>';
			}
			else {
				s += '<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0"><tr><td>';
				s += c.join('<br>');
				s += '</td><td width="85" style="text-align: right;">';
				s += c2;
				s += '</td></tr></table>';
			}

			ar.push('<div class="edost_office_search_result">' + s + search_button + '</div>' + delimiter_address);
		}
		if (warning.length > 0) ar.push('<div class="edost_office_warning">' + warning.join(delimiter_address2) + '</div>');
		if (metro_html != '') ar.push('<div id="edost_address_metro">' + metro_html + '</div>');
		if (r_html != '') ar.push(r_html);
		if (hint != '') ar.push('<div style="text-align: center; padding-bottom: 10px;">' + hint.join(delimiter_address2) + '</div>');
		var html = (self.landscape ? '<div style="height: 10px;"></div>' : '') + ar.join(delimiter_address2) + (self.fullscreen ? '<div style="height: ' + h + 'px;"></div>' : '');

		if (html != address_draw_html) E_main.innerHTML = address_draw_html = html;

		if (!address_scroll_reset) E_main.scrollTop = top_main;
		address_scroll_reset = false;

//		self.info('update');

	}


	this.set_map = function(n) {

		var init = false;
		if (n == 'init') {
			init = true;
			n = 'all';
		}
		else {
			self.address('clear_active');
			self.balloon('close');
		}

		if (self.data == undefined) return;
		if (self.data.length == 1) n = 'all';

		office_active = n;

		if (self.map) if (api21) self.map.geoObjects.removeAll();

		var point_count = 0;
		for (var i = 0; i < self.data.length; i++) {
			var show = (n == 'all' || i == n ? true : false);

			var E = document.getElementById(name + '_price_td_' + i);
			if (E) E.className = 'edost_active_' + (show ? 'on' : 'off');

			if (!self.map) continue;

			if (!show) self.map.geoObjects.remove(self.data[i].geo);
			else {
				point_count += self.data[i].point.length;
				if (n == 'all') self.map.geoObjects.remove(self.data[i].geo);
				else {
					self.map.geoObjects.add(self.data[i].geo);

					var p = self.data[i].geo.getBounds();
					if (p[0][0] == p[1][0] && p[0][1] == p[1][1]) point_count = 1;
					if (point_count == 1) self.map.setCenter(p[0]);
					else self.map.setBounds(p, {checkZoomRange: false});
				}
			}
		}

		if (self.map)
			if (n != 'all') self.map.geoObjects.remove(geo);
			else {
				self.map.geoObjects.add(geo);

				var p = geo.getBounds();
				if (p[0][0] == p[1][0] && p[0][1] == p[1][1]) point_count = 1;
				if (point_count == 1) self.map.setCenter(p[0]);
				else self.map.setBounds(p, {checkZoomRange: false});
			}

		var E = document.getElementById(name + '_price_td_all');
		if (E) E.style.display = (n == 'all' || self.data.length == 1 ? 'none' : 'block');

		if (self.map)
			if (point_count == 1) self.map.setZoom(15);
			else {
				var z = self.map.getZoom();
				if (z == 0) z = 11;
				self.map.setZoom(z - 1);
			}

		if (!init)
			if (address_filter !== false) self.address('search', 'repeat');
			else self.address();

	}


	this.create_map = function() {

		if (self.map) return;
		var E = document.getElementById('edost_office_' + (self.inside ? 'inside' : 'window') + '_map');
		if (!E) return;

		E.innerHTML = '';
		E.className = 'edost_map' + (!self.inside ? '2' : '');

		api21 = (window.ymaps && window.ymaps.control && window.ymaps.control.FullscreenControl ? true : false);

		var v = {center: [0, 0], zoom: 12, type: 'yandex#map', behaviors: ['default', 'scrollZoom']};
		if (api21) v.controls = ['zoomControl', 'typeSelector'];

		self.map = new ymaps.Map('edost_office_' + (self.inside ? 'inside' : 'window') + '_map', v);
		self.map_save = self.map;

		if (!api21) {
			self.map.controls
				.add('zoomControl', { left: 5, top: 5 })
				.add('typeSelector')
				.add('mapTools', { left: 35, top: 5 });
		}

		map_loading = false;

		if (self.inside) {
			var E = document.getElementById('edost_office_detailed');
			if (E) E.style.display = 'block';
		}
		else {
			var E = document.getElementById('edost_office_window');
			if (E && E.style.display == 'none') return;
		}

		self.window('show', '');

	}


	this.add_map = function() {

		if (map_loading) return;

		map_loading = true;

		if (!window.ymaps || !ymaps.Clusterer) {
			var E = document.body;
			var E2 = document.createElement('SCRIPT');
			E2.type = 'text/javascript';
			E2.charset = 'utf-8';
			E2.src = protocol + 'api-maps.yandex.ru/' + (edost_resize.os == 'android' ? '2.1.50' : '2.0-stable') + '/?load=package.standard,package.clusters&lang=ru-RU' + (yandex_api_key ? '&apikey=' + yandex_api_key : '');
			E.appendChild(E2);
		}

		if (window.ymaps) ymaps.ready(self.create_map);
		else {
			if (self.timer != undefined) window.clearInterval(self.timer);
			self.timer = window.setInterval('if (window.ymaps) { window.clearInterval(' + name + '.timer); ymaps.ready(' + name + '.create_map); }', 500);
		}

	}


	// окно с данными по активному пункту выдачи и тарифам
	this.balloon = function(param, mode) {

		if (param == 'redraw' && !self.balloon_active) return;

		var center = [-1, -1];
		var redraw = false;

		// клик по метке на карте
		if (typeof param === 'object') {
			if (!self.fullscreen) balloon_map_active = true;

			var key = param.get('target')['properties'].get('office');
			var p = param.get('position');
			var x = edost_resize.get_scroll('x');
			var y = edost_resize.get_scroll('y');
			center = [p[0] - x, p[1] - y];

			param = 'close';
			for (var i = 0; i < address_data.length; i++) {
				for (var i2 = 0; i2 < address_data[i].point_key.length; i2++) {
					var k = address_data[i].point_key[i2];
					if (k[0] == key[0] && k[1] == key[1]) { param = i; break; }
				}
				if (param !== 'close') break;
			}

			if (param !== 'close' && self.fullscreen) {
				var p = self.data[key[0]].point[key[1]].gps;
				self.address('set_point_active', [p[1], p[0]]);
			}
		}

		var E = document.getElementById('edost_office_balloon');
		if (!E) return;

		var E_fon = document.getElementById('edost_office_balloon_fon');

		if (param === 'close') {
			E.style.display = 'none';
			self.balloon_active = false;
			if (balloon_map_active) self.address('clear_active');
			if (E_fon) E_fon.style.display = 'none';
			if (balloon_main) {
				balloon_main = false;
				self.window('close');
			}
			return;
		}

		var E_head = document.getElementById('edost_office_balloon_head');
		var E_data = document.getElementById('edost_office_balloon_data');
		if (!E_head || !E_data) return;

		self.balloon_active = true;
		var top_data = E_data.scrollTop;


		E.style.display = 'block';

		if (param != 'redraw') {
			var office = self.data;
			var repeat_head = '';
			var repeat_data = [];
			var repeat_count = 0;
			var repeat_individual = false;
			var cod_tariff = false, office_all = true;
			var p_count = address_data[param].point_key.length
			var r = ['', '', ''];
			for (var i = 0; i < p_count; i++) {
				var key = address_data[param].point_key[i];

				if (mode !== 'all' && office_active !== 'all' && key[0] != office_active) {
					office_all = false;
					continue;
				}

				var v = office[ key[0] ];
				var p = v.point[ key[1] ];

				var head = 'Пункт выдачи', hint = '', ico = '';

				if (edost_resize.template_ico == 'C') ico = '<img class="edost_ico edost_ico_company_normal" src="' + self.ico(v.company_id, p.mode) + '" border="0">';
				else if (edost_resize.template_ico == 'T') ico = '<img class="edost_ico_normal" src="' + ico_path + v.ico + '.gif" border="0">';
				else ico = '<img class="edost_ico_95" src="' + v.ico + '" border="0">';

				var detailed = '';
				if (self.detailed && p.detailed != 'N' && (!self.map_active || edost_resize.mobile || !(p.options & 1) && p.mode != 'post')) detailed = '<div class="edost_button_detailed" onclick="' + name + '.info(\'%office%\'' + (p.detailed ? ', \'' + p.detailed + '\'' : '') + ')">подробнее...</div>';

				if (v.company_id.substr(0, 1) == 's' && (v.company.substr(0, 9) == 'Самовывоз' || v.format_original == 'shop')) v.company = '';

				if (edost_window.in_array(p.mode, postamat_mode)) head = 'Постамат';
				else if (p.mode == 'post') head = 'Почтовое отделение <span style="opacity: 0.6;">' + p.code + '</span>';
				else if (v.format_original == 'shop') head = 'Магазин';
				else if (v.format == 'terminal') head = 'Терминал ТК';
//				hint = '&nbsp;<a href="' + protocol + 'pickpoint.ru/faq/?category=5" target="_blank"><img class="edost_hint2" style="opacity: 0.6;" src="' + protocol + 'edostimg.ru/img/site/hint.gif"></a>';

				head = '<span class="edost_office_balloon_head">' + head + '</span>' + hint;
				var head_tariff = ico;
				if (edost_resize.template_ico == 'C') head_tariff += (v.company.length >= 11 ? '<br>' : '') + ' <span class="edost_office_balloon_tariff">' + v.company + '</span>';

				var s = [];
				if (p.cod && (p.options & 6) == 6) s.push(!self.cod ? 'При получении заказ можно оплатить только банковскими картами.' : 'Оплата только банковскими картами!');
				if (p.options & 256) s.push('Точка выдачи перегружена, срок <span style="display: inline-block;">доставки может быть увеличен.</span>');
				for (var i2 = 0; i2 < s.length; i2++) s[i2] = '<div style="padding-top: 5px;">' + s[i2] + '</div>';
				warning = (s.length > 0 ? '<tr class="edost_balloon_warning"><td colspan="3">' + s.join('') + '</td></tr>' : '');

				var s = [];
				if (p.cod && self.detailed) {
					if ((p.options & 2) == 0) s.push(['cash', 'наличные']);
					if (p.options & 4) s.push(['card', 'банковские карты']);
					if (p.options & 16) s.push(['paypass', 'бесконтактный платеж']);
				}
				for (var i2 = 0; i2 < s.length; i2++) s[i2] = '<img class="edost_ico_payment" src="' + protocol + 'edostimg.ru/img/site/payment_' + s[i2][0] + '.svg" title="' + s[i2][1] + '" border="0">';
				var payment_type = (s.length > 0 ? '<span style="display: inline-block;">' + s.join(' ') + '</span>' : '');

				var button = '<div class="edost_office_balloon_div' + (!p.cod ? ' edost_office_balloon_cod_hide' : '') + (v.price_count == 1 ? ' edost_office_balloon_tariff_hide' : '') + '">' + v.button.replace(/%office%/g, p.id).replace(/%head%/g, head_tariff).replace(/%payment_type%/g, payment_type).replace(/%warning%/g, warning) + '</div>';

				var address = (p.name != '' ? '<span class="edost_office_balloon_name">' + p.name + '</span><br>' : '') + '<span class="edost_office_balloon_address">' + p.address + '</span>';

				var metro = '';
				for (var i2 = 0; i2 < address_data[param].metro_near.length; i2++) if (i2 < 3 && (address_data[param].metro_near[i2][1] <= distance_near_max || address_data[param].metro_near[i2][1] - address_data[param].metro_near[0][1] <= 200)) metro += self.address('draw_metro', address_data[param].metro_near[i2]);

				r[0] = head + '<br>' + address + ' ' + detailed.replace('%office%', p.id) + metro + '<div class="edost_balloon_schedule2">' + p.schedule + '</div>';
				r[1] = button;

				// офисы с одинаковыми адресами
				if (p.repeat != undefined) {
					repeat_count++;
					if (repeat_head == '') {
						if (p.repeat_individual) repeat_individual = true;
						repeat_head = (!p.repeat_individual ? head + '<br>' + address + ' ' + detailed + metro + '<div class="edost_balloon_schedule2">' + p.schedule + '</div>' : '<b>' + address + '</b>' + metro);
					}

					var s = '';
					var head_active = head + p.schedule; // + (v.company_id == 23 ? p.code : '');
					var repeat_index = -1;
					for (var i2 = 0; i2 < repeat_data.length; i2++) if (repeat_data[i2][0] == head_active) { repeat_index = i2; break; }
					if (repeat_index == -1) { repeat_data.push([head_active, '', []]); repeat_index = repeat_data.length - 1; }
					if (p.repeat_individual) {
						if (repeat_data[repeat_index][1] != '') s += delimiter_balloon2;
						else s += '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="edost_office_balloon_head_individual' + (p.mode == 'post' ? ' edost_post' : '') + '" ' + (repeat_index == 0 ? 'style="margin-top: 0;"' : '') + '><tr>'
								+ '<td>' + head + (detailed != '' ? '<div style="margin-bottom: 3px;">' + detailed + '</div>' : '') + '</td>'
								+ '<td><div class="edost_balloon_schedule2">' + p.schedule + '</div></td></tr></table>';
					}
					repeat_data[repeat_index][1] += (repeat_data[repeat_index][1] != '' && !p.repeat_individual ? delimiter_balloon2 : '') + s + button;
					repeat_data[repeat_index][2].push([v.company_id, p.id, p.options & 1]);
				}
			}
			if (repeat_count > 1) {
				var s = '', o = '';
				for (var u = 0; u < repeat_data.length; u++) {
					var c = repeat_data[u];
					var n = c[2].length;
					for (var u2 = 0; u2 < n; u2++) if (u2 == 0 || edost_window.in_array(c[2][u2][0], [30, 5, 26, 23]) && !c[2][u2][2]) o = c[2][u2][1] + (n > 1 ? '_repeat' : '');
					s += c[1].replace('%office%', o);
				}
				r[0] = repeat_head.replace('%office%', o);
				r[1] = s;
			}

			var s = '';
			if (!near_hide && !self.inside) s += (repeat_count > 1 && !office_all && repeat_individual ? '<br>' : '') + '<div id="edost_balloon_near" class="edost_button_search' + (repeat_count > 1 && office_all ? ' edost_office_repeat' : '') + '" style="position: absolute; height: 30px; line-height: 12px; width: 80px;" onclick="edost_office.address(\'search\', ' + param + ')"><span>' + near_show + '</span></div>';
			if (!office_all) s += '<div id="edost_balloon_office" class="edost_button_search" style="position: absolute; height: 30px; line-height: 12px; width: 60px;" onclick="edost_office.balloon(' + param + ', \'all\')"><span>' + office_show + '</span></div>';
			s += '<div id="edost_balloon_close2" class="edost_button_window_close" style="position: absolute; height: 30px;" onclick="' + name + '.balloon(\'close\');">закрыть</div>';
			s += '<div id="edost_balloon_close" style="position: absolute;">' + self.close.replace('%onclick%', name + ".balloon('close')") + '</div>';
			r[0] += s;
			E_head.innerHTML = '<div id="edost_balloon_head_data">' + r[0] + '</div>';
			E_data.innerHTML = r[1];

			r[3] = center;
			r[4] = param;
			r[5] = mode;
			balloon_draw = r;
		}
		else {
			redraw = true;
			center = balloon_draw[3];
			param = balloon_draw[4];
			mode = balloon_draw[5];
		}


		var E_close = document.getElementById('edost_balloon_close');
		var E_close2 = document.getElementById('edost_balloon_close2');
		var E_near = document.getElementById('edost_balloon_near');
		var E_office = document.getElementById('edost_balloon_office');
		var E_head_data = document.getElementById('edost_balloon_head_data');


		var fullscreen = false;
		var mobile = edost_resize.mobile;

		var landscape = (browser_width > 500 && browser_height < 650 && browser_width > browser_height ? true : false);

		var window_w = (landscape ? 600 : 400);
		var window_h = (landscape ? 400 : 600);

		if (landscape && (browser_width < 650 || browser_height < 450) || !landscape && (browser_width < 450 || browser_height < 650)) {
			fullscreen = true;
			window_w = browser_width;
			window_h = browser_height;
		}

		if (E_fon) E_fon.style.display = (self.fullscreen || self.inside ? 'block' : 'none');

		edost_resize.change_class(E, ['', 'edost_office_balloon_fullscreen'], fullscreen ? 1 : 0);
		edost_resize.change_class(E, ['', 'edost_office_balloon_small'], !landscape && window_w > 350 || landscape && window_w*0.5 > 350 ? 0 : 1);
		edost_resize.change_class(E, ['', 'edost_office_balloon_landscape'], landscape ? 1 : 0);

		var c = 0;
		if (edost_resize.mobile)
			if (!fullscreen) c = 1;
			else if (browser_width < browser_height && browser_width < 450 && browser_height < 700 || browser_width > browser_height && browser_width < 700 && browser_height < 450) c = 3;
			else c = 2;
		var ar = ['edost_device_pc', 'edost_device_tablet', 'edost_device_tablet_small', 'edost_device_phone'];
		var device = ar[c].substring(13);
		edost_resize.change_class(E, ar, c);

		E_close.style.display = (device == 'pc' ? 'block' : 'none');
		E_close2.style.display = (device != 'pc' ? 'block' : 'none');

		if (self.fullscreen) {
			center = [-1, -1];
			mode = '';
		}

		if (fullscreen) {
			E.style.width = window_w + 'px';
			E.style.height = window_h + 'px';
			E.style.overflow = 'hidden';
			E.style.left = 0;
			E.style.top = 0;

			if (landscape) {
				E_data.style.height = 'auto';
				var h_data = E_data.offsetHeight;
				E_head.style.height = window_h + 'px';
				E_data.style.height = window_h + 'px';
				E_data.style.width = '50%';
				E_data.style.top = (h_data < window_h ? Math.round((window_h - h_data)*0.5) : 0) + 'px';

				E_head_data.style.marginTop = 0;
				var h = E_head_data.offsetHeight + 20;
				var h2 = Math.round(window_h*0.5);
				if (h < h2) h = h2;
				E_close2.style.top = h + 'px';
				E_close2.style.left = '20px';
				if (E_near) {
					E_near.style.top = h + 'px';
					E_near.style.left = (Math.round(window_w*0.5) - (E_office ? 180 : 100)) + 'px';
				}
				if (E_office) {
					E_office.style.top = h + 'px';
					E_office.style.left = (Math.round(window_w*0.5) - 80) + 'px';
				}
			}
			else {
				E_head.style.height = 'auto';

				E_head_data.style.marginTop = '34px';
				E_close2.style.top = '8px';
				E_close2.style.left = (window_w - 88) + 'px';
				if (E_near) E_near.style.top = E_near.style.left = '8px';
				if (E_office) {
					E_office.style.top = '8px';
					E_office.style.left = '108px';
				}

				var h_head = E_head.offsetHeight;

				E_data.style.height = (window_h - h_head) + 'px';
				E_data.style.width = '100%';
			}
		}
		else {
			E.style.width = window_w + 'px';
			E.style.height = 'auto';
			E.style.overflow = 'hidden';
			E_data.style.height = 'auto';
			E_data.style.width = '100%';
			E_head.style.height = 'auto';

			E_head_data.style.marginTop = (landscape || device == 'pc' ? 0 : '34px');

			var h_head = E_head.offsetHeight;
			var h_data = E_data.offsetHeight;

			if (landscape) {
				var h = h_data + 20;
				var h2 = h_head + (device == 'pc' ? 10 : 60);
				if (h < h2) h = h2;
				if (h > window_h) h = window_h;
				window_h = h;
				E.style.height = window_h + 'px';

				E_head.style.height = h + 'px';
				var w = Math.round(window_w*0.5);
				E_data.style.width = w + 'px';
				E_data.style.left = w + 'px';
				E_data.style.top = ((h_data < window_h ? Math.round((window_h - h_data)*0.5) : 0)) + 'px';
				if (h_data >= window_h) E_data.style.height = (window_h) + 'px';

				if (device == 'pc') {
					E_close.style.top = '6px';
					E_close.style.left = '4px';
					if (E_near) {
						E_near.style.top = '5px';
						E_near.style.left = 0;
					}
					if (E_office) {
						E_office.style.top = '5px';
						E_office.style.left = 0;
					}
				}
				else {
					var h = E_head_data.offsetHeight + 26;
					var h2 = Math.round(window_h*0.5);
					if (h < h2) h = h2;
					E_close2.style.top = h + 'px';
					E_close2.style.left = '20px';
					if (E_near) {
						E_near.style.top = h + 'px';
						E_near.style.left = (Math.round(window_w*0.5) - (E_office ? 180 : 100)) + 'px';
					}
					if (E_office) {
						E_office.style.top = h + 'px';
						E_office.style.left = (Math.round(window_w*0.5) - 80) + 'px';
					}
				}
			}
			else {
				var h = h_head + h_data;
				if (h > window_h) {
					h = window_h;
					E_data.style.height = (window_h - h_head) + 'px';
				}
				window_h = h;
				E.style.height = window_h + 'px';

				if (device == 'pc') {
					E_close.style.top = '6px';
					E_close.style.left = (window_w - 32) + 'px';
					if (E_near) {
						E_near.style.top = '5px';
						E_near.style.left = 0;
					}
					if (E_office) {
						E_office.style.top = '5px';
						E_office.style.left = 0;
					}
				}
				else {
					E_close2.style.top = '8px';
					E_close2.style.left = (window_w - 88) + 'px';
					if (E_near) E_near.style.left = E_near.style.top = '8px';
					if (E_office) {
						E_office.style.top = '8px';
						E_office.style.left = '108px';
					}
				}
			}

			if (mode == 'address') {
				var search_i = -1;
				for (var i = 0; i < address_search.length; i++) if (!address_search[i][1] && address_search[i][0] == param) { search_i = i; break; }
				if (search_i == -1) mode = '';
			}
			if (mode == 'address') {
				// установка окна рядом с адресной строкой
				var E_address = document.getElementById('edost_office_search_' + search_i);
				if (E_address) {
					var rect = E_address.getBoundingClientRect();
					var x = rect.left + rect.width;
					var y = rect.top - window_h*0.5 + 26;
				}
			}
			else {
				// установка окна по центру экрана
				if (center[0] != -1) {
					var x = center[0] - window_w*0.5;
					var y = center[1] - window_h*0.5;
				}
				else {
					var x = (browser_width - window_w)*0.5;
					var y = (browser_height - window_h)*0.5;
				}
			}

			if (y < 15) y = 15;
			if (y + window_h > browser_height - 15) y = browser_height - 15 - window_h;
			if (x < 15) x = 15;
			if (x + window_w > browser_width - 15) x = browser_width - 15 - window_w;

			E.style.left = Math.round(x) + 'px';
			E.style.top = Math.round(y) + 'px';

			balloon_width = window_w;
		}

		if (redraw) E_data.scrollTop = top_data;

	}


	// подробная информация (для маленького экрана)
	this.info = function(id, link) {

		if (link == undefined || link == '') link = protocol + 'edost.ru/office.php?c=' + id;
		else link = link.replace(/%id%/g, id);

		var a = false;
		if (link.indexOf('edost.ru/office.php') != -1) link += '&map=Y' + (!edost_resize.mobile ? '&pc=Y' : '');
		else if (link.indexOf('frame=Y') == -1) a = true;

		if (a) window.open(link, '_blank');
		else edost_window.set('frame', 'head=;class=edost_frame;href=' + encodeURIComponent(link));

	}

}

var edost_office = new edost_office_create('edost_office');
var edost_office2 = new edost_office_create('edost_office2');




function get_position(E) {

	if (!E) return;

	var x = E.offsetLeft;
	var y = E.offsetTop;

	while (E.offsetParent != null) {
		E = E.offsetParent;
		x += E.offsetLeft;
		y += E.offsetTop;
	}

	return {left: x, top: y};

}


var edost_resize = new function() {
	var self = this
	var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://')
	var timer = false, template_width = 0, E_template_width = false, drawing = false
	var template_width_main, template_browser_height, scroll_Y, window_scroll_X, window_scroll_Y, double_update = false, template_param = {"mode": "full", "width": 0, "width2": 0, "fixed": 0, "top": 0}
	var	button_window_scroll_Y = false, button_window_width = 0, button_window_height = 0, window_scroll_disable = false

	var data = []
	var phone_width = 0, header_query = '', header_height = 0, header_width = 0, sticky = true, button_timer
	var loading_id = 'edost_data_loading';

	this.init = false
	this.device = ''
	this.os = ''
	this.mobile = ''
	this.count = ''
	this.browser_width = 0
	this.browser_height = 0
	this.template_2019 = false
	this.template_ico = 'T'
	this.template_compact = 'off';
	this.template_priority = false
	this.window_scroll_disable = false

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

		self.os = os;

		if (os == 'android' && c('mobile') || os != 'windows' && c('iphone') || c('ipod') || os == 'windows' && c('phone') || os == 'blackberry' && !c('tablet') || os == 'fxos' && c('mobile') || c('meego')) self.device = 'phone';
		else if (c('ipad') || os == 'android' && !c('mobile') || os == 'blackberry' && c('tablet') || os == 'windows' && c('touch') || os == 'fxos' && c('tablet')) self.device = 'tablet';

		self.mobile = (self.device == 'phone' || self.device == 'tablet' ? true : false);

		if (/MSIE 10/i.test(navigator.userAgent) || /MSIE 9/i.test(navigator.userAgent) || /rv:11.0/i.test(navigator.userAgent)) sticky = false;

	}

	this.bar = function(param) {

		if (param == 'start' || param == 'timer') button_window_scroll_Y = -1;
		if (param == 'timer') {
			if (button_timer != undefined) window.clearInterval(button_timer);
			var button_timer = window.setInterval("edost_resize.bar()", 40);
			return;
		}

		var s = self.get_scroll('y');
		var w = window.innerWidth;
		var h = window.innerHeight;

		if (!edost_window.in_array(param, ['loading', 'save', 'start']) && button_window_scroll_Y == s && button_window_width == w && button_window_height == h) return;

		button_window_scroll_Y = s;
		button_window_width = w;
		button_window_height = h;

		var E_data = document.getElementById('edost_data_div');
		var E_bar = document.getElementById('edost_bar');
		if (!E_data || !E_bar) return;

		var rect = E_data.getBoundingClientRect();

		if (param === 'loading') {
			// загрузка нового контента
			E_data.innerHTML = edost_office.loading;
			if (rect.top < 0) window.scrollBy(0, rect.top - 80);
			return;
		}
		if (edost_window.in_array(param, ['save', 'start'])) {
			// сохранение с блокировкой блока
			var E_fon = document.getElementById(loading_id + '_fon');
			if (!E_fon && param == 'save') {
				var E = document.createElement('DIV');
				E.style = 'position: absolute; background: #FFF; opacity: 0.6; z-index: 4;';
				E.id = loading_id + '_fon';
				E.innerHTML = '<div id="' + loading_id + '">' + edost_office.loading + '</div>';
				E_data.parentNode.insertBefore(E, E_data);
	            E_fon = document.getElementById(loading_id + '_fon');
			}
            if (E_fon) {
				E_fon.style.display = (param == 'save' ? 'block' : 'none');
				if (param == 'save') {
					E_loading = document.getElementById(loading_id);
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
		self.change_class(E_bar, ['', 'edost_bar_up'], up ? 1 : 0);
		E_bar.style.width = (up ? (rect.width-2) + 'px' : '');
		E_bar.style.left = (up ? (rect.left+1) + 'px' : '');

	}

	this.start = function() {

        scroll_Y = -1;
        template_width_main = -1;
		template_width = -1;
		template_browser_height = -1;
		window_scroll_disable = false;
		double_update = false;
		self.count = 0;

		if (timer != undefined) window.clearInterval(timer);

		var E = document.getElementById('edost_template_width_div');
		E_template_width = (E ? E : false);

		var E = document.getElementById('edost_template_data');
		if (!E) return;

		data = []
		var ar = E.value.split('|');
		for (var i = 0; i < ar.length; i++) {
			var s = ar[i].split(':');
			var s2 = s[0].split('-');
			var v = {"type": s2[0], "name": s[1], "id": s2[1] ? s2[1] : ''};
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
//		edost_ShowData(data, '', 20);

        if (!self.init) {
			self.init = true;

			var E = document.getElementById('edost_template_2019');
			if (E) {
				self.template_2019 = true;
				self.template_ico = E.getAttribute('data-ico');
				self.template_priority = E.getAttribute('data-priority');
				self.template_compact = E.getAttribute('data-compact');

				var s = E.getAttribute('data-window_scroll_disable');
				self.window_scroll_disable = (!s || s != 'N' ? true : false);

				if (self.template_compact == 'S') {
					var E2 = document.getElementById('ORDER_FORM');
					if (E2) {
						edost_resize.change_class(E2, ['edost_compact_main', 'edost_supercompact_main'], self.mobile ? 1 : 0);
						edost_resize.change_class(E2, ['edost_compact_main2', 'edost_supercompact_main2'], self.mobile ? 1 : 0);
					}
				}

				var s = (E.value != '' ? E.value.split(':') : '');
				if (s[1]) {
					s = s[1].split('|');
					phone_width = (s[0] && s[0]*1 > 0 ? s[0]*1 : 0);
					header_query = (s[1] ? s[1] : '');
					header_height = (s[2] && s[2]*1 > 0 ? s[2]*1 : 0);
					header_width = (s[3] && s[3]*1 > 0 ? s[3]*1 : 0);
				}

				window.addEventListener('resize', edost_resize.update);
				window.addEventListener('scroll', edost_resize.update);
//				window.addEventListener('mousewheel', edost_resize.update);
				if (edost_resize.mobile) window.addEventListener('orientationchange', edost_resize.update);
			}
		}

		drawing = false;
		if (self.template_2019) edost_resize.update();
		else {
			if (timer != undefined) window.clearInterval(timer);
			timer = window.setInterval("edost_resize.update()", 200);
		}

	}

	this.change_class = function(E, list, active) {

		if (!E) return;
		if (!active) active = 0;
		if (list[active] != '' && E.classList.contains(list[active])) return;
		for (var i = 0; i < list.length; i++) if (i != active && list[i] != '' && E.classList.contains(list[i])) E.classList.remove(list[i]);
		if (list[active] != '') E.classList.add(list[active]);

	}

	this.get_scroll = function(param) {
		if (param == 'x') return (window.pageXOffset != undefined ? window.pageXOffset : (document.documentElement && document.documentElement.scrollLeft || document.body && document.body.scrollLeft || 0) - document.documentElement.clientLeft);
		if (param == 'y') return (window.pageXOffset != undefined ? window.pageYOffset : (document.documentElement && document.documentElement.scrollTop || document.body && document.body.scrollTop || 0) - document.documentElement.clientTop);
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
		if (header_width == 0 || window.innerWidth < header_width) {
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
		if (self.template_2019 && id == undefined) {
			var E = document.getElementById('order_form_content');
			if (E) {
				var w = E.offsetWidth;
				var browser_h = window.innerHeight;
				if (w != template_width_main || template_browser_height != browser_h) {
					template_width_main = w;
					template_browser_height = browser_h;
					scroll_Y = -1;
					update_param = true;

					var E = document.getElementById('order_form_main');
					var E2 = document.getElementById('order_form_total');
					var E2_div = document.getElementById('order_form_total_div');
					var E_form = document.getElementById('ORDER_FORM');
					if (E && E2 && E2_div && E_form) {
						var ar = ['edost_template_total_full', 'edost_template_total_small', 'edost_template_total_off']; // все в компактном блоке, компактный блок без товаров, компактный блок отключен
						var a = (phone_width > 0 && window.innerWidth < phone_width ? false : true);
						var c = (template_width_main > 700 && a ? 0 : 2);
						if (c == 0) {
							var w2 = (template_width_main > 800 ? 250 : 230);
							var w = template_width_main - w2 - 20;
							E.style.width = w + 'px';
							E2.style.width = w2 + 'px';
							E2.style.display = 'block';

							if (sticky) E2_div.style = 'width: ' + w2 + 'px; position: sticky; position: -webkit-sticky; top: ' + (10 + jump*1) + 'px;';
							else E2_div.style.width = w2 + 'px';

							var E_cart = document.getElementById('order_total_cart');
							var E_cart_count = document.getElementById('order_total_cart_count');
							if (!E_cart) { if (E_cart_count) c = 1; }
							else if (E_cart_count) {
								self.change_class(E_form, ar, 0);
								var h = E2_div.offsetHeight + jump;
								if (h > template_browser_height - 40 || h > E.offsetHeight) c = 1;
								if (double_update === false) double_update = 1;
							}

							template_param.width = w;
							template_param.width2 = w2;
						}
						else {
							E.style.width = '100%';
							E2.style.display = 'none';

							template_param.width = 0;
							template_param.width2 = 0;
						}
						self.change_class(E_form, ar, c);
						template_param.mode = ar[c].substr(21);
					}
				}
			}
		}

		var resize = true;
		if (id != undefined) w = width;
		else {
			if (drawing && !self.template_2019) return;

			var browser_w = window.innerWidth;
			var browser_h = window.innerHeight;

			if (self.browser_width != browser_w || self.browser_height != browser_h) {
				edost_window.resize();
				edost_office.resize('redraw');
				if (edost_office2.inside) edost_office2.resize('redraw');
			}

			self.browser_width = browser_w;
			self.browser_height = browser_h;

			w = E_template_width.offsetWidth;
			if (w == 0) {
				self.start();
				return;
			}
			if (w == template_width) resize = false;
			else {
				template_width = w;
				drawing = true;
				E = document.getElementById('edost_template_width');
				if (E) E.value = w;
			}
		}
		if (resize) self.count++;
		if (resize) for (var i = 0; i < data.length; i++) {
			var v = data[i];

			var c = '';
			var active = 0;
			for (var i2 = 0; i2 < v.param.length; i2++)
				if (!(i2%2)) {
					c = v.param[i2];
					active = Math.floor(i2/2);
				}
				else if (v.param[i2] < w) break;

			if (id != undefined && v.id != id || id == undefined && v.id != '') continue;

			var ar = false;
			if (v.type == 'id') var ar = [document.getElementById(v.name)];
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

				self.change_class(v2, v.class, active);
			}
		}

		if (id == undefined) {
			drawing = false;
		}

		// прокрутка компактного блока "итого"
		if (!sticky && self.template_2019 && id == undefined) {
			var y = self.get_scroll('y');
			if (y != scroll_Y) {
				scroll_Y = y;
				update_param = true;

				var E = document.getElementById('order_form_main');
				var E2_div = document.getElementById('order_form_total_div');
				if (E && E2_div) {
					var rect = E.getBoundingClientRect();
					var top = Math.round(rect.top) + scroll_Y;

					var main_height = E.offsetHeight;
					var browser_h = window.innerHeight;
					var margin_top = 10;
					var up = margin_top + jump;
					var h = E2_div.offsetHeight;

					if (top < scroll_Y + up && h < main_height) {
						var h2 = Math.round(rect.height - 20);
						var y = top + h2 - (scroll_Y + h);

						if (y > up) y = up;
						E2_div.style.position = 'fixed';
						E2_div.style.top = y + 'px';

						template_param.fixed = 1;
						template_param.top = y;
					}
					else {
						E2_div.style.position = '';
						E2_div.style.top = margin_top + 'px';

						template_param.fixed = 0;
						template_param.top = margin_top;
					}
				}
			}
		}

		if (sticky && self.template_2019 && id == undefined) {
			var E = document.getElementById('order_form_main');
			var E2 = document.getElementById('order_form_total');
			if (E && E2) E2.style.height = (E.offsetHeight - 20) + 'px';
		}

		if (update_param) {
			var E = document.getElementById('edost_template_2019');
			if (E) E.value = template_param.mode + '|' + template_param.width + '|' + template_param.width2 + '|' + template_param.fixed + '|' + template_param.top;
		}

		if (double_update == 2) double_update = false;
		if (double_update == 1) {
			double_update = 2;
			template_width_main = -1;
			self.update();
		}

	}

}

edost_resize.get_device();




var edost_window_create = function(var_name, div_name) {
	var self = this
	var main_name = 'edost_window'
	var protocol = (document.location.protocol == 'https:' ? 'https://' : 'http://')
	var format, param_profile, onkeydown_backup = 'free', overflow_backup = false, scroll_backup = false, data_backup = false, onclose = '', window_width = 0, data_width = 0, browser_width = 0, browser_height = 0
	var onclose_set_office_start, mobile_jump = false, head_color_default = '#888', loading = false, scroll_reset = false, window_scroll = false, request = false
	var alarm_position = 0, alarm_fast, error_id = 0
	var arguments_backup = false, window_id = false
	var address_id = ['edost_city2', 'edost_street', 'edost_house_1', 'edost_house_2', 'edost_door_1']

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
					   '<div class="edost_button_base edost_button_date" style="background: #08C; font-size: 14px; padding: 4px 8px 5px 8px; line-height: 14px; margin-top: 20px;" onclick="edost_window.set(\'profile\', \'old\')">перейти к профилям сдачи</div>',
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

	if (var_name == undefined) var_name = main_name;
	div_name = (div_name != undefined ? '_' + div_name : '');

	var main_id = main_name + div_name
	var fon_id = main_name + '_fon' + div_name
	var head_id = main_name + '_head' + div_name
	var loading_id = main_name + '_loading' + div_name
	var name_id = main_name + '_name' + div_name
	var button_id = main_name + '_button' + div_name
	var save_id = main_name + '_save' + div_name
	var close_id = main_name + '_close' + div_name
	var data_id = main_name + '_data' + div_name
	var frame_id = main_name + '_frame' + div_name

	this.in_array = function(v, ar) {
		for (var i = 0; i < ar.length; i++) if (v == ar[i]) return true;
		return false;
	}

	this.value = function(o, q, v) {
		if (o && q != '') var o = o.querySelector(q);
		if (!o) return '';
		if (v == undefined) return o.value;
		else {
			if (o.tagName == 'SELECT' && !o.querySelector('option[value="' + v + '"]')) return;
			o.value = v;
		}
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

	this.config = function(param) {
		if (self.config_data[self.mode]) {
			 if (param === undefined) return self.config_data[self.mode];
			 else if (self.config_data[self.mode][param]) return self.config_data[self.mode][param];
		}
		return false;
	}

	this.get_error = function(c) {
		var s = 'код ' + c;
		var ar = [[22, 'cбой на сервере службы доставки'], [25, 'сбой подключения к серверу службы доставки'], [27, 'не задан почтовый индекс'], [28, 'не задан адрес'], [29, 'не задан номер заказа'],
			[30, 'не заданы данные получателя'], [31, 'не задан телефон'], [32, 'не задан email'], [33, 'не заданы данные заказа'], [34, 'не задан вес'], [35, 'не заданы габариты'], [36, 'не заданы данные упаковки'],
			[37, 'не верный формат номера сдачи'], [38, 'пропущено поле коментарий'], [39, 'даты сдач не совпадают'], [40, 'нет данных о сдаче'], [41, 'заказ не в сдаче'], [42, 'не смогли удалить сдачу'],
			[43, 'не смогли изменить дату сдачи'], [44, 'не смогли удалить всю сдачу (только часть)'], [45, 'не смогли переместить из сдачи в заказ'], [46, 'не смогли создать заказ без ШПИ'],
			[47, 'не верный формат даты сдачи'], [48, 'не верный тип тарифа'], [49, 'не верный тариф'], [51, 'не верный код страны'], [52, 'не верный код региона'], [55, 'не задан регион доставки'],
			[56, 'не задан город доставки'], [57, 'не задан тариф'], [58, 'не задан статус оплаты'], [59, 'не задан внутренний идентификатор заказа в магазине'], [60, 'не задан флаг'],
			[61, 'не передали все параметры'], [64, 'заказ уже передан в службу доставки'], [65, 'не смогли получить данные о сдаче'], [66, 'не смогли зарегистрировать сдачу в почтовом отделении'],
			[67, 'не смогли удалить заказ'], [69, 'не смогли добавить в сдачу'], [70, 'не смогли включить в сдачу'], [71, 'печать бланков для данного вида отправления на текущем этапе оформления недоступна'],
			[72, 'невозможно создать бланк (заказ не найден в системе службы доставки)'], [73, 'не смогли оформить заказ'], [74, 'превышен суточный лимит запросов к API службы доставки'], [75, 'внутренняя ошибка'],
			[76, 'населенный пункт не обслуживается'], [77, 'не верное значение НДС'], [78, 'не задан номер дома'], [79, 'не верный тип документа для печати бланков'], [80, 'в упаковке содержится не известный товар'],
			[81, 'не смогли удалить заказ, попробуйте выполнить операцию позже'], [82, 'данный заказ уже оформлен (для повторного оформления заказ необходимо вручную удалить в личном кабинете службы доставки)'],
			[84, 'на указанную дату и адрес уже существует заявка на вызов курьера'], [85, 'не смогли оформить заявку на вызов курьера'], [86, 'указанный заказ не оформлен'], [87, 'не смогли изменить профили'],
			[88, 'сегодня на указанное время вызов курьера невозможен (необходимо указать более позднее время или перенести вызов на другой день)'], [89, 'время ожидания курьера выходит за допустимый диапазон (с 09:00 до 18:00)'],
			[92, 'не смогли определить тариф'], [93, 'не верный почтовый индекс']];
		for (var i = 0; i < ar.length; i++) if (ar[i][0] == c) s = ar[i][1];
		s = '<div class="edost_warning" style="font-size: 20px;">ошибка</div><div style="font-size: 16px;">' + s + '</div>';
		return s;
	}

	this.error = function(E, v, type) {
		var error = '';
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
			if (type == 'zip') {
				E_zip = document.getElementById('edost_zip_required');
				if (E_zip && E_zip.value != 'Y') return false;
			}

			edost_resize.change_class(E, ['', 'edost_prop_error'], 1);
			E.oninput = new Function('event', var_name + '.input("update", this, event)');

			var E2 = E;
			if (type == 'passport') {
				E2 = document.getElementById('edost_location_passport_div');
				if (!E2) return false;
			}
			if (self.in_array(type, ['address', 'street', 'zip'])) {
				E2 = document.getElementById('edost_location_address_div');
				if (!E2) return false;
			}

			var s = E2.parentNode.getElementsByTagName('DIV');
			if (s) for (var i = 0; i < s.length; i++) if (s[i].classList.contains('edost_prop_error')) {
				id = s[i].getAttribute('data-error_id');
				if (!id) {
					error_id++;
					id = error_id;
					s[i].setAttribute('data-error_id', id);
				}
				E.setAttribute('data-error_id', id);
				s[i].innerHTML = '<span class="edost_prop_error">' + error + '</span>';
			}

			return true;
		}
        return false;
	}

	this.props = function(E) {

		var r = true;
		var first = false;
		var fast = (E ? true : false);

		if (!fast) {
			E = document.getElementById('order_auth');
			if (!E) E = document.getElementById('ORDER_FORM');
		}

		if (E) {
			var ar = E.getElementsByTagName('INPUT');
            if (ar) {
				for (var i = 0; i < ar.length; i++) if (fast || ar[i].id && ar[i].type != 'hidden' && (ar[i].name.indexOf('ORDER_PROP_') == 0 || self.in_array(ar[i].id, ['edost_agreement', 'edost_passport_2', 'edost_passport_3', 'edost_passport_4', 'edost_zip']))) {
					type = ar[i].getAttribute('data-type');
					if (!type && self.in_array(ar[i].id, ['edost_passport_2', 'edost_passport_3', 'edost_passport_4'])) type = 'passport';
					if (!type && self.in_array(ar[i].id, ['edost_zip'])) type = 'zip';
					if (!type) continue;

					v = (type == 'agreement' ? ar[i].checked : edost_office.trim(ar[i].value, true));

					if (self.error(ar[i], v, type)) {
						r = false;
						if (!first) first = ar[i];
					}
				}

				var E2 = document.getElementById('edost_address_hide');
				if (E2 && E2.value != 'Y') {
					var a = {};
					var city2_required = document.getElementById('edost_city2_required');
					for (var i = 0; i < ar.length; i++) if (self.in_array(ar[i].id, address_id)) a[ar[i].id] = (edost_office.trim(ar[i].value, true) != '' ? true : false);
					var a_city2 = (city2_required && city2_required.value == 'Y' && !a.edost_city2 ? true : false);
					var a_street = (!a.edost_street && !a.edost_house_1 && !a.edost_house_2 && !a.edost_door_1 ? true : false);
					if (a_city2 || a_street) {
						for (var i = 0; i < ar.length; i++) if (a_city2 && ar[i].id == 'edost_city2' || a_street && ar[i].id != 'edost_city2' && self.in_array(ar[i].id, address_id)) if (self.error(ar[i], '', 'address')) {
							r = false;
							if (!first) first = ar[i];
						}
					}
					else {
						var street_required = document.getElementById('edost_street_required');
						var area = document.getElementById('edost_area');
						if (street_required && street_required.value != '' && area && area.value == 'Y') {
							var street = document.getElementById('edost_street');
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
		if (s) for (var i = 0; i < s.length; i++) edost_resize.change_class(s[i], ['', 'edost_prop_blink'], 0);

		if (!r && first) self.alarm(first, fast);

		return r;

	}

	this.copy = function(E, save) {

		if (!E) return false;

		var r = true;

		var ar = E.getElementsByTagName('INPUT');
		if (ar) for (var i = 0; i < ar.length; i++) {
			name = ar[i].getAttribute('data-name');
			if (!name) continue;

			type = ar[i].type;
			s = ar[i].getAttribute('data-type');
			if (s) type = s;

			var E2 = document.getElementById(name);
			if (!E2) continue;

            if (save) {
				if (type == 'checkbox') E2.checked = ar[i].checked;
				else E2.value = edost_office.trim(ar[i].value, true);
            }
            else {
            	if (type == 'checkbox') ar[i].checked = E2.checked;
            	else ar[i].value = E2.value;
            }
		}

		if (save) r = self.props(E);

		if (!r) self.resize();

		return r;

	}

	this.alarm = function(E, fast) {

		if (E) {
			alarm_fast = fast;
			var rect = E.getBoundingClientRect();
			var browser_h = window.innerHeight;

			if (rect.top < 150) alarm_position = rect.top - 150;
			else if (rect.top > browser_h - 250) alarm_position = rect.top - browser_h + 250;
			else alarm_position = 0;
		}

		if (!fast && alarm_position != 0) {
			var y = alarm_position*0.2;
			if (Math.abs(y) > 40) y = (y < 0 ? -40 : 40);
			window.scrollBy(0, y);
			alarm_position -= y;
			if (Math.abs(alarm_position) > 1) {
				window.setTimeout('edost_window.alarm()', 25);
				return;
			}
		}

        var s = document.querySelectorAll((alarm_fast ? '#edost_window ' : '') + 'div.edost_prop_error span');
		if (s) for (var i = 0; i < s.length; i++) edost_resize.change_class(s[i], ['', 'edost_prop_blink'], 1);

	}

	this.input = function(param, E, event) {

		if (param == 'focus') {
			if (edost_resize.mobile) {
				var rect = E.getBoundingClientRect();
				mobile_jump = rect.top - 10;
				self.resize();
			}
		}
		if (param == 'blur') {
			mobile_jump = false;
			if (edost_resize.mobile) self.resize();
		}

		if (param == 'update') {
			if (E.value != '' && E.classList.contains('edost_prop_error')) {
				E.classList.remove('edost_prop_error');

				if (self.in_array(E.id, address_id)) for (var i = 0; i < address_id.length; i++) {
					var E2 = document.getElementById(address_id[i]);
					if (E2) E2.classList.remove('edost_prop_error');
				}

		        var id = E.getAttribute('data-error_id');
		        if (id) {
			        var s = document.querySelector('div[data-error_id="' + id + '"]');
					if (s) s.innerHTML = '';
				}
			}
			self.resize();
		}

		if (E.type == 'tel') {
			var p = E.selectionStart;
			var v = E.value;
			var v2 = v.replace(/^\s*/, '').replace(/[^0-9+\)\( -]/g, '');
			var n = v.replace(/[^0-9]/g, '');
			var s = v.length;

			var vo = E.getAttribute('data-value');
			if (vo === null) vo = '';
			var no = vo.replace(/[^0-9]/g, '');

			E.setAttribute('data-value', v2);

			var vl = v.length;
			var vol = vo.length;
			var nl = n.length;
			var nol = no.length;

			var c = '', cl = 0;
			var ar = ['+7', '+375', '+996', '+374'];
			for (var i = 0; i < ar.length; i++) if (v2.indexOf(ar[i]) == 0) { c = ar[i]; cl = ar[i].length - 2; break; }

			if (v.length != v2.length) {
				E.value = v2;
				E.setSelectionRange(p-1, p-1);
			}
			else if (c != '') {
				if (nol == 0 && nl > 5) {
					if (c == '+7' && nl == 11) {
						E.value = '+7-' + n[1]+n[2]+n[3] + '-' + n[4]+n[5]+n[6] + '-' + n[7]+n[8] + '-' + n[9]+n[10];
					}
				}
				else {
					if (v2 == c && vol == vl-1 || c == '+7' && n[1] == 9 && (nl == 4 && nol == 3 || nl == 7 && nol == 6 || nl == 9 && nol == 8)) E.value = v2 + '-';
					if (c == '+7' && nl > 11) {
						E.value = v2.substr(0, v2.length-1);
						E.setSelectionRange(p-1, p-1);
					}
				}
			}
		}

	}

	this.submit = function(value, param) {

		var mode = self.mode;

		if (mode == 'batch_delete') {
			edost_SetControl(self.param.id, 'batch_delete', self.param.E);
			self.set('close_full');
			return;
		}
		if (mode == 'call') {
			request = true;
			edost_Setting('batch', 'post', 'id=' + self.param.id + '&call=1&save=Y');
			return;
		}
		if (mode == 'profile') {
			if (self.register_reload_onclose) {
				request = true;
				edost_Setting('batch', 'post', 'id=' + self.param.id + '&profile_shop=' + self.param.profile_shop + '&profile_delivery=' + self.param.profile_delivery + '&save=Y');
			}
			else self.set('close_full');
			return;
		}
		if (mode == 'batch_date') {
			if (self.param.date_error) return;
			var E = document.getElementById('window_edost_register_date');
			if (E) edost_Register('button', 'button_date|' + self.param.id, E.value)
			self.set('close_full');
			return;
		}
		if (mode == 'order_batch_delete') {
			edost_SetControl(self.param.id, 'order_batch_delete', true);
			self.set('close_full');
			return;
		}
		if (mode == 'register_delete2') {
			edost_SetControl(self.param.id, 'delete_register', true);
			self.set('close_full');
			return;
		}
		if (mode == 'register_delete') {
			if (value == 'delete') edost_SetControl(self.param.id, 'delete_register', true);
			if (value == 'date') {
				if (self.param.date_error) return;
				var E = document.getElementById('window_edost_register_date');
				if (E) edost_SetControl(self.param.id, 'order_date', E.value);
			}
			self.set('close_full');
			return;
		}
		if (mode == 'profile_setting') {
			edost_window.set('profile_setting_new');
			return;
		}
		if (self.in_array(mode, ['profile_setting_new', 'profile_setting_change'])) {
			var E = document.getElementById('edost_profile_id');
			var id = (E ? E.value : '');

			var post = [];
	        var s = document.querySelectorAll('#' + main_id + ' input, #' + main_id + ' select');
			if (s) for (var i = 0; i < s.length; i++) {
				if (s[i].type == 'checkbox' && !s[i].checked) continue;
				var v = '';
				if (s[i].type == 'checkbox') v = 1;
				else v = edost_office.trim(s[i].value.replace(/\=/g, ' ').replace(/\|/g, ' '), true);
				post.push(s[i].name + '=' + v);
			}
			post = post.join('|');

			self.register_reload_onclose = self.reload = true;
			if (data_backup.mode == 'profile') {
				self.close_onset = true;
				post += '|local=1';
			}

			edost_Setting(mode, 'save', post);
			return;
		}
		if (mode == 'option') {
			var post = [];
			var E = document.getElementById(data_id + '_div');
	        var s = E.querySelectorAll('div.edost_option_service input');
			if (s) for (var i = 0; i < s.length; i++) {
				var v = s[i].getAttribute('data-id');
				if (s[i].checked) post.push(v);
			}

			self.set('close_full');
			edost_Package('save', post.join(','), self.param);

			return;
		}
		if (mode == 'option_setting') {
			var E = document.getElementById(main_id);
    	    var post = [];
    	    var s = E.querySelectorAll('input[type="radio"]:checked');
			if (s) for (var i = 0; i < s.length; i++) {
				var name = s[i].name.replace('edost_service_', '');
				post.push(name + ',' + s[i].value);
			}

			self.register_reload_onclose = true;
			edost_Setting(mode, 'save', post.join(':'));

			return;
		}

		// согласие
		if (mode == 'agreement' || mode == 'agreement_fast') {
			self.set('close_full');
			edost_Agreement('set', mode == 'agreement_fast' ? true : false);
			return;
		}
		// быстрое оформление заказа
		if (mode == 'fast') {
			var E = document.getElementById(data_id);
			if (!E || !self.copy(E, true)) return;
			self.set('close_full');
			edost_Agreement('submit', true);
			return;
		}

		var cod = (self.cod ? self.cod : false);

		if (param != undefined) {
			edost_office.window(param);
			return;
		}

		if (value) {
			self.set('close_full');
			var E = document.getElementById(value);
			if (E) E.checked = true;
		}

		if (cod) {
			var E = document.getElementById(cod);
			if (E) {
				E.disabled = false;
				E.checked = true;
			}
		}

		if (!value) return;

		if (value.indexOf('PAY_SYSTEM') > 0) changePaySystem();
		else submitForm();

    }

	this.get_param = function(v) {
		var r = {};
		if (typeof v === 'object') {
			r.E = v;
			var p = v.getBoundingClientRect();
			r.position = [p.left + p.width*0.5, p.top];
			if (!r.position[0] && self.param.position) r.position = self.param.position;
			v = v.getAttribute('data-param');
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
//		alert(param + ' | ' + mode + ' [' + self.mode + ' | ' + data_backup.mode + ']');

		loading = false;

		var show = (!self.in_array(mode, ['close_full', 'close', 'esc']) ? true : false);

		self.copy_id = '';
		if (self.in_array(mode, ['close', 'esc']) && data_backup !== false && data_backup.mode != self.mode && !request) {
			// восстановление основного окна при закрытии вторичного
			show = true;

			var E_data = document.getElementById(data_id);
			E_data.innerHTML = data_backup.html;

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
			if (mode && self.mode && mode != self.mode && !self.in_array(self.mode, ['paysystem', 'call_profile'])) {
				var E_data = document.getElementById(data_id);
				data_backup = {"html": (E_data ? E_data.innerHTML : ''), "scroll": (E_data ? E_data.scrollTop : 0), "mode": self.mode, "param": edost_window.clone(self.param)};
				E_data.innerHTML = '';
			}

			if (param !== 'old') self.param = self.get_param(param);
			self.copy_id = mode;
			if (mode == 'option') self.copy_id += '_' + self.param.company;
			if (mode == 'package_detail') self.copy_id += '_' + self.param.id;

			window_scroll = false;
			scroll_reset = true;
		}

		if (self.in_array(mode, ['close_full']) && self.mode == 'agreement') edost_Agreement('unset');
		if (!show && self.register_reload_onclose && self.in_array(self.mde, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile'])) edost_SetParam('register', 'reload');

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
			var E = document.getElementById(data_id);
			if (E) self.copy(E, true);
		}
		self.save = (self.config('save') ? true : false);

		if (self.param.cod_id) self.cod = self.param.cod_id;
		data_width = 0;

		if (!show) {
			if (edost_resize.template_2019) edost_resize.scroll('recover', scroll_backup);

		    document.onkeydown = onkeydown_backup;
		    onkeydown_backup = 'free';
			data_backup = false;
			self.mode = '';
			self.register_reload_onclose = self.close_onset = self.reload = false;
            self.cod = false;
		}
		else {
			if (onkeydown_backup == 'free') {
	    		if (edost_resize.template_2019) scroll_backup = edost_resize.scroll('save');

			    onkeydown_backup = document.onkeydown;
				document.onkeydown = new Function('event', 'if (event.keyCode == 27) ' + var_name + '.set("esc");');
			}
		}

		var E_main = document.getElementById(main_id);
		if (!E_main) {
			var E = document.body;

			var E2 = document.createElement('DIV');
			E2.className = 'edost_window_fon';
			E2.id = fon_id;
			E2.style.display = 'none';
			E2.onclick = new Function('', var_name + '.set("close")');
			E2.innerHTML = '<div id="' + loading_id + '">' + edost_office.loading + '</div>';
			E.appendChild(E2);

			var E2 = document.createElement('DIV');
			E2.id = main_id;
			E2.style.display = 'none';

			var s = '';
			s += edost_office.close.replace('%onclick%', var_name + ".set('close')");
			s += '<div id="' + head_id + '">';
			s += '<div id="' + name_id + '"></div>'
			s += '<div id="' + button_id + '" style="display: none;"><div class="edost_button_window_close" style="display: inline-block;" onclick="' + var_name + '.set(\'close\');">закрыть</div></div>';
			s += '</div>';
			s += '<div id="' + data_id + '"></div>';
			s += '<div id="' + save_id + '">';
			s += '<div class="edost_button_div">';
			s += '<div class="edost_button_left">';
			s += '<div class="edost_button_save" onclick="' + var_name + '.submit();">сохранить</div>';
			s += '<div class="edost_button_new" onclick="' + var_name + '.submit(0);">создать</div>';
			s += '<div class="edost_button_yes" onclick="' + var_name + '.submit();">да</div>';
			s += '</div>';
			s += '<div class="edost_button_right">';
			s += '<div class="edost_button_cancel" onclick="' + var_name + '.set(\'close\');">отменить</div>';
			s += '<div class="edost_button_close" onclick="' + var_name + '.set(\'close\');">закрыть</div>';
			s += '<div class="edost_button_no" onclick="' + var_name + '.set(\'close\');">нет</div>';
			s += '</div>';
			s += '</div>';
			s += '</div>';
			E2.innerHTML = s;
			E.appendChild(E2);

			E_main = document.getElementById(main_id);
			if (!E_main) return;
		}


		var copy_id = (self.copy_id ? 'edost_' + self.copy_id + '_div' : '');

		var c = ' ' + (copy_id && copy_id == 'edost_paysystem_div' ? copy_id : 'edost_delivery_div');

		var E_head = document.getElementById(name_id);
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
		E_main.className = s.join(' ');

		var display = (!show ? 'none' : 'block');

		var E_data = document.getElementById(data_id);
		if (!E_data) return;
		E_data.className = 'edost edost_window_data' + c;

		E_main.style.display = display;
		E_main.style.zIndex = (config.up ? '10582' : '');

		var E = document.getElementById(fon_id);
		if (E) {
			E.style.display = display;
			E.style.zIndex = (config.up ? '10580' : '');
		}

		var resize = true;
		if (config.confirm) {
			// текстовые данные
			E_data.innerHTML = config.confirm;
		}
		else if (self.in_array(self.mode, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile']) && self.copy_id !== false) {
			// загрузка данных извне
			resize = false;
			var v = '';
			if (self.param.type || self.mode == 'profile_setting_new') v = 0;
			if (self.param.local && self.param.change) v = data_backup.param['profile_' + self.param.type];
			else if (self.in_array(self.mode, ['profile', 'profile_setting_change'])) v = self.param.id;
			E_data.innerHTML = '';
			edost_Setting(self.mode, 'get', v);
		}
		else if (self.mode == 'frame') {
			// страница пункта выдачи
			E_data.innerHTML = '<iframe id="' + frame_id + '" style="display: block;" src="' + self.param.href + '" frameborder="0" width="100%"></iframe>';
		}
		else if (copy_id) {
			// копирование данных из документа
			var E = document.getElementById(copy_id);
			if (E) {
				if (!show) E_data.innerHTML = '';
				else {
					var s = '';
					s += '<div id="' + data_id +  '_width"></div>';
					s += '<div id="' + data_id +  '_div">';
					s += E.innerHTML;

					if (self.in_array(self.mode, ['register_delete', 'batch_date'])) s = s.replace(/edost_register_date/g, 'window_edost_register_date');
					if (self.mode == 'fast' || self.mode == 'agreement' || self.mode == 'agreement_fast') s = s.replace(/submitForm\('Y'\)/g, 'window.' + var_name + '.submit(this)');
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
			if (self.in_array(self.mode, ['register_delete', 'batch_date'])) {
				var E = document.getElementById('window_edost_register_date');
				if (E && E.value == self.param.batch_date) E.value = E.getAttribute('data-date');
			}

			if (self.mode == 'profile' && resize) self.resize('set_profile', E_data);

			if (self.mode == 'option') {
		        var s = E_data.querySelectorAll('div.edost_option_service input');
				if (s) for (var i = 0; i < s.length; i++) {
					var v = s[i].getAttribute('data-id');
					s[i].checked = (self.in_array(v, self.param.service) ? true : false);
				}
			}
			if (self.mode == 'fast') self.copy(E_data);
		}

		if (!show) return;

		var E = document.getElementById(main_id);
		if (!E || E.style.display == 'none') return;

		if (resize) self.resize();

//		self.fit();
//		if (self.timer_resize != undefined) window.clearInterval(self.timer_resize);
//		self.timer_resize = window.setInterval(var_name + '.fit("resize")', 400);

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
				self.value(value, 'select[name="edost_' + s + '"]', self.param[s]);
			}
			return;
		}

		if (self.in_array(self.mode, ['register_delete', 'batch_date']) && param == 'error') self.param.date_error = value;

		if (self.in_array(self.mode, ['profile_setting', 'profile_setting_new', 'profile_setting_change', 'option_setting', 'profile', 'call']) && param == 'set') {
			loading = false;

			if (self.close_onset) { self.close_onset = false; self.set('close', value); return; }
			if (value === 'close') { self.set('close'); return; }

			if (request) {
				request = false;
				var s = value.split('error:');
				if (!s[1]) {
					if (self.mode == 'call') {
						var E = document.getElementById('edost_call_button_' + self.param.id);
						if (E) E.style.display = 'none';
						var E = document.getElementById('edost_call_' + self.param.id);
						if (E) E.style.display = 'block';
					}
					self.set('close_full');
					return;
				}
				value = self.get_error(s[1]);
				self.mode = 'error';
				self.save = false;
			}

			var E_data = document.getElementById(data_id);
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
						s += '<option value="' + p[0] + '"' + (p[0] == ar[i].value ? ' selected' : '') + '>' + p[3] + (!self.in_array(p[3], ['Москва', 'Санкт-Петербург', 'Севастополь']) ? ' (' + regions[0][p[2]] + ')' : '') + '</option>';
					}
					s = '<select name="' + ar[i].name + '" style="width: 100%;">' + s + '</select>';
					ar[i].parentNode.innerHTML = s;
				}

				if (self.param.type == 'delivery') {
					var E2 = E_data.querySelector('select[name="type"]');
					if (E2) E2.value = self.param.company;
				}
			}
		}

		var E = document.getElementById(main_id);
		if (!E || E.style.display == 'none') return;

		// размер окна браузера
		var browser_w = window.innerWidth;
		var browser_h = window.innerHeight;

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

		E.style.width = window_w + 'px';

		var E_data = document.getElementById(data_id);
		var top = E_data.scrollTop;
		var landscape = (fullscreen && browser_width > 500 && browser_height < 450 && browser_width > browser_height ? true : false);
		var mobile = (fullscreen || edost_resize.mobile ? true : false);
		var landscape_head_width = (config.landscape_head_width ? config.landscape_head_width : 110);

		// адаптация контента
		var E_width = document.getElementById(data_id);
		if (E_width && window_w != data_width) {
			data_width = window_w;
			edost_resize.update('window', window_w);
		}

		var c = 0;
		if (self.mode == 'fast') {
			var w = window_w - (landscape ? landscape_head_width : 0)
			c = (w > 350 ? 1 : 2);
		}
		edost_resize.change_class(E, ['', 'edost_props_normal', 'edost_props_small'], c);

		edost_resize.change_class(E, ['', 'edost_window_fullscreen'], fullscreen ? 1 : 0);

		if (param !== 'loading') {
			if (self.mode == 'profile') {
				for (var i = 0; i <= 1; i++) {
					var s = 'profile_' + (i == 0 ? 'shop' : 'delivery');
					self.param[s] = self.value(E_data, 'select[name="edost_' + s + '"]');
					edost_resize.change_class(E, ['', 'edost_' + s + '_change_main'], self.param[s] ? 1 : 0);
				}
			}
			if (self.mode.indexOf('profile_setting') == 0) {
				edost_resize.change_class(E, ['', 'edost_field_new'], self.mode == 'profile_setting_new' ? 1 : 0);

				var E2 = E.querySelector('select[name="type"]');
				var c = (E2 && E2.value == 'shop' ? 1 : 2);
				edost_resize.change_class(E, ['', 'edost_field_shop', 'edost_field_company'], c);

				var E2 = E.querySelector('select[name="mode"]');
				var c = (c != 2 || E2 && E2.value == 'N' ? 0 : 1);
				edost_resize.change_class(E, ['', 'edost_field_contract'], c);
			}
		}

		var c = 0;
		if (edost_resize.mobile)
			if (!fullscreen) c = 1;
			else if (browser_width < browser_height && browser_width < 450 && browser_height < 700 || browser_width > browser_height && browser_width < 700 && browser_height < 450) c = 3;
			else c = 2;
		var ar = ['edost_device_pc', 'edost_device_tablet', 'edost_device_tablet_small', 'edost_device_phone'];
		var device = ar[c].substring(13);
		edost_resize.change_class(E, ar, c);

		var c = 0;
		if (mobile) c = (landscape ? 2 : 1);
		edost_resize.change_class(E, ['', 'edost_window_mobile', 'edost_window_landscape'], c);

		var E_head = document.getElementById(head_id);
		var E_name = document.getElementById(name_id);
		var E_button = document.getElementById(button_id);
		var E_buffer = document.getElementById(data_id + '_buffer');
		var E_save = document.getElementById(save_id);
		var E_frame = document.getElementById(frame_id);

		E_save.style.display = (E_save && self.save ? 'block' : 'none');
		edost_resize.change_class(E, ['', 'edost_button_save_main', 'edost_button_new_main', 'edost_button_close_main', 'edost_button_yes_main',], config.save);

		if (self.in_array(self.mode, ['register_delete', 'batch_date'])) edost_resize.change_class(E, ['', 'edost_button_date_error'], self.param.date_error ? 1 : 0);
		if (self.mode == 'batch_date') edost_resize.change_class(E, ['', 'edost_call_warning_main'], self.param.call ? 1 : 0);

		edost_resize.change_class(E, ['', 'edost_error'], self.mode == 'error' ? 1 : 0);

		E.style.opacity = (loading && config.loading ? '0.01' : 1);

		var agreement = (self.mode == 'agreement' || self.mode == 'agreement_fast' ? true : false);
		if (agreement) {
			landscape_head_width = 140;
			var E_agreement = document.getElementById('window_edost_agreement_text');
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

			if (agreement) E_agreement.style.height = (window_h - 120) + 'px';

			E_data.style.marginTop = (h_data >= window_h ? 0 : Math.round((window_h - h_data)*0.5) + 'px');
			E_data.style.height = window_h + jump + 'px';
			E_name.style.marginTop = Math.round((window_h - h)*0.5) + 'px';
		}
		else {
			E_name.style.marginTop = 0;
			E_data.style.width = 'auto';
			E_data.style.marginLeft = E_data.style.marginTop = 0;

			if (agreement) {
				var h = window_h - 180;
				if ((device == 'pc' || device == 'tablet') && h > 350) h = 350;
				E_agreement.style.height = h + 'px';
			}

			if (!fullscreen || self.mode == 'fast' && mobile) E_data.style.height = 'auto';

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

				if (mobile)
					if (mobile_jump !== false) {
					}
					else if (self.mode == 'fast' && h_data + 40 > h) {
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

		E.style.borderRadius = (fullscreen ? 0 : '8px');
		E.style.width = window_w + 'px';
		E.style.height = window_h + jump + 'px';
		if (self.param.position) {
			var x = Math.round(self.param.position[0] - window_w*0.5);
			var y = Math.round(self.param.position[1] - window_h + 80);
		}
		else {
			var x = Math.round((browser_w - window_w)*0.5);
			var y = Math.round((browser_h - window_h)*0.5);
		}

		if (!fullscreen && mobile_jump === false) {
			if (y < 15) y = 15;
			if (y + window_h > browser_height - 15) y = browser_height - 15 - window_h;
			if (x < 15) x = 15;
			if (x + window_w > browser_width - 40) x = browser_width - 40 - window_w;
		}

		if (mobile_jump !== false) {
			E.style.position = 'absolute';
			var h = - (!fullscreen ? 70 : mobile_jump) + (!landscape ? 20 : 0);
			if (!landscape && !fullscreen && browser_height > 800) h = 20;
			E.style.top = (edost_resize.get_scroll('y') + h) + 'px';
		}
		else {
			E.style.position = '';
			E.style.top = y + 'px';
		}

		E.style.left = x + 'px';

		if (window_scroll !== false) top = window_scroll; else if (scroll_reset) top = 0;
		E_data.scrollTop = top;
		scroll_reset = false;

		var E_loading = document.getElementById(loading_id);
		if (E_loading) {
			E_loading.style.display = (loading ? 'block' : 'none');

			if (loading) {
				E_loading.style.width = E.style.width;
				E_loading.style.height = E.style.height;
				E_loading.style.left = E.style.left;
				E_loading.style.top = E.style.top;

		        var E2 = E_loading.querySelector('div');
		        if (E2) E2.style.paddingTop = Math.round((window_h)*0.5 - 32) + 'px';
			}
		}
	}

}

var edost_window = new edost_window_create();




// проверка параметров отправления
function edost_CheckPackage(tariff, weight, size, batch_oversize) {

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


function edost_ShowData(data, prefix, size, main) {
	if (main == undefined) main = true;
	var s = '';
	for (k in data) {
		s += prefix + " " + k + ": ";
		if (data[k] && 'object' === typeof data[k] && prefix.length < size-1) {
			s += '\r\n' + edost_ShowData(data[k], prefix + "    ", size, false)
		}
		else s += data[k] + "\r\n";
		if (main) s += '\r\n';
	}
	if (main) alert(s);
	else return s;
}
