<?
$MESS["MCACHE_TITLE"] = "Podëlio nustatymai";
$MESS["MAIN_TAB_3"] = "Paðalinti podëlio failus";
$MESS["MAIN_TAB_4"] = "Komponentø padëjimas";
$MESS["MAIN_OPTION_CLEAR_CACHE"] = "Paðalinti podëlio failus";
$MESS["MAIN_OPTION_PUBL"] = "Komponentø podëlio nustatymai";
$MESS["MAIN_OPTION_CLEAR_CACHE_OLD"] = "Tik pasenusius";
$MESS["MAIN_OPTION_CLEAR_CACHE_ALL"] = "Visus";
$MESS["MAIN_OPTION_CLEAR_CACHE_MENU"] = "Meniu";
$MESS["MAIN_OPTION_CLEAR_CACHE_MANAGED"] = "Visà valdomà podëlá";
$MESS["MAIN_OPTION_CLEAR_CACHE_STATIC"] = "Visus puslapius HTML podëlyje";
$MESS["MAIN_OPTION_CLEAR_CACHE_CLEAR"] = "Iðvalyti";
$MESS["MAIN_OPTION_CACHE_ON"] = "Komponentø podëlis yra ájungtas pagal nutylëjimà";
$MESS["MAIN_OPTION_CACHE_OFF"] = "Komponentø podëlis yra iðjungtas pagal numatymà";
$MESS["MAIN_OPTION_CACHE_BUTTON_OFF"] = "Iðjungti padëjimà";
$MESS["MAIN_OPTION_CACHE_BUTTON_ON"] = "Ájungti padëjimà";
$MESS["cache_admin_note4"] = "<p>HTML podëliavimas rekomenduojamas svetainës skyriams, kurie yra keièiami retai ir daþniausiai lankomi anoniminiø vartotojø. Kai HTML podëlis ájungta, vyksta ðie procesai: </p>
<ul style=\"font-size:100%\">
<li>HTML talpyklos procesai tik iðvardyti átraukimo kaukës ir neiðvardyti atskirties kaukës;</li>
<li>Dël neautorizuotø lankytojø, sistemos patikrinimas dël puslapio kopijos saugomas HTML podëlyje. Jei puslapis yra rastas podëlyje, jis rodomas be sisteminiø moduliø. Statistika nestebës lankytojø. Skelbimai, branduolis ir kiti moduliai taip pat nebus átraukti;</li>
<li>Puslapiai bus siunèiami suspausti, jei Suspaudimo modulis ádiegtas á podëlá jo generavimo metu;</li>
<li>Jei puslapio podëlio nerasta, ji yra apdorojamas áprastu bûdu. Baigus puslapio ákëlimà, puslapio kopija bus iðsaugota HTML podëlyje;</li>
</ul>
<p>Podëlio valymas:</p>
<ul style=\"font-size:100%\">
<li>Jei duomenø áraðymo metu virðyjama disko kvota, podëlis visiðkai iðkraunamas;</li>
<li>Pilnas podëlio iðkrovimas taip pat atliekamas, jei kokie nors duomenys pasikeitë per Valdymo skydelá;</li>
<li>Jei duomenys yra patalpinami ið vieðøjø svetainës puslapiø  (pvz. pridedant pastabas ar balsus), tada iðkraaunamos tik dalys, susijusios su podëliu;</li>
</ul>
<p>Atkreipkite dëmesá, kad kai neautorizuoti naudotojai aplanko nepodëliuojamose svetainës puslapiuose, sesijos bus pradëta ir HTML-podëlis nebebus aktyvus.</p>
<p>Svarbios pastabos:</p>
<ul style=\"font-size:100%\">
<li>Statistika nëra stebima;</li>
<li>Skelbimø modulis veiks tik HTML podëlio sulurimo metu. Atkreipkite dëmesá, kad tai neturi átakos iðoriniø moduliø skelbimams (Google Ad Sense ir tt);</li>
<li>Lyginamø objektø rezultatai nebus iðsaugoti neautorizuotiems naudotojams (sesija turëtø bûti pradëta);</li>
<li>Disko kvota turi bûti nustatyta siekiant iðvengti DOS atakas á diskà;</li>
<li>Visø svetainës skyriø funkcionalumas turi bûti patikrintas, ájungus HTML podëlá (pvz. Blog'o Komentarai neveiks su senais blogø ðablonais ir tt);</li>
</ul>";
$MESS["MAIN_OPTION_CACHE_OK"] = "Laikinosios atminties failai iðvalyti";
$MESS["MAIN_OPTION_CACHE_SUCCESS"] = "Komponentø padëjimo tipas sëkmingai perjungtas";
$MESS["MAIN_OPTION_CACHE_ERROR"] = "Komponentø padëjimo tipas jau nustatytas pagal ðià reikðmæ";
$MESS["cache_admin_note1"] = "<p>Auto podëlio reþimo naudojimas nuostabiai pagreitina jûsø svetainæ!</p>
<p>Auto podëlio komponentø reþimas atnaujina informacijà pagal tø komponentø parametrus.</p>
<p>Jei norite atnaujinti iðsaugotus objektus puslapyje, jûs galite:</p>
<p>1. Atidarykite reikiamà puslapá ir atnaujinkite savo objektus, paspaudæ specialø duomenø atnaujinimo mygtukà administracinioje  árankiø juostoje.</p>
<img src=\"/bitrix/images/main/page_cache_en.png\" vspace=\"5\" />
<p>2. Svetainës redagavimo reþime  jûs galite spustelëti podëlio iðvalymo mygtukà pasirinktas komponentui. </p>
<img src=\"/bitrix/images/main/comp_cache_en.png\" vspace=\"5\" />
<p>3. Eikite á komponento parametrus ir perjunkite reikalingus komponentus á neiðsaugojimo podëlyje reþimà.</p>
<img src=\"/bitrix/images/main/spisok_en.png\" vspace=\"5\" />
<p>Ájungus iðsaugojimo podëlyje reþimà (pagal nutylëjimà) visi komponentai su podëlio nustatymu <i>\"Auto\"</i> bus perjungti darbui su podëliu.</p>
<p>Komponentai su podëlio nustatymu <i>\"Podëlis\"</i> ir su nustatytu laiku didesniu nei 0 (nulis) visada veiks podëlio reþime.</p>
<p>Komponentai su podëlio nustatymu <i>\"Nenaudoti podëlio\"</i> arba su podëlio laiku lygiø 0 (nulis), visada veiks ne podëlio rëþimu.</p>";
$MESS["cache_admin_note2"] = "Paðalinus podëlio failus, visas rodomas turinys bus atnaujintas atsiþvelgiant á naujus duomenis.
Nauji podëlio failai bus sukurti palaipsniui pagal uþklausimus á tuos puslapius (su podëlio sritimis).";
$MESS["main_cache_managed_saved"] = "Valdomo podëlio parametrai buvo iðsaugoti.";
$MESS["main_cache_managed"] = "Valdomas podëlis";
$MESS["main_cache_managed_sett"] = "Valdomo podëlio parametrai";
$MESS["main_cache_managed_on"] = "Valdomas podëlis yra ájungtas.";
$MESS["main_cache_managed_off"] = "Valdomas podëlis yra iðjungtas (nerekomenduojama).";
$MESS["main_cache_managed_turn_off"] = "Áðjungti valdomà podëlá (nerekomenduojama).";
$MESS["main_cache_managed_const"] = "BX_COMP_MANAGED_CACHE konstanta nustatyta. Valdomas podëlis pastoviai ájungtas.";
$MESS["main_cache_managed_turn_on"] = "Ájungti valdomà podëlá";
$MESS["main_cache_managed_note"] = "Podëlio valdymo technologija <b>Cache Dependencies</b> atnaujina podëlá kas kartà kai vyksta duomenø pakeitimai. Jei ði funkcija ájungta, jums nereikës atnaujinti podëlio rankiniu bûdu, atnaujinant naujienas ar produktus: svetainës lankytojai visada matys aktualià atnaujintà informacijà.

<br><br>Gaukite daugiau informacijos apie podëlio technologijas Bitrix svetainëje.
<br><br><span style=\"color:grey\">Pastaba: ne visi komponentai gali palaikyti ðià funkcijà.</span>";
$MESS["cache_admin_note5"] = "HTML podëlis visada ájungtas ðioje laidoje.";
$MESS["main_cache_wrong_cache_type"] = "Neteisingas podëlio tipas.";
$MESS["main_cache_wrong_cache_path"] = "Neteisingas podëlio failo kelias.";
$MESS["main_cache_in_progress"] = "Ðalinami podëlio failai.";
$MESS["main_cache_finished"] = "Podëlio failai yra paðalinti.";
$MESS["main_cache_files_scanned_count"] = "Apdorota:  #value#";
$MESS["main_cache_files_scanned_size"] = "Apdorotø failø dydis: #value#";
$MESS["main_cache_files_deleted_count"] = "Paðalinta:  #value#";
$MESS["main_cache_files_deleted_size"] = "Paðalintø failø dydis: #value#";
$MESS["main_cache_files_delete_errors"] = "Ðalinimo klaidos: #value#";
$MESS["main_cache_files_last_path"] = "Dabartinis aplankas: #value#";
$MESS["main_cache_files_start"] = "Pradëti";
$MESS["main_cache_files_continue"] = "Tæsti";
$MESS["main_cache_files_stop"] = "Nutraukti";
?>