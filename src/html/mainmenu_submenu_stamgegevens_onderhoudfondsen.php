<?php

/*
    AE-ICT sourcemodule created 04 apr. 2022
    Author              : Chris van Santen
    Filename            : mainmenu_submenu_stamgegevens_onderhoudfondsen.php


*/

$mnu->addItem("stamgegevens_onderhoudfondsen", "Fondsen", "url=fondsList.php");

if ($__appvar["bedrijf"] == "HOME")// && $__appvar["bedrijf"] != "TEST"
{
  $mnu->addItem("stamgegevens_onderhoudfondsen", "Fondsaanvragen", "url=fondsaanvragenList.php?filterNew=1");
}
elseif ($_SESSION['usersession']['gebruiker']['fondsaanvragenAanleveren'] == 1)
{
  $mnu->addItem("stamgegevens_onderhoudfondsen", "Fondsaanvraag", "url=fondsaanvragenEdit.php?action=new");
}
$mnu->addItem("stamgegevens_onderhoudfondsen", "");

$mnu->addItem("stamgegevens_onderhoudfondsen", "Beleggingscategorien per fonds", "url=beleggingscategorieperfondsList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Beleggingssectoren per fonds", "url=beleggingssectorperfondsList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Benchmarkverdeling", "url=benchmarkverdelingList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Benchmarkverdeling vanaf", "url=benchmarkverdelingVanafList.php");


$mnu->addItem("stamgegevens_onderhoudfondsen", "Extra informatie", "url=fondsextrainformatieList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Extra trekvelden", "url=fondsextratrekveldenList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Extra velden", "url=fondsextraveldenList.php");

if ($ms->allowed(2, 4))  // call 7630
{
  $mnu->addItem("stamgegevens_onderhoudfondsen", "Fonds EMT-data ", "url=FondsenEMTdataList.php");
}

if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "TEST")
{
  $mnu->addItem("stamgegevens_onderhoudfondsen", "Fonds factor", "url=factorvanafdatumList.php");
}
if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "TEST")
{
  $mnu->addItem("stamgegevens_onderhoudfondsen", "Fonds (koers) parameter import", "url=fondsParameterImport.php");
}
$mnu->addItem("stamgegevens_onderhoudfondsen", "Fonds kosten", "url=fondskostenList.php");

$mnu->addItem("stamgegevens_onderhoudfondsen", "Fondsen per emittent", "url=emittentperfondsList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Fondsomschrijving vanaf datum", "url=fondsomschrijvingvanafList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Fondsparameters historie", "url=fondsparameterHistorieList.php");

$mnu->addItem("stamgegevens_onderhoudfondsen", "Fund Informatie", "url=fondsenfundinformatieList.php");

$mnu->addItem("stamgegevens_onderhoudfondsen", "Optiestatistieken ", "url=fondsenoptiestatistiekenList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Optiesymbolen", "url=optieSymbolList.php");

if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "FDX" || $__appvar["bedrijf"] == "VEC")
{
  $mnu->addItem("stamgegevens_onderhoudfondsen", "PSAF per fonds", "url=psafperfondsList.php");
}

$mnu->addItem("stamgegevens_onderhoudfondsen", "Rentekalender Obligaties", "url=rentekalenderObligaties.php");
$mnu->addItem("stamgegevens_onderhoudfondsen", "Rentepercentages per fonds", "url=rentepercentageList.php");

$mnu->addItem("stamgegevens_onderhoudfondsen", "Turbosymbolen", "url=turboSymbolList.php");

if ($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
{
  $mnu->addItem("stamgegevens_onderhoudfondsen", "Vul ontbrekende categorieën", "url=categorieFondsKoppelen.php");
}


$mnu->addItem("stamgegevens_onderhoudfondsen", "Zorgplicht per fonds", "url=zorgplichtperfondsList.php");

