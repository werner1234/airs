<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/08/31 14:37:40 $
 		File Versie					: $Revision: 1.5 $

 		$Log: querydataList.php,v $
 		Revision 1.5  2011/08/31 14:37:40  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2006/08/18 08:34:49  cvs
 		*** empty log message ***
 		

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AE_cls_listCSV.php");
session_start();

$editScript = "querydataEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("Querydata","id",array("width"=>"100","search"=>false));
$list->addField("Querydata","Naam",array("width"=>"200" ,"search"=>false));
$list->addField("Querydata","Omschrijving",array("width"=>"500","search"=>false));

//ueryWizard.php
if($_GET['type'] == "queryWizard")
{
	$qPage = "queryWizard.php";
}
else
{
	$qPage = "reportBuilder.php";
}

$list->setWhere(" Type = '".$_GET['type']."' ");
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php

$template = '
  <tr class="list_dataregel" onmouseover=this.className="list_dataregel_hover" onmouseout=this.className="list_dataregel"
           title="Klik op de knop links om de details te zien/muteren">
	   <td class="list_button">
	     <div class="icon">
	       <a href="querydataEdit.php?action=edit&id={id_value}">
	         <img src="images/16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;
	       </a>
	     </div>
	  </td>
	  <td class="listTableData"   align="left" >
	    <a href="'.$qPage.'?do=queryOpenen&id={id_value}">{Naam_value}</a>&nbsp;
	  </td>
	  <td class="listTableData"   align="left" >
	    {Omschrijving_value} &nbsp;
	  </td>
	</tr>';

while($data = $list->getRow())
{
	echo $list->buildRow($data,$template);
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