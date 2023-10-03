<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/21 17:48:19 $
 		File Versie					: $Revision: 1.27 $

 		$Log: adventExportFile.php,v $
 		Revision 1.27  2018/12/21 17:48:19  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2018/10/15 11:12:46  cvs
 		call 7241
 		
 		Revision 1.25  2018/10/15 10:37:54  cvs
 		call 7087
 		
 		Revision 1.24  2018/06/20 14:10:06  cvs
 		binck code 160 voor regie
 		
 		Revision 1.23  2018/03/21 12:25:34  cvs
 		call 6758
 		
 		Revision 1.22  2018/03/06 11:02:51  cvs
 		call 6702
 		
 		Revision 1.21  2018/02/07 14:17:35  cvs
 		call 6569
 		
 		Revision 1.20  2018/01/12 16:34:57  cvs
 		call 6433
 		
 		Revision 1.19  2017/11/02 09:47:13  cvs
 		sql error banktransactieID en ubs code
 		
 		Revision 1.18  2017/09/20 06:22:43  cvs
 		megaupdate 2722
 		
 		Revision 1.17  2017/04/13 13:06:41  cvs
 		no message
 		
 		Revision 1.16  2017/02/22 07:40:15  cvs
 		cal 5571
 		
 		Revision 1.15  2016/10/21 10:15:19  cvs
 		call 5239
 		
 		Revision 1.14  2016/03/16 12:55:26  cvs
 		call 4747
 		
 		Revision 1.13  2015/09/09 15:06:10  cvs
 		*** empty log message ***
 		
 		Revision 1.12  2015/07/01 14:06:59  cvs
 		*** empty log message ***
 		
 		Revision 1.11  2015/03/16 12:39:28  cvs
 		*** empty log message ***
 		
 		Revision 1.10  2014/11/25 07:50:38  cvs
 		dbs 3217
 		
 		Revision 1.9  2014/11/06 09:25:51  cvs
 		dbs 3166
 		
 		Revision 1.8  2014/10/24 15:08:37  cvs
 		dbs 3148
 		
 		Revision 1.7  2014/03/12 11:18:50  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2014/03/10 09:57:19  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2014/03/07 13:44:02  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/01/31 11:16:31  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2013/12/16 08:20:59  cvs
 		*** empty log message ***

 		Revision 1.2  2013/12/11 10:06:26  cvs
 		*** empty log message ***

 		Revision 1.1  2013/11/15 10:22:21  cvs
 		aanpassing tbv Adventexport

*/
error_reporting(E_ALL);
include_once("wwwvars.php");
include_once("../classes/AE_cls_adventExport.php");
include_once("../classes/AE_cls_lookup.php");
include_once("../config/advent_functies.php");
session_start();

$export = new adventExport();

echo template($__appvar["templateContentHeader"],$editcontent);

echo "<h1>aanmaken Advent exportbestanden</h1>";
echo "<div id='loading'><img src='images/loading.gif' width='48'/> moment bezig met verwerken </div>";

switch ($_GET["type"])
{
  case "transBeheer":
    makeTransFile();
    break;
  case "cash":
    makeCashFile();
    break;
  case "positie":
    makePositieFile();
    break;
  case "alle":
    makeTransFile();
    $export = new adventExport();
    makeCashFile();
    break;
  case "validatePre":
    validateAirsInfo();
    break;
  case "validatePost":
    validateAirsInfo("post");
    break;
  default:
    echo "<br><h2 style='color: red'>Ongeldige aanroep</h2>";
    break;
}

?>
<script>
  $("#loading").hide();
</script>
<br/><br/>
<a href='advent_filemanager.php'>Ga naar Advent uitvoermap</a>
<?
exit;


function validateAirsInfo($typeValidate="pre")
{
  global $USR;
  global $depotBank;
  global $errorArray;
  $db = new DB();
  $db2 = new DB();
  $lkp = new AE_lookup();

  if ($typeValidate == "pre")
  {
    $query = "
      SELECT
        TijdelijkeRekeningmutaties.Rekening,
        Rekeningen.Depotbank
      FROM
        (TijdelijkeRekeningmutaties)
      INNER JOIN Rekeningen ON TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
      WHERE
        TijdelijkeRekeningmutaties.change_user = '$USR'
      GROUP BY
        TijdelijkeRekeningmutaties.Rekening
      ORDER BY
        Rekening ASC
    ";

    $db->executeQuery($query);
    while ($testRec = $db->nextRecord() )
    {
      $typeRekening = $lkp->getRekening(array( "rekening"=>$testRec["Rekening"], "depotbank"=>$testRec["Depotbank"]),"typeRekening");
      if ($typeRekening == "")
      {
        $errorArray[] = "Geen typeRekening bij ".$testRec["Rekening"]." / ".$testRec["Depotbank"];
      }
    }

    $query = "
    SELECT
      Fonds
    FROM
      (TijdelijkeRekeningmutaties)
    WHERE
      TijdelijkeRekeningmutaties.change_user = '$USR'
    AND
      Fonds <> ''
    GROUP BY
      Fonds
    ORDER BY
      Fonds ASC
    ";

    $dbTest = new DB();
    $dbTest->executeQuery($query);
    while ($testRec = $dbTest->nextRecord() )
    {
      $ISIN = $lkp->getFonds($testRec["Fonds"], "ISINCode");
      if ($adventRec = $lkp->getAdventMapping("Fonds = '".$testRec["Fonds"]."'") )
      {
        if (trim($adventRec["adventSecCode"]) == "")
        {
          $errorArray[] = "Geen adventSecCode bij ".$testRec["Fonds"]." ($ISIN)";
        }
        if (trim($adventRec["adventCode"]) == "")
        {
          $errorArray[] = "Geen adventCode bij ".$testRec["Fonds"]." ($ISIN)";;
        }
      }
      else
      {
        if ($testRec["Fonds"] <> "")
          $errorArray[] = "Geen adventMapping bij ".$testRec["Fonds"]." ($ISIN)";
      }
    }

    if (count($errorArray) > 0)
    {
      echo "<ol>";
      for ($x=0; $x < count($errorArray); $x++)
      {
        echo "<li>".$errorArray[$x]."\n";
      }
      echo "</ol>";
    }
    else
    {
      echo "<br/><br/><br/>Geen fouten gevonden!";
    }
  }
  else
  {
    $query = "
    SELECT
      count(id) as aantal
    FROM
      (TijdelijkeRekeningmutaties)
    WHERE
      verwerkt = 0 AND
      TijdelijkeRekeningmutaties.change_user = '$USR'
    ";

    $nietverwerkt = $db->lookupRecordByQuery($query);

    echo "<li> records niet verwerkt: ".$nietverwerkt["aantal"];
  }

?>
<script>
  $("#loading").hide();
</script>
  <br/>
  <br/>
  <a href='tijdelijkerekeningmutatiesList.php'>Tijdelijk importbestand</a><br/>
  <a href='adventExport.php'>Advent exportmenu</a><br/><br/>
<?
  exit();
}


function makeTransFile()
{
  global $USR, $rec;
  global $export, $errorArray;
  global $depotBank;

  $lkp      = new AE_lookup();

  //
  // aan- en verkoop stukken  boekingen
  //
  $dbAdvent = GetMutaties("Transactietype IN  ('A','V', 'A/O', 'V/O', 'A/S', 'V/S') ");

  while ($rec = $dbAdvent->nextRecord())
  {
   
    //listarray($rec);
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }
    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $kostQuery = "bankTransactieId = '".$rec["bankTransactieId"]. "' AND Grootboekrekening = 'KOST'";
    $useKost = false;
    if( $kostRec = lookupMutatie($kostQuery))
    {
      SetMutatiesVerwerkt($kostQuery);
      $useKost = true;
    }

    /////////////////////////////
    $kobuQuery = "bankTransactieId = '".$rec["bankTransactieId"]. "' AND Grootboekrekening = 'KOBU'";
    $useKobu = false;
    if( $kobuRec = lookupMutatie($kobuQuery))
    {
      SetMutatiesVerwerkt($kobuQuery);
      $useKobu = true;
    }
    /////////////////////////////
    $tobQuery = "bankTransactieId = '".$rec["bankTransactieId"]. "' AND Grootboekrekening = 'TOB'";
    $useTob = false;
    if( $tobRec = lookupMutatie($tobQuery))
    {
      SetMutatiesVerwerkt($tobQuery);
      $useTob = true;
    }
    /////////////////////////////
    if ($useKobu OR $useTob)
    {
      $useKobu = true;
      $kobuRec["Bedrag"] += $tobRec["Bedrag"];
    }

    /////////////////////////////
    $renobQuery = "bankTransactieId = '".$rec["bankTransactieId"]. "' AND Grootboekrekening IN ('RENOB','RENME') ";

    $useRenob = false;
    if( $renobRec = lookupMutatie($renobQuery))
    {
      SetMutatiesVerwerkt($renobQuery);
      $useRenob = true;
    }
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;

    $AdventMap = $lkp->getAdventMapping("Fonds = '".$rec["Fonds"]."'");
    //listarray($AdventMap);

    switch ($rec["Transactietype"])
    {
      case "A":
        $tt = "by";
        break;
      case "V":
        $tt = "sl";
        break;
      case "A/O":
        $tt = "by";
        break;
      case "V/O":
        $tt = "ss";
        break;
      case "A/S":
        $tt = "cs";
        break;
      case "V/S":
        $tt = "sl";
        break;
      default:
        $tt = "";
    }

    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,$tt);
    $export->addField(4,$AdventMap["adventSecCode"].$fondsValuta);
    $export->addField(5,$AdventMap["adventCode"]);
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(9,abs($rec["Aantal"]));
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    $export->addField(18,adventBedrag($rec));
    $export->addField(23,"0");
    if ($useKost) $export->addField(24,adventBedrag($kostRec));
    $export->addField(26,"y");
    if ($useKobu) $export->addField(27,adventBedrag($kobuRec));
    
    if (substr($rec["Transactietype"],0,1) == "A")
      $export->lineBuffer[18] += ($export->lineBuffer[24] + $export->lineBuffer[27]);
    else
      $export->lineBuffer[18] -= ($export->lineBuffer[24] + $export->lineBuffer[27]);
    
    $export->addField(29,"n");
    $export->addField(30,getDepotBank($portRec["bank"],"N"));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addField(78,getDepotBank($portRec["bank"]));
    $export->addValutaFields($rec);

    if ( trim(strtolower($portRec["SoortOvereenkomst"])) == "regie")
    {
      $export->addField(30,getDepotBank($portRec["bank"],"N",true));
      $export->pushBuffer("2");
    }
    else
      $export->pushBuffer();

    if ($useRenob)
    {
      $export->addField(1,rewritePortefeuille($portRec));
      $export->addField(2,(substr($rec["Transactietype"],0,1) == "A")?"pa":"sa");
      $export->addField(4,$AdventMap["adventSecCode"].$fondsValuta);
      $export->addField(5,$AdventMap["adventCode"]);
      $export->addField(6,dbdate2advent($rec["Boekdatum"]));
      $export->addField(7,dbdate2advent($rec["settlementDatum"]));
      $export->addField(9,"1");
      $export->addField(12,"ca".$valutaCode);
      $export->addField(13,$portRec["typeRekening"]);
      $export->addField(17,"y");
      $export->addField(18,adventBedrag($renobRec));
      $export->addField(42,"2");
      $export->addField(45,"n");
      $export->addValutaFields($rec);
      if ( trim(strtolower($portRec["SoortOvereenkomst"])) == "regie")
      {
        $export->pushBuffer("2");
      }
      else
        $export->pushBuffer();
    }
  }
  SetMutatiesVerwerkt("Transactietype IN  ('A','V', 'A/O', 'V/O', 'A/S', 'V/S') ");

  // aanmaken CSV bestand
  $export->makeCsv("transacties_beheer_".$depotBank."_");
  $export->makeCsv("transacties_regie_".$depotBank."_","2");
}


//function dbdate2match($date)
//{
//  $d = explode("-", $date);
//  return mktime(0,0,0,$d[1],$d[2],$d[0]);
//}

function makeCashFile()
{
  global $USR, $rec;
  global $export;
  global $depotBank;

  $lkp      = new AE_lookup();

  //
  // stortingen en ontrekkingen
  //

  $dbAdvent = GetMutaties("Grootboekrekening IN ('ONTTR','STORT') AND RIGHT(TijdelijkeRekeningmutaties.Rekening, 3) <> 'MEM'");

  while ($rec = $dbAdvent->nextRecord())
  {
    
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }
    
    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $tel++;
    $rec["teller"] = $tel;

    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;



    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));


    if (substr($rec["aktie"],0,2) == "MA")
    {
      $export->addField(2,($rec["Grootboekrekening"] == "STORT")?"ti":"to");
    }
    else
    {
      $export->addField(2,($rec["Grootboekrekening"] == "STORT")?"li":"lo");
    }
    if ($rec["Grootboekrekening"] == "ONTTR" AND substr(strtolower($rec["Omschrijving"]),0,12) == "uitlevering ")
    {
      $export->addField(2,"dp");
      $export->addField(4,"ep".$fondsValuta);
      $export->addField(5,"depkost");
    }
    else
    {
      $export->addField(4,"ca".$fondsValuta);
      $export->addField(5,$portRec["typeRekening"]);
    }

    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(17,"y");
    $export->addField(18,adventBedrag($rec));
    $export->addField(42,"2");
    $export->addValutaFields($rec);
    $export->pushBuffer();

    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,";");
    $export->addField(3,str_replace(",", " ",$rec["Omschrijving"]));
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
//debug($export);
    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening IN ('ONTTR','STORT') AND RIGHT(TijdelijkeRekeningmutaties.Rekening, 3) <> 'MEM'");
  //
  // rente boekingen
  //
  $dbAdvent = GetMutaties("Grootboekrekening = 'RENTE'");


  while ($rec = $dbAdvent->nextRecord())
  {
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;

    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,($rec["Bedrag"] < 0 )?"ai":"in");
    $export->addField(4,"ca".$valutaCode);
//    $export->addField(5,$portRec["typeRekening"]);
    $export->addField(5,"Interest"); // call 6758
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(9,$bedragAbs);
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    $export->addField(18,adventBedrag($rec));
    $export->addField(21,"0");
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addField(79,"n");
    $export->addValutaFields($rec);

    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening = 'RENTE'");

  //
  // Fee boekingen
  //
  $dbAdvent = GetMutaties("Grootboekrekening IN  ('BEH', 'BEW')");

  while ($rec = $dbAdvent->nextRecord())
  {
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;
    
    if ($portRec["Remisier"] == "WMP-exkst")  // dbs 3506
    {
      $grootboek = ($rec["Grootboekrekening"] == "BEH")?"manfeeex":"bewaarex";
    }
    else
    {
      if ($rec["Depotbank"] == "LOM" OR $rec["Depotbank"] == "UBS" )
      {
        $grootboek = ($rec["Grootboekrekening"] == "BEH")?"manfeeWMPS":"bewaarloon";
      }
      else
      {
        $grootboek = ($rec["Grootboekrekening"] == "BEH")?"manfee":"bewaarloon";
      }

    }
    
    $oms = substr(strtolower($rec["Omschrijving"]),0,19);
    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    
    $soortValuta = "ep".$valutaCode;
    
    if ($oms == "execution only verg" OR
        $oms == "BE Execution only v")
    {
      $grootboek = "exonwmp";
      $soortValuta = "ex".$valutaCode;
    }
    elseif ($oms == "vermogensregie verg" OR 
            $oms == "vermogensregievergo")
    {
      $grootboek = "VRFee";
      $soortValuta = "ex".$valutaCode;
    }
    
  
    
    
    
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,($rec["Bedrag"] < 0 )?"dp":"wd");
    $export->addField(4,$soortValuta);
    $export->addField(5,$grootboek);
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    $export->addField(18,adventBedrag($rec));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addValutaFields($rec);
    $export->pushBuffer();

    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,";");
    $export->addField(3,str_replace(",", " ",$rec["Omschrijving"]));
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));

    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening IN  ('BEH', 'BEW')");
  //
  // Coupon boekingen
  //
  $dbAdvent = GetMutaties("Grootboekrekening IN  ('RENOB') AND aktie IN ('PRED','R','UITK +','CR','CP','INTR') ");

  while ($rec = $dbAdvent->nextRecord())
  {
//    debug($rec);
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $divBeQuery = "bankTransactieId = '".$rec["bankTransactieId"]. "' AND Grootboekrekening = 'DIVBE'";
    $useDivBe = false;
    if( $divbeRec = lookupMutatie($divBeQuery))
    {
      SetMutatiesVerwerkt($divBeQuery);
      $useDivBe = true;
    }
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;

    $AdventMap = $lkp->getAdventMapping("Fonds = '".$rec["Fonds"]."'");
    $fondsRec = $lkp->getFonds($rec["Fonds"]);
    $bedragAbs = abs($rec["Bedrag"]);
    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($fondsRec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,($rec["aktie"]=="PRED")?"ac":"in");
    $export->addField(4,$AdventMap["adventSecCode"].$fondsValuta);
    $export->addField(5,$AdventMap["adventCode"]);
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
//    $export->addField(18,adventBedrag($rec));
    $export->addField(18,adventBedrag2($rec["Bedrag"] + $divbeRec["Bedrag"]));
    if ($useDivBe) $export->addField(21,adventBedrag($divbeRec));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addField(79,"n");
    $export->addValutaFields($rec);

    $export->pushBuffer();

    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,";");
    $export->addField(3,str_replace(",", " ",$rec["Omschrijving"]));
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));

    $export->pushBuffer();

  }
  SetMutatiesVerwerkt("Grootboekrekening IN  ('RENOB') AND aktie IN ('PRED','R','UITK +','CR') ");

  //
  // Dividend boekingen
  //
  $dbAdvent = GetMutaties("Grootboekrekening IN  ('DIV') ");

  while ($rec = $dbAdvent->nextRecord())
  {
   
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $divBeQuery = "bankTransactieId = '".$rec["bankTransactieId"]. "' AND Grootboekrekening = 'DIVBE'";
    $useDivBe = false;
    if( $divbeRec = lookupMutatie($divBeQuery))
    {
      SetMutatiesVerwerkt($divBeQuery);
      $useDivBe = true;
    }
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;

    $AdventMap = $lkp->getAdventMapping("Fonds = '".$rec["Fonds"]."'");
    $fondsRec  = $lkp->getFonds($rec["Fonds"]);
   
    $bedragAbs = abs($rec["Bedrag"]);
    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($fondsRec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,"dv");
    $export->addField(4,$AdventMap["adventSecCode"].$fondsValuta);
    $export->addField(5,$AdventMap["adventCode"]);
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    //$export->addField(18,adventBedrag($rec));
    $export->addField(18,adventBedrag2($rec["Bedrag"] + $divbeRec["Bedrag"]));
    if ($useDivBe) $export->addField(21,adventBedrag($divbeRec));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addField(79,"n");
    $export->addField(80,"0");
    $export->addValutaFields($rec);
    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening IN  ('DIV')");

  //
  // Valutatransacties
  //
  $dbAdvent = GetMutaties("Grootboekrekening IN  ('KRUIS') AND TijdelijkeRekeningmutaties.Valuta <> 'EUR' ");

  //TODO waarom gaat adventbedrag hier fout??

  while ($rec = $dbAdvent->nextRecord())
  {
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $bedragInEUR = abs(number_format($rec["Valutakoers"] * $rec["Bedrag"],2,".",""));
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;

    $AdventMap = $lkp->getAdventMapping("Fonds = '".$rec["Fonds"]."'");
    $bedragAbs = abs($rec["Bedrag"]);
    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);

    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,($rec["Bedrag"] > 0)?"by":"sl");
    $export->addField(4,"ca".$fondsValuta);
    $export->addField(5,"cash");
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(9,$bedragAbs);
    $export->addField(12,"caeu");
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    $export->addField(18,$bedragInEUR);
    $export->addField(42,"2");
    $export->addField(45,"n");
    if ($rec["aktie"] == "FX" )
    {
      $export->addField(29,"n");
      $export->addField(30,"65533");
    }
    $export->addValutaFields($rec);
    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening IN  ('KRUIS') AND TijdelijkeRekeningmutaties.Valuta <> 'EUR' ");

  //
  // Kosten op Coupon/dividend boekingen
  //
  $dbAdvent = GetMutaties( "Grootboekrekening = 'KNBA' AND aktie IN ('R','DV','CD','CR') ");

  while ($rec = $dbAdvent->nextRecord())
  {
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,"dp");
    $export->addField(4,"ep".$valutaCode);
    $export->addField(5,($rec["aktie"] == "R")?"cpncomm":"dvcomm");
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    $export->addField(18,adventBedrag($rec));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addValutaFields($rec);
    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening = 'KNBA' AND aktie IN ('R','DV','CD','CR')");


 //
  // Bankkosten
  //
  $dbAdvent = GetMutaties( "Grootboekrekening = 'KNBA' AND aktie NOT IN ('R','DV','CD','CR','O-G') ");

  while ($rec = $dbAdvent->nextRecord())
  {
    
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,"dp");
    $export->addField(4,"ex".$valutaCode);
    $export->addField(5,"Bankkosten");
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    $export->addField(18,adventBedrag($rec));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addValutaFields($rec);
    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening = 'KNBA' AND aktie NOT IN ('R','DV','CD','CR','O-G')");


  //
  // Omgehangen kosten  van ontrekking
  //
  $dbAdvent = GetMutaties( "Grootboekrekening IN ('KNBA','KOST','KOBU') AND aktie ='O-G' ");

  while ($rec = $dbAdvent->nextRecord())
  {
    
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }

    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,"dp");
    $export->addField(4,"ex".$valutaCode);
    $export->addField(5,"Bankkosten");
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(12,"ca".$valutaCode);
    $export->addField(13,$portRec["typeRekening"]);
    $export->addField(17,"y");
    $export->addField(18,adventBedrag($rec));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addValutaFields($rec);
    $export->pushBuffer();
  }
  SetMutatiesVerwerkt("Grootboekrekening IN ('KNBA','KOST','KOBU') AND aktie ='O-G' ");
  
  //
  // deponering en lichting stukken Dividend boekingen
  //
  $dbAdvent = GetMutaties("Transactietype IN  ('L','D') ");

  while ($rec = $dbAdvent->nextRecord())
  {
    if ($rec["settlementDatum"] <> "0000-00-00" AND (dbdate2match($rec["Boekdatum"]) > dbdate2match($rec["settlementDatum"])) )   
    {
      $rec["Boekdatum"] = $rec["settlementDatum"] ;
    }
    if (trim($rec["settlementDatum"]) == "0000-00-00")  $rec["settlementDatum"] = $rec["Boekdatum"];  // als settlement datum leeg dan boekdatum gebruiken.
    
    $renobQuery = "bankTransactieId = '".$rec["bankTransactieId"]. "' AND Grootboekrekening IN ('RENOB','RENME')";
    $useRenob = false;
    if( $renobRec = lookupMutatie($renobQuery))
    {
      SetMutatiesVerwerkt($renobQuery);
      $useRenob = true;
    }
    $portRec = $lkp->getRekening(array( "rekening"=>$rec["Rekening"], "depotbank"=>$rec["Depotbank"]));
    if ($portRec["InternDepot"]) continue;

    $AdventMap = $lkp->getAdventMapping("Fonds = '".$rec["Fonds"]."'");

    $valutaCode = adventValuta($rec["Rekening"]);
    $fondsValuta = adventValuta($rec["Valuta"]);
    $export->addField(1,rewritePortefeuille($portRec));
    $export->addField(2,($rec["Transactietype"] == "L")?"lo":"li");
    $export->addField(4,$AdventMap["adventSecCode"].$fondsValuta);
    $export->addField(5,$AdventMap["adventCode"]);
    $export->addField(6,dbdate2advent($rec["Boekdatum"]));
    $export->addField(7,dbdate2advent($rec["settlementDatum"]));
    $export->addField(9,abs($rec["Aantal"]));
    $export->addField(17,"y");
    $export->addField(18,abs($rec["Debet"]+$rec["Credit"]));
    $export->addField(29,"n");
    $export->addField(30,getDepotBank($portRec["bank"],"N"));
    $export->addField(42,"2");
    $export->addField(45,"n");
    $export->addField(59,"y");
    $export->addField(78,getDepotBank($portRec["bank"]));
    $export->addValutaFields($rec);

    if ( trim(strtolower($portRec["SoortOvereenkomst"])) == "regie")
    {
      $export->addField(30,getDepotBank($portRec["bank"],"N",true));
    }

    $export->pushBuffer();

    if ($useRenob)
    {
      $code12 = "aw".$valutaCode;
      //debug("","in renob");
      $export->addField(1,rewritePortefeuille($portRec));
      $export->addField(2,($rec["Transactietype"] == "L")?"sa":"pa");
      $export->addField(4,$AdventMap["adventSecCode"].$fondsValuta);
      $export->addField(5,$AdventMap["adventCode"]);
      $export->addField(6,dbdate2advent($rec["Boekdatum"]));
      $export->addField(7,dbdate2advent($rec["settlementDatum"]));
      $export->addField(9,"");
      $export->addField(12,$valutaCode);
      $export->addField(13,"client");
      $export->addField(17,"y");
      $export->addField(18,adventBedrag($renobRec));
      $export->addField(42,"2");
      $export->addField(45,"n");
      $export->addValutaFields($rec);
      $export->addField(12,"aw".$export->lineBuffer[12]);
      $export->pushBuffer();
    }
    
  }
  SetMutatiesVerwerkt("Transactietype IN  ('L','D') ");


  // aanmaken CSV bestand
  $export->makeCsv("overige_".$depotBank."_");
}

function getDepotBank($bank,$soort="A",$regie=false)
{
  global $depotBank;
  $depotArray = array(
    "TGB" => array("TGB","1","150"),
    "BIN" => array("BBK","22","160"),
    "AAB" => array("AABH","31","11"),
    "FVL" => array("FVL","7","7"),
    "LOM" => array("LOM","37","37"),
    "UBS" => array("UBS","65533","65533")
  );
  
  $depotBank = $bank;
  if ($soort <> "A")
  {
    return ($regie)?$depotArray[$bank][2]:$depotArray[$bank][1];
  }
  else
  {
    return $depotArray[$bank][0];
  }


}

function rewritePortefeuille($portRec)
{
  global $depotBank;
  $depotBank = $portRec["bank"];
  
  $dArray = array( "TGB" => "TG", "BIN" => "B", "AAB" => "A");
  if ($portRec["bank"] == "FVL")  // uitzondering voor van lanschot
  {
    $portefeuille = $portRec["Portefeuille"];
  }
  else
  {
    $portefeuille = ($portRec["PortefeuilleDepotbank"] <> "")?$portRec["PortefeuilleDepotbank"]:$portRec["Portefeuille"];
  }

  if ($portRec["bank"] == "UBSL")  // uitzondering voor van lanschot
  {
    $portefeuille = ($portRec["PortefeuilleDepotbank"] <> "")?$portRec["PortefeuilleDepotbank"]:$portRec["Portefeuille"];
    $portefeuille .= "001";
  }

  if ( trim(strtolower($portRec["SoortOvereenkomst"])) == "regie")
  {
    return $dArray[$portRec["bank"]].$portefeuille;
  }
  else
  {
    return $portefeuille;
  }
}

function adventBedrag($record,$abs=true)
{
  $bedrag = number_format($record["Bedrag"],2,".","");
  return ($abs)?abs($bedrag):$bedrag;
}

function adventBedrag2($bedrag,$abs=true)
{
  $bedrag = round($bedrag,2);
  return ($abs)?abs($bedrag):$bedrag;
}

function adventValuta($rekening)
{
  return strtolower(
                    substr(
                           substr($rekening,-3)
                           ,0,2));

}

function lookupMutatie($where, $debug=false)
{
  global $USR;
  $db = new DB();

  $query = "
  SELECT
    TijdelijkeRekeningmutaties.*
  FROM
    (TijdelijkeRekeningmutaties)
  WHERE
    {$where}
  ";
  if (!$debug)
  {
    $query .= "
  AND
  (
      verwerkt < 1
    OR
      ISNULL(verwerkt)
  )
    ";

  }
  $query .= "
  AND
    TijdelijkeRekeningmutaties.change_user = '{$USR}'
  ";
    
  return $db->lookupRecordByQuery($query);
}


function GetMutaties($where, $debug=false)
{
  global $USR;
  $db = new DB();

  $query = "
  SELECT
    TijdelijkeRekeningmutaties.*,
    Rekeningen.Depotbank
  FROM
    (TijdelijkeRekeningmutaties)
  INNER JOIN Rekeningen ON TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
  WHERE
    {$where}
  ";
  if (!$debug)
  {
    $query .= "
  AND
  (
      verwerkt < 1
    OR
      ISNULL(verwerkt)
  )";
  }
  $query .= "
  AND
    TijdelijkeRekeningmutaties.change_user = '{$USR}'
  ORDER BY
    TijdelijkeRekeningmutaties.Rekening ASC";

  
  $db->executeQuery($query);
  return $db;
}

function SetMutatiesVerwerkt($where)
{
  global $USR;
  $db = new DB();
  $query = "
  UPDATE
    (TijdelijkeRekeningmutaties)
  SET
    verwerkt = 1
  WHERE
    {$where}
  AND
    TijdelijkeRekeningmutaties.change_user = '{$USR}'
  ";
  $db->executeQuery($query);

  return $db;

}

?>