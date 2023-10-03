<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/06/10 15:19:00 $
 		File Versie					: $Revision: 1.127 $

 		$Log: vars.php,v $
 		Revision 1.127  2020/06/10 15:19:00  rvv
 		*** empty log message ***

 		Revision 1.126  2020/06/05 10:48:36  cvs
 		call 6055

 		Revision 1.125  2020/06/03 15:35:25  rvv
 		*** empty log message ***

 		Revision 1.124  2020/05/25 13:43:08  cvs
 		call 6055

 		Revision 1.123  2020/05/18 14:57:27  cvs
 		call 6055

 		Revision 1.122  2020/05/09 16:53:12  rvv
 		*** empty log message ***

 		Revision 1.121  2020/04/15 16:07:58  rvv
 		*** empty log message ***

 		Revision 1.120  2019/04/27 18:38:00  rvv
 		*** empty log message ***

 		Revision 1.119  2019/03/06 16:11:06  rvv
 		*** empty log message ***

 		Revision 1.118  2019/01/09 15:45:28  rvv
 		*** empty log message ***

 		Revision 1.117  2018/12/14 16:37:36  rvv
 		*** empty log message ***

 		Revision 1.116  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie

 		Revision 1.115  2018/02/17 19:14:43  rvv
 		*** empty log message ***

 		Revision 1.114  2017/12/20 16:58:06  rvv
 		*** empty log message ***

 		Revision 1.113  2017/12/11 06:39:36  rvv
 		*** empty log message ***

 		Revision 1.112  2017/12/02 19:10:49  rvv
 		*** empty log message ***

 		Revision 1.111  2017/08/16 15:53:57  rvv
 		*** empty log message ***

 		Revision 1.110  2017/07/26 09:51:06  rvv
 		*** empty log message ***

 		Revision 1.109  2017/04/16 10:35:15  rvv
 		*** empty log message ***

 		Revision 1.108  2016/05/29 14:03:28  rvv
 		*** empty log message ***

 		Revision 1.107  2016/01/24 09:50:50  rvv
 		*** empty log message ***

 		Revision 1.106  2016/01/03 09:13:49  rvv
 		*** empty log message ***

 		Revision 1.105  2015/12/13 08:57:50  rvv
 		*** empty log message ***

 		Revision 1.104  2015/12/06 07:43:20  rvv
 		*** empty log message ***

 		Revision 1.103  2015/09/23 14:58:14  rvv
 		*** empty log message ***

 		Revision 1.102  2015/05/08 12:02:15  cvs
 		*** empty log message ***

 		Revision 1.101  2015/03/18 15:48:41  rm
 		htmldir en rapportdir

 		Revision 1.100  2014/07/20 13:06:21  rvv
 		*** empty log message ***

 		Revision 1.99  2014/01/11 15:53:11  rvv
 		*** empty log message ***

 		Revision 1.98  2013/12/22 16:02:45  rvv
 		*** empty log message ***

 		Revision 1.97  2013/12/07 17:49:45  rvv
 		*** empty log message ***

 		Revision 1.96  2013/05/08 15:41:54  rvv
 		*** empty log message ***

 		Revision 1.95  2013/03/24 09:38:32  rvv
 		*** empty log message ***

 		Revision 1.94  2013/01/20 13:23:08  rvv
 		*** empty log message ***

 		Revision 1.93  2013/01/16 17:00:34  rvv
 		*** empty log message ***

 		Revision 1.92  2013/01/02 16:54:19  rvv
 		*** empty log message ***

 		Revision 1.91  2012/12/19 16:59:05  rvv
 		*** empty log message ***

 		Revision 1.90  2012/12/12 16:51:14  rvv
 		*** empty log message ***

 		Revision 1.89  2012/11/28 17:00:58  rvv
 		*** empty log message ***

 		Revision 1.88  2012/11/25 13:39:27  rvv
 		*** empty log message ***

 		Revision 1.87  2012/11/25 13:04:38  rvv
 		*** empty log message ***

 		Revision 1.86  2012/09/05 18:21:53  rvv
 		*** empty log message ***

 		Revision 1.85  2012/05/23 15:55:11  rvv
 		*** empty log message ***

 		Revision 1.84  2011/12/24 16:32:15  rvv
 		*** empty log message ***

 		Revision 1.83  2011/09/28 18:42:22  rvv
 		*** empty log message ***

 		Revision 1.82  2011/06/08 18:25:12  rvv
 		*** empty log message ***

 		Revision 1.81  2011/05/29 06:44:06  rvv
 		*** empty log message ***

 		Revision 1.80  2011/03/06 18:12:59  rvv
 		*** empty log message ***


*/
// hier worden variabelen voor de applicatie gedefinieerd.


//session_name("AIRS".$__appvar["bedrijf"]);
	    error_log('start vars    ', 3, 'php://stdout');
//$__modules[] = "basis";
$__appvar["templateHeader"] = "templates/kop.inc";
$__appvar["templateFooter"] = "templates/voet.inc";

$__appvar["templateContentHeader"] = "templates/content_kop.inc";
$__appvar["templateContentFooter"] = "templates/content_voet.inc";
$__appvar["templateContentFooterZonderMenu"] = "templates/zonderMenuContent_voet.inc";
$__appvar["templateRefreshFooter"] = "templates/content_voet_refresh.inc";
$__appvar["templateRefreshFooterZonderMenu"] = "templates/zonderMenuContent_voet_refresh.inc";

$__appvar["rowsPerPage"] = 25;
$__appvar["pix"] = "images/";
$__appvar["date_seperator"] = "-";

$__appvar["basedir"] =  realpath(dirname(__FILE__)."/..");
$__appvar["htmldir"] = $__appvar["basedir"] . DIRECTORY_SEPARATOR . 'html';
$__appvar["classdir"] = $__appvar["basedir"] . '/classes';
$__appvar["recordsdir"] = $__appvar["basedir"] . '/classes/records';
$__appvar["rapportdir"] = $__appvar["basedir"] . DIRECTORY_SEPARATOR . 'html' . DIRECTORY_SEPARATOR . 'rapport';

if (version_compare(phpversion(), '5.3.0', '>'))
{
  include_once ($__appvar["basedir"]."/config/modw3.php");
}
	    error_log('start vars1    ', 3, 'php://stdout');
if(isset($_SERVER["SERVER_NAME"]))
  $__appvar["server"]  =  $_SERVER['SERVER_NAME'];

$_SERVER['DOCUMENT_ROOT']=realpath($_SERVER['DOCUMENT_ROOT']);
$__appvar["baseurl"] =  str_replace($_SERVER['DOCUMENT_ROOT'],"",realpath(dirname(__FILE__)."/../html/"));

if (strstr($__appvar["baseurl"],":"))
{
  $__appvar["baseurl"] = str_replace("\\","/",$__appvar["baseurl"] );
  $__appvar["baseurl"] = str_replace($_SERVER['DOCUMENT_ROOT'],"",$__appvar["baseurl"]);
}
$__appvar["tempdir"] = $__appvar["basedir"]."/temp/";
$__appvar["iconBasePath"] = 'icon/';

$__appvar["databaseObjects"]       = "/classes/records";


include_once($__appvar["basedir"]."/config/modules.php");
include_once($__appvar["basedir"]."/classes/errorHandler.php");
include_once($__appvar["basedir"]."/classes/AE_cls_mysql.php");
include_once($__appvar["basedir"]."/classes/AE_cls_navbar.php");
include_once($__appvar["basedir"]."/classes/AE_cls_koppelObject.php");
include_once($__appvar["basedir"]."/classes/AE_cls_submenu.php");
include_once($__appvar["basedir"]."/classes/AE_cls_DHTML_Calendar.php");
include_once($__appvar["basedir"]."/classes/AE_cls_config.php");

// version information
include_once($__appvar["basedir"]."/config/version.php");
include_once($__appvar["basedir"]."/config/icons.php");
include_once($__appvar["basedir"]."/config/applicatie_functies.php");
include_once($__appvar["basedir"]."/config/template_vars.php");
include_once($__appvar["basedir"]."/config/auth.php");
include_once($__appvar["basedir"]."/config/rapportage.php");
include_once($__appvar["basedir"]."/config/dateFunctions.php");
//records

include_once($__appvar["basedir"]."/classes/formObject.php");
include_once($__appvar["basedir"]."/classes/mysqlTable.php");
include_once($__appvar["basedir"]."/classes/mysqlObject.php");
	    error_log('start vars2    ', 3, 'php://stdout');
// Vertaal functie include
$appTranslation = new AE_cls_ApplicatieVertaling();
include_once($__appvar["basedir"] . "/config/applicatieVertaling.php");


//verhuist naar applicatie_functies
loadDatabaseObjects($__appvar["basedir"].$__appvar["databaseObjects"]);

//$queryObjects = array("Client","Depotbank","Valuta","Grootboekrekening","Beleggingscategorie","Beleggingssector","Zorgplichtcategorie",
//											"Vertaling","Vermogensbeheerder","Accountmanager","Indice","BeleggingscategoriePerFonds","Risicoklassen","Fonds",
//											"BeleggingssectorPerFonds","Rentepercentage","ZorgplichtPerPortefeuille","Fondskoersen","Valutakoersen",
//											"Rekeningafschriften", "Rekeningmutaties","Portefeuilles");

$queryWizardObjects = array("Client"=>"Client",
                            "Depotbank"=>"Depotbank",
                            "Valuta"=>"Valuta",
                            "Grootboekrekening"=>"Grootboekrekening",
                            "Beleggingscategorie"=>"Beleggingscategorie",
                            "Beleggingssector"=>"Beleggingssector",
                            "Zorgplichtcategorie"=>"Zorgplichtcategorie",
											      "Vertaling"=>"Vertaling",
											      "Vermogensbeheerder"=>"Vermogensbeheerder",
											      "Accountmanager"=>"Accountmanager",
											      "Indice"=>"Indice",
											      "BeleggingscategoriePerFonds"=>"BeleggingscategoriePerFonds",
											      "Risicoklassen"=>"Risicoklassen",
											      "Fonds"=>"Fonds",
											      "BeleggingssectorPerFonds"=>"BeleggingssectorPerFonds",
											      "Rentepercentage"=>"Rentepercentage",
											      "ZorgplichtPerPortefeuille"=>"ZorgplichtPerPortefeuille",
											      "Fondskoersen"=>"Fondskoersen",
											      "Valutakoersen"=>"Valutakoersen",
											      "Rekeningafschriften"=>"Rekeningafschriften",
											      "Rekeningmutaties"=>"Rekeningmutaties",
											      "Portefeuilles"=>"Portefeuilles");

$__appvar["PerformanceBerekeningOptions"] = array("2"=>"Modified-Dietz",
																									"3"=>"Time Weighted rate of return",
																									"4"=>"Maandelijkse waardering",
																									"5"=>"Maandelijkse waardering real-time",
																									"6"=>"Attributie kwartaal waardering",
                                                  "7"=>'Modified-Dietz vanaf start',
                                                  "8"=>'Kwartaal waardering');

$__appvar["FactuurBeheerfeeBerekeningOptions"] = array("0"=>"op basis van maandelijkse waardering",
																									"1"=>"op basis van dagelijkse waardering");

$__appvar["TaalOptions"] = array("0"=>"Nederlands",
																 "1"=>"Engels",
																 "2"=>"Duits",
																 "3"=>"Frans",
																 "4"=>"Italiaans",
																 "5"=>"Pools");

$__appvar["WegingscategorieOptions"] = array("1","Aandelen-index");

$__appvar["Maanden"] = array("1"=>"januari",
														 "2"=>"februari",
														 "3"=>"maart",
														 "4"=>"april",
														 "5"=>"mei",
														 "6"=>"juni",
														 "7"=>"juli",
														 "8"=>"augustus",
														 "9"=>"september",
														 "10"=>"oktober",
														 "11"=>"november",
														 "12"=>"december"
														 );

$__appvar["Rapporten"] = array(
                             "FRONT"=>"Voorblad",
														 "OIH"=>"Onderverdeling in Hoofdsector",
														 "OIS"=>"Onderverdeling in beleggingssector",
														 "OIR"=>"Onderverdeling in regio",
														 "HSE"=>"Huidige samenstelling effectenportefeuille",
														 "OIB"=>"Onderverdeling in beleggingscategorie",
														 "AFM"=>"Onderverdeling in AFM categorien",
														 "OIV"=>"Onderverdeling in valuta",
														 "PERF"=>"Performancemeting (in EUR)",
														 "PERFG"=>"Historisch rendement",
														 "PERFD"=>"Performancemeting verdiept",
														 "VOLK"=>"Vergelijkend overzicht lopend kalenderjaar",
														 "VOLKD"=>"Vergelijkend overzicht lopend kalenderjaar verdiept",
														 "VHO"=>"Vergelijkend historisch overzicht",
														 "TRANS"=>"Transactie-overzicht",
														 "MUT"=>"Mutatie-overzicht",
														 "GRAFIEK"=>"Risico grafiek",
														 "ATT"=>"Attributie",
														 "CASHFLOW"=>"Cashflow",
														 "CASHFLOW-Y"=>"Cashflow Yearly",
														 "MODEL"=>"Model Controle",
														 "SMV"=>"Saldomutatieverloop",
														 "RISK"=>"Risk",
														 "END"=>"Achter blad",
														 "INDEX"=>"Index vergelijking",
                             "VAR"=>"Var",
                             "ZORG"=>"Zorgplicht detail",
                             "HUIS"=>"Huisfondsen",
                             "FISCAAL"=>"Fiscaal",
                             "DUURZAAM"=>"Duurzaamheid",
                             "KERNV"=>'Kerngegevens vastrentend',
                             "KERNZ"=>'Kerngegevens zakelijk',
                             "SCENARIO"=>'Scenario analyse',
                             "SCENARIO2"=>'Scenario analyse 2',
                             "TRANSFEE"=>'Transactiekosten',
                             "PORTAL"=>'Portaal',
                             "ORDERS"=>'Orders',
                             "JOURNAAL"=>'Journaal',
                             "VKM"=>'Vergelijkende kostenmaatstaf',
														 "VKMD"=>'Vergelijkende kostenmaatstaf detail',
														 "VKMS"=>'Vergelijkende kostenmaatstaf samenvatting',
                             "VKMA"=>'Vergelijkende kostenmaatstaf ex-ante',
														 "RESTRICTIES"=>'Restrictie controle',
	                           "DOORKIJK"=>'Doorkijk',
                             "DOORKIJKVR"=>'Doorkijk vastrentende waarden'
														 );

$__appvar["AABTransakties"] = array (
														 "do_A"     =>"aankoop van stukken",
														 "do_V"     =>"verkoop van stukken",
														 "do_AO"    =>"aankoop van opties",
														 "do_VO"    =>"verkoop van opties",
														 "do_CD"    =>"Contant dividend",
														 "do_CR"    =>"betaling couponrente",
														 "do_L"     =>"lichting van stukken",
                             "do_DVP"   =>"Delivery versus Payment",
														 "do_D"     =>"deponering van stukken",
                             "do_RVP"   =>"Receive versus Payment",
														 "do_L_nul" =>"lichting van stukken met koers 0",
														 "do_D_nul" =>"deponering van stukken met koers 0",
                             "do_niets" =>"negeer tt"
														 );

$__appvar["BeheerfeeBasisberekening"] = array (
  "0" =>  vt("Gemiddeld vermogen"),
  "1" =>  vt("Beginvermogen"),
  "2" =>  vt("Eindvermogen"),
  "3" =>  vt("Gecorrigeerd beginvermogen"),
  "4" =>  vt("3 Maands ultimo"),
  "5" =>  vt("4 Maands ultimo"),
  "6" =>  vt("Dagelijks gemiddelde"),
  "7"=>   vt("3-maands mnd-gem")
);

$__appvar["IXP_SoortOvereenkomst"] = array ('Administratief','Execution Only','Advies','Beheer');
$__appvar["IXP_Beleggingscategorie"] = array ('Aandelen','Obligaties','Liquiditeiten');
$__appvar["emailBronnen"] = array ('transactie'=>'Transactie','order'=>'Order','nieuweFondsen'=>'Nieuwe fondsen','portaal'=>'Portaal','signaleringen'=>'Signaleringen');

if($__appvar["superuser"])
{
  $preInstallScript = $__appvar["basedir"]."/config/PREinstall.php";
  if (file_exists($preInstallScript))  // er bestaat een installatie updatescript dus uitvoeren
  {
    include_once($preInstallScript);
    if (!unlink($preInstallScript))
    {
      echo "<br><br><br><br><br>FOUT: verwijderen van installatiescript mislukt, neem aub contact op met AIRS om deze fout te melden";
    }
  }
}

if (file_exists($__appvar["tempdir"]."update.lock"))  // bezig met update
{
	include_once($__appvar["basedir"]."/html/updateMelding.php");
	exit;
}

include_once($__appvar["basedir"]."/config/CRM_vars.php");
include_once($__appvar["basedir"]."/config/FIX_vars.php");
include_once($__appvar["basedir"]."/config/ordersVars.php");

// voor coderen van klantnamen wordt bedrij omgezet in een numerieke code
$__appvar['bedrijfsnummer'] = ord(substr(strtoupper($__appvar['bedrijf']),0,1)).ord(substr(strtoupper($__appvar['bedrijf']),1,1)).ord(substr(strtoupper($__appvar['bedrijf']),2,1));

$__appvar['TijdelijkeRapportageMaakUniek'] = " AND TijdelijkeRapportage.add_user = '".$USR."' AND TijdelijkeRapportage.sessionId = '".$sessionId."' ";


include_once($__appvar["basedir"]."/config/debugSpecial.php");

if (file_exists($__appvar["basedir"]."/config/custom_vars.inc"))  //mogelijkheid variabelen te herdifineren
{
include_once($__appvar["basedir"]."/config/custom_vars.inc");
}

if (get_magic_quotes_gpc())
{
  $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
  while (list($key, $val) = each($process))
  {
    foreach ($val as $k => $v)
    {
      unset($process[$key][$k]);
      if (is_array($v))
      {
        $process[$key][stripslashes($k)] = $v;
        $process[] = &$process[$key][stripslashes($k)];
      }
      else
      {
        $process[$key][stripslashes($k)] = stripslashes($v);
      }
    }
  }
  unset($process);
}

if (isset($_SESSION["dbName"]) && $_SESSION["dbName"] <> "")
  $_DB_resources[1]['db']     = $_SESSION["dbName"];

if(!isset($_SERVER['REQUEST_TIME']) || $_SERVER['REQUEST_TIME']==0)
	$_SERVER['REQUEST_TIME']=time();
?>