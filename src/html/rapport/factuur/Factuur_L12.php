<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/18 16:47:01 $
File Versie					: $Revision: 1.26 $

$Log: Factuur_L12.php,v $
Revision 1.26  2019/12/18 16:47:01  rvv
*** empty log message ***

Revision 1.25  2019/09/14 17:08:22  rvv
*** empty log message ***

Revision 1.24  2019/07/10 15:39:39  rvv
*** empty log message ***

Revision 1.23  2019/06/29 18:25:27  rvv
*** empty log message ***

Revision 1.22  2019/06/05 16:40:42  rvv
*** empty log message ***

Revision 1.21  2019/04/06 17:14:47  rvv
*** empty log message ***

Revision 1.20  2019/01/12 17:09:06  rvv
*** empty log message ***

Revision 1.19  2019/01/09 15:54:13  rvv
*** empty log message ***

Revision 1.18  2018/12/01 19:52:28  rvv
*** empty log message ***

Revision 1.17  2018/11/03 18:49:44  rvv
*** empty log message ***

Revision 1.16  2018/03/29 06:42:23  rvv
*** empty log message ***

Revision 1.15  2018/03/28 15:46:46  rvv
*** empty log message ***

Revision 1.14  2017/07/12 15:59:58  rvv
*** empty log message ***

Revision 1.13  2015/06/10 16:02:01  rvv
*** empty log message ***

Revision 1.12  2014/01/26 15:08:38  rvv
*** empty log message ***

Revision 1.11  2012/09/26 16:07:01  rvv
*** empty log message ***

Revision 1.10  2012/07/04 16:05:44  rvv
*** empty log message ***

Revision 1.9  2012/06/30 14:45:30  rvv
*** empty log message ***

Revision 1.8  2012/05/23 15:57:43  rvv
*** empty log message ***

Revision 1.7  2012/04/01 07:45:30  rvv
*** empty log message ***

Revision 1.6  2012/01/11 19:22:46  rvv
*** empty log message ***

Revision 1.5  2012/01/11 10:41:35  rvv
*** empty log message ***

Revision 1.4  2012/01/11 08:59:57  rvv
*** empty log message ***

Revision 1.3  2012/01/04 16:29:20  rvv
*** empty log message ***

Revision 1.2  2011/12/31 18:17:33  rvv
*** empty log message ***

Revision 1.1  2011/12/28 18:46:03  rvv
*** empty log message ***

Revision 1.7  2011/04/11 19:49:19  rvv
*** empty log message ***


*/


$kop='iVBORw0KGgoAAAANSUhEUgAAARwAAAEcCAMAAAAiKvvSAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkJBNkQ4REUxRjU5MjExRThCNjk5ODNGNTk5MDE1NjBFIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkJBNkQ4REUyRjU5MjExRThCNjk5ODNGNTk5MDE1NjBFIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QkE2RDhEREZGNTkyMTFFOEI2OTk4M0Y1OTkwMTU2MEUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QkE2RDhERTBGNTkyMTFFOEI2OTk4M0Y1OTkwMTU2MEUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6VosubAAAAM1BMVEWBlqNC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';

$db=new DB();
$query="SELECT * FROM CRM_naw WHERE portefeuille='".$this->waarden['portefeuille']."'";
$db->SQL($query);
$crmData=$db->lookupRecord();


    $mageBackup=$this->pdf->marge;
    $rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->marge = 30;
    $this->pdf->rowHeight=4;
		$this->pdf->SetLeftMargin($this->pdf->marge);
		$this->pdf->SetRightMargin($this->pdf->marge);
		$this->pdf->SetTopMargin($this->pdf->marge);

		// Moet weg bij in gebruikname HelveticaNeue
if (file_exists(FPDF_FONTPATH . 'calibri.php'))
{
  if (!isset($this->pdf->fonts['calibri']))
  {
    $this->pdf->AddFont('calibri', '', 'calibri.php');
    $this->pdf->AddFont('calibri', 'B', 'calibrib.php');
    $this->pdf->AddFont('calibri', 'I', 'calibrii.php');
    $this->pdf->AddFont('calibri', 'BI', 'calibribi.php');
  }

}

    $font='Calibri';
    //$font='HelveticaNeue';

		$this->pdf->SetFont($font,"",11);

		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');
		$this->pdf->SetY($this->pdf->getY() +30);

		$width=80;
		if(file_exists($this->pdf->rapport_logo))
		  $this->pdf->image($this->pdf->rapport_logo,210/2-$width/2,15,$width);

		//965/112
	//	$this->pdf->MemImage(base64_decode($voet), 25, 272,165);// $w=0, $h=0, $link=''
		// start eerste block

		$this->pdf->SetWidths(array(80,90));
		$this->pdf->SetAligns(array("L","L"));
		$kwartaal = intval(ceil(date("n",db2jul($this->waarden['datumTot']))/3));

		$kwartalen[1] = 'eerste';
		$kwartalen[2] = 'tweede';
		$kwartalen[3] = 'derde';
		$kwartalen[4] = 'vierde';

		$this->pdf->SetWidths(array(160));
		$this->pdf->SetAligns(array("L","L"));
		$this->pdf->row(array($this->waarden['CRM_naam']));
		if ($this->waarden['CRM_naam1'] !='')
		  $this->pdf->row(array($this->waarden['CRM_naam1']));
    if ($this->waarden['CRM_PaAanhef'] !='')
      $this->pdf->row(array($this->waarden['CRM_PaAanhef']));
		$this->pdf->row(array($this->waarden['CRM_verzendAdres']));
		$plaats='';
		if($this->waarden['CRM_verzendPc'] != '')
		  $plaats .= $this->waarden['CRM_verzendPc']." ";
		$plaats .= $this->waarden['CRM_verzendPlaats'];
		$this->pdf->row(array($plaats));
		if(strtolower($this->waarden['CRM_verzendLand']) <> 'nederland')
		  $this->pdf->row(array($this->waarden['CRM_verzendLand']));

		$this->pdf->rowHeight=5;
		$this->pdf->SetY($this->pdf->getY() +20);

		$this->pdf->ln();

		if ($this->factuurnummer < 10000)
      $factuurnummer = substr(10000+$this->factuurnummer,1);
    else
      $factuurnummer =   $this->factuurnummer;

		//$this->pdf->SetFont($font,"",11);
		$this->pdf->SetWidths(array(40,100));
		$this->pdf->SetAligns(array("L","L"));
		$tussenruimte =0;
	  $this->pdf->setY(95);
    $this->pdf->SetFont($font,"B",11);
    $this->pdf->row(array(vertaalTekst("Factuur",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($font,"",11);
    $this->pdf->ln();
  	$this->pdf->row(array(vertaalTekst("Factuurdatum",$this->pdf->rapport_taal),": ".date("j")." ".vertaalTekst($this->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln($tussenruimte);
		$this->pdf->row(array(vertaalTekst("Cliëntnummer",$this->pdf->rapport_taal),": ".$crmData['debiteurnr']));
		$this->pdf->ln($tussenruimte);
		$jaar=substr($this->waarden['datumTot'],2,2);
		$factuurnummerFull=date("y").".70.".$factuurnummer;
    $this->pdf->row(array(vertaalTekst("Factuurnummer",$this->pdf->rapport_taal),": $factuurnummerFull"));

    $this->pdf->SetWidths(array(150));
		$this->pdf->SetY(130);
		$this->pdf->SetFont($font,"",11);


if($this->waarden['BeheerfeeAantalFacturen']==1)
{
 
	$periode = 'het afgelopen jaar';
	$periodeTxt='';
}
elseif($this->waarden['BeheerfeeAantalFacturen']==12)
{
  $periode = 'de afgelopen maand';
  $periodeTxt=vertaalTekst($this->__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))],$this->pdf->rapport_taal).' ';
}
else
{
	$periode = 'het afgelopen kwartaal';
	$periodeTxt=vertaalTekst($kwartalen[$kwartaal],$this->pdf->rapport_taal)." ".vertaalTekst("kwartaal",$this->pdf->rapport_taal)." ";
}

//listarray($this->waarden);
if($this->waarden['SoortOvereenkomst']=='Beleggingsadvies'||$this->waarden['SoortOvereenkomst']=='Vermogensbegeleiding')
{
  $feeOmschrijving = 'Vergoeding voor vermogensbegeleiding';
  $feeOmschrijvingKort= 'Vergoeding vermogensbegeleiding';
}
elseif($this->waarden['SoortOvereenkomst']=='Portefeuilleadvies')
{
  $feeOmschrijving = 'Vergoeding voor portefeuilleadvies';
  $feeOmschrijvingKort= 'Vergoeding portefeuilleadvies';
}
elseif($this->waarden['SoortOvereenkomst']=='Overige advieswerkzaamheden')
{
  $feeOmschrijving = 'Vergoeding voor advieswerkzaamheden';
  $feeOmschrijvingKort= 'Vergoeding advieswerkzaamheden';
}
else
{
  $feeOmschrijving = 'Beheervergoeding';
  $feeOmschrijvingKort = 'Beheervergoeding';
}
  if(trim($crmData['extraTekstFactuur']) <> '')
    $this->pdf->row(array($crmData['extraTekstFactuur']));
  else
  	$this->pdf->row(array(vertaalTekst('Hierbij ontvangt u de factuur inzake de '.strtolower($feeOmschrijving).' over '.$periode,$this->pdf->rapport_taal).'.'));

	$this->pdf->ln(6);

	//BeheerfeeAantalFacturen

	if (strlen($beheerfeePercentagePeriode) > 8)
	  $beheerfeePercentagePeriode  = $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ,8);
	else
	  $beheerfeePercentagePeriode = $this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] ;


	$this->pdf->SetWidths(array(115,5,25,10));
	$this->pdf->SetAligns(array("L","R","R","L"));


		$this->pdf->row(array("$feeOmschrijvingKort ".$periodeTxt."20$jaar","€",$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
    foreach($this->waarden['extraFactuurregels']['regels'] as $regel)
    {
	    $this->pdf->row(array(vertaalTekst($regel['omschrijving'],$this->pdf->rapport_taal), "€", $this->formatGetal($regel['bedrag'], 2)));
    }
		$this->pdf->ln($tussenruimte);

		if($this->waarden['btwTarief'] != 0)
		{
	  	$this->pdf->row(array(vertaalTekst("BTW",$this->pdf->rapport_taal)." ".$this->formatGetal($this->waarden['btwTarief'],0)."%","€",$this->formatGetal($this->waarden['btw'],2),'+'));
	  	$this->pdf->Line($this->pdf->marge+115+5,$this->pdf->GetY(),$this->pdf->marge +115+5+25 ,$this->pdf->GetY());
	  	$this->pdf->ln($tussenruimte);
	  //	$this->pdf->row(array("Subtotaal","€",$this->formatGetal($this->waarden['beheerfeePerPeriode']+$this->waarden['btw'],2)));
	  //	$this->pdf->ln($tussenruimte);
    	$this->pdf->Line($this->pdf->marge+115+5,$this->pdf->GetY(),$this->pdf->marge +115+5+25 ,$this->pdf->GetY());
      $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),"€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
		}
		else
		{
		  //$this->pdf->ln(10);
		}

		//$this->pdf->row(array("Bestandsvergoeding","€",$this->formatGetal($this->waarden['bestandsvergoeding'],2),'-'));

		//$this->pdf->ln($tussenruimte);
if($crmData['BTW_verlegd']<>0)
{
  $this->pdf->ln();
  $this->pdf->ln();
  $this->pdf->row(array(vertaalTekst('BTW verlegd', $this->pdf->rapport_taal) . "\n" . vertaalTekst('BTW cliënt', $this->pdf->rapport_taal) . ": " . $crmData['btwnr']));
}
		$this->pdf->ln();
		$this->pdf->ln();
		$this->pdf->SetWidths(array(150));
if($this->waarden['BetalingsinfoMee']==1)
	$this->pdf->row(array(vertaalTekst("Wij verzoeken u vriendelijk bovenvermeld bedrag binnen 14 dagen over te maken naar ABN AMRO Bank, IBAN NL03 ABNA 0519 5296 93 (BIC ABNANL2A) o.v.v. cliënt- en factuurnummer.",$this->pdf->rapport_taal)));
else
	$this->pdf->row(array(vertaalTekst("Het bedrag zal automatisch binnen 7 dagen worden afgeschreven van uw effectenrekening.",$this->pdf->rapport_taal)));
//		"Per 1 januari 2012 is ons BTW-nummer gewijzigd in NL807290427B01."));
	//	$this->pdf->ln(70);
	//	$this->pdf->row(array("Bijlage"));
$x=$this->pdf->getX();
$y=$this->pdf->getY();
$this->pdf->AutoPageBreak=false;
$this->pdf->setXY(0,297-20);
$this->pdf->SetFont($font,"",10);
$this->pdf->SetTextColor(51,51,51);
$this->pdf->MultiCell(210,$this->pdf->rowHeight-0.5,"Waterland Investment Services B.V.
Krijn Taconiskade 428, 1087 HW Amsterdam, The Netherlands | + 31 20 246 6060 | www.waterland.co.nl
Bank NL03ABNA0519529693 | Chamber of Commerce 33306164 | VAT NL807290427B01",0,'C',0);
$this->pdf->AutoPageBreak=true;
$this->pdf->SetTextColor(0,0,0);
$this->pdf->setXY($x,$y);
//logscherm($this->waarden['SoortOvereenkomst']." ".$this->waarden['portefeuilledata']['BeheerfeeMethode']);
	//if($this->waarden['BeheerfeeBasisberekening'] == 4 && $feeOmschrijvingKort != 'Vergoeding vermogensbegeleiding')
  //if($this->waarden['portefeuilledata']['BeheerfeeMethode']==1 || $this->waarden['portefeuilledata']['BeheerfeeMethode']==2 || ($this->waarden['portefeuilledata']['BeheerfeeMethode']==3 && $feeOmschrijvingKort != 'Vergoeding vermogensbegeleiding') )

  if($this->waarden['BeheerfeeBedragVast'] == 0)
	{
	  $this->pdf->SetFont($font,"",11);
		$this->pdf->AddPage('P');
		$this->pdf->SetWidths(array(150));
		$width=17;
		$this->pdf->MemImage(base64_decode($kop), 210/2-$width/2, 10,$width );// $w=0, $h=0, $link=''
		$this->pdf->ln(20);

		$this->pdf->row(array(vertaalTekst("Specificatie $feeOmschrijvingKort",$this->pdf->rapport_taal)));
    $this->pdf->ln(20);

		$this->pdf->SetWidths(array(40,100));
		$this->pdf->SetAligns(array("L","L"));
		$this->pdf->row(array(vertaalTekst("Cliëntnummer",$this->pdf->rapport_taal),": ".$crmData['debiteurnr']));
		$this->pdf->ln($tussenruimte);
		$this->pdf->row(array(vertaalTekst("Factuurnummer",$this->pdf->rapport_taal),": ".$factuurnummerFull));
		$this->pdf->ln(20);

		$this->pdf->SetWidths(array(150));
		$this->pdf->row(array(vertaalTekst("Gemiddelde marktwaarde van de portefeuille",$this->pdf->rapport_taal).":"));
		$this->pdf->ln(10);

  	$this->pdf->SetWidths(array(50,5,30,5,25,5,25));
  	$this->pdf->SetAligns(array("L","R","R","R",'R','R'));

	  for($i=1;$i<4;$i++)
  	{
  	  if($this->waarden["maandsData_$i"] > 0)
      {
        $this->pdf->row(array(date('d', $this->waarden["maandsData_$i"]) . ' ' . vertaalTekst($this->__appvar["Maanden"][date('n', $this->waarden["maandsData_$i"])], $this->pdf->rapport_taal) . ' ' . date('Y', $this->waarden["maandsData_$i"]), "€", $this->formatGetal($this->waarden["maandsWaarde_$i"], 0)));
        $this->pdf->ln($tussenruimte);
      }
	  }
	 
		$this->pdf->Line($this->pdf->marge+50,$this->pdf->GetY(),$this->pdf->marge+50+5+30 ,$this->pdf->GetY());
		$this->pdf->ln($tussenruimte);
		$this->pdf->row(array("","€",$this->formatGetal($this->waarden['portefeuilleVerdeling']['maandsGemiddeldeWaarde']['totaal'],0)));//$this->waarden['maandsGemiddelde']
		
		if($this->waarden['BeheerfeeBedragBuitenFee']<>0)
    {
      $this->pdf->ln(4);
      $this->pdf->row(array(vertaalTekst("Uitgesloten vermogen",$this->pdf->rapport_taal), "€", $this->formatGetal($this->waarden['BeheerfeeBedragBuitenFee'], 0)));
      $this->pdf->Line($this->pdf->marge + 50, $this->pdf->GetY(), $this->pdf->marge + 50 + 5 + 30, $this->pdf->GetY());
      $this->pdf->ln($tussenruimte);
      $this->pdf->row(array("", "€", $this->formatGetal($this->waarden['maandsGemiddelde'] - $this->waarden['BeheerfeeBedragBuitenFee'], 0)));
    }
		$this->pdf->ln(20);

    $dagen=$this->waarden['periodeDagen']['dagen'];
    $dagenJaar=$this->waarden['periodeDagen']['dagenInJaar'];
    $jaardeel=$dagen/$dagenJaar;

    $this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),$this->pdf->marge+50+5+30+5+25+5+25,$this->pdf->GetY());
    $this->pdf->SetAligns(array("L","R","R","L",'R','R','R'));
		$this->pdf->row(array("%","",vertaalTekst('Waarde',$this->pdf->rapport_taal),'',vertaalTekst('Periode',$this->pdf->rapport_taal),'',''));
		$this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),$this->pdf->marge+50+5+30+5+25+5+25,$this->pdf->GetY());
		$this->pdf->SetAligns(array("L","R","R","R",'R','R','R'));
	  $this->pdf->ln($tussenruimte);

//listarray($this->waarden);
    if($this->waarden['BeheerfeeMethode']==3)
    {
        $this->pdf->row(array($this->formatGetal($this->waarden['BeheerfeePercentageVermogen'],2),"€",$this->formatGetal($this->waarden['maandsGemiddelde'],0),'',"$dagen ".vertaalTekst("dagen",$this->pdf->rapport_taal),'€',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
    }
    elseif($this->waarden['BeheerfeeMethode']==1)
    {
		  foreach ($this->waarden['staffelWaarden'] as $staffel)
		  {
		    if($staffel['waarde'] <> 0)
		      $this->pdf->row(array($this->formatGetal($staffel['percentage'],2),"€",$this->formatGetal($staffel['waarde'],0),'',"$dagen ".vertaalTekst("dagen",$this->pdf->rapport_taal),'€',$this->formatGetal($staffel['feeDeel'],2)));
		  }
    }
		$this->pdf->ln(5);
		$this->pdf->Line($this->pdf->marge+50+5+30+5+25,$this->pdf->GetY(),$this->pdf->marge+50+5+30+5+25+5+25,$this->pdf->GetY());
		$this->pdf->ln($tussenruimte);
		$this->pdf->row(array(vertaalTekst("Beheervergoeding",$this->pdf->rapport_taal),"",'','','','€',$this->formatGetal($this->waarden['beheerfeePerPeriode'],2)));
		$this->pdf->Line($this->pdf->marge,$this->pdf->GetY(),$this->pdf->marge+50+5+30+5+25+5+25,$this->pdf->GetY());

	}
//		$this->pdf->rowHeight=4;

$this->pdf->marge=$mageBackup;
$this->pdf->rowHeight=$rowHeightBackup;
$this->pdf->SetLeftMargin($this->pdf->marge);
$this->pdf->SetRightMargin($this->pdf->marge);
$this->pdf->SetTopMargin($this->pdf->marge);
?>