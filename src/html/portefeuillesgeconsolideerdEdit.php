<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 4 september 2021
    Author              : $Author:  $
    Laatste aanpassing  : $Date:  $
    File Versie         : $Revision:  $
 		
    $Log: $
 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " muteren";

$__funcvar['listurl']  = "portefeuillesgeconsolideerdList.php";
$__funcvar['location'] = "portefeuillesgeconsolideerdEdit.php";

$object = new PortefeuillesGeconsolideerd();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent['jsincludes'] .= "\n<script language=JavaScript src=\"javascript/sack/tw-sack.js\" type=text/javascript></script>\n";

$editcontent['javascript'] .= "

var ajax = new Array();
var ajaxvp = new Array();


function VirtuelePortefeuilleChanged()
{
  getPortefeuilles(document.editForm.VirtuelePortefeuille.value,'Portefeuilles','Portefeuille');
}

  function getPortefeuilles (sel,tabel,veld)
  {
  var oldValue = document.getElementById(veld).value;
  var virtuelePortefeuille = sel;
        if(virtuelePortefeuille.length>0){
                var index = ajax.length;
                ajax[index] = new sack();
                ajax[index].element = veld;
                ajax[index].requestFile = 'lookups/ajaxLookup.php?module=PortefeuillesPerVermogensbeheerder&query='+virtuelePortefeuille+'|portefeuillesgeconsolideerd'+'|'+oldValue; // Specifying which file to get
                ajax[index].onCompletion = function(){ setWaarden(index,veld,oldValue) };       // Specify function that will be executed after file has been found
                ajax[index].onError = function(){ alert('Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.') };
                ajax[index].runAJAX();          // Execute AJAX function
                 }
  }


function setWaarden(index,veld,oldValue)
{
        var     Waarden = ajax[index].response;
        var elements = Waarden.split('\\t\\n');
        if(elements.length > 1)
        {
          document.getElementById(veld).options.length=0;
          AddName('editForm',veld,'---','');
        for(var i=0;i<elements.length;i++)
        {
         if(elements[i] != '')
         {
             AddName('editForm',veld,elements[i],elements[i])
           }
    }
        }
  else
  {
    document.getElementById(veld).options.length=0;
  }
        document.getElementById(veld).value = oldValue;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}

";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
$editObject->usetemplate = true;


if ($_GET['portefeuille'] || $_GET['VirtuelePortefeuille'] || $data['id']>0)
{
  if($_GET['portefeuille'] <> '' && $_GET['VirtuelePortefeuille']=='')
  {
    $_GET['VirtuelePortefeuille'] = $_GET['portefeuille'];
  }
  else
  {
    $object->getById($data['id']);
    $_GET['VirtuelePortefeuille']=$object->get('VirtuelePortefeuille');
  }
  $DB = new DB();
  $object->set('VirtuelePortefeuille', $_GET['VirtuelePortefeuille']);
  $q = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE Portefeuille='" . $_GET['VirtuelePortefeuille'] . "'";
  $DB->SQL($q);
  $vermogensbeheerder = $DB->lookupRecord();
  $editObject->verzendVermogensbeheerder=$vermogensbeheerder['Vermogensbeheerder'];
}


$template='
<form name="editForm" action="{updateScript}" method="{method}" >
<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="updateScript" value="{updateScript}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
<input type="hidden"  value="'.$vermogensbeheerder['Vermogensbeheerder'].'" name="vermogensbeheerder" >
{id_inputfield}
<div class="formblock">
<div class="formlinks"><label for="VirtuelePortefeuille">{VirtuelePortefeuille_description}</label> </div>
<div class="formrechts">
{VirtuelePortefeuille_inputfield} {VirtuelePortefeuille_error}
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="Portefeuille">{Portefeuille_description}</label> </div>
<div class="formrechts">
{Portefeuille_inputfield} {Portefeuille_error}
</div>
</div>

<div class="formblock">
<div class="formlinks">&nbsp;</div>
<div class="formrechts">
{change_user_value} {change_date_value}</div>
</div>

<div class="formblock">
<div class="formlinks">&nbsp;</div>
<div class="formrechts">
{verzendKnop}
</div>

</form></div>';

$editObject->formTemplate=$template;




$editObject->controller($action,$data);
//echo $editObject->getTemplate();exit;
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

if($data['frame']==1)
{
  $query="SELECT Portefeuille,Portefeuille FROM Portefeuilles
WHERE consolidatie=0  AND Vermogensbeheerder='". $vermogensbeheerder['Vermogensbeheerder']."'
 AND (Portefeuille NOT IN(SELECT Portefeuille FROM PortefeuillesGeconsolideerd WHERE VirtuelePortefeuille='".mysql_real_escape_string($_GET['VirtuelePortefeuille'])."') OR Portefeuille='".$object->get('Portefeuille')."') ORDER BY Portefeuille"; //AND Einddatum>now() AND Startdatum>'1990-01-01'
  $object->setOption('Portefeuille', 'select_query', $query);
  
  $object->setOption('VirtuelePortefeuille', 'form_type', 'text');
  $object->setOption('VirtuelePortefeuille', 'form_extra', 'READONLY');
  $object->setOption('vermogensbeheerder', 'form_type', 'text');
  $object->setOption('vermogensbeheerder', 'form_extra', 'READONLY');
//listarray($_SESSION['usersession']['gebruiker']);

  if ($_SESSION['usersession']['gebruiker']['portefeuilledetailsAanleveren'] > 0)
  {
    $editObject->formVars["verzendKnop"] = '<a href="#" onClick="editForm.submit();"><img src="images/16/save.gif" width="16" height="16" border="0" alt="Verzenden" align="absmiddle">&nbsp;Naar AIRS verzenden</a>
    <input type="hidden" name="frame" value="1">';
  }
  else
    $editObject->formVars["verzendKnop"] ='Geen rechten om te verzenden.';
  echo $editObject->getOutput();
  
}
else
{
  if($__appvar["bedrijf"]=='HOME' || $__appvar["bedrijf"]=='TEST')
    $object->setOption('VirtuelePortefeuille', 'select_query', "SELECT Portefeuille,concat(Portefeuille,' - ',Vermogensbeheerder) FROM Portefeuilles WHERE consolidatie=1 ORDER BY Portefeuille");
  else
    $object->setOption('Portefeuille', 'select_query', "SELECT Portefeuille,Portefeuille FROM Portefeuilles WHERE consolidatie=0 ORDER BY Portefeuille");
  echo $editObject->getOutput();
}
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
?>