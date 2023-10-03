<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/06/09 15:56:13 $
 		File Versie					: $Revision: 1.2 $

 		$Log: autoExterneQuery.php,v $
 		Revision 1.2  2018/06/09 15:56:13  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2018/05/19 16:22:32  rvv
 		*** empty log message ***
 		
 	

*/

$disable_auth = true;
include_once("wwwvars.php");
include_once("externequerierun.php");
$export= new externeQueryRun();
$ids=$export->getAutorunJobs();
logIt("Aantal externe queries:".count($ids));
foreach($ids as $id)
	$export->sendXlsEmail($id);

logIt("bepaalActieveFondsen");
include_once("../classes/bepaalActieveFondsenClass.php");
$actieveFondsen = new bepaalActieveFondsen();
$actieveFondsen->verbose=false;
$actieveFondsen->createTable();
$actieveFondsen->fillTable();

        
?>
