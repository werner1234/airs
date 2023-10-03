<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2012/03/09 09:08:56 $
File Versie					: $Revision: 1.1 $

$Log: portefeuilleAutomaat_verwerk.php,v $
Revision 1.1  2012/03/09 09:08:56  cvs
*** empty log message ***


*/
include_once("wwwvars.php");
include_once("rapport/rapportRekenClass.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('AE_cls_positieBinck.php');
include_once('AE_cls_positieABN.php');

function nf($bedrag)
{
  if (isNumeric($bedrag))
    return number_format($bedrag,4,".","");
  else
    return $bedrag;
}




session_start();
$_SESSION[NAV] = "";
	$prb = new ProgressBar();


$content = array();
$verlopenPortefeuille = array();

//echo template($__appvar["templateContentHeader"],$content);
$outputArray = array();
$DB = new DB();
$DB1 = new DB();
$addRecDb = new DB();
$addCount = 0;
$cfg = new AE_config();
$directory = str_replace("//","\\",$cfg->getData("portAutomaat_importmap"));


$content = array();
$_bank = $_GET["bank"];

if (!$_GET["bank"])
{
  echo "ongeldige aanroep";
  exit;
}


 


$importmap = $directory.$_bank;

if ($handle = opendir("$importmap")) 
{ 
    while ($file = readdir($handle) ) 
    { 
        $files[] = $file;
    } 
    sort($files);
    if (count($files) > 2)  // zijn er bestanden?
    {
      for ($x=0; $x < count($files); $x++) 
      {
        $file = $files[$x];
        if(is_dir($directory."/".$file))
          continue; 
        else 
          $content[] = $file;
       }          
    }
} 

echo "map:".$importmap." ".(is_dir($importmap)?"bestaat":"is ongeldig");

removeBankItems($_bank);

for ($x=0; $x < count($content); $x++)
{
  $batchid = date("Ymd_His");
  $file =  $importmap."/".$content[$x];
  $bestand = $content[$x];
  echo "<br />".date("H:i:s")." -- bestand: ".$bestand.", Batchid: ".$batchid;
  if ($_bank == "aab")
  {
    $abn = new positieABN();
    $abn->bestand = $bestand;
    $abn->batchid = $batchid;
	  if (!$abn->readFile($file))
    {
       $batchError[] = "ABN: kan bestand niet inlezen: $file ";
    }
    else
    {
      //listarray($outputArray);
      addOutputToTable($outputArray);
      //echo "<hr><hr>";
    }
  }
  elseif ($_bank == "bin")
  {
    $bin = new positieBinck();
    
	  if (!$bin->readFile($file))
    {
       $batchError[] = "BINCK: kan bestand niet inlezen: $file ";
    }
    else
    {
     // echo "output";
    //listarray($outputArray);
    addOutputToTable($outputArray);
    //echo "<hr><hr>";
    }
  }
   elseif ($_bank == "rab")
  {
    include_once('controlePortefeuilles_readRaboTransCVSFile.php');
	  if (!readRaboTransCvsFile($file))
    {
       $batchError[] = "RABO: kan bestand niet inlezen: $file ";
    }
    else
    {
      echo "output";
      listarray($outputArray);
    }
  }
   elseif ($_bank == "sns")
  {
    require_once('controlePortefeuilles_readSNSCVSFile.php');
	  if (!readSNSCvsFile($file))
    {
       $batchError[] = "SNS: kan bestand niet inlezen: $file ";
       listarray($error);
    }
    else
    {
      echo "output";
      //listarray($outputArray);
      addOutputToTable($outputArray);
    }
  }
   elseif ($_bank == "tgb")
  {
    ///  == STROEVE !
    include_once('controlePortefeuilles_readCVSFile.php');
	  if (!readCvsFile($file))
    {
       $batchError[] = "TGB: kan bestand niet inlezen: $file ";
    }
    else
    {
      echo "output";
      listarray($outputArray);
    }
  }
  else
  {
    echo "foute bankselectie";
    exit;
  }
  
  
}
/////////////////////////


function addOutputToTable()
{
  global $portefeuilleInCsv,$portefeuilleArray, $outputArray, $_bank, $batchid, $bestand, $verlopenPortefeuille;
  
  $foutCount = 0;
  //listarray($outputArray);
	for ($portIndex=0;$portIndex < count($portefeuilleArray);$portIndex++)
	{
	  $_port = $portefeuilleArray[$portIndex];
	  $A =  $outputArray[$_port]["A"];   // $A is een pointer geen Array!!
	  $B =  $outputArray[$_port]["B"];   // $B is een pointer geen Array!!

    if ($_GET['bank'] == "raboTrans")
    {
       for ($telA=0;$telA < count($A);$telA++)
	    {
        $scrArray[trim($A[$telA][portefeuille])][trim($B[$telA][fonds])]["A"] = nf($A[$telA][aantal]);
        $scrArray[trim($A[$telA][portefeuille])][trim($B[$telA][fonds])]["B"] = nf($B[$telA][aantal]);
	    }
    }
    else
    {
	    for ($telA=0;$telA < count($A);$telA++)
	    {
	      $matchFound = 0;
	      $scrArray[trim($A[$telA][portefeuille])][trim($A[$telA][fonds])]["A"] = nf($A[$telA][aantal]);
	      for ($telB=0;$telB < count($B);$telB++)
  	    {
	        if (  $B[$telB][fonds] == "") continue;
          
	        if (  trim($A[$telA][fonds])        == trim($B[$telB][fonds])             and
	              trim($A[$telA][portefeuille]) == trim($B[$telB][portefeuille])          )
	        {
	           $scrArray[trim($A[$telA][portefeuille])][trim($A[$telA][fonds])]["B"] = nf($B[$telB][aantal]);
  	         $B[$telB][match] = 1;
  	         //echo "**";
	           break;
	        }
	        else
	        {
	          $scrArray[trim($A[$telA][portefeuille])][trim($A[$telA][fonds])]["B"] = "Geen bank info";
	        }
          
	      }
	    }

	    for ($telB=0;$telB < count($B);$telB++) // regels niet gematched toevoegen
  	  {
	      if ($B[$telB][match] == 1) continue;
	      $scrArray[trim($B[$telB][portefeuille])][trim($B[$telB][fonds])]["B"] = nf($B[$telB][aantal]);
	      $scrArray[trim($B[$telB][portefeuille])][trim($B[$telB][fonds])]["A"] = "Geen AIRS info";
  	  }
    }
    
    

	}

  while ($port = each($scrArray))
  {
    $tdPortefeuille = $port[0];
  
	  while ($data = each($port[1]) )
	  {
	  	$tdFonds = $data[0];
    
      // als bank aantal factor 1 tov AIRS
		  if (round($data[1]["A"],2) == round($data[1]["B"],2) )
		  {
		    continue;
		  }
      // als bank aantal factor 0.01 tov AIRS
      if (round($data[1]["A"],2) == round($data[1]["B"] * 100,2) )
		  {
		    continue;
		  }
      // als bank aantal factor 0.001 tov AIRS
      if (round($data[1]["A"],2) == round($data[1]["B"] * 1000,2))
		  {
		    continue;
      }
		  if (($data[1]["A"] == 0 AND stristr($data[1]["B"], "geen bank info")) OR
		      ($data[1]["B"] == 0 AND stristr($data[1]["A"], "geen airs info") ))  //"geen info" met tegenwaarde 0 negeren
      {
		    continue;
		  }

		  $portNum = ereg_replace("[^0-9]","",$tdPortefeuille);
	    if (in_array($portNum, $verlopenPortefeuille))
	    {
	      $sk++;
	      continue;
	    }
      if (in_array($tdPortefeuille, $verlopenPortefeuille))
      {
        
      }
      else
      {
        addRecord(array("bank"         => $_bank,
                        "portefeuille" => $tdPortefeuille,
                        "fonds"        => $tdFonds,
                        "aantal_airs"  => $data[1]["A"],
                        "aantal_bank"  => $data[1]["B"],
                        "file"         => $bestand,
                        "batchid"      => $batchid));
      }                  
    }
  }
  echo $addCount;
  $addCount = 0;
  $portefeuilleArray = array();
  $outputArray = array();
  ///////////////*******************
}


echo "<br>skipped portefeuilleregels $sk<br>";


function removeBankItems($bank)
{
  global $addRecDb, $addCount;
  $query = "DELETE FROM PortefeuilleAutoumaat WHERE bank = '$bank'";
  $addRecDb->executeQuery($query);
}

function addRecord($data)
{
  global $USR, $addRecDb, $addCount;
  $addCount++;
  $query = "INSERT INTO PortefeuilleAutoumaat SET add_date = NOW() ";
  foreach ($data as $key => $value) 
  {
      $query .= ", $key = '".$value."' ";
  }
  $addRecDb->executeQuery($query);
  
}

echo "$addCount records -->klaar";
listarray($batchError);
?>