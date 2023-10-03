<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/05/13 13:49:14 $
    File Versie         : $Revision: 1.5 $


 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/MONITOR_importMatrixHelper.php");

session_start();

$db = new DB();

$subHeader     = "";
$mainHeader    = vt("import matrix overzicht");

$editScript = "MONITOR_importMatrixEdit.php";
$allow_add  = false;

$hlp = new MONITOR_importMatrixHelper();
$lastDate = $hlp->lastDateForm;

$filter = $_GET['filterDatum'];
if ($filter == "")
{
  $filter = $hlp->lastDateDb;
}

$options = $hlp->createDateOptions($filter);

if ($_POST["action"] == "populate")
{
  echo "<div ><h2> ".$hlp->populateToday()." " . vt('items toegevoegd') . " </h2></div>";
  unset($_GET["fillMatrix"]);
}

if ($_POST["action"] == "verwerk")
{
  foreach ($_POST as $key=>$value)
  {
    $p = explode("_", $key);
    if ($p[0] == "rowId")
    {
      $ids[] = $p[1];
    }
  }
  if (count($ids) > 0)
  {
    $db = new DB();
    $query = "UPDATE `MONITOR_importMatrix` SET 
      `change_date` = NOW(),
      `change_user` = '{$USR}',
      `door`        = '{$USR}',
      `verwerkt`    = {$_POST["verwerkStatus"]}
    WHERE id IN(".implode(",",$ids).")";
    $db->executeQuery($query);
//    debug($query);
  }

}

$todayCount = $hlp->checkMatrix(date("Y-m-d"));

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 500;




//$list->addColumn("MONITOR_importMatrix","id",array());
$list->addFixedField("MONITOR_importMatrix", "add_date");
$list->addFixedField("MONITOR_importMatrix","bedrijf",array("search"=>true));
$list->addFixedField("MONITOR_importMatrix","depotbank",array("search"=>true));
$list->addFixedField("MONITOR_importMatrix","bestanden",array("search"=>false));
//$list->addFixedField("MONITOR_importMatrix","autoPortaalVulling",array("search"=>false));
//$list->addFixedField("MONITOR_importMatrix","verwerkPrio",array("search"=>false));
$html = $list->getCustomFields('MONITOR_importMatrix','MONITOR_importMatrix');

$list->setWhere("DATE(add_date) = '".$filter."'");

$_SESSION["submenu"] = New Submenu();
$_SESSION['submenu']->addItem(vt("Verwerk selectie"),"javascript:parent.frames['content'].verzenden(1);");
$_SESSION["submenu"]->addItem("&nbsp;","");
$_SESSION['submenu']->addItem(vt("Verwerk selectie (onhold)"),"javascript:parent.frames['content'].verzenden(2);");

$_SESSION["submenu"]->addItem($html,"");

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $list->perPage ,$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));

$content["pageHeader"] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader (" . vt('laatste vuldatum') . ": $lastDate)
</div><br><br>";

$content["javascript"] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

";
echo template($__appvar["templateContentHeader"],$content);

if ($todayCount == 0)
{
?>
   <button id="btnGenerate" class="btnGreen"> <?= vt('Matrix vullen met vandaag'); ?> </button>
   <br/><br/>

<?
}

?>

<style>
  .msgBox{
    border:1px solid #999;
    width: 150px;
    padding: 10px;
    margin-left:400px;
    background: whitesmoke;
  }
  #msgBoxAantal{
    font-size: 20px;
    font-weight: bold;
    padding: 5px;
  }
</style>
<div class="msgBox">
  <span><?= vt('Aantal Bestanden'); ?>: </span><span id="msgBoxAantal">0</span>
</div>
  <form method='GET' name='controleForm'>
    <?= vt('Datum filter'); ?> :
    <select name='filterDatum' onChange='document.controleForm.submit();'>
      <?=$options?>
    </select>
    <br/>
  </form>
<?
echo $list->filterHeader();

echo "
	<div id=\"wrapper\" style=\"overflow:hidden;\">
		<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(1);\">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> " . vt('Alles selecteren') . "</div>
		<div class=\"buttonDiv\" style=\"width:150px;float:left;\" onclick=\"checkAll(0);\">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> " . vt('Niets selecteren') . "</div>
		<div class=\"buttonDiv\" style=\"width:160px;float:left;\" onclick=\"checkAll(-1);\">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> " . vt('Selectie omkeren') . "</div>
	</div>
	
	<br>
	";


?>
  <form name="listForm" method="POST" id="chkBoxForm">
    <input type="hidden" name="action" id="action" value="verwerk" />
    <input type="hidden" name="verwerkStatus" id="verwerkStatus" value="1" />
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php


while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
  $checkbox='<input type="checkbox" class="chkBox" name="rowId_'.$data['id']['value'].'" value="1" data-bestanden="'.$data["MONITOR_importMatrix.bestanden"]["value"].'"  >';//checked
  $list->editIconExtra=$checkbox;

	echo $list->buildRow($data);
}
?>
</table>
  </form>

<link rel="stylesheet" href="style/AIRS_default.css" type="text/css" media="screen">
<script>
  function countBestanden()
  {
    var count = 0;
    $(".chkBox").each(function()
    {

      if ($(this).prop("checked"))
      {
        count += $(this).data("bestanden");
      }
    });

    $("#msgBoxAantal").text(count);
  }

  function checkAll(optie)
  {
    var cur;

    $(".chkBox").each(function()
    {
      if (optie == -1)
      {
        cur = $(this).prop( "checked");
      }
      else
      {
        cur = (optie != 1);
      }
      $(this).prop( "checked", !cur );
    });

    countBestanden();
  }

  function countCheck()
  {
    var counted = 0;

    $(".chkBox").each(function()
    {
      if ( $(this).prop("checked"))
      {
        counted++;

      }
    });

    return counted;
  }

  function verzenden(status=1)
  {
    $("#verwerkStatus").val(status);
    if ( countCheck() > 0)
    {
      $("#chkBoxForm").submit();
    }
    else
    {
      alert('selectie is leeg');
    }
  }

  $(document).ready(function ()
  {
    $(".chkBox").change(function(){
      countBestanden();
    });
    $("#btnGenerate").click(function (e)
    {
      e.preventDefault();
      $("#action").val("populate");
      $("#chkBoxForm").submit();
    })
  })
</script>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
