<?
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION"] = "O diret�rio de armazenamento da sess�o cont�m sess�es de projetos diferentes.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_OWNER"] = "Motivo: o propriet�rio do arquivo n�o � o usu�rio atual<br> 
Arquivo: #ARQUIVO# <br> 
UID do propriet�rio do arquivo: #FILE_ONWER#<br> 
UID do usu�rio atual: #CURRENT_OWNER#<br>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_SIGN"] = "Motivo: o arquivo de sess�o n�o est� assinado com a assinatura do site atual<br> 
Arquivo: #FILE#<br> 
Assinatura do site atual: #SIGN#<br> 
Conte�dos do arquivo: <pre>#FILE_CONTENT#</pre>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_DETAIL"] = "A situa��o pode acontecer quando isto compromete completamente o seu projeto.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_RECOMMENDATION"] = "Usar um armazenamento individual para cada projeto.";
$MESS["SECURITY_SITE_CHECKER_EnvironmentTest_NAME"] = "Verifica��o de Meio Ambiente";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR"] = "A se��o do diret�rio de armazenamento de arquivos � acess�vel por todos os usu�rios do sistema";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_ADDITIONAL"] = "Diret�rio de armazenamento de sess�o: #DIR#<br> 
Permiss�o: #PERMS#";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_DETAIL"] = "Esta vulnerabilidade pode ser usada para ler ou alterar a execu��o de scripts de dados da sess�o em outros servidores virtuais.";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_RECOMMENDATION"] = "Configure as permiss�es de acesso corretamente ou mude o diret�rio. Outra op��o � armazenar sess�es em banco de dados: <a href=\"/bitrix/admin/security_session.php\">Prote��o de sess�o</a>.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP"] = "Scripts PHP s�o executados no diret�rio de arquivos carregados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DETAIL"] = "s vezes, os desenvolvedores n�o prestam aten��o suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE"] = "Scripts PHP com a extens�o dupla (por exemplo php.lala) s�o executados no diret�rio de arquivos carregados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_DETAIL"] = "s vezes, os desenvolvedores n�o prestam aten��o suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY"] = "Scripts em Python s�o executados no diret�rio de arquivos carregados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_DETAIL"] = "s vezes, os desenvolvedores n�o prestam aten��o suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS"] = "O Apache n�o deve processar os arquivos. Htaccess no diret�rio de arquivos enviados";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_DETAIL"] = "s vezes, os desenvolvedores n�o prestam aten��o suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION"] = "Apache Content Negotiation est� ativado no diret�rio de upload de arquivos.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_DETAIL"] = "Apache Content Negotiation n�o � recomendada porque poder� incorrer em ataques XSS.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_RECOMMENDATION"] = "Configure o servidor web corretamente.";
?>