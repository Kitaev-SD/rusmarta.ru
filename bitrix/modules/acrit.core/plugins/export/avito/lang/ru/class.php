<?

$strMessPrefix = 'ACRIT_EXP_AVITO_';

// General
$MESS[$strMessPrefix.'NAME'] = '�����';

// Documentation
$MESS[$strMessPrefix.'PARAGRAPH_ABOUT_REQUIRED_PARAMS'] = '<b>��������!</b><br/>
��������� ��������� �������� <b>���������������</b> � ����� ������� � <b>�������������</b> � ������,<br/>
������� � ����� �������� ������������� �������� ������ ���������.<br/>
��� ������������� ����������� ����������� ������� ��������� ���������� �� ������� ��������<br/>
(� ���������� � � ������������).';
$MESS[$strMessPrefix.'IMAGES_MAX_COUNT'] = '������������ ���������� ����������� ��� ��������� �#NAME#�: <b>#COUNT#</b>.';
$MESS[$strMessPrefix.'USEFUL_LINKS'] = '�������� ������:';
$MESS[$strMessPrefix.'DOCUMENTATION'] = '������������';
$MESS[$strMessPrefix.'CHECK_XML'] = '��������� XML';
$MESS[$strMessPrefix.'FAQ'] = '������� � ������';

// Default settings
$MESS[$strMessPrefix.'SETTINGS_FILE'] = '�������� ����';
$MESS[$strMessPrefix.'SETTINGS_FILE_PLACEHOLDER'] = '��������, /upload/xml/avito.xml';
$MESS[$strMessPrefix.'SETTINGS_FILE_HINT'] = '������� ����� ����, � ������� ����� ����������� ������� �� ������� �������.<br/><br/><b>������ �������� �����</b>:<br/><code>/upload/xml/avito.xml</code>';
$MESS[$strMessPrefix.'SETTINGS_ENCODING'] = '��������� �����';
$MESS[$strMessPrefix.'SETTINGS_ENCODING_HINT'] = '�������� ��������� �����. �������������� ������� ����� ����������� ���.';
$MESS[$strMessPrefix.'SETTINGS_ZIP'] = '��������� � Zip';
$MESS[$strMessPrefix.'SETTINGS_ZIP_HINT'] = '������ �������� ��������� ���������� �������������� ���� � Zip. ��������� �������� � Zip-�����, ������ �����, ����������� � ������.������, ����������� �����������, ��� �������� ��� ���������� ��������.';

// Headers
$MESS[$strMessPrefix.'HEADER_GENERAL'] = '����� ������';
$MESS[$strMessPrefix.'HEADER_LOCATION'] = '������ � ��������������';
$MESS[$strMessPrefix.'HEADER_CHARACTERISTICS'] = '��������������';

// Fields
$MESS[$strMessPrefix.'FIELD_ID_NAME'] = '������������� ����������';
$MESS[$strMessPrefix.'FIELD_ID_DESC'] = '���������� ������������� ���������� � ����� ���� ������ � ������ �� 100 ��������.<br/><br/>
� ������ � ���� �� ���������� ������ ����������� ���� � ��� �� ������������� �� ����� � �����. ������������ ����� ������� �������� � ���������� ������������� ���������� ������ �����.<br/><br/>
��� ���������� ������ ���������� ���������� ������������ ����� �������������.';
$MESS[$strMessPrefix.'FIELD_DATE_BEGIN_NAME'] = '���� � ����� ������ ���������� ����������';
$MESS[$strMessPrefix.'FIELD_DATE_BEGIN_DESC'] = '���� � ����� ������ ���������� ���������� � ����� ������ ����� �� ���� �������� �������� ��������� <a href="http://ru.wikipedia.org/wiki/ISO_8601" target="_blank">ISO 8601</a>:<br/><br/>
	������ ���� � ������� "YYYY-MM-DD" (MSK);<br/>
	��� ���� � ����� � ������� "YYYY-MM-DDTHH:mm:ss+hh:mm".<br/>
	�����: ���������� ����� ������������ � ��������� ���� (���� ������� ������ ����) ��� ����� ���������� ������� (���� ������ ���� � �����) � ������� ���� (���� ��� �������������� ����� �������� ������ ����� ��������).<br/><br/>
	���� ������� �� �����, ���������� ����� ������������ ����� �� ����� ������� ��������� XML-����� � ���.';
$MESS[$strMessPrefix.'FIELD_DATE_END_NAME'] = '���� � �����, �� ������� ���������� ���������';
$MESS[$strMessPrefix.'FIELD_DATE_END_DESC'] = '���� � �����, �� ������� ���������� ��������� � ����� ������ ����� �� ���� �������� �������� ��������� <a href="http://ru.wikipedia.org/wiki/ISO_8601" target="_blank">ISO 8601</a>:<br/><br/>
	������ ���� � ������� "YYYY-MM-DD" (MSK);<br/>
	��� ���� � ����� � ������� "YYYY-MM-DDTHH:mm:ss+hh:mm".<br/>
	���� �������� � �������, �� ����� ���������� �� ����� ������������, � ������������ � ����� ����� � ����������.<br/><br/>
	�����: � ������ ������������ �� ��������� �������� �������� DateEnd ����������� ��������� ��� � ���, � ��� �������� �� ���������� � �� ����� ������ ��������.<br/><br/>
	���� ��������� ���������� ���������� �� ����� ����� �������� ����������� (����� ��������� ����� ������� � ������ �������� ��� ������� ������������). ���� ����� ��������� ���������� ���������� �� ��� ������������ � XML-����� � DateEnd �� ������ ��� ������� �������� � �������, �� ���������� ����� ����� ������������. ���� � �������� DateEnd ������� ������ ���� (��� �������) � ��� ��������� � ����� ��������� ���������� ����������, �� ����������� ���������� �� ����������.';
$MESS[$strMessPrefix.'FIELD_LISTING_FEE_NAME'] = '������� �������� ����������';
$MESS[$strMessPrefix.'FIELD_LISTING_FEE_DESC'] = '������� <a href="https://support.avito.ru/hc/ru/articles/203867766" target="_blank">�������� ����������</a> � ���� �� �������� ������:<br/><br/>
<ul>
	<li>�Package�</li> � ���������� ���������� �������������� ������ ��� ������� ����������� ������ ����������;<br/>
	<li>�PackageSingle�</li> � ��� ������� ����������� ������ ������ ���������� ���������� ���������� � ����; ���� ��� ����������� ������, �� ���������� ����� �� �������� �����, �� ���������� ������� ����������;<br/>
	<li>�Single�</li> � ������ ������� ����������, ���������� ��� ������� ����������� ����� �� �������� �����; ���� ���� ���������� ����� ����������, �� ����� ��������������.<br/>���� ������� ���� ��� �����������, �� �������� �� ��������� � �Package�.
</ul>
';
$MESS[$strMessPrefix.'FIELD_AD_STATUS_NAME'] = '������� ������';
$MESS[$strMessPrefix.'FIELD_AD_STATUS_DESC'] = '<a href="https://support.avito.ru/hc/ru/sections/200009758" target="_blank">������� ������</a>, ������� ����� ��������� � ���������� � ���� �� �������� ������:
<ul>
	<li>�Free� � ������� ����������;</li>
	<li>�Premium� � <a href="https://support.avito.ru/hc/ru/articles/200026868" target="_blank">�������-����������</a>;</li>
	<li>�VIP� � <a href="https://support.avito.ru/hc/ru/articles/200026848" target="_blank">VIP-����������</a>;</li>
	<li>�PushUp� � <a href="https://support.avito.ru/hc/ru/articles/200026828" target="_blank">�������� ���������� � ������</a>;</li>
	<li>�Highlight� � <a href="https://support.avito.ru/hc/ru/articles/200026858" target="_blank">��������� ����������</a>;</li>
	<li>�TurboSale�� ���������� ������ �<a href="https://support.avito.ru/hc/ru/articles/200026838" target="_blank">�����-�������</a>�;</li>
	<li>�QuickSale� � ���������� ������ �<a href="https://support.avito.ru/hc/ru/articles/200026838" target="_blank">������� �������</a>�.</li>
</ul>
���� ������� ���� ��� �����������, �� ������ ���������� �� ��������� � �Free�.<br/><br/>
��� ��������� ���������� ������� ������ ���������� ������� ����� �� <a href="https://www.avito.ru/account" target="_blank">�������� �����</a>. ���� ����� �� �������� ������������ ��� ���������� ������, ���������� ����������� ��� ������� (Free).<br/><br/>
���� ������� ������ ����������� � ������ ���������� �� ����, ��� ���� ��� � ������������ ������ �������:<br/>
<ul>
	<li>��� ����� �Premium�, �VIP�, �Highlight� � ��� � 7 ����,</li>
	<li>��� �PushUp� � ��� � 2 ���,</li>
	<li>������ ����� �QuickSale�, �TurboSale� � ��� � 7 ����.</li>
</ul>
���� �� ��������� ���������� ������� ������ ���������� � XML ��� ��� ���������� �� �Free�, �� ������ ����� ��������� ��������.
������ ������ ��� ������ ���������� ������������ ���������� ���� �� �����: ���� ������ ����� ���� ������������, ���� ��� �� ���������� ���� �������� ������. �� ���� ���� ������������ ����� ��������� ������ ���� ������.';
$MESS[$strMessPrefix.'FIELD_AVITO_ID_NAME'] = '����� ���������� �� �����';
$MESS[$strMessPrefix.'FIELD_AVITO_ID_DESC'] = '����� ���������� �� ����� � ����� �����.<br/><br/>
���� �� ��������� ���������� �������, � ������ ������ ��������� ��� � ������� ������������, �� �������� 2 �������� ��������. ������� 1 � ��������������� ���������� �� ��������� ������� �������������� ������ ���������� (��������� �������� � ������� �<a href="http://autoload.avito.ru/format/faq/" target="_blank">������� � ������</a>�). � ���������, � ���� �������� ��������� ������������ ������� ������.<br/><br/>
������ ������� � ����� �������� ������ �������������� ������, ����� ������� � XML-����� � ��������� AvitoId ������ ����� ����������� ����������. ��� ���������� ������ � ����� �������, ���������� �������� ��������� �������� ������� � ����������� ���������� �� ����� � ��������� ������ ����������.<br/><br/>
�����: ���� ���� ��������� � ����������� ���������� �������� � XML, ����� ������� ������ �� ����� ���������� ������ ��������� Description � ��������� �������: �AvitoId: XXX� (��� �XXX� � ����� ����������). ��� ���������� � ����������� �� ����� ������������ �� �����.';
$MESS[$strMessPrefix.'FIELD_ALLOW_EMAIL_NAME'] = '����������� �������� ��������� �� ���������� ����� ����';
$MESS[$strMessPrefix.'FIELD_ALLOW_EMAIL_DESC'] = '����������� �������� ��������� �� ���������� ����� ���� � ���� �� �������� ������:<br/>
<ul>
	<li>���,</li>
	<li>����.</li>
</ul>
����������: �������� �� ��������� � ���.';
$MESS[$strMessPrefix.'FIELD_ALLOW_EMAIL_DEFAULT'] = '��';
$MESS[$strMessPrefix.'FIELD_MANAGER_NAME_NAME'] = '��� ���������, ����������� ����';
$MESS[$strMessPrefix.'FIELD_MANAGER_NAME_DESC'] = '��� ���������, ����������� ���� �������� �� ������� ���������� � ������ �� ����� 40 ��������.';
$MESS[$strMessPrefix.'FIELD_CONTACT_PHONE_NAME'] = '���������� �������';
$MESS[$strMessPrefix.'FIELD_CONTACT_PHONE_DESC'] = '���������� ������� � ������, ���������� ������ ���� ���������� ����� ��������; ������ ���� ����������� ������ ��� ������ ��� ���������� ���������. ���������� �������:<br/>
<ul>
	<li>+7 (495) 777-10-66,</li>
	<li>(81374) 4-55-75,</li>
	<li>8 905 207 04 90,</li>
	<li>+7 905 2070490,</li>
	<li>88123855085,</li>
	<li>9052070490.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LATITUDE_NAME'] = '������';
$MESS[$strMessPrefix.'FIELD_LATITUDE_DESC'] = '�������������� ������. ��������� � Longitude �������� ������������� �������� Address.';
$MESS[$strMessPrefix.'FIELD_LONGITUDE_NAME'] = '�������';
$MESS[$strMessPrefix.'FIELD_LONGITUDE_DESC'] = '�������������� �������. ��������� � Latitude �������� ������������� �������� Address.';
$MESS[$strMessPrefix.'FIELD_ADDRESS_NAME'] = '������ ����� �������';
$MESS[$strMessPrefix.'FIELD_ADDRESS_DESC'] = '������ ����� ������� � ������ �� 256 ��������.<br/><br/>
�������� ������������� ��������� "Region", "City", "Subway", "District", "Street" � ��� ���������� "Address", �������� ������������� ��������� ��������� �� �����, ��� ����� ���������������.';
$MESS[$strMessPrefix.'FIELD_REGION_NAME'] = '������';
$MESS[$strMessPrefix.'FIELD_REGION_DESC'] = '������, � ������� ��������� ������ ���������� � � ������������ �� ���������� �� <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">�����������</a>.<br/><br/>
����������: ������� �������� ����������, ������������� ������������ ������� "Address".';
$MESS[$strMessPrefix.'FIELD_CITY_NAME'] = '����� ��� ���������� �����';
$MESS[$strMessPrefix.'FIELD_CITY_DESC'] = '����� ��� ���������� �����, � ������� ��������� ������ ���������� � � ������������ �� ���������� �� <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">�����������</a>.<br/><br/>
������� ���������� ��� ���� ��������, ����� ������ � �����-����������.<br/><br/>
���������� �������� ��������. ���� ��������� �������� � ��� �����������, �� ������� ��������� � ������ ������� ����� �� �����������, � ������ �������� ����������� ������ � � �������� Street.<br/><br/>
����������: ������� �������� ����������, ������������� ������������ ������� "Address".';
$MESS[$strMessPrefix.'FIELD_SUBWAY_NAME'] = '��������� ������� �����';
$MESS[$strMessPrefix.'FIELD_SUBWAY_DESC'] = '
��������� ������� ����� � � ������������ �� ���������� �� <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">�����������</a>.<br/><br/>
����������: ������� �������� ����������, ������������� ������������ ������� "Address".';
$MESS[$strMessPrefix.'FIELD_DISTRICT_NAME'] = '����� ������';
$MESS[$strMessPrefix.'FIELD_DISTRICT_DESC'] = '����� ������ � � ������������ �� ���������� �� <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">�����������</a>.<br/><br/>
����������: ������� �������� ����������, ������������� ������������ ������� "Address".';
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = '���������';
$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = '���������';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_NAME'] = '�������� ����������';
$MESS[$strMessPrefix.'FIELD_DESCRIPTION_DESC'] = '��������� �������� ���������� � ������������ � <a href="https://support.avito.ru/hc/ru/articles/200026968" target="_blank">��������� �����</a> � ������ �� ����� 3000 ��������.
���� � ��� ���� ���������� <a href="https://support.avito.ru/hc/ru/articles/226597708" target="_blank">��������</a>, �� �������� �������� ������ <a href="https://ru.wikipedia.org/wiki/CDATA#CDATA_.D0.B2_XML" target="_blank">CDATA</a>, �� ������ ������������ �������������� �������������� � ������� HTML-����� � ������ �� ���������� ������: p, br, strong, em, ul, ol, li.';
$MESS[$strMessPrefix.'FIELD_IMAGES_NAME'] = '����������';
$MESS[$strMessPrefix.'FIELD_IMAGES_DESC'] = '���������� � ��������� ��������, �� ������ �������� �Image� �� ������ �����������.<br/><br/>
���������� ����������� ������� ����������: JPEG (*.jpg), PNG (*.png).<br/><br/>
��� ������ ��������� ���������� ������������ ���������� ����������, ������� ����� ���������� � ���������� (��� ���������� ����� ����� ���������� ������������).';

$MESS[$strMessPrefix.'FIELD_IMAGE_URLS_NAME'] = '���������� � ���� HTTP-������';
$MESS[$strMessPrefix.'FIELD_IMAGE_URLS_DESC'] = '���������� ����������� ������� ����������: JPEG, PNG. ����������� ���������� ������ ����� ����������� � 30 ��.<br/><br/>
��� ������ ��������� ���������� ������������ ���������� ����������, ������� ����� ���������� � ���������� (��� ���������� ����� ����� ���������� ������������).<br/><br/>
������: <code>http://img.test.ru/8F7B-4A4F3A0F2BA1.jpg | http://img.test.ru/8F7B-4A4F3A0F2XA3.jpg</code>';

$MESS[$strMessPrefix.'FIELD_IMAGE_NAMES_NAME'] = '���������� � ���� �������� ������.';
$MESS[$strMessPrefix.'FIELD_IMAGE_NAMES_DESC'] = '������� ������������ ��� �������� ����� c ������������ � ������ � ������������ ������� ����� ������ �������.<br/>
���������� ����������� ������� ����������: JPEG, PNG. ����������� ���������� ������ ����� ����������� � 30 ��.<br/>
��� ������ ��������� ���������� ������������ ���������� ����������, ������� ����� ���������� � ���������� (��� ���������� ����� ����� ���������� ������������)<br/>
������: <code>a1.jpg | a2.jpg | a3.jpg</code>';

$MESS[$strMessPrefix.'FIELD_VIDEO_URL_NAME'] = '����� c YouTube';
$MESS[$strMessPrefix.'FIELD_VIDEO_URL_DESC'] = '����� c YouTube � ������. ��������:<br/>http://www.youtube.com/watch?v=YKmDXNrDdBI';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = '�������� ����������';
$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = '�������� ���������� � ������ �� 50 ��������.<br/>
����������: �� ������ � �������� ���� � ���������� ���������� � ��� ����� ���� ��������� ���� � � �� ����������� ����� �������.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = '���� � ������';
$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = '���� � ������ � ����� �����.';
$MESS[$strMessPrefix.'FIELD_CONDITION_NAME'] = '��������� ����';
$MESS[$strMessPrefix.'FIELD_CONDITION_DESC'] = '��������� ���� � ���� �� �������� ������:
<ul>
	<li>�����</li>
	<li>�/�</li>
</ul>';

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = '������ � XML-����';

# Display results
$MESS[$strMessPrefix.'RESULT_GENERATED'] = '���������� ����� �������';
$MESS[$strMessPrefix.'RESULT_EXPORTED'] = '����� ���������';
$MESS[$strMessPrefix.'RESULT_ELAPSED_TIME'] = '��������� �������';
$MESS[$strMessPrefix.'RESULT_DATETIME'] = '����� ���������';
$MESS[$strMessPrefix.'RESULT_STEP'] = '��������� �����';

#
$MESS[$strMessPrefix.'NO_EXPORT_FILE_SPECIFIED'] = '�� ������ ���� � ��������� �����.';
?>