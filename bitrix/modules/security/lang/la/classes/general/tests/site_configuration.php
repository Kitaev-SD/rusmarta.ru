<?
$MESS["SECURITY_SITE_CHECKER_ADMIN_SECURITY_LEVEL"] = "Nivel de seguridad del grupo de usuarios del administrador no es elevado.";
$MESS["SECURITY_SITE_CHECKER_ADMIN_SECURITY_LEVEL_DETAIL"] = "El reducido nivel de seguridad puede ser utilizado por un atacante.";
$MESS["SECURITY_SITE_CHECKER_ADMIN_SECURITY_LEVEL_RECOMMENDATION"] = "Elevar";
$MESS["SECURITY_SITE_CHECKER_DANGER_EXTENSIONS"] = "La lista de archivos potencialmente peligrosos está incompleta";
$MESS["SECURITY_SITE_CHECKER_DANGER_EXTENSIONS_ADDITIONAL"] = "Actual: #ACTUAL#<br>
Recomendado (preferencias del servidor excluidas): #EXPECTED#<br>
Falta: #MISSING#";
$MESS["SECURITY_SITE_CHECKER_DANGER_EXTENSIONS_DETAIL"] = "La lista actual de extensiones de archivos potencialmente peligrosos no incluye todos los valores recomendados. Mantenga esta lista actualizada en todo momento.";
$MESS["SECURITY_SITE_CHECKER_DANGER_EXTENSIONS_RECOMMENDATION"] = "Editar la lista de extensiones de archivo en la página de configuración del sitio web: <a href=\"/bitrix/admin/settings.php?mid=fileman\" target=\"_blank\">Site Explorer</a>.";
$MESS["SECURITY_SITE_CHECKER_DB_DEBUG"] = "Consulta depuración SQL está activada (\$DBDebug is true)";
$MESS["SECURITY_SITE_CHECKER_DB_DEBUG_DETAIL"] = "Información de depuración SQL puede revelar información confidencial.";
$MESS["SECURITY_SITE_CHECKER_DB_DEBUG_RECOMMENDATION"] = "Inhabilitar";
$MESS["SECURITY_SITE_CHECKER_DB_EMPTY_PASS"] = "La contraseña de la base de datos está vacía";
$MESS["SECURITY_SITE_CHECKER_DB_EMPTY_PASS_DETAIL"] = "Una contraseña de base de datos vacía es una de las las maneras de perder el control de su proyecto.";
$MESS["SECURITY_SITE_CHECKER_DB_EMPTY_PASS_RECOMMENDATION"] = "Establecer contraseña";
$MESS["SECURITY_SITE_CHECKER_DB_MIN_LEN_PASS"] = "La contraseña de la base es menor de 8 caracteres.";
$MESS["SECURITY_SITE_CHECKER_DB_MIN_LEN_PASS_DETAIL"] = "La contraseña es demasiado débil.";
$MESS["SECURITY_SITE_CHECKER_DB_MIN_LEN_PASS_RECOMMENDATION"] = "Una contraseña más larga.";
$MESS["SECURITY_SITE_CHECKER_DB_NO_DIT_PASS"] = "La contraseña de la base no incluye números";
$MESS["SECURITY_SITE_CHECKER_DB_NO_DIT_PASS_DETAIL"] = "La contraseña es demasiado débil.";
$MESS["SECURITY_SITE_CHECKER_DB_NO_DIT_PASS_RECOMMENDATION"] = "Agregar números a la contraseña.";
$MESS["SECURITY_SITE_CHECKER_DB_NO_SIGN_PASS"] = "La contraseña de la base de datos no incluye los signos de puntuación.";
$MESS["SECURITY_SITE_CHECKER_DB_NO_SIGN_PASS_DETAIL"] = "La contraseña es demasiado débil.";
$MESS["SECURITY_SITE_CHECKER_DB_NO_SIGN_PASS_RECOMMENDATION"] = "Agregar signos de puntuación para la contraseña.";
$MESS["SECURITY_SITE_CHECKER_DB_SAME_REGISTER_PASS"] = "La contraseña de la base sólo incluye caracteres en minúscula o mayúscula.";
$MESS["SECURITY_SITE_CHECKER_DB_SAME_REGISTER_PASS_DETAIL"] = "La contraseña es demasiado débil.";
$MESS["SECURITY_SITE_CHECKER_DB_SAME_REGISTER_PASS_RECOMMENDATION"] = "Utilice caracteres inferior y superior en la contraseña.";
$MESS["SECURITY_SITE_CHECKER_ERROR_REPORTING"] = "Nivel de advertencia se debe ajustar a los \"errores\" o \"ninguno\"";
$MESS["SECURITY_SITE_CHECKER_ERROR_REPORTING_DETAIL"] = "PHP warnings puede mostrar toda la ruta de acceso físico a su proyecto web.";
$MESS["SECURITY_SITE_CHECKER_ERROR_REPORTING_RECOMMENDATION"] = "Cambiar nivel de advertencia a \"ninguno\".";
$MESS["SECURITY_SITE_CHECKER_EXCEPTION_DEBUG"] = "El reporte de errores extendido está habilitado";
$MESS["SECURITY_SITE_CHECKER_EXCEPTION_DEBUG_DETAIL"] = "El reporte de errores extendido puede revelar información privada sobre su proyecto.";
$MESS["SECURITY_SITE_CHECKER_EXCEPTION_DEBUG_RECOMMENDATION"] = "Desactivar modo de presentación extendida en .settings.php.";
$MESS["SECURITY_SITE_CHECKER_MODULES_VERSION"] = "Módulos obsoletos están todavía en uso";
$MESS["SECURITY_SITE_CHECKER_MODULES_VERSION_ARRITIONAL"] = "Las actualizaciones están disponibles para:<br>#MODULES#";
$MESS["SECURITY_SITE_CHECKER_MODULES_VERSION_DETAIL"] = "Hay nuevas versiones disponibles";
$MESS["SECURITY_SITE_CHECKER_MODULES_VERSION_ERROR"] = "No se puede comprobar si hay actualizaciones de la plataforma";
$MESS["SECURITY_SITE_CHECKER_MODULES_VERSION_ERROR_DETAIL"] = "Una actualización de SiteUpdate puede estar disponible, o el periodo de actualización ha expirado.";
$MESS["SECURITY_SITE_CHECKER_MODULES_VERSION_ERROR_RECOMMENDATION"] = "Vea los detalles en el <a href=\"/bitrix/admin/update_system.php\" target=\"_blank\">platform update page</a>.";
$MESS["SECURITY_SITE_CHECKER_MODULES_VERSION_RECOMMENDATION"] = "Se recomienda que actualice los módulos una vez que la nueva versión este disponible: <a href=\"/bitrix/admin/update_system.php\" target=\"_blank\">Platform Update</a>";
$MESS["SECURITY_SITE_CHECKER_REDIRECT_OFF"] = "Redirección de protección desactivada";
$MESS["SECURITY_SITE_CHECKER_REDIRECT_OFF_DETAIL"] = "Una redirección a una página web arbitraria de otro fabricante puede provocar ataques de diversa índole. Activar la protección de redirección para hacer su sitio web seguro (cuando se utiliza la API estándar).";
$MESS["SECURITY_SITE_CHECKER_REDIRECT_OFF_RECOMMENDATION"] = "Habilitar redirección protección <a href=\"/bitrix/admin/security_redirect.php\" target=\"_blank\">here</a>.";
$MESS["SECURITY_SITE_CHECKER_SiteConfigurationTest_NAME"] = "Prueba de configuración Web";
$MESS["SECURITY_SITE_CHECKER_WAF_OFF"] = "Está deshabilitado el filtro proactivo.";
$MESS["SECURITY_SITE_CHECKER_WAF_OFF_DETAIL"] = "Filtro proactivo deshabilitado definitivamente, no ayudará su sitio web.";
$MESS["SECURITY_SITE_CHECKER_WAF_OFF_RECOMMENDATION"] = "Activar filtro Proactive";
?>