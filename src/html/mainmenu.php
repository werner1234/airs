<?php
/*
    AE-ICT sourcemodule created 03 nov. 2021
    Author              : Chris van Santen
    Filename            : mainmenu.php


*/
		
include_once("wwwvars.php");
include_once("../classes/AE_cls_menu.php");
include_once($__appvar["basedir"]."/classes/AE_cls_Morningstar.php");
$bestandsVergoedingsModule  = getVermogensbeheerderField('module_bestandsvergoeding');
$dd_AWS_map                 = (strtoupper(getVermogensbeheerderField('ddInleesLocatie')) == "AWS");

session_start();

$ms = new AE_cls_Morningstar();

$db = new DB();

$mnu = New Menu();
//$db = new DB();

$adventverwerking = getVermogensbeheerderField("adventVerwerking");
$millogicverwerking = getVermogensbeheerderField("millogicVerwerking");
$morningstarCheck = getVermogensbeheerderField("morningstar ");

if (!$_SESSION["wwb_soort"])
{
  $cfg = new AE_config();

  $_SESSION["wwb_soort"] = ($cfg->getData("wwBeleid_soort") != "")?"wwAan":"wwUit";
}

if ($_SESSION["wwb_WWchange"])
{
  $mnu->addItem("","Wachtwoord wijzigen","url=wwb_wachtwoordWijzigen.php");

}
else
{



  if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {
    $mnu->addItem("","Bedrijf","submenu=bedrijf");
  }

  $klantmenuFile = "custom/Menu".$__appvar['bedrijfsnummer'].".php";
  if (file_exists($klantmenuFile))
  {
    include_once($klantmenuFile);
    $mnu->addItem("",$klantMenuText,"submenu=klantmenu");
  }

  if(!isset($__appvar["crmOnly"]))
  {
    if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
    {
    $mnu->addItem("","Stamgegevens","submenu=stamgegevens");
    $mnu->addItem("","Koersen","submenu=koersen");
    if(!isset($__appvar["participatiesOnly"]))
      $mnu->addItem("","Transacties","submenu=transacties");
    }
     if(!isset($__appvar["participatiesOnly"]))
      $mnu->addItem("","Rapportage","submenu=rapportage");


    $ordermoduleAccess=GetModuleAccess("ORDER");
    if ($ordermoduleAccess >0 && ($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0))
    {
      $mnu->addItem("","Ordering","submenu=ordering");
      if($ordermoduleAccess==2 || $__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST")
      {
        $mnu->addItem("ordering","Order Overzicht","url=ordersListV2.php?resetFilter=1");
        if(checkOrderAcces('handmatig_opslaan')==true)
          $mnu->addItem("ordering","Nieuwe order","url=ordersEditV2.php?action=new&returnUrl=ordersList.php?status=ingevoerd");
        $mnu->addItem("ordering","Fix orders","url=fixordersList.php?resetFilter=1");
        $mnu->addItem("ordering","open Fix orders","url=fixordersList.php?openOrders=1");

        if($_SESSION['usersession']['gebruiker']['ordersNietAanmaken'] == 0)
        {
          $mnu->addItem("ordering","Handmatige bulkorder invoer","url=ordersEditBulkV2.php");
        }
        $mnu->addItem("ordering","Tijdelijke bulkorders","url=tijdelijkebulkordersv2List.php?resetFilter=1"); //tijdelijkebulkordersv2Verwerken
        //$_SESSION['usersession']['gebruiker']['ordersNietVerwerken']==0
        //$mnu->addItem("ordering","Tijdelijke bulkorders","url=tijdelijkebulkordersv2List.php?resetFilter=1");
        $db=new DB();
        if(checkOrderAcces('VermogensbeheerderOrderOrderdesk')==false && $db->QRecords('SELECT id FROM OrdersV2 WHERE orderStatus=-1 limit 1') == 0 )
          unset($__ORDERvar["orderStatus"][-1]);
        foreach($__ORDERvar["orderStatus"] as $x=>$omschrijving)
          $mnu->addItem("ordering","Order Overzicht met status ".$__ORDERvar["orderStatus"][$x],"url=ordersListV2.php?status=".urlencode($__ORDERvar["orderStatus"][$x]));
        $mnu->addItem("ordering","Order Overzicht met status annuleer verzoek","url=ordersListV2.php?status=annuleerVerzoek");

        $mnu->addItem("ordering","Orders V1","submenu=orderv1");
        $mnu->addItem("orderv1","Order Overzicht","url=ordersList.php?resetFilter=1");
        $mnu->addItem("orderv1","Order regels","url=orderregelsList.php");
        //$mnu->addItem("orderv1","Nieuwe order","url=ordersEdit.php?action=new&returnUrl=ordersList.php?status=ingevoerd");
        if ( $__appvar['bedrijf'] === 'HOME' || $__appvar['bedrijf'] === 'ANO')
          $mnu->addItem("orderv1","Tijdelijkeorderregels","url=tijdelijkeorderregelsList.php");
        $mnu->addItem("orderv1","Enkelvoudige order toevoegen","url=ordersEditEnkelvoudig.php?action=new&returnUrl=ordersList.php?status=ingevoerd");
        //$mnu->addItem("orderv1","Bulk order invoer","url=ordersEditBulk.php");
        for($x=0;$x<count($__ORDERvar["status"]);$x++)
          $mnu->addItem("orderv1","Order Overzicht met status ".$__ORDERvar["status"][$x],"url=ordersList.php?status=".urlencode($__ORDERvar["status"][$x]));
        if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "ANO" || $__appvar["bedrijf"] == "FDX" || $__appvar["bedrijf"] == "VEC" )
          $mnu->addItem("orderv1", "Printen Nota's", "url=orderregelsNotaList.php");
        if($_SESSION['usersession']['gebruiker']['orderbeheerder']==1)
        {
          $mnu->addItem("ordering","Order beheer","submenu=orderbeheer");
          $mnu->addItem("orderbeheer","Orderstatus aanpassen","url=ordersStatusEditV2.php?action=new&returnUrl=ordersList.php");
        }
        $mnu->addItem("ordering","Order regels","url=orderregelsListV2.php");
        if($__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "VRYACC")
        {
          $mnu->addItem("orderbeheer","Storneren orders","url=ordersStorneren.php");
        }
      }
      else
      {
        $mnu->addItem("ordering","Order Overzicht","url=ordersList.php?resetFilter=1");
        $mnu->addItem("ordering","Order regels","url=orderregelsList.php");
        $mnu->addItem("ordering","Nieuwe order","url=ordersEdit.php?action=new&returnUrl=ordersList.php?status=ingevoerd");
        if ( $__appvar['bedrijf'] === 'HOME' || $__appvar['bedrijf'] === 'ANO')
          $mnu->addItem("ordering","Tijdelijkeorderregels","url=tijdelijkeorderregelsList.php");
        $mnu->addItem("ordering","Enkelvoudige order toevoegen","url=ordersEditEnkelvoudig.php?action=new&returnUrl=ordersList.php?status=ingevoerd");
        $mnu->addItem("ordering","Bulk order invoer","url=ordersEditBulk.php");
        for($x=0;$x<count($__ORDERvar["status"]);$x++)
          $mnu->addItem("ordering","Order Overzicht met status ".$__ORDERvar["status"][$x],"url=ordersList.php?status=".urlencode($__ORDERvar["status"][$x]));
      }

      if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "ANO" || $__appvar["bedrijf"] == "FDX" || $__appvar["bedrijf"] == "VEC" )
      {
        if($ordermoduleAccess==2)
          $mnu->addItem("ordering", "Printen Nota's", "url=orderregelsNotaListV2.php");
        else
          $mnu->addItem("ordering", "Printen Nota's", "url=orderregelsNotaList.php");

      }
      //if($__appvar["bedrijf"] == "ANO" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "SLV")
      $mnu->addItem("ordering", "Order rapporten", "url=rapportOrderSelectie.php");
      
    }

    if (GetModuleAccess("BOEKEN"))
     $boeken = true;
    else
     $boeken = false;

    if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
      $mnu->addItem("","Jaarafsluiting","submenu=jaarafsluiting");
  }

  /*
  // CRM module
  */

  $mnu->addItem("","Help","submenu=help");
  if (GetModuleAccess("CRM") && ($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0))
  {
    $mnu->addItem("","CRM","submenu=crm");
    include("CRM_menuInclude.php");
  }

  if ($__appvar["bedrijf"] == "TRA" || $__appvar["bedrijf"] == "ACRM" || GetModuleAccess("UREN") )
  {
     $mnu->addItem("","Uren","submenu=uren");
     $mnu->addItem("uren","mijn urenlijst","url=CRM_uur_registratieList.php?q=perUser",1);
     $mnu->addItem("uren","Uren per relatie","url=CRM_uur_registratieRelatieList.php",1);
     if ($__appvar["bedrijf"] == "TRA" || $__appvar["bedrijf"] == "ACRM" || GetModuleAccess("UREN")==2)
     {
       $mnu->addItem("uren","Uur activiteiten codes","url=CRM_uur_activiteitenList.php");//CRM_templateEditor.php
       $mnu->addItem("uren","Alle uren","url=CRM_uur_registratieList.php",1);
     }
  }
  //$mnu->addItem("","Uitloggen","url=login.php?logout=true",0,"_top");

  // todo: dit kan na de ontwikkel cyclus weg
//  if ($__develop)
//  {
//    $mnu->addItem("","Home","url=welcomeNw.php");
//  }
//  else
//  {
    $mnu->addItem("","Home","url=welcome.php");
//  }

  if((checkAccess("superapp") && $__appvar['master'] == true))// && $__appvar["bedrijf"] <> 'TEST'
  {

    if($__appvar["bedrijf"] == 'TEST')
      $mnu->addItem("bedrijf","Update","url=queueImport.php");
  }
  else
  {
    if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
      $mnu->addItem("bedrijf","Update","url=queueImport.php");

    //$mnu->addItem("bedrijf","Import database","url=importfile.php");
  }
  $mnu->addItem("bedrijf","");
  if(!isset($__appvar["crmOnly"]))
  {
    $mnu->addItem("bedrijf", "Bedrijf consistentie controle", "url=BedrijfConsistentieControle.php");
  }

  if(checkAccess("superapp"))
  {
    if(!isset($__appvar["crmOnly"]))
      $mnu->addItem("bedrijf","Bedrijfsgegevens","url=bedrijfsgegevensList.php",1);
  }

  if($dd_AWS_map)
  {
    $mnu->addItem("bedrijf", "Digitale documenten map", "url=dd_inleesFilman.php");
  }

  if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {

    $mnu->addItem("bedrijf", "Digitale documenten inlezen", "url=dd_inlees.php");
    $mnu->addItem("bedrijf", "Digitale documenten instellingen", "url=dd_setup.php");

  }

  if (get_eMailInlezenCheck())
  {
    $mnu->addItem("bedrijf","Digitale documenten mailbox","url=dd_inlees_email.php");
  }

  if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {

   if(!isset($__appvar["crmOnly"]) && $_SESSION['usersession']['gebruiker']['rechtenExterneQueries'] > 0)
      $mnu->addItem("bedrijf","Externe queries","url=externequeriesList.php");

  }

  if(GetCRMAccess(2))
  {
    $mnu->addItem("bedrijf", "Gebruiksgegevens", "url=usagelogList.php");
  }


  if(checkAccess("superapp") && !isset($__appvar["crmOnly"]))
  {
    $mnu->addItem("bedrijf","Geplande taken (cron)","submenu=cron");
    $mnu->addItem("cron","Geplande taken","url=cronList.php");
    $mnu->addItem("cron","Log","url=cronlogList.php");
  }


  if($__appvar["enableHENSspecial"])  $mnu->addItem("bedrijf","Handmatige Iphone Update","url=HEN_dbDump.php");

  if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {
    if (!isset($__appvar["crmOnly"]))
    {

      $mnu->addItem("bedrijf", "Klant mutaties", "url=klantmutatiesList.php");
    }
  }

  if(GetCRMAccess(2))
  {

    $mnu->addItem("bedrijf", "Logs", "submenu=bedrijf_logs");
  }

  if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {
    if (!isset($__appvar["crmOnly"]))
    {

      $mnu->addItem("bedrijf", "Portfeuillewaarde herrekenen", "url=portefeuilleWaardeHerrekening.php");
    }
  }


  if((checkAccess("superapp") && $__appvar['master'] == true))// && $__appvar["bedrijf"] <> 'TEST'
  {
    $mnu->addItem("bedrijf","Update historie","url=updatehistoryList.php");
    $mnu->addItem("bedrijf","Exporteren naar Queue","url=queueExport.php");
    $mnu->addItem("bedrijf","Exporteren zaterdag export","url=queueZaterdagExport.php");
    $mnu->addItem("bedrijf","Exporteren updateinfo","url=queueExportUpdateInfo.php");
    $mnu->addItem("bedrijf","Exporteren SMS aan/uit","url=queueExportSMS.php");
    $mnu->addItem("bedrijf","Exporteren ZIP file","url=queueExportZip.php");  // call 10293

    if (strtoupper($USR) == 'FEGT' OR strtoupper($USR) == 'JBR' )
    {
      $mnu->addItem("bedrijf","Updateserver queue","url=updatequeueList.php");
    }
  }

  if(GetCRMAccess(2))
  {

    $mnu->addItem("bedrijf", "Standaard veldvulling", "url=standaardveldvullingList.php");

  }
  if($_SESSION['usersession']['gebruiker']['Beheerder'] > 0 || $_SESSION['usersession']['gebruiker']['Gebruikersbeheer'])
  {
    $mnu->addItem("bedrijf","Systeem instellingen","url=bedrijfInstellingen.php");
    if(isset($__appvar["crmOnly"]))
    {
      $mnu->addItem("bedrijf","Gebruikers","url=gebruikerList.php");
      $mnu->addItem("toegangsControle","Wachtwoord beleid","url=wwb_instellingen.php");
      if ($__appvar["tgc"] == "enabled")
      {
        $mnu->addItem("bedrijf","ToegangsControle","submenu=toegangsControle");
        include_once 'mainmenu_toegangsControle.php';
      }
    }
  }


  if(GetCRMAccess(2))
  {




    if(isset($__appvar["crmOnly"]))
    {
      $mnu->addItem("bedrijf", "Risicoklassen", "url=risicoklassenList.php");
      $mnu->addItem("bedrijf", "Scenarios per vermogensbeheerder", "url=scenariospervermogensbeheerderList.php");
    }

    $mnu->addItem("bedrijf_logs","eMail log","url=emaillogList.php");
    $mnu->addItem("bedrijf_logs","Track & Trace","url=trackandtraceList.php");
    $mnu->addItem("bedrijf_logs","Controle eMail historie","url=controlemailhistorieList.php");
    $mnu->addItem("bedrijf_logs","CRM&nbsp;mutatieLog","url=CRM_mutatieQueueList.php?action=log");
    if ($__appvar["apiExternEnabled"])
    {
      $mnu->addItem("bedrijf_logs","API extern communicatie","url=api_extern_loggingList.php");
    }

  }

  if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {


    $mnu->addItem("stamgegevens","Algemene stamgegevens","submenu=stamgegevens_algemeen");
    $mnu->addItem("stamgegevens","Onderhoud vermogensbeheerders","submenu=stamgegevens_onderhoudvermogenbeh");
    $mnu->addItem("stamgegevens","Onderhoud fondsen","submenu=stamgegevens_onderhoudfondsen");
    if(!isset($__appvar["participatiesOnly"]))
      $mnu->addItem("stamgegevens","Onderhoud portefeuilles","submenu=stamgegevens_onderhoudportef");

    if($adventverwerking)
    {
      $mnu->addItem("stamgegevens","Onderhoud Advent export","submenu=stamgegevens_advent");
      $mnu->addItem("stamgegevens_advent","Mappinglijst Fondsen","url=advent_fondsmappingList.php");
      $mnu->addItem("stamgegevens_advent","Instellingen tbv Advent","url=advent_setup.php");
    }

    if($millogicverwerking)
    {
      $mnu->addItem("stamgegevens","Onderhoud Millogic export","submenu=stamgegevens_millogic");
      $mnu->addItem("stamgegevens_millogic","Rekening parameters","url=millogic_rekeningenList.php");
      $mnu->addItem("stamgegevens_millogic","Fonds parameters","url=millogic_fondsparametersList.php");
      $mnu->addItem("stamgegevens_millogic","Transactie mapping","url=millogic_transactiemappingList.php");
    }

    $doorkijk = "mainmenu_doorkijk.php";
    if (file_exists($doorkijk))
    {
      $mnu->addItem("stamgegevens","Onderhoud Doorkijk","submenu=stamgegevens_doorkijk");
      include_once($doorkijk);
    }


  }
  include_once ("mainmenu_submenu_stamgegevens_algemeen.php");



  if ($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {
    include_once ("mainmenu_submenu_stamgegevens_onderhoudvermogenbeh.php");

    include_once ("mainmenu_submenu_stamgegevens_onderhoudfondsen.php");

    include_once ("mainmenu_submenu_stamgegevens_onderhoudportef.php");

    include_once ("mainmenu_submenu_koersen.php");

    include_once ("mainmenu_submenu_transacties.php");



    $mnu->addItem("importVoorgang", "Importvoortgang", "url=MONITOR_voortgang.php");
    $mnu->addItem("importVoorgang", "Importstatus", "url=MONITOR_importMatrixList.php");
    $mnu->addItem("importVoorgang", "Bedrijf-depotbank", "url=MONITOR_bedrijfDepotList.php");

    include_once("mainmenu_recon.php");

    $mnu->addItem("adventexport", "Positie conversie", "submenu=adventPositieExport");
    $mnu->addItem("adventexport", "Advent uitvoermap", "url=advent_filemanager.php");

    $mnu->addItem("adventPositieExport", "ABN v1", "url=advent_positie_convertAAB.php");
    $mnu->addItem("adventPositieExport", "ABN v2", "url=advent_positie_convertCSV.php?bank=AAB");
    $mnu->addItem("adventPositieExport", "Binck v1", "url=advent_positie_convertCSV.php?bank=Binck");
    $mnu->addItem("adventPositieExport", "Binck v2", "url=advent_positie_convertCSV.php?bank=Binck&version=2");
    $mnu->addItem("adventPositieExport", "TGB", "url=advent_positie_convertCSV.php?bank=TGB");
    $mnu->addItem("adventPositieExport", "FVL", "url=advent_positie_convertCSV.php?bank=FVL");
    $mnu->addItem("adventPositieExport", "LOM", "url=advent_positie_convertCSV.php?bank=LOM");
    $mnu->addItem("adventPositieExport", "SAXO", "url=advent_positie_convertCSV.php?bank=SAXO");
    $mnu->addItem("adventPositieExport", "UBS", "url=advent_positie_convertUBS.php");
    $mnu->addItem("adventPositieExport", "UBS Lux", "url=advent_positie_convertCSV.php?bank=UBSL");


    if ((($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST") && GetCRMAccess(2)) || strtoupper($USR) == 'AIRS')
    {
      $mnu->addItem("transacties", "Dagelijkse fee berekening", "url=historischeDagelijkeWaardenBerekeningSelectie.php");
    }
    if ($bestandsVergoedingsModule == 1)
    {
      $mnu->addItem("transacties", "Bestandsvergoedingen", "url=bestandsvergoedingenList.php");
    }
    if ($bestandsVergoedingsModule > 0)
    {
      $mnu->addItem("transacties", "Bestandsvergoeding regels", "url=bestandsvergoedingperportefeuilleList.php");
    }
    if (strtoupper($USR) == 'FEGT' ||
        strtoupper($USR) == 'KRL' ||
        strtoupper($USR) == 'JBR' ||
        strtoupper($USR) == 'JSR')
    {
      $mnu->addItem("transacties", "Gegevensimport tabellen", "url=tabelDataImport.php");
    }
  }

  $mnu->addItem("rapportage","Front-Office","url=rapportFrontofficeClientSelectie.php");
  $mnu->addItem("rapportage","Back-office" ,"url=rapportBackoffice.php" );
  $mnu->addItem("rapportage","Facturering Beheerfee","url=factuurSelectie.php");
  $mnu->addItem("rapportage","" );

  if ( $morningstarCheck == 2 || $morningstarCheck == 4 ) {
    $mnu->addItem("rapportage", "EMT rapport", "url=rapportEmtSelectie.php");
  }
  $mnu->addItem("rapportage","Query wizard" ,"url=queryWizard.php?type=all" );
  $mnu->addItem("rapportage","Report-builder" ,"url=reportBuilder.php" );
  $mnu->addItem("rapportage","Report-builder II" ,"url=reportBuilder2.php" );
  if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST")
  {
    $mnu->addItem("rapportage","XLS-batch" ,"url=rapportXlsBatch.php");
  }
  $mnu->addItem("rapportage","XLS-selectie" ,"url=rapportXlsSelectie.php" );


  //$mnu->addItem("rapportage","Front-Office oud","url=rapportFrontofficeClientSelectieold.php");

//  if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "ANO")
//    $mnu->addItem("rapportage","Front-office Html","url=rapportFrontofficeHtmlRapport.php");
  //$mnu->addItem("rapportage","Back-office II" ,"url=rapportSelectie.php?type=dagRapportage" );
//  if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "TEST" || $__appvar["bedrijf"] == "WEY")
//    $mnu->addItem("rapportage","digidoc pdf samenvoegen" ,"url=rapportBackofficeMerge.php");



  if($_SESSION['usersession']['gebruiker']['Beheerder'] >= 0)
  {
//  $mnu->addItem("portefeuillesControle","Stroeve ISIN","url=portefeuillesControle.php?bank=stroeve");
//  $mnu->addItem("portefeuillesControle","Stroeve StroeveCode","url=portefeuillesControle.php?bank=stroeveEigen");
//  $mnu->addItem("portefeuillesControle","Gilissen","url=portefeuillesControle.php?bank=gilis");
//  $mnu->addItem("portefeuillesControle","Gilissen VT","url=portefeuillesControle.php?bank=gilisVt");
//  $mnu->addItem("portefeuillesControle","Binck bank","url=portefeuillesControle.php?bank=binck");
//  $mnu->addItem("portefeuillesControle","ABN-AMRO","url=portefeuillesControle.php?bank=abn");
//  $mnu->addItem("portefeuillesControle","ABN-AMRO Belgie","url=portefeuillesControle.php?bank=abnbe");
//  $mnu->addItem("portefeuillesControle","SNS","url=portefeuillesControle.php?bank=sns");
//  $mnu->addItem("portefeuillesControle","SNS Securities","url=portefeuillesControle.php?bank=snssec");
//  $mnu->addItem("portefeuillesControle","ANT","url=portefeuillesControle.php?bank=ant");
//  $mnu->addItem("portefeuillesControle","Rabo","url=portefeuillesControle.php?bank=rabo");
//  $mnu->addItem("portefeuillesControle","Rabo via transactiebestand","url=portefeuillesControle.php?bank=raboTrans");
//  $mnu->addItem("portefeuillesControle","Rabo via positiemap","url=portefeuillesControle.php?bank=raboExcel");
//  $mnu->addItem("portefeuillesControle","Rothschild","url=portefeuillesControle.php?bank=bpere");

//  $mnu->addItem("positieImport","Stroeve ISIN","url=positieImport.php?bank=stroeve");
//  $mnu->addItem("positieImport","Gilissen","url=positieImport.php?bank=gilis");

//  $mnu->addItem("positieAutomaat","Verschillen overzicht","url=portefeuilleautoumaatList.php");
//  $mnu->addItem("positieAutomaat","Genereer handmatig","url=portefeuilleAutomaat_genereer.php");
//  $mnu->addItem("positieAutomaat","Instellingen","url=portefeuilleAutomaat_setup.php");


  if(GetModuleAccess('FACTUURHISTORIE'))
    $mnu->addItem("rapportage","Factuur historie","url=factuurhistorieList.php");
  //$mnu->addItem("rapportage","Facturering Beheerfee II","url=rapportSelectie.php?type=factuur");
  if($bestandsVergoedingsModule==1)
    $mnu->addItem("rapportage","Bestandsvergoeding","url=bestandsvergoedingSelectie.php");



  if(checkAccess("superapp"))
    $mnu->addItem("jaarafsluiting","Jaarafsluiten","url=jaarafsluiting.php");
  }


  $mnu->addItem("help","DatabaseInfo","url=helpDataBase.php");
  $mnu->addItem("help","Filemanager","url=tmpManager.php");
  $mnu->addItem("help","Handleidingen AIRS","url=handleidingenairsList.php");
  $mnu->addItem("help","Help teksten","submenu=htekst");
  $mnu->addItem("help","Online-support","url=remoteSupport.php");
  $mnu->addItem("help","SysteemInfo","url=help.php");
  $mnu->addItem("help","Update informatie","url=updateinformatieList.php");
  if ($_SESSION["wwb_soort"] == "wwAan")
  {
  $mnu->addItem("help","Wachtwoord wijzigen","url=wwb_wachtwoordWijzigen.php");
  }


  //$mnu->addItem("help","Online-support","url=http://eu.ntrsupport.com/inquiero/anonymous2.asp?skclient=&lang=nl&con=1&online=1&bonline=1&login=38378&oper=airs",0,"_blank");


  $mnu->addItem("htekst","Handleiding","url=handleiding.php");
  $mnu->addItem("htekst","Help teksten","url=help_tekstList.php");
  $mnu->addItem("htekst","Help teksten DB","url=help_veldenList.php");


  //$mnu->addItem("help","GebruikersInfo","url=helpUser.php");



  
}

/*
foreach ($mnu->menuItems as $menu=>$items)
{
  echo "<b>$menu</b> <br>\n";
  foreach ($items as $id=>$item)
  {
    echo "- ".$item['name']." <br>\n";
  }
}
*/
include_once "mainmenu_shortcut.php";

if($nomenu != true)
  $menuList =  $mnu->createMenu();
/*
if($__appvar['bedrijf'] === 'TEST' || $__appvar['bedrijf'] === 'TRA' || $__appvar['bedrijf'] === 'HOME')
echo '
  <script type="text/javascript" src="javascript/jquery-min.js"></script>
  <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
  <div id="notify-box"></div>
  <script>
    $(document).ready(function(){
      $( "#notify-box" ).draggable();
      $("#notify-box").load("notifyData.php");
    });
  </script>
';
*/
?>