<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2016/10/05 16:18:26 $
File Versie					: $Revision: 1.20 $

$Log: RapportOIB_L36.php,v $
Revision 1.20  2016/10/05 16:18:26  rvv
*** empty log message ***

Revision 1.19  2015/01/11 12:48:50  rvv
*** empty log message ***

Revision 1.18  2014/05/10 13:54:39  rvv
*** empty log message ***

Revision 1.17  2014/03/19 16:39:09  rvv
*** empty log message ***

Revision 1.16  2014/03/12 15:13:44  rvv
*** empty log message ***

Revision 1.15  2014/03/08 18:58:03  rvv
*** empty log message ***

Revision 1.14  2014/03/08 17:02:57  rvv
*** empty log message ***

Revision 1.13  2014/03/01 14:01:38  rvv
*** empty log message ***

Revision 1.12  2013/07/13 15:19:44  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L36.php");

class RapportOIB_L36
{
	function RapportOIB_L36($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_startDatum = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->PortefeuilleStartdatumJul = db2jul($this->pdf->PortefeuilleStartdatum);
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->rapport_titel = "Kerngegevens portefeuille";
	}

	function getOIBdata($datum)
	{
	  global $__appvar;
	  $DB = new DB();
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
								 " rapportageDatum = '".$datum."' AND ".
								 " portefeuille = '".$this->portefeuille."'"
								 .$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$portefwaarde = $DB->nextRecord();
		$portTotaal = $portefwaarde['totaal'];
		$this->portTotaal[$datum]=$portTotaal;

		$query="SELECT
CategorienPerHoofdcategorie.Hoofdcategorie,
CategorienPerHoofdcategorie.Beleggingscategorie,
Beleggingscategorien.Afdrukvolgorde,
Beleggingscategorien.Omschrijving,
hoofdCat.Omschrijving as HcatOmschrijving,
hoofdCat.Afdrukvolgorde as HcatVolgorde
FROM
CategorienPerHoofdcategorie
Inner Join Beleggingscategorien ON CategorienPerHoofdcategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Inner Join Beleggingscategorien as hoofdCat ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdCat.Beleggingscategorie
WHERE CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY HcatVolgorde,Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
      $this->categorien[$categorien['Beleggingscategorie']]=$categorien['Omschrijving'];
      $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['Beleggingscategorie']]=array();
      $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['Beleggingscategorie']]=array();
		}

		$query = "SELECT TijdelijkeRapportage.portefeuille,
		    TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving,
			TijdelijkeRapportage.beleggingscategorie as beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel, TijdelijkeRapportage.Hoofdcategorie,
		   	TijdelijkeRapportage.HoofdcategorieOmschrijving ".
			" FROM TijdelijkeRapportage
			WHERE (TijdelijkeRapportage.portefeuille = '".$this->portefeuille."') AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$datum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY beleggingscategorie ".
			" ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde asc, TijdelijkeRapportage.valutaVolgorde";
		debugSpecial($query,__FILE__,__LINE__);

		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
		  $this->hoofdcategorien[$categorien['Hoofdcategorie']]=$categorien['HoofdcategorieOmschrijving'];
		  $this->categorien[$categorien['beleggingscategorie']]=$categorien['Omschrijving'];
      
      if($categorien['beleggingscategorie']=='Liquiditeiten')
        $categorien['Hoofdcategorie']='Liquiditeiten';
		  if ($categorien['beleggingscategorie']=='')
		    $categorien['beleggingscategorie']='geenCat';
		  if ($categorien['Hoofdcategorie']=='')
		    $categorien['Hoofdcategorie']='geenHCat';

		  if($categorien['beleggingscategorie']=='Rente')
		  {
		    $categorien['Omschrijving']='Opgelopen Rente';
		    $categorien['beleggingscategorie']='Opgelopen Rente';
		  }
		  //$catogorieData
    //$catogorieData[$categorien['beleggingscategorie']]['port']['waarde']+=$categorien['subtotaalactueel'];
    //$hoofdCatogorieData[$categorien['Hoofdcategorie']]['port']['waarde']+=$categorien['subtotaalactueel'];

    $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['waarde']+=$categorien['subtotaalactueel'];
    $this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['precentage']=$this->tabelData[$datum][$categorien['Hoofdcategorie']][$categorien['beleggingscategorie']]['waarde']/$portTotaal;


    $this->tabelDataHcat[$datum][$categorien['Hoofdcategorie']]['waarde']+=$categorien['subtotaalactueel'];
   
		}
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

  function SetLegends2($data, $format)
  {
      $this->pdf->legends=array();
      $this->pdf->wLegend=0;

      $this->pdf->sum=array_sum($data);

      $this->pdf->NbVal=count($data);
      foreach($data as $l=>$val)
      {
          //$p=sprintf('%.1f',$val/$this->sum*100).'%';
          if($val <> 0)
          {
            $p=sprintf('%.1f',$val).'%';
            $legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
          }
          else
            $legend='';
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
      }
  }

	function writeRapport()
	{
	  global $__appvar;
    $this->getOIBdata($this->rapportageDatum);
    //$this->getOIBdata($this->rapportageDatumVanaf);



	  $this->pdf->addPage();
 		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+14);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetWidths(array(15,250));
    $this->pdf->SetY(75);

    $DB = new DB();
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];

		$query = "SELECT Beleggingscategorie,Omschrijving as value FROM Beleggingscategorien";
    $DB->SQL($query);
    $DB->Query();
	  while($data=$DB->nextRecord())
      $omschrijving[$data['Beleggingscategorie']]=$data['value'];
    $omschrijving['geenCat']='Geen categorie';
    $omschrijving['geen-Hcat']='Geen categorie';
    $omschrijving['totaal']='Totaal';
    $omschrijving['geenHCat']='Geen categorie';


    $this->pdf->SetWidths(array(60,20,20));

 	  $grafiekData=array();
 	  $grafiekKleurData=array();

 	  $grafiekData=array();
 	  $grafiekKleurData=array();
	  foreach ($this->tabelDataHcat[$this->rapportageDatum] as $hcategorie=>$catData)
	  {

  	    $grafiekData[$omschrijving[$hcategorie]]=round($catData['waarde']/$this->portTotaal[$this->rapportageDatum]*100,1);
        $grafiekDataWaarde[$omschrijving[$hcategorie]]=round($catData['waarde'],2);
	      $grafiekKleurData[$omschrijving[$hcategorie]]=array($kleuren[$hcategorie]['R']['value'],$kleuren[$hcategorie]['G']['value'],$kleuren[$hcategorie]['B']['value']);

	  }
    
	  if(round($this->portTotaal[$this->rapportageDatum])!=0)
	  {
	    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
      $this->pdf->setXY(25,45-5);
    
      $barGraph=false;
      foreach($grafiekData as $cat=>$waarde)
      {
        if($waarde<0)
          $barGraph=true;
      }
      if($barGraph==false)
      {
        $this->pdf->Cell(50,4,"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2),0,0,'C');
	      $this->pdf->setXY(25,45);
	      $this->PieChart(40, 40, $grafiekData, $grafiekDataWaarde,$grafiekKleurData);
      }
      else
      {
        $this->pdf->setXY(35,45);
        $this->BarDiagram(60, 140, $grafiekData, '%l (%p)',$grafiekKleurData,"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2));
	    }
    }


 

   // $hcatDataAlles=$att->bereken($startdatumMeerjarenrendement,$this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
    
  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->pdf->PortefeuilleStartdatum,$this->rapportageDatum ,$this->portefeuille);

  $perfIndexCum=1;
  $aantalOverslaan=3;
  foreach($indexData as $i=>$data)
  {
    $datum=$data['datum'];
    $juldate=db2jul($datum);

    if(date('d-m',$juldate)=='31-01')
      $dag=1;
    else
      $dag=0;
    $startDatum=date("Y-m-d",mktime(0,0,0,date('m',$juldate),$dag,date("Y",$juldate)));
    if($this->pdf->portefeuilledata['SpecifiekeIndex'] <> '')
    {
      $perfIndex=$this->fondsPerf($this->pdf->portefeuilledata['SpecifiekeIndex'],$startDatum,$datum);
      $perfIndexCum= ($perfIndexCum  * (1+$perfIndex)) ;
      $data['specifiekeIndex']=($perfIndexCum-1)*100;
    
      if($i<$aantalOverslaan)
        $data['specifiekeIndex']='';
      if($i==$aantalOverslaan)
      {
        $data['specifiekeIndex']=$data['index']-100;
        $perfIndexCum=1+($data['specifiekeIndex']/100);
      }
      $lijngrafiekData['specifiekeIndex'][]=$data['specifiekeIndex'];
    }
    $lijngrafiekData['portefeuille'][]=$data['index']-100;
    
    $maand=date("M",$juldate);
    if($maand=="Jan")
      $jaar=date("Y",$juldate);
    else
      $jaar='';
    $lijngrafiekData['datum'][]= $maand;
    $lijngrafiekData['jaren'][]= $jaar;

  }



	  $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
    $this->pdf->setXY(120,40);
	  $this->pdf->MultiCell(165,4,"Lange termijn resultaat", 0, "C");
	   $this->pdf->SetTextColor(0,0,0);

    $this->pdf->setXY(120,43);
    $this->LineDiagram(165, 50, $lijngrafiekData,array(array(0,128,0),array(0,0,205)),0,0,6,5,1);//50

    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
    
  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

    
      if(count($indexData) > 0)
   {
    		$this->pdf->widthA = array(26,25,30,30,23,23,23,24,28,24,26);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(200,240,255);

        $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);
         $this->pdf->SetY(120);
         $this->pdf->Ln();

            $this->pdf->row(array(vertaalTekst("Maand",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Begin-\nvermogen",$this->pdf->rapport_taal),
		                      vertaalTekst("Stortingen en \nonttrekkingen",$this->pdf->rapport_taal),
		                      vertaalTekst("Koersresultaat",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Inkomsten",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Kosten",$this->pdf->rapport_taal)."\n ",
		                      vertaalTekst("Opgelopen-\nrente",$this->pdf->rapport_taal),
		                      vertaalTekst("Beleggings\nresultaat",$this->pdf->rapport_taal),
		                     	vertaalTekst("Eind-\nvermogen",$this->pdf->rapport_taal),
		                      vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n(".vertaalTekst("maand",$this->pdf->rapport_taal).")",
		                      vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n(".vertaalTekst("cumulatief",$this->pdf->rapport_taal).")"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);                      
    $sumWidth = array_sum($this->pdf->widthA);
	  $this->pdf->Line($this->pdf->marge+$this->pdf->widthB[0],$this->pdf->GetY(),$this->pdf->marge+$sumWidth,$this->pdf->GetY());
    
        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($indexData as $row)
		    {

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
        //  listarray($row);
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
        $i++;
		    }
		    $this->pdf->fillCell=array();


            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);
        
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
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
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

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
          $legend=str_replace(array('%l','%v','%p'),array(vertaalTekst($l,$this->pdf->rapport_taal),$val,$p),$format);
          $this->pdf->legends[]=$legend;
          $this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->pdf->wLegend);
      }
  }
  
  
  
  function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
  {

      $this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
      $this->SetLegends2($data,$format);


      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $nbDiv=5;
      $legendWidth=10;
      $YDiag = $YPage;
      $hDiag = floor($h);
      $XDiag = $XPage +  $legendWidth;
      $lDiag = floor($w - $legendWidth);
      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0) {
          $maxVal = max($data)*1.1;
      }
      if ($minVal == 0) {
          $minVal = min($data)*1.1;
      }
      if($minVal > 0)
        $minVal=0;
      $maxVal=ceil($maxVal/10)*10;  

      $offset=$minVal;
      $valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100; 
      $bandBreedte = $valIndRepere * $nbDiv;
      $lRepere = floor($lDiag / $nbDiv);
      $unit = $lDiag / $bandBreedte;
      $hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
      $hDiag = $hBar * ($this->pdf->NbVal + 1);
      
      //echo "$hBar <br>\n";
      $eBaton = floor($hBar * 80 / 100);
      $legendaStep=$unit;

      $legendaStep=$unit/$nbDiv*$bandBreedte;
      //if($bandBreedte/$legendaStep > $nbDiv)
      //  $legendaStep=$legendaStep*5;
     // if($bandBreedte/$legendaStep > $nbDiv)
      //  $legendaStep=$legendaStep*2;
     // if($bandBreedte/$legendaStep > $nbDiv)
     //   $legendaStep=$legendaStep/2*5;
      $valIndRepere=round($valIndRepere/$unit/5)*5;


      $this->pdf->SetLineWidth(0.2);
      $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $nullijn=$XDiag - ($offset * $unit);
    
      $i=0;
      $nbDiv=10;
      
      $this->pdf->SetFont($this->pdf->rapport_font, '', 5);
      if(round($legendaStep,5) <> 0.0)
      {
        //for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
        for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          $i++;
          if($i>100)
            break;
        }

        $i=0;
        //for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
        for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
        {
          $this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
          $this->pdf->setXY($x,$YDiag + $hDiag);
          $this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,2),0,0,'C');
          
          $i++;
          if($i>100)
            break;
        }
      }
     
      $i=0;

      $this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
      $this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
      

      foreach($data as $key=>$val)
      {
          $this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
          $xval = $nullijn;
          $lval = ($val * $unit);
          $yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
          $hval = $eBaton;
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
          $this->pdf->SetXY($XPage, $yval);
          $this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
          $i++;
      }

      //Scales
      $minPos=($minVal * $unit);
      $maxPos=($maxVal * $unit);

      $unit=($maxPos-$minPos)/$nbDiv;
     // echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";


  }
  
function PieChart($w, $h, $data, $dataWaarden, $colors=null,$hcat)
  {

      $this->pdf->sum=array_sum($data);
      $this->pdf->NbVal=count($data);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
     // $this->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 4;
      $hLegend = 2;
      $radius = min($w - $margin * 4 - $hLegend - $this->pdf->wLegend, $h - $margin * 2);
      $radius=min($w,$h);

      $radius = floor($radius / 2);
      $XDiag = $XPage + $margin + $radius;
      $YDiag = $YPage + $margin + $radius;


      //Sectors
      $this->pdf->SetLineWidth(0.2);
      $angleStart = 0;
      $angleEnd = 0;
      $i = 0;
      $aantal=count($data);
      foreach($data as $key=>$val)
      {
        $angle = floor(($val * 360) / doubleval($this->pdf->sum));

        if ($angle != 0)
        {
          $angleEnd = $angleStart + $angle;

          $avgAngle=($angleStart+$angleEnd)/360*M_PI;
          $factor=1.5;

          if($i==($aantal-1))
            $angleEnd=360;

        //  echo " $angle $angleStart + $angleEnd = ".(($angleStart+$angleEnd)/2)." ".$this->pdf->legends[$i]." | cos:".cos($avgAngle)." | sin:".sin($avgAngle)."  <br>\n";
              $this->pdf->SetFillColor($colors[$key][0],$colors[$key][1],$colors[$key][2]);
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

      $x1 = $XPage -10  ;
      $x2 = $x1 +  $margin;
      $y1 = $YDiag + ($radius) + $margin+5;

$this->pdf->SetXY($this->pdf->GetX(),$y1-5);

      //for($i=0; $i<$this->pdf->NbVal; $i++)
      foreach($data as $key=>$value)
      {
          //$this->pdf->SetXY($x2-30,$y1);
          $this->pdf->SetX($x2-$radius-10);
          $this->pdf->SetFillColor($colors[$key][0],$colors[$key][1],$colors[$key][2]);
          $this->pdf->Rect($x1, $y1+$extraY, $hLegend, $hLegend, 'DF');
          $this->pdf->SetXY($x2,$y1+$extraY);
          $this->pdf->Cell(60,$hLegend,$key.' ('.$value.'%)');
          $this->pdf->Cell(20,$hLegend,'€ '.$this->formatGetal($dataWaarden[$key],2),0,0,'R');
          $y1+=$hLegend + 2;
          $lastHcat=$hcat[$i];
      }
      $this->pdf->SetFillColor(0,0,0);

  }


function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;

    $legendDatum= $data['datum'];
    $legendJaar= $data['jaren'];
    $data1 = $data['specifiekeIndex'];
    $data = $data['portefeuille'];
    $legendaItems= $data['legenda'];

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

    $this->pdf->Rect($XDiag, $YDiag, $w-$margin, $h,'FD','',array(245,245,245));

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(155,155,155);
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
    
    if($jaar && count($data) < 12)
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
      if((0-$n*$stapgrootte) > $minVal)
      {  
        $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      }
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
    {
      if($n*$stapgrootte < $maxVal)
      {  
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+0 ." %");
      }
      $n++;
      if($n >20)
         break;
    }


    $aantal=count($legendDatum);
    $div=1;
    if($aantal>24)
    {
      $div=ceil($aantal/24);
      $div=4;
    }

    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
    for ($i=0; $i<count($data); $i++)
    {
      $this->pdf->line($XDiag+($i+1)*$unit, $YDiag+$hDiag+2, $XDiag+($i+1)*$unit, $YDiag+$hDiag+3,array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,0,0)) );
      if($i%$div==0)
        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],25);
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+12,$legendJaar[$i],0);
      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
      if ($i>0)
        $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color);
      if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
      $yval = $yval2;
    }


    if(is_array($data1) && count($data1)>0)
    {
      $yval=$YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color1);

      for ($i=0; $i<count($data1); $i++)
      {
        
        $yval2 = $YDiag + (($maxVal-$data1[$i]) * $waardeCorrectie) ;
        
        if($i>2)
        {
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
          $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$color1);
        if ($i==count($data1)-1)
          $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color1);
        }
        
         $yval = $yval2;

      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));


  //   $XPage
   // $YPage
    if(count($data1)>0)
      $legendaItems=array('portefeuille','benchmark');
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
    $this->pdf->Rect($XPage+$step, $YPage+$h+14, 3, 3, 'DF','',$kleur);
    $this->pdf->SetXY($XPage+3+$step,$YPage+$h+14);
    $this->pdf->Cell(0,3,$item);
    $step+=($w/2);
    }
    $this->pdf->SetDrawColor(0,0,0);
    $this->pdf->SetFillColor(0,0,0);
  }

  function fondsPerf($fonds,$van,$tot)
  {
    $DB=new DB();
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
  	$DB->SQL($query);
    $startKoers=$DB->lookupRecord();

    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($query);
    $eindKoers=$DB->lookupRecord();
    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
    return $perf;
  }






}
?>