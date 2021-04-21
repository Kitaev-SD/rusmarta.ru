<?
$MESS["SEC_SESSION_ADMIN_DB_BUTTON_OFF"] = "Não armazenar dados da sessão no banco de dados do Módulo de Segurança";
$MESS["SEC_SESSION_ADMIN_DB_BUTTON_ON"] = "Armazenar dados da sessão no banco de dados do Módulo de Segurança";
$MESS["SEC_SESSION_ADMIN_DB_NOTE"] = "<p>A maioria dos ataques de roubos de dados de usário autorizado. 
Permitindo o <b>proteção da sessão</b> torna o session hijacking inútil. </p>
<p>Além das opções de sessão padrão de proteção que você pode definir nas preferências de grupos de usuários, a proteção proativa da sessão <b></b>:
<ul style='font-size:100%'>
<li> muda o ID da sessão periodicamente, e a freqüência pode ser definida; </li>
<li> armazena os dados de sessão na tabela do módulo. </li>
</ul>
<p>Armazenar os dados da sessão no banco de dados do módulo de dados, impede o roubo de dados por scripts rodando em outros servidores virtuais, o que elimina os erros de configuração de hospedagem virtual, configurações defeituosas de permissão de acesso à pasta temporária e outros problemas relacionados com o sistema operacional. Ele também reduz o estresse do sistema de arquivos, transferindo as operações para o servidor de banco de dados. </p>
<p><i>Recomendado para nível alto.</i></p> ";
$MESS["SEC_SESSION_ADMIN_DB_OFF"] = "Os dados da sessão não são armazenados no banco de dados do módulo de segurança.";
$MESS["SEC_SESSION_ADMIN_DB_ON"] = "Os dados da sessão são armazenados no banco de dados do módulo de segurança.";
$MESS["SEC_SESSION_ADMIN_DB_WARNING"] = "Atenção! Alternar/desligar o modo de sessão irá causar aos usuários autorizados a perda de autorização (os dados da sessão serão destruídos).";
$MESS["SEC_SESSION_ADMIN_SAVEDB_TAB"] = "Sessões em banco de dados";
$MESS["SEC_SESSION_ADMIN_SAVEDB_TAB_TITLE"] = "Configurar o armazenamento de dados da sessão no banco de dados";
$MESS["SEC_SESSION_ADMIN_SESSID_BUTTON_OFF"] = "Desativar a mudança de ID";
$MESS["SEC_SESSION_ADMIN_SESSID_BUTTON_ON"] = "Não a permitir a mudança do ID";
$MESS["SEC_SESSION_ADMIN_SESSID_NOTE"] = "<p>Se este recurso estiver ativo, o ID da sessão vai mudar após o período de tempo especificado. Isso aumenta a carga do servidor, mas, obviamente, faz o roubo de ID sem uso instantâneo absolutamente sem sentido. </p>
<p><i>Recomendado para nível alto.</i></p> ";
$MESS["SEC_SESSION_ADMIN_SESSID_OFF"] = "A troca do ID da sessão não está desabilitada.";
$MESS["SEC_SESSION_ADMIN_SESSID_ON"] = "A troca do ID da sessão não está habilitada.";
$MESS["SEC_SESSION_ADMIN_SESSID_TAB"] = "Mudança de ID";
$MESS["SEC_SESSION_ADMIN_SESSID_TAB_TITLE"] = "Configurar a troca periódica de ID da sessão";
$MESS["SEC_SESSION_ADMIN_SESSID_TTL"] = "A duração do ID da sessão,sec.";
$MESS["SEC_SESSION_ADMIN_SESSID_WARNING"] = "ID da sessão não é compatível com o módulo de proteção pró-ativo. O identificador retornou que a função session_id () não deve ter mais de 32 caracteres e deve conter apenas letras latinas ou números.";
$MESS["SEC_SESSION_ADMIN_TITLE"] = "Proteção da sessão";
?>