<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2019/08/30 14:44:04 $
 		File Versie					: $Revision: 1.38 $

 		$Log: portefeuillesEdit.php,v $


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
$ajx = new AE_cls_ajaxLookup(array("client"));
$ajx->changeModuleTriggerID("client", "Client");

$subHeader  = vt("portefeuille");
$mainHeader = vt("muteren");
$editcontent["pageHeader"] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$__funcvar["listurl"]  = "portefeuillesList.php";
$__funcvar["location"] = "portefeuillesEdit.php";

$object = new Portefeuilles();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar  = $__appvar;

$editcontent["jsincludes"] .= "<script language=JavaScript src=\"javascript/tabbladen.js\" type=text/javascript></script>\n";
$editcontent["jsincludes"] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";

if($_GET['action'] == "new")
	$editcontent["body"] = " onLoad=\"javascript:tabOpen('0');vermogensbeheerderChanged();\" ";
else
	$editcontent["body"] = " onLoad=\"javascript:tabOpen('0');\" ";

$editObject->template = $editcontent;

$editObject->formTemplate = "portefeuillesTemplate.html";
$editObject->usetemplate = true;

$data = $_GET;
$action = $data["action"];

$DB = new DB();

if($id<>0)
{
  $db=new DB();
  $query="SELECT Portefeuilles.Vermogensbeheerder FROM Portefeuilles WHERE id='" . $id . "'";
  $db->SQL($query);
  $db->Query();
  $verm = $db->nextRecord();

  $query = "SELECT
VermogensbeheerdersPerBedrijf.bedrijf,
count(aantal.Vermogensbeheerder) as aantal
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN VermogensbeheerdersPerBedrijf as aantal ON VermogensbeheerdersPerBedrijf.Bedrijf = aantal.Bedrijf
WHERE VermogensbeheerdersPerBedrijf.Vermogensbeheerder='" . $verm['Vermogensbeheerder'] . "' GROUP BY VermogensbeheerdersPerBedrijf.bedrijf";
  $db->SQL($query);
  $db->Query();
  $bedrijf = $db->nextRecord();
  if ($bedrijf['aantal'] > 1)
  {
    $accountmanagerQuery = "SELECT
Accountmanagers.Accountmanager AS `Value`,
concat(Accountmanagers.Accountmanager, ' - ',Accountmanagers.Vermogensbeheerder)
FROM
Accountmanagers
INNER JOIN VermogensbeheerdersPerBedrijf ON Accountmanagers.Vermogensbeheerder = VermogensbeheerdersPerBedrijf.Vermogensbeheerder
WHERE VermogensbeheerdersPerBedrijf.bedrijf='" . $bedrijf['bedrijf'] . "'
ORDER BY if(Accountmanagers.Vermogensbeheerder='".$verm['Vermogensbeheerder']."',0,1),Accountmanagers.Vermogensbeheerder,Accountmanagers.Accountmanager";
  }
  else
  {
    $accountmanagerQuery = "SELECT Accountmanagers.Accountmanager,Accountmanagers.Accountmanager FROM  Accountmanagers WHERE Accountmanagers.Vermogensbeheerder = '" . $verm['Vermogensbeheerder'] . "' ORDER BY Accountmanagers.Accountmanager";
  }
  $object->setOption('Risicoklasse', 'select_query', "SELECT Risicoklassen.Risicoklasse,Risicoklassen.Risicoklasse FROM  Risicoklassen WHERE Risicoklassen.Vermogensbeheerder = '" . $verm['Vermogensbeheerder'] . "'");
  $object->setOption('Accountmanager', 'select_query', $accountmanagerQuery);
  $object->setOption('tweedeAanspreekpunt', 'select_query', $accountmanagerQuery);
  $object->setOption('Remisier', 'select_query', "SELECT Remisiers.Remisier,Remisiers.Remisier FROM Remisiers,Portefeuilles  WHERE Remisiers.Vermogensbeheerder = '" . $verm['Vermogensbeheerder'] . "'");
  $object->setOption('SoortOvereenkomst', 'select_query', "SELECT KeuzePerVermogensbeheerder.waarde,KeuzePerVermogensbeheerder.waarde FROM KeuzePerVermogensbeheerder WHERE KeuzePerVermogensbeheerder.categorie='soortovereenkomsten' AND Vermogensbeheerder='".$verm['Vermogensbeheerder']."' ");
}
$object->data['fields']["Taal"]["form_options"] = $__appvar["TaalOptions"];

if($_GET['action'] == "new")
{
 	for($a=0; $a <=7; $a++)
    if($object->data['fields']['BeheerfeeMethode']['default_value'] == $a)
			$editObject->formVars["Methode_".$a] = "checked";
}  
// if edit
if($action == "edit")
{

	$DB->SQL("SELECT BeheerfeeTransactiefeeKosten,BeheerfeePerformanceDrempelBedrag,BeheerfeePerformancePercentage, BeheerfeePerformanceDrempelPercentage,BeheerfeeMinJaarBedrag,BeheerfeeBTW,BeheerfeeMethode,BeheerfeeRemisiervergoedingsPercentage,BeheerfeeTeruggaveHuisfondsenPercentage,BeheerfeeAdministratieVergoeding  FROM Portefeuilles WHERE id = '".$id."'");
	$DB->Query();
	$bf = $DB->NextRecord();

	for($a=0; $a <=7; $a++)
		if($bf["BeheerfeeMethode"] == $a)
			$editObject->formVars["Methode_".$a] = "checked";

 
}
if(isset($data['kleurcode']) && is_array($data['kleurcode']))
  $data['kleurcode']=serialize($data['kleurcode']);



$editObject->controller($action,$data);

$con=new AIRS_consolidatie();
$Ps=$con->ophalenPortefeuillesViaVp($object->get('Portefeuille'));

if(count($Ps)>0)
{
  
  $object->setPropertie('consolidatie', 'form_extra', 'disabled');
  $editObject->formVars["consolidatieVasteStartStyle"]='';
  $editObject->formVars["consolidatieVasteEindStyle"]='';
}
else
{
  $editObject->formVars["consolidatieVasteStartStyle"]='style="display:none"';
  $editObject->formVars["consolidatieVasteEindStyle"]='style="display:none"';
}
$object->setOption('Client', 'form_type', 'text');
//$object->setOption('Client', 'form_extra', 'READONLY');

$huidigeKleurcode=$object->get('kleurcode');
$huidigeKleurcode=unserialize($huidigeKleurcode);
$editObject->formVars["kleurcode"]='';
for($i=0;$i<3;$i++)
{
//	$editObject->formVars["kleurcode"] .= "<input name=\"kleurcode[]\" value=\"".$huidigeKleurcode[$i]."\" size=\"3\">";
}
$kleuren=array(0 => 'R',1 => 'G',2 => 'B');
foreach ( $kleuren as $kleurKey => $kleur ) {

$editObject->formVars["kleurcode"] .= ' <input size="3" maxlength="3" type="text" value="'.$huidigeKleurcode[$kleurKey].'" class="colorp" id="kleurcode_'.$kleur.'" data-group="kleurcode" name="kleurcode[]" >';
}
$editObject->formVars["kleurcode"] .= '<div id="kleurcode-colorPicker" class="colorpicker-component colorDisplayField" style="" title="Using format option">
                <input type="hidden" class="form-control input-lg" value=""/><span class="input-group-addon" ><i></i></span></div>';



$editObject->JSinsert = $ajx->getJsInTags();
echo $editObject->getOutput();


if ($result = $editObject->result)
{
	$con=new AIRS_consolidatie();
	$VPs=$con->ophalenVPsViaPortefeuille($object->get('Portefeuille'));
	if(count($VPs)>0)
  {
    $con->bijwerkenConsolidaties($VPs);
  }
	elseif($object->get('consolidatie')==1)
  {
    $con->bijwerkenConsolidaties($object->get('Portefeuille'));
  }
  if($object->get('Client')<>'')
  {
    $query = "UPDATE Clienten SET change_date=now() WHERE Client='" . $object->get('Client') . "'";
    $DB->SQL($query);
    $DB->Query();
  }
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>