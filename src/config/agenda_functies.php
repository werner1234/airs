<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2010/01/24 17:47:44 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: agenda_functies.php,v $
 		Revision 1.1  2010/01/24 17:47:44  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2008/09/23 08:04:42  cvs
 		eerste commit vanuit simbis 23092008
 		
 		Revision 1.2  2005/11/21 10:08:25  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2005/11/17 08:07:00  cvs
 		*** empty log message ***
 		
 	
*/

function Startweek($indat=0)
{
 //
 // Functie Startweek
 //
 // Door Chris van Santen
 //      24 april 2000
 //
 // Geeft een startdatum van de week terug als juliandate
 //

  if ($indat == 0)
  {
    $indat = time();
  }
	$vanjul = $indat - ((date('w',$indat)-1)*86400);
  $startstr = date('Y',$vanjul)."-".date('m',$vanjul)."-".date('d',$vanjul);
  return db2jul($startstr." 00:00:00");
}

function Stopweek($indat=0)
{
 // 
 // Functie Stopweek
 //
 // Door Chris van Santen
 //      24 april 2000
 //
 // Geeft een stopdatum van de week terug als juliandate
 // 

  if ($indat == 0) 
  { 
    $indat = time(); 
  }
	$vanjul = $indat - ((date('w',$indat)-1)*86400)+(6*86400);
  $stopstr = date('Y',$vanjul)."-".date('m',$vanjul)."-".date('d',$vanjul);


//  $van = (date('d',$indat)-date('w',$indat))+1;
//  if ($van < 10) {$van = "0".$van; }
//  $startstr = date('Y',$indat)."-".date('m',$indat)."-".$van;
//  $indat = (int)db2jul($startstr)+ 604800 - 86400;
//  $tm = (date('d',$indat));
//  $stopstr = date('Y',$indat)."-".date('m',$indat)."-".$tm;
  return db2jul($stopstr." 00:00:00");
}


function Qweek($Qtable,$Qvar,$indat=0)
{
 // 
 // Functie Qweek
 //
 // Door Chris van Santen
 //      24 april 2000
 //
 // Geeft een querystring voor een week terug ma-zo
 //   de ingevoerde datum bepaald de week
 //

  if ($indat == 0) 
  { 
    $indat = time();
  }
	$vanjul = $indat - ((date('w',$indat)-1)*86400);
  $startstr = date('Y',$vanjul)."-".date('m',$vanjul)."-".date('d',$vanjul);
    
  $indat = (int)db2jul($startstr)+ 604800 - 86400;
  $tm = (date('d',$indat));
  $stopstr = date('Y',$indat)."-".date('m',$indat)."-".$tm;
 
  $Qstr="SELECT * FROM $Qtable WHERE $Qvar >= '$startstr' AND $Qvar <= '$stopstr' ";
  
  return $Qstr;
}  

function get_sunday_before ( $year, $month, $day ) 
{
  $weekday = date ( "w", mktime ( 0, 0, 0, $month, $day, $year ) );
  $newdate = mktime ( 0, 0, 0, $month, $day - $weekday, $year );
  return $newdate;
}

?>