<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/15 16:36:16 $
 		File Versie					: $Revision: 1.166 $

 		$Log: FactuurRekenClass.php,v $
 		Revision 1.166  2020/07/15 16:36:16  rvv
 		*** empty log message ***
 		
*/

class factuurBerekening
{
	function factuurBerekening($portefeuille, $vandatum, $tmdatum,$drempelPercentage,$tijdelijkeTabelVullen = true)
	{
		// ***************************** ophalen data voor afdruk ************************ //
		global $__appvar;

		$this->portefeuille = $portefeuille;

		$this->DB = new DB();
		$this->DB2 = new DB();


		$query="CREATE TEMPORARY TABLE IF NOT EXISTS `tempFactuur` ( `id` int(11) NOT NULL AUTO_INCREMENT, `sleutel` varchar(100) NOT NULL, `gegevens` mediumtext NOT NULL, PRIMARY KEY (`id`),  KEY `sleutel` (`sleutel`) )";
		$this->DB2->SQL($query);
		$this->DB2->Query();

		$query = "SELECT Clienten.* ".
			" FROM Portefeuilles, Clienten ".
			" WHERE ".
			" Portefeuilles.Client = Clienten.Client AND ".
			" Portefeuilles.Portefeuille = '".$this->portefeuille."'";

		$this->DB->SQL($query);
		$this->DB->Query();
		$this->clientdata = $this->DB->nextRecord();

		$query = "SELECT Portefeuilles.* , Depotbanken.Omschrijving as depotbankOmschrijving ,Vermogensbeheerders.PerformanceBerekening,Vermogensbeheerders.CrmClientNaam,
		          Vermogensbeheerders.FactuurMinimumPerTransactie, Vermogensbeheerders.FactuurMinimumBedrag, Vermogensbeheerders.bestandsvergoedingBtw, Vermogensbeheerders.module_bestandsvergoeding,
              Vermogensbeheerders.BeheerfeeAdministratieVergoedingVast,Vermogensbeheerders.Layout
		          FROM
		          Portefeuilles
		          LEFT JOIN Depotbanken ON Portefeuilles.depotbank =  Depotbanken.depotbank
		          JOIN Vermogensbeheerders  ON Portefeuilles.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder
		          WHERE Portefeuilles.Einddatum > '$tmdatum' AND Portefeuilles.Portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$this->portefeuilledata = $this->DB->NextRecord();
    $this->portefeuilledata['portefeuilles_BeheerfeeBedragBuitenFee'][$this->portefeuille] = $this->portefeuilledata['BeheerfeeBedragBuitenFee'];

		if($this->portefeuilledata['afrekenvalutaKosten']=='')
			$this->portefeuilledata['afrekenvalutaKosten']='EUR';

    $somVelden=array('BeheerfeeBedragBuitenFee');
		if($this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille'] <> '' && $this->portefeuilledata['feeToevoegMethode'] <> 0)
		{
			$query="SELECT valutaUitsluiten,BeheerfeeBedragVast,BeheerfeeMethode,BeheerfeeBasisberekening,BeheerfeeKortingspercentage,BeheerfeePercentageVermogen,BeheerfeeBedrag,BeheerfeePerformancePercentage,BeheerfeeTeruggaveHuisfondsenPercentage,BeheerfeeRemisiervergoedingsPercentage,BeheerfeeAantalFacturen,BeheerfeeStaffel1,BeheerfeeStaffel2,BeheerfeeStaffel3,BeheerfeeStaffel4,BeheerfeeStaffel5,BeheerfeeStaffelPercentage1,BeheerfeeStaffelPercentage2,BeheerfeeStaffelPercentage3,BeheerfeeStaffelPercentage4,BeheerfeeStaffelPercentage5,BeheerfeeAdministratieVergoeding,BeheerfeeMinJaarBedrag,BeheerfeePerformanceDrempelPercentage,BeheerfeePerformanceDrempelBedrag,BeheerfeeSchijvenTarief,BeheerfeePerformancefeeJaarlijks,BeheerfeeFacturatieVanaf,BeheerfeeFacturatieVooraf,BeheerfeeBedragBuitenFee,BeheerfeeLiquiditeitenViaModel FROM Portefeuilles WHERE Portefeuilles.Portefeuille='".$this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille']."'"; //BeheerfeeBTW,
			//$query="SELECT  BeheerfeeMethode FROM Portefeuilles WHERE Portefeuilles.Portefeuille='".$this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille']."'";
			$this->DB->SQL($query);
			$this->DB->Query();
			$data = $this->DB->NextRecord();
			foreach ($data as $key=>$value)
      {
        if(in_array($key,$somVelden))
          $this->portefeuilledata[$key] += $value;
        else
          $this->portefeuilledata[$key] = $value;
      }


			if($this->portefeuilledata['feeToevoegMethode'] == 2 )
			{
				//$hfactuur= new factuurBerekening($this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille'],$vandatum, $tmdatum,$drempelPercentage);
				//$this->hfactuurWaarden=$hfactuur->berekenWaarden();
				$this->hfactuurWaarden=$this->getFactuurWaarden($this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille'],$vandatum, $tmdatum,$drempelPercentage);
			}

			$query="SELECT Portefeuilles.Portefeuille,BeheerfeeBedragBuitenFee FROM Portefeuilles WHERE Portefeuilles.Einddatum > '$tmdatum' AND Portefeuilles.BeheerfeeToevoegenAanPortefeuille IN('".$this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille']."') AND Portefeuille <> '".$this->portefeuille."' ";
			$this->DB->SQL($query);
			$this->DB->Query();
			$this->portefeuilles=array($this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille']);
			while($data = $this->DB->NextRecord())
			{
				foreach ($data as $key=>$value)
					if(in_array($key,$somVelden))
          {
            $this->portefeuilledata[$key] += $value;
            $this->portefeuilledata['portefeuilles_'.$key][$data['Portefeuille']] = $value;
          }
					$this->portefeuilles[] = $data['Portefeuille'];
			}
			$this->portefeuilles[]=$this->portefeuille;
		}
		else
		{
			$this->portefeuilles=array();
			if($this->portefeuilledata['feeToevoegMethode'] <> 2 )
			{
				$query="SELECT Portefeuille,BeheerfeeBedragBuitenFee FROM Portefeuilles WHERE Portefeuilles.Einddatum > '$tmdatum' AND Portefeuilles.BeheerfeeToevoegenAanPortefeuille = '".$this->portefeuille."' ";
				$this->DB->SQL($query);
				$this->DB->Query();
				$this->portefeuilles=array();
        while($data = $this->DB->NextRecord())
        {
          foreach ($data as $key=>$value)
          {
            if(in_array($key,$somVelden))
            {
              $this->portefeuilledata['portefeuilles_'.$key][$data['Portefeuille']] = $value;
              $this->portefeuilledata[$key] += $value;
            }
          }
          $this->portefeuilles[] = $data['Portefeuille'];
        }
			}
			$this->portefeuilles[]=$this->portefeuille;
 		}
		$this->portefeuillesFilter="portefeuille IN('".implode("','",$this->portefeuilles)."')";

		$query = "SELECT * FROM CRM_naw WHERE  CRM_naw.portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$this->CRMdata = $this->DB->NextRecord();

		$query = "SELECT * FROM Rekeningen WHERE Portefeuille = '".$this->portefeuille."' AND Rekening like '%EUR' AND Inactief=0 LIMIT 1";
		$this->DB->SQL($query);
		$this->DB->Query();
		$this->eurRekening = $this->DB->NextRecord();

		if($drempelPercentage)
			$this->portefeuilledata['BeheerfeePerformanceDrempelPercentage'] = $drempelPercentage;

		$eindJul=db2jul($tmdatum);
		$startmaand=date('m',$eindJul)-(12/$this->portefeuilledata['BeheerfeeAantalFacturen']);
		$startjaar=date('Y',$eindJul);
		if($startmaand <> 0)
			$vandatum=jul2sql(mktime(0,0,0,$startmaand+1,0,$startjaar));

		if(db2jul($vandatum) < db2jul($this->portefeuilledata['Startdatum']))
		{
			$this->vandatum = $this->portefeuilledata['Startdatum'];
			if($this->portefeuilledata['BeheerfeeBasisberekening'] != '0')
				$this->portefeuilledata['WerkelijkeDagen'] = 1;
			elseif(db2jul($this->portefeuilledata['BeheerfeeFacturatieVanaf']) > db2jul($vandatum))
				$this->portefeuilledata['WerkelijkeDagen'] = 1;
		}
		else
		{
			$this->vandatum = $vandatum;
		}

		$this->tmdatum = $tmdatum;
		$this->julrapportTm = $eindJul;
		$this->rapportMaand	= date("m",$eindJul);
		$this->rapportDag   = date("d",$eindJul);
		$this->rapportJaar  = date("Y",$eindJul);



		//$tmdatum//$vandatum datum aangepast?
		$BeheerfeeFacturatieVanaf=db2jul($this->portefeuilledata['BeheerfeeFacturatieVanaf']);
		if(db2jul($vandatum) < $BeheerfeeFacturatieVanaf && $eindJul >  $BeheerfeeFacturatieVanaf )
		{
			echo "<br>\nFacturatiedatum < factuur vanaf datum. Werkelijke dagen gebruiken vanaf ".$this->portefeuilledata['BeheerfeeFacturatieVanaf']." voor ".$this->portefeuille.".";
			$vandatum=$this->portefeuilledata['BeheerfeeFacturatieVanaf'];
      $this->vandatum = $vandatum;
			$this->portefeuilledata['WerkelijkeDagen'] = 1;
		}
		elseif($BeheerfeeFacturatieVanaf==$eindJul)
		{
			echo "<br>\n Facturatie vanaf is gelijk aan rapportagedatum, nog geen factuur voor ".$this->portefeuille." aanmaken.";
			$this->afbreken = true;
			return false;
		}
		elseif($BeheerfeeFacturatieVanaf > $eindJul)
		{
			echo "<br>\n Facturatie vanaf is groter dan rapportagedatum, nog geen factuur voor ".$this->portefeuille." aanmaken.";
			$this->afbreken = true;
			return false;
		}

		$this->julrapportVanaf 		  = db2jul($vandatum);
		$this->rapportMaandVanaf 	  = date("m",$this->julrapportVanaf);
		$this->rapportDagVanaf 		  = date("d",$this->julrapportVanaf);
		$this->rapportJaarVanaf 		= date("Y",$this->julrapportVanaf);
    $this->maandsFondsUitsluitingen =array();

		if ($this->portefeuilledata['BeheerfeeFacturatieVooraf'] == 1 )
		{
			$this->portefeuilledata['WerkelijkeDagen'] = 0;
		}

		if($this->portefeuilledata['BeheerfeeAantalFacturen'] == 1)
		{
			$julvan = db2jul($this->vandatum);
			$jultm  = db2jul($this->tmdatum);
			if(!GetModuleAccess('FACTUURHISTORIE'))
			{

				if($this->portefeuilledata['BeheerfeeFacturatieVanaf'] <> '0000-00-00' && $this->portefeuilledata['BeheerfeeAantalFacturen'] == 1 && substr($this->portefeuilledata['BeheerfeeFacturatieVanaf'],5,2)==substr($this->tmdatum,5,2))
				{
					logScherm("Factuur voor ".$this->portefeuille." aanmaken ivm facturatiemaand gelijk aan rapportagemaand.");
				}
				else
				{
			  	if(date("d-m",$jultm) == "31-12" && $this->portefeuilledata['BeheerfeeFacturatieVanaf'] == '0000-00-00')
			  	{
				  	$this->vandatum =  date('Y',$julvan).'-01-01' ;
				  }
			  	else
			  	{
						if($this->portefeuilledata['BeheerfeeFacturatieVanaf'] <> '0000-00-00')
							logscherm("Factuur niet aanmaken voor portefeuille ".$this->portefeuille." ivm ingestelde FacturatieVanaf maand.");
						else
				    	logscherm("Datum is niet 31-12. Factuur niet maken voor portefeuille ".$this->portefeuille.".");
				  	$this->afbreken = true;
					  return false;
				  }
				}
			}
		}
		elseif($this->portefeuilledata['BeheerfeeAantalFacturen'] == 2 && $this->portefeuilledata['Layout'] <> 13)
		{
			$julvan = db2jul($this->vandatum);
			$jultm  = db2jul($this->tmdatum);

			if(date("d-m",$jultm) == "30-06")
			{
				$this->vandatum =  date('Y',$julvan).'-01-01' ;
			}
			elseif (date("d-m",$jultm) == "31-12")
			{
				$this->vandatum =  date('Y',$julvan).'-06-30' ;
			}
			else
			{
				echo "Geen halfjaar factuur datum voor portefeuille ".$this->portefeuille.".";
				$this->afbreken = true;
				return false;
			}
		}

		if($tijdelijkeTabelVullen == true)
		{

			if($this->rapportMaandVanaf == 1 && $this->rapportDagVanaf == 1)
			{
				$this->startjaar = true;
				$this->extrastart = $this->vandatum;
			}
			else
			{
				$this->startjaar = false;
				if(mktime(0,0,0,1,1,$this->rapportJaarVanaf) < db2jul($this->portefeuilledata['Startdatum']))
				{
					$this->extrastart = substr($this->portefeuilledata['Startdatum'],0,10);
				}
				else
				{
					$this->extrastart = substr(jul2db(mktime(0,0,0,1,1,$this->rapportJaarVanaf)),0,10);
					foreach ($this->portefeuilles as $portefeuille)
					{
						$fondswaarden['c'] =  $this->berekenPortefeuilleWaarde($portefeuille,$this->extrastart,true);
						vulTijdelijkeTabel($fondswaarden['c'] ,$portefeuille,$this->extrastart);
					}
				}
			}
			$this->waardeVerdeling=array();
			foreach ($this->portefeuilles as $portefeuille)
			{
				$fondswaarden['a'] =  $this->berekenPortefeuilleWaarde($portefeuille, $this->vandatum,$this->startjaar);
				vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$this->vandatum);
				$fondswaarden['b'] =  $this->berekenPortefeuilleWaarde($portefeuille, $this->tmdatum,0);
				vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$this->tmdatum);


				if($this->portefeuilledata['valutaUitsluiten'] == 1)
				{
					$perioden=array('a','b');
					foreach($perioden as $periode)
					{
						$newArray=array();
						foreach ($fondswaarden[$periode] as $waarden)
						{
							if($waarden['type']<>'rekening')
							{
								$newArray[]=$waarden;
							}
							if($periode=='a')
							{
								$this->waardeVerdeling[$portefeuille]['beginWaarde']['totaal'] += $waarden['actuelePortefeuilleWaardeEuro'];
								$this->waardeVerdeling[$portefeuille]['beginWaardeDetail'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro'];
							}
							else
							{
								$this->waardeVerdeling[$portefeuille]['eindWaarde']['totaal'] += $waarden['actuelePortefeuilleWaardeEuro'];
								$this->waardeVerdeling[$portefeuille]['eindWaardeDetail'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro'];
							}
              //$this->waardeVerdeling[$portefeuille]['gemiddeldeWaarde']['totaal'] += ($waarden['actuelePortefeuilleWaardeEuro']/2);
              //$this->waardeVerdeling[$portefeuille]['gemiddeldeDetail'][$waarden['type']] += ($waarden['actuelePortefeuilleWaardeEuro']/2);
						}
						$fondswaarden[$periode]=$newArray;
					}
				}

				if($this->portefeuilledata['BeheerfeeLiquiditeitenViaModel']==1)
				{
					foreach ($fondswaarden['a'] as $waarden)
						$this->waardeVerdeling['portefeuille']['beginWaarde'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro'];
					foreach ($fondswaarden['b'] as $waarden)
						$this->waardeVerdeling['portefeuille']['eindWaarde'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro'];
				}
				if(1)
				{
					foreach ($fondswaarden['a'] as $waarden)
					{
						$this->portefeuilleVerdeling['beginWaarde']['totaal'] += $waarden['actuelePortefeuilleWaardeEuro'];
						$this->portefeuilleVerdeling['beginWaarde'][$portefeuille] += $waarden['actuelePortefeuilleWaardeEuro'];
					}
					foreach ($fondswaarden['b'] as $waarden)
					{
						$this->portefeuilleVerdeling['eindWaarde']['totaal'] += $waarden['actuelePortefeuilleWaardeEuro'];
						$this->portefeuilleVerdeling['eindWaarde'][$portefeuille] += $waarden['actuelePortefeuilleWaardeEuro'];
					}
				}
			}
			if(1)//altijd de verdeling berekenen.
			{
				$maanden=0;
				if($this->portefeuilledata['BeheerfeeBasisberekening']==4)
					$maanden=3;
				elseif($this->portefeuilledata['BeheerfeeBasisberekening']==5)
					$maanden=4;
        elseif($this->portefeuilledata['BeheerfeeBasisberekening']==7)
          $maanden=3;
				foreach ($this->portefeuilles as $portefeuille)
				{
					$this->portefeuilleVerdeling['gemiddeldeWaarde']['totaal'] += ( $this->portefeuilleVerdeling['beginWaarde'][$portefeuille] + $this->portefeuilleVerdeling['eindWaarde'][$portefeuille])/2;
					$this->portefeuilleVerdeling['gemiddeldeWaarde'][$portefeuille] = ( $this->portefeuilleVerdeling['beginWaarde'][$portefeuille] + $this->portefeuilleVerdeling['eindWaarde'][$portefeuille])/2;

					if($this->portefeuilledata['BeheerfeeBasisberekening']==4 || $this->portefeuilledata['BeheerfeeBasisberekening']==5)
					{
						$gemiddelde=$this->getMaandsUltimo($maanden,$portefeuille,true);
						$this->portefeuilleVerdeling['maandsGemiddeldeWaarde']['totaal'] += ($gemiddelde['maandsGemiddelde']-$gemiddelde['maandsLiquiditeitenGemiddelde']);
						$this->portefeuilleVerdeling['maandsGemiddeldeWaarde'][$portefeuille] = ($gemiddelde['maandsGemiddelde']-$gemiddelde['maandsLiquiditeitenGemiddelde']);
					}
          if($this->portefeuilledata['BeheerfeeBasisberekening']==6)
          {
            $dagGemiddeldeWaarde=$this->getDagGemidelde($portefeuille);
            $this->portefeuilleVerdeling['dagGemiddeldeWaarde']['totaal'] += $dagGemiddeldeWaarde;
            $this->portefeuilleVerdeling['dagGemiddeldeWaarde'][$portefeuille] += $dagGemiddeldeWaarde;
          }
          if($this->portefeuilledata['BeheerfeeBasisberekening']==7)
          {
            $gemiddelde=$this->getMaandsGemiddelde($maanden,$portefeuille,false);
            for($i=1;$i<=$maanden+1;$i++)
            {
              $maand = $this->maandsData[$i];
              $this->portefeuilleVerdeling['maandsGemiddeldeWaardeDetails']['totaal'][$maand] += $this->maandsWaarde[$i];
              $this->portefeuilleVerdeling['maandsGemiddeldeWaardeDetails'][$portefeuille][$maand] = $this->maandsWaarde[$i];
            }
            $this->getKorting($portefeuille);
            $tmp=array_values($this->maandsFondsUitsluitingen);
            $gemiddeldeKorting=0;
            foreach($tmp as $i=>$val)
            {
              if($i==0||$i==count($tmp)-1)
                $mul=0.5;
              else
                $mul=1;
              $gemiddeldeKorting+=$val*$mul;
            }
            $gemiddeldeKorting=$gemiddeldeKorting/(count($tmp)-1);
            
            $this->portefeuilleVerdeling['maandsGemiddeldeWaarde']['totaal'] += ($gemiddelde['maandsGemiddelde']-$gemiddelde['maandsLiquiditeitenGemiddelde']-$gemiddeldeKorting);
            $this->portefeuilleVerdeling['maandsGemiddeldeWaarde'][$portefeuille] = ($gemiddelde['maandsGemiddelde']-$gemiddelde['maandsLiquiditeitenGemiddelde']-$gemiddeldeKorting);
            $this->uitgeslotenFondsenPerPortefeuille[$portefeuille]=$this->maandsFondsUitsluitingen;
            foreach($this->maandsFondsUitsluitingen as $maand=>$waarde)
              $this->uitgeslotenFondsenPerPortefeuille['totaal'][$maand]+=$waarde;
            unset($this->maandsFondsUitsluitingen);
            unset($this->maandsFondsUitsluitingenGevuld);
          }
				}
				if($this->portefeuilledata['BeheerfeeBasisberekening']==4 || $this->portefeuilledata['BeheerfeeBasisberekening']==5)
				{
					if ($this->portefeuilledata['feeToevoegMethode'] == 0)
						$this->getMaandsUltimo($maanden, '', false);
					else
						$this->getMaandsUltimo($maanden, $this->portefeuille, false);
				}
        if($this->portefeuilledata['BeheerfeeBasisberekening']==7)
        {
          if ($this->portefeuilledata['feeToevoegMethode'] == 0)
            $this->getMaandsGemiddelde($maanden, '', false);
          else
            $this->getMaandsGemiddelde($maanden, $this->portefeuille, false);
        }

			}


			if($this->portefeuilledata['BeheerfeeLiquiditeitenViaModel']==1 )
			{
				foreach ($this->berekenPortefeuilleWaarde($this->portefeuilledata['ModelPortefeuille'],$this->vandatum,$this->startjaar) as $waarden)
					$this->waardeVerdeling['model']['beginWaarde'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro'];
				foreach ($this->berekenPortefeuilleWaarde($this->portefeuilledata['ModelPortefeuille'],$this->tmdatum,0) as $waarden)
					$this->waardeVerdeling['model']['eindWaarde'][$waarden['type']] += $waarden['actuelePortefeuilleWaardeEuro'];

				foreach ($this->waardeVerdeling as $portefeuilleType=>$waardes)
				{
					foreach ($waardes as $periode=>$typeData)
					{
						$totaalWaarde=array_sum($typeData);
						foreach ($typeData as $type=>$waarde)
							$this->procentVerdeling[$portefeuilleType][$periode][$type]=$waarde/$totaalWaarde;
					}
				}
			}
		}
		else
		{
			if(mktime(0,0,0,1,1,$this->rapportJaarVanaf) < db2jul($this->portefeuilledata['Startdatum']))
			{
				$this->extrastart = substr($this->portefeuilledata['Startdatum'],0,10);
			}
			else
			{
				$this->extrastart = substr(jul2db(mktime(0,0,0,1,1,$this->rapportJaarVanaf)),0,10);
			}
		}
		$this->huisfondsFee=0;
		if($this->portefeuilledata['BeheerfeeHuisfondsenOvernemen']==1)
		{
			if($this->maandsUltimoGevuld == false)
				$this->maandsUltimoVullen();

			$query="SELECT Fondsen.Portefeuille,Fondsen.Omschrijving,Fondsen.Fonds,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
FROM TijdelijkeRapportage 
INNER JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.fonds = FondsenBuitenBeheerfee.Fonds AND FondsenBuitenBeheerfee.uitsluitenFee = 0
INNER JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
WHERE TijdelijkeRapportage.rapportageDatum ='".$tmdatum."' AND  TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']." ORDER BY Fondsen.Portefeuille";
			$this->DB->SQL($query);
			$this->DB->Query();
			$fondsen=array();
			while($dbdata = $this->DB->NextRecord())
			{
				$fondsen[]=$dbdata;
			}

			$this->huisfondsFactuurWaarden=array();
			foreach($fondsen as $dbdata)
			{
        if($this->portefeuilledata['BeheerfeeBasisberekening']==7)
        {
          $maandsUltimo = $this->getMaandsGemiddelde(3, $this->portefeuille, false, " AND Fonds='" . $dbdata['Fonds'] . "'");
        }
        else
        {
          $maandsUltimo = $this->getMaandsUltimo(3, $this->portefeuille, false, " AND Fonds='" . $dbdata['Fonds'] . "'");
        }
				$huisFactuurWaarden=$this->getFactuurWaarden($dbdata['Portefeuille'], $vandatum, $tmdatum, $drempelPercentage);
				$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]=$huisFactuurWaarden;
				$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['omschrijving']=$dbdata['Omschrijving'];
				$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['fondsWaarde']=$dbdata['actuelePortefeuilleWaardeEuro'];
				$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['fondsAandeel']=$maandsUltimo['maandsGemiddelde']/$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['rekenvermogen'];
				$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['retourJaar']=$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['fondsAandeel']*
					$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['beheerfeeOpJaarbasis'];

				$this->huisfondsFeeJaar+=$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['fondsAandeel']*
					$this->huisfondsFactuurWaarden[$dbdata['Portefeuille']]['beheerfeeOpJaarbasis'];
			}

		}
    if($this->portefeuilledata['BeheerfeeTeruggaveHuisfondsenPercentage'] <> 0)
      $this->huisfondsenBepalen=true;
    else
      $this->huisfondsenBepalen=false;

		$this->extraFactuurregels=array();
	}

	function berekenWaarden($alleVelden = true)
	{
		$this->data=array();
    $dezePortefeuilleUitsluiten=0;
		if($this->afbreken == true)
		{

		}
		else
		{
			$aandeelWaarde=1;
			$aandeelFee=1;
			$forceerBerekening=false;
			if($this->portefeuilledata['BeheerfeeBasisberekening']==0)
				$verdeling='gemiddeldeWaarde';
			elseif($this->portefeuilledata['BeheerfeeBasisberekening']==1)
				$verdeling='beginWaarde';
			elseif($this->portefeuilledata['BeheerfeeBasisberekening']==4 || $this->portefeuilledata['BeheerfeeBasisberekening']==5 || $this->portefeuilledata['BeheerfeeBasisberekening']==7)
				$verdeling='maandsGemiddeldeWaarde';
			elseif($this->portefeuilledata['BeheerfeeBasisberekening']==6)
         $verdeling='dagGemiddeldeWaarde';
			else
				$verdeling='eindWaarde';

			if($this->portefeuilledata['feeToevoegMethode'] == 1 )
			{
       
        $totaalUitsluiten=0;
        if($this->portefeuilledata['BeheerfeeBedragBuitenFee']>0)
        {
          $dezePortefeuilleUitsluiten = $this->portefeuilledata['portefeuilles_BeheerfeeBedragBuitenFee'][$this->portefeuille];
          $totaalUitsluiten = $this->portefeuilledata['BeheerfeeBedragBuitenFee'];
        }
				$aandeelWaarde=($this->portefeuilleVerdeling[$verdeling][$this->portefeuille]/$this->portefeuilleVerdeling[$verdeling]['totaal']);
				$aandeelFee   =(($this->portefeuilleVerdeling[$verdeling][$this->portefeuille]-$dezePortefeuilleUitsluiten)/($this->portefeuilleVerdeling[$verdeling]['totaal']-$totaalUitsluiten));
				$forceerBerekening=true;
			}

			if ($this->portefeuilledata['WerkelijkeDagen'] == 1)
			{
				if(empty($this->periodeDeelVanJaar))
					$this->periodeDeelVanJaar = $this->getPeriodeDeelVanJaar();
			}
			else
			{
				$this->periodeDeelVanJaar = 1 / $this->portefeuilledata['BeheerfeeAantalFacturen'];
			}


			$this->beheerfeeOpJaarbasis = $this->getBeheerfee($this->portefeuilledata['BeheerfeeMethode'],$forceerBerekening);

			if($this->portefeuilledata['feeToevoegMethode'] == 2 && isset($this->hfactuurWaarden['beheerfeeOpJaarbasis']) )
			{
				$aandeelWaarde=($this->portefeuilleVerdeling[$verdeling][$this->portefeuille]/$this->portefeuilleVerdeling[$verdeling]['totaal']);
				$this->beheerfeeOpJaarbasis=($this->beheerfeeOpJaarbasis-$this->hfactuurWaarden['beheerfeeOpJaarbasis']);
				$aandeelFee=$this->portefeuilleVerdeling[$verdeling][$this->portefeuille]/($this->portefeuilleVerdeling[$verdeling]['totaal']-$this->portefeuilleVerdeling[$verdeling][$this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille']]);
				if($this->portefeuilledata['BeheerfeeSchijvenTarief'] <> 1)
				{
					$verschil=$this->portefeuilleVerdeling[$verdeling][$this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille']];
					foreach ($this->staffelWaarden as $index=>$staffelWaarden)
					{
						if($staffelWaarden['waarde'] < $verschil)
						{
							$verschil-=$staffelWaarden['waarde'];
							$staffelWaarden['waarde']=0;
						}
						elseif($verschil > 0)
						{
							$staffelWaarden['waarde']-=$verschil;
							$verschil=0;
						}
//        listarray($staffelWaarden); echo "$verschil <br>\n";
						$staffelWaarden['fee']=$staffelWaarden['waarde']*$staffelWaarden['percentage']*0.01;
						$this->staffelWaarden[$index]['waarde']=$staffelWaarden['waarde'];
						$this->staffelWaarden[$index]['fee']=$staffelWaarden['fee'];
					}
				}
			}
			//echo $this->portefeuille." $aandeelWaarde $aandeelFee <br>\n";
			if($this->portefeuilledata['BeheerfeeBedragVast']==1)
				$this->beheerfeeOpJaarbasis+=($this->getBeheerfeeBedrag(true)/$aandeelFee);



			if(empty($this->remisierBedrag) && $this->portefeuilledata['BeheerfeeRemisiervergoedingsPercentage'] > 0)
			{
				$this->getRemissiervergoeding();
				$this->remisierBedrag = round($this->remisierBedrag,2);
			}

			if($this->portefeuilledata['BeheerfeePerformancePercentage'] > 0 || $this->portefeuilledata['BeheerfeePerformanceDrempelPercentage'] > 0 || $this->portefeuilledata['BeheerfeePerformanceDrempelBedrag'] > 0)
			{
				$this->performancefee = $this->getPerformancefee();
			}

			if ($this->portefeuilledata['BeheerfeeAdministratieVergoeding'] <> 0)
			{
				$this->administratieBedrag = $this->getBeheerfeeAdministratieVergoeding();
      if($this->portefeuilledata['BeheerfeeAdminVgConUitsluiten']==1)
        $this->administratieBedrag=($this->getBeheerfeeAdministratieVergoeding(true)/$aandeelFee);
			}
			if($this->portefeuilledata['BestandsvergoedingUitkeren'] > 0)
				$this->bestandsvergoeding=$this->getBestandvergoeding();
			else
				$this->bestandsvergoeding=0;

			if($this->portefeuilledata['BeheerfeeTransactiefeeKosten'] > 0)
				$this->transactiefee=$this->getTransactiefee();
			else
				$this->transactiefee=0;

			$this->getFactuurregels();

			$huisfondsKorting=round($this->huisfondsWaarde*$this->portefeuilledata['BeheerfeePercentageVermogen'] * $this->getPeriodeDeelVanJaar() /100,2);
//echo "<br>\n".$this->beheerfeeOpJaarbasis." * ".$this->periodeDeelVanJaar." + ".$this->administratieBedrag." > ".$this->portefeuilledata['BeheerfeeMinJaarBedrag']." * ".$this->periodeDeelVanJaar."<br>\n";
			if (($this->beheerfeeOpJaarbasis * $this->periodeDeelVanJaar) + $this->administratieBedrag + $huisfondsKorting  >
				$this->portefeuilledata['BeheerfeeMinJaarBedrag'] * $this->periodeDeelVanJaar)
			{
				$this->beheerfeePerPeriodeNor = ($this->beheerfeeOpJaarbasis * $this->periodeDeelVanJaar) + $this->administratieBedrag ;
				$this->beheerfeePerPeriode = ($this->beheerfeeOpJaarbasis * $this->periodeDeelVanJaar) + $this->administratieBedrag + $this->performancefee;
				$this->beheerfeePerPeriode = round($this->beheerfeePerPeriode,2);
				$this->beheerfeeBetalen = $this->beheerfeePerPeriode - $this->remisierBedrag;
			}
			else
			{
				$this->MinJaarbedragGebruikt = true;
				$this->beheerfeePerPeriodeNor = ($this->beheerfeeOpJaarbasis * $this->periodeDeelVanJaar) + $this->administratieBedrag + $this->performancefee;
				$this->beheerfeePerPeriode = $this->portefeuilledata['BeheerfeeMinJaarBedrag'] * $this->periodeDeelVanJaar;
				$this->beheerfeePerPeriode = round($this->beheerfeePerPeriode,2);
				$this->beheerfeeBetalen = $this->beheerfeePerPeriode - $this->remisierBedrag;
			}

			if($this->portefeuilledata['bestandsvergoedingBtw']==0)
			{
				$this->beheerfeeBetalen = ($this->beheerfeeBetalen*$aandeelFee - $this->bestandsvergoeding)/$aandeelFee ;
			}

			if($this->portefeuilledata['TransactiefeeBtw']==0)
			{
				$this->beheerfeeBetalen = ($this->beheerfeeBetalen*$aandeelFee + $this->transactiefee)/$aandeelFee ;
			}

			if($this->extraFactuurregels['metBTW']<>0)
			{
				$this->beheerfeeBetalen = ($this->beheerfeeBetalen*$aandeelFee + $this->extraFactuurregels['metBTW'])/$aandeelFee ;
			}


			if($this->beheerfeeBetalen < 0)
				$this->beheerfeeBetalen = 0;
			$this->beheerfeeBetalen=round($this->beheerfeeBetalen*$aandeelFee,2);
			$this->beheerfeePerPeriodeNor=round($this->beheerfeePerPeriodeNor,2);
      
			if($this->portefeuilledata['BeheerfeeBTW'] > 0)
			{
				$this->btwTarief = $this->portefeuilledata['BeheerfeeBTW'];
				$this->btw = round($this->beheerfeeBetalen * ($this->btwTarief/100),2);
				$this->btwNor = round($this->beheerfeePerPeriodeNor * ($this->btwTarief/100),2);
			}
			else
			{
				$this->btw = $this->extraFactuurregels['btwBedrag'];
				if($this->btw <> 0)
				{
					$this->btwTarief=21;
				}
			}
			$this->beheerfeeBetalenIncl = $this->beheerfeeBetalen + $this->btw;


			if($this->portefeuilledata['bestandsvergoedingBtw']==1)
				$this->beheerfeeBetalenIncl = ($this->beheerfeeBetalenIncl) - $this->bestandsvergoeding;

			if($this->portefeuilledata['TransactiefeeBtw']==1)
				$this->beheerfeeBetalenIncl = ($this->beheerfeeBetalenIncl) + $this->transactiefee;

			if($this->portefeuilledata['bestandsvergoedingBtw']==1)
				$this->beheerfeeBetalenIncl = ($this->beheerfeeBetalenIncl) - $this->bestandsvergoeding;

			if($this->extraFactuurregels['zonderBTW'] <>0)
			{
				$this->beheerfeeBetalen += $this->extraFactuurregels['zonderBTW'];
				$this->beheerfeeBetalenIncl = ($this->beheerfeeBetalenIncl) + $this->extraFactuurregels['zonderBTW'];
			}

			if($this->portefeuilledata['BeheerfeeBedragBuitenBTW'] <> 0)
			  $this->beheerfeeBetalenIncl +=	$this->portefeuilledata['BeheerfeeBedragBuitenBTW'] * $this->periodeDeelVanJaar;

			if ($alleVelden )
			{
				$this->getGemiddeldVermogen();
				$this->getstortingenOntrekkingen();
				$this->getResultaat();
				$this->getLiquiditeitenVanaf();
				$this->getLiquiditeitenEind();
				$this->getPeriodePerformance();
				$this->getJaarPerformance();
				$this->getTransacties();
			}


			$data=array();
			$data['client']                   =   $this->clientdata['Client'];
			$data['clientNaam']               =   $this->clientdata['Naam'];
			$data['clientNaam1']              =   $this->clientdata['Naam1'];
			$data['clientAdres']              =   $this->clientdata['Adres'];
			$data['clientPostcode']           =   $this->clientdata['pc'];
			$data['clientWoonplaats']         =   $this->clientdata['Woonplaats'];
			$data['clientLand']               =   $this->clientdata['Land'];
			$data['clientTelefoon']           =   $this->clientdata['Telefoon'];
			$data['clientFax']                =   $this->clientdata['Fax'];
			$data['clientEmail']              =   $this->clientdata['Email'];
			$data['debiteurnr']               =   $this->CRMdata['debiteurnr'];
			$data['btwnr']                    =   $this->CRMdata['btwnr'];
			$data['IBAN']                     =   $this->CRMdata['IBAN'];

			$data['SoortOvereenkomst']        =   $this->portefeuilledata['SoortOvereenkomst'];

			$data['administratieBedrag']      =   $this->administratieBedrag*$aandeelFee;
			$data['beheerfeeOpJaarbasis']     =   round($this->beheerfeeOpJaarbasis*$aandeelFee,2);
			$data['beheerfeePerPeriode']      =   $this->beheerfeePerPeriode*$aandeelFee;
			$data['beheerfeePerPeriodeNor']   =   round($this->beheerfeePerPeriodeNor*$aandeelFee,2);
			$data['periodeDeelVanJaar']       =   round($this->periodeDeelVanJaar,4);
			$data['periodeDagen']             =   $this->periodeDagen;

			$data['BeheerfeePercentageVermogen'] = $this->portefeuilledata['BeheerfeePercentageVermogen'];
			$data['BeheerfeePercentageVermogenDeelVanJaar'] = $this->portefeuilledata['BeheerfeePercentageVermogen'] * $this->periodeDeelVanJaar;
			$data['BeheerfeeBasisberekening'] =   $this->portefeuilledata['BeheerfeeBasisberekening'];
			$data['BeheerfeeMethode']         =   $this->portefeuilledata['BeheerfeeMethode'];
			$data['BeheerfeeFacturatieVooraf']         =   $this->portefeuilledata['BeheerfeeFacturatieVooraf'];
			$data['BeheerfeeKortingspercentage']         =   $this->portefeuilledata['BeheerfeeKortingspercentage'];
      $data['BeheerfeeBedragVast']      =   $this->portefeuilledata['BeheerfeeBedrag'];

			$data['MinJaarbedragGebruikt']    =   $this->MinJaarbedragGebruikt;

			$data['beheerfeeBetalen']         =   $this->beheerfeeBetalen;
			$data['beheerfeeBetalenInclNew']  =   $this->beheerfeeBetalen;
			$data['btwTarief']                =   $this->btwTarief;
			$data['btw']                      =   $this->btw;
			$data['btwNor']                   =   $this->btwNor;
			$data['beheerfeeBetalenIncl']     =   $this->beheerfeeBetalenIncl;
			$data['BeheerfeeBedragBuitenBTW'] =   $this->portefeuilledata['BeheerfeeBedragBuitenBTW'];
			$data['BeheerfeeBedragBuitenBTWPeriode'] =   $this->portefeuilledata['BeheerfeeBedragBuitenBTW'] * $this->periodeDeelVanJaar;
			$data['BeheerfeePerformanceDrempelPercentage']=$this->portefeuilledata['BeheerfeePerformanceDrempelPercentage'];

			//$data['beheerfeePerPeriodeNor']  =   round($this->beheerfeePerPeriodeNor + $this->btwNew,2);

			$data['portefeuille']             =   $this->portefeuille;

			$data['stortingen']               =   $this->stortingen;
			$data['onttrekkingen']            =   $this->onttrekkingen;
			$data['stortingenOntrekkingen']   =   $this->stortingenOntrekkingen;
			$data['resultaat']                =   round($this->resultaat,2);
      $data['aandeelWaarde']            =   $aandeelWaarde;
      $data['aandeelFee']               =   $aandeelFee;

			$data['totaalWaardeVanaf']        =   round($this->totaalWaardeVanaf['totaal']*$aandeelWaarde,2);
			$data['totaalWaarde']             =   round($this->totaalWaarde['totaal']*$aandeelWaarde,2);
			$data['gemiddeldeVermogen']       =   round($this->gemiddeldeVermogen*$aandeelWaarde,2);
			$data['rekenvermogenFee']         =   round($this->rekenvermogen*$aandeelFee,2);
      $data['rekenvermogen']            =   round($this->rekenvermogen,2);
			$data['rekenvermogenLiquiditeiten']=  round($this->rekenvermogenLiquiditeiten*$aandeelFee,2);
			$data['basisRekenvermogen']       =   round($this->basisRekenvermogen*$aandeelWaarde,2);

			//$data['fondsWaardeBuitenFee']     =   round($this->fondsWaardeBuitenFee*$aandeelWaarde,2);
			$data['maandsGemiddelde']         =   round($this->maandsGemiddelde*$aandeelWaarde,2);
			$data['waardeVerdeling']          =   $this->waardeVerdeling;
      $data['portefeuilleVerdeling']    =   $this->portefeuilleVerdeling;

			$data['performancePeriode']       =   round($this->performancePeriode,2);
			$data['performanceJaar']          =   round($this->performanceJaar,2);
			$data['remisierBedrag']           =   $this->remisierBedrag;
			$data['BeheerfeeRemisiervergoedingsPercentage'] = $this->portefeuilledata['BeheerfeeRemisiervergoedingsPercentage'];
			$data['huisfondsWaarde']          =   round($this->huisfondsWaarde,2);
			$data['huisfondsKorting']         =   $huisfondsKorting;
			$data['huisfondsKortingFondsen']  =   $this->huisfondsKortingFondsen;
			$data['BeheerfeeTeruggaveHuisfondsenPercentage'] = $this->portefeuilledata['BeheerfeeTeruggaveHuisfondsenPercentage'];
			$data['BeheerfeeMinJaarBedrag'] = $this->portefeuilledata['BeheerfeeMinJaarBedrag'];

			$data['waardeLiquiditeitenVanaf'] =   round($this->waardeLiquiditeitenVanaf['totaal'],2);
			$data['waardeLiquiditeitenEind']  =   round($this->waardeLiquiditeitenEind['totaal'],2);
			$data['BeheerfeeLiquiditeitenPercentage']=$this->portefeuilledata['BeheerfeeLiquiditeitenPercentage'];

			$data['datumVan']                 =   $this->vandatum;
			$data['datumFacturatieVanaf']     =   date("Y-m-d",$this->julrapportVanaf);
			$data['datumTot']                 =   $this->tmdatum;
			$data['rapportJaar']              =		$this->rapportJaarVanaf;
			$data['kwartaal']                 = 	ceil(date("n",db2jul($this->tmdatum))/3);
			$data['BeheerfeeAantalFacturen']  =   $this->portefeuilledata['BeheerfeeAantalFacturen'];

			$data['totaalTransactie']         =   $this->totaalTransactie['totaal'];
			$data['valuta']                   =   'EUR';
			$data['Accountmanager']           =   $this->portefeuilledata['Accountmanager'];
			$data['Vermogensbeheerder']       =   $this->portefeuilledata['Vermogensbeheerder'];
			$data['Depotbank']                =   $this->portefeuilledata['Depotbank'];
			$data['depotbankOmschrijving']    =   $this->portefeuilledata['depotbankOmschrijving'];
			$data['performancefee']           =   $this->performancefee;
			$data['performancefeeRekenbedrag']=   $this->performancefeeRekenbedrag;
			$data['performancefeeRekenpercentage'] = $this->performancefeeRekenpercentage;
			$data['bestandsvergoeding']       =   $this->bestandsvergoeding;
			$data['BestandsvergoedingUitkeren'] = $this->portefeuilledata['BestandsvergoedingUitkeren'];
			$data['transactiefee']            = $this->transactiefee;
			$data['BetalingsinfoMee']         = $this->portefeuilledata['BetalingsinfoMee'];

			if($dezePortefeuilleUitsluiten>0)
        $data['BeheerfeeBedragBuitenFeePortefeuille'] = $dezePortefeuilleUitsluiten;
			
		  $data['BeheerfeeBedragBuitenFee'] = $this->portefeuilledata['BeheerfeeBedragBuitenFee'];
			$data['FactuurMemo']              = $this->portefeuilledata['FactuurMemo'];
			$data['FactuurMinimumBedrag']     = $this->portefeuilledata['FactuurMinimumBedrag'];
			$data['afwijkendeOmzetsoort']     =	$this->portefeuilledata ['afwijkendeOmzetsoort'];

			$data['CRM_naam']                 = $this->CRMdata['naam'];
			$data['CRM_naam1']                = $this->CRMdata['naam1'];
			$data['CRM_verzendAanhef']        = $this->CRMdata['verzendAanhef'];
			$data['CRM_PaAanhef']             = $this->CRMdata['verzendPaAanhef'];
			$data['CRM_verzendAdres']         = $this->CRMdata['verzendAdres'];
			$data['CRM_verzendPc']            = $this->CRMdata['verzendPc'];
			$data['CRM_verzendPlaats']        = $this->CRMdata['verzendPlaats'];
			$data['CRM_verzendLand']          = $this->CRMdata['verzendLand'];
			$data['o_clientNaam']             = $data['clientNaam'];
			$data['o_clientNaam1']            = $data['clientNaam1'];
			$data['o_clientAdres']            = $data['clientAdres'];
			$data['o_clientPostcode']         = $data['clientPostcode'];
			$data['o_clientWoonplaats']       = $data['clientWoonplaats'];
			$data['o_clientLand']             = $data['clientLand'];
			if($this->portefeuilledata['CrmClientNaam'] == 1)
			{
				$data['clientNaam']               =   $this->CRMdata['naam'];
				$data['clientNaam1']              =   $this->CRMdata['naam1'];
				$data['clientAdres']              =   $this->CRMdata['verzendAdres'];
				$data['clientPostcode']           =   $this->CRMdata['verzendPc'];
				$data['clientWoonplaats']         =   $this->CRMdata['verzendPlaats'];
				$data['clientLand']               =   $this->CRMdata['verzendLand'];
			}

			$data['rekeningEur']              = substr($this->eurRekening['Rekening'],0,-3);
			$data['rekeningIBAN']             = $this->eurRekening['IBANnr'];
			for ($i=1; $i<5; $i++)
			{
				$data["maandsWaarde_$i"]   =	$this->maandsWaarde[$i]; //
				$data["maandsData_$i"]     =	db2jul($this->maandsData[$i]);
			}
			if(is_array($this->uitgeslotenFondsenPerPortefeuille))
      {
        $data['uitgeslotenFondsenPerPortefeuille']=$this->uitgeslotenFondsenPerPortefeuille;
      }
			  
			if(is_array($this->staffelWaarden))
			{
				foreach ($this->staffelWaarden as $id=>$staffel)
				{
					if(is_array($staffel))
					{
						$this->staffelWaarden[$id]['waarde'] = $staffel['waarde'] * $aandeelFee;
						$this->staffelWaarden[$id]['feeDeel'] = $staffel['fee'] * $this->periodeDeelVanJaar * $aandeelFee;
					}
				}
			}

			$data["staffelWaarden"]=$this->staffelWaarden;
			$data["huisfondsFeeJaar"]=$this->huisfondsFeeJaar;
			$data["huisfondsFactuurWaarden"]=$this->huisfondsFactuurWaarden;
			$this->getJaarResultaat();
			$data["NettoVermogenstoenameYtd"]=round($this->jaarResultaat,2);
			$data["BeginwaardeJaar"]=round($this->jaarTotaalWaardeVanaf['totaal'],2);
			$data['highwatermark']=$this->highwatermark;
			$data['extraFactuurregels']=$this->extraFactuurregels;
			$data['overigeKosten']=($this->extraFactuurregels['metBTW']+$this->extraFactuurregels['zonderBTW']);
      $data['portefeuilledata']=$this->portefeuilledata;
      $data['maandsFondsUitsluitingen']=$this->maandsFondsUitsluitingen;

			ksort($data);
			$this->data=$data;
    //echo "<br>\nif(".$this->portefeuilledata['FactuurMinimumBedrag']." > (((".$this->beheerfeeOpJaarbasis ."*". $this->periodeDeelVanJaar.")-".$this->huisfondsKorting.")))<br>\n";
	    // logscherm($this->portefeuilledata['FactuurMinimumBedrag']." >  ".$data['beheerfeeBetalen']);
			//logscherm($this->portefeuilledata['FactuurMinimumBedrag']." > ((".$this->beheerfeeOpJaarbasis." * ".$this->periodeDeelVanJaar."))");
			if($this->portefeuilledata['FactuurMinimumBedrag'] >  $data['beheerfeeBetalen'] )//(($this->beheerfeeOpJaarbasis * $this->periodeDeelVanJaar)))
			{
        echo "<br>\n minimum fee > fee (".$this->portefeuilledata['FactuurMinimumBedrag'].">".$data['beheerfeeBetalen'].")";
				$this->afbreken = true;//listarray($data);
				return false;
			}
			if($_POST['debug']==1)
			{
				listarray($data);
			}
			//listarray($data);
			return $data;
		}
	}

  function highWatermarkBerekening($startDatum,$eindDatum,$highWaterStartWaarde=0)
	{
    global $USR;

		$basisPerformancefeePercentage = $this->portefeuilledata['BeheerfeePerformancePercentage'];
		$w_highwatermarkStart=$this->portefeuilledata["BeheerfeeHighwatermarkStart"];
		if($highWaterStartWaarde==0 && $w_highwatermarkStart <>0)
      $highWaterStartWaarde=$w_highwatermarkStart;

		if(substr($startDatum,5,5)=='01-01')
			$startJaar=true;
		else
			$startJaar=false;
		$startWaarde=0;
		$beginwaarden=$this->berekenPortefeuilleWaarde($this->portefeuille,$startDatum,$startJaar);
		foreach($beginwaarden as $regel)
			$startWaarde+=$regel['actuelePortefeuilleWaardeEuro'];

		if ($this->portefeuilledata['BeheerfeePerformancefeeJaarlijks'] == 1)
		{
			if(date("d-m",db2jul($eindDatum)) == "31-12")
			{
				$beginWaarde = 0;
        $startDatum = date("Y-01-01", db2jul($eindDatum));
				$beginwaarden = $this->berekenPortefeuilleWaarde($this->portefeuille, $startDatum, true);
				foreach ($beginwaarden as $regel)
				{
					$beginWaarde += $regel['actuelePortefeuilleWaardeEuro'];
				}
			}
			else
			{
				return array('bedrag'=>0,'percentage'=>0);
			}
		}
		else
		{
			$beginWaarde=$this->getTotaalWaardeVanaf();
			$beginWaarde=$beginWaarde['totaal'];
			//$startHighWdatum=$this->vandatum;
		}
		$eindWaarde=$this->getTotaalWaarde();
		$eindWaarde=$eindWaarde['totaal'];
		$eindStortOnttr =round(getStortingen($this->portefeuille   ,$startDatum,$eindDatum,$this->portefeuilledata['afrekenvalutaKosten']),2)
			-round(getOnttrekkingen($this->portefeuille,$startDatum,$eindDatum,$this->portefeuilledata['afrekenvalutaKosten']),2);

    $this->highwatermark=array('hoogsteWaarde'=>0);

		$this->highwatermark['performanceFeePercentage']=$basisPerformancefeePercentage;
    
    $feeRendement=$eindWaarde-$highWaterStartWaarde-$eindStortOnttr;
  	$periodeRendement=$eindWaarde-$beginWaarde-$eindStortOnttr;
		if($feeRendement<0)
      $feeRendement=0;

    $this->highwatermark['hoogsteWaarde'] = $highWaterStartWaarde + $eindStortOnttr + $feeRendement;
    $this->highwatermark['rendementTbvFee']=$feeRendement;
    $this->highwatermark['periodeRendement']=$periodeRendement;
		$bedrag=$feeRendement;
		$percentage=$basisPerformancefeePercentage;
  
		$db=new DB();
		$query="SELECT id,highwatermark,portfoliowaarde,datum FROM feehistorie WHERE portefeuille='".$this->portefeuille."' AND date(datum) = '".$eindDatum."' ";
		$db->SQL($query);
		$db->Query();
		$data=$db->lookupRecord();
		$change_part=",change_date=now(),change_user='$USR'";
		$add_part=",add_date=now(),add_user='$USR' $change_part";

		$jaar=substr($eindDatum,0,4);
		$maand=substr($eindDatum,5,2);
		$laatsteVanMaand=mktime(0,0,0,$maand+1,0,$jaar);
		if(date('Y-m-d',$laatsteVanMaand) == $eindDatum)
    {
      if ($data['id'] > 0)
      {
        $query = "UPDATE feehistorie SET highwatermark='" . $this->highwatermark['hoogsteWaarde'] . "', portfoliowaarde='$eindWaarde' $change_part WHERE id='" . $data['id'] . "'";
      }
      else
      {
        $query = "INSERT INTO feehistorie SET portefeuille='" . $this->portefeuille . "',highwatermark='" . $this->highwatermark['hoogsteWaarde'] . "',portfoliowaarde='$eindWaarde',datum='" . $eindDatum . "' $add_part";
      }
      $db->SQL($query);
      $db->Query();
    }
    else
    {
      logscherm("Opslaan/bijwerken van highwatermark overgeslagen, $eindDatum is niet de laatste dag van de maand.");
    }
//		echo $query;
//    echo "<br>\n W: $w_highwatermarkStart | a: $a_rendementStart | b: $b_rendementEind | fee: $feeRendement * $basisPerformancefeePercentage %";
//  listarray($this->highwatermark);
    //listarray(array('bedrag'=>$bedrag,'percentage'=>$percentage));
		return array('bedrag'=>$bedrag,'percentage'=>$percentage);
	}

	function getPerformancefee()
	{
		$basisPerformancefeePercentage = $this->portefeuilledata['BeheerfeePerformancePercentage'];

		if($this->portefeuilledata["BeheerfeeHighwatermarkStart"] <> 0 || $this->portefeuilledata["BeheerfeePerformanceViaHighwatermark"]==1)
		{

			$db=new DB();
			$query="SELECT id,highwatermark,portfoliowaarde,datum FROM feehistorie WHERE portefeuille='".$this->portefeuille."' AND datum < '".$this->tmdatum."' ORDER BY datum desc limit 1";
			$db->SQL($query);
			$vorige=$db->lookupRecord();
			if($vorige['id']>0)
			{
				$tmp = $this->highWatermarkBerekening(substr($this->vandatum,0,10),$this->tmdatum,$vorige['highwatermark']);
			}
      else
			{
				$tmp = $this->highWatermarkBerekening(substr($this->vandatum, 0, 10), $this->tmdatum);
			}
			$percentage = $tmp['percentage'];
			$bedrag = $tmp['bedrag'];
		}
		else
		{
			if ($this->portefeuilledata['BeheerfeePerformancefeeJaarlijks'] == 1 && date("d-m",db2jul($this->tmdatum)) == "31-12")
			{
				$percentage = $this->getJaarPerformance();
				$bedrag = $this->getJaarResultaat();
			}
			elseif ($this->portefeuilledata['BeheerfeePerformancefeeJaarlijks'] == 0)
			{
				$percentage = $this->getPeriodePerformance();
				$bedrag = $this->getResultaat();
			}
			else
			{
				$bedrag = 0;
				$percentage = 0;
				$basisPerformancefeePercentage = 0;
			}

			if ($bedrag > $this->portefeuilledata['BeheerfeePerformanceDrempelBedrag'])
			{
				$bedrag = $bedrag - $this->portefeuilledata['BeheerfeePerformanceDrempelBedrag'];
			}
			else
			{
				$bedrag = 0;
			}
			//echo "$percentage > ".$this->portefeuilledata['BeheerfeePerformanceDrempelPercentage']."<br>\n";
			if ($percentage > $this->portefeuilledata['BeheerfeePerformanceDrempelPercentage'])
			{
				//echo "<br>\n $bedrag/$percentage=";
				if($this->portefeuilledata['BeheerfeePerformanceDrempelPercentage'] <> 0)
				{
				//	echo "=($bedrag/$percentage)	*	($percentage-(".$this->portefeuilledata['BeheerfeePerformanceDrempelPercentage']."))<br>\n";
					$bedrag=($bedrag/$percentage)	*	($percentage-($this->portefeuilledata['BeheerfeePerformanceDrempelPercentage']));
        }
				if($bedrag<0)
					$bedrag=0;

				$percentage=$basisPerformancefeePercentage;
				//echo " $bedrag <br>\n";
				//echo " $percentage <br>\n";
			}
			else
			{
				$percentage = 0;
			}
		}
//  $this->performancefee = round($bedrag * ((round($basisPerformancefeePercentage + $percentage,2)) / 100),2);
		$this->performancefeeRekenbedrag=$bedrag;
		$this->performancefeeRekenpercentage=$percentage;
		$this->performancefee = round($bedrag * (round($percentage,2) / 100),2);

//echo   $this->performancefee.' = round('.$bedrag.' * (round('.$percentage.',2) / 100),2)';exit;
		return $this->performancefee;
	}

	function maandsUltimoVullen($maanden=3)
	{
		$werkmaand=$this->rapportMaand;
		$werkmaand-=$maanden;

		$kwartaal=ceil(date("n",db2jul($this->tmdatum))/3);
		if($kwartaal==1)
			$start=$this->rapportJaar."-01-01";
		elseif($kwartaal==2)
			$start=$this->rapportJaar."-03-31";
		elseif($kwartaal==3)
			$start=$this->rapportJaar."-06-30";
		elseif($kwartaal==4)
			$start=$this->rapportJaar."-09-30";

    if($this->portefeuilledata['BeheerfeeBasisberekening']==7)
      $startI=-1;
    else
      $startI=0;
		$datumGevuld=array();
		for ($i=$startI; $i<$maanden; $i++)
		{
			$berekenDatum = mktime(0,0,0,($i+$werkmaand+2),0,$this->rapportJaar);
			if(db2jul($start) > $berekenDatum && $startI>=0)
				$berekenDatum=$start;
			else
				$berekenDatum = jul2sql($berekenDatum);

			if(!in_array($berekenDatum,$datumGevuld))
			{
				if(substr($berekenDatum,5,5)=='01-01')
					$minDag=true;
				else
					$minDag=false;
				foreach ($this->portefeuilles as $portefeuille)
				{
					$fondswaarden['a'] =  $this->berekenPortefeuilleWaarde($portefeuille, $berekenDatum,$minDag);
					vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$berekenDatum);
					foreach ($fondswaarden['a'] as $waarden)
					{
						$totaal[$berekenDatum]['totaal'] +=$waarden['actuelePortefeuilleWaardeEuro'];
						$totaal[$berekenDatum][$portefeuille] +=$waarden['actuelePortefeuilleWaardeEuro'];
					}
				}
				$datumGevuld[]=$berekenDatum;
			}
		}
		//listarray($totaal);
		$this->maandsUltimoGevuld = true;
	}
  
  function getMaandsGemiddelde($maanden=3,$portefeuille='',$noUpdateMaandwaarden=false,$extraFilter='',$fonds='')
  {
    global $__appvar;
    if($this->maandsUltimoGevuld == false)
      $this->maandsUltimoVullen($maanden);
    $maandenMetWaarde=0;
    $werkmaand=$this->rapportMaand;
    $werkmaand-=$maanden;
  
    if($portefeuille <> '')
      $portefeuilleFilter="portefeuille = '$portefeuille'";
    else
      $portefeuilleFilter=$this->portefeuillesFilter;

    $datumGevuld=array();
    for ($i=0; $i<($maanden+1); $i++)
    {
      $berekenDatum = mktime(0,0,0,($i+$werkmaand+1),0,$this->rapportJaar);
      if($berekenDatum>$this->julrapportTm)
        $berekenDatum=$this->julrapportTm;
      $berekenDatum = jul2sql($berekenDatum);
      
      if(!in_array($berekenDatum,$datumGevuld))
      {
        $datumGevuld[]=$berekenDatum;
      }
    }
    $maandsTotaal=0;
    $waardeLiquiditeitendrieMaanden=0;
    $waardeUitgeslotenFondsenMaanden=0;
    foreach($datumGevuld as $i=>$berekenDatum)
    {
//    listarray($berekenDatum);
      $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
        "FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum = '".$berekenDatum."' AND ".
        $portefeuilleFilter." $extraFilter "
        .$__appvar['TijdelijkeRapportageMaakUniek'];
      $this->DB->SQL($query);
      $this->DB->Query();
      $tmp = $this->DB->nextRecord();
      $valutaKoers=getValutaKoers($this->portefeuilledata['afrekenvalutaKosten'],$berekenDatum);
      if($i==0 || $i==$maanden)
        $factor=0.5;
      else
        $factor=1;
      $maandsTotaal += ($tmp['totaal']/$valutaKoers)*$factor;
      //if($this->portefeuilledata['Vermogensbeheerder']=='DOU')
      //  echo "$portefeuilleFilter $berekenDatum $extraFilter  ".($tmp['totaal']/$valutaKoers)."<br>\n";
      if($tmp['totaal'] <> 0)
      {
        $maandenMetWaarde += 1;
      }
      elseif($extraFilter<>'')
      {
        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
          " rapportageDatum = '".$berekenDatum."' AND $portefeuilleFilter ".$__appvar['TijdelijkeRapportageMaakUniek'];
        $this->DB->SQL($query);
        $this->DB->Query();
        $tmp1 = $this->DB->nextRecord();
        if($tmp1['totaal'] <> 0)
        {
          $maandenMetWaarde += 1;
        }
      }
      
      if ($this->portefeuilledata['valutaUitsluiten'] == 1 || $this->portefeuilledata['BeheerfeeLiquiditeitenAnderPercentage']) //uitsluiten valuta
      {
        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
          "FROM TijdelijkeRapportage WHERE ".
          " rapportageDatum = '".$berekenDatum."' AND ".
          $portefeuilleFilter." $extraFilter ".
          " AND type = 'rekening' "
          .$__appvar['TijdelijkeRapportageMaakUniek'];
        debugSpecial($query,__FILE__,__LINE__);
        $this->DB->SQL($query);
        $this->DB->Query();
        $waardeLiquiditeiten = $this->DB->nextRecord();
        $valutaKoers=getValutaKoers($this->portefeuilledata['afrekenvalutaKosten'],$berekenDatum);
        $waardeLiquiditeitendrieMaanden += ($waardeLiquiditeiten['totaal']/$valutaKoers)*$factor;
        //if($this->portefeuilledata['Vermogensbeheerder']=='DOU')
        //  echo "$portefeuilleFilter $berekenDatum $extraFilter  liquiditeiten ".($waardeLiquiditeiten['totaal']/$valutaKoers)."<br>\n";
      }
      if($noUpdateMaandwaarden==false)
      {
        $this->maandsWaarde[$i+1] = $tmp['totaal'] -  $waardeLiquiditeiten['totaal'];
        $this->maandsData[$i+1] = $berekenDatum;
      }
      else
      {
       // echo "<br>\n $berekenDatum $portefeuilleFilter $extraFilter € " . $tmp['totaal'] . "";
        if($extraFilter<>'')
        {
          if(!isset($this->maandsFondsUitsluitingenGevuld[$berekenDatum][$portefeuilleFilter][$extraFilter]))
          {
            $this->maandsFondsUitsluitingen[$berekenDatum] += $tmp['totaal'];
            $this->maandsFondsUitsluitingenGevuld[$berekenDatum][$portefeuilleFilter][$extraFilter]=$tmp['totaal'];
            $this->maandsFondsUitsluitingenGevuld[$berekenDatum][$portefeuille][$fonds]=$tmp['totaal'];
            $this->maandsFondsUitsluitingenGevuld[$berekenDatum][$portefeuille]['totaal']=+$tmp['totaal'];
            
          }
          $waardeUitgeslotenFondsenMaanden+=$tmp['totaal'];
          
        }
        
      }
    }
    
    $data['maandsGemiddelde']  = $maandsTotaal / ($maandenMetWaarde-1);
    $data['maandsLiquiditeitenGemiddelde'] = $waardeLiquiditeitendrieMaanden / ($maandenMetWaarde-1);
    
    //listarray($this->maandsData);
    //listarray($this->maandsWaarde);
    //listarray($data);
    
    if($this->portefeuilledata['feeToevoegMethode'] <> 0)//if(isset($this->hfactuurWaarden))
      $noUpdateMaandwaarden=false;
    if($noUpdateMaandwaarden==false)
    {
      $this->maandsGemiddelde = $data['maandsGemiddelde'];
      $this->maandsLiquiditeitenGemiddelde = $data['maandsLiquiditeitenGemiddelde'];
    }
    
    if($maanden <> ($maandenMetWaarde-1) )
      logscherm("$maanden maands gemiddelde (".round($this->maandsGemiddelde,2).") voor portefeuille ".$this->portefeuille." gebruikt $maandenMetWaarde meetpunten.");
    
    return $data;
  }
  
	function getMaandsUltimo($maanden=3,$portefeuille='',$noUpdateMaandwaarden=false,$extraFilter='')
	{
		//echo "<br>\n";
		global $__appvar;
		if($this->maandsUltimoGevuld == false)
			$this->maandsUltimoVullen($maanden);
		$maandenMetWaarde=0;
		$werkmaand=$this->rapportMaand;
		$werkmaand-=$maanden;

		$kwartaal=ceil(date("n",db2jul($this->tmdatum))/3);
		if($kwartaal==1)
			$start=$this->rapportJaar."-01-01";
		elseif($kwartaal==2)
			$start=$this->rapportJaar."-03-31";
		elseif($kwartaal==3)
			$start=$this->rapportJaar."-06-30";
		elseif($kwartaal==4)
			$start=$this->rapportJaar."-09-30";

		if($portefeuille <> '')
			$portefeuilleFilter="portefeuille = '$portefeuille'";
		else
			$portefeuilleFilter=$this->portefeuillesFilter;

		//echo $this->portefeuille." $portefeuille $portefeuilleFilter <br>\n";
		$datumGevuld=array();
		for ($i=1; $i<($maanden+1); $i++)
		{

			$berekenDatum = mktime(0,0,0,($i+$werkmaand+1),0,$this->rapportJaar);

			if($berekenDatum>$this->julrapportTm)
				$berekenDatum=$this->julrapportTm;

			if(db2jul($start) > $berekenDatum)
				$berekenDatum=$start;
			else
				$berekenDatum = jul2sql($berekenDatum);

			if(!in_array($berekenDatum,$datumGevuld))
			{
				$datumGevuld[]=$berekenDatum;
			}
		}
    
    $maandsTotaal=0;
    $waardeLiquiditeitendrieMaanden=0;
    $waardeUitgeslotenFondsenMaanden=0;
		foreach($datumGevuld as $i=>$berekenDatum)
		{
//    listarray($berekenDatum);
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				"FROM TijdelijkeRapportage WHERE ".
				" rapportageDatum = '".$berekenDatum."' AND ".
				$portefeuilleFilter." $extraFilter "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			$this->DB->SQL($query);
			$this->DB->Query();
			$tmp = $this->DB->nextRecord();
      $valutaKoers=getValutaKoers($this->portefeuilledata['afrekenvalutaKosten'],$berekenDatum);
			$maandsTotaal += ($tmp['totaal']/$valutaKoers);
			//if($this->portefeuilledata['Vermogensbeheerder']=='DOU')
			//  echo "$portefeuilleFilter $berekenDatum $extraFilter  ".($tmp['totaal']/$valutaKoers)."<br>\n";
			if($tmp['totaal'] <> 0)
      {
        $maandenMetWaarde += 1;
      }
      elseif($extraFilter<>'')
      {
        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
        " rapportageDatum = '".$berekenDatum."' AND $portefeuilleFilter ".$__appvar['TijdelijkeRapportageMaakUniek'];
        $this->DB->SQL($query);
        $this->DB->Query();
        $tmp1 = $this->DB->nextRecord();
        if($tmp1['totaal'] <> 0)
        {
          $maandenMetWaarde += 1;
        }
      }
      
			if ($this->portefeuilledata['valutaUitsluiten'] == 1 || $this->portefeuilledata['BeheerfeeLiquiditeitenAnderPercentage']) //uitsluiten valuta
			{
				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					"FROM TijdelijkeRapportage WHERE ".
					" rapportageDatum = '".$berekenDatum."' AND ".
					$portefeuilleFilter." $extraFilter ".
					" AND type = 'rekening' "
					.$__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query,__FILE__,__LINE__);
				$this->DB->SQL($query);
				$this->DB->Query();
				$waardeLiquiditeiten = $this->DB->nextRecord();
				$valutaKoers=getValutaKoers($this->portefeuilledata['afrekenvalutaKosten'],$berekenDatum);
				$waardeLiquiditeitendrieMaanden += ($waardeLiquiditeiten['totaal']/$valutaKoers);
			
				//if($this->portefeuilledata['Vermogensbeheerder']=='DOU')
			  //  echo "$portefeuilleFilter $berekenDatum $extraFilter  liquiditeiten ".($waardeLiquiditeiten['totaal']/$valutaKoers)."<br>\n";
			}
			if($noUpdateMaandwaarden==false)
			{
				$this->maandsWaarde[$i+1] = $tmp['totaal'] -  $waardeLiquiditeiten['totaal'];
				$this->maandsData[$i+1] = $berekenDatum;
			}
      else
      {
        //echo "<br>\n $berekenDatum $extraFilter € " . $tmp['totaal'] . "";
        if($extraFilter<>'')
        {
          $this->maandsFondsUitsluitingen[$berekenDatum]=$tmp['totaal'];
          $waardeUitgeslotenFondsenMaanden+=$tmp['totaal'];
        }
        
      }
		}

	$data['maandsGemiddelde']  = $maandsTotaal / $maandenMetWaarde;
	$data['maandsLiquiditeitenGemiddelde'] = $waardeLiquiditeitendrieMaanden / $maandenMetWaarde;
 
	if($this->portefeuilledata['feeToevoegMethode'] <> 0)//if(isset($this->hfactuurWaarden))
		 $noUpdateMaandwaarden=false;
	if($noUpdateMaandwaarden==false)
	{
		$this->maandsGemiddelde = $data['maandsGemiddelde'];
		$this->maandsLiquiditeitenGemiddelde = $data['maandsLiquiditeitenGemiddelde'];
	}

	if($maanden <> $maandenMetWaarde)
		logscherm("$maanden maands ultimo (".$this->maandsGemiddelde.") voor portefeuille ".$this->portefeuille." gebruikt $maandenMetWaarde meetpunten.");

		return $data;
	}

	function getDagGemidelde($portefeuille='',$gebruikDatabase=true)
	{
		global $__appvar;
    $vanaf=db2jul($this->vandatum);//$this->julrapportVanaf
		
		if($portefeuille<>'')
      $portefeuilleList=array($portefeuille);
		else
	  	$portefeuilleList=$this->portefeuilles;
		if($this->julrapportTm < $vanaf)
			return 0;

		if($this->portefeuilledata['BeheerfeeFacturatieVooraf'] == 1)
			$stortingenLos=true;
		else
			$stortingenLos=false;

    $this->huisfondsWaardeOpDatum=array();
    $this->liquiditeitenWaardeOpDatum=array();
    $totaleWaardeOpDatum=array();

		for ($i=$vanaf; $i<=$this->julrapportTm; $i=$i+$secondenInDag)//+86400
		{
			$jaar=date('Y',$i);
			$maand=date('m',$i);
			$dag=date('d',$i);
			$i=mktime(0,0,0,$maand,$dag,$jaar);
			$volgendeI=mktime(0,0,0,$maand,$dag+1,$jaar);
			$secondenInDag=$volgendeI-$i;

			$berekenDatum = jul2sql($i);
			//echo "$berekenDatum $secondenInDag <br>\n";
			if(substr($berekenDatum,5,5)=='01-01')
				$minDag=true;
			else
				$minDag=false;
			
      $db=new DB();
      $portefeuilleWaarden=array();
      
      $dbData=array();
			foreach($portefeuilleList as $portefeuille)
			{
        if($gebruikDatabase==true)
        {
          $query = "SELECT portefeuille,datum,eindvermogen,liquiditeiten,huisfondsen,grondslag FROM HistorischeDagelijkseWaarden WHERE portefeuille='" . mysql_real_escape_string($portefeuille) . "' AND datum='$berekenDatum'";
          $db->SQL($query);
          $dbData=$db->lookupRecord();
          if($dbData['portefeuille']<>'')
          {
            $waardes[$dbData['datum']]=$dbData['grondslag'];
            $this->liquiditeitenWaardeOpDatum[$dbData['datum']]=$dbData['liquiditeiten'];
            $this->huisfondsWaardeOpDatum[$dbData['datum']]=unserialize($dbData['huisfondsen']);
            //logScherm('Waarden voor '.$portefeuille.' uit HistorischeDagelijkseWaarden ophehaald voor '.$berekenDatum);
          }
          else
          {
           // logScherm('geen waarden voor '.$portefeuille.' in HistorischeDagelijkseWaarden '.$berekenDatum);
          }
          
        }
        if($dbData['portefeuille']=='')
        {
          $fondswaarden['a'] = $this->berekenPortefeuilleWaarde($portefeuille, $berekenDatum, $minDag);
          foreach ($fondswaarden['a'] as $waarden)
          {
            if ($this->huisfondsenBepalen)
            {
              $query = "SELECT Fonds FROM FondsenBuitenBeheerfee WHERE Fonds='" . $waarden['fonds'] . "' AND FondsenBuitenBeheerfee.uitsluitenFee=1 AND Vermogensbeheerder='" . $this->portefeuilledata['Vermogensbeheerder'] . "'";
              if ($db->QRecords($query))
              {
                $this->huisfondsWaardeOpDatum[$berekenDatum][$waarden['fondsOmschrijving']] += $waarden['actuelePortefeuilleWaardeEuro'];
              }
            }
            if ($waarden['type'] == 'rekening')
            {
              $this->liquiditeitenWaardeOpDatum[$berekenDatum] += $waarden['actuelePortefeuilleWaardeEuro'];
            }
            $waardes[$berekenDatum] += $waarden['actuelePortefeuilleWaardeEuro'];
    
            $totaleWaardeOpDatum[$berekenDatum] += $waarden['actuelePortefeuilleWaardeEuro'];
          }
          $waardes[$berekenDatum] = $waardes[$berekenDatum] / getValutaKoers($this->portefeuilledata['afrekenvalutaKosten'], $berekenDatum);
          $this->liquiditeitenWaardeOpDatum[$berekenDatum] +=0;
  
          if ($stortingenLos == true)
          {
            $vanafDatum = jul2sql($this->julrapportVanaf);
            $storting = getStortingen($portefeuille, $vanafDatum, $berekenDatum, $this->portefeuilledata['afrekenvalutaKosten']) - getOnttrekkingen($portefeuille, $berekenDatum, $berekenDatum, $this->portefeuilledata['afrekenvalutaKosten']);
            $waardes[$berekenDatum] = $waardes[$berekenDatum] - $storting;
          }
        }


			}
			//echo "<br>\n$portefeuille<br>\n";
		}
		$this->dagWaarden=$waardes;
		$this->dagTotalen=$totaleWaardeOpDatum;

		$gemiddelde=0;
		$aantal=count($waardes);
		foreach($waardes as $datum=>$waarde)
			$gemiddelde+=($waarde/$aantal);

   // listarray($waardes);listarray($gemiddelde.'+'.$storting);
		if($stortingenLos==true)
		{
			$gemiddelde+=$storting;
		}
 //echo "<br>\n$portefeuille | $gemiddelde<br>\n";
		return $gemiddelde;
	}

	function getTotaalWaardeVanaf()
	{
		global $__appvar;
		if(empty($this->totaalWaardeVanaf))
		{
			// haal totaalwaarde op startdatum
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				"FROM TijdelijkeRapportage WHERE ".
				" rapportageDatum = '".$this->vandatum."' AND ".
				$this->portefeuillesFilter
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$this->DB->SQL($query);
			$this->DB->Query();
			$this->totaalWaardeVanaf = $this->DB->nextRecord();
			$this->totaalWaardeVanaf['totaal']=$this->totaalWaardeVanaf['totaal']/getValutaKoers($this->portefeuilledata['afrekenvalutaKosten'],$this->vandatum);
		}


		return $this->totaalWaardeVanaf;
	}

	function getTotaalWaarde()
	{
		global $__appvar;
		if(empty($this->totaalWaarde))
		{
			// haal totaalwaarde op einddatum
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				"FROM TijdelijkeRapportage WHERE ".
				" rapportageDatum = '".$this->tmdatum."' AND ".
				$this->portefeuillesFilter
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$this->DB->SQL($query);
			$this->DB->Query();
			$this->totaalWaarde = $this->DB->nextRecord();
			$this->totaalWaarde['totaal']=$this->totaalWaarde['totaal']/getValutaKoers($this->portefeuilledata['afrekenvalutaKosten'],$this->tmdatum);
		}

		return $this->totaalWaarde;
	}

	function getGemiddeldVermogen()
	{
		if(!empty($this->totaalWaardeVanaf['totaal']) && !empty($this->totaalWaarde['totaal']))
		{
			$this->gemiddeldeVermogen = ($this->totaalWaardeVanaf['totaal'] + $this->totaalWaarde['totaal']) / 2;
		}
		else
		{
			//$this->setError('Gemiddeld vermogen niet kunnen bepalen. Vanaf waarde: "'.$this->totaalWaardeVanaf['totaal'].'"  Rapportage waarde: "'.$this->totaalWaarde['totaal'].'" .');
			$this->getTotaalWaardeVanaf();
			$this->getTotaalWaarde();
			$this->gemiddeldeVermogen = ($this->totaalWaardeVanaf['totaal'] + $this->totaalWaarde['totaal']) / 2;
		}
		return $this->gemiddeldeVermogen;
	}

	function getWeging()
	{
		$query = "SELECT ".
			"SUM(((TO_DAYS('".$this->tmdatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$this->tmdatum."') - TO_DAYS('".$this->vandatum."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers))  AS totaal2 ".
			"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			//"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
			"Rekeningen.".$this->portefeuillesFilter." AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$this->vandatum."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$this->tmdatum."' AND ".
			"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";

		$this->DB->SQL($query);
		$this->DB->Query();
		$this->weging = $this->DB->NextRecord();
		return $this->weging;
	}

	function getTransacties()
	{
		// select trasactiekosten uit mutatie table.o
		$query = "SELECT
  							((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)) as kosten,
  							Rekeningmutaties.Omschrijving
							FROM
								Rekeningmutaties, Rekeningen
							WHERE
  							Rekeningmutaties.Rekening = Rekeningen.Rekening AND
  							Rekeningmutaties.Boekdatum > '".$this->vandatum."' AND
	  						Rekeningmutaties.Boekdatum <= '".$this->tmdatum."' AND
  							Rekeningmutaties.Grootboekrekening = 'KOST' AND ".$this->portefeuillesFilter;
		//Rekeningen.Portefeuille = '".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$_totaalTransactie = 0;
		$subDB = new DB();
		$this->totaalTransactie['aantal'] = $this->DB->records();

		while ($data = $this->DB->nextRecord())
		{
			$_match = strtolower(substr($data['Omschrijving'],0,7));
			if ($_match == "aankoop" OR $_match == "verkoop")
			{
				$_fonds = trim(substr($data['Omschrijving'],8));
				$_subQuery="SELECT Fonds FROM FondsenBuitenBeheerfee WHERE Fonds='".mysql_escape_string($_fonds)."' AND FondsenBuitenBeheerfee.uitsluitenFee=1 AND Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'";
				$subDB->SQL($_subQuery);
				if ($subRecord = $subDB->lookupRecord())
				{
					if($subRecord['Fonds'] <> '' && $this->portefeuilledata['Depotbank'] == "STR")
					{
						$this->totaalTransactie['aantal']--;
					}
					else
					{
						$_totaalTransactie += $data['kosten'];
					}
				}
				else
				{
					$_totaalTransactie += $data['kosten'];
				}
			}
		}
		$this->totaalTransactie['totaal'] = $_totaalTransactie * -1;

		//selecteer korting per depotbank.
		$query = "SELECT Korting FROM KortingenPerDepotbank WHERE ".
			" Vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."' AND ".
			" Depotbank = '".$this->portefeuilledata['Depotbank']."' AND Grootboekrekening = 'KOST'";
		$this->DB->SQL($query);
		$this->DB->Query();
		if ($this->DB->records() > 0)
		{
			$korting = $this->DB->nextRecord();
			$this->totaalTransactie['totaal'] = $this->totaalTransactie['totaal'] - ($this->totaalTransactie['aantal'] * $korting['Korting']);
		}
		return $this->totaalTransactie;
	}

	function getRemissiervergoeding()
	{
		if(empty($this->totaalTransactie['totaal']))
		{
			$this->getTransacties();
			$this->remisierBedrag = ($this->totaalTransactie['totaal']/100) * $this->portefeuilledata['BeheerfeeRemisiervergoedingsPercentage'];
		}
		else
		{
			$this->remisierBedrag = ($this->totaalTransactie['totaal']/100) * $this->portefeuilledata['BeheerfeeRemisiervergoedingsPercentage'];
		}
		return $this->remisierBedrag;
	}

	function getKorting($portefeuille='')
	{
		global $__appvar;
  
		if($portefeuille<>'')
      $portefeuilleFilter="portefeuille = '$portefeuille'";
		else
      $portefeuilleFilter=$this->portefeuillesFilter;
    
		// korting op huisfonds.
		if($this->portefeuilledata['BeheerfeeTeruggaveHuisfondsenPercentage'] <> 0)
		{
		  $this->huisfondsKortingFondsen=array();
			$methode=$this->portefeuilledata['BeheerfeeBasisberekening'];
			$huisfondsWaarde = 0;

			if($methode==0)
				$huisfondsKortingPercentage = $this->portefeuilledata['BeheerfeeTeruggaveHuisfondsenPercentage']/200;
			else
				$huisfondsKortingPercentage = $this->portefeuilledata['BeheerfeeTeruggaveHuisfondsenPercentage']/100;

      //$huisfondsKortingPercentageFee = $this->portefeuilledata['BeheerfeePercentageVermogen'] * $this->getPeriodeDeelVanJaar() /10

			if($methode==1||$methode==0)
			{

				$query = "SELECT TijdelijkeRapportage.actuelePortefeuilleWaardeEuro*$huisfondsKortingPercentage as actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.fondsOmschrijving
                  FROM TijdelijkeRapportage
                  INNER JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.Fonds = FondsenBuitenBeheerfee.Fonds AND FondsenBuitenBeheerfee.uitsluitenFee=1 AND FondsenBuitenBeheerfee.Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."' ".
					" WHERE TijdelijkeRapportage.".$portefeuilleFilter.
					" AND rapportageDatum  = '".$this->vandatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query,__FILE__,__LINE__);
				$this->DB->SQL($query);
				$this->DB->Query();

				while($huisfonds = $this->DB->nextRecord())
				{
					$this->huisfondsKortingFondsen[$huisfonds['fondsOmschrijving']] += $huisfonds['actuelePortefeuilleWaardeEuro'];
					$huisfondsWaarde += $huisfonds['actuelePortefeuilleWaardeEuro'];
				}
			}

     if($methode==2||$methode==0||$methode==3)
			{
				$query = "SELECT TijdelijkeRapportage.actuelePortefeuilleWaardeEuro*$huisfondsKortingPercentage as actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.fondsOmschrijving
			      FROM TijdelijkeRapportage
            INNER JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.Fonds = FondsenBuitenBeheerfee.Fonds AND FondsenBuitenBeheerfee.uitsluitenFee=1 AND FondsenBuitenBeheerfee.Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."' ".
					" WHERE TijdelijkeRapportage.Fonds = FondsenBuitenBeheerfee.Fonds AND TijdelijkeRapportage.".$portefeuilleFilter.
					" AND rapportageDatum  = '".$this->tmdatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
				debugSpecial($query,__FILE__,__LINE__);
				$this->DB->SQL($query);
				$this->DB->Query();

				while($huisfonds = $this->DB->nextRecord())
				{
					$this->huisfondsKortingFondsen[$huisfonds['fondsOmschrijving']] += $huisfonds['actuelePortefeuilleWaardeEuro'];
					$huisfondsWaarde += $huisfonds['actuelePortefeuilleWaardeEuro'];
				}
			}
     if($methode==4||$methode==5||$methode==7) //maandsUltimo
     {
     // $this->huisfondsenBepalen
       $huisfondsWaarde=0;
       if($methode==4)
         $maanden=3;
       elseif($methode==5)
         $maanden=4;
			 $query="SELECT TijdelijkeRapportage.actuelePortefeuilleWaardeEuro*$huisfondsKortingPercentage as actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.Fonds,TijdelijkeRapportage.fondsOmschrijving
			      FROM TijdelijkeRapportage
            INNER JOIN FondsenBuitenBeheerfee ON TijdelijkeRapportage.Fonds = FondsenBuitenBeheerfee.Fonds AND FondsenBuitenBeheerfee.uitsluitenFee=1 AND FondsenBuitenBeheerfee.Vermogensbeheerder='".$this->portefeuilledata['Vermogensbeheerder']."'
          	 WHERE TijdelijkeRapportage.".$portefeuilleFilter." ".$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY TijdelijkeRapportage.Fonds";
			 $this->DB->SQL($query);
			 $this->DB->Query();
       $huisfondsen=array();
       while($huisfonds = $this->DB->nextRecord())
       {
         $huisfondsen[] = $huisfonds;
       }
       foreach($huisfondsen as $huisfonds)
		   {
         $huisfondsFilter="AND TijdelijkeRapportage.fonds='".$huisfonds['Fonds']."'";
         if($this->portefeuilledata['BeheerfeeBasisberekening']==7)
         {
           $waarde = $this->getMaandsGemiddelde(3,$portefeuille,true,$huisfondsFilter,$huisfonds['Fonds']);
         }
         else
         {
           $waarde = $this->getMaandsUltimo($maanden, $portefeuille, true, $huisfondsFilter);
         }
         $this->huisfondsKortingFondsen[$huisfonds['fondsOmschrijving']]=$waarde['maandsGemiddelde'];
         $huisfondsWaarde+=$waarde['maandsGemiddelde'];
			 }
     }
   
     if($methode==6)//dagelijks
     {
       //listarray($this->huisfondsWaardeOpDatum);
       $huisfondsWaarde=0;
       $aantal=count($this->huisfondsWaardeOpDatum);
       foreach($this->huisfondsWaardeOpDatum as $datum=>$fondsen)
       {
         foreach($fondsen as $fondsOmschrijving=>$waarde)
         {
           $huisfondsWaarde+=($waarde/$aantal);
           $this->huisfondsKortingFondsen[$fondsOmschrijving]+=$waarde/$aantal;
         }
       }
		}
	 }

		$this->huisfondsWaarde = $huisfondsWaarde;
		return $this->huisfondsWaarde;
	}

	function getFondsWaardeBuitenFee()
	{
		//niet meer in gebruik

	}

	function getStortingen()
	{
		if(empty($this->stortingen))
			$this->stortingen 			 			= round(getStortingen($this->portefeuille,$this->vandatum,$this->tmdatum,$this->portefeuilledata['afrekenvalutaKosten']),2);
		return $this->stortingen ;
	}

	function getOnttrekkingen()
	{
		if(empty($this->onttrekkingen))
			$this->onttrekkingen 		 			= round(getOnttrekkingen($this->portefeuille,$this->vandatum,$this->tmdatum,$this->portefeuilledata['afrekenvalutaKosten']),2);
		return $this->onttrekkingen;
	}

	function getstortingenOntrekkingen()
	{
		if(empty($this->stortingenOntrekkingen))
		{
			if(empty($this->stortingen))
				$this->stortingen 			 			= $this->getStortingen();
			if(empty($this->onttrekkingen))
				$this->onttrekkingen 		 			= $this->getOnttrekkingen();

			$this->stortingenOntrekkingen = $this->stortingen - $this->onttrekkingen;
		}
		return $this->stortingenOntrekkingen;
	}

	function getResultaat()
	{
		$this->stortingenOntrekkingen = $this->getstortingenOntrekkingen();

		if (empty($this->totaalWaarde['totaal']))
			$this->getTotaalWaarde();
		if (empty($this->totaalWaardeVanaf['totaal']))
			$this->getTotaalWaardeVanaf();

		$this->resultaat = $this->totaalWaarde['totaal'] - $this->totaalWaardeVanaf['totaal'] - $this->stortingen + $this->onttrekkingen;

		return $this->resultaat;
	}

	function getJaarResultaat()
	{
		global $__appvar;
		if(empty($this->jaarResultaat))
		{
			$this->jaarStortingen = round(getStortingen($this->portefeuille,$this->extrastart,$this->tmdatum),2);
			$this->jaarOnttrekkingen = round(getOnttrekkingen($this->portefeuille,$this->extrastart,$this->tmdatum),2);

			// haal totaalwaarde op startdatum
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				"FROM TijdelijkeRapportage WHERE ".
				" rapportageDatum = '".$this->extrastart."' AND ".
				//" portefeuille = '".$this->portefeuille."' "
				$this->portefeuillesFilter
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$this->DB->SQL($query);
			$this->DB->Query();
			$this->jaarTotaalWaardeVanaf = $this->DB->nextRecord();

			if (empty($this->totaalWaarde['totaal']))
				$this->getTotaalWaarde();
			$this->jaarResultaat = $this->totaalWaarde['totaal'] - $this->jaarTotaalWaardeVanaf['totaal'] - $this->jaarStortingen + $this->jaarOnttrekkingen;
		}
		return $this->jaarResultaat;
	}

	function getLiquiditeitenVanaf()
	{
		global $__appvar;
		if(empty($this->waardeLiquiditeitenVanaf))
		{
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				"FROM TijdelijkeRapportage WHERE ".
				" rapportageDatum = '".$this->vandatum."' AND ".
				//" portefeuille = '".$this->portefeuille."' AND ".
				$this->portefeuillesFilter.
				" AND type = 'rekening' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$this->DB->SQL($query);
			$this->DB->Query();
			$this->waardeLiquiditeitenVanaf = $this->DB->nextRecord();
		}
		return $this->waardeLiquiditeitenVanaf;
	}

	function getLiquiditeitenEind()
	{
		global $__appvar;
		if(empty($this->waardeLiquiditeitenEind))
		{
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				"FROM TijdelijkeRapportage WHERE ".
				" rapportageDatum = '".$this->tmdatum."' AND ".
				//" portefeuille = '".$this->portefeuille."' AND".
				$this->portefeuillesFilter.
				" AND type = 'rekening' "
				.$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$this->DB->SQL($query);
			$this->DB->Query();
			$this->waardeLiquiditeitenEind = $this->DB->nextRecord();
			//echo $query."<br>\n".$this->waardeLiquiditeitenEind['totaal']."<br>\n";exit;
		}
		return $this->waardeLiquiditeitenEind;
	}



	function getRekenvermogen($methode,$forceerBerekening=false)
	{
		/*
         "0"=>"Gemiddeld vermogen",
        "1"=>"Beginvermogen",
        "2"=>"Eindvermogen",
        "3"=>"Gecorrigeerd beginvermogen"
        "4"=>"3 maands ultimo"
        "5"=>"4 maands ultimo"
        "6"=>"dag gemiddelde"

        Kijk welk Basis rekenvermogen gebruikt moet worden
    */

		$this->stortingenOntrekkingen = $this->getstortingenOntrekkingen();
		if (empty($this->rekenvermogen) || $forceerBerekening==true)
		{
			switch($methode)
			{
				case 1 :
					if(empty($this->totaalWaardeVanaf['totaal']))
						$this->getTotaalWaardeVanaf();
					$this->rekenvermogen = $this->totaalWaardeVanaf['totaal'];
					break;
				case 2 :
					if(empty($this->totaalWaarde['totaal']))
						$this->gettotaalWaarde();
					$this->rekenvermogen = $this->totaalWaarde['totaal'];
					break;
				case 3 :
					if(empty($this->totaalWaardeVanaf['totaal']))
						$this->getTotaalWaardeVanaf();
					if(empty($this->resultaat))
						$this->getResultaat();
					if(empty($this->weging['totaal1']))
						$this->getWeging();
					$this->rekenvermogen = $this->totaalWaardeVanaf['totaal'] + $this->resultaat + $this->weging['totaal1'];
					$this->stortingenOntrekkingen  = $this->weging['totaal1'];
					break;
				case 4 :
					if($forceerBerekening)//&& $this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille'] <> ''
					{
						$noUpdateMaandwaarden=true;
					}
					if(empty($this->maandsGemiddelde) || $forceerBerekening)
						$this->getMaandsUltimo(3,'',$noUpdateMaandwaarden);
					$this->rekenvermogen = $this->maandsGemiddelde - $this->maandsLiquiditeitenGemiddelde;

					$this->gemiddeldeVermogen = $this->maandsGemiddelde;
					break;
				case 5 :
					if($forceerBerekening)//&& $this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille'] <> ''
					{
						$noUpdateMaandwaarden=true;
					}
					if(empty($this->maandsGemiddelde) || $forceerBerekening)
						$this->getMaandsUltimo(4,'',$noUpdateMaandwaarden);
					$this->rekenvermogen = $this->maandsGemiddelde - $this->maandsLiquiditeitenGemiddelde;
					$this->gemiddeldeVermogen = $this->maandsGemiddelde;
					break;
				case 6 :
					$this->rekenvermogen = $this->getDagGemidelde();
					break;
        case 7 :
          if($forceerBerekening)//&& $this->portefeuilledata['BeheerfeeToevoegenAanPortefeuille'] <> ''
          {
            $noUpdateMaandwaarden=true;
          }
          if(empty($this->maandsGemiddelde) || $forceerBerekening)
            $this->getmaandsGemiddelde(3,'',$noUpdateMaandwaarden);
          $this->rekenvermogen = $this->maandsGemiddelde - $this->maandsLiquiditeitenGemiddelde;
          $this->gemiddeldeVermogen = $this->maandsGemiddelde;
          break;
				default :
					if(empty($this->gemiddeldeVermogen))
						$this->getGemiddeldVermogen();
					$this->rekenvermogen = $this->gemiddeldeVermogen;
					break;
			}
			if($this->portefeuilledata['valutaUitsluiten'] == 1)
			{
				$this->basisRekenvermogen=$this->rekenvermogen;
				switch($methode)
				{
					case 1 :
						if(empty($this->waardeLiquiditeitenVanaf))
							$this->getLiquiditeitenVanaf();
						$this->rekenvermogen = $this->rekenvermogen - $this->waardeLiquiditeitenVanaf['totaal'];
						break;
					case 2 :
						if(empty($this->waardeLiquiditeitenEind))
							$this->getLiquiditeitenEind();
//echo " ".$this->rekenvermogen. " - ".$this->waardeLiquiditeitenEind['totaal']."<br>\n";
						$this->rekenvermogen = $this->rekenvermogen - $this->waardeLiquiditeitenEind['totaal'];
						break;
					case 3 :
						if(empty($this->waardeLiquiditeitenVanaf))
							$this->getLiquiditeitenVanaf();
						$this->rekenvermogen = $this->rekenvermogen -  $this->waardeLiquiditeitenVanaf['totaal'];
						break;
					case 4 :
						break;
					case 5 :
						break;
      case 6 :
        $liquiditeitenWaarde=0;
        $aantal=count($this->liquiditeitenWaardeOpDatum);
        foreach($this->liquiditeitenWaardeOpDatum as $datum=>$waarde)
             $liquiditeitenWaarde+=($waarde/$aantal);
        $this->rekenvermogen = $this->rekenvermogen - $liquiditeitenWaarde;
      break;
					default :
						if(empty($this->waardeLiquiditeitenVanaf))
							$this->getLiquiditeitenVanaf();
						if(empty($this->waardeLiquiditeitenEind))
							$this->getLiquiditeitenEind();
						$this->rekenvermogen = $this->rekenvermogen - ($this->waardeLiquiditeitenVanaf['totaal'] + $this->waardeLiquiditeitenEind['totaal']) / 2;
						break;
				}
				
			}
			elseif ($this->portefeuilledata['BeheerfeeLiquiditeitenViaModel'] == 1)
			{
				$this->basisRekenvermogen=$this->rekenvermogen;
				switch($methode)
				{
					case 1 :
						if($this->procentVerdeling['portefeuille']['beginWaarde']['rekening']  > $this->procentVerdeling['model']['beginWaarde']['rekening'])
						{
							$waardeZondeLiquiditeiten = $this->waardeVerdeling['portefeuille']['beginWaarde']['fondsen'] + $this->waardeVerdeling['portefeuille']['beginWaarde']['rente'];
							$this->rekenvermogen = $waardeZondeLiquiditeiten+($waardeZondeLiquiditeiten/(1-$this->procentVerdeling['model']['beginWaarde']['rekening'])*$this->procentVerdeling['model']['beginWaarde']['rekening']);
						}
						break;
					case 2 :
						if($this->procentVerdeling['portefeuille']['eindWaarde']['rekening']  > $this->procentVerdeling['model']['eindWaarde']['rekening'])
						{
							$waardeZondeLiquiditeiten = $this->waardeVerdeling['portefeuille']['eindWaarde']['fondsen'] + $this->waardeVerdeling['portefeuille']['eindWaarde']['rente'];
							$this->rekenvermogen =$waardeZondeLiquiditeiten+($waardeZondeLiquiditeiten/(1-$this->procentVerdeling['model']['eindWaarde']['rekening'])*$this->procentVerdeling['model']['eindWaarde']['rekening']);
						}
						break;
					case 3 :
						if($this->procentVerdeling['portefeuille']['beginWaarde']['rekening']  > $this->procentVerdeling['model']['beginWaarde']['rekening'])
						{
							$waardeZondeLiquiditeiten = $this->waardeVerdeling['portefeuille']['beginWaarde']['fondsen'] + $this->waardeVerdeling['portefeuille']['beginWaarde']['rente'];
							$this->rekenvermogen = $waardeZondeLiquiditeiten+($waardeZondeLiquiditeiten/(1-$this->procentVerdeling['model']['beginWaarde']['rekening'])*$this->procentVerdeling['model']['beginWaarde']['rekening']);
						}
						break;
					default :
						if($this->procentVerdeling['portefeuille']['beginWaarde']['rekening'] + $this->procentVerdeling['portefeuille']['eindWaarde']['rekening']  >
							$this->procentVerdeling['model']['beginWaarde']['rekening'] + $this->procentVerdeling['model']['eindWaarde']['rekening'])
						{
							$waardeZondeLiquiditeiten = ($this->waardeVerdeling['portefeuille']['beginWaarde']['fondsen'] + $this->waardeVerdeling['portefeuille']['beginWaarde']['rente'] +
									$this->waardeVerdeling['portefeuille']['eindWaarde']['fondsen'] + $this->waardeVerdeling['portefeuille']['eindWaarde']['rente']) / 2;
							$herrekendeLiq=($waardeZondeLiquiditeiten/((1-$this->procentVerdeling['model']['beginWaarde']['rekening'])+(1-$this->procentVerdeling['model']['beginWaarde']['rekening']))/2*
								(($this->procentVerdeling['model']['beginWaarde']['rekening']+$this->procentVerdeling['model']['beginWaarde']['rekening'])/2));
							$this->rekenvermogen = $waardeZondeLiquiditeiten + $herrekendeLiq;
						}
						break;
				}
			}
			elseif ($this->portefeuilledata['BeheerfeeLiquiditeitenAnderPercentage']==1)
			{
				$this->basisRekenvermogen=$this->rekenvermogen;
				switch($methode)
				{
					case 1 :
						if(empty($this->waardeLiquiditeitenVanaf))
							$this->getLiquiditeitenVanaf();
						if($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'] <> 0)
						{
							$liqPercentage=($this->waardeLiquiditeitenVanaf['totaal']/$this->rekenvermogen)*100;

							if($liqPercentage>$this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'])
							{
								$this->waardeLiquiditeitenVanaf['totaal']-=($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage']*$this->rekenvermogen/100);
							}
							else
								$this->waardeLiquiditeitenVanaf['totaal']=0;
						}
						$this->rekenvermogen = $this->rekenvermogen - $this->waardeLiquiditeitenVanaf['totaal'];
						$this->rekenvermogenLiquiditeiten = $this->waardeLiquiditeitenVanaf['totaal'];
						break;
					case 2 :
						if(empty($this->waardeLiquiditeitenEind))
							$this->getLiquiditeitenEind();
						if($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'] <> 0)
						{
							$liqPercentage=($this->waardeLiquiditeitenEind['totaal']/$this->rekenvermogen)*100;
							if($liqPercentage>$this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'])
							{
								$this->waardeLiquiditeitenEind['totaal']-=($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage']*$this->rekenvermogen/100);
							}
							else
								$this->waardeLiquiditeitenEind['totaal']=0;
						}
						$this->rekenvermogen = $this->rekenvermogen - $this->waardeLiquiditeitenEind['totaal'];
						$this->rekenvermogenLiquiditeiten = $this->waardeLiquiditeitenEind['totaal'];
						break;
					case 3 :
						if(empty($this->waardeLiquiditeitenVanaf))
							$this->getLiquiditeitenVanaf();
						if($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'] <> 0)
						{
							$liqPercentage=($this->waardeLiquiditeitenVanaf['totaal']/$this->rekenvermogen)*100;
							if($liqPercentage>$this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'])
							{
								$this->waardeLiquiditeitenVanaf['totaal']-=($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage']*$this->rekenvermogen/100);
							}
							else
								$this->waardeLiquiditeitenVanaf['totaal']=0;
						}
						$this->rekenvermogen = $this->rekenvermogen -  $this->waardeLiquiditeitenVanaf['totaal'];
						$this->rekenvermogenLiquiditeiten = $this->waardeLiquiditeitenVanaf['totaal'];
						break;
					case 4 :
						if(empty($this->maandsGemiddelde))
							$this->getMaandsUltimo(3,'',$noUpdateMaandwaarden);
						if($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'] <> 0)
						{
							$liqPercentage=($this->maandsLiquiditeitenGemiddelde/$this->maandsGemiddelde)*100;
							if($liqPercentage>$this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'])
							{
								$this->maandsLiquiditeitenGemiddelde-=($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage']*$this->maandsGemiddelde/100);
							}
							else
								$this->maandsLiquiditeitenGemiddelde=0;
						}
						$this->rekenvermogen = $this->maandsGemiddelde - $this->maandsLiquiditeitenGemiddelde;
						$this->rekenvermogenLiquiditeiten = $this->maandsLiquiditeitenGemiddelde;
						$this->gemiddeldeVermogen = $this->maandsGemiddelde;
						break;
					case 5 :
						if(empty($this->maandsGemiddelde))
							$this->getMaandsUltimo(4,'',$noUpdateMaandwaarden);
						if($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'] <> 0)
						{
							$liqPercentage=($this->maandsLiquiditeitenGemiddelde/$this->maandsGemiddelde)*100;
							if($liqPercentage>$this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'])
							{
								$this->maandsLiquiditeitenGemiddelde-=($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage']*$this->maandsGemiddelde/100);
							}
							else
								$this->maandsLiquiditeitenGemiddelde=0;
						}
						$this->rekenvermogen = $this->maandsGemiddelde - $this->maandsLiquiditeitenGemiddelde;
						$this->rekenvermogenLiquiditeiten = $this->maandsLiquiditeitenGemiddelde;
						$this->gemiddeldeVermogen = $this->maandsGemiddelde;
						break;
					default :
						if(empty($this->waardeLiquiditeitenVanaf))
							$this->getLiquiditeitenVanaf();
						if(empty($this->waardeLiquiditeitenEind))
							$this->getLiquiditeitenEind();
						if($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'] <> 0)
						{
							$liqPercentage=((($this->waardeLiquiditeitenVanaf['totaal']+$this->waardeLiquiditeitenEind['totaal'])/2)/$this->rekenvermogen)*100;

							if($liqPercentage>$this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage'])
							{
								$this->waardeLiquiditeitenVanaf['totaal']-=($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage']*$this->rekenvermogen/100);
								$this->waardeLiquiditeitenEind['totaal']-=($this->portefeuilledata['BeheerfeeLiquiditeitenAfroomPercentage']*$this->rekenvermogen/100);
							}
						}
						$this->rekenvermogen = $this->rekenvermogen - ($this->waardeLiquiditeitenVanaf['totaal'] + $this->waardeLiquiditeitenEind['totaal']) / 2;
						$this->rekenvermogenLiquiditeiten = ($this->waardeLiquiditeitenVanaf['totaal'] + $this->waardeLiquiditeitenEind['totaal']) / 2;
						break;
				}
			}
			if(!isset($this->basisRekenvermogen))
				$this->basisRekenvermogen=$this->rekenvermogen;

			if (empty($this->huisfondsKorting) && $this->portefeuilledata['BeheerfeeTeruggaveHuisfondsenPercentage'] > 0)
			{
				$this->getKorting();
				$this->huisfondsWaarde = round($this->huisfondsWaarde,2);
      //echo "<br>\nrekenvermogen:".$this->rekenvermogen."<br>\n" ;
				$this->rekenvermogen=$this->rekenvermogen-$this->huisfondsWaarde;
        
     //echo "<br>\nhuisfondsWaarde:".$this->huisfondsWaarde."<br>\n rekenvermogen".$this->rekenvermogen;exit;
			}

			if($this->portefeuilledata['BeheerfeeBedragBuitenFee'])
			{
				$this->rekenvermogen=$this->rekenvermogen-$this->portefeuilledata['BeheerfeeBedragBuitenFee'];
				if($this->rekenvermogen < 0)
					$this->rekenvermogen=0;
			}
			//$this->fondsWaardeBuitenFee=$this->getFondsWaardeBuitenFee();
			//$this->rekenvermogen=$this->rekenvermogen-$this->fondsWaardeBuitenFee;

		}
		return $this->rekenvermogen;
	}

	function getPeriodePerformance()
	{
		if(empty($this->performancePeriode))
			$this->performancePeriode = performanceMeting($this->portefeuille, $this->vandatum, $this->tmdatum,$this->portefeuilledata['PerformanceBerekening']);
		return $this->performancePeriode;
	}

	function getJaarPerformance()
	{
		if(empty($this->performanceJaar))
			$this->performanceJaar 	= performanceMeting($this->portefeuille, $this->extrastart, $this->tmdatum,$this->portefeuilledata['PerformanceBerekening']);
		return $this->performanceJaar;
	}

function getBeheerfeeAdministratieVergoeding($force=false)
	{
  if($this->portefeuilledata['BeheerfeeAdminVgConUitsluiten']==1 && $force==false)
    return 0;
// BeheerfeeAdministratieVergoeding
		if ($this->portefeuilledata['BeheerfeeAdministratieVergoeding'] <> 0)
		{
      if($this->portefeuilledata['BeheerfeeAdministratieVergoedingVast']==1)
        $this->administratieBedrag = $this->portefeuilledata['BeheerfeeAdministratieVergoeding'];
      elseif($this->portefeuilledata['BeheerfeeAdminVergoedingJaarlijks']==1)
      {
        if(substr($this->tmdatum,5,5)=='12-31')//'12-31'
          $this->administratieBedrag = $this->portefeuilledata['BeheerfeeAdministratieVergoeding'];
      }
			else
				$this->administratieBedrag = $this->portefeuilledata['BeheerfeeAdministratieVergoeding'] * $this->getPeriodeDeelVanJaar();
		}
		else
			$this->administratieBedrag = 0;

		return $this->administratieBedrag;
	}

	function getBeheerfeeBedrag($force=false)
	{
		if($this->portefeuilledata['BeheerfeeBedragVast']==1 && $force==false)
			return 0;

		$this->BeheerfeeBedrag = $this->portefeuilledata['BeheerfeeBedrag'];
		return $this->BeheerfeeBedrag;
	}

	function getStaffelPercentage()
	{
    // if($this->portefeuilledata['feeToevoegMethode'] == 1 )
		if(empty($this->Rekenvermogen))
			$this->getRekenvermogen($this->portefeuilledata['BeheerfeeBasisberekening'],true);

		$restwaarde = $this->rekenvermogen;

		$_bs1 = $this->portefeuilledata["BeheerfeeStaffel1"];
		$_bs2 = $this->portefeuilledata["BeheerfeeStaffel2"];
		$_bs3 = $this->portefeuilledata["BeheerfeeStaffel3"];
		$_bs4 = $this->portefeuilledata["BeheerfeeStaffel4"];
		$_bs5 = $this->portefeuilledata["BeheerfeeStaffel5"];

		if (($restwaarde > $_bs1) AND $_bs1 > 0 ) $maxStaf = 1;
		if (($restwaarde > $_bs2) AND $_bs2 > $_bs1) $maxStaf = 2;
		if (($restwaarde > $_bs3) AND $_bs3 > $_bs2) $maxStaf = 3;
		if (($restwaarde > $_bs4) AND $_bs4 > $_bs3) $maxStaf = 4;
		if (($restwaarde > $_bs5) AND $_bs5 > $_bs4) $maxStaf = 5;

		if($this->portefeuilledata['BeheerfeeSchijvenTarief'] == 1)
		{
			$schijf[1] = $this->portefeuilledata["BeheerfeeStaffelPercentage1"];
			$schijf[2] = $this->portefeuilledata["BeheerfeeStaffelPercentage2"];
			$schijf[3] = $this->portefeuilledata["BeheerfeeStaffelPercentage3"];
			$schijf[4] = $this->portefeuilledata["BeheerfeeStaffelPercentage4"];
			$schijf[5] = $this->portefeuilledata["BeheerfeeStaffelPercentage5"];

			$schijvenPerentage = $schijf[$maxStaf+1];

			$this->staffelTotaal = $restwaarde * ($schijvenPerentage /100);
			$this->staffelWaarden['schijvenPerentage']=$schijvenPerentage;
			//echo $this->staffelTotaal." = $restwaarde * ($schijvenPerentage /100)<br>\n";
		}
		else
		{
			/*
        start Chris methode
      */
			$_vastStaf[1] =  $_bs1          * ($this->portefeuilledata["BeheerfeeStaffelPercentage1"]/100);
			$_vastStaf[2] = ($_bs2 - $_bs1) * ($this->portefeuilledata["BeheerfeeStaffelPercentage2"]/100);
			$_vastStaf[3] = ($_bs3 - $_bs2) * ($this->portefeuilledata["BeheerfeeStaffelPercentage3"]/100);
			$_vastStaf[4] = ($_bs4 - $_bs3) * ($this->portefeuilledata["BeheerfeeStaffelPercentage4"]/100);
			$_vastStaf[5] = ($_bs5 - $_bs4) * ($this->portefeuilledata["BeheerfeeStaffelPercentage5"]/100);

			for ($x=1; $x <= $maxStaf; $x++)
			{
				$tmpWaarde = $_vastStaf[$x]/($this->portefeuilledata["BeheerfeeStaffelPercentage".$x]/100);
				$this->staffelWaarden[$x]=array('percentage'=>$this->portefeuilledata["BeheerfeeStaffelPercentage$x"],'waarde'=>$tmpWaarde,'fee'=>$_vastStaf[$x],'staffelBegin'=>$this->portefeuilledata["BeheerfeeStaffel".($x-1)],'staffelEind'=>$this->portefeuilledata["BeheerfeeStaffel".($x)]);
				$_fee += $_vastStaf[$x];
			}
			$_ss = "_bs".($maxStaf);
			$_rest = ($restwaarde - $$_ss);
			$_feerest = ($_rest * ($this->portefeuilledata["BeheerfeeStaffelPercentage".($maxStaf+1)]/100));

			$this->staffelWaarden[($maxStaf+1)]=array('percentage'=>$this->portefeuilledata["BeheerfeeStaffelPercentage".($maxStaf+1)],'waarde'=>round($_rest,2),'fee'=>round($_feerest,2),'staffelBegin'=>$this->portefeuilledata["BeheerfeeStaffel".($maxStaf-1)],'staffelEind'=>round($this->rekenvermogen,2));
			$this->staffelTotaal = $_fee + $_feerest;
		}
		/*
      einde Chris methode
    */

		return  $this->staffelTotaal;
	}

	function getPeriodeDeelVanJaar()
	{
		if (!isset($this->periodeDeelVanJaar))
		{
			$rapportageJaar = substr($this->tmdatum,0,4);

			$maanden=12/$this->portefeuilledata['BeheerfeeAantalFacturen'];
			$startMaand=date('n',db2jul($this->tmdatum))-$maanden;
			$startDatumJul=mktime(0,0,0,$startMaand+1,0,$rapportageJaar);
			$dagMinder=0;

			$BeheerfeeFacturatieVanaf=db2jul($this->portefeuilledata['BeheerfeeFacturatieVanaf']);
			if($startDatumJul < $BeheerfeeFacturatieVanaf && db2jul($this->tmdatum) >  $BeheerfeeFacturatieVanaf)
				$startDatumJul=$BeheerfeeFacturatieVanaf;

			if(db2jul($this->portefeuilledata['Startdatum']) > $startDatumJul)
			{
				$startDatum=$this->portefeuilledata['Startdatum'];
				$dagMinder=1;
			}
			else
				$startDatum=date('Y-m-d',$startDatumJul);

			$aantalDagen = round(((db2jul($this->tmdatum) - db2jul($startDatum))/86400),0)-$dagMinder;
			$aantalDagenInPeriode = round(((db2jul($this->tmdatum) - $this->julrapportVanaf)/86400),0);
//echo "<br>\n".((db2jul($this->tmdatum) - db2jul($startDatum))/86400);
			$aantalDagenInJaar = (mktime(0,0,0,1,1,$rapportageJaar+1) - mktime(0,0,0,1,1,$rapportageJaar))/86400;

			$this->periodeDeelVanJaar = $aantalDagen / $aantalDagenInJaar;
			$this->periodeDagen=array('dagen'=>$aantalDagen,'dagenInJaar'=>$aantalDagenInJaar,'periode'=>$startDatum."->".$this->tmdatum,'dagenInHelePeriode'=>$aantalDagenInPeriode);
			//echo "<br>\ndagen in jaar $aantalDagenInJaar = ".date('d-m-Y',mktime(0,0,0,1,1,$rapportageJaar+1))." - ".date('d-m-Y',mktime(0,0,0,1,1,$rapportageJaar))."<br>\n";
			// echo " aantal dagen $aantalDagen = (".$this->tmdatum." - ".$startDatum.") - 1 dag bij gebruikt van portefeuilleStartdatum <br>\n ";
			// echo   $this->periodeDeelVanJaar." = $aantalDagen / $aantalDagenInJaar           $rapportageJaar<br>";
		}
		return $this->periodeDeelVanJaar;
	}

	function getBeheerfee($methode,$forceerBerekening=false)
	{

		if(empty($this->rekenvermogen) || $forceerBerekening)
		{
			$this->getRekenvermogen($this->portefeuilledata['BeheerfeeBasisberekening'],$forceerBerekening);
		}

		if($methode == 0)
		{
			$this->beheerfeeOpJaarbasis = 0;
			$this->beheerfeePerPeriode = 0;
		}
		elseif($methode == 1 || $methode == 2 )
		{
			if(empty($this->staffelTotaal))
				$this->staffelTotaal = $this->getStaffelPercentage();

			if ($methode == 2)
			{
				$this->staffelTotaal = $this->staffelTotaal - (($this->staffelTotaal/100) * $this->portefeuilledata["BeheerfeeKortingspercentage"]);
			}
			$this->beheerfeeOpJaarbasis = $this->staffelTotaal;
		}
		elseif($methode == 3)
		{
			$this->beheerfeeOpJaarbasis = (($this->rekenvermogen/100) * $this->portefeuilledata['BeheerfeePercentageVermogen']);
		}
		elseif($methode == 4)
		{
			$this->beheerfeeOpJaarbasis = 0;//$this->getBeheerfeeBedrag();
		}
//  echo "<br>\n".$this->beheerfeeOpJaarbasis."<br>\n";
		$this->beheerfeeOpJaarbasis += $this->getBeheerfeeBedrag();
//  echo "<br>\n".$this->beheerfeeOpJaarbasis."<br>\n";
		$this->beheerfeeOpJaarbasis+=($this->rekenvermogenLiquiditeiten*$this->portefeuilledata['BeheerfeeLiquiditeitenPercentage']/100);

//  echo "<br>\n".$this->beheerfeeOpJaarbasis."<br>\n";
//rvv 2013-07-09    $this->beheerfeeOpJaarbasis-=$this->huisfondsFeeJaar;
		// echo $this->beheerfeeOpJaarbasis."<br>\n";
		//$this->rekenvermogenLiquiditeiten*$aandeelWaarde

		return $this->beheerfeeOpJaarbasis;
	}

	function getBestandvergoeding()
	{
		if($this->bestandsvergoeding)
			return $this->bestandsvergoeding;

		$updateIds=array();
		$vergoeding=0;
		$query="SELECT bedragUitbetaald FROM BestandsvergoedingPerPortefeuille WHERE portefeuille='".$this->portefeuille."' AND datumUitbetaald='".$this->tmdatum."'";// portefeuille = '".$this->portefeuille."'
		$this->DB->SQL($query); //echo $query."<br>\n";
		$this->DB->Query();
		while($data=$this->DB->nextRecord())
			$vergoeding += $data['bedragUitbetaald'];
//echo $this->portefeuilledata['module_bestandsvergoeding'];
		if($this->portefeuilledata['module_bestandsvergoeding'] == 1)
		{
			$query="SELECT
	  	BestandsvergoedingPerPortefeuille.id,
      BestandsvergoedingPerPortefeuille.portefeuille,
      BestandsvergoedingPerPortefeuille.bedragBerekend,
      round(BestandsvergoedingPerPortefeuille.bedragBerekend * (Bestandsvergoedingen.waardeHerrekend/Bestandsvergoedingen.waardeBerekend) ,2) as vergoeding,
      if(Vermogensbeheerders.verrekeningBestandsvergoeding = 0,
        if(Bestandsvergoedingen.datumOntvangen > '2000-01-01',round(BestandsvergoedingPerPortefeuille.bedragBerekend * (Bestandsvergoedingen.waardeHerrekend/Bestandsvergoedingen.waardeBerekend) ,2),0),
      if(Vermogensbeheerders.verrekeningBestandsvergoeding = 1,
        if(Bestandsvergoedingen.datumGeaccordeerd > '2000-01-01',round(BestandsvergoedingPerPortefeuille.bedragBerekend * (Bestandsvergoedingen.waardeHerrekend/Bestandsvergoedingen.waardeBerekend) ,2),0),0))
      AS vergoeding,Bestandsvergoedingen.vermogensbeheerder,Bestandsvergoedingen.emittent,Bestandsvergoedingen.depotbank,Bestandsvergoedingen.datumBerekend,Bestandsvergoedingen.waardeBerekend,Bestandsvergoedingen.datumHerrekend,Bestandsvergoedingen.waardeHerrekend,Bestandsvergoedingen.datumGeaccordeerd,Bestandsvergoedingen.datumOntvangen,Bestandsvergoedingen.datumUitbetaald,Bestandsvergoedingen.periodeVan,Bestandsvergoedingen.periodeTm
      FROM
        BestandsvergoedingPerPortefeuille
        Inner Join Bestandsvergoedingen ON Bestandsvergoedingen.id = BestandsvergoedingPerPortefeuille.bestandsvergoedingId
        Inner Join Vermogensbeheerders ON Bestandsvergoedingen.vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
      WHERE BestandsvergoedingPerPortefeuille.datumUitbetaald < '2000-01-01' AND (Bestandsvergoedingen.datumGeaccordeerd > '2000-01-01' OR Bestandsvergoedingen.datumOntvangen > '2000-01-01') AND
       ".$this->portefeuillesFilter;// BestandsvergoedingPerPortefeuille.portefeuille = '".$this->portefeuille."'";
			$db=new DB();
			$this->DB->SQL($query);
			$this->DB->Query();
			while($data=$this->DB->nextRecord())
			{
				$vergoeding += $data['vergoeding'];
				$updateIds[$data['id']]=$data['vergoeding'];
			}
		}
		elseif($this->portefeuilledata['module_bestandsvergoeding'] == 2)
		{
			$db=new DB();
			$query="SELECT BestandsvergoedingPerPortefeuille.id, BestandsvergoedingPerPortefeuille.bedragBerekend as vergoeding FROM BestandsvergoedingPerPortefeuille
	      WHERE BestandsvergoedingPerPortefeuille.portefeuille='".$this->portefeuille."' AND BestandsvergoedingPerPortefeuille.datumUitbetaald < '2000-01-01'  ";// ".$this->portefeuillesFilter."
			$this->DB->SQL($query);//echo $query."<br>\n";//AND change_date > '".$this->vandatum."'
			$this->DB->Query();
			while($data=$this->DB->nextRecord())
			{
				$vergoeding += $data['vergoeding'];
				$updateIds[$data['id']]=$data['vergoeding'];
				//listarray($updateIds);
			}
		}
		foreach ($updateIds as $id=>$waarde)
		{ //update het totaal op alle regels??
			if($this->portefeuilledata['module_bestandsvergoeding'] == 1)
				$updateWaarde=$vergoeding;
			else
				$updateWaarde=$waarde;

			$query="UPDATE BestandsvergoedingPerPortefeuille SET bedragUitbetaald='$updateWaarde', datumUitbetaald='".$this->tmdatum."' WHERE id='$id'";
			$this->DB->SQL($query);
			$this->DB->Query();
		}

		$this->bestandsvergoeding=$vergoeding;
		return $vergoeding;
	}

	function getFactuurregels()
	{
		$this->extraFactuurregels=array();
		$query="SELECT omschrijving,bedrag,btw FROM Factuurregels WHERE portefeuille='".$this->portefeuille."' AND datum='".$this->tmdatum."'";
		$this->DB->SQL($query); //echo $query."<br>\n";
		$this->DB->Query();
		while($data=$this->DB->nextRecord())
		{
			$data['btwBedrag']=$data['bedrag']*0.21;
			$this->extraFactuurregels['regels'][]=$data;
			if($data['btw']==1)
			{
				$this->extraFactuurregels['metBTW'] += $data['bedrag'];
				$this->extraFactuurregels['btwBedrag'] += $data['btwBedrag'];
			}
			else
				$this->extraFactuurregels['zonderBTW']+=$data['bedrag'];
		}

		return $this->extraFactuurregels;
	}

	function getTransactiefee()
	{

		if($this->transactiefee)
			return $this->transactiefee;

		if($this->portefeuilledata['FactuurMinimumPerTransactie'] <> 0)
			$minBedragPerTransactie=$this->portefeuilledata['FactuurMinimumPerTransactie'];
		else
			$minBedragPerTransactie=0;

		$query="SELECT sum(if(abs(bedrag) > $minBedragPerTransactie,1,0)) as aantal FROM Rekeningmutaties JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening WHERE 
  Boekdatum > '".$this->vandatum."' AND Boekdatum <='".$this->tmdatum."' AND Transactietype IN('A','V','A/O','A/S','V/O','V/S') AND
  portefeuille='".$this->portefeuille."' ";
		$this->DB->SQL($query);
		$this->DB->Query();
		$aantal=$this->DB->nextRecord();
		//

		$transactiefee=$aantal['aantal']*$this->portefeuilledata['BeheerfeeTransactiefeeKosten'];
		$this->transactiefee=$transactiefee;
		return $transactiefee;
	}

	function getValutaKoers($valuta='EUR',$datum)
	{
		if($valuta=='EUR')
			return 1;
		else
		{
			$db=new DB();
			$query="SELECT Koers FROM valutaKoersen WHERE Valuta='$valuta' AND datum <='$datum' order by datum desc limit 1";
			$db->SQL($query);
			$data=$db->lookupRecord();
			return $data['Koers'];
		}
	}

	function setError($error)
	{
		if (!is_array($this->errors))
			$this->errors= array();

		$this->errors[] = $error;
	}

	function berekenPortefeuilleWaarde($portefeuille, $rapportageDatum, $min1dag = false, $rapportageValuta = 'EUR',$rapportageBeginDatum,$afronding=2)
	{
		$sleutelWaarde='waarde|'.$portefeuille."|".$rapportageDatum."|".$min1dag."|".$rapportageValuta."|".$rapportageBeginDatum."|".$afronding;
		$query="SELECT sleutel,gegevens FROM tempFactuur WHERE sleutel='".mysql_real_escape_string($sleutelWaarde)."'";
		$this->DB2->SQL($query);
		$factuur=$this->DB2->lookupRecord();
		if($factuur['sleutel'] <> '')
		{
			$waarden = unserialize($factuur['gegevens']);
			logscherm("Waarde '".$portefeuille."' op $rapportageDatum uit buffer tabel gehaald.");
		}
		else
		{
			$waarden=berekenPortefeuilleWaarde($portefeuille, $rapportageDatum, $min1dag, $rapportageValuta,$rapportageBeginDatum,$afronding);
			$waardenSerialized=serialize($waarden);
			$query="INSERT INTO tempFactuur SET sleutel='".mysql_real_escape_string($sleutelWaarde)."', gegevens='".mysql_real_escape_string($waardenSerialized)."'";
			$this->DB2->SQL($query);
			$this->DB2->Query();
		  //logscherm("Waarde '".$portefeuille."' op $rapportageDatum in buffer tabel geplaatst.");
		}
		return $waarden;
	}

	function getFactuurWaarden($portefeuille,$vandatum,$tmdatum,$drempelPercentage)
	{
		$sleutelWaarde = 'factuur|' . $portefeuille . "|" . $vandatum . "|" . $tmdatum . "|" . $drempelPercentage;
		$query = "SELECT sleutel,gegevens FROM tempFactuur WHERE sleutel='" . mysql_real_escape_string($sleutelWaarde) . "'";
		$this->DB2->SQL($query);
		$factuur = $this->DB2->lookupRecord();
		if ($factuur['sleutel'] <> '')
		{
			$factuurWaarden = unserialize($factuur['gegevens']);
			logscherm("Factuur '" . $portefeuille . "' over $vandatum -> $tmdatum uit buffer tabel gehaald.");
		}
		else
		{
			$factuurObject = new factuurBerekening($portefeuille, $vandatum, $tmdatum, $drempelPercentage);
			$factuurWaarden = $factuurObject->berekenWaarden();
			$huisSerialized = serialize($factuurWaarden);
			$query = "INSERT INTO tempFactuur SET sleutel='" . mysql_real_escape_string($sleutelWaarde) . "', gegevens='" . mysql_real_escape_string($huisSerialized) . "'";
			$this->DB2->SQL($query);
			$this->DB2->Query();
			logscherm("Factuur '" . $portefeuille. "' over $vandatum -> $tmdatum in buffer tabel geplaatst.");
		}
		return $factuurWaarden;
	}

}

?>