<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/25 15:37:41 $
File Versie					: $Revision: 1.2 $

$Log: RapportOIB_L91.php,v $
Revision 1.2  2020/07/25 15:37:41  rvv
*** empty log message ***

Revision 1.1  2020/07/01 16:22:28  rvv
*** empty log message ***




*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportOIB_L91
{
	function RapportOIB_L91($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Mandaat controle";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->checkPng=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAC0AAAA1CAMAAADiQZJeAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjNCMjBERDg2Q0U3NjExRUE4NzM3ODU3RUM2QkI2RUFGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjNCMjBERDg3Q0U3NjExRUE4NzM3ODU3RUM2QkI2RUFGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6M0IyMEREODRDRTc2MTFFQTg3Mzc4NTdFQzZCQjZFQUYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6M0IyMEREODVDRTc2MTFFQTg3Mzc4NTdFQzZCQjZFQUYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5O95YdAAAAMFBMVEX0+vSWz5JUsUzn9Oas2ajF5MLX7NV4wXJFqzxuvWc9pzRht1qEx3/+/v46pjH////UjrOAAAACYUlEQVR42rSWy6LrIAhFUfBRleT///YCxuZRk5PJzaADs4qIG3ZgnTwcXODZC5itUc3ewTsayC/Lkhu+oWPLiz7lE/mZZkYLXHKRX++Q+ZZmQPpo2Jywb+ApAk9oITG4ainICVlPanwLEWFsAYwxxhCIWi2dTUFDcGy+L9TkKAShUEK6Wr3vBzOWkLfdwsbbuq+1RaABar61BdzzZMntc3hdHLg8SNkwXgsKMbhUc9+iJKVLTwxhftuox3Itb3QmmIOH2kL0cl9Gh/Xv5z/Ty3saO13e0xXaE32QoNBF6IdMkOir8R77IW90XspwzvuWRhVFxncVNHhpcKCTajDTTB7WPe0rySB0U7rQ3u4EAxbdlR022oH9jqUku2kVGJKK2B2GhObgTFtpLNXFcMZ2hdeuVasj7wEUR03jDK96izIAJJ4f672TkrbXBYaPVhPWVBYfvqp3fmu8M7yiBmVYzyXsQ1APdC6oFGNpMn2CNtxBPYr/wHrIhYQGPWY8tCBVT9f5ahVFobnpvDs3+U9Th2qVkx2DTYrnntdEnM5BZknFB356otWPWWOSyechtI0/VSNsQvd0P384StZZfQvsr3LRKd7hbEKwzW1U60XJ5fFDHt4s0WqhslWFzeHDS9i7ys9xLfVoCzik5h1PbFl1M8wQxrHTr6S/IqsBLp6mF7DkTzzr1FrZD3j3S8Z0dlS5OTKXqnttDwfbLEgEKKISswt1c9obLx6O6iJGMvss528JuHwRdIfMyQ+n5YevgrOjJgJ+/OIQR22WT/GNfrQAE7cLlMRqw0Q3cGOocfrinwADADDacgmRnW/iAAAAAElFTkSuQmCC');
	}
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  
  function writeRapport()
  {
    global $__appvar;
    $query = "SELECT  Portefeuille,Risicoklasse, Client,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille = '" . $this->portefeuille . "' ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $portefeuilledata = $DB->nextRecord();
    
    
    $this->pdf->AddPage();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->setDrawColor($this->pdf->rapportLineColor[0],$this->pdf->rapportLineColor[1],$this->pdf->rapportLineColor[2]);
    $this->pdf->ln();
    
    $vermogensplanPercentages['blauw'] = array('vast' => '70% - 90%', 'zak' => '10% - 30%');
    $vermogensplanPercentages['groen'] = array('vast' => '50% - 70%', 'zak' => '30% - 50%');
    $vermogensplanPercentages['geel'] = array('vast' => '30% - 50%', 'zak' => '50% - 70%');
    $vermogensplanPercentages['rood'] = array('vast' => '10% - 30%', 'zak' => '70% - 90%');
    $vermogensplanPercentages['oranje'] = array('vast' => '0% - 10%', 'zak' => '90% - 100%');
    
 
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(0, 160));
    $this->pdf->SetAligns(array('L', 'L'));
    $this->pdf->row(array("", "U heeft gekozen voor vermogensplan " . $portefeuilledata['Risicoklasse'] . "."));
    $this->pdf->row(array("", "De bandbreedte voor de vastrentende waarden is " . $vermogensplanPercentages[$portefeuilledata['Risicoklasse']]['vast'] . ","));
    $this->pdf->row(array("", "voor de zakelijke waarden is de bandbreedte is " . $vermogensplanPercentages[$portefeuilledata['Risicoklasse']]['zak']));
    
/*
    // haal totaalwaarde op om % te berekenen
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal " .
      "FROM TijdelijkeRapportage WHERE " .
      " rapportageDatum ='" . $this->rapportageDatum . "' AND " .
      " portefeuille = '" . $this->portefeuille . "' "
      . $__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query, __FILE__, __LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
*/
    $zorgplicht=new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($portefeuilledata,$this->rapportageDatum );
    //listarray($zpwaarde);
    $this->pdf->SetWidths(array(28,12,18,12, 5, 28,15,15));
    $this->pdf->SetAligns(array('L', 'R', 'R', 'R', 'C', 'L','R'));
    $this->pdf->ln(20);
    $ystart=$this->pdf->getY();
    $this->pdf->CellBorders = array('T','T','T','T','','T','T','T');
    $this->pdf->row(array('','','','','','','',''));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders =array();
    $this->pdf->row(array('Vermogensverdeling','% Min','% Neutraal','% Max','','Mandaat controle','% actueel',''));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders = array('U','U','U','U','','U','U','U');
    $this->pdf->row(array('','','','','','','',''));
    $this->pdf->CellBorders =array();
    $this->pdf->ln(2);
    foreach($zpwaarde['conclusieDetail'] as $categorie=>$details)
    {
      $this->pdf->row(array($categorie,$this->formatGetal($details['minimum'],0).'%',$this->formatGetal($details['norm'],0).'%',$this->formatGetal($details['maximum'],0).'%',
        '',
        $categorie,$this->formatGetal($details['percentage'],0).'%'));
      if($zpwaarde['voldoet'] =='Ja')
        $this->pdf->memImage($this->checkPng,$this->pdf->getX()+123,$this->pdf->getY()-4,4);
      $this->pdf->ln(2);
    }
    $this->pdf->CellBorders = array('T','T','T','T','','T','T','T');
    $this->pdf->row(array('','','','','','','',''));
    
    
    $this->pdf->setY($ystart);
    $this->pdf->SetWidths(array(137,70,15));
    $this->pdf->CellBorders = array('','T','T');
    $this->pdf->SetAligns(array('L', 'L', 'R'));
    $this->pdf->row(array('','',''));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders =array();
    $this->pdf->row(array('','Risicomaatstaven',''));
    $this->pdf->ln(-2);
    $this->pdf->CellBorders = array('','U','U');
    $this->pdf->row(array('','',''));
    $this->pdf->CellBorders =array();
    $this->pdf->ln(2);
    $risicomaatstaven=array(array('','Gemiddeld verwacht totaal rendement per jaar','%'),
      array('','Standaarddeviatie (ex-ante risico)','%'),
      array('','Standaarddeviatie van de portefeuille (AFM normen)','%'),
      array('','Maximaal verwacht verlies in enig jaar (AFM normen)','%'),
      array('','Maximaal verwachte winst in enig jaar (AFM normen)','%'));
    foreach($risicomaatstaven as $regel)
    {
      $this->pdf->row($regel);
      
    }
    $this->pdf->CellBorders = array('','T','T','T');
    $this->pdf->row(array('','','',''));
  
    $this->pdf->setY(130);
    $this->pdf->row(array('Historisch risico en rendement (10 jaars periode)'));
  
    unset($this->pdf->CellBorders);
  }

}
?>