<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/08/31 14:35:13 $
 		File Versie					: $Revision: 1.1 $

*/

include_once("wwwvars.php");

if($_GET['vermogensbeheerder']=='')
{
  echo "Geen vermogensbeheerder ontvangen. (cleanup.php?vermogensbeheerder=test)";
  exit;
}

$db = new DB();
$query="SELECT Rekeningen.Rekening, Rekeningen.Portefeuille
FROM Rekeningen
Inner Join Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
WHERE Portefeuilles.Vermogensbeheerder='".$_GET['vermogensbeheerder']."'";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  $rekeningen[$data['Rekening']]=$data['Rekening'];
  $portefeuilles[$data['Portefeuille']]=$data['Portefeuille'];
}

$query="show tables";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord('num'))
  $tables[]=$data[0];

foreach ($tables as $n=>$table)
{
  $query="SHOW COLUMNS FROM $table ";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord('num'))
  {
    $velden[$table][$n]=$data[0];

    if(strtolower($data[0])=='rekening')
    {
      $rekeningTable[$n]['table']=$table;
      $rekeningTable[$n]['veld']=$data[0];
    }

    if(strtolower($data[0])=='portefeuille')
    {
      $portefeuilleTable[$n]['table']=$table;
      $portefeuilleTable[$n]['veld']=$data[0];
    }

    if(strtolower($data[0])=='vermogensbeheerder')
    {
      $vermTable[$n]['table']=$table;
      $vermTable[$n]['veld']=$data[0];
    }
  }
}

foreach ($rekeningTable as $table)
  $queries[$table['table']]="DELETE FROM ".$table['table']." WHERE ".$table['veld']." <> '' AND ".$table['veld']." in('".implode("','",$rekeningen)."')";
foreach ($portefeuilleTable as $table)
  $queries[$table['table']]="DELETE FROM ".$table['table']." WHERE ".$table['veld']." <> '' AND ".$table['veld']." in('".implode("','",$portefeuilles)."')";
foreach ($vermTable as $table)
  $queries[$table['table']]="DELETE FROM ".$table['table']." WHERE ".$table['veld']." =  '".$_GET['vermogensbeheerder']."'";

if($_GET['uitvoeren']=='1')
{
  foreach ($queries as $table=>$query)
  {
    $db->SQL($query);
    if($db->Query())
    {
      echo "Query $table uitgevoerd.<br>\n";ob_flush();
      $db->SQL("OPTIMIZE TABLE `$table`");
      $db->Query();
    }
  }
}
else
{
  echo "De volgende queries worden automatisch aangemaakt. <a href=\"".$PHP_SELF."?vermogensbeheerder=".$_GET['vermogensbeheerder']."&uitvoeren=1\">uitvoeren?</a>";
  listarray($queries);
}

?>