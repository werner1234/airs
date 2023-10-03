<?php
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');
include_once('../classes/AE_cls_xls.php');
include_once('rapport/rapportRekenClass.php');

session_start();
$_SESSION["NAV"] = "";
//$_SESSION['submenu'] = New Submenu();
//$_SESSION['submenu']->addItem("<br>","");
//$_SESSION['submenu']->addItem('Bepaal actieve fondsen',"bepaalActieveFondsen.php");
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);
// if poster
if($_POST['posted'])
{
  if(!checkAccess("superapp"))
   $aanvullen=0;

	if(empty($datum))
	{
		$_error = vt("Fout").": ".vt("geen datum opgegeven!");
	}

	if (empty($_error))
	{
		$prb = new ProgressBar();	// create new ProgressBar
		$prb->pedding = 2;	// Bar Pedding
		$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
		$prb->setFrame();          	                // set ProgressBar Frame
		$prb->frame['left'] = 50;	                  // Frame position from left
		$prb->frame['top'] = 	80;	                  // Frame position from top
		$prb->addLabel('text','txt1','Moment ...');	// add Text as Label 'txt1' and value 'Please wait'
		$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
		$prb->show();	                              // show the ProgressBar

?>
<b><?=vt("Controle")?> (<?=$datum?>)</b><br>
<?
		$DB 	= new DB();
		$dateJul = form2jul($datum);
		$date = jul2sql($dateJul,true);
		$aantalfondsen =0;
		$valutas =0;
		$xls = new AE_xls();

 		// check Valuta
    if(is_array($actieveValuta) && $__appvar["bedrijf"]=="HOME")
      $query = "SELECT Valuta FROM Valutas WHERE Valuta IN ('".implode("','",$actieveValuta)."')";
    else
	  	$query = "SELECT Valuta FROM Valutas";
		$DB->SQL($query);
		$DB->Query();

		$pro_multiplier = (100 / ($DB->Records()+1));

		$prb->moveStep(0);
		$prb->setLabelValue('txt1',vt('Valuta controle...'));
		$pro_step = 0;

		$DB2 	= new DB();
		while($data = $DB->NextRecord())
		{
 			$pro_step += $pro_multiplier;
 			$prb->moveStep($pro_step);

			$query = "SELECT id FROM Valutakoersen WHERE datum = '".$date."' AND Valuta = '".$data['Valuta']."'";
			$DB2->SQL($query);
			$DB2->Query();
			if($DB2->Records() <= 0)
			{
				// if aanvullen, aanvullen koers, haal laatste op!
				if($aanvullen == 1)
				{
					//select
					$query = "SELECT Koers FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' ORDER BY datum DESC LIMIT 1";
					$DB2->SQL($query);
					$DB2->Query();
					if($DB2->Records() == 1)
					{
						$koers = $DB2->NextRecord();
						$query = "INSERT INTO Valutakoersen SET datum = '".$date."' , Valuta = '".$data['Valuta']."', Koers = '".$koers['Koers']."', ".
										 " add_user = '".$USR."', ".
										 " add_date = NOW(), ".
										 " change_user = '".$USR."',".
										 " change_date = NOW()";
						$DB2->SQL($query);
						$DB2->Query();
						$valutas++;
					}
					else
					{
						$onbekendekoers[] = "Valutakoers ".$data['Valuta']." : geen laatst ingevoerde koerswaarde gevonden!";
					}
				}
				else
				{
				  $query = "SELECT datum FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' AND datum <='".date("Y-m-d",form2jul($datum))."' ORDER BY datum DESC LIMIT 1";
		    	$DB2->SQL($query);
		    	$DB2->Query();
          $laatsteKoers=$DB2->NextRecord();
				  $onbekendekoers[] = "Valutakoers ".$data['Valuta']." op ".$datum." niet gevonden (laatste ".date('d-m-Y',db2jul($laatsteKoers['datum'])).")";
				}
			}
		}

		// check Fondsen

		$jaar = date("Y",form2jul($datum));

		// controle op einddatum portefeuille
		$extraquery  .= " Portefeuilles.Einddatum > '".$date."' AND";
    
    if(is_array($geimporteerdeFondsen))
      $extraquery .= " Rekeningmutaties.Fonds IN ('".implode("','",$geimporteerdeFondsen)."') AND";



		if($_POST['koersControleCheck']==1)
		  $koersControleCheck="AND Fondsen.koersControle='0' ";

    $q="SELECT
round(Sum(Rekeningmutaties.Aantal),4) AS Aantal,
Rekeningmutaties.Fonds,
Portefeuilles.Portefeuille
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.Einddatum > '".$date."'
WHERE $extraquery
Rekeningmutaties.Boekdatum >= '".$jaar."-01-01' AND Rekeningmutaties.Boekdatum <= '".$date."' AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds,Portefeuilles.Portefeuille
HAVING Aantal <> 0 
ORDER BY Rekeningmutaties.Fonds";

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();

		$fondsen = array();
    while($data=$DB->nextRecord())
    {
      if(!isset($fondsen[$data['Fonds']]))
      {
        $fondsen[$data['Fonds']]=array();
        $fondsen[$data['Fonds']]['Aantal']=0;
				$fondsen[$data['Fonds']]['PortefeuilleAantal']=0;
      }
			$fondsen[$data['Fonds']]['PortefeuilleAantal']++;
      $fondsen[$data['Fonds']]['Aantal']+=$data['Aantal'];
    }
  
  
	  $q = "SELECT Indices.Beursindex FROM Indices GROUP BY Indices.Beursindex ORDER BY Indices.Beursindex";
		$DB->SQL($q);
		$DB->Query();
		while($fonds = $DB->NextRecord())
    {
      if(!isset($fondsen[$fonds['Beursindex']]))
      {
        $fondsen[$fonds['Beursindex']]=array();
        $fondsen[$fonds['Beursindex']]['Aantal']=0;
      }
    }
		$q = "SELECT Fondsen.Fonds,Fondsen.EindDatum FROM Fondsen WHERE Fondsen.fondssoort='INDEX' AND Fondsen.EindDatum='0000-00-00'";
		$DB->SQL($q);
		$DB->Query();
		while($fonds = $DB->NextRecord())
		{
			if(!isset($fondsen[$fonds['Fonds']]))
			{
				$fondsen[$fonds['Fonds']]=array();
				$fondsen[$fonds['Fonds']]['Aantal']=0;
			}
		}

		$prb->moveStep(0);
		$prb->setLabelValue('txt1','Ophalen van actieve fondsen...');
		$pro_step = 0;

		$pro_multiplier = (100 / (count($fondsen)+1) );
		$prb->moveStep(0);
		$prb->setLabelValue('txt1','Koers controle...');
		$pro_step = 0;

		$DB2 	= new DB();
		// clean array
		$xlsData=array(array('Fonds','FondsImportCode','fondssoort','koersControleOverslaan',
                         'laatste datum','laatste koers','voorLaatste datum','voorlaatste koers','verschil','percentage','absoluut%','add_date','label','koersmethodiek','aantalPortefeuilles'));
		foreach ($fondsen as $fonds=>$fondsData)
		{
		  $query="SELECT Fonds,FondsImportCode,ISINCode,identifierVWD,identifierFactSet,
      koersmethodiek,Fondseenheid,Einddatum,Lossingsdatum,OptieExpDatum,Valuta,KoersAltijdAanvragen,fondssoort,koersControle
      FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."' AND (Einddatum > now() OR Einddatum < '1980-01-01') $koersControleCheck";
      $DB2->SQL($query);
      $fondsInfo=$DB2->lookupRecord();
 			$pro_step += $pro_multiplier;
 			$prb->moveStep($pro_step);

      if(isset($fondsInfo['Fonds']))
      {
 		  	if($_POST['ouderdom']==1)
 		  	{
 			    $query = "SELECT Koers, datum, add_date FROM Fondskoersen WHERE Fonds = '".$fonds."' AND datum <= '".$date."' ORDER BY datum DESC LIMIT 2";
			  	$DB2->SQL($query); 
			  	$DB2->Query();
          $koersen=array();
				  while ($data=$DB2->nextRecord())
			  	{ 
			  	  $koersen[]=$data;
			  	}
          $verschil='';
          $procentVerschil='';
          if($koersen[0]['Koers'] <> '' && $koersen[1]['Koers'] <>'')
          {
            $verschil=$koersen[0]['Koers']-$koersen[1]['Koers'];
            $procentVerschil=round($verschil/$koersen[1]['Koers']*100,3);
          }
          if(isset($labelPerRegel[$fondsInfo['Fonds']]))
          {
            $label=$labelPerRegel[$fondsInfo['Fonds']];
            unset($labelPerRegel[$fondsInfo['Fonds']]);
          }
          else
            $label='geen koersupdate';
          
          $tmp=array($fonds,$fondsInfo['FondsImportCode'],$fondsInfo['fondssoort'],$fondsInfo['koersControle'], 
             substr($koersen[0]['datum'],0,10),$koersen[0]['Koers'],
             substr($koersen[1]['datum'],0,10),$koersen[1]['Koers'],
             round($verschil,3),
             $procentVerschil,
             abs($procentVerschil),
             date('d-m-Y',db2jul($koersen[0]['add_date'])),
             $label,
             $fondsInfo['koersmethodiek'],
						 $fondsData['PortefeuilleAantal']);
			  	$xlsData[]=$tmp;
 		  	}
 		  	else
 		  	{
		  	  $query = "SELECT id FROM Fondskoersen WHERE datum = '".$date."' AND Fonds = '".$fonds."'";
		    	$DB2->SQL($query);
		    	$DB2->Query();
		    	if($DB2->Records() <= 0)
			    {
			  	// if aanvullen, aanvullen koers, haal laatste op!
			    	if($aanvullen == 1)
			    	{
				  	//select
				    	$query = "SELECT Koers FROM Fondskoersen WHERE Fonds = '".$fonds."' ORDER BY datum DESC LIMIT 1";
				    	$DB2->SQL($query);
				    	$DB2->Query();
				    	if($DB2->Records() == 1)
				    	{
						    $koers = $DB2->NextRecord();
						    $query = "INSERT INTO Fondskoersen SET datum = '".$date."' , Fonds = '".$fonds."', Koers = '".$koers['Koers']."', ".
							  		 " add_user = '".$USR."', ".
							  		 " add_date = NOW(), ".
							  		 " change_user = '".$USR."',".
							  		 " change_date = NOW()";
					    	$DB2->SQL($query);
					  	  $DB2->Query();
					  	  $aantalfondsen++;
					    }
					    else
					    {
					    	$onbekendekoers[] =  "Fondskoers ".$fonds." : geen laatst ingevoerde koerswaarde gevonden!";
					    }
				    }
				    else
				    {
				      $query = "SELECT datum FROM Fondskoersen WHERE Fonds = '".$fonds."' AND datum <='".date("Y-m-d",form2jul($datum))."' ORDER BY datum DESC LIMIT 1";
		    	    $DB2->SQL($query);
		    	    $DB2->Query();
              $laatsteKoers=$DB2->NextRecord();
				    	$onbekendekoers[] = "Fondskoers ".$fonds." op ".$datum." niet gevonden (laatste ".date('d-m-Y',db2jul($laatsteKoers['datum'])).")";
				    }
			    }
 			  }
	  	}
    }
	}
	if($aanvullen ==1)
	{
		echo "<br>".vt("fondsen")." ".$aantalfondsen." ".vt("en")." ".$valutas." ".vt("valuta aangevuld").".";
	}


  if($_POST['ouderdom']==1)
  {
    if(isset($labelPerRegel))
    {
      $xlsData[]=array('inactieve fondsen.');
      foreach($labelPerRegel as $fonds=>$label)
      {
        $query="SELECT Fonds,FondsImportCode,fondssoort,koersControle,koersmethodiek FROM Fondsen WHERE Fonds='".mysql_real_escape_string($fonds)."' ";
        $DB2->SQL($query);
        $fondsInfo=$DB2->lookupRecord();
      
 			  $query = "SELECT Koers, datum, add_date FROM Fondskoersen WHERE Fonds = '".$fonds."' AND datum <= '".$date."' ORDER BY datum DESC LIMIT 2";
			  $DB2->SQL($query); 
			  $DB2->Query();
        $koersen=array();
				while ($data=$DB2->nextRecord())
			  { 
			    $koersen[]=$data;
			  }
        $verschil='';
        $procentVerschil='';
        if($koersen[0]['Koers'] <> '' && $koersen[1]['Koers'] <>'')
        {
          $verschil=$koersen[0]['Koers']-$koersen[1]['Koers'];
          $procentVerschil=round($verschil/$koersen[1]['Koers']*100,3);
        }
          
        $tmp=array($fonds,$fondsInfo['FondsImportCode'],$fondsInfo['fondssoort'],$fondsInfo['koersControle'], 
             substr($koersen[0]['datum'],0,10),$koersen[0]['Koers'],
             substr($koersen[1]['datum'],0,10),$koersen[1]['Koers'],
             round($verschil,3),
             $procentVerschil,
             abs($procentVerschil),
             date('d-m-Y',db2jul($koersen[0]['add_date'])),
             $label,
             $fondsInfo['koersmethodiek'],
					   $fondsData['PortefeuilleAantal']);
	 	    $xlsData[]=$tmp;
      } 
    }
		if(is_object($xls))
		{
			$xls->setData($xlsData);
			$xls->OutputXls($__appvar['tempdir'] . 'ouderdom.xls', true);
			echo "<br><br><br><a href='showTempfile.php?show=1&filename=ouderdom.xls&unlink=1' ><b>".vt("Download XLS file").".</b></a><br><br><br><br>".vt("Overige meldingen").":";
		}
  }



		for ($a=0; $a < count($onbekendekoers); $a++)
		{
			echo "<br>".$onbekendekoers[$a];
		}


	$prb->hide();
	echo $_error;
  if(!$_POST['noExit']==true)
  	exit;
}

if(!$_FILES['importfile']['name'])
{
	// get laatste valutaDatum
$laatsteValuta = getLaatsteValutadatum();




?>

<form action="koersControle.php" method="POST" target="importFrame" name="controleForm">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<input type="hidden" name="aanvullen" value="0" />
<input type="hidden" name="ouderdom" value="0" />
<!-- Name of input element determines name in $_FILES array -->
<b><?=vt("Koerscontrole")?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>

<div class="form">
<div class="formblock">
<div class="formlinks"> <?=vt("Datum opgeven")?></div>
<div class="formrechts">
<input type="text" name="datum" value="<?=date("d-m-Y",db2jul($laatsteValuta))?>" size="15">
</div>
</div>


<div class="form">
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="checkbox" name="koersControleCheck" value="1" checked> <?=vt("Te controleren Fondsen")?>
</div>
</div>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
 <input type="button" value="<?=vt("Controleren")?>" onClick="document.controleForm.aanvullen.value='0';document.controleForm.submit();">
<?

if(checkAccess("superapp") &! $__appvar['master'])
{
?>
<input type="button" value="<?=vt("Koersen aanvullen")?>"  onClick="document.controleForm.aanvullen.value='1';document.controleForm.submit();document.controleForm.aanvullen.value='0';">
&nbsp;&nbsp;&nbsp;&nbsp;
<?
}
?>

<input type="button" value="<?=vt("Ouderdoms analyse")?>"  onClick="document.controleForm.ouderdom.value='1';document.controleForm.submit();document.controleForm.ouderdom.value='0';">
</div>
</div>
</form>
</div>

<?
if($__appvar['master'])
{
?>
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<form action="bepaalActieveFondsen.php" method="POST" target="importFrame">
<input type="submit" value="<?=vt("Bepaal actieve fondsen")?>">
</form>
</div>
</div>
<?
}
?>

<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<iframe width="600" height="400" name="importFrame"></iframe>
</div>
</div>

</div>
<?
}
echo template($__appvar["templateRefreshFooter"],$content);
