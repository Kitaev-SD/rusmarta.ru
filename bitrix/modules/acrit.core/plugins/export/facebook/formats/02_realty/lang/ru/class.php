<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_REALTY_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB ������������';
$MESS[$strMessPrefix . 'DESCRIPTION'] = 'test';

// Default settings
$MESS[$strMessPrefix . 'SETTINGS_TITLE'] = '��������� ����� (��� title)';
$MESS[$strMessPrefix . 'SETTINGS_TITLE_HINT'] = '������� ����� ��������� �����.';

$MESS[$strMessPrefix . 'SETTINGS_FILE'] = '�������� ����';
$MESS[$strMessPrefix . 'SETTINGS_FILE_PLACEHOLDER'] = '��������, /upload/acrit.exportproplus/facebook_realty.xml';
$MESS[$strMessPrefix . 'SETTINGS_FILE_HINT'] = '������� ����� ����, � ������� ����� ����������� ������� �� ������� �������.<br/><br/><b>������ �������� �����</b>:<br/><code>/upload/xml/facebook_goods.xml</code>';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN'] = '������� ����';
$MESS[$strMessPrefix . 'SETTINGS_FILE_OPEN_TITLE'] = '���� ��������� � ����� �������';

// Headers
// Fields
$MESS[$strName . 'home_listing_id'] = '���������� ������������� ����';
$MESS[$strHint . 'home_listing_id'] = '<b>��������� ��� ������������ ������� � ��������.</b><br>
���������� ������������� ����/������������/�������� � ��������; �������� �������� ���������������� �������������.<br>
������: FB_home_1234
';
$MESS[$strName . 'home_listing_group_id'] = '���������� �������������(� ������ ������)';
$MESS[$strHint . 'home_listing_group_id'] = '<b>�� ��������� � ������������ �����������. �������������� ��� ���������.</b><br>
���������� ������������� ���� ��� ��������. ������ ���� ���������� ��� ������ ������.
';
$MESS[$strName . 'name'] = '��������� ����������';
$MESS[$strHint . 'name'] = '<b>��������� ��� ������������ ������� � ��������.</b><br>
��������� ���������� � ����. <br>������:Modern Eichler in Green Oaks
';
$MESS[$strName . 'availability'] = '������� �����������';
$MESS[$strHint . 'availability'] = '<b>��������� ��� ������������ ������� � ��������.</b><br>
������� ����������� ������������. <br>
�������������� ��������:<br> for_sale, for_rent, sale_pending, recently_sold, off_market, available_soon. <br>��� ��������� ������������ �������������� �������� - for_rent.
';
$MESS[$strName . 'address@format'] = '�������� ���� �� ������!';
$MESS[$strName . 'address.component@name'] = '�������� ���� �� ������!';
$MESS[$strHint . 'address@format'] = '����������� ����, �������� ��� ��� �������������!';
$MESS[$strHint . 'address.component@name'] = '����������� ����, �������� ��� ��� �������������!';
$MESS[$strName . 'address.component'] = '�������� ����� �������������';
$MESS[$strHint . 'address.component'] = '<b>��������� ��� ������������ ������� � �������� .</b><br>
�������� ����� �������������, ������� ������ ���� ���������� � �� ����������������.<br>
���������� �������������� ���� addr1[�����],city[�����],region[������],country[������],postal_code[������] <br>
��. <a  target="_blank" href="https://developers.facebook.com/docs/marketing-api/real-estate-ads/get-started#address-object">��������� ������� ������</a>. <br>
';

$MESS[$strName . 'latitude'] = '������';
$MESS[$strHint . 'latitude'] = '<b>��������� ��� ������������ ������� � �������� .</b><br>
������ ��������.<br>
������: 37.484100
';
$MESS[$strName . 'longitude'] = '�������';
$MESS[$strHint . 'longitude'] = '<b>��������� ��� ������������ ������� � �������� .</b><br>
������� ��������.<br>
������: -122.148252
';
$MESS[$strName . 'neighborhood'] = '������ ������������';
$MESS[$strHint . 'neighborhood'] = '<b>��������� ��� ������������ �������. �������������, �� ������������ ������������� ��� ��������.</b><br>
������������ ����������: 20<br>
������ ������������ ��� �������������. ����� ����� ��������� �������.<br>
������: Menlo Oaks
';
$MESS[$strName . 'price'] = '���� ������� ��� ������';
$MESS[$strHint . 'price'] = '<b>��������� ��� ������������ ������� � �������� .</b><br>
���� ������� ��� ������ ������������. �������������� ���� ��� ���������, �� ������� ������� [����������� ��� ������ ISO] (https://en.wikipedia.org/wiki/ISO_4217?fbclid=IwAR0_xYfUmL3kIUA6sMeEaFAzbJa4MLeMiPDPrftFSX6wkKiTXxPinC-5j70 ", ������ �����.<br>
������: 13,999 USD
';
$MESS[$strName . 'image.url'] = 'URL-����� �����������';
$MESS[$strHint . 'image.url'] = '<b>��������� ��� ������������ ������� � �������� .</b><br>
�������� �����������: 20<br>
������������ ������: 4 ��<br>
URL-����� �����������, ������������� � ����� ����������.<br>
��� ����������� (1: 1) ����������� ������ � ������� ����������� ������� ���� ����������� ������ ���� 600 x 600.<br>
��� ��������� ����������� ���������� ������ ����������� ������ ���� �� ����� 1200x630 ��������.<br>
��� ��������� ������ ���������� ������������ � ������������ ���� ��� ����������� �������.<br>
��. <a href="https://developers.facebook.com/docs/marketing-api/real-estate-ads/get-started#image-object" target="_blank">��������� ������� �����������</a>.
';
$MESS[$strName . 'image.tag'] = '��� �����������';
$MESS[$strHint . 'image.tag'] = '� ����������� �������� ���, ������� ����������, ��� �� ��� ����������. � ������������ ����� ���� ������� ��������� �����.<br>
�������: Fitness Center,Swimming Pool<br>
INSTAGRAM_STANDARD_PREFERRED- ��������� �������������� �������� ������������ ����������� � ����� ���� ��� ����������� �� ���������, ������� ����� �������������� ��� Instagram. ���� ��� ������������ � ��������.
';
$MESS[$strName . 'url'] = '������ �� �������� �� ������� ������������';
$MESS[$strHint . 'url'] = '<b>��������� ��� ������������ ������� � �������� .</b><br>
������ �� �������� �� ������� ������������. ������ ���� ����������� URL.<br>
��. ��������� ������� ����������� .<br>
������: http://www.realestate.com
';
$MESS[$strName . 'description'] = '��������';
$MESS[$strHint . 'description'] = '<b>�������������� ��� ������������ ����������. ��������� ��� ���������.</b><br>
������������ ���������� ����������: 5000<br>
�������� ������������.<br>
������: Beautiful 3BD home available in Belmont
';
$MESS[$strName . 'num_beds'] = '����� ���������� ������';
$MESS[$strHint . 'num_beds'] = '<b>�������������� ��� ������������ ����������. ��������� ��� ���������.</b><br>
����� ���������� ������. ����� 0 ��� ������.<br>
������: 2
';
$MESS[$strName . 'num_baths'] = '����� ���������� ��������';
$MESS[$strHint . 'num_baths'] = '<b>�������������� ��� ������������ ����������.</b><br>
����� ���������� ��������. <b>��� ��������� ������ ���� 1�������.</b>
';
$MESS[$strName . 'num_rooms'] = '����� ���������� ������ ';
$MESS[$strHint . 'num_rooms'] = '<b>����������� ��� ������������ ����������. ��������� ��� ���������.</b><br>
����� ���������� ������ � �������������.
';
$MESS[$strName . 'property_type'] = '��� ������������';
$MESS[$strHint . 'property_type'] = '<b>�������������� ��� ������������ ����������.</b><br>
��� ������������. <br>
�������������� �������� ��� ������������ ����������: <br>apartment, condo, house, land, manufactured, other, townhouse. <br><br>
�������������� �������� ��� ��������: <br>apartment, builder_floor, condo, house, house_in_condominium, house_in_villa, loft, penthouse, studio, townhouse, other.
';
$MESS[$strName . 'listing_type'] = '��� ������� ������������';
$MESS[$strHint . 'listing_type'] = '<b>�������������� ��� ������������ ����������.</b><br>
��� ������� ������������. <br>
�������������� �������� ��� ������������ ����������: <br>
for_rent_by_agent, for_rent_by_owner, for_sale_by_agent, for_sale_by_owner, foreclosed, new_construction, new_listing. <br><br>
�������������� �������� ��� ��������: <br>for_rent_by_agent, for_rent_by_owner.
';
$MESS[$strName . 'area_size'] = '������� ��� ������������';
$MESS[$strHint . 'area_size'] = '<b>����������� ��� ������������ ����������. ��������� ��� ���������.</b><br>
������� ��� ������������ �������� ���������� �����.
';
$MESS[$strName . 'area_unit'] = '������� (���������� ���� ��� ���������� �����)';
$MESS[$strHint . 'area_unit'] = '����������� ��� ������������ ����������. ��������� ��� ���������.<br>
������� (���������� ���� ��� ���������� �����) �������� ������� ����. <br>
�������������� ��������: sq_ft, sq_m.
';
$MESS[$strName . 'ac_type'] = '��� ������������';
$MESS[$strHint . 'ac_type'] = '<b>����������� ��� ������������ ����������. �������������� ��� ���������.</b><br>
��� ������������. <br>�������������� ��������:<br> central, other, none.
';
$MESS[$strName . 'furnish_type'] = '��� ��������� � ������� ������';
$MESS[$strHint . 'furnish_type'] = '<b>����������� ��� ������������ ����������. �������������� ��� ���������.</b><br>
��� ��������� � ������� ������.<br>
�������������� ��������: <br>
furnished, semi-furnished, unfurnished.
';
$MESS[$strName . 'heating_type'] = '��� ���������';
$MESS[$strHint . 'heating_type'] = '<b>����������� ��� ������������ ����������. �������������� ��� ���������.</b><br>
��� ���������, �������������� � �������������. <br>
�������������� ��������: <br>
central, gas, electric, radiator, other, none.
';
$MESS[$strName . 'laundry_type'] = '��� ����� � �������';
$MESS[$strHint . 'laundry_type'] = '<b>����������� ��� ������������ ����������. �������������� ��� ���������.</b><br>
��� ����� � �������. <br>
�������������� ��������:<br>
in_unit, in_building, other, none.
';
$MESS[$strName . 'num_units'] = '����� ���������� ������';
$MESS[$strHint . 'num_units'] = '<b>�������������� ��� ������������ ������� � �������� .</b><br>
����� ���������� ������ (�������, �������������), ��������� ��� ������.<br>
������: 0
';
$MESS[$strName . 'parking_type'] = '��� ��������';
$MESS[$strHint . 'parking_type'] = '<b>����������� ��� ������������ ����������. �������������� ��� ���������.</b><br>
��� �������� �� �������������. <br>
�������������� ��������: <br>
garage, street, off-street, other, none.
';
$MESS[$strName . 'partner_verification'] = '����������� �� ��������-������� �������';
$MESS[$strHint . 'partner_verification'] = '<b>����������� ��� ������������ ����������. �������������� ��� ���������.</b><br>
����������� �� ��������-������� �������.<br> �������������� ��������:<br> verified, none.
';
$MESS[$strName . 'year_built'] = '��� ��������� ������������ � ������� ����';
$MESS[$strHint . 'year_built'] = '��� ��������� ������������ � ������� ���� , ��� �� 4 ����.<br>
������: 1994.
';
$MESS[$strName . 'pet_policy'] = '���������� ��� ���������� ��������';
$MESS[$strHint . 'pet_policy'] = '<b>����������� ��� ������������ ����������. �������������� ��� ���������.</b><br>
���������� ��������� �� ���������:<br> cat, dog, all, none.
';
$MESS[$strHead . 'HEADER_AVAILABLE_DATES_PRICE_CONFIG'] = '������ ��� � ���, ����� ������� ��������';
$MESS[$strHint . 'HEADER_AVAILABLE_DATES_PRICE_CONFIG'] = '������ ��� � ���, ����� ������� ��������. ����� �� ���������� ��������, Facebook ����� ������������� ������ �� ������ �� ��������� ��� � ����������� ���������� ��������������� ���� � ����� ����������.<br>
��. <a href="https://developers.facebook.com/docs/marketing-api/real-estate-ads/get-started#available_dates-object" target="_blank">��������� ������� "��������� ����"</a>
';
$MESS[$strName . 'available_dates_price_config.start_date'] = '������ ���������� ��������� ���';
$MESS[$strHint . 'available_dates_price_config.start_date'] = '�������������.<br>
������ ���������� ��������� ��� � ������� ISO-8601; ������� ���� ������. ���� �� ������ �������������� start_date, �� end_date��������� ������������ ��� � ���� ����.<br>
������:, YYYY-MM-DD �������� 2018-01-01.
';
$MESS[$strName . 'available_dates_price_config.end_date'] = '����� ���������� ��������� ���';
$MESS[$strHint . 'available_dates_price_config.end_date'] = '�������������.<br>
����� ���������� ��������� ��� � ������� ISO-8601; ��������� ���� ���������. ���� �� ������ �������������� end_date, �� start_date ��������� ������������ ������� ����.<br>
������:, YYYY-MM-DD �������� 2018-02-01.';
$MESS[$strName . 'available_dates_price_config.rate'] = '����';
$MESS[$strHint . 'available_dates_price_config.rate'] = '������������� ���� �������� � ���� ��������� ���������.<br>
������: 10000 ���� ���������� ���� $100.00 USD';
$MESS[$strName . 'available_dates_price_config.currency'] = '��� ������';
$MESS[$strHint . 'available_dates_price_config.currency'] = '�����������, ���� �� ������� ���� [rate]. ��� ������ <a href="https://www.iso.org/iso-4217-currency-codes.html?fbclid=IwAR1Wm77c8rk0H-cckzb52g7L1gCLipFiIUpDw28MOQQdcTISleuthnAdyn0" target="_blank">ISO-4217</a>.<br>
������: USD, GBP � �.�.';
$MESS[$strName . 'available_dates_price_config.interval'] = '���� ���������� �� ���������� ������.';
$MESS[$strHint . 'available_dates_price_config.interval'] = '���� ���������� �� ���������� ������.<br>
���������� ��������: nightly, weekly, monthly, sale.';
$MESS[$strName . 'applink'] = '������ �� ����������';
$MESS[$strHint . 'applink'] = '������ �� ����������';





# Steps
$MESS[$strMessPrefix . 'STEP_EXPORT'] = '������ � XML-����';

# Display results
$MESS[$strMessPrefix . 'RESULT_GENERATED'] = '���������� ����� �������';
$MESS[$strMessPrefix . 'RESULT_EXPORTED'] = '����� ���������';
$MESS[$strMessPrefix . 'RESULT_ELAPSED_TIME'] = '��������� �������';
$MESS[$strMessPrefix . 'RESULT_DATETIME'] = '����� ���������';

#
$MESS[$strMessPrefix . 'NO_EXPORT_FILE_SPECIFIED'] = '�� ������ ���� � ��������� �����.';
