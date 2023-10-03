<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/09/13 13:34:56 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20100806_PREinstall.php,v $
 		Revision 1.1  2010/09/13 13:34:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/08/04 15:22:36  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/07/31 16:10:42  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/07/25 14:34:30  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/28 15:50:26  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/04/24 19:15:41  rvv
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();
$tst->changeField("dd_reference","filename",array("Type"=>"varchar(60)","Null"=>false)); 
$tst->changeField("dd_reference","filesize",array("Type"=>"int(11)","Null"=>false)); 
$tst->changeField("dd_reference","filetype",array("Type"=>"varchar(50)","Null"=>false)); 
$tst->changeField("dd_reference","categorie",array("Type"=>"varchar(60)","Null"=>false)); 

$query="SHOW TABLES LIKE 'dd_datastore%'";
$db=new DB();
$db2=new DB();
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord('num'))
{
  $table=$data[0];
  $query="UPDATE dd_reference JOIN $table ON dd_reference.id=$table.referenceId SET 
  dd_reference.filename=$table.filename,
  dd_reference.filesize=$table.filesize,
  dd_reference.filetype=$table.filetype ";
  $db2->SQL($query);
  $db2->Query();
}

$DB = new DB();
$query="show variables like 'character_set_database'";
$DB->SQL($query);
$charset=$DB->LookupRecord();
$charset=$charset['Value'];
if($charset != '')
{
  $query="SHOW tables";
  $DB = new DB();
  $DB2 = new DB();
  $DB->SQL($query);
  $DB->Query();
  while($data=$DB->nextRecord('num'))
  {
    $table=$data[0];
    $query="ALTER TABLE $table CONVERT TO CHARACTER SET $charset";
    $DB2->SQL($query);
    $DB2->Query();
  }
}



?>