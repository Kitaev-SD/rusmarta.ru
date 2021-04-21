<?
$MESS["SEC_SESSION_ADMIN_DB_BUTTON_OFF"] = "No almacene la sesi�n de la base de datos en el m�dulo de seguridad de la base de datos";
$MESS["SEC_SESSION_ADMIN_DB_BUTTON_ON"] = "Almacenar la sesi�n de la base de datos en el m�dulo de seguridad de la base de datos";
$MESS["SEC_SESSION_ADMIN_DB_NOTE"] = "<p>La mayor�a de los ataques web se producen al intentar robar datos de una sesi�n. Al habilitar la <b>Prototecci�n de sesi�n</b> hacemos a esta sensible a los ataques.</p>
<p>Adicionalmente a la protecci�n de sesiones est�ndard, usted puede configurar opciones en las preferencias de cada grupo de usuarios en la <b>protecci�n proactiva de la sesi�n</b>:
<ul style='font-size:100%'>
<li>Cambiando el ID de la sesi�n despu�s de cierto periodo de tiempo;</li>
<li>Almacenando los datos de la sesi�n en una base de datos del m�dulo.</li>
</ul>
<p>Almacenando los datos de la sesi�n en la base de datos del m�dulo evitamos que los datos sean robados por scripts ejecutados en el servidor virtual, scripts que se aprovechen de una mala configuraci�n, mala asignaci�n de permisos en las carpetas personales y otros problemas relacionados con el sistema operativo. Esto tambi�n reduce la carga del sistema de archivos y el proceso de descarga del servidor de la base de datos.</p>
<p><i>Recomendado para un nivel alto .</i></p>";
$MESS["SEC_SESSION_ADMIN_DB_OFF"] = "Sesi�n de la base de datos no est�n almacenados el m�dulo de la base de datos.";
$MESS["SEC_SESSION_ADMIN_DB_ON"] = "La base de datos de la sesi�n est� almacenada en el m�dulo de seguridad de la base de datos.";
$MESS["SEC_SESSION_ADMIN_DB_WARNING"] = "�Atenci�n! Alternar el per�odo de sesiones o desactivar este modo har� que los usuarios autorizados a pierdan su actual autorizaci�n (lso datos de la sesi�n ser�n destruidos).";
$MESS["SEC_SESSION_ADMIN_SAVEDB_TAB"] = "Sesiones en la Base de Datos";
$MESS["SEC_SESSION_ADMIN_SAVEDB_TAB_TITLE"] = "Configurar el almacenamiento de la sesi�n de la data en la base de datos";
$MESS["SEC_SESSION_ADMIN_SESSID_BUTTON_OFF"] = "Cambio de ID desactivado";
$MESS["SEC_SESSION_ADMIN_SESSID_BUTTON_ON"] = "Cambio de ID activado";
$MESS["SEC_SESSION_ADMIN_SESSID_NOTE"] = "<p>Si esta funci�n es habilitada, el ID de la sesi�n ser� cambiada despu�s del periodo de tiempo especificado. Esto le dar� mayor trabajo al servidor, pero obviamente har� imposible el secuestro de los datos de la sesi�n.</p>
<p><i>Recomendado para un nivel alto .</i></p>";
$MESS["SEC_SESSION_ADMIN_SESSID_OFF"] = "El cambio del ID de la sesi�n est� desactivado.";
$MESS["SEC_SESSION_ADMIN_SESSID_ON"] = "El cambio del ID de la sesi�n est� habilitado.";
$MESS["SEC_SESSION_ADMIN_SESSID_TAB"] = "ID de cambio";
$MESS["SEC_SESSION_ADMIN_SESSID_TAB_TITLE"] = "Configurar cambio peri�dico de ID de sesi�n";
$MESS["SEC_SESSION_ADMIN_SESSID_TTL"] = "ID del tiempo de duraci�n de la sesi�n, seg.";
$MESS["SEC_SESSION_ADMIN_SESSID_WARNING"] = "La ID de sesi�n no es compatible con el m�dulo de Protecci�n Proactia. El identificador retornado con la funic�n session_id() debe no tener m�s de 32 caracteres y deber�a contener s�lo d�gitos y caracteres latinos.";
$MESS["SEC_SESSION_ADMIN_TITLE"] = "Sesi�n de Protecci�n";
?>