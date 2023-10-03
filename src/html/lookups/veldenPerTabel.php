<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/07/24 06:42:52 $
 		File Versie					: $Revision: 1.4 $

 		$Log: veldenPerTabel.php,v $
 		Revision 1.4  2018/07/24 06:42:52  cvs
 		call 7041
 		
 		Revision 1.3  2015/12/16 17:30:20  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2014/08/09 15:07:13  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2013/07/24 15:57:16  rvv
 		*** empty log message ***
 		

*/
include_once("../../config/local_vars.php");
include_once("../../config/vars.php");
include_once("../../config/auth.php");

require("../../config/checkLoggedIn.php");

if (trim($_GET['query']) == "")
{
  exit;
}

header('Content-type: text/plain');

$results = search();
sendResults($results);


function search()
{
  global $DB,$_GET;
  
  $query  = $_GET['query'];
  
  $object=new $query;
  
  foreach($object->data['fields'] as $veld=>$veldData)
  {
    $results[]=$veld."\t";
  }
  natcasesort($results);
  return $results;

}

function sendResults($results)
{
  foreach($results as $value)
	{
		print $value."\n";
	}
}

?>