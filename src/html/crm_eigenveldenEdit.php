<?php
/*
    AE-ICT CODEX source module versie 1.6, 28 april 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/09/04 16:06:12 $
    File Versie         : $Revision: 1.10 $

    $Log: crm_eigenveldenEdit.php,v $
    Revision 1.10  2019/09/04 16:06:12  rvv
    *** empty log message ***

    Revision 1.9  2019/09/04 15:29:38  rvv
    *** empty log message ***

    Revision 1.8  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.7  2016/04/10 15:45:47  rvv
    *** empty log message ***

    Revision 1.6  2014/08/13 14:52:55  rvv
    *** empty log message ***

    Revision 1.5  2014/08/09 15:05:41  rvv
    *** empty log message ***

    Revision 1.4  2014/07/27 11:29:40  rvv
    *** empty log message ***

    Revision 1.3  2014/06/11 15:44:58  rvv
    *** empty log message ***

    Revision 1.2  2014/02/22 18:42:25  rvv
    *** empty log message ***

    Revision 1.1  2012/04/28 15:55:51  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_SQLman.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "crm_eigenveldenList.php";
$__funcvar['location'] = "crm_eigenveldenEdit.php";

$object = new CRM_eigenVelden();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;

$verplicht=array();
foreach($data as $key=>$value)
{ 
  if(substr($key,0,9)=='verplicht')
  {
    $tmp=explode('@',$key);
    $verplicht[$tmp[1]][]=$tmp[2];
    
  }
}

$data['extraVeldData']=serialize($verplicht);

if($data['id'] > 0)
  $object->setOption('veldnaam','form_extra','readonly');

$editObject->controller($action,$data);

if($data['aantalTekens']>0 && $data['aantalTekens']<255)
{
  $varcharTekens=$data['aantalTekens'];
}
else
{
  $varcharTekens=255;
}

$veldTypen=array('Tekst'=>"varchar($varcharTekens)",'Memo'=>'text','Getal'=>'double','Datum'=>'date','Trekveld'=>'varchar(200)','Checkbox'=>'tinyint(4)');

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
if($object->get('relatieSoort')==1)
  $fields=getFields(unserialize($object->get('extraVeldData')));

if($object->get('veldtype')!='Tekst')
  $style='style="display:none"';

$editObject->formTemplate='
<form name="editForm" action="{updateScript}">
<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="updateScript" value="{updateScript}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
{id_inputfield}<div class="formblock">
<div class="formlinks"><label for="veldnaam">{veldnaam_description}</label> </div>
<div class="formrechts">
{veldnaam_inputfield} {veldnaam_error}
</div>
</div>

<fieldset style="
  border-top:1px solid #ccc;
  border-left:0;
  border-right:0;
padding: 6px;
    margin: 0px 15px 15px 0px;
    border-bottom: #CCC 1px solid;
  
  ">
  <legend>' . vt('Omschrijving') . '</legend>
  <div class="formblock">
  <div class="formlinks"><label for="omschrijving">{omschrijving_description}</label> </div>
  <div class="formrechts">
  {omschrijving_inputfield} {omschrijving_error}
  </div>
  </div>
  
  <div class="formblock">
  <div class="formlinks"><label for="omschrijving">{omschrijving_en_description}</label> </div>
  <div class="formrechts">
  {omschrijving_en_inputfield} {omschrijving_en_error}
  </div>
  </div>
  
  <div class="formblock">
  <div class="formlinks"><label for="omschrijving">{omschrijving_fr_description}</label> </div>
  <div class="formrechts">
  {omschrijving_fr_inputfield} {omschrijving_fr_error}
  </div>
  </div>
  
  <div class="formblock">
  <div class="formlinks"><label for="omschrijving">{omschrijving_du_description}</label> </div>
  <div class="formrechts">
  {omschrijving_du_inputfield} {omschrijving_du_error}
  </div>
  </div>
</fieldset>
<div class="formblock">
<div class="formlinks"><label for="veldtype">{veldtype_description}</label> </div>
<div class="formrechts">
{veldtype_inputfield} {veldtype_error}
</div>
</div>

<div class="formblock" id="divAantalTekens" '.$style.'>
<div class="formlinks"><label for="veldtype">{aantalTekens_description}</label> </div>
<div class="formrechts">
{aantalTekens_inputfield} {aantalTekens_error} (' . vt('Bij het verkleinen van een veld kan er data verloren gaan.') . ')
</div>
</div>

<div class="formblock">
<div class="formlinks"><label for="trekveldSelectieveld">{trekveldSelectieveld_description}</label> </div>
<div class="formrechts">
{trekveldSelectieveld_inputfield} {trekveldSelectieveld_error}
</div>
</div>

<div class="formblock">	 
 <div class="formlinks"><label for="relatieSoort">{relatieSoort_description}</label> </div>	 
 <div class="formrechts">	 
 {relatieSoort_inputfield} {relatieSoort_error}	 
 </div>	 
 </div>
 
<div class="formblock">
<div class="formlinks"><label for="extraVeldData">{extraVeldData_description}</label> </div>
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

echo $editObject->getOutput();

if ($result = $editObject->result)
{
  if($action=='update')
  {
    $tst = new SQLman();
    $tst->changeField("CRM_naw",$object->get('veldnaam'),array("Type"=>$veldTypen[$object->get('veldtype')],"Null"=>false));
  }

	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}


function getFields($selectie)
{ 
  $objecten=array('Naw'=>'Naw');

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
?>