<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2015/02/22 10:13:08 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20150222_PREinstall_slv.php,v $
 		Revision 1.1  2015/02/22 10:13:08  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/02/15 10:39:12  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/01/24 20:02:43  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2015/01/04 13:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/12/13 12:34:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/08/09 15:05:04  rvv
 		*** empty log message ***
 		

	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");
$tst = new SQLman();

$velden=array(
array('1', 'PEP', 'Politically Exp Pers', 'Checkbox', '0', 'CGR', '2012-11-02 14:33:21', 'CGR', '2012-11-02 14:22:20', '0', '0', '', '', '', ''),
array('2', 'PUP', 'Publicly Exp Pers', 'Checkbox', '0', 'CGR', '2012-11-02 14:33:49', 'CGR', '2012-11-02 14:33:49', '0', '0', '', '', '', ''),
array('3', 'verzendAdres2', 'Verzendadres deel 2', 'Tekst', '0', 'TNT', '2013-04-16 07:39:32', 'TNT', '2013-04-16 07:39:32', '0', '0', '', '', '', ''),
array('4', 'Adres2', 'Adres deel 2', 'Tekst', '0', 'TNT', '2013-05-12 14:56:04', 'TNT', '2013-05-12 14:56:04', '0', '0', '', '', '', ''),
array('5', 'Personeel', 'Personeel', 'Checkbox', '0', 'TNT', '2013-07-23 15:32:32', 'TNT', '2013-07-23 15:32:32', '1', '0', '', '', '', ''),
array('6', 'VerdienModel', 'Verdien model', 'Trekveld', '0', 'TNT', '2013-10-04 09:50:01', 'TNT', '2013-10-04 09:50:01', '0', '0', '', '', '', ''),
array('7', 'Stockinstructie', 'Stockinstructie', 'Trekveld', '0', 'TNT', '2013-10-04 09:57:42', 'TNT', '2013-10-04 09:57:42', '0', '0', '', '', '', ''),
array('8', 'Bewaarloon', 'Bewaarloon', 'Trekveld', '0', 'TNT', '2013-10-04 09:57:58', 'TNT', '2013-10-04 09:57:58', '0', '0', '', '', '', ''),
array('9', 'debiteur', 'Clienten', 'Checkbox', '0', 'SYS', '2014-02-24 08:32:14', 'SYS', '2014-02-24 08:32:14', '1', '0', '', '', '', ''),
array('10', 'crediteur', 'Leveranciers', 'Checkbox', '0', 'SYS', '2014-02-24 08:32:14', 'SYS', '2014-02-24 08:32:14', '1', '0', '', '', '', ''),
array('11', 'prospect', 'Prospects', 'Checkbox', '0', 'SYS', '2014-02-24 08:32:14', 'SYS', '2014-02-24 08:32:14', '1', '0', '', '', '', ''),
array('12', 'overige', 'Overige', 'Checkbox', '0', 'SYS', '2014-02-24 08:32:14', 'SYS', '2014-02-24 08:32:14', '1', '0', '', '', '', ''),
array('13', 'AfwijkingOvk', 'Afwijkingen in overeenkomst', 'Checkbox', '0', 'TNT', '2014-05-10 12:37:47', 'TNT', '2014-05-10 12:37:47', '0', '0', '', '', '', ''),
array('14', 'AfwijkingOvkToel', 'Toelichting', 'Tekst', '80', 'TNT', '2014-05-10 12:44:48', 'TNT', '2014-05-10 12:38:27', '0', '0', '', '', '', ''),
array('15', 'SoortAanvangsvermogen', 'Soort aanvangsvermogen', 'Trekveld', '0', 'TNT', '2014-08-25 14:20:40', 'TNT', '2014-08-25 14:20:40', '0', '0', 'a:0:{}', '', '', ''),
array('16', 'VermogenViaSLV', 'Met hoeveel procent van uw vermogen wenst u via Stroeve & Lemberger te beleggen?', 'Trekveld', '0', 'TNT', '2014-08-25 16:30:18', 'TNT', '2014-08-25 16:30:18', '0', '0', 'a:0:{}', '', '', ''),
array('17', 'InkomenVersusLasten', 'Hoe voorziet uw huidige inkomen voor vaste lasten en levensonderhoud?', 'Trekveld', '0', 'TNT', '2014-08-25 16:32:32', 'TNT', '2014-08-25 16:32:32', '0', '0', 'a:0:{}', '', '', ''),
array('18', 'InkomenVersusLastenTekst', 'Toelichting bij inkomensoverschot', 'Tekst', '0', 'TNT', '2014-08-25 16:35:07', 'TNT', '2014-08-25 16:35:07', '0', '0', 'a:0:{}', '', '', ''),
array('19', 'Inkomensverwachting', 'Welke van deze uitspraken omschrijven het beste uw inkomensverwachting voor de komende 5 jaar?', 'Trekveld', '0', 'TNT', '2014-08-25 16:36:03', 'TNT', '2014-08-25 16:36:03', '0', '0', 'a:0:{}', '', '', ''),
array('20', 'OnttrekkenVanVermogen', 'Bent u van plan substantieel te onttrekken uit uw portefeuille?', 'Trekveld', '0', 'TNT', '2014-08-25 16:39:53', 'TNT', '2014-08-25 16:39:53', '0', '0', 'a:0:{}', '', '', ''),
array('21', 'OnttrekkenVanVermogenTekst', 'Toelichting bij onttrekkingen', 'Tekst', '0', 'TNT', '2014-08-25 16:41:25', 'TNT', '2014-08-25 16:41:25', '0', '0', 'a:0:{}', '', '', ''),
array('22', 'UitbreidenVanVermogen', 'Bent u van plan uw vrij belegbaar vermogen uit te breiden?', 'Trekveld', '0', 'TNT', '2014-08-25 16:42:19', 'TNT', '2014-08-25 16:42:19', '0', '0', 'a:0:{}', 'OnttrekkenVanVermogen', '', ''),
array('23', 'UitbreidenVanVermogenTekst', 'Toelichting bij uitbreiding vermogen', 'Tekst', '0', 'TNT', '2014-08-25 16:42:51', 'TNT', '2014-08-25 16:42:51', '0', '0', 'a:0:{}', '', '', ''),
array('24', 'FinancieleDoel1', 'Wat is uw belangrijkste financiële doel?', 'Trekveld', '0', 'TNT', '2014-08-25 16:43:57', 'TNT', '2014-08-25 16:43:57', '0', '0', 'a:0:{}', '', '', ''),
array('25', 'FinancieleDoel2', 'Wat is uw één na belangrijkste financiële doel?', 'Trekveld', '0', 'TNT', '2014-08-25 16:47:43', 'TNT', '2014-08-25 16:47:43', '0', '0', 'a:0:{}', 'FinancieleDoel1', '', ''),
array('26', 'AfhankelijkPortefeuille', 'Hoe afhankelijk bent u in financieel opzicht van het behalen van de doelen met de beleggingsportefeuille die wordt ondergebracht?', 'Trekveld', '0', 'TNT', '2014-08-25 16:49:23', 'TNT', '2014-08-25 16:49:23', '0', '0', 'a:0:{}', '', '', ''),
array('27', 'BeleggenGeleendGeld', 'Maakt u bij het beleggen bij Stroeve & Lemberger gebruik van geleend geld?', 'Trekveld', '0', 'TNT', '2014-08-25 16:51:24', 'TNT', '2014-08-25 16:51:24', '0', '0', 'a:0:{}', '', '', ''),
array('28', 'BeleggenGeleendGeldTekst', 'Toelichting beleggen geleend geld', 'Tekst', '0', 'TNT', '2014-08-25 16:51:49', 'TNT', '2014-08-25 16:51:49', '0', '0', 'a:0:{}', '', '', ''),
array('29', 'WijzigingSituatie', 'Verwacht u binnen enkele jaren wijzigingen in uw financiële situatie?', 'Trekveld', '0', 'TNT', '2014-08-25 16:53:47', 'TNT', '2014-08-25 16:53:47', '0', '0', 'a:0:{}', '', '', ''),
array('30', 'WijzigingSituatieGezin', 'Verwacht u wijzigingen in uw gezinssamenstelling?', 'Trekveld', '0', 'TNT', '2014-08-25 16:54:57', 'TNT', '2014-08-25 16:54:57', '0', '0', 'a:0:{}', '', '', ''),
array('31', 'WijzigingSituatieInkomen', 'Verwacht u veranderingen in uw inkomsten (1e rekeninghouder)?', 'Trekveld', '0', 'TNT', '2014-08-25 16:55:32', 'TNT', '2014-08-25 16:55:32', '0', '0', 'a:0:{}', '', '', ''),
array('32', 'WijzigingSituatieInkomenPart', 'Verwacht u veranderingen in uw inkomsten (partner)?', 'Trekveld', '0', 'TNT', '2014-08-25 16:58:53', 'TNT', '2014-08-25 16:56:35', '0', '0', 'a:0:{}', 'WijzigingSituatieInkomen', '', ''),
array('33', 'WijzigingSituatieInkomenToel', 'Toelichting bij wijziging situatie inkomen', 'Tekst', '0', 'TNT', '2014-08-25 16:59:26', 'TNT', '2014-08-25 16:59:26', '0', '0', 'a:0:{}', '', '', ''),
array('34', 'WijzigingSituatieInkomenPartToel', 'Toelichting bij wijziging situatie inkomen partner', 'Tekst', '0', 'TNT', '2014-08-25 17:00:02', 'TNT', '2014-08-25 17:00:02', '0', '0', 'a:0:{}', '', '', ''),
array('35', 'Ouderdomspensioenopbouw', 'Hoe is uw ouderdomspensioen geregeld?', 'Trekveld', '0', 'TNT', '2014-08-25 17:00:52', 'TNT', '2014-08-25 17:00:52', '0', '0', 'a:0:{}', '', '', ''),
array('36', 'OuderdomspensioenopbouwToel', 'Toelichting bij opbouw pensioen', 'Tekst', '0', 'TNT', '2014-08-25 17:02:30', 'TNT', '2014-08-25 17:02:30', '0', '0', 'a:0:{}', '', '', ''),
array('37', 'OuderdomspensioenVoldoende', 'Is uw ouderdomspensioen voldoende om te voorzien in uw levensonderhoud?', 'Trekveld', '0', 'TNT', '2014-08-26 10:52:10', 'TNT', '2014-08-26 10:52:10', '0', '0', 'a:0:{}', '', '', ''),
array('38', 'Pensioentekort', 'Moeten de beleggingen bij S&L rekening houden met uw pensioentekort?', 'Trekveld', '0', 'TNT', '2014-08-26 10:53:41', 'TNT', '2014-08-26 10:53:41', '0', '0', 'a:0:{}', '', '', ''),
array('39', 'PensioentekortBedrag', 'Indien ja, wat heeft u jaarlijks extra nodig en vanaf welke datum', 'Tekst', '0', 'TNT', '2014-08-26 10:54:42', 'TNT', '2014-08-26 10:54:42', '0', '0', 'a:0:{}', '', '', ''),
array('40', 'MaatregelenOverlijden', 'Heeft u maatregelen getroffen voor nabestaanden bij vroegtijdig overlijden van u of uw partner?', 'Trekveld', '0', 'TNT', '2014-08-26 11:49:47', 'TNT', '2014-08-26 11:03:13', '0', '0', 'a:0:{}', 'MaatregelenArbOng', '', ''),
array('41', 'MaatregelenOverlijdenToel', 'Toelichting bij maatregelen bij vroegtijdig overlijden', 'Tekst', '0', 'TNT', '2014-08-26 11:19:41', 'TNT', '2014-08-26 11:19:41', '0', '0', 'a:0:{}', '', '', ''),
array('42', 'MaatregelenArbOng', 'Heeft u maatregelen getroffen indien u of uw partner arbeidsongeschikt zouden worden?', 'Trekveld', '0', 'TNT', '2014-08-26 11:20:37', 'TNT', '2014-08-26 11:20:37', '0', '0', 'a:0:{}', '', '', ''),
array('43', 'MaatregelenArbOngToel', 'Toelichting bij maatregelen bij arbeidsongeschiktheid', 'Tekst', '0', 'TNT', '2014-08-26 11:21:00', 'TNT', '2014-08-26 11:21:00', '0', '0', 'a:0:{}', '', '', ''),
array('44', 'BelangBeursgenoteerd', 'Bezit u of uw partner een (in)direct belang van meer dan 1% bij een beursgenoteerde onderneming?', 'Trekveld', '0', 'TNT', '2014-08-26 11:39:20', 'TNT', '2014-08-26 11:39:20', '0', '0', 'a:0:{}', '', '', ''),
array('45', 'BelangBeursgenoteerdToel', 'Toelichting beursgenoteerde belangen', 'Tekst', '0', 'TNT', '2014-08-26 11:39:53', 'TNT', '2014-08-26 11:39:53', '0', '0', 'a:0:{}', '', '', ''),
array('46', 'part_opleidingsniveau', 'Opleidingsniveau partner', 'Trekveld', '0', 'TNT', '2014-09-07 15:52:14', 'TNT', '2014-09-07 15:52:14', '0', '0', 'a:0:{}', 'opleidingsniveau', '', ''),
array('47', 'verplichtingenOverig', 'Overige verplichtingen', 'Getal', '0', 'TNT', '2014-09-07 16:12:39', 'TNT', '2014-09-07 16:12:39', '0', '0', 'a:0:{}', '', '', ''),
array('48', 'clientprofielOverigeInfo', 'Overige informatie welke van invloed is op het vast te stellen cliëntenprofiel', 'Memo', '0', 'TNT', '2014-09-07 16:15:20', 'TNT', '2014-09-07 16:15:20', '0', '0', 'a:0:{}', '', '', ''),
array('49', 'Vraag1Risico', '1e additionele vraag over beleggen en risico', 'Trekveld', '0', 'TNT', '2014-09-07 16:53:18', 'TNT', '2014-09-07 16:53:18', '0', '0', 'a:0:{}', '', '', ''),
array('50', 'Vraag2Risico', '2e additionele vraag over beleggen en risico', 'Trekveld', '0', 'TNT', '2014-09-07 16:53:37', 'TNT', '2014-09-07 16:53:37', '0', '0', 'a:0:{}', '', '', ''),
array('51', 'Vraag3Risico', '3e additionele vraag over beleggen en risico', 'Trekveld', '0', 'TNT', '2014-09-07 16:54:00', 'TNT', '2014-09-07 16:54:00', '0', '0', 'a:0:{}', '', '', ''),
array('52', 'risicoprofielVragenlijst', 'Risicoprofiel op basis van vragenlijst', 'Trekveld', '0', 'TNT', '2014-09-15 07:22:04', 'TNT', '2014-09-15 07:22:04', '0', '0', 'a:0:{}', 'risicoprofiel', '', ''),
array('53', 'risicoprofielScenario', 'Risicoprofiel op basis van scenario-analyse', 'Trekveld', '0', 'TNT', '2014-09-15 07:22:32', 'TNT', '2014-09-15 07:22:32', '0', '0', 'a:0:{}', 'risicoprofiel', '', ''),
array('54', 'risicoprofielControlevragen', 'Risicoprofiel op basis van controlevragen', 'Trekveld', '0', 'TNT', '2014-09-15 07:23:02', 'TNT', '2014-09-15 07:23:02', '0', '0', 'a:0:{}', 'risicoprofiel', '', ''),
array('55', 'risicoprofielObvGesprek', 'Risicoprofiel op basis van gesprek', 'Trekveld', '0', 'TNT', '2014-09-15 07:24:06', 'TNT', '2014-09-15 07:24:06', '0', '0', 'a:0:{}', 'risicoprofiel', '', ''),
array('56', 'risicoprofielAndersDanAdvies', 'Afwijkend risicoprofiel van S&L Advies', 'Trekveld', '0', 'TNT', '2014-09-15 07:25:11', 'TNT', '2014-09-15 07:25:11', '0', '0', 'a:0:{}', 'risicoprofiel', '', ''),
array('57', 'risicoprofielDilemma', 'Waren er in het gesprek dilemma s of conflicterende doelen?', 'Trekveld', '0', 'CGR', '2014-09-15 14:18:13', 'TNT', '2014-09-15 07:31:05', '0', '0', 'a:0:{}', '', '', ''),
array('58', 'risicoprofielDilemmaToel', 'Toelichting bij dilemma\'s risicoprofiel', 'Memo', '0', 'TNT', '2014-09-15 07:33:25', 'TNT', '2014-09-15 07:33:25', '0', '0', 'a:0:{}', '', '', ''),
array('59', 'profielBeleggingsfondsen', 'Beleggingsfondsen', 'Trekveld', '0', 'CGR', '2014-09-15 14:24:54', 'CGR', '2014-09-15 14:24:54', '0', '0', 'a:0:{}', '', '', ''),
array('60', 'profielStucProd', 'Structured Products', 'Trekveld', '0', 'CGR', '2014-09-15 14:25:25', 'CGR', '2014-09-15 14:25:25', '0', '0', 'a:0:{}', '', '', ''),
array('61', 'FinancieleDoel1Belangrijk', 'Hoe belangrijk is het behalen van uw 1e doel', 'Trekveld', '0', 'TNT', '2014-10-28 15:27:54', 'TNT', '2014-10-28 15:27:54', '0', '0', 'a:0:{}', '', '', ''),
array('62', 'FinancieleDoel2Belangrijk', 'Hoe belangrijk is het behalen van uw 2e doel', 'Trekveld', '0', 'TNT', '2014-10-28 15:29:41', 'TNT', '2014-10-28 15:29:41', '0', '0', 'a:0:{}', 'FinancieleDoel1Belangrijk', '', ''),
array('63', 'part_vermogenTotaalBelegbaar', 'Belegbaar vermogen partner', 'Getal', '0', 'TNT', '2014-11-05 13:21:08', 'TNT', '2014-11-05 13:21:08', '0', '0', 'a:0:{}', '', '', ''),
array('64', 'verplichtingenPensioen', 'Pensioen', 'Getal', '0', 'TNT', '2014-11-05 13:22:51', 'TNT', '2014-11-05 13:22:51', '0', '0', 'a:0:{}', '', '', ''),
array('65', 'WijzigingSituatieUitgaven', 'Er moeten grote uitgaven gedaan worden', 'Trekveld', '0', 'TNT', '2014-11-05 13:50:52', 'TNT', '2014-11-05 13:50:52', '0', '0', 'a:0:{}', '', '', ''),
array('66', 'WijzigingSituatieInkomsten', 'De ontvangst van een groot bedrag', 'Tekst', '0', 'TNT', '2014-11-05 13:51:24', 'TNT', '2014-11-05 13:51:24', '0', '0', 'a:0:{}', '', '', ''),
array('67', 'WijzigingSituatieSchenken', 'Schenken aan', 'Trekveld', '0', 'TNT', '2014-11-05 13:52:02', 'TNT', '2014-11-05 13:52:02', '0', '0', 'a:0:{}', '', '', ''),
array('68', 'WijzigingSituatieVerkoop', 'Verkoop van bezittingen', 'Tekst', '0', 'TNT', '2014-11-05 13:52:26', 'TNT', '2014-11-05 13:52:26', '0', '0', 'a:0:{}', '', '', ''),
array('69', 'WijzigingSituatieGezinFin', 'Financiële consequentie', 'Getal', '0', 'TNT', '2014-11-05 14:13:14', 'TNT', '2014-11-05 14:13:14', '0', '0', 'a:0:{}', '', '', ''),
array('70', 'WijzigingSituatieInkomenFin', 'Financiële consequentie', 'Getal', '0', 'TNT', '2014-11-05 14:13:49', 'TNT', '2014-11-05 14:13:49', '0', '0', 'a:0:{}', '', '', ''),
array('71', 'WijzigingSituatieInkomenPartFin', 'Financiële consequentie', 'Getal', '0', 'TNT', '2014-11-05 14:14:03', 'TNT', '2014-11-05 14:14:03', '0', '0', 'a:0:{}', '', '', ''),
array('72', 'WijzigingSituatieUitgavenFin', 'Financiële consequentie', 'Getal', '0', 'TNT', '2014-11-05 14:14:44', 'TNT', '2014-11-05 14:14:44', '0', '0', 'a:0:{}', '', '', ''),
array('73', 'WijzigingSituatieInkomstenFin', 'Financiële consequentie', 'Getal', '0', 'TNT', '2014-11-05 14:15:13', 'TNT', '2014-11-05 14:15:13', '0', '0', 'a:0:{}', '', '', ''),
array('74', 'WijzigingSituatieSchenkenFin', 'Financiële consequentie', 'Getal', '0', 'TNT', '2014-11-05 14:15:31', 'TNT', '2014-11-05 14:15:31', '0', '0', 'a:0:{}', '', '', ''),
array('75', 'WijzigingSituatieVerkoopFin', 'Financiële consequentie', 'Getal', '0', 'TNT', '2014-11-05 14:30:32', 'TNT', '2014-11-05 14:30:32', '0', '0', 'a:0:{}', '', '', ''),
array('76', 'PRDConclusie', 'Conclusie uit PRD', 'Memo', '0', 'TNT', '2014-12-17 11:54:38', 'TNT', '2014-12-17 11:54:38', '0', '0', 'a:0:{}', '', '', ''),
array('77', 'Ouderdomspensioenopbouw2', 'Hoe is uw ouderdomspensioen geregeld?', 'Trekveld', '0', 'TNT', '2014-12-17 12:17:28', 'TNT', '2014-12-17 12:17:28', '0', '0', 'a:0:{}', 'Ouderdomspensioenopbouw', '', ''),
array('78', 'Ouderdomspensioenopbouw3', 'Hoe is uw ouderdomspensioen geregeld?', 'Trekveld', '0', 'TNT', '2014-12-17 12:17:42', 'TNT', '2014-12-17 12:17:42', '0', '0', 'a:0:{}', 'Ouderdomspensioenopbouw', '', ''),
array('79', 'Ouderdomspensioenopbouw4', 'Hoe is uw ouderdomspensioen geregeld?', 'Trekveld', '0', 'TNT', '2014-12-17 12:17:56', 'TNT', '2014-12-17 12:17:56', '0', '0', 'a:0:{}', 'Ouderdomspensioenopbouw', '', ''),
array('80', 'verplichtingenTotaal', 'Totaal van de verplichtingen', 'Getal', '0', 'TNT', '2015-01-07 14:31:04', 'TNT', '2015-01-07 14:31:04', '0', '0', 'a:0:{}', '', '', ''),
array('81', 'ZInkomenVersusLasten', 'Hoe is uw privé inkomenssituatie geregeld?', 'Trekveld', '0', 'TNT', '2015-02-02 11:33:53', 'TNT', '2015-02-02 11:33:53', '0', '0', 'a:0:{}', '', '', ''),
array('82', 'ZInvesteringsbehoefte', 'Is een deel van het bij Stroeve & Lemberger te beleggen vermogen in de toekomst nodig in uw bedrijf voor investeringen', 'Trekveld', '0', 'TNT', '2015-02-02 11:38:19', 'TNT', '2015-02-02 11:38:19', '0', '0', 'a:0:{}', '', '', ''),
array('83', 'ZInvesteringsbehoefteTekst', 'Toelichting bij investeringsbehoefte', 'Tekst', '0', 'TNT', '2015-02-02 11:39:34', 'TNT', '2015-02-02 11:39:34', '0', '0', 'a:0:{}', '', '', ''),
array('84', 'ZWaardePensioenverplichting', 'Wat is de fiscale en commerciële waardering van de pensioenverplichting? Welke activa staan hier tegenover', 'Memo', '0', 'TNT', '2015-02-02 11:41:50', 'TNT', '2015-02-02 11:41:50', '0', '0', 'a:0:{}', '', '', ''),
array('85', 'ZAanvullendeinfo', 'Zijn er andere zaken waar S&L bij het beleggen van het vermogen in de entiteit rekening mee moet houden?', 'Trekveld', '0', 'TNT', '2015-02-02 11:43:24', 'TNT', '2015-02-02 11:43:24', '0', '0', 'a:0:{}', '', '', ''),
array('86', 'ZAanvullendeinfoTekst', 'Toelichting bij aanvullende informatie beleggen', 'Tekst', '0', 'TNT', '2015-02-02 11:44:33', 'TNT', '2015-02-02 11:44:33', '0', '0', 'a:0:{}', '', '', ''),
array('87', 'ZPensioentekortBedrag', 'Hoe groot bedraagt uw jaarlijkse pensioentekort?', 'Getal', '0', 'TNT', '2015-02-02 11:57:48', 'TNT', '2015-02-02 11:57:48', '0', '0', 'a:0:{}', '', '', ''),
array('88', 'ZPensioentekortDatum', 'Vanaf welke datum treed uw pensioentekort op?', 'Datum', '0', 'TNT', '2015-02-02 11:58:11', 'TNT', '2015-02-02 11:58:11', '0', '0', 'a:0:{}', '', '', ''),
array('89', 'DoelstellingOnderneming', 'Doelstelling entiteit', 'Tekst', '0', 'TNT', '2015-02-10 12:20:25', 'TNT', '2015-02-02 16:03:41', '0', '0', 'a:0:{}', '', '', ''),
array('90', 'BrancheOnderneming', 'Branche entiteit', 'Tekst', '0', 'TNT', '2015-02-10 12:20:16', 'TNT', '2015-02-02 16:04:19', '0', '0', 'a:0:{}', '', '', ''),
array('91', 'JaaromzetOnderneming', 'Jaaromzet entiteit', 'Getal', '0', 'TNT', '2015-02-10 12:20:08', 'TNT', '2015-02-02 16:04:36', '0', '0', 'a:0:{}', '', '', ''),
array('92', 'EigenVermogenOnderneming', 'Eigen vermogen van de entiteit', 'Getal', '0', 'TNT', '2015-02-10 12:19:59', 'TNT', '2015-02-10 12:19:59', '0', '0', 'a:0:{}', '', '', ''),
array('93', 'JaaromzetOndernemingTekst', 'Toelichting bij jaaromzet', 'Memo', '0', 'TNT', '2015-02-10 12:23:55', 'TNT', '2015-02-10 12:23:55', '0', '0', 'a:0:{}', '', '', ''),
array('94', 'EigenVermogenOndernemingTekst', 'Toelichting eigen vermogen', 'Memo', '0', 'TNT', '2015-02-10 12:24:27', 'TNT', '2015-02-10 12:24:27', '0', '0', 'a:0:{}', '', '', ''),
array('95', 'ScenarioTotstandkoming', 'Totstandkoming Scenarioanalyse', 'Memo', '0', 'TNT', '2015-02-18 12:55:54', 'TNT', '2015-02-18 12:55:54', '0', '0', 'a:0:{}', '', '', '')
);

$crmNawVelden=array();
$veldTypen=array('Tekst'=>'varchar(255)','Memo'=>'text','Getal'=>'double','Datum'=>'date','Trekveld'=>'varchar(100)','Checkbox'=>'tinyint(4)');
$CRMeigenVelden=array(1=>'veldnaam',2=>'omschrijving',3=>'veldtype',4=>'weergaveBreedte',5=>'change_user',
7=>'add_user',8=>'add_date',9=>'relatieSoort',10=>'headerBreedte',11=>'extraVeldData',
12=>'trekveldSelectieveld',13=>'uitlijning',14=>'getalformat');
$db=new DB();
$query="DESC CRM_naw";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  $crmNawVelden[]=strtoupper($data['Field']);
}

foreach($velden as $veldData)
{
   if(!in_array(strtoupper($veldData[1]),$crmNawVelden))
   {
     $query="SELECT id FROM CRM_eigenVelden WHERE veldnaam='".$veldData[1]."'";
     if($db->QRecords($query) < 1)
     { 
       $tst->changeField("CRM_naw",$veldData[1],array("Type"=>$veldTypen[$veldData[3]],"Null"=>false));
       $query="INSERT INTO CRM_eigenVelden SET change_date=now() ";
       foreach($CRMeigenVelden as $index=>$veldnaam)
	       if(isset($veldData[$index]))
           $query.=", $veldnaam = '".mysql_real_escape_string($veldData[$index])."'";
       $db->SQL($query);
       $db->Query();
     }
     else
       echo "Veld ".$veldData[1]." aanwezig in CRM_eigenVelden<br>\n";
  
   }
   else
     echo "Veld ".$veldData[1]." aanwezig in CRM_naw<br>\n";
  
}





?>