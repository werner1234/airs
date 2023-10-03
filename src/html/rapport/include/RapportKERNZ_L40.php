<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/02/29 16:24:09 $
File Versie					: $Revision: 1.4 $

$Log: RapportKERNZ_L40.php,v $
Revision 1.4  2020/02/29 16:24:09  rvv
*** empty log message ***

Revision 1.3  2020/01/29 17:36:42  rvv
*** empty log message ***

Revision 1.2  2019/01/06 12:43:52  rvv
*** empty log message ***

Revision 1.1  2019/01/05 18:38:35  rvv
*** empty log message ***

Revision 1.26  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.25  2017/01/21 17:48:04  rvv
*** empty log message ***

Revision 1.24  2017/01/18 17:02:28  rvv
*** empty log message ***

Revision 1.23  2015/06/13 13:16:01  rvv
*** empty log message ***

Revision 1.22  2015/04/04 15:15:15  rvv
*** empty log message ***

Revision 1.21  2013/12/21 18:31:54  rvv
*** empty log message ***

Revision 1.20  2013/12/14 17:16:30  rvv
*** empty log message ***

Revision 1.19  2013/12/07 17:51:24  rvv
*** empty log message ***

Revision 1.18  2013/11/02 17:04:05  rvv
*** empty log message ***

Revision 1.17  2013/07/15 17:06:38  rvv
*** empty log message ***

Revision 1.16  2012/12/08 14:48:08  rvv
*** empty log message ***

Revision 1.15  2012/12/05 16:45:29  rvv
*** empty log message ***

Revision 1.14  2012/11/21 16:29:06  rvv
*** empty log message ***

Revision 1.13  2012/11/14 16:48:28  rvv
*** empty log message ***

Revision 1.12  2012/11/07 17:07:29  rvv
*** empty log message ***

Revision 1.11  2012/11/03 18:14:13  rvv
*** empty log message ***

Revision 1.10  2012/11/01 14:40:05  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
//include_once($__appvar["basedir"]."/html/rapport/RapportHuidigeSamenstellingLayout.php");

class RapportKERNZ_L40
{
	function RapportKERNZ_L40($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum, $valuta = 'EUR')
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "KERNZ";
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_titel = "Aandelen optie constructies";
		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->verdeling='beleggingssector';
    $this->pdf->tweedeDeel=false;
    $this->pdf->underlinePercentage=0.8;
    $this->modelDataPrinted=array();

	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec,$extraTeken='')
	{
	  if(round($waarde,2) <> 0)
	  	return number_format($waarde,$dec,",",".").$extraTeken;
	}

	function formatAantal($waarde, $dec, $VierDecimalenZonderNullen=false)
	{
	  if(round($waarde,2) == 0)
      return '';
	  elseif ($VierDecimalenZonderNullen)
	  {
	   $getal = explode('.',$waarde);
	   $decimaalDeel = $getal[1];
	   if ($decimaalDeel != '0000' )
	   {
	     for ($i = strlen($decimaalDeel); $i >=0; $i--)
	     {
         $decimaal = $decimaalDeel[$i-1];
	       if ($decimaal != '0' && !$newDec)
	       {
	         $newDec = $i;
	       }
	     }
	     return number_format($waarde,$newDec,",",".");
	   }
	  else
	   return number_format($waarde,$dec,",",".");
	  }
	  else
	   return number_format($waarde,$dec,",",".");
	}


	function writeRapport()
	{

	  # LOOP over H-CAT/CAT/(regio of sector)
	  # eerst fonds dan optie tonen.
	  # rapportagedatum +365 dagen is kortlopende
	  # P 229002
		global $__appvar;
		// rapport settings
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$this->portefeuilledata = $DB->nextRecord();


	  $this->pdf->widthB = array(65,20,20,20,20,20,20,20,20,20,20,20,20);
		$this->pdf->alignB = array('L','R','R','R','R','R','R','R','R','R','R');
    

		$this->pdf->AddPage();
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$DB2 = new DB();

		$verdeling=$this->verdeling;
  	$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
    


			$query = "SELECT
TijdelijkeRapportage.fondsOmschrijving,
TijdelijkeRapportage.fonds,
Fondsen.OptieBovenliggendFonds,
Fondsen.OptieType,
Fondsen.fondseenheid,
IF ( Fondsen.OptieBovenliggendFonds = '', TijdelijkeRapportage.Fonds, Fondsen.OptieBovenliggendFonds ) AS onderliggendFonds,
TijdelijkeRapportage.totaalAantal,
TijdelijkeRapportage.fondspaar,
TijdelijkeRapportage.actueleFonds,
TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
FROM
	TijdelijkeRapportage
LEFT JOIN Fondsen ON TijdelijkeRapportage.fonds = Fondsen.Fonds
WHERE
			 TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND
			 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' AND TijdelijkeRapportage.fondspaar not IN(0,100) ".
			$__appvar['TijdelijkeRapportageMaakUniek'].
			" ORDER BY onderliggendFonds, TijdelijkeRapportage.fondspaar, Fondsen.OptieBovenliggendFonds, TijdelijkeRapportage.fondsOmschrijving ASC";
			debugSpecial($query,__FILE__,__LINE__); //TijdelijkeRapportage.type <> 'rente' AND
		


			$DB2->SQL($query);
			$DB2->Query();

			$regels=array();
      //$regels[]=array('hoofdcategorieOmschrijving'=>'hoofdcategorieOmschrijving','hoofdsector'=>'hoofdsector','verdeling'=>'verdeling','beleggingscategorie'=>'beleggingscategorie','fondsOmschrijving'=>'fondsOmschrijving','verdelingOmschrijving'=>'verdelingOmschrijving');
      while($subdata = $DB2->NextRecord())
			{
        $regels[]=$subdata;

      }
      $optieData=array();
      $dalingsPercentages=array(5,10,15,20);
      foreach($regels as $regel)
			{
				
        if($regel['OptieType']=='')
        {
          $regel['OptieType'] = 'fonds';
          $optieData[$regel['fondspaar']][$regel['OptieType']]['fonds']=$regel['fonds'];
          $optieData[$regel['fondspaar']][$regel['OptieType']]['fondsOmschrijving']=$regel['fondsOmschrijving'];
          $optieData[$regel['fondspaar']][$regel['OptieType']]['actueleFonds']=$regel['actueleFonds'];
          foreach($dalingsPercentages as $percentage)
          {
          	$fondsKoers=($regel['actueleFonds']-$regel['actueleFonds']*$percentage/100);
            $optieData[$regel['fondspaar']][$regel['OptieType']]['Min'.$percentage]['actueleFonds']= $fondsKoers;
            $optieData[$regel['fondspaar']][$regel['OptieType']]['Min'.$percentage]['actuelePortefeuilleWaardeEuro']= $fondsKoers*$regel['totaalAantal']*$regel['fondseenheid'];
            $optieData[$regel['fondspaar']]['totaal']['Min'.$percentage]['actuelePortefeuilleWaardeEuro']+=$fondsKoers*$regel['totaalAantal']*$regel['fondseenheid'];
          }
        }
        else
				{
					$query="SELECT delta,gamma,datum FROM fondsenOptiestatistieken WHERE fonds='".mysql_real_escape_string($regel['fonds'])."' AND datum <='".$this->rapportageDatum."' ORDER BY datum desc limit 1";
          $DB2->SQL($query);
          $deltaData=$DB2->lookupRecord();
          if(!isset($deltaData['delta']))
            $deltaData['delta']=0;
          if(!isset($deltaData['gamma']))
            $deltaData['gamma']=0;
          $optieData[$regel['fondspaar']][$regel['OptieType']][$regel['fonds']]['delta']=$deltaData['delta'];
          $optieData[$regel['fondspaar']][$regel['OptieType']][$regel['fonds']]['gamma']=$deltaData['gamma'];
          $optieData[$regel['fondspaar']][$regel['OptieType']][$regel['fonds']]['actueleFonds']=$regel['actueleFonds'];
          
          if(!isset($optieData[$regel['fondspaar']][$regel['OptieType']]['deltas']))
            $optieData[$regel['fondspaar']][$regel['OptieType']]['deltas']='';
          if(!isset($optieData[$regel['fondspaar']][$regel['OptieType']]['gammas']))
            $optieData[$regel['fondspaar']][$regel['OptieType']]['gammas']='';
          
          if($deltaData['delta']===0)
            $optieData[$regel['fondspaar']][$regel['OptieType']]['deltas'].=$deltaData['delta'];
          else
            $optieData[$regel['fondspaar']][$regel['OptieType']]['deltas'].=$this->formatGetal($deltaData['delta'],4).' ';
          $optieData[$regel['fondspaar']][$regel['OptieType']]['deltas']=trim($optieData[$regel['fondspaar']][$regel['OptieType']]['deltas']);
          
          if($deltaData['gamma']===0)
            $optieData[$regel['fondspaar']][$regel['OptieType']]['gammas'].=$deltaData['gamma'];
          else
            $optieData[$regel['fondspaar']][$regel['OptieType']]['gammas'].=$this->formatGetal($deltaData['gamma'],4).' ';
          $optieData[$regel['fondspaar']][$regel['OptieType']]['gammas']=trim($optieData[$regel['fondspaar']][$regel['OptieType']]['gammas']);
          
          foreach($dalingsPercentages as $percentage)
          {
            $fondskoersVerschil=$optieData[$regel['fondspaar']]['fonds']['Min'.$percentage]['actueleFonds']-$optieData[$regel['fondspaar']]['fonds']['actueleFonds'];
            //$optieKoers=(($deltaData['delta']+$fondskoersVerschil*$deltaData['gamma'])*$fondskoersVerschil) + $regel['actueleFonds'];
  
            $optieKoers=($deltaData['delta']*$fondskoersVerschil)+$deltaData['gamma']*pow($fondskoersVerschil,2) + $regel['actueleFonds'];

            if($optieKoers<0)
              $optieKoers=0;
            $optieData[$regel['fondspaar']][$regel['OptieType']][$regel['fonds']]['Min'.$percentage]['actueleFonds']=$optieKoers;
            $optieData[$regel['fondspaar']][$regel['OptieType']][$regel['fonds']]['Min'.$percentage]['actuelePortefeuilleWaardeEuro']=$optieKoers*$regel['totaalAantal']*$regel['fondseenheid'];
  
            $optieData[$regel['fondspaar']]['totaal']['Min'.$percentage]['actuelePortefeuilleWaardeEuro']+=$optieKoers*$regel['totaalAantal']*$regel['fondseenheid'];
            // $koers=($regel['actueleFonds']-$regel['actueleFonds']*$percentage/100) +$optieData[$regel['fondspaar']][$regel['OptieType']]['delta'][$regel['fonds']]
          }
				}

        $optieData[$regel['fondspaar']][$regel['OptieType']]['actuelePortefeuilleWaardeEuro']+=$regel['actuelePortefeuilleWaardeEuro'];
        $optieData[$regel['fondspaar']][$regel['OptieType']]['totaalAantal']+=$regel['totaalAantal'];
			}
     // listarray($optieData);
     // exit;
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    $this->pdf->row(array('Combinatie','Aantal Aandelen','Aantal Put Opties',"Put Delta","Put Gamma","Aantal Call opties","Call Delta","Call Gamma","Waarde\nAandelen","Waarde\nOpties","Waarde Combinatie"));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    $combiWaardeTotaal=0;
    foreach($optieData as $optiePaarId=>$optiePaar)
      {
         $combiWaarde=$optiePaar['fonds']['actuelePortefeuilleWaardeEuro']+$optiePaar['P']['actuelePortefeuilleWaardeEuro']+$optiePaar['C']['actuelePortefeuilleWaardeEuro'];
         $combiWaardeTotaal+=$combiWaarde;
			   $this->pdf->row(array($optiePaar['fonds']['fondsOmschrijving'],
                              $this->formatAantal($optiePaar['fonds']['totaalAantal'],0,true),
                              $this->formatAantal($optiePaar['P']['totaalAantal'],0,true),
                              $optiePaar['P']['deltas'],
                              $optiePaar['P']['gammas'],
                              $this->formatAantal($optiePaar['C']['totaalAantal'],0,true),
                              $optiePaar['C']['deltas'],
                              $optiePaar['C']['gammas'],
													    $this->formatGetal($optiePaar['fonds']['actuelePortefeuilleWaardeEuro'],2),
                              $this->formatGetal($optiePaar['P']['actuelePortefeuilleWaardeEuro']+$optiePaar['C']['actuelePortefeuilleWaardeEuro'],2),
                              $this->formatGetal($combiWaarde,2)));
        
  //    listarray($subdata['fondsOmschrijving']);
//listarray($this->totalenRente);   

		}
    $this->pdf->ln();
    $this->pdf->widthB = array(65+20+20,20,20,20,20,20,20,20,20,20,20);
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->row(array('Totale waarde aandelen/optie combinaties',"","","","","","","",$this->formatGetal($combiWaardeTotaal,2)));
    
    
    $this->pdf->widthB = array(65,25,25,5,25,25,5,25,25,5,25,25);
    $this->pdf->alignB = array('L','C','C','C','C','C','C','C','C','C','C','C','C','C');
    $this->pdf->AddPage();
    $this->pdf->ln();
    $this->pdf->SetWidths($this->pdf->widthB);
    $this->pdf->SetAligns($this->pdf->alignB);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $header=array('Combinatie');
    $totalen=array();
    foreach($dalingsPercentages as $percentage)
		{
      $header[]="Waarde\nCombinatie\nbij % daling\n$percentage %";
      $header[]="%\nWaarde\ndaling";
      $header[]='';
		}
    $this->pdf->CellBorders=array('',array('T','L','R'),array('T','R'),'',array('T','L','R'),array('T','R'),'',array('T','L','R'),array('T','R'),'',array('T','L','R'),array('T','R'));
    $this->pdf->row($header);
    $this->pdf->alignB = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->CellBorders=array('',array('L','R'),array('R'),'',array('L','R'),array('R'),'',array('L','R'),array('R'),'',array('L','R'),array('R'));
    $this->pdf->SetAligns($this->pdf->alignB);
    
    foreach($optieData as $optiePaarId=>$optiePaar)
    {
      $tmpRow=array($optiePaar['fonds']['fondsOmschrijving']);
      $huidigeWaarde=$optiePaar['fonds']['actuelePortefeuilleWaardeEuro']+$optiePaar['P']['actuelePortefeuilleWaardeEuro']+$optiePaar['C']['actuelePortefeuilleWaardeEuro'];
      foreach($dalingsPercentages as $percentage)
			{
				$verwachteWaarde=$optiePaar['totaal']['Min'.$percentage]['actuelePortefeuilleWaardeEuro'];
				
        $tmpRow[] =$this->formatGetal($verwachteWaarde,2);
        $tmpRow[] =$this->formatGetal(($verwachteWaarde-$huidigeWaarde)/$huidigeWaarde*100,2).' %';
        $tmpRow[] ='';
        $totalen['Min'.$percentage]+=$verwachteWaarde;
			}
      $this->pdf->row($tmpRow);
    }
    $this->pdf->row(array('','','','','','','','','','','','',''));
    
    $tmpRow=array('Totalen');
    foreach($dalingsPercentages as $percentage)
    {
      $tmpRow[] =$this->formatGetal($totalen['Min'.$percentage],2);
      $tmpRow[] =$this->formatGetal(( $totalen['Min'.$percentage]-$combiWaardeTotaal)/$combiWaardeTotaal*100,2).' %';
      $tmpRow[] ='';
    }
    $this->pdf->CellBorders=array('',array('U','L','R'),array('U','R'),'',array('U','L','R'),array('U','R'),'',array('U','L','R'),array('U','R'),'',array('U','L','R'),array('U','R'));
    $this->pdf->row($tmpRow);
    
    
    // print grandtotaal
		//$this->pdf->ln();

   // $this->pdf->SetWidths(array(200));
    //$this->pdf->row(array("* Resultaat is exclusief lopende rente"));
  //  $this->pdf->MultiCell(200,$this->pdf->rowHeight,"* Resultaat is exclusief lopende rente",0,'L');


$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
	}
}
?>
