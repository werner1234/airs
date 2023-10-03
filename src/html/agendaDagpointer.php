<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/01/24 17:05:54 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: agendaDagpointer.php,v $
 		Revision 1.1  2010/01/24 17:05:54  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/09/23 08:04:42  cvs
 		eerste commit vanuit simbis 23092008
 		
 		Revision 1.1  2005/11/17 08:09:45  cvs
 		*** empty log message ***
 		
 	
*/

session_start();
if ($single == "1")  $_SESSION['agendaSingle'] = 1;
if ($single == "0")  $_SESSION['agendaSingle'] = 0;

if(empty($_SESSION['agendaDagpointer']))
	$_SESSION['agendaDagpointer'] = mktime();
  
if (isset($richting))
{
	switch ($_SESSION['agendaLayout'])
  {
    case "dag":
      $verschil = 86400;
      break;
    case "week":
      $verschil = 86400 * 7;
      break;
    case "maand";
      $verschil = 86400 * 30;
      break;
  }
	if ($richting == "B")  $verschil = $verschil * -1;
	$_SESSION['agendaDagpointer'] = ($_SESSION['agendaDagpointer'] + $verschil);
}
else
{
  if (isset($spec))
    $_SESSION['agendaDagpointer'] = $spec;
  else
    $_SESSION['agendaDagpointer'] = mktime();
}


if (!isset($_SESSION['agendaLayout']))
{
  $_SESSION['agendaLayout'] = "week";
  header("location: agendaDagpointer.php");
}
session_write_close();

switch ($_SESSION['agendaLayout'])
{
  case "dag":
     header("location: agendaDag.php");
	break;
  case "week":
     header("location: agendaWeek.php");
	break;
  case "maand";
     header("location: agendaMaand.php");
	break;
}
?>