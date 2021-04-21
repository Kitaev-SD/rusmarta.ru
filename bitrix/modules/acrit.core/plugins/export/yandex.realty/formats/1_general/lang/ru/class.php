<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = '������.������������';

$MESS['YANDEX_REALTY_BOOLEAN'] = '������ ������������ ��������:
<ul>
	<li>���/����,</li>
	<li>�true�/�false�,</li>
	<li>�1�/�0�,</li>
	<li>�+�/�-�.</li>
</ul>';
$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'] = '<ul style="column-count:3;">
	<li>���������� ������������</li>
	<li>���������� ����</li>
	<li>���������� ���� (������)</li>
	<li>���������� ���������</li>
	<li>���������� �������</li>
	<li>��������� ����</li>
	<li>������������ �������</li>
	<li>�������� �������</li>
	<li>������������ �������</li>
	<li>������������� �������</li>
	<li>����������� �������</li>
	<li>����������� �������</li>
	<li>��������������� �������</li>
	<li>��������� �������</li>
	<li>����������� �������</li>
	<li>��������� �������</li>
	<li>����������� �������</li>
	<li>������������� ����</li>
	<li>������������ ����</li>
	<li>������������� �������</li>
	<li>���������� �������</li>
	<li>������������� �������</li>
	<li>������������� �������</li>
	<li>������ �������</li>
	<li>��������� �������</li>
	<li>���������� �������</li>
	<li>�������� ����</li>
	<li>���������� ����</li>
	<li>���������� �������</li>
	<li>��������� �������</li>
	<li>��������� �������</li>
	<li>����������� �������</li>
	<li>������������ �������</li>
	<li>�������������� ����</li>
	<li>�������� �������</li>
	<li>�������� �������</li>
	<li>��������� �������</li>
	<li>����������� �������</li>
	<li>����������� ����</li>
	<li>����������� �������</li>
	<li>����������� �������</li>
</ul>';

// Fields: General
$MESS[$strHead.'HEADER_GENERAL'] = '����� ���������� �� ����������';
$MESS[$strName.'@internal-id'] = '������������� ����������';
	$MESS[$strHint.'@internal-id'] = '������������� ����������. ������ ���� ���������� ��� ������� ����������.';
$MESS[$strName.'type'] = '��� ������';
	$MESS[$strHint.'type'] = '������ ������������ ��������: ��������, �������.';
$MESS[$strName.'property-type'] = '��� ������������';
	$MESS[$strHint.'property-type'] = '������ ������������ ��������: �������/�living�.';
$MESS[$strName.'category'] = '��������� �������';
	$MESS[$strHint.'category'] = '��������� ��������:
<ul>
	<li>�����/��������/�cottage�</li>
	<li>����/�house�</li>
	<li>���� � ��������/�house with lot�</li>
	<li>��������/�lot�</li>
	<li>������ ����</li>
	<li>���������/�flat�</li>
	<li>��������/�room�</li>
	<li>���������/�townhouse�</li>
	<li>��������/�duplex�</li>
	<li>������/�garage�.</li>
</ul>';
$MESS[$strName.'lot-number'] = '����� ����';
	$MESS[$strHint.'lot-number'] = '����� ����.';
$MESS[$strName.'cadastral-number'] = '����������� ����� ������� ������������.';
	$MESS[$strHint.'cadastral-number'] = '����������� ����� ������� ������������.';
$MESS[$strName.'url'] = 'URL �������� � �����������';
	$MESS[$strHint.'url'] = 'URL �������� � �����������.';
$MESS[$strName.'creation-date'] = '���� �������� ����������';
	$MESS[$strHint.'creation-date'] = '����������� � ������� YYYY-MM-DDTHH:mm:ss+00:00.';
$MESS[$strName.'last-update-date'] = '���� ���������� ���������� ����������';
	$MESS[$strHint.'last-update-date'] = '����������� � ������� YYYY-MM-DDTHH:mm:ss+00:00.';
$MESS[$strName.'vas'] = '�������������� ����� �� ����������� ����������';
	$MESS[$strHint.'vas'] = '������� �����������, ���� � ���������� ������ ���� ��������� �������������� �����.<br/><br/>
��������� ��������:
<ul>
	<li>�premium�</li>
	<li>�raise�</li>
	<li>�promotion�</li>
</ul>
��� �������� �raise� (���������) ����� ��������� ���������� ��������������� � ������������ �����. ��� ����� ������ vas ������� ������� start-time, ���� � ����� � ������� YYYY-MM-DDTHH:mm:ss+00:00 � �������� raise.<br/><br/>
������ ���� ������� ���� ������� ���������� �����. ��������� ������� �� �����. ����������, � �������� ��������� �����, ����� ����������� ��������� � ��������� �����.<br/><br/>
<b>��������.</b> ����� ������ ���������� � ����������� ��� ����������.';

// Fields: Location
$MESS[$strHead.'HEADER_LOCATION'] = '�������������� �������';
$MESS[$strName.'location.country'] = '������, � ������� ���������� ������';
	$MESS[$strHint.'location.country'] = '����������. � ��������� ����� ���������� ����������� ������ ��� �������� ������������, ������������� � ������.';
$MESS[$strName.'location.region'] = '�������� �������� ��';
	$MESS[$strHint.'location.region'] = '�������������� ������� ��� �������� � ������ � �����-����������.';
$MESS[$strName.'location.district'] = '�������� ������ �������� ��';
	$MESS[$strHint.'location.district'] = '�������� ������ �������� ��.';
$MESS[$strName.'location.locality-name'] = '�������� ����������� ������';
	$MESS[$strHint.'location.locality-name'] = '�������� ����������� ������.';
$MESS[$strName.'location.sub-locality-name'] = '����� ����������� ������';
	$MESS[$strHint.'location.sub-locality-name'] = '����� ����������� ������.';
$MESS[$strName.'location.address'] = '����� ������� (����� � ����� ������)';
	$MESS[$strHint.'location.address'] = '��� ���������� ������������ ����� ���� ��������� �������������.';
$MESS[$strName.'location.apartment'] = '����� ��������';
	$MESS[$strHint.'location.apartment'] = '����� ��������.';
$MESS[$strName.'location.direction'] = '�����';
	$MESS[$strHint.'location.direction'] = '������� ���������� ������ ��� �������� � ������ � ���������� �������.';
$MESS[$strName.'location.distance'] = '���������� �� ����� �� ����';
	$MESS[$strHint.'location.distance'] = '�������� ����������� � ����������.<br/><br/>
	������� ���������� ������ ��� �������� � ������ � ���������� �������.';
$MESS[$strName.'location.latitude'] = '�������������� ������';
	$MESS[$strHint.'location.latitude'] = '�������������� ������.';
$MESS[$strName.'location.longitude'] = '�������������� �������';
	$MESS[$strHint.'location.longitude'] = '�������������� �������.';
$MESS[$strName.'location.metro.name'] = '�������� ������� �����';
	$MESS[$strHint.'location.metro.name'] = '�������� ������� �����.';
$MESS[$strName.'location.metro.time-on-transport'] = '����� �� ����� � ������� �� ����������';
	$MESS[$strHint.'location.metro.time-on-transport'] = '����� �� ����� � ������� �� ����������.';
$MESS[$strName.'location.metro.time-on-foot'] = '����� �� ����� � ������� ������';
	$MESS[$strHint.'location.metro.time-on-foot'] = '����� �� ����� � ������� ������.';
$MESS[$strName.'location.railway-station'] = '��������� ��������������� �������';
	$MESS[$strHint.'location.railway-station'] = '������� ����������� ������ ��� ���������� ������������.';

# Object general
$MESS[$strHead.'HEADER_OBJECT_GENERAL'] = '����� ���������� �� �������';

# Fields: Terms
$MESS[$strHead.'HEADER_TERMS'] = '���������� �� �������� ������';
$MESS[$strName.'price.value'] = '����';
	$MESS[$strHint.'price.value'] = '����� ����������� ��� ��������.<br/><br/>
���� ������ �������� ��� (���� �� ����) � ���������� ���������������� ������� (��� ������������ ������������).';
$MESS[$strName.'price.currency'] = '������, � ������� ������� ����';
	$MESS[$strHint.'price.currency'] = '���� ����������� ������� ���������� ������ � ��� ������, ������� ������� � ����������.<br/><br/>
��������� ��������:
<ul>
	<li>�RUR� ��� �RUB� (���������� �����)</li>
	<li>�EUR� (����)</li>
	<li>�USD� (������������ ������).</li>
</ul>';
$MESS[$strName.'price.period'] = '������ ��� ������� ��������� ������';
	$MESS[$strHint.'price.period'] = '������� ������������ ������ ��� ���������� �� ������.<br/><br/>
��������� ��������:
<ul>
	<li>������/�day�</li>
	<li>�������/�month�</li>
</ul>';
$MESS[$strName.'price.unit'] = '������� ������� ��������� ��� �������';
	$MESS[$strHint.'price.unit'] = '�������� ����� ����������, ���� ���� ������� �� ������� �������.<br/><br/>
��������� ��������:
<ul>
	<li>���. �/�sq. m�</li>
	<li>�c����</li>
	<li>�������/�hectare�.</li>
</ul>';
$MESS[$strName.'rent-pledge'] = '�����';
	$MESS[$strHint.'rent-pledge'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'deal-status'] = '��� ������';
	$MESS[$strHint.'deal-status'] = '���� ������� �����������, ��� ���������� �������� � ������������ ��������� ���������� �� �����������.<br/><br/>
��������� ��������:
<ul>
	<li>���������� �������/�������� �� �����������,</li>
	<li>������������/�reassignment�.</li>
</ul>
��������� �������� ��� ��������� ������������:
<ul>
	<li>������� �������/�sale�,</li>
	<li>���������� ������� ��������/�primary sale of secondary�,</li>
	<li>���������� �������/�countersale�</li>.
</ul>';
$MESS[$strName.'haggle'] = '����';
	$MESS[$strHint.'haggle'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'mortgage'] = '�������';
	$MESS[$strHint.'mortgage'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'prepayment'] = '����������';
	$MESS[$strHint.'prepayment'] = '����������� �������� �������� � ��������� ��� ����� �%�.<br/><br/>
������������ �������� � 100.';
$MESS[$strName.'agent-fee'] = '�������� ������';
	$MESS[$strHint.'agent-fee'] = '����������� �������� �������� � ��������� ��� ����� �%�.';
$MESS[$strName.'not-for-agents'] = '������� �������� ������� �� ��������';
	$MESS[$strHint.'not-for-agents'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'utilities-included'] = '������������ ������ �������� � ��������� � �������� ������';
	$MESS[$strHint.'utilities-included'] = $MESS['YANDEX_REALTY_BOOLEAN'];


# Fields: Object info
$MESS[$strHead.'HEADER_OBJECT_INFO'] = '���������� �� �������';
$MESS[$strName.'area.value'] = '����� ������� (�����)';
	$MESS[$strHint.'area.value'] = '������������ ������� ��� ���� ����������, ����� ��������, ��� ������ ����� ������������ lot-type';
$MESS[$strName.'area.unit'] = '����� ������� (��. ���.)';
	$MESS[$strHint.'area.unit'] = '������������ ������� ��� ���� ����������, ����� ��������, ��� ������ ����� ������������ lot-type';
$MESS[$strName.'image'] = '����������';
	$MESS[$strHint.'image'] = '������������ ������� ��� ���������� � ����� ������������ � ������.<br/><br/>
	����� ���� ��������� �����. <b>���������� ���������� ������� ���������� ������ ����� image</b>.<br/><br/>
	�� ������� ���������� �����������, �� ������� ������� ��������� � ������� (��������, �������� ��� ���������� �����������).';
$MESS[$strName.'renovation'] = '������';
	$MESS[$strHint.'renovation'] = '��������� ��������:
<ul>
	<li>�������������,</li>
	<li>�����,</li>
	<li>�� ��������,</li>
	<li>�������� �������,</li>
	<li>��������,</li>
	<li>���������� ������,</li>
	<li>��������� �������.</li>
</ul>';
$MESS[$strName.'quality'] = '��������� �������';
	$MESS[$strHint.'quality'] = '��������� ��������:
<ul>
<li>���������,</li>
<li>��������,</li>
<li>�����������,</li>
<li>�������</li>.
</ul>';
$MESS[$strName.'description'] = '��������� �������� ����������';
	$MESS[$strHint.'description'] = '�������� � ��������� �����.';

// Defaults
$MESS[$strLang.'_boolean_default_y'] = '��';
$MESS[$strLang.'_boolean_default_n'] = '���';
$MESS[$strLang.'property-type_default'] = '�����';
$MESS[$strLang.'area_unit_default'] = '��. �';
$MESS[$strLang.'location.country_default'] = '������';
$MESS[$strLang.'sales-agent.category_default'] = '���������';

// Fields: object info
$MESS[$strName.'room-space.value'] = '������� ������� (�����)';
	$MESS[$strHint.'room-space.value'] = '���������� ������������ ��������� ������ ��������������� ���������� ������.<br/><br/>
	������������ ������� ��� ������� ��� ������ �������.<br/><br/>
	������� �� ������������ ��� ������.<br/><br/>
	������� �� ������������ ��� �������� �� ��������� �����������.';
$MESS[$strName.'room-space.unit'] = '������� ������� (��. ���.)';
	$MESS[$strHint.'room-space.unit'] = '���������� ������������ ��������� ������ ��������������� ���������� ������.<br/><br/>
	������������ ������� ��� ������� ��� ������ �������.<br/><br/>
	������� �� ������������ ��� ������.<br/><br/>
	������� �� ������������ ��� �������� �� ��������� �����������.';
$MESS[$strName.'living-space.value'] = '����� ������� (�����)';
	$MESS[$strHint.'living-space.value'] = '��� ������� � ����� ������� ����������� ������� �������.<br/><br/>
	�� ��������� ����� ����������� ��������� ����������.';
$MESS[$strName.'living-space.unit'] = '����� ������� (��. ���.)';
	$MESS[$strHint.'living-space.unit'] = '��� ������� � ����� ������� ����������� ������� �������.<br/><br/>
	�� ��������� ����� ����������� ��������� ����������.';
$MESS[$strName.'kitchen-space.value'] = '������� ����� (�����)';
	$MESS[$strHint.'kitchen-space.value'] = '������� �����.';
$MESS[$strName.'kitchen-space.unit'] = '������� ����� (��. ���.)';
	$MESS[$strHint.'kitchen-space.unit'] = '������� �����.';
	
// Fields: additional
$MESS[$strHead.'HEADER_OBJECT_ADDITIONAL'] = '�������������� ���������� �� �������';
$MESS[$strName.'rooms'] = '����� ���������� ������';
	$MESS[$strHint.'rooms'] = '��� ��������� ���������� ���������� ������ ����������� �������� �������� �������.<br/><br/>
������� �� ������������ ��� ������.';
$MESS[$strName.'rooms-offered'] = '���������� ������, ����������� � ������ (��� �����)';
	$MESS[$strHint.'rooms-offered'] = '������� �� ������������ ��� ������.<br/><br/>
������� �� ������������ ��� �������� �� ��������� �����������.';
$MESS[$strName.'floor'] = '����';
	$MESS[$strHint.'floor'] = '������������ ������� ��� �������� ������������';
$MESS[$strName.'new-flat'] = '������� �����������';
	$MESS[$strHint.'new-flat'] = '������ ������������ ��������: ���, �true�, �1�, �+�.';
$MESS[$strName.'apartments'] = '�����������';
	$MESS[$strHint.'apartments'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'studio'] = '������';
	$MESS[$strHint.'studio'] = '������� ������������ ������ ��� ���������� � ������� � ������ ��������.<br/><br/>
������ ������������ ��������: ���, �true�, �1�, �+�.<br/><br>
������� �� ������������ ��� �������� �� ��������� �����������.';
$MESS[$strName.'open-plan'] = '��������� ����������';
	$MESS[$strHint.'open-plan'] = '������� ������������ ������ ��� ���������� � ������� � ������ ��������.<br/><br/>
������ ������������ ��������: ���, �true�, �1�, �+�.<br/><br/>
������� �� ������������ ��� ������.';
$MESS[$strName.'rooms-type'] = '��� ������';
	$MESS[$strHint.'rooms-type'] = '��������� ��������:
<ul>
	<li>��������</li>
	<li>�����������.</li>
</ul>';
$MESS[$strName.'window-view'] = '��� �� ����';
	$MESS[$strHint.'window-view'] = '��������� ��������:
<ul>
	<li>��� ����</li>
	<li>��� �����.</li>
</ul>';
$MESS[$strName.'balcony'] = '��� �������';
	$MESS[$strHint.'balcony'] = '��������� ��������:
<ul>
	<li>�������</li>
	<li>��������</li>
	<li>�2 �������</li>
	<li>�2 ������</li>
	<li>� �. �.</li>
</ul>';
$MESS[$strName.'bathroom-unit'] = '��� �������';
	$MESS[$strHint.'bathroom-unit'] = '��������� ��������:
<ul>
	<li>������������</li>
	<li>�����������</li>
	<li>�������� �������� (�������� �2�).</li>
</ul>';
$MESS[$strName.'air-conditioner'] = '������� ������� �����������������';
	$MESS[$strHint.'air-conditioner'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'phone'] = '������� ��������';
	$MESS[$strHint.'phone'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'internet'] = '������� ���������';
	$MESS[$strHint.'internet'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'room-furniture'] = '������� ������';
	$MESS[$strHint.'room-furniture'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'kitchen-furniture'] = '������� ������ �� �����';
	$MESS[$strHint.'kitchen-furniture'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'television'] = '������� ����������';
	$MESS[$strHint.'television'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'washing-machine'] = '������� ���������� ������';
	$MESS[$strHint.'washing-machine'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'dishwasher'] = '������� ������������� ������';
	$MESS[$strHint.'dishwasher'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'refrigerator'] = '������� ������������';
	$MESS[$strHint.'refrigerator'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'built-in-tech'] = '���������� �������';
	$MESS[$strHint.'built-in-tech'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'floor-covering'] = '�������� ����';
	$MESS[$strHint.'floor-covering'] = '��������� ��������:
<ul>
	<li>���������,</li>
	<li>��������,</li>
	<li>���������,</li>
	<li>�������.</li>
</ul>';
$MESS[$strName.'with-children'] = '���������� � ������';
	$MESS[$strHint.'with-children'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'with-pets'] = '���������� � ���������';
	$MESS[$strHint.'with-pets'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'entrance-type'] = '���� � ���������';
	$MESS[$strHint.'entrance-type'] = '��������� ��������:
<ul>
�common� (�����)
�separate� (���������).
</ul>';
$MESS[$strName.'phone-lines'] = '���������� ���������� �����';
	$MESS[$strHint.'phone-lines'] = '���������� ���������� �����';
$MESS[$strName.'adding-phone-on-request'] = '����������� ���������� ���������� �����';
	$MESS[$strHint.'adding-phone-on-request'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'self-selection-telecom'] = '����������� ���������������� ������ ��������� �������������������� �����';
	$MESS[$strHint.'self-selection-telecom'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'ventilation'] = '������� ����������';
	$MESS[$strHint.'ventilation'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'fire-alarm'] = '������� �������� ������������';
	$MESS[$strHint.'fire-alarm'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'electric-capacity'] = '���������� ������������� ��������';
	$MESS[$strHint.'electric-capacity'] = '����������� ����� �����. �������� ���������� � ���.';
$MESS[$strName.'window-type'] = '��� ����';
	$MESS[$strHint.'window-type'] = '��������� ��������:
<ul>
	<li>����������,</li>
	<li>����������,</li>
	<li>��������</li>.
</ul>';

#
$MESS[$strName.'yandex-building-id'] = '�� ������ ��������� � �������';
	$MESS[$strHint.'yandex-building-id'] = '�� ������ ��������� � ���� ������ �������.<br/><br/>
	� ��������� ����� ������� �������������� ��� �������� � ��������, ��������� � ������: '.$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'].'
	������������� ������ � ������ ������� � <a href="https://realty.yandex.ru/newbuildings.tsv" target="_blank">������ ��������������� yandex-building-id</a>.<br/><br/>
	������������� ������������ � ������ ��������, �� ������� ��������� �������� ������ ���������.<br/><br/>
	������� ������� ����������, ����� ���������� ��������� ������������ � ���������������� ������ ���������.';
$MESS[$strName.'yandex-house-id'] = '�� ������� ������ ��������� � ������� (��� ����������)';
	$MESS[$strHint.'yandex-house-id'] = '������������� ������� ������ ��������� � ���� ������ �������.<br/><br/>
	� ��������� ����� ������� �������������� ��� �������� � ��������, ��������� � ������: '.$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'].'
	������������� ������ � ������� ������� <a href="https://realty.yandex.ru/newbuildings.tsv" target="_blank">� ������ ��������������� yandex-building-id</a>.<br/><br/>
	������� ������� ����������, ����� ���������� ��������� ������������ � ���������������� ������� ������ ���������.';
	
#
$MESS[$strName.'office-class'] = '����� ������-������';
	$MESS[$strHint.'office-class'] = '��������� ��������: �A�, �A+�, �B�, �B+�, �C�, �C+�.';
$MESS[$strName.'building-state'] = '������ ������������� ���� (��� ����������)';
	$MESS[$strHint.'building-state'] = '������ ������������ ��������:
<ul>
	<li>�built� (��� ��������, �� �� ����),</li>
	<li>�hand-over� (���� � ������������),</li>
	<li>�unfinished� (��������).</li>
</ul>
���� �������� built-year � ready-quarter ������� � ��������� �������, ��� �������� building-state ������� ���������� �������� hand-over.';
$MESS[$strName.'building-phase'] = '������� ������������� (��� ����������)';
	$MESS[$strHint.'building-phase'] = '��������� ��������: �������� 1�, �II ��������, �3� � �. �.';
$MESS[$strName.'building-series'] = '����� ���� (��� ����������)';
	$MESS[$strHint.'building-series'] = '����� ����';

#
$MESS[$strName.'floors-total'] = '����� ���������� ������ � ����';
	$MESS[$strHint.'floors-total'] = '����� ���������� ������ � ����';
$MESS[$strName.'building-name'] = '�������� ������ ���������';
	$MESS[$strHint.'building-name'] = '� ��������� ����� ������� �������������� ������ ��� �������� � ��������, ��������� � ������: '.$MESS['YANDEX_REALTY_SUPPORTED_REGIONS'].'
	���������� ����� ������ �������� ��.';
$MESS[$strName.'building-type'] = '��� ����/������';
	$MESS[$strHint.'building-type'] = '��������� �������� ��� ����� ������������:
<ul>
	<li>��������,</li>
	<li>�����������,</li>
	<li>����������,</li>
	<li>���������-����������,</li>
	<li>��������,</li>
	<li>����������.</li>
</ul>
��������� �������� ��� ������������ ������������:
<ul>
	<li>�business center� (������-�����),</li>
	<li>�detached building� (�������� ������� ������),</li>
	<li>�residential building� (���������� ���������),</li>
	<li>�shopping center� (�������� �����),</li>
	<li>�warehouse� (��������� ��������).</li>
</ul>
��������� �������� ��� ����������:
<ul>
	<li>����������,</li>
	<li>��������,</li>
	<li>����������.</li>
</ul>
';
$MESS[$strName.'built-year'] = '��� �����/���������';
	$MESS[$strHint.'built-year'] = '������������ ������� ��� ����� (�������� ����������), ������� ���� ����� ����� 5 ��� ����� ��� ����� ����� � �������.<br/><br/>
��� ���������� ��������� ���������, �������� � �1996�, � �� �96�.';
$MESS[$strName.'ready-quarter'] = '������� ����� ���� (��� ����������)';
	$MESS[$strHint.'ready-quarter'] = '������ ������������ ��������: �1�, �2�, �3�, �4�.';
$MESS[$strName.'building-section'] = '������ ����';
	$MESS[$strHint.'building-section'] = '��������� ��������: ������� 1�, ������� ��, ���� 3� � �. �.';
$MESS[$strName.'ceiling-height'] = '������ ��������';
	$MESS[$strHint.'ceiling-height'] = '������ �������� � ������';
$MESS[$strName.'guarded-building'] = '�������� ����������';
	$MESS[$strHint.'guarded-building'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'pmg'] = '����������� ���';
	$MESS[$strHint.'pmg'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'access-control-system'] = '������� ���������� �������';
	$MESS[$strHint.'access-control-system'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'lift'] = '����';
	$MESS[$strHint.'lift'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'rubbish-chute'] = '������������';
	$MESS[$strHint.'rubbish-chute'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'electricity-supply'] = '�������������';
	$MESS[$strHint.'electricity-supply'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'water-supply'] = '����������';
	$MESS[$strHint.'water-supply'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'gas-supply'] = '���';
	$MESS[$strHint.'gas-supply'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'sewerage-supply'] = '�����������';
	$MESS[$strHint.'sewerage-supply'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'heating-supply'] = '���������';
	$MESS[$strHint.'heating-supply'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'toilet'] = '������';
	$MESS[$strHint.'toilet'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'shower'] = '���';
	$MESS[$strHint.'shower'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'pool'] = '�������';
	$MESS[$strHint.'pool'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'billiard'] = '�������';
	$MESS[$strHint.'billiard'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'sauna'] = '�����';
	$MESS[$strHint.'sauna'] = '������� ������������ ��� �����.<br/><br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'parking'] = '������� ���������� ��������';
	$MESS[$strHint.'parking'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'parking-places'] = '���������� ��������������� ����������� ����';
	$MESS[$strHint.'parking-places'] = '���������� ��������������� ����������� ����';
$MESS[$strName.'parking-place-price'] = '��������� ������������ �����';
	$MESS[$strHint.'parking-place-price'] = '����������� ��������� ������ ����� � ����� � ������';
$MESS[$strName.'parking-guest'] = '������� �������� ����������� ����';
	$MESS[$strHint.'parking-guest'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'parking-guest-places'] = '���������� �������� ����������� ����';
	$MESS[$strHint.'parking-guest-places'] = '���������� �������� ����������� ����';
$MESS[$strName.'alarm'] = '������� ������������ � ����';
	$MESS[$strHint.'alarm'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'flat-alarm'] = '������� ������������ � ��������';
	$MESS[$strHint.'flat-alarm'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'security'] = '������� ������';
	$MESS[$strHint.'security'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'is-elite'] = '������� ������������';
	$MESS[$strHint.'is-elite'] = $MESS['YANDEX_REALTY_BOOLEAN'];
	
# Just commercial
$MESS[$strHead.'HEADER_COMMERCIAL'] = '�������������� ���������� �� ������������ ������������';

# Just warehouses
$MESS[$strHead.'HEADER_WAREHOUSES'] = '�������������� ���������� �� ��������� � ���������������� ����������';
$MESS[$strName.'twenty-four-seven'] = '����������� ��������������� ������� ����������� ���������� �� ������ ������ 24/7';
	$MESS[$strHint.'twenty-four-seven'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'eating-facilities'] = '������� ����������� �������� � ������';
	$MESS[$strHint.'eating-facilities'] = '������� ���������� ��� ������-������� � ��������� ����������.<br/>'.$MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'responsible-storage'] = '������������� ��������';
	$MESS[$strHint.'responsible-storage'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'pallet-price'] = '��������� ������-����� � ����� � ������ � ������ �������';
	$MESS[$strHint.'pallet-price'] = '����������� � ������ �������������� ��������.';
$MESS[$strName.'freight-elevator'] = '������� ��������� �����';
	$MESS[$strHint.'freight-elevator'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'truck-entrance'] = '����������� �������� ����';
	$MESS[$strHint.'truck-entrance'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'ramp'] = '������� �������';
	$MESS[$strHint.'ramp'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'railway'] = '������� ����� �������� ������';
	$MESS[$strHint.'railway'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'office-warehouse'] = '������� ����� �� ������';
	$MESS[$strHint.'office-warehouse'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'open-area'] = '������� �������� ��������';
	$MESS[$strHint.'open-area'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'service-three-pl'] = '������� 3PL (�������������) �����';
	$MESS[$strHint.'service-three-pl'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'temperature-comment'] = '������������� ������';
	$MESS[$strHint.'temperature-comment'] = '����������� ��� ������������� ����� �� ������.';

# Just garage
$MESS[$strHead.'HEADER_GARAGE'] = '�������������� ���������� �� �������';
$MESS[$strName.'garage-type'] = '��������� ������';
	$MESS[$strHint.'garage-type'] = '������������ ������� ��� ���������� � ������� � ������ �������.<br/><br/>
��������� ��������:
<ul>
	<li>������/�garage�</li>
	<li>������������/�parking place�</li>
	<li>�����/�box�</li>
</ul>';
$MESS[$strName.'ownership-type'] = '������ �������������';
	$MESS[$strHint.'ownership-type'] = '��������� ��������:
<ul>
	<li>���������������/�private�</li>
	<li>�����������/�cooperative�</li>
	<li>��� ������������/�by proxy�.</li>
</ul>';
$MESS[$strName.'garage-name'] = '�������� �������-������������� �����������.';
	$MESS[$strHint.'garage-name'] = '�������� �������-������������� �����������.';
$MESS[$strName.'parking-type'] = '��� ��������';
	$MESS[$strHint.'parking-type'] = '������� ������������ ������ ��� ����� � ������������ �����.<br/><br/>
��������� ��������:
<ul>
	<li>�����������/�underground�</li>
	<li>����������/�ground�</li>
	<li>����������������/�multilevel�.</li>
</ul>';
$MESS[$strName.'automatic-gates'] = '������� �������������� �����';
	$MESS[$strHint.'automatic-gates'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'cctv'] = '������� ���������������';
	$MESS[$strHint.'cctv'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'inspection-pit'] = '������� ��������� ���';
	$MESS[$strHint.'inspection-pit'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'cellar'] = '������� ������� ��� �������';
	$MESS[$strHint.'cellar'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'car-wash'] = '������� ���������';
	$MESS[$strHint.'car-wash'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'auto-repair'] = '������� �����������';
	$MESS[$strHint.'auto-repair'] = $MESS['YANDEX_REALTY_BOOLEAN'];
$MESS[$strName.'new-parking'] = '������� ������ � �����������';
	$MESS[$strHint.'new-parking'] = $MESS['YANDEX_REALTY_BOOLEAN'];

# Fields: Seller
$MESS[$strHead.'HEADER_SELLER'] = '���������� � �������� ��� ������������';
$MESS[$strName.'sales-agent.name'] = '��� ��������, ������������ ��� ������';
	$MESS[$strHint.'sales-agent.name'] = '��� ��������, ������������ ��� ������.';
$MESS[$strName.'sales-agent.phone'] = '����� ��������';
	$MESS[$strHint.'sales-agent.phone'] = '����� ����������� � ������������� �������.<br/><br/>
<b>������:</b><br/>
&lt;phone&gt;+74951234567&lt;/phone&gt;<br/><br/>
���� ������� ���������, ������ �� ��� ���������� ���������� � ��������� �������� phone.<br/><br/>
����������. ��� �������� ������������ ����������� ������ ���� ������� ������ ������ �������.';
$MESS[$strName.'sales-agent.category'] = '��� �������� ��� ������������';
	$MESS[$strHint.'sales-agent.category'] = '������ ������������ ��������:
<ul>
	<li>����������/�agency�</li>
	<li>�����������/�developer�.</li>
</ul>
����������. ������� ������� ��������� �������� ����������/�agency�.';
$MESS[$strName.'sales-agent.organization'] = '�������� �����������';
	$MESS[$strHint.'sales-agent.organization'] = '�������� �����������.';
$MESS[$strName.'sales-agent.url'] = '���� ��������� ��� �����������';
	$MESS[$strHint.'sales-agent.url'] = '���� ��������� ��� �����������. ��������: <code>https://www.acrit-studio.ru/';
$MESS[$strName.'sales-agent.email'] = '����������� ����� ��������';
	$MESS[$strHint.'sales-agent.email'] = '����������� ����� ��������.';
$MESS[$strName.'sales-agent.photo'] = '������ �� ���������� ������ ��� ������� ��������';
	$MESS[$strHint.'sales-agent.photo'] = '������ �� ���������� ������ ��� ������� ��������.';



?>