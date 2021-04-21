<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Google.News (Atom)';

// Settings
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'XML_TITLE'] = 'Заголовок XML';
	$MESS[$strSHint.'XML_TITLE'] = 'Заголовок XML-файла (тег &lt;title&gt;).';
$MESS[$strSName.'XML_DESCRIPTION'] = 'Описание XML';
	$MESS[$strSHint.'XML_DESCRIPTION'] = 'Заголовок XML-файла (тег &lt;description&gt;).';
$MESS[$strSName.'XML_LINK'] = 'Адрес страницы в XML';
	$MESS[$strSHint.'XML_LINK'] = 'Адрес страницы, например /blog/ (необязательно, по умолчанию будет выгружаться ссылка на главную страницу).';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = 'Основные данные о товарах';
$MESS[$strName.'id'] = 'Идентификатор';
	$MESS[$strHint.'id'] = 'Идентификатор статьи (любая уникальная строка).';
$MESS[$strName.'published'] = 'Дата публикации';
	$MESS[$strHint.'published'] = 'Дата публикации статьи (формат <code>2015-01-23T15:26:19.468-08:00</code>).';
$MESS[$strName.'updated'] = 'Дата публикации';
	$MESS[$strHint.'updated'] = 'Дата изменения статьи (формат <code>2015-01-23T15:26:19.468-08:00</code>).';
$MESS[$strName.'title'] = 'Заголовок';
	$MESS[$strHint.'title'] = 'Заголовок статьи';
$MESS[$strName.'title@type'] = 'Тип заголовка';
	$MESS[$strHint.'title@type'] = 'Тип заголовка статьи (text или html).';
$MESS[$strName.'content'] = 'Контент';
	$MESS[$strHint.'content'] = 'Полный текст статьи.';
$MESS[$strName.'content@type'] = 'Тип контента';
	$MESS[$strHint.'content@type'] = 'Тип полного текста статьи (text или html).';
$MESS[$strName.'author.name'] = 'Автор (имя)';
	$MESS[$strHint.'author.name'] = 'Имя автора статьи.';
$MESS[$strName.'author.email'] = 'Автор (e-mail)';
	$MESS[$strHint.'author.email'] = 'E-mail автора статьи.';
