<?php
/* 	
    AE-ICT CODEX source module versie 1.7, 11 maart 2020
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2020/04/29 15:56:45 $
    File Versie         : $Revision: 1.2 $
 		
    $Log: modelportefeuillespermodelportefeuilleList.php,v $
    Revision 1.2  2020/04/29 15:56:45  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("modelportefeuilles per modelportefeuille");
$mainHeader    = vt("overzicht");

$editScript = "modelportefeuillespermodelportefeuilleEdit.php";
$allow_add  = true;


$db=new DB();
$query="SELECT
  modelPortefeuillesPerModelPortefeuille.modelportefeuille,
  SUM(modelPortefeuillesPerModelPortefeuille.percentage) as totaal,
  modelPortefeuillesPerModelPortefeuille.vanaf
FROM 
  modelPortefeuillesPerModelPortefeuille
GROUP BY 
  modelPortefeuillesPerModelPortefeuille.modelportefeuille,
  modelPortefeuillesPerModelPortefeuille.vanaf
HAVING 
  totaal <> 100
ORDER BY 
  modelPortefeuillesPerModelPortefeuille.modelportefeuille,
  modelPortefeuillesPerModelPortefeuille.vanaf desc ";

$db->SQL($query);
$db->query();
if($db->records() > 0)
{
  $table = '<br />
  <table class="table table-compact" style="width:300px;">
    <thead>
      <tr>
        <td><strong>'.vt("Modelportefeuille").'</strong></td>
        <td><strong>'.vt("Vanaf").'</strong></td>
        <td><strong>'.vt("Totaal percentage").'</strong></td>
      </tr>
    </thead>  ';
  while ($data = $db->NextRecord())
  {
    if ( $data['totaal'] != 100 ) {
      $table.="<tr><td>".$data['modelportefeuille']."</td><td>".date('d-m-Y', strtotime($data['vanaf']))."</td><td align='right' class='tableTotal'>".number_format($data['totaal'], 2)."</td></tr>";
    }
  }
  $table.="</table><br />";
}



$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

//$list->addFixedField("ModelPortefeuillesPerModelPortefeuille","id",array("list_width"=>"100","search"=>false));
//$list->addColumn("ModelPortefeuillesPerModelPortefeuille","vermogensbeheerder",array("list_width"=>"100","search"=>false));
$list->addFixedField("ModelPortefeuillesPerModelPortefeuille","modelPortefeuille",array("list_width"=>"100","search"=>false));
$list->addFixedField("ModelPortefeuillesPerModelPortefeuille","modelPortefeuilleComponent",array("list_width"=>"100","search"=>false));
$list->addFixedField("ModelPortefeuillesPerModelPortefeuille","percentage",array("list_width"=>"100","search"=>false));
$list->addFixedField("ModelPortefeuillesPerModelPortefeuille","vanaf",array("list_width"=>"100","search"=>false));
//$list->addColumn("ModelPortefeuillesPerModelPortefeuille","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("ModelPortefeuillesPerModelPortefeuille","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("ModelPortefeuillesPerModelPortefeuille","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("ModelPortefeuillesPerModelPortefeuille","change_user",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields(array('ModelPortefeuillesPerModelPortefeuille'),'ModelPortefeuillesPerModelPortefeuille');
$list->ownTables=array('modelPortefeuillesPerModelPortefeuille');

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
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
	parent.frames['content'].location = '".$editScript."?action=new';
}

function editRecord(url) 
{
	location = url;
}
";
echo template($__appvar["templateContentHeader"],$content);

echo $table;

?>
  <br>
<?=$list->filterHeader();?>
  <table class="list_tabel" cellspacing="0">
    <?=$list->printHeader();?>
    <?php
    $list->customEdit =true;
    while($data = $list->getRow())
    {
      $data['extraqs']='frame='.$_GET['frame'];
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
