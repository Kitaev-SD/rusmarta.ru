<?
$f = 'modules/edost.delivery/admin/edost.php';
$r = $_SERVER['DOCUMENT_ROOT'];
$s = $r.'/local/'.$f;
if (!is_file($s)) $s = $r.'/bitrix/'.$f;
require_once($s);
?>