<?
/*********************************************************************************
��������� ������ eDost (��� ���������� ������ ���� �� ��������������)
*********************************************************************************/

//define('DELIVERY_EDOST_WEIGHT_DEFAULT', '5000'); // ��� � ������� ������� ������ �� ��������� (����� ��������������, ���� ��� � ������ �� �����)

//define('DELIVERY_EDOST_WEIGHT_PROPERTY_NAME', 'WEIGHT'); // �������� �������� (PROPERTY) ������, � ������� �������� ���
//define('DELIVERY_EDOST_WEIGHT_PROPERTY_MEASURE', 'G'); // 'KG' ��� 'G' - ������� ��������� �������� (PROPERTY) ������, � ������� �������� ���

//define('DELIVERY_EDOST_VOLUME_PROPERTY_NAME', 'VOLUME'); // �������� �������� (PROPERTY) ������, � ������� �������� ����� 'VOLUME' (������������, ����� �������� � ������� �� ������)
//define('DELIVERY_EDOST_VOLUME_PROPERTY_RATIO', 1000); // ����������� �������� ������� ��������� ������ � ������� ��������� ��������� (������: ���������� = 1000, ���� ����� � ������ ����������, � �������� � �����������)

// �������� ������� (PROPERTY) ������, � ������� �������� ��������
//define('DELIVERY_EDOST_LENGTH_PROPERTY_NAME', 'LENGTH');
//define('DELIVERY_EDOST_WIDTH_PROPERTY_NAME', 'WIDTH');
//define('DELIVERY_EDOST_HEIGHT_PROPERTY_NAME', 'HEIGHT');

define('DELIVERY_EDOST_FUNCTION', 'Y'); // 'Y' - ���������� ���� � ����������������� ��������� 'edost_function.php'
//define('DELIVERY_EDOST_FUNCTION_RUN_AFTER_CACHE', 'Y'); // 'Y' - �������� �������� ������� 'AfterCalculate' � 'AfterGetOffice', ���� ������ ��������� �� ����

//define('DELIVERY_EDOST_BUYER_STORE', '2876A178=1,2441=2'); // �������� ������� ������ eDost � ������� ��������: '��� eDost'='��� ��������',... (������: '1234A1234=1,100=2')
//define('DELIVERY_EDOST_IGNORE_ZERO_WEIGHT', 'Y'); // 'Y' - ������������ ��������, ���� � ������� ���� ����� � ������� �����

//define('DELIVERY_EDOST_WEIGHT_FROM_MAIN_PRODUCT', 'Y'); // 'Y' - ������������ ��� �������� ������, ���� � ��� ��������� ����������� ��� �� �����
//define('DELIVERY_EDOST_PROPERTY_FROM_MAIN_PRODUCT', 'Y'); // 'Y' - ������������ �������� (PROPERTY) �������� ������ (��������, ��� � �����)

//define('DELIVERY_EDOST_JS_SALE_ORDER_AJAX', 'N'); // 'N' - �� ���������� JS ���������� � ����� �� �������� ���������� ������ (sale.order.ajax)
define('DELIVERY_EDOST_PICKPOINT_WIDGET', 'N'); // 'Y' - ������������ ��������� ������ PickPoint ��� ������ ���������� � ������� ������ �� ����� (�������� ������ � ������� Visual)

//define('DELIVERY_EDOST_WRITE_LOG', 1); // 1 - ������ ������ ������� � ��� ���� ����� ������� CDeliveryEDOST::WriteLog()
define('DELIVERY_EDOST_CACHE_LIFETIME', 18000); // ��� 5 ����� = 60*60*5, ��� 1 ���� = 60*60*24*1
?>