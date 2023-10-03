<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 mei 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2015/12/01 08:58:01 $
    File Versie         : $Revision: 1.1 $
 		
    $Log: reconVerrijkList.php,v $
    Revision 1.1  2015/12/01 08:58:01  cvs
    update 2540, call 4352



 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();




$subHeader     = "";
$mainHeader    = "Verrijken Reconciliatie lijst ";

$editScript = "tijdelijkereconEdit.php";
$allow_add  = true;



$_SESSION[NAV] = "";


$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";


echo template($__appvar["templateContentHeader"],$content);

$query = "
SELECT
	id,
  portefeuille,
  vermogensbeheerder
FROM
	tijdelijkeRecon
WHERE
	tijdelijkeRecon.client = '' AND 
	portefeuille <> ''          AND
  add_user = '$USR'           AND 
  tijdelijkeRecon.fondsCodeMatch = 'Geen AIRS'
GROUP BY 
  portefeuille
  ";
//debug($query);

$db = new DB();
$db2 = new DB();

$db->executeQuery($query);
while ($pRec = $db->nextRecord())
{
  if ($client = getData($pRec))
  {
    $query = "
      UPDATE tijdelijkeRecon SET 
        client = '".$client."'
      WHERE 
        portefeuille = '".$pRec["portefeuille"]."' AND 
        vermogensbeheerder = '".$pRec["vermogensbeheerder"]."'     ";
    $db2->executeQuery($query);
    $modArray[] = array($pRec["portefeuille"], $client, $pRec["vermogensbeheerder"] );
  }  
  
}  
   
?>
<style>
  table{
    padding: 5px;
    border: 1px solid #333;
    width:600px;
  }
   tr:nth-child(odd) {
    background: #F2F2F2;
}

  tr:nth-child(even) {
    background: #FAFAFA;
}
  .head{
    background: #333;
    
  }
  .head td{
    color: white;
  }

</style>
<p>De volgende portefeuilles zijn verrijkt</p>  
<table>
  <tr style="background: #333;" class="head">
    <td>#</td><td>Portefeuille</td><td>Client</td><td>VB</td>
  </tr>

<?
for ($x=0; $x < count($modArray); $x++)
{
  $a = $modArray[$x];
  echo "<tr><td>".($x+1)."<td>".$a[0]."</td><td>".$a[1]."</td><td>".$a[2]."</td></tr>\n";
}
?>
</table>
<br/>
<br/>
<br/>
<a href="tijdelijkereconList.php" >Terug naar Reconciliatie lijst</a>
<?
function getData($rec)
{
  $query = "
SELECT
	Rekeningen.Portefeuille,
	Portefeuilles.Portefeuille,
	Portefeuilles.Client
FROM
	Rekeningen
INNER JOIN 
	Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
WHERE
	(
    Rekeningen.Rekening = '".$rec["portefeuille"]."EUR' OR
    Rekeningen.Rekening = '".$rec["portefeuille"]."MEM'   
	)
  AND
    Portefeuilles.Vermogensbeheerder = '".$rec["vermogensbeheerder"]."'
  ";
 // debug($query);
  $db = new DB();
  if ($clientRec = $db->lookupRecordByQuery($query))
  {
    return $clientRec["Client"];
  }
  else 
  {
    return false;
  }
}

echo template($__appvar["templateRefreshFooter"],$content);
?>