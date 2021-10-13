<?

$strMessPrefix = 'ACRIT_EXP_FACEBOOK_CARS_OFFERS_';
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);
// General
$MESS[$strMessPrefix . 'NAME'] = 'FB ���������� Offer Ads';

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
$MESS[$strName . 'vehicle_offer_id'] = '���������� ������������� ����������.';
$MESS[$strHint . 'vehicle_offer_id'] = '����������� .

���������� ������������� ����������. ������� ������������� ����� ������������ ��� ����������� �����������. ��� �� �� ��������, ������� ���������� � �������� content_id��������� � ������� .

������: offer1
';
$MESS[$strName . 'make'] = '����� ����������';
$MESS[$strHint . 'make'] = '����������� .

"Make" ��� ����� ����������.

������: Endomoto
';

$MESS[$strName . 'model'] = '������ ����������';
$MESS[$strHint . 'model'] = '����������� .

������ ����������.

������: EndoHatch
';
$MESS[$strName . 'year'] = '��� ������� ����������';
$MESS[$strHint . 'year'] = '�����������.
��� ������� ���������� � yyyy ������.
������: 2015
';

$MESS[$strName . 'offer_type'] = '��� �����������';
$MESS[$strHint . 'offer_type'] = '�����������.
��� �����������. ���������� ��������: lease, finance, cash
';
$MESS[$strName . 'title'] = '�������� ���������� / �����������';
$MESS[$strHint . 'title'] = '����������� .

�������� ���������� / �����������.

������: "$299 per month for the EndoHatch GE!"
';
$MESS[$strName . 'offer_description'] = '�������� �����������.';
$MESS[$strHint . 'offer_description'] = '����������� .

�������� �����������.

������: This offer is valid only during the month of September.
';
$MESS[$strName . 'url'] = '������ �� ������� ����, ��� �� ������ ����������� �����������.';
$MESS[$strHint . 'url'] = '����������� .

������ �� ������� ����, ��� �� ������ ����������� �����������.
';

$MESS[$strName . 'offer_disclaimer'] = '����� �� ���������������';
$MESS[$strHint . 'offer_disclaimer'] = '����������� .

����� �� ���������������, ��������� � ������������.
';
$MESS[$strName . 'image.url'] = 'URL-����� �����������';
$MESS[$strHint . 'image.url'] = '����������� .

��������: 20

URL ����������� ����������. ���� � ��� ���� ����� ��� ���� ����������� ������������� ��������, ��������� ����� ������� ����������: image[1].url, image[2].url� ��� �����. �� ������ ������������ ���� �� ���� ����������� . ������ ������� ����������� ����� ���������� �� 4 ��. ��� �������� �������� ��������� ������� 2 �����������.

����� ������������ ����������� �������, ������������ ���������� ����������� � ������������ ������ 1: 1 (600 x 600 ��������).

��� ������ ������� � ����� ������������ ��������� - ������������ ����������� � ������������ ������ 1,91: 1 (1200 x 630 ��������).
������� ������ � ������ ������� ������ � ������������� � Marketplace .
';
$MESS[$strName . 'image.tag'] = '��� �����������';
$MESS[$strHint . 'image.tag'] = '� ����������� �������� ���, ������� ����������, ��� �� ��� ����������. � ������������ ����� ���� ������� ��������� �����.<br>
�������: Fitness Center,Swimming Pool<br>
INSTAGRAM_STANDARD_PREFERRED- ��������� �������������� �������� ������������ ����������� � ����� ���� ��� ����������� �� ���������, ������� ����� �������������� ��� Instagram. ���� ��� ������������ � ��������.
';
$MESS[$strName . 'amount_price'] = '����� ������ ��� ��������� �����������.';
$MESS[$strHint . 'amount_price'] = '����������� .

����� ������ ��� ��������� �����������. �������������� ���� ��� ���������, �� ������� ������� ��� ������ ISO � �������� ����� ���������� � �������.

������: ��� ������ 329 �������� � ����� ��� ������� � 2000 �������� ���������� �������� 329 USD��� 2000 USD.
';


$MESS[$strName . 'amount_percentage'] = '�������� � ���������';
$MESS[$strHint . 'amount_percentage'] = '����������� .

�������� � ���������, ���� ��� ������� � �����������. ������ �������� ���������� � ��������� (������ :), 3.9% APR� �� � �����.
';
$MESS[$strName . 'amount_qualifier'] = '������������ ����� � �������� ��� � ��������� �� �����';
$MESS[$strHint . 'amount_qualifier'] = '����������� .

������������ ����� � �������� ��� � ��������� �� �����. ���������� �������� ����� ���� per month��� APR.

������: /mo100 $ / ��� ��� ����������� ������. APR� 1,1% ������� ��� ���������� �����������.
';
$MESS[$strName . 'term_length'] = '���� �������� �����������';
$MESS[$strHint . 'term_length'] = '����������� .

���� �������� �����������. ���� ����������� ������������ ����� ������ �� ���� 329 ����. ��� � ����� �� 3 ����, �� ��������� �����, 3� ��������������� ��������� term_qualifier- ����� years.

������: /mo100 $ / ��� ��� ����������� ������. APR� 1,1% ������� ��� ���������� �����������.
';
$MESS[$strName . 'term_qualifier'] = '������� �� ���� �������� �����������';
$MESS[$strHint . 'term_qualifier'] = '����������� .

������� �� ���� �������� �����������. ���������� ��������: �������� ��� ������.

������: months��� ����������� 329 �������� � ����� �� 36 ������� � year��� ����������� 329 �������� � ����� �� 3 ����.
';

$MESS[$strName . 'downpayment'] = '����� ��������������� ������ ��� ������� ��� ������';
$MESS[$strHint . 'downpayment'] = '����������� .

����� ��������������� ������ ��� ������� ��� ������. �������������� ���� ��� ���������, �� ������� ������� ��� ������ ISO � �������� ����� ���������� � �������.

������: ����������� 1500 USD� �������� ��������, ���� ���� ������� ���������� $1500 due at signing + 1 month payment.
';
$MESS[$strName . 'downpayment_qualifier'] = '������������ downpayment��������';
$MESS[$strHint . 'downpayment_qualifier'] = '����������� .

������������ downpayment��������. ������: ����������� due at signing + 1 month payment��������, ���� ���� ������� ��������������� ������� $1500 due at signing + 1 month payment.
';

$MESS[$strName . 'trim'] = '������� ������ ����������';
$MESS[$strHint . 'trim'] = '����������� .

������� ������ ����������. ������: GE. .

������� ��������� ����������. ���������� ��������: New, Used��� CPO(����������������� �����������).';
$MESS[$strName . 'price'] = '������������� �������������� ��������� ���� ���������� � �������';
$MESS[$strHint . 'price'] = '�������������

������������� �������������� ��������� ���� ���������� � �������. �������������� ���� ��� ���������, �� ������� ������� ��� ������ ISO � �������� ����� ���������� � �������. ������:13,999 USD
';
$MESS[$strName . 'body_style'] = '����� �����������.';
$MESS[$strHint . 'body_style'] = '����������� .

����� �����������. ���������� ��������: CONVERTIBLE, COUPE, HATCHBACK, MINIVAN, TRUCK, SUV, SEDAN, VAN, WAGON, CROSSOVER, ���OTHER
';
$MESS[$strName . 'start_date'] = '���� ������, � ������� ����������� �������������';
$MESS[$strHint . 'start_date'] = '������������� .

���� ������, � ������� ����������� �������������. ������ ���� � ������� ���� ����-��-�� .

������: 2018-09-05
';
$MESS[$strName . 'end_date'] = '���� ���������';
$MESS[$strHint . 'end_date'] = '������������� .

���� ���������, ����� ������� ����������� �������������. ������ ���� � ������� ���� ����-��-�� .

������: 2018-09-05
';

$MESS[$strName . 'market_name'] = '������� ����������';
$MESS[$strHint . 'market_name'] = '������������� .

�������� ����� / ������������ �������� ���� (DMA).
';

$MESS[$strName . 'dma_codes'] = '��� ������������� �������� ';
$MESS[$strHint . 'dma_codes'] = '����������� ��� ������������� �����������. ������������� ��� ������������� ����������� .

������ ����� ���������� �������� ���� (DMA) ��� ����������� �����<a href="https://help-ooyala.brightcove.com/sites/all/libraries/dita/en/video-platform/reference/dma_codes.html?fbclid=IwAR2Ruflliy9uMeMTcySfxZ7iathPEGMcmm-1xlFf1j6u5224bjTqz5i3qtw" target="_blank">������� �������</a>   ��� ������� XML. ��. �������� ���� DMA . �������� ���� ������ ��� ������������� �����������.
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
