<?
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR"] = "Los archivos temporales se guardan en el directorio root del proyecto";
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR_ADDITIONAL"] = "Directorio actual: #DIR#";
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR_DETAIL"] = "No se recomienda guardar los archivos temporales creados por CTempFile a la carpeta root.";
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR_RECOMMENDATION"] = "Definir una constante \"BX_TEMPORARY_FILES_DIRECTORY\" in \"bitrix/php_interface/dbconn.php\" y especificar una ruta requerida.<br>
Siga estos pasos:<br>
1. Elija un nombre para el directorio temporal y creelo. Por ejemplo,\"/home/bitrix/tmp/www\":
<pre>
mkdir -p -m 700 /home/bitrix/tmp/www
</pre>
2. Definir la constante para que el sistema sepa que desea guardar los archivos temporales en la carpeta:
<pre>
define (\"BX_TEMPORARY_FILES_DIRECTORY\", \"/home/bitrix/tmp/www\");
</pre>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION"] = "El directorio de almacenamiento de sesión contiene sesiones de los diferentes proyectos.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_OWNER"] = "Motivo: propietario del archivo no es el usuario actual<br>
Archivo: #FILE#<br>
UID de propietario del archivo: #FILE_ONWER#<br>
UID del usuario actual: #CURRENT_OWNER#<br>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_SIGN"] = "Motivo: archivo de sesión no está firmado con el sitio web actual<br>
Archivo: #FILE#<br>
Firma del sitio web actual: #SIGN#<br>
Contenido del archivo: <pre>#FILE_CONTENT#</pre>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_DETAIL"] = "Algo puede pasar cuando compromete el proyecto completamente.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_RECOMMENDATION"] = "Utilizar un almacenamiento individual para cada proyecto.";
$MESS["SECURITY_SITE_CHECKER_EnvironmentTest_NAME"] = "Verificación del entorno";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER"] = "PHP se está ejecutando como un usuario con privilegios";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER_ADDITIONAL"] = "#UID#/#GID#";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER_DETAIL"] = "Ejecución de PHP como un usuario con privilegios (por ejemplo root) puede comprometer la seguridad de su proyecto";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER_RECOMMENDATION"] = "Configure su servidor de tal manera que PHP se ejecuta como un usuario sin privilegios";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR"] = "Sesión de archivos y directorio de almacenamiento son accesible por todos los usuarios del sistema";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_ADDITIONAL"] = "Directorio de almacenamiento de la sesión: #DIR#<br>
Permiso: #PERMS#";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_DETAIL"] = "Esta vulnerabilidad puede ser utilizada para leer o cambiar los datos de la sesión de las secuencias que se ejecutan en otros servidores virtuales.";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_RECOMMENDATION"] = "Configurar permisos de acceso correctamente, o cambiar el directorio. Otra opción es la de almacenar las sesiones en la base de datos: <a href=\"/bitrix/admin/security_session.php\">Session protection</a>.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP"] = "PHP scripts son ejecutados en el directorio de archivos cargados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DETAIL"] = "A veces los desarrolladores no prestan suficiente atención a los filtros de nombre de archivo adecuado. Un atacante podría aprovechar esta vulnerabilidad y tomar el control completo de su proyecto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE"] = "Scripts PHP con la doble extensión (por ejemplo php.lala) se ejecutan en el directorio de archivos cargados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_DETAIL"] = "A veces los desarrolladores no prestan suficiente atención a los filtros de nombre de archivo adecuado. Un atacante podría aprovechar esta vulnerabilidad y tomar el control completo de su proyecto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_RECOMMENDATION"] = "Configure su servidor web correctamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_RECOMMENDATION"] = "Configure su servidor web correctamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY"] = "Python scripts son ejecutados en el directorio de archivos cargados.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_DETAIL"] = "A veces los desarrolladores no prestan suficiente atención a los filtros de nombre de archivo adecuado. Un atacante podría aprovechar esta vulnerabilidad y tomar el control completo de su proyecto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_RECOMMENDATION"] = "Configure su servidor web correctamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS"] = "Apache no debe procesar los archivos .htaccess en el directorio de archivos cargados";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_DETAIL"] = "A veces los desarrolladores no prestan suficiente atención a los filtros de nombre de archivo adecuado. Un atacante podría aprovechar esta vulnerabilidad y tomar el control completo de su proyecto.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_RECOMMENDATION"] = "Configure su servidor web correctamente.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION"] = "Apache Content Negotiation está habilitada en el directorio de carga de archivos.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_DETAIL"] = "No se recomienda Apache Content Negotiation debido a que esto puede provocar ataques XSS.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_RECOMMENDATION"] = "Configure su servidor web correctamente.";
?>