<?
$MESS["BP_DBLA_APP"] = "Aprobado";
$MESS["BP_DBLA_APPROVE"] = "Por favor aprobar o rechazar el documento.";
$MESS["BP_DBLA_APPROVE2"] = "Por favor aprobar o rechazar el documento.";
$MESS["BP_DBLA_APPROVE2_TEXT"] = "
Usted tiene que aprobar o rechazar el documento \"{=Document:NAME}\".

Autor: {=Document:CREATED_BY_PRINTABLE}";
$MESS["BP_DBLA_APPROVE2_TITLE"] = "Aprobaci�n del documento: Etapa 2";
$MESS["BP_DBLA_APPROVE_TEXT"] = "Usted tiene que aprobar o rechazar el documento \"{=Document:NAME}\".

Autor: {=Document:CREATED_BY_PRINTABLE}";
$MESS["BP_DBLA_APPROVE_TITLR"] = "Aprobaci�n del documento: Etapa 1";
$MESS["BP_DBLA_APP_S"] = "Estado: Aprobado";
$MESS["BP_DBLA_DESC"] = "Recomendado cuando un documento requiere de una evaluaci�n preliminar de expertos antes de su aprobaci�n. Durante el proceso de primera etapa de un documento sea refrendada por un experto. Si un experto rechaza el documento este �ltimo se devuelve a un autor para su revisi�n. Si el documento es aprobado, se transmite para su aprobaci�n final por un selecto grupo de empleados de manera simple mayor�a. Si la votaci�n final no, se devuelve el documento para su revisi�n y el procedimiento de aprobaci�n se inicia desde el principio.";
$MESS["BP_DBLA_M"] = "Mensaje de Correo electr�nico";
$MESS["BP_DBLA_MAIL2_SUBJ"] = "por favor responder con respecto a \"{=Document:NAME}\"";
$MESS["BP_DBLA_MAIL2_TEXT"] = "Usted tiene que aprobar o rechazar el documento \"{=Document:NAME}\".

Proceda abriendo el enlace: http://#HTTP_HOST##TASK_URL#

Autor: {=Document:CREATED_BY_PRINTABLE}";
$MESS["BP_DBLA_MAIL3_SUBJ"] = "Votaci�n sobre {=Document:NAME}: el documento ha sido aceptado.";
$MESS["BP_DBLA_MAIL3_TEXT"] = "Votaci�n sobre \"{=Document:NAME}\" ha sido completada.

El documento fue aceptado por {=ApproveActivity2:ApprovedPercent}% de los votos.

Aprobado:  {=ApproveActivity2:ApprovedCount}
Rechazado: {=ApproveActivity2:NotApprovedCount}

{=ApproveActivity2:Comments}";
$MESS["BP_DBLA_MAIL4_SUBJ"] = "Votaci�n sobre {=Document:NAME}: el documento ha sido rechazado.";
$MESS["BP_DBLA_MAIL4_TEXT"] = "La primera etapa de aprobaci�n \"{=Document:NAME}\" ha concluido.

El documento fue rechazado.

{=ApproveActivity1:Comments}";
$MESS["BP_DBLA_MAIL_SUBJ"] = "El documento ha pasado Etapa 1";
$MESS["BP_DBLA_MAIL_TEXT"] = "El documento \"{=Document:NAME}\" ha pasado a la primera etapa de aprobaci�n

El documento ha sido aprobado.

{=ApproveActivity1:Comments}";
$MESS["BP_DBLA_NAME"] = "Aprobaci�n de dos etapas";
$MESS["BP_DBLA_NAPP"] = "Votaci�n sobre {=Document:NAME}: el documento ha sido rechazado";
$MESS["BP_DBLA_NAPP_DRAFT"] = "Envido para revisi�n";
$MESS["BP_DBLA_NAPP_DRAFT_S"] = "Estado: Enviado para revisi�n";
$MESS["BP_DBLA_NAPP_TEXT"] = "Votaci�n sobre \"{=Document:NAME}\" ha sido completada.

El documento fue rechazado.

Aprobado:  {=ApproveActivity2:ApprovedCount}
Rechazado: {=ApproveActivity2:NotApprovedCount}

{=ApproveActivity2:Comments}";
$MESS["BP_DBLA_PARAM1"] = "Etapa 1 Votaci�n de personas";
$MESS["BP_DBLA_PARAM2"] = "Etapa 2 Votaci�n de personas";
$MESS["BP_DBLA_PUB_TITLE"] = "Publicar documento";
$MESS["BP_DBLA_S"] = "Secuencia de acciones";
$MESS["BP_DBLA_T"] = "Proceso de negocios secuencial";
$MESS["BP_DBLA_TASK"] = "Aprobar documento: \"{=Document:NAME}\"";
$MESS["BP_DBLA_TASK_DESC"] = "Usted tiene que aprobar o rechazar el documento \"{=Document:NAME}\".

Proceda abriendo el enlace: http://#HTTP_HOST##TASK_URL#

Autor: {=Document:CREATED_BY_PRINTABLE}";
?>