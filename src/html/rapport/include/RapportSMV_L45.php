<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2017/03/04 19:21:42 $
 		File Versie					: $Revision: 1.12 $

 		$Log: RapportSMV_L45.php,v $
 		Revision 1.12  2017/03/04 19:21:42  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/10/16 15:14:53  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/05 16:19:00  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/09/11 08:30:02  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/03/27 17:33:44  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2016/03/09 17:24:31  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2014/09/06 15:24:17  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2014/08/02 15:25:09  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2014/07/27 11:31:03  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2014/07/23 15:44:04  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/07/19 14:27:59  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/07/16 16:01:16  rvv
 		*** empty log message ***
 		
 		
*/
class RapportSMV_L45
{
  function RapportSMV_L45($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
  {
   	$this->pdf = &$pdf;
		$this->pdf->rapport_type = "SMV";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Saldomutatieverloop";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $this->pdf->excelData[]=array("Boekdatum","Rekeningvaluta","Beleggingscategorie","Aan/Verkoop",'Fonds','Saldo r/c',"Fondsmutaties","Gekochte/verkochte rente","gererealiseerd resultaat (ytd)",
     "Transactie kosten","Dividend","Rente Obligaties","Bronheffing","Bewaarloon","Beheervergoeding","Rente","stortingen/onttrekkingen");
  }

  	function formatGetal($waarde, $dec)
	{
	  if(round($waarde,2)== 0.00)
	    return '';
		return number_format($waarde,$dec,",",".");
	}

  function writeRapport()
  {
    global $__appvar;

    $w=(297-$this->pdf->marge*2)/16;
    for($i=0;$i<=16;$i++)
    {
      if($i==2)
        $widths[]=$w*2;
      else
        $widths[]=$w;
      if($i<3)
        $aligns[]='L';
      else
        $aligns[]='R';  
    }
    //listarray($widths);
    $widths[0]=$widths[0]-5;
    $widths[1]=$widths[1]-5;
    $widths[3]=$widths[3]+3;
    $widths[4]=$widths[4]+3;
    $widths[6]=$widths[6]+2;
    $widths[14]=$widths[14]+3;
    
    $this->pdf->setWidths($widths);
		$this->pdf->setAligns($aligns) ;
    
    $fontsizeBackup=$this->pdf->rapport_fontsize;
    $this->pdf->rapport_fontsize=$this->pdf->rapport_fontsize-2;
    $afronding=2;

    $this->pdf->AddPage();
      
    $this->pdf->templateVars['SMVPaginas']=$this->pdf->page;

    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);

		$DB = new DB();
    $DB2 = new DB();
	  $DB->SQL("SELECT DISTINCT(Grootboekrekening) AS Grootboekrekening  FROM Grootboekrekeningen  ORDER BY Grootboekrekening ");
    $DB->Query();
    while($gbData = $DB->nextRecord())
      $grootboeken[] = strtoupper($gbData['Grootboekrekening']);

    $queryGrootboekSom='';
    //foreach($grootboeken as $grootBoek)
    //   $queryGrootboekSom.="sum(if(Rekeningmutaties.Grootboekrekening='$grootBoek',Rekeningmutaties.Bedrag,0)) as Gb$grootBoek,\n";
    //echo $queryGrootboekSom;exit;
    $query="SELECT Distinct(Rekeningmutaties.Rekening), Rekeningen.Valuta as valuta FROM Rekeningmutaties JOIN Rekeningen on Rekeningen.Rekening=Rekeningmutaties.Rekening WHERE Rekeningmutaties.Verwerkt = '1' AND
Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND Rekeningen.Portefeuille='".$this->portefeuille."' AND memoriaal=0";
    $DB->SQL($query);
		$DB->Query();
    $rekeningenPerValuta=array();
		while($data = $DB->nextRecord())
    {
      $rekeningen[$data['Rekening']] = $data;
      $rekeningenPerValuta[$data['valuta']][$data['Rekening']]=$data;
    }

    $nieuweStart=date('Y-m-d',$this->pdf->rapport_datumvanaf);
    if(substr($nieuweStart,5,5)=='01-01')
      $startJaar=true;
    else
      $startJaar=false;
        
    $fondsRegels=berekenPortefeuilleWaarde($this->portefeuille,$nieuweStart,$startJaar);
    $startSaldo=array();
    foreach($fondsRegels as $regel)
    {
      if($regel['type']=='rekening')
      { 
        $regel['saldo']=+$regel['actuelePortefeuilleWaardeEuro'];
        $startSaldo[$regel['valuta']]+=$regel['actuelePortefeuilleWaardeInValuta'];
		    $rekeningen[$regel['rekening']]=$regel;
        $rekeningenPerValuta[$regel['valuta']][$regel['rekening']]=$regel;
      }
    }
    


		$query = "SELECT
    sum(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) as saldo ,TijdelijkeRapportage.rekening,valuta
    FROM
    TijdelijkeRapportage
    WHERE
    TijdelijkeRapportage.rapportageDatum =  '".$this->rapportageDatum."' AND
    TijdelijkeRapportage.portefeuille =  '".$this->portefeuille."' AND
    TijdelijkeRapportage.`type` =  'rekening'
    ".$__appvar['TijdelijkeRapportageMaakUniek']." 
    GROUP BY TijdelijkeRapportage.rekening";

    $DB->SQL($query);
		$DB->Query();
		while($data = $DB->nextRecord())
		{
		  $eindSaldo[$data['valuta']]+=$data['saldo'];
		  $rekeningen[$data['rekening']]=$data;
      $rekeningenPerValuta[$data['valuta']][$data['rekening']]=$data;
		}

    ksort($rekeningen);

		$beginJaar = date("Y", $this->pdf->rapport_datumvanaf);
	  $jaar = date("Y", $this->pdf->rapport_datum);

  	if ($beginJaar != '1970' && $jaar != $beginJaar)
  	{
  	  for($jaren=$beginJaar;$jaren <= $jaar; $jaren++)
  	  {
  	    if(isset($jarenString))
  	    {
  	      $jarenString .= ",'$jaren'";
          if(isset($januariUitluiten))
            $januariUitluiten .=",'$jaren-01-01 00:00:00'";
          else
            $januariUitluiten .= "'$jaren-01-01 00:00:00'";
	      }
	      else
          $jarenString .= "'$jaren'";
 	    }

 	   	$boekjarenFilter = "AND ( YEAR(Rekeningmutaties.Boekdatum) IN ($jarenString) ) ";
	    $januariFilter = "AND Rekeningmutaties.Boekdatum NOT IN ($januariUitluiten) ";
	  }

foreach($rekeningenPerValuta as $valuta=>$rekeningen)
{


  	$this->pdf->Row(array(date('d-M',$this->pdf->rapport_datumvanaf-86400),'','Begin saldo '.$valuta,$this->formatGetal($startSaldo[$valuta],0)));
    $this->pdf->excelData[]=array(date('d-m-Y',$this->pdf->rapport_datumvanaf-86400),$valuta,'','','Begin saldo '.$valuta,round($startSaldo[$valuta],$afronding));



$query="SELECT 
Rekeningmutaties.Afschriftnummer,
Rekeningmutaties.Valuta, Rekeningmutaties.Boekdatum, Rekeningmutaties.Omschrijving, (Rekeningmutaties.Transactietype) as Transactietype,
Rekeningmutaties.Bedrag as Bedrag , Rekeningmutaties.Aantal, Rekeningmutaties.Debet AS Debet, Rekeningmutaties.Credit AS Credit,
 Rekeningmutaties.Valutakoers, Rekeningmutaties.Rekening, Rekeningmutaties.Fonds, Rekeningmutaties.Grootboekrekening, Rekeningmutaties.Transactietype, Rekeningmutaties.Afschriftnummer, 
Grootboekrekeningen.Omschrijving AS gbOmschrijving, Grootboekrekeningen.Opbrengst, Grootboekrekeningen.Kosten, Grootboekrekeningen.Afdrukvolgorde,
 BeleggingscategoriePerFonds.Beleggingscategorie, CategorienPerHoofdcategorie.Hoofdcategorie, Beleggingscategorien.Omschrijving as hoofdcategorieOmschrijving,
 if( ISNULL(Beleggingscategorien.Afdrukvolgorde),300,Beleggingscategorien.Afdrukvolgorde) as hVolgorde 
FROM Rekeningmutaties 
JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
LEFT JOIN BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE Rekeningmutaties.Rekening IN('".implode("','",array_keys($rekeningen))."')  $boekjarenFilter $januariFilter AND Rekeningmutaties.Verwerkt = '1' 
AND Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' $extraquery
AND (Grootboekrekeningen.Kosten=1 OR Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Onttrekking=1 OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Kruispost=1 OR Grootboekrekeningen.FondsAanVerkoop=1)
ORDER BY hVolgorde,Rekeningmutaties.Boekdatum ,Rekeningmutaties.Omschrijving

";

		$DB->SQL($query);
		$DB->Query();
		$mutatieData = array();
    $saldoOpDatum=array();
		$newSaldo = $startSaldo[$valuta];
    $hoofdcategorieOmschijvingen=array('Liquiditeiten'=>'Liquiditeiten');
		while($mutaties = $DB->nextRecord())
		{

		  $hoofdcategorieOmschijvingen[$mutaties['Hoofdcategorie']]=$mutaties['hoofdcategorieOmschrijving'];
		  $totaal=array();
      if($mutaties['Fonds']=='')
        $mutaties['Fonds']='Liquiditeiten';
        
      if($mutaties['Hoofdcategorie']=='')
        $mutaties['Hoofdcategorie']='Liquiditeiten';

      if($mutaties['Beleggingscategorie']=='')
        $mutaties['Beleggingscategorie']='Liquiditeiten';
        
      $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['Boekdatum']=$mutaties['Boekdatum'];
      if($mutaties['Transactietype']<>'')
        if(isset($mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['Transactietype']))
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['Transactietype'].=' '.$mutaties['Transactietype'];
        else
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['Transactietype']=$mutaties['Transactietype'];
        
      $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['Rekening']=$mutaties['Rekening'];  
      switch ($mutaties['Grootboekrekening'])
	    {
		    case "BEH" :
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['BEH']+=$mutaties['Bedrag'];
        break;
        case "BEW" :
           $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['BEW']+=$mutaties['Bedrag'];
        break;
		    case "DIV" :
           $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['DIV']+=$mutaties['Bedrag'];
        break;
        case "DIVBE" :
           $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['DIVBE']+=$mutaties['Bedrag'];
        break;
        case "STORT" :
        case "ONTTR" :
           $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['STORT']+=$mutaties['Bedrag'];
        break;        
        case "FONDS" :
           $resultaat=0;
           $beginWaardeEur=$mutaties['Bedrag'];
           if(substr($mutaties['Transactietype'],0,1)=='V' || $mutaties['Transactietype']=='A/S' || $mutaties['Transactietype']=='L')
           {
      	    	$historie = berekenHistorischKostprijs($this->portefeuille,$mutaties['Fonds'], $mutaties['Boekdatum'],$this->pdf->rapportageValuta);
       		    $beginWaardeEur  = -1* $mutaties['Aantal'] * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar']  * $historie['fondsEenheid'];
              $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['RESULTAAT']+=($mutaties['Bedrag']-$beginWaardeEur);
           }
           $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['FONDS']+=($beginWaardeEur);
        break;
        case "KNBA" :
        case "KOBU" :
        case "KOST" :
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['KOST']+=$mutaties['Bedrag'];
        break;
        case "RENTE" :
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['RENTE']+=$mutaties['Bedrag'];
        break;  
        case "RENME" :
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['RENME']+=$mutaties['Bedrag'];
        break; 
        case "RENOB" :
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['RENOB']+=$mutaties['Bedrag'];
        break;      
        default:
          $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['VALUTAKOERS']+=$mutaties['Bedrag'];
      }
		  $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['TOTAAL']+=$mutaties['Bedrag']; 
		  $newSaldo = $newSaldo + $mutaties['Bedrag'];
		  $saldoOpDatum[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']] = $newSaldo;
      $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['NEWSALDO']=$newSaldo;
      $mutatieData[$mutaties['Hoofdcategorie']][$mutaties['Boekdatum']][$mutaties['Afschriftnummer']][$mutaties['Fonds']]['Beleggingscategorie']=$mutaties['Beleggingscategorie'];
		}

//listarray($mutatieData);
    $velden=array('TOTAAL','FONDS','RENME','RESULTAAT','KOST','DIV','RENOB','DIVBE','BEW','BEH','RENTE','STORT');//,'VALUTAKOERS'
		unset($lastDatum);
		$n=0;
		//$aantal = count($mutatieData);
		foreach ($mutatieData as $hoofdCategorie=>$hoofdCategorieData)
		{
		  $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
		  $this->pdf->Cell(100,4,$hoofdcategorieOmschijvingen[$hoofdCategorie],0,1,'L');
      $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
		  foreach($hoofdCategorieData as $boekdatum=>$afschriften)
      {
        foreach($afschriften as $afschrift=>$fondsen)
        {
		      foreach($fondsen as $fonds=>$fondsData)
          {
		  	   $newSaldo =  $saldoOpDatum[$hoofdCategorie][$fondsData['Boekdatum']];
           $newSaldoTxt = $this->formatGetal($saldoOpDatum[$hoofdCategorie][$fondsData['Boekdatum']],$afronding);
           
           $tmp=array(date("d-M",db2jul($fondsData['Boekdatum'])),$fondsData['Transactietype'],$fonds);
           $tmpXls=array(date("d-m-Y",db2jul($fondsData['Boekdatum'])),$valuta,$fondsData['Beleggingscategorie'],$fondsData['Transactietype'],$fonds);
           
           foreach($velden as $veld)
           {
             if($veld=='TOTAAL')
             {
                $correctie=1; 
               // array_push($tmp,$this->formatGetal($fondsData['NEWSALDO']));
                $waarde=$fondsData[$veld]*$correctie; 
             }   
             else
             {
                $correctie=-1; 
                $waarde=$fondsData[$veld]*$correctie; 
                
             }
             array_push($tmp,$this->formatGetal($waarde,$afronding)); 
             array_push($tmpXls,round($waarde,$afronding)); 
            // if($veld=='RESULTAAT')
           //    echo $hoofdCategorie." ".$fonds." ".$fondsData[$veld]."<br>\n";
             $totalen[$veld]+=$fondsData[$veld]*$correctie;
           }
            if(1)//round($fondsData['TOTAAL'],1) <> 0.0)
            {
              $this->pdf->excelData[]=$tmpXls;
              $this->pdf->Row($tmp);
            }

	        $n++;
          }
        }
      }
    // listarray($totalen);
      $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
      $this->pdf->Cell(100,4,"Totaal ".$hoofdcategorieOmschijvingen[$hoofdCategorie],0,0,'L');
      $this->pdf->SetX($this->pdf->marge);
      $tmp= array('','','');
      $tmpXls= array('','','','');
      foreach($velden as $veld)
      {
        if(round($totalen[$veld],1) == 0)
          $totalen[$veld]=0;
        array_push($tmp,$this->formatGetal($totalen[$veld],$afronding));
        array_push($tmpXls,round($totalen[$veld],$afronding));
      } 
      $this->pdf->Row($tmp);
     // $this->pdf->excelData[]=$tmpXls;
       $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
       $this->pdf->Ln();
       $totalen=array();

		}
    
    $this->pdf->Row(array(date('d-M',$this->pdf->rapport_datum),'','Eind saldo '.$valuta,$this->formatGetal($eindSaldo[$valuta],$afronding)));
    $this->pdf->excelData[]=array(date('d-m-Y',$this->pdf->rapport_datum),$valuta,'','','Eind saldo '.$valuta,round($eindSaldo[$valuta],$afronding));
    $this->pdf->ln();
    if(round($eindSaldo[$valuta],2) != round($newSaldo,2))
		{

		  $this->pdf->SetFont($this->pdf->rapport_font,"B",$this->pdf->rapport_fontsize);
		  $this->pdf->MultiCell(200,4, "Begin en eindwaarden voor portefeuille $rekening komen niet overeen met de verwachte waarde. (".$this->formatGetal($eindSaldo[$valuta],2)." != ".$this->formatGetal($newSaldo,2)." verschil: ".$this->formatGetal($newSaldo-$eindSaldo[$valuta],2).")", 0, "L");
      $this->pdf->excelData[]=array("Begin en eindwaarden voor portefeuille $rekening komen niet overeen met de verwachte waarde. (".$this->formatGetal($eindSaldo[$valuta],2)." != ".$this->formatGetal($newSaldo,2)." verschil: ".$this->formatGetal($newSaldo-$eindSaldo[$valuta],2).")");
      $this->pdf->SetFont($this->pdf->rapport_font,"",$this->pdf->rapport_fontsize);
		  //  echo "<script> alert('Waarden SMV rapport voor portefeuille ".$this->portefeuille." komen niet overeen (".$this->formatGetal($eindSaldo['saldo'],2)." - ".$this->formatGetal($newSaldo,2).")'); </script>";flush();
    //  exit;
		}

}


	$this->pdf->rapport_fontsize=$fontsizeBackup;



  }


}
?>