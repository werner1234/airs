<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportEND_L109
{
	function RapportEND_L109($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "END";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Geschiktheidsrapportage";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
		$this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
		$this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0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');
		$this->DB = new DB();
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	
  function zorgplichtMeting($pdata,$einddatum)
	{
	  global $__appvar;
	  	$zorgMeting = "Voldoet ";
			$zorgMetingReden = "";
      $voldoetNietReden='';
	    $portefeuille = $pdata['Portefeuille'];
	  	$DB3 = new DB();
	  	$DB4 = new DB();
			// haal totaalwaarde op om % te berekenen
			$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
							 "FROM TijdelijkeRapportage WHERE ".
							 " rapportageDatum ='".$einddatum."' AND ".
							 " portefeuille = '".$portefeuille."' "
							  .$__appvar['TijdelijkeRapportageMaakUniek'];
			debugSpecial($query,__FILE__,__LINE__);
			$DB3->SQL($query);
			$DB3->Query();
			$totaalWaarde = $DB3->nextRecord();
			$totaalWaarde = $totaalWaarde['totaal'];

			if ($this->pdf->rapport_layout == 8)//RVV 29-08-06 liquiditeiten ophalen
			{
			 $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
			    	  "FROM TijdelijkeRapportage WHERE ".
					  " type <> 'fondsen' AND ".
					  " rapportageDatum ='".$einddatum."' AND ".
					  " portefeuille = '".$portefeuille."' "
					   .$__appvar['TijdelijkeRapportageMaakUniek'];
			 debugSpecial($query,__FILE__,__LINE__);
			 $DB2 = new DB();
			 $DB2->SQL($query);
			 $DB2->Query();
			 $zorgtotaaldata = $DB2->nextRecord();
			 $liquiditeitentotaal = $zorgtotaaldata['totaal'];
			}//end rvv liquiditeiten ophalen


  $koppeling='beleggingscategorie';


//  echo $query;exit;
  
  
    $zorgTabel='vermogensbeheerder	categorie	waarde	Zorgplicht	Afdrukvolgorde
AUR	Beleggingscategorien	Liquiditeiten	Liquiditeiten	0
AUR	Beleggingscategorien	AU-A IndAand	Aandelen	1
AUR	Beleggingscategorien	AU-A Markt	Aandelen	2
AUR	Beleggingscategorien	AU-A Factor	Aandelen	3
AUR	Beleggingscategorien	AU-A Actief	Aandelen	4
AUR	Beleggingscategorien	AU-A EquityLS	Aandelen	5
AUR	Beleggingscategorien	AU-A Eq MktNeut	Aandelen	6
AUR	Beleggingscategorien	AU-A PrivEquity	Aandelen	7
AUR	Beleggingscategorien	AU-O Staats	Obligaties	8
AUR	Beleggingscategorien	AU-O Infl Lk	Obligaties	9
AUR	Beleggingscategorien	AU-O Ged Oblig	Obligaties	10
AUR	Beleggingscategorien	AU-O Bedrijfs	Obligaties	11
AUR	Beleggingscategorien	AU-O Achterg.	Obligaties	12
AUR	Beleggingscategorien	AU-O High Yld	Obligaties	13
AUR	Beleggingscategorien	AU-O Emerg Debt	Obligaties	14
AUR	Beleggingscategorien	AU-O Pref Aand	Obligaties	15
AUR	Beleggingscategorien	AU-O Distr Debt	Obligaties	16
AUR	Beleggingscategorien	AU-O Conv. Arb.	Obligaties	17
AUR	Beleggingscategorien	AU-O Cred&Bd Ar	Obligaties	18
AUR	Beleggingscategorien	AU-O Priv Dt	Obligaties	19
AUR	Beleggingscategorien	AU-O Peer-t-p	Obligaties	20
AUR	Beleggingscategorien	AU-O Crowd Fd	Obligaties	21
AUR	Beleggingscategorien	AU-Al Hedgefnd	Alternatieven	22
AUR	Beleggingscategorien	AU-Al Grondst	Commodities	23
AUR	Beleggingscategorien	AU-Al Goud	Commodities	24
AUR	Beleggingscategorien	AU-Al Dir Vastg	Vastgoed	25
AUR	Beleggingscategorien	AU-L Geldmarktf	Liquiditeiten	26';
  
    $normTabel='Risicoprofiel	Zorgplicht	Norm	Minimum	Maximum
Zeer Defensief	Aandelen	10	0	20
Zeer Defensief	Vastgoed	0	0	5
Zeer Defensief	Alternatieven	0	0	5
Zeer Defensief	Commodities	0	0	5
Zeer Defensief	Obligaties	80	0	100
Zeer Defensief	Liquiditeiten	10	0	100
Defensief	Aandelen	25	0	35
Defensief	Vastgoed	5	0	10
Defensief	Alternatieven	0	0	10
Defensief	Commodities	0	0	10
Defensief	Obligaties	60	0	100
Defensief	Liquiditeiten	10	0	100
Neutraal	Aandelen	45	0	60
Neutraal	Vastgoed	5	0	10
Neutraal	Alternatieven	0	0	15
Neutraal	Commodities	0	0	10
Neutraal	Obligaties	40	0	100
Neutraal	Liquiditeiten	10	0	100
Offensief	Aandelen	65	0	80
Offensief	Vastgoed	5	0	15
Offensief	Alternatieven	0	0	15
Offensief	Commodities	0	0	10
Offensief	Obligaties	20	0	100
Offensief	Liquiditeiten	10	0	100
Zeer Offensief	Aandelen	85	0	100
Zeer Offensief	Vastgoed	5	0	15
Zeer Offensief	Alternatieven	0	0	15
Zeer Offensief	Commodities	0	0	10
Zeer Offensief	Obligaties	0	0	100
Zeer Offensief	Liquiditeiten	10	0	100
Niet beursgenoteerd, risicodragend	Vastgoed	0	0	100';
  
    $zorgRegels=explode("\n",$zorgTabel);
    $normRegels=explode("\n",$normTabel);
    $categorieKoppeling=array();
    foreach($zorgRegels as $i=>$regel)
    {
      $velden=explode("\t",$regel);
      if($i==0)
      {
        continue;
      }
      else
      {
        $categorieKoppeling[$velden[2]] = $velden[3];
      }
    
    }
  
    $zorgplichtIndeling=array();
    foreach($normRegels as $i=>$regel)
    {
      $velden=explode("\t",$regel);
      if($i==0)
      {
        $header=$velden;
        continue;
      }
      else
      {
        $tmp=array();
        foreach($velden as $index=>$waarde)
          $tmp[$header[$index]]=$waarde;
        $zorgplichtIndeling[$velden[0]][$velden[1]]=$tmp;
        
      }
    
    }
    //listarray($pdata);
    //listarray($zorgplichtIndeling);exit;
    
    
$query= "SELECT
       TijdelijkeRapportage.beleggingscategorie,
			100 as Percentage,
	    actuelePortefeuilleWaardeEuro as totaal,
	    sum(actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro,
	    TijdelijkeRapportage.fondsOmschrijving,
      TijdelijkeRapportage.type,
      TijdelijkeRapportage.Fonds,
      TijdelijkeRapportage.rekening,
      SUM(if(TijdelijkeRapportage.type='fondsen',TijdelijkeRapportage.totaalAantal,0)) as totaalAantal,
      TijdelijkeRapportage.actueleFonds,
      TijdelijkeRapportage.hoofdcategorie
      FROM TijdelijkeRapportage
      WHERE  rapportageDatum ='".$einddatum."' AND portefeuille = '".$portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
      GROUP BY TijdelijkeRapportage.beleggingscategorie
      ORDER BY TijdelijkeRapportage.beleggingscategorie"; //
$DB3->SQL($query); //echo "<br>\n". $query."<br>\n<br>\n";exit;
$DB3->Query();
//$totaalWaarde=0;
  
    if(isset($zorgplichtIndeling[$pdata['Risicoklasse']]))
      $pZorg=$zorgplichtIndeling[$pdata['Risicoklasse']];
    else
      $pZorg=$zorgplichtIndeling['Neutraal'];
  
    $waardePerZorgplicht=array();
while($data = $DB3->nextRecord())
{
  
  $data['Zorgplicht']=$categorieKoppeling[$data['beleggingscategorie']];

  if($data['Zorgplicht'] == '')
    $data['Zorgplicht']='Overige';
  

  $waardePerZorgplicht[$data['Zorgplicht']]+=$data['totaal'];
  $zorgplichtCategorien[$data['Zorgplicht']]=$data['Zorgplicht'];
  
  

  $tmp=$pZorg[$data['Zorgplicht']];
  $tmp['Zorgplicht']=$data['Zorgplicht'];
 // $tmp['fondsGekoppeld']=true;
    
  $zpCategorien[$data['Zorgplicht']]=$tmp;//array('Minimum'=>0,'Maximum'=>100,'Zorgplicht'=>$data['Zorgplicht'],'fondsGekoppeld'=>true);
}


//listarray($zpCategorien);
$conclusie=array();
$conclusieDetailFonds=array();

foreach ($zpCategorien as $zpdata)
{
  $txt2='Voldoet';
  $zorgtotaal = $waardePerZorgplicht[$zpdata['Zorgplicht']];
  $txt='';
  if(1)//$zorgtotaal <> 0 ||
  {
    $zorgPercentage = round($zorgtotaal / ($totaalWaarde / 100),4);
//echo "<br>\n ".$zpdata['Zorgplicht']."<br>\n $zorgPercentage = round($zorgtotaal / ($totaalWaarde / 100),4);<br>\n";
//echo "$zorgPercentage > ".$zpdata['Maximum']." || $zorgPercentage < ".$zpdata['Minimum'] ."<br>\n";
//listarray($zpdata);
    $afwijking=0;
    if($zorgPercentage < $zpdata['Minimum'] || $zorgPercentage > $zpdata['Maximum'])
    {
      $txt2='Voldoet niet';
      $zorgMeting = "Voldoet niet ";
      if($zorgPercentage < $zpdata['Minimum'])
      {
        $txt = $zpdata['Zorgplicht']." < ".$zpdata['Minimum']." % : ".round($zorgPercentage,2)." %\n";
        $afwijking=$zorgPercentage-$zpdata['Minimum'];
        if(!isset($zpdata['fondsGekoppeld']))
        {
          $zorgMetingReden .= $txt;
          $voldoetNietReden .= $txt;
        }
      }
      else
      {
        $txt = $zpdata['Zorgplicht']." > ".$zpdata['Maximum']." % : ".round($zorgPercentage,2)." %\n";
        $afwijking=$zorgPercentage-$zpdata['Maximum'];
        if(!isset($zpdata['fondsGekoppeld']))
        {
          $zorgMetingReden .= $txt;
          $voldoetNietReden .= $txt;
        }
      }
    }
    else
    {
      $txt=$zpdata['Zorgplicht']."=".round($zorgPercentage,2)." %\n";
      if(!isset($zpdata['fondsGekoppeld']))
        $zorgMetingReden .= $txt;
    }
  }
  
  //$zorgMetingReden.=$zpdata['Zorgplicht']." => $zorgtotaal / $totaalWaarde = ".round($zorgtotaal / ($totaalWaarde / 100),2)."\n";
  
  if(!isset($zpdata['fondsGekoppeld']))
    $conclusieDetail[$zpdata['Zorgplicht']]=array('percentage'=>$zorgPercentage,
                                                  'minimum'=>$zpdata['Minimum'],
                                                  'maximum'=>$zpdata['Maximum'],
                                                  'norm'=>$zpdata['Norm'],
                                                  'Norm'=>$zpdata['Norm'],
                                                  'afwijking'=>$afwijking);
  else
    $conclusieDetailFonds[$zpdata['Zorgplicht']]=array('percentage'=>$zorgPercentage,
                                                       'minimum'=>$zpdata['Minimum'],
                                                       'maximum'=>$zpdata['Maximum'],
                                                       'norm'=>$zpdata['Norm'],
                                                       'Norm'=>$zpdata['Norm'],
                                                       'afwijking'=>$afwijking);
  
  $conclusie[]=array($zpdata['Zorgplicht'],$zpdata['Minimum'].' < x < '.$zpdata['Maximum'], $this->formatGetal($zorgPercentage,1),$this->formatGetal($zorgtotaal,2),$txt,$txt2);
  $zorgPercentage=0;
  $zorgtotaal=0;
  
}

if ($this->pdf->rapport_layout == 8 && $zorgMetingReden == "") //rvv 29-08-06
{
  $zorgMetingReden= "Geen waarden ";
}

if($zorgMeting=="Voldoet ")
  $voldoet="Ja";
else
  $voldoet="Nee";

return array('totaalWaarde'=>$totaalWaarde,'zorgMeting'=>$zorgMeting,'zorgMetingReden'=>$zorgMetingReden,'voldoetNietReden'=>$voldoetNietReden,'voldoet'=>$voldoet,'detail'=>$waardePerFonds,'categorien'=>$zpCategorien,
             'conclusie'=>$conclusie,'conclusieDetail'=>$conclusieDetail,'conclusieDetailFonds'=>$conclusieDetailFonds);
}


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;

		$this->pdf->SetWidths(array(297/2-$this->pdf->marge*2));
		$this->pdf->SetAligns(array('L'));
		$this->pdf->setXY($this->pdf->marge,30);
		$this->pdf->SetFont($this->pdf->rapport_font,'bu',$this->pdf->rapport_fontsize);

		$query="SELECT ZpMethode,Selectieveld1,Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."' ";
    $this->DB->SQL($query);
    $zpMethode = $this->DB->lookupRecord();
    
  //  listarray($categorieKoppeling);
  
  
		$zorg=new zorgplichtControle($this->pdf->portefeuilledata);
		if($zpMethode['ZpMethode']==2)
      $zorgPlichtResultaat=$zorg->standaarddeviatieMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
		elseif($zpMethode['ZpMethode']==3)
      $zorgPlichtResultaat=$zorg->werkelijkeStandaarddeviatieMeting($this->pdf->portefeuilledata,$this->rapportageDatum);
		else
    {
      if($zpMethode['Selectieveld1']=='ZEE' && $zpMethode['Vermogensbeheerder']=='AUR')
        $zorgPlichtResultaat = $this->zorgplichtMeting($this->pdf->portefeuilledata, $this->rapportageDatum);
      else
        $zorgPlichtResultaat = $zorg->zorgplichtMeting($this->pdf->portefeuilledata, $this->rapportageDatum);
    }
		$zorgPlichtResultaat['zorgMetingReden']=str_replace("\n"," ",$zorgPlichtResultaat['zorgMetingReden']);

		if($zorgPlichtResultaat['voldoet']=='Ja')
		{
			$binnnenBuiten = 'binnen';
			$voldoetTxt='Wij zijn van mening dat onze vermogensbeheerdienst en de door ons voor u beheerde beleggingsportefeuille (inclusief de daarmee samenhangende transacties) op dit moment nog steeds geschikt voor u zijn. Mochten uw persoonlijke en/of financiële omstandigheden echter zijn gewijzigd, dan vernemen wij dat graag zo spoedig mogelijk. Wij zullen in dat geval beoordelen of de wijzigingen gevolgen hebben voor uw beleggingsprofiel en het beheer van uw vermogen.';
		}
		else
		{
			$binnnenBuiten = 'buiten';

			$velden=array();
			$checkVelden=array('KwartaalGeschikt');
			$query = "desc CRM_naw";
			$this->DB->SQL($query);
			$this->DB->query();
			while($data=$this->DB->nextRecord('num'))
				$velden[]=$data[0];
			$extraVeld='';
			foreach($checkVelden as $check)
				if(in_array($check,$velden))
					$extraVeld.=','.$check;

			$query = "SELECT verzendAanhef $extraVeld FROM CRM_naw WHERE portefeuille = '".$this->portefeuille."' ";
			$this->DB->SQL($query);
			$crmData = $this->DB->lookupRecord();
			if($crmData['KwartaalGeschikt']<>'')
				$voldoetTxt='Uw portefeuille voldoet momenteel (nog) niet aan onze geschiktheidseisen. U en uw relatiemanager zijn daarvan op de hoogte en hebben gezamenlijk besloten dit tijdelijk toe te staan. Daar is het volgende van vastgelegd in onze systemen: '.$crmData['KwartaalGeschikt'];
			else
			  $voldoetTxt='Uw portefeuille voldoet momenteel (nog) niet aan onze geschiktheidseisen. Dit kan diverse oorzaken hebben zoals bijvoorbeeld stortingen, onttrekkingen of recente wijzigingen in uw profiel. Uw relatiemanager zal met u contact opnemen om dit te bespreken voor zover dit reeds nog niet gebeurd is.';

		}

		$this->pdf->Row(array('Geschiktheidsrapportage portefeuillenummer '.$this->portefeuille.' '.$this->pdf->portefeuilledata['Naam'].' '.$this->pdf->portefeuilledata['Naam1']));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln();
		$this->pdf->Row(array("Deze geschiktheidsrapportage heeft tot doel vast te stellen of het door Auréus verrichte vermogensbeheer nog geschikt voor u is. De geschiktheidsbeoordeling stelt ons in staat (blijvend) te kunnen handelen in uw belang."));
    $this->pdf->ln();
    $this->pdf->Row(array("In dit verband beoordelen wij of uw beleggingsportefeuille (nog) past bij uw beleggingsprofiel. Uw Beleggingsprofiel is onder meer gebaseerd op uw beleggingsdoelstellingen, beleggingshorizon, financiële situatie, risicobereidheid (zowel emotioneel als uw mogelijkheid om beleggingsrisico’s financieel te kunnen dragen) en kennis en ervaring. Deze informatie is bij aanvang van de samenwerking bij u ingewonnen en vastgelegd, bijvoorbeeld in uw beleggingsvoorstel of vermogensplan."));
		$this->pdf->ln();
  	$this->pdf->Row(array("Op basis van de uitgebreide cliënt inventarisatie bij de aanvang van onze dienstverlening en de jaarlijkse herijking hiervan zijn we een beleggingsprofiel met u overeengekomen. Dat beleggingsprofiel is per brief aan u bevestigd. Momenteel is uw beleggingsprofiel ".$this->pdf->portefeuilledata['Risicoklasse'].". De kaders en de bandbreedtes van dit profiel zijn bij aanvang van onze dienstverlening aan u verstrekt. Mocht u deze opnieuw willen ontvangen vraag ons dan naar de bijlage ‘overzicht beleggingsprofielen’. Uw portefeuille bevindt zicht momenteel $binnnenBuiten de kaders van uw beleggingsprofiel en de daarbij gestelde bandbreedtes. De huidige weging in uw portefeuille is:"));
		$this->pdf->ln();

		$this->toonZorgplicht($zorgPlichtResultaat,$zpMethode['ZpMethode'],true);

		$this->pdf->SetWidths(array(297/2-$this->pdf->marge*2));
		$this->pdf->ln();
    $this->pdf->Row(array($voldoetTxt));
		$this->pdf->ln();
    $this->pdf->Row(array("Wij zullen verder periodiek (minimaal) jaarlijks in overleg met u treden voor een update van uw persoonlijke en financiële omstandigheden en een evaluatie van het door ons uitgevoerde vermogensbeheer."));


  }

	function toonZorgplicht($zpwaarde,$methode,$klein=false)
	{
		global $__appvar;
    $db=new DB();
    /*
		$query="SELECT
Zorgplichtcategorien.Vermogensbeheerder,
Zorgplichtcategorien.Zorgplicht,
Zorgplichtcategorien.Omschrijving
FROM Zorgplichtcategorien
WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
ORDER BY Zorgplichtcategorien.Omschrijving";
		$db->SQL($query);
		$db->query();
		
		while($data=$db->nextRecord())
		{
			$omschrijvingen[$data['Zorgplicht']]=$data['Omschrijving'];
		}
    */
    
    $query="SELECT
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.waarde,
Zorgplichtcategorien.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN Zorgplichtcategorien ON KeuzePerVermogensbeheerder.waarde = Zorgplichtcategorien.Zorgplicht AND KeuzePerVermogensbeheerder.vermogensbeheerder = Zorgplichtcategorien.Vermogensbeheerder
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie='Zorgplichtcategorien'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde,KeuzePerVermogensbeheerder.categorie";
    $this->DB->SQL($query);
    $this->DB->Query();
    $omschrijvingen=array('AFM'=>'Standaarddeviatie','stdev'=>'Standaarddeviatie');
    while($data=$this->DB->nextRecord())
    {
      $omschrijvingen[$data['waarde']]=$data['Omschrijving'];
    }
    
		$tmp=array();
		foreach ($zpwaarde['conclusie'] as $index=>$regelData)
    {
      if($methode==2)
        $regelData[0]='AFM';
      $tmp[$regelData[0]] = $regelData;
    }

		krsort($tmp);

    //listarray($zpwaarde['conclusieDetail']);
		//listarray($tmp);exit;

		$this->pdf->SetAligns(array('L','R','R','R','R','R'));
		$beginY=$this->pdf->getY();
  
		if($klein==true)
    {
      $this->pdf->SetWidths(array(60, 20, 20, 20, 20));
      $checkPos=140;
    }
		else
    {
      $this->pdf->SetWidths(array(60, 20, 20, 40, 50, 20));
      $checkPos=210;
    }
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		
		if($klein==true)
    {
      $this->pdf->row(array((($methode == 2 || $methode == 3)?'':'beleggingscategorie'), 'minimaal', 'maximaal', "werkelijk", "conclusie"));
    }
		else
    {
      //$this->pdf->line($this->pdf->marge,$this->pdf->getY(),$this->pdf->marge+210,$this->pdf->getY());
      $this->pdf->row(array((($methode == 2 || $methode == 3)?'':'Beleggingscategorie'), 'Minimaal', 'Maximaal', 'Zorgplichtwaarde EUR', "Zorgplichtwaarde percentage", "Conclusie"));
    }
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetAligns(array('L','R','R','R','R','R'));
		
    foreach($zpwaarde['conclusieDetail'] as $cat=>$details)
    {
      if(!isset($omschrijvingen[$cat]))
        $omschrijvingen[$cat]=$cat;
    }
    //asort($omschrijvingen);
    $totaalZp=0;
    $totaalZpProcent=0;
    foreach($omschrijvingen as $zorgplichtCategorie=>$omschrijving)//foreach($zpwaarde['conclusieDetail'] as $cat=>$details)
		{
      if(!isset($zpwaarde['conclusieDetail'][$zorgplichtCategorie]))
        continue;
      $details=$zpwaarde['conclusieDetail'][$zorgplichtCategorie];
			//if($zpwaarde['categorien'][$cat]['Minimum'])
      if(round($details['minimum'])==$details['minimum'])
			  $min=$this->formatGetal($details['minimum'],0)."%";
      else
        $min=$this->formatGetal($details['minimum'],1)."%";
			//else
			//   $min='';
			//if($zpwaarde['categorien'][$cat]['Maximum'])
      if(round($details['maximum'])==$details['maximum'])
			  $max=$this->formatGetal($details['maximum'],0)."%";
      else
        $max=$this->formatGetal($details['maximum'],1)."%";
			// else
			//   $max='';

			if($tmp[$zorgplichtCategorie][5]=='Voldoet')
				$this->pdf->MemImage($this->checkImg,$checkPos,$this->pdf->getY(),3.9,3.9);
			else
				$this->pdf->MemImage($this->deleteImg,$checkPos,$this->pdf->getY(),3.9,3.9);
			
			$zpWaarde=(str_replace(array('.',','),array('','.'),$tmp[$zorgplichtCategorie][3]));
			
			if($klein==true)
        $this->pdf->row(array($omschrijving,$min,$max,$this->formatGetal($details['percentage'],1)."%"));
			else
		  	$this->pdf->row(array($omschrijving,$min,$max,$this->formatGetal($zpWaarde,0),$this->formatGetal($details['percentage'],1)."%"));
			$totaalZp+=$zpWaarde;
      $totaalZpProcent+=$details['percentage'];
		}
    $extraY=4;
    if($klein==false)
    {
      $this->pdf->row(array('Totaal', '', '', $this->formatGetal($totaalZp, 0), $this->formatGetal($totaalZpProcent, 1) . "%"));
      //$this->pdf->line($this->pdf->marge, $this->pdf->getY(), $this->pdf->marge + 210, $this->pdf->getY());
      $extraY+=4;
    }
    //else
	  	$this->pdf->Rect($this->pdf->marge,$beginY,$checkPos,count($zpwaarde['conclusieDetail'])*4+$extraY);
	}
}
?>
