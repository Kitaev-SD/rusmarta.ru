<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_HTML2TEXT_';

$MESS[$strMessPrefix.'NAME'] = '������������� HTML � �����';
$MESS[$strMessPrefix.'DESC'] = '�������������� HTML-����������� � ����� (�������� ����� � ����������� ������ � ���).<br/>
<p>������ �������� ������� � �������, ����� ������� ����� � ������� HTML (� ���������������), �� ��������� ��� ����� ��� ��������������.</p>
<p>�������� ��������� ������:</p>
<ul>
	<li><b>����������� �����</b> - ������ �������������� ����������� ������� (������������ php-������� strip_tags � ���������� �����������),</li>
	<li><b>������� �����</b> - �������������� �������� �� ������� ������� HTMLToTxt,</li>
	<li><b>Html2text</b> - ������������ ������� ���������� <a href="https://github.com/soundasleep/html2text" target="_blank">html2text</a>,</li>
	<li><b>����������� ����������</b> - ����� ����������� ����������� ���������� �OnCustomHtmlToText� (���������� ��� ����������� � ����� init.php):<br/>
		<pre>
AddEventHandler("'.$GLOBALS['strModuleId'].'", "OnCustomHtmlToText", "MyOnCustomHtmlToText");<br/>
function MyOnCustomHtmlToText(&$mValue, $arParams, $obField){<br/>
	$intProfileID = $obField->getProfileID();<br/>
	if($intProfileID == 5) {<br/>
		$mValue = strip_tags($mValue);<br/>
	}<br/>
	else {<br/>
		$mValue = null;<br/>
	}<br/>
}
		</pre>
		���� �������� ������ <b>null</b>, �� ����� ������������� �������� ����������� �����.
	</li>
</ul>
';
$MESS[$strMessPrefix.'TYPE_SIMPLE'] = '����������� �����';
$MESS[$strMessPrefix.'TYPE_BITRIX'] = '�������-�����';
$MESS[$strMessPrefix.'TYPE_HTML2TEXT'] = 'Html2text';
$MESS[$strMessPrefix.'TYPE_CUSTOM'] = '����������� ����������';
?>