<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/04/13 17:42:49 $
File Versie					: $Revision: 1.1 $

$Log: RapportKERNZ_L67.php,v $
Revision 1.1  2019/04/13 17:42:49  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportKERNZ_L67
{
//RapportPERF_L63
	function RapportKERNZ_L67($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);

		$this->pdf->rapport_titel = "Vermogensontwikkeling";

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

   	$this->pdf->AddPage();
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
    $index=new indexHerberekening();
    $perioden=$index->getJaren(db2jul($this->rapportageDatumVanaf),db2jul($this->rapportageDatum));
 
 
		$y=$this->pdf->getY();
		$x=$this->pdf->getX();
		$this->pdf->setXY($x,$y);
    
    foreach($perioden as $periode)
    {
      $jaar=substr($periode['stop'],0,4);
      //$kwartaal=ceil(date("n",(db2jul($periode['stop'])))/3);
      $data[$jaar]=$this->getPerfWaarden($periode['start'],$periode['stop']); //$kwartaal.'e kwartaal '.
    }

    $data['totaal']=$this->getPerfWaarden($this->rapportageDatumVanaf,$this->rapportageDatum);//.$jaar
   
   
   
    $koppen=array('');
    $koppenXls=array('');
    $tmp=array();
    $tmpXls=array();
    foreach($data as $col=>$regelData)
    {
      $koppen[]=$col;
      $koppenXls[]=$col;
      $n=0;
      foreach($regelData as $omschrijving => $waarde)
      {
        if(!isset($tmp[$n][0]))
        {
          $tmp[$n][0]=$omschrijving;
          $tmpXls[$n][0]=$omschrijving;
        }
        $tmp[$n][]=$this->formatGetal($waarde,2);
        $tmpXls[$n][]=round($waarde,2);
        $n++;
      }
      
		}
    
    $this->pdf->excelData[]=$koppenXls;
    
    $this->pdf->SetWidths(array(82,40,40,40,40,40));
    $this->pdf->ln();
    $this->pdf->SetFillColor(240);
    $this->pdf->Rect($this->pdf->GetX(),$this->pdf->GetY(),82,8,'FD');
    $this->pdf->Rect($this->pdf->GetX()+82,$this->pdf->GetY()+8,5*40,count($tmp)*4,'FD');
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    
    $this->pdf->Rect($this->pdf->GetX()+82,$this->pdf->GetY(),5*40,8,'FD');
    $this->pdf->Rect($this->pdf->GetX(),$this->pdf->GetY()+8,82,count($tmp)*4,'FD');
    
		$this->pdf->SetAligns(array('L','R','R','R','R','R'));
	  $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($koppen);
    $this->pdf->Ln(4);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$backup=$this->pdf->PageBreakTrigger;
		$this->pdf->PageBreakTrigger=200;
    foreach($tmp as $row)
      $this->pdf->row($row);
    foreach($tmpXls as $row)
      $this->pdf->excelData[]=$row;
		$this->pdf->PageBreakTrigger=$backup;
   
   // $this->addPerf($this->rapportageDatumVanaf,$this->rapportageDatum,0);
   // $this->pdf->setXY($x,$y);
	//	$this->addPerf(substr($this->rapportageDatumVanaf,0,4)."-01-01",$this->rapportageDatum,130);

	}
  
  function getPerfWaarden($vanaf,$tot)
  {
    if(substr($vanaf,5,5)=='01-01')
      $minDag=true;
    else
      $minDag=false;  
    $waardenBegin=berekenPortefeuilleWaarde($this->portefeuille,$vanaf,$minDag,'EUR',$vanaf);

    foreach($waardenBegin as $idex=>$waarden)
    {
      if($waarden['type']=='rente' && $waarden['fonds']=='')
      {
        $waarden['type']='rekeningRente';
      }
      $verdeling[$vanaf][$waarden['type']]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $verdeling[$vanaf][$waarden['type']]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $verdeling[$vanaf][$waarden['type']]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);
      $totaal[$vanaf]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $totaal[$vanaf]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $totaal[$vanaf]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);

    }
    if(substr($tot,5,5)=='01-01')
      $minDag=true;
    else
      $minDag=false;
    $waardenEind=berekenPortefeuilleWaarde($this->portefeuille,$tot,$minDag,'EUR',$vanaf);

    foreach($waardenEind as $idex=>$waarden)
    {
      if($waarden['type']=='rente' && $waarden['fonds']=='')
      {
        $waarden['type']='rekeningRente';
      }
      $verdeling[$tot][$waarden['type']]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $verdeling[$tot][$waarden['type']]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $verdeling[$tot][$waarden['type']]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);
      $totaal[$tot]['actuelePortefeuilleWaardeEuro']+=$waarden['actuelePortefeuilleWaardeEuro'];
      $totaal[$tot]['beginPortefeuilleWaardeEuro']+=$waarden['beginPortefeuilleWaardeEuro'];
      $totaal[$tot]['ongerealiseerdeKoersResultaat']+=($waarden['actuelePortefeuilleWaardeEuro']-$waarden['beginPortefeuilleWaardeEuro']);

    }
    $toonWaarden['Begin vermogen']=$totaal[$vanaf]['actuelePortefeuilleWaardeEuro']*-1;
    $toonWaarden['Eind vermogen']=$totaal[$tot]['actuelePortefeuilleWaardeEuro'];
    $toonWaarden['Begin effecten']=$verdeling[$vanaf]['fondsen']['actuelePortefeuilleWaardeEuro']*-1;
    $toonWaarden['Eind effecten']=$verdeling[$tot]['fondsen']['actuelePortefeuilleWaardeEuro']; 
    $toonWaarden['Begin saldo opgelopen rente']=$verdeling[$vanaf]['rente']['actuelePortefeuilleWaardeEuro']*-1; 
    $toonWaarden['Eind saldo opgelopen rente']=$verdeling[$tot]['rente']['actuelePortefeuilleWaardeEuro']; 
    $toonWaarden['Begin liquiditeiten']=$verdeling[$vanaf]['rekening']['actuelePortefeuilleWaardeEuro']*-1;
    $toonWaarden['Eind liquiditeiten']=$verdeling[$tot]['rekening']['actuelePortefeuilleWaardeEuro']; 
    $toonWaarden['Begin opgelopen rente liquiditeiten']=$verdeling[$vanaf]['rekeningRente']['actuelePortefeuilleWaardeEuro']*-1; 
    $toonWaarden['Eind opgelopen rente liquiditeiten']=$verdeling[$tot]['rekeningRente']['actuelePortefeuilleWaardeEuro']; 

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers - Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaal
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Transactietype IN('A','A/O','A/S')";
		$DB = new DB();
		$DB->SQL($query);
    $tmp=$DB->lookupRecord();
    $toonWaarden['Totaal aankopen']=$tmp['totaal']; 
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers - Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaal
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Rekeningmutaties.Grootboekrekening = 'FONDS' AND Rekeningmutaties.Transactietype IN('V','V/O','V/S')";
		$DB = new DB();
		$DB->SQL($query);
    $DB->lookupRecord();
    $tmp=$DB->lookupRecord();
    $toonWaarden['Totaal verkopen']=$tmp['totaal'];
  
  
    $perGrootboek=array();
    $grootboekrekeningen=array();
    $query = "SELECT Grootboekrekeningen.Grootboekrekening, Grootboekrekeningen.Omschrijving
FROM Grootboekrekeningen
JOIN KeuzePerVermogensbeheerder ON Grootboekrekeningen.Grootboekrekening=KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Grootboekrekeningen.Opbrengst = '1' OR Kosten = '1' ORDER BY Grootboekrekeningen.Opbrengst,Grootboekrekeningen.Kosten,KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    while($opbrengst = $DB->nextRecord())
    {
      $perGrootboek[$opbrengst['Omschrijving']] = 0;
      $grootboekrekeningen[$opbrengst['Grootboekrekening']]=$opbrengst['Omschrijving'];
    }
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, 
		  	SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet,
		  	Grootboekrekeningen.Grootboekrekening,
        Grootboekrekeningen.Omschrijving 
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
        JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Grootboekrekeningen.Opbrengst = '1' GROUP BY Grootboekrekeningen.Omschrijving ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
   	while($opbrengst = $DB->nextRecord())
		{
			$perGrootboek[$opbrengst['Omschrijving']] +=  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet'])*-1;
      $grootboekrekeningen[$opbrengst['Grootboekrekening']]=$opbrengst['Omschrijving'];
		}
    
    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers ) AS totaalcredit, 
		  	SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaaldebet,
		  	Grootboekrekeningen.Grootboekrekening,
        Grootboekrekeningen.Omschrijving 
		  	FROM Rekeningmutaties
        JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille 
        JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
		  	WHERE 
		  	Rekeningen.Portefeuille = '".$this->portefeuille."' AND 
		  	Rekeningmutaties.Verwerkt = '1' AND 
		  	Rekeningmutaties.Boekdatum > '".$vanaf."' AND 
		  	Rekeningmutaties.Boekdatum <= '".$tot."' AND 
			  Grootboekrekeningen.Kosten = '1' GROUP BY Grootboekrekeningen.Omschrijving ";
		$DB->SQL($query);
		$DB->Query();        
    while($kosten = $DB->nextRecord())
		{
		  $perGrootboek[$kosten['Omschrijving']] +=  ($kosten['totaalcredit']-$kosten['totaaldebet']);
      $grootboekrekeningen[$kosten['Grootboekrekening']]=$kosten['Omschrijving'];
		}
  

		foreach($grootboekrekeningen as $grootboekrekening=>$grootboekOmschrijving)
    {
		  if($grootboekrekening=='HUUR' || $grootboekrekening=='OG')
      {
        if(round($perGrootboek[$grootboekOmschrijving],1) <> 0.0)
          $toonWaarden[$grootboekOmschrijving]=$perGrootboek[$grootboekOmschrijving];
      }
      else
		    $toonWaarden[$grootboekOmschrijving]=$perGrootboek[$grootboekOmschrijving];
    }
    
    $waardeMutatie = $toonWaarden['Eind vermogen'] - $toonWaarden['Begin vermogen'];
		$toonWaarden['Stortingen'] 	 = getStortingen($this->portefeuille,$vanaf,$tot,$this->pdf->rapportageValuta)*-1;
		$toonWaarden['Onttrekkingen']= getOnttrekkingen($this->portefeuille,$vanaf,$tot,$this->pdf->rapportageValuta);
		//$toonWaarden['Resultaat verslagperiode'] = $waardeMutatie - $toonWaarden['Stortingen'] + $toonWaarden['Onttrekkingen'];
    
    $ongerealiseerd=$verdeling[$tot]['fondsen']['actuelePortefeuilleWaardeEuro']-$verdeling[$tot]['fondsen']['beginPortefeuilleWaardeEuro'];
    $gerealiseerd=gerealiseerdKoersresultaat($this->portefeuille, $vanaf, $tot,$this->pdf->rapportageValuta,true);
    $toonWaarden['Ongerealiseerd koers resultaat']=$ongerealiseerd;
    $toonWaarden['Gerealiseerd koers resultaat']=$gerealiseerd;
    $toonWaarden['Ongerealiseerd koers resultaat ']=$ongerealiseerd*-1;
    $toonWaarden['Gerealiseerd koers resultaat ']=$gerealiseerd*-1;
        
    return $toonWaarden;
  }

}
?>