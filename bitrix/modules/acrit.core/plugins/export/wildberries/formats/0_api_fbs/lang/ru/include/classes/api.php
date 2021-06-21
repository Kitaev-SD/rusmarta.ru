<?
\Acrit\Core\Export\Exporter::getLangPrefix(realpath(__DIR__.'/../../../class.php'), $strLang, $strHead, $strName, $strHint);

// General
$MESS[$strLang.'ERROR_GENERAL'] = 'Ошибка при выполнении команды #COMMAND#: #ERROR#.';
	$MESS[$strLang.'ERROR_GENERAL_DEBUG'] = $MESS[$strLang.'ERROR_GENERAL'].PHP_EOL.' Json: #JSON#';
$MESS[$strLang.'ERROR_REQUEST'] = 'Ошибка выполнения запроса для команды #COMMAND# (ответ: #CODE#).';
	$MESS[$strLang.'ERROR_REQUEST_DEBUG'] = $MESS[$strLang.'ERROR_REQUEST'].PHP_EOL.' Json: #JSON#'.PHP_EOL.'Response: #RESPONSE#';
$MESS[$strLang.'NOTICE_ATTEMPT'] = 'Попытка выполнения метода #COMMAND# (#INDEX# / #COUNT#) после получения ответа #CODE#.';
$MESS[$strLang.'INFO_ELEMENT'] = '[Товар #ELEMENT_ID#] ';



?>