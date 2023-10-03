<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/03/14 18:41:12 $
 		File Versie					: $Revision: 1.16 $

 		$Log: rapportBackoffice.php,v $
 		Revision 1.16  2020/03/14 18:41:12  rvv
 		*** empty log message ***

*/
include_once("wwwvars.php");
$cfg = new AE_config();

$query = "SELECT
  Vermogensbeheerders.Layout,
  Vermogensbeheerders.Vermogensbeheerder,
  Vermogensbeheerders.Export_data_frontOffice,
  Vermogensbeheerders.check_module_CRM,
  Vermogensbeheerders.CrmPortefeuilleInformatie,
  max(Vermogensbeheerders.check_portaalCrmVink) as check_portaalCrmVink
FROM
  Vermogensbeheerders
Inner Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR'
GROUP BY Vermogensbeheerders.Vermogensbeheerder
ORDER BY Vermogensbeheerders.Vermogensbeheerder
LIMIT 1";

$DB = new DB();
$DB->SQL($query);
$DB->Query();
$rdata = $DB->nextRecord();


if(!$_POST && !$_GET)
{
  unset($_SESSION['factuurNummers']);
 
  if($_SESSION['backofficeSelectie']['rapport_types'])
  {
    $tmp=$_SESSION['backofficeSelectie']['rapport_types'];
    unset($_SESSION['backofficeSelectie']);
    $_SESSION['backofficeSelectie']['rapport_types']=$tmp;
  }
  else
   unset($_SESSION['backofficeSelectie']);
 
  if($rdata['CrmPortefeuilleInformatie'])
    $_SESSION['backofficeSelectie']['periode']='Maandrapportage';
  else
    $_SESSION['backofficeSelectie']['periode']='Clienten';
}
else
{
  if(isset($_POST['email']))          {    $cfg->addItem(vt('emailOpmaak') ,addslashes($_POST['email'])); }
  if(isset($_POST['onderwerp']))      {    $cfg->addItem(vt('emailOnderwerp'),addslashes($_POST['onderwerp'])); }
  if(isset($_POST['brief']))          {    $cfg->addItem(vt('briefOpmaak'),addslashes($_POST['brief'])); }
  if(isset($_POST['afzender']))       {    $cfg->addItem(vt('emailAfzender'),addslashes($_POST['afzender'])); }
  if(isset($_POST['afzenderEmail']))  {    $cfg->addItem(vt('emailAfzenderEmail'),addslashes($_POST['afzenderEmail'])); }
  if(isset($_POST['ccEmail']))        {    $cfg->addItem(vt('emailccEmail'),addslashes($_POST['ccEmail'])); }
  if(isset($_POST['bccEmail']))       {    $cfg->addItem(vt('emailbccEmail'),addslashes($_POST['bccEmail'])); }

 // $_SESSION['lastPost']=$_POST;
}

session_start();
if(!is_array($_SESSION['backofficeSelectie']))
  $_SESSION['backofficeSelectie']=array();

$_SESSION['backofficeSelectie']=array_merge($_SESSION['backofficeSelectie'],$_POST,$_GET);
$_SESSION['backofficeSelectie']['portefeuilleIntern']=$_SESSION['portefeuilleIntern'];

if(!isset($_SESSION['backofficeSelectie']['CRM_extraAdres']))
  $_SESSION['backofficeSelectie']['CRM_extraAdres']=true;

include ('rapportFrontofficeClientSelectieLayout.php');

//listarray($_SESSION['backofficeSelectie']);
if($_POST['stap']=='opmaak')
{
  $data['email']          =$cfg->getData('emailOpmaak');
  $data['onderwerp']      =$cfg->getData('emailOnderwerp');
  $data['brief']          =$cfg->getData('briefOpmaak');
  $data['afzender']       =$cfg->getData('emailAfzender');
  $data['afzenderEmail']  =$cfg->getData('emailAfzenderEmail');
  $data['ccEmail']        =$cfg->getData('emailccEmail');
  $data['bccEmail']       =$cfg->getData('emailbccEmail');


  include('rapportBackofficeClientOpmaak.php');
}
elseif($_POST['stap']=='samenvatting')
{
  include('rapportBackofficeClientSamenvatting.php');
}
elseif($_POST['stap']=='productie')
{
  include('rapportBackofficeClientProductie.php');
}
else
{
  include('rapportBackofficeClientSelectie.php');
}



