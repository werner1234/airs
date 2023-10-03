<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

session_start();
$_SESSION[NAV] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);

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
	$prb->setLabelValue('txt1','Bezig met consistentie controle');
	$pro_step = 0;
		
	// voor in loop
	$DB3 = new DB();
	$DB4 = new DB();
	
	// selecteer grootboekrekeningen met vinkje kruispost.
	$DB2 = new DB();
	$DB2->SQL("SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Kruispost = '1' ");
	$DB2->Query();
	while($gb = $DB2->NextRecord())
	{
		$grootboeken[] = " Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";
	}	
	
	$grootboekSQL = implode(" OR ",$grootboeken);
	
	$query = "SELECT * FROM Portefeuilles WHERE Portefeuille >= '".$vanPortefeuille."' AND Portefeuille <= '".$tmPortefeuille."'";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	
	$pro_multiplier = (100 / $DB->Records());
	
	echo "Aantal portefeuilles in selectie: ".$DB->Records()."<br>";
	while($data = $DB->NextRecord())
	{
 		$pro_step += $pro_multiplier;
 		$prb->moveStep($pro_step);	
   		
		//-------------------------------------------------------------------------
		// Kruispost controle 
		if($_GET['kruispost'] == 1)
		{
			$query = "SELECT Rekeningen.Portefeuille, SUM(ROUND((ROUND(Rekeningmutaties.Bedrag,2) * Rekeningmutaties.Valutakoers),2)) AS verschil FROM Rekeningen, Rekeningmutaties ".
						 " WHERE Rekeningen.Rekening = Rekeningmutaties.Rekening AND ".
						 " Rekeningen.Portefeuille = '".$data['Portefeuille']."' AND ".
						 " ( ".$grootboekSQL." ) GROUP BY Rekeningen.Portefeuille ";
						 
			$DB2->SQL($query);
			$DB2->Query();
			$mutatieverschil = $DB2->NextRecord();
		
			if($mutatieverschil[verschil] <> 0 && !empty($mutatieverschil[verschil]))
			{
				echo "<br>Fout: er staat een bedag op kruispost, Portefeuille : ".$data['Portefeuille']." verschil: ".$mutatieverschil['verschil'] ;
			}
		}
		
		
		
		//-------------------------------------------------------------------------
		// Begin en eindsaldi rekeningafschriften controle 

		if($_GET['afschrift'] == 1)
		{
			// selecteer alle rekeningen bij portefeuille
			$query = "SELECT DISTINCT(Rekeningafschriften.Rekening), Rekeningen.Portefeuille FROM Rekeningen, Rekeningafschriften ".
							 " WHERE Rekeningen.Rekening = Rekeningafschriften.Rekening AND ".
							 " Rekeningen.Portefeuille = '".$data['Portefeuille']."' ";
							 
			$DB2->SQL($query);
			$DB2->Query();
			while($rekening = $DB2->NextRecord())
			{
				// loop alle afschriften, check saldi
				
				$query = "SELECT ROUND(SUM(Rekeningmutaties.Bedrag),2) AS controle , ".
				" ROUND((Rekeningafschriften.NieuwSaldo - Rekeningafschriften.Saldo),2) AS mutatie , ".
				" Rekeningafschriften.* ".
				" FROM Rekeningafschriften ".
				" JOIN Rekeningmutaties ON Rekeningafschriften.Rekening = Rekeningmutaties.Rekening ".
				" AND Rekeningafschriften.Afschriftnummer = Rekeningmutaties.Afschriftnummer ".
				" WHERE Rekeningafschriften.Rekening = '".$rekening['Rekening']."' ".
				" GROUP BY Rekeningafschriften.Afschriftnummer ".
				" ORDER BY Afschriftnummer ";
										
				$DB3->SQL($query);
				$DB3->Query();
				while($afschrift = $DB3->NextRecord())
				{
					// check of controle gelijk is aan nieuw Saldo!
					if($afschrift['controle'] <> $afschrift['mutatie'])
					{
						// fout , mutatie verschil.
						echo "<br>Fout: Mutatieverschil van ".($afschrift['controle'] -$afschrift['mutatie'])." op Rekening ".$afschrift['Rekening']." , afschrift ".$afschrift['Afschriftnummer'];
						flush();
					}
					$vorigeSaldo = $afschrif['NieuwSaldo'];
		
				}
			}
		}

		
		if($_GET['mutatieverschil'] == 1)
		{
			//-------------------------------------------------------------------------
			// Check alle rekeningmutaties op Bedrag.
			// selecteer alle rekeningen bij portefeuille
			$query = "SELECT DISTINCT(Rekeningafschriften.Rekening), Rekeningen.Portefeuille FROM Rekeningen, Rekeningafschriften ".
							 " WHERE Rekeningen.Rekening = Rekeningafschriften.Rekening AND ".
							 " Rekeningen.Portefeuille = '".$data['Portefeuille']."' ";
							 
			$DB2->SQL($query);
			$DB2->Query();
			while($rekening = $DB2->NextRecord())
			{
				// loop alle afschriften, check saldi
				
			 $query = "SELECT ROUND(Rekeningmutaties.Credit - Rekeningmutaties.Debet,2) AS controle , ".
				" ROUND(Rekeningmutaties.Bedrag,2) AS Bedrag,  ".
				" Rekeningmutaties.Valuta, ".
				" Rekeningmutaties.Rekening, ".
				" Rekeningmutaties.Valutakoers, ".
				" Rekeningmutaties.Afschriftnummer, ".
				" Rekeningen.Valuta AS RekeningValuta ".
				" FROM Rekeningmutaties, Rekeningen ".
				" WHERE Rekeningen.Rekening = '".$rekening['Rekening']."' AND ". 
				" Rekeningmutaties.Rekening = '".$rekening['Rekening']."' ".
				" ORDER BY Afschriftnummer ";
										
				$DB3->SQL($query);
				$DB3->Query();
				while($afschrift = $DB3->NextRecord())
				{
					if($afschrift['RekeningValuta'] <> $afschrift['Valuta'])
						$afschrift['controle'] = round($afschrift['controle'] * $afschrift[Valutakoers],2);
					// check of controle gelijk is aan nieuw Saldo!
					if($afschrift['controle'] <> $afschrift['Bedrag'])
					{
						// fout , mutatie verschil.
						echo "<br>Fout: Rekeningmutatie verschil  van ".($afschrift['controle'] -$afschrift['Bedrag'])." op Rekening ".$afschrift['Rekening']." , afschrift ".$afschrift['Afschriftnummer'];
						flush();
					}
					$vorigeSaldo = $afschrif['NieuwSaldo'];
	
				}
			}
		}


		if($_GET['fonds'] == 1)
		{
			//-------------------------------------------------------------------------
			// Fondsen zonder beleggingscategorie controle
			$query = "SELECT BeleggingscategoriePerFonds.Beleggingscategorie, Portefeuilles.Vermogensbeheerder, Rekeningen.Portefeuille, Rekeningmutaties . * ".
				" FROM (Rekeningmutaties, Rekeningen, Portefeuilles) ".
				" LEFT JOIN BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds ".
				" AND Portefeuilles.Vermogensbeheerder = BeleggingscategoriePerFonds.Vermogensbeheerder ".
				" WHERE Rekeningen.Rekening = Rekeningmutaties.Rekening AND ".
				" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
				" Rekeningen.Portefeuille = '".$data['Portefeuille']."' AND ".
				" Rekeningmutaties.Grootboekrekening = 'FONDS' AND BeleggingscategoriePerFonds.Beleggingscategorie IS NULL ";
			
			$DB2->SQL($query);
			$DB2->Query();
			while($fonds = $DB2->NextRecord())
			{
				echo "<br>Fout: Fonds ".$fonds['Fonds']." bij portefeuille ".$fonds['Portefeuille']." heeft geen beleggingscategorie";
			}
		}
	}	
	$prb->hide();
}
else 
{
	$query = "SELECT Portefeuille FROM Portefeuilles ORDER BY Portefeuille ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query($query);
	while($rek = $DB->NextRecord())
	{
		$options[] = $rek[Portefeuille];
		$laatstSelect = $rek[Portefeuille];
	}
	
?>
<form action="consistentieControle.php" method="GET" target="importFrame" name="controleForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="aanvullen" value="0" />
<!-- Name of input element determines name in $_FILES array -->
<b>Consistentie controle</b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>

<div class="form">
<div class="formblock">
<div class="formlinks"> Portefeuille van: </div>
<div class="formrechts">
<select name="vanPortefeuille">
<?=SelectArray("",$options)?>
</select>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> Portefeuille tot: </div>
<div class="formrechts">
<select name="tmPortefeuille">
<?=SelectArray($laatstSelect,$options)?>
</select>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> &nbsp; </div>
<div class="formrechts">
<input type="checkbox" name="afschrift" value="1" checked> Afschriften controle <br>
<input type="checkbox" name="kruispost" value="1" checked> Kruisposten controle <br>
<input type="checkbox" name="mutatieverschil" value="1" checked> Rekeningmutatie verschillen <br>
<input type="checkbox" name="fonds" value="1" checked> Fondsen zonder beleggingscategorie controle <br>
</div>
</div>


<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="button" value="Start controle" onClick="document.controleForm.submit();">
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
<?
echo template($__appvar["templateRefreshFooter"],$content);
}
?>
