<?
$MESS["SUBSCRIBE_CONFIRM_NAME"] = "Prenumeratos patvirtinimas";
$MESS["SUBSCRIBE_CONFIRM_DESC"] = "#ID# - prenumeratos ID
#EMAIL# - prenumeratos el.paðto adresas
#CONFIRM_CODE# - patvirtinimo kodas
#SUBSCR_SECTION# - skyriuje su prenumeratos redagavimo puslapiu (nurodomas nustatymuose)
#USER_NAME# - prenumeratoriaus vardas (neprivaloma)
#DATE_SUBSCR# - adreso ávedimo/keitimo data
";
$MESS["SUBSCRIBE_CONFIRM_SUBJECT"] = "#SITE_NAME#: Prenumeratos patvirtinimas";
$MESS["SUBSCRIBE_CONFIRM_MESSAGE"] = "Informacinis praneðimas nuo #SITE_NAME#
---------------------------------------

Sveiki,

Jûs gavote ðá praneðimà, nes prenumeratos praðymas buvo pateiktas jûsø adresui naujienoms ið #SERVER_NAME# gauti.

Èia yra detali informacija apie jûsø prenumeratà:

Prenumeratos el.adresas .............. #EMAIL#
El.adreso ávedimo/redagavimo data .... #DATE_SUBSCR#

Jûsø patvirtinimo kodas: #CONFIRM_CODE#

Praðome spausti pateiktà ðiame laiðke nuorodà jûsø prenumeratai patvirtinti.
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#

Arba eikite á ðá puslapá ir áveskite savo patvirtinimo kodà rankiniu bûdu:
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#

Jûs negausite jokio praneðimo, kol neiðsiøsite mums savo patvirtinimo.

---------------------------------------------------------------------
Praðome iðsaugoti ðá praneðimà, nes jame yra informacija apie autorizavimà.
Naudojant patvirtinimo kodà, jûs galite pakeisti prenumeratos parametrus arba jos 
atsisakyti.

Redaguoti parametrus:
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#

Atsisakyti prenumeratos:
http://#SERVER_NAME##SUBSCR_SECTION#subscr_edit.php?ID=#ID#&CONFIRM_CODE=#CONFIRM_CODE#&action=unsubscribe
---------------------------------------------------------------------

Ðis praneðimas sukurtas automatiðkai
";
?>