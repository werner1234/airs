<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2011/08/31 14:37:39 $
 		File Versie					: $Revision: 1.2 $
 		
 		$Log: crm_naw_cfList.php,v $
 		Revision 1.2  2011/08/31 14:37:39  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2006/01/31 11:16:13  cvs
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
session_start();

$allow_add =true;
$editScript = "crm_naw_cfEdit.php";

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];

$list->addField("CRM_naw_cf","id",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","rel_id",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","verzendAdres",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","verzendPc",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","verzendPlaats",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","verzendLand",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","kvkInDosier",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","statutenInDosier",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","rekeningActiefSinds",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","tripartieteInDosier",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","tripartieteDatum",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenbeheerOvereenkomstInDosier",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenbeheerOvereenkomstDatum",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","kinderen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","inkomenSoort",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","inkomenIndicatie",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenOnroerendGoed",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenHypotheek",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenOverigVermogen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenOverigSchuld",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenTotaalBelegbaar",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenBelegdViaDC",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenHerkomst",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","vermogenVerplichtingen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringBelegtSinds",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringBelegtInEigenbeheer",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringBelegtInVermogensadvies",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringBelegtInProducten",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetVastrentende",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetVastrentendeDatum",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetBeleggingsFondsen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetBeleggingsFondsenDatum",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetIndividueleAandelen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetIndividueleAandelenDatum",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetOpties",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetOptiesDatum",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetFutures",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","ervaringMetFuturesDatum",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","beleggingsHorizon",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","beleggingsDoelstelling",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","risicoprofielFinancieleGegevens",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","risicoprofielGesprek",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","risicoprofielAfwijkendeAfspraak",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","risicoprofielOverig",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","risicoprofiel",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielAandelenBinnenland",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielAandelenBuitenland",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielObligatiesEuro",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielObligatiesOverigeValuta",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielWarrants",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielOptiesKopenCalls",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielOptiesOngedektVerkopenCalls",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielOptiesGedektVerkopenCalls",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielOptiesKopenPuts",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielOptiesVerkopenPuts",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielTermijnFuturesOptiesVerkopenPuts",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielValutasInclOTC",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielEdelmetalen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielVerleentToestemmingDebetstanden",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielNietTerbeurzeBeleggingsfondsen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielInsiderRegeling",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","profielOverigeBeperkingen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","huidigesamenstellingAandelen",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","huidigesamenstellingObligaties",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","huidigesamenstellingOverige",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","huidigesamenstellingLiquiditeiten",array("width"=>100,"search"=>false));
$list->addField("CRM_naw_cf","huidigesamenstellingTotaal",array("width"=>100,"search"=>false));


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
while($data = $list->printRow())
{
	echo $data;
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