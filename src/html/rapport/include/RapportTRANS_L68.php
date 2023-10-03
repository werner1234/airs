<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/04 16:41:40 $
File Versie					: $Revision: 1.15 $

$Log: RapportTRANS_L68.php,v $
Revision 1.15  2020/05/04 16:41:40  rvv
*** empty log message ***

Revision 1.13  2019/07/17 15:44:30  rvv
*** empty log message ***

Revision 1.12  2019/07/17 15:34:55  rvv
*** empty log message ***

Revision 1.11  2019/06/21 14:52:45  rm
7880

Revision 1.10  2019/06/19 15:59:09  rvv
*** empty log message ***

Revision 1.9  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.8  2018/06/20 16:40:16  rvv
*** empty log message ***

Revision 1.7  2017/10/08 14:09:23  rvv
*** empty log message ***

Revision 1.6  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.5  2016/06/19 15:22:08  rvv
*** empty log message ***

Revision 1.4  2016/06/12 10:27:20  rvv
*** empty log message ***

Revision 1.3  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.2  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.1  2016/05/04 16:08:25  rvv
*** empty log message ***

Revision 1.4  2015/12/19 08:29:17  rvv
*** empty log message ***

Revision 1.3  2013/06/15 15:55:18  rvv
*** empty log message ***

Revision 1.2  2013/06/12 18:46:36  rvv
*** empty log message ***

Revision 1.1  2013/05/26 13:54:49  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportTransactieoverzichtLayout.php");

class RapportTRANS_L68
{
	function RapportTRANS_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "TRANS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Transactie-overzicht";

		if ($this->pdf->rapportageValuta != 'EUR' && $this->pdf->rapportageValuta != '')
		  $this->pdf->rapport_titel .= " in ".$this->pdf->rapportageValuta;

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;


	  $this->pdf->excelData[]=array("Datum",'Aan/verkoop','Rekening','Aantal','Fonds','Aankoopkoers in valuta','Aankoopwaarde in valuta','Aankoopwaarde in EUR',
	  'Verkoopkoers in valuta','Verkoopwaarde in valuta','Verkoopwaarde in valuta','Historsiche kostprijs in EUR');

	}

	function formatGetal($waarde, $dec)
	{
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
	       //  echo $this->portefeuille." $waarde <br>";exit;
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

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function printTotaal($title, $totaalA, $totaalB, $procent)
	{
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$actueel = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2];

		$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		if(!empty($totaalA))
		{
			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthA[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetal($totaalA,2);
		}

		if(!empty($totaalB))
		{
			$totaalBtxt = $this->formatGetal($totaalB,2);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthA[3],4,$title, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[5],4,$totaalBtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthA[6],4,$totaalprtxt, 0,0, "R");

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();

		return $totaalA;
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


		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$this->pdf->MultiCell(90,4, $title, 0, "L");
		$this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
	}


	function writeRapport()
	{

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";


		// voor data
		$this->pdf->widthB = array(20,20,20,30,70,30,30,30,30);
		$this->pdf->alignB = array('L','L','L','R','L','R','R','R','R','R','R','R','R','R','R');
		// voor kopjes
		$this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;


		if($this->pdf->rapport_MUT_kwartaal == 1 && ($this->pdf->selectData['backoffice'] == true) )
		{
			$maand = date("n",db2jul($this->rapportageDatum));
			$kwartaal = floor(($maand / 4)+1);
			switch($kwartaal)
			{
				case 1 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-01-01";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 2 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-03-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 3 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-06-31";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
				case 4 :
					$this->rapportageDatumVanaf = date("Y",db2jul($this->rapportageDatumVanaf))."-09-30";
					$this->pdf->rapport_datumvanaf = db2jul($this->rapportageDatumVanaf);
				break;
			}
		}

		$this->pdf->AddPage();
 		$this->pdf->templateVars['TRANSPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['TRANSPaginas']=$this->pdf->rapport_titel;
		$this->pdf->setWidths($this->pdf->widthB);
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);


		if($this->pdf->lastPOST['doorkijk']==1)
		{
			$this->verdiept = new portefeuilleVerdiept($this->pdf, $this->portefeuille, $this->rapportageDatum);
			//	$verdiepteFondsen = $this->verdiept->getFondsen();
			//listarray();exit;
			$fondsPerPortefeuille = array();
			foreach ($this->verdiept->FondsPortefeuilleData as $fonds => $portefeuille)
			{
				$fondsPerPortefeuille[$portefeuille] = $fonds;
			}
			$portefeuilles = array_values($this->verdiept->FondsPortefeuilleData);
			$portefeuilles[] = $this->portefeuille;
			$portefeuilleFilter=" Rekeningen.Portefeuille IN ('" . implode("','",$portefeuilles)."') ";
		}
		else
		{
			$portefeuilleFilter="Rekeningen.Portefeuille = '".$this->portefeuille."' ";
		}

		// loopje over Grootboekrekeningen Opbrengsten = 1
		$query = "SELECT Rekeningen.Portefeuille, Fondsen.Omschrijving, ".
		"Fondsen.Fondseenheid, ".
		"Rekeningmutaties.Boekdatum, ".
		"Rekeningmutaties.id,
		Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		 Rekeningmutaties.Afschriftnummer,
		 Rekeningmutaties.Rekening,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
		"Rekeningmutaties.Fondskoers, ".
		"Rekeningmutaties.Debet as Debet, ".
		"Rekeningmutaties.Credit as Credit, ".
		"Rekeningmutaties.Valutakoers,
		 1 $koersQuery as Rapportagekoers ".
		"FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
		"$portefeuilleFilter AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
		"Rekeningmutaties.Transactietype <> 'B' AND ".
		"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' ".
		"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		// haal koersresultaat op om % te berekenen

		$rapjaar = date('Y',db2jul($this->rapportageDatumVanaf));
		//$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta);
		$transactietypen = array();

		$buffer = array();
		$sortBuffer = array();
    
    if($DB->records()==0)
    {
      $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
      $this->pdf->ln();
      $this->pdf->MultiCell(280,4,"Er zijn geen transacties geweest gedurende de verslagperiode.", 0, "L");
      return true;
    }
    
    
    $velden=array('Aantal','Debet','Credit');
    $fondsAandeelOpDatum=array();
		while($mutaties = $DB->nextRecord())
		{

			if($this->pdf->lastPOST['doorkijk']==1)
			{
				if($mutaties['Portefeuille']<> $this->portefeuille )
				{
					$fonds=$fondsPerPortefeuille[$mutaties['Portefeuille']];
					//$this->verdiept->FondsPortefeuilleData[]
					//
					if(isset($fondsAandeelOpDatum[$fonds][$mutaties['Boekdatum']]))
						$aandeel=$fondsAandeelOpDatum[$fonds][$mutaties['Boekdatum']];
					else
					{
						$aandeel=bepaalHuisfondsAandeel($fonds, $this->portefeuille, $mutaties['Boekdatum']);
						$fondsAandeelOpDatum[$fonds][$mutaties['Boekdatum']]=$aandeel;
					}
          $mutaties['aandeel']=$aandeel;
  				foreach($velden as $veld)
	  			{
		  			$mutaties[$veld]=$mutaties[$veld]*$aandeel;
			  	}
				}

			}

			$buffer[] = $mutaties;

		}
    
    $totaal_aankoop_waarde=0;
    $totaal_verkoop_waarde=0;
    $fill=false;
		foreach ($buffer as $mutaties)
		{

			//if($mutaties['Transactietype'] != "A/S")
			$mutaties['Aantal'] = abs($mutaties['Aantal']);

			$aankoop_koers = "";
			$aankoop_waarde = "";
			$verkoop_koers = "";
			$verkoop_waarde = "";
			$t_aankoop_koers=0;
      $t_aankoop_waardeinValuta=0;
			$t_aankoop_waarde=0;
			$t_verkoop_koers=0;
			$t_verkoop_waardeinValuta=0;
			$t_verkoop_waarde=0;

			if(isset($mutaties['aandeel']) && $mutaties['aandeel']<0)
      { //listarray($mutaties);
        if($mutaties['Transactietype'] =='L')
        {
          $mutaties['Transactietype'] = 'D';
          if($mutaties['Credit']<>0)
          {
            $mutaties['Debet']=$mutaties['Credit'];
            $mutaties['Credit']=0;
          }
        }
        elseif($mutaties['Transactietype'] =='D')
        {
          $mutaties['Transactietype'] = 'L';
          if($mutaties['Debet']<>0)
          {
            $mutaties['Credit']=$mutaties['Debet'];
            $mutaties['Debet']=0;
          }
        }
      }


			switch($mutaties['Transactietype'])
			{
					case "A" :
						// Aankoop
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers, 2);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "A/O" :
						// Aankoop / openen
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "A/S" :
						// Aankoop / sluiten
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_koers > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);

					break;
					case "B" :
						// Beginstorting
					break;
					case "D" :
					case "S" :
							// Deponering
						$t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_aankoop_waardeinValuta = abs($mutaties['Debet']);
						$t_aankoop_koers					= $mutaties['Fondskoers'];

						$totaal_aankoop_waarde += $t_aankoop_waarde;

						if($t_aankoop_waarde > 0)
							$aankoop_koers 					= $this->formatGetal($t_aankoop_koers,2);
						if($t_aankoop_waarde > 0)
							$aankoop_waarde 				= $this->formatGetal($t_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "L" :
							// Lichting
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V" :
							// Verkopen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V/O" :
							// Verkopen / openen
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					case "V/S" :
					 		// Verkopen / sluiten
						$t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
						$t_verkoop_waardeinValuta = abs($mutaties['Credit']);
						$t_verkoop_koers					= $mutaties['Fondskoers'];

						$totaal_verkoop_waarde += $t_verkoop_waarde;

						if($t_verkoop_koers > 0)
							$verkoop_koers 					= $this->formatGetal($t_verkoop_koers,2);
						if($t_verkoop_waarde > 0)
							$verkoop_waarde 				= $this->formatGetal($t_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal);
					break;
					default :
								$_error = "Fout ongeldig tranactietype!!";
					break;
			}
	
      $datum=date("d-m-Y",db2jul($mutaties['Boekdatum']));
  		preg_match("/[0-9]{1,}/", $mutaties['Rekening'], $matches);
			if($matches[0])
				$rekening=$matches[0];
			else
				$rekening='';


			if($fill==true)
			{
				$this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
				$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,0,1);
				//listarray($this->pdf->widths);
				$fill=false;
			}
			else
			{
				$this->pdf->fillCell=array();
				$fill=true;
			}
			
      if($this->pdf->GetStringWidth($mutaties['Omschrijving']) > $this->pdf->widths[4]-1)
			  $omschrijving=substr($mutaties['Omschrijving'],0,35)."...";
			else
		  	$omschrijving=$mutaties['Omschrijving'];

			$this->pdf->row(array($datum,
											$mutaties['Transactietype'],
											$rekening,
											$this->formatGetal($mutaties['Aantal'],2),
											$omschrijving,
											$aankoop_koers,
											$aankoop_waarde,
											$verkoop_koers,
											$verkoop_waarde));

					$this->pdf->excelData[]=array(date("d-m-Y",db2jul($mutaties['Boekdatum'])),
											$mutaties['Transactietype'],
						          $rekening,
											round($mutaties['Aantal'],2),
											$mutaties['Omschrijving'],
                      $t_aankoop_koers,
                      $t_aankoop_waardeinValuta,
											$t_aankoop_waarde,
											$t_verkoop_koers,
											$t_verkoop_waardeinValuta,
											$t_verkoop_waarde);
	

			$transactietypen[] = $mutaties['Transactietype'];
		}


		$totaal1 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5] ;
		$totaal2 = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3] + $this->pdf->widthA[4] + $this->pdf->widthA[5] + $this->pdf->widthA[6] + $this->pdf->widthA[7] + $this->pdf->widthA[8] ;
		$totaal3 = $totaal2 + $this->pdf->widthA[9] + $this->pdf->widthA[10] + $this->pdf->widthA[11];

		//$actueeleind = $actueel + $this->pdf->widthA[3] +$this->pdf->widthA[4]+ $this->pdf->widthA[5]+ $this->pdf->widthA[6]+ $this->pdf->widthA[7];

		$this->pdf->SetTextColor($this->pdf->rapport_subtotaal_omschr_fontcolor['r'],$this->pdf->rapport_subtotaal_omschr_fontcolor['g'],$this->pdf->rapport_subtotaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_kop2_font,$this->pdf->rapport_kop2_fontstyle,$this->pdf->rapport_kop2_fontsize);





		//$koersresultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

	
	//	$this->pdf->line($totaal1 + 2, $this->pdf->GetY(), $totaal1 + $this->pdf->widthA[6],  $this->pdf->GetY());
	//	$this->pdf->line($totaal2 + 2, $this->pdf->GetY(), $totaal2 + $this->pdf->widthA[9],  $this->pdf->GetY());
	//	$this->pdf->line($totaal3 + 2, $this->pdf->GetY(), $totaal3 + $this->pdf->widthA[12], $this->pdf->GetY());
		$this->pdf->fillCell=array();
    $this->pdf->CellBorders = array('','','','','','','T','','T');
    $this->pdf->SetFont($this->pdf->rapport_kop2_font,'B',$this->pdf->rapport_kop2_fontsize);
    
    $this->pdf->row(array("",
								"",
								"",
								"",
								vertaalTekst("Totalen",$this->pdf->rapport_taal),'',
									$this->formatGetal($totaal_aankoop_waarde,$this->pdf->rapport_TRANS_decimaal),
								"",
								$this->formatGetal($totaal_verkoop_waarde,$this->pdf->rapport_TRANS_decimaal),
								""));
		unset($this->pdf->CellBorders);
								//$totaal_resultaat_waarde
	//	$this->pdf->line($totaal1+2,$this->pdf->GetY(),$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY());
	//	$this->pdf->line($totaal2+2,$this->pdf->GetY(),$totaal2 + $this->pdf->widthA[9],$this->pdf->GetY());
	//	$this->pdf->line($totaal3+2,$this->pdf->GetY(),$totaal3 + $this->pdf->widthA[12],$this->pdf->GetY());

	//	$this->pdf->line($totaal1+2,$this->pdf->GetY()+1,$totaal1 + $this->pdf->widthA[6],$this->pdf->GetY()+1);
	//	$this->pdf->line($totaal2+2,$this->pdf->GetY()+1,$totaal2 + $this->pdf->widthA[9],$this->pdf->GetY()+1);
	//	$this->pdf->line($totaal3+2,$this->pdf->GetY()+1,$totaal3 + $this->pdf->widthA[12],$this->pdf->GetY()+1);
	




		if($this->pdf->rapport_TRANS_legenda == 1)
		{
			$this->pdf->ln();

			$transactietypen = array_unique($transactietypen);
			sort($transactietypen);

			$hoogte = (count($transactietypen) * 4) ;
			if(($this->pdf->GetY() + $hoogte + 8) >= $this->pdf->pagebreak) {
				$this->pdf->AddPage();
				$this->pdf->ln();
			}

			$this->pdf->SetFillColor($this->pdf->rapport_regelAchtergrond[0],$this->pdf->rapport_regelAchtergrond[1],$this->pdf->rapport_regelAchtergrond[2]);
			//$this->pdf->SetX($this->pdf->marge + $this->pdf->widthB[0]);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte,'F');
			$this->pdf->SetFillColor(0);
			$this->pdf->Rect($this->pdf->marge ,$this->pdf->getY(),110,$hoogte);
			//$this->pdf->SetX($this->pdf->marge);
			$this->pdf->SetX($this->pdf->marge);

			// kopfontcolor

			$this->pdf->SetTextColor(0,0,0);
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

			reset($transactietypen);

   		while (list($key, $val) = each($transactietypen))
   		{
				switch($val)
				{
					case "A" :
						$this->pdf->Cell(30,4, "A", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Aankoop",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "A/O" :
						$this->pdf->Cell(30,4, "A/O", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Aankoop / openen",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "A/S" :
						$this->pdf->Cell(30,4, "A/S", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Aankoop / sluiten",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "D" :
						$this->pdf->Cell(30,4, "D", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Deponering",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "L" :
						$this->pdf->Cell(30,4, "L", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Lichting",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "V" :
						$this->pdf->Cell(30,4, "V", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Verkoop",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "V/O" :
						$this->pdf->Cell(30,4, "V/O", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Verkoop / openen",$this->pdf->rapport_taal), 0,1, "L");
					break;
					case "V/S" :
						$this->pdf->Cell(30,4, "V/S", 0,0, "L");
						$this->pdf->Cell(80,4, vertaalTekst("Verkoop / sluiten",$this->pdf->rapport_taal), 0,1, "L");
					break;
				}
			}
		}

		if(isset($this->pdf->rapport_TRANS_disclaimerText))
		{
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize-2);
		  $this->pdf->MultiCell(280,4, $this->pdf->rapport_TRANS_disclaimerText, 0, "L");
		  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
		}
		unset(	$this->pdf->fillCell );

//	if($this->pdf->rapport_layout == 16)
//	{
//	  $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
//    if($this->pdf->GetY()< $this->pdf->pagebreak-4)
//      $this->pdf->SetY($this->pdf->pagebreak);
//	  $this->pdf->Cell(80,4, vertaalTekst("Hoewel deze informatie met de meeste zorg is samengesteld, aanvaardt Keijser Capital N.V. geen aansprakelijkheid voor de volledigheid en/of juistheid van bovenstaande opgave.",$this->pdf->rapport_taal), 0,1, "L");
//	}



	}
}
?>