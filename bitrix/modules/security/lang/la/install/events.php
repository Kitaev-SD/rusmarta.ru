<?
$MESS["VIRUS_DETECTED_DESC"] = "#EMAIL# - Direcci�n de e-mail del administrador del sitio web (desde las configuraciones del M�dulo Principal)";
$MESS["VIRUS_DETECTED_MESSAGE"] = "Mensaje informativo de #SITE_NAME#
------------------------------------------
Usted recibe este mensaje debido a que la Protecci�n Proactiva ha #SERVER_NAME# ha detectado un c�digo potencialmente peligroso.
1.  El c�digo potencialmente peligroso, ha sido retirado del sitio web.
2. Revise el log de eventos para asegurarse de que el c�digo es realmente perjudicial, y no se trata de un simple contador o framework.
  (link: http://#SERVER_NAME#/bitrix/admin/event_log.php?lang=la&set_filter=Y&find_type=audit_type_id&find_audit_type[]=SECURITY_VIRUS)
3.  Si el c�digo no es peligroso, agr�guelo en el listado de excepciones en la p�gina de configuraciones del Antivirus.
  (link: http://#SERVER_NAME#/bitrix/admin/security_antivirus.php?lang=la&tabControl_active_tab=exceptions )
4.  Si el c�digo es un virus. Complete los siguientes pasos:

        a) Cambie la contrase�a del administrador y de otras personas responsables del sitio web.
        b) Cambie el login, y contrase�a de los accesos ssh y ftp. 
        c) Compruebe y elimine el virus de las computadoras de los administradores que tienen accesos ssh o ftp al sitio web.
         d) Cambie a apagado el guardado de contrase�as en programas que proveen acceso al sitio web mediante ssh o ftp. 
        e) Elimine el c�digo peligros desde los archivos infectados. Por ejemplo, reinstale los archivos infectados usando el m�s reciente backup realizado. 

---------------------------------------------------------------------
  Este mensaje se gener� autom�ticamente.";
$MESS["VIRUS_DETECTED_NAME"] = "Virus detectado";
$MESS["VIRUS_DETECTED_SUBJECT"] = "#SITE_NAME#:  Virus detectado";
?>