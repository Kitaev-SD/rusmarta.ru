<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_FLIGHT_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB авиарейсы';

// Default settings
$MESS[$strMessPrefix . 'SETTINGS_TITLE'] = 'Заголовок файла (тег title)';
$MESS[$strMessPrefix . 'SETTINGS_TITLE_HINT'] = 'Укажите здесь заголовок файла.';

$MESS[$strMessPrefix . 'SETTINGS_FILE'] = 'Итоговый файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = 'Например, /upload/acrit.exportproplus/facebook_flight.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = 'Укажите здесь файл, в который будет выполняться экспорт по данному профилю.<br/><br/><b>Пример указания файла</b>:<br/><code>/upload/xml/facebook_flight.xml</code>';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN'] = 'Открыть файл';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN_TITLE'] = 'Файл откроется в новой вкладке';

// Headers
// Fields
$MESS[$strName . 'origin_airport'] = 'Код IATA аэропорта отправления';
$MESS[$strHint . 'origin_airport'] = '<b>Обязательное поле.</b><br>
Код IATA аэропорта отправления. Поддерживаются коды IATA для аэропортов и городов. Для проверки кодов используйте средство поиска кодов IATA. Совет. Чтобы проверка прошла успешно, не используйте пробелы в этом уникальном идентификаторе.<br>
Пример: SFO.
';
$MESS[$strName . 'destination_airport'] = 'Код IATA аэропорта прибытия';
$MESS[$strHint . 'destination_airport'] = '<b>Обязательное поле.</b><br>
Код IATA аэропорта прибытия. Поддерживаются коды IATA для аэропортов и городов. Для проверки кодов используйте средство поиска кодов IATA. Совет. Чтобы проверка прошла успешно, не используйте пробелы в этом уникальном идентификаторе.<br>
Example: JFK.
';
$MESS[$strName . 'image.url'] = 'URL-адрес изображения';
$MESS[$strHint . 'image.url'] = '<b>Обязательное поле.</b><br>
Максимальное количество элементов: 20.<br>
Данные об изображениях для этого авиарейса. Для авиарейса можно предоставить до 20 изображений. Каждое изображение содержит два поля: url и tag. С одним изображением может быть связано несколько меток. Необходимо предоставить хотя бы один элемент image (изображение). Размер каждого изображения не должен превышать 4 МБ.
См. <a href="https://developers.facebook.com/docs/marketing-api/flight-ads/catalog#image-object" target="_blank">См. параметры объектов image.</a>.
';
$MESS[$strName . 'image.tag'] = 'тег изображения';
$MESS[$strHint . 'image.tag'] = 'К изображению добавлен тег, который показывает, что на нем изображено. С изображением может быть связано несколько тегов.<br>
Примеры: Fitness Center,Swimming Pool<br>
INSTAGRAM_STANDARD_PREFERRED- Позволяет рекламодателям пометить определенное изображение в своем фиде как изображение по умолчанию, которое будет использоваться для Instagram. Этот тег чувствителен к регистру.
';

$MESS[$strName . 'description'] = 'Краткое описание маршрута.';
$MESS[$strHint . 'description'] = '<b>Обязательное поле.</b><br>
Максимальный размер: 5000.<br>
Краткое описание маршрута.
';
$MESS[$strName . 'url'] = 'Ссылка на внешний сайт, на котором можно получить информацию об авиарейсе';
$MESS[$strHint . 'url'] = '<b>Обязательно, только если не указана глубокая ссылка</b>
Обязательно, только если не указана глубокая ссылка <a href="https://developers.facebook.com/docs/marketing-api/dynamic-ads-for-travel/ads-management" target="_blank">на уровне объявления</a>. Можно использовать поле <b>Deep Link</b> в <a href="https://business.facebook.com/adsmanager/manage" target="_blank">Ads Manager</a> или <b>template_url_spec</b> в API.<br>
Ссылка на внешний сайт, на котором можно получить информацию об авиарейсе. Приоритет имеет глубокая ссылка на уровне объявления.
';

$MESS[$strName . 'origin_city'] = 'Название города вылета.';
$MESS[$strHint . 'origin_city'] = 'Название города вылета.
Пример: San Francisco.
';
$MESS[$strName . 'destination_city'] = 'Название города прибытия.';
$MESS[$strHint . 'destination_city'] = 'Название города прибытия.
Пример: New York.
';
$MESS[$strName . 'price'] = 'Цена билета. Значение должно указываться вместе с валютой.';
$MESS[$strHint . 'price'] = 'Цена билета. Значение должно указываться вместе с валютой.<br>
Пример: 99.99 USD.
';
$MESS[$strName . 'one_way_price'] = 'Цена билета в одну сторону. Значение должно указываться вместе с валютой.';
$MESS[$strHint . 'one_way_price'] = 'Цена билета в одну сторону. Значение должно указываться вместе с валютой.<br>
Пример: 99.99 USD.
';
$MESS[$strName . 'priority'] = 'Приоритет авиарейса. ';
$MESS[$strHint . 'priority'] = 'Приоритет авиарейса. Возможные варианты: от 0 (самый низкий) до 5 (самый высокий). Если это значение не указано, авиарейс имеет приоритет 0.<br>
Пример: 5.
';

$MESS[$strName . 'applink'] = 'Добавьте глубокую ссылку на страницу сведений о гостинице в мобильном приложении,';
$MESS[$strHint . 'applink'] = '<b>Необязательное поле.</b><br>
Добавьте глубокую ссылку на страницу сведений о гостинице в мобильном приложении, используя <a href="https://developers.facebook.com/docs/applinks" target="_blank">App Links</a>. Вы можете указать глубокие ссылки (в порядке убывания значимости):<br>
На уровне рекламы при помощи template_url_spec.<br>
В ленте, используя объект Applink.<br>
Путем добавления на свой сайт метатегов App Links.<br>
Подробнее о <a href="https://developers.facebook.com/docs/marketing-api/catalog/guides/product-deep-links" target="_blank">глубоких ссылках на товары</a> <br>
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
