<?
$MESS["STATISTIC_ACTIVITY_EXCEEDING_DESC"] = "#ACTIVITY_TIME_LIMIT# - tiempo del per?odo de prueba (sec.)
#ACTIVITY_HITS# - cantidad de hits para el tiempo del periodo de prueba
#ACTIVITY_HITS_LIMIT# - max de hits para el tiempo del periodo de prueba
#ACTIVITY_EXCEEDING# - actividad superior
#CURRENT_TIME# - tiempo de bloqueo (server time)
#DELAY_TIME# - periodo de bloqueo
#USER_AGENT# - AgentUsuario
#SESSION_ID# - ID de la sesi?n
#SESSION_LINK# - link para el informe de la sesi?n
#SERACHER_ID# - ID del buscador
#SEARCHER_NAME# - nombre del buscador
#SEARCHER_LINK# - link Buscador de lista de hits
#VISITOR_ID# - ID del visitante
#VISITOR_LINK# - link para el perfil del usuario
#STOPLIST_LINK# - link para a?adir al visitante para detener la lista";
$MESS["STATISTIC_ACTIVITY_EXCEEDING_MESSAGE"] = "L?mite de actividad fue superado por los visitantes en el sitio web #SERVER_NAME#.

empezar desde #CURRENT_TIME# the visitor was blocked for #DELAY_TIME# sec.

Actividad  - #ACTIVITY_HITS# hits per #ACTIVITY_TIME_LIMIT# sec. (limit - #ACTIVITY_HITS_LIMIT#)
Visitante   - #VISITOR_ID#
Sesi?n   - #SESSION_ID#
Buscador  - [#SERACHER_ID#] #SEARCHER_NAME#
UserAgent - #USER_AGENT#

>===============================================================
Utilice el siguiente enlace para a?adir a la lista de detenci?n:
http://#SERVER_NAME##STOPLIST_LINK#
Utilice el siguiente enlace para ver el per?odo de sesiones:
http://#SERVER_NAME##SESSION_LINK#
Utilice el siguiente enlace para ver el perfil de visitante:
http://#SERVER_NAME##VISITOR_LINK#
Utilice el siguiente enlace para ver buscador de hits:
http://#SERVER_NAME##SEARCHER_LINK#";
$MESS["STATISTIC_ACTIVITY_EXCEEDING_NAME"] = "L?mite superior a la actividad de los visitantes";
$MESS["STATISTIC_ACTIVITY_EXCEEDING_SUBJECT"] = "#SERVER_NAME#: l{imite en el exceso de actividades";
$MESS["STATISTIC_DAILY_REPORT_DESC"] = "#EMAIL_TO# -  email del administrador del sitio web
#SERVER_TIME# - tiempo del servidor durante el env?o de informe
#HTML_HEADER# - encabezado HTML
tabla de tr?fico del sitio web (hits, sesiones, alojamientos, invitados, eventos) (HTML)
#HTML_ADV# - tabla de campa?as de publicidad (TOP 10) (HTML)
#HTML_EVENTS# - tabla de tipo de eventos (TOP 10) (HTML)
#HTML_REFERERS# - tabla de sitios web referidos (TOP 10) (HTML)
#HTML_PHRASES# - tabla de frases de b?squeda (TOP 10) (HTML)
#HTML_SEARCHERS# - tabla de indexaci?n del sitio web (TOP 10) (HTML)
#HTML_FOOTER# - Pie de p?gina HTML";
$MESS["STATISTIC_DAILY_REPORT_MESSAGE"] = "#HTML_HEADER#
<font class='h2'>Summarized statistics for <font color='#A52929'>#SITE_NAME#</font> site<br>
Data on <font color='#0D716F'>#SERVER_TIME#</font></font>
<br><br>
<a class='tablebodylink' href='http://#SERVER_NAME#/bitrix/admin/stat_list.php?lang=#LANGUAGE_ID#'>http://#SERVER_NAME#/bitrix/admin/stat_list.php?lang=#LANGUAGE_ID#</a>
<br>
<hr><br>
#HTML_COMMON#
<br>
#HTML_ADV#
<br>
#HTML_REFERERS#
<br>
#HTML_PHRASES#
<br>
#HTML_SEARCHERS#
<br>
#HTML_EVENTS#
<br>
<hr>
<a class='tablebodylink' href='http://#SERVER_NAME#/bitrix/admin/stat_list.php?lang=#LANGUAGE_ID#'>http://#SERVER_NAME#/bitrix/admin/stat_list.php?lang=#LANGUAGE_ID#</a>
#HTML_FOOTER#";
$MESS["STATISTIC_DAILY_REPORT_NAME"] = "Las estad?sticas del informe diario del sitio web";
$MESS["STATISTIC_DAILY_REPORT_SUBJECT"] = "#SERVER_NAME#: Estad?sticas del sitio web (#SERVER_TIME#)";
?>