<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 2 november 2012
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2015/12/05 13:39:16 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: CRM_uur_registratieRelatieList.php,v $
    Revision 1.3  2015/12/05 13:39:16  rvv
    *** empty log message ***

    Revision 1.2  2014/12/24 09:54:51  cvs
    call 3105

    Revision 1.1  2012/11/02 15:53:36  cvs
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();
$__appvar['rowsPerPage']=1000;

$subHeader     = "";
$mainHeader    = vt("klant uren overzicht");

$editScript = "CRM_uur_registratieEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("CRM_uur_registratie","id",array("list_width"=>"100","search"=>false));
$list->addColumn("CRM_uur_registratie","datum",array("list_width"=>"100","search"=>false,"list_align"=>"right"));
$list->addColumn("CRM_uur_registratie","tijd",array("list_width"=>"40","search"=>false,"list_align"=>"right","description"=>"tijd"));
$list->addColumn("","actitviteit",array("sql_alias"=>"concat(CRM_uur_activiteiten.code,': ',CRM_uur_activiteiten.omschrijving) ","list_width"=>"300","list_search"=>true,"search"=>true));
$list->addColumn("CRM_uur_registratie","wn_code",array("list_width"=>"30","search"=>false,"description"=>"WN","list_align"=>"center"));
$list->addColumn("CRM_uur_registratie","verwerkt",array("list_width"=>"20","search"=>false,"description"=>"VW","list_align"=>"center"));
$list->addColumn("CRM_uur_registratie","memo",array("list_width"=>"350","search"=>true));

//$list->addColumn("CRM_uur_registratie","act_id",array("list_width"=>"100","search"=>false));

$list->addColumn("CRM_uur_registratie","deb_id",array("list_width"=>"100","list_invisible"=>true));
$list->addColumn("","debiteur",array("sql_alias"=>"concat(CRM_naw.debiteurnr,': ',CRM_naw.naam) ","list_width"=>"250","list_invisible"=>true,"search"=>true));

$list->setJoin("LEFT JOIN CRM_naw ON  CRM_uur_registratie.deb_id = CRM_naw.id
                LEFT JOIN CRM_uur_activiteiten ON  CRM_uur_registratie.act_id = CRM_uur_activiteiten.id");

$list->setWhere("verwerkt = 0");
// set default sort
$_GET['sort'][]      = "CRM_naw.debiteurnr";
$_GET['direction'][] = "ASC";
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
	// $list->buildRow($data,$template="",$options="");
  
  if ($prevRelatie <> $data["debiteur"]["value"])
  {
    $prevRelatie = $data["debiteur"]["value"];
    $blankRowTemplate = "
    <tr style='background-color:Silver; height: 30px' >
      <td colspan='99'>
       <a href='CRM_uur_verzamel.php?deb_id={$data[deb_id][value]}'>&nbsp;<img src='images/16/bullist.gif' width='16' height='16' border='0' align='absmiddle'> verzamel uren</a>&nbsp;&nbsp;|&nbsp;&nbsp;
       <b>{$prevRelatie}</b>
      </td>
    </tr>
    ";
    echo $list->buildRow(array(),$blankRowTemplate);
  }
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