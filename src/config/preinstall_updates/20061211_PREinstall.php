<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2006/12/11 10:58:12 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20061211_PREinstall.php,v $
 		Revision 1.1  2006/12/11 10:58:12  rvv
 		modelportefeuille
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


$tst = new SQLman();
$tst->changeField("Portefeuilles","BeheerfeeMinJaarBedrag",array("Type"=>"double", "Null"=>false));
$tst->changeField("Portefeuilles","OptieToestaan",array("Type"=>"tinyint(4)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","OptieTools",array("Type"=>"tinyint(4)","Null"=>false)); 

$queries[] = "CREATE TABLE `ModelPortefeuilles` (
  `id` int(11) NOT NULL auto_increment,
  `Portefeuille` varchar(12) default NULL,
  `Omschrijving` varchar(50) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM; ";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20061206 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
	}  
}

?>