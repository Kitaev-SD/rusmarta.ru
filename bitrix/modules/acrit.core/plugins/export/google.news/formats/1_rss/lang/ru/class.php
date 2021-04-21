<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Google.News (RSS)';

// Settings
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'XML_TITLE'] = '��������� XML';
	$MESS[$strSHint.'XML_TITLE'] = '��������� XML-����� (��� &lt;title&gt;).';
$MESS[$strSName.'XML_DESCRIPTION'] = '�������� XML';
	$MESS[$strSHint.'XML_DESCRIPTION'] = '��������� XML-����� (��� &lt;description&gt;).';
$MESS[$strSName.'XML_LINK'] = '����� �������� � XML';
	$MESS[$strSHint.'XML_LINK'] = '����� ��������, �������� /blog/ (�������������, �� ��������� ����� ����������� ������ �� ������� ��������).';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = '�������� ������ � �������';
$MESS[$strName.'guid'] = '������������� ������';
	$MESS[$strHint.'guid'] = '������������� ������ (��� ������ URL). <a href="https://support.google.com/news/publisher-center/answer/9545420?hl=ru" target="_blank">���������</a>';
$MESS[$strName.'guid@isPermaLink'] = '���� ������� ������ � guid';
	$MESS[$strHint.'guid@isPermaLink'] = '������ ������� �������� ������� ���������� ������ � ���� <b><code>guid</code></b>: � ����� ������ �������� ������ ���� <b><code>true</code></b>, ����� - <b><code>false</code></b>. <a href="https://support.google.com/news/publisher-center/answer/9545420?hl=ru" target="_blank">���������</a>';
$MESS[$strName.'pubDate'] = '���� ����������';
	$MESS[$strHint.'pubDate'] = '���� ���������� ������ (������ <code>Fri, 23 Jan 2015 23:17:00 +0000</code>).. ������ ��� ��������� Google ����������, ��������� �� � ������ ���������.';
$MESS[$strName.'title'] = '��������� ������';
	$MESS[$strHint.'title'] = '��������� ������';
$MESS[$strName.'description'] = '�������� ������';
	$MESS[$strHint.'description'] = '������� �������� ������.
	<ul>
		<li>���� �������� ���� <b><code>content:encoded</code></b> �����, � �������� �������� ����� �������������� ���� <b><code>description</code></b>.</li>
		<li>���� � ���� ���� ��� ���� (<b><code>content:encoded</code></b> � <b><code>description</code></b>), � Google �������� ����� ����������� ��� ���, ������� �������� �������� ������ ��������.</li>
	</ul>';
$MESS[$strName.'content:encoded'] = '������ ������� ������';
	$MESS[$strHint.'content:encoded'] = '������ ������� ������.
	<ul>
		<li>���� �������� ���� <b><code>content:encoded</code></b> �����, � �������� �������� ����� �������������� ���� <b><code>description</code></b>.</li>
		<li>���� � ���� ���� ��� ���� (<b><code>content:encoded</code></b> � <b><code>description</code></b>), � Google �������� ����� ����������� ��� ���, ������� �������� �������� ������ ��������.</li>
	</ul>';
$MESS[$strName.'link'] = '������ �� ������';
	$MESS[$strHint.'link'] = '������ �� ������.';
$MESS[$strName.'author'] = 'Email ������ ������';
	$MESS[$strHint.'author'] = 'Email ������ ������.';
