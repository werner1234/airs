<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/13 14:47:19 $
 		File Versie					: $Revision: 1.5 $

 		$Log: CorrelatieStdevClass.php,v $
 		Revision 1.5  2019/11/13 14:47:19  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/11/06 15:53:41  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/09/21 16:30:52  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/08/03 16:50:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/07/31 14:06:23  rvv
 		*** empty log message ***
 		
 	*/

class CorrelatieStdev
{

  function CorrelatieStdev($portefeuille,$datum)
  {
    include_once("rapportRekenClass.php");
    $this->portefeuille = $portefeuille;
    $this->rapportageDatum = $datum;
    
    $this->db = new DB();
    $this->meetpunten=array();
    $this->verdeling=array();
    $this->verdelingCategorie=array();
    $this->valutaRekeningen=array();
    $this->portefeuilleWaarde=array();
    $this->eersteKoersDatum=$this->rapportageDatum;
    $this->componenten=array();
    $this->valutas=array();
    $this->fondsKoersen=array();
    $this->koersenPerFonds=array();
    $this->rendementen=array();
    $this->correlatieMatrix=array();
    $this->correlatieMatrixOpDatum=array();
    $this->fondsStandaardDeviatie=array();
    $this->puntenPerJaar=0;
    $this->debug=true;
    $this->var=array();
    $this->std=array();
  }
  
  function bepaalPeriode($jarenTerug=5,$rapportageDatum='')
  {
    if($rapportageDatum=='')
      $rapportageDatum=$this->rapportageDatum;
    $this->meetpunten=array();
    $this->jarenTerug=$jarenTerug;
    $yearsAgo=(substr($this->rapportageDatum,0,4)-$jarenTerug).'-'.substr($rapportageDatum,5,5);
    $this->eersteKoersDatum=$yearsAgo;

  }
  
  function bepaalPortefeuilleVerdeling($datum,$categorieverdering='',$componenten=array())
  {
    if($categorieverdering=='')
      $categorieverdering='hoofdcategorie';
    if(count($componenten)==0)
    {
      $componenten = berekenPortefeuilleWaarde($this->portefeuille, $datum, 0, 'EUR', $datum);
      vulTijdelijkeTabel($componenten, $this->portefeuille, $datum);
    }
    $totaleWaarde=0;
    $totaleCategorieWaarde=array();
    $fondsWaarde=array();
    $verdeling=array();
    $categorieVerdeling=array();

    foreach($componenten as $fondsData)
    {
      if($fondsData['type']<>'rekening')
      {
        $fondsWaarde[$fondsData['fonds']] += $fondsData['actuelePortefeuilleWaardeEuro'];
      }
      $totaleWaarde += $fondsData['actuelePortefeuilleWaardeEuro'];
  
      $totaleCategorieWaarde[$fondsData[$categorieverdering]] += $fondsData['actuelePortefeuilleWaardeEuro'];

    }
    foreach($componenten as $fondsData)
    {
      if($fondsData['type']=='rekening')
      {
        $verdeling[$fondsData['valuta']] +=  $fondsData['actuelePortefeuilleWaardeEuro']/$totaleWaarde;
        $this->componenten[$fondsData['valuta']]=$fondsData['valuta'];
        $this->valutas[$fondsData['valuta']]=$fondsData['valuta'];
        $categorieVerdeling[$fondsData[$categorieverdering]][$fondsData['valuta']]+= $fondsWaarde[$fondsData['fonds']]/$totaleCategorieWaarde[$fondsData[$categorieverdering]];
      }
      elseif($fondsData['type']=='fondsen')
      {
        $verdeling[$fondsData['fonds']] += $fondsWaarde[$fondsData['fonds']]/$totaleWaarde;
        $this->componenten[$fondsData['fonds']]=$fondsData['fonds'];
        $categorieVerdeling[$fondsData[$categorieverdering]][$fondsData['fonds']]+= $fondsWaarde[$fondsData['fonds']]/$totaleCategorieWaarde[$fondsData[$categorieverdering]];
      }
    }
    $this->verdeling[$datum]=$verdeling;
    $this->verdelingCategorie[$datum]=$categorieVerdeling;
    $this->portefeuilleWaarde[$datum]=$totaleWaarde;
  }
  
  function setFondsen($datum,$categorieverdering='',$fondsen=array())
  {
    if($categorieverdering=='')
      $categorieverdering='hoofdcategorie';
    
    $totaleWaarde=0;
    $totaleCategorieWaarde=array();
    $fondsWaarde=array();
    $verdeling=array();
    $categorieVerdeling=array();
    
    foreach($fondsen as $fondsData)
    {
      if($fondsData['type']<>'rekening')
      {
        $fondsWaarde[$fondsData['fonds']] += $fondsData['actuelePortefeuilleWaardeEuro'];
      }
      $totaleWaarde += $fondsData['actuelePortefeuilleWaardeEuro'];
      $totaleCategorieWaarde[$fondsData[$categorieverdering]] += $fondsData['actuelePortefeuilleWaardeEuro'];
    }
    foreach($fondsen as $fondsData)
    {
      $verdeling[$fondsData['fonds']] += $fondsWaarde[$fondsData['fonds']]/$totaleWaarde;
      $this->componenten[$fondsData['fonds']]=$fondsData['fonds'];
      $categorieVerdeling[$fondsData[$categorieverdering]][$fondsData['fonds']]+= $fondsWaarde[$fondsData['fonds']]/$totaleCategorieWaarde[$fondsData[$categorieverdering]];
    }
    $this->verdeling[$datum]=$verdeling;
    $this->verdelingCategorie[$datum]=$categorieVerdeling;
    $this->portefeuilleWaarde[$datum]=$totaleWaarde;
  }

  
  function getKoersen($datum='')
  {

    $this->koersenPerFonds=array();

    if($datum=='')
      $rapportageDatum=$this->rapportageDatum;
    else
      $rapportageDatum=$datum;

    $valutas=array();
    $fondsen=array();
    foreach($this->componenten as $component)
    {
      if(isset($this->valutas[$component]))
      {
        $valutas[$component]=$component;
      }
      else
      {
        $fondsen[$component]=$component;
      }
    }

    $items=array('Fondskoersen'=>'Fonds','Valutakoersen'=>'Valuta');
    foreach($items as $tabel=>$veld)
    {
      if($tabel=='Fondskoersen')
        $componenten=$fondsen;
      else
        $componenten=$valutas;

      // $componenten=array('Ishares Core Eur Corp ETF','Ishares EUR Prop');
      $query = "SELECT $veld as component,Koers,Datum FROM $tabel WHERE $veld IN('" . implode("','", $componenten) . "') AND Datum >= '" . $this->eersteKoersDatum . "' AND Datum <= '" . $rapportageDatum . "' order by Datum";
      $this->db->SQL($query);
      $this->db->Query();
      while ($koers = $this->db->NextRecord())
      {
        $datumJul = db2jul($koers['Datum']);
        $this->koersenPerFonds[$koers['component']][$datumJul]=$koers['Koers'];
      }
    }
  }

  function berekenFondsRendementen($fonds1,$fonds2)
  {

    $koersen=array();
    $meetpunten=array();
    $rendementen=array();
    $eersteKoersDag='';

    foreach ($this->koersenPerFonds[$fonds1] as $dag => $koers)
    {
      if (isset($this->koersenPerFonds[$fonds2][$dag]))
      {
        if($eersteKoersDag=='')
          $eersteKoersDag=$dag;

        $koersen[$fonds1][$dag] = $koers;
        $koersen[$fonds2][$dag] = $this->koersenPerFonds[$fonds2][$dag];
        $meetpunten[$dag]=date('Y-m-d',$dag);
      }
    }

    $vorigeKoers=array();
    foreach($koersen[$fonds1] as $koersJul=>$koers1)
    {
      if(isset($vorigeKoers[$fonds1]))
      {
        $perf = ($koers1 - $vorigeKoers[$fonds1]) / ($vorigeKoers[$fonds1] / 100);
        $rendementen[$fonds1][$koersJul] = $perf;
      }
      $vorigeKoers[$fonds1]=$koers1;
      $koers2=$koersen[$fonds2][$koersJul];
      if(isset($vorigeKoers[$fonds2]))
      {
        $perf = ($koers2 - $vorigeKoers[$fonds2]) / ($vorigeKoers[$fonds2] / 100);
        $rendementen[$fonds2][$koersJul] = $perf;
      }
      $vorigeKoers[$fonds2]=$koers2;
    }

    return $rendementen;
  }
  
  function bepaalCorrelatieMatrix($datum='')
  {
    $matrix=array();
    if($datum=='')
      $datum=$this->rapportageDatum;
    $this->fondsStandaardDeviatie=array();
    $allecomponenten=array_keys($this->verdeling[$datum]);

    foreach($allecomponenten as $component1)
    {
      if(!isset($this->fondsStandaardDeviatie[$component1]))
      {
        $first=key($this->koersenPerFonds[$component1]);
        end($this->koersenPerFonds[$component1]);
        $last=key($this->koersenPerFonds[$component1]);
        $jaren=($last-$first)/86400/365.25;
        $koersen=array_values($this->koersenPerFonds[$component1]);
        $puntenPerJaar=count($koersen)/$jaren;

        $tmp=array();
        foreach($koersen as $koers)
        {
          if(isset($vorigeKoers))
          {
            $tmp[]=($koers - $vorigeKoers) / ($vorigeKoers / 100);
          }
          $vorigeKoers=$koers;
        }
        unset($vorigeKoers);
        $this->fondsStandaardDeviatie[$component1] = standard_deviation($tmp) * pow($puntenPerJaar, 0.5);
      }

      foreach($allecomponenten as $component2)
      {
        if($component1==$component2)
        {
          $correlation=1;
        }
        else
        {
          $rendementen = $this->berekenFondsRendementen($component1, $component2, $datum);
          $correlation = pearson_correlation($rendementen[$component1], $rendementen[$component2]);
        }

        if($correlation==-1)
        {
          $correlation = 0;
        }
        $matrix[$component1][$component2]=$correlation;
      }
    //  exit;
    }
    $this->correlatieMatrix=$matrix;
    if($datum<>'')
      $this->correlatieMatrixOpDatum[$datum]=$matrix;

  }
  
  function berekenVariantie($datum='',$categorie='')
  {

    if($datum=='')
    {
      $datum = $this->rapportageDatum;
      if(isset($this->correlatieMatrixOpDatum[$datum]))
      {
        $matrix = $this->correlatieMatrixOpDatum[$datum];
      }
      else
      {
        $matrix=$this->correlatieMatrix;
      }
      
    }
    else
      $matrix=$this->correlatieMatrix;

      
    $var=0;
    $debugTxt='';
    $debugArray=array();
    
    
    foreach ($matrix as $component1=>$componentData2)
    {
      foreach ($componentData2 as $component2=>$correlatie)
      {
        if($categorie=='')
          $percentage=$this->verdeling[$datum][$component2];
        else
          $percentage=$this->verdelingCategorie[$datum][$categorie][$component2];
        
        if($correlatie <> 0 && $percentage <> 0)
        {
          $relatieVar[$component1.'_'.$component2]=$percentage*$this->fondsStandaardDeviatie[$component1];
          $relatieVarDebug[$component1.'_'.$component2]=round($percentage,4)." * ".$this->fondsStandaardDeviatie[$component1]." ";
          if($component1 == $component2)
          {
            if($this->debug)
            {
              $debugTxt .= $component1 . '_' . $component2 . " =>  " . round($percentage, 4) . "^2 * " . $correlatie . "^2 * " . $this->fondsStandaardDeviatie[$component1] . "^2 =" . pow($percentage, 2) * pow($correlatie, 2) * pow($this->fondsStandaardDeviatie[$component1], 2) . "\n";
              $debugArray[]= $component1 . '_' . $component2 . " =>  " . round($percentage, 4) . "^2 * " . $correlatie . "^2 * " . $this->fondsStandaardDeviatie[$component1] . "^2 =" . pow($percentage, 2) * pow($correlatie, 2) * pow($this->fondsStandaardDeviatie[$component1], 2);
            }
            $var+=pow($percentage,2)*pow($correlatie,2)*pow($this->fondsStandaardDeviatie[$component1],2);
          }
          else
          {
            if(isset($relatieVar[$component2.'_'.$component1]))
            {
              if($this->debug)
              {
                $debugTxt .= $component1 . '_' . $component2 . " => 2 * " . $relatieVarDebug[$component2 . '_' . $component1] . " * " . round($percentage, 4) . " * " . $correlatie . " * " . $this->fondsStandaardDeviatie[$component1] . "\n";
                $debugArray[]=$component1 . '_' . $component2 . " => 2 * " . $relatieVarDebug[$component2 . '_' . $component1] . " * " . round($percentage, 4) . " * " . $correlatie . " * " . $this->fondsStandaardDeviatie[$component1];
              }
              $var+=2* $relatieVar[$component2.'_'.$component1]* $percentage*$correlatie*$this->fondsStandaardDeviatie[$component1];
            }
          }
        }
      }
    }
    if($categorie=='')
      $categorie='totaal';
      
    $this->var[$categorie][$datum]=$var;
    $this->std[$categorie][$datum]=pow($var,0.5);
    $this->debugTxt=$debugTxt;
    $this->debugArray=$debugArray;
  }
  
  
  
  function bereken($jaren=5)
  {
    $this->bepaalPortefeuilleVerdeling($this->rapportageDatum);
    $this->bepaalPeriode($jaren);
    $this->getKoersen();
    $this->bepaalCorrelatieMatrix();
    $this->berekenVariantie();
    
    
  }
  
  function maakXlsDebug()
  {
    global $__appvar;
    $datum=$this->rapportageDatum;
    $xlsData=array();
  
    $xlsData[]=array('Verdeling op '.$datum);
    $xlsData[]=array('Fonds','Percentage');
    foreach($this->verdeling[$datum] as $component=>$percentage)
    {
      $xlsData[]=array($component,$percentage*100);
    }
    $xlsData[]=array('');
  
    $xlsData[]=array('Fonds koersen/rendementen');
    $header=array('Datum');
    foreach($this->componenten as $component)
    {
      $header[]='Koersen '.$component;
      $header[]='rendement '.$component;
    }
    $xlsData[]=$header;
    foreach($this->meetpunten as $meetpuntJul=>$meetpuntDate)
    {
      $row=array($meetpuntDate);
      foreach($this->componenten as $component)
      {
        $row[]= $this->koersen[$component][$meetpuntJul];
        $row[]= $this->rendementen[$component][$meetpuntJul];
      }
      $xlsData[]=$row;
    }
    $xlsData[]='';
    $row=array('stdev');
    foreach($this->componenten as $component)
    {
      $row[]= $component;
      $row[]= $this->fondsStandaardDeviatie[$component];
    }
    $xlsData[]=$row;
  
    $xlsData[]=array('');
    $xlsData[]=array('Fondsrendement stdev');
    $xlsData[]=array('Fonds','stdev');
    foreach($this->componenten as $component)
    {
      $xlsData[]=array($component,$this->fondsStandaardDeviatie[$component]);
    }
  
    $xlsData[]=array('');
    $xlsData[]=array('Correlatie matrix');
    $header=array('Fonds');
    foreach($this->componenten as $component)
    {
      $header[]=$component;
    }
    $xlsData[]=$header;
    foreach ($this->correlatieMatrix as $component1=>$componentData2)
    {
      $row=array($component1);
      foreach ($componentData2 as $component2 => $correlatie)
      {
        $row[]=$correlatie;
      }
      $xlsData[]=$row;
    }

  
    $xlsData[]=array('');
    $xlsData[]=array('var', $this->var['totaal'][$datum]);
    $xlsData[]=array('stdev',$this->std['totaal'][$datum]);
  
    $xlsData[]=array('');
    $xlsData[]=array('var berekening');
    foreach($this->debugArray as $row)
      $xlsData[]=array($row);
    
  
  
    include_once($__appvar["basedir"].'/classes/excel/Writer.php');
    $workbook = new Spreadsheet_Excel_Writer();
  
    $worksheet =& $workbook->addWorksheet();
    for($regel = 0; $regel < count($xlsData); $regel++ )
    {
      for($col = 0; $col < count($xlsData[$regel]); $col++)
      {
        $worksheet->write($regel, $col, $xlsData[$regel][$col]);
      }
    }
    $workbook->send('rapport.xls');
    $workbook->close();
    
    
  }




}

?>