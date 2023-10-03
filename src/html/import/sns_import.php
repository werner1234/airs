<?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2014/07/10 06:53:14 $
 		File Versie					: $Revision: 1.7 $

 		$Log: sns_import.php,v $
 		Revision 1.7  2014/07/10 06:53:14  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2009/04/09 11:19:14  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2009/03/31 08:45:19  cvs
 		*** empty log message ***

 		Revision 1.4  2008/12/22 16:07:20  cvs
 		verdichten transactieregels

 		Revision 1.3  2008/10/01 07:48:06  cvs
 		nieuwe commit 1-10-2008

 		Revision 1.2  2008/05/29 15:31:19  cvs
 		diverse tweaks op aanwijzing van Theo

 		Revision 1.1  2008/05/27 15:21:07  cvs
 		*** empty log message ***

 		Revision 1.14  2007/08/15 07:14:42  cvs
 		omzetten naar nieuwe indeling van CSV bestand

 		Revision 1.13  2005/12/19 16:27:14  cvs
 		*** empty log message ***

 		Revision 1.12  2005/12/16 15:56:11  jwellner
 		no message

 		Revision 1.11  2005/11/09 10:15:59  cvs
 		overrule datum

 		Revision 1.10  2005/09/27 15:02:14  cvs
 		debugmelding verwijderd

 		Revision 1.9  2005/09/27 14:57:45  cvs
 		controle dubbel inlezen

 		Revision 1.8  2005/09/21 09:04:21  cvs
 		setlocale weggehaald




*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("sns_functies.php");

$skipFoutregels = array();


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

$content = array();
$content[style] = '<link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">';
echo template("../".$__appvar["templateContentHeader"],$content);
if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}
else
{
  if ($DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ") > 0)
  {
  	echo "<br>
  <br>
  De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR' ").")<br>
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
include("sns_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van SNS CSV bestand');

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
	<br>
	<br>
	<b>Vervolg aktie?</b>
	<form action="<?=$PHP_SELF?>">
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
      <option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
  	<input type="submit" value=" Uitvoeren">
	</form>

<?
	exit();
	}
}



$progressStep = 0;
$prb->setLabelValue('txt1','Converteren records ('.$csvRegels.' records)');


$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";

$transactieCodes = array('1'  => 'A',
                         '2'  => 'V',
                         '3'  => 'D',
                         '4'  => 'L',
                         '7'  => 'A',
                         '8'  => 'DV_R',
                         '9'  => 'V',
                         '11' => 'A_S',
                         '12' => 'V_O',
                         '18' => 'A',
                         '24' => 'L');

while ($data = fgetcsv($handle, 1000, ";"))
{
	$row++;

  $data = cleanRow($data);

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
// EINDE insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie

  if(strlen($data[1])== 16) //Transacties
  {
    if(isNumeric($data[3]))
      $portefeuille = intval($data[3]);

    if ($data[9])
    {
      $_snscode = $data[9];
      $query = "SELECT * FROM Fondsen WHERE SNSCode = '".$_snscode."' ";
      $DB->SQL($query);
      $fonds = $DB->lookupRecord();
    }

    $transactieCode = $data[4];
    $val = $transactieCodes[$transactieCode];
  }
  elseif (strlen($data[1]) == 8) //Mutaties
  {
    if(isNumeric($data[2]))
      $portefeuille = intval($data[2]);

    $val = 'Mutatie';
  }

  $do_func = "do_$val";

  if ( function_exists($do_func) )
    call_user_func($do_func);
  else
    $skipped .= "- transaktie ".$data[1]." ".$data[5]." overgeslagen<br>";

 // echo $skipped;
 //exit;

}
fclose($handle);

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
/* start samenvoegen deelorders van transactieregels
** 22 dec 2008 cvs
*/
$_query = "SELECT * FROM TijdelijkeRekeningmutaties ORDER BY Rekening, Omschrijving ,Transactietype, Grootboekrekening";
$db2 = new DB();
$DB->SQL($_query);
$DB->Query();
$vorigRec = array();
$DB->records();
$telverdicht = 0;
if ($DB->records() > 0)
{

  while ($rec = $DB->nextRecord())
  {
    $telverdicht++;
    if ( $vorigRec['Rekening']           == $rec['Rekening']  AND
         $vorigRec['Omschrijving']       == $rec['Omschrijving']  AND
         $vorigRec['Transactietype']     == $rec['Transactietype']  AND
         $vorigRec['Grootboekrekening']  == $rec['Grootboekrekening'] )
    {
     if ($rec["Aantal"] == 0)  // kosten
     {
       $tempRec = $vorigRec;
       $tempRec["Debet"] = $vorigRec["Debet"]+ $rec["Debet"];
       $tempRec["Credit"] = $vorigRec["Credit"]+ $rec["Credit"];
       $tempRec["Bedrag"] = $vorigRec["Bedrag"]+ $rec["Bedrag"];

       $_updateQ = "UPDATE TijdelijkeRekeningmutaties SET Debet = ".$tempRec["Debet"].", Credit = ".$tempRec["Credit"].", Bedrag = ".$tempRec["Bedrag"]." WHERE id = ".$vorigRec["id"];
       $db2->SQL($_updateQ);
       $db2->query();
       $telverdicht--;
       $_updateQ = "DELETE FROM TijdelijkeRekeningmutaties WHERE id = ".$rec["id"];
       $db2->SQL($_updateQ);
       $db2->query();
       $vorigRec = $tempRec;

     }
     else
     {
       $tempRec = $vorigRec;
       $tempRec['Aantal']     = $vorigRec['Aantal'] + $rec['Aantal'];
       $tempRec['Debet']      = $vorigRec['Debet']  + $rec['Debet'];
       $tempRec['Credit']     = $vorigRec['Credit'] + $rec['Credit'];
       $tempRec['Bedrag']     = $vorigRec['Bedrag'] + $rec['Bedrag'];
       $tempRec['Fondskoers'] = abs($tempRec['Debet']  + $tempRec['Credit'])/$tempRec['Aantal'];
       $tempRec['Valutakoers']= abs($tempRec["Bedrag"]) /($tempRec['Debet'] + $tempRec['Credit']);
       $_updateQ = "
       UPDATE TijdelijkeRekeningmutaties SET
         Aantal = ".$tempRec["Aantal"].",
         Debet = ".$tempRec["Debet"].",
         Credit = ".$tempRec["Credit"].",
         Bedrag = ".$tempRec["Bedrag"].",
         Fondskoers =".$tempRec["Fondskoers"].",
         Valutakoers =".$tempRec['Valutakoers']."
       WHERE id = ".$vorigRec["id"];
       $db2->SQL($_updateQ);
       $db2->query();
       $telverdicht--;
       $_updateQ = "DELETE FROM TijdelijkeRekeningmutaties WHERE id = ".$rec["id"];
       $db2->SQL($_updateQ);
       $db2->query();
       $vorigRec = $tempRec;
     }

    }
    else
      $vorigRec = $rec;

  }
}
$prb->hide();
?>


<b>Klaar met inlezen <br></b>
Records in SNS CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
verdicht tot <?=$telverdicht?> regels<br>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>