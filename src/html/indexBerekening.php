<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/05/23 16:36:21 $
 		File Versie					: $Revision: 1.71 $

 		$Log: indexBerekening.php,v $
 		Revision 1.71  2020/05/23 16:36:21  rvv
 		*** empty log message ***
 		
 		Revision 1.70  2020/01/15 16:26:49  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2019/12/21 13:44:37  rvv
 		*** empty log message ***
 		
 		Revision 1.68  2019/10/18 17:37:32  rvv
 		*** empty log message ***
 		
 		Revision 1.67  2019/09/07 16:06:15  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2019/08/21 10:51:03  rvv
 		*** empty log message ***
 		
 		Revision 1.65  2019/07/17 15:31:51  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2019/06/26 15:08:52  rvv
 		*** empty log message ***
 		
 		Revision 1.63  2019/06/22 16:30:27  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2019/05/29 15:42:46  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2018/11/22 07:26:04  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2018/10/16 12:43:25  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2018/10/13 17:16:37  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2018/09/23 17:14:23  cvs
 		call 7175
 		
 		Revision 1.57  2018/09/06 15:30:01  rvv
 		*** empty log message ***
 		
 		Revision 1.56  2018/09/02 11:58:56  rvv
 		*** empty log message ***
 		
 		Revision 1.55  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.54  2018/05/02 16:06:26  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2018/01/27 17:28:13  rvv
 		*** empty log message ***
 		
 		Revision 1.52  2017/06/21 16:06:41  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2017/02/22 17:14:01  rvv
 		*** empty log message ***
 		
 		Revision 1.50  2016/03/12 17:44:42  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2016/03/09 17:03:38  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2016/01/06 16:31:26  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2015/12/13 09:01:21  rvv
 		*** empty log message ***
 		
 		Revision 1.46  2015/11/07 16:42:56  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2015/01/31 19:58:37  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2013/09/28 14:42:13  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2013/07/17 15:50:29  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2013/06/26 15:54:39  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2013/05/22 15:54:41  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2013/01/13 13:33:45  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2012/12/12 16:52:03  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2012/05/09 18:46:22  rvv
 		*** empty log message ***

 		Revision 1.37  2012/02/19 16:11:27  rvv
 		*** empty log message ***

 		Revision 1.36  2012/02/01 19:30:47  rvv
 		*** empty log message ***

 		Revision 1.35  2011/12/07 19:14:04  rvv
 		*** empty log message ***

 		Revision 1.34  2011/09/03 14:28:39  rvv
 		*** empty log message ***

 		Revision 1.33  2011/07/27 16:26:05  rvv
 		*** empty log message ***

 		Revision 1.32  2011/06/13 14:49:14  rvv
 		*** empty log message ***

 		Revision 1.31  2011/04/17 09:12:10  rvv
 		*** empty log message ***

 		Revision 1.30  2011/04/09 14:30:03  rvv
 		*** empty log message ***

 		Revision 1.29  2011/03/30 20:35:16  rvv
 		*** empty log message ***

 		Revision 1.28  2011/03/17 09:09:08  rvv
 		*** empty log message ***

 		Revision 1.27  2011/03/14 12:17:37  rvv
 		*** empty log message ***

 		Revision 1.26  2011/03/13 18:40:35  rvv
 		*** empty log message ***

 		Revision 1.25  2011/01/29 15:55:31  rvv
 		*** empty log message ***

 		Revision 1.24  2011/01/26 17:17:25  rvv
 		*** empty log message ***

*/


include_once("rapport/rapportRekenClass.php");
include_once("rapport/rapportATTberekening.php");
include_once($__appvar["basedir"]."/classes/scenarioBerekening.php");  

class indexHerberekening
{
	function indexHerberekening( $selectData=array() )
	{
		$this->selectData = $selectData;
    $this->voorStartdatumNegeren=false;
		$this->forceDbLoad=false;
	}

	function formatGetal($waarde, $dec=2)
	{
		return number_format($waarde,$dec,",",".");
	}

	function BerekenMutaties($beginDatum,$eindDatum,$portefeuille)
	{
		$totaalWaarde =array();
		$db = new DB();



		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,(substr($beginDatum, 5, 5) == '01-01')?true:false,'EUR',$beginDatum);

	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }

	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,(substr($eindDatum, 5, 5) == '01-01')?true:false,'EUR',$beginDatum);

	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }
	  $DB=new DB();

  	$query = "SELECT SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
 	  "  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$beginDatum."')) ".
	  "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	  "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	  "FROM  (Rekeningen, Portefeuilles ) Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
  	"WHERE ".
  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
  	"Rekeningmutaties.Verwerkt = '1' AND ".
  	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
  	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND ".
	  "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
  	$DB->SQL($query);
  	$DB->Query();
  	$weging = $DB->NextRecord();
    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
		//echo "$beginDatum->$eindDatum $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";

    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
	  $stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum);
  	$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum);
  	$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

	  $query = "SELECT SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) - SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($resultaatVerslagperiode+$kosten['totaalkosten'],2);
    $data['performance'] =$performance;
    return $data;

	}


	function BerekenMutaties2($beginDatum,$eindDatum,$portefeuille,$valuta='EUR')
	{
	  if(substr($beginDatum,5,5)=='12-31')
	   $beginDatum=(substr($beginDatum,0,4)+1).'-01-01';

	  if ($valuta != "EUR" )
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$totaalWaarde =array();
		$db = new DB();

		$query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
		$db->SQL($query);
		$startDatum=$db->lookupRecord();

		$query="SELECT
Beleggingscategorien.Beleggingscategorie,
Beleggingscategorien.Omschrijving,
Beleggingscategorien.Afdrukvolgorde,
BeleggingscategoriePerFonds.Vermogensbeheerder,
Portefeuilles.Portefeuille
FROM
Beleggingscategorien
Inner Join BeleggingscategoriePerFonds ON Beleggingscategorien.Beleggingscategorie = BeleggingscategoriePerFonds.Beleggingscategorie
Inner Join Portefeuilles ON BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE Portefeuilles.Portefeuille='$portefeuille'
GROUP BY Beleggingscategorien.Beleggingscategorie
ORDER BY Afdrukvolgorde desc";
  		$db->SQL($query);
			$db->Query();
     $this->categorieVolgorde['LIQ']=0;
			while($data=$db->nextRecord())
				  $this->categorieVolgorde[$data['Beleggingscategorie']]=0;

    if(db2jul($beginDatum) <= db2jul($startDatum['Startdatum']))
    {
       if($this->voorStartdatumNegeren==true && db2jul($eindDatum) <= db2jul($startDatum['Startdatum']))
       return array('periode'=>$beginDatum."->".$eindDatum,'periodeForm'=>date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum)));

      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    }
    else
      $wegingsDatum=$beginDatum;

		$startjaar=substr($beginDatum,0,4);


		$koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,$valuta,true);
		//echo "att $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$beginDatum,$eindDatum,'EUR',true);<br>\n";

		$fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$beginDatum,(substr($beginDatum, 5, 5) == '01-01')?true:false,$valuta,$beginDatum);

		if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$beginDatum);
		else
		  $valutaKoers=1;
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }


	  $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$eindDatum,(substr($eindDatum, 5, 5) == '01-01')?true:false,$valuta,$beginDatum);
    $categorieVerdeling=$this->categorieVolgorde;

   // listarray($categorieVerdeling);
   	if($valuta <> 'EUR')
	  	$valutaKoers=getValutaKoers($valuta,$eindDatum);
		else
		  $valutaKoers=1;
    
    $extraVerdelingData=array();
		if(isset($this->extraVerdeling) && $this->extraVerdeling <> '')
    {
      $extraVerdeling = true;
    }
		else
    {
      $extraVerdeling = false;
    }
	  foreach ($fondswaarden['eindmaand'] as $regel)
	  {
	    $regel['actuelePortefeuilleWaardeEuro']=$regel['actuelePortefeuilleWaardeEuro']/$valutaKoers;
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      
      if($extraVerdeling==true)
			{
				if(isset($regel[$this->extraVerdeling]) && $regel[$this->extraVerdeling] <> '')
        {
          $extraVerdelingData[$regel[$this->extraVerdeling]]+= $regel['actuelePortefeuilleWaardeEuro'];
        }
        elseif($regel['type']=='rekening')
				{
          $extraVerdelingData['Liquiditeiten']+= $regel['actuelePortefeuilleWaardeEuro'];
				}
				else
				{
          $extraVerdelingData['geenCategorie']+= $regel['actuelePortefeuilleWaardeEuro'];
				}
			}
	  }
    
    $wegingsDatum=checkPerfStart($portefeuille,$wegingsDatum);

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
	  $DB=new DB();

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$eindDatum."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	"  / (TO_DAYS('".$eindDatum."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$beginDatum."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$eindDatum."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

	if(($totaalWaarde['begin'] == 0 || $this->methode == 'TWR') && empty($weging['totaal1']) && $weging['totaal2']>0)
    $weging['totaal1']=$weging['totaal2'];

  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
	$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
//echo "<br>\n $query <br>\n";
//echo "perf $beginDatum -> $eindDatum | $wegingsDatum | $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") - ".$weging['totaal2'].") / $gemiddelde) * 100;<br>\n";
	  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$onttrekkingen = getOnttrekkingen($portefeuille,$beginDatum, $eindDatum,$valuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

		$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery)  AS totaalkosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $kosten = $db->lookupRecord();

    $query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaalOpbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$beginDatum' AND Rekeningmutaties.Boekdatum <= '$eindDatum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Kosten ";
    $db->SQL($query);
    $opbrengsten = $db->lookupRecord();

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['totaalOpbrengsten']+$kosten['totaalkosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;
    
    if(db2jul($wegingsDatum)>db2jul($eindDatum))
    {
      $performance=0;
    }

    foreach ($categorieVerdeling as $cat=>$waarde)
      $categorieVerdeling[$cat]=$waarde."";
    $data['database']=0;
    $data['valuta']=$valuta;
    $data['periode']= $beginDatum."->".$eindDatum;
    $data['periodeForm']= date("d-m-Y",db2jul($beginDatum))." - ".date("d-m-Y",db2jul($eindDatum));
    $data['waardeBegin']=round($totaalWaarde['begin'],2);
    $data['waardeHuidige']=round($totaalWaarde['eind'],2);
    $data['waardeMutatie']=round($waardeMutatie,2);
    $data['stortingen']=round($stortingen,2);
    $data['onttrekkingen']=round($onttrekkingen,2);
    $data['resultaatVerslagperiode'] = round($resultaatVerslagperiode,2);
    $data['gemiddelde'] = $gemiddelde;
    $data['kosten'] = round($kosten['totaalkosten'],2);
    $data['opbrengsten'] = round($opbrengsten['totaalOpbrengsten'],2);
    $data['performance'] =$performance;
    $data['ongerealiseerd'] =$ongerealiseerd;
    $data['rente'] = $opgelopenRente;
    $data['gerealiseerd'] =$koersResultaat;
    $data['extra']['cat']=$categorieVerdeling;
    if($extraVerdeling==true)
    {
      $data['extra'][$this->extraVerdeling] = $extraVerdelingData;
    }
    return $data;

	}


	function getWaarden($datumBegin,$datumEind,$portefeuille,$specifiekeIndex='',$methode='maanden',$valuta='EUR',$output='')
	{
	  if(is_array($portefeuille))
	  {
	    $portefeuilles=$portefeuille[1];
	    $portefeuille=$portefeuille[0];
	  }
		$db=new DB();
    $julBegin = db2jul($datumBegin);
    $beginDatum=date("Y-m-d",$julBegin);
    $julEind = db2jul($datumEind);

   	$eindjaar = date("Y",$julEind);
    $eindmaand = date("m",$julEind);
    $beginjaar = date("Y",$julBegin);
    $startjaar = date("Y",$julBegin);
    $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);

    $vorigeIndex = 100;
    $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
		 $datum=array();
  
		$this->methode=$methode;
  if($methode=='maanden')
  {
     $datum=$this->getMaanden($julBegin,$julEind);
     $type='m';
  }
  elseif($methode=='dagKwartaal')
  {
    $datum=$this->getDagen($julBegin,$julEind);
    $type='dk';
  }
  elseif($methode=='kwartaal')
  {
    $datum=$this->getKwartalen($julBegin,$julEind);
    $type='k';
  }
  elseif($methode=='jaar')
  {
    $datum=$this->getJaren($julBegin,$julEind);
    $type='j';
  }  
  elseif($methode=='TWR')
  {
    $datum=$this->getTWRstortingsdagen($portefeuille,$julBegin,$julEind,true);
    $type='t';
  }  
  elseif($methode=='dagYTD')
  {
     //$datum=$this->getDagen($julBegin,$julEind,'jaar');
     $datum=array();
      $newJul=$julBegin;
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y-m-d',$julBegin),'stop'=>date('Y-m-d',$newJul));
      }
     $type='dy';
  }
  elseif ($methode=='halveMaanden')
  {
    $datum=$this->getHalveMaanden($julBegin,$julEind);
    $type='2w';
  }
  elseif($methode=='weken')
  {
    $datum=$this->getWeken($julBegin,$julEind);
    $type='w';
  }
  elseif($methode=='dagen')
  {
    $datum=$this->getDagen2($julBegin,$julEind);
    $type='d';
  }
/*
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));
	$datum[$i]['stop']=$datumEind;
*/

	$i=1;
  $indexData=array();
	$indexData['index']=100;
	$indexData['specifiekeIndex']=100;
	$kwartaalBegin=100;

	$huidigeIndex=$specifiekeIndex;
  $jsonOutput=array('label'=>$portefeuille,'data'=>array());
	foreach ($datum as $periode)
	{
	    if($specifiekeIndex != '')
	    {
	      //if($specifiekeIndex )
	      $query="SELECT specifiekeIndex FROM HistorischeSpecifiekeIndex WHERE portefeuille='$portefeuille' AND tot > '".$periode['stop']."' ORDER BY tot desc limit 1";
	      $db->SQL($query);
        $oldIndex=$db->lookupRecord();
        if($oldIndex['specifiekeIndex'] <> '')
        {
          $specifiekeIndex=$oldIndex['specifiekeIndex'];
          unset($startSpecifiekeIndexKoers);
        }
        else
        {
          if($huidigeIndex <> $specifiekeIndex)
            unset($startSpecifiekeIndexKoers);
          $specifiekeIndex=$huidigeIndex;
        }

	      if(empty($startSpecifiekeIndexKoers))
	      {
	        $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['start']."' ORDER BY Datum DESC limit 1 ";
	        $db->SQL($query);
	        $specifiekeIndexData = $db->lookupRecord();
	        $startSpecifiekeIndexKoers=$specifiekeIndexData['Koers'];
	      }
	      $query = "SELECT Koers FROM Fondskoersen WHERE fonds = '".$specifiekeIndex."' AND Datum <= '".$periode['stop']."' ORDER BY Datum DESC limit 1 ";
	      $db->SQL($query);
	      $specifiekeIndexData = $db->lookupRecord();
	      $specifiekeIndexKoers = $specifiekeIndexData['Koers'];
	    }
      $specifiekeIndexWaarden[$i] =($specifiekeIndexKoers/$startSpecifiekeIndexKoers)*100;


	  	$query = "SELECT indexWaarde, Datum, PortefeuilleWaarde, PortefeuilleBeginWaarde, Stortingen, Onttrekkingen, Opbrengsten, Kosten ,Categorie, gerealiseerd,ongerealiseerd,rente,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille = '".$portefeuille."' AND
		            Datum = '".substr($periode['stop'],0,10)."' ";

	  	if(db2jul($periode['start']) == db2jul($periode['stop']))
	  	{

	  	}
	  	elseif($db->QRecords($query) > 0 && ($valuta == 'EUR' || $valuta == '' || $this->forceDbLoad==true) )
	  	{
	  	  $dbData = $db->nextRecord();
        $indexData['database']=1;
	  	  $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	  $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	  $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
        $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	  $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	  $indexData['stortingen'] = $dbData['Stortingen'];
	  	  $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
        if(round($indexData['waardeBegin'],2)==0.0 && round($indexData['stortingen'],2)==0.0 && round($indexData['onttrekkingen'],2)==0.0)
          $indexData['resultaatVerslagperiode']=0;
        else
	      $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	  $indexData['kosten'] = $dbData['Kosten'];
	  	  $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	  $indexData['performance'] = $dbData['indexWaarde'];
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	    $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	    $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	    $indexData['rente'] = $dbData['rente'];
  	    $indexData['extra'] = unserialize($dbData['extra']);
	  	}
	  	else
	  	{
	  	  if(isset($portefeuilles) && ($valuta == 'EUR' || $valuta == ''  || $this->forceDbLoad==true ))
	  	  {
	  	    $query = "SELECT  Datum, sum(PortefeuilleWaarde) as PortefeuilleWaarde, sum(PortefeuilleBeginWaarde) as PortefeuilleBeginWaarde,
	  	    sum(Stortingen) as Stortingen, sum(Onttrekkingen) as Onttrekkingen, sum(Opbrengsten) as Opbrengsten, sum(Kosten) as Kosten ,Categorie, SUM(gerealiseerd) as gerealiseerd,
	  	    sum(ongerealiseerd) as ongerealiseerd, sum(rente) as rente, sum(gemiddelde) as gemiddelde,extra
		            FROM HistorischePortefeuilleIndex
		            WHERE
		            Categorie = 'Totaal' AND periode='$type' AND
		            portefeuille IN ('".implode("','",$portefeuilles)."') AND
		            Datum = '".substr($periode['stop'],0,10)."' GROUP BY Datum";

	  	    if($db->QRecords($query) > 0)
	  	    {
	  	    $dbData = $db->nextRecord();
          $indexData['database']=1;
	  	    $indexData['periodeForm'] = jul2form(db2jul($periode['start']))." - ".jul2form(db2jul($periode['stop']));
	  	    $indexData['periode']= $periode['start']."->".$periode['stop'];
	  	    $indexData['waardeMutatie'] = $dbData['PortefeuilleWaarde']-$dbData['PortefeuilleBeginWaarde'];
          $indexData['waardeBegin'] = $dbData['PortefeuilleWaarde']-$indexData['waardeMutatie'];
	  	    $indexData['waardeHuidige'] = $dbData['PortefeuilleWaarde'];
	  	    $indexData['stortingen'] = $dbData['Stortingen'];
	  	    $indexData['onttrekkingen'] = $dbData['Onttrekkingen'];
	  	    if(round($indexData['waardeBegin'],2)==0.0 && round($indexData['stortingen'],2)==0.0 && round($indexData['onttrekkingen'],2)==0.0)
            $indexData['resultaatVerslagperiode']=0;
	  	    else
	          $indexData['resultaatVerslagperiode'] =  $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	  	    $indexData['kosten'] = $dbData['Kosten'];
	  	    $indexData['opbrengsten'] = $dbData['Opbrengsten'];
	  	    $indexData['performance'] = $indexData['resultaatVerslagperiode']/$dbData['gemiddelde']*100;
  	    //$indexData['resultaatVerslagperiode'] = $dbData['Opbrengsten']-$dbData['Kosten'];
  	      $indexData['gerealiseerd'] = $dbData['gerealiseerd'];
  	      $indexData['ongerealiseerd'] = $dbData['ongerealiseerd'];
  	      $indexData['rente'] = $dbData['rente'];
  	      $indexData['extra'] = unserialize($dbData['extra']);
  	      //listarray($indexData);
	    	  }
	    	  else
	  	      $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille));
	  	  }
        else
	  	    $indexData = array_merge($indexData,$this->BerekenMutaties2($periode['start'],$periode['stop'],$portefeuille,$valuta));
	  	}

	  	$indexData['datum'] = jul2sql(form2jul(substr($indexData['periodeForm'],-10,10)));
//          echo $indexData['periode']." ".$indexData['performance']."<br>\n";
	  	if($methode=='dagKwartaal')
	  	{
	  	  if($periode['blok'] <> $lastBlok)
	  	    $kwartaalBegin=$indexData['index'];
	  	  $indexData['index'] = ($kwartaalBegin  * (100+$indexData['performance'])/100);
	  	  $lastBlok=$periode['blok'];
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'performance'=>$indexData['performance'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	if($methode=='dagYTD')
	  	{
	  	  $indexData['index']=$indexData['performance']+100;
        $data[$i] = array('index'=>$indexData['index'],'performance'=>$indexData['performance'],'datum'=>$indexData['datum'],'periodeForm'=>$indexData['periodeForm']);
	  	}
	  	else
	  	{

        if(empty($specifiekeIndexWaarden[$i-1]))
	    	  $indexData['specifiekeIndexPerformance'] = $specifiekeIndexWaarden[$i]-100;
	    	else
	    	  $indexData['specifiekeIndexPerformance'] =($specifiekeIndexWaarden[$i]/$specifiekeIndexWaarden[$i-1])*100 -100;
	      $indexData['specifiekeIndex'] = ($indexData['specifiekeIndex']  * (100+$indexData['specifiekeIndexPerformance'])/100) ;
	      if(empty($indexData['index']))
	        $indexData['index']=100;
	  	  $indexData['index'] = ($indexData['index']  * (100+$indexData['performance'])/100);
	      $data[$i] = $indexData;

	  	}
      /*)
      if($output=='html')
      {
        
        $jsonOutput['data'][]=array(adodb_db2jul($data[$i]['datum'])*1000,$data[$i]['index']);
        //
        $dbData=mysql_real_escape_string(serialize($data[$i]));
        $query="INSERT INTO CRM_htmlData SET 
        portefeuille='$portefeuille',
        datum='".$data[$i]['datum']."',
        dataType='perf',
        data='".$dbData."',
        add_user='$USR',change_user='$USR',add_date=NOW(),change_date=NOW()";
        $db->SQL($query);
        $db->Query();
        //
        file_put_contents('../tmp/perf.json',json_encode($jsonOutput));
      }
      */

  $i++;
	}

	return $data;
	}


	function periodePerformance($portefeuille,$datumBegin,$datumEind,$portefeuilleStartdatum='')
	{
		$beginwaarde=0;
		$eindwaarde=0;

		$gegevens=berekenPortefeuilleWaarde($portefeuille,$datumBegin,(substr($datumBegin, 5, 5) == '01-01')?true:false,'EUR',$datumBegin);
		foreach($gegevens as $waarde)
			$beginwaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
		$gegevens=berekenPortefeuilleWaarde($portefeuille,$datumEind,(substr($datumEind, 5, 5) == '01-01')?true:false,'EUR',$datumBegin);
		foreach($gegevens as $waarde)
			$eindwaarde+=$waarde['actuelePortefeuilleWaardeEuro'];

		if($datumBegin == $portefeuilleStartdatum)
			$weegDatum=date("Y-m-d",db2jul($datumBegin)+24*3600);
		else
			$weegDatum=$datumBegin;
		
		if(db2jul($portefeuilleStartdatum)>=db2jul($weegDatum))
		{
      $weegDatum=date("Y-m-d",db2jul($portefeuilleStartdatum)+24*3600);
		}

		$DB=new DB();
		$query = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$weegDatum."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
			"FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
			"WHERE ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
		$DB->SQL($query);// logit($query);
		$DB->Query();
		$weging = $DB->NextRecord();

		$gemiddelde = $beginwaarde + $weging['totaal1'];
		if($gemiddelde <> 0)
			$performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / $gemiddelde) * 100;

		return $performance;
	}

	function getWaardenATT($datumBegin,$datumEind,$portefeuille,$categorie='Totaal',$periodeBlok='maand',$valuta='EUR')
	{
	  $this->berekening = new rapportATTberekening($portefeuille);
	  if(is_array($categorie))
	    $this->berekening->categorien = $categorie;
	  else
      $this->berekening->categorien[] = $categorie;
    $this->berekening->pdata['pdf']=true;
    $this->berekening->attributiePerformance($portefeuille,$datumBegin,$datumEind,'rapportagePeriode',$valuta,$periodeBlok);

    foreach ($this->berekening->categorien as $categorie)
    {
      $indexData['index'] = 100;
      foreach ($this->berekening->performance as $periode=>$data)
      {
        if($periode != 'rapportagePeriode')
        {
    	  $indexData['periodeForm']    = jul2form(db2jul(substr($periode,0,10)))." - ".jul2form(db2jul(substr($periode,11)));
  	    $indexData['waardeMutatie']  = $data['totaalWaarde'][$categorie]['eind']-$data['totaalWaarde'][$categorie]['begin'];
        $indexData['waardeBegin']    = $data['totaalWaarde'][$categorie]['begin'];
	  	  $indexData['waardeHuidige']  = $data['totaalWaarde'][$categorie]['eind'];
	  	  $indexData['stortingen']     = $data['AttributieStortingenOntrekkingen'][$categorie]['stortingen'];
	  	  $indexData['onttrekkingen']  = $data['AttributieStortingenOntrekkingen'][$categorie]['onttrekkingen'];
	  	  $indexData['resultaatVerslagperiode'] = $indexData['waardeMutatie'] - $indexData['stortingen'] + $indexData['onttrekkingen'];
	   	  $indexData['kosten']         = $data['totaal']['kosten'][$categorie];
	   	  $indexData['opbrengsten']    = $data['totaal']['opbrengsten'][$categorie];
	   	  $indexData['performance']    = $data['totaal']['performance'][$categorie];
	   	  $indexData['index']          = ($indexData['index']  * (100+$indexData['performance'])/100);
	   	  $indexData['datum']          = substr($periode,11);
	   	  if(count($this->berekening->categorien)>1)
	   	  $tmp[$categorie][] = $indexData;
	   	  else
	  	  $tmp[] = $indexData;
        }
      }
    }
	  return $tmp;
	}




	function Bereken($weekWaarden=false)
	{

    $selectie = new portefeuilleSelectie($this->selectData);
    $selectie->getRecords();
    $portefeuilles = $selectie->getSelectie();

		$DBs = new DB();
    ini_set('serialize_precision',12);
		$DB2 = new DB();
		if(count($portefeuilles) <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / count($portefeuilles);
		}


  	foreach($portefeuilles as $pdata)
		{
		 	if($this->progressbar)
		  {
		  	$pro_step += $pro_multiplier;
		  	$this->progressbar->moveStep($pro_step);
		  }
      
      if($pdata['Vermogensbeheerder'] == "REN")
      {
        global $__appvar;
        include_once("../classes/AE_cls_fpdf.php");
        include_once($__appvar["basedir"].'/classes/fpdi/fpdi.php');
        include_once("rapport/PDFRapport.php");
        include_once("rapport/include/ATTberekening_L68.php");
        include_once("rapport/include/RapportPERF_L68.php");
        
        $pdf = new PDFRapport('L','mm');
        $pdf->rapportageValuta = "EUR";
        $pdf->ValutaKoersEind  = 1;
        $pdf->ValutaKoersStart = 1;
        $pdf->ValutaKoersBegin = 1;
        $pdf->portefeuilledata=$pdata;
        loadLayoutSettings($pdf, $pdata['Portefeuille']);
  
        $DB = new DB();
        $DB->SQL("SELECT * FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='" . $pdata['Portefeuille'] . "'");
        $DB->Query();
        if($DB->records())
        {
          $vpdata = $DB->nextRecord();
          $consolidatiePortefeuilles = array();
          for ($i = 1; $i < 41; $i++)
          {
            if ($vpdata['Portefeuille' . $i] <> '')
            {
              $consolidatiePortefeuilles[] = $vpdata['Portefeuille' . $i];
            }
          }
          $pdf->portefeuilles = $consolidatiePortefeuilles;
        }
        $DB->SQL("SELECT Portefeuille FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille='" . $pdata['Portefeuille'] . "'");
        $DB->Query();
        if($DB->records())
				{
				  while($vpdata = $DB->nextRecord())
					{
						if(!in_array($vpdata['Portefeuille'],$consolidatiePortefeuilles))
            $consolidatiePortefeuilles[] = $vpdata['Portefeuille'];
					}
          $pdf->portefeuilles = $consolidatiePortefeuilles;
				}
        $pdf->portefeuille=$pdata['Portefeuille'];
  
        if($weekWaarden==true)
        {
          $type = 'w';
          $datumPeriode = $this->getWeken($this->selectData['datumVan'], $this->selectData['datumTm'], true);
        }
        else
				{
					$type='m';
          $datumPeriode = $this->getMaanden($this->selectData['datumVan'],$this->selectData['datumTm']);
				}
   

        foreach($datumPeriode as $periode)
        {
          $config=array();
          $config['type']=$type;
         
          $config['stop']=$periode['stop'];
          $config['aanvullen']=$this->selectData['aanvullen'];
  
          if(db2jul($pdata['Startdatum']) > db2jul($periode['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
          {
            continue;
          }
					elseif(db2jul($periode['start']) == db2jul($periode['stop']))
          {
            continue;
          }
          if(db2jul($pdata['Startdatum']) > db2jul($periode['start']))
            $periode['start']=substr($pdata['Startdatum'],0,10);
  
          $config['start']=$periode['start'];
          

          $attPdf = new RapportPERF_L68($pdf, $pdata['Portefeuille'], $periode['start'], $periode['stop']);
          $attPdf->pdf->lastPOST['doorkijk']=1;
          if($weekWaarden==true)
            $attPdf->att->perioden='jaar';
          $attData = $attPdf->att->bereken($periode['start'], $periode['stop'], 'Hoofdcategorie',false);
          $attPdf->att->indexSuperUser=$this->indexSuperUser;
          $attPdf->att->HPIBijwerken($attData,$pdata,$config);
        }
    }
    elseif($pdata['Vermogensbeheerder'] == "SEQ")
    {
      global $__appvar;
      include_once("../classes/AE_cls_fpdf.php");
      include_once($__appvar["basedir"].'/classes/fpdi/fpdi.php');
      include_once("rapport/PDFRapport.php");
      include_once("rapport/include/ATTberekening_L22.php");
      include_once("rapport/include/RapportRISK_L22.php");
  
      $pdf = new PDFRapport('L','mm');
      $pdf->rapportageValuta = "EUR";
      $pdf->ValutaKoersEind  = 1;
      $pdf->ValutaKoersStart = 1;
      $pdf->ValutaKoersBegin = 1;
      $pdf->portefeuilledata=$pdata;
      loadLayoutSettings($pdf, $pdata['Portefeuille']);
    
      if($weekWaarden==true)
      {
        $type = 'w';
        $datumPeriode = $this->getWeken($this->selectData['datumVan'], $this->selectData['datumTm'], true);
      }
      else
      {
        $type='m';
        $datumPeriode = $this->getMaanden($this->selectData['datumVan'],$this->selectData['datumTm']);
      }
    
      foreach($datumPeriode as $periode)
      {
        $config=array();
        $config['type']=$type;
    
        $config['stop']=$periode['stop'];
        $config['aanvullen']=$this->selectData['aanvullen'];
    
        if(db2jul($pdata['Startdatum']) > db2jul($periode['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
        {
          continue;
        }
				elseif(db2jul($periode['start']) == db2jul($periode['stop']))
        {
          continue;
        }
        if(db2jul($pdata['Startdatum']) > db2jul($periode['start']))
          $periode['start']=substr($pdata['Startdatum'],0,10);
    
        $config['start']=$periode['start'];
    
    
        $attPdf = new RapportRISK_L22($pdf, $pdata['Portefeuille'], $periode['start'], $periode['stop']);
        //$attPdf->pdf->lastPOST['doorkijk']=1;
        //if($weekWaarden==true)
        //  $attPdf->att->perioden='jaar';
        $attData = $attPdf->att->bereken($periode['start'], $periode['stop'], 'categorien');//'Hoofdcategorie'
        $attPdf->att->indexSuperUser=$this->indexSuperUser;
        $attPdf->att->HPIBijwerken($attData,$pdata,$config);
       
      }
    }
   	elseif($weekWaarden==false && ($pdata['Vermogensbeheerder'] == 'WAT' || $pdata['Vermogensbeheerder'] == 'WAT1' || $pdata['Vermogensbeheerder'] == 'WWO'))
    {
      $pdata['rapportageDatum']=jul2sql($this->selectData['datumTm']);
      $pdata['rapportageDatumVanaf']=jul2sql($this->selectData['datumVan']);
      $pdata['aanvullen']=$this->selectData['aanvullen'];
      $pdata['debug']=$this->selectData['debug'];
      $berekening = new rapportATTberekening($pdata);
      $berekening->pdata['pdf']=false;
      $berekening->indexSuperUser=$this->indexSuperUser;
      $berekening->Bereken();
      // listarray($berekening->performance);
      //  exit;
    }
    else
    {

      $pstartJul = db2jul($pdata['Startdatum']);
	    if($pstartJul > $this->selectData['datumVan'])
	      $julBegin= $pstartJul;
      else
        $julBegin = $this->selectData['datumVan'];

      $julEind = $this->selectData['datumTm'];
   	  $datum = $this->getMaanden($julBegin,$julEind);
      $type='m';
      $portefeuille = $pdata['Portefeuille'];

			if($weekWaarden==true)
			{
				$type = 'w';
				$datum = $this->getWeken($julBegin,$julEind,true);
			}

		$indexAanwezig = array();
	  if ($this->selectData['aanvullen'] == 1)
	  {
	    $query = "SELECT Datum FROM HistorischePortefeuilleIndex WHERE Portefeuille = '$portefeuille' AND periode='$type' AND Categorie = 'Totaal' ";
	    $DB2->SQL($query);
	    $DB2->Query();
      while ($data = $DB2->nextRecord())
	    {
         $indexAanwezig[] = $data['Datum'];
	    }
    }

    //rvv debug
		if($weekWaarden==false && ($pdata['Vermogensbeheerder'] == "HEN" || $pdata['PerformanceBerekening'] == 7))
    {
      $datum=array();
      $newJul=$julBegin;
      $type='dy';
      while($newJul < $julEind)
      {
        $newJul=$newJul+86400;
        $datum[]=array('start'=>date('Y',$julBegin)."-01-01",'stop'=>date('Y-m-d',$newJul));
      }
    }
    //echo $portefeuille."<br>\n";
//listarray($datum);
			for ($i=0; $i < count($datum); $i++) //Bereken Performance voor data
		  {
		    $done=false;
	      $startjaar = date("Y",db2jul($datum[$i]['start']))+1;
   	    if(db2jul($datum[$i]['start']) == mktime (0,0,0,1,0,$startjaar))
	        $datum[$i]['start']= $startjaar.'-01-01';

	      if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['start']))
	        $datum[$i]['start'] = $pdata['Startdatum'];

			   if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
			   {
			     $datum[$i]['performance']=0;
			     $done = true;
			   }
			   elseif(in_array(substr($datum[$i]['stop'],0,10),$indexAanwezig))
		     {
           $done = true;
  		   }
  		   elseif(db2jul($datum[$i]['start']) == db2jul($datum[$i]['stop']))
	  	   {
	  	    //echo "overslaan<br>";
	  	   }
			   else // Normale berekening.
			   {
			     if($weekWaarden==false && ($pdata['Vermogensbeheerder'] == "HEN"))
			     {
			     	 global $__appvar;
             include_once("../classes/AE_cls_fpdf.php");
             include_once("rapport/PDFRapport.php");
             include_once("rapport/include/RapportPERF_L26.php");

			       $pdf = new PDFRapport('L','mm');
             $pdf->rapportageValuta = "EUR";
	           $pdf->ValutaKoersEind  = 1;
             $pdf->ValutaKoersStart = 1;
             $pdf->ValutaKoersBegin = 1;
             loadLayoutSettings($pdf, $portefeuille);

             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['start'],(substr($datum[$i]['start'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$datum[$i]['start']);
             $fondswaarden = berekenPortefeuilleWaarde($portefeuille,$datum[$i]['stop'],(substr($datum[$i]['stop'], 5, 5) == '01-01')?true:false,$pdata['RapportageValuta'],$datum[$i]['start']);
             vulTijdelijkeTabel($fondswaarden ,$portefeuille,$datum[$i]['stop']);

             $pdf->PortefeuilleStartdatum=$pdata['Startdatum'];
             $pdf->HENIndex=true;
             $rapport = new RapportPERF_L26($pdf, $portefeuille, $datum[$i]['start'], $datum[$i]['stop']);
	           $rapport->writeRapport();

             foreach ($datum as $periode)
             {
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['start']);
               verwijderTijdelijkeTabel($portefeuille,$datum[$i]['stop']);
             }
             $PerformanceMeting=$rapport->pdf->excelData;

             $performance= number_format($PerformanceMeting[0][37],4) ;
             $data['waardeHuidige']=$PerformanceMeting[0][29];
			       $data['waardeBegin']=$PerformanceMeting[0][28];
 		         $data['stortingen']=$PerformanceMeting[0][30];
			       $data['onttrekkingen']=0;
			       $data['opbrengsten']=$PerformanceMeting[0][32];
			       $data['kosten']=$PerformanceMeting[1][13];
			     }
			     else
			     {
             $data = $this->berekenMutaties2($datum[$i]['start'],$datum[$i]['stop'],$portefeuille);
		         $performance = number_format($data['performance'],4) ;
			     }
           $senarioWaarden=$this->getScenario($portefeuille,$datum[$i]['stop'],$data['waardeHuidige']);
		    $query = "SELECT id FROM HistorischePortefeuilleIndex WHERE periode='$type' AND Portefeuille = '$portefeuille' AND Datum = '".substr($datum[$i]['stop'],0,10)."' ";
		    $DB2->SQL($query);
		    $DB2->Query();
		    $records = $DB2->records();
		    if($records > 1)
		    {
		      echo "<script  type=\"text/JavaScript\">alert('Dubbele record gevonden voor portefeuille $portefeuille en datum ".substr($datum[$i]['stop'],0,10)."'); </script>";
		    }
		    $qBody=	    " Portefeuille = '$portefeuille' ,
			                Categorie = 'Totaal',
			                PortefeuilleWaarde = '".round($data['waardeHuidige'],2)."' ,
			                PortefeuilleBeginWaarde = '".round($data['waardeBegin'],2)."' ,
 		                  Stortingen = '".round($data['stortingen'],2)."' ,
			                Onttrekkingen = '".round($data['onttrekkingen'],2)."' ,
			                Opbrengsten = '".round($data['opbrengsten'],2)."' ,
			                Kosten = '".round($data['kosten'],2)."' ,
			                Datum = '".$datum[$i]['stop']."',
			                IndexWaarde = '$performance' ,
                      periode='$type',
			                gerealiseerd = '".round($data['gerealiseerd'],2)."',
			                ongerealiseerd = '".round($data['ongerealiseerd'],2)."',
			                rente = '".round($data['rente'],2)."',
			                extra = '".addslashes(serialize($data['extra']))."',
			                gemiddelde = '".round($data['gemiddelde'],2)."',
			                ";
        if(count($senarioWaarden)>0)
        {
          $qBody.="scenarioKansOpDoel='".round($senarioWaarden['scenarioKansOpDoel'],2)."', 
                   scenarioVerwachtVermogen='".round($senarioWaarden['scenarioVerwachtVermogen'],2)."',
                   scenarioProfiel='".$senarioWaarden['scenarioProfiel']."',
                   ";
        }              

		    if ($records > 0)
		    {
		      $id = $DB2->lookupRecord();
		      $id = $id['id'];


          if($this->indexSuperUser==false && date("Y",db2jul($datum[$i]['stop'])) != date('Y'))
          {
            $query="select 1";
            echo "Geen rechten om records in het verleden te vernieuwen. $portefeuille ".$datum[$i]['stop']."<br>\n";
          }
          else
          {
            $query = "UPDATE HistorischePortefeuilleIndex SET $qBody change_date = NOW(), change_user = '$this->USR' WHERE id = $id ";
          }
		    }
		    else
		    {
			    $query = "INSERT INTO HistorischePortefeuilleIndex SET $qBody change_date = NOW(),change_user = '$this->USR', add_date = NOW(), add_user = '$this->USR' ";
		    }
			  if((db2jul($pdata['Startdatum']) < db2jul($datum[$i]['stop'])) && $done == false)
			  {
			    $DB2->SQL($query);
			    $DB2->Query();
			  }
		  }
		}
	}
		}
	if($this->progressbar)
	{
	  $this->progressbar->hide();
  	exit;
	}
}

function getScenario($portefeuille,$datum,$huidigeWaarde)
{
  global $__appvar;
  $DB=new DB();
  $query="SELECT check_module_SCENARIO,Vermogensbeheerders.Vermogensbeheerder FROM Vermogensbeheerders 
      JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder 
      WHERE portefeuille='".$portefeuille."'";
 	$DB->SQL($query);
	$DB->Query();
	$check_module_SCENARIO = $DB->nextRecord(); 
  if($check_module_SCENARIO['check_module_SCENARIO']==0)
    return array();
    
  $query="SELECT id FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
	$DB->SQL($query);
	$DB->Query();
  $crmId = $DB->nextRecord();   
 
  $sc= new scenarioBerekening($crmId['id']);
  $sc->CRMdata['startvermogen']=$huidigeWaarde;
  $sc->CRMdata['startdatum']=$datum;
  if(!$sc->loadMatrix())
    $sc->createNewMatix(true);
  $sc->berekenSimulaties(0,10000);
  $scenarioKansOpDoel=$sc->berekenDoelKans();
  $sc->berekenVerdeling();
  if(isset($sc->verwachteWaarden['Normaal']))
    $scenarioVerwachtVermogen=$sc->verwachteWaarden['Normaal'];
  else
    $scenarioVerwachtVermogen=$sc->gemiddelde;  

  return array('scenarioKansOpDoel'=>$scenarioKansOpDoel,'scenarioVerwachtVermogen'=>$scenarioVerwachtVermogen,'scenarioProfiel'=>$sc->CRMdata['gewenstRisicoprofiel']);

}

	function BerekenScenarios()
	{
	  $einddatum = jul2sql($this->selectData[datumTm]);

		$jaar = date("Y",$this->datumTm);

		// controle op einddatum portefeuille
		$extraquery  = " Portefeuilles.Einddatum > '".jul2db($this->selectData[datumTm])."' AND";

		// selectie scherm.
		if($this->selectData[portefeuilleTm])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$this->selectData[portefeuilleVan]."' AND Portefeuilles.Portefeuille <= '".$this->selectData[portefeuilleTm]."') AND";
		if($this->selectData[vermogensbeheerderTm])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$this->selectData[vermogensbeheerderVan]."' AND Portefeuilles.Vermogensbeheerder <= '".$this->selectData[vermogensbeheerderTm]."') AND ";
		if($this->selectData[accountmanagerTm])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$this->selectData[accountmanagerVan]."' AND Portefeuilles.Accountmanager <= '".$this->selectData[accountmanagerTm]."') AND ";
		if($this->selectData[depotbankTm])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$this->selectData[depotbankVan]."' AND Portefeuilles.Depotbank <= '".$this->selectData[depotbankTm]."') AND ";
		if($this->selectData[AFMprofielTm])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$this->selectData[AFMprofielVan]."' AND Portefeuilles.AFMprofiel <= '".$this->selectData[AFMprofielTm]."') AND ";
		if($this->selectData[RisicoklasseTm])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$this->selectData[RisicoklasseVan]."' AND Portefeuilles.Risicoklasse <= '".$this->selectData[RisicoklasseTm]."') AND ";
		if($this->selectData[SoortOvereenkomstTm])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$this->selectData[SoortOvereenkomstVan]."' AND Portefeuilles.SoortOvereenkomst <= '".$this->selectData[SoortOvereenkomstTm]."') AND ";
		if($this->selectData[RemisierTm])
			$extraquery .= " (Portefeuilles.Remisier >= '".$this->selectData[RemisierVan]."' AND Portefeuilles.Remisier <= '".$this->selectData[RemisierTm]."') AND ";
		if($this->selectData['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$this->selectData['clientVan']."' AND Portefeuilles.Client <= '".$this->selectData['clientTm']."') AND ";
		if (count($this->selectData['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$this->selectData['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}
    if($this->selectData['metConsolidatie']=='0')
    {
      $extraquery .= " AND Portefeuilles.consolidatie=0 ";
    }
    

		if(checkAccess($type))
			$join = "";
		else
			$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$this->USR."'";

		$query = " SELECT ".
						 " Portefeuilles.Vermogensbeheerder, ".
						 " Portefeuilles.Risicoklasse, ".
						 " Portefeuilles.Portefeuille, ".
						 " Portefeuilles.Startdatum, ".
						 " Portefeuilles.Einddatum, ".
						 " Portefeuilles.Client, ".
						 " Portefeuilles.Depotbank, ".
			//			 " Portefeuilles.RapportageValuta, ".
						 " Vermogensbeheerders.attributieInPerformance,
						   Vermogensbeheerders.PerformanceBerekening, ".
						 " Clienten.Naam,  ".
						 " Portefeuilles.ClientVermogensbeheerder  ".
					 " FROM (Portefeuilles, Clienten ,Vermogensbeheerders) ".$join." WHERE ".$extraquery.
					 " Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
					 " ORDER BY Portefeuilles.Portefeuille ";

		$DBs = new DB();
		$DBs->SQL($query);
		$DBs->Query();

		$DB2 = new DB();
		$records = $DBs->records();
		if($records <= 0)
		{
			echo "<b>Fout: geen portefeuilles binnen selectie!</b>";
			if($this->progressbar)
			$this->progressbar->hide();
			exit;
		}

		if($this->progressbar)
		{
			$this->progressbar->moveStep(0);
			$pro_step = 0;
			$pro_multiplier = 100 / $records;
		}


  	while($pdata = $DBs->nextRecord())
		{
		 	if($this->progressbar)
		  {
		  	$pro_step += $pro_multiplier;
		  	$this->progressbar->moveStep($pro_step);
		  }
      $pstartJul = db2jul($pdata['Startdatum']);
      $julBegin=$pstartJul;
	    //if($pstartJul > $this->selectData['datumVan'])
	    //  $julBegin= $pstartJul;
     // else
      //  $julBegin = $this->selectData['datumVan'];

      $julEind = $this->selectData['datumTm'];
   	  $datum = $this->getMaanden($julBegin,$julEind);
      $portefeuille = $pdata['Portefeuille'];
  		$indexAanwezig = array();
      $query = "SELECT datum FROM HistorischeScenarios WHERE portefeuille = '$portefeuille' ";
	    $DB2->SQL($query);
	    $DB2->Query();
      while ($data = $DB2->nextRecord())
	    {
         $indexAanwezig[] = $data['datum'];
	    }

			for ($i=0; $i < count($datum); $i++) //Bereken Performance voor data
		  {
		    $done=false;
	      $startjaar = date("Y",db2jul($datum[$i]['start']))+1;
   	    if(db2jul($datum[$i]['start']) == mktime (0,0,0,1,0,$startjaar))
	        $datum[$i]['start']= $startjaar.'-01-01';

	      if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['start']))
	        $datum[$i]['start'] = $pdata['Startdatum'];

			   if(db2jul($pdata['Startdatum']) > db2jul($datum[$i]['stop'])) //Wanneer de portefeuille nog niet bestond geen performance.
			   {
			     $datum[$i]['performance']=0;
			     $done = true;
			   }
			   elseif(in_array(substr($datum[$i]['stop'],0,10),$indexAanwezig))
		     {
           $done = true;
  		   }
  		   elseif(db2jul($datum[$i]['start']) == db2jul($datum[$i]['stop']))
	  	   {
	  	    //echo "overslaan<br>";
	  	   }
			   else // Normale berekening.
			   {
			     //$data = $this->berekenMutaties2($datum[$i]['start'],$datum[$i]['stop'],$portefeuille);
         	$fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$datum[$i]['stop'],(substr($datum[$i]['stop'], 5, 5) == '01-01')?true:false,'EUR',$datum[$i]['start']);
          $data['waardeHuidige']=0;
          foreach ($fondswaarden['eindmaand'] as $regel)
	        {
	          $data['waardeHuidige']+=$regel['actuelePortefeuilleWaardeEuro'];
          }
          $senarioWaarden=$this->getScenario($portefeuille,$datum[$i]['stop'],$data['waardeHuidige']);
  	 	    $query = "SELECT id FROM HistorischeScenarios WHERE portefeuille = '$portefeuille' AND datum = '".substr($datum[$i]['stop'],0,10)."' ";
	  	    $DB2->SQL($query);
		      $DB2->Query();
		      $records = $DB2->records();
		      if($records > 1)
		      {
		       echo "<script  type=\"text/JavaScript\">alert('Dubbele record gevonden voor portefeuille $portefeuille en datum ".substr($datum[$i]['stop'],0,10)."'); </script>";
		      }
          $qBody="portefeuille = '$portefeuille',
                  datum='".$datum[$i]['stop']."',
                   scenarioKansOpDoel='".round($senarioWaarden['scenarioKansOpDoel'],2)."', 
                   scenarioVerwachtVermogen='".round($senarioWaarden['scenarioVerwachtVermogen'],2)."',
                   scenarioProfiel='".$senarioWaarden['scenarioProfiel']."',
                   ";
    
		    if ($records > 0)
		    {
		      $id = $DB2->lookupRecord();
		      $id = $id['id'];


          if($this->indexSuperUser==false && date("Y",db2jul($datum[$i]['stop'])) != date('Y'))
          {
            $query="select 1";
            echo "Geen rechten om records in het verleden te vernieuwen. $portefeuille ".$datum[$i]['stop']."<br>\n";
          }
          else
		        $query = "UPDATE
			                HistorischeScenarios
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR'
			               WHERE id = $id ";
		    }
		    else
		    {
			    $query = "INSERT INTO
			                HistorischeScenarios
			              SET
                      $qBody
			                change_date = NOW(),
			                change_user = '$this->USR',
			                add_date = NOW(),
			                add_user = '$this->USR' ";
		    }
			  if((db2jul($pdata['Startdatum']) < db2jul($datum[$i]['stop'])) && $done == false)
			  {
			    $DB2->SQL($query);
			    $DB2->Query();
			  }
		  }
		}
	
		}
	if($this->progressbar)
	{
	  $this->progressbar->hide();
  	exit;
	}
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

function getJaren($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);

	  $i=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,1,0,$beginjaar+$i);
	    $counterEnd   = mktime (0,0,0,1,0,$beginjaar+1+$i);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);
      
      if(db2jul($datum[$i]['stop']) < db2jul($datum[$i]['start']))
         unset($datum[$i]);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
       $i++;
	  }
	  return $datum;
}

function getHalveMaanden($julBegin, $julEind)
{
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $i=0;
	  $j=0;
	  $stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand+$j,0,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand+$j+1,0,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
        $datum[$i]['start'] = date('Y-m-d',$julBegin);
	    else
	      $datum[$i]['start'] =date('Y-m-d',$counterStart);

	    $tusenCounter= mktime (0,0,0,$beginmaand+$j,15,$beginjaar);
	    if($tusenCounter > $counterEnd)
	    {
	      $datum[$i]['stop']=date('Y-m-d',$julEind);
        break;
	    }
      if($tusenCounter > $julBegin)
      {
	      $datum[$i]['stop']=date('Y-m-d',$tusenCounter);
	      $i++;
	      $datum[$i]['start']=date('Y-m-d',$tusenCounter);
      }
	    $datum[$i]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$i]['start'] ==  $datum[$i]['stop'])
	      unset($datum[$i]);
        
        
      $i++;
      $j++;
	  }
	  return $datum;
}

  function getDagen2($julBegin, $julEind)
  {
	  $beginjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
	  $begindag = date("d",$julBegin);
	  $i=0;
    $counterEnd=0;
    $datum=array();
    while ($counterEnd < $julEind)
	  {
       $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
       $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+1,$beginjaar);
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd));
       $i++;
	  }
    return $datum;
  }
  
function getDagen($julBegin, $julEind,$periode='kwartaal')
{

  if($periode=='kwartaal')
    $blokken=$this->getKwartalen($julBegin, $julEind);
  elseif($periode=='maanden')
    $blokken=$this->getMaanden($julBegin, $julEind);
  elseif($periode=='jaar')
    $blokken=$this->getJaren($julBegin, $julEind);
  else
    $blokken=array('start'=>date("Y-m-d",$julBegin),'stop'=>date("Y-m-d",$julEind));

  foreach ($blokken as $blok=>$periode)
  {
    $julBegin=db2jul($periode['start']);
    $julEind=db2jul($periode['stop']);
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
       $datum[]=array('start'=>date('Y-m-d',$counterStart),'stop'=>date('Y-m-d',$counterEnd),'blok'=>$blok);
       $i++;
	  }
  }
  return $datum;
}

  function getTWRstortingsdagen($portefeuille,$julBegin, $julEind,$maandUltimoToevoegen=true)
  {
    $query="SELECT DATE(Rekeningmutaties.Boekdatum) as datum
    FROM Rekeningen Inner Join Rekeningmutaties ON Rekeningen.Rekening = Rekeningmutaties.Rekening
    WHERE Rekeningen.Portefeuille='$portefeuille'  AND
    Rekeningmutaties.Boekdatum >= '".date('Y-m-d',$julBegin)."' AND  Rekeningmutaties.Boekdatum <= '".date('Y-m-d',$julEind)."' AND  Rekeningmutaties.Grootboekrekening IN('STORT','ONTTR')
    GROUP BY Rekeningmutaties.Boekdatum
    ORDER BY Boekdatum";
    
    $DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$i=0;
		$start =date('Y-m-d',$julBegin);
		$eind =date('Y-m-d',$julEind);
		$lastdatum=$start;
    $datum=array();
	  while($mutaties = $DB->nextRecord())
		{
		  if($lastdatum <> $mutaties['datum'])
		  {
		    $datum[$i]['start'] = $lastdatum;
		    $datum[$i]['stop']  =$mutaties['datum'];
		  }
		  $lastdatum=$mutaties['datum'];
		  $i++;
		}

		if($lastdatum <> $eind)
		{
		  $datum[$i]['start'] = $lastdatum;
		  $datum[$i]['stop']  =$eind;
		}
  
    if($maandUltimoToevoegen==true)
    {
      $maanden=$this->getMaanden($julBegin, $julEind);
      $allePerioden=array();
      foreach($maanden as $maand)
      {
        $allePerioden[$maand['start']] = $maand['start'];
        $allePerioden[$maand['stop']] = $maand['stop'];
        
      }
      foreach($datum as $maand)
      {
        $allePerioden[$maand['start']] = $maand['start'];
        $allePerioden[$maand['stop']] = $maand['stop'];
      }
      ksort($allePerioden);
      $datum=array();
      $laatsteDag='';
      foreach($allePerioden as $dag)
			{
				if($laatsteDag<>'')
          $datum[]=array('start'=>$laatsteDag,'stop'=>$dag);
				$laatsteDag=$dag;
			}
    }
		return $datum;
  }

	function getWeken($julBegin, $julEind,$beginVrijdag=false)
  {
    $eindjaar = date("Y",$julEind);
	  $eindmaand = date("m",$julEind);
    $einddag = date("d",$julEind);
	  $beginjaar = date("Y",$julBegin);
	  $startjaar = date("Y",$julBegin);
	  $beginmaand = date("m",$julBegin);
    $begindag = date("d",$julBegin);
		$i=0;
		$iIndex=0;

		if($beginVrijdag<> false)
		{
			$julBeginOrg = $julBegin;
			$extraDagen = 0;
			$dagVanWeek = date('w', $julBegin);
			if ($dagVanWeek < 5)
			{
				$extraDagen = 5 - $dagVanWeek;
			}
			elseif ($dagVanWeek > 5)
			{
				$extraDagen = 12 - $dagVanWeek;
			}
			$begindag += $extraDagen;
			$julBegin = mktime(0, 0, 0, $beginmaand, $begindag, $beginjaar);


		if($julBegin<>$julBeginOrg && ($beginVrijdag===true || $beginVrijdag===1))
		{
			$datum[$iIndex]['start'] = date('Y-m-d', $julBeginOrg);
			$datum[$iIndex]['stop'] = date('Y-m-d', $julBegin);
			$iIndex++;
		}
		}

		$i=0;
	  $stop=mktime (0,0,0,$eindmaand,$einddag,$eindjaar);
		$counterStart=0;
  	while ($counterStart < $stop)
	  {
	    $counterStart = mktime (0,0,0,$beginmaand,$begindag+$i,$beginjaar);
	    $counterEnd   = mktime (0,0,0,$beginmaand,$begindag+$i+7,$beginjaar);
	    if($counterEnd >= $julEind)
	      $counterEnd = $julEind;

      if($i == 0)
      {
        $datum[$iIndex]['start'] = date('Y-m-d',$julBegin);
      }
	    else
	    {
	      $datum[$iIndex]['start'] =date('Y-m-d',$counterStart);
	      if(substr($datum[$iIndex]['start'],5,5)=='12-31')
	        $datum[$iIndex]['start']=(date('Y',$counterStart)+1)."-01-01";
	    }

	    $datum[$iIndex]['stop']=date('Y-m-d',$counterEnd);

	    if($datum[$iIndex]['start'] ==  $datum[$iIndex]['stop'] || db2jul($datum[$iIndex]['start']) > db2jul($datum[$iIndex]['stop']) || ($beginVrijdag!==true && $beginVrijdag==2 && date('w',$counterEnd)<>5 ))
        unset($datum[$iIndex]);
       $i=$i+7;
			 $iIndex++;
	  }

	  return $datum;
  }


}

?>
