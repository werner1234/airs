<?php
/*
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2006/06/14 10:21:35 $
 		File Versie					: $Revision: 1.4 $

 		$Log: Menu658484.php,v $



*/

/// ontwikkel AIRS CRMonly  //

$klantMenuText = "Beleggersgiro";  // menunaam in bovenbalk

$mnu->addItem("klantmenu","Importeer client/portefeuille","url=externApi_clientPortefeuilleQueue.php");
$mnu->addItem("klantmenu","Importeer mutaties","url=externApi_mutatieQueue.php");
$mnu->addItem("klantmenu","Wijzig verdeling modelport.","url=modelportefeuillesList.php");
$mnu->addItem("klantmenu","Verwerk dividenden","url=beleggersgiro_divMutaties.php");
$mnu->addItem("klantmenu","Verwerk beheerfee","url=factuurSelectie.php");
$mnu->addItem("klantmenu","Rebalance","url=beleggersgiro_rebalance.php");
$mnu->addItem("klantmenu","Recon Extern","url=beleggersgiro_reconExtern.php");
$mnu->addItem("klantmenu","Recon Intern","url=beleggersgiro_reconIntern.php");
$mnu->addItem("klantmenu","Afvinken mutaties","url=externApi_todo.php");

