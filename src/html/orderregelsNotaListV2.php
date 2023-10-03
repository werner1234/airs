<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 mei 2006
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/10/08 06:24:40 $
    File Versie         : $Revision: 1.8 $

    $Log: orderregelsNotaListV2.php,v $

*/
include_once("wwwvars.php");
include_once("../config/ordersVars.php");
include_once("../classes/mysqlList.php");
include_once("./rapport/rapportRekenClass.php");
include_once("./orderControlleRekenClass.php");
include_once("./printNota2PDFv2.php");
$js = "";
$downloadLink='';
$errorMsg = "";
if(isset($_POST))
{
  $ids=array();
  $printIds = array();
  $emailIds = array();
  $actie=$_POST['actie'];
  if($_POST['emailIds']<>''||$_POST['printIds']<>'')
  {
    if(trim($_POST['emailIds']) <> '')
    {
      $ids=explode('|',$_POST['emailIds']);
    }

    if(trim($_POST['printIds']) <> '')
    {
      $printIds=explode('|',$_POST['printIds']);
    }

    $actie='verzendenConfirmed';
  }
  elseif($actie=='verzenden' OR $actie=='testPdf' OR $actie=='moduleZ')
  {
    foreach ($_POST as $key => $value)
    {
      if (substr($key, 0, 6) == 'check_')
      {
        $ids[] = substr($key, 6);
      }
    }
  }

  if($actie=='verzenden')
  {
    $db = new DB();
    $query = "SELECT OrderRegelsV2.id, OrderRegelsV2.portefeuille, OrderRegelsV2.client, CRM_naw.email FROM OrderRegelsV2 LEFT JOIN CRM_naw ON OrderRegelsV2.portefeuille = CRM_naw.portefeuille WHERE OrderRegelsV2.id IN('" . implode("','", $ids) . "')";
    $db->SQL($query);
    $db->Query();
    $missendeEmail = array();
    $htmlUitvoer='';
    while ($email = $db->nextRecord())
    {
      if ($email['email'] == '')
      {
        $printIds[$email['id']] = $email['portefeuille'] . '-' . $email['client'];
        $missendeEmail[$email['portefeuille'] . '-' . $email['client']][] = $email['id'];
      }
      else
      {
        $emailIds[$email['id']] = $email['portefeuille'] . '-' . $email['client'];
      }
    }
    if (count($printIds) > 0)
    {
      $htmlUitvoer = "<fieldset style='width: 600px'><b>Voor de onderstaande relaties is geen email adres geconfigureerd:</b><br>\n";
      foreach ($missendeEmail as $naam => $idArray)
      {
        $htmlUitvoer .= "$naam <br>\n";
      }
      $htmlUitvoer .= "<br>\n Aantal te mailen Nota's:" . count($emailIds) . "<br>\n Aantal te printen Nota's: " . count($printIds) . "<br>\n <br>\n ";
      $htmlUitvoer .= "<form name='printForm' method='POST' action='orderregelsNotaListV2.php' >
    <input type='hidden' name='emailIds' value='" . implode('|', array_keys($emailIds)). "'>
    <input type='hidden' name='printIds' value='" . implode('|', array_keys($printIds)). "'>
    <div class=\"buttonDiv\" style=\"width:230px;float:left;\" onclick=\"document.printForm.submit();\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> Mails en pdf aanmaken.</div>
    <br>\n <br>\n </form></fieldset>";
      $actie='confirm';
    }
  }

  $nota=new createNotas();
  if($actie=='testPdf')
  {
    $nota->newPDF();
    foreach($ids as $regelId)
    {
      $nota->newNota($regelId);
    }    
    $nota->outputPdfToTemp('test');
    if(count($nota->exportFiles)==1)
    {
      $downloadLink='<a href=\'showTempfile.php?show=1&filename='.basename($nota->exportFiles[0]).'&unlink=1\'>Export voltooid <b>download pdf</b></a><br><br>';
    }

  }
  elseif($actie=='verzenden' || $actie=='verzendenConfirmed')
  {
    foreach($ids as $regelId)
    {
      $nota->newPDF();
      $nota->newNota($regelId);
      $nota->outputPdfToEmailqueue($regelId);
    }
    if(count($printIds) > 0)
    {
      $nota->newPDF();
      foreach ($printIds as $regelId)
      {
        $nota->newNota($regelId);
        $nota->setPrintDate($regelId);
      }
      $nota->outputPdfToTemp(date('Ymd_Hi'));
      if (count($nota->exportFiles) == 1)
      {
        $downloadLink = '<a href=\'showTempfile.php?show=1&filename=' . basename($nota->exportFiles[0]) . '&unlink=1\'>Export voltooid <b>download pdf</b></a><br><br>';
      }
    }
  }
  elseif(substr($actie,0,7) =='moduleZ')
  {
    $idArr = array();
    foreach ($_POST as $key=>$value)
    {

      $p = explode("_",$key);
      if ($p[0] == "check")
      {
        $idArr[] = $p[1];
      }
    }


    $_SESSION["moduleZ_data"] = $idArr;
    $mzType = substr($actie,8);
    $js = "
    $('#modulezMsg').load('moduleZ_POST_trade.php?mzType=".$mzType."');
    $('#modulezMsg').show(300);
    ";
  }
  elseif($actie  == 'addBatchId')
  {
    $db = new DB();
//    debug($_POST);
    foreach ($_POST as $key=>$value)
    {
      $p = explode("_",$key);
      if ($p[0] == "check")
      {
        $idArr[] = $p[1];
      }
    }
    if (count($idArr) > 0)
    {
      $query = "SELECT count(id) as tel FROM `OrderRegelsV2` WHERE `id` IN ('".implode("','",$idArr)."') AND `externeBatchId` != ''";
      $recTest = $db->lookupRecordByQuery($query);
      if ($recTest["tel"] > 0)
      {
        $errorMsg = "Fout: niet alle regels hebben een leeg batchId, bewerking afgebroken";
      }
      else
      {
        $query = "UPDATE `OrderRegelsV2` SET `externeBatchId` = 'HM-AIRS".date("Ymd")."T".date("His")."' WHERE `id` IN ('".implode("','",$idArr)."')";
        $db->executeQuery($query);
      }




    }

  }
}


session_start();
$__appvar['rowsPerPage']=3000;

$subHeader     = "";
$mainHeader    = vt("Orderregels");

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
  $queryWhere = " AND OrderRegelsV2.printDate = '0000-00-00' ";
  $selectedFilter['nieuw']='checked';
}

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br>$downloadLink

 $htmlUitvoer
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
function createModuleZ(mzType)
{
  var numberSelected=countCheck();
  if(numberSelected > 0)
  {
    var answer = confirm('Wilt u ' + numberSelected  + ' orders exporteren naar moduleZ?');
    if(answer)
    {
      document.listForm.actie.value='moduleZ-'+mzType;
      document.listForm.submit();
     
    }
  }
}

function createBatchid()
{
  var numberSelected=countCheck();
  
  if(numberSelected > 0)
  {
    var answer = confirm('Wilt u ' + numberSelected  + ' orders een nieuw batchId geven?');
    if(answer)
    {
      document.listForm.actie.value='addBatchId';
      document.listForm.submit();
    }
  }
}
 
";

?>
<?

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

$editScript = "orderregelsEditV2.php";
$allow_add  = false;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;


$list->perPage = $__appvar['rowsPerPage'];
$list->idTable="OrderRegelsV2";
$list->ownTables=array('OrderRegelsV2');

$list->addColumn("","check",array("description"=>' ',"list_width"=>"50","search"=>false,'list_nobreak'=>true));
$list->addFixedField("OrderRegelsV2","orderid",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","fondsOmschrijving",array("list_width"=>"250","search"=>false));
$list->addFixedField("OrdersV2","fondsCode",array('description'=>'ISIN-code',"list_width"=>"100","search"=>false));
$list->addFixedField("OrderRegelsV2","aantal",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrderRegelsV2","portefeuille",array("list_width"=>"100","search"=>false));
//$list->addFixedField("OrderRegelsV2","printDate",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","transactieSoort",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrdersV2","transactieType",array("list_width"=>"100","search"=>false));
$list->addFixedField("OrderRegelsV2","externeBatchId",array("list_width"=>"160","search"=>false));

$list->categorieVolgorde=array('OrdersV2'=>array('Algemeen'),
                               'OrderRegelsV2'=>array('Algemeen'),
                               'Naw'=>array("Algemeen","Adres","Verzendadres","Telefoon","Internetgegevens","Bedrijfinfo","Persoonsinfo","Legitimatie","Informatie partner","Legitimatie partner","Adviseurs","geen",'Contract','Beleggen','Rapportage','Profiel','Relatie geschenk','Recordinfo'));

$html = $list->getCustomFields(array('OrdersV2','OrderRegelsV2','Naw'),'OrderNotaQueueList');
$list->setJoin("JOIN OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id 
LEFT JOIN CRM_naw ON OrderRegelsV2.portefeuille=CRM_naw.portefeuille");

if($__appvar["bedrijf"] == "FDX" )
  $list->setWhere("OrdersV2.orderStatus > 2  AND OrdersV2.orderStatus < 5  $queryWhere");
else
  $list->setWhere("OrdersV2.orderStatus > 1  AND OrdersV2.orderStatus < 5  $queryWhere");

$list->setOrder($_GET['sort'],$_GET['direction']);
$list->setSearch($_GET['selectie']);
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

echo template($__appvar["templateContentHeader"],$content);

?>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <style>
    .errMsg{
      display: flex;
      margin: 10px;
      max-width: 800px;
      min-height: 100px;
      background: Maroon;
      color: white;
      font-size: 16px;
      justify-content: center;
      align-items: center;
    }
    #modulezMsg{

      display: none;
      margin: 10px;
      width: 90%;
      min-height: 100px;
      background: lemonchiffon;

    }

  </style>
  <div id="modulezMsg"><i class="fa fa-spinner fa-spin" style="font-size:36px"></i> <?= vt('moment aub'); ?></div>
<?
if ($errorMsg != "")
{
  echo "<div class='errMsg'>{$errorMsg}</div>";
}

$selectieKnoppen="<div id=\"wrapper\" style=\"overflow:hidden;\"> 
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> " . vt('Alles selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> " . vt('Niets selecteren') . "</div>
<div class=\"buttonDiv\" style=\"width:160px;float:left;\" onclick=\"checkAll(-1);\">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> " . vt('Selectie omkeren') . "</div>
</div>";
?>
<br>

<?=$list->filterHeader();?>
<input type="radio"  name='notafilter' value='nieuw' <?=$selectedFilter['nieuw']?>  onClick="document.location = '<?=$PHP_SELF?>?notafilter=nieuw'"/>
<label> <?= vt("Nieuwe nota's"); ?>  </label>

<input type='radio' name='notafilter' value='alles' <?=$selectedFilter['alles']?>  onClick="document.location = '<?=$PHP_SELF?>?notafilter=alles'"/>
<label> <?= vt("Alle nota's"); ?> </label>

<table class="list_tabel" cellspacing="0">
<form name='listForm' method='POST' action='orderregelsNotaListV2.php' >
<input type='hidden' name='actie' value='' >
<input type='hidden' name='idList' value='' >



<br><br>
<?
echo $selectieKnoppen;

?>
<br><br>
<?=$list->printHeader();?>

<?

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("Test pdf voor selectie",'javascript:parent.frames[\'content\'].createTestPdf();');
$_SESSION['submenu']->addItem("Mail nota's voor selectie",'javascript:parent.frames[\'content\'].createMails();');
$_SESSION['submenu']->addItem("Nota Email opmaak",'orderregelsNotaEmailOpmaak.php');
if ($__appvar["moduleZ"] == 1)
{
  $_SESSION['submenu']->addItem("Export ModuleZ MM",'javascript:parent.frames[\'content\'].createModuleZ(\'mm\');');
  $_SESSION['submenu']->addItem("Export ModuleZ Reb.",'javascript:parent.frames[\'content\'].createModuleZ(\'reb\');');
  $_SESSION['submenu']->addItem("Genereer batchId",'javascript:parent.frames[\'content\'].createBatchid();');
}
$_SESSION['submenu']->addItem("<br>".$html,'');



while($data = $list->getRow())
{
  $data['.check']['value']="<input type=\"checkbox\" name=\"check_".$data['id']['value']."\" value=\"1\" >";
  $data['.check']['value'].="<a href=\"printNota2PDFv2.php?regelId=".$data['id']['value']."\">".drawButton("afdrukken","","maak nota")."</a>";
  $data['.check']['noClick'] =true;
  $data['transactieType']['value']=$__ORDERvar["transactieType"][$data['transactieType']['value']];
  $data['transactieSoort']['value']=$__ORDERvar["transactieSoort"][$data['transactieSoort']['value']];
  echo $list->buildRow($data);
}
?>
</table>
</form>



<script>
  $(document).ready(function () {
    <?=$js?>
  });
</script>

<?
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$editcontent);
