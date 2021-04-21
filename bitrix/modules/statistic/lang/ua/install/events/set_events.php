<?
$MESS["STATISTIC_ACTIVITY_EXCEEDING_NAME"] = "ѕеревищенн€ л≥м≥ту активност≥";
$MESS["STATISTIC_ACTIVITY_EXCEEDING_DESC"] = "#ACTIVITY_TIME_LIMIT# Ч тестовий ≥нтервал часу
#ACTIVITY_HITS# Ч к≥льк≥сть х≥т≥в за тестовий ≥нтервал часу
#ACTIVITY_HITS_LIMIT# Ч максимальна к≥льк≥сть х≥т≥в за тестовий ≥нтервал часу (л≥м≥т активност≥)
#ACTIVITY_EXCEEDING# Ч перевищенн€ к≥лькость х≥т≥в
#CURRENT_TIME# Ч момент блокуванн€ (час на сервер≥)
#DELAY_TIME# Ч тривал≥сть блокуванн€
#USER_AGENT# Ч UserAgent
#SESSION_ID# Ч ID сес≥њ
#SESSION_LINK# Ч посиланн€ на сес≥ю
#SERACHER_ID# Ч ID пошуковика
#SEARCHER_NAME# Ч найменуванн€ пошуковика
#SEARCHER_LINK# Ч посиланн€ на список х≥т≥в пошуковика
#VISITOR_ID# Ч ID в≥дв≥дувача
#VISITOR_LINK# Ч посиланн€ на профайл в≥дв≥дувача
#STOPLIST_LINK# Ч посиланн€ дл€ додаванн€ в≥дв≥дувача в стоп-лист
";
$MESS["STATISTIC_DAILY_REPORT_NAME"] = "ўоденна статистика сайту";
$MESS["STATISTIC_DAILY_REPORT_DESC"] = "#EMAIL_TO# Ч e-mail адм≥н≥стратора сайту
#SERVER_TIME# Ч час на сервер≥ на момент момент надсиланн€ зв≥ту
#HTML_HEADER# Ч в≥дкритт€ тегу HTML + CSS стил≥
#HTML_COMMON# Ч таблиц€ в≥дв≥дуваност≥ сайту (х≥ти, сес≥њ, хости, в≥дв≥дувач≥, под≥њ) (HTML)
#HTML_ADV# Ч таблиц€ рекламних кампан≥й (TOP 10) (HTML)
#HTML_EVENTS# Ч таблиц€ тип≥в под≥й (TOP 10) (HTML)
#HTML_REFERERS# Ч таблиц€ сайт≥в, що посилаютьс€ (TOP 10) (HTML)
#HTML_PHRASES# Ч таблиц€ пошукових фраз (TOP 10) (HTML)
#HTML_SEARCHERS# Ч таблиц€ ≥ндексац≥њ сайта (TOP 10) (HTML)
#HTML_FOOTER# Ч закритт€ тегу HTML";
$MESS["STATISTIC_DAILY_REPORT_SUBJECT"] = "#SERVER_NAME#: —татистика сайту (#SERVER_TIME#)";
$MESS["STATISTIC_DAILY_REPORT_MESSAGE"] = "#HTML_HEADER#
<font class='h2'> ”загальнена статистика сайту <font color='#A52929'>#SITE_NAME#</font><br>
ƒан≥ на <font color='#0D716F'>#SERVER_TIME#</font></font>
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
#HTML_FOOTER#
";
$MESS["STATISTIC_ACTIVITY_EXCEEDING_SUBJECT"] = "#SERVER_NAME#: ѕеревищено л≥м≥т активност≥";
$MESS["STATISTIC_ACTIVITY_EXCEEDING_MESSAGE"] = "Ќа сайт≥ #SERVER_NAME# в≥дв≥дувач перевищив встановлений л≥м≥т активност≥.

ѕочинаючи з #CURRENT_TIME# в≥дв≥дувача заблоковано на #DELAY_TIME# сек.

јктивн≥сть  Ч #ACTIVITY_HITS# хитов за #ACTIVITY_TIME_LIMIT# сек. (л≥м≥тЧ #ACTIVITY_HITS_LIMIT#)
¬≥дв≥дувач  Ч #VISITOR_ID#
—ес≥€ Ч #SESSION_ID#
ѕошуковик Ч [#SERACHER_ID#] #SEARCHER_NAME#
UserAgent Ч #USER_AGENT#

>===============================================================================================
ўоб додати до стоп-листа скористайтес€ нижченаведеним посиланн€м:
http://#SERVER_NAME##STOPLIST_LINK#
ƒл€ перегл€ду сес≥њ в≥дв≥дувача скористайтес€ нижченаведеним посиланн€м:
http://#SERVER_NAME##SESSION_LINK#
ƒл€ перегл€ду профайлу в≥дв≥дувача скористайтес€ нижченаведеним посиланн€м:
http://#SERVER_NAME##VISITOR_LINK#
ƒл€ перегл€ду статистики х≥т≥в пошуковика скористайтес€ нижченаведеним посиланн€м:
http://#SERVER_NAME##SEARCHER_LINK#
";
?>