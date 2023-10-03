
<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2015/03/07 17:27:08 $
File Versie					: $Revision: 1.8 $

$Log: bepaalActieveFondsen.php,v $
Revision 1.8  2015/03/07 17:27:08  rvv
*** empty log message ***



*/

include_once("wwwvars.php");
include_once("../classes/bepaalActieveFondsenClass.php");

$actieveFondsen = new bepaalActieveFondsen();
$actieveFondsen->verbose=true;
$actieveFondsen->createTable();
$actieveFondsen->fillTable();
$actieveFondsen->createXls();




?>