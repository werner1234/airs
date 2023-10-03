<?php
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("rapport/rapportRekenClass.php");
include_once("indexBerekening.php");
include_once("rapport/CashflowClass.php");
include_once("../classes/AE_cls_fpdf.php");
include_once("rapport/Zorgplichtcontrole.php");


global $__appvar;

//rvv
/*
    $y = array();

    $index = 0 ;
    for($x = 0 ; $x< 10 ; $x += 1) {
        $y[$index] =  round(sin($x)*100);
        $index++ ;
    }

    $series = array();
    $series[0] = $y ;
    $data = json_encode($series);
    echo $data;
    exit();
*/
//rvv



//echo '[["Heavy Industry",12],["Retail",9],["Light Industry",14],["Out of home",16]]';exit;

if($_POST['datum']=='')
  $_POST['datum']=substr(getLaatsteValutadatum(),0,10);

if($_POST['vulling']=='1')
{
  $eenJan=false;
  if(substr($_POST['datum'],5,5)=='01-01')
    $eenJan=true;
  //$jaar=substr($_POST['datum'],0,4);  
  //$beginDatum="$jaar-01-01";
  //vulTijdelijkeTabel(berekenPortefeuilleWaarde($_POST['portefeuille'],$beginDatum,true),$_POST['portefeuille'],$beginDatum);
  vulTijdelijkeTabel(berekenPortefeuilleWaarde($_POST['portefeuille'],$_POST['datum'],$eenJan),$_POST['portefeuille'],$_POST['datum']);

}


$db=new DB();
$query="SELECT SUM(actuelePortefeuilleWaardeEuro) as totaal FROM TijdelijkeRapportage WHERE
TijdelijkeRapportage.rapportageDatum='".$_POST['datum']."' AND
TijdelijkeRapportage.portefeuille='".$_GET['portefeuille']."'
".$__appvar['TijdelijkeRapportageMaakUniek'];
$db->SQL($query);
$totaal= $db->lookupRecord();
$totaal=$totaal['totaal'];



if($_GET['chart'] <> '')
{
  if($_GET['chart']=='rendement')
  {
    $db=new DB();
    $query="SELECT SpecifiekeIndex FROM Portefeuilles WHERE Portefeuille='".$_GET['portefeuille']."'";
    $db->SQL($query);
    $specifiekeIndex=$db->lookupRecord();
    $tabel=array();
    //echo "$query ".$specifiekeIndex['SpecifiekeIndex'];
    $startDatum=(substr($_POST['datum'],0,4)-1)."-".substr($_POST['datum'],5,2)."-01";
    $index=new indexHerberekening();
    $indexData = $index->getWaarden($startDatum, $_POST['datum'],$_GET['portefeuille'],$specifiekeIndex['SpecifiekeIndex'],'maanden');
    foreach($indexData as $perfData)
    {
      if($perfData['specifiekeIndexPerformance']==-100)
        $perfData['specifiekeIndexPerformance']=0;
      $tabel[]=array('perf'=>$perfData['index']-100,
                     'indexFonds'=>$perfData['specifiekeIndexPerformance'],
                     'periode'=>substr($perfData['periodeForm'],13,10));
    }
    echo json_encode($tabel);
    exit;
    
  }
  
  if($_GET['chart']=='cash')
  {
    $tabel=array();
    $cash=new Cashflow($_GET['portefeuille'],db2jul($_POST['datum']),db2jul($_POST['datum']));
    $cash->genereerTransacties();
    $cash->genereerRows();

    foreach ($cash->gegevens['jaar'] as $jaar => $waarden)
    {
      if($waarden['lossing'] == 0)
        $waarden['lossing']=0;
      if($waarden['rente'] == 0)
        $waarden['rente']=0;
               
        
      $tabel[]=array('lossing'=>$waarden['lossing'],'rente'=>$waarden['rente'],'jaar'=>$jaar);
    }
    
    echo json_encode($tabel);
    exit;
    
  }
  
  


	$query="SELECT grafiek_kleur FROM Vermogensbeheerders 
  JOIN Portefeuilles ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder 
  WHERE portefeuille='".$_GET['portefeuille']."'";
	$db->SQL($query);
	$db->Query();
	$kleuren = $db->LookupRecord();
	$allekleuren = unserialize($kleuren['grafiek_kleur']);
  
  if($_GET['chart']=='Valuta')
    $valutaKleuren=$allekleuren['OIV'];
  elseif($_GET['chart']=='Beleggingscategorie')
    $valutaKleuren=$allekleuren['OIB'];
    
  $veldNaam=$_GET['chart'];
  $query="SELECT
TijdelijkeRapportage.$veldNaam,
TijdelijkeRapportage.".$veldNaam."Omschrijving,
sum(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) as actuelePortefeuilleWaardeEuro
FROM
TijdelijkeRapportage
WHERE
TijdelijkeRapportage.rapportageDatum='".$_POST['datum']."' AND
TijdelijkeRapportage.portefeuille='".$_GET['portefeuille']."'
".$__appvar['TijdelijkeRapportageMaakUniek']." GROUP BY TijdelijkeRapportage.$veldNaam ORDER BY TijdelijkeRapportage.".$veldNaam."Volgorde";

//$kleuren=array('#FF0000','#00FF00','#0000FF','#FF00FF','#00FFFF','#FF00FF');
  $db->SQL($query);
  $db->Query();
  $tabel=array();
  $verdeling=array();
  $waarde=array();
  $omschrijving=array();
  $kleuren=array();
  $kleurVars=array('R','G','B');
  //listarray($valutaKleuren);
  while ($data=$db->nextRecord())
  { 
    if($data[$veldNaam.'Omschrijving']=='')
      $data[$veldNaam.'Omschrijving']='Geen omschrijving';
    $percentage=$data['actuelePortefeuilleWaardeEuro']/$totaal *100;
    $verdeling[] = $percentage; //$data[$veldNaam.'Omschrijving']." (".round($percentage,1).")"
    $waarde[] = $data['actuelePortefeuilleWaardeEuro'];
    $omschrijving[] =$data[$veldNaam.'Omschrijving']."";
        
    $kleur='#';
    foreach($kleurVars as $var)
    {
      $decValue=$valutaKleuren[$data[$veldNaam]][$var]['value'];

      if($decValue==='')
        $decValue=rand(0,255);
      $kleur.=str_pad(dechex($decValue), 2, '0', STR_PAD_LEFT);
    } 
    $kleuren[]=$kleur;
    
    $tabel[]=array('verdeling'=>$percentage,'omschrijving'=>$data[$veldNaam.'Omschrijving'],'waarde'=>$data['actuelePortefeuilleWaardeEuro'],'kleur'=>$kleur);
  }
  if(count($verdeling) < 1)
    $verdeling[]=100;
  $tmp=json_encode($tabel);
  //$tmp=json_encode(array(array('verdeling'=>$verdeling,'omschrijving'=>$omschrijving,'kleuren'=>$kleuren,'waarde'=>$waarde)));
  //$tmp= json_encode(array($verdeling,$omschrijving,$kleuren,$waarde,array('a'=>'b')));
  //logit($query);
  echo $tmp;
  exit;
}





	function getFondsDiv($fonds,$portefeuille)
	{
	  $divId=rand(1,2000000000);
	  //$divId='';
	  $html='<a id="aTag'.$divId.'" href="javascript:toggleAndChangeText('.$divId.');">&#9658</a>
<div id="divToToggle'.$divId.'" style="display:none">
   <a href="fondskoersenList.php?Fonds='.$fonds.'" target="_blank"><b>Fondskoers</b></a>
</div>';

	  return $html;
	}
 

  if($_GET['table']=='zorg')
  {
   // listarray($_GET);  
    $tabel=array();
    $db=new DB();
  	$query="SELECT Vermogensbeheerder,Portefeuille  FROM Portefeuilles WHERE portefeuille='".$_GET['portefeuille']."'";
    $db->SQL($query);
    $pdata=$db->lookupRecord();
 
    $zorg = new Zorgplichtcontrole();
    $zorgData=$zorg->zorgplichtMeting($pdata,$_POST['datum']);        
    //    listarray($zorgData['conclusie']);
    //  $tabel[]=array('lossing'=>$waarden['lossing'],'rente'=>$waarden['rente'],'jaar'=>$jaar);
    
    $tmp=array();
    foreach($zorgData['conclusie'] as $regel=>$data)
    {
     $tmp[]=array($data[0],$data[1],str_replace(',','.',$data[2]),str_replace(',','.',str_replace('.','',$data[3])),'',$data[5]);
    }
    echo  json_encode($tmp);
    exit;
 
    echo json_encode($zorgData['conclusie']);
    exit;
    
  }


if($_GET['table']=='samenstelling')
{
  $totalenOp=$_POST['totalen'];

  $query="SELECT
  TijdelijkeRapportage.totaalAantal,
  TijdelijkeRapportage.Fonds,
  TijdelijkeRapportage.$totalenOp,
  TijdelijkeRapportage.".$totalenOp."Omschrijving,
    TijdelijkeRapportage.fondsOmschrijving,
  TijdelijkeRapportage.actueleFonds,

  TijdelijkeRapportage.actuelePortefeuilleWaardeEuro
  FROM
  TijdelijkeRapportage
  WHERE
  TijdelijkeRapportage.rapportageDatum='".$_POST['datum']."' AND
  TijdelijkeRapportage.portefeuille='".$_POST['portefeuille']."'
  ".$__appvar['TijdelijkeRapportageMaakUniek']."
  ORDER BY TijdelijkeRapportage.".$totalenOp."Volgorde,TijdelijkeRapportage.fondsOmschrijving";

  $db->SQL($query);
  $db->Query();
  $tmp=array();
  $align=array('align="left"','align="right"','align="right"','align="right"');
  $width=array('width="300"','width="300"','width="300"','width="300"');
  $tmp[]=array('<td '.$width[0].' '.$align[0].'><b>Fonds</b></td>','<td '.$width[1].' '.$align[1].'><b>Aantal</b></td>','<td '.$width[2].' '.$align[2].'><b>Fondskoers</b></td>','<td '.$width[3].' '.$align[3].'><b>Waarde</b></td>');
  $totalen=array();
  while ($data=$db->nextRecord())
  {
    if($data[$totalenOp."Omschrijving"]=='')
      $data[$totalenOp."Omschrijving"]='Geen omschrijving';

    if($data[$totalenOp."Omschrijving"]<>$lastcategorie)
    {
      if(isset($totalen[$lastcategorie]))
        $tmp[]=array('<td '.$width[0].' '.$align[0].'><b>Totaal '.$lastcategorie.'</b></td>','<td '.$width[1].' '.$align[1].'></td>','<td '.$width[2].' '.$align[2].'></td>','<td '.$width[3].' '.$align[3].'><b>'.formatGetal($totalen[$lastcategorie],2).'</b></td>');
      $tmp[]=array('<td><b>'.$data[$totalenOp."Omschrijving"].'</b></td>');
    }

    $tmp[]=array('<td '.$width[0].' '.$align[0].'>'.$data['fondsOmschrijving'].' '.getFondsDiv($data['Fonds'],$_POST['portefeuille']).' </td>','<td '.$width[1].' '.$align[1].'>'.$data['totaalAantal'].'</td>','<td '.$width[2].' '.$align[2].'>'.$data['actuelePortefeuilleWaardeEuro'].'</td>','<td '.$width[3].' '.$align[3].'>'.formatGetal($data['actuelePortefeuilleWaardeEuro'],2).'</td>');
    $lastcategorie=$data[$totalenOp."Omschrijving"];
    $totalen[$lastcategorie]=$totalen[$lastcategorie]+$data['actuelePortefeuilleWaardeEuro'];
  }
  if(isset($totalen[$lastcategorie]))
    $tmp[]=array('<td '.$width[0].' '.$align[0].'><b>Totaal '.$lastcategorie.'</b></td>','<td '.$width[1].' '.$align[1].'></td>','<td '.$width[2].' '.$align[2].'></td>','<td '.$width[3].' '.$align[3].'><b>'.formatGetal($totalen[$lastcategorie],2).'</b></td>');

  $tmp[]=array('<td '.$width[0].' '.$align[0].'><b>Totaal portefeuille</b></td>','<td '.$width[1].' '.$align[1].'></td>','<td '.$width[2].' '.$align[2].'></td>','<td '.$width[3].' '.$align[3].'><b>'.formatGetal($totaal,2).'</b></td>');

  if(count($tmp) < 1)
    $tmp[]=array('Geen waarden');
echo encodeTable($tmp);
exit;
}

if($_GET['table']=='Perf')
{
  $startDatum=substr($_POST['datum'],0,4)."-01-01";
  $waarden=getPerfWaarden($_POST['portefeuille'],$startDatum,$_POST['datum']);
 
  $table="<table>";
  $table.="<tr><td><b>Performance</b></td></tr>";
  $table.="<tr><td>Waarde Portefeuille per ".date("d-m-Y",db2jul($startDatum))."</td><td align=\"right\">".formatGetal($waarden['waardeBegin'])."</td></tr>";
  $table.="<tr><td>Waarde Portefeuille per ".date("d-m-Y",db2jul($_POST['datum']))."</td><td align=\"right\">".formatGetal($waarden['waardeEind'])."</td></tr>";
  $table.="<tr><td></td><td></td></tr>";
  $table.="<tr><td>Mutatie waarde portefeuille </td><td align=\"right\">".formatGetal($waarden['waardeEind'])."</td></tr>";
  $table.="<tr><td>Totaal stortingen gedurende verslagperiode</td><td align=\"right\">".formatGetal($waarden['stortingen'])."</td></tr>";
  $table.="<tr><td>Totaal onttrekkingen gedurende verslagperiode</td><td align=\"right\">".formatGetal($waarden['onttrekkingen '])."</td></tr>";
  $table.="<tr ><td></td><td></td></tr>";
  $table.="<tr><td>Resultaat over verslagperiode</td><td align=\"right\">".formatGetal($waarden['resultaatVerslagperiode'])."</td></tr>";
  $table.="<tr><td></td><td></td></tr>";
  $table.="<tr><td>Rendement over verslagperiode</td><td align=\"right\">".formatGetal($waarden['rendementProcent'],2)."</td></tr>";  
  $table.="</table>";
  echo $table;
  
  exit;
} 



function encode($tmp)
{
  $txt='';
  foreach ($tmp as $key=>$value)
  {
    if($txt <> '')
      $txt.=',';
    $txt.="['$key',$value]";
  }
  return "[".$txt."]";
}

function encodeTable($tmp)
{
  $txt='<table>';
  foreach ($tmp as $regel=>$data)
  {
    $txt.='<tr>';
    foreach ($data as $key=>$value)
      $txt.=$value;
     $txt.='</tr>';
  }
  $txt.='</table>';
  return $txt;
}

if (!function_exists('json_encode')) {
    function json_encode($data) {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}
	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

	function getPerfWaarden($portefeuille,$vanafDatum,$totDatum)
	{
	 global $__appvar;
  	// ***************************** ophalen data voor afdruk ************************ //

  	$waarden=array();
    $koersQuery = "";
	    $totRapKoers=1;
	    $vanRapKoers=1;

    
    if(substr($vanafDatum,5,5)=='01-01')
      $beginJaar=true;
    else
      $beginJaar=false;  
 
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$vanafDatum,$beginJaar,'EUR',$vanafDatum);
    vulTijdelijkeTabel($fondsen,$portefeuille,$vanafDatum);
    $totaal=array();
    foreach($fondsen as $id=>$regel)
    {
      if($regel['type']=='rente')
      {
        $totaalB['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      } 
    }
 
    $totaalWaarde['totaal']=0;
    $totaalWaardeVanaf['totaal']=0;
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$totDatum,false,'EUR',$vanafDatum);
    vulTijdelijkeTabel($fondsen,$portefeuille,$totDatum);
    $totaal=array();
    foreach($fondsen as $id=>$regel)
    {
      $totaalWaarde['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      $totaalWaardeVanaf['totaal']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
      if($regel['type']=='rente')
      {
        $totaalA['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      }
      if($regel['type']=='fondsen')
      {
        $totaal['totaalB']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
        $totaal['totaalA']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
      }
    }

    $ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA'];
    $waarden['ongerealiseerdeKoersResultaat']=$ongerealiseerdeKoersResultaat;


    $DB=new DB();

		$waardeEind				  = $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($portefeuille,$vanafDatum,$totDatum,'EUR');
		$onttrekkingen 		 	= getOnttrekkingen($portefeuille,$vanafDatum,$totDatum,'EUR');
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	= performanceMeting($portefeuille, $vanafDatum, $totDatum, 1,'EUR');

		$waarden['waardeEind']=$waardeEind;
		$waarden['waardeBegin']=$waardeBegin;
		$waarden['waardeMutatie']=$waardeMutatie;
		$waarden['stortingen']=$stortingen;
		$waarden['onttrekkingen ']=$onttrekkingen;
		$waarden['resultaatVerslagperiode']=$resultaatVerslagperiode;
		$waarden['rendementProcent']=$rendementProcent;

    $RapJaar = date("Y", db2jul($totDatum));
    $RapStartJaar = date("Y", db2jul($vanafDatum));
		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;
		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($portefeuille, $vanafDatum, $totDatum,'EUR',true);
		$totaalOpbrengst += $gerealiseerdeKoersResultaat;
		$waarden['gerealiseerdeKoersResultaat']=$gerealiseerdeKoersResultaat;

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $totRapKoers;
		$totaalOpbrengst += $opgelopenRente;
		$waarden['opgelopenRente']=$opgelopenRente;


      $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";


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
		  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' ";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

			while($opbrengst = $DB2->nextRecord())
			{
				$opbrengstenPerGrootboek[$gb['Omschrijving']] =  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}
		$waarden['opbrengstenPerGrootboek']=$opbrengstenPerGrootboek;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		// loopje over Grootboekrekeningen Kosten = 1
		  $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
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
			if($kosten['Grootboekrekening'] == "KNBA")
			{
			  $kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = "Bankkosten en provisie";
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			elseif($kosten['Grootboekrekening'] == "KOBU")
			{
				$kostenPerGrootboek['KOST']['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}
			else
			{
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'];
				$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
			}


			$totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
		}
					foreach ($kostenPerGrootboek as $data)
			{
			  $tmp[$data['Omschrijving']]=$data['Bedrag'];
			}

		$waarden['kostenPerGrootboek']=$tmp;
		$waarden['totaalKosten']=$totaalKosten;

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		$koersResulaatValutas = $resultaatVerslagperiode - ($totaalOpbrengst  -  $totaalKosten);
		$totaalOpbrengst += $koersResulaatValutas;
		$waarden['kostenProcent']=$kostenProcent;
		$waarden['koersResulaatValutas']=$koersResulaatValutas;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

		return $waarden;
	}
?>
