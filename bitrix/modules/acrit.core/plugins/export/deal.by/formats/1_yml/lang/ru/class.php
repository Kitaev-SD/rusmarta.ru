<?
\Acrit\Core\Export\Exporter::getLangPrefix(__FILE__, $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'NAME'] = 'deal.by (������ YML)';

// Settings
$MESS[$strLang.'SETTINGS_NAME_SHOP_NAME'] = '�������� ��������';
	$MESS[$strLang.'SETTINGS_HINT_SHOP_NAME'] = '������� ����� �������� �������� (��� ������ � XML-����� � ���� &lt;name&gt;).';
$MESS[$strLang.'SETTINGS_NAME_SHOP_COMPANY'] = '�������� �����������';
	$MESS[$strLang.'SETTINGS_HINT_SHOP_COMPANY'] = '������� ����� �������� ����������� (��� ������ � XML-����� � ���� &lt;company&gt;).';

// Fields
$MESS[$strHead.'HEADER_GENERAL'] = '����� ����������';
	$MESS[$strName.'@id'] = '�������������';
		$MESS[$strHint.'@id'] = '���������� ������������� ������.';
	$MESS[$strName.'@available'] = '�����������';
		$MESS[$strHint.'@available'] = '���������� ����������� ������ (true / false)';
	$MESS[$strName.'@type'] = '��� ������ � ��������';
		$MESS[$strHint.'@type'] = '��� ������ � ��������.<br/><br/>
��� vendor.model ������������ ��� �������� �������� ������������� ������� �� deal.by �� �������� �typePrefix + vendor + model�.<br/><br/>
��� �������� vendor.model ������ ���� ����������� ���������� �������� ���������_������.';
	$MESS[$strName.'@selling_type'] = '��� ������ �� deal.by';
		$MESS[$strHint.'@selling_type'] = '���_������ ���������� ���������� ������ � �������� �� �������� ������� �������.<br/><br/>
��������� ��������: r, w, u, s.
<ul>
	<li>r � ������ ��������� ������ � ������� ��� ��������������� � ������������ ������� � ���������� ������.</li>
	<li>w � ������ ��������� ������ ����� ��� ��������������� � ������������ �������, ������� ��������� ������ �����.</li>
	<li>u � ������ ��������� ����� � � ������� ��� �������, ������� ��������� � ����� � � �������.</li>
	<li>s � ������.</li>
</ul>
��� ������� ������������ ��� ���������� �����, ��������������� ������� ����� ��� ���������.';
	$MESS[$strName.'@group_id'] = '�������������';
		$MESS[$strHint.'@group_id'] = '�������� "group_id" � ��� ���������� �����, ������� ������������ ��� ���������� ��������� ������ � ���  ��������������. �������, � ������� ���� ����� "group_id", � ����� ������� �������������� ����� ��������� "param name" ��������� �������������� ��������� ������, ������� ����� �����-�� ����� "group_id".';
	$MESS[$strName.'name'] = '�������� ������';
		$MESS[$strHint.'name'] = '�������� ������. ������������ ���� ��� ���������� ��������� type="vendor.model". ��� ������������� type="vendor.model" ��������_������ �� ������ ���� ������. � ������ ������������� type="vendor.model", �������� ������ ����� ������������ �� �������� ���� ����� �� ��������� �������: typePrefix + vendor + model.';
	$MESS[$strName.'url'] = '������ �� �����';
		$MESS[$strHint.'url'] = '������ �� ����� �� ����� ��������.';
	$MESS[$strName.'typePrefix'] = '������� ��������';
		$MESS[$strHint.'typePrefix'] = '� ������ ������������� type="vendor.model", �������� ������ ����� ������������ �� �������� ���� ����� �� ��������� �������: typePrefix + vendor + model.';
	$MESS[$strName.'categoryId'] = '������������� ���������';
		$MESS[$strHint.'categoryId'] = '����� ������ (���������), � ������� ����� ��������� ������� �� ����� �������� ����� �������; ������������� ������ ������ ��� ������ ��������� � ����� �������� ����� <catalog> � ������ ������� ����� �������.';
	$MESS[$strName.'portal_category_id'] = '������������� ���������';
		$MESS[$strHint.'portal_category_id'] = 'ID_���������_��_������� � ���������� ������������� ��������� �������, � ������� ����� ����������� ������ ����� ����� �������.';
	$MESS[$strName.'portal_category_url'] = 'URL ���������';
		$MESS[$strHint.'portal_category_url'] = 'URL ���������';
	$MESS[$strName.'price'] = '���� ������';
		$MESS[$strHint.'price'] = '���� ��� ���� � ������ ������. �������� ������������ ������ ��� �������� ���� oldprice.
��������! ���� � ����� ������� �� ������ ��� available ��� ������, ����� ������� ����� � ������ �� ������� ������ �� ����� ������������ �� �����. ���� ������ ����� ����� �� ���� oldprice (���� ������ ��� ������).';
	$MESS[$strName.'oldprice'] = '���� ������ ��� ������';
		$MESS[$strHint.'oldprice'] = '���� � ������ ���� ������, � ������ ���� ����������� ���� ��� ����� ������. ��� ������� ������� ���� ��� price �������� ������������. ��� oldprice ������ ������������ ��������� � ����� discount.';
	$MESS[$strName.'minimum_order_quantity'] = '����������_�������';
		$MESS[$strHint.'minimum_order_quantity'] = '������������ ��� �������� ������������ ���������� (���� ���� ������ ��)  ��� �������� ���� ������� � ����� ������ ��������� ������ �����.';
	$MESS[$strName.'quantity_in_stock'] = '���������� ������ �� ������';
		$MESS[$strHint.'quantity_in_stock'] = '������������ ��� �������� ������� ������� �� ������.';
	$MESS[$strName.'prices.price.value'] = '����';
		$MESS[$strHint.'prices.price.value'] = '���� ������. ������ ���� �������� ��������� � ����������������� ����� � �����: ����� �������� � ����� ����� ������ ���� ����������.';
	$MESS[$strName.'prices.price.quantity'] = '���. ����� ��� ��������� ����';
		$MESS[$strHint.'prices.price.quantity'] = '������� ����� ����������� ����� ����� ��� �������� ���� ����. ������ ���� �������� ��������� � ����������������� ����� � �����: ����� �������� � ����� ����� ������ ���� ����������.';
	$MESS[$strName.'discount'] = '������';
		$MESS[$strHint.'discount'] = '������ ������. ���� � ������ ���� ������, � ������ ���� ����������� �������� ������ ��� �������. ������: 12.5, 30%. ��� ������� ������� ���� ��� price �������� ������������.';
	$MESS[$strName.'currencyId'] = '������';
		$MESS[$strHint.'currencyId'] = '������, � ������� ������� ���� (RUB, UAH, BYR, KZT, EUR, USD).';
	$MESS[$strName.'picture'] = '����������';
		$MESS[$strHint.'picture'] = '������ �� ���������� ������. ����� ���� ������� �� 1 �� 10 ������, � ����������� �� ������ �����.
�������� ��������: ����� ������������� ����������� � Google �����. ��� ����� ����� ���� ������ � ����� ����������� ������ import@uaprom-prod-1495098375216.iam.gserviceaccount.com.';
	$MESS[$strName.'vendor'] = '�������� �������������';
		$MESS[$strHint.'vendor'] = '�����, �������� ����� ��� �������� �����������-�������������, ��� ������ �������� ���������� ����� (�������� �������� ������������ ������������). �������� ������������� ������������� � ������� ��������������� � �������� ������.<br/><br/>
		<b>��������!</b> ����������� ���� ������������� ����� ������������ ������ ���� �� ���� � ���� �������������� �������. ��������� ��� ������� � �������� ������ ������������� ����� ��� ����������/�������������� ������ � ���� ��������������� (���� ���������������). ��� ������������� ���� �vendor.model�, ��� vendor �������� ������������, ��� ��� ��������� � ������������ �������� ������.';
	$MESS[$strName.'vendorCode'] = '��� (�������)';
		$MESS[$strHint.'vendorCode'] = '��� ������ (�������) ��������� ��� �������� � �������� ������ ������ ������� �� ����� �������� � � ������ �������� ��� ���������� ��������� �������. ����� �������� - 25 �������� (�����, ���������, ��������, ����� �-�, �_�, �.�, �/� � ������).';
	$MESS[$strName.'model'] = '������ ������';
		$MESS[$strHint.'model'] = '������ ������, ��������� � ������������ �������� ������ ��� ������������� ���� vendor.model';
	$MESS[$strName.'barcode'] = '��� (�������)';
		$MESS[$strHint.'barcode'] = '��� ������ (�������) ��������� ��� �������� � �������� ������ ������ ������� �� ����� �������� � � ������ �������� ��� ���������� ��������� �������. ����� �������� - 25 �������� (�����, ���������, ��������, ����� �-�, �_�, �.�, �/� � ������).';
	$MESS[$strName.'country'] = '������-�������������';
		$MESS[$strHint.'country'] = '������������� � ������� ������� �������������� � �������� ������.';
	$MESS[$strName.'country_of_origin'] = '������ �������������';
		$MESS[$strHint.'country_of_origin'] = '������ �������������';
	$MESS[$strName.'description'] = '�������� ������';
		$MESS[$strHint.'description'] = '�������� ������. ������������ ����. ����� �������� ������ ����� ��������� HTML-���� � ����������� ������ ���� �������� � ��� &lt;![CDATA[...]]&gt;.';
	$MESS[$strName.'keywords'] = '�������� �����';
		$MESS[$strHint.'keywords'] = '�������� ����� (��������� �������, ����) �������� ������� ��� ������, ����� �������';
	$MESS[$strName.'delivery'] = '��������';
		$MESS[$strHint.'delivery'] = '��������';
	$MESS[$strName.'local_delivery_cost'] = '��������� ��������';
		$MESS[$strHint.'local_delivery_cost'] = '��������� ���������� ��������';
	$MESS[$strName.'manufacturer_warranty'] = '�������� �������������';
		$MESS[$strHint.'manufacturer_warranty'] = '�������� �������������';
	$MESS[$strName.'downloadable'] = '������� ����� �������';
		$MESS[$strHint.'downloadable'] = '������� ����� �������. ���� ������� true, ����������� ������������ �� ���� ��������.';
$MESS[$strHead.'HEADER_BOOK'] = '�������������� ����';
	$MESS[$strName.'author'] = '�����';
		$MESS[$strHint.'author'] = '����� ������������.';
	$MESS[$strName.'publisher'] = '������������';
		$MESS[$strHint.'publisher'] = '������������';
	$MESS[$strName.'series'] = '�����';
		$MESS[$strHint.'series'] = '�����';
	$MESS[$strName.'year'] = '���.';
		$MESS[$strHint.'year'] = '��� �������.';
	$MESS[$strName.'ISBN'] = '��� ISBN';
		$MESS[$strHint.'ISBN'] = 'International Standard Book Number � ������������� ���������� ����� �������� �������. ���� �� ���������, ������� ��� ����� �������.<br/><br/>
		������� ISBN � SBN ����������� �� ������������. ��������� ����� ���������� �� ������ �� �����, ����� ����������� ����������� ����� (check-digit) � ��������� ����� ���� ������ ��������������� � ���������� ������� �� ������������ �������. ��� ��������� ISBN �� ����� ��� ������ ������ (��������, 978-5-94878-004-7) ��� ����������� �� ������������ �������������� ����������� � ���������� ���� � ������ �� ������.';
	$MESS[$strName.'volume'] = '���������� �����';
		$MESS[$strHint.'volume'] = '����� ���������� �����, ���� ������� ������� �� ���������� �����.';
	$MESS[$strName.'part'] = '����� ����';
		$MESS[$strHint.'part'] = '������� ����� ����, ���� ������� ������� �� ���������� �����.';
	$MESS[$strName.'language'] = '����';
		$MESS[$strHint.'language'] = '����, �� ������� ������ ������������.';
	$MESS[$strName.'binding'] = '������';
		$MESS[$strHint.'binding'] = '������';
	$MESS[$strName.'page_extent'] = '���������� �������';
		$MESS[$strHint.'page_extent'] = '���������� ������� � �����, ������ ���� ����� ������������� ������.';
$MESS[$strHead.'HEADER_AUDIOBOOK'] = '�������������� ���������';
	$MESS[$strName.'performed_by'] = '�����������';
		$MESS[$strHint.'performed_by'] = '�����������. ���� �� ���������, ������������� ����� �������.';
	$MESS[$strName.'performance_type'] = '��� ����������';
		$MESS[$strHint.'performance_type'] = '��� ���������� (��������������, ������������� �������� � �. �.).';
	$MESS[$strName.'storage'] = '��������';
		$MESS[$strHint.'storage'] = '�������� ����������.';
	$MESS[$strName.'format'] = '������';
		$MESS[$strHint.'format'] = '������ ����������.';
	$MESS[$strName.'recording_length'] = '����� ��������';
		$MESS[$strHint.'recording_length'] = '����� ��������, �������� � ������� mm.ss (������.�������).';
$MESS[$strHead.'HEADER_MEDIA'] = '�������������� �����';
	$MESS[$strName.'artist'] = '�����������';
		$MESS[$strHint.'artist'] = '�����������';
	$MESS[$strName.'title'] = '��������';
		$MESS[$strHint.'title'] = '��������';
	$MESS[$strName.'media'] = '��������';
		$MESS[$strHint.'media'] = '��������';
	$MESS[$strName.'starring'] = '������';
		$MESS[$strHint.'starring'] = '������';
	$MESS[$strName.'director'] = '��������';
		$MESS[$strHint.'director'] = '��������';
	$MESS[$strName.'originalName'] = '������������ ��������';
		$MESS[$strHint.'originalName'] = '������������ ��������';
$MESS[$strHead.'HEADER_TOURS'] = '�������������� �����';
	$MESS[$strName.'worldRegion'] = '����� �����';
		$MESS[$strHint.'worldRegion'] = '����� �����';
	$MESS[$strName.'region'] = '������ ��� �����';
		$MESS[$strHint.'region'] = '������ ��� �����';
	$MESS[$strName.'days'] = '���������� ���� ����';
		$MESS[$strHint.'days'] = '���������� ���� ����';
	$MESS[$strName.'dataTour'] = '���� �������';
		$MESS[$strHint.'dataTour'] = '���� �������. ���������������� ������: YYYY-MM-DD hh:mm:ss. <a href="https://yandex.ru/support/partnermarket/export/date-format.html" target="_blank">������������� �������</a>.
� ������� YML ������� offer ����� ��������� ��������� ��������� dataTour.';
	$MESS[$strName.'hotel_stars'] = '������ �����';
		$MESS[$strHint.'hotel_stars'] = '������ �����';
	$MESS[$strName.'room'] = '��� �������';
		$MESS[$strHint.'room'] = '��� ������� (SNG, DBL � �. �.).';
	$MESS[$strName.'meal'] = '��� �������';
		$MESS[$strHint.'meal'] = '��� ������� (All, HB � �. �.).';
	$MESS[$strName.'included'] = '��� ��������';
		$MESS[$strHint.'included'] = '��� �������� � ��������� ����.';
	$MESS[$strName.'transport'] = '���������';
		$MESS[$strHint.'transport'] = '���������';
$MESS[$strHead.'HEADER_TICKETS'] = '�������������� �������';
	$MESS[$strName.'place'] = '����� ����������';
		$MESS[$strHint.'place'] = '����� ����������';
	$MESS[$strName.'hall'] = '���';
		$MESS[$strHint.'hall'] = '���';
	$MESS[$strName.'hall@plan'] = '���� ����';
		$MESS[$strHint.'hall@plan'] = '���� ����';
	$MESS[$strName.'hall_part'] = '��� � ����� � ����';
		$MESS[$strHint.'hall_part'] = '��� � ����� � ����';
	$MESS[$strName.'date'] = '���� � ����� ������';
		$MESS[$strHint.'date'] = '���� � ����� ������. ���������������� ������: YYYY-MM-DD hh:mm:ss. <a href="https://yandex.ru/support/partnermarket/export/date-format.html" target="_blank">������������� �������</a>.';
	$MESS[$strName.'is_premiere'] = '��������';
		$MESS[$strHint.'is_premiere'] = '������� ������������ ����������� (true / false).';
	$MESS[$strName.'is_kids'] = '������� �����������';
		$MESS[$strHint.'is_kids'] = '������� �������� ����������� (true / false).';

?>