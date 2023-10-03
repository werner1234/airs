<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/04/17 07:35:47 $
    File Versie         : $Revision: 1.5 $

    $Log: dashboard_assetVerdeling_functies.php,v $
naar RVV 2021-12-13

*/

include_once "../../config/applicatie_functies.php";
include_once("../../html/rapport/rapportRekenClass_minimal.php");

$path = explode("html/",getcwd());
$airsRoot = $path[0];

$basisKleuren = array(
  "ff0000", // rood
  "00ff00", // groen
  "ff8000", // oranje
  "0000ff", // blauw
  "ffff00", // geel
  "ff00ff", // paars
  "808080", // grijs
  "804040", // bruin
  "ff4040",
  "40ff40",
  "4040ff",
  "ffff40",
  "ff40ff",
  "40ffff",
  "ff8080",
  "80ff80",
  "8080ff",
  "ffff80",
  "ff80ff",
  "80ffff",
);

function getASSETHCvalues($apiCall = false)
{
  global $portefeuille, $USR, $_ATT, $jsATT, $divATT, $airsRoot;
  global $basisKleuren;
  $clrArray = generateColorSet($apiCall);

  include_once $airsRoot."/classes/AE_cls_formatter.php";

  $frmt = new AE_cls_formatter(",",".");
  if ($apiCall)
  {
    $datum = getLaatsteValutadatum();
    $w = getPortefeuilleWaarde($portefeuille, $datum);
  }
  else
  {
    $w = getPortefeuilleWaarde($portefeuille);
  }


  $portefeuilleWaarde = $w["portWaarde"];
  $_ATT = $w["data"];
  $_INFO = $w["info"];
  $hcArray = array();
  foreach ($_INFO as $item)
  {
    $hcArray[$item["hCat"]] = $item["hCatOms"];
  }
  $ndx = 0;
  $divATT = "";
  $negativeFound = false;
  $legendRaw = array();
  $hc = array();
  foreach ($_ATT as $k=>$v)
  {

    $cat = (trim($hcArray[$k]) != "")?$hcArray[$k]:"Geen";

    foreach($v as $item)
    {
      if (trim( $clrArray[$k]) == "")
      {
        $klr = $basisKleuren[$ndx];
        $ndx++;
      }
      else
      {
        $klr = $clrArray[$k];
      }
      $hc[$k]["kleur"] = $klr;
      $hc[$k]["waarde"] += $item["waarde"];
      $hc[$k]["percentage"] += $item["percent"];
      $hc[$k]["omschrijving"] = $cat;
      $hc[$k]["categorie"] = $k;
      $hc[$k]["catVolg"] = $item["catVolg"];
      $hc[$k]["hCatVolg"] = $item["hCatVolg"];


    }


  }
  foreach ($hc as $item)
  {


    $divATT .= "\n\t<li><span style='color:#".$item["kleur"]."; font-size: 1.2em;' >&#9729;</span> ".$item["omschrijving"].": ".$frmt->format("@N{.1}", $item["percentage"])."% </li>";
    $jsATT .= "\n{  y: ".$item["percentage"].", legendText:'".$item["omschrijving"]." (".$frmt->format("@N{.1}", $item["percentage"])."%)<br/>&euro; ".$frmt->format("@N{.0}", $item["waarde"])."', color:'#".$item["kleur"]."' },";

    $index = ($item["hCatVolg"]*10000) + $item["catVolg"];
    $legendRaw[$index][] = array(
      "categorie"    => $item["categorie"],
      "kleur"        => $item["kleur"],
      "percentage"   => $item["percentage"],
      "omschrijving" => $item["omschrijving"],
      "waarde"       => $frmt->format("@N{.0}", $item["waarde"]),
      "catVolg"      => $item["catVolg"],
      "hCatVolg"     => $item["hCatVolg"]

    );
    if ($item["percentage"] < 0)
    {
      $negativeFound = true;
    }
  }
  krsort($legendRaw);
//  print_r($legendRaw);
  return array( $divATT, $jsATT, $negativeFound, $legendRaw);
  //$divATT = substr($divATT,10)."</li>";

}


function getASSETvalues($apiCall = false)
{
  global $portefeuille, $USR, $_ATT, $jsATT, $divATT, $airsRoot;
  global $basisKleuren;
  $clrArray = generateColorSet($apiCall);

  include_once $airsRoot."/classes/AE_cls_formatter.php";

  $frmt = new AE_cls_formatter(",",".");
  if ($apiCall)
  {
    $datum = getLaatsteValutadatum();
    $w = getPortefeuilleWaarde($portefeuille, $datum);
  }
  else
  {
    $w = getPortefeuilleWaarde($portefeuille);
  }


  $portefeuilleWaarde = $w["portWaarde"];
  $_ATT = $w["data"];
  $_INFO = $w["info"];
  $ndx = 0;
  $divATT = "";
  $negativeFound = false;
  $legendRaw = array();
  foreach ($_ATT as $k=>$v)
  {
//    $hCat = (trim($v["titel"]) != "")?$v["titel"]:"Geen";
//    $divATT .="</ul></li>\n<li>".$hCat." (<b>".round($v["sum"],1)."%</b>) <ul>";
    
    foreach($v as $item)
    {
      if (trim( $clrArray[$item["cat"]]) == "")
      {
        $klr = $basisKleuren[$ndx];
        $ndx++;
      }
      else
      {
        $klr = $clrArray[$item["cat"]];
      }
      $cat = (trim($item["catOms"]) != "")?$item["catOms"]:"Geen";
      //
      $divATT .= "\n\t<li><span style='color:#".$klr."; font-size: 1.2em;' >&#9729;</span> ".$cat.": ".$frmt->format("@N{.1}", $item["percent"])."% </li>";
      $jsATT .= "\n{  y: ".$item["percent"].", legendText:'".$cat." (".$frmt->format("@N{.1}", $item["percent"])."%)<br/>&euro; ".$frmt->format("@N{.0}", $item["waarde"])."', color:'#".$klr."' },";
      $legendRaw[($item["percent"] * 100)][] = array(
        "categorie"    => $k,
        "kleur"        => $klr,
        "percentage"   => $item["percent"],
        "omschrijving" => $cat,
        "waarde"       => $frmt->format("@N{.0}", $item["waarde"]),

      );
      if ($item["percent"] < 0)
      {
        $negativeFound = true;
      }
    }

  }
  krsort($legendRaw);
  return array( $divATT, $jsATT, $negativeFound, $legendRaw);
  //$divATT = substr($divATT,10)."</li>";

}

function dec2hex($int)
{
  return substr("00".dechex((int)$int),-2);
}

function generateColorSet($apiCall = false)
{

  global $basisKleuren, $portefeuille;

  $cycle = 0;
  $VB = "";
  if ($apiCall)
  {
    $db=new DB();
    $query = "
      SELECT
        Vermogensbeheerders.grafiek_kleur,
        Vermogensbeheerders.Vermogensbeheerder
      FROM
        Vermogensbeheerders
      INNER JOIN Portefeuilles ON 
        Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder
      WHERE 
        Portefeuille = '$portefeuille'
     ";

    $tmp = $db->lookupRecordByQuery($query);
    $clr =  unserialize($tmp['grafiek_kleur']);
    $VB = $tmp["Vermogensbeheerder"];

  }
  else
  {
    $clr = unserialize(getVermogensbeheerderField("grafiek_kleur"));
  }

  foreach ((array)$clr["OIB"] as $k=>$v)
  {
    $clrArray[$k] = dec2hex ($v["R"]["value"]).dec2hex ($v["G"]["value"]).dec2hex ($v["B"]["value"]);
  }
  if ($apiCall)
  {
    return $clrArray;
  }

  $db = new DB();
  $vbField = ($VB == "")?getVermogensbeheerderField("Vermogensbeheerder"):$VB;
  $query = "
    SELECT 
      * 
    FROM 
      `KeuzePerVermogensbeheerder` 
    WHERE 
      `vermogensbeheerder` = '".$vbField."' AND
      `categorie` = 'Beleggingscategorien'";

  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $k = $rec["waarde"];
    if (trim($clrArray[$k]) != "000000" )
    {
      $colorTable[$k] = $clrArray[$k];
    }
    else
    {
      $colorTable[$k] = $basisKleuren[$cycle];
      $cycle++;
    }
  }

 return $colorTable;
}

function getPortefeuilleWaarde($portefeuille, $datum="")
{

  global $_GET, $airsRoot;
//  $stop  = $_GET["stop"];

  $stop  = "2017-08-21";
  if ($datum == "")
  {
    $stop  = date("Y-m-d");
  }
  else
  {
    $stop = $datum;
  }

  //$includeFile = $airsRoot."html/rapport/rapportRekenClass.php";

  //include_once($includeFile);

  $waardes = berekenPortefeuilleWaarde($portefeuille, $stop,false,"",$stop);

  foreach ($waardes as $item)
  {
    $portefeuilleWaarde += $item["actuelePortefeuilleWaardeEuro"];
    $catInfo[$item["beleggingscategorie"]] = array(
      "cat"       => $item["beleggingscategorie"],
      "catOms"    => $item["beleggingscategorieOmschrijving"],
      "catVolg"   => $item["beleggingscategorieVolgorde"],
      "hCat"      => $item["hoofdcategorie"],
      "hCatOms"   => $item["hoofdcategorieOmschrijving"],
      "hCatVolg"  => $item["hoofdcategorieVolgorde"]
    );
    $tempArray[$item["beleggingscategorie"]] += $item["actuelePortefeuilleWaardeEuro"];
  }

  foreach ($tempArray as $k => $v)
  {
    $aandeel[$catInfo[$k]["hCat"]][] = array(
      "cat"       => $catInfo[$k]["cat"],
      "hCat"      => $catInfo[$k]["hCat"],
      "catOms"    => $catInfo[$k]["catOms"],
      "hCatOms"   => $catInfo[$k]["hCatOms"],
      "waarde"    => $v,
      "percent"   => round(($v/$portefeuilleWaarde)*100,1),
      "catVolg"   => $catInfo[$k]["catVolg"],
      "hCatVolg"  => $catInfo[$k]["hCatVolg"],
    );
  }

  return array("portWaarde" =>$portefeuilleWaarde ,"data"=>$aandeel, "info" => $catInfo);

}


//// overgenomen uit rapportRekenClass.php
///
///
if (!function_exists("db2jul"))
{


  function db2jul($dbdate = "")
  {
    if ($dbdate == "")
    {
      return -1;
    }
    else
    {
      $jaar = intval(substr($dbdate, 0, 4));
      if ($jaar == 0)
      {
        return 0;
      }

      $maand = substr($dbdate, 5, 2);
      $dag = substr($dbdate, 8, 2);
      $uur = substr($dbdate, 11, 2);
      $min = substr($dbdate, 14, 2);
      $sec = substr($dbdate, 17, 2);
    }

    return mktime($uur, $min, $sec, $maand, $dag, $jaar);
  }
}


if (!function_exists("jul2db"))
{
  function jul2db ($indat=0)
  {
    if ($indat == 0)
    {
      $indat = time();
    }
    return date('Y',$indat) ."-". date('m',$indat) ."-". date('d',$indat) ." ".
      date('H',$indat) .":". date('i',$indat) .":". date('s',$indat) ;
  }
}




?>