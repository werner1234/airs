<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2013/12/11 11:35:30 $
 		File Versie					: $Revision: 1.1 $

 		$Log: advent_functies.php,v $
 		Revision 1.1  2013/12/11 11:35:30  cvs
 		*** empty log message ***
 		
*/

define("_LF",chr(10));
function getRights($myLevel="")
{
  // dummy functie tbv rechten in filemanager
  return true;
} 

function dbdate2advent($date)
{
  $dateSplit = explode(" ",$date);
  $parts = explode("-",$dateSplit[0]);  //yyyy-mm-dd
  return $parts[1].$parts[2].$parts[0];  //mmddyyyy
}		



?>