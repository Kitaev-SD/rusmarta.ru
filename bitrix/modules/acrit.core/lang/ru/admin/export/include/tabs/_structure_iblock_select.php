<?
$MESS['ACRIT_EXP_STRUCTURE_IBLOCK_SELECT_EMPTY'] = '--- �������� �������� ---';
$MESS['ACRIT_EXP_STRUCTURE_IBLOCK_SELECT_MULTIPLE_NOTICE'] = '<b>��������!</b> � ������ ������� ��������� ����� ��������� ���������� (����� ��������� ����������: #COUNT#). <a href="javascript:void(0)" class="acrit-inline-link" data-role="iblock-multiple-notice-toggle">���������</a>
<div data-role="iblock-multiple-notice-container" style="display:none;">
	<p>�������� � ������ ������� �������������� ����� �� ���� ��������� ����������, ������ ������ �������� ����� ����������� ���������.</p>
	<ul>
		<li>��� �������� � ���������� ������� �� ��� �������� ��� � ������ (��. ����).</li>
		<li>��������� ������������� ����������.</li>
		<li>����������� ��������� ����� �����: �� �������� ���������� �� ��������� (��� �������� <a href="javascript:void(0)" data-role="iblock-multiple-notice-link" style="color:inherit;">�������� ����������</a>).</li>
		<li>������� ������� ������� ��������� �������� ��������� (��������� ������ ���������� �� ���������).</li>
		<li>������ ����������� ����� ������ � <a href="/bitrix/admin/settings.php?lang=#LANGUAGE_ID#&mid=#MODULE_ID#" target="_blank" style="color:inherit;">���������� ������</a>.</li>
	</ul>
</div>
<script>
$(\'a[data-role="iblock-multiple-notice-link"]\').bind(\'click\', function(e){
	e.preventDefault();
	$(\'[data-role="preview-iblocks"]\').trigger(\'click\');
});
$(\'a[data-role="iblock-multiple-notice-toggle"]\').bind(\'click\', function(e){
	e.preventDefault();
	var container = $(\'[data-role="iblock-multiple-notice-container"\');
	if(!container.is(\':animated\')){
		container.slideToggle();
	}
});
</script>';
?>