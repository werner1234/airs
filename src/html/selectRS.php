<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/07/26 15:59:20 $
 		File Versie					: $Revision: 1.25 $

 		$Log:
*/

$needMysqlClass = true;

include_once("wwwvars.php");
require("../config/jsrsServer.php");
jsrsDispatch( "getRekeningen getSaldo getVoorlopigeSaldo getGrootboekrekening getFondskoers getValutakoers getAccountmanager getRisicoklasse getZorgplicht getFondsGebruik getRemisier getSoortOvereenkomsten" );

// voor rekeningmutatiesTemplate.php
function getGrootboekrekening($form)
{
	$grootboek = $form['Grootboekrekening'];
	$DB = new DB();
	$DB->SQL("SELECT FondsAanVerkoop,Storting,Kosten,Opbrengst,Beginboeking,Kruispost FROM Grootboekrekeningen WHERE Grootboekrekening = '".$grootboek."'");
	$DB->Query();
	$data = $DB->NextRecord();
	return $data['FondsAanVerkoop']."-".$data['Storting']."-".$data['Kosten']."-".$data['Opbrengst']."-".$data['Beginboeking']."-".$data['Kruispost'];
}

// voor rekeningmutatiesTemplate.php
function getFondskoers($form)
{
	$fonds = $form['Fonds'];
	$datum = jul2db(form2jul($form['Boekdatum']));
	$DB = new DB();

	//"SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid FROM Fondskoersen LEFT JOIN Fondsen ON Fondskoersen.Fonds = Fondsen.Fonds WHERE Fondskoersen.Fonds = '".$fonds."' AND Fondskoersen.datum = '".$datum."'"
	$query = "SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid FROM Fondsen  LEFT JOIN Fondskoersen ON Fondsen.Fonds = Fondskoersen.Fonds AND Fondskoersen.datum <=  '".$datum."' WHERE Fondsen.Fonds = '".$fonds."' ORDER BY Fondskoersen.datum DESC LIMIT 1";
	$DB->SQL($query);
	$DB->Query();
	if($DB->Records() > 0) {
		$data = $DB->NextRecord();
	}
	else {
		$DB = new DB();
		$DB->SQL("SELECT Fondskoersen.Koers, Fondsen.Valuta, Fondsen.Fondseenheid FROM Fondskoersen LEFT JOIN Fondsen ON Fondskoersen.Fonds = Fondsen.Fonds WHERE Fondskoersen.Fonds = '".$fonds."' ORDER BY Fondskoersen.datum DESC LIMIT 1");
		$DB->Query();
		$data = $DB->NextRecord();
	}
	return $data['Koers']."|".$data['Valuta']."|".$data['Fondseenheid'];
}

// voor rekeningmutatiesTemplate.php
function getValutakoers($form)
{
	$valuta = $form['Valuta'];
	$rekeningValuta = $form['RekeningValuta'];
	$datum = jul2db(form2jul($form['Boekdatum']));

	$DB = new DB();
	$DB->SQL("SELECT Valutakoersen.Koers FROM Valutakoersen WHERE Valutakoersen.Valuta = '".$valuta."' AND Valutakoersen.datum <= '".$datum."' ORDER BY Valutakoersen.datum DESC LIMIT 1");
	$DB->Query();
	$data1 = $DB->NextRecord();

	$DB->SQL("SELECT Valutakoersen.Koers FROM Valutakoersen WHERE Valutakoersen.Valuta = '".$rekeningValuta."' AND Valutakoersen.datum <= '".$datum."' ORDER BY Valutakoersen.datum DESC LIMIT 1");
	$DB->Query();
	$data2 = $DB->NextRecord();

	return $data1['Koers']."|".$data2['Koers'];
}

function getRekeningen($form)
{
	$clientID =  $form['Client'];
	$memoriaal = $form['Memoriaal'];
  return serializeSql( "SELECT Rekening,Rekening AS Value FROM Rekeningen,Portefeuilles WHERE Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Rekeningen.Inactief=0 AND Portefeuilles.Client = '".$clientID."' AND Memoriaal = '".$memoriaal."'" );
}

function getSaldo($form)
{
	$rekeningNr = $form['Rekening'];
	$stamp = form2jul($form['Datum']);

	$DB = new DB();
	$DB->SQL("SELECT NieuwSaldo,Afschriftnummer FROM Rekeningafschriften WHERE Rekeningafschriften.Rekening = '".$rekeningNr."' AND YEAR(Rekeningafschriften.Datum) = '".date("Y",$stamp)."' ORDER BY Afschriftnummer DESC LIMIT 1");
	$DB->Query();
	if($DB->Records() >0) {
		$data = $DB->NextRecord();
		$data['Afschriftnummer']++;
		return $data['Afschriftnummer']."|".$data['NieuwSaldo'];
	}
	else
	{
		$nummer = (date("Y",$stamp) * 1000)+1;
		return $nummer."|0";
	}
}


function getVoorlopigeSaldo($form)
{
	$rekeningNr = $form['Rekening'];
	$stamp = form2jul($form['Datum']);
  $saldo="0";

	$DB = new DB();
  $query="SELECT * FROM 
(
 (SELECT Afschriftnummer,change_date FROM VoorlopigeRekeningafschriften  WHERE VoorlopigeRekeningafschriften.Rekening = '".$rekeningNr."' AND YEAR(VoorlopigeRekeningafschriften.Datum) = '".date("Y",$stamp)."' ORDER BY Afschriftnummer DESC LIMIT 1)
   UNION
 (SELECT Afschriftnummer,change_date FROM Rekeningafschriften  WHERE Rekeningafschriften.Rekening = '".$rekeningNr."' AND YEAR(Rekeningafschriften.Datum) = '".date("Y",$stamp)."' ORDER BY Afschriftnummer DESC LIMIT 1) 
) 
  as tmp 
order by change_date desc limit 1";  
	$DB->SQL($query);
  $DB->Query();
	if($DB->Records() >0)
	{
		$data = $DB->NextRecord();
		$data['Afschriftnummer']++;
    $Afschriftnummer=$data['Afschriftnummer'];
	}
  else
  {
    $Afschriftnummer = (date("Y",$stamp) * 1000)+1;
  }
  
	$DB->SQL("SELECT NieuwSaldo FROM VoorlopigeRekeningafschriften WHERE VoorlopigeRekeningafschriften.Rekening = '".$rekeningNr."' AND YEAR(VoorlopigeRekeningafschriften.Datum) = '".date("Y",$stamp)."' AND Verwerkt = 0 ORDER BY Afschriftnummer DESC LIMIT 1");
	$DB->Query();
	if($DB->Records() >0)
	{
		$data = $DB->NextRecord();
		$saldo=round($data['NieuwSaldo'],2);
	}
	else
	{
	  $DB->SQL("SELECT NieuwSaldo FROM Rekeningafschriften WHERE Rekeningafschriften.Rekening = '".$rekeningNr."' AND YEAR(Rekeningafschriften.Datum) = '".date("Y",$stamp)."' ORDER BY Afschriftnummer DESC LIMIT 1");
	  $DB->Query();
	  if($DB->Records() >0)
	  {
	  	$data = $DB->NextRecord();
	  	$saldo=round($data['NieuwSaldo'],2);
  	}
  }
	return $Afschriftnummer."|".$saldo;
}
// PortefeuilleEdit

function getAccountmanager($form)
{
  $data='';
	$vmID =  $form['Vermogensbeheerder'];
	$db=new DB();
	$query="SELECT
VermogensbeheerdersPerBedrijf.bedrijf,
count(aantal.Vermogensbeheerder) as aantal
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN VermogensbeheerdersPerBedrijf as aantal ON VermogensbeheerdersPerBedrijf.Bedrijf = aantal.Bedrijf
WHERE VermogensbeheerdersPerBedrijf.Vermogensbeheerder='".$vmID."' GROUP BY VermogensbeheerdersPerBedrijf.bedrijf";
	$db->SQL($query);
  $db->Query();
  $bedrijf=$db->nextRecord();

	if(isset($bedrijf['bedrijf']) && $bedrijf['bedrijf']<>'' && $bedrijf['aantal']>1)
  {
    $query = "SELECT
concat(Accountmanagers.Accountmanager, ' - ',Accountmanagers.Vermogensbeheerder),
Accountmanagers.Accountmanager AS `Value`
FROM
Accountmanagers
INNER JOIN VermogensbeheerdersPerBedrijf ON Accountmanagers.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.bedrijf='" . $bedrijf['bedrijf'] . "'
ORDER BY if(Accountmanagers.Vermogensbeheerder='$vmID',0,1),Accountmanagers.Vermogensbeheerder,Accountmanagers.Accountmanager
";
    $data = serializeSql($query);
  }

  if($data=='')
  {
    $data = serializeSql("SELECT Accountmanager, Accountmanager AS Value FROM Accountmanagers WHERE Vermogensbeheerder = '" . $vmID . "' ORDER BY Accountmanagers.Accountmanager");
  }

  return $data;
}

function getSoortOvereenkomst($form)
{
	$vmID =  $form['Vermogensbeheerder'];
  return serializeSql( "SELECT waarde,waarde AS Value FROM KeuzePerVermogensbeheerder WHERE categorie='soortovereenkomsten' AND Vermogensbeheerder = '".$vmID."'" );
}

function getRisicoklasse($form)
{
	$vmID =  $form['Vermogensbeheerder'];
  return serializeSql( "SELECT Risicoklasse,Risicoklasse AS Value FROM Risicoklassen WHERE Vermogensbeheerder = '".$vmID."'" );
}

function getRemisier($form)
{
	$vmID =  $form['Vermogensbeheerder'];
  return serializeSql( "SELECT Remisier,Remisier AS Value FROM Remisiers WHERE Vermogensbeheerder = '".$vmID."'" );
}


function getFondsGebruik($form)
{
  return serializeSql( "SELECT GrootboekRekening, FondsGebruik FROM Grootboekrekeningen" );
}

// ZorgplichtPerFondsEdit.php

function getZorgplicht($form)
{
	$vmID =  $form[Vermogensbeheerder];
  return serializeSql( "SELECT Omschrijving, Zorgplicht AS Value FROM Zorgplichtcategorien WHERE Vermogensbeheerder = '".$vmID."'" );
}

function serializeSql( $sql ){
	$DB = new DB();
	$DB->SQL($sql);
	$DB->Query();
//logit($sql);
  $s = '';
  while ($row = $DB->NextRecord()) {
   $s .= join( $row, '~') . "|";
  }
  return $s;
}

?>
