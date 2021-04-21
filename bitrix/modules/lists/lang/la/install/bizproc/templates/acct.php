<?
$MESS["LIBTA_AAQD1"] = "Usted necesita aprobar o rechazar la factura

Nombre: {=Document:NAME}
Creado el: {=Document:DATE_CREATE}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Tipo: {=Document:PROPERTY_TYPE}
Número y fecha de factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}
Archivo: {=Variable:Domain}{=Document:PROPERTY_FILE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_AAQD2"] = "Usted necesita aprobar o rechazar el pago de la factura

Aprobado por: {=Variable:Approver_printable}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Número y fecha de la factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}
Archivo: {=Variable:Domain}{=Document:PROPERTY_FILE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_AAQN1"] = "Factura aprobada \"{=Document:NAME}\"";
$MESS["LIBTA_AAQN2"] = "Pago de factura aprobado  \"{=Document:NAME}\"";
$MESS["LIBTA_APPROVED"] = "Aprobado";
$MESS["LIBTA_APPROVED_N"] = "Nio aprobado";
$MESS["LIBTA_APPROVED_R"] = "Rechazado";
$MESS["LIBTA_APPROVED_Y"] = "Aprovado";
$MESS["LIBTA_BDT"] = "Partida presupuestaria";
$MESS["LIBTA_BP_TITLE"] = "Facturas";
$MESS["LIBTA_CREATED_BY"] = "Creado por";
$MESS["LIBTA_DATE_CREATE"] = "Creado el";
$MESS["LIBTA_DATE_PAY"] = "Fecha de pago (proveido por contador)";
$MESS["LIBTA_DOCS"] = "Copias del documento";
$MESS["LIBTA_DOCS_NO"] = "No";
$MESS["LIBTA_DOCS_YES"] = "Si";
$MESS["LIBTA_FILE"] = "Archivo (copia de factura)";
$MESS["LIBTA_NAME"] = "Nombre";
$MESS["LIBTA_NUM_DATE"] = "Fecha y numero de la factura";
$MESS["LIBTA_NUM_PP"] = "Número de la Orden de pago (proveido por contador)";
$MESS["LIBTA_PAID"] = "Pagado";
$MESS["LIBTA_PAID_NO"] = "No";
$MESS["LIBTA_PAID_YES"] = "Si";
$MESS["LIBTA_RIA10_DESCR"] = "Pago de factura

Pago confirmado: {=Variable:PaymentApprover_printable}
Factura aprobada: {=Variable:Approver_printable}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Fecah y monto de la factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}
Archivo: {=Variable:Domain}{=Document:PROPERTY_FILE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_RIA10_NAME"] = "Pago de factura \"{=Document:NAME}\"";
$MESS["LIBTA_RIA10_R1"] = "Fecha de pago";
$MESS["LIBTA_RIA10_R2"] = "Otro número para el pago";
$MESS["LIBTA_RRA15_DESCR"] = "Recolección de información sobre

Pago confirmado: {=Variable:PaymentApprover_printable}
Factura aprobada: {=Variable:Approver_printable}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Fecha y número de factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}
Archivo: {=Variable:Domain}{=Document:PROPERTY_FILE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_RRA15_NAME"] = "Recopilar la documentación sobre \"{=Document:NAME}\"";
$MESS["LIBTA_RRA15_SM"] = "Colectar documentos";
$MESS["LIBTA_RRA15_TASKBUTTON"] = "Recolección de documentos";
$MESS["LIBTA_RRA17_BUTTON"] = "Documenntación recibida";
$MESS["LIBTA_RRA17_DESCR"] = "Por la presente confirmo la recepción de la factura.

Fecha de pago: {=Document:PROPERTY_DATE_PAY}
Orden de pago Numero: {=Document:PROPERTY_NUM_PAY}
Pago confirmado: {=Variable:PaymentApprover_printable}
Factura aprobada: {=Variable:Approver_printable}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Fecha y número de factura: {=Document:PROPERTY_NUM_DATE}
MOnto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}
Archivo: {=Variable:Domain}{=Document:PROPERTY_FILE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_RRA17_NAME"] = "Confirmación de recpeción de documento el \"{=Document:NAME}\"";
$MESS["LIBTA_SMA_MESSAGE_1"] = "Por favor, apruebe la factura
Creada por: {=Document:CREATED_BY_PRINTABLE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Monto: {=Document:PROPERTY_SUM}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_SMA_MESSAGE_10"] = "Factura no probada
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_SMA_MESSAGE_2"] = "Apruebo la factura

Creada el: {=Document:DATE_CREATE}
Titulo: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_SMA_MESSAGE_3"] = "Apruebe el pago de la factura por favor

Aprobado por: {=Variable:Approver_printable}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Titulo: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Número y fecha de la factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}

{=Variable:Link}{=Document:ID}/

Tareas:
{=Variable:TasksLink}";
$MESS["LIBTA_SMA_MESSAGE_4"] = "Pago de la factura confirmado

Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_SMA_MESSAGE_5"] = "Pague su factura por favor

Pago confirmado: {=Variable:PaymentApprover_printable}
Factura aprobada: {=Variable:Approver_printable}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Número y fecha de factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}

{=Variable:Link}{=Document:ID}/
Tareas:
{=Variable:TasksLink}";
$MESS["LIBTA_SMA_MESSAGE_6"] = "Factura pagada; documentación requerida.
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}";
$MESS["LIBTA_SMA_MESSAGE_7"] = "Toda la documentación recogida de facturación
Fecha de pago: {=Document:PROPERTY_DATE_PAY}
Otro número de pago: {=Document:PROPERTY_NUM_PAY}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Fecah y número de factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}

{=Variable:Link}{=Document:ID}/

Tareas:
{=Variable:TasksLink}";
$MESS["LIBTA_SMA_MESSAGE_8"] = "Documentación recivida.

Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_SMA_MESSAGE_9"] = "Pago sin confirmación

Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}

{=Variable:Link}{=Document:ID}/";
$MESS["LIBTA_STATE1"] = "de ser aprobado";
$MESS["LIBTA_STATE2"] = "Aprobado ({=Variable:Approver_printable})";
$MESS["LIBTA_STATE3"] = "No aprobado ({=Variable:Approver_printable})";
$MESS["LIBTA_STATE4"] = "El pago se ha confirmado";
$MESS["LIBTA_STATE5"] = "Pago confirmado";
$MESS["LIBTA_STATE6"] = "Pendiente de pago";
$MESS["LIBTA_STATE7"] = "Pagado";
$MESS["LIBTA_STATE8"] = "Cerrado";
$MESS["LIBTA_STATE9"] = "Pago no aceptado";
$MESS["LIBTA_SUM"] = "Monto";
$MESS["LIBTA_TYPE"] = "Tipo";
$MESS["LIBTA_TYPE_ADV"] = "Publicidad";
$MESS["LIBTA_TYPE_C"] = "Gastos reembolsables";
$MESS["LIBTA_TYPE_D"] = "Otros";
$MESS["LIBTA_TYPE_EX"] = "Gastos de representación";
$MESS["LIBTA_T_AAQN1"] = "Pago de factura aprobado";
$MESS["LIBTA_T_AAQN2"] = "Confirmación de pago de factura";
$MESS["LIBTA_T_ASFA1"] = "Fijar el campo \"Aprobado\" al documento";
$MESS["LIBTA_T_ASFA2"] = "Fijar el campo \"Aprobado\" para el documento";
$MESS["LIBTA_T_ASFA3"] = "Fijar el campo \"aprobado\" para el documento";
$MESS["LIBTA_T_ASFA4"] = "Editar documento";
$MESS["LIBTA_T_ASFA5"] = "Editar documento";
$MESS["LIBTA_T_GUAX1"] = "Seleccionar supervisor";
$MESS["LIBTA_T_IFELSEA1"] = "Supervisores alcanzados";
$MESS["LIBTA_T_IFELSEA2"] = "Factura aprobada";
$MESS["LIBTA_T_IFELSEBA1"] = "Si";
$MESS["LIBTA_T_IFELSEBA2"] = "No";
$MESS["LIBTA_T_IFELSEBA3"] = "Si";
$MESS["LIBTA_T_IFELSEBA4"] = "No";
$MESS["LIBTA_T_PBP"] = "Business Process secuencial";
$MESS["LIBTA_T_PDA1"] = "Publicar documento";
$MESS["LIBTA_T_RIA10"] = "Factura de pagos";
$MESS["LIBTA_T_RRA15"] = "Documentación sobre";
$MESS["LIBTA_T_RRA17_NAME"] = "Documenntación recibida";
$MESS["LIBTA_T_SA0"] = "Secuencia de acciones";
$MESS["LIBTA_T_SMA_MESSAGE_1"] = "Mensaje: solicitud de aprobación de factura";
$MESS["LIBTA_T_SMA_MESSAGE_10"] = "Mensaje: factura no aprobada";
$MESS["LIBTA_T_SMA_MESSAGE_2"] = "Mensaje: pago aprobado";
$MESS["LIBTA_T_SMA_MESSAGE_3"] = "Mensaje: confirmación del pago";
$MESS["LIBTA_T_SMA_MESSAGE_4"] = "Mensaje: pago confirmado";
$MESS["LIBTA_T_SMA_MESSAGE_5"] = "Mensaje: Factura";
$MESS["LIBTA_T_SMA_MESSAGE_6"] = "Mensaje: factura pagada";
$MESS["LIBTA_T_SMA_MESSAGE_7"] = "Mensaje: recolección de información de documentación";
$MESS["LIBTA_T_SMA_MESSAGE_8"] = "Mensaje: documentación recibida";
$MESS["LIBTA_T_SMA_MESSAGE_9"] = "Mensaje: pago no confirmado";
$MESS["LIBTA_T_SPA1"] = "Fijar permisos para: autor";
$MESS["LIBTA_T_SPAX1"] = "Fijar permisos para: supervisor";
$MESS["LIBTA_T_SPAX2"] = "Fijar permisos para: aprobación del administrador";
$MESS["LIBTA_T_SPAX3"] = "Establecer permisos para: pagador";
$MESS["LIBTA_T_SPAX4"] = "Permisos fijados por: ";
$MESS["LIBTA_T_SPAX5"] = "Fijar permisos: final";
$MESS["LIBTA_T_SSTA1"] = "Status: de ser aprobado";
$MESS["LIBTA_T_SSTA2"] = "Status: aprobado";
$MESS["LIBTA_T_SSTA3"] = "Status: not aprobado";
$MESS["LIBTA_T_SSTA4"] = "Status: El pago se ha confirmado";
$MESS["LIBTA_T_SSTA5"] = "Status: pago confirmado";
$MESS["LIBTA_T_SSTA6"] = "Status: pago pendiente";
$MESS["LIBTA_T_SSTA7"] = "Status: factura pagada";
$MESS["LIBTA_T_SSTA8"] = "Status:: factura cerrada";
$MESS["LIBTA_T_SSTA9"] = "Status: pago rechazado";
$MESS["LIBTA_T_SVWA1"] = "Fijar supervisor";
$MESS["LIBTA_T_SVWA2"] = "Fijar supervisor";
$MESS["LIBTA_T_SVWA3"] = "Fijar variable";
$MESS["LIBTA_T_WHILEA1"] = "Ciclo de aprobación";
$MESS["LIBTA_T_XMA_MESSAGES_1"] = "Mensaje: Factura aprobada";
$MESS["LIBTA_T_XMA_MESSAGES_2"] = "Mensaje: confirmación del pago";
$MESS["LIBTA_T_XMA_MESSAGES_3"] = "Mensaje: Factura";
$MESS["LIBTA_V_APPR"] = "Pago aprobado por";
$MESS["LIBTA_V_APPRU"] = "Supervisor";
$MESS["LIBTA_V_BK"] = "Departamento de Cuentas (aprobación)";
$MESS["LIBTA_V_BKD"] = "Departamento de Cuentas (documentación)";
$MESS["LIBTA_V_BKP"] = "Departamento de Cuentas (pagos)";
$MESS["LIBTA_V_DOMAIN"] = "Dominio";
$MESS["LIBTA_V_LINK"] = "Link";
$MESS["LIBTA_V_MAPPR"] = "Administración (aprobación)";
$MESS["LIBTA_V_MNG"] = "Administración";
$MESS["LIBTA_V_PDATE"] = "Fecha de apgo";
$MESS["LIBTA_V_PNUM"] = "Otro número de pago";
$MESS["LIBTA_V_TLINK"] = "Link a tareas";
$MESS["LIBTA_XMA_MESSAGES_1"] = "BIP: Factura aprobada";
$MESS["LIBTA_XMA_MESSAGES_2"] = "BIP: confirmación del pago";
$MESS["LIBTA_XMA_MESSAGES_3"] = "BIP: Factura";
$MESS["LIBTA_XMA_MESSAGET_1"] = "Por favor, apruebe la factura

Creada por: {=Document:CREATED_BY_PRINTABLE}
Creada eln: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Número y fecha de factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}

{=Variable:Link}{=Document:ID}/

Tarea del bussines process:
{=Variable:TasksLink}";
$MESS["LIBTA_XMA_MESSAGET_2"] = "Por favor confirme el pago de la factura
Aprobado por: {=Variable:Approver_printable}
Creado por: {=Document:CREATED_BY_PRINTABLE}
Creado el: {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Número y fecha de la factura: {=Document:PROPERTY_NUM_DATE}
MOnto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}

{=Variable:Link}{=Document:ID}/

Tareas:
{=Variable:TasksLink}";
$MESS["LIBTA_XMA_MESSAGET_3"] = "Pague su factura por favor

Pago confirmado: {=Variable:PaymentApprover_printable}
Factura aprobada: {=Variable:Approver_printable}
Creado por:  {=Document:CREATED_BY_PRINTABLE}
Creado el:  {=Document:DATE_CREATE}
Título: {=Document:NAME}
Tipo: {=Document:PROPERTY_TYPE}
Número y fecha de factura: {=Document:PROPERTY_NUM_DATE}
Monto: {=Document:PROPERTY_SUM}
Partida presupuestaria: {=Document:PROPERTY_BDT}

{=Variable:Link}{=Document:ID}/

Tareas:
{=Variable:TasksLink}";
?>