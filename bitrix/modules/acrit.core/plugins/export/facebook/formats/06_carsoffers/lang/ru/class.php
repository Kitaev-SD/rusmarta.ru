<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_CARS_OFFERS_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB автомобили Offer Ads';

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
$MESS[$strName . 'vehicle_offer_id'] = 'Уникальный идентификатор автомобиля.';
$MESS[$strHint . 'vehicle_offer_id'] = 'Обязательно .

Уникальный идентификатор автомобиля. которые рекламодатели будут использовать для определения предложения. Это то же значение, которое передается в качестве content_idпараметра в пикселе .

Пример: offer1
';
$MESS[$strName . 'make'] = 'Марка автомобиля';
$MESS[$strHint . 'make'] = 'Обязательно .

"Make" или марка автомобиля.

Пример: Endomoto
';

$MESS[$strName . 'model'] = 'Модель автомобиля';
$MESS[$strHint . 'model'] = 'Обязательно .

Модель автомобиля.

Пример: EndoHatch
';
$MESS[$strName . 'year'] = 'Год выпуска автомобиля';
$MESS[$strHint . 'year'] = 'Обязательно.
Год выпуска автомобиля в yyyy формат.
Пример: 2015
';

$MESS[$strName . 'offer_type'] = 'Тип предложения';
$MESS[$strHint . 'offer_type'] = 'Обязательно.
Тип предложения. Допустимые значения: lease, finance, cash
';
$MESS[$strName . 'title'] = 'Название объявления / предложения';
$MESS[$strHint . 'title'] = 'Обязательно .

Название объявления / предложения.

Пример: "$299 per month for the EndoHatch GE!"
';
$MESS[$strName . 'offer_description'] = 'Описание предложения.';
$MESS[$strHint . 'offer_description'] = 'Обязательно .

Описание предложения.

Пример: This offer is valid only during the month of September.
';
$MESS[$strName . 'url'] = 'Ссылка на внешний сайт, где вы можете просмотреть предложение.';
$MESS[$strHint . 'url'] = 'Обязательно .

Ссылка на внешний сайт, где вы можете просмотреть предложение.
';

$MESS[$strName . 'offer_disclaimer'] = 'Отказ от ответственности';
$MESS[$strHint . 'offer_disclaimer'] = 'Обязательно .

Отказ от ответственности, связанный с предложением.
';
$MESS[$strName . 'image.url'] = 'URL-адрес изображения';
$MESS[$strHint . 'image.url'] = 'Обязательно .

Максимум: 20

URL изображения автомобиля. Если у вас есть более чем одно изображение транспортного средства, следовать этому правилу именования: image[1].url, image[2].urlи так далее. Вы должны предоставить хотя бы одно изображение . Размер каждого изображения может составлять до 4 МБ. Для торговой площадки требуется минимум 2 изображения.

Чтобы использовать карусельную рекламу, предоставьте квадратные изображения с соотношением сторон 1: 1 (600 x 600 пикселей).

Для показа рекламы с одним транспортным средством - предоставьте изображения с соотношением сторон 1,91: 1 (1200 x 630 пикселей).
Узнайте больше о лучших методах работы с изображениями в Marketplace .
';
$MESS[$strName . 'image.tag'] = 'тег изображения';
$MESS[$strHint . 'image.tag'] = 'К изображению добавлен тег, который показывает, что на нем изображено. С изображением может быть связано несколько тегов.<br>
Примеры: Fitness Center,Swimming Pool<br>
INSTAGRAM_STANDARD_PREFERRED- Позволяет рекламодателям пометить определенное изображение в своем фиде как изображение по умолчанию, которое будет использоваться для Instagram. Этот тег чувствителен к регистру.
';
$MESS[$strName . 'amount_price'] = 'Сумма аренды или денежного предложения.';
$MESS[$strHint . 'amount_price'] = 'Рекомендуем .

Сумма аренды или денежного предложения. Отформатируйте цену как стоимость, за которой следует код валюты ISO с пробелом между стоимостью и валютой.

Пример: для аренды 329 долларов в месяц или кэшбэка в 2000 долларов значениями являются 329 USDили 2000 USD.
';


$MESS[$strName . 'amount_percentage'] = 'Значение в процентах';
$MESS[$strHint . 'amount_percentage'] = 'Рекомендуем .

Значение в процентах, если оно указано в предложении. Иногда значение выражается в процентах (пример :), 3.9% APRа не в сумме.
';
$MESS[$strName . 'amount_qualifier'] = 'Квалификатор суммы в долларах или в процентах от суммы';
$MESS[$strHint . 'amount_qualifier'] = 'Рекомендуем .

Квалификатор суммы в долларах или в процентах от суммы. Допустимые значения могут быть per monthили APR.

Пример: /mo100 $ / мес для предложений аренды. APRв 1,1% годовых для финансовых предложений.
';
$MESS[$strName . 'term_length'] = 'Срок действия предложения';
$MESS[$strHint . 'term_length'] = 'Рекомендуем .

Срок действия предложения. Если предложение представляет собой аренду по цене 329 долл. США в месяц на 3 года, то стоимость равна, 3а соответствующая стоимость term_qualifier- равна years.

Пример: /mo100 $ / мес для предложений аренды. APRв 1,1% годовых для финансовых предложений.
';
$MESS[$strName . 'term_qualifier'] = 'Единицы на срок действия предложения';
$MESS[$strHint . 'term_qualifier'] = 'Рекомендуем .

Единицы на срок действия предложения. Допустимые значения: «месяцы» или «годы».

Пример: monthsдля предложения 329 долларов в месяц на 36 месяцев и yearдля предложения 329 долларов в месяц на 3 года.
';

$MESS[$strName . 'downpayment'] = 'Сумма первоначального взноса при покупке или аренде';
$MESS[$strHint . 'downpayment'] = 'Рекомендуем .

Сумма первоначального взноса при покупке или аренде. Отформатируйте цену как стоимость, за которой следует код валюты ISO с пробелом между стоимостью и валютой.

Пример: используйте 1500 USDв качестве значения, если есть условия предоплаты $1500 due at signing + 1 month payment.
';
$MESS[$strName . 'downpayment_qualifier'] = 'Квалификатор downpaymentзначения';
$MESS[$strHint . 'downpayment_qualifier'] = 'Рекомендуем .

Квалификатор downpaymentзначения. Пример: используйте due at signing + 1 month paymentзначение, если есть условия первоначального платежа $1500 due at signing + 1 month payment.
';

$MESS[$strName . 'trim'] = 'Отделка салона автомобиля';
$MESS[$strHint . 'trim'] = 'Рекомендуем .

Отделка салона автомобиля. Пример: GE. .

Текущее состояние автомобиля. Допустимые значения: New, Usedили CPO(сертифицированные подержанный).';
$MESS[$strName . 'price'] = 'Рекомендуемая производителем розничная цена автомобиля с валютой';
$MESS[$strHint . 'price'] = 'рекомендуемые

Рекомендуемая производителем розничная цена автомобиля с валютой. Отформатируйте цену как стоимость, за которой следует код валюты ISO с пробелом между стоимостью и валютой. Пример:13,999 USD
';
$MESS[$strName . 'body_style'] = 'Кузов автомобилей.';
$MESS[$strHint . 'body_style'] = 'Рекомендуем .

Кузов автомобилей. Допустимые значения: CONVERTIBLE, COUPE, HATCHBACK, MINIVAN, TRUCK, SUV, SEDAN, VAN, WAGON, CROSSOVER, илиOTHER
';
$MESS[$strName . 'start_date'] = 'Дата начала, с которой предложение действительно';
$MESS[$strHint . 'start_date'] = 'Необязательно .

Дата начала, с которой предложение действительно. Должен быть в формате даты гггг-мм-дд .

Пример: 2018-09-05
';
$MESS[$strName . 'end_date'] = 'Дата окончания';
$MESS[$strHint . 'end_date'] = 'Необязательно .

Дата окончания, после которой предложение действительно. Должен быть в формате даты гггг-мм-дд .

Пример: 2018-09-05
';

$MESS[$strName . 'market_name'] = 'Наличие автомобиля';
$MESS[$strHint . 'market_name'] = 'Необязательно .

Название рынка / обозначенная рыночная зона (DMA).
';

$MESS[$strName . 'dma_codes'] = 'Тип транспортного средства ';
$MESS[$strHint . 'dma_codes'] = 'Обязательно для регионального предложения. Необязательно для национального предложения .

Список кодов выделенной рыночной зоны (DMA) для конкретного рынка<a href="https://help-ooyala.brightcove.com/sites/all/libraries/dita/en/video-platform/reference/dma_codes.html?fbclid=IwAR2Ruflliy9uMeMTcySfxZ7iathPEGMcmm-1xlFf1j6u5224bjTqz5i3qtw" target="_blank">примеры каналов</a>   для формата XML. См. Стандарт кода DMA . Оставьте поле пустым для национального предложения.
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
