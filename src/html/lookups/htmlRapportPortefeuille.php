<?php
include_once("../../config/local_vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");

require("../../config/checkLoggedIn.php");

if (strlen(trim($_GET["term"])) < 2)
{
  exit;
}

header('Content-type: text/plain');
global $__appvar;

$term = trim(strip_tags($_GET['term']));

 $query="SELECT Portefeuilles.Portefeuille, concat(Portefeuilles.Portefeuille,' - ',Portefeuilles.Client) as naam FROM (Portefeuilles, Clienten)
 WHERE Portefeuilles.Einddatum >= NOW() AND
 Portefeuilles.Portefeuille like '%$term%' OR  Portefeuilles.Client like '%$term%' group by  Portefeuilles.Portefeuille ORDER BY Portefeuilles.Client ASC limit 20";

 $db=new DB();
 $db->SQL($query);
 $db->Query();


$tmp=array();

while($data=$db->nextRecord())
{
  $tmp[]=array('id'=>$data['Portefeuille'],'value'=>$data['naam']);
}


echo json_encode($tmp);
?>