<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "vermogensbeheerderEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("Vermogensbeheerder","id",array("width"=>100,"search"=>false));

$list->addFixedField("Vermogensbeheerder","Vermogensbeheerder",array("width"=>150,"search"=>true));
$list->addFixedField("Vermogensbeheerder","Naam",array("search"=>true));
$list->addFixedField("Vermogensbeheerder","Adres",array("width"=>200,"search"=>false));
$list->addFixedField("Vermogensbeheerder","Woonplaats",array("width"=>200,"search"=>false));

if(checkAccess($type)) 
{
	// superusers
	$allow_add = true;
}
else 
{
	// normale users mogen alleen hun eigen vermogensbeheerders zien
	$list->setJoin("INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' ");
	$allow_add = false;
}
$html = $list->getCustomFields('Vermogensbeheerder');
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);


$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem($html,"");

session_start();
$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";


$db = new DB();

echo template($__appvar["templateContentHeader"],$content);
?>

  <link rel="stylesheet" href="style/fontAwsomePro/fontawesome-all.min.css">
<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
//while($data = $list->printRow())
while($data = $list->getRow())
{

  if ($__appvar["bedrijf"] == "HOME" OR $__appvar["bedrijf"] == "TEST")
  {
    $vb = $data["Vermogensbeheerders.Vermogensbeheerder"]["value"];
    $query = "SELECT * FROM `VermogensbeheerdersPerBedrijf` WHERE `Vermogensbeheerder` = '{$vb}'";
    $bedrijfRec = $db->lookupRecordByQuery($query);
    $data["Vermogensbeheerders.Vermogensbeheerder"]["value"] = "<button class='btnCrm' data-bedrijf='{$bedrijfRec['Bedrijf']}' title='" . vt('ga naar CRM') . " -> {$bedrijfRec['Bedrijf']}'> <i class='fa fa-sign-out-alt'></i> </button> ".$vb;
  }


  echo $list->buildRow($data);
}
?>
</table>
</form>



<script>
  $(document).ready(function(){
    $(".btnCrm").click(function (e) {
      e.preventDefault();
      var bc = $(this).data("bedrijf");
      var url = "<?=$__appvar["crmUrl"]?>"+ bc;
      console.log(url);
      window.open(url,"_blank");
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