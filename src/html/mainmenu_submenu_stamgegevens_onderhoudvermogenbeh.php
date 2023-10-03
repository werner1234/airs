<?php

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Vermogensbeheerders", "url=vermogensbeheerderList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Accountmanagers", "url=accountmanagerList.php",1);
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Attributie per grootboekrekening", "url=attributiepergrootboekrekeningList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Begrippenrapport", "url=begrippenrapportList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Beleggingscategorien per hoofdcategorie", "url=categorienperhoofdcategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Beleggingscategorien per vermogensbeheerder", "url=categorienpervermogensbeheerderList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Beleggingssectoren per hoofdsector", "url=sectorperhoofdsectorList.php");
//$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Beleggingscategorien per wegingscategorie","url=beleggingscategorieperwegingscategorieList.php");
if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "FDX" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "VEC")
{
  $mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Brokerinstructies", "url=brokerinstructiesList.php");
}

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Contractuele controles/restricties", "url=contractueleuitsluitingenList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Eigen rapport teksten", "url=custom_txtList.php?type=rapport");

if ($ordermoduleAccess >0 && ($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0))
{

  if ($ordermoduleAccess == 2 || $__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST")
  {
    $mnu->addItem("stamgegevens_onderhoudvermogenbeh","Fix depotbanken per vermogensbeheerder","url=fixdepotbankenpervermogensbeheerderList.php");
  }
}

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Fondsen buiten beheerfee/huisfonds", "url=fondsenbuitenbeheerfeeList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Geautomatiseerde rapporten", "url=autorunList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Grootboek nummers", "url=grootboeknummersList.php");
//$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Grootboekrekening per vermogensbeheerder","url=grootboekpervermogensbeheerderList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Index per AttributieCategorie", "url=indexperattributiecategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Index per Beleggingscategorie", "url=indexperbeleggingscategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Indices per vermogensbeheerder", "url=indiceList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Inflatiepercentages", "url=inflatiepercentagesList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Keuze per vermogensbeheerder", "url=keuzepervermogensbeheerderList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Korting per depotbank", "url=kortingenperdepotbankList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Norm per risicoprofiel", "url=normperrisicoprofielList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Orderkosten", "url=orderkostenList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Parameters per vermogensbeheerder", "url=parameterspervermogensbeheerderList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Referentieportefeuille per beleggingscategorie", "url=referentieportefeuilleperbeleggingscategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Remisiers", "url=remisiersList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Risicoklassen", "url=risicoklassenList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Scenarios per vermogensbeheerder", "url=scenariospervermogensbeheerderList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Scenario instellingen", "url=scenarioinstellingenList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Standaarddeviatie per risicoklasse", "url=standaarddeviatieperrisicoklasseList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Valuta per regio", "url=valutaperregioList.php");

$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Zorgplicht per risicoklasse", "url=zorgplichtperrisicoklasseList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh", "Zorgplicht per beleggingscategorie", "url=zorgplichtperbeleggingscategorieList.php");








