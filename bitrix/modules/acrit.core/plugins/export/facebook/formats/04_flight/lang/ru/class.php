<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_FLIGHT_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB ���������';

// Default settings
$MESS[$strMessPrefix . 'SETTINGS_TITLE'] = '��������� ����� (��� title)';
$MESS[$strMessPrefix . 'SETTINGS_TITLE_HINT'] = '������� ����� ��������� �����.';

$MESS[$strMessPrefix . 'SETTINGS_FILE'] = '�������� ����';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = '��������, /upload/acrit.exportproplus/facebook_flight.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = '������� ����� ����, � ������� ����� ����������� ������� �� ������� �������.<br/><br/><b>������ �������� �����</b>:<br/><code>/upload/xml/facebook_flight.xml</code>';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN'] = '������� ����';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN_TITLE'] = '���� ��������� � ����� �������';

// Headers
// Fields
$MESS[$strName . 'origin_airport'] = '��� IATA ��������� �����������';
$MESS[$strHint . 'origin_airport'] = '<b>������������ ����.</b><br>
��� IATA ��������� �����������. �������������� ���� IATA ��� ���������� � �������. ��� �������� ����� ����������� �������� ������ ����� IATA. �����. ����� �������� ������ �������, �� ����������� ������� � ���� ���������� ��������������.<br>
������: SFO.
';
$MESS[$strName . 'destination_airport'] = '��� IATA ��������� ��������';
$MESS[$strHint . 'destination_airport'] = '<b>������������ ����.</b><br>
��� IATA ��������� ��������. �������������� ���� IATA ��� ���������� � �������. ��� �������� ����� ����������� �������� ������ ����� IATA. �����. ����� �������� ������ �������, �� ����������� ������� � ���� ���������� ��������������.<br>
Example: JFK.
';
$MESS[$strName . 'image.url'] = 'URL-����� �����������';
$MESS[$strHint . 'image.url'] = '<b>������������ ����.</b><br>
������������ ���������� ���������: 20.<br>
������ �� ������������ ��� ����� ���������. ��� ��������� ����� ������������ �� 20 �����������. ������ ����������� �������� ��� ����: url � tag. � ����� ������������ ����� ���� ������� ��������� �����. ���������� ������������ ���� �� ���� ������� image (�����������). ������ ������� ����������� �� ������ ��������� 4 ��.
��. <a href="https://developers.facebook.com/docs/marketing-api/flight-ads/catalog#image-object" target="_blank">��. ��������� �������� image.</a>.
';
$MESS[$strName . 'image.tag'] = '��� �����������';
$MESS[$strHint . 'image.tag'] = '� ����������� �������� ���, ������� ����������, ��� �� ��� ����������. � ������������ ����� ���� ������� ��������� �����.<br>
�������: Fitness Center,Swimming Pool<br>
INSTAGRAM_STANDARD_PREFERRED- ��������� �������������� �������� ������������ ����������� � ����� ���� ��� ����������� �� ���������, ������� ����� �������������� ��� Instagram. ���� ��� ������������ � ��������.
';

$MESS[$strName . 'description'] = '������� �������� ��������.';
$MESS[$strHint . 'description'] = '<b>������������ ����.</b><br>
������������ ������: 5000.<br>
������� �������� ��������.
';
$MESS[$strName . 'url'] = '������ �� ������� ����, �� ������� ����� �������� ���������� �� ���������';
$MESS[$strHint . 'url'] = '<b>�����������, ������ ���� �� ������� �������� ������</b>
�����������, ������ ���� �� ������� �������� ������ <a href="https://developers.facebook.com/docs/marketing-api/dynamic-ads-for-travel/ads-management" target="_blank">�� ������ ����������</a>. ����� ������������ ���� <b>Deep Link</b> � <a href="https://business.facebook.com/adsmanager/manage" target="_blank">Ads Manager</a> ��� <b>template_url_spec</b> � API.<br>
������ �� ������� ����, �� ������� ����� �������� ���������� �� ���������. ��������� ����� �������� ������ �� ������ ����������.
';

$MESS[$strName . 'origin_city'] = '�������� ������ ������.';
$MESS[$strHint . 'origin_city'] = '�������� ������ ������.
������: San Francisco.
';
$MESS[$strName . 'destination_city'] = '�������� ������ ��������.';
$MESS[$strHint . 'destination_city'] = '�������� ������ ��������.
������: New York.
';
$MESS[$strName . 'price'] = '���� ������. �������� ������ ����������� ������ � �������.';
$MESS[$strHint . 'price'] = '���� ������. �������� ������ ����������� ������ � �������.<br>
������: 99.99 USD.
';
$MESS[$strName . 'one_way_price'] = '���� ������ � ���� �������. �������� ������ ����������� ������ � �������.';
$MESS[$strHint . 'one_way_price'] = '���� ������ � ���� �������. �������� ������ ����������� ������ � �������.<br>
������: 99.99 USD.
';
$MESS[$strName . 'priority'] = '��������� ���������. ';
$MESS[$strHint . 'priority'] = '��������� ���������. ��������� ��������: �� 0 (����� ������) �� 5 (����� �������). ���� ��� �������� �� �������, �������� ����� ��������� 0.<br>
������: 5.
';

$MESS[$strName . 'applink'] = '�������� �������� ������ �� �������� �������� � ��������� � ��������� ����������,';
$MESS[$strHint . 'applink'] = '<b>�������������� ����.</b><br>
�������� �������� ������ �� �������� �������� � ��������� � ��������� ����������, ��������� <a href="https://developers.facebook.com/docs/applinks" target="_blank">App Links</a>. �� ������ ������� �������� ������ (� ������� �������� ����������):<br>
�� ������ ������� ��� ������ template_url_spec.<br>
� �����, ��������� ������ Applink.<br>
����� ���������� �� ���� ���� ��������� App Links.<br>
��������� � <a href="https://developers.facebook.com/docs/marketing-api/catalog/guides/product-deep-links" target="_blank">�������� ������� �� ������</a> <br>
';


# Steps
$MESS[$strMessPrefix . 'STEP_EXPORT'] = '������ � XML-����';

# Display results
$MESS[$strMessPrefix . 'RESULT_GENERATED'] = '���������� ����� �������';
$MESS[$strMessPrefix . 'RESULT_EXPORTED'] = '����� ���������';
$MESS[$strMessPrefix . 'RESULT_ELAPSED_TIME'] = '��������� �������';
$MESS[$strMessPrefix . 'RESULT_DATETIME'] = '����� ���������';

#
$MESS[$strMessPrefix . 'NO_EXPORT_FILE_SPECIFIED'] = '�� ������ ���� � ��������� �����.';
