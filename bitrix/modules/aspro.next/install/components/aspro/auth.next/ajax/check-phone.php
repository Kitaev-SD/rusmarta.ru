<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$phone = $_REQUEST["phone"];
$result = preg_match_all('/\d/',$_REQUEST["phone"],$found);
$phone = implode('',$found[0]);

$rsUser = CUser::GetList( $by="ID", $order="ASC", array( "PERSONAL_PHONE" => $phone), array( "FIELDS" => array("ID", "NAME")) );
$arItems = array();
while($arItem=$rsUser->Fetch()){
	$arItems[$arItem["ID"]] = $arItem["ID"];
}
if($arItems){
	//if($arItem["ID"]!=$_REQUEST["userid"])
	if(!isset($arItems[$_REQUEST["userid"]]))
		echo 'false';
	else
		echo 'true';
}else{
	echo 'true';
}?>