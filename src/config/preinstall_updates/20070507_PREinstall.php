<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2007/05/07 14:52:26 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20070507_PREinstall.php,v $
 		Revision 1.1  2007/05/07 14:52:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/21 16:10:31  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/12/11 10:58:12  rvv
 		modelportefeuille
 		
 		Revision 1.1  2006/12/07 16:10:48  rvv
 		*** empty log message ***
 		
 	
*/

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$queries[] = 	" CREATE TABLE `FondsenPerVermogensbeheerder` (
  `id` int(11) NOT NULL auto_increment,
  `Vermogensbeheerder` varchar(20) NOT NULL default '',
  `Fonds` varchar(25) NOT NULL default '',
  `add_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `add_user` varchar(10) NOT NULL default '',
  `change_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `change_user` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `Vermogensbeheerder` (`Vermogensbeheerder`),
  KEY `Fonds` (`Fonds`)
) ENGINE=MyISAM ;";

$db = new DB;

for ($a=0; $a < count($queries); $a++)
{
	$db->SQL($queries[$a]);
	if (!$db->Query())
	{
		echo "FOUTMELDING: 20070507 Aanpassen tabel in regel ".$a." mislukt, neem aub contact op met AIRS.";
		//exit;
	}  
}

$tst = new SQLman();

$tst->changeField("Vermogensbeheerders","check_rekeningmutaties",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_categorie",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_sector",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_zorgplichtFonds",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_zorgplichtPortefeuille",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_hoofdcategorie",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_hoofdsector",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_sectorRegio",array("Type"=>"tinyint(4)","NULL"=>false));
$tst->changeField("Vermogensbeheerders","check_sectorAttributie",array("Type"=>"tinyint(4)","NULL"=>false));


?>