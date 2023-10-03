<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2016/10/26 10:46:35 $
 		File Versie					: $Revision: 1.13 $

 		$Log: aehelper.php,v $
 		Revision 1.13  2016/10/26 10:46:35  rvv
 		*** empty log message ***
 		
 		Revision 1.12  2016/10/26 10:39:15  rvv
 		*** empty log message ***
 		
 		Revision 1.11  2016/10/23 11:30:11  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2016/10/16 15:04:38  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2013/03/29 07:46:04  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2013/03/29 07:29:03  rvv
 		*** empty log message ***
 		
 		Revision 1.7  2009/02/17 12:52:37  cvs
 		*** empty log message ***
 		
 		Revision 1.6  2009/01/09 09:25:28  cvs
 		field info

 		Revision 1.5  2008/12/31 10:39:40  rvv
 		*** empty log message ***

 		Revision 1.4  2007/09/11 13:59:57  cvs
 		*** empty log message ***

 		Revision 1.3  2007/09/11 13:43:30  cvs
 		helpfuncties uitbreiden


*/

  include_once("wwwvars.php");
  if ($_GET["a"])
    $limit = $_GET["a"];
  else
    $limit = 25;


if($_GET['rapportageInstellingenVerzenden']==1)
{
  $db=new DB();
  $query="SELECT portefeuille,rapportageVinkSelectie FROM CRM_naw WHERE portefeuille <> ''";
  $db->SQL($query);
  $db->Query();
  $instellingen=array();
  $aantal=$db->records();
  $n=0;
  logscherm("Gevonden portefeuilles $aantal",true);
  while($data=$db->nextRecord())
  {
    $tiental=round($n/$aantal*10);
    if($tiental<>$lastTiental)
      logscherm(($tiental*10)."% opgehaald.",true);

    $tmp=unserialize($data['rapportageVinkSelectie']);
    if(count($tmp) > 0)
    {
      $instellingen[$data['portefeuille']] = $data['rapportageVinkSelectie'];
      $n++;
    }
    else
      logscherm("Geen waarden gevonden voor ".$data['portefeuille']."",true);
    $lastTiental=$tiental;


  }
  echo "<br>\n($n) rapportage vink selecties opgehaald.";

  $n=0;
  $db=new DB(2);
  $aantal=count($instellingen);
  foreach($instellingen as $portefeuille=>$vinkjes)
  {
    $tiental=round($n/$aantal*10);
    if($tiental<>$lastTiental)
      logscherm(($tiental*10)."% verzonden.",true);

    $query="SELECT id FROM rapportageInstellingen WHERE portefeuille='".mysql_real_escape_string($portefeuille)."'";
    if($db->QRecords($query) > 0)
      $query="UPDATE rapportageInstellingen SET bedrijf='".$__appvar["bedrijf"]."', rapportageVinkSelectie='".mysql_real_escape_string($vinkjes)."', change_date=now(), change_user='$USR' WHERE portefeuille='".mysql_real_escape_string($portefeuille)."'";
    else
      $query="INSERT INTO rapportageInstellingen SET bedrijf='".$__appvar["bedrijf"]."',portefeuille='$portefeuille', rapportageVinkSelectie='".mysql_real_escape_string($vinkjes)."', add_date=now(), change_date=now(), add_user='$USR', change_user='$USR' ";
    $db->SQL($query);
    if($db->Query())
      $n++;
    $lastTiental=$tiental;
  }
  echo "<br>\n($n) rapportage vink selecties verzonden.";
  exit;
}
if($_GET['rapportageInstellingenInlezen']==1)
{
  if($__appvar["bedrijf"] == "TEST")
  {
    $db=new DB(2);
    $query="SELECT portefeuille,rapportageVinkSelectie FROM rapportageInstellingen";
    $db->SQL($query);
    $db->Query();
    $aantal=$db->records();
    $n=0;
    while($data=$db->nextRecord())
    {
      $tiental=round($n/$aantal*10);
      if($tiental<>$lastTiental)
        logscherm(($tiental*10)."% opgehaald.",true);

      $tmp=unserialize($data['rapportageVinkSelectie']);
      if(count($tmp) > 0);
      $instellingen[$data['portefeuille']]=$data['rapportageVinkSelectie'];
      $lastTiental=$tiental;
      $n++;
    }
    $db=new DB();
    $n=0;
    foreach($instellingen as $portefeuille=>$instellingen)
    {
      $tiental=round($n/$aantal*10);
      if($tiental<>$lastTiental)
        logscherm(($tiental*10)."% ingelezen.",true);

      $query="SELECT id FROM CRM_naw WHERE portefeuille='$portefeuille'";
      if($db->QRecords($query) > 0)
        $query="UPDATE CRM_naw SET rapportageVinkSelectie='".mysql_real_escape_string($instellingen)."', change_date=now(), change_user='$USR' WHERE portefeuille='$portefeuille'";
      else
        $query="INSERT INTO CRM_naw SET debiteur=1,aktief=1,zoekveld='$portefeuille',naam='$portefeuille',portefeuille='$portefeuille', rapportageVinkSelectie='".mysql_real_escape_string($instellingen)."', add_date=now(), change_date=now(), add_user='$USR', change_user='$USR' ";
      $db->SQL($query);
      if($db->Query())
        $n++;
      $lastTiental=$tiental;
    }
    echo "<br>\n($n) rapportage vink selecties ingelezen.";
  }
  else
  {
    echo "Inlezen niet mogelijk.";
    exit;
  }
  exit;
}

//  $_DB_resources[2]['server'] = "develop.aeict.nl";
//  $_DB_resources[2]['user']   = "airslogger";
//  $_DB_resources[2]['passwd'] = "a1i2";
//  $_DB_resources[2]['db']     = "airs_queue";


  $melding = array();

  $melding["general"]["version"] = $PRG_VERSION;
  $melding["general"]["release"] = $PRG_RELEASE;
  $melding["general"]["appvar"]  = $__appvar;
  $melding["general"]["server"]  = $_SERVER;
  $melding["general"]["session"] = $_SESSION;



  $db = new DB();
  $db->SQL("show table status from ".$_DB_resources[1]['db']);
  $db->Query();
  while ($data = $db->nextRecord())
  {
    $dbArray[] = $data;
    $dbName[] = $data["Name"] ;
  }

  $dbinfo["dbinfo"] = $dbArray;

  for ($q=0 ;$q < count($dbName); $q++)
  {
    $db->SQL("show fields from ".$dbName[$q]);
    $db->Query();
    while ($data = $db->nextRecord())
    {
      $fldArray[] = array($dbName[$q],$data);
    }
  }
  $dbfields["fieldinfo"] = $fldArray;
  $extArray = array("php","html","js",'png');

  include_once("../config/helperFunctions.php");

  $filelist =searchdir("./../",5);
  $fileInfo = array();

  while (list($key, $val) = each($filelist))
  {
    if (validExt($val))
    {
      $fileInfo[] = substr($val,2)."|".date("d-m-Y H:i", filemtime($val))."|".filesize($val)."|". getVersie($val)."|".md5_file($val) ;
    }
  }

  $handle = fopen ("../config/local_vars.php", "r");
  while (!feof ($handle))
  {
    $buffer .= fgets($handle, 4096);
  }
  fclose ($handle);



  $log = new  DB(2);

  $query = "INSERT INTO terugmelding SET ";
  $query  .= "  datum = NOW()";
  $query  .= ", bedrijf = '".$__appvar['bedrijf']."'";
  $query  .= ", txt = '".mysql_escape_string(serialize($melding))."'";
  $query  .= ", dbinfo = '".mysql_escape_string(serialize($dbinfo))."'";
  $query  .= ", dbfields = '".mysql_escape_string(serialize($dbfields))."'";
  $query  .= ", fileinfo = '".addslashes(serialize($fileInfo))."'";
  $query  .= ", local_vars = '".mysql_escape_string($buffer)."'";

  $log->SQL($query);
  if ($log->query())
  {
    if(!$silent)
      echo "Meldingen zijn verstuurd!";
  }
  else
  {
    if(!$silent)
      echo "Melding kunnen NIET verstuurd worden, meldt dit bericht aan AIRS a.u.b.";
  }


?>