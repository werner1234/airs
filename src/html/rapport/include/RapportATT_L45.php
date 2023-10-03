<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.16 $

$Log: RapportATT_L45.php,v $
Revision 1.16  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.15  2015/10/14 16:12:05  rvv
*** empty log message ***

Revision 1.14  2014/07/12 15:30:53  rvv
*** empty log message ***

Revision 1.13  2013/10/02 15:48:20  rvv
*** empty log message ***

Revision 1.12  2013/08/07 17:19:23  rvv
*** empty log message ***

Revision 1.11  2013/07/09 08:05:48  rvv
*** empty log message ***

Revision 1.10  2013/06/19 15:54:30  rvv
*** empty log message ***

Revision 1.9  2013/05/29 11:46:50  rvv
*** empty log message ***

Revision 1.8  2013/05/26 13:54:49  rvv
*** empty log message ***

Revision 1.7  2013/05/04 15:59:49  rvv
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

Revision 1.7  2013/04/06 16:16:30  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once("rapport/ATTberekening2.php");

class RapportATT_L45
{
	function RapportATT_L45($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Maandelijks verloop en resultaat";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->realRapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) == db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  }
	  else
	  {
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	   if ($this->rapportageDatumVanaf != "$RapStartJaar-01-01" && $this->pdf->engineII == false)
	   {
	    $fondswaarden =  berekenPortefeuilleWaarde($this->portefeuille,"$RapStartJaar-01-01",true);
      vulTijdelijkeTabel($fondswaarden ,$this->portefeuille,"$RapStartJaar-01-01");
      $this->extraVulling = true;
	   }
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
  
  
  function toonGrafiek($grafiekData)
  {
  		  if (count($grafiekData) > 1)
		  {
        $this->pdf->SetXY(8,95);//104
  		  $this->pdf->Cell(0, 5, 'Rendement (cumulatief)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,105)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(270, 30, $grafiekData,$this->pdf->rapport_grafiek_color,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 80);
		  }
  }



	function writeRapport()
	{
	  global $__appvar;

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
//$this->pdf->rowHeight=5;

	 	//Kleuren instellen
		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur ,grafiek_sortering FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$allekleuren = unserialize($kleuren['grafiek_kleur']);
    $this->categorieKleuren=$allekleuren['OIB'];
	 // $this->categorieOmschrijving=array('LIQ'=>'Liquiditeiten','ZAK'=>'Zakelijke waarden','VAR'=>'Vastrentende waarden','Liquiditeiten'=>'Liquiditeiten');



		$this->pdf->AddPage();
    $this->pdf->widthA = array(25,30,25,25,25,20,20,20,22,27,20,20);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->Ln(7);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
   	for($i=0;$i<count($this->pdf->widthA);$i++)
		  $this->pdf->fillCell[] = 1;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);


		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
//    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Opgelopen-\nrente ",
                          "Kosten\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n ",
		                      "Rendement\n(maand)",
		                      "Rendement\n(Cumulatief)"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $this->pdf->fillCell=array();
    $sumWidth = array_sum($this->pdf->widthA);
    
   
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
  $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);



  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

  //$indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

//exit;

foreach ($indexData as $index=>$data)
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
		  $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=$data['Omschrijving'];
		}


$grafiekData['Datum'][]="$RapStartJaar-12-01";

   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $totaalRendament=100;
        $totaalRendamentIndex=100;
        $this->pdf->SetFillColor(230,230,230);
        $fill=false;
		    foreach ($rendamentWaarden as $row)
		    {
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
          
          if($fill==true)
		      {
		        $this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
	          $fill=true;
		      }

		      $this->pdf->row(array(vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],0),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
		                           $this->formatGetal($row['gerealiseerd'],0),
		                           $this->formatGetal($row['ongerealiseerd'],0),
		                           $this->formatGetal($row['opbrengsten'],0),
                               $this->formatGetal($row['rente'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['resultaatVerslagperiode'],0),
		                           $this->formatGetal($row['waardeHuidige'],0),
		                           $this->formatGetal($row['performance'],2),
		                           $this->formatGetal($row['index']-100,2)));

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
		    }
		    $this->pdf->fillCell=array();
        $this->pdf->ln(3);
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->SetDrawColor($this->pdf->rapport_paars[0],$this->pdf->rapport_paars[1],$this->pdf->rapport_paars[2]);
        $this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U','U','U');
		    $this->pdf->row(array('Samenvatting',
		                           $this->formatGetal($waardeBegin,0),
		                           $this->formatGetal($totaalStortingenOntrekkingen,0),
		                           $this->formatGetal($totaalGerealiseerd,0),
		                           $this->formatGetal($totaalOngerealiseerd,0),
		                           $this->formatGetal($totaalOpbrengsten,0),
		                           $this->formatGetal($totaalRente,0),
		                           $this->formatGetal($totaalKosten,0),
		                           $this->formatGetal($totaalResultaat,0),
		                           $this->formatGetal($totaalWaarde,0),
		                           '',
		                           $this->formatGetal($totaalRendament-100,2)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
        unset($this->pdf->CellBorders);
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }
/*
    if(round($totaalResultaat) != round($resultaatVerslagperiode['Totaal']))
    {
     echo "<script  type=\"text/JavaScript\">alert('Beleggingsresultaat totaal (".(round($resultaatVerslagperiode['Totaal'],2)).") komt niet overeen met perioden (".round($totaalResultaat,2).") verschil (".(round($resultaatVerslagperiode['Totaal'],2)-round($totaalResultaat,2)).") voor portefeuille ".$this->portefeuille."'); </script>";
      ob_flush();
    }


		  if (count($barGraph) > 0)
		  {
		      $this->pdf->SetXY(15,180)		;//112
		      $this->VBarDiagram(267, 70, $barGraph['Index'],'Vermogensverdeling');
		  }


		  $this->pdf->SetXY(8, 155);//165



		$this->pdf->ln(10);
		$this->pdf->SetX(108);


	  $this->pdf->MultiCell(170,4,$titel,0,'L');
	  $this->pdf->SetX(108);
    */
   $this->pdf->fillCell = array();

$this->toonGrafiek($grafiekData);
//$this->pdf->rowHeight=4;

$this->toonATT();

$this->pdf->rapport_titel ="Vergelijkingsmaatstaven en kosten";
$this->toonIndex();
$this->toonKosten();

	}
  
 	function toonKosten()
	{ 
     $data['periode']=$this->getKosten($this->realRapportageDatumVanaf,$this->rapportageDatum);
	  $data['ytm']=$this->getKosten($this->rapportageDatumVanaf,$this->rapportageDatum);

    $this->pdf->Ln(20);
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    
       
    for($i=0;$i<count($this->pdf->widthA);$i++)
	  $this->pdf->fillCell[] = 1;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    
    
    
		$this->pdf->row(array(vertaalTekst("Kosten",$this->pdf->rapport_taal),"Verslagperiode","Lopend jaar"));
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);


		$keys=array();
 		foreach ($data['ytm']['kostenPerGrootboek'] as $key=>$val)
		  $keys[$key]=$key;         
		foreach ($keys as $key)
		{
		  if(round($data['periode']['kostenPerGrootboek'][$key],2) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],2) != 0.00)
			  $this->pdf->row(array(vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],2),$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],2),""));
		}


    $this->pdf->CellBorders=array('','TS','TS');
		$this->pdf->row(array("",$this->formatGetal($data['periode']['totaalKosten'],2),$this->formatGetal($data['ytm']['totaalKosten'],2)));
    unset($this->pdf->CellBorders);
  }
  
  
  
	function getKosten($vanafDatum,$totDatum)
	{
  	$waarden=array();
    $DB=new DB();

		  $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "Grootboekrekeningen.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();

		while($kosten = $DB->nextRecord())
		{
			if($kosten['Grootboekrekening'] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			elseif($kosten['Grootboekrekening'] == "KOBU")
			{
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}


			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}
					foreach ($kostenPerGrootboek as $data)
			{
			  $tmp[$data['Omschrijving']]=$data['Bedrag'];
			}

		$waarden['kostenPerGrootboek']=$tmp;
		$waarden['totaalKosten']=$totaalKosten;

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		$waarden['kostenProcent']=$kostenProcent;
		$waarden['koersResulaatValutas']=$koersResulaatValutas;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		return $waarden;
	}
  
function toonIndex()
	{
	  global $__appvar;
	  $this->pdf->addPage();
    $this->pdf->Ln(7);
	  $this->pdf->templateVars['INDEXPaginas'] = $this->pdf->customPageNo;

	  $RapStartJaar = date("Y", db2jul($this->realRapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->realRapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";


	  $DB=new DB();
	  $perioden=array('jan'=>$this->tweedePerformanceStart,'begin'=>$this->realRapportageDatumVanaf,'eind'=>$this->rapportageDatum);

	  $query="SELECT
Indices.Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta,
Indices.toelichting,
BeleggingscategoriePerFonds.Vermogensbeheerder,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as catOmschrijving
FROM
Indices
Inner Join Fondsen ON Indices.Beursindex = Fondsen.Fonds
LEFT Join BeleggingscategoriePerFonds ON Indices.Beursindex = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE Indices.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Indices.Afdrukvolgorde";


		$DB->SQL($query);
		$DB->Query();
		$benchmarkCategorie=array();
	  while($index = $DB->nextRecord())
		{
      if($index['catOmschrijving'] == '')
        $index['catOmschrijving']='Overige';

		  $benchmarkCategorie[$index['catOmschrijving']][]=$index['Beursindex'];

		 	$indexData[$index['Beursindex']]=$index;
      foreach ($perioden as $periode=>$datum)
      {
        $indexData[$index['Beursindex']]['fondsKoers_'.$periode]=$this->getFondsKoers($index['Beursindex'],$datum);
        $indexData[$index['Beursindex']]['valutaKoers_'.$periode]=getValutaKoers($index['Valuta'],$datum);
      }
     	$indexData[$index['Beursindex']]['performanceJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan'])    / ($indexData[$index['Beursindex']]['fondsKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performance'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']) / ($indexData[$index['Beursindex']]['fondsKoers_begin']/100 );
  		$indexData[$index['Beursindex']]['performanceEurJaar'] = ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_jan']  *$indexData[$index['Beursindex']]['valutaKoers_jan'])/(  $indexData[$index['Beursindex']]['fondsKoers_jan']*  $indexData[$index['Beursindex']]['valutaKoers_jan']/100 );
			$indexData[$index['Beursindex']]['performanceEur'] =     ($indexData[$index['Beursindex']]['fondsKoers_eind']*$indexData[$index['Beursindex']]['valutaKoers_eind'] - $indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin'])/($indexData[$index['Beursindex']]['fondsKoers_begin']*$indexData[$index['Beursindex']]['valutaKoers_begin']/100 );
		}

		$query = "SELECT
TijdelijkeRapportage.valuta,Valutas.Omschrijving,
Valutas.Afdrukvolgorde
FROM
TijdelijkeRapportage
Inner Join Valutas ON TijdelijkeRapportage.valuta = Valutas.Valuta WHERE Portefeuille='".$this->portefeuille."' AND TijdelijkeRapportage.valuta <> '".$this->pdf->rapportageValuta."' GROUP BY Valuta
ORDER BY Valutas.Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
	  while($valuta = $DB->nextRecord())
		{
		  $valutas[]=$valuta['Valuta'];
		  $indexValuta[$valuta['valuta']]=$valuta;
		  foreach ($perioden as $periode=>$datum)
      {
        $indexValuta[$valuta['valuta']]['valutaKoers_'.$periode]=getValutaKoers($valuta['valuta'],$datum);
      }
      $indexValuta[$valuta['valuta']]['performanceJaar'] = ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_jan'])    / ($indexValuta[$valuta['valuta']]['valutaKoers_jan']/100 );
			$indexValuta[$valuta['valuta']]['performance'] =     ($indexValuta[$valuta['valuta']]['valutaKoers_eind'] - $indexValuta[$valuta['valuta']]['valutaKoers_begin']) / ($indexValuta[$valuta['valuta']]['valutaKoers_begin']/100 );
		}

  	$this->pdf->SetWidths(array(60,33,33,33,33,33));
  	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R'));
 	  

  	for($i=0;$i<count($this->pdf->widthA);$i++)
	  $this->pdf->fillCell[] = 1;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
  
  	if($perioden['jan']==$perioden['begin'])
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U');
  	  $this->pdf->row(array("Index\n ","Koers ".date("d-m-Y",db2jul($perioden['begin']))."\n ","Koers ".date("d-m-Y",db2jul($perioden['eind']))."\n ",'Rendement verslagperiode in %'));
  	}
  	else
  	{
  	  $this->pdf->CellBorders = array('','U','U','U','U','U','U');
  	  $this->pdf->row(array("Index\n ","Koers ".date("d-m-Y",db2jul($perioden['jan']))."\n ","Koers ".date("d-m-Y",db2jul($perioden['begin']))."\n ","Koers ".date("d-m-Y",db2jul($perioden['eind']))."\n ",'Rendement verslagperiode in %','Rendement vanaf '.date("d-m-Y",db2jul($this->tweedePerformanceStart)).' in %'));
  	}
  	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	unset($this->pdf->CellBorders);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);


  	foreach ($benchmarkCategorie as $categorie=>$fondsen)
  	{
  	  $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  	  $this->pdf->row(array("",$categorie));
  	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  	  foreach ($fondsen as $fonds)
  	  {
  	    $fondsData=$indexData[$fonds];
  	    if($perioden['jan']==$perioden['begin'])
  	    {
  	      $this->pdf->row(array($fondsData['Omschrijving'],
     	    $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
  	      $this->formatGetal($fondsData['performance'],1)));
  	    }
  	    else
  	    {
  	      $this->pdf->row(array($fondsData['Omschrijving'],
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_jan'],2),
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_begin'],2),
  	      $this->formatGetal($indexData[$fonds]['fondsKoers_eind'],2),
  	      $this->formatGetal($fondsData['performance'],1),$this->formatGetal($fondsData['performanceJaar'],1)));
  	    }
  	  }
  	}

	}
  	function getFondsKoers($fonds,$datum)
	{
	  $db=new DB();
	  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
	  $db->SQL($query);
	  $koers=$db->lookupRecord();
	  return $koers['Koers'];
	}

  
  
function toonATT()
	{
		global $__appvar;
		$query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

		if(db2jul(date("Y",$this->pdf->rapport_datum)."-01-01") > db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);

	  $this->tweedePerformanceStart=$rapportageStartJaar;

		$startDatumTekst=date("j",$this->pdf->rapport_datumvanaf)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datumvanaf)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datumvanaf);
    $rapDatumTekst=date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum);
    $startJaarDatumTekst=date("j",db2jul($rapportageStartJaar))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($rapportageStartJaar))],$this->pdf->rapport_taal)." ".date("Y",db2jul($rapportageStartJaar));



		$this->pdf->templateVars['ATTPaginas'] = $this->pdf->customPageNo;//+$this->pdf->extraPage
		$this->pdf->setY(150);
		$this->pdf->SetWidths(array(55,65, 40, 55,65));
		$this->pdf->SetAligns(array('L','C','L','L','C'));
    //$this->pdf->CellBorders = array('T','T','T','T','T');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		//$this->pdf->row(array('','Beleggingsresultaat','','','Beleggingsresultaat'));
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);

		$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
	//	$this->pdf->row(array('',"   ",'','',"  "));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths(array(63,22,22,23, 20, 63,22,22,23));
		$this->pdf->SetAligns(array('L','R','R','R','C','L','R','R','R'));
    $y=$this->pdf->getY();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    for($i=0;$i<count($this->pdf->widths);$i++)
      $this->pdf->fillCell[] = 1;
		$this->pdf->row(array(vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal),'',"","",'',vertaalTekst('Beleggingscategorie',$this->pdf->rapport_taal)));
		$this->pdf->setY($y);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			$this->pdf->CellBorders = array('U','U','U','U','U','U','U','U','U','U');
		$this->pdf->row(array("$startDatumTekst - $rapDatumTekst\n ",vertaalTekst('Resultaat',$this->pdf->rapport_taal)."\n ",vertaalTekst('Performance',$this->pdf->rapport_taal)." ".vertaalTekst('categorie',$this->pdf->rapport_taal),vertaalTekst('Performance',$this->pdf->rapport_taal)."\n".vertaalTekst('contributie',$this->pdf->rapport_taal)."  ","\n "," $startJaarDatumTekst - $rapDatumTekst \n ",
		vertaalTekst('Resultaat',$this->pdf->rapport_taal)."\n ",vertaalTekst('Performance',$this->pdf->rapport_taal)."\n".vertaalTekst('categorie',$this->pdf->rapport_taal)."  ",vertaalTekst('Performance',$this->pdf->rapport_taal)."\n".vertaalTekst('contributie',$this->pdf->rapport_taal)."  "));
  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);

    unset($this->pdf->CellBorders);
    unset($this->pdf->fillCell);

  

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

        $query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Grootboekrekening,
SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers)-(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers)$koersQuery) as waarde,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Omschrijving
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND Grootboekrekeningen.Kosten=1 AND Rekeningmutaties.Fonds='' AND
Rekeningmutaties.Boekdatum > '".$this->realRapportageDatumVanaf."'  AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening";

    $DB=new DB();
 		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
      $totalen['rapportagePeriode']['kosten'] += $data['waarde'];
		}

		        $query="SELECT
Rekeningen.Portefeuille,
Rekeningen.Rekening,
Rekeningmutaties.Grootboekrekening,
SUM((Rekeningmutaties.Credit*Rekeningmutaties.Valutakoers)-(Rekeningmutaties.Debet*Rekeningmutaties.Valutakoers)$koersQuery) as waarde,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
Grootboekrekeningen.Kosten,
Grootboekrekeningen.Opbrengst,
Grootboekrekeningen.Omschrijving
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE
Rekeningen.Portefeuille='".$this->portefeuille."' AND Grootboekrekeningen.Kosten=1 AND Rekeningmutaties.Fonds='' AND
Rekeningmutaties.Boekdatum > '".$this->tweedePerformanceStart."'  AND  Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."'
GROUP BY Rekeningmutaties.Grootboekrekening";

    $DB=new DB();
 		$DB->SQL($query);
		$DB->Query();
		while($data = $DB->NextRecord())
		{
      $totalen['lopendeJaar']['kosten'] += $data['waarde'];
		}

    unset($this->pdf->CellBorders);

                 
$query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie AS `Hoofdcategorie`,
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
      $categorieVolgorde[$data['Hoofdcategorie']]=$data['HcatOmschrijving'];
		}

$att=new ATTberekening2($this);
$this->waarden['rapportagePeriode']=$att->bereken($this->realRapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
$this->waarden['lopendeJaar']=$att->bereken($this->tweedePerformanceStart,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');


$totalen['rapportagePeriode']['perf'] =performanceMeting($this->portefeuille,$this->realRapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);
$totalen['lopendeJaar']['perf']     =performanceMeting($this->portefeuille,$this->tweedePerformanceStart,  $this->rapportageDatum,$this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

    foreach ($categorieVolgorde as $hoofdCategorie=>$hoofdcategorieOmschrijving)
    {
     // $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
     // $this->pdf->row(array(vertaalTekst($hoofdCategorie,$this->pdf->rapport_taal),'','','','',vertaalTekst($hoofdCategorie,$this->pdf->rapport_taal)));
    //  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

    //  foreach ($categorien as $categorie=>$categorieOmschrijving)
    //  {
          $this->pdf->row(array(vertaalTekst($hoofdcategorieOmschrijving,$this->pdf->rapport_taal),
      		$this->formatGetal($this->waarden['rapportagePeriode'][$hoofdCategorie]['resultaat'],0),
      		$this->formatGetal($this->waarden['rapportagePeriode'][$hoofdCategorie]['procent'],1).'%',
      		$this->formatGetal($this->waarden['rapportagePeriode'][$hoofdCategorie]['bijdrage'],1).'%',
      		'',
      		vertaalTekst($hoofdcategorieOmschrijving,$this->pdf->rapport_taal),
      		$this->formatGetal($this->waarden['lopendeJaar'][$hoofdCategorie]['resultaat'],0),
      		$this->formatGetal($this->waarden['lopendeJaar'][$hoofdCategorie]['procent'],1).'%',
      		$this->formatGetal($this->waarden['lopendeJaar'][$hoofdCategorie]['bijdrage'],1).'%'));

      		$weging[$hoofdCategorie]=$this->waarden['lopendeJaar']['bijdrage'][$hoofdCategorie]/$this->waarden['lopendeJaar']['performance'][$hoofdCategorie];

      		$totalen['rapportagePeriode']['resultaat'] += $this->waarden['rapportagePeriode'][$hoofdCategorie]['resultaat'];
      		$totalen['rapportagePeriode']['opbrengsten'] += $this->waarden['rapportagePeriode'][$hoofdCategorie]['opbrengsten'];
      		$totalen['rapportagePeriode']['bijdrage'] += $this->waarden['rapportagePeriode'][$hoofdCategorie]['bijdrage'];

      		$totalen['lopendeJaar']['resultaat'] += $this->waarden['lopendeJaar'][$hoofdCategorie]['resultaat'];
      		$totalen['lopendeJaar']['opbrengsten'] += $this->waarden['lopendeJaar'][$hoofdCategorie]['opbrengsten'];
      		$totalen['lopendeJaar']['bijdrage'] += $this->waarden['lopendeJaar'][$hoofdCategorie]['bijdrage'];

    //  }
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->CellBorders = array('T','T','T','T','T','T','T','T','T','T');
    $this->pdf->Line($this->pdf->marge,$this->pdf->getY(),288,$this->pdf->getY());
    

    $this->pdf->row(array(vertaalTekst("Bruto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['rapportagePeriode']['resultaat'],0),
      		'',
      		$this->formatGetal($totalen['rapportagePeriode']['bijdrage'],1).'%',
      		'',
      		vertaalTekst("Bruto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['lopendeJaar']['resultaat'],0),
      		'',
      		$this->formatGetal($totalen['lopendeJaar']['bijdrage'],1).'%'));
    
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
 unset($this->pdf->CellBorders);

    $this->pdf->row(array( vertaalTekst("Beheer- en bewaarloon",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['rapportagePeriode']['kosten'],0),
      		'',
      		$this->formatGetal(round($totalen['rapportagePeriode']['perf'],1)-round($totalen['rapportagePeriode']['bijdrage'],1),1).'%',
      		'',
      		vertaalTekst("Beheer- en bewaarloon",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['lopendeJaar']['kosten'],0),
      		'',
      		$this->formatGetal(round($totalen['lopendeJaar']['perf'],1)-round($totalen['lopendeJaar']['bijdrage'],1),1).'%'));
      		

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->Line($this->pdf->marge,$this->pdf->getY(),288,$this->pdf->getY());

		$this->pdf->row(array(vertaalTekst("Netto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['rapportagePeriode']['resultaat']+$totalen['rapportagePeriode']['kosten'],0),
      		'',//$this->formatGetal($this->waarden['rapportagePeriode']['performance']['Totaal'],2)
      		$this->formatGetal($totalen['rapportagePeriode']['perf'],1).'%',
      		'',
      		vertaalTekst("Netto resultaat",$this->pdf->rapport_taal),
      		$this->formatGetal($totalen['lopendeJaar']['resultaat']+$totalen['lopendeJaar']['kosten'],0),
         	'',//$this->formatGetal($this->waarden['lopendeJaar']['performance']['Totaal'],2)
      		$this->formatGetal($totalen['lopendeJaar']['perf'],1).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);


  }


function formatGetalLength ($getal,$decimaal,$gewensteLengte)
{
 $lengte = strlen(round($getal));
 if($getal < 0)
  $lengte --;
 $mogelijkeDecimalen = $gewensteLengte - $lengte;
 if($lengte >$gewensteLengte)
   $decimaal = 0;
 elseif ($decimaal > $mogelijkeDecimalen)
   $decimaal = $mogelijkeDecimalen;
 return number_format($getal,$decimaal,',','');
}



	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='')
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
	$vorigeIndex = 100;
	$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	$datum == array();

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
	 	$indexData = array_merge($indexData,$this->BerekenMutaties($periode['start'],$periode['stop'],$portefeuille));
	 	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
 	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	  $data[$i] = $indexData;
    $i++;
	}
	return $data;
	}



	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();

    if(db2jul($beginDatum) < db2jul($this->pdf->PortefeuilleStartdatum))
      $wegingsDatum=$this->pdf->PortefeuilleStartdatum;
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;

		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,'EUR',$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
      
      if($regel['hoofdcategorie']=='')
        $regel['hoofdcategorie']='geen';

      $categorieVerdeling[$regel['hoofdcategorie']] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        //$categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        //$categorieVerdeling['OBL'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
       // $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging[totaal2]) / $gemiddelde) * 100;


	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS totaalkosten
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

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaalOpbrengsten
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

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']=array('cat'=>$categorieVerdeling);
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
    $lDiag = floor($w - $margin * 1 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(0,0,0);
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
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(155,155,155)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(155,155,155)));
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
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+6,date("d-M-Y",db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
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
        if ($i>0)
          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
         $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


  function VBarDiagram($w, $h, $data,$title)
  {
      global $__appvar;
      $XPage=$this->pdf->GetX();
      $YPage=$this->pdf->GetY();
      $this->pdf->SetXY($XPage,$YPage-$h);
      $this->pdf->SetFont($this->pdf->rapport_font, 'B', 8.5);
		  $this->pdf->Cell(0, 5, 'Vermogensverdeling', 0, 1);
  		//$this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),282,$this->pdf->GetY());
      $h=$h-10;
      $this->pdf->SetXY($XPage+10,$YPage);
          
          
      $legendaWidth = 60;
      $grafiekPunt = array();
      $verwijder=array();
      



      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach ($waarden as $categorie=>$waarde)
        {
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarde;
          $grafiekCategorie[$categorie][$datum]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarde));
          $minVal=min(array($minVal,$waarde));

          if($waarde < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarde;
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;

          if(!isset($this->categorieVolgorde[$categorie]))
          {
            $this->categorieVolgorde[$categorie]=$categorie;
            $this->categorieOmschrijving[$categorie]=$categorie;
          } 
          if(!isset($colors[$categorie])) 
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }



      $numBars = 12;//count($legenda);
      //$numBars=10;

      if($color == null)
      {
        $color=array(155,155,155);
      }


      if($maxVal <= 100)
        $maxVal=100;
      elseif($maxVal < 125)
        $maxVal=125;

      if($minVal >= 0)
        $minVal = 0;
      elseif($minVal > -25)
        $minVal=-25;

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie) //legenda
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$bGrafiek+3 , $YstartGrafiek-$hGrafiek+$n*8+2, 2, 2, 'F',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$bGrafiek+6 ,$YstartGrafiek-$hGrafiek+$n*8+1.5 );
          $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorie],0,0,'L');
          $n++;
        }
      }

      if($minVal < 0)
      {
        $unit = $hGrafiek / (-1 * $minVal + $maxVal) * -1;
        $nulYpos =  $unit * (-1 * $minVal);
      }
      else
      {
        $unit = $hGrafiek / $maxVal * -1;
        $nulYpos =0;
      }


      $horDiv = 5;
      $horInterval = $hGrafiek / $horDiv;
      $bereik = $hGrafiek/$unit;

      $this->pdf->SetFont($this->pdf->rapport_font, '', 7);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      $lineW=1;
      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull=true;
        if($i != $nulpunt)
          $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $lineW ,$i,array('dash' => 0,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        if($i != $nulpunt)
          $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $lineW ,$i,array('dash' => 0,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)."%",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(255,255,255)));
      $this->pdf->SetLineWidth(0.3527);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   
   foreach ($grafiek as $datum=>$data)
   {
      foreach (($this->categorieVolgorde) as $categorie)
      {
        if(isset($data[$categorie]))
        {
          $val=$data[$categorie];
        //foreach($data as $categorie=>$val)
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          //$this->pdf->Line($xval, $yval+$hval, $xval + $lval ,$yval+$hval,array('dash' => 0,'color'=>array(255,255,255)));
          
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);


         $legendaPrinted[$datum] = 1;
         }
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
      foreach (($this->categorieVolgorde) as $categorie)
      {
        if(isset($data[$categorie]))
        {
          $val=$data[$categorie];
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'D',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         }
      }
      $i++;
   }
   $this->pdf->SetLineWidth(0.1);
         $this->pdf->Line($XstartGrafiek, $nulpunt, $XstartGrafiek + $bGrafiek ,$nulpunt,array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->Line($XstartGrafiek, $nulpunt, $XstartGrafiek ,$top,array('dash' => 0,'color'=>array(0,0,0)));
      
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
}
?>