<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 mei 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/19 09:03:31 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: reconFillOpmerking.php,v $
    Revision 1.2  2018/10/19 09:03:31  cvs
    call 7167

    Revision 1.1  2018/09/23 17:14:23  cvs
    call 7175





 	
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
	tijdelijkeRecon.id,
	tijdelijkeRecon.portefeuille,
	tijdelijkeRecon.rekeningnummer,
	tijdelijkeRecon.verschil,
	CASE WHEN Portefeuilles.Einddatum < now( ) THEN
		'Ptf Vervallen' 
	ELSE
		CASE WHEN tijdelijkeRecon.verschil = 0 THEN
				'Akkoord' 
		ELSE
			CASE WHEN tijdelijkeRecon.depotbank IN ( 'TGB', 'FVL' )  AND Fondsen.fondssoort IN ( 'OBL', 'OVERIG' ) AND ( ( ( tijdelijkeRecon.positieBank * 1000 ) - tijdelijkeRecon.positieAirs ) = 0 ) THEN
				'Akk TGB Factor' 
			ELSE
				CASE WHEN tijdelijkeRecon.cashPositie = 1  AND abs( tijdelijkeRecon.verschil ) < 0.5 THEN
					'CashKlein' ELSE '' 
				END 
			END 
		END 
	END AS 'Opmerking' 
FROM
	tijdelijkeRecon
LEFT JOIN Portefeuilles ON 
	tijdelijkeRecon.Portefeuille = Portefeuilles.Portefeuille
LEFT JOIN Fondsen ON 
	tijdelijkeRecon.fonds = Fondsen.Fonds
WHERE tijdelijkeRecon.add_user = '$USR'	
  ";
//debug($query);

$db = new DB();
$db2 = new DB();

$db->executeQuery($query);
while ($pRec = $db->nextRecord())
{
    $query = "
      UPDATE tijdelijkeRecon SET 
        Opmerking = '".$pRec["Opmerking"]."'
      WHERE 
        id = ".$pRec["id"];

    $db2->executeQuery($query);
    $modArray[$pRec["Opmerking"]]++;
  
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
.ac{
  text-align: center;
}
</style>
<p>De volgende portefeuilles zijn verrijkt</p>

<table>
  <tr style="background: #333;" class="head">
    <td>status</td><td>aantal</td>
  </tr>

<?
foreach ($modArray as $k=>$v)
{
  $k = ($k != "")?$k:"Met verschillen";
  echo "<tr><td>".($k)."</td><td class='ac'>".$v."</td></tr>\n";
}
?>
</table>
<br/>
<br/>
<br/>
<a href="tijdelijkereconList.php" >Terug naar Reconciliatie lijst</a>
<?

echo template($__appvar["templateRefreshFooter"],$content);
?>