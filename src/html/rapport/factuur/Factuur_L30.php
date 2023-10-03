<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2012/11/25 13:16:56 $
File Versie					: $Revision: 1.2 $

$Log: Factuur_L30.php,v $
Revision 1.2  2012/11/25 13:16:56  rvv
*** empty log message ***

Revision 1.1  2012/11/21 16:29:32  rvv
*** empty log message ***

Revision 1.14  2012/11/14 16:49:19  rvv
*** empty log message ***

Revision 1.13  2012/09/30 11:17:18  rvv
*** empty log message ***

Revision 1.12  2011/10/05 09:44:18  rvv
*** empty log message ***

Revision 1.11  2011/07/03 06:44:00  rvv
*** empty log message ***

Revision 1.10  2011/06/15 16:25:45  rvv
*** empty log message ***

Revision 1.9  2010/04/12 17:23:11  rvv
*** empty log message ***

Revision 1.8  2010/03/03 20:06:41  rvv
*** empty log message ***

Revision 1.7  2009/12/23 15:00:59  rvv
*** empty log message ***

Revision 1.6  2009/06/26 13:37:51  rvv
*** empty log message ***

Revision 1.5  2009/05/05 12:38:08  cvs
*** empty log message ***

Revision 1.4  2008/03/18 09:42:38  rvv
*** empty log message ***

Revision 1.3  2008/01/10 16:27:31  rvv
*** empty log message ***

Revision 1.2  2007/10/04 09:14:51  rvv
*** empty log message ***

Revision 1.1  2007/08/02 14:46:59  rvv
*** empty log message ***



*/



    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize-2);
    $extraMarge=22;
 		$this->pdf->rapport_type = "FACTUUR";
		$this->pdf->AddPage('P');

    if(is_file($this->pdf->rapport_logo))
		{
		  $logopos = 63;
 		  $factor=0.050;
	    $this->pdf->Image($this->pdf->rapport_logo, $logopos, 13, 1691*$factor, 586*$factor);
      
      $DB = new DB();
      $query = "SELECT
Vermogensbeheerders.Vermogensbeheerder,
Vermogensbeheerders.Naam,
Vermogensbeheerders.Adres,
Vermogensbeheerders.Woonplaats,
Vermogensbeheerders.Telefoon,
Vermogensbeheerders.Fax,
Vermogensbeheerders.Email,
Vermogensbeheerders.rekening,
Vermogensbeheerders.bank,
Vermogensbeheerders.website
FROM
Vermogensbeheerders
WHERE
Vermogensbeheerders.Vermogensbeheerder = '".$this->waarden['Vermogensbeheerder']."'";
      $DB->SQL($query);
      $vermData = $DB->lookupRecord();
      
     $this->pdf->setY(22);
     $this->pdf->SetTextColor(246,171,26);
     $this->pdf->SetWidths(array(160,40));
     $this->pdf->SetAligns(array("L","L","L"));
     $this->pdf->row(array("",$vermData['Adres']));
     $this->pdf->row(array("",$vermData['Woonplaats']));
     $this->pdf->ln();
     $this->pdf->row(array("","t"));
     $this->pdf->row(array("","e"));
     //."\n".$vermData['']."\n".$vermData['Telefoon']."\n".$vermData['website']."\n".$vermData['Email']."\n\nbtw NL850856723B01
     
     $this->pdf->ln(-8);
     $this->pdf->SetTextColor(151,137,126);
     $this->pdf->row(array("","   ".$vermData['Telefoon']));
     $this->pdf->row(array("","   ".$vermData['Email']));
     $this->pdf->ln();
     $this->pdf->row(array("",$vermData['website']));
     $this->pdf->row(array("","K.v.K. 62851799"));
     
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
    if($this->waarden['BeheerfeeAantalFacturen']==12)
    {
      global $__appvar;
      $periode = $__appvar["Maanden"][date("n",db2jul($this->waarden['datumTot']))];
    }
		elseif($this->waarden['BeheerfeeAantalFacturen']==4)
    {
      $kwartalen = array('null', 'eerste', 'tweede', 'derde', 'vierde');
      $kwartaal = $kwartalen[$this->waarden['kwartaal']];
      $periode = $kwartaal.' kwartaal';
    }
    elseif($this->waarden['BeheerfeeAantalFacturen']==2)
    {
      if (date("n", db2jul($this->waarden['datumTot']) < 7))
      {
        $periode = 'eerste halfjaar';
      }
      else
      {
        $periode = 'tweede halfjaar';
      }
    }
    else
    {
      $periode='';
    }
    
    $productieDatum=date("j")." ".$this->__appvar["Maanden"][date("n")]." ".date("Y");
    $this->pdf->ln(8);
    $this->pdf->SetWidths(array($extraMarge,135));
    $this->pdf->SetAligns(array("L",'R'));
    $this->pdf->row(array('','‘s-Hertogenbosch, '.$productieDatum));
    $this->pdf->SetWidths(array($extraMarge,105,5,25));
    $this->pdf->SetAligns(array("L","L","L",'R'));
    $this->pdf->ln(8);
    $this->pdf->row(array('',"Nota : ".substr($this->waarden['rapportJaar'],2,2).'-'.sprintf("%03d",$this->waarden['factuurNummer'])));
		$this->pdf->ln(8);
    $this->pdf->row(array('','Betreft: beheervergoeding '.$periode.' '.$this->waarden['rapportJaar']));
	
    $this->pdf->SetWidths(array($extraMarge,105,5,25));

    $rapJul=db2jul($this->waarden['datumTot']);
		$this->pdf->ln();
    $this->pdf->row(array('',"Beheerd vermogen per ".(date("d",$rapJul))." ".vertaalTekst($this->__appvar["Maanden"][date("n",$rapJul)],$pdf->rapport_taal)." ".date("Y",$rapJul),"€",$this->formatGetal($this->waarden['rekenvermogen'],2)));
    $this->pdf->ln(16);
    $this->pdf->row(array('','Beheervergoeding '.$periode.' '.$this->waarden['rapportJaar'],'€',$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));
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
    $this->pdf->row(array('',"BTW no.: NL.8549.83.685.B01"));


?>