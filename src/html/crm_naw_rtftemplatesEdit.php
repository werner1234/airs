<?php
/*
    AE-ICT CODEX source module versie 1.6, 4 augustus 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/08/23 11:56:23 $
    File Versie         : $Revision: 1.7 $

    $Log: crm_naw_rtftemplatesEdit.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

include_once ("../classes/AE_cls_fileUpload.php");
$upl = new AE_cls_fileUpload();

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "crm_naw_rtftemplatesList.php";
$__funcvar['location'] = "crm_naw_rtftemplatesEdit.php";

$object = new CRM_naw_RtfTemplates();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;


$data = array_merge($_GET,$_POST);
$action = $data['action'];

$nameField='';
if($action <> 'new')
{
$nameField=' <div class="formlinks"><label for="naam">{naam_description}</label> </div>
<div class="formrechts">
{naam_inputfield} {naam_error}
</div>
</div>';

}


$verplicht=array();
foreach($data as $key=>$value)
{ 
  if(substr($key,0,9)=='verplicht')
  {
    $tmp=explode('@',$key);
    $verplicht[$tmp[1]][]=$tmp[2];
    
  }
}

$data['verplichteVelden']=serialize($verplicht);



  if($_FILES['importfile']['tmp_name'] <> '')
  {
    if (!$upl->checkExtension($_FILES['importfile']['name']))
    {
      echo vt("Fout: veboden bestandsformaat");
      exit;
    }
    $filename=$_FILES['importfile']['tmp_name'];
    $data['naam']=$_FILES['importfile']['name'];
    $filesize = filesize($filename);
    $fileHandle = fopen($filename, "r");
    $data['template'] = fread($fileHandle, $filesize);
    fclose($fileHandle);
  }

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;



$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$editObject->controller($action,$data);



$fields=getFields(unserialize($object->get('verplichteVelden')));

$editObject->formTemplate='
<form enctype="multipart/form-data"  method="POST" name="editForm" action="{updateScript}">
<input type="hidden" name="MAX_FILE_SIZE" value="256000000">
<input type="hidden" name="posted" value="true" />

<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
{id_inputfield}<div class="formblock">

'.$nameField.'

<div class="formblock">
<div class="formlinks"><label for="standaard">{standaard_description}</label> </div>
<div class="formrechts">
{standaard_inputfield} {standaard_error}
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="categorie">{categorie_description}</label> </div>
<div class="formrechts">
{categorie_inputfield} {categorie_error}
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="template">{template_description}</label> </div>
<div class="formrechts">
<input type="file" name="importfile" size="50">
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="verplichteVelden">{verplichteVelden_description}</label> </div>
<div class="formrechts">
'.$fields['0'].'
</div>
</div>


<div class="formblock">
<div class="formlinks">&nbsp;</div>
<div class="formrechts">
{change_user_value} {change_date_value}</div>
</div>

</form></div>
';
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");

//$editObject->template['style']='<link type="text/css" href="style/jquery.css" rel="stylesheet" />';

//echo "<pre>";
//echo template($__appvar["templateContentHeader"],$editObject->template);
//echo "</pre>";

echo $editObject->getOutput();
//echo template($__appvar["templateRefreshFooter"],$editObject->template);


$db=new DB();
if ($result = $editObject->result)
{
  if($data['standaard'] == 1)
  {
    $query="UPDATE CRM_naw_RtfTemplates SET standaard=0 WHERE id <> '".$object->get('id')."'";
    $db->SQL($query);
    $db->Query();
  }
	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}



function getFields($selectie)
{ 
  $objecten=array('Naw'=>'Naw','Portefeuilles'=>'Portefeuilles','CRM_naw_adressen'=>'Adressen','CRM_naw_kontaktpersoon'=>'Contactpersoon','laatstePortefeuilleWaarde'=>'PortefeuilleWaarde');

  foreach ($objecten as $objectnaam=>$omschrijving)
  {
    $naw = new $objectnaam();
    $veldenKey=array();

    foreach ($naw->data['fields'] as $key=>$values)
     $veldenKey[]=$key;
    natcasesort($veldenKey);
    $html_opties .= "<div class=\"menutitle\" onclick=\"SwitchMenu('sub$objectnaam')\">$omschrijving</div><span class=\"submenu\" id=\"sub$objectnaam\">\n";
    foreach ($veldenKey as $key)
    {
      if(isset($selectie[$objectnaam]) && in_array($key,$selectie[$objectnaam]))
        $checked='checked';
      else
        $checked='';  
      $html_opties .= "<input type='checkbox' name='verplicht@".$objectnaam."@".$key."' $checked value='1'> 
      <label for=\"".$key."\" title=\"".$naw->data['fields'][$key]['description']."\"> ".$key." </label><br>\n";
    }
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
<br><br><b><?= vt('Velden'); ?></b>
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
?>