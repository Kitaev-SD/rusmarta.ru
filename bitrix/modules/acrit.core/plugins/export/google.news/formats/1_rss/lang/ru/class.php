<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Google.News (RSS)';

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
$MESS[$strName.'guid'] = 'Идентификатор статьи';
	$MESS[$strHint.'guid'] = 'Идентификатор статьи (или полный URL). <a href="https://support.google.com/news/publisher-center/answer/9545420?hl=ru" target="_blank">Подробнее</a>';
$MESS[$strName.'guid@isPermaLink'] = 'Флаг наличия ссылки в guid';
	$MESS[$strHint.'guid@isPermaLink'] = 'Данный атрибут означает наличие постоянной ссылки в поле <b><code>guid</code></b>: в таком случае значение должно быть <b><code>true</code></b>, иначе - <b><code>false</code></b>. <a href="https://support.google.com/news/publisher-center/answer/9545420?hl=ru" target="_blank">Подробнее</a>';
$MESS[$strName.'pubDate'] = 'Дата публикации';
	$MESS[$strHint.'pubDate'] = 'Дата публикации статьи (формат <code>Fri, 23 Jan 2015 23:17:00 +0000</code>).. Данный тег позволяет Google определить, вносились ли в статью изменения.';
$MESS[$strName.'title'] = 'Заголовок статьи';
	$MESS[$strHint.'title'] = 'Заголовок статьи';
$MESS[$strName.'description'] = 'Описание статьи';
	$MESS[$strHint.'description'] = 'Краткое описание статьи.
	<ul>
		<li>Если значение поля <b><code>content:encoded</code></b> пусто, в качестве контента будет использоваться поле <b><code>description</code></b>.</li>
		<li>Если в фиде есть оба тега (<b><code>content:encoded</code></b> и <b><code>description</code></b>), в Google Новостях будет учитываться тот тег, контент которого содержит больше символов.</li>
	</ul>';
$MESS[$strName.'content:encoded'] = 'Полный контент статьи';
	$MESS[$strHint.'content:encoded'] = 'Полный контент статьи.
	<ul>
		<li>Если значение поля <b><code>content:encoded</code></b> пусто, в качестве контента будет использоваться поле <b><code>description</code></b>.</li>
		<li>Если в фиде есть оба тега (<b><code>content:encoded</code></b> и <b><code>description</code></b>), в Google Новостях будет учитываться тот тег, контент которого содержит больше символов.</li>
	</ul>';
$MESS[$strName.'link'] = 'Ссылка на статью';
	$MESS[$strHint.'link'] = 'Ссылка на статью.';
$MESS[$strName.'author'] = 'Email автора статьи';
	$MESS[$strHint.'author'] = 'Email автора статьи.';
