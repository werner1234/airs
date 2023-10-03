<?php 
/* 	
 		Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2005/03/24 15:13:08 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: server.php,v $
 		Revision 1.3  2005/03/24 15:13:08  jwellner
 		no message
 		
*/
$needMysqlClass = true;
include_once("wwwvars.php");

require("../config/jsrsServer.php");

jsrsDispatch( "update" );

function update($data) {
  global $dbRes;
  global $__funcvar;
	
	$action = $data[action];
		
	include($data[updateScript]);
	
	// return = array( true/false, content) ;
	//echo $_error;
	if($result == false)
		return $_error;
	else
		return $result;
}

?>