<?php
/*
    AE-ICT CODEX source module versie 1.6, 18 mei 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/07/22 18:20:50 $
    File Versie         : $Revision: 1.6 $

    $Log: bestandsvergoedingperportefeuilleList.php,v $
    Revision 1.6  2017/07/22 18:20:50  rvv
    *** empty log message ***

    Revision 1.5  2012/04/08 08:10:43  rvv
    *** empty log message ***

    Revision 1.4  2011/11/19 15:41:14  rvv
    *** empty log message ***

    Revision 1.3  2011/09/28 18:44:42  rvv
    *** empty log message ***

    Revision 1.2  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.1  2011/05/18 16:50:14  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

if(getVermogensbeheerderField('module_bestandsvergoeding')==2)
{
  $invoer=true;
  $hideEdit=false;
}
else
{
 $invoer=false;
 $hideEdit=true;
}

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "bestandsvergoedingperportefeuilleEdit.php";

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
if($_POST['toXls']=='')
  echo template($__appvar["templateContentHeader"],$content);
if(!isset($_GET['bestandsvergoedingId']))
{

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 1000;


if($invoer==false)
{
  $_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem("Terug naar bestandsvergoedingen ",'bestandsvergoedingenList.php');
}
$list->addColumn("BestandsvergoedingPerPortefeuille","portefeuille",array("list_width"=>"100","search"=>false));
$list->addColumn("Portefeuilles","Client",array("list_width"=>"100","search"=>false));
$list->addColumn("BestandsvergoedingPerPortefeuille","bedragBerekend",array("list_width"=>"100","search"=>false));
$list->addColumn("BestandsvergoedingPerPortefeuille","bedragUitbetaald",array("list_width"=>"100","search"=>false));
$list->addColumn("BestandsvergoedingPerPortefeuille","datumUitbetaald",array("list_width"=>"100","search"=>false));
if($invoer)
  $list->addColumn("BestandsvergoedingPerPortefeuille","Fonds",array("list_width"=>"200","search"=>false));

$list->setWhere("Portefeuilles.Portefeuille=BestandsvergoedingPerPortefeuille.portefeuille AND Portefeuilles.consolidatie=0");


$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->setFilter();
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$invoer));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


?>


<table class="list_tabel" cellspacing="0">
<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader($hideEdit);?>
<?php


while($data = $list->getRow())
{
  if($hideEdit)
    $data['id']['value']=0;
  $data['disableEdit']=$hideEdit;
	echo $list->buildRow($data);

}


}
else
{

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = 1000;

if($invoer==false)
{
  $_SESSION['submenu'] = New Submenu();
  $_SESSION['submenu']->addItem("Terug naar bestandsvergoedingen ",'bestandsvergoedingenList.php');
}

$db=new DB();
$query="SELECT waardeBerekend,waardeHerrekend FROM Bestandsvergoedingen WHERE id='".$_GET['bestandsvergoedingId']."'";
$db->SQL($query);
$bestandsvergoeding=$db->lookupRecord();
$factor=$bestandsvergoeding['waardeHerrekend']/$bestandsvergoeding['waardeBerekend'];

$list->addColumn("BestandsvergoedingPerPortefeuille","portefeuille",array("list_width"=>"100","search"=>false));
$list->addColumn("Portefeuilles","Client",array("list_width"=>"100","search"=>false));
$list->addColumn("BestandsvergoedingPerPortefeuille","bedragBerekend",array("list_width"=>"100","search"=>false));
$list->addColumn("","bedragHerrekend",array("list_width"=>"100","search"=>false,"list_format"=>"%01.2f"));
$list->addColumn("BestandsvergoedingPerPortefeuille","bedragUitbetaald",array("list_width"=>"100","search"=>false));
$list->addColumn("BestandsvergoedingPerPortefeuille","datumUitbetaald",array("list_width"=>"100","search"=>false));

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setWhere("bestandsvergoedingId='".$_GET['bestandsvergoedingId']."' AND Portefeuilles.Portefeuille=BestandsvergoedingPerPortefeuille.portefeuille");

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader($hideEdit);?>
<?php
$totalen['disableEdit']=$hideEdit;
$totalen['portefeuille']['value']='&nbsp;';
$totalen['client']['value']='<b>Totaal</b>';
$totalen['bedragBerekend']=array("list_format"=>"%01.2f");
$totalen['bedragHerrekend']=array("list_format"=>"%01.2f");
$totalen['bedragUitbetaald']=array("list_format"=>"%01.2f");

while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	$data['disableEdit']=$hideEdit;
	$data['bedragHerrekend']['value']=round($data['bedragBerekend']['value']*$factor,2);
	echo $list->buildRow($data);
	$totalen['bedragBerekend']['value']+=$data['bedragBerekend']['value'];
	$totalen['bedragHerrekend']['value']+=$data['bedragHerrekend']['value'];
	$totalen['bedragUitbetaald']['value']+=$data['bedragUitbetaald']['value'];
}
echo $list->buildRow($totalen);
}



?>
</table>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>