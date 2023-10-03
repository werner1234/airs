<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
 		File Versie					: $Revision: 1.1 $

 		$Log: Menu657879.php,v $
 		Revision 1.1  2018/09/23 17:14:23  cvs
 		call 7175
 		



*/

///  DEMO.AIRSHOST.NL //

$klantMenuText = "| Frank |";  // menunaam in bovenbalk

// item in klantmenu
//$mnu->addItem("klantmenu","Fonds Recon inlezen","url=fondsRecon.php");
//$mnu->addItem("klantmenu","tijdelijke fondsrecon","url=tijdelijkefondsreconList.php",1);
//$mnu->addItem("klantmenu","batch recon jobmanager","url=recon/batch_jobManager.php");
//$mnu->addItem("klantmenu","batch recon job toevoegen","url=recon/batch_jobAdd.php");
//$mnu->addItem("klantmenu","Zoek/Vervang list","url=importzoekvervangList.php");
//$mnu->addItem("klantmenu","Klanten dBase","url=http://10.171.122.203:91/index.php",0,"_blank");
$mnu->addItem("klantmenu","taken import","url=taken_ImportFase1.php",1);
$mnu->addItem("klantmenu","handelzeker API","url=getSanctieInfo.php",1);
?>