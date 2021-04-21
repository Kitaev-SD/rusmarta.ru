<?
$MESS["VIRUS_DETECTED_NAME"] = "Aptiktas virusas";
$MESS["VIRUS_DETECTED_DESC"] = "#EMAIL# - Svetainës administratoriaus e-paðto adresas (ið branduolio modulio nustatymø)";
$MESS["VIRUS_DETECTED_SUBJECT"] = "#SITE_NAME#: Aptiktas virusas";
$MESS["VIRUS_DETECTED_MESSAGE"] = "Informacinis praneðimas ið #SITE_NAME# 

------------------------------------------

Jûs gavote ðá praneðimà kaip potencialiai pavojingo kodo aptikimo rezultatà ið proaktyvios #SERVER_NAME#  apsaugos sistemos.

1.  Potencialiai pavojingas kodas buvo iðkirptas ið html. 
2.  Patikrinkite ávykiø þurnalà ir ásitikinkite, kad kodas yra ið tiesø þalingas, ir tai ne vien skaitiklis arba struktûra.
	(nuoroda: http://#SERVER_NAME#/bitrix/admin/event_log.php?lang=en&set_filter=Y&find_type=audit_type_id&find_audit_type[]=SECURITY_VIRUS )
3.  Jei kodas yra nekenksmingas, átraukite já á \"Iðimtèiø\" sàraðà antivirusos nustatymø puslapyje. 
	(nuoroda: http://#SERVER_NAME#/bitrix/admin/security_antivirus.php?lang=en&tabControl_active_tab=exceptions )
4.  Jei kodas yra virusas, tada atlikite ðiuos þingsnius:

	a) Pakeiskite administratoriaus ir kitø atsakingø vartotojø prisijungimo slaptaþodá svetainëje.
	b) Pakeiskite prisijungimo slaptaþodá ssh ir ftp.
	c) Iðtirkite ir paðalinkite virusus ið administratoriø, kurie turi prieigà prie svetainës per ssh ar ftp, kompiuteriø.
	d) Iðjunkite slaptaþodþiø saugojimo programas, kurios suteikia prieigà prie svetainës per ssh ar ftp. 
	e) Paðalinkite kenksmingà kodà ið uþkrëstø failø. Pavyzdþiui, ið naujo ádiekite uþkrëstus failus naudojant naujausià atsarginæ kopijà.

---------------------------------------------------------------------
Ðis praneðimas buvo sugeneruotas automatiðkai.";
?>