<?php
/* 	
    AE-ICT CODEX source module versie 1.2, 21 november 2005
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2011/08/31 14:37:39 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: CRM_faqList.php,v $
    Revision 1.4  2011/08/31 14:37:39  rvv
    *** empty log message ***

    Revision 1.3  2006/02/15 11:19:44  cvs
    klantspecifiek menu

    Revision 1.2  2006/01/25 11:50:17  cvs
    *** empty log message ***

    Revision 1.1  2006/01/05 16:06:05  cvs
    eerste CRM test

    Revision 1.2  2005/12/14 12:35:13  cvs
    *** empty log message ***

    Revision 1.4  2005/11/28 07:31:48  cvs
    *** empty log message ***

    Revision 1.3  2005/11/22 14:53:48  cvs
    *** empty log message ***

    Revision 1.2  2005/11/22 14:31:07  cvs
    *** empty log message ***

    Revision 1.1  2005/11/21 16:35:06  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = "Kennisbank overzicht";

$editScript = "CRM_faqEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Faq","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","pdf",array("list_width"=>"30"));
$list->addColumn("Faq_ow","onderwerp",array("list_width"=>"150","search"=>false));
$list->addColumn("Faq","kop",array("list_width"=>"","search"=>true));
$list->addColumn("Faq","change_date",array("list_width"=>"100","search"=>false,"description"=>"datum aangepast"));
//$list->setJoin(" join faq_ow ON faq.sectie = faq_ow.id ");

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
$list->setWhere(" CRM_faq.sectie = CRM_faq_ow.id ");
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring

$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION[NAV] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION[NAV]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION[NAV]->addItem(new NavSearch($_GET['selectie']));

$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div><br><br>";

$content[javascript] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  $data[pdf][value] = "<a href=\"CRM_faqMakePDF.php?id=".$data[id][value]."\" target=\"pdfOutput\"><img src=\"images/pdf_20x20.gif\" border=\"0\"></a>";
  
	// $list->buildRow($data,$template="",$options="");
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