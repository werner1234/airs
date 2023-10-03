<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIS_L12
{
	function RapportOIS_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "";//Onderverdeling in beleggingssector";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->totaalWaarde=0;
	}


	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	
	function maakPortefeuille()
  {
    $DB=new DB();
    if(is_array($this->pdf->portefeuilles))
    {
      $crm=new Naw();
      if(isset($crm->data['fields']['Portefeuillesoort']))
        $portefeuilleSoort='CRM_naw.Portefeuillesoort';
      else
        $portefeuilleSoort='Portefeuilles.soortOvereenkomst';
  
      if(isset($crm->data['fields']['RappNaam']))
        $naamVeld='CRM_naw.RappNaam as RappNaam';
      else
        $naamVeld='Portefeuilles.Selectieveld2 as RappNaam';

      $soortOvereenkomst=array();
      $query="SELECT Portefeuilles.Portefeuille,Portefeuilles.ClientVermogensbeheerder,Portefeuilles.Selectieveld1,$naamVeld ,$portefeuilleSoort as soortOvereenkomst
FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille WHERE Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."') ORDER BY soortOvereenkomst desc, Portefeuilles.Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($portefeuille = $DB->NextRecord())
      {
        $soortOvereenkomst[$portefeuille['Portefeuille']]=$portefeuille;
      }
      
      $correctieVelden=array('totaalAantal','actuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
      $tmp=array();
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
    //    echo $this->rapportageDatumVanaf ." ".(substr($this->rapportageDatumVanaf ,5,5)=='01-01'?true:false)."<br>\n";exit;
        $fondswaarden = berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum,(substr($this->rapportageDatum ,5,5)=='01-01'?true:false),'EUR',$this->rapportageDatumVanaf);

        foreach ($fondswaarden as $instrument)
        {
            unset($instrument['voorgaandejarenactief']);
  
          if($soortOvereenkomst[$portefeuille]['soortOvereenkomst']=='Effecten')// || $portefeuille=='JAMADVWAT')
          {
            $instrument['fondsOmschrijving']=($soortOvereenkomst[$portefeuille]['RappNaam']<>''?$soortOvereenkomst[$portefeuille]['RappNaam']:$portefeuille);
            $instrument['totaalAantal']=0;
            $instrument['actueleFonds']=0;
            $instrument['beleggingssector']='';
            $instrument['beleggingssectorOmschrijving']='';
            $instrument['beleggingssectorVolgorde']='127';
            $instrument['rekening']='';//'Rekening '.($soortOvereenkomst[$portefeuille]['Selectieveld2']<>''?$soortOvereenkomst[$portefeuille]['Selectieveld2']:$portefeuille);
            $instrument['valuta']='EUR';
            $key = '|' . $instrument['beleggingscategorie']. '|';
          }
          else
          {
            $key = '|' . $instrument['type'] . '|' . $instrument['fonds'] . '|' . $instrument['rekening'] . '|';
          }
            if (isset($tmp[$key]))
            {
              foreach ($correctieVelden as $veld)
              {
                $veld = ($veld);
                $tmp[$key][$veld] += $instrument[$veld];
              }
            }
            else
            {
              $tmp[$key] = $instrument;
            }
            //	listarray($instrument);
        }
        
      
        
      }
      $fondswaarden  = array_values($tmp);
      $portefeuille=substr($portefeuille,0,17).'GRAFIEK';
      vulTijdelijkeTabel($fondswaarden, $portefeuille, $this->rapportageDatum);
      return $portefeuille;
    //  listarray($fondswaarden);
    //  listarray($soortOvereenkomst);
    }
    else
    {
      return $this->portefeuille;
    }
    
  
  }

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;
	  return number_format($waarde,$dec,",",".");
  }

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !isset($newDec))
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}


	function printTotaal($title, $totaalA, $totaalB, $procent='', $grandtotaal = false)
	{
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
		// lege regel
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4] + $this->pdf->widthB[5] + $this->pdf->widthB[6] + $this->pdf->widthB[7] ;

		if(!empty($totaalB))
			$totaalBtxt = $this->formatGetalKoers($totaalB,$this->pdf->rapport_OIS_decimaal);
    else
      return 0;  

		$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[8],$this->pdf->GetY());

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->SetX(0);

		$this->pdf->Cell($actueel,4, $title, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		//$this->pdf->Cell($this->pdf->widthB[6],4, "", 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[8],4,$totaalBtxt, 0,0, "R");

		$this->pdf->Cell($this->pdf->widthB[9],4,$procent, 0,1, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($grandtotaal)
		{
		//	$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[8],$this->pdf->GetY());
		//	$this->pdf->Line($actueel,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[8],$this->pdf->GetY()+1);
		}
		else
		{
			//$this->pdf->setDash(1,1);
			$this->pdf->Line($actueel,$this->pdf->GetY(),$actueel + $this->pdf->widthB[8],$this->pdf->GetY());
		//	$this->pdf->setDash();
		}

		//$this->pdf->ln();

		return $totaalB;
	}

	function printKop($title, $procent, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);


    $afronding=1;
 	  $procenttxt = $this->formatGetal($procent,$afronding)." %";
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell($this->pdf->widthB[0],4, $procenttxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[1],4, $title, 0,1, "L");
		$this->pdf->line($this->pdf->marge+$this->pdf->widthB[0],$this->pdf->getY(),$this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1],$this->pdf->getY(),array('color'=>array($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2])));
	}

	function addRente($categorie)
	{
		//echo "$categorie <br>\n";
		global $__appvar;
		// selecteer rente
		$actueleWaardePortefeuille=0;
		$query = "SELECT TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalValuta, ".
			" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) subtotaalbegin, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
			" TijdelijkeRapportage LEFT JOIN Valutas on (TijdelijkeRapportage.valuta = Valutas.Valuta) ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rente'  AND TijdelijkeRapportage.Beleggingscategorie='$categorie' AND".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie ".
			" ORDER BY Valutas.Afdrukvolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		if($DB->records() > 0)
		{
			$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS rentetotaal FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rente'  AND TijdelijkeRapportage.Beleggingscategorie='$categorie' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($q,__FILE__,__LINE__);
			$DB3 = new DB();
			$DB3->SQL($q);
			$DB3->Query();
			$subtotaal = $DB3->nextRecord();
			$subtotaal = $subtotaal['rentetotaal'];
			$percentageVanTotaal = $subtotaal/ ($this->totaalWaarde/100);
			if($subtotaal>0)
		  	$this->printKop(vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal),$percentageVanTotaal ,"b");
			else
				$this->printKop(vertaalTekst("Verschuldigde rente",$this->pdf->rapport_taal),$percentageVanTotaal ,"b");
			$totaalRenteInValuta = 0 ;
			while($categorien = $DB->NextRecord())
			{

				$subtotaalRenteInValuta = 0;
				$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
					" TijdelijkeRapportage.actueleValuta , ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
					" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
					" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
					" FROM TijdelijkeRapportage WHERE ".
					" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
					" TijdelijkeRapportage.type = 'rente'  AND ".
					" TijdelijkeRapportage.beleggingscategorie = '".$categorien['beleggingscategorie']."' AND ".
					" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
					.$__appvar['TijdelijkeRapportageMaakUniek'].
					" ORDER BY TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";
				debugSpecial($subquery,__FILE__,__LINE__);
				$DB2 = new DB();
				$DB2->SQL($subquery);
				$DB2->Query();
        $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
				while($subdata = $DB2->NextRecord())
				{
					$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($this->totaalWaarde/100);
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
					$subtotaalRenteInValuta += $subdata['actuelePortefeuilleWaardeEuro'];
					$this->pdf->SetWidths($this->pdf->widthB);
					$this->pdf->SetAligns($this->pdf->alignB);
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
					$this->pdf->row(array("","",	"",$subdata['fondsOmschrijving'],
														"",
														$this->formatGetal($subdata['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_OIS_decimaal),
														$subdata['valuta'],
														"",
														$this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
														$percentageVanTotaaltxt));
				}
				// print subtotaal
				$totaalRenteInValuta += $subtotaalRenteInValuta;
			}
			// totaal op rente
			$actueleWaardePortefeuille = $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), "",$totaalRenteInValuta);
		}
		return $actueleWaardePortefeuille;
	}

	function writeRapport()
	{
		global $__appvar;
		// voor data


		// voor kopjes
    if($this->pdf->rapport_type=='OIB')
      $this->pdf->widthA = array(12,55,5,58,25,25,24,12,28,17,20);
    else
  		$this->pdf->widthA = array(12,55,5,78,25,25,24,12,28,17);
		$this->pdf->alignA = array('R','L','L','L','R','R','R','L','R','R','R');
    $this->pdf->widthB = $this->pdf->widthA;
    $this->pdf->alignB = $this->pdf->alignA;
      
    $this->pdf->AddPage();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->SetLineWidth(0.2);

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
		$this->totaalWaarde=$totaalWaarde;


		$actueleWaardePortefeuille = 0;
		$lastBeleggingscategorie='leeg';
		$renteCategorieen=array();

		$query = "SELECT  TijdelijkeRapportage.beleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.BeleggingssectorOmschrijving AS secOmschrijving , ".
		" TijdelijkeRapportage.beleggingssector, ".
		" TijdelijkeRapportage.valuta, ".
		" TijdelijkeRapportage.beleggingscategorie, ".
		" SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro) AS subtotaalbegin, ".
		" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
		" FROM (TijdelijkeRapportage) ".
		" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
		" TijdelijkeRapportage.type IN('fondsen','rekening') AND ".
		" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
		.$__appvar['TijdelijkeRapportageMaakUniek'].
		" GROUP BY TijdelijkeRapportage.beleggingscategorie  ".
		" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.beleggingssectorVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB = new DB();
		$DB->SQL($query); //echo "catt $query<br>\n";exit;
		$DB->Query();
    $lastCategorie2='';
    $totaalbegin = 0;
    $totaalactueel = 0;
    $totaalLiquiditeitenEuro = 0;
		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			// print totaal op hele categorie.

			if($lastCategorie2 <> $categorien['Omschrijving'] && !empty($lastCategorie2) )
			{
				$title = vertaalTekst("Subtotaal",$this->pdf->rapport_taal);
				$actueleWaardePortefeuille += $this->printTotaal($title, $totaalbegin, $totaalactueel);
				$percentageVanTotaal_totaal = 0;
				$totaalbegin = 0;
				$totaalactueel = 0;

			}
			if(!in_array($lastBeleggingscategorie,$renteCategorieen))
      {
        $actueleWaardePortefeuille += $this->addRente($lastBeleggingscategorie);
        $renteCategorieen[] = $lastBeleggingscategorie;
      }
			if($lastCategorie2 <> $categorien['Omschrijving'])
			{
    
				$percentageVanTotaal = $categorien['subtotaalactueel']/ ($totaalWaarde/100); //$totaalWaarde
				$this->printKop(vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal),$percentageVanTotaal, "b");
				$secTel =0;
			}
			// subkop (valuta)
			// print detail (select from tijdelijkeRapportage)

			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.beleggingssector, ".
			" TijdelijkeRapportage.beleggingssectorOmschrijving AS secOmschrijving, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.actueleFonds, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" TijdelijkeRapportage.valuta, ".
        " TijdelijkeRapportage.Bewaarder,TijdelijkeRapportage.type, TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.fonds, ".
			" TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage ".
			" WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.beleggingscategorie =  '".$categorien['beleggingscategorie']."' AND ".
			" TijdelijkeRapportage.type IN ('fondsen','rekening') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.beleggingssectorVolgorde asc, TijdelijkeRapportage.Lossingsdatum, TijdelijkeRapportage.fondsOmschrijving asc";

			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery); //echo "subq $subquery <br>\n<br>\n";
			$DB2->Query();

			$lastCategorie = "xx";
			$secTel = 0;
			while($subdata = $DB2->NextRecord())
			{
				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				if($lastCategorie <> $subdata['secOmschrijving'])
				{
					// selecteer sum van deze sector... en dan :

					$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS sectortotaal FROM TijdelijkeRapportage ".
							 " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
						   " TijdelijkeRapportage.beleggingscategorie =  '".$subdata['beleggingscategorie']."' AND ".
						   " TijdelijkeRapportage.beleggingssector =  '".$subdata['beleggingssector']."' AND ".
						   " TijdelijkeRapportage.type IN ('fondsen','rekening') AND ".
							 " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
							 .$__appvar['TijdelijkeRapportageMaakUniek'];
					debugSpecial($q,__FILE__,__LINE__);
					$DB3 = new DB();
					$DB3->SQL($q); //echo "q $q <br>\n";
					$DB3->Query();
					$subtotaal = $DB3->nextRecord();
					$subtotaal = $subtotaal['sectortotaal'];


					$percentageVanTotaal =round($subtotaal/ ($categorien['subtotaalactueel']/100),1);
          //$percentageVanTotaal =round($subtotaal/ ($this->totaalWaarde/100),1);
						//echo $categorien[Omschrijving]." ".$subdata['beleggingssector']." ".$percentageVanTotaal."<br>\n";
            $percentageVanTotaaltxt=$this->formatGetal($percentageVanTotaal,1).' %';
						  //nog geen percentage tonen, pas later bij fondsregels
						//  $percentageVanTotaaltxt = '';
						  $fondsPercentageweergeven = true;
		
//    $this->pdf->CellFontColor=array($this->pdf->rapport_kop_fontcolor,$this->pdf->rapport_fontcolor);
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				  $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
					$this->pdf->Cell($this->pdf->widthB[0],4, ($subdata['secOmschrijving']<>''?$percentageVanTotaaltxt:''), 0,0, "R");
					$this->pdf->Cell($this->pdf->widthB[1],4, $subdata['secOmschrijving'], 0,0, "L");
          $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
        //  $this->pdf->line($this->pdf->marge+$this->pdf->widthB[0],$this->pdf->getY()+4,$this->pdf->marge+$this->pdf->widthB[0]+$this->pdf->widthB[1],$this->pdf->getY()+4,array('color'=>array(128,128,128)));
					$this->pdf->SetX($this->pdf->marge);
				}


  			$percentageVanTotaal = $subdata['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);
				$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        
        if($this->pdf->rapport_type=='OIB')
        {
          if($subdata['Bewaarder']=='TGB')
            $subdata['Bewaarder']='IG';
          $this->pdf->row(array('',//$this->formatGetal(($subdata['actuelePortefeuilleWaardeEuro']/$categorien['subtotaalactueel'])*100,$afronding).' %',
                            "", "",
                            trim($subdata['fondsOmschrijving'].' '.$subdata['rekening']),
                            ($subdata['type']=='rekening'?'':$this->formatAantal($subdata['totaalAantal'], 0, $this->pdf->rapport_OIS_aantalVierDecimaal)),
                            ($subdata['type']=='rekening'?$this->formatAantal($subdata['actuelePortefeuilleWaardeInValuta'],2):$this->formatGetal($subdata['actueleFonds'], 2)),
                            $subdata['valuta'],
                            "",
                            $this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_OIS_decimaal),
                            $percentageVanTotaaltxt,
                            $subdata['Bewaarder']));
        }
        else
        {
          $this->pdf->row(array('',//$this->formatGetal(($subdata['actuelePortefeuilleWaardeEuro']/$categorien['subtotaalactueel'])*100,$afronding).' %',
                            "", "",
                            trim($subdata['fondsOmschrijving'].' '.$subdata['rekening']),
                            ($subdata['type']=='rekening'?'':$this->formatAantal($subdata['totaalAantal'], 0, $this->pdf->rapport_OIS_aantalVierDecimaal)),
                            ($subdata['type']=='rekening'?$this->formatAantal($subdata['actuelePortefeuilleWaardeInValuta'],2):$this->formatGetal($subdata['actueleFonds'], 2)),
                            $subdata['valuta'],
                            "",
                            $this->formatGetalKoers($subdata['actuelePortefeuilleWaardeEuro'], $this->pdf->rapport_OIS_decimaal),
                            $percentageVanTotaaltxt));
        }

				$percentageVanTotaal_totaal += $percentageVanTotaal;

				$valutaWaarden[$categorien['valuta']] = $subdata['actueleValuta'];
				$lastCategorie = $subdata['secOmschrijving'];
			}
			$lastBeleggingscategorie=$categorien['beleggingscategorie'];

			// print categorie footers
			//$this->printSubTotaal("Subtotaal:", $categorien[subtotaalbegin], $categorien[subtotaalactueel]);

			// totaal op categorie tellen
			$totaalbegin += $categorien['subtotaalbegin'];
			$totaalactueel += $categorien['subtotaalactueel'];
			$lastCategorie2 = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie
		//$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel,$percentageVanTotaal_totaal);

		$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), $totaalbegin, $totaalactueel);
    if(!in_array($lastBeleggingscategorie,$renteCategorieen))
    {
      $actueleWaardePortefeuille += $this->addRente($lastBeleggingscategorie);
    }
		$percentageVanTotaal_totaal = 0;


/*
		// Liquiditeiten
		$q = "SELECT SUM(actuelePortefeuilleWaardeEuro)AS liqtotaal FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.type = 'rekening'  AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($q,__FILE__,__LINE__);
		$DB3 = new DB();
		$DB3->SQL($q);
		$DB3->Query();
		$subtotaal = $DB3->nextRecord();
		$subtotaal = $subtotaal['liqtotaal'];


			$percentageVanTotaal = $subtotaal/ ($totaalWaarde/100);
			$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,0)." %";


		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , TijdelijkeRapportage.beleggingscategorie, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.rekening, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening' ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY SUBSTR(TijdelijkeRapportage.rekening,1,LENGTH(TijdelijkeRapportage.rekening)-3), TijdelijkeRapportage.valutaVolgorde, TijdelijkeRapportage.fondsOmschrijving";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		if($DB1->records() > 0)
		{
			$this->printKop(vertaalTekst("Liquiditeiten",$this->pdf->rapport_taal),$percentageVanTotaal,"b");

			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			while($data = $DB1->NextRecord())
			{
				if($this->pdf->rapport_layout == 5 || $this->pdf->rapport_layout == 12)
				{
					$percentageVanTotaal = $data['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);
					$percentageVanTotaaltxt = $this->formatGetal($percentageVanTotaal,1);
				}
				else
				{
					$percentageVanTotaaltxt = "";
				}

				if($this->pdf->rapport_OIS_liquiditeiten_omschr)
					$this->pdf->rapport_liquiditeiten_omschr = $this->pdf->rapport_OIS_liquiditeiten_omschr;

				$omschrijving = $this->pdf->rapport_liquiditeiten_omschr;
				$omschrijving = vertaalTekst(str_replace("{Rekening}",$data['rekening'],$omschrijving),$this->pdf->rapport_taal);
				$omschrijving = str_replace("{Tenaamstelling}",vertaalTekst($data['fondsOmschrijving'],$this->pdf->rapport_taal),$omschrijving);
				$omschrijving = vertaalTekst(str_replace("{Valuta}",$data['valuta'],$omschrijving),$this->pdf->rapport_taal);

				$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

				$this->pdf->SetWidths($this->pdf->widthB);
				$this->pdf->SetAligns($this->pdf->alignB);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$this->pdf->row(array("",
												"",	"",
												$omschrijving,
												"",
												$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_OIS_decimaal),
												$data['valuta'],
												"",
												$this->formatGetalKoers($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIS_decimaal),
												$percentageVanTotaaltxt));
				$lastBeleggingscategorie=$data['beleggingscategorie'];
			}
			// totaal liquiditeiten
			$actueleWaardePortefeuille += $this->printTotaal(vertaalTekst("Subtotaal",$this->pdf->rapport_taal), "", $totaalLiquiditeitenEuro);
      $lastBeleggingscategorie='Liquiditeiten';
		}
		*/
    //if($lastBeleggingscategorie=='')
		//	$lastBeleggingscategorie='Liquiditeiten';
		//$actueleWaardePortefeuille+=$this->addRente($lastBeleggingscategorie);


		// check op totaalwaarde!

		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		$actueleWaardePortefeuille = $totaalWaarde;
		// print grandtotaal


			$totaalTxt = $this->formatGetal(100,1);


		$this->printTotaal(vertaalTekst("Totaal",$this->pdf->rapport_taal), "", $actueleWaardePortefeuille , $totaalTxt, true);


	}
  
    function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}
}
?>