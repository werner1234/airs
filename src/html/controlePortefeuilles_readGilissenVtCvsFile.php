<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/09/14 09:26:56 $
 		File Versie					: $Revision: 1.3 $

 		$Log: controlePortefeuilles_readGilissenVtCvsFile.php,v $
 		Revision 1.3  2011/09/14 09:26:56  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/06/06 09:04:22  rvv
 		*** empty log message ***

 		Revision 1.1  2007/04/20 12:19:31  rvv
 		*** empty log message ***



 		functie in controlePortefeuilles.php
*/


function readGilissenVtCvsFile($filename,$vermogensbeheerder)
{
	global $ndx, $csvRegels,$prb,$outputArray,$error,$DB,$DB1,$datum,$portefeuilleArray,$tijd;

	$start = mktime();
	$error = array();
	if (!$handle = @fopen($filename, "r"))
	{
		$error[] = "FOUT bestand $filename is niet leesbaar";
		return false;
	}
	$csvRegels = Count(file($filename));

  $pro_multiplier = 100/$csvRegels;
  $row = 0;
  $ndx= 0;

  $prb->setLabelValue('txt1','inlezen van CSV bestand ('.$csvRegels.' records)');
  while ($data = fgetcsv($handle, 1000, ";"))
  {
    $csv=array();

  if ($row !=0)
  {
    if ($data[0] == '') continue;  // sla lege regels over
    $csv['portefeuille']  = trim($data[0]);
    $csv['contractnr']    = trim($data[1]);
    $csv['begindatum']    = trim($data[2]);
    $csv['einddatum']     = trim($data[3]);
    $csv['valuta']        = trim($data[4]);
    $csv['waarde']        = trim($data[5]);
    $csv['tegenwaarde']   = trim($data[6]);
    $csv['remisier']      = trim($data[7]);

    if ($csv['valuta'] == 'U$')
      $csv['valuta'] = 'USD';

    if ($csv['waarde'] < 0)
     $csv['tegenwaarde'] =  $csv['tegenwaarde'] * -1;

    $begindatum=  $csv['begindatum'];

    if(db2jul($datum) > mktime(0,0,0,substr($begindatum,4,2),substr($begindatum,6,2),substr($begindatum,0,4)))
    {
    $portefeuilleArray[$csv['portefeuille']]['csvValuta'][$csv['valuta']]['waarde']      += $csv['waarde'];
    $portefeuilleArray[$csv['portefeuille']]['csvValuta'][$csv['valuta']]['tegenwaarde'] += $csv['tegenwaarde'];
    $portefeuilleArray[$csv['portefeuille']]['contractnr'] = $csv['contractnr'];
    $portefeuilleArray[$csv['portefeuille']]['einddatum'] = mktime(0,0,0,substr($csv['einddatum'],4,2),substr($csv['einddatum'],6,2),substr($csv['einddatum'],0,4));
    $portefeuilleArray[$csv['portefeuille']]['remisier']   = $csv['remisier'];
    }


  	$pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
  }
  $row++;
  }

  $DB = New DB();
  $allePortefeuilles = array();
  $query="SELECT Portefeuille FROM Portefeuilles WHERE Vermogensbeheerder = '".$vermogensbeheerder."' AND einddatum > NOW()";
  $DB->SQL($query);
  $DB->Query();
  while($data = $DB->NextRecord())
  {
    $allePortefeuilles[] = $data['Portefeuille'] ;
  }
 $missendePortefeuilles = array();
 $missendePortefeuilles = (array_diff($allePortefeuilles,array_keys($portefeuilleArray)));



  $prb->hide();

	$prb = new ProgressBar();	// create new ProgressBar
	$prb->pedding = 2;	// Bar Pedding
	$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
	$prb->setFrame();          	                // set ProgressBar Frame
	$prb->frame['left'] = 50;	                  // Frame position from left
	$prb->frame['top'] = 	80;	                  // Frame position from top
	$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
	$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
	$prb->show();

	$portefeuilles = count($portefeuilleArray);
  $pro_multiplier = 100 / $portefeuilles;
  $pro_step = 0;
  $prb->setLabelValue('txt1','Ophalen rekening gegevens ('.$portefeuilles.' portefeuilles)');


  while (list($portefeuille, $portefeuilleData) = each($portefeuilleArray))
  {
   		$rekArray =  berekenRekeningWaarde($portefeuille, $datum);

			// haal totaalwaarde op om % te berekenen
			$cash = array();
			for ($x=0;$x < count($rekArray);$x++)
			{
			    if ($rekArray[$x]["termijn"] <> 0)
			    {
			      $cash[$rekArray[$x]["valuta"]]["ter"] += $rekArray[$x]["bedrag"];
			      $cash[$rekArray[$x]["valuta"]]["teu"] += $rekArray[$x]["bedrag"] * $rekArray[$x]["koers"];
			    }
			}
			$portefeuilleArray[$portefeuille]['AirsValuta'] = $cash;

    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
  }
  $prb->hide();

	$prb = new ProgressBar();	// create new ProgressBar
	$prb->pedding = 2;	// Bar Pedding
	$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
	$prb->setFrame();          	                // set ProgressBar Frame
	$prb->frame['left'] = 50;	                  // Frame position from left
	$prb->frame['top'] = 	80;	                  // Frame position from top
	$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
	$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
	$prb->show();

	$portefeuilles = count($missendePortefeuilles);
  $pro_multiplier = 100 / $portefeuilles;
  $pro_step = 0;
  $prb->setLabelValue('txt1','Ophalen portefeuilles('.$portefeuilles.') zonder csv data');
  foreach ($missendePortefeuilles as $portefeuille) //portefeuilles zonder bank vt informatie ook testen.
  {
  		$rekArray =  berekenRekeningWaarde($portefeuille, $datum);
			// haal totaalwaarde op om % te berekenen
			$cash = array();
			for ($x=0;$x < count($rekArray);$x++)
			{
			    if ($rekArray[$x]["termijn"] <> 0)
			    {
			      $cash[$rekArray[$x]["valuta"]]["ter"] += $rekArray[$x]["bedrag"];
			      $cash[$rekArray[$x]["valuta"]]["teu"] += $rekArray[$x]["bedrag"] * $rekArray[$x]["koers"];
			    }
			}
			if ($cash['EUR']["teu"] > 0)
			{
			  $portefeuilleArray[$portefeuille]['AirsValuta'] = $cash;
			  $portefeuilleArray[$portefeuille]['csvValuta'] = 'niet aanwezig';

			}

    $pro_step += $pro_multiplier;
    $prb->moveStep($pro_step);
  }

  $n=0;
  reset($portefeuilleArray);
  while (list($portefeuille, $portefeuilleData) = each($portefeuilleArray))
  {
    if ($portefeuilleData['csvValuta'] == 'niet aanwezig')
    {
      while (list($valuta, $valutaData) = each($portefeuilleData['AirsValuta']))
      {
        $portefeuilleArray[$portefeuille]['verschil'][$valuta]['waarde']      = 0 - $portefeuilleData['AirsValuta'][$valuta]['ter'] ;
      }
    }
    else
    {
      while (list($valuta, $valutaData) = each($portefeuilleData['csvValuta']))
      {
        $portefeuilleArray[$portefeuille]['verschil'][$valuta]['waarde']      = $portefeuilleData['csvValuta'][$valuta]['waarde'] - $portefeuilleData['AirsValuta'][$valuta]['ter'] ;
        $portefeuilleArray[$portefeuille]['verschil'][$valuta]['tegenwaarde'] = $portefeuilleData['csvValuta'][$valuta]['tegenwaarde'] + $portefeuilleData['AirsValuta']['EUR']['teu'] ;
      }
    }

    $tmpDB = New DB();
    $query = "
    SELECT
    Clienten.Naam,
    Portefeuilles.Client
    FROM
    Clienten ,
    Portefeuilles
    WHERE Portefeuilles.Client = Clienten.Client
    AND
    Portefeuilles.Portefeuille = '$portefeuille'";

    $tmpDB->SQL($query);
    if( $data = $tmpDB->lookupRecord())
    {
    $portefeuilleArray[$portefeuille]['naam']   = $data['Naam'];
    $portefeuilleArray[$portefeuille]['client'] = $data['Client'];
    }
    else
    {
    $portefeuilleArray[$portefeuille]['naam']   = 'n/a '.$n;
    $portefeuilleArray[$portefeuille]['client'] = 'n/a '.$n;
    }
    $n++;
  }


 // print_r($portefeuilleArray);
 // InsertAIRSsection($portefeuilleInCsv);

  fclose($handle);

  $tijd = mktime() - $start;
  unlink($filename);
  if (Count($error) == 0)
  	return true;
  else
  	return false;

}
?>