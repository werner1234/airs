<?php

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "fondskoersaanvragenEdit.php";
$allow_add  = false;

if($_GET['export']==1 && $_GET['id'] > 0)
{
  $object = new FondskoersAanvragen();
  $object->getById($_GET['id']);
  $object->exportToPdf();
 
}
elseif($_GET['verwerk']==1 && $_GET['id'] > 0)
{
  echo verwerkFondskoersAanvraag($_GET['id']);
  unset($_SERVER['QUERY_STRING']);
}


if(!isset($_POST['filter_0_veldnaam']) && $_GET['filterNew']==1)
{
  $_POST['filter_0_veldnaam'] = 'fondskoersAanvragen.verwerkt';
  $_POST['filter_0_methode'] = 'gelijk';
  $_POST['filter_0_waarde'] = '0';
  
  $_POST['sort_0_veldnaam'] = 'koersAanvragen.add_date';
  $_POST['sort_0_veldnaam'] = 'DESC';
}
$list = new MysqlList2();
$list->idField = "id";
$list->idTable ='fondskoersAanvragen';
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$extraVelden=array('OptieSymbool');//,'OptieType','OptieExpDatum','OptieUitoefenPrijs','Fondseenheid','Beurs','standaardSector','OptieBovenliggendFonds','identifierVWD');


$list->addColumn("","verwerken",array("list_width"=>"300","description"=>" ",'list_nobreak'=>true,'list_order'=>false));
$list->addFixedField("FondskoersAanvragen","Fonds",array("list_width"=>"200","search"=>false,"list_invisible"=>false));
$list->addFixedField("FondskoersAanvragen","verwerkt",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondskoersAanvragen","Koers",array("list_width"=>"120","search"=>false));
$list->addFixedField("FondskoersAanvragen","Datum",array("list_width"=>"100","search"=>false));
$list->addFixedField("FondskoersAanvragen","add_date",array("list_width"=>"100","search"=>false));


$html = $list->getCustomFields('FondskoersAanvragen');

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

$cfg=new AE_config();
$lockDatum=$cfg->getData('fondskoersLockDatum');
$lockJul=db2jul($lockDatum);

$DB=new DB();
while($data = $list->getRow())
{
  $data[".verwerken"]["value"]='';
  if($data['fondskoersAanvragen.Fonds']['value'] <> '')
  {
    $query = "SELECT Fonds FROM Fondsen WHERE Fonds='" . mysql_real_escape_string($data['fondskoersAanvragen.Fonds']['value']) . "'";
    $aantal = $DB->QRecords($query);
    $koersAantal=0;
    $koersAanwezig='';
    if($aantal==1)
    {
      $query = "SELECT Fonds FROM Fondskoersen WHERE Fonds='" . mysql_real_escape_string($data['fondskoersAanvragen.Fonds']['value']) . "' AND Datum='" . $data['fondskoersAanvragen.Datum']['value'] . "'";
      $koersAantal = $DB->QRecords($query);
      if($koersAantal>0)
        $data[".verwerken"]["value"] = "Koers is al aanwezig.";
  
      $query = "SELECT Datum,Koers FROM Fondskoersen WHERE Fonds='" . mysql_real_escape_string($data['fondskoersAanvragen.Fonds']['value']) . "' AND Datum < '" . $data['fondskoersAanvragen.Datum']['value'] . "' ORDER BY Datum desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      $laatste=$DB->lookupRecord();
      $data[".verwerken"]["value"].="<br>\nLaatste koers ".substr($laatste['Datum'],0,10)." (".$laatste['Koers'].") verandering ". round(($data['fondskoersAanvragen.Koers']['value']-$laatste['Koers'])/$laatste['Koers']*100,1) ."%";
    }
  }
  else
    $aantal=0;
  

  if($aantal==1&&$data["fondskoersAanvragen.verwerkt"]["value"]==0)
  {
   /*
    $query="SELECT Rekeningmutaties.Fonds, MAX(Rekeningmutaties.Boekdatum), Portefeuilles.Vermogensbeheerder
FROM Rekeningmutaties INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
WHERE Rekeningmutaties.Fonds='" . mysql_real_escape_string($data['fondskoersAanvragen.Fonds']['value']) . "' AND Rekeningmutaties.Boekdatum>='" . date('Y') ."-01-01' GROUP BY Portefeuilles.Vermogensbeheerder";
  */
    $query="SELECT Vermogensbeheerder,max(jaar) as laatsteJaar FROM
(
SELECT
Rekeningmutaties.Fonds,
Portefeuilles.Vermogensbeheerder,
year(Rekeningmutaties.Boekdatum) as jaar
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
WHERE Rekeningmutaties.Fonds='" . mysql_real_escape_string($data['fondskoersAanvragen.Fonds']['value']) . "'
GROUP BY Portefeuilles.Vermogensbeheerder,jaar
) as historie
GROUP BY Vermogensbeheerder order by laatsteJaar desc, Vermogensbeheerder ";
    
    $DB->SQL($query);
    $DB->Query();
    $aantalVerm=$DB->records();
    if($DB->records()>0)
    {
      $aanwezigBij="<br> Aanwezig bij:";
      while ($aanwezig = $DB->nextRecord())
      {
        $aanwezigBij.=" ".$aanwezig['Vermogensbeheerder']."(".$aanwezig['laatsteJaar'].")";
      }
      $data[".verwerken"]["value"] .= $aanwezigBij;
    }
  }
  $lock=false;
  if( db2jul($data['fondskoersAanvragen.Datum']['value']) < $lockJul)
  {
    $data[".verwerken"]["value"] .= "<br> Datum < vastzetdatum '$lockDatum'";
    $lock=true;
  }
  
  if($data["fondskoersAanvragen.verwerkt"]["value"]!=0)
    $data["tr_class"] = '';
  elseif($aantal==0 || $koersAantal<>0)
    $data["tr_class"] = "list_dataregel_rood";
  elseif($aantal==1 && $aantalVerm==1 && $lock==false)
    $data["tr_class"] = "list_dataregel_groen";
  else
    $data["tr_class"] = "list_dataregel_geel";
  
  if($data["tr_class"] == "list_dataregel_geel" || $data["tr_class"] == "list_dataregel_groen")
    $data[".verwerken"]["value"] = "<a href=fondskoersaanvragenList.php?verwerk=1&id=" . $data['id']['value'] . ">" . drawButton("record_next", "", "verwerk") . " verwerken</a> ". $data[".verwerken"]["value"] ;
  
 // $data[".verwerken"]["value"] .= "<a href=fondsaanvragenList.php?export=1&id=".$data['id']['value'].">".drawButton("afdrukken","","afdrukken")."</a>";

    
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