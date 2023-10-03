<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/22 15:47:20 $
File Versie					: $Revision: 1.8 $

$Log: Factuur_L76.php,v $
Revision 1.8  2020/04/22 15:47:20  rvv
*** empty log message ***

Revision 1.7  2018/09/19 17:35:50  rvv
*** empty log message ***

Revision 1.6  2018/09/15 17:46:24  rvv
*** empty log message ***

Revision 1.5  2018/06/21 05:45:13  rvv
*** empty log message ***

Revision 1.4  2018/06/20 16:40:51  rvv
*** empty log message ***

Revision 1.3  2018/06/11 05:13:52  rvv
*** empty log message ***

Revision 1.2  2018/06/10 14:42:04  rvv
*** empty log message ***

Revision 1.1  2018/04/22 09:36:19  rvv
*** empty log message ***

Revision 1.11  2015/12/02 09:48:57  rvv
*** empty log message ***

Revision 1.10  2015/04/22 15:26:04  rvv
*** empty log message ***

Revision 1.9  2015/04/15 18:22:16  rvv
*** empty log message ***

Revision 1.8  2015/02/09 11:26:15  rvv
*** empty log message ***

Revision 1.7  2015/02/07 20:38:33  rvv
*** empty log message ***

Revision 1.6  2014/08/02 15:26:16  rvv
*** empty log message ***

Revision 1.5  2014/01/18 17:25:33  rvv
*** empty log message ***

Revision 1.4  2013/07/10 10:45:13  rvv
*** empty log message ***

Revision 1.3  2013/07/10 08:09:19  rvv
*** empty log message ***

Revision 1.2  2013/07/06 16:01:29  rvv
*** empty log message ***

Revision 1.1  2013/06/30 15:06:36  rvv
*** empty log message ***

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


$margeBackup=$this->pdf->marge;
    $this->pdf->marge = 30;
    $this->pdf->rowHeight=4;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		$this->pdf->rapport_type = "FACTUUR";

$DB=new DB();
$DB->SQL("SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email
FROM
Vermogensbeheerders
WHERE Vermogensbeheerders.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'");
$vermData=$DB->lookupRecord();

		$front='Times';
$fontsize=8;
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
		
    if(is_file($this->pdf->rapport_logo))
		{
		   $factor=0.06;
		   $xSize=983*$factor;//$x=885*$factor;
		   $ySize=217*$factor;//$y=849*$factor;
			 $this->pdf->Image($this->pdf->rapport_logo, 135, 8, $xSize, $ySize);
     //  $this->pdf->SetFillColor(255,255,255);
     //  $this->pdf->Rect(229,18,$xSize,5,"F");
       
		}
$this->pdf->SetY(35);
$this->pdf->SetWidths(array(70,50,80));
$this->pdf->SetAligns(array("L","R","L"));
$this->pdf->SetFont($font,"B",$fontsize);
$this->pdf->row(array('','',$vermData['Naam']));
$this->pdf->SetFont($font,"",$fontsize);
$this->pdf->Ln();
$this->pdf->row(array('','Bezoekadres:',$vermData['Adres'].' '.$vermData['Woonplaats']));
$this->pdf->row(array('','Telefoon',$vermData['Telefoon']));
$this->pdf->row(array('','E-mail','info@ambassadorvermogensbeheer.nl'));


    $this->pdf->SetY($this->pdf->getY() +5);
    $this->pdf->nextFactuur=true;
		// start eerste block
		$this->pdf->SetWidths(array(100,80));
		$this->pdf->SetAligns(array("L","L"));

		$kwartaal = ceil(date("n",db2jul($this->waarden['datumTot']))/3);
		$kwartalen[1] = 'eerste';
		$kwartalen[2] = 'tweede';
		$kwartalen[3] = 'derde';
		$kwartalen[4] = 'vierde';

    $this->pdf->SetFont($font,"",12);
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

    if($this->factuurnummer < 1000)
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

//		if ($this->waarden['SoortOvereenkomst'] == 'Advies')
//		{
//		   $this->pdf->row(array("Factuur inzake", 'Adviesvergoeding '.$kwartalen[$this->waarden['kwartaal']].' kwartaal '.$this->waarden['rapportJaar']));
//		   $this->pdf->ln($tussenruimte);
//		}
//		else
		//{
		  $this->pdf->row(array("Factuur inzake", 'Vergoeding '.$kwartalen[$this->waarden['kwartaal']].' kwartaal '.$this->waarden['rapportJaar']));
		  $this->pdf->ln($tussenruimte);
		//}



		$this->pdf->row(array("Notanummer", $factuurnummer));
		$this->pdf->ln($tussenruimte);
    $this->pdf->row(array("Clientnummer", $this->portefeuille));

		$this->pdf->SetY($this->pdf->getY() +15);
		$this->pdf->SetFont($font,"",12);
    
   // $this->waarden['beheerfeePerPeriodeNor']-=$this->waarden['huisfondsKorting'];

 //$this->waarden['beheerfeeBetalenIncl']=(($this->waarden['beheerfeePerPeriodeNor']+$this->waarden['performancefee'])*($this->waarden['btwTarief']+100)/100)+$this->waarden['transactiefee'];
//	if ($this->waarden['SoortOvereenkomst'] == 'Advies')
//	{
	$introTekst = 	"De vergoeding over het ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".$this->waarden['rapportJaar']." bedraagt € "
		                 .$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)." inclusief BTW. Voor de berekening wordt verwezen naar de onderstaande specificatie.";
//    $type = "Adviesvergoeding";
//	}
//	else
//	{
//		$introTekst = 	"De beheervergoeding ".$kwartalen[$this->waarden['kwartaal']]." kwartaal ".$this->waarden['rapportJaar']." bedraagt € "
//		                 .$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)." inclusief BTW. Voor de berekening wordt verwezen naar de onderstaande toelichting.";
//	  $type = "Beheervergoeding";
//	}

	$this->pdf->SetWidths(array(150));
	$this->pdf->row(array($introTekst));

	$this->pdf->ln(6);

	//BeheerfeeAantalFacturen
  
  $beheerfeePercentagePeriode=$this->waarden['BeheerfeePercentageVermogenDeelVanJaar'];

	if (strlen($beheerfeePercentagePeriode) > 8)
	  $beheerfeePercentagePeriode  = $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ,6);
	else
	  $beheerfeePercentagePeriode = $this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ;


	$this->pdf->SetWidths(array(85,5,27,8,30));
	$this->pdf->SetAligns(array("L","R","R","R","R"));

	//listarray($this->waarden);

	$startY= $this->pdf->GetY();

	$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(),$this->pdf->marge +155 ,$this->pdf->GetY());
	$this->pdf->ln(1);
	$this->pdf->row(array("Specificatie"));
	$this->pdf->ln(1);
	$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(),$this->pdf->marge +155 ,$this->pdf->GetY());
	$startY2= $this->pdf->GetY();
	$this->pdf->ln(4);

//	$this->pdf->row(array("  Totaal vermogen per ".date("j",db2jul($this->waarden['datumVan']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumVan']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumVan'])), "€", $this->formatGetal($this->waarden['totaalWaardeVanaf'],2) ));
//	$this->pdf->ln(1);
	$this->pdf->row(array("  Totaal vermogen per ".date("j",db2jul($this->waarden['datumTot']))." ".vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->waarden['datumTot'])),
										    "€", $this->formatGetal($this->waarden['totaalWaarde'],2) ));
	
/*
  $liqVer=round($this->waarden['basisRekenvermogen']-$this->waarden['rekenvermogen']-$this->waarden['huisfondsWaarde'],2);
  if($liqVer<>0)
	{
		$this->pdf->row(array("  Liquiditeiten verrekening",
											'€', $this->formatGetal($liqVer, 2)));
		$this->pdf->row(array("  Beheervergoeding berekend over",
											'€', $this->formatGetal($this->waarden['rekenvermogen'], 2)));
	}
*/
//	$this->pdf->Line($this->pdf->marge + 90 ,$this->pdf->GetY(),$this->pdf->marge +90 + 25 ,$this->pdf->GetY());
//	$this->pdf->ln(1);
//	$this->pdf->row(array("  Gemiddeld belegd vermogen","€",$this->formatGetal($this->waarden['gemiddeldeVermogen'],2)));
	$this->pdf->ln();
 // listarray($this->waarden);
  //rvv
  if(count($this->waarden['huisfondsKortingFondsen']) > 0)
  {
    $newTotaal=$this->waarden['totaalWaarde'];
    foreach($this->waarden['huisfondsKortingFondsen'] as $fonds=>$waarde)
    {
      $this->pdf->row(array("  $fonds", "€", $this->formatGetal($waarde,2) ));
      $newTotaal -= $waarde;
    }
    $this->pdf->ln(2);
    $this->pdf->Line($this->pdf->marge + 85 ,$this->pdf->GetY(),$this->pdf->marge +85 + 30 ,$this->pdf->GetY());
    $this->pdf->ln(2);
    $this->pdf->row(array("  Beheervergoeding berekend over", "€", $this->formatGetal($newTotaal,2) ));
  }
	$this->pdf->ln();
  //
  //rvv

	if(round($this->waarden['administratieBedrag'],2) <> 0)
	{
	  $this->pdf->row(array("  Administratiekosten",'','',"€",$this->formatGetal($this->waarden['administratieBedrag'],2)."  "));
	  $this->pdf->ln(1);
	}

//	$this->pdf->row(array("  Managementfee in % ","","$beheerfeePercentagePeriode %"));
//	$this->pdf->ln(1);

	if($this->formatGetal($this->waarden['MinJaarbedragGebruikt']))
	{


	}
	else
	{
  	$this->pdf->row(array("  Beheervergoeding",'','',"€",$this->formatGetal($this->waarden['beheerfeePerPeriodeNor'],2)."  "));
    $this->waarden['beheerfeePerPeriode']=$this->waarden['beheerfeePerPeriodeNor'];
	}
  $this->pdf->ln(); 
  if($this->waarden['transactiefee'] <> 0)
  {
     $this->pdf->row(array("  Mutatievergoeding",'','',"€",$this->formatGetal($this->waarden['transactiefee'],2)."  "));
  }
  else
  {
    $this->pdf->ln();
	}
  $this->pdf->ln(); 
  //$this->waarden['performancefee']=100;
  if($this->waarden['performancefee'] <> 0)
  {
    $this->pdf->row(array("  Performancefee",'','',"€",$this->formatGetal($this->waarden['performancefee'],2)."  "));
    $this->waarden['beheerfeePerPeriodeNor']+=$this->waarden['performancefee'];
  }
  else
  {
   
    $this->pdf->ln();
  }
  $this->pdf->SetFont($font,"B",12);
  $this->pdf->CellBorders = array('','','','','T');
		$this->pdf->row(array("  Totaal exclusief BTW",'','',"€",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)."  "));
	  $this->pdf->ln();
  $this->pdf->SetFont($font,"",12);
  unset($this->pdf->CellBorders);

    //$this->waarden['btw']=round($this->waarden['beheerfeePerPeriodeNor']*($this->waarden['btwTarief'])/100,2);
		$this->pdf->ln();
		$this->pdf->row(array("  BTW ".$this->formatGetal($this->waarden['btwTarief'],0) ."%",'','',"€",$this->formatGetal($this->waarden['btw'],2)."  "));
		$this->pdf->ln();
    //$this->waarden['btw']=$this->waarden['btwNor'];


    

	  $this->pdf->ln();
    $this->pdf->SetFont($font,"B",12);
    $this->pdf->CellBorders = array('','','','','T');
    $this->pdf->row(array("  Totaal inclusief BTW",'','',"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)."  "));
	  $this->pdf->ln();
    $this->pdf->SetFont($font,"",12);
    unset($this->pdf->CellBorders);
    
		$this->pdf->Line($this->pdf->marge ,$this->pdf->GetY(),$this->pdf->marge +155 ,$this->pdf->GetY());

		$this->pdf->Line($this->pdf->marge ,$startY,$this->pdf->marge,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge+119,$startY2,$this->pdf->marge+119,$this->pdf->GetY());
		$this->pdf->Line($this->pdf->marge+155 ,$startY,$this->pdf->marge+155,$this->pdf->GetY());

			$this->pdf->SetWidths(array(150));
		$this->pdf->ln(15);
		$this->pdf->row(array("Het bedrag zal binnenkort van uw rekening worden afgeschreven."));
		$this->pdf->ln(15);




$this->pdf->marge=$margeBackup;
$this->pdf->SetLeftMargin($this->pdf->marge);
$this->pdf->SetRightMargin($this->pdf->marge);
$this->pdf->SetTopMargin($this->pdf->marge);


$this->pdf->AutoPageBreak=false;
$this->pdf->SetY(297-15);
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize-2);
$this->pdf->Cell(210-$this->pdf->marge*2,5,$vermData['Adres'].', '.$vermData['Woonplaats'].' - www.ambassadorvermogensbeheer.nl - info@ambassadorvermogensbeheer.nl - 035-2031035',0,1,'C');
$this->pdf->Cell(210-$this->pdf->marge*2,5,'IBAN: NL59 ABNA 0516 0106 89 - KvK: 28087987 - BTW: NL8092.88.722 B01',0,1,'C');
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->AutoPageBreak=true;








?>