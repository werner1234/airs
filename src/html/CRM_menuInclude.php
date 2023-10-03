<?
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/07/08 15:38:46 $
 		File Versie					: $Revision: 1.70 $

 		includefile met de menuitems voor de CRM module
 		$mnu->addItem("","CRM","submenu=crm");
 		$Log: CRM_menuInclude.php,v $
 		Revision 1.70  2020/07/08 15:38:46  rvv
 		*** empty log message ***
 		
 		Revision 1.69  2020/05/11 08:20:05  cvs
 		spelfout
 		
 		Revision 1.68  2020/04/15 14:15:51  rm
 		8144
 		
 		Revision 1.67  2020/02/26 16:10:59  rvv
 		*** empty log message ***
 		
 		Revision 1.66  2020/01/30 07:26:12  cvs
 		call 8380
 		
 		Revision 1.65  2020/01/11 19:42:11  rvv
 		*** empty log message ***
 		
 		Revision 1.64  2019/03/01 08:58:20  cvs
 		call 7364
 		
 		Revision 1.63  2018/07/25 15:33:43  rvv
 		*** empty log message ***
 		
 		Revision 1.62  2018/07/23 05:43:19  rvv
 		*** empty log message ***
 		
 		Revision 1.61  2018/07/22 12:48:43  rvv
 		*** empty log message ***
 		
 		Revision 1.60  2018/06/27 16:11:10  rvv
 		*** empty log message ***
 		
 		Revision 1.59  2018/05/06 11:31:30  rvv
 		*** empty log message ***
 		
 		Revision 1.58  2018/03/07 09:14:41  cvs
 		call 6440
 		
 		Revision 1.57  2018/02/01 13:05:09  cvs
 		update naar airsV2
 		
 		Revision 1.56  2018/01/10 16:11:44  rm
 		6431
 		
 		Revision 1.55  2017/12/20 16:59:57  rvv
 		*** empty log message ***
 		
 		Revision 1.54  2017/12/16 18:42:38  rvv
 		*** empty log message ***
 		
 		Revision 1.53  2017/12/08 18:23:43  rm
 		no message
 		
 		Revision 1.52  2017/11/25 20:22:26  rvv
 		*** empty log message ***
 		
 		Revision 1.51  2017/11/15 11:07:39  cvs
 		call 6145
 		
 		Revision 1.50  2017/10/14 17:22:29  rvv
 		*** empty log message ***
 		
 		Revision 1.49  2017/07/16 10:50:44  rvv
 		*** empty log message ***
 		
 		Revision 1.48  2016/11/20 10:19:03  rvv
 		*** empty log message ***
 		
 		Revision 1.47  2016/06/17 12:19:24  rm
 		no message
 		
 		Revision 1.46  2015/11/29 13:09:34  rvv
 		*** empty log message ***
 		
 		Revision 1.45  2015/11/18 17:05:01  rvv
 		*** empty log message ***
 		
 		Revision 1.44  2015/06/10 15:59:49  rvv
 		*** empty log message ***
 		
 		Revision 1.43  2015/03/20 08:29:24  rm
 		participaties
 		
 		Revision 1.42  2015/03/11 16:53:42  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2015/01/28 13:08:28  rm
 		update voor Participanten
 		
 		Revision 1.40  2014/10/04 15:18:53  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2014/08/27 15:45:24  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2014/08/23 15:36:34  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2014/08/20 15:29:13  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2014/08/13 14:52:31  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2014/08/09 15:05:41  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2014/07/23 16:09:33  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2014/07/20 13:06:52  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2014/06/08 07:54:21  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2014/04/26 16:40:33  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2014/02/28 16:39:28  rvv
 		*** empty log message ***
 		
 		Revision 1.29  2013/09/01 13:31:16  rvv
 		*** empty log message ***
 		
 		
     Revision 1.28  2013/08/10 15:47:17  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/01/05 16:06:05  cvs
 		eerste CRM test


*/


if (GetModuleAccess('alleenNAW') == 1)
{
  $mnu->addItem("crm", "Relaties", "submenu=crm_relaties");
  $mnu->addItem("crm_relaties", "NAW Actieve relaties", "url=CRM_nawOnlyList.php?sql=actief");
  $mnu->addItem("crm_relaties", "NAW Inactieve relaties", "url=CRM_nawOnlyList.php?sql=inactief");
  $mnu->addItem("crm_relaties", "NAW Alle relaties", "url=CRM_nawOnlyList.php?sql=all");
  $mnu->addItem("crm", "Wachtrijen", "submenu=crm_wachtrij");
  $mnu->addItem("crm_wachtrij", "email wachtrij", "url=emailqueueList.php", 1);
  $mnu->addItem("crm", "Instellingen", "submenu=crm_setup");
  

  if (GetModuleAccess('PORTAAL') == 1)
  {
    $mnu->addItem("crm_wachtrij", "portaal wachtrij", "url=portaalqueueList.php", 1);
  }
  if ($__appvar["apiBlancoEnabled"] )
  {
    $mnu->addItem("crm_wachtrij", "Blanco API wachtrij", "url=CRM_blanco_mutatieQueueList.php", 1);
  }
  
  if (GetCRMAccess(2))
  {
    $mnu->addItem("crm_setup", "Mail templates", "submenu=CrmMailTemplatesMenu");
    $mnu->addItem("CrmMailTemplatesMenu", "Waardedaling mail template", "url=waardedalingPortefeuilleMailSettings.php");
    if (isset($_DB_resources[DBportaal]))
    {
      $mnu->addItem("crm_setup", "NAW portaal synchronisatie", "url=CRM_nawPortaalSync.php");
    }
    if ($__appvar["bedrijf"] == "HOME" OR $USR == "AIRS")
    {
      $mnu->addItem("crm_setup", "Digidoc controle", "url=html/_ddCheck.php");

    }
  }
}
else
{

  $mnu->addItem("crm", "Relaties", "submenu=crm_relaties");
  $mnu->addItem("crm", "Kennisbank", "url=CRM_faqList.php");
  $mnu->addItem("crm", "Instellingen", "submenu=crm_setup");



  if ($_SESSION["usersession"]["gebruiker"]["crmImport"] == 1)  // call 6145
  {
    $mnu->addItem("crm_setup", "CRM import", "submenu=crm_setup_import");

    $mnu->addItem("crm_setup_import", "CRM import", "url=CRM_naw_ImportFase1.php");
    $mnu->addItem("crm_setup_import", "CRM import documenten", "url=CRM_naw_zipUload.php");
    //if($__appvar["bedrijf"] == "HOME" || $__appvar["bedrijf"] == "AEI" || $__appvar["bedrijf"] == "TEST")
    $mnu->addItem("crm_setup_import", "CRM Import rapportageinst.", "url=CRM_rapportageInstellingImport.php");
  }


//$mnu->addItem("crm","Query wizard" ,"url=queryWizard.php?type=CRM" );
  $mnu->addItem("crm", "Gespreksverslag details", "url=CRM_naw_dossierListDetails.php");



  $mnu->addItem("crm", "Wachtrijen", "submenu=crm_wachtrij");

  $mnu->addItem("crm_wachtrij", "email wachtrij", "url=emailqueueList.php", 1);
  if (GetModuleAccess('PORTAAL') == 1)
  {
    $mnu->addItem("crm_wachtrij", "portaal wachtrij", "url=portaalqueueList.php", 1);
  }

//$mnu->addItem("crm","eDossier wachtrij","url=edossierqueueList.php",1);

  $mnu->addItem("crm_wachtrij", "eDosier wachtrij", "url=edossierqueueList.php", 1);
  if (GetCRMAccess(2))
  {
    $mnu->addItem("crm_wachtrij", "CRM wijzigingen portaal", "url=CRM_mutatieQueueVerwerken.php", 1);
    $mnu->addItem("crm_wachtrij", "Vragenlijsten portaal", "url=CRM_portaalVragenQueue.php", 1);
  }

  if ($__appvar["apiExternEnabled"] )
  {
    $mnu->addItem("crm_wachtrij", "Externe API wachtrij", "url=API_queueExternList.php", 1);
  }

  if ($__appvar["apiBlancoEnabled"] )
  {
    $mnu->addItem("crm_wachtrij", "Blanco API wachtrij", "url=CRM_blanco_mutatieQueueList.php", 1);
  }

  $db = new DB();
  $query = "SELECT CRM_relatieSoorten FROM Gebruikers WHERE Gebruiker='$USR'";
  $db->SQL($query);
  $CRM_relatieSoorten = $db->lookupRecord();
  $CRM_relatieSoorten = unserialize($CRM_relatieSoorten['CRM_relatieSoorten']);

  if (is_array($CRM_relatieSoorten))
  {
    $vertalingen = array('all' => 'Alle relaties', 'debiteur' => 'Clienten', 'crediteur' => 'Leveranciers', 'prospect' => 'Prospects', 'overige' => 'Overige', 'inaktief' => 'Inaktieven', 'aktief' => 'Aktieven');
    $query = "SELECT veldnaam,omschrijving FROM CRM_eigenVelden WHERE relatieSoort=1";
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $vertalingen[$data['veldnaam']] = $data['omschrijving'];
    }

    $query = "DESC CRM_naw";
    $db->SQL($query);
    $db->Query();
    $crmVelden = array();
    while ($data = $db->nextRecord('num'))
    {
      $crmVelden[] = $data[0];
    }

    foreach ($CRM_relatieSoorten as $relatieSoort)
    {
      $omschrijving = $vertalingen[$relatieSoort];
      if ($omschrijving == '' && in_array($relatieSoort, $crmVelden))
      {
        $omschrijving = $relatieSoort;
      }
      if ($omschrijving <> '')
      {
        $mnu->addItem("crm_relaties", $omschrijving, "url=CRM_nawList.php?sql=" . $relatieSoort);
      }
    }
  }
  else
  {
    $mnu->addItem("crm_relaties", "Alle relaties", "url=CRM_nawList.php?sql=all");
    $mnu->addItem("crm_relaties", "Clienten", "url=CRM_nawList.php?sql=debiteur");
    $mnu->addItem("crm_relaties", "Leveranciers", "url=CRM_nawList.php?sql=crediteur");
    $mnu->addItem("crm_relaties", "Prospects", "url=CRM_nawList.php?sql=prospect");
    $mnu->addItem("crm_relaties", "Overige", "url=CRM_nawList.php?sql=overige");
    $mnu->addItem("crm_relaties", "Inaktieven", "url=CRM_nawList.php?sql=inaktief", 1);
    $query = "SELECT veldnaam,omschrijving FROM CRM_eigenVelden WHERE relatieSoort=1 AND veldnaam NOT IN('debiteur','crediteur','prospect','overige')";
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $mnu->addItem("crm_relaties", $data['omschrijving'], "url=CRM_nawList.php?sql=" . $data['veldnaam']);
    }
  }

  $menus = $__CRMvars["selectieTypen"];
  ksort($menus);
  while (list($key, $val) = each($menus))
  {
    $mnu->addItem("crm_setup_trek", $val, "url=CRM_selectieveldenList.php?module=" . urlencode($key) . "&omschrijving=" . urlencode($val));
  }
  $mnu->addItem("crm_setup_trek", "");


  $query = "SELECT omschrijving FROM CRM_selectievelden WHERE module = 'standaardTaken' ORDER BY omschrijving ";
  $db->SQL($query);
  $db->Query();
  while ($data = $db->nextRecord())
  {
    $mnu->addItem("taak_voortgang", $data['omschrijving'], "url=taakVoortgang.php?categorie=" . urlencode($data['omschrijving']));
  }

  $query = "SELECT max(Vermogensbeheerders.check_module_CRM_eigenVelden) AS eingenVelden FROM Vermogensbeheerders";
  $eigenVelden = $db->lookupRecordByQuery($query);

  if ($eigenVelden['eingenVelden'] == 1)
  {
    $query = "SELECT CRM_eigenVelden.veldnaam, CRM_eigenVelden.omschrijving, CRM_eigenVelden.veldtype, CRM_eigenVelden.weergaveBreedte FROM CRM_eigenVelden WHERE CRM_eigenVelden.veldtype='Trekveld' ORDER BY veldnaam";
    $db->SQL($query);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $mnu->addItem("crm_setup_trek", $data['omschrijving'], "url=CRM_selectieveldenList.php?module=" . urlencode($data['veldnaam']) . "&omschrijving=" . urlencode($data['omschrijving']));
    }
  }

  $mnu->addItem("crm_setup", "Kennisbank onderwerpen", "url=CRM_faq_owList.php");

  $mnu->addItem("crm_setup","RTF/PDF templates","submenu=rtfpdftemplates");
  $mnu->addItem("crm_setup","Veldinstellingen","submenu=veldinstellingen");
  $mnu->addItem("crm_setup","Templates","submenu=templates");



  $mnu->addItem("rtfpdftemplates", "Upload RTF templates", "url=crm_naw_rtftemplatesList.php");
  $mnu->addItem("rtfpdftemplates", "Upload PDF templates", "url=PDF_TemplateUpload.php");
  $mnu->addItem("rtfpdftemplates", "PDF template tekst", "url=pdftemplatetextList.php");
  $mnu->addItem("rtfpdftemplates", "PDF template afbeelding", "url=pdftemplateafbeeldingList.php");

  $mnu->addItem("veldinstellingen", "Instellingen trekvelden", "submenu=crm_setup_trek");
  if ($eigenVelden['eingenVelden'] == 1)
  {
    $mnu->addItem("veldinstellingen", "Velden en veldinstellingen", "url=crm_eigenveldenList.php");
  }
  $mnu->addItem("veldinstellingen", "Veld toegangsrechten", "url=CRM_veldRechten.php");//CRM_templateEditor.php
  $mnu->addItem("veldinstellingen", "Veldopmaak", "url=veldopmaakList.php");


  $mnu->addItem("templates", "CRM naw templates", "url=crm_naw_templatesList.php");//CRM_templateEditor.php
  $mnu->addItem("templates", "Mail templates", "submenu=CrmMailTemplatesMenu");

  $mnu->addItem("crm_setup", "Brieven", "url=CRM_rtfBriefOpmaak.php");
//  $mnu->addItem("crm_setup", "CRM wachtwoord generator", "url=CRM_pwdGen.php");


  if (GetCRMAccess(2))
  {
    $mnu->addItem("templates", "CRM gespreksverslag templates", "url=crm_naw_dossier_templatesList.php");//CRM_templateEditor.php
    $mnu->addItem("crm_setup", "Gespreksverslagen verplaatsen", "url=CRM_naw_dossierMove.php");
    if (isset($_DB_resources[DBportaal]))
    {
      $mnu->addItem("crm_setup", "NAW portaal synchronisatie", "url=CRM_nawPortaalSync.php");

    }
    if ($__appvar["bedrijf"] == "HOME" OR $USR == "AIRS")
    {
      $mnu->addItem("crm_setup", "Digidoc controle", "url=html/_ddCheck.php");

    }
    $mnu->addItem("crm_setup", "Verwijderen gehele relatie", "url=CRM_naw_verwijderRelatie.php");
    $mnu->addItem("CrmMailTemplatesMenu", "Waardedaling mail template", "url=waardedalingPortefeuilleMailSettings.php");
    $mnu->addItem("CrmMailTemplatesMenu", "Bevestigings mail orders", "url=ordersConfirmMailSettings.php");
  }

  $query = "SELECT Max(Vermogensbeheerders.check_module_VRAGEN) AS module_VRAGEN FROM Vermogensbeheerders";
  $db->SQL($query);
  $vragen = $db->lookupRecord();
  if ($vragen['module_VRAGEN'] == 1)
  {
    $mnu->addItem("crm_setup", "", "", 1);
    $mnu->addItem("crm_setup", "Vragenlijst instellingen", "submenu=vragenlijstenMenu");
    $mnu->addItem("vragenlijstenMenu", "Vragenlijsten", "url=vragenvragenlijstenList.php");
    $mnu->addItem("vragenlijstenMenu", "Vragen voor vragenlijsten", "url=vragenvragenList.php");
    $mnu->addItem("vragenlijstenMenu", "Antwoorden op vragenlijst vragen", "url=vragenantwoordenList.php");
  }

  $mnu->addItem("", "Agenda en Taken", "submenu=agenda");
  $mnu->addItem("agenda", "Agenda", "submenu=agendaSub");
  $mnu->addItem("agenda", "Taken", "submenu=takenSub");
  $mnu->addItem("agendaSub", "Toon agenda", "url=agendaDagpointer.php");
  $mnu->addItem("agendaSub", "", "", 1);
  $mnu->addItem("agendaSub", "Agenda overzicht", "url=agendaList.php?do=alles");
  $mnu->addItem("agendaSub", "Mijn agenda ", "url=agendaList.php?do=mijzelf");

  $mnu->addItem("takenSub", "Openstaande taken", "url=takenList.php?filter=openstaand");
  $mnu->addItem("takenSub", "Alle taken", "url=takenList.php?do=alles");
  $mnu->addItem("takenSub", "", "", 1);
  $mnu->addItem("takenSub", "Taak-voortgang", "submenu=taak_voortgang");
  $mnu->addItem("takenSub", "Taken details", "url=CRM_naw_takenListDetail.php");
  $mnu->addItem("takenSub", "Standaard taken", "url=standaardtakenList.php");

  if (GetCRMAccess(2))
  {
    $mnu->addItem("takenSub", "Import taken", "url=taken_ImportFase1.php");
  }
  $mnu->addItem("CrmMailTemplatesMenu", "Overige templates", "url=customTemplateList.php");
}

if (GetModuleAccess('ParticipatieGebruiker') == 1)
{
  $mnu->addItem('crm', 'Participaties', 'submenu=participaties');
  $mnu->addItem('participaties', 'Bulk rapportage', 'url=participants_bulkReport.php');
  $mnu->addItem('participaties', 'Overzicht verloop', 'url=participants_overviewCourse.php');
  $mnu->addItem('participaties', 'Overzicht posities', 'url=participants_overviewPositions.php');
  $mnu->addItem('participaties', 'Invoer mutaties', 'url=participants_inputMutations.php');
  $mnu->addItem('participaties', 'Bulk import mutaties', 'url=participants_bulkImportMutations.php');
  $mnu->addItem('participaties', 'Te printen transacties', 'url=participants_mailTransactionsList.php');

  $mnu->addItem("CrmMailTemplatesMenu", "Participanten transactie mail", "url=mailTemplate_participantenTransactieMailSettings.php");

}


$mnu->addItem('crm', 'Signaleringen', 'submenu=signaleringen');
$mnu->addItem('signaleringen', 'Overzicht signaleringen waardedaling portefeuille', 'url=signaleringportrendList.php');
$mnu->addItem('signaleringen', 'Overzicht Signaleringen stortingen/onttrekkingen', 'url=signaleringstortingenList.php');

?>