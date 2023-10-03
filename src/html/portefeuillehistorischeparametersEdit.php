<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 september 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/02/06 15:59:51 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: portefeuillehistorischeparametersEdit.php,v $
    Revision 1.3  2019/02/06 15:59:51  rvv
    *** empty log message ***

    Revision 1.2  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.1  2017/09/02 17:18:56  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader     = vt("portefeuille historische parameters");
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "portefeuillehistorischeparametersList.php";
$__funcvar['location'] = "portefeuillehistorischeparametersEdit.php";

$object = new PortefeuilleHistorischeParameters();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editcontent['javascript'] .='


    var ajax = new Array();



function getSelectie(tabel,portefeuille)
{

	if(tabel.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = \'waarde\';
		ajax[index].requestFile = \'lookups/ajaxLookup.php?module=Koppelvelden&query=\'+portefeuille+\'|\'+tabel+\'|portefeuille\';	// Specifying which file to get
		ajax[index].onCompletion = function(){ setSelectie(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert(\'Ophalen waarden mislukt.\') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setSelectie(index)
{
 	var	waarden = ajax[index].response;
 	document.getElementById(\'waarde\').options.length=0;
 	var elements = waarden.split(\'\\t\\n\');
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != \'\')
 	 {
 	   //AddName(\'editForm\',\'waarde\',elements[i],elements[i])
     var parts=elements[i].split(\'\\t\');
     AddName(\'editForm\',\'waarde\',parts[0]+\' - \'+parts[1],parts[0]);
 	 }
 	}
 	document.editForm.waarde.value = value;
}

function waardenLaden(veld)
{
  if(veld==\'SpecifiekeIndex\')
  {
 //   getAjaxWaarden(\'eJwLdvVxdQ5RcMvPSynWAZMKbkH%2BvhCB1DyFcA%2FXIFcF18y8FJfEktJcBTsFP%2F9wDU0F%2FyAkQVsFdQMg0AUjdaCci2uQglMkxBAAyp4crQ%3D%3D\',\'\',document.getElementById(\'waarde\').name);
 //   alert(\'get SpecifiekeIndex\');
  }
  else if(veld==\'Risicoprofiel\')
  {
   
 //   alert(\'get Risicoprofiel\');
  }
  else if(veld==\'SoortOvereenkomst\')
  {
  //document.getElementById(\'portefeuille\').value
 //   getSelectie(\'SoortOvereenkomsten\',document.editForm.portefeuille.value);
 //  alert(\'get SoortOvereenkomst\');
  }

}


';

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;

$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
