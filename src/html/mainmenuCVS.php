<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2012/03/09 09:23:28 $
 		File Versie					: $Revision: 1.1 $

 		$Log: mainmenuCVS.php,v $
 		Revision 1.1  2012/03/09 09:23:28  cvs
 		*** empty log message ***
 		
 		Revision 1.116  2011/06/02 15:03:40  rvv
 		*** empty log message ***
 		
 		Revision 1.115  2011/05/14 10:50:07  rvv
 		*** empty log message ***



*/
include_once("wwwvars.php");
include_once("../classes/AE_cls_menu.php");

$mnu = New Menu();

$mnu->addItem("","Bedrijf","submenu=bedrijf");
if ( $__appvar['master'])
{
  include_once("dbsMenu.php");    
}
if(!isset($__appvar["crmOnly"]))
{

$mnu->addItem("","Stamgegevens","submenu=stamgegevens");
$mnu->addItem("","Koersen","submenu=koersen");
//if(checkAccess("superapp"))
$mnu->addItem("","Transacties","submenu=transacties");

$mnu->addItem("","Rapportage","submenu=rapportage");

$klantmenuFile = "custom/Menu".$__appvar['bedrijfsnummer'].".php";
if (file_exists($klantmenuFile))
{
  include_once($klantmenuFile);
  $mnu->addItem("",$klantMenuText,"submenu=klantmenu");
}



if (GetModuleAccess("ORDER"))
{

  $mnu->addItem("","Ordering","submenu=ordering");
  if (file_exists("ordersList.php"))
  {

    $mnu->addItem("ordering","Order Overzicht","url=ordersList.php");
    $mnu->addItem("ordering","Order regels","url=orderregelsList.php");
    $mnu->addItem("ordering","Nieuwe order","url=ordersEdit.php?action=new&returnUrl=ordersList.php?status=ingevoerd");
    for($x=0;$x<count($__ORDERvar["status"]);$x++)
    {
      $mnu->addItem("ordering","Order Overzicht met status ".$__ORDERvar["status"][$x],"url=ordersList.php?status=".urlencode($__ORDERvar["status"][$x]));
    }
  }
}

if (GetModuleAccess("BOEKEN"))
 $boeken = true;
else
 $boeken = false;

$mnu->addItem("","Jaarafsluiting","submenu=jaarafsluiting");
/*
// CRM module
*/
}
$mnu->addItem("","Help","submenu=help");
if (GetModuleAccess("CRM"))
{
  $mnu->addItem("","CRM","submenu=crm");
  include("CRM_menuInclude.php");
}

$mnu->addItem("","Uitloggen","url=login.php?logout=true",0,"_top");
$mnu->addItem("","Home","url=welcome.php");


$mnu->addItem("bedrijf","Digitale document instellingen","url=dd_setup.php");
$mnu->addItem("bedrijf","Digitale document inlezen","url=dd_inlees.php");

if(checkAccess("superapp") && $__appvar['master'] == true )
{
	$mnu->addItem("bedrijf","Update historie","url=updatehistoryList.php");
	$mnu->addItem("bedrijf","Exporteren naar Queue","url=queueExport.php");
	$mnu->addItem("bedrijf","Importeren access database","url=updateImportDatabase.php");
}
else
{
	$mnu->addItem("bedrijf","Update","url=queueImport.php");
  if ($__appvar["bedrijf"] == "HEN")
  {
    $mnu->addItem("bedrijf","Handmatige Iphone Update","url=HEN_dbDump.php");
  }
}

if(checkAccess("superapp"))
{
  $mnu->addItem("bedrijf","Bedrijfsgegevens","url=bedrijfsgegevensList.php",1);
  $mnu->addItem("bedrijf","instellingen","url=bedrijfInstellingen.php");
}
$mnu->addItem("bedrijf","Bedrijf consistentie controle","url=BedrijfConsistentieControle.php");
$mnu->addItem("bedrijf","Klant mutaties","url=klantmutatiesList.php");

$mnu->addItem("stamgegevens","Algemene stamgegevens","submenu=stamgegevens_algemeen");
$mnu->addItem("stamgegevens","Onderhoud vermogensbeheerders","submenu=stamgegevens_onderhoudvermogenbeh");
$mnu->addItem("stamgegevens","Onderhoud fondsen","submenu=stamgegevens_onderhoudfondsen");
$mnu->addItem("stamgegevens","Onderhoud portefeuilles","submenu=stamgegevens_onderhoudportef");

$mnu->addItem("stamgegevens_algemeen","Depotbanken","url=depotbankList.php");
$mnu->addItem("stamgegevens_algemeen","Valutas","url=valutaList.php");
$mnu->addItem("stamgegevens_algemeen","Grootboekrekeningen","url=grootboekrekeningList.php");
$mnu->addItem("stamgegevens_algemeen","Beleggingscategorien","url=beleggingscategorieList.php");
$mnu->addItem("stamgegevens_algemeen","Beleggingssectoren","url=beleggingssectorList.php");
$mnu->addItem("stamgegevens_algemeen","Zorgplichtcategorien","url=zorgplichtcategorieList.php");
$mnu->addItem("stamgegevens_algemeen","Transactietypes","url=transactietypeList.php");
$mnu->addItem("stamgegevens_algemeen","Vertalingen","url=vertalingList.php");
$mnu->addItem("stamgegevens_algemeen","Regio's","url=regiosList.php");
$mnu->addItem("stamgegevens_algemeen","Attributiecategorien","url=attributiecategorienList.php");
$mnu->addItem("stamgegevens_algemeen","Beurzen","url=beurzenList.php");
$mnu->addItem("stamgegevens_algemeen","BB landcodes","url=bblandcodesList.php");
$mnu->addItem("stamgegevens_algemeen","Rating","url=ratingList.php");
$mnu->addItem("stamgegevens_algemeen","Bewaarders","url=bewaardersList.php");
$mnu->addItem("stamgegevens_algemeen","Emittenten","url=emittentenList.php");
//$mnu->addItem("stamgegevens_algemeen","TI toewijzing grootboek","url=importgrootboektoewijzingList.php");

if(checkAccess("superapp"))
{
	$mnu->addItem("stamgegevens_algemeen","AAB transaktiescodes","url=aabtransaktiecodesList.php");
}

if(checkAccess("superapp"))
{
	$mnu->addItem("stamgegevens_algemeen","Gebruikers","url=gebruikerList.php");
}

$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Vermogensbeheerders","url=vermogensbeheerderList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Accountmanagers","url=accountmanagerList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Remisiers","url=remisiersList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Indices per vermogensbeheerder","url=indiceList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Index per Beleggingscategorie","url=indexperbeleggingscategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Index per AttributieCategorie","url=indexperattributiecategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Beleggingscategorien per hoofdcategorie","url=categorienperhoofdcategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Beleggingscategorien per vermogensbeheerder","url=categorienpervermogensbeheerderList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Beleggingssectoren per hoofdsector","url=sectorperhoofdsectorList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Beleggingscategorien per wegingscategorie","url=beleggingscategorieperwegingscategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Fondsen buiten beheerfee","url=fondsenbuitenbeheerfeeList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Risicoklassen","url=risicoklassenList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Korting per depotbank","url=kortingenperdepotbankList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Valuta per regio","url=valutaperregioList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Attributie per grootboekrekening","url=attributiepergrootboekrekeningList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Grootboekrekening per vermogensbeheerder","url=grootboekpervermogensbeheerderList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Geautomatiseerde rapporten","url=autorunList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Eigen rapport teksten","url=custom_txtList.php?type=rapport");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Zorgplicht per risicoklasse","url=zorgplichtperrisicoklasseList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Zorgplicht per beleggingscategorie","url=zorgplichtperbeleggingscategorieList.php");
$mnu->addItem("stamgegevens_onderhoudvermogenbeh","Keuze per vermogensbeheerder","url=keuzepervermogensbeheerderList.php");

$mnu->addItem("stamgegevens_onderhoudfondsen","Fondsen","url=fondsList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen","Fondsomschrijving vanaf datum","url=fondsomschrijvingvanafList.php");

//$mnu->addItem("stamgegevens_onderhoudfondsen","Participatie verloop","url=fondsparticipatieverloopList.php");

$mnu->addItem("stamgegevens_onderhoudfondsen","Rentepercentages per fonds","url=rentepercentageList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen","Zorgplicht per fonds","url=zorgplichtperfondsList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen","Beleggingssectoren per fonds","url=beleggingssectorperfondsList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen","Beleggingscategorien per fonds","url=beleggingscategorieperfondsList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen","Benchmarkverdeling","url=benchmarkverdelingList.php");
$mnu->addItem("stamgegevens_onderhoudfondsen","Fondsen per emittent","url=emittentperfondsList.php");

$mnu->addItem("stamgegevens_onderhoudportef","Clienten","url=clientList.php");
$mnu->addItem("stamgegevens_onderhoudportef","Portefeuilles","url=portefeuillesList.php");
$mnu->addItem("stamgegevens_onderhoudportef","Geconsolideerde portefeuilles","url=geconsolideerdeportefeuillesList.php");
$mnu->addItem("stamgegevens_onderhoudportef","Zorgplicht parameters per portefeuilles","url=zorgplichtperportefeuilleList.php");
$mnu->addItem("stamgegevens_onderhoudportef","Rekeningen","url=rekeningenList.php");
$mnu->addItem("stamgegevens_onderhoudportef","Model portefeuilles","url=modelportefeuillesList.php");
$mnu->addItem("stamgegevens_onderhoudportef","Beleggingsplan","url=beleggingsplanList.php");




if(checkAccess("superapp"))
{
	$mnu->addItem("koersen","Koersimport","url=koersImport.php");
	$mnu->addItem("koersen","Koersimport AAB","url=koersImportAAB.php");
}

$mnu->addItem("koersen","Fondskoersen","url=fondskoersenList.php");
$mnu->addItem("koersen","Schaduwkoersen","url=schaduwkoersenList.php");
$mnu->addItem("koersen","Valutakoersen","url=valutakoersenList.php");

if(checkAccess("superapp"))
{
	$mnu->addItem("koersen","Koerscontrole","url=koersControle.php");
}

$mnu->addItem("transacties","Rekeningafschriften","url=rekeningafschriftenList.php");
$mnu->addItem("transacties","Rekeningmutaties","url=rekeningmutatiesList.php");
$mnu->addItem("transacties","Portefeuille index","url=historischeportefeuilleindexList.php");
if($boeken)
  $mnu->addItem("transacties","Voorlopigerekeningafschriften","url=voorlopigeRekeningafschriftenList.php");
if(checkAccess())
  $mnu->addItem("transacties","Verwerken","url=transactiesVerwerken.php");
$mnu->addItem("transacties","Memoriaal","url=rekeningafschriftenList.php?memoriaal=true");
if($boeken)
  $mnu->addItem("transacties","Voorlopige Memoriaal","url=voorlopigeRekeningafschriftenList.php?memoriaal=true");
if(checkAccess("superapp"))
{
$mnu->addItem("transacties","Tijdelijk importbestand","url=tijdelijkerekeningmutatiesList.php");
$mnu->addItem("transacties","Transactie-Import","url=transaktieImport.php");
$mnu->addItem("transacties","Consistentie-Controle","url=consistentieControle.php");
$mnu->addItem("transacties","Portefeuilles-Controle","submenu=portefeuillesControle");
$mnu->addItem("transacties","Saldo's herberekenen","url=saldosHerberekenen.php");
$mnu->addItem("transacties","Positie-Import","submenu=positieImport");
$mnu->addItem("transacties","Index berekening","url=indexBerekeningSelectie.php");
$mnu->addItem("transacties","Saldi naar Transacties","url=importSaldi.php");
$mnu->addItem("transacties","");
$mnu->addItem("transacties","Transacties converteren","url=convert_transacties.php");
$mnu->addItem("transacties","Posities converteren","url=convert_positie.php");
$mnu->addItem("transacties","Tijdelijk Posities Overzicht","url=tijdelijkepositielijstList.php");
$mnu->addItem("transacties","Posities Overzicht","url=positielijstList.php");
}
$mnu->addItem("transacties","");
$mnu->addItem("transacties","Bestandsvergoedingen","url=bestandsvergoedingenList.php");

$mnu->addItem("rapportage","Front-office","url=rapportFrontofficeClientSelectie.php");
//$mnu->addItem("rapportage","Front-office II","url=rapportSelectie.php");
$mnu->addItem("rapportage","Back-office" ,"url=rapportBackoffice.php" );
//$mnu->addItem("rapportage","Back-office II" ,"url=rapportSelectie.php?type=dagRapportage" );
$mnu->addItem("rapportage","XLS-selectie" ,"url=rapportXlsSelectie.php" );
$mnu->addItem("rapportage","Report-builder" ,"url=reportBuilder.php" );
$mnu->addItem("rapportage","Query wizard" ,"url=queryWizard.php?type=all" );

$mnu->addItem("portefeuillesControle","Stroeve ISIN","url=portefeuillesControle.php?bank=stroeve");
$mnu->addItem("portefeuillesControle","Stroeve StroeveCode","url=portefeuillesControle.php?bank=stroeveEigen");
$mnu->addItem("portefeuillesControle","Gilissen","url=portefeuillesControle.php?bank=gilis");
$mnu->addItem("portefeuillesControle","Gilissen VT","url=portefeuillesControle.php?bank=gilisVt");
$mnu->addItem("portefeuillesControle","Binck bank","url=portefeuillesControle.php?bank=binck");
$mnu->addItem("portefeuillesControle","ABN-AMRO","url=portefeuillesControle.php?bank=abn");
$mnu->addItem("portefeuillesControle","ABN-AMRO Belgie","url=portefeuillesControle.php?bank=abnbe");
$mnu->addItem("portefeuillesControle","SNS","url=portefeuillesControle.php?bank=sns");
$mnu->addItem("portefeuillesControle","SNS Securities","url=portefeuillesControle.php?bank=snssec");
$mnu->addItem("portefeuillesControle","ANT","url=portefeuillesControle.php?bank=ant");
$mnu->addItem("portefeuillesControle","Rabo","url=portefeuillesControle.php?bank=rabo");
$mnu->addItem("portefeuillesControle","Rabo via transactiebestand","url=portefeuillesControle.php?bank=raboTrans");
$mnu->addItem("portefeuillesControle","Rabo via positiemap","url=portefeuillesControle.php?bank=raboExcel");
$mnu->addItem("portefeuillesControle","Rothschild","url=portefeuillesControle.php?bank=bpere");

$mnu->addItem("positieImport","Stroeve ISIN","url=positieImport.php?bank=stroeve");
$mnu->addItem("positieImport","Gilissen","url=positieImport.php?bank=gilis");

$mnu->addItem("rapportage","Facturering Beheerfee","url=factuurSelectie.php");
if(GetModuleAccess('FACTUURHISTORIE'))
  $mnu->addItem("rapportage","Factuur historie","url=factuurhistorieList.php");
//$mnu->addItem("rapportage","Facturering Beheerfee II","url=rapportSelectie.php?type=factuur");

$mnu->addItem("rapportage","Bestandsvergoeding","url=bestandsvergoedingSelectie.php");


if(checkAccess("superapp"))
	$mnu->addItem("jaarafsluiting","Jaarafsluiting","url=jaarafsluiting.php");


if ( $__appvar['dbs'] )
{
  $mnu->addItem("help", "DBS", "submenu=clientdbs");
  $mnu->addItem("clientdbs", "melding aanmaken", "url=dbsClientEdit.php?action=new");
  $mnu->addItem("clientdbs", "openstaand", "url=dbsClientList.php?q=open");
  $mnu->addItem("clientdbs", "alles", "url=dbsClientList.php?q=all");
      
}
  $mnu->addItem("help","SysteemInfo","url=help.php");
  $mnu->addItem("help","DatabaseInfo","url=helpDataBase.php");
  $mnu->addItem("help","Online-support","url=http://eu.ntrsupport.com/inquiero/anonymous2.asp?skclient=&lang=nl&con=1&online=1&bonline=1&login=38378&oper=airs",0,"_blank");



$menuList =  $mnu->createMenu();
?>