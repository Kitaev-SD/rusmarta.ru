<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?CModule::IncludeModule("iblock");?>

<?
$arFilter = array(
	"ACTIVE" => "Y",
	"IBLOCK_ID" => 7,
	"NAME" => $_GET['city']
);

$arElement = CIBlockElement::GetList(
	array("SORT" => "ASC"),
	$arFilter,
	false,
	array("nTopCount" => 1),
	array("ID", "PREVIEW_TEXT")
)->Fetch();

echo $arElement["PREVIEW_TEXT"];

if ($arElement["PREVIEW_TEXT"] == false) {
	$arElement2 = CIBlockElement::GetList(
		array("SORT" => "ASC"),
		array("ACTIVE" => "Y", "IBLOCK_ID" => 7),
		false,
		array("nTopCount" => 1),
		array("ID", "PREVIEW_TEXT")
	)->Fetch();

	echo $arElement2["PREVIEW_TEXT"];
}
?>