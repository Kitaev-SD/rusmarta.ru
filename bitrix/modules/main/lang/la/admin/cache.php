<?
$MESS["MAIN_OPTION_CACHE_BUTTON_OFF"] = "Deshabilitar cach�";
$MESS["MAIN_OPTION_CACHE_BUTTON_ON"] = "Habilitar cach�";
$MESS["MAIN_OPTION_CACHE_ERROR"] = "El tipo de cach� de componetes ahora tiene este valor";
$MESS["MAIN_OPTION_CACHE_OFF"] = "El cach� de componentes est� deshabilitado por defecto";
$MESS["MAIN_OPTION_CACHE_OK"] = "Archivos de Cach� limpiados";
$MESS["MAIN_OPTION_CACHE_ON"] = "El cach� de componentes est� habilitado por defecto";
$MESS["MAIN_OPTION_CACHE_SUCCESS"] = "El tipo de cach� de componentes ha cambiado satisfactoriamente";
$MESS["MAIN_OPTION_CLEAR_CACHE"] = "Limpiar Archivos de Cach�";
$MESS["MAIN_OPTION_CLEAR_CACHE_ALL"] = "Todo";
$MESS["MAIN_OPTION_CLEAR_CACHE_CLEAR"] = "Limpiar";
$MESS["MAIN_OPTION_CLEAR_CACHE_MANAGED"] = "Todo el cach� administrativo";
$MESS["MAIN_OPTION_CLEAR_CACHE_MENU"] = "Men�";
$MESS["MAIN_OPTION_CLEAR_CACHE_OLD"] = "S�lo lo vencido";
$MESS["MAIN_OPTION_CLEAR_CACHE_STATIC"] = "Todas las p�ginas en cach� HTML";
$MESS["MAIN_OPTION_PUBL"] = "Configuraciones de cach� de componentes";
$MESS["MAIN_TAB_3"] = "Eliminar Archivos de Cache";
$MESS["MAIN_TAB_4"] = "Cach� de componentes";
$MESS["MCACHE_TITLE"] = "Configuraciones de cach�";
$MESS["cache_admin_note1"] = "<p>Usando el modo Autocache la velocidad de su sitio web se incrementar� increiblemente!</p>
<p>En el modo Autocache, la informaci�n generada por componentes es refrescada de acuerdo a las configuraciones del componente.</p>
<p>Para refrescar los objetos cacheados sobre su p�gina usted puede:</p>
<p>1. Abrir la p�gina requerida y refrescar el objeto haciendo click en un bot�n especial sobre la barra administrativa.</p>
<img src=\"/bitrix/images/main/page_cache_en.png\" vspace=\"5\" />
<p>2. En el modo Edici�n del Sitio, usted puede hacer click en el bot�n limpiar cach� de un componente espec�fico. </p>
<img src=\"/bitrix/images/main/comp_cache_en.png\" vspace=\"5\" />
<p>3. Dir�jase a las configuraciones del componente y cambie el componente requerido al modo sin cach�.</p>
<img src=\"/bitrix/images/main/spisok_en.png\" vspace=\"5\" />
<p>Despu�s de habilitar el modo de cach�, por defecto todos los componentes con la configuraci�n de Auto cach�<i>\"Auto\"</i> ser�n cambiados a trabajar con el cach�.</p>
<p>Componentes con la configuraci�n <i>\"Cach�\"</i> y con el tiempo de cach� superior a 0 (cero), trabajar�n en el modo cach�.</p>
<p>Componentes con la configuraci�n <i>\"No usar cach�\"</i> o con el tiempo de cach� igual a 0 (cero), trabajar�n sin cach�.</p>";
$MESS["cache_admin_note2"] = "Despu�s de realizar la actualizaci�n de archivos se mostrar� el contenido actualizado de acuerdo a la nueva informaci�n y datos publicados. Los nuevos archivos de cach� se ir�n creando gradualmente mientras se vayan solicitando nuevas p�ginas y estas se vayan guardando en el cach�.";
$MESS["cache_admin_note4"] = "<p>El cach� HTML es recomendado para secciones del sitio web que cambian con poca frecuencia y son mayormente vistas por visitantes an�nimos. Los siguientes procesos se llevan a cabo cuando el cach� HTML es habilitado: </p>
<ul style=\"font-size:100%\">
<li>Proceso de Cache HTML s�lo para p�ginas listadas en la m�scara de inclusi�n y no listadas en la m�scara de exclusi�n;</li>
<li>Para usuarios no autorizados, el sistema revisa si una copia de la p�gina es almacenada en el cach� HTML. Si la p�gina es encontrada en el cach�, esta es mostrada sin incluir funciones de algunos m�dulos del sistema, por ejemplo las estad�sticas no ser�n registradas, el m�dulo principal as� como otros m�dulos no son tomados en cuenta;</li>
<li>Las p�ginas ser�n comprimidas si el M�dulo de Compresi�n est� instalado en el momento de generaci�n del cach�.;</li>
<li>Si no es encontrada la p�gina en el cach�, esta es procesada por la v�a regular. Despu�s de terminada la carga de la p�gina un copia de ella ser� guardada en el cach�;</li>
</ul>
<p>Limpieza de cach�:</p>
<ul style=\"font-size:100%\">
<li>Si al guardar los datos esta excede el espacio disponible en el disco, el cach� ser� completamente limpiado;</li>
<li>Tambi�n se realiza la eliminaci�n completa del cach� si alg�n dato es cambiado mediante el panel de control;</li>
<li>Si los datos son enviados desde las p�ginas p�blicas del sitio web (por ejemplo, la adici�n de comentarios o votos), entonces s�lo las partes seleccionadas del cach� ser�n eliminadas;</li>
</ul>
<p>F�jese que los usuarios no autorizados que vistan p�ginas no cacheadas, iniciar�n una sesi�n y el cach� HTML no ser� m�s usado.</p>
<p>Notas importantes:</p>
<ul style=\"font-size:100%\">
<li>Estad�sticas no ser�n seguidas;</li>
<li>El m�dulo de Publicidad trabajar� s�lo en el momento de creaci�n del cach� HTML. Note que esto no afecta m�dulos externos de publicidad (Google Ad Sense por ejemplo);</li>
<li>El resultado de la comparaci�n de items no ser� guardado para usuarios no autorizados (una sesi�n debe ser iniciada);</li>
<li>La cuota de disco debe ser especificada para evitar ataques DOS;</li>
<li>Toda la funcionalidad de la secci�n del sitio a la que se le habilit� cach� HTML debe ser revisada (por ejemplo los comentarios con las plantillas antiguas del blog, entre otros detalles.);</li>
</ul>";
$MESS["cache_admin_note5"] = "El cach� HTML siempre est� habilitado en esta edici�n.";
$MESS["main_cache_files_continue"] = "Continuar";
$MESS["main_cache_files_delete_errors"] = "Errores en la eliminaci�n: #value#";
$MESS["main_cache_files_deleted_count"] = "Borrado: #value#";
$MESS["main_cache_files_deleted_size"] = "Tama�o de los archivos borrados: #value#";
$MESS["main_cache_files_last_path"] = "Carpeta Actual: #value#";
$MESS["main_cache_files_scanned_count"] = "Procesado: #value#";
$MESS["main_cache_files_scanned_size"] = "Tama�o de los archivos procesados: #value#";
$MESS["main_cache_files_start"] = "Inicio";
$MESS["main_cache_files_stop"] = "Detener";
$MESS["main_cache_finished"] = "Los archivos del cach� han sido borrados.";
$MESS["main_cache_in_progress"] = "Borrando archivos de cach�.";
$MESS["main_cache_managed"] = "Cach� Administrativo";
$MESS["main_cache_managed_const"] = "La constante BX_COMP_MANAGED_CACHE est� definida. El administrador del cach� siempre est� habilitado.";
$MESS["main_cache_managed_note"] = "La tecnolog�a de <b>Dependencias de Cach�</b> actualiza el cach� cada vez que un cambio de datos ocurre. Si esta caracter�stica est� encendida, usted no tendr� que actualizar el cach� manualmente cuando actualiza noticias o productos: los visitantes del sitio web siempre ver�n actualizada la informaci�n.
<br><br>Consiga mayor informaci�n acerca de esta tecnolog�a en el sitio web de Bitrix 
<br><br><span style=\"color:grey\">Nota: no todos los componentes soportan esta caracter�stica.</span>
";
$MESS["main_cache_managed_off"] = "El Cach� Administrativo est� deshabilitado (no recomendado).";
$MESS["main_cache_managed_on"] = "El Cach� Administrativo est� habilitado.";
$MESS["main_cache_managed_saved"] = "La configuraci�n de cach� administrado ha sido guardada.";
$MESS["main_cache_managed_sett"] = "Par�metros de cach� Administrado";
$MESS["main_cache_managed_turn_off"] = "Deshabilitar Cach� Administrativo (no recomendado).";
$MESS["main_cache_managed_turn_on"] = "Habilitar Cach� Administrativo";
$MESS["main_cache_wrong_cache_path"] = "Ruta del archivo de cach� inv�lida.";
$MESS["main_cache_wrong_cache_type"] = "Tipo de cach� es inv�lido.";
?>