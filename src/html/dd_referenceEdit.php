<?php
/*
    AE-ICT CODEX source module versie 1.6, 14 november 2009
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/03/25 16:10:56 $
    File Versie         : $Revision: 1.16 $

    $Log: dd_referenceEdit.php,v $
    Revision 1.16  2020/03/25 16:10:56  cvs
    call 7883

    Revision 1.15  2019/08/23 11:30:31  cvs
    call 8024

    Revision 1.14  2018/09/05 15:48:53  rvv
    *** empty log message ***

    Revision 1.13  2018/09/03 05:31:52  rvv
    *** empty log message ***

    Revision 1.12  2018/08/18 12:40:14  rvv
    php 5.6 & consolidatie

    Revision 1.11  2015/11/11 13:34:34  rvv
    *** empty log message ***

    Revision 1.10  2015/11/11 13:23:49  rvv
    *** empty log message ***

    Revision 1.9  2015/05/27 16:15:51  rvv
    *** empty log message ***

    Revision 1.8  2015/05/16 06:21:57  rvv
    *** empty log message ***

    Revision 1.7  2015/05/10 08:01:01  rvv
    *** empty log message ***

    Revision 1.6  2015/05/06 15:56:16  rvv
    *** empty log message ***

    Revision 1.5  2015/04/12 08:55:14  rvv
    *** empty log message ***

    Revision 1.4  2012/11/25 13:15:50  rvv
    *** empty log message ***

    Revision 1.3  2012/04/30 11:03:06  rvv
    *** empty log message ***

    Revision 1.2  2009/11/22 14:07:41  rvv
    *** empty log message ***

    Revision 1.1  2009/11/15 16:45:31  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");
include_once("../classes/AE_cls_digidoc.php");

$subHeader = "";
$mainHeader    = vt("muteren");

$__funcvar['listurl']  = "dd_referenceList.php";
$__funcvar['location'] = "dd_referenceEdit.php";

$object = new Dd_reference();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$editcontent['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b> ".$subHeader."</div><br><br>";

$editObject->template = $editcontent;

$data = array_merge($_POST,$_GET);
$action = $data['action'];

$portefeuille='';

if($data['toPortaal']==1)
{
  $db=new DB();
  if($data['rel_id']==0)
  {
    $query="SELECT module_id FROM dd_reference WHERE id='".$data['id']."'";
    $db->SQL($query);
    $crmId=$db->lookupRecord();
    $data['rel_id']=$crmId['module_id'];
  }
  $query="SELECT portefeuille, CRMGebrNaam FROM CRM_naw WHERE id='".$data['rel_id']."'";
  $CRMRec=$db->lookupRecordByQuery($query);

  if(trim($CRMRec['portefeuille']) == '' AND $CRMRec['CRMGebrNaam'] != '')
  {
    $CRMRec['portefeuille'] ='P'.str_pad($CRMRec['CRMGebrNaam'], 6, '0', STR_PAD_LEFT);
  }

  $portefeuille=$CRMRec['portefeuille'];
  $db=new DB(DBportaal);
  $query="SELECT id FROM clienten WHERE portefeuille='".mysql_real_escape_string($portefeuille)."'";
  $db->SQL($query);
  $clientData=$db->lookupRecord();
  if($clientData['id'] > 0)
  $clientId=$clientData['id'];
  if($clientId == '' || $portefeuille=='')
  {
    logit("Digidoc::portefeuille ( $portefeuille ) niet gevonden in portaal. Export naar portaal afgebroken" );
    echo vtb("Client met portefeuille ( %s ) niet gevonden in portaal. Export naar portaal afgebroken. %s", array($portefeuille, $query));
    exit;
  }

}

$editObject->includeHeaderInOutput = true;  // geen templateheaders in $editObject->output toevoegen
$editObject->usetemplate = true;
$editObject->formTemplate = 'dd_referenceEditTemplate.html';

// mogelijkheid om value's aan te passen bv. : $object->set("field",$object->get("fiel")." bladiebla");
$db=new DB();
$query="SELECT max(check_portaalDocumenten) as aan FROM Vermogensbeheerders";
$db->SQL($query);
$portaal=$db->lookupRecord();

if($data['id'] > 1 && isset($_DB_resources[DBportaal]['server']) && $portaal['aan']==1)
{
  $db=new DB(DBportaal);
  $query="SELECT id FROM dd_reference WHERE portaalKoppelId='".$data['id'] ."'";
  $db->SQL($query);
  $db->Query();
  $portaalRef=$db->nextRecord();

  if($portaalRef['id']>0)
    $editObject->formVars['toPortaal']='<div class="formblock">
    <div class="formlinks"> ' . vt('Document is in de portaal aanwezig.') . ' </div>
    <div class="formrechts">
    <input type="checkbox" name="deleteFromPortaal" id="deleteFromPortaal" value="1"> ' . vt('Document uit portaal verwijderen?') . '
    </div>
    </div>';
  else
    $editObject->formVars['toPortaal']='<div class="formblock">
    <div class="formlinks">' . vt('Document naar portaal zenden.') . '</div>
    <div class="formrechts">
    <input type="checkbox" name="toPortaal" id="toPortaal" value="1">
    </div>
    </div>';  
}

if($data['id'] < 1 )
{
  if(isset($_DB_resources[DBportaal]['server']) && $portaal['aan']==1)
  {
    $editObject->formVars['toPortaal']='<div class="formblock">
    <div class="formlinks">' . vt('Document naar portaal zenden.') . '</div>
    <div class="formrechts">
    <input type="checkbox" name="toPortaal" id="toPortaal" value="1">
    </div>
    </div>';
  }
  $editObject->formVars['rel_id']=$data['rel_id'];
  $editObject->formVars['toevoegenFile']='<div class="formblock">
   <div class="formlinks">' . vt('Bestand toevoegen') . '</div>
   <div class="formrechts">
   <input type="file" name="importfile" size="50">
   </div>
   </div>';

  include_once ("../classes/AE_cls_fileUpload.php");
  $upl = new AE_cls_fileUpload();

  if($_FILES['importfile']['tmp_name'] <> '' )
  {
    if ($upl->checkExtension($_FILES['importfile']['name']))
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
      $rec ["description"] = $data['description'];
      $rec ["blobdata"] = $docdata;
      $rec ["keywords"] = $data['keywords'];
      $rec ["module"] = 'CRM_naw';
      $rec ["module_id"] = $data['rel_id'];
      $rec ["categorie"] = $data['categorie'];
      $dd->useZlib = false;
      if (!$dd->addDocumentToStore($rec))
      {
        logit("Digidoc::afgebroken door foutmelding.." );
        echo "<br> " . vt('afgebroken door foutmelding.');
        exit;
      }
      else
      {
        logit("Digidoc::document {$file} toegevoegd aan relatie CRM_naw id {$data['rel_id']}" );
      }

      logDD_refference($dd->referenceId,'id','','Toegevoegd.');
      $data['id']=$dd->referenceId;
      if($data['toPortaal'])
      {



        $airsRefId=$dd->referenceId;
        $dd = new digidoc(DBportaal);
        $dd->useZlib = false;
        $rec ["module_id"] = $clientId;
        $rec ["module"] = 'clienten';
        $extraVelden=array('portaalKoppelId'=>$airsRefId,'reportDate'=>date('Y-m-d'),'clientID'=>$clientId);
        if($dd->addDocumentToStore($rec,$extraVelden) == false)
        {
          logit("Digidoc::Niet gelukt om document in de portaal te plaatsen, client id {$clientId}" );
          echo vt('Niet gelukt om document in de portaal te plaatsen.') . "<br>\n";flush(); ob_flush();
        }
        else
        {
          logit("Digidoc::document {$file} in portaal geplaastst aan relatie client id {$clientId}" );
        }
        logDD_refference($airsRefId,'portaalKoppelId','Toegevoegd aan portaal.',$dd->referenceId);
        $object->set('portaalKoppelId',$dd->referenceId);
      }
      header("Location: ".$returnUrl);
      exit;
    }
    else
    {
      echo "<h3>" . vt('ongeldig bestand opgegeven') . "</h3>";
      exit;
    }

  }

}


$editObject->controller($action,$data);
$rootReference=$object->get('rootReference');
$dd_id=$object->get('dd_id');
$refId=$object->get('id');

echo $editObject->getOutput();

if($data['id'] > 0 && $action=='update' )
{
  if($data['toPortaal'])
  {
    $db=new DB();
    $query="SELECT blobdata,blobCompressed FROM ".$object->get('datastore')." WHERE id='".$object->get('dd_id')."'";
    $db->SQL($query);
    $blobRecord=$db->lookupRecord();
    if($blobRecord['blobCompressed'])
      $blobRecord['blobdata']=gzuncompress($blobRecord['blobdata']);
      
    $airsRefId=$dd->referenceId;
    $dd = new digidoc(DBportaal);
    $rec=array();
    $rec ["filename"] = $object->get('filename');
    $rec ["filesize"] = $object->get('filesize');
    $rec ["filetype"] = $object->get('filetype');
    $rec ["description"] = $object->get('description');
    $rec ["blobdata"] = $blobRecord['blobdata'];
    $rec ["keywords"] = $object->get('keywords');
    $rec ["module"] = 'clienten';
    $rec ["module_id"] = $clientId;
    $rec ["categorie"] = $object->get('categorie');
    $dd->useZlib = $blobRecord['blobCompressed'];
    $extraVelden=array('portaalKoppelId'=>$object->get('id'),'reportDate'=>$object->get('add_date'),'clientID'=>$clientId);
    if($dd->addDocumentToStore($rec,$extraVelden) == false)
    {
      logit("Digidoc::Niet gelukt om document in de portaal te plaatsen, client id {$clientId}" );
      echo vt('Niet gelukt om document in de portaal te plaatsen.') . "<br>\n";flush(); ob_flush();
    }

    if($dd->referenceId>0)
    {
      logit("Digidoc::document {$rec ["filename"]} in portaal geplaastst aan relatie client id {$clientId}" );
      logDD_refference($data['id'],'portaalKoppelId','Toegevoegd aan portaal.',$dd->portaalKoppelId);
      $object->set('portaalKoppelId',$dd->referenceId);
      $object->save();
    }
  }
  if($portaalRef['id']>0)
  {
    $logAccessBackup=$__appvar['logAccess'];
    $__appvar['logAccess']=false;
    $dataPortaal=$data;
    $dataPortaal['id']=$portaalRef['id'];
    $dataPortaal['portaalKoppelId']=$data['id'];
    unset($dataPortaal['module']);
    unset($dataPortaal['module_id']);
    unset($dataPortaal['dd_id']);
    unset($dataPortaal['datastore']);
    $objectPortaal = new Dd_reference(DBportaal);
    $objectPortaal->dbId=DBportaal;
    $editObjectPortaal =new editObject($objectPortaal,DBportaal);
    $editObjectPortaal->__funcvar = $__funcvar;
    $editObjectPortaal->__appvar = $__appvar;
    $editObjectPortaal->controller($action,$dataPortaal);
    $__appvar['logAccess']=$logAccessBackup;
  }
}



if ($result = $editObject->result)
{
  if($_POST['deleteFromPortaal']==1)
  {
    deleteFromPortaal($refId);
  }
  
  if($action=='delete')
  {
    deleteFromPortaal($refId);
    if($rootReference > 0)
    {

      $db = new DB();
      $query="DELETE FROM ".$object->get('datastore')." WHERE id='$dd_id' ";
      $db->SQL($query);
      $db->Query();
      logit("Digidoc::portaalKoppelId $refId (".$object->get("filename").") verwijderd" );
      logDD_refference($refId,'portaalKoppelId','','Verwijderd.');
    }
  }

	header("Location: ".$returnUrl);
}
else
{
	echo $_error = $editObject->_error;
}

function deleteFromPortaal($refId)
{
  global $_DB_resources;
  if(isset($_DB_resources[DBportaal]['server']))
  {
    $db=new DB(DBportaal);
    $query="SELECT id,dd_id,datastore FROM dd_reference WHERE portaalKoppelId='".$refId."'";
    $db->SQL($query);
    $portaalRec=$db->lookupRecord();
    if($portaalRec['datastore'] <> '' && $portaalRec['dd_id'] > 0)
    {
      $query="DELETE FROM ".$portaalRec['datastore']." WHERE id='".$portaalRec['dd_id']."'";
      $db->SQL($query);
      $db->Query();
      $query="DELETE FROM dd_reference WHERE id='".$portaalRec['id']."'";
      $db->SQL($query);
      $db->Query();
      logit("Digidoc::portaalKoppelId {$refId} verwijderd uit portaal");
      logDD_refference($refId,'portaalKoppelId','','Verwijderd uit portaal.');
    }

    $object=new Dd_reference();
    $object->getById($refId);
    if($object->get('id')>0)
    {
      $object->set('portaalKoppelId', 0);
      $object->save();
    }
  }
}

function logDD_refference($recordId,$veld,$oude,$nieuwe)
{
  global $USR;
  $db=new DB();
  $query="INSERT INTO  trackAndTrace SET tabel='dd_reference', recordId ='$recordId',veld='$veld',oudeWaarde='".mysql_real_escape_string($oude)."',nieuweWaarde='".mysql_real_escape_string($nieuwe)."',add_date=now(),add_user='$USR'";
  $db->SQL($query);
  $db->query();
}
?>