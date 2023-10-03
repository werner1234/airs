<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/04/08 12:49:31 $
 		File Versie					: $Revision: 1.3 $

 		$Log: Menu868289.php,v $
 		Revision 1.3  2019/04/08 12:49:31  cvs
 		call 7629
 		
 		Revision 1.2  2018/09/14 09:39:39  cvs
 		Naar VRY omgeving ter TEST
 		
 		Revision 1.1  2018/06/18 06:53:57  cvs
 		update naar VRY omgeving
 		



*/

///  Airshost VRY //

$klantMenuText = "| ModuleZ |";  // menunaam in bovenbalk

if ($__appvar["moduleZ"] == 1)
{
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
