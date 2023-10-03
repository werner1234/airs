<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.7 $
 		
 		$Log: crm_naw_cfEdit.php,v $
 		Revision 1.7  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.6  2008/06/30 06:53:04  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2008/03/25 14:01:42  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2008/02/20 12:04:30  rvv
 		GET->POST omzetting
 		
 		Revision 1.3  2007/11/27 13:19:18  cvs
 		CRM
 		- verjaardaglijst
 		- velden omzetten van extra velden naar naw
 		- excel van tijdelijke rekening mutaties
 		
 		Revision 1.2  2007/08/02 14:41:29  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/01/31 11:16:13  cvs
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar[listurl]  = "crm_naw_cfList.php";
$__funcvar[location] = "crm_naw_cfEdit.php";



$mainHeader   = "extra velden muteren, bij relatie&nbsp;";

$object = new CRM_naw_cf();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;

$editcontent[jsincludes] .= "<script language=JavaScript src=\"javascript/tabbladen.js\" type=text/javascript></script>\n";
$editcontent[jsincludes] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
$editcontent[jsincludes] .= "<script language=JavaScript src=\"javascript/algemeen.js\" type=text/javascript></script>";
//$editcontent[jsincludes] .= "<script language=JavaScript src=\"javascript/CRM_nawEdit.js\" type=text/javascript></script>";
$editcontent[body] 				= " onLoad=\"javascript:tabOpen('0');\"  ";


$data = array_merge($_GET,$_POST);
$action = $data[action];

$deb_id = $_GET[deb_id];
if ($deb_id > 0)
{
  
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = $deb_id";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader    = $nawRec["naam"]." te ".$nawRec["plaats"];
  $db = new DB();
  $q="SELECT id FROM CRM_naw_cf WHERE rel_id=$deb_id";
  $db->SQL($q);
  if ($myRec = $db->lookupRecord())
  {
    $data['id'] = $myRec['id'];
    $action = "edit";
  }      
  else
  {
    $action = "new";
    $object->set("rel_id",$deb_id);
  }  
  $_SESSION["NAV"]->returnUrl = "CRM_nawEdit.php?action=edit&id=".$deb_id."&useSavedUrl=1";
}  

$editcontent[pageHeader] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editObject->template = $editcontent;
$editObject->usetemplate = true;
$editObject->formTemplate = "crm_naw_cfEditTemplate.html";
$editObject->controller($action,$data);

$object->setOption("inkomenSoort"          ,"form_options",GetSelectieVelden("soort inkomen",false));
$object->setOption("beleggingsHorizon"     ,"form_options",GetSelectieVelden("beleggingshorizon",false));
$object->setOption("beleggingsDoelstelling","form_options",GetSelectieVelden("beleggingsdoelstelling",false));
$object->setOption("risicoprofiel"         ,"form_options",GetSelectieVelden("risicoprofiel",false));
$object->setOption("verzendFreq"           ,"form_options",GetSelectieVelden("verzend freq rapportage",false));
$object->setOption("inContactDoor"           ,"form_options",GetSelectieVelden("in contact door",false));

$ervaringSelectie = GetSelectieVelden("ervaring",false);
$object->setOption("ervaringMetGestructureerdeProductenDatum","form_options",$ervaringSelectie);
$object->setOption("ervaringMetGestructureerdeProducten"     ,"form_options",$ervaringSelectie);
$object->setOption("ervaringBelegtInEigenbeheer"             ,"form_options",$ervaringSelectie);
$object->setOption("ervaringBelegtInVermogensadvies"         ,"form_options",$ervaringSelectie);
$object->setOption("ervaringBelegtInProducten"               ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetVastrentende"                 ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetBeleggingsFondsen"            ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetIndividueleAandelen"          ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetOpties"                       ,"form_options",$ervaringSelectie);
$object->setOption("ervaringMetFutures"                      ,"form_options",$ervaringSelectie);



$object->setOption("huidigesamenstellingAandelen","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingObligaties","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingOverige","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingLiquiditeiten","form_extra","onBlur=\"this.value=formatNumericField(this);huidigeTotaal()\" style=\"text-align:right;\" ");
$object->setOption("huidigesamenstellingTotaal","form_extra","READONLY style=\"text-align:right;\" ");

//$editObject->formVars["vermogensbeheerder"] = 'via ';
echo $editObject->getOutput();




if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>