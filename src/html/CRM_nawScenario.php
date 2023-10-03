<?php
/*
    AE-ICT CODEX source module versie 1.1.1.1, 10 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.3 $

    $Log: CRM_nawScenario.php,v $
    Revision 1.3  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.2  2014/11/30 13:10:28  rvv
    *** empty log message ***

    Revision 1.1  2014/05/29 12:07:22  rvv
    *** empty log message ***

    Revision 1.1  2012/05/06 11:54:01  rvv
    *** empty log message ***

    Revision 1.3  2006/02/01 10:06:29  cvs
    *** empty log message ***




*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");


$mainHeader   = "Scenario Analyse,&nbsp;&nbsp;&nbsp;";


$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

echo template($__appvar["templateContentHeader"],$content);

$object=new Naw();
$object->getById($_GET['id']);
$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;
$editObject->template = $editcontent;
$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = '
<fieldset id="RelatieSectie1">
  <legend> Algemeen </legend>
  <div class="formblock">
    <div class="formlinks">
    </div>
    <div class="formrechts">
      <table><tr>
      <td valign="top">
      <label id="debiteur">{debiteur_inputfield} {debiteur_description}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label id="crediteur">{crediteur_inputfield} {crediteur_description}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label id="overige">{overige_inputfield} {overige_description}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label id="tag">{tag_inputfield} {tag_description}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label id="aktief">{aktief_inputfield} {aktief_description}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label id="contactTijd" {contactTijdStyle}>{contactTijd_inputfield} {contactTijd_description}</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <label>{prospect_inputfield} {prospect_description}</label>
      </td>
            <td valign="top">
      <span id="prospectStatusSpan"> {prospectStatus_inputfield} &nbsp; 
      {prospectStatus_description} {prospectEigenaar_inputfield} &nbsp; {prospectEigenaar_description}
      <br /> {prospectStatusChange_value} </span>
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      </td>
      </tr> </table>
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks">{naam_description} </div>
    <div class="formrechts">
      {naam_inputfield} {naam_error}<br>
      {naam1_inputfield} {naam1_error}
    </div>
  </div>

  <div class="formblock">
    <div class="formlinks">{zoekveld_description} </div>
    <div class="formrechts">
    {zoekveld_inputfield} {zoekveld_error}
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">{debiteurnr_description} </div>
    <div class="formrechts">
  {debiteurnr_inputfield}    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">{portefeuille_description} </div>
    <div class="formrechts">
    {portefeuille_inputfield} {portefeuille_error}
    &nbsp;&nbsp;&nbsp;Relatie Sinds {relatieSinds_inputfield} {relatieSinds_error}  &nbsp;&nbsp;&nbsp; {wachtwoord_description} {wachtwoord_inputfield} {wachtwoord_error}
    </div>
  </div>

   <div class="formblock">
    <div class="formlinks">{accountEigenaar_description} </div>
    <div class="formrechts">
    {accountEigenaar_inputfield} {accountEigenaar_error}
    </div>
  </div>
</fieldset>
';

if($_GET['frame']==1)
 $editObject->formTemplate='';
 
$editObject->controller('edit','');
unset($_SESSION['NAV']);
echo $editObject->getOutput();


echo '
<fieldset >
<table>
<tr><td>
<iframe id="extraFrameLinks" name="extraFrameLinks" src="CRM_nawScenarioEdit.php?frame=1&action=edit&id='.$_GET['id'].'" width="600" height="300" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0">
</iframe>
</td><td>
<iframe id="extraFrameRechts" name="extraFrameRechts" src="crm_naw_cashflowList.php?frame=1&rel_id='.$_GET['id'].'" width="600" height="300" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0">
</iframe>
</td></tr>
</table>
</fieldset>
<iframe id="extraFrameRechts" name="extraFrameRechts" src="scenario.php?frame=1&id='.$_GET['id'].'" width="1200" height="600" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0">

';



unset($_SESSION['NAV']);
echo template($__appvar["templateRefreshFooter"],$content);
//frameSet.php?page="'.base64_encode($url).'

?>
<script>

//document.getElementById('extraFrameLinks').location = 'frameSet.php?page=Q1JNX25hd19rb250YWt0cGVyc29vbkxpc3QucGhwP2RlYl9pZD0x';
//alert(document.getElementById('extraFrameLinks').location);
//document.frames['extraFrameLinks'].location = 'frameSet.php?page=Q1JNX25hd19rb250YWt0cGVyc29vbkxpc3QucGhwP2RlYl9pZD0x';


</script>
