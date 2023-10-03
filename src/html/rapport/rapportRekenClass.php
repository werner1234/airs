<?
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/06/27 16:21:20 $
File Versie					: $Revision: 1.199 $

$Log: rapportRekenClass.php,v $
Revision 1.199  2020/06/27 16:21:20  rvv
*** empty log message ***

Revision 1.198  2020/06/10 15:24:35  rvv
*** empty log message ***

Revision 1.197  2020/06/03 15:40:17  rvv
*** empty log message ***

Revision 1.196  2020/05/24 12:42:18  rvv
*** empty log message ***

Revision 1.195  2020/05/16 15:56:10  rvv
*** empty log message ***

Revision 1.194  2020/05/02 15:56:32  rvv
*** empty log message ***

Revision 1.193  2020/03/29 08:55:27  rvv
*** empty log message ***

Revision 1.192  2020/03/11 16:20:55  rvv
*** empty log message ***

Revision 1.191  2020/02/29 16:22:15  rvv
*** empty log message ***

Revision 1.190  2019/05/11 16:46:29  rvv
*** empty log message ***

Revision 1.189  2019/03/09 18:47:30  rvv
*** empty log message ***

Revision 1.188  2018/11/28 13:18:18  rvv
*** empty log message ***

Revision 1.187  2018/09/01 16:52:29  rvv
*** empty log message ***

Revision 1.186  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.185  2018/07/28 14:46:31  rvv
*** empty log message ***

Revision 1.184  2018/07/25 16:09:04  rvv
*** empty log message ***

Revision 1.183  2018/07/18 15:47:01  rvv
*** empty log message ***

Revision 1.182  2018/07/15 05:49:57  rvv
*** empty log message ***

Revision 1.180  2018/06/09 15:56:54  rvv
*** empty log message ***

Revision 1.179  2018/05/19 16:23:14  rvv
*** empty log message ***

Revision 1.178  2018/05/16 15:30:42  rvv
*** empty log message ***

Revision 1.177  2018/05/06 11:32:09  rvv
*** empty log message ***

Revision 1.176  2018/04/25 16:51:38  rvv
*** empty log message ***

Revision 1.175  2018/03/28 15:54:43  rvv
*** empty log message ***

Revision 1.174  2018/03/21 17:03:56  rvv
*** empty log message ***

Revision 1.173  2018/03/17 18:47:40  rvv
*** empty log message ***

Revision 1.172  2017/10/01 14:32:57  rvv
*** empty log message ***

Revision 1.171  2017/08/23 15:22:25  cvs
file splitsen tbv API interface

Revision 1.170  2017/08/23 14:00:41  cvs
file splitsen tbv API interface

Revision 1.169  2017/08/12 12:16:43  rvv
*** empty log message ***

Revision 1.168  2017/06/18 09:17:37  rvv
*** empty log message ***

Revision 1.167  2017/06/09 06:03:24  rvv
*** empty log message ***

Revision 1.166  2017/06/07 16:26:21  rvv
*** empty log message ***


*/

$p = explode("html/",getcwd());

$wwwvars = $p[0]."/html/rapport/wwwvars.php";


include_once($wwwvars);



///
/// in onderstaande file staan de functie die de API interface nodig heeft om de rapporten te genereren
///
include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass_minimal.php");


include_once($__appvar["basedir"]."/html/indexBerekening.php");





function getFondsMutaties($rekening, $van, $tot,$valuta = 'EUR')
{
  if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT ".
	"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet , ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit ".
	"FROM Rekeningmutaties ".
	"WHERE ".
	"Rekeningmutaties.Rekening = '".$rekening."' AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
	"Rekeningmutaties.Grootboekrekening = 'FONDS' ";
//	"Rekeningmutaties.Grootboekrekening <> 'STORT'  AND Rekeningmutaties.Grootboekrekening <> 'ONTTR' ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return ($data[subdebet] - $data[subcredit]);
}

function getFondsMutaties2($rekening, $van, $tot,$valuta = 'EUR',$debug=false)
{
  $DB = new DB();
  $query = "SELECT portefeuille from Rekeningen WHERE rekening = '$rekening' ";
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
  $portefeuille = $data['portefeuille'];

  if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers AS subdebet , ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers AS subcredit,
Rekeningmutaties.Transactietype,Rekeningmutaties.Fonds, Rekeningmutaties.Aantal
FROM Rekeningmutaties
	WHERE ".
	"Rekeningmutaties.Rekening = '".$rekening."' AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
	"Rekeningmutaties.Grootboekrekening = 'FONDS' ";
//	"Rekeningmutaties.Grootboekrekening <> 'STORT'  AND Rekeningmutaties.Grootboekrekening <> 'ONTTR' ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();

	$debetTotaal = 0;
	$ceditTotaal = 0;

	if($debug)
	  echo 'Bepaling getFondsMutaties <br>';

	while($data = $DB->nextRecord())
	{
//	  listarray($data);
	  $debetTotaal += $data['subdebet'];
	  if($data['Transactietype'] == 'V')
	  {
	    $tmp=fondsWaardeOpdatum($portefeuille,$data['Fonds'],$tot,$valuta);
	    $historischeAankoopWaarde = abs($data['Aantal']) * $tmp['fondsEenheid'] * $tmp['historischeWaarde'];
	    $ceditTotaal += $historischeAankoopWaarde;
//	    listarray($tmp);
	if($debug)
	    echo $data['Fonds']." cred:".$data['subcredit']." deb:".$data['subdebet']." historischeAankoopWaarde -> $historischeAankoopWaarde <br>".$data['Aantal']." * ".$tmp['fondsEenheid']." * ".$tmp['historischeWaarde']."<br>";
	  }
	  else
	  {
	if($debug)
	    echo $data['Fonds']." cred:".$data['subcredit']." deb:".$data['subdebet']."<br>";
	    $creditTotaal += $data['subcredit'];
	  }
	}
	if($debug)
    echo " cred:$ceditTotaal deb:$debetTotaal ->".($debetTotaal - $ceditTotaal)."<br>";
	return ($debetTotaal - $ceditTotaal);
}


function getRekeningOnttrekkingen($rekening, $van, $tot,$valuta = 'EUR')
{
  if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT ".
	"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet , ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit ".
	"FROM Rekeningmutaties ".
	"WHERE ".
	"Rekeningmutaties.Rekening = '".$rekening."' AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Onttrekking=1)
	";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return ($data['subdebet'] - $data['subcredit']);
}

function getRekeningBeginwaarde($rekening, $beginjaar ,$valuta = 'EUR')
{
  if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit , ".
	"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet ".
	"FROM Rekeningmutaties ".
	"WHERE ".
	"Rekeningmutaties.Rekening = '".$rekening."' AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"YEAR(Rekeningmutaties.Boekdatum) = '".$beginjaar."' AND ".
	"Rekeningmutaties.Grootboekrekening = 'VERM'
	 ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return ($data['subcredit'] - $data['subdebet']);
}

function getRekeningStortingen($rekening, $van, $tot,$valuta = 'EUR')
{
  if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit , ".
	"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet ".
	"FROM Rekeningmutaties ".
	"WHERE ".
	"Rekeningmutaties.Rekening = '".$rekening."' AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Storting=1)
	GROUP BY Rekeningmutaties.Grootboekrekening ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return ($data['subcredit'] - $data['subdebet']);
}

function getOnttrekkingen($portefeuille, $van, $tot,$valuta = 'EUR')
{
  if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT ".
	"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet , ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit ".
	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
	"WHERE ".
	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Onttrekking=1) ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return ($data['subdebet'] - $data['subcredit']);
}

function getStortingen($portefeuille, $van, $tot,$valuta = 'EUR')
{
  if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers)$koersQuery) AS subcredit , ".
	"SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers)$koersQuery) AS subdebet ".
	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
	"WHERE ".
	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$van."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$tot."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Storting=1) ";
	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return ($data['subcredit'] - $data['subdebet']);
}

function getAttributieStortingen($portefeuille, $van, $tot, $categorie = 'Totaal',$valuta = 'EUR' )
{
	if ($categorie != 'Totaal')
	{
	  $attributieQuery = " AND BeleggingssectorPerFonds.AttributieCategorie = '$categorie' ";
	  $attributieQuery .= " AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder ";
	}
	else
	{
	  $attributieQuery = " AND Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1) ";
	}

	if ($valuta != "EUR")
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	$query = "SELECT
	SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers) $koersQuery) AS subcredit ,
	SUM((ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers) $koersQuery) AS subdebet
 	FROM
	(Rekeningmutaties ,  Rekeningen, Portefeuilles)
	left  join BeleggingssectorPerFonds  on Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds
	WHERE Rekeningmutaties.Rekening = Rekeningen.Rekening AND
	Rekeningen.Portefeuille = '".$portefeuille."' AND
	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
 	Rekeningmutaties.Verwerkt = '1' AND
	Rekeningmutaties.Boekdatum > '".$van."' AND
	Rekeningmutaties.Boekdatum <= '".$tot."'
	".$attributieQuery ;

	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
//echo $categorie."<br>". $query."<br>";listarray($data);
    return ($data);
}

function meervoudigeModelPortefeuilleVerdeling($portefeuille,$rapportageDatum,$modelPort='')
{
	$db=new DB();

  $query="SELECT Vanaf FROM ModelPortefeuillesPerPortefeuille
  WHERE ModelPortefeuillesPerPortefeuille.Portefeuille='$portefeuille' AND Vanaf <= '$rapportageDatum'
  ORDER BY Vanaf desc limit 1";
  $db->SQL($query);
  $laatsteDatum=$db->lookupRecord();
  
	$query="SELECT ModelPortefeuille,Percentage/100 as Percentage FROM ModelPortefeuillesPerPortefeuille 
  WHERE ModelPortefeuillesPerPortefeuille.Portefeuille='$portefeuille' AND Vanaf = '".$laatsteDatum['Vanaf']."'
  ORDER BY Vanaf";
	$db->SQL($query);
	$db->Query();
	$verdeling=array();
	while($data=$db->nextRecord())
	{
		$verdeling[$data['ModelPortefeuille']]=$data['Percentage'];
	}
  if(count($verdeling)==0)
  {
    $query="SELECT vanaf FROM modelPortefeuillesPerModelPortefeuille WHERE modelPortefeuille='$modelPort' AND
    Vanaf <= '$rapportageDatum'  ORDER BY Vanaf desc limit 1";
    $db->SQL($query);
    $laatsteDatum=$db->lookupRecord();
  
    $query="SELECT modelPortefeuilleComponent,percentage/100 as percentage FROM modelPortefeuillesPerModelPortefeuille
  WHERE modelPortefeuillesPerModelPortefeuille.modelPortefeuille='$modelPort' AND vanaf = '".$laatsteDatum['vanaf']."'
  ORDER BY Vanaf";
    $db->SQL($query);
    $db->Query();
    $verdeling=array();
    while($data=$db->nextRecord())
    {
      $verdeling[$data['modelPortefeuilleComponent']]=$data['percentage'];
    }
  }
	
	return $verdeling;
}

function berekenMeervoudigeModelPortefeuille($portefeuille,$rapportageDatum,$modelPortefeuille='')
{
	$db=new DB();
	$verdeling=meervoudigeModelPortefeuilleVerdeling($portefeuille,$rapportageDatum,$modelPortefeuille);

  $percentageVelden=array('totaalAantal','beginPortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','actuelePortefeuilleWaardeEuro');
  
  
  $totaal=array();
  $modelportefeuilles=array();
  $totaleWaardeAlles=0;  
  foreach($verdeling as $modelportefeuille=>$percentage)
  {
		$query="SELECT Fixed,Beleggingscategorie FROM ModelPortefeuilles WHERE Portefeuille='".$modelportefeuille."'";
		$db->SQL($query);
		$db->Query();
		$modelType = $db->nextRecord();

		if($modelType['Fixed']==1)
			$portefeuilleDataTmp = berekenFixedModelPortefeuille($modelportefeuille,$rapportageDatum);
		else
      $portefeuilleDataTmp = berekenPortefeuilleWaarde($modelportefeuille, $rapportageDatum);
    $totaleWaardeModel=0;
    $portefeuilleData=array();
    //echo $modelportefeuille;
    //listarray($portefeuilleDataTmp);
    foreach($portefeuilleDataTmp as $index=>$waarden)
    { 
      //if($waarden['type']!='rente')
        $portefeuilleData[$index]=$waarden;
        $totaleWaardeModel+=$waarden['actuelePortefeuilleWaardeEuro'];
        $totaleWaardeAlles+=$waarden['actuelePortefeuilleWaardeEuro'];
    }
    $modelportefeuilles[$modelportefeuille]['onderdelen']=$portefeuilleData;
    $modelportefeuilles[$modelportefeuille]['totaleWaarde']=$totaleWaardeModel;
  }

  $vertaalArray=array('DuurzaamCategorieVolgorde'=>'duurzaamCategorieVolgorde','Hoofdcategorie'=>'hoofdcategorie','HoofdcategorieOmschrijving'=>'hoofdcategorieOmschrijving','AttributieCategorieVolgorde'=>'attributieCategorieVolgorde',
    'RegioOmschrijving'=>'regioOmschrijving','DuurzaamCategorieOmschrijving'=>'duurzaamCategorieOmschrijving','HoofdcategorieVolgorde'=>'hoofdcategorieVolgorde','Hoofdsector'=>'hoofdsector','HoofdsectorVolgorde'=>'hoofdsectorVolgorde',
    'BeleggingssectorVolgorde'=>'beleggingssectorVolgorde','AttributieCategorieOmschrijving'=>'attributieCategorieOmschrijving','AfmCategorieOmschrijving'=>'afmCategorieOmschrijving','RegioVolgorde'=>'regioVolgorde',
    'BeleggingscategorieVolgorde'=>'beleggingscategorieVolgorde','HoofdsectorOmschrijving'=>'hoofdsectorOmschrijving','BeleggingssectorOmschrijving'=>'beleggingssectorOmschrijving','BeleggingscategorieOmschrijving'=>'beleggingscategorieOmschrijving',
    'DuurzaamCategorie'=>'duurzaamCategorie','AfmCategorie'=>'afmCategorie','Beleggingscategorie'=>'beleggingscategorie','Beleggingssector'=>'beleggingssector','Valuta'=>'valuta','FondsEenheid'=>'fondsEenheid');
  foreach($verdeling as $modelportefeuille=>$percentage)
  {
      $portefeuilleData=$modelportefeuilles[$modelportefeuille]['onderdelen'];
      $modelAandeel=$modelportefeuilles[$modelportefeuille]['totaleWaarde']/$totaleWaardeAlles;

      foreach($portefeuilleData as $index=>$waarden)
      {
        if($waarden['type']=='rekening')
          $naam=$waarden['rekening'];
        else
          $naam=$waarden['fonds']; 
        $type=$waarden['type'];   
        
        foreach($waarden as $veld=>$waarde)
        {
          if(isset($vertaalArray[$veld]))
          {
            $veld=$vertaalArray[$veld];
          }
          if(!in_array($veld,$percentageVelden))
          {
            $totaal[$type][$naam][$veld]=$waarde;
          }
          else
          {
            $totaal[$type][$naam][$veld]+=($portefeuilleData[$index][$veld]/$modelAandeel)*$percentage;//
          }
        }
        //if( $waarden['fonds'] =='iShares III MSCI World')
        // echo $waarden['fonds']." | ".$waarden['actuelePortefeuilleWaardeEuro'].'/'.$modelAandeel."*$percentage | ".(($portefeuilleData[$index]['actuelePortefeuilleWaardeEuro']/$modelAandeel)*$percentage)."<br>\n";
        //echo $waarden['fonds']."|".($portefeuilleData[$index]['actuelePortefeuilleWaardeEuro']/$modelAandeel)*$percentage."<br>\n";
      }
      $portefeuilleDataNew=array();
      foreach($totaal as $type=>$fondsregels)
      {
        foreach($fondsregels as $fonds=>$regel)
          $portefeuilleDataNew[]=$regel;
      }
   } 
  /*
    $totaleWaardeModelTest=0;
    foreach($portefeuilleDataNew as $index=>$waarden)
    { 
        $totaleWaardeModelTest+=$waarden['actuelePortefeuilleWaardeEuro'];
    }
*/    //echo "eerste: $totaleWaardeAlles  Tweede: $totaleWaardeModelTest <br>\n";
    // listarray($portefeuilleDataNew);exit;
     return $portefeuilleDataNew;
}

function berekenFixedModelPortefeuille($portefeuille,$rapportageDatum)
{
  $db=new DB();
  $query="SELECT Datum as FixedDatum,Portefeuilles.Vermogensbeheerder  FROM ModelPortefeuilleFixed Join Portefeuilles ON ModelPortefeuilleFixed.Portefeuille = Portefeuilles.Portefeuille
  WHERE ModelPortefeuilleFixed.Portefeuille='$portefeuille' AND Datum <= '$rapportageDatum' ORDER BY Datum desc limit 1;";
  $db->SQL($query);
  $fixedDatum=$db->lookupRecord();
  
  
  $query="SELECT Beleggingscategorien.Beleggingscategorie,Beleggingscategorien.Beleggingscategorie,CategorienPerHoofdcategorie.Hoofdcategorie, Hcat.Omschrijving, Hcat.Afdrukvolgorde FROM
          Beleggingscategorien
          Inner Join CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie
          Inner Join Beleggingscategorien as Hcat ON CategorienPerHoofdcategorie.Hoofdcategorie = Hcat.Beleggingscategorie
          WHERE CategorienPerHoofdcategorie.Vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."' ";
	$db->SQL($query);
	$db->Query();
	while($data = $db->NextRecord())
	{
	  $hoofdVerdeling['Hoofdcategorie']['Omschrijving'][$data['Hoofdcategorie']]=$data['Omschrijving'];
	  $hoofdVerdeling['Hoofdcategorie']['Koppeling'][$data['Beleggingscategorie']]=$data['Hoofdcategorie'];
    $hoofdVerdeling['Hoofdcategorie']['Afdrukvolgorde'][$data['Hoofdcategorie']]=$data['Afdrukvolgorde'];
	}

	$query="SELECT Beleggingssectoren.Beleggingssector,SectorenPerHoofdsector.Hoofdsector, Hsec.Omschrijving, Hsec.Afdrukvolgorde FROM
          Beleggingssectoren
          Inner Join SectorenPerHoofdsector ON Beleggingssectoren.Beleggingssector= SectorenPerHoofdsector.Beleggingssector
          Inner Join Beleggingssectoren as Hsec ON SectorenPerHoofdsector.Hoofdsector  = Hsec.Beleggingssector
          WHERE SectorenPerHoofdsector.Vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."'";
	$db->SQL($query);
	$db->Query();
	while($data = $db->NextRecord())
	{
	  $hoofdVerdeling['Hoofdsector']['Omschrijving'][$data['Hoofdsector']]=$data['Omschrijving'];
	  $hoofdVerdeling['Hoofdsector']['Koppeling'][$data['Beleggingssector']]=$data['Hoofdsector'];
    $hoofdVerdeling['Hoofdsector']['Afdrukvolgorde'][$data['Hoofdsector']]=$data['Afdrukvolgorde'];
	}

  $query="SELECT Fonds,Percentage FROM ModelPortefeuilleFixed WHERE Portefeuille='$portefeuille' AND Datum='".$fixedDatum['FixedDatum']."' ";
  $db->SQL($query);
  $db->Query();
  $n=0;
  $totaalPercentage=0;
  $db2=new DB();
  while ($data=$db->nextRecord())
  {
    $totaalPercentage +=$data['Percentage'];
    $regels[$n]['actuelePortefeuilleWaardeEuro'] = $data['Percentage']*1000;
    $regels[$n]['fonds']=$data['Fonds'];

    if($data['Fonds'] == 'LIQ' || $data['Fonds'] == 'Liquiditeiten')
    {
       $regels[$n]['type'] = 'rekening';
       $regels[$n]['valuta'] = 'EUR';
       $regels[$n]['fondsOmschrijving']='Liquiditeiten';
       $regels[$n]['afmCategorie']='01liquiditeiten';
    }
    else
    {
      
      $query="SELECT FondsEenheid,Valuta,Omschrijving as fondsOmschrijving,Lossingsdatum FROM Fondsen WHERE Fonds = '".$data['Fonds']."'";
      $db2->SQL($query);
      $fondsData=$db2->lookupRecord();
  
      $query="SELECT Omschrijving FROM FondsOmschrijvingVanaf WHERE Fonds='".$data['Fonds']."' AND Vanaf <= '$rapportageDatum' ORDER BY Vanaf DESC LIMIT 1";
      $db2->SQL($query);
      $db2->Query();
      $fondsOmschrijving = $db2->NextRecord();
      if($fondsOmschrijving['Omschrijving'] <> '')
        $fondsData['fondsOmschrijving']=$fondsOmschrijving['Omschrijving'];
  
      $query="SELECT FondsRapportagenaam FROM FondsExtraInformatie WHERE Fonds='".$data['Fonds']."'";
      $db2->SQL($query);
      $db2->Query();
      $fondsOmschrijving = $db2->NextRecord();
      if($fondsOmschrijving['FondsRapportagenaam'] <> '')
        $fondsData['fondsOmschrijving']=$fondsOmschrijving['FondsRapportagenaam'];
      
      $velden=array('Beleggingssector'=>array('tabelFonds'=>'BeleggingssectorPerFonds','tabelVeld'=>'Beleggingssectoren'),
                    'AttributieCategorie'=>array('tabelFonds'=>'BeleggingssectorPerFonds','tabelVeld'=>'AttributieCategorien'),
                    'Regio'=>array('tabelFonds'=>'BeleggingssectorPerFonds','tabelVeld'=>'Regios'),
                    'Beleggingscategorie'=>array('tabelFonds'=>'BeleggingscategoriePerFonds','tabelVeld'=>'Beleggingscategorien'),
										'AfmCategorie'=>array('tabelFonds'=>'BeleggingscategoriePerFonds','tabelVeld'=>'afmCategorien'),
										'DuurzaamCategorie'=>array('tabelFonds'=>'BeleggingssectorPerFonds','tabelVeld'=>'DuurzaamCategorien'));
  
      foreach($velden as $veld=>$veldData)
      { 
        $query="SELECT ".$veldData['tabelFonds'].".".$veld.",
        ".$veldData['tabelVeld'].".Afdrukvolgorde as tweedeAfdrukvolgorde,
        KeuzePerVermogensbeheerder.Afdrukvolgorde as eersteAfdrukvolgorde,
        ".$veldData['tabelVeld'].".Omschrijving as ".$veld."Omschrijving
        FROM ".$veldData['tabelFonds']." 
        LEFT JOIN ".$veldData['tabelVeld']." ON ".$veldData['tabelFonds'].".".$veld." = ".$veldData['tabelVeld'].".".$veld."
        LEFT JOIN KeuzePerVermogensbeheerder ON ".$veldData['tabelFonds'].".".$veld." = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie='".$veldData['tabelVeld']."' AND KeuzePerVermogensbeheerder.vermogensbeheerder='".$fixedDatum['Vermogensbeheerder']."'
        WHERE ".$veldData['tabelFonds'].".Fonds = '".addslashes($data['Fonds'])."' AND ".$veldData['tabelFonds'].".Vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."' AND Vanaf <= '".$rapportageDatum."' ORDER BY ".$veldData['tabelFonds'].".Vanaf DESC LIMIT 1";
        $db2->SQL($query);
        $tmp=$db2->lookupRecord();
        $fondsData[$veld]=$tmp[$veld];
        $fondsData[$veld.'Omschrijving']=$tmp[$veld.'Omschrijving'];
        if($tmp['eersteAfdrukvolgorde'] <> 0)
          $fondsData[$veld.'Afdrukvolgorde']=$tmp['eersteAfdrukvolgorde'];
        else  
          $fondsData[$veld.'Afdrukvolgorde']=$tmp['tweedeAfdrukvolgorde'];
      }
      
      $query="SELECT
hoofdcategorieDetails.Beleggingscategorie,
hoofdcategorieDetails.Afdrukvolgorde AS tweedeAfdrukvolgorde,
KeuzePerVermogensbeheerder.Afdrukvolgorde AS eersteAfdrukvolgorde,
hoofdcategorieDetails.Omschrijving AS BeleggingscategorieOmschrijving
FROM
BeleggingscategoriePerFonds
LEFT JOIN Beleggingscategorien ON BeleggingscategoriePerFonds.Beleggingscategorie = Beleggingscategorien.Beleggingscategorie
LEFT JOIN CategorienPerHoofdcategorie ON Beleggingscategorien.Beleggingscategorie = CategorienPerHoofdcategorie.Beleggingscategorie AND CategorienPerHoofdcategorie.Vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."' 
LEFT JOIN Beleggingscategorien as hoofdcategorieDetails ON CategorienPerHoofdcategorie.Hoofdcategorie = hoofdcategorieDetails.Beleggingscategorie
LEFT JOIN KeuzePerVermogensbeheerder ON  hoofdcategorieDetails.Beleggingscategorie = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie = 'Beleggingscategorien' AND KeuzePerVermogensbeheerder.vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."' 
WHERE BeleggingscategoriePerFonds.Fonds = '".addslashes($data['Fonds'])."' AND BeleggingscategoriePerFonds.Vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."'  AND Vanaf <= '".$rapportageDatum."' 
ORDER BY BeleggingscategoriePerFonds.Vanaf DESC
LIMIT 1";
        $db2->SQL($query); 
        $tmp=$db2->lookupRecord();
        $fondsData['Hoofdcategorie']=$tmp['Beleggingscategorie'];
        $fondsData['HoofdcategorieOmschrijving']=$tmp['BeleggingscategorieOmschrijving'];
        if($tmp['eersteAfdrukvolgorde'] <> '')
          $fondsData['HoofdcategorieAfdrukvolgorde']=$tmp['eersteAfdrukvolgorde'];
        else
          $fondsData['HoofdcategorieAfdrukvolgorde']=$tmp['tweedeAfdrukvolgorde'];  

$query="SELECT
hoofdsectorDetails.Beleggingssector,
hoofdsectorDetails.Afdrukvolgorde AS tweedeAfdrukvolgorde,
KeuzePerVermogensbeheerder.Afdrukvolgorde AS eersteAfdrukvolgorde,
hoofdsectorDetails.Omschrijving AS sectorOmschrijving
FROM
BeleggingssectorPerFonds
LEFT JOIN Beleggingssectoren ON BeleggingssectorPerFonds.Beleggingssector = Beleggingssectoren.Beleggingssector
LEFT JOIN SectorenPerHoofdsector ON Beleggingssectoren.Beleggingssector = SectorenPerHoofdsector.Beleggingssector AND SectorenPerHoofdsector.Vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."' 
LEFT JOIN Beleggingssectoren as hoofdsectorDetails ON SectorenPerHoofdsector.Hoofdsector= hoofdsectorDetails.Beleggingssector
LEFT JOIN KeuzePerVermogensbeheerder ON hoofdsectorDetails.Beleggingssector = KeuzePerVermogensbeheerder.waarde AND KeuzePerVermogensbeheerder.categorie = 'Beleggingssectoren' AND KeuzePerVermogensbeheerder.vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."' 
WHERE BeleggingssectorPerFonds.Fonds = '".addslashes($data['Fonds'])."' AND BeleggingssectorPerFonds.Vermogensbeheerder = '".$fixedDatum['Vermogensbeheerder']."'  AND Vanaf <= '".$fixedDatum['Vermogensbeheerder']."'
ORDER BY BeleggingssectorPerFonds.Vanaf DESC
LIMIT 1";
        $db2->SQL($query); 
        $tmp=$db2->lookupRecord();
        $fondsData['Hoofdsector']=$tmp['Beleggingssector'];
        $fondsData['HoofdsectorOmschrijving']=$tmp['sectorOmschrijving'];
        if($tmp['eersteAfdrukvolgorde'] <> '')
          $fondsData['HoofdsectorAfdrukvolgorde']=$tmp['eersteAfdrukvolgorde'];
        else
          $fondsData['HoofdsectorAfdrukvolgorde']=$tmp['tweedeAfdrukvolgorde'];  

/*
      foreach($hoofdVerdeling as $veld=>$waarden)
      {
        if($veld=='Hoofdcategorie')
          $koppeling='Beleggingscategorie';
        else
          $koppeling='Beleggingssector';
        
        $fondsData[$veld]=$waarden['Koppeling'][$fondsData[$koppeling]];
        $fondsData[$veld.'Volgorde']=$waarden['Afdrukvolgorde'][$fondsData[$veld]];
        $fondsData[$veld.'Omschrijving']=$waarden['Omschrijving'][$fondsData[$veld]];
      }
    */  
      
      foreach($fondsData as $key=>$value)
      {
        $key=str_replace('Afdrukvolgorde','Volgorde',$key);
        $regels[$n][$key]=$value;
      } 
      //$regels[$n]['fondsOmschrijving']=$fondsData['Omschrijving'];
      $query="SELECT Koers,Datum FROM Fondskoersen WHERE Datum <= '$rapportageDatum' AND Fonds='".addslashes($data['Fonds'])."' ORDER BY Datum DESC limit 1 ";
  	  $db2->SQL($query);
  	  $fondsKoers=$db2->lookupRecord();
  	  if(!isset($fondsKoers['Koers']))
        $fondsKoers['koers']=0.001;
  	  $query="SELECT Koers FROM Valutakoersen WHERE Datum <= '$rapportageDatum' AND valuta='".$fondsData['Valuta']."' ORDER BY Datum DESC limit 1 ";
  	  $db2->SQL($query);
  	  $valutaKoers=$db2->lookupRecord();

  	  $regels[$n]['totaalAantal'] = $regels[$n]['actuelePortefeuilleWaardeEuro']/$fondsData['FondsEenheid']/$valutaKoers['Koers']/$fondsKoers['Koers'];
      $regels[$n]['type'] = 'fondsen';
      $regels[$n]['actueleFonds']=$fondsKoers['Koers'];
      $regels[$n]['actueleValuta']=$valutaKoers['Koers'];
      //$regels[$n]['FondsEenheid']=$fondsData['FondsEenheid'];
    }
    $n++;
  }

  return $regels;

}




function berekenPortefeuilleWaardeBewaarders($portefeuille, $rapportageDatum, $min1dag = false, $rapportageValuta = 'EUR',$rapportageBeginDatum)
{
  return berekenPortefeuilleWaarde($portefeuille, $rapportageDatum, $min1dag, $rapportageValuta,$rapportageBeginDatum,$afronding=2,true);
}
function berekenPortefeuilleWaardeQuick($portefeuille, $datum, $min1dag = false)
{
	$fondswaardenClean = array();
	$fondswaardenRente = array();
	$rekeningwaarden 	 = array();

	$jaar = date("Y",db2jul($datum));

	$extraquery = " Portefeuilles.Portefeuille = '".$portefeuille."' AND ";
	$q = "SELECT
	Rekeningmutaties.Fonds, Portefeuilles.Vermogensbeheerder
  FROM (Rekeningmutaties, Rekeningen, Portefeuilles)
	WHERE
	  Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
	" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".$extraquery.
	" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
	" Rekeningmutaties.Verwerkt = '1' AND ".
	" Rekeningmutaties.Boekdatum <= '".$datum."' AND ".
  " Rekeningmutaties.GrootboekRekening = 'FONDS' ".
	" GROUP BY Rekeningmutaties.Fonds ";

	$DB = new DB();
	$DB2 = new DB();
	$DB->SQL($q);
	$DB->Query();

	$records = $DB->records();

	while($fonds = $DB->NextRecord())
	{
    $query= "SELECT Beleggingssector FROM BeleggingssectorPerFonds,Portefeuilles  WHERE BeleggingssectorPerFonds.Fonds = '".$fonds['Fonds']."' AND
	  BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".$extraquery. " BeleggingssectorPerFonds.Vanaf <= '".$datum."'AND Portefeuilles.Portefeuille='$portefeuille' ORDER BY BeleggingssectorPerFonds.Vanaf DESC LIMIT 1";
	 	$DB2->SQL($query);
	  $DB2->Query();
	  $Beleggingssector= $DB2->NextRecord();
	  $fonds['Beleggingssector']=$Beleggingssector['Beleggingssector'];

    $query= "SELECT Beleggingscategorie FROM BeleggingscategoriePerFonds,Portefeuilles WHERE BeleggingscategoriePerFonds.Fonds = '".$fonds['Fonds']."' AND
    BeleggingscategoriePerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Vanaf <= '".$datum."'  AND Portefeuilles.Portefeuille='$portefeuille' ORDER BY BeleggingscategoriePerFonds.Vanaf DESC LIMIT 1";
	  $DB2->SQL($query);
	  $DB2->Query();
	  $Beleggingscategorie= $DB2->NextRecord();
	  $fonds['Beleggingscategorie']=$Beleggingscategorie['Beleggingscategorie'];

		$fondsen[] = $fonds;
		$vermogensbeheerder=$fonds['Vermogensbeheerder'];
	}

	if($vermogensbeheerder=='RCN')
    $geenRente=true;
  else
    $geenRente=false;


	for($a=0; $a < count($fondsen); $a++)
	{
		// berekening van Fonds Waarden in een aparte functie gezet
		$fondswaarden[$fondsen[$a]['Fonds']] = fondsAantalOpdatum($portefeuille, $fondsen[$a]['Fonds'], $datum);
	}

	// clean array
	for($a=0; $a <count($fondsen); $a++)
	{
		$fonds 	= $fondsen[$a];
		$data 	= $fondswaarden[$fonds['Fonds']];

		if(round($data['totaalAantal'],7) <> 0)
		{

			// bereken totalen met actuele koers
			$actuelePortefeuilleWaardeInValuta 	= ($data['fondsEenheid']  * $data['totaalAantal']) * $data['actueleFonds'];
			$actuelePortefeuilleWaardeEuro 			=  $data['actueleValuta'] * $actuelePortefeuilleWaardeInValuta;

			// maak nieuwe schone array
			$clean = $data;
			$clean[beginPortefeuilleWaardeInValuta] 	= round($beginPortefeuilleWaardeInValuta,2);
			$clean[beginPortefeuilleWaardeEuro] 			= round($beginPortefeuilleWaardeEuro,2);
			$clean[actuelePortefeuilleWaardeInValuta] = round($actuelePortefeuilleWaardeInValuta,2);
			$clean[actuelePortefeuilleWaardeEuro] 		= round($actuelePortefeuilleWaardeEuro,2);
			$clean[fonds] 														= $fonds['Fonds'];
			$clean[beleggingssector] 									= $fonds[Beleggingssector];
			$clean[beleggingscategorie] 							= $fonds[Beleggingscategorie];


			$fondswaardenClean[] = $clean;
		}
	}
	// bereken de rente
	$t = count($fondswaardenClean);
	for($a=0; $a <count($fondswaardenClean); $a++)
	{
		if($fondswaardenClean[$a]['renteBerekenen'] == 1 && $geenRente == false)
		{
			$rentebedrag = renteOverPeriode($fondswaardenClean[$a], $datum, $min1dag);
			$fondswaardenRente[$t] = $fondswaardenClean[$a];
			$fondswaardenRente[$t]['type'] = "rente";
			$fondswaardenRente[$t][actuelePortefeuilleWaardeInValuta] = round($rentebedrag,2);
			$fondswaardenRente[$t][actuelePortefeuilleWaardeEuro] = round($fondswaardenClean[$a]['actueleValuta'] * $rentebedrag,2);
			$t++;
		}
	}

	// merge rente array met fondsen array
	$fondswaardenClean = array_merge($fondswaardenClean, $fondswaardenRente);
	// haal actuele stand rekening op.
  $_beginJaar = substr($datum,0,4)."-01-01";
/*
  $query = "SELECT Rekeningen.Valuta, SUM(Rekeningmutaties.Bedrag) as totaal, ".
  	       " Rekeningen.RenteBerekenen, ".
	         " Rekeningen.Rente30_360 ".
						" FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
					  " WHERE ".
						" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
						" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".$extraquery.
						" Rekeningen.Memoriaal < 1 AND ".
						" Rekeningmutaties.boekdatum >= '".$_beginJaar."' AND ".
						" Rekeningmutaties.boekdatum <= '".$datum."' ".
            " GROUP BY Rekeningen.Valuta ".
            " ORDER BY Rekeningen.Valuta";
*/
 	$query = "SELECT DISTINCT(Rekeningafschriften.Rekening), ".
	" Rekeningen.Valuta, ".
	" Rekeningen.RenteBerekenen, ".
	" Rekeningen.Rente30_360, ".
	" Rekeningen.Tenaamstelling ".
	" FROM (Rekeningafschriften)".
	" LEFT JOIN Rekeningen ON Rekeningafschriften.Rekening = Rekeningen.Rekening ".
	" WHERE ".
	" Rekeningen.Memoriaal <> '1' AND ".
	" Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	" YEAR(Rekeningafschriften.Datum) = '".$_beginJaar."' ";

	$DB1 = new DB();
	$DB1->SQL($query);
	$DB1->Query();

	$t = count($fondswaardenClean);
	$u = 0;

	if($data['Tenaamstelling'] )
	{
	 $rekeningType = $data['Tenaamstelling'];
	}
	else
	{
		$rekeningType = "Effectenrekening ";
	}

	while($data = $DB1->NextRecord())
	{

	  $_beginJaar = substr($datum,0,4)."-01-01";
    $subquery = "SELECT SUM(Bedrag) as totaal FROM Rekeningmutaties WHERE boekdatum >= '".$_beginJaar."' AND boekdatum <= '".$datum."' AND ".
                "Rekening = '".$data[Rekening]."' Group By Rekeningmutaties.Rekening";

    $DB2 = new DB();
		$DB2->SQL($subquery);
		$DB2->Query();
		$subdata = $DB2->nextRecord();


		if(round($subdata[totaal],2)	<> 0)
		{
			$q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$data['Valuta']."' AND Datum <= '".$datum."' ORDER BY Datum DESC LIMIT 1";

			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$actuelevaluta = $DB2->NextRecord();
			//
			if ($data['RenteBerekenen'] == 1)
		  {
      $rente = depositoRenteOverPeriode($data['Rekening'],$datum,$subdata['totaal'],$data['Rente30_360']);
      $depositoWaarden[$u]['type'] = 'rente';
      $depositoWaarden[$u]['fondsOmschrijving'] = $rekeningType." ".$data['Rekening'] ;
			$depositoWaarden[$u]['rekening'] = $data['Rekening'];
			$depositoWaarden[$u]['valuta'] = $data['Valuta'];
			$depositoWaarden[$u]['historischeValutakoers'] = $data['Valuta'];
			$depositoWaarden[$u]['actueleValuta'] = $actuelevaluta['Koers'];
			$depositoWaarden[$u]['actuelePortefeuilleWaardeInValuta'] = round($rente,2);
			$depositoWaarden[$u]['actuelePortefeuilleWaardeEuro'] = round($depositoWaarden[$u]['actueleValuta'] * $rente,2);
			$u++;
		  }
		  //

			$rekeningwaarden[$t]['type'] 							= "rekening";
			$rekeningwaarden[$t]['fondsOmschrijving'] = $data['Valuta'];
			$rekeningwaarden[$t][rekening] 					= $data['Rekening'];
			$rekeningwaarden[$t]['valuta'] 						= $data['Valuta'];
			$rekeningwaarden[$t]['actueleValuta'] 		= $actuelevaluta['Koers'];

			// get actuele valuta
			$rekeningwaarden[$t][actuelePortefeuilleWaardeInValuta] = round($subdata[totaal],2);
			$rekeningwaarden[$t][actuelePortefeuilleWaardeEuro] 		= round($rekeningwaarden[$t]['actueleValuta'] * $subdata[totaal],2);
			$t++;
		}
	}
	// merge rekeningen array
	$fondswaardenClean = array_merge($fondswaardenClean, $rekeningwaarden);

	foreach ($depositoWaarden as $deposito)
	{
	$fondswaardenClean[] = $deposito;
	}

	return $fondswaardenClean;
}

function backupTijdelijkeTabel($portefeuille, $datum)
{
	global $USR,$sessionId;
	$DB = new DB();
	$query = "SELECT * FROM TijdelijkeRapportage WHERE portefeuille = '".$portefeuille."' AND rapportageDatum = '".substr($datum,0,10)."' AND add_user = '$USR' AND sessionId='$sessionId'";
	$DB->SQL($query);
	$DB->Query();
	$regels=array();
	$unsetVelden=array('rapportageDatum','portefeuille','add_date','add_user','sessionId');
	while($data=$DB->nextRecord())
	{
		foreach($unsetVelden as $veld)
			unset($data[$veld]);
		$regels[]=$data;
	}
	return $regels;
}

function vulTijdelijkeTabel($data, $portefeuille, $datum)
{
	global $USR,$sessionId;
	$DB = new DB();
	// eerst tabel opschonen
	$query = "DELETE FROM TijdelijkeRapportage WHERE portefeuille = '".$portefeuille."' AND rapportageDatum = '".substr($datum,0,10)."' AND add_user = '$USR' AND sessionId='$sessionId'";
	debugSpecial($query,__FILE__,__LINE__);

	$DB->SQL($query);
	$DB->Query();

	for($x=0; $x < count($data); $x++)
	{
		$fields = array_keys($data[$x]);

		$query = "INSERT INTO TijdelijkeRapportage SET ";

	  for ($a = 0; $a < count($fields); $a++)
    {
      $fieldName = $fields[$a];

			if($fieldName <> 'voorgaandejarenActief')
			{
      	if ($a > 0)
	        $query .= ", ";

					$query .= " ".$fieldName." = '".mysql_escape_string($data[$x][$fieldName])."'";
			}
		}

		$query .= ", rapportageDatum='".$datum."', portefeuille='".$portefeuille."',add_date=NOW(),add_user='$USR',sessionId='$sessionId'";

		$DB->SQL($query);
		if(!$DB->Query())
			$_error = true;
	}
	if(!empty($_error))
		return false;
	return true;
}

function verwijderTijdelijkeTabel($portefeuille, $datum=false)
{
	global $USR,$sessionId;
	$DB = new DB();
	if($datum <> false)
	  $datumQuery="AND rapportageDatum = '".$datum."'";

	$query = "DELETE FROM TijdelijkeRapportage WHERE portefeuille = '".$portefeuille."' AND add_user = '".$USR."' AND sessionId='$sessionId' $datumQuery"; //
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	return $DB->Query();
}

function updateBeginJaarTijdelijkeTabel($portefeuille,$startDatum,$rapportageDatum)
{
  $db = new DB();
  $db2 = new DB();
  $query = "SELECT * FROM TijdelijkeRapportage WHERE Portefeuille = '$portefeuille' AND rapportageDatum = '$startDatum' AND type <> 'fondsen' ";
  $db->SQL($query);
	$db->Query();
	while ($data = $db->nextRecord())
	{
	  $query = "UPDATE
	              TijdelijkeRapportage
	            SET
	              beginwaardeLopendeJaar          = '".$data['actueleFonds']."' ,
                beginPortefeuilleWaardeInValuta = '".$data['actuelePortefeuilleWaardeInValuta']."' ,
                beginPortefeuilleWaardeEuro     = '".$data['actuelePortefeuilleWaardeEuro']."'
              WHERE
                Portefeuille    = '$portefeuille' AND
                rapportageDatum = '$rapportageDatum' AND
                fonds           = '".$data['fonds']."' AND
                 type           = '".$data['type']."' AND
                rekening        = '".$data['rekening']."'
	           ";
//echo "$query \n<br>\n"	 ;
  $db2->SQL($query);
	$db2->Query();
	}

}


function berekenHistorischKostprijs($portefeuille, $fonds, $datum, $valuta = 'EUR',$rapportageBeginDatum,$id)
{
	//alias voor fondsWaardeOpdatum.
	$_tmp = fondsWaardeOpdatum($portefeuille, $fonds, $datum, $valuta,$rapportageBeginDatum,$id);//
	return $_tmp;
}


function bepaalHuisfondsKoers($fonds,$huisfondsPortefeuille,$datum)
{
	if(substr($datum,5,5)=='01-01')
		$min1dag=true;
	else
		$min1dag=false;
	$jaar=substr($datum,0,4);
	$beginDatum=$jaar.'-01-01';
 $waarde=	berekenportefeuilleWaardeQuick($huisfondsPortefeuille, $datum,$min1dag,'EUR',$beginDatum);
	$totaleWaarde=0.0;
 foreach($waarde as $regel)
   $totaleWaarde+=$regel['actuelePortefeuilleWaardeEuro'];

	$qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal FROM Rekeningmutaties
	  WHERE  Rekeningmutaties.Fonds = '$fonds' AND ".
		" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
		" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
		" Rekeningmutaties.Verwerkt = '1' AND ".
		" Rekeningmutaties.Boekdatum <= '".$datum."' ".
		" GROUP BY Rekeningmutaties.Fonds ";
	$DB=new DB();
	$DB->SQL($qMutaties);
	$DB->Query();
	$aantal=$DB->lookupRecord();
	$data=array('Koers'=>$totaleWaarde/$aantal['totaalAantal'],'Datum'=>$datum,'aantal'=>$aantal['totaalAantal']);

	//echo "<br>\n$fonds | $datum <br>\n";
	//echo "$fonds| $huisfondsPortefeuille, $datum,$min1dag,'EUR',$beginDatum | $totaleWaarde | ".$data['Koers']." <br>\n";

	return $data;

}

function bepaalHuidfondsenVerdeling($portefeuille,$rapportagedatum,$startdatum='',$valuta='EUR')
{
	if(substr($rapportagedatum,5,5)=='01-01')
		$mindag=true;
	else
		$mindag=false;
	if($startdatum=='')
		$startdatum=substr($rapportagedatum,0,5).'01-01';

	$fondswaarden = berekenPortefeuilleWaarde($portefeuille,$rapportagedatum,$mindag,$valuta,$startdatum);
  $correctieVelden=array('totaalAantal','actuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
	$DB=new DB();
	$nieuweRegels=array();
	$waardeViaHuisfonds=false;
  foreach($fondswaarden as $regel)
  {
		$hoofdKey='|'.$regel['type'].'|'.$regel['fonds'].'|'.$regel['rekening'].'|';
		if($regel['type']=='fondsen')
		{
			$query = "SELECT Fondsen.Portefeuille FROM Fondsen WHERE Fondsen.Fonds='" . $regel['fonds'] . "' AND Fondsen.Huisfonds=1 AND Fondsen.Portefeuille<>''";
			if ($DB->QRecords($query) == 1)
			{
				$waardeViaHuisfonds=true;
				$huisfonds = $DB->nextRecord();

				$waarden = bepaalHuisfondsResultaat($regel['fonds'], $portefeuille, $huisfonds['Portefeuille'], $startdatum, $rapportagedatum);
				$huisfondsregels = $waarden['regels']['eind'];

				foreach ($huisfondsregels as $index => $huisfondswaarden)
				{
					foreach ($correctieVelden as $veld)
						$huisfondswaarden[$veld] = $huisfondswaarden[$veld] * $waarden['aandeel'];

					$key='|'.$huisfondswaarden['type'].'|'.$huisfondswaarden['fonds'].'|'.$huisfondswaarden['rekening'].'|';
					if(!isset($nieuweRegels[$key]))
						$nieuweRegels[$key]=$huisfondswaarden;
					else
						foreach($correctieVelden as $veld)
							$nieuweRegels[$key][$veld] += $fondswaarden[$veld];
				}
			}
			else
				$waardeViaHuisfonds=false;
		}
		if($waardeViaHuisfonds==false)
		{
			if(!isset($nieuweRegels[$hoofdKey]))
				$nieuweRegels[$hoofdKey]=$regel;
			else
				foreach($correctieVelden as $veld)
					$nieuweRegels[$hoofdKey][$veld] += $regel[$veld];
		}
  }

	$nieuweRegelsClean  = array_values($nieuweRegels);
	return $nieuweRegelsClean;
}

function bepaalHuisfondsAandeel($fonds,$portefeuille,$datum)
{
	$jaar=substr($datum,0,4);
	$DB=new DB();
	$qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal FROM Rekeningmutaties JOIN Rekeningen on Rekeningmutaties.Rekening=Rekeningen.Rekening
	  WHERE  Rekeningmutaties.Fonds = '$fonds' AND ".
		" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
		" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
		" Rekeningmutaties.Verwerkt = '1' AND ".
		" Rekeningmutaties.Boekdatum <= '".$datum."' AND Rekeningen.Portefeuille='$portefeuille' ".
		" GROUP BY Rekeningmutaties.Fonds ";
	$DB->SQL($qMutaties);
	$DB->Query();
	$aantalPortefeuille=$DB->lookupRecord();

	$qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal FROM Rekeningmutaties
	  WHERE  Rekeningmutaties.Fonds = '$fonds' AND ".
		" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
		" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
		" Rekeningmutaties.Verwerkt = '1' AND ".
		" Rekeningmutaties.Boekdatum <= '".$datum."' GROUP BY Rekeningmutaties.Fonds ";
	$DB->SQL($qMutaties);
	$DB->Query();
	$aantal=$DB->lookupRecord();
	$aandeel=$aantalPortefeuille['totaalAantal']/$aantal['totaalAantal'];
  return $aandeel;
}

function bepaalHuisfondsResultaat($fonds,$portefeuille,$huisfondsPortefeuille,$datumVanaf,$datumTot)
{
	$valuta='EUR';
	if(substr($datumVanaf,5,5)=='01-01')
		$min1dag=true;
	else
		$min1dag=false;
	$jaar=substr($datumVanaf,0,4);
	$beginDatum=$jaar.'-01-01';
	$waarde['begin']=	berekenportefeuilleWaarde($huisfondsPortefeuille, $datumVanaf,$min1dag,$valuta,$beginDatum);
	$totaleWaardeBegin=0.0;
	$totaleWaardeFonds=array();
	$fondsVerdeling=array();
	foreach($waarde['begin'] as $regel)
	{
		$totaleWaardeBegin += $regel['actuelePortefeuilleWaardeEuro'];
		if($regel['type']=='rente' && $regel['fonds'] != '')
		{
			$totaleWaardeFonds['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
		}
	}
	if(substr($datumTot,5,5)=='01-01')
		$min1dag=true;
	else
		$min1dag=false;
	$waarde['eind']=	berekenportefeuilleWaarde($huisfondsPortefeuille, $datumTot,$min1dag,$valuta,$datumVanaf);
	$totaleWaardeEind=0.0;
	$verdeling=array();

	$verdelingPer=array('hoofdcategorie','beleggingscategorie','Regio','beleggingssector');
	foreach($verdelingPer as $veld)
  	$verdeling[$veld]=array();
	foreach($waarde['eind'] as $regel)
	{
		if($regel['type']=='fondsen')
		{
			$totaleWaardeFonds['begin'] += $regel['beginPortefeuilleWaardeEuro'];
			$totaleWaardeFonds['eind']  += $regel['actuelePortefeuilleWaardeEuro'];
		}
		elseif($regel['type']=='rente' && $regel['fonds'] != '')
		{
			$totaleWaardeFonds['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
		}

		$totaleWaardeEind += $regel['actuelePortefeuilleWaardeEuro'];

		foreach($verdelingPer as $veld)
			$verdeling[$veld][$regel[$veld]]+=$regel['actuelePortefeuilleWaardeEuro'];


	}


	$waardeMutatie = $totaleWaardeEind - $totaleWaardeBegin;
	$stortingen = getStortingen($huisfondsPortefeuille,$datumVanaf, $datumTot,$valuta);
	$onttrekkingen = getOnttrekkingen($huisfondsPortefeuille,$datumVanaf, $datumTot,$valuta);

	$db=new DB();
	$query = "SELECT ".
		"SUM(((TO_DAYS('".$datumTot."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
		"  / (TO_DAYS('".$datumTot."') - TO_DAYS('".$datumVanaf."')) ".
		"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS gewogen, ".
		"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal ".
		"FROM  (Rekeningen, Portefeuilles,Grootboekrekeningen )
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
		"WHERE ".
		"Rekeningen.Portefeuille = '".$huisfondsPortefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$datumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$datumTot."' AND
	Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND (Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$db->SQL($query);
	$db->Query();
	$stortingenEnOnttrekkingen = $db->NextRecord();


	$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;

	$gerealiseerd=gerealiseerdKoersresultaat($huisfondsPortefeuille,$datumVanaf,$datumTot,$valuta,true);
	$ongerealiseerd=$totaleWaardeFonds['eind']-$totaleWaardeFonds['begin'];
	$opgelopenRente=$totaleWaardeFonds['renteEind']-$totaleWaardeFonds['renteBegin'];
	$koersresultaat=$gerealiseerd+$ongerealiseerd;//+$opgelopenRente;
	//$directeopbrengst=$resultaatVerslagperiode-$koersresultaat;
	$aandeel=bepaalHuisfondsAandeel($fonds,$portefeuille,$datumTot);

	$db=new DB();
	$query = "SELECT SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers )-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers )  AS totaalkosten,Grootboekrekeningen.GrootboekRekening
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$huisfondsPortefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$datumVanaf' AND Rekeningmutaties.Boekdatum <= '$datumTot' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.GrootboekRekening ";
	$db->SQL($query);
	$db->query();
	$kostenPerGrootboek=array();
	$directekosten=0;
	while($kosten=$db->nextRecord())
	{
		$directekosten+=$kosten['totaalkosten'];
		$kostenPerGrootboek[$kosten['GrootboekRekening']]+=$kosten['totaalkosten']*$aandeel;
	}


	$query = "SELECT  SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers )-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers ) AS totaalOpbrengsten,Grootboekrekeningen.GrootboekRekening
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$huisfondsPortefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$datumVanaf' AND Rekeningmutaties.Boekdatum <= '$datumTot' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.GrootboekRekening ";
	$db->SQL($query);
	$db->query();
	$opbrengstenPerGrootboek=array();
	$directeopbrengst=0;
	while($opbrengsten=$db->nextRecord())
	{
		$directeopbrengst+=$opbrengsten['totaalOpbrengsten'];
		$opbrengstenPerGrootboek[$opbrengsten['GrootboekRekening']]+=$opbrengsten['totaalOpbrengsten']*$aandeel;
	}

/*
	$qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal FROM Rekeningmutaties JOIN Rekeningen on Rekeningmutaties.Rekening=Rekeningen.Rekening
	  WHERE  Rekeningmutaties.Fonds = '$fonds' AND ".
		" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
		" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
		" Rekeningmutaties.Verwerkt = '1' AND ".
		" Rekeningmutaties.Boekdatum <= '".$datumTot."' AND Rekeningen.Portefeuille='$portefeuille' ".
		" GROUP BY Rekeningmutaties.Fonds ";
	$DB=new DB();
	$DB->SQL($qMutaties);
	$DB->Query();
	$aantalPortefeuille=$DB->lookupRecord();

	$qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal FROM Rekeningmutaties
	  WHERE  Rekeningmutaties.Fonds = '$fonds' AND ".
		" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
		" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
		" Rekeningmutaties.Verwerkt = '1' AND ".
		" Rekeningmutaties.Boekdatum <= '".$datumTot."' GROUP BY Rekeningmutaties.Fonds ";
	$DB=new DB();
	$DB->SQL($qMutaties);
	$DB->Query();
	$aantal=$DB->lookupRecord();
	$aandeel=$aantalPortefeuille['totaalAantal']/$aantal['totaalAantal'];
*/


	foreach($verdeling as $type=>$waarden)
		  foreach($waarden as $categorie=>$waardeEur)
			   $verdeling[$type][$categorie]=$waardeEur*$aandeel;



	$data=array('fonds'=>$fonds,'aandeel'=>$aandeel,
							'datum'=>$datumTot,'huisfondsWaarde'=>$waarde['eind'],
							'koersresultaat'=>$koersresultaat,'directekosten'=>$directekosten,'directeopbrengst'=>$directeopbrengst,'resultaat'=>$resultaatVerslagperiode,
							'gerealiseerd'=>$gerealiseerd,'ongerealiseerd'=>$ongerealiseerd,'opgelopenRente'=>$opgelopenRente,'stortingen'=>$stortingen,'onttrekkingen'=>$onttrekkingen,
		          'stortingenEnOnttrekkingenGewogen'=>$stortingenEnOnttrekkingen['gewogen'],'aandeelkostenPerGrootboek'=>$kostenPerGrootboek,'opbrengstenPerGrootboek'=>$opbrengstenPerGrootboek,
							'aandeelKoersresultaat'=>$koersresultaat*$aandeel,'aandeeldirectekosten'=>$directekosten*$aandeel,'aandeelDirecteopbrengst'=>$directeopbrengst*$aandeel,
							'aandeelResultaat'=>$resultaatVerslagperiode*$aandeel,'aandeelStortingen'=>$stortingen*$aandeel,'aandeelOnttrekkingen'=>$onttrekkingen*$aandeel,
							'aandeelStortingenEnOnttrekkingenGewogen'=>$stortingenEnOnttrekkingen['gewogen']*$aandeel,'aandeelOpgelopenRente'=>$opgelopenRente*$aandeel,
		          'verdeling'=>$verdeling,'regels'=>$waarde);

//listarray($data);
	return $data;
}

/*
	functie 			: fondsWaardeOpdatum($portefeuille, $fonds, $rapportageDatum)
	omschrijving  : aparte functie om fonds status uit te lezen op een bepaalde datum

	voorbeeld output:

    [Altair Risin 100] => Array
        (
            ['type'] => fondsen
            ['totaalAantal'] => 59
            [historischeWaarde] => 1.5531
            [historischeValutakoers] => 0.77214115
            [beginwaardeLopendeJaar] => 1.5531
            ['fondsEenheid'] => 1
            [beginwaardeValutaLopendeJaar] => 0.77214115
            ['renteBerekenen'] => 0
            ['fondsOmschrijving'] => Altair Rising Star 1/100
            ['valuta'] => USD
            ['eersteRentedatum'] =>
            ['rentedatum'] => 2004-01-01 00:00:00
            ['renteperiode'] => 12
            ['actueleValuta'] => 0.82644628
            ['actueleFonds'] => 1.480309
        )
*/

function fondsAantalOpdatum($portefeuille, $fonds, $rapportageDatum,$portefeuilleList='')
{
  global $USR;
	$a = 1;
	$fondsen[$a]['Fonds'] = $fonds;
	$jaar = date("Y", db2jul($rapportageDatum));

	$DB = new DB();
	$q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$fondsen[$a]['Fonds']."' AND Datum <= '".$rapportageDatum."' LIMIT 1";
	$DB->SQL($q);
	$DB->Query();
	$rente = "";
	if($DB->records() > 0)
	{
		$rente = $DB->NextRecord();
		$renteBerekenen = 1;
	}
	else
	{
		$renteBerekenen = 0;
	}

	// als portefeuille een array is maak de mutatie selectie groter!
	if(is_array($portefeuilleList))
	{
	  $extraquery=" Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') AND ";
	}
	elseif(is_array($portefeuille))
	{ 
		// controle op einddatum portefeuille
		$extraquery  = " Portefeuilles.Einddatum > '".jul2db($portefeuille['datumTm'])."' AND";

		if($portefeuille['portefeuilleTm'])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$portefeuille['portefeuilleVan']."' AND Portefeuilles.Portefeuille <= '".$portefeuille['portefeuilleTm']."') AND";
		if($portefeuille['vermogensbeheerderTm'])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$portefeuille['vermogensbeheerderVan']."' AND Portefeuilles.Vermogensbeheerder <= '".$portefeuille['vermogensbeheerderTm']."') AND ";
		if($portefeuille['accountmanagerTm'])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$portefeuille['accountmanagerVan']."' AND Portefeuilles.Accountmanager <= '".$portefeuille['accountmanagerTm']."') AND ";
// Toevoeging 02-10-06 om fondsselectie compleet te maken.
		if($portefeuille['depotbankTm'])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$portefeuille['depotbankVan']."' AND Portefeuilles.Depotbank <= '".$portefeuille['depotbankTm']."') AND ";
		if($portefeuille['AFMprofielTm'])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$portefeuille['AFMprofielVan']."' AND Portefeuilles.AFMprofiel <= '".$portefeuille['AFMprofielTm']."') AND ";
		if($portefeuille['RisicoklasseTm'])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$portefeuille['RisicoklasseVan']."' AND Portefeuilles.Risicoklasse <= '".$portefeuille['RisicoklasseTm']."') AND ";
		if($portefeuille['SoortOvereenkomstTm'])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$portefeuille['SoortOvereenkomstVan']."' AND Portefeuilles.SoortOvereenkomst <= '".$portefeuille['SoortOvereenkomstTm']."') AND ";
		if($portefeuille['RemisierTm'])
			$extraquery .= " (Portefeuilles.Remisier >= '".$portefeuille['RemisierVan']."' AND Portefeuilles.Remisier <= '".$portefeuille['RemisierTm']."') AND ";
		if($portefeuille['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$portefeuille['clientVan']."' AND Portefeuilles.Client <= '".$portefeuille['clientTm']."') AND";
		if (count($portefeuille['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$portefeuille['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}
  }
	else {
		$extraquery  = " Portefeuilles.Portefeuille = '".$portefeuille."' AND 	";
	}

	if(checkAccess($type))
	{
	  $join = "";
		$beperktToegankelijk = "";
	}
	else
	{
		$join = "INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	           JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";
	  $beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
	}

	$qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal, ".
	" Fondsen.Renteperiode, ".
	" Fondsen.EersteRentedatum, ".
	" Fondsen.Rentedatum, ".
	" Fondsen.Fondseenheid, ".
	" Fondsen.Valuta, ".
	" Fondsen.Fonds, ".
	" Fondsen.EindDatum, ".
	" Fondsen.Omschrijving AS FondsOmschrijving, Fondsen.Huisfonds,Fondsen.Portefeuille as huisfondsPortefeuille".
	" FROM Rekeningmutaties, ".
	" Rekeningen, Fondsen, Portefeuilles
	  $join
	  WHERE ".$extraquery.
	" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
	" Fondsen.Fonds = Rekeningmutaties.Fonds AND ".
	" Rekeningmutaties.Fonds = '".$fondsen[$a]['Fonds']."' AND ".
	" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
	" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
	" Rekeningmutaties.Verwerkt = '1' AND ".
	" Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' ".$beperktToegankelijk.
	" GROUP BY Rekeningmutaties.Fonds ";

	$DB->SQL($qMutaties);
	$DB->Query();

	$fondswaarden[$fondsen[$a]['Fonds']]['type'] = "fondsen";
	$fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = 0;
	$fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] = 1;
	$fondswaarden[$fondsen[$a]['Fonds']]['renteBerekenen'] = $renteBerekenen;

	$DB2 = new DB();
	while($mutatie = $DB->NextRecord())
	{
		// haal actuele valuta koers op!
		if(empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta']))
		{
			$actuelevaluta = array();
			$q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$mutatie['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$actuelevaluta = $DB2->NextRecord();
		}

		// haal actuele fonds koers op!
		if(empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds']))
		{
			$actuelefonds = array();
			$q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$mutatie['Fonds']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1";
			$DB2->SQL($q);
			$DB2->Query();
			$actuelefonds = $DB2->NextRecord();
			if($actuelefonds['Koers']=='' && $mutatie['Huisfonds']==1 &&  $mutatie['huisfondsPortefeuille']<>'')
				$actuelefonds=bepaalHuisfondsKoers($mutatie['Fonds'],$mutatie['huisfondsPortefeuille'],$rapportageDatum);
		}

		$fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] 			= $mutatie['totaalAantal'];
		$fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] 			= $mutatie['Fondseenheid'];
		$fondswaarden[$fondsen[$a]['Fonds']]['fondsOmschrijving'] = $mutatie['FondsOmschrijving'];
		$fondswaarden[$fondsen[$a]['Fonds']]['valuta'] 						= $mutatie['Valuta'];
		$fondswaarden[$fondsen[$a]['Fonds']]['eersteRentedatum'] 	= $mutatie['EersteRentedatum'];

		$fondswaarden[$fondsen[$a]['Fonds']]['rentedatum'] 				= $mutatie['Rentedatum'];
		$fondswaarden[$fondsen[$a]['Fonds']]['renteperiode'] 			= $mutatie['Renteperiode'];

		$fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] 		= $actuelevaluta['Koers'];
		$fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds'] 			= $actuelefonds['Koers'];
		$fondswaarden[$fondsen[$a]['Fonds']]['EindDatum'] 			  = $mutatie['EindDatum'];
	} 
	return $fondswaarden[$fondsen[$a]['Fonds']];
}

function optieAantalOpdatum($portefeuille, $fonds, $rapportageDatum,$portefeuilleList='')
{
	$a = 1;
	$fondsen[$a]['Fonds'] = $fonds;
	$jaar = date("Y", db2jul($rapportageDatum));

	$DB = new DB();
	$q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Fonds = '".$fondsen[$a]['Fonds']."' AND Datum <= '".$rapportageDatum."' LIMIT 1";
	$DB->SQL($q);
	$DB->Query();
	$rente = "";
	if($DB->records() > 0)
	{
		$rente = $DB->NextRecord();
		$renteBerekenen = 1;
	}
	else
	{
		$renteBerekenen = 0;
	}

	// als portefeuille een array is maak de mutatie selectie groter!
	if(is_array($portefeuilleList))
	{
	  $extraquery=" Portefeuilles.Portefeuille IN('".implode("','",$portefeuilleList)."') AND ";
	}
	elseif(is_array($portefeuille))
	{
		// controle op einddatum portefeuille
		$extraquery  = " Portefeuilles.Einddatum > '".jul2db($portefeuille['datumTm'])."' AND";

		if($portefeuille['portefeuilleTm'])
			$extraquery .= " (Portefeuilles.Portefeuille >= '".$portefeuille['portefeuilleVan']."' AND Portefeuilles.Portefeuille <= '".$portefeuille['portefeuilleTm']."') AND";
		if($portefeuille['vermogensbeheerderTm'])
			$extraquery .= " (Portefeuilles.Vermogensbeheerder >= '".$portefeuille['vermogensbeheerderVan']."' AND Portefeuilles.Vermogensbeheerder <= '".$portefeuille['vermogensbeheerderTm']."') AND ";
		if($portefeuille['accountmanagerTm'])
			$extraquery .= " (Portefeuilles.Accountmanager >= '".$portefeuille['accountmanagerVan']."' AND Portefeuilles.Accountmanager <= '".$portefeuille['accountmanagerTm']."') AND ";
// Toevoeging 02-10-06 om fondsselectie compleet te maken.
		if($portefeuille['depotbankTm'])
			$extraquery .= " (Portefeuilles.Depotbank >= '".$portefeuille['depotbankVan']."' AND Portefeuilles.Depotbank <= '".$portefeuille['depotbankTm']."') AND ";
		if($portefeuille['AFMprofielTm'])
			$extraquery .= " (Portefeuilles.AFMprofiel >= '".$portefeuille['AFMprofielVan']."' AND Portefeuilles.AFMprofiel <= '".$portefeuille['AFMprofielTm']."') AND ";
		if($portefeuille['RisicoklasseTm'])
			$extraquery .= " (Portefeuilles.Risicoklasse >= '".$portefeuille['RisicoklasseVan']."' AND Portefeuilles.Risicoklasse <= '".$portefeuille['RisicoklasseTm']."') AND ";
		if($portefeuille['SoortOvereenkomstTm'])
			$extraquery .= " (Portefeuilles.SoortOvereenkomst >= '".$portefeuille['SoortOvereenkomstVan']."' AND Portefeuilles.SoortOvereenkomst <= '".$portefeuille['SoortOvereenkomstTm']."') AND ";
		if($portefeuille['RemisierTm'])
			$extraquery .= " (Portefeuilles.Remisier >= '".$portefeuille['RemisierVan']."' AND Portefeuilles.Remisier <= '".$portefeuille['RemisierTm']."') AND ";
		if($portefeuille['clientTm'])
		  $extraquery .= " (Portefeuilles.Client >= '".$portefeuille['clientVan']."' AND Portefeuilles.Client <= '".$portefeuille['clientTm']."') AND ";
		if (count($portefeuille['selectedPortefeuilles']) > 0)
		{
		 $portefeuilleSelectie = implode('\',\'',$portefeuille['selectedPortefeuilles']);
	   $extraquery .= " Portefeuilles.Portefeuille IN('$portefeuilleSelectie') AND ";
		}
	}
	else {
		$extraquery  = " Portefeuilles.Portefeuille = '".$portefeuille."' AND 	";
	}

	$qMutaties = "SELECT SUM(Rekeningmutaties.Aantal) AS totaalAantal, ".
	" Fondsen.Renteperiode, ".
	" Fondsen.EersteRentedatum, ".
	" Fondsen.Rentedatum, ".
	" Fondsen.Fondseenheid, ".
	" Fondsen.Valuta, ".
	" Fondsen.Fonds, ".
	" Fondsen.OptieType, ".
	" Fondsen.optieUitoefenPrijs, ".
	" Fondsen.OptieExpDatum, ".
	" Fondsen.Omschrijving AS FondsOmschrijving ".
	" FROM Rekeningmutaties, ".
	" Rekeningen, Fondsen, Portefeuilles WHERE ".$extraquery.
	" Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	" Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
	" Fondsen.Fonds = Rekeningmutaties.Fonds AND ".
	" Rekeningmutaties.Fonds = '".$fondsen[$a]['Fonds']."' AND ".
	" Rekeningmutaties.GrootboekRekening = 'FONDS' AND".
	" YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND ".
	" Rekeningmutaties.Verwerkt = '1' AND ".
	" Rekeningmutaties.Boekdatum <= '".$rapportageDatum."' ".
	" GROUP BY Rekeningmutaties.Fonds ";

	$DB->SQL($qMutaties);
	$DB->Query();

	$fondswaarden[$fondsen[$a]['Fonds']]['type'] = "fondsen";
	$fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] = 0;
	$fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] = 1;
	$fondswaarden[$fondsen[$a]['Fonds']]['renteBerekenen'] = $renteBerekenen;

	while($mutatie = $DB->NextRecord())
	{
		// haal actuele valuta koers op!
		if(empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta']))
		{
			$actuelevaluta = array();
			$q = "SELECT Koers,Datum FROM Valutakoersen WHERE Valuta = '".$mutatie['Valuta']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1";
			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$actuelevaluta = $DB2->NextRecord();
		}

		// haal actuele fonds koers op!
		if(empty($fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds']))
		{
			$actuelefonds = array();
			$q = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds = '".$mutatie['Fonds']."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC LIMIT 1";
			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$actuelefonds = $DB2->NextRecord();
		}

		$fondswaarden[$fondsen[$a]['Fonds']]['totaalAantal'] 			= $mutatie['totaalAantal'];
		$fondswaarden[$fondsen[$a]['Fonds']]['fondsEenheid'] 			= $mutatie['Fondseenheid'];
		$fondswaarden[$fondsen[$a]['Fonds']]['fondsOmschrijving'] = $mutatie['FondsOmschrijving'];
		$fondswaarden[$fondsen[$a]['Fonds']]['valuta'] 						= $mutatie['Valuta'];
		$fondswaarden[$fondsen[$a]['Fonds']]['eersteRentedatum'] 	= $mutatie['EersteRentedatum'];

		$fondswaarden[$fondsen[$a]['Fonds']]['rentedatum'] 				= $mutatie['Rentedatum'];
		$fondswaarden[$fondsen[$a]['Fonds']]['renteperiode'] 			= $mutatie['Renteperiode'];

		$fondswaarden[$fondsen[$a]['Fonds']]['actueleValuta'] 		= $actuelevaluta['Koers'];
		$fondswaarden[$fondsen[$a]['Fonds']]['actueleFonds'] 			= $actuelefonds['Koers'];

		$fondswaarden[$fondsen[$a]['Fonds']]['optieType'] 			= $mutatie['OptieType']; //Put/call toevoeging rvv 061123
		$fondswaarden[$fondsen[$a]['Fonds']]['optieUitoefenPrijs'] 			= $mutatie['optieUitoefenPrijs'];
		$fondswaarden[$fondsen[$a]['Fonds']]['optieExpDatum'] 			= $mutatie['OptieExpDatum'];
	}
	return $fondswaarden[$fondsen[$a]['Fonds']];
}

function gerealiseerdKoersresultaat($portefeuille, $datumBegin, $datumEind, $valuta='EUR',$vanafStartdatum = true,$attributieCategorie = 'Totaal',$gesplitst=false,$debug=false)
{
  if ($valuta != "EUR" )
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";

	 if($attributieCategorie != 'Totaal')
	 {
	   $join = " LEFT JOIN BeleggingssectorPerFonds ON Rekeningmutaties.Fonds = BeleggingssectorPerFonds.Fonds  AND BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder " ;
	   $where = " BeleggingssectorPerFonds.AttributieCategorie = '$attributieCategorie' AND ";
	 }
	 else
	 {
	   $join = '';
	   $where = '';
	 }

	// loopje over Grootboekrekeningen Opbrengsten = 1

	$query = "SELECT Rekeningmutaties.id, Fondsen.Fondseenheid, ".
	" Rekeningmutaties.Boekdatum, ".
	" Rekeningmutaties.Aantal, ".
	" Rekeningmutaties.Transactietype, ".
	" ABS(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers) AS totaal, ".
	" ABS(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS totaal_deb, ".
	" 1 $koersQuery as Rapportagekoers, ".
	" Rekeningmutaties.Fonds,  ".
	" Rekeningmutaties.Fondskoers, ".
	" Rekeningmutaties.Debet, ".
	" Rekeningmutaties.Credit, ".
	" Rekeningmutaties.Valutakoers ".
	"FROM (Rekeningmutaties, Fondsen, Rekeningen, Portefeuilles, Grootboekrekeningen)
	 $join ".
	"WHERE ".
	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
	"Rekeningmutaties.Fonds = Fondsen.Fonds AND ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening AND ".
	"(Rekeningmutaties.Transactietype = 'L' OR ".
	" Rekeningmutaties.Transactietype = 'V' OR ".
	" Rekeningmutaties.Transactietype = 'V/S' OR ".
	" Rekeningmutaties.Transactietype = 'A/S') AND ".
	"Grootboekrekeningen.FondsAanVerkoop = '1' AND ".
	 $where.
	"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$datumEind."' ".
	"ORDER BY Rekeningmutaties.Boekdatum, Rekeningmutaties.Fonds ,Rekeningmutaties.id ";

	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();

	while($mutaties = $DB->nextRecord())
	{
		$mutaties['Aantal'] = abs($mutaties['Aantal']);
  	if($vanafStartdatum)
	  	$historie = berekenHistorischKostprijs($portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$valuta,$datumBegin,$mutaties['id']);
	  else
	  	$historie = berekenHistorischKostprijs($portefeuille, $mutaties['Fonds'], $mutaties['Boekdatum'],$valuta,'',$mutaties['id']);
//if($mutaties['Fonds']=='SPDR SP Int Small Cap ETF'){ listarray($historie); listarray($mutaties);}
		if($mutaties['Transactietype'] == "A/S")
		{
			$historischekostprijs                      = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde']      * $historie['historischeValutakoers']       * $mutaties['Fondseenheid'];
			$historischekostprijsTransactieValutaKoers = ($mutaties['Aantal'] * -1) * $historie['historischeWaarde']      * $mutaties['Valutakoers']                  * $mutaties['Fondseenheid'];


			$beginditjaar                           = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
			$beginditjaarTransactieValutaKoers         = ($mutaties['Aantal'] * -1) * $historie['beginwaardeLopendeJaar'] * $mutaties['Valutakoers']               * $mutaties['Fondseenheid'];

		}
		else
		{
			$historischekostprijs                      = $mutaties['Aantal']        * $historie['historischeWaarde']      * $historie['historischeValutakoers']       * $mutaties['Fondseenheid'];
			$historischekostprijsTransactieValutaKoers = $mutaties['Aantal']        * $historie['historischeWaarde']      * $mutaties['Valutakoers']                * $mutaties['Fondseenheid'];
			$beginditjaar                              = $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar'] * $historie['beginwaardeValutaLopendeJaar'] * $mutaties['Fondseenheid'];
			$beginditjaarTransactieValutaKoers            = $mutaties['Aantal']        * $historie['beginwaardeLopendeJaar'] * $mutaties['Valutakoers']                * $mutaties['Fondseenheid'];
		}

		if ($valuta != 'EUR')
		{
		 $historischekostprijs  = $historischekostprijs / $historie['historischeRapportageValutakoers'];
		 if($vanafStartdatum==1)
       { $beginditjaar          = $beginditjaar         / getValutaKoers($valuta,$datumBegin);}
		 else
       { $beginditjaar          = $beginditjaar         / getValutaKoers($valuta,date("Y",db2jul($datumEind)).'-01-01');}
		}


		if($historie['voorgaandejarenActief'] == 0)
		{
			if($mutaties['Transactietype'] == "A/S")
			{
				$resultaatlopende       = ($mutaties['totaal_deb'] / $mutaties['Rapportagekoers'])  - $historischekostprijs;
				$fondsResultaat         = ($mutaties['totaal_deb'] / $mutaties['Rapportagekoers'])  - $historischekostprijsTransactieValutaKoers;
    
			}
			else
			{
			  $resultaatlopende         = ($mutaties['totaal']     / $mutaties['Rapportagekoers'])  - $historischekostprijs;
			  $fondsResultaat    = ($mutaties['totaal']     / $mutaties['Rapportagekoers'])  - $historischekostprijsTransactieValutaKoers;
			}
		}
		else
		{

			if($mutaties['Transactietype'] == "A/S")
			{
				$resultaatlopende =  (( $mutaties['totaal_deb'] * -1) * $mutaties['Rapportagekoers']) - $beginditjaar;
				$fondsResultaat =  (( $mutaties['totaal_deb'] * -1) * $mutaties['Rapportagekoers']) - $beginditjaarTransactieValutaKoers;
			}
			else
			{
			  $resultaatlopende =    (  $mutaties['totaal']           * $mutaties['Rapportagekoers']) - $beginditjaar;
			 
			  $fondsResultaat =    (  $mutaties['totaal']           * $mutaties['Rapportagekoers']) - $beginditjaarTransactieValutaKoers;
			}
		}
$debug=0;

if($debug==1)
{

listarray($mutaties);
listarray($historie);
//$aandeelValuta=	$mutaties['Valutakoers']/$historie['beginwaardeValutaLopendeJaar'];
//$aandeelFonds= $mutaties['Fondskoers']/$historie['beginwaardeLopendeJaar'];
  echo "$resultaatlopende =    (  ".$mutaties['totaal']."           * ".$mutaties['Rapportagekoers'].") - $beginditjaar; <br>\n";
//echo "$aandeelValuta = ".$mutaties['Valutakoers']."/".$historie['beginwaardeValutaLopendeJaar']." valuta transactiekoers/aankoopkoers <br>\n";
//echo "$aandeelFonds = ".$mutaties['Fondskoers']."/".$historie['beginwaardeLopendeJaar']." fonds Fondskoers/historischeWaarde <br>\n";
  /*  */
echo "".$mutaties['Fonds']." ResultaatTotaal= $resultaatlopende  fondsResultaat= $fondsResultaat valutaResultaat ".($resultaatlopende-$fondsResultaat)."<br>\n";ob_flush();
//echo "<br>\n";
}
  $totaal_resultaat_waarde += $resultaatlopende;
  $totaal_fondsDeel+=$fondsResultaat;
	}
  if($debug==1)
  {
    echo "$datumBegin $datumEind  $totaal_resultaat_waarde $totaal_fondsDeel<br>\n";
  }
  
  if($gesplitst==true)
	return array('totaal'=>$totaal_resultaat_waarde,'fonds'=>$totaal_fondsDeel,'valuta'=>$totaal_resultaat_waarde-$totaal_fondsDeel);

	return $totaal_resultaat_waarde;
}

function performanceMeting($portefeuille, $datumBegin, $datumEind, $type = "1", $valuta = 'EUR')
{
	global $__appvar;
  $DB = new DB();
  $query="SELECT layout FROM Vermogensbeheerders JOIN Portefeuilles on Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
  $DB->SQL($query);
  $layout=$DB->lookupRecord();
  $eigenAtt=false;
  if(file_exists($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php"))
  { 
    include_once($__appvar["basedir"]."/html/rapport/include/ATTberekening_L".$layout['layout'].".php");
    $eigenAtt=true;
  }
  elseif(file_exists($__appvar["basedir"]."/html/rapport/include/layout_".$layout['layout']."/ATTberekening_L".$layout['layout'].".php"))
  {
    include_once($__appvar["basedir"]."/html/rapport/include/layout_".$layout['layout']."/ATTberekening_L".$layout['layout'].".php");
    $eigenAtt=true;
  }
  if($eigenAtt==true)
  {
    $attObject="ATTberekening_L".$layout['layout'];
    $att=new $attObject();
    if(method_exists("ATTberekening_L".$layout['layout'],'getPerf'))
    {
      return $att->getPerf($portefeuille, $datumBegin, $datumEind,$valuta);
    }
  }
//echo   " $portefeuille, $datumBegin, $datumEind, $type , $valuta <br>\n";ob_flush();
	if($type == 6)//Attributie kwartaalwaardering
	{
	  $index=new rapportATTberekening($portefeuille);
	  $index->categorien[] = 'Totaal';
	  $performance = $index->attributiePerformance($portefeuille, $datumBegin, $datumEind,'all',$valuta,'kwartaal');
	  return $performance['Totaal'] -100;
	}
	elseif($type == 5)//Maandelijkse waardering realtime?
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaardenATT($datumBegin, $datumEind,$portefeuille,'Totaal','maand',$valuta);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;
	}
	elseif($type == 3)//TWR
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','TWR');
		foreach ($indexData as $data)
		  $performance =  $data['index'] -100;

	  return $performance;
	}
	elseif($type == 4)//Maandelijkse waardering
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','maanden',$valuta);
		foreach ($indexData as $data)
		{
     // echo "$portefeuille | $datumBegin -> ".$data['datum']." | ".$data['performance']." -> ".$data['index']."<br>\n";ob_flush();
		  $performance =  $data['index'] -100;
		}

	  return $performance;

	}
	elseif($type == 8)//Kwartaal waardering
	{
	  $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','kwartaal',$valuta);
		foreach ($indexData as $data)
		{
		  $performance =  $data['index'] -100;
		}
	  return $performance;
	}  

	if ($valuta != "EUR" )
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	else
	  $koersQuery = "";
  
  if(substr($datumBegin,0,4)==substr($datumEind,0,4) || ((substr($datumBegin,5,5)=='31-12') && substr($datumEind,5,5)=='01-01') || $type == 7 )
  {
    $wegingsDatum=$datumBegin;
    /*
    $query="SELECT Portefeuilles.Startdatum FROM Portefeuilles WHERE Portefeuilles.Portefeuille='$portefeuille'";
    $DB->SQL($query);
    $startDatum=$DB->lookupRecord();
    if(db2jul($datumBegin) <= db2jul($startDatum['Startdatum']))
    {
      $wegingsDatum=date('Y-m-d',db2jul($startDatum['Startdatum'])+86400); //$startDatum['Startdatum'];
    }
    */
	// haal beginwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum = '".$datumBegin."' AND ".
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$beginwaarde = $DB->NextRecord();
	//echo $beginwaarde." = ".$beginwaarde[totaal]." / ".getValutaKoers($valuta,$datumBegin)."<br>";
	$beginwaarde = $beginwaarde['totaal'] / getValutaKoers($valuta,$datumBegin);

	// haal eindwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum ='".$datumEind."' AND ".
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query); 
	$DB->Query();
	$eindwaarde = $DB->NextRecord();
	$eindwaarde = $eindwaarde['totaal']  / getValutaKoers($valuta,$datumEind);

	$query = "SELECT ".
	"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$wegingsDatum."')) ".
	"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	"FROM  (Rekeningen, Portefeuilles)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	"WHERE ".
	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	"Rekeningmutaties.Verwerkt = '1' AND ".
	"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
	"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
	"Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
	$DB->SQL($query);
	$DB->Query();
	$weging = $DB->NextRecord();

  $gemiddelde = $beginwaarde + $weging['totaal1'];
  if($gemiddelde <> 0)
    $performance = ((($eindwaarde - $beginwaarde) - $weging['totaal2']) / $gemiddelde) * 100;
  }
  else
  {
    $index=new indexHerberekening();
    $indexData = $index->getWaarden($datumBegin, $datumEind,$portefeuille,'','jaar',$valuta);
    foreach($indexData as $index)
        $performance=$index['index']-100;
  }

//echo "gemiddelde $gemiddelde = $beginwaarde + ".$weging[totaal1]."\n<br>\n";
//echo "$datumBegin - $datumEind -> performance = $performance = ((($eindwaarde - $beginwaarde) - ".$weging[totaal2].") / $gemiddelde) * 100";flush();
//echo "<br>$performance<br>";
return $performance;
}


function attributiePerformance($portefeuille, $datumBegin, $datumEind, $type = "1", $categorie, $valuta='EUR')
{
	global $__appvar;

	//echo " $datumBegin, $datumEind  <br>" ;

	if($type == 5)
	{
		return attributiePerformanceMaandelijkseWaardering($portefeuille, $datumBegin, $datumEind,$categorie,$valuta);
	}


	if ($valuta != "EUR" )
	{
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  $startValutakoers = getValutaKoers($valuta,$datumBegin);
	  $eindValutaKoers = getValutaKoers($valuta,$datumEind);
	}
	else
	{
	  $koersQuery = "";
	  $startValutakoers = 1;
	  $eindValutaKoers = 1;
	}

	if ($categorie == 'Liquiditeiten')
  	{
      $attributieQuery = " ( TijdelijkeRapportage.AttributieCategorie <> '' ) AND ";
  	}
  	else
    {
      $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '".$categorie."' AND";
  	}
	$attributieQuery2 = " BeleggingssectorPerFonds.AttributieCategorie = '".$categorie."' AND ";

	$DB = new DB();
	// haal beginwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum = '".$datumBegin."' AND ".
				 $attributieQuery.
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$beginwaarde = $DB->NextRecord();
	$beginwaarde = $beginwaarde[totaal] / $startValutakoers;

	if (round($beginwaarde,2) == 0)
	{
  $query =  " SELECT Rekeningmutaties.Boekdatum
	  	FROM BeleggingssectorPerFonds, Rekeningen, Portefeuilles,
			Rekeningmutaties
			WHERE
			Rekeningmutaties.Rekening = Rekeningen.Rekening AND BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND
			Rekeningen.Portefeuille = '".$portefeuille."' AND
			Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
			Rekeningmutaties.Verwerkt = '1' AND
			Rekeningmutaties.Boekdatum > '".$datumBegin."' AND
			Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
			$attributieQuery2
			Rekeningmutaties.Grootboekrekening = 'FONDS' ORDER BY Rekeningmutaties.Boekdatum LIMIT 1";

	$DB->SQL($query);
	$DB->Query();
	$startdatum = $DB->NextRecord();
	//$datumBegin = jul2sql(db2jul($startdatum['Boekdatum'])-86400);
	//$datumBegin =$datumBegin;
	}
	// haal eindwaarde op.
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				 "FROM TijdelijkeRapportage WHERE ".
				 " rapportageDatum ='".$datumEind."' AND ".
				 $attributieQuery.
				 " portefeuille = '".$portefeuille."' "
				 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$eindwaarde = $DB->NextRecord();
	$eindwaarde = $eindwaarde[totaal] / $eindValutaKoers;
	$query = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM BeleggingssectorPerFonds, Rekeningen, Portefeuilles,
			Rekeningmutaties   ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND
			BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds  AND
			BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			$attributieQuery2.
			"Rekeningmutaties.Grootboekrekening = 'FONDS'  ";

	$DB->SQL($query);//echo "$query <br><br>";
	$DB->Query();
	$weging = $DB->NextRecord();

	//Attributie grootboekkoppeling
	$query = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM Rekeningen, Portefeuilles , AttributiePerGrootboekrekening , Rekeningmutaties , Grootboekrekeningen ".
			"WHERE
			AttributiePerGrootboekrekening.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
			Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			"Rekeningmutaties.Grootboekrekening <> 'FONDS'  AND ".
			"AttributiePerGrootboekrekening.AttributieCategorie = '$categorie' ";
	$DB->SQL($query);
	$DB->Query();
	$attributie = $DB->NextRecord();




	if ($kostenDeel == 0)
	{
	// totale portefeuille waarde
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$datumEind."' AND ".
						 " portefeuille = '".$portefeuille."'  ".
			//			 " AND TijdelijkeRapportage.AttributieCategorie <> '' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
	$DB->SQL($query);
	$DB->Query();
	$portefeuilleTotaal = $DB->NextRecord() ;
	$kostenDeel = $eindwaarde / ($portefeuilleTotaal['totaal'] / $eindValutaKoers);



	/* */
	$query ="SELECT
		SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) AS subcredit,
		SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) AS subdebet,
		AttributiePerGrootboekrekening.AttributieCategorie,
 		Rekeningmutaties.Grootboekrekening,
 		Grootboekrekeningen.Kosten ,
 		Grootboekrekeningen.Opbrengst ".
		" FROM (Rekeningen, Portefeuilles , AttributiePerGrootboekrekening)
		Right JOIN Rekeningmutaties  ON  Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening  ,Grootboekrekeningen
		WHERE
		AttributiePerGrootboekrekening.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
		Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND
		Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 		Rekeningen.Portefeuille = '$portefeuille' AND
 		Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		Rekeningmutaties.Verwerkt = '1' AND
		Rekeningmutaties.Boekdatum > '".$datumBegin."' AND
 		Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
 		AttributiePerGrootboekrekening.AttributieCategorie = '$categorie' AND  Grootboekrekeningen.Kosten = '1' AND
 		Rekeningmutaties.Grootboekrekening <> 'FONDS'
		GROUP BY Rekeningmutaties.Grootboekrekening	";

		$DB->SQL($query);
		$DB->Query();

while($attributieGrootboek = $DB->nextRecord())
{
   $grootboekKostenTotaal += $attributieGrootboek['subdebet'];
   $grootboekKostenTotaal -= $attributieGrootboek['subcredit'];
}
	//echo $categorie.'--'.$grootboekKostenTotaal."<br><br>";

/*	*/
	}

	$query = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM Rekeningen, Portefeuilles , Rekeningmutaties , Grootboekrekeningen ".
			"WHERE Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			"Rekeningmutaties.Grootboekrekening <> 'FONDS' AND ".
			"Grootboekrekeningen.Kosten = '1' ";
	$DB->SQL($query);
	$DB->Query();
	$kosten = $DB->NextRecord();
//		echo $kosten['totaal2'] * $kostenDeel." = ".$kosten['totaal2']." * $kostenDeel <br>" ;
	$kosten['totaal1'] = $kosten['totaal1'] * $kostenDeel;
	$kosten['totaal2'] = $kosten['totaal2'] * $kostenDeel;


//
$kosten['totaal2'] = $grootboekKostenTotaal;
//

		/*

	if ($kostenDeel == 0)
	{
	// totale portefeuille waarde
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$datumEind."' AND ".
						 " portefeuille = '".$portefeuille."'  ".
			//			 " AND TijdelijkeRapportage.AttributieCategorie <> '' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
	$DB->SQL($query);
	$DB->Query();
	$portefeuilleTotaal = $DB->NextRecord() ;



		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$datumEind' AND ".
						 " TijdelijkeRapportage.AttributieCategorie = '' AND".
						 " portefeuille = '$portefeuille' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeLiquiditeiten = $DB->nextRecord();


	$kostenDeel = $eindwaarde / ($portefeuilleTotaal['totaal'] - $totaalWaardeLiquiditeiten['totaal'] / $eindValutaKoers);



	$query ="SELECT
		SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) AS subcredit,
		SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) AS subdebet,
		AttributiePerGrootboekrekening.AttributieCategorie,
 		Rekeningmutaties.Grootboekrekening,
 		Grootboekrekeningen.Kosten ,
 		Grootboekrekeningen.Opbrengst ".
		" FROM (Rekeningen, Portefeuilles , AttributiePerGrootboekrekening)
		Right JOIN Rekeningmutaties  ON  Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening  ,Grootboekrekeningen
		WHERE
		AttributiePerGrootboekrekening.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
		Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND
		Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 		Rekeningen.Portefeuille = '$portefeuille' AND
 		Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		Rekeningmutaties.Verwerkt = '1' AND
		Rekeningmutaties.Boekdatum > '".$datumBegin."' AND
 		Rekeningmutaties.Boekdatum <= '".$datumEind."' AND
 		AttributiePerGrootboekrekening.AttributieCategorie = '$categorie' AND  Grootboekrekeningen.Kosten = '1' AND
 		Rekeningmutaties.Grootboekrekening <> 'FONDS'
		GROUP BY Rekeningmutaties.Grootboekrekening	";

		$DB->SQL($query);
		$DB->Query();

while($attributieGrootboek = $DB->nextRecord())
{
   $grootboekKostenTotaal += $attributieGrootboek['subdebet'];
   $grootboekKostenTotaal -= $attributieGrootboek['subcredit'];
}
	//echo $categorie.'--'.$grootboekKostenTotaal."<br><br>";


	}

	$query = "SELECT ".
			"SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM Rekeningen, Portefeuilles , Rekeningmutaties , Grootboekrekeningen ".
			"WHERE Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
			"Rekeningmutaties.Grootboekrekening <> 'FONDS' AND ".
			"Grootboekrekeningen.Kosten = '1' ";
	$DB->SQL($query); //echo " <br> $query <br>";
	$DB->Query();
	$kosten = $DB->NextRecord();
//		echo $kosten['totaal2'] * $kostenDeel." = ".$kosten['totaal2']." * $kostenDeel <br>" ;
//	$kosten['totaal1'] = $kosten['totaal1'] * $kostenDeel;
//	$kosten['totaal2'] = $kosten['totaal2'] * $kostenDeel;

//Kosten naar ratio
$query = "SELECT
SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers )) AS grootboekKosten ,
SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum))
			 / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."'))
		  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS grootboekKostenGewogen,
  Rekeningmutaties.Grootboekrekening, Grootboekrekeningen.Kosten , Grootboekrekeningen.Opbrengst
FROM (Rekeningen, Portefeuilles , Rekeningmutaties , Grootboekrekeningen )
WHERE
Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND
Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 		Rekeningen.Portefeuille = '".$portefeuille."' AND
 		Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		Rekeningmutaties.Verwerkt = '1' AND
		Rekeningmutaties.Boekdatum > '$datumBegin' AND
 		Rekeningmutaties.Boekdatum <= '$datumEind' AND
 Rekeningmutaties.Grootboekrekening <> 'FONDS' AND
Grootboekrekeningen.Kosten = 1
GROUP BY Rekeningmutaties.Grootboekrekening" ;

$DB->SQL($query);// echo $query;exit;
$grootboekKostenData = $DB->lookupRecord();
$grootboekKostenTotaal= $grootboekKostenData['grootboekKosten'] *-1;
$grootboekKostenGewogen = $grootboekKostenData['grootboekKostenGewogen'] *-1;

//
//$kosten['totaal1'] = $grootboekKostenGewogen * $kostenDeel;
//$kosten['totaal2'] = $grootboekKostenTotaal * $kostenDeel;
//
$kosten = array();

*/

	switch($type)
	{
	  case "0" :
		case "1" :
		case "2" :
			$gemiddelde = $beginwaarde - $weging['totaal1'] - $attributie['totaal1'] - $kosten['totaal1'];
			if($gemiddelde <> 0)
			{
				$performance = ((($eindwaarde - $beginwaarde) + $weging['totaal2'] + $attributie['totaal2'] + $kosten['totaal2']) / $gemiddelde) * 100;
//echo  "$datumBegin - $datumEind -> $categorie =>  $performance = ((($eindwaarde - $beginwaarde) + ".$weging['totaal2']." + ".$attributie['totaal2']." + ".$kosten['totaal2'].") / $gemiddelde) * 100 <br><br>";
//echo " gemiddelde = " .$totaalWaarde['begin']." - ".$weging['totaal1']." - " .$attributie['totaal1']." - ".$kosten['totaal1']." <br>";
//flush();exit;
			}
		break;
		default :
//
		break;
	}
	return $performance;
}

function fondsParticipatieAantalOpdatum($fonds, $rapportageDatum)
{
	$jaar = date("Y", db2jul($rapportageDatum));

	$qMutaties = "SELECT SUM(FondsParticipatieVerloop.Aantal) AS totaalAantal ".
		" FROM FondsParticipatieVerloop ".
		" WHERE ".
		" FondsParticipatieVerloop.Fonds = '".$fonds."' AND ".
		" YEAR(FondsParticipatieVerloop.Datum) = '".$jaar."' AND ".
		" FondsParticipatieVerloop.Datum <= '".$rapportageDatum."' ".
		" GROUP BY FondsParticipatieVerloop.Fonds ";

	$DB = new DB();
	$DB->SQL($qMutaties);
	$DB->Query();
	$fdata = $DB->Nextrecord();

	return $fdata['totaalAantal'];
}

//////////////////////////////
function berekenRekeningWaarde($portefeuille, $rapportageDatum, $min1dag = false)
{
	/*
		datum = SQL datum
	*/

	$jaar = substr($rapportageDatum,0,4);

	$rekeningwaarden 	 = array();


	$query = "
	SELECT
    Rekeningmutaties.*,
    Rekeningen.Termijnrekening,
    Rekeningen.Tenaamstelling,
    SUM(Rekeningmutaties.Bedrag) as totaal,
    Rekeningen.Valuta as rekeningValuta
  FROM
    Rekeningmutaties, Rekeningen
  WHERE
    Rekeningmutaties.Rekening = Rekeningen.Rekening AND
    Rekeningen.Memoriaal = 0 AND
    Rekeningen.Portefeuille = '".$portefeuille."' AND
    Rekeningmutaties.boekdatum >= '".$jaar."-01-01' AND
    Rekeningmutaties.boekdatum <= '".$rapportageDatum."'
    Group By Rekeningmutaties.Rekening";
// rvv 061204 Toevoeging rekening Valuta om echte valuta rekening te bepalen.
	$DB1 = new DB();
	$DB1->SQL($query);
	$DB1->Query();

	$t = 0;
	while($data = $DB1->NextRecord())
	{
		if($data[Tenaamstelling] )
		{
			$rekeningType = $data[Tenaamstelling];
		}
		else
		{
			$rekeningType = "Effectenrekening ";
		}

		if(round($data['totaal'],2)	<> 0)
		{
			// haal actuele valuta koers op!
			$actuelevaluta = array();
			$q = "
			SELECT
			  Koers,Datum
			FROM
			  Valutakoersen
			WHERE
			  Valuta = '".$data['rekeningValuta']."' AND
			  Datum <= '".$rapportageDatum."'
			ORDER BY
			  Datum DESC ";
			$DB2 = new DB();
			$DB2->SQL($q);
			$DB2->Query();
			$actuelevaluta = $DB2->lookupRecord();
			$output[$t]["koers"]    = $actuelevaluta["Koers"];
    }
    if (substr($data["rekeningValuta"],3,1) == "F")  // termijnrekening met eigen valutacode
    {
      $output[$t]["rekening"] = $data["Rekening"];
		  $output[$t]["bedrag"]   = $data["totaal"];
		  $output[$t]["valuta"]   = substr($data["rekeningValuta"],0,3);
		  $output[$t]["termijn"]  = 1;
    }
    else
    {
      $output[$t]["rekening"] = $data["Rekening"];
		  $output[$t]["bedrag"]   = $data["totaal"];
		  $output[$t]["valuta"]   = $data["rekeningValuta"];
		  $output[$t]["termijn"]  = $data["Termijnrekening"];
    }
    $output[$t]["tenaamstelling"]  = $rekeningType;


		$t++;

	}

//	listarray($output);
	// return nieuwe array.
	return $output;
}

  function ongerealiseerdeKoersResultaat($portefeuille,$startdatum,$einddatum,$valuta = 'EUR')
  {
    global $__appvar;
    $RapJaar = date("Y", db2jul($einddatum));
    $RapStartJaar = date("Y", db2jul($startdatum));
    $DB = new DB();

    // ophalen van het totaal beginwaare en actuele waarde voor ongerealiseerde koersresultaat
 		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".getValutaKoers($valuta,$einddatum)."  AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro)/ ".getValutaKoers($valuta,$startdatum)."  AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$einddatum' AND ".
						 " portefeuille = '$portefeuille' AND "
						 ." type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaal = $DB->nextRecord();
		$ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA']; //huidigeJaarRapdatum - 01-01-HuidigeJaar = OngerealiseerdHuidigeJaar.


//rvv 	Extra query die het mogelijk maakt om een startdatum na 1-1-jaar te kiezen. Het resultaat binnen het lopende jaar tot de start
//		datum wordt van het totaal afgehaald.
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".getValutaKoers($valuta,$startdatum)." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro / ".getValutaKoers($valuta,$RapStartJaar.'-01-01')." ) AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='$startdatum' AND ".
						 " portefeuille = '$portefeuille' AND "
						 . " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];

		$DB->SQL($query);
		$DB->Query();
		$totaalWaardeVanaf = $DB->nextRecord();
		$ongerealiseerdeKoersResultaatTotStart = $totaalWaardeVanaf['totaalB'] - $totaalWaardeVanaf['totaalA'];
		$ongerealiseerdeKoersResultaat -= $ongerealiseerdeKoersResultaatTotStart;


    if ($RapJaar != $RapStartJaar) //Wanneer we startdatum in het afgelopen jaar kiezen moeten we de resultaten van dat jaar ook ophalen.
    {
    	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".getValutaKoers($valuta,$RapStartJaar."-12-31")."  AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro) / ".getValutaKoers($valuta,$RapStartJaar."-01-01")." AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$RapStartJaar."-12-31' AND ".
						 " portefeuille = '".$portefeuille."' AND ".
						 " type = 'fondsen' ".$__appvar['TijdelijkeRapportageMaakUniek'];
		$DB->SQL($query);
		$DB->Query();
		$totaalVorigeJaar = $DB->nextRecord();
		$ongerealiseerdeKoersResultaatVorigJaar = ($totaalVorigeJaar['totaalB'] - $totaalVorigeJaar['totaalA']);
		$ongerealiseerdeKoersResultaat += $ongerealiseerdeKoersResultaatVorigJaar  ;
    }
 	return $ongerealiseerdeKoersResultaat;
  }




function performanceMetingMaandelijkseWaardering($portefeuille, $datumBegin, $datumEind,$valuta = 'EUR')
{
  $julBegin = db2jul($datumBegin);
  $julEind = db2jul($datumEind);

  if ($valuta != "EUR" )
	{
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	}
	else
	{
	  $koersQuery = "";
	  $startValutakoers = 1;
	  $eindValutaKoers = 1;
	}


 	$eindjaar = date("Y",$julEind);
	$eindmaand = date("m",$julEind);
	$beginjaar = date("Y",$julBegin);
	$startjaar = date("Y",$julBegin);
	$beginmaand = date("m",$julBegin);

	$ready = false;
	$i=0;
	$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	$totaalPerf =100;

	$datum = array();
	while ($ready == false)
	{
	  if (mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar) > $stop)
	  {
	    $ready = true;
		}
		else
		{
		  if($i==0)
        $datum[$i]['start']=$datumBegin;
	    else
	    {
		    $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));//(0,0,0,$beginmaand+$i,1,$startjaar)); eerstevanmaand
	    }
	     $datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
	    $i++;
		}

	}
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));//(0,0,0,$beginmaand+$i-1,1,$startjaar));eerstevanmaand
	$datum[$i]['stop']=$datumEind;

	foreach ($datum as $periode)
	{
    if(db2jul($periode['start']) == mktime (0,0,0,1,0,$startjaar))
	    $periode['start']= $startjaar.'-01-01';

	  if ($valuta != "EUR" )
	  {
	    $startValutakoers = getValutaKoers($valuta,$periode['start']);
	    $eindValutaKoers = getValutaKoers($valuta,$periode['stop']);
	  }

	  if(db2jul($periode['start']) == mktime (0,0,0,1,1,$beginjaar) )
    	$startjaar = true;
    else
			$startjaar = false;


    $totaalWaarde = array();
	  $fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$periode['start'],$startjaar,$valuta,$periode['start']);

	  foreach ($fondswaarden['beginmaand'] as $regel)
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
    $totaalWaarde['begin'] = $totaalWaarde['begin']  / $startValutakoers;


    $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$periode['stop'],false,$valuta,$periode['start']);
		foreach ($fondswaarden['eindmaand'] as $regel)
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
    $totaalWaarde['eind'] = $totaalWaarde['eind']/$eindValutaKoers;

    $DB = new DB();
    	$query = "SELECT ".
	    "SUM(((TO_DAYS('".$periode['stop']."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
	    "  / (TO_DAYS('".$periode['stop']."') - TO_DAYS('".$periode['start']."')) ".
	    "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
	    "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers )$koersQuery - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
	    "FROM  (Rekeningen, Portefeuilles)
	     Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
	    "WHERE ".
      "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
	    "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
	    "Rekeningmutaties.Verwerkt = '1' AND ".
	    "Rekeningmutaties.Boekdatum > '".$periode['start']."' AND ".
	    "Rekeningmutaties.Boekdatum <= '".$periode['stop']."' AND ".
	    "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
    $DB->SQL($query);
    $DB->Query();
    $weging = $DB->NextRecord();

    $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];

    if($gemiddelde <> 0)
	  {
	    $performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100;
		}
	//		echo $periode['start'].'-'.$periode['stop']."  => $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") + ".$weging['totaal2']." + ".$attributie['totaal2']." + ".$kosten['totaal2'].") / $gemiddelde) * 100;<br>";
	//		echo ($totaalPerf  * (100+$performance)/100)." = ($totaalPerf  * (100+$performance)/100) <br>";
		    $totaalPerf = ($totaalPerf  * (100+$performance)/100) ;
    //  echo "<br>".$totaalPerf."<br>";
	}
	//echo"<br><br>";
	return $totaalPerf-100;
}

function attributiePerformanceMaandelijkseWaardering($portefeuille, $datumBegin, $datumEind,$categorie,$valuta='EUR')
{
  if ($valuta != "EUR" )
	{
	  $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$valuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	}
	else
	{
	  $koersQuery = "";
	  $startValutakoers = 1;
	  $eindValutaKoers = 1;
	}

  $julBegin = db2jul($datumBegin);
  $julEind = db2jul($datumEind);

 	$eindjaar = date("Y",$julEind);
	$eindmaand = date("m",$julEind);
	$beginjaar = date("Y",$julBegin);
	$startjaar = date("Y",$julBegin);
	$beginmaand = date("m",$julBegin);

	$ready = false;
	$i=0;
	$totaalPerf = 100;
	$stop=mktime (0,0,0,$eindmaand,0,$eindjaar);
	$datum == array();

	while ($ready == false)
	{
	  if (mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar) > $stop)
	  {
	    $ready = true;
		}
		else
		{
		  if($i==0)
        $datum[$i]['start']=$datumBegin;
	    else
	    {
		    $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));//(0,0,0,$beginmaand+$i,1,$startjaar)); eerstevanmaand
	    }
	    $datum[$i]['stop']=jul2db(mktime (0,0,0,$beginmaand+$i+1,0,$beginjaar));
	    $i++;
		}
	}
	if($i==0)
    $datum[$i]['start']=$datumBegin;
	else
	  $datum[$i]['start']=jul2db(mktime (0,0,0,$beginmaand+$i,0,$startjaar));//(0,0,0,$beginmaand+$i-1,1,$startjaar));eerstevanmaand
	$datum[$i]['stop']=$datumEind;

	foreach ($datum as $periode)
	{
	  if(db2jul($periode['start']) == mktime (0,0,0,1,0,$startjaar))//eerstevanmaand
	    $periode['start']= $startjaar.'-01-01';//eerstevanmaand

	  if ($valuta != "EUR" )
	  {
	    $startValutakoers = getValutaKoers($valuta,$periode['start']);
	    $eindValutaKoers = getValutaKoers($valuta,$periode['stop']);
	  }

	  if(db2jul($periode['start']) == mktime (0,0,0,1,1,$beginjaar) )
    	$startjaar = true;
    else
			$startjaar = false;

    $totaalWaarde = array();
	  $fondswaarden['beginmaand'] =  berekenPortefeuilleWaarde($portefeuille,$periode['start'],$startjaar,$valuta,$periode['start']);
	  foreach ($fondswaarden['beginmaand'] as $regel)
	  {
	    if($regel['AttributieCategorie'] == $categorie)
        $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'] /$startValutakoers;
	  }

    $fondswaarden['eindmaand'] =  berekenPortefeuilleWaarde($portefeuille,$periode['stop'],false,$valuta,$periode['start']);
		foreach ($fondswaarden['eindmaand'] as $regel)
		{
		  if($regel['AttributieCategorie'] == $categorie)
        $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'] / $eindValutaKoers;
		}
//echo "$categorie ".$periode['start']."->".$periode['stop']." koers $startValutakoers en  $eindValutaKoers -> ". $totaalWaarde['begin']." | ".$totaalWaarde['eind']."<br>";
    if ($categorie == 'Liquiditeiten')
  	{
      $attributieQuery = " ( TijdelijkeRapportage.AttributieCategorie <> '' ) AND ";
  	}
  	else
    {
      $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '".$categorie."' AND";
  	}
	$attributieQuery2 = " BeleggingssectorPerFonds.AttributieCategorie = '".$categorie."' AND ";

	$DB = new DB();


if(round($totaalWaarde['begin'],2) == 0)
{
   $query =" SELECT Rekeningmutaties.Boekdatum
       FROM BeleggingssectorPerFonds, Rekeningen, Portefeuilles, Rekeningmutaties   ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND
			BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND
			BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$periode['start']."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$periode['stop']."' AND ".
			$attributieQuery2.
			"Rekeningmutaties.Grootboekrekening = 'FONDS'
			ORDER  BY Rekeningmutaties.Boekdatum limit 1
			";

  $DB->SQL($query);// echo $query;flush();exit;
	$DB->Query();
	$eersteBoekdatum = $DB->NextRecord();
	$periode['start'] =  jul2sql(db2jul($eersteBoekdatum['Boekdatum'])-86400);
}
//echo " ".$periode['start']." ";

 $query = "SELECT ".
			"SUM(((TO_DAYS('".$periode['stop']."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$periode['stop']."') - TO_DAYS('".$periode['start']."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM BeleggingssectorPerFonds, Rekeningen, Portefeuilles, Rekeningmutaties ".
			"WHERE ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND
			BeleggingssectorPerFonds.Fonds = Rekeningmutaties.Fonds AND
			BeleggingssectorPerFonds.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$periode['start']."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$periode['stop']."' AND ".
			$attributieQuery2.
			"Rekeningmutaties.Grootboekrekening = 'FONDS'  ";


	$DB->SQL($query);// echo $query;flush();exit;

	$DB->Query();
	$weging = $DB->NextRecord();

	//Attributie grootboekkoppeling
	$query = "SELECT ".
			"SUM(((TO_DAYS('".$periode['stop']."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$periode['stop']."') - TO_DAYS('".$periode['start']."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM Rekeningen, Portefeuilles , AttributiePerGrootboekrekening , Rekeningmutaties , Grootboekrekeningen ".
			"WHERE
			AttributiePerGrootboekrekening.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
			Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$periode['start']."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$periode['stop']."' AND ".
			"Rekeningmutaties.Grootboekrekening <> 'FONDS'  AND ".
			"AttributiePerGrootboekrekening.AttributieCategorie = '$categorie' ";
	$DB->SQL($query); //echo "$query <br><br>";flush();exit;
	$DB->Query();
	$attributie = $DB->NextRecord();
/*
	if ($kostenDeel == 0)
	{
	// totale portefeuille waarde
	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$periode['stop']."' AND ".
						 " portefeuille = '".$portefeuille."'  ".
			//			 " AND TijdelijkeRapportage.AttributieCategorie <> '' ".
						 $__appvar['TijdelijkeRapportageMaakUniek'];
	$DB->SQL($query);
	$DB->Query();
	$portefeuilleTotaal = $DB->NextRecord() ;
	$kostenDeel = $totaalWaarde['eind'] / ($portefeuilleTotaal['totaal'] / $eindValutaKoers);
	}

	*/

	$query ="SELECT
		SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) AS subcredit,
		SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) AS subdebet,
		SUM(((TO_DAYS('".$periode['stop']."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$periode['stop']."') - TO_DAYS('".$periode['start']."'))
		  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS TotaalGewogen,
		AttributiePerGrootboekrekening.AttributieCategorie,
 		Rekeningmutaties.Grootboekrekening,
 		Grootboekrekeningen.Kosten ,
 		Grootboekrekeningen.Opbrengst ".
		" FROM (Rekeningen, Portefeuilles , AttributiePerGrootboekrekening)
		Right JOIN Rekeningmutaties  ON  Rekeningmutaties.Grootboekrekening = AttributiePerGrootboekrekening.Grootboekrekening  ,Grootboekrekeningen
		WHERE
		AttributiePerGrootboekrekening.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND
		Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND
		Rekeningmutaties.Rekening = Rekeningen.Rekening AND
 		Rekeningen.Portefeuille = '$portefeuille' AND
 		Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		Rekeningmutaties.Verwerkt = '1' AND
		Rekeningmutaties.Boekdatum > '".$periode['start']."' AND
 		Rekeningmutaties.Boekdatum <= '".$periode['stop']."' AND
 		AttributiePerGrootboekrekening.AttributieCategorie = '$categorie'  AND
 		Rekeningmutaties.Grootboekrekening <> 'FONDS'
		GROUP BY Rekeningmutaties.Grootboekrekening	";

		$DB->SQL($query); //echo " $query  <br><br>";flush();
		$DB->Query();

while($attributieGrootboek = $DB->nextRecord())
{
   $grootboekKostenTotaal += $attributieGrootboek['subdebet'];
   $grootboekKostenTotaal -= $attributieGrootboek['subcredit'];

   $grootboekKostenTotaalGewogen += $attributieGrootboek['TotaalGewogen'];

}
//	echo $categorie.'--'.$grootboekKostenTotaal."<br><br>";
	//flush();
/*

	$query = "SELECT ".
			"SUM(((TO_DAYS('".$periode['stop']."') - TO_DAYS(Rekeningmutaties.Boekdatum)) ".
			"  / (TO_DAYS('".$periode['stop']."') - TO_DAYS('".$periode['start']."')) ".
			"  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery) ))) AS totaal1, ".
			"SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery))  AS totaal2 ".
			"FROM Rekeningen, Portefeuilles , Rekeningmutaties , Grootboekrekeningen ".
			"WHERE Grootboekrekeningen.Grootboekrekening = Rekeningmutaties.Grootboekrekening AND ".
			"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
			"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
			"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
			"Rekeningmutaties.Verwerkt = '1' AND ".
			"Rekeningmutaties.Boekdatum > '".$periode['start']."' AND ".
			"Rekeningmutaties.Boekdatum <= '".$periode['stop']."' AND ".
			"Rekeningmutaties.Grootboekrekening <> 'FONDS' AND ".
			"Grootboekrekeningen.Kosten = '1' ";
  $DB->SQL($query); // echo $query." <br><br>";
	$DB->Query();
	$kosten = $DB->NextRecord();
		//echo $kosten['totaal2'] * $kostenDeel." = ".$kosten['totaal2']." * $kostenDeel <br>" ;
	$kosten['totaal1'] = $kosten['totaal1'] * $kostenDeel;
	$kosten['totaal2'] = $kosten['totaal2'] * $kostenDeel;
*/

//
$kosten['totaal2'] = $grootboekKostenTotaal *-1;
$kosten['totaal1'] = $grootboekKostenTotaalGewogen *-1;
//
$kosten =  array();


$gemiddelde = $totaalWaarde['begin'] - $weging['totaal1'] - $attributie['totaal1'] - $kosten['totaal1'];

				$performance = ((($totaalWaarde['eind'] - $totaalWaarde['begin']) + $weging['totaal2'] + $attributie['totaal2'] + $kosten['totaal2']) / $gemiddelde) * 100;
//echo $periode['start'].'-'.$periode['stop']." $categorie => $performance = (((".$totaalWaarde['eind']." - ".$totaalWaarde['begin'].") + ".$weging['totaal2']." + ".$attributie['totaal2']." + ".$kosten['totaal2'].") / $gemiddelde) * 100;<br>";
//echo " gemiddelde = " .$totaalWaarde['begin']." - ".$weging['totaal1']." - " .$attributie['totaal1']." - ".$kosten['totaal1']."<br>";
//flush();
//	$totaalPerf+= $performance;
				$totaalPerf = ($totaalPerf  * (100+$performance)/100) ;
	}
//	listarray($som);

	return $totaalPerf -100;
}

function AFMstd($portefeuille,$datum,$debug=false)
{
  $db=new DB();
  //$query="SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='$portefeuille'";
  //$db->SQL($query);
  //$vermogensbeheerder=$db->lookupRecord();

  $query="SELECT SUM(actuelePortefeuilleWaardeEuro) as waarde FROM TijdelijkeRapportage WHERE Portefeuille='$portefeuille' AND rapportageDatum='$datum'";
  $db->SQL($query); 
  $totaal=$db->lookupRecord();
  if($totaal['waarde'] <> 0)
    $totaalWaarde=$totaal['waarde'];
  else
    $totaalWaarde=1;

  $query="SELECT id,afmCategorie,omschrijving,standaarddeviatie,correlatie FROM afmCategorien WHERE afmCategorie like '%liquiditeiten%'";
  $db->SQL($query);
  $liquiditeiten=$db->lookupRecord();
  
  /*
    $query="SELECT if(TijdelijkeRapportage.`type`='rekening','".$liquiditeiten['afmCategorie']."',BeleggingscategoriePerFonds.afmCategorie) as afmCategorie,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$totaalWaarde as percentage
FROM TijdelijkeRapportage LEFT  Join BeleggingscategoriePerFonds ON TijdelijkeRapportage.fonds = BeleggingscategoriePerFonds.Fonds AND BeleggingscategoriePerFonds.Vermogensbeheerder='".$vermogensbeheerder['Vermogensbeheerder']."'
WHERE Portefeuille='$portefeuille' AND rapportageDatum='$datum'
GROUP BY BeleggingscategoriePerFonds.afmCategorie ";
  */
  
  $query="SELECT TijdelijkeRapportage.afmCategorie as afmCategorie,
SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro)/$totaalWaarde as percentage
FROM TijdelijkeRapportage 
WHERE Portefeuille='$portefeuille' AND rapportageDatum='$datum'
GROUP BY TijdelijkeRapportage.afmCategorie";

  $db->SQL($query);
  $db->Query();
  $afmVerdeling=array();
  while($data=$db->nextRecord())
  {
    $afmVerdeling[$data['afmCategorie']]=$data['percentage'];
  }

  $afmCategorien=array();
  $afmCategorieCorrelatie=array();
  $afmCategorienStd=array();
  $query="SELECT id,afmCategorie,omschrijving,standaarddeviatie,correlatie FROM afmCategorien ORDER BY id";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    $afmCategorien[$data['afmCategorie']]=$data['id'];
    $data['correlatie']=unserialize($data['correlatie']);
    $afmCategorienStd[$data['id']]=$data['standaarddeviatie'];
    foreach ($data['correlatie'] as $id=>$correlatie)
    {
      if($correlatie <> '')
      {
        $afmCategorieCorrelatie[$data['id']][$id]=$correlatie;
        $afmCategorieCorrelatie[$id][$data['id']]=$correlatie;
      }
    }
  }

  $afmVerdelingId=array();
  foreach ($afmVerdeling as $categorie=>$percentage)
  {
    $afmVerdelingId[$afmCategorien[$categorie]]=$percentage;
  }

  $afmVerdelingIdKeys=array_keys($afmVerdelingId);

  $var=0;
  $debugTxt.="relatie tussen categorie => berekening\n";
  foreach ($afmVerdelingIdKeys as $id)
  {
    foreach ($afmVerdelingId as $key2=>$percentage)
    {
      if($afmCategorieCorrelatie[$id][$key2] <> 0)
      {
        $relatieVar[$id.'_'.$key2]=$percentage*$afmCategorienStd[$id];
        $relatieVarDebug[$id.'_'.$key2]=round($percentage,4)." * ".$afmCategorienStd[$id]." ";
        if($id == $key2)
        {
          if($debug)
            $debugTxt.=$id.'_'.$key2." =>  ".round($percentage,4)."^2 * ".$afmCategorieCorrelatie[$id][$key2]."^2 * ".$afmCategorienStd[$id]."^2 =".pow($percentage,2)*pow($afmCategorieCorrelatie[$id][$key2],2)*pow($afmCategorienStd[$id],2)."\n";
          $var+=pow($percentage,2)*pow($afmCategorieCorrelatie[$id][$key2],2)*pow($afmCategorienStd[$id],2);
        }
        else
        {
          if(isset($relatieVar[$key2.'_'.$id]))
          {
            if($debug)
              $debugTxt.= $id.'_'.$key2." => 2 * ".$relatieVarDebug[$key2.'_'.$id]." * ".round($percentage,4)." * ".$afmCategorieCorrelatie[$id][$key2]." * ".$afmCategorienStd[$id]."\n";
            $var+=2* $relatieVar[$key2.'_'.$id]* $percentage*$afmCategorieCorrelatie[$id][$key2]*$afmCategorienStd[$id];
          }
        }
      }
    }
  }
  if($debug)
   $debugTxt.= "var=$var\n";
  $afmstd=pow($var,0.5);
  if($debug)
    $debugTxt.= "afmStd=$afmstd\n";
  //

  if($debug)
  {
    $debugArray['debugTxt']=$debugTxt;
    $debugArray['verdeling']=$afmVerdelingId;
    $debugArray['std']=$afmCategorienStd;
    $debugArray['correlatie']=$afmCategorieCorrelatie;
  }

  return array('std'=>$afmstd,'debug'=>$debugArray);

}

function getAFMWaarden($portefeuille,$rapportageDatum)
  {
    global $__appvar;
    $DB = new DB();
		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$rapportageDatum."' AND ".
						 " portefeuille = '".$portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
		$totaalWaarde = $totaalWaarde['totaal'];


    $query="SELECT id,afmCategorie,omschrijving FROM afmCategorien ORDER BY id";
		$DB->SQL($query);
		$DB->Query();
    $categorien=array('nietIngedeeld'=>'Niet ingedeeld');
		while($data=$DB->nextRecord())
    {
      $categorien[$data['afmCategorie']]=$data['omschrijving'];
      $afmCode[substr($data['afmCategorie'],0,2)]=$data['afmCategorie'];
    }

		$actueleWaardePortefeuille = 0;

    foreach($categorien as $categorie=>$omschrijving)
    {
      if($categorie=='nietIngedeeld')
        $filterCategorie='';
      else
        $filterCategorie=$categorie;
        
      $afmCategorieverdeling[$categorie]['omschrijving']=$omschrijving;
		  $query = "SELECT TijdelijkeRapportage.afmCategorieOmschrijving as Omschrijving, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.afmCategorie, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta, ".
			" TijdelijkeRapportage.actuelePortefeuilleWaardeEuro,
        TijdelijkeRapportage.fondsOmschrijving ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$portefeuille."' AND 
        TijdelijkeRapportage.afmCategorie='$filterCategorie' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].

			" ORDER BY TijdelijkeRapportage.afmCategorie asc,  TijdelijkeRapportage.valutaVolgorde asc";
	  	debugSpecial($query,__FILE__,__LINE__);
  		$DB->SQL($query); 
	  	$DB->Query();
      while($data=$DB->nextRecord())
      { 
        if($data['afmCategorie']=='')
          $data['afmCategorie']='nietIngedeeld';
        $afmCategorieverdeling[$data['afmCategorie']]['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
        $afmCategorieverdeling[$data['afmCategorie']]['procent']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;
        $afmCategorieverdeling[$data['afmCategorie']]['fondsen'][$data['fondsOmschrijving']]['actuelePortefeuilleWaardeEuro']+=$data['actuelePortefeuilleWaardeEuro'];
        $afmCategorieverdeling[$data['afmCategorie']]['fondsen'][$data['fondsOmschrijving']]['procent']+=$data['actuelePortefeuilleWaardeEuro']/$totaalWaarde;

      }
    }
    return array('verdeling'=>$afmCategorieverdeling,'totaalWaarde'=>$totaalWaarde,'codeKoppel'=>$afmCode);
    
  }

function getFondsenVerdiept($portefeuille,$datum,$details=false)
{
	global $__appvar;
	$DB = new DB();


	$query = "SELECT
Fondsen.fonds,
Fondsen.Portefeuille,
round(SUM(Rekeningmutaties.Aantal),4) as aantal,
Rekeningmutaties.Boekdatum
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds AND Fondsen.Portefeuille<>''
WHERE Rekeningen.Portefeuille='$portefeuille' AND 
Rekeningmutaties.Boekdatum>='".substr($datum,0,4)."-01-01' AND Rekeningmutaties.Boekdatum<='$datum'
GROUP BY Fondsen.Portefeuille
HAVING aantal <> 0 ";

	$DB->SQL($query);
	$DB->Query();
	$fondsPortefeuilleData=array();
	$verdiepteData=array();
	while($data = $DB->nextRecord())
	{
		$fondsPortefeuilleData[$data['fonds']] = $data['Portefeuille'];
	}
	if($details==false)
    return $fondsPortefeuilleData;
	else
		return array('fondsData'=>$fondsPortefeuilleData);
}
function bepaaldFondsWaardenVerdiept($pdf,$portefeuille,$einddatum)
{
	global $__appvar;
	$startjaar=true;

	include_once($__appvar["basedir"]."/classes/portefeuilleVerdieptClass.php");
	$verdiept = new portefeuilleVerdiept($pdf,$portefeuille,$einddatum);
	$verdiepteFondsen = $verdiept->getFondsen();

	foreach ($verdiepteFondsen as $fonds)
		$verdiept->bepaalVerdeling($fonds,$verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$einddatum);

	$fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$einddatum,$startjaar,'EUR',substr($einddatum,0,4).'-01-01');
	$correctieVelden=array('totaalAantal','ActuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
	foreach($fondswaarden as $i=>$fondsData)
	{
		//
		if(isset($pdf->fondsPortefeuille[$fondsData['fonds']]))
		{
			$fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
			$fondsWaardeHuis=$pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
			$aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
			//echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis ";exit;
			unset($fondswaarden[$i]);
			foreach($pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
			{
				foreach ($details as $element => $emementDetail)
				{
					if(isset($emementDetail['overige']))
					{
						foreach($correctieVelden as $veld)
							$emementDetail['overige'][$veld]=($emementDetail['overige'][$veld]*$aandeel);
						unset($emementDetail['overige']['WaardeEuro']);
						unset($emementDetail['overige']['koersLeeftijd']);
						unset($emementDetail['overige']['FondsOmschrijving']);
						unset($emementDetail['overige']['Fonds']);
						$fondswaarden[] = $emementDetail['overige'];
					}
				}
			}
		}
	}
	$fondswaarden  = array_values($fondswaarden);
  //listarray($fondswaarden);
	$tmp=array();
	$conversies=array('ActuelePortefeuilleWaardeEuro'=>'actuelePortefeuilleWaardeEuro');
  $correctieVelden=array('totaalAantal','actuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
	foreach($fondswaarden as $mixedInstrument)
	{
		$instrument=array();
		foreach($mixedInstrument as $variabele=>$waarde)
		{
			if(isset($conversies[$variabele]))
      {
        $variabele=$conversies[$variabele];
        $instrument[$variabele] = $waarde;
      }
			else
      {
        $instrument[$variabele] = $waarde;
      }
		}
		//listarray($instrument);
		unset($instrument['voorgaandejarenactief']);

		$key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
		if(isset($tmp[$key]))
		{
			foreach($correctieVelden as $index=>$veld)
			{
				$tmp[$key][$veld] += $instrument[$veld];
			}
		}
		else
			$tmp[$key]=$instrument;
		//	listarray($instrument);
	}
	$fondswaarden  = array_values($tmp);
	//echo $portefeuille,$einddatum;listarray($fondswaarden);
	return $fondswaarden;
}

function consolidatieAanmaken($postData,$rapportageStartDatum,$rapportageEindDatum)
{
	global $USR;
	$rapportageDatum['a']=$rapportageStartDatum;
	$rapportageDatum['b']=$rapportageEindDatum;
	$startData=array();

	/*
	if(isset($postData['Portefeuille'])&& $postData['Portefeuille'] <> '')
	{
		$postData['selectedFields']=array();
		$DB = new DB();
		$DB->SQL("SELECT * FROM GeconsolideerdePortefeuilles WHERE VirtuelePortefeuille='".$postData['Portefeuille']."'");
		$DB->Query();
		$pdata = $DB->nextRecord();
		$consolidatiePaar=$pdata;
		for($i=1;$i<41;$i++)
			if($pdata['Portefeuille'.$i] <> '')
				$postData['selectedFields'][] = $pdata['Portefeuille'.$i];
	}
*/

	$n=0;
	foreach ($postData['selectedFields'] as $portefeuille)
	{
		// controle of gebruiker bij vermogensbeheerder mag
		if(checkAccess())
		{
			$join = "";
			$beperktToegankelijk = '';
		}
		else
		{
			$join = " INNER JOIN VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND  VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."'
	  				JOIN Gebruikers ON Gebruikers.Gebruiker = VermogensbeheerdersPerGebruiker.Gebruiker ";

			if($_SESSION['usersession']['gebruiker']['internePortefeuilles'] == '1')
				$internDepotToegang="OR Portefeuilles.interndepot=1";
			else

				$internDepotToegang='';

			if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
				$beperktToegankelijk = " AND (Portefeuilles.Accountmanager='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' OR Portefeuilles.tweedeAanspreekpunt ='".$_SESSION['usersession']['gebruiker']['Accountmanager']."' $internDepotToegang) ";
			else
				$beperktToegankelijk = " AND (Portefeuilles.beperktToegankelijk = '0' OR  Gebruikers.beperkingOpheffen = '1' ) ";
		}

		// check begin datum rapportage!
		//Portefeuilles.SpecifiekeIndex, verwijderd
		$query = "SELECT Portefeuilles.Portefeuille,  
	         Portefeuilles.Startdatum,".
			"Portefeuilles.Einddatum,		".
			"Portefeuilles.RapportageValuta, ".
			"Portefeuilles.AEXVergelijking,".
			"Portefeuilles.PortefeuilleVoorzet,
				   Portefeuilles.startdatumMeerjarenrendement,
          Portefeuilles.Taal,
					Accountmanagers.Naam as accountmanager,
					tweedeAanspreekpunt.naam as tweedeAanspreekpunt,".
			"Clienten.naam,		".
			"Vermogensbeheerders.AfdrukvolgordeOIH,		".
			"Vermogensbeheerders.AfdrukvolgordeOIS, 	".
			"Vermogensbeheerders.AfdrukvolgordeOIR, 	".
			"Vermogensbeheerders.AfdrukvolgordeHSE, 	".
			"Vermogensbeheerders.AfdrukvolgordeOIB, 	".
			"Vermogensbeheerders.AfdrukvolgordeOIV, 	".
			"Vermogensbeheerders.AfdrukvolgordePERF, 	".
			"Vermogensbeheerders.AfdrukvolgordeVOLK, 	".
			"Vermogensbeheerders.AfdrukvolgordeVHO, 	".
			"Vermogensbeheerders.AfdrukvolgordeTRANS, ".
			"Vermogensbeheerders.AfdrukvolgordeMUT, 	".
			"Vermogensbeheerders.AfdrukvolgordeGRAFIEK,	".
			"Vermogensbeheerders.attributieInPerformance,	".
			"Vermogensbeheerders.Vermogensbeheerder,	".
			"Vermogensbeheerders.Export_data_frontOffice,	".
			"Portefeuilles.Depotbank	".
			" FROM (Portefeuilles, Vermogensbeheerders, Clienten) ".$join."
					  LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager= Accountmanagers.Accountmanager
					  LEFT JOIN Accountmanagers as tweedeAanspreekpunt ON Portefeuilles.tweedeAanspreekpunt=tweedeAanspreekpunt.Accountmanager 
					  WHERE Portefeuilles.Portefeuille = '".$portefeuille."'".
			" AND Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder ".
			" AND Portefeuilles.Client = Clienten.Client $beperktToegankelijk ";

		// asort
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$pdata = $DB->nextRecord();

		if ($n==0)
		{
			$hoofdPdata= $pdata;
		}
		elseif ($n==1)
		{
			$Pdata2 = $pdata;
		}

		// todo : sorteer rapporttypes
		// todo : controlleer of datum in data bereik zit!!

		$query = "SELECT Depotbank, Rekening, Valuta, Memoriaal, Termijnrekening, Tenaamstelling , RenteBerekenen, Rente30_360, Beleggingscategorie, AttributieCategorie, Inactief FROM Rekeningen WHERE Portefeuille = '".$portefeuille."'";
		$DB->SQL($query);
		$DB->Query();
		while($rekening = $DB->NextRecord())
			$rekeningen[] = array('rekening'=>$rekening['Rekening'],
														'valuta'=>$rekening['Valuta'],
														'memoriaal'=>$rekening['Memoriaal'],
														'tenaamstelling'=>$rekening['Tenaamstelling'],
														'termijnrekening'=>$rekening['Termijnrekening'],
														'RenteBerekenen'=>$rekening['RenteBerekenen'],
														'Rente30_360'=>$rekening['Rente30_360'],
														'Beleggingscategorie'=>$rekening['Beleggingscategorie'],
														'AttributieCategorie'=>$rekening['AttributieCategorie'],
														'Depotbank'=>$rekening['Depotbank'],
														'Inactief'=>$rekening['Inactief'] )  ;




		$n++;
		$portefeuilles[]=$portefeuille;

		if($pdata['Startdatum']=='0000-00-00 00:00:00')
		{
			echo vtb("Portefeuille %s heeft geen startdatum.", array($portefeuille));
			exit;
		}

		if(isset($consolidatiePaar['Startdatum']) && substr($consolidatiePaar['Startdatum'],0,10) <> '0000-00-00')
		{
			$rapportageDatum['a'] = $consolidatiePaar['Startdatum'];
			$startData[] = db2jul($rapportageDatum['a']);
		}
		else
		{
			if ($pdata['Startdatum'] <> '')
			{
				$startData[] = db2jul($pdata['Startdatum']);
			}
		}
		if($pdata['Einddatum']<>'')
			$eindData[]=db2jul($pdata['Einddatum']);

		$startDataRendement[]=db2jul($pdata['startdatumMeerjarenrendement']);
	}

	$eersteStart=min($startData);
	if(db2jul($rapportageDatum['a']) < $eersteStart)
	{
		$rapportageDatum['a'] = jul2sql($eersteStart);
		$hoofdRapportageDatumVanaf = $rapportageDatum['a'];
	}

	if(db2jul($rapportageDatum['b']) > max($eindData))
	{
		echo "<b>Fout: portefeille $portefeuille heeft een einddatum  (".date("d-m-Y",max($eindData)).")</b>";
		exit;
	}

	// controlleer of datum a niet groter is dan datum b!
	if(db2jul($rapportageDatum['a']) > db2jul($rapportageDatum['b']))
	{
		echo "<b>Fout: Van datum kan niet groter zijn dan T/m datum! </b>";
		exit;
	}


	if(isset($postData['Portefeuille']) && $postData['Portefeuille'] <> '')
	{
		$portefeuille = $postData['Portefeuille'];//substr($_POST['Portefeuille'],0,8).'_'.substr($USR,0,3); //  `Portefeuille` varchar(12) default NULL, `VirtuelePortefeuille` varchar(12) default NULL,
		$client=$consolidatiePaar['Client'];
	}
	else
	{
		$client=$USR;
		$portefeuille = 'C_'.$USR;
	}
	verwijderTijdelijkeTabel($portefeuille);
	$pdata = $hoofdPdata;

	if(isset($consolidatiePaar))
	{
		foreach($consolidatiePaar as $key=>$value)
		{
			if($key=='Risicoprofiel')
				$key='Risicoklasse';
			$pdata[$key]=$value;
		}
	}

	$queries = array();
	$delay=15;
	$queries[] = "DELETE FROM Clienten WHERE consolidatie = 2 AND (add_date < now() - interval $delay minute OR Client='".$client."' ) ";
	$queries[] = "DELETE FROM Portefeuilles WHERE consolidatie = 2 AND (add_date <  now() - interval $delay minute OR Portefeuille = '".$portefeuille."' ) ";
	$queries[] = "DELETE FROM Rekeningen WHERE consolidatie = 2 AND (add_date < now() - interval $delay minute OR Portefeuille = '".$portefeuille."' ) ";

	foreach($queries as $query)
	{
		//logit($query);
		$DB->SQL($query);
		$DB->Query();
	}


	$query="SELECT id,add_date,add_user,Portefeuille FROM Rekeningen WHERE consolidatie=2 limit 1";//Portefeuille = '".$portefeuille."' AND
	$DB->SQL($query);
	$DB->Query();
	$aanwezig=$DB->lookupRecord();
	if($aanwezig['id']>0)
	{
		if($aanwezig['add_user']==$USR)
		{
			echo "Er zijn momenteel nog geconsolideerde Rekeningen voor portefeuille (" . $aanwezig['Portefeuille'] . ") aanwezig. <a href='?verwijder=1'>verwijder</a>";// Deze records worden binnen $delay minuten verwijderd waarna het rapport opnieuw gestart kan worden.
			exit;
		}
		else
		{
			echo "Er zijn momenteel nog geconsolideerde Rekeningen voor portefeuille (" . $aanwezig['Portefeuille'] . ") aanwezig. (" . $aanwezig['add_date'] . "/" . $aanwezig['add_user'] . ")";// Deze records worden binnen $delay minuten verwijderd waarna het rapport opnieuw gestart kan worden.
			exit;
		}
	}

	$createInfo=", add_date=NOW(),change_date=NOW(),add_user='$USR',change_user='$USR' ";
	$queries = array();
	$queries[] = "DELETE FROM Rekeningen WHERE Portefeuille = '".$portefeuille."'";
	$queries[] = "INSERT INTO Clienten SET consolidatie=2, Client = '".$client."' , Naam = 'Consolidatie(".$USR.")' $createInfo";
	if(max($startDataRendement)>1)
		$startdatumMeerjarenrendement="startdatumMeerjarenrendement='".date('Y-m-d',max($startDataRendement))."',";
	else
		$startdatumMeerjarenrendement='';
	$pdata['startDatum']=date('Y-m-d',$eersteStart);
	$queries[] = "INSERT INTO Portefeuilles SET consolidatie=2,
                                          Client = '".$client."' ,
	                                        Portefeuille = '".$portefeuille."' ,
	                                        startDatum = '".$pdata['startDatum']."',
	                                        Einddatum = '2037-12-31',
                                          $startdatumMeerjarenrendement
	                                        Depotbank = '".$pdata['Depotbank'] ."' ,
                                          Taal = '".$pdata['Taal']."' ,
	                                        AEXVergelijking = '".$pdata['AEXVergelijking'] ."' ,
	                                        PortefeuilleVoorzet = '".$pdata['PortefeuilleVoorzet'] ."' ,
	                                        SpecifiekeIndex = '".$pdata['SpecifiekeIndex']."' ,
                                          RapportageValuta = '".$pdata['RapportageValuta']."' ,
                                          Risicoprofiel = '".$pdata['Risicoprofiel'] ."' ,
                                          Risicoklasse = '".$pdata['Risicoklasse'] ."' ,
                                          SoortOvereenkomst = '".$pdata['SoortOvereenkomst'] ."' ,
                                          ModelPortefeuille = '".$pdata['ModelPortefeuille'] ."' ,
                                          ZpMethode = '".$pdata['ZpMethode'] ."' ,
	                                        Vermogensbeheerder = '".$pdata['Vermogensbeheerder'] ."' $createInfo";



	for ($a=0; $a < count($queries); $a++)
	{
		$DB->SQL($queries[$a]);
		$DB->Query();
	}

	foreach ($rekeningen as $rekening)
	{
		$query = "INSERT INTO Rekeningen SET consolidatie=2,
                                       Portefeuille = '".mysql_real_escape_string($portefeuille)."',
	                                     Rekening = '".mysql_real_escape_string($rekening['rekening']) ."',
	                                     Valuta = '".mysql_real_escape_string($rekening['valuta'])."',
	                                     Memoriaal = '".mysql_real_escape_string($rekening['memoriaal'])."',
	                                     Tenaamstelling = '".mysql_real_escape_string($rekening['tenaamstelling'])."',
	                                     Termijnrekening = '".mysql_real_escape_string($rekening['termijnrekening']) ."',
	                                     RenteBerekenen = '".mysql_real_escape_string($rekening['RenteBerekenen']) ."',
	                                     Rente30_360 = '".mysql_real_escape_string($rekening['Rente30_360']) ."',
	                                     Beleggingscategorie = '".mysql_real_escape_string($rekening['Beleggingscategorie']) ."',
                                       Depotbank = '".mysql_real_escape_string($rekening['Depotbank'])."',
                                       Inactief = '".mysql_real_escape_string($rekening['Inactief'])."',
	                                     AttributieCategorie = '".mysql_real_escape_string($rekening['AttributieCategorie']) ."' $createInfo";
		$DB->SQL($query);
		$DB->Query();
	}

  return array('portefeuille'=>$portefeuille,'rapportageStart'=>$rapportageDatum['a'],'rapportageEind'=>$rapportageDatum['b'],'portefeuilles'=>$portefeuilles,
							 'consolidatiePaar'=>$consolidatiePaar,'hoofdPdata'=>$hoofdPdata,'pdata'=>$pdata,'Pdata2'=>$Pdata2);
}

function verwijderConsolidatie($portefeuille)
{
	global $USR;
	$queries = array();
	$queries[] = "DELETE FROM Clienten WHERE consolidatie = 2 AND add_user='$USR' ";
	$queries[] = "DELETE FROM Portefeuilles WHERE consolidatie = 2 AND add_user='$USR' ";
	$queries[] = "DELETE FROM Rekeningen WHERE consolidatie = 2 AND Portefeuille = '".$portefeuille."' AND add_user='$USR'";

	$DB=new DB();
	for ($a=0; $a < count($queries); $a++)
	{
		$DB->SQL($queries[$a]);
		$DB->Query();
	}
}

function bepaalModelUitsluitingen($portefeuille,$einddatum)
{
  global $__appvar;
  //Uitsluitingen begin
  //$portefeuilleData = berekenPortefeuilleWaarde($this->portefeuille, $einddatum,(substr($einddatum, 5, 5) == '01-01')?true:false,'EUR',$einddatum);
  //vulTijdelijkeTabel($portefeuilleData,$this->portefeuille,$einddatum);
  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal FROM TijdelijkeRapportage WHERE ".
    " rapportageDatum = '".$einddatum."' AND ".
    " portefeuille = '".$portefeuille."' AND type <> 'rente' " //$extraCategorieFilter
    .$__appvar['TijdelijkeRapportageMaakUniek'];
  debugSpecial($query,__FILE__,__LINE__);
  $DB3 = new DB();
  $DB3->SQL($query);
  $DB3->Query();
  $portefwaarde = $DB3->nextRecord();
  
  $query="SELECT uitsluitingenModelcontrole.fonds,uitsluitingenModelcontrole.rekening,uitsluitingenModelcontrole.bedrag,Fondsen.Omschrijving as fondsOmschrijving
FROM uitsluitingenModelcontrole
LEFT JOIN Fondsen ON uitsluitingenModelcontrole.fonds=Fondsen.Fonds
WHERE uitsluitingenModelcontrole.portefeuille='".$portefeuille."'";
  $DB3->SQL($query);
  $DB3->Query();
  $uitsluitingen=array();
  $gecorigeerdeRekeningen=array();
  $portefeuilleRegels=array();
  while($data = $DB3->nextRecord())
  {
    $uitsluitingen[]=$data;
  }
  if(count($uitsluitingen)>0)
  {
    $portefeuilleRegels[]=array('Uitgesloten:');
  }
  foreach($uitsluitingen as $regel)
  {
    if($regel['fonds']<>'')
    {
      
      $query = "DELETE FROM TijdelijkeRapportage WHERE fonds='" . mysql_real_escape_string($regel['fonds']) . "' AND portefeuille='" . $portefeuille. "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      if($regel['fondsOmschrijving']<>'')
        $txt=$regel['fondsOmschrijving'];
      else
        $txt=$regel['fonds'];
    }
    elseif($regel['rekening']=='alle')
    {
      $query = "DELETE FROM TijdelijkeRapportage WHERE type='rekening' AND portefeuille='" .$portefeuille. "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      $txt='Alle rekeningen';
      $gecorigeerdeRekeningen['alle']='alle';
    }
    elseif($regel['bedrag']<>0)
    {
      $query = "UPDATE TijdelijkeRapportage SET actuelePortefeuilleWaardeEuro=actuelePortefeuilleWaardeEuro-" . doubleval($regel['bedrag']) . " WHERE portefeuille='" . $portefeuille . "' AND rekening='" . mysql_real_escape_string($regel['rekening']) . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      $txt=$regel['rekening'].', bedrag €'.number_format($regel['bedrag'],2,',','.');
      $gecorigeerdeRekeningen[$regel['rekening']]=$regel['rekening'];
    }
    elseif($regel['rekening']<>'')
    {
      $query = "DELETE FROM TijdelijkeRapportage WHERE type='rekening' AND portefeuille='" . $portefeuille. "' AND rekening='" . mysql_real_escape_string($regel['rekening']) . "' " . $__appvar['TijdelijkeRapportageMaakUniek'];
      $txt=$regel['rekening'];
    }
    else
    {
      $query = '';
      $txt='';
    }
    if($query<>'')
    {
      $portefeuilleRegels[]=array($txt);
//          logscherm($query);
      $DB3->SQL($query);
      $DB3->Query();
    }
  }
  if(count($uitsluitingen)>0)
  {
    $portefeuilleRegels[]=array('');
  }
  return array('portefeuilleRegels'=>$portefeuilleRegels,'gecorigeerdeRekeningen'=>$gecorigeerdeRekeningen,'portefwaarde'=>$portefwaarde);
}

?>