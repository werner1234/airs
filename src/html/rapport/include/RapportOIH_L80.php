<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/12/01 17:55:28 $
File Versie					: $Revision: 1.1 $

$Log: RapportOIH_L80.php,v $
Revision 1.1  2019/12/01 17:55:28  rvv
*** empty log message ***

Revision 1.10  2018/08/18 12:40:15  rvv
php 5.6 & consolidatie

Revision 1.9  2018/06/16 17:42:56  rvv
*** empty log message ***

Revision 1.8  2018/06/09 15:58:54  rvv
*** empty log message ***

Revision 1.7  2018/03/04 10:14:13  rvv
*** empty log message ***

Revision 1.6  2017/07/12 08:00:45  rvv
*** empty log message ***

Revision 1.5  2015/02/11 16:49:55  rvv
*** empty log message ***

Revision 1.4  2013/06/30 15:07:33  rvv
*** empty log message ***

Revision 1.3  2013/04/27 16:29:28  rvv
*** empty log message ***

Revision 1.2  2013/04/17 16:00:15  rvv
*** empty log message ***

Revision 1.1  2013/03/17 10:58:29  rvv
*** empty log message ***

Revision 1.8  2013/03/13 17:01:08  rvv
*** empty log message ***

Revision 1.7  2013/02/20 15:12:14  rvv
*** empty log message ***

Revision 1.6  2013/02/10 10:06:07  rvv
*** empty log message ***

Revision 1.5  2013/02/06 19:06:11  rvv
*** empty log message ***

Revision 1.4  2013/02/03 09:04:21  rvv
*** empty log message ***

Revision 1.3  2013/01/27 14:14:24  rvv
*** empty log message ***

Revision 1.2  2013/01/20 13:27:16  rvv
*** empty log message ***

Revision 1.1  2013/01/13 13:35:39  rvv
*** empty log message ***

Revision 1.11  2013/01/06 10:09:57  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIH_L80
{

	function RapportOIH_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Resultaat en rendementsberekening";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
  
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }



	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$DB = new DB();

		$this->pdf->widthB = array(280);
		$this->pdf->alignB = array('L');


		$this->pdf->AddPage();
    $this->pdf->templateVars['OIHPaginas']=$this->pdf->page;
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);

    $this->getKleuren();
    
    $startX = 20;
    $gebruikteCategorie = $this->addZorgBar();
    $this->plotZorgBar2($startX, 4, 50, $gebruikteCategorie);
 
   
    if(count($this->pdf->portefeuilles)>0)
    {
      foreach($this->pdf->portefeuilles as $portefeuille)
      {
        $tmp=berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum, (substr($this->rapportageDatum, 5, 5) == '01-01')?true:false,'EUR',$this->rapportageDatum);
        vulTijdelijkeTabel($tmp,$portefeuille,$this->rapportageDatum);
        $startX += 80;
        $gebruikteCategorie = $this->addZorgBar($portefeuille);
        $this->plotZorgBar2($startX, 4, 50, $gebruikteCategorie,$portefeuille);
      }
    }

	}
  
  function getKleuren()
  {
    $db=new DB();
    $query="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $this->kleuren=unserialize($data['grafiek_kleur']);
    if($this->kleuren['OIS']['Liquiditeiten']['G']['value']==0)
      $this->kleuren['OIS']['Liquiditeiten']=$this->kleuren['OIB']['Liquiditeiten'];
    foreach($this->kleuren as $groep=>$kleuren)
    {
      foreach($kleuren as $cat=>$kleurdata)
        $this->kleuren['alle'][$cat]=$kleurdata;
    }
  }




  function addZorgBar($portefeuille='')
  {
    if($portefeuille<>'')
    {
      $db=new DB();
      $query="SELECT Portefeuille,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".mysql_real_escape_string($portefeuille)."'";
      $db->SQL($query);
      $pdata=$db->lookupRecord();
    }
    else
    {
      $pdata=$this->pdf->portefeuilledata;
    }
    include_once("rapport/Zorgplichtcontrole.php");
    $zorgplicht = new Zorgplichtcontrole();
	 
	  $zpwaarde=$zorgplicht->zorgplichtMeting($pdata,$this->rapportageDatum);
    $gebruikteCategorien=array();
    foreach($zpwaarde['categorien'] as $categorie=>$data)
    {
      if(!isset($data['fondsGekoppeld']))
      {
        $gebruikteCategorien[$categorie]=$data;
      }
    }
    foreach($zpwaarde['conclusie'] as $data)
    {
      foreach($gebruikteCategorien as $categorie=>$gebruikteCategorie)
      {
        if($data[0]==$gebruikteCategorie['Zorgplicht'])
        {
          $gebruikteCategorien[$categorie]['percentage']=$data[2];
          $gebruikteCategorien[$categorie]['conclusie']=$data[5];
        }
      }
    }   
    return $gebruikteCategorien;
  }


  function plotZorgBar2($startX,$barWidth,$height,$zorgdata,$portefeuille='')
  {
    $DB=new DB();
    $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien WHERE vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
	  $DB->SQL($query);
	  $DB->Query();
	  while($data = $DB->NextRecord())
    {
      $categorien[$data['Zorgplicht']]=$data['Omschrijving'];
    }
  
    $this->pdf->setXY($startX+35,45);
    $this->pdf->Cell(5,5,"Mandaat controle ".$portefeuille,0,0,'C');
    $this->pdf->Rect($startX,50,70,95);
    $this->pdf->setXY($startX+10,64);
  
    $hProcent=$height/100;

    $marge=1;
    $extraY=8;
    $xPage=$this->pdf->getX();
    $yPage=$this->pdf->getY();   
    
   
    
     
     foreach($zorgdata as $categorie=>$data)
     {
       
    $data['percentage']=str_replace(',','.',$data['percentage']);
     
    $this->pdf->setXY($xPage-$marge-4,$yPage-2);
    $this->pdf->Rect($xPage, $yPage, $hProcent*100, $barWidth, 'D');
    $this->pdf->Rect($xPage, $yPage+$extraY,$hProcent*100, $barWidth,  'D');
        $this->pdf->setXY($xPage-2,$yPage-$marge-8);
    $this->pdf->cell(4,4,$categorien[$categorie],0,0,'L');
    $this->pdf->setXY($xPage-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"0",0,0,'C');
    $this->pdf->setXY($xPage+$hProcent*100-2,$yPage-$marge-4);
    $this->pdf->cell(4,4,"100%",0,0,'C');
    $this->pdf->setXY($xPage+$hProcent*$data['Minimum']-2,$yPage-$marge-4);
    if($data['Minimum']<>0)
      $this->pdf->cell(4,4,"".$data['Minimum'].'',0,0,'C');
    $this->pdf->setXY($xPage+$hProcent*$data['Maximum']-2,$yPage-$marge-4);
    if($data['Maximum']<>100)
      $this->pdf->cell(4,4,"".$data['Maximum'].'',0,0,'C');
     
    //$this->pdf->setXY($xPage+$hProcent*$data['Norm']-2,$yPage+$marge+5);
    //$this->pdf->cell(4,4,"Norm ".$data['Norm'],0,0,'R');
    
    $this->pdf->SetFillColor(239,86,61);
    $this->pdf->Rect($xPage, $yPage, $hProcent*$data['Minimum'], $barWidth,  'DF');
    $this->pdf->SetFillColor(27,159,17);
    $this->pdf->Rect($xPage+$hProcent*$data['Minimum'], $yPage,$hProcent*($data['Maximum']-$data['Minimum']), $barWidth,   'DF');
    $this->pdf->SetFillColor(239,86,61);
    $this->pdf->Rect($xPage+$hProcent*$data['Maximum'], $yPage, $hProcent*(100-$data['Maximum']),$barWidth,  'DF');
     
    //$this->pdf->Line($xPage+$hProcent*$data['Norm'], $yPage,$xPage+$hProcent*$data['Norm'],$yPage+$barWidth);
    if($data['conclusie']=='Voldoet')
      $this->pdf->SetFillColor(27,159,17);
    else
      $this->pdf->SetFillColor(239,86,61);  
    $this->pdf->Rect($xPage,$yPage+$extraY , $hProcent*$data['percentage'], $barWidth,  'DF');
    $this->pdf->setXY($xPage+$hProcent*$data['percentage']-2,$yPage+$barWidth+$marge+$extraY);
    $this->pdf->cell(4,4,$this->formatGetal($data['percentage'],1).'% werkelijk',0,0,'L');
    $yPage+=30;
    }
  }
  



}
?>