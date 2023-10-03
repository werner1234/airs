<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 27 oktober 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/09/04 10:02:03 $
    File Versie         : $Revision: 1.3 $


 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/reconMonitor_importMatrixHelper.php");

session_start();

$db = new DB();

$subHeader     = "";
$mainHeader    = "Recon matrix overzicht";

$editScript = "reconMonitor_matrixEdit.php";
$allow_add  = false;

$hlp = new reconMonitor_importMatrixHelper();
//debug($_REQUEST, $hlp->lastDateDb);
$statusArray  = $hlp->statusArray;
$statusBG = $hlp->statusBG;
$statusFG = $hlp->statusFG;
$lastDate = $hlp->lastDateForm;
$optionsText = "\n\t<option value=''>---</option>";
foreach ($statusArray as $k=>$v)
{
  $optionsText .= "\n\t<option value='$k'>$v</option>";
}

$filter = $_GET['filterDatum'];
if ($filter == "")
{
  $filter = $hlp->lastDateDb;
}



$options = $hlp->createDateOptions($filter);

if ($_POST["action"] == "populate")
{
  echo "<div ><h2> ".$hlp->populateToday($_POST["fillDate"])." items toegevoegd </h2></div>";
  unset($_GET["fillMatrix"]);
}

if ($_POST["action"] == "verwerk")
{
  debug($_POST);
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
    $query = "UPDATE `reconMonitor_matrix` SET 
      `change_date` = NOW(),
      `change_user` = '{$USR}',
      `door`        = '{$USR}',
      `status` = '{$_POST["status"]}'
      
    WHERE id IN(".implode(",",$ids).")";
    $db->executeQuery($query);
  }

}

$todayCount = $hlp->checkMatrix(date("Y-m-d"));

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 500;




//$list->addColumn("MONITOR_importMatrix","id",array());
$list->addFixedField("reconMonitor_matrix", "datum");
$list->addFixedField("reconMonitor_matrix","bedrijf",array("search"=>true));
$list->addFixedField("reconMonitor_matrix","depotbank",array("search"=>true));
$list->addFixedField("reconMonitor_matrix","status",array("search"=>false));
$html = $list->getCustomFields('reconMonitor_matrix','reconMonitor_matrix');

$list->setWhere("DATE(datum) = '".$filter."'");

$_SESSION["submenu"] = New Submenu();
//$_SESSION['submenu']->addItem("Verwerk selectie","javascript:parent.frames['content'].verzenden('');");

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
  <b>$mainHeader</b> $subHeader (laatste vuldatum: $lastDate)
</div><br><br>";

$content["javascript"] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

";
$content["style"] = $editcontent['style'];
echo template($__appvar["templateContentHeader"],$content);





//if ($todayCount == 0)
{
?>
   <button id="btnGenerate" class="btnGreen"> recon Matrix vullen met datum </button> <input type="text" class="AIRSdatepicker" id="formFillDate" name="formFillDate" value="<?=date("d-m-Y")?>" />
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
  .selBox{
    padding: 10px;
    width: 800px;
    margin-bottom: 20px;
  }
</style>
<div class="msgBox">
  <span>Aantal Bestanden: </span><span id="msgBoxAantal">0</span>
</div>
  <form method='GET' name='controleForm'>
    Datum filter :
    <select name='filterDatum' onChange='document.controleForm.submit();'>
      <?=$options?>
    </select>
    <br/>
  </form>
<?
echo $list->filterHeader();

echo "
	
	
	<br>
	";


?>
  <form name="listForm" method="POST" id="chkBoxForm">
    <input type="hidden" name="action" id="action" value="verwerk" />
    <input type="hidden" name="fillDate" id="fillDate" value="" />

    <fieldset class="selBox">
      <div id="wrapper" style="overflow:hidden;">
        <div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(1);">&nbsp;&nbsp;<img src='icon/16/checks.png' class='simbisIcon' /> Alles selecteren</div>
        <div class="buttonDiv" style="width:150px;float:left;" onclick="checkAll(0);">&nbsp;&nbsp;<img src='icon/16/undo.png' class='simbisIcon' /> Niets selecteren</div>
        <div class="buttonDiv" style="width:160px;float:left;" onclick="checkAll(-1);">&nbsp;&nbsp;<img src='icon/16/replace2.png' class='simbisIcon' /> Selectie omkeren</div>
      </div>
      <br/>
      Wijzig de gemarkeerde naar de volgende status:
      <select name="status" id="statusInp">
        <?=$optionsText?>
      </select>  <button id="btnVerwerkSelectie">verwerk selectie</button>
      <br/>

      
    </fieldset>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php


while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
  $checkbox='<input type="checkbox" class="chkBox" name="rowId_'.$data['id']['value'].'" value="1" data-bestanden="'.$data["MONITOR_importMatrix.bestanden"]["value"].'"  >';//checked
  $list->editIconExtra=$checkbox;
//debug($data);
  $sts = $data["reconMonitor_matrix.status"]["value"];
  $data["reconMonitor_matrix.status"]["value"] = $statusArray[$sts];
  $data["reconMonitor_matrix.status"]["td_style"] = "style='background: {$statusBG[$sts]}; color: {$statusFG[$sts]}'";
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

  // function verzenden()
  // {
  //   if ( countCheck() > 0 && $("#statusInp").val() != "")
  //   {
  //     $("#chkBoxForm").submit();
  //   }
  //   else
  //   {
  //     alert('selectie is leeg');
  //   }
  // }

  $(document).ready(function ()
  {
    $(".chkBox").change(function(){
      countBestanden();
    });
    $("#btnVerwerkSelectie").click(function (e)
    {

        e.preventDefault();
        if ( countCheck() > 0 && $("#statusInp").val() != "")
        {
            $("#chkBoxForm").submit();
        }
        else
        {
            alert("selectie of status zijn niet gevuld");
        }

    });


    $("#btnGenerate").click(function (e)
    {
      e.preventDefault();
      $("#action").val("populate");
      var fDate = $("#formFillDate").val();
      $("#fillDate").val(fDate);
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
?>