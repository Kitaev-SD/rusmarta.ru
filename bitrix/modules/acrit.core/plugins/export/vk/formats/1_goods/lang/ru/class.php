<?
$strMessPrefix = 'ACRIT_EXP_VK_GOODS_';

// General

$MESS[$strMessPrefix.'NAME'] = '��������� (������)';

// Headers
$MESS[$strMessPrefix.'HEADER_GENERAL'] = '����� ������';

$MESS[$strMessPrefix."FIELD_ITEM_ID_NAME"] = "������������� ������";
	$MESS[$strMessPrefix."FIELD_ITEM_ID_DESC"] = "������������� ������.";
$MESS[$strMessPrefix."FIELD_OWNER_ID_NAME"] = "������������� ��������� �������";
$MESS[$strMessPrefix."FIELD_NAME_NAME"] = "�������� ������";
	$MESS[$strMessPrefix."FIELD_NAME_DESC"] = "����� �������� - �� 4 �� 100 ��������";
$MESS[$strMessPrefix."FIELD_DESCRIPTION_NAME"] = "�������� ������";
	$MESS[$strMessPrefix."FIELD_DESCRIPTION_DESC"] = "����������� ����� 10 ��������";
$MESS[$strMessPrefix."FIELD_CATEGORY_ID_NAME"] = "������������� ��������� ������";
$MESS[$strMessPrefix."FIELD_PRICE_NAME"] = "���� ������";
$MESS[$strMessPrefix."FIELD_OLD_PRICE_NAME"] = "������ ����";
$MESS[$strMessPrefix."FIELD_MAIN_PHOTO_ID_NAME"] = "���������� ������� ������";
	$MESS[$strMessPrefix."FIELD_MAIN_PHOTO_ID_DESC"] = "���������� �������: JPG, PNG, GIF.<br/>
�����������: ����������� ������ ���� � 400x400px, ����� ������ � ������ �� ����� 14000px, ���� ������� �� ����� 50 ��. ";
$MESS[$strMessPrefix."FIELD_PHOTO_IDS_NAME"] = "�������������� ����������";
	$MESS[$strMessPrefix."FIELD_PHOTO_IDS_DESC"] = "�������������� ���������� ������.";
$MESS[$strMessPrefix."FIELD_URL_NAME"] = "������ �� ���� ������";
	$MESS[$strMessPrefix."FIELD_URL_DESC"] = "������������ ����� - 50 ��������";
$MESS[$strMessPrefix."FIELD_DIMENSION_WIDTH_NAME"] = "������ � �����������";
	$MESS[$strMessPrefix."FIELD_DIMENSION_WIDTH_DESC"] = "������������� �����, ����������� �������� 0, ������������ �������� 100000.";
$MESS[$strMessPrefix."FIELD_DIMENSION_HEIGHT_NAME"] = "������ � �����������";
	$MESS[$strMessPrefix."FIELD_DIMENSION_HEIGHT_DESC"] = "������������� �����, ����������� �������� 0, ������������ �������� 100000.";
$MESS[$strMessPrefix."FIELD_DIMENSION_LENGTH_NAME"] = "������� � �����������";
	$MESS[$strMessPrefix."FIELD_DIMENSION_LENGTH_DESC"] = "������������� �����, ����������� �������� 0, ������������ �������� 100000.";
$MESS[$strMessPrefix."FIELD_WEIGHT_NAME"] = "��� � �������";
	$MESS[$strMessPrefix."FIELD_WEIGHT_DESC"] = "������������� �����, ����������� �������� 0, ������������ �������� 100000000.";
$MESS[$strMessPrefix."FIELD_SKU_NAME"] = "������� ������";
	$MESS[$strMessPrefix."FIELD_SKU_DESC"] = "������������ ������, ������������ ����� - 50 ��������";
$MESS[$strMessPrefix."FIELD_ALBUM_NAME_NAME"] = "�������� �������� �������";
	$MESS[$strMessPrefix."FIELD_ALBUM_NAME_DESC"] = "�������� �������� ������� ������ VK.";
$MESS[$strMessPrefix."FIELD_ALBUM_PHOTO_ID_NAME"] = "�������� ��������";
	$MESS[$strMessPrefix."FIELD_ALBUM_PHOTO_ID_DESC"] = "���������� �������: JPG, PNG, GIF.<br/>
�����������: ����������� ������ ���� � 1280x720px, ����� ������ � ������ �� ����� 14000px, ���� ������� �� ����� 50 ��. ";
$MESS[$strMessPrefix."FIELD_DELETED_NAME"] = "����������� �����";
	$MESS[$strMessPrefix."FIELD_DELETED_DESC"] = "������� 0, ���� ����� ��������, 1 ���� ����� �����.";
$MESS[$strMessPrefix."FIELD_PORTAL_REQUIREMENTS"] = "https://vk.com/dev/methods";

# Steps
$MESS[$strMessPrefix.'STEP_EXPORT'] = '������� � VK';

# Tabs
$MESS[$strMessPrefix.'TAB_CLEAR_NAME'] = '�������';
$MESS[$strMessPrefix.'TAB_CLEAR_DESC'] = '�������� ������� ������';
$MESS[$strMessPrefix.'TAB_ALBUMS_NAME'] = '��������';
$MESS[$strMessPrefix.'TAB_ALBUMS_DESC'] = '������ � ���������� ������';
$MESS[$strMessPrefix.'CLEAR_HEADER'] = '�������� �������';
$MESS[$strMessPrefix.'CLEAR_ALERT'] = '������� ������?';
$MESS[$strMessPrefix.'CLEAR_ALL'] = '�������� ���� �������';
$MESS[$strMessPrefix.'CLEAR_ALL_DESC'] = '�������� ���� ������� ������';
$MESS[$strMessPrefix.'CLEAR_ALL_BTN_TITLE'] = '������� ��� ������';
$MESS[$strMessPrefix.'CLEAR_LOADED'] = '�������� ����������� �������';
$MESS[$strMessPrefix.'CLEAR_LOADED_DESC'] = '�������� �������, ����������� � ����, ��� ������������ � ������ ������� ��������';
$MESS[$strMessPrefix.'CLEAR_LOADED_BTN_TITLE'] = '������� ����������� ������';
$MESS[$strMessPrefix.'CLEAR_ALBUM'] = '�������� ������� ��������';
$MESS[$strMessPrefix.'CLEAR_ALBUM_DESC'] = '�������� ������� ��������� ��������';
$MESS[$strMessPrefix.'CLEAR_ALBUM_BTN_TITLE'] = '������� ������ ��������';
$MESS[$strMessPrefix.'CLEAR_ALBUM_SELECT_TITLE'] = '�������� ��������';
$MESS[$strMessPrefix.'ALBUMS_HEADER'] = '������������ ��� �������� � ��������';
$MESS[$strMessPrefix.'ALBUMS_TABLE_H1'] = '������';
$MESS[$strMessPrefix.'ALBUMS_TABLE_H2'] = '�������� ��������';
$MESS[$strMessPrefix.'ALBUMS_TABLE_H3'] = 'ID �������� (����������� �������������)';
$MESS[$strMessPrefix.'ALBUMS_TABLE_NOTE'] = '������ ������� ����� ��������� ���� � ��� ������, ���� ��� ���� "�������� �������� ������� (album_name)" �� ������ ��������� ������ "ID (������)" ������ (��. ������� "��������� ����������" / "���� �������").
<br><br>� ������� �����, ������� "ID ��������" �� ����� ���������. �������� � �� �������� ��� ��������� �������� ��� ��� �����, � ������� �������� ������� "�������� ��������".';

# Process
$MESS[$strMessPrefix.'PROCESS_PHASED_END_STEP'] = '�������� ���� ��������. ��� ��������� ������� ������� ����������� � ������� #POSITION#.';
$MESS[$strMessPrefix.'PROCESS_PHASED_END_ALL'] = '��������� ��� ����� ��������.';

?>
