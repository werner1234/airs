<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/04/12 08:30:57 $
File Versie					: $Revision: 1.4 $

$Log: Factuur_L62.php,v $
Revision 1.4  2017/04/12 08:30:57  rvv
*** empty log message ***

Revision 1.3  2016/04/20 15:48:10  rvv
*** empty log message ***

Revision 1.2  2016/04/16 17:13:32  rvv
*** empty log message ***

Revision 1.1  2016/04/13 16:29:35  rvv
*** empty log message ***

Revision 1.11  2016/01/28 15:27:18  rvv
*** empty log message ***

Revision 1.10  2016/01/28 15:24:28  rvv
*** empty log message ***

Revision 1.9  2016/01/28 08:32:32  rvv
*** empty log message ***

Revision 1.8  2016/01/27 17:09:49  rvv
*** empty log message ***

Revision 1.7  2016/01/17 18:17:14  rvv
*** empty log message ***

Revision 1.6  2015/11/11 17:31:31  rvv
*** empty log message ***

Revision 1.5  2015/10/29 04:56:05  rvv
*** empty log message ***

Revision 1.4  2015/10/28 16:58:42  rvv
*** empty log message ***

Revision 1.3  2015/10/21 16:14:27  rvv
*** empty log message ***

Revision 1.2  2015/10/07 19:39:13  rvv
*** empty log message ***

Revision 1.1  2015/10/04 11:51:13  rvv
*** empty log message ***



*/


global $__appvar;
$this->pdf->rapport_type = "FACTUUR";


		if(file_exists(FPDF_FONTPATH.'calibri.php'))
		{
  	  if(!isset($this->pdf->fonts['calibri']))
	    {
		    $this->pdf->AddFont('calibri','','calibri.php');
		    $this->pdf->AddFont('calibri','B','calibrib.php');
		    $this->pdf->AddFont('calibri','I','calibrii.php');
		    $this->pdf->AddFont('calibri','BI','calibribi.php');
	    }
		 $font = 'calibri';
     $fontsize=8;
	  }
   

		$this->pdf->AddPage('P');
    
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
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);  
	  $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
      $logoYpos=10;
		  $xSize=70;
	    $this->pdf->Image($this->pdf->rapport_logo,210/2-$xSize/2, $logoYpos, $xSize);
      //$this->pdf->SetXY(30,30);
      //$this->pdf->SetFont($font,"",$fontsize);
      //$this->pdf->Cell(100,4,'Veilinghavenkade 135, 3521 AT UTRECHT',0,1,'L');
 		}
    $this->pdf->SetY(40);
    $this->pdf->SetWidths(array(95,50,50));
    $this->pdf->SetAligns(array("L","R","L"));
    $this->pdf->SetFont($font,"B",$fontsize);
    $this->pdf->row(array('','',$vermData['Naam']));
    $this->pdf->SetFont($font,"",$fontsize);
    $this->pdf->Ln();
    $this->pdf->row(array('','Bezoekadres:',$vermData['Adres'].' '.$vermData['Woonplaats']));
    $this->pdf->row(array('','Telefoon',$vermData['Telefoon']));
    $this->pdf->row(array('','E-mail','info@ambassadorinvestments.nl'));
    //$this->pdf->Ln();
    //$this->pdf->row(array('','Bankrekening','NL19ABNA049.54.24.129'));
    //$this->pdf->row(array('','Internetadres','www.tein.eu'));

    if(isset($this->waarden['periodeDagen']['periode']) && $this->waarden['periodeDagen']['periode'] <> '')
    {
      $parts=explode('->',$this->waarden['periodeDagen']['periode']);
      $vanjul=db2jul($parts[0]);
      $totjul=db2jul($parts[1]);
    }
    else
    {
      $vanjul=db2jul($this->waarden['datumVan']);
      $totjul=db2jul($this->waarden['datumTot']);
      if(substr($this->waarden['datumVan'],5,5) != '01-01')
		    $vanjul+=86400;
    }
   	$vanDatum=date("j",$vanjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanjul)],$this->pdf->rapport_taal)." ".date("Y",$vanjul);
    $totDatum=date("j",$totjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$totjul)],$this->pdf->rapport_taal)." ".date("Y",$totjul);
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");


   $this->DB = new DB();
   $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.verzendAdres as adres,
CRM_naw.verzendPc as pc,
CRM_naw.verzendPlaats as plaats,
CRM_naw.verzendLand as land,
CRM_naw.verzendPaAanhef,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    
    $extraMarge=30-$this->pdf->marge;
		$this->pdf->SetY(55-8);
		$this->pdf->SetWidths(array($extraMarge,100,80));
    $this->pdf->SetFont($font,"",10);
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$crmData['naam']));
    $this->pdf->ln(1);
		if (trim($crmData['naam1']) !='')
    {
		  $this->pdf->row(array('',$crmData['naam1']));
      $this->pdf->ln(1);
		}
		if (trim($crmData['verzendPaAanhef']) !='')
    {
		  $this->pdf->row(array('',$crmData['verzendPaAanhef']));
      $this->pdf->ln(1);
		}
    $this->pdf->row(array('',$crmData['adres']));
    $this->pdf->ln(1);
		$plaats='';
    $plaats=$crmData['pc'];
    if($crmData['plaats'] != '')
      $plaats.="  ".$crmData['plaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$crmData['land']));

    $this->pdf->SetY(105);
    $this->pdf->SetFont($font,"BU",$fontsize+4);
    $this->pdf->row(array('',"FACTUUR"));
    $this->pdf->SetFont($font,"",$fontsize+2);
    $this->pdf->ln(2);
    
    $this->pdf->SetWidths(array($extraMarge,30,80));
    $this->pdf->row(array('','Factuurdatum',$nu));
    if($this->pdf->rapport_datumvanaf<db2jul($this->waarden['datumVan']))
    {
      $vanDatum=db2jul($this->waarden['datumVan']);
      $this->pdf->row(array('','Startdatum',date("j",$vanDatum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanDatum)],$this->pdf->rapport_taal)." ".date("Y",$vanDatum)));
    }
    $this->pdf->row(array('','Factuurnummer',sprintf("%06d",$this->waarden['factuurNummer'])));
    $this->pdf->row(array('','Rekeningnummer',$this->portefeuille));
    $this->pdf->row(array('','Risicoprofiel ',$this->pdf->portefeuilledata['Risicoklasse']));
    // listarray($this->pdf->portefeuilledata);

		$this->pdf->SetY(135);
    $lijnx2=210-10;
    $this->pdf->Line(30,$this->pdf->GetY()-1,$lijnx2,$this->pdf->GetY()-1);
    $this->pdf->SetWidths(array($extraMarge,30,90,30));
		$this->pdf->SetFont($font,"B",11);
    $this->pdf->SetAligns(array('L','L','L','R'));
    $this->pdf->row(array('',"Percentage\nexclusief BTW",'Beschrijving','Bedrag'));
    $this->pdf->SetFont($font,"",11);
    $this->pdf->Line(30,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(6);

    $kwartalen = array('null','eerste','tweede','derde','vierde');
    $jaar=date('Y',$totjul);
    if($this->waarden['BeheerfeeFacturatieVooraf']==1)
    {
      $tmpKwartaal=$this->waarden['kwartaal'];
      $tmpKwartaal++;
      if($tmpKwartaal==5)
      {
        $tmpKwartaal=1;
        $jaar++;
      }
      $kwartaal=$kwartalen[$tmpKwartaal]; 
    }
    else
      $kwartaal=$kwartalen[$this->waarden['kwartaal']]; 
   
    if(trim(strtolower($this->waarden['SoortOvereenkomst']))=='binckgiro')
      $feeNaam='Beheerfee';
    else
      $feeNaam=$this->waarden['SoortOvereenkomst'].'fee';

    if($this->waarden['BeheerfeeMethode']==3)
    {
       $this->pdf->row(array('',$this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'],3),
                            $feeNaam.' '.$kwartaal.
                            ' kwartaal '.$jaar.' over € '.$this->formatGetal($this->waarden['basisRekenvermogen'],2),
                            '€ '.$this->formatGetal($this->waarden['beheerfeePerPeriode']+$this->waarden['huisfondsKorting']-$this->waarden['performancefee']-$this->waarden['administratieBedrag'],2)));
    }
    else
    {
      foreach($this->waarden['staffelWaarden'] as $index=>$staffelData)
      {
        $this->pdf->row(array('',$this->formatGetal($staffelData['percentage'],3),
                                $feeNaam.' '.$kwartaal.
                               ' kwartaal '.$jaar.' over € '.$this->formatGetal($staffelData['waarde'],2),
                                '€ '.$this->formatGetal($staffelData['feeDeel'],2)));

      }
    }
    


      $this->pdf->ln(10);
    //listarray($this->waarden['huisfondsKortingFondsen']);
   // if($this->waarden['huisfondsKorting'] <> 0)
   // {
      $kortingsFondsen=array();
      foreach($this->waarden['huisfondsKortingFondsen'] as $fonds=>$fondsWaarde)
      {
        if(strtolower(substr($fonds,0,7))=='altaica')
          $fonds='Altaica';
        $kortingsFondsen[$fonds]+=$fondsWaarde;
      }
      foreach($kortingsFondsen as $fonds=>$fondsWaarde)
        $this->pdf->row(array('','','Korting '.$fonds.' €'.$this->formatGetal($fondsWaarde,2)));
    
    //  $this->pdf->SetY($this->pdf->GetY()-$this->pdf->rowHeight);
    //  $this->pdf->row(array('','','','€ -'.$this->formatGetal($this->waarden['huisfondsKorting'],2)));
   // }
     

    if($this->waarden['highwatermark']['performanceFeePercentage'] > 0)
    {
    $this->pdf->row(array('','','Beleggingsresultaat','€ '.$this->formatGetal($this->waarden['highwatermark']['rendementPeriode'],2)));
    $this->pdf->row(array('','','Hoogste cumulatieveperformance t/m vorige periode','€ '.$this->formatGetal($this->waarden['highwatermark']['hoogsteCumulatieveRendement'],2)));
    $this->pdf->row(array('','','Cumulatieveperformance t/m vorige periode','€ '.$this->formatGetal($this->waarden['highwatermark']['cumulatieveRendementTmVorigePeriode'],2)));
    $this->pdf->row(array('','','Cumulatieveperformance t/m huidige periode','€ '.$this->formatGetal($this->waarden['highwatermark']['cumulatieveRendementTmHuidigePeriode'],2)));
    $this->pdf->row(array('','','Resultaat t.b.v. berekening performancefee','€ '.$this->formatGetal($this->waarden['highwatermark']['rendementTbvFee'],2)));
    $this->pdf->ln(3);
    $this->pdf->row(array('','','Performancefee '.$kwartalen[$this->waarden['kwartaal']].
                            ' kwartaal '.date('Y',$totjul),
                            '€ '.$this->formatGetal($this->waarden['performancefee'],2)));
    }                          
  
    $this->pdf->SetY(190);
    $this->pdf->ln(10);
    $this->pdf->row(array('','','Kosten toezichthouder','€ '.$this->formatGetal($this->waarden['administratieBedrag'],2)));
  
    $this->pdf->Line(30,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
    $this->pdf->row(array('','','  Totaalbedrag ex. BTW','€ '.$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));                                                              
    $this->pdf->Line(30+130,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
    $this->pdf->row(array('','','  BTW '.$this->formatGetal($this->waarden['btwTarief'],2).'%','€ '.$this->formatGetal($this->waarden['btw'],2)));                                                              
    $this->pdf->Line(30+130,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
    $this->pdf->row(array('','','  Totaalbedrag incl. BTW','€ '.$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));                                                              
    $this->pdf->Line(30+130,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
  
  
    $this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetWidths(array($extraMarge,40+65+30));
    $this->pdf->SetY(235);
    if($this->waarden['FactuurMemo']<>'')
    {
      $this->pdf->row(array('',$this->waarden['FactuurMemo']));
      $this->pdf->ln(3);
    }
    $this->pdf->row(array('',trim("Dit bedrag wordt binnen 7 dagen automatisch afgeschreven van uw rekening ".$this->waarden['IBAN'])."."));
    $this->pdf->ln(3);


//      $this->pdf->SetY(260);
//      $this->pdf->SetFont($font,"",7);
//$this->pdf->SetAligns(array('L','R'));
//   $this->pdf->row(array('',"T&E inmaxxa is een handelsnaam van T&E Effecten BV
//Ingeschreven bij de K.v.K. te Utrecht onder nr. 30222517 – BTW-nummer: NL.817576174.B01"));

   //$this->pdf->Rect($this->pdf->marge+$extraMarge,$beginY,210-($extraMarge+$this->pdf->marge)*2,$this->pdf->GetY()-$beginY);
$this->pdf->AutoPageBreak=false;
$this->pdf->SetY(297-15);
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize-2);
$this->pdf->Cell(210,5,$vermData['Adres'].', '.$vermData['Woonplaats'].' - www.ambassadorinvestments.nl - info@ambassadorinvestments.nl - 035-2031035',0,1,'C');
$this->pdf->Cell(210,5,'IBAN: NL59 ABNA 0516 0106 89 - KvK: 28087987 - BTW: NL8092.88.722 B01',0,1,'C');
$this->pdf->SetFont($this->pdf->rapport_font,'',$fontsize);
$this->pdf->AutoPageBreak=true;
   
//   $this->pdf->AddPage($this->pdf->CurOrientation);
//   $this->pdf->emailSkipPages[]=$this->pdf->page;
  $this->pdf->SetTextColor(0,0,0);

?>
