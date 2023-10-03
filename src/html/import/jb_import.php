  <?
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/06/12 06:55:03 $
 		File Versie					: $Revision: 1.7 $

 		$Log: jb_import.php,v $
 		Revision 1.7  2020/06/12 06:55:03  cvs
 		call 8680
 		
 		Revision 1.6  2018/09/11 14:58:09  cvs
 		call 7130
 		
 		Revision 1.5  2018/07/20 07:28:45  cvs
 		call 7054
 		
 		Revision 1.4  2018/06/15 07:28:15  cvs
 		call 5912
 		
 		Revision 1.3  2018/05/23 13:11:12  cvs
 		call 5912
 		
 		Revision 1.2  2018/05/01 06:13:10  cvs
 		call 5913
 		
 		Revision 1.8  2017/10/16 12:27:15  cvs
 		call 6170
 		
 		Revision 1.7  2017/09/20 06:16:53  cvs
 		call 6115
 		
 		Revision 1.6  2017/02/22 07:40:42  cvs
 		cal 5571
 		
 		Revision 1.5  2016/11/10 07:20:16  cvs
 		call 5402
 		
 		Revision 1.4  2016/10/21 13:49:49  cvs
 		call 3856
 		
 		Revision 1.3  2016/10/21 10:55:49  cvs
 		call 3856
 		
 		Revision 1.2  2016/04/04 14:27:18  cvs
 		no message
 		
 		Revision 1.1  2015/12/01 09:01:53  cvs
 		update 2540, call 4352
 		
 		Revision 1.1  2015/05/06 09:39:32  cvs
 		*** empty log message ***

*/

include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");
include_once("jb_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("JB");

$skipFoutregels = array();
$meldArray = array();

if ($_POST["addRekening"] == "1")
{

  $rac = new rekeningAddStamgegevens($_SESSION["VB"],"JB");
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
$content["style"] = '
  <link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <script type="text/javascript" src="../javascript/jquery-1.11.1.min.js"></script>
  ';

echo template("../".$__appvar["templateContentHeader"],$content);
if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$DB->executeQuery($query);
}
else
{
  $tempRecords = $DB->QRecords("SELECT id FROM TijdelijkeRekeningmutaties WHERE  TijdelijkeRekeningmutaties.change_user = '$USR'");
  if ($tempRecords > 0)
  {
  	echo "<br>
  <br>
  De tabel TijdelijkeRekeningmutaties is niet leeg voor gebruiker ($USR) (".$tempRecords.")<br>
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
include("jb_validate.php");


$volgnr = 1;
$prb->setLabelValue('txt1','Validatie van Lombard CSV bestand');

if ($doIt <> "1")  
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
	<form action="<?=$PHP_SELF?>" method="POST" id="addRekeningForm">
    <div id="kopje"></div>
	  <input type="hidden" name="doIt" value="1">
  	<input type="hidden" name="bestand" value="<?=$file?>">
  	<input type="hidden" name="foutregels" value="<?=$foutregels?>">
  	<select name="action" id="frmAction">
    	<option value="stop">Bestand verwijderen en import afbreken</option>
    	<option value="go">Bestand inlezen en onvolledige regels overslaan</option>
      <option value="retry">Bestand opnieuw inlezen en valideren</option>
  	</select>
    <br/>
<?

    if ( count($_SESSION["rekeningAddArray"]) >0 )
    {

      $rac = new rekeningAddStamgegevens($_SESSION["VB"],"JB");
      $rac->getStyles();

      $rekArray = $_SESSION["rekeningAddArray"];
      for ($rNdx=0; $rNdx < count($rekArray); $rNdx++)
      {
        $rac->makeInputRow($rekArray[$rNdx]);
      }
      echo $rac->getHTML();
    }
?>
    <input type="hidden" name="addRekening" id="addRekening" value="0">
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
  $transactieMapping = array();
  $feeMapping = array();
 $query = "SELECT JBcode,doActie FROM jbTransactieCodes  ";
 $DB->executeQuery($query);
 while ($row = $DB->nextRecord())
 {
   if (substr($row["doActie"],0,4) == "FEE_")
   {
     $feeMapping[substr($row["JBcode"],2)] = substr($row["doActie"],4);
   }
   else
   {
     $transactieMapping[$row["JBcode"]] = $row["doActie"];
   }

 }
//debug($transactieMapping, "transactie mapping");
//debug($feeMapping, "fee mapping");
$row = 0;
$handle = fopen($file, "r");
$pro_multiplier = (100/$csvRegels);
$_tfile = explode("/",$file);
$_file = $_tfile[count($_tfile)-1];
$skipped = "";
while ($data = fgetcsv($handle, 4096, ";"))
{
  $row++;


//  if ($row > 4)
//  {
//    break;
//  }  // eerste 4 records


  $pro_step += $pro_multiplier;
  $prb->moveStep($pro_step);
  if (in_array($row, $skipFoutregels))
  {
    $skipped .= "- regel $row overgeslagen<br>";
    continue; // rest overslaan, lees nieuwe regel
  }

// BEGIN insert leeg veld in $data[0] om veldnummering gelijk te maken aan de documentatie
  $data = array_reverse($data);
  $data[] = "leeg";
  $data = array_reverse($data);
  $data["row"] = $row;

  if ($data[1] == "KBEW" ) // call
  {
    $kosten = array();
    for ($x=58; $x < 78; $x=$x+2 )
    {
      $idx  = $x;
      $code = $data[$x];
      $bedrag = $data[$x+1];
      if (trim($code) != "")
      {
        $kosten[$code] += $bedrag;
      }

    }
    $data["kosten"] = $kosten;
  }
  if ((trim($data[21]) != "" AND $data[1] == "DBEW") OR
      (trim($data[21]) == "1" AND $data[1] == "KBEW") )
  {
    $skipped .= "- regel $row overgeslagen storno {$data[1]}<br>";
    continue; // rest overslaan, lees nieuwe regel
  }

  if ( trim($data[37]) == "" OR
       ($data[16] == "SB" AND $data[1] == "KBEW")  )
  {
    $transId = $data[41];
  }
  else
  {
    $transId = $data[37];
  }

  if ($data[5] == "1220" AND $data[1] == "KBEW")
  {
    $skipped .= "- regel $row overgeslagen KBEW met 1220<br>";
    continue; // rest overslaan, lees nieuwe regel
  }
  else
  {

    if (count($dataRaw[$transId][$data[1]]) > 0 AND $data[1] == "KBEW")
    {
     $dataRaw[$transId."A"][$data[1]] = $data;
    }
    else
    {
      $dataRaw[$transId][$data[1]] = $data;
    }

  }




}
$prb->hide();

// DBEW --> effectent
// KBEW --> cash

foreach ($dataRaw as $k => $data)
{

//  debug($data);
  $transcode = "";

  if (trim($data["KBEW"][29]) == "NTF PREPAYMENT")
  {
    $meldArray[] = $data["DBEW"]["row"].": NTF PREPAYMENT overgeslagen";
    continue;
  }

  if ( (count($data) == 1) AND (count($data["DBEW"]) > 0) )
  {
    $transcode = $data["DBEW"][17];
  }
  elseif ((count($data) == 1) AND (count($data["KBEW"]) > 0) )
  {
    $transcode = $data["KBEW"][16];
  }
  elseif (count($data) == 2)
  {
    $transcode = $data["DBEW"][17];
  }
  else
  {
    //
  }

  $data["KBEW"][15] = is_numeric($data["KBEW"][15])?$data["KBEW"][15]:1; //call 9807


  if ($transcode != "")
  {
    $val = $transactieMapping[$transcode];


    $do_func = "do_$val";

//    $data = $v[$transcode];

    $do_func = "do_$val";
    //debug($do_func);
    if ( function_exists($do_func) )
      call_user_func($do_func);
    else
    {
      $meldArray[] = $row.": transaktieccode ($transcode) niet gevonden";
    }

    //debug($data);
  }


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
    if (checkForDoubleImport($output[$ndx]) AND !$__develop )
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
$prb->hide();
?>


<b>Klaar met inlezen <br></b>
<?
listarray($meldArray);
?>

Records in JB CSV bestand :<?=$row?><br>
aangemaakte mutatieregels : <?=count($output)?><BR>
<?=$skipped?>
<hr>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<hr>
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>