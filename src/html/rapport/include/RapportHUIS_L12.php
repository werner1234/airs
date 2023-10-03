<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/15 16:13:11 $
File Versie					: $Revision: 1.15 $

$Log: RapportHUIS_L12.php,v $
Revision 1.15  2020/04/15 16:13:11  rvv
*** empty log message ***

Revision 1.14  2020/04/01 16:54:10  rvv
*** empty log message ***

Revision 1.13  2020/03/29 16:37:07  rvv
*** empty log message ***

Revision 1.12  2020/03/29 08:07:03  rvv
*** empty log message ***

Revision 1.11  2020/03/28 15:45:39  rvv
*** empty log message ***

Revision 1.10  2020/03/25 16:43:07  rvv
*** empty log message ***

Revision 1.9  2020/03/07 14:41:15  rvv
*** empty log message ***

Revision 1.8  2020/03/04 16:40:47  rvv
*** empty log message ***

Revision 1.7  2020/02/29 16:24:09  rvv
*** empty log message ***

Revision 1.6  2020/02/28 05:25:02  rvv
*** empty log message ***

Revision 1.5  2020/02/26 16:12:54  rvv
*** empty log message ***

Revision 1.4  2020/02/08 10:33:21  rvv
*** empty log message ***

Revision 1.3  2020/01/29 17:36:42  rvv
*** empty log message ***

Revision 1.2  2019/12/11 11:17:44  rvv
*** empty log message ***

Revision 1.1  2019/11/09 16:39:21  rvv
*** empty log message ***

Revision 1.5  2019/07/06 15:43:47  rvv
*** empty log message ***

Revision 1.4  2019/06/22 16:31:44  rvv
*** empty log message ***

Revision 1.3  2019/06/12 15:23:21  rvv
*** empty log message ***

Revision 1.2  2019/05/08 15:11:07  rvv
*** empty log message ***

Revision 1.1  2019/03/23 17:05:54  rvv
*** empty log message ***

Revision 1.5  2019/02/20 16:51:10  rvv
*** empty log message ***

Revision 1.9  2019/02/16 19:37:13  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportHUIS_L12
{
	function RapportHUIS_L12($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "HUIS";
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "";
    //$this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
   // $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');


    $this->perioden=array();
    $this->perioden['start']= $rapportageDatumVanaf;
   $this->perioden['eind']= $this->rapportageDatum;

    
    
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    if((db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf)) || (db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01")))
    {
      $this->tweedePerformanceStart = substr($this->pdf->PortefeuilleStartdatum,0,10);
    }
    else
    {
      $this->tweedePerformanceStart = "$RapStartJaar-01-01";
    }
    $this->perioden['jan']=$this->tweedePerformanceStart;
    $this->pdf->tweedePerformanceStart= db2jul($this->tweedePerformanceStart );

		$this->portefeuille = $portefeuille;

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


  
	function writeRapport()
	{
		
	
		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[	$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    
  $this->printBenchmarkvergelijking();
//  exit;

	}


  function printBenchmarkvergelijking()
  {
    global $__appvar;
    $DB = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];

    $zorgplichtcategorien=array();
    $query="SELECT waarde as Zorgplicht ,
Zorgplichtcategorien.Omschrijving as zorgOmschrijving
FROM KeuzePerVermogensbeheerder
 JOIN Zorgplichtcategorien ON KeuzePerVermogensbeheerder.waarde=Zorgplichtcategorien.Zorgplicht AND Zorgplichtcategorien.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE KeuzePerVermogensbeheerder.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='Zorgplichtcategorien' ORDER BY Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
    {
      $zorgplichtcategorien[$data['Zorgplicht']] = $data;
      $verdeling[$data['Zorgplicht']]= $data;
    }
  


    $query = "SELECT 
              SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal,
	ZorgplichtPerBeleggingscategorie.Zorgplicht,
TijdelijkeRapportage.beleggingscategorie ,
	beleggingscategorieOmschrijving ".
      "FROM TijdelijkeRapportage
             LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
             LEFT JOIN Zorgplichtcategorien ON ZorgplichtPerBeleggingscategorie.Zorgplicht=Zorgplichtcategorien.Zorgplicht AND Zorgplichtcategorien.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
             WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek']."
              GROUP BY 
              TijdelijkeRapportage.beleggingscategorie,
	            Zorgplicht
              ORDER BY beleggingscategorieVolgorde";
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);//echo $query;exit;
    $DB->Query();
 
    while($data=$DB->nextRecord())
    {
      if(!isset($zorgplichtcategorien[$data['Zorgplicht']]))
        $zorgplichtcategorien[$data['Zorgplicht']]=$data;
      $verdeling[$data['Zorgplicht']]['percentage'] +=$data['totaal']/$totaalWaarde*100;
      $verdeling[$data['Zorgplicht']]['waarde'] += $data['totaal'];
      $verdeling[$data['Zorgplicht']]['beleggingscategorien'][]=$data['beleggingscategorieOmschrijving'];
      $verdeling[$data['Zorgplicht']]['zorgOmschrijving']=$zorgplichtcategorien[$data['Zorgplicht']]['zorgOmschrijving'];
    }

    $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."' ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    $zorgplichtPerPortefeuille=false;
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtPerPortefeuille=true;
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }

    if($zorgplichtPerPortefeuille==false && count($this->pdf->portefeuilles)>1)
    {
      foreach($this->pdf->portefeuilles as $port)
      {
        $pWaarde=0;
        $precs=berekenPortefeuilleWaarde($port,$this->rapportageDatum, (substr($this->rapportageDatum,5,5)=='01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$this->rapportageDatum);
        foreach($precs as $rec)
        {
          $pWaarde+=($rec['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind);
        }
        $pAandeel=$pWaarde/$totaalWaarde;

        $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$port."' ORDER BY Zorgplicht";
        $DB->SQL($query);
        $DB->Query();
        while($zorgplicht = $DB->nextRecord())
        {
          $zorgplichtcategorien[$zorgplicht['Zorgplicht']]['norm']+=$zorgplicht['norm']*$pAandeel;
        }
      }
    }


    $query="SELECT vanaf FROM ZorgplichtPerPortefeuille WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' AND vanaf < '".$this->perioden['eind']."' ORDER BY vanaf desc limit 1";
    $DB->SQL($query);// echo $query;exit;
    $DB->Query();
    $datum=$DB->nextRecord();
    if($datum['vanaf'] <> '')
      $vanafWhere="AND vanaf='".$datum['vanaf'] ."'";
    else
      $vanafWhere='';

    $query="SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Vermogensbeheerder,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum,
ZorgplichtPerPortefeuille.norm
FROM
ZorgplichtPerPortefeuille
WHERE ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' $vanafWhere
 ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
    }

    $zorgplcihtConversie=array('RISD'=>'RISD','Zakelijke waarden'=>'H-Aand','Alternatieven'=>'H-AltBel','Vastrentende waarden'=>'H-Oblig','Liquiditeiten'=>'H-Liq');
    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      $query="SELECT IndexPerBeleggingscategorie.Fonds,Fondsen.Omschrijving FROM IndexPerBeleggingscategorie
      JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
      WHERE Categoriesoort='Beleggingscategorien' AND Categorie='".$zorgplcihtConversie[$zorgplicht]."' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      AND (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."') ORDER BY IndexPerBeleggingscategorie.Portefeuille desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      $data = $DB->nextRecord();

      $zorgplichtcategorien[$zorgplicht]['fonds']=$data['Fonds'];
      $zorgplichtcategorien[$zorgplicht]['fondsOmschrijving']=$data['Omschrijving'];
    }

    //$index=new indexHerberekening();
    //$perioden=$index->getMaanden(db2jul($beginDatum),db2jul($eindDatum));

    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {


      $query="SELECT vanaf FROM benchmarkverdelingVanaf WHERE benchmark='".$zorgplichtData['fonds']."' AND vanaf < '".$this->perioden['eind']."' ORDER BY vanaf desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      if($DB->records()>0)
      {
        $datum = $DB->nextRecord();
        $query = "SELECT benchmarkverdelingVanaf.fonds,benchmarkverdelingVanaf.percentage,Fondsen.Omschrijving FROM benchmarkverdelingVanaf
        JOIN Fondsen ON benchmarkverdelingVanaf.fonds = Fondsen.Fonds
        WHERE benchmark='".$zorgplichtData['fonds']."' AND vanaf = '" . $datum['vanaf'] . "'";

      }
      else
      {
        $query = "SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving
        FROM benchmarkverdeling
        JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
        WHERE benchmark='" . $zorgplichtData['fonds'] . "'";
      }
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $zorgplichtcategorien[$zorgplicht]['fondsSamenselling'][$data['fonds']] = $data;
      }
    }



    //$this->pdf->SetWidths(array(50,20,20,30,25,25,40,20,20));
  
    $this->pdf->SetWidths(array($this->pdf->blokken[0]/2,$this->pdf->blokken[0]/4,$this->pdf->blokken[0]/4,
                            $this->pdf->blokken[1]/3,$this->pdf->blokken[1]/3,$this->pdf->blokken[1]/3,
                            $this->pdf->blokken[2]/2,$this->pdf->blokken[2]/4,$this->pdf->blokken[2]/4));
    //$xVinkPlaatje=50+20+20+30+25+25+75;
    //listarray($this->pdf->blokken);exit;

    $this->pdf->ln();
    $this->pdf->ln();

    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
    $this->pdf->row(array('',vertaalTekst("Waarde",$this->pdf->rapport_taal),vertaalTekst("Huidig",$this->pdf->rapport_taal),
                      vertaalTekst("Strategisch",$this->pdf->rapport_taal),vertaalTekst('Minimum',$this->pdf->rapport_taal),vertaalTekst("Maximum" ,$this->pdf->rapport_taal)));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
    $totaal=array();
    $voldoetTotaal='';
    $lnhoogte=3;
    //listarray($verdeling);
    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgData)
    {
      if(!isset($verdeling[$zorgplichtCategorie]['percentage']))
        $verdeling[$zorgplichtCategorie]['percentage']=0;
  
      if($zorgData['Minimum']<>0 || $zorgData['Maximum']<>0)
      {
        if ($verdeling[$zorgplichtCategorie]['percentage'] <= $zorgData['Maximum'] && $verdeling[$zorgplichtCategorie]['percentage'] >= $zorgData['Minimum'])
        {
          //$this->pdf->MemImage($this->checkImg, $xVinkPlaatje, $this->pdf->getY(), 3.5, 3.5);
          $voldoet = 'Ja';
          if ($voldoetTotaal == '')
          {
            $voldoetTotaal = $voldoet;
          }
        }
        else
        {
          //$this->pdf->MemImage($this->deleteImg, $xVinkPlaatje, $this->pdf->getY(), 3.5, 3.5);
          $voldoet = 'Nee';
          $voldoetTotaal = $voldoet;
        }
      }
     
      if($verdeling[$zorgplichtCategorie]['zorgOmschrijving']=='')
         $verdeling[$zorgplichtCategorie]['zorgOmschrijving']='leeg';

      //$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
     

      if($zorgplichtCategorie=='RISM' || $zorgplichtCategorie=='RISD')
      {
        unset( $this->pdf->CellFontStyle);
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        $totaal['waarde']+=$verdeling[$zorgplichtCategorie]['waarde'];
        $totaal['percentage']+=$verdeling[$zorgplichtCategorie]['percentage'];
        $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
        $this->pdf->CellBorders=array('',array('T','U'),array('T','U'),array('T','U'),array('T','U'),array('T','U'));
        $this->pdf->row(array(vertaalTekst($verdeling[$zorgplichtCategorie]['zorgOmschrijving'], $this->pdf->rapport_taal),
                          $this->formatGetal($verdeling[$zorgplichtCategorie]['waarde'], 0),
                          $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'], 1) . '%',
                          $this->formatGetal($zorgData['norm'], 1) . '%',
                          $this->formatGetal($zorgData['Minimum'], 1) . '%',
                          $this->formatGetal($zorgData['Maximum'], 1) . '%',
                          vertaalTekst($voldoet, $this->pdf->rapport_taal)));
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        unset($this->pdf->CellBorders);
      }
      else
      {
        $this->pdf->CellFontStyle=array(array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
        $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
        //$this->pdf->row(array(vertaalTekst('        '.$verdeling[$zorgplichtCategorie]['zorgOmschrijving'],$this->pdf->rapport_taal),'','','','','',vertaalTekst($voldoet,$this->pdf->rapport_taal)));
       
        if($zorgData['Minimum']<>0 || $zorgData['Maximum']<>0)
          $this->pdf->row(array(vertaalTekst('        '.$verdeling[$zorgplichtCategorie]['zorgOmschrijving'], $this->pdf->rapport_taal),
                          $this->formatGetal($verdeling[$zorgplichtCategorie]['waarde'], 0),
                          $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'], 1) . '%',
                          $this->formatGetal($zorgData['norm'], 1) . '%',
                          $this->formatGetal($zorgData['Minimum'], 1) . '%',
                          $this->formatGetal($zorgData['Maximum'], 1) . '%',
                          vertaalTekst($voldoet, $this->pdf->rapport_taal)));
        
      }
      $this->pdf->ln($lnhoogte);
      unset( $this->pdf->CellFontStyle);

    
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->SetTextColor($this->pdf->rapport_default_fontcolor['r'],$this->pdf->rapport_default_fontcolor['g'],$this->pdf->rapport_default_fontcolor['b']);
      /*
      foreach($verdeling[$zorgplichtCategorie]['beleggingscategorien'] as $categorie)
      {

        $this->pdf->row(array(vertaalTekst('        '.$categorie,$this->pdf->rapport_taal),'','','','','',vertaalTekst($voldoet,$this->pdf->rapport_taal)));
        $this->pdf->ln($lnhoogte);
      }
      */

    }
    $this->pdf->ln(2);
   // $this->pdf->CellFontStyle=array(array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
    unset($this->pdf->CellFontStyle);
    $this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
    $this->pdf->setDrawColor($this->pdf->rapport_totaalLijnenColor[0],$this->pdf->rapport_totaalLijnenColor[1],$this->pdf->rapport_totaalLijnenColor[2]);
    $this->pdf->CellBorders=array('','T','T');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("Totaal",$this->pdf->rapport_taal),
                      $this->formatGetal($totaal['waarde'],0),
                      $this->formatGetal($totaal['percentage'],1).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->setDrawColor(0);
    unset($this->pdf->CellBorders);
    unset($this->pdf->CellFontStyle);
  
    $crmData=array();
    if($this->pdf->checkRappNaam==true)
    {
      $query="SELECT GeschiktheidVoldoet,GeschiktheidVoldoetNiet  FROM CRM_naw WHERE portefeuille='".$this->portefeuille."'";
      $DB->SQL($query);
      $crmData=$DB->lookupRecord();
    }
  
    $txt='';
    if($voldoetTotaal=='Ja')
    {
      if($crmData['GeschiktheidVoldoet']<>'')
        $txt=$crmData['GeschiktheidVoldoet'];
      else
        $txt='Op de verslagdatum was uw portefeuille samengesteld in overeenstemming met onze vastlegging van uw beleggingsdoelstelling, beleggingshorizon, kennis- en ervaringsniveau, risicobereidheid en verliescapaciteit. De gerapporteerde percentages van de verschillende beleggingscategorieën (aandelen, alternatieven, vastrentende waarden en liquiditeiten) van uw portefeuille vallen binnen de met u overeengekomen bandbreedtes. In het kwartaalbericht zijn de belangrijkste beleggingsbeslissingen toegelicht. Daarin is ook vermeld of er gedurende het kwartaal wijzigingen zijn aangebracht in de door ons optimaal geachte asset allocatie.';
//      $txt='U kunt hierboven lezen hoe de huidige verdeling van de beleggingen over de verschillende beleggingscategorieën (aandelen, alternatieven, vastrentende waarden en liquiditeiten) zich verhoudt tot de gekozen lange-termijn strategische allocatie. Deze verdeling bevindt zich binnen de met u afgesproken bandbreedtes.';
    }
    elseif($voldoetTotaal=='Nee')
    {
      if($crmData['GeschiktheidVoldoetNiet']<>'')
        $txt=$crmData['GeschiktheidVoldoetNiet'];
      else
        $txt='';//Naar ons oordeel is uw beheerportefeuille opgebouwd uit financiële instrumenten die geschikt zijn voor uw portefeuille en passen bij uw financiële situatie, doelstellingen en risicohouding. Het risico-/rendementsprofiel van de portefeuille is in lijn met de afspraken die wij daarover met u hebben gemaakt.';
    }
    
    
    $this->pdf->ln(8);
    $this->pdf->MultiCell(270,$this->pdf->rowHeight+1,$txt,0);
  
    //$this->pdf->ln(8);
    //$this->pdf->MultiCell(270,$this->pdf->rowHeight+1,$txt,0);
    // listarray($zorgplichtcategorien);


  }


}
?>