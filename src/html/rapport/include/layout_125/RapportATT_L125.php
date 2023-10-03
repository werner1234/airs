<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once("rapport/include/layout_125/ATTberekening_L125.php");

class RapportATT_L125
{
	function RapportATT_L125($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Rendementen";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;

		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));


	  $this->rapportageDatumVanaf = "$RapStartJaar-01-01";

	}
  
  function formatGetal($waarde, $dec, $teken='')
  {
    return formatGetal_L125($waarde, $dec, $teken);
  }


	function header($periode='')
  {
    $colW=280/11;
    $this->pdf->widthA = array(20-$this->pdf->marge,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW,$colW);//,23
    $this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R');
  
    $this->pdf->SetWidths($this->pdf->widthA);
    $this->pdf->SetAligns($this->pdf->alignA);
  
    //for($i=0;$i<count($this->pdf->widthA);$i++)
    //  $this->pdf->fillCell[] = 1;
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
  
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->ln();
    $this->pdf->row(array('',$periode,
                      "Begin-\nvermogen",
                      "Stortingen",
                      "Opnamen",
                      "Koers-\nresultaat",
                      "Dividend",
                      "Kosten\n ",
                      "Eind-\nvermogen",
                      "Beleggings-\nresultaat",
                      "Totaal\nrendement"));
    $this->pdf->SetTextColor(0);
  }


	function writeRapport()
	{
	  global $__appvar;
    $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
    $RapJaar = date("Y", db2jul($this->rapportageDatum));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		$this->pdf->AddPage();
    
    subHeader_L125($this->pdf,28,array(280),array('Voorgaande jaren'));
		$this->pdf->ln();
		$this->header();
    $this->pdf->ln();

  $index=new indexHerberekening();
  $historieTot=$this->rapportageDatumVanaf;
  if(substr($historieTot,5,5)=='01-01')
  {
    $historieTot=(substr($historieTot,0,4)-1)."-12-31";
  }
  $indexJaren=array();
 
  if(isset($historieTot))
  {
    $indexHistorie = $index->getWaarden($this->pdf->PortefeuilleStartdatum ,$historieTot  ,$this->portefeuille,'','jaar');
    foreach($indexHistorie as $jaarData)
    {
      $jaar=substr($jaarData['periode'],0,4);
      $jaarData['index']=($jaarData['performance']+100);
      $indexJaren[$jaar]=$jaarData;
    }
  }
  if(count($indexJaren) > 0)
  {
    $this->pdf->fillCell = array();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
    $this->pdf->CellFontStyle=array('',array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
    
    
    foreach ($indexJaren as $jaar=>$row)
    {
      $this->pdf->row(array('',$jaar,
		                    $this->formatGetal($row['waardeBegin'],0,'€'),
		                    $this->formatGetal($row['stortingen'],0,'€'),
                        $this->formatGetal($row['onttrekkingen'],0,'€'),
		                    $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],0,'€'),
                        $this->formatGetal($row['opbrengsten']+$row['rente'],0,'€'),
                        $this->formatGetal($row['kosten'],0,'€'),
                        $this->formatGetal($row['waardeHuidige'],0,'€'),
                        $this->formatGetal($row['resultaatVerslagperiode'],0,'€'),
                        $this->formatGetal($row['index']-100,1,'%')
                         ));
      $this->pdf->Ln();
    }
    $this->pdf->Ln();
  }
 
 
 
  //$this->att=new ATTberekening_L125($this);
 // $rendemnten=$this->att->getPerfArray($this->portefeuille, $this->rapportageDatumVanaf,$this->rapportageDatum);
    
  $index=new indexHerberekening();
  $indexData = $index->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$this->portefeuille);
  if(substr($this->rapportageDatum,0,4) > 2015)
  {
    foreach($indexData as $i=>$data)
    {
      $einddatum=substr($data['periode'],12,10);
      $tmp=$index->BerekenMutaties2($this->rapportageDatumVanaf,$einddatum,$this->portefeuille);
      //echo $this->rapportageDatumVanaf."-> $einddatum ".$indexData[$i]['index']."->".(100+$tmp['performance'])."<br>\n";
      $indexData[$i]['index']=100+$tmp['performance'];
    }
  }


    $rendamentWaarden=array();
foreach ($indexData as $index=>$data)
{
  if($data['datum'] != '0000-00-00')
  {
    $rendamentWaarden[] = $data;
  
  }
}
//echo count($rendamentWaarden)."<br>\n";
$totaalHoogte=$this->pdf->getY()+$this->pdf->rowHeight*6+(count($rendamentWaarden)*($this->pdf->rowHeight+3))+($this->pdf->rowHeight+6);
//echo "$totaalHoogte <br>\n".$this->pdf->PageBreakTrigger;exit;

if($totaalHoogte>$this->pdf->PageBreakTrigger)
  $this->pdf->addPage();
    unset($this->pdf->CellFontStyle);
    subHeader_L125($this->pdf,$this->pdf->getY(),array(280),array('Lopende jaar'));
    $this->pdf->ln();
    $this->header($RapJaar);
    $this->pdf->ln();
    $this->pdf->CellFontStyle=array('',array($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize),array($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize));
    
    
    
    $grafiekData['Datum'][]="$RapStartJaar-12-01";

   if(count($rendamentWaarden) > 0)
   {
        $n=1;
        $this->pdf->fillCell = array();
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
        $this->pdf->underlinePercentage=0.8;


        $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r']*1.2,$this->pdf->rapport_kop_bgcolor['g']*1.2,$this->pdf->rapport_kop_bgcolor['b']*1.2);

        $totaalRendament=100;
        $totalen=array();
		    foreach ($rendamentWaarden as $row)
		    {
		      $datum = db2jul($row['datum']);

		      $this->pdf->row(array('',ucfirst(vertaalTekst($__appvar["Maanden"][date("n",$datum)],$this->pdf->rapport_taal)) ,
		                           $this->formatGetal($row['waardeBegin'],0,'€'),
		                           $this->formatGetal($row['stortingen'],0,'€'),
                               $this->formatGetal($row['onttrekkingen'],0,'€'),
		                           $this->formatGetal($row['gerealiseerd']+$row['ongerealiseerd'],0,'€'),
		                           $this->formatGetal($row['opbrengsten']+$row['rente'],0,'€'),
		                           $this->formatGetal($row['kosten'],0,'€'),

		                           $this->formatGetal($row['resultaatVerslagperiode'],0,'€'),
		                           $this->formatGetal($row['waardeHuidige'],0,'€'),
                               $this->formatGetal($row['index']-100,1,"%"),
                               ));
          $this->pdf->ln(3);
		                           if(!isset($totalen['waardeBegin']))
                                 $totalen['waardeBegin']=$row['waardeBegin'];
                                 $totalen['waardeHuidige'] = $row['waardeHuidige'];
                                 $totalen['resultaatVerslagperiode'] += $row['resultaatVerslagperiode'];
                                 $totalen['gerealiseerd'] += $row['gerealiseerd'];
                                 $totalen['ongerealiseerd'] += $row['ongerealiseerd'];
                                 $totalen['opbrengsten'] += $row['opbrengsten'];
                                 $totalen['kosten'] += $row['kosten'];
                                 $totalen['rente'] += $row['rente'];
                                 $totalen['stortingen'] += $row['stortingen'];
                                 $totalen['onttrekkingen'] += $row['onttrekkingen'];
                                 $totalen['index'] = $row['index'];//$rendemnten[$row['datum']]['index'];

		    $n++;
		    }
		    $this->pdf->fillCell=array();


        $this->pdf->ln(3);
     $this->pdf->Line(20,$this->pdf->GetY() ,$this->pdf->w-20,$this->pdf->GetY(),array('color'=>$this->pdf->textGrijs));
     $this->pdf->ln(3);
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

		    $this->pdf->row(array('','Totaal',
		                           $this->formatGetal($totalen['waardeBegin'],0,'€'),
		                           $this->formatGetal($totalen['stortingen'],0,'€'),
		                           $this->formatGetal($totalen['onttrekkingen'],0,'€'),
		                           $this->formatGetal($totalen['gerealiseerd']+$totalen['ongerealiseerd'],0,'€'),
		                           $this->formatGetal($totalen['opbrengsten']+$totalen['rente'],0,'€'),
		                           $this->formatGetal($totalen['kosten'],0,'€'),
		                           $this->formatGetal($totalen['resultaatVerslagperiode'],0,'€'),
		                           $this->formatGetal($totalen['waardeHuidige'],0,'€'),
                               $this->formatGetal($totalen['index']-100,1,'%')
		                           ));
     
		     $this->pdf->CellBorders = array();
         $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		  }




	   $this->pdf->fillCell = array();
     unset($this->pdf->CellFontStyle);

	}



}
?>