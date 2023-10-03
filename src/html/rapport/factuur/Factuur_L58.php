<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/09/04 14:43:02 $
File Versie					: $Revision: 1.10 $

$Log: Factuur_L58.php,v $
Revision 1.10  2016/09/04 14:43:02  rvv
*** empty log message ***

Revision 1.9  2015/11/29 13:16:01  rvv
*** empty log message ***

Revision 1.8  2015/02/22 09:56:15  rvv
*** empty log message ***

Revision 1.7  2015/02/18 17:09:30  rvv
*** empty log message ***

Revision 1.6  2015/01/31 20:03:51  rvv
*** empty log message ***

Revision 1.5  2015/01/26 15:27:41  rvv
*** empty log message ***

Revision 1.4  2015/01/19 16:11:23  rvv
*** empty log message ***

Revision 1.3  2015/01/19 12:48:24  rvv
*** empty log message ***

Revision 1.2  2015/01/19 11:33:50  rvv
*** empty log message ***

Revision 1.1  2015/01/14 20:21:48  rvv
*** empty log message ***

Revision 1.1  2010/03/31 17:26:47  rvv
*** empty log message ***



*/

global $__appvar;



      if(file_exists(FPDF_FONTPATH.'calibri.php'))
		  {
  	    if(!isset($this->pdf->fonts['calibri']))
	      {
		      $this->pdf->AddFont('calibri','','calibri.php');
		      $this->pdf->AddFont('calibri','B','calibrib.php');
		      $this->pdf->AddFont('calibri','I','calibrii.php');
		      $this->pdf->AddFont('calibri','BI','calibribi.php');
	      }
			  $this->pdf->rapport_font = 'calibri';
		  }
      else
        $this->pdf->rapport_font = 'Times';
        

		$this->pdf->rapport_type = "FACTUUR";

		$this->pdf->AddPage('P');

    $logoWidth=25;
    $uitlijningX=210-$logoWidth-25;
    $lijnkleur=array(228,182,68);
    $kopkleur=array(201,128,61);
    $rechterTekst=array(40,52,83);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',10);
    if(is_file($this->pdf->rapport_logo))
		{
			$this->pdf->Image($this->pdf->rapport_logo, $uitlijningX, 15, $logoWidth);
      
      $this->pdf->Line($uitlijningX+0.5,85,$uitlijningX+35,85,array('color'=>$lijnkleur));
      $this->pdf->Circle($uitlijningX,85,0.5,0.5);
 		}
    
    $extraLn=2;
    $this->pdf->SetWidths(array($uitlijningX-$this->pdf->marge,40));
    $this->pdf->SetAligns(array("L","L","L"));
    $this->pdf->SetY(95);
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','Evolf B.V.'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($rechterTekst[0],$rechterTekst[1],$rechterTekst[2]);
    $this->pdf->row(array('','Beukenlaan 137-141'));
    $this->pdf->Ln($extraLn);
    $this->pdf->row(array('','5616 VD Eindhoven'));
    $this->pdf->Ln($extraLn);
    $this->pdf->row(array('','Nederland'));
    
    $this->pdf->Ln(10); 
       
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','Telefoon'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($rechterTekst[0],$rechterTekst[1],$rechterTekst[2]);
    $this->pdf->row(array('','+31 (0)40 288 11 44'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','Email'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($rechterTekst[0],$rechterTekst[1],$rechterTekst[2]);
    $this->pdf->row(array('','info@evolf.nl'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','Internet'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($rechterTekst[0],$rechterTekst[1],$rechterTekst[2]);
    $this->pdf->row(array('','evolf.nl'));
    
    $this->pdf->Ln(10);
    
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','KvK'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($rechterTekst[0],$rechterTekst[1],$rechterTekst[2]);
    $this->pdf->row(array('','60181311'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','iban'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($rechterTekst[0],$rechterTekst[1],$rechterTekst[2]);
    $this->pdf->row(array('','NL45ABNA0435835688'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','BTW'));
    $this->pdf->Ln($extraLn);
    $this->pdf->SetTextColor($rechterTekst[0],$rechterTekst[1],$rechterTekst[2]);
    $this->pdf->row(array('','8537.97.973.B.01'));
       
     
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetY(40);
    $xMarge=15;
		$this->pdf->SetFont($this->pdf->rapport_font,'B',10);
    $this->pdf->SetWidths(array($xMarge,150));
    $this->pdf->SetAligns(array("L","L","L"));
    $this->pdf->Ln();
		$this->pdf->row(array('','Persoonlijk / Vertrouwelijk'));
    $this->pdf->Ln();
	  $this->pdf->SetFont($this->pdf->rapport_font,'',10);
  	$this->pdf->row(array('',$this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
    if ($this->waarden['clientAdres'] !='')  
      $this->pdf->row(array('',$this->waarden['clientAdres']));
    $woonplaats=$this->waarden['clientWoonplaats'];
		if($this->waarden['clientPostcode'] != '')
	  	$woonplaats = $this->waarden['clientPostcode'] . " " . $woonplaats;
    $this->pdf->row(array('',$woonplaats));
		$this->pdf->row(array('',$this->waarden['clientLand']));
    $this->pdf->Ln();
    $this->pdf->row(array('',$this->waarden['FactuurMemo']));

		$this->pdf->SetY(85);
	  $this->pdf->SetFont($this->pdf->rapport_font,'B',10);
  	$this->pdf->row(array('','Factuur'));
    $this->pdf->Ln();
    $YPage=$this->pdf->getY();
    $this->pdf->SetWidths(array($xMarge,28,150));
    $this->pdf->SetFont($this->pdf->rapport_font,'I',10);
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->row(array('','Factuurnummer'));
    $this->pdf->row(array('','Datum'));
    $this->pdf->SetY($YPage);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'',10);
    $rapJul=db2jul($this->waarden['datumTot']);
    
    $rapJulVan=db2jul($this->waarden['datumFacturatieVanaf']);
    //$rapJulVan=db2jul($this->waarden['datumVan']);
    
    $kwartaal = ceil(date("n",$rapJul)/3);
    $jaar=date('Y',$rapJul);// echo "<br>\n $rapJul $jaar $kwartaal<br>\n";
    //$this->pdf->row(array('','',$this->waarden['debiteurnr'].".".$jaar.".".$kwartaal.".".sprintf("%03d",$this->waarden['factuurNummer'])));
    $this->pdf->row(array('','',$this->waarden['portefeuille'].".".$jaar.".".$kwartaal.".".sprintf("%03d",$this->waarden['factuurNummer'])));
    $this->pdf->row(array('','',date("j")." ".vertaalTekst($__appvar["Maanden"][date("n")],$pdf->rapport_taal)." ".date("Y")));   
		$this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',10);
		$this->pdf->SetWidths(array($xMarge,100,5,20));
    $this->pdf->SetAligns(array("L","L","R","R"));
    $this->pdf->ln();
    $this->pdf->row(array('',"Omschrijving",'',"Bedrag"));
    $this->pdf->Line($this->pdf->marge+$xMarge,$this->pdf->getY(),$this->pdf->marge+$xMarge+80+15+30,$this->pdf->getY());
    $this->pdf->ln(6);
    $this->pdf->SetFont($this->pdf->rapport_font,'',10);
    
    if($this->waarden['SoortOvereenkomst'] <> '')
     $soortOvereenkomst=$this->waarden['SoortOvereenkomst'];
    else
     $soortOvereenkomst='Advies';
   

    $this->pdf->row(array('',$soortOvereenkomst."fee"));
    $this->pdf->ln(4);
    $this->pdf->row(array('',"Eindvermogen voor fee-berekening € ".$this->formatGetal($this->waarden['rekenvermogen'],2)));
    if($this->waarden['BeheerfeeMethode']==1)
    {
      foreach ($this->waarden['staffelWaarden'] as $staffelWaarden)
      {
        $this->pdf->row(array('', $this->formatGetal(($staffelWaarden['percentage'] / $this->waarden['BeheerfeeAantalFacturen']), 4) . "% per kwartaal over € ". $this->formatGetal($staffelWaarden['waarde'],2), '€', $this->formatGetal($staffelWaarden['feeDeel'], 2) ));
        //$this->formatGetal($this->waarden['beheerfeeBetalen'], 2)
      }
    }
    else
    {
      $this->pdf->row(array('', round(($this->waarden['BeheerfeePercentageVermogen'] / $this->waarden['BeheerfeeAantalFacturen']), 4) . "% per kwartaal vanaf " . (date("j", $rapJulVan)) . " " . vertaalTekst($__appvar["Maanden"][date("n", $rapJulVan)], $pdf->rapport_taal) . " " . date("Y", $rapJulVan), '€', $this->formatGetal($this->waarden['beheerfeeBetalen'], 2)));
    }
    $this->pdf->ln(6);
    $this->pdf->SetWidths(array($xMarge+70,30,5,20));
    $this->pdf->Line($this->pdf->marge+$xMarge+70,$this->pdf->getY(),$this->pdf->marge+$xMarge+80+15+30,$this->pdf->getY());
    $this->pdf->ln();
    $this->pdf->row(array('',"Totaal excl. btw",'€',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
    $this->pdf->ln();
    $this->pdf->row(array('',$this->waarden['btwTarief']."% BTW",'€',$this->formatGetal($this->waarden['btw'],2)));
  	$this->pdf->ln();
  	$this->pdf->row(array('',"Totaal incl. btw","€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
  	$this->pdf->ln(10);
    $this->pdf->SetWidths(array($xMarge,125));
    $this->pdf->SetY(260);
    $this->pdf->SetTextColor($kopkleur[0],$kopkleur[1],$kopkleur[2]);
    $this->pdf->SetFont($this->pdf->rapport_font,'I',10);
    $this->pdf->row(array('',"Het bedrag wordt binnen 7 dagen automatisch geïncaseerd van uw rekening ".$this->waarden['rekeningEur']." bij ".$this->waarden['depotbankOmschrijving']."."));
    $this->pdf->SetFont($this->pdf->rapport_font,'',10);
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);
?>