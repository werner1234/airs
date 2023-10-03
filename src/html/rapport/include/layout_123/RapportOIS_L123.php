<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
//ini_set('max_execution_time',60);
class RapportOIS_L123
{

	function RapportOIS_L123($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    
		$this->pdf->rapport_titel = "Overzicht aandelen naar sector";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->filterCategorie='H-Equity';
		$this->filterVariabele='hoofdcategorie';
    $this->selectVariabele='beleggingssector';
    $this->regelsOnderdrukken=false;
	}
  
  function getAanVerkopen()
  {
    $DB = new DB();
//Sum(Rekeningmutaties.Aantal*Rekeningmutaties.Fondskoers*Rekeningmutaties.Valutakoers) AS waarde,
    $query="SELECT
Rekeningmutaties.Boekdatum as LaatsteBoekdatum,
Rekeningmutaties.Fonds,
Rekeningen.Portefeuille,
(Rekeningmutaties.Aantal) AS aantal,
Rekeningmutaties.Transactietype,
(Rekeningmutaties.Credit-Rekeningmutaties.Debet) AS waardeValuta,
((Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)-(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)) AS waarde,
(Rekeningmutaties.Bedrag) as bedrag,
CASE
    WHEN Rekeningmutaties.Transactietype ='V' THEN \"V\"
    WHEN Rekeningmutaties.Transactietype ='V/S' THEN \"V\"
    WHEN Rekeningmutaties.Transactietype ='A/S' THEN \"V\"
    WHEN Rekeningmutaties.Transactietype ='L' THEN \"A\"
    ELSE \"A\"
END as Transactietype2
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$this->portefeuille."' AND Rekeningmutaties.Grootboekrekening='FONDS' AND Rekeningmutaties.Transactietype IN('A','A/O','A/V','D', 'V','V/S','A/S','L','B','V/O') AND Rekeningmutaties.Boekdatum <='".$this->rapportageDatum."'
ORDER BY Transactietype2,	Rekeningmutaties.Fonds, Rekeningmutaties.Boekdatum";
    $DB->SQL($query);
    $DB->Query();
    $transacties=array();
    
    //$verkopen=$aanverkopen['V'];//+$aanverkopen['V/S']+$aanverkopen['A/S']+$aanverkopen['L'];
    //$aankopen=$aanverkopen['A'];//+$aanverkopen['A/O']+$aanverkopen['V/O']+$aanverkopen['D'];
    while($data=$DB->nextRecord())
    {
      if(!isset($transacties[$data['Transactietype2']][$data['Fonds']]))
      {
        $transacties[$data['Transactietype2']][$data['Fonds']] = $data;
      }
      else
      {
        if($data['Transactietype'] <> 'B')
        {
          $transacties[$data['Transactietype2']][$data['Fonds']]['aantal'] += $data['aantal'];
          $transacties[$data['Transactietype2']][$data['Fonds']]['waardeValuta'] += $data['waardeValuta'];
          $transacties[$data['Transactietype2']][$data['Fonds']]['waarde'] += $data['waarde'];
          $transacties[$data['Transactietype2']][$data['Fonds']]['bedrag'] += $data['bedrag'];
          $transacties[$data['Transactietype2']][$data['Fonds']]['LaatsteBoekdatum'] = $data['LaatsteBoekdatum'];
        }
      }
    }
    //listarray($transacties);
    return $transacties;
    
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  function formatAantal($waarde, $dec)
  {
    $getal = explode('.',$waarde);
    $decimaalDeel = $getal[1];
    if ($decimaalDeel != '0000' )
    {
      for ($i = strlen($decimaalDeel); $i >=0; $i--)
      {
        $decimaal = $decimaalDeel[$i-1];
        if (!isset($newDec) && $decimaal != '0')
        {
          $newDec = $i;
        }
      }
      return number_format($waarde,$newDec,",",".");
    }
    else
      return number_format($waarde,$dec,",",".");
   
  }

	function header($categorie,$addPage=false)
  {
    if($addPage==true)
      $this->pdf->addPage();

    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    if($categorie=='H-FixInc')
    {
      $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->SetWidths(array(54,24,22.5,22.5,22.5,22,22,24,22,22,22));
      $this->pdf->Row(array(
        "\n \n ",
        vertaalTekst("Duurzaamheids indicator\n ", $this->pdf->rapport_taal),
        vertaalTekst("Aantal /\nNominale waarde", $this->pdf->rapport_taal),
        vertaalTekst("Aankoop\nwaarde\n ", $this->pdf->rapport_taal),
        vertaalTekst("Marktwaarde\n \n ", $this->pdf->rapport_taal),
        vertaalTekst("Opgelopen\nrente \n ", $this->pdf->rapport_taal),
        vertaalTekst("Coupon\ndatum\n ", $this->pdf->rapport_taal),
        vertaalTekst("Ongerealiseerd resultaat\n ", $this->pdf->rapport_taal),
        vertaalTekst("Looptijd\nresterend\n ", $this->pdf->rapport_taal),
        vertaalTekst("Rating \n \n ", $this->pdf->rapport_taal),
        vertaalTekst("Weging portefeuille\n ", $this->pdf->rapport_taal)
      ));
      $this->pdf->Row(array(
        "\n \n ",
        "\n \n ",
        vertaalTekst("Valuta\n \n ", $this->pdf->rapport_taal),
        vertaalTekst("Gemiddele\naankoop\nprijs", $this->pdf->rapport_taal)."*",
        vertaalTekst("Slotkoers*\n \n ", $this->pdf->rapport_taal),
        vertaalTekst("Effectief\nrendement\n ", $this->pdf->rapport_taal),
        vertaalTekst("Coupon\nrendement\n ", $this->pdf->rapport_taal),
        vertaalTekst("Ongerealiseerd\nresultaat %\n ", $this->pdf->rapport_taal),
        vertaalTekst("Looptijd\naangepast\n ", $this->pdf->rapport_taal),
        vertaalTekst("\n \n ", $this->pdf->rapport_taal),
        vertaalTekst("Weging categorie\n ", $this->pdf->rapport_taal)
      ));

    }
    elseif($this->filterCategorie=='HAA-Liquidit' || $this->pdf->rapport_type=='HSE')
    {
      $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R'));
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1);
      $this->pdf->SetWidths(array(65,30,30,30,30,30,30,35));
      $this->pdf->Row(array(vertaalTekst("Rekening \n ", $this->pdf->rapport_taal), vertaalTekst("Aantal /\nNominale waarde", $this->pdf->rapport_taal), vertaalTekst("Valuta\n ", $this->pdf->rapport_taal), vertaalTekst("Rentevoet\n ", $this->pdf->rapport_taal), vertaalTekst("Rentedatum\n ", $this->pdf->rapport_taal), vertaalTekst("Marktwaarde\n ", $this->pdf->rapport_taal), vertaalTekst("Opgelopen rente\n ", $this->pdf->rapport_taal), vertaalTekst("Weging portefeuille\n ", $this->pdf->rapport_taal)));
      $this->pdf->Row(array("\n ", "\n ", vertaalTekst("Wissel\nkoers", $this->pdf->rapport_taal), "\n ", "\n ","\n ", "\n ", vertaalTekst("Weging categorie\n ", $this->pdf->rapport_taal)));
    }
    else
    {
      $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
      $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
      $this->pdf->SetWidths(array(45,10,25,25,25,25,25,25,25,25,25));
      $this->pdf->Row(array(
        "\n ",
        "\n ",
        vertaalTekst("Aantal\n ", $this->pdf->rapport_taal),
        vertaalTekst("Valuta\n ", $this->pdf->rapport_taal),
        vertaalTekst("Gemiddelde \nKostprijs ", $this->pdf->rapport_taal),
        vertaalTekst("Slotkoers \n ", $this->pdf->rapport_taal),
        vertaalTekst("Aanschaf\nwaarde EUR ", $this->pdf->rapport_taal),
        vertaalTekst("Ongerealiseerd \nresultaat EUR ", $this->pdf->rapport_taal),
        vertaalTekst("Marktwaarde \n EUR resultaat", $this->pdf->rapport_taal),
        vertaalTekst("Weging portefeuille ", $this->pdf->rapport_taal),
        vertaalTekst("Weging categorie ", $this->pdf->rapport_taal)
      ));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$this->pdf->rapport_highRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
  }

  function headerVerdeling()
  {
    $this->pdf->setAligns(array("L",'L','R','R'));
    $this->pdf->fillCell=array(0,1,1,1);

    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetWidths(array(130,80,35+35));
    $this->pdf->Row(array("","\n ",vertaalTekst("Portefeuille verdeling\n ", $this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(130,80,35,35));
    $this->pdf->Row(array("","\n ",vertaalTekst("in EUR\n ", $this->pdf->rapport_taal),vertaalTekst("Weging\n ", $this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$this->pdf->rapport_highRow;
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);

  }
  function verdelingRegel($data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('','U','U','U');
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->Row(array('',$data[0],$this->formatGetal($data[1],0),$this->formatGetal($data[2],2)."%"));
    unset($this->pdf->CellBorders);
  }
  function printVerdelingTotaal($data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_donkergrijs[0],$this->pdf->rapport_donkergrijs[1],$this->pdf->rapport_donkergrijs[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->fillCell=array(0,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',$data[0],$this->formatGetal($data[1],0),$this->formatGetal($data[2],2)."%"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
    unset($this->pdf->fillCell);
  }

  function printKop($omschrijving,$data)
  {
    if($omschrijving=='alleenTotaal')
      return '';
    if($this->filterCategorie=='H-FixInc')
    {
      $this->printOblKop($omschrijving, $data);
    }
    elseif($this->filterCategorie=='HAA-Liquidit' || $this->pdf->rapport_type=='HSE')
    {
      $this->printLiqKop($omschrijving, $data);
    }
    else
    {
      $this->printSectorRegioKop($omschrijving, $data);
    }
  }

  function printVerdelingKop($omschrijving,$waarde)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array(0,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array('',$omschrijving,
                      $this->formatGetal($waarde,0)."",
                      $this->formatGetal($waarde/$this->categorieWaarde*100,2)."%"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }

  function printSectorRegioKop($omschrijving,$data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array($omschrijving."","\n","\n","\n","\n","\n",
                      $this->formatGetal($data['historischeWaardeEUR'],0)." ",
                      $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0)." ",
                      $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']-$data['historischeWaardeEUR']),0)." ",
                      $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$this->totaalWaarde*100,2)."%",
                      $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$this->categorieWaarde*100,2)."%"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }

  function printOblKop($omschrijving,$data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array($omschrijving."\n ","\n ","\n ",
                      $this->formatGetal($data['historischeWaardeEUR'],0)."\n ",
                      $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0)."\n ",
                      $this->formatGetal(($data['renteactuelePortefeuilleWaardeEuro']),0)."\n ",
                      "\n ",$this->formatGetal(($data['actuelePortefeuilleWaardeEuro']-$data['historischeWaardeEUR']),0)."\n ","\n ","\n ",
                      $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']+$data['renteactuelePortefeuilleWaardeEuro'])/$this->totaalWaarde*100,2)."%\n".
                      $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']+$data['renteactuelePortefeuilleWaardeEuro'])/$this->categorieWaarde*100,2)."%"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }

  function printLiqKop($omschrijving,$data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array($omschrijving."\n ","\n ","\n ","\n ","\n ",
                      $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0)."\n ","\n ",
                      $this->formatGetal(($data['actuelePortefeuilleWaardeEuro'])/$this->totaalWaarde*100,2)."%\n".$this->formatGetal(($data['actuelePortefeuilleWaardeEuro'])/$this->categorieWaarde*100,2)."%"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }

  function printTotaal($omschrijving,$data)
  {
    $this->pdf->SetFillColor($this->pdf->rapport_donkergrijs[0],$this->pdf->rapport_donkergrijs[1],$this->pdf->rapport_donkergrijs[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->fillCell=array(1,1,1,1,1,1,1,1,1,1,1);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    if($this->filterCategorie=='H-FixInc')
    {
      $this->pdf->Row(array($omschrijving."\n ","\n ","\n ",
                        $this->formatGetal($data['historischeWaardeEUR'],0)."\n ",
                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0)."\n ",
                        $this->formatGetal(($data['renteactuelePortefeuilleWaardeEuro']),0)."\n ",
                        "\n".$this->formatGetal($data['gewogenYield'], 2).'%',
                        $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']-$data['historischeWaardeEUR']),0)."\n ",
                        $this->formatGetal($data['gewogenDuration'], 2)."\n".$this->formatGetal($data['gewogenModifiedDuration'], 2),"\n ",
                        $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']+$data['renteactuelePortefeuilleWaardeEuro'])/$this->totaalWaarde*100,2)."%\n".$this->formatGetal(($data['actuelePortefeuilleWaardeEuro']+$data['renteactuelePortefeuilleWaardeEuro'])/$this->categorieWaarde*100,2)."%"));
    }
    elseif($this->filterCategorie=='HAA-Liquidit'  || $this->pdf->rapport_type=='HSE')
    {
      $this->pdf->Row(array($omschrijving."\n ","\n ","\n ","\n ","\n ",$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0)."\n ","\n ",
                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$this->totaalWaarde*100,2)."%\n".$this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$this->categorieWaarde*100,2)."%"));

    }
    else
    {
      $this->pdf->Row(array($omschrijving . "\n", "\n", "\n", "\n", "\n", "\n",
                        $this->formatGetal($data['historischeWaardeEUR'], 0),
                        $this->formatGetal($data['actuelePortefeuilleWaardeEuro'], 0),
                        $this->formatGetal(($data['rente'] - $data['historischeWaardeEUR']), 0),
                        $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']+$data['renteactuelePortefeuilleWaardeEuro']) / $this->totaalWaarde * 100, 2) . "%",
                        $this->formatGetal(($data['actuelePortefeuilleWaardeEuro']+$data['renteactuelePortefeuilleWaardeEuro']) / $this->categorieWaarde * 100, 2) . "%"));
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);

  }

  function printRegel($data)
  {
    if(isset($data['fondsen']['onderdrukken']) && $data['fondsen']['onderdrukken'] ==1)
      return '';
    if($this->filterCategorie=='H-FixInc')
    {
      $this->printOblRegel($data);
    }
    elseif($this->filterCategorie=='HAA-Liquidit' || $this->pdf->rapport_type=='HSE')
    {
      $this->printLiqRegel($data);
    }
    else
    {
      $this->printRegioSectorRegel($data);
    }
  }

  function printRegioSectorRegel($data)
  {
    $fondsdata=$data['fondsen'];
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $ongerealiseerdResultaat=($fondsdata['actuelePortefeuilleWaardeEuro']-$fondsdata['historischeWaardeEUR']);
    $ongerealiseerdResultaatLopendeJaar=($fondsdata['actuelePortefeuilleWaardeEuro']-$fondsdata['beginPortefeuilleWaardeEuro']);
    if(isset($fondsdata['aankopen']['LaatsteBoekdatum']) && $fondsdata['aankopen']['Transactietype'] <> 'B')
    {
      $laatsteBoekdatum=date('d/m/Y',db2jul($fondsdata['aankopen']['LaatsteBoekdatum']));
    }
    else
    {
      $laatsteBoekdatum='';
    }
    $omschrijving=$this->testTxtLength($fondsdata['FondsOmschrijving'],0);
    $this->pdf->Row(array($omschrijving,
                      $fondsdata['duurzaamCategorieOmschrijving'],
                      $this->formatAantal($fondsdata['totaalAantal'],0),
                      $fondsdata['Valuta'],
                      $this->formatGetal($fondsdata['historischeWaarde'],2),
                      $this->formatGetal($fondsdata['actueleFonds'],2),
                      $this->formatGetal($fondsdata['historischeWaardeEUR'],0),
                      $this->formatGetal($fondsdata['actuelePortefeuilleWaardeEuro'],0),
                      $this->formatGetal(($ongerealiseerdResultaat),0),
                      $this->formatGetal($fondsdata['actuelePortefeuilleWaardeEuro']/$this->totaalWaarde*100,2)."%",
                      $this->formatGetal($fondsdata['actuelePortefeuilleWaardeEuro']/$this->categorieWaarde*100,2)."%"
    ));

    unset($this->pdf->CellBorders);
    //  $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);

  }

  function printLiqRegel($data)
  {

    $rekeningTypes = array(
      "cash"        => "Standaard geldrekening",
      "AABBELSP"    => "AAB Beleggersspaarrek.",
      "AABONDDEP"   => "AAB Ondernemersdep.",
      "AABORR"      => "AAB Optimale Renterekening",
      "AABSPAAR"    => "AAB Spaarrekening",
      "AABVERM"     => "AAB Vermogens Spaarrek.",
      "AABzakenrek" => "AAB Zakenrekening",
      "AABMPPRC"    => "AAB MeesP Part Rek Crt",
      "AABPBS"      => "AAB Priv Banking Spaarrek.",
      "AABMPPBS"    => "AAB MeesP Private Banking Spaarrek",
      "MARGIN"      => "Margin rekening",
      "VLER"        => "Van Lanschot Effectenrekening",
      "KIR"         => "KIR-rekening",
      "ZICHT"       => "Zicht-rekening",
    );


    $rekeningdata=$data['rekening'];
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $omschrijving=$this->testTxtLength(trim($rekeningdata['FondsOmschrijving']),1);
    $this->pdf->Row(array($omschrijving."\n".trim($rekeningdata['IBANnr'].' '.(isset($rekeningTypes[$rekeningdata['typeRekening']])?$rekeningTypes[$rekeningdata['typeRekening']]:$rekeningdata['typeRekening'])),//$rekeningdata['Rekening'],
                      $this->formatGetal($rekeningdata['actuelePortefeuilleWaardeInValuta'],2),
                      $rekeningdata['Valuta']."\n".($rekeningdata['Valuta']<>'EUR'?$this->formatGetal($rekeningdata['ActueleValuta'],4):''),
                      '','',
                      $this->formatGetal($rekeningdata['actuelePortefeuilleWaardeEuro'],0),
                      $this->formatGetal($rekeningdata['rente'],0),
                      $this->formatGetal($rekeningdata['actuelePortefeuilleWaardeEuro']/$this->totaalWaarde*100,2)."%\n".$this->formatGetal($rekeningdata['actuelePortefeuilleWaardeEuro']/$this->categorieWaarde*100,2)."%"));

    unset($this->pdf->CellBorders);
    //  $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);

  }

  function printOblRegel($data)
  {
    $fondsdata=$data['fondsen'];
    $rentedata=$data['rente'];
    $obldata=$data['oblBerekening'];
    $this->pdf->SetFillColor($this->pdf->rapport_grijs[0],$this->pdf->rapport_grijs[1],$this->pdf->rapport_grijs[2]);
    $this->pdf->fillCell=array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U','U');
    $this->pdf->setDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $ongerealiseerdResultaat=($fondsdata['actuelePortefeuilleWaardeEuro']-$fondsdata['historischeWaardeEUR']);
    $omschrijving=$this->testTxtLength($fondsdata['FondsOmschrijving'],0);
    //$ongerealiseerdResultaatLopendeJaar=($fondsdata['actuelePortefeuilleWaardeEuro']-$fondsdata['beginPortefeuilleWaardeEuro']);
    if(isset($fondsdata['aankopen']['LaatsteBoekdatum'])  && $fondsdata['aankopen']['Transactietype'] <> 'B' )
    {
      $laatsteBoekdatum=date('d/m/Y',db2jul($fondsdata['aankopen']['LaatsteBoekdatum']));
    }
    else
    {
      $laatsteBoekdatum='';
    }
    $this->pdf->Row(array($omschrijving."\n".$fondsdata['ISINCode'],
                      $fondsdata['duurzaamCategorieOmschrijving'],
                      $this->formatAantal($fondsdata['totaalAantal'],0)."\n".$fondsdata['Valuta'],
                      $this->formatGetal($fondsdata['historischeWaardeEUR'],0)."\n".$this->formatGetal($fondsdata['historischeWaarde'],4),
                      $this->formatGetal($fondsdata['actuelePortefeuilleWaardeEuro'],0)."\n".$this->formatGetal($fondsdata['actueleFonds'],2),
                      $this->formatGetal($rentedata['actuelePortefeuilleWaardeEuro'],0)."\n".$this->formatGetal($obldata['yield'],2),
                      date('d/m',db2jul($fondsdata['Rentedatum']))."\n".$this->formatGetal($obldata['Rentepercentage'],2)."%",
                      $this->formatGetal(($ongerealiseerdResultaat),0)."\n". $this->formatGetal(($ongerealiseerdResultaat/$fondsdata['historischeWaardeEUR']*100),2)."%",
                      $this->formatGetal($obldata['duration'],2)."\n".$this->formatGetal($obldata['modifiedDuration'],2),
                      $fondsdata['fondsRating']."\n". ($this->filterCategorie!='H-FixInc' ? $laatsteBoekdatum:''),
                      $this->formatGetal($fondsdata['actuelePortefeuilleWaardeEuro']/$this->totaalWaarde*100,2)."%\n".$this->formatGetal($fondsdata['actuelePortefeuilleWaardeEuro']/$this->categorieWaarde*100,2)."%"));

    unset($this->pdf->CellBorders);
    //  $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);

  }

  function printVoettekst()
  {
    $this->pdf->setWidths(array(200));
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'I',$this->pdf->rapport_fontsize);
    $this->pdf->ln(3);
    $this->pdf->Row(array("* De bedragen in deze kolom worden getoond in de valuta van de belegging."));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }

	function writeRapport()
	{
		global $__appvar;
    if($this->pdf->rapport_type == "OIS")
      $this->regelsOnderdrukken=true;
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $this->allekleuren = unserialize($kleuren['grafiek_kleur']);

    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal,
     TijdelijkeRapportage.".$this->filterVariabele." ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'].
    "GROUP BY TijdelijkeRapportage.".$this->filterVariabele."";
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $this->totaalWaarde=0;
    $this->categorieWaarde=0;
    $categorieWaarden=array();
    while($data = $DB->nextRecord())
    {
      $this->totaalWaarde+= $data['totaal'];
      $categorieWaarden[$data[$this->filterVariabele]]=$data['totaal'];
    }

    $this->categorieWaarde=$categorieWaarden[$this->filterCategorie];
    $aanVerkopen=$this->getAanVerkopen();

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

    $poly=array($this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,30,
      $this->pdf->w-$this->pdf->marge,35,
      $this->pdf->marge,35);

    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(200));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(28);

    $filtercategorieVertalingen=array('HAA-Equity'=>'aandelen','H-FixInc'=>'obligaties','HAA-Liquidit'=>'liquiditeiten','H-MoneyMkt'=>'Liquiditeiten');
    $intro='';
    if($this->selectVariabele=='beleggingssector')
    {
      $intro="Deze tabel toont de verdeling van de vermogenscategorie ".$filtercategorieVertalingen[$this->filterCategorie]." naar de verschillende sectoren.";
      $sortering="";
    }
    elseif($this->selectVariabele=='regio')
    {
      $intro="Deze tabel toont de verdeling van de vermogenscategorie ".$filtercategorieVertalingen[$this->filterCategorie]." naar de verschillende regio's.";
    }

    $leftJoin='';
    if($this->filterCategorie=='H-FixInc')
    {
      $intro="Deze tabel toont de verdeling van vastrentende beleggingen naar subcategorie.";
      $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
      $this->cashfow->genereerTransacties();
      $this->cashfow->genereerRows();
    }
    elseif($this->filterCategorie=='HAA-Altern')
    {
      $intro="Deze tabel toont de verdeling van alternatieve beleggingen naar subcategorie.";
    }
    elseif($this->filterCategorie=='HAA-Liquidit' || $this->pdf->rapport_type=='HSE')
    {
      $intro="Deze tabel toont de verdeling van de liquiditeiten.";
      $leftJoin='LEFT JOIN Rekeningen ON TijdelijkeRapportage.Rekening=Rekeningen.Rekening AND Rekeningen.consolidatie=0 LEFT';
      $extraVeld='Rekeningen.IBANnr, Rekeningen.typeRekening,';
      $extraOrder=',TijdelijkeRapportage.Rekening';
    }

    $this->pdf->Row(array(vertaalTekst(str_replace('  ', ' ', $intro), $this->pdf->rapport_taal)));
    $this->pdf->ln(8);
    $this->header($this->filterCategorie);

    $query = "SELECT $extraVeld TijdelijkeRapportage.".$this->selectVariabele."Omschrijving as Omschrijving, TijdelijkeRapportage.Fonds,Fondsen.rating as fondsRating,TijdelijkeRapportage.Rekening,
Fondsen.Lossingsdatum,
Fondsen.Rentedatum,
Fondsen.Renteperiode,
Fondsen.variabeleCoupon,
    TijdelijkeRapportage.FondsOmschrijving,Fondsen.ISINCode, duurzaamCategorieOmschrijving, TijdelijkeRapportage.Valuta, TijdelijkeRapportage.ActueleValuta,
       TijdelijkeRapportage.beleggingssectorOmschrijving AS secOmschrijving , ".
      " TijdelijkeRapportage.beleggingssector, TijdelijkeRapportage.totaalAantal, ".
      " TijdelijkeRapportage.beginPortefeuilleWaardeEuro, ".
      " TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,TijdelijkeRapportage.hoofdcategorie,TijdelijkeRapportage.beleggingscategorie,
      TijdelijkeRapportage.type,
      TijdelijkeRapportage.historischeWaarde, TijdelijkeRapportage.actueleFonds,
       (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeEUR".
      " FROM TijdelijkeRapportage
      $leftJoin JOIN Fondsen ON TijdelijkeRapportage.Fonds=Fondsen.Fonds ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " ".$this->filterVariabele."='".$this->filterCategorie."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " ORDER BY TijdelijkeRapportage.".$this->selectVariabele."Volgorde, TijdelijkeRapportage.FondsOmschrijving $extraOrder";
    debugSpecial($query,__FILE__,__LINE__);

    $DB->SQL($query);
    $DB->Query();

    $somVelden=array('beginPortefeuilleWaardeEuro','actuelePortefeuilleWaardeEuro','historischeWaardeEUR');
    $verdeling=array();
    $totalen=array();
    while($data = $DB->NextRecord())
    {
      if(($data['type']=='fondsen'||$data['type']=='rente') && $data['FondsOmschrijving'] <> '')
        $ident=$data['FondsOmschrijving'];
      else
        $ident=$data['FondsOmschrijving']."_".$data['Rekening'].'_'.$data['type'];

      if($this->regelsOnderdrukken==true)
      {
        if($data['fondsen']['hoofdcategorie']=='HAA-Equity' && $data['fondsen']['beleggingscategorie']=='AA-Equity-DevMk')
        {
          $data['onderdrukken'] = 1;
          $data['Omschrijving']='alleenTotaal';
          $ident='alleenTotaal';
        }
      }

      foreach($somVelden as $veld)
      {
        if($data['type']=='rente')
        {
          $verdeling[$this->filterCategorie][$data['Omschrijving']]['totaal']['rente' . $veld] += $data[$veld];
          $totalen['rente' . $veld] += $data[$veld];
        }
        else
        {
          $verdeling[$this->filterCategorie][$data['Omschrijving']]['totaal'][$veld] += $data[$veld];
          $totalen[$veld] += $data[$veld];
        }
      }
      if(isset($aanVerkopen['A'][$data['Fonds']]))
        $data['aankopen']=$aanVerkopen['A'][$data['Fonds']];

      $verdeling[$this->filterCategorie][$data['Omschrijving']]['regels'][$ident][$data['type']]=$data;

      if($this->filterCategorie=='H-FixInc' && $data['type']=='fondsen')
      {
        $oblData=$this->ObligatieBerekening($data);
//        debug($this->filterCategorie);
//        debug($oblData);
        $this->oblData[] = $oblData;//array_merge($oblData,$data);
        $this->fondsData[] = $data;
        $verdeling[$this->filterCategorie][$data['Omschrijving']]['regels'][$ident]['oblBerekening']=$oblData;
        foreach($oblData as $veld=>$waarde)
        {
          if(substr($veld,0,7)=='gewogen')
          {
            $totalen[$veld] += $waarde;
          }
        }
      }

    }

    foreach($verdeling[$this->filterCategorie] as $omschrijving=>$details)
    {
      if($this->pdf->getY()>177)
      {
        $this->header($this->filterCategorie,true);
      }
      $kop = $omschrijving;
      if ( $kop === 'Other' ) {
        $kop = 'Alternatieven';
      }

      $this->printKop(vertaalTekst($kop, $this->pdf->rapport_taal),$details['totaal']);
      foreach($details['regels'] as $regelData)
      {
        if($this->pdf->getY()>177)
        {
          $this->header($this->filterCategorie,true);
        }
        $this->printRegel($regelData);
      }
    }
    if($this->pdf->getY()>177)
      $this->header($this->filterCategorie,true);
    if($this->pdf->rapport_type == "VOLK")
      $this->printTotaal(vertaalTekst("Totaal/Germiddelden", $this->pdf->rapport_taal),$totalen);
    else
      $this->printTotaal(vertaalTekst("Totaal ".$filtercategorieVertalingen[$this->filterCategorie], $this->pdf->rapport_taal),$totalen);

    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    unset($this->pdf->fillCell);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);

    if($this->pdf->rapport_type == "VOLK" || $this->pdf->rapport_type == "OIH")
      $this->printVoettekst();

    if($this->filterCategorie=='H-FixInc' || $this->filterCategorie=='HAA-Altern'|| $this->filterCategorie=='HAA-Liquidit' || $this->pdf->rapport_type=='HSE')
    {
      return ;
    }
    
    
    $query='';
    $kleuren=array();
    $kleurVar='OIB';
//    debug($this->selectVariabele);
    if($this->selectVariabele=='beleggingssector')
    {
      
      $query="SELECT
TijdelijkeRapportage.BeleggingssectorOmschrijving as Omschrijving,
TijdelijkeRapportage.Fonds,
TijdelijkeRapportage.Beleggingscategorie as waarde,
TijdelijkeRapportage.Hoofdcategorie,
TijdelijkeRapportage.beleggingssector as waardeTijdelijkeRap,
sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.BeleggingscategorieOmschrijving as BeleggingscategorieOmschrijving
FROM TijdelijkeRapportage
WHERE
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND TijdelijkeRapportage.Hoofdcategorie='".$this->filterCategorie."'

AND TijdelijkeRapportage.Hoofdcategorie='".$this->filterCategorie."'
GROUP BY TijdelijkeRapportage.Beleggingscategorie,TijdelijkeRapportage.beleggingssector
ORDER BY TijdelijkeRapportage.BeleggingscategorieVolgorde,TijdelijkeRapportage.beleggingssectorVolgorde,TijdelijkeRapportage.Beleggingscategorie";

      $this->pdf->rapport_titel="Sector analyse ".$filtercategorieVertalingen[$this->filterCategorie];
      $koptekst="In deze tabel en grafiek ziet u de sectorverdeling van de aandelenportefeuille in procenten uitgedrukt alsook de verdeling volgens het risicoprofiel";
      $grafiekTitel = str_replace('  ',' ',"Sector verdeling ".$filtercategorieVertalingen[$this->filterCategorie]." ontwikkelde markten");
      $kleurVar='OIS';
      $grafiek='pie';
      //echo $query;exit;
    }
    elseif($this->selectVariabele=='regio')
    {
      $query = "SELECT KeuzePerVermogensbeheerder.waarde, Regios.Omschrijving FROM KeuzePerVermogensbeheerder
JOIN Regios ON KeuzePerVermogensbeheerder.waarde = Regios.Regio WHERE categorie='regios' AND KeuzePerVermogensbeheerder.vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
ORDER BY KeuzePerVermogensbeheerder.afdrukvolgorde, Regios.Omschrijving ";
  
      $query="SELECT
TijdelijkeRapportage.regio as waarde,
sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.regioOmschrijving as Omschrijving,
TijdelijkeRapportage.Fonds,
TijdelijkeRapportage.Beleggingscategorie,
TijdelijkeRapportage.BeleggingscategorieOmschrijving,
TijdelijkeRapportage.Hoofdcategorie
FROM
TijdelijkeRapportage
WHERE
TijdelijkeRapportage.portefeuille = '".$this->portefeuille."'  AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND TijdelijkeRapportage.Hoofdcategorie='".$this->filterCategorie."'
GROUP BY TijdelijkeRapportage.regio
ORDER BY regioVolgorde,BeleggingssectorOmschrijving";
      
      $this->pdf->rapport_titel="Regio analyse ".$filtercategorieVertalingen[$this->filterCategorie];
      $koptekst="In deze tabel en grafiek ziet u de regioverdeling van de ".$filtercategorieVertalingen[$this->filterCategorie]."portefeuille in procenten uitgedrukt alsook de verdeling volgens het risicoprofiel";
      $grafiekTitel='Regio verdeling';
      $kleurVar='OIR';
      $grafiek='pie';
    }

    foreach($this->allekleuren[$kleurVar] as $var=>$kleur)
    {
      $kleuren[$var]=array($kleur['R']['value'],$kleur['G']['value'],$kleur['B']['value']);
    }

    if($query<>'')
    {
      $DB->SQL($query);
      $DB->Query();
      $verdelingGrafiek = array();
      $verdelingTabelExtra =  array();
      $verdelingTotaalPerCategorie=array();
      while ($data = $DB->NextRecord())
      {
        $percentage=$data['actuelePortefeuilleWaardeEuro']/$this->categorieWaarde*100;

        if($data['Hoofdcategorie']=='HAA-Equity' && $data['Beleggingscategorie'] <>'AA-Equity-DevMk')
        {
          $data['Omschrijving']='alleenTotaal';
          $verdelingGrafiek[$data['BeleggingscategorieOmschrijving']] += $percentage;
          $verdelingGrafiekKleuren[$data['BeleggingscategorieOmschrijving']]=$kleuren[$data['waardeTijdelijkeRap']];
        }
        else
        {
          $verdelingGrafiek[$data['Omschrijving']] += $percentage;
          $verdelingGrafiekKleuren[$data['Omschrijving']]=( isset ($kleuren[$data['waardeTijdelijkeRap']]) ? $kleuren[$data['waardeTijdelijkeRap']] : $kleuren[$data['waarde']]);
        }
        
        $verdelingTabelExtra[$data['BeleggingscategorieOmschrijving']][$data['Omschrijving']]+=$data['actuelePortefeuilleWaardeEuro'];
        $verdelingTotaalPerCategorie[$data['BeleggingscategorieOmschrijving']]+=$data['actuelePortefeuilleWaardeEuro'];
      
      }
    }

    arsort($verdelingGrafiek);
    $kleurSort = $verdelingGrafiek;
    $verdelingGrafiekKleuren = array_merge($kleurSort, $verdelingGrafiekKleuren);

    if ( ! isset ($this->skipVerdeling) || $this->skipVerdeling === false ) {
      if(!isset($verdelingTabelExtra))
      {
        foreach ($verdeling[$this->filterCategorie] as $categorie => $details)
        {
          $verdelingTabelExtra['laag1'][$categorie] += $details['totaal']['actuelePortefeuilleWaardeEuro'];
        }
      }

      if( ($this->pdf->GetY() + 150) > $this->pdf->PageBreakTrigger) {
        $this->pdf->AddPage($this->pdf->CurOrientation);
        $this->pdf->rect($this->pdf->marge, $this->pdf->getY()-3, $this->pdf->w-($this->pdf->marge*2)-1, 10, 'F',null,$this->pdf->rapport_lichtgrijs);
      }


      $this->pdf->setAligns(array('L'));
      $this->pdf->SetWidths(array(200));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

      $this->pdf->Row(array(vertaalTekst($koptekst, $this->pdf->rapport_taal)));
      $this->pdf->ln(8);

      $this->headerVerdeling();
      $totalen=array("Totaal ".$filtercategorieVertalingen[$this->filterCategorie]);
      $lastCat='';
     // listarray($verdelingTotaalPerCategorie);
     // listarray($verdelingTabelExtra);
      foreach($verdelingTabelExtra as $laag1=>$verdelingTabel)
      {
        if($laag1<>'laag1')
        {
          //$this->pdf->row(array('',$laag1));
  //        $this->printVerdelingKop($laag1,$verdelingTotaalPerCategorie[$laag1]);
        }
        arsort($verdelingTabel);
        foreach($verdelingTabel as $omschrijving=>$waarde)
        {


          $percentage=$waarde/$this->categorieWaarde*100;
          $totalen[1]+=$waarde;
          $totalen[2]+=$percentage;
          if($omschrijving<>'alleenTotaal')
          {
            $this->verdelingRegel(array($omschrijving, $waarde, $percentage));

          }

        }
      }

      $this->printVerdelingTotaal($totalen);
      if($grafiek=='bar')
      {
        $this->pdf->setXY(40, 50);
        $this->BarDiagram(70, 100, $verdelingGrafiek, vertaalTekst($grafiekTitel, $this->pdf->rapport_taal));
      }
      elseif($grafiek=='pie')
      {
        $this->pdf->setXY(10, 45);
        $hoogte = 60;
        $yCorrectie = ($hoogte / 2) - (count($verdelingGrafiek) * 3) / 2;

        PieChart_L123($this->pdf, $hoogte, $hoogte, $verdelingGrafiek, '%l - %p', array_values($verdelingGrafiekKleuren), vertaalTekst($grafiekTitel, $this->pdf->rapport_taal), array($this->pdf->getX() + $hoogte + 5, $this->pdf->getY() + $yCorrectie + 5));
      }

    }
    unset($this->pdf->fillCell);
    $this->pdf->rowHeight=$this->pdf->rapport_lowRow;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);
	}
  
  function testTxtLength($txt,$cell=1)
  {
    $stringWidth=$this->pdf->GetStringWidth($txt."   ");
    if($stringWidth < $this->pdf->widths[$cell])
    {
      return $txt;
    }
    else
    {
      $tmpTxt=$txt;
      for($i=strlen($txt); $i > 0; $i--)
      {
        if($this->pdf->GetStringWidth($tmpTxt."...   ")>$this->pdf->widths[$cell])
          $tmpTxt=substr($txt,0,$i);
        else
          return $tmpTxt.'...';
      }
      return $tmpTxt;
    }
  }
  
  
  
  function ObligatieBerekening($data)
  {
    $totalen=array();
//    debug($data);
    if($data['Lossingsdatum'] <> '')
      $lossingsJul = adodb_db2jul($data['Lossingsdatum']);
    else
      $lossingsJul=0;
    $rentedatumJul = adodb_db2jul($data['Rentedatum']);
    $renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));
  
    $koers=getRentePercentage($data['Fonds'],$this->rapportageDatum);
  
    $renteDag=0;
    if($data['variabeleCoupon'] == 1)
    {
      $rapportJul=adodb_db2jul($this->rapportageDatum);
      $renteJul=adodb_db2jul($data['Rentedatum']);
      $renteStap=($data['Renteperiode']/12)*31556925.96;
      $renteDag=$renteJul;
      if($renteStap > 100000)
        while($renteDag<$rapportJul)
        {
          $renteDag+=$renteStap;
        }
    }

    $aandeel=$data['actuelePortefeuilleWaardeEuro']/$this->categorieWaarde;
    $aandeelTotaal=$data['actuelePortefeuilleWaardeEuro']/$this->totaalWaarde;
    if($lossingsJul > 0)
    {
      $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;
      $p = $data['actueleFonds'];
      $r = $koers['Rentepercentage']/100;
      $b = $this->cashfow->fondsDataKeyed[$data['Fonds']]['lossingskoers'];
      $year = $jaar;
      $ytm=  $this->cashfow->bondYTM($p,$r,$b,$year)*100;
    
      $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

      $duration=$this->cashfow->waardePerFonds[$data['Fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$data['Fonds']]['ActueelWaarde'];
      if($data['variabeleCoupon'] == 1 && $renteDag <> 0)
        $modifiedDuration=($renteDag-db2jul($this->rapportageDatum))/86400/365;
      else
        $modifiedDuration=$duration/(1+$ytm/100);
  
      $totalen['aandeelCategorie']=$aandeel;
      $totalen['aandeelTotaal']=$aandeelTotaal;
      $totalen['ytm']=$ytm;
      $totalen['duration']=$duration;
      $totalen['modifiedDuration']=$modifiedDuration;
      $totalen['Rentepercentage']=$koers['Rentepercentage'];
      $totalen['yield']=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['ActueleValuta'];
      $totalen['restLooptijd']=$restLooptijd;
    
      $totalen['gewogenYield']+=$koers['Rentepercentage']*$data['totaalAantal']/$data['actuelePortefeuilleWaardeEuro']*$data['ActueleValuta']*$aandeel;
      $totalen['gewogenYtm']+=$ytm*$aandeel;
      $totalen['gewogenDuration']+=$duration*$aandeel;
      $totalen['gewogenModifiedDuration']+=$modifiedDuration*$aandeel;
      $totalen['gewogenRestLooptijd']+=$restLooptijd*$aandeel;
    
     
    }
    return $totalen;
  }
  
  
  function BarDiagram($w, $h, $data,$titel)
  {
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();

    $legendWidth=10;
    $YDiag = $YPage;
    $hDiag = floor($h);
    $XDiag = $XPage +  $legendWidth;
    $lDiag = floor($w - $legendWidth);
    $maxVal=0;
    $minVal=0;
    $aantalRegels=count($data);
    
    if ($maxVal == 0) {
      $maxVal = max($data)*1.1;
    }
    if ($minVal == 0) {
      $minVal = min($data)*1.1;
    }
    if($minVal > 0)
      $minVal=0;
  
    
    $maxVal=ceil($maxVal/10)*10;
    if($maxVal<0)
      $maxVal=0;
  
    $bandBreedte=($maxVal-$minVal);
    if($bandBreedte>50)
      $step=10;
    else
      $step=5;
    
    
    $offset=$minVal;
    $unit = $lDiag / $bandBreedte;
    $hBar = floor($hDiag / ($aantalRegels + 1));
    
    if($hBar>5)
      $hBar=5;
    
    $aantal=count($data);
    if($aantal>25)
      $hBar=4;
    
    $hDiag = $hBar * ($aantalRegels + 1);
    
    //echo "$hBar <br>\n";
    $eBaton = floor($hBar * 60 / 100);
    
    $color=array();
    $this->pdf->SetLineWidth(0.2);
   // $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->Line($XDiag, $YDiag+$hDiag, $XDiag+$lDiag, $YDiag+$hDiag);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $nullijn=$XDiag - ($offset * $unit);
    
    $i=0;
    $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
    if(round($step,5) <> 0.0)
    {
      //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
      for($x=$nullijn;$x>=$XDiag; $x=$x-$step*$unit)
      {
        $i++;
        if($i>100)
          break;
        
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5,  $this->formatGetal(($x-$nullijn)/$unit,0).'%',0,0,'C');

      }
      
      $i=0;
      for($x=$nullijn;$x<=($XDiag+$lDiag); $x=$x+$step*$unit)
      {
        $i++;
        if($i>100)
          break;
        
        if(round(($x-$nullijn)/$unit)==0)
          continue;
        $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
        $this->pdf->setXY($x,$YDiag + $hDiag);
        $this->pdf->Cell(0.1, 5, $this->formatGetal(($x-$nullijn)/$unit,0).'%',0,0,'C');
        

      }
    }
    
    $i=0;
    //$this->pdf->SetDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetXY($this->pdf->marge, $YDiag);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($lDiag, -7, $titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
    foreach($data as $key=>$val)
    {

      $xval = $nullijn;
      $lval = ($val * $unit);
      $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
      $yvalLine = $YDiag + ($i + 1) * $hBar - $hBar/2;
      $hval = $eBaton;
      $this->pdf->Line($nullijn, $yvalLine, $nullijn-0.5, $yvalLine);
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'F');
      $this->pdf->SetXY($XPage, $yval);
      $this->pdf->Cell($legendWidth , $hval, $key,0,0,'R');
      $i++;
    }
  
    $this->pdf->Rect($XPage+$w/2-8, $YDiag + $hDiag+7.25 , 1.5, 1.5, 'F',null,$this->pdf->rapport_donkergroen);
    $this->pdf->setXY($XPage,$YDiag + $hDiag+6);
    $this->pdf->Cell($w,4, vertaalTekst('Portefeuille', $this->pdf->rapport_taal),0,0,'C');
  }
  
 
}
?>