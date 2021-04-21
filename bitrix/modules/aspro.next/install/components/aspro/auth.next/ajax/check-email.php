<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$rsUser = CUser::GetList( $by="ID", $order="DESC", array( "=EMAIL" => $_REQUEST["email"] ), array( "FIELDS" => array("ID", "NAME", "EMAIL")) );
if($arItem=$rsUser->Fetch()){
	if($arItem["ID"]!=$_REQUEST["userid"])
		echo 'false';
	else
		echo 'true';
}else{
	echo 'true';
}?>