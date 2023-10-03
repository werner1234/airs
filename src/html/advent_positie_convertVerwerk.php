<?php
/*
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2015/05/06 09:46:13 $
    File Versie            : $Revision: 1.4 $

    $Log: advent_positie_convertVerwerk.php,v $
    Revision 1.4  2015/05/06 09:46:13  cvs
    *** empty log message ***

    Revision 1.3  2014/04/04 09:01:32  cvs
    *** empty log message ***

    Revision 1.2  2014/03/07 13:44:02  cvs
    *** empty log message ***

    Revision 1.1  2013/12/16 08:21:00  cvs
    *** empty log message ***

    Revision 1.6  2012/02/14 14:22:48  cvs
    update 14-2-2012
    uitvoer via TMP_571 tabel, om fractie telling mogelijk te maken

    Revision 1.5  2011/11/29 09:28:06  cvs
    als importregel met spatie dan verwijder spatie

    Revision 1.4  2011/11/11 12:55:06  cvs
    veld ASof toeveogen, update 11-11-2011

    Revision 1.3  2011/11/09 11:07:31  cvs
    fieldheader bij geldmutaties, update 9-11-2011

    Revision 1.2  2011/11/08 15:43:12  cvs
    verschillende datum formaten, update 8 november 2011

    Revision 1.1  2011/10/26 12:20:43  cvs
    versie 1.00 eerste commit



*/
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/AE_cls_lookup.php');
include_once("wwwvars.php");
include_once("advent_positie_convertFuncties.php");


$lkp = new AE_lookup();

$cfg = new AE_config();

$directory = $cfg->getData("advent_outputDir");


//$datumFormat = $cfg->getData("datumFormat");

//$startMemory = memory_get_usage(true);
$MT5Naam = $_GET["MT5XX"];
$MT9Naam = $_GET["MT940"];
$MT5valid = validateFile($MT5Naam);
$MT9valid = validateFile($MT9Naam);
printStatus("Validatie bestanden");
printStatus("MT5XX is ".($MT5valid?"gevalideerd":"ongeldig"));
printStatus("MT940 is ".($MT9valid?"gevalideerd":"ongeldig"));

if (!$MT5valid OR !$MT9valid)
{
  printStatus("conversie afgebroken, ongeldig bestand gevonden").
  unlink($MT5Naam);
  unlink($MT9Naam);
  exit();
}

//
// setup van de progressbar
//
$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','Start conversie ...');	// add Text as Label 'txt1' and value 'Please wait'
printStatus("Start conversie ...");
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar



$row = 1;
$ndx=0;
$dataSet = Array();
$row = Array();
$_tempRow = Array();

// MT5xx inlezen
$regels = count(file($MT5Naam));
$prb->max = $regels;
$prb->setLabelValue('txt1','Inlezen MT5XX bestand ('.$regels.' regels)');
printStatus('Inlezen MT5XX bestand ('.$regels.' regels)');
$handle = fopen($MT5Naam, "r");

while ($data = fgets($handle, 4096))
{
	if ($data[0] == " ") $data = substr($data,1);  // als regel begint met spatie deze verwijderen
	$prb->moveNext();
  MTfileToArray($data);
}
fclose($handle);
unlink($MT5Naam);

// MT940 inlezen

$regels = count(file($MT9Naam));
$prb->max = $regels;
$prb->setLabelValue('txt1','Inlezen MT940 bestand ('.$regels.' regels)');
printStatus('Inlezen MT940 bestand ('.$regels.' regels)');
$prb->moveMin();
$handle = fopen($MT9Naam, "r");

while ($data = fgets($handle, 4096))
{
	if ($data[0] == " ") $data = substr($data,1);  // als regel begint met spatie deze verwijderen
	$prb->moveNext();
  MTfileToArray($data);
}
fclose($handle);
unlink($MT9Naam);

//
//  alle ongeldige records weggooien
//


$prb->max = count($dataSet);
$prb->setLabelValue('txt1','Indelen MT records ('.count($dataSet)." items) ");
printStatus('Indelen MT records ('.count($dataSet)." items) ");
$prb->moveMin();
for($loopndx = 0;$loopndx < count($dataSet);$loopndx++)
{
	$_var = trim($dataSet[$loopndx]["type"]);
  $prb->moveNext();

	switch ($_var)
	{
		case "940":
		  $_tmprows = array();
		  $_mt940Count++;
		  $_data = explode(chr(10),$dataSet[$loopndx]["txt"]);
		  $addRecord = 0;
		  for ($subLoop = 0; $subLoop < count($_data);$subLoop++)
		  {
		  	$_r = explode("&&",$_data[$subLoop]);
		  	array_push($_tmprows,$_data[$subLoop]);
		  	if ($_r[0] == "25") $addRecord++; // veld 61 bestaat
		  	if ($_r[0] == "86")               // skip veld 83 als de tekst begin met waarden uit de $mt940_skip array (transacties)
		  	{
		  		$skipTag = false;
		  		for ($xx=0; $xx < count($mt940_skip);$xx++)
		    	{
		    		$arrValue = $mt940_skip[$xx];
		  	  	if (substr(strtoupper($_r[1]),0,strlen($arrValue)) == $arrValue)
		  	  	{
		  	  	  array_pop($_tmprows);  // verwijder 83 regel
		  	  	  array_pop($_tmprows);  // verwijder vorige 61 regel
		  	  	  $addRecord--;
		  	  		$skipTag = true;
		  	  		$mt940_83_skip++;
		  	  	}
		    	}
		    	if ($skipTag)
		    	  $mt940_rec[] = "[SKIP]  ".str_replace(chr(13),"#",$_r[1]);
		    	else
		    	  $mt940_rec[] = str_replace(chr(13),"#",$_r[1]);
		  	}

		  }

		  if ($addRecord > 0)
		  {
	  		$_mt940CountClean++;
	  		$dataSet940[] = array("type"=>"940","txt"=>implode(chr(10),$_tmprows));
	  	}
		  break;
    case "554":
		  $_mt554Count++;
		  $dataSet554[] = $dataSet[$loopndx];
		  break;
    case "571":
		  $_mt571Count++;
		  $dataSet571[] = $dataSet[$loopndx];
		  break;
		default:

	}
}
unset($dataSet);

printStatus("MT940 records :".count($dataSet940));
printStatus("MT554 records :".count($dataSet554));
printStatus("MT571 records :".count($dataSet571));

if (count($dataSet940) > 0)
{
  $prb->max = count($dataSet940);
  $prb->setLabelValue('txt1','exportset aanmaken MT940 ('.count($dataSet940)." records) ");
  $prb->moveMin();

  if (count($dataSet940) <> 0) // er bestaand MT940 mutaties
  {
	  for ($_ndx = 0; $_ndx < count($dataSet940);$_ndx++)
	  {
      $prb->moveNext();
		  $MT940_converted[] = convertMt940($dataSet940[$_ndx]);
	  }
  }
  unset($dataSet940);
  printStatus("MT940 exportset gereed");
}

if (count($dataSet571) > 0)
{
  $prb->max = count($dataSet571);
  $prb->setLabelValue('txt1','exportset aanmaken MT571 ('.count($dataSet571)." records) ");
  $prb->moveMin();

  if (count($dataSet571) <> 0) // er bestaand MT571 mutaties
  {
    for ($_ndx = 0; $_ndx < count($dataSet571);$_ndx++)
    {
      $prb->moveNext();
		  $MT571_converted[] = convertMt571($dataSet571[$_ndx]);
	  }
  }
  unset($dataSet571);
  printStatus("MT571 exportset gereed");
}

if (count($dataSet554) > 0)
{
  $prb->max = count($dataSet554);
  $prb->setLabelValue('txt1','exportset aanmaken MT554 ('.count($dataSet554)." records) ");
  $prb->moveMin();

  if (count($dataSet554) <> 0) // er bestaand MT554 mutaties
  {
    for ($_ndx = 0; $_ndx < count($dataSet554);$_ndx++)
    {
      $prb->moveNext();
		  $MT554_converted[] = convertMt554($dataSet554[$_ndx]);
	  }
  }
  unset($dataSet544);
  printStatus("MT554 exportset gereed");

}

// aanmaken MT940 csv file

$filenameA = $directory."cashPosities_AAB_".date("Ymd_His").".csv";

if (count($MT940_converted) > 0)
{
  printStatus("schrijven van $filenameA");

  $fhA = @fopen($filenameA, "w");
  // schrijf fieldheader
  //$data = '"As of","Account ID","Local Curr.","Value Local","Base Curr.","Value Base"'."\n";
  fwrite($fhA, $data);

  $prb->max = count($MT940_converted);
  $prb->setLabelValue('txt1','csv bestand aanmaken MT940 ('.count($MT940_converted)." records) ");
  $prb->moveMin();
  for ($x=0; $x < count($MT940_converted) ;$x++)
  {
    $prb->moveNext();
    $row = $MT940_converted[$x];
    $rekRec = $lkp->getRekening(array( "rekening"  => $row["rekeningnr"].$row["valutacode62"], 
                                       "depotbank" =>"AAB"
                               ));
    $dataArray = array($row["asOf"],
                       $rekRec["Portefeuille"],
                       $row["rekeningnr"],
                       '"'.$row["valutacode62"].'"',
                       $row["nieuwSaldo62"],
                       '"'.$rekRec["typeRekening"].'"'
                       );

    $data = implode(',',$dataArray)."\n";
    fwrite($fhA, $data);
  }
  fclose($fhA);
  unset($MT940_converted);
}

// aanmaken MT571 cvs file


$filename = $directory."effectenPosities_AAB_".date("Ymd_His").".csv";
if (count($MT571_converted) > 0)
{
  $data = "";
  printStatus("schrijven van $filename");
  $fh = @fopen($filename, "w");
  // schrijf fieldheader
  //$data = '"As of","Account ID","Account Name","Issue Type","IssueID","Issue Name","Quantity"'."\n";
  fwrite($fh, $data);
  $prb->max = count($MT571_converted);
  $prb->setLabelValue('txt1','csv bestand aanmaken MT571 ('.count($MT571_converted)." records) ");
  $prb->moveMin();
  $db = new DB();
  $query = "TRUNCATE TMP_571";
  $db->executeQuery($query);

  for ($x=0; $x < count($MT571_converted) ;$x++)
  {
    $prb->moveNext();
    $row = $MT571_converted[$x];
    for($y=0;$y < count($row);$y++)
    {
      $tmp = "";
      if (trim($row[$y]["isincode"]) <> "")
      {
        $query = "SELECT * FROM Fondsen WHERE AABCode= '".trim($row[$y]["aabcode"])."' OR ABRCode= '".trim($row[$y]["aabcode"])."'";
        if ($isinRec = $db->lookupRecordByQuery($query))
        {
           if ($isinRec["fractieEenheid"] <> 0)
           {
             $row[$y]["aantal"] =  $row[$y]["aantal"] / $isinRec["fractieEenheid"];
             $tmp = "fractie";
           }

         }
         addTMP_571(array($row[$y]["asOf"],
                         $row[$y]["portefeuille"],
                         $row[$y]["aabcode"],
                         "",
                         $row[$y]["isincode"],
                         trim(addslashes($row[$y]["fondsnaam"])),
                         $row[$y]["aantal"] ));

      }
    }
  }


  // vind alle fondsen met fracties
  $query = 'SELECT *, sum(aantal) as totaal FROM TMP_571 WHERE isincode NOT in ("Geen ISIN", "") GROUP BY portefeuille, isincode';
  $db->executeQuery($query);
  while ($tmpRec = $db->nextRecord())
  {
      if ($tmpRec["aantal"] <> $tmpRec["totaal"])
      {
        $mergeRecs[] = $tmpRec;
      }
  }
  // de gevonden fractie records verwerken
  for ($x=0; $x < count($mergeRecs); $x++)
  {
    $_r = $mergeRecs[$x];
    // verwijder de losse records
    $query = "DELETE FROM TMP_571 WHERE portefeuille = '".$_r["portefeuille"]."' AND isincode = '".$_r["isincode"]."'";
    $db->executeQuery($query);

    // voeg 1 getotaliseerd record in de tabel toe.
    $query  = "INSERT INTO TMP_571 SET ";
    $query .= "  asOf = '".$_r["asOf"]."' ";
    $query .= ",  portefeuille = '".$_r["portefeuille"]."' ";
    $query .= ",  isincode = '".$_r["isincode"]."' ";
    $query .= ",  veld3 = '".$_r["veld3"]."' ";
    $query .= ",  fondsnaam = '".addslashes($_r["fondsnaam"])."' ";
    $query .= ",  aantal = ".$_r["totaal"]." ";
    $db->executeQuery($query);

  }

  // maak van de tijdelijke tabel een CSV bestand
  $query = "SELECT * FROM TMP_571 ORDER BY portefeuille, isincode";
  $db->executeQuery($query);
  $prevISIN = "-1";
  while ($tmpRec = $db->nextRecord())
  {
      $lkp = new AE_lookup();
      $infoRec = $lkp->getAdventInfoByEffectenPositie($tmpRec["veld3"]);
      //listarray($infoRec);
      $data  = $tmpRec["asOf"].',';
      $data .= $tmpRec["portefeuille"].',';
      $data .= $tmpRec["veld3"].',';  //AABcode
      $data .= '"'.$tmpRec["isincode"].'"'.',';
      $data .= '"'.$tmpRec["fondsnaam"].'"'.',';
      $data .= '"'.$infoRec["adventCode"].'"'.',';
      $data .= '"'.$infoRec["adventSecCodeValuta"].'"'.',';
      $data .= $tmpRec["aantal"]."\n";
      fwrite($fh, $data);
  }

  fclose($fh);
  unset($MT571_converted);
}

// aanmaken MT554 cvs file
/*
$filename = $directory.date("Ymd_His")."_effectenMutatie.csv";

if (count($MT554_converted) > 0)
{
  printStatus("schrijven van $filename");
  $fh = @fopen($filename, "w");
  $prb->max = count($MT554_converted);
  $prb->setLabelValue('txt1','csv bestand aanmaken MT554 ('.count($MT554_converted)." records) ");
  $prb->moveMin();
  for ($x=0; $x < count($MT554_converted) ;$x++)
  {
    $prb->moveNext();
    $row = $MT554_converted[$x];
    $row["brutoBedrag"] = ($row["notaBedrag"]          -
                           $row["transactieKosten"]    -
                           $row["kostenCorrespondent"] -
                           $row["gekochteRente"]       -
                           $row["dividendBelasting"]    );


    $dataArray = array($row["transactie-id"],
                       $row["transactiedatum"],
                       $row["rekeningnr"],
                       $row["portefeuille"],
                       $row["transactie-code"],
                       $row["fondsnaam"],
                       $row["aantal"],
                       $row["isincode"],
                       $row["fondsvaluta"],
                       $row["prijsPerStuk"],
                       $row["valutakoers"],
                       $row["valutadatum"],
                       $row["rekeningValuta"],
                       $row["brutoBedrag"],
                       $row["notaBedrag"],
                       $row["transactieKosten"],
                       $row["transactieKostenValuta"],
                       $row["kostenCorrespondent"],
                       $row["kostenCorrespondentValuta"],
                       $row["gekochteRente"],
                       $row["gekochteRenteValuta"],
                       $row["dividendBelasting"],
                       $row["dividendBelastingValuta"]   );
    $data = implode(',',$dataArray)."\n";
    fwrite($fh, $data);

  }
  fclose($fh);
  unset($MT554_converted);
}
*/

printStatus("Einde verwerking");

$prb->hide();

?>
<br />
<br />
<hr />
<a href='advent_filemanager.php' target="content">Ga naar Advent uitvoermap</a>
<hr />
<?

exit();



// lokale functies
////////////////////////////////////////////////

function validateFile($filename)
{
	if (!$handle = @fopen($filename, "r"))  return false;

  $row = 0;
  $fileValid = false;

  while ($data =  fgets($handle, 4096))
  {
    $row++;
		if (trim($data) == "ABNANL2A")
		{
			$fileValid = true;
			break;
		}
		if ($row > 15) break;
  }

  fclose($handle);
  return $fileValid;
}

function MTfileToArray($data)
{
  global $dataSet, $ndx, $_regel, $_prevKey;
  if ($data[0] == " ") $data = substr($data,1);  // als regel begint met spatie deze verwijderen
  switch (trim($data))
  {
   	case "ABNANL2A":
        //cycle
   		break;
   	case "571":
   	case "554":
   	case "940":  //type record
   	  $dataSet[$ndx]["type"] = $data;
 			break;
   	case "-":  // einde record
      $ndx++;
 			break;
  	default:
  	  if (substr($data,0,1) <> ":")
   	  {
   	    $dataSet[$ndx]["txt"] = substr($dataSet[$ndx]["txt"],0,-1)." ".$data;
   	  }
   	  else
   	  {
   	  	$_regel = explode(":",$data);
   	  	$_prevKey = $_regel[1];
   	  	$dataSet[$ndx]["txt"] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	  }
   		break;
   }
}

function printStatus($txt)
{
  global $startMemory;
  echo "<br />".date("H:i:s")." :: ".$txt;
}


function addTMP_571($data)
{
  $db = new DB();
  $values = "'".implode("','",$data)."' ";
  $query = "INSERT INTO TMP_571 (asOf,portefeuille,veld3,veld4,isincode,fondsnaam,aantal) VALUES($values)";
  $db->executeQuery($query);
  unset($db);
}



?>