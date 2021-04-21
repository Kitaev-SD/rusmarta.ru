<?
$MESS["WF_IBLOCK_STATUS_CHANGE_DESC"] = "#ID# - ID
#IBLOCK_ID# - ID de block de informaci�n
#IBLOCK_TYPE# - tipo de block de informaci�n
#SECTION_ID# - ID de la secci�n
#ADMIN_EMAIL# - EMails del flujo de trabajo de los administradores
#BCC# - Emails de los usuarios que ya han modificado el tiempo o alg�n elemento que pueda modificarlo
#PREV_STATUS_ID# - ID del estado previo del elemento
#PREV_STATUS_TITLE# - nombre del estado previo del elemento
#STATUS_ID# -  estado actual del ID
#STATUS_TITLE# - nombre del estado actual
#DATE_CREATE# - fecha de creaci�n del elemento
#CREATED_BY_ID# - ID del usuario que cre� el elemento
#CREATED_BY_NAME# - nombre del usuario que cre� el elemento
#CREATED_BY_EMAIL# - EMail del usuario que cre� el elemento
#DATE_MODIFY# - fecha de modificaci�n del elemento
#MODIFIED_BY_ID# - ID del usuario que modific� el elemento
#MODIFIED_BY_NAME# - nombre del usuario que modific� el documento
#NAME# - nombre del elemento
#PREVIEW_HTML# - descripci�n breve en formato de HTML
#PREVIEW_TEXT# - descripci�n breve en formato de TEXTO
#PREVIEW# - descripci�n breve almacenada en la base de datos
#PREVIEW_TYPE# - Formato para el texto previo (texto | html)
#DETAIL_HTML# - descripci�n completa en formato HTML
#DETAIL_TEXT# - descripci�n completa en formato TEXTO
#DETAIL# - descripci�n completa almacenada en la base de datos
#DETAIL_TYPE# - tipo de descripci�n completa (texto | html)
#COMMENTS# - comentarios";
$MESS["WF_IBLOCK_STATUS_CHANGE_MESSAGE"] = "#SITE_NAME#: estado del elemento ##ID# fue cambiado (block de informaci�n ##IBLOCK_ID#; tipo - #IBLOCK_TYPE#)
---------------------------------------------------------------------------

Ahora los campos del elemento tienen los siguientes valores:

Nombre         - #NAME#
Status       - [#STATUS_ID#] #STATUS_TITLE#; previous - [#PREV_STATUS_ID#] #PREV_STATUS_TITLE#
Creado      - #DATE_CREATE#; [#CREATED_BY_ID#] #CREATED_BY_NAME#
Modificado     - #DATE_MODIFY#; [#MODIFIED_BY_ID#] #MODIFIED_BY_NAME#

Breve descripci�n (type - #PREVIEW_TYPE#):
---------------------------------------------------------------------------
#PREVIEW_TEXT#
---------------------------------------------------------------------------

Descripci�n completa de (type - #DETAIL_TYPE#):
---------------------------------------------------------------------------
#DETAIL_TEXT#
---------------------------------------------------------------------------

Comentarios:
---------------------------------------------------------------------------
#COMMENTS#
---------------------------------------------------------------------------

Para ver y editar documento visitar el link:
http://#SERVER_NAME#/bitrix/admin/iblock_element_edit.php?lang=en&WF=Y&PID=#ID#&type=#IBLOCK_TYPE#&IBLOCK_ID=#IBLOCK_ID#&filter_section=#SECTION_ID#

Mensaje generado autom�ticamente.";
$MESS["WF_IBLOCK_STATUS_CHANGE_NAME"] = "Estado del elemento del block de informaci�n fue cambiado";
$MESS["WF_IBLOCK_STATUS_CHANGE_SUBJECT"] = "#SITE_NAME#: Status del elemento ##ID# fue cambiado (block de informaci�n ##IBLOCK_ID#; tipo - #IBLOCK_TYPE#)";
$MESS["WF_NEW_DOCUMENT_DESC"] = "#ID# - ID
#ADMIN_EMAIL# - EMails flujo de trabajo de los administradores
#BCC# - Emails de los usuarios que ya han modificado el documento de alg�n tiempo o que se puede modificar#STATUS_ID# -  ID del estado
#STATUS_TITLE# - nombre del estado
#DATE_ENTER# - fecha de creaci�n del documento
#ENTERED_BY_ID# - ID del usuario que cre� el documento
#ENTERED_BY_NAME# - nombre del usuario que cre� el documento
#ENTERED_BY_EMAIL# - E-Mail del usuario que ha creado el documento
#FILENAME# - nombre completo del archivo
#TITLE# - t�tulo del archivo
#BODY_HTML# - el contenido de sus documentos en formato de HTML
#BODY_TEXT# - el contenido de sus documentos en formato de TEXTO
#BODY# - el contenido de los documentos est� almacenado en la base de datos
#BODY_TYPE# - tipo de contenido de documento
#COMMENTS# - comentarios";
$MESS["WF_NEW_DOCUMENT_MESSAGE"] = "Un nuevo documento ha sido creado en  #SITE_NAME#.
---------------------------------------------------------------------------

Ahora los campos del documento tienen los siguientes valores:

ID            - #ID#
Archivo          - #FILENAME#
T�tulo        - #TITLE#
Status        - [#STATUS_ID#] #STATUS_TITLE#
Creado        - #DATE_ENTER#; [#ENTERED_BY_ID#] #ENTERED_BY_NAME#

Contenidos (tipo - #BODY_TYPE#):
---------------------------------------------------------------------------
#BODY_TEXT#
---------------------------------------------------------------------------

Comentarios:
---------------------------------------------------------------------------
#COMMENTS#
---------------------------------------------------------------------------

Para ver y editar el documento visite el link:
http://#SERVER_NAME#/bitrix/admin/workflow_edit.php?lang=en&ID=#ID#

Mensaje generado autom�ticamente.";
$MESS["WF_NEW_DOCUMENT_NAME"] = "Un nuevo documento fue creado";
$MESS["WF_NEW_DOCUMENT_SUBJECT"] = "#SITE_NAME#: Un nuevo documento fue creado";
$MESS["WF_NEW_IBLOCK_ELEMENT_DESC"] = "#ID# - ID
#IBLOCK_ID# - ID del block de informaci�n
#IBLOCK_TYPE# - tipo de block de informaci�n
#SECTION_ID# - ID de la secci�n
#ADMIN_EMAIL# - EMails del flujo de trabajo de los administradores
#BCC# - Emails de los usuarios que ya han modificado el tiempo o alg�n elemento que pueda modificarlo
#STATUS_ID# -  ID del estado actual
#STATUS_TITLE# - nombre del estado actual
#DATE_CREATE# - fecha de creaci�n del elemento
#CREATED_BY_ID# - ID del usuario que ha creado del elemento
#CREATED_BY_NAME# - nombre del usuario que ha creado el elemento
#CREATED_BY_EMAIL# - EMail del usuario que cre� el documento
#NAME# - nombre del elemento
#PREVIEW_HTML# - descripci�n breve formato de HTML
#PREVIEW_TEXT# - descripci�n breve formato de TEXT
#PREVIEW# - descripci�n breve almacenada en la base de datos
#PREVIEW_TYPE# - tipo de descripci�n breve (texto | html)
#DETAIL_HTML# - descripci�n completa en formato de HTML
#DETAIL_TEXT# - descripci�n completa en formato de TEXT
#DETAIL# - descripci�n completa almacenada en la base de datos
#DETAIL_TYPE# - tipo de descripci�n completa(texto | html)
#COMMENTS# - comentarios";
$MESS["WF_NEW_IBLOCK_ELEMENT_MESSAGE"] = "#SITE_NAME#: Un nuevo elemento ha sido creado(block de informaci�n # #IBLOCK_ID#; tipo - #IBLOCK_TYPE#)
---------------------------------------------------------------------------

Ahora los elementos tienen los siguientes valores:

Nombre         - #NAME#
Status       - [#STATUS_ID#] #STATUS_TITLE#
Creado      - #DATE_CREATE#; [#CREATED_BY_ID#] #CREATED_BY_NAME#

Breve descripci�n (tipo - #PREVIEW_TYPE#):
---------------------------------------------------------------------------
#PREVIEW_TEXT#
---------------------------------------------------------------------------

Descripci�n completa de (tipo - #DETAIL_TYPE#):
---------------------------------------------------------------------------
#DETAIL_TEXT#
---------------------------------------------------------------------------

Comentarios:
---------------------------------------------------------------------------
#COMMENTS#
---------------------------------------------------------------------------

Para ver y editar el docuemnto visitar el link:
http://#SERVER_NAME#/bitrix/admin/iblock_element_edit.php?lang=en&WF=Y&PID=#ID#&type=#IBLOCK_TYPE#&IBLOCK_ID=#IBLOCK_ID#&filter_section=#SECTION_ID#

Mensaje generado autom�ticamente.";
$MESS["WF_NEW_IBLOCK_ELEMENT_NAME"] = "Nuevo elemento de block de informaci�n fue creado";
$MESS["WF_NEW_IBLOCK_ELEMENT_SUBJECT"] = "#SITE_NAME#: Un nuevo elemento fue creado (block informativo # #IBLOCK_ID#; tipo - #IBLOCK_TYPE#)";
$MESS["WF_STATUS_CHANGE_DESC"] = "#ID# - ID
#ADMIN_EMAIL# - Correos electr�nicos de flujo de trabajo de los administradores
#BCC# - Correos electr�nicos de los usuarios que ya han modificado el documento de alg�n tiempo o que se puede modificar
#PREV_STATUS_ID# - ID del estado previo del documento
#PREV_STATUS_TITLE# - nombre del estado previo del documento
#STATUS_ID# - ID del estado
#STATUS_TITLE# - nombre del estado
#DATE_ENTER# - fecha de creaci�n del documento
#ENTERED_BY_ID# - ID del usuario que creo el documento
#ENTERED_BY_NAME# - nombre del usuario que creo el documento
#ENTERED_BY_EMAIL# - EMail del usuario que cre el documento
#DATE_MODIFY# - fecha de modicficaci�n del documento
#MODIFIED_BY_ID# - ID del usuario que cre� el documento
#MODIFIED_BY_NAME# - nombre del usuario que cre� el documento
#FILENAME# - full file name
#TITLE# - file title
#BODY_HTML# - document contents in HTML format
#BODY_TEXT# - document contents in TEXT format
#BODY# - document's content stored in database
#BODY_TYPE# - type of document contents
#COMMENTS# - comments";
$MESS["WF_STATUS_CHANGE_MESSAGE"] = "Estado del documento # #ID# fue cambiado a #SITE_NAME#.
---------------------------------------------------------------------------

Ahora los campos en el documento tienen los siguientes valores:

Archivo         - #FILENAME#
T�tulo         - #TITLE#
Status        - [#STATUS_ID#] #STATUS_TITLE#; previous - [#PREV_STATUS_ID#] #PREV_STATUS_TITLE#
Creado       - #DATE_ENTER#; [#ENTERED_BY_ID#] #ENTERED_BY_NAME#
Modificado      - #DATE_MODIFY#; [#MODIFIED_BY_ID#] #MODIFIED_BY_NAME#

Contenidos (type - #BODY_TYPE#):
---------------------------------------------------------------------------
#BODY_TEXT#
---------------------------------------------------------------------------

Comentarios:
---------------------------------------------------------------------------
#COMMENTS#
---------------------------------------------------------------------------

Para ver y editar el documento visitar el link:
http://#SERVER_NAME#/bitrix/admin/workflow_edit.php?lang=en&ID=#ID#

Mensaje generado autom�ticamente.";
$MESS["WF_STATUS_CHANGE_NAME"] = "El estado del documento fue cambiado";
$MESS["WF_STATUS_CHANGE_SUBJECT"] = "#SITE_NAME#: Estado del documento # #ID# fue cambiado";
?>