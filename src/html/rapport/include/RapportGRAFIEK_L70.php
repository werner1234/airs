<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/07/15 16:38:19 $
File Versie					: $Revision: 1.5 $

$Log: RapportGRAFIEK_L70.php,v $
Revision 1.5  2020/07/15 16:38:19  rvv
*** empty log message ***

Revision 1.4  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.3  2016/06/12 10:20:31  rvv
*** empty log message ***

Revision 1.2  2016/06/08 15:40:53  rvv
*** empty log message ***

Revision 1.1  2016/05/22 18:49:26  rvv
*** empty log message ***

Revision 1.9  2015/04/19 08:35:38  rvv
*** empty log message ***

Revision 1.8  2015/04/01 16:00:45  rvv
*** empty log message ***

Revision 1.7  2015/03/29 07:43:24  rvv
*** empty log message ***

Revision 1.6  2015/02/09 18:32:44  rvv
*** empty log message ***

Revision 1.5  2015/01/31 20:02:23  rvv
*** empty log message ***

Revision 1.4  2015/01/14 20:18:36  rvv
*** empty log message ***

Revision 1.3  2014/12/20 16:32:36  rvv
*** empty log message ***

Revision 1.2  2014/11/23 14:13:22  rvv
*** empty log message ***

Revision 1.1  2014/11/08 18:37:31  rvv
*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L70.php");
//include_once("rapport/include/ATTberekening_L35.php");

//include_once("rapport/ATTberekening2.php");

class RapportGRAFIEK_L70
{
	function RapportGRAFIEK_L70($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "RISK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->excelData 	= array();

		$this->pdf->rapport_titel = "Rendement per beleggingscategorie afgezet tegen benchmark";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
   
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
    $query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();

    if(db2jul(date("Y-01-01",$this->pdf->rapport_datum)) < db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
	  else
	   	$rapportageStartJaar=date("Y-01-01",$this->pdf->rapport_datum);
  
    $rapportageStartJaar=$this->rapportageDatumVanaf;
	  $this->tweedePerformanceStart=$rapportageStartJaar;

    $att=new ATTberekening_L70($this);
    $att->indexPerformance=true;
    $att->indexNormaleWeging=true;
    //$this->waarden['Historie']=$att->bereken(substr($this->pdf->PortefeuilleStartdatum,0,10),  $this->rapportageDatum,'EUR','hoofdcategorie');
    $this->waarden['Historie']=$att->bereken($rapportageStartJaar,  $this->rapportageDatum,'EUR','zorgplichtcategorie','maanden');

    if($this->pdf->debug==true)
    {
      //listarray($this->waarden['Historie']['totaal']);
       $this->pdf->excelData[]=array('Totaal categorie'); 
      $this->pdf->excelData[]=array('Datum','PortefeuillePerf','IndexPerf'); 
      foreach($this->waarden['Historie'] as $categorie=>$categorieData)
      {
        //if($categorie <> 'totaal')
        //{
          $this->pdf->excelData[]=array($categorie);
          foreach($categorieData['perfWaarden'] as $datum=>$perfData)
            $this->pdf->excelData[]=array($datum,$perfData['procent'],$perfData['indexPerf']); 
        
          $this->pdf->excelData[]=array('');
        // }
      } 
    } 
    $stapelTypen=array('procentBruto','indexPerf'); //,'bijdrage'
    $somTypen=array();
    $gemiddeldeTypen=array('weging');

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
     $this->jaarTotalen[$categorie]=array();

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
    { 
      $laatste=array();
      
      if($lastCategorie <> '')
      {
        $this->pdf->excelData[]=array('Totaal',
           $this->jaarTotalen[$lastCategorie][$jaar]['procent'],'','',
           $this->jaarTotalen[$lastCategorie][$jaar]['indexPerf'],
           $this->jaarTotalen[$lastCategorie][$jaar]['procent']- $this->jaarTotalen[$lastCategorie][$jaar]['indexPerf'],
          $this->jaarTotalen[$lastCategorie][$jaar]['allocatieEffect'],
          ( $this->jaarTotalen[$lastCategorie][$jaar]['procent']- $this->jaarTotalen[$lastCategorie][$jaar]['indexPerf'])-$this->jaarTotalen[$lastCategorie][$jaar]['allocatieEffect']
          );
          
        
      }

       $this->pdf->excelData[]=array($categorie);
       $this->pdf->excelData[]=array('datum','Performance','weging','indexBijdrageWaarde','indexPerf','Attributie',
       'allocatieEffect','SellectieEffect','interactieEffect');
       foreach ($categorieData['perfWaarden'] as $datum=>$waarden)
      {
        $jaar=substr($datum,0,4);
        $this->jaarTotalen[$categorie][$jaar]['resultaat']+=$waarden['resultaat'];
        foreach ($stapelTypen as $type)
        {
          $this->jaarTotalen[$categorie][$jaar][$type]=((1+$waarden[$type])*(1+$laatste[$jaar][$type])-1);
          $laatste[$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type];
        }
        foreach ($somTypen as $type)
        {
          $this->jaarTotalen[$categorie][$jaar][$type]+=$waarden[$type];
        }
        foreach ($gemiddeldeTypen as $type)
          $this->jaarTotalen[$categorie][$jaar][$type]+=$waarden[$type];
        
        if($categorie!='totaal')
        { 
         // echo $categorie."<br>\n";
         // listarray($waarden); 
          $attributieEffect=(($waarden['weging']*$waarden['procentBruto'])-($waarden['indexPerf']*$waarden['indexBijdrageWaarde']))*100;
          //$attributieEffect=($waarden['procentBruto']-$waarden['indexPerf'])*100;
          $allocatieEffect=($waarden['weging']-$waarden['indexBijdrageWaarde'])*($waarden['indexPerf'])*100;
          $selectieEffect=($waarden['procentBruto']-$waarden['indexPerf'])*$waarden['indexBijdrageWaarde']*100;
          $interactieEffect=($waarden['weging']-$waarden['indexBijdrageWaarde'])*($waarden['procentBruto']-$waarden['indexPerf'])*100;
          
          
      //  echo " $attributieEffect=((".$waarden['weging']."*".$waarden['procentBruto'].")-(".$waarden['indexPerf']."*".$waarden['indexBijdrageWaarde']."))*100 <br>\n";
        // echo "a: $attributieEffect ";
       //   $attributieEffect=$allocatieEffect+$selectieEffect+$interactieEffect;
        //  echo "b: $attributieEffect  $categorie $datum<br>\n"; ob_flush();
          $this->maandTotalen[$datum]['attributieEffect']+=$attributieEffect;
          $this->maandTotalen[$datum]['allocatieEffect']+=$allocatieEffect;
          $this->maandTotalen[$datum]['selectieEffect']+=$selectieEffect;
          $this->maandTotalen[$datum]['interactieEffect']+=$interactieEffect;

          $this->pdf->excelData[]=array($datum,
            $waarden['procentBruto'],
            $waarden['weging'],
            $waarden['indexBijdrageWaarde'],
            $waarden['indexPerf'],
            $this->maandTotalen[$datum]['attributieEffect'],
            $this->maandTotalen[$datum]['allocatieEffect'],
            $this->maandTotalen[$datum]['selectieEffect'],
            $this->maandTotalen[$datum]['interactieEffect']); 
            
          $this->jaarTotalen[$categorie][$jaar]['portBijdrage']+=$waarden['bijdrage'];
          $this->jaarTotalen[$categorie][$jaar]['attributieEffect']+=$attributieEffect;
          $this->jaarTotalen[$categorie][$jaar]['allocatieEffect']+=$allocatieEffect;
          $this->jaarTotalen[$categorie][$jaar]['selectieEffect']+=$selectieEffect;
          $this->jaarTotalen[$categorie][$jaar]['interactieEffect']+=$interactieEffect;
                    
          $this->jaarTotalen['totaal'][$jaar]['portBijdrage']+=$waarden['bijdrage'];
          $this->jaarTotalen['totaal'][$jaar]['attributieEffect']+=$attributieEffect;
          $this->jaarTotalen['totaal'][$jaar]['allocatieEffect']+=$allocatieEffect;
          $this->jaarTotalen['totaal'][$jaar]['selectieEffect']+=$selectieEffect;
          $this->jaarTotalen['totaal'][$jaar]['interactieEffect']+=$interactieEffect;
          
        }



        $lastCategorie=$categorie;
           // $this->formatGetal($this->waarden['Periode'][$categorie]['bijdrage'],2),
      }
      foreach ($gemiddeldeTypen as $type)
        $this->jaarTotalen[$categorie][$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type]/count($categorieData['perfWaarden']);
    }
   //   listarray($this->maandTotalen);exit;
//listarray($this->jaarTotalen);
/*
   $this->jaarTotalen[$categorie][$jaar]['allocatieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];
   $this->jaarTotalen[$categorie][$jaar]['selectieEffect']+=($waarden['procent']-$waarden['indexPerf'])*$waarden['indexBijdrageWaarde'];
   $this->jaarTotalen[$categorie][$jaar]['interactieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*($waarden['procent']-$waarden['indexPerf'])*100;
   $this->jaarTotalen['totaal'][$jaar]['allocatieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];//wordt gebruikt
*/

    $startJaar=date("Y",$this->pdf->rapport_datum);
    $this->oib->hoofdcategorien['totaal']="Totaal";
    $this->pdf->rapport_titel = "Performance en attributie-overzicht per beleggingscategorie en totaal";
    $this->pdf->AddPage();
    $this->pdf->templateVars['GRAFIEKPaginas']=$this->pdf->page;
    $this->pdf->templateVarsOmschrijving['GRAFIEKPaginas'] = $this->pdf->rapport_titel;
    
    $this->pdf->SetWidths(array(38.1429,28.6071,28.6071,28.6071,28.6071,28.6071,28.6071,28.6071,28.6071));
   	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
   	$this->pdf->ln(5);
   	$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->CellBorders=array('U','U','U','U','U','U','U','U','U');
    $this->pdf->row(array("","Tactische\nWeging","Strategische\nWeging","Rendement\nPortefeuille","Ontwikkeling\nbenchmark",'Attributie',"Allocatie\neffect","Selectie\neffect","Interactie\neffect"));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    unset($this->pdf->CellBorders);
    $this->pdf->ln();

$DB=new DB();
    /*
$query="SELECT Beleggingscategorie,Omschrijving FROM Beleggingscategorien";
$DB->SQL($query);
$DB->Query();
while($data=$DB->nextRecord())
  $categorieLookup[$data['Beleggingscategorie']]=$data['Omschrijving'];
  $categorieLookup['geen-Hcat']='Geen hoofdcategorie';
    */
    $query="SELECT Zorgplicht,Omschrijving FROM Zorgplichtcategorien ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
      $categorieLookup[$data['Zorgplicht']]=$data['Omschrijving'];
    $categorieLookup['geen-zorgplicht']='Geen zorgplichtcategorie';


   foreach ($this->jaarTotalen as $categorie=>$jaarWaarden)
    {

      $waarden=$jaarWaarden[$startJaar];
      $this->pdf->row(array($categorieLookup[$categorie],
      $this->formatGetal($waarden['weging']*100,1), // . ' '.$this->formatGetal(($oibData[$categorie]['port']['procent'])*100,1)
      $this->formatGetal($att->normData[$categorie],1),
      $this->formatGetal($waarden['procentBruto']*100,2),
      $this->formatGetal($waarden['indexPerf']*100,2),
      $this->formatGetal($waarden['attributieEffect'] ,2),
      $this->formatGetal($waarden['allocatieEffect'],2),
      $this->formatGetal($waarden['selectieEffect'],2),
      $this->formatGetal($waarden['interactieEffect'],2)));
      $this->pdf->ln(5);


    }
 
      //$this->pdf->rapport_titel = "Maandelijkse attributie-effecten";
     // $this->pdf->AddPage();
      $this->pdf->setXY(15,182);
      $barData=array();
      foreach($this->maandTotalen as $maand=>$waarden)
      {
        unset($waarden['attributieEffect']);
        $barData[$maand]=$waarden;
      }
      $this->VBarDiagram2(130,137-50,$barData,'');
      $colors=array('allocatie effect'=>array(151,185,199),'selectie effect'=>array(154,178,148),'interactie effect'=>array(127,127,127)); //
      $xval=25;$yval=185;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3, $effect,0,0,'L');
         $xval+=40;
      }
     // $this->maandTotalenCumulatief=array('attributieEffect'=>array(),'allocatieEffect'=>array(),'selectieEffect'=>array(),'interactieEffect'=>array());
      foreach($this->maandTotalen as $maand=>$maandWaarden)
        foreach($maandWaarden as $type=>$waarde)
        {
          $tmp[$type]+=$waarde;
          $this->maandTotalenCumulatief[$type][$maand]=$tmp[$type];
        }
        
      $colors=array('attributie effect'=>array(241,89,38),'allocatie effect'=>array(151,185,199),'selectie effect'=>array(154,178,148),'interactie effect'=>array(127,127,127)); //
      $this->LineDiagram(160,50+50,120,120-50,$this->maandTotalenCumulatief,'');
      $xval=165;$yval=185;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(50, 3, $effect,0,0,'L');
         $xval+=30;
      }
      
      
  
/*

    $this->pdf->SetFillColor(255,255,255);
    $this->pdf->rapport_titel = "Stortingen, onttrekkingen, inkomsten en uitgaven";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ATT2Paginas']=$this->pdf->page;

    $index=new indexHerberekening();
    $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);

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
		        //$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1,1,1);
		        $fill=false;
		      }
		      else
		      {
		        $this->pdf->fillCell=array();
		         $fill=true;
		      }
		      $this->pdf->row(array(date("Y",$datum).' '.vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal) ,
		                           $this->formatGetal($row['waardeBegin'],2),
		                           $this->formatGetal($row['stortingen']-$row['onttrekkingen'],2),
		                           $this->formatGetal($row['gerealiseerd'],2),
		                           $this->formatGetal($row['ongerealiseerd'],2),
		                           $this->formatGetal($row['opbrengsten'],2),
		                           $this->formatGetal($row['kosten'],2),
		                           $this->formatGetal($row['rente'],2),
		                           $this->formatGetal($row['resultaatVerslagperiode'],2),
		                           $this->formatGetal($row['waardeHuidige'],2)));
                               
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
		                           $this->formatGetal($waardeBegin,2),
		                           $this->formatGetal($totaalStortingenOntrekkingen,2),
		                           $this->formatGetal($totaalGerealiseerd,2),
		                           $this->formatGetal($totaalOngerealiseerd,2),
		                           $this->formatGetal($totaalOpbrengsten,2),
		                           $this->formatGetal($totaalKosten,2),
		                           $this->formatGetal($totaalRente,2),
		                           $this->formatGetal($totaalResultaat,2),
		                           $this->formatGetal($totaalWaarde,2),

		                           ));//$this->formatGetal($totaalRendamentIndex-100,2)
		                           	    $this->pdf->CellBorders = array();

		  }
      
*/
	}
  
  
function LineDiagram($x,$y,$w, $h, $data, $title,$color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $this->pdf->Rect($x-10,$y-5,$w+15,$h+15);
    $this->pdf->setXY($x,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4,$title,'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    


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
      $color=array(0,0,0);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

     $maanden=array();
      $maxVal=0;
      $minVal=0;
      foreach($data as $type=>$maandData)
      {
        
        $tmp=count($maandData);
        if($tmp > $aantalMaanden)
          $aantalMaanden=$tmp;
        foreach($maandData as $maand=>$waarde)
        {
          $maanden[$maand]=$maand;
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)  
            $minVal = $waarde;
        }
      }

    $minVal = floor(($minVal-1) * 1.1);
    $maxVal = ceil(($maxVal+1) * 1.1);
    $legendYstep = ($maxVal - $minVal) / $horDiv;
    $verInterval = ($lDiag / $verDiv);
    $horInterval = ($hDiag / $horDiv);
    $waardeCorrectie = $hDiag / ($maxVal - $minVal);
    $unit = $lDiag / $aantalMaanden;

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

    $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
    $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
  
   // $color=array(200,0,0);

  $colors=array('attributieEffect'=>array(241,89,38),'allocatieEffect'=>array(151,185,199),'selectieEffect'=>array(154,178,148),'interactieEffect'=>array(127,127,127)); //
 
    //for ($i=0; $i<count($data); $i++)
    $maandPrinted=array();
    foreach($data as $type=>$maandData)
    {
      $i=0;
      $color=$colors[$type];
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($maandData as $maand=>$waarde)
      {
        //foreach($maandData as $line)
       // $extrax=($unit*0.1*-1);
        
       //   $extrax1=($unit*0.1*-1);
        

        $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8,$legendDatum[$i],0);

        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        
        if($i <> -1)
        {
          $this->pdf->line($XDiag+$i*$unit+$extrax1, $yval, $XDiag+($i+1)*$unit+$extrax, $yval2,$lineStyle );
        }
        $this->pdf->Rect($XDiag+($i+1)*$unit-0.5+$extrax, $yval2-0.5, 1, 1 ,'F','',$color);
        
        if($waarde <> 0)
          $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$yval2-2.5,$this->formatGetal($waarde,1));
          $yval = $yval2;
        
      
        if(!isset($maandPrinted[$maand]))
        {
          $maandPrinted[$maand]=1;
          $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$bodem+5,date('M',db2jul($maand)));
          
        }
        
        $i++;
        
        
      }
    }
    $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
    $this->pdf->SetFillColor(0,0,0);
  }

  function VBarDiagram2($w, $h, $data, $format, $color=null,$nbDiv=4,$numBars=0)
  {
      global $__appvar;
      $legendDatum = $data['datum'];
      //$data = $data['portefeuille'];
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->SetLegends($data,$format);

      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);

      $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
      if($color == null)
          $color=array(155,155,155);
      
      $maxVal=0;
      $minVal=0;
      $maanden=array();
      foreach($data as $maand=>$maandData)
      {
        $maanden[$maand]=$maand;
        foreach($maandData as $type=>$waarde)
        {
          if($waarde > $maxVal)
            $maxVal = $waarde;
          if($waarde < $minVal)  
            $minVal = $waarde;
        }
      }
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
      
      $numBars=count($data);
      if($numBars > 0)
        $this->pdf->NbVal=$numBars;

     $colors=array('attributieEffect'=>array(241,89,38),'allocatieEffect'=>array(151,185,199),'selectieEffect'=>array(154,178,148),'interactieEffect'=>array(127,127,127)); //
 

      $vBar = ($bGrafiek / ($this->pdf->NbVal ))/4; //3
      $bGrafiek = $vBar * ($this->pdf->NbVal );
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      foreach($data as $maand=>$maandData)
      {
        
        foreach($maandData as $type=>$val)
        {
          $color=$colors[$type];
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiek + $nulYpos;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$color);
          $this->pdf->SetTextColor(255,255,255);
          if(abs($hval) > 3 && $eBaton > 4)
          {
            $this->pdf->SetXY($xval, $yval+($hval/2)-2);
            $this->pdf->Cell($eBaton, 4, number_format($val,1,',','.')."%",0,0,'C');
          }
         $this->pdf->SetTextColor(0,0,0);
          $i++;
          }
          $i++;
          

          $this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2,$YstartGrafiek +3 ,date('M',db2jul($maand)));
          
      }



     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
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