<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/05/05 19:41:36 $
File Versie					: $Revision: 1.17 $

$Log: RapportPERF_L70.php,v $
Revision 1.17  2018/05/05 19:41:36  rvv
*** empty log message ***

Revision 1.16  2018/03/31 18:06:01  rvv
*** empty log message ***

Revision 1.15  2018/03/25 10:16:55  rvv
*** empty log message ***

Revision 1.14  2018/02/03 18:54:04  rvv
*** empty log message ***

Revision 1.13  2017/11/04 17:40:21  rvv
*** empty log message ***

Revision 1.12  2017/11/02 07:39:42  rvv
*** empty log message ***

Revision 1.11  2017/11/01 16:51:06  rvv
*** empty log message ***

Revision 1.10  2016/09/04 14:42:06  rvv
*** empty log message ***

Revision 1.9  2016/07/20 16:12:00  rvv
*** empty log message ***

Revision 1.8  2016/07/07 15:37:35  rvv
*** empty log message ***

Revision 1.7  2016/07/07 07:44:38  rvv
*** empty log message ***

Revision 1.6  2016/07/06 16:09:31  rvv
*** empty log message ***

Revision 1.5  2016/06/29 16:04:07  rvv
*** empty log message ***

Revision 1.4  2016/06/15 15:58:41  rvv
*** empty log message ***

Revision 1.3  2016/06/12 10:20:31  rvv
*** empty log message ***

Revision 1.2  2016/06/08 15:40:53  rvv
*** empty log message ***

Revision 1.1  2016/05/22 18:49:26  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L70.php");

class RapportPERF_L70
{

	function RapportPERF_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		global $__appvar;
		$this->__appvar = $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERF";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->underlinePercentage=1;

		if($this->pdf->rapport_PERF_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERF_titel;
		else
			$this->pdf->rapport_titel = "Performancemeting (in ".$this->pdf->rapportageValuta.")";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  if(strval($pdf->rapport_ATT_decimaal) != '')
	    $this->bedragDecimalen=$pdf->rapport_ATT_decimaal;
	  else
	    $this->bedragDecimalen=2;

	  $this->db = new DB();


	  $this->categorien=array('totaal'=>'Totaal');

/*
    $query="SELECT 
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving 
FROM KeuzePerVermogensbeheerder 
JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie
WHERE 
KeuzePerVermogensbeheerder.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
KeuzePerVermogensbeheerder.categorie='Beleggingscategorien'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde ";*/
    $query="SELECT
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie
FROM
Beleggingscategorien
JOIN CategorienPerHoofdcategorie ON  Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie 
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' 
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Beleggingscategorien.Afdrukvolgorde
";


    $query="SELECT AttributieCategorie as Beleggingscategorie,AttributieCategorieOmschrijving as Omschrijving, AttributieCategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND AttributieCategorie <>'' GROUP BY AttributieCategorieVolgorde  ORDER BY AttributieCategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')

    $this->db->SQL($query);
    $this->db->Query();
    while($data=$this->db->nextRecord())
    {
      $this->categorien[$data['Beleggingscategorie']]=$data['Omschrijving'];
    }

	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		  return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $RapStopJaar = date("Y", db2jul($this->rapportageDatum));
    if($RapStartJaar != $RapStopJaar)
      $this->tweedePerformanceStart = "$RapStopJaar-01-01";
    else
    {
	    if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	    elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	      $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	    else
	     $this->tweedePerformanceStart = "$RapStartJaar-01-01";
    }
	}


  function createRows()
  {
    $this->waarden=$this->pdf->hcatData;

    $row['waardeVanaf'] = array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $row['waardeTot'] = array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $row['mutatiewaarde'] = array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $row['totaalStortingen'] = array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['totaalOnttrekkingen'] = array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['totaalMutaties'] = array("",vertaalTekst("Effectenmutaties gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['directeOpbrengsten'] = array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
    $row['toegerekendeKosten'] = array("",vertaalTekst("Toegerekende kosten",$this->pdf->rapport_taal));
    $row['resultaatVerslagperiode'] = array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcent'] = array("",vertaalTekst("Totaal netto-rendement",$this->pdf->rapport_taal));
    $row['rendementProcentBruto']= array("",vertaalTekst("Rendement bruto",$this->pdf->rapport_taal));
    $row['totaalNettoRentement']= array("",vertaalTekst("Netto rendement",$this->pdf->rapport_taal));
    $row['resultaatBruto']= array("",vertaalTekst("Bruto resultaat",$this->pdf->rapport_taal));
    //$row['rendementProcentJaar'] = array("",vertaalTekst("Rendement over lopende jaar",$this->pdf->rapport_taal));
    $row['gerealiseerdKoersresultaat'] = array("",vertaalTekst("gerealiseerdKoersresultaat",$this->pdf->rapport_taal));
    $row['ongerealiseerdeKoersResultaaten'] = array("",vertaalTekst("ongerealiseerdeKoersResultaaten",$this->pdf->rapport_taal));
    $row['opgelopenRentes'] = array("",vertaalTekst("opgelopenRentes",$this->pdf->rapport_taal));
    $row['totaal'] = array("",vertaalTekst("Totaal Performance",$this->pdf->rapport_taal));


    $this->pdf->row(array(""));
    $kopRegel = array();
	  array_push($kopRegel,"");
	  array_push($kopRegel,"");
    
    

    
    foreach ($this->categorien as $categorie=>$omschrijving)
    {
		  array_push($kopRegel,vertaalTekst($omschrijving,$this->pdf->rapport_taal));
		  array_push($kopRegel,"");
    }
		$this->pdf->row($kopRegel);


    $liquiditeitenMutatie=0;
    foreach ($this->categorien as $categorie=>$omschrijving)
    {
       if($categorie <> 'totaal' && $categorie <> 'Liquiditeiten' && $categorie <> 'H-Liq')
       {
         $liquiditeitenMutatie+=$this->waarden['rapportagePeriode'][$categorie]['opbrengst']*-1;
         $liquiditeitenMutatie+=$this->waarden['rapportagePeriode'][$categorie]['storting']+$this->waarden['rapportagePeriode'][$categorie]['onttrekking'];
       }
       if($categorie == 'totaal')
         $liquiditeitenMutatie+=$this->waarden['rapportagePeriode'][$categorie]['kosten']*-1;
    }
    foreach ($this->categorien as $categorie=>$omschrijving)
    {
      $this->waarden['rapportagePeriode'][$categorie]['mutatie']=$this->waarden['rapportagePeriode'][$categorie]['eindwaarde']-$this->waarden['rapportagePeriode'][$categorie]['beginwaarde'];

      if($categorie=='totaal')
      {
       // $this->waarden['rapportagePeriode'][$categorie]['kosten']=0;
       // $this->waarden['rapportagePeriode'][$categorie]['opbrengst']=0;
        $this->waarden['rapportagePeriode'][$categorie]['storting']=getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking']=getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
        
        $resultaatVerslagperiode[$categorie]=$this->waarden['rapportagePeriode'][$categorie]['resultaat'];
        /*
        $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode'][$categorie]['mutatie'] -
        $this->waarden['rapportagePeriode'][$categorie]['storting'] +
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking'] +
        $this->waarden['rapportagePeriode'][$categorie]['kosten'] +
        $this->waarden['rapportagePeriode'][$categorie]['opbrengst'];
        */
      
       array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting'],$this->bedragDecimalen));
       array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking'],$this->bedragDecimalen));
       array_push($row['totaalStortingen'],"");
       array_push($row['totaalOnttrekkingen'],"");
       array_push($row['totaalMutaties'],"");
       array_push($row['totaalMutaties'],"");
      }
      else
      {
        $this->waarden['rapportagePeriode'][$categorie]['storting']=$this->waarden['rapportagePeriode'][$categorie]['storting']*-1;
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking']=$this->waarden['rapportagePeriode'][$categorie]['onttrekking']*-1;


        if($categorie == 'H-Liq')
        {
          $liqOnttrekkingen=getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
          $liqStorting=getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
          array_push($row['totaalOnttrekkingen'],$this->formatGetal($liqOnttrekkingen,$this->bedragDecimalen));
          array_push($row['totaalStortingen'],$this->formatGetal($liqStorting,$this->bedragDecimalen));
          array_push($row['totaalStortingen'],"");
          array_push($row['totaalOnttrekkingen'],"");
        }
        elseif($categorie=='Liq-Extern')
        {
          array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting']*-1,$this->bedragDecimalen));
          array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking']*-1,$this->bedragDecimalen));
         
          array_push($row['totaalStortingen'],"");
          array_push($row['totaalOnttrekkingen'],"");
        }
        else
        {
          array_push($row['totaalStortingen'],"");
          array_push($row['totaalOnttrekkingen'],"");
          array_push($row['totaalStortingen'],"");
          array_push($row['totaalOnttrekkingen'],"");
        }
        //listarray($this->waarden['rapportagePeriode']['Liquiditeiten']);exit;
        $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode'][$categorie]['mutatie'] +
        $this->waarden['rapportagePeriode'][$categorie]['storting'] +
        $this->waarden['rapportagePeriode'][$categorie]['onttrekking'] +
        $this->waarden['rapportagePeriode'][$categorie]['kosten'] +
        $this->waarden['rapportagePeriode'][$categorie]['opbrengst']+ 
        $this->waarden['rapportagePeriode'][$categorie]['RENMETotaal'];

   
       if($categorie=='Liquiditeiten' || $categorie == 'H-Liq')
          array_push($row['totaalMutaties'],$this->formatGetal($liquiditeitenMutatie,$this->bedragDecimalen));
       elseif($categorie=='Liq-Extern')
          array_push($row['totaalMutaties'],$this->formatGetal(0,$this->bedragDecimalen));   
       else
          array_push($row['totaalMutaties'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting']+$this->waarden['rapportagePeriode'][$categorie]['onttrekking'],$this->bedragDecimalen));
        array_push($row['totaalMutaties'],"");
      }

      array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['beginwaarde'],$this->bedragDecimalen,true));
      array_push($row['waardeVanaf'],"");

      array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['eindwaarde'],$this->bedragDecimalen));
      array_push($row['waardeTot'],"");

      array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['mutatie'],$this->bedragDecimalen));
      array_push($row['mutatiewaarde'],"");

      if($categorie=='Liquiditeiten' || $categorie=='Liq-Extern' || $categorie == 'H-Liq')
      {
        array_push($row['rendementProcent'],'');
        array_push($row['rendementProcent'],'');

        //array_push($row['rendementProcentJaar'],'');
        //array_push($row['rendementProcentJaar'],'');        
      }
      else
      {
        if($categorie=='totaal')
        {
          array_push($row['totaalNettoRentement'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procent'],2));
          if(isset($this->waarden['rapportagePeriode']['Liq-Extern']))
            array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode']['Liq-Extern']['procentZonderExtLiq'],2));
          else
            array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procent'],2));  
          array_push($row['totaalNettoRentement'],'%');
        }
        else        
        {
          array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procent'],2));
          //array_push($row['totaalNettoRentement'],'');
        }
        array_push($row['rendementProcent'],'%');
        
   
  
        
        //array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar'][$categorie]['procent'],2));
        //array_push($row['rendementProcentJaar'],'%');
      }
           


      if ($categorie == 'totaal')
      {

        array_push($row['directeOpbrengsten'],'');
        array_push($row['toegerekendeKosten'],'');//$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['kosten'],$this->bedragDecimalen));
        array_push($row['resultaatBruto'],'');//$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['resultaatBruto'],$this->bedragDecimalen));
        array_push($row['rendementProcentBruto'],'');   
        array_push($row['rendementProcentBruto'],''); 
      }
      else
      {

        if($categorie=='Liquiditeiten' || $categorie == 'H-Liq')
        {
          array_push($row['rendementProcentBruto'],'');
          array_push($row['rendementProcentBruto'],'');
          //array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['opbrengst'],$this->bedragDecimalen));
          array_push($row['directeOpbrengsten'],'');
//          array_push($row['toegerekendeKosten'],$this->formatGetal(-1*$this->waarden['rapportagePeriode'][$categorie]['resultaatBruto'],$this->bedragDecimalen));
//          array_push($row['resultaatBruto'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['opbrengst']+$this->waarden['rapportagePeriode'][$categorie]['kosten'],$this->bedragDecimalen));
         //listarray($this->waarden['rapportagePeriode'][$categorie]);ob_flush();
          array_push($row['toegerekendeKosten'],$this->formatGetal(-1*$this->waarden['rapportagePeriode'][$categorie]['kosten'],$this->bedragDecimalen));
          //array_push($row['resultaatBruto'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['resultaatBruto'],$this->bedragDecimalen));
          $liqBruto=$this->waarden['rapportagePeriode'][$categorie]['mutatie']+$liqStorting-$liqOnttrekkingen+$liquiditeitenMutatie;
          array_push($row['resultaatBruto'],$this->formatGetal($liqBruto,$this->bedragDecimalen));
          $resultaatVerslagperiode[$categorie]=$liqBruto+$this->waarden['rapportagePeriode'][$categorie]['kosten'];
         //listarray($liquiditeitenMutatie);

        }
        else
        {
          array_push($row['rendementProcentBruto'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procentBruto'],2));
          array_push($row['rendementProcentBruto'],'%');
          array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['opbrengst'],$this->bedragDecimalen));
          array_push($row['toegerekendeKosten'],$this->formatGetal(-1*$this->waarden['rapportagePeriode'][$categorie]['kosten'],$this->bedragDecimalen));
          array_push($row['resultaatBruto'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['resultaatBruto'],$this->bedragDecimalen));

        }
         
      }
      array_push($row['directeOpbrengsten'],"");
      array_push($row['toegerekendeKosten'],"");
      array_push($row['resultaatBruto'],"");
  
      //array_push($row['resultaatVerslagperiode'],$this->formatGetal($resultaatVerslagperiode[$categorie],$this->bedragDecimalen));
      array_push($row['resultaatVerslagperiode'],$this->formatGetal($resultaatVerslagperiode[$categorie],$this->bedragDecimalen));
      
      array_push($row['resultaatVerslagperiode'],"");
      
   }
  return $row;
  }

  function getFondsKoers($fonds,$datum)
  {
    $db=new DB();
    $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
    $db->SQL($query);
    $koers=$db->lookupRecord();
    return $koers['Koers'];
  }
  
  function indexVergelijking()
  {
    $DB=new DB();
    

	  $perioden=array('begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
	  $query="SELECT
Indices.Beursindex,
Indices.specialeIndex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";
   	$DB->SQL($query);
		$DB->Query();
    $indices=array();
	  while($index = $DB->nextRecord())
      $indices[]=$index;

$query="SELECT Portefeuilles.specifiekeIndex as Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta 
FROM Portefeuilles 
Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds  
WHERE Portefeuilles.Portefeuille = '$this->portefeuille'";
   	$DB->SQL($query);
		$DB->Query();
	  while($index = $DB->nextRecord())
    {
      $indices[]=array();
      $indices[]=$index;
    }

	  foreach($indices as $index)
		{
		  if($index['specialeIndex']==1)
      {
   	    $specialeBenchmarks[]=$index['Beursindex'];
		   	$specialeIndexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
          $specialeIndexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
  	  	$specialeIndexData[$index['Beursindex']]['performance'] =     ($specialeIndexData[$index['Beursindex']]['fondsKoers_eind'] - $specialeIndexData[$index['Beursindex']]['fondsKoers_begin']) / ($specialeIndexData[$index['Beursindex']]['fondsKoers_begin']/100 );
      }
      else
      {  
		    $benchmarks[]=$index['Beursindex'];
		   	$indexData[$index['Beursindex']]=$index;
        foreach ($perioden as $periode=>$datum)
        {
          $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
          $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
        }
  	  	$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		}
      $indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}
  
    
		$this->pdf->SetY(120);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	$this->pdf->SetWidths(array(123,60,20,20,20,25));
  	$this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->Rect($this->pdf->marge+123,120,145,count($benchmarks)*4+4);
 	  $this->pdf->row(array("",vertaalTekst("Vergelijkingsmaatstaven",$this->pdf->rapport_taal),
                      "".date("d-m-Y",db2jul($perioden['begin'])),
                      "".date("d-m-Y",db2jul($perioden['eind'])),
                      vertaalTekst("Rendement",$this->pdf->rapport_taal),
                      vertaalTekst("Rendement EUR",$this->pdf->rapport_taal)));
  	unset($this->pdf->CellBorders);   
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 
  	foreach ($benchmarks as $fonds)
  	{  
        $fondsData=$indexData[$fonds];
        if($fondsData['Omschrijving']=='')
          $this->pdf->row(array(''));
        else
          $this->pdf->row(array('',$fondsData['Omschrijving'],
            $this->formatGetal($fondsData['fondsKoers_begin'],2),
            $this->formatGetal($fondsData['fondsKoers_eind'],2),
            $this->formatGetal($fondsData['performance'],2)."%",
            $this->formatGetal($fondsData['performanceEur'],2)."%"));
    }
    
    
    if(count($specialeBenchmarks) > 0)
    {
   	 	$this->pdf->SetY(150);
    	 $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	 	$this->pdf->SetWidths(array(150,60,20,20,20));
  	 	$this->pdf->SetAligns(array('L','L','R','R','R'));
     	$this->pdf->Rect($this->pdf->marge+150,150,120,count($specialeBenchmarks)*4+4);
 	   	$this->pdf->row(array("",vertaalTekst("Overige marktindices ter informatie",$this->pdf->rapport_taal),"".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),vertaalTekst("Rendement",$this->pdf->rapport_taal)));
  	 	unset($this->pdf->CellBorders);
  	  	 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

  	 	foreach ($specialeBenchmarks as $fonds)
  	 	{
        $fondsData=$specialeIndexData[$fonds];
        if($fondsData['Omschrijving']=='')
          $this->pdf->row(array(''));
        else
          $this->pdf->row(array('',$fondsData['Omschrijving'],
            $this->formatGetal($fondsData['fondsKoers_begin'],2),
            $this->formatGetal($fondsData['fondsKoers_eind'],2),
            $this->formatGetal($fondsData['performance'],2)."%"));
     	}
    }
    
  }

	function writeRapport()
	{
	  $this->tweedeStart();
	  $DB = new DB();
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$kopStyle = "u";

	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	  if($this->pdf->portefeuilledata['PerformanceBerekening'] == 2)
	    $periodeBlok = 'periode';
	  elseif($this->pdf->portefeuilledata['PerformanceBerekening'] == 6)
	    $periodeBlok = 'kwartaal';
	  else
	    $periodeBlok = 'maand';

	  $query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum,Portefeuilles.SoortOvereenkomst, ".
		" Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, ".
		" Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
		" Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
		" AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
		$DB->SQL($query);
		$pdata = $DB->lookupRecord();


$att=new ATTberekening_L70($this);
$hcatData=$att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,'attributie','ytd');
//echo "".$this->rapportageDatumVanaf.",".$this->rapportageDatum.",".$this->pdf->rapportageValuta.",'categorie' <br>\n";
$this->pdf->hcatData['rapportagePeriode']=$hcatData;
    foreach($hcatData as $categorie=>$waarden)
      if(!isset($this->categorien[$categorie]))
        $this->categorien[$categorie]=substr($categorie,0,10);
//listarray($hcatData);

//$hcatData=$att->bereken($this->tweedePerformanceStart,$this->rapportageDatum,$this->pdf->rapportageValuta,'categorie');
//$this->pdf->hcatData['lopendeJaar']=$hcatData;
//listarray($this->pdf->hcatData['rapportagePeriode']);
  		$waardenPerGrootboek = $this->waardenPerGrootboek();
	  	$this->waardenPerGrootboek = $waardenPerGrootboek;
 //   $attributieCategorieGrootboek['Opbrengst'] = $tmp['opbrengst'];
 //   $attributieCategorieGrootboek['Kosten'] = $tmp['kosten'];
 //   $this->attributieGrootboekPeriode = $attributieCategorieGrootboek;
    $waarde = $this->bepaalCategorieWaarden();


//listarray($this->pdf->hcatData);exit;
    $pW=5;
    $this->pdf->widthA = array(1,70,25,$pW,25,$pW,25,$pW,25,$pW,25,$pW,25,$pW,25,$pW,25,$pW,25,$pW,25,$pW);
		$this->pdf->alignA = array('L','L','R','C','R','C','R','C','R','C','R','C','R','C','R','C','R','C');
    $this->pdf->widthB = array(1,70,25,10,25,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R','R');
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
    $posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->AddPage();
    $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['PERFPaginas'] = $this->pdf->rapport_titel;

    //$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);

    $row = $this->createRows();
		$this->pdf->row($row['waardeVanaf']);
		$this->pdf->CellBorders = array('','','U','','U','','U','','U','','U','','U','','U','','U');
		$this->pdf->row($row['waardeTot']);
		$this->pdf->CellBorders = array();
		$this->pdf->ln();

		$this->pdf->row($row['mutatiewaarde']);
		$this->pdf->row($row['totaalStortingen']);
 		$this->pdf->row($row['totaalOnttrekkingen']);
    $this->pdf->row($row['totaalMutaties']);
    
 		$this->pdf->row($row['directeOpbrengsten']);
    $this->pdf->row($row['resultaatBruto']);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
   // listarray($this->pdf->widths);listarray($this->pdf->aligns);
//    for($i=0;$i<12;$i++)
 //     $row['rendementProcentBruto'][$i]='';
//    $row['rendementProcentBruto'][13]='%';
   // listarray($row['rendementProcentBruto']);
   // $row['rendementProcentBruto']=array('','test','100','%');
    $this->pdf->row($row['rendementProcentBruto']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','','U','','U','','U','','U','','U','','U','','U','','U');
 		$this->pdf->row($row['toegerekendeKosten']);
		$this->pdf->CellBorders = array();
		$this->pdf->ln();
    $this->pdf->CellBorders=array();
    
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','',$row['resultaatVerslagperiode'][2]));
    $row['resultaatVerslagperiode'][2]=' ';
    $this->pdf->ln(-1*$this->pdf->rowHeight);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->CellBorders = array('','','UU','','UU','','UU','','UU','','UU','','UU');
    $this->pdf->row($row['resultaatVerslagperiode']);
    
    
	  $this->pdf->CellBorders = array();
		$this->pdf->ln();

/*
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','',$row['rendementProcent'][2]));
    $row['rendementProcent'][2]=' ';
    $this->pdf->ln(-1*$this->pdf->rowHeight);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    foreach($row['rendementProcent'] as $index=>$waarde)
    {
      if($index < 2)
		    $this->pdf->CellBorders[]='';
      else
      {
        if($waarde <> '' && $waarde <> '%')
          $this->pdf->CellBorders[]='UU';
        else
          $this->pdf->CellBorders[]='';
      }
  	}
    


		$this->pdf->row($row['rendementProcent']);
*/
		//$this->pdf->CellBorders = array();
 $this->pdf->ln();

    $this->pdf->row($row['totaalNettoRentement']);

    
    
		//$this->pdf->row($row['rendementProcentJaar']);
		$this->pdf->CellBorders = array();
	  if($this->pdf->debug)
	  {
	    $this->pdf->row(array(''));
			$this->pdf->row($row['directeOpbrengsten']);
			$this->pdf->row($row['toegerekendeKosten']);
			$this->pdf->row($row['gerealiseerdKoersresultaat']);
			$this->pdf->row($row['ongerealiseerdeKoersResultaaten']);
			$this->pdf->row($row['opgelopenRentes']);
			$this->pdf->row($row['totaal']);
	  }
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$ypos = $this->pdf->GetY();
		$this->pdf->SetY($ypos);
//		$this->pdf->ln();
		$totaalOpbrengst += $this->waarde['opgelopenRentes']['Totaal'];
		$totaalOpbrengst += $this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'];
		$totaalOpbrengst += $this->waarde['gerealiseerdKoersresultaat']['Totaal'];
    
    foreach ($waardenPerGrootboek['opbrengst'] as $grootboek=>$grootboekWaarden)
		  $totaalOpbrengst += $grootboekWaarden['bedrag'];
	
//listarray($waardenPerGrootboek['opbrengst']);ob_flush();
    $koersResulaatValutas = $this->waarden['rapportagePeriode']['totaal']['resultaat'] - ($totaalOpbrengst  -  $waardenPerGrootboek['totaalKosten']);
	//	$koersResulaatValutas = 0;
		$totaalOpbrengst += $koersResulaatValutas;
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		//$this->pdf->ln();
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);

		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$y=$this->pdf->getY();
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

  	  if(round($this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'],2) != 0.00)
  	   	$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($this->waarde['ongerealiseerdeKoersResultaaten']['Totaal'],$this->bedragDecimalen),""));
  	 	if(round($this->waarde['gerealiseerdKoersresultaat']['Totaal'],2) != 0.00)
		    $this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($this->waarde['gerealiseerdKoersresultaat']['Totaal'],$this->bedragDecimalen),""));
		  if(round($koersResulaatValutas,2) != 0.00)
		    $this->pdf->row(array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,$this->bedragDecimalen),""));

  	if(round($this->waarde['opgelopenRentes']['Totaal'],2) != 0.00)
			 $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($this->waarde['opgelopenRentes']['Totaal'],$this->bedragDecimalen),""));

		foreach ($waardenPerGrootboek['opbrengst'] as $grootboek=>$grootboekWaarden)
		{
		  if(round($grootboekWaarden['bedrag'],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($grootboekWaarden['omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($grootboekWaarden['bedrag'],$this->bedragDecimalen),""));
		
		}
		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","",$this->formatGetal($totaalOpbrengst,$this->bedragDecimalen)));
		//$this->pdf->ln();
		//listarray($this->pdf->widthB);exit;
		//$this->pdf->SetWidths(array(129,80,30,10,30,100));
		//$this->pdf->SetAligns($this->pdf->alignB);

		//$this->pdf->setY($y);
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		//$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		foreach ($waardenPerGrootboek['kosten'] as $grootboek=>$grootboekWaarden)
		{
		  
		  if($grootboek=='BEH')
      {
        if($pdata['SoortOvereenkomst']=='Beheer')
          $grootboekWaarden['omschrijving'] = 'Beheervergoeding';
        else
          $grootboekWaarden['omschrijving'] = 'Adviesvergoeding';
      }
		  if(round($grootboekWaarden['bedrag'],2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($grootboekWaarden['omschrijving'],$this->pdf->rapport_taal),$this->formatGetal($grootboekWaarden['bedrag'],$this->bedragDecimalen),""));
		}

		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		//		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3]+80;
		//$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->row(array("","",$this->formatGetal($waardenPerGrootboek['totaalKosten'],$this->bedragDecimalen)));


		$this->pdf->ln();
		//$this->pdf->ln();
		$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($totaalOpbrengst - $waardenPerGrootboek['totaalKosten'],$this->bedragDecimalen)));
		//$posTotaal+=40;
		$this->pdf->Line($posSubtotaal,$this->pdf->GetY() ,$posSubtotaalEnd  ,$this->pdf->GetY());
		$this->pdf->Line($posSubtotaal,$this->pdf->GetY()+1 ,$posSubtotaalEnd  ,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$RapJaar = date("Y", db2jul($this->rapportageDatum));
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));

   //$this->toonZorgplicht();
   $this->indexVergelijking();
	 if($this->pdf->debug)
	 {
	   listarray($this->berekening->performance);flush();
	   exit;
   }
	}



  function bepaalCategorieWaarden()
  {
       $categorie = 'Totaal';
  	    $gerealiseerdKoersresultaat[$categorie] = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,true,$categorie);

	 		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'fondsen' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaal = $this->db->nextRecord();
        $ongerealiseerdeKoersResultaaten[$categorie] = ($totaal['totaalB'] - $totaal['totaalA']) ;

        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						     "FROM TijdelijkeRapportage WHERE ".
						     " rapportageDatum ='".$this->rapportageDatum."' AND ".
						     " portefeuille = '".$this->portefeuille."' AND ".$attributieQuery.
						     " type = 'rente' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaalA = $this->db->nextRecord();
    		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				    		 "FROM TijdelijkeRapportage WHERE ".
						     " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						     " portefeuille = '".$this->portefeuille."' AND ". $attributieQuery.
						     " type = 'rente' ". $this->__appvar['TijdelijkeRapportageMaakUniek'] ;
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaalB = $this->db->nextRecord();
    		$opgelopenRentes[$categorie] = ($totaalA['totaal'] - $totaalB['totaal']) / $this->pdf->ValutaKoersEind;


    $waarden=array('gerealiseerdKoersresultaat'=>$gerealiseerdKoersresultaat,
                   'ongerealiseerdeKoersResultaaten'=>$ongerealiseerdeKoersResultaaten,
                   'opgelopenRentes'=>$opgelopenRentes);
    $this->waarde = $waarden;
  return $waarden;
  }


  function waardenPerGrootboek()
  {

	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening,
		Grootboekrekeningen.Kosten ,Grootboekrekeningen.Opbrengst,".
		"SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) -  ".
		"SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery )AS waarde ".
		"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		"  (Grootboekrekeningen.Kosten = '1' || Grootboekrekeningen.Opbrengst ='1') ".
		"GROUP BY Rekeningmutaties.Grootboekrekening ".
		"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$waardenPerGrootboek = array();
		while($grootboek = $DB->nextRecord())
		{
		  if($grootboek['Opbrengst']=='1')
		  {
		    $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
		    $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['bedrag'] += $grootboek['waarde'];
		    $waardenPerGrootboek['totaalOpbrengst'] += $grootboek['waarde'];
		  }
		  else
		  {
		  	if($grootboek['Grootboekrekening'] == "KNBA")
		  	{
		  	  $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = "Bankkosten en provisie";
		  	  $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
			  }
			  else if($grootboek['Grootboekrekening'] == "KOBU")
		  	{
				  $waardenPerGrootboek['kosten']['KOST']['bedrag'] -= $grootboek['waarde'];

		  	}
		  	else
			  {
		  		$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
			  	$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
		  	}
        $waardenPerGrootboek['totaalKosten'] -= $grootboek['waarde'];
		  }
		}

		return $waardenPerGrootboek;
  }
  

}

?>