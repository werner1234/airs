<?php
/*
    AE-ICT CODEX source module versie 1.6, 13 juni 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/04/29 15:56:45 $
    File Versie         : $Revision: 1.22 $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AE_cls_digidoc.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "portaalqueueEdit.php";
$allow_add  = true;

if(isset($_POST))
{
  $ids=array();
  foreach($_POST as $key=>$value)
  { 
    if(substr($key,0,6)=='check_')
    { 
      $ids[]=substr($key,6);
    }
  }
}
$__appvar['rowsPerPage']=100;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->idTable="portaalQueue";
$list->ownTables=array('portaalQueue');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Verzend selectie naar portaal"),'javascript:parent.frames[\'content\'].sendPortaal();');
$_SESSION['submenu']->addItem(vt("Verzend rapportages naar portaal"),'portaalqueueList.php?verzend=1');
$_SESSION['submenu']->addItem("<br>",'');
$_SESSION['submenu']->addItem(vt("Verwijder selectie"),'javascript:parent.frames[\'content\'].verwijderSelectie();');
$_SESSION['submenu']->addItem(vt("Verwijder alle rapportages"),'portaalqueueList.php?verwijder=1');




$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";


$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br>

<div id=\"wrapper\" style=\"overflow:hidden;\"> 
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> " . vt('Alles selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> " . vt('Niets selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:160px;float:left;\" onclick=\"checkAll(-1);\">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> " . vt('Selectie omkeren') . "</div>
</div>

<br>
";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function checkAll(optie)
{
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
   {
      if(optie == -1)
      {
        if(theForm[z].checked == true)
          theForm[z].checked=false;
        else
          theForm[z].checked=true;  
      }
      else
      {
        theForm[z].checked = optie;
      }
   }
  }
}


function countCheck()
{
  var counter=0;
  var theForm = document.listForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
    if(theForm[z].type == 'checkbox' && theForm[z].name.substr(0,6) == 'check_')
    {
      if(theForm[z].checked == true)
        counter++;
    }
  }
  return counter;
}




function sendPortaal()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('" . vt('Wilt u') . " ' + numberSelected  + ' " . vt('documenten verzenden?') . "');
    if(answer)
    {
      document.listForm.actie.value='verzendSelectie';
      document.listForm.submit();
      //alert('test');
    }
  }
}

function verwijderSelectie()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('" . vt('Wilt u') . " ' + numberSelected  + ' " . vt('documenten verwijderen?') . "');
    if(answer)
    {
      document.listForm.actie.value='verwijderSelectie';
      document.listForm.submit();
      //alert('test');
    }
  }
}
";

//listarray($_GET);listarray($_POST);listarray($ids);exit;
$headerSend=false;
$db = new DB();
if($_GET['verwijder']==1 || $_POST['actie']=='verwijderSelectie')
{
  $_GET['verwijder']=0;
  if($_POST['actie']=='verwijderSelectie')
  {
    $filter="WHERE id IN('".implode("','",$ids)."')";
  }
  
  $query='DELETE FROM portaalQueue '.$filter;
  $db->SQL($query);
  $db->Query();
}
if($_GET['verzend']==1 || $_POST['actie']=='verzendSelectie')
{
  echo template($__appvar["templateContentHeader"],$content);
  $headerSend=true;
  if($_POST['actie']=='verzendSelectie')
  {
    $filter="AND id IN('".implode("','",$ids)."')";
  }
  
  $_GET['verzend']=0;
  $dbPortaal = new DB(DBportaal);
  $query='DESC clienten';
  $dbPortaal->SQL($query);
  $dbPortaal->Query();
  while($data=$dbPortaal->nextRecord())
    $velden[]=$data['Field'];
  $veldenKoppeling=array('name'=>'naam','name1'=>'naam1','email'=>'email','password'=>'crmWachtwoord','verzendAanhef'=>'verzendAanhef','depotbank'=>'depotbank',
                         'accountmanagerNaam'=>'accountmanagerNaam','accountmanagerGebruikerNaam'=>'accountmanagerGebruikerNaam',
                         'accountmanagerEmail'=>'accountmanagerEmail','accountmanagerTelefoon'=>'accountmanagerTelefoon','rel_id'=>'crmId');
    
  $db2 = new DB();
  $query="SELECT id FROM portaalQueue WHERE status='aangemaakt' $filter ";
  $db->SQL($query);
  $db->Query();
  $ids=array();
  while ($data = $db->nextRecord())
  {
    $ids[]=$data['id'];
  }

  foreach($ids as $id)
  {
    $query="SELECT * FROM portaalQueue WHERE id='$id'";
    $db->SQL($query);
    $data=$db->lookupRecord();
      $emails = explode(';', $data['email']);
      $data['email'] = $emails[0];
      $dd = new digidoc(DBportaal);
      $dbPortaal = new DB(DBportaal);
      $querySet = '';
      foreach ($velden as $veld)
      {
        if ($veldenKoppeling[$veld])
        {
          $querySet .= ',' . $veld . "= '" . mysql_real_escape_string($data[$veldenKoppeling[$veld]]) . "'";
        }
      }

      $q = "SELECT id FROM clienten WHERE portefeuille='" . $data['portefeuille'] . "'";
      if ($dbPortaal->QRecords($q) == 0)
      {
        $q = "INSERT INTO clienten SET change_user='$USR',change_date=now(),add_user='$USR',add_date=now(),
          portefeuille='" . $data['portefeuille'] . "' $querySet ,passwordChange=now(),passwordTimes=0,loginTimes=0,loginLast='0000-00-00 00:00:00'";
        $dbPortaal->SQL($q);
        $dbPortaal->Query();
        $clientId = $dbPortaal->last_id();
      }
      else
      {
        $clientId = $dbPortaal->lookupRecord();
        $clientId = $clientId['id'];
        $query = "UPDATE clienten SET change_date=now() $querySet WHERE id='$clientId'";
        $dbPortaal->SQL($query);
        $dbPortaal->Query();
      }

      $inportaal = false;
      if ($clientId > 0)
      {
        if (strtoupper($data['periode']) == 'C')
        {
          if($data['pdfData']<>'')
          {
            $q = "DELETE FROM datastoreDaily WHERE clientID='$clientId'";
            $dbPortaal->SQL($q);
            $dbPortaal->Query();
            $q = "INSERT INTO  datastoreDaily SET change_user='$USR',change_date=now(),add_user='$USR',add_date=now(),
        clientID='$clientId',reportDate='" . $data['raportageDatum'] . "',filename='" . $data['filename'] . "',filesize='" . strlen($data['pdfData']) . "',
        filetype='application/pdf',description='" . str_replace($data['portefeuille'], '', $data['filename']) . "',
        blobdata=unhex('" . bin2hex($data['pdfData']) . "') ";
            $dbPortaal->SQL($q);
            if ($dbPortaal->Query())
            {
              echo "" . vt('Dag rapportage') . " " . $data['portefeuille'] . " " . vt('in portaal geplaatst') . ". <br>\n";
              flush();
              ob_flush();
              $inportaal = true;
            }
          }
          else
          {
            $inportaal = true;
          }
        }
        else
        {
          if($data['pdfData']<>'')
          {
            $extraVelden = array();
            $rec ["filename"] = $data['filename'];
            $rec ["filesize"] = strlen($data['pdfData']);
            $rec ["filetype"] = "application/pdf";
            $rec ["description"] = str_replace($data['portefeuille'], '', $data['filename']);
            $rec ["blobdata"] = $data['pdfData'];
            $rec ["keywords"] = $data['filename'];
            $rec ["categorie"] = $data['periode'];
            $rec ["module"] = 'CRM_naw';
            $rec ["module_id"] = $data['crmId'];
  
            $month = 0;
            $quater = 0;
            $year = 0;
  
            if (strtoupper($data['periode']) == 'M')
            {
              $month = substr($data['raportageDatum'], 5, 2);
            }
            elseif (strtoupper($data['periode']) == 'K')
            {
              $quater = floor(date("n", db2jul($data['raportageDatum'])) / 3);
            }
            //elseif(strtoupper($data['periode'])=='J')
            $year = substr($data['raportageDatum'], 0, 4);
  
            $extraVelden = array('month' => $month, 'quater' => $quater, 'year' => $year, 'clientID' => $clientId, 'reportDate' => $data['raportageDatum']);
  
            $dd->useZlib = false;
            if ($dd->addDocumentToStore($rec, $extraVelden) == false)
            {
              echo "" . vt('Niet gelukt om de rapportage') . " " . $data['portefeuille'] . " " . vt('in de portaal te plaatsen') . ".<br>\n";
              flush();
              ob_flush();
            }
            else
            {
              echo "" . vt('Rapportage') . " " . $data['portefeuille'] . " " . vt('in portaal geplaatst') . ". <br>\n";
              flush();
              ob_flush();
              $inportaal = true;
            }
          }
          else
          {
            $inportaal = true;
          }
        }
      }

      if ($data['pdfFactuurData'] <> '' && $inportaal == true)
      {
        $file = "factuur_" . $data['filename'];
        $filesize = strlen($data['pdfFactuurData']);
        $filetype = 'application/pdf';

        $dd = new digidoc();
        $rec = array();
        $rec ["filename"] = $file;
        $rec ["filesize"] = "$filesize";
        $rec ["filetype"] = "$filetype";
        $rec ["description"] = str_replace($data['portefeuille'], '', $file);
        $rec ["blobdata"] = $data['pdfFactuurData'];
        $rec ["keywords"] = $file;
        $rec ["module"] = 'CRM_naw';
        $rec ["module_id"] = $data['crmId'];
        $rec ["categorie"] = 'Factuur';
        $dd->useZlib = false;
        $dd->addDocumentToStore($rec);

        if (true)//naar portaal
        {
          $airsRefId = $dd->referenceId;
          $dd = new digidoc(DBportaal);
          $dd->useZlib = false;
          $rec ["module_id"] = $clientId;
          $rec ["module"] = 'clienten';
          $extraVelden = array('portaalKoppelId' => $airsRefId, 'reportDate' => date('Y-m-d'), 'clientID' => $clientId);
          if ($dd->addDocumentToStore($rec, $extraVelden) == false)
          {
            echo "" . vt('Niet gelukt om document in de portaal te plaatsen.') . "<br>\n";
            flush();
            ob_flush();
            $inportaal = false;
          }
          $db = new DB();
          $query = "UPDATE dd_reference SET portaalKoppelId='" . $dd->referenceId . "' WHERE id='$airsRefId'";
          $db->SQL($query);
          $db->Query();
        }

      }

      if ($inportaal)
      {
        $db2 = new DB();
        $db2->SQL("DELETE FROM portaalQueue WHERE id='" . $data['id'] . "'");
        $db2->Query();
      }

  }
}

$list->addColumn("","check",array("description"=>' ',"list_width"=>"20","search"=>false,'list_nobreak'=>true));
$list->addFixedField("PortaalQueue","status",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortaalQueue","periode",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortaalQueue","raportageDatum",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortaalQueue","portefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortaalQueue","naam",array("list_width"=>"100","search"=>false));
$list->addFixedField("PortaalQueue","naam1",array("list_width"=>"100","search"=>false));

$list->categorieVolgorde=array('PortaalQueue'=>array('Algemeen'));
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');
$html = $list->getCustomFields(array('PortaalQueue','Portefeuilles'),'PortaalQueuelist');

$extraTabelJoinAdded=array();
foreach ($list->columns as $colData)
{
  if(in_array($colData['objectname'],$extraTabelJoinAdded))
    continue;
  if($colData['objectname'] == 'Portefeuilles')
  {
    $joinEvenementen = " LEFT JOIN CRM_evenementen ON CRM_naw.id = CRM_evenementen.rel_id ";
    $extraTabelJoinAdded[] = $colData['objectname'];
    $joinPortefeuilles = "LEFT JOIN Portefeuilles ON portaalQueue.portefeuille = Portefeuilles.Portefeuille";
  }
}
$list->setJoin($joinPortefeuilles);

$_SESSION['submenu']->addItem($html,"");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

if($headerSend==false)
  echo template($__appvar["templateContentHeader"],$content);
?>

<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<form name='listForm' method='POST' action='portaalqueueList.php' >
<input type='hidden' name='actie' value='' >
<input type='hidden' name='idList' value='' >
<?=$list->printHeader();?>
<?
while($data = $list->getRow())
{
  $data['.check']['value']="<input type=\"checkbox\" name=\"check_".$data['id']['value']."\" value=\"1\" >";
  $data['.check']['noClick'] =true;
	echo $list->buildRow($data);
}
?>
</table>
</form>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>
