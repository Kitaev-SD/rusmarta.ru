<?

$strMessPrefix = 'ACRIT_EXP_MARKETGID_';

// General
$MESS[$strMessPrefix . 'NAME'] = 'MarketGid';


// Default settings
$MESS[$strMessPrefix . 'SETTINGS_FILE'] = '�������� ����';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = '��������, /upload/xml/MarketGid.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = '������� ����� ����, � ������� ����� ����������� ������� �� ������� �������.<br/><br/><b>������ �������� �����</b>:<br/><code>/upload/xml/file.xml</code>';
$MESS[$strMessPrefix . 'SETTINGS_ENCODING'] = '��������� �����';
$MESS[$strMessPrefix . 'SETTINGS_ENCODING_HINT'] = '�������� ��������� �����. �������������� ������� ����� ����������� ���.';
$MESS[$strMessPrefix . 'SETTINGS_ZIP'] = '��������� � Zip';
$MESS[$strMessPrefix . 'SETTINGS_ZIP_HINT'] = '������ �������� ��������� ���������� �������������� ���� � Zip. ��������� �������� � Zip-�����, ������ �����, ����������� � ������.������, ����������� �����������, ��� �������� ��� ���������� ��������.';
$MESS[$strMessPrefix . 'SETTINGS_DELETE_XML_IF_ZIP'] = '������� XML-����';
$MESS[$strMessPrefix . 'SETTINGS_DELETE_XML_IF_ZIP_HINT'] = '������ ����� ��������� ������� ��������������� XML-����, ������� ������ ZIP-�����.';


// Fields
$MESS[$strMessPrefix . 'FIELD_ID_NAME'] = '������������� ������';
$MESS[$strMessPrefix . 'FIELD_ID_DESC'] = '	������� ���� teaser - ������������. ���������� ������������� ������, ������� �������� � ���� ������� �� �������� ������. ���������� �������� [0-9, A-Z, a-z, -, _]  ';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_NAME'] = '������� ������';
$MESS[$strMessPrefix . 'FIELD_AVAILABLE_DESC'] = '������� ���� teaser - �� ������������. �������� �� ��������� = false ������ ������. ���������� �������� [0, 1, true, false]. ��� �������� false ������ �� ���������.     ';
$MESS[$strMessPrefix . 'FIELD_NAME_NAME'] = '��������� ������';
$MESS[$strMessPrefix . 'FIELD_NAME_DESC'] = '���- ������������. ��������� ������. � ������ �� ������ ��������� 65 ��������, ����� ����� ������� �� 65 ���������� ������� (�� ��������� ���������� �����, �� ������������ 65 ��������). ��������� �� �� �������, ��� � � �������� ������. ';
$MESS[$strMessPrefix . 'FIELD_URL_NAME'] = '������';
$MESS[$strMessPrefix . 'FIELD_URL_DESC'] = '���- ������������. ������ �� �������� ������.';
$MESS[$strMessPrefix . 'FIELD_PRICE_NAME'] = '���� ������';
$MESS[$strMessPrefix . 'FIELD_PRICE_DESC'] = '���� ������ � ��������� ������. ���������� �������� - [0-9]';

$MESS[$strMessPrefix . 'FIELD_CURRENCY_ID_NAME'] = '��� ������';
$MESS[$strMessPrefix . 'FIELD_CURRENCY_ID_DESC'] = '������������� ������, � ������� ������� ���� ������. �������� �� ��������� = USD. ������ ��������� �������� �� ������:RUB,UAH,USD,EUR,BYN,INR,ILS,GEL,KZT,AED';
$MESS[$strMessPrefix . 'FIELD_PICTURE_NAME'] = '��������';
$MESS[$strMessPrefix . 'FIELD_PICTURE_DESC'] = '���- ������������. ������ �� ����������� ������. ���� ����������� ������ ���� �� ������ ��� 492x328. � ������ ��� ������ ��������������� ����� �������. ���������� ����������: *.jpg, *.jpeg     ';

$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_NAME'] = '��������';
$MESS[$strMessPrefix . 'FIELD_DESCRIPTION_DESC'] = '���- ������������. ��������� ����� (�������� ��������). � ������ �� ������ ���������  75 ��������, ����� ����� ������� �� 75 ���������� ������� (�� ��������� ���������� �����, �� ������������  75 ��������). ��������� �� �� �������, ��� � � �������� �����';

$MESS[$strMessPrefix . 'FIELD_GROUP_ID_NAME'] = '������������� ��������� ������';
$MESS[$strMessPrefix . 'FIELD_GROUP_ID_DESC'] = '��� - ������������. ���������� ������������� ��������� ������. ��� �� ����� ������� ������������� ���������, ������������ � ������� ���������.   ';

# Steps
$MESS[$strMessPrefix . 'STEP_EXPORT'] = '������ � XML-����';
$MESS[$strMessPrefix . 'STEP_ZIP'] = '��������� � Zip';

# Display results
$MESS[$strMessPrefix . 'RESULT_GENERATED'] = '���������� ����� �������';
$MESS[$strMessPrefix . 'RESULT_EXPORTED'] = '����� ���������';
$MESS[$strMessPrefix . 'RESULT_ELAPSED_TIME'] = '��������� �������';
$MESS[$strMessPrefix . 'RESULT_DATETIME'] = '����� ���������';
$MESS[$strMessPrefix . 'RESULT_FILE_ZIP'] = '������� ZIP-�����';

#
$MESS[$strMessPrefix . 'NO_EXPORT_FILE_SPECIFIED'] = '�� ������ ���� � ��������� �����.';
$MESS[$strMessPrefix . 'WRONG_VALUE_FOR_AGE_YEAR'] = '������������ �������� ��� ���� �age� (unit=�year�): #TEXT#';
$MESS[$strMessPrefix . 'WRONG_VALUE_FOR_AGE_MONTH'] = '������������ �������� ��� ���� �age� (unit=�month�): #TEXT#';
$MESS[$strMessPrefix . 'GIFTS_ARE_NOT_FOUND'] = '������� �� �������.';
$MESS[$strMessPrefix . 'CATEGORIES_EMPTY_ANSWER'] = '������ ��������� ��������� ( #URL# ). ���������� ��� ���.';
$MESS[$strMessPrefix . 'ERROR_SAVING_CATEGORIES_TMP'] = '������ ���������� ���������� ����� ���������: #FILE#. ��������� ������� ������� ��� ������ � ���� ����.';
$MESS[$strMessPrefix . 'CATEGORIES_ARE_EMPTY'] = '����������� ���� #URL# �� �������� ���������. ���������� ��� ���.';
$MESS[$strMessPrefix . 'ERROR_SAVING_CATEGORIES_TMP'] = '������ ���������� ����� � �����������: #FILE#. ��������� ������� ������� ��� ������ � ���� ����.';
?>