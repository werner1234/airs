<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "valutakoersenEdit.php";

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->noExport=true;


$list->addFixedField("Valutakoersen","Valuta",array("list_width"=>150,"search"=>true));
$list->addFixedField("Valutakoersen","Datum",array("search"=>false));
$list->addFixedField("Valutakoersen","Koers",array("list_width"=>100,"align"=>"right","list_format"=>"%01.8f"));

$html = $list->getCustomFields(array('Valutakoersen'));
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>", "");
$_SESSION['submenu']->addItem($html, "");

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
if($_GET['Valuta']=='')
  $_GET['Valuta']='recent';
if(isset($_POST['filter_0_veldnaam']) && $_GET['Valuta']=='recent')
{
  $_GET['Valuta']='geen';
}

if(!empty($_GET['Valuta']) && $_GET['Valuta'] <> 'geen' && $_GET['Valuta'] <> 'recent' )
{
	$list->setWhere(" Valuta = '".$_GET['Valuta']."' ");

	$db=new DB();
	$query="SELECT year(Datum) as jaar FROM Valutakoersen WHERE Valuta='".$_GET['Valuta']."' GROUP BY jaar ORDER By jaar";
	$db->SQL($query);
	$db->Query();
	$jaren=array();
	while($data= $db->nextRecord())
	  $jaren[]=$data['jaar'];

	$koersenHtml="<table border=1>\n<tr class=\"list_kopregel\"><td class=\"list_kopregel_data\" width=100>Datum</td><td class=\"list_kopregel_data\" width=100 align=right>Koers</td></tr>\n";
	foreach ($jaren as $jaar)
	{
	  $query="SELECT Koers,Datum FROM Valutakoersen WHERE Valuta='".$_GET['Valuta']."' AND year(Datum)='$jaar' ORDER BY Datum desc limit 1";
	  $db->SQL($query);
	  $db->Query();
	  $data= $db->nextRecord();
	  $koersenHtml.="<tr><td>".dbdate2form($data['Datum'])."</td><td align=right>".$data['Koers']."</td></tr>\n";
	}
	$koersenHtml.="</table>\n";
}
elseif($_GET['Valuta'] =='' || $_GET['Valuta'] =='recent')
{
	$list->setWhere(" Datum = '".getLaatsteValutadatum()."' ");
}

$DB = new DB();
$DB->SQL("SELECT Valuta FROM Valutas ORDER BY Valuta ASC");
$DB->Query();
while($data = $DB->NextRecord())
{
	$options .= "<option value=\"".$data['Valuta']."\" ".($_GET['Valuta']==$data['Valuta']?"selected":"").">".$data['Valuta']."</option>\n";
}

if(empty($_GET['sort'])) {
	$_GET['sort'] = array("Datum");
	$_GET['direction'] = array("DESC");
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
	parent.frames['content'].location = '".$editScript."?action=new&Valuta=".$Valuta."';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<form action="valutakoersenList.php" method="GET"  name="controleForm">
<?=vt('Valuta');?> :
<select name="Valuta" onChange="document.controleForm.submit();">
<option value="geen" <?=($_GET['Valuta']=='geen')?'selected':''?>>--</option>
<option value="recent" <?=($_GET['Valuta']=='recent')?'selected':''?>><?=vt('meest recent');?></option>
<?=$options?>
</select>
<input type="submit" value="<?=vt('Overzicht');?>">
</form>
<br>
<br>
<table>
<tr>
<td>
  <?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->printRow())
{
	echo $data;
}
?>
</table>
</td>
<td> &nbsp;&nbsp;</td>
<td valign="top">
<?=$koersenHtml?>
</td>
</tr>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>