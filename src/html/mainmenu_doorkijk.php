<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/12/04 13:31:28 $
    File Versie         : $Revision: 1.3 $

    $Log: mainmenu_doorkijk.php,v $
    Revision 1.3  2017/12/04 13:31:28  cvs
    call 6349

    Revision 1.2  2017/12/04 11:01:01  cvs
    call 6349

    Revision 1.1  2017/12/04 10:55:20  cvs
    Update van Ben ingelezen dd 4-12-2017



*/
// --- Doorkijk project
if($_SESSION['usersession']['gebruiker']['Beheerder'] == 1)
{
$mnu->addItem("stamgegevens_doorkijk","Doorkijk import","url=doorkijk_morning_import.php");
}

$mnu->addItem("stamgegevens_doorkijk","Wegingen per fonds", "url=doorkijk_categorieWegingenPerFondsList.php");
$mnu->addItem("stamgegevens_doorkijk","DoorkijkCategorie per vermogensbeheerder", "url=doorkijk_categoriePerVermogensbeheerderList.php");
$mnu->addItem("stamgegevens_doorkijk","Provider catergorie soort", "url=doorkijk_msCategoriesoortList.php");
$mnu->addItem("stamgegevens_doorkijk","Koppeling per vermogensbeheerder", "url=doorkijk_koppelingPerVermogensbeheerderList.php");


// --- einde Doorkijk project
