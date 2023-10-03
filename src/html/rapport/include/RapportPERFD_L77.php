<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/06 15:48:23 $
File Versie					: $Revision: 1.15 $

$Log: RapportPERFD_L77.php,v $
Revision 1.15  2020/06/06 15:48:23  rvv
*** empty log message ***

Revision 1.14  2020/05/23 06:34:56  rvv
*** empty log message ***

Revision 1.13  2020/05/20 17:13:48  rvv
*** empty log message ***

Revision 1.12  2020/03/07 14:41:15  rvv
*** empty log message ***

Revision 1.11  2020/02/19 15:02:02  rvv
*** empty log message ***

Revision 1.10  2020/02/15 18:29:05  rvv
*** empty log message ***

Revision 1.9  2020/02/09 12:20:44  rvv
*** empty log message ***

Revision 1.8  2020/02/09 10:11:59  rvv
*** empty log message ***

Revision 1.7  2020/02/05 17:12:48  rvv
*** empty log message ***

Revision 1.6  2020/01/29 17:36:42  rvv
*** empty log message ***

Revision 1.5  2020/01/25 16:36:35  rvv
*** empty log message ***

Revision 1.4  2020/01/18 16:37:36  rvv
*** empty log message ***

Revision 1.3  2019/12/11 11:17:44  rvv
*** empty log message ***

Revision 1.2  2019/11/27 15:55:39  rvv
*** empty log message ***

Revision 1.1  2018/09/15 17:45:24  rvv
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
include_once("rapport/include/ATTberekening_L77.php");

class RapportPERFD_L77
{
	function RapportPERFD_L77($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
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
    $query = "SELECT Portefeuilles.startDatum, Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();

		$query="SELECT
KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
Beleggingscategorien.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN Beleggingscategorien ON KeuzePerVermogensbeheerder.waarde = Beleggingscategorien.Beleggingscategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder = '".$this->portefeuilledata['Vermogensbeheerder']."' AND KeuzePerVermogensbeheerder.categorie = 'Beleggingscategorien'
ORDER BY KeuzePerVermogensbeheerder.Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $omschrijvingen=array();
    while($data=$DB->nextRecord())
    {
      $omschrijvingen[$data['waarde']]=$data['Omschrijving'];
    }
    
/*
    if(db2jul($this->rapportageDatumVanaf) > db2jul($this->portefeuilledata['startDatum']))
	   	$rapportageStartJaar= date("Y-01-01",$this->pdf->rapport_datum);
	  else
	   	$rapportageStartJaar=substr($this->portefeuilledata['startDatum'],0,10);
    
  //  $rapportageStartJaar='2019-01-31';
	  $this->tweedePerformanceStart=$rapportageStartJaar;
*/
	//  $att=new ATTberekening2($this);
  //  $waarden=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,$this->pdf->rapportageValuta,'hoofdcategorie');
  

    $att=new ATTberekening_L77($this);
    $att->indexPerformance=true;
    //$this->waarden['Historie']=$att->bereken(substr($this->pdf->PortefeuilleStartdatum,0,10),  $this->rapportageDatum,'EUR','hoofdcategorie');
   // $this->waarden['Historie']=$att->bereken($rapportageStartJaar,  $this->rapportageDatum,'Hoofdcategorie');
    $this->waarden['Historie']=$att->bereken($this->rapportageDatumVanaf,  $this->rapportageDatum,'Beleggingscategorien');
    

    if($this->pdf->debug==true)
    {
      //listarray($this->waarden['Historie']['totaal']);
       $this->pdf->excelData[]=array('Totaal categorie'); 
      $this->pdf->excelData[]=array('Datum','PortefeuillePerf','IndexPerf'); 
      foreach($this->waarden['Historie'] as $categorie=>$categorieData)
      {
        if($categorie <> 'totaal')
        {
          //$this->pdf->excelData[]=array($categorie);
          //foreach($categorieData['perfWaarden'] as $datum=>$perfData)
          //  $this->pdf->excelData[]=array($datum,$perfData['procent']+1,$perfData['indexPerf']); 
        
          //$this->pdf->excelData[]=array('');
         }
      } 
    } 
    $stapelTypen=array('procentBruto','indexPerf'); //,'bijdrage','indexBijdrageWaarde'
    $effecten=array('allocatieEffect','selectieEffect','interactieEffect','restfactor','attributieEffect');

    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
     $this->jaarTotalen[$categorie]=array();
    
    $this->maandTotalenCumulatief=array();
    foreach ($this->waarden['Historie'] as $categorie=>$categorieData)
    { 
      $laatste=array();

       foreach ($categorieData['perfWaarden'] as $datum=>$waarden)
       {
         $jaar = substr($datum, 0, 4);
         $this->jaarTotalen[$categorie][$jaar]['resultaat'] += $waarden['resultaat'];
         foreach ($stapelTypen as $type)
         {
           $this->jaarTotalen[$categorie][$jaar][$type] = ((1 + $waarden[$type]/100) * (1 + $laatste[$jaar][$type]/100) - 1)*100;
           $laatste[$jaar][$type] = $this->jaarTotalen[$categorie][$jaar][$type];
         }
  
  
         $this->jaarTotalen[$categorie][$jaar]['aandeelOpTotaal'] = $waarden['aandeelOpTotaal'];
         $this->jaarTotalen[$categorie][$jaar]['portBijdrage'] += $waarden['bijdrage'];
  
  
         if ($categorie != 'totaal')
         {
  
           $maandEffecten=array();
           $maandEffecten['attributieEffect'] = ($waarden['aandeelOpTotaal'] *$waarden['procentBruto']- $waarden['indexBijdrageWaarde'] * $waarden['indexPerf'])*100 ;
           $maandEffecten['allocatieEffect'] = ($waarden['aandeelOpTotaal'] - $waarden['indexBijdrageWaarde']) * ($waarden['indexPerf'] - $this->waarden['Historie']['totaal']['perfWaarden'][$datum]['indexPerf']) * 100;
           $maandEffecten['selectieEffect'] = ($waarden['procentBruto'] - $waarden['indexPerf']) * $waarden['indexBijdrageWaarde'] * 100;
           $maandEffecten['interactieEffect'] = ($waarden['aandeelOpTotaal'] - $waarden['indexBijdrageWaarde']) * ($waarden['procentBruto'] - $waarden['indexPerf']) * 100;
           $maandEffecten['restfactor'] = ($waarden['aandeelOpTotaal'] - $waarden['indexBijdrageWaarde']) * $this->waarden['Historie']['totaal']['perfWaarden'][$datum]['indexPerf'] * 100;
  
         //  $maandEffecten['attributieEffect'] = $maandEffecten['allocatieEffect'] +$maandEffecten['selectieEffect'] +$maandEffecten['interactieEffect'] +$maandEffecten['restfactor'] ;

           foreach($effecten as $effect)
           {
             $this->maandTotalen[$datum][$effect]+=$maandEffecten[$effect];
             
             $this->jaarTotalen[$categorie][$jaar][$effect] = ((1 + $maandEffecten[$effect]/100) * (1 + $laatste[$jaar][$effect]/100) - 1)*100;
             $laatste[$jaar][$effect] = $this->jaarTotalen[$categorie][$jaar][$effect];
  
 
           }

         }
  
       }
    }
  //  listarray( $this->maandTotalen);// listarray($this->waarden['Historie']);
    foreach( $this->jaarTotalen as $categorie=>$jaarData)
    {
      foreach($jaarData as $jaar=>$catData)
      {
          if($categorie <> 'totaal')
          {
            foreach($effecten as $effect)
            {
              $this->jaarTotalen['totaal'][$jaar][$effect] += $catData[$effect];
            }
          }
        
      }
    }
    
    //exit;
  //  listarray($this->jaarTotalen);
 //   exit;
//listarray($this->jaarTotalen);
    $startJaar=date("Y",$this->pdf->rapport_datum);
    $this->pdf->rapport_titel = "Performance en attributie-overzicht per beleggingscategorie en totaal";
    $this->pdf->AddPage();
    $this->pdf->templateVars['ATTPaginas']=$this->pdf->page;
    $this->pdf->SetWidths(array(40,25,25,25,25,25,25,25,25,25));
   	$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R'));
   	$this->pdf->ln(5);
   	$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
    $this->pdf->row(array("", vertaalTekst("Tactische\nWeging", $this->pdf->rapport_taal), vertaalTekst("Strategische\nWeging", $this->pdf->rapport_taal), vertaalTekst("Rendement\nPortefeuille", $this->pdf->rapport_taal),
                       vertaalTekst("Ontwikkeling\nbenchmark", $this->pdf->rapport_taal), vertaalTekst("Attributie\n ", $this->pdf->rapport_taal),vertaalTekst("Allocatie\neffect", $this->pdf->rapport_taal),
                      vertaalTekst("Selectie\neffect", $this->pdf->rapport_taal),vertaalTekst('Interactie effect', $this->pdf->rapport_taal)));//, vertaalTekst('Restfactor', $this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->ln();
    
//listarray($this->jaarTotalen);
 
   if($__appvar['bedrijf']=='TEST')
   {
     $decimalenProc = 4;
     $decimalenAtt = 4;
   }
   else
   {
     $decimalenProc = 1;
     $decimalenAtt = 2;
   }
   foreach ($this->jaarTotalen as $categorie=>$jaarWaarden)
    {

      $waarden=$jaarWaarden[$startJaar];
      //listarray($waarden);      
      $this->pdf->row(array(vertaalTekst($omschrijvingen[$categorie], $this->pdf->rapport_taal),
                        $this->formatGetal($waarden['aandeelOpTotaal']*100,$decimalenProc),//." ". $this->formatGetal(($oibData[$categorie]['port']['procent'])*100,1),//$waarden['aandeelOpTotaal']*100,1), // . ' '.
      $this->formatGetal($att->normData[$categorie],$decimalenProc),
      $this->formatGetal($waarden['procentBruto']*100,$decimalenAtt),
      $this->formatGetal($waarden['indexPerf']*100,$decimalenAtt),
                        $this->formatGetal($waarden['allocatieEffect']+$waarden['selectieEffect']+$waarden['interactieEffect'],$decimalenAtt),//$waarden['attributieEffect']
                        $this->formatGetal($waarden['allocatieEffect'],$decimalenAtt),
                        $this->formatGetal($waarden['selectieEffect'],$decimalenAtt),
                        $this->formatGetal($waarden['interactieEffect'],$decimalenAtt)));//, $this->formatGetal($waarden['restfactor'],2)));
      $this->pdf->ln(5);


    }
    
      //$this->pdf->rapport_titel = "Maandelijkse attributie-effecten";
     // $this->pdf->AddPage();

    $tonenEffecten=array('attributieEffect','allocatieEffect','selectieEffect','interactieEffect');
    $laatste=array();
    foreach($this->maandTotalen as $maand=>$maandWaarden)
    {
      foreach ($maandWaarden as $effect => $waarde)
      {
         $laatste[$effect] = ((1 + $waarde/100) * (1 + $laatste[$effect]/100) - 1)*100;
//         if($effect=='attributieEffect')
         $this->maandTotalenCumulatief[$effect][$maand] = $laatste[$effect];
      }
    }

    foreach($this->maandTotalenCumulatief['attributieEffect'] as $maand=>$waarde)
    {
      $nieuweWaarde=$this->maandTotalenCumulatief['allocatieEffect'][$maand]+$this->maandTotalenCumulatief['selectieEffect'][$maand]+$this->maandTotalenCumulatief['interactieEffect'][$maand];
      $this->maandTotalenCumulatief['attributieEffect'][$maand]=$nieuweWaarde;
      //echo "$maand $waarde $nieuweWaarde <br>\n";
    }
    //maandTotalenCumulatief
    unset($this->maandTotalenCumulatief['restfactor']);
   // listarray($this->maandTotalenCumulatief);
    if($this->pdf->getY()>100)
      $this->pdf->addPage();
    $this->pdf->setXY(15,182);
    $barData=array();
    // listarray($this->maandTotalen);
    foreach($this->maandTotalen as $maand=>$waarden)
    {
      unset($waarden['attributieEffect']);
      unset($waarden['restfactor']);
      $barData[$maand]=$waarden;
    }
    $this->VBarDiagram2(130,137-50,$barData,'');
    $colors=array('allocatie effect'=>array(176,218,238),'selectie effect'=>array(4,157,218),'interactie effect'=>array(156,222,202));//,'Totaal'=>array(0, 52, 121)); //'attributie effect'=>,array(87,165,25)
    $xval=25;$yval=185;
    foreach($colors as $effect=>$color)
    {
      $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
      $this->pdf->SetTextColor(0);
      $this->pdf->SetXY($xval+5, $yval);
      $this->pdf->Cell(50, 3,  vertaalTekst($effect ,$this->pdf->rapport_taal),0,0,'L');
      $xval+=40;
    }
    
    //  $colors=array('allocate effect'=>array(0,52,121),'selectie effect'=>array(87,165,25),'attributie effect'=>array(108,31,128)); //
     $colors=array('allocatie effect'=>array(176,218,238),'selectie effect'=>array(4,157,218),'interactie effect'=>array(156,222,202),'Attributie'=>array(1,88,109)); //
//listarray($this->maandTotalenCumulatief);
  
    $this->LineDiagram(160,50+50,120,120-50,$this->maandTotalenCumulatief,'');
      $xval=165;$yval=185;
      foreach($colors as $effect=>$color)
      {
         $this->pdf->Rect($xval, $yval, 3, 3, 'DF',null,$color);
         $this->pdf->SetTextColor(0);
         $this->pdf->SetXY($xval+5, $yval);
         $this->pdf->Cell(40, 3,  vertaalTekst($effect , $this->pdf->rapport_taal),0,0,'L');
         $xval+=30;
      }
      
  

	}
  
  
function LineDiagram($x,$y,$w, $h, $data, $title,$color=null, $maxVal=0, $minVal=0, $horDiv=4, $verDiv=4,$jaar=0)
  {
    global $__appvar;
    
    $this->pdf->Rect($x-10,$y-5,$w+15,$h+15);
    $this->pdf->setXY($x,$y);
    $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize+2);
    $this->pdf->Multicell($w,4, vertaalTekst($title, $this->pdf->rapport_taal),'','C');
    $this->pdf->setXY($x,$y+8);
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);

    //$bereikdata =   $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 0;
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

    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
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

  //  $minVal = floor(($minVal) * 1.1);
  //  $maxVal = ceil(($maxVal) * 1.1);
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

    //   $colors=array('allocatieEffect'=>array(108,31,128),'selectieEffect'=>array(234,105,11),'interactieEffect'=>array(0,52,121),'attributieEffect'=>array(0,0,0)); //
    $colors=array('allocatieEffect'=>array(176,218,238),'selectieEffect'=>array(4,157,218),'interactieEffect'=>array(156,222,202),'AttributieEffect'=>array(1,88,109)); //


    //for ($i=0; $i<count($data); $i++)
    $maandPrinted=array();
    $extrax1=0;
    $extrax=0;
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
        

       // $this->pdf->TextWithRotation($XDiag+($i)*$unit-5+$unit,$YDiag+$hDiag+8, vertaalTekst($legendDatum[$i], $this->pdf->rapport_taal),0);

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


    //$colors=array('allocatieEffect'=>array(108,31,128),'selectieEffect'=>array(234,105,11),'interactieEffect'=>array(0,52,121)); //
    $colors=array('allocatieEffect'=>array(176,218,238),'selectieEffect'=>array(4,157,218),'interactieEffect'=>array(156,222,202),'Attributie'=>array(1,88,109)); //

    $vBar = ($bGrafiek / ($this->pdf->NbVal ))/4; //4
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