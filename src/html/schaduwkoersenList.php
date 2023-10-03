<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 14 februari 2009
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/04/04 14:44:13 $
    File Versie         : $Revision: 1.3 $
 		
    $Log: schaduwkoersenList.php,v $
    Revision 1.3  2018/04/04 14:44:13  cvs
    call 6749

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = vt("schaduw koersen");
$mainHeader    = vt("overzicht");

$editScript = "schaduwkoersenEdit.php";
$allow_add  = true;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addColumn("Schaduwkoersen","id",array("list_width"=>"100","search"=>false));
$list->addColumn("Schaduwkoersen","Fonds",array("list_width"=>"100","search"=>false));
$list->addColumn("Schaduwkoersen","Datum",array("list_width"=>"100","search"=>false));
$list->addColumn("Schaduwkoersen","Koers",array("list_width"=>"100","search"=>false));
//$list->addColumn("Schaduwkoersen","add_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("Schaduwkoersen","add_user",array("list_width"=>"100","search"=>false));
//$list->addColumn("Schaduwkoersen","change_date",array("list_width"=>"100","search"=>false));
//$list->addColumn("Schaduwkoersen","change_user",array("list_width"=>"100","search"=>false));


if(!empty($Fonds))
{
	$list->setWhere(" Fonds = '".$Fonds."' ");
}
else 
{
	$list->setWhere(" Datum = '".getLaatsteValutadatum()."' ");
}

if($_GET["actief"] == "inactief" )
{
	$inactiefChecked = "checked";
	$actief = "inactief";
	$alleenActief = " ";
}
else 
{
	$actiefChecked = "checked";
	$actief = "actief";
	$alleenActief = " AND (Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00') ";
}

$DB = new DB();
$DB->SQL("SELECT Fonds FROM Fondsen WHERE 1=1 ".$alleenActief." ORDER BY Fonds ASC");
$DB->Query();
while($data = $DB->NextRecord())
{
	$options .= "<option value=\"".$data['Fonds']."\" ".($Fonds==$data['Fonds']?"selected":"").">".$data['Fonds']."</option>\n";
}

if(empty($_GET['sort']))
{
	$_GET['sort'] = array("Datum");
	$_GET['direction'] = array("DESC");
}
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION["NAV"] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));


$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem(vt("Importeer .CSV"),"schaduwKoersImport.php");

session_write_close();

$content["pageHeader"] = "<br><div class='edit_actionTxt'>

</div>";


$ajx = new AE_cls_ajaxLookup("fonds");
$ajx->changeModuleTriggerID("fondsSelect","Fonds");

$content["style2"] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';

$content["javascript"] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new&Fonds=".$Fonds."';
}
";
echo template($__appvar["templateContentHeader"],$content);


?>
  <style>
    .ui-autocomplete {
      max-height: 100px;
      overflow-y: auto;
      /* prevent horizontal scrollbar */
      overflow-x: hidden;
      background: #FFF;
    }

  </style>
<br>
<form action="schaduwkoersenList.php" method="GET"  name="controleForm">
<?=vt("Fonds")?> : <input name="Fonds" id="fondsSelect" value="<?=$Fonds?>"/> &nbsp;&nbsp;&nbsp;&nbsp;
  <select name="Fonds" id="typeFonds">
    <option value="actief"><?=vt("Actieve fondsen")?></option>
    <option value="alle"><?=vt("Alle fondsen")?></option>
  </select>




<br/>
<br/>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
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


?>

<script>
  $(document).ready(function(){

    document.cookie = 'fondsSel=aktief';
    $("#typeFonds").change(function(){
      document.cookie = 'fondsSel='+$(this).val();
    });

    $("#fondsSelect").autocomplete(
      {
        source: "lookups/getFondsForSchaduw.php",           // link naar lookup script

        change: function(e, ui)
        {
          if (!ui.item)
          {

            $(this).val("");                                  // reset waarde als niet uit de lookup
          }
        },
        select: function(event, ui)                           // bij selectie clientside vars updaten
        {
          $(this).val(ui.item.Fonds);
          window.open("?Fonds="+ui.item.Fonds,"content");

        },
        open: function()
        {
          $(".ui-autocomplete").css("width", "500px");
        },
        minLength: 2,                                         // pas na de tweede letter starten met zoeken
        delay: 0,
        autoFocus: true
      });
  });
  </script>


<?
echo template($__appvar["templateRefreshFooter"],$content);
