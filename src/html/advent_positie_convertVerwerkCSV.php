<?
/*
    AE-ICT sourcemodule created 26 okt. 2022
    Author              : Chris van Santen
    Filename            : advent_positie_convertVerwerkCSV.php


*/

include_once('../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");

include_once("../classes/AE_cls_adventExport.php");
include_once("../classes/AE_cls_lookup.php");
include_once("../config/advent_functies.php");

function makeNr($value)
{
  return str_replace(",",".",$value);
}


//listarray($_GET);
$bank = $_GET["bank"];
$version = $_GET["version"];
$skipFoutregels = array();

$exportCash  = new adventExport();
$exportTrans = new adventExport();
$lkp = new AE_lookup();
$DB = new DB();


$exportCash->fieldsPerLine = 6;
$exportTrans->fieldsPerLine = 8;

//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//


$content = array();
$content["style"] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
//echo template("../".$__appvar["templateContentHeader"],$content);


//
// setup van de progressbar
//
$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar


$error = array();
$csvRegels = 1;
$volgnr = 1;

$progressStep = 0;



$file = $_GET["file"];
$regels = count(file($file));
$prb->max = $regels;
$handle = fopen($file, "r");
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
$row=0;
$prb->moveMin();
$prb->setLabelValue('txt1','Converteren records ('.$regels.' records)');


switch (strtolower($bank) )
{
  case "ubsl": // call 8416
    echo "UBS Lux import<br/>";

    while ($data = fgetcsv($handle, 8192, ";"))
    {
      $data = trimFields($data);
//      debug($data);
      $row++;

      if (isNumeric($data[17]) AND (substr($data[20],2,1) == "." AND substr($data[20],5,1) == "."))
      {
        $fileType = "sec";
      }
      else
      {
        $fileType = "cash";
      }
//      debug(
//        array(
//          isNumeric($data[17]),
//          substr($data[20],2,1),
//          substr($data[20],5,1),
//        ),
//        $fileType
//      );
      // bepalen regeltype
      if ($fileType == "cash")
        {
          if ($data[0] == "acc. no.")  // headers overslaan
          {
            continue;
          }


          if ($data[9] != "")  // movementdate mag niet gevuld
          {
            continue;
          }

          $days = _julDag($data[1]);

          if (_julDag($dataSet[$data[0] . $data[3]]["datum"]) < $days)
          {
            $dataSet[$data[0] . $data[3]] = array(
              "datum"    => $data[1],
              "rekening" => (int)$data[0],
              "saldo"    => (float)$data[2],
              "currency" => $data[3]
            );
          }


      }
      else  // stukken
      {

        if ($data[0] == "Acc. Nr.")  // headers overslaan
        {
          continue;
        }
        $data = trimFields($data);
        $portefeuille = $data[0]."001";
        $row++;
        $d = explode(".",$data[20]);
        $datum = dbdate2advent($d[2]."-".$d[1]."-".$d[0]);
        $transRec = $lkp->getAdventInfoByEffectenPositie("{$data[1]}|{$data[3]}|{$data[18]}","UBSL");
//        debug($transRec, "{$data[1]}|{$data[3]}|{$data[18]}");
        $exportTrans->addField(1,$datum);
        $exportTrans->addField(2,$portefeuille);
        $exportTrans->addField(3,$data[18]);
        $exportTrans->addField(4,$data[1]);
        $exportTrans->addField(5,$transRec["Fonds"]);
        $exportTrans->addField(6,$transRec["adventCode"]);
        $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
        $exportTrans->addField(8,makeNr($data[4]));
        $exportTrans->pushBuffer();

      }
    }
    foreach ($dataSet as $data)
    {

      $teller++;
      $d = explode(".", $data["datum"]);
      $datum = dbdate2advent($d[2] . "-" . $d[1] . "-" . $d[0]);
      if (!$rekRec = $lkp->getRekening(array("rekening" => $data["rekening"] . $data["currency"], "depotbank" => "UBSL")))
      {
        $error[] = "USBlux rekening: " . $data["rekening"] . $data["currency"] . " niet gevonden";
      }
      $exportCash->addField(1, $datum);
      $exportCash->addField(2, $data["rekening"] . "001");
      $exportCash->addField(3, $data["rekening"] . "001" . $data["currency"]);
      $exportCash->addField(4, $data["currency"]);
      $exportCash->addField(5, round($data["saldo"], 2));
      $exportCash->addField(6, $rekRec["typeRekening"]);
      $exportCash->pushBuffer();
    }

    fclose($handle);
    unlink($file);
    echo "<hr/>";
    if (count($error)>0)
    {
      echo implode("<br/>", $error);
    }
    else
    {
      echo "Geen fouten gevonden";
    }
    echo "<hr/>";
    $exportCash->makeCsv("cashPosities_UBSLUX_");
    $exportTrans->makeCsv("effectenPosities_UBSLUX_");

    break;
  case "binck":
    echo "Binckbank import<br/>";

    $delimiter = ( $_GET["version"] == 2)?";":",";
    while ($data = fgetcsv($handle, 1000, $delimiter))
    {
    	$row++;

     	$prb->moveNext();
    // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    	 $data = array_reverse($data);
    	 $data[] = "leeg";
    	 $data = array_reverse($data);
       $datum = dbdate2advent($data[2]);
       if ($data[5] == "DIV")
       {
         // DIV overslaan call 6075
         continue;
       }


       if ($_GET["version"] == 2)
       {
//         if ($row == 1)  //header overslaan
//         {
//           continue;
//         }
//debug($data);
         if ($data[16] == '')  // cash
         {
         //  debug($data,"cash");
           if (!$rekRec = $lkp->getRekening(array("rekening"=>$data[1].$data[3],"depotbank"=>"BIN")))
           {
             $error[] = "Binck rekening: ".$data[1].$data[3]." niet gevonden" ;
           }
           $exportCash->addField(1,$datum);
           $exportCash->addField(2,$data[1]);
           $exportCash->addField(3,$data[1].$data[3]);
           $exportCash->addField(4,$data[3]);
           $exportCash->addField(5,round(makeNr($data[10]),2));
           $exportCash->addField(6,$rekRec["typeRekening"]);
           $exportCash->pushBuffer();
         }
         else
         {
           $transRec = $lkp->getAdventInfoByEffectenPositie($data[4]."|".$data[8]."|".$data[17],"BINCK");
           $exportTrans->addField(1,$datum);
           $exportTrans->addField(2,$data[1]);
           $exportTrans->addField(3,$data[17]);
           $exportTrans->addField(4,$data[4]);
           $exportTrans->addField(5,$transRec["Fonds"]);
           $exportTrans->addField(6,$transRec["adventCode"]);
           $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
           $exportTrans->addField(8,makeNr($data[10]));
           $exportTrans->pushBuffer();

         }
       }
       else
       {
         //listarray($data);
         if ($data[17] == "")  // cash
         {
           if (!$rekRec = $lkp->getRekening(array("rekening"=>$data[1].$data[8],"depotbank"=>"BIN")))
           {
             $error[] = "Binck rekening: ".$data[1].$data[8]." niet gevonden" ;
           }
           $exportCash->addField(1,$datum);
           $exportCash->addField(2,$data[1]);
           $exportCash->addField(3,$data[1].$data[8]);
           $exportCash->addField(4,$data[8]);
           $exportCash->addField(5,round(str_replace(",", ".",$data[10]),2));
           $exportCash->addField(6,$rekRec["typeRekening"]);
           $exportCash->pushBuffer();
         }
         else
         {
           $transRec = $lkp->getAdventInfoByEffectenPositie($data[4]."|".$data[8]."|".$data[17],"BINCK");
           //  listarray($transRec);
           $exportTrans->addField(1,$datum);
           $exportTrans->addField(2,$data[1]);
           $exportTrans->addField(3,$data[17]);
           $exportTrans->addField(4,$data[4]);
           $exportTrans->addField(5,$transRec["Fonds"]);
           $exportTrans->addField(6,$transRec["adventCode"]);
           $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
           $exportTrans->addField(8,str_replace(",", ".",$data[10]));
           $exportTrans->pushBuffer();

         }

       }




    }
    fclose($handle);
    unlink($file);
    echo "<hr/>";
    if (count($error)>0)
    {
      echo implode("<br/>", $error);
    }
    else
    {
      echo "Geen fouten gevonden";
    }
    echo "<hr/>";        
    $exportCash->makeCsv("cashPosities_BIN_");
    $exportTrans->makeCsv("effectenPosities_BIN_");
    break;
  case "aab":
    echo "ABN v2 import<br/>";
    $banknaam = "AAB";
    while ($data = fgetcsv($handle, 4096, ";"))
    {
    	$row++;

     	$prb->moveNext();
    // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    	 $data = array_reverse($data);
    	 $data[] = "leeg";
    	 $data = array_reverse($data);
       $dbdate = date("Y-m-d");
       $datum = dbdate2advent($dbdate);
       if (!is_numeric($data[1]) ) continue;



       //

      $data[1] = $data[1] * 1;
//      debug($data);

       if ($data[3] == "")  // cash
        {
          if (!$rekRec = $lkp->getRekening(array("rekening"=>$data[1].$data[4],"depotbank"=>"AAB")))
          {
            $error[] = "AAB rekening: ".$data[1].$data[4]." niet gevonden" ;
          }

          $exportCash->addField(1,$datum);
          $exportCash->addField(2,$rekRec["Portefeuille"]);
          $exportCash->addField(3,$data[1]);
          $exportCash->addField(4,$data[4]);
          $exportCash->addField(5,$data[14]);
          $exportCash->addField(6,$rekRec["typeRekening"]);
          $exportCash->pushBuffer();
        }
        else
        {
//          debug($data);
          $transRec = $lkp->getAdventInfoByEffectenPositie("{$data[17]}|{$data[4]}|{$data[3]}","AAB");
//          debug($transRec,"{$data[17]}|{$data[4]}|{$data[3]}");
          $exportTrans->addField(1,$datum);
          $exportTrans->addField(2,(int)$data[1]);
          $exportTrans->addField(3,$data[3]);
          $exportTrans->addField(4,$data[17]);
          $exportTrans->addField(5,$transRec["Fonds"]);
          $exportTrans->addField(6,$transRec["adventCode"]);
          $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
          $exportTrans->addField(8,$data[12]);
          $exportTrans->pushBuffer();
        }

    }
    fclose($handle);
    unlink($file);
    echo "<hr/>";
    if (count($error)>0)
    {
      echo implode("<br/>", $error);
    }
    else
    {
      echo "Geen fouten gevonden";
    }
    echo "<hr/>";
    $exportCash->makeCsv("cashPosities_AAB_");

    $exportTrans->makeCsv("effectenPosities_AAB_");
    break;
  case "tgb":
    echo "TGB import<br/>";
    $banknaam = "TGB";
    while ($data = fgetcsv($handle, 1000, ";"))
    {
      $row++;

      $prb->moveNext();
      // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
      $data = array_reverse($data);
      $data[] = "leeg";
      $data = array_reverse($data);
      $dbdate = date("Y-m-d");
      $datum = dbdate2advent($dbdate);
      if (!is_numeric($data[1]) ) continue;

      if ($data[17] == "" AND $data[13] == "")  // cash
      {
        if (!$rekRec = $lkp->getRekening(array("rekening"=>$data[1].$data[3],"depotbank"=>"TGB")))
        {
          $error[] = "TGB rekening: ".$data[1].$data[3]." niet gevonden" ;
        }
        //listarray($rekRec);
        $exportCash->addField(1,$datum);
        $exportCash->addField(2,$data[1]);
        $exportCash->addField(3,$data[1].$data[3]);
        $exportCash->addField(4,$data[3]);
        $exportCash->addField(5,$data[8]);
        $exportCash->addField(6,$rekRec["typeRekening"]);
        $exportCash->pushBuffer();
      }
      else
      {
        $transRec = $lkp->getAdventInfoByEffectenPositie($data[17]."|".$data[3]."|".$data[13],"TGB");
        //listarray($data);
        $exportTrans->addField(1,$datum);
        $exportTrans->addField(2,$data[1]);
        $exportTrans->addField(3,$data[13]);
        $exportTrans->addField(4,$data[17]);
        $exportTrans->addField(5,$transRec["Fonds"]);
        $exportTrans->addField(6,$transRec["adventCode"]);
        $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
        $exportTrans->addField(8,$data[8]);
        $exportTrans->pushBuffer();
      }

    }
    fclose($handle);
    unlink($file);
    echo "<hr/>";
    if (count($error)>0)
    {
      echo implode("<br/>", $error);
    }
    else
    {
      echo "Geen fouten gevonden";
    }
    echo "<hr/>";
    $exportCash->makeCsv("cashPosities_TGB_");

    $exportTrans->makeCsv("effectenPosities_TGB_");
    break;
  case "fvl":
    echo "FVL import<br/>";
    $banknaam = "FVL";
    while ($data = fgetcsv($handle, 1000, ";"))
    {
    	$row++;

     	$prb->moveNext();
    // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
    	 $data = array_reverse($data);
    	 $data[] = "leeg";
    	 $data = array_reverse($data);
       $dbdate = date("Y-m-d");
       $datum = dbdate2advent($dbdate);
       if (!is_numeric($data[1]) ) continue;
       if (!$rekRec = $lkp->getRekening(array("rekening"=>$data[1].$data[3],"depotbank"=>"FVL")))
       {
         if ($data[17] == "" AND $data[13] == "")
         {
            $error[] = "FVL rekening: ".$data[1].$data[3]." niet in AIRS gevonden" ;
         } 
       }
       
       $portefeuille = trim($rekRec["Portefeuille"]) <> ""?trim($rekRec["Portefeuille"]):$data[1];       
       $rekening     = trim($rekRec["Rekening"]) <> ""?trim($rekRec["Rekening"]):$data[1].$data[3];
       
       if ($data[17] <> "" OR $data[13] <> "")
       {
         if (!$portRec = $lkp->getPortefeuille(" Portefeuilles.Portefeuille = '".$portefeuille."' OR Portefeuilles.PortefeuilleDepotbank = '".$portefeuille."'"))
         {
           $error[] = "FVL portefeuille: ".$portefeuille." niet in AIRS gevonden" ;
         }   
         else
         {
            $portefeuille = trim($portRec["Portefeuille"]) <> ""?trim($portRec["Portefeuille"]):$data[1];
         }
       }  
       
       
       
       if ($data[17] == "" AND $data[13] == "")  // cash
        {
          
          //listarray($rekRec);
          $exportCash->addField(1,$datum);
          $exportCash->addField(2,$portefeuille);
          $exportCash->addField(3,$rekening);
          $exportCash->addField(4,$data[3]);
          $exportCash->addField(5,$data[8]);
          $exportCash->addField(6,$rekRec["typeRekening"]);
          $exportCash->pushBuffer();
        }
        else
        {
          $transRec = $lkp->getAdventInfoByEffectenPositie($data[17]."|".$data[3]."|".$data[13],"FVL");
          
          $exportTrans->addField(1,$datum);
          $exportTrans->addField(2,$portefeuille);
          $exportTrans->addField(3,$data[13]);
          $exportTrans->addField(4,$data[17]);
          $exportTrans->addField(5,$transRec["Fonds"]);
          $exportTrans->addField(6,$transRec["adventCode"]);
          $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
          $exportTrans->addField(8,$data[8]);
          $exportTrans->pushBuffer();
        }

    }
    fclose($handle);
    unlink($file);
    echo "<hr/>";
    if (count($error)>0)
    {
      echo implode("<br/>", $error);
    }
    else
    {
      echo "Geen fouten gevonden";
    }
    echo "<hr/>";
    $exportCash->makeCsv("cashPosities_FVL_");

    $exportTrans->makeCsv("effectenPosities_FVL_");
    break;
  case "lom":
    echo "LOM import<br/>";
    $banknaam = "LOM";
    while ($data = fgetcsv($handle, 8196, ";"))
    {
      $row++;
      $cash = false;
      $prb->moveNext();
      // BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//      $data = array_reverse($data);
//      $data[] = "leeg";
//      $data = array_reverse($data);
      $dbdate = substr($data[0],0,4)."-".substr($data[0],4,2)."-".substr($data[0],6,2);
      $datum = dbdate2advent($dbdate);


      $portefeuille = trim($data[146]);
      $valuta       = trim($data[62]);
      $rekeninnr    = $portefeuille;

      $isin         = trim($data[37]);
      $bankCode     = trim($data[152]);

      if (!is_numeric($data[0]) ) continue;
//      debug ($data, $row);
      if (substr($data[125],0,16) == "ORDINARY ACCOUNT" OR
          substr($data[125],0,12) == "INCOME TO BE"   )
      {
        $aantal              = trim($data[54]);
        $cash = true;

        if (substr($data[125],0,12) == "INCOME TO BE")
        {
          $reknr = "TBR-".$rekeninnr;
          $bedrag =  $data[237] - $aantal;
        }
        else
        {
          $reknr = $rekeninnr.$valuta;
          $bedrag = $data[237];
        }
        if (!$rekRec = $lkp->getRekening(array("rekening"=>$reknr,"depotbank"=>"LOM")))
        {
          $error[] = "LOM rekening: ".$reknr." niet in AIRS gevonden" ;
        }
        $portefeuille = trim($rekRec["Portefeuille"]) <> ""?trim($rekRec["Portefeuille"]):$data[1];
      }
      else
      {
        if (!$portRec = $lkp->getPortefeuille(" Portefeuilles.Portefeuille = '".$portefeuille."' OR Portefeuilles.PortefeuilleDepotbank = '".$portefeuille."'"))
        {
          $error[] = "LOM portefeuille: ".$portefeuille." niet in AIRS gevonden" ;
        }
        else
        {
          $portefeuille = trim($portRec["Portefeuille"]) <> ""?trim($portRec["Portefeuille"]):$data[146];
        }
      }


      $rekening     = trim($rekRec["Rekening"]) <> ""?trim($rekRec["Rekening"]):$rekeninnr.$valuta;


      if ($cash)  // cash
      {
        //listarray($rekRec);
        $exportCash->addField(1,$datum);
        $exportCash->addField(2,$portefeuille);
        $exportCash->addField(3,$rekening);
        $exportCash->addField(4,$valuta);
        $exportCash->addField(5,$bedrag);
        $exportCash->addField(6,$rekRec["typeRekening"]);
        $exportCash->pushBuffer();
      }
      else
      {
        if ($data[313] > 0)
        {
          $aantal = trim($data[237])/trim($data[313]);  // opties
        }
        else
        {
          $aantal = trim($data[237]);
        }
        $fondsOmschrijving = trim($data[125]);
//        debug("-----------------------------");
        $transRec = $lkp->getAdventInfoByEffectenPositie($isin."|".$valuta."|".$bankCode."|".$fondsOmschrijving,"LOM");
//        debug($isin."|".$valuta."|".$bankCode);
//        debug($transRec);
        $exportTrans->addField(1,$datum);
        $exportTrans->addField(2,$portefeuille);
        $exportTrans->addField(3,$bankCode);
        $exportTrans->addField(4,$isin);
        $exportTrans->addField(5,$transRec["Fonds"]);
        $exportTrans->addField(6,$transRec["adventCode"]);
        $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
        $exportTrans->addField(8,$aantal);
        $exportTrans->pushBuffer();
      }

    }
    fclose($handle);
    unlink($file);
    echo "<hr/>";
    if (count($error)>0)
    {
      echo implode("<br/>", $error);
    }
    else
    {
      echo "Geen fouten gevonden";
    }
    echo "<hr/>";

    $exportCash->useCombineRows = true;
    $exportCash->makeCsv("cashPosities_LOM_");

    $exportTrans->useCombineRows = true;
    $exportTrans->makeCsv("effectenPosities_LOM_");
    break;
  case "saxo":
    echo "Saxobank import<br/>";

    $delimiter = ",";
    while ($data = fgetcsv($handle, 8000, $delimiter))
    {
      $row++;
      $prb->moveNext();
      if (trim($data[0]) == "CounterpartID" OR trim($data[0]) == "")
      {
        continue; // header overslaan
      }

      $datum = dbdate2advent($data[2]);

      if (strtolower($data[23]) == "cash")  // cash
      {
        //  debug($data,"cash");
        if (!$rekRec = $lkp->getRekening(array("rekening"=>$data[0].$data[2],"depotbank"=>"SAXO")))
        {
          $error[] = "Saxo rekening: ".$data[1].$data[3]." niet gevonden" ;
        }
        $exportCash->addField(1,$datum);
        $exportCash->addField(2,$data[0]);
        $exportCash->addField(3,$data[0].$data[2]);
        $exportCash->addField(4,$data[2]);
        $exportCash->addField(5,round(makeNr($data[21]),2));
        $exportCash->addField(6,$rekRec["typeRekening"]);
        $exportCash->pushBuffer();
      }
      else
      {
        $transRec = $lkp->getAdventInfoByEffectenPositie($data[8]."|".$data[12]."|".$data[25],"SAXO");
        $exportTrans->addField(1,$datum);
        $exportTrans->addField(2,$data[0]);
        $exportTrans->addField(3,$data[25]);
        $exportTrans->addField(4,$data[8]);
        $exportTrans->addField(5,$transRec["Fonds"]);
        $exportTrans->addField(6,$transRec["adventCode"]);
        $exportTrans->addField(7,$transRec["adventSecCodeValuta"]);
        $exportTrans->addField(8,makeNr($data[17]));
        $exportTrans->pushBuffer();

      }





    }
    fclose($handle);
    unlink($file);
    echo "<hr/>";
    if (count($error)>0)
    {
      echo implode("<br/>", $error);
    }
    else
    {
      echo "Geen fouten gevonden";
    }
    echo "<hr/>";
    $exportCash->makeCsv("cashPosities_SAXO_");
    $exportTrans->makeCsv("effectenPosities_SAXO_");
    break;
  default:
    echo "ERROR";
    exit;
}

$prb->hide();
?>


<b>Klaar met inlezen <br></b>
Records in <?=$bank?> CSV bestand :<?=$row?><br>
<?=$skipped?>
<hr>
<a target="content" href="advent_filemanager.php">Ga naar Advent uitvoermap</a>
<hr>
<?
//echo template("../".$__appvar["templateRefreshFooter"],$content);
function trimFields($in)
{
  $out = array();
  foreach ($in as $item)
  {
    $out[] = trim($item);
  }
  return $out;
}

function _julDag($datum)
{
  $d = explode(".",$datum);
  $julian = mktime(1,1,1,(int)$d[1], (int) $d[0], (int) $d[2]);
  return floor($julian / 86400);
}