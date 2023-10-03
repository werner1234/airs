<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/08/07 12:33:15 $
 		File Versie					: $Revision: 1.5 $

 		$Log: RapportCASH_L81.php,v $
 		Revision 1.5  2019/08/07 12:33:15  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/08/04 13:30:57  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/07/27 18:03:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/02/10 14:26:45  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/02/09 19:02:53  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/01/12 17:08:31  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2018/01/21 09:00:44  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2018/01/13 19:10:28  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2012/09/23 08:51:44  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2012/04/14 16:51:17  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2012/03/25 13:27:46  rvv
 		*** empty log message ***

 		Revision 1.1  2012/03/11 17:19:57  rvv
 		*** empty log message ***

 		Revision 1.2  2012/03/04 11:39:58  rvv
 		*** empty log message ***

 		Revision 1.1  2012/02/29 16:52:49  rvv
 		*** empty log message ***

 		Revision 1.10  2012/02/26 15:17:43  rvv
 		*** empty log message ***

 		Revision 1.9  2012/01/04 16:28:38  rvv
 		*** empty log message ***

 		Revision 1.8  2011/12/07 19:14:53  rvv
 		*** empty log message ***

 		Revision 1.7  2011/09/14 09:26:56  rvv
 		*** empty log message ***

 		Revision 1.6  2011/09/03 14:30:20  rvv
 		*** empty log message ***

 		Revision 1.5  2011/07/03 06:42:47  rvv
 		*** empty log message ***

 		Revision 1.4  2011/06/15 16:14:39  rvv
 		*** empty log message ***

 		Revision 1.3  2011/06/13 14:41:56  rvv
 		*** empty log message ***

 		Revision 1.2  2011/06/02 15:05:05  rvv
 		*** empty log message ***

 		Revision 1.1  2011/05/29 06:38:42  rvv
 		*** empty log message ***


*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/CashflowClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");

//ini_set('max_execution_time',60);
class RapportCASH_L81
{
	function RapportCASH_L81($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
	 //
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "CASH";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Cashflow (lossing en coupon) per rating";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->excelData=array();
		$this->ratings=array();
		$query="SELECT rating,omschrijving FROM Rating ORDER BY Afdrukvolgorde";
		$db=new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->NextRecord())
    {
      $this->ratings[$data['rating']]=$data['omschrijving'];
    }
	}

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}
  
  
  
  function genereerRows($cashfow,$jaren)
  {
    global $__appvar;
    $tmp=array();
    $db=new DB();
    
    foreach($cashfow->cashArray as $dateId=>$regels)
    {
     
      foreach($regels as $i=>$fonds)
      {
        $query="SELECT rating FROM Fondsen where Fonds='".mysql_real_escape_string($fonds['fonds'])."'";
        $db->SQL($query);
        $rating=$db->lookupRecord();
        $fonds['rating']=$rating['rating'];
        $tmp[$dateId][$fonds['fondsOmschrijving'].'_'.$fonds['type'].'_'.$i]=$fonds;
      }
    }
    foreach($tmp as $dateId=>$regels)
    {
      ksort($regels);
      $tmp[$dateId]=array_values($regels);
    }
    //  $tmp=$this->cashArray ;
    
    $gegevens=array();
    $rapJaar=substr($this->rapportageDatum,0,4);
    foreach ($tmp as $datum=>$regels)
    {
      if($datum > $cashfow->datumJul)
      {
        //   listarray($regels);
        foreach ($regels as $id=>$fonds)
        {
          if($fonds['type']=='lossing')
          {
            $waarde = $fonds['totaalAantal']*$fonds['fondsEenheid']*$fonds['actueleValuta']* $fonds['lossingskoers'];//*100;

          }
          elseif($fonds['type']=='rente')
          {
            $waarde = $cashfow->renteOverPeriode($fonds,adodb_date("Y-m-d",$datum));

          }
          
          if(round($waarde,2)==0.00)
            continue;
  
          $jaar=adodb_date("Y",$datum);
          if($jaar > ($rapJaar+$jaren))
            $jaar='Overig';
          
          //$cashfow->regelsRaw[]=array(adodb_date("d-m-Y",$datum),$fonds['fonds'],$fonds['rating'],$fonds['fondsOmschrijving'],$fonds['type'],$waarde);
          $gegevens[$jaar]['Totaal']+=$waarde;
          $gegevens[$jaar][$fonds['rating']]+=$waarde;
          //$this->formuleDelen[] = array('waarde'=>$waarde,'jaar'=>$fonds['jaar']);
        }
      }
    }
    $cashfow->gegevens = $gegevens;
    
    
 
    
    return $gegevens;
  }


	function writeRapport()
	{
		global $__appvar;
		$this->pdf->AddPage();
		$this->pdf->templateVars['CASHPaginas']=$this->pdf->page;
		
		$aantalCols=count($this->ratings)+2;
		$width=(297-$this->pdf->marge*2)/$aantalCols;
    $headerWidth=array();
    $headerAlign=array('L');
		for($i=0;$i<=$aantalCols;$i++)
    {
      $headerWidth[] = $width;
      $headerAlign[]='R';
    }
	  $this->pdf->SetWidths($headerWidth);
		$this->pdf->SetAligns($headerAlign);
		$header=array('Jaar'=>'Jaar','Totaal'=>'Totaal');
		$bordersTop=array('U','U');
		$bordersTotaal=array('',array('TS','UU'));
		foreach($this->ratings as $rating=> $omschrijvng)
    {
      $header[$rating] = $omschrijvng;
      $bordersTop[]='U';
      $bordersTotaal[]=array('TS','UU');
    }

		// print categorie headers

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	  $cashfow = new Cashflow($this->portefeuille,$this->pdf->rapport_datumvanaf,$this->pdf->rapport_datum,$this->pdf->debug);
		$cashfow->genereerTransacties();
		$regels = $this->genereerRows($cashfow,25);
    $regelsXls = $this->genereerRows($cashfow,40);
		$rowIndex=array_keys($regels);
    $rowIndexXls=array_keys($regelsXls);
    $this->pdf->underlinePercentage=0.8;
    
    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-2*$this->pdf->marge, 8 , 'F');
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    //$this->pdf->Row(array('','','Rating'));
    $this->pdf->CellBorders = $bordersTop;
    $headerValues=array_values($header);
    $headerValues[2]="Rating\n".$headerValues[2];
    $this->pdf->Row($headerValues);
    $this->pdf->excelData[]=$headerValues;
    $this->pdf->SetTextColor(0);
    unset($this->pdf->CellBorders);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $totaalRegel=array();
		foreach ($rowIndex as $jaar)
		{
		   $row=array();
       foreach($header as $col=>$colOmschrijving)
       {
         if($col=='Jaar')
         {
           $row[] = $jaar;
         }
         else
         {
           $row[] = $this->formatGetal($regels[$jaar][$col], 0);
           $totaalRegel[$col]+=$regels[$jaar][$col];
         }
       }
       $this->pdf->Row($row);
		}
    foreach ($rowIndexXls as $jaar)
    {
      $xlsRow=array();
      foreach($header as $col=>$colOmschrijving)
      {
        if($col=='Jaar')
          $xlsRow[] = $jaar;
        else
          $xlsRow[] = round($regelsXls[$jaar][$col], 0);
      }
      $this->pdf->excelData[]=$xlsRow;
    }
		$this->pdf->ln(2);
		$this->pdf->CellBorders = $bordersTop;
    
    $row=array();
    $xlsRow=$row;
    foreach($header as $col=>$colOmschrijving)
    {
      if($col=='Jaar')
      {
        $row[] = 'Totaal';
        $xlsRow[] = 'Totaal';
      }
      else
      {
        $row[] = $this->formatGetal($totaalRegel[$col], 0);
        $xlsRow[] = round($totaalRegel[$col], 0);
      }
    }
    $this->pdf->CellBorders = $bordersTotaal;
    $this->pdf->Row($row);
    $this->pdf->excelData[]=$xlsRow;
    

		$this->pdf->CellBorders = array();

		
	}

}
?>