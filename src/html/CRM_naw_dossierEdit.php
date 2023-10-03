<?php

include_once("wwwvars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_digidoc.php");

//error_reporting(E_ALL);
$subHeader     = "";
$__funcvar['listurl']  = "CRM_naw_dossierList.php";
$__funcvar['location'] = "CRM_naw_dossierEdit.php";

$object = new Naw_dossier();

$data = array_merge($_GET,$_POST);

$action = $data['action'];

if ($action == "new")
  $mainHeader    = "gespreksverslag toevoegen";
else
  $mainHeader    = "gespreksverslag bekijken";

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar  = $__appvar;

$db=new DB();

$query="SELECT CRM_selectievelden.omschrijving, CRM_selectievelden.waarde FROM CRM_selectievelden WHERE module = 'gesprekstypen' ";
$db->SQL($query);
$db->query();

$clientContactJS="function checkContact() 
{
  var contactTypen = new Array();
";
while($dbData=$db->NextRecord())
{
  $typeOpties[$dbData['omschrijving']]=$dbData['omschrijving'];
  if($dbData['waarde']==1)
  {
    $clientContactJS .= "  contactTypen['" . addslashes($dbData['omschrijving']) . "']=true;\n";
  }
}
$clientContactJS.="
var huidigType=document.getElementById('type').value;
if(contactTypen[huidigType]==true)
{
  document.getElementById('isContact').checked=true;
  document.getElementById('isContact').disabled=true;
}
else
{
  document.getElementById('isContact').checked=false;
  document.getElementById('isContact').disabled=true;
}


}";

$editObject->formVars['contactJS']=$clientContactJS;

$object->setOption('type', 'form_options',$typeOpties);

$query="SELECT id,omschrijving FROM CRM_naw_dossier_templates ORDER BY omschrijving";
$db->SQL($query);
$db->query();
$templateOptions='<select  class="" type="select" name="templateId" id="templateId"  >';
$templateOptions.='<option value=""> --- </option>';
while($dbData=$db->NextRecord())
{
  $templateOptions.='<option value="'.$dbData['id'].'" >'.$dbData['omschrijving'].'</option>';
}
$templateOptions.='</select> ';
$editObject->formVars['templates'] = $templateOptions;

$query="SELECT
Vermogensbeheerders.CRM_GesprVerslagVerwWijz as verwijder
FROM
Vermogensbeheerders
INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
WHERE
VermogensbeheerdersPerGebruiker.Gebruiker='".$_SESSION['usersession']['gebruiker']['Gebruiker']."' AND Vermogensbeheerders.CRM_GesprVerslagVerwWijz>0";
if($db->QRecords($query)>0)
  $allowEditVermogensbeheerder=true;
else
  $allowEditVermogensbeheerder=false;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";
$editcontent['eigenFocus']="if(document.getElementById('kop')){try{document.getElementById('kop').focus(); checkContact();break;} catch(err) { }}";
$editObject->template = $editcontent;

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$editObject->formTemplate = "CRM_naw_dossierEditTemplate.html";

$editObject->formVars['ajax_edit'] = 'false';
if($data['id'])
{
  $object->getById($data['id']);

  $tijd=explode(":",$object->get('duur'));
  unset($tijd[2]);
  $object->set('duur',implode(":",$tijd));
  

  if($_SESSION['usersession']['gebruiker']['CRMlevel']==2 && $allowEditVermogensbeheerder==true)
  {
    //listarray($_SESSION['usersession']['gebruiker']);
  }
  elseif(($object->get('add_user') != $USR || substr($object->get('add_date'),0,10) != date('Y-m-d')) && $object->get('id') > 0)
  {
    $_SESSION['NAV']->addItem(new NavEdit("editForm", false,false,true));
    $noSave=true;
    $action='edit';
    
    $editObject->formVars['ajax_edit'] = 'true';
  }
  
}

$editObject->controller($action,$data);

if(isset($_GET['toList']))
  $editObject->formVars['action2']='toList';

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if ($action == "new")
{
  $object->setOption("rel_id","value",$_GET['rel_id']);
  $object->setOption("datum","value", date("Y-m-d"));//jul2form(time())
  $mainHeader    = "Dossier toevoegen";
}
else
{
  $tijd=explode(":",$object->get('duur'));
  unset($tijd[2]);
  $object->set('duur',implode(":",$tijd));
  if($_SESSION['usersession']['gebruiker']['CRMlevel']==2 && $allowEditVermogensbeheerder)
  {
   // listarray($_SESSION['usersession']['gebruiker']);
  }
  elseif(($object->get('add_user') != $USR || substr($object->get('add_date'),0,10) != date('Y-m-d')) && $object->get('id') > 0)
  {
    $_SESSION['NAV']->addItem(new NavEdit("editForm", false,false,true));
    $noSave=true;
    $action='edit';
  }
}

if(!$noSave)
{
  $editObject->formVars['save_onder']='<div class="formblock">
<div class="formlinks">
<a href="#" onClick="editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan</a>
</div>
<div class="formrechts">
<a href="#" onClick="editForm.action2.value=\'taak\';editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan en nieuwe taak</a>&nbsp;&nbsp;
<a href="#" onClick="editForm.action2.value=\'agenda\';editForm.submit();"><img src="images//16/save.gif" width="16" height="16" border="0" alt="sla de wijzigingen op" align="absmiddle">&nbsp;Opslaan en nieuw agenda punt</a>
</div>';

 if($object->get('dd_reference_id') < 1)
   $editObject->formVars['toevoegenFile']='<div class="formblock">
   <div class="formlinks">Bestand toevoegen</div>
   <div class="formrechts">
   <input type="file" name="importfile" size="50">
   </div>
   </div>';
}

if($object->get('dd_reference_id') > 0)
{
  $db=new DB();
  $query="SELECT filename,dd_id, datastore FROM dd_reference WHERE id='".$object->get('dd_reference_id')."'";
  $db->SQL($query);
  $dd=$db->lookupRecord(); 
  $editObject->formVars['toevoegenFile']='
  <div class="formblock">
   <div class="formlinks">Bestand </div>
   <div class="formrechts">
   <a href="dd_push.php?show=1&datastore='.$dd['datastore'].'&dd_id='.$dd['dd_id'].'"><b>download</b> ('.$dd['filename'].')</a>
   </div>
   </div>';
}
//

//if($action <> 'update' && !$noSave)
echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($action=='update'||$action=='edit'||$action=='delete')
  {
    $relId=$editObject->object->get('rel_id');
    if($relId>0)
    {
      $query="UPDATE CRM_naw SET 
CRM_naw.laatsteGesprekId=(select MAX(CRM_naw_dossier.id) FROM CRM_naw_dossier WHERE CRM_naw_dossier.rel_id=$relId),
CRM_naw.clientGesproken=(select MAX(CRM_naw_dossier.datum) FROM CRM_naw_dossier WHERE CRM_naw_dossier.rel_id=$relId AND CRM_naw_dossier.clientGesproken=1)
WHERE CRM_naw.id='$relId'";
      $db->SQL($query);
      $db->query();
    }
  }

  if($_FILES['importfile']['tmp_name'] <> '')
  {
    $filename=$_FILES['importfile']['tmp_name'];
    $file=$_FILES['importfile']['name'];
    $filesize = filesize($filename);
    $filetype = mime_content_type($filename);
    $fileHandle = fopen($filename, "r");
    $docdata = fread($fileHandle, $filesize);
    fclose($fileHandle);

    $dd = new digidoc();
    $rec=array();
    $rec ["filename"] = $file;
    $rec ["filesize"] = "$filesize";
    $rec ["filetype"] = "$filetype";
    $rec ["description"] = $file;
    $rec ["blobdata"] = $docdata;
    $rec ["keywords"] = '';
    $rec ["module"] = 'CRM_naw';
    $rec ["module_id"] = $data['rel_id'];
    $rec ["categorie"] = 'Documenten';
    $dd->useZlib = false;
    $dd->addDocumentToStore($rec);
    $object->set('dd_reference_id',$dd->referenceId);
    $object->save();
  }

  if($_POST['action2']=='agenda')
    header("Location: agendaEdit.php?action=new&deb_id=".$_POST['rel_id']."");
  elseif($_POST['action2']=='taak')
    header("Location: takenEdit.php?action=new&rel_id=".$_POST['rel_id']."");
  elseif($_POST['action2']=='toList')
    header("Location: CRM_naw_dossierList.php?deb_id=".$_POST['rel_id']);
  else
	  header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}
?>