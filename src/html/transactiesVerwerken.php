<?php
/*
    AE-ICT sourcemodule created 28 sep. 2020
    Author              : Chris van Santen
    Filename            : transactiesVerwerken.php


*/

include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

session_start();
$_SESSION['NAV'] = "";
session_write_close();

$content = array();
$content['jsincludes'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"javascript/jquery-ui-min.js\"></script>";
echo template($__appvar["templateContentHeader"],$content);

$laatsteValuta = getLaatsteValutadatum();

$afschriftIds=array();
foreach($_POST as $key=>$value)
{
	if(substr($key,0,12)=='afschriftId_')
	{
		$afschriftIds[]=substr($key,12);
	}
}


if(count($afschriftIds) > 0)
{
	$posted = true;
	$DB = new DB();
	$idFilter=" id IN('".implode("','",$afschriftIds) ."') ";
	$query = 	"SELECT min(datum) as eersteDatum FROM Rekeningafschriften WHERE $idFilter";
	$DB->SQL($query);
	$eersteDatum=$DB->lookupRecord();
	$datum=$eersteDatum['eersteDatum'];
	$datumJul=db2jul($eersteDatum['eersteDatum']);
	$selectieWhere=$idFilter." AND ";
}
else
{
	$datumJul = form2jul($_POST['vanafDatum']);
	$datum=date('Y-m-d',$datumJul);
	$selectieWhere=" Rekening >= '".$_POST['vanRekening']."' AND Rekening <= '".$_POST['totRekening']."' AND ";
}

if($posted == true)
{

	$prb = new ProgressBar();	// create new ProgressBar
	$prb->pedding = 2;	// Bar Pedding
	$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
	$prb->setFrame();          	                // set ProgressBar Frame
	$prb->frame['left'] = 50;	                  // Frame position from left
	$prb->frame['top'] = 	80;	                  // Frame position from top
	$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
	$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
	$prb->show();	                              // show the ProgressBar

	$prb->moveStep(0);
	$prb->setLabelValue('txt1','Moment ');
	$pro_step = 0;

	// check datum.
	if(empty($_POST['vanafDatum']) && count($afschriftIds)<1 )
	{
		echo vt("Fout").": ".vt("geen jaar opgegeven!");
		exit;
	}
	else
	{
	  if($__appvar["bedrijf"]!='RCN')
	  {
	    if(db2jul($laatsteValuta)-$datumJul > 3600*24*400)
	    {
	      echo vt("Meer dan een jaar aan afschriften verwerken? Verwerking afgebroken").". ".round((db2jul($laatsteValuta)-$datumJul)/(3600*24))." ".vt("dagen").".";
	      exit;
	    }
	  }
	}

	$DB2 = new DB();
	$DB3 = new DB();
	$mutaties=array();

	$query = 	" SELECT * FROM Rekeningafschriften WHERE $selectieWhere ".
						" Datum >= '".$datum."'";

	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	echo vt("Aantal afschriften in selectie:")." ".$DB->Records()."<br>";

	$pro_multiplier = (100 / $DB->Records());

	while($data = $DB->NextRecord())
	{
 		$pro_step += $pro_multiplier;
 		$prb->moveStep($pro_step);

		$nogeenkeerverwerken = false;
		// check of er een niet verwerkte mutatie is!


		if($data['Verwerkt'] >0)
		{
			$DB2->SQL("SELECT id FROM Rekeningmutaties WHERE Afschriftnummer = '".$data["Afschriftnummer"]."' AND Rekening = '".$data["Rekening"]."' AND Verwerkt = '0'");
			$DB2->Query();
			if($DB2->records() > 0)
			{
				echo "<br>".vt("Niet verwerkte regels gevonden in verwerkt afschrift!");
				$nogeenkeerverwerken = true;
			}
		}

		if($data['Verwerkt'] == 0 || $nogeenkeerverwerken == true)
		{

			// check sum
			$DB2->SQL("SELECT SUM(Bedrag) AS Totaal FROM Rekeningmutaties WHERE Afschriftnummer = '".$data["Afschriftnummer"]."' AND Rekening = '".$data["Rekening"]."'");
			$DB2->Query();
			$totaal = $DB2->NextRecord();

			$mutatieBedrag = round(($data["NieuwSaldo"] - $data["Saldo"]),2);
			// Reken mutatieverschil uit
			$mutatieVerschil = $mutatieBedrag - round($totaal['Totaal'],2);
			// Zet Fieldset Class voor mutatie veschil
			if($mutatieVerschil  <> 0)
			{
				echo "<br><b>".vt("Mutatieverschil")."</b> ".vt("Rekening").": ".$data['Rekening'].", ".vt("Afschriftnummer").": ".$data['Afschriftnummer']." (".$mutatieVerschil.")";
			}
			else
			{
				// Verwerk!
				$query1 = "UPDATE Rekeningmutaties SET Verwerkt = '1', change_user = '".$USR."', change_date = NOW() WHERE Afschriftnummer = '".$data["Afschriftnummer"]."' AND Rekening = '".$data["Rekening"]."'";
				$DB2->SQL($query1);
				if($DB2->Query())
				{
				  if($__appvar['master'] == false)
				  {
				    $query3="SELECT * FROM Rekeningmutaties WHERE Afschriftnummer = '".$data["Afschriftnummer"]."' AND Rekening = '".$data["Rekening"]."' AND Grootboekrekening='Fonds'";
				    $DB3->SQL($query3);
				    $DB3->Query();
				    while($data3=$DB3->nextRecord())
				      $mutaties[]=$data3;
				  }

					$query2 = "UPDATE Rekeningafschriften SET Verwerkt = '1', change_user = '".$USR."', change_date = NOW()  WHERE id = '".$data["id"]."'";
					$DB2->SQL($query2);
					if($DB2->Query())
					{
						echo "<br>".vt("Rekening").": ".$data['Rekening'].", ".vt("Afschriftnummer").": ".$data['Afschriftnummer']." ".vt("verwerkt").".";
					}
				}
				else
				{
					echo "<br><b>".vt("Fout bij verwerken")."</b> ".vt("Rekening").": ".$data['Rekening'].", ".vt("Afschriftnummer").": ".$data['Afschriftnummer']." (".$mutatieVerschil.")";
				}
			}
		}
    if($_POST['log_all']==1)
	  {
	    echo "<br>".vt("Datum").": ".$data['Datum'].", ".vt("Rekening").": ".$data['Rekening'].", ".vt("Afschriftnummer").": ".$data['Afschriftnummer'];
	  }
	}

	if($__appvar['master'] == false)
  {
    orderCheck($mutaties);
  }


	$prb->hide();
  
  $query = 	" SELECT count(id) as aantal FROM Rekeningafschriften WHERE Rekening >= '".$vanRekening."' ".
						" AND Rekening <= '".$totRekening."' AND ".
						" Datum >= '".formdate2db($_POST['vanafDatum'])."' AND verwerkt=0";
  $DB->SQL($query);
	$aantal=$DB->lookupRecord();
	echo "<br>".vt("Aantal nog onverwerkte rekeningafschriften in selectie").": ".$aantal['aantal']."<br>";
  $query = 	" SELECT count(id) as aantal FROM Rekeningmutaties WHERE Rekening >= '".$vanRekening."' ".
						" AND Rekening <= '".$totRekening."' AND ".
						" Boekdatum >= '".formdate2db($_POST['vanafDatum'])."' AND verwerkt=0";
  $DB->SQL($query);
	$aantal=$DB->lookupRecord();
	echo "".vt("Aantal nog onverwerkte rekeningmutaties in selectie").": ".$aantal['aantal']."<br>";
}
else
{
  
  
  $DB = new DB();
//	$DB->SQL($query);
	//$DB->Query($query);
  $rec = $DB->lookupRecordByQuery("SELECT Rekening FROM Rekeningen WHERE consolidatie=0 ORDER BY Rekening");
  $eersteRekening = $rec["Rekening"];
  
  $rec = $DB->lookupRecordByQuery("SELECT Rekening FROM Rekeningen WHERE consolidatie=0 ORDER BY Rekening DESC");
  $laatsteRekening = $rec["Rekening"];
  

	// get laatste valutaDatum

?>

<style>
  .ui-menu {
     width:400px;
     height: 400px;
  }
  .ui-autocomplete {
    font-size: .8em;
    max-height: 500px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  
</style>

<form action="transactiesVerwerken.php" method="POST" target="importFrame" name="controleForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="aanvullen" value="0" />
<!-- Name of input element determines name in $_FILES array -->
<b><?=vt("Transacties verwerken")?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?=vt("Van rekening")?>: </div>
<div class="formrechts">
<input name="vanRekening" id="vanRekening" value="<?=$eersteRekening?>" />
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?=vt("T/m rekening")?>: </div>
<div class="formrechts">
 <input name="totRekening" id="tmRekening" value="<?=$laatsteRekening?>" />
</div>
</div>

<?
if($__appvar["bedrijf"]=='FCM')
  $datum=date("d-m-Y",db2jul($laatsteValuta)-(24*3600*365));
else
  $datum=date("d-m-Y",db2jul($laatsteValuta)-(24*3600*30));
?>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?=vt("Vanaf datum")?>: </div>
<div class="formrechts">
<input type="text" name="vanafDatum" value="<?=$datum?>" size="15"> dd-mm-yyyy
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="checkbox" name="log_error" value="1" checked> <?=vt("Log fouten")?>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="checkbox" name="log_all" value="1"> <?=vt("Log alles")?>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="<?=vt("Verwerken")?>" onClick="document.controleForm.submit();">
&nbsp;&nbsp;&nbsp;&nbsp;
</div>
</div>

</div>

</form>


<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<iframe width="600" height="400" name="importFrame"></iframe>
</div>
</div>

</div>
  
<script>
   $(document).ready(function(){
     
      $("#vanRekening").focus().select();
      $("#vanRekening").change(function(){
        $("#tmRekening").val($("#vanRekening").val());
      });
      $("#vanRekening").autocomplete(
      {
        source: "lookups/jq_Rekening.php",
        mustMatch:true,
        select: function( event, ui ) 
        {   
          $("#vanRekening").val(ui.item.Rekening);
          return false;
        },
        minLength: 2,
        autoFocus: true,
        delay : 0 
      });
      
      $("#tmRekening").autocomplete(
      {
        source: "lookups/jq_Rekening.php",
        mustMatch:true,
        select: function( event, ui ) 
        {   
          $("#tmRekening").val(ui.item.Rekening);
          return false;
        },
        minLength: 2,
        autoFocus: true,
        delay : 0 
      });
   });
</script>
  
<?
}

echo template($__appvar["templateRefreshFooter"],$content);
