<?
$strMessPrefix = 'ACRIT_EXP_CUSTOM_EXCEL_GENERAL_';

// General
$MESS[$strMessPrefix.'NAME'] = '[������������ Excel]';

// Separators
$MESS[$strMessPrefix.'SEPARATOR_COMMA'] = '�������';
$MESS[$strMessPrefix.'SEPARATOR_SEMICOLON'] = '����� � �������';
$MESS[$strMessPrefix.'SEPARATOR_TAB'] = '���������';
$MESS[$strMessPrefix.'SEPARATOR_SPACE'] = '������';

// Line types
$MESS[$strMessPrefix.'LINE_TYPE_CRLF'] = '\r\n (Windows)';
$MESS[$strMessPrefix.'LINE_TYPE_LF'] = '\n (Unix)';
$MESS[$strMessPrefix.'LINE_TYPE_CR'] = '\r (MacOS)';


// Default settings
$MESS[$strMessPrefix.'SETTINGS_FILE'] = '�������� ����';
	$MESS[$strMessPrefix.'SETTINGS_FILE_PLACEHOLDER'] = '��������, /upload/excel/custom.excel';
	$MESS[$strMessPrefix.'SETTINGS_FILE_HINT'] = '������� ����� ����, � ������� ����� ����������� ������� �� ������� �������.<br/><br/><b>������ �������� �����</b>:<br/><code>/upload/excel/custom.xls</code>';
$MESS[$strMessPrefix.'SETTINGS_FORMAT'] = '������ �����';
	$MESS[$strMessPrefix.'SETTINGS_FORMAT_HINT'] = '�������� ��������� ������ ����� Excel: xlsx (�����) ��� xls (������).<br/><br/><b>��������!</b> ��� ��������� ������� ������������� �������� ���������� ����� � ���� ��������� ����, ������ �����������!';
$MESS[$strMessPrefix.'SETTINGS_ZIP'] = '��������� � Zip';
	$MESS[$strMessPrefix.'SETTINGS_ZIP_HINT'] = '������ �������� ��������� ���������� �������������� ���� � Zip. ��������� �������� � Zip-�����, ������ ����� ����������� �����������, ��� �������� ��� ����������.';
$MESS[$strMessPrefix.'SETTINGS_DELETE_EXCEL_IF_ZIP'] = '������� Excel-����';
	$MESS[$strMessPrefix.'SETTINGS_DELETE_EXCEL_IF_ZIP_HINT'] = '������ ����� ��������� ������� ��������������� Excel-����, ������� ������ ZIP-�����.';

// Formats
$MESS[$strMessPrefix.'FORMAT_XLSX'] = 'xlsx (����� ������ MS Excel)';
$MESS[$strMessPrefix.'FORMAT_XLS'] = 'xls (������ ������ MS Excel)';
$MESS[$strMessPrefix.'FORMAT_ODS'] = 'ods (������ OpenOffice)';

// Tabs
$MESS[$strMessPrefix.'TAB_EXCEL_SETTINGS_NAME'] = '��������� Excel';
	$MESS[$strMessPrefix.'TAB_EXCEL_SETTINGS_TITLE'] = '����� ��������� Excel';

//
$MESS[$strMessPrefix.'DEFAULT_SHEET_TITLE'] = '������� �������';

// Headers
$MESS[$strMessPrefix.'HEADER_GENERAL'] = '����� ������';
$MESS[$strMessPrefix.'HEADER_DELIVERY'] = '������ � ��������';
$MESS[$strMessPrefix.'HEADER_MORE'] = '�������������� ������';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = '������������� ������';
	$MESS[$strMessPrefix.'FIELD_ID_DESC'] = '������������� �����������. ����� �������� ������ �� ���� � ��������� ����. ������������ ����� � 20 ��������. ������ ���� ���������� ��� ������� �����������.<br/><br/>�������� ��������� ��� offer.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/id-type-available.html" target="_blank">��������� �������� ��������.</a>';
$MESS[$strMessPrefix.'FIELD_CBID_NAME'] = '������ ������ �� �������� ������';
	$MESS[$strMessPrefix.'FIELD_CBID_DESC'] = '������ ������ �� �������� ������.<br/><br/>�������� ��������� ��� offer.<br/><br/><a href="https://yandex.ru/support/partnermarket/bid-cbid.html" target="_blank">��������� �������� ��������.</a>';
$MESS[$strMessPrefix.'FIELD_BID_NAME'] = '������ ������ �� ��������� ������ ����������';
	$MESS[$strMessPrefix.'FIELD_BID_DESC'] = '������ ������ �� ��������� ������ ���������� (���, ����� �������� ������).<br/><br/>�������� ��������� ��� offer.<br/><br/><a href="https://yandex.ru/support/partnermarket/bid-cbid.html" target="_blank">��������� �������� ��������.</a>';
$MESS[$strMessPrefix.'FIELD_AVAILABLE_NAME'] = '������� ������';
	$MESS[$strMessPrefix.'FIELD_AVAILABLE_DESC'] = '������ ������:<br/><b>true � �� ������� / ������ � ��������</b><br/>����� ����� ��������� �������� ��� � ����� ������ � �����, ������� �� ��������� � ������ ��������. �� ������� � ������ ����� ������� ���������� ���� ��������.<br/><b>false � ��� �����</b><br/>������ ���� �������� �������� ��� � ����� ������ ����������. ���� ����� ���������� � ����������� ����������� (������������ ���� � ��� ������). �� ������� � ������ ����� �������� ������� ��� ����� ������ �����.<br/><br/><b>��������</b>. ������� ������������ � ���������� � ������, ����������� � ������ ��������. ������� �� ������������, ����� ������� ��������� ���������� �������� ��������� � �����-����� (������ �������).<br/><br/>�������� ��������� ��� offer. ���� ������� �� ������, ������������ �������� �� ��������� � true.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/id-type-available.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_URL_NAME'] = 'URL ������';
	$MESS[$strMessPrefix.'FIELD_URL_DESC'] = 'URL �������� ������ �� ����� ��������. ������������ ����� ������ � 512 ��������. ����������� ������������� ������.<br/><br/>���� �� ����������� ������������� ����, �� ������ ���� �������� �� ��������� HTTP (�� HTTPS). ����������� ������������� ������ � ������� Punycode.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = '���� ������';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = '���������� ���� ������.<br/><br/><b>����������</b>. ���� ����� ��������� �� ����, ������� � �. �. (�� �������), ���������� ���� �� ���� ������� �������. ��������, ���� �� �������� ������ �������, ���������� ���� �� �����.<br/>� ��������� ���������� (���� �����-���� ���������� � ������� YML) ��������� ��������� ��������� ���� ��� � � ������� �������� from="true".<br/>������: <code>&lt;price from="true"&gt;2000&lt;/price&gt;</code><br/><br/>��� ��������� � ��������� �����������: ��������� � ������, ������� ��������, ���������, ��������, �������� �������, ��������, ����������, ��������� � ������, ��������, ������������� ������, �������, ��������, ��������� ����������, ��������� ������ � ��������� �������, �������, ������ � �������, ������, ���������, �������, ��������, ���������, ������ � �������, �������, ���������, �������, �������.';
$MESS[$strMessPrefix.'FIELD_OLD_PRICE_NAME'] = '������ ���� ������';
	$MESS[$strMessPrefix.'FIELD_OLD_PRICE_DESC'] = '������ ���� ������. ������ ���� ���� ���������� ����. ������ ������������� ������������ ������� ����� ������ � ���������� ����� � ���������� ������������� ������.<br/><br/><a href="https://yandex.ru/support/partnermarket/oldprice.html" target="_blank">��������� �������� ��������</a>.<br/><br/><b>����������</b>. <a href="https://yandex.ru/support/partnermarket/efficiency/data-update.html" target="_blank">������ �����������</a> �� ������� ������ 40�80 �����.';
$MESS[$strMessPrefix.'FIELD_VAT_NAME'] = '������ ���';
	$MESS[$strMessPrefix.'FIELD_VAT_DESC'] = '������ ��� ��� ������. ������������, ������ ���� �� �������� �������� ������ �� �����-�����.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/vat.html" target="_blank">���������� �������� � ��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_NAME'] = '��� ������';
	$MESS[$strMessPrefix.'FIELD_CURRENCY_ID_DESC'] = '������, � ������� ������� ���� ������: RUB, USD, EUR, UAH, KZT, BYN. ���� � ������ ������ ��������������� ���� �����. ��������, ������ � USD ���� ��������� ���� � ��������, � �� � ������.<br/><br/><b>����������</b>. � ��������� ������� ��� ����������� ������� ���� ������� ����������� �����. ��� ������ ���� ���������� ��� ����� ����������� � ������ ������ �� �������� ����� �� ��.';
$MESS[$strMessPrefix.'FIELD_PICTURE_NAME'] = '��������';
	$MESS[$strMessPrefix.'FIELD_PICTURE_DESC'] = 'URL-������ �� �������� ������.<br/><br/><a href="https://yandex.ru/support/partnermarket/picture.html#requirements" target="_blank">����������� ������������ � ������������ � ������ � �����������</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_NAME'] = '����������� ���������� ��������';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_DESC'] = '����������� ���������� �������� �� ������� ��������.<br/><br/>��������� ��������:<br/><b>true</b> � ����� ����� ���� ��������� ��������.<br/><b>false</b> � ����� �� ����� ���� ��������� �������� (������ ���������);<br/><br/><b>��������</b>. ������� delivery ������ ����������� ����� �������� false, ���� ����� ��������� ��������� ������������ (��������� �������, ������������� ��������).<br/><br/>���� ������� �� ������, �� ����������� �������� �� ���������, ��. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_COST_NAME'] = '��������� ���������� ��������';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_COST_DESC'] = '��������� ��������.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_DAYS_NAME'] = '���� ���������� ��������';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_DAYS_DESC'] = '���� �������� � ������� ����.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_NAME'] = '����� ���������� ��������';
	$MESS[$strMessPrefix.'FIELD_DELIVERY_OPTIONS_ORDER_BEFORE_DESC'] = '�����, �� �������� ����� ������� �����, ����� �������� ��� � ���� ����.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/delivery-options.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_PICKUP_NAME'] = '����������� ����������';
	$MESS[$strMessPrefix.'FIELD_PICKUP_DESC'] = '����������� ���������� �� ������� ������.<br/><br/>��������� ��������:<br/><b>true</b> � ����� ����� ������� � ������� ������ (������������);<br/><b>false</b> � ����� ������ ������� � ������� ������.<br/><br/>���� ������� �� ������, �� ����������� �������� �� ���������, ��. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_STORE_NAME'] = '����������� ������ ����� ��� ���������������� ������';
	$MESS[$strMessPrefix.'FIELD_STORE_DESC'] = '����������� ������ ����� ��� ���������������� ������.<br/><br/>��������� ��������:<br/><b>true</b> � ����� ����� ������ ��� ���������������� ������.<br/><b>false</b> � ����� ������ ������ ��� ���������������� ������;<br/><br/>���� ������� �� ������, �� ����������� �������� �� ���������, ��. <a href="https://yandex.ru/support/partnermarket/delivery.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = '�������� ������';
	$MESS[$strMessPrefix.'FIELD_DESCRIPTION_DESC'] = '�������� �����������. ����� ������ �� ����� 3000 �������� (������� ����� ����������). � �������� ��������� ���������:<br/><ul><li>������ ���������, ������ ����������� �����, �������� ������, ������ ICQ, ������ ������������, ����� ������.</li><li>����� �������, �����������, ��������, �������� (����� ���������� ���������), ����������, �������, ������������ ����, ��������, �new�, �������, ������, ����.</li><li>������� ������� ������, ��������, ������ �� ������ ��� ���������� (�� ����� ���������� � �������� sales_notes).</li><li>������, � ������� ��������� �����.</li><li>���������� � ������ ������������ ������ (��������, ������ ������ ������ � ������������). ��� ������ ����������� ����� ������� ��������� �����������.</li></ul><br/>� ������� YML ��������� ������������ ��������� xhtml-���� &lt;h3&gt;...&lt;/h3&gt;, &lt;ul&gt;&lt;li&gt;...&lt;/li&gt;&lt;/ul&gt;, &lt;p&gt;...&lt;/p&gt;, &lt;br/&gt; ��� �������, ���:<br/><ul><li>��� ��������� � ���� CDATA � ������� &lt;![CDATA[ ����� � �������������� xhtml-�������� ]]&gt;;</li><li>��������� ����� ������� ��������� XHTML.</li></ul><br/><a href="https://yandex.ru/support/partnermarket/elements/description.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_SALES_NOTES_NAME'] = '������� ������� ������';
	$MESS[$strMessPrefix.'FIELD_SALES_NOTES_DESC'] = '������� ������� ������.<br/><br/>����������� ������� ����������� ��� ������ ������ (��������, ����������� ����� ������, ����������� ���������� ������� ��� ������������� ����������), ���� ��� ���� � ����� ��������.<br/><br/>����� ����� ������� ������ � �������� ������, ������ � �������������� ������� (��������, �������� ������ ��� ���������).<br/><br/>������ � ������� ������ ������ ���� ������� � �����������, �� ����� �� ������ ��������� 50 ��������.';
$MESS[$strMessPrefix.'FIELD_MIN_QUANTITY_NAME'] = '����������� ���-�� ���������� �������';
	$MESS[$strMessPrefix.'FIELD_MIN_QUANTITY_DESC'] = '����������� ���������� ���������� ������� � ����� ������ (��� �������, ����� ������� �������� ������ ����������, � �� ��������). ������� ������������ ������ � ���������� ���������� , ��������� �����, ����������, ������.<br/><br/>���� ������� �� ������, ������������ �������� �� ��������� � 1.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/moq.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_STEP_QUANTITY_NAME'] = '��� ���������� ���-��';
	$MESS[$strMessPrefix.'FIELD_STEP_QUANTITY_DESC'] = '���������� ������, ������� ���������� ����� ��������� � ������������ � ������� ������.�������. ������� ������������ � ���������� � min-quantity � ������ � ���������� ���������� , ��������� �����, ����������, ������.<br/><br/>���� ������� �� ������, ������������ �������� �� ��������� � 1.<br/><br/><a href="https://yandex.ru/support/partnermarket/elements/moq.html" target="_blank">��������� �������� ��������</a>.';
$MESS[$strMessPrefix.'FIELD_MANUFACTURER_WARRANTY_NAME'] = '����������� �������� �������������';
	$MESS[$strMessPrefix.'FIELD_MANUFACTURER_WARRANTY_DESC'] = '����������� �������� �������������.<br/><br/>��������� ��������:<br/><b>true</b> � ����� ����� ����������� �������� �������������;<br/><br/><b>false</b> � ����� �� ����� ����������� �������� �������������.';
$MESS[$strMessPrefix.'FIELD_COUNTRY_OF_ORIGIN_NAME'] = '������ ������������';
	$MESS[$strMessPrefix.'FIELD_COUNTRY_OF_ORIGIN_DESC'] = '������ ������������ ������.<br/><br/>������ �����, ������� ����� ���� ������� � ���� ��������: <a href="http://partner.market.yandex.ru/pages/help/Countries.pdf" target="_blank">http://partner.market.yandex.ru/pages/help/Countries.pdf</a>.';
$MESS[$strMessPrefix.'FIELD_ADULT_NAME'] = '����� ��� ��������';
	$MESS[$strMessPrefix.'FIELD_ADULT_DESC'] = '����� ����� ��������� � �������������� ����������� ������������, ���� ���� ������� ������������� ������� � �����. ��������� �������� � true, false.';
$MESS[$strMessPrefix.'FIELD_BARCODE_NAME'] = '�������� ������';
	$MESS[$strMessPrefix.'FIELD_BARCODE_DESC'] = '�������� ������ �� ������������� � ����� �� ��������: EAN-13, EAN-8, UPC-A, UPC-E.<br/><br/>� YML ������� offer ����� ��������� ��������� ��������� barcode.';
$MESS[$strMessPrefix.'FIELD_EXPIRY_NAME'] = '���� ��������';
	$MESS[$strMessPrefix.'FIELD_EXPIRY_DESC'] = '���� �������� / ���� ������ ���� ���� ��������� ����� �������� / ����� ������.<br/><br/>�������� �������� ������ ���� � ������� ISO8601:<br/><br/>��� ����� �������� / ����� ������ � P1Y2M10DT2H30M. ����������� ������� � 1 ���, 2 ������, 10 ����, 2 ���� � 30 �����.<br/><br/>��� ���� ��������� ����� �������� / ����� ������ � YYYY-MM-DDThh:mm.';
$MESS[$strMessPrefix.'FIELD_WEIGHT_NAME'] = '��� ������';
	$MESS[$strMessPrefix.'FIELD_WEIGHT_DESC'] = '��� ������ � ����������� � ������ ��������.<br/><br/>������: ������������� ����� � ��������� 0.001, ����������� ����� � ������� ����� � �����.<br/><br/>��� �������� ����� ������� �������� �������� ������������� ����������� ��������� ��������:<br/><ul><li>���� ��������� ���� ����� ����������� ������ 5, �� ������ ���� �����������, � ��� ����������� ����������;</li><li>���� ��������� ���� ����� ����������� ������ ��� ����� 5, �� ������ ���� ������������� �� �������, � ��� ����������� ����������.</li></ul>';
$MESS[$strMessPrefix.'FIELD_DIMENSIONS_NAME'] = '�������� ������';
	$MESS[$strMessPrefix.'FIELD_DIMENSIONS_DESC'] = '�������� ������ (�����, ������, ������) � ��������. ������� ������� � �����������.<br/><br/>������: ��� ������������� ����� � ��������� 0.001, ����������� ����� � ������� ����� � �����. ����� ������ ���� ��������� �������� �/� ��� ��������.<br/><br/>��� �������� ����� ������� �������� �������� ������������� ����������� ��������� ��������:<br/><ul><li>���� ��������� ���� ����� ����������� ������ 5, �� ������ ���� �����������, � ��� ����������� ����������;</li><li>���� ��������� ���� ����� ����������� ������ ��� ����� 5, �� ������ ���� ������������� �� �������, � ��� ����������� ����������.</li></ul>';
$MESS[$strMessPrefix.'FIELD_DOWNLOADABLE_NAME'] = '����������� �������';
	$MESS[$strMessPrefix.'FIELD_DOWNLOADABLE_DESC'] = '������� ����� �������. ���� ������� true, ����������� ������������ �� ���� ��������.';
$MESS[$strMessPrefix.'FIELD_AGE_NAME'] = '���������� ��������� ������';
	$MESS[$strMessPrefix.'FIELD_AGE_DESC'] = '���������� ��������� ������.<br/><br/>���� �������� � ������� �������� unit �� ��������� year. ���������� �������� ��������� age ��� unit="year": 0, 6, 12, 16, 18.<br/><br/>';
$MESS[$strMessPrefix.'FIELD_GROUP_ID_NAME'] = '������ ������';
	$MESS[$strMessPrefix.'FIELD_GROUP_ID_DESC'] = '������� ���������� ���� �����������, ������� �������� ���������� ����� ������ � ������ ����� ���������� ��������. �������� ������ ���� ����� ������, �������� 9 ��������.<br/><br/>�������� ��������� �������� offer.<br/><br/>������������ ������ � ���������� ������, ����� � ����������, ������, ���������, ���������� � ����, ������� ������, ���������� ��� ����������� �����������.';
$MESS[$strMessPrefix.'FIELD_PARAM_NAME'] = '���. ���������';
	$MESS[$strMessPrefix.'FIELD_PARAM_DESC'] = '���. ���������';

#
$MESS[$strMessPrefix.'DELETING_EMPTY_TAGS_START'] = '������� [ID = #ELEMENT_ID#]: �������� ������ �����..';
$MESS[$strMessPrefix.'DELETING_EMPTY_TAGS_FINISH'] = '������� [ID = #ELEMENT_ID#]: �������� ������ ���������.';

#
$MESS[$strMessPrefix.'NO_EXPORT_FILE_SPECIFIED'] = '�� ������ ���� � ��������� �����.';
$MESS[$strMessPrefix.'NO_PHP_ZIP_FUNCTIONS'] = '���������� Zip-������� � PHP: ��� ����������, �.�. ��������� ������.������� �������� � Excel-�����, ��� ������ � �������� ���������� Zip-�������.';
$MESS[$strMessPrefix.'WRONG_VALUE_FOR_AGE_YEAR'] = 'Wrong value for tag �age� (unit=�year�): #TEXT#';
$MESS[$strMessPrefix.'WRONG_VALUE_FOR_AGE_MONTH'] = 'Wrong value for tag �age� (unit=�month�): #TEXT#';

#
$MESS[$strMessPrefix.'INVALID_EXCEL_GENERAL'] = '������ ������������ Excel ��� ��������.';
$MESS[$strMessPrefix.'INVALID_EXCEL_CATEGORY'] = '������ ������������ Excel ��� ���������.';
$MESS[$strMessPrefix.'INVALID_EXCEL_CURRENCY'] = '������ ������������ Excel ��� ������.';
$MESS[$strMessPrefix.'INVALID_EXCEL_ITEM'] = '������ ������������ Excel ��� ������ (�������� #IBLOCK_ID#).';
$MESS[$strMessPrefix.'INVALID_EXCEL_OFFER'] = '������ ������������ Excel ��� ����������� (�������� #IBLOCK_ID#).';

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = '������ � Excel-����';
$MESS[$strMessPrefix.'STEP_ZIP'] = '��������� � Zip';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = '���������� ����� �������';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = '����� ���������';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = '��������� �������';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = '����� ���������';
$MESS[$strMessPrefix.'RESULT_FILE_ZIP'] = '������� ZIP-�����';

$MESS[$strMessPrefix.'LARGE_FILE_NOTICE'] = '<b>��������!</b> �� ��������� �������, ��������� � ��������� ������� ������ � Excel, ����������� ������ �������� � ��������� ��������� ������.';

?>