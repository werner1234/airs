<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2018/09/07 10:08:58 $
 		File Versie					: $Revision: 1.1 $

 		$Log: Menu846983.php,v $
 		Revision 1.1  2018/09/07 10:08:58  cvs
 		menu voor test robert
 		



*/

/// ontwikkel Chris GDM  //

$klantMenuText = "| VRY |";  // menunaam in bovenbalk

// item in klantmenu
//if ($__appvar["moduleZ"] == 1)
{

//  $mnu->addItem("klantmenu","Bedrijf-depotbank","url=MONITOR_bedrijfDepotList.php");
//  $mnu->addItem("klantmenu","Importstatus","url=MONITOR_importMatrixList.php");
//  $mnu->addItem("klantmenu","Importvoortgang","url=MONITOR_voortgang.php");
//  $mnu->addItem("klantmenu","<hr/>","url=#");

  $mnu->addItem("klantmenu","Diverse data ophalen","url=moduleZ_getMisc.php",1);

  $mnu->addItem("klantmenu","");
  $mnu->addItem("klantmenu","Nieuwe klant","url=moduleZ_CRM_nawEdit.php",1);
  $mnu->addItem("klantmenu","Nieuwe rekening","url=moduleZ_nieuweRekeningEdit.php",1);

  $mnu->addItem("klantmenu","");
  $mnu->addItem("klantmenu","Transfersaldo's ophalen","url=moduleZ_getHandelTransfer.php",1);
  $mnu->addItem("klantmenu","Rebalance ophalen","url=moduleZ_getHandelRebalance.php",1);
  $mnu->addItem("klantmenu","Tijdelijke bulkorders","url=tijdelijkebulkordersv2List.php",1);
  $mnu->addItem("klantmenu","Verzenden nota's","url=orderregelsNotaListV2.php",1);

  $mnu->addItem("klantmenu","");
  $mnu->addItem("klantmenu","ModuleZ transacties ophalen","url=import/moduleZ_getTransactions.php");
  $mnu->addItem("klantmenu","ModuleZ posities ophalen","url=recon/moduleZ_getPositions.php",1);

  $mnu->addItem("klantmenu","");
  $mnu->addItem("klantmenu","Api log","url=moduleZ_api_loggingList.php",1);
  $mnu->addItem("klantmenu","AirsKoppelingen","url=airsKoppelingenList.php",1);
  $mnu->addItem("klantmenu","Batchlijst","url=modulezTijdelijkeBatchList.php",1);
  $mnu->addItem("klantmenu","Upload raportages","url=modulezUploadRapportages.php",1);

}
