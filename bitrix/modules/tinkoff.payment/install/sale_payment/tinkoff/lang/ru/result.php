<?php
global $MESS;

$MESS["SALE_TINKOFF_TITLE"]="Тинькофф Банк";
$MESS['SALE_TINKOFF_DESCRIPTION'] = 'https://oplata.tinkoff.ru/';

$MESS["SALE_TINKOFF_TERMINAL_ID_NAME"]="Терминал";
$MESS["SALE_TINKOFF_TERMINAL_ID_DESCR"]="Терминал доступен в Личном кабинете https://oplata.tinkoff.ru/";

$MESS["SALE_TINKOFF_SHOP_SECRET_WORD_NAME"]="Пароль";
$MESS["SALE_TINKOFF_SHOP_SECRET_WORD_DESCR"]="Пароль доступен в Личном кабинете https://oplata.tinkoff.ru/";

$MESS["SALE_TINKOFF_SHOULD_PAY_NAME"]="Сумма к оплате";
$MESS["SALE_TINKOFF_SHOULD_PAY_DESCR"]="Сумма оплаты. Выберите Значение \"Стоимость заказа\".";

$MESS['SALE_TINKOFF_TAXATION_NAME'] = 'Система налогообложения';
$MESS['SALE_TINKOFF_TAXATION_DESCR'] = 'Выберите систему налогообложения для Вашего магазина';
$MESS['SALE_TINKOFF_TAXATION_OSN'] = 'Oбщая СН';
$MESS['SALE_TINKOFF_TAXATION_USN_IMCOME'] = 'Упрощенная СН (доходы)';
$MESS['SALE_TINKOFF_TAXATION_USN_IMCOME_OUTCOME'] = 'Упрощенная СН (доходы минус расходы)';
$MESS['SALE_TINKOFF_TAXATION_ENVD'] = 'Единый налог на вмененный доход';
$MESS['SALE_TINKOFF_TAXATION_ESN'] = 'Единый сельскохозяйственный налог';
$MESS['SALE_TINKOFF_TAXATION_PATENT'] = 'Патентная СН';

$MESS['SALE_TINKOFF_LANGUAGE_NAME'] = 'Язык платежной формы';
$MESS['SALE_TINKOFF_LANGUAGE_DESCR'] = 'Выберите язык платежной формы для Вашего магазина';
$MESS['SALE_TINKOFF_LANGUAGE_RU'] = 'Русский';
$MESS['SALE_TINKOFF_LANGUAGE_EN'] = 'Английский';

$MESS['SALE_TINKOFF_ENABLE_TAXATION_NAME'] = 'Передавать данные для формирования чека';
$MESS['SALE_TINKOFF_ENABLE_TAXATION_DESCR'] = 'Данные чека будут передаваться в онлайн-кассу';
$MESS['SALE_TINKOFF_YES'] = 'Да';
$MESS['SALE_TINKOFF_NO'] = 'Нет';

$MESS['SALE_TINKOFF_DELIVERY_TAXATION_NAME'] = 'Ставка налога для доставки';
$MESS['SALE_TINKOFF_DELIVERY_TAXATION_DESCR'] = 'Параметр необходим для добавления информации о доставке в чек. Доставка добавляется в чек отдельной позицией.';
$MESS['SALE_TINKOFF_VAT_NONE'] = 'Без НДС';
$MESS['SALE_TINKOFF_VAT_ZERO'] = 'НДС 0%';
$MESS['SALE_TINKOFF_VAT_REDUCED'] = 'НДС 10%';
$MESS['SALE_TINKOFF_VAT_STANDARD'] = 'НДС 20%';

$MESS['SALE_TINKOFF_PAYBUTTON_NAME'] = 'Оплатить';

$MESS['SALE_TINKOFF_UNAVAILABLE'] = 'Запрос к платежному сервису был отправлен некорректно. Проверьте настройки';

$MESS['SALE_TINKOFF_EMAIL_NAME'] = 'Email пользователя';
$MESS['SALE_TINKOFF_EMAIL_DESCR'] = 'Электронная почта, которую указал в заказе пользователь';

$MESS['SALE_TINKOFF_ORDER_INFO'] = "Информация о заказе";
$MESS['SALE_TINKOFF_ORDER_PAYMENT'] = "Оплата заказа";

$MESS['SALE_TINKOFF_SUCCESS'] = "успешно";
$MESS['SALE_TINKOFF_FAIL'] = "не успешно";
$MESS['SALE_TINKOFF_FAIL_TEXT'] = "Заказ с номером %s не найден";
$MESS['SALE_TINKOFF_SUCCESS_TEXT'] = "Заказ с номером %s оплачен %s <br/> Состояние заказа можно узнать на <a href=\"%s\">странице заказа</a>";

//
$MESS['SALE_TINKOFF_CONNECT_ERROR'] = "Не удалось соединиться с платёжным сервисом.";
$MESS['SALE_TINKOFF_SUM_ERROR'] = 'Сумма заказа не сходится. Ответ сервиса: %s';
$MESS['SALE_TINKOFF_TOKEN_ERROR'] = 'Токены не совпадают. Запрос сервиса: %s';
$MESS['SALE_TINKOFF_STATUS_ERROR'] = 'Статус заказа не определён. Чтобы запросить статус вызовите метод getStatus';
$MESS['SALE_TINKOFF_QUERY_ERROR'] = 'Не удалось отправить запрос';
$MESS['SALE_TINKOFF_PAYMENT_CANCELED'] = 'оплата заказа отменена';
$MESS['SALE_TINKOFF_PAYMENT_ID_ERROR'] = 'Платеж уже был совершен. Запрос сервиса: %s';

$MESS['SALE_TINKOFF_FIO_NAME'] = 'Ф.И.О пользователя';
$MESS['SALE_TINKOFF_FIO_DESCR'] = '';
$MESS['SALE_TINKOFF_PHONE_NAME'] = 'Телефон пользователя';
$MESS['SALE_TINKOFF_PHONE_DESCR'] = '';

$MESS['SALE_TINKOFF_TAX_ERROR'] = 'Не удалось получить данные о налоге на товар. Проверьте настройки.';
$MESS['SALE_TINKOFF_TAXATION_ERROR'] = 'Не удалось получить данные о системе налогообложения. Проверьте настройки.';
$MESS['SALE_TINKOFF_TAX_DELIVERY_ERROR'] = 'Не удалось получить данные о налоге на доставку. Проверьте настройки.';

$MESS["SALE_TINKOFF_PAYMENT_METHOD_NAME"] = "Признак способа расчёта";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_FULL_PREPAYMENT"] = "Предоплата 100%";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_PREPAYMENT"] = "Предоплата";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_ADVANCE"] = "Аванc";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_FULL_PAYMENT"] = "Полный расчет";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_PAYMENT"] = "Частичный расчет и кредит";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_CREDIT"] = "Передача в кредит";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_CREDIT_PAYMENT"] = "Оплата кредита";

$MESS["SALE_TINKOFF_PAYMENT_OBJECT_NAME"] = "Признак предмета расчёта";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_COMMODITY"] = "Товар";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_EXCISE"] = "Подакцизный товар";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_JOB"] = "Работа";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_SERVICE"] = "Услуга";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_BET"] = "Ставка азартной игры";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_PRIZE"] = "Выигрыш азартной игры";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_LOTTERY"] = "Лотерейный билет";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_LOTTERY_PRIZE"] = "Выигрыш лотереи";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_INTELLECTUAL_ACTIVITY"] = "Предоставление результатов интеллектуальной деятельности";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_PAYMENT"] = "Платеж";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_AGENT_COMMISSION"] = "Агентское вознаграждение";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_COMPOSITE"] = "Составной предмет расчета";
$MESS["SALE_TINKOFF_PAYMENT_METHOD_ANOTHER"] = "Иной предмет расчета";

$MESS["SALE_TINKOFF_EMAIL_COMPANY_NAME"] = "Email компании";