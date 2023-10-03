<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/23 17:14:23 $
    File Versie         : $Revision: 1.1 $

    $Log: reconBewaardersPerVb.php,v $
    Revision 1.1  2018/09/23 17:14:23  cvs
    call 7175



*/

include_once("wwwvars.php");
session_start();
$vb = $_GET["vb"];
$db = new DB();
$query = "
SELECT
mt.portefeuille,
mt.bewaarddoor,	
mt.DepotPort,
mt.DepotRekening,
mt.fonds, -- fonds
mt.isincode,
mt.valuta,
mt.totaalaantal, -- totaalaantal
mt.totaalsaldo
FROM
	(
SELECT
	Portefeuilles.Portefeuille,
	Portefeuilles.Vermogensbeheerder,
	Portefeuilles.Depotbank AS 'DepotPort',
	Rekeningen.Depotbank AS 'DepotRekening',
	Rekeningmutaties.Bewaarder,
  CASE
	  WHEN Rekeningmutaties.Bewaarder <> '' THEN
	    Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank 
	END AS 'BewaardDoor',
	Rekeningen.Rekening,
	Rekeningmutaties.Fonds,
	Fondsen.ISINcode,
	Fondsen.Valuta,
	sum( Rekeningmutaties.Aantal ) AS 'TotaalAantal',
	sum( Rekeningmutaties.Bedrag ) AS 'TotaalSaldo'
FROM
	Rekeningmutaties
	INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
	INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
	LEFT JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds 
WHERE
	YEAR(Rekeningmutaties.Boekdatum) = '2017' 
	AND Rekeningmutaties.Verwerkt = '1' 
	AND Boekdatum <= '2017-06-30'
	and Grootboekrekening='Fonds'
GROUP BY
	Portefeuilles.Portefeuille,
	Portefeuilles.Vermogensbeheerder,
	Portefeuilles.Depotbank,
	Rekeningen.Depotbank,
	Rekeningmutaties.Bewaarder,
CASE
		
		WHEN Rekeningmutaties.Bewaarder <> '' THEN
		Rekeningmutaties.Bewaarder ELSE Rekeningen.Depotbank 
	END,
	Rekeningen.Rekening,
	Rekeningmutaties.Fonds,
	Fondsen.ISINcode,
	Fondsen.Valuta 
	) mt

";

debug($_GET);
?>