<?
$MESS["VIRUS_DETECTED_DESC"] = "#EMAIL# - endereço do administrador do site de e-mail (a partir das configurações do módulo do kernel)";
$MESS["VIRUS_DETECTED_MESSAGE"] = "Mensagem informativa de #SITE_NAME# 

------------------------------------------ 

Você recebeu esta mensagem como um resultado da detecção de código potencialmente perigoso pelo sistema de proteção pró-ativa de #SERVER_NAME#. 

1. O código potencialmente perigoso foi cortado do html. 
2. Verifique o log de eventos e certifique-se de que o código é realmente prejudicial, e não é simplesmente um contador ou quadro. 
(Link:http://#SERVER_NAME#/bitrix/admin/event_log.php?lang=en&set_filter=Y&find_type=audit_type_id&find_audit_type[]=SECURITY_VIRUS) 
3. Se o código não é prejudicial, adicioná-lo à lista das \"exceções\" na página de configurações de antivírus. 
(Link: http://#SERVER_NAME#/bitrix/admin/security_antivirus.php?lang=en&tabControl_active_tab=exceptions )) 
4. Se o código for um vírus, então siga os seguintes passos: 

a) Altere a senha de login para o administrador e outros usuários responsáveis para o site. 
b) Alterae a senha de login para ssh e ftp. 
c) Testar e remover vírus de computadores de administradores que possuem acesso ao site através de SSH ou FTP. 
d) Desligue o salvamento de senha em programas que permitem o acesso ao site através de SSH ou FTP. 
e) Excluir o código nocivo dos arquivos infectados. Por exemplo, re-instalar os arquivos infectados usando o backup mais recente. 

--------------------------------------------------------------------- 
Esta mensagem foi gerada automaticamente....";
$MESS["VIRUS_DETECTED_NAME"] = "Vírus detectado";
$MESS["VIRUS_DETECTED_SUBJECT"] = "#SITE_NAME#: Vírus detectado";
?>