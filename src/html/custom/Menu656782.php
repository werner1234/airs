<?php
/*
    AE-ICT source module
    Author  						: $Author: jwellner $
 		Laatste aanpassing	: $Date: 2006/06/14 10:21:35 $
 		File Versie					: $Revision: 1.4 $

 		$Log: Menu658484.php,v $



*/

/// ontwikkel AIRS CRMonly  //

if (facmodAccess())
{
  $klantMenuText = "| Facturatie |";  // menunaam in bovenbalk

  if ($__appvar["factuurmodule"] == 1)
  {
    $mnu->addItem("klantmenu","artikelen","url=facmod_artikelList.php",1);
    $mnu->addItem("klantmenu","abonnementen","url=facmod_abonnementList.php?do=all",1);
    $mnu->addItem("klantmenu","niet gefactureerde regels","url=facmod_factuurregelsList.php?do=notinvoicedList");
    $mnu->addItem("klantmenu","factuurbeheer","url=facmod_factuurbeheerList.php",1);
    $mnu->addItem("klantmenu","setup","url=facmod_setupFactuur.php",1);
  }

}





