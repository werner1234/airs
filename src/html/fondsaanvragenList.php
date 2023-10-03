<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 10 januari 2015
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2017/05/06 17:22:56 $
    File Versie         : $Revision: 1.15 $
 		
    $Log: fondsaanvragenList.php,v $
    Revision 1.15  2017/05/06 17:22:56  rvv
    *** empty log message ***

*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = vt("overzicht");

$editScript = "fondsaanvragenEdit.php";
$allow_add  = false;

if($_GET['export']==1 && $_GET['id'] > 0)
{
  $object = new FondsAanvragen();
  $object->getById($_GET['id']);
  $object->exportToPdf();
 
}
elseif($_GET['verwerk']==1 && $_GET['id'] > 0)
{
   echo verwerkFondsAanvraag($_GET['id']);
}


if(!isset($_POST['filter_0_veldnaam']) && $_GET['filterNew']==1)
{
   $_POST['filter_0_veldnaam'] ='fondsAanvragen.verwerkt';
   $_POST['filter_0_methode'] ='gelijk';
   $_POST['filter_0_waarde'] = '0';
}
$list = new MysqlList2();
$list->idField = "id";
$list->idTable ='fondsAanvragen';
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$extraVelden=array('OptieSymbool');//,'OptieType','OptieExpDatum','OptieUitoefenPrijs','Fondseenheid','Beurs','standaardSector','OptieBovenliggendFonds','identifierVWD');


$list->addColumn("","verwerken",array("list_width"=>"60","description"=>" ",'list_nobreak'=>true,'list_order'=>false));
$list->addFixedField("FondsAanvragen","ISINCode",array("list_width"=>"200","search"=>true));
$list->addFixedField("FondsAanvragen","Valuta",array("list_width"=>"100","search"=>true));
$list->addFixedField("FondsAanvragen","Beleggingscategorie",array("list_width"=>"200","search"=>true));
$list->addFixedField("FondsAanvragen","Fonds",array("list_width"=>"200","search"=>false,"list_invisible"=>false));
$list->addFixedField("FondsAanvragen","Omschrijving",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsAanvragen","verwerkt",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondsAanvragen","Vermogensbeheerder",array("list_width"=>"200","search"=>true));
$list->addFixedField("FondsAanvragen","Koers",array("list_width"=>"75","search"=>false));
$list->addFixedField("FondsAanvragen","koersdatum",array("list_width"=>"75","search"=>false));
$list->addFixedField("FondsAanvragen","overigeInfo",array("list_width"=>"200","search"=>false));
foreach($extraVelden as $veld)
  $list->addFixedField("FondsAanvragen",$veld,array("list_width"=>"200","search"=>false,"list_invisible"=>true));
$list->addFixedField("FondsAanvragen","add_user",array("list_width"=>"120","search"=>true));
$list->addFixedField("FondsAanvragen","fondsnaam",array("list_width"=>"200","search"=>false,"list_invisible"=>true));
$list->addFixedField("FondsAanvragen","add_user",array("list_width"=>"120","search"=>true));
$list->addFixedField("FondsAanvragen","add_date",array("list_width"=>"140",'form_type'=>'text',"search"=>true));

$extraVelden[]='Fonds';
$extraVelden=array('fondsnaam');

$html = $list->getCustomFields('FondsAanvragen');

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
";
echo template($__appvar["templateContentHeader"],$content);

?>
<?=$list->filterHeader();?>

<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$DB=new DB();
while($data = $list->getRow())
{
  if($data['fondsAanvragen.OptieSymbool']['value'] <> '' && $data['fondsAanvragen.Fonds']['value'] <> '')
    $query="SELECT Fonds FROM Fondsen WHERE Fonds='".$data['fondsAanvragen.Fonds']['value']."'";
  else
    $query="SELECT Fonds FROM Fondsen WHERE ISINCode='".$data['fondsAanvragen.ISINCode']['value']."' AND Valuta='".$data['fondsAanvragen.Valuta']['value']."'";
  $aantal=$DB->QRecords($query);
 
  if($data["fondsAanvragen.verwerkt"]["value"]!=0)
    $data["tr_class"] = '';   
  elseif($aantal==0)
    $data["tr_class"] = "list_dataregel_rood";
  elseif($aantal==1)
    $data["tr_class"] = "list_dataregel_groen";
  else
    $data["tr_class"] = "list_dataregel_geel";

  if($data['fondsAanvragen.OptieSymbool']['value'] <> '' && $data['fondsAanvragen.Fonds']['value'] <> '')
    $aantal=1;
 //
  $data[".verwerken"]["value"]='';  
  if($aantal==1&&$data["fondsAanvragen.verwerkt"]["value"]==0)
    $data[".verwerken"]["value"] = "<a href=fondsaanvragenList.php?verwerk=1&id=".$data['id']['value'].">".drawButton("record_next","","verwerk")."</a>";
  else
  {
    $data[".verwerken"]["value"] = "<a href=fondsEdit.php?action=edit&ISINCode=" .urlencode(trim($data['fondsAanvragen.ISINCode']['value'])) . "&FondsImportCode=" .urlencode(trim($data['fondsAanvragen.ISINCode']['value'])) . "&Valuta=" . $data['fondsAanvragen.Valuta']['value']."&Omschrijving=" .urlencode(trim($data['fondsAanvragen.fondsnaam']['value']));
    foreach($extraVelden as $veld)
    {
      if(isset($data['fondsAanvragen.'.$veld]['value']) && $data['fondsAanvragen.'.$veld]['value']<>'')
        $data[".verwerken"]["value"] .= "&$veld=".urlencode(trim($data['fondsAanvragen.' . $veld]['value']));
    }
    $data[".verwerken"]["value"] .= ">".drawButton("save", "", "AddFonds") . "</a>";
  }
 
  $data[".verwerken"]["value"] .= "<a href=fondsaanvragenList.php?export=1&id=".$data['id']['value'].">".drawButton("afdrukken","",vt("afdrukken"))."</a>";

    
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
