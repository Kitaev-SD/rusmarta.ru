<?
$MESS["SEC_OTP_SECRET_KEY"] = "Slaptas raktas (tiekiamas su árenginiu)";
$MESS["SEC_OTP_INIT"] = "Inicializacija";
$MESS["SEC_OTP_PASS1"] = "Pirmasis árenginio slaptaþodá (paspauskite ir uþraðykite)";
$MESS["SEC_OTP_PASS2"] = "Antrasis árenginio slaptaþodá (paspauskite ir uþraðykite)";
$MESS["SEC_OTP_TYPE"] = "Slaptaþodþio generavimo algoritmas";
$MESS["SEC_OTP_STATUS"] = "Dabartinis statusas";
$MESS["SEC_OTP_STATUS_ON"] = "Ájungtas";
$MESS["SEC_OTP_NEW_ACCESS_DENIED"] = "Prieiga prie dviejø etapø autentifikavimo kontrolës buvo atmesta.";
$MESS["SEC_OTP_NEW_SWITCH_ON"] = "Ájungti dviejø etapø autentifikacijà";
$MESS["SEC_OTP_DESCRIPTION_INTRO_TITLE"] = "Vienkartinis slaptaþodis";
$MESS["SEC_OTP_DESCRIPTION_INTRO_SITE"] = "Ðiandien naudotojas panaudoja prisijungimo vardà ir slaptaþodá, norëdamas prisijungti prie jûsø svetainës. Taèiau yra bûdai, kaip piktybinis naudotojas gali patekti á jûsø kompiuterá ir pavogti ðiuos duomenis, pavyzdþiui, jei naudotojas iðsaugo savo slaptaþodá. <br>
<b> Dviejø þingsniø autentifikavimas </b> yra rekomenduojama galimybë apsisaugoti nuo ásilauþëliø programinës árangos. Kiekvienà kartà, kai naudotojas prisijungia prie sistemos, jis turës iðlaikyti dviejø lygiø patikrinimà. Pirma, ávesti naudotojo vardà ir slaptaþodá. Po to ávesti vienkartiná saugos kodà, iðsiøsti á jo mobiløjá prietaisà. Esmë yra ta, kad uþpuolikas negali pavogti duomenø, nes jis neþino saugumo kodo.";
$MESS["SEC_OTP_DESCRIPTION_INTRO_INTRANET"] = "Nuo ðiol Jûsø Bitrix24 yra apsaugotas duomenø ðifravimo technologija, prisijungimo vardu ir slaptaþodþiu kiekvienam darbuotojui.
<br>
<b>Dviejø þingsniø autentifikavimas </b> yra rekomenduojama galimybë apsisaugoti jûsø Bitrix24 nuo ásilauþëliø programinës árangos. Kiekvienà kartà, kai naudotojas prisijungia prie sistemos, jis turës iðlaikyti dviejø lygiø patikrinimà. Pirma, ávesti naudotojo vardà ir slaptaþodá. Po to ávesti vienkartiná saugos kodà, iðsiøsti á jo mobiløjá prietaisà. Esmë yra ta, kad uþpuolikas negali pavogti duomenø, nes jis neþino saugumo kodo.";
$MESS["SEC_OTP_DESCRIPTION_USING_TITLE"] = "Naudojamas vienkartinis slaptaþodis";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_0"] = "Þingsnis 1";
$MESS["SEC_OTP_DESCRIPTION_USING_STEP_1"] = "Þingsnis 2";
$MESS["SEC_OTP_DESCRIPTION_USING"] = "Kai ájungtas dviejø etapø autentifikavimas, naudotojas turës iðlaikyti dviejø lygiø patikrinimà. <br>
Pirma, ávesti savo elektroninio paðto adresà ir slaptaþodá, kaip áprasta. <br>
Po to ávesti vienkartiná apsaugos kodà, iðsiøsta á jo mobiløjá prietaisà ar gautà specialøjá raktà.";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION_TITLE"] = "Aktivavimas ";
$MESS["SEC_OTP_DESCRIPTION_ACTIVATION"] = "Vienkartinis kodas dviejø pakopø autentifikavimui gali bûti gautas naudojant specialø átaisà (raktas), arba nemokamà mobiliàjà aplikacijà (Bitrix OTP) kiekvienas naaudotojas turi ádiegti savo mobiliajame prietaise. <br>
Norëdamas ájungti raktà, administratorius turës atidaryti naudotojo profilá ir ávesti du sugeneruotus slaptaþodþius. <br>
Norëdamas gauti vienkartiná kodà á mobiløjá prietaisà, naudotojas gali atsisiøsti ir paleisti aplikacijà ir nuskaityti QR kodà nustatymø puslapyje savo naudotojo profilyje arba ávesti paskyros duomenis rankiniu bûdu.";
$MESS["SEC_OTP_DESCRIPTION_ABOUT_TITLE"] = "Apraðymas";
$MESS["SEC_OTP_DESCRIPTION_ABOUT"] = "Vienkartinis slaptaþodis (OTP) buvo sukurtas kaip dalis OATH iniciatyva. <br>
OTP remiasi hmac ir SHA-1 / SHA-256 / SHA-512. Ðiuo metu, du algoritmai yra palaikomi generuoti kodus:
<ul> <li> pagal skaitiklá (hmac Vienkartinis slaptaþodis, HOTP), kaip apraðyta <a href=\"https://tools.ietf.org/html/rfc4226\" target=\"_blank\"> RFC4226 </> </li>
<li> pagal laikà (laiko pagrindu vienkartinis slaptaþodá, TOTP), kaip apraðyta <a href=\"https://tools.ietf.org/html/rfc6238\" target=\"_blank\"> RFC6238 </a> </li> </ul>
Apskaièiuoti OTP rertë algoritmas paima du ávesties parametrus: slaptas raktas (pradinë reikðmë) ir dabartinë skaitiklio vertë (reikalingø ciklø generavimo skaièiø arba esamà laikà, priklausomai nuo algoritmo). Pradinë reikðmë yra iðsaugoma prietaise ir svetainëje, kai tik prietaisas buvo inicijuotas. Jei naudojant HOTP, prietaiso skaitiklis didëja per kiekvienà OTP generavimà, o serverio skaitiklis keièiasi per kiekvienà sëkmingà OTP autentifikavimà. Jei naudojate TOTP, skaitiklis nëra iðsaugomas prietaise, ir serveris stebi galimus laiko pokyèius prietaise per kiekvienà sëkmingà OTP autentifikavimà. <br>
Kiekvienas ið OTP prietaisø partijoje turi ðifruotà failà, kuriame yra pradinës reikðmës (slapti raktai) kiekvienam partijos árenginiui, failas pririðtas prie prietaiso serijos numerio, kurá galima rasti ant prietaiso. <br>
Jei prietaiso ir serverio generatoriaus skaitikliai tampa nesinchronizuoti, jûs galite lengvai sinchronizuot juos ið naujo pagal serverio reikðmes á reikðmes, saugomus árenginyje. Ði procedûra reikalauja, kad sistemos administratorius (ar naudotojas, kuris turi pakankamai teisiø) generuotø dvi ið eilës OTP reikðmes ir ávestø juos svetainëje.<br>
Jûs galite rasti mobiliàjà aplikacijà AppStore ir GooglePlay.";
$MESS["SEC_OTP_CONNECT_MOBILE_TITLE"] = "Pajungti mobilø prietaisà";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_1"] = "Atsisiøsti Bitrix OTP mobiliojo aplikacijà jûsø telefonui <a href=\"https://itunes.apple.com/en/app/bitrix24-otp/id929604673?l=en\" target=\"_new\">AppStore</a> on <a href=\"https://play.google.com/store/apps/details?id=com.bitrixsoft.otp\" target=\"_new\">GooglePlay</a>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_2"] = "Paleisti aplikacijà ir paspausti <b>Konfigûruoti</b>";
$MESS["SEC_OTP_CONNECT_MOBILE_STEP_3"] = "Pasirinkite, kaip jûs norite ávesti duomenis: naudojant QR kodà rankiniu bûdu";
$MESS["SEC_OTP_CONNECT_MOBILE_SCAN_QR"] = "Pridëkite jûsø mobiliøjá prietaisà prie monitoriaus ir palaukite, kol aplikacija nuskaitys kodà.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT"] = "Norëdami ávesti duomenis rankiniu bûdu, nurodykite svetainës adresà, savo el.paðtà arba naudotojo vardà, slaptà kodà paveikslëlyje ir pasirinkite pagrindiná tipà.";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_HOTP"] = " ";
$MESS["SEC_OTP_CONNECT_MOBILE_MANUAL_INPUT_TOTP"] = "pagal laikà";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_DESCRIPTION"] = "Kai kodas bus sëkmingai nuskaitytas ar áraðytas rankiniu bûdu, jûsø mobilusis telefonas parodys kodà, kurá turësite ávesti þemiau.";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_CODE"] = "Ávesti kodà";
$MESS["SEC_OTP_CONNECT_MOBILE_INPUT_NEXT_DESCRIPTION"] = "OTP algoritmas reikalauja dviejø kodus autentifikavimui. Praðome generuoti kità kodà ir ávesti já þemiau.";
$MESS["SEC_OTP_CONNECT_MOBILE_ENTER_NEXT_CODE"] = "Ávesti kità kodà";
$MESS["SEC_OTP_CONNECT_DONE"] = "Pasirengæs";
$MESS["SEC_OTP_CONNECT_DEVICE_TITLE"] = "Prijunkite raktà";
$MESS["SEC_OTP_CONNECTED"] = "Prisijungæs ";
$MESS["SEC_OTP_ENABLE"] = "Ájungti";
$MESS["SEC_OTP_DISABLE"] = "Iðjungti ";
$MESS["SEC_OTP_SYNC_NOW"] = "Sinchronizuoti";
$MESS["SEC_OTP_MOBILE_INPUT_METHODS_SEPARATOR"] = "arba";
$MESS["SEC_OTP_MOBILE_SCAN_QR"] = "Skenuoti QR kodà";
$MESS["SEC_OTP_MOBILE_MANUAL_INPUT"] = "Ávesti kodà rankiniu bûdu";
$MESS["SEC_OTP_CONNECT_DEVICE"] = "Prijunkite raktà";
$MESS["SEC_OTP_CONNECT_MOBILE"] = "Prijungti mobilø prietaisà";
$MESS["SEC_OTP_CONNECT_NEW_DEVICE"] = "Prijunkite naujà raktà";
$MESS["SEC_OTP_CONNECT_NEW_MOBILE"] = "Prijungti naujà mobilø prietaisà";
$MESS["SEC_OTP_ERROR_TITLE"] = "Nepavyko iðsaugoti, nes ávyko klaida. ";
$MESS["SEC_OTP_UNKNOWN_ERROR"] = "Netikëta klaida. Praðome pabandyti vëliau.";
$MESS["SEC_OTP_RECOVERY_CODES_BUTTON"] = "Atstatymo kodai";
$MESS["SEC_OTP_RECOVERY_CODES_TITLE"] = "Atstatymo kodai";
$MESS["SEC_OTP_RECOVERY_CODES_DESCRIPTION"] = "Nukopijuokite atkûrimo kodus, kuriø jums gali prireikti, jei jûs neteksite savo mobiliojo prietaiso ar negalësite gauti kodà per aplikacijà dël bet kurios kitos prieþasties.";
$MESS["SEC_OTP_RECOVERY_CODES_WARNING"] = "Laikykite jas patogiai, tarkim, jûsø piniginëje ar rankinëje. Kiekvienas ið kodø gali bûti naudojamas tik vienà kartà.";
$MESS["SEC_OTP_RECOVERY_CODES_PRINT"] = "Spausdinti";
$MESS["SEC_OTP_RECOVERY_CODES_SAVE_FILE"] = "Iðsaugoti á tekstiná failà";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE_DESCRIPTION"] = "Sutrumpinti pagal atkûrimo kodus?<br/>
Kurti naujus.  <br/><br/>
Naujø atkûrimo kodø kûrimas paneigia <br/> anksèiau gautus kodus.";
$MESS["SEC_OTP_RECOVERY_CODES_REGENERATE"] = "Generuoti naujà kodà";
$MESS["SEC_OTP_RECOVERY_CODES_NOTE"] = "Kodas gali bûti naudojamas tik vienà kartà. Patarimas: paðalinti panaudotus kodus ið sàraðo.";
$MESS["SEC_OTP_WARNING_RECOVERY_CODES"] = "Dviejø etapø autentifikavimas yra ájungtas, taèiau jûs negalite sukurti atkûrimo kodø. Jums gali juos prireikti, jei jûs netekote savo mobiliojo prietaiso ar negalite gauti kodo per aplikacijà dël bet kurios kitos prieþasties.";
$MESS["SEC_OTP_NO_DAYS"] = "visada";
$MESS["SEC_OTP_DEACTIVATE_UNTIL"] = "Iðjungta iki #DATE#";
$MESS["SEC_OTP_MANDATORY_EXPIRED"] = "Laikas, per kurá naudotojas turi nustatyti dviejø etapø autentifikavimà, jau pasibaigæs.";
$MESS["SEC_OTP_MANDATORY_ALMOST_EXPIRED"] = "Laikas, per kurá naudotojas turi nustatyti dviejø etapø autentifikavimà, pasibaigs #DATE#.";
$MESS["SEC_OTP_MANDATORY_DISABLED"] = "Privalomas dviejø etapø autentifikavimas iðjungtas.";
$MESS["SEC_OTP_MANDATORY_ENABLE_DEFAULT"] = "Reikalauja dviejø etapø autentifikavimo aktyvavimo";
$MESS["SEC_OTP_MANDATORY_ENABLE"] = "Reikalauja dviejø etapø autentifikavimo aktyvavimo per";
$MESS["SEC_OTP_MANDATORY_DEFFER"] = "Iðplëtimas";
?>