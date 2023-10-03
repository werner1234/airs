<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/03/15 16:34:28 $
    File Versie         : $Revision: 1.5 $
*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/mysqlList.php");
include_once("./rapport/rapportRekenClass.php");
include_once("./orderControlleRekenClass.php");
include_once("./printNota2PDF.php");

$downloadLink='';
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

  $nota=new createNotas();
  if($_POST['actie']=='testPdf')
  {
    $nota->newPDF();
    foreach($ids as $regelId)
    {
      $nota->newNota($regelId);
    }    
    $nota->outputPdfToTemp('test');
    if(count($nota->exportFiles)==1)
      $downloadLink='<a href=\'showTempfile.php?show=1&filename='.basename($nota->exportFiles[0]).'&unlink=1\'>Export voltooid <b>download pdf</b></a><br><br>';
  }
  elseif($_POST['actie']=='verzenden')
  {
    foreach($ids as $regelId)
    {
      $nota->newPDF();
      $nota->newNota($regelId);
      $nota->outputPdfToEmailqueue($regelId);
    }
  }
}


session_start();
$__appvar['rowsPerPage']=1000;

$subHeader     = "";
$mainHeader    = "Orderregels";

$selectedFilter=array('nieuw'=>'','alles'=>'');
if($_GET['notafilter'] == "alles" )
{
	$filter = "alles";
	$queryWhere = "";
  $selectedFilter['alles']='checked';
}
else
{
  $filter = "nieuw";
  $queryWhere = " AND OrderRegels.printDate = '0000-00-00' ";
  $selectedFilter['nieuw']='checked';
}

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br>$downloadLink
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

function createMails()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('Wilt u ' + numberSelected  + ' nota(s) aanmaken?');
    if(answer)
    {
      document.listForm.actie.value='verzenden';
      document.listForm.submit();
      //alert('test');
    }
  }
}

function createTestPdf()
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('Wilt u ' + numberSelected  + ' nota(s) aanmaken?');
    if(answer)
    {
      document.listForm.actie.value='testPdf';
      document.listForm.submit();
      //alert('test');
    }
  }
}
";

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

$editScript = "orderregelsEdit.php";
$allow_add  = false;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;


$list->perPage = $__appvar['rowsPerPage'];
$list->idTable="OrderRegels";
$list->ownTables=array('OrderRegels');

$list->addColumn("","check",array("description"=>' ',"list_width"=>"50","search"=>false,'list_nobreak'=>true));
$list->addFixedField("OrderRegels","orderid",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orders","fondsOmschrijving",array("list_width"=>"250","search"=>false));
$list->addFixedField("Orders","fondsCode",array('description'=>'ISIN-code',"list_width"=>"100","search"=>false));
$list->addFixedField("OrderRegels","aantal",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrderRegels","portefeuille",array("list_width"=>"100","search"=>false));
//$list->addFixedField("OrderRegels","printDate",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orders","transactieSoort",array("list_width"=>"100","search"=>false));
$list->addFixedField("Orders","transactieType",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields(array('Orders','OrderRegels'),'OrderNotaQueueList');
$list->setJoin("JOIN Orders ON OrderRegels.orderid = Orders.orderid ");
$list->setWhere("Orders.LaatsteStatus > 2  AND Orders.LaatsteStatus < 5  $queryWhere");

$list->setOrder($_GET['sort'],$_GET['direction']);
$list->setSearch($_GET['selectie']);
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

echo template($__appvar["templateContentHeader"],$content);

$selectieKnoppen="<div id=\"wrapper\" style=\"overflow:hidden;\"> 
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> " . vt('Alles selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> " . vt('Niets selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:160px;float:left;\" onclick=\"checkAll(-1);\">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> " . vt('Selectie omkeren') . "</div>
</div>";
?>
<br>
<?=$list->filterHeader();?>
<input type="radio"  name='notafilter' value='nieuw' <?=$selectedFilter['nieuw']?>  onClick="document.location = '<?=$PHP_SELF?>?notafilter=nieuw'"/>
<label> <?= vt('Nieuwe nota\'s'); ?> </label>

<input type='radio' name='notafilter' value='alles' <?=$selectedFilter['alles']?>  onClick="document.location = '<?=$PHP_SELF?>?notafilter=alles'"/>
<label> <?= vt('Alle nota\'s'); ?> </label>

<table class="list_tabel" cellspacing="0">
<form name='listForm' method='POST' action='orderregelsNotaList.php' >
<input type='hidden' name='actie' value='' >
<input type='hidden' name='idList' value='' >



<br><br>
<?=$selectieKnoppen?>
<br><br>
<?=$list->printHeader();?>

<?

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Test pdf voor selectie"),'javascript:parent.frames[\'content\'].createTestPdf();');
$_SESSION['submenu']->addItem(vt("Mail nota's voor selectie"),'javascript:parent.frames[\'content\'].createMails();');
$_SESSION['submenu']->addItem("<br>".$html,'');



while($data = $list->getRow())
{
  $data['.check']['value']="<input type=\"checkbox\" name=\"check_".$data['id']['value']."\" value=\"1\" >";
  $data['.check']['value'].="<a href=\"printNota2PDF.php?regelId=".$data['id']['value']."\">".drawButton("afdrukken","",vt("maak nota"))."</a>";
  $data['.check']['noClick'] =true;
  $data['transactieType']['value']=$__ORDERvar["transactieType"][$data['transactieType']['value']];
  $data['transactieSoort']['value']=$__ORDERvar["transactieSoort"][$data['transactieSoort']['value']];
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
echo template($__appvar["templateRefreshFooter"],$editcontent);
?>