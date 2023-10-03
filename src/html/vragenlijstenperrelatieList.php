<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 22 november 2017
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/04/10 09:20:32 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: vragenlijstenperrelatieList.php,v $
    Revision 1.4  2019/04/10 09:20:32  cvs
    call 6257



*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("Vragenlijsten per relatie overzicht");
//debug($_GET);
$editScript = "vragenlijstenperrelatieEdit.php";
$allow_add  = true;

if ($_GET["copyId"] > 0)
{
  include_once ("../classes/AIRS_vragen_helper.php");
  $vrgHelper = new AIRS_vragen_helper($_GET["copyId"]);
  $v = $vrgHelper->crmRefRec;

  $query = "INSERT INTO VragenLijstenPerRelatie SET ";
  $query .= " `change_user` = '$USR', ";
  $query .= " `change_date` = NOW(), ";
  $query .= " `add_user` = '$USR', ";
  $query .= " `add_date` = NOW(), ";
  $query .= " `nawId` ='".$v["nawId"]."', ";
  $query .= " `vragenLijstId` ='".$v["vragenLijstId"]."', ";
  $query .= " `zichtbaarInPortaal` ='".$v["zichtbaarInPortaal"]."', ";
  $query .= " `portaalStatus` ='herhaal', ";
  $query .= " `omschrijving` ='(kopie) ".$v["omschrijving"]."', ";
  $query .= " `log` ='".date("d-m-Y h:i")." aangemaakt via kopieerknop' ";
  $db  = new DB();
  $db2 = new DB();
  $db->executeQuery($query);
  $lastid = $db->last_id();

  $query = "SELECT * FROM VragenIngevuld WHERE crmRef_id = ".$_GET["copyId"];
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $query = "INSERT INTO VragenIngevuld SET ";
    $query .= " `change_user` = '$USR', ";
    $query .= " `change_date` = NOW(), ";
    $query .= " `add_user` = '$USR', ";
    $query .= " `add_date` = NOW(), ";
    $query .= " `datum` = NOW(), ";
    $query .= " `relatieId` ='".$rec["relatieId"]."', ";
    $query .= " `vragenlijstId` ='".$rec["vragenlijstId"]."', ";
    $query .= " `vraagId` ='".$rec["vraagId"]."', ";
    $query .= " `antwoordId` ='".$rec["antwoordId"]."', ";
    $query .= " `antwoordOpen` ='".$rec["antwoordOpen"]."', ";
    $query .= " `crmRef_id` ='".$lastid."' ";
    $db2->executeQuery($query);
  }
  header("location: ".$_SESSION["crmRefUrl"]."&msg=copied");
  exit;
}
else
{
  $_SESSION["crmRefUrl"] = $_SERVER["REQUEST_URI"];
}


unset($_SESSION['submenu']);

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("VragenLijstenPerRelatie","id",array("search"=>false));

$list->addColumn("","copy",array("search"=>false,"list_width"=>"100","description"=>"kopie"));
//$list->addColumn("VragenLijstenPerRelatie","vragenLijstId",array("search"=>false));
$list->addColumn("VragenLijstenPerRelatie","datum",array("search"=>false));
$list->addColumn("VragenLijstenPerRelatie","omschrijving",array("search"=>true, "width"=>"500"));
$list->addColumn("VragenLijstenPerRelatie","zichtbaarInPortaal",array("search"=>false));
$list->addColumn("VragenLijstenPerRelatie","portaalStatus",array("search"=>false));
$list->addColumn("VragenLijstenPerRelatie","portaalDatumIngevuld",array("search"=>false));
//$list->addColumn("VragenLijstenPerRelatie","crmRef_id",array("list_invisible"=>true));

//$list->addColumn("VragenLijstenPerRelatie","memo",array("search"=>false));
//$list->addColumn("VragenLijstenPerRelatie","log",array("search"=>false));

$list->setWhere("nawId = ".$_GET["rel_id"]);
// set default sort
// $_GET['sort'][]      = "tablename.field";
// $_GET['direction'][] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new&rel_id=".$_GET["rel_id"]."';
}
";
//debug($content);
echo template($__appvar["templateContentHeader"],$content);
?>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">


<?
  //echo "<h2>vragenlijst is gekopieerd</h2>";
?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$dbl = new DB();
while($data = $list->getRow())
{
  $query="SELECT id FROM VragenIngevuld WHERE crmRef_id = '".$data["id"]["value"]."' ";
  $vrgRec = $dbl->lookupRecordByQuery($query);
	// $list->buildRow($data,$template="",$options="");
  $data['copy']['value']='<a class="icon" target="pdf" href="vragenantwoordenPrint.php?id='.$vrgRec['id'].'&score=0"> '.maakKnop('pdf.png',array('size'=>16,'tooltip'=>'Print zonder cijfers')).'</a>';
  $data['copy']['value'].='&nbsp;&nbsp; <a class="icon" target="pdf" href="vragenantwoordenPrint.php?id='.$vrgRec['id'].'&score=1"> '.maakKnop('pdf.png',array('size'=>16,'tooltip'=>'Print met cijfers')).'</a>';
  $id=$data["id"]["value"];
  $data["copy"]["value"] .= "&nbsp;&nbsp;<button class='btnCopy' id='btn_$id' data-id='$id' title='" . vt('kopieer vragenlijst met antwoorden') . "'><i class='fa fa-clone' aria-hidden='true'></i></button>";
	echo $list->buildRow($data);
}
?>
</table>

<script>
  $(document).ready(function(){
    $(".btnCopy").click(function(e){
      e.preventDefault();

      var copyId = $(this).data("id");
      location.assign("?rel_id=<?=$_GET["rel_id"]?>&copyId="+copyId);

    });
  });
</script>
<?
logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>