<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Wildberries.ru (API)';

// Steps
$MESS[$strLang.'STEP_PAUSE_BEFORE_STOCKS'] = 'Подготовка к выгрузке остатков';
$MESS[$strLang.'STEP_EXPORT_STOCKS'] = 'Выгрузка остатков';

// Settings
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'SUPPLIER_ID'] = 'Идентификатор поставщика (supplierID)';
	$MESS[$strSHint.'SUPPLIER_ID'] = 'Идентификатор поставщика можно узнать в <a href="https://suppliers.wildberries.ru/specification/client/index" target="_blank">личном кабинете Wildberries</a> (кнопка «Получить UUID»).<br/><br/>
	Данный идентификатор важен при проверке токена после авторизации, т.к. в дальнейшем все запросы выполняются от имени данного поставщика.';
	$MESS[$strLang.'SUPPLIER_ID'] = 'Пример: 10c4c229-32bf-4a03-accf-4f0a8d43349d';
$MESS[$strSName.'AUTH'] = 'Авторизация по номеру телефона';
	$MESS[$strSHint.'AUTH'] = 'Выгрузка на Wildberries требует авторизации в системе по номеру телефона.';
		$MESS[$strLang.'AUTH_PHONE'] = 'Номер телефона: +79998887766';
		$MESS[$strLang.'AUTH_BUTTON'] = 'Выполнить авторизацию';
		$MESS[$strLang.'AUTH_CONFIRM'] = 'Укажите код, отправленный Вам по SMS на #PHONE# (#LENGTH# символов):';
		$MESS[$strSName.'TOKEN'] = 'Токен авторизации';
			$MESS[$strSHint.'TOKEN'] = 'Токен для выполнения запросов к Wildberries.<br/><br/>Чтобы выделить токен, сделайте тройной клик в текстовом поле.';
			$MESS[$strLang.'TOKEN_EMPTY'] = 'Токен отсутствует';
			$MESS[$strLang.'TOKEN_CHECK'] = 'Проверить токен';
			$MESS[$strLang.'TOKEN_GET'] = 'Получить токен';
			$MESS[$strLang.'TOKEN_CLEAR_CONFIRM'] = 'Вы уверены, что хотите очистить токен?'.PHP_EOL.'Это потребует новой авторизации.';
			$MESS[$strLang.'TOKEN_ERROR_SUPPLIER_ID'] = 'Укажите корректный идентификатор поставщика';
			$MESS[$strSName.'AUTHORIZATION'] = 'Единый токен авторизации';
			$MESS[$strSHint.'AUTHORIZATION'] = 'Укажите здесь единый токен авторизации, получить его можно на странице <a href="https://suppliers-portal.wildberries.ru/supplier-settings/access-to-new-api" target="_blank">личного кабинета</a>.';
$MESS[$strLang.'EXPORT_STOCKS_CHECKBOX'] = 'Выгружать остатки';
	$MESS[$strLang.'EXPORT_STOCKS_HINT'] = 'Отметьте опцию, если необходимо отправлять остатки этим же профилем.
	<br/><br/>Для выгрузки необходимо указать <a href="https://suppliers-portal.wildberries.ru/marketplace-pass/api-access" target="_blank">токен</a>, ID склада (он указан в ЛК Wildberries) и название склада - любой текст для удобства.';
	// $MESS[$strLang.'STOCK_TOKEN'] = 'Токен для выгрузки остатков';
	// 	$MESS[$strLang.'STOCK_TOKEN_DESC'] = 'Токен можно получить на странице <a href="#TOKEN_URL#" target="_blank">Доступ к API</a>.';
	// 	$MESS[$strLang.'STOCK_TOKEN_GET'] = 'Получить токен';
	$MESS[$strLang.'STOCK_ID'] = 'ID склада';
	$MESS[$strLang.'STOCK_NAME'] = 'Название склада';
	$MESS[$strLang.'STOCK_HINT'] = 'ID склада можно узнать на странице <a href="#STORE_URL#" target="_blank">Склад поставщика</a> (в настройках личного кабинета).<br/><br/>
Название склада - произвольное название для удобства управления в списке полей.';
	$MESS[$strLang.'FIELD_STOCK'] = 'Остаток «#STORE_NAME#» [#STORE_ID#]';
	$MESS[$strLang.'FIELD_STOCK_DESCRIPTION'] = 'Укажите здесь значение остатка для выбранного склада.';
	$MESS[$strLang.'OFFERS_NEW_MODE_CHECKBOX'] = 'Выгружать ТП как товары (для особых случаев!)';
	$MESS[$strLang.'OFFERS_NEW_MODE_HINT'] = 'Данная опция включает режим выгрузки, при котором каждое торговое предложение выгружается не как вариация, а как отдельный товар.<br/><br/>
	Это полезно в случаях, когда ТП представляют собой не простейшие вариации, различающиеся ценой, цветом, размером, и т.д., но и дополнительными характеристиками, в т.ч. если у каждого ТП могут быть свои фотографии.<br/><br/>
	На одном и том же сайте данная опция может быть включена для выгрузки одних категорий товаров и отключена для других - в зависимости от характера данных в ТП.<br/><br/>
	<b>Внимание!</b> Не рекомедуем изменять режим после выгрузки.';


// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Основные данные о товарах';
$MESS[$strName.'object'] = 'Категория товара Wildberries';
	$MESS[$strHint.'object'] = 'Укажите здесь название категории.<br/><br/>
	Название категории должно быть указано в точности так же, как на Wildberries. Искать категории можно, нажав кнопку «Добавить категорию» на вкладке «Категории».';
// $MESS[$strName.'supplierVendorCode'] = 'Артикул поставщика';
// 	$MESS[$strHint.'supplierVendorCode'] = 'Артикул поставщика. Допускаются только латинские символы, цифры, символ подчеркивания.';
$MESS[$strName.'countryProduction'] = 'Страна-производитель';
	$MESS[$strHint.'countryProduction'] = 'Укажите здесь страну проиводства.';
$MESS[$strName.'vendorCode'] = 'Артикул';
	$MESS[$strHint.'vendorCode'] = 'Артикул товара.';
$MESS[$strName.'barcode'] = 'Штрихкод';
	$MESS[$strHint.'barcode'] = 'Штрихкод товара/предложения.<br/><br/>
Штрихкод должен быть уникальным в пределах всего портала Wildberries, иначе успешная выгрузка невозможна.';

$MESS[$strLang.'GUESS_BRAND'] = 'Бренд';
$MESS[$strLang.'GUESS_DESCRIPTION'] = 'Описание';
$MESS[$strLang.'GUESS_TNVED'] = 'Тнвэд';

$MESS[$strLang.'CUSTOM_ATTR_PRICE'] = 'Розничная цена';
	$MESS[$strLang.'CUSTOM_ATTR_PRICE_UNIT'] = 'рублей';
$MESS[$strLang.'CUSTOM_ATTR_PHOTO'] = 'Фото';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_PHOTO'] = 'Минимальное разрешение - <code><b>450х600</b></code><br/>
Максимальное количество - <code><b>10 фото</b></code>.';
$MESS[$strLang.'CUSTOM_ATTR_INGREDIENTS'] = 'Состав';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_INGREDIENTS'] = 'Каждый элемент из состава указывается в соотвествии со значениями из справочника. Значение указывается в процентах.<br/><br/>
Сумма значений процентного состава должна быть равна 100%.';
$MESS[$strLang.'CUSTOM_ATTR_ADDITIONAL_COLORS'] = 'Доп. цвета';
$MESS[$strLang.'CUSTOM_ATTR_KEYWORDS'] = 'Ключевые слова';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_KEYWORDS'] = 'Ключевые слова - это слова и фразы описывающие ваш продукт и предоставляют дополнительный контекст, чтобы помочь людям найти ваш товар.<br/><br/>
Примеры: Деревянный, 100% хлопок, длинный рукав.<br/><br/>
Допускается не более 16ти слов.';
$MESS[$strLang.'CUSTOM_ATTR_DESCRIPTION'] = 'Описание';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_DESCRIPTION'] = 'Опишите основные характеристики продукта, его особенности и преимущества.<br/><br/>
Максимальное количество символов - 1000.<br/><br/>
Разрешённые символы: <code><b>а-яА-ЯёЁ0-9a-zA-Z @!?,.|/:;\'"*&@#$№%[]{}()+-$</b></code>';

$MESS[$strLang.'REF'] = ' (из справочника)';
$MESS[$strLang.'FOR_VARIATION'] = ' (для вариации)';

// $MESS[$strLang.'FIELD_FOR_NOMENCLATURE'] = ' [доп. поле]';

$MESS[$strLang.'ERROR_EMPTY_PRODUCT_CATEGORY'] = 'Для товара #ELEMENT_ID# не указана категория.';

$MESS[$strLang.'NOTICE_SUPPORT'] = '<b>Внимание!</b> На данный формат выгрузки не распространяются условия бесплатной техподдержки. Помощь в настройке осуществляется <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">на платной основе</a>.';

$MESS[$strLang.'LOG_ELEMENT'] = 'Товар ##ELEMENT_ID# [#VENDOR_CODE#]: #METHOD#';
$MESS[$strLang.'LOG_ELEMENT_DEBUG'] = 'Товар ##ELEMENT_ID# [#VENDOR_CODE#]: ошибка загрузки данных #METHOD# #JSON# #RESULT#';
$MESS[$strLang.'LOG_IMAGE_UPLOAD_ERROR'] = 'Товар ##ELEMENT_ID#: Ошибка загрузки изображения #UUID# (#URL#): код ответа #RESPONSE_CODE#: #RESPONSE#';
$MESS[$strLang.'LOG_STOCKS_EXPORTED'] = 'Остатки выгружены (всего: #COUNT#).';
$MESS[$strLang.'LOG_STOCKS_ERROR_TITLE'] = 'Ошибка выгрузки остатков.';
$MESS[$strLang.'LOG_STOCKS_ERROR'] = 'Остатки не выгружены из-за ошибки: #RESPONSE_CODE# <pre>#HEADERS#</pre> <pre>#CONTENT#</pre>';

$MESS[$strLang.'ERROR_LOGIN_TOO_MANY_ATTEMPLS'] = 'Пожалуйста, повторите запрос примерно через #TIME# с.';
$MESS[$strLang.'ERROR_WRONG_CONFIRM_CODE'] = 'Указан неверный SMS-код.';
$MESS[$strLang.'ERROR_RECEIVED_EMPTY_TOKEN'] = 'Ошибка авторизации. Попробуйте ещё раз.';
$MESS[$strLang.'ERROR_EMPTY_REQUIRED_FIELDS'] = 'Для категории «#CATEGORY#» не заполнены обязательные поля: #FIELDS#';
$MESS[$strLang.'ERROR_INGREDIENTS_SUMM'] = 'Сумма значений процентного состава должна быть равна 100% [элемент ID = #ELEMENT_ID#].';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API'] = 'Ошибка отправки товаров в Wildberries.';
