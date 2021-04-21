<?
$MESS["SUP_SE_TICKET_CHANGE_BY_AUTHOR_FOR_AUTHOR_TEXT"] = "#ID# - ID del ticket
#LANGUAGE_ID# - ID del idioma del sitio web cuyo ticket de problema est� vinculado a
#DATE_CREATE# - fecha de creaci�n
#TIMESTAMP# - fecha del cambio
#DATE_CLOSE# - fecha del cierre
#TITLE# - t�tulo del ticket
#CATEGORY# - categor�a del ticket
#STATUS# - estatus del ticket
#CRITICALITY# - criticalidad del ticket
#SLA# - nivel de soporte
#SOURCE# - fuente del ticket (web, email, tel�fono etc.)
#SPAM_MARK# - marca del spam
#MESSAGE_BODY# - cuerpo del mensaje
#FILES_LINKS# - v�nculos para archivos adjuntos
#ADMIN_EDIT_URL# - v�nculo para el cambio del ticket (secci�n administrativa)
#PUBLIC_EDIT_URL# - v�nculo para el cambio del ticket (secci�n p�blica)

#OWNER_EMAIL# - #OWNER_USER_EMAIL# and/or #OWNER_SID#
#OWNER_USER_ID# - ID del ticket del autor
#OWNER_USER_NAME# - nombre del autor del ticket
#OWNER_USER_LOGIN# - autor de inicio de sesi�n del ticket
#OWNER_USER_EMAIL# -  email del autor del ticket
#OWNER_TEXT# - [#OWNER_USER_ID#] (#OWNER_USER_LOGIN#) #OWNER_USER_NAME#
#OWNER_SID# - alguna identificaci�n del autor del ticket(como email, tel�fono)

#SUPPORT_EMAIL# - #RESPONSIBLE_USER_EMAIL# or #SUPPORT_ADMIN_EMAIL#
#RESPONSIBLE_USER_NAME# - Nombre completo de la persona a cargo
#RESPONSIBLE_USER_ID# - ID del usuario de la persona a cargo
#RESPONSIBLE_USER_EMAIL# - email de la persona a cargo
#RESPONSIBLE_USER_LOGIN# - inicio de sesi�n de la persona a cargo
#RESPONSIBLE_TEXT# - [#RESPONSIBLE_USER_ID#] (#RESPONSIBLE_USER_LOGIN#) #RESPONSIBLE_USER_NAME#
#SUPPORT_ADMIN_EMAIL# - emails de los administradores de soporte

#CREATED_USER_ID# - ID del creador del ticket
#CREATED_USER_LOGIN# - creador de la sesi�n del ticket
#CREATED_USER_EMAIL# - creador del email del ticket
#CREATED_USER_NAME# - nombre del creador del ticket
#CREATED_MODULE_NAME# - ID del m�dulo que fue usado para crear el ticket
#CREATED_TEXT# - [#CREATED_USER_ID#] (#CREATED_USER_LOGIN#) #CREATED_USER_NAME#

#SUPPORT_COMMENTS# - comentarios administrativos

";
$MESS["SUP_SE_TICKET_CHANGE_BY_AUTHOR_FOR_AUTHOR_TITLE"] = "Ticket fue cambiado por el autor (para el autor)";
$MESS["SUP_SE_TICKET_CHANGE_BY_SUPPORT_FOR_AUTHOR_MESSAGE"] = "Cambios en su solicitud # #ID# at #SERVER_NAME#.

#WHAT_CHANGE#
Asunto: #TITLE#

Desde: #MESSAGE_SOURCE##MESSAGE_AUTHOR_SID##MESSAGE_AUTHOR_TEXT#

>======================== MENSAJE ====================================#FILES_LINKS##MESSAGE_BODY#
>=====================================================================

Autor  - #SOURCE##OWNER_SID##OWNER_TEXT#
Creado - #CREATED_TEXT##CREATED_MODULE_NAME# [#DATE_CREATE#]
Cambiado - #MODIFIED_TEXT##MODIFIED_MODULE_NAME# [#TIMESTAMP#]

Responsable   - #RESPONSIBLE_TEXT#
Categor�a      - #CATEGORY#
Criticalidad   - #CRITICALITY#
Estatus        - #STATUS#
Rango         - #RATE#
Nivel de soporte - #SLA#

Para ver y editar la solicitud visitar enlace:
http://#SERVER_NAME##PUBLIC_EDIT_URL#?ID=#ID#

Le pedimos que no se olvide del registro de respuestas techsupport despu�s del cierre de la solicitud:http://#SERVER_NAME##PUBLIC_EDIT_URL#?ID=#ID#

Mensaje generado autom�ticamente.";
$MESS["SUP_SE_TICKET_CHANGE_BY_SUPPORT_FOR_AUTHOR_SUBJECT"] = "[TID##ID#] #SERVER_NAME#: Cambios en su solicitud";
$MESS["SUP_SE_TICKET_CHANGE_BY_SUPPORT_FOR_AUTHOR_TEXT"] = "#ID# - ID del ticket
#LANGUAGE_ID# - ID del idioma del sitio web cuyo ticket de problema est� vinculado a
#DATE_CREATE# - fecha de creaci�n
#TIMESTAMP# - fecha del cambio
#DATE_CLOSE# - fecha del cierre
#TITLE# - t�tulo del ticket
#CATEGORY# - categor�a del ticket
#STATUS# - estatus del ticket
#CRITICALITY# - criticalidad del ticket
#SLA# - nivel de soporte
#SOURCE# - fuente del ticket (web, email, tel�fono etc.)
#SPAM_MARK# - marca del spam
#MESSAGE_BODY# - cuerpo del mensaje
#FILES_LINKS# - v�nculos para archivos adjuntos
#ADMIN_EDIT_URL# - v�nculo para el cambio del ticket (secci�n administrativa)
#PUBLIC_EDIT_URL# - v�nculo para el cambio del ticket (secci�n p�blica)

#OWNER_EMAIL# - #OWNER_USER_EMAIL# and/or #OWNER_SID#
#OWNER_USER_ID# - ID del ticket del autor
#OWNER_USER_NAME# - nombre del autor del ticket
#OWNER_USER_LOGIN# - autor de inicio de sesi�n del ticket
#OWNER_USER_EMAIL# -  email del autor del ticket
#OWNER_TEXT# - [#OWNER_USER_ID#] (#OWNER_USER_LOGIN#) #OWNER_USER_NAME#
#OWNER_SID# - alguna identificaci�n del autor del ticket(como email, tel�fono)

#SUPPORT_EMAIL# - #RESPONSIBLE_USER_EMAIL# or #SUPPORT_ADMIN_EMAIL#
#RESPONSIBLE_USER_NAME# - Nombre completo de la persona a cargo
#RESPONSIBLE_USER_ID# - ID del usuario de la persona a cargo
#RESPONSIBLE_USER_EMAIL# - email de la persona a cargo
#RESPONSIBLE_USER_LOGIN# - inicio de sesi�n de la persona a cargo
#RESPONSIBLE_TEXT# - [#RESPONSIBLE_USER_ID#] (#RESPONSIBLE_USER_LOGIN#) #RESPONSIBLE_USER_NAME#
#SUPPORT_ADMIN_EMAIL# - emails de los administradores de soporte

#CREATED_USER_ID# - ID del creador del ticket
#CREATED_USER_LOGIN# - creador de la sesi�n del ticket
#CREATED_USER_EMAIL# - creador del email del ticket
#CREATED_USER_NAME# - nombre del creador del ticket
#CREATED_MODULE_NAME# - ID del m�dulo que fue usado para crear el ticket
#CREATED_TEXT# - [#CREATED_USER_ID#] (#CREATED_USER_LOGIN#) #CREATED_USER_NAME#

#SUPPORT_COMMENTS# - comentarios administrativos

";
$MESS["SUP_SE_TICKET_CHANGE_BY_SUPPORT_FOR_AUTHOR_TITLE"] = "Ticket fue cambiado por un miembro del soporte t�cnico (para el autor)";
$MESS["SUP_SE_TICKET_CHANGE_FOR_TECHSUPPORT_MESSAGE"] = "Cambios en la solicitud  # #ID# at #SERVER_NAME#.
#SPAM_MARK#
#WHAT_CHANGE#
Asunto: #TITLE#

Desde: #MESSAGE_SOURCE##MESSAGE_AUTHOR_SID##MESSAGE_AUTHOR_TEXT#

>#MESSAGE_HEADER##FILES_LINKS##MESSAGE_BODY#
>#MESSAGE_FOOTER#

Autor  - #SOURCE##OWNER_SID##OWNER_TEXT#
Creado - #CREATED_TEXT##CREATED_MODULE_NAME# [#DATE_CREATE#]
Cambiado - #MODIFIED_TEXT##MODIFIED_MODULE_NAME# [#TIMESTAMP#]

Responsable   - #RESPONSIBLE_TEXT#
Categor�a      - #CATEGORY#
Criticalidad  - #CRITICALITY#
Estado        - #STATUS#
Clase          - #RATE#
Nivel de soporte - #SLA#

>======================= COMENTARIOS ===================================#SUPPORT_COMMENTS#
>====================================================================

Para ver y editar la solicitud visitar el link:
http://#SERVER_NAME##ADMIN_EDIT_URL#?ID=#ID#&lang=#LANGUAGE_ID#

Mensaje generado autom�ticamente.";
$MESS["SUP_SE_TICKET_CHANGE_FOR_TECHSUPPORT_SUBJECT"] = "[TID##ID#] #SERVER_NAME#: Cambios en solicitud";
$MESS["SUP_SE_TICKET_CHANGE_FOR_TECHSUPPORT_TEXT"] = "#ID# - ID del ticket
#LANGUAGE_ID# - ID del idioma del sitio web cuyo ticket de problema est� vinculado a
#DATE_CREATE# - fecha de creaci�n
#TIMESTAMP# - fecha del cambio
#DATE_CLOSE# - fecha del cierre
#TITLE# - t�tulo del ticket
#CATEGORY# - categor�a del ticket
#STATUS# - estatus del ticket
#CRITICALITY# - criticalidad del ticket
#SLA# - nivel de soporte
#SOURCE# - fuente del ticket (web, email, tel�fono etc.)
#SPAM_MARK# - marca del spam
#MESSAGE_BODY# - cuerpo del mensaje
#FILES_LINKS# - v�nculos para archivos adjuntos
#ADMIN_EDIT_URL# - v�nculo para el cambio del ticket (secci�n administrativa)
#PUBLIC_EDIT_URL# - v�nculo para el cambio del ticket (secci�n p�blica)

#OWNER_EMAIL# - #OWNER_USER_EMAIL# and/or #OWNER_SID#
#OWNER_USER_ID# - ID del ticket del autor
#OWNER_USER_NAME# - nombre del autor del ticket
#OWNER_USER_LOGIN# - autor de inicio de sesi�n del ticket
#OWNER_USER_EMAIL# -  email del autor del ticket
#OWNER_TEXT# - [#OWNER_USER_ID#] (#OWNER_USER_LOGIN#) #OWNER_USER_NAME#
#OWNER_SID# - alguna identificaci�n del autor del ticket(como email, tel�fono)

#SUPPORT_EMAIL# - #RESPONSIBLE_USER_EMAIL# or #SUPPORT_ADMIN_EMAIL#
#RESPONSIBLE_USER_NAME# - Nombre completo de la persona a cargo
#RESPONSIBLE_USER_ID# - ID del usuario de la persona a cargo
#RESPONSIBLE_USER_EMAIL# - email de la persona a cargo
#RESPONSIBLE_USER_LOGIN# - inicio de sesi�n de la persona a cargo
#RESPONSIBLE_TEXT# - [#RESPONSIBLE_USER_ID#] (#RESPONSIBLE_USER_LOGIN#) #RESPONSIBLE_USER_NAME#
#SUPPORT_ADMIN_EMAIL# - emails de los administradores de soporte

#CREATED_USER_ID# - ID del creador del ticket
#CREATED_USER_LOGIN# - creador de la sesi�n del ticket
#CREATED_USER_EMAIL# - creador del email del ticket
#CREATED_USER_NAME# - nombre del creador del ticket
#CREATED_MODULE_NAME# - ID del m�dulo que fue usado para crear el ticket
#CREATED_TEXT# - [#CREATED_USER_ID#] (#CREATED_USER_LOGIN#) #CREATED_USER_NAME#

#SUPPORT_COMMENTS# - comentarios administrativos

";
$MESS["SUP_SE_TICKET_CHANGE_FOR_TECHSUPPORT_TITLE"] = "Cambios en el ticket (para soporte t�cnico)";
$MESS["SUP_SE_TICKET_GENERATE_SUPERCOUPON_TEXT"] = "#COUPON# - Cup�n
#COUPON_ID# - ID del Cup�n
#DATE# - Fecha de uso
#USER_ID# -  ID del usuario
#SESSION_ID# - ID de la sesi�n
#GUEST_ID# - ID del Invitado";
$MESS["SUP_SE_TICKET_GENERATE_SUPERCOUPON_TITLE"] = "Cup�n activado";
$MESS["SUP_SE_TICKET_NEW_FOR_AUTHOR_MESSAGE"] = "Su solicitud ha sido aceptada con n�mero �nico #ID#.

Por favor, no responda a este mensaje. Esta s�lo genera una
confirmaci�n, lo que demuestra que techsupport ha recibido su solicitud y est� trabajando en ello.

Informaci�n sobre su solicitud:

Asunto       - #TITLE#
Desde        - #SOURCE##OWNER_SID##OWNER_TEXT#
Categor�a     - #CATEGORY#
Criticalidad  - #CRITICALITY#

Creado      - #CREATED_TEXT##CREATED_MODULE_NAME# [#DATE_CREATE#]
Responsable   - #RESPONSIBLE_TEXT#
Nivel de soporte - #SLA#

>======================== MENSAJE ====================================

#FILES_LINKS##MESSAGE_BODY#

>=====================================================================

Para ver y editar la solicitud visitar enlace:
http://#SERVER_NAME##PUBLIC_EDIT_URL#?ID=#ID#

Mensaje generado autom�ticamente.";
$MESS["SUP_SE_TICKET_NEW_FOR_AUTHOR_SUBJECT"] = "[TID##ID#] #SERVER_NAME#: Su solicitud ha sido aceptada con �xito";
$MESS["SUP_SE_TICKET_NEW_FOR_AUTHOR_TEXT"] = "
#ID# - ID del ticket
#LANGUAGE_ID# - ID del idioma del sitio web cuyo ticket de problema est� vinculado a
#DATE_CREATE# - fecha de creaci�n
#TIMESTAMP# - fecha del cambio
#DATE_CLOSE# - fecha del cierre
#TITLE# - t�tulo del ticket
#CATEGORY# - categor�a del ticket
#STATUS# - estatus del ticket
#CRITICALITY# - criticalidad del ticket
#SLA# - nivel de soporte
#SOURCE# - fuente del ticket (web, email, tel�fono etc.)
#SPAM_MARK# - marca del spam
#MESSAGE_BODY# - cuerpo del mensaje
#FILES_LINKS# - v�nculos para archivos adjuntos
#ADMIN_EDIT_URL# - v�nculo para el cambio del ticket (secci�n administrativa)
#PUBLIC_EDIT_URL# - v�nculo para el cambio del ticket (secci�n p�blica)

#OWNER_EMAIL# - #OWNER_USER_EMAIL# and/or #OWNER_SID#
#OWNER_USER_ID# - ID del ticket del autor
#OWNER_USER_NAME# - nombre del autor del ticket
#OWNER_USER_LOGIN# - autor de inicio de sesi�n del ticket
#OWNER_USER_EMAIL# -  email del autor del ticket
#OWNER_TEXT# - [#OWNER_USER_ID#] (#OWNER_USER_LOGIN#) #OWNER_USER_NAME#
#OWNER_SID# - alguna identificaci�n del autor del ticket(como email, tel�fono)

#SUPPORT_EMAIL# - #RESPONSIBLE_USER_EMAIL# or #SUPPORT_ADMIN_EMAIL#
#RESPONSIBLE_USER_NAME# - Nombre completo de la persona a cargo
#RESPONSIBLE_USER_ID# - ID del usuario de la persona a cargo
#RESPONSIBLE_USER_EMAIL# - email de la persona a cargo
#RESPONSIBLE_USER_LOGIN# - inicio de sesi�n de la persona a cargo
#RESPONSIBLE_TEXT# - [#RESPONSIBLE_USER_ID#] (#RESPONSIBLE_USER_LOGIN#) #RESPONSIBLE_USER_NAME#
#SUPPORT_ADMIN_EMAIL# - emails de los administradores de soporte

#CREATED_USER_ID# - ID del creador del ticket
#CREATED_USER_LOGIN# - creador de la sesi�n del ticket
#CREATED_USER_EMAIL# - creador del email del ticket
#CREATED_USER_NAME# - nombre del creador del ticket
#CREATED_MODULE_NAME# - ID del m�dulo que fue usado para crear el ticket
#CREATED_TEXT# - [#CREATED_USER_ID#] (#CREATED_USER_LOGIN#) #CREATED_USER_NAME#

#SUPPORT_COMMENTS# - comentarios administrativos
";
$MESS["SUP_SE_TICKET_NEW_FOR_AUTHOR_TITLE"] = "Nuevo ticket (para el autor)";
$MESS["SUP_SE_TICKET_NEW_FOR_TECHSUPPORT_MESSAGE"] = "Nueva solicitud # #ID# at #SERVER_NAME#.
#SPAM_MARK#
Desde: #SOURCE##OWNER_SID##OWNER_TEXT#
Asunto: #TITLE#

>======================== MENSAJE ====================================

#FILES_LINKS##MESSAGE_BODY#

>=====================================================================

Responsable   - #RESPONSIBLE_TEXT#
Categor�a     - #CATEGORY#
Criticalidad   - #CRITICALITY#
Nivel de soporte - #SLA#
Creado       - #CREATED_TEXT##CREATED_MODULE_NAME# [#DATE_CREATE#]

Para ver y editar la solicitud visitar enlace:
http://#SERVER_NAME##ADMIN_EDIT_URL#?ID=#ID#&lang=#LANGUAGE_ID#

Mensaje generado autom�ticamente.";
$MESS["SUP_SE_TICKET_NEW_FOR_TECHSUPPORT_SUBJECT"] = "[TID##ID#] #SERVER_NAME#: Nueva solicitud";
$MESS["SUP_SE_TICKET_NEW_FOR_TECHSUPPORT_TEXT"] = "#ID# - ID del ticket
#LANGUAGE_ID# - ID del idioma del sitio web cuyo ticket de problema est� vinculado a
#DATE_CREATE# - fecha de creaci�n
#TIMESTAMP# - fecha del cambio
#DATE_CLOSE# - fecha del cierre
#TITLE# - t�tulo del ticket
#CATEGORY# - categor�a del ticket
#STATUS# - estatus del ticket
#CRITICALITY# - criticalidad del ticket
#SLA# - nivel de soporte
#SOURCE# - fuente del ticket (web, email, tel�fono etc.)
#SPAM_MARK# - marca del spam
#MESSAGE_BODY# - cuerpo del mensaje
#FILES_LINKS# - v�nculos para archivos adjuntos
#ADMIN_EDIT_URL# - v�nculo para el cambio del ticket (secci�n administrativa)
#PUBLIC_EDIT_URL# - v�nculo para el cambio del ticket (secci�n p�blica)

#OWNER_EMAIL# - #OWNER_USER_EMAIL# and/or #OWNER_SID#
#OWNER_USER_ID# - ID del ticket del autor
#OWNER_USER_NAME# - nombre del autor del ticket
#OWNER_USER_LOGIN# - autor de inicio de sesi�n del ticket
#OWNER_USER_EMAIL# -  email del autor del ticket
#OWNER_TEXT# - [#OWNER_USER_ID#] (#OWNER_USER_LOGIN#) #OWNER_USER_NAME#
#OWNER_SID# - alguna identificaci�n del autor del ticket(como email, tel�fono)

#SUPPORT_EMAIL# - #RESPONSIBLE_USER_EMAIL# or #SUPPORT_ADMIN_EMAIL#
#RESPONSIBLE_USER_NAME# - Nombre completo de la persona a cargo
#RESPONSIBLE_USER_ID# - ID del usuario de la persona a cargo
#RESPONSIBLE_USER_EMAIL# - email de la persona a cargo
#RESPONSIBLE_USER_LOGIN# - inicio de sesi�n de la persona a cargo
#RESPONSIBLE_TEXT# - [#RESPONSIBLE_USER_ID#] (#RESPONSIBLE_USER_LOGIN#) #RESPONSIBLE_USER_NAME#
#SUPPORT_ADMIN_EMAIL# - emails de los administradores de soporte

#CREATED_USER_ID# - ID del creador del ticket
#CREATED_USER_LOGIN# - creador de la sesi�n del ticket
#CREATED_USER_EMAIL# - creador del email del ticket
#CREATED_USER_NAME# - nombre del creador del ticket
#CREATED_MODULE_NAME# - ID del m�dulo que fue usado para crear el ticket
#CREATED_TEXT# - [#CREATED_USER_ID#] (#CREATED_USER_LOGIN#) #CREATED_USER_NAME#

#SUPPORT_COMMENTS# - comentarios administrativos

#COUPON# - cup�n";
$MESS["SUP_SE_TICKET_NEW_FOR_TECHSUPPORT_TITLE"] = "Nuevo ticket (para soporte t�cnico)";
$MESS["SUP_SE_TICKET_OVERDUE_REMINDER_MESSAGE"] = "Recordatorio de necesidad de respuesta para el ticket # #ID# at #SERVER_NAME#.

Fecha de expiraci�n - #EXPIRATION_DATE# (permanece: #REMAINED_TIME#)

>======================= DATA TICKET =================================

Asunto - #TITLE#

Autor  - #SOURCE##OWNER_SID##OWNER_TEXT#
Creado - #CREATED_TEXT##CREATED_MODULE_NAME# [#DATE_CREATE#]

Nivel de soporte - #SLA#

Responsable   - #RESPONSIBLE_TEXT#
Categor�a      - #CATEGORY#
Criticalidad   - #CRITICALITY#
Estatus        - #STATUS#
Claae de respuesta  - #RATE#

>================= Mensaje requeire respuesta ===========================
#MESSAGE_BODY#
>=====================================================================

Para ver y editar la solicitud visitar el link:
http://#SERVER_NAME##ADMIN_EDIT_URL#?ID=#ID#&lang=#LANGUAGE_ID#

Mensaje generado autom�ticamente.";
$MESS["SUP_SE_TICKET_OVERDUE_REMINDER_SUBJECT"] = "[TID##ID#] #SERVER_NAME#: Recordatorio de la necesidad de la respuesta";
$MESS["SUP_SE_TICKET_OVERDUE_REMINDER_TEXT"] = "#ID# - ID del ticket
#LANGUAGE_ID# - ID del idioma del sitio web cuyo ticket de problema est� vinculado a
#DATE_CREATE# - fecha de creaci�n
#TITLE# - t�tulo del ticket
#STATUS# - estatus del ticket
#CATEGORY# - categor�a del ticket
#CRITICALITY# - criticalidad del ticket
#RATE# - rango de respuesta
#SLA# - nivel de soporte
#SOURCE# - fuente inicial del ticket (web, email, tel�fono etc.)
#ADMIN_EDIT_URL# - v�nculo al cambio del ticket (secci�n administrativa)

#EXPIRATION_DATE# - fecha de expiraci�n de respuesta
#REMAINED_TIME# - cu�nto queda hasta la fecha de vencimiento de la respuesta
#OWNER_EMAIL# - #OWNER_USER_EMAIL# and/or #OWNER_SID#
#OWNER_USER_ID# - ID del autor del ticket
#OWNER_USER_NAME# - nombre del autor del ticket
#OWNER_USER_LOGIN# - sesi�n del autor del ticket
#OWNER_USER_EMAIL# - email del autor del ticket
#OWNER_TEXT# - [#OWNER_USER_ID#] (#OWNER_USER_LOGIN#) #OWNER_USER_NAME#
#OWNER_SID# - algun identificador del autor del ticket(como email, phone y el gusto)

#SUPPORT_EMAIL# - #RESPONSIBLE_USER_EMAIL# or #SUPPORT_ADMIN_EMAIL#
#RESPONSIBLE_USER_NAME# - nombre completo de la persona a cargo
#RESPONSIBLE_USER_ID# - ID del usuario de la persona a cargo
#RESPONSIBLE_USER_EMAIL# - email de la persona a cargo
#RESPONSIBLE_USER_LOGIN# - sesi�n de la persona a cargo
#RESPONSIBLE_TEXT# - [#RESPONSIBLE_USER_ID#] (#RESPONSIBLE_USER_LOGIN#) #RESPONSIBLE_USER_NAME#
#SUPPORT_ADMIN_EMAIL# - EMails de los administradores de soporte

#CREATED_USER_ID# - ID del creador del ticket
#CREATED_USER_LOGIN# - sesi�n del creador del ticket
#CREATED_USER_EMAIL# - email del creador del ticket
#CREATED_USER_NAME# - nombre del creador del ticket
#CREATED_MODULE_NAME# - ID del m�dulo que fue usado para la creaci�on del ticket
#CREATED_TEXT# - [#CREATED_USER_ID#] (#CREATED_USER_LOGIN#) #CREATED_USER_NAME#

#MESSAGE_BODY# - cuerpo del mensaje el cual requiere una respuesta
";
$MESS["SUP_SE_TICKET_OVERDUE_REMINDER_TITLE"] = "Recordatorio de necesidad de respuesta (para soporte t�cnico)";
?>