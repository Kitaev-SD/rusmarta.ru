<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_HOTELS_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB гостиницы';

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
$MESS[$strName . 'hotel_id'] = 'Уникальный идентификатор гостиницы ';
$MESS[$strHint . 'hotel_id'] = '<b>Обязательное поле.</b><br>
Максимальная длина: 100.<br>
Уникальный идентификатор гостиницы в каталоге. Он будет сопоставляться с идентификаторами content_ids в событиях приложения и пикселя, связанных с hotel. Совет. Чтобы проверка прошла успешно, рекомендуем убрать из этого уникального идентификатора пробелы. ID не должны повторяться.<br>
Пример: FB_hotel_1234.
';
$MESS[$strName . 'room_id'] = 'Уникальный идентификатор для гостиничного номера';
$MESS[$strHint . 'room_id'] = '<b>Обязательно при добавлении информации о гостиничных номерах.</b><br>
Введите уникальный ID для типа гостиничного номера. Максимальная длина: 100. <br>
Пример: FB_hotel_room_1234.
';
$MESS[$strName . 'name'] = 'Наиболее употребительное название гостиницы.';
$MESS[$strHint . 'name'] = '<b>Обязательное поле.</b><br> <br>Пример: Facebook Hotel.
';
$MESS[$strName . 'description'] = 'Краткое описание гостиницы.';
$MESS[$strHint . 'description'] = '<b>Обязательное поле.</b><br>
Краткое описание гостиницы.<br>
Максимальный размер: 5 000.<br>
Пример: Only 30 minutes away from San Francisco.
';
$MESS[$strName . 'checkin_date'] = 'Текущая доступность';
$MESS[$strHint . 'checkin_date'] = '<b>Обязательно при добавлении информации о гостиничных номерах.</b><br>
Дата заезда в гостиницу. Можно добавить до 180 дней начиная с даты загрузки фида. Используется стандарт <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fen.wikipedia.org%2Fwiki%2FISO_8601%22%3Ehttps%3A%2F%2Fen.wikipedia.org%2Fwiki%2FISO_8601&h=AT2O_R46_3oqAkRyBV_hm276JO2m7j4aL3DmuDBcflghefvW0lj-_fAfM9jIDDW7VE8RUSHx0e_kefpu4RT06o6WYwKc4o6jD7hZlyCZgDhcbH1d6dfMGvvH9Spu9qxSREH_4i6n1ydyeFxEoQ" target="_blank">ISO-8601</a> (YYYY-MM-DD).<br>
Пример: 8/1/17.
';
$MESS[$strName . 'length_of_stay'] = 'Количество ночей проживания в гостинице.';
$MESS[$strHint . 'length_of_stay'] = '<b>Обязательно при добавлении информации о гостиничных номерах.</b><br>
Количество ночей проживания в гостинице.<br>
Пример: 7.
';
$MESS[$strName . 'base_price'] = 'Базовая цена за ночь в гостинице.';
$MESS[$strHint . 'base_price'] = '<b>Обязательно при добавлении информации о гостиничных номерах.</b><br>
Базовая цена за ночь в гостинице. Значение необходимо указывать вместе с валютой (например, USD для долларов США). Используйте денежный формат <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">с кодом валюты по стандарту ISO</a>  через пробел.<br>
Пример: 199.00 EUR.
';
$MESS[$strName . 'price'] = 'Общая стоимость проживания в гостинице с учетом checkin_date и length_of_stay';
$MESS[$strHint . 'price'] = '<b>Обязательно при добавлении информации о гостиничных номерах.</b><br>
Общая стоимость проживания в гостинице с учетом checkin_date и length_of_stay. Используйте денежный формат <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">с кодом валюты по стандарту ISO</a> через пробел.<br>
Пример: 1393.00 USD.
';
$MESS[$strName . 'tax'] = 'Ставка налога';
$MESS[$strHint . 'tax'] = '<b>Обязательно при добавлении информации о гостиничных номерах.</b><br>
Ставка налога, применимая к стоимости. Используйте денежный формат <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">с кодом валюты по стандарту ISO</a> через пробел.<br>
Пример: 14.
';
$MESS[$strName . 'fees'] = 'Комиссии, применимые к стоимости';
$MESS[$strHint . 'fees'] = '<b>Обязательно при добавлении информации о гостиничных номерах.</b><br>
Комиссии, применимые к стоимости. Используйте денежный формат <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">с кодом валюты по стандарту ISO</a> через пробел.<br>
Пример: 253.00 USD.
';
$MESS[$strName . 'url'] = 'Ссылка на внешний сайт, на котором можно забронировать номер в гостинице.';
$MESS[$strHint . 'url'] = '<b>Обязательное поле.</b><br>
Ссылка на внешний сайт, на котором можно забронировать номер в гостинице. Вы также можете указать URL на <a href="https://developers.facebook.com/docs/marketing-api/dynamic-ads-for-travel/ads-management#creative" target="_blank"> уровне рекламы </a>при помощи template_url_spec. URL на уровне рекламы имеют приоритет над URL в ленте.<br>
Пример: https://www.facebook.com/hotel.
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
$MESS[$strName . 'brand'] = 'Бренд сети гостиниц.';
$MESS[$strHint . 'brand'] = '<b>Обязательное поле.</b><br>
Бренд сети гостиниц.<br>
Пример: Hilton.';
$MESS[$strName . 'address@format'] = 'Техническое поле, изменять его нет необходимости!';
$MESS[$strHint . 'address@format'] = 'Техническое поле, изменять его нет необходимости!';
$MESS[$strName . 'address.component@name'] = 'Техническое поле, изменять его нет необходимости!';
$MESS[$strHint . 'address.component@name'] = 'Техническое поле, изменять его нет необходимости!';
$MESS[$strName . 'address.component'] = 'Почтовый адрес собственности';
$MESS[$strHint . 'address.component'] = '
Почтовый адрес гостиницы, который должен быть сопоставим с ее местонахождением.<br>
Проставить соответственно поля <br>
<b>Обязательные</b> addr1[Основой адрес гостиницы],city[Город],region[Регион],country[Страна],postal_code[Индекс] <br>
<b>Опциональные</b> addr2[Дополнительный адрес гостиницы],addr3[Третий адрес гостиницы.],city_id[Значение, которое будет использоваться в URL глубокой ссылки (template_url) в рекламном креативе.] <br>
См. <a  target="_blank" href="https://developers.facebook.com/docs/marketing-api/hotel-ads/catalog#address-object">Параметры объекта адреса</a>. <br>
';
$MESS[$strName . 'neighborhood'] = 'Район, где расположена гостиница.';
$MESS[$strHint . 'neighborhood'] = '<b>Обязательное поле.</b><br>
Максимальное количество районов: 20.<br>
Район, где расположена гостиница. Если вы хотите указать более одного района, добавьте по столбцу для каждого района и используйте в названии каждого столбца синтаксис пути JSON, чтобы указать количество районов.<br>
Пример: Belle Haven.
';
$MESS[$strName . 'latitude'] = 'Широта';
$MESS[$strHint . 'latitude'] = '<b>Обязательное поле.</b><br>
Широта листинга.<br>
Пример: 37.484100
';
$MESS[$strName . 'longitude'] = 'Долгота';
$MESS[$strHint . 'longitude'] = '<b>Обязательное поле.</b><br>
Долгота листинга.<br>
Пример: -122.148252
';

$MESS[$strName . 'sale_price'] = 'Цена со скидкой за сутки в гостинице с учетом checkin_date и length_of_stay';
$MESS[$strHint . 'sale_price'] = '<b>Необязательное поле.</b><br>
Цена со скидкой за сутки в гостинице с учетом checkin_date и length_of_stay. Используйте, чтобы предлагать скидки от обычной цены. Значение необходимо указывать вместе с валютой (например, USD для долларов США). Значение sale_price для гостиницы должно быть меньше значения base_price. Используйте денежный формат с кодом валюты по стандарту ISO через пробел.<br>
Пример: 149.00 USD.
';
$MESS[$strName . 'guest_ratings.score'] = 'Рейтинг';
$MESS[$strHint . 'guest_ratings.score'] = '<b>Необязательное поле.</b><br>
Рейтинг. Если задано, необходимо также указать score, max_score, number_of_reviewers и rating_system.<br>
Пример: 9.0/10.
';
$MESS[$strName . 'guest_ratings.rating_system'] = 'Система, используемая для сбора отзывов.';
$MESS[$strHint . 'guest_ratings.rating_system'] = '<b>Необязательное поле.</b><br>
Система, используемая для сбора отзывов.<br>
Примеры: Expedia, TripAdvisor.
';
$MESS[$strName . 'guest_ratings.number_of_reviewers'] = 'Общее количество людей, оценивших гостиницу.';
$MESS[$strHint . 'guest_ratings.number_of_reviewers'] = '<b>Необязательное поле.</b><br>
Общее количество людей, оценивших гостиницу.<br>
Пример: 5287.
';
$MESS[$strName . 'guest_ratings.max_score'] = 'Максимальное значение для рейтинга гостиницы. ';
$MESS[$strHint . 'guest_ratings.max_score'] = '<b>Обязательное поле.</b><br>
Максимальное значение для рейтинга гостиницы. Должно быть не менее нуля и не более 100.<br>
Пример: 10.
';
$MESS[$strName . 'star_rating'] = 'Звездочный рейтинг';
$MESS[$strHint . 'star_rating'] = '';
$MESS[$strName . 'loyalty_program'] = 'Программа лояльности, в которой людям начисляются баллы за пребывание в гостинице.';
$MESS[$strHint . 'loyalty_program'] = '<b>Необязательное поле.</b><br>
Программа лояльности, в которой людям начисляются баллы за пребывание в гостинице.<br>
Пример: Premium program.
';
$MESS[$strName . 'margin_level'] = 'Индикатор прибыльности гостиницы';
$MESS[$strHint . 'margin_level'] = '<b>Необязательное поле.</b><br>
Индикатор прибыльности гостиницы со значением от 1 до 10.<br>
Пример: 9.
';
$MESS[$strName . 'phone'] = 'Основной номер телефона гостиницы.';
$MESS[$strHint . 'phone'] = '<b>Необязательное поле.</b><br>
Основной номер телефона гостиницы.<br>
Пример: +61 296027455.
';
$MESS[$strName . 'applink'] = 'Добавьте глубокую ссылку на страницу сведений о гостинице в мобильном приложении,';
$MESS[$strHint . 'applink'] = '<b>Необязательное поле.</b><br>
Добавьте глубокую ссылку на страницу сведений о гостинице в мобильном приложении, используя <a href="https://developers.facebook.com/docs/applinks" target="_blank">App Links</a>. Вы можете указать глубокие ссылки (в порядке убывания значимости):<br>
На уровне рекламы при помощи template_url_spec.<br>
В ленте, используя объект Applink.<br>
Путем добавления на свой сайт метатегов App Links.<br>
Подробнее о <a href="https://developers.facebook.com/docs/marketing-api/catalog/guides/product-deep-links" target="_blank">глубоких ссылках на товары</a> <br>
';

$MESS[$strName . 'priority'] = 'Индикатор приоритета гостиницы ';
$MESS[$strHint . 'priority'] = '<b>Необязательное поле.</b><br>
Индикатор приоритета гостиницы от 0 (самый низкий приоритет) до 5 (самый высокий приоритет).<br>
Пример: 5.
';
$MESS[$strName . 'category'] = 'Тип объекта недвижимости.';
$MESS[$strHint . 'category'] = '<b>Необязательное поле.</b><br>
Тип объекта недвижимости. Можно использовать любые внутренние категории. <br>
Пример: Resort, Day Room.
';
$MESS[$strName . 'number_of_rooms'] = 'Общее количество номеров в объявлении';
$MESS[$strHint . 'number_of_rooms'] = '<b>Необязательное поле.</b><br>
Общее количество номеров в объявлении.<br>
Пример: 150.
';





# Steps
$MESS[$strMessPrefix . 'STEP_EXPORT'] = 'Запись в XML-файл';

# Display results
$MESS[$strMessPrefix . 'RESULT_GENERATED'] = 'Обработано новых товаров';
$MESS[$strMessPrefix . 'RESULT_EXPORTED'] = 'Всего выгружено';
$MESS[$strMessPrefix . 'RESULT_ELAPSED_TIME'] = 'Затрачено времени';
$MESS[$strMessPrefix . 'RESULT_DATETIME'] = 'Время окончания';

#
$MESS[$strMessPrefix . 'NO_EXPORT_FILE_SPECIFIED'] = 'Не указан путь к итоговому файлу.';
