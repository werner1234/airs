<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/06/27 14:10:48 $
 		File Versie					: $Revision: 1.5 $

 		$Log: ubs_import.php,v $
 		Revision 1.5  2018/06/27 14:10:48  cvs
 		cal 6765
 		
 		naar RVV 20201104
 		

*/

//mnt/importdata/UBS
include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");

include_once("ubs_functies.php");

include_once "../../classes/AIRS_import_afwijkingen.php";
$afw = new AIRS_import_afwijkingen("UBS");


$__debug = true;
//debug($_GET,"get",true);
//exit;
$path = $__ubsImportMap."/".$_GET["UBSmap"]."/";
$markFile = "_done_.txt";

if (!$__develop)
{

  if (file_exists($path.$markFile))
  {
    echo "<h3>map is gemarkeerd als al ingelezen. <br/>verwijder bestand <b>".$markFile."</b> om de map alsnog in te lezen</h3>";
    echo file_get_contents($path.$markFile);
    exit;
  }
  else
  {  
    file_put_contents($path.$markFile, "map ingelezen d.d. ".date("d-m-Y H:i:s")." door ".$USR);
  }
  if (!file_exists($path."/verwerkt"))
  {
    mkdir($path."/verwerkt");
  }
}

$content = array();
$content[style] = '
  <link href="../style/workspace.css" rel="stylesheet" type="text/css" media="screen">
  <link href="../style/simbisBase.css" rel="stylesheet" type="text/css" media="screen">
  ';
echo template("../".$__appvar["templateContentHeader"],$content);

$statsArray = array();

$prb = new ProgressBar();	// create new ProgressBar
$prb->pedding = 2;	// Bar Pedding
$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
$prb->setFrame();          	                // set ProgressBar Frame
$prb->frame['left'] = 50;	                  // Frame position from left
$prb->frame['top'] = 	80;	                  // Frame position from top
$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
$prb->show();	                              // show the ProgressBar

echo "<br/>map inlezen: ".$path."<hr/>";

$skipArray = array(".","..","verwerkt",$markFile);
if ($handle = opendir($path))
{
  $stamp = date("Ymd_Hi")."-";
  $ndx = 0;
  while ( ($file = readdir($handle)) !== false )
  {

    if (!in_array($file, $skipArray) )
    {
      $ndx++;
      $progressStep = 0;
      
      $fp = file($path.$file);

      $csvRegels = count($fp);
      
      $fp = "";
      $prb->setLabelValue('txt1','Inlezen bestand '.$file.' ('.$csvRegels.' regels)');
      $csvhandle = fopen($path.$file, "r");
      $pro_multiplier = (100/$csvRegels);
      $row = 0;
      $rawData = array();
      $module = "";
      while ($data = fgetcsv($csvhandle, 8192, ";"))
      {


        if ($row == 4)
        {
          $module = $data[0];
        }
        if ($module == "AI566") // ZAN heeft , als decimaalteken terwijl andere bestanden . gebruiken
        {
          $data = str_replace(",",".", $data);
        }

        $rawData[] = $data;
        $row++;

        $pro_step += $pro_multiplier;
        $prb->moveStep($pro_step);
        if (in_array($row , $skipFoutregels))
        {
          $skipped .= "- regel $row overgeslagen<br>";
          continue; // rest overslaan, lees nieuwe regel
        }      
      }
      if (!$__develop)
      {
        rename($path.$file,$path."/verwerkt/".$stamp.$file);
      }
      
      $bestanden[] = array("file" => $file,
                           "rows" => $csvRegels,
                           "data" => $rawData);
    }
    
  }
  closedir($handle);  
  if (!$__develop)
  {
    rename($path.$markFile,$path."/verwerkt/".$stamp.$markFile);
  }  
}  
else
{  
  echo  "fout bij openen UBS Map";
  exit();
}
$prb->hide();

for ($bb=0; $bb < count($bestanden); $bb++)
{
  
  $fieldData = array();

  $rawDataArray = $bestanden[$bb]["data"];

  $offset = 0;
  if (substr($rawDataArray[4][0],0,1) != "A")
  {
    $offset = 1;
  }

  $module    = $rawDataArray[4+$offset][0];
  $date      = str_replace(".","-",$rawDataArray[2][0]);
  $fields    = $rawDataArray[7];
  

  for($idx=8+$offset; $idx < count($rawDataArray); $idx++)
  {
    $fieldData[] = $rawDataArray[$idx];
  }
  $meldArray = array();

  switch ($module)
  {
    case "AI300":
      include_once 'ubs_forexConf.php';
      do_forexConf($date,$fields,$fieldData);
      break;
    case "AI515":
      include_once 'ubs_PurchSaleConf.php';
      do_PurchSaleConf($date,$fields,$fieldData);
      break;
    case "AI566":
      include_once 'ubs_CorpActConf.php';
      do_CorpActConf($date,$fields,$fieldData);
      break;
    case "AI900":
    case "AI910":
      include_once 'ubs_DebitCreditConf.php';
      do_DebitCreditConf($date,$fields,$module,$fieldData);
      break;
    case "AI999":
      include_once 'ubs_ChargesAdvice.php';
      do_ChargesAdvice($date,$fields,$fieldData);
      break;
    default:
      echo "<li> module: $module nog niet aanwezig";
      break;
  }
}



$db = new DB();
if ($_GET["retry"] == 1)
{
  $query = "DELETE FROM TijdelijkeRekeningmutaties WHERE add_user = '$USR' ";
	$db->executeQuery($query);
}
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
	   //  exit();
     }
   }
   //$pro_step += $pro_multiplier;
   //$prb->moveStep($pro_step);

	$_query = "INSERT INTO TijdelijkeRekeningmutaties SET";
	while (list($key, $value) = each($output[$ndx]))
	{
	  if ($manualBoekdatum AND $key == "Boekdatum")
	  {
	    $value = $manualBoekdatum;
	  }

    $_query .= "`TijdelijkeRekeningmutaties`.$key = '".mysql_escape_string($value)."',";
	}
  $_query .= " add_date = NOW()";
  $_query .= ", add_user = '".$USR."'";
	$_query .= ", change_date = NOW()";
  $_query .= ", change_user = '".$USR."'";

	if (!$db->executeQuery($_query))
	{
	  echo mysql_error();
	  Echo "<br> FOUT bij het wegschrijven naar de database!";
	  exit();
	}
}


$prb->hide();
?>


<b>Klaar met inlezen </b><br/>

Statistiek<br/>
<table class="b1">
  <thead>
  <td class="pl10 pr10 al">module</td>
  <td class="pl10 pr10 ac">regels</td>
  <td class="pl10 pr10 ac">fouten</td>
  <td class="pl10 pr10 ac">controles</td>
  <td class="pl10 pr10 ac">foutmeldingen</td>
  </thead>

<?
    for($x=0; $x< count($statsArray); $x++)
    {
      $t = $statsArray[$x];
?>
  <tr>
    <td class="pl10 pr10 al b1 vat"><?=$t["module"]?></td>
    <td class="pl10 pr10 ar b1 vat"><?=$t["regels"]?></td>
    <td class="pl10 pr10 ar b1 vat"><?=$t["fouten"]?></td>
    <td class="pl10 pr10 al b1 vat"><?=$t["controle"]?></td>
    <td class="pl10 pr10 al b1 vat rood"><?=$t["errors"]?></td>
  </tr>
<?  
    }
?>

</table>

<br/>
<?=$skipped?>
<hr/>
<a href="../tijdelijkerekeningmutatiesList.php">Ga naar tijdelijk importbestand</a>
<!--
<hr>
<a href="<?=$_SERVER["REQUEST_URI"]."&retry=1"?>">Nogmaals inlezen</a>
-->
<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>