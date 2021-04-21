<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$rsUser = CUser::GetList($by="ID", $order="DESC", array( "=EMAIL" => $_REQUEST["email"] ) );
if( intval( $rsUser->SelectedRowsCount() ) > 0 ){ echo 'true'; }else{ echo 'false'; }?>