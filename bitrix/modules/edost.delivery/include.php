<?
//define('DELIVERY_EDOST_ADMIN_FILE_EDIT', 'Y');

$s = $GLOBALS['APPLICATION']->GetCurPage();
$admin_file_edit = ($s === '/bitrix/admin/fileman_file_edit.php' ? true : false);
if (!$admin_file_edit && $s !== '/bitrix/admin/fileman_admin.php' && $s !== '/bitrix/admin/fileman_file_upload.php') {
	$MODULE_ID = 'edost.delivery';
	if (!CModule::IncludeModule('sale')) return false;

	$s = 'modules/'.$MODULE_ID.'/classes/general/delivery_edost.php';
	$s = $_SERVER['DOCUMENT_ROOT'].getLocalPath($s);
	IncludeModuleLangFile($s);

	CModule::AddAutoloadClasses($MODULE_ID, array('CEdostModifySaleOrderAjax' => 'general/edost_saleorderajax.php'));
}
else if ($admin_file_edit && defined('DELIVERY_EDOST_ADMIN_FILE_EDIT') && DELIVERY_EDOST_ADMIN_FILE_EDIT == 'Y') {
	$s = '
		function edost_SetStyle() {
			var E = document.querySelector("div.bxce-base-cont");
			if (E) E.style.height = "780px";

			var E = document.querySelector("div.bxce.bxce--light");
			if (E) E.style.height = "830px";

			var E = document.querySelector("div.adm-detail-content-item-block");
			var p = BX.pos(E);
			window.scroll(0, p.top-50);
		}

		window.setTimeout("edost_SetStyle()", 500);
	';
	$s = '<script>'.$s.'</script>';
	$GLOBALS['APPLICATION']->AddHeadString($s);
}
?>