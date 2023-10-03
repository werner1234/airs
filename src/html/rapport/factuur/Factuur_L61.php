<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/25 17:16:27 $
File Versie					: $Revision: 1.22 $

$Log: Factuur_L61.php,v $
Revision 1.22  2020/04/25 17:16:27  rvv
*** empty log message ***

Revision 1.21  2020/04/01 16:52:24  rvv
*** empty log message ***

Revision 1.20  2020/01/23 05:18:10  rvv
*** empty log message ***

Revision 1.19  2019/09/28 17:19:16  rvv
*** empty log message ***

Revision 1.18  2019/09/23 04:20:29  rvv
*** empty log message ***

Revision 1.17  2017/05/10 14:45:45  rvv
*** empty log message ***

Revision 1.16  2017/01/21 17:12:29  rvv
*** empty log message ***

Revision 1.15  2016/07/16 15:15:15  rvv
*** empty log message ***

Revision 1.14  2016/07/09 19:02:35  rvv
*** empty log message ***

Revision 1.13  2016/04/30 15:32:22  rvv
*** empty log message ***

Revision 1.12  2016/04/16 17:13:32  rvv
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
   
   // if($this->pdf->selectData['allInOne']==1)
    //{
    //  $this->pdf->oddEvenCheck[$this->portefeuille]=count($this->pdf->pages);
  //  }
   // else
   // {
	  	if (count($this->pdf->pages) % 2 && ($this->pdf->selectData['type'] != 'eMail'))
	  	{
    		$this->pdf->AddPage('L');
	  	  $this->pdf->emailSkipPages[]=$this->pdf->page;
	  	}
   // }
		$this->pdf->AddPage('P');
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);  
	  $logo=$__appvar['basedir']."/html/rapport/logo/".$this->pdf->portefeuilledata['Logo'];
		if(is_file($logo))
		{
      $logoYpos=5;
		  $xSize=70;
	    $this->pdf->Image($this->pdf->rapport_logo,210/2-$xSize/2, $logoYpos, $xSize);
      $this->pdf->SetXY(30,45);
      $this->pdf->SetFont($font,"",$fontsize);
      $this->pdf->Cell(100,4,'Wilhelminakade 1, 3072 AP  ROTTERDAM',0,1,'L');
 		}
    $this->pdf->SetY(55);
    $this->pdf->SetWidths(array(95,50,50));
    $this->pdf->SetAligns(array("L","R","L"));
    $this->pdf->SetFont($font,"",$fontsize);

$this->pdf->row(array('','Bezoekadres:',"Maastoren 43rd floor\nWilhelminakade 1, 3072 AP  ROTTERDAM"));
$this->pdf->row(array('','Telefoon','+31 (0)10 302  71 00'));
$this->pdf->row(array('','E-mail','info@blauwtulp.com'));
$this->pdf->row(array('','Internetadres','www.blauwtulp.com'));
$this->pdf->row(array('','Bankrekening','NL83 INGB 0007 3516 11'));

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
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.verzendPaAanhef,
Portefeuilles.BetalingsinfoMee
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
//$crmData=array('naam'=>'naam','naam1'=>'naam1','adres'=>'adres','pc'=>'1111aa','plaats'=>'plaats','land'=>'land');
    $extraMarge=15-$this->pdf->marge;
		$this->pdf->SetY(55);
		$this->pdf->SetWidths(array($extraMarge+15,100,80));
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
    
    $this->pdf->SetWidths(array($extraMarge,100,80));
    $this->pdf->SetY(105);
    $this->pdf->SetFont($font,"BU",$fontsize+4);
    $this->pdf->row(array('',"FACTUUR"));
    $this->pdf->SetFont($font,"",$fontsize+2);
    $this->pdf->ln(2);
    $this->pdf->SetWidths(array($extraMarge,30,80));
    $this->pdf->row(array('','Factuurdatum',$nu));
    $this->waarden['factuurNummer']='ROT'.sprintf("%06d",$this->waarden['factuurNummer']);
    $this->pdf->row(array('','Factuurnummer',$this->waarden['factuurNummer']));
    $this->pdf->row(array('','Rekeningnummer',$this->portefeuille));

		$this->pdf->SetY(135);
    $lijnx2=210-10;
    $this->pdf->Line($extraMarge+$this->pdf->marge,$this->pdf->GetY()-1,$lijnx2,$this->pdf->GetY()-1);
    $this->pdf->SetWidths(array($extraMarge,22,130,30));
		$this->pdf->SetFont($font,"B",11);
    $this->pdf->SetAligns(array('L','L','L','R'));
    $this->pdf->row(array('',"Percentage\nexcl. BTW",'Beschrijving','Bedrag'));
    $this->pdf->SetFont($font,"",11);
    $this->pdf->Line($extraMarge+$this->pdf->marge,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);

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
   
    $vastbedrag=$this->waarden['BeheerfeeBedragVast']*$this->waarden['periodeDeelVanJaar'];
    
    $andereFeeRegels=false;
    if($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'] <> 0)
    {
      $andereFeeRegels=true;
      $this->pdf->row(array('', $this->formatGetal($this->waarden['BeheerfeePercentageVermogenDeelVanJaar'], 3),
                        $feeNaam . ' ' . $kwartaal .
                        ' kwartaal ' . $jaar . ' over € ' . $this->formatGetal($this->waarden['rekenvermogen'] + $this->waarden['huisfondsWaarde'], 2),
                        '€ ' . $this->formatGetal($this->waarden['beheerfeePerPeriode'] + $this->waarden['huisfondsKorting'] - $this->waarden['performancefee'] - $this->waarden['administratieBedrag'] - $vastbedrag, 2)));
    }
    else
    {
      if($this->waarden['staffelWaarden']['schijvenPerentage'] <> 0)
      {
        $andereFeeRegels=true;
        $this->pdf->row(array('',$this->formatGetal($this->waarden['staffelWaarden']['schijvenPerentage'],3),
                          $feeNaam.' '.$kwartaal.
                          ' kwartaal '.$jaar.' over € '.$this->formatGetal($this->waarden['rekenvermogen'],2),
                          '€ '.$this->formatGetal($this->waarden['beheerfeePerPeriode'] + $this->waarden['huisfondsKorting'] - $this->waarden['performancefee'] - $this->waarden['administratieBedrag'] - $vastbedrag,2)));
      }
      else
      {
        foreach($this->waarden['staffelWaarden'] as $index=>$staffelData)
        {
          $andereFeeRegels=true;
          $this->pdf->row(array('',$this->formatGetal($staffelData['percentage']*$this->waarden['periodeDeelVanJaar'],3),
                          $feeNaam.' '.$kwartaal.
                          ' kwartaal '.$jaar.' over € '.$this->formatGetal($staffelData['waarde'],2),
                          '€ '.$this->formatGetal($staffelData['feeDeel'],2)));
        }
      }
    }
    if($this->waarden['BeheerfeeBedragBuitenFee'] <> 0)
      $this->pdf->row(array('','','Bedrag buiten feeberekening € '.$this->formatGetal($this->waarden['BeheerfeeBedragBuitenFee'],2)));
    if($this->waarden['BeheerfeeBedragVast'] <> 0)
    {
      if($andereFeeRegels==true)
        $this->pdf->row(array('', '', 'Vast bedrag ' . strtolower($this->waarden['SoortOvereenkomst']) . "fee", '€ ' . $this->formatGetal($vastbedrag, 2)));
      else
        $this->pdf->row(array('', '', 'Vast bedrag ' . strtolower($this->waarden['SoortOvereenkomst']) . "fee $kwartaal kwartaal $jaar", '€ ' . $this->formatGetal($vastbedrag, 2)));
    }
//listarray($this->waarden);
    $this->pdf->ln(6);
    $extraRegel=false;
    $kortingsFondsen=array();
    foreach($this->waarden['huisfondsKortingFondsen'] as $fonds=>$fondsWaarde)
    {
     // if(strtolower(substr($fonds,0,7))=='altaica')
     //   $fonds='Altaica';
    //  $kortingsFondsen[$fonds]=$fonds;
      $korting=$fondsWaarde*-0.01*$this->waarden['BeheerfeePercentageVermogen']*$this->waarden['periodeDeelVanJaar'];
      if($korting<>0)
      {
        $extraRegel = true;
        $this->pdf->row(array('', '', 'Korting ' . $fonds . " over € " . $this->formatGetal($fondsWaarde, 2), '€ ' . $this->formatGetal($korting, 2)));
      }
    }
    if($extraRegel==true)
      $this->pdf->ln(3);
    //foreach($kortingsFondsen as $fonds)
//listarray($this->waarden);
    /*
    if($this->waarden['huisfondsKorting'] <> 0)
    {
    //  $this->pdf->SetY($this->pdf->GetY()-$this->pdf->rowHeight);
      $this->pdf->row(array('','','','€ -'.$this->formatGetal($this->waarden['huisfondsKorting'],2)));
    }
    */
  
    if($this->waarden['highwatermark']['performanceFeePercentage'] > 0)
    {
      $this->pdf->SetWidths(array($extraMarge,22,110,30));
    $this->pdf->row(array('','','Beleggingsresultaat','€ '.$this->formatGetal($this->waarden['highwatermark']['rendementPeriode'],2)));
    $this->pdf->row(array('','','Hoogste cumulatieveperformance t/m vorige periode','€ '.$this->formatGetal($this->waarden['highwatermark']['hoogsteCumulatieveRendement'],2)));
    $this->pdf->row(array('','','Cumulatieveperformance t/m vorige periode','€ '.$this->formatGetal($this->waarden['highwatermark']['cumulatieveRendementTmVorigePeriode'],2)));
    $this->pdf->row(array('','','Cumulatieveperformance t/m huidige periode','€ '.$this->formatGetal($this->waarden['highwatermark']['cumulatieveRendementTmHuidigePeriode'],2)));
    $this->pdf->row(array('','','Resultaat t.b.v. berekening performancefee','€ '.$this->formatGetal($this->waarden['highwatermark']['rendementTbvFee'],2)));
    $this->pdf->ln(3);
    $this->pdf->row(array('','','Performancefee '.$kwartalen[$this->waarden['kwartaal']].
                            ' kwartaal '.date('Y',$totjul),'',
                            '€ '.$this->formatGetal($this->waarden['performancefee'],2)));
    }
    
    $this->pdf->SetWidths(array($extraMarge,22,130,30));
    $this->pdf->SetY(190);
    $this->pdf->ln(10);
    if($this->waarden['administratieBedrag'] <> 0)
      $this->pdf->row(array('','','Vastleggingskosten','€ '.$this->formatGetal($this->waarden['administratieBedrag'],2)));
  
    $this->pdf->Line($extraMarge+$this->pdf->marge,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
    $this->pdf->row(array('','','  Totaalbedrag ex. BTW','€ '.$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));                                                              
    $this->pdf->Line($extraMarge+$this->pdf->marge+22,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
    $this->pdf->row(array('','','  BTW '.$this->formatGetal($this->waarden['btwTarief'],2).'%','€ '.$this->formatGetal($this->waarden['btw'],2)));                                                              
    $this->pdf->Line($extraMarge+$this->pdf->marge+22,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
    $this->pdf->row(array('','','  Totaalbedrag incl. BTW','€ '.$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));                                                              
    $this->pdf->Line($extraMarge+$this->pdf->marge,$this->pdf->GetY()+1,$lijnx2,$this->pdf->GetY()+1);
    $this->pdf->ln(2);
  
  
        $this->pdf->SetAligns(array('L','L'));
      $this->pdf->SetWidths(array($extraMarge,40+65+30));
   $this->pdf->SetY(235);
   $this->pdf->row(array('',trim("Dit bedrag wordt binnen 7 dagen automatisch afgeschreven van uw rekening ".$this->waarden['IBAN'])."."));
   $this->pdf->ln(3);
$this->pdf->SetWidths(array($extraMarge-5,40+65+30+30));
      $this->pdf->SetY(260);
      $this->pdf->SetFont($font,"",7);
$this->pdf->SetAligns(array('L','R'));
   $this->pdf->row(array('',"Blauwtulp is onderdeel van Auréus Group B.V., welke staat ingeschreven bij de K.v.K. onder nr. 14073764 - BTW-nummer: NL8111.09343B01"));

   //$this->pdf->Rect($this->pdf->marge+$extraMarge,$beginY,210-($extraMarge+$this->pdf->marge)*2,$this->pdf->GetY()-$beginY);
   $this->pdf->AddPage($this->pdf->CurOrientation);
   $this->pdf->emailSkipPages[]=$this->pdf->page;
  $this->pdf->SetTextColor(0,0,0);

?>
