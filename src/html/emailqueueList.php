<?php
/*
    AE-ICT CODEX source module versie 1.6, 2 juni 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/02/28 19:23:29 $
    File Versie         : $Revision: 1.30 $

    $Log: emailqueueList.php,v $
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/CRMeMailing.php");
include_once('../classes/AE_cls_phpmailer.php');
session_start();
$__appvar['rowsPerPage']=10000;

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "emailqueueEdit.php";
$allow_add  = true;

function createSelect($naam,$selectVelden,$selectedField)
{
  $txt='<select name="'.$naam.'">';
  foreach ($selectVelden as $key=>$omschrijving)
  {
    if($key==$selectedField)
      $selected='selected';
    else
      $selected='';
    $txt.='<option value="'.$key.'" '.$selected.' >'.$omschrijving."\n";
  }
  $txt.='</select>';
  return $txt;
}

//listarray($_SESSION);
unset($_SESSION['tableSettings']);
//exit;

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Verzend selectie"),'javascript:parent.frames[\'content\'].sendMails();');
$_SESSION['submenu']->addItem("<br>",'');
$_SESSION['submenu']->addItem(vt("Verwijder alle berichten"),'javascript:parent.frames[\'content\'].verwijderAlles();');
$_SESSION['submenu']->addItem(vt("Verwijder selectie"),'javascript:parent.frames[\'content\'].verwijderSelectie();');
$_SESSION['submenu']->addItem("<br>",'');
$_SESSION['submenu']->addItem(vt("Berichten samenvoegen"),'javascript:parent.frames[\'content\'].samenvoegenSelectie();');
$_SESSION['submenu']->addItem("<br>",'');
$_SESSION['submenu']->addItem(vt("Bijlage toevoegen"),'javascript:parent.frames[\'content\'].addAttachement();');
//emailqueueAddFile.php

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


$db = new DB();

if($_POST['actie']=='attach')
{ 
  include('emailqueueAddFile.php');
  exit;
}
if(isset($_GET['verdwijderBijlage']))
{
  $query="DELETE FROM emailQueueAttachments WHERE id='".$_GET['verdwijderBijlage']."'";
  $db->SQL($query);
  $db->Query();
}
if($_POST['actie']=='verwijderAlles')
{
  $query='TRUNCATE emailQueue';
  $db->SQL($query);
  $db->Query();
  $query="TRUNCATE emailQueueAttachments";
  $db->SQL($query);
  $db->Query();
}
if($_POST['actie']=='verwijderSelectie')
{
  $query="DELETE emailQueueAttachments FROM emailQueueAttachments JOIN emailQueue ON emailQueueAttachments.emailQueueId=emailQueue.id 
  WHERE emailQueue.id IN('".implode("','",$ids)."')";
  $db->SQL($query);
  $db->Query(); 
  $query="DELETE emailQueue FROM emailQueue WHERE emailQueue.id IN('".implode("','",$ids)."')";
  $db->SQL($query);
  $db->Query(); 
}

if($_POST['actie']=='samenvoegen')
{
  $groupEmail=array();
  $meldingen='';
  $query="SELECT count(emailQueue.id) as aantal, emailQueue.receiverEmail ,SUM(LENGTH(attachment)) as attachmentSize
  FROM emailQueue 
  LEFT JOIN emailQueueAttachments ON emailQueue.id=emailQueueAttachments.emailQueueId
  WHERE status='aangemaakt' AND emailQueue.id IN('".implode("','",$ids)."') GROUP BY emailQueue.receiverEmail HAVING aantal > 1";
  $db->SQL($query);
  $db->Query();
  while($data=$db->nextRecord())
  {
    if($data['attachmentSize'] > 8000000)
      $meldingen.="(".$data['aantal'].") " . vt('emails voor') . " (".$data['receiverEmail'].")\\n";
    else
      $groupEmail[]=$data['receiverEmail'];
  }
  
  if($meldingen <> '')
    echo "<script>alert(\"" . vt('De') . "\\n".$meldingen." " . vt('zijn niet samengevoegd omdat deze gezamelijk meer dan 8MB zijn.') . " \");</script>";

  foreach ($groupEmail as $emailAddress)
  {
    $n=0;
    $queries=array();
    $query="SELECT emailQueue.id FROM emailQueue WHERE status='aangemaakt' AND emailQueue.receiverEmail='$emailAddress' AND emailQueue.id IN('".implode("','",$ids)."')";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      if($n==0)
        $primaryId=$data['id'];
      else
      {
        $queries[]="UPDATE emailQueueAttachments SET emailQueueId='$primaryId' WHERE emailQueueId='".$data['id']."'";
        $queries[]="DELETE FROM emailQueue WHERE id='".$data['id']."'";
      }
      $n++;
    }
    foreach ($queries as $query)
    {
      $db->SQL($query);
      $db->Query();
    }
  }
}


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

/*
if($__appvar["bedrijf"]=='SEQ')
{
  $content['pageHeader'].="<input type='checkbox' name='debug' value='1' >";
}
*/

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


function addAttachement()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    document.listForm.actie.value='attach';
    document.listForm.submit();
  }
  else
  {
    alert('Geen emails geselcteerd.');
  }
}

function sendMails()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('" . vt('Wilt u') . " ' + numberSelected  + ' " . vt('emails verzenden?') . "');
    if(answer)
    {
      document.listForm.actie.value='verzenden';
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

function verwijderAlles()
{
  var answer = confirm('" . vt('Wilt u alle documenten verwijderen?') . "');
  if(answer)
  {
    document.listForm.actie.value='verwijderAlles';
    document.listForm.submit();
  }
}


function samenvoegenSelectie()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('" . vt('Wilt u') . " ' + numberSelected  + ' " . vt('documenten samenvoegen?') . "');
    if(answer)
    {
      document.listForm.actie.value='samenvoegen';
      document.listForm.submit();
      //alert('test');
    }
  }
  else
  {
    alert('Geen emails geselcteerd.');
  }
}

";

if($_POST['toXls'] != 1)
  echo template($__appvar["templateContentHeader"],$content);

if($_POST['actie']=='verzenden')
{ 
  echo '<span data-field="send-emails-message">';
  $verzend=new CRMeMailing();
  $verzend->verzendMails($ids,$_POST['toDdb'],$_POST['categorie']);
  echo '</span>';
}


$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->idTable="emailQueue";
$list->ownTables=array('emailQueue');
//$list->addColumn("EmailQueue","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","check",array("description"=>' ',"list_width"=>"20","search"=>false,'list_nobreak'=>true));
$list->addFixedField("EmailQueue","status",array("list_width"=>"100","search"=>false));
//$list->addFixedField("EmailQueue","senderName",array("list_width"=>"100","search"=>false));
//$list->addFixedField("EmailQueue","senderEmail",array("list_width"=>"100","search"=>false));
$list->addFixedField("EmailQueue","receiverName",array("list_width"=>"250","search"=>false));
$list->addFixedField("EmailQueue","receiverEmail",array("list_width"=>"250","search"=>false));
$list->addFixedField("EmailQueue","subject",array("list_width"=>"300","search"=>false));
//$list->addFixedField("Portefeuilles","Risicoklasse",array("list_width"=>"130","search"=>true,"list_order"=>true));
$list->addColumn("","bijlagen",array("list_width"=>"100","search"=>false,'sql_alias'=>'count(emailQueueAttachments.id)',"list_order"=>true));
$list->addColumn("","grootte",array("list_width"=>"100","search"=>false,'sql_alias'=>'sum(length(emailQueueAttachments.attachment))',"list_order"=>true,'list_invisible'=>true));

$list->categorieVolgorde=array('EmailQueue'=>array('Algemeen'),'Portefeuilles'=>array('Gegevens','Beheerfee','Staffels'));


$html = $list->getCustomFields(array('EmailQueue','Portefeuilles'),'EmailQueuelist');
$_SESSION['submenu']->addItem($html,"");



$list->setJoin("LEFT JOIN emailQueueAttachments ON emailQueue.id=emailQueueAttachments.emailQueueId
LEFT JOIN CRM_naw ON emailQueue.crmId = CRM_naw.id
LEFT JOIN Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0");
$list->setGroupBy('emailQueue.id');
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

if($_POST['toXls'] == 1)
  exit();
  

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


$query="SELECT omschrijving FROM (CRM_selectievelden) WHERE module = 'docCategrien'";
$db->SQL($query);
$db->query();
while($dbData=$db->nextRecord())
{
  $categorien[$dbData['omschrijving']]=$dbData['omschrijving'];
}

$htmlCategorie=createSelect('categorie',$categorien,'email');
?>
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<form name='listForm' method='POST' action='emailqueueList.php' >
<input type='hidden' name='actie' value='' >
<input type='hidden' name='idList' value='' >

<?= vt('Koppel CRM'); ?> <input type='checkbox' name='toDdb' value='1' onclick="if(this.checked){$('#ddCategorieSpan').show();} else{$('#ddCategorieSpan').hide();}" title="Emails bij verzending opslaan als CRM document?"> <span id="ddCategorieSpan" style="display: none"><?=$htmlCategorie?></span>
<br /><br />
<?=$list->printHeader();?>
<?
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
  $data['.bijlagen']['list_nobreak']=true;
  $data['.bijlagen']['value'] .= ' &nbsp;&nbsp;&nbsp; '.round(($data['.grootte']['value']/1024/1024),3)."MB";
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