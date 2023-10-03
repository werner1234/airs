<?php

$mnu->addItem("stamgegevens_onderhoudportef", "Portefeuilles", "url=portefeuillesList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Rekeningen", "url=rekeningenList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "");

$mnu->addItem("stamgegevens_onderhoudportef", "Beheerfeehistorie", "url=factuurbeheerfeehistorieList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Beleggingsplan", "url=beleggingsplanList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Clienten", "url=clientList.php");

if ($__appvar["bedrijf"] == "HOME")
{
  $mnu->addItem("stamgegevens_onderhoudportef", "Duplicaat rekeningen", "url=rekeningenduplicaatList.php");
  $mnu->addItem("stamgegevens_onderhoudportef", "Duplicaat rekening aanmaken", "url=rekeningenAddDuplicaat.php");
}

$mnu->addItem("stamgegevens_onderhoudportef", "Eigendom per portfeuille", "url=EigendomPerPortefeuilleList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Factuurregels", "url=factuurregelsList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Fee historie", "url=feehistorieList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Historische tenaamstelling", "url=historischetenaamstellingList.php");
//$mnu->addItem("stamgegevens_onderhoudportef","Historische specifieke index","url=historischespecifiekeindexList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Geconsolideerde portefeuilles", "url=geconsolideerdeportefeuillesList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Model portefeuilles", "url=modelportefeuillesList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Model portefeuilles per modelportefeuille", "url=modelportefeuillespermodelportefeuilleList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Model portefeuilles per portefeuille", "url=modelportefeuillesperportefeuilleList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Normweging per beleggingscategorie", "url=normwegingperbeleggingscategorieList.php");

if (checkAccess())
{
  $mnu->addItem("stamgegevens_onderhoudportef", "Omnummering via csv", "url=omnummeringViaCsv.php");
}

$mnu->addItem("stamgegevens_onderhoudportef", "Portefeuilles clusters", "url=portefeuilleclustersList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Portefeuilles Geconsolideerdeerd ", "url=portefeuillesgeconsolideerdList.php");
$mnu->addItem("stamgegevens_onderhoudportef", "Portefeuillehistorischeparameters", "url=portefeuillehistorischeparametersList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Rekeningen in Consolidaties", "url=rekeningenList.php?consolidatie=1");
$mnu->addItem("stamgegevens_onderhoudportef", "Rekeningparameters historie", "url=rekeningenhistorischeparametersList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Standaarddeviatie per Portefeuille", "url=standaarddeviatieperportefeuilleList.php");

$mnu->addItem("stamgegevens_onderhoudportef", "Uitsluitingen modelcontrole", "url=uitsluitingenmodelcontroleList.php");

if (checkAccess())
{
  $mnu->addItem("stamgegevens_onderhoudportef", "Vergeet portefeuille", "url=vergeetPortefeuille.php");
  if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST")
    $mnu->addItem("stamgegevens_onderhoudportef", "Verhuis portefeuille", "url=portefeuilleVerhuizen.php");
}

$mnu->addItem("stamgegevens_onderhoudportef", "Zorgplicht parameters per portefeuilles", "url=zorgplichtperportefeuilleList.php");





