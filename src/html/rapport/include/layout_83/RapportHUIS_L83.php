<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/06/26 14:15:28 $
File Versie					: $Revision: 1.6 $

$Log: RapportHUIS_L83.php,v $
Revision 1.6  2019/06/26 14:15:28  rvv
*** empty log message ***

Revision 1.5  2019/05/22 07:35:28  rvv
*** empty log message ***

Revision 1.4  2019/05/18 16:28:56  rvv
*** empty log message ***

Revision 1.3  2019/05/15 15:31:34  rvv
*** empty log message ***

Revision 1.2  2019/05/04 18:23:53  rvv
*** empty log message ***

Revision 1.1  2019/03/02 18:23:01  rvv
*** empty log message ***

Revision 1.3  2016/11/30 16:48:42  rvv
*** empty log message ***

Revision 1.2  2016/11/16 16:51:17  rvv
*** empty log message ***

Revision 1.1  2016/11/14 08:12:30  rvv
*** empty log message ***

Revision 1.41  2014/12/21 13:24:42  rvv
*** empty log message ***

Revision 1.40  2012/03/14 17:29:35  rvv
*** empty log message ***

Revision 1.39  2012/01/15 11:03:37  rvv
*** empty log message ***

Revision 1.38  2011/12/24 16:36:57  rvv
*** empty log message ***

Revision 1.37  2011/12/24 16:34:55  rvv
*** empty log message ***

Revision 1.36  2011/06/25 16:51:45  rvv
*** empty log message ***

Revision 1.35  2011/05/18 16:51:08  rvv
*** empty log message ***

Revision 1.34  2010/09/15 16:27:45  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportHUIS_L83
{
	function RapportHUIS_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		if($this->pdf->rapport_HSE_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_HSE_titel;
		else
			$this->pdf->rapport_titel = "Algemeen";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');
    $this->tweedeMarge=130;
    $this->crmData=array();
  }

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{
		global $__appvar;
		// rapport settings
		$this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type .'Paginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type .'Paginas']=$this->pdf->rapport_titel;
		// haal totaalwaarde op om % te berekenen
    
    
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $zorg=new zorgplichtControle($this->pdf->portefeuilledata);
    $zorgPlichtResultaat=$zorg->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
    $zorgPlichtResultaat['zorgMetingReden']=str_replace("\n"," ",$zorgPlichtResultaat['zorgMetingReden']);
    
    $this->getCRMdata();
    
    $this->pdf->setY(30);
    $this->toonPositie($zorgPlichtResultaat);
    $this->pdf->setY(70);
    $this->ToonInkomstenUitgaven();
    $this->pdf->setY(120);
    $this->toonGeschiktheid();
    
    
    $this->pdf->setY(30);
    $this->toonPortefeuilleprofiel($zorgPlichtResultaat);
    $this->pdf->setY(70);
    $this->toonZorgplicht($zorgPlichtResultaat);
    $this->pdf->setY(120);
    $this->toonScenario();

	}

	function ToonInkomstenUitgaven()
  {
    $rowData=array();
    $velden=array('InkomstenArbeid'=>'Inkomsten uit arbeid','InkomstenPensioen'=>'Inkomsten uit pensioen','InkomstenAOW'=>'Inkomsten uit AOW','InkomstenStamrecht'=>'Inkomsten uit stamrecht','InkomstenLijfrente'=>'Inkomsten uit lijfrente','InkomstenManagementfee'=>'Inkomsten uit management fee','UitgavenConsumptief'=>'Uitgaven (consumptief)','UitgavenEenmalig'=>'Uitgaven (éénmalig)');
    foreach($velden as $veld=>$omschrijving)
    {
      if($this->crmData[$veld]<>0)
        $rowData[]=array($omschrijving,'€ '.$this->formatGetal($this->crmData[$veld], 0));
  
    }
    if(count($rowData)>0)
    {
      $widths = array(60, 5 + 25);
      $this->pdf->SetWidths(array(60));
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
      $this->pdf->row(array('Inkomsten en uitgaven per jaar'));
      $this->pdf->SetWidths($widths);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      foreach ($rowData as $row)
      {
       $this->pdf->row($row);
      }
    }
	}
	function getCRMdata()
  {
    $gebruikteCrmVelden=array('VermogensopstellingDatum','BalansDeelnemingen','BalansOnroerendGoed','Balans1eEigenWoning','BalansEffecten','BalansActivaToel','BalansActivaOverig','BalansLiquiditeiten','BalansTotaalActiva',
      'BalansEigenVermogen','BalansSchulden','BalansHypotheek','BalansBelastingClaim','BalansPassivaToel','BalansPassivaOverig','BalansTotaalPassiva',
      'beleggingsDoelstelling','beleggingsHorizon','Risicobereidheid',
      'OPALDatumRapport','OPALVerwachtRendement','OPALNegatiefRendement','OPALEindVerwacht','OPALEindGoed','OPALEindSlecht','OPALKansVermogensdoelstelling',
      'OPALKansInkomensdoelstelling','PortefeuilleUitzonderingen','InkomstenUitgavenDatum','InkomstenUitgavenConclusie',
      'InkomstenArbeid','InkomstenPensioen','InkomstenAOW','InkomstenStamrecht','InkomstenLijfrente','InkomstenManagementfee','UitgavenConsumptief','UitgavenEenmalig');
    
    $query = "DESC CRM_naw";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $crmVelden=array();
    while($data=$DB->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
    $nawSelect='';
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $nawSelect.=",CRM_naw.$veld ";
      }
    }
  
    $query="SELECT Portefeuilles.risicoklasse,laatstePortefeuilleWaarde.laatsteWaarde $nawSelect FROM Portefeuilles
 LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.Portefeuille
 LEFT JOIN laatstePortefeuilleWaarde ON Portefeuilles.Portefeuille=laatstePortefeuilleWaarde.Portefeuille
 WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $this->crmData=$DB->lookupRecord();

  }
  
  function toonPositie()
  {
  
  
    /*
  $gebruikteCrmVelden=array('VermogensopstellingDatum','BalansDeelnemingen','BalansOnroerendGoed','Balans1eEigenWoning','BalansEffecten','BalansLiquiditeiten','BalansTotaalActiva',
      'BalansEigenVermogen','BalansSchulden','BalansHypotheek','BalansBelastingClaim','BalansTotaalPassiva',
      'beleggingsDoelstelling','beleggingsHorizon','Risicobereidheid',
      'OPALVerwachtRendement','OPALNegatiefRendement','OPALEindVerwacht','OPALEindGoed','OPALEindSlecht','OPALKansVermogensdoelstelling',
      'OPALKansInkomensdoelstelling','PortefeuilleUitzonderingen','InkomstenUitgavenDatum','InkomstenUitgavenConclusie');
  CRM_naw.beleggingsDoelstelling

CRM_naw.beleggingsHorizon
CRM_naw.Risicobereidheid
Portefeuilles.risicoklasse
3. Hier de onderstaande velden opnemen:

CRM_naw.OPALVerwachtRendement
CRM_naw.OPALNegatiefRendement
CRM_naw.OPALEindVerwacht
CRM_naw.OPALEindGoed
CRM_naw.OPALEindSlecht
laatstePortefeuilleWaarde.laatsteWaarde
CRM_naw.OPALKansVermogensdoelstelling
CRM_naw.OPALKansInkomensdoelstelling
4. Hier de inhoud van het veld CRM_naw.PortefeuilleUitzonderingen opnemen

5. Hier de datum uit het veld CRM_naw.InkomstenUitgavenDatum halen en de tekst daaronder uit CRM_naw.InkomstenUitgavenConclusie
    
        listarray($this->crmData);
*/
  

    $crmObject=new Naw();
    $activa=array('BalansDeelnemingen','BalansOnroerendGoed','Balans1eEigenWoning','BalansEffecten','BalansLiquiditeiten','BalansActivaOverig','BalansTotaalActiva');
    $activaTonen=array();
    $passiva=array('BalansEigenVermogen','BalansSchulden','BalansHypotheek','BalansBelastingClaim','BalansPassivaOverig','BalansTotaalPassiva');
    $passivaTonen=array();
    $omschrijvingen=array();
    foreach($crmObject->data['fields'] as $key=>$dataFields)
    {
      foreach($activa as $veld)
      {
        if(strtolower($veld)==strtolower($key))
        {
          $omschrijvingen[$key] = $dataFields['description'];
          if($this->crmData[$veld]<>0 && $veld <> 'BalansTotaalActiva')
            $activaTonen[]=$veld;
        }
      }
      foreach($passiva as $veld)
      {
        if(strtolower($veld)==strtolower($key))
        {
          $omschrijvingen[$key] = $dataFields['description'];
          if($this->crmData[$veld]<>0 && $veld <> 'BalansTotaalPassiva')
            $passivaTonen[]=$veld;
        }
      }
    }
    if($this->crmData['BalansActivaToel']<>'')
    {
      $omschrijvingen['BalansActivaOverig'] = $this->crmData['BalansActivaToel'];
    }
    if($this->crmData['BalansPassivaToel']<>'')
    {
      $omschrijvingen['BalansPassivaOverig'] = $this->crmData['BalansPassivaToel'];
    }

    $max=max(count($activaTonen),count($passivaTonen));
    $ballansRows=array();
    for($i=0;$i<=$max;$i++)
    {
      if($this->crmData[$activaTonen[$i]]<>0)
      {
        $activaBedrag = '€ '.$this->formatGetal($this->crmData[$activaTonen[$i]], 0);
      }
      else
      {
        $activaBedrag = '';
      }
      if($this->crmData[$passivaTonen[$i]]<>0)
      {
        $passivaBedrag = '€ '.$this->formatGetal($this->crmData[$passivaTonen[$i]], 0);
        
      }
      else
      {
        $passivaBedrag = '';
      }
      $ballansRows[]=array($omschrijvingen[$activaTonen[$i]],$activaBedrag,$omschrijvingen[$passivaTonen[$i]],$passivaBedrag);
    }

    
    $beginY=$this->pdf->getY();
    $widths=array(100);
    $this->pdf->SetWidths(array(200));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Uw financiële positie per '.date('d-m-Y',db2jul($this->crmData['VermogensopstellingDatum']))));
  
    $this->pdf->SetWidths(array(35,4+15,35,4+15));
    $this->pdf->SetAligns(array('L','R','L','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize-2);
  
    $this->pdf->CellBorders = array('U','U','U','U');
    $this->pdf->row(array('Activa','','Passiva',''));
    $this->pdf->CellBorders = array('','R','','');
    
    foreach($ballansRows as $row)
      $this->pdf->row($row);
  
    $this->pdf->CellBorders = array('',array('T','R'),'','T');
    $this->pdf->row(array($omschrijvingen['BalansTotaalActiva'],'€ '.$this->formatGetal($this->crmData['BalansTotaalActiva'],0 ),$omschrijvingen['BalansTotaalPassiva'],'€ '.$this->formatGetal($this->crmData['BalansTotaalPassiva'],0 )));
    unset($this->pdf->CellBorders);
    
    $this->pdf->setY($beginY+40);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    //$this->pdf->row(array('Zoals per '.date('d-m-Y',db2jul($this->crmData['VermogensopstellingDatum'])).' bekend bij Van Lawick & Co.:'));
    $this->pdf->ln();
    //$this->pdf->row(array('Uw inkomsten en uitgaven per '.date('d-m-Y',db2jul($this->crmData['InkomstenUitgavenDatum'])).':'));
    //$this->pdf->row(array($this->crmData['InkomstenUitgavenConclusie'])); //

  }
  
  function toonGeschiktheid()
  {
   // $beginY=$this->pdf->getY();
    $widths=array(109);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetAligns(array('L','L'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('Geschiktheidsverklaring'));
  //  $this->pdf->setY($beginY+40);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetAligns(array('J'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(3);
    $this->pdf->row(array('Van Lawick beoordeelt doorlopend of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel en dat beleggen geschikt voor u is. Uw beleggingsprofiel is onder andere gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheden om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie hebben wij vastgelegd in onze systemen.

Wij zijn van mening dat onze vermogensbeheerdienst en de beleggingsportefeuille die wij voor u beheren (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn en u een gerede kans bieden op het behalen van uw beleggingsdoelstelling. Tevens achten wij de beleggingsportefeuille geschikt gegeven uw kennis en ervaring met beleggen, zoals ook vastgelegd in de vermogensbeheer overeenkomst.

Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk.'));
    $this->pdf->ln();

  }
  
	
	function toonPortefeuilleprofiel()
  {
    $beginY=$this->pdf->getY();
    $widths=array($this->tweedeMarge,60,130);
    $this->pdf->SetWidths(array($this->tweedeMarge,60));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Portefeuilleprofiel'));
   // $this->pdf->setY($beginY+1);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Beleggingsdoelstelling:',$this->crmData['beleggingsDoelstelling']));
    $this->pdf->row(array('','Beleggingshorizon:',$this->crmData['beleggingsHorizon']));
    $this->pdf->row(array('','Risicobereidheid:',$this->crmData['Risicobereidheid']));
    $this->pdf->row(array('','Risicoprofiel:',$this->crmData['risicoklasse']));
  }
  
  
  function toonZorgplicht($zpwaarde)
  {
    $db=new DB();
    $query="SELECT
Zorgplichtcategorien.Vermogensbeheerder,
Zorgplichtcategorien.Zorgplicht,
Zorgplichtcategorien.Omschrijving
FROM Zorgplichtcategorien
WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $db->query();
    $omschrijvingen=array();
    while($data=$db->nextRecord())
    {
      $omschrijvingen[$data['Zorgplicht']]=$data['Omschrijving'];
    }
    
    
    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;
    
    krsort($tmp);

    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $beginY=$this->pdf->getY();
    
    $widths=array($this->tweedeMarge,40,15,15,15,33,20);
    $this->pdf->SetWidths(array($this->tweedeMarge,60));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','Afgesproken bandbreedte'));
    $this->pdf->setY($beginY+1);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','',"\nmin.","\nnorm","\nmax.","weging\nrapportagedatum","\nControle"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    
    foreach($zpwaarde['conclusieDetail'] as $cat=>$details)
    {
     
      $min=$this->formatGetal($details['minimum'],0)."%";
      $max=$this->formatGetal($details['maximum'],0)."%";
      
      if($tmp[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,array_sum($widths)-6,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,array_sum($widths)-6,$this->pdf->getY(),3.9,3.9);
      
      $this->pdf->row(array('',$omschrijvingen[$cat],$min,$this->formatGetal($details['norm'],0)."%",$max,$this->formatGetal($details['percentage'],1)."%"));
    }
    if(trim($this->crmData['PortefeuilleUitzonderingen'])<>'')
    {
      $this->pdf->ln();
      $this->pdf->SetWidths(array($this->tweedeMarge,130));
      $this->pdf->row(array('','Uitzonderingen:'));
      $this->pdf->row(array('',$this->crmData['PortefeuilleUitzonderingen'])); //
    }
    //$this->pdf->Rect($this->pdf->marge,$beginY,140,count($zpwaarde['conclusieDetail'])*4+4);
  }
  
  function toonScenario()
  {
    $beginY=$this->pdf->getY();
    $widths=array($this->tweedeMarge,120,25);
    $this->pdf->SetWidths(array($this->tweedeMarge,60));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','OPAL Scenario analyse per '.date('d-m-Y',db2jul($this->crmData['OPALDatumRapport']))));
    // $this->pdf->setY($beginY+1);
    $this->pdf->SetWidths($widths);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    if($this->crmData['OPALVerwachtRendement']<>0)
      $this->pdf->row(array('','Uw verwacht beleggingsrendement bij bovenstaand portefeuilleprofiel:',$this->formatGetal($this->crmData['OPALVerwachtRendement'],1).'% per jaar'));
    if($this->crmData['OPALNegatiefRendement']<>0)
      $this->pdf->row(array('','Er is een kans van 2,5% dat het verlies in één jaar tijd meer is dan:',$this->formatGetal($this->crmData['OPALNegatiefRendement'],1).'%'));
    $this->pdf->ln();
    $this->pdf->row(array('','Eindwaarde van de portefeuille einde beleggingshorizon:'));
    if($this->crmData['OPALEindVerwacht']<>0)
      $this->pdf->row(array('','Verwachte markt','€ '.$this->formatGetal($this->crmData['OPALEindVerwacht'],0)));
    if($this->crmData['OPALEindGoed']<>0)
      $this->pdf->row(array('','Goede markt','€ '.$this->formatGetal($this->crmData['OPALEindGoed'],0)));
    if($this->crmData['OPALEindSlecht']<>0)
      $this->pdf->row(array('','Slechte markt','€ '.$this->formatGetal($this->crmData['OPALEindSlecht'],0)));
    if($this->crmData['laatsteWaarde']<>0)
      $this->pdf->row(array('','Huidige waarde','€ '.$this->formatGetal($this->crmData['laatsteWaarde'],0)));
    $this->pdf->ln();
    if($this->crmData['OPALKansVermogensdoelstelling']<>0)
      $this->pdf->row(array('','Kans op het realiseren van de vermogensdoelstelling:',$this->formatGetal($this->crmData['OPALKansVermogensdoelstelling'],0).'%'));
    if($this->crmData['OPALKansInkomensdoelstelling']<>0)
      $this->pdf->row(array('','Kans op het realiseren inkomensdoelstelling:',$this->formatGetal($this->crmData['OPALKansInkomensdoelstelling'],0).'%'));
    $this->pdf->ln();
    $this->pdf->row(array('','Bron: OPAL , Ortec Finance B.V'));
  }
}
?>