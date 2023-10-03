<?php
/*
    AE-ICT CODEX source module versie 1.6, 28 maart 2009
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/12/28 18:45:14 $
    File Versie         : $Revision: 1.5 $

    $Log: tijdelijkeorderregelsList.php,v $
    Revision 1.5  2011/12/28 18:45:14  rvv
    *** empty log message ***

    Revision 1.4  2011/08/31 14:37:40  rvv
    *** empty log message ***

    Revision 1.3  2009/10/25 08:37:21  rvv
    *** empty log message ***

    Revision 1.2  2009/04/05 09:23:36  rvv
    *** empty log message ***

    Revision 1.1  2009/03/29 14:39:51  rvv
    *** empty log message ***


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/orderRegelsAanmaken.php");
session_start();
$__appvar['rowsPerPage']=1000;

if($_POST['verwerk'] > 0)
{
  $db=new DB();
  foreach ($_POST as $key=>$value)
  {
    if(substr($key,0,3)=='id_')
    {
      $ids[]=substr($key,3);
    }
  }
  foreach ($_POST as $key=>$value)
  {
    if(substr($key,0,6)=='fonds_')
    {
      $fonds=substr($key,6);
      $fondsen[]=base64_decode($fonds);//str_replace('_',' ',$fonds);
    }
  }

  $verwerk = new orderRegelsAanmaken();
  if($_POST['verwerk']=='1')
  {
    foreach ($ids as $id)
    {
      $verwerk->verzamel($id);
    }
    foreach ($fondsen as $fonds)
    {
      $verwerk->verzamelFonds($fonds);
    }
    if(count($ids)>0 || count($fondsen)>0)
    {
      $verwerk->makeOrders();
      $regelInfo = $verwerk->counter." regels verwerkt. <br>\n";
    }
  }
  else
  {
    foreach ($ids as $id)
    {
      $verwerk->verwijderId($id);
      $regelInfo = $verwerk->counter." regels verwijderd. <br>\n";
    }
    foreach ($fondsen as $fonds)
    {
      $verwerk->verwijderFonds($fonds);
    }
  }

}




$subHeader     = "";
$mainHeader    = " Verwerk geselecteerde fondsregels tot orders.";

$editScript = "tijdelijkeorderregelsEdit.php";
$allow_add  = false;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("TijdelijkeOrderRegels","id",array("list_width"=>"100","search"=>false));

if($_GET['fonds'])
{
  $type='fonds';
  $extraWhere = " AND fonds = '".$_GET['fonds']."'";
}
else
{
  $type='all';
}


if($type=='all')
{
  $list->setGroupBy("fonds");
  $list->addColumn("","dt",array("description"=>'Detail',"list_width"=>"30","search"=>false));
  $list->addColumn("","vw",array("list_width"=>"30","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","fonds",array("list_width"=>"200","search"=>false));
  $list->addColumn("","kopen",array('sql_alias'=>'SUM(TijdelijkeOrderRegels.kopen)',"list_width"=>"30","search"=>false));
  $list->addColumn("","verkopen",array('sql_alias'=>'round(SUM(TijdelijkeOrderRegels.verkopen))',"list_width"=>"30","search"=>false));
}
else
{
  $list->addColumn("","vw",array("list_width"=>"30","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","fonds",array("list_width"=>"200","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","portefeuille",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","modelPercentage",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","portefeuillePercentage",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","afwijking",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","valuta",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","kopen",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","verkopen",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","overschrijding",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","modelWaarde",array("list_width"=>"100","search"=>false));
  $list->addColumn("TijdelijkeOrderRegels","koers",array("list_width"=>"100","search"=>false));
}



$list->setWhere("add_user='$USR' $extraWhere");
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);


// select page
$list->selectPage($_GET['page']);



$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

	$_SESSION[submenu] = New Submenu();
	$_SESSION[submenu]->addItem("Naar fondsniveau","tijdelijkeorderregelsList.php");


$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";


$content[javascript] .= "
function addRecord()
{
	parent.frames['content'].location = '".$editScript."?action=new';
}

function uncheckAll()
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox')
   {
    theForm[z].checked = false;
   }
  }
}
function checkAll()
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox')
   {
    theForm[z].checked = true;
   }
  }
}
";
echo template($__appvar["templateContentHeader"],$content);

if($type=='all')
{
  $disableEdit = true;
}
?>


<form method="POST" name="editForm">
<input type="hidden" name="verwerk" value="1">
<table class="list_tabel" cellspacing="0">

<?=$list->printHeader($disableEdit);?>
<?php
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
	if($type=='all')
	{
	 $data['vw']['value']="<input type=\"checkbox\" name=\"fonds_".base64_encode($data['fonds']['value'])."\" value=\"1\">";
	  $data['dt']['value']='<div class="icon" valign=center ><a href="tijdelijkeorderregelsList.php?fonds='.$data['fonds']['value'].'"><img src="images/16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a></div>';

	 $data['disableEdit']=$disableEdit;
	}
	else
	{
	  $data['vw']['value']="<input type=\"checkbox\" name=\"id_".$data['id']['value']."\" value=\"1\">";
	}

	 if($data['kopen']['value'] > 0)
			$data['kopen']['value']=round($data['kopen']['value']);
	 else
	   $data['kopen']['value']='';

		if($data['verkopen']['value'] > 0)
		{
		  if(intval($data['verkopen']['value']) == $data['verkopen']['value'] )
		    $data['verkopen']['value'] = intval($data['verkopen']['value']);
		}
		else
  	  $data['verkopen']['value']='';

	echo $list->buildRow($data);
}
?>
</table>
<br><br>
<div class="formlinks"> <input type="button" value="Verwerk selectie."   onclick="javascript:document.editForm.verwerk.value=1;document.editForm.submit();"> </div>
<div class="formlinks"> <input type="button" value="Verwijder selectie." onclick="javascript:document.editForm.verwerk.value=2;document.editForm.submit();" > </div>
<div> <a href="javascript:checkAll();"> check</a>  / <a href="javascript:uncheckAll();"> uncheck</a>  </div>
</form>
<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>