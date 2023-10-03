<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/06/30 12:52:03 $
 		File Versie					: $Revision: 1.2 $

 		$Log: 20111022_PREinstall.php,v $
 		Revision 1.2  2012/06/30 12:52:03  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2011/10/23 13:29:57  rvv
 		*** empty log message ***

 		Revision 1.1  2011/08/07 09:06:42  rvv
 		*** empty log message ***

*/
include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("CRM_naw_adressen","geboortedatum",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
$tst->changeField("CRM_naw_adressen","verjaardagLijst",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("CRM_naw_kontaktpersoon","geboortedatum",array("Type"=>"date","Null"=>false,'Default'=>'default \'0000-00-00\''));
$tst->changeField("CRM_naw_kontaktpersoon","verjaardagLijst",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("CRM_naw","bijeenkomstinteresse",array("Type"=>"tinyint(4)","Null"=>false,'Default'=>'default \'0\''));
$tst->changeField("CRM_evenementen","evenement",array("Type"=>"varchar(50)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw_adressen","evenement",array("Type"=>"varchar(50)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("CRM_naw","profielVastgoed",array("Type"=>"varchar(1)","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("Gebruikers","emailHandtekening",array("Type"=>"mediumtext","Null"=>false,'Default'=>'default \'\''));
$tst->changeField("emailQueue","bccEmail",array("Type"=>"varchar(100)","Null"=>false,'Default'=>'default \'\''));

$tst->tableExist("CRM_uur_activiteiten",true);
$tst->changeField("CRM_uur_activiteiten","code",array("Type"=>"varchar(4)","Null"=>false));
$tst->changeField("CRM_uur_activiteiten","omschrijving",array("Type"=>"varchar(50)","Null"=>false));
$tst->tableExist("CRM_uur_registratie",true);
$tst->changeField("CRM_uur_registratie","wn_code",array("Type"=>"varchar(10)","Null"=>false));
$tst->changeField("CRM_uur_registratie","datum",array("Type"=>"date","Null"=>false));
$tst->changeField("CRM_uur_registratie","deb_id",array("Type"=>"int","Null"=>false));
$tst->changeField("CRM_uur_registratie","act_id",array("Type"=>"int","Null"=>false));
$tst->changeField("CRM_uur_registratie","tijd",array("Type"=>"double","Null"=>false));
$tst->changeField("CRM_uur_registratie","memo",array("Type"=>"text","Null"=>false));
$tst->changeField("CRM_uur_registratie","verwerkt",array("Type"=>"tinyint","Null"=>false));


$db= new DB();
$db->SQL("show fields FROM  CRM_naw");
$db->query();
$queries=array();
while($data=$db->NextRecord())
{
   if($data['Field'] == 'SofiNr')
     $queries[]="ALTER TABLE CRM_naw CHANGE  SofiNr BSN  varchar(15) NOT NULL default ''";
    if($data['Field'] == 'part_SofiNr')
     $queries[]="ALTER TABLE CRM_naw CHANGE  part_SofiNr part_BSN  varchar(15) NOT NULL default ''";
    if($data['Field'] == 'afgifteID')
     $queries[]="ALTER TABLE CRM_naw CHANGE  afgifteID IdGeldigTot  date NOT NULL default '0000-00-00'";
    if($data['Field'] == 'part_afgifteID')
     $queries[]="ALTER TABLE CRM_naw CHANGE  part_afgifteID part_IdGeldigTot date NOT NULL default '0000-00-00'";
}


$tst->changeField("CRM_naw","IdGeldigTot",array("Type"=>"date","Null"=>false));
$tst->changeField("CRM_naw","part_IdGeldigTot",array("Type"=>"date","Null"=>false));
$tst->changeField("CRM_naw","part_BSN",array("Type"=>"varchar(15)","Null"=>false));
$tst->changeField("CRM_naw","BSN",array("Type"=>"varchar(15)","Null"=>false));

foreach ($queries as $query)
{
  $db->SQL($query);
  $db->query();
}

