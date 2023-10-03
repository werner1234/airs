<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/09 15:46:58 $
File Versie					: $Revision: 1.1 $

$Log: RapportPORTAL.php,v $
Revision 1.1  2019/01/09 15:46:58  rvv
*** empty log message ***

Revision 1.4  2015/12/06 07:44:05  rvv
*** empty log message ***

Revision 1.2  2015/09/26 15:57:19  rvv
*** empty log message ***

Revision 1.1  2015/09/23 16:10:57  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportPORTAL
{

	function RapportPORTAL($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PORTAL";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
  	$this->pdf->rapport_titel = "";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

   
    if($rapportageDatumVanaf==$rapportageDatum && substr($rapportageDatumVanaf,5,5)=='01-01')
      $this->rapportageDatumVanaf=(substr($rapportageDatumVanaf,0,4)-1).'-12-31';
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
  
	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}
	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function writeRapport()
	{
		global $__appvar;
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$DB = new DB();
		$this->pdf->AddPage('P');
    
    $this->pdf->rapport_fontsizeBackup=$this->pdf->rapport_fontsize;
    $this->pdf->rapport_fontsize+=4;
    $this->pdf->rowHeightBackup=$this->pdf->rowHeight;
    $this->pdf->rowHeight+=2;

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor[r],$this->pdf->rapport_fontcolor[g],$this->pdf->rapport_fontcolor[b]);

		// ***************************** ophalen data voor afdruk ************************ //

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();

		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro / ".$this->pdf->ValutaKoersBegin." ) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();

		$waardeEind				= $totaalWaarde[totaal];
		$waardeBegin 			 	= $totaalWaardeVanaf[totaal];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
   
		$this->pdf->SetWidths(array(1,100,35,15));
		$this->pdf->SetAligns(array('L','L','R','L'));
	  $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln(2);



		$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf),$this->formatGetal($waardeBegin,2,true),""));
		$this->pdf->row(array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)),$this->formatGetal($waardeEind,2),""));
			// subtotaal
		$this->pdf->ln();
		$this->pdf->row(array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal),$this->formatGetal($waardeMutatie,2),""));
		$this->pdf->row(array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($stortingen,2),""));
		$this->pdf->row(array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($onttrekkingen,2),""));
		$this->pdf->ln();
		$this->pdf->row(array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($resultaatVerslagperiode,2),""));
		$this->pdf->ln();
    $this->pdf->row(array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal),$this->formatGetal($rendementProcent,2),"%"));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$ypos = $this->pdf->GetY();
    
    $index=new indexHerberekening();
    $indexData = $index->getWaarden($this->rapportageDatumVanaf,$this->rapportageDatum ,$this->portefeuille);//$this->pdf->PortefeuilleStartdatum
    if(count($indexData) > 0)
    {
      $this->pdf->widthA = array(1,35,35,35,35,35);
      $this->pdf->alignA = array('L','L','R','R','R','R');
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
      $n=1;
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->SetY(105);
      $this->pdf->Ln();

            $this->pdf->row(array('',vertaalTekst("Maand",$this->pdf->rapport_taal)."\n ",
                                      vertaalTekst("Begin-\nvermogen",$this->pdf->rapport_taal),
                                      vertaalTekst("Stortingen en \nonttrekkingen",$this->pdf->rapport_taal),
                                      vertaalTekst("Beleggings\nresultaat",$this->pdf->rapport_taal),
                                        vertaalTekst("Eind-\nvermogen",$this->pdf->rapport_taal)));
                                    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $sumWidth = array_sum($this->pdf->widthA);
          $this->pdf->Line($this->pdf->marge+$this->pdf->widthA[0],$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());

        $totaalRendament=100;
        $totaalRendamentIndex=100;
                    foreach ($indexData as $row)
                    {

          $datum = db2jul($row['datum']);
          $this->pdf->CellBorders = array();
          $this->pdf->row(array('',date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
                                           $this->formatGetal($row['waardeBegin'],2),
                                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
                                           $this->formatGetal($row['resultaatVerslagperiode'],2),
                                           $this->formatGetal($row['waardeHuidige'],2)));
                                           
                                           if(!isset($waardeBegin))
                                             $waardeBegin=$row['waardeBegin'];
                                           $totaalWaarde = $row['waardeHuidige'];
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



            $this->pdf->CellBorders = array('','','TS','TS','TS','TS');
            $this->pdf->row(array('','','','','',''));
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);

        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
        /*
                    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
                                           $this->formatGetal($waardeBegin,2),
                                           $this->formatGetal($totaalStortingenOntrekkingen,2),
                                           $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd,2),
                                           $this->formatGetal($totaalOpbrengsten,2),
                                           $this->formatGetal($totaalKosten,2),
                                           $this->formatGetal($totaalRente,2),
                                           $this->formatGetal($totaalResultaat,2),
                                           $this->formatGetal($totaalWaarde,2),
                                           '',
                                           $this->formatGetal($totaalRendament-100,2)
                                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
         */                                  
                    $this->pdf->row(array('',vertaalTekst('Totaal',$this->pdf->rapport_taal),
                                           $this->formatGetal($waardeBegin,2),
                                           $this->formatGetal($totaalStortingenOntrekkingen,2),
                                           $this->formatGetal($totaalResultaat,2),
                                           $this->formatGetal($totaalWaarde,2)
                                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
                                                                                
                    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

                  }

//////////


    
$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY WaardeEuro desc";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorieEind']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorieEind']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorieEind']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$waardeEind;
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$waardeEind*100;
	}

	$query="SELECT
if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',TijdelijkeRapportage.beleggingscategorie,'geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.type='rekening','Liquiditeiten', if(TijdelijkeRapportage.beleggingscategorie <> '',Beleggingscategorien.Omschrijving,'geen')) as categorieOmschrijving
FROM TijdelijkeRapportage LEFT JOIN Beleggingscategorien on TijdelijkeRapportage.beleggingscategorie = Beleggingscategorien.beleggingscategorie
	WHERE TijdelijkeRapportage.Portefeuille = '".$this->portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$this->rapportageDatumVanaf."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY WaardeEuro desc";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorieBegin']['data'][$cat['categorie']]['waardeEur']=$cat['WaardeEuro'];
	   $data['beleggingscategorieBegin']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorieBegin']['pieData'][$cat['categorieOmschrijving']]= $cat['WaardeEuro']/$waardeBegin;
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$waardeBegin*100;
	}


foreach ($data as $type=>$typeData)
{
  $n=0;
  foreach ($typeData['data'] as $categorie=>$gegevens)
  {
    if(!is_array($regelData[$n]))
      $regelData[$n]=array('','','','','','','','','','');
    if($type=='beleggingscategorieBegin')
      $offset=0;
    if($type=='beleggingscategorieEind')
    {
      $offset=3;
    }  
    if($type=='valutaVerdeling')
      $offset=8;

     $regelData[$n][0]='';
     if(($type=='beleggingscategorieBegin' || 'beleggingscategorieEind') && $regelData[$n][1] =='')
       $regelData[$n][1]=$gegevens['Omschrijving'];
     $regelData[$n][2+$offset]=$this->formatGetal($gegevens['waardeEur'],0);
     $regelData[$n][3+$offset]=$this->formatGetal($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2).'%';
     $regelData[$n][4+$offset]='';
     $n++;

     $regelTotaal[$type]['waardeEur']+=$gegevens['waardeEur'];
     $regelTotaal[$type]['percentage']+=round($data[$type]['kleurData'][$gegevens['Omschrijving']]['percentage'],2);
  }

}


foreach ($regelData as $regelNr=>$regel)
{
  ksort($regel);
  $regelData[$regelNr]=$regel;
}

$this->pdf->setXY($this->pdf->marge,200);
$this->pdf->SetWidths(array(1, 45,27,20, 15, 45,27,20,));
//$this->pdf->SetWidths(array(45, 40,20,15, 40, 40,20,15, 15));
$this->pdf->SetAligns(array('L', 'L','R','R',  'L',  'R','R','R'));


$this->pdf->underlinePercentage=0.8;
$this->pdf->CellBorders = array();
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','',date("d-m-Y",db2jul($this->rapportageDatumVanaf)), '','',
date("d-m-Y",db2jul($this->rapportageDatum)), '',''

));
//
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->CellBorders = array();
$this->pdf->ln(2);

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
foreach ($regelData as $regel)
{
  $this->pdf->row($regel);
}
$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Totaal ', $this->formatGetal($regelTotaal['beleggingscategorieBegin']['waardeEur']),
$this->formatGetal($regelTotaal['beleggingscategorieBegin']['percentage'],0).'%',
'Totaal ', $this->formatGetal($regelTotaal['beleggingscategorieEind']['waardeEur']),
$this->formatGetal($regelTotaal['beleggingscategorieEind']['percentage'],0).'%'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	

    $this->pdf->rapport_fontsize=$this->pdf->rapport_fontsizeBackup;
    $this->pdf->rowHeight=$this->pdf->rowHeightBackup;
    $this->pdf->AddPage('P');
    $this->pdf->rapport_fontsize+=4;
    $this->pdf->rowHeight+=2;
      
      $this->pdf->widthA = array(1,90,35,35,35);
      $this->pdf->alignA = array('L','L','R','R','R','R','R');
      $this->pdf->SetWidths($this->pdf->widthA);
      $this->pdf->SetAligns($this->pdf->alignA);
      $this->pdf->fillCell = array();
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->underlinePercentage=0.8;
      $this->pdf->SetY(30);
      $this->pdf->Ln();
      $this->pdf->row(array('',vertaalTekst("Fonds",$this->pdf->rapport_taal)."",
                                      vertaalTekst("Aantal",$this->pdf->rapport_taal),
                                      vertaalTekst("Koers",$this->pdf->rapport_taal),
                                      vertaalTekst("Waarde EUR",$this->pdf->rapport_taal)));
                                    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $sumWidth = array_sum($this->pdf->widthA);
      $this->pdf->Line($this->pdf->marge+$this->pdf->widthA[0],$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());


			$query = "SELECT
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
TijdelijkeRapportage.beleggingscategorie,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.type
FROM TijdelijkeRapportage
WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' And
 TijdelijkeRapportage.rapportageDatum =  '".$this->rapportageDatum."'  
".$__appvar['TijdelijkeRapportageMaakUniek']."
ORDER BY  TijdelijkeRapportage.type, TijdelijkeRapportage.beleggingscategorieVolgorde, 
TijdelijkeRapportage.beleggingscategorie,  TijdelijkeRapportage.fondsOmschrijving";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $lastCat='';
    while($data=$DB->nextRecord())
    {
      if($lastCat!=$data['beleggingscategorie'])
      {
        if($lastCat<>'')
          $this->pdf->ln();
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->row(array('',$data['beleggingscategorieOmschrijving']));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      }
      $this->pdf->row(array('',$data['fondsOmschrijving'], 
                               $this->formatAantal($data['totaalAantal'],0,true), 
                               $this->formatGetal($data['actueleFonds'],2),
                               $this->formatGetal($data['actuelePortefeuilleWaardeEuro'],2)));
      $lastCat=$data['beleggingscategorie'];
    }      
   $this->pdf->ln();
			   // in PDFRapport.php
			   $this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
    $this->pdf->rapport_fontsize=$this->pdf->rapport_fontsizeBackup;
      $this->pdf->rowHeight=$this->pdf->rowHeightBackup;

	}
  
}
?>