<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/29 10:04:44 $
 		File Versie					: $Revision: 1.1 $

 		$Log: Menu777975.php,v $
 		Revision 1.1  2020/07/29 10:04:44  cvs
 		call 8750

    naar RVV 20201102

*/

$klantMenuText = " | MOKA | ";

$mnu->addItem("klantmenu","Import client/portefeuille","url=Mylo/mylo_importClientPortefeuille.php");
$mnu->addItem("klantmenu","Import transacties","url=Mylo/mylo_importTransacties.php");
$mnu->addItem("klantmenu","Import transacties depotbank","url=Mylo/mylo_importTransactiesSelect.php");
$mnu->addItem("klantmenu","Orders importeren voor externe orders","url=externeOrdersImport.php");
$mnu->addItem("klantmenu","Externe Orders","url=externeOrdersList.php");
$mnu->addItem("klantmenu","AFM","url=Mylo/AfmCheck.php");
$mnu->addItem("klantmenu","Interne Recon","url=Mylo/reconInternExtern.php");
$mnu->addItem("klantmenu","Recon depotbank","url=Mylo/mylo_reconSelect.php");
$mnu->addItem("klantmenu","Controle Stukkenverkoop","url=Mylo/mylo_verkoopControle.php?action=stukken");
$mnu->addItem("klantmenu","Controle Cashverkoop","url=Mylo/mylo_verkoopControle.php?action=geld");





