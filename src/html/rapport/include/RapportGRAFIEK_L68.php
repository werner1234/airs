<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/09/28 17:20:17 $
File Versie					: $Revision: 1.12 $

$Log: RapportGRAFIEK_L68.php,v $
Revision 1.12  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.11  2019/09/14 17:09:05  rvv
*** empty log message ***

Revision 1.10  2019/03/27 16:20:18  rvv
*** empty log message ***

Revision 1.9  2019/03/23 17:05:54  rvv
*** empty log message ***

Revision 1.8  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.7  2019/01/23 16:27:16  rvv
*** empty log message ***

Revision 1.6  2019/01/19 13:54:10  rvv
*** empty log message ***

Revision 1.5  2018/12/21 17:49:26  rvv
*** empty log message ***

Revision 1.4  2018/12/16 15:43:57  rvv
*** empty log message ***

Revision 1.3  2018/12/15 17:49:14  rvv
*** empty log message ***

Revision 1.2  2018/12/05 16:36:17  rvv
*** empty log message ***

Revision 1.1  2018/11/21 16:48:32  rvv
*** empty log message ***

Revision 1.28  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.27  2018/02/22 07:45:39  rvv
*** empty log message ***

Revision 1.26  2018/02/21 17:15:09  rvv
*** empty log message ***

Revision 1.25  2018/02/19 07:16:26  rvv
*** empty log message ***

Revision 1.24  2018/02/17 19:18:57  rvv
*** empty log message ***

Revision 1.23  2018/02/14 16:53:20  rvv
*** empty log message ***

Revision 1.22  2018/01/27 17:31:22  rvv
*** empty log message ***

Revision 1.21  2018/01/13 19:10:28  rvv
*** empty log message ***

Revision 1.20  2016/04/20 15:46:31  rvv
*** empty log message ***

Revision 1.19  2015/07/22 13:16:13  rvv
*** empty log message ***

Revision 1.18  2014/04/05 15:33:48  rvv
*** empty log message ***

Revision 1.17  2014/03/26 18:26:15  rvv
*** empty log message ***

Revision 1.16  2014/03/01 14:01:38  rvv
*** empty log message ***

Revision 1.15  2014/02/12 15:55:51  rvv
*** empty log message ***

Revision 1.14  2014/02/08 17:42:08  rvv
*** empty log message ***

Revision 1.13  2014/02/02 10:49:59  rvv
*** empty log message ***

Revision 1.12  2013/12/23 16:43:01  rvv
*** empty log message ***

Revision 1.11  2013/12/18 17:10:42  rvv
*** empty log message ***

Revision 1.10  2013/10/19 15:57:25  rvv
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
include_once("rapport/include/RapportPERF_L68.php");
//include_once("rapport/ATTberekening2.php");

class RapportGRAFIEK_L68
{
	function RapportGRAFIEK_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->perf = new RapportPERF_L68($this->pdf,$portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
    
    $this->pdf->rapport_type = "GRAFIEK";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->excelData 	= array();

		$this->pdf->rapport_titel = "Rendement per beleggingscategorie afgezet tegen benchmark";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
		//$this->oib = new RapportOIB_L35($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum);
    $this->pdf->rapport_type = "GRAFIEK";
    
    $this->rapport=&$this;
    $this->rapport_datumvanaf=db2jul($this->rapport->rapportageDatumVanaf);
    $this->rapport_datum=db2jul($this->rapport->rapportageDatum);
    $this->rapport_jaar  =date('Y',$this->rapport_datum);
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

    $this->pdf->rapport_titel = "Attributie-analyse";
    $this->pdf->templateVars['GRAFIEKPaginas']=$this->pdf->page+1;
    $this->pdf->templateVarsOmschrijving['GRAFIEKPaginas'] = $this->pdf->rapport_titel;

		//$this->pdf->AddPage();
    $query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();



    if(db2jul($this->rapportageDatumVanaf) > db2jul($portefeuilledata['startDatum']))
	   	$rapportageStartJaar= $this->rapportageDatumVanaf;//date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($portefeuilledata['startDatum'],0,10);
	  $this->tweedePerformanceStart=$rapportageStartJaar;

    //$att=new ATTberekening_L35($this);
    $this->indexPerformance=true;
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
      $hpiGebruik=true;
    else
      $hpiGebruik=false;
    
    $this->att=new ATTberekening_L68($this);
    $this->att->indexPerformance=false;
    if($this->pdf->lastPOST['perfPstart']==1)
    {
     // $this->waarden['Historie'] = $this->bereken(substr($this->pdf->PortefeuilleStartdatum, 0, 10), $this->rapportageDatum, $this->pdf->rapportageValuta, 'hoofdcategorie');
      $this->waarden['Historie'] = $this->att->bereken(substr($this->pdf->PortefeuilleStartdatum, 0, 10),$this->rapportageDatum,'Hoofdcategorie',$hpiGebruik);

    }
    else
    {
    //  $this->waarden['Historie'] = $this->bereken($rapportageStartJaar,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
      $this->waarden['Historie'] = $this->att->bereken($rapportageStartJaar,$this->rapportageDatum,'Hoofdcategorie',$hpiGebruik);
    }

//listarray($this->waarden['Historie']);




    if($this->pdf->debug==true)
    {
      //listarray($this->waarden['Historie']['totaal']);
       $this->pdf->excelData[]=array('Totaal categorie'); 
      $this->pdf->excelData[]=array('Datum','PortefeuillePerf','IndexPerf'); 

    } 
    $stapelTypen=array('procent'); //,'bijdrage'
    $somTypen=array('indexPerf');
    $gemiddeldeTypen=array('weging');

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
     $this->jaarTotalen[$categorie]=array();
    
    /*
    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
    {
      foreach($categorieData['perfWaarden'] as $maand=>$maandData)
      {
        echo "$categorie $maand ".$maandData['procent']."<br>\n";
      }
    }
    echo "<br>\n";
    */
    $maandDetails=array();
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
       'allocatieEffect (weging-indexBijdrageWaarde)*indexPerf','SellectieEffect Totaal(Performance-indexPerf)-allocatieEffect');
      foreach ($categorieData['perfWaarden'] as $datum=>$waarden)
      {
        $waarden['indexBijdrageWaarde']=$this->waarden['Historie']['totaal']['perfWaarden'][$datum]['planTotalen'][$categorie];
        if($categorie=='VAR')
          $waarden['indexBijdrageWaarde']+=$this->waarden['Historie']['totaal']['perfWaarden'][$datum]['planTotalen']['Liquiditeiten'];
        
       // listarray($this->waarden['Historie']['totaal']['perfWaarden'][$datum]['planTotalen']);
        $jaar=substr($datum,0,2);
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
          $this->maandTotalen[$datum]['allocatieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf']*100;
          //echo  "$categorie ". $this->maandTotalen[$datum]['allocatieEffect']."+=(".$waarden['weging']."-".$waarden['indexBijdrageWaarde'].")*".$waarden['indexPerf']."*100 <br>\n";
       
          $this->maandCumulatief[$datum]['allocatieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];
          //echo $categorie;listarray($waarden);
          $this->jaarTotalen['totaal'][$jaar]['allocatieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];//wordt gebruikt
       //   echo " (".$waarden['weging']."-".$waarden['indexBijdrageWaarde'].")*".$waarden['indexPerf']."<br>\n"; ob_flush();
          $this->jaarTotalen[$categorie][$jaar]['allocatieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf'];
  
  
          $maandDetails[$datum][$categorie]['allocatieEffect']+=($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf']*100;
          $maandDetails[$datum][$categorie]['weging']=$waarden['weging'];
          $maandDetails[$datum][$categorie]['indexBijdrageWaarde']=$waarden['indexBijdrageWaarde'];
          $maandDetails[$datum][$categorie]['procent']=$waarden['procent'];
          $maandDetails[$datum][$categorie]['indexPerf']=$waarden['indexPerf'];
          
          $this->pdf->excelData[]=array($datum,
            $waarden['procent'],
            $waarden['weging'],
            $waarden['indexBijdrageWaarde'],
            $waarden['indexPerf'],
            $waarden['procent']-$waarden['indexPerf'],
            ($waarden['weging']-$waarden['indexBijdrageWaarde'])*$waarden['indexPerf']); 
          
        }
        else
        {

         //  $this->maandTotalen[$datum]['attributieEffect']= ($this->jaarTotalen[$categorie][$jaar]['procent']-$this->jaarTotalen[$categorie][$jaar]['indexPerf'])*100;
          $this->maandTotalen[$datum]['selectieEffect']+=(($waarden['procent']-$waarden['indexPerf'])-$this->maandTotalen[$datum]['allocatieEffect']/100)*100;
//echo "$datum |". $this->maandTotalen[$datum]['selectieEffect']."+=((".$waarden['procent']."-".$waarden['indexPerf'].")-".$this->maandTotalen[$datum]['allocatieEffect']."/100)*100 <br>\n";
         // $this->maandCumulatief[$datum]['selectieEffect']=(($this->jaarTotalen[$categorie][$jaar]['procent']-$this->jaarTotalen[$categorie][$jaar]['indexPerf'])-($this->jaarTotalen[$categorie][$jaar]['allocatieEffect']))*100;

          $this->maandTotalen[$datum]['totaalEffect']+=($waarden['procent']-$waarden['indexPerf'])*100;
  
          $maandDetails[$datum][$categorie]['weging']=$waarden['weging'];
          $maandDetails[$datum][$categorie]['indexBijdrageWaarde']=1;//$waarden['indexBijdrageWaarde'];
          $maandDetails[$datum][$categorie]['procent']=$waarden['procent'];
          $maandDetails[$datum][$categorie]['indexPerf']=$waarden['indexPerf'];
  
        }

         $this->jaarTotalen[$categorie][$jaar]['portBijdrage']+=$waarden['bijdrage'];
         //$this->jaarTotalen[$categorie][$jaar]['indexBijdrageWaarde']+=$waarden['bijdrage'];         
                  

        $lastCategorie=$categorie;
           // $this->formatGetal($this->waarden['Periode'][$categorie]['bijdrage'],2),
      }

      foreach ($gemiddeldeTypen as $type)
        $this->jaarTotalen[$categorie][$jaar][$type]=$this->jaarTotalen[$categorie][$jaar][$type]/count($categorieData['perfWaarden']);
    }
    
//listarray($this->maandTotalen);
//listarray($this->jaarTotalen);
    $startJaar=date("Y",$this->pdf->rapport_datum);
    $this->oib->hoofdcategorien['totaal']="Totaal";

    $this->pdf->AddPage();
    $this->pdf->templateVars['GRAFIEKPaginas']=$this->pdf->page;
   
      $this->pdf->setXY(20,98);
      $barData=array();
     // listarray($this->maandTotalen);
      foreach($this->maandTotalen as $maand=>$waarden)
      {
        unset($waarden['attributieEffect']);
        $barData[$maand]=$waarden;
      }

      $this->VBarDiagram2(260,55,$barData,'');
    //  $colors=array('Allocatie effect'=>array(2,40,54),'Selectie effect'=>array(173,160,122),'Totaal'=>array(87,100,87)); //'attributie effect'=>,array(87,165,25)

      $tmp=array();
      foreach($this->maandTotalen as $maand=>$maandWaarden)
      {
        foreach ($maandWaarden as $type => $waarde)
        {
          if(!isset($tmp[$type]))
            $tmp[$type]=0;
          if ($type == 'attributieEffect') //||)
          {
            $tmp[$type] = $waarde;
          }
          elseif ($type == 'selectieEffect____uit')
          {
            $tmp[$type] = $this->maandCumulatief[$maand][$type];
          }
          else
          {
           // $tmp[$type] += $waarde;
            $tmp[$type] = ((1+$tmp[$type]/100)*(1+$waarde/100)-1)*100;
          }
          $this->maandTotalenCumulatief[$type][$maand] = $tmp[$type];
        }
      }
    //  $colors=array('Allocatie effect'=>array(0,52,121),'Selectie effect'=>array(87,165,25),'attributie effect'=>array(108,31,128)); //
     //$colors=array('Allocatie effect'=>array(2,40,54),'Selectie effect'=>array(173,160,122),'Totaal'=>array(87,100,87)); //

//    listarray($this->maandTotalen );
//listarray($this->maandTotalenCumulatief);
    $this->pdf->setXY(20,120);
    $this->LineDiagram(260,55,$this->maandTotalenCumulatief,'');
    
    /*
    $this->pdf->addPage();
    $this->pdf->setWidths(array(40,30,30,30,30,30,30,30,30));
    $this->pdf->setAligns(array('L','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->row(array('Categorie','Tactische Weging','Strategische Weging','Rendement Portefeuille','Ontwikkeling Benchmark','allocatieEffect','selectieEffect','totaalEffect'));
    $dec=6;
    foreach($this->maandTotalen as $maand=>$waarden)
    {
      //listarray($waarden);exit;
      $this->pdf->row(array($maand));
      foreach($maandDetails[$maand] as $cat=>$catDetails)
      {
        if($cat<>'totaal')
        {
          if(isset( $this->att->categorien[$cat]))
            $omschrijving=$this->att->categorien[$cat];
          else
            $omschrijving=$cat;
          $this->pdf->row(array($omschrijving, $this->formatGetal($catDetails['weging'], $dec), $this->formatGetal($catDetails['indexBijdrageWaarde'], $dec), $this->formatGetal($catDetails['procent'], $dec), $this->formatGetal($catDetails['indexPerf'], $dec), $this->formatGetal($catDetails['allocatieEffect'], $dec)));
        }
      }
      $this->pdf->row(array('Totaal',$this->formatGetal($maandDetails[$maand]['totaal']['weging'],$dec),$this->formatGetal($maandDetails[$maand]['totaal']['indexBijdrageWaarde'],$dec),$this->formatGetal($maandDetails[$maand]['totaal']['procent'],$dec),$this->formatGetal($maandDetails[$maand]['totaal']['indexPerf'],$dec)
                      ,$this->formatGetal($waarden['allocatieEffect'],$dec),$this->formatGetal($waarden['selectieEffect'],$dec),$this->formatGetal($waarden['totaalEffect'],$dec)));
    }
  */
  
      

	}
  
  
function LineDiagram($w, $h, $data, $title,$color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
  
  
    $XPage = $this->pdf->GetX();
    $YPage= $this->pdf->GetY();
    
    $this->pdf->Rect($XPage-10,$YPage-5,$w+15,$h+20);

    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4, vertaalTekst($title, $this->pdf->rapport_taal),'','C');
    $this->pdf->setXY($XPage,$YPage+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    //$bereikdata =   $data;

    $margin = 0;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = floor($w)-$margin;

    if(is_array($color[0]))
    {
      $color1= $color[1];
      $color = $color[0];
    }

    if($color == null)
      $color=array(0,0,0);
    $this->pdf->SetLineWidth(0.2);

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

     $maanden=array();
      $maxVal=0;
      $minVal=0;
      $aantalMaanden=0;
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
    $unit = $lDiag / ($aantalMaanden+.5);

//echo "$minVal $maxVal";exit;

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
    for($i=$nulpunt; $i<= $bodem; $i+= $absUnit*$stapgrootte)
    {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$w ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 0-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
    }

    $n=0;
    for($i=$nulpunt; $i >= $top; $i-= $absUnit*$stapgrootte)
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

    
    //$colors=array('allocatieEffect'=>array(2,40,54),'selectieEffect'=>array(173,160,122),'totaalEffect'=>array(87,100,87)); //
  
    $colors=array('allocatieEffect'=>array($this->pdf->rapport_grafiek_icolor[0], $this->pdf->rapport_grafiek_icolor[1], $this->pdf->rapport_grafiek_icolor[2]),
                  'selectieEffect'=>array(0,153,255),
                  'totaalEffect'=>array($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b'])); //

//listarray($data);
unset($data['attributieEffect']);
    //for ($i=0; $i<count($data); $i++)
    $maandPrinted=array();
    $nulMarkerPrinted=array();
    foreach($data as $type=>$maandData)
    {
      $i=0;
      $color=$colors[$type];
      $yval = $YDiag + (($maxVal) * $waardeCorrectie) ;
      $lineStyle = array('width' => 0.5, 'cap' => 'round', 'join' => 'miter', 'dash' => 0, 'color' => $color);
      foreach($maandData as $maand=>$waarde)
      {
        //foreach($maandData as $line)
       // $extrax=($unit*0.1*-1);
        
       //   $extrax1=($unit*0.1*-1);
        

       // $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8, vertaalTekst($legendDatum[$i], $this->pdf->rapport_taal),0);

        $yval2 = $YDiag + (($maxVal-$waarde) * $waardeCorrectie) ;
        
        if($i <> -1)
        {
          $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        }
  
        if(!isset($nulMarkerPrinted[$i]))
        {
         // $this->pdf->Rect($XDiag + ($i + 1) * $unit - 0.5, $YDiag + (($maxVal) * $waardeCorrectie)-0.5, 1, 1, 'F', '', array(0,0,0));
          $nulMarkerPrinted[$i]=1;
        }
        //$this->pdf->Rect($XDiag+($i+1)*$unit-0.5, $yval2-0.5, 1, 1 ,'F','',$color);
        
      //  if($waarde <> 0)
      //    $this->pdf->Text($XDiag+($i+1)*$unit+$extrax,$yval2-2.5,$this->formatGetal($waarde,1));
          $yval = $yval2;
        
      
          
        if(!isset($maandPrinted[$maand]))
        {
          $maandPrinted[$maand]=1;

  
          $this->pdf->TextWithRotation($XDiag+($i+1)*$unit-10,$bodem+5,date("d-m-Y",db2jul($maand)),30);
          
        }
        
        $i++;
        
        
      }
    }
  
  
  
  
  $omschrijving=array('allocatieEffect'=>'Allocatie effect',
    'selectieEffect'=>'Selectie effect',
    'totaalEffect'=>'Totaal'); //
  
  
  
  $this->pdf->SetLineStyle(array('width' => 0.1,'color'=>array(0,0,0)));
  $xval=$XPage+40;
  $yval=$YPage+$h+10;
  
  $this->pdf->SetXY($XPage, $yval);
  $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize+2);
  $this->pdf->Cell(40, 3,  vertaalTekst('Cumulatief', $this->pdf->rapport_taal),0,0,'L');
  $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
  foreach($colors as $effect=>$color)
  {
    $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
    $this->pdf->SetTextColor(0);
    $this->pdf->SetXY($xval+5, $yval);
    $this->pdf->Cell(50, 3,  vertaalTekst($omschrijving[$effect] , $this->pdf->rapport_taal),0,0,'L');
    $xval+=40;
  }
  
  

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
      $margin = 0;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1);
  
    $this->pdf->Rect(10,$YPage-$h-5,$w+15,$h+20);
    
     // $this->pdf->Rect($XstartGrafiek, $YstartGrafiek-$hGrafiek, $w- $margin, $hGrafiek,'D',''); //,array(245,245,245)
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

      $stapgrootte = ceil(abs($bereik)/$horDiv*10)/5;
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
      
      $numBars=count($data)+.5;
      if($numBars > 0)
        $this->pdf->NbVal=$numBars;
    
    
    $colors=array('allocatieEffect'=>array($this->pdf->rapport_grafiek_icolor[0], $this->pdf->rapport_grafiek_icolor[1], $this->pdf->rapport_grafiek_icolor[2]),
                  'selectieEffect'=>array(0,153,255),
                  'totaalEffect'=>array($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b'])); //

    $vBar = ($bGrafiek / ($this->pdf->NbVal ))/4;
      $bGrafiek = $vBar * ($this->pdf->NbVal );
      $eBaton = ($vBar * 80 / 100);
      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
    $XstartGrafiek+=$vBar*2;
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
          

       //   $this->pdf->Text($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2 -2,$YstartGrafiek +3 ,date('M-y',db2jul($maand)));
  
        $this->pdf->TextWithRotation($XstartGrafiek + ($i -2) * $vBar - $eBaton / 2 -2,$YstartGrafiek +5,date("d-m-Y",db2jul($maand)),30);
          
      }
  
    $colors=array('Allocatie effect'=>array($this->pdf->rapport_grafiek_icolor[0], $this->pdf->rapport_grafiek_icolor[1], $this->pdf->rapport_grafiek_icolor[2]),
                  'Selectie effect'=>array(0,153,255),
                  'Totaal'=>array($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b'])); //
  

    
    $xval=$XPage+40;
    $yval=$YPage+10;
  
    $this->pdf->SetXY($XPage, $yval);
    $this->pdf->SetFont($this->pdf->rapport_font, 'b', $this->pdf->rapport_fontsize+2);
    $this->pdf->Cell(40, 3,  vertaalTekst('Per maand', $this->pdf->rapport_taal),0,0,'L');
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    foreach($colors as $effect=>$color)
    {
      $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
      $this->pdf->SetTextColor(0);
      $this->pdf->SetXY($xval+5, $yval);
      $this->pdf->Cell(50, 3,  vertaalTekst($effect ,$this->pdf->rapport_taal),0,0,'L');
      $xval+=40;
    }

     // $color=array(155,155,155);
     // $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
  }


  
  function bereken($van,$tot,$valuta='EUR',$stapeling='categorie',$periode='maanden')
  {
    $this->rapport_jaar =substr($van,0,4);
    global $__appvar;
    $DB=new DB();
    $query="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
Rekeningmutaties.Fonds,
BeleggingscategoriePerFonds.Beleggingscategorie,
Beleggingscategorien.Omschrijving as categorieOmschrijving,
Beleggingscategorien.Afdrukvolgorde,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving as hoofdCategorieOmschrijving,
Fondsen.Omschrijving as FondsOmschrijving,
Fondsen.Valuta
FROM
Rekeningen
Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
LEFT Join BeleggingscategoriePerFonds ON Rekeningmutaties.Fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
Inner Join Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT Join CategorienPerHoofdcategorie ON BeleggingscategoriePerFonds.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Beleggingscategorien as HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie
Inner Join Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '".$tot."'
AND Rekeningmutaties.Fonds <> ''
GROUP BY Rekeningmutaties.Fonds
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde,Fondsen.Omschrijving ";
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='geen-Hcat';
      
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['fondsen'][]=$data['Fonds'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['Hoofdcategorie']=$data['Hoofdcategorie'];
      $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
      $perRegio[$data['Regio']]['fondsen'][]=$data['Fonds'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['fondsen'][]=$data['Fonds'];
      $alleData['fondsen'][]=$data['Fonds'];
    }
    
    
    $query="SELECT
Rekeningmutaties.rekening,
Rekeningen.Beleggingscategorie,
Beleggingscategorien.Omschrijving AS categorieOmschrijving,
CategorienPerHoofdcategorie.Hoofdcategorie,
HoofdBeleggingscategorien.Omschrijving AS hoofdCategorieOmschrijving,
ValutaPerRegio.Regio,
Regios.Omschrijving as regioOmschrijving,
Regios.Afdrukvolgorde
FROM
Rekeningmutaties
Inner Join Rekeningen ON Rekeningmutaties.rekening = Rekeningen.Rekening
Left Join CategorienPerHoofdcategorie ON Rekeningen.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
Left Join Beleggingscategorien ON Rekeningen.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
Left Join Beleggingscategorien AS HoofdBeleggingscategorien ON HoofdBeleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Hoofdcategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join ValutaPerRegio ON Rekeningen.Valuta = ValutaPerRegio.Valuta AND ValutaPerRegio.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
LEFT Join Regios ON ValutaPerRegio.Regio = Regios.Regio
WHERE
Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
Rekeningmutaties.Boekdatum >= '".$this->rapport_jaar."-01-01' AND  Rekeningmutaties.Boekdatum <= '$tot'
GROUP BY Rekeningen.rekening
ORDER BY HoofdBeleggingscategorien.Afdrukvolgorde, Regios.Afdrukvolgorde,Beleggingscategorien.Afdrukvolgorde";
    
    $DB->SQL($query);
    $DB->Query();
    while($data = $DB->NextRecord())
    {
      if($data['Hoofdcategorie']=='')
        $data['Hoofdcategorie']='geen-Hcat';
      $perHoofdcategorie[$data['Hoofdcategorie']]['omschrijving']=$data['hoofdCategorieOmschrijving'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['rekeningen'][]=$data['rekening'];
      $perHoofdcategorie[$data['Hoofdcategorie']]['Hoofdcategorie']=$data['Hoofdcategorie'];
      $perRegio[$data['Regio']]['omschrijving']=$data['regioOmschrijving'];
      $perRegio[$data['Regio']]['rekeningen'][]=$data['rekening'];
      $perCategorie[$data['Beleggingscategorie']]['omschrijving']=$data['categorieOmschrijving'];
      $perCategorie[$data['Beleggingscategorie']]['rekeningen'][]=$data['rekening'];
      $alleData['rekeningen'][]=$data['rekening'];
    }
    
    $alleData['Hoofdcategorie']='totaal';
    $perfTotaal=$this->fondsPerformance($alleData,$van,$tot,$periode,true,$valuta,'totaal');
    
    if($stapeling=='categorie')
      foreach ($perCategorie as $categorie=>$categorieData)
        $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,$periode,false,$valuta,$categorie);
    elseif($stapeling=='hoofdcategorie')
      foreach ($perHoofdcategorie as $categorie=>$categorieData)
        $perfData[$categorie] = $this->fondsPerformance($categorieData,$van,$tot,$periode,false,$valuta,$categorie);
    
    $perfData['totaal']=$perfTotaal;
    return $perfData;
  }
  
  function fondsPerformance($fondsData,$van,$tot,$stapeling='',$totaal=false,$valuta='EUR',$catnaam='leeg')
  {
    global $__appvar;
    if($stapeling=='maanden')
      $perioden=$this->getMaanden(db2jul($van),db2jul($tot));
    elseif($stapeling=='weken')
      $perioden=$this->getWeken(db2jul($van),db2jul($tot));
    elseif($stapeling=='wekenVrijdag')
      $perioden=$this->getWeken(db2jul($van),db2jul($tot),true);
    elseif($stapeling=='dagen')
      $perioden=$this->getDagen(db2jul($van),db2jul($tot));
    else
      $perioden[]=array('start'=>$van,'stop'=>$tot);
    
    
    global $__appvar;
    $DB=new DB();
    
    foreach ($perioden as $periode)
    {
      foreach ($periode as $rapDatum)
      {
        $query ="SELECT id FROM TijdelijkeRapportage WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$rapDatum' ".$__appvar['TijdelijkeRapportageMaakUniek'];
        if($DB->QRecords($query) < 1)
        {
          if(substr($rapDatum,5,5)=='01-01')
            $startJaar=1;
          else
            $startJaar=0;
          
          $fondswaarden =  berekenPortefeuilleWaarde($this->rapport->portefeuille, $rapDatum,$startJaar);
          vulTijdelijkeTabel($fondswaarden ,$this->rapport->portefeuille,$rapDatum);
        }
      }
    }
    
    
    foreach ($perioden as $periode)
    {
      $datumBegin=$periode['start'];
      if(substr($this->rapport->pdf->PortefeuilleStartdatum,0,10) == $datumBegin)
        $weegDatum=date('Y-m-d',db2jul($datumBegin)+86400);
      else
        $weegDatum=$datumBegin;
      $datumEind=$periode['stop'];
      
      if(!$fondsData['fondsen'])
        $fondsData['fondsen']=array('geen');
      if(!$fondsData['rekeningen'])
        $fondsData['rekeningen']=array('geen');
      
      $tijdelijkefondsenWhere = " TijdelijkeRapportage.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $rekeningFondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fondsData['fondsen'])."') ";
      $tijdelijkeRekeningenWhere = "TijdelijkeRapportage.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      $rekeningRekeningenWhere = "Rekeningmutaties.rekening IN('".implode('\',\'',$fondsData['rekeningen'])."')  ";
      
      if ($valuta <> 'EUR')
      {
        $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
        $startValutaKoers= getValutaKoers($valuta,$datumBegin);
        $eindValutaKoers= getValutaKoers($valuta,$datumEind);
      }
      else
      {
        $koersQuery = "";
        $startValutaKoers= 1;
        $eindValutaKoers= 1;
      }
      
      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$startValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.`type`='rente',TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,0))/$startValutaKoers as renteWaarde
               FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin' AND
                 ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere )".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $start = $DB->NextRecord();
      $beginwaarde = $start['actuelePortefeuilleWaardeEuro'];
      
      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro  FROM TijdelijkeRapportage
               WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum= '$datumBegin'".$__appvar['TijdelijkeRapportageMaakUniek'];
      $DB->SQL($query);
      $DB->Query();
      $start = $DB->NextRecord();
      $totaalBeginwaarde = $start['actuelePortefeuilleWaardeEuro'];
      
      $query = "SELECT ".
        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
        "  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
        "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
        "FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
        "WHERE ".
        "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
      $DB->SQL($query);
      $DB->Query();
      $weging = $DB->NextRecord();
      $totaalGemiddelde = $totaalBeginwaarde + $weging['totaal1'];
      
      
      if(!isset($this->totalen[$datumEind]['gemiddeldeWaarde']))
      {
        $query = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) )))$koersQuery AS totaal
	      FROM Rekeningen
	      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
	      JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
	      WHERE
        Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	      Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	      Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
        $DB->SQL($query);
        $DB->Query();
        $weging = $DB->NextRecord();
        $totaalGemiddelde = $totaalBeginwaarde + $weging['totaal'];
        $this->totalen[$datumEind]['gemiddeldeWaarde']=$totaalGemiddelde;
      }
      
      $query ="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$eindValutaKoers as actuelePortefeuilleWaardeEuro,
                      SUM(if(TijdelijkeRapportage.type='rekening' ,TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,TijdelijkeRapportage.beginPortefeuilleWaardeEuro))/$startValutaKoers as beginWaardeNew
             FROM TijdelijkeRapportage
             WHERE TijdelijkeRapportage.portefeuille = '".$this->rapport->portefeuille."' AND TijdelijkeRapportage.rapportageDatum='$datumEind' AND
               ( $tijdelijkeRekeningenWhere OR $tijdelijkefondsenWhere ) ".$__appvar['TijdelijkeRapportageMaakUniek'] ;
      $DB->SQL($query);
      $DB->Query();
      $eind = $DB->NextRecord();
      $ongerealiseerdResultaat=$eind['actuelePortefeuilleWaardeEuro']-$eind['beginWaardeNew']-$start['renteWaarde'];
      $eindwaarde = $eind['actuelePortefeuilleWaardeEuro'];
      
      $queryAttributieStortingenOntrekkingenRekening = "SELECT SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."'))  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) )))*-1 $koersQuery AS gewogen, ".
        "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)  AS storting,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)  AS onttrekking ".
        "FROM  Rekeningmutaties JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	               WHERE (Rekeningmutaties.Fonds <> '' OR Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
	               $rekeningRekeningenWhere ";
      $DB->SQL($queryAttributieStortingenOntrekkingenRekening); //echo $queryAttributieStortingenOntrekkingenRekening."";
      $DB->Query();
      $AttributieStortingenOntrekkingenRekening = $DB->NextRecord();
      
      $queryRekeningDirecteKostenOpbrengsten = "SELECT SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))$koersQuery AS totaal,
	              SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery  AS opbrengstTotaal,
	              SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1)$koersQuery  AS kostenTotaal
	              FROM Rekeningmutaties
	              JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
	              WHERE (Grootboekrekeningen.Opbrengst=1) AND Rekeningmutaties.Fonds = '' AND Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND Rekeningmutaties.Boekdatum <= '".$datumEind."' AND $rekeningRekeningenWhere ";
      $DB->SQL($queryRekeningDirecteKostenOpbrengsten);
      $DB->Query();
      $RekeningDirecteKostenOpbrengsten = $DB->NextRecord();
      
      $queryFondsDirecteKostenOpbrengsten = "SELECT
       SUM(if(Grootboekrekeningen.Kosten =1, (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0))$koersQuery as kostenTotaal,
       SUM(if(Grootboekrekeningen.Opbrengst =1,if(Grootboekrekeningen.Grootboekrekening ='RENME' ,0,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ) ,0))$koersQuery as opbrengstTotaal ,
       SUM(if(Grootboekrekeningen.Grootboekrekening ='RENME', (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0))$koersQuery as RENMETotaal
            FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
                JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
                WHERE
                (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten =1)  AND
                Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
                Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
                Rekeningmutaties.Boekdatum <= '$datumEind' AND
                $rekeningFondsenWhere ";
      $DB->SQL($queryFondsDirecteKostenOpbrengsten);
      $DB->Query();
      $FondsDirecteKostenOpbrengsten = $DB->NextRecord(); //echo "$queryFondsDirecteKostenOpbrengsten <br><br>\n";
      
      $queryAttributieStortingenOntrekkingen = "SELECT ".
        "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
        "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ) )) AS gewogen, ".
        "SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal,
	               SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS storting,
	               SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery)  AS onttrekking ".
        "FROM  (Rekeningen, Portefeuilles)
	               Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
        "WHERE ".
        "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
        "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
        "Rekeningmutaties.Verwerkt = '1' AND ".
        "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
        "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
        " $rekeningFondsenWhere ";//Rekeningmutaties.Grootboekrekening = 'FONDS' AND
      $DB->SQL($queryAttributieStortingenOntrekkingen);// echo "$queryAttributieStortingenOntrekkingen <br><br>\n";
      $DB->Query();
      $AttributieStortingenOntrekkingen = $DB->NextRecord();
      // listarray($AttributieStortingenOntrekkingen);
      
      $AttributieStortingenOntrekkingen['gewogen'] +=$AttributieStortingenOntrekkingenRekening['gewogen'];
      
      $query = "SELECT SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  - SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) as totaal,
   	            SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery)  AS storting,
   	            SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers *-1 $koersQuery)  AS onttrekking
 	              FROM (Rekeningmutaties,Rekeningen) Inner Join Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
	              WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND
	              $rekeningRekeningenWhere  AND
 	              Rekeningmutaties.Verwerkt = '1' AND
	              Rekeningmutaties.Boekdatum > '$datumBegin' AND
	               Rekeningmutaties.Boekdatum <= '$datumEind' AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1 OR  Rekeningmutaties.Fonds <> ''  )";
      $DB->SQL($query);//echo "$query <br><br>\n";
      $DB->Query();
      $data = $DB->nextRecord();
      $AttributieStortingenOntrekkingen['totaal'] +=$data['totaal'];
      $AttributieStortingenOntrekkingen['storting'] +=$data['storting'];
      $AttributieStortingenOntrekkingen['onttrekking'] +=$data['onttrekking'];
      
      $queryKostenOpbrengsten = "SELECT SUM(if(Grootboekrekeningen.Kosten =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)$koersQuery) as kostenTotaal,
          SUM(if(Grootboekrekeningen.Opbrengst =1,(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ),0)$koersQuery) as opbrengstTotaal
        FROM (Rekeningen, Portefeuilles) Left JOIN Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening
        JOIN Grootboekrekeningen on Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening
        WHERE
           (Grootboekrekeningen.Opbrengst=1 OR Grootboekrekeningen.Kosten=1)  AND
           Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
           Rekeningmutaties.Verwerkt = '1' AND Rekeningmutaties.Boekdatum > '$datumBegin' AND
           Rekeningmutaties.Boekdatum <= '$datumEind' AND Rekeningmutaties.Fonds = '' AND $rekeningRekeningenWhere";
      $DB->SQL($queryKostenOpbrengsten);
      $DB->Query();
      $nietToegerekendeKosten = $DB->NextRecord();
      
      $AttributieStortingenOntrekkingen['totaal'] += $nietToegerekendeKosten['kostenTotaal'];
      $gemiddelde = $beginwaarde - $AttributieStortingenOntrekkingen['gewogen'];
      $performance = ((($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal']) / $gemiddelde);
//echo  round($performance*100,2)." $gemiddelde = $beginwaarde - ".$AttributieStortingenOntrekkingen['gewogen']."<br>\n";
//echo $fondsData['Hoofdcategorie']." ".round($performance*100,2)."= ((($eindwaarde - $beginwaarde) - ". $AttributieStortingenOntrekkingen['totaal'].") / $gemiddelde) <br>\n";
      
      $mutatieData=$this->genereerMutatieLijst($datumBegin,$datumEind,$fondsData['fondsen'],$valuta);
      
      $indexData=$this->indexPerformance($fondsData['Hoofdcategorie'],$datumBegin,$datumEind);


      if($totaal==true)
        $this->totalen[$datumEind]['gemiddeldeWaarde']=$gemiddelde;
      
      $weging=$gemiddelde/$totaalGemiddelde;//$this->totalen[$datumEind]['gemiddeldeWaarde'];
      $resultaat=($eindwaarde - $beginwaarde) - $AttributieStortingenOntrekkingen['totaal'];
      $bijdrage=$resultaat/$gemiddelde*$weging;
      
      
      $overPerfPeriode=($performance+1)/($indexData['perf']+1)-1;
      $relContrib=$overPerfPeriode*$weging;
//echo " $datumEind $weging <br>\n";
      //  echo "$resultaat = ($eindwaarde - $beginwaarde) - ".$AttributieStortingenOntrekkingen['totaal']."<br>\n";
//echo "$performance <br>\n $weging=$gemiddelde/".$this->totalen[$datumEind]['gemiddeldeWaarde']."; <br>\n $bijdrage=$resultaat/$gemiddelde*$weging <br>\n<br>\n";
      $waarden[$datumEind]=array('periode'=>"$datumBegin $datumEind $weegDatum",
                                 'indexPerf'=>$indexData['perf'],
                                 'indexBijdrage'=>$indexData['bijdrage'],
                                 'indexBijdrageWaarde'=>$indexData['percentage'],
                                 'overPerf'=>$overPerfPeriode,
                                 'relContrib'=>$relContrib,
                                 'beginwaarde'=>$beginwaarde,
                                 'eindwaarde'=>$eindwaarde,
                                 'procent'=>$performance,
                                 'stort'=>$AttributieStortingenOntrekkingen['totaal'],
                                 'stortEnOnttrekking'=>$AttributieStortingenOntrekkingen['totaal'],
                                 'storting'=>$AttributieStortingenOntrekkingen['storting'],
                                 'onttrekking'=>$AttributieStortingenOntrekkingen['onttrekking'],
                                 'kosten'=>$FondsDirecteKostenOpbrengsten['kostenTotaal'],
                                 'opbrengst'=>$FondsDirecteKostenOpbrengsten['opbrengstTotaal'],
                                 'kostenNietGekoppeld'=>$nietToegerekendeKosten['kostenTotaal'],
                                 'resultaat'=>$resultaat,
                                 'gemWaarde'=>$gemiddelde,
                                 'ongerealiseerd'=>$ongerealiseerdResultaat  + $FondsDirecteKostenOpbrengsten['RENMETotaal'] ,
                                 'gerealiseerd'=>$mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
                                 'weging'=>$weging,
                                 'bijdrage'=>$bijdrage);
    }
    
    
    $stapelItems=array('procent','bijdrage','indexBijdrage','overPerf','relContrib');
    $somItems=array('indexPerf','stort','stortEnOnttrekking','storting','onttrekking','kosten','kostenNietGekoppeld','resultaat','ongerealiseerd','gerealiseerd','opbrengst');
    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=1;
    
    foreach ($waarden as $datum=>$waarde)
    {
      $perfData['totaal']['resultaat'] +=$waarde['resultaat'];
      foreach ($stapelItems as $item)
        $perfData['totaal'][$item] = ($perfData['totaal'][$item]  * (1+$waarde[$item])) ;
      $waarden[$datum]['index']=$perfData['totaal']['procent']*100;
    }
    $this->waarden[$catnaam]=$waarden;
    foreach ($stapelItems as $item)
      $perfData['totaal'][$item]=($perfData['totaal'][$item]-1)*100;
    $perfData['totaal']['categorie']=$fondsData['categorie'];
    
    foreach ($waarden as $datum=>$waarde)
    {
      if($waarde['beginwaarde']=='')
        $waarde['beginwaarde']=0;
      
      if(!isset($perfData['totaal']['beginwaarde']))
        $perfData['totaal']['beginwaarde']=$waarde['beginwaarde'];
      
      $perfData['totaal']['eindwaarde']=$waarde['eindwaarde'];
      
      foreach ($somItems as $item)
        $perfData['totaal'][$item]+=$waarde[$item];
    }
//listarray($FondsDirecteKostenOpbrengsten);
    
    if($stapeling == true)
    {
      $perfData['totaal']['perfWaarden']=$waarden;
      return $perfData['totaal'];
    }
    else
    {
      return array(
        'beginwaarde'        => $beginwaarde,
        'eindwaarde'         => $eindwaarde,
        'procent'            => $performance * 100,
        'stort'              => $AttributieStortingenOntrekkingen['totaal'],
        'stortEnOnttrekking' => $AttributieStortingenOntrekkingen['totaal'],
        'storting'           => $AttributieStortingenOntrekkingen['storting'],
        'onttrekking'        => $AttributieStortingenOntrekkingen['onttrekking'],
        'kosten'             => $FondsDirecteKostenOpbrengsten['kostenTotaal'],
        'resultaat'          => $resultaat,
        'gemWaarde'          => $gemiddelde,
        'ongerealiseerd'     => $ongerealiseerdResultaat + $FondsDirecteKostenOpbrengsten['RENMETotaal'],
        'gerealiseerd'       => $mutatieData['totalen']['gerealiseerdResultaat'] + $FondsDirecteKostenOpbrengsten['opbrengstTotaal'] + $RekeningDirecteKostenOpbrengsten['totaal'],
        'weging'             => $weging,
        'bijdrage'           => $bijdrage * 100);
    }
  }
  
  function getWeken($julBegin, $julEind, $beginVrijdag=false)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $einddag = date("d",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);
    
    $i=0;
    $stop=mktime (0,0,0,$eindmaand,$einddag,$eindjaar);
    
    
    $beginVrijdag=true;
    if($beginVrijdag==true)
    {
      $extraDagen=0;
      $dagVanWeek= date('w',$julBegin);
      if($dagVanWeek < 5)
        $extraDagen=5-$dagVanWeek;
      elseif($dagVanWeek > 5)
        $extraDagen=12-$dagVanWeek;
      $begindag+=$extraDagen;
    }
    
    while ($counterStart < $stop)
    {
      $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+7,$beginjaar);
      if($counterEnd >= $julEind)
        $counterEnd = $julEind;
      
      if($i == 0)
      {
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
      }
      else
      {
        $datum[$i]['start'] =date('Y-m-d',$counterStart);
        if(substr($datum[$i]['start'],5,5)=='12-31')
          $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
      }
      
      $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
      $i=$i+7;
    }
    
    return $datum;
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
      {
        $datum[$i]['start'] =date('Y-m-d',$counterStart);
        if(substr($datum[$i]['start'],5,5)=='12-31')
          $datum[$i]['start']=(date('Y',$counterStart)+1)."-01-01";
      }
      
      $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if($datum[$i]['start'] ==  $datum[$i]['stop'])
        unset($datum[$i]);
      $i++;
    }
    return $datum;
  }
  
  
  function getKwartalen($julBegin, $julEind)
  {
    if($julBegin > $julEind )
      return array();
    $beginjaar = date("Y",$julBegin);
    $eindjaar = date("Y",$julEind);
    $maandenStap=3;
    $stap=1;
    $n=0;
    $teller=$julBegin;
    $kwartaalGrenzen=array();
    $datum=array();
    while ($teller < $julEind)
    {
      $teller = mktime (0,0,0,$stap,0,$beginjaar);
      $stap +=$maandenStap;
      if($teller > $julBegin && $teller < $julEind)
      {
        $grensDatum=date("d-m-Y",$teller);
        $kwartaalGrenzen[] = $teller;
      }
    }
    if(count($kwartaalGrenzen) > 0)
    {
      $datum[$n]['start']=date('Y-m-d',$julBegin);
      foreach ($kwartaalGrenzen as $grens)
      {
        $datum[$n]['stop']=date('Y-m-d',$grens);
        $n++;
        $start=date('Y-m-d',$grens);
        if(substr($start,-5)=='12-31')
          $start=(substr($start,0,4)+1).'-01-01';
        
        $datum[$n]['start']=$start;
      }
      $datum[$n]['stop']=date('Y-m-d',$julEind);
    }
    else
    {
      $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$julEind));
    }
    return $datum;
  }
  
  function getDagen($julBegin, $julEind)
  {
    $eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $einddag= date("d",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);
    $counterStart=$julBegin;
    $i=0;
    while ($counterEnd < $julEind)
    {
      $counterStart = mktime (0,0,0,$beginmaand,$begindag,$beginjaar);
      $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
      $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd));
      $i++;
    }
    return $datum;
  }
  
  function getTWRdagen($julBegin, $julEind)
  {
    $query="SELECT DATE(Rekeningmutaties.Boekdatum) as datum
    FROM Rekeningen Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
    WHERE Rekeningen.Portefeuille='".$this->rapport->portefeuille."'  AND
    Rekeningmutaties.Boekdatum >= '".date('Y-m-d',$julBegin)."' AND  Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$julEind)."'
    GROUP BY Rekeningmutaties.Boekdatum ORDER BY Boekdatum";
    
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    $i=0;
    while($mutaties = $DB->nextRecord())
    {
      if($i>0)
      {
        $datum[$i]['start'] = $lastdatum;
        $datum[$i]['stop']=$mutaties['datum'];
      }
      $lastdatum=$mutaties['datum'];
      $i++;
    }
    return $datum;
  }
  
  function genereerMutatieLijst($rapportageDatumVanaf,$rapportageDatum,$fonds='',$valuta='EUR')
  {
    // loopje over Grootboekrekeningen Opbrengsten = 1
    if(is_array($fonds))
      $fondsenWhere = " Rekeningmutaties.Fonds IN('".implode('\',\'',$fonds)."') ";
    elseif($fonds!='')
      $fondsenWhere=" Rekeningmutaties.Fonds='$fonds'";
    else
      $fondsenWhere='';
    
    if($valuta=='EUR')
      $koersQuery=",(SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) as Rapportagekoers ";
    else $koersQuery=', 1 as Rapportagekoers ';
    
    
    $query = "SELECT Fondsen.Omschrijving, ".
      "Fondsen.Fondseenheid, ".
      "Rekeningmutaties.Boekdatum, ".
      "Rekeningmutaties.Transactietype,
		Rekeningmutaties.Valuta,
		Rekeningmutaties.Fonds,
		 Rekeningmutaties.Afschriftnummer,
     Rekeningmutaties.omschrijving as rekeningOmschrijving,
		 Rekeningmutaties.Aantal AS Aantal, Rekeningmutaties.Fonds,  ".
      "Rekeningmutaties.Fondskoers, ".
      "Rekeningmutaties.Debet as Debet, ".
      "Rekeningmutaties.Credit as Credit, ".
      "Rekeningmutaties.Valutakoers ".
      " $koersQuery ".
      "FROM Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen ".
      "WHERE ".
      "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
      "Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
      "Rekeningen.Portefeuille = '".$this->rapport->portefeuille."' AND ".
      "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
      "Rekeningmutaties.Verwerkt = '1' AND $fondsenWhere AND ".
      "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
      "Rekeningmutaties.Transactietype <> 'B' AND ".
      "Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
      "Rekeningmutaties.Boekdatum > '$rapportageDatumVanaf' AND ".
      "Rekeningmutaties.Boekdatum <= '$rapportageDatum' ".
      "ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds, Rekeningmutaties.id ";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
    
    while($mutaties = $DB->nextRecord())
    {
      $mutaties['Aantal'] = abs($mutaties['Aantal']);
      $aankoop_koers = "";
      $aankoop_waardeinValuta = "";
      $aankoop_waarde = "";
      $verkoop_koers = "";
      $verkoop_waardeinValuta = "";
      $verkoop_waarde = "";
      $historisch_kostprijs = "";
      $resultaat_voorgaande = "";
      $resultaat_lopendeProcent = "";
      $resultaatlopende = 0 ;
      //$mutaties['Rapportagekoers']=1;
      
      switch($mutaties['Transactietype'])
      {
        case "A" :
        case "A/O" :
        case "A/S" :
        case "D" :
        case "S" :
          $t_aankoop_waarde 				= abs($mutaties['Debet']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_aankoop_waardeinValuta = abs($mutaties['Debet']);
          $t_aankoop_koers					= $mutaties['Fondskoers'];
          $totaal_aankoop_waarde += $t_aankoop_waarde;
          if($t_aankoop_waarde > 0)
            $aankoop_koers 					= $t_aankoop_koers;
          if($t_aankoop_waardeinValuta > 0)
            $aankoop_waardeinValuta = $t_aankoop_waardeinValuta;
          if($t_aankoop_koers > 0)
            $aankoop_waarde 				= $t_aankoop_waarde;
          break;
        case "B" :
          // Beginstorting
          break;
        case "L" :
        case "V" :
        case "V/O" :
        case "V/S" :
          $t_verkoop_waarde 				= abs($mutaties['Credit']) * $mutaties['Valutakoers'] * $mutaties['Rapportagekoers'];
          $t_verkoop_waardeinValuta = abs($mutaties['Credit']);
          $t_verkoop_koers					= $mutaties['Fondskoers'];
          $totaal_verkoop_waarde += $t_verkoop_waarde;
          if($t_verkoop_koers > 0)
            $verkoop_koers 					= $t_verkoop_koers;
          if($t_verkoop_waardeinValuta > 0)
            $verkoop_waardeinValuta = $t_verkoop_waardeinValuta;
          if($t_verkoop_waarde > 0)
            $verkoop_waarde 				= $t_verkoop_waarde;
          break;
        default :
          $_error = "Fout ongeldig tranactietype!!";
          break;
      }
      
      /*
        Alleen resultaat berekenen bij "Sluiten", niet bij "Openen".
      */
      if($mutaties['Transactietype'] == "L" || $mutaties['Transactietype'] == "V" || $mutaties['Transactietype'] == "V/S" || $mutaties['Transactietype'] == "A/S")
      {
        $historie = berekenHistorischKostprijs($this->rapport->portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$this->rapport->pdf->rapportageValuta);
        if($mutaties['Transactietype'] == "A/S")
          $rekenAantal=($mutaties['Aantal'] * -1) ;
        else
          $rekenAantal=$mutaties['Aantal'];
        
        $historischekostprijs = $rekenAantal       * $historie['historischeWaarde']       * $historie['historischeValutakoers']        * $mutaties['Fondseenheid'];
        $beginditjaar         = $rekenAantal       * $historie['beginwaardeLopendeJaar']  * $historie['beginwaardeValutaLopendeJaar']  * $mutaties['Fondseenheid'];
        
        
        if($this->rapport->pdf->rapportageValuta != 'EUR' && $mutaties['Valuta'] == $this->rapport->pdf->rapportageValuta)
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeValutakoers'];
          $beginditjaar         = $beginditjaar         / getValutaKoers($this->rapport->pdf->rapportageValuta ,date("Y",db2jul($this->rapport->rapportageDatum).'-01-01'));
        }
        elseif ($this->rapport->pdf->rapportageValuta != 'EUR')
        {
          $historischekostprijs = $historischekostprijs / $historie['historischeRapportageValutakoers'];
          $beginditjaar         = $beginditjaar         / getValutaKoers($this->rapport->pdf->rapportageValuta ,date("Y",db2jul($this->rapport->rapportageDatum).'-01-01'));
        }
        
        if($historie['voorgaandejarenActief'] == 0)
        {
          $resultaatvoorgaande = 0;
          $resultaatlopende = $t_verkoop_waarde - $historischekostprijs;
          if($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = 0;
            $resultaatlopende = $t_aankoop_waarde - $historischekostprijs;
          }
        }
        else
        {
          $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
          $resultaatlopende = $t_verkoop_waarde - $beginditjaar;
          if($mutaties['Transactietype'] == "A/S")
          {
            $resultaatvoorgaande = $beginditjaar - $historischekostprijs;
            $resultaatlopende = ($t_aankoop_waarde * -1) - $beginditjaar;
          }
        }
        $result_historischkostprijs = $historischekostprijs;
        $result_voorgaandejaren = $resultaatvoorgaande;
        $result_lopendejaar = $resultaatlopende;
        $totaal_resultaat_waarde += $resultaatlopende;
      }
      else
      {
        $result_historischkostprijs = 0;
        $result_voorgaandejaren = 0;
        $result_lopendejaar = 0;
      }
      //	listarray($mutaties);
      $data[$mutaties['Fonds']]['mutatie']+=$aankoop_waarde-$verkoop_waarde;
      $data[$mutaties['Fonds']]['transacties'].=' '.$mutaties['Transactietype'];
      $data[$mutaties['Fonds']]['aantal']+=$mutaties['Aantal'];
      $data[$mutaties['Fonds']]['aankoop']+=$aankoop_waarde;
      $data[$mutaties['Fonds']]['verkoop']+=$verkoop_waarde;
      $data[$mutaties['Fonds']]['resultaatJaren']+=$result_voorgaandejaren;
      $data[$mutaties['Fonds']]['resultaatJaar']+=$result_lopendejaar;
      $data['totalen']['gerealiseerdResultaat']+=($result_voorgaandejaren+$result_lopendejaar);
      $data['totalen']['mutaties']+=$data[$mutaties['Fonds']]['mutatie'];
    }
    return $data;
  }
  
  function indexPerformance($categorie,$van,$tot)
  {
  
  
    $fondsVerdeling=$this->perf->benchmarkVerdelingOpDatum($tot,$categorie);
    $perf=$this->perf->getFondsPerformance($fondsVerdeling,$van,$tot)/100;
  // listarray($this->perf->planTotalen[$tot]);
    //listarray($indexData);
 //  listarray($fondsVerdeling);
   //echo "$categorie $van,$tot";
   // listarray($perf);
  
    $plan=array();
    if(isset($this->perf->planTotalen[$tot][$categorie]))
    {
      $plan = $this->perf->planTotalen[$tot];
      if ($categorie == 'VAR')
      {
        $plan['VAR'] += $plan['Liquiditeiten'];
        unset($plan['Liquiditeiten']);
      }
    }
    if(isset($plan[$categorie]))
      $bijdragePercentage=$plan[$categorie];
    else
      $bijdragePercentage=1;
    
    $tmp= array('perf'=>$perf,
                'bijdrage'=>$perf*$bijdragePercentage,
                'datum'=>$tot,
                'percentage'=>$bijdragePercentage,//$fondsData['Percentage']
                'categorie'=>$categorie,
                'hoofdfonds'=>$fondsVerdeling);
   // listarray($tmp);
    return $tmp;
    
   // echo $categorie.",$van,$tot <br>\n";
    
    
    global $__appvar;
    $DB = new DB();
    if(!is_array($this->indexLookup) || count($this->indexLookup) < 1)
    {
      $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie
      WHERE IndexPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
            AND (IndexPerBeleggingscategorie.Portefeuille='".$this->rapport->portefeuille."' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
      $DB->SQL($query);
      $DB->Query();
      while($index=$DB->nextRecord())
        $this->indexLookup[$index['Beleggingscategorie']]=$index['Fonds'];
      $this->indexLookup['totaal']=$this->rapport->pdf->portefeuilledata['SpecifiekeIndex'];
    }
    
    if(!is_array($this->normData) || count($this->normData) < 1)
    {
      $this->normData['totaal']=100;
      $q="SELECT ZorgplichtPerBeleggingscategorie.Beleggingscategorie,ZorgplichtPerRisicoklasse.norm,ZorgplichtPerRisicoklasse.Zorgplicht,CategorienPerHoofdcategorie.Hoofdcategorie
       FROM
       ZorgplichtPerRisicoklasse
       Inner Join ZorgplichtPerBeleggingscategorie ON ZorgplichtPerRisicoklasse.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       WHERE ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
       ORDER by CategorienPerHoofdcategorie.Hoofdcategorie";
      $DB->SQL($q);
      $DB->Query();
      while($data=$DB->nextRecord())
        $this->normData[$data['Hoofdcategorie']]=$data['norm'];
      
      $q="SELECT
      ZorgplichtPerBeleggingscategorie.Beleggingscategorie,
      CategorienPerHoofdcategorie.Hoofdcategorie,
      ZorgplichtPerPortefeuille.Zorgplicht,
      ZorgplichtPerPortefeuille.norm
      FROM ZorgplichtPerPortefeuille
      JOIN ZorgplichtPerBeleggingscategorie  ON ZorgplichtPerPortefeuille.Zorgplicht = ZorgplichtPerBeleggingscategorie.Zorgplicht AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      Inner Join CategorienPerHoofdcategorie ON ZorgplichtPerBeleggingscategorie.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->rapport->portefeuille."' AND ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."'
      ORDER by CategorienPerHoofdcategorie.Hoofdcategorie
      ";
      $DB->SQL($q);
      $DB->Query();
      while($data=$DB->nextRecord())
        $this->normData[$data['Hoofdcategorie']]=$data['norm'];
    }
    
    $fonds=$this->indexLookup[$categorie];
    
    /*
    $query="SELECT
    IndexPerBeleggingscategorie.Fonds,
    ModelPortefeuilleFixed.Percentage / 100 as Percentage
    FROM IndexPerBeleggingscategorie LEFT Join ModelPortefeuilleFixed ON IndexPerBeleggingscategorie.Beleggingscategorie = ModelPortefeuilleFixed.Fonds
    WHERE Vermogensbeheerder='".$this->rapport->pdf->portefeuilledata['Vermogensbeheerder']."' AND Beleggingscategorie='$categorie' ";
    $DB->SQL($query); //echo " $query <br><br>\n";
    $fondsData=$DB->lookupRecord();
    $fonds=$fondsData['Fonds'];
    */
    
    $query="SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
    $DB->SQL($query);
    $DB->Query();
    $verdeling=array();
    while($data=$DB->nextRecord())
      $verdeling[$data['fonds']]=$data['percentage'];
    
    if(count($verdeling)==0)
      $verdeling[$fonds]=100;
    
    $totalPerf=0;
    foreach($verdeling as $fonds=>$percentage)
    {
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '".substr($tot,0,4)."-01-01' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $janKoers=$DB->lookupRecord();
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $startKoers=$DB->lookupRecord();
      
      $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
      $DB->SQL($query);
      $eindKoers=$DB->lookupRecord();
      
      $perfVoorPeriode=($startKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perfJaar=($eindKoers['Koers'] - $janKoers['Koers']) / ($janKoers['Koers']);
      $perf=$perfJaar-$perfVoorPeriode;
      //$perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
      $totalPerf+=($perf*$percentage/100);
    }
    
    $perf= $totalPerf;
    
    if($_POST['debug']==1)
      echo "$categorie | $fonds | $van | $tot | $perf<br>\n";
    
    
    /*
    
    echo "$fonds <br>\n";
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$van' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
  	$DB->SQL($query);
    $startKoers=$DB->lookupRecord();
    $query="SELECT Fonds, Datum, Koers FROM Fondskoersen WHERE datum  <= '$tot' AND Fonds='".$fonds."' ORDER BY Datum DESC LIMIT 1";
	  $DB->SQL($query);
    $eindKoers=$DB->lookupRecord();
    $perf=($eindKoers['Koers'] - $startKoers['Koers']) / ($startKoers['Koers']);
    
    */
    $waarden[$periode['stop']]=array('perf'=>$perf,'aandeel'=>$fondsData['Percentage']);
    
    
    $tmp= array('perf'=>$perf,
                'bijdrage'=>$perf*$fondsData['Percentage'],
                'datum'=>$tot,
                'percentage'=>($this->normData[$categorie]/100),//$fondsData['Percentage']
                'categorie'=>$categorie,
                'hoofdfonds'=>$fonds,
                'koersVan'=>$startKoers['Koers'],
                'koersEind'=>$eindKoers['Koers']);
    
    return $tmp;
  }
}
?>