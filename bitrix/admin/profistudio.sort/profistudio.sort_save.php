<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule('iblock');

$ibSect=new CIBlockSection();
$ibEl=new CIBlockElement();

//print_r($_REQUEST);

foreach ($_REQUEST['sort'] as $key => $value) {
	echo $key[0];
	if($key[0]=="E"){	
		if($ibEl->Update(substr($key,1),array("SORT"=>$_POST["sort"][$key][0]))){
			echo "good";
		} else {
			echo $ibEl->LAST_ERROR;
		}
	} elseif($key[0]=="S"){
		if($ibSect->Update(substr($key,1),array("SORT"=>$_POST["sort"][$key][0]*10))){
			echo "good";
		} else {
			echo $ibSect->LAST_ERROR;
		}
	}
}
?>