<?
$MESS['ACRIT_EXP_JSON_STRUCTURE'] = '��������� JSON';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_GENERAL'] = '����� ��������� JSON';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_GENERAL_HINT'] = '����� ����������� ����� ��������� JSON';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_NOTICE'] = '�������� ��������� �������:<br/>
#DATE# - ������� ����/����� � ������� �����,<br/>
#DATE(������ ����/������� PHP)#<br/>
��������: #DATE(Y-m-d H:i:s)#, #DATE(c)#, #DATE(r)#<br/>
������ �����, ��� ������� �� ����� ��������� � ������� - ��� ��� ����������� ������� ����� ������� �������������.';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_EXAMPLE'] = '{
	"date": #DATE#,
	"items": [
		#JSON_ITEMS#
	]
}';
	$MESS['ACRIT_EXP_JSON_STRUCTURE_PLACEHOLDER'] = '��������:'.PHP_EOL.htmlspecialcharsbx($MESS['ACRIT_EXP_JSON_STRUCTURE_EXAMPLE']);

$MESS['ACRIT_EXP_JSON_FIELDS'] = '���� JSON (������ ���� � ����� ������)';
	$MESS['ACRIT_EXP_JSON_ELEMENT_FIELDS'] = '���� ������� � JSON-�����';
	$MESS['ACRIT_EXP_JSON_ELEMENT_FIELDS_HINT'] = '������� ����� ������ ����� (������ ���� � ��������� ������) ��� �������.';
	$MESS['ACRIT_EXP_JSON_OFFER_FIELDS'] = '���� �������� ����������� � JSON-�����';$MESS['ACRIT_EXP_JSON_ELEMENT_FIELDS_HINT'] = '������� ����� ������ ����� (������ ���� � ��������� ������) ��� �������.';
	$MESS['ACRIT_EXP_JSON_OFFER_FIELDS_HINT'] = '������� ����� ������ ����� (������ ���� � ��������� ������) ��� �������� �����������. ���� ������ ����� ��� ������� � ��� �� �� �����������, �� ���� ��� �� ����� �� ���������.';
	$MESS['ACRIT_EXP_JSON_OFFER_FIELDS_NOTICE'] = '<small>������ ���� ���� �� ���������� �� ����� �������</small>';
	$MESS['ACRIT_EXP_JSON_FIELD_PLACEHOLDER'] = '��������:'.PHP_EOL.'#EXAMPLE#';
	$MESS['ACRIT_EXP_JSON_FIELDS_NOTICE'] = '<b>��������!</b> ����� ��������� ������ ����� ���������� ��������� ���������. ���� �� ������� �������� ����, ���������� ����� ������������� �����. ���� (������� ���������� ����������).';
	
$MESS['ACRIT_EXP_JSON_SETTINGS'] = '�������������� ���������';
	$MESS['ACRIT_EXP_JSON_ADD_UTM'] = '��������� UTM-�����';
		$MESS['ACRIT_EXP_JSON_ADD_UTM_HINT'] = '�������� ������ �����, ���� ����� ��������� UTM-����� � �������: ��� ���� � ������ ����� ����������� ����� ���� (utm_content, utm_source � ��).';
	$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS'] = '�� ������ �������';
	$MESS['ACRIT_EXP_JSON_UTM_FIELD'] = '����, � ������� ����� �������� UTM-�����';
		$MESS['ACRIT_EXP_JSON_UTM_FIELD_HINT'] = '������� ����� ���� (��� ���� �� ��������� - ����� �������), � ������� ���������� �������� UTM-�����.';
	$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS'] = '�� ������ �������';
		$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS_HINT'] = '������ ����� ��������� ��������� �� �� � ����� ���� � �������, � ������ ����, � ��������� ���� (���� ����������� ����).';
	$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS_FIELD'] = '���� ������ ��� �������� ��';
		$MESS['ACRIT_EXP_JSON_OFFERS_PREPROCESS_FIELD_HINT'] = '�������, � ����� ���� ������ ����� �������� ������ � ��������� �������������. ��������, offers, ��� offers.items - � ������ ������ ����� ��������� ��� ���������� ������� ����������� - �.�. ����� ������� ���� offers, ������ ���� ���� items, � ������ ���� ����� ������ ��.';
	$MESS['ACRIT_EXP_JSON_TRANSFORM_FIELDS'] = '���� ��� �������������';
		$MESS['ACRIT_EXP_JSON_TRANSFORM_FIELDS_HINT'] = '������� ����� ���� (����� �������) ��� �������������, ��� ������� ������ ��������� �������, ����������� �������� ������� ������ �� �������������, ������������ ����� ������, ���������� �������� � ������������ ����������������, ��������:
<table style="table-layout:fixed;width:100%;">
<tbody>
<tr>
<td style="vertical-align:top; width:50%;">
<pre>
{<br/>
	"sub": {<br/>
		"key": [<br/>
			"2222",<br/>
			"ffffffffff"<br/>
		],<br/>
		"value": [<br/>
			"22222222222",<br/>
			"gggggggggg"<br/>
		]<br/>
	}<br/>
}<br/>
</pre>
</td>
<td style="vertical-align:top; width:50%;">
<pre>
{<br/>
	"sub": [<br/>
		{<br/>
			"key": "2222",<br/>
			"value": "22222222222"<br/>
		},<br/>
		{<br/>
			"key": "ffffffffff",<br/>
			"value": "gggggggggg"<br/>
		}<br/>
	]<br/>
}<br/>
</pre>
</td>
</tr>
</tbody>
</table>
��� ��������� � ������� ���������� ��������� ����� �������� �������, ��������� �� ����� ����� �� �������.<br/><br/>
���� ����� ��������� ����� ����������� - ��������, properties, ��� properties.delivery (���� delivery ������ ���� properties)';
	$MESS['ACRIT_EXP_JSON_ENCODE_OPTIONS'] = '����� ����������� JSON';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTIONS_HINT'] = '������� ����� ����� ����������� JSON, �������� <b><code>JSON_PRETTY_PRINT</code></b>.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_PRETTY_PRINT'] = '��������������� ������� ���';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_UNESCAPED_UNICODE'] = '�� ���������� ������������� ������� Unicode.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_FORCE_OBJECT'] = '�������� ������� ����� ��� ��������������� ��������.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_UNESCAPED_SLASHES'] = '�� ������������ ����� /.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_QUOT'] = '���������� ������� ������� �������.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_APOS'] = '���������� ������� ��������� �������';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_TAG'] = '���������� ������� &lt; � &gt;.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_HEX_AMP'] = '���������� ������� &amp;.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_INVALID_UTF8_IGNORE'] = '������������ ������������ ������� UTF-8';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_INVALID_UTF8_SUBSTITUTE'] = '���������� ������������ ������� UTF-8.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_NUMERIC_CHECK'] = '������������ �����, ���������� �����, ��� �����';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_PRESERVE_ZERO_FRACTION'] = '������� �� ��������� ������� ����� � �����.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_UNESCAPED_LINE_TERMINATORS'] = '�� ���������� ������� ����� ������.';
		$MESS['ACRIT_EXP_JSON_ENCODE_OPTION_JSON_PARTIAL_OUTPUT_ON_ERROR'] = '����������� �������� �� ��������� ������ ���������.';






?>