<?
$MESS["SEC_OTP_CONNECTED"] = "Conectado";
$MESS["SEC_OTP_CONNECT_DEVICE"] = "Conectar dongle";
$MESS["SEC_OTP_CONNECT_DEVICE_TITLE"] = "Conectar dongle";
$MESS["SEC_OTP_CONNECT_DONE"] = "Pronto";
$MESS["SEC_OTP_CONNECT_MOBILE"] = "Conectar dispositivo móvel";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_CODE"] = "Digite o código";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_NEXT_CODE"] = "Digite o próximo código";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_DESCRIPTION"] = "Uma vez que o código tenha sido digitalizado com sucesso ou inserido manualmente, seu celular irá mostrar o código que você terá que digitar abaixo.";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_NEXT_DESCRIPTION"] = "O algoritmo OTP requer dois códigos de autenticação. Gere o próximo código e digite-o abaixo.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT"] = "Para inserir os dados manualmente, especifique o endereço do site, seu e-mail ou login, um código secreto na imagem e selecione o tipo de chave.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_HOTP"] = "com base em contador";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_TOTP"] = "com base em tempo";
$MESS["SEC_OTP_CONNECT_MOBILE_SCAN_QR"] = "Leve seu dispositivo móvel para o monitor e aguarde enquanto o aplicativo faz a leitura do código.";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_1"] = "Baixe o aplicativo para celular Bitrix OTP para o seu celular na <a href=\"https://itunes.apple.com/en/app/bitrix24-otp/id929604673?l=en\"target=\"_new\">AppStore</a> em <a href=\"https://play.google.com/store/apps/details?id=com.bitrixsoft.otp\" target=\"_new\">GooglePlay</a>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_2"] = "Execute o aplicativo e clique em <b>Configurar</b>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_3"] = "Escolha como você deseja inserir os dados: utilizando código QR ou manualmente";
$MESS["SEC_OTP_CONNECT_MOBILE_TITLE"] = "Conectar dispositivo móvel";
$MESS["SEC_OTP_CONNECT_NEW_DEVICE"] = "Conectar novo dongle";
$MESS["SEC_OTP_CONNECT_NEW_MOBILE"] = "Conectar novo dispositivo móvel";
$MESS["SEC_OTP_DEACTIVATE_UNTIL"] = "Desativado até #DATE#";
$MESS["SEC_OTP_DESCRIPTION_ABOUT"] = "A Senha Avulsa (OTP) foi desenvolvida como parte da iniciativa OATH.<br> 
A OTP é baseada em HMAC e SHA-1/SHA-256/SHA-512. No momento, os dois algoritmos são suportados para gerar códigos:
<ul><li>com base em contador (HMAC-Baseado em Senha Avulsa, HOTP) conforme descrito <a href=\"https://tools.ietf.org/html/rfc4226\" target= \"_blank\">RFC4226</a></li> 
<li>baseado em tempo (Senha Avulsa com base em Tempo, TOTP) conforme descrito em <a href=\"https://tools.ietf.org/html/rfc6238\" target=\"_blank\">RFC6238</a></li></ul> 
Para calcular o valor OTP o algoritmo pega dois parâmetros de entrada: uma chave secreta (valor inicial) e um valor de contador atual (o número de ciclos de geração requeridos ou a hora atual dependendo do algoritmo). O valor inicial é salvo no dispositivo, bem como no site, uma vez que um dispositivo tenha sido inicializado. Se você estiver usando HOTP, o contador do dispositivo é incrementado a cada geração de OTP, enquanto o contador do servidor é alterado a cada autenticação de OTP bem-sucedida. Se você estiver usando TOTP, nenhum contador é salvo no dispositivo, e o servidor mantém o controle da possível mudança de horário do dispositivo a cada autenticação de OTP bem-sucedida.<br> 
Cada dispositivo OTP em um lote inclui um arquivo criptografado que contém valores iniciais (chaves secretas) para cada dispositivo no lote, o arquivo está vinculado ao número de série do dispositivo que pode ser encontrado no dispositivo.<br> 
Se os contadores do dispositivo e do servidor saírem fora de sincronia, eles podem ser facilmente sincronizados de volta, trazendo o valor do servidor para o do dispositivo. Para fazer isso, o administrador (ou um usuário com permissão apropriada) tem que gerar duas OTP's consecutivas e digitá-las no site.<br> 
Você pode encontrar o aplicativo para celular na AppStore e GooglePlay.";
$MESS["SEC_OTP_DESCRIPTION_ABOUT_TITLE"] = "Descrição";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION"] = "Um código avulso para autenticação em duas etapas pode ser obtido utilizando um dispositivo especial (um dongle), ou um aplicativo gratuito para celular (Bitrix OTP) que cada usuário tem que ter instalado em seu dispositivo móvel.<br> 
Para ativar um dongle, o administrador terá que abrir o perfil do usuário e digitar as duas senhas geradas pelo.<br> 
Para obter um código avulso em um dispositivo móvel, o usuário pode baixar e executar o aplicativo, e digitalizar o código QR na página de configurações no seu perfil de usuário ou inserir os dados de conta manualmente.";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION_TITLE"] = "Ativação";
$MESS["SEC_OTP_DESCRIPTION_INTRO_INTRANET"] = "Hoje, o usuário está usando um par de login e senha para autenticar em seu Bitrix24. No entanto, existem ferramentas que uma pessoa mal-intencionada 
pode empregar para entrar em um computador e roubar esses dados, por exemplo, se um usuário salva sua senha.<br> 
<b>A autenticação em duas etapas</b> é a opção recomendada para proteger seu Bitrix24 contra software de hacker. Cada vez que um usuário entrar no sistema, terá que passar dois níveis de verificação. Primeiro, digite o login e a senha. Em seguida, digite o código de segurança enviado para o seu dispositivo móvel. A conclusão é que um invasor não pode utilizar dados roubados porque ele não sabe o código de segurança.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_SITE"] = "Hoje, um usuário está usando um par de login e senha para autenticar em seu site. No entanto, existem ferramentas que uma pessoa mal-intencionada 
pode empregar para entrar em um computador e roubar esses dados, por exemplo, se um usuário salva sua senha.<br> 
<b>A autenticação em duas etapas</b> é a opção recomendada para proteger contra software de hacker. Cada vez que um usuário entrar no sistema, terá que passar dois níveis de verificação. Primeiro, digite o login e a senha. Em seguida, digite o código de segurança enviado para o seu dispositivo móvel. A conclusão é que um invasor não pode utilizar dados roubados porque ele não sabe o código de segurança.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_TITLE"] = "Senha avulsa";
$MESS["SEC_OTP_DESCRIPTION_USING"] = "Quando a autenticação em duas etapas é ativada, um usuário terá que passar dois níveis de verificação ao fazer o login. <br> 
Primeiro, digite seu e-mail e senha, como de costume. <br> 
Em seguida, digite um código de segurança avulso enviado para o seu dispositivo móvel ou obtido usando um dongle exclusivo.";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_0"] = "Etapa 1";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_1"] = "Etapa 2";
$MESS["SEC_OTP_DESCRIPTION_USING_TITLE"] = "Usando Senhas Avulsas";
$MESS["SEC_OTP_DISABLE"] = "Desativar";
$MESS["SEC_OTP_ENABLE"] = "Ativar";
$MESS["SEC_OTP_ERROR_TITLE"] = "Não é possível salvar porque ocorreu um erro.";
$MESS["SEC_OTP_INIT"] = "Inicialização";
$MESS["SEC_OTP_MANDATORY_ALMOST_EXPIRED"] = "O período de tempo durante o qual um usuário tem que configurar a autenticação em duas etapas irá expirar em #DATE#.";
$MESS["SEC_OTP_MANDATORY_DEFFER"] = "Ampliar";
$MESS["SEC_OTP_MANDATORY_DISABLED"] = "Autenticação obrigatória em duas etapas desativada.";
$MESS["SEC_OTP_MANDATORY_ENABLE"] = "Requer a ativação da autenticação em duas etapas dentro de";
$MESS["SEC_OTP_MANDATORY_ENABLE_DEFAULT"] = "Requer a ativação da autenticação em duas etapas";
$MESS["SEC_OTP_MANDATORY_EXPIRED"] = "O período de tempo durante o qual um usuário tinha que configurar a autenticação em duas etapas expirou.";
$MESS["SEC_OTP_MOBILE_INPUT_METHODS_SEPARATOR"] = "ou";
$MESS["SEC_OTP_MOBILE_MANUAL_INPUT"] = "Digite o código manualmente";
$MESS["SEC_OTP_MOBILE_SCAN_QR"] = "Ler o código QR";
$MESS["SEC_OTP_NEW_ACCESS_DENIED"] = "O acesso ao controle de autenticação em duas etapas foi negado.";
$MESS["SEC_OTP_NEW_SWITCH_ON"] = "Ativar autenticação em duas etapas";
$MESS["SEC_OTP_NO_DAYS"] = "para sempre";
$MESS["SEC_OTP_PASS1"] = "A primeira senha do dispositivo (Clique e anote)";
$MESS["SEC_OTP_PASS2"] = "A segunda senha do dispositivo (clique novamente e anote)";
$MESS["SEC_OTP_RECOVERY_CODES_BUTTON"] = "Códigos de recuperação";
$MESS["SEC_OTP_RECOVERY_CODES_DESCRIPTION"] = "Copie os códigos de recuperação que você pode precisar se perder seu dispositivo móvel ou não puder obter um código através do aplicativo por qualquer outro motivo.";
$MESS["SEC_OTP_RECOVERY_CODES_NOTE"] = "Um código só pode ser usado uma vez. Dica: tire códigos utilizados da lista.";
$MESS["SEC_OTP_RECOVERY_CODES_PRINT"] = "Imprimir";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE"] = "Gerar novos códigos";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE_DESCRIPTION"] = "Você tem poucos códigos de recuperação?<br/> 
Crie alguns novos. <br/><br/> 
Criar novos códigos de recuperação invalida <br/> os códigos gerados anteriormente.";
$MESS["SEC_OTP_RECOVERY_CODES_SAVE_FILE"] = "Salvar em arquivo de texto";
$MESS["SEC_OTP_RECOVERY_CODES_TITLE"] = "Códigos de recuperação";
$MESS["SEC_OTP_RECOVERY_CODES_WARNING"] = "Mantenha-os à mão, ou seja, na sua carteira ou bolsa. Cada um dos códigos só pode ser utilizado uma vez.";
$MESS["SEC_OTP_SECRET_KEY"] = "Secret Key (fornecida com o aparelho)";
$MESS["SEC_OTP_STATUS"] = "Status atual";
$MESS["SEC_OTP_STATUS_ON"] = "Ativado";
$MESS["SEC_OTP_SYNC_NOW"] = "Sincronizar";
$MESS["SEC_OTP_TYPE"] = "Algoritmo de geração de senha";
$MESS["SEC_OTP_UNKNOWN_ERROR"] = "Erro inesperado. Tente novamente mais tarde.";
$MESS["SEC_OTP_WARNING_RECOVERY_CODES"] = "A autenticação em duas etapas foi ativada, mas você não criou códigos de recuperação. Você poderá precisar deles se você perder seu dispositivo móvel ou não puder obter um código através do aplicativo por qualquer outro motivo.";
$MESS["security_TAB"] = "Senha temporária";
$MESS["security_TAB_TITLE"] = "Configurações de senhas temporárias";
?>