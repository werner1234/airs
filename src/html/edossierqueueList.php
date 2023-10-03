<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 16 juli 2017
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2018/04/14 17:21:13 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: edossierqueueList.php,v $
    Revision 1.3  2018/04/14 17:21:13  rvv
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

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
$__appvar['rowsPerPage']=500;

$editScript = "edossierqueueEdit.php";
$allow_add  = true;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->idTable="eDossierQueue";
$list->ownTables=array('eDossierQueue');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Verzend selectie naar eDossier"),'javascript:parent.frames[\'content\'].sendDossier();');
$_SESSION['submenu']->addItem(vt("Verzend rapportages naar eDossier"),'edossierqueueList.php?verzend=1');
$_SESSION['submenu']->addItem("<br>",'');
$_SESSION['submenu']->addItem(vt("Verwijder selectie"),'javascript:parent.frames[\'content\'].verwijderSelectie();');
$_SESSION['submenu']->addItem(vt("Verwijder alle rapportages"),'edossierqueueList.php?verwijder=1');



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




function sendDossier()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('Wilt u ' + numberSelected  + ' documenten verzenden?');
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
    var answer = confirm('" . vt('Wilt u') . " ' + numberSelected  + ' " . vt('documenten verwijderen') . "?');
    if(answer)
    {
      document.listForm.actie.value='verwijderSelectie';
      document.listForm.submit();
      //alert('test');
    }
  }
}
";

if($_POST['toXls']!=1)
	echo template($__appvar["templateContentHeader"],$content);

$db = new DB();
if($_GET['verwijder']==1 || $_POST['actie']=='verwijderSelectie')
{
	$_GET['verwijder']=0;
	if($_POST['actie']=='verwijderSelectie')
	{
		$filter="WHERE id IN('".implode("','",$ids)."')";
	}

	$query='DELETE FROM eDossierQueue '.$filter;
	$db->SQL($query);
	$db->Query();
}
if($_GET['verzend']==1 || $_POST['actie']=='verzendSelectie')
{
	if ($_POST['actie'] == 'verzendSelectie')
	{
		$filter = "AND id IN('" . implode("','", $ids) . "')";
	}

	$_GET['verzend'] = 0;
	$db2 = new DB();
	$query = "SELECT id FROM eDossierQueue WHERE 1 $filter ";
	$db->SQL($query);
	$db->Query();
	$ids = array();
	while ($data = $db->nextRecord())
	{
		$ids[] = $data['id'];
	}

	foreach ($ids as $id)
	{
		$query = "SELECT filename,filesize,filetype,categorie,description,keywords,`module`,module_id,blobdata FROM eDossierQueue WHERE id='$id'";
		$db->SQL($query);
		$rec = $db->lookupRecord();
		$dd = new digidoc();
		$extraVelden = array();
		$dd->useZlib = false;
		$inportaal = false;
		if ($dd->addDocumentToStore($rec, $extraVelden) == false)
		{
			echo "" . vt('Niet gelukt om de rapportage') . " " . $rec['filename'] . " " . vt('in de eDossier te plaatsen') . ".<br>\n";
			flush();
			ob_flush();
		}
		else
		{
			echo "" . vt('Rapportage') . " " . $rec['filename'] . " " . vt('in eDossier geplaatst') . ". <br>\n";
			flush();
			ob_flush();
			$inportaal = true;
		}

		if ($inportaal)
		{
			$db2 = new DB();
			$db2->SQL("DELETE FROM eDossierQueue WHERE id='" . $id . "'");
			$db2->Query();
		}
	}
}
$list->addColumn("","check",array("description"=>' ',"list_width"=>"30","search"=>false,'list_nobreak'=>true));
$list->addFixedField("EDossierQueue","portefeuille",array("list_width"=>"120","search"=>false));
$list->addFixedField("EDossierQueue","filename",array("list_width"=>"150","search"=>false));
$list->addFixedField("EDossierQueue","filesize",array("list_width"=>"100","search"=>false));
$list->addFixedField("EDossierQueue","categorie",array("list_width"=>"100","search"=>false));
$list->addFixedField("EDossierQueue","description",array("list_width"=>"200","search"=>false));
$list->addFixedField("EDossierQueue","add_date",array("list_width"=>"120","search"=>false));
$list->addFixedField("EDossierQueue","add_user",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('EDossierQueue'),'EDossierQueuelist');
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

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

echo $list->filterHeader();
?>
	<table class="list_tabel" cellspacing="0">
		<form name='listForm' method='POST' action='edossierqueueList.php' >
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