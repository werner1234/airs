<?php
/*
    AE-ICT CODEX source module versie 1.6, 20 augustus 2011
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2019/09/21 16:53:54 $
    File Versie         : $Revision: 1.4 $

    $Log: CRM_trackandtraceList.php,v $
    Revision 1.4  2019/09/21 16:53:54  rvv
    *** empty log message ***

    Revision 1.3  2019/09/21 16:30:12  rvv
    *** empty log message ***

    Revision 1.2  2018/02/01 13:07:50  cvs
    update naar airsV2

    Revision 1.1  2017/02/18 17:29:20  rvv
    *** empty log message ***

    Revision 1.3  2012/01/28 16:13:06  rvv
    *** empty log message ***

    Revision 1.2  2011/12/11 10:57:35  rvv
    *** empty log message ***

    Revision 1.1  2011/08/31 14:37:40  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "trackandtraceEdit.php";
$allow_add  = false;

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("TrackAndTrace","veld",array("list_width"=>"150","search"=>false));
$list->addFixedField("TrackAndTrace","oudeWaarde",array("list_width"=>"150","search"=>false));
$list->addFixedField("TrackAndTrace","nieuweWaarde",array("list_width"=>"150","search"=>false));
$list->addFixedField("TrackAndTrace","add_date",array("list_width"=>"150","search"=>false));
$list->addFixedField("TrackAndTrace","add_user",array("list_width"=>"100","search"=>false));

$html = $list->getCustomFields('TrackAndTrace','CRM_TrackAndTrace');

$_SESSION['submenu'] = New Submenu();

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort

if(!is_array($list->sortOptions) || count($list->sortOptions)<1)
{
  $list->sortOptions = array(array('veldnaam' => 'trackAndTrace.id', 'methode' => 'DESC'));
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

$list->setWhere(" trackAndTrace.tabel = 'CRM_naw' AND trackAndTrace.recordId='".$_GET['rel_id']."'");

// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));

$content['pageHeader'] = "<br><div class='edit_actionTxt'>
 
  <button  onclick=\"location.href='CRM_naw_historie.php?rel_id=".$_GET['rel_id']."'\"  > Historie per datum</button><br>
   $subHeader
</div><br>
";

$content['javascript'] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>

<br>
<form name="editForm" method="POST">
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader(true);?>
<?php
while($data = $list->getRow())
{

	$data['disableEdit']=true;
	$data['noClick']=true;
	echo $list->buildRow($data);


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