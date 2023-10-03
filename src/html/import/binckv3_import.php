<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/01/14 12:58:04 $
 		File Versie					: $Revision: 1.24 $

 		$Log: binckv3_import.php,v $
 		Revision 1.24  2020/01/14 12:58:04  cvs
 		call 7993
 		
 		Revision 1.23  2020/01/14 11:56:19  cvs
 		call 6223
 		
 		Revision 1.22  2019/12/16 11:22:12  cvs
 		call 8306
 		
 		Revision 1.21  2019/12/16 10:59:05  cvs
 		binck fondskoersen
 		
 		Revision 1.20  2019/06/05 11:22:23  cvs
 		call 7851
 		
 		Revision 1.19  2019/04/10 12:45:50  cvs
 		call 7701
 		
 		Revision 1.18  2018/10/02 10:21:52  cvs
 		call 7202
 		
 		Revision 1.17  2018/07/03 06:55:00  cvs
 		no message
 		
 		Revision 1.16  2017/11/02 10:21:08  cvs
 		call 6315
 		
 		Revision 1.15  2017/10/02 13:10:35  cvs
 		call 5477, terugdraaien binckcode mapping
 		
 		Revision 1.14  2017/09/29 12:18:21  cvs
 		call 6223
 		
 		Revision 1.13  2017/09/20 06:16:18  cvs
 		megaupdate
 		
 		Revision 1.12  2015/05/06 09:42:14  cvs
 		*** empty log message ***
 		
 		Revision 1.11  2015/01/05 14:45:36  cvs
 		*** empty log message ***
 		
 		Revision 1.10  2014/12/24 12:15:17  cvs
 		dbs 3332 en 3335
 		
 		Revision 1.9  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.8  2014/10/13 11:42:06  cvs
 		call 3116
 		
 		Revision 1.7  2014/09/12 14:47:35  cvs
 		dbs 2833
 		
 		Revision 1.6  2014/07/10 06:53:14  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2014/03/12 10:02:54  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/02/10 10:56:21  cvs
 		*** empty log message ***

 		Revision 1.3  2014/02/08 09:04:33  cvs
 		*** empty log message ***

 		Revision 1.2  2014/01/25 13:12:23  cvs
 		*** empty log message ***

 		Revision 1.1  2013/10/10 14:13:04  cvs
 		*** empty log message ***




*/


include_once('../../classes/AE_cls_progressbar.php');

include_once("wwwvars.php");
include_once("checkForDoubleImport.php");

include_once ("algemeneImportFuncties.php");

include_once("binckv3_functies.php");

// call 4857 aanpassing start
include_once "../../classes/AIRS_import_afwijkingen.php";
include_once "../../classes/AE_cls_fondskoers.php";
$afw = new AIRS_import_afwijkingen("BIN");
$fndkrs = new AE_cls_fondskoers();
$uitkArray = array();
$toekArray = array();
$optieArray = Array("PUT", "CALL", "FUT");

//$afw->initModule();
//$afw->setVerbose();
//$afw->disable();
// call 4857 aanpassing stop

//$__skipDouble = true;
//error_reporting(E_ALL);

function makeNr($value)
{
  return str_replace(",",".",$value);
}

$skipFoutregels = array();
$meldArray = array();

if ($_POST["addRekening"] == "1")
{

  $rac = new rekeningAddStamgegevens($_SESSION["VB"],"BIN");
  $rac->addRekeningen($_POST);
  $doIt = 0;
  $file = $bestand;
}

if ($doIt == "1")  // validatie mislukt, wat te doen?
{
  switch ($action)
  {
    case "stop":
      echo "<br>Het transactiebestand is verwijderd en de import is afgebroken";
    	if (file_exists($bestand) ) unlink($bestand);
		  exit();
      break;
    case "retry":
      $doIt = 0;
      $file = $bestand;
      break;
    default: 
      $skipFoutregels = explode(",",$foutregels);
		  array_shift($skipFoutregels);  // verwijder eerste lege key
		  $file = $bestand; 
  }
}


//
// check of er records in de TijdelijkeRekeningmutaties tabel zitten
//
$DB = new DB();

$rekeningAddArray = array();

$content = array();
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <script type="text/javascript" src="../javascript/jquery-1.11.1.min.js"></script>';
echo template("../".$__appvar["templateContentHeader"],$content);
if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}
else
{
  if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
  {
  	echo "<br>
  <br>
  De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties").")<br>
  <br>
  de import is geannuleerd ";
  	exit;
  }
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
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar


$csvRegels = 1;
include("binckv3_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Binck V3 CSV bestand');

if ($doIt <> "1")  // validatie is al gebeurd dus skippen
{
	if (!validateCvsFile($file))
	{
		$prb->hide();
?>
  	<table cellpadding="0" cellspacing="0">
  	<tr>
    	<td colspan="2" bgcolor="#BBBBBB">
     	 Foutmelding bij validatie van CSV bestand<br>
     	 Bestandsnaam :<?=$file?>
    	</td>
  	</tr>
<?
	$foutregels = "";
	$_vsp = "";

	for ($x=0;$x < count($error);$x++)
	{
		$_spA = explode(":",$error[$x]);
		$_sp = trim($_spA[0]);
		if ( $_vsp <> $_sp )
		$foutregels .= ",".$_sp;
		$_vsp = $_sp;
?>
  	<tr>
    	<td bgcolor="#BBBBBB"><?=$x?></td>
    	<td>&nbsp;&nbsp;
	      <?=$error[$x];?>
  	  </td>
  	</tr>

<?

	}


?>
	</table>
<?php

    if (count($_SESSION["importFoutFile"]) > 0)
    {
      echo "<br/><a href='wwwFoutenBestand.php?bank=BINCK' ><button id='btnDownload'>Download FOUTEN bestand</button></a><br/>";
    }

?>

  <br>
	<br>
	<b>Vervolg aktie?</b>
  <form action="<?=$PHP_SELF?>" method="POST" id="addRekeningForm">
    <div id="kopje"></div>
     <select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
    	<option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
<?

    if ( count($_SESSION["rekeningAddArray"]) >0 )
    {

      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"BIN");
      $rac->getStyles();

      $rekArray = $_SESSION["rekeningAddArray"];
      for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
      {
         $rac->makeInputRow($rekArray[$rNdx]);
      }
      echo $rac->getHTML();
    }



?>
   
	  <input type="hidden" name="doIt" id="doIt" value="1">
	  <input type="hidden" name="addRekening" id="addRekening" value="0">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<br/>
    <button id="btnSubmit"> Uitvoeren </button>
	</form>

  <script>

<?
    if ( count($_SESSION["rekeningAddArray"]) >0 )
    {
      echo $rac->getJS();
    }
?>

  </script>
<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');



$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";

$omwlTel = 1;
$row = 0;
while ($data = fgetcsv($handle, 1000, ";"))
{
	$row++;
  $data = str_replace("\xEF\xBB\xBF", "", $data);  // remove BOM
  if (trim($data[0]) == "")
  {
    continue;  // regel overslaan als eerste kolom is leeg
  }
 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
	 $data = array_reverse($data);
	 $data[] = "leeg";
	 $data = array_reverse($data);
   $data[99] = $row;
  // listarray($data);
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
//   echo print_r($data);
//   exit();

/*
** 15 aug 2007 cvs
** hieronder aanpassingen aan de array om de nieuwe structuur van de cvs file aan te passen naar de oude standaard
*/


/**************************************
 1    Account Number
 2    Account Type
 3    Account Currency
 4    Book Date
 5    Transaction Time
 6    Reverse Transaction
 7    Transaction Type Code
 8    Exchange Rate
 9    Transaction Currency
 10   Quantity
 11   Price
 12   Invoice Amount
 13   Brokerage Fees
 14   Tax
 15   Interest
 16   Value Date settlement
 17   Transaction Number
 18   ISIN Code
 19   Symbol
 20   Subtype
 21   Expiration Date
 22   Exercise Price
 23   Instrument Type Code
 24   Undefined
 25   Undefined
 26   Undefined
 27   Undefined
 28   Transaction Date
 29   Instrument Type
 30   Binck ID
 31   Instrument Name
 32   Deposit Value
 33   Exchange Code
 34   Other Transaction
 35   Reference Code
 36   Market costs
 37   Line number
 38
 39
 40
***************************************/
  $t7 = $data[7];
  $data[7]  = $data[6];
  $data[6]  = $t7;
  $data[4]  = str_replace("/","",$data[4]);
  $data[40] = str_replace("/","",$data[16]);  //settlementdatum
  $data[8] = makeNr($data[8]);
  $data[10] = makeNr($data[10]);
  $data[11] = makeNr($data[11]);
  $data[12] = makeNr($data[12]);
  $data[13] = makeNr($data[13]);
  $data[14] = makeNr($data[14]);
  $data[15] = makeNr($data[15]);
  $data[16] = makeNr($data[34]);  // was in V2 veld 16, in V3 veld 34
  $data[19] = $data[18];          // isin code verhuisd naar veld 18
//  $data[32] = $data[31];          // Binckcode verhuisd naar veld 30

  $data[36] = makeNr($data[36]);
  $data[97] = "-";                // virtueel veld voor stoploss turbo's
  $data[98] = "-";                // virtueel veld om onderscheid te maken bij AS_C en AS_P


  if ($data[6] == "OMWL")  // call 9552
  {
    if ($data[2] == "1000" )
    {
      continue;
    }
    $ident =  $data[17].$data[1].$omwlTel;
    $omwlTel++;
  }
  else
  {
    $ident =  $data[17].$data[1];
  }


  if(stristr($data[24],"stoploss") OR  stristr($data[24],"stop loss"))
  {
    $data[97] = "stoploss";
  }  
  
  if (
        ($data[2] == "100" OR ( $data[2] == "1000" AND strtolower($data[29]) == "indexopties") )
      AND 
        $data[6] == "AS C" AND $data[18] == ""
      )
  {
    $data[98] = "C";
    $ident .="C";
  }  
  if (
       ($data[2] == "100" OR ( $data[2] == "1000" AND strtolower($data[29]) == "indexopties") ) 
      AND 
        $data[6] == "AS P" AND $data[18] == ""
      )
  {
    $ident .="P";
    $data[98] = "P";
  }  
  if (
       ($data[2] == "100" OR ( $data[2] == "1000" AND strtolower($data[29]) == "indexopties") ) 
      AND 
        $data[6] == "EX C" AND $data[18] == ""
      )
  {
    $data[98] = "C";
    $ident .="C";
  }  
  if (
       ($data[2] == "100" OR( $data[2] == "1000" AND strtolower($data[29]) == "indexopties") ) 
      AND 
        $data[6] == "EX P" AND $data[18] == ""
      )
  {
    $ident .="P";
    $data[98] = "P";
  }
  
  $rawD[$ident][$data[2]] = $data;
  
}


ksort($rawD);
//listarray($rawD, "RAWD");
//debug($rawD);
getUITKdata();
getTOEKdata();


foreach ($rawD as $dataset)
{

  ksort($dataset);

  $subIndx = count($dataset[1000]) > 0?1000:1010;
  //debug($dataset, $subIndx);
  if ($dataset[100][2] == "100")
  {
    $dataset[100][12] = $dataset[$subIndx][12];
    $dataset[100][99] += $dataset[$subIndx][99]*10000;
    $dataOut[] = $dataset[100];
  }
  else
  {
    foreach ($dataset as $sData)
    {
      $dataOut[] = $sData;
    }
  }

}
//listarray($dataOut);
/*
** 15 aug 2007 cvs
** einde aanpassing
*/
//la($dataOut);
fclose($handle);


for($e=0; $e < count($dataOut); $e++)
{
  $data = $dataOut[$e];
//  debug($data);
  //la($data);


//   if ($data[19] <> "")
//   {
//   	 $_isin = trim($data[19]);
//     if ($data[6] == "TOEK" OR $data[6] == "UITK")
//     {
//       $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$_isin."' ";
//     }
//     else
//     {
//       $valutaCode = ($data[9] == "PNC")?"GBP":$data[9];
//       $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$_isin."' AND Valuta = '".$valutaCode."' ";
//     }
//
//     $DB->SQL($query);
//     $fonds = $DB->lookupRecord();
//   }
//   else
//   {
//     $_binckCode = trim($data[32]);
//     $query = "SELECT * FROM Fondsen WHERE binckCode = '".$_binckCode."' ";
//
//     $DB->SQL($query);
//     $fonds = $DB->lookupRecord();
//   }

  $fonds = getFonds($data);


   $num = count($data);
   $val = str_replace(" ","_",$data[6]); // vervang spatie door underscore
   $val = str_replace("-","_",$val); // vervang - door underscore
   if ($data[6] == "OMWL")  // call 5992
   {

     if ($data[10] > 0)
     {

       $do_func = "do_D";
     }
     else
     {

       $do_func = "do_L";
     }
   }
   elseif ( $fonds["fondssoort"] == "OPT" AND
            ($data[6] == "D" OR $data[6] == "L") AND
            $data[24] == "Expiratie") // call 9476
   { // richting op basis van huidige positie
     $mr = array(
     "Rekening" => getRekening(trim($data[1])."MEM"),
     "Fonds" => $fonds["Fonds"]
     );
     $pos = getPositionByFonds($mr);
     if ($pos > 0)
     {
       $do_func = "do_L";
     }
     else
     {
       $do_func = "do_D";
     }

   }
   else
   {
     $do_func = "do_$val";
   }



   //debug($data, $do_func);
   //la($do_func,"functie aanroep");
   if ( function_exists($do_func) )
     call_user_func($do_func);
   else
     $skipped .= "- transaktie $data[6] overgeslagen<br>";
//listarray($data);

}


//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;

reset($output);
for ($ndx=0;$ndx < count($output);$ndx++)
{
  if ($ndx == 0)
  {
     if (checkForDoubleImport($output[$ndx]))
     {
       $prb->hide();
       Echo "<br> FOUT: De eerste transactieregel komt exact overeen met reeds aanwezige informatie";
       listarray($output[$ndx]);
	     exit();
     }  
   }
   $pro_step += $pro_multiplier;
   $prb->moveStep($pro_step);

	$_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
	$sep = " ";
	while (list($key, $value) = each($output[$ndx]))
	{
	  if ($manualBoekdatum AND $key == "Boekdatum")
	  {
	    $value = $manualBoekdatum;
	  }

   $_query .= "$sep TijdelijkeRekeningmutaties.$key = '".mysql_escape_string($value)."'
";
   $sep = ",";
	}
  $_query .= ", add_date = NOW()";
  $_query .= ", add_user = '".$USR."'";
	$_query .= ", change_date = NOW()";
  $_query .= ", change_user = '".$USR."'";
  $DB->SQL($_query);
	if (!$DB->Query())
	{
	  echo mysql_error();
	  Echo "<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}
}
$prb->hide();

//debug($fndkrs->fondsData);



include_once "verschillenLijst.html";


//echo $fndkrs->showNotInAirs(true);
//echo $fndkrs->js();

?>



Records in Binck CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<a href="<?=$_SERVER["REQUEST_URI"]."&retry=1"?>">Nogmaals inlezen</a>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>