<?
$MESS["SUBSCRIBE_CONFIRM_DESC"] = "#ID# - ID de la suscripci�n
#EMAIL# - email de suscripci�n
#CONFIRM_CODE# - c�digo de confirmaci�n
#SUBSCR_SECTION# - secci�n con suscripci�n de la p�gina(especificar las configuraciones)
#USER_NAME# - nombre del suscriptor (puede estar ausente)
#DATE_SUBSCR# - fecha de adici�n/cambio de direcci�n";
$MESS["SUBSCRIBE_CONFIRM_MESSAGE"] = "Mensaje interno de #SITE_NAME#
---------------------------------------

Hola,

Usted ha recibido este mensaje por que su direcci�n est� suscrita para noticias desde #SERVER_NAME#.

Aqu� est� la informaci�n al detalle sobre sus suscripci�n:

email de suscripci�n .............. #EMAIL#
Fecha del mail adici�n/edici�n .... #DATE_SUBSCR#

Su c�digo de confirmaci�n: #CONFIRM_CODE#

Por favor visite el link de este mensaje para confirmar su suscripci�n
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#

O vaya a esta p�gina e ingrese su c�digo de confirmaci�n manualmente:
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#

Usted no podr� recibir ning�n mensaje hasta que nos env�e su confirmaci�n.

---------------------------------------------------------------------
Por favor guarde este mensaje puesto que contiene informaci�n sobre la autorizaci�n
Usando su c�digo de confirmaci�n usted podr� cambiar los par�metros de la suscripci�n.
Editar par�metros:
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#

No suscribir:
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#&action=unsubscribe
---------------------------------------------------------------------

Este mensaje fue generado autom�ticamente.";
$MESS["SUBSCRIBE_CONFIRM_NAME"] = "Confirmaci�n de suscripci�n";
$MESS["SUBSCRIBE_CONFIRM_SUBJECT"] = "#SITE_NAME#: Confirmaci�n de suscripci�n";
?>