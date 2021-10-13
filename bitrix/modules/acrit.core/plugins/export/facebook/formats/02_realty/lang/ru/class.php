<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_REALTY_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB недвижимость';
$MESS[$strMessPrefix . 'DESCRIPTION'] = 'test';

// Default settings
$MESS[$strMessPrefix . 'SETTINGS_TITLE'] = 'Заголовок файла (тег title)';
$MESS[$strMessPrefix . 'SETTINGS_TITLE_HINT'] = 'Укажите здесь заголовок файла.';

$MESS[$strMessPrefix . 'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/acrit.exportproplus/facebook_realty.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/facebook_goods.xml</code>';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN'] = 'Открыть файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN_TITLE'] = 'Файл откроется в новой вкладке';

// Headers
// Fields
$MESS[$strName . 'home_listing_id'] = 'Уникальный идентификатор дома';
$MESS[$strHint . 'home_listing_id'] = '<b>Требуется для динамической рекламы и торговли.</b><br>
Уникальный идентификатор дома/апартаментов/квартиры в листинге; возможно наиболее детализированный идентификатор.<br>
Пример: FB_home_1234
';
$MESS[$strName . 'home_listing_group_id'] = 'Уникальный идентификатор(в рамках группы)';
$MESS[$strHint . 'home_listing_group_id'] = '<b>Не применимо к динамическим объявлениям. Необязательный для коммерции.</b><br>
Уникальный идентификатор дома или квартиры. Должен быть уникальным для каждой группы.
';
$MESS[$strName . 'name'] = 'Заголовок объявления';
$MESS[$strHint . 'name'] = '<b>Требуется для динамической рекламы и торговли.</b><br>
Заголовок объявления о доме. <br>Пример:Modern Eichler in Green Oaks
';
$MESS[$strName . 'availability'] = 'Текущая доступность';
$MESS[$strHint . 'availability'] = '<b>Требуется для динамической рекламы и торговли.</b><br>
Текущая доступность недвижимости. <br>
Поддерживаемые значения:<br> for_sale, for_rent, sale_pending, recently_sold, off_market, available_soon. <br>Для коммерции единственное поддерживаемое значение - for_rent.
';
$MESS[$strName . 'address@format'] = 'Значения поля не менять!';
$MESS[$strName . 'address.component@name'] = 'Значения поля не менять!';
$MESS[$strHint . 'address@format'] = 'Техническое поле, изменять его нет необходимости!';
$MESS[$strHint . 'address.component@name'] = 'Техническое поле, изменять его нет необходимости!';
$MESS[$strName . 'address.component'] = 'Почтовый адрес собственности';
$MESS[$strHint . 'address.component'] = '<b>Требуется для динамической рекламы и торговли .</b><br>
Почтовый адрес собственности, который должен быть сопоставим с ее местонахождением.<br>
Проставить соответственно поля addr1[Улица],city[Город],region[Регион],country[Страна],postal_code[Индекс] <br>
См. <a  target="_blank" href="https://developers.facebook.com/docs/marketing-api/real-estate-ads/get-started#address-object">Параметры объекта адреса</a>. <br>
';

$MESS[$strName . 'latitude'] = 'Широта';
$MESS[$strHint . 'latitude'] = '<b>Требуется для динамической рекламы и торговли .</b><br>
Широта листинга.<br>
Пример: 37.484100
';
$MESS[$strName . 'longitude'] = 'Долгота';
$MESS[$strHint . 'longitude'] = '<b>Требуется для динамической рекламы и торговли .</b><br>
Долгота листинга.<br>
Пример: -122.148252
';
$MESS[$strName . 'neighborhood'] = 'Список окрестностей';
$MESS[$strHint . 'neighborhood'] = '<b>Требуется для динамической рекламы. Необязательно, но настоятельно рекомендуется для торговли.</b><br>
Максимальное количество: 20<br>
Список окрестностей для собственности. Может иметь несколько районов.<br>
Пример: Menlo Oaks
';
$MESS[$strName . 'price'] = 'Цена продажи или аренды';
$MESS[$strHint . 'price'] = '<b>Требуется для динамической рекламы и торговли .</b><br>
Цена продажи или аренды недвижимости. Отформатируйте цену как стоимость, за которой следует [трехзначный код валюты ISO] (https://en.wikipedia.org/wiki/ISO_4217?fbclid=IwAR0_xYfUmL3kIUA6sMeEaFAzbJa4MLeMiPDPrftFSX6wkKiTXxPinC-5j70 ", пробел между.<br>
Пример: 13,999 USD
';
$MESS[$strName . 'image.url'] = 'URL-адрес изображения';
$MESS[$strHint . 'image.url'] = '<b>Требуется для динамической рекламы и торговли .</b><br>
Максимум изображений: 20<br>
Максимальный размер: 4 МБ<br>
URL-адрес изображения, используемого в вашем объявлении.<br>
Для квадратного (1: 1) соотношения сторон в формате карусельной рекламы ваше изображение должно быть 600 x 600.<br>
Для одинарных графических объявлений размер изображения должен быть не менее 1200x630 пикселей.<br>
Для коммерции первая фотография отображается в коммерческом фиде как изображение обложки.<br>
См. <a href="https://developers.facebook.com/docs/marketing-api/real-estate-ads/get-started#image-object" target="_blank">Параметры объекта изображения</a>.
';
$MESS[$strName . 'image.tag'] = 'тег изображения';
$MESS[$strHint . 'image.tag'] = 'К изображению добавлен тег, который показывает, что на нем изображено. С изображением может быть связано несколько тегов.<br>
Примеры: Fitness Center,Swimming Pool<br>
INSTAGRAM_STANDARD_PREFERRED- Позволяет рекламодателям пометить определенное изображение в своем фиде как изображение по умолчанию, которое будет использоваться для Instagram. Этот тег чувствителен к регистру.
';
$MESS[$strName . 'url'] = 'Ссылка на страницу со списком недвижимости';
$MESS[$strHint . 'url'] = '<b>Требуется для динамической рекламы и торговли .</b><br>
Ссылка на страницу со списком недвижимости. Должен быть действующий URL.<br>
См. Параметры объекта изображения .<br>
Пример: http://www.realestate.com
';
$MESS[$strName . 'description'] = 'Описание';
$MESS[$strHint . 'description'] = '<b>Необязательный для динамических объявлений. Требуется для коммерции.</b><br>
Максимальное количество персонажей: 5000<br>
Описание недвижимости.<br>
Пример: Beautiful 3BD home available in Belmont
';
$MESS[$strName . 'num_beds'] = 'Общее количество спален';
$MESS[$strHint . 'num_beds'] = '<b>Необязательный для динамических объявлений. Требуется для коммерции.</b><br>
Общее количество спален. Можно 0 для студий.<br>
Пример: 2
';
$MESS[$strName . 'num_baths'] = 'Общее количество санузлов';
$MESS[$strHint . 'num_baths'] = '<b>Необязательный для динамических объявлений.</b><br>
Общее количество санузлов. <b>Для коммерции должно быть 1минимум.</b>
';
$MESS[$strName . 'num_rooms'] = 'Общее количество комнат ';
$MESS[$strHint . 'num_rooms'] = '<b>Неприменимо для динамических объявлений. Требуется для коммерции.</b><br>
Общее количество комнат в собственности.
';
$MESS[$strName . 'property_type'] = 'Тип недвижимости';
$MESS[$strHint . 'property_type'] = '<b>Необязательный для динамических объявлений.</b><br>
Тип недвижимости. <br>
Поддерживаемые значения для динамических объявлений: <br>apartment, condo, house, land, manufactured, other, townhouse. <br><br>
Поддерживаемые значения для торговли: <br>apartment, builder_floor, condo, house, house_in_condominium, house_in_villa, loft, penthouse, studio, townhouse, other.
';
$MESS[$strName . 'listing_type'] = 'Тип объекта недвижимости';
$MESS[$strHint . 'listing_type'] = '<b>Необязательный для динамических объявлений.</b><br>
Тип объекта недвижимости. <br>
Поддерживаемые значения для динамических объявлений: <br>
for_rent_by_agent, for_rent_by_owner, for_sale_by_agent, for_sale_by_owner, foreclosed, new_construction, new_listing. <br><br>
Поддерживаемые значения для торговли: <br>for_rent_by_agent, for_rent_by_owner.
';
$MESS[$strName . 'area_size'] = 'Площадь или пространство';
$MESS[$strHint . 'area_size'] = '<b>Неприменимо для динамических объявлений. Требуется для коммерции.</b><br>
Площадь или пространство листинга поэтажного плана.
';
$MESS[$strName . 'area_unit'] = 'Единицы (квадратные футы или квадратные метры)';
$MESS[$strHint . 'area_unit'] = 'Неприменимо для динамических объявлений. Требуется для коммерции.<br>
Единицы (квадратные футы или квадратные метры) значения площади пола. <br>
Поддерживаемые значения: sq_ft, sq_m.
';
$MESS[$strName . 'ac_type'] = 'Тип кондиционера';
$MESS[$strHint . 'ac_type'] = '<b>Неприменимо для динамических объявлений. Необязательный для коммерции.</b><br>
Тип кондиционера. <br>Поддерживаемые значения:<br> central, other, none.
';
$MESS[$strName . 'furnish_type'] = 'Тип имеющейся в объекте мебели';
$MESS[$strHint . 'furnish_type'] = '<b>Неприменимо для динамических объявлений. Необязательный для коммерции.</b><br>
Тип имеющейся в объекте мебели.<br>
Поддерживаемые значения: <br>
furnished, semi-furnished, unfurnished.
';
$MESS[$strName . 'heating_type'] = 'Тип отопления';
$MESS[$strHint . 'heating_type'] = '<b>Неприменимо для динамических объявлений. Необязательный для коммерции.</b><br>
Тип отопления, установленного в собственности. <br>
Поддерживаемые значения: <br>
central, gas, electric, radiator, other, none.
';
$MESS[$strName . 'laundry_type'] = 'Тип белья в наличии';
$MESS[$strHint . 'laundry_type'] = '<b>Неприменимо для динамических объявлений. Необязательный для коммерции.</b><br>
Тип белья в наличии. <br>
Поддерживаемые значения:<br>
in_unit, in_building, other, none.
';
$MESS[$strName . 'num_units'] = 'Общее количество единиц';
$MESS[$strHint . 'num_units'] = '<b>Необязательный для динамической рекламы и торговли .</b><br>
Общее количество единиц (квартир, кондоминиумов), доступных для аренды.<br>
Пример: 0
';
$MESS[$strName . 'parking_type'] = 'Тип парковки';
$MESS[$strHint . 'parking_type'] = '<b>Неприменимо для динамических объявлений. Необязательный для коммерции.</b><br>
Тип парковки на собственности. <br>
Поддерживаемые значения: <br>
garage, street, off-street, other, none.
';
$MESS[$strName . 'partner_verification'] = 'Подтвердила ли компания-партнер листинг';
$MESS[$strHint . 'partner_verification'] = '<b>Неприменимо для динамических объявлений. Необязательный для коммерции.</b><br>
Подтвердила ли компания-партнер листинг.<br> Поддерживаемые значения:<br> verified, none.
';
$MESS[$strName . 'year_built'] = 'Год постройки недвижимости в формате ГГГГ';
$MESS[$strHint . 'year_built'] = 'Год постройки недвижимости в формате ГГГГ , год из 4 цифр.<br>
Пример: 1994.
';
$MESS[$strName . 'pet_policy'] = 'Разрешения для размещения животных';
$MESS[$strHint . 'pet_policy'] = '<b>Неприменимо для динамических объявлений. Необязательный для коммерции.</b><br>
Обозначает животными на имущество:<br> cat, dog, all, none.
';
$MESS[$strHead . 'HEADER_AVAILABLE_DATES_PRICE_CONFIG'] = 'Список дат и цен, когда листинг доступен';
$MESS[$strHint . 'HEADER_AVAILABLE_DATES_PRICE_CONFIG'] = 'Список дат и цен, когда листинг доступен. Когда вы указываете значения, Facebook может рекомендовать списки на основе их доступных дат и динамически отображать соответствующую цену в вашем объявлении.<br>
См. <a href="https://developers.facebook.com/docs/marketing-api/real-estate-ads/get-started#available_dates-object" target="_blank">Параметры объекта "Доступные даты"</a>
';
$MESS[$strName . 'available_dates_price_config.start_date'] = 'Начало доступного диапазона дат';
$MESS[$strHint . 'available_dates_price_config.start_date'] = 'Необязательно.<br>
Начало доступного диапазона дат в формате ISO-8601; включая дату начала. Если вы только предоставляете start_date, по end_dateумолчанию используется год с этой даты.<br>
Пример:, YYYY-MM-DD например 2018-01-01.
';
$MESS[$strName . 'available_dates_price_config.end_date'] = 'Конец доступного диапазона дат';
$MESS[$strHint . 'available_dates_price_config.end_date'] = 'Необязательно.<br>
Конец доступного диапазона дат в формате ISO-8601; исключает дату окончания. Если вы только предоставляете end_date, по start_date умолчанию используется текущая дата.<br>
Пример:, YYYY-MM-DD например 2018-02-01.';
$MESS[$strName . 'available_dates_price_config.rate'] = 'Цена';
$MESS[$strHint . 'available_dates_price_config.rate'] = 'Целочисленная цена листинга в этом временном диапазоне.<br>
Пример: 10000 если объявление было $100.00 USD';
$MESS[$strName . 'available_dates_price_config.currency'] = 'Код валюты';
$MESS[$strHint . 'available_dates_price_config.currency'] = 'Обязательно, если вы указана Цена [rate]. Код валюты <a href="https://www.iso.org/iso-4217-currency-codes.html?fbclid=IwAR1Wm77c8rk0H-cckzb52g7L1gCLipFiIUpDw28MOQQdcTISleuthnAdyn0" target="_blank">ISO-4217</a>.<br>
Пример: USD, GBP и т.д.';
$MESS[$strName . 'available_dates_price_config.interval'] = 'Срок пребывания по указанному тарифу.';
$MESS[$strHint . 'available_dates_price_config.interval'] = 'Срок пребывания по указанному тарифу.<br>
Допустимые значения: nightly, weekly, monthly, sale.';
$MESS[$strName . 'applink'] = 'Ссылка на приложения';
$MESS[$strHint . 'applink'] = 'Ссылка на приложения';





# Steps
$MESS[$strMessPrefix . 'STEP_EXPORT'] = 'Запись в XML-файл';

# Display results
$MESS[$strMessPrefix . 'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix . 'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix . 'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix . 'RESULT_DATETIME'] = 'Время окончания';

#
$MESS[$strMessPrefix . 'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';
