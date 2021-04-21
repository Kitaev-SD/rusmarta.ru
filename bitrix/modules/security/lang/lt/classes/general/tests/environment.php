<?
$MESS["SECURITY_SITE_CHECKER_EnvironmentTest_NAME"] = "Aplinkos patikrinimas";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR"] = "Sesijos failø saugyklos katalogas yra pasiekiamas visiems sistemos naudotojams";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_DETAIL"] = "Ðis paþeidþiamumas gali bûti naudojamas skaityti arba keisti sesijos duomenis, paleidþiant skriptà kitame virtualiame serveryje.";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_RECOMMENDATION"] = "Konfigûruoti prieigos teises teisingai, arba pakeisti katalogà. Kitas variantas yra iðsaugoti sesijas duomenø bazëje:<a href=\"/bitrix/admin/security_session.php\">Sesijos apsauga</a>.";
$MESS["SECURITY_SITE_CHECKER_SESSION_DIR_ADDITIONAL"] = "Sesijos iðsaugojimo katalogas: #DIR#<br>
Leidimas: #PERMS#";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION"] = "Sesijos saugojimo katalogas gali turëti ávairiø projektø sesijas.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_DETAIL"] = "Tai gali padëti uþpuolikui skaityti ir raðyti sesijos duomenis, naudojant kitø virtualiø serveriø skriptus.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_RECOMMENDATION"] = "Keisti katalogà ar saugoti sesijas duomenø bazëje: <a href=\"/bitrix/admin/security_session.php\">Sesijos apsauga</a>.";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_OWNER"] = "Prie=astis: failo savininkas yra ne dabartinis naudrtotojas<br>
Failas: #FILE#<br>
Failo savininko UID: #FILE_ONWER#<br>
Dabartinio naudotojo UID: #CURRENT_OWNER#<br>";
$MESS["SECURITY_SITE_CHECKER_COLLECTIVE_SESSION_ADDITIONAL_SIGN"] = "Prieþastis: sesijos failas nëra pasiraðytas dabartiniu savetainës paraðu<br>
Failas: #FILE#<br>
Dabartinis svetainës paraðas: #SIGN#<br>
Failo turinys: <pre>#FILE_CONTENT#</pre>";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP"] = "PHP skriptai yra paleisti ákeltø failø kataloge.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DETAIL"] = "Kartais kûrëjai neskiria pakankamai dëmesio tinkamo failo pavadinimo filtrams. Uþpuolikas gali iðnaudoti ðá paþeidþiamumà ir pradëti visiðkai kontroliuoti jûsø projektà.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_RECOMMENDATION"] = "Konfigûruoti jûsø web serverá teisingai. ";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE"] = "PHP su dvigubu iðplëtimu (pvz php.lala) yra atliekamas ákeltø failø kataloge.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_DETAIL"] = "Kartais kûrëjai neskiria pakankamai dëmesio tinkamo failo pavadinimo filtrams. Uþpuolikas gali iðnaudoti ðá paþeidþiamumà ir pradëti visiðkai kontroliuoti jûsø projektà.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PHP_DOUBLE_RECOMMENDATION"] = "Konfigûruokite jûsø web serverá teisingai. ";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY"] = "Python skriptai yra paleisti ákeltø failø kataloge.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_DETAIL"] = "Kartais kûrëjai neskiria pakankamai dëmesio tinkamo failo pavadinimo filtrams. Uþpuolikas gali iðnaudoti ðá paþeidþiamumà ir pradëti visiðkai kontroliuoti jûsø projektà.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_EXECUTABLE_PY_RECOMMENDATION"] = "Konfigûruokite jûsø web serverá teisingai. ";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS"] = "Apache neturi apdoroti .htaccess failus ákeltø failø kataloge";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_DETAIL"] = "Kartais kûrëjai neskiria pakankamai dëmesio tinkamo failo pavadinimo filtrams. Uþpuolikas gali iðnaudoti ðá paþeidþiamumà ir pradëti visiðkai kontroliuoti jûsø projektà.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_HTACCESS_RECOMMENDATION"] = "Konfigûruokite jûsø web serverá teisingai. ";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION"] = "Apache turinio derybos yra ájungtos failø ákëlimo kataloge.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_DETAIL"] = "Apache turinio derybos nerekomenduojamos, nes jis gali patirti XSS atakas.";
$MESS["SECURITY_SITE_CHECKER_UPLOAD_NEGOTIATION_RECOMMENDATION"] = "Konfigûruokite jûsø web serverá teisingai. ";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER"] = "PHP veikia kaip privilegijuotas naudotojas";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER_DETAIL"] = "Veikiantis PHP kaip priviligijuotas naudotojas (pvz. root) gali sukompromituoti projekto saugumà";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER_RECOMMENDATION"] = "Sukonfigûruokite savo serverá, kad jis veiktø kaip neprivilegijuotas  PHP naudotojas.";
$MESS["SECURITY_SITE_CHECKER_PHP_PRIVILEGED_USER_ADDITIONAL"] = "#UID#/#GID#";
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR"] = "Laikini failai yra kaupiami projekto root kataloge";
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR_DETAIL"] = "Nerekomenduojama saugoti laikinus failus sukurtus CTempFile pagalba root kataloge.";
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR_RECOMMENDATION"] = "Apibrëþkite konstantà \"BX_TEMPORARY_FILES_DIRECTORY\" faile \"bitrix/php_interface/dbconn.php\" ir nurodykite reikiamà kelià. <br />
Atlikite þingsnius: <br />
1. Pasirinkite laikinojo katalogo pavadinimà ir sukurkite já. Pvz.: \"/home/bitrix/tmp/www\":
<pre>
mkdir -p -m 700 /home/bitrix/tmp/www
</pre>
2. Apibrëþkite konstantà , kad sistema þinotø kur jus norite kaupti laikinas bylas:
<pre>
define(\"BX_TEMPORARY_FILES_DIRECTORY\", \"/home/bitrix/tmp/www\");
</pre>";
$MESS["SECURITY_SITE_CHECKER_BITRIX_TMP_DIR_ADDITIONAL"] = "Dabartinis katalogas: #DIR#";
?>