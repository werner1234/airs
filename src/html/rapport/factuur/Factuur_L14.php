<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/12/12 16:53:11 $
File Versie					: $Revision: 1.8 $

$Log: Factuur_L14.php,v $
Revision 1.8  2012/12/12 16:53:11  rvv
*** empty log message ***

Revision 1.7  2011/04/11 19:49:19  rvv
*** empty log message ***

Revision 1.6  2011/04/11 18:02:23  rvv
*** empty log message ***

Revision 1.5  2010/01/20 12:28:47  rvv
*** empty log message ***

Revision 1.4  2009/05/05 12:38:08  cvs
*** empty log message ***

Revision 1.3  2008/10/30 12:10:17  rvv
*** empty log message ***

Revision 1.2  2008/09/26 07:54:41  rvv
*** empty log message ***

Revision 1.1  2008/03/18 09:42:38  rvv
*** empty log message ***

Revision 1.3  2008/01/10 16:27:31  rvv
*** empty log message ***

Revision 1.2  2007/10/04 09:14:51  rvv
*** empty log message ***

Revision 1.1  2007/08/02 14:46:59  rvv
*** empty log message ***



*/


//listarray($this->waarden);



    $this->pdf->marge = 30;
    $this->pdf->rowHeight=4;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->rapport_type = "FACTUUR";
		$front='Times';
		if(file_exists(FPDF_FONTPATH.'calibri.php'))
		{
  	  if(!isset($this->pdf->fonts['calibri']))
	    {
		    $this->pdf->AddFont('calibri','','calibri.php');
		    $this->pdf->AddFont('calibri','B','calibrib.php');
		    $this->pdf->AddFont('calibri','I','calibrii.php');
		    $this->pdf->AddFont('calibri','BI','calibribi.php');
	    }
		  //$this->pdf->rapport_font = 'calibri';
		  $font='calibri';
		  $this->pdf->SetFont($font,"",12);
		}
		else
		$this->pdf->SetFont($font,"",12);



		$this->pdf->AddPage('P');


		$this->pdf->SetY($this->pdf->getY() +30);
		// start eerste block

				$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));

		$kwartaal = ceil(date("n",db2jul($this->waarden['datumTot']))/3);
	//	$kwartaal = ceil(date("n",db2jul('2006-03-20'))/3);

		$kwartalen[1] = 'eerste';
		$kwartalen[2] = 'tweede';
		$kwartalen[3] = 'derde';
		$kwartalen[4] = 'vierde';

		$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));
		$this->pdf->row(array($this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array($this->waarden['clientNaam1']));
		$this->pdf->row(array($this->waarden['clientAdres']));
		$plaats='';
		if($this->waarden['clientPostcode'] != '')
		  $plaats .= $this->waarden['clientPostcode']." ";
		$plaats .= $this->waarden['clientWoonplaats'];
		$this->pdf->row(array($plaats));

		$this->pdf->SetY($this->pdf->getY() +20);

		$this->pdf->ln();
/*
		if ($this->factuurnummer < 10)
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-00'.$this->factuurnummer;
		elseif  ($this->factuurnummer < 100)
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-0'.$this->factuurnummer;
		else //toevoeging voor nummers >100
		  $factuurnummer = $this->waarden['rapportJaar']."-".$this->waarden['kwartaal'].'-'.$this->factuurnummer;
*/

if ($this->factuurnummer < 1000)
  $factuurnummer = substr(1000+$this->factuurnummer,1);
else
  $factuurnummer =   $this->factuurnummer;

		$this->pdf->SetFont($font,"",12);
		$this->pdf->SetWidths(array(30,100));
		$this->pdf->SetAligns(array("L","L"));
		$tussenruimte =1;
	  $this->pdf->setY(100);
//rapdate		$this->pdf->row(array("Datum:", date("j",db2jul($this->waarden['datumTot']))." ".$this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))]." ".date("Y",db2jul($this->waarden['datumTot']))));
  	$this->pdf->row(array("Datum", date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y")));
		$this->pdf->ln($tussenruimte);

		if ($this->waarden['SoortOvereenkomst'] == 'Advies')
		{
		   $this->pdf->row(array("Factuur inzake", 'Adviesvergoeding '.$kwartalen[$this->waarden['kwartaal']].' kwartaal '.$this->waarden['rapportJaar']));
		   $this->pdf->ln($tussenruimte);
		}
		else
		{
		  $this->pdf->row(array("Factuur inzake", 'Beheervergoeding '.$kwartalen[$this->waarden['kwartaal']].' kwartaal '.$this->waarden['rapportJaar']));
		  $this->pdf->ln($tussenruimte);
		}



		$this->pdf->row(array("Notanummer", $factuurnummer));
		$this->pdf->ln($tussenruimte);
    $this->pdf->row(array("Clientnummer", $this->portefeuille));
    $this->pdf->ln($tussenruimte);

		$this->pdf->ln();
		$this->pdf->SetY($this->pdf->getY() +15);
		$this->pdf->SetFont($font,"",12);


	if ($this->waarden['SoortOvereenkomst'] == 'Advies')
	{
	$introTekst = 	"De adviesvergoeding ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".$this->waarden['rapportJaar']." bedraagt € "
		                 .$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)." inclusief BTW. Voor de berekening wordt verwezen naar de onderstaande toelichting.";
    $type = "Adviesvergoeding";
	}
	else
	{
		$introTekst = 	"De beheervergoeding ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".$this->waarden['rapportJaar']." bedraagt € "
		                 .$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)." inclusief BTW. Voor de berekening wordt verwezen naar de onderstaande toelichting.";
	  $type = "Beheervergoeding";
	}

	$this->pdf->SetWidths(array(150));
	$this->pdf->row(array($introTekst));

	$this->pdf->ln(6);

	//BeheerfeeAantalFacturen

	if (strlen($beheerfeePercentagePeriode) > 8)
	  $beheerfeePercentagePeriode  = $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ,8);
	else
	  $beheerfeePercentagePeriode = $this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ;


	$this->pdf->SetWidths(array(85,5,25,10,30));
	$this->pdf->SetAligns(array("L","R","R","R","R"));

	//listarray($this->waarden);

	$startY= $this->pdf->GetY();

	$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(),$this->pdf->marge +155 ,$this->pdf->GetY());
	$this->pdf->ln(1);
	$this->pdf->row(array("Toelichting berekening "));
	$this->pdf->ln(1);
	$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(),$this->pdf->marge +155 ,$this->pdf->GetY());
	$startY2= $this->pdf->GetY();
	$this->pdf->ln(4);

	$this->pdf->row(array("  Totaal vermogen per ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])), "€", $this->formatGetal($this->waarden['totaalWaardeVanaf'],2) ));
	$this->pdf->ln(1);
	$this->pdf->row(array("  Totaal vermogen per ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])), "€", $this->formatGetal($this->waarden['totaalWaarde'],2) ));
	$this->pdf->Line($this->pdf->marge + 90 ,$this->pdf->GetY(),$this->pdf->marge +90 + 25 ,$this->pdf->GetY());
	$this->pdf->ln(1);
	$this->pdf->row(array("  Gemiddeld belegd vermogen","€",$this->formatGetal($this->waarden['gemiddeldeVermogen'],2)));
	$this->pdf->ln();

	if(round($this->waarden['administratieBedrag'],2) <> 0)
	{
	  $this->pdf->row(array("  Administratiekosten",'','',"€",$this->formatGetal($this->waarden['administratieBedrag'],2)."  "));
	  $this->pdf->ln(1);
	}

	$this->pdf->row(array("  $type in % ","","$beheerfeePercentagePeriode %"));
	$this->pdf->ln(1);

	if($this->formatGetal($this->waarden['MinJaarbedragGebruikt']))
	{


	}
	else
	{

	$this->pdf->row(array("  $type",'','',"€",$this->formatGetal($this->waarden['beheerfeePerPeriodeNor'],2)."  "));
	}



		$this->pdf->ln();
		$this->pdf->row(array("  BTW ".$this->formatGetal($this->waarden['btwTarief'],0) ."%",'','',"€",$this->formatGetal($this->waarden['btwNor'],2)."  "));
		$this->pdf->ln(8);
		$this->pdf->row(array("  $type inclusief BTW",'','',"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)."  "));
	  $this->pdf->ln();
		$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(),$this->pdf->marge +155 ,$this->pdf->GetY());

		$this->pdf->Line($this->pdf->marge ,$startY,$this->pdf->marge,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge+119,$startY2,$this->pdf->marge+119,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge+155 ,$startY,$this->pdf->marge+155,$this->pdf->GetY());

			$this->pdf->SetWidths(array(150));
		$this->pdf->ln(15);
		$this->pdf->row(array("Het bedrag zal binnenkort van uw rekening worden afgeschreven."));
				$this->pdf->ln(15);
		$this->pdf->SetFont($font,"",10);
		$this->pdf->row(array("BTW-nummer Antaurus B.V.: NL008356178B01"));








?>