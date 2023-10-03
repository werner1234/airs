<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2009/12/13 17:24:46 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: 20091212_PREinstall.php,v $
 		Revision 1.1  2009/12/13 17:24:46  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/11/15 16:51:06  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/10/17 15:43:09  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2009/10/17 13:27:49  rvv
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
$tst->changeField("CRM_naw","prospectStatusChange",array("Type"=>"datetime","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","rekening",array("Type"=>"varchar(20)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","bank",array("Type"=>"varchar(40)","Null"=>false)); 
$tst->changeField("Vermogensbeheerders","check_module_FACTUURHISTORIE",array("Type"=>"tinyint(4)","Null"=>false)); 

$db=new DB();
$query="SHOW index FROM Clienten";
$db->SQL($query);
$db->Query();
$indexFields=array();
while($data=$db->nextRecord())
  $indexFields[]=$data['Column_name'];
  
if(!in_array('Client',$indexFields))
{
  $query="CREATE INDEX Client ON Clienten (Client)";
  $db->SQL($query);
  $db->Query();
} 



?>