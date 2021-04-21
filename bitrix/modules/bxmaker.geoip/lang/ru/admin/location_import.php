<?

	$MODULE_ID = 'bxmaker.geoip';

	$MESS[$MODULE_ID . 'ACCESS_DENIED'] = 'Нет доступа';

	$MESS[$MODULE_ID . '_MAIN_PAGE'] = 'Импорт местоположений';


	$MESS[$MODULE_ID . '.TAB.EDIT']   = 'Импорт местоположений';
	$MESS[$MODULE_ID . '.PAGE_TITLE'] = 'Импорт местоположений';

	$MESS[$MODULE_ID . '.BTN_IMPORT'] = 'Импортировать';
	$MESS[$MODULE_ID . '.BTN_STOP']   = 'Остановить';

	$MESS[$MODULE_ID . '_COUNTRY_FIELD']   = 'Страна';
	$MESS[$MODULE_ID . '_COUNTRY_CODE.RU']   = 'Россия';
	$MESS[$MODULE_ID . '_COUNTRY_CODE.UA']   = 'Украина';
	$MESS[$MODULE_ID . '_COUNTRY_CODE.KZ']   = 'Казахстан';


	$MESS[$MODULE_ID . '.IMPORT_INFO_BOX'] = 'Нажмите кнопку "' . $MESS[$MODULE_ID . '.BTN_IMPORT'] . '"  чтобы начать импрт местоположений, если местоположения уже существуют то они будут перезаписаны.
	<b>Важно:</b> импортруемые местополоежния никак НЕ СВЯЗАНЫ с местоположениями интернет-магазина, этот вараинт используется для редакций битрикса младше "Малый Бизнес" в которых нет
	модуля интернет-магазина и соответственно местоположений';


	$MESS[$MODULE_ID . '.AJAX.IMPORT_STATUS_CLEAN'] = 'Файлы загружены. Таблицы подготовлны.';
	$MESS[$MODULE_ID . '.AJAX.IMPORT_STATUS_OK']   = 'Обработано #I# из #COUNT# строк. Импорт завершен';

	// Ошибки
	$MESS[$MODULE_ID . '.AJAX.IMPORT_STOPED'] = 'Импорт остановлен';
	$MESS[$MODULE_ID . '.AJAX.ERROR_DOWNLOAD_COUNTRY'] = 'Не удалось загрузить страны, попробуйте еще раз, при повторных
	проблемах повторите попытку позже (если так и не удалось загрузить файл, напишите в тех. поддержку модуля)';

	$MESS[$MODULE_ID . '.AJAX.ERROR_DOWNLOAD_CITY'] = 'Не удалось загрузить города, попробуйте еще раз, при повторных
	проблемах повторите попытку позже (если так и не удалось загрузить файл, напишите в тех. поддержку модуля)';

	$MESS[$MODULE_ID . '.AJAX.IMPORT_STATUS_COUNTRY'] = 'Идет импорт информации о стране, текущая позиция - #I# из #COUNT#,   #PERCENT#%';
	$MESS[$MODULE_ID . '.AJAX.IMPORT_STATUS_REGION'] = 'Идет импорт регионов, текущая позиция - #I# из #COUNT#,   #PERCENT#%';
	$MESS[$MODULE_ID . '.AJAX.IMPORT_STATUS_CITY'] = 'Идет импорт городов, текущая позиция - #I# из #COUNT#,   #PERCENT#%';

	$MESS[$MODULE_ID . '.AJAX.IMPORT_STATUS_OK'] = 'Импорт окончен';






