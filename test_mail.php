<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");?>
<?if (mail("sk@liderpoiska.ru","test subject", "test body","From: sale@rusmarta.ru")) {
    echo "Отправлено";
} else {
    echo "Не отправлено";
} ?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>