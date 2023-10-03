<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 19 juli 2007
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/05/30 15:26:58 $
    File Versie         : $Revision: 1.14 $
 		
    $Log: geconsolideerdeportefeuillesEdit.php,v $
    Revision 1.14  2020/05/30 15:26:58  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader  = vt("geconsolideerde portefeuille");
$mainHeader = vt("muteren");

$__funcvar["listurl"]  = "geconsolideerdeportefeuillesList.php";
$__funcvar["location"] = "geconsolideerdeportefeuillesEdit.php";

$editcontent["jsincludes"] .= "<script language=JavaScript src=\"javascript/jsrsClient.js\" type=text/javascript></script>";
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$object = new GeconsolideerdePortefeuilles();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editcontent['javascript'].='

var ajax = new Array();
var ajaxvp = new Array();

function buildQueryArray(theFormName)
{
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e=0;e<theForm.elements.length;e++) {
    if (theForm.elements[e].name!=\'\') {
    	qs[theForm.elements[e].name] = theForm.elements[e].value;
      }
    }
  return qs;
}

function vermogensbeheerderChanged()
{

  
  getSelectie(document.editForm.Vermogensbeheerder.value);

}

'.
"

function getSelectie(sel)
{

	if(sel.length>0){
		var index = ajaxvp.length;
		ajaxvp[index] = new sack();
		ajaxvp[index].element = 'VirtuelePortefeuille';
		ajaxvp[index].requestFile = 'lookups/ajaxLookup.php?module=BedrijfPortefuilles&query='+sel+'|consolidatie';	// Specifying which file to get
		ajaxvp[index].onCompletion = function(){ setSelectieVP(index) };	// Specify function that will be executed after file has been found
		ajaxvp[index].onError = function(){ alert('Ophalen waarden mislukt.') };
		ajaxvp[index].runAJAX();		// Execute AJAX function
	}
	
	if(sel.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Portefeuille1';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=BedrijfPortefuilles&query='+sel;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setSelectie(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen waarden mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setSelectieVP(index)
{
 	var	waarden = ajaxvp[index].response;
 	document.getElementById('VirtuelePortefeuille').options.length=0;
 	console.log('rvv '+waarden);
 	var elements = waarden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	 	   AddNamePortefeuille('editForm','VirtuelePortefeuille',elements[i],elements[i]);
 	 }
 	}
 
}

function setSelectie(index)
{
 	var	waarden = ajax[index].response;
 	for(var n=1;n<41;n++)
  {
  	document.getElementById('Portefeuille'+n).options.length=0;
  }
 	var elements = waarden.split('\\t\\n');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	 	   AddNamePortefeuille('editForm','Portefeuille1',elements[i],elements[i]);
 	 }
 	}
 	var options = $('#Portefeuille1').html();
 	for(var n=1;n<41;n++)
  {
    $('#Portefeuille'+n).html(options);
  }
 
}

function AddNamePortefeuille(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{  
  if(p_OptionText=='---'){p_OptionValue='';}  
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";

$editObject->template = $editcontent;

if($_GET['action'] == "new")
	$editcontent['body'] = "vermogensbeheerderChanged();\" ";

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
$editObject->controller($action,$data);

if($object->get('Vermogensbeheerder'))
{
  $object->setOption('SoortOvereenkomst','select_query', "SELECT KeuzePerVermogensbeheerder.waarde,KeuzePerVermogensbeheerder.waarde FROM KeuzePerVermogensbeheerder WHERE KeuzePerVermogensbeheerder.categorie='soortovereenkomsten' AND Vermogensbeheerder = '".$object->get('Vermogensbeheerder')."'");
  $portefeuilles=array();
  $DB=new DB();
  $query="SELECT
Portefeuilles.Portefeuille
FROM
VermogensbeheerdersPerBedrijf
INNER JOIN Portefeuilles ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder AND Portefeuilles.consolidatie=0
WHERE Bedrijf IN(
SELECT Bedrijf FROM VermogensbeheerdersPerBedrijf WHERE vermogensbeheerder='".$object->get('Vermogensbeheerder')."') ORDER BY Portefeuille ";
  $DB->SQL($query);
  $DB->Query();
  while($rec=$DB->nextRecord())
  {
    $portefeuilles[$rec['Portefeuille']]=$rec['Portefeuille'];
  }
}  

for($i=0;$i<41;$i++)
{
  if(count($portefeuilles) > 0)
    $object->setOption('Portefeuille'.$i,'form_options',$portefeuilles);
  else
  {  

  $waarde=$object->get('Portefeuille'.$i);
  if($waarde <> '')
  { 
    $object->setOption('Portefeuille'.$i,'form_options',array($waarde=>$waarde));
  }
  }  
}



$vermogensbeheerder=$object->get('vermogensbeheerder');
$object->setOption('Risicoprofiel','select_query', "SELECT Risicoklassen.Risicoklasse, Risicoklassen.Risicoklasse 
FROM Risicoklassen JOIN GeconsolideerdePortefeuilles ON GeconsolideerdePortefeuilles.Vermogensbeheerder=Risicoklassen.Vermogensbeheerder
WHERE GeconsolideerdePortefeuilles.id='".$_GET['id']."'  ");

if($data['frame']==1)
{
  $object->setOption('VirtuelePortefeuille', 'form_type', 'text');
  $object->setOption('VirtuelePortefeuille', 'form_extra', 'READONLY');
  $object->setOption('Vermogensbeheerder', 'form_type', 'text');
  $object->setOption('Vermogensbeheerder', 'form_extra', 'READONLY');
  $object->data['fields']['VirtuelePortefeuille']['key_field']=false;
//listarray($_SESSION['usersession']['gebruiker']);

  if ($_SESSION['usersession']['gebruiker']['mutatiesAanleveren'] > 0 || $_SESSION['usersession']['gebruiker']['fondsmutatiesAanleveren'] > 0)
  {
    $editObject->formVars["submit"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <input type="hidden" name="frame" value="1">';
  }
  elseif(checkAccess())
  {
    if($action=='edit')
      $editUrl=$_SERVER['REQUEST_URI'];
    $editObject->formVars["submit"] = '<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Opslaan</a>
    <input type="hidden" name="frame" value="1">
    <input type="hidden" name="editUrl" value="'.$editUrl.'">';
    if($action=='update')
      $returnUrl=$data['editUrl'];
  }
  $html = $editObject->getOutput();
  $html=preg_replace("/<\\/div>.{1,8}<\\/form>/s", '<div class="formblock"><div class="formlinks">&nbsp;</div><div class="formrechts">' . $editObject->formVars["submit"] . "</div></div></form></div>", $html);
  echo $html;
}
else
  echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($object->get('VirtuelePortefeuille')<>'')
  {
    if(checkAccess())
    {
      $con=new AIRS_consolidatie();
      $con->bijwerkenConsolidaties($object->get('VirtuelePortefeuille'));
    }
  }
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
