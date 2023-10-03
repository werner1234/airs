<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/20 16:19:15 $
 		File Versie					: $Revision: 1.26 $

 		$Log: RapportPERFG_L35.php,v $
 		Revision 1.26  2019/11/20 16:19:15  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2019/11/17 17:58:42  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2019/11/17 09:40:14  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2019/11/16 17:12:28  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2018/01/13 19:10:29  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2017/03/29 16:23:27  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2016/02/13 14:02:39  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2013/10/26 15:42:47  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2013/10/19 15:57:40  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2013/07/28 09:59:15  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2013/07/20 16:26:07  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2013/07/17 15:53:14  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2013/04/03 14:58:34  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2013/02/10 10:06:07  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2013/02/06 19:06:11  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/12/08 14:48:08  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2012/12/05 16:45:29  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2012/05/02 15:53:13  rvv
 		*** empty log message ***

 		Revision 1.7  2012/04/14 16:51:17  rvv
 		*** empty log message ***

 		Revision 1.6  2012/03/25 13:27:46  rvv
 		*** empty log message ***

 		Revision 1.5  2012/03/21 19:08:58  rvv
 		*** empty log message ***

 		Revision 1.4  2012/03/18 16:08:24  rvv
 		*** empty log message ***

 		Revision 1.3  2012/03/14 17:30:11  rvv
 		*** empty log message ***

 		Revision 1.2  2012/03/04 11:39:58  rvv
 		*** empty log message ***

 		Revision 1.1  2012/02/26 15:17:43  rvv
 		*** empty log message ***

 		Revision 1.7  2012/02/22 19:22:07  rvv
 		*** empty log message ***

 		Revision 1.6  2011/06/29 10:33:16  rvv
 		*** empty log message ***

 		Revision 1.5  2011/02/26 16:02:23  rvv
 		*** empty log message ***

 		Revision 1.4  2011/01/12 16:17:13  rvv
 		*** empty log message ***

*/

include_once('../indexBerekening.php');
include_once("rapport/include/layout_111/ATTberekening_L111.php");


class RapportPERFD_L111
{

  function RapportPERFD_L111($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		if($this->pdf->rapport_PERFGRAFIEK_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_PERFG_titel;
		else
			$this->pdf->rapport_titel = "Rendement op het belegd vermogen versus gewogen benchmark";


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}


  function writeRapport()
	{

		$query = "SELECT Portefeuilles.Startdatum,Portefeuilles.startdatumMeerjarenrendement, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();
    $this->pdf->AddPage();
    $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0),'dash' => 0));
    $startJul=db2jul($this->portefeuilledata['startdatumMeerjarenrendement']);
/*
    $DB = new DB();
    $query = "SELECT id, MONTH(Datum) as month, YEAR(Datum) as year FROM HistorischePortefeuilleIndex WHERE Portefeuille = '".$this->portefeuille."' AND Categorie = 'Totaal' ORDER BY Datum ASC LIMIT 1 ";
    $DB->SQL($query);
    $DB->Query();
    //$datum = $DB->nextRecord();

if($datum['id'] > 0)
{
  if($startJul>0)
    $start=$this->portefeuilledata['startdatumMeerjarenrendement'];
  else
  {
    if($datum['month'] <10)
      $datum['month'] = "0".$datum['month'];
    $start = $datum['year'].'-'.$datum['month'].'-01';
  }
}
else
{
*/
  if($startJul>0)
    $start=$this->portefeuilledata['startdatumMeerjarenrendement'];
  else
    $start=substr($this->portefeuilledata['Startdatum'],0,10);
//}
//else
//  $start = $this->rapportageDatumVanaf;




$eind = $this->rapportageDatum;
$datumStart = db2jul($start);
$datumStop  = db2jul($eind);




//$index = new indexHerberekening();
//$index->geenCacheGebruik=true;
//$indexWaarden = $index->getWaarden($start,$eind,$this->portefeuille,$this->portefeuilledata['SpecifiekeIndex'],'periode');

$att=new ATTberekening_L111($this);
$this->meerjarenStart=$start;

//echo "Norm ".$this->rapportageDatumVanaf.",$eind,".$this->pdf->rapportageValuta.",hoofdcategorie <br>\n";
$hcatData=$att->bereken($this->rapportageDatumVanaf,$eind,'Hoofdcategorie');

unset($hcatData['H-Liq']);
$this->firstHeader = true;
$this->toonData($hcatData);

if(1)
{
  //echo "Historisch $start,$eind".$this->pdf->rapportageValuta.",hoofdcategorie <br>\n";
  $this->pdf->rapport_titel = "Historisch rendement op het belegd vermogen versus gewogen benchmark";
  $this->pdf->AddPage();
  $hcatDataJaren=$att->bereken($start,$eind,'Hoofdcategorie');
  //listarray($hcatDataJaren);
  unset($hcatDataJaren['H-Liq']);
  $hcatDataJarenShort=$this->maandenNaarJaren($hcatDataJaren);
  $this->toonData($hcatDataJaren,'jaar',$hcatDataJarenShort);
}

}

function maandenNaarJaren($maandDataIn)
{
//listarray($maandData);
  $tmp=array();
  $somVelden=array('stort','stortEnOnttrekking','storting','onttrekking','kosten','opbrengst','kostenNietGekoppeld','resultaat','ongerealiseerd','gerealiseerd','resultaatBruto');
  $stapelItems=array('indexPerf','indexBijdrageWaarde','overPerf','relContrib','procent','procentBruto');
  $gemiddeldeVelden=array('gemWaarde');
  $laatsteJaar=0;
  $laasteJulDatum=0;
  $lastJaar='';

  foreach($maandDataIn as $categorie=>$maandData)
  {
    foreach($maandData['perfWaarden'] as $maand=>$totaalData)
    {
      $julDatum=db2jul($maand);
      $jaar=date("Y",$julDatum);

      if($jaar<>$laatsteJaar)
      {
        $laatsteDag[$laatsteJaar]=date("-m-d",$laasteJulDatum);
      }

      $laasteJulDatum=$julDatum;
      $laatsteJaar=$jaar;
    }
    $laatsteDag[$laatsteJaar]=date("-m-d",$laasteJulDatum);

    $aantalWaarden=0;
    $begonnen=false;
    $startDatum='';
    foreach($maandData['perfWaarden'] as $maand=>$totaalData)
    {
      $julDatum=db2jul($maand);
      $jaar=date("Y",$julDatum);
      //$dateEnd='-12-31';
      $dateEnd=$laatsteDag[$jaar];
      if($jaar <> '')
      {

        if($jaar <> $lastJaar)
        {
          $lastJaar='';
          $startDatum='';
         // $begonnen=false;
          if($lastJaar <> '')
          {
            foreach ($gemiddeldeVelden as $item)
              $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]=$tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] /($aantalWaarden+1);
          }
          $aantalWaarden=0;

        }

        if($totaalData['beginwaarde']==0 && $totaalData['eindwaarde']==0 && $totaalData['stortEnOnttrekking']==0 && $totaalData['resultaat']==0 && $begonnen==false)
        {
//nog niet begonnen
        //  listarray($totaalData);
        }
        else
        {
          if($begonnen==false || ($begonnen==true && $jaar <> $lastJaar) )
            $startDatum=substr($totaalData['periode'],0,10);
          $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['startDatum']=$startDatum;
          $begonnen=true;

        }

        if($begonnen==false)
        {
          continue;
        }

        if(!isset($tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['beginwaarde']))
        {
          $tmp[$categorie]['perfWaarden'][$jaar . $dateEnd]['beginwaarde'] = $totaalData['beginwaarde'];
          if($tmp[$categorie]['perfWaarden'][$jaar . $dateEnd]['beginwaarde']=='')
            $tmp[$categorie]['perfWaarden'][$jaar . $dateEnd]['beginwaarde']=0;
        }
        $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['eindwaarde']=$totaalData['eindwaarde'];
        $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['index']=$totaalData['index'];

        foreach($somVelden as $veld)
          $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$veld]+=$totaalData[$veld];

        foreach ($stapelItems as $item)
        {
          $tmp[$categorie]['perfWaarden'][$jaar . $dateEnd][$item] = (($tmp[$categorie]['perfWaarden'][$jaar . $dateEnd][$item] + 1) * ($totaalData[$item] + 1)) - 1;
        }
        foreach ($gemiddeldeVelden as $item)
          $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] += $totaalData[$item];

        $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd]['indexBruto']=$totaalData['indexBruto'];


         $lastJaar=$jaar;
         $aantalWaarden++;
      }
    }
    foreach ($gemiddeldeVelden as $item)
      $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] =$tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]/($aantalWaarden+1);
    //foreach ($stapelItems as $item)
   //   $tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item] =$tmp[$categorie]['perfWaarden'][$jaar.$dateEnd][$item]-1;
  }

  //listarray($tmp);
  return $tmp;
}

function toonData($perfdata,$periode='maand',$hcatDataJarenShort=array())
{

  $DB = new DB();
  $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie, IndexPerBeleggingscategorie.Fonds, Fondsen.Omschrijving
FROM IndexPerBeleggingscategorie
JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds=Fondsen.Fonds
WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      AND (IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
$DB->SQL($query);
$DB->Query();
  $fondsOmschrijvingen=array();
while($index=$DB->nextRecord())
{
  $indexLookup[$index['Beleggingscategorie']] = $index['Fonds'];
  $fondsOmschrijvingen[$index['Fonds']]=$index['Omschrijving'];
}
$indexLookup['totaal']=$this->portefeuilledata['SpecifiekeIndex'];


$query="SELECT
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Beleggingscategorie
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Hoofdcategorie = Beleggingscategorien.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
GROUP BY Beleggingscategorien.Beleggingscategorie";
    $DB->SQL($query);
    $DB->Query();
  $hCategorieOmschrijvingen=array();


while($cat=$DB->nextRecord())
  $hCategorieOmschrijvingen[$cat['Beleggingscategorie']]=$cat['Omschrijving'];

  $datumStop  = db2jul($this->rapportageDatum);
  $maandPeriode=0;
  if($periode=='maand')
    $maandPeriode=mktime(0,0,0,0,0,date("Y",$datumStop));//-1

  $this->pdf->CellBorders=array();
  $this->pdf->setY(45);
  //$this->pdf->ln();
  //$this->pdf->CellBorders = array(array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'),array('U','T','L','R'));
  $listWidthsFirst=array(14,26,25,25,16,26,22,22,22,26,22,22);
  $listWidths=array(14,26,25,25,26,25,15,15,15,15);

  $this->pdf->CellBorders = array();
  $YendIndex = $this->pdf->GetY();
  //$categorien=array('ZAK','VAR','totaal');
  $categorien=array_keys($perfdata);
  //listarray($categorien);
  //listarray($perfdata['H-Aand']['perfWaarden']);
  $hcatWaarden=array();

  foreach ($categorien as $cat)
  {
    $begonnen=false;
    $perfIndexCum=1;
    foreach ($perfdata[$cat]['perfWaarden'] as $datum=>$data)
    {
      $juldate=db2jul($datum);
      if($juldate > $maandPeriode)
      { //echo date("Y-m-d",mktime(0,0,0,1,1,substr($datum,0,4)))." $datum <br>\n";

       // $perfIndex=$this->fondsPerf($indexLookup[$cat],date("Y-m-d",mktime(0,0,0,substr($datum,5,2),0,substr($datum,0,4))),$datum);
        if($data['beginwaarde']==0 && $data['eindwaarde']==0 && $data['stortEnOnttrekking']==0 && $begonnen==false)
        {
          $perfIndex=0;
          $data['specifiekeIndex']=0;
        }
        else
        {
          $begonnen = true;
         // $hcatWaarden[$cat]['perfWaarden'][$datum]['beginDatum']=date("Y-m-d",mktime(0,0,0,substr($datum,5,2),0,substr($datum,0,4)));
        }

        if($begonnen==false)
          continue;

       //  $perfIndexCum+=$perfIndex;// ($perfIndexCum  * (1+$perfIndex)) ;
        $perfIndex=$data['indexPerf'];
        $perfIndexCum= ($perfIndexCum  * (1+$perfIndex)) ;
        $data['specifiekeIndex']=($perfIndexCum-1)*100;

      //  listarray($data);
         if($this->pdf->debug==true)
         {
           echo "<b> cumulatief ".round($perfIndexCum*100,4)."</b><br>\n";
         }
         //$data['specifiekeIndex']=$perfIndexCum*100;//($perfIndexCum-1)*100;
         $hcatWaarden['periode'][$cat]['portefeuille'][]=$data['indexBruto']-100;
         if($indexLookup[$cat]<> '')
         {
           $hcatWaarden['periode'][$cat]['specifiekeIndex'][] = $data['specifiekeIndex'];
           $hcatWaarden['periode'][$cat]['specifiekeIndexFonds'] = $fondsOmschrijvingen[$indexLookup[$cat]];
         }
         if($periode=='jaar')
           $hcatWaarden['periode'][$cat]['datum'][]= date("M y",$juldate);
         else
           $hcatWaarden['periode'][$cat]['datum'][]= date("M",$juldate);
         $hcatWaarden['periode'][$cat]['waarde'][]=$data;
      }
    }
  }
  $chartData=$hcatWaarden;
//listarray($chartData['periode']);
  if(count($hcatDataJarenShort) > 0)
     $hcatWaarden=$hcatDataJarenShort;

  foreach ($categorien as $cat)
  {
    $perfIndexCum=1;
    $begonnen=false;
    foreach ($hcatWaarden[$cat]['perfWaarden'] as $datum=>$data)
    {
     // echo $datum."<br>\n";listarray($data);
      $juldate=db2jul($datum);
      if($juldate > $maandPeriode)
      {
    //    listarray($data);
        /*
         if($periode=='jaar')
         {
           if($data['startDatum']<>'')
             $start=$data['startDatum'];
           else
             $start=date("Y-m-d",mktime(0,0,0,0,0,substr($datum,0,4)));
       //   $perfIndex=$this->fondsPerf($indexLookup[$cat],$start,$datum);
           //echo $data['startDatum']."| $begonnen | $perfIndex |     ".$indexLookup[$cat]." | $start | $datum <br>\n";
         }
         else
         {
        //   $perfIndex = $this->fondsPerf($indexLookup[$cat], date("Y-m-d", mktime(0, 0, 0, substr($datum, 5, 2), 0, substr($datum, 0, 4))), $datum);
           //echo "$perfIndex |     ".$indexLookup[$cat]." | ".date("Y-m-d", mktime(0,0,0,     substr($datum,5,2),0,    substr($datum,0,4)))."|".$datum.");<br>\n";
         }
  */
        $perfIndex=$data['indexPerf'];



      //  echo $indexLookup[$cat]." | $datum | ". $perfIndex."<br>\n";
        if($data['beginwaarde']==0 && $data['eindwaarde']==0 && $data['stortEnOnttrekking']==0 && $begonnen==false)
        {
          $perfIndex=0;
          $data['specifiekeIndex']=0;
        }
        else
        {
          $begonnen=true;
        }
         $perfIndexCum= ($perfIndexCum  * (1+$perfIndex)) ;

         $data['specifiekeIndex']=($perfIndexCum-1)*100;
      //  echo "$cat $datum $perfIndexCum= ($perfIndexCum  * (1+$perfIndex)) ;<br>\n";

         $hcatWaarden['periode'][$cat]['portefeuille'][]=$data['index']-100;
         if($indexLookup[$cat]!='')
         {
           $hcatWaarden['periode'][$cat]['specifiekeIndex'][] = $data['specifiekeIndex'];
           $hcatWaarden['periode'][$cat]['specifiekeIndexFonds'] = $fondsOmschrijvingen[$indexLookup[$cat]];
         }
         if($periode=='jaar')
           $hcatWaarden['periode'][$cat]['datum'][]= date("Y",$juldate);
         else
           $hcatWaarden['periode'][$cat]['datum'][]= date("M",$juldate);
         $hcatWaarden['periode'][$cat]['waarde'][]=$data;
      }
    }

  }
//listarray($hcatWaarden);


  if($periode=='jaar')
    $this->pdf->rapport_titel = "Historisch rendement per beleggingscategorie versus bijbehorende benchmark";
  else
    $this->pdf->rapport_titel = "Rendement per beleggingscategorie over lopende jaar versus bijbehorende benchmark";



  $this->pdf->setWidths($listWidths);
  $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R'));

  //$categorien=array('ZAK','VAR');

  $n=1;
  $lastCat=$categorien[count($categorien)-1];


  foreach ($categorien as $cat)
  {
    if($cat=='totaal')
      continue;
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->setWidths(array(150));
    $this->pdf->Row(array( vertaalTekst($hCategorieOmschrijvingen[$cat],$this->pdf->rapport_taal)));
    $this->pdf->setWidths($listWidths);
    if($indexLookup[$cat]== '')
    {
      $bmPerf='';
      $perfVerschil='';
    }
    else
    {
      $bmPerf= vertaalTekst('cumu. bench', $this->pdf->rapport_taal);
      $perfVerschil=vertaalTekst('verschil', $this->pdf->rapport_taal);
    }

    if ( $this->firstHeader === true ) {
      $this->pdf->excelData[]=array( vertaalTekst('Categorie', $this->pdf->rapport_taal), vertaalTekst('periode', $this->pdf->rapport_taal),  vertaalTekst('beginvermogen', $this->pdf->rapport_taal),  vertaalTekst('mutaties', $this->pdf->rapport_taal),
        vertaalTekst('inkomsten uit beleggingen', $this->pdf->rapport_taal), vertaalTekst('eindvermogen', $this->pdf->rapport_taal), vertaalTekst('rendement', $this->pdf->rapport_taal),
        vertaalTekst('in %', $this->pdf->rapport_taal), vertaalTekst('cumu. in %', $this->pdf->rapport_taal), $bmPerf,$perfVerschil);

      $this->firstHeader = false;
    }

    $this->pdf->Row(array( vertaalTekst('periode', $this->pdf->rapport_taal),  vertaalTekst('beginvermogen', $this->pdf->rapport_taal),  vertaalTekst('mutaties', $this->pdf->rapport_taal),
      vertaalTekst('inkomsten uit beleggingen', $this->pdf->rapport_taal), vertaalTekst('eindvermogen', $this->pdf->rapport_taal), vertaalTekst('rendement', $this->pdf->rapport_taal),
      vertaalTekst('in %', $this->pdf->rapport_taal), vertaalTekst('cumu. in %', $this->pdf->rapport_taal), $bmPerf,$perfVerschil));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    foreach($hcatWaarden['periode'][$cat]['datum'] as $i=>$datum)
    {
      //listarray($hcatWaarden['periode'][$cat]['waarde'][$i]);
     // $brutoResultaat=$hcatWaarden['periode'][$cat]['waarde'][$i]['resultaat']-$hcatWaarden['periode'][$cat]['waarde'][$i]['kosten']-$hcatWaarden['periode'][$cat]['waarde'][$i]['kostenNietGekoppeld'];
     // $brutoRendement=$brutoResultaat/$hcatWaarden['periode'][$cat]['waarde'][$i]['gemWaarde']*100;
      if($indexLookup[$cat]== '')
      {
        $bmPerf='';
        $perfVerschil='';
        $bmPerfXls='';
        $perfVerschilXls='';
      }
      else
      {
        $bmPerfXls= $hcatWaarden['periode'][$cat]['waarde'][$i]['specifiekeIndex'];
        $perfVerschilXls= $hcatWaarden['periode'][$cat]['waarde'][$i]['indexBruto']-100-$hcatWaarden['periode'][$cat]['waarde'][$i]['specifiekeIndex'];
        $bmPerf= $this->formatGetal($bmPerfXls,2);
        $perfVerschil= $this->formatGetal($perfVerschilXls,2);
      }

      $this->pdf->Row(array($datum,
      $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['beginwaarde'],2),
      $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['stortEnOnttrekking'],2),
      $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['gerealiseerd']+$hcatWaarden['periode'][$cat]['waarde'][$i]['opbrengst'],2),
      $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['eindwaarde'],2),
      $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['resultaatBruto'],2),
      $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['procentBruto']*100,2),
      $this->formatGetal($hcatWaarden['periode'][$cat]['waarde'][$i]['indexBruto']-100,2),
      $bmPerf,
      $perfVerschil
      ));

      $this->pdf->excelData[]= array($hCategorieOmschrijvingen[$cat],
        $datum,
        round($hcatWaarden['periode'][$cat]['waarde'][$i]['beginwaarde'],2),
        round($hcatWaarden['periode'][$cat]['waarde'][$i]['stortEnOnttrekking'],2),
        round($hcatWaarden['periode'][$cat]['waarde'][$i]['gerealiseerd']+$hcatWaarden['periode'][$cat]['waarde'][$i]['opbrengst'],2),
        round($hcatWaarden['periode'][$cat]['waarde'][$i]['eindwaarde'],2),
        round($hcatWaarden['periode'][$cat]['waarde'][$i]['resultaatBruto'],2),
        round($hcatWaarden['periode'][$cat]['waarde'][$i]['procentBruto']*100,2),
        round($hcatWaarden['periode'][$cat]['waarde'][$i]['indexBruto']-100,2),
        round($bmPerfXls,2),
        round($perfVerschilXls,2)
      );
    }
    $this->pdf->excelData[]=array();
    if($indexLookup[$cat]=='')
    {
      $kleuren=array(array(87,165,25));
    }
    else
    {
      $kleuren=array(array(87,165,25),array(0,52,121));
    }
    $this->pdf->ln();
    if($n%2==0)
    {
      $this->pdf->setY(45);
      $this->pdf->setXY(220,120);
      $this->LineDiagram(70, 60, $chartData['periode'][$cat],$kleuren,0,0,6,5,$periode,$cat);//50
    }
    else
    {
      $this->pdf->setXY(220,40);
      $this->LineDiagram(70, 60, $chartData['periode'][$cat],$kleuren,0,0,6,5,$periode,$cat);//50
      $this->pdf->setY(120);
    }
    
    if($n==2 && $cat <> $lastCat )
    {
      $this->pdf->addPage();
      $this->pdf->setY(45);
      $n=0;
    }
    $n++;
  }



  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }


function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$periode='maand',$cat='')
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $benchmarkOmschrijving=$data['specifiekeIndexFonds'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    $legendaItems= $data['legenda'];
  


    if(count($data1)>0)
      $bereikdata = array_merge($data,$data1);
    else
      $bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
  
   // $this->pdf->TextWithRotation($XPage,$YPage,$cat,25);
    
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w - $margin * 1 );

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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

    if($periode=='maand')
      $unit = $lDiag / 12;

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
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
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
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");

      $n++;
      if($n >20)
         break;
    }
    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    $jaren=ceil(count($data)/12);
    for ($i=0; $i<count($data); $i++)
    {
      if($i%$jaren==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }

    
    if(is_array($data1))
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);

         $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));

   //   $XPage
   // $YPage
    if(isset($color1))
      $legendaItems=array('portefeuille',($benchmarkOmschrijving<>''?$benchmarkOmschrijving:'benchmark'));
    else
      $legendaItems=array('portefeuille');
    $step=5;
    foreach ($legendaItems as $index=>$item)
    {
      if($index==0)
        $kleur=$color;
      else
        $kleur=$color1;
    $this->pdf->SetDrawColor($kleur[0],$kleur[1],$kleur[2]);
    $this->pdf->Rect($XPage+$step, $YPage+$h+10, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+10);
    $this->pdf->Cell(0,3, vertaalTekst($item,$this->pdf->rapport_taal));
    $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }

  function fondsPerf($fonds,$van,$tot)
  {
    $DB=new DB();
    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
      $verdeling[$data['fonds']]=$data['percentage'];

    if(count($verdeling)==0)
      $verdeling[$fonds]=100;

    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $janKoers=$DB->lookupRecord();
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
    	$DB->SQL($query);
      $startKoers=$DB->lookupRecord();

      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	    $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      $perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perf=$perfJaar-$perfVoorPeriode;
      
      if($this->pdf->debug==true)
      {
        echo "koers $fonds ".substr($tot,0,4)."-01-01 ".$janKoers['Koers']."<br>\n";
        echo "koers $fonds $van ".$startKoers['Koers']."<br>\n";
        echo "koers $fonds $tot ".$eindKoers['Koers']."<br>\n";
        echo "perf voor begin $perfVoorPeriode = (".$startKoers['Koers']." - ".$janKoers['Koers'].") / (".$janKoers['Koers'].")<br>\n";
        echo "Perf tot einddatum $perfJaar =(".$eindKoers['Koers']." - ".$janKoers['Koers'].") / ".($janKoers['Koers'])."<br>\n";
        echo "m<b> $fonds $van,$tot  $perf </b>= ( $perfJaar - $perfVoorPeriode ) <br>\n";
      }
      $totalPerf+=($perf*$percentage/100);
    }  
    //echo "t $fonds $totalPerf $van,$tot<br>\n";

    return $totalPerf;
  }

}
?>