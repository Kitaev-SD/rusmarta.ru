<?
$strMessPrefix = 'ACRIT_EXP_AVITO_REALTY_';

// General
$MESS[$strMessPrefix.'NAME'] = '����� (������������)';

// Headers
$MESS[$strMessPrefix.'HEADER_LEASE'] = '�������������� ��� ������';

// Fields
$MESS[$strMessPrefix.'FIELD_STREET_NAME'] = '����� ������� ����������';
	$MESS[$strMessPrefix.'FIELD_STREET_DESC'] = '����� ������� ���������� � ������ �� 256 ��������, ����������:<br/>
����� ������� � ������ �� 256 ��������, ����������:
<ul>
	<li>�������� ����� � ����� ���� � ���� ����� ������ ���������� ����� �� <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">�����������</a>;</li>
	<li>���� ������� ����������� ������ ��� � �����������, �� � ���� �������� ����� �������:
		<ul>
			<li>����� ������� (���� ����),</li>
			<li>���������� ����� (�����������),</li>
			<li>����� � ����� ����, �������� ��� ���������� ���.: "���������� �-�, �. �����, ��. ������, �. 7".</li>
		</ul>
	</li>
</ul>
����������:<br/>
<ul>
	<li>������� �������� ����������, ������������� ������������ ������� "Address";</li>
	<li>��� �������-���������� ��� �������� NewDevelopmentId ���� Street �� �����������, �. �. �������� ������� �� ����������� ����������� ����� � �� ����� ���� ��������������;</li>
	<li>��� ����, ����� ��� ������ ��� ���������� ������������ � ������ �� �����, ����������:
		<ul>
			<li>������� ��� ������ �����, ��������� <a href="https://yandex.ru/maps/" target="_blank">������.������</a>,</li>
			<li>��� ������ �������������� ���������� (��. ����).</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LATITUDE_NAME'] = '�������������� ������';
	$MESS[$strMessPrefix.'FIELD_LATITUDE_DESC'] = '�������������� ������ ������� (��� �������� ����� �� �����), <a href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B5_%D0%BA%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%82%D1%8B#.D0.A4.D0.BE.D1.80.D0.BC.D0.B0.D1.82.D1.8B_.D0.B7.D0.B0.D0.BF.D0.B8.D1.81.D0.B8_.D0.B3.D0.B5.D0.BE.D0.B3.D1.80.D0.B0.D1.84.D0.B8.D1.87.D0.B5.D1.81.D0.BA.D0.B8.D1.85_.D0.BA.D0.BE.D0.BE.D1.80.D0.B4.D0.B8.D0.BD.D0.B0.D1.82" target="_blank">� �������� � ���������� �����</a>.<br/><br/>
����������:<br/>
<ul>
	<li>���� ���������� �������, ����������� ���������� ����� ������������ �� ���, � ������� Address ����� ��������������.</li>
	<li>���� ���������� �� ������, �� ����� ���������� ��������� ����� �� ����� �������������, ��������� �������������� ������� �� ��������� ����� "City" � "Street";</li>
	<li>��� �������-���������� � NewDevelopmentId ���������� ������� �� ����������� ����������� ����� � �� ����� ���� ��������������,</li>
	<li>�������� Latitude � Longitude �������� ���������������, �� ���� ��� �������, ����������� ���������� ����� ������������ �� ���, � ������� Address ����� ��������������.</li>
</ul>
<b>��������!</b> � 28.10.2019 �� ����� �������������� ����������� ���������� �� ��������� Region, City, District, Subway, Street. ��� ����������� ���������� ����������� ������������ ������� Address. � 25.11.2019 �������� Region, City, District, Subway, Street ���������� �������������� � XML-�����.';
$MESS[$strMessPrefix.'FIELD_LONGITUDE_NAME'] = '�������������� �������';
	$MESS[$strMessPrefix.'FIELD_LONGITUDE_DESC'] = '�������������� ������� ������� (��� �������� ����� �� �����), <a href="https://ru.wikipedia.org/wiki/%D0%93%D0%B5%D0%BE%D0%B3%D1%80%D0%B0%D1%84%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D0%B5_%D0%BA%D0%BE%D0%BE%D1%80%D0%B4%D0%B8%D0%BD%D0%B0%D1%82%D1%8B#.D0.A4.D0.BE.D1.80.D0.BC.D0.B0.D1.82.D1.8B_.D0.B7.D0.B0.D0.BF.D0.B8.D1.81.D0.B8_.D0.B3.D0.B5.D0.BE.D0.B3.D1.80.D0.B0.D1.84.D0.B8.D1.87.D0.B5.D1.81.D0.BA.D0.B8.D1.85_.D0.BA.D0.BE.D0.BE.D1.80.D0.B4.D0.B8.D0.BD.D0.B0.D1.82" target="_blank">� �������� � ���������� �����</a>.<br/><br/>
����������:<br/>
<ul>
	<li>���� ���������� �������, ����������� ���������� ����� ������������ �� ���, � ������� Address ����� ��������������.</li>
	<li>���� ���������� �� ������, �� ����� ���������� ��������� ����� �� ����� �������������, ��������� �������������� ������� �� ��������� ����� "City" � "Street";</li>
	<li>��� �������-���������� � NewDevelopmentId ���������� ������� �� ����������� ����������� ����� � �� ����� ���� ��������������,</li>
	<li>�������� Latitude � Longitude �������� ���������������, �� ���� ��� �������, ����������� ���������� ����� ������������ �� ���, � ������� Address ����� ��������������.</li>
</ul>
<b>��������!</b> � 28.10.2019 �� ����� �������������� ����������� ���������� �� ��������� Region, City, District, Subway, Street. ��� ����������� ���������� ����������� ������������ ������� Address. � 25.11.2019 �������� Region, City, District, Subway, Street ���������� �������������� � XML-�����.';
$MESS[$strMessPrefix.'FIELD_DISTANCE_TO_CITY_NAME'] = '���������� �� ������, � ��';
	$MESS[$strMessPrefix.'FIELD_DISTANCE_TO_CITY_DESC'] = '���������� �� ������, � �� � ����� �����.<br/><br/>
����������: ���� ������ ��������� � ����� ������, ��:
<ul>
	<li>����� ��������� �������� "0";</li>
	<li>���� � ������ ���� �����, �� ����� ����������� ������� ��������� ������� ����� (���� Subway);</li>
	<li>���� �� <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">�����������</a> ������� � ������ ���� ������, �� ����� ������� ����� � ������������ �� ���������� ����������� (���� District).</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DIRECTION_ROAD_NAME'] = '����������� �� ������';
	$MESS[$strMessPrefix.'FIELD_DIRECTION_ROAD_DESC'] = '����������� �� ������ � � ������������ �� ���������� <a href="http://autoload.avito.ru/format/Locations.xml" target="_blank">�����������</a>.<br/><br/>
<b>�����������, ���� � ����������� ��� ������ ������� �����������</b>.<br/><br>
<b>����������� ��� �������� �� � ����� ������</b>.';
#
$MESS[$strMessPrefix.'FIELD_CATEGORY_NAME'] = '��������� ������� ������������';
	$MESS[$strMessPrefix.'FIELD_CATEGORY_DESC'] = '��������� ������� ������������ � ���� �� �������� ������:<br/>
<ul>
	<li>��������,</li>
	<li>�������,</li>
	<li>����, ����, ��������,</li>
	<li>��������� �������,</li>
	<li>������ � �����������,</li>
	<li>������������ ������������,</li>
	<li>������������ �� �������.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OPERATION_TYPE_NAME'] = '��� ����������';
	$MESS[$strMessPrefix.'FIELD_OPERATION_TYPE_DESC'] = '��� ���������� � ���� �� �������� ������:
<ul>
	<li>������,</li>
	<li>����.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_COUNTRY_NAME'] = '������';
	$MESS[$strMessPrefix.'FIELD_COUNTRY_DESC'] = '������, � ������� ��������� ������ ���������� � � ������������ �� ���������� �� <a href="http://autoload.avito.ru/format/Countries.xml" target="_blank">�����������</a>.';
$MESS[$strMessPrefix.'FIELD_TITLE_NAME'] = '�������� ���������� (������ ��� ������������ ������������)';
	$MESS[$strMessPrefix.'FIELD_TITLE_DESC'] = '�������� ���������� ����������� �������������, ������ �� ��������� ���������� �������.<br/><br/>
������ � ��������� ������������� ������������� ��������� ����� ������ ��������������. � ��������� ���������� ��������� ������ ��� ������� � �������� ���������. �������� ����, ����, ������������ ��������, ���������� ����������, ������ ����� ��� ������ ����� ������� / ������ �������� <a href="https://support.avito.ru/hc/ru/articles/200026888" target="_blank">������� �����</a>.';
$MESS[$strMessPrefix.'FIELD_PRICE_NAME'] = '���� � ������';
	$MESS[$strMessPrefix.'FIELD_PRICE_DESC'] = '���� � ������ � ����������� �� ���� ���������� � ����� �����:
<ul>
	<li>������ � ���. �� ��;</li>
	<li>���� � � ����������� �� ����� ������:
		<ul>
			<li>�� ���������� ���� � ���. � ����� �� ���� ������;</li>
			<li>��������� � ���. �� �����.</li>
		</ul>
</ul>';
$MESS[$strMessPrefix.'FIELD_PRICE_TYPE_NAME'] = '������� ������� ����';
	$MESS[$strMessPrefix.'FIELD_PRICE_TYPE_DESC'] = '������� ������� ���� � ���� �� �������� ������:<br/>
<ul>
<li>������ � ���.;
	<ul>
		<li>�� �� � �������� �� ���������,</li>
		<li>�� �<sup>2</sup></li>
	</ul>
<li>���� � ���.:
	<ul>
		<li>� ����� � �������� �� ���������,</li>
		<li>� ����� �� �<sup>2</sup>,</li>
		<li>� ���,</li>
		<li>� ��� �� �<sup>2</sup>.</li>
	</ul>
</ul>
';
$MESS[$strMessPrefix.'FIELD_ROOMS_NAME'] = '���������� ������ � ��������';
	$MESS[$strMessPrefix.'FIELD_ROOMS_DESC'] = '���������� ������ � �������� � ����� ����� ��� ����� "������".';
$MESS[$strMessPrefix.'FIELD_SQUARE_NAME'] = '����� ������� �������';
	$MESS[$strMessPrefix.'FIELD_SQUARE_DESC'] = '����� ������� ������� ������������, ������������ �� �������, � ��. ������ � ���������� �����.<br/><br/>
����������: ��� ��������� "����, ����, ��������" ����� ����� ��������� ������� ����, ������� ������� ����������� � ���� LandArea.';
$MESS[$strMessPrefix.'FIELD_KITCHEN_SPACE_NAME'] = '������� �����';
	$MESS[$strMessPrefix.'FIELD_KITCHEN_SPACE_DESC'] = '������� �����, � ��. ������ � ���������� �����.';
$MESS[$strMessPrefix.'FIELD_LIVING_SPACE_NAME'] = '����� �������';
	$MESS[$strMessPrefix.'FIELD_LIVING_SPACE_DESC'] = '����� �������, � ��. ������ � ���������� �����.';
$MESS[$strMessPrefix.'FIELD_LAND_AREA_NAME'] = '������� �������';
	$MESS[$strMessPrefix.'FIELD_LAND_AREA_DESC'] = '������� �������, � ������ � ���������� �����.';
$MESS[$strMessPrefix.'FIELD_FLOOR_NAME'] = '����';
	$MESS[$strMessPrefix.'FIELD_FLOOR_DESC'] = '����, �� ������� ��������� ������ � ����� �����.';
$MESS[$strMessPrefix.'FIELD_FLOORS_NAME'] = '���������� ������ � ����';
	$MESS[$strMessPrefix.'FIELD_FLOORS_DESC'] = '���������� ������ � ���� � ����� �����.';
$MESS[$strMessPrefix.'FIELD_HOUSE_TYPE_NAME'] = '��� ����';
	$MESS[$strMessPrefix.'FIELD_HOUSE_TYPE_DESC'] = '��� ���� � ���� �� �������� ������:
<ul>
	<li>���������,</li>
	<li>���������,</li>
	<li>�������,</li>
	<li>����������,</li>
	<li>����������.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_WALLS_TYPE_NAME'] = '�������� ����';
	$MESS[$strMessPrefix.'FIELD_WALLS_TYPE_DESC'] = '�������� ���� � ���� �� �������� ������:
<ul>
	<li>������,</li>
	<li>����,</li>
	<li>������,</li>
	<li>���������,</li>
	<li>������,</li>
	<li>���������,</li>
	<li>�������-������,</li>
	<li>�/� ������,</li>
	<li>����������������� ���������.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_MARKET_TYPE_NAME'] = '��������/�����������';
	$MESS[$strMessPrefix.'FIELD_MARKET_TYPE_DESC'] = '�������������� �������� � ����� � ���� �� �������� ������:
<ul>
	<li>��������,</li>
	<li>�����������.</li>
</ul>
<b>����������� ��� ���� (OperationType) "������"</b>.';
$MESS[$strMessPrefix.'FIELD_NEW_DEVELOPMENT_ID_NAME'] = '������ �����������';
	$MESS[$strMessPrefix.'FIELD_NEW_DEVELOPMENT_ID_DESC'] = '������ ����������� � ID ������� �� <a href="https://autoload.avito.ru/format/New_developments.xml" target="_blank">XML-�����������</a>:
<ul>
	<li>���� � ����� ��������� ���������� ���� �������, �� ����������� ID ������� (�������� Housing);</li>
	<li>���� �������� ���, �� ID ������ ��������� (�������� Object).</li>
</ul>
���� ����� ������� NewDevelopmentId, �� �������� ���� Street � �������������� ��������� ������� �� ����������� ����������� �����.<br/><br/>
�����: ���� � ����� ����������� ��� ������� ��� ������� ��� �� ����� � ��� ������, �� ��������� �� ������ <a href="mailto:newdevelopments@avito.ru">newdevelopments@avito.ru</a> � ��������� ������ �� ����, ��� ���� ��������� ������������:<br/>
<ul>
	<li>��� ���: ���������� �� ������������� ����, ���������� ������������� ������� � ������������ ����������� � ��������� ���������� 214-�� (��� �������� ������� ����� 01.01.2017), ��������� ����������;</li>
	<li>��� ���: ���������� �� ������������� ���� � ����� �����������).</li>
</ul>
<b>����������� ��� ���� "�����������"</b>.';
$MESS[$strMessPrefix.'FIELD_PROPERTY_RIGHTS_NAME'] = '����� �������������';
	$MESS[$strMessPrefix.'FIELD_PROPERTY_RIGHTS_DESC'] = '����� ������������� � ���� �� �������� ������:
<ul>
	<li>�����������;</li>
	<li>���������;</li>
	<li>���������� (�������� ������ � ��������� "��������. ������. �����������").</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_OBJECT_TYPE_NAME'] = '��� �������';
	$MESS[$strMessPrefix.'FIELD_OBJECT_TYPE_DESC'] = '��� ������� � ���� �� �������� ������ (�������� ��� ������ ���������):<br/>
<ul>
	<li>
		<b>����, ����, ��������:</b>
		<ul>
			<li>���,</li>
			<li>����,</li>
			<li>�������,</li>
			<li>��������;</li>
		</ul>
	</li>
	<li>
		<b>��������� �������:</b>
		<ul>
			<li>��������� (���),</li>
			<li>����������������� (���, ���),</li>
			<li>��������������;</li>
		</ul>
	</li>
	<li>
		<b>������ � �����������:</b>
		<ul>
			<li>�����,</li>
			<li>�����������;</li>
		</ul>
	</li>
	<li>
		<b>������������ ������������:</b>
		<ul>
			<li>���������,</li>
			<li>������� ���������,</li>
			<li>��������� ������������� �������,</li>
			<li>��������� ���������� ����������,</li>
			<li>���������������� ���������,</li>
			<li>��������� ���������</li>
			<li>�������� ���������;</li>
		</ul>
	</li>
	<li>
		<b>������������ �� �������:</b>
		<ul>
			<li>��������, �����������,</li>
			<li>���, �����,</li>
			<li>��������� �������,</li>
			<li>�����, �����������,</li>
			<li>������������ ������������.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ADDITIONAL_OBJECT_TYPES_NAME'] = '��� ������� - �������������� ��������';
	$MESS[$strMessPrefix.'FIELD_ADDITIONAL_OBJECT_TYPES_DESC'] = '��� ������� - �������������� ��������. ��������� �������� (�� ����� 2) � ����������� �� ���������:
<ul>
	<li>
		��������� ���������� ����������
		<ul>
			<li>����</li>
			<li>�������� ���������</li>
			<li>������������</li>
			<li>�����</li>
			<li>�������</li>
			<li>���������</li>
		</ul>
	</li>
	<li>
		�������� ���������
		<ul>
			<li>����</li>
			<li>�������</li>
		</ul>
	</li>
	<li>
		������� ���������
		<ul>
			<li>�������� ���������</li>
		</ul>
	</li>
	<li>
		��������� ���������
		<ul>
			<li>������������</li>
		</ul>
	</li>
	<li>
		���������������� ���������
		<ul>
			<li>�����</li>
		</ul>
	</li>
</ul>
<b>3� � ����������� ��������� �������� ����� ���������������.</b>';
$MESS[$strMessPrefix.'FIELD_OBJECT_SUBTYPE_NAME'] = '������ �������';
	$MESS[$strMessPrefix.'FIELD_OBJECT_SUBTYPE_DESC'] = '������ ������� � ���� �� �������� ������ (�������� ��� ������� ����):<br/>
<ul>
	<li>
		<b>�����:</b>
		<ul>
			<li>��������������,</li>
			<li>���������,</li>
			<li>�������������;</li>
		</ul>
	</li>
	<li>
		<b>�����������:</b>
		<ul>
			<li>�������������� �������,</li>
			<li>��������� �������,</li>
			<li>������ �������,</li>
			<li>�������� �������.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SECURED_NAME'] = '������ �������';
	$MESS[$strMessPrefix.'FIELD_SECURED_DESC'] = '������ ������� � ���� �� �������� ������:<br/>
<ul>
	<li>��,</li>
	<li>���.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BUILDING_CLASS_NAME'] = '����� ������';
	$MESS[$strMessPrefix.'FIELD_BUILDING_CLASS_DESC'] = '����� ������ (������ ��� ����� ������� "������� ���������" � "��������� ���������") � ���� �� �������� ������:
<ul>
	<li>A,</li>
	<li>B,</li>
	<li>C,</li>
	<li>D.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CADASTRAL_NUMBER_NAME'] = '����������� �����';
	$MESS[$strMessPrefix.'FIELD_CADASTRAL_NUMBER_DESC'] = '����������� ����� � ������.<br/><br/>
����������: �� ������������ � ���������� ���������.';
$MESS[$strMessPrefix.'FIELD_DECORATION_NAME'] = '������� ���������';
	$MESS[$strMessPrefix.'FIELD_DECORATION_DESC'] = '������� ��������� (������ ��� ����� ������� (MarketType) "�����������"). ��������� �������� ���������:
<ul>
	<li>"��� �������"</li>
	<li>"��������"</li>
	<li>"��������"</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SAFE_DEMONSTRATION_NAME'] = '������ �����';
	$MESS[$strMessPrefix.'FIELD_SAFE_DEMONSTRATION_DESC'] = '������ ����� � ���� �� �������� ������:
<ul>
	<li>���� ��������</li>
	<li>�� ����</li>
</ul>
<b>�����</b>: ������ ������� �� �������������� � ��������������� ������/��������� (��� ���� ��������� ���������) � �������/����������� (��� ��������� ��������).';
$MESS[$strMessPrefix.'FIELD_APARTMENT_NUMBER_NAME'] = '����� ��������';
	$MESS[$strMessPrefix.'FIELD_APARTMENT_NUMBER_DESC'] = '����� �������� - ������, ���������� �� 1 �� 10 ��������.';
$MESS[$strMessPrefix.'FIELD_STATUS_NAME'] = '������ ������������';
	$MESS[$strMessPrefix.'FIELD_STATUS_DESC'] = '������ ������������ � ���� �� �������� ������:
<ul>
	<li>��������</li>
	<li>�����������</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_NAME'] = '������ ��� ������';
	$MESS[$strMessPrefix.'FIELD_BALCONY_OR_LOGGIA_DESC'] = '������ ��� ������ � ���� �� �������� ������:
<ul>
	<li>������</li>
	<li>������</li>
	<li>���</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_VIEW_FROM_WINDOWS_NAME'] = '��� �� ����';
	$MESS[$strMessPrefix.'FIELD_VIEW_FROM_WINDOWS_DESC'] = '��� �� ���� � ���� ��� ����� �������� �� ������:
<ul>
	<li>�� �����</li>
	<li>�� ����</li>
</ul>
���� ������ ����������� ��� �������������!';
$MESS[$strMessPrefix.'FIELD_BUILT_YEAR_NAME'] = '��� ���������';
	$MESS[$strMessPrefix.'FIELD_BUILT_YEAR_DESC'] = '��� ��������� (������ ��� ����� ������� (MarketType) "��������") -  ����� �����.';
$MESS[$strMessPrefix.'FIELD_PASSENGER_ELEVATOR_NAME'] = '������������ ����';
	$MESS[$strMessPrefix.'FIELD_PASSENGER_ELEVATOR_DESC'] = '������������ ���� � ���� �� �������� ������:
<ul>
	<li>���</li>
	<li>1</li>
	<li>2</li>
	<li>3</li>
	<li>4</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_FREIGHT_ELEVATOR_NAME'] = '�������� ����';
	$MESS[$strMessPrefix.'FIELD_FREIGHT_ELEVATOR_DESC'] = '������������ ���� � ���� �� �������� ������:
<ul>
	<li>���</li>
	<li>1</li>
	<li>2</li>
	<li>3</li>
	<li>4</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_IN_HOUSE_NAME'] = '� ����';
	$MESS[$strMessPrefix.'FIELD_IN_HOUSE_DESC'] = '� ���� (������ ��� ����� ������� (MarketType) "��������") - ��������� �������� � ���������� ���������� �� ������:
<ul>
	<li>��������</li>
	<li>������������</li>
	<li>�������������</li>
</ul>
���� ������ ����������� ��� �������������!';
$MESS[$strMessPrefix.'FIELD_COURTYARD_NAME'] = '����';
	$MESS[$strMessPrefix.'FIELD_COURTYARD_DESC'] = '���� - ��������� �������� � ���������� ���������� �� ������:
<ul>
	<li>�������� ����������</li>
	<li>������� ��������</li>
	<li>���������� ��������</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_PARKING_NAME'] = '��������';
	$MESS[$strMessPrefix.'FIELD_PARKING_DESC'] = '�������� - ��������� �������� � ���������� ���������� �� ������:
<ul>
	<li>���������</li>
	<li>�������� ��������������</li>
	<li>�� ���������� �� �����</li>
	<li>�������� �� �����</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_CEILING_HEIGHT_NAME'] = '������ ��������, �';
	$MESS[$strMessPrefix.'FIELD_CEILING_HEIGHT_DESC'] = '������ ��������, � ������  � ���������� �����.';
$MESS[$strMessPrefix.'FIELD_RENOVATION_NAME'] = '������';
	$MESS[$strMessPrefix.'FIELD_RENOVATION_DESC'] = '������ (������ ��� ����� ������� (MarketType) "��������")  � ���� �� �������� ������:
<ul>
	<li>�������������</li>
	<li>����</li>
	<li>������������</li>
	<li>������� �������</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_BATHROOM_NAME'] = '�������';
	$MESS[$strMessPrefix.'FIELD_BATHROOM_DESC'] = '������� � ���� �� �������� ������:
<ul>
	<li>�����������</li>
	<li>����������</li>
	<li>���������</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_SSADDITIONALLY_NAME'] = '������������� (��� ��������)';
	$MESS[$strMessPrefix.'FIELD_SSADDITIONALLY_DESC'] = '������������� (������ ��� ����� ������� (MarketType) "��������") - ��������� �������� � ���������� ���������� �� ������:
<ul>
	<li>������</li>
	<li>������� �������</li>
	<li>�����������</li>
	<li>�����������</li>
	<li>���������� ����</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_NDADDITIONALLY_NAME'] = '������������� (��� ����������)';
	$MESS[$strMessPrefix.'FIELD_NDADDITIONALLY_DESC'] = '������������� (������ ��� ����� ������� (MarketType) "�����������") - ��������� �������� � ���������� ���������� �� ������:
<ul>
	<li>�����������</li>
	<li>���������� ����</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_DEALTYPE_NAME'] = '��� ������';
	$MESS[$strMessPrefix.'FIELD_DEALTYPE_DESC'] = '��� ������ � ���� �� �������� ������:
<ul>
	<li>������ �������</li>
	<li>��������������</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_ROOMTYPE_NAME'] = '��� ������';
	$MESS[$strMessPrefix.'FIELD_ROOMTYPE_DESC'] = '��� ������ - ��������� �������� � ���������� ���������� �� ������:
<ul>
	<li>�������</li>
	<li>�������������</li>
</ul>';


$MESS[$strMessPrefix.'FIELD_LEASE_TYPE_NAME'] = '��� ������';
	$MESS[$strMessPrefix.'FIELD_LEASE_TYPE_DESC'] = '��� ������ � ���� �� �������� ������:<br/>
<ul>
	<li>�� ���������� ����,</li>
	<li>���������.</li>
</ul>
<b>����������� ��� ���� "����"</b>.';
$MESS[$strMessPrefix.'FIELD_LEASE_BEDS_NAME'] = '���������� ��������.';
	$MESS[$strMessPrefix.'FIELD_LEASE_BEDS_DESC'] = '���������� �������� (������ ��� ������) � ����� �����.';
$MESS[$strMessPrefix.'FIELD_LEASE_SLEEPING_PLACES_NAME'] = '���������� �������� ����';
	$MESS[$strMessPrefix.'FIELD_LEASE_SLEEPING_PLACES_DESC'] = '���������� �������� ���� (������ ��� ������) � ����� �����.';
$MESS[$strMessPrefix.'FIELD_LEASE_MULTIMEDIA_NAME'] = '����� "�����������"';
	$MESS[$strMessPrefix.'FIELD_LEASE_MULTIMEDIA_DESC'] = '����� "�����������" (������ ��� ������) � ��������� �������� &lt;Option&gt; � ���������� ���������� �� ������:<br/>
<ul>
	<li>Wi-Fi,</li>
	<li>���������,</li>
	<li>��������� / �������� ��.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_APPLIANCES_NAME'] = '����� "������� �������"';
	$MESS[$strMessPrefix.'FIELD_LEASE_APPLIANCES_DESC'] = '����� "������� �������" (������ ��� ������) � ��������� �������� &lt;Option&gt; � ���������� ���������� �� ������:<br/>
<ul>
	<li>�����,</li>
	<li>�������������,</li>
	<li>�����������,</li>
	<li>���������� ������,</li>
	<li>���,</li>
	<li>����.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_NAME'] = '����� "�������"';
	$MESS[$strMessPrefix.'FIELD_LEASE_COMFORT_DESC'] = '����� "�������" (������ ��� ������) � ��������� �������� &lt;Option&gt; � ���������� ���������� �� ������:<br/>
<ul>
	<li>�����������,</li>
	<li>�����,</li>
	<li>������ � ���������� "��������" � "�������":<br/>
		<ul>
			<li>������ / ������,</li>
			<li>����������� �����;</li>
		</ul>
	</li>
	<li>������ � ��������� "����, ����, ��������":<br/>
		<ul>
			<li>�������,</li>
			<li>���� / �����.</li>
		</ul>
	</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_ADDITIONALLY_NAME'] = '����� "�������������"';
	$MESS[$strMessPrefix.'FIELD_LEASE_ADDITIONALLY_DESC'] = '����� "�������������" (������ ��� ������) � ��������� �������� &lt;Option&gt; � ���������� ���������� �� ������:<br/>
<ul>
	<li>����� � ���������,</li>
	<li>����� � ������,</li>
	<li>����� ��� �����������,</li>
	<li>����� ������.</li>
</ul>';
$MESS[$strMessPrefix.'FIELD_LEASE_COMMISSION_SIZE_NAME'] = '������ �������� � %';
	$MESS[$strMessPrefix.'FIELD_LEASE_COMMISSION_SIZE_DESC'] = '������ �������� � % � ����� �����.<br/><br/>
	<b>����������� ��� ������������ ������ � ������ ����� ������������� "���������"</b>.';
$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_NAME'] = '�����';
	$MESS[$strMessPrefix.'FIELD_LEASE_DEPOSIT_DESC'] = '����� � ���� �� �������� ������:
<ul>
	<li>��� ������,</li>
	<li>0,5 ������,</li>
	<li>1 �����,</li>
	<li>1,5 ������,</li>
	<li>2 ������,</li>
	<li>2,5 ������,</li>
	<li>3 ������.</li>
</ul>
<b>����������� ��� ������������ ������</b>.';


?>