<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Способы оплаты");?>

<?$APPLICATION->IncludeComponent("bitrix:catalog.section.list", "payments",
	Array(
		"IBLOCK_TYPE" => "content",
		"IBLOCK_ID" => "#PAYMENTS_IBLOCK_ID#",
		"SECTION_ID" => "",
		"SECTION_CODE" => "",
		"COUNT_ELEMENTS" => "N",
		"TOP_DEPTH" => "2",
		"SECTION_FIELDS" => array(),
		"SECTION_USER_FIELDS" => array(),
		"VIEW_MODE" => "",
		"SHOW_PARENT_NAME" => "",
		"SECTION_URL" => "",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_GROUPS" => "Y",
		"ADD_SECTIONS_CHAIN" => "N"
	)
);?>

<h2>Дополнительное текстовое описание раздела</h2>
<p>Вы можете отключить ненужные вам способы оплаты или добавить свои, используя удобную для вас структуру. Рекомендуем не использовать вложенность категорий&nbsp;более 2-х уровней.</p>
<p>Данный сайт является демо-версией готового интернет-магазина ЭЛЕКТРОСИЛА для 1С-Битрикс. Вся информация на сайте не является офертой, а служит лишь примером наполнения для ознакомления с возможностями решения.</p>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>