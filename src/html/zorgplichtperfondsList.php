<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "zorgplichtperfondsEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addField("ZorgplichtPerFonds","id",array("width"=>100,"search"=>false));
$list->addFixedField("ZorgplichtPerFonds","Vermogensbeheerder",array("width"=>100,"search"=>true));
$list->addFixedField("ZorgplichtPerFonds","Percentage",array("width"=>100,"search"=>true));
$list->addFixedField("ZorgplichtPerFonds","Zorgplicht",array("width"=>100,"search"=>true));
$list->addFixedField("ZorgplichtPerFonds","Fonds",array("width"=>100,"search"=>true));

$html = $list->getCustomFields(array('ZorgplichtPerFonds')); 
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>","");
$_SESSION['submenu']->addItem($html,"");

if(checkAccess($type)) 
{
	// superusers
	$allow_add = true;
}
else 
{
	// normale user
	$allow_add = false;
}


// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";

$content['style'] .= $editcontent['style'];
$content['jsincludes'] .= $editcontent['jsincludes'];
echo template($__appvar["templateContentHeader"],$content);

$query="
  SELECT
    ZorgplichtPerFonds.Fonds,
    ZorgplichtPerFonds.Vermogensbeheerder,
    SUM(ZorgplichtPerFonds.Percentage) as totaal,
    ZorgplichtPerFonds.add_date,
    ZorgplichtPerFonds.Zorgplicht
  FROM 
    ZorgplichtPerFonds 
  GROUP BY 
      ZorgplichtPerFonds.Vermogensbeheerder,
      ZorgplichtPerFonds.Fonds
  HAVING 
    totaal > 100
  ORDER BY 
    ZorgplichtPerFonds.Fonds,
    ZorgplichtPerFonds.add_date desc 
";


$db = new DB();
$db->SQL($query);
$db->query();
if($db->records() > 0)
{
  $table = '<br />
  <table class="table table-compact" style="width:350px;">
    <thead>
      <tr>
        <td><strong>'.vt("Vb").'</strong></td>
        <td><strong>'.vt("Fonds").'</strong></td>
        <td><strong>'.vt("Totaal percentage").'</strong></td>
      </tr>  
    </thead>';
  while ($data = $db->NextRecord())
  {
    if ( $data['totaal'] != 100 ) {
      $table.="<tr><td>".$data['Vermogensbeheerder']."</td><td>".$data['Fonds']."</td><td align='right' class='tableTotal'>".number_format($data['totaal'], 2)."</td></tr>";
    }
  }
  $table.="</table><br />";
}



?>
<br>
<?=$list->filterHeader();?>
<?=$table;?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>