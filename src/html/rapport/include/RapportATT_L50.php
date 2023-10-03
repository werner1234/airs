<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
File Versie					: $Revision: 1.5 $

$Log: RapportATT_L50.php,v $
Revision 1.5  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.4  2014/04/05 15:33:48  rvv
*** empty log message ***

Revision 1.3  2013/08/28 16:02:50  rvv
*** empty log message ***

Revision 1.2  2013/08/24 15:48:47  rvv
*** empty log message ***

Revision 1.1  2013/06/30 15:07:33  rvv
*** empty log message ***

Revision 1.9  2012/09/16 12:45:46  rvv
*** empty log message ***

Revision 1.8  2012/05/12 15:11:00  rvv
*** empty log message ***

Revision 1.7  2012/04/14 16:51:17  rvv
*** empty log message ***

Revision 1.6  2012/03/28 15:55:19  rvv
*** empty log message ***

Revision 1.5  2012/03/25 13:27:46  rvv
*** empty log message ***

Revision 1.4  2012/03/21 19:08:58  rvv
*** empty log message ***

Revision 1.3  2012/03/18 16:08:24  rvv
*** empty log message ***

Revision 1.2  2012/03/11 17:19:57  rvv
*** empty log message ***

Revision 1.1  2012/03/04 11:39:58  rvv
*** empty log message ***

Revision 1.1  2012/02/29 16:52:49  rvv
*** empty log message ***

Revision 1.1  2012/02/26 15:17:43  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L50.php");
include_once("rapport/include/RapportOIB_L50.php");

include_once("rapport/ATTberekening2.php");

class RapportATT_L50
{
	function RapportATT_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);

		$this->pdf->rapport_titel = "Rendement per beleggingscategorie afgezet tegen benchmark";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		$this->oib = new RapportOIB_L50($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function writeRapport()
	{
		$DB = new DB();
		global $__appvar;
		//$this->pdf->AddPage();
    $query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();


		$this->oib->getOIBdata();
		$this->oib->hoofdcategorien['geen-Hcat']='geen-Hcat';
		$oibData=$this->oib->hoofdCatogorieData;
		$oibData['totaal']['port']['procent']=1;

    if($this->pdf->rapport_datum > db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
	  $this->tweedePerformanceStart=$rapportageStartJaar;
	//  $att=new ATTberekening2($this);
  //  $waarden=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
  //  listarray($waarden);

    $att=new ATTberekening_L50($this);
    $att->indexPerformance=true;
    $this->waarden['Historie']=$att->bereken(substr($this->rapportageDatumVanaf,0,10),  $this->rapportageDatum,'EUR','hoofdcategorie');

    $typen=array('procent','indexPerf'); //,'bijdrage'

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
     $this->jaarTotalen[$categorie]=array();

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
    {   // listarray($categorieData);
      $laatste=array();

       foreach ($categorieData['perfWaarden'] as $datum=>$waarden)
      {
        $jaar=substr($datum,0,4);
        $this->jaarTotalen[$categorie][$jaar]['resultaat']+=$waarden['resultaat'];
        foreach ($typen as $type)
        {
          $this->jaarTotalen[$categorie][$jaar][$type]=((1+$waarden[$type])*(1+$laatste[$jaar][$type])-1);
          $laatste[$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type];
        }

        if($categorie!='totaal')
        {
          $this->jaarTotalen[$categorie][$jaar]['allocateEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];
         // echo $this->jaarTotalen[$categorie][$jaar]['allocateEffect']."+=(".$waarden['weging']."-".$waarden['indexBijdrageWaarde'].")*".$waarden['indexPerf']."<br>\n";
          $this->jaarTotalen[$categorie][$jaar]['selectieEffect']+=($waarden['procent']-$waarden['indexPerf'])*$waarden['weging'];

          $this->jaarTotalen['totaal'][$jaar]['allocateEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];

        }

         $this->jaarTotalen[$categorie][$jaar]['portBijdrage']+=$waarden['bijdrage'];


           // $this->formatGetal($this->waarden['Periode'][$categorie]['bijdrage'],2),
      }
    }



     $startJaar=date("Y",$this->pdf->rapport_datum);



    $this->oib->hoofdcategorien['totaal']="Totaal";
    $this->pdf->rapport_titel = "Performance en attributie-overzicht per beleggingscategorie en totaal";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
    $this->pdf->SetWidths(array(40,30,30,30,30,30,30,30));
   	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
   	$this->pdf->ln(5);
   	$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array("","Tactische\nWeging","Strategische\nWeging","Rendement\nPortefeuille","Ontwikkeling\nbenchmark","Allocatie\neffect","Selectie\neffect",'Attributie'));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln();
   foreach ($this->jaarTotalen as $categorie=>$jaarWaarden)
    {

      $waarden=$jaarWaarden[$startJaar];
      $this->pdf->row(array($this->oib->hoofdcategorien[$categorie],$this->formatGetal(($oibData[$categorie]['port']['procent'])*100,1),$this->formatGetal($att->normData[$categorie],1),
      $this->formatGetal($waarden['procent']*100,2),$this->formatGetal($waarden['indexPerf']*100,2),
      $this->formatGetal($waarden['allocateEffect']*100,2),
      $this->formatGetal((($waarden['procent']-$waarden['indexPerf'])-$waarden['allocateEffect'])*100,2),
      $this->formatGetal(($waarden['procent']-$waarden['indexPerf'])*100,2)));
      $this->pdf->ln(5);

      $grafiekWaarden[$this->oib->hoofdcategorien[$categorie]]=array($waarden['allocateEffect']*100,
        (($waarden['procent']-$waarden['indexPerf'])-$waarden['allocateEffect'])*100,
        ($waarden['procent']-$waarden['indexPerf'])*100);
    }
    
    $x=25;	 
 	  unset($grafiekWaarden['geen-Hcat']);	 
    unset($grafiekWaarden['Liquiditeiten']);
    foreach ($grafiekWaarden as $cat=>$waarden)	 
     {	 
       $this->pdf->SetXY($x,180-60);	 
       $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);	 
       $this->pdf->Cell(50, 4,$cat,0,0,'C');	 
       $this->pdf->SetXY($x,180);	 
       $this->VBarDiagram(50,50,$waarden,'');	 
       $x+=70;	 
     }	 
 	 
     $ystep=10;	 
     $x=230;	 
     $y=140;	 
     $legenda=array('Allocatie effect'=>array(87,165,25),'Selectie effect'=>array(255,0,59),'Totaal'=>array(0,52,121));	 
      $this->pdf->SetFont($this->pdf->rapport_font, '', 8);	 
     foreach ($legenda as $omschrijving=>$color)	 
     {	 
       $this->pdf->setXY($x,$y);	 
       $this->pdf->Rect($x-5, $y, 4, 4, 'DF',null,$color);	 
       $this->pdf->Cell(100, 4, $omschrijving,0,0,'L');	 
       $y+=$ystep;	 
     }



    $this->pdf->SetFillColor(255,255,255);
    $this->pdf->rapport_titel = "Stortingen, onttrekkingen, inkomsten en uitgaven";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ATT2Paginas']=$this->pdf->page;


  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

/*
     $att=new ATTberekening_L50($this);
    $att->indexPerformance=true;
    $indexData=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'EUR','hoofdcategorie');
*/

      $this->pdf->widthA = array(26,26,24,25,25,20,20,25,25,25,23,23);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R');

		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		for($i=0;$i<count($this->pdf->widthA);$i++)
		  $this->pdf->fillCell[] = 1;

		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

    $this->pdf->ln();
		$this->pdf->row(array("Maand\n ",
		                      "Beginvermogen\n ",
		                      "Stortingen en\nonttrekkingen",
		                      "Gerealiseerd\nresultaat",
		                      "Ongerealiseerd\nresultaat",
		                      "Inkomsten\n ",
		                      "Kosten\n ",
		                      "Opgelopen\nrente\n ",
		                      "Beleggings\nresultaat",
		                     	"Eindvermogen\n "));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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
      $barGraph['Index'][$data['datum']][$categorie] = $waarde/$data['waardeHuidige']*100;
    }
  }
}


$grafiekData['Datum'][]="$RapStartJaar-12-01";

   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     //   $this->pdf->CellBorders = array('','US','US','US','US','US','US','US','US','US','US','US');
        $this->pdf->underlinePercentage=0.8;

       //$this->pdf->SetFillColor(230,230,230);
        $this->pdf->SetFillColor(240,240,240);
        $totaalRendament=100;
        $totaalRendamentIndex=100;
		    foreach ($rendamentWaarden as $row)
		    {
		      //listarray($row);
		      $resultaat = $row['Opbrengsten']-$row['Kosten'];
		      $datum = db2jul($row['datum']);

		      if($fill==true)
		      {
		        $//this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],0),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],0),
		                           $this->formatGetal($row['gerealiseerd'],0),
		                           $this->formatGetal($row['ongerealiseerd'],0),
		                           $this->formatGetal($row['opbrengsten'],0),
		                           $this->formatGetal($row['kosten'],0),
		                           $this->formatGetal($row['rente'],0),
		                           $this->formatGetal($row['resultaatVerslagperiode'],0),
		                           $this->formatGetal($row['waardeHuidige'],0)));
                               
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
        $this->pdf->CellBorders = array('','UU','UU','UU','UU','UU','UU','UU','UU','UU','','UU');
		    $this->pdf->row(array('Cumulatief',
		                           $this->formatGetal($waardeBegin,0),
		                           $this->formatGetal($totaalStortingenOntrekkingen,0),
		                           $this->formatGetal($totaalGerealiseerd,0),
		                           $this->formatGetal($totaalOngerealiseerd,0),
		                           $this->formatGetal($totaalOpbrengsten,0),
		                           $this->formatGetal($totaalKosten,0),
		                           $this->formatGetal($totaalRente,0),
		                           $this->formatGetal($totaalResultaat,0),
		                           $this->formatGetal($totaalWaarde,0),

		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		                           	    $this->pdf->CellBorders = array();

		  }


	}




  function VBarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

$this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'FD','',array(245,245,245));

      if($color == null)
          $color=array(155,155,155);
      if ($maxVal == 0)
        $maxVal = ceil(max($data));
      $minVal = floor(min($data));

      $minVal = $minVal * 1.1;
      $maxVal = $maxVal * 1.2;

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

      $stapgrootte = ceil(abs($bereik)/$horDiv);
      $top = $YstartGrafiek-$h;
      $bodem = $YstartGrafiek;
      $absUnit =abs($unit);

      $nulpunt = $YstartGrafiek + $nulYpos;
      $n=0;

      for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
      {
        $skipNull = true;
        $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
        $this->pdf->Text($XstartGrafiek-7, $i, $n*$stapgrootte." %");
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

      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

        $colors=array(array(87,165,25),array(255,0,59),array(0,52,121));

      $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
      $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $index=>$val)
      {

        $color=$colors[$index];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
      }



     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }
}
?>