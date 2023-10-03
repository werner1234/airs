<?php

if(checkAccess("superapp") || $_SESSION['usersession']['gebruiker']['Gebruikersbeheer'])
{
  $mnu->addItem("stamgegevens_algemeen", "Gebruikers", "url=gebruikerList.php");
}
if(checkAccess("superapp") || $_SESSION['usersession']['gebruiker']['Gebruikersbeheer'])
{

  if ($__appvar["tgc"] == "enabled")
  {
    $mnu->addItem("stamgegevens_algemeen", "ToegangsControle", "submenu=toegangsControle");
    include_once 'mainmenu_toegangsControle.php';
  }
  $mnu->addItem("stamgegevens_algemeen","Wachtwoord beleid","url=wwb_instellingen.php");
}
$mnu->addItem("stamgegevens_algemeen","");

if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
{
  $mnu->addItem("stamgegevens_algemeen", "AFM categorien", "url=afmcategorienList.php");
  $mnu->addItem("stamgegevens_algemeen", "AFM kostensoorten", "url=afmkostensoortenList.php");
  $mnu->addItem("stamgegevens_algemeen", "Applicatie vertaling", "url=appvertalingList.php");
  $mnu->addItem("stamgegevens_algemeen", "Attributiecategorien", "url=attributiecategorienList.php");

  $mnu->addItem("stamgegevens_algemeen", "BB landcodes", "url=bblandcodesList.php");
  $mnu->addItem("stamgegevens_algemeen", "Beurzen", "url=beurzenList.php");
  $mnu->addItem("stamgegevens_algemeen", "Bewaarders", "url=bewaardersList.php");
  if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "FDX" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "VEC")
  {
    $mnu->addItem("stamgegevens_algemeen", "BIC-codes", "url=biccodesList.php");
  }

  $mnu->addItem("stamgegevens_algemeen", "Beleggingscategorien", "url=beleggingscategorieList.php");
  $mnu->addItem("stamgegevens_algemeen", "Beleggingssectoren", "url=beleggingssectorList.php");


  $mnu->addItem("stamgegevens_algemeen", "Depotbanken", "url=depotbankList.php", 0, '', array('objects' => array('Depotbank'), 'pages' => array('extra depotbank info')));
  $mnu->addItem("stamgegevens_algemeen", "Duurzaamcategorien", "url=duurzaamcategorienList.php");

  $mnu->addItem("stamgegevens_algemeen", "Eigenaars", "url=EigenaarsList.php");
  $mnu->addItem("stamgegevens_algemeen", "Emittenten", "url=emittentenList.php");
  $mnu->addItem("stamgegevens_algemeen", "Externequeriecategorie&euml;n", "url=externequerycategorienList.php");

  $mnu->addItem("stamgegevens_algemeen", "Grootboekrekeningen", "url=grootboekrekeningList.php");

  $mnu->addItem("stamgegevens_algemeen", "ISO landen", "url=isolandenList.php");

  if ($ordermoduleAccess > 0)
  {
    $mnu->addItem("stamgegevens_algemeen", "Orderredenen", "url=orderredenenList.php");
  }

  $mnu->addItem("stamgegevens_algemeen", "Rating", "url=ratingList.php");
  $mnu->addItem("stamgegevens_algemeen", "Regio's", "url=regiosList.php");
  $mnu->addItem("stamgegevens_algemeen", "Rendementsheffing", "url=rendementsheffingList.php");

  $mnu->addItem("stamgegevens_algemeen", "Soortovereenkomsten", "url=soortovereenkomstenList.php");

  $mnu->addItem("stamgegevens_algemeen", "TI toewijzing grootboek", "url=importgrootboektoewijzingList.php");
  $mnu->addItem("stamgegevens_algemeen", "Toelichting Stort/Onttr", "url=toelichtingstortonttrList.php");
  if (checkAccess("superapp"))
  {
    $mnu->addItem("stamgegevens_algemeen", "Transactiecodes", "submenu=Transactiecodes");
    include_once 'mainmenu_stamgegevens_algemeen.php';
  }
  $mnu->addItem("stamgegevens_algemeen", "Transactietypes", "url=transactietypeList.php");

  $mnu->addItem("stamgegevens_algemeen", "Vertalingen", "url=vertalingList.php");
  $mnu->addItem("stamgegevens_algemeen", "Valutas", "url=valutaList.php");

  $mnu->addItem("stamgegevens_algemeen", "Zorgplichtcategorien", "url=zorgplichtcategorieList.php");

}


$mnu->addItem("stamgegevens_algemeen", "Begrippencategorieën", "url=begrippencategorieList.php");

