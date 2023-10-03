<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/07/02 07:28:57 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: CRM_Copy.php,v $
 		Revision 1.3  2008/07/02 07:28:57  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2008/06/30 06:53:04  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/22 13:34:23  rvv
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
$db = new DB();
$query = "SELECT portefeuille FROM CRM_naw ";
$db->SQL($query);
$db->Query();
while($data = $db->nextRecord())
 $bestaande[]=$data['portefeuille'];

$query = "SELECT `Portefeuilles`.`Portefeuille`,`Clienten`.`Client`,`Clienten`.`Naam`,`Clienten`.`Naam1`,`Clienten`.`Adres`,
                 `Clienten`.`Woonplaats`,`Clienten`.`Telefoon`,`Clienten`.`Fax`,`Clienten`.`Email` 
          FROM   `Portefeuilles`
          JOIN   `Clienten` ON `Portefeuilles`.`Client` = `Clienten`.`Client`
          WHERE   portefeuille NOT IN('".implode('\',\'',$bestaande)."') AND Portefeuilles.Einddatum > NOW()";
$db->SQL($query);
$db->Query();
while($data = $db->nextRecord())
{
  $nieuwePortefeuilles[]=$data;
}

echo count($bestaande)." portefeuilles gevonden in CRM_naw. <br>";
echo count($nieuwePortefeuilles)." portefeuilles gevonden welke nog toegevoegd moeten worden. <br><br>";


if($_GET['actie']=='verwerk')
{
  $x=0;
  foreach ($nieuwePortefeuilles as $data)
  {
    $query = "INSERT INTO CRM_naw SET
    portefeuille = '".$data['Portefeuille']."',
    naam = '".addslashes($data['Naam'].' '.$data['Naam1'])."',
    adres = '".addslashes($data['Adres'])."',
    plaats = '".addslashes($data['Woonplaats'])."',
    tel1 = '".$data['Telefoon']."',
    fax =  '".$data['Fax']."',
    email = '".addslashes($data['Email'])."',
    aktief = '1',
    debiteur = '1',
    memo = 'toegevoegd via portefeuillelijst',
    add_date = '".date('Y-m-d')."',
    add_user = 'AIRS',
    change_date = NOW(),
    change_user = 'AIRS'
    ";
    $db->SQL($query);
    if($db->Query())
      $x++;
    else
      echo "Fout in ".$data['Portefeuille']."<br>";
  }
  echo "$x portefeuilles toegevoegd. <br><br>";
}
elseif(count($nieuwePortefeuilles) > 0)
 echo "<a href=\"CRM_Copy.php?actie=verwerk\" >Regels Toevoegen</a>";



?>
