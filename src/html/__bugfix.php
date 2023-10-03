<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2020/06/26 04:42:47 $
  File Versie					: $Revision: 1.1 $

  $Log: __jbl.php,v $
  Revision 1.1  2020/06/26 04:42:47  cvs

 */

include_once("wwwvars.php");

session_start();
global $__debug;
$__debug = true;
$dbP = new db(DBportaal);
$dbL = new db();


if ($_GET["action"] == "verwerk")
{

  foreach ($_SESSION["portaalCons_koppel"] as $id=>$clntid)
  {
    $query = "
    UPDATE dd_reference SET 
      module_id = {$clntid}, 
      clientID = {$clntid},
      change_date = NOW(),
      change_user = 'Airscorr'
    WHERE id = {$id}      
      ";
    $dbP->executeQuery($query);
    debug($query);
  }
  debug($_SESSION["portaalCons_koppel"]);
  debug($_SESSION["portaalCons_data"]);
  exit;
}


$query = "SELECT * FROM clienten ";

$dbP->executeQuery($query);

while ($rec = $dbP->nextRecord())
{
  $clntPort[$rec["portefeuille"]] = array(
    "clnt_id" => $rec["id"],
    "rel_id" => $rec["rel_id"]
  );
}
debug($clntPort, "portefeuile => clientID");



$query = "
SELECT 
  dd_reference.*,
  CRM_naw.id as c_id,
  CRM_naw.portefeuille as c_port
FROM 
  dd_reference
left join CRM_naw on 
   dd_reference.module_id = CRM_naw.id
WHERE dd_reference.portaalKoppelId > 0 AND dd_reference.module = 'CRM_naw' ORDER BY dd_reference.portaalKoppelId DESC ";

debug($query);
$dbL->executeQuery($query);
while ($airshost_refrec = $dbL->nextRecord())
{

  $query = "SELECT * FROM dd_reference WHERE id= ".$airshost_refrec["portaalKoppelId"]; // vanuit het portaal
  $ddrPortaal = $dbP->lookupRecordByQuery($query);
  if ($_GET["dif"] == 1 AND ($clntPort[$airshost_refrec["c_port"]]["clnt_id"] == $ddrPortaal["module_id"]))
  {
    continue;
  }

  $out[] = array(
    "h_id" => $airshost_refrec["id"],
    "h_rel_id" => $airshost_refrec["module_id"],
    "h_crm_id" => $airshost_refrec["c_id"],
    "h_portefeuille" => $airshost_refrec["c_port"],
    "h_koppel_id" => $airshost_refrec["portaalKoppelId"],
    "p_id" => $ddrPortaal["id"],
    "p_module_id" => $ddrPortaal["module_id"],
    "p_portaalKoppelId" => $ddrPortaal["portaalKoppelId"],
    "p_module" => $ddrPortaal["module"],
    "p_description" => $ddrPortaal["description"],
    "p_filename" => $ddrPortaal["filename"],
    "c_clnt_id" => $clntPort[$airshost_refrec["c_port"]]["clnt_id"],
    "c_rel_id" => $clntPort[$airshost_refrec["c_port"]]["rel_id"],
    "koppel" => ($clntPort[$airshost_refrec["c_port"]]["clnt_id"] == $ddrPortaal["module_id"])?"correct":"wijkt af",
    "p_change_date" => $ddrPortaal["change_date"]
  );
  if ((int)$ddrPortaal["id"] > 0)
  {
    $verwerkIds[$ddrPortaal["id"]] =  $clntPort[$airshost_refrec["c_port"]]["clnt_id"];
  }


}

$_SESSION["portaalCons_data"] = $out;
$_SESSION["portaalCons_koppel"] = $verwerkIds;
debug($out);
if ($_GET["dif"] == 1 )
{
?>
<form >

  <h2>verwerk afwijkende?</h2>
  <input type="hidden" name="action" value="verwerk">
  <input type="submit" value="verwerken..">


</form>
<?php
}

