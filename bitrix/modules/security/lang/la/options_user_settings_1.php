<?
$MESS["SEC_OTP_CONNECTED"] = "Conectado";
$MESS["SEC_OTP_CONNECT_DEVICE"] = "Conecte el dongle";
$MESS["SEC_OTP_CONNECT_DEVICE_TITLE"] = "Conecte el dongle";
$MESS["SEC_OTP_CONNECT_DONE"] = "Listo";
$MESS["SEC_OTP_CONNECT_MOBILE"] = "Conecte el dispositivo móvil";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_CODE"] = "Ingresar código";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_NEXT_CODE"] = "Ingresar el siguiente código";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_DESCRIPTION"] = "Una vez que el código ha sido escaneada o introducido manualmente con éxito, el teléfono móvil mostrará el código que tendrá que introducir a continuación.";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_NEXT_DESCRIPTION"] = "El algoritmo de OTP requiere dos códigos para la autenticación. Por favor, generar el siguiente código y entrar en él a continuación.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT"] = "Para introducir datos de forma manual, especifique la dirección de la página web, el correo electrónico o el inicio de sesión, un código secreto en la imagen y seleccione el tipo de clave.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_HOTP"] = "basado en el contador";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_TOTP"] = "basado en el tiempo";
$MESS["SEC_OTP_CONNECT_MOBILE_SCAN_QR"] = "Traiga su dispositivo móvil para el monitor y espere mientras la aplicación está escaneando el código.";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_1"] = "Descarga la aplicación móvil de Bitrix OTP para su teléfono en a href=\"https://itunes.apple.com/en/app/bitrix24-otp/id929604673?l=en\" target=\"_new\">AppStore</a> on <a href=\"https://play.google.com/store/apps/details?id=com.bitrixsoft.otp\" target=\"_new\">GooglePlay</a>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_2"] = "Ejecute la aplicación y haga clic en <b>Configurar</b>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_3"] = "Elige cómo quieres ingresar datos: utilizando el código QR o manualmente";
$MESS["SEC_OTP_CONNECT_MOBILE_TITLE"] = "Conecte el dispositivo móvil";
$MESS["SEC_OTP_CONNECT_NEW_DEVICE"] = "Conecte nuevo dongle";
$MESS["SEC_OTP_CONNECT_NEW_MOBILE"] = "Conecte nuevo dispositivo móvil";
$MESS["SEC_OTP_DEACTIVATE_UNTIL"] = "Desactivado hasta #DATE#";
$MESS["SEC_OTP_DESCRIPTION_ABOUT"] = "One-Time Password (OTP) fue desarrollado como parte de la iniciativa OATH.<br>
OTP se basa en HMAC y SHA-1/SHA-256/SHA-512. En el presente ambos algoritmos son soportados por el código generado:
<ul>
  <li>basado en contador (HMAC-Based One-time Password, HOTP) como se describe en <a href=\"https://tools.ietf.org/html/rfc4226\" target=\"_blank\">RFC4226</a></li>
<li>basado en tiempo (Time-based One-time Password, TOTP) como se describe en <a href=\"https://tools.ietf.org/html/rfc6238\" target=\"_blank\">RFC6238</a></li></ul>
<p>Para calcular el valor OTP el algoritmo toma dos parámetros de ingreso, una clave secreta (valor inicial) y el valor actual del contador (el número de generación de ciclos requeridos o el tiempo actual, dependiendo de el algoritmo).</p>
<p>El valor inicial se guarda en el dispositivo, así como en la página web una vez que un dispositivo se ha inicializado. Si utiliza HOTP, el contador del dispositivo se incrementa en cada generación OTP, mientras que el contador del servidor cambia en cada autenticación OTP exitosa. Si utiliza TOTP, sin contador se guarda en el dispositivo y el servidor realiza un seguimiento del posible cambio de hora del dispositivo en cada autenticación exitosa OTP.<br />
  Cada uno de los dispositivos de OTP en un lote incluye un archivo cifrado que contiene los valores iniciales (claves secretas) para cada dispositivo en el lote, el archivo se une a el número de serie del dispositivo que se puede encontrar en el dispositivo.<br />
  Si los contadores del dispositivo y del servidor si incrementan sin sincronización, ellos pueden fácilmente sincronizarse nuevamente dándole el valor del servidor al dispositivo. Para ello, el administrador (o un usuario con permisos apropiados) tiene que generar dos OTP consecutivos y entrar en el sitio web.<br />
Usted puede encontrar la aplicación móvil en la AppStore y GooglePlay.</p>";
$MESS["SEC_OTP_DESCRIPTION_ABOUT_TITLE"] = "Descripción";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION"] = "Un código de una sola vez para la autenticación de dos pasos se puede obtener mediante el uso de un dispositivo especial (dongle), o una aplicación móvil gratuita (Bitrix OTP) cada usuario necesita tener instalado en su dispositivo móvil.<br>
Para habilitar un dispositivo de seguridad, el administrador tendrá que abrir el perfil del usuario y escribir las dos contraseñas generadas por el.<br>";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION_TITLE"] = "Activación";
$MESS["SEC_OTP_DESCRIPTION_INTRO_INTRANET"] = "Hoy en día, un usuario utiliza un par de usuarios y contraseñas para autenticarse en Bitrix24. Sin embargo, existen herramientas que una persona malintencionada puede emplear para entrar en un ordenador y robar estos datos, por ejemplo, si un usuario guarda sus contraseñas.<br>
<b>Autenticación de dos pasos</b> es la opción recomendada para la protección contra software pirata. Cada vez que un usuario inicia sesión en el sistema, tendrá que pasar dos niveles de verificación. En primer lugar, introduzca el nombre de usuario y contraseña. A continuación, introduzca un código de seguridad de una sola vez enviado a su dispositivo móvil. La conclusión es que un atacante no puede hacer uso de los datos robados porque no conoce el código de seguridad.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_SITE"] = "Hoy en día, un usuario utiliza un par de usuarios y contraseñas para autenticarse en Bitrix24. Sin embargo, existen herramientas que una persona malintencionada puede emplear para entrar en un ordenador y robar estos datos, por ejemplo, si un usuario guarda sus contraseñas.<br>
<b>Autenticación de dos pasos</b> es la opción recomendada para la protección contra software pirata. Cada vez que un usuario inicia sesión en el sistema, tendrá que pasar dos niveles de verificación. En primer lugar, introduzca el nombre de usuario y contraseña. A continuación, introduzca un código de seguridad de una sola vez enviado a su dispositivo móvil. La conclusión es que un atacante no puede hacer uso de los datos robados porque no conoce el código de seguridad.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_TITLE"] = "Contraseña de una sola vez";
$MESS["SEC_OTP_DESCRIPTION_USING"] = "Cuando la autenticación de dos pasos se activa, el usuario tendrá que aprobar dos niveles de comprobación al iniciar la sesión. <br>
En primer lugar, introduzca su dirección de correo y contraseña, como de costumbre. <br>
A continuación, introduzca un código de seguridad de una sola vez envió a su dispositivo móvil o la obtenida utilizando un dispositivo de seguridad dedicado.";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_0"] = "Paso 1";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_1"] = "Paso 2";
$MESS["SEC_OTP_DESCRIPTION_USING_TITLE"] = "Usa contraseñas de un solo uso";
$MESS["SEC_OTP_DISABLE"] = "Desactivar";
$MESS["SEC_OTP_ENABLE"] = "Activar";
$MESS["SEC_OTP_ERROR_TITLE"] = "No se puede guardar debido a un error.";
$MESS["SEC_OTP_INIT"] = "Inicialización";
$MESS["SEC_OTP_MANDATORY_ALMOST_EXPIRED"] = "El tiempo durante el cual un usuario tiene que configurar la autenticación de dos pasos expirará el #DATE#.";
$MESS["SEC_OTP_MANDATORY_DEFFER"] = "Extender";
$MESS["SEC_OTP_MANDATORY_DISABLED"] = "Autenticación de dos pasos desactivado obligatoriamente.";
$MESS["SEC_OTP_MANDATORY_ENABLE"] = "Requerir la activación de la autenticación de dos pasos en un plazo";
$MESS["SEC_OTP_MANDATORY_ENABLE_DEFAULT"] = "Requerir la activación de la autenticación de dos pasos";
$MESS["SEC_OTP_MANDATORY_EXPIRED"] = "El tiempo durante el cual un usuario tenía que configurar la autenticación de dos pasos ya ha expirado.";
$MESS["SEC_OTP_MOBILE_INPUT_METHODS_SEPARATOR"] = "o";
$MESS["SEC_OTP_MOBILE_MANUAL_INPUT"] = "Introducir código manualmente";
$MESS["SEC_OTP_MOBILE_SCAN_QR"] = "Escanear código QR";
$MESS["SEC_OTP_NEW_ACCESS_DENIED"] = "Se le negó el acceso al control de autenticación de dos pasos.";
$MESS["SEC_OTP_NEW_SWITCH_ON"] = "Activar la autenticación de dos pasos";
$MESS["SEC_OTP_NO_DAYS"] = "para siempre";
$MESS["SEC_OTP_PASS1"] = "La primera contraseña del dispositivo (haga click y escriba abajo)";
$MESS["SEC_OTP_PASS2"] = "La segunda contraseña del dispositivo (click nuevamente y escriba abajo)";
$MESS["SEC_OTP_RECOVERY_CODES_BUTTON"] = "Códigos de copia de seguridad";
$MESS["SEC_OTP_RECOVERY_CODES_DESCRIPTION"] = "Copie los códigos de seguridad que pueda necesitar si usted perdió su dispositivo móvil o no puede obtener un código a través de la aplicación por cualquier otro motivo.";
$MESS["SEC_OTP_RECOVERY_CODES_NOTE"] = "Un código sólo puede utilizarse una vez. Sugerencia: Obtener códigos utilizados fuera de la lista.";
$MESS["SEC_OTP_RECOVERY_CODES_PRINT"] = "Imprimir";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE"] = "Generar nuevos códigos";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE_DESCRIPTION"] = "¿Copia de los códigos cortos de seguridad?<br/>
Crear otras nuevas. <br/><br/>
Creación de nuevos códigos de seguridad invalido <br/>códigos generados previamente.";
$MESS["SEC_OTP_RECOVERY_CODES_SAVE_FILE"] = "Guardar en archivo de texto";
$MESS["SEC_OTP_RECOVERY_CODES_TITLE"] = "Códigos de copia de seguridad";
$MESS["SEC_OTP_RECOVERY_CODES_WARNING"] = "Mantenga a la mano, por ejemplo en su billetera o cartera. Cada uno de los códigos sólo pueden usarse una vez.";
$MESS["SEC_OTP_SECRET_KEY"] = "Clave secreta (proporcionada por eldispositivo)";
$MESS["SEC_OTP_STATUS"] = "Estado actual";
$MESS["SEC_OTP_STATUS_ON"] = "Habilitado";
$MESS["SEC_OTP_SYNC_NOW"] = "Sincronizar";
$MESS["SEC_OTP_TYPE"] = "Algoritmo de generación de contraseña";
$MESS["SEC_OTP_UNKNOWN_ERROR"] = "Error inesperado. Inténtalo de nuevo más tarde.";
$MESS["SEC_OTP_WARNING_RECOVERY_CODES"] = "La autenticación de dos pasos está habilitada pero no crea códigos de seguridad. Usted puede necesitarlos si perdió su dispositivo móvil o no puede obtener un código a través de la aplicación por cualquier otro motivo.";
?>