<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.11 $

$Log: RapportOIS_L37.php,v $
Revision 1.11  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.10  2017/05/26 16:45:07  rvv
*** empty log message ***

Revision 1.9  2012/09/05 18:19:11  rvv
*** empty log message ***

Revision 1.8  2012/07/08 07:03:49  rvv
*** empty log message ***

Revision 1.7  2012/05/30 16:02:38  rvv
*** empty log message ***

Revision 1.6  2012/05/27 12:38:05  rvv
*** empty log message ***

Revision 1.5  2012/05/27 08:33:10  rvv
*** empty log message ***

Revision 1.4  2012/05/12 15:11:00  rvv
*** empty log message ***

Revision 1.3  2012/05/09 18:47:45  rvv
*** empty log message ***

Revision 1.2  2012/05/06 12:01:49  rvv
*** empty log message ***

Revision 1.1  2012/05/02 15:53:13  rvv
*** empty log message ***

Revision 1.8  2012/04/25 15:20:45  rvv
*** empty log message ***

Revision 1.7  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.6  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.5  2012/03/21 19:08:58  rvv
*** empty log message ***

Revision 1.4  2012/03/17 11:58:16  rvv
*** empty log message ***

Revision 1.3  2012/03/14 17:30:11  rvv
*** empty log message ***

Revision 1.2  2012/03/11 17:19:57  rvv
*** empty log message ***

Revision 1.1  2012/02/29 16:52:49  rvv
*** empty log message ***

Revision 1.29  2011/06/25 16:51:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");

class RapportOIS_L37
{
	function RapportOIS_L37($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->underlinePercentage=0.8;
		$this->pdf->rapport_titel = vertaalTekst('Portefeuille overzicht',$this->pdf->rapport_taal);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec,$nulNietTonen=false)
	{
	  if($waarde==0 && $nulNietTonen==true)
	    return '';

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

	function printKop($title, $type="default")
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
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor[r],$this->pdf->rapport_kop3_fontcolor[g],$this->pdf->rapport_kop3_fontcolor[b]);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor[r],$this->pdf->rapport_default_fontcolor[g],$this->pdf->rapport_default_fontcolor[b]);
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		$this->pdf->widthA = array(1,90,20,20,25,30,30,30,30,20);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R');

		$this->pdf->widthB = array(1,90,20,20,20,30,30,30,20,20);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R');


	  $this->cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
    $this->cashfow->genereerTransacties();
		$this->cashfow->genereerRows();
 	  $cashflowJaar=array();
	  $cashflowTotaal=0;
    $maanden=array(0,'jan','feb','mrt','apr','mei','jun','jul','aug','sep','okt','nov','dec');
		for($i=1;$i<13;$i++)
		{
		  $cashflowHuidigjaar[$maanden[$i]]['lossing'] +=0;
		  $cashflowHuidigjaar[$maanden[$i]]['rente'] +=0;
		}

		$rapJaar=substr($this->rapportageDatum,0,4);
		foreach ($this->cashfow->regelsRaw as $regel)
		{
		  $jaar=substr($regel['0'],6,4);
 		  if($jaar > ($rapJaar+13))
	      $jaar='Overig';
		  $maand=$maanden[intval(substr($regel['0'],3,2))];
		  $cashflowJaar[$jaar]['lossing'] +=0;
		  $cashflowJaar[$jaar]['rente'] +=0;
		  if($jaar==$rapJaar)
		    $cashflowHuidigjaar[$maand][$regel[2]] +=$regel[3];
		  $cashflowJaar[$jaar][$regel[2]] +=$regel[3];
		  $cashflowTotaal +=$regel[3];
		}


    $this->pdf->rapport_titel = vertaalTekst("Zakelijke Waarden",$this->pdf->rapport_taal);
		$this->pdf->AddPage();
		$this->pdf->templateVars['OISPaginas']=$this->pdf->customPageNo;

		getTypeGrafiekData($this,'Regio'," AND TijdelijkeRapportage.hoofdcategorie='ZAK' AND TijdelijkeRapportage.beleggingscategorie='AAND'");
		getTypeGrafiekData($this,'Beleggingssector',"AND TijdelijkeRapportage.hoofdcategorie='ZAK'");
		getTypeGrafiekData($this,'Valuta',"AND TijdelijkeRapportage.hoofdcategorie='RISM'",array('EUR','USD'));


		$this->pdf->setY(55);




	  $this->pdf->OISsettings['ZAKheader']=1;
	  HeaderOIS_L37($this->pdf);
	  //unset($this->pdf->OISsettings['ZAKheader']);


		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];


				$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) /".$this->pdf->ValutaKoersEind."  AS totaal,
				TijdelijkeRapportage.hoofdcategorie,
        TijdelijkeRapportage.hoofdcategorieOmschrijving ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY TijdelijkeRapportage.hoofdcategorie";
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query); //echo $query;exit;
		$DB->Query();
		while($waarde = $DB->nextRecord())
		  $hcatWaarde[$waarde['hoofdcategorieOmschrijving']] = $waarde['totaal'];




			$subquery = "SELECT TijdelijkeRapportage.fondsOmschrijving,TijdelijkeRapportage.fonds,Fondsen.standaardSector, ".
			" TijdelijkeRapportage.actueleValuta, ".
			" TijdelijkeRapportage.totaalAantal, ".
			" TijdelijkeRapportage.historischeWaarde, ".
			" TijdelijkeRapportage.historischeValutakoers, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.fondsEenheid) AS historischeWaardeTotaal, ".
			" (TijdelijkeRapportage.totaalAantal * TijdelijkeRapportage.historischeWaarde * TijdelijkeRapportage.historischeValutakoers * TijdelijkeRapportage.fondsEenheid / TijdelijkeRapportage.historischeRapportageValutakoers) AS historischeWaardeTotaalValuta, ".
			" TijdelijkeRapportage.beginwaardeLopendeJaar, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.beginPortefeuilleWaardeEuro, TijdelijkeRapportage.actueleFonds, TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
			TijdelijkeRapportage.actuelePortefeuilleWaardeEuro /".$this->pdf->ValutaKoersEind." as actuelePortefeuilleWaardeEuro,
			TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille,
			TijdelijkeRapportage.hoofdcategorieOmschrijving,
			TijdelijkeRapportage.beleggingscategorieOmschrijving,
			TijdelijkeRapportage.beleggingscategorie,
			TijdelijkeRapportage.regio,
      TijdelijkeRapportage.hoofdcategorieVolgorde ,
      TijdelijkeRapportage.`type`,
      TijdelijkeRapportage.Rentedatum,
      Fondsen.Renteperiode,
      Rating.omschrijving as Rating,
      TijdelijkeRapportage.Lossingsdatum
      FROM TijdelijkeRapportage
      LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
      Left Join Rating ON (Fondsen.rating = Rating.rating )
			WHERE TijdelijkeRapportage.`type` <> 'rente' AND".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,Fondsen.standaardSector desc, TijdelijkeRapportage.beleggingscategorieVolgorde,TijdelijkeRapportage.Lossingsdatum,
                 TijdelijkeRapportage.beleggingssectorVolgorde,TijdelijkeRapportage.regioVolgorde,TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__);
			$DB2 = new DB();
			$DB2->SQL($subquery);
			$DB2->Query();

			$renteVanafJul = adodb_db2jul(jul2sql($this->pdf->rapport_datum));
			while($subdata = $DB2->NextRecord())
			{
				$rente=getRenteParameters($subdata['fonds'], $this->rapportageDatum);
				foreach($rente as $key=>$value)
					$subdata[$key]=$value;
			  if( isset($lastStandaardSector) && ($lastStandaardSector != $subdata['standaardSector'] || $lastBeleggingscategorie !=$subdata['beleggingscategorie'] || $lastRegio != $subdata['regio']) )
			  {
			    $this->pdf->ln(2);
			  }

			  $lastStandaardSector=$subdata['standaardSector'];
			  $lastBeleggingscategorie=$subdata['beleggingscategorie'];
			  $lastRegio=$subdata['regio'];


			  $DB->SQL("SELECT actuelePortefeuilleWaardeEuro,fonds FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.`type` =  'rente' AND TijdelijkeRapportage.fonds='".$subdata['fonds']."' AND TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".$__appvar['TijdelijkeRapportageMaakUniek']);
  			$rente=$DB->lookupRecord();
  			$subdata['actuelePortefeuilleWaardeEuro'] +=$rente['actuelePortefeuilleWaardeEuro'];
        if($subdata['Rentedatum'] <> '0000-00-00')
        {
    			$rentePeriodetxt = "  ".date("d-m",db2jul($subdata['Rentedatum']));
	  			if($subdata['renteperiode'] <> 12 && $subdata['Renteperiode'] <> 0)
		  			$rentePeriodetxt .= " / ".$subdata['Renteperiode'];
        }
        else
          $rentePeriodetxt='';

          $lossingsJul = adodb_db2jul($subdata['Lossingsdatum']);
          $duration='';
          $ytm='';
        if($lossingsJul > 0)
	      {
					$koers=getRentePercentage($subdata['fonds'],$this->rapportageDatum);
		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;
		  	  $p = $subdata['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->cashfow->fondsDataKeyed[$subdata['fonds']]['lossingskoers'];
	        $y = $jaar;

	        $ytm=  $this->cashfow->bondYTM($p,$r,$b,$y)*100;
	        $restLooptijd=($lossingsJul-$this->pdf->rapport_datum)/31556925.96;

	         //listarray($this->cashfow->waardePerFonds);
	         $duration=$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaardeJaar']/$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaarde'];
	         //echo $subdata['fonds']." $duration= ".$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaardeJaar']."/".$this->cashfow->waardePerFonds[$subdata['fonds']]['ActueelWaarde']."<br>\n";
	         $modifiedDuration=$duration/(1+$ytm/100);
	         $aandeel=$subdata['actuelePortefeuilleWaardeEuro']/$hcatWaarde[$subdata['hoofdcategorieOmschrijving']];//$totaalWaarde;

           $totalen[$subdata['beleggingscategorieOmschrijving']]['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['ytm']+=$ytm*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['duration']+=$duration*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalen[$subdata['beleggingscategorieOmschrijving']]['restLooptijd']+=$restLooptijd*$aandeel;

	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['yield']+=$koers['Rentepercentage']*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['ytm']+=$ytm*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['duration']+=$duration*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['modifiedDuration']+=$modifiedDuration*$aandeel;
	         $totalenH[$subdata['hoofdcategorieOmschrijving']]['restLooptijd']+=$restLooptijd*$aandeel;

        }

			  if($subdata['hoofdcategorieOmschrijving']=='')
			    $subdata['hoofdcategorieOmschrijving']='Geen categorie';
			  if($subdata['beleggingscategorieOmschrijving']=='')
			    $subdata['beleggingscategorieOmschrijving']='Geen categorie';

        if($subdata['beleggingscategorie'] != 'Liquiditeiten')
        {
				$fondsResultaat = ($subdata['actuelePortefeuilleWaardeInValuta'] - $subdata['historischeWaardeTotaal']) * $subdata['actueleValuta'] / $this->pdf->ValutaKoersEind;
				$fondsResultaatprocent = ($fondsResultaat / $subdata['historischeWaardeTotaal']) * 100;

				if($subdata['historischeWaardeTotaal'] < 0 && $fondsResultaat > 0)
				  $fondsResultaatprocent = -1 * $fondsResultaatprocent;

				$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
				$valutaResultaat = $subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta'] - $fondsResultaat;
				//$procentResultaat = (($totaalactueel - $totaalhistorisch) / ($totaalhistorisch /100));
				$procentResultaat = (($subdata['actuelePortefeuilleWaardeEuro'] - $subdata['historischeWaardeTotaalValuta']) / ($subdata['historischeWaardeTotaalValuta'] /100));
        $gecombeneerdResultaat = $fondsResultaat + $valutaResultaat;

				if($subdata['historischeWaardeTotaalValuta'] < 0)
					$procentResultaat = -1 * $procentResultaat;

				if($procentResultaat > 1000 || $procentResultaat < -1000)
					$procentResultaattxt = "p.m.";
				else
					$procentResultaattxt = $this->formatGetal($procentResultaat,$this->pdf->rapport_VHO_decimaal_proc);

				$fondsResultaattxt = "";
				$valutaResultaattxt = "";
				if($fondsResultaat <> 0)
					$fondsResultaattxt = $this->formatGetal($fondsResultaat,$this->pdf->rapport_VHO_decimaal);

				if($valutaResultaat <> 0)
					$valutaResultaattxt = $this->formatGetal($valutaResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);

				if($gecombeneerdResultaat <> 0)
				  $gecombeneerdResultaattxt = $this->formatGetal($gecombeneerdResultaat,$this->pdf->rapport_VHO_decimaal,$this->pdf->rapport_VHO_decimaal_proc);

				if($fondsResultaatprocent > 1000 || $fondsResultaatprocent < -1000)
					$fondsResultaatprocenttxt = "p.m.";
				else
					$fondsResultaatprocenttxt = $this->formatGetal($fondsResultaatprocent,$this->pdf->rapport_VHO_decimaal_proc);
        }
        else
        {
          $fondsResultaat=0;
          $valutaResultaat=0;
          $fondsResultaattxt = "";
			  	$valutaResultaattxt = "";
			  	$fondsResultaatprocenttxt='';
			  	$valutaResultaattxt='';
			  	$gecombeneerdResultaattxt='';
        }

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
				$percentageVanTotaal = ($subdata['actuelePortefeuilleWaardeEuro']) / ($hcatWaarde[$subdata['hoofdcategorieOmschrijving']]/100);
				$percentageTotaalTekst = $this->formatGetal($percentageVanTotaal,1)."%";
				$waardePerFonds[$subdata['fondsOmschrijving']]=$subdata['actuelePortefeuilleWaardeEuro'];



				if($lastCategorie <> $subdata['beleggingscategorieOmschrijving'])
				{
				  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				  if($lastCategorie <> '')
				  {
             if($this->pdf->Hcat == "Risicomijdende beleggingen")
             {
               $this->pdf->rapport_titel = vertaalTekst("Risicomijdende beleggingen",$this->pdf->rapport_taal);
               $this->printTotaal('CATVAR',$lastCategorie,$totalen[$lastCategorie]);
             }
             else
  			       $this->printTotaal('CATZAK',$lastCategorie,$totalen[$lastCategorie]);
  			     $this->pdf->ln();
					}
				}
				if($lastHCategorie <> $subdata['hoofdcategorieOmschrijving'])
				{

				  $this->pdf->Hcat=$subdata['hoofdcategorieOmschrijving'];
				  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				  if($lastHCategorie <> '')
				  {
				    $this->printTotaal('ZAK',$lastHCategorie,$totalenH[$lastHCategorie]);


		$y=$this->pdf->getY()+10;

    if($y > 150)
    {
      $this->pdf->addPage();
      $y=$this->pdf->getY()+10;
    }
    $this->pdf->ln();
	  $this->pdf->setWidths(array(115,52,115));
    $this->pdf->setAligns(array('C','C','C'));
   	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Verdeling over regio\'s',$this->pdf->rapport_taal),'',vertaalTekst('Verdeling over sectoren',$this->pdf->rapport_taal)));
		$this->pdf->setXY(23,$y);
	  PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Regio']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Regio']['grafiekKleur']);

    $this->pdf->setXY(190,$y);
	  PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Beleggingssector']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Beleggingssector']['grafiekKleur']);
    $this->pdf->ln(8);

			      $this->pdf->rapport_titel = vertaalTekst("Risicomijdende beleggingen",$this->pdf->rapport_taal);
			      $this->pdf->addPage();
			      $this->pdf->templateVars['OIS2Paginas']=$this->pdf->customPageNo;

				    if(!isset($this->pdf->OISsettings['VARheader']))
				    {

	  			  	$this->pdf->OISsettings['VARheader']=1;
              $this->pdf->ln(10);
	  			  	HeaderOIS_L37($this->pdf);
				    }

				  }
				}

				$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
				if($lastHCategorie <> $subdata['hoofdcategorieOmschrijving'])
				{
				   $this->pdf->row(array('',vertaalTekst($subdata['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal)));
				   $this->pdf->ln();
				}

				if($lastCategorie <> $subdata['beleggingscategorieOmschrijving'])
				   $this->pdf->row(array('',vertaalTekst($subdata['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal)));
				 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

				$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


						 if($subdata['beleggingscategorie']  == 'Liquiditeiten11')
				    {
				      $this->pdf->cat = 'Liquiditeiten';
				      HeaderOIS_L37($this->pdf);
				    }


        if($this->pdf->Hcat == "Risicomijdende beleggingen")
        {
                 $this->pdf->row(array('',$subdata['fondsOmschrijving'],
												$subdata['valuta'],
												$this->formatGetal($subdata['totaalAantal'],0,true),
												$this->formatGetal($subdata['actueleFonds'],2,true),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],0,true),
												$this->formatGetal($subdata['historischeWaarde'],2,true),
												$gecombeneerdResultaattxt,
												$percentageTotaalTekst,
												$this->formatGetal($ytm,1,true)));
          }
          elseif($this->pdf->Hcat == "Liquiditeiten")
          {

              $this->pdf->row(array('',$subdata['fondsOmschrijving'],$subdata['valuta'],'','',$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_VHO_decimaal)));

          }
          else
          {
               $this->pdf->row(array('',$subdata['fondsOmschrijving'],
               					$subdata['valuta'],
												$this->formatAantal($subdata['totaalAantal'],0,true),
												$this->formatGetal($subdata['actueleFonds'],2,true),
												$this->formatGetal($subdata['actuelePortefeuilleWaardeEuro'],0,true),
												$this->formatGetal($subdata['historischeWaarde'],2,true),
												$gecombeneerdResultaattxt,
												$percentageTotaalTekst)
												);
          }


					$totalenH[$subdata['hoofdcategorieOmschrijving']]['aanschafEUR']+=$subdata['historischeWaardeTotaalValuta'];
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['valutaresultaat']+=$valutaResultaat;
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['fondsResultaat']+=$fondsResultaat;
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['rente']+=$rente['actuelePortefeuilleWaardeEuro'];
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['waardeEUR']+=$subdata['actuelePortefeuilleWaardeEuro'];
					$totalenH[$subdata['hoofdcategorieOmschrijving']]['percentage']+=$percentageVanTotaal;

					$totalen[$subdata['beleggingscategorieOmschrijving']]['aanschafEUR']+=$subdata['historischeWaardeTotaalValuta'];
					$totalen[$subdata['beleggingscategorieOmschrijving']]['valutaresultaat']+=$valutaResultaat;
					$totalen[$subdata['beleggingscategorieOmschrijving']]['fondsResultaat']+=$fondsResultaat;
					$totalen[$subdata['beleggingscategorieOmschrijving']]['rente']+=$rente['actuelePortefeuilleWaardeEuro'];
					$totalen[$subdata['beleggingscategorieOmschrijving']]['waardeEUR']+=$subdata['actuelePortefeuilleWaardeEuro'];
					$totalen[$subdata['beleggingscategorieOmschrijving']]['percentage']+=$percentageVanTotaal;
					$lastHCategorie=$subdata['hoofdcategorieOmschrijving'];
					$lastCategorie=$subdata['beleggingscategorieOmschrijving'];
	  }

	  if($this->pdf->Hcat == "Risicomijdende beleggingen")
	      $this->printTotaal('CATVAR',$lastCategorie,$totalen[$lastCategorie]);
		elseif($this->pdf->Hcat == "Zakelijke waarden")
	     $this->printTotaal('CATZAK',$lastCategorie,$totalen[$lastCategorie]);
	  else
	     $this->printTotaal('CATLIQ',$lastCategorie,$totalen[$lastCategorie]);
    $this->pdf->ln();
    if($this->pdf->Hcat == "Risicomijdende beleggingen")
     $this->printTotaal('VAR',$lastHCategorie,$totalenH[$lastHCategorie]);
		elseif($this->pdf->Hcat == "Zakelijke waarden")
 	    $this->printTotaal('ZAK',$lastHCategorie,$totalenH[$lastHCategorie]);
 	  else
 	    $this->printTotaal('LIQ',$lastHCategorie,$totalenH[$lastHCategorie]);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


    $y=$this->pdf->getY()+10;
    if($y > 150)
    {
      $this->pdf->addPage();
      $y=$this->pdf->getY()+10;
    }
    $this->pdf->setXY(15,$y+45);
    VBarDiagram($this->pdf,100,40,$cashflowJaar,"");
    $this->pdf->setY($y);
    $this->pdf->setWidths(array(115,52,115));
    $this->pdf->setAligns(array('C','C','C'));
   	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Cashflow overzicht',$this->pdf->rapport_taal),'',vertaalTekst('Verdeling over valuta',$this->pdf->rapport_taal)));
    $this->pdf->setXY(190,$y+5);
	  PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Valuta']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur']);

	}


	function printTotaal($type,$categorie,$waarden)
	{
	  $categorie=vertaalTekst($categorie,$this->pdf->rapport_taal);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $totaal=vertaalTekst('Totaal',$this->pdf->rapport_taal);
	  if($type=='ZAK')
	  {
     $this->pdf->CellBorders = array('','','','','',array('TS','UU'),'',array('TS','UU'),array('TS','UU'));
     $this->pdf->row(array('',$totaal.' '.$categorie,'','','',$this->formatGetal($waarden['waardeEUR'],0,true),'',$this->formatGetal($waarden['fondsResultaat']+$waarden['valutaresultaat'],0,true),$this->formatGetal($waarden['percentage'],1,true).'%'));
	  }
    if($type=='CATZAK')
    {
	    $this->pdf->CellBorders = array('','','','','',array('TS'),'',array('TS'),array('TS'));
    	$this->pdf->row(array('',$totaal.' '.$categorie,'','','',$this->formatGetal($waarden['waardeEUR'],0,true),'',$this->formatGetal($waarden['valutaresultaat']+$waarden['fondsResultaat'],0,true),$this->formatGetal($waarden['percentage'],1,true).'%'));
    }

    if($type=='VAR')
    {
      $this->pdf->CellBorders = array('','','','','',array('TS','UU'),'',array('TS','UU'),array('TS','UU'));
		  $this->pdf->row(array('',$totaal.' '.$categorie,'','','',$this->formatGetal($waarden['waardeEUR'],0,true),'',$this->formatGetal($waarden['valutaresultaat']+$waarden['fondsResultaat'],0,true),$this->formatGetal($waarden['percentage'],1,true).'%'));
    }
    if($type=='CATVAR')
    {
  	  $this->pdf->CellBorders = array('','','','','',array('TS'),'',array('TS'),array('TS'),array('TS'));
		  $this->pdf->row(array('',$totaal.' '.$categorie,'','','',$this->formatGetal($waarden['waardeEUR'],0,true),'',$this->formatGetal($waarden['valutaresultaat']+$waarden['fondsResultaat'],0,true),$this->formatGetal($waarden['percentage'],1,true).'%',$this->formatGetal($waarden['ytm']/($waarden['percentage']/100),1,true)));
    }

    if($type=='LIQ')
    {
      $this->pdf->CellBorders = array('','','','','',array('TS','UU'));
		  $this->pdf->row(array('',$totaal.' '.$categorie,'','','',$this->formatGetal($waarden['waardeEUR'],0,true)));
    }
    if($type=='CATLIQ')
    {
  	  $this->pdf->CellBorders = array('','','','','',array('TS'));
		  $this->pdf->row(array('',$totaal.' '.$categorie,'','','',$this->formatGetal($waarden['waardeEUR'],0,true)));
    }

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
	}



}
?>