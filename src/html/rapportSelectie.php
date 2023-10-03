<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2008/05/16 08:09:42 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: rapportSelectie.php,v $
 		Revision 1.3  2008/05/16 08:09:42  rvv
 		*** empty log message ***
 		
 	
*/

//$AEPDF2=true;

include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once("../classes/AE_cls_progressbar.php");

if(!$_POST) // Selectie.
{
  include_once("../classes/mysqlList.php");
  include_once('../classes/rapportSelectieClass.php');
  $selectie = new rapportSelectie();
  $selectie->genereerHTML();
}
else //Afdrukken 
{
  include_once('../classes/AE_cls_fpdf.php');
  include_once('../classes/rapportAfdrukkenClass.php');
  $afdruk = new rapportAfdrukken();
  $afdruk->genereerRapport();

}




?>