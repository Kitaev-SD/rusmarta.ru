<?
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION"] = "O diretório de armazenamento da sessão contém sessões de projetos diferentes.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_OWNER"] = "Motivo: o proprietário do arquivo não é o usuário atual<br> 
Arquivo: #ARQUIVO# <br> 
UID do proprietário do arquivo: #FILE_ONWER#<br> 
UID do usuário atual: #CURRENT_OWNER#<br>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_SIGN"] = "Motivo: o arquivo de sessão não está assinado com a assinatura do site atual<br> 
Arquivo: #FILE#<br> 
Assinatura do site atual: #SIGN#<br> 
Conteúdos do arquivo: <pre>#FILE_CONTENT#</pre>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_DETAIL"] = "A situação pode acontecer quando isto compromete completamente o seu projeto.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_RECOMMENDATION"] = "Usar um armazenamento individual para cada projeto.";
$MESS["SECURITY_SITE_CHECKER_EnvironmentTest_NAME"] = "Verificação de Meio Ambiente";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR"] = "A seção do diretório de armazenamento de arquivos é acessível por todos os usuários do sistema";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_ADDITIONAL"] = "Diretório de armazenamento de sessão: #DIR#<br> 
Permissão: #PERMS#";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_DETAIL"] = "Esta vulnerabilidade pode ser usada para ler ou alterar a execução de scripts de dados da sessão em outros servidores virtuais.";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_RECOMMENDATION"] = "Configure as permissões de acesso corretamente ou mude o diretório. Outra opção é armazenar sessões em banco de dados: <a href=\"/bitrix/admin/security_session.php\">Proteção de sessão</a>.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP"] = "Scripts PHP são executados no diretório de arquivos carregados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DETAIL"] = "s vezes, os desenvolvedores não prestam atenção suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE"] = "Scripts PHP com a extensão dupla (por exemplo php.lala) são executados no diretório de arquivos carregados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_DETAIL"] = "s vezes, os desenvolvedores não prestam atenção suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY"] = "Scripts em Python são executados no diretório de arquivos carregados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_DETAIL"] = "s vezes, os desenvolvedores não prestam atenção suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS"] = "O Apache não deve processar os arquivos. Htaccess no diretório de arquivos enviados";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_DETAIL"] = "s vezes, os desenvolvedores não prestam atenção suficiente em filtros de nome de arquivo adequados. Um invasor pode explorar essa vulnerabilidade e assumir total controle do seu projeto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_RECOMMENDATION"] = "Configure seu servidor web corretamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION"] = "Apache Content Negotiation está ativado no diretório de upload de arquivos.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_DETAIL"] = "Apache Content Negotiation não é recomendada porque poderá incorrer em ataques XSS.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_RECOMMENDATION"] = "Configure o servidor web corretamente.";
?>