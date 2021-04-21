<?
$MESS["ADV_BANNER_STATUS_CHANGE_DESC"] = "#EMAIL_TO# - Correo electr�nico del receptor (#OWNER_EMAIL#)
#ADMIN_EMAIL# - Correo electr�nico del usuarios con roles de \"administrar banners\" y \"administrador\"
#ADD_EMAIL# - Correo electr�nico de los administradores de banners
#STAT_EMAIL# - EMail de los usuarios que tienen permisos para ver las estad�sticas del banner
#EDIT_EMAIL# - EMail de los usuarios que tienen permiso para modificar algunos campos del contrato
#OWNER_EMAIL# - EMail de los usuarios que tienen cualquier permiso en el contrato
#BCC# - copiar (#ADMIN_EMAIL#)
#ID# -  ID del banner
#CONTRACT_ID# - ID del contrato
#CONTRACT_NAME# - T�tulo del contrato
#TYPE_SID# - Tipo de ID
#TYPE_NAME# - Tipo de t�tulo
#STATUS# - Estado
#STATUS_COMMENTS# - Comentarios para el estado
#NAME# - T�tulo del banner
#GROUP_SID# - Grupo de banner
#ACTIVE# - Actividad de la bandera del banner [Y | N]
#INDICATOR# - Est� el banner mostr�ndose en el sitio web?
#SITE_ID# - Parte del idioma para la exhibici�n del banner
#WEIGHT# - Peso (prioridad)
#MAX_SHOW_COUNT# - N�mero m�ximo de banners mostrados
#SHOW_COUNT# - N�mero de banners mostrados
#MAX_CLICK_COUNT# - M�ximo n�mero de clicks en el banner
#CLICK_COUNT# - N�mero de clicks en el banner
#DATE_LAST_SHOW# - Fecha de la �ltima exhibici�n del banner
#DATE_LAST_CLICK# - Fecha del �ltimo click del banner
#DATE_SHOW_FROM# - Fecha de inicio del per�odo que muestra el banner
#DATE_SHOW_TO# - Fecha de finalizaci�n que se muestra el banner
#IMAGE_LINK# - Link de la imagen
#IMAGE_ALT# - Sugerencia del texto de la imagen
#URL# - URL en la imagen
#URL_TARGET# - Donde abrir URL
#CODE# - C�digo del banner
#CODE_TYPE# - Tipo de c�digo del banner  (texto | html)
#COMMENTS# - Comentarios del banner
#DATE_CREATE# - Fecha de creaci�n del banner
#CREATED_BY# - Banner
#DATE_MODIFY# - Fecha de modificaci�n del banner
#MODIFIED_BY# - Qui�n modic� el banner";
$MESS["ADV_BANNER_STATUS_CHANGE_MESSAGE"] = "estado del banner # #ID# fue cambiado a [#STATUS#].
==================== Configuraciones del banner====================";
$MESS["ADV_BANNER_STATUS_CHANGE_NAME"] = "El estado del banner fue cambiado";
$MESS["ADV_BANNER_STATUS_CHANGE_SUBJECT"] = "[BID##ID#] #SITE_NAME#: estado del banner se ha cambiado - [#STATUS#]";
$MESS["ADV_CONTRACT_INFO_DESC"] = "#ID# - ID del contrato
#MESSAGE# - mensaje
#EMAIL_TO# - EMail del receptor del mensaje
#ADMIN_EMAIL# - EMail de los usuarios con roles \"administradores de banners\" y \"administrador\"
#ADD_EMAIL# - EMail de los administradores de banners
#STAT_EMAIL# - EMail de los usuarios que tienen permisos para ver estad�sticas del banner #EDIT_EMAIL# - EMail de los usuarios que tienen permisos para modificar algunos campos del contrato #OWNER_EMAIL# - EMail de usuarios que tienen cualquier permiso sobre el contrato
#BCC# - copiar
#INDICATOR# - Se muestran los banners de contrato en el sitio web?
#ACTIVE# - bandera de actividad del contrato [Y | N]
#NAME# - t�tulo del contrato
#DESCRIPTION# - descripci�n del contrato
#MAX_SHOW_COUNT# - n�mero m�ximo de todos los anuncios de banners de contrato
#SHOW_COUNT# - n�mero de todos los anuncios de banners de contrato
#MAX_CLICK_COUNT# - n�mero m�ximo de todos los clicks de banners de contrato
#CLICK_COUNT# - n�mero de todos los clicks de banners de contrato
#BANNERS# - n�mero de banners de contrato
#DATE_SHOW_FROM# - fecha de inicio del per�odo de demostraci�n del banner
#DATE_SHOW_TO# - fecha de finalizaci�n del per�odo de demostraci�n del banner
#DATE_CREATE# - fecha de creaci�n del contrato
#CREATED_BY# - creador del contrato
#DATE_MODIFY# - fecha de modificaci�n del contrato
#MODIFIED_BY# - qui�n ha modificado el contrato";
$MESS["ADV_CONTRACT_INFO_MESSAGE"] = "#MESSAGE#
Contrato: [#ID#] #NAME#
#DESCRIPTION#
=================== Configuraciones del contrato==============================

Actividad: #INDICATOR#

Per�odo    - [#DATE_SHOW_FROM# - #DATE_SHOW_TO#]
Mostrar    - #SHOW_COUNT# / #MAX_SHOW_COUNT#
Clicked   - #CLICK_COUNT# / #MAX_CLICK_COUNT#
Act. bandera - [#ACTIVE#]

Banners   - #BANNERS#
=====================================================================

Creado  - #CREATED_BY# [#DATE_CREATE#]
Cambiado  - #MODIFIED_BY# [#DATE_MODIFY#]

para ver los ajustes visitar el link:
http://#SERVER_NAME#/bitrix/admin/adv_contract_edit.php?ID=#ID#&lang=#LANGUAGE_ID#

Mensaje generado autom�ticamente.";
$MESS["ADV_CONTRACT_INFO_NAME"] = "Ajustes del contrato de publicidad";
$MESS["ADV_CONTRACT_INFO_SUBJECT"] = "[CID##ID#] #SITE_NAME#: Ajustes del contrato de publicidad";
?>