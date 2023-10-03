<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/05/15 17:15:00 $
File Versie					: $Revision: 1.6 $

$Log: RapportEND_L42.php,v $
Revision 1.6  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.5  2016/04/30 15:33:27  rvv
*** empty log message ***

Revision 1.4  2015/02/15 10:36:54  rvv
*** empty log message ***

Revision 1.3  2015/02/07 20:37:51  rvv
*** empty log message ***

Revision 1.2  2015/01/17 18:32:01  rvv
*** empty log message ***

Revision 1.1  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.1  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.3  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.2  2014/04/23 16:18:44  rvv
*** empty log message ***

Revision 1.1  2014/01/22 17:01:30  rvv
*** empty log message ***

Revision 1.9  2014/01/18 17:27:23  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportEND_L42
{
	function RapportEND_L42($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Notities";
  // 	
	}

	function writeRapport()
	{
	  global $__appvar;

    unset($this->pdf->templateVars['NotitiePaginas']);
  
    $uitsluiten=array('eMail','portaal','eDossier');
    if(!in_array($this->pdf->selectData['type'],$uitsluiten))
    {
      for($i=0;$i<5;$i++)
      {
       if ((count($this->pdf->pages)+1)%4)
       {
  	 	   $this->pdf->AddPage($this->pdf->CurOrientation);
         if(!isset($this->pdf->templateVars['NotitiePaginas']))
           $this->pdf->templateVars['NotitiePaginas']=$this->pdf->page;
         $this->maakNotities();
       }
       else
       {
        break;
       }
      }
    }
    
    $this->pdf->rapport_titel = "Disclaimer";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ENDPaginas']=$this->pdf->page;
    
    $this->pdf->SetWidths(array((297-($this->pdf->marge*2))));
		$this->pdf->SetAligns(array("L"));
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
		$this->pdf->row(array('Disclaimer'));
    $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->row(array('Aan de betrouwbaarheid van deze vermogensrapportage heeft Ritzer en Rouw alle zorg besteed die redelijkerwijs van haar verwacht mag worden. Voor de totstandkoming wordt gebruik gemaakt van externe koersinformatiesystemen. De informatie is derhalve afkomstig van een onafhankelijke bron. Omdat prijzen en koersen in deze systemen indicatief zijn, kan voor handelsdoeleinden niet op deze prijzen en koersen worden vertrouwd. Een voorbehoud wordt bovendien gemaakt voor druk- en zetfouten.'));
    $this->pdf->ln();
    $this->pdf->row(array('Fiscale koersen kunnen afwijken van de koersen zoals bekend op het moment van publicatie van deze rapportage. Deze rapportage is dus niet opgesteld voor fiscale doeleinden. Ritzer en Rouw is haar cliënt of zijn/haar fiscaal adviseur graag van dienst bij het aanleveren van fiscaal relevante gegevens in verband met het opstellen van de fiscale aangifte.'));
    $this->pdf->ln();
    $this->pdf->row(array('Effecten zijn gewaardeerd op basis van de slotkoersen van de laatste handelsdag van de periode waar de rapportage betrekking op heeft, waarbij de slotkoers wordt gevormd door de meest recent tot stand gekomen beurskoers die op die laatste handelsdag bekend is. Deze slotkoers hoeft niet tot stand te zijn gekomen op deze laatste handelsdag. De meest recent tot stand gekomen beurskoers kan immers (ruime) tijd voor de laatste handelsdag tot stand zijn gekomen, terwijl in de periode tussen de totstandkoming van deze laatst bekende beurskoers en de laatste handelsdag geen beurshandel heeft plaatsgevonden. De waardering in de markt kan in dat geval op basis van actuele bied- en laatprijzen aanzienlijk afwijken van de getoonde slotkoers.'));
    $this->pdf->ln();
    $this->pdf->row(array('Indien niet-beursgenoteerde beleggingen (waaronder onderhandse leningen) deel uitmaken van deze rapportage, is de waardering hiervan gebaseerd op een in overleg tussen Ritzer en Rouw en haar cliënt tot stand gekomen methodiek. Door gebrek aan liquiditeit (verhandelbaarheid) kan de actuele waarde van een dergelijke belegging afwijken van de in deze rapportage opgenomen waarde.'));
    $this->pdf->ln();
    $this->pdf->row(array('Opgelopen rente op vastrentende waarden wordt berekend vanaf de laatste coupondatum, dan wel vanaf de aankoopdatum, tot de laatste dag van de periode waarop deze rapportage betrekking heeft. Vreemde valuta (niet-Euro) zijn gewaardeerd tegen door Telekurs vastgestelde koersen op de laatste handelsdag van de periode waarop deze rapportage betrekking heeft. Genoemde kostprijzen zijn bruto kostprijzen waarin geen rekening is gehouden met aankoopkosten.'));

	}
  
    function maakNotities()
  {
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
    $rapportYstart=30;
    $this->pdf->setY($rapportYstart); 
  	$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(150,4,'Ruimte voor aantekeningen', 0, "L");
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,0),'dash'=>0));
    $stappen=25;
    $yStart=$rapportYstart+5;
    $yStop=210-15;
    $w=297-$this->pdf->marge;
    $stap=($yStop-$yStart)/$stappen;
    for($i=0;$i<=$stappen;$i++)
    {
      $this->pdf->Line($this->pdf->marge,$yStart+$i*$stap,$w,$yStart+$i*$stap);
    }
  }
}
?>