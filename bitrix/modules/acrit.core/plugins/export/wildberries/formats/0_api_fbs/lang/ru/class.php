<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'Wildberries.ru (API)';

// Steps
$MESS[$strLang.'STEP_PAUSE_BEFORE_STOCKS'] = '���������� � �������� ��������';
$MESS[$strLang.'STEP_EXPORT_STOCKS'] = '�������� ��������';

// Settings
$strSName = $strLang.'SETTINGS_NAME_';
$strSHint = $strLang.'SETTINGS_HINT_';
$MESS[$strSName.'SUPPLIER_ID'] = '������������� ���������� (supplierID)';
	$MESS[$strSHint.'SUPPLIER_ID'] = '������������� ���������� ����� ������ � <a href="https://suppliers.wildberries.ru/specification/client/index" target="_blank">������ �������� Wildberries</a> (������ ��������� UUID�).<br/><br/>
	������ ������������� ����� ��� �������� ������ ����� �����������, �.�. � ���������� ��� ������� ����������� �� ����� ������� ����������.';
	$MESS[$strLang.'SUPPLIER_ID'] = '������: 10c4c229-32bf-4a03-accf-4f0a8d43349d';
$MESS[$strSName.'AUTH'] = '����������� �� ������ ��������';
	$MESS[$strSHint.'AUTH'] = '�������� �� Wildberries ������� ����������� � ������� �� ������ ��������.';
		$MESS[$strLang.'AUTH_PHONE'] = '����� ��������: +79998887766';
		$MESS[$strLang.'AUTH_BUTTON'] = '��������� �����������';
		$MESS[$strLang.'AUTH_CONFIRM'] = '������� ���, ������������ ��� �� SMS �� #PHONE# (#LENGTH# ��������):';
		$MESS[$strSName.'TOKEN'] = '����� �����������';
			$MESS[$strSHint.'TOKEN'] = '����� ��� ���������� �������� � Wildberries.<br/><br/>����� �������� �����, �������� ������� ���� � ��������� ����.';
			$MESS[$strLang.'TOKEN_EMPTY'] = '����� �����������';
			$MESS[$strLang.'TOKEN_CHECK'] = '��������� �����';
			$MESS[$strLang.'TOKEN_GET'] = '�������� �����';
			$MESS[$strLang.'TOKEN_CLEAR_CONFIRM'] = '�� �������, ��� ������ �������� �����?'.PHP_EOL.'��� ��������� ����� �����������.';
			$MESS[$strLang.'TOKEN_ERROR_SUPPLIER_ID'] = '������� ���������� ������������� ����������';
			$MESS[$strSName.'AUTHORIZATION'] = '������ ����� �����������';
			$MESS[$strSHint.'AUTHORIZATION'] = '������� ����� ������ ����� �����������, �������� ��� ����� �� �������� <a href="https://suppliers-portal.wildberries.ru/supplier-settings/access-to-new-api" target="_blank">������� ��������</a>.';
$MESS[$strLang.'EXPORT_STOCKS_CHECKBOX'] = '��������� �������';
	$MESS[$strLang.'EXPORT_STOCKS_HINT'] = '�������� �����, ���� ���������� ���������� ������� ���� �� ��������.
	<br/><br/>��� �������� ���������� ������� <a href="https://suppliers-portal.wildberries.ru/marketplace-pass/api-access" target="_blank">�����</a>, ID ������ (�� ������ � �� Wildberries) � �������� ������ - ����� ����� ��� ��������.';
	// $MESS[$strLang.'STOCK_TOKEN'] = '����� ��� �������� ��������';
	// 	$MESS[$strLang.'STOCK_TOKEN_DESC'] = '����� ����� �������� �� �������� <a href="#TOKEN_URL#" target="_blank">������ � API</a>.';
	// 	$MESS[$strLang.'STOCK_TOKEN_GET'] = '�������� �����';
	$MESS[$strLang.'STOCK_ID'] = 'ID ������';
	$MESS[$strLang.'STOCK_NAME'] = '�������� ������';
	$MESS[$strLang.'STOCK_HINT'] = 'ID ������ ����� ������ �� �������� <a href="#STORE_URL#" target="_blank">����� ����������</a> (� ���������� ������� ��������).<br/><br/>
�������� ������ - ������������ �������� ��� �������� ���������� � ������ �����.';
	$MESS[$strLang.'FIELD_STOCK'] = '������� �#STORE_NAME#� [#STORE_ID#]';
	$MESS[$strLang.'FIELD_STOCK_DESCRIPTION'] = '������� ����� �������� ������� ��� ���������� ������.';
	$MESS[$strLang.'OFFERS_NEW_MODE_CHECKBOX'] = '��������� �� ��� ������ (��� ������ �������!)';
	$MESS[$strLang.'OFFERS_NEW_MODE_HINT'] = '������ ����� �������� ����� ��������, ��� ������� ������ �������� ����������� ����������� �� ��� ��������, � ��� ��������� �����.<br/><br/>
	��� ������� � �������, ����� �� ������������ ����� �� ���������� ��������, ������������� �����, ������, ��������, � �.�., �� � ��������������� ����������������, � �.�. ���� � ������� �� ����� ���� ���� ����������.<br/><br/>
	�� ����� � ��� �� ����� ������ ����� ����� ���� �������� ��� �������� ����� ��������� ������� � ��������� ��� ������ - � ����������� �� ��������� ������ � ��.<br/><br/>
	<b>��������!</b> �� ���������� �������� ����� ����� ��������.';


// Fields
$MESS[$strHead.'HEADER_GENERAL'] = '�������� ������ � �������';
$MESS[$strName.'object'] = '��������� ������ Wildberries';
	$MESS[$strHint.'object'] = '������� ����� �������� ���������.<br/><br/>
	�������� ��������� ������ ���� ������� � �������� ��� ��, ��� �� Wildberries. ������ ��������� �����, ����� ������ ��������� ���������� �� ������� ����������.';
// $MESS[$strName.'supplierVendorCode'] = '������� ����������';
// 	$MESS[$strHint.'supplierVendorCode'] = '������� ����������. ����������� ������ ��������� �������, �����, ������ �������������.';
$MESS[$strName.'countryProduction'] = '������-�������������';
	$MESS[$strHint.'countryProduction'] = '������� ����� ������ �����������.';
$MESS[$strName.'vendorCode'] = '�������';
	$MESS[$strHint.'vendorCode'] = '������� ������.';
$MESS[$strName.'barcode'] = '��������';
	$MESS[$strHint.'barcode'] = '�������� ������/�����������.<br/><br/>
�������� ������ ���� ���������� � �������� ����� ������� Wildberries, ����� �������� �������� ����������.';

$MESS[$strLang.'GUESS_BRAND'] = '�����';
$MESS[$strLang.'GUESS_DESCRIPTION'] = '��������';
$MESS[$strLang.'GUESS_TNVED'] = '�����';

$MESS[$strLang.'CUSTOM_ATTR_PRICE'] = '��������� ����';
	$MESS[$strLang.'CUSTOM_ATTR_PRICE_UNIT'] = '������';
$MESS[$strLang.'CUSTOM_ATTR_PHOTO'] = '����';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_PHOTO'] = '����������� ���������� - <code><b>450�600</b></code><br/>
������������ ���������� - <code><b>10 ����</b></code>.';
$MESS[$strLang.'CUSTOM_ATTR_INGREDIENTS'] = '������';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_INGREDIENTS'] = '������ ������� �� ������� ����������� � ����������� �� ���������� �� �����������. �������� ����������� � ���������.<br/><br/>
����� �������� ����������� ������� ������ ���� ����� 100%.';
$MESS[$strLang.'CUSTOM_ATTR_ADDITIONAL_COLORS'] = '���. �����';
$MESS[$strLang.'CUSTOM_ATTR_KEYWORDS'] = '�������� �����';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_KEYWORDS'] = '�������� ����� - ��� ����� � ����� ����������� ��� ������� � ������������� �������������� ��������, ����� ������ ����� ����� ��� �����.<br/><br/>
�������: ����������, 100% ������, ������� �����.<br/><br/>
����������� �� ����� 16�� ����.';
$MESS[$strLang.'CUSTOM_ATTR_DESCRIPTION'] = '��������';
	$MESS[$strLang.'DESC_CUSTOM_ATTR_DESCRIPTION'] = '������� �������� �������������� ��������, ��� ����������� � ������������.<br/><br/>
������������ ���������� �������� - 1000.<br/><br/>
����������� �������: <code><b>�-��-߸�0-9a-zA-Z @!?,.|/:;\'"*&@#$�%[]{}()+-$</b></code>';

$MESS[$strLang.'REF'] = ' (�� �����������)';
$MESS[$strLang.'FOR_VARIATION'] = ' (��� ��������)';

// $MESS[$strLang.'FIELD_FOR_NOMENCLATURE'] = ' [���. ����]';

$MESS[$strLang.'ERROR_EMPTY_PRODUCT_CATEGORY'] = '��� ������ #ELEMENT_ID# �� ������� ���������.';

$MESS[$strLang.'NOTICE_SUPPORT'] = '<b>��������!</b> �� ������ ������ �������� �� ���������������� ������� ���������� ������������. ������ � ��������� �������������� <a href="/bitrix/admin/acrit_exportproplus_new_support.php?lang=ru&AcritExpSupport_active_tab=ask" target="_blank">�� ������� ������</a>.';

$MESS[$strLang.'LOG_ELEMENT'] = '����� ##ELEMENT_ID# [#VENDOR_CODE#]: #METHOD#';
$MESS[$strLang.'LOG_ELEMENT_DEBUG'] = '����� ##ELEMENT_ID# [#VENDOR_CODE#]: ������ �������� ������ #METHOD# #JSON# #RESULT#';
$MESS[$strLang.'LOG_IMAGE_UPLOAD_ERROR'] = '����� ##ELEMENT_ID#: ������ �������� ����������� #UUID# (#URL#): ��� ������ #RESPONSE_CODE#: #RESPONSE#';
$MESS[$strLang.'LOG_STOCKS_EXPORTED'] = '������� ��������� (�����: #COUNT#).';
$MESS[$strLang.'LOG_STOCKS_ERROR_TITLE'] = '������ �������� ��������.';
$MESS[$strLang.'LOG_STOCKS_ERROR'] = '������� �� ��������� ��-�� ������: #RESPONSE_CODE# <pre>#HEADERS#</pre> <pre>#CONTENT#</pre>';

$MESS[$strLang.'ERROR_LOGIN_TOO_MANY_ATTEMPLS'] = '����������, ��������� ������ �������� ����� #TIME# �.';
$MESS[$strLang.'ERROR_WRONG_CONFIRM_CODE'] = '������ �������� SMS-���.';
$MESS[$strLang.'ERROR_RECEIVED_EMPTY_TOKEN'] = '������ �����������. ���������� ��� ���.';
$MESS[$strLang.'ERROR_EMPTY_REQUIRED_FIELDS'] = '��� ��������� �#CATEGORY#� �� ��������� ������������ ����: #FIELDS#';
$MESS[$strLang.'ERROR_INGREDIENTS_SUMM'] = '����� �������� ����������� ������� ������ ���� ����� 100% [������� ID = #ELEMENT_ID#].';
$MESS[$strLang.'ERROR_EXPORT_ITEMS_BY_API'] = '������ �������� ������� � Wildberries.';
