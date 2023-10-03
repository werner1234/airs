<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/05/05 14:56:40 $
File Versie					: $Revision: 1.21 $

$Log: RapportATT_L51.php,v $
Revision 1.21  2019/05/05 14:56:40  rvv
*** empty log message ***

Revision 1.20  2019/05/04 18:22:48  rvv
*** empty log message ***

Revision 1.19  2019/02/23 18:32:59  rvv
*** empty log message ***

Revision 1.18  2019/02/13 14:50:15  rvv
*** empty log message ***

Revision 1.17  2018/10/17 07:48:11  rvv
*** empty log message ***

Revision 1.16  2018/04/07 15:21:44  rvv
*** empty log message ***

Revision 1.15  2017/03/31 15:39:22  rvv
*** empty log message ***

Revision 1.14  2015/12/30 19:01:23  rvv
*** empty log message ***

Revision 1.13  2015/12/20 16:46:36  rvv
*** empty log message ***

Revision 1.12  2015/11/13 16:31:37  rvv
*** empty log message ***

Revision 1.11  2015/09/13 11:32:51  rvv
*** empty log message ***

Revision 1.10  2015/05/20 16:04:40  rvv
*** empty log message ***

Revision 1.9  2015/02/04 16:08:06  rvv
*** empty log message ***

Revision 1.8  2014/09/06 15:24:17  rvv
*** empty log message ***

Revision 1.7  2014/06/14 16:40:37  rvv
*** empty log message ***

Revision 1.6  2014/05/10 13:54:39  rvv
*** empty log message ***

Revision 1.5  2014/05/05 15:52:25  rvv
*** empty log message ***

Revision 1.4  2014/05/04 10:55:50  rvv
*** empty log message ***

Revision 1.3  2014/04/26 16:43:08  rvv
*** empty log message ***

Revision 1.2  2014/04/02 15:53:15  rvv
*** empty log message ***

Revision 1.1  2013/11/13 15:47:34  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");


class RapportATT_L51
{
	function RapportATT_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Beleggingsresultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();
	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     //$this->tweedePerformanceStart =$rapportageDatumVanaf;
     $this->rapportageDatumVanaf =$rapportageDatumVanaf;
     //echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     //exit;
	 }

	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) >= db2jul($this->rapportageDatumVanaf))
	  {
	    $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
	  }
	  else
	  {
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
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
    
    $indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
    if(substr($this->rapportageDatumVanaf,0,4)<>substr($this->rapportageDatum,0,4))
    {
      $kwartalen=true;
      $this->pdf->rapport_titel = "Beleggingsresultaat verslagperiode";
    }
    else
    {
      $kwartalen=false;
    }
    $this->pdf->attKwartalen=$kwartalen;
    
		$this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['ATTPaginas']=$this->pdf->rapport_titel;
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];



//  $index=new indexHerberekening();
//  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
//listarray($indexData);

///listarray($indexData);
//exit;

$aantal=count($indexData);
$buffer=array();
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $maand=substr($data['datum'],5,5);
    $grafiekTonen=true;
    if($kwartalen==true)
    {
      $grafiekTonen=false;
      if(!isset($buffer['waardeBegin']))
        $buffer['waardeBegin']= $data['waardeBegin'];
      $buffer['stortingen']+= $data['stortingen'];
      $buffer['onttrekkingen']+= $data['onttrekkingen'];
      $buffer['gerealiseerd']+= $data['gerealiseerd'];
      $buffer['ongerealiseerd']+= $data['ongerealiseerd'];
      $buffer['opbrengsten']+= $data['opbrengsten'];
      $buffer['kosten']+= $data['kosten'];
      $buffer['rente']+= $data['rente'];
      $buffer['resultaatVerslagperiode']+= $data['resultaatVerslagperiode'];
      $buffer['waardeHuidige']= $data['waardeHuidige'];
      $buffer['datum'] = $data['datum'];
      $buffer['index'] = $data['index'];
      $buffer['specifiekeIndex'] = $data['specifiekeIndex'];
      $buffer['performance'] = ((1+$buffer['performance']/100)*(1+$data['performance']/100)-1)*100;

      if($maand=='03-31'||$maand=='06-30'||$maand=='09-30'||$maand=='12-31'||$index==($aantal-1))
      {
        $grafiekData['Datum'][] = $buffer['datum'];
        $grafiekData['Index'][] = $buffer['index']-100;
        $grafiekData['benchmarkIndex'][] = $buffer['specifiekeIndex']-100;
        $rendamentWaarden[]=$buffer;
        $buffer=array();
        $grafiekTonen=true;
      }
    }
    else
    {
    
  
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
    }
  //  foreach ($data['categorieVerdeling'] as $categorie=>$waarde)
  
    if($grafiekTonen==true)
    {
      $barGraph['Index'][$data['datum']]['leeg'] = 0;
      foreach ($data['extra']['cat'] as $categorie => $waarde)
      {
        if ($categorie == 'LIQ')
        {
          $categorie = 'Liquiditeiten';
        }
    
        $barGraph['Index'][$data['datum']][$categorie] += $waarde / $data['waardeHuidige'] * 100;
        if ($waarde <> 0)
        {
          $categorien[$categorie] = $categorie;
        }
      }
    }
  }
}

		$q="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving as Omschrijving,beleggingscategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND Beleggingscategorie <>'' GROUP BY Beleggingscategorie  ORDER BY beleggingscategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
  	$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  //$this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}



for($i=count($barGraph['Index']); $i<12;$i++)
{
  $barGraph['Index']['vulling'.$i]=array();
}

    $zorgplicht = new Zorgplichtcontrole();
  	$zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;


     $query="SELECT
	Beleggingscategorien.Omschrijving as beleggingscategorieOmschrijving,
	KeuzePerVermogensbeheerder.waarde as beleggingscategorie,
	ZorgplichtPerBeleggingscategorie.Zorgplicht,
	ZorgplichtPerRisicoklasse.Norm
FROM
KeuzePerVermogensbeheerder
JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde=Beleggingscategorien.Beleggingscategorie
LEFT JOIN ZorgplichtPerBeleggingscategorie ON Beleggingscategorien.Beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie
AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT JOIN ZorgplichtPerRisicoklasse ON ZorgplichtPerBeleggingscategorie.Zorgplicht = ZorgplichtPerRisicoklasse.Zorgplicht
AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = ZorgplichtPerRisicoklasse.Vermogensbeheerder
WHERE
KeuzePerVermogensbeheerder.categorie='Beleggingscategorien' AND KeuzePerVermogensbeheerder.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
GROUP BY
	ZorgplichtPerBeleggingscategorie.Zorgplicht
ORDER BY
	KeuzePerVermogensbeheerder.Afdrukvolgorde ";
    $DB->SQL($query);
    $DB->Query();
    $tmp=array();
		while($data=$DB->nextRecord())
		{
	//	$tmp[$data['beleggingscategorie']]=$data['norm'];
     $tmp[$data['beleggingscategorie']]=$zpwaarde['categorien'][$data['Zorgplicht']]['Norm'];
    }

$barGraph['Index']['Beleggingsplan']=$tmp;
   
   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       //$this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(200,240,255);
        //$brightness=1.55;
        //echo $this->pdf->rapport_grijs[0]*$brightness.",".$this->pdf->rapport_grijs[1]*$brightness.",".$this->pdf->rapport_grijs[2]*$brightness."<br>\n";
        //$this->pdf->SetFillColor(240);


        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
		      //listarray($row);
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);
/*
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
          */
          if($kwartalen==true)
          {
            $periode=date("Y",$datum).' Q'.ceil(date("n",$datum)/3);
          }
          else
          {
            $periode=date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal);
          }
          $this->pdf->CellBorders = array();
		      $this->pdf->row(array($periode ,
		                           $this->formatGetal($row['waardeBegin'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
		                           $this->formatGetal($row['gerealiseerd'],2),
		                           $this->formatGetal($row['ongerealiseerd'],2),
		                           $this->formatGetal($row['opbrengsten'],2),
		                           $this->formatGetal($row['kosten'],2),
		                           $this->formatGetal($row['rente'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['waardeHuidige'],2),
		                           $this->formatGetal($row['performance'],2),
		                           $this->formatGetal($row['index'],2)));
                               
                               

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


            
            $this->pdf->CellBorders = array('','TS','TS','TS','TS','TS','TS','TS','TS','TS','','TS'); 
            $this->pdf->row(array('','','','','','','','','','','','')); 
            $this->pdf->SetY($this->pdf->GetY()-4);


        $this->pdf->ln(3);
   
        //$this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->CellBorders = array();
		    $this->pdf->row(array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
		                           $this->formatGetal($waardeBegin,2),
		                           $this->formatGetal($totaalStortingenOntrekkingen,2),
		                           $this->formatGetal($totaalGerealiseerd,2),
		                           $this->formatGetal($totaalOngerealiseerd,2),
		                           $this->formatGetal($totaalOpbrengsten,2),
		                           $this->formatGetal($totaalKosten,2),
		                           $this->formatGetal($totaalRente,2),
		                           $this->formatGetal($totaalResultaat,2),
		                           $this->formatGetal($totaalWaarde,2),
		                           '',
		                           $this->formatGetal($totaalRendament,2)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }


		  if (count($barGraph) > 0)
		  {
		    $this->pdf->SetXY($this->pdf->marge,102)		;//112
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		    	$this->pdf->Cell(0, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
  		    $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
		      $this->pdf->SetXY(15,180)		;//112
		      $this->VBarDiagram(220, 70, $barGraph['Index']);
		  }
/*
		  if (count($grafiekData) > 1)
		  {
        $this->pdf->SetXY(8,109+37);//104
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
  		  $this->pdf->Cell(0, 5, 'Rendement (cumulatief in %)', 0, 1);
  		  $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+277,$this->pdf->GetY());
  		  $this->pdf->SetXY(15,117+35)		;//112
        $valX = $this->pdf->GetX();
        $valY = $this->pdf->GetY();
        //function LineDiagram($w, $h, $data, $color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
        $this->LineDiagram(220, 30, $grafiekData,$this->pdf->rapport_grafiek_color,0,0,6,5,1);//50
        $this->pdf->SetXY($valX, $valY + 80);
		  }
      */
		  $this->pdf->SetXY(8, 155);//165

    if(round($totaalResultaat) != round($resultaatVerslagperiode['Totaal']))
    {
    // echo "<script  type=\"text/JavaScript\">alert('Beleggingsresultaat totaal (".(round($resultaatVerslagperiode['Totaal'],2)).") komt niet overeen met perioden (".round($totaalResultaat,2).") verschil (".(round($resultaatVerslagperiode['Totaal'],2)-round($totaalResultaat,2)).") voor portefeuille ".$this->portefeuille."'); </script>";
    //  ob_flush();
    }

		$this->pdf->ln(10);
		$this->pdf->SetX(108);


	  $this->pdf->MultiCell(170,4,$titel,0,'L');
	  $this->pdf->SetX(108);


	   $this->pdf->fillCell = array();


		if($this->extraVulling)
		{
	   // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
		}

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
  
  include_once("rapport/include/ATTberekening_L68.php");
  
  $att=new ATTberekening_L68($this);
  
  if ($this->pdf->lastPOST['doorkijk'] == 1)
    $hpiGebruik=true;
  else
    $hpiGebruik=false;
  
  $verdeling='Beleggingscategorien';//attributie
  $waarden['rapportagePeriode'] = $att->bereken($this->rapportageDatumVanaf, $this->rapportageDatum, $verdeling,$hpiGebruik);

  $realData=array();
  $categorieen=array();
  foreach($waarden['rapportagePeriode'] as $cat=> $catData)
  {
    if($catData['beginwaarde']<>0 || $catData['eindwaarde'] <> 0 || $catData['stort']<>0 || $catData['procent'] <> 0)
    {
      $realData[$cat] = $catData;
      if($cat<>'totaal')
        $categorieen[$cat]=$cat;
    }
  }
  $newRows=array();
  //$lastGerealiseerd=0;
  $lastIndex=0;
  foreach($realData['totaal']['perfWaarden'] as $maand=>$maandData)
  {
    $tmp=array();
    
  
    $tmp['waardeBegin']=$maandData['beginwaarde'];
    $tmp['waardeHuidige']=$maandData['eindwaarde'];
    $tmp['waardeMutatie']=$maandData['eindwaarde']-$maandData['beginwaarde'];
    $tmp['stortingen']=$maandData['storting'];
    $tmp['onttrekkingen']=$maandData['onttrekking'];
    $tmp['resultaatVerslagperiode']=$maandData['resultaat'];
    $tmp['kosten']=$maandData['kosten'];
    $tmp['opbrengsten']=$maandData['opbrengst'];
    $tmp['performance']=$maandData['procent']*100;
    $tmp['ongerealiseerd']=$maandData['ongerealiseerd'];
    $tmp['rente']=$maandData['rente'];
    $tmp['gerealiseerd']=$maandData['gerealiseerd'];
    //$lastGerealiseerd=((1+($tmp['gerealiseerd']/100)*(1+$lastGerealiseerd/100)-1)*100);
    //$tmp['gerealiseerdYtd']=$lastGerealiseerd;
    foreach($categorieen as $cat)
      $tmp['extra']['cat'][$cat]=$realData[$cat]['perfWaarden'][$maand]['eindwaarde'];
    $tmp['datum']=$maand;
    $tmp['index']=((1+$maandData['procent'])*(1+$lastIndex/100)-1)*100;
 //   echo $tmp['index']."=(".(1+$maandData['procent']).""*"".(1+$lastIndex/100)."-1)*100) <br>\n";
    $lastIndex=$tmp['index'];

    $newRows[]=$tmp;
  }
  //listarray($realData);
  /*
	 $julBegin = db2jul($datumBegin);
   $julEind = db2jul($datumEind);
   $datum=$this->getMaanden($julBegin,$julEind);


  $i=0;
  $indexData=array();
	foreach ($datum as $periode)
	{
	 	//$indexData = array_merge($indexData,);
    $indexData =$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille,'EUR',$laatsteWaarde);
	 	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
 	  if(empty($laatsteWaarde['index']))
	    $laatsteWaarde['index']=100;
	  $indexData['index'] = ($laatsteWaarde['index']  * (100+$indexData['performance'])/100);
        
	  $data[$i] = $indexData;
    $laatsteWaarde=$indexData;
    
    $i++;
	}
  */
 // listarray($data); listarray($newRows); exit;
  
  
  $db=new DB();
  $query="SELECT
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.Afdrukvolgorde
FROM
KeuzePerVermogensbeheerder
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='Beleggingscategorien'
ORDER BY Afdrukvolgorde desc";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
    $this->categorieVolgorde[$data['waarde']]=0;
  $this->categorieVolgorde['LIQ']=0;
  $query="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille
FROM
Beleggingscategorien
Inner Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
Inner Join Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Afdrukvolgorde desc";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
    $this->categorieVolgorde[$data['Beleggingscategorie']]=0;
  
  
	return $newRows;
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
    $lDiag = floor($w - $w/12 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(116,95,71);
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
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
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
      $extrax=($unit*0.1*-1);
      if($i <> 0)
        $extrax1=($unit*0.1*-1);
        
        
      $this->pdf->TextWithRotation($XDiag+($i)*$unit-10+$unit,$YDiag+$hDiag+8,jul2form(db2jul($legendDatum[$i])),25);

      $yval2 = $YDiag + (($maxVal-$data[$i]) * $waardeCorrectie) ;
      $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
      $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
      
      if($data[$i] <> 0)
        $this->pdf->Text($XDiag+($i+1)*$unit-1+$extrax,$yval2-2.5,$this->formatGetal($data[$i],1));
     
      
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
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        
        $this->pdf->Text($XDiag+($i+1)*$unit-0.5,$yval2-4.5,$data1[$i]);
         
        $yval = $yval2;
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }


  function VBarDiagram($w, $h, $data)
  {
      global $__appvar;
      $legendaWidth = 00;
      $grafiekPunt = array();
      $verwijder=array();

      $minVal=0;
      $maxVal=100;
      foreach ($data as $datum=>$waarden)
      {
        if(substr($datum,4,1)=='-')
          $legenda[$datum] = jul2form(db2jul($datum));
        elseif($datum=='Beleggingsplan')
          $legenda[$datum] = vertaalTekst('Beleggingsplan',$this->pdf->rapport_taal);
        else
          $legenda[$datum] = '';  
          
        $n=0;
       
        foreach($waarden as $key=>$value)
        {
          if($value <0)
            $minTotal[$datum]+=$value;
          else
            $maxTotal[$datum]+=$value;  
        }
        
       
        foreach ($this->categorieVolgorde as $categorie=>$waarde)
        {
        //foreach ($waarden as $categorie=>$waarde)
        //{
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarden[$categorie];
          $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          if($maxTotal[$datum] > 100)
            $maxVal=max(array($maxVal,$maxTotal[$datum]));
          if($minTotal[$datum] < 0)
            $minVal=min(array($minVal,$minTotal[$datum]));

          if($waarden[$categorie] < 0)
          {
             unset($grafiek[$datum][$categorie]);
             $grafiekNegatief[$datum][$categorie]=$waarden[$categorie];
          }
          else
             $grafiekNegatief[$datum][$categorie]=0;


          if(!isset($colors[$categorie]))
            $colors[$categorie]=array($this->categorieKleuren[$categorie]['R']['value'],$this->categorieKleuren[$categorie]['G']['value'],$this->categorieKleuren[$categorie]['B']['value']);
          $n++;
        }
      }


      $numBars = count($legenda);
      $numBars=11;

      if($color == null)
      {
        $color=array(155,155,155);
      }

    
      if(round($maxVal,0) <= 100)
        $maxVal=100;
      elseif($maxVal < 112.5)
        $maxVal=112.5;
      elseif($maxVal < 125)
        $maxVal=125;
      elseif($maxVal < 150)
        $maxVal=150;
        
      if(round($minVal) >= 0)
        $minVal = 0;
      elseif($minVal > -12.5)
        $minVal=-12.5;
      elseif($minVal > -25)
        $minVal=-25;
      elseif($minVal > -50)
        $minVal=-50;
        
      //echo "$maxVal $minVal ";exit;
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1)-45 ; // - legenda

      $n=0;
      foreach (array_reverse($this->categorieVolgorde) as $categorie=>$waarde)//
      {
        if(isset($this->categorieOmschrijving[$categorie]) && is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*7+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*7+1.5 );
          $this->pdf->MultiCell(45, 4,$this->categorieOmschrijving[$categorie],0,'L');
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

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
        $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte*-1)." %",0,0,'R');
        $n++;
        if($n >20)
         break;
      }

      $n=0;
      for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
      {
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        if($skipNull == true)
          $skipNull = false;
        else
        {
          $this->pdf->SetXY($XstartGrafiek-12, $i-1.5);
          $this->pdf->Cell(10, 3, $this->formatGetal($n*$stapgrootte)." %",0,0,'R');
        }
        $n++;
        if($n >20)
          break;
      }



    if($numBars > 0)
      $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal));
        $bGrafiek = $vBar * ($this->pdf->NbVal);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
    $this->pdf->SetTextColor(0,0,0);
   foreach ($grafiek as $datum=>$data)
   {
      //echo $datum.' '. count($data)."<br>\n";
     // listarray($data);
      $aantal=count($data);
      $n=1;
      foreach($data as $categorie=>$val)
      {
        if(!isset($YstartGrafiekLast[$datum]))
          $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'F',null,$colors[$categorie]);
          $this->pdf->Line($xval,$yval,$xval,$yval+$hval);
          $this->pdf->Line($xval+$lval,$yval,$xval+$lval,$yval+$hval);
          if($aantal==$n)
            $this->pdf->Line($xval,$yval+$hval,$xval+$lval,$yval+$hval);
          
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;

          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-1.25,$YstartGrafiek+4,$legenda[$datum],0);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
         $n++;
      }
      $i++;
   }

   $i=0;
   $YstartGrafiekLast=array();
   foreach ($grafiekNegatief as $datum=>$data)
   {
      foreach($data as $categorie=>$val)
      {
          if(!isset($YstartGrafiekLast[$datum]))
            $YstartGrafiekLast[$datum] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + (1 + $i ) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$datum] + $nulYpos ;
          $hval = ($val * $unit);

          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$categorie]);
          $YstartGrafiekLast[$datum] = $YstartGrafiekLast[$datum]+$hval;

          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }


         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
      }
      $i++;
   }
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  }
  
  
function getMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$i,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}


	function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR',$vorigeWaarde=array())
	{
	  if(substr($beginDatum,5,5)=='12-31')
	   $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';

	  if ($valuta != "EUR" )
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$totaalWaarde =array();
		$db = new DB();

		$query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->SQL($query);
		$startDatum=$db->lookupRecord();


		$query="SELECT
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.Afdrukvolgorde
FROM
KeuzePerVermogensbeheerder
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='Beleggingscategorien'
ORDER BY Afdrukvolgorde desc";
  		$db->SQL($query);
			$db->Query();
			while($data=$db->nextRecord())
				  $this->categorieVolgorde[$data['waarde']]=0;
      $this->categorieVolgorde['LIQ']=0;   
		$query="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille
FROM
Beleggingscategorien
Inner Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
Inner Join Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='$portefeuille'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Afdrukvolgorde desc";
  		$db->SQL($query);
			$db->Query();
			while($data=$db->nextRecord())
				  $this->categorieVolgorde[$data['Beleggingscategorie']]=0;


    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);
		if(db2jul($beginDatum) == mktime (0,0,0,1,1,$startjaar))
		 $beginjaar = true;
		else
		 $beginjaar = false;


		//$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
    $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$this->tweedePerformanceStart ,$eindDatum,$valuta,true);
		//echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,$beginjaar,$valuta,$beginDatum);

		if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
		  $valutaKoers=1;
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
   	if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
		  $valutaKoers=1;

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }


	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  if($gemiddelde<0)
    $gemiddelde=$gemiddelde*-1;
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;

//echo "perf $eindDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
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

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
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
    $valutaResultaat=$resultaatVerslagperiode-(($koersResultaat-$vorigeWaarde['gerealiseerdYtd'])+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    foreach ($categorieVerdeling as $cat=>$waarde)
      $categorieVerdeling[$cat]=$waarde."";

    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat-$vorigeWaarde['gerealiseerdYtd'];
    $data['gerealiseerdYtd'] =$koersResultaat;
    $data['extra']['cat']=$categorieVerdeling;
    return $data;

	}


}
?>