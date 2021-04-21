<?
$MESS['MAIN_OPTIONS_SITE_ASPRO_TITLE'] = '#SITE_NAME#';
$MESS['ASPRO_NEXT_PAGE_TITLE'] = 'Интеграция с Flowlu';
$MESS['ASPRO_NEXT_NO_RIGHTS_FOR_VIEWING'] = 'Доступ закрыт';
$MESS['ASPRO_NEXT_MODULE_NOT_INCLUDED'] = 'Не удалось подключить модуль Аспро: Next';
$MESS['ASPRO_NEXT_MODULE_CONTROL_CENTER_ERROR'] = 'Не удалось получить информацию об установке решения';
$MESS['ASPRO_NEXT_MODULE_CONFIG_FLOWLU'] = 'Настройки';
$MESS['ASPRO_NEXT_MODULE_LINK_FLOWLU'] = 'Интеграция и соответствие полей';
$MESS['ASPRO_NEXT_MODULE_DOMAIN_FLOWLU'] = 'Ваш домен в flowlu';
$MESS['ASPRO_NEXT_MODULE_LOGIN_FLOWLU'] = 'Логин';
$MESS['ASPRO_NEXT_MODULE_PASSWORD_FLOWLU'] = 'Пароль';
$MESS['ASPRO_NEXT_MODULE_WEB_FORM_FLOWLU'] = 'Веб-формы';
$MESS['ASPRO_NEXT_MODULE_AUTOMATE_SEND_FLOWLU'] = 'Автоматически отправлять результаты в CRM';
$MESS['ASPRO_NEXT_MODULE_ACTIVE_FLOWLU'] = 'Включить интеграцию';
$MESS['ASPRO_NEXT_MODULE_TOKEN_FLOWLU'] = 'Ключ для авторизации в API';
$MESS['ASPRO_NEXT_MODULE_CHECK_AUTH'] = 'Проверить авторизацию';
$MESS['ASPRO_NEXT_MODULE_TOKEN_HINT'] = 'Управление API-ключами доступно в разделе Личный кабинет > Все настройки > API.';
$MESS['ASPRO_NEXT_MODULE_SAVE_OPTION'] = 'Сохранить';
$MESS['ASPRO_NEXT_MODULE_DELETE_OPTION'] = 'Установить значения по умолчанию';
$MESS['ASPRO_NEXT_MODULE_DELETE_OPTION_TITLE'] = 'Внимание! Все настройки будут удалены. Продолжить?';
$MESS['ASPRO_NEXT_MODULE_DELETE_OPTION_TEXT'] = 'Сбросить';
$MESS['ASPRO_NEXT_MODULE_FIELDS_FLOWLU'] = 'Поля';
$MESS['ASPRO_NEXT_MODULE_ALL_FIELDS_FLOWLU'] = 'Поля веб-форм и CRM';
$MESS['ASPRO_NEXT_MODULE_RESULTS_FLOWLU'] = 'Результаты';
$MESS['ASPRO_NEXT_MODULE_ALL_RESULTS_FLOWLU'] = 'Результаты веб-форм';
$MESS['ASPRO_NEXT_MODULE_USE_LOG_FLOWLU'] = 'Использовать логирование';
$MESS['ASPRO_NEXT_MODULE_LEAD_NAME_FLOWLU_TITLE'] = 'Название лида с сайта по-умолчанию';
$MESS['ASPRO_NEXT_MODULE_LEAD_NAME_FLOWLU'] = 'Заявка с сайта';
$MESS['CRM_FIELD_TABLE'] = 'Поле CRM';
$MESS['FORM_FIELD_TABLE'] = 'Поле формы';
$MESS['FORM_RESULT_FIELD_TABLE'] = 'Результаты форм';
$MESS['FORM_CRM_ADD'] = 'Добавить';
$MESS['ASPRO_NEXT_MODULE_NO_FORM_FIELD_MATCHING'] = 'Необходимо <b>ввести верные данные</b> для авторизации, <b>включить интеграцию</b> и <b>установить соответствие полей</b> веб-форм и полей CRM на вкладке <b>"Поля"</b>.';

$MESS["FORM_FIELD_CRM_FIELDS_FORM_NAME_NO"] = "Не выбрано";
$MESS["FORM_RESULT_INFO"] = "Создан: DATE_CREATE Изменен: TIMESTAMP_X";
$MESS["FORM_RESULT_SEND"] = "отправлено";
$MESS["FORM_RESULT_NO_SEND"] = "неотправлено";
$MESS["DELETE_NODE"] = "удалить";
$MESS["SEND_CRM"] = "отправить в CRM";
$MESS["ASPRO_NEXT_MODULE_NO_FORM_RESULTS"] = "Нет результатов формы";

$MESS['ASPRO_NEXT_MODULE_AUTOMATE_SEND_FLOWLU_HINT'] = 'Если опция установлена, то после успешной отправки веб-формы произойдет автоматическое отправление результатов в CRM. Иначе, все результаты будут вручную отправляться через таблицу <b>"Результаты форм"</b> на вкладке <b>"Результаты"</b>';
$MESS['ASPRO_NEXT_MODULE_USE_LOG_FLOWLU_HINT'] = 'Все файлы с логами будут помещаться в папку <b>/upload/logs/aspor.next/crm/YY-mm-dd/flowlu_create_lead_request.log</b> (исходящие запросы с сайта в crm) и <b>/upload/logs/aspor.next/crm/YY-mm-dd/flowlu_create_lead_response.log</b> (ответ crm)';
$MESS['ASPRO_NEXT_MODULE_NO_FORMS'] = 'Не установлен модуль <b>"Веб-формы"</b>. Дальнейшая интеграция невозможна.';
$MESS['ASPRO_NEXT_MODULE_INTEGRATION_FLOWLU'] = 'Необходимо задать верные параметры в разделе <b>"Настройки"</b>. После чего будет доступна возможность включения интеграции с сайтом и задать соответствие полей веб-форм';
$MESS['ASPRO_NEXT_MODULE_DOMAIN_HINT'] = 'Необходимо ввести ваш домен 3го уровня (без https:// и .flowlu.ru).<br>Пример: https://<u>домен</u>.flowlu.ru - надо ввести <u>домен</u>.';

$MESS["ASPRO_NEXT_NO_SITE_INSTALLED"] = 'Не найдено сайтов с установленным решением &laquo;Аспро: Next;<br />
<input type="button" value="Установить" style="margin-top: 10px;" onclick="document.location.href=\'/bitrix/admin/wizard_install.php?lang=ru&wizardName=aspro:next&#SESSION_ID#\'">';
?>