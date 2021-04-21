<?
$MESS["SEC_OTP_CONNECTED"] = "Conectado";
$MESS["SEC_OTP_CONNECT_DEVICE"] = "Conectar dongle";
$MESS["SEC_OTP_CONNECT_DEVICE_TITLE"] = "Conectar dongle";
$MESS["SEC_OTP_CONNECT_DONE"] = "Pronto";
$MESS["SEC_OTP_CONNECT_MOBILE"] = "Conectar dispositivo m�vel";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_CODE"] = "Digite o c�digo";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_NEXT_CODE"] = "Digite o pr�ximo c�digo";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_DESCRIPTION"] = "Uma vez que o c�digo tenha sido digitalizado com sucesso ou inserido manualmente, seu celular ir� mostrar o c�digo que voc� ter� que digitar abaixo.";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_NEXT_DESCRIPTION"] = "O algoritmo OTP requer dois c�digos de autentica��o. Gere o pr�ximo c�digo e digite-o abaixo.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT"] = "Para inserir os dados manualmente, especifique o endere�o do site, seu e-mail ou login, um c�digo secreto na imagem e selecione o tipo de chave.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_HOTP"] = "com base em contador";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_TOTP"] = "com base em tempo";
$MESS["SEC_OTP_CONNECT_MOBILE_SCAN_QR"] = "Leve seu dispositivo m�vel para o monitor e aguarde enquanto o aplicativo faz a leitura do c�digo.";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_1"] = "Baixe o aplicativo para celular Bitrix OTP para o seu celular na <a href=\"https://itunes.apple.com/en/app/bitrix24-otp/id929604673?l=en\"target=\"_new\">AppStore</a> em <a href=\"https://play.google.com/store/apps/details?id=com.bitrixsoft.otp\" target=\"_new\">GooglePlay</a>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_2"] = "Execute o aplicativo e clique em <b>Configurar</b>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_3"] = "Escolha como voc� deseja inserir os dados: utilizando c�digo QR ou manualmente";
$MESS["SEC_OTP_CONNECT_MOBILE_TITLE"] = "Conectar dispositivo m�vel";
$MESS["SEC_OTP_CONNECT_NEW_DEVICE"] = "Conectar novo dongle";
$MESS["SEC_OTP_CONNECT_NEW_MOBILE"] = "Conectar novo dispositivo m�vel";
$MESS["SEC_OTP_DEACTIVATE_UNTIL"] = "Desativado at� #DATE#";
$MESS["SEC_OTP_DESCRIPTION_ABOUT"] = "A Senha Avulsa (OTP) foi desenvolvida como parte da iniciativa OATH.<br> 
A OTP � baseada em HMAC e SHA-1/SHA-256/SHA-512. No momento, os dois algoritmos s�o suportados para gerar c�digos:
<ul><li>com base em contador (HMAC-Baseado em Senha Avulsa, HOTP) conforme descrito <a href=\"https://tools.ietf.org/html/rfc4226\" target= \"_blank\">RFC4226</a></li> 
<li>baseado em tempo (Senha Avulsa com base em Tempo, TOTP) conforme descrito em <a href=\"https://tools.ietf.org/html/rfc6238\" target=\"_blank\">RFC6238</a></li></ul> 
Para calcular o valor OTP o algoritmo pega dois par�metros de entrada: uma chave secreta (valor inicial) e um valor de contador atual (o n�mero de ciclos de gera��o requeridos ou a hora atual dependendo do algoritmo). O valor inicial � salvo no dispositivo, bem como no site, uma vez que um dispositivo tenha sido inicializado. Se voc� estiver usando HOTP, o contador do dispositivo � incrementado a cada gera��o de OTP, enquanto o contador do servidor � alterado a cada autentica��o de OTP bem-sucedida. Se voc� estiver usando TOTP, nenhum contador � salvo no dispositivo, e o servidor mant�m o controle da poss�vel mudan�a de hor�rio do dispositivo a cada autentica��o de OTP bem-sucedida.<br> 
Cada dispositivo OTP em um lote inclui um arquivo criptografado que cont�m valores iniciais (chaves secretas) para cada dispositivo no lote, o arquivo est� vinculado ao n�mero de s�rie do dispositivo que pode ser encontrado no dispositivo.<br> 
Se os contadores do dispositivo e do servidor sa�rem fora de sincronia, eles podem ser facilmente sincronizados de volta, trazendo o valor do servidor para o do dispositivo. Para fazer isso, o administrador (ou um usu�rio com permiss�o apropriada) tem que gerar duas OTP's consecutivas e digit�-las no site.<br> 
Voc� pode encontrar o aplicativo para celular na AppStore e GooglePlay.";
$MESS["SEC_OTP_DESCRIPTION_ABOUT_TITLE"] = "Descri��o";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION"] = "Um c�digo avulso para autentica��o em duas etapas pode ser obtido utilizando um dispositivo especial (um dongle), ou um aplicativo gratuito para celular (Bitrix OTP) que cada usu�rio tem que ter instalado em seu dispositivo m�vel.<br> 
Para ativar um dongle, o administrador ter� que abrir o perfil do usu�rio e digitar as duas senhas geradas pelo.<br> 
Para obter um c�digo avulso em um dispositivo m�vel, o usu�rio pode baixar e executar o aplicativo, e digitalizar o c�digo QR na p�gina de configura��es no seu perfil de usu�rio ou inserir os dados de conta manualmente.";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION_TITLE"] = "Ativa��o";
$MESS["SEC_OTP_DESCRIPTION_INTRO_INTRANET"] = "Hoje, o usu�rio est� usando um par de login e senha para autenticar em seu Bitrix24. No entanto, existem ferramentas que uma pessoa mal-intencionada 
pode empregar para entrar em um computador e roubar esses dados, por exemplo, se um usu�rio salva sua senha.<br> 
<b>A autentica��o em duas etapas</b> � a op��o recomendada para proteger seu Bitrix24 contra software de hacker. Cada vez que um usu�rio entrar no sistema, ter� que passar dois n�veis de verifica��o. Primeiro, digite o login e a senha. Em seguida, digite o c�digo de seguran�a enviado para o seu dispositivo m�vel. A conclus�o � que um invasor n�o pode utilizar dados roubados porque ele n�o sabe o c�digo de seguran�a.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_SITE"] = "Hoje, um usu�rio est� usando um par de login e senha para autenticar em seu site. No entanto, existem ferramentas que uma pessoa mal-intencionada 
pode empregar para entrar em um computador e roubar esses dados, por exemplo, se um usu�rio salva sua senha.<br> 
<b>A autentica��o em duas etapas</b> � a op��o recomendada para proteger contra software de hacker. Cada vez que um usu�rio entrar no sistema, ter� que passar dois n�veis de verifica��o. Primeiro, digite o login e a senha. Em seguida, digite o c�digo de seguran�a enviado para o seu dispositivo m�vel. A conclus�o � que um invasor n�o pode utilizar dados roubados porque ele n�o sabe o c�digo de seguran�a.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_TITLE"] = "Senha avulsa";
$MESS["SEC_OTP_DESCRIPTION_USING"] = "Quando a autentica��o em duas etapas � ativada, um usu�rio ter� que passar dois n�veis de verifica��o ao fazer o login. <br> 
Primeiro, digite seu e-mail e senha, como de costume. <br> 
Em seguida, digite um c�digo de seguran�a avulso enviado para o seu dispositivo m�vel ou obtido usando um dongle exclusivo.";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_0"] = "Etapa 1";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_1"] = "Etapa 2";
$MESS["SEC_OTP_DESCRIPTION_USING_TITLE"] = "Usando Senhas Avulsas";
$MESS["SEC_OTP_DISABLE"] = "Desativar";
$MESS["SEC_OTP_ENABLE"] = "Ativar";
$MESS["SEC_OTP_ERROR_TITLE"] = "N�o � poss�vel salvar porque ocorreu um erro.";
$MESS["SEC_OTP_INIT"] = "Inicializa��o";
$MESS["SEC_OTP_MANDATORY_ALMOST_EXPIRED"] = "O per�odo de tempo durante o qual um usu�rio tem que configurar a autentica��o em duas etapas ir� expirar em #DATE#.";
$MESS["SEC_OTP_MANDATORY_DEFFER"] = "Ampliar";
$MESS["SEC_OTP_MANDATORY_DISABLED"] = "Autentica��o obrigat�ria em duas etapas desativada.";
$MESS["SEC_OTP_MANDATORY_ENABLE"] = "Requer a ativa��o da autentica��o em duas etapas dentro de";
$MESS["SEC_OTP_MANDATORY_ENABLE_DEFAULT"] = "Requer a ativa��o da autentica��o em duas etapas";
$MESS["SEC_OTP_MANDATORY_EXPIRED"] = "O per�odo de tempo durante o qual um usu�rio tinha que configurar a autentica��o em duas etapas expirou.";
$MESS["SEC_OTP_MOBILE_INPUT_METHODS_SEPARATOR"] = "ou";
$MESS["SEC_OTP_MOBILE_MANUAL_INPUT"] = "Digite o c�digo manualmente";
$MESS["SEC_OTP_MOBILE_SCAN_QR"] = "Ler o c�digo QR";
$MESS["SEC_OTP_NEW_ACCESS_DENIED"] = "O acesso ao controle de autentica��o em duas etapas foi negado.";
$MESS["SEC_OTP_NEW_SWITCH_ON"] = "Ativar autentica��o em duas etapas";
$MESS["SEC_OTP_NO_DAYS"] = "para sempre";
$MESS["SEC_OTP_PASS1"] = "A primeira senha do dispositivo (Clique e anote)";
$MESS["SEC_OTP_PASS2"] = "A segunda senha do dispositivo (clique novamente e anote)";
$MESS["SEC_OTP_RECOVERY_CODES_BUTTON"] = "C�digos de recupera��o";
$MESS["SEC_OTP_RECOVERY_CODES_DESCRIPTION"] = "Copie os c�digos de recupera��o que voc� pode precisar se perder seu dispositivo m�vel ou n�o puder obter um c�digo atrav�s do aplicativo por qualquer outro motivo.";
$MESS["SEC_OTP_RECOVERY_CODES_NOTE"] = "Um c�digo s� pode ser usado uma vez. Dica: tire c�digos utilizados da lista.";
$MESS["SEC_OTP_RECOVERY_CODES_PRINT"] = "Imprimir";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE"] = "Gerar novos c�digos";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE_DESCRIPTION"] = "Voc� tem poucos c�digos de recupera��o?<br/> 
Crie alguns novos. <br/><br/> 
Criar novos c�digos de recupera��o invalida <br/> os c�digos gerados anteriormente.";
$MESS["SEC_OTP_RECOVERY_CODES_SAVE_FILE"] = "Salvar em arquivo de texto";
$MESS["SEC_OTP_RECOVERY_CODES_TITLE"] = "C�digos de recupera��o";
$MESS["SEC_OTP_RECOVERY_CODES_WARNING"] = "Mantenha-os � m�o, ou seja, na sua carteira ou bolsa. Cada um dos c�digos s� pode ser utilizado uma vez.";
$MESS["SEC_OTP_SECRET_KEY"] = "Secret Key (fornecida com o aparelho)";
$MESS["SEC_OTP_STATUS"] = "Status atual";
$MESS["SEC_OTP_STATUS_ON"] = "Ativado";
$MESS["SEC_OTP_SYNC_NOW"] = "Sincronizar";
$MESS["SEC_OTP_TYPE"] = "Algoritmo de gera��o de senha";
$MESS["SEC_OTP_UNKNOWN_ERROR"] = "Erro inesperado. Tente novamente mais tarde.";
$MESS["SEC_OTP_WARNING_RECOVERY_CODES"] = "A autentica��o em duas etapas foi ativada, mas voc� n�o criou c�digos de recupera��o. Voc� poder� precisar deles se voc� perder seu dispositivo m�vel ou n�o puder obter um c�digo atrav�s do aplicativo por qualquer outro motivo.";
$MESS["security_TAB"] = "Senha tempor�ria";
$MESS["security_TAB_TITLE"] = "Configura��es de senhas tempor�rias";
?>