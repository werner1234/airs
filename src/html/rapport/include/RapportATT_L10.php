<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/01/30 16:46:57 $
File Versie					: $Revision: 1.6 $

$Log: RapportATT_L10.php,v $
Revision 1.6  2019/01/30 16:46:57  rvv
*** empty log message ***

Revision 1.5  2019/01/16 16:28:10  rvv
*** empty log message ***

Revision 1.4  2018/12/05 16:36:17  rvv
*** empty log message ***

Revision 1.3  2018/12/03 06:50:50  rvv
*** empty log message ***

Revision 1.2  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.1  2018/10/07 08:29:03  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");


class RapportATT_L10
{
	function RapportATT_L10($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Performancemeting in de tijd";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

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

	function afmBlock()
  {
    global $__appvar;
    $DB=new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
  
    $beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
    $q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
    $DB->SQL($q);
    $DB->Query();
    $kleuren = $DB->LookupRecord();
    $kleuren = unserialize($kleuren['grafiek_kleur']);
    $kleuren = $kleuren['AFM'];

  
    $query = "SELECT TijdelijkeRapportage.afmCategorieOmschrijving as Omschrijving, ".
      " TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
      " TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.afmCategorie, ".
      " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
      " SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
      " FROM TijdelijkeRapportage ".
      " WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
      " TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
      .$__appvar['TijdelijkeRapportageMaakUniek'].
      " GROUP BY TijdelijkeRapportage.afmCategorie ".
      " ORDER BY TijdelijkeRapportage.afmCategorie asc ";
    debugSpecial($query,__FILE__,__LINE__);
  
    $DB->SQL($query);
    $DB->Query();
    
    $this->pdf->setY(100);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY()-4, 287/2-$this->pdf->marge, 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->setWidths(array(75,30,30));
    $this->pdf->row(array(vertaalTekst("AFM categorie",$this->pdf->rapport_taal),
                      vertaalTekst("in ".$this->pdf->rapportageValuta,$this->pdf->rapport_taal),
                      vertaalTekst("in %",$this->pdf->rapport_taal)));
    $this->pdf->SetTextColor(0);

    
    $totalen=array();
    while($categorien = $DB->NextRecord())
    {
      $percentageVanTotaal=$categorien['subtotaalactueel']/$totaalWaarde;
      $this->pdf->row(array($categorien['Omschrijving'],
                        $this->formatGetal($categorien['subtotaalactueel'],2),
                        $this->formatGetal($percentageVanTotaal*100,1).""));
      $totalen['subtotaalactueel']+=$categorien['subtotaalactueel'];
      $totalen['percentage']+=$percentageVanTotaal;
  
      $pieData[$categorien['Omschrijving']]=$percentageVanTotaal*100;
      $grafiekKleuren[]=array($kleuren[$categorien['afmCategorie']]['R']['value'],$kleuren[$categorien['afmCategorie']]['G']['value'],$kleuren[$categorien['afmCategorie']]['B']['value']);
    }
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array("Totalen",$this->formatGetal($totalen['subtotaalactueel'],2),
                      $this->formatGetal($totalen['percentage']*100,1).""));
    
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $afm=AFMstd($this->portefeuille,$this->rapportageDatum,$this->pdf->debug);
  
    $this->pdf->ln(4);
    // $this->pdf->SetWidths(array(40,35+25,0,25,25,15,115));
    $this->pdf->Row(array(vertaalTekst('AFM-standaarddeviatie',$this->pdf->rapport_taal).'*','',$this->formatGetal($afm['std'],2)." %"));
  
    $this->pdf->setY(160);
    $this->pdf->SetWidths(array(135));
    $this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize-1);
    $this->pdf->Row(array(vertaalTekst('* Standaarddeviatie op basis van leidraad risicoprofielen AFM 2021 en risicostandaarden beleggingen 2021 CFA Society VBA Netherlands. De daadwerkelijke standaarddeviatie van uw portefeuille kan hiervan afwijken.',$this->pdf->rapport_taal)));
    
  
    $this->pdf->SetXY(190, 100);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',10);
    $this->pdf->Cell(50,4,vertaalTekst('Onderverdeling in AFM categorieën', $this->pdf->rapport_taal),0,1,"C");
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetX(190);
    $this->pdf->PieChart(100, 50, $pieData, '%l (%p)', $grafiekKleuren);
    
    
  }


	function writeRapport()
	{
	  global $__appvar;

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));


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




//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


  $index=new indexHerberekening();
  $index->voorStartdatumNegeren=true;
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

 
$i=0;
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
  }
}

 
   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(160,168,20);
         $this->pdf->SetFillColor($this->pdf->rapport_background_fill[0],$this->pdf->rapport_background_fill[1],$this->pdf->rapport_background_fill[2]);
          
        //$factor=2;
        //$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*$factor,$this->pdf->rapport_kop_bgcolor['g']*$factor,$this->pdf->rapport_kop_bgcolor['b']*$factor);


        $totaalRendament=100;
        $totaalRendamentIndex=100;
        $totaal=array();
        $perioden=array('jaar','kwartaal');
        $fill=true;
        $qPerf=0;  
		    foreach ($rendamentWaarden as $row)
		    {
		      //listarray($row);
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
          $kwartaal = ceil(date("n",$datum)/3);

          if(isset($lastKwartaal) && $lastKwartaal!=$kwartaal)
          {
          //   $this->printTotaal($totaal,$lastKwartaal);
    
            $totaal['kwartaal']=array();
            $qPerf=0; 
         //   $fill=true;

          }
          
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
          
          $this->pdf->CellBorders = array();
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],2),
		                           $this->formatGetal($row['opbrengsten'],2),
		                           $this->formatGetal($row['kosten'],2),
		                           $this->formatGetal($row['rente'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['waardeHuidige'],2),
		                           $this->formatGetal($row['performance'],2)."%",
		                           $this->formatGetal($row['index']-100,2)."%"));
                               
                             
           foreach($perioden as $periode)
           {
             
		                           if(!isset($totaal[$periode]['waardeBegin']))
		                             $totaal[$periode]['waardeBegin']=$row['waardeBegin'];
		                           $totaal[$periode]['Waarde'] = $row['waardeHuidige'];
		                           $totaal[$periode]['Resultaat'] += $row['resultaatVerslagperiode'];
		                           $totaal[$periode]['Gerealiseerd'] += $row['gerealiseerd'];
		                           $totaal[$periode]['Ongerealiseerd'] += $row['ongerealiseerd'];
		                           $totaal[$periode]['Opbrengsten'] += $row['opbrengsten'];
		                           $totaal[$periode]['Kosten'] += $row['kosten'];
		                           $totaal[$periode]['Rente'] += $row['rente'];
		                           $totaal[$periode]['StortingenOntrekkingen'] += $row['stortingen']-$row['onttrekkingen'];
                               if($periode=='kwartaal')
                               {
                                 $qPerf=((1+$qPerf)*(1+$row['performance']/100))-1;
                                 $totaal[$periode]['Rendament'] = $qPerf;
                               }                               
                               else
		                             $totaal[$periode]['Rendament'] = $row['index'];
           }
		    $n++;
        $i++;
          $lastKwartaal=$kwartaal;
		    }
    
		    $this->pdf->fillCell=array();
       // $this->printTotaal($totaal,$lastKwartaal);

            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


     //   $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['jaar']['waardeBegin'],2),
		                           $this->formatGetal($totaal['jaar']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['jaar']['Gerealiseerd']+$totaal['jaar']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['jaar']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['jaar']['Kosten'],2),
		                           $this->formatGetal($totaal['jaar']['Rente'],2),
		                           $this->formatGetal($totaal['jaar']['Resultaat'],2),
		                           $this->formatGetal($totaal['jaar']['Waarde'],2),
		                           '',
		                           $this->formatGetal($totaal['jaar']['Rendament']-100,2)."%"
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }

		  $this->afmBlock();
	   $this->pdf->fillCell = array();


	}

function printTotaal($totaal,$kwartaal)
{//

      //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
      $this->pdf->fillCell=array();
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','TS',''); 
		    $this->pdf->row(array(vertaalTekst('Totaal Q'.$kwartaal,$this->pdf->rapport_taal),
		                           $this->formatGetal($totaal['kwartaal']['waardeBegin'],2),
		                           $this->formatGetal($totaal['kwartaal']['StortingenOntrekkingen'],2),
		                           $this->formatGetal($totaal['kwartaal']['Gerealiseerd']+$totaal['kwartaal']['Ongerealiseerd'],2),
		                           $this->formatGetal($totaal['kwartaal']['Opbrengsten'],2),
		                           $this->formatGetal($totaal['kwartaal']['Kosten'],2),
		                           $this->formatGetal($totaal['kwartaal']['Rente'],2),
		                           $this->formatGetal($totaal['kwartaal']['Resultaat'],2),
		                           $this->formatGetal($totaal['kwartaal']['Waarde'],2),
		                           $this->formatGetal($totaal['kwartaal']['Rendament']*100,2),
                               ''
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->ln(1);
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





}
?>