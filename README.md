# tover_gotcha
Website voor het jaarlijkse Gotcha spel, georganiseerd door Tover

TODO 
- ~~Aanmelden/afmelden~~
- Ledengegevens inladen (beernummer + naam).
- ~~Verdelen kaartjes (automatisch?)~~
	- ~~Aanmaken codes~~
	- ~~Verdelen targets~~
	- ~~Resetopties~~
	- ~~Inbouwen in adminpaneel~~
- ~~Adminpaneel~~ (admin:ToverGotchaAdmin)
- Mensen killen aan eind van ronde (via cron). Extra weergave fixen voor als ze dood zijn door de ronde.
- ~~Weergeven of mensen al kills hebben in ronde~~
- Definitief maken van begintijd en ronde momenten (vraag Tover)
	- Dit ook nice weergeven
- Server opzetten (@Jurgen?)
- ~~Hoe kunnen we het beste met tijden werken? Timestamps? Datetimes in GTM+1?~~ -> dateTime
- ~~Verifieren van code en mensen op dood zetten~~
- LoginToSite fixen (@Jurgen)
- Fix uiterlijk van de modals?
- Killfeed?

Mensen loggen in met hun beer (loginToSite), dan komen ze in de auth tabel. Zodra ze zich aanmelden komen ze in de player tabel (als ze zich afmelden gaat is_playing weer op 0). Op een gegeven moment gaan de inschrijvingen dicht, dan worden alle codes gegenereerd en krijgt iedereen z'n code en target te zien. Dan moet iedereen elke week iemand killen (anders lig je uit het spel). Als je de geheime code van de persoon die je killt invult, wordt zijn status op dood gezet, word jouw target_id geüpdated (en die van hem op -1 gezet). Je moet dan dus de persoon killen die jouw oude target moest killen.
