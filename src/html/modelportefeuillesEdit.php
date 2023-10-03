<?php
/*
    AE-ICT CODEX source module versie 1.3, 5 december 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.9 $

    $Log: modelportefeuillesEdit.php,v $
    Revision 1.9  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.8  2017/02/06 07:52:11  rm
    no message

    Revision 1.7  2017/01/31 13:35:30  rm
    no message

    Revision 1.6  2017/01/18 16:05:27  rm
    5430 (EFI) Invoer FX-modelportefeuilles

    Revision 1.5  2014/09/13 14:37:42  rvv
    *** empty log message ***

    Revision 1.4  2013/09/07 15:59:15  rvv
    *** empty log message ***

    Revision 1.3  2013/08/21 11:42:00  rvv
    *** empty log message ***

    Revision 1.2  2011/04/27 17:53:49  rvv
    *** empty log message ***

    Revision 1.1  2006/12/11 11:03:16  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "modelportefeuillesList.php";
$__funcvar['location'] = "modelportefeuillesEdit.php";


$object = new ModelPortefeuilles();



if ( isset ($_GET['type']) && $_GET['type'] == 'copyTo' ) {
  $copyData = $_GET;

  $modelPortefeuilleObj = $object;
  $modelPortefeuille = $modelPortefeuilleObj->parseById($copyData['id']);

  if ( empty ($modelPortefeuille) ) {


  } else {
    $AEArray = new AE_Array();
    $newdate = $copyData['newDate'];

    $dateTotcheck = explode('-', $newdate);
    if ( strlen($dateTotcheck[2]) === 4 ) {
      $newdate = formdate2db($newdate);
    }

    $modelPortefeuilleFixedObj = new ModelPortefeuilleFixed();
    $modelPortefeuilleFixedDatas = $modelPortefeuilleFixedObj->parseBySearch(array(
      'Portefeuille'  => $modelPortefeuille['Portefeuille'],
      'Datum' => $copyData['useDate']
    ),"all", null, -1);
    foreach ( $modelPortefeuilleFixedDatas as $modelPortefeuilleFixedData ) {
      $modelPortefeuilleFixedData['id'] = null;
      $modelPortefeuilleFixedData['add_date'] = date('Y-m-d H:i:s');
      $modelPortefeuilleFixedData['change_date'] = date('Y-m-d H:i:s');
      $modelPortefeuilleFixedData['add_user'] = $USR;
      $modelPortefeuilleFixedData['change_user'] = $USR;
      $modelPortefeuilleFixedData['Datum'] = $newdate;
      $modelPortefeuilleFixedInsertQuery = "INSERT INTO `ModelPortefeuilleFixed` SET " . $AEArray->toSqlInsert($modelPortefeuilleFixedData);

      $db = new DB();
      $db->executeQuery($modelPortefeuilleFixedInsertQuery);
    }
  }

  $_GET = array(
    'action'  => 'edit',
    'id'  => $copyData['id']
  );
  header("Location: modelportefeuillesEdit.php?action=edit&id=".$copyData['id']);
  exit();
}


$editcontent['style'] .= $editcontentNieuw['style'];

$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

//$editcontent['body'] = "onLoad='javascript:portefeuilleChanged();'";
$editcontent['javascript'] = "
function submitForm()
{
	//check values ?
	document.editForm.submit();
}
var value = '';
var ajax = new Array();
function portefeuilleChanged()
{
  getBeleggingscategorien(document.editForm.Portefeuille.value,document.editForm.Beleggingscategorie.value);
}


function getBeleggingscategorien(sel,value)
{
	var Portefeuille = sel;
	if(Portefeuille.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = 'Beleggingscategorie';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=Koppelvelden&query='+Portefeuille+'|Beleggingscategorien|portefeuille';	// Specifying which file to get
		ajax[index].onCompletion = function(){ setValues(index,'Beleggingscategorie',value) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen Zorgplicht waarden mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function setValues(index,target,value)
{
 	var	velden = ajax[index].response;
 	document.getElementById(target).options.length=0;
 	var elements = velden.split('\\t\\n');
  AddName('editForm',target,'---','')
 	for(var i=0;i<elements.length;i++)
 	{
 	 if(elements[i] != '')
 	 {
 	   //AddName('editForm',target,elements[i],elements[i])
     
     var parts=elements[i].split('\\t');
     AddName('editForm',target,parts[0]+' - '+parts[1],parts[0]);
 	 }
 	}
 	document.getElementById(target).value=value;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}
";


$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent['body'] = " onLoad=\"showFixed();javascript:portefeuilleChanged();\" ";
//listarray($editcontent);
//$editcontent['javascript']

$editObject->template = $editcontent;
$editObject->formTemplate='modelportefeuillesTemplate.html';
$editObject->usetemplate = true;

$data = $_GET;



$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen

$autocomplete = new Autocomplete();
$editObject->template['script_voet'] .= $autocomplete->getAutoCompleteScript('ModelPortefeuilles', 'Portefeuille', 'Portefeuille');
$editObject->controller($action,$data);

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
$db=new DB();
$query="SELECT Datum FROM ModelPortefeuilleFixed WHERE Portefeuille='".$object->get('Portefeuille')."' GROUP BY Datum ORDER BY Datum";
$dates=array(date('Y-m-d')=>'Vandaag');
$db->SQL($query);
$db->Query();
$locked=false;
while($dbData=$db->nextRecord())
{
  $dates[$dbData['Datum']]=jul2form(db2jul($dbData['Datum']));
  $selected=$dbData['Datum'];
  $locked=true;
}
$object->set('FixedDatum',$selected);
$object->setOption('FixedDatum','form_options',$dates);
if($locked) {
  $object->setOption('Portefeuille','form_extra','disabled');
  $object->setOption('Portefeuille','select_query','SELECT Portefeuille, Portefeuille FROM Portefeuilles WHERE Portefeuille = "'.$object->get('Portefeuille') .'" ORDER BY Portefeuille');
}

$huidigeType = (int) $object->get('Fixed');
if ( $huidigeType === 1 ) {
  $editObject->formVars['copyBtn'] = '<span id="copy" class="btn-new btn-default"><i class="fa fa-files-o" aria-hidden="true"></i> Kopieer</span>';
}


  /*
if($object->get("Portefeuille") <> '')
  $object->setOption('Beleggingscategorie','select_query',"SELECT KeuzePerVermogensbeheerder.waarde,KeuzePerVermogensbeheerder.waarde FROM KeuzePerVermogensbeheerder
JOIN Portefeuilles ON KeuzePerVermogensbeheerder.vermogensbeheerder = Portefeuilles.Vermogensbeheerder
WHERE
KeuzePerVermogensbeheerder.categorie IN('Beleggingscategorien')
AND Portefeuilles.Portefeuille='".$object->get("Portefeuille")."'");  
*/

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($action=='delete')
  {
    $query="DELETE FROM ModelPortefeuilleFixed WHERE Portefeuille='".$object->get('Portefeuille')."'";
    $db->SQL($query);
    $db->Query();
  }
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>