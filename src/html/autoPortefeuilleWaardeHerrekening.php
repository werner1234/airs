<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/02/26 16:10:59 $
 		File Versie					: $Revision: 1.5 $

 		$Log: autoPortefeuilleWaardeHerrekening.php,v $
 		Revision 1.5  2020/02/26 16:10:59  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/08/21 15:31:44  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2018/12/29 13:59:10  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2018/01/10 16:24:43  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2017/12/20 16:59:57  rvv
 		*** empty log message ***
 		

*/

$disable_auth = true;
$debug=false;
include_once("wwwvars.php");

if($debug==true && function_exists('xhprof_enable'))
  xhprof_enable();

portefeuilleWaardeHerrekening();
logIt('portefeuilleWaardeHerrekening() klaar.');

bepaalSignaleringen($__appvar["bedrijf"]);
logIt('bepaalSignaleringen voor ('.$__appvar["bedrijf"].') klaar');

bepaalSignaleringenStortingen($__appvar["bedrijf"]);
logIt('bepaalSignaleringenStortingen voor ('.$__appvar["bedrijf"].') klaar');

logIt('begin vulPortaal.');
vulPortaal();
logIt('vulPortaal klaar');

if($debug==true && function_exists('xhprof_disable'))
{
  $xhprof_data = xhprof_disable();
  file_put_contents('run_portefeuilleWaardeHerrekening_'.date('Ymd_his'), serialize($xhprof_data));
 // listarray($xhprof_data);
}
?>
