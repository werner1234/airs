<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/02/13 14:50:15 $
File Versie					: $Revision: 1.37 $

$Log: RapportPERF_L51.php,v $
Revision 1.37  2019/02/13 14:50:15  rvv
*** empty log message ***

Revision 1.36  2019/02/03 13:43:54  rvv
*** empty log message ***

Revision 1.35  2018/12/03 06:50:50  rvv
*** empty log message ***

Revision 1.34  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.33  2018/04/25 16:45:28  rvv
*** empty log message ***

Revision 1.32  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.31  2017/03/25 16:01:09  rvv
*** empty log message ***

Revision 1.30  2016/02/13 14:02:39  rvv
*** empty log message ***

Revision 1.29  2015/12/30 19:01:23  rvv
*** empty log message ***

Revision 1.28  2015/12/20 16:46:36  rvv
*** empty log message ***

Revision 1.27  2015/11/23 12:50:46  rvv
*** empty log message ***

Revision 1.26  2015/10/04 11:52:21  rvv
*** empty log message ***

Revision 1.25  2015/09/26 15:57:57  rvv
*** empty log message ***

Revision 1.24  2015/09/13 11:32:51  rvv
*** empty log message ***

Revision 1.23  2015/05/20 16:04:40  rvv
*** empty log message ***

Revision 1.22  2015/05/03 13:03:54  rvv
*** empty log message ***

Revision 1.21  2015/03/19 07:20:21  rvv
*** empty log message ***

Revision 1.20  2015/03/19 07:00:37  rvv
*** empty log message ***

Revision 1.19  2015/03/18 16:29:56  rvv
*** empty log message ***

Revision 1.18  2014/12/03 17:30:11  rvv
*** empty log message ***

Revision 1.17  2014/09/06 15:24:17  rvv
*** empty log message ***

Revision 1.16  2014/09/03 15:56:32  rvv
*** empty log message ***

Revision 1.15  2014/07/19 14:27:59  rvv
*** empty log message ***

Revision 1.14  2014/06/29 15:38:56  rvv
*** empty log message ***


*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once("rapport/include/rapportATTberekening_L51.php");
include_once("rapport/include/ATTberekening_L68.php");

class RapportPERF_L51
{

	function RapportPERF_L51($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
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
	    $this->bedragDecimalen=0;

	  $this->periodeId = substr(jul2db(db2jul($this->rapportageDatumVanaf)),0,10)."-".substr(jul2db(db2jul($this->rapportageDatum)),0,10);
	  $this->db = new DB();
/*
	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     flush();
     exit;
	 }
  */
    
    $this->att=new ATTberekening_L68($this);
    
    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
	  $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');

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
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	}

	function getAttributieCategorien($realCategorie)
	{
	  $this->AttCategorien=array('Totaal');
    $categorieOmschrijving['totaal'] = 'Totaal';
 		$query="SELECT KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
AttributieCategorien.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN AttributieCategorien ON KeuzePerVermogensbeheerder.waarde = AttributieCategorien.AttributieCategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='AttributieCategorien'
ORDER BY Afdrukvolgorde";
		$this->db->SQL($query);
    $this->db->Query();
		while($categorie = $this->db->nextRecord())
		{
		  $categorieOmschrijving[$categorie['waarde']]=$categorie['Omschrijving'];
      $this->AttCategorien[]=$categorie['waarde'];
		}
  
	  $query = "SELECT  BeleggingssectorPerFonds.AttributieCategorie,  AttributieCategorien.Omschrijving
              FROM BeleggingssectorPerFonds  ,AttributieCategorien
              WHERE BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
              BeleggingssectorPerFonds.AttributieCategorie =  AttributieCategorien.AttributieCategorie
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie
              ORDER By AttributieCategorien.Afdrukvolgorde";
		$this->db->SQL($query);
		$this->db->Query();
		//$this->categorien[] = 'Totaal';
		
		while($categorie = $this->db->nextRecord())
		{
		  $categorieOmschrijving[$categorie['AttributieCategorie']]=$categorie['Omschrijving'];
		  //$this->categorien[]=$categorie['AttributieCategorie'];
		}
		if(!in_array('Liquiditeiten',$this->categorien))
		{
		  $categorieOmschrijving['Liquiditeiten']='Liquiditeiten';
		 // $this->categorien[]='Liquiditeiten';
		}

    $this->pdf->ln();
    $y=$this->pdf->GetY();
		$kopRegel = array();
	  array_push($kopRegel,"");
	  array_push($kopRegel,"");
		foreach ($realCategorie as $categorie)
		{
		  array_push($kopRegel,vertaalTekst($categorieOmschrijving[$categorie],$this->pdf->rapport_taal));
		  array_push($kopRegel,"");
		}
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row($kopRegel);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetY($y+8);
		return $realCategorie;
	}
/*
  function bepaalCategorieWaarden()
  {

    foreach ($this->categorien as $categorie)
    {
      if ($categorie == 'Totaal')
        $attributieQuery = '';
      elseif ($categorie == 'Liquiditeiten')
        $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '' AND ";
      else
        $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '".$categorie."' AND";
     
		  if ($categorie == 'Totaal' || $this->pdf->debug)
		  {

		    $gerealiseerdKoersresultaat[$categorie] = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,true,$categorie);
        $totaleWaarde=array();
	 		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 $attributieQuery
						 ." type = 'fondsen' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
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
    		$opgelopenRentes[$categorie] = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;


      }
    }

    
    $waarden=array('gerealiseerdKoersresultaat'=>$gerealiseerdKoersresultaat,
                   'ongerealiseerdeKoersResultaaten'=>$ongerealiseerdeKoersResultaaten,
                   'opgelopenRentes'=>$opgelopenRentes);//, 'koersResulaatValutas'=>$koersResulaatValutas

    $this->waarde = $waarden;
  return $waarden;
  }
*/

  function createRows()
  {
    $row['waardeVanaf'] = array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $row['waardeTot'] = array("",vertaalTekst("Waarde vermogen per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $row['mutatiewaarde'] = array("",vertaalTekst("Mutatie waarde vermogen",$this->pdf->rapport_taal));
    $row['totaalStortingen'] = array("",vertaalTekst("Totaal stortingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['totaalOnttrekkingen'] = array("",vertaalTekst("Totaal onttrekkingen gedurende verslagperiode",$this->pdf->rapport_taal));
    $row['directeOpbrengsten'] = array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
    $row['toegerekendeKosten'] = array("",vertaalTekst("Toegerekende kosten",$this->pdf->rapport_taal));
    $row['resultaatVerslagperiode'] = array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcent'] = array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcentJaar'] = array("",vertaalTekst("Rendement over lopende jaar",$this->pdf->rapport_taal));
    $row['gerealiseerdKoersresultaat'] = array("",vertaalTekst("gerealiseerdKoersresultaat",$this->pdf->rapport_taal));
    $row['ongerealiseerdeKoersResultaaten'] = array("",vertaalTekst("ongerealiseerdeKoersResultaaten",$this->pdf->rapport_taal));
    $row['opgelopenRentes'] = array("",vertaalTekst("opgelopenRentes",$this->pdf->rapport_taal));
    $row['totaal'] = array("",vertaalTekst("Totaal Performance",$this->pdf->rapport_taal));
   
   
    //listarray($this->waarden['rapportagePeriode']);
    
    foreach ($this->categorien as $categorie)
    {
      $this->waarden['rapportagePeriode'][$categorie]['mutatie']=$this->waarden['rapportagePeriode'][$categorie]['eindwaarde']-$this->waarden['rapportagePeriode'][$categorie]['beginwaarde'];
      
      /*
      $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode'][$categorie]['mutatie'] - $this->waarden['rapportagePeriode'][$categorie]['storting'] + $this->waarden['rapportagePeriode'][$categorie]['onttrekking'] + $this->waarden['rapportagePeriode'][$categorie]['opbrengst'] - $this->waarden['rapportagePeriode'][$categorie]['kosten'];
      if ($categorie == 'totaal')
      {
       // $resultaatCorrectie = $resultaatVerslagperiode['Totaal'] - $this->waarde['opgelopenrente'] - $this->waarde['ongerealiseerdFondsResultaat'][$categorie] -
       //   $this->waarde['gerealiseerdFondsResultaat'][$categorie]-($this->waardenPerGrootboek['opbrengst'] - $this->waardenPerGrootboek['kosten']);
  
      //  echo   "PERF koersResulaatValutas = ".      $resultaatCorrectie." =  ".$resultaatVerslagperiode['Totaal']." - ".$this->waarden['opgelopenRentes'][$categorie]." - ".$this->waarden['ongerealiseerdeKoersResultaaten'][$categorie]." - ".
      //    $this->waarden['gerealiseerdKoersresultaat'][$categorie]."-(".$this->waardenPerGrootboek['totaalOpbrengst']." - ".$this->waardenPerGrootboek['totaalKosten'].")<br>\n";
      //  $this->waarden['koersResulaatValutas'][$categorie]=$resultaatCorrectie;
        
        if(round($resultaatCorrectie,1) != round($this->waarden['gerealiseerdKoersresultaat'][$categorie],1))//correctie vreemde valuta
        {
        //  $this->waarden['gerealiseerdKoersresultaat'][$categorie]  = $resultaatCorrectie ;
        }
        
      }
     */
      array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['beginwaarde'],$this->bedragDecimalen,true));
      array_push($row['waardeVanaf'],"");

      array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['eindwaarde'],$this->bedragDecimalen));
      array_push($row['waardeTot'],"");

      array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['mutatie'],$this->bedragDecimalen));
      array_push($row['mutatiewaarde'],"");

      if ($categorie == 'Liquiditeiten')
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting'],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking'],$this->bedragDecimalen));
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcentJaar'],' ');
        array_push($row['rendementProcentJaar'],' ');
      }
      else
      {
       // echo "$categorie <br>\n";ob_flush();
        if ($categorie == 'totaal')
        {
          array_push($row['totaalOnttrekkingen'], $this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting'], $this->bedragDecimalen));
          array_push($row['totaalStortingen'], $this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking'], $this->bedragDecimalen));
        }
        else
        {
          array_push($row['totaalOnttrekkingen'], $this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['storting'] - $this->waarden['rapportagePeriode'][$categorie]['opbrengst'], $this->bedragDecimalen));
          array_push($row['totaalStortingen'], $this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['onttrekking'] - $this->waarden['rapportagePeriode'][$categorie]['kosten'], $this->bedragDecimalen));
  
        }
        array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['procent'],2));
        array_push($row['rendementProcent'],'%');
        array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar'][$categorie]['procent'],2));
        array_push($row['rendementProcentJaar'],'%');
      }

      array_push($row['totaalStortingen'],"");
      array_push($row['totaalOnttrekkingen'],"");

      if ($categorie == 'totaal')
      {
        array_push($row['directeOpbrengsten'],'0');
        array_push($row['toegerekendeKosten'],'0');
      }
      else
      {
        array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['opbrengst'],$this->bedragDecimalen));
        array_push($row['toegerekendeKosten'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['kosten'],$this->bedragDecimalen));
      }
      array_push($row['directeOpbrengsten'],"");
      array_push($row['toegerekendeKosten'],"");

      array_push($row['resultaatVerslagperiode'],$this->formatGetal($this->waarden['rapportagePeriode'][$categorie]['resultaat'],$this->bedragDecimalen));
      array_push($row['resultaatVerslagperiode'],"");
   }
  return $row;
  }



	function writeRapport()
	{
	  $this->tweedeStart();
	 	$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$kopStyle = "u";
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
      $hpiGebruik=true;
    else
      $hpiGebruik=false;
    
    $this->waarden['rapportagePeriode'] = $this->att->bereken($this->rapportageDatumVanaf, $this->rapportageDatum, 'attributie',$hpiGebruik);
    $this->att=new ATTberekening_L68($this);
    $this->waarden['lopendeJaar'] = $this->att->bereken($this->tweedePerformanceStart, $this->rapportageDatum, 'attributie',$hpiGebruik);
    
  
    $realCategorie=array('totaal'=>'totaal');
    foreach($this->waarden['lopendeJaar'] as $categorie=>$details)
    {
      if($details['eindwaarde'] <> 0 || $details['beginwaarde'] <> 0 || $details['storting'] <> 0 || $details['onttrekking'] <> 0)
      {
        $realCategorie[$categorie]=$categorie;
      }
    }
  
  
    if(count($realCategorie) > 6)
      $x=185/count($realCategorie)-3;
    else
      $x=23;  

    $this->pdf->widthA = array(0,95,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->widthB = array(0,95,30,10,30,116);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R');
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->AddPage();
    $this->pdf->templateVars['PERFPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['PERFPaginas']=$this->pdf->rapport_titel;
 
    
    //$this->pdf->row(array("",vertaalTekst("Resultaat verslagperiode",$this->pdf->rapport_taal),"",""));
    $this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_kop_fontsize);
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
    
	  $this->categorien = $this->getAttributieCategorien($realCategorie);

    $row = $this->createRows();
    $uLines=array('','');
    $uuLines=array('','');
    $uuRendementLines=array('','');
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($realCategorie as $categorie)
    {
      array_push($uLines,'U');
      array_push($uLines,'');
      array_push($uuLines,'UU');
      array_push($uuLines,'');
      if($categorie=='Liquiditeiten')
      {
        array_push($uuRendementLines,'');
        array_push($uuRendementLines,'');
      }
      else
      {
        array_push($uuRendementLines,'UU');
        array_push($uuRendementLines,'');
      }
    }
    
		$this->pdf->row($row['waardeVanaf']);
		$this->pdf->CellBorders = $uLines;
		$this->pdf->row($row['waardeTot']);
		$this->pdf->CellBorders = array();
		$this->pdf->ln();

		$this->pdf->row($row['mutatiewaarde']);
		$this->pdf->row($row['totaalStortingen']);
    $this->pdf->CellBorders = $uLines;
 		$this->pdf->row($row['totaalOnttrekkingen']);
 		//$this->pdf->row($row['directeOpbrengsten']);
   	//$this->pdf->row($row['toegerekendeKosten']);
		$this->pdf->CellBorders = array();
		$this->pdf->ln();

		$this->pdf->CellBorders = $uuLines;
		$this->pdf->row($row['resultaatVerslagperiode']);
	  $this->pdf->CellBorders = array();
		$this->pdf->ln();

		$this->pdf->row($row['rendementProcent']);
		$this->pdf->CellBorders = $uuRendementLines;
		$this->pdf->row($row['rendementProcentJaar']);
		$this->pdf->CellBorders = array();
  
	//	listarray($this->waarden['rapportagePeriode']);
	 if($this->pdf->debug)
	 {
		$this->pdf->row(array(''));
		//	$this->pdf->row($row['directeOpbrengsten']);
		//	$this->pdf->row($row['toegerekendeKosten']);
			$this->pdf->row(array('gerealiseerdKoersresultaat','',$this->waarden['rapportagePeriode']['totaal']['gerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['gerealiseerdValutaResultaat']));
			$this->pdf->row(array('ongerealiseerdeKoersResultaaten','',$this->waarden['rapportagePeriode']['totaal']['ongerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['ongerealiseerdValutaResultaat']));
			$this->pdf->row(array('opgelopenRentes','',$this->waarden['rapportagePeriode']['totaal']['opgelopenrente']));
			//$this->pdf->row(array('totaal','',$this->waarden['rapportagePeriode']['totaal']['Totaal']));
	 }
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$ypos = $this->pdf->GetY();
		$this->pdf->SetY($ypos);
		$this->pdf->ln();
    $totaalOpbrengst=0;
		$totaalOpbrengst += $this->waarden['rapportagePeriode']['totaal']['opgelopenrente'];
		$totaalOpbrengst += $this->waarden['rapportagePeriode']['totaal']['ongerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['ongerealiseerdValutaResultaat'];
		$totaalOpbrengst += $this->waarden['rapportagePeriode']['totaal']['gerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['gerealiseerdValutaResultaat'];
  //listarray($totaalOpbrengst);
		//listarray($this->waarden['rapportagePeriode']['totaal']);exit;
    //koersResulaatValutas
		$koersResulaatValutas = $this->waarden['rapportagePeriode']['totaal']['resultaat']-($totaalOpbrengst+$this->waarden['rapportagePeriode']['totaal']['kosten']+$this->waarden['rapportagePeriode']['totaal']['opbrengst']); // $this->waarden['koersResulaatValutas']['Totaal'];
		$totaalOpbrengst += $koersResulaatValutas;
		$posSubtotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1];
		$posSubtotaalEnd = $posSubtotaal + $this->pdf->widthA[2];
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->ln();
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b'.$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
    
    $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening FROM Grootboekrekeningen ORDER BY Grootboekrekeningen.Afdrukvolgorde ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $grootboekOmschrijvingen = array();
    while($grootboek = $DB->nextRecord())
    {
      $grootboekOmschrijvingen[$grootboek['Grootboekrekening']]=$grootboek['Omschrijving'];
    }


  // listarray($this->waarden['rapportagePeriode']['totaal']);
  	  if(round($this->waarden['rapportagePeriode']['totaal']['ongerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['ongerealiseerdValutaResultaat'],2) != 0.00)
  	   	$this->pdf->row(array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['rapportagePeriode']['totaal']['ongerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['ongerealiseerdValutaResultaat'],$this->bedragDecimalen),""));
  	 	if(round($this->waarden['rapportagePeriode']['totaal']['gerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['gerealiseerdValutaResultaat'],2) != 0.00)
		    $this->pdf->row(array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['rapportagePeriode']['totaal']['gerealiseerdFondsResultaat']+$this->waarden['rapportagePeriode']['totaal']['gerealiseerdValutaResultaat'],$this->bedragDecimalen),""));
		  if(round($koersResulaatValutas,2) != 0.00)
		    $this->pdf->row(array("",vertaalTekst("Koersresultaten liquiditeiten",$this->pdf->rapport_taal),$this->formatGetal($koersResulaatValutas,$this->bedragDecimalen),""));

  	if(round($this->waarden['rapportagePeriode']['totaal']['opgelopenrente'],2) != 0.00)
			 $this->pdf->row(array("",vertaalTekst("Resultaat opgelopen rente",$this->pdf->rapport_taal),$this->formatGetal($this->waarden['rapportagePeriode']['totaal']['opgelopenrente'],$this->bedragDecimalen),""));

		foreach ($this->waarden['rapportagePeriode']['totaal']['grootboekOpbrengsten'] as $grootboek=>$waarde)
		{
      if(isset($grootboekOmschrijvingen[$grootboek]))
        $omschrijving=$grootboekOmschrijvingen[$grootboek];
      else
        $omschrijving=$grootboek;
      
		  if(round($waarde,2) != 0.00)
			  $this->pdf->row(array("",vertaalTekst($omschrijving,$this->pdf->rapport_taal),$this->formatGetal($waarde,$this->bedragDecimalen),""));
			  $totaalOpbrengst += $waarde;
		}

		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($totaalOpbrengst,$this->bedragDecimalen)));
		$this->pdf->ln();
		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
    $grootboekKosten=0;
		foreach ($this->waarden['rapportagePeriode']['totaal']['grootboekKosten']  as $grootboek=>$waarde)
		{
		  if(isset($grootboekOmschrijvingen[$grootboek]))
		    $omschrijving=$grootboekOmschrijvingen[$grootboek];
		  else
        $omschrijving=$grootboek;
      
      if(round($waarde,2) != 0.00)
        $this->pdf->row(array("",vertaalTekst($omschrijving,$this->pdf->rapport_taal),$this->formatGetal($waarde,$this->bedragDecimalen),""));
      $grootboekKosten += $waarde;
		}

		$this->pdf->Line($posSubtotaal ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->row(array("","","","",$this->formatGetal($grootboekKosten,$this->bedragDecimalen)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->row(array("",vertaalTekst("Resultaat lopende jaar",$this->pdf->rapport_taal),"","",$this->formatGetal($totaalOpbrengst + $grootboekKosten,$this->bedragDecimalen)));
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY() ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY());
		$this->pdf->Line($posTotaal +2 ,$this->pdf->GetY()+1 ,$posTotaal + $this->pdf->widthA[4] ,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


   $this->toonZorgplicht();
  
	 if($this->pdf->debug)
	 {
	  // listarray($this->berekening->performance);flush();
	  // exit;
   }
	}

  
   
  
  
   function toonZorgplicht()
  {
    global $__appvar;

		$categorieWaarden=array();

		$zorgplicht = new Zorgplichtcontrole();
		$zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

		$zpdata=array();
		foreach ($zpwaarde['conclusie'] as $index=>$regelData)
			$zpdata[$regelData[0]]=$regelData;
		ksort($zpdata);

		//listarray($zpwaarde['conclusie']);


    		// haal totaalwaarde op om % te berekenen
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


		$query="SELECT
ZorgplichtPerBeleggingscategorie.Zorgplicht,
ZorgplichtPerBeleggingscategorie.Beleggingscategorie,
ZorgplichtPerBeleggingscategorie.Vermogensbeheerder,
Beleggingscategorien.Omschrijving as beleggingscategorieOmschrijving
FROM
ZorgplichtPerBeleggingscategorie
INNER JOIN Beleggingscategorien ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
WHERE ZorgplichtPerBeleggingscategorie.Zorgplicht IN('".implode("','",array_keys($zpdata))."') AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
		$DB->SQL($query);
		$DB->Query();
		while($data= $DB->nextRecord())
		{
			$categorieWaarden[$data['Zorgplicht']]=0;
			$categorieOmschrijving[$data['Zorgplicht']]=$data['beleggingscategorieOmschrijving'];
		}


    if($totaalWaarde['totaal'] <> 0)
    {
       $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$totaalWaarde['totaal']." as percentage,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE TijdelijkeRapportage.Portefeuille =  '".$this->portefeuille."' AND
 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
      $DB->SQL($query);
      $DB->Query();
		  while($data= $DB->nextRecord())
		  {
		    $categorieWaarden[$data['Zorgplicht']]=$data['percentage']*100;
        $categorieOmschrijving[$data['Zorgplicht']]=$data['beleggingscategorieOmschrijving'];
		  }
    }

    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->SetY(150);
    $beginY=$this->pdf->getY();
    $extraX=155;
   	
  	$this->pdf->SetWidths(array($extraX,40,16,16,16,16,16));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',
    vertaalTekst("beleggingscategorie",$this->pdf->rapport_taal),
    vertaalTekst("minimaal",$this->pdf->rapport_taal),
    vertaalTekst("norm",$this->pdf->rapport_taal),
    vertaalTekst("maximaal",$this->pdf->rapport_taal),
    vertaalTekst("werkelijk",$this->pdf->rapport_taal),
    vertaalTekst("conclusie",$this->pdf->rapport_taal)));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
  	//foreach ($tmp as $index=>$regelData)
    
    /*
    - zorgplichtcategorie
- minimum
- neutraal (is norm)
- maximum
- werkelijk
*/
  //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($zpdata[$cat][2])
        $risicogewogen=$zpdata[$cat][2]."%";
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
        $norm=$this->formatGetal($zpwaarde['categorien'][$cat]['Norm'],0)."%";
        
      if($zpdata[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,120+$extraX,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,120+$extraX,$this->pdf->getY(),3.9,3.9);  

  	  $this->pdf->row(array('',vertaalTekst($categorieOmschrijving[$cat],$this->pdf->rapport_taal),$min,$norm,$max,$zpdata[$cat][2]."%"));//$risicogewogen $this->formatGetal($categorieWaarden[$cat],1)
    }
    $this->pdf->Rect($this->pdf->marge+$extraX,$beginY,120,count($categorieWaarden)*4+4);
  }


}

?>