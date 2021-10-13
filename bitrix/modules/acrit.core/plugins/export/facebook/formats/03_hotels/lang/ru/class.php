<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_HOTELS_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB ���������';

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
$MESS[$strName . 'hotel_id'] = '���������� ������������� ��������� ';
$MESS[$strHint . 'hotel_id'] = '<b>������������ ����.</b><br>
������������ �����: 100.<br>
���������� ������������� ��������� � ��������. �� ����� �������������� � ���������������� content_ids � �������� ���������� � �������, ��������� � hotel. �����. ����� �������� ������ �������, ����������� ������ �� ����� ����������� �������������� �������. ID �� ������ �����������.<br>
������: FB_hotel_1234.
';
$MESS[$strName . 'room_id'] = '���������� ������������� ��� ������������ ������';
$MESS[$strHint . 'room_id'] = '<b>����������� ��� ���������� ���������� � ����������� �������.</b><br>
������� ���������� ID ��� ���� ������������ ������. ������������ �����: 100. <br>
������: FB_hotel_room_1234.
';
$MESS[$strName . 'name'] = '�������� ��������������� �������� ���������.';
$MESS[$strHint . 'name'] = '<b>������������ ����.</b><br> <br>������: Facebook Hotel.
';
$MESS[$strName . 'description'] = '������� �������� ���������.';
$MESS[$strHint . 'description'] = '<b>������������ ����.</b><br>
������� �������� ���������.<br>
������������ ������: 5 000.<br>
������: Only 30 minutes away from San Francisco.
';
$MESS[$strName . 'checkin_date'] = '������� �����������';
$MESS[$strHint . 'checkin_date'] = '<b>����������� ��� ���������� ���������� � ����������� �������.</b><br>
���� ������ � ���������. ����� �������� �� 180 ���� ������� � ���� �������� ����. ������������ �������� <a href="https://l.facebook.com/l.php?u=https%3A%2F%2Fen.wikipedia.org%2Fwiki%2FISO_8601%22%3Ehttps%3A%2F%2Fen.wikipedia.org%2Fwiki%2FISO_8601&h=AT2O_R46_3oqAkRyBV_hm276JO2m7j4aL3DmuDBcflghefvW0lj-_fAfM9jIDDW7VE8RUSHx0e_kefpu4RT06o6WYwKc4o6jD7hZlyCZgDhcbH1d6dfMGvvH9Spu9qxSREH_4i6n1ydyeFxEoQ" target="_blank">ISO-8601</a> (YYYY-MM-DD).<br>
������: 8/1/17.
';
$MESS[$strName . 'length_of_stay'] = '���������� ����� ���������� � ���������.';
$MESS[$strHint . 'length_of_stay'] = '<b>����������� ��� ���������� ���������� � ����������� �������.</b><br>
���������� ����� ���������� � ���������.<br>
������: 7.
';
$MESS[$strName . 'base_price'] = '������� ���� �� ���� � ���������.';
$MESS[$strHint . 'base_price'] = '<b>����������� ��� ���������� ���������� � ����������� �������.</b><br>
������� ���� �� ���� � ���������. �������� ���������� ��������� ������ � ������� (��������, USD ��� �������� ���). ����������� �������� ������ <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">� ����� ������ �� ��������� ISO</a>  ����� ������.<br>
������: 199.00 EUR.
';
$MESS[$strName . 'price'] = '����� ��������� ���������� � ��������� � ������ checkin_date � length_of_stay';
$MESS[$strHint . 'price'] = '<b>����������� ��� ���������� ���������� � ����������� �������.</b><br>
����� ��������� ���������� � ��������� � ������ checkin_date � length_of_stay. ����������� �������� ������ <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">� ����� ������ �� ��������� ISO</a> ����� ������.<br>
������: 1393.00 USD.
';
$MESS[$strName . 'tax'] = '������ ������';
$MESS[$strHint . 'tax'] = '<b>����������� ��� ���������� ���������� � ����������� �������.</b><br>
������ ������, ���������� � ���������. ����������� �������� ������ <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">� ����� ������ �� ��������� ISO</a> ����� ������.<br>
������: 14.
';
$MESS[$strName . 'fees'] = '��������, ���������� � ���������';
$MESS[$strHint . 'fees'] = '<b>����������� ��� ���������� ���������� � ����������� �������.</b><br>
��������, ���������� � ���������. ����������� �������� ������ <a href="https://en.wikipedia.org/wiki/ISO_4217" target="_blank">� ����� ������ �� ��������� ISO</a> ����� ������.<br>
������: 253.00 USD.
';
$MESS[$strName . 'url'] = '������ �� ������� ����, �� ������� ����� ������������� ����� � ���������.';
$MESS[$strHint . 'url'] = '<b>������������ ����.</b><br>
������ �� ������� ����, �� ������� ����� ������������� ����� � ���������. �� ����� ������ ������� URL �� <a href="https://developers.facebook.com/docs/marketing-api/dynamic-ads-for-travel/ads-management#creative" target="_blank"> ������ ������� </a>��� ������ template_url_spec. URL �� ������ ������� ����� ��������� ��� URL � �����.<br>
������: https://www.facebook.com/hotel.
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
$MESS[$strName . 'brand'] = '����� ���� ��������.';
$MESS[$strHint . 'brand'] = '<b>������������ ����.</b><br>
����� ���� ��������.<br>
������: Hilton.';
$MESS[$strName . 'address@format'] = '����������� ����, �������� ��� ��� �������������!';
$MESS[$strHint . 'address@format'] = '����������� ����, �������� ��� ��� �������������!';
$MESS[$strName . 'address.component@name'] = '����������� ����, �������� ��� ��� �������������!';
$MESS[$strHint . 'address.component@name'] = '����������� ����, �������� ��� ��� �������������!';
$MESS[$strName . 'address.component'] = '�������� ����� �������������';
$MESS[$strHint . 'address.component'] = '
�������� ����� ���������, ������� ������ ���� ���������� � �� ����������������.<br>
���������� �������������� ���� <br>
<b>������������</b> addr1[������� ����� ���������],city[�����],region[������],country[������],postal_code[������] <br>
<b>������������</b> addr2[�������������� ����� ���������],addr3[������ ����� ���������.],city_id[��������, ������� ����� �������������� � URL �������� ������ (template_url) � ��������� ��������.] <br>
��. <a  target="_blank" href="https://developers.facebook.com/docs/marketing-api/hotel-ads/catalog#address-object">��������� ������� ������</a>. <br>
';
$MESS[$strName . 'neighborhood'] = '�����, ��� ����������� ���������.';
$MESS[$strHint . 'neighborhood'] = '<b>������������ ����.</b><br>
������������ ���������� �������: 20.<br>
�����, ��� ����������� ���������. ���� �� ������ ������� ����� ������ ������, �������� �� ������� ��� ������� ������ � ����������� � �������� ������� ������� ��������� ���� JSON, ����� ������� ���������� �������.<br>
������: Belle Haven.
';
$MESS[$strName . 'latitude'] = '������';
$MESS[$strHint . 'latitude'] = '<b>������������ ����.</b><br>
������ ��������.<br>
������: 37.484100
';
$MESS[$strName . 'longitude'] = '�������';
$MESS[$strHint . 'longitude'] = '<b>������������ ����.</b><br>
������� ��������.<br>
������: -122.148252
';

$MESS[$strName . 'sale_price'] = '���� �� ������� �� ����� � ��������� � ������ checkin_date � length_of_stay';
$MESS[$strHint . 'sale_price'] = '<b>�������������� ����.</b><br>
���� �� ������� �� ����� � ��������� � ������ checkin_date � length_of_stay. �����������, ����� ���������� ������ �� ������� ����. �������� ���������� ��������� ������ � ������� (��������, USD ��� �������� ���). �������� sale_price ��� ��������� ������ ���� ������ �������� base_price. ����������� �������� ������ � ����� ������ �� ��������� ISO ����� ������.<br>
������: 149.00 USD.
';
$MESS[$strName . 'guest_ratings.score'] = '�������';
$MESS[$strHint . 'guest_ratings.score'] = '<b>�������������� ����.</b><br>
�������. ���� ������, ���������� ����� ������� score, max_score, number_of_reviewers � rating_system.<br>
������: 9.0/10.
';
$MESS[$strName . 'guest_ratings.rating_system'] = '�������, ������������ ��� ����� �������.';
$MESS[$strHint . 'guest_ratings.rating_system'] = '<b>�������������� ����.</b><br>
�������, ������������ ��� ����� �������.<br>
�������: Expedia, TripAdvisor.
';
$MESS[$strName . 'guest_ratings.number_of_reviewers'] = '����� ���������� �����, ��������� ���������.';
$MESS[$strHint . 'guest_ratings.number_of_reviewers'] = '<b>�������������� ����.</b><br>
����� ���������� �����, ��������� ���������.<br>
������: 5287.
';
$MESS[$strName . 'guest_ratings.max_score'] = '������������ �������� ��� �������� ���������. ';
$MESS[$strHint . 'guest_ratings.max_score'] = '<b>������������ ����.</b><br>
������������ �������� ��� �������� ���������. ������ ���� �� ����� ���� � �� ����� 100.<br>
������: 10.
';
$MESS[$strName . 'star_rating'] = '���������� �������';
$MESS[$strHint . 'star_rating'] = '';
$MESS[$strName . 'loyalty_program'] = '��������� ����������, � ������� ����� ����������� ����� �� ���������� � ���������.';
$MESS[$strHint . 'loyalty_program'] = '<b>�������������� ����.</b><br>
��������� ����������, � ������� ����� ����������� ����� �� ���������� � ���������.<br>
������: Premium program.
';
$MESS[$strName . 'margin_level'] = '��������� ������������ ���������';
$MESS[$strHint . 'margin_level'] = '<b>�������������� ����.</b><br>
��������� ������������ ��������� �� ��������� �� 1 �� 10.<br>
������: 9.
';
$MESS[$strName . 'phone'] = '�������� ����� �������� ���������.';
$MESS[$strHint . 'phone'] = '<b>�������������� ����.</b><br>
�������� ����� �������� ���������.<br>
������: +61 296027455.
';
$MESS[$strName . 'applink'] = '�������� �������� ������ �� �������� �������� � ��������� � ��������� ����������,';
$MESS[$strHint . 'applink'] = '<b>�������������� ����.</b><br>
�������� �������� ������ �� �������� �������� � ��������� � ��������� ����������, ��������� <a href="https://developers.facebook.com/docs/applinks" target="_blank">App Links</a>. �� ������ ������� �������� ������ (� ������� �������� ����������):<br>
�� ������ ������� ��� ������ template_url_spec.<br>
� �����, ��������� ������ Applink.<br>
����� ���������� �� ���� ���� ��������� App Links.<br>
��������� � <a href="https://developers.facebook.com/docs/marketing-api/catalog/guides/product-deep-links" target="_blank">�������� ������� �� ������</a> <br>
';

$MESS[$strName . 'priority'] = '��������� ���������� ��������� ';
$MESS[$strHint . 'priority'] = '<b>�������������� ����.</b><br>
��������� ���������� ��������� �� 0 (����� ������ ���������) �� 5 (����� ������� ���������).<br>
������: 5.
';
$MESS[$strName . 'category'] = '��� ������� ������������.';
$MESS[$strHint . 'category'] = '<b>�������������� ����.</b><br>
��� ������� ������������. ����� ������������ ����� ���������� ���������. <br>
������: Resort, Day Room.
';
$MESS[$strName . 'number_of_rooms'] = '����� ���������� ������� � ����������';
$MESS[$strHint . 'number_of_rooms'] = '<b>�������������� ����.</b><br>
����� ���������� ������� � ����������.<br>
������: 150.
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
