<?php

if (checkAccess("superapp"))
{
  $mnu->addItem("koersen", "Koersimport", "url=koersImport.php");
  $mnu->addItem("koersen", "Koersimport AAB", "url=koersImportAAB.php");
  $mnu->addItem("koersen", "");
}


$mnu->addItem("koersen", "Fondskoersen", "url=fondskoersenList.php");

if ($__appvar["bedrijf"] == "HOME")// && $__appvar["bedrijf"] != "TEST"
{
  $mnu->addItem("koersen", "Fondskoersaanvragen", "url=fondskoersaanvragenList.php?filterNew=1");
}
elseif ($_SESSION['usersession']['gebruiker']['fondsaanvragenAanleveren'] == 1)
{
  $mnu->addItem("koersen", "Fondskoersaanvraag", "url=fondskoersaanvragenEdit.php?action=new");
}

$mnu->addItem("koersen", "Koerscontrole", "url=koersControle.php");
$mnu->addItem("koersen", "Koerscontrole rapport", "url=rapportKoersControleSelectie.php");
if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI")
{
  $mnu->addItem("koersen", "Ontbr. Identifiers", "url=bepaalOntbrekendeIdentifiers.php");
}
$mnu->addItem("koersen", "Schaduwkoersen", "url=schaduwkoersenList.php");
$mnu->addItem("koersen", "Valutakoersen", "url=valutakoersenList.php");
if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI")
{
  $mnu->addItem("koersen", "VWD-lijsten", "url=genereerVWDlijsten.php");
}


