<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_DATEFORMAT_';

$arFormats = array(
	\CDatabase::DateFormatToPHP(FORMAT_DATETIME),
	'Y-m-d H:i:s',
	'Y-m-d',
	'd.m.Y H:i',
	'd.m.Y',
);
$strFormats = '';
foreach($arFormats as $index => $strFormat){
	$strFormats .= '<tr><td style="padding-right:10px;white-space:nowrap;width:1px;"><b><code>'.$strFormat.'</code></b></td><td>'.date($strFormat).($index==0?' <b>(������� ������)</b>':'').'</td></tr>';
}
#
$MESS[$strMessPrefix.'NAME'] = '�������� ������ ����';
$MESS[$strMessPrefix.'DESC'] = '������ ����� ��������� �������� ������ ����/������� �� ������ ������� � ������<br/><br/>����������� <a href="http://php.net/manual/en/function.date.php" target="_blank">������ PHP</a>, ��������:<br/><table style="width:100%">'.$strFormats.'</table>';
$MESS[$strMessPrefix.'TEXT'] = ' => ';
$MESS[$strMessPrefix.'KEEP'] = '����.';
$MESS[$strMessPrefix.'KEEP_HINT'] = '�������� ������ �������, ���� ����� ��������� ��������, ������� �� ���� �������������� �� ������� ������������� �������. � ��������� ������ �������� ���������.';
$MESS[$strMessPrefix.'CHANGE'] = '�������� ����/�����:';
$MESS[$strMessPrefix.'CHANGE_DAYS'] = '����';
$MESS[$strMessPrefix.'CHANGE_HOURS'] = '�����';
$MESS[$strMessPrefix.'CHANGE_MINUTES'] = '�����';
$MESS[$strMessPrefix.'CHANGE_SECONDS'] = '������';
$MESS[$strMessPrefix.'CHANGE_HINT'] = '�� ������ ��������� (����., +30) ��� ��������� (����., -20) �������� ����.';
$MESS[$strMessPrefix.'CHANGE_LOG_MESSAGE'] = '������ ��������� ����: #MESSAGE# [����: #FIELD#, ��������: #VALUE#].';
?>