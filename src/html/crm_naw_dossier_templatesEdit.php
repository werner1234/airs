<?php
/*
    AE-ICT CODEX source module versie 1.6, 6 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.2 $

    $Log: crm_naw_dossier_templatesEdit.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


function getFields()
{
  $objecten=array('Naw'=>'Naw','Portefeuilles'=>'Portefeuilles');

  foreach ($objecten as $objectnaam=>$omschrijving)
  {
    $naw = new $objectnaam();
    $veldenKey=array();

    foreach ($naw->data['fields'] as $key=>$values)
     $veldenKey[]=$key;
    natcasesort($veldenKey);
    $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$objectnaam')\">$omschrijving</div><span class=\"submenu\" id=\"sub$objectnaam\">\n";
    foreach ($veldenKey as $key)
      $html_opties .= "<label for=\"".$key."\" title=\"".$naw->data['fields'][$key]['description']."\"> ".$key." </label><br>\n";
    $html_opties .= "</span>\n";

    if($objectnaam=='Naw')
    {
      $veldenKeyNaw=$veldenKey;
      $nawFields= $naw->data['fields'];
    }
  }


 $html = "
 <script language=\"JavaScript\" TYPE=\"text/javascript\">
function Aanpassen()
{
	document.kolForm.submit();
}
function Opslaan()
{
	document.kolForm.kolUpdate.value=\"2\";
	document.kolForm.submit();
}
function Herladen()
{
	document.kolForm.kolUpdate.value=\"3\";
	document.kolForm.submit();
}
</script>
<br><br><b>Velden</b>
<br>
<form name=\"kolForm\" target=\"content\" action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\" >
<input type=\"hidden\" name=\"kolUpdate\" value=\"1\">

<style type=\"text/css\">
.menutitle{
cursor:pointer;
margin-bottom: 5px;
background-color:#ECECFF;
color:#000000;
width:120px;
padding:2px;
text-align:center;
font-weight:bold;
/*/*/border:1px solid #000000;/* */
}

input {
	color: Navy;
	background-color:#FBFBFB;
	font-size:14px;
	border : 0px;
	border-bottom : 1px solid silver;
	border-left : 1px solid silver;
	font-weight: bold;
}

.submenu{
margin-bottom: 0.5em;
}
</style>

<script type=\"text/javascript\" src=\"javascript/menu.js\"></script>

<div id=\"masterdiv\">
";
$html .= $html_opties;
$html .="</div>";
$html .="</form>";

return array($html,$veldenKeyNaw,$nawFields);
}


$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "crm_naw_dossier_templatesList.php";
$__funcvar['location'] = "crm_naw_dossier_templatesEdit.php";

$object = new CRM_naw_dossier_templates();



$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->skipStripAll=true;


$data = array_merge($_POST,$_GET);
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = "crm_naw_dossier_templatesEditTemplate.html";



$db=new db();
$query="SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, max(Vermogensbeheerders.CrmPortefeuilleInformatie) as CrmPortefeuilleInformatie,Vermogensbeheerders.Layout
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder";
$db->SQL($query);
$gebruikPortefeuilleInformatie = $db->lookupRecord();


if($action=='edit')
  $editObject->controller($action,$data);


  $fields = getFields();

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($fields[0],"");
$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$tabs=array('template'=>'template');
foreach ($tabs as $tab=>$omschrijving)
  $loadEditor.="loadEditor('$tab',400,1000);\n";


$editcontent['javascript'].="
function loadEditor(textarea,h,w)
{
  CKEDITOR.replace( textarea ,
	{
    height: h,
		width: w,
    uiColor: '#9AB8F3',
    allowedContent: true 
	});
}

function doEditorOnload()
{
$loadEditor
}

function submitForm()
{
	document.editForm.submit();
}

function previewRtf()
{
document.editForm.target='_blank';
document.editForm.action.value='preview';
document.editForm.submit();
document.editForm.target='_self';
}
 ";
$editcontent['body']='onLoad="doEditorOnload();"';
$editcontent['jsincludes'].='<script type="text/javascript" src="javascript/ckeditor4/ckeditor.js"></script>';
$editObject->template = $editcontent;




//$data['tabs']='tyest data';
if($action<>'edit')
  $editObject->controller($action,$data);

$tabData=unserialize($object->get('tabs'));

if(!is_array($tabData))
  $tabData=array();


foreach ($tabs as $tab=>$omschrijving)
{

  $tabEdit.= '
<div class="formblock">
<div class="formlinks">'.$tab.' <br>
'.$extraInput.'</div>
<div class="formrechts">
<textarea class=""  cols="60"  rows="10" name="'.$tab.'" id="'.$tab.'">'.htmlspecialchars($object->get($tab)).'</textarea>
</div>
</div>';
}

$editObject->formVars['template']=$tabEdit;

echo $editObject->getOutput();

if ($result = $editObject->result)
{
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>