<?php
/* 	
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2013/12/16 08:21:00 $
    File Versie            : $Revision: 1.7 $
 		
    $Log: convert_positie_aab_import.php,v $
    Revision 1.7  2013/12/16 08:21:00  cvs
    *** empty log message ***

    Revision 1.6  2011/12/09 13:58:48  cvs
    *** empty log message ***

    Revision 1.5  2011/12/06 14:07:41  cvs
    eerste spatie verwijderen indien aanwezig

    Revision 1.4  2011/07/21 11:45:51  cvs
    *** empty log message ***

    Revision 1.3  2011/07/19 14:31:58  cvs
    *** empty log message ***

    Revision 1.2  2011/06/22 12:31:31  cvs
    *** empty log message ***

    Revision 1.1  2011/06/22 11:58:29  cvs
    *** empty log message ***

 		
 	
*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("../convert_functies.php");
session_start();

//listarray($_GET);
//listarray($_SESSION["convert"]);

$_SESSION["portefeuillesQueries"]       = "";
$_SESSION["rekeningNrsQueries"]         = "";
$_SESSION["beleggingscategorieQueries"] = "";

$tArray = $_SESSION["convert"]["_POST"];

$file = $tArray["file"];
$staticData = array("depotbank"          => "aab",  
                    "vermogensbeheerder" => $tArray["vermogensbeheerder"],
                    "batchid"            => $tArray["batchid"]);


$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','verwerken importbestand');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar

$volgnr = 1;
$errorArray = array();
$row = 1;
$handle = fopen($file, "r");
$ndx=0;
$dataSet = Array();
$row = Array();
$_tempRow = Array();
$regtel = 0;

//
// lees alle bekende records in array
//

while ($data = fgets($handle, 4096))
{
  if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
	$regtel++;
	$prb->moveNext();
	$skipToNextRecord = false;
  
  if (substr($data,0,1) == "5" AND trim($data) <> "571")
  {
    $skipToNextRecord = true;
  }
  else
  {
    switch (trim($data))
    {
   	  case "ABNANL2A":
        //cycle
   	  	break;
     
      case "01":  //negeer regel
       break;   
     	case "940":  //type record
     	  $skipToNextRecord = true;
   	    break;
   	  case "571":
     	  $skipToNextRecord = false;
     	  $dataSet[$ndx]["type"] = $data;
 		   	break;
     	case "-":  // einde record
     	   if (isset($dataSet[$ndx]["type"])) $ndx++;
         $skipToNextRecord = false;
        
 			  break;
    	default:
    	  if ($skipToNextRecord == true OR !isset($dataSet[$ndx]["type"]))
  	     break;
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
}

$dataSet571 = $dataSet;
$regtel = 0;
fclose($handle);
$handle = fopen($file, "r");
$dataSet = array();
while ($data = fgets($handle, 4096))
{
  if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
	$regtel++;
	$prb->moveNext();
  if (substr($data,0,1) == "5" )
  {
    $skipToNextRecord = true;
  }
  else
  {

  	switch (trim($data))
    {
   	  case "ABNANL2A":
        //cycle
   	  	break;
   	  case "940":  //type record
     	  $t940++;
     	  $skipToNextRecord = false;
   	    $dataSet[$ndx][type] = $data;
 			  break;
   	  case "-":  // einde record
   	    $skipToNextRecord = false;
        $ndx++;
 			  break;
  	 default:
  	    if ($skipToNextRecord == true OR !isset($dataSet[$ndx][type]))
  	      break;
  	   if (substr($data,0,1) <> ":")
   	    {
   	      $dataSet[$ndx][txt] = substr($dataSet[$ndx][txt],0,-1)." ".$data;
   	    }
   	    else
   	    {
   	  	 $_regel = explode(":",$data);
   	  	 $_prevKey = $_regel[1];
   	  	 $dataSet[$ndx][txt] .= $_regel[1]."&&".$_regel[2];  // vul data velden
   	    }
   		 break;
    }
  }  
}

fclose($handle);
unlink($file);
$dataSet940 = $dataSet;
/*
$prb->hide();

echo "<hr>571<hr>";
//listarray($dataSet571);
echo "<hr>940<hr>";
//listarray($dataSet940);
*/
unset($dataSet);
$resultItems = 0;


$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;

if (count($dataSet571) > 0)
{
    $pro_multiplier = 100/count($dataSet571);
    for ($x=0;$x < count($dataSet571);$x++)
    {
     $resultItems += convert_571($dataSet571[$x]["txt"]);
     $pro_step += $pro_multiplier;
     $prb->moveStep($pro_step);
    }  
}

if (count($dataSet940) > 0)
{
  $pro_multiplier = 100/count($dataSet940);
  for ($x=0;$x < count($dataSet940);$x++)
  {
    $resultItems += convert_940($dataSet940[$x]["txt"]);
    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
  }  
}


$prb->hide();

if (count($errorArray) > 0)
{
 ?>
 <h2>validatie foutenoverzicht.</h2>
<?
  for ($x=0; $x < count($errorArray); $x++)
  {
    echo "<br />".($x+1).": ".$errorArray[$x];
  } 
 // listarray($portefeuillesQueries);
 // listarray($rekeningNrsQueries);
 // listarray($beleggingscategorieQueries);
}
else
{
  
  
$_SESSION["portefeuillesQueries"]       = $portefeuillesQueries;
$_SESSION["rekeningNrsQueries"]         = $rekeningNrsQueries;
$_SESSION["beleggingscategorieQueries"] = $beleggingscategorieQueries;
?>



<b>Klaar met inlezen <br></b>
Regels in ABN bestand :<?=$regtel?><br>
aangemaakte mutatieregels : <?=$resultItems?><BR>
<?=$skipped?>
<hr>
<h3>verwerking maakt de volgende rekeningnummers aan</h3>
<?
for ($x=0; $x < count($dryrunRekeningnr); $x++)
{
  echo "<li>".$dryrunRekeningnr[$x];
}
?>
<hr>
<h3>verwerking maakt de volgende porteuilles aan</h3>
<?
for ($x=0; $x < count($dryrunPorteuille); $x++)
{
  echo "<li>".$dryrunPorteuille[$x];
}
?>

<hr>
<a href="../tijdelijkepositielijstList.php">Ga naar tijdelijk conversiebestand</a>
<hr>
	
<?
}
exit;


function convert_940($_data)
{ 
  global $staticData;
    
  $tel = -1;
  $dataRows = explode("\n", $_data);
  
  for ($x=0; $x < count($dataRows);$x++)
  {
    $row = unpackRow($dataRows[$x]);
    if ($row["25"] <> "") // aanlooprecord
    {
      $valuta = "";
      $portefeuille  = trim($row["25"]);
    }
    elseif ($row["62F"] <> "")
    {
      $valuta  = substr($row["62F"],7,3);
      $sign    = (substr($row["62F"],0,1) == "D")?-1:1;
      $bedrag  = cnvBedrag(substr($row["62F"],10)) * $sign;
      $datum   = "20".substr($row["62F"],1,2)."-".substr($row["62F"],3,2)."-".substr($row["62F"],5,2);
    }
    if ($portefeuille <> "" AND $valuta <> "")
    {
      
      $tel++;
      $data[$tel] = $staticData;
      $data[$tel]["Portefeuille"]      = $portefeuille;
      $data[$tel]["datum"]             = $datum;
      $data[$tel]["soort"]             = "liquiditeiten";
      $data[$tel]["fondsOmschrijving"] = "liquiditeiten :".$portefeuille.$valuta;
      $data[$tel]["fondsValuta"]       = $valuta;
      $data[$tel]["waardeInValuta"]    = $bedrag;
      $data[$tel]["waardeInEUR"]       = ($valuta == "EUR")?$bedrag:0;
      checkAndAddRekening($data[$tel]);
      $valuta       = "";
      $portefeuille = "";
    }
  
  } 
  
  writeToTemp($data);
  return count($data);
}

function convert_571($_data)
{
  $dbISIN = new DB();
  global $staticData, $tArray, $errorArray;
  $tel = -1;
  $dataRows = explode("\n", $_data);
  
  for ($x=0; $x < count($dataRows);$x++)
  {
    
    $row = unpackRow($dataRows[$x]);

    if ($row["83a"] <> "") // aanlooprecord
    {
      $Portefeuille  = intval($row["83a"]);
    }
    elseif ($row["67A"] <> "")
    {  
      $datum         = "20".substr($row["67A"],0,2)."-".substr($row["67A"],2,2)."-".substr($row["67A"],4,2);
      $soort         = "stukken";
    }
    elseif ($row["35H"] <> "")
    {
      $tel++;
      $data[$tel] = $staticData;
      $data[$tel]["Portefeuille"]      = $Portefeuille;
      $data[$tel]["datum"]             = $datum;
      $data[$tel]["soort"]             = $soort;
      $data[$tel]["aantal"]            = getNumValue($row["35H"],3);
      $data[$tel]["fondsSoort"]        = substr($row["35H"],0,3);
    }
    elseif ($row["35B"])
    {  
      $data[$tel]["fondsCode"]         = trim(substr($row["35B"],5,6));
      $data[$tel]["fondsOmschrijving"] = trim(substr($row["35B"],12,40));
      if ($data[$tel]["fondsCode"] <> "")
      {
        //$qISIN = "SELECT Fonds,ISINCode FROM Fondsen WHERE AABCode = '".$data[$tel]["fondsCode"]."' ";
        $qISIN = "SELECT * FROM Fondsen WHERE AABCode = '".$data[$tel]["fondsCode"]."' OR ABRCode = '".$data[$tel]["fondsCode"]."' ";
        $dbISIN->SQL($qISIN);
        $dbISIN->Query();
        if ($ISINRec = $dbISIN->lookupRecord())
        {
          $isin = $ISINRec["ISINCode"];
          CheckAndAddBeleggingscategoriePerFonds($ISINRec["Fonds"], $tArray["vermogensbeheerder"] );
          
          if (trim($isin) <> "")
            $data[$tel]["ISIN"] = $isin;
          else
            $errorArray[]="Geen ISIN gevonden bij AABcode ".$data[$tel]["fondsCode"]." (".$data[$tel]["fondsOmschrijving"].")";  
        }
        else
        {
          $errorArray[]="Geen fonds gevonden bij AABcode ".$data[$tel]["fondsCode"]." (".$data[$tel]["fondsOmschrijving"].")";  
        }  
      }
      
    }
    elseif ($row["33B"])
    {  
      $data[$tel]["fondsValuta"]       = substr($row["33B"],0,3);
      
      if (strtoupper($data[$tel]["fondsValuta"]) == "PCT")  // obligaties fondsvaluta uit fondstabel ophalen
      {
        $qISIN = "SELECT Valuta FROM Fondsen WHERE AABCode = '".$data[$tel]["fondsCode"]."' ";
        $dbISIN->SQL($qISIN);
        $dbISIN->Query();
        if ($ISINRec = $dbISIN->lookupRecord())
        {
          $data[$tel]["fondsValuta"] = $ISINRec["Valuta"];
        }
      }
      
      $data[$tel]["koers"]             = getNumValue($row["33B"],3);
    }
    elseif ($row["32H"])
    {  
      $data[$tel]["waardeInEUR"]       = getNumValue($row["32H"],3);
    }
    
  }
  writeToTemp($data);
  return count($data);
  
}  
  
   
 
function writeToTemp($output)
{
  global $USR;
  
  $DB = new DB();
  
  for ($ndx=0;$ndx < count($output);$ndx++)
  {
	 $_query = "INSERT INTO TijdelijkePositieLijst SET \n";
	 $sep = " ";
	 while (list($key, $value) = each($output[$ndx]))
	 {
	   if ($manualBoekdatum AND $key == "Boekdatum")
	   { 
	     $value = $manualBoekdatum;
	   }

     $_query .= "$sep TijdelijkePositieLijst.$key = '".mysql_escape_string($value)."'\n";
     $sep = ",";
	 }
   $_query .= ", add_date = NOW() \n";
   $_query .= ", add_user = '".$USR."' \n";
	 $_query .= ", change_date = NOW() \n";
   $_query .= ", change_user = '".$USR."' \n";
  // echo "<PRE>".$_query."</PRE><br />";
    $DB->SQL($_query);
	  if (!$DB->Query())
	  {
	    echo mysql_error();
	    Echo "<br> FOUT bij het wegschrijven naar de database!";
	    exit();
	  }
  } 
} 

function getNumValue($data, $offset=3)
{
  $out = "";
  for($xx=$offset;$xx < strlen($data);$xx++)
  {
    $_l = 	substr($data,$xx,1);
    if ($_l >= "0" AND $_l <= "9")
      $out .= $_l;
    elseif ($_l == ",")
      $out .= ".";
  }
  return $out;
}

function unpackRow($rowItems)
{
  
   $items = explode("&&", $rowItems);
   $out[$items[0]] = $items[1];
 
  return $out;
}

function cnvBedrag($txt)
{
	return str_replace(',','.',$txt);
}


?>