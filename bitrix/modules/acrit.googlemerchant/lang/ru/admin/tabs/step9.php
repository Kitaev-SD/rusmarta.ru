<?
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_INFILE"] = "В файл";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_RUN"] = "Настройки запуска выгрузки";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_AGENT"] = "Агент";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON"] = "Автоматический - по расписанию (cron)";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_COMP"] = "Ручной";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_TYPE"] = "Тип запуска:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILE_TYPE"] = "Тип файла:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_MODE"] = "Режим запуск cron-задания:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_PHP"] = "Путь до интерпретатора php:";

$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_PHP_PATHS"] = "<b>Пути по которым на вашем сервере может располагаться интерпретатор php</b>:<br/><br/>";

$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_SHORTTAG_PHP"] = "<b>Внимание!</b> Не определен или не указан параметр short_open_tag в настройках пути к интерпретатору php.<br/>Сделать это можно следующим образом: <b>путь_к_php -d short_open_tag</b>";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILE_XML"] = "xml";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILE_CSV"] = "csv";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILE_XLS"] = "xls";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_MODE_PHP"] = "php";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_MODE_CURL"] = "curl";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_THREADS"] = "Количество потоков:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_USE_COMPRESS"] = "Упаковать в zip:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILENAME"] = "Файл выгрузки";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILENAME_USED"] = "Данный файл выгрузки уже используется другим профилем (ID=<span>#ID#</span>).";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_DATESTART"] = "Дата начала выгрузки:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_PERIOD"] = "Период <b>(минуты)</b>:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_LSATSTART"] = "Дата последнего запуска:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_NEXTSTART"] = "Дата следующего запуска:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CSV_INFO"] = "При экспорте в CSV генерация файла происходит в один шаг и может перенагрузить сервер объемом обрабатываемых данных";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_INFO"] = "Справочная информация<br/>Выгрузка в файл (файлы) осуществляется следующими типами запуска:<br/>вручную - компонент, автоматически - cron<br/>
                            Выгрузка на экран (по URL) осуществляется только компонентом";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_IS_PERIOD"] = "Периодический:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_IS_PERIOD_HELP"] = "Если опция активна, то очередная дата запуска будет рассчитываться по периоду.<br/>Если опция не активна, то очередная дата запуска будет рассчитываться как:<br/>Очередная дата запуска = дата последнего запуска + период.";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_IS_STEP_EXPORT"] = "Поэтапная выгрузка:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_IS_STEP_EXPORT_HELP"] = "Если опция активна, то экспорт данных будет производиться поэтапно в соответствующей опцией.";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_STEP_EXPORT_CNT"] = "Товаров за шаг:";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_STEP_EXPORT_CNT_HELP"] = "<b>Внимание!</b><br/>Важный параметр, определяет количество товаров, добавляемых в файл экспорта при каждой выгрузке.<br/><br/><b>Важно!</b><br/>Значение поля не должно превышать значения поля \"Количество товарных предложений, обрабатываемых за шаг\"";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_MAXIMUM_PRODUCTS"] = "Максимум товаров";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_MAXIMUM_PRODUCTS_HELP"] = "Важный параметр, определяет максимальное количество товаров, выгружаемых в файл экспорта. По умолчанию: 0 - не ограничивать";
$MESS["ACRIT_GOOGLEMERCHANT_NAJMITE_DLA_VYBORA_D"] = "Нажмите для выбора даты";
$MESS["ACRIT_GOOGLEMERCHANT_EXPORTP_STEP"] = "Количество товарных предложений, обрабатываемых за шаг.<br/>Рекомендации по выбору количества потоков выгрузки";
$MESS["ACRIT_GOOGLEMERCHANT_EXPORTP_STEP_CALC"] = "Рассчитать";
$MESS["ACRIT_GOOGLEMERCHANT_RUN_INNEW_WINDOW"] = "Запустить экспорт в новом окне";
$MESS["ACRIT_GOOGLEMERCHANT_RUN_FILE_EXPORT"] = "Сгенерировать файл экспорта";
$MESS["ACRIT_GOOGLEMERCHANT_RERUN_FILE_EXPORT"] = "Перегенерировать файл экспорта";
$MESS["ACRIT_GOOGLEMERCHANT_FILE_EXPORT_NOT_EXIST"] = "Файл экспорта еще не создавался!";
$MESS["ACRIT_GOOGLEMERCHANT_FILE_EXPORT_NEED_RERUN"] = "Изменены настройки выгрузки. Необходимо перегенерировать файл экспорта!";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_EXPORT_RUN"] = "<b class='required' style='color: red;'>Экспорт уже запущен</b>";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_UNLOCK"] = "Удалить блокировку";
$MESS["ACRIT_GOOGLEMERCHANT_OPEN_INNEW_WINDOW"] = "Открыть файл в новом окне";
$MESS["ACRIT_GOOGLEMERCHANT_RUN_EXPORT_FILE_DESCRIPTION"] = '<span class="required"><b>Важно!</b></span> Для сохранение файлов экспорта создан специальный раздел "/acrit.googlemerchant/" в корне сайта.<br/>
 Настоятельно рекомендуем сохранять файлы в этот раздел или любой подраздел данного раздела.<br/>
 Если файл сохранен в рекомендуемом разделе, то при открытии на просмотр содержимое файла кэшироваться браузером не будет,<br/>
 в противном случае файл будет кэшироваться и при каждом открытии файла для просмотра в браузере<br/>
 необходимо сбрасывать кэш (нажать <b>Ctrl+R</b> или <b>Ctrl+F5</b>).
';

$MESS["ACRIT_GOOGLEMERCHANT_AGENT_ROW_ADD"] = "Добавить режим выгрузки";

$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES_NO_EXEC"] = "<span class=\"errortext\">Внимание! На Вашем сервере в целях безопасности отключена системная функция <code><b>exec()</b></code>, поэтому автоматическая конфигурация планировщика Cron невозможна. Используйте приведенную ниже инструкцию.</span>";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES0"] = "Режим Cron формирует в системе агент для данной выгрузки и обеспечивает ее выполнение с использованием утилиты Cron.";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES1"] = "Агенты - это PHP-функции, которые запускаются с определенной периодичностью. В самом начале загрузки каждой страницы система автоматически проверяет, есть ли агент, который нуждается в запуске, и в случае необходимости исполняет его.";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES2"] = "Утилита cron доступна только на хостингах, работающих под операционными системами семейства UNIX.";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES3"] = "Утилита cron работает в фоновом режиме и выполняет указанные задачи в указанное время. Для включения экспорта в список задач необходимо установить конфигурационный файл";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES4"] = "в cron. Этот файл содержит инструкции на выполнение указанных вами экспортов. После изменения набора экспортов, установленных на cron, необходимо заново установить конфигурационный файл.";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES5"] = "Для установки конфигурационного файла необходимо соединиться с вашим сайтом по SSH (SSH2) или какому-либо другому аналогичному протоколу, поддерживаемому вашим провайдером для удаленного доступа. В строке ввода нужно выполнить команду";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES6"] = "Для просмотра списка установленных задач нужно выполнить команду";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES7"] = "Для удаления списка установленных задач нужно выполнить команду";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES8"] = "Текущий список установленных на cron задач:";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES10"] = "Внимание! Если у вас установлены на cron задачи, которых нет в конфигурационном файле, то при применении этого файла такие задачи будут удалены.";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES11"] = "Оболочкой для выполнения задач на cron является файл";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES12"] = "Убедитесь, что в нем прописаны правильные пути к php и корню сайта.";
$MESS["ACRIT_GOOGLEMERCHANT_CES_NOTES13"] = "Количество потоков: ";


$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_TYPE_HELP"] = "Вы можете выбрать режим запуска генерации файла экспорта. В случае выбора работы через компонент для получения нового файла необходимо нажать на ссылку \"Запустить экспорт в новом окне\". В случае выбора запуска через Cron система сама в нужный момент, указанный в настройке даты и времени запуска, выполнит генерацию файла по указанному расписанию с учетом выбранного интервала в минутах";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILE_TYPE_HELP"] = "Модуль позволяет выгрузить данные в файл в двух форматах: xml и csv. Последний вариант используется, как правило, для выгрузки прайс-листов дилерам, либо же для открытия его в Excel";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_MODE_HELP"] = "Модуль позволяет запустить задачу на cron в двух режимах: php и curl. По умолчанию используется первый варинат";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_CRON_PHP_HELP"] = "В случае, если в настройках операционной системы не указаны пути по умолчанию (такое хоть и редко, но встречается), необходимо указать абсолютный путь до интерпретатора php. Например, так: /usr/bin/php";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_THREADS_HELP"] = "Критически важный параметр для управления скоростью генерации файла. По умолчанию равен 1 потоку, так как большинство хостингов не позволяют управлять выполнением  заданий в многопоточном режиме. Управление потоками используется для снижения времени генерации файлов экспорта для сверх больших каталогов (исчисляемых миллионами записей) и менять его можно только будучи полностью уверенным, что на хостинге разрешено управление потоками";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_USE_COMPRESS_HELP"] = "Опция позволяет сформировать файл в упакованном виде и используется в случаях с большими размерами файлов, так как, например, у Яндекса есть ограничение на размер принимаемого файла в 10 Мб";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_FILENAME_HELP"] = "Здесь указывается конечное имя формируемого файла и важно следить за тем чтобы имя файла не было одинаковым в разных выгрузках";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_DATESTART_HELP"] = "Дату начала выгрузки важно указывать с указанием точного времени старта генерации файла. Именно в указанную минуту произойдет старт. При этом учитывайте тот фактор, что на саму генерацию файла необходимо время и чем больших размеров ваш каталог товаров и чем меньшее значение указано в парметре \"количество товаров за шаг\", тем больше потребуется времени на выполнение генрации";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_PERIOD_HELP"] = "Важный параметр автоматического запуска генерации. Не указывайте слишком короткий промежуток запуска, например, 10 минут. Может запросто не хватить на полную отработку генерации файла и произойдет второй запуск что приведет в итоге к кофликту и некорректному формированию файла. Ставьте этот параметр, к примеру, 1440 минут, что равно одним суткам. Как правило, генерация файла раз в сутки вполне достаточна";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_LSATSTART_HELP"] = "Статистический параметр, необходимый для контроля последнего времени запуска генерации файла экспорта";
$MESS["ACRIT_GOOGLEMERCHANT_RUNTYPE_NEXTSTART_HELP"] = "Статистический параметр, необходимый для контроля следующего времени запуска генерации файла экспорта";
$MESS["ACRIT_GOOGLEMERCHANT_EXPORTP_STEP_HELP"] = "Очень важный параметр, влияющий на скорость генерации файла: чем меньше его значение, тем меньше создается нагрузка на сервер. При этом важно понимать, что при генерации в csv формате параметр не учитывается и генерация происходит за один шаг сразу всего каталога, что может вызвать очень большую нагрузку на сервер.<br/><br/>Для балансирования и распределения нагрузки на сервере, а также обеспечения максимальной производительности выгрузки можно использовать потоки. Здесь представлен модельный расчет данного параметра, рекомендуемый к установлению";
$MESS["ACRIT_GOOGLEMERCHANT_RUN_INNEW_WINDOW_HELP"] = "По данной ссылке запускается ручной режим генрации файла экспорта";
$MESS["ACRIT_GOOGLEMERCHANT_OPEN_INNEW_WINDOW_HELP"] = "По данной ссылке вы можете открыть последний сформированный выгрузкой файл экспотра";

$MESS["ACRIT_GOOGLEMERCHANT_LOG_STATISTICK"] = "Детальная статистика последней выгрузки";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ALL"] = "ВСЕГО в выгрузке:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ALL_IB"] = "Инфоблоков:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ALL_SECTION"] = "Разделов:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ALL_OFFERS"] = "Товарных предложений:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_EXPORT"] = "ВЫГРУЖЕНО:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_OFFERS_EXPORT"] = "Товарных предложений:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ERROR"] = "ОШИБКИ ВЫГРУЗКИ:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ERR_OFFERS"] = "Товарные предложения:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ERR_FORMAT"] = "Ошибки формата выгрузки:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_ALL_STAT"] = "Общая статистика по выгрузке, техническая информация";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_OPEN"] = "Открыть в новом окне";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_SEND_EMAIL"] = "Отправлять лог-файл на почту:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_PLACEHOLD"] = "email@email.com";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_FILE"] = "Протокол ошибок экспорта:";
$MESS["ACRIT_GOOGLEMERCHANT_LOG_UPDATE"] = "Обновить";

$MESS["ACRIT_GOOGLEMERCHANT_FEED_ADD_LINK"] = "Добавить feed";
$MESS["ACRIT_GOOGLEMERCHANT_YANDEX_ADD_LINK"] = "https://partner.market.yandex.ru/pre";
$MESS["ACRIT_GOOGLEMERCHANT_GOOGLE_ADD_LINK"] = "https://merchants.google.com/mc/products/sources/createDataSource";
$MESS["ACRIT_GOOGLEMERCHANT_PRICERU_ADD_LINK"] = "https://biz.price.ru/registration";
$MESS["ACRIT_GOOGLEMERCHANT_APORT_ADD_LINK"] = "http://www.aport.ru/user/register";
$MESS["ACRIT_GOOGLEMERCHANT_MAIL_ADD_LINK"] = "http://torg.mail.ru/ecb_account/reg/";
$MESS["ACRIT_GOOGLEMERCHANT_NADAVI_NET_ADD_LINK"] = "http://nadavi.net/reg_shop.php";
$MESS["ACRIT_GOOGLEMERCHANT_SRAVNI_COM_ADD_LINK"] = "http://sravni.com/?action=firm/registration&step=0";

$MESS["ACRIT_GOOGLEMERCHANT_OPEN_FEED_ADD_LINK"] = "Добавить новый feed";
$MESS["ACRIT_GOOGLEMERCHANT_OPEN_FEED_ADD_LINK_HELP"] = "По данной ссылке вы можете добавить новый feed на торговую площадку";

$MESS["ACRIT_GOOGLEMERCHANT_PROTECT_FIELDSET_HEADER"] = "Защита выгрузки";
$MESS["ACRIT_GOOGLEMERCHANT_PROTECT_FIELDSET_DESCRIPTION"] = "<b>\"ЗАЩИТА ВЫГРУЗКИ\"</b> позволяет защитить доступ к формируемому файлу выгрузки логином и паролем без использования которых получить его будет невозможно<br/><br/>";
$MESS["ACRIT_GOOGLEMERCHANT_SETUP_LOGIN_FIELDSET"] = "Логин: ";
$MESS["ACRIT_GOOGLEMERCHANT_SETUP_PASSWORD_FIELDSET"] = "Пароль: ";

$MESS["ACRIT_GOOGLEMERCHANT_EXPORT_UNLOCK"] = "Производятся системные действия по обработке остановленной выгрузки. Пожалуйста, ожидайте.";
$MESS["ACRIT_GOOGLEMERCHANT_EXPORT_UNLOCK_PAGE_CLEAR_SESSION"] = "Очистить сессию";
$MESS["ACRIT_GOOGLEMERCHANT_EXPORT_UNLOCK_PAGE_REFRESH"] = "Обновить страницу";
?>