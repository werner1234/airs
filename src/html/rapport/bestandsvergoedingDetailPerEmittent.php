<?
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2011/09/28 18:46:00 $
File Versie					: $Revision: 1.5 $

$Log: bestandsvergoedingDetailPerEmittent.php,v $
Revision 1.5  2011/09/28 18:46:00  rvv
*** empty log message ***

Revision 1.4  2011/09/14 09:26:56  rvv
*** empty log message ***

Revision 1.3  2011/05/18 16:51:08  rvv
*** empty log message ***

Revision 1.2  2011/05/15 14:29:22  rvv
*** empty log message ***

Revision 1.1  2011/04/17 08:57:25  rvv
*** empty log message ***

Revision 1.2  2011/03/26 16:51:50  rvv
*** empty log message ***

Revision 1.1  2011/03/23 16:59:47  rvv
*** empty log message ***


*/
class bestandsvergoedingDetailPerEmittent
{
	var $selectData;
	var $excelData;

	function bestandsvergoedingDetailPerEmittent( $selectData )
	{
    $this->selectData = $selectData;
  	$this->pdf = new PDFOverzicht('L','mm');
	  $this->pdf->excelData = array();
	  $this->db=new DB();
	}

	function writeRapport()
	{
		global $__appvar,$USR;
		if($this->type=='db' || $_POST['alleClienten']==0)
		  $uitkeerFilter="AND Portefeuilles.BestandsvergoedingUitkeren = 1";


		$selectie = new portefeuilleSelectie($this->selectData,$this->orderby);
    $records = $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();
    $portefeuilleList=array_keys($portefeuilles);
		$extraquery=" AND Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') ";
		$rapportageDatum=date('Y-m-d',$this->selectData['datumTm']);
		$rapportageDatumBegin=date('Y-m-d',$this->selectData['datumVan']);

		$this->type=$this->selectData['soort'];


		if($this->selectData['fonds'] <> '')
		  $extraquery .=" AND Rekeningmutaties.Fonds='".$this->selectData['fonds']."' ";
		if($this->selectData['emittent'] <> '')
		  $extraquery=" AND emittentPerFonds.emittent='".$this->selectData['emittent']."' ";

		$query="SELECT Rekeningmutaties.Fonds,Portefeuilles.Client, Portefeuilles.Portefeuille,emittentPerFonds.rekenmethode,emittentPerFonds.percentage,emittentPerFonds.emittent,Portefeuilles.depotbank,Portefeuilles.Vermogensbeheerder,
		SUM(Rekeningmutaties.Aantal) AS totaalAantal,Fondsen.Valuta,Fondsen.Fondseenheid
		FROM emittentPerFonds
INNER JOIN Rekeningmutaties ON Rekeningmutaties.Fonds = emittentPerFonds.fonds
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
INNER JOIN Fondsen ON emittentPerFonds.fonds = Fondsen.Fonds
WHERE YEAR(Rekeningmutaties.Boekdatum) = '".date('Y',$this->selectData['datumTm'])."' AND
emittentPerFonds.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder AND Portefeuilles.Depotbank=emittentPerFonds.depotbank AND
Portefeuilles.Einddatum  > '$rapportageDatum'  $uitkeerFilter $extraquery
GROUP BY emittentPerFonds.emittent,Portefeuilles.Portefeuille,Rekeningmutaties.Fonds
HAVING totaalAantal <> 0
ORDER BY emittentPerFonds.emittent,Portefeuilles.Portefeuille,Rekeningmutaties.Fonds
";
		//echo $query;
		$db=new DB();
		$db2=new DB();
		$db->SQL($query);
		$db->Query();
		while($data=$db->nextRecord())
		{
		  $datumArray=$this->getDagenVoorMethode($data['rekenmethode']);
		  $this->fondsAantalOpDatum($datumArray,$data['Portefeuille'],$data['Fonds']);
		  $fondsen[$data['emittent']][$data['Fonds']]=array('fonds'=>$data['Fonds'],'valuta'=>$data['Valuta'],'fondseenheid'=>$data['Fondseenheid']);
		  $portefeuillesPerEmittent[$data['emittent']][$data['Portefeuille']][$data['Fonds']]=array('aantal'=>$data['totaalAantal']);
		  $portefeuilleSettings[$data['Portefeuille']]=array('Vermogensbeheerder'=>$data['Vermogensbeheerder'],'depotbank'=>$data['depotbank'],'client'=>$data['Client']);
	    $emittentInstelling[$data['emittent']][$data['Portefeuille']][$data['Fonds']]=array('rekenmethode'=>$data['rekenmethode'],'percentage'=>$data['percentage']);
	    $valutas[$data['Valuta']]=$data['Valuta'];
	    $this->getValutaKoersen($datumArray,$data['Valuta']);
	    $this->getFondsKoersen($datumArray,$data['Fonds']);
		}

		foreach ($portefeuillesPerEmittent as $emittent=>$portefeuilleData)
		{
			$header=array('Portefeuille','client','depotbank');
			$totaalWaarde=array();
			$totaalVergoeding=array();
      foreach ($fondsen[$emittent] as $fonds=>$fondsData)
      {
        array_push($header,$fonds." waarde");
        array_push($header,$fonds." vergoeding");
      }

		  $this->pdf->excelData[] = array($emittent);
		  if($this->type=='detail')
		    $this->pdf->excelData[] = $header;
		  elseif($this->type=='fonds')
		    $this->pdf->excelData[] = array('Fonds','Waarde','Vergoeding');

		  foreach ($portefeuilleData as $portefeuille=>$pData)
		  {

		    $tmp=array();
		    array_push($tmp,$portefeuille);
		    array_push($tmp,$portefeuilleSettings[$portefeuille]['client']);
		    array_push($tmp,$portefeuilleSettings[$portefeuille]['depotbank']);

		    foreach ($fondsen[$emittent] as $fonds=>$fondsData)
		    {
		      $actuelePortefeuilleWaardeEuro=0;
		      $beginPortefeuilleWaardeEuro=0;
		      $vergoeding=0;
		      $actuelePortefeuilleWaardeEuro 	=  $this->valutaKoersen[$rapportageDatum][$fondsData['valuta']] * ($fondsData['fondseenheid']  * $this->fondsAantal[$portefeuille][$rapportageDatum][$fonds]) * $this->fondsKoersen[$rapportageDatum][$fonds];
		      $beginPortefeuilleWaardeEuro 	=  $this->valutaKoersen[$rapportageDatumBegin][$fondsData['valuta']] * ($fondsData['fondseenheid']  * $this->fondsAantal[$portefeuille][$rapportageDatumBegin][$fonds]) * $this->fondsKoersen[$rapportageDatumBegin][$fonds];
		      if($emittentInstelling[$emittent][$portefeuille][$fonds]['rekenmethode']==1)
			      $vergoeding=$beginPortefeuilleWaardeEuro*$emittentInstelling[$emittent][$portefeuille][$fonds]['percentage']*0.01*0.25;
			    elseif($emittentInstelling[$emittent][$portefeuille][$fonds]['rekenmethode']==2)
			      $vergoeding=$actuelePortefeuilleWaardeEuro*$emittentInstelling[$emittent][$portefeuille][$fonds]['percentage']*0.01*0.25;
			    elseif($emittentInstelling[$emittent][$portefeuille][$fonds]['rekenmethode']==3)
			      $vergoeding=($beginPortefeuilleWaardeEuro+$actuelePortefeuilleWaardeEuro)*0.5*$emittentInstelling[$emittent][$portefeuille][$fonds]['percentage']*0.01*0.25;
			    elseif($emittentInstelling[$emittent][$portefeuille][$fonds]['rekenmethode']==4)
			    {
			      $n=0;
			      $waardeSom=0;
			      foreach ($this->getMaanden($this->selectData['datumVan'],$this->selectData['datumTm']) as $index=>$periode)
			      {
			        $waardeSom+=$this->valutaKoersen[$periode['stop']][$fondsData['valuta']] * ($fondsData['fondseenheid']  * $this->fondsAantal[$portefeuille][$periode['stop']][$fonds]) * $this->fondsKoersen[$periode['stop']][$fonds];
			        $n++;
			      }
			      $vergoeding=($waardeSom/$n)*$emittentInstelling[$emittent][$portefeuille][$fonds]['percentage']*0.01*0.25;
			    }
			    elseif($emittentInstelling[$emittent][$portefeuille][$fonds]['rekenmethode']==5)
			    {
			      $n=0;
			      $waardeSom=0;
			      foreach ($this->getDagen($this->selectData['datumVan'],$this->selectData['datumTm']) as $index=>$periode)
			      {
			        $waardeSom+=$this->valutaKoersen[$periode['stop']][$fondsData['valuta']] * ($fondsData['fondseenheid']  * $this->fondsAantal[$portefeuille][$periode['stop']][$fonds]) * $this->fondsKoersen[$periode['stop']][$fonds];
			        $n++;
			      }
			      $vergoeding=($waardeSom/$n)*$emittentInstelling[$emittent][$portefeuille][$fonds]['percentage']*0.01*0.25;
			    }

			    array_push($tmp,round($actuelePortefeuilleWaardeEuro,2));
          array_push($tmp,round($vergoeding,2));

          $totaalWaardePerFonds[$fonds]+=$actuelePortefeuilleWaardeEuro;
          $totaalVergoedingPerFonds[$fonds]+=$vergoeding;
          $totaalWaardePerPortefeuille[$portefeuille]+=$actuelePortefeuilleWaardeEuro;
          $totaalVergoedingPerPortefeuille[$portefeuille]+=$vergoeding;
          $totaalVergoedingPerPortefeuilleEmittentDepot[$portefeuille][$emittent][$portefeuilleSettings[$portefeuille]['depotbank']]+=$vergoeding;
 		    }
 		    $bestandsvergoedingen[$emittent][$portefeuilleSettings[$portefeuille]['depotbank']][$portefeuilleSettings[$portefeuille]['Vermogensbeheerder']]['vergoeding']+=$totaalVergoedingPerPortefeuilleEmittentDepot[$portefeuille][$emittent][$portefeuilleSettings[$portefeuille]['depotbank']];
        $bestandsvergoedingen[$emittent][$portefeuilleSettings[$portefeuille]['depotbank']][$portefeuilleSettings[$portefeuille]['Vermogensbeheerder']]['portefeuilles'][]=$portefeuille;

		    if($this->type=='detail')
		      $this->pdf->excelData[] = $tmp;
		  }

		  $totaalRow=array('Totaal','');
      foreach ($fondsen[$emittent] as $fonds=>$fondsData)
      {
        array_push($totaalRow,round($totaalWaardePerFonds[$fonds],2));
        array_push($totaalRow,round($totaalVergoedingPerFonds[$fonds],2));
      }
      if($this->type=='detail')
        $this->pdf->excelData[] = $totaalRow;

      if($this->type=='fonds')
      {
        foreach ($totaalWaardePerFonds as $fonds=>$actuelePortefeuilleWaardeEuro)
          $this->pdf->excelData[] = array($fonds,round($actuelePortefeuilleWaardeEuro,2),round($totaalVergoedingPerFonds[$fonds],2));
      }
      $this->pdf->excelData[] = array('');
		}

	  //listarray($bestandsvergoedingen);
    if($this->type=='db')
    {
      if($_SESSION['usersession']['gebruiker']['bestandsvergoedingEdit'] <> 1)
      {
        echo "Geen rechten op records aan te maken.";
        exit;
      }
      $regelCount=0;

      foreach ($bestandsvergoedingen as $emittent=>$depotbankData)
      {
        foreach ($depotbankData as $depotbank=>$vermogenshedeerderData)
        {
          $totaal=0;
          $totaalPortefeuilles=array();
          foreach ($vermogenshedeerderData as $vermogensbeheerder=>$bestandsvergoedingData)
          {
            $bestandsvergoeding=$bestandsvergoedingData['vergoeding'];
            $query="SELECT bestandsvergoedingNiveau FROM Vermogensbeheerders WHERE Vermogensbeheerder='$vermogensbeheerder'";
            $db->SQL($query);
            $bestandsvergoedingNiveau=$db->lookupRecord();
            if($bestandsvergoedingNiveau['bestandsvergoedingNiveau'] == 1)
            {
              $samenvoegen=true;
              $totaal+=$bestandsvergoeding;
              foreach ($bestandsvergoedingData['portefeuilles'] as $portefeuille)
                $totaalPortefeuilles[]=$portefeuille;
            }
            else
            {
              $query="SELECT id FROM Bestandsvergoedingen WHERE vermogensbeheerder='$vermogensbeheerder' AND periodeVan='$rapportageDatumBegin' AND periodeTm='$rapportageDatum' AND emittent='$emittent' AND depotbank='$depotbank'";
              if($db->QRecords($query) == 0)
              {
                $query="INSERT INTO `Bestandsvergoedingen` SET `vermogensbeheerder`='$vermogensbeheerder',`periodeVan`='$rapportageDatumBegin',`periodeTm`='$rapportageDatum',`emittent`='$emittent',
                       `depotbank`='$depotbank',`datumBerekend`=now(),`waardeBerekend`='".round($bestandsvergoeding,2)."',`status`='".date('Ymd_Hi')."/$USR aangemaakt',`add_date`=now(),`add_user`='$USR',`change_date`=now(),`change_user`='$USR'";
		            $db->SQL($query);
		            $db->Query();
		            $BestandsvergoedingId=$db->last_id();
		            foreach ($bestandsvergoedingData['portefeuilles'] as $portefeuille)
		            {
		              $query="INSERT INTO BestandsvergoedingPerPortefeuille SET bestandsvergoedingId='$BestandsvergoedingId',portefeuille='$portefeuille',
		                      bedragBerekend='".round($totaalVergoedingPerPortefeuillePerEmittent[$portefeuille][$emittent],2)."',add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
		              $db->SQL($query);
		              $db->Query();
		              $regelCount++;
		            }
              }
              else
                echo "Voor '$vermogensbeheerder' '$rapportageDatumBegin'->'$rapportageDatum', '$emittent', '$depotbank' bestaat al een record in Bestandsvergoedingen.<br>\n";
            }
          }
          if($samenvoegen==true)
          {
            $query="SELECT id FROM Bestandsvergoedingen WHERE vermogensbeheerder='$vermogensbeheerder' AND periodeVan='$rapportageDatumBegin' AND periodeTm='$rapportageDatum' AND emittent='$emittent' AND depotbank='$depotbank'";
            if($db->QRecords($query) == 0)
            {
              $query="INSERT INTO `Bestandsvergoedingen` SET `vermogensbeheerder`='$vermogensbeheerder',`periodeVan`='$rapportageDatumBegin',`periodeTm`='$rapportageDatum',`emittent`='$emittent',
                     `depotbank`='$depotbank',`datumBerekend`=now(),`waardeBerekend`='".round($totaal,2)."',`status`='".date('Ymd_Hi')."/$USR aangemaakt',`add_date`=now(),`add_user`='$USR',`change_date`=now(),`change_user`='$USR'";
		          $db->SQL($query);
		          $db->Query();
		          $BestandsvergoedingId=$db->last_id();
		          foreach ($totaalPortefeuilles as $portefeuille)
		          {
		            $query="INSERT INTO BestandsvergoedingPerPortefeuille SET bestandsvergoedingId='$BestandsvergoedingId',portefeuille='$portefeuille',
		                    bedragBerekend='".round($totaalVergoedingPerPortefeuilleEmittentDepot[$portefeuille][$emittent][$depotbank],2)."',add_date=now(),add_user='$USR',change_date=now(),change_user='$USR'";
		            $db->SQL($query);
		            $db->Query();
		            $regelCount++;
		          }
            }
            else
              echo "Voor '$vermogensbeheerder' '$rapportageDatumBegin'->'$rapportageDatum', '$emittent', '$depotbank' bestaat al een record in Bestandsvergoedingen.<br>\n";

          }
        }
      }
      $this->pdf->excelData[] = array('('.$regelCount.') records aangemaakt.');
    }
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
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
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

  function getDagenVoorMethode($methode)
  {
    $datumArray=array();
    if($methode==1)
		  $datumArray[]=date('Y-m-d',$this->selectData['datumVan']);
		elseif($methode==2)
		  $datumArray[]=date('Y-m-d',$this->selectData['datumTm']);
		elseif($methode==3)
		{
		  $datumArray[]=date('Y-m-d',$this->selectData['datumVan']);
		  $datumArray[]=date('Y-m-d',$this->selectData['datumTm']);
		}
		elseif($methode==4)
		{
		  $this->maanden=$this->getMaanden($this->selectData['datumVan'],$this->selectData['datumTm']);
		  foreach ($this->maanden as $periode)
		  {
		    $datumArray[]=$periode['start'];
		    $datumArray[]=$periode['stop'];
		  }
		}
		elseif($methode==5)
		{
		  $this->dagen=$this->getDagen($this->selectData['datumVan'],$this->selectData['datumTm']);
		  foreach ($this->dagen as $periode)
		  {
		    $datumArray[]=$periode['start'];
		    $datumArray[]=$periode['stop'];
		  }
		}
		$datumArray=array_unique($datumArray);
		return $datumArray;
  }

  function getValutaKoersen($datumArray,$valuta)
  {
  	foreach ($datumArray as $datum)
		{
      if(empty($this->valutaKoersen[$datum][$valuta]))
		  {
  			$q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$valuta."' AND Datum <= '$datum' ORDER BY Datum DESC LIMIT 1";
	      $this->db->SQL($q);
		    $this->db->Query();
			  $actuelevaluta = $this->db->NextRecord();
			  $this->valutaKoersen[$datum][$valuta]=$actuelevaluta['Koers'];
		  }
		}
  }
  function getFondsKoersen($datumArray,$fonds)
  {
  	foreach ($datumArray as $datum)
		{
      if(empty($this->fondsKoersen[$datum][$fonds]))
      {
	  	  $q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$fonds."' AND Datum <= '$datum' ORDER BY Datum DESC LIMIT 1";
 		    $this->db->SQL($q);
    	  $this->db->Query();
	      $actuelefonds = $this->db->NextRecord();
	      $this->fondsKoersen[$datum][$fonds]=$actuelefonds['Koers'];
      }
    }
  }

  function fondsAantalOpDatum($datumArray,$portefeuille,$fonds)
  {
  	$query = "SELECT Rekeningmutaties.Aantal, date(Rekeningmutaties.Boekdatum) as Boekdatum
   	FROM Rekeningmutaties
    INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
    INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
    WHERE
    YEAR(Rekeningmutaties.Boekdatum) = '".date('Y',$this->selectData['datumTm'])."' AND
    Rekeningmutaties.Fonds = '$fonds' AND
	  Rekeningmutaties.GrootboekRekening = 'FONDS' AND
	  Rekeningmutaties.Verwerkt = '1' AND Portefeuilles.Portefeuille='$portefeuille' AND
	  Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$this->selectData['datumTm'])."'
	  order by Rekeningmutaties.Boekdatum";
    $this->db->SQL($query);
    $this->db->Query();
    while($aantal = $this->db->NextRecord())
      $transacties[$aantal['Boekdatum']]=$aantal['Aantal'];

    foreach ($datumArray as $datum)
		{
		  $aantal=0;
		  foreach ($transacties as $boekdatum=>$boekAantal)
		  {
		    if(db2jul($datum) >= db2jul($boekdatum))
		      $aantal+=$boekAantal;
		  }
		  $this->fondsAantal[$portefeuille][$datum][$fonds]=$aantal;
		}
  }
}
?>