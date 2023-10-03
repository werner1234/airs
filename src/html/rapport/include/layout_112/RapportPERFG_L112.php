<?php
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");

class RapportPERFG_L112
{
	function RapportPERFG_L112($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFG";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Resultaat lopend jaar";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->tweedeStart();
    
    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');
    
	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
    $this->pdf->excelData[]=array(vertaalTekst("Jaar",$this->pdf->rapport_taal)."",
      vertaalTekst("Begin-vermogen",$this->pdf->rapport_taal),
      vertaalTekst("Stortingen en onttrekkingen",$this->pdf->rapport_taal),
      vertaalTekst("Koersresultaat",$this->pdf->rapport_taal)."",
      vertaalTekst("Inkomsten",$this->pdf->rapport_taal)."",
      vertaalTekst("Kosten",$this->pdf->rapport_taal)."",
      vertaalTekst("Opgelopen rente",$this->pdf->rapport_taal),
      vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),
      vertaalTekst("Eindvermogen",$this->pdf->rapport_taal),
      vertaalTekst("Rendement",$this->pdf->rapport_taal)." (".vertaalTekst("jaar",$this->pdf->rapport_taal).")",
      vertaalTekst("Rendement",$this->pdf->rapport_taal)." (".vertaalTekst("Cumulatief",$this->pdf->rapport_taal).")");
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
    
    
    $q="SELECT Omschrijving FROM Fondsen WHERE Fonds = '".mysql_real_escape_string($this->pdf->portefeuilledata['SpecifiekeIndex'])."'";
    $DB = new DB();
    $DB->SQL($q);
    $DB->Query();
    $this->benchmarkOmschrijving = $DB->LookupRecord();
    
    


//listarray($this->categorieVolgorde);
		// voor data
		$this->pdf->widthA = array(1,95,25,5,25,5,25,5,25,5,25,5,25,5,25,5);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R');


  	$this->pdf->widthB = array(1,95,30,10,30,115);
		$this->pdf->alignB = array('L','L','R','R','R');
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];


  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
 
 

//  $indexData = $this->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
//listarray($indexData);
//exit;

    $laatsteDatum='leeg';
foreach ($indexData as $i=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
    $grafiekData['Datum'][] = $data['datum'];
    $grafiekData['Index'][] = $data['index']-100;
    $grafiekData['benchmarkIndex'][] = $data['specifiekeIndex']-100;
    $barGraph['Index'][$data['datum']]['leeg']=0;
    foreach ($data['extra']['cat'] as $categorie=>$waarde)
    {
      if($categorie=='LIQ'||$categorie=='H-Liq')
        $categorie='Liquiditeiten';

      $barGraph['Index'][$data['datum']][$categorie] += $waarde/$data['waardeHuidige']*100;
      if($waarde <> 0)
        $categorien[$categorie]=$categorie;
    }
  }

  $huidigeJaarGrafiek[$data['datum']]['performance']=$data['performance'];
  $huidigeJaarGrafiek[$data['datum']]['performanceCumu']=((1+$huidigeJaarGrafiek[$laatsteDatum]['performanceCumu']/100) * (1+$data['performance']/100)-1) * 100;
  $laatsteDatum=$data['datum'];
}
    $stdev=getFondsPerformanceGestappeld2($this->pdf->portefeuilledata['SpecifiekeIndex'],$this->portefeuille,$this->rapportageDatumVanaf , $this->rapportageDatum,'maanden',false,true,true);
    $laatsteDatum='leeg';
    foreach($stdev->reeksen['benchmark'] as $datum=>$rendementDetails)
    {
      $huidigeJaarGrafiek[$datum]['benchmark'] = $rendementDetails['perf'];
      $huidigeJaarGrafiek[$datum]['benchmarkCumu'] = ((1 + $huidigeJaarGrafiek[$laatsteDatum]['benchmarkCumu'] / 100) * (1 + $rendementDetails['perf'] / 100) - 1) * 100;
      $laatsteDatum=$datum;
    }
    
    $indexDataJaren = $index->getWaarden($this->pdf->portefeuilledata['Startdatum'] ,$this->rapportageDatum ,$this->portefeuille,'','jaar');
    foreach ($indexDataJaren as $i=>$data)
    {
      $jaarGrafiek[$data['datum']]['performance']=$data['performance'];
      $jaarGrafiek[$data['datum']]['performanceCumu']=((1+$jaarGrafiek[$laatsteDatum]['performanceCumu']/100) * (1+$data['performance']/100)-1) * 100;
      $laatsteDatum=$data['datum'];
    }
    $stdev=getFondsPerformanceGestappeld2($this->pdf->portefeuilledata['SpecifiekeIndex'],$this->portefeuille,$this->pdf->portefeuilledata['Startdatum'] , $this->rapportageDatum,'jaar',false,true,true);
    $laatsteDatum='leeg';
    foreach($stdev->reeksen['benchmark'] as $datum=>$rendementDetails)
    {
      $jaarGrafiek[$datum]['benchmark'] = $rendementDetails['perf'];
      $jaarGrafiek[$datum]['benchmarkCumu'] = ((1 + $jaarGrafiek[$laatsteDatum]['benchmarkCumu'] / 100) * (1 + $rendementDetails['perf'] / 100) - 1) * 100;
      $laatsteDatum=$datum;
    }

		$q="SELECT Beleggingscategorie,BeleggingscategorieOmschrijving as Omschrijving,beleggingscategorieVolgorde FROM TijdelijkeRapportage WHERE Portefeuille='".$this->portefeuille."' AND Beleggingscategorie <>'' GROUP BY Beleggingscategorie  ORDER BY beleggingscategorieVolgorde asc"; //WHERE Beleggingscategorie IN('LIQ','ZAK','VAR','Liquiditeiten')
		$DB->SQL($q);
		$DB->Query();
		while($data=$DB->nextRecord())
		{
		  $this->categorieVolgorde[$data['Beleggingscategorie']]=$data['Beleggingscategorie'];
		  $this->categorieOmschrijving[$data['Beleggingscategorie']]=vertaalTekst($data['Omschrijving'],$this->pdf->rapport_taal);
		}
    $this->categorieVolgorde['Liquiditeiten']='Liquiditeiten';
    $this->categorieOmschrijving['Liquiditeiten']=vertaalTekst('Liquiditeiten',$this->pdf->rapport_taal);

$grafiekData['Datum'][]="$RapStartJaar-12-01";
    $i=0;
   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       $this->pdf->SetFillColor(230,230,230);
        //$this->pdf->SetFillColor(200,240,255);

       // $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);


        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
		      //listarray($row);
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
          
          $this->pdf->CellBorders = array();
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],0),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],0),
		                           $this->formatGetal($row['opbrengsten'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['rente'],0),
		                           $this->formatGetal($row['resultaatVerslagperiode'],0),
		                           $this->formatGetal($row['waardeHuidige'],0),
		                           $this->formatGetal($row['performance'],2),
		                           $this->formatGetal($row['index']-100,2)));
          $this->pdf->excelData[]=array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           round($row['waardeBegin'],0),
		                           round($row['stortingen']-$row['onttrekkingen'],0),
		                           round($row['gerealiseerd']+$row['ongerealiseerd'],0),
		                           round($row['opbrengsten'],0),
		                           round($row['kosten'],0),
		                           round($row['rente'],0),
		                           round($row['resultaatVerslagperiode'],0),
		                           round($row['waardeHuidige'],0),
		                           round($row['performance'],2),
		                           round($row['index']-100,2));
                               

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
		                           $this->formatGetal($waardeBegin,0),
		                           $this->formatGetal($totaalStortingenOntrekkingen,0),
		                           $this->formatGetal($totaalGerealiseerd+$totaalOngerealiseerd,0),
		                           $this->formatGetal($totaalOpbrengsten,0),
		                           $this->formatGetal($totaalKosten,0),
		                           $this->formatGetal($totaalRente,0),
		                           $this->formatGetal($totaalResultaat,0),
		                           $this->formatGetal($totaalWaarde,0),
		                           '',
		                           $this->formatGetal($totaalRendament-100,2)
		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
  
     $this->pdf->excelData[]=array(vertaalTekst('Totaal',$this->pdf->rapport_taal),
       round($waardeBegin,0),
       round($totaalStortingenOntrekkingen,0),
       round($totaalGerealiseerd+$totaalOngerealiseerd,0),
       round($totaalOpbrengsten,0),
       round($totaalKosten,0),
       round($totaalRente,0),
       round($totaalResultaat,0),
       round($totaalWaarde,0),
       '',
       round($totaalRendament-100,2)
     );
		    $this->pdf->CellBorders = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }

    if(count($jaarGrafiek)>1)
    {
      $meerjaren=true;
    }
    else
    {
      $meerjaren=false;
    }
    
		  if (count($barGraph) > 0)
		  {
		    $this->pdf->SetXY($this->pdf->marge,99)		;//112
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $this->pdf->Cell(138, 5, vertaalTekst('Rendement lopend jaar',$this->pdf->rapport_taal), 0, 1);

 		   // $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+130,$this->pdf->GetY());
	      $this->pdf->SetXY(15,185)		;//112
        if($meerjaren==true)
        {
          $this->VBarDiagram2(120, 30, $jaarGrafiek, true,true);
        }
        else
        {
          $this->VBarDiagram(80, 30, $barGraph['Index']);
        }
      
        $this->pdf->SetXY($this->pdf->marge,148)		;//112
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        if($meerjaren==true)
          $this->pdf->Cell(100, 5, vertaalTekst('Historisch rendement',$this->pdf->rapport_taal), 0, 1);
        else
          $this->pdf->Cell(100, 5, vertaalTekst('Vermogensverdeling',$this->pdf->rapport_taal), 0, 1);
        $this->pdf->Line($this->pdf->marge, $this->pdf->GetY(),$this->pdf->marge+130,$this->pdf->GetY());
        
        $this->pdf->SetXY(15,135)		;//112
        $this->VBarDiagram2(120,30,$huidigeJaarGrafiek,true);
		  }
    
    $this->pdf->SetY(104);//112
		  $this->indexVergelijking();
		  //$this->pdf->ln();
    $this->pdf->SetY(148);//112
		  $this->toonZorgplicht();
		  
	   $this->pdf->fillCell = array();


		if($this->extraVulling)
		{
	   // verwijderTijdelijkeTabel($this->portefeuille,"$RapStartJaar-01-01");
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
  
  function toonZorgplicht()
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
  
    $this->totaalWaarde=$totaalWaarde;
    
    if(round($this->totaalWaarde,2) <> 0)
    {
      $query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / " . $this->totaalWaarde . " as percentage,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
WHERE TijdelijkeRapportage.Portefeuille =  '" . $this->portefeuille . "' AND
 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $categorieWaarden[$data['Zorgplicht']] = $data['percentage'] * 100;
        $categorieOmschrijving[$data['Zorgplicht']] = $data['beleggingscategorieOmschrijving'];
      }
    }
    $zorgplicht = new Zorgplichtcontrole();
    $zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
    
    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;
    
    krsort($tmp);

//listarray($zpwaarde['conclusie']);
    //listarray($tmp);exit;
    
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $beginY=$this->pdf->getY();
    
    $this->pdf->SetWidths(array(135,55,20,20,20,20));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','beleggingscategorie','minimaal','maximaal',"werkelijk","conclusie"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    //foreach ($tmp as $index=>$regelData)
  
    //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen='';
      //if($zpwaarde['categorien'][$cat]['Minimum'])
      $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      //else
      //   $min='';
      //if($zpwaarde['categorien'][$cat]['Maximum'])
      $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
      // else
      //   $max='';
      
      if($tmp[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,$this->pdf->widths[0]+133,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,$this->pdf->widths[0]+133,$this->pdf->getY(),3.9,3.9);
      
      
      $this->pdf->row(array('',$categorieOmschrijving[$cat],$min,$max,$this->formatGetal($categorieWaarden[$cat],1)."%"));//$risicogewogen
    }
    // $this->pdf->Rect($this->pdf->marge,$beginY,120,count($categorieWaarden)*4+4);
  }
  
  function indexVergelijking()
  {
    $DB=new DB();
    
    
    $perioden=array('begin'=>$this->rapportageDatumVanaf,'eind'=>$this->rapportageDatum);
  
    $query="SELECT Portefeuilles.specifiekeIndex as Beursindex,
Fondsen.Omschrijving,
Fondsen.Valuta
FROM Portefeuilles
Inner Join Fondsen ON Portefeuilles.specifiekeIndex = Fondsen.Fonds
WHERE Portefeuilles.Portefeuille = '$this->portefeuille'";
    $DB->SQL($query);
    $DB->Query();
    $specifiekeIndex = $DB->nextRecord();
    
    $query="SELECT
benchmarkverdeling.benchmark,
benchmarkverdeling.fonds as Beursindex,
benchmarkverdeling.percentage,
Fondsen.Omschrijving,
Fondsen.Valuta
FROM
benchmarkverdeling
Inner Join Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
WHERE benchmarkverdeling.benchmark='".mysql_real_escape_string($specifiekeIndex['Beursindex'])."'";

    $DB->SQL($query);
    $DB->Query();
    $indices=array();
    while($index = $DB->nextRecord())
      $indices[]=$index;
    
    $query="SELECT max(benchmarkverdelingVanaf.vanaf) as datum FROM
benchmarkverdelingVanaf WHERE vanaf<='".$this->rapportageDatum."' AND
benchmarkverdelingVanaf.benchmark ='".mysql_real_escape_string($specifiekeIndex['Beursindex'])."'";
    $DB->SQL($query);
    $datum=$DB->lookupRecord();
    $query="SELECT
	benchmarkverdelingVanaf.benchmark,
	benchmarkverdelingVanaf.fonds AS Beursindex,
	benchmarkverdelingVanaf.percentage,
	Fondsen.Omschrijving,
	Fondsen.Valuta
FROM
	benchmarkverdelingVanaf
INNER JOIN Fondsen ON benchmarkverdelingVanaf.fonds = Fondsen.Fonds
WHERE
	benchmarkverdelingVanaf.benchmark ='".mysql_real_escape_string($specifiekeIndex['Beursindex'])."' AND benchmarkverdelingVanaf.vanaf='".$datum['datum']."'";
    $DB->SQL($query);
    $DB->Query();
    $indices=array();
    while($index = $DB->nextRecord())
      $indices[]=$index;

      $indices[]=array();
      $indices[]=$specifiekeIndex;
    
    
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
    
    
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetWidths(array(135,60,20,20,20,25));
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->Rect($this->pdf->marge+135,$this->pdf->getY(),145,count($benchmarks)*4+4);
    $this->pdf->row(array("","Vergelijkingsmaatstaven","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement","Rendement EUR"));
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
      $this->pdf->row(array("","Overige marktindices ter informatie","".date("d-m-Y",db2jul($perioden['begin'])),"".date("d-m-Y",db2jul($perioden['eind'])),"Rendement"));
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
	$datum = array();

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

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['OBL'] += $regel['actuelePortefeuilleWaardeEuro'];
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
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;


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
    $lDiag = floor($w - $w/12 );

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(116,95,71);
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

      foreach ($data as $datum=>$waarden)
      {
        $legenda[$datum] = jul2form(db2jul($datum));
        $n=0;
        $minVal=0;
        $maxVal=100;
        foreach (array_reverse($this->categorieVolgorde) as $categorie)
        {
        //foreach ($waarden as $categorie=>$waarde)
        //{
          if($categorie=='LIQ')
            $categorie='Liquiditeiten';
          $grafiek[$datum][$categorie]=$waarden[$categorie];
          $grafiekCategorie[$categorie][$datum]=$waarden[$categorie];
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;

          $maxVal=max(array($maxVal,$waarden[$categorie]));
          $minVal=min(array($minVal,$waarden[$categorie]));

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
      $numBars=10;

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
      $bGrafiek = ($w - $margin * 1) - ($w/12)*2; // - legenda

      $n=0;

    if(count($grafiekCategorie)>10)
      $regelhoogte=5;
    else
      $regelhoogte=7;

      foreach (($this->categorieVolgorde) as $categorie)//array_reverse
      {
        if(is_array($grafiekCategorie[$categorie]))
        {
          $this->pdf->Rect($XstartGrafiek+$w+3 , $YstartGrafiek-$hGrafiek+$n*$regelhoogte+2, 2, 2, 'DF',null,$colors[$categorie]);
          $this->pdf->SetXY($XstartGrafiek+$w+6 ,$YstartGrafiek-$hGrafiek+$n*$regelhoogte+1.5 );
          $this->pdf->MultiCell(65, 4,$this->categorieOmschrijving[$categorie],0,'L');
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

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 50 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;

   foreach ($grafiek as $datum=>$data)
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
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            if($eBaton>5)
              $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

         if($legendaPrinted[$datum] != 1)
           $this->pdf->TextWithRotation($xval-4,$YstartGrafiek+8,$legenda[$datum],25);

         if($grafiekPunt[$categorie][$datum])
         {
            $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(194,179,157));
            if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek);
            $lastX = $xval+.5*$eBaton;
            $lastY = $grafiekPunt[$categorie][$datum] * $unit + $YstartGrafiek;
         }
         $legendaPrinted[$datum] = 1;
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
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);

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


  function VBarDiagram2($w, $h, $data,$metLijn=false,$jaren=false)
  {
    global $__appvar;

    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
    $YstartGrafiek = $YPage - floor($margin * 1);
    $hGrafiek = ($h - $margin * 1);
    $XstartGrafiek = $XPage + $margin * 1 ;
    $bGrafiek = ($w - $margin * 1);

    //$this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
    $color=array(155,155,155);

    $maxVal=0;
    $minVal=0;
    $maanden=array();
    $aantalStaven=0;
    foreach($data as $maand=>$maandData)
    {
      if($aantalStaven==0)
        $aantalStaven=count($maandData);
      $maanden[$maand]=$maand;
      foreach($maandData as $type=>$waarde)
      {
        if($waarde > $maxVal)
          $maxVal = $waarde;
        if($waarde < $minVal)
          $minVal = $waarde;
      }
    }
    if($metLijn==true)
      $aantalStaven=$aantalStaven/2;
    if($maxVal > 1)
      $maxVal=ceil($maxVal);
    if($minVal < -1)
      $minVal=floor($minVal);
    $minVal = $minVal * 1.1;
    $maxVal = $maxVal * 1.1;
    if ($maxVal <0)
      $maxVal=0;

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

    $horDiv = 10;
    $horInterval = $hGrafiek / $horDiv;
    $bereik = $hGrafiek/$unit;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $this->pdf->SetTextColor(0,0,0);

    $stapgrootte = ceil(abs($bereik)/$horDiv*10)/10;
    $top = $YstartGrafiek-$h;
    $bodem = $YstartGrafiek;
    $absUnit =abs($unit);

    $nulpunt = $YstartGrafiek + $nulYpos;
    $n=0;

    for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XstartGrafiek-7, $i, -1*$n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }

    $n=0;
    for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
    {
      $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      if($skipNull == true)
        $skipNull = false;
      else
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
      $n++;
      if($n >20)
        break;
    }

    $numBars=count($data);
    if($numBars > 0)
      $this->pdf->NbVal=$numBars;


    $this->pdf->SetFillColor();

    $colors=array('performance'=>array($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']),
                  'benchmark'=>array(75,119,197));//,'totaalEffect'=>array(0, 52, 121)); //



    $vBar = ($bGrafiek / ($this->pdf->NbVal))/($aantalStaven+1); //4
    $bGrafiek = $vBar * ($this->pdf->NbVal);
    $eBaton = ($vBar * 80 / 100);
    $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
    $this->pdf->SetLineWidth(0.2);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
    $i=0;

    $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $lastXY=array();
    foreach($data as $periode=>$maandData)
    {

      foreach($maandData as $type=>$val)
      {
        if($metLijn==true)
        {
          //listarray($type);
          if(substr($type,-4)=='Cumu')
          {
            continue;
          }

        }

        $color=$colors[$type];
        $legenda[$type]=$color;
        //Bar
        $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
        $lval = $eBaton;
        $yval = $YstartGrafiek + $nulYpos;
        $hval = ($val * $unit);
        $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
        /*
        $this->pdf->SetTextColor(255,255,255);
        if(abs($hval) > 3 && $eBaton > 4)
        {
          $this->pdf->SetXY($xval, $yval+($hval/2)-2);
          $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
        }
        $this->pdf->SetTextColor(0,0,0);
        */
        $i++;
      }
      $i++;
      if(strlen($periode)==4)
        $xLegenda=$periode;
      else
      {
        if($jaren==true)
          $xLegenda = date('Y', db2jul($periode));
        else
          $xLegenda = date('M', db2jul($periode));
      }
      $this->pdf->Text($XstartGrafiek + ($i-1) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,$xLegenda);

    }

    $xPos=$XstartGrafiek;
    foreach($data as $periode=>$maandData)
    {
      foreach($maandData as $type=>$val)
      {
        if($metLijn==true)
        {
          //listarray($type);
          if(substr($type,-4)=='Cumu')
          {
            //echo "$type $periode $val <br>\n";

            $color=$colors[substr($type,0,-4)];
            if(!isset($lastXY[$type]))
              $lastXY[$type]=array($XstartGrafiek,$YstartGrafiek + $nulYpos);

            $newXY=array($xPos + $vBar ,$YstartGrafiek + $nulYpos+($val * $unit));

            if($type=='benchmarkCumu')
              $this->pdf->Line($lastXY[$type][0], $lastXY[$type][1] , $newXY[0],$newXY[1] ,array('dash' => "1,2",'color'=>array(0,0,0)));
            else
              $this->pdf->Line($lastXY[$type][0], $lastXY[$type][1] , $newXY[0],$newXY[1] ,array('dash' => 0,'color'=>array(0,0,0)));
            $this->pdf->setDash(0);
            $this->pdf->setDrawColor(0);
            $this->pdf->Rect($newXY[0]-0.5, $newXY[1]-0.5 , 1, 1, 'DF',null,array(0,0,0));

            $lastXY[$type]=array( $newXY[0],$newXY[1]);
            $xPos+=$vBar;
          }
        }
      }
      $xPos+=$vBar;
    }

    $n=0;
    $omschrijvingen=array('performance'=>'Portefeuille','benchmark'=>'Benchmark: '.$this->benchmarkOmschrijving['Omschrijving']);
    $this->pdf->setDash(0);
    $y=5;
    foreach($legenda as $type=>$kleur)
    {
      if($type=='benchmark')
      {
        $this->pdf->Line($XstartGrafiek+6, $YstartGrafiek+$y+($n*4)+1 , $XstartGrafiek+8, $YstartGrafiek+$y+($n*4)+1,array('dash' => "1,2",'color'=>array(0,0,0)));
        $this->pdf->setDash(0);
      }
      else
      {
        $this->pdf->Line($XstartGrafiek+6, $YstartGrafiek+$y+($n*4)+1 , $XstartGrafiek+8, $YstartGrafiek+$y+($n*4)+1,array('dash' => 0,'color'=>array(0,0,0)));
      }


      $this->pdf->Rect($XstartGrafiek+10, $YstartGrafiek+$y+($n*4), 2, 2, 'DF',null,$kleur);

      $this->pdf->Text($XstartGrafiek+14, $YstartGrafiek+$y+($n*4)+1.5, vertaalTekst($omschrijvingen[$type],$this->pdf->rapport_taal));

      $n++;
    }



    // $color=array(155,155,155);
    // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }
}
?>