<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/07/10 07:30:34 $
    File Versie         : $Revision: 1.4 $

    $Log: api_portStatics.php,v $
    Revision 1.4  2020/07/10 07:30:34  cvs
    call 8707

    Revision 1.3  2020/07/08 11:20:08  cvs
    call 8707

    Revision 1.2  2019/09/30 08:27:04  cvs
    call 8136

    Revision 1.1  2018/09/26 09:30:07  cvs
    update naar DEMO



*/


global $__dbDebug;
$portefeuille = $__ses["data"]["portefeuille"];

$db = new DB();
$db->debug = $__dbDebug;
$query = "SELECT * FROM `Portefeuilles` WHERE Portefeuille = '$portefeuille'";
$portRec = $db->lookupRecordByQuery($query);

$query = "
SELECT
	CRM_naw.id,
	CRM_naw.zoekveld,
	CRM_naw.IBAN,
	Portefeuilles.Portefeuille,
	Depotbanken.Omschrijving as Depotbank,
	Portefeuilles.Startdatum,
	Portefeuilles.Risicoklasse,
	Portefeuilles.Taal,
	ModelPortefeuilles.Omschrijving AS 'Modelportefeuille',
	Fondsen.Omschrijving AS 'Benchmark',
	Portefeuilles.SoortOvereenkomst 
FROM
	CRM_naw
	LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille = Portefeuilles.Portefeuille
	LEFT JOIN Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
	LEFT JOIN ModelPortefeuilles ON Portefeuilles.ModelPortefeuille = ModelPortefeuilles.Portefeuille
	LEFT JOIN Fondsen ON Portefeuilles.SpecifiekeIndex = Fondsen.Fonds
WHERE 
  Portefeuilles.Portefeuille = '{$portefeuille}'
";

$output = $db->lookupRecordByQuery($query);
//$output["query"] = $query;
$query = "SELECT DATE(Datum) as Datum FROM Valutakoersen WHERE Valuta = 'EUR' ORDER BY Datum DESC";
$o = $db->lookupRecordByQuery($query);
$output["rapportageDatum"] = $o['Datum'];

$portRec["Depotbank"]         = $output["Depotbank"];
$portRec["ModelPortefeuille"] = $output["Modelportefeuille"];
$portRec["SpecifiekeIndex"]   = $output["Benchmark"];

$output["portefeuilleData"] = $portRec;


