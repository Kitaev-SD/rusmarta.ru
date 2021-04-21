<?
$MESS["ADV_BANNER_STATUS_CHANGE_NAME"] = "«м≥нивс€ статус банера";
$MESS["ADV_BANNER_STATUS_CHANGE_DESC"] = "#EMAIL_TO# Ч e-mail одержувача пов≥домленн€ (#OWNER_EMAIL#)
#ADMIN_EMAIL# Ч e-mail користувач≥в, що мають роль Ђменеджер банер≥вї ≥ Ђадм≥н≥страторї
#ADD_EMAIL# Ч e-mail користувач≥в, що мають право керуванн€ банерами контракту
#STAT_EMAIL# Ч e-mail користувач≥в, що мають право перегл€ду банер≥в конракту
#EDIT_EMAIL# Ч e-mail користувач≥в, що мають право модиф≥кац≥њ де€ких пол≥в контракту
#OWNER_EMAIL# Ч e-mail користувач≥в, що мають будь €ке право на контракт
#BCC# Ч прихована коп≥€ (#ADMIN_EMAIL#)
#ID# Ч ID банера
#CONTRACT_ID# Ч ID контракту
#CONTRACT_NAME# Ч заголовок контракту
#TYPE_SID# Ч ID типу
#TYPE_NAME# Ч заголовок типу
#STATUS# Ч статус
#STATUS_COMMENTS# Ч коментар до статусу
#NAME# Ч заголовок банера
#GROUP_SID# Ч група банера
#INDICATOR# Ч чи показуЇтьс€ банер на сайт≥?
#ACTIVE# Ч прапор активност≥ банера [Y | N]
#MAX_SHOW_COUNT# Ч максимальна к≥льк≥сть показ≥в банера
#SHOW_COUNT# Ч ск≥льки раз≥в банер був показаний на сайт≥
#MAX_CLICK_COUNT# Ч максимальна к≥льк≥сть кл≥к≥в на банер
#CLICK_COUNT# Ч ск≥льки раз≥в кл≥кнули на банер
#DATE_LAST_SHOW# Ч дата останнього показу банера
#DATE_LAST_CLICK# Ч дата останнього кл≥ка на банер
#DATE_SHOW_FROM# Ч дата початку показу банера
#DATE_SHOW_TO# Ч дата зак≥нченн€ показу банера
#IMAGE_LINK# Ч посиланн€ на зображенн€ банера
#IMAGE_ALT# Ч текст п≥дказки на зображенн≥
#URL# Ч URL на зображенн≥
#URL_TARGET# Ч де розгорнути URL зображенн€
#CODE# Ч код банера
#CODE_TYPE# Ч тип коду банера (text | html)
#COMMENTS# Ч коментар до банеру
#DATE_CREATE# Ч дата створенн€ банера
#CREATED_BY# Ч ким був створений банер
#DATE_MODIFY# Ч дата зм≥ни банера
#MODIFIED_BY# Ч ким зм≥нено банер
";
$MESS["ADV_BANNER_STATUS_CHANGE_SUBJECT"] = "[BID##ID#] #SITE_NAME#: «м≥нивс€ статус банера Ч [#STATUS#]";
$MESS["ADV_BANNER_STATUS_CHANGE_MESSAGE"] = "—татус банера # #ID# зм≥нивс€ на [#STATUS#].

>=================== ѕараметри баннера ===============================

Ѕанер   Ч [#ID#] #NAME#
 онтракт Ч [#CONTRACT_ID#] #CONTRACT_NAME#
“ип Ч [#TYPE_SID#] #TYPE_NAME#
√рупа Ч #GROUP_SID#

----------------------------------------------------------------------

јктивн≥сть: #INDICATOR#

ѕер≥од Ч [#DATE_SHOW_FROM# - #DATE_SHOW_TO#]
ѕоказано Ч #SHOW_COUNT# / #MAX_SHOW_COUNT# [#DATE_LAST_SHOW#]
 л≥кнули Ч #CLICK_COUNT# / #MAX_CLICK_COUNT# [#DATE_LAST_CLICK#]
ѕрапор акт. Ч [#ACTIVE#]
—татус Ч [#STATUS#]
 оментар:
#STATUS_COMMENTS#
----------------------------------------------------------------------

«ображенн€ Ч [#IMAGE_ALT#] #IMAGE_LINK#
URL  Ч [#URL_TARGET#] #URL#

 од: [#CODE_TYPE#]
#CODE#

>=====================================================================

—творено Ч #CREATED_BY# [#DATE_CREATE#]
«м≥нено Ч #MODIFIED_BY# [#DATE_MODIFY#]

ƒл€ перегл€ду параметр≥в банера скористайтес€ посиланн€м:
http://#SERVER_NAME#/bitrix/admin/adv_banner_edit.php?ID=#ID#&CONTRACT_ID=#CONTRACT_ID#&lang=#LANGUAGE_ID#

Ћичт сгенеровано автоматично.";
$MESS["ADV_CONTRACT_INFO_NAME"] = "ѕараметри рекламного контракту";
$MESS["ADV_CONTRACT_INFO_DESC"] = "#MESSAGE# Ч пов≥домленн€
#EMAIL_TO# Ч e-mail одержувача пов≥домленн€ (#OWNER_EMAIL#)
#ADMIN_EMAIL# Ч e-mail користувач≥в, що мають роль Ђменеджер банер≥вї ≥ Ђадм≥н≥страторї
#ADD_EMAIL# Ч e-mail користувач≥в, що мають право керуванн€ банерами контракту
#STAT_EMAIL# Ч e-mail користувач≥в, що мають право перегл€ду банер≥в конракту
#EDIT_EMAIL# Ч e-mail користувач≥в, що мають право модиф≥кац≥њ де€ких пол≥в контракту
#OWNER_EMAIL# Ч e-mail користувач≥в, що мають будь €ке право на контракт
#BCC# Ч прихована коп≥€ (#ADMIN_EMAIL#)
#ID# Ч ID банера
#INDICATOR# Ч чи показуЇтьс€ банер на сайт≥?
#ACTIVE# Ч прапор активност≥ банера [Y | N]
#NAME# Ч заголовок банера
#DESCRIPTION# Ч опис контракту
#MAX_SHOW_COUNT# Ч максимальна к≥льк≥сть показ≥в банера
#SHOW_COUNT# Ч ск≥льки раз≥в банер був показаний на сайт≥
#MAX_CLICK_COUNT# Ч максимальна к≥льк≥сть кл≥к≥в на банер
#CLICK_COUNT# Ч ск≥льки раз≥в кл≥кнули на банер 
#BANNERS# Ч к≥льк≥сть банер≥в контракту 
#DATE_SHOW_FROM# Ч дата початку показу банера
#DATE_SHOW_TO# Ч дата зак≥нченн€ показу банера
#DATE_CREATE# Ч дата створенн€ баннера
#CREATED_BY# Ч ким був створений банер
#DATE_MODIFY# Ч дата зм≥ни банера
#MODIFIED_BY# Ч ким зм≥нено банер";
$MESS["ADV_CONTRACT_INFO_SUBJECT"] = "[CID##ID#] #SITE_NAME#: ѕараметри рекламного контракту";
$MESS["ADV_CONTRACT_INFO_MESSAGE"] = "#MESSAGE#
 онтракт: [#ID#] #NAME#
#DESCRIPTION#
>================== ѕараметри контракту ==============================

јктивн≥сть: #INDICATOR#

ѕер≥од Ч [#DATE_SHOW_FROM# - #DATE_SHOW_TO#]
ѕоказано Ч #SHOW_COUNT# / #MAX_SHOW_COUNT#
 л≥кнули Ч #CLICK_COUNT# / #MAX_CLICK_COUNT#
ѕрапор акт. Ч [#ACTIVE#]

Ѕанер≥в Ч #BANNERS#
>=====================================================================

—творено Ч #CREATED_BY# [#DATE_CREATE#]
«м≥нено Ч #MODIFIED_BY# [#DATE_MODIFY#]

ƒл€ перегл€ду параметр≥в контракту скористайтес€ посиланн€м:
http://#SERVER_NAME#/bitrix/admin/adv_contract_edit.php?ID=#ID#&lang=#LANGUAGE_ID#

Ћист сгенеровано автоматично.";
?>