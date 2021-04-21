<?
$MESS["SECURITY_SITE_CHECKER_LOW_PHP_VERSION_ENTROPY"] = "A versão do php está desatualizada";
$MESS["SECURITY_SITE_CHECKER_LOW_PHP_VERSION_ENTROPY_DETAIL"] = "A versão atual do php não suporta a instalação de uma fonte adicional de entropia na criação de um ID da sessão";
$MESS["SECURITY_SITE_CHECKER_LOW_PHP_VERSION_ENTROPY_RECOMMENDATION"] = "Atualize o Php para a versão 5.3.3 ou superior, de preferência para a versão estável mais recente";
$MESS["SECURITY_SITE_CHECKER_PHP_ASP"] = "Tags no estilo ASP estão habilitadas";
$MESS["SECURITY_SITE_CHECKER_PHP_ASP_DETAIL"] = "Apenas alguns desenvolvedores sabem que essa opção existe. Esta opção é redundante.";
$MESS["SECURITY_SITE_CHECKER_PHP_ASP_RECOMMENDATION"] = "Adicionar ou editar a seguinte linha nas configurações do PHP: <br> asp_tags = Off";
$MESS["SECURITY_SITE_CHECKER_PHP_COOKIEONLY"] = "IDs de sessões são salvos em outros armazenamentos além de cookies";
$MESS["SECURITY_SITE_CHECKER_PHP_COOKIEONLY_DETAIL"] = "Salvar um ID de sessão em outro lugar que os cookies pode levar a hijacking de sessão.";
$MESS["SECURITY_SITE_CHECKER_PHP_COOKIEONLY_RECOMMENDATION"] = "Adicionar ou editar a seguinte linha nas configurações do PHP: <br> session.use_only_cookies = On";
$MESS["SECURITY_SITE_CHECKER_PHP_ENTROPY"] = "Nenhuma fonte de entropia adicional para o ID da sessão foi definido";
$MESS["SECURITY_SITE_CHECKER_PHP_ENTROPY_DETAIL"] = "A falta de entropia adicional pode ser usada para prever os números aleatórios e o ID da sessão.";
$MESS["SECURITY_SITE_CHECKER_PHP_ENTROPY_RECOMMENDATION"] = "Adicione a seguinte linha para as configurações de PHP: <br> session.entropy_file =/dev/urandom <br> session.entropy_length = 128";
$MESS["SECURITY_SITE_CHECKER_PHP_FOPEN"] = "O acesso de leitura para URL wrappers está habilitado.";
$MESS["SECURITY_SITE_CHECKER_PHP_FOPEN_DETAIL"] = "Esta opção não é necessária, mas pode, eventualmente, ser utilizada por um invasor.";
$MESS["SECURITY_SITE_CHECKER_PHP_FOPEN_RECOMMENDATION"] = "Adicionar ou editar a seguinte linha nas configurações do PHP:<br>allow_url_fopen=Off";
$MESS["SECURITY_SITE_CHECKER_PHP_HTTPONLY"] = "Os cookies são acessíveis a partir de JavaScript";
$MESS["SECURITY_SITE_CHECKER_PHP_HTTPONLY_DETAIL"] = "Tornar cookies acessíveis a partir do JavaScript aumentará as chances de ataques XSS bem-sucedidos.";
$MESS["SECURITY_SITE_CHECKER_PHP_HTTPONLY_RECOMMENDATION"] = "Adicionar ou editar a seguinte linha nas configurações do PHP: <br> session.cookie_httponly = On";
$MESS["SECURITY_SITE_CHECKER_PHP_INCLUDE"] = "URL wrappers estão habilitados";
$MESS["SECURITY_SITE_CHECKER_PHP_INCLUDE_DETAIL"] = "Esta opção é absolutamente não recomendável.";
$MESS["SECURITY_SITE_CHECKER_PHP_INCLUDE_RECOMMENDATION"] = "Adicionar ou editar a seguinte linha nas configurações do PHP:<br>allow_url_fopen=Off";
$MESS["SECURITY_SITE_CHECKER_PHP_MBSTRING_SUBSTITUTE"] = "Excluir sequencias de caracteres inválidos ";
$MESS["SECURITY_SITE_CHECKER_PHP_MBSTRING_SUBSTITUTE_DETAIL"] = "A capacidade de apagar os caracteres inválidos podem ser explorados para os chamados ataques de seqüência de bytes inválidos.";
$MESS["SECURITY_SITE_CHECKER_PHP_MBSTRING_SUBSTITUTE_RECOMMENDATION"] = "Na configuração do PHP, altere o valor de mbstring.substitute_character para qualquer coisa, mas \"nenhum\".";
$MESS["SECURITY_SITE_CHECKER_PhpConfigurationTest_NAME"] = "Verificação de configurações do PHP";
?>