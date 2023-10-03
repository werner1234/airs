<?php
/*
    AE-ICT sourcemodule created 05 jun 2019
    Author              : Chris van Santen
    Filename            : mainmenu_recon.php

*/

if ($__appvar["bedrijf"] == "WAT")
{
  $mnu->addItem("Reconciliatie", "Overzicht ingelezen positie", "url=tijdelijkereconList.php");
  $mnu->addItem("Reconciliatie", "");
  $mnu->addItem("Reconciliatie", "Inlezen positie bestanden", "url=reconSelectDepotbank.php");
}

if(checkAccess("superapp"))
{
  $mnu->addItem("Reconciliatie", "Overzicht ingelezen positie", "url=tijdelijkereconList.php");
  $mnu->addItem("Reconciliatie", "");
  $mnu->addItem("Reconciliatie", "Inlezen positie bestanden", "url=reconSelectDepotbank.php");
  if ($__appvar["bedrijf"] == "HOME")
  {
    $mnu->addItem("Reconciliatie", "Batch recon jobmanager", "url=batch_jobManager.php");
  }

  $mnu->addItem("Reconciliatie", "");
  $mnu->addItem("Reconciliatie", "Duplicaat rekeningen", "url=reconDuplicaatRekening.php");
  $mnu->addItem("Reconciliatie", "");
  $mnu->addItem("Reconciliatile", "Overige posities", "url=reconOverigePosities.php");
  if ($__appvar["bedrijf"] == "HOME" OR $__appvar["bedrijf"] == "TEST")
  {
    $mnu->addItem("Reconciliatie","");
    $mnu->addItem("Reconciliatie","Recon V3","url=reconV3Start.php");
    $mnu->addItem("Reconciliatie","Recon V3 Log","url=reconV3LogList.php");
    $mnu->addItem("Reconciliatie","");
    $mnu->addItem("Reconciliatie","Recon voortgang","url=reconMonitor_voortgang.php");
    $mnu->addItem("Reconciliatie","Recon status","url=reconMonitor_matrixList.php");
  }

  if($__develop)
  {
//    $mnu->addItem("Reconciliatie","");
//    $mnu->addItem("Reconciliatie","<span style='background: maroon; color:white; display: inline-block; width:100%'>Recon V3</span>","url=reconV3Start.php");
    $mnu->addItem("Reconciliatie","");
    $mnu->addItem("Reconciliatie","Recon per bewaarder","url=reconBewaarderSelect.php");
  }
}
else
{

  $reconAllowed = getVermogensbeheerderField('jaarafsluitingPerBewaarder');
  if ($reconAllowed == 1)
  {
    $mnu->addItem("Reconciliatie","");
    $mnu->addItem("Reconciliatie","Recon per bewaarder","url=reconBewaarderSelect.php");
  }
}
