<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2018/03/17 18:48:55 $
File Versie					: $Revision: 1.24 $

$Log: RapportATT_L20.php,v $
Revision 1.24  2018/03/17 18:48:55  rvv
*** empty log message ***

Revision 1.23  2012/12/15 14:52:51  rvv
*** empty log message ***

Revision 1.22  2011/05/04 16:31:36  rvv
*** empty log message ***

*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/indexBerekening.php");
include_once($__appvar["basedir"]."/html/rapport/rapportATTberekening.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportATT_L20
{
	function RapportATT_L20($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "ATT";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Vermogensontwikkeling";

			$this->pdf->rapport_header = array('','Beleggingsplan',"Waarde per"," "," "," "," "," ");


		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;

		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  $this->rapJaar =$RapStopJaar;

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     exit;
	 }
	}

	function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec,$percentage = false)
	{
	  if(round($waarde,2)== 0.00)
	    return '';

	  if($percentage == true)
	  {
	    if($waarde > 100)
	      return "p.m.";
	    else
	      return number_format($waarde,$dec,",",".").'%';
	  }
	  else
	  	return number_format($waarde,$dec,",",".");

	}


	function writeRapport()
	{
	  global $__appvar;
	  $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->addPage();

	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	   $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	 $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));



	 		 $DB = new DB();

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
		$totaalWaarde = $totaalWaarde[totaal];

	 $beleggingsplan=array();
	 $query = "SELECT Waarde,Datum,Portefeuille,ProcentRisicoDragend,ProcentRisicoMijdend FROM Beleggingsplan WHERE portefeuille = '".$this->portefeuille."' AND Datum < NOW() order by Datum DESC";
	 $DB->SQL($query);
	 $DB->Query();
	 $ProcentArray =  $DB->nextRecord();
	 $query = "SELECT Waarde,Datum,Portefeuille FROM Beleggingsplan WHERE portefeuille = '".$this->portefeuille."' order by Datum ";
	 $DB->SQL($query);
	 $DB->Query();
	 while($data = $DB->nextRecord())
	 {
	   $beleggingsplan[$data['Datum']] = $data;
	   $this->planEind=$data['Datum'];
	 }

	 $this->pdf->subTitle = 'Totale waarde van vermogen op '.date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum)."   EUR ".$this->formatGetal($totaalWaarde);


	  $query = "SELECT  BeleggingssectorPerFonds.AttributieCategorie,  AttributieCategorien.Omschrijving
              FROM BeleggingssectorPerFonds  ,AttributieCategorien
              WHERE BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
              BeleggingssectorPerFonds.AttributieCategorie =  AttributieCategorien.AttributieCategorie
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie";
		$DB->SQL($query);
		$DB->Query();
		while($categorie = $DB->nextRecord())
		{
		  $categorien[]=$categorie['AttributieCategorie'];
		  $this->categorieOmschrijving[$categorie['AttributieCategorie']]=$categorie['Omschrijving'];
		}
		$this->categorieOmschrijving['Liquiditeiten']='Liquiditeiten';
		$this->categorieOmschrijving['Totaal']='Totaal';
//		if(count($categorien) >0)
//		  $categorien[]='Liquiditeiten';
  	$categorien[] = 'Totaal';

 $index=new indexHerberekening();
 $rendamentWaarden = $index->getWaardenATT('2005-01-01' ,$this->rapportageDatum ,$this->portefeuille,$categorien,'kwartaal');

foreach ($rendamentWaarden as $categorie=>$catWaarden)
{
  $nietVerwijderen = false;
  foreach ($catWaarden as $id=>$waarden)
  {
    if($waarden['waardeHuidige'] <> 0)
      $nietVerwijderen = true;
  }
  if($nietVerwijderen == false)
  {
    unset($rendamentWaarden[$categorie]);
    unset($categorien[array_search($categorie,$categorien)]);
  }
}


foreach ($rendamentWaarden as $categorie=>$waarden)
{
  $jaarIndex[$categorie] = 100;
  $kwartaalIndex[$categorie] = 100;
 // listarray($jaarUltimo['indexKwartaal']);flush();ob_flush();
  foreach ($waarden as $index)
  {

    $stortingenOnttrekkingen[$categorie] += $index['stortingen']-$index['onttrekkingen'];
    $stortingenOnttrekkingenTotaal[$categorie] += $index['stortingen']-$index['onttrekkingen'];
    $resultaat[$categorie] += $index['resultaatVerslagperiode'];
    $resultaatTotaal[$categorie] += $index['resultaatVerslagperiode'];

    $jaarIndex[$categorie] = ($jaarIndex[$categorie]  * (100+$index['performance'])/100);
    $kwartaalIndex[$categorie] = ($kwartaalIndex[$categorie]  * (100+$index['performance'])/100);
//echo $index['datum']." $categorie ".$kwartaalIndex[$categorie]." = (".$kwartaalIndex[$categorie]." * (100+".$index['performance'].")/100); <br>\n";
    if($index['datum'] == $this->rapportageDatum)
    {
      $jaarUltimo['waarde'][$index['datum']][$categorie] = $index['waardeHuidige'];
      $jaarUltimo['index'][$index['datum']][$categorie] = $index['index'];
      $jaarUltimo['stortingenOnttrekkingen'][$index['datum']][$categorie] = $stortingenOnttrekkingen[$categorie];
      $jaarUltimo['resultaat'][$index['datum']][$categorie] = $resultaat[$categorie];
      $jaarUltimo['indexJaar'][$index['datum']][$categorie] = $jaarIndex[$categorie]-100;
   //   $stortingenOnttrekkingen[$categorie]=0;
   //   $resultaat[$categorie]=0;
      $jaarUltimo['indexKwartaal'][$index['datum']][$categorie] = $kwartaalIndex[$categorie]-100;
    //  $kwartaalIndex[$categorie] = 100;

    }

    if($index['datum'] == $this->rapJaar.'-12-31')
    {
      $jaarUltimo['waarde'][$index['datum']][$categorie] = $index['waardeHuidige'];
      $jaarUltimo['index'][$index['datum']][$categorie] = $index['index'];
      $jaarUltimo['indexKwartaal'][$index['datum']][$categorie] = $kwartaalIndex[$categorie]-100;

      $kwartaalIndex[$categorie] = 100;
      $jaarUltimo['resultaat'][$index['datum']][$categorie] = $resultaat[$categorie];

    }
    
    if(substr($index['datum'],4)== '-12-31')
    {
      $jaarUltimo['waarde'][$index['datum']][$categorie] = $index['waardeHuidige'];
      $jaarUltimo['index'][$index['datum']][$categorie] = $index['index'];
      $jaarUltimo['stortingenOnttrekkingen'][$index['datum']][$categorie] = $stortingenOnttrekkingen[$categorie];
      /*
        2-2-2008 cvs
        TODO waarom word er 100 van het resultaat afgetrokken??
      */
      //$jaarUltimo['resultaat'][$index['datum']][$categorie] = $resultaat[$categorie]-100;
      $jaarUltimo['resultaat'][$index['datum']][$categorie] = $resultaat[$categorie];
      $jaarUltimo['indexJaar'][$index['datum']][$categorie] = $jaarIndex[$categorie]-100;
      $stortingenOnttrekkingen[$categorie]=0;
      $resultaat[$categorie]=0;
      $jaarIndex[$categorie] = 100;
      $kwartaalIndex[$categorie] = 100;
    }
/*
    if($index['datum'] == $this->rapJaar.'-09-30')
    {
      $jaarUltimo['waarde'][$index['datum']][$categorie] = $index['waardeHuidige'];
      $jaarUltimo['index'][$index['datum']][$categorie] = $index['index'];
      $jaarUltimo['indexKwartaal'][$index['datum']][$categorie] = $kwartaalIndex[$categorie] -100;
      $kwartaalIndex[$categorie] = 100;
      $jaarUltimo['resultaat'][$index['datum']][$categorie] = $resultaat[$categorie];
      $jaarUltimo['stortingenOnttrekkingen'][$index['datum']][$categorie] = $stortingenOnttrekkingen[$categorie];
    }

    if($index['datum'] == $this->rapJaar.'-06-30')
    {
      $jaarUltimo['waarde'][$index['datum']][$categorie] = $index['waardeHuidige'];
      $jaarUltimo['index'][$index['datum']][$categorie] = $index['index'];
      $jaarUltimo['indexKwartaal'][$index['datum']][$categorie] = $kwartaalIndex[$categorie]-100;
      $kwartaalIndex[$categorie] = 100;
      $jaarUltimo['resultaat'][$index['datum']][$categorie] = $resultaat[$categorie];
      $jaarUltimo['stortingenOnttrekkingen'][$index['datum']][$categorie] = $stortingenOnttrekkingen[$categorie];
    }

    if($index['datum'] == $this->rapJaar.'-03-31')
    {
      $jaarUltimo['waarde'][$index['datum']][$categorie] = $index['waardeHuidige'];
      $jaarUltimo['index'][$index['datum']][$categorie] = $index['index'];
      $jaarUltimo['indexKwartaal'][$index['datum']][$categorie] = $kwartaalIndex[$categorie]-100;
      $kwartaalIndex[$categorie] = 100;
      $jaarUltimo['resultaat'][$index['datum']][$categorie] = $resultaat[$categorie];
      $jaarUltimo['stortingenOnttrekkingen'][$index['datum']][$categorie] = $stortingenOnttrekkingen[$categorie];
     // $jaarUltimo['waarde'][$this->rapJaar.'-01-01'][$categorie] = $index['waardeBegin'];
    }
    */
  }
}
/*
$maand = date("n",$this->pdf->rapport_datum);
if($maand >= 9)
{
  unset($jaarUltimo['waarde'][$this->rapJaar.'-03-31']);
  unset($jaarUltimo['index'][$this->rapJaar.'-03-31']);
}
*/
 $jaarUltimo['stortingenOnttrekkingen']['cumulatief']['Totaal'] = $stortingenOnttrekkingenTotaal['Totaal'];
 $jaarUltimo['resultaat']['cumulatief']['Totaal'] = $resultaatTotaal['Totaal'];

 $alleDatums = array_merge(array_keys($jaarUltimo['waarde']),array_keys($beleggingsplan));

 $alleDatums[] = date("Y-m-d",$this->pdf->rapport_datum);
 foreach ($alleDatums as $key=>$datum) //Tussenpunten bepalen.
 {
   unset($startDatum);
   unset($eindDatum);
   if($beleggingsplan[$datum])
     $nieuwPlan[$datum]['Waarde'] = $beleggingsplan[$datum]['Waarde'];
   else
   {
     foreach ($beleggingsplan as $planDatum=>$planWaarden)
     {
       if(db2jul($planDatum) <= db2jul($datum))
         $startDatum = db2jul($planDatum);
       if(db2jul($planDatum) >= db2jul($datum) && !isset($eindDatum))
         $eindDatum = db2jul($planDatum);
     }
     $periode = $eindDatum-$startDatum;
     $deel = db2jul($datum)-$periode-$startDatum;
     $factor = (1+($periode/$deel))*-1;
     if($startDatum > 0)
       $waarde = (($beleggingsplan[jul2sql($eindDatum)]['Waarde']-$beleggingsplan[jul2sql($startDatum)]['Waarde'])*$factor)+$beleggingsplan[jul2sql($startDatum)]['Waarde'];
     $nieuwPlan[$datum]['Waarde'] = $waarde;

   }
 }
 $beleggingsplan = $nieuwPlan;

//   if(round($nieuwPlan[date("Y-m-d",$this->pdf->rapport_datum)]['Waarde']) > 0 )
//   {
     $plankader['RISM']['waarde'] = $nieuwPlan[date("Y-m-d",$this->pdf->rapport_datum)]['Waarde'];
     $plankader['Totaal']['percentage'] =   $this->formatGetal($ProcentArray["ProcentRisicoDragend"]+$ProcentArray["ProcentRisicoMijdend"],1)."%";
     $plankader['RISD']['percentage'] =   $this->formatGetal($ProcentArray["ProcentRisicoDragend"],1)."%";
     $plankader['RISM']['percentage'] = $this->formatGetal($ProcentArray["ProcentRisicoMijdend"],1)."%";
//   }

krsort($jaarUltimo['waarde']);
//exit;
   $kolW=19;
	 $this->pdf->SetWidths(array(45,35,20+15,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW));
	 $this->pdf->SetAligns(array('L','C','R','C','C','C','C'));

   $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
	 $this->pdf->row(array('Allocatie belegbaar vermogen','','','', '','' ));

	 $kopWaarden[0] = array();
	 $kopWaarden[1] = array('','Beleggingsplan',"Waarde per ".date("d-m-y",$this->pdf->rapport_datum));
	 $waardenIndex = array('','planEuro','planPercentage',date("Y-m-d",$this->pdf->rapport_datum),date("Y-m-d",$this->pdf->rapport_datum));
	 $borders = array(array('L','T','R','U'),array('L','T','R','U'),array('L','T','R','U'),array('L','T','R','U'));
	 $maand = date("n",$this->pdf->rapport_datum);
	 if($maand==12)
	  $n = 1;
	 else
	  $n = 0;
/*
	 if($maand > 9 && $n <2)
	 {
	   $n++;
     $kwartalen[] = date('d-m-y',mktime(0,0,0,10,0,$this->rapJaar));
     $waardenIndex[] =date('Y-m-d',mktime(0,0,0,10,0,$this->rapJaar));
     $n=2;
   }
   if ($maand > 6 && $n <2)
   {
     $n++;
     $kwartalen[] = date('d-m-y',mktime(0,0,0,7,0,$this->rapJaar));
     $waardenIndex[] =date('Y-m-d',mktime(0,0,0,7,0,$this->rapJaar));
     $n=2;
   }
   if($maand > 3 && $n <2)
   {
     $n++;
     $kwartalen[] = date('d-m-y',mktime(0,0,0,4,0,$this->rapJaar));
     $waardenIndex[] =date('Y-m-d',mktime(0,0,0,4,0,$this->rapJaar));
   }


   foreach ($kwartalen as $datum)
   {
      array_push($kopWaarden[1],$datum ) ;
      array_push($borders,array('T','R','U'));
   }
   foreach ($jaarUltimo['waarde'] as $datum=>$waarden)
   {
     if(substr($datum,4)== '-12-31' && substr($datum,0,4) != $this->rapJaar)
     {
        array_push($kopWaarden[1],"Ult. ".substr($datum,0,4)) ;
        array_push($borders,array('T','R','U'));
        $waardenIndex[] = $datum;
     }
   }
*/
	//  $this->pdf->row($kopWaarden[0]);

	  $this->pdf->SetFillColor(234,230,223);
	  $this->pdf->fillCell = array(1,1,1,1,0,0,0,0,0,0);
	  $this->pdf->SetAligns(array('L','C','R','R','R','R','R','R','R','R','R','R'));
	  $this->pdf->CellBorders=$borders;
	  $this->pdf->row($kopWaarden[1]);
	  $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetWidths(array(45,25,10,20,15,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW,$kolW));

  unset($this->pdf->fillCell);
   //  $this->pdf->fillCell = array(0,0,0,1,1,0,0,0);

   $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

$n = count($kopWaarden[1])+2;
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

$this->pdf->rowHeight = 6;

$borders2=array(array("L",'R'));
$borders3=array(array("L",'R','U'));
$tmp=array('Belegbaar Vermogen');
for($x=0;$x<count($borders);$x++)
{
  
  
  if($x==0)
  {
    array_push($borders3,array('U'));
    array_push($borders2,'');
  }
  else
  {
    array_push($borders3,array('R','U'));
    array_push($borders2,'R');
  }

  array_push($tmp,'');

}
$this->pdf->CellBorders=$borders2;
$this->pdf->row($tmp);


$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

//$this->pdf->rowHeight = 8;
$x=1;
$regelnr = count($categorien);
foreach ($categorien as $categorie)
{
  if($x==$regelnr)
    $this->pdf->CellBorders=$borders3;

  $tmp = array();
  for($index=0;$index<$n;$index++)
  {
   if($index == 0)
     array_push($tmp,$this->categorieOmschrijving[$categorie]);
   elseif($index == 1)
   {
    // if($categorie =='RISM')
    //   array_push($tmp,$this->formatGetal($plankader[$categorie]['waarde'],0));
    // else
       array_push($tmp,$plankader[$categorie]['percentage']);//beleggingsplan percentage
       //array_push($tmp,'');//beleggingsplan euro
   }
   elseif($index == 2)
     array_push($tmp,'');//array_push($tmp,$plankader[$categorie]['percentage']);//beleggingsplan percentage
   elseif($index == 3)
     array_push($tmp,$this->formatGetal($jaarUltimo['waarde'][$waardenIndex[$index]][$categorie],0));//Waarde RapportageDatum
   elseif($index == 4)
     array_push($tmp,$this->formatGetal($jaarUltimo['waarde'][$waardenIndex[$index]][$categorie]/$jaarUltimo['waarde'][$waardenIndex[$index]]['Totaal']*100,1)."%");//RapportageDatum percentage
   else
      array_push($tmp,$this->formatGetal($jaarUltimo['waarde'][$waardenIndex[$index]][$categorie],0));

  }
   if($categorie == "Totaal")
     $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
   else
     $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
     $x++;
	$this->pdf->row($tmp);
}


$this->pdf->fillCell = array();
$this->pdf->setY(90);
$this->pdf->ln();

if(count($jaarUltimo['resultaat']) > 12)
  $this->pdf->addPage();

$this->pdf->CellBorders=array(array('T','L','R'),array('T','L','R'),array('T','L','R'),array('T','L','R')  ,array('T','L','R'),array('T','R','L'),array('T','R','L'),array('T','R','L'));
$this->pdf->SetWidths(array(25,35,35,35,   22+25,  22+25,  22+25,  22+22));
$this->pdf->SetAligns(array('L', 'C','C',  'C','C',  'C','C'));
$tmp=array(' ','Belegbaar vermogen','Resultaat in jaar','Stortingen en');
foreach ($categorien as $categorie)
  array_push($tmp,$this->categorieOmschrijving[$categorie]);
$this->pdf->fillCell = array(1,1,1,1,1,1,1,1);
$this->pdf->row($tmp);

$this->pdf->SetWidths(array(25,35,35,35,   22,25,  22,25,  22,25,  22,25));
$this->pdf->SetAligns(array('L','R','R','C', 'C','C',  'C','C',  'C','C'));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
//$this->pdf->CellBorders=array(array('T','L','R','U'),array('T','R','U','L'),array('T','R','U','L'),array('T','R','U','L'));
$this->pdf->CellBorders=array(array('L','R','U'),array('R','U','L'),array('R','U','L'),array('R','U','L'));
$this->pdf->rowHeight=4;

$this->pdf->SetFillColor(234,230,223);
$this->pdf->fillCell = array(1,1,1,1,1,1,1,1,1,1);

 if(count($categorien) == 3)
{
  foreach(array(array('U','L'),'U',array('U','L'),'U',array('U','L'),array('U','R')) as $border)
    $this->pdf->CellBorders[]=$border;
  $tmp=array(" \n ","\n ","\n ","onttrekkingen\n ");
  foreach(array("Index\n ","Vanaf begin\njaar","Index\n ","Vanaf begin\njaar","Index\n ","Vanaf begin\njaar") as $kop)
    $tmp[]=$kop;


}
else
{
  
    foreach(array(array('U','L'),'U',array('U','L'),array('U','R')) as $border)
    $this->pdf->CellBorders[]=$border;
  $tmp=array(" \n ","\n ","\n ","onttrekkingen\n ");
  foreach(array("Index\n ","Vanaf begin\njaar","Index\n ","Vanaf begin\njaar") as $kop)
    $tmp[]=$kop;
//$this->pdf->CellBorders=array(array('U','L'),array('U','L'),'U',array('U','L'),'U',array('U','R'));
//$this->pdf->row(array(" \n ","Index\n ","Vanaf begin\njaar","Index\n ","Vanaf begin\njaar"));
//$this->pdf->CellBorders=array('L','L','','L','R');
}  

$this->pdf->row($tmp);
$this->pdf->SetAligns(array('L','R','R','R', 'C','C',  'C','C',  'C','C'));
//$this->pdf->row();



 $this->pdf->rowHeight=6;
$this->pdf->CellBorders=array(array('L','R'),array('R'),array('R'),array('R'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

 $this->pdf->fillCell = array();
 
$regelnr= count($jaarUltimo['index']);
$n=0;


 foreach ($jaarUltimo['resultaat'] as $datum=>$waarden)
 {
   if($datum == 'cumulatief')
   {
     $periode = 'Cumulatief';
     $this->pdf->CellBorders=array(array('L','R','U','T'),array('R','U','T'),array('R','U','T'),array('R','U','T'),
     array('U','T'),array('R','U','T'),array('U','T'),array('R','U','T'),array('U','T'),array('R','U','T'));
   }
   elseif($datum == date("Y-m-d",$this->pdf->rapport_datum))
   {
     $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
     $periode = date("d-m-Y",$this->pdf->rapport_datum);
     $extraLine = true;
   }
   else
   {
     if(substr($datum,5,5) == '12-31')
        $this->pdf->CellBorders=array(array('L','U','R'),array('R','U'),array('R','U'),array('R','U'),'U',array('U','R'),'U',array('U','R'),'U',array('U','R'));
     else
       $this->pdf->CellBorders=array(array('L','R'),array('R'),array('R'),array('R'),array('R'),array('R'),array('R'));

     $periode = jul2form(db2jul($datum));//'31 december '.substr($datum,0,4);
   }
  
   $tmp=array($periode,$this->formatGetal($jaarUltimo['waarde'][$datum]['Totaal']),
   	                      $this->formatGetal($jaarUltimo['resultaat'][$datum]['Totaal']),
   	                      $this->formatGetal($jaarUltimo['stortingenOnttrekkingen'][$datum]['Totaal']));
           
           



  //$tmp = array(date("d-m-Y",db2jul($datum)));
  foreach ($categorien as $categorie)
  {
    array_push($tmp,$this->formatGetal($jaarUltimo['index'][$datum][$categorie],1));
    array_push($tmp,$this->formatGetal($jaarUltimo['indexJaar'][$datum][$categorie],1,true));
  }
  $n++;

  if($n==$regelnr)
  {
    if(count($categorien) == 2)
     $this->pdf->CellBorders=array(array('L','U','R'),array('R','U'),array('R','U'),array('R','U'),array('U'),array('U','R'),'U',array('U','R'),'U',array('U','R'));
    else
     $this->pdf->CellBorders=array(array('L','U','R'),array('R','U'),array('R','U'),array('R','U'),array('U'),array('U','R'),'U',array('U','R'),'U',array('U','R'));
  }

            
                          
                          
   	$this->pdf->row($tmp);
                          
                          
                          
                          
                          

   	if($extraLine == true)
   	{
   	  $extraLine = false;

   	 $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
   	}
 }
	  $this->pdf->rowHeight=4;
ksort($jaarUltimo['waarde']);
 //flush();


/*
 $this->pdf->setXY(170,160);
$this->VBarDiagram(100,50,$jaarUltimo['waarde'],$beleggingsplan);
*/

/*
$this->pdf->last_rapport_type = $this->pdf->rapport_type;
$this->pdf->last_rapport_titel = $this->pdf->rapport_titel;

$this->pdf->addPage();

$this->pdf->SetWidths(array(30,40,40,40,40));
$this->pdf->SetAligns(array('L','C','C','C'));

$borders = array();
foreach ($categorien as $categorie)
  array_push($borders,array('T','L'));
array_push($borders,array('T','L','R'));
$this->pdf->CellBorders=$borders;

$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);

 $this->pdf->SetFillColor(234,230,223);
$tmp = array('Datum');
foreach ($categorien as $categorie)
  array_push($tmp,$this->categorieOmschrijving[$categorie]);
$this->pdf->fillCell = array(1,1,1,1,1);
$this->pdf->row($tmp);
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


$this->pdf->SetAligns(array('L','C','C',  'C','C',  'C','C'));
$this->pdf->SetWidths(array(30,  20,20,  20,20,  20,20,  20,20));
$this->pdf->fillCell = array(1,1,1,1,1,1,1);



if(count($categorien) == 3)
{
$this->pdf->CellBorders=array(array('U','L'),array('U','L'),'U',array('U','L'),'U',array('U','L'),'U',array('U','R'));
$this->pdf->row(array(" \n ","Index\n ","Vanaf begin\njaar",
                         "Index\n ","Vanaf begin\njaar",
                         "Index\n ","Vanaf begin\njaar"));
$this->pdf->CellBorders=array('L','L','','L','','L','R');
}
else
{
$this->pdf->CellBorders=array(array('U','L'),array('U','L'),'U',array('U','L'),'U',array('U','R'));
$this->pdf->row(array(" \n ","Index\n ","Vanaf begin\njaar","Index\n ","Vanaf begin\njaar"));
$this->pdf->CellBorders=array('L','L','','L','R');
}

$this->pdf->fillCell = array();
$this->pdf->SetAligns(array('L','R','R',  'R','R',  'R','R',  'R','R'));
*/


/*
// toevoegen als eerste waarde vorige jaar met index 100
// cvs 27 jan 2009
reset($jaarUltimo['index']);
$StartUlti[key($jaarUltimo['index'])] = $jaarUltimo['index'][key($jaarUltimo['index'])];
$tmpKey = substr(key($jaarUltimo['index']),0,4)-1;
$tmpKey .= "-12-31";

$tmpArray = array_reverse($jaarUltimo['index']);

foreach ($categorien as $categorie)
  $tmpArray[$tmpKey][$categorie]=100;
//$tmpArray[$tmpKey] = array('RISD'=>100,'RISM'=>100,'Totaal'=>100);
$jaarUltimo['index'] =  array_reverse($tmpArray);
*/

/*
$regelnr= count($jaarUltimo['index']);
$n=0;


foreach ($jaarUltimo['index'] as $datum=>$waarden)
{
  $tmp = array(date("d-m-Y",db2jul($datum)));
  foreach ($categorien as $categorie)
  {
    array_push($tmp,$this->formatGetal($waarden[$categorie],1));
    array_push($tmp,$this->formatGetal($jaarUltimo['indexJaar'][$datum][$categorie],1,true));
  }
  $n++;

  if($n==$regelnr)
  {
    if(count($categorien) == 2)
     $this->pdf->CellBorders=array(array('U','L'),array('U','L'),'U',array('U','L'),'U',array('U','R'));
    else
     $this->pdf->CellBorders=array(array('U','L'),array('U','L'),'U',array('U','L'),'U',array('U','L'),'U',array('U','R'));
  }
  $this->pdf->row($tmp);
}
$this->pdf->CellBorders=array();
*/

//listarray($jaarUltimo);ob_flush();
/*
$this->pdf->setY(100);
$this->pdf->SetWidths(array(35,25,20,25,20));

if(round($jaarUltimo['waarde'][$this->rapportageDatumVanaf]['Totaal'],2) == 0.00)
{
  	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaal  FROM TijdelijkeRapportage WHERE rapportageDatum ='".$this->rapportageDatumVanaf."' AND  portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$jaarUltimo['waarde'][$this->rapportageDatumVanaf]['Totaal'] = $totaalWaarde['totaal'];
}

$periodeWaardemutatie = $jaarUltimo['waarde'][$this->rapportageDatum]['Totaal']-$jaarUltimo['waarde'][$this->rapportageDatumVanaf]['Totaal'];
$periodeStortingen = getStortingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
$periodeOnttrekkingen = getOnttrekkingen($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta);
//$periodeProcent  	= performanceMeting($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);

$ongerealiseerdeKoersResultaat = $this->ongerealiseerdeKoersResultaat();
$this->totaalOpbrengst += $ongerealiseerdeKoersResultaat;
$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($this->portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum,$this->pdf->rapportageValuta,true);
$this->totaalOpbrengst += $gerealiseerdeKoersResultaat;

if($jaarUltimo['waarde'][($this->rapJaar).'-01-01']['Totaal'])
  $januariWaarde=$jaarUltimo['waarde'][($this->rapJaar).'-01-01']['Totaal'];
else
  $januariWaarde=$jaarUltimo['waarde'][($this->rapJaar-1).'-12-31']['Totaal'];

$jaarWaardemutatie = $jaarUltimo['waarde'][$this->rapportageDatum]['Totaal']-$januariWaarde;
$jaarStortingen = getStortingen($this->portefeuille,$this->rapJaar.'-01-01',$this->rapportageDatum,$this->pdf->rapportageValuta);
$jaarOnttrekkingen = getOnttrekkingen($this->portefeuille,$this->rapJaar.'-01-01',$this->rapportageDatum,$this->pdf->rapportageValuta);
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize +2);


$this->pdf->rowHeight = 6;
$this->pdf->SetWidths(array(130,100));
$this->pdf->row(array('Beleggingsresultaat','Ontwikkeling index beleggingsresultaat'));
$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
$this->pdf->SetWidths(array(35+25+20,25+20));
$this->pdf->SetAligns(array('R','R'));
$this->pdf->CellBorders=array(array('L','T'),array('L','R','T'));
// cvs 5 feb 2009 fill header
$this->pdf->SetFillColor(234,230,223);
$this->pdf->fillCell = array(1,1);
$this->pdf->row(array(jul2form(db2jul($this->rapportageDatumVanaf)).' tot en met '.jul2form(db2jul($this->rapportageDatum)),'Cumulatief '.$this->rapJaar));
$this->pdf->fillCell = array();
$this->pdf->SetAligns(array('L','R','R','R','R'));
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->SetWidths(array(35,25,20,25,20));
$this->pdf->rowHeight = 8;
$this->pdf->CellBorders=array(array('L','T'),array('R','T'),array('R','T'),array('R','T'),array('R','T'),array('R','T'));
//listarray($jaarUltimo);exit;
$this->pdf->row(array('Belegbaar vermogen',jul2form(db2jul($this->rapportageDatumVanaf)),$this->formatGetal($jaarUltimo['waarde'][$this->rapportageDatumVanaf]['Totaal']),jul2form(db2jul($this->rapJaar.'-01-01')),$this->formatGetal($januariWaarde)));

$this->pdf->CellBorders=array('L','R','R','R','R','R');
$this->pdf->row(array('Stortingen','',$this->formatGetal($periodeStortingen),'',$this->formatGetal($jaarStortingen)));
$this->pdf->row(array('Onttrekkingen','',$this->formatGetal($periodeOnttrekkingen*-1),'',$this->formatGetal($jaarOnttrekkingen*-1)));
$this->pdf->row(array('Resultaat','',$this->formatGetal($periodeWaardemutatie - $periodeStortingen + $periodeOnttrekkingen),'',$this->formatGetal($jaarWaardemutatie - $jaarStortingen + $jaarOnttrekkingen)));
$this->pdf->CellBorders=array(array('L','U'),array('R','U'),array('R','U'),array('R','U'),array('R','U'),array('R','U'));
$this->pdf->row(array('Belegbaar vermogen',jul2form(db2jul($this->rapportageDatum)),$this->formatGetal($jaarUltimo['waarde'][$this->rapportageDatum]['Totaal']),jul2form(db2jul($this->rapportageDatum)),$this->formatGetal($jaarUltimo['waarde'][$this->rapportageDatum]['Totaal'])));

$this->pdf->SetWidths(array(60,20,25,20));

//listarray($jaarUltimo['index']);exit;

foreach ($jaarUltimo['index'] as $datum=>$waarden)
{
  if(substr($datum,5)=='12-31' || $datum==$this->rapportageDatumVanaf || $datum==$this->rapportageDatum)
  {
    foreach ($waarden as $categorie=>$waarde)
    {
     $grafiekData['grafiek'][$categorie][]= $waarde;
    }
    $grafiekData['datum'][] = $datum;
  }

}

//$data=array('grafiek'=>array('RISD'=>array(100,120,110),'RISM'=>array(90,100,120)),'datum'=>array());
//$this->pdf->setXY(170,110);
//$this->LineDiagram(100,50,$grafiekData);
*/
$this->pdf->CellBorders=array();
$this->pdf->rowHeight=4;

//$this->rapJaar.'-01-01'

//listarray($jaarUltimo);ob_flush();

	}

/*
  function VBarDiagram($w, $h, $data,$beleggingsplan)
  {
      global $__appvar;
      $legendaWidth = 0;
      $grafiekPunt = array();

      foreach ($data as $datum=>$waarden)
        if(substr($datum,5)=='12-31' || $datum==$this->rapportageDatumVanaf || $datum==$this->rapportageDatum || $datum == $this->planEind)
          $newData[$datum]=$waarden;
      $data=$newData;
      foreach ($beleggingsplan as $datum=>$waarden)
        if(substr($datum,5)=='12-31' || $datum==$this->rapportageDatumVanaf || $datum==$this->rapportageDatum || $datum == $this->planEind)
          $newData[$datum]=$waarden;
      $beleggingsplan=$newData;

      foreach ($data as $datum=>$waarden)
        foreach ($waarden as $categorie=>$waarde)
           $categorien[] =   $categorie;

      foreach ($beleggingsplan as $datum=>$d)
        foreach ($categorien as $categorie)
          if(db2jul($datum) > $this->pdf->rapport_datum)
            $data[$datum][$categorie] = 0;
      $categorien = array();

      foreach ($data as $datum=>$waarden)
      {
        $legenda[] = jul2form(db2jul($datum));
        $n=0;
        foreach ($waarden as $categorie=>$waarde)
        {
          $grafiek[$n][]=$waarde;
          $categorien[$categorie] = $n;
          $categorieId[$n]=$categorie ;
          $grafiekPunt[$n][] =  $beleggingsplan[$datum]['Waarde'];
          $n++;
        }
      }

      $numBars = count($legenda);

      if($color == null)
      {
        $color=array(155,155,155);



        $colors[]=array(220,213,202);
        $colors[]=array(103,89,73);
        $colors[]=array(228,23,112);
        $colors[]=array(255,255,155);
        $colors[]=array(155,255,255);
      }
      if ($maxVal == 0)
      {

        foreach ($grafiek as $g)
          foreach ($g as $val)
           $maxVal = ceil(max($val,$maxVal));

        foreach ($beleggingsplan as $datum=>$waarde)
          $maxVal = ceil(max($waarde['Waarde'],$maxVal));

        // ceil(max($grafiek[$categorien['Totaal']]));
        $digits = strlen($maxVal);
        $firstdigit = ceil(substr($maxVal,0,2)/10);
        $maxVal = $firstdigit.str_repeat(0,($digits-1));
      }
      $minVal = floor(min($grafiek[$categorien['Totaal']]));

      unset($grafiek[$categorien['Totaal']]);

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      $XPage = $this->pdf->GetX();
      $YPage = $this->pdf->GetY();
      $margin = 2;
      $YstartGrafiek = $YPage - floor($margin * 1);
      $hGrafiek = ($h - $margin * 1);
      $XstartGrafiek = $XPage + $margin * 1 ;
      $bGrafiek = ($w - $margin * 1) - $legendaWidth; // - legenda

      $n=0;
      foreach ($grafiek as $id =>$waarden)
      {
//        $this->pdf->Rect($XstartGrafiek + $bGrafiek +2, $n*4 +$YstartGrafiek-$hGrafiek , 2, 2, 'DF',null,$colors[$id]);
//        $this->pdf->SetXY($XstartGrafiek + $bGrafiek +6, $n*4 +$YstartGrafiek-$hGrafiek -0.75 );
//        $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorieId[$id]],0,0,'L');

        $this->pdf->Rect($XstartGrafiek-5 +$n*45 , $YstartGrafiek-$hGrafiek-10, 2, 2, 'DF',null,$colors[$id]);
        $this->pdf->SetXY($XstartGrafiek-1+$n*45 ,$YstartGrafiek-$hGrafiek-10.75 );
        $this->pdf->Cell(20, 3,$this->categorieOmschrijving[$categorieId[$id]],0,0,'L');
        $n++;
      }


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

  $stapgrootte = (abs($bereik)/$horDiv);
  $top = $YstartGrafiek-$h;
  $bodem = $YstartGrafiek;
  $absUnit =abs($unit);

$nulpunt = $YstartGrafiek + $nulYpos;
$n=0;



$n=0;
  for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
  {
    $this->pdf->Line($XstartGrafiek, $i, $XstartGrafiek + $bGrafiek ,$i,array('dash' => 1,'color'=>array(0,0,0)));
    //  $this->pdf->Text($XstartGrafiek-10, $i,$this->formatGetal($n*$stapgrootte));
    $this->pdf->SetXY($XstartGrafiek-20, $i-1);
    $this->pdf->Cell(20, 3, $this->formatGetal($n*$stapgrootte),0,0,'R');
    $n++;
    if($n >20)
      break;
  }


if($numBars > 0)
  $this->pdf->NbVal=$numBars;

        $vBar = ($bGrafiek / ($this->pdf->NbVal + 1));
        $bGrafiek = $vBar * ($this->pdf->NbVal + 1);
        $eBaton = ($vBar * 80 / 100);


      $this->pdf->SetLineStyle(array('dash' => 0,'color'=>array(0,0,0)));
      $this->pdf->SetLineWidth(0.2);

      $this->pdf->SetFillColor($color[0],$color[1],$color[2]);
      $i=0;
//   foreach ($grafiek as $id=>$data)

   for($id=count($grafiek);$id>=0;$id--)
   {
      $data = $grafiek[$id];
      foreach($data as $key=>$val)
      {
        if(!isset($YstartGrafiekLast[$key]))
          $YstartGrafiekLast[$key] = $YstartGrafiek;
          //Bar
          $xval = $XstartGrafiek + ($i + 1) * $vBar - $eBaton / 2;
          $lval = $eBaton;
          $yval = $YstartGrafiekLast[$key] + $nulYpos ;
          $hval = ($val * $unit);
          $this->pdf->Rect($xval, $yval, $lval, $hval, 'DF',null,$colors[$id]);
          $YstartGrafiekLast[$key] = $YstartGrafiekLast[$key]+$hval;


         if($legendaPrinted[$key] != 1)
         {
           $this->pdf->TextWithRotation($xval,$YstartGrafiek+10,$legenda[$key],45);
         }

         if($id==0)
         {
           if($grafiekPunt[0][$key] > 0)
           {
             $this->pdf->Rect($xval+.5*$eBaton-.5, $grafiekPunt[0][$key] * $unit + $YstartGrafiek -.5 , 1, 1, 'DF',null,array(128,128,128));
             if($lastX)
              $this->pdf->line($lastX,$lastY,$xval+.5*$eBaton, $grafiekPunt[0][$key] * $unit + $YstartGrafiek);
             $lastX = $xval+.5*$eBaton;
             $lastY = $grafiekPunt[0][$key] * $unit + $YstartGrafiek;
           }
         }
         $legendaPrinted[$key] = 1;
         $i++;
      }
      $i=0;
   }

      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);


  }

  function ongerealiseerdeKoersResultaat()
  {
    global $__appvar;
    $DB = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind."  AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro)/ ".$this->pdf->ValutaKoersStart."  AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND "
						 ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$ongerealiseerdeKoersResultaat = $totaal[totaalB] - $totaal[totaalA];
		return $ongerealiseerdeKoersResultaat;
  }

  function opgelopenRente()
  {
    global $__appvar;
    $DB = new DB();
  		// ophalen van rente totaal A en rentetotaal B
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalA = $DB->nextRecord();

		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 " type = 'rente' ". $__appvar['TijdelijkeRapportageMaakUniek'] ;
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalB = $DB->nextRecord();

		$opgelopenRente = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
		return $opgelopenRente;
  }

  function opbrengstenPerGrootboek()
  {
    	$query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving".
		" FROM Grootboekrekeningen ".
		" WHERE Grootboekrekeningen.Opbrengst = '1'  ".
		" ORDER BY Grootboekrekeningen.Afdrukvolgorde";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT  ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$this->totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}
		return $opbrengstenPerGrootboek;
  }

  function kostenPerGrootboek()
  {
      $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "Grootboekrekeningen.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();

		while($kosten = $DB->nextRecord())
		{
			if($kosten[Grootboekrekening] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Omschrijving] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten[Grootboekrekening]][Bedrag] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}

			$this->totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}

		return $kostenPerGrootboek;
  }

  function LineDiagram($w, $h, $data)
  {
    global $__appvar;

    $legendaWidth = 30;

     $color=null; $maxVal=0; $minVal=0; $horDiv=10; $verDiv=4;$jaar=0;
    $datum = $data['datum'];
    $categorieOmschrijving = $data['categorien'];
    $data = $data['grafiek'];
    $bereikdata = array();
    if(count($data)>0)
    {
      foreach ($data as $cat=>$waarden)
      {
        $aantal = max(count($waarden),$aantal);
        $bereikdata = array_merge($bereikdata,$data[$cat]);
      }
    }
    else
      $bereikdata = $data;

    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $YDiag = $YPage + $margin;
    $hDiag = floor($h - $margin * 1);
    $XDiag = $XPage + $margin * 1 ;
    $lDiag = ($w - $legendaWidth);

     if($color == null)
    {
        $color=array(155,155,155);
        $colors[]=array(220,213,202);
        $colors[]=array(103,89,73);
        $colors[]=array(228,23,112);
        $colors[]=array(255,255,155);
        $colors[]=array(255,255,155);
        $colors[]=array(155,255,255);
    }



    $this->pdf->SetLineWidth(0.2);
  //  $this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);


  $this->pdf->SetFont($this->pdf->rapport_font,''.$kopStyle,$this->pdf->rapport_fontsize);

    $this->pdf->SetFillColor($color[0],$color[1],$color[2]);

    if ($maxVal == 0)
    {
      $maxVal = ceil(max($bereikdata));
      if ($maxVal < 100)
        $maxVal = 101;
    }
    if ($minVal == 0)
    {
      $minVal = floor(min($bereikdata));
      if ($minVal > 100)
        $minVal = 99;
    }

    $minVal = 100 + ($minVal-100) * 2;
    $maxVal = 100 + ($maxVal-100) * 2;


     $legendYstep = ($maxVal - $minVal) / $horDiv;

     $verInterval = ($lDiag / $verDiv);
     $horInterval = ($hDiag / $horDiv);

     $waardeCorrectie = $hDiag / ($maxVal - $minVal);

     $unit = $lDiag / $aantal;


      for ($i = 0; $i <= $verDiv; $i++) //x-as verdeling
      {
        $xpos = $XDiag + $verInterval * $i;
      }

      $this->pdf->SetFont($this->pdf->rapport_font, '', 6);
      $this->pdf->SetTextColor(0,0,0);
       $this->pdf->SetDrawColor(0,0,0);


   $stapgrootte = ceil(abs($maxVal - $minVal)/$horDiv);
   $unith = $hDiag / (-1 * $minVal + $maxVal);
  //$honderdLijn = false;





  $top = $YPage;
  $bodem = $YDiag+$hDiag;
  $absUnit =abs($unith);

$nulpunt = $YDiag + (($maxVal-100) * $waardeCorrectie);
$n=0;
//echo "$i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte ";
  for($i=$nulpunt; $i< $bodem; $i+= $absUnit*$stapgrootte)
  {
      $skipNull = true;
      $this->pdf->Line($XDiag, $i, $XPage+$lDiag+2 ,$i,array('dash' => 1,'color'=>array(0,0,0)));
      $this->pdf->Text($XDiag-7, $i, 100-($n*$stapgrootte) ." %");
      $n++;
      if($n >20)
       break;
  }
$n=0;
  for($i=$nulpunt; $i > $top; $i-= $absUnit*$stapgrootte)
  {
    $this->pdf->Line($XDiag, $i, $XPage+$lDiag+2 ,$i,array('dash' => 1,'color'=>array(0,0,0)));
    if($skipNull == true)
      $skipNull = false;
    else
      $this->pdf->Text($XDiag-7, $i, ($n*$stapgrootte)+100 ." %");
    $n++;
    if($n >20)
       break;
  }


 $n =0;

 foreach ($data as $categorie=>$grafiek)
 {
   // listarray($data1);
    $yval=$YDiag + (($maxVal-100) * $waardeCorrectie) ;
     $lineStyle = array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $colors[$n]);
      for ($i=0; $i<count($grafiek); $i++)
      {
        $yval2 = $YDiag + (($maxVal-$grafiek[$i]) * $waardeCorrectie) ;
        $this->pdf->line($XDiag+$i*$unit, $yval, $XDiag+($i+1)*$unit, $yval2,$lineStyle );
        if ($i>0)
         $this->pdf->Rect($XDiag+$i*$unit-0.5, $yval-0.5, 1, 1 ,'F','',$colors[$n]);
        $yval = $yval2;
        if(!isset($datumPrinted[$i]))
        {
          $this->pdf->TextWithRotation($XDiag+($i)*$unit+(0.5*$unit),$YDiag+$hDiag+10,jul2form(db2jul($datum[$i])),45);
          $datumPrinted[$i] = 1;
        }
      }
        $this->pdf->Rect($XDiag + $lDiag +2, $n*4 +$YDiag+2 , 2, 2, 'DF',null,$colors[$n]);
        $this->pdf->SetXY($XDiag + $lDiag +6, $n*4 +$YDiag+2 -0.75 );
        $this->pdf->Cell(20, 3,$n.' '.$this->categorieOmschrijving[$categorie],0,0,'L');
        $n++;
 }
      $this->pdf->SetLineStyle(array('color'=>array(0,0,0)));
      $this->pdf->SetFillColor(0,0,0);
  }
*/

}
?>