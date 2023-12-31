<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportVKMS_L102
{
  function RapportVKMS_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum,$laatstejaar=true)
  {
    if(is_object($pdf))
    {
      $this->pdf = &$pdf;
      $this->pdf->rapport_type = "VKMS";
      $this->pdf->rapport_datum = db2jul($rapportageDatum);
      $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
      $this->pdf->rapport_jaar = date('Y', $this->pdf->rapport_datum);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->rapport_titel = "Vergelijkende kostenmaatstaf";
      $this->pdfVullen=true;
      $this->ValutaKoersEind=$this->pdf->ValutaKoersEind;
    }
    else
      $this->pdfVullen=false;


    if(!isset($this->pdf->PortefeuilleStartdatum))
    {
      $db=new DB();
      $query = "SELECT Portefeuilles.portefeuille, Portefeuilles.Clientvermogensbeheerder, Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.portefeuille='$portefeuille' limit 1";
      $db->SQL($query);
      $pdata = $db->lookupRecord();
      $this->pdf->PortefeuilleStartdatum=$pdata['Startdatum'];

    }

    $this->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->rapport_datum = db2jul($rapportageDatum);
    $this->rapport_jaar = date('Y', $this->rapport_datum);
    $this->ValutaKoersEind=1;
    if($laatstejaar==true)
      $this->vanafDatum=($this->rapport_jaar-1).date('-m-d',$this->rapport_datum);
    else
      $this->vanafDatum=$rapportageDatumVanaf;
    $this->vanafJul=db2jul($this->vanafDatum);
    $this->pdf->rapport_datumvanaf=$this->vanafJul;
    $portefeuilleStartJul=db2jul($this->pdf->PortefeuilleStartdatum);
    $this->melding="";
    $this->perioden=array();
    $this->queryVanaf=$this->vanafDatum;
    if($portefeuilleStartJul>$this->vanafJul)
    {
      $oldstart=$this->vanafDatum;
      $this->queryVanaf=date('Y-m-d',$portefeuilleStartJul);
      $this->pdf->rapport_datumvanaf =$portefeuilleStartJul;//+86400
      $this->vanafDatum=date('Y-m-d',$portefeuilleStartJul);//+86400
      $dagen=($this->pdf->rapport_datum-$portefeuilleStartJul)/86400;//+86400
      $this->vanafJul=$portefeuilleStartJul;//+86400;

      $this->melding= vertaalTekst("Door onvoldoende historie bedraagt de rapportage periode",$this->pdf->rapport_taal)." ".round($dagen)." ".vertaalTekst("dagen",$this->pdf->rapport_taal).".";
    }
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $this->vanafDatum;
    $this->rapportageDatum = $rapportageDatum;
    $this->periode=round(($this->rapport_datum-$this->vanafJul)/86400)/round((mktime(0,0, 0 ,12,31,$this->rapport_jaar)-mktime(0,0, 0 ,1,0,$this->rapport_jaar))/86400);
    //echo round(($this->rapport_datum-$this->vanafJul)/86400) . "/".(mktime(0,0, 0 ,12,31,$this->rapport_jaar)-mktime(0,0, 0 ,1,0,$this->rapport_jaar))/86400 . "<br>\n";
    //echo date('d-m-Y',$this->vanafJul)." ". $this->periode;exit;


    $this->pdf->excelData[]=array('Categorie','Fonds',date('d-m-Y',$this->pdf->rapport_datumvanaf),
      date('d-m-Y',$this->pdf->rapport_datum),'Mutaties','Resultaat','Gemiddeld vermogen',
      'Doorl.kosten %','FundTransCost %','FundPerfFee %','dl kosten absoluut','Weging','VKM bijdrage','ISIN code','Valuta');
    $this->verdelingTotaal=array();
    $this->verdelingFondsen=array();
    $this->skipSummary=false;
    $this->skipDetail=true;
    $this->skipLangeTermijn=true;
  }

  function formatGetal($waarde, $dec,$procent=false,$toonNul=false)
  {
    if($waarde==0 && $toonNul==false)
      return '';
    $data=number_format($waarde,$dec,",",".");
    if($procent==true)
      $data.=" %";
    return $data;
  }


  function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
  {
    if($waarde==0)
      return;
    if ($VierDecimalenZonderNullen)
    {
      $getal = explode('.',$waarde);
      $decimaalDeel = $getal[1];
      if ($decimaalDeel != '0000' )
      {
        for ($i = strlen($decimaalDeel); $i >=0; $i--)
        {
          $decimaal = $decimaalDeel[$i-1];
          if ($decimaal != '0' && !$newDec)
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



  function printSubTotaal($lastCategorieOmschrijving,$allData,$style='')
  {
    if($this->pdf->getY()+4>$this->pdf->PageBreakTrigger)
      $this->pdf->addPage();
    if($lastCategorieOmschrijving != 'Totaal')
    {
      $prefix='Subtotaal';
      $this->pdf->CellBorders = array('','','TS','TS','TS','TS','TS','','','','TS','TS','TS','TS','TS');
    }
    else
    {
      $prefix='';
      $this->pdf->CellBorders = array('','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),'','','',array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'),array('TS','UU'));
    }

    $this->pdf->SetFont($this->pdf->rapport_font,$style,$this->pdf->rapport_fontsize);

    $this->pdf->Cell(40,4,vertaalTekst("$prefix",$this->pdf->rapport_taal)." ".vertaalTekst($lastCategorieOmschrijving,$this->pdf->rapport_taal),0,'L');
    $this->pdf->setX($this->pdf->marge);

    $data=$allData['perf'];

    $this->pdf->row(array('','',
      $this->formatGetal($data['beginwaarde'],0),
      $this->formatGetal($data['eindwaarde'],0),
      $this->formatGetal($data['stort'],0),
      $this->formatGetal($data['resultaat'],0),
      $this->formatGetal($data['gemWaarde'],0),
      '','',
      $this->formatGetal($data['dlkostenPercentage'],0),
      $this->formatGetal($data['dlkostenAbsoluut'],0),
      $this->formatGetal($data['weging']*100,2,true),
      $this->formatGetal($data['bijdrageVKM'],2,true)
    ));

    $this->pdf->CellBorders = array();
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }

  function printKop($title, $type='',$ln=false)
  {
    if($ln)
      $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,$type,$this->pdf->rapport_fontsize);
    $this->pdf->Cell(40,4,vertaalTekst($title,$this->pdf->rapport_taal),0,1,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  }

  function vulVorigJaar($portefeuille)
  {
    if(substr($this->vanafDatum,5,5)=='01-01')
      $startjaar=true;
    else
      $startjaar=false;
    $fondswaarden =  berekenPortefeuilleWaarde($portefeuille, $this->vanafDatum,$startjaar);
    vulTijdelijkeTabel($fondswaarden ,$portefeuille, $this->vanafDatum);
    $this->extraVulling = true;

  }

  function kostenKader($portefeuille,$totaalDoorlopendekosten,$perfTotaal,$totaalDoorlopendekostenGesplitst)
  {

    if ($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta<>'')
    {
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "";
    }

    $DB=new DB();
    $query="SELECT CRM_naw.naam FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $crm=$DB->nextRecord();


    $query="SELECT SUM(abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet $koersQuery)+abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit $koersQuery)) AS totaal
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
WHERE Rekeningen.Portefeuille='".$portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->rapportageDatum."'
AND Rekeningen.Memoriaal = 0 AND Rekeningmutaties.Grootboekrekening='FONDS'  AND
Rekeningmutaties.Transactietype IN('A','A/O','A/S','V','V/O','V/S')
GROUP BY Rekeningmutaties.Grootboekrekening";
    $DB->SQL($query);
    $DB->Query();
    $spreadKosten=$DB->nextRecord();
    $spreadKostenEUR=($this->spreadKostenPunten / 10000 * $spreadKosten['totaal']);

    $alleGrootboekWaarden=array();
    $query="SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ))*-1  AS totaal,
Rekeningmutaties.Grootboekrekening,
Grootboekrekeningen.Omschrijving
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening
WHERE Rekeningen.Portefeuille='".$portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening
ORDER BY Grootboekrekeningen.Afdrukvolgorde";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      if($data['Grootboekrekening']=='KOBU')
        $data['Omschrijving']='Overige kosten';
      $alleGrootboekWaarden[$data['Grootboekrekening']]=$data['totaal'];
    }

//echo "$spreadKostenEUR=(".$this->spreadKostenPunten." / 10000 * ".$spreadKosten['totaal']."); <br>\n";exit;
    $query="SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ))*-1  AS totaal,
Rekeningmutaties.Grootboekrekening,
Grootboekrekeningen.Omschrijving
FROM Rekeningmutaties
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening=Grootboekrekeningen.Grootboekrekening AND (Grootboekrekeningen.Kosten=1 OR Rekeningmutaties.Grootboekrekening IN('BEH','BEW','KOST') )
WHERE Rekeningen.Portefeuille='".$portefeuille."' AND Rekeningmutaties.Boekdatum>'".$this->vanafDatum."' AND Rekeningmutaties.Boekdatum<='".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening
ORDER BY Grootboekrekeningen.Afdrukvolgorde";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $gemiddelde=$this->verdelingTotaal[$portefeuille]['totaal']['gemiddelde'];
    $doorlopendeKostenPercentage = $totaalDoorlopendekosten / $perfTotaal['gemWaarde'];
//echo "$doorlopendeKostenPercentage = $totaalDoorlopendekosten / ".$perfTotaal['gemWaarde']."<br>\n";exit;


    $barData=array();



    $percentage=$perfTotaal['percentageIndirectVermogenMetKostenfactor'];//$gemWaardeBeleggingen/($gemiddelde+$totaalDoorlopendekosten);
    $herrekendeKosten=$doorlopendeKostenPercentage/$percentage;
    $aandeelIndirect=$perfTotaal['gemWaarde']/$gemiddelde;
    $vkmPercentagePortefeuille=$herrekendeKosten*$aandeelIndirect*100;
    $barData['Lopende kosten']=$vkmPercentagePortefeuille;
    if($this->pdfVullen==true)
    {

    }
    $totaal=0;
    $grootBoekKostenTotaal=0;
    $kostenPerGrootboek=array('BEH','BEW','KOST','KOBU');
    $grootboekKosten=array('BEH'=>0,'BEW'=>0,'KOST'=>0,'overige'=>0);
    $grootboekOmschrijving=array('BEH'=>'Beheerfee','BEW'=>'Bewaarloon','KOST'=>'Transactiekosten','overige'=>'Overige kosten');
    while($data = $DB->nextRecord())
    {
      if(!in_array($data['Grootboekrekening'],$kostenPerGrootboek))
      {
        $data['Grootboekrekening'] = 'overige';
        $data['Omschrijving']='Overige kosten';
      }
      if($data['Grootboekrekening']=='KOBU')
      {
        $data['Grootboekrekening'] = 'KOST';
        $data['Omschrijving'] = 'Transactiekosten';
      }

      $grootboekKosten[$data['Grootboekrekening']]+=$data['totaal'];
      if(!isset($grootboekOmschrijving[$data['Grootboekrekening']]))
        $grootboekOmschrijving[$data['Grootboekrekening']]=$data['Omschrijving'];
      $totaal+=$data['totaal'];
      $kostenProcent=$data['totaal']/$gemiddelde*100;
      $barData[$data['Omschrijving']]+=$kostenProcent;

    }


    if($spreadKostenEUR <> 0)
    {
      $kostenProcent = $spreadKostenEUR / $gemiddelde * 100;
      $totaal += $spreadKostenEUR;
      $barData['Spread-kosten'] = $kostenProcent;

      $grootboekKosten[$data['Spread-kosten']]+=$spreadKostenEUR;
      $grootboekOmschrijving['Spread-kosten']='Spread-kosten';
    }

    $grootBoekKostenTotaal=$totaal;
    //echo " $grootBoekKostenTotaal=$totaal;";exit;

    $kostenPercentage=$totaal/$gemiddelde*100;
    $vkmWaarde=$vkmPercentagePortefeuille + $kostenPercentage;

    $vkmArray=array('vkmPercentagePortefeuille'=>$vkmPercentagePortefeuille,'kostenPercentage'=>$kostenPercentage,'vkmWaarde'=>$vkmWaarde,'grootboekKosten'=>$grootboekKosten,'grootboekOmschrijving'=>$grootboekOmschrijving,
      'gemiddeldeWaarde'=>$gemiddelde,'grootBoekKostenTotaal'=>$grootBoekKostenTotaal,'totaalDoorlopendekosten'=>$totaalDoorlopendekosten,'totaalDirecteKosten'=>$totaal,
      'totaalDoorlopendekostenGesplitst'=>$totaalDoorlopendekostenGesplitst,'doorlopendeKostenPercentage'=>$doorlopendeKostenPercentage,'percentageIndirectVermogenMetKostenfactor'=>$perfTotaal['percentageIndirectVermogenMetKostenfactor'],
      'fondsGemiddeldeWaarde'=>$perfTotaal['gemWaarde'],'alleGrootboekWaarden'=>$alleGrootboekWaarden,'naam'=>$crm['naam']);
    if($this->portefueille=$portefeuille)
    {
      $this->vkmWaarde[$portefeuille] = $vkmArray;
      $this->barData[$portefeuille]=$barData;
    }
    return $vkmArray;

  }

  function getGewogenStortingenOnttrekkingen($portefeuille,$van,$tot)
  {
    if ($this->pdf->rapportageValuta <> 'EUR' && $this->pdf->rapportageValuta<>'')
    {
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    }
    else
    {
      $koersQuery = "";
    }
    $DB=new DB();

    $query = "SELECT " .
      "SUM(((TO_DAYS('".$tot."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
      "  / (TO_DAYS('".$tot."') - TO_DAYS('".$van."')) ".
      "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS gewogen, " .
      "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal " .
      "FROM  (Rekeningen, Portefeuilles)
	       Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening " .
      "WHERE " .
      "Rekeningen.Portefeuille = '" . $portefeuille . "' AND " .
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND " .
      "Rekeningmutaties.Verwerkt = '1' AND " .
      "Rekeningmutaties.Boekdatum > '".$van."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
      "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();
    return $weging;
  }

  function getGewogenStortingenOnttrekkingenFondsen($portefeuille,$datumBegin,$datumEind,$rekeningFondsenWhere,$koersQuery)
  {
    $DB=new DB();
    $queryAttributieStortingenOntrekkingen = "SELECT ".
      "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
      "  * ((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
      "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
      "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Rekeningmutaties.Transactietype<>'B' AND ".
      "Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Grootboekrekening='FONDS' AND ".
      "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
      " $rekeningFondsenWhere ";
    $DB->SQL($queryAttributieStortingenOntrekkingen);//echo $queryAttributieStortingenOntrekkingen;
    $DB->Query();
    $weging = $DB->NextRecord();
    return $weging;
  }

  function berekenPortefeuille($portefeuille)
  {
    $DB=new DB();
    $this->vulVorigJaar($portefeuille);

    foreach ($this->perioden as $periode)
    {
      $portefeuileWaarde = array();
      $dagenPeriode = round((db2jul($periode['stop']) - db2jul($periode['start'])) / 86400);

      if (substr($this->vanafDatum, 5, 5) == '01-01')
      {
        $startjaar = true;
      }
      else
      {
        $startjaar = false;
      }
      $fondswaardenStart = berekenPortefeuilleWaarde($portefeuille, $periode['start'], $startjaar, $this->pdf->rapportageValuta, $periode['start']);
      $storingen = $this->getGewogenStortingenOnttrekkingen($portefeuille,$periode['start'], $periode['stop']);
      if($this->pdf->rapportageValuta<>'EUR'&& $this->pdf->rapportageValuta<>'')
      {
        $koers=getValutaKoers( $this->pdf->rapportageValuta, $periode['stop']);
      }
      else
      {
        $koers=1;
      }
      foreach ($fondswaardenStart as $waarden)
      {
        $waarden['actuelePortefeuilleWaardeEuro']=$waarden['actuelePortefeuilleWaardeEuro']/$koers;
        $portefeuileWaarde['start'] += $waarden['actuelePortefeuilleWaardeEuro'];
        $this->verdelingFondsen[$portefeuille][$periode['start']][$waarden['fonds']]['start'] += $waarden['actuelePortefeuilleWaardeEuro'];
      }
      /*
            foreach($fondswaardenStop as $waarden)
            {
              $portefeuileWaarde['stop']+=$waarden['actuelePortefeuilleWaardeEuro'];
              $this->verdelingFondsen[$portefeuille][$periode['stop']][$waarden['fonds']]['stop']=$waarden['actuelePortefeuilleWaardeEuro'];
            }
      
            $portefeuileWaarde['gemiddelde2']=($portefeuileWaarde['start']+$portefeuileWaarde['stop'])/2;
      */
      $portefeuileWaarde['gemiddelde'] = $portefeuileWaarde['start'] + $storingen['gewogen'];
      //		echo $periode['start']."->".$periode['stop']." | ".$portefeuileWaarde['gemiddelde']."=(".$portefeuileWaarde['start']."+".$storingen['gewogen'].") aandeel:(".($dagenPeriode/$dagenTotaal).")<br>\n";
      $portefeuileWaarde['aandeel'] = $dagenPeriode / $this->dagenTotaal;
      $this->verdelingTotaal[$portefeuille]['perioden'][$periode['stop']] = $portefeuileWaarde;
      $this->verdelingTotaal[$portefeuille]['totaal']['gemiddelde'] += $portefeuileWaarde['aandeel'] * $portefeuileWaarde['gemiddelde'];
    }
//echo $this->verdelingTotaal[$portefeuille]['totaal']['gemiddelde'];exit;


    $query = "SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
if(Fondsen.OptieBovenliggendFonds <> '',Fondsen.OptieBovenliggendFonds,Rekeningmutaties.Fonds) as fondsVolgorde,
Fondsen.OptieBovenliggendFonds,
BeleggingssectorPerFonds.Regio,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta,
Fondsen.ISINcode,
Fondsen.VKM
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds AND BeleggingssectorPerFonds.Vermogensbeheerder = '" . $this->portefeuilledata['Vermogensbeheerder'] . "'
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '" . $this->portefeuilledata['Vermogensbeheerder'] . "'
LEFT Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '" . $this->portefeuilledata['Vermogensbeheerder'] . "'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
LEFT JOIN KeuzePerVermogensbeheerder as BeleggingscategorienVolgorde ON BeleggingscategoriePerFonds.Beleggingscategorie = BeleggingscategorienVolgorde.waarde AND BeleggingscategorienVolgorde.Vermogensbeheerder = '" . $this->portefeuilledata['Vermogensbeheerder'] . "' AND BeleggingscategorienVolgorde.categorie='Beleggingscategorien'
LEFT JOIN KeuzePerVermogensbeheerder as HoofdcategorienVolgorde ON HoofdBeleggingscategorien.Beleggingscategorie = HoofdcategorienVolgorde.waarde AND HoofdcategorienVolgorde.Vermogensbeheerder = '" . $this->portefeuilledata['Vermogensbeheerder'] . "' AND HoofdcategorienVolgorde.categorie='Beleggingscategorien'
WHERE
Rekeningen.Portefeuille='" . $portefeuille . "'  AND
Rekeningmutaties.Boekdatum >= '" . $this->queryVanaf . "' AND  Rekeningmutaties.Boekdatum <= '" . $this->rapportageDatum . "'
AND Rekeningmutaties.Fonds <> '' AND Fondsen.VKM=1
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdcategorienVolgorde.Afdrukvolgorde, HoofdBeleggingscategorien.Afdrukvolgorde,BeleggingscategorienVolgorde.Afdrukvolgorde, BeleggingscategorienVolgorde.Afdrukvolgorde, Beleggingscategorien.Afdrukvolgorde,fondsVolgorde,OptieBovenliggendFonds,FondsOmschrijving ";

    $heeftOptie = array();
    $DB->SQL($query);
    $DB->Query();
    while ($data = $DB->NextRecord())
    {
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving'] = $data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][] = $data['Fonds'];
      $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['omschrijving'] = $data['categorieOmschrijving'];//[$data['Regio']]
      $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsen'][] = $data['Fonds'];//[$data['Regio']]
      $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsOmschrijving'][] = $data['FondsOmschrijving'];//[$data['Regio']]
      $perCategorie[$data['Hoofdcategorie']][$data['Beleggingscategorie']]['fondsValuta'][] = $data['Valuta'];//[$data['Regio']]
      $alleData['fondsen'][] = $data['Fonds'];
      $fondsGegevens[$data['Fonds']] = $data;

      if ($data['OptieBovenliggendFonds'] <> '' && !in_array($data['OptieBovenliggendFonds'], $heeftOptie))
      {
        $heeftOptie[] = $data['OptieBovenliggendFonds'];
      }
    }

    $this->totalen['gemiddeldeWaarde'][$portefeuille] = 0;
    $totaalBijdrageVKM = 0;
    $totaalDoorlopendekosten = 0;
    $totaalDoorlopendekostenGesplitst = array();
    $perfTotaal = $this->fondsPerformance($portefeuille, $alleData, true);

    $this->totalen['gemiddeldeWaarde'][$portefeuille] = $perfTotaal['gemWaarde'];
    //echo $portefeuille.' | ';ob_flush();listarray($perfTotaal);

    foreach ($perHoofdcategorie as $hoofdCategorie => $hoofdcategorieData)
    {
      $perHoofdcategorie[$hoofdCategorie]['perf'] = $this->fondsPerformance($portefeuille,$hoofdcategorieData);
    }


    foreach ($perCategorie as $hoofdCategorie => $regioData)
    {
      foreach ($regioData as $categorie => $categorieData)
      {
        $perCategorie[$hoofdCategorie][$categorie]['perf'] = $this->fondsPerformance($portefeuille,$categorieData);
      }
    } //[$regio]



    if ($this->pdfVullen == true)
    {
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $oldWidths = $this->pdf->widths;
      $this->pdf->widths[0] += 35;
      $this->pdf->widths[1] -= 35;
    }
    $totaalSom=array();
    foreach ($perHoofdcategorie as $hoofdcategorie => $hoofdcategorieData)
    {
      $data = $hoofdcategorieData['perf'];
      if ($this->pdfVullen == true)
      {
        if ($data['bijdrage'] < 0)
        {
          $this->pdf->CellFontColor = array('', '', '', '', '', '', '', '', '', '', '', '', $this->pdf->rapport_font_rood);
        }
        else
        {
          $this->pdf->CellFontColor = array('', '', '', '', '', '', '', '', '', '', '', '', $this->pdf->rapport_font_groen);
        }
      }
      $totaalSom['beginwaarde'] += $data['beginwaarde'];
      $totaalSom['eindwaarde'] += $data['eindwaarde'];
      $totaalSom['stort'] += $data['stort'];
      $totaalSom['gerealiseerd'] += $data['gerealiseerd'];
      $totaalSom['ongerealiseerd'] += $data['ongerealiseerd'];
      $totaalSom['kosten'] += $data['kosten'];
      $totaalSom['resultaat'] += $data['resultaat'];
      $totaalSom['gemWaarde'] += $data['gemWaarde'];
      $totaalSom['weging'] += $data['weging'];
      $totaalSom['bijdrage'] += $data['bijdrage'];

    }
    $perfTotaal = $totaalSom;

    $percentageIndirectVermogenMetKostenfactor = 0;
    if ($this->pdfVullen == true)
    {
      $this->pdf->widths = $oldWidths;
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array('T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T', 'T');
      unset($this->pdf->CellBorders);
    }
    foreach ($perCategorie as $hoofdcategorie => $categorieData)
    {
      if ($this->pdfVullen == true)
      {
        $this->printKop($perHoofdcategorie[$hoofdcategorie]['omschrijving'], 'BI', true);
      }

      foreach ($categorieData as $categorie => $fondsData)
      {

        if ($this->pdfVullen == true)
        {
          if ($categorie != $lastCategorie)
          {
            $this->printKop($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], '');
          }
          $lastCategorie = $categorie;

          $widthsBackup = $this->pdf->widths;
          $alignsBackup = $this->pdf->aligns;
          $newIndex = 0;
          $newWidths = array();

          foreach ($this->pdf->widths as $index => $waarde)
          {
            if ($index < 2)
            {
              $newIndex += $waarde;
            }
            else
            {
              $newIndex = $waarde;
            }
            if ($index == 0)
            {
              $newWidths[] = 0;
            }
            else
            {
              $newWidths[] = $newIndex;
            }
          }

          $this->pdf->widths = $newWidths;
          $this->pdf->widthsBackup = $newWidths;
        }
        $somVelden = array('beginwaarde', 'eindwaarde', 'stort', 'resultaat', 'gemWaarde', 'weging', 'bijdrage');
        foreach ($fondsData['fondsen'] as $id => $fonds)
        {

          $lastLn = false;
          $tmp = array();
          $tmp['fondsen'] = array($fonds);
          $tmp['categorie'] = $categorie;
          $data = $this->fondsPerformance($portefeuille,$tmp);

          if ($fondsGegevens[$fonds]['Fonds'] != $fondsGegevens[$fonds]['fondsVolgorde'] && $fondsGegevens[$fonds]['OptieBovenliggendFonds'] == $laatste)
          {
            foreach ($somVelden as $veld)
            {
              $sub[$veld] += $data[$veld];
            }
            $sub['aantal']++;
          }

          if ($fondsGegevens[$fonds]['OptieBovenliggendFonds'] == '')
          {
            $laatste = $fonds;
          }

          if ($fondsGegevens[$fonds]['Fonds'] == $fondsGegevens[$fonds]['fondsVolgorde'] || (isset($lastfondsVolgorde) && $fondsGegevens[$fonds]['fondsVolgorde'] <> $lastfondsVolgorde))
          { //echo " $laatsteFonds ".$sub['aantal']."<br>\n";ob_flush();
            if ($sub['aantal'] > 1)
            {
              $bijdrageVKM = $sub['weging'] * 100 * $kostenPercentage['percentage'];
              $perHoofdcategorie[$hoofdcategorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
              $perCategorie[$hoofdcategorie][$categorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
              if ($this->pdfVullen == true)
              {
                $this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS');
                $this->pdf->row(array('', '        ' . vertaalTekst("subtotaal",$this->pdf->rapport_taal) . ' ' . $laatsteFonds,
                  $this->formatGetal($sub['beginwaarde'], 0),
                  $this->formatGetal($sub['eindwaarde'], 0),
                  $this->formatGetal($sub['stort'], 0),
                  $this->formatGetal($sub['resultaat'], 0),
                  $this->formatGetal($sub['gemWaarde'], 0),
                  $this->formatGetal($sub['kosten'], 0),
                  $this->formatGetal($kostenPercentage['percentage'], 2),
                  $this->formatGetal($sub['gemWaarde'] * $kostenPercentage['percentage'] / 100, 0),
                  $this->formatGetal($sub['weging'] * 100, 2, true),
                  $this->formatGetal($bijdrageVKM, 2, true)));


                unset($this->pdf->CellBorders);
                $this->pdf->Ln();
                $lastLn = true;
              }
            }
            $sub = array('aantal' => 1);
            foreach ($somVelden as $veld)
            {
              $sub[$veld] += $data[$veld];
            }

            $laatsteFonds = substr($fondsData['fondsOmschrijving'][$id], 0, 30);

          }
          $lastfondsVolgorde = $fondsGegevens[$fonds]['fondsVolgorde'];


          if ($data['beginwaarde'] < 0 || $data['eindwaarde'] < 0)
          {
            $spiegeling = -1;
          }
          else
          {
            $spiegeling = 1;
          }
          $this->pdf->widths = $newWidths;
          $this->pdf->aligns = $alignsBackup;
          if (in_array($fonds, $heeftOptie) && $lastLn == false)
          {
            $this->pdf->Ln();
          }

          $query = "SELECT fondskosten.percentage as TotCostFund, fondskosten.transCostFund as FundTransCost, fondskosten.perfFeeFund as FundPerfFee FROM fondskosten
                       JOIN Fondsen ON fondskosten.fonds=Fondsen.Fonds
                       WHERE fondskosten.fonds='$fonds' AND Fondsen.VKM=1 AND datum <= '" . $this->rapportageDatum . "'
                       ORDER BY datum desc";
          $DB->SQL($query);
          $DB->Query();
          $kostenPercentage = $DB->NextRecord();
          $totaalKostenPercentage = ($kostenPercentage['TotCostFund'] + $kostenPercentage['FundTransCost'] + $kostenPercentage['FundPerfFee']);
          $bijdrageVKM = $sub['weging'] * $totaalKostenPercentage;
          $dlkostenAbsoluut = $sub['gemWaarde'] * $totaalKostenPercentage / 100;
          if ($DB->records() > 0)
          {//$kostenPercentage['percentage']<>0
            $percentageIndirectVermogenMetKostenfactor += $sub['weging'];
            $TotCostFundTxt = $this->formatGetal($kostenPercentage['TotCostFund']+$kostenPercentage['FundTransCost']+$kostenPercentage['FundPerfFee'], 2, false, true);
            $FundTransCostTxt = '';// $this->formatGetal($kostenPercentage['FundTransCost'], 2, false, true);
            $FundPerfFeeTxt = '';//$this->formatGetal($kostenPercentage['FundPerfFee'], 2, false, true);
            $dlkostenAbsoluutTxt = $this->formatGetal($dlkostenAbsoluut, 0, false, true);

          }
          else
          {
            $TotCostFundTxt = '';
            $FundTransCostTxt = '';
            $FundPerfFeeTxt = '';
            $dlkostenAbsoluutTxt = '';
          }

          if ($this->pdfVullen == true)
          {
            $omschrijvingWidth = $this->pdf->GetStringWidth('    ' . $fondsData['fondsOmschrijving'][$id]);
            $cellWidth = $this->pdf->widths[1] - 2;
            if ($omschrijvingWidth > $cellWidth)
            {
              $dotWidth = $this->pdf->GetStringWidth('...');
              $chars = strlen('    ' . $fondsData['fondsOmschrijving'][$id]);
              $newOmschrijving = '    ' . $fondsData['fondsOmschrijving'][$id];
              for ($i = 3; $i < $chars; $i++)
              {
                $omschrijvingWidth = $this->pdf->GetStringWidth(substr($newOmschrijving, 0, $chars - $i));
                if ($cellWidth > ($omschrijvingWidth + $dotWidth))
                {
                  $omschrijving = substr($newOmschrijving, 0, $chars - $i) . '...';
                  break;
                }
              }
            }
            else
            {
              $omschrijving = '    ' . $fondsData['fondsOmschrijving'][$id];
            }
            //   echo $this->pdf->widths[0]." ".$this->pdf->widths[1]." ".$omschrijving."<br>\n";
            $this->pdf->row(array('', $omschrijving,
              $this->formatGetal($data['beginwaarde'], 0),
              $this->formatGetal($data['eindwaarde'], 0),
              $this->formatGetal($data['stort'], 0),
              $this->formatGetal($data['resultaat'], 0),
              $this->formatGetal($data['gemWaarde'], 0),
              $TotCostFundTxt,
              $FundTransCostTxt,
              $FundPerfFeeTxt,
              $dlkostenAbsoluutTxt,
              $this->formatGetal($sub['weging'] * 100, 2, true),
              $this->formatGetal($bijdrageVKM, 2, true)
            ));
            $this->pdf->excelData[] = array($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $fondsData['fondsOmschrijving'][$id],
              round($data['beginwaarde'], 0),
              round($data['eindwaarde'], 0),
              round($data['stort'], 0),
              round($data['resultaat'], 0),
              round($data['gemWaarde'], 0),
              round($kostenPercentage['TotCostFund'], 2),
              round($kostenPercentage['FundTransCost'], 2),
              round($kostenPercentage['FundPerfFee'], 2),
              round($dlkostenAbsoluut, 0),
              round($sub['weging'] * 100, 2),
              round($bijdrageVKM, 2),
              $fondsGegevens[$fondsData['fondsen'][$id]]['ISINcode'],
              $fondsGegevens[$fondsData['fondsen'][$id]]['Valuta']);
          }
          $totaalBijdrageVKM += $bijdrageVKM;
          $totaalDoorlopendekosten += $sub['gemWaarde'] * $totaalKostenPercentage / 100;
          $totaalDoorlopendekostenGesplitst['TotCostFund'] += $sub['gemWaarde'] * $kostenPercentage['TotCostFund'] / 100;
          $totaalDoorlopendekostenGesplitst['FundTransCost'] += $sub['gemWaarde'] * $kostenPercentage['FundTransCost'] / 100;
          $totaalDoorlopendekostenGesplitst['FundPerfFee'] += $sub['gemWaarde'] * $kostenPercentage['FundPerfFee'] / 100;


          $perHoofdcategorie[$hoofdcategorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
          $perHoofdcategorie[$hoofdcategorie]['perf']['transkosten'] += $data['kosten'];
          $perHoofdcategorie[$hoofdcategorie]['perf']['dlkostenAbsoluut'] += $dlkostenAbsoluut;
          $perCategorie[$hoofdcategorie][$categorie]['perf']['bijdrageVKM'] += $bijdrageVKM;
          //$perCategorie[$hoofdcategorie][$categorie]['perf']['transkosten'] +=$data['kosten'];
          $perCategorie[$hoofdcategorie][$categorie]['perf']['dlkostenAbsoluut'] += $dlkostenAbsoluut;

          $totaalKosten += $data['kosten'];
          $totaaldlKosten += $dlkostenAbsoluut;
          // listarray($data);

          if ($this->pdfVullen == true)
          {
            if (count($fondsData['fondsen']) - 1 == $id)
            {
              if ($sub['aantal'] > 1)
              {
                $this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS');
                $this->pdf->row(array('', '        ' . vertaalTekst("subtotaal",$this->pdf->rapport_taal) . ' ' . $laatsteFonds,
                  $this->formatGetal($sub['beginwaarde'], $this->pdf->rapport_VOLK_decimaal),
                  $this->formatGetal($sub['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
                  $this->formatGetal($sub['stort'], 0),
                  $this->formatGetal($sub['resultaat'], 0),
                  $this->formatGetal($sub['gemWaarde'], 0),
                  $this->formatGetal($sub['transkosten']+$sub['dlkostenPercentage']+$sub['dlkostenAbsoluut'], 0),
                  '','',
                  $this->formatGetal($sub['weging'] * 100, 2, true)
                ));
                unset($this->pdf->CellBorders);
                $this->pdf->Ln();
              }
              $sub = array('aantal' => 1);
              foreach ($somVelden as $veld)
              {
                $sub[$veld] += $data[$veld];
              }

              $laatsteFonds = substr($fondsData['fondsOmschrijving'][$id], 0, 30);

            }

          }

        }
        $rekeningData = array();
        $totaalRekeningen = 0;
        foreach ($fondsData['rekeningen'] as $id => $rekening)
        {
          $tmp = array();
          $tmp['rekeningen'] = array($rekening);
          $data = $this->fondsPerformance($portefeuille,$tmp);
          $rekeningData[$id] = array('perf' => $data, 'rekening' => $rekening);
          $rekeningWaarde[$id] = $data['eindwaarde'];
          $totaalRekeningen += $data['eindwaarde'];
        }
        arsort($rekeningWaarde);


        if ($this->pdfVullen == true)
        {
          if ($lastRegio <> '')
          {
            $subregio = $perRegio[$hoofdcategorie][$categorie][$lastRegio]['perf'];
            $this->pdf->CellBorders = array('', '', '', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS', 'TS');
            $this->pdf->SetFont($this->pdf->rapport_font, 'I', $this->pdf->rapport_fontsize);
            $this->pdf->row(array('', '  ' . vertaalTekst("subtotaal",$this->pdf->rapport_taal) . ' ' . $perRegio[$hoofdcategorie][$categorie][$lastRegio]['omschrijving'],
              $this->formatGetal($subregio['beginwaarde'], $this->pdf->rapport_VOLK_decimaal),
              $this->formatGetal($subregio['eindwaarde'], $this->pdf->rapport_VOLK_decimaal),
              $this->formatGetal($subregio['stort'], 0),
              $this->formatGetal($subregio['resultaat'], 0),
              $this->formatGetal($subregio['gemWaarde'], 0),
              $this->formatGetal($subregio['resultaat'] / $subregio['gemWaarde'] * 100, 2),
              $this->formatGetal($subregio['weging'] * 100, 2, true),
              $this->formatGetal($subregio['bijdrage'] * 100, 2, true)));
            $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
            $this->pdf->Ln();
            unset($this->pdf->CellBorders);
            $lastRegio = '';
          }

          $this->pdf->widths = $widthsBackup;
          $this->printSubTotaal($perCategorie[$hoofdcategorie][$categorie]['omschrijving'], $perCategorie[$hoofdcategorie][$categorie]);
        }
      }
      if ($this->pdfVullen == true)
      {
        $this->printSubTotaal($perHoofdcategorie[$hoofdcategorie]['omschrijving'], $perHoofdcategorie[$hoofdcategorie], 'BI');
      }
      $lastHoofdcategorie = $hoofdcategorie;
    }

    $perfTotaal['bijdrageVKM'] = $totaalBijdrageVKM;
    $perfTotaal['transkosten'] = $totaalKosten;
    $perfTotaal['dlkostenAbsoluut'] = $totaaldlKosten;
    $perfTotaal['percentageIndirectVermogenMetKostenfactor'] = $percentageIndirectVermogenMetKostenfactor;

    $this->pdf->excelData[] = array('Totaal', '',
      round($perfTotaal['beginwaarde'], 0),
      round($perfTotaal['eindwaarde'], 0),
      round($perfTotaal['stort'], 0),
      round($perfTotaal['resultaat'], 0),
      round($perfTotaal['gemWaarde'], 0),
      round($perfTotaal['kosten'], 0),
      round($perfTotaal['percentage'], 2),
      round($perfTotaal['dlkostenAbsoluut'], 0),
      round($perfTotaal['weging'] * 100, 2),
      round($perfTotaal['bijdrageVKM'], 2));

    if ($this->pdfVullen == true)
    {
      $this->printSubTotaal('Totaal', array('perf' => $perfTotaal), 'BI');
      $y = $this->pdf->getY() + 10 + 18 * $this->pdf->rowHeight;
      if ($y > $this->pdf->PageBreakTrigger)
      {
        $this->pdf->vmkHeaderOnderdrukken = true;
        if ($this->skipSummary == false)
        {
          $this->pdf->addPage();
        }
        //$y=$this->pdf->getY()+10;
      }
    }

    return array('totaalDoorlopendekosten'=>$totaalDoorlopendekosten, 'perfTotaal'=>$perfTotaal, 'totaalDoorlopendekostenGesplitst'=> $totaalDoorlopendekostenGesplitst);
  }

  function writeRapport()
  {
    global $__appvar, $USR;

    $query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank,Portefeuilles.spreadKosten, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '" . $this->portefeuille . "' AND Portefeuilles.Client = Clienten.Client ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $this->portefeuilledata = $DB->nextRecord();


    $beheerder = $this->portefeuilledata['Vermogensbeheerder'];
    $q = "SELECT grafiek_kleur ,grafiek_sortering,spreadKosten FROM Vermogensbeheerders WHERE Vermogensbeheerder = '" . $beheerder . "'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $this->spreadKostenPunten = $kleuren['spreadKosten'];
    if ($this->portefeuilledata['spreadKosten'] <> 0)
    {
      $this->spreadKostenPunten = $this->portefeuilledata['spreadKosten'];
    }

    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    $gewensteKleuren = $allekleuren['Grootboek'];
    $mogelijkeKleuren = array();

    $kleurGebruikt = array();
    foreach ($allekleuren as $type => $typeKleuren)
    {
      foreach ($typeKleuren as $kleurcategorie => $kleurdata)
      {
        $kleur = array($kleurdata['R']['value'], $kleurdata['G']['value'], $kleurdata['B']['value']);

        if ($kleur[0] <> 0 || $kleur[1] <> 0 || $kleur[2] <> 0)
        {
          $kleurString = $kleur[0] . $kleur[1] . $kleur[2];
          if (!in_array($kleurString, $kleurGebruikt))
          {
            $kleurGebruikt[] = $kleurString;
            $mogelijkeKleuren[] = $kleur;
          }
        }
      }
    }

    if ($this->skipDetail == true)
    {
      $this->pdfVullen = false;
    }

    if ($this->pdfVullen == true)
    {
      $dataWidth = array(28,50,25,25,23,23,25,25,1,1,18,18,15);
      $this->pdf->SetWidths($dataWidth);

      $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'], $this->pdf->rapport_default_fontcolor['g'], $this->pdf->rapport_default_fontcolor['b']);
      $this->pdf->AddPage();
      $this->pdf->templateVars['VKMPaginas'] = $this->pdf->page;
      $this->pdf->templateVarsOmschrijving['VKMPaginas']=$this->pdf->rapport_titel;
      $this->pdf->SetDrawColor($this->pdf->rapport_lijn_rood['r'], $this->pdf->rapport_lijn_rood['g'], $this->pdf->rapport_lijn_rood['b']);
      $this->pdf->SetLineWidth(0.1);
    }


    $indexberekening = new indexHerberekening();
    $julvanaf = db2jul($this->rapportageDatumVanaf);
    $jultot = db2jul($this->rapportageDatum);
    $this->dagenTotaal = round(($jultot - $julvanaf) / 86400);
    $this->perioden = $indexberekening->getMaanden($julvanaf, $jultot);

    $details=$this->berekenPortefeuille($this->portefeuille);

    if ($this->skipDetail == true)
    {
      //$this->pdfVullen = true;
      $this->pdf->vmkHeaderOnderdrukken = true;
      $this->pdf->templateVars['VKMSPaginas'] = $this->pdf->page;
      $this->pdf->templateVarsOmschrijving['VKMSPaginas']=$this->pdf->rapport_titel;
      $this->pdf->addPage();
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    }

    $DB = new DB();
    $query = "SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Kosten=1";
    $DB->SQL($query);
    $DB->Query();
    $n = 0;
    $grootboekKleuren = array();
    while ($data = $DB->nextRecord())
    {
      $mogelijkeKleuren[$n];
      if ($gewensteKleuren[$data['Grootboekrekening']]['R']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['G']['value'] || $gewensteKleuren[$data['Grootboekrekening']]['B']['value'])
      {
        $grootboekKleuren[$data['Omschrijving']] = array($gewensteKleuren[$data['Grootboekrekening']]['R']['value'], $gewensteKleuren[$data['Grootboekrekening']]['G']['value'], $gewensteKleuren[$data['Grootboekrekening']]['B']['value']);
      }
      else
      {
        $grootboekKleuren[$data['Omschrijving']] = $mogelijkeKleuren[$n];
      }
      $n++;
    }
//
    $key = 'Indirecte (fonds)kosten';
    if ($gewensteKleuren[$key]['R']['value'] || $gewensteKleuren[$key]['G']['value'] || $gewensteKleuren[$key]['B']['value'])
    {
      $grootboekKleuren['Lopende kosten'] = array($gewensteKleuren[$key]['R']['value'], $gewensteKleuren[$key]['G']['value'], $gewensteKleuren[$key]['B']['value']);
    }
    else
    {
      $grootboekKleuren['Lopende kosten'] = $mogelijkeKleuren[$n];
    }

    $this->grootboekKleuren = $grootboekKleuren;


    if ($this->skipSummary == false)
    {
      $pagina=1;
      $lastPagina=1;
      $vkmData=array();
      $vkmTotaal=$this->kostenKader($this->portefeuille, $details['totaalDoorlopendekosten'], $details['perfTotaal'], $details['totaalDoorlopendekostenGesplitst']);
      $vkmData[$pagina][$this->portefeuille]=$vkmTotaal;//$this->kostenKader($this->portefeuille, $details['totaalDoorlopendekosten'], $details['perfTotaal'], $details['totaalDoorlopendekostenGesplitst']);
      $n=1;
      if(is_array($this->pdf->portefeuilles ) && count($this->pdf->portefeuilles)> 1)
      {
        foreach ($this->pdf->portefeuilles as $portefeuille)
        {
          if(!isset($vkmData[$pagina][$this->portefeuille]))
            $vkmData[$pagina][$this->portefeuille]=$vkmTotaal;

          $details=$this->berekenPortefeuille($portefeuille);
          $vkmData[$pagina][$portefeuille]=$this->kostenKader($portefeuille, $details['totaalDoorlopendekosten'], $details['perfTotaal'], $details['totaalDoorlopendekostenGesplitst']);
          $n++;
          if($n>5)
          {
            $n=1;
            $pagina++;
          }
        }
      }
      $this->pdfVullen = true;
      foreach($vkmData as $pagina=>$data)
      {
        if($pagina<>$lastPagina)
        {
          $this->pdf->addPage();
          $lastPagina=$pagina;
        }
        $this->toonVKMWaarden($data);
      }

      if ($this->pdfVullen == true)
      {
        $this->pdf->setAligns(array('L', 'R', 'R'));
        $this->pdf->setWidths(array(110, 30, 30));
        if ($this->melding <> '')
        {
          $this->pdf->ln();
          $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'], $this->pdf->rapport_default_fontcolor['g'], $this->pdf->rapport_default_fontcolor['b']);
          $this->pdf->row(array($this->melding));
          $this->pdf->excelData[] = array();
          $this->pdf->excelData[] = array('', $this->melding);
        }
        unset($this->pdf->CellFontColor);

        if ($this->skipLangeTermijn == false)
        {
          $this->langeTermijngrafiek();
        }
      }
    }

    if (isset($this->pdf->vmkHeaderOnderdrukken))
    {
      unset($this->pdf->vmkHeaderOnderdrukken);
    }
  }

  function toonVKMWaarden($vkmPerPortefeuille)
  {
    /*
    if(substr($this->vanafDatum,5,5)==substr($this->rapportageDatum,5,5))
    {
      $kostenTxt=vertaalTekst('Directe kosten afgelopen 12 maanden',$this->pdf->rapport_taal);
    }
    else
    {
      $kostenTxt=vertaalTekst('Directe kosten vanaf',$this->pdf->rapport_taal).' ' . date('d-m-Y', db2jul($this->vanafDatum));
    }
    */

    $kostenTxt=vertaalTekst('DIRECTE KOSTEN',$this->pdf->rapport_taal);
    $kop=array('');
    $indirecteKosten=array(vertaalTekst('Lopende kosten',$this->pdf->rapport_taal));
    $indirecteKostenVermogen=array(vertaalTekst('Lopende kosten ten opzichte van onderliggend vermogen',$this->pdf->rapport_taal));
    $percentageGemIndirect=array(vertaalTekst("Percentage van het gemiddeld indirect vermogen met een kostenfactor",$this->pdf->rapport_taal));
    $herrekendIndirect=array(vertaalTekst("Herrekende Lopende kosten",$this->pdf->rapport_taal));
    $aandeelIndirect=array(vertaalTekst('Aandeel indirecte beleggingen',$this->pdf->rapport_taal));
    $gemiddeldVermogen=array(vertaalTekst('Gemiddeld vermogen',$this->pdf->rapport_taal));
    $indirecteKostenfactor=array(vertaalTekst('Lopende kosten op de totale portefeuille',$this->pdf->rapport_taal));
    $grootboekKosten=array();
    $totaalDirect=array(vertaalTekst('Totaal directe kosten',$this->pdf->rapport_taal));
    $vkm=array(vertaalTekst('TOTALE KOSTEN PORTEFEUILLE',$this->pdf->rapport_taal));

    $grootboekKop=array($kostenTxt);
    $kostenIndirectKop=array(vertaalTekst('INDIRECTE KOSTEN',$this->pdf->rapport_taal));
    $fiscaliteit=array(vertaalTekst('Fiscaliteit',$this->pdf->rapport_taal));

    //listarray($vkmPerPortefeuille);
    $this->pdf->ln(-4);
    $alleGrootboeken=array();
    $maxVkm=0;
    $colWidth=37;
    $col1=60;

    if(count($this->pdf->portefeuilles)<5)
    {
      $colWidth = 222 / count($vkmPerPortefeuille);
      if ($colWidth > 60)
      {
        $colWidth = 60;
      }
      elseif($colWidth<37)
      {
        $colWidth = 37;
      }
    }


    foreach($vkmPerPortefeuille as $portefeuille=>$vkmWaarden)
    {
      $grootboekKop[]=vertaalTekst('nominaal',$this->pdf->rapport_taal);//$this->pdf->rapportageValuta;
      $grootboekKop[]='%';//vertaalTekst('Percentage',$this->pdf->rapport_taal);

      foreach($vkmWaarden['grootboekKosten'] as $grootboek=>$bedrag)
      {
        $alleGrootboeken[$grootboek]=0;
        if(!isset($grootboekKosten[$grootboek]))
          $grootboekKosten[$grootboek]=array(vertaalTekst($vkmWaarden['grootboekOmschrijving'][$grootboek],$this->pdf->rapport_taal));
      }
      $maxVkm=max($maxVkm,$vkmWaarden['vkmWaarde']);
    }
    foreach($vkmPerPortefeuille as $portefeuille=>$vkmWaarden)
    {
      //  $gemiddelde=$this->verdelingTotaal[$portefeuille]['totaal']['gemiddelde'];

      $herrekendeKosten=$vkmWaarden['doorlopendeKostenPercentage']/$vkmWaarden['percentageIndirectVermogenMetKostenfactor'];
      $vkmPercentagePortefeuille=$herrekendeKosten*$vkmWaarden['fondsGemiddeldeWaarde']/$vkmWaarden['gemiddeldeWaarde']*100;


      if($this->portefeuille==$portefeuille)
      {
        $kop[]= vertaalTekst('Totaal',$this->pdf->rapport_taal) . " \n ";
      }
      else
      {
        if($vkmWaarden['naam']<>'')
          $kop[]=$vkmWaarden['naam'];
        else
          $kop[]=$portefeuille;
      }
      $kostenIndirectKop[]=vertaalTekst('Nominaal',$this->pdf->rapport_taal);
      $kostenIndirectKop[]='%';

      // echo "$portefeuille ".($vkmWaarden['fondsGemiddeldeWaarde']/$vkmWaarden['gemiddeldeWaarde'] * 100)."=".$vkmWaarden['fondsGemiddeldeWaarde']."/".$vkmWaarden['gemiddeldeWaarde']."  * 100<br>\n";
      $indirecteKosten[]= $this->formatGetal($vkmWaarden['totaalDoorlopendekosten'], 0,false,true);
      $indirecteKosten[]= $this->formatGetal($vkmWaarden['doorlopendeKostenPercentage'] * 100, 2,true,true);
      //$indirecteKostenVermogen[]= $this->formatGetal($vkmWaarden['doorlopendeKostenPercentage'] * 100, 2) . ' %';
      $percentageGemIndirect[]= $this->formatGetal($vkmWaarden['percentageIndirectVermogenMetKostenfactor'] * 100, 2,true,true);
      $herrekendIndirect[]= $this->formatGetal($vkmWaarden['doorlopendeKostenPercentage']/$vkmWaarden['percentageIndirectVermogenMetKostenfactor'] * 100, 2,true,true);
      $aandeelIndirect[]=$this->formatGetal($vkmWaarden['fondsGemiddeldeWaarde']/$vkmWaarden['gemiddeldeWaarde'] * 100, 2,true,true);
      $gemiddeldVermogen[]=$this->formatGetal($vkmWaarden['gemiddeldeWaarde'], 0,false,true) . ' '.$this->pdf->rapportageValuta;
      $indirecteKostenfactor[]=$this->formatGetal($herrekendeKosten*$vkmWaarden['fondsGemiddeldeWaarde'], 0,false,true);
      $indirecteKostenfactor[]=$this->formatGetal($herrekendeKosten*$vkmWaarden['fondsGemiddeldeWaarde']/$vkmWaarden['gemiddeldeWaarde'] * 100, 2,true,true);
      //if($this->portefeuille==$portefeuille)
      $totaalDirect[]=$this->formatGetal($vkmWaarden['totaalDirecteKosten'],0);
      //else
      //  $totaalDirect[]='';
      $totaalDirect[]=$this->formatGetal($vkmWaarden['totaalDirecteKosten']/$vkmWaarden['gemiddeldeWaarde']*100,2,true,true);


      foreach($alleGrootboeken as $grootboek=>$null)
      {
        if($grootboek=='KOBU')
          $grootboek='KOST';

        //   $vkmWaarden['grootboekKosten'] as $grootboek=>$bedrag
        $bedrag= $vkmWaarden['grootboekKosten'][$grootboek];
        if($this->portefeuille==$portefeuille)
        {
          $grootboekKosten[$grootboek][] = $this->formatGetal($bedrag, 0,false,true);
          $grootboekKosten[$grootboek][] = $this->formatGetal($bedrag / $vkmWaarden['gemiddeldeWaarde'] * 100, 2,true,true);
        }
        else
        {
          //  $grootboekKosten[$grootboek][]='';
          $grootboekKosten[$grootboek][] = $this->formatGetal($bedrag, 0,false,true);
          $grootboekKosten[$grootboek][] = $this->formatGetal($bedrag / $vkmWaarden['gemiddeldeWaarde'] * 100, 2,true,true);
        }
      }

      //if($this->portefeuille==$portefeuille)
      $vkm[]=$this->formatGetal($vkmWaarden['vkmWaarde']*$vkmWaarden['gemiddeldeWaarde']/100, 0,false,true);
      //else
      //  $vkm[]='';
      $vkm[]=$this->formatGetal($vkmWaarden['vkmWaarde'], 2).' %';

      $bedrag=$vkmWaarden['alleGrootboekWaarden']['TOB']+$vkmWaarden['alleGrootboekWaarden']['DIVBE']+$vkmWaarden['alleGrootboekWaarden']['ROER']+$vkmWaarden['alleGrootboekWaarden']['BTLBR']+$vkmWaarden['alleGrootboekWaarden']['TOBO'];
      $fiscaliteit[]=$this->formatGetal($bedrag, 0,false,true);
      $fiscaliteit[]=$this->formatGetal(($bedrag)/$vkmWaarden['gemiddeldeWaarde']*100, 2,true,true);
    }


    if($this->pdfVullen==true)
    {

      $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'], $this->pdf->rapport_default_fontcolor['g'], $this->pdf->rapport_default_fontcolor['b']);
      $this->pdf->ln();
      $yBegin=$this->pdf->getY();
      $this->pdf->excelData[]=array();
      $this->pdf->excelData[]=array();
      $this->pdf->setAligns(array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R'));
      $this->pdf->setWidths(array($col1, $colWidth, $colWidth, $colWidth, $colWidth, $colWidth, $colWidth, $colWidth, $colWidth, $colWidth, $colWidth));
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      $ystart=$this->pdf->getY();
      $this->pdf->row($kop);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->row($indirecteKosten);
      //$this->pdf->row($indirecteKostenVermogen);
      //$this->pdf->ln();


      //$this->pdf->row($percentageGemIndirect);
      //$this->pdf->row($herrekendIndirect);
      //$this->pdf->row($aandeelIndirect);//array(, );
      //$this->pdf->ln();
      $this->pdf->row($gemiddeldVermogen);
      $this->pdf->rect($this->pdf->marge,$ystart,$col1+count($vkmPerPortefeuille)*$colWidth,$this->pdf->getY()-$ystart);

      $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->setWidths(array($col1,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2,$colWidth/2));
      $ystart=$this->pdf->getY();
      $this->pdf->row($kostenIndirectKop);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->row($indirecteKosten);
      $this->pdf->row($indirecteKostenfactor);
      $this->pdf->rect($this->pdf->marge,$ystart,$col1+count($vkmPerPortefeuille)*$colWidth,$this->pdf->getY()-$ystart);

      $this->pdf->ln();
      //$this->pdf->setWidths(array(60, 20, 20, 40));

      $ystart=$this->pdf->getY();
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      $this->pdf->row($grootboekKop);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      foreach($grootboekKosten as $grootboek=>$regel)
        $this->pdf->row($regel);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->row($totaalDirect);
      $this->pdf->rect($this->pdf->marge,$ystart,$col1+count($vkmPerPortefeuille)*$colWidth,$this->pdf->getY()-$ystart);

      $this->pdf->ln();
      $grootboekKop[0]='';
      $ystart=$this->pdf->getY();
      $this->pdf->row($grootboekKop);
      $this->pdf->row($vkm);
      $this->pdf->rect($this->pdf->marge,$ystart,$col1+count($vkmPerPortefeuille)*$colWidth,$this->pdf->getY()-$ystart);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $yEind=$this->pdf->getY();

      $lijn=$this->pdf->marge+$this->pdf->widths[0]+$this->pdf->widths[1]+$this->pdf->widths[2];
      $lijnen=array($lijn,$lijn+0.7);
      for($i=1;$i<count($vkmPerPortefeuille);$i++)
      {
        $lijn+=$colWidth;
        $lijnen[] =$lijn;
      }
      foreach ($lijnen as $x)
        $this->pdf->line($x,$yBegin,$x,$yEind);


      //  $this->pdf->setWidths(array(90, 36, 32, 32, 32, 32, 32, 32, 32, 32, 32));
      $this->pdf->ln();
      $this->pdf->row($fiscaliteit);

      $startYGrafiek=$this->pdf->getY();
      $n=0;

      foreach($vkmPerPortefeuille as $portefeuille=>$vkmWaarden)
      {
        arsort($this->barData[$portefeuille]);
        $this->pdf->setXY($col1+20+$n*$colWidth, 160);
        $this->VBarVerdeling(23, 40, $this->barData[$portefeuille],$portefeuille,$maxVkm);
        $this->pdf->setXY($this->pdf->marge, $startYGrafiek);
        $n++;
      }

      $kop1=vertaalTekst("lopende kosten:",$this->pdf->rapport_taal);
      $txt1='                                houdt enkel rekening met het deel van de portefeuille dat belegd is in beleggingsfondsen en trackers.
indien geen data voor het betreffende beleggingsfonds of de betreffende tracker beschikbaar is:
wordt voor dergelijke posities het gemiddelde genomen van de lopende kosten van de posities waar wel gegevens voor beschikbaar zijn.';
      $this->pdf->setXY($this->pdf->marge,165);
      $this->pdf->setWidths(array(280));
      $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize);
      $this->pdf->row(array(vertaalTekst("toelichting",$this->pdf->rapport_taal)));
      $this->pdf->SetFont($this->pdf->rapport_font, 'bu', $this->pdf->rapport_fontsize);
      $this->pdf->row(arraY($kop1));
      $this->pdf->ln($this->pdf->rowHeight*-1);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->row(arraY($txt1));

      $kop1=vertaalTekst("lopende kosten op de totale portefeuille:",$this->pdf->rapport_taal);
      $txt1='                                                                             ' . vertaalTekst("houdt rekening met het het gewicht van de beleggingsfondsen en trackers in de totale beleggingsportefeuille.",$this->pdf->rapport_taal) . '';
      //   $this->pdf->ln();
      $this->pdf->SetFont($this->pdf->rapport_font, 'bu', $this->pdf->rapport_fontsize);
      $this->pdf->row(arraY($kop1));
      $this->pdf->ln($this->pdf->rowHeight*-1);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->row(arraY($txt1));


    }
  }

  function langeTermijngrafiek($portefeuille)
  {
    global $__appvar;
    $db=new DB();
    $query="SELECT CRM_naw.doeldatum FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
    $db->SQL($query);
    $db->query();
    $data=$db->nextRecord();
    if($data['doeldatum'] > 1900)
      $doelJaar=$data['doeldatum'];
    else
      $doelJaar=$this->pdf->rapport_jaar+10;

    $query="SELECT Risicoklassen.verwachtRendement FROM Portefeuilles 
 JOIN Risicoklassen ON Portefeuilles.Risicoklasse=Risicoklassen.Risicoklasse AND Portefeuilles.Vermogensbeheerder = Risicoklassen.Vermogensbeheerder
 WHERE Portefeuilles.portefeuille='".$portefeuille."'";
    $db->SQL($query);
    $db->query();
    $data=$db->nextRecord();
    if($data['verwachtRendement'] <> 0 )
      $rendement=$data['verwachtRendement'];
    else
    {
      $jaren=(db2jul($this->rapportageDatum)-db2jul($this->pdf->PortefeuilleStartdatum))/(365.25*3600*24);
      $rendementProcentJaar = performanceMeting($portefeuille,$this->pdf->PortefeuilleStartdatum,$this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
      $rendement = $rendementProcentJaar/$jaren;
    }

    $query ="SELECT SUM(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro FROM TijdelijkeRapportage 
		WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $db->SQL($query);
    $db->Query();
    $start = $db->NextRecord();
    $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];


    $this->pdf->excelData[]=array();
    $this->pdf->excelData[]=array('Termijngrafiek');
    $this->pdf->excelData[]=array('doelJaar','rendement','beginwaarde','vkm');
    $this->pdf->excelData[]=array($doelJaar,round($rendement,2),round($beginwaarde,2),round($this->vkmWaarde['vkmWaarde'],4));
    $this->pdf->excelData[]=array('jaar','waardeNaKosten','cumulatieveKosten','waardeZonderKosten');

    $kosten=0;
    $grafiekWaarden=array();
    for($i=$this->pdf->rapport_jaar; $i<=$doelJaar; $i++)
    {
      $jaren=$i-$this->pdf->rapport_jaar;
      $nieuweWaarde=$beginwaarde*pow(1+($rendement/100),$jaren);
      $kosten+=$nieuweWaarde*($this->vkmWaarde['vkmWaarde']/100);

      $grafiekWaarden['waardeNaKosten'][]=$nieuweWaarde;
      $grafiekWaarden['cumulatieveKosten'][]=$kosten;
      $grafiekWaarden['waardeZonderKosten'][]=$nieuweWaarde+$kosten;
      $grafiekWaarden['datum'][]=$i;
      $this->pdf->excelData[]=array($i,round($nieuweWaarde,2),round($kosten,2),round($nieuweWaarde+$kosten,2));
    }
    $grafiekWaarden['legenda']=array('Waardeontwikkeling zonder kosten','Waardeontwikkeling na kosten','Cumulatieve kosten');

    $grafiekWaarden['titel']="Impact van kosten op het lange termijn rendement van de portefeuille";
    if(!isset($this->waardeZonderKostenKleur))
      $this->waardeZonderKostenKleur=array(100,100,200);
    if(!isset($this->waardeNaKostenKleur))
      $this->waardeNaKostenKleur=array(100,200,100);
    if(!isset($this->cumulatieveKostenKleur))
      $this->cumulatieveKostenKleur=array(200,100,100);

    if($this->pdf->getY()+70>$this->pdf->pagebreak)
      $this->pdf->addPage();

    $this->pdf->setXY(30,$this->pdf->getY()+10);
    $this->LineDiagram(120, 55, $grafiekWaarden,array($this->waardeZonderKostenKleur,$this->waardeNaKostenKleur,$this->cumulatieveKostenKleur),0,0,4,4,false);//50


//		listarray($grafiekWaarden);
//		echo "$doelJaar $rendement $beginwaarde";
    //	listarray( $this->vkmWaarde);
    //	exit;
  }

  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$vanafBegin=false)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['waardeNaKosten'];
    $data2 = $data['cumulatieveKosten'];
    $data = $data['waardeZonderKosten'];


    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    if(count($data2)>0)
      $bereikdata = array_merge($bereikdata,$data2);

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY()+2;
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    //	$this->pdf->setY($Ypage-3);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,vertaalTekst($titel,$this->pdf->rapport_taal),0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color2= $color[2];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);


    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
    }


    //	echo $maxVal;exit;

    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    $maxVal = ceil(($maxVal+1) * 1.1);

    //	$maxVal=round($maxVal,floor(log10($maxVal))*-1+1);

    $significance=floor(log10($maxVal));
    $significance=pow(10,$significance);
    $maxVal=	ceil($maxVal/$significance)*$significance;

    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);



    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;

    //$this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);

    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);

    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);

    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->setXY($XDiag-7, $i);
      $this->pdf->Cell(7 , 4 , "� ". 0-($n*$stapgrootte) , 0, 1, "R");

      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->setXY($XDiag-7, $i);
        $this->pdf->Cell(7 , 4 , "� " .(($n * $stapgrootte) + 0) , 0, 1, "R");

      }
      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;

      if ($i>0 || $vanafBegin==true)
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }

      $yval = $yval2;
    }

    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;

        if ($i>0 || $vanafBegin==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }

    if(is_array($data2))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);
      for ($i=0; $i<count($data2); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data2[$i]) * $waardeCorrectie) ;

        if ($i>0 || $vanafBegin==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        $yval = $yval2;
      }
    }

    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
    $aantal=count($legendaItems);
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      elseif($index==1)
        $kleur=$color1;
      else
        $kleur=$color2;
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$w+5 , $YPage+$step+10, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+$w+3+5,$YPage+$step+10);
      $this->pdf->Cell(0,3,vertaalTekst($item,$this->pdf->rapport_taal));

      $step+=6;
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }





  function fondsKostenOpbrengsten($portefeuille,$fonds,$datumBegin,$datumEind)
  {
    $DB=new DB();
    $query = "SELECT
      Sum((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaalWaarde
      FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
      JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
      WHERE
      (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
      Rekeningen.Portefeuille = '".$portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
      Rekeningmutaties.Boekdatum <= '$datumEind' AND
      Rekeningmutaties.Fonds = '$fonds'";
    $DB->SQL($query); //echo "$fonds $query  <br>\n";
    $DB->Query();
    $totaalWaarde = $DB->NextRecord();

    return $totaalWaarde['totaalWaarde'];
  }


  function fondsPerformance($portefeuille,$fondsData,$totaal=false)
  {
    $datumBegin=$this->vanafDatum;
    $weegDatum=$datumBegin;
    $datumEind=$this->rapportageDatum;

    global $__appvar;
    $DB=new DB();
    $totaalPerf = 100;

    if(!$fondsData['fondsen'])
      $fondsData['fondsen']=array('geen');
    if(!$fondsData['rekeningen'])
      $fondsData['rekeningen']=array('geen');

    if ($this->pdfVullen==true && $this->pdf->rapportageValuta <> 'EUR')
    {
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
      $startValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumBegin);
      $eindValutaKoers= getValutaKoers($this->pdf->rapportageValuta,$datumEind);
    }
    else
    {
      $koersQuery = "";
      $startValutaKoers= 1;
      $eindValutaKoers= 1;
    }



    $fondsenWhere = " Fondsen.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
    $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
    $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
    $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
    $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";




    $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
               SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as liqWaarde,
               SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
    $DB->Query();
    $start = $DB->NextRecord();
    $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];

    $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                       SUM(TijdelijkeRapportage.beginPortefeuilleWaardeEuro)/2/$eindValutaKoers  as beginPortefeuilleWaardeEuro,
                       Sum(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro)) as beginWaardeNew
                FROM TijdelijkeRapportage
                WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind'   AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
    $DB->SQL($query);
    $DB->Query();
    $eind = $DB->NextRecord();
    $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
    $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];


    $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM((if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
       SUM((if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) ,0))) as opbrengstTotaal ,
       SUM((if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery ),0))) as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Transactietype<>'B' AND 
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
    $DB->SQL($queryFondsDirecteKostenOpbrengsten);
    $DB->Query();
    $FondsDirecteKostenOpbrengsten = $DB->NextRecord();


    $queryAttributieStortingenOntrekkingen = "SELECT ".
      "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
      "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
      "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1)$koersQuery)  AS storting,
	               SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )$koersQuery)  AS onttrekking ".
      "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND  Rekeningmutaties.Transactietype<>'B' AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
      " $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
    $DB->SQL($queryAttributieStortingenOntrekkingen); //echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
    $DB->Query();
    $AttributieStortingenOntrekkingen = $DB->NextRecord();

    //   $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];


    $queryKostenOpbrengsten = "SELECT
          SUM((if(Grootboekrekeningen.Kosten       =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as kostenTotaal,
          SUM((if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery),0))) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
           Rekeningen.Portefeuille = '".$portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Transactietype<>'B' AND 
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
    $DB->SQL($queryKostenOpbrengsten);
    $DB->Query();
    $nietToegerekendeKosten = $DB->NextRecord();
    $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];



    // $indexData=$this->indexPerformance($fondsData['categorie'],$weegDatum,$datumEind);
    $gemiddelde=0;
    foreach($this->perioden as $periode)
    {
      $aandeelPeriode=$this->verdelingTotaal[$portefeuille]['perioden'][$periode['stop']]['aandeel'];

      $stortingen=$this->getGewogenStortingenOnttrekkingenFondsen($portefeuille,$periode['start'],$periode['stop'],$rekeningFondsenWhere,$koersQuery);
      $startwaarde=0;
      foreach($fondsData['fondsen'] as $fonds)
      {
        $startwaarde += $this->verdelingFondsen[$portefeuille][$periode['start']][$fonds]['start'];
      }
      $gemiddeldeMaand=$startwaarde+$stortingen['gewogen'];

      //if($fondsData['fondsen'][0]=='Ishares Iboxx HY CB')
      //  echo $fondsData['fondsen'][0]." ".$periode['stop']." $aandeelPeriode*($startwaarde+".$stortingen['gewogen'].")=".($aandeelPeriode*$gemiddeldeMaand)."<br>\n";

      $gemiddelde+=$aandeelPeriode*$gemiddeldeMaand;
      //echo "	$gemiddelde+=$aandeelPeriode*$gemiddeldeMaand;<br>\n";
    }
//echo "$portefeuille totaal $gemiddelde<br>\n";
    //if($fondsData['fondsen'][0]=='Ishares Iboxx HY CB')
    // echo "<br>\n$gemiddelde";
    if($totaal==false)
      $weging=$gemiddelde/$this->totalen['gemiddeldeWaarde'][$portefeuille];
    else
      $weging=$gemiddelde/$this->verdelingTotaal[$portefeuille]['totaal']['gemiddelde'];
    $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
    $bijdrage=$resultaat/$gemiddelde*$weging;



    return array(
      'beginwaarde'=>$beginwaarde,
      'eindwaarde'=>$eindwaarde,

      'stort'=>$AttributieStortingenOntrekkingen['totaal'],
      'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
      'storting'=>$AttributieStortingenOntrekkingen['storting'],
      'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
      'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
      'resultaat'=>$resultaat,
      'gemWaarde'=>$gemiddelde,

      'weging'=>$weging,
      'bijdrage'=>$bijdrage);
  }



  function getMaanden($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);

    $i=0;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    $counterStart=0;
    while ($counterStart < $stop)
    {
      $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
      else
      {
        $datum[$i]['start'] =date('Y-m-d',$counterStart);
        if(substr($datum[$i]['start'],5,5)=='12-31')
          $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
      }

      $datum[$i]['stop']=date('Y-m-d',$counterEnd);

      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
      $i++;
    }
    return $datum;
  }

  function fondsPerf($fonds,$van,$tot)
  {
    $DB=new DB();
    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
      $verdeling[$data['fonds']]=$data['percentage'];

    if(count($verdeling)==0)
      $verdeling[$fonds]=100;

    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $janKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      $perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perf=$perfJaar-$perfVoorPeriode;

      if($this->pdf->debug==true)
      {
        echo "koers $fonds ".substr($tot,0,4)."-01-01 ".$janKoers['Koers']."<br>\n";
        echo "koers $fonds $van ".$startKoers['Koers']."<br>\n";
        echo "koers $fonds $tot ".$eindKoers['Koers']."<br>\n";
        echo "perf voor begin $perfVoorPeriode = (".$startKoers['Koers']." - ".$janKoers['Koers'].") / (".$janKoers['Koers'].")<br>\n";
        echo "Perf tot einddatum $perfJaar =(".$eindKoers['Koers']." - ".$janKoers['Koers'].") / ".($janKoers['Koers'])."<br>\n";
        echo "m<b> $fonds $van,$tot  $perf </b>= ( $perfJaar - $perfVoorPeriode ) <br>\n";
      }
      $totalPerf+=($perf*$percentage/100);
    }
    //echo "t $fonds $totalPerf $van,$tot<br>\n";

    return $totalPerf;
  }

  function VBarVerdeling($w, $h, $data,$portefeuille,$maxValue=0)
  {
    global $__appvar;
    $grafiekPunt = array();

    $minVal=0;


    $n=0;
    $grafiek=array();
    $colors=array();

    $aantal=count($data);
    $kleurStap=floor((255-75)/$aantal);
    foreach ($data as $categorie=>$waarde)
    {
      $grafiek[$categorie]=$waarde;
      $categorien[$categorie] = $n;
      $categorieId[$n]=$categorie ;


      if(!isset($colors[$categorie]))
      {
        if(is_array($this->grootboekKleuren[$categorie]))
          $colors[$categorie] = $this->grootboekKleuren[$categorie];
        else
        {
          $random = 75 + $kleurStap * $n;
          $colors[$categorie] = array($random, $random, $random);//,rand(0,255),rand(0,255)
        }
      }
      $n++;
    }

    $numBars=1;
    if($color == null)
    {
      $color=array(155,155,155);
    }

    $maxVal=array_sum($data);
    if($maxVal<$maxValue)
      $maxVal=$maxValue;

    $maxVal=ceil($maxVal*2)/2;
    if($maxVal <= 0)
      $maxVal=0;

    if($minVal >= 0)
      $minVal = 0;

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();

    $YstartGrafiek = $YPage;
    $hGrafiek = $h;
    $XstartGrafiek = $XPage;
    $bGrafiek = $w; // - legenda

    $unit = $hGrafiek / $maxVal * -1;
    $nulYpos =0;


    $horDiv = 5;
    $bereik = $hGrafiek/$unit;
    //$this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor(0,0,0);
    $stapgrootte = round(abs($bereik)/$horDiv*10)/10;
    $top = $YstartGrafiek-$h;
    $absUnit =abs($unit);

    $nulpunt = $YstartGrafiek + $nulYpos;


    $n=0;
    for($i=$nulpunt; $i >= $top-0.1; $i-= $absUnit*$stapgrootte)
    {
      //echo $n*$stapgrootte." => $i >= $top  ->$maxVal ".$absUnit*$stapgrootte."<br>\n";
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-10, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte,1,false,true)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }


    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

    $vBar = ($bGrafiek / 2);
    $eBaton = ($vBar / 2);


    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);


    foreach($grafiek as $categorie=>$val)
    {
      if(!isset($YstartGrafiekLast))
        $YstartGrafiekLast = $YstartGrafiek;
      //Bar
      $xval = $XstartGrafiek +  $vBar - $eBaton/2 ;
      $lval = $eBaton;
      $yval = $YstartGrafiekLast+ $nulYpos ;
      $hval = ($val * $unit);

      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
      $YstartGrafiekLast = $YstartGrafiekLast+$hval;
      $this->pdf->SetTextColor(255,255,255);
      if(abs($hval) > 3)
      {
        $this->pdf->SetXY($xval, $yval+($hval/2)-2);
        //$this->pdf->Cell($eBaton, 4, number_format($val,2,',','.')."%",0,0,'C');
      }
      $this->pdf->SetTextColor(0,0,0);

    }

    if($this->portefeuille==$portefeuille)
    {
      $xval = $XstartGrafiek - 60 + 5;
      $yval = $YstartGrafiek;
      foreach ($grafiek as $categorie => $val)
      {
        $yval -= 10;
        $this->pdf->Rect($xval, $yval, 2, 2, 'DF',null, $colors[$categorie]);
        $this->pdf->SetXY($xval + 4, $yval - 1);
        $this->pdf->Cell(50, 4, vertaalTekst($categorie,$this->pdf->rapport_taal) , 0, 0, 'L');//. " " . number_format($val, 2, ',', '.') . "%"

      }
    }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }

}