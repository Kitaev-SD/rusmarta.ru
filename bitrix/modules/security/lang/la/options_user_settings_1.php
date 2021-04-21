<?
$MESS["SEC_OTP_CONNECTED"] = "Conectado";
$MESS["SEC_OTP_CONNECT_DEVICE"] = "Conecte el dongle";
$MESS["SEC_OTP_CONNECT_DEVICE_TITLE"] = "Conecte el dongle";
$MESS["SEC_OTP_CONNECT_DONE"] = "Listo";
$MESS["SEC_OTP_CONNECT_MOBILE"] = "Conecte el dispositivo m�vil";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_CODE"] = "Ingresar c�digo";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_NEXT_CODE"] = "Ingresar el siguiente c�digo";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_DESCRIPTION"] = "Una vez que el c�digo ha sido escaneada o introducido manualmente con �xito, el tel�fono m�vil mostrar� el c�digo que tendr� que introducir a continuaci�n.";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_NEXT_DESCRIPTION"] = "El algoritmo de OTP requiere dos c�digos para la autenticaci�n. Por favor, generar el siguiente c�digo y entrar en �l a continuaci�n.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT"] = "Para introducir datos de forma manual, especifique la direcci�n de la p�gina web, el correo electr�nico o el inicio de sesi�n, un c�digo secreto en la imagen y seleccione el tipo de clave.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_HOTP"] = "basado en el contador";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_TOTP"] = "basado en el tiempo";
$MESS["SEC_OTP_CONNECT_MOBILE_SCAN_QR"] = "Traiga su dispositivo m�vil para el monitor y espere mientras la aplicaci�n est� escaneando el c�digo.";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_1"] = "Descarga la aplicaci�n m�vil de Bitrix OTP para su tel�fono en a href=\"https://itunes.apple.com/en/app/bitrix24-otp/id929604673?l=en\" target=\"_new\">AppStore</a> on <a href=\"https://play.google.com/store/apps/details?id=com.bitrixsoft.otp\" target=\"_new\">GooglePlay</a>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_2"] = "Ejecute la aplicaci�n y haga clic en <b>Configurar</b>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_3"] = "Elige c�mo quieres ingresar datos: utilizando el c�digo QR o manualmente";
$MESS["SEC_OTP_CONNECT_MOBILE_TITLE"] = "Conecte el dispositivo m�vil";
$MESS["SEC_OTP_CONNECT_NEW_DEVICE"] = "Conecte nuevo dongle";
$MESS["SEC_OTP_CONNECT_NEW_MOBILE"] = "Conecte nuevo dispositivo m�vil";
$MESS["SEC_OTP_DEACTIVATE_UNTIL"] = "Desactivado hasta #DATE#";
$MESS["SEC_OTP_DESCRIPTION_ABOUT"] = "One-Time Password (OTP) fue desarrollado como parte de la iniciativa OATH.<br>
OTP se basa en HMAC y SHA-1/SHA-256/SHA-512. En el presente ambos algoritmos son soportados por el c�digo generado:
<ul>
  <li>basado en contador (HMAC-Based One-time Password, HOTP) como se describe en <a href=\"https://tools.ietf.org/html/rfc4226\" target=\"_blank\">RFC4226</a></li>
<li>basado en tiempo (Time-based One-time Password, TOTP) como se describe en <a href=\"https://tools.ietf.org/html/rfc6238\" target=\"_blank\">RFC6238</a></li></ul>
<p>Para calcular el valor OTP el algoritmo toma dos par�metros de ingreso, una clave secreta (valor inicial) y el valor actual del contador (el n�mero de generaci�n de ciclos requeridos o el tiempo actual, dependiendo de el algoritmo).</p>
<p>El valor inicial se guarda en el dispositivo, as� como en la p�gina web una vez que un dispositivo se ha inicializado. Si utiliza HOTP, el contador del dispositivo se incrementa en cada generaci�n OTP, mientras que el contador del servidor cambia en cada autenticaci�n OTP exitosa. Si utiliza TOTP, sin contador se guarda en el dispositivo y el servidor realiza un seguimiento del posible cambio de hora del dispositivo en cada autenticaci�n exitosa OTP.<br />
  Cada uno de los dispositivos de OTP en un lote incluye un archivo cifrado que contiene los valores iniciales (claves secretas) para cada dispositivo en el lote, el archivo se une a el n�mero de serie del dispositivo que se puede encontrar en el dispositivo.<br />
  Si los contadores del dispositivo y del servidor si incrementan sin sincronizaci�n, ellos pueden f�cilmente sincronizarse nuevamente d�ndole el valor del servidor al dispositivo. Para ello, el administrador (o un usuario con permisos apropiados) tiene que generar dos OTP consecutivos y entrar en el sitio web.<br />
Usted puede encontrar la aplicaci�n m�vil en la AppStore y GooglePlay.</p>";
$MESS["SEC_OTP_DESCRIPTION_ABOUT_TITLE"] = "Descripci�n";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION"] = "Un c�digo de una sola vez para la autenticaci�n de dos pasos se puede obtener mediante el uso de un dispositivo especial (dongle), o una aplicaci�n m�vil gratuita (Bitrix OTP) cada usuario necesita tener instalado en su dispositivo m�vil.<br>
Para habilitar un dispositivo de seguridad, el administrador tendr� que abrir el perfil del usuario y escribir las dos contrase�as generadas por el.<br>";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION_TITLE"] = "Activaci�n";
$MESS["SEC_OTP_DESCRIPTION_INTRO_INTRANET"] = "Hoy en d�a, un usuario utiliza un par de usuarios y contrase�as para autenticarse en Bitrix24. Sin embargo, existen herramientas que una persona malintencionada puede emplear para entrar en un ordenador y robar estos datos, por ejemplo, si un usuario guarda sus contrase�as.<br>
<b>Autenticaci�n de dos pasos</b> es la opci�n recomendada para la protecci�n contra software pirata. Cada vez que un usuario inicia sesi�n en el sistema, tendr� que pasar dos niveles de verificaci�n. En primer lugar, introduzca el nombre de usuario y contrase�a. A continuaci�n, introduzca un c�digo de seguridad de una sola vez enviado a su dispositivo m�vil. La conclusi�n es que un atacante no puede hacer uso de los datos robados porque no conoce el c�digo de seguridad.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_SITE"] = "Hoy en d�a, un usuario utiliza un par de usuarios y contrase�as para autenticarse en Bitrix24. Sin embargo, existen herramientas que una persona malintencionada puede emplear para entrar en un ordenador y robar estos datos, por ejemplo, si un usuario guarda sus contrase�as.<br>
<b>Autenticaci�n de dos pasos</b> es la opci�n recomendada para la protecci�n contra software pirata. Cada vez que un usuario inicia sesi�n en el sistema, tendr� que pasar dos niveles de verificaci�n. En primer lugar, introduzca el nombre de usuario y contrase�a. A continuaci�n, introduzca un c�digo de seguridad de una sola vez enviado a su dispositivo m�vil. La conclusi�n es que un atacante no puede hacer uso de los datos robados porque no conoce el c�digo de seguridad.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_TITLE"] = "Contrase�a de una sola vez";
$MESS["SEC_OTP_DESCRIPTION_USING"] = "Cuando la autenticaci�n de dos pasos se activa, el usuario tendr� que aprobar dos niveles de comprobaci�n al iniciar la sesi�n. <br>
En primer lugar, introduzca su direcci�n de correo y contrase�a, como de costumbre. <br>
A continuaci�n, introduzca un c�digo de seguridad de una sola vez envi� a su dispositivo m�vil o la obtenida utilizando un dispositivo de seguridad dedicado.";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_0"] = "Paso 1";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_1"] = "Paso 2";
$MESS["SEC_OTP_DESCRIPTION_USING_TITLE"] = "Usa contrase�as de un solo uso";
$MESS["SEC_OTP_DISABLE"] = "Desactivar";
$MESS["SEC_OTP_ENABLE"] = "Activar";
$MESS["SEC_OTP_ERROR_TITLE"] = "No se puede guardar debido a un error.";
$MESS["SEC_OTP_INIT"] = "Inicializaci�n";
$MESS["SEC_OTP_MANDATORY_ALMOST_EXPIRED"] = "El tiempo durante el cual un usuario tiene que configurar la autenticaci�n de dos pasos expirar� el #DATE#.";
$MESS["SEC_OTP_MANDATORY_DEFFER"] = "Extender";
$MESS["SEC_OTP_MANDATORY_DISABLED"] = "Autenticaci�n de dos pasos desactivado obligatoriamente.";
$MESS["SEC_OTP_MANDATORY_ENABLE"] = "Requerir la activaci�n de la autenticaci�n de dos pasos en un plazo";
$MESS["SEC_OTP_MANDATORY_ENABLE_DEFAULT"] = "Requerir la activaci�n de la autenticaci�n de dos pasos";
$MESS["SEC_OTP_MANDATORY_EXPIRED"] = "El tiempo durante el cual un usuario ten�a que configurar la autenticaci�n de dos pasos ya ha expirado.";
$MESS["SEC_OTP_MOBILE_INPUT_METHODS_SEPARATOR"] = "o";
$MESS["SEC_OTP_MOBILE_MANUAL_INPUT"] = "Introducir c�digo manualmente";
$MESS["SEC_OTP_MOBILE_SCAN_QR"] = "Escanear c�digo QR";
$MESS["SEC_OTP_NEW_ACCESS_DENIED"] = "Se le neg� el acceso al control de autenticaci�n de dos pasos.";
$MESS["SEC_OTP_NEW_SWITCH_ON"] = "Activar la autenticaci�n de dos pasos";
$MESS["SEC_OTP_NO_DAYS"] = "para siempre";
$MESS["SEC_OTP_PASS1"] = "La primera contrase�a del dispositivo (haga click y escriba abajo)";
$MESS["SEC_OTP_PASS2"] = "La segunda contrase�a del dispositivo (click nuevamente y escriba abajo)";
$MESS["SEC_OTP_RECOVERY_CODES_BUTTON"] = "C�digos de copia de seguridad";
$MESS["SEC_OTP_RECOVERY_CODES_DESCRIPTION"] = "Copie los c�digos de seguridad que pueda necesitar si usted perdi� su dispositivo m�vil o no puede obtener un c�digo a trav�s de la aplicaci�n por cualquier otro motivo.";
$MESS["SEC_OTP_RECOVERY_CODES_NOTE"] = "Un c�digo s�lo puede utilizarse una vez. Sugerencia: Obtener c�digos utilizados fuera de la lista.";
$MESS["SEC_OTP_RECOVERY_CODES_PRINT"] = "Imprimir";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE"] = "Generar nuevos c�digos";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE_DESCRIPTION"] = "�Copia de los c�digos cortos de seguridad?<br/>
Crear otras nuevas. <br/><br/>
Creaci�n de nuevos c�digos de seguridad invalido <br/>c�digos generados previamente.";
$MESS["SEC_OTP_RECOVERY_CODES_SAVE_FILE"] = "Guardar en archivo de texto";
$MESS["SEC_OTP_RECOVERY_CODES_TITLE"] = "C�digos de copia de seguridad";
$MESS["SEC_OTP_RECOVERY_CODES_WARNING"] = "Mantenga a la mano, por ejemplo en su billetera o cartera. Cada uno de los c�digos s�lo pueden usarse una vez.";
$MESS["SEC_OTP_SECRET_KEY"] = "Clave secreta (proporcionada por eldispositivo)";
$MESS["SEC_OTP_STATUS"] = "Estado actual";
$MESS["SEC_OTP_STATUS_ON"] = "Habilitado";
$MESS["SEC_OTP_SYNC_NOW"] = "Sincronizar";
$MESS["SEC_OTP_TYPE"] = "Algoritmo de generaci�n de contrase�a";
$MESS["SEC_OTP_UNKNOWN_ERROR"] = "Error inesperado. Int�ntalo de nuevo m�s tarde.";
$MESS["SEC_OTP_WARNING_RECOVERY_CODES"] = "La autenticaci�n de dos pasos est� habilitada pero no crea c�digos de seguridad. Usted puede necesitarlos si perdi� su dispositivo m�vil o no puede obtener un c�digo a trav�s de la aplicaci�n por cualquier otro motivo.";
?>