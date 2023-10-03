<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/01 12:11:26 $
 		File Versie					: $Revision: 1.11 $

 		$Log: credswiss_import.php,v $
 		Revision 1.11  2020/07/01 12:11:26  cvs
 		call 8714
 		
 		Revision 1.10  2020/06/12 06:43:29  cvs
 		no message
 		
 		Revision 1.9  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.8  2015/10/02 13:49:06  cvs
 		*** empty log message ***
 		
 		Revision 1.7  2015/03/26 10:05:50  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2015/03/26 09:48:19  cvs
 		*** empty log message ***
 		
 		Revision 1.5  2015/01/14 08:27:39  cvs
 		*** empty log message ***
 		
 		Revision 1.4  2014/12/16 07:30:12  cvs
 		*** empty log message ***
 		
 		Revision 1.3  2014/11/20 12:48:18  cvs
 		dbs 2746
 		
 		Revision 1.2  2014/11/13 10:43:10  cvs
 		dbs2746
 		
 		Revision 1.1  2014/09/29 12:21:42  cvs
 		*** empty log message ***
 		

*/


include_once('../../classes/AE_cls_progressbar.php');
include_once("wwwvars.php");
include_once("checkForDoubleImport.php");

include_once("credswiss_functies.php");
$__debug = true;
//debug($_GET,"get",true);

$path = $__credswissImportMap."/".$_GET["CSmap"]."/";
$markFile = "_done_.txt";


if (!$__develop)
{
  if (!file_exists($path."/verwerkt"))
  {
    mkdir($path."/verwerkt");
  }

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

if ($handle = opendir($path))
{
  $stamp = date("Ymd_Hi")."-";
  $ndx = 0;
  while ( ($file = readdir($handle)) !== false )
  {
    
    if (substr(strtolower($file),-4) == ".csv")
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
      while ($data = fgetcsv($csvhandle, 8192, ";"))
      {
       
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
  if (!$__develop)
  {
    rename($path.$markFile,$path."/verwerkt/".$stamp.$markFile);
  }
  closedir($handle);  

}  
else
{  
  echo  "fout bij openen CreditSwiss Map";
  exit();
}
$prb->hide();

for ($bb=0; $bb < count($bestanden); $bb++)
{
  
  $fieldData = array();

  $rawDataArray = $bestanden[$bb]["data"];
  $module    = $rawDataArray[0][0];
  $date      = $rawDataArray[3][0];
  $fields    = $rawDataArray[5];
  

  for($idx=6; $idx < count($rawDataArray); $idx++)
  {
    $fieldData[] = $rawDataArray[$idx];
  }
  //debug($fieldData,$module);
  $meldArray = array();
//  debug($module, $bb);
  switch ($module)
  {                                                     // vervallen wordt nu verwerkt in CashPositions call 8714
//    case "ChargesAdvice":
//      include_once 'credswiss_ChargesAdvice.php';
//      do_ChargesAdvice($date,$fields,$fieldData);
//      break;
    case "PurchSaleConf":  // checked
      include_once 'credswiss_PurchSaleConf.php';
      do_PurchSaleConf($date,$fields,$fieldData);
      break;
    case "CorpActConf":  // checked
//      debug($fieldData,$module);
      include_once 'credswiss_CorpActConf.php';
      do_CorpActConf($date,$fields,$fieldData);
      break;
//    case "ForexConf":                                 // vervallen wordt nu verwerkt in CashPositions call 3459
//      include_once 'credswiss_ForexConf.php';
//      do_ForexConf($date,$fields,$fieldData);
//      break;    
    case "DebitCreditConf":  //checked
      include_once 'credswiss_DebitCreditConf.php';
      do_DebitCreditConf($date,$fields,$fieldData);
      break;    
    case "ReceiveDeliverConf":  // checked
      include_once 'credswiss_ReceiveDeliverConf.php';
      do_ReceiveDeliverConf($date,$fields,$fieldData);
      break;    
    case "CashPositions":
      include_once 'credswiss_CashPositions.php';
      do_CashPositions($date,$fields,$fieldData);
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