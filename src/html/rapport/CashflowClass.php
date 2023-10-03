<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/11/13 14:47:19 $
 		File Versie					: $Revision: 1.27 $

 		$Log: CashflowClass.php,v $
 		Revision 1.27  2019/11/13 14:47:19  rvv
 		*** empty log message ***
 		
 		Revision 1.26  2019/11/09 16:44:02  rvv
 		*** empty log message ***
 		
 		Revision 1.25  2019/11/06 15:53:41  rvv
 		*** empty log message ***
 		
 		Revision 1.24  2017/05/29 18:18:58  rvv
 		*** empty log message ***
 		
 		Revision 1.23  2017/05/27 09:45:17  rvv
 		*** empty log message ***
 		
 		Revision 1.22  2016/02/15 06:57:18  rvv
 		*** empty log message ***
 		
 		Revision 1.21  2016/02/13 14:01:52  rvv
 		*** empty log message ***
 		
 		Revision 1.20  2015/02/15 10:37:47  rvv
 		*** empty log message ***
 		
 		Revision 1.19  2015/01/24 19:53:08  rvv
 		*** empty log message ***
 		
 		Revision 1.18  2015/01/17 18:56:51  rvv
 		*** empty log message ***
 		
 		Revision 1.17  2014/03/12 15:13:18  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2013/02/13 17:05:48  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2013/02/10 10:05:24  rvv
 		*** empty log message ***
 		
 		Revision 1.14  2012/11/04 13:32:13  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2012/09/09 17:35:02  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2012/09/05 18:18:18  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2012/05/27 08:31:47  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2012/03/18 16:09:27  rvv
 		*** empty log message ***

 		Revision 1.9  2012/02/29 16:56:06  rvv
 		*** empty log message ***

 		Revision 1.8  2011/12/24 16:34:55  rvv
 		*** empty log message ***

 		Revision 1.7  2011/11/27 12:45:58  rvv
 		*** empty log message ***

 		Revision 1.6  2010/09/11 15:16:29  rvv
 		*** empty log message ***

 		Revision 1.5  2010/02/14 12:35:46  rvv
 		*** empty log message ***

 		Revision 1.4  2009/08/30 09:11:26  rvv
 		*** empty log message ***

 		Revision 1.3  2009/04/08 17:14:01  rvv
 		*** empty log message ***

 		Revision 1.2  2009/01/20 17:44:08  rvv
 		*** empty log message ***

 		Revision 1.1  2008/12/03 10:54:28  rvv
 		*** empty log message ***


*/
//ini_set('max_execution_time',10);
//error_reporting(E_ALL);
//ini_set('display_errors','On');

class Cashflow
{

  function Cashflow($portefeuille,$datumVanaf,$datum,$debug = false,$fonds='')
  {
    global $__appvar;
    $this->portefeuille= $portefeuille;
    $this->datumJul = $datum;
    $this->datumVanafJul = $datumVanaf;
    $this->nowJul = time();
    $this->debug = $debug;
    $this->fondsData = array();
    $this->db = new DB();
    $this->db2 = new DB();
    if($fonds<>'')
      $fondsFilter="AND Fonds='$fonds'";
    else
      $fondsFilter='';  
    $query =  "SELECT * FROM TijdelijkeRapportage WHERE rapportageDatum = '".jul2sql($this->datumJul)."' $fondsFilter AND portefeuille = '".$this->portefeuille."' ".$__appvar['TijdelijkeRapportageMaakUniek'];
    $this->db->SQL($query);
    $this->db->Query();
    $this->portefeuilleWaarde=0;
    while($data=$this->db->nextRecord())
    {
      if(adodb_db2jul($data['Lossingsdatum']) > 0)
        $this->portefeuilleWaarde+=$data['actuelePortefeuilleWaardeEuro'];
      $query =  "SELECT lossingskoers,Fonds,variabeleCoupon,Rentepercentage FROM Fondsen WHERE Fonds = '".$data['fonds']."'";
      $this->db2->SQL($query);
      $this->db2->Query();
      $lossingskoers = $this->db2->NextRecord();
      
      if($lossingskoers['lossingskoers'] <> 0)
        $data['lossingskoers'] =$lossingskoers['lossingskoers'];
      else
        $data['lossingskoers'] =100; 
         
      $data['variabeleCoupon']=$lossingskoers['variabeleCoupon'];  
      
      $data['lossingsWaarde']=$data['totaalAantal'] * $data['lossingskoers'] * $data['fondsEenheid'] * $data['actueleValuta']; 


    	$q = "SELECT rentedatum,renteperiode,Rente30_360,variabeleCoupon,lossingskoers FROM FondsParameterHistorie WHERE Fonds = '".$data['fonds']."' AND GebruikTot > '".jul2sql($this->datumJul)."' ORDER BY GebruikTot DESC LIMIT 1";
	    $this->db2->SQL($q);
	    $this->db2->Query();
      if($this->db2->records()>0)
      {
  	    $startWaarden = $this->db2->NextRecord();
        $data['rentedatum']=$startWaarden['rentedatum'];
        $data['renteperiode']=$startWaarden['renteperiode'];
        $data['Rente30_360']=$startWaarden['Rente30_360'];
        $data['variabeleCoupon']=$startWaarden['variabeleCoupon'];
        if($startWaarden['lossingskoers'] == 0)
          $data['lossingskoers']=100;
        else
          $data['lossingskoers']=$startWaarden['lossingskoers'];
      }   

   // echo $data['fonds']." ".$data['rentePerJaar']."=". $lossingskoers['Rentepercentage']."*".$data['totaalAantal']."*".$data['lossingskoers']."*".$data['fondsEenheid']."*".$data['actueleValuta']."<br>\n";  
      $this->fondsData[]=$data;
      if($data['type'] == 'fondsen')
        $this->fondsDataKeyed[$data['fonds']]=$data;
    }
  }

  function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



  function genereerTransacties($berekenenVanaf='',$berekenenTot='')
  {
    if($berekenenVanaf=='')
      $berekenenVanaf=$this->datumJul;

      
    foreach ($this->fondsData as $fonds)
	  {
  	  if($fonds['type'] == 'fondsen')
	    {
        $lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
        $rentedag=substr($fonds['rentedatum'],8,2);
        $rentemaand=substr($fonds['rentedatum'],5,2);
        //$renteVanafJul = adodb_db2jul($fonds['renteVanaf']);
        $rentedatumJul = adodb_db2jul($fonds['rentedatum']);
        if(substr($fonds['rentedatum'],5,5)=='01-01')
          $rentedatumJul+=86400;
        $renteVanafJul = adodb_db2jul(jul2sql($berekenenVanaf));
        
        if($lossingsJul > 0)
	      {
	        if($berekenenTot <> '' && $berekenenTot < $lossingsJul)
            $lossingsJul=$berekenenTot;
            
	        $this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	        $this->lossingsWaardeTotaal += $fonds['totaalAantal'] * $fonds['lossingskoers'] * $fonds['fondsEenheid'] * $fonds['actueleValuta'];

	        $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$fonds['fonds']."' ORDER BY Datum DESC LIMIT 1";
		      $this->db->SQL($q);
		      if($this->db->Query())
			     $koers = $this->db->NextRecord();

		  	  $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		  	  $p = $fonds['actueleFonds'];
	        $r = $koers['Rentepercentage']/100;
	        $b = $this->fondsDataKeyed[$fonds['fonds']]['lossingskoers'];// $fonds['lossingskoers']
	        $y = $jaar;
 //       
          $ytm=  $this->bondYTM($p,$r,$b,$y)*100;  
          $restLooptijd=($lossingsJul-$berekenenVanaf)/31556925.96;
//         
          $aandeel=$fonds['actuelePortefeuilleWaardeEuro']/$this->portefeuilleWaarde;
          $totalen['yield']+=$koers['Rentepercentage']*$aandeel;
          $totalen['ytm']+=$ytm*$aandeel;
          $totalen['restLooptijd']+=$restLooptijd*$aandeel;
  //   echo $fonds['fonds']." $restLooptijd * $aandeel; <br>\n";

          $this->ytmWaarden[$fonds['fonds']]=$ytm;
	        $this->ytm[$fonds['fonds'].' looptijd '.round($jaar,1)." jaar."] = $ytm;//$this->bondYTM($p,$r,$b,$y)*100;

	        $cashArray[$lossingsJul.''][]=array('type'=>'lossing','totaalAantal'=>$fonds['totaalAantal'],'fondsOmschrijving'=>$fonds['fondsOmschrijving'],'fonds'=>$fonds['fonds'],
	                                         'Rentepercentage'=>$koers['Rentepercentage'],'fondsEenheid'=>$fonds['fondsEenheid'],'actueleValuta'=>$fonds['actueleValuta'],
	                                         'jaar'=>$jaar,'lossingskoers'=>$fonds['lossingskoers'],'variabeleCoupon'=>$fonds['variabeleCoupon']);
          $counter =0;
          //listarray($fonds);
          if ($rentedatumJul > 0) //adodb_db2jul($fonds['renteVanaf'])
	        {
	          for($i=$rentedatumJul;$i<=($lossingsJul+2*86400);$i=$i+31556925.96/(12/$fonds['renteperiode']))
	          {
	            if($fonds['renteperiode']==12)
              {
                $renteOp=adodb_db2jul(adodb_date("Y-",$i).$rentemaand."-".$rentedag);
              }
              else
              {
  	            $renteOp=adodb_db2jul(adodb_date("Y-m-",$i).$rentedag);
              }
/*
                if($fonds['fonds'] =='ING FRN 2003-Perp')
                {
                  echo $fonds['fonds']."   " . date('Y-m-d', $rentedatumJul)." ". date('Y-m-d', $renteOp)."<br>\n";
                  ob_flush();
                }
*/
	            if($i>=$berekenenVanaf)
              {
                $cashArray[$renteOp . ''][] = array('renteOp'=>date('Y-m-d', $renteOp), 'type'              => 'rente', 'totaalAantal' => $fonds['totaalAantal'], 'actueleValuta' => $fonds['actueleValuta'],
                                                    'fondsOmschrijving' => $fonds['fondsOmschrijving'], 'Rentepercentage' => $koers['Rentepercentage'],
                                                    'rentePeriode'      => $fonds['renteperiode'], 'fondsEenheid' => $fonds['fondsEenheid'], 'actueleFonds' => $fonds['actueleFonds'],
                                                    'fonds'             => $fonds['fonds'], 'rentedatum' => $fonds['rentedatum'], 'eersteRentedatum' => $fonds['eersteRentedatum'], 'renteVanaf' => $fonds['renteVanaf'],
                                                    'jaar'              => ($i - $renteVanafJul) / 31556925.96, 'lossingskoers' => $fonds['lossingskoers']);


              }
	           if ($counter >5000)
	           {
	             echo "Meer dan 5000 Renteperioden? Berekening afgebroken. Huidige Fonds: ";
	             listarray($fonds);
	             break;
	           }
	           $counter++;
	          }
	        }
	      }
	    }
	  }

    $this->portefeuilleTotaal=$totalen;
	  ksort($cashArray,SORT_NUMERIC);
	  $this->cashArray = $cashArray;

	  return $this->cashArray;
  }


  function genereerRows()
  {
    global $__appvar;
    $tmp=array();
    foreach($this->cashArray as $dateId=>$regels)
    {
      foreach($regels as $i=>$fonds)
      {
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
    foreach ($tmp as $datum=>$regels)
    {
  	 if($datum > $this->datumJul)
	   {
    //   listarray($regels);
	    foreach ($regels as $id=>$fonds)
	    {
	      if($fonds['type']=='lossing')
	      {
	        $waarde = $fonds['totaalAantal']*$fonds['fondsEenheid']*$fonds['actueleValuta']* $fonds['lossingskoers'];//*100;
	        $gegevens['jaar'][adodb_date("Y",$datum)]['lossing']+=$waarde;
	        if($datum > $this->datumJul && $datum < $this->datumJul+31556925.96)
	          $gegevens['maand'][$__appvar["Maanden"][adodb_date("n",$datum)].' '.adodb_date("Y",$datum)]['lossing']+=$waarde;
	        if($datum > $this->datumJul && $datum)
	          $gegevens['maanden'][adodb_date("Y-m-d",$datum)]['lossing']+=$waarde;
	      }
  	    elseif($fonds['type']=='rente')
  	    {
          $waarde = $this->renteOverPeriode($fonds,adodb_date("Y-m-d",$datum));
	        $gegevens['jaar'][adodb_date("Y",$datum)]['rente']+=$waarde;
	        if($datum > $this->datumJul && $datum < $this->datumJul+31556925.96)
	          $gegevens['maand'][$__appvar["Maanden"][adodb_date("n",$datum)].' '.adodb_date("Y",$datum)]['rente']+=$waarde;

	        if($datum > $this->datumJul && $datum )
	        {
	          $gegevens['maanden'][adodb_date("Y-m-d",$datum)]['rente']+=$waarde;
	        }
        }

        $actueleWaarde = $waarde/(pow((1+$fonds['Rentepercentage']/100),$fonds['jaar']));
        if(round($waarde,2)==0.00)
          continue;

	      if($this->debug)
        {
	        $this->regels[]=array(adodb_date("d-m-Y",$datum),$fonds['fondsOmschrijving'],$fonds['type'],$this->formatGetal($waarde,2),
	        $this->formatGetal($fonds['jaar'],3),$this->formatGetal($actueleWaarde,2),$this->formatGetal($actueleWaarde*$fonds['jaar'],2));
        }
        else
        {
   	      $this->regels[]=array(adodb_date("d-m-Y",$datum),$fonds['fondsOmschrijving'],$fonds['type'],$this->formatGetal($waarde,2));
   	      $this->regelsRaw[]=array(adodb_date("d-m-Y",$datum),$fonds['fondsOmschrijving'],$fonds['type'],$waarde);
        }
        $this->waardePerFonds[$fonds['fonds']]['ActueelWaardeJaar']+= $actueleWaarde*$fonds['jaar'];
        $this->waardePerFonds[$fonds['fonds']]['ActueelWaarde']+= $actueleWaarde;
	      $this->totaalActueelJaar += $actueleWaarde*$fonds['jaar'];
	      $this->totaalActueel += $actueleWaarde;
	      $this->totaalWaarde += $waarde;
        //$this->formuleDelen[] = array('waarde'=>$waarde,'jaar'=>$fonds['jaar']);
	   }
	  }
   }
   $this->gegevens = $gegevens;
   
   
   foreach ($this->fondsData as $fonds)
	 {
     if($fonds['type'] == 'fondsen')
	   {
        $lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
        if($lossingsJul > 0)
	      { 
	        $renteDag=0;
			    if($fonds['variabeleCoupon'] == 1)
			    {
			      $rapportJul=$this->datumJul;
			      $renteJul=adodb_db2jul($fonds['rentedatum']);
            $renteStap=($fonds['renteperiode']/12)*31556925.96;
            $renteDag=$renteJul;
            if($renteStap > 1000)
            {
              while($renteDag<$rapportJul)
              {
                $renteDag+=$renteStap;
              }
            }
			    }
        
          $duration=$this->waardePerFonds[$fonds['fonds']]['ActueelWaardeJaar']/$this->waardePerFonds[$fonds['fonds']]['ActueelWaarde'];
   //echo $fonds['fonds']." ".round($this->ytmWaarden[$fonds['fonds']],2)." ".round($duration,2)."<br>\n";
   //echo "$renteDag duration ".round($duration,2)."=".round($this->waardePerFonds[$fonds['fonds']]['ActueelWaardeJaar'],2)."/".round($this->waardePerFonds[$fonds['fonds']]['ActueelWaarde'],2)."<br>\n";
          $aandeel=$fonds['actuelePortefeuilleWaardeEuro']/$this->portefeuilleWaarde;
          if($fonds['variabeleCoupon'] == 1 && $renteDag <> 0)
	           $modifiedDuration=($renteDag-$this->datumJul)/86400/365;
          else
	           $modifiedDuration=$duration/(1+$this->ytmWaarden[$fonds['fonds']]/100);
   //echo "aandeel=".round($aandeel,2)." modifiedDuration ".round($modifiedDuration,2)."<br>\n";   
          $this->portefeuilleTotaal['duration']+=$duration*$aandeel;
          $this->portefeuilleTotaal['modifiedDuration']+=$modifiedDuration*$aandeel;  
        }
      }
    }
   
   return $this->regels;
  }
  
  
  function ytm()
  {
//listarray($this->portefeuilleTotaal);
	 $this->YTMrows[]=array('','portefeuille','YTM ',
	                       $this->formatGetal($this->portefeuilleTotaal['ytm'],2).' %'
	                       );
	 $this->YTMrows[]=array('','portefeuille','modified Duration ',
	                       $this->formatGetal($this->portefeuilleTotaal['duration'],2).' jaar'
	                       );
    
    
  }

  function ytm_old()
  {


$doelwaarde = $this->lossingsWaardeTotaal;
$counter = 0;
$limit =5000;
$portefeuilleYTM= 0;
for ($i=0;$i<=1; $i=$i+0.00999)
{
  if($counter >$limit)
   break;
  foreach ($this->formuleDelen as $waarden)
  {
    $runTotaal += $waarden['waarde'] * POW((1+$i),-1*$waarden['jaar']);
  }
// echo "i $i -> $doelwaarde > $runTotaal <br>";ob_flush();

  if($doelwaarde == $runTotaal)
   break;

  if($doelwaarde > $runTotaal)
  {
    $vorigeI = $i-0.01;
    for ($j=$i; $j >= $vorigeI; $j=$j-0.000999)
    {
      if($counter >$limit)
        break;
      foreach ($this->formuleDelen as $waarden)
      {
        $runTotaal2 += $waarden['waarde'] * POW((1+$j),-1*$waarden['jaar']);
      }

 //echo "j $j >= $vorigeI? -> $doelwaarde < $runTotaal2 <br>";

      if($doelwaarde < $runTotaal2)
      {
        $vorigeJ = $j+0.001;

        for ($k=$j; $k<=$vorigeJ; $k=$k+0.0000999)
        {
          if($counter >$limit)
            break;
          foreach ($this->formuleDelen as $waarden)
          {
           $runTotaal3 += $waarden['waarde'] * POW((1+$k),-1*$waarden['jaar']);
          }
 //echo "k $k -> $doelwaarde > $runTotaal3 <br>";ob_flush();
          if($doelwaarde > $runTotaal3)
          {
            $portefeuilleYTM= $k*100;

              $berekeningString = "Lossingswaarde = ".$doelwaarde." is bij benadering ";
              foreach ($this->formuleDelen as $waarden)
              {
                $berekeningString .= round($waarden['waarde'],4)." * POW((1+".round($k,4).",-1*".round($waarden['jaar'],4).") + ";
              }
              $berekeningString .= " is YTM = $portefeuilleYTM %.";

            break;
          }

          $runTotaal3 =0;
          $counter ++;
        }
        break;
      }
      $runTotaal2 =0;
      $counter ++;
    }
    break;
  }
  $runTotaal =0;
  $counter ++;
}
//echo $counter;
if(empty($portefeuilleYTM)&& !empty($j))
  $portefeuilleYTM = $j *100;
elseif(empty($portefeuilleYTM)&& !empty($i))
  $portefeuilleYTM = $i *100;

  if($portefeuilleYTM < 99)
  {
	 $this->YTMrows[]=array('','portefeuille','YTM ',
	                       $this->formatGetal($portefeuilleYTM,2).' %'
	                       );
  }
 if($this->debug)
 {
  $this->YTMdebugCells[]= $berekeningString;

 }
$modifiedDuration=($this->totaalActueelJaar/$this->totaalActueel)/(1+$portefeuilleYTM/100);
	 $this->YTMrows[]=array('','portefeuille','modified Duration ',
	                       $this->formatGetal($modifiedDuration,2).' jaar'
	                       );


 if($this->debug)
    $this->YTMdebugCells[]= "$modifiedDuration = (totaalActueelJaar/totaalActueel)/(1+portefeuilleYTM/100) = (".$this->totaalActueelJaar."/".$this->totaalActueel.")/(1+$portefeuilleYTM/100)";

  }



function fYTM($z,$p,$c,$b,$y)
{
 // $tmp = ($c + $b)* pow($z,$y+1) - $b * pow($z,$y) - ($c+$p)*$z + $p;
 	return ($c + $b)* pow($z,$y+1) - $b * pow($z,$y) - ($c+$p)*$z + $p;
}


function dfYTM($z,$p,$c,$b,$y)
{
 // $tmp = ($y+1)*($c + $b) * pow($z,$y) - $y * $b * pow($z,$y - 1) - ($c+$p);
 	return ($y+1)*($c + $b) * pow($z,$y) - $y * $b * pow($z,$y - 1) - ($c+$p);
}

function returnRate($pv,$fv,$y)
{
	return pow($fv/$pv,1.0/$y) - 1.0;
}

function bondYTM($pv , $pmt, $fv ,  $nper, $type=0, $guess=.1) 
{
  //pv=actuele koers, pmt=Rentepercentage, fv=lossingskoers, nper=resterende jaren
  $pmt=$pmt*100;
  $pv=$pv*-1;
  $type2 = ($type) ? 1 : 0;
  $wanted_precision = 1e-8;
  $current_diff = 1e99;
  $x = null;
  $next_x = null;
  $y = null;
  $z = null;
  if ($guess == 0)
  {
    $x = .1;
  }
  else
    $x = $guess;
  $max_iterations = 100;
  $iterations_done = 0;
  while ($current_diff > $wanted_precision && $iterations_done < $max_iterations)
  {
    if ($x == 0)
    {
      $next_x = $x - ($pv + $pmt * $nper + $fv) / ($pv * $nper + $pmt * ($nper * ($nper - 1) + 2 * $type2 * $nper) / 2);
    }
    else
    {
      $y = pow(1 + $x, $nper - 1);
      $z = $y * (1 + $x);
      $next_x = $x * (1 - ($x * $pv * $z + $pmt * (1 + $x * $type2) * ($z - 1) + $x * $fv) / ($x * $x * $nper * $pv * $y - $pmt * ($z - 1) + $x * $pmt * (1 + $x * $type2) * $nper * $y));
    }
    $iterations_done++;
    $current_diff = abs($next_x - $x);
    $x = $next_x;
  }
  if ($guess == 0 && abs($x) < $wanted_precision)
  {
    $x = 0;
  }
  //  echo "$x=$pv , $pmt, $fv ,  $nper <br> \n";
  if ($current_diff >= $wanted_precision)
  {
    return false;
  }
  else
    return $x;
}

function bondYTM_old($p,$r,$b,$y)
{//echo "$p <br>\n $r<br>\n $b<br>\n$y<br>\n" ;ob_flush();
	$z = $r;
	$c = $r*$b;
	$E = .00001;
//echo "<br>\n";
	if ($r == 0)
	{
		return $this->returnRate($p,$b,$y);
	}
	for ($i = 0; $i < 100; $i++)
	{
		if (abs($this->fYTM($z,$p,$c,$b,$y)) < $E) break;
    
    for($n=0; $n< 100; $n++)
    {
      if(abs($this->dfYTM($z,$p,$c,$b,$y)) >= $E)
        break;
      $z+= .1;
    }
//		while (abs($this->dfYTM($z,$p,$c,$b,$y)) < $E) $z+= .1;
//echo "$n $z <br>\n"; 

		$z = $z - ($this->fYTM($z,$p,$c,$b,$y)/$this->dfYTM($z,$p,$c,$b,$y));
	}
	if (abs($this->fYTM($z,$p,$c,$b,$y)) >= $E) return -1;  // error
	return (1/$z) - 1;
}




function renteOverPeriode($fondsdata,  $renteDatum)
{
	$fonds 						= $fondsdata[fonds];
	$aantal 					= $fondsdata[totaalAantal];
	//$rentedatum 			= $fondsdata[rentedatum];
	$eersteRentedatum = $fondsdata[eersteRentedatum];
	$rentePeriode 		= $fondsdata[rentePeriode];
	$valutaKoers      = $fondsdata['actueleValuta'];
	$rentebedrag = 0;

	$fondsdata[renteDatum]=$renteDatum;
	//listarray($fondsdata);

	$renteStartJul = adodb_db2jul($renteDatum) - (31556925.96*($rentePeriode/12));
  $renteStopJul  = adodb_db2jul($renteDatum);

	if(adodb_db2jul($eersteRentedatum) > $renteStartJul+2*86400)
	{
    $renteStartJul = adodb_db2jul($fondsdata['renteVanaf']);
	}



	//echo date('d-m-Y',$renteStartJul)."->  $renteDatum <nr>";

	$DB = new DB();
	// selecteer beginrente.
	$sel = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Datum <= '".adodb_jul2db($renteStartJul)."' AND Fonds = '".$fonds."' ORDER BY Datum DESC LIMIT 1";
	$DB->SQL($sel);
	$DB->Query();
	$beginrente = $DB->NextRecord();
	$rente = $beginrente['Rentepercentage'];

	$sel = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Datum >= '".adodb_jul2db($renteStartJul)."' AND Datum < '".(adodb_jul2db($renteStopJul))."' AND Fonds = '".$fonds."' ORDER BY Datum ASC";
	$DB->SQL($sel);
	$DB->Query();
	if($DB->records() > 0)
	{
	  $julvan = $renteStartJul;
		while($rentedata = $DB->nextRecord())
		{
      $jultot2 = adodb_db2jul($rentedata[Datum]);
	    $rentebedrag 	+= ($aantal * ($rente/100)) * (($jultot2 - $julvan)/31556925.96);
			$julvan = adodb_db2jul($rentedata[Datum]);
			$rente = $rentedata[Rentepercentage];
		}
   $rentebedrag 	+= ($aantal * ($rente/100)) * (($renteStopJul - $julvan)/31556925.96);
	}
	else
    $rentebedrag 	= ($aantal * ($rente/100)) * (($renteStopJul - $renteStartJul)/31556925.96);

	return $rentebedrag *$valutaKoers;
}

function ytmFonds($fonds)
{

   $fonds=$this->fondsDataKeyed[$fonds];
   $ytm=0;
	 $lossingsJul = adodb_db2jul($fonds['Lossingsdatum']);
   $rentedatumJul = adodb_db2jul($fonds['rentedatum']);
   $renteVanafJul = adodb_db2jul(jul2sql($this->datumJul));

   //listarray($fonds);
   if($lossingsJul > 0)
	 {
	   $this->huidigeWaardeTotaal += $fonds['actuelePortefeuilleWaardeEuro'];
	   $this->lossingsWaardeTotaal += $fonds['totaalAantal'] * $fonds['lossingskoers'] * $fonds['fondsEenheid'] * $fonds['actueleValuta']; //*100

	   $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$fonds['fonds']."' ORDER BY Datum DESC LIMIT 1";
		 $this->db->SQL($q);
		 if($this->db->Query())
		 $koers = $this->db->NextRecord();

		 $jaar = ($lossingsJul-$renteVanafJul)/31556925.96;

		 $p = $fonds['actueleFonds'];
	   $r = $koers['Rentepercentage']/100;
	   $b = 100;
	   $y = $jaar;

	   $ytm = $this->bondYTM($p,$r,$b,$y)*100;
	  // echo $fonds['fonds']." $ytm = $this->bondYTM($p,$r,$b,$y)*100; <br>\n";
   }
   return $ytm;
}


}

?>