<?
$MESS["bxmaker.smsnotice.SMS_STATUS_WAIT"] = "Ожидание";
$MESS["bxmaker.smsnotice.SMS_STATUS_SENT"] = "Отправлено";
$MESS["bxmaker.smsnotice.SMS_STATUS_ERROR"] = "Ошибка";
$MESS["bxmaker.smsnotice.SMS_STATUS_DELIVERED"] = "Доставлено";

$MESS["bxmaker.smsnotice.MANAGER.SEND_SMS_DEBUG_MODE"] = "Имитация отправки. СМС не отправляются в смс сервис. Отключается  настройках модуля.";
$MESS["bxmaker.smsnotice.MANAGER.OK"] = "Ок";
$MESS["bxmaker.smsnotice.MANAGER.NOT_FOUNT_TEMPLATES"] = "Не найдены активные шаблоны такого типа";
$MESS["bxmaker.smsnotice.MANAGER.NOTICE.UNKNOWN_SERVICE_ID"] = "Не найден сервис с таким идентификатором";

$MESS["bxmaker.smsnotice.MANAGER.NOTICE.INVALID_PARAM_SERVICE_ID"] = "Значение параметра serviceId не указано или отсутствует";
$MESS["bxmaker.smsnotice.MANAGER.MODULE_DEMO_EXPIRED"] = "Время тестового периода закончилось! Приобретите полную версию модуля, чтобы снять ограничения и продолжить работу, а также получать обновления  и возможность использовать новые сервисы и их выгоды.";
$MESS["bxmaker.smsnotice.MANAGER.MODULE_DEMO"] = "Модуль работает в демо-режиме. Через несколько тестовые период закончится. Вы можете купить версию без ограничений и использовать новые сервисы и их выгоды при обновлении.";
$MESS["bxmaker.smsnotice.MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_ERROR_EVENTRESULT"] = "Один из обработчиков событий вернул статус - ERROR. Отправка не произошла.";

$MESS["bxmaker.smsnotice.MANAGER.EVENT_ONBEFORE_SEND_TEMPLATE_EMPTY_PARAMS"] = "После работы обработчиков событий отсутствует массив с данными, даже пустой - . Отправка не произошла.";
$MESS["bxmaker.smsnotice.MANAGER.EVENT_ONBEFORE_SEND_ERROR_EVENTRESULT"] = "Один из обработчиков событий вернул статус - ERROR. Отправка не произошла.";
$MESS["bxmaker.smsnotice.MANAGER.EVENT_ONBEFORE_SEND_EMPTY_PARAMS"] = "После работы обработчиков событий отсутствует 1 или несколько обязательных параметров для отправки. Отправка не произошла.";
$MESS["bxmaker.smsnotice.MANAGER.ERROR_SKIP_GROUP"] = "Пользователь состоит в группе, которой запрещено отправлять смс";

$MESS["bxmaker.smsnotice.MANAGER.ERROR_SERVICE_INITIALIZATION"] = "Не удалось инициализировать сервис для отправки СМС. Вероятнее всего у вас нет ни одного активного сервиса, вам нужно <a href=\"/bitrix/admin/bxmaker.smsnotice_service_list.php?lang=ru\" target=\"_blank\">добавить здесь</a>";
$MESS["bxmaker.smsnotice.MANAGER.ERROR_INVALID_PHONE"] = "Номер телефона введен с ошибками";
$MESS["bxmaker.smsnotice.MANAGER.EMAIL_PHONE_IS_NOT_DEFINED"] = "Не определен номер телефона для смс отправляемого автоматически при почтовом событии";
$MESS["bxmaker.smsnotice.DEMO_NOTICE"] = "В демо-режиме доступен полный функционал, ограничения распространяются только на количество дней использования.<br> После окончания демо-периода,  смс сообщения перестанут отправляться как в ручном, так и в автоматическом режиме.<br>После покупки модуля смс сообщения вновь начнут отправляться без ограничений. <br>Приятной работы!";

$MESS["bxmaker.smsnotice.DEMO_EXPIRED_NOTICE"] = "Демо-режим закончился. Для восстановления работы модуля, приобретите платную версию - <a href=\"https://bxmaker.ru/wPpgd\" target=\"_blank\">здесь.</a>";
$MESS["bxmaker.smsnotice.AJAX.TEXT_SMS_EMPTY"] = "Введите текст сообщения";
$MESS["bxmaker.smsnotice.AJAX.TEMPLATE_TYPE_FIELD_SITE_NAME"] = "#SITE_NAME# - Название веб-сайта, <small>Из настроек сайта, область - Параметры</small><br>";
$MESS["bxmaker.smsnotice.AJAX.TEMPLATE_TYPE_FIELD_SERVER_NAME"] = "#SERVER_NAME# - URL сервера (без http://), <small>Из настроек сайта, область - Параметры</small><br>";

$MESS["bxmaker.smsnotice.AJAX.SMS_SEND_SUCCESSFULL"] = "Смс успешно отправлено";
$MESS["bxmaker.smsnotice.AJAX.SITE_ID_NOT_FOUND"] = "Не указан идентификатор сайта или  не удалось его определить";
$MESS["bxmaker.smsnotice.AJAX.METHOD_NOT_FOUND"] = "Не указан или указан неизвестный метод";
$MESS["bxmaker.smsnotice.AJAX.ACCESS_DENIED"] = "Недостаточно прав";
    
    $MESS["bxmaker.smsnotice.MANAGER.CHECK_CONDITION_FAIL"] = "Отправка смс остановлена, из за несоответствия заданным условиям из шаблона смс - ";


$MESS["bxmaker.smsnotice.TRANSLIT_CHARS"] =  '{"а": "a", "б": "b", "в": "v", "г": "g", "д": "d", "е": "e", "ё": "e", "ж": "zh", "з": "z", "и": "i", "й": "j", "к": "k", "л": "l", "м": "m", "н": "n","о": "o", "п": "p", "р": "r","с": "s", "т": "t", "у": "u", "ф": "f", "х": "h","ц": "c", "ч": "ch", "ш": "sh", "щ": "sh","ъ": "\'", "ы": "y", "ь": "\'", "э": "e", "ю": "yu", "я": "ya"}';



?>