<?
$connection = \Bitrix\Main\Application::getConnection();
$connection->queryExecute("SET NAMES 'cp1251'");
$connection = Bitrix\Main\Application::getConnection();
$connection->queryExecute("SET sql_mode=''");
$connection = Bitrix\Main\Application::getConnection();
$connection->queryExecute("SET innodb_strict_mode=0");
?>
