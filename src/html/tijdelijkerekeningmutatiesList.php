<?php
/*
 		Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2020/07/06 11:58:46 $
 		File Versie					: $Revision: 1.66 $

 		$Log: tijdelijkerekeningmutatiesList.php,v $
 		Revision 1.66  2020/07/06 11:58:46  cvs
 		extra () prio 2
 		

*/

//aevertaal:  nog niet vertaald eerst overleg met Frank

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

if (!isset($_SESSION["TRM_hideState"]))
{
  $_SESSION["TRM_hideState"] = "unhide";
}

$adventverwerking = getVermogensbeheerderField("adventVerwerking");

$millogicverwerking = getVermogensbeheerderField("millogicVerwerking") ;

$editScript = "tijdelijkerekeningmutatiesEdit.php";

$colorArray    = array("DIV","COUP","OPHEFFING","STORTING","KOOP VREEMDE VALUTA","OPNAME","PURCHASE","SELL","SALE","EMISSIE","HERBELEG","CONVERSIE","DEPOSIT ","WITHDRAW");
$colorAbnArray = array("DIV","COUP","OPHEFFING","STORTING","KOOP VREEMDE VALUTA","OPNAME","PURCHASE","SELL","SALE","EMISSIE","HERBELEG","CONVERSIE","DEPOSIT ","WITHDRAW");


$list = new MysqlList2();
$list->idField = "id";
$list->idTable='TijdelijkeRekeningmutaties';
$list->editScript = $editScript;
//$list->perPage = $__appvar['rowsPerPage'];
$list->perPage = 1000;

$_SESSION["TRMListUrl"] = str_replace("&prioFilter=off","",$_SERVER["REQUEST_URI"]);


if ($_POST["hideToggle"] == "1")
{
  $_SESSION["TRM_hideState"] = $_POST["hideState"];
}

$hideWhere = "";
if ($_SESSION["TRM_hideState"] == "hide")
{
  $hideWhere = "
  AND
    Grootboekrekening NOT IN ('FONDS', 'DIV', 'DIVBE', 'ROER', 'KOST', 'KOBU', 'BEH', 'BEW', 'RENOB', 'RENME' )
   ";
}


//debug($_SESSION["TRMListUrl"], $_SESSION["prioFilterUsed"]);

$allow_add = false;


$prioFilter = "";

if ($_GET["prioFilter"] == "off")
{
  unset($_SESSION["prioFilterUsed"]);
}

if ($_POST["prioFilterNew"] != "" )
{
  $prioFilter = $_POST["prioFilterNew"];
  $_SESSION["prioFilterUsed"] = $prioFilter;
  $prioFilterVB = $_POST["prioFilterVB"];
  $_SESSION["prioFilterUsedVB"] = $prioFilterVB;

}
else
{
  $prioFilter = $_SESSION["prioFilterUsed"];
}


$prioText = "";

if ($prioFilter != "")
{
  switch ($prioFilter)
  {
    case "prio_8":
      $prioText = "Filter op Prio 8";
      $prioStyle = "background:#3E9EFF; color: white";
      $prioWhere = " AND
      ( 
        (        
          MONTH(`TijdelijkeRekeningmutaties`.Boekdatum) != MONTH(`TijdelijkeRekeningmutaties`.add_date) AND 
          DAY(`TijdelijkeRekeningmutaties`.add_date) > 6
        ) OR 
        (
          DAYOFYEAR(`TijdelijkeRekeningmutaties`.add_date) - DAYOFYEAR(`TijdelijkeRekeningmutaties`.Boekdatum) > 31
        ) OR
        (
          YEAR(`TijdelijkeRekeningmutaties`.add_date) !=   YEAR(`TijdelijkeRekeningmutaties`.Boekdatum)
        )
      )    
      ";
      break;
    case "prio_7":
      $prioText = "Filter op Prio 7";
      $prioStyle = "background:Maroon; color:white!important;";
      $prioWhere = " AND
      ( 
        
          (`Fondsen`.EindDatum < NOW() AND `Fondsen`.EindDatum != '0000-00-00' ) OR 
          `TijdelijkeRekeningmutaties`.Fonds = 'optie/future' 
          
      )    
      ";
      break;
    case "prio_6":
      $prioText = "Filter op Prio 6";
      $prioStyle = "background:#80FFFF;";
      $prioWhere = " AND
      ( 
        
         ( `TijdelijkeRekeningmutaties`.Grootboekrekening = 'FONDS' AND 
           `Fondsen`.fondssoort = 'OPT'    AND
            (
               `TijdelijkeRekeningmutaties`.Transactietype <> 'A/O' AND 
               `TijdelijkeRekeningmutaties`.Transactietype <> 'A/S' AND 
               `TijdelijkeRekeningmutaties`.Transactietype <> 'V/O' AND 
               `TijdelijkeRekeningmutaties`.Transactietype <> 'V/S'
            )
         ) OR
         (
           `Portefeuilles`.`overgangsdepot` = 1
         )   
          
      )    
      ";
      break;
    case "prio_5":
      $prioText = "Filter op Prio 5";
      $prioStyle = "background:#80FF80;";
      $prioWhere = " AND
      ( 
        (
          `TijdelijkeRekeningmutaties`.Grootboekrekening = 'FONDS' AND 
          `Fondsen`.fondssoort != 'STOCKDIV'    AND
          (`TijdelijkeRekeningmutaties`.Transactietype = 'D'  OR `TijdelijkeRekeningmutaties`.Transactietype = 'L')  AND
          `TijdelijkeRekeningmutaties`.Fondskoers = '0'    
        ) OR 
        (
          `TijdelijkeRekeningmutaties`.Grootboekrekening = 'FONDS' AND 
          `Fondsen`.fondssoort = 'STOCKDIV'    AND
          (`TijdelijkeRekeningmutaties`.Transactietype = 'D'  OR `TijdelijkeRekeningmutaties`.Transactietype = 'L')  AND
          `TijdelijkeRekeningmutaties`.Fondskoers != '0'
        ) 
      )    
      ";
      break;
    case "prio_4":
      $prioText = "Filter op Prio 4";
      $prioStyle = "background:orange;";
      $prioWhere = " AND
      ( 
        (`TijdelijkeRekeningmutaties`.Aantal = '0' AND `TijdelijkeRekeningmutaties`.Grootboekrekening = 'FONDS') OR 
        (`TijdelijkeRekeningmutaties`.Fonds = '' AND `TijdelijkeRekeningmutaties`.Grootboekrekening = 'VKSTO') OR
        (`TijdelijkeRekeningmutaties`.Fonds = '' AND `TijdelijkeRekeningmutaties`.Grootboekrekening = 'VMAR') 
      )    
      ";
      break;
    case "prio_3":
      $prioText = "Filter op Prio 3";
      $prioStyle = "background:#FFCC66;";

      $prioWhere = " AND
      ( 
        (
          SUBSTR(`TijdelijkeRekeningmutaties`.Grootboekrekening,1,3) = 'DIV' AND  `Fondsen`.fondssoort = 'STOCKDIV'
        )
        OR
        (
          TijdelijkeRekeningmutaties.Grootboekrekening = 'FONDS' AND 
          TijdelijkeRekeningmutaties.Fondskoers != 0             AND 
          (ActieveFondsen.laatsteKoers/TijdelijkeRekeningmutaties.Fondskoers) >= 9.5
        )
      )    
      ";
      break;
    case "prio_2":
      $prioText = "Filter op Prio 2";
      $prioStyle = "background:#FAA39A;";
      $prioWhere = " AND
      (( 
        (`TijdelijkeRekeningmutaties`.Valuta != 'EUR' AND `TijdelijkeRekeningmutaties`.Valutakoers = '1') OR 
        (`TijdelijkeRekeningmutaties`.Valuta = 'EUR' AND `TijdelijkeRekeningmutaties`.Valutakoers != '1') OR 
        (`TijdelijkeRekeningmutaties`.Valutakoers = '0') OR 
        (`TijdelijkeRekeningmutaties`.Valuta = '???') OR 
        (`TijdelijkeRekeningmutaties`.Valuta = '')
      )  
     	OR 
     	(
        RIGHT(`TijdelijkeRekeningmutaties`.Rekening,3) != 'EUR' AND
        RIGHT(`TijdelijkeRekeningmutaties`.Rekening,3) != 'MEM' AND
        RIGHT(`TijdelijkeRekeningmutaties`.Rekening,3) != 'LEN' AND
        RIGHT(`TijdelijkeRekeningmutaties`.Rekening,3) != 'DEP' AND
        `TijdelijkeRekeningmutaties`.Valuta = 'EUR'
	    ))  
      ";
      break;
    case "prio_1":
      $prioText = "Filter op Prio 1";
      $prioStyle = "background:#FF99CC;";
      if ($prioFilterVB == "ABN")
      {
        $prioWhere = " AND 
        (
      (
        `TijdelijkeRekeningmutaties`.Omschrijving LIKE '%KOSTEN %' AND `TijdelijkeRekeningmutaties`.Grootboekrekening = 'ONTTR') OR 
        (
          `TijdelijkeRekeningmutaties`.regelnr < 0 AND
          (
            `TijdelijkeRekeningmutaties`.Omschrijving REGEXP '".implode("|",$colorAbnArray)."' OR
            (
              SUBSTR(`TijdelijkeRekeningmutaties`.Omschrijving,1,2) = '**' AND SUBSTR(`TijdelijkeRekeningmutaties`.Omschrijving,16,2) = '//' 
            )
          )
        )
      )";
      }
      else
      {
        $prioWhere = " AND
        (
          (`TijdelijkeRekeningmutaties`.Omschrijving LIKE '%KOSTEN %' AND `TijdelijkeRekeningmutaties`.Grootboekrekening = 'ONTTR') OR 
          (
            (`TijdelijkeRekeningmutaties`.Omschrijving REGEXP '".implode("|",$colorArray)."'  AND 
              (
                `TijdelijkeRekeningmutaties`.Grootboekrekening = 'ONTTR' OR
                `TijdelijkeRekeningmutaties`.Grootboekrekening = 'STORT'
              ) 
            ) 
            OR
            (
              SUBSTR(`TijdelijkeRekeningmutaties`.Omschrijving,1,2) = '**' AND SUBSTR(`TijdelijkeRekeningmutaties`.Omschrijving,16,2) = '//' 
            )
          )
        ) 
      ";

      }

      break;


  }
}

if ($prioText !=  "")
{
  $prioText = "<a href='{$PHP_SELF}?prioFilter=off' title='filter uitschakelen' ><i class=\"fa fa-times\"></i></i></a> ".$prioText;
}

$list->addColumn("","vink",array("list_wdth"=>20,"description"=>" ", "list_align"=>"center"));
$list->addFixedField("TijdelijkeRekeningmutaties","Rekening",array("list_tdcode"=>"nowrap","search"=>true));
$list->addFixedField("TijdelijkeRekeningmutaties","Omschrijving",array("list_tdcode"=>"nowrap","search"=>true));
$list->addFixedField("TijdelijkeRekeningmutaties","Boekdatum",array("list_tdcode"=>"nowrap","search"=>false, "list_align" => "right"));
$list->addFixedField("TijdelijkeRekeningmutaties","Grootboekrekening",array("list_tdcode"=>"nowrap","description"=>"GB","search"=>true,"list_align" => "center"));
$list->addFixedField("TijdelijkeRekeningmutaties","Valuta",array("list_tdcode"=>"nowrap","search"=>false,"description"=>"Val","list_align" => "center"));
$list->addFixedField("TijdelijkeRekeningmutaties","Valutakoers",array("list_tdcode"=>"nowrap","search"=>false));

$list->addFixedField("TijdelijkeRekeningmutaties","Aantal",array("list_tdcode"=>"nowrap","search"=>false));
$list->addFixedField("TijdelijkeRekeningmutaties","Fondskoers",array("list_tdcode"=>"nowrap","search"=>true));
$list->addFixedField("TijdelijkeRekeningmutaties","Debet",array("list_tdcode"=>"nowrap","search"=>false));
$list->addFixedField("TijdelijkeRekeningmutaties","Credit",array("list_tdcode"=>"nowrap","search"=>false));
$list->addFixedField("TijdelijkeRekeningmutaties","Bedrag",array("list_tdcode"=>"nowrap","search"=>false));
$list->addFixedField("TijdelijkeRekeningmutaties","Transactietype",array("list_tdcode"=>"nowrap","description"=>"TT","search"=>false, "list_align" => "center"));



//$list->addField("TijdelijkeRekeningmutaties","change_date",array("width"=>100,"search"=>false));
//$list->addField("TijdelijkeRekeningmutaties","change_user",array("width"=>100,"search"=>false));


//$list->addField("TijdelijkeRekeningmutaties","aktie",array("width"=>100,"search"=>false));
//$list->addFixedField("TijdelijkeRekeningmutaties","Verwerkt",array("width"=>100,"search"=>false, "description"=>"vw", "list_align" => "center"));
//$list->addField("TijdelijkeRekeningmutaties","Memoriaalboeking",array("width"=>100,"search"=>false));
//$list->addFixedField("TijdelijkeRekeningmutaties","bankTransactieId",array("width"=>"","search"=>true));
//$list->addFixedField("TijdelijkeRekeningmutaties","settlementDatum",array("width"=>"","search"=>true, "description"=>"Set. dat."));
$list->addFixedField("TijdelijkeRekeningmutaties","Fonds",array("width"=>"","search"=>true));
$list->addFixedField("Fonds","fondssoort",array("width"=>"","search"=>true));

$list->addFixedField("Fonds","EindDatum",array("list_invisible"=>true));
$list->addFixedField("Portefeuilles","overgangsdepot",array("list_invisible"=>true));
$list->addColumn("","laatsteKoers",array("sql_alias" => "ActieveFondsen.laatsteKoers","list_invisible"=>true));
$list->addFixedField("","triggers",array());
$list->addFixedField("","AIRSkoers",array());
$list->addFixedField("TijdelijkeRekeningmutaties","add_date",array("width"=>100,"search"=>false,"list_invisible"=>true));
$list->addFixedField("TijdelijkeRekeningmutaties","regelnr",array("width"=>100,"search"=>true,"list_invisible"=>false));
$list->addFixedField("TijdelijkeRekeningmutaties","aktie",array("width"=>100,"list_invisible"=>true));
$list->setJoin("
  LEFT JOIN Fondsen ON TijdelijkeRekeningmutaties.Fonds = Fondsen.Fonds
  LEFT JOIN Rekeningen ON TijdelijkeRekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
  LEFT JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0
  LEFT JOIN ActieveFondsen ON TijdelijkeRekeningmutaties.Fonds = ActieveFondsen.Fonds
  ");
$list->ownTables=array('TijdelijkeRekeningmutaties');
$list->categorieVolgorde = array(
  'Portefeuilles' =>array('Gegevens'),
  'TijdelijkeRekeningmutaties' =>array('Algemeen'),
  'Fonds' =>array('Algemeen'),
  'Rekeningen' =>array('Algemeen'),
);
$html = $list->getCustomFields(array('TijdelijkeRekeningmutaties','Fonds','Rekeningen','Portefeuilles'));

if(empty($_GET['sort']))
{
	$_GET['sort'] = array("Rekening");
	$_GET['direction'] = array("ASC");
}

$list->setWhere(" TijdelijkeRekeningmutaties.change_user = '$USR' {$prioWhere} {$hideWhere}");
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);



$kruisQuery = "
SELECT
	kp.Portefeuille,
	kp.Boekdatum,
	round( sum( kp.bedragEUR ), 2 ) 
FROM
	(
    SELECT
      rk.Portefeuille,
      rm.id,
      rm.boekdatum,
      rm.valuta,
      rm.valutakoers,
      rm.debet,
      rm.credit,
      rm.bedrag,
      CASE
        WHEN rk.valuta = 'EUR' THEN
          rm.Bedrag ELSE rm.bedrag * rm.valutakoers 
        END AS 'BedragEUR' 
    FROM
      TijdelijkeRekeningmutaties rm
      INNER JOIN Rekeningen rk ON 
        rm.Rekening = rk.Rekening AND rk.consolidatie = 0
      INNER JOIN Portefeuilles pf ON 
        rk.Portefeuille = pf.Portefeuille 
    WHERE
      Grootboekrekening = 'KRUIS' 
      AND rm.add_user = '{$USR}' 
	) kp 
GROUP BY
	kp.Portefeuille,
	kp.Boekdatum 
HAVING
	SUM( kp.bedragEUR ) > 0.02 
	OR SUM( kp.bedragEUR ) < - 0.02
";

$afwijk = "";
$db = new DB();
$db->executeQuery($kruisQuery);
if ($db->records() > 0)
{
  while ($rec =$db->nextRecord())
  {
    $datum = dbdate2form($rec["Boekdatum"]);
    $afwijk .= "<li>{$rec["Portefeuille"]} ({$datum})</li>";
  }
}


//echo "<!-- \n".$list->getSQL()."-->";

session_start();

$_SESSION["trm_zv_query"] = $list->getSQL();

$qStr = str_replace("&prioFilter=off", "", getenv("QUERY_STRING"));
$_SESSION["NAV"] = new NavBar($PHP_SELF, $qStr);
$_SESSION["NAV"]->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$allow_add));
$_SESSION["NAV"]->addItem(new NavSearch($_GET['selectie']));
//$_SESSION['submenu'] = New Submenu();

$_SESSION['submenu'] = New Submenu();

$_SESSION['submenu']->addItem("Transactie-Import","transaktieImport.php");
$_SESSION['submenu']->addItem("<br>","");
if ($list->records() <> 0)
{

	$_SESSION['submenu']->addItem(vt("Verwerken"),"javascript:parent.frames['content'].verwerkSelectie();");
//	$_SESSION['submenu']->addItem("Verwerken","tijdelijkerekeningmutatiesVerwerk.php");
	//$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Lijst leegmaken"),"tijdelijkerekeningmutatiesDrop.php");
  //$_SESSION['submenu']->addItem("<br>","");

  $_SESSION['submenu']->addItem(vt("Zoek en vervang"),"tijdelijkerekeningmutatiesSearch.php?selectie=".$_GET['selectie']);
  //$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Opslaan als CSV"),"tijdelijkerekeningmutatiesCSV.php");
  //$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Opslaan als XLS"),"tijdelijkerekeningmutatiesXLS.php");
  //$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Fondsmutaties XLS"),"tijdelijkerekeningFondsmutatiesXLS.php");
  //$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Fondsmutaties PDF"),"tijdelijkerekeningFondsmutatiesPDF.php");
  //$_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem(vt("Boekingen PDF"),"tijdelijkerekeningFondsmutatiesPDF2.php");
  //$_SESSION['submenu']->addItem("<br>","");
  if ($__appvar["bedrijf"] == "HOME" OR $__appvar["bedrijf"] == "BLA")
  {
    $_SESSION['submenu']->addItem(vt("Blanco export"),"blancoExportFile.php");
  }

  if ($adventverwerking)
  {
    $_SESSION['submenu']->addItem(vt("Advent export"),"adventExport.php");

  }
  if ($millogicverwerking OR $__appvar["bedrijf"] == "HOME")
  {
    $_SESSION['submenu']->addItem(vt("Millogic export"),"millogicExport.php");
  }
  //aetodo: hoe te activeren
  if($__appvar["bedrijf"] == "HOME" ||
     $__appvar["bedrijf"] == "BOX" )
  {
    $_SESSION['submenu']->addItem(vt("AAB K-clienten"),"tijdelijkerekeningmutaties_AAB-kClient.php");
  }
}
else
{
  $geenRecords = true;
}
$_SESSION['submenu']->addItem($html,"");
session_write_close();

$content[javascript] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
  <link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
  <link href="widget/css/font-awesome.min.css" rel="stylesheet">
  <link href="style/smoothness/jquery-ui-1.11.1.custom.css" rel="stylesheet" type="text/css" media="screen">
<style>

header{
  padding:10px;
  font-size: 18px;
  font-weight: bold;

}
#content{

  width: 98%;
  height: 99%;
  overflow: auto;
  padding-right: 5px;



  font-family : Verdana, Geneva, Arial, Helvetica, sans-serif;
  font-size : 10pt;
}
.list_dataregel_rose{
  background:#FF99CC;
}
.list_dataregel_oranje{
  background:orange;
}
.list_dataregel_groen{
  background:#80FF80;
}
.list_dataregel_cyaan{
  background:#80FFFF;
}
.list_dataregel_maroon{
  background:Maroon;
}
.list_dataregel_maroon td{
  color:white;
}  
.list_dataregel_blauw td{
  color:white;
}  
.list_dataregel_blauw{
  background:#3E9EFF;
  
}
.legenda{
  padding:2px;
  border-radius: 5px;
  text-align: center;
  border:1px solid #333;
  height: 40px;
  
}
.selectVerwerk{
  padding: 10px;
  width: 400px;
  margin-bottom: 10px;
}
#vinkTel{
  display: inline-block;
  text-align: center;

  padding: 3px;
  margin-left: 10px;
  border: 1px solid #999;
  border-radius: 5px;
  background: #DDD;
  color: black;
}
.prioClick{

  float: left;
  width: 30px;
  background: #666;
  color:whitesmoke;
  height: 34px;
  border-radius: 5px;
  border:1px solid #EEE;
  text-align: center;
  padding:7px;
  font-size: 1.1em;
  font-weight: bold;
  cursor: pointer;
}
.prio{

  float: left;
  width: 20px;
  background: #333;
  color:whitesmoke;
  height: 15px;
  border-radius: 5px;
  text-align: center;
  padding:7px;
  font-size: 1.1em;
  font-weight: bold;
}

button {
  padding: 10px 15px 10px 15px;
  background: rgba(20,60,90,1);
  color: white;
  border: 0px;
  cursor: pointer;
}

#hideForm button{
  background-color: darkgreen;
}

.kruispost{
  margin-left: 2em;
  background: rgba(253,197,186,0.42);
  width: 300px;
  border:0;
  margin-bottom: 1em;
}

.kruispost legend{
  background: maroon;
  color: white;
  padding: 5px 15px;
}
.selectVerwerk legend{
  padding: 5px 15px;
}

.legendaTable{
  width: 100%;
}
.legendaTable td{
  width:50%;
}
TH {
    font-size: 10px;
}
.hideFS{
  margin: 5px;
  padding: 15px;
  margin-bottom: 10px;
}
</style>
<div id="content">

  <header style="<?=$prioStyle?>"><?=$prioText?></header>
<br>

  <form name="editForm" method="POST" id="filterForm">
    <input type="hidden" name="prioFilterNew" id="prioFilterNew" value="<?=$prioFilter?>" />
    <input type="hidden" name="prioFilterVB" id="prioFilterVB" value="ANY" />
<?=$list->filterHeader();?>

<div style="display: flex">
<fieldset class="hideFS">
  <legend>mutaties</legend>
    <form name="hideForm" method="POST" id="hideForm" class="hideForm">
      <input type="hidden" name="hideToggle" value="1">
      <?
      if ($_SESSION["TRM_hideState"] == "hide")
      {
        ?>
        <input type="hidden" name="hideState" value="unhide">
        <button type="submit">Tonen</button>
        <?
      }
      else
      {
        ?>

        <input type="hidden" name="hideState" value="hide">
        <button type="submit">Verbergen</button>

        <?
      }
      ?>
    </form>
</fieldset>
  <fieldset class="selectVerwerk">
    <legend> selectieve verwerking </legend>
    <button id="btnSelectAll">alles selecteren</button>
    <button id="btnSelectNone">niets selecteren</button>
    <span id="vinkTel">..</span>
  </fieldset>
  <?
  if ($afwijk != "")
  {
?>
    <fieldset class="kruispost">
      <legend>Kruispost afwijkingen</legend>
      <?=$afwijk?>
    </fieldset>
<?
  }
  ?>

</div>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
  </form>
<form action="tijdelijkerekeningmutatiesVerwerk.php" method="post" id="selectForm" name="selectForm" >
  <input type="hidden" name="totalRecords" value="<?=$list->records()?>">
  <input type="hidden" name="recordsChecked" id="recordsChecked" value="0">

<?php

$dblkup = new DB();


$first = true;
while($data = $list->getRow())
{
  if ($first)
  {
//   debug($data);
    $first = false;
  }
  $id = $data["id"]["value"];
  $triggers = "";

  $data[".vink"]["value"] = "<input id='vink_$id' name='vink_$id' class='vinkVeld' type='checkbox' />";

  // prio 8
  $boekDateParts = explode("-",$data["TijdelijkeRekeningmutaties.Boekdatum"]["value"]);
  $addDateParts = explode("-",$data["TijdelijkeRekeningmutaties.add_date"]["value"]);
  
  $t = array($data["TijdelijkeRekeningmutaties.add_date"]["value"],
             _julDag($data["TijdelijkeRekeningmutaties.add_date"]["value"]),
      $data["TijdelijkeRekeningmutaties.Boekdatum"]["value"],
             _julDag($data["TijdelijkeRekeningmutaties.Boekdatum"]["value"]));
  //debug($t, _julDag($data["add_date"]["value"]) - _julDag($data["Boekdatum"]["value"]));
  if (  ( 
             $boekDateParts[1] <> $addDateParts[1]    // maanden ongelijk
           AND 
             $addDateParts[2] > 6                     // boekdatm > 6
         ) 
        OR
        (
          _julDag($data["TijdelijkeRekeningmutaties.add_date"]["value"]) - _julDag($data["TijdelijkeRekeningmutaties.Boekdatum"]["value"])  > 31
        )
     )
  {
    $data["tr_class"] = "list_dataregel_blauw";
    $triggers .= "/8";
  }
  
  // prio 7
//  debug($data);
  if (passedEinddatum($data["Fondsen.EindDatum"]["value"]) OR
          $data["TijdelijkeRekeningmutaties.Fonds"]["value"] == "optie/future")
  {
    $data["tr_class"] = "list_dataregel_maroon";
    $triggers .= "/7";
  }  
  // prio 6
  if ( 
        $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "FONDS"
      AND
        $data["Fondsen.fondssoort"]["value"] == "OPT"
      AND
        ( $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] <> "A/O" AND
          $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] <> "A/S" AND
          $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] <> "V/O" AND
          $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] <> "V/S"
        )

      )
  {
    $data["tr_class"] = "list_dataregel_cyaan";
    $triggers .= "/6";
  }
  if ( 
        $data["TijdelijkeRekeningmutaties.aktie"]["value"] == "RVP"
      OR   
        $data["TijdelijkeRekeningmutaties.aktie"]["value"] == "DVP"
      )
  {
    $data["tr_class"] = "list_dataregel_cyaan";
    $triggers .= "/6";
  }
//  debug($data);
  if ( $data["Portefeuilles.overgangsdepot"]["value"] != 0 )  // call 9550

  {
    $data["tr_class"] = "list_dataregel_cyaan";
    $triggers .= "/6";
  }

  // prio 5
  if ( 
       (
            $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "FONDS"
          AND
            $data["Fondsen.fondssoort"]["value"] <> "STOCKDIV"
          AND
            ( $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] == "D" OR $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] == "L")
          AND
            $data["TijdelijkeRekeningmutaties.Fondskoers"]["value"] == "0"
        )  
       
      OR
      (
            $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "FONDS"
          AND
            $data["Fondsen.fondssoort"]["value"] == "STOCKDIV"
          AND
            ( $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] == "D" OR $data["TijdelijkeRekeningmutaties.Transactietype"]["value"] == "L")
          AND
            $data["TijdelijkeRekeningmutaties.Fondskoers"]["value"] <> "0"
      )
    )   
  {
    $data["tr_class"] = "list_dataregel_groen";
    $triggers .= "/5";
  }  
  // prio 4
  if (($data["TijdelijkeRekeningmutaties.Aantal"]["value"] == 0 AND $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "FONDS" )  OR
      (trim($data["TijdelijkeRekeningmutaties.Fonds"]["value"]) == "" AND $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "VKSTO" ) OR
      (trim($data["TijdelijkeRekeningmutaties.Fonds"]["value"]) == "" AND $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "VMAR" ))
  {
    $data["tr_class"] = "list_dataregel_oranje";
    $triggers .= "/4";
  }
 
  
  // prio 3
  if ( $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "FONDS" AND $data["TijdelijkeRekeningmutaties.Fondskoers"]["value"] <> "0" )
       
  {
    $query = "SELECT Koers FROM Fondskoersen WHERE Fonds = '".$data["TijdelijkeRekeningmutaties.Fonds"]["value"]."' AND Datum <= NOW() ORDER BY Datum DESC";
    $tmpRec = $dblkup->lookupRecordByQuery($query);
    
    if ($tmpRec["Koers"]/$data["TijdelijkeRekeningmutaties.Fondskoers"]["value"] >= 9.5)
    {
//      $data["AIRSkoers"]["value"] = $tmpRec["Koers"];
      $data["tr_class"] = "list_dataregel_geel";
      $triggers .= "/3";
    }
    
  }
  if ( substr($data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"],0,3) == "DIV" AND
        $data["Fondsen.fondssoort"]["value"] == "STOCKDIV"    )
  {
     $data["tr_class"] = "list_dataregel_geel";
     $triggers .= "/3";
  }        
  // prio 2
  if ( ( $data["TijdelijkeRekeningmutaties.Valuta"]["value"] <> "EUR" AND $data["TijdelijkeRekeningmutaties.Valutakoers"]["value"] == 1)  OR
       ( $data["TijdelijkeRekeningmutaties.Valuta"]["value"] == "EUR" AND $data["TijdelijkeRekeningmutaties.Valutakoers"]["value"] <> 1)  OR
       ( $data["TijdelijkeRekeningmutaties.Valutakoers"]["value"] == 0 )                                       OR
       ( $data["TijdelijkeRekeningmutaties.Valuta"]["value"] == "???"  )                                       OR
       ( trim($data["TijdelijkeRekeningmutaties.Valuta"]["value"]) == "" ) )
  {
    $data["tr_class"] = "list_dataregel_rood";
    $triggers .= "/2";
  }
  
  if ( (substr($data["TijdelijkeRekeningmutaties.Rekening"]["value"],-3) <> "EUR" AND
        substr($data["TijdelijkeRekeningmutaties.Rekening"]["value"],-3) <> "MEM" AND
        substr($data["TijdelijkeRekeningmutaties.Rekening"]["value"],-3) <> "LEN" AND
        substr($data["TijdelijkeRekeningmutaties.Rekening"]["value"],-3) <> "DEP") AND
         $data["TijdelijkeRekeningmutaties.Valuta"]["value"] == "EUR" )
  {
    $data["tr_class"] = "list_dataregel_rood";
    $triggers .= "/2";
  }        
  
  // prio 1

  $used = false;



  if ($data["TijdelijkeRekeningmutaties.regelnr"]["value"] < 0 )   // LET OP onderstaande werkt alleen voor AAB NL
  {
    $vbSelected = "ABN";
    $used = false;
    for ($ts=0; $ts < count($colorAbnArray); $ts++)
    {
      if (stristr($data["TijdelijkeRekeningmutaties.Omschrijving"]["value"], $colorAbnArray[$ts]))
      {
        $data["tr_class"] = "list_dataregel_rose";
        $used = true;
      }
    }
    // **datum
    if (substr($data["TijdelijkeRekeningmutaties.Omschrijving"]["value"],0,2) == "**" AND
        substr($data["TijdelijkeRekeningmutaties.Omschrijving"]["value"],15,2) == "//" )
    {
      $data["tr_class"] = "list_dataregel_rose";
      $used = true;
      $colorAbnArray[] = "combi ** //";
    }
    
    if ($used)
    {
      $triggers .= "/1";
    }
  }
  else
  {
    $vbSelected = "ANY";
    for ($ts=0; $ts < count($colorArray); $ts++)
    {
      if (stristr($data["TijdelijkeRekeningmutaties.Omschrijving"]["value"], $colorArray[$ts]) AND
        (
          $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "ONTTR" OR
          $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "STORT"
        )
      )
      {
        $data["tr_class"] = "list_dataregel_rose";
        $used = true;
      }
    }
  }
  if (stristr($data["TijdelijkeRekeningmutaties.Omschrijving"]["value"],"KOSTEN ") and
              $data["TijdelijkeRekeningmutaties.Grootboekrekening"]["value"] == "ONTTR")
  {
      $data["tr_class"] = "list_dataregel_rose";
      $triggers .= "/1";
  }
  $data["triggers"]["value"] = $triggers;
	echo $list->buildRow($data);
}
?>
</table>
</form>
<br />
<br />
<br />
<fieldset>
  <legend><?= vt('Legenda'); ?></legend>
  <table  class="legendaTable">
    <tr style="vertical-align: text-top;">
      <td>
        <p class="list_dataregel_rose legenda"><button id="prio_1" class="prioClick">1</button><?= vt('omschrijving bevat'); ?> <?=implode(", ",$colorAbnArray)?>, KOSTEN </p>
        <p class="list_dataregel_rood legenda"><button id="prio_2" class="prioClick">2</button>(<?= vt('valuta'); ?> <> EUR &amp; <?= vt('koers'); ?> = 1) of (<?= vt('valuta'); ?> = EUR &amp; <?= vt('koers'); ?> <> 1) of <?= vt('koers'); ?> = 0 <?= vt('of valuta onbekend'); ?></p>
        <p class="list_dataregel_geel legenda"><button id="prio_3" class="prioClick">3</button><?= vt('koersfactor'); ?> > 10 (<?= vt('fractiefonds'); ?>) of <br/><?= vt('Dividend op stockjes'); ?></p>
        <p class="list_dataregel_oranje legenda"><button id="prio_4" class="prioClick">4</button><?= vt('aantal'); ?> = 0 &amp; <?= vt('grootboek'); ?> = FONDS of <br/>VKSTO <?= vt('zonder Fonds'); ?></p>
      </td>
      <td>
        <p class="list_dataregel_groen legenda"><button id="prio_5" class="prioClick">5</button><?= vt('grootboek'); ?> = FONDS &amp; <?= vt('Fondssoort'); ?> = STOCKDIV &amp; TT = D of L &amp; <?= vt('Koers'); ?> <> 0 <?= vt('of'); ?><br />
        grootboek = FONDS &amp; Fondssoort <> STOCKDIV &amp; TT = D of L &amp; Koers = 0</p>
        <p class="list_dataregel_cyaan legenda"><button id="prio_6" class="prioClick">6</button>foutief transactietype bij optietransactie of <br/> ABN AMRO DVP/RVP of overgangsdepot = 1</p>
        <p class="list_dataregel_maroon legenda" style="color: white;"><button id="prio_7" class="prioClick">7</button>Fonds.einddatum <= vandaag of <br/> Optie/Future import CS</p>
        <p class="list_dataregel_blauw legenda" style="color: white;"><button id="prio_8" class="prioClick">8</button>boekdatum vs aanmaakdatum maandoverloop</p>
      </td>
    </tr>
  </table>
  
</fieldset>



<script>
  function verwerkSelectie(){

    var aantal = $('.vinkVeld:checked').length
    var totaal = $('.vinkVeld').length
    if (aantal != 0)
    {
      AEConfirm(
        'Weet u zeker dat u een selectie wilt verwerken <br/>U heeft ' + aantal + ' van ' + totaal + ' records geselecteerd?',
        'Selectie verwerken?',
        function () {
          $("#selectForm").submit();
        }
      );
    }
    else
    {
      $("#selectForm").submit();
    }


    }
    function countChecked()
    {
      var aantal = $('.vinkVeld:checked').length
      $("#vinkTel").html(aantal+ " aangevinkt");
      $("#recordsChecked").val(aantal);
    }
    $(document).ready(function(){

      $(".prioClick").click(function (e) {
        e.preventDefault();
        $("#prioFilterNew").val($(this).attr("id"));
        $("#prioFilterVB").val("<?=$vbSelected?>");
        $("#filterForm").submit();
      });

      $(".vinkVeld").prop('checked', false);
      countChecked();
      $("#btnSelectAll").click(function()  {  $(".vinkVeld").prop('checked', true);   countChecked(); });
      $("#btnSelectNone").click(function() {  $(".vinkVeld").prop('checked', false);  countChecked(); });
      $(".vinkVeld").change(function(){
         countChecked();
      });
    });

    //javascript:parent.frames['content'].sendMails();
    </script>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}

if($geenRecords == true)
{
  $db=new DB();
  $query = "SELECT id FROM TijdelijkeRekeningmutaties ";
  echo "<br> " . vt('Aantal gevonden records van overige gebruikers is') . " (".$db->QRecords($query).")";
}

echo "</div>";


echo template($__appvar["templateRefreshFooter"],$content);


// local functions
function passedEinddatum($dbdate)
{
  $parts = explode("-", $dbdate);
  if ((int)$parts[0] == 0) return false;  // lege datum
  
  return (mktime(0,0,0,$parts[1],$parts[2],$parts[0]) < mktime());
}


function _julDag($dbDatum)
{
  $parts = explode("-",$dbDatum);
  $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
  return floor($julian / 86400);
}
?>