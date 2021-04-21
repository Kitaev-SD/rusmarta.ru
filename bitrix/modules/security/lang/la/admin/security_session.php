<?
$MESS["SEC_SESSION_ADMIN_DB_BUTTON_OFF"] = "No almacene la sesión de la base de datos en el módulo de seguridad de la base de datos";
$MESS["SEC_SESSION_ADMIN_DB_BUTTON_ON"] = "Almacenar la sesión de la base de datos en el módulo de seguridad de la base de datos";
$MESS["SEC_SESSION_ADMIN_DB_NOTE"] = "<p>La mayoría de los ataques web se producen al intentar robar datos de una sesión. Al habilitar la <b>Prototección de sesión</b> hacemos a esta sensible a los ataques.</p>
<p>Adicionalmente a la protección de sesiones estándard, usted puede configurar opciones en las preferencias de cada grupo de usuarios en la <b>protección proactiva de la sesión</b>:
<ul style='font-size:100%'>
<li>Cambiando el ID de la sesión después de cierto periodo de tiempo;</li>
<li>Almacenando los datos de la sesién en una base de datos del módulo.</li>
</ul>
<p>Almacenando los datos de la sesión en la base de datos del módulo evitamos que los datos sean robados por scripts ejecutados en el servidor virtual, scripts que se aprovechen de una mala configuración, mala asignación de permisos en las carpetas personales y otros problemas relacionados con el sistema operativo. Esto también reduce la carga del sistema de archivos y el proceso de descarga del servidor de la base de datos.</p>
<p><i>Recomendado para un nivel alto .</i></p>";
$MESS["SEC_SESSION_ADMIN_DB_OFF"] = "Sesión de la base de datos no están almacenados el módulo de la base de datos.";
$MESS["SEC_SESSION_ADMIN_DB_ON"] = "La base de datos de la sesión está almacenada en el módulo de seguridad de la base de datos.";
$MESS["SEC_SESSION_ADMIN_DB_WARNING"] = "¡Atención! Alternar el período de sesiones o desactivar este modo hará que los usuarios autorizados a pierdan su actual autorización (lso datos de la sesión serán destruidos).";
$MESS["SEC_SESSION_ADMIN_SAVEDB_TAB"] = "Sesiones en la Base de Datos";
$MESS["SEC_SESSION_ADMIN_SAVEDB_TAB_TITLE"] = "Configurar el almacenamiento de la sesión de la data en la base de datos";
$MESS["SEC_SESSION_ADMIN_SESSID_BUTTON_OFF"] = "Cambio de ID desactivado";
$MESS["SEC_SESSION_ADMIN_SESSID_BUTTON_ON"] = "Cambio de ID activado";
$MESS["SEC_SESSION_ADMIN_SESSID_NOTE"] = "<p>Si esta función es habilitada, el ID de la sesión será cambiada después del periodo de tiempo especificado. Esto le dará mayor trabajo al servidor, pero obviamente hará imposible el secuestro de los datos de la sesión.</p>
<p><i>Recomendado para un nivel alto .</i></p>";
$MESS["SEC_SESSION_ADMIN_SESSID_OFF"] = "El cambio del ID de la sesión está desactivado.";
$MESS["SEC_SESSION_ADMIN_SESSID_ON"] = "El cambio del ID de la sesión está habilitado.";
$MESS["SEC_SESSION_ADMIN_SESSID_TAB"] = "ID de cambio";
$MESS["SEC_SESSION_ADMIN_SESSID_TAB_TITLE"] = "Configurar cambio periódico de ID de sesión";
$MESS["SEC_SESSION_ADMIN_SESSID_TTL"] = "ID del tiempo de duración de la sesión, seg.";
$MESS["SEC_SESSION_ADMIN_SESSID_WARNING"] = "La ID de sesión no es compatible con el módulo de Protección Proactia. El identificador retornado con la funicón session_id() debe no tener más de 32 caracteres y debería contener sólo dígitos y caracteres latinos.";
$MESS["SEC_SESSION_ADMIN_TITLE"] = "Sesión de Protección";
?>