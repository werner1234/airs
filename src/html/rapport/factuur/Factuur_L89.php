<?php
/*
Author  						: $Author: cvs $
Laatste aanpassing	: $Date: 2020/07/14 10:25:59 $
File Versie					: $Revision: 1.4 $

$Log: Factuur_L89.php,v $
Revision 1.4  2020/07/14 10:25:59  cvs
plaatsnaam terug naar Naarden

Revision 1.3  2020/07/13 10:18:58  cvs
plaatsnaam aan gepast naar Culemborg

Revision 1.2  2020/07/12 16:36:02  rvv
*** empty log message ***

Revision 1.1  2020/07/12 10:35:48  rvv
*** empty log message ***

Revision 1.2  2012/11/25 13:16:56  rvv
*** empty log message ***

*/



    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize-2);
    $extraMarge=22;
 		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');
    
    if(is_file($this->pdf->rapport_logo))
		{
      $factor=0.04;
      $xSize=1500*$factor;
      
      $logopos=($this->pdf->w/2)-($xSize/2);
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 13,$xSize);
     
	    $db=new DB();
	    $query="SELECT naam,adres,woonplaats,telefoon,email FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$this->waarden['Vermogensbeheerder']."'";
	    $db->SQL($query);
	    $vermData=$db->lookupRecord();
     $this->pdf->setY(22);
     
     $this->pdf->SetTextColor($this->pdf->rapport_logokleur[0],$this->pdf->rapport_logokleur[1],$this->pdf->rapport_logokleur[2]);
     $this->pdf->SetWidths(array(150,50));
     $this->pdf->SetAligns(array("L","L","L"));
     $this->pdf->row(array("",$vermData['adres']));
     $this->pdf->row(array("",$vermData['woonplaats']));
     //$this->pdf->ln();
     //$this->pdf->row(array("","Postbus 316"));
     //$this->pdf->row(array("","5260 AH Vught"));
     $this->pdf->ln();
     $this->pdf->row(array("","t"));
     $this->pdf->row(array("","e"));
     
     $this->pdf->ln(-8);
     $this->pdf->SetTextColor(151,137,126);
     $this->pdf->row(array("","   ".$vermData['telefoon']));
     $this->pdf->row(array("","   ".$vermData['email']));
     $this->pdf->row(array("","www.catalpavermogensbeheer.nl"));
     $this->pdf->row(array("","K.v.K. 75273802"));
     
     $this->pdf->SetTextColor(0,0,0);
		
		}
    
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    
		$this->pdf->SetY(64);
		$this->pdf->SetWidths(array($extraMarge,110,80));
		$this->pdf->SetAligns(array("L","L","L",'R'));
		$this->pdf->row(array('',$this->waarden['clientNaam']));
		if ($this->waarden['clientNaam1'] !='')
		  $this->pdf->row(array('',$this->waarden['clientNaam1']));
		$this->pdf->row(array('',$this->waarden['clientAdres']));
		$plaats=$this->waarden['clientWoonplaats'];
		if($this->waarden['clientPostcode'] != '')
	  	$plaats = $this->waarden['clientPostcode'] . " " . $plaats;
		$this->pdf->row(array('',$plaats));
		$this->pdf->row(array('',$this->waarden['clientLand']));

		$this->pdf->SetY(100);

    $kwartaal=ceil(date("n",db2jul($this->waarden['datumTot']))/3);
 
    
    $productieDatum=date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y");
    $this->pdf->ln(8);
    $this->pdf->SetWidths(array($extraMarge,135));
    $this->pdf->SetAligns(array("L",'R'));
    $this->pdf->row(array('','Naarden, '.$productieDatum));
    $this->pdf->SetWidths(array($extraMarge,105,5,25));
    $this->pdf->SetAligns(array("L","L","L",'R'));
    $this->pdf->ln(8);
    $this->pdf->row(array('',"Nota : ".$this->waarden['rapportJaar'].'-Q'.$kwartaal.'-'.sprintf("%04d",$this->waarden['factuurNummer'])));
		$this->pdf->ln(8);
    $this->pdf->row(array('','Betreft: Beheervergoeding '.$kwartaal.'e kwartaal '.$this->waarden['rapportJaar']));
	
    $this->pdf->SetWidths(array($extraMarge,105,5,25));

    $rapJul=db2jul($this->waarden['datumTot']);
		$this->pdf->ln();
    $this->pdf->row(array('',"Beheerd vermogen per ".(date("d",$rapJul))." ".vertaalTekst($this->__appvar["Maanden"][date("n",$rapJul)],$pdf->rapport_taal)." ".date("Y",$rapJul),"€",$this->formatGetal($this->waarden['rekenvermogen'],2)));
    $this->pdf->ln(16);
    $this->pdf->row(array('','Beheervergoeding ','€',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
    $this->pdf->CellBorders=array('','','','U');
    $this->pdf->ln();
    $this->pdf->row(array('',"B.T.W. ".$this->waarden['btwTarief']."% ",'€',$this->formatGetal($this->waarden['btw'],2)));
  	$this->pdf->ln();
    unset($this->pdf->CellBorders);
  	$this->pdf->row(array('',"Totaal","€",$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
    $this->pdf->ln(8);
    $this->pdf->SetWidths(array($extraMarge,150));
    $this->pdf->row(array('',"U hoeft geen betaling te verrichten, het bovenstaande bedrag is inmiddels van uw bankrekening afgeschreven."));
    $this->pdf->ln(16);
    $this->pdf->row(array('',"BTW no.: NL860218260B01"));


?>