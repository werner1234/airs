<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/05/13 13:23:39 $
    File Versie         : $Revision: 1.5 $

    $Log: api_info_doorkijk.php,v $
    Revision 1.5  2019/05/13 13:23:39  cvs
    call 7630

    Revision 1.4  2019/04/24 14:13:04  cvs
    call 7630

    Revision 1.3  2019/04/08 12:28:49  cvs
    call 7691

    Revision 1.2  2018/10/22 14:20:49  cvs
    call 7228

    Revision 1.1  2018/07/11 10:01:19  cvs
    call 6812



*/




$portefeuille = $__ses["data"]["portefeuille"];
if ($__ses["data"]["rapportDatum"])
{
  $datum = $__ses["data"]["rapportDatum"];
}
else
{
  $datum = date("Y-m-d");
}

$fonds = rawurldecode($__ses["data"]["fonds"]);

$ms = new AE_cls_Morningstar($portefeuille);

/////////////////////////////////////

$msFilter = $ms->doorkijkFilter(2,4);

debugFile("p: $portefeuille  $f: $fonds");
debugFile("MS level: ".$ms->level.", portefeuille: ".$ms->portefeuille);
debugFile(var_export($__ses, true));

$db = new DB();
$query = "
  SELECT 
    msCategoriesoort, 
    date(max(datumVanaf)) as vanaf 
  FROM 
    doorkijk_categorieWegingenPerFonds 
  WHERE 
    fonds = '$fonds' 
  $msFilter
  GROUP BY 
    msCategoriesoort";
debugFile($query);
$db->executeQuery($query);

$categorieSoortVanaf = array();
while ($doorkijk = $db->nextRecord())
{
  $categorieSoortVanaf[$doorkijk['vanaf']][] = $doorkijk['msCategoriesoort'];
}

foreach ($categorieSoortVanaf as $vanaf => $categorieSoorten)
{

  $query = "
    SELECT 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort,
      doorkijk_categorieWegingenPerFonds.msCategorie,
      doorkijk_categorieWegingenPerFonds.weging,
      doorkijk_categorieWegingenPerFonds.datumVanaf,
      doorkijk_msCategoriesoort.omschrijving,
      doorkijk_msCategoriesoort.grafiekKleur
    FROM 
      doorkijk_categorieWegingenPerFonds 
    LEFT JOIN doorkijk_msCategoriesoort ON 
      doorkijk_categorieWegingenPerFonds.msCategorie=doorkijk_msCategoriesoort.msCategorie AND 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort=doorkijk_msCategoriesoort.msCategoriesoort
    WHERE 
      doorkijk_categorieWegingenPerFonds.fonds='$fonds'       AND 
      doorkijk_categorieWegingenPerFonds.datumVanaf>='$vanaf' AND 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort IN ('" . implode("','", $categorieSoorten) . "')
    ORDER BY 
      doorkijk_categorieWegingenPerFonds.msCategoriesoort,
      doorkijk_categorieWegingenPerFonds.msCategorie,
      doorkijk_categorieWegingenPerFonds.datumVanaf
      ";


  $db->executeQuery($query);

  $wegingTotaal = 0;
  $doorkijkCatNew = 1;
  $firstLoop = 1;

  while ($doorkijk = $db->nextRecord())
  {
    list($r, $g, $b) = unserialize($doorkijk['grafiekKleur']);
    $doorkijk['grafiekKleur'] = sprintf('%.0f, %.0f, %.0f', $r, $g, $b);

    $msOmschrijving = ($doorkijk['omschrijving'] <> '')?$doorkijk['omschrijving']:$doorkijk['msCategorie'];

    $doorkijk['msOmschrijving'] = $msOmschrijving;
    $doorkijkArray[$doorkijk['msCategoriesoort']][] = $doorkijk;
  }
}
if ( empty ($doorkijkArray) )
{
  echo 'Geen gegevens beschikbaar.';
}
else
{


  $output = array();

  //
  foreach ( $doorkijkArray as $msCategoriesoort => $doorkijkDatas )
  {
    $wegingTotaal = 0;

  $output[$msCategoriesoort]["msCategoriesoort"] = $msCategoriesoort;
  $output[$msCategoriesoort]["vanaf"] = $vanaf;
    $count = 1;
    $type = 'pi';
    foreach ( $doorkijkDatas as $doorkijk )
    {
      $wegingTotaal += $doorkijk['weging'];
    }
    $output[$msCategoriesoort]["doorkijkData"] = $doorkijkDatas;
    $output[$msCategoriesoort]["wegingTotaal"] = $wegingTotaal;

  }

}