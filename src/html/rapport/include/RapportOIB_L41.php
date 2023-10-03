<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.8 $

$Log: RapportOIB_L41.php,v $
Revision 1.8  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.7  2012/12/30 14:27:12  rvv
*** empty log message ***

Revision 1.6  2012/12/02 11:05:56  rvv
*** empty log message ***

Revision 1.5  2012/11/17 16:02:20  rvv
*** empty log message ***

Revision 1.4  2012/11/04 13:15:03  rvv
*** empty log message ***

Revision 1.3  2012/11/03 18:14:13  rvv
*** empty log message ***

Revision 1.2  2012/10/24 18:06:07  rvv
*** empty log message ***

Revision 1.1  2012/08/11 13:51:25  rvv
*** empty log message ***

Revision 1.2  2012/06/20 18:11:09  rvv
*** empty log message ***

Revision 1.1  2012/06/17 13:04:11  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");

class RapportOIB_L41
{
	function RapportOIB_L41($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Opbouw en verdeling van het vermogen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->pdf->underlinePercentage=0.8;
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


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}


	function writeRapport()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
    
		// voor data
		$this->pdf->widthB = array(40,35,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,35,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');

		$this->pdf->AddPage();
    $this->pdf->templateVars['OIBPaginas']=$this->pdf->page;

    $width=297;
    $height=210;
    $headerHeight=30;

    if($this->pdf->debug)
    {
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,0),'dash'=>"8,8"));
    $tMarge=$this->pdf->marge;
    $sMarge=$this->pdf->marge;
    $this->pdf->Line($sMarge,$tMarge,$tMarge,$height-$tMarge);//links
    $this->pdf->Line($width-$sMarge,$tMarge,$width-$tMarge,$height-$tMarge);//rechts
    $this->pdf->Line($width/2,$tMarge,$width/2,$height-$tMarge);//v-middel
    $this->pdf->Line(($width/2-$this->pdf->marge)/2+$width/2,$tMarge,($width/2-$this->pdf->marge)/2+$width/2,$height-$tMarge);//v-middel van midden rechts
    $this->pdf->Line(($width/2-$this->pdf->marge)/2+$sMarge,($height-$headerHeight-$tMarge)/2+$headerHeight,($width/2-$this->pdf->marge)/2+$sMarge,$height-$tMarge);//v-middel van midden links

    $this->pdf->Line($sMarge,$headerHeight,$width-$tMarge,$headerHeight);//header hoogte
    $this->pdf->Line($sMarge,($height-$headerHeight-$tMarge)/2+$headerHeight,$width-$tMarge,($height-$headerHeight-$tMarge)/2+$headerHeight);//h-midden
    $this->pdf->SetLineStyle(array('cap'=>'round','width'=>0.1,'color'=>array(0,0,0),'dash'=>0));
   

    }
    
    $lwb=(297/2)-$this->pdf->marge; //133.5
  		// haal totaalwaarde op om % te berekenen
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

		//getTypeGrafiekData($object,$type,$extraWhere='',$items=array())
		getTypeGrafiekData($this,'Beleggingscategorie');
		getTypeGrafiekData($this,'Valuta');
		getTypeGrafiekData($this,'Regio');


	  $this->pdf->setY($headerHeight);
		$this->pdf->setWidths(array(0,$lwb*0.3,$lwb*0.25,$lwb*0.2,$lwb*0.2,0.05));
		$this->pdf->setAligns(array('L','L','L','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

    
    $this->pdf->fillCell=array(0,1,1,1,1);
    $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
    $this->pdf->SetTextColor(255,255,255);
    $this->pdf->row(array('','Beleggingscategorie','Valutasoort','in EUR','in %'));
    $this->pdf->CellBorders = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaal=0;
    $totaalProcent=0;
    unset($this->pdf->fillCell);
    $this->pdf->SetFillColor(0,0,0);
    $this->pdf->SetTextColor(0,0,0);

  	foreach ($this->pdf->grafiekData['Beleggingscategorie']['port']['waarde'] as $categorie=>$waarde)
		{
		  $catData=array();
		  $query="SELECT valuta,valutaOmschrijving,SUM(actuelePortefeuilleWaardeEuro) as  actuelePortefeuilleWaardeEuro
		          FROM TijdelijkeRapportage
		          WHERE Beleggingscategorie='$categorie' AND rapportageDatum ='".$this->rapportageDatum."' AND portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek']."
		          GROUP BY valuta
		          ORDER BY valutaVolgorde";
		  $DB->SQL($query);
		  $DB->Query();
		  while($data=$DB->nextRecord())
         $catData[]=$data;

		  foreach ($catData as $row=>$data)
		  {
		    if($row==0)
		    {
		      $y=$this->pdf->getY();
		      //$this->pdf->SetFont($this->pdf->rapport_font,'BI',$this->pdf->rapport_fontsize);
		      $this->pdf->row(array('',$this->pdf->veldOmschrijvingen['Beleggingscategorie'][$categorie]));
		      //$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		      $this->pdf->setY($y);
		    }
        
        if($row==(count($catData)-1))
          $this->pdf->CellBorders = array('','U','U','U','U');
        else
          $this->pdf->CellBorders = array();
          
        
		    $this->pdf->row(array('','', $data['valutaOmschrijving'],$this->formatGetal($data['actuelePortefeuilleWaardeEuro'],0),$this->formatGetal($data['actuelePortefeuilleWaardeEuro']/$totaalWaarde*100,1)));

        if($row==(count($catData)-1))
        {
           $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
           $this->pdf->CellBorders = array();
		  		 $this->pdf->row(array('','','',$this->formatGetal($waarde,0),$this->formatGetal($this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie]*100,1)));
		  		 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		  		 
		  		 $this->pdf->ln();
        }
		  }
		  $totaal+=$waarde;
		  $totaalProcent+=$this->pdf->grafiekData['Beleggingscategorie']['port']['procent'][$categorie];
		}
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->CellBorders = array('','T','T','T','T');
    $this->pdf->row(array('','Totaal','',$this->formatGetal($totaal,0),$this->formatGetal($totaalProcent*100,1)));
    $this->pdf->CellBorders = array();

    $this->pdf->setXY(170,130);
	  //PieChart($this->pdf,50, 45, $this->pdf->grafiekData['Beleggingscategorie']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
    $this->pdf->ln(8);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);



$diameter = 35;
$hoek = 30;
$dikte = 10;
$Xas= 75;
$yas= 60;
$onderY=80;
$xRechts=150;

$tinten=array(1,0.5,0.7);
foreach($tinten as $tint)
{
$colors[]=array($this->pdf->blue[0]*$tint,$this->pdf->blue[1]*$tint,$this->pdf->blue[2]*$tint);
$colors[]=array($this->pdf->midblue[0]*$tint,$this->pdf->midblue[1]*$tint,$this->pdf->midblue[2]*$tint);//$this->pdf->midblue;
$colors[]=array($this->pdf->lightblue[0]*$tint,$this->pdf->lightblue[1]*$tint,$this->pdf->lightblue[2]*$tint);//$this->pdf->lightblue;
$colors[]=array($this->pdf->green[0]*$tint,$this->pdf->green[1]*$tint,$this->pdf->green[2]*$tint);//$this->pdf->green;
$colors[]=array($this->pdf->kopkleur[0]*$tint,$this->pdf->kopkleur[1]*$tint,$this->pdf->kopkleur[2]*$tint);//$this->pdf->kopkleur;
$colors[]=array($this->pdf->lightgreen[0]*$tint,$this->pdf->lightgreen[1]*$tint,$this->pdf->lightgreen[2]*$tint);//$this->pdf->lightgreen;
}

//listarray($this->pdf->grafiekData);exit;
foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
{
  $oibData[$omschrijving]=$waarde;
}

foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
{
  $oivData[$omschrijving]=$waarde;
}

foreach ($this->pdf->grafiekData['Regio']['grafiek'] as $omschrijving=>$waarde)
{
  $oirData[$omschrijving]=$waarde;
}
//listarray($this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);


//$this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas+$xRechts,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
//$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie");

$lwb=(297/2)-$this->pdf->marge; //133.5
$vwh=((210-$headerHeight-$this->pdf->marge)/2+$headerHeight)-$headerHeight;

$chartsize=55;
$this->pdf->setXY($this->pdf->marge+(($lwb/4)*5-$chartsize/2),$headerHeight);
//$legendaStart=array($this->pdf->marge+(($lwb/4)*7-$chartsize/2),$headerHeight+10);
$legendaStart=array($this->pdf->marge+(($lwb/4)*6),$headerHeight+10);
PieChart_L41($this->pdf,$chartsize,$vwh,$oibData,'%l',$colors,'Beleggingscategorie',$legendaStart);//'%l (%p)'

$this->pdf->setXY($this->pdf->marge+(($lwb/4)*5-$chartsize/2),$headerHeight+$vwh-10);
PieChart_L41($this->pdf,$chartsize,$vwh,$oivData,'%l',$colors,'Valuta');

$this->pdf->setXY($this->pdf->marge+(($lwb/4)*7-$chartsize/2),$headerHeight+$vwh-10);
PieChart_L41($this->pdf,$chartsize,$vwh,$oirData,'%l',$colors,'Regio');

//$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'],$Xas,$yas+$onderY,$this->pdf->grafiekData['Valuta']['grafiekKleur']);
//$this->pdf->Pie3D($grafiekData['OIV']['Percentage'],$this->pdf->grafiekData['Valuta']['grafiekKleur'],$Xas,$yas+$onderY,$diameter,$hoek,$dikte,"Valuta");

//$this->pdf->set3dLabels($grafiekData['OIR']['Omschrijving'],$Xas+$xRechts,$yas+$onderY,$this->pdf->grafiekData['Regio']['grafiekKleur']);
//$this->pdf->Pie3D($grafiekData['OIR']['Percentage'],$this->pdf->grafiekData['Regio']['grafiekKleur'],$Xas+$xRechts,$yas+$onderY,$diameter,$hoek,$dikte,"Regio");

  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>