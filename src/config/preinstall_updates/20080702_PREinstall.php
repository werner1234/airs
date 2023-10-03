<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/07/09 09:07:59 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: 20080702_PREinstall.php,v $
 		Revision 1.2  2008/07/09 09:07:59  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/07/02 07:27:38  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/05/06 10:18:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:23:57  cvs
 		gebruikerstabel ivm CRM
 		
 		Revision 1.1  2007/09/27 13:35:24  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2007/08/24 11:26:49  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2007/08/24 11:25:17  cvs
 		*** empty log message ***
 		
 
 		
 	
*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");


		 
$tst = new SQLman();

//$tst->changeField("CRM_naw","enOfRekening",array("Type"=>"tinyint(1)","Null"=>false)); 
//$tst->changeField("Vermogensbeheerders","CrmPortefeuilleInformatie",array("Type"=>"tinyint(1)","Null"=>false)); 
$tst->changeField("Transactieoverzicht","Fonds",array("Type"=>"varchar(25)","Null"=>false)); 
$tst->changeField("Clienten","Client",array("Type"=>"varchar(16)","Null"=>false)); 
$tst->changeField("Portefeuilles","Client",array("Type"=>"varchar(16)","Null"=>false)); 
$tst->changeField("GeconsolideerdePortefeuilles","Client",array("Type"=>"varchar(16)","Null"=>false)); 



$queries['Remisiers'] = "  CREATE TABLE `Remisiers` (
  `id` int(11) NOT NULL auto_increment,
  `Remisier` varchar(15) default NULL,
  `Naam` varchar(50) default NULL,
  `Vermogensbeheerder` varchar(10) default NULL,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  PRIMARY KEY  (`id`)
) ";


$DB= new DB();
foreach ($queries as $tabel=>$query)
{
      $testQuery = "SHOW TABLES LIKE '".$tabel."'";
      $DB->SQL($testQuery);
      $DB->Query();
      if($DB->records() == 0)
      {
         $DB->SQL($query);
         $DB->Query();
      }
}


if($DB->QRecords("SELECT id FROM Remisiers") == 0 && $__appvar['master'])
{
  $update = "INSERT INTO 
  Remisiers (Remisier,Vermogensbeheerder,add_date,add_user) 
  SELECT Portefeuilles.Remisier , Portefeuilles.Vermogensbeheerder,NOW(),'Beheer' FROM Portefeuilles GROUP BY Portefeuilles.Remisier";
  $DB->SQL($update);
  $DB->Query();

}







?>