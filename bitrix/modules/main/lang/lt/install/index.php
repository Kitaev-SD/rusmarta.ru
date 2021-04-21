<?
$MESS["MAIN_ADMIN_GROUP_NAME"] = "Adminisratoriai";
$MESS["MAIN_ADMIN_GROUP_DESC"] = "Pilna prieiga";
$MESS["MAIN_EVERYONE_GROUP_NAME"] = "Visi naudotojai (su neautorizuotais naudotojais)";
$MESS["MAIN_EVERYONE_GROUP_DESC"] = "Visi naudotojai (iskaitant neautorizuotus naudotojus)";
$MESS["MAIN_VOTE_RATING_GROUP_NAME"] = "Naudotojai, kuriems leista balsuoti uþ reitingà";
$MESS["MAIN_VOTE_RATING_GROUP_DESC"] = "Narystë ðioje naudotojø grupëje valdoma automatiðkai.";
$MESS["MAIN_VOTE_AUTHORITY_GROUP_NAME"] = "Naudotojai, kuriems leidþiama balsuoti uþ kompetencijà";
$MESS["MAIN_VOTE_AUTHORITY_GROUP_DESC"] = "Narystë ðioje naudotojø grupëje valdoma automatiðkai.";
$MESS["MAIN_RULE_ADD_GROUP_AUTHORITY_NAME"] = "Uþsiregistruoti naudotojø, kuriems leidþiama balsuoti uþ kompetencijà, grupëje";
$MESS["MAIN_RULE_ADD_GROUP_RATING_NAME"] = "Uþsiregistruoti naudotojø, kuriems leidþiama balsuoti uþ reitingà, grupëje";
$MESS["MAIN_RULE_REM_GROUP_AUTHORITY_NAME"] = "Paðalinti ið naudotojø, kuriems uþdrausta balsuoti uþ kompetencijà, grupës ";
$MESS["MAIN_RULE_REM_GROUP_RATING_NAME"] = "Paðalinti ið naudotojø, kuriems uþdrausta balsuoti uþ reitingà, grupës ";
$MESS["MAIN_RATING_NAME"] = "Reitingas";
$MESS["MAIN_RATING_AUTHORITY_NAME"] = "Kompetencija";
$MESS["MAIN_RATING_TEXT_LIKE_Y"] = "Patinka";
$MESS["MAIN_RATING_TEXT_LIKE_N"] = "Nepatinka";
$MESS["MAIN_RATING_TEXT_LIKE_D"] = "Patinka";
$MESS["MAIN_DEFAULT_SITE_NAME"] = "Numatytojo svetainë";
$MESS["MAIN_DEFAULT_LANGUAGE_NAME"] = "Angliðkai";
$MESS["MAIN_DEFAULT_LANGUAGE_FORMAT_DATE"] = "MM/DD/YYYY";
$MESS["MAIN_DEFAULT_LANGUAGE_FORMAT_DATETIME"] = "MM/DD/YYYY H:MI:SS T";
$MESS["MAIN_DEFAULT_LANGUAGE_FORMAT_NAME"] = "#NAME# #LAST_NAME#";
$MESS["MAIN_DEFAULT_LANGUAGE_FORMAT_CHARSET"] = "iso-8859-1";
$MESS["MAIN_DEFAULT_SITE_FORMAT_DATE"] = "MM/DD/YYYY";
$MESS["MAIN_DEFAULT_SITE_FORMAT_DATETIME"] = "MM/DD/YYYY H:MI:SS T";
$MESS["MAIN_DEFAULT_SITE_FORMAT_NAME"] = "#NAME# #LAST_NAME#";
$MESS["MAIN_DEFAULT_SITE_FORMAT_CHARSET"] = "iso-8859-1";
$MESS["MAIN_MODULE_NAME"] = "Pagrindinis modulis";
$MESS["MAIN_MODULE_DESC"] = "Produkto branduolys";
$MESS["MAIN_INSTALL_DB_ERROR"] = "Nepavyko prisijungti prie duomenø bazës. Praðome patikrinti parametrus.";
$MESS["MAIN_NEW_USER_TYPE_NAME"] = "Buvo registruotas naujas naudotojas";
$MESS["MAIN_NEW_USER_TYPE_DESC"] = "
#USER_ID# - Naudotojo ID
#LOGIN# - Prisijungimo vardas
#EMAIL# - El.paðtas
#NAME# - Vardas
#LAST_NAME# - Pavardë
#USER_IP# - Naudotojo IP
#USER_HOST# - Naudotojos serveris";
$MESS["MAIN_USER_INFO_TYPE_NAME"] = "Paskyros informacija";
$MESS["MAIN_USER_INFO_TYPE_DESC"] = "
#USER_ID# - Naudotojo ID
#STATUS# -Paskyros statusas
#MESSAGE# - Praneðimas naudotojui
#LOGIN# - Prisijungimo vardas
#CHECKWORD# - Kontrolinë eilutë slaptaþodþio pakeitimui
#NAME# - Vardas
#LAST_NAME# - Pavardë
#EMAIL# - Naudotojo el.paðtas";
$MESS["MAIN_NEW_USER_CONFIRM_TYPE_NAME"] = "Naujo naudotojo registracijos informacija";
$MESS["MAIN_NEW_USER_CONFIRM_TYPE_DESC"] = "
#USER_ID# - Naudotojo ID
#LOGIN# - Prisijungimo vardas
#EMAIL# - El.paðtas
#NAME# - Vardas
#LAST_NAME# - Pavardë
#USER_IP# - Naudotojo IP
#USER_HOST# - Naudotojo serveris
#CONFIRM_CODE# - Patvirtinimo kodas
";
$MESS["MAIN_USER_INVITE_TYPE_NAME"] = "Naujo naudotojo pakvietimas";
$MESS["MAIN_USER_INVITE_TYPE_DESC"] = "#ID# - Naudotojo ID
#LOGIN# -Prisijungimo vardas
#URL_LOGIN# - Koduotas prisijungimo vardas naudoti URL
#EMAIL# - El.paðtas
#NAME# - Vardas
#LAST_NAME# - Pavardë
#PASSWORD# - Naudotojo slaptaþodis
#CHECKWORD# - Slaptaþodþio patikrinimo eilutë
#XML_ID# - Naudotojo ID susieti su iðoriniais duomenø ðaltiniais
";
$MESS["MAIN_NEW_USER_EVENT_NAME"] = "#SITE_NAME#: Naujas naudotojas buvo áregistruotas svetainëje";
$MESS["MAIN_NEW_USER_EVENT_DESC"] = "Informacinis praneðimas ið #SITE_NAME#
---------------------------------------

Naujas naudotojas buvo sëkmingai áregistruotas svetainëje  #SERVER_NAME#.

Naudotojo informacija:
Naudotojo ID: #USER_ID#

Vardas: #NAME#
Pavardë: #LAST_NAME#
Naudotojo el.paðtas: #EMAIL#

Prisijungimo vardas: #LOGIN#

Praneðimas sugeneruotas automatiðkai.";
$MESS["MAIN_USER_INFO_EVENT_NAME"] = "#SITE_NAME#: Registracijos informacija";
$MESS["MAIN_USER_INFO_EVENT_DESC"] = "Informacinis praneðimas ið #SITE_NAME#
---------------------------------------

#NAME# #LAST_NAME#,

#MESSAGE#

Jûsø registracijos informacija:

Naudotojo ID: #USER_ID#
Paskyros statusas: #STATUS#
Prisijungimo vardas: #LOGIN#

To change your password please visit the link below:
http://#SERVER_NAME#/auth/index.php?change_password=yes&lang=en&USER_CHECKWORD=#CHECKWORD#&USER_LOGIN=#LOGIN#

Praneðimas sugeneruotas automatiðkai.";
$MESS["MAIN_USER_PASS_REQUEST_EVENT_DESC"] = "Informacinis praneðimas nuo #SITE_NAME#
---------------------------------------

#NAME# #LAST_NAME#,

#MESSAGE#

Norëdami pakeisti slaptaþodá, praðome sekti nuorodà::
http://#SERVER_NAME#/auth/index.php?change_password=yes&lang=en&USER_CHECKWORD=#CHECKWORD#&USER_LOGIN=#URL_LOGIN#

Jûsø registracijos informacija:

Naudotojo ID: #USER_ID#
Paskyros statusas: #STATUS#
Prisijungimo vardas: #LOGIN#

Automatiðkai sugeneruotas praneðimas.";
$MESS["MAIN_USER_PASS_CHANGED_EVENT_DESC"] = "Informacinis praneðimas nuo #SITE_NAME#
---------------------------------------

#NAME# #LAST_NAME#,

#MESSAGE#

Jûsø registracijos informacija:

Naudotojo ID: #USER_ID#
Paskyros statusas: #STATUS#
Prisijungimo vardas: #LOGIN#

Automatiðkai sugeneruotas praneðimas.";
$MESS["MAIN_NEW_USER_CONFIRM_EVENT_NAME"] = "#SITE_NAME#: Naujo naudotojo registracijos patvirtinimas";
$MESS["MAIN_NEW_USER_CONFIRM_EVENT_DESC"] = "Sveikinimai nuo #SITE_NAME#!
------------------------------------------

Sveiki,

Jûs gavote ðá laiðkà, nes jûs (arba kas nors kitas), naudojosi jûsø el.paðtu ir uþsiregistravo #SERVER_NAME#.

Jûsø registracijos patvirtinimo kodas: #CONFIRM_CODE#

Praðome naudoti nuorodà þemiau, norëdami patikrinti ir aktyvuoti savo registracijà:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#

Arba atidarykite ðià nuorodà savo narðyklëje ir áveskite kodà rankiniu bûdu:
http://#SERVER_NAME#/auth/index.php?confirm_registration=yes&confirm_user_id=#USER_ID#

Dëmesio! Jûsø paskyra nebus aktyvuota, kol nepatvirtinsite registracijà.

---------------------------------------------------------------------

Automatiðkai sugeneruotas praneðimas.";
$MESS["MAIN_USER_INVITE_EVENT_NAME"] = "#SITE_NAME#: Pakvietimas á svetainæ";
$MESS["MAIN_USER_INVITE_EVENT_DESC"] = "
Informacinis praneðimas nuo #SITE_NAME#
------------------------------------------
Sveiki #NAME# #LAST_NAME#!

Administratorius pridëjo jus prie registruotø svetainës naudotojø.

Kvieèiame apsilankyti mûsø svetainëje.

Jûsø registracijos informacija:

Naudotojo ID: #ID#
Prisijungimo vardas: #LOGIN#

Mes rekomenduojame jums pakeisti automatiðkai sugeneruotà slaptaþodá.

Norëdami pakeisti slaptaþodá, praðome sekti nuorodà:
http://#SERVER_NAME#/auth.php?change_password=yes&USER_LOGIN=#URL_LOGIN#&USER_CHECKWORD=#CHECKWORD#";
$MESS["MF_EVENT_NAME"] = "Siunèiamas praneðimas naudojant gráþtamojo ryðio formà";
$MESS["MF_EVENT_DESCRIPTION"] = "#AUTHOR# - Praneðimo autorius
#AUTHOR_EMAIL# - Autoriaus el.paðtas
#TEXT# - Praneðimo tekstas
#EMAIL_FROM# - Siuntëjo el.paðto adresas
#EMAIL_TO# - Gavëjo el.paðto adresas";
$MESS["MF_EVENT_SUBJECT"] = "#SITE_NAME#: Atsiliepimo formos praneðimas";
$MESS["MF_EVENT_MESSAGE"] = "Praneðimas nuo #SITE_NAME#
------------------------------------------

Praneðimas buvo iðsiøstas ið gráþtamojo ryðio formos.

Iðsiuntë: #AUTHOR#
Siuntëjo el.paðtas: #AUTHOR_EMAIL#

Praneðimo tekstas:
#TEXT#

Ðis praneðimas buvo sukurtas automatiðkai.";
$MESS["MAIN_USER_PASS_REQUEST_TYPE_NAME"] = "Slaptaþodþio pakeitimo praðymas ";
$MESS["MAIN_USER_PASS_CHANGED_TYPE_NAME"] = "Slaptaþodþio pakeitimo patvirtinimas";
$MESS["MAIN_USER_PASS_REQUEST_EVENT_NAME"] = "#SITE_NAME#: Slaptaþodþio pakeitimo praðymas ";
$MESS["MAIN_USER_PASS_CHANGED_EVENT_NAME"] = "#SITE_NAME#: Slaptaþodþio pakeitimo patvirtinimas";
$MESS["MAIN_DESKTOP_CREATEDBY_KEY"] = "Sukurta";
$MESS["MAIN_DESKTOP_CREATEDBY_VALUE"] = "Bitrix, Inc. ";
$MESS["MAIN_DESKTOP_URL_KEY"] = "Svetainës URL";
$MESS["MAIN_DESKTOP_URL_VALUE"] = "<a href=\"http://www.bitrixsoft.com\">www.bitrixsoft.com</a>";
$MESS["MAIN_DESKTOP_PRODUCTION_KEY"] = "Iðleistas";
$MESS["MAIN_DESKTOP_PRODUCTION_VALUE"] = "12.12.2011";
$MESS["MAIN_DESKTOP_RESPONSIBLE_KEY"] = "Administratorius";
$MESS["MAIN_DESKTOP_RESPONSIBLE_VALUE"] = "John Doe";
$MESS["MAIN_DESKTOP_EMAIL_KEY"] = "El. paðtas";
$MESS["MAIN_DESKTOP_EMAIL_VALUE"] = "<a href=\"mailto:info@bitrixsoft.com\">info@bitrixsoft.com</a>";
$MESS["MAIN_DESKTOP_INFO_TITLE"] = "Svetainës informacija";
$MESS["MAIN_DESKTOP_RSS_TITLE"] = "Bitrix naujienos";
$MESS["MAIN_RULE_AUTO_AUTHORITY_VOTE_NAME"] = "Automatinis balsavimas uþ naudotojo kompetencijà";
$MESS["MAIN_SMILE_DEF_SET_NAME"] = "Numatytieji nustatymai";
$MESS["MAIN_MAIL_CONFIRM_EVENT_TYPE_NAME"] = "Patvirtinkite siuntëjo el. paðto adresà";
$MESS["MAIN_MAIL_CONFIRM_EVENT_TYPE_DESC"] = "
#EMAIL_TO# - patvirtinimo el. paðto adresas
#MESSAGE_SUBJECT# - þinutës tema
#CONFIRM_CODE# - patvirtinimo kodas";
?>