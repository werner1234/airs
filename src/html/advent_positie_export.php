<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2013/12/11 10:06:26 $
 		File Versie					: $Revision: 1.1 $

 		$Log: advent_positie_export.php,v $
 		Revision 1.1  2013/12/11 10:06:26  cvs
 		*** empty log message ***
 		


*/
//error_reporting(E_ALL);

include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
//include_once("rapport/rapportRekenClass.php");

session_start();
$_SESSION[NAV] = "";
session_write_close();

$content = array();
$verlopenPortefeuille = array();
//echo template($__appvar["templateContentHeader"],$content);
$outputArray = array();
$DB = new DB();
$DB1 = new DB();
echo $_GET['datum'];
$dd = explode($__appvar["date_seperator"],$_GET['datum']);
/*
if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
{
	echo "Ongeldige datum opgegeven!";
	exit;
}
$datum = $dd[2]."-".$dd[1]."-".$dd[0];
*/
$uitvoer = $_GET['uitvoer'];
//////////////////////////////////////////
// functies
//////////////////////////////////////////


function nf($bedrag)
{
  if (isNumeric($bedrag))
    return number_format($bedrag,4,".","");
  else
    return $bedrag;
}



	$prb = new ProgressBar();	// create new ProgressBar
	$prb->pedding = 2;	// Bar Pedding
	$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
	$prb->setFrame();          	                // set ProgressBar Frame
	$prb->frame['left'] = 50;	                  // Frame position from left
	$prb->frame['top'] = 	80;	                  // Frame position from top
	$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
	$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
	$prb->show();
	if ($_GET['bank'] == "gilis")
	{
	  include("advent_positie_export_gilissen.php");
	  if (!readGilissenCvsFile($_GET['file'],$_GET['file1']))
	  {
		  echo "Problemen bij het inlezen van bestand!";
		  exit;
	  }

	}
	elseif ($_GET['bank'] == "abn")
	{
	  include("advent_positie_export_abn.php");
	  if (!readAbn($_GET['file'],$_GET['file1']))
	  {
		  echo "Problemen bij het inlezen van bestand!";
		  exit;
	  }
	}
	elseif ($_GET['bank'] == "binck")
	{
	 include('advent_positie_export_binck.php');
	 if (!readBinck($_GET['file'],$_GET['Vermogensbeheerder']))
	  {
		  echo "Problemen bij het inlezen van bestand!";
		  exit;
	  }
	}
	elseif ($_GET['bank'] == "stroeve")
	{
	  include("advent_positie_export_stroeve.php");
    if (!readStroeve($_GET['file']))
	  {
		  echo "Problemen bij het inlezen van bestand!";
		  exit;
	  }
	}
  else
  {
    echo "FOUT: ".__FILE__;
    exit();
  }

listarray($bankOutput);
//listarray($outputArray);
exit;
//echo "uitvoer start";
// show the ProgressBar

$foutCount = 0;
//listarray($outputArray);
for ($portIndex=0;$portIndex < count($portefeuilleArray);$portIndex++)
{
  $_port = $portefeuilleArray[$portIndex];
  $A =  $outputArray[$_port]['A'];   // $A is een pointer geen Array!!
  $B =  $outputArray[$_port]['B'];   // $B is een pointer geen Array!!

  if ($_GET['bank'] == "raboTrans")
  {
    for ($telA=0;$telA < count($A);$telA++)
    {
      $scrArray[trim($A[$telA]['portefeuille'])][trim($B[$telA]['fonds'])]['A'] = nf($A[$telA]['aantal']);
      $scrArray[trim($A[$telA]['portefeuille'])][trim($B[$telA]['fonds'])]['B'] = nf($B[$telA]['aantal']);
    }
  }
  else
  {

    for ($telA=0;$telA < count($A);$telA++)
    {
      $matchFound = 0;

     // if ($A[$telA]['fonds'] == "") continue;

      $scrArray[trim($A[$telA]['portefeuille'])][trim($A[$telA]['fonds'])]['A'] = nf($A[$telA]['aantal']);

      for ($telB=0;$telB < count($B);$telB++)
	    {
        if ($B[$telB]['fonds'] == "") continue;
        //if ($B[$telB]['match'] == 1)  continue;

        //echo "<br>".trim($A[$telA]['portefeuille'])." / ".trim($B[$telB]['portefeuille'])." / #".trim($A[$telA]['fonds'])."# / #".trim($B[$telB]['fonds'])."#";


        if (     trim($A[$telA]['fonds'])        == trim($B[$telB]['fonds'])             and
                 trim($A[$telA]['portefeuille']) == trim($B[$telB]['portefeuille'])          )
        {
           $scrArray[trim($A[$telA]['portefeuille'])][trim($A[$telA]['fonds'])]['B'] = nf($B[$telB]['aantal']);
	         $B[$telB]['match'] = 1;
	         //echo "**";
           break;
        }
        else
        {
          $scrArray[trim($A[$telA]['portefeuille'])][trim($A[$telA]['fonds'])]['B'] = "Geen bank info";
        }
      }
    }

    for ($telB=0;$telB < count($B);$telB++) // regels niet gematched toevoegen
	  {
      if ($B[$telB]['match'] == 1) continue;
      $scrArray[trim($B[$telB]['portefeuille'])][trim($B[$telB]['fonds'])]['B'] = nf($B[$telB]['aantal']);
      $scrArray[trim($B[$telB]['portefeuille'])][trim($B[$telB]['fonds'])]['A'] = "Geen AIRS info";
	  }
  }

}


$outputArray = array();
$prb->hide();
if ($_GET["naar"] == "scherm")
{
?>
<style>
TD{
  font: 12px 'Arial' bold;
  color: Black;
  text-decoration: none;
}
.formport {
  font: 12px 'Arial' bold;
  color: Black;
	float: left;
	width:100px;
	text-align: left;
}
.formlinks {
  font: 12px 'Arial' bold;
  color: Black;
	float: left;
	width:270px;
	text-align: left;
}

.formrechts {
  font: 12px 'Arial' bold;
  color: Black;
  float: left;
	width:110px;
	text-align: right;
}

.formblock {
	float: left;
	margin: 1px;
	margin-left: 13px;
	width: 95%;
	padding:2pt;
	font-size:12px;

}
</style>



soort uitvoer <?=$_GET['uitvoer']?>

<?

$outCsv = array();

?>
<div class="formblock">
  <div class="formport"><b>portefeuille</b> </div>
  <div class="formlinks"><b>fonds</b></div>
  <div class="formrechts"><b>AIRS</b></div>
  <div class="formrechts"><b>bank</b></div>
  <div class="formrechts"><b>AIRS gister</b></div>
</div>
<hr>
<?
  $sk = 0;


  if (count($bankOutput) > 0)
  {
    for ($_tel = 0;$_tel < count($bankOutput); $_tel++)
    {
      if (round($bankOutput[$_tel]['Asaldo'],2) == round($bankOutput[$_tel]['Bsaldo'],2) AND $_GET['uitvoer'] == "verschillen")
  		{
  		  continue;
  		}
  		if (($bankOutput[$_tel]['Bsaldo'] == 0 AND stristr($bankOutput[$_tel]['Asaldo'], "geen airs info") AND $_GET['uitvoer'] == "verschillen") )  //"geen info" met tegenwaarde 0 negeren
      {
  		  continue;
  		}
  		if (nf($bankOutput[$_tel]['Asaldo']) == "Einddatum")  // inaktieve portefeuille negeren
  		{
  		  continue;
  		}
  		if (nf($bankOutput[$_tel]['Asaldo']) == 0 AND $bankOutput[$_tel]['Bsaldo'] == "Bestaat niet")  // Als AIRS saldo =0 en niet in Bank dan negeren
  		{
  		    continue;
  		}
?>
  
    <div class="formblock">
      <div class="formport"><?=$bankOutput[$_tel]['rekeningnr']?> </div>
      <div class="formlinks">Liquiditeiten</div>
      <div class="formrechts"><?=nf($bankOutput[$_tel]['Asaldo'])?></div>
      <div class="formrechts"><?=nf($bankOutput[$_tel]['Bsaldo'])?></div>
      <div class="formrechts" style="color:Maroon"><?=nf($bankOutput[$_tel]['AsaldoG'])?></div>
    </div>
 <?
    }
  }

  while ($port = each($scrArray))
  {
    $tdPortefeuille = $port[0];
    //listarray($port);
  	while ($data = each($port[1]) )
  	{
    	  flush();
    		$tdFonds = $data[0];
    
        // als bank aantal factor 1 tov AIRS
    		if (round($data[1]['A'],2) == round($data[1]['B'],2) AND $_GET['uitvoer'] == "verschillen")
    		{
    		  continue;
    		}
        // als bank aantal factor 0.01 tov AIRS
        if (round($data[1]['A'],2) == round($data[1]['B'] * 100,2) AND $_GET['uitvoer'] == "verschillen")
    		{
    		  continue;
    		}
        // als bank aantal factor 0.001 tov AIRS
        if (round($data[1]['A'],2) == round($data[1]['B'] * 1000,2) AND $_GET['uitvoer'] == "verschillen")
    		{
    		  continue;
    		}
    		if (($data[1]['A'] == 0 AND stristr($data[1]['B'], "geen bank info") AND $_GET['uitvoer'] == "verschillen") OR
    		    ($data[1]['B'] == 0 AND stristr($data[1]['A'], "geen airs info") AND $_GET['uitvoer'] == "verschillen") )  //"geen info" met tegenwaarde 0 negeren
        {
    		  continue;
    		}
    
    		$portNum = ereg_replace("[^0-9]","",$tdPortefeuille);
    	  if (in_array($portNum, $verlopenPortefeuille))
    	  {
    	    $sk++;
    	    continue;
    	  }
  
?>
  
  <div class="formblock">
    <div class="formport"><?=$sk?>&nbsp;&nbsp;<?=$tdPortefeuille?> </div>
    <div class="formlinks"><?=$tdFonds?></div>
    <div class="formrechts"><?=$data[1]['A']?></div>
    <div class="formrechts"><?=$data[1]['B']?></div>
  </div>
<?
  
  	}
  }

  echo "<br>skipped portefeuilleregels $sk<br>";


// listarray($verlopenPortefeuille);
	echo "Controle verwerkt in $tijd seconden <hr>";
  if ($vermogenbeheerderFound <> "")
  {
    $db = new DB();
    $query = "SELECT Bedrijf FROM VermogensbeheerdersPerBedrijf WHERE Vermogensbeheerder ='$vermogenbeheerderFound'";
    $db->SQL($query);
    $bedrijfsRec = $db->lookupRecord();


    $query = "SELECT Vermogensbeheerder FROM VermogensbeheerdersPerBedrijf WHERE Bedrijf='".$bedrijfsRec["Bedrijf"]."' ";
    $db->executeQuery($query);
    while($vbRec = $db->nextRecord())
    {
      $vbs[] = $vbRec["Vermogensbeheerder"];
    }

    $query = "SELECT Portefeuille FROM Portefeuilles WHERE  Vermogensbeheerder in ('".implode("','",$vbs)."') AND  Depotbank = 'TGB' AND Einddatum > NOW()";
    $db->executeQuery($query);
    while($portRec = $db->nextRecord())
    {
      if (!in_array($portRec["Portefeuille"],$portefeuilleArray))
        echo "<li>".$portRec["Portefeuille"]." niet gevonden in bankbestand";

    }
  }


  echo template($__appvar["templateRefreshFooter"],$content);
}  // naar scherm
else
{


?>
<script>
  function pushpdf(file,save)
  {
    var width='800';
    var height='600';
    var target = '_blank';
    var location = 'pushFile.php?filetype=csv&file=' + file;
    if(save == 1)
    {
      // opslaan als bestand
      document.location = location + '&action=attachment';
    }
    else
    {
      // pushen naar PDF reader
      var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
      doc.document.location = location;
    }
  }
</script>
<?

if ($vtControle == false)
{
  $outCsv[] = array("portefeuille","fonds","AIRS positie","bank positie","AIRS positie gisteren");
  $sk = 0;
  if (count($bankOutput) > 0)
  {
    for ($_tel = 0;$_tel < count($bankOutput); $_tel++)
    {
      if (round($bankOutput[$_tel]['Asaldo'],2) == round($bankOutput[$_tel]['Bsaldo'],2) AND $_GET['uitvoer'] == "verschillen")
		  {
		    continue;
		  }
		  if (($bankOutput[$_tel]['Bsaldo'] == 0 AND stristr($bankOutput[$_tel]['Asaldo'], "geen airs info") AND $_GET['uitvoer'] == "verschillen") )  //"geen info" met tegenwaarde 0 negeren
      {
		    continue;
		  }
		  if (nf($bankOutput[$_tel]['Asaldo']) == "Einddatum")  // inaktieve portefeuille negeren
		  {
		    continue;
		  }
      /*
		  if (nf($bankOutput[$_tel]['Asaldo']) == 0 AND $bankOutput[$_tel]['Bsaldo'] == "Bestaat niet")  // Als AIRS saldo =0 en niet in Bank dan negeren
	   	{
		    continue;
		  }
      */
		  $outCsv[] = array($bankOutput[$_tel]['rekeningnr'],"Liquiditeiten",nf($bankOutput[$_tel]['Asaldo']),nf($bankOutput[$_tel]['Bsaldo']),nf($bankOutput[$_tel]['AsaldoG']));
    }
  }

  while ($port = each($scrArray))
  {
    $tdPortefeuille = $port[0];
    while ($data = each($port[1]) )
	  {
      $tdFonds = $data[0];
      if ($data[1]['A'] == $data[1]['B'] AND $_GET['uitvoer'] == "verschillen")
      {
	      continue;
	    }
		  if (($data[1]['A'] == 0 AND stristr($data[1]['B'], "geen bank info") AND $_GET['uitvoer'] == "verschillen") OR
		      ($data[1]['B'] == 0 AND stristr($data[1]['A'], "geen airs info") AND $_GET['uitvoer'] == "verschillen") )  //"geen info" met tegenwaarde 0 negeren
      {
		    continue;
		  }

		  $portNum = ereg_replace("[^0-9]","",$tdPortefeuille);
	    if (in_array($portNum, $verlopenPortefeuille))
	    {
	      $sk++;
	      continue;
	    }
	    $outCsv[] = array($tdPortefeuille,$tdFonds,$data[1]['A'],$data[1]['B']);
	  }
  }
}
    
$path = $__appvar[tempdir];
$filename = "portefeuilleControle_".$USR.mktime().".".$_GET["naar"];

if ($_GET["naar"] == 'xls')
   {
		 include_once('../classes/excel/Writer.php');
     $workbook = new Spreadsheet_Excel_Writer($path.$filename);

    $excelOpmaak['getal']=array('setBgColor'=>'26','setFgColor'=>'8','setSize'=>'10','setNumFormat'=>'2');

    while(list($opmaakSleutel,$eigenschappen)=each($excelOpmaak))
    {
      $opmaak[$opmaakSleutel] =& $workbook->addFormat();
      while(list($eigenschap,$value)=each($eigenschappen))
      {
        $opmaak[$opmaakSleutel]->$eigenschap($value);
      }
    }

     $worksheet =& $workbook->addWorksheet();
     for($regel = 0; $regel < count($outCsv); $regel++ )
		   for($col = 0; $col < count($outCsv[$regel]); $col++)
		   {
		     if(isNumeric($outCsv[$regel][$col]) && $col >0)
		     {
		       $worksheet->write($regel, $col, $outCsv[$regel][$col],$opmaak['getal']);
		     }
		     else
		   	  $worksheet->write($regel, $col, $outCsv[$regel][$col]);
		   }
	   $workbook->close();
   }
	 elseif($fp = fopen($path.$filename,"w+"))
	 {
	   if ($_GET["naar"] == "mutaties")
     {
       $db=new DB();
       foreach ($outCsv as $regel=>$regelData)
       {
         $rekening=$regelData[0];
         $type=$regelData[1];
         if(substr($type,0,13) == 'Liquiditeiten' && strlen($type) > 13)
         {
           $parts=explode(" ",$type);
           $type=$parts[0];
           $rekening=$parts[1];
         }
         $verschil=round($regelData[3]-$regelData[2],2);
         if($type=='Liquiditeiten' && $verschil <> 0 && $db->QRecords("SELECT id FROM Rekeningen WHERE Rekening='$rekening'"))
         {
           if($verschil > 0)
            $waardeVeld="Credit='$verschil',Debet=0";
           else
            $waardeVeld="Debet='".abs($verschil)."',Credit=0";

           $query="INSERT INTO TijdelijkeRekeningmutaties SET Rekening='$rekening',Omschrijving='Correctie bankkosten',Boekdatum='$datum',Valuta='EUR',Valutakoers='1',$waardeVeld, Grootboekrekening='KNBA',
           Bedrag='$verschil',change_date=now(),add_date=now(),add_user='$USR',change_user='$USR',Verwerkt='0',Fonds='',Fondskoers='0'";
           $db->SQL($query);
           $db->Query();
         }
       }
       echo "<a href=\"javascript:parent.parent.frames['content'].location='tijdelijkerekeningmutatiesList.php'\">Tijdelijk importbestand</a>";
       exit;
     }

		 $csvdata = generateCSV($outCsv);
	 	 fwrite($fp,$csvdata);
		 fclose($fp);
 	 }
	 else
	 {
		echo "Fout: kan niet schrijven naar ".$filename;
		exit;
	 }
	 ?>

<script>
  pushpdf('<?=$filename?>',1);
</script>
<?

	}

?>