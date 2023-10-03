<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/25 17:16:27 $
File Versie					: $Revision: 1.27 $

$Log: Factuur_L25.php,v $
Revision 1.27  2020/04/25 17:16:27  rvv
*** empty log message ***

Revision 1.26  2020/02/02 12:08:31  rvv
*** empty log message ***

Revision 1.25  2018/10/10 16:11:45  rvv
*** empty log message ***

Revision 1.24  2018/09/19 17:35:50  rvv
*** empty log message ***

Revision 1.23  2017/10/02 06:04:00  rvv
*** empty log message ***

Revision 1.22  2017/09/30 16:30:24  rvv
*** empty log message ***

Revision 1.21  2017/03/20 06:41:15  rvv
*** empty log message ***

Revision 1.20  2017/02/04 19:11:04  rvv
*** empty log message ***

Revision 1.19  2016/10/19 18:41:21  rvv
*** empty log message ***

Revision 1.18  2016/07/24 09:50:17  rvv
*** empty log message ***

Revision 1.17  2016/02/04 11:56:13  rvv
*** empty log message ***

Revision 1.16  2016/01/27 07:39:03  rvv
*** empty log message ***

Revision 1.15  2014/04/12 16:27:35  rvv
*** empty log message ***

Revision 1.14  2012/09/13 15:59:12  rvv
*** empty log message ***

Revision 1.13  2012/02/09 12:14:28  cvs
adreswijziging

Revision 1.12  2011/12/14 19:00:28  rvv
*** empty log message ***

Revision 1.11  2011/10/12 18:02:04  rvv
*** empty log message ***

Revision 1.10  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.9  2011/07/08 06:43:47  cvs
*** empty log message ***

Revision 1.8  2011/03/13 18:37:31  rvv
*** empty log message ***

Revision 1.7  2011/01/11 08:23:38  cvs
*** empty log message ***

Revision 1.6  2011/01/10 08:02:21  cvs
*** empty log message ***

Revision 1.5  2011/01/08 14:26:52  rvv
*** empty log message ***

Revision 1.4  2011/01/05 18:52:30  rvv
*** empty log message ***

Revision 1.3  2010/07/02 12:23:19  cvs
*** empty log message ***

Revision 1.2  2010/07/02 12:03:34  cvs
*** empty log message ***

Revision 1.1  2010/05/05 18:38:23  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:47  rvv
*** empty log message ***



*/

global $__appvar;


	  $this->pdf->SetWidths(array(22,150));
	  $this->pdf->SetAligns(array('R','L'));
    $this->pdf->rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight = 5;
    
    $this->pdf->brief_font='Arial';
    if(file_exists(FPDF_FONTPATH.'calibril.php'))
		{
  	  if(!isset($this->pdf->fonts['calibri']))
	    {
		    $this->pdf->AddFont('calibri','','calibri.php');
		    $this->pdf->AddFont('calibri','B','calibriB.php');
		    $this->pdf->AddFont('calibri','I','calibrii.php');
		    $this->pdf->AddFont('calibri','BI','calibribi.php');
	    }
		// $this->pdf->rapport_font = 'calibri';
      $this->pdf->brief_font='calibri';
		}
		
    $db=new DB();
    $query="SELECT Portefeuilles.Risicoklasse, Portefeuilles.selectieveld1 FROM Portefeuilles WHERE Portefeuilles.Portefeuille='".$this->waarden['portefeuille']."'";
    $db->SQL($query);
    $portefeuilleData=$db->lookupRecord();
    
		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');

    if(is_file($this->pdf->rapport_logo))
		{
		  $w=48;
			$this->pdf->Image($this->pdf->rapport_logo, $this->pdf->w/2-$w/2, 10, 48);
    //   $pdfObject->Image($pdfObject->rapport_logo,3,180, 48);
		}
		$this->pdf->SetY(50);
		//$this->pdf->SetFont($this->pdf->brief_font,'B',11);
		//$this->pdf->row(array('','Vertrouwelijk'));

		$this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->row(array('',$this->waarden['clientNaam']));
		if(trim($this->waarden['clientNaam1']) <> '')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
		$this->pdf->row(array('',$this->waarden['clientAdres']));
		if($this->waarden['clientPostcode'] != '')
	  	$plaats = $this->waarden['clientPostcode'] . " " .$this->waarden['clientWoonplaats'];
	  else
	  	$plaats = $this->waarden['clientWoonplaats'];
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$this->waarden['clientLand']));

		$this->pdf->SetY(90);
		$this->pdf->SetAligns(array('R','R'));
		$selectieVeld=substr($portefeuilleData['selectieveld1'],0,3);
		$plaatsKoppelingen=array('MAA'=>'Maastricht',
                             'LAN'=>'Lanaken',
                             'AMS'=>'Amsterdam',
                             'ROT'=>'Rotterdam',
                             'DEV'=>'Deventer',
                             'PUT'=>'Putten',
                             'VEN'=>'Venlo',
                             'VLD'=>'Veldhoven',
                             'WAA'=>'Waalre',
                             'ZEE'=>'Goes');
		if(isset($plaatsKoppelingen[$selectieVeld]))
      $plaats=$plaatsKoppelingen[$selectieVeld];
		else
      $plaats='...';

		
    $this->pdf->row(array('',$plaats.', '.date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y")));
		$this->pdf->ln();

		$this->pdf->SetAligns(array('R','L'));

		$this->pdf->row(array('',"Geachte ".$this->waarden['CRM_verzendAanhef'].","));
		$this->pdf->ln();
		$vanaf=db2jul($this->waarden['datumVan']);
		$tot=db2jul($this->waarden['datumTot']);
		$vanafTxt=date("d",$vanaf)." ".vertaalTekst($__appvar["Maanden"][date("n",$vanaf)],$this->pdf->rapport_taal)." ".date("Y",$vanaf);
		$totTxt=date("d",$tot)." ".vertaalTekst($__appvar["Maanden"][date("n",$tot)],$this->pdf->rapport_taal)." ".date("Y",$tot);
		$rapportagePeriode = $vanafTxt.' t/m '.$totTxt;



    if($portefeuilleData['Risicoklasse']=='Niet beursgenoteerd, risicodragend')
      $txt="Bijgaand ontvangt u van ons de kwartaalrapportage en de factuur voor portefeuille ".$this->waarden['portefeuille']." ";
   else
      $txt="Hierbij ontvangt u van ons de factuur voor portefeuille ".$this->waarden['portefeuille']." ";

    $vertaling=array(1=>'eerste',2=>'tweede',3=>'derde',4=>'vierde',5=>'vijfde',6=>'zesde',7=>'zevende',8=>'achtste',9=>'negende',10=>'tiende',11=>'elfde',12=>'twaalfde');
    if($this->waarden['BeheerfeeAantalFacturen']==12)
    {
      $periode = 'Maand';
      $tussen='de';
      $huidige=$vertaling[date("n")];
    }
    else
    {
      $periode = "Kwartaal";
      $tussen='het';
      $huidige=$vertaling[ceil(date("n")/3)];
    }

    if($this->waarden['BeheerfeeFacturatieVooraf']==1)
    {
      $txt.="over $tussen $huidige ".strtolower($periode).".";
    }
    else
    {
      $txt.="over $tussen afgelopen ".strtolower($periode).".";
    }


    $this->pdf->row(array('',$txt));
		$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->brief_font,'B',11);
    $this->waarden['factuurNummer']=substr($portefeuilleData['selectieveld1'],0,3).date("Y").".".sprintf("%03d",$this->waarden['factuurNummer']);
		$this->pdf->row(array('',"Factuurnummer: ".$this->waarden['factuurNummer']));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
		$this->pdf->ln();
		$this->pdf->SetWidths(array(22,100,15,30));
    $this->pdf->SetAligns(array("L","L","R","R"));
   // $this->pdf->row(array('',"Vermogen per $vanafTxt",'€',$this->formatGetal($this->waarden['totaalWaardeVanaf'],2)));
   // $this->pdf->row(array('',"Vermogen per $totTxt",'€',$this->formatGetal($this->waarden['totaalWaarde'],2)));
    if($this->waarden['huisfondsWaarde'] <> 0)
    {
      $this->pdf->row(array('', "Gehanteerd vermogen voor fee berekening", '€', $this->formatGetal($this->waarden['rekenvermogen'], 2)));
    }
    else
    {
      $this->pdf->row(array('', "Gehanteerd vermogen voor fee berekening", '€', $this->formatGetal($this->waarden['basisRekenvermogen'], 2)));
    }
    if(round($this->waarden['basisRekenvermogen']-$this->waarden['rekenvermogen']-$this->waarden['huisfondsWaarde'],1) <> 0.0)
    {
      $this->pdf->row(array('',"Liquiditeiten verrekening",'€',$this->formatGetal($this->waarden['basisRekenvermogen']-$this->waarden['rekenvermogen']-$this->waarden['huisfondsWaarde'],2)));
      $this->pdf->row(array('',"Rekenvermogen",'€',$this->formatGetal($this->waarden['rekenvermogen'],2)));
    }
    
    $this->pdf->ln();

    if($this->waarden['periodeDagen']['dagenInJaar'] <> '')
      $dagen=$this->waarden['periodeDagen']['dagen']."/".$this->waarden['periodeDagen']['dagenInJaar']." van ".$this->waarden['BeheerfeePercentageVermogen']."%";
    else
      $dagen='';
    if(!in_array($this->waarden['BeheerfeeMethode'],array(0,1)))
      $this->pdf->row(array('',$periode."fee $dagen",' ',$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],4)."%")); //$this->waarden['SoortOvereenkomst']
    if(round(1/$this->waarden['BeheerfeeAantalFacturen'],4)==round($this->waarden['periodeDeelVanJaar'],4))
    {
      $vastBedrag = $this->waarden['BeheerfeeBedragVast'] / $this->waarden['BeheerfeeAantalFacturen'];
    }
    else
    {
      $vastBedrag = $this->waarden['periodeDeelVanJaar'] * $this->waarden['BeheerfeeBedragVast'];
    }
    
    $fee=$this->waarden['beheerfeePerPeriodeNor']-$vastBedrag-$this->waarden['administratieBedrag'];
    if(round($fee,2)<>0)
      $this->pdf->row(array('',"Berekende fee ".(($this->waarden['BeheerfeeMethode']==1)?'(conform staffel)':''),'€',$this->formatGetal($fee,2).""));
    if($vastBedrag<>0)
      $this->pdf->row(array('',"Vast bedrag",'€',$this->formatGetal($vastBedrag,2).""));
    if($this->waarden['administratieBedrag']<>0)
      $this->pdf->row(array('',"Administratievergoeding",'€',$this->formatGetal($this->waarden['administratieBedrag'],2).""));
    if($this->waarden['performancefee']<>0)
      $this->pdf->row(array('',"Berekende performance fee",'€',$this->formatGetal($this->waarden['performancefee'],2).""));
    
    //De berekende fee
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->brief_font,'B',11);
    $this->pdf->row(array('',"Factuurbedrag"));
    $this->pdf->SetFont($this->pdf->brief_font,'',11);
    $this->pdf->ln();
    $this->pdf->row(array('',"Subtotaal",'€',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
    $this->pdf->row(array('',"BTW ".$this->waarden['btwTarief']."%",'€',$this->formatGetal($this->waarden['btw'],2)));
    $this->pdf->ln();
    $this->pdf->row(array('',"========= ",' ','========='));
    $this->pdf->ln();
    $this->pdf->row(array('',"Totaal",'€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
    $this->pdf->ln();
    $this->pdf->SetWidths(array(22,145));
  
    if($this->waarden['BetalingsinfoMee'])
    {
      if(trim($this->waarden['FactuurMemo']) <>'')
      {
        $this->pdf->row(array('', $this->waarden['FactuurMemo']));
      }
     //   $this->pdf->row(array('', "Wij verzoeken u vriendelijk om dit bedrag te voldoen binnen 30 dagen op rekeningnummer: IBAN NL61ABNA0421423528 t.n.v. Auréus. U kunt er ook voor kiezen dit bedrag automatisch per kwartaal door ons te laten incasseren, neem hiervoor contact met ons op."));
    }
    else
    {
      if(trim($this->waarden['FactuurMemo']) <>'')
        $this->pdf->row(array('',$this->waarden['FactuurMemo']));
      
      $this->pdf->row(array('','Dit bedrag zal binnenkort van uw rekening '. $this->waarden['portefeuille'] .' bij '. $this->waarden['depotbankOmschrijving'] .' worden afgeschreven, onder vermelding van bovenstaand factuurnummer. Heeft u nog vragen of opmerkingen naar aanleiding van deze factuur, neem dan gerust contact met ons op.'));
        //$this->pdf->row(array('', "Zoals afgesproken zal dit bedrag binnenkort van uw rekening " . $this->waarden['portefeuille'] . " bij " . $this->waarden['depotbankOmschrijving'] . " onder vermelding van factuurnummer $factuurNr worden afgeschreven."));
    }
$this->pdf->ln();
$this->pdf->row(array('','Met vriendelijke groet,'));
$this->pdf->ln();
$this->pdf->row(array('','Auréus'));


    $this->pdf->AutoPageBreak=false;
    $this->pdf->SetY(277);
    $this->pdf->SetWidths(array(10,200));
    $this->pdf->SetAligns(array('L','L','L','L','L'));
    $this->pdf->SetFont($this->pdf->brief_font,'B',8);
    $this->pdf->SetTextColor(151,151,151);
    $this->pdf->SetWidths(array(15,60,55,55));
    $this->pdf->rowHeight=4;
    $this->pdf->row(array('','Auréus Group BV','Website',''));
    $this->pdf->SetFont($this->pdf->brief_font,'',8);
    $this->pdf->ln(-4);
    if($selectieVeld=='LAN')
    {
      $this->pdf->row(array('', '', '', 'IBAN: BE20735028350256'));
      $this->pdf->row(array('', 'Europaplein 13', 'www.aureus.eu', 'BTW: BE 0842.091.840'));
      $this->pdf->row(array('', 'BE-3620  Lanaken', 'info@aureus.eu', 'Vergunninghouder FSMA/AFM'));
    }
    else
    {
      $this->pdf->row(array('', '', '', 'IBAN: NL61ABNA0421423528'));
      $this->pdf->row(array('', 'Piet Heinkade 55', 'www.aureus.eu', 'BTW: NL811109343B01'));
      $this->pdf->row(array('', '1019 GM Amsterdam', 'info@aureus.eu', 'KvK: 14073764'));
    }
    $this->pdf->row(array('','','','',));
    $this->pdf->AutoPageBreak=true;
    $this->pdf->SetFillColor(82,83,90);
    $this->pdf->rect(0,$this->pdf->h-5,$this->pdf->w/2,5,'F');
    $this->pdf->SetFillColor(132,149,164);
    $this->pdf->rect($this->pdf->w/2,$this->pdf->h-5,$this->pdf->w/2,5,'F');
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->rowHeight=$this->pdf->rowHeightBackup;

    ?>