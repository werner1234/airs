<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 3 augustus 2014
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/08/18 12:40:14 $
    File Versie         : $Revision: 1.10 $
 		
    $Log: vrageningevuldEdit.php,v $
    Revision 1.10  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.9  2018/01/24 11:15:08  rvv
    *** empty log message ***

    Revision 1.8  2018/01/24 10:29:19  rvv
    *** empty log message ***

    Revision 1.7  2017/12/27 18:27:54  rvv
    *** empty log message ***

    Revision 1.6  2014/11/19 16:41:12  rvv
    *** empty log message ***

    Revision 1.5  2014/08/25 10:12:01  rvv
    *** empty log message ***

    Revision 1.4  2014/08/23 15:36:34  rvv
    *** empty log message ***

    Revision 1.3  2014/08/09 15:05:41  rvv
    *** empty log message ***

    Revision 1.2  2014/08/03 13:21:03  rvv
    *** empty log message ***

    Revision 1.1  2014/08/03 13:14:10  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

$subHeader = "";
$mainHeader    = " Vragenlijst";

$__funcvar['listurl']  = "vrageningevuldList.php";
$__funcvar['location'] = "vrageningevuldEdit.php";

$object = new VragenIngevuld();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = $_GET;
$action = $data['action'];

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen 
//$editObject->usetemplate = true;
if($action == 'new' || $action == 'edit')
  $editObject->controller($action,$data);

$db=new DB();

if($action == 'edit')
{
  $query="SELECT vragenlijstId,relatieId,date(add_date) as datum FROM VragenIngevuld WHERE id='".$data['id']."'";
  $db->SQL($query);  
  $tmp=$db->lookupRecord();
  $data['vragenlijstId']=$tmp['vragenlijstId'];
  $data['relatieId']=$tmp['relatieId'];
  $data['datum']=$tmp['datum'];

  include_once ("../classes/AIRS_vragen_helper.php");
  $vraagRef = new AIRS_vragen_helper(-1,array("vragenlijstId" => $data['vragenlijstId'], "relatieId" => $data['relatieId'], "add_date" => $data['datum']));
  $data['crmRef_id']=$vraagRef->getCrmRefId();
}

if($data['relatieId'] > 0)
  $object->set('relatieId',$data['relatieId']);
  
if($data['vragenlijstId'] > 0)
{
  $editObject->usetemplate = true;
  $formTemplate='<form name="editForm" action="{updateScript}">
<div class="form">
<input type="hidden" name="action" value="{action}">
<input type="hidden" name="updateScript" value="{updateScript}">
<input type="hidden" name="returnUrl" value="{returnUrl}">
<input type="hidden" name="datum" value="'.$data['datum'].'">
<input type="hidden" name="crmRef_id" value="'.$data['crmRef_id'].'">
{id_inputfield}{relatieId_inputfield}
<input class="" type="hidden"  value="{vragenlijstId_value}" name="vragenlijstId" >
';
  
  $db2=new DB();
  $object->set('vragenlijstId',$data['vragenlijstId']);
   
  $query="SELECT id,vraagNummer,vraag,CRM_trekveld FROM VragenVragen WHERE vragenlijstId='".$data['vragenlijstId']."' ORDER BY volgorde";
  $db->SQL($query);
  $db->Query();
  while($dbData=$db->nextRecord())
  {
    if($dbData['vraag']=='')
      $dbData['vraag']='Vraag is leeg?';
      
    $antwoordQuery="";
    $openVraag=false;
    if($dbData['CRM_trekveld']=='')
    {
      $antwoordQuery = "SELECT id,omschrijving FROM VragenAntwoorden WHERE vraagId='" . $dbData['id'] . "'";
      if($db2->QRecords($antwoordQuery)==0)
        $openVraag=true;
      else
        $openVraag=false;
    }
    else
      $antwoordQuery="SELECT id, if(waarde='',omschrijving,waarde) as waarde FROM CRM_selectievelden WHERE module='".$dbData['CRM_trekveld']."'";

    if($openVraag==true)
      $object->addField('openVraagId_'.$dbData['id'],
                        array("description"=>$dbData['vraagNummer']." ".$dbData['vraag'],
                              "default_value"=>"",
                              "db_size"=>"255",
                              "db_type"=>"text",
                              "form_type"=>"textarea",
                              "form_size"=>"50",
                              "form_rows"=>"4",
                              "form_visible"=>true,
                              "list_visible"=>true,
                              "list_width"=>"100",
                              "list_align"=>"left",
                              "list_search"=>false,
                              "list_order"=>"true"));
    else
  	  $object->addField('vraagId_'.$dbData['id'],
													array("description"=>$dbData['vraagNummer']." ".$dbData['vraag'],
													"default_value"=>"",
													"db_size"=>"11",
													"db_type"=>"int",
													"form_type"=>"selectKeyed",
                          "select_query"=>$antwoordQuery,
													"form_size"=>"11",
													"form_visible"=>true,
													"list_visible"=>true,
													"list_width"=>"100",
													"list_align"=>"left",
													"list_search"=>false,
													"list_order"=>"true"));
  
    $query="SELECT id,antwoordId,antwoordOpen FROM VragenIngevuld 
    WHERE relatieId='".$data['relatieId']."' AND vragenlijstId='".$data['vragenlijstId']."' AND vraagId='".$dbData['id']."' 
    AND date(add_date)='".$data['datum']."'";
    $db2->SQL($query); 
    $db2->Query();
    $record=$db2->nextRecord();
    if($action=='edit')
    {
      $data['vraagId_' . $dbData['id']] = $record['antwoordId'];
      $object->set('openVraagId_'.$dbData['id'],$record['antwoordOpen']);
    }
    $object->set('vraagId_'.$dbData['id'],$data['vraagId_'.$dbData['id']]);

    if($action=='update')
    {
      if($record['id'] > 0)
      {
        $query="UPDATE VragenIngevuld SET relatieId='".$data['relatieId']."', vragenlijstId='".$data['vragenlijstId']."', 
                vraagId='".$dbData['id']."', change_date=now(),change_user='$USR', antwoordId='".$data['vraagId_'.$dbData['id']]."', antwoordOpen='".$data['openVraagId_'.$dbData['id']]."' WHERE id='".$record['id']."'";
      }
      else
      {
        $query="INSERT INTO VragenIngevuld SET relatieId='".$data['relatieId']."', vragenlijstId='".$data['vragenlijstId']."', 
                vraagId='".$dbData['id']."', add_date=now(),add_user='$USR', antwoordId='".$data['vraagId_'.$dbData['id']]."', antwoordOpen='".$data['openVraagId_'.$dbData['id']]."'";
      }
      $db2->SQL($query);
      $db2->Query();
    }
    
    $formTemplate.='<div class="formblock">
<div style="width:800px;">{vraagId_'.$dbData['id'].'_description} {openVraagId_'.$dbData['id'].'_description} </div>
<div>{vraagId_'.$dbData['id'].'_inputfield} {openVraagId_'.$dbData['id'].'_inputfield} {vraagId_'.$dbData['id'].'_error}{openVraagId_'.$dbData['id'].'_error}</div>
<br>
</div>';
  }
  
  $formTemplate.='<div class="formblock">
<div class="formlinks">&nbsp;</div>
<div class="formrechts">
{change_user_value} {change_date_value}</div>
</div>

</form></div>';
  
  $editObject->formTemplate = $formTemplate;
 
}
// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");



echo $editObject->getOutput();

if($action=='update')
{
	header("Location: ".$returnUrl);
}
else 
{
	echo $_error = $editObject->_error;
}
?>
