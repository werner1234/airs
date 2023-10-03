<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.11 $

 		$Log: ajaxLookup.php,v $
 		Revision 1.11  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.10  2018/08/15 09:17:07  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2018/07/24 06:38:40  cvs
 		call 7041
 		
 		Revision 1.8  2016/02/18 05:16:24  rvv
 		*** empty log message ***
 		
 

*/
include_once("../../config/local_vars.php");
include_once("../../config/vars.php");
include_once("../../config/applicatie_functies.php");
include_once("../../classes/AE_cls_mysql.php");
if (!isset($_SESSION))
{
  session_start();
}
//if (!isset($_SESSION["USR"]))
//{
////  header("HTTP/1.0 404 Not Found");
////  exit;
//}

if($__appvar["bedrijf"] == "VMA")
{
  $_DB_resources[1]['db'] = $_SESSION["DATABASE"];
}  
$DB = new DB();


/** add new ajax lookup functionality **/
$data = array_merge($_POST, $_GET);

if ( isset($data['fromClass']) ) {
  $data['ajaxClassCall'] = true;
  if (class_exists($data['fromClass'])) {
    $fromClass = new $data['fromClass'] ();
  }
  if (
    method_exists($fromClass, $data['type'])
    && is_callable(array($fromClass, $data['type'])))
  {
      call_user_func(array(
        $fromClass, 
        $data['type']
      ));
      exit();
  } else {
    exit('Not found');
  }
}
/** end **/

header('Content-type: text/plain');

if(!isset($search_queries))
  $search_queries='';

$results = search($search_queries);
sendResults($results);


function search($search_queries)
{
  global $DB,$_GET;
  
  $query  = $_GET['query'];
  $module = $_GET['module'];
  $output  = ( isset ($_GET['output']) ? $_GET['output'] : '' );
  
	if (strlen($query) == 0 OR $module == "")
		return;

  $search = trim($query);
  $searchParts=explode("|",$search);
  include_once($module.".php");
  // in include staan 2 variabelen
  // $theQuery is de searchQuery
  // $velden = array met velden die teruggegeven worden

  $DB->SQL($theQuery);
  if(!$DB->Query())
  {  
    echo "Query mislukt.".$DB->errorstr."\n";
    exit;
  }
  $resultaten = array();
  if($velden[0]=='num')
  {
    while ( $rec = $DB->nextRecord('num') )
    {
      $row='';
      foreach ($rec as $veld)
        $row .= $veld."\t";
      $resultaten[] = $row;
    }
  }
  else
  {
    if ( $output === 'json' )
    {
      $returnJson = array();
      while ( $rec = $DB->nextRecord() )
      {
        $returnJson[] = $rec;
      }
      echo json_encode($returnJson);
      exit();
    }
    
    while ( $rec = $DB->nextRecord() )
    {
      $row = $rec[ $velden[0] ];
      for ($q=1 ; $q < count($velden);$q++)
        $row .= "\t".$rec[ $velden[$q] ];
      $resultaten[] = $row."\t";
    }
  }
  return $resultaten;

}

function sendResults($results)
{
	for ($i = 0; $i < count($results); $i++)
	{
		print "$results[$i]\n";
	}
}

?>