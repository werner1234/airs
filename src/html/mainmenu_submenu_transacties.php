<?php

$mnu->addItem("transacties", "Rekeningafschriften V2", "url=rekeningafschriften_v2_List.php");

$mnu->addItem("transacties", "Rekeningmutaties", "url=rekeningmutatiesList.php");
$mnu->addItem("transacties", "Portefeuille index", "url=historischeportefeuilleindexList.php");

//$mnu->addItem("transacties", "Voorlopigerekeningafschriften", "url=voorlopigeRekeningafschriftenList.php");
$mnu->addItem("transacties", "Voorlopigerekeningafschriften V2", "url=voorlopigeRekeningafschriften_v2_List.php?type=temp");

if (checkAccess())
{
  $mnu->addItem("transacties", "Verwerken", "url=transactiesVerwerken.php");
}
//$mnu->addItem("transacties", "Memoriaal", "url=rekeningafschriftenList.php?memoriaal=true");

if ($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0 || checkAccess())
{
  //$mnu->addItem("transacties", "Voorlopige Memoriaal", "url=voorlopigeRekeningafschriftenList.php?memoriaal=true");

  /*if ( $__appvar['bedrijf'] === 'HOME' || $__appvar['bedrijf'] === 'ANO') {
    $mnu->addItem("transacties","Voorlopige Memoriaal V2","url=voorlopigeRekeningafschriften_v2_List.php?type=temp&memoriaal=true");
  }*/
}

if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "VEC")
{
  //call 5086
  //$mnu->addItem("transacties", "Rekeningmutaties Afvinken", "url=rekeningmutatiesafvinkList.php");
}

if ($__appvar["bedrijf"] == "WAT")
{
  $mnu->addItem("transacties", "Reconciliatie", "submenu=Reconciliatie");

}


if (checkAccess("superapp"))
{
  $mnu->addItem("transacties", "Tijdelijk importbestand", "url=tijdelijkerekeningmutatiesList.php", 1);
  $mnu->addItem("transacties", "");
  $mnu->addItem("transacties", "Transactie-Import", "url=transaktieImport.php");
  $mnu->addItem("transacties", "Import voortgang", "submenu=importVoorgang");
  $mnu->addItem("transacties", "");
  $mnu->addItem("transacties", "Consistentie-Controle", "url=consistentieControle.php");
  //$mnu->addItem("transacties", "Portefeuilles-Controle", "submenu=portefeuillesControle");
  $mnu->addItem("transacties", "Reconciliatie", "submenu=Reconciliatie");
  $mnu->addItem("transacties", "Saldo's herberekenen", "url=saldosHerberekenen.php");
  //$mnu->addItem("transacties", "Positie-Import", "submenu=positieImport");
  $mnu->addItem("transacties", "Index berekening", "url=indexBerekeningSelectie.php");
  $mnu->addItem("transacties", "Scenario berekening", "url=scenarioBerekeningSelectie.php");
  //$mnu->addItem("transacties", "Saldi naar Transacties", "url=importSaldi.php");
  $mnu->addItem("transacties", "Dividend mutaties", "url=dividendMutatieSelectie.php");
  $mnu->addItem("transacties", "Portefeuille afboeken", "url=portefeuilleAfboeken.php");
  $mnu->addItem("transacties", "Controle depotbankcodes", "url=depotbankcodesImport.php");

  if ($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {

    $mnu->addItem("transacties", "Aanpassen boekingen (VERM)", "url=VERM_Afboeken.php");
  }



  if ($__appvar["bedrijf"] == "HOME" or $__appvar["bedrijf"] == "TEST")
  {
    $mnu->addItem("transacties", "");
    $mnu->addItem("transacties", "Rekeningen aanmaken", "url=rekeningAddRekeningen.php");
    $mnu->addItem("transacties", "Clienten aanmaken", "url=clientAddClienten.php");
    $mnu->addItem("transacties", "Check Fondsen", "url=fondsenCheck.php");
  }


//  $mnu->addItem("transacties", "");
  //$mnu->addItem("transacties", "Bulkcontrole", "submenu=positieAutomaat");
  $mnu->addItem("transacties", "");
  //$mnu->addItem("transacties", "Transacties converteren", "url=convert_transacties.php");
  //$mnu->addItem("transacties", "Posities converteren", "url=convert_positie.php");
  //$mnu->addItem("transacties", "Tijdelijk Posities Overzicht", "url=tijdelijkepositielijstList.php");
  //$mnu->addItem("transacties", "Posities Overzicht", "url=positielijstList.php");
  $mnu->addItem("transacties", "Rapport journaalpost", "url=rapportJournaalpost.php");

 //$mnu->addItem("transacties", "");
  if ($adventverwerking)
  {
    $mnu->addItem("transacties", "Advent Export", "submenu=adventexport");
  }
}
if ($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST")
{
  $mnu->addItem("transacties", "Huisfondsen", "submenu=huisfonds");
  $mnu->addItem("huisfonds","Huisfondsen importeren en afboeken","url=huisFondsStart.php");
  $mnu->addItem("huisfonds","Huisfondsen opboeken","url=huisFondsVerwerk.php?opboeken=1");

}


//if ($__appvar['bedrijf'] == 'HOME' || $__appvar['bedrijf'] == 'ANO' || $__appvar["bedrijf"] == "TEST")

// $mnu->addItem("transacties","Rekeningafschriften","url=rekeningafschriftenList.php");