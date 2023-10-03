<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/23 16:39:43 $
File Versie					: $Revision: 1.9 $

$Log: RapportKERNZ_L86.php,v $
Revision 1.9  2020/05/23 16:39:43  rvv
*** empty log message ***

Revision 1.8  2020/05/21 07:50:20  rvv
*** empty log message ***

Revision 1.7  2020/05/20 17:15:50  rvv
*** empty log message ***

Revision 1.6  2020/05/16 16:24:10  rvv
*** empty log message ***

Revision 1.5  2020/01/29 17:37:38  rvv
*** empty log message ***

Revision 1.4  2020/01/25 16:37:33  rvv
*** empty log message ***

Revision 1.3  2020/01/08 14:32:11  rvv
*** empty log message ***

Revision 1.2  2019/10/19 16:46:32  rvv
*** empty log message ***

Revision 1.1  2019/09/28 17:21:00  rvv
*** empty log message ***

Revision 1.1  2019/08/31 12:20:08  rvv
*** empty log message ***




*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_86/RapportHUIS_L86.php");
include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/include/layout_86/ATTberekening_L86.php");
class RapportKERNZ_L86
{
	function RapportKERNZ_L86($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";

	}
	
	function getCRMSelect()
  {
    $gebruikteCrmVelden=array('ToelVermogen','ToelRisico','ToelKosten');
  
    $db = new DB();
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden=array();
    while($data=$db->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
  
    $nawSelect='';
    $nietgevonden=array();
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $nawSelect.=",CRM_naw.$veld ";
      }
      else
      {
        $nietgevonden[]=$veld;
      }
    }
    return $nawSelect;
  }
	
	function getCRMdata($portefeuille,$nawSelect)
  {
   
    $db = new DB();
    
    $query="
      SELECT
        Portefeuilles.Risicoklasse,
        Portefeuilles.Vermogensbeheerder,
        laatstePortefeuilleWaarde.laatsteWaarde
        $nawSelect
      FROM
        CRM_naw
      JOIN Portefeuilles on
        CRM_naw.portefeuille=Portefeuilles.Portefeuille
      LEFT JOIN laatstePortefeuilleWaarde on
        CRM_naw.portefeuille=laatstePortefeuilleWaarde.Portefeuille
      WHERE
        CRM_naw.portefeuille='".$portefeuille."'";

    $db->SQL($query);
    $db->Query();
    $data=$db->nextRecord();
    return $data;
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function addKop($txt)
  {
    $this->pdf->setFillColor($this->kopKleur[0],$this->kopKleur[1],$this->kopKleur[2]);
    $this->pdf->rect(0,$this->pdf->getY(),210,8,'F');
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->setX(8);
    $this->pdf->cell(210,8,$txt,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln(8);
  }
  

  

	function writeRapport()
	{
		global $__appvar;
  
    $crmSelect=$this->getCRMSelect();
		$crm=new NAW();
    $crmVelden=array();
		foreach($crm->data['fields'] as $veld=>$veldData)
    {
      $crmVelden[$veld]=$veldData['description'];
    }

    //$this->huis=new RapportHUIS_consolidatie_L86($this->pdf,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
   // $this->huis->pdf->rapport_type = "KERNZ";
  //  $this->huis->writeRapport();
    foreach($this->pdf->portefeuilles as $portefeuille)
    {

      
      vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum,0,'EUR',$this->rapportageDatum) ,$portefeuille,$this->rapportageDatum);
      $this->huis=new RapportHUIS_L86($this->pdf,$portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
      $this->huis->pdf->rapport_type = "KERNZ";
      $this->huis->viaKERNZ=true;
      
  /*
      $this->pdf->addPage('P');
      $data=$this->getCRMdata($portefeuille,$crmSelect);
      $this->pdf->setWidths(array(180,20,20));

      $this->pdf->setXY($this->pdf->marge,60);
      $this->pdf->row(array($crmVelden['ToelVermogen']<>''?$crmVelden['ToelVermogen']:'ToelVermogen'));
      $this->pdf->row(array($data['ToelVermogen']));
  
      $this->pdf->setXY($this->pdf->marge,140);
      $this->pdf->row(array($crmVelden['ToelRisico']<>''?$crmVelden['ToelRisico']:'ToelRisico'));
      $this->pdf->row(array($data['ToelRisico']));
  
      $this->pdf->setXY($this->pdf->marge,220);
      $this->pdf->row(array($crmVelden['ToelKosten']<>''?$crmVelden['ToelKosten']:'ToelKosten'));
      $this->pdf->row(array($data['ToelKosten']));
      $this->pdf->setWidths(array(78, 20, 20));
  */
      $this->huis->writeRapport();
    }

	}
	
  
}



class RapportHUIS_consolidatie_L86
{
  function RapportHUIS_consolidatie_L86($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "HUIS";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->portefeuille = $portefeuille;
    
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
    $this->pdf->rapport_rendementText="Rendement over verslagperiode";
    $this->kopKleur=array(238,202,96);
    $this->pdf->setDrawColor(0,0,0);
    $this->att=new ATTberekening_L86($this);
    if (is_array($this->pdf->portefeuilles))
    {
      $this->portefeuilles = $this->pdf->portefeuilles;
      $this->portefeuilles[]=$portefeuille;
    }
    else
    {
      $this->portefeuilles = array($portefeuille);
    }

    $this->imgConclusie=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAdUAAAHjCAMAAABCRHV1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjlBNTlDNUY3OUIzNDExRUE4MkRBRjE5M0VDOTZBOTdEIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjlBNTlDNUY4OUIzNDExRUE4MkRBRjE5M0VDOTZBOTdEIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6OUE1OUM1RjU5QjM0MTFFQTgyREFGMTkzRUM5NkE5N0QiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6OUE1OUM1RjY5QjM0MTFFQTgyREFGMTkzRUM5NkE5N0QiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz68syvQAAAAYFBMVEXLy8vD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');
    $this->imgActie=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAdUAAAHjCAMAAABCRHV1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkMzRDBCQjIxOUIzNDExRUFBRTk1RjMyMTlCNUVFRjFEIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkMzRDBCQjIyOUIzNDExRUFBRTk1RjMyMTlCNUVFRjFEIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QzNEMEJCMUY5QjM0MTFFQUFFOTVGMzIxOUI1RUVGMUQiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QzNEMEJCMjA5QjM0MTFFQUFFOTVGMzIxOUI1RUVGMUQiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5rWCjHAAAAYFBMVEWbm5t2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');

  }
  
  function addGrijsVlak()
  {
    $this->pdf->setFillColor(217,217,217);
    
    $this->pdf->rect(100,90,110,297-90,'F');
  }

  function addGrijsVlak2()
  {
    $this->pdf->setFillColor(217,217,217);
    $this->pdf->rect(100,55,110,297-130,'F');
  }

  function addGrijs($txt)
  {
    $y=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setX(100);
    $this->pdf->MultiCell(110,5,$txt,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->setXY(0,$y+30);
  }
  
  
  function addKop($txt)
  {
    $this->pdf->setFillColor($this->kopKleur[0],$this->kopKleur[1],$this->kopKleur[2]);
    $this->pdf->rect(0,$this->pdf->getY(),210,8,'F');
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->setX(8);
    $this->pdf->cell(210,8,$txt,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln(8);
  }
  
  function getCRMSelect()
  {
    $gebruikteCrmVelden=array('ToelVermogen','ToelRisico','ToelKosten');
    
    $db = new DB();
    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden=array();
    while($data=$db->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
    
    $nawSelect='';
    $nietgevonden=array();
    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $nawSelect.=",CRM_naw.$veld ";
      }
      else
      {
        $nietgevonden[]=$veld;
      }
    }
    return $nawSelect;
  }
  
  function getCRMdata($portefeuille,$nawSelect)
  {
    
    $db = new DB();
    
    $query="
      SELECT
        Portefeuilles.Risicoklasse,
        Portefeuilles.Vermogensbeheerder,
        laatstePortefeuilleWaarde.laatsteWaarde
        $nawSelect
      FROM
        CRM_naw
      JOIN Portefeuilles on
        CRM_naw.portefeuille=Portefeuilles.Portefeuille
      LEFT JOIN laatstePortefeuilleWaarde on
        CRM_naw.portefeuille=laatstePortefeuilleWaarde.Portefeuille
      WHERE
        CRM_naw.portefeuille='".$portefeuille."'";
    
    $db->SQL($query);
    $db->Query();
    $data=$db->nextRecord();
    return $data;
  }
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function writeRapport()
  {
    global $__appvar;
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->AddPage('P');
    $crmSelect=$this->getCRMSelect();
    $this->crmData=$this->getCRMdata($this->portefeuille,$crmSelect);
  
    $this->addGrijsVlak();
    $YBegin=38;
    $this->pdf->setY($YBegin);
    $this->addOIB();
    $YBegin+=90;
    $this->pdf->setXY($this->pdf->marge,$YBegin);
    $this->addATT();
    $YBegin+=60;
    $this->pdf->setXY($this->pdf->marge,$YBegin);
    $this->addVAR();
    $YBegin+=30;
    $this->pdf->setXY($this->pdf->marge,$YBegin);
    $this->addVKM();
    
    $this->pdf->addPage('P');
    $this->addGrijsVlak2();
    $YBegin=40;
    $this->pdf->setY($YBegin);
    $this->addConclusie();
    $YBegin+=90;
    $this->pdf->setY($YBegin);
    $this->addActiepunten();
    
	}
	
	function addConclusie()
  {
    $this->addKop('CONCLUSIE OVER UW VERMOGENSBEHEER');
    $this->pdf->ln(8);
    $this->pdf->memImage($this->imgConclusie,25,$this->pdf->getY()+10,45);
    $this->addGrijs("Tekst en conclusie over het vermogen.\n\nº Bulletpoint\nº Bulletpoint\nº Bulletpoint");
    
  }
  
  function addActiepunten()
  {
    $this->addKop('TE BESPREKEN ACTIEPUNTEN');
    $this->pdf->ln(8);
    $this->pdf->memImage($this->imgActie,25,$this->pdf->getY()+10,45);
    $this->addGrijs("Bullet points.\n\nº Bulletpoint\nº Bulletpoint\nº Bulletpoint");
  }
  
  function addVKM()
  {
	  $yStart=$this->pdf->getY();
    $vkm=new RapportVKM(null,$this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
    $vkm->writeRapport();
    //	listarray($vkm->vkmWaarde);
    
    $this->pdf->setAligns(array('L', 'R', 'R'));
    $this->pdf->setWidths(array(40, 25, 25));
    $this->addKop("KOSTEN");
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Indirecte fondskosten',$this->pdf->rapport_taal),vertaalTekst('Bedrag',$this->pdf->rapport_taal),vertaalTekst('Percentage',$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Doorlopende kosten',$this->pdf->rapport_taal), $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekostenGesplitst']['TotCostFund'], 0),
                      $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekostenGesplitst']['TotCostFund']/ $vkm->vkmWaarde['fondsGemiddeldeWaarde']*100, 2).' %' ));
    $this->pdf->row(array(vertaalTekst('Transactie kosten',$this->pdf->rapport_taal), $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekostenGesplitst']['FundTransCost'], 0),
                      $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekostenGesplitst']['FundTransCost']/ $vkm->vkmWaarde['fondsGemiddeldeWaarde']*100, 2).' %' ));
    $this->pdf->row(array(vertaalTekst('Performance fee',$this->pdf->rapport_taal), $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekostenGesplitst']['FundPerfFee'], 0),
                      $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekostenGesplitst']['FundPerfFee']/ $vkm->vkmWaarde['fondsGemiddeldeWaarde']*100, 2).' %' ));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Totaal indirecte fondskosten',$this->pdf->rapport_taal), $this->formatGetal($vkm->vkmWaarde['totaalDoorlopendekosten'], 0) ,
                      $this->formatGetal($vkm->vkmWaarde['doorlopendeKostenPercentage'] * 100, 2).' %'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    
    
    $aandeelIndirect=$vkm->vkmWaarde['fondsGemiddeldeWaarde']/$vkm->vkmWaarde['gemiddeldeWaarde'];
    
    
    $this->pdf->row(array(vertaalTekst('Gem. verm. fondsen kosten',$this->pdf->rapport_taal),$this->formatGetal($vkm->vkmWaarde['fondsGemiddeldeWaarde'], 0), $this->formatGetal($aandeelIndirect * 100, 2) . ' %'));
    $this->pdf->row(array(vertaalTekst('Totaal gemiddeld vermogen',$this->pdf->rapport_taal), $this->formatGetal($vkm->vkmWaarde['gemiddeldeWaarde'], 0),'100,00 %'));
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Indirecte factor',$this->pdf->rapport_taal), '',$this->formatGetal($vkm->vkmWaarde['vkmPercentagePortefeuille'], 2).' %'));
    $barData=array();
    $barData['Indirecte fondskosten']=$vkm->vkmWaarde['vkmPercentagePortefeuille'];
    //$this->pdf->setWidths(array(60, 20, 20, 40));
    $this->pdf->setWidths(array(50,20,20,20,20,18,18,18,18,18,15));
    
    $query="SELECT Grootboekrekening,Omschrijving FROM Grootboekrekeningen";
    $db=new DB();
    $db->SQL($query);
    $db->Query();
    while($data=$db->NextRecord())
    {
      $grootboekOmschrijvingen[$data['Grootboekrekening']]=$data['Omschrijving'];
    }
    
    
    foreach($vkm->vkmWaarde['grootboekKosten'] as $key=>$value)
    {
      $barData[$grootboekOmschrijvingen[$key]]=$value/$vkm->vkmWaarde['gemiddeldeWaarde']*100;
    }
    $this->grootboekKleuren=$vkm->grootboekKleuren;
    
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Totaal directe kosten',$this->pdf->rapport_taal), $this->formatGetal($vkm->vkmWaarde['totaalDirecteKosten'], 0), $this->formatGetal($vkm->vkmWaarde['totaalDirecteKosten']/$vkm->vkmWaarde['gemiddeldeWaarde']*100, 2).' %'));
    $this->pdf->row(array(vertaalTekst('Vergelijkende kostenmaatstaf',$this->pdf->rapport_taal),$this->formatGetal($vkm->vkmWaarde['vkmWaarde']*$vkm->vkmWaarde['gemiddeldeWaarde']/100, 0), $this->formatGetal($vkm->vkmWaarde['vkmWaarde'], 2).' %'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  $this->pdf->setY($yStart+10);
    $txt=($this->crmData['ToelKosten']<>''?$this->crmData['ToelKosten']:"Tekst met toelichting over kosten");
    $this->addGrijs($txt);
    
    /*
    
    
    arsort($barData);
    
    $this->pdf->setX(140);
    $this->VBarVerdeling(20,50,$barData);
*/
    
    
  }
  
  
  function VBarVerdeling($w, $h, $data)
  {
    global $__appvar;
    $grafiekPunt = array();
    
    $minVal=0;
    
    
    $n=0;
    $grafiek=array();
    $colors=array();
    
    $aantal=count($data);
    $kleurStap=floor((255-75)/$aantal);
    foreach ($data as $categorie=>$waarde)
    {
      $grafiek[$categorie]=$waarde;
      $categorien[$categorie] = $n;
      $categorieId[$n]=$categorie ;
      
      
      if(!isset($colors[$categorie]))
      {
        if(is_array($this->grootboekKleuren[$categorie]))
          $colors[$categorie] = $this->grootboekKleuren[$categorie];
        else
        {
          $random = 75 + $kleurStap * $n;
          $colors[$categorie] = array($random, $random, $random);//,rand(0,255),rand(0,255)
        }
      }
      $n++;
    }
    
    $numBars=1;
    if($color == null)
    {
      $color=array(155,155,155);
    }
    
    
    $maxVal=ceil(array_sum($data)*2)/2;
    if($maxVal <= 0)
      $maxVal=0;
    
    if($minVal >= 0)
      $minVal = 0;
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    
    $YstartGrafiek = $YPage;
    $hGrafiek = $h;
    $XstartGrafiek = $XPage;
    $bGrafiek = $w; // - legenda
    
    $unit = $hGrafiek / $maxVal * -1;
    $nulYpos =0;
    
    
    $horDiv = 5;
    $bereik = $hGrafiek/$unit;
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $stapgrootte = round(abs($bereik)/$horDiv*10)/10;
    $top = $YstartGrafiek-$h;
    $absUnit =abs($unit);
    
    $nulpunt = $YstartGrafiek + $nulYpos;
    
    
    $n=0;
    for($i=$nulpunt; $i >= $top-0.1; $i-= $absUnit*$stapgrootte)
    {
      //echo $n*$stapgrootte." => $i >= $top  ->$maxVal ".$absUnit*$stapgrootte."<br>\n";
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte,1,false,true)." %",0,0,'R');
      }
      $n++;
      if($n >20)
        break;
    }
    
    
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;
    
    $vBar = ($bGrafiek / 2);
    $eBaton = ($vBar / 2);
    
    
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    
    foreach($grafiek as $categorie=>$val)
    {
      if(!isset($YstartGrafiekLast))
        $YstartGrafiekLast = $YstartGrafiek;
      //Bar
      $xval = $XstartGrafiek +  $vBar - $eBaton/2 ;
      $lval = $eBaton;
      $yval = $YstartGrafiekLast+ $nulYpos ;
      $hval = ($val * $unit);
      
      $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
      $YstartGrafiekLast = $YstartGrafiekLast+$hval;
      $this->pdf->SetTextColor(255,255,255);
      /*
      if(abs($hval) > 3)
      {
        $this->pdf->SetXY($xval, $yval+($hval/2)-2);
        $this->pdf->Cell($eBaton, 4, number_format($val,2,',','.')."%",0,0,'C');
      }
      */
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
      
      
    }
    
    
    $xval = $XstartGrafiek +  $bGrafiek +5;
    $yval = $YstartGrafiek  ;
    foreach($grafiek as $categorie=>$val)
    {
      $yval-=10;
      $this->pdf->Rect($xval, $yval, 2, 2, 'DF',null,$colors[$categorie]);
      $this->pdf->SetXY($xval+4, $yval-1);
      $this->pdf->Cell(50, 4, $categorie." ".number_format($val,2,',','.')."%",0,0,'L');
      
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
  function addOIB()
  {
    global $__appvar;
    
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $portefeuille=$this->portefeuille;
    $rapportageDatum=$this->rapportageDatum;
    $oibBeginY=$this->pdf->getY();
    
    
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$rapportageDatum."' AND ".
      " portefeuille = '".$portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    
    $query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY categorie
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";
    
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($cat = $DB->nextRecord())
    {
      $data['beleggingscategorieEind']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
      $data['beleggingscategorieEind']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
      $data['beleggingscategorieEind']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
      $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
      $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
    }
    
    
    
    $w=(210-$this->pdf->marge*2)/7;
    $this->pdf->widthA = array($w,$w,$w,$w,$w,$w,$w);
    $this->pdf->alignA = array('L','R','R','R','R','R','R');
    
    $this->addKop('OVERZICHT PER '.date('d-m-Y',db2jul($rapportageDatum)));
    $this->pdf->ln();
    
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Rect($this->pdf->marge,$this->pdf->GetY(),array_sum($this->pdf->widthA), 8, 'F');
    $this->pdf->row(array(vertaalTekst("Categorie",$this->pdf->rapport_taal)."\n ",
                      vertaalTekst("Begin-\nvermogen",$this->pdf->rapport_taal),
                      vertaalTekst("Storting onttrekking",$this->pdf->rapport_taal),
                      vertaalTekst("Beleggings resultaat",$this->pdf->rapport_taal),
                      vertaalTekst("Eind-\nvermogen",$this->pdf->rapport_taal),
                      vertaalTekst("Rendem.",$this->pdf->rapport_taal)."\n(".vertaalTekst("Cumu.",$this->pdf->rapport_taal).")",
                      vertaalTekst("Benchmark",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    //$sumWidth = array_sum($this->pdf->widthA);
    //$this->pdf->Line($this->pdf->marge+$this->pdf->widthB[0],$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());
    $this->pdf->SetTextColor(0,0,0);
    
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Categorien');

    
    if(count($this->waarden['Periode']) > 0)
        {
      $n=1;
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->SetFillColor(230,230,230);
      
      $totaalRendament=100;

      $fill=false;
      foreach ($this->waarden['Periode'] as $categorie=>$row)
      {
        if($categorie=='totaal')
          continue;
        $datum = db2jul($row['datum']);
        
        if($fill==true)
        {
          $this->pdf->fillCell = array(1,1,1,1,1,1,1);
          $fill=false;
        }
        else
        {
          $this->pdf->fillCell=array();
          $fill=true;
        }
        
        $this->pdf->CellBorders = array();
        $this->pdf->row(array($this->att->categorien[$categorie],//.' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
                          $this->formatGetal($row['beginwaarde'],0),
                          $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
                          $this->formatGetal($row['resultaat'],0),
                          $this->formatGetal($row['eindwaarde'],0),
                          $this->formatGetal($row['procent'],2),
                          $this->formatGetal($row['benchmark'],2)));
        
        
        
        if(!isset($waardeBegin))
          $waardeBegin=$row['waardeBegin'];
        $totaalWaarde += $row['waardeEur'];
        $totaalResultaat += $row['resultaatVerslagperiode'];
        $totaalGerealiseerd += $row['gerealiseerd'];
        $totaalOngerealiseerd += $row['ongerealiseerd'];
        $totaalOpbrengsten += $row['opbrengsten'];
        $totaalKosten += $row['kosten'];
        $totaalRente += $row['rente'];
        $totaalStortingenOntrekkingen += $row['stortingen']-$row['onttrekkingen'];
        $totaalRendament = $row['index'];
        
        $n++;
        $i++;
      }
      $this->pdf->fillCell=array();
      
      
      
      $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS');
      $this->pdf->row(array('','','','','','','','','','','','',''));
      $this->pdf->SetY($this->pdf->GetY()-4);
      
      
      $this->pdf->ln(3);
      $totaal=$this->waarden['Periode']['totaal'];
      
      //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->CellBorders = array();
      $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
                        $this->formatGetal($totaal['beginwaarde'],0),
                        $this->formatGetal($totaal['stortingen']-$totaal['onttrekkingen'],0),
                        $this->formatGetal($totaal['resultaat'],0),
                        $this->formatGetal($totaal['eindwaarde'],0),
                        $this->formatGetal($totaal['procent'],2),
                        $this->formatGetal($totaal['benchmark'],2)));
      $this->pdf->CellBorders = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->setXY($this->pdf->marge,$oibBeginY+55);

      $txt=($this->crmData['ToelVermogen']<>''?$this->crmData['ToelVermogen']:'Tekst over strategische en tactische allocatie');
      $this->addGrijs($txt);
      
    }
    
   // $this->pdf->setY($yStart+8);
    
    $this->pdf->setXY(15,$oibBeginY+50);
//$this->pdf->setXY(175,40);
 //   $this->printPie($data['beleggingscategorieEind']['pieData'],$data['beleggingscategorieEind']['kleurData'],'',30,30);//'Categorieverdeling '.date("d-m-Y",db2jul($rapportageDatum))

	}
	
	function addATT()
	{
    $yStart=$this->pdf->getY();
    $this->addKop('RENDEMENT');
    $this->pdf->widthA = array(13,20,20,20,20,20,15,20,20,15,15);
    $this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
    $index=new indexHerberekening();
    
    // getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR',$output='')
    $benchmark=$this->pdf->portefeuilledata['SpecifiekeIndex'];
    $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille,$benchmark);
    //listarray();exit;
    $grafiekData=array();
    foreach ($indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['datum'][] = date("M",db2jul($data['datum']));//$data['datum'];
        $grafiekData['portefeuille'][] = $data['index']-100;
        if($benchmark<>'')
          $grafiekData['specifiekeIndex'][] = $data['specifiekeIndex']-100;
        //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
        
        $barGraph['Index'][$data['datum']]['leeg']=0;
        foreach ($data['extra']['cat'] as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          
          $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
          if($waarde <> 0)
            $categorien[$categorie]=$categorie;
        }
      }
      
    }
    
    if (count($grafiekData) > 1)
    {
      $this->pdf->SetXY(14,$yStart+20);//104
      if($benchmark<>'')
        $grafiekData['legenda']=array('Portefeuille','Benchmark');
      else

      $grafiekData['legenda']=array('Portefeuille');//,'Benchmark');
      $grafiekData['titel']=vertaalTekst('Rendement',$this->pdf->rapport_taal).' ('.vertaalTekst('cumulatief',$this->pdf->rapport_taal).' '.vertaalTekst('in',$this->pdf->rapport_taal).' %)';
      $indexKleur=array(array(0, 40, 58),array(150,150,150)); //listarray($this->pdf->rapport_grafiek_color);exit;
      $this->LineDiagramVar(80, 20, $grafiekData,$indexKleur,0,0,6,5,true);//50

      
    }
    $this->pdf->setY($yStart+10);
    $txt=($this->crmData['ToelVermogen']<>''?$this->crmData['ToelVermogen']:'Tekst met toelichting over rendement Tekst met toelichting over rendement Tekst met toelichting over rendement');
    $this->addGrijs($txt);
    
  }
  
  function addVAR()
  {
    $yStart=$this->pdf->getY();
    $stdev=new rapportSDberekening($this->portefeuille,$this->rapportageDatum,1);
    $stdev->settings['SdFrequentie']='m';
    $stdev->settings['aantalPerJaar']=12;
    
    //  $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
    //   $stdev->noTotaal=true;
    if(count($this->pdf->portefeuilles) > 1)
      $stdev->consolidatiePortefeuilles=$this->pdf->portefeuilles;
    
    $stdev->addReeks('totaal');
    $stdev->addReeks('benchmark',$this->pdf->portefeuilledata['SpecifiekeIndex']);
   // $this->addPerfGrafiek($stdev);
    
    $stdev->berekenWaarden();
    $riskData=$stdev->riskAnalyze('totaal','benchmark',false,true);
    $riskBenchmark=$stdev->riskAnalyze('benchmark','totaal',false,true);

    $this->addKop('RISICO');
    
    $this->VARrechts($riskData,$riskBenchmark);
    $this->pdf->setY($yStart+10);
    $txt=($this->crmData['ToelRisico']<>''?$this->crmData['ToelRisico']:'Tekst met toelichting over risico Tekst met toelichting over risico Tekst met toelichting over risico');
    $this->addGrijs($txt);
    
  }
  
  
  function VARrechts($riskData,$riskBenchmark)
  {
    global $__appvar;
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum ."' AND ".
      " portefeuille = '". $this->portefeuille ."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    
    $this->pdf->Ln(2);
    $this->pdf->setX($this->pdf->marge);//,165);
    $this->pdf->SetWidths(array(40,25,25));
    $this->pdf->SetAligns(array('L','R','R'));
   
    if(count($riskBenchmark) >1)
    { $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('', 'Portefeuille', 'Benchmark'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      //$this->pdf->ln(2);
      $this->pdf->row(array('Standaarddeviatie', $this->formatGetal($riskData['standaarddeviatie'], 1) . '%', $this->formatGetal($riskData['standaarddeviatieBenchmark'], 1) . '%'));
    //  $this->pdf->ln(2);
    }
    else
    {
      $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('', 'Portefeuille'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     // $this->pdf->ln(2);
      $this->pdf->row(array('Standaarddeviatie', $this->formatGetal($riskData['standaarddeviatie'], 1) . '%'));
     // $this->pdf->ln(2);
    }
    // $this->pdf->row(array('','AFM-Standaarddeviatie',$this->formatGetal($riskData['standaarddeviatieAFM'],1).'%'));
    //  $this->pdf->ln(2);
    
    if($riskData['valueAtRisk'] <> 0)
    {
      $riskData['valueAtRisk'] = (100 - $riskData['valueAtRisk']) / 100 * $totaalWaarde;
      $riskBenchmark['valueAtRisk'] = (100 - $riskBenchmark['valueAtRisk']) / 100 * $totaalWaarde;
    }
    
    if(count($riskBenchmark) >1)
    {
      $this->pdf->row(array('Value at Risk', '€ ' . $this->formatGetal($riskData['valueAtRisk'], 0), '€ ' . $this->formatGetal($riskBenchmark['valueAtRisk'], 0)));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
    //  $this->pdf->ln(2);
      $this->pdf->row(array('Maximale terugval', $this->formatGetal($riskData['maxDrawdown'], 1) . '%', $this->formatGetal($riskBenchmark['maxDrawdown'], 1) . '%'));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
   //   $this->pdf->ln(2);
      /*
      $this->pdf->row(array('', 'Tracking Error', $this->formatGetal($riskData['trackingError'], 1) . '%', ''));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
      $this->pdf->ln(2);
      $this->pdf->row(array('', 'Sharpe ratio', $this->formatGetal($riskData['sharpeRatio'], 1) . '', $this->formatGetal($riskBenchmark['sharpeRatio'], 1)));
      $this->pdf->ln(2);
      $this->pdf->row(array('', 'Informatieratio', $this->formatGetal($riskData['informatieratio'], 1) . '', ''));
      */
    }
    else
    {
      $this->pdf->row(array('Value at Risk', '€ ' . $this->formatGetal($riskData['valueAtRisk'], 0)));//'Value at Risk geeft het verwachte maximale verlies aan met een waarschijnlijkheid van 95%. De historische VaR is bepaald aan de hand van de werkelijke jaarlijkse rendementsverdeling over de afgelopen tien jaar.'
   //   $this->pdf->ln(2);
      $this->pdf->row(array('Maximale terugval', $this->formatGetal($riskData['maxDrawdown'], 1) . '%'));//'Maximum Drawdown geeft de maximale daling weer vanaf de hoogste waarde in een specifieke periode. Deze periode betreft in de overzichten een periode van tien jaar.'
   //   $this->pdf->ln(2);
      /*
      $this->pdf->row(array('', 'Tracking Error', $this->formatGetal($riskData['trackingError'], 1) . '%'));//'De Tracking-error geeft een indicatie weer van de mate van afwijking van het rendement van de portefeuille ten opzichte van de benchmark.'
      $this->pdf->ln(2);
      $this->pdf->row(array('', 'Sharpe ratio', $this->formatGetal($riskData['sharpeRatio'], 1)));
      $this->pdf->ln(2);
      $this->pdf->row(array('', 'Informatieratio', $this->formatGetal($riskData['informatieratio'], 1) . ''));
      */
    }
    
  }
  
  
  function addPerfGrafiek($stdev)
  {
    $portIndex=1;
    $indexIndex=1;
    foreach($stdev->reeksen['totaal'] as $datum=>$perfData)
    {
      
      $juldate=db2jul($datum);
      $portIndex=(1+$perfData['perf']/100)*$portIndex;
      $perfGrafiek['portefeuille'][]=($portIndex-1)*100;
      
      $benchmarkData = $stdev->reeksen['benchmark'][$datum];
      $indexIndex = (1 + $benchmarkData['perf'] / 100) * $indexIndex;
      $perfGrafiek['specifiekeIndex'][] = ($indexIndex - 1) * 100;
      $perfGrafiek['datum'][]= date("M y",$juldate);
    }
    
    
    $perfGrafiek['legenda']=array('Portefeuille','Benchmark');


    $this->pdf->setXY(120,112);
    $indexKleur=array(176,160,122);
    $perfGrafiek['titel']='Portefeuille rendement';
    $this->LineDiagramVar(80, 30, $perfGrafiek,array(array(0, 40, 58),$indexKleur),0,0,6,5,true);//50

    
    
  }
  
  function LineDiagramVar($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$eerstePunt=false)
  {
    global $__appvar;
    
    $legendDatum= $data['datum'];
    $legendaItems= $data['legenda'];
    $titel=$data['titel'];
    $data1 = $data['specifiekeIndex'];
    $data2 = $data['extra'];
    $data = $data['portefeuille'];
    
    
    if(count($data2)>0)
      $bereikdata = array_merge(array_values($data),array_values($data1),array_values($data2));
    elseif(count($data1)>0)
      $bereikdata = array_merge(array_values($data),array_values($data1));
    else
      $bereikdata =  array_values($data);
    
    // listarray($bereikdata);//exit;
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );
    
    $this->pdf->setXY($XPage,$YPage-4);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->Cell($w,0,$titel,0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)));
    
    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));
    
    if(is_array($color[0]))
    {
      $color2= $color[2];
      $color1= $color[1];
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);
    
    
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      foreach($bereikdata as $value)
      {
        if ($value > $maxVal)
        {
          $maxVal = floor($value);
        }
      }
    }
    if ($minVal == 0)
    {
      foreach($bereikdata as $value)
      {
        if($value<$minVal)
          $minVal = floor($value);
      }
    }
    
    $minVal = floor(($minVal-1) * 1.1);
    if($minVal > 0)
      $minVal=0;
    if($maxVal < 0)
      $maxVal=0;
    
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      $xpos = $XDiag + $verInterval * $i;
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 8);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    if($titel=='Sharpe-ratio')
      $yAs='';
    else
      $yAs=' %';
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      //$this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) .$yAs);
      
      $this->pdf->setXY($XDiag-11, $i-2);
      $this->pdf->cell(10,4,0-($n*$stapgrootte) .$yAs,0,1,'R');
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
      {
        $this->pdf->setXY($XDiag-11, $i-2);
        $this->pdf->cell(10,4,(($n*$stapgrootte) + 0) . $yAs,0,1,'R');
        
        // $this->pdf->Text($XDiag - 7, $i, ($n * $stapgrootte) + 0 . $yAs);
      }
      $n++;
      if($n >20)
        break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    //for ($i=0; $i<count($data); $i++)
    $i=0;
    $start=false;
  

    

    
    $cubic=true;
    if($cubic==true)
    {
      $oldh=$h;
      $oldData=$data;
      $data=array(0);
      foreach ($oldData as $datum=>$value)
      {
        $datumArray[] = $datum;
        $data[] = $value;
      }
      
      $Index = 1;
      $XLast = -1;
      foreach ( $data as $Key => $Value )
      {
        $XIn[$Key] = $Index;
        $YIn[$Key] = $Value;
        $Index++;
      }
    
      $Index--;
//         $Index=count($data);
      $Yt[0] = 0;
      $Yt[1] = 0;
      $U[1]  = 0;
      for($i=1;$i<=$Index-1;$i++)
      {
        $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
        $p      = $Sig * $Yt[$i-1] + 2;
        $Yt[$i] = ($Sig - 1) / $p;
        $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
        $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
      }
      $qn = 0;
      $un = 0;
      $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);
    
      for($k=$Index-1;$k>=1;$k--)
        $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];
    
    
      $Accuracy=0.1;
      for($X=1;$X<=$Index;$X=$X+$Accuracy)
      {
        $klo = 1;
        $khi = $Index;
        $k   = $khi - $klo;
        while($k > 1)
        {
          $k = $khi - $klo;
          If ( $XIn[$k] >= $X )
            $khi = $k;
          else
            $klo = $k;
        }
        $klo = $khi - 1;
      
        $h     = $XIn[$khi] - $XIn[$klo];
        $a     = ($XIn[$khi] - $X) / $h;
        $b     = ($X - $XIn[$klo]) / $h;
        $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;
      
        // echo "$Value <br>\n";
      
        //$YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);
        $YPos = $YDiag + (($maxVal-$Value) * $waardeCorrectie) ;
        $XPos = $XDiag+($X-1)*$unit;
      
      
        if($X==1)
        {
          $XLast=$XPos;
          $YLast=$YPos;
        }
      
        $this->pdf->Line($XLast,$YLast,$XPos,$YPos,$lineStyle);
        $XLast = $XPos;
        $YLast = $YPos;
      
      }
      $h=$oldh;
    }
    else
    {
    foreach($data as $datum=>$waarde)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$datum],25);
      $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
      
      if ($start==true || $eerstePunt==true)//$i>0 &&
      {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      }
      if($waarde<>0)
        $start=true;
      
      $yval = $yval2;
      $i++;
    }
    }
    
     if($cubic==true)
    {
      $oldh=$h;
      $oldData=$data1;
      $data=array(0);
$lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      foreach ($oldData as $datum=>$value)
      {
        $datumArray[] = $datum;
        $data[] = $value;
      }
      
      $Index = 1;
      $XLast = -1;
      foreach ( $data as $Key => $Value )
      {
        $XIn[$Key] = $Index;
        $YIn[$Key] = $Value;
        $Index++;
      }
    
      $Index--;
//         $Index=count($data);
      $Yt[0] = 0;
      $Yt[1] = 0;
      $U[1]  = 0;
      for($i=1;$i<=$Index-1;$i++)
      {
        $Sig    = ($XIn[$i] - $XIn[$i-1]) / ($XIn[$i+1] - $XIn[$i-1]);
        $p      = $Sig * $Yt[$i-1] + 2;
        $Yt[$i] = ($Sig - 1) / $p;
        $U[$i]  = ($YIn[$i+1] - $YIn[$i]) / ($XIn[$i+1] - $XIn[$i]) - ($YIn[$i] - $YIn[$i-1]) / ($XIn[$i] - $XIn[$i-1]);
        $U[$i]  = (6 * $U[$i] / ($XIn[$i+1] - $XIn[$i-1]) - $Sig * $U[$i-1]) / $p;
      }
      $qn = 0;
      $un = 0;
      $Yt[$Index] = ($un - $qn * $U[$Index-1]) / ($qn * $Yt[$Index-1] + 1);
    
      for($k=$Index-1;$k>=1;$k--)
        $Yt[$k] = $Yt[$k] * $Yt[$k+1] + $U[$k];
    
    
      $Accuracy=0.1;
      for($X=1;$X<=$Index;$X=$X+$Accuracy)
      {
        $klo = 1;
        $khi = $Index;
        $k   = $khi - $klo;
        while($k > 1)
        {
          $k = $khi - $klo;
          If ( $XIn[$k] >= $X )
            $khi = $k;
          else
            $klo = $k;
        }
        $klo = $khi - 1;
      
        $h     = $XIn[$khi] - $XIn[$klo];
        $a     = ($XIn[$khi] - $X) / $h;
        $b     = ($X - $XIn[$klo]) / $h;
        $Value = $a * $YIn[$klo] + $b * $YIn[$khi] + (($a*$a*$a - $a) * $Yt[$klo] + ($b*$b*$b - $b) * $Yt[$khi]) * ($h*$h) / 6;
      
        // echo "$Value <br>\n";
      
        //$YPos = $this->GArea_Y2 - (($Value-$this->VMin) * $this->DivisionRatio);
        $YPos = $YDiag + (($maxVal-$Value) * $waardeCorrectie) ;
        $XPos = $XDiag+($X-1)*$unit;
      
      
        if($X==1)
        {
          $XLast=$XPos;
          $YLast=$YPos;
        }
      
        $this->pdf->Line($XLast,$YLast,$XPos,$YPos,$lineStyle);
        $XLast = $XPos;
        $YLast = $YPos;
      
      }
       $h=$oldh;
    }
    else
    {
    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      
      // for ($i=0; $i<count($data1); $i++)
      $i=0;
      $start=false;
      foreach($data1 as $datum=>$waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        
        if ($i>0 && $start==true || $eerstePunt==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        if($waarde<>0)
          {
          $start=true;
          }
        $yval = $yval2;
        $i++;
      }
    }
    }
    
    if(is_array($data2))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color2);
      
      //for ($i=0; $i<count($data2); $i++)
      $i=0;
      $start=false;
      foreach($data2 as $datum=>$waarde)
      {
        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        
        if ($i>0 && $start==true || $eerstePunt==true)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
        if($waarde<>0)
          $start=true;
        $yval = $yval2;
        $i++;
      }
    }
    
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'width' => 0.2,'cap' => 'butt'));
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      elseif($index==1)
        $kleur=$color1;
      else
        $kleur=$color2;
      
      $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
      $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
      $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
      $this->pdf->Cell(0,3,$item);
      $step+=($w/3);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }
  
  
  function getWaarden($datumBegin,$datumEind,$portefeuille)
  {
    $julBegin = db2jul($datumBegin);
    $julEind = db2jul($datumEind);
    
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    
    $ready = false;
    $i=0;
    
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
    
    
    while ($ready == false)
    {
      if (mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar) > $stop)
      {
        $ready = true;
      }
      else
      {
        if($i==0)
          $datum[$i]['start']=$datumBegin;
        else
        {
          $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
        }
        $datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
        $i++;
      }
    }
    if($i==0)
      $datum[$i]['start']=$datumBegin;
    else
      $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
    $datum[$i]['stop']=$datumEind;

    $i=1;
    $indexData['index']=100;
  
    $db=new DB();


    foreach ($datum as $periode)
    {
  
      $query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='m' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($periode['stop'],0,10)."' ";
  
      if(db2jul($periode['start']) == db2jul($periode['stop']))
      {
    
      }
      elseif($db->QRecords($query) > 0)
      {
        $dbData = $db->nextRecord();
        $indexData['database']=1;
        $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
        $indexData['periode']= $periode['start']."->".$periode['stop'];
        $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
        $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
        $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
        $indexData['stortingen'] = $dbData['Stortingen'];
        $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
        $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
        $indexData['kosten'] = $dbData['Kosten'];
        $indexData['opbrengsten'] = $dbData['Opbrengsten'];
        $indexData['performance'] = $dbData['indexWaarde'];
        $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
        $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
        $indexData['rente'] = $dbData['rente'];
        $indexData['extra'] = unserialize($dbData['extra']);
      }
      else
      {
      $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille));
      }
      $indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
      $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
      $data[$i] = $indexData;
      $i++;
    }
    return $data;
  }
  
  function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR')
  {
    if(substr($beginDatum,5,5)=='12-31')
      $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';
    
    if ($valuta != "EUR" )
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
    else
      $koersQuery = "";
    
    $totaalWaarde =array();
    $db = new DB();
    
    $query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
    $db->SQL($query);
    $startDatum=$db->lookupRecord();
    
    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
    {
      if($this->voorStartdatumNegeren==true && db2jul($eindDatum) <= db2jul($startDatum['Startdatum']))
        return array('periode'=>$beginDatum."->".$eindDatum,'periodeForm'=>date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum)));
      
      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    }
    else
      $wegingsDatum=$beginDatum;
    
    $startjaar=substr($beginDatum,0,4);
    
    $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
    //echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";
    
		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,(substr($beginDatum, 5, 5) == '01-01')?true:false,$valuta,$beginDatum);
    
    if($valuta <> 'EUR')
      $valutaKoers=getValutaKoers($valuta,$beginDatum);
    else
      $valutaKoers=1;
    foreach ($fondswaarden['beginmaand'] as $regel)
    {
      $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,(substr($eindDatum, 5, 5) == '01-01')?true:false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;
    
    // listarray($categorieVerdeling);
    if($valuta <> 'EUR')
      $valutaKoers=getValutaKoers($valuta,$eindDatum);
    else
      $valutaKoers=1;
    
    foreach ($fondswaarden['eindmaand'] as $regel)
    {
      $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
      
      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['hoofdcategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['G-LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
    }
    
    
    $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
    $DB=new DB();
    
    $query = "SELECT ".
      "SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
      "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
      "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
      "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
      "FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
      "WHERE ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND ".
      "Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
      "Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();
    
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
    $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $eindDatum  $wegingsDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
    $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta);
    $onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta);
    $resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    
    $query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();
    
    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;
    
    foreach ($categorieVerdeling as $cat=>$waarde)
      $categorieVerdeling[$cat]=$waarde."";
    $data['database']=0;
    
    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']['cat']=$categorieVerdeling;
    return $data;
    
  }
  
  
  
  function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $legendDatum= $data['Datum'];
    $data1 = $data['Index1'];
    $data = $data['Index'];
    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $w/12 );
    
    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }
    
    if($color == null)
      $color=array(116,95,71);
    $this->pdf->SetLineWidth(0.2);
    
    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    
    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 0)
        $maxVal = 1;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 0)
        $minVal =-1;
    }
    
    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / count($data);
    
    if($jaar)
      $unit = $lDiag / 12;
    
    for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
    {
      $xpos = $XDiag + $verInterval * $i;
    }
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetDrawColor(0,0,0);
    
    $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
    $unith = $hDiag / (-1 * $minVal + $maxVal);
    
    $top = $YPage;
    $bodem = $YDiag+$hDiag;
    $absUnit =abs($unith);
    
    $nulpunt = $YDiag + (($maxVal) * $waardeCorrectie);
    $n=0;
    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
        break;
    }
    
    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
      
      $n++;
      if($n >20)
        break;
    }
    
    //datum onder grafiek
    /*
    $datumStart = db2jul($legendDatum[0]);
    $datumStart = vertaalTekst($__appvar["Maanden"][date("n",$datumStart)],$pdf->rapport_taal).' '.date("Y",$datumStart);
    $datumStop  =  db2jul($legendDatum[count($legendDatum)-1])+86400;
    $datumStop  = vertaalTekst($__appvar["Maanden"][date("n",$datumStop)],$pdf->rapport_taal).' '.date("Y",$datumStop);
    $ypos = $YDiag + $hDiag + $margin*2;
    $xpos = $XDiag;
    $this->pdf->Text($xpos, $ypos,$datumStart);
    $xpos = $XPage+$w - $this->pdf->GetStringWidth($datumStop);
    $this->pdf->Text($xpos, $ypos,$datumStop);
*/
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    //listarray($data);
    // $color=array(200,0,0);
    
    
    for ($i=0; $i<count($data); $i++)
    {
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
      
      
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);
      
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
      
      
      $yval = $yval2;
    }
    
    if(is_array($data1))
    {
      // listarray($data1);
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);
      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        
        $this->pdf->Text($XDiag+($i+1)*$unit-0.5,$yval2-4.5,$data1[$i]);
        
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }
  
  //OIB charts
  function printPie($pieData,$kleurdata,$title='',$width=100,$height=100)
  {
    
    $col1=array(255,0,0); // rood
    $col2=array(0,255,0); // groen
    $col3=array(255,128,0); // oranje
    $col4=array(0,0,255); // blauw
    $col5=array(255,255,0); // geel
    $col6=array(255,0,255); // paars
    $col7=array(128,128,128); // grijs
    $col8=array(128,64,64); // bruin
    $col9=array(255,255,255); // wit
    $col0=array(0,0,0); //zwart
    $standaardKleuren=array($col1,$col2,$col3,$col4,$col5,$col6,$col7,$col8,$col9,$col0);
    // standaardkleuren vervangen voor eigen kleuren.
    $startX=$this->pdf->GetX();
    
    if(isset($kleurdata))
    {
      $grafiekKleuren = array();
      $a=0;
      while (list($key, $value) = each($kleurdata))
      {
        if ($value['R']['value'] == 0 && $value['G']['value'] == 0 && $value['B']['value'] == 0)
          $grafiekKleuren[]=$standaardKleuren[$a];
        else
          $grafiekKleuren[] = array($value['R']['value'],$value['G']['value'],$value['B']['value']);
        $pieData[$key] = $value['percentage'];
        $a++;
      }
    }
    else
      $grafiekKleuren = $standaardKleuren;
    
    while (list($key, $value) = each($pieData))
      if ($value < 0)
        $pieData[$key] = -1 * $value;
    
    //$this->pdf->SetXY(210, $this->pdf->headerStart);
    $y = $this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->setXY($startX,$y-4);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    if($title<>'')
    
    $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
    $this->pdf->setXY($startX,$y);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $this->pdf->setX($startX);
    $this->PieChart($width, $height, $pieData, '%l (%p)', $grafiekKleuren);
    $hoogte = ($this->pdf->getY() - $y) + 8;
    $this->pdf->setY($y);
    
    $this->pdf->SetLineWidth($this->pdf->lineWidth);
    $this->pdf->setX($startX);
    
    //	$this->pdf->Rect($startX,$this->pdf->getY(),$width,$hoogte);
    
  }
  
  function PieChart($w, $h, $data, $format, $colors=null)
  {
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->SetLegends($data,$format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 4;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
    $radius=min($w,$h);
    
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if($colors == null) {
      for($i = 0;$i < $this->pdf->NbVal; $i++) {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray,$gray,$gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $aantal=count($data);
    foreach($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        
        $avgAngle=($angleStart+$angleEnd)/360*M_PI;
        $factor=1.5;
        
        if($i==($aantal-1))
          $angleEnd=360;
        
        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
        $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
        $this->pdf->Sector($XDiag+(sin($avgAngle)*$factor), $YDiag-(cos($avgAngle)*$factor), $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    //   if ($angleEnd != 360) {
    //      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    //  }
    
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w +10;
    $x2 = $x1 + $hLegend + $margin;
    $y1 = $YPage+5;// + ($radius) + $margin;
    
    for($i=0; $i<$this->pdf->NbVal; $i++) {
      $this->pdf->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
      $this->pdf->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2,$y1);
      $this->pdf->Cell(0,$hLegend,$this->pdf->legends[$i]);
      $y1+=$hLegend + 2;
    }
    
  }
  
  function SetLegends($data, $format)
  {
    $this->pdf->legends=array();
    $this->pdf->wLegend=0;
    
    $this->pdf->sum=array_sum($data);
    
    $this->pdf->NbVal=count($data);
    foreach($data as $l=>$val)
    {
      //$p=sprintf('%.1f',$val/$this->sum*100).'%';
      $p=sprintf('%.1f',$val).'%';
      $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
      $this->pdf->legends[]=$legend;
      $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
    }
  }
  
  
}
?>
