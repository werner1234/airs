<?php
/* 	
  AE-ICT source module
    Author                 : $Author: cvs $
    Laatste aanpassing     : $Date: 2013/12/16 08:21:00 $
    File Versie            : $Revision: 1.3 $
 		
    $Log: convert_positie_bin_import.php,v $
    Revision 1.3  2013/12/16 08:21:00  cvs
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


$_SESSION["portefeuillesQueries"]       = "";
$_SESSION["rekeningNrsQueries"]         = "";
$_SESSION["beleggingscategorieQueries"] = "";


//listarray($_GET);
//listarray($_SESSION["convert"]);

$tArray = $_SESSION["convert"]["_POST"];


$staticData = array("depotbank"          => "bin",  
                    "vermogensbeheerder" => $tArray["vermogensbeheerder"],
                    "batchid"            => $tArray["batchid"]);


$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar

//

 
$volgnr = 1;
$error = array();
$progressStep = 0;
$file = $tArray["file"];

$csvRegels = count(file($file));
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
//$data = fgetcsv($handle, 1000, ";");  //eerste regel overslaan veldnamen
while ($csvdata = fgetcsv($handle, 1000, ","))
{
	$row++;
 	$pro_step += $pro_multiplier;
 	$prb->moveStep($pro_step);
	if (in_array($row , $skipFoutregels))
 	{
 		$skipped .= "- regel $row overgeslagen<br>";
 		continue; // rest overslaan, lees nieuwe regel
 	}
//listarray($csvdata);
   $num = count($data);
   $val = str_replace(" ","_",$data[6]); // vervang spatie door underscore
   $val = str_replace("-","_",$data[6]); // vervang - door underscore
   $data = $staticData;
   $data["Portefeuille"]      = $csvdata[0];
   $data["datum"]            = ($tArray["manualBoekdatum"] <> "")?$tArray["manualBoekdatum"]:$csvdata[1];
   $data["fondsCode"]         = $csvdata[2];
   $data["fondsCodeNumeriek"] = $csvdata[16];
   $data["fondsSoort"]        = $csvdata[15];
   $data["fondsOmschrijving"] = $csvdata[17];
   $data["fondsValuta"]       = $csvdata[7];
   $data["ISIN"]              = $csvdata[3];
   $data["kostprijs"]         = $csvdata[14];
   $data["koers"]             = $csvdata[8];
   $data["valutakoers"]       = $csvdata[10];
   $data["optieSoort"]        = $csvdata[4];
   $data["aantal"]            = $csvdata[9];
   if (trim($csvdata[17]) == "")
   {
     $data["soort"] = "liquiditeiten";
     $data["waardeInEUR"]       = 0;
     $data["waardeInValuta"]    = $data["aantal"];
     $data["aantal"]            = 0;
     $data["fondsOmschrijving"] = "liquiditeiten :".$data["Portefeuille"].$data["fondsValuta"] ;
   }
   else
   {
     $data["soort"] = "stukken";
     $data["waardeInEUR"]       = $csvdata[12];
     $data["waardeInValuta"]    = $csvdata[13];
   }
      
   //listarray($data);
   if (validate())
   {
     if ($data["soort"] == "liquiditeiten")
     {
       checkAndAddRekening($data);
     }  
     checkAndAddPortefeuille($data);
     $output[] = $data;
   }
  
   
}
fclose($handle);
unlink($file);


if (count($error) > 0)
{
  $prb->hide();
  echo "<h3>Validatiefouten</h3>";
  for ($x=0; $x <count($error); $x++)
  {
    echo "<li>".$error[$x];
  }
}
else
{
//
// plaats output in TijdelijkeRekeningmutaties table
//
$prb->moveStep(0);
$prb->setLabelValue('txt1','Opslaan in tijdelijke tabel');
$pro_step = 0;
$DB = new DB();
reset($output);
for ($ndx=0;$ndx < count($output);$ndx++)
{
  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);

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
  //echo "<PRE>".$_query."</PRE><br />";
  $DB->SQL($_query);
	if (!$DB->Query())
	{
	  echo mysql_error();
	  Echo "<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}
}
$prb->hide();


$_SESSION["portefeuillesQueries"]       = $portefeuillesQueries;
$_SESSION["rekeningNrsQueries"]         = $rekeningNrsQueries;
$_SESSION["beleggingscategorieQueries"] = "";

?>


<b>Klaar met inlezen <br></b>
Records in Binck CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
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
echo template("../".$__appvar["templateRefreshFooter"],$content);


function validate()
{
  global $error, $data, $row;
  //listarray($error);
  //listarray($data);
  //echo $row;
  $DB = new DB();
  
  if ($data["soort"] == "stukken" AND $data["ISIN"] <> "")
  {
    $_isin = trim($data["ISIN"]);
    $query = "SELECT * FROM Fondsen WHERE ISINCode = '".$_isin."' AND Valuta = '".$data["fondsValuta"]."'";
    $DB->SQL($query);
    if (!$fonds = $DB->lookupRecord())
    {
       $error[] = "$row :ISIN icm Valutacode komt niet voor fonds tabel ($_isin / ".$data["fondsValuta"]." )";
       return false;
    }
    else
      return true;
  }
  return true;
}
?>