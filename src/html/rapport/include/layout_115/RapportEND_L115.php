<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/08 16:04:22 $
File Versie					: $Revision: 1.2 $

$Log: RapportEND.php,v $
Revision 1.2  2019/06/08 16:04:22  rvv
*** empty log message ***

Revision 1.1  2019/04/27 18:32:35  rvv
*** empty log message ***

Revision 1.3  2019/04/24 15:23:46  rvv
*** empty log message ***

Revision 1.2  2019/04/24 14:42:25  rvv
*** empty log message ***

Revision 1.1  2019/04/10 15:47:20  rvv
*** empty log message ***

Revision 1.4  2014/02/22 18:43:38  rvv
*** empty log message ***

Revision 1.3  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.2  2012/10/17 09:16:53  rvv
*** empty log message ***

Revision 1.1  2012/10/07 14:57:18  rvv
*** empty log message ***


*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportEND_L115
{
  function RapportEND_L115($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "END";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    
    $this->pdf->rapport_titel = 'Disclaimer';
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatumVanafJul=db2jul($this->rapportageDatumVanaf);
    $this->rapportageDatum = $rapportageDatum;
    $this->rapportageDatumJul=db2jul($this->rapportageDatum);
    $this->pdf->extraPage =0;
    $this->DB = new DB();
    
    $this->rapportMaand 	= date("n",$this->rapportageDatumJul);
    $this->rapportDag 		= date("d",$this->rapportageDatumJul);
    $this->rapportJaar 		= date("Y",$this->rapportageDatumJul);
    
    $this->pdf->brief_font = $this->pdf->rapport_font;
    
  }
  
  function pageCheck($extraMarge,$ystart)
  {
    if($this->pdf->getY()>$this->pdf->PageBreakTrigger-5)
    {
      if($extraMarge==0)
      {
        $extraMarge = 140;
        $this->pdf->setY($ystart);
      }
      else
      {
        $extraMarge=0;
        $this->pdf->addPage();
      }
      $this->pdf->SetWidths(array($extraMarge,40,90));
    }
    return $extraMarge;
  }
  
  
  function writeRapport()
  {
    global $__appvar;
    
    $this->pdf->addPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    
    $this->pdf->SetAligns(array('L','L','L','L'));
    $this->pdf->SetWidths(array(5,100));
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Disclaimer'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->ln();
    $body='Forza Asset Management streeft ernaar om accurate en tijdige informatie te verstrekken van betrouwbare bronnen.
    
Forza Asset Management kan niet instaan voor de nauwkeurigheid en volledigheid van de informatie. De (procentuele) performance berekening kan afwijken met die van uw bank.

Het overzicht van uw beleggingsportefeuille is strikt persoonlijk en vertrouwelijk. Dit overzicht is zorgvuldig samengesteld en vormt een actuele weergave van uw investeringen.

Aan de inhoud van dit rapport kunnen geen rechten worden ontleend.

De posities worden getoond tegen de laatst bekende koersen (Forza Asset Management) op de laatste handelsdag van de verslagperiode. Vooral voor niet-beursgenoteerde effecten is de weergegeven koers misschien niet altijd de meest recente koers.

Indien u een omissie constateert of dat deze rapportage niet volledig is, verzoeken wij u vriendelijk contact op te nemen met uw accountmanager.

Dit rapport is niet bedoeld voor fiscale doeleinden.';
  
    $this->pdf->row(array('',$body));
    
    
  }
}
?>
