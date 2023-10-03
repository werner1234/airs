<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/20 16:19:15 $
File Versie					: $Revision: 1.7 $

$Log: RapportATT_L54.php,v $
Revision 1.7  2019/11/20 16:19:15  rvv
*** empty log message ***

Revision 1.6  2019/10/19 16:45:25  rvv
*** empty log message ***

Revision 1.5  2019/10/11 17:40:07  rvv
*** empty log message ***

Revision 1.4  2014/11/05 16:52:22  rvv
*** empty log message ***

Revision 1.3  2014/06/08 07:56:12  rvv
*** empty log message ***

Revision 1.2  2014/04/12 16:28:12  rvv
*** empty log message ***

Revision 1.1  2014/03/02 10:26:23  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***

Revision 1.8  2013/11/23 17:23:24  rvv
*** empty log message ***

Revision 1.7  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.6  2012/10/21 12:44:08  rvv
*** empty log message ***

Revision 1.5  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.4  2012/09/23 08:51:44  rvv
*** empty log message ***

Revision 1.3  2012/09/19 16:53:18  rvv
*** empty log message ***

Revision 1.2  2012/09/13 15:58:37  rvv
*** empty log message ***

Revision 1.5  2012/08/11 13:17:53  rvv
*** empty log message ***

Revision 1.4  2012/07/11 11:33:23  rvv
*** empty log message ***

Revision 1.3  2012/06/09 13:43:40  rvv
*** empty log message ***

Revision 1.2  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.1  2012/05/27 08:33:11  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

class RapportATT_L117
{
	function RapportATT_L117($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat portefeuille";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf =  substr($this->pdf->PortefeuilleStartdatum,0,10);// $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
	
	function kop($periode='Jaar',$titel)
  {
    $this->pdf->widthA = array(23,23,27,27,23,23,22,22,22,24,24,20);
    $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->ln(4);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    $this->pdf->cell(200,6,vertaalTekst($titel, $this->pdf->rapport_taal),0,1,'L');
    $this->pdf->SetFillColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor[0],$this->pdf->rapport_kop_fontcolor[1],$this->pdf->rapport_kop_fontcolor[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widthA), 8, 'F');
    $this->pdf->row(array(vertaalTekst($periode,$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Begin\nvermogen",$this->pdf->rapport_taal),
                      vertaalTekst("Stortingen en \nonttrekkingen",$this->pdf->rapport_taal),
                      vertaalTekst("Koersresultaat",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Valutaresultaat",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Inkomsten",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Opgelopen\nrente",$this->pdf->rapport_taal),
                      vertaalTekst("Kosten",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Belastingen",$this->pdf->rapport_taal),
                      vertaalTekst("Beleggings\nresultaat",$this->pdf->rapport_taal),
                      vertaalTekst("Waarde in \nEUR",$this->pdf->rapport_taal),
                      vertaalTekst("Beleg.res.",$this->pdf->rapport_taal)."\n".vertaalTekst("portefeuille",$this->pdf->rapport_taal),
                      ));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor[0],$this->pdf->rapport_fontcolor[1],$this->pdf->rapport_fontcolor[2]);

  }


	function writeRapport()
	{
	  global $__appvar;

    $RapStartJaar = date("Y", db2jul($this->rapportageDatum));



		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $poly=array($this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,25,
      $this->pdf->w-$this->pdf->marge,30,
      $this->pdf->w-$this->pdf->marge-5,35,
      $this->pdf->marge,35);
    
    $this->pdf->Polygon($poly,'F',null,$this->pdf->rapport_lichtgrijs);
    $this->pdf->setAligns(array('L'));
    $this->pdf->SetWidths(array(230));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->sety(26);

    if( $this->pdf->rapport_taal == 2 ) {
      $this->pdf->Row(array("The table below represents a breakdown of your investment results. This is done on a monthly basis. The secon dtabel shows the historical value development of your assets for ht current year and past years."));
    } elseif( $this->pdf->rapport_taal == 3 ) {
      $this->pdf->Row(array("Le tableau ci-dessous présente les performances de votre portefeuille mois par mois. Le deuxième tableau présente la performance depuis le début de l'année ainsi que celles des années précédentes."));
    } else {
      $this->pdf->Row(array("In de onderstaande tabel ziet u hoe uw beleggingsresultaat is samengesteld. Dit wordt op maandelijkse basis uitgedrukt als een percentage. De tweede tabel toont de historische waardeontwikkeling van uw vermogen over het lopende jaar en de afgelopen jaren."));
    }

  $db=new DB();
  $query="SELECT min(Datum) as datum FROM HistorischePortefeuilleIndex WHERE Portefeuille='".$this->portefeuille."' AND Categorie='Totaal' AND periode='m'";
  $db->SQL($query);
  $begin=$db->lookupRecord();
  if($db->records() > 0 && $begin['datum']<>'')
  {
    $begin=substr($begin['datum'],0,7).'-01';
  }
  else
  {
    $begin=$this->rapportageDatumVanaf;
  }
  
  
  $index=new indexHerberekening();
  $maanden = $index->getMaanden(db2jul($begin) ,db2jul($this->rapportageDatum));
  $maandWaarden=array();
  $jaarWaarden=array();
  $somVelden=array('waardeMutatie','stortingen','onttrekkingen','resultaatVerslagperiode','kosten','opbrengsten','ongerealiseerd','ongerealiseerdFondsValuta','ongerealiseerdFonds','ongerealiseerdValuta',
    'rente','belasting','gerealiseerdFonds','gerealiseerdValuta');

  foreach($maanden as $periode)
  {
    $maandData=$this->BerekenMutaties($periode['start'],$periode['stop'],$this->portefeuille);
   // $maandWaarden[]=$maandData;
  
     $jaar=date('Y',db2jul($periode['stop']));
     if($jaar >= $RapStartJaar)
     {
       $maandWaarden[]=$maandData;
     }

     foreach($somVelden as $veld)
     {
       $jaarWaarden[$jaar][$veld]+=$maandData[$veld];
     }
     if(!isset($jaarWaarden[$jaar]['waardeBegin']))
       $jaarWaarden[$jaar]['waardeBegin']=$maandData['waardeBegin'];
     $jaarWaarden[$jaar]['waardeHuidige']=$maandData['waardeHuidige'];
     $jaarWaarden[$jaar]['performance']=((1+$jaarWaarden[$jaar]['performance']/100)*(1+$maandData['performance']/100)-1)*100;
    /*
   */
  }
  /*
    $jaren = $index->getJaren(db2jul(substr($this->pdf->PortefeuilleStartdatum,0,10)) ,db2jul($this->rapportageDatum));
    foreach($jaren as $periode)
    {
      $jaarData = $this->BerekenMutaties($periode['start'], $periode['stop'], $this->portefeuille);
      $jaar = date('Y', db2jul($periode['stop']));
      $jaarWaarden[$jaar]=$jaarData;
    }
  */
//  listarray($rendamentWaarden);exit;
//  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
    
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->fillCell = array();
    if(count($maandWaarden) > 0)
    {
       $this->kop($RapStartJaar,'Beleggingsresultaat huidige periode');
       $this->pdf->rowHeight=6;
       $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
       $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
       $this->pdf->SetDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
       foreach ($maandWaarden as $row)
		   {
         $datum = db2jul($row['datum']);
		     $this->pdf->row(array(vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal).' '.date("Y",$datum) ,
		                           $this->formatGetal($row['waardeBegin'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
                            //round($row['ongerealiseerdFonds'],0)."\n".round($row['gerealiseerdFonds'],0),
                               $this->formatGetal($row['ongerealiseerdFonds']+$row['gerealiseerdFonds'],2),
                            //round($row['ongerealiseerdValuta'],0)."\n".round($row['gerealiseerdValuta'],0),
                               $this->formatGetal($row['ongerealiseerdValuta']+$row['gerealiseerdValuta'],2),
		                           $this->formatGetal($row['opbrengsten'],2),
		                           $this->formatGetal($row['rente'],2),
                               $this->formatGetal($row['kosten'],2),
                               $this->formatGetal($row['belasting'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['waardeHuidige'],2),
		                           $this->formatGetal($row['performance'],2)."%"));
                               
                               
		   }
		   $this->pdf->fillCell=array();
       $this->pdf->CellBorders = array();
    }
    
    $this->pdf->rowHeight=$rowHeightBackup;
    
    
    $this->pdf->fillCell = array();
    if(count($maandWaarden) > 0)
    {
      $this->kop('Jaar','Historische beleggingsresultaat');
      $this->pdf->rowHeight=6;
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
      $this->pdf->SetDrawColor($this->pdf->rapport_donkergroen[0],$this->pdf->rapport_donkergroen[1],$this->pdf->rapport_donkergroen[2]);
      foreach ($jaarWaarden as $jaar=>$row)
      {
        if($jaar<2022)
        {
          $this->pdf->row(array($jaar,'','','','','','','','','',
                            $this->formatGetal($row['waardeHuidige'], 2),
                            $this->formatGetal($row['performance'], 2) . "%"));
        }
        else
        {
          $this->pdf->row(array($jaar,
                            $this->formatGetal($row['waardeBegin'], 2),
                            $this->formatGetal($row['stortingen'] - $row['onttrekkingen'], 2),
                            //round($row['ongerealiseerdFonds'],0)."\n".round($row['gerealiseerdFonds'],0),
                            $this->formatGetal($row['ongerealiseerdFonds'] + $row['gerealiseerdFonds'], 2),
                            //round($row['ongerealiseerdValuta'],0)."\n".round($row['gerealiseerdValuta'],0),
                            $this->formatGetal($row['ongerealiseerdValuta'] + $row['gerealiseerdValuta'], 2),
                            $this->formatGetal($row['opbrengsten'], 2),
                            $this->formatGetal($row['rente'], 2),
                            $this->formatGetal($row['kosten'], 2),
                            $this->formatGetal($row['belasting'], 2),
                            $this->formatGetal($row['resultaatVerslagperiode'], 2),
                            $this->formatGetal($row['waardeHuidige'], 2),
                            $this->formatGetal($row['performance'], 2) . "%"));
        }
      }
      $this->pdf->fillCell=array();
      $this->pdf->CellBorders = array();
    }
    $this->pdf->rowHeight=$rowHeightBackup;

	}


function formatGetalLength ($getal,$decimaal,$gewensteLengte)
{
 $lengte = strlen(round($getal));
 if($getal < 0)
  $lengte --;
 $mogelijkeDecimalen = $gewensteLengte - $lengte;
 if($lengte >$gewensteLengte)
   $decimaal = 0;
 elseif ($decimaal > $mogelijkeDecimalen)
   $decimaal = $mogelijkeDecimalen;
 return number_format($getal,$decimaal,',','');
}



	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();
		
		
		if(substr($beginDatum,5,5)=='12-31')
    {
      $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';
    }
    
    $query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='m' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($eindDatum,0,10)."' ";
    

    if($db->QRecords($query) > 0)
    {
      $data=$db->nextRecord();
  
      $data['periode']= $beginDatum."->".$eindDatum;
      $data['datum']= $eindDatum;
      $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
      $data['waardeBegin']=round($data['PortefeuilleBeginWaarde'],2);
      $data['waardeHuidige']=round($data['PortefeuilleWaarde'],2);
      $data['waardeMutatie']=round($data['PortefeuilleWaarde']-$data['PortefeuilleBeginWaarde'],2);
      $data['stortingen']=round($data['Stortingen'],2);
      $data['onttrekkingen']=round($data['Onttrekkingen'],2);
      $data['resultaatVerslagperiode'] = round( $data['waardeMutatie'] - $data['Stortingen'] + $data['Onttrekkingen'],2);
      $data['kosten'] = round($data['Kosten'],2);
      $data['opbrengsten'] = round($data['Opbrengsten'],2);
      $data['performance'] =$data['indexWaarde'];
      $data['ongerealiseerdFondsValuta']='';
      $data['ongerealiseerdFonds'] = $data['ongerealiseerd'];
      $data['ongerealiseerdValuta'] ='';
      $data['belasting'] = '';
      $data['gerealiseerdFonds'] = $data['gerealiseerd'];
      $data['gerealiseerdValuta'] ='';
      return $data;
      
    }
      
    if(db2jul($beginDatum) < db2jul($this->pdf->PortefeuilleStartdatum))
      $wegingsDatum=$this->pdf->PortefeuilleStartdatum;
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true,$attributieCategorie = 'Totaal',$gesplitst=true,$debug=false);

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
  
        $ongerealiseerd=($regel['actuelePortefeuilleWaardeEuro']-$regel['beginPortefeuilleWaardeEuro']);
        $ongerealiseerdFonds=($regel['actuelePortefeuilleWaardeInValuta'] - $regel['beginPortefeuilleWaardeInValuta']) * $regel['actueleValuta'];
        $totaalWaarde['ongerealiseerd'] += $ongerealiseerd;
        $totaalWaarde['ongerealiseerdFonds'] +=$ongerealiseerdFonds;
        $totaalWaarde['ongerealiseerdValuta'] += $ongerealiseerd - $ongerealiseerdFonds;
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }

	  $ongerealiseerd=$totaalWaarde['ongerealiseerd'];
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query); 
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging[totaal2]) / $gemiddelde) * 100;


	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();
    
    $query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS belasting
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Grootboekrekening IN('ROER','DIVBE')
              GROUP BY Grootboekrekeningen.Kosten "; //
    $db->SQL($query);
    $belasting = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat['totaal']+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;
    
    $data=array();
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['datum']= $eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten']-$belasting['belasting'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['ongerealiseerdFondsValuta']=$totaalWaarde['ongerealiseerd'];
    $data['ongerealiseerdFonds'] = $totaalWaarde['ongerealiseerdFonds'];
    $data['ongerealiseerdValuta'] =$totaalWaarde['ongerealiseerdValuta'];
    $data['rente'] = $opgelopenRente;
    $data['belasting'] = $belasting['belasting'];
    $data['gerealiseerdFonds'] =$koersResultaat['fonds'];
    $data['gerealiseerdValuta'] =$koersResultaat['valuta'];
    return $data;

	}

}
?>