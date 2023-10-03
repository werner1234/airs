<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/09/28 17:20:17 $
File Versie					: $Revision: 1.16 $

$Log: RapportHSE_L49.php,v $
Revision 1.16  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.15  2018/06/13 15:27:48  rvv
*** empty log message ***

Revision 1.14  2017/06/25 14:49:37  rvv
*** empty log message ***

Revision 1.13  2017/05/17 15:57:50  rvv
*** empty log message ***

Revision 1.12  2017/05/13 16:27:34  rvv
*** empty log message ***

Revision 1.11  2014/12/24 16:00:30  rvv
*** empty log message ***

Revision 1.10  2014/12/13 19:24:44  rvv
*** empty log message ***

Revision 1.9  2014/12/10 16:58:25  rvv
*** empty log message ***

Revision 1.8  2014/04/05 15:33:48  rvv
*** empty log message ***

Revision 1.7  2014/04/02 15:53:15  rvv
*** empty log message ***

Revision 1.6  2014/03/29 16:22:37  rvv
*** empty log message ***

Revision 1.5  2014/03/27 15:59:32  rvv
*** empty log message ***

Revision 1.4  2014/03/27 14:59:18  rvv
*** empty log message ***

Revision 1.3  2014/03/22 15:47:14  rvv
*** empty log message ***

Revision 1.2  2013/12/18 17:10:42  rvv
*** empty log message ***

Revision 1.1  2013/12/14 17:16:30  rvv
*** empty log message ***

Revision 1.1  2013/06/05 15:56:07  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHSE_L49
{

	function RapportHSE_L49($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HSE";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "";

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
    
    $db=new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."'"
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$db->SQL($query);
		$db->Query();
		$totaalWaarde = $db->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];
    
    
        	$query = "SELECT
			 TijdelijkeRapportage.beleggingscategorie,
       sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
			 FROM TijdelijkeRapportage
			 WHERE       
			 TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY beleggingscategorie";
			debugSpecial($query,__FILE__,__LINE__);

			$db->SQL($query);
			$db->Query();
      while($data = $db->nextRecord())
      {
       $categorieTotalen[$data['beleggingscategorie']]=$data['actuelePortefeuilleWaardeEuro'];
      }
    
    	$query = "SELECT
			 TijdelijkeRapportage.beleggingscategorie,
       TijdelijkeRapportage.beleggingscategorieOmschrijving,
			 TijdelijkeRapportage.fondsOmschrijving,
			 TijdelijkeRapportage.totaalAantal,
			 TijdelijkeRapportage.Valuta as fondsValuta,
			 TijdelijkeRapportage.actueleFonds,
			 TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta,
       TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
       TijdelijkeRapportage.beginPortefeuilleWaardeEuro,
       Rekeningen.Valuta,
       Rekeningen.Portefeuille,
       TijdelijkeRapportage.type
			 FROM TijdelijkeRapportage
			 LEFT Join Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
       LEFT JOIN Rekeningen ON TijdelijkeRapportage.rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
			 WHERE
			 TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde, 
         TijdelijkeRapportage.Lossingsdatum,
          TijdelijkeRapportage.fondspaar desc, 
           TijdelijkeRapportage.Lossingsdatum, Fondsen.OptieBovenliggendFonds,
         TijdelijkeRapportage.type,TijdelijkeRapportage.fondsOmschrijving asc";
			debugSpecial($subquery,__FILE__,__LINE__); 

			$db->SQL($query);
			$db->Query();
      $n=0; 

      while($data = $db->nextRecord())
      {
        if($data['type']=='rekening')
          $data['fondsOmschrijving'].=' '.$data['Portefeuille'].' '.$data['Valuta'];
        $buffer[]=$data;
      }
      
      $regels=3;
      $regelsPerCategorie=array();
      foreach($buffer as $data)
      {
        $regelsPerCategorie[$data['beleggingscategorie']]++;
        if($data['beleggingscategorie'] <> $lastCategorie)
        {
          if($data['beleggingscategorie']=='Liquiditeiten')
            $regelsPerCategorie[$data['beleggingscategorie']]+=2;
          $regels++;
        }
        $lastCategorie=$data['beleggingscategorie'];
        $regels++;
      }
    
      $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);  
      
      if (count($this->pdf->pages) % 2 <> 0)
      { 
        $this->pdf->toonHeader=false;
        $this->pdf->AddPage();
        $this->maakNotities();
        checkPage($this->pdf);
        $this->pdf->toonHeader=true;
      }
           
      $extraNotitie=false; 
      if($regels<=29)
      {
        $this->pdf->toonHeader=false;
        $this->pdf->AddPage();
        $this->maakNotities();
        checkPage($this->pdf);
        $this->pdf->toonHeader=true;
        $this->pdf->AddPage();
      }
      else
      {

        $this->pdf->toonHeader=true;
        $this->pdf->AddPage();
         checkPage($this->pdf);
        //$extraNotitie=false;
      }
      
      $this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1);
      $lastCategorie='';
      foreach($buffer as $data)
      {
        if($data['beleggingscategorie'] <> $lastCategorie)
        {
     //     echo ($regelsPerCategorie[$data['beleggingscategorie']]+1)*$this->pdf->rowHeight+$this->pdf->GetY()+1 ." ".$data['beleggingscategorie']." ".($regelsPerCategorie[$data['beleggingscategorie']]+1)." ".$this->pdf->GetY()." ".$this->pdf->PageBreakTrigger."<br>\n";

          if(($regelsPerCategorie[$data['beleggingscategorie']]+1)* $this->pdf->rowHeight+$this->pdf->GetY()+1 > $this->pdf->PageBreakTrigger)
          {
            $this->pdf->AddPage();
            $this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1);
          }  
          $this->pdf->SetFillColor($this->pdf->achtergrondKop[0],$this->pdf->achtergrondKop[1],$this->pdf->achtergrondKop[2]);
          $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
          $this->pdf->row(array($data['beleggingscategorieOmschrijving'],'','','','','','','','','','','',$this->formatGetal($categorieTotalen[$data['beleggingscategorie']]),'','','',$this->formatGetal($categorieTotalen[$data['beleggingscategorie']]/$totaalWaarde*100,0).'%'));
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        }
        
        if($this->pdf->GetY()+4 > $this->pdf->PageBreakTrigger)
          $this->pdf->AddPage();
   
        if($data['beleggingscategorie']=='Liquiditeiten')
        {
     
          if($this->pdf->lastPOST['anoniem']==1)
          {
            continue;
            //$data['fondsOmschrijving']=preg_replace('/[0-9]/','',$data['fondsOmschrijving']);
            //$data['fondsOmschrijving']=preg_replace('/  /',' ',$data['fondsOmschrijving']);
          }
     
        }
        $n=$this->switchColor($n);  
        $this->pdf->row(array('','',$data['fondsOmschrijving'],'',
        $this->formatGetal($data['totaalAantal'],0),'',
        $this->formatGetal($data['actueleFonds'],2),'',
        $data['fondsValuta'],'',
        $this->formatGetal($data['actuelePortefeuilleWaardeInValuta']),'',
        $this->formatGetal($data['actuelePortefeuilleWaardeEuro']),'',
        $this->formatGetal($data['beginPortefeuilleWaardeEuro']),'',
        $this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100,0).'%'));
        $lastCategorie=$data['beleggingscategorie'];
        
      }
      $this->pdf->Ln();
      $this->pdf->SetFillColor($this->pdf->achtergrondTotaal[0],$this->pdf->achtergrondTotaal[1],$this->pdf->achtergrondTotaal[2]);
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('Totaal','','','','','','','','','','','',$this->formatGetal($totaalWaarde),'','','',$this->formatGetal(100,0).'%'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      unset($this->pdf->fillCell);
      
      if($extraNotitie==true)
      {
        if($this->pdf->GetY()+25 < $this->pdf->PageBreakTrigger)
        {
          $this->pdf->Ln();
          $this->maakNotities(true);
        }
      }
      checkPage($this->pdf);
      
  
  }
  

  function maakNotities($gebruikY=false)
  {
    if($gebruikY==true)
      $startY=$this->pdf->GetY();
    else  
      $startY=$this->pdf->rapportYstart;
    
    
    //$stappen=25;
    $yStart=$startY+9;
    $yStop=210-$this->pdf->margeOnder;
    //$stap=($yStop-$yStart)/$stappen;
    $stap=6.84;
      
    $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize+2);
    $this->pdf->setY($startY+2); 
  	$this->pdf->SetX($this->pdf->marge);
		$this->pdf->Cell(150,4,'Notities', 0, "L");
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array($this->pdf->achtergrondlijn[0],$this->pdf->achtergrondlijn[1],$this->pdf->achtergrondlijn[2]),'dash'=>0));

    $w=297-$this->pdf->marge;
    $stappen=($yStop-$yStart)/$stap;
   // echo $stapen."<br>\n";
    for($i=0;$i<=$stappen;$i++)
    {
      $this->pdf->Line($this->pdf->marge,$yStart+$i*$stap,$w,$yStart+$i*$stap);
    }
  }
  
  function switchColor($n)
  {
    $col1=$this->pdf->achtergrondLicht;
    $col2=$this->pdf->achtergrondDonker;
    $this->pdf->fillCell = array(1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1);
    if($n%2==0)
      $this->pdf->SetFillColor($col1[0],$col1[1],$col1[2]);
    else
      $this->pdf->SetFillColor($col2[0],$col2[1],$col2[2]);
      
      $n++;
      return $n;
  }
  
  
  
  
}



?>