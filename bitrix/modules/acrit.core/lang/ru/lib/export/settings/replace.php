<?
$strMessPrefix = 'ACRIT_EXP_SETTINGS_REPLACE_';

$MESS[$strMessPrefix.'NAME'] = '������ � ������';
$MESS[$strMessPrefix.'DESC'] = '������� ����������� ��������� ������.<br/>
<p>������� �������� ���������� ���� �������� ������ ��� ������ (�.�. ��� ���������� ������� ���» �� ���������, ���� ����� ������ ����).</p>
<p>������� �RegExp� (�������� ������ � ������� �������� ������) ���������� ������������� ���������� ���������, ��� ���� ������� ����������� �� ������ ������. ��� ������ ������ ����� ���������� ���� ��� �������� <a href="http://php.net/manual/ru/reference.pcre.pattern.modifiers.php" target="_blank">������������� ���������� ���������</a>.</p>
<p><b>��������!</b> ��� ������ ������� �RegExp� � ������������� ������� �������� ������ �������������� php-�������� <code>str_ireplace</code>, ������� ����� ���: �� �������� ������ � ������ ������ ����� ������� ����� (����., ����� ���� ����� ��������, �� - ���).</p>
<p>������ ������ � ������������� ����������� ��������� (�������� ����� � ����������� �� ���������� � ���������� "cm"):<br/>
��� ����:<br/>
<code>^([\d]*)([\d])$</code><br/>
�� ��� ��������:<br/>
<code>$1.$2 cm</code><br/>
</p>
';
$MESS[$strMessPrefix.'GROUP'] = '������ � ������';

$MESS[$strMessPrefix.'FROM'] = '��� ����?';
$MESS[$strMessPrefix.'TO'] = '�� ��� ��������?';
$MESS[$strMessPrefix.'USE_REGEXP'] = 'RegExp';
$MESS[$strMessPrefix.'USE_REGEXP_HINT'] = '�������� �������, ���� ���������� ������������ ���������� ��������� ��� ������';
$MESS[$strMessPrefix.'MODIFIER'] = '�����.';
$MESS[$strMessPrefix.'MODIFIER_HINT'] = '������������ ��� RegExp';
$MESS[$strMessPrefix.'CASE_SENSITIVE'] = '�������';
$MESS[$strMessPrefix.'CASE_SENSITIVE_HINT'] = '�������� �������, ���� ���������� ��������� ������� ��� ������ ������';
$MESS[$strMessPrefix.'ADD'] = '��������';
$MESS[$strMessPrefix.'NOTHING'] = '���� �� ���������';
$MESS[$strMessPrefix.'DELETE_HINT'] = '������� ����� ������� ������';
?>