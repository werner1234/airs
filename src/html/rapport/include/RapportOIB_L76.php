<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.4 $

$Log: RapportOIB_L76.php,v $
Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2018/04/28 18:36:15  rvv
*** empty log message ***

Revision 1.2  2018/04/22 09:30:29  rvv
*** empty log message ***

Revision 1.1  2018/04/18 16:18:39  rvv
*** empty log message ***

Revision 1.7  2013/05/01 18:03:38  rvv
*** empty log message ***

Revision 1.6  2013/05/01 15:53:08  rvv
*** empty log message ***

Revision 1.5  2013/04/27 16:29:28  rvv
*** empty log message ***

Revision 1.4  2013/04/20 16:34:57  rvv
*** empty log message ***

Revision 1.3  2013/04/17 15:59:22  rvv
*** empty log message ***

Revision 1.2  2013/04/10 15:58:01  rvv
*** empty log message ***

Revision 1.1  2013/04/07 16:06:51  rvv
*** empty log message ***

Revision 1.5  2011/02/10 19:56:35  rvv
*** empty log message ***

Revision 1.4  2011/01/08 14:27:56  rvv
*** empty log message ***

Revision 1.3  2010/12/22 18:45:30  rvv
*** empty log message ***

Revision 1.2  2010/12/19 13:05:15  rvv
*** empty log message ***

Revision 1.1  2010/07/04 15:24:39  rvv
*** empty log message ***


*/
global $__appvar;

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/RapportATT_L76.php");


class RapportOIB_L76
{
	function RapportOIB_L76($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    

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
  
  function barChart()
  {
    $this->att=new RapportATT_L76($this->pdf, $this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
    
   
  //  if(substr($this->att->rapportageDatumVanaf,5,5)=='01-01')
  //    $start=date("Y-m-d",db2jul($this->att->rapportageDatumVanaf)-(86400*60));
  //  else
     $start=$this->att->rapportageDatumVanaf; 
      
    $this->indexData = $this->att->getWaarden($start ,$this->att->rapportageDatum ,$this->att->portefeuille);
    foreach ($this->indexData as $index=>$data)
    {
      if($data['datum'] != '0000-00-00')
      {
        $rendamentWaarden[] = $data;
        $grafiekData['Datum'][] = $data['datum'];
        $grafiekData['Index'][] = $data['index']-100;
        $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
    //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
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
     
     $DB=new DB();
$q="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Vermogensbeheerder
FROM
Beleggingscategorien 
INNER JOIN CategorienPerHoofdcategorie ON  Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'

 AND CategorienPerHoofdcategorie.Hoofdcategorie IN('".implode("','",$categorien)."')
GROUP BY CategorienPerHoofdcategorie.Hoofdcategorie
ORDER BY Beleggingscategorien.Afdrukvolgorde asc";
		$DB->SQL($q); 
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  $this->att->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->att->categorieOmschrijving[$data['Beleggingscategorie']]=$data['Omschrijving'];
		}
    
    $q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->att->categorieKleuren=$allekleuren['OIB'];
     
   
		 if (count($barGraph) > 0)
		 {
		    $this->pdf->SetXY(15,180)		;//112
		    $this->att->VBarDiagram(267, 70, $barGraph['Index'],'Vermogensverdeling');
		 }
}




	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas'] = $this->pdf->customPageNo;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas'] = $this->pdf->rapport_titel;
    $this->pdf->Ln(7);

		$rapportageDatum = $this->rapportageDatum;
		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	$portefeuille = $this->portefeuille;

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

  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatumVanaf."' AND ".
					 " portefeuille = '".$portefeuille."' "
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaardeBegin = $DB->nextRecord();
	$totaalWaardeBegin = $totaalWaardeBegin['totaal'];

	$query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM
			TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND
			TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND
 			TijdelijkeRapportage.Type = 'rekening'
			" .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalLiquiditeiten = $DB->nextRecord();
	$totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];




	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    
    
    $query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie AS `CategorienPerHoofdcategorie.Hoofdcategorie`,
CategorienPerHoofdcategorie.Beleggingscategorie AS Beleggingscategorie,
Beleggingscategorien.Omschrijving AS BeleggingscategorieOmschrijving,
Hcat.Omschrijving AS HcatOmschrijving,
Beleggingscategorien.Afdrukvolgorde
FROM
(CategorienPerHoofdcategorie)
INNER JOIN Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
INNER JOIN Beleggingscategorien as Hcat ON CategorienPerHoofdcategorie.Hoofdcategorie = Hcat.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY 
Beleggingscategorien.Afdrukvolgorde";
 		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
      $categorieVolgorde[$data['HcatOmschrijving']][$data['Beleggingscategorie']]=$data['BeleggingscategorieOmschrijving'];
		}
    


$query="SELECT TijdelijkeRapportage.Valuta,
TijdelijkeRapportage.Valuta as ValutaOmschrijving, 
TijdelijkeRapportage.beleggingscategorie as categorie, 
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving, 
TijdelijkeRapportage.hoofdcategorie as hoofdcategorie, 
TijdelijkeRapportage.hoofdcategorieOmschrijving as hoofdcategorieOmschrijving, 
TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro AS WaardeEuro
FROM TijdelijkeRapportage 
WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
.$__appvar['TijdelijkeRapportageMaakUniek'].
" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query); 
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	 	 $data['beleggingscategorieEind']['data']['H'][$cat['hoofdcategorieOmschrijving']]['waardeEur']+=$cat['WaardeEuro'];
	   $data['beleggingscategorieEind']['data']['H'][$cat['hoofdcategorieOmschrijving']]['Omschrijving']=$cat['hoofdcategorieOmschrijving'];  
     $data['beleggingscategorieEind']['data']['H'][$cat['hoofdcategorieOmschrijving']]['hoofdcategorie']=1;   
     $data['beleggingscategorieEind']['data']['H'][$cat['hoofdcategorieOmschrijving']]['percentage']+=$cat['WaardeEuro']/$totaalWaarde*100;
     
	   $data['beleggingscategorieEind']['data']['C'][$cat['categorie']]['waardeEur']+=$cat['WaardeEuro'];
	   $data['beleggingscategorieEind']['data']['C'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
     $data['beleggingscategorieEind']['data']['C'][$cat['categorie']]['percentage']+=($cat['WaardeEuro']/$totaalWaarde*100);
          
	   $data['beleggingscategorieEind']['pieData'][$cat['categorieOmschrijving']]+= $cat['WaardeEuro']/$totaalWaarde;
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieEind']['kleurData'][$cat['categorieOmschrijving']]['percentage']+=$cat['WaardeEuro']/$totaalWaarde*100;
  }

  
  $query="SELECT TijdelijkeRapportage.Valuta, Valutas.Omschrijving as ValutaOmschrijving,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
FROM TijdelijkeRapportage 
LEFT JOIN Valutas on TijdelijkeRapportage.Valuta = Valutas.Valuta
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."'"
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY TijdelijkeRapportage.Valuta
	ORDER BY WaardeEuro desc";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
  while($cat = $DB->nextRecord())
  {  
     $data['valutaEind']['data'][$cat['Valuta']]['waardeEur']=$cat['WaardeEuro'];
     $data['valutaEind']['data'][$cat['Valuta']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;;
	   $data['valutaEind']['data'][$cat['Valuta']]['Omschrijving']=$cat['ValutaOmschrijving'];
	   $data['valutaEind']['pieData'][$cat['ValutaOmschrijving']]= $cat['WaardeEuro']/$totaalWaarde;
	   $data['valutaEind']['kleurData'][$cat['ValutaOmschrijving']]=$allekleuren['OIV'][$cat['Valuta']];
	   $data['valutaEind']['kleurData'][$cat['ValutaOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaarde*100;
	}

	$query="SELECT TijdelijkeRapportage.Valuta,
TijdelijkeRapportage.Valuta as ValutaOmschrijving, 
TijdelijkeRapportage.beleggingscategorie as categorie, 
TijdelijkeRapportage.beleggingscategorieOmschrijving as categorieOmschrijving, 
TijdelijkeRapportage.hoofdcategorie as hoofdcategorie, 
TijdelijkeRapportage.hoofdcategorieOmschrijving as hoofdcategorieOmschrijving, 
TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro AS WaardeEuro
FROM TijdelijkeRapportage 
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatumVanaf."'"
	.$__appvar['TijdelijkeRapportageMaakUniek']." 
	ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	while($cat = $DB->nextRecord())
	{
	   $data['beleggingscategorieBegin']['data']['H'][$cat['hoofdcategorieOmschrijving']]['waardeEur']+=$cat['WaardeEuro'];
	   $data['beleggingscategorieBegin']['data']['H'][$cat['hoofdcategorieOmschrijving']]['Omschrijving']=$cat['hoofdcategorieOmschrijving']; 
     $data['beleggingscategorieBegin']['data']['H'][$cat['hoofdcategorieOmschrijving']]['percentage']+=$cat['WaardeEuro']/$totaalWaardeBegin*100;
     $data['beleggingscategorieBegin']['data']['H'][$cat['hoofdcategorieOmschrijving']]['hoofdcategorie']=1; 

	   $data['beleggingscategorieBegin']['data']['C'][$cat['categorie']]['waardeEur']+=$cat['WaardeEuro'];
	   $data['beleggingscategorieBegin']['data']['C'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
     $data['beleggingscategorieBegin']['data']['C'][$cat['categorie']]['percentage']+=$cat['WaardeEuro']/$totaalWaardeBegin*100;
 
	   $data['beleggingscategorieBegin']['pieData'][$cat['categorieOmschrijving']]+= $cat['WaardeEuro']/$totaalWaardeBegin;
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]=$allekleuren['OIB'][$cat['categorie']];
	   $data['beleggingscategorieBegin']['kleurData'][$cat['categorieOmschrijving']]['percentage']=$cat['WaardeEuro']/$totaalWaardeBegin*100;
	}


 $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
 $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);

$afm['beleggingscategorieBegin']=AFMstd($portefeuille,$rapportageDatumVanaf,false);
$afm['beleggingscategorieEind']=AFMstd($portefeuille,$rapportageDatum,false);


$keys=array();
foreach($afm['beleggingscategorieBegin']['debug']['verdeling'] as $key=>$adata)
  $keys[]=$key;
foreach($afm['beleggingscategorieEind']['debug']['verdeling'] as $key=>$adata)
  $keys[]=$key;
  
$n=0;
$deling=1;
foreach ($data['valutaEind']['data'] as $valuta=>$gegevens)
{
  $regelData[$n]=array('','','','','','','','','','','','');
     $offset=8;
     if(isset($gegevens['waardeEur'])) 
       $waardeEur=$this->formatGetal($gegevens['waardeEur'],0);
     else
       $waardeEur='';  
     if(isset($gegevens['percentage'])) 
       $percentage=$this->formatGetal($gegevens['percentage'],1).'%';
     else
       $percentage=''; 
       
     $regelData[$n][0]='';
     $regelData[$n][1+$offset]=$gegevens['Omschrijving'];
     $regelData[$n][2+$offset]=$waardeEur;
     $regelData[$n][3+$offset]=$percentage;
     $regelData[$n][4+$offset]='';
     $n++;

     $regelTotaal['valutaEind']['waardeEur']+=$gegevens['waardeEur']/$deling;
     $regelTotaal['valutaEind']['percentage']+=round($gegevens['percentage']/$deling,2);
}
$deling=2;
foreach($data['beleggingscategorieBegin']['data'] as $catType=>$catdata)
{
  foreach($catdata as $categorie=>$gegevens)
  {
    $regelTotaal['beleggingscategorieBegin']['waardeEur']+=$gegevens['waardeEur']/$deling;
    $regelTotaal['beleggingscategorieBegin']['percentage']+=round($gegevens['percentage']/$deling,2);
  }
}
foreach($data['beleggingscategorieEind']['data'] as $catType=>$catdata)
{
  foreach($catdata as $categorie=>$gegevens)
  {
    $regelTotaal['beleggingscategorieEind']['waardeEur']+=$gegevens['waardeEur']/$deling;
    $regelTotaal['beleggingscategorieEind']['percentage']+=round($gegevens['percentage']/$deling,2);
  }
}

foreach ($regelData as $regelNr=>$regel)
{
  ksort($regel);
  $regelData[$regelNr]=$regel;
}


//297
//echo 297-array_sum(array(0, 55,20,15, 5, 55,20,15, 5, 55,20,15))-2*$this->pdf->marge;exit;

$this->pdf->SetWidths(array(0, 56,20,15, 5, 55,20,15, 5, 55,20,15));
//$this->pdf->SetWidths(array(45, 40,20,15, 40, 40,20,15, 15));
$this->pdf->SetAligns(array('L', 'L','R','R',  'L',  'L','R','R',  'L',  'L','R','R'));


$this->pdf->underlinePercentage=0.8;

$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);
for($i=0;$i<count($this->pdf->widths);$i++)
  $this->pdf->fillCell[] = 1;
//$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Totaal '.date("d-m-Y",db2jul($rapportageDatumVanaf))."\n ", 
$this->formatGetal($regelTotaal['beleggingscategorieBegin']['waardeEur'])."\n ",
$this->formatGetal($regelTotaal['beleggingscategorieBegin']['percentage'],1)."%\n "," \n ",
'Totaal '.date("d-m-Y",db2jul($rapportageDatum))."\n ", 
$this->formatGetal($regelTotaal['beleggingscategorieEind']['waardeEur'])."\n ",
$this->formatGetal($regelTotaal['beleggingscategorieEind']['percentage'],1)."%\n "
,"\n ",'Totaal '.date("d-m-Y",db2jul($rapportageDatum))."\n ", 
$this->formatGetal($regelTotaal['valutaEind']['waardeEur'])."\n ",
$this->formatGetal($regelTotaal['valutaEind']['percentage'],1)."%\n "
,"\n "));
unset($this->pdf->fillCell);
$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);

$this->pdf->CellBorders = array();
$this->pdf->ln(2);

$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$ypage=$this->pdf->GetY();
foreach ($regelData as $regel)
{
  $this->pdf->row($regel);
}
$this->pdf->SetY($ypage);

foreach($categorieVolgorde as $hcat=>$categorien)
{
  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array('',$hcat, 
$this->formatGetal($data['beleggingscategorieBegin']['data']['H'][$hcat]['waardeEur'])."",
$this->formatGetal($data['beleggingscategorieBegin']['data']['H'][$hcat]['percentage'],1)."%","",
$hcat, 
$this->formatGetal($data['beleggingscategorieEind']['data']['H'][$hcat]['waardeEur']),
$this->formatGetal($data['beleggingscategorieEind']['data']['H'][$hcat]['percentage'],1)."%"));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  foreach($categorien as $categorie=>$omschrijving)
  {
    $this->pdf->row(array('',$omschrijving, 
$this->formatGetal($data['beleggingscategorieBegin']['data']['C'][$categorie]['waardeEur'])."",
$this->formatGetal($data['beleggingscategorieBegin']['data']['C'][$categorie]['percentage'],1)."%","",
$omschrijving, 
$this->formatGetal($data['beleggingscategorieEind']['data']['C'][$categorie]['waardeEur']),
$this->formatGetal($data['beleggingscategorieEind']['data']['C'][$categorie]['percentage'],1)."%"));
  }
}

$this->pdf->ln();


//$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
$this->pdf->row(array('','Standaarddeviatie','',$this->formatGetal($afm['beleggingscategorieBegin']['std'],1),'',
'Standaarddeviatie','',$this->formatGetal($afm['beleggingscategorieEind']['std'],1)
));

$this->barChart();


$this->pdf->CellBorders = array();
/*
$this->pdf->ln(2);
$regelData=array();
foreach ($afmData as $regelNr=>$regel)
{
  ksort($regel);
  $regelData[$regelNr]=$regel;
}
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
foreach ($regelData as $regel)
{
  $this->pdf->row($regel);
}
*/


}




	function printPie($pieData,$kleurdata,$title='',$width=75,$height=75)
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

		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
		while (list($key, $value) = each($pieData))
			if ($value < 0)
				$pieData[$key] = -1 * $value;

			//$this->pdf->SetXY(210, $this->pdf->headerStart);
			$y = $this->pdf->getY();
			$this->pdf->SetFont($this->pdf->rapport_font,'b',10);
			$this->pdf->setXY($startX,$y-4);
      $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
      $this->pdf->Cell(50,4,vertaalTekst($title, $this->pdf->rapport_taal),0,0,"C");
      $this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
  	$this->pdf->setXY($startX,$y);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

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
          $factor=0;

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

      $x1 = $XPage ;
      $x2 = $x1 + $hLegend + $margin;
      $y1 = $YDiag + ($radius) + $margin;

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