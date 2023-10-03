<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

class fondsRente extends Table
{
  var $data = array();
  function fondsRente()
  {
    $this->defineData();
    $this->setDefaults();
    $this->set($this->data['identity'],0);
  }
  
  function addField($name, $properties)
	{
		$this->data['fields'][$name] = $properties;
	}

  function defineData()
  {
    global $__appvar;
    $this->data['table']  = "fondsRente";
    $this->data['identity'] = "id";
    $this->data['logChange'] = true;
		$this->addField('id',array("description"=>"id","db_size"=>"11","db_type"=>"int","form_type"=>"text","form_visible"=>false,"list_visible"=>false,"list_align"=>"right","list_search"=>false,"list_order"=>"true"));
		$this->addField('Fonds',          array("description"=>"Fonds",          "db_size"=>"25","db_type"=>"varchar","form_size"=>"25","form_type"=>"text","form_visible"=>true,"list_width"=>"150","list_visible"=>true,"list_align"=>"left", "list_search"=>false,"list_order"=>"true"));
		$this->addField('Rentepercentage',array("description"=>"Rentepercentage","db_size"=>"0", "db_type"=>"double", "form_size"=>"8", "form_type"=>"text","form_visible"=>true,"list_width"=>"150","list_visible"=>true,"list_align"=>"right","list_search"=>false,"list_order"=>"true"));
 		$this->addField('VolgendeRentedatum',array("description"=>"VolgendeRentedatum","db_size"=>"0","db_type"=>"datetime","form_type"=>"calendar","form_visible"=>true,"list_width"=>"150","list_visible"=>true,"list_align"=>"left","list_search"=>false,"list_order"=>"true"));
 }                      
}

$editScript = "fondsEdit.php";

$list = new MysqlList2();
$list->editScript = $editScript;
$list->idTable = "Fondsen";
$list->perPage = $__appvar['rowsPerPage'];

$list->addFixedField("Fonds","Fonds",array("list_width"=>150,"search"=>true));
$list->addFixedField("Fonds","Omschrijving",array("list_width"=>150,"search"=>true));
$list->addFixedField("Fonds","FondsImportCode",array("list_width"=>150,"search"=>true));
$list->addFixedField("Fonds","Valuta",array("list_width"=>75,"search"=>false));

$objecten=array('Fonds','BeleggingssectorPerFonds','BeleggingscategoriePerFonds','ActieveFondsen','fondsRente','fondskosten','ZorgplichtPerFonds','doorkijk_categorieWegingenPerFonds');
$DB=new DB();
$query="SELECT id FROM FondsExtraVelden limit 1";
if($DB->QRecords($query))
  $objecten[]='FondsExtraInformatie';
$html = $list->getCustomFields($objecten,'Fondslist');

$joinfondsRente='';
$joinfondsKosten='';
$joinZorgPlicht='';
$joinActieveFondsen='';
$joinBeleggingscategorie='';
$joinBeleggingssector='';
$joinDoorkijk='';
foreach ($list->columns as $colData)
{
  if($colData['objectname'] == 'BeleggingssectorPerFonds')
  {
    $joinBeleggingssector=" LEFT JOIN BeleggingssectorPerFonds ON Fondsen.Fonds = BeleggingssectorPerFonds.Fonds ";
  }
  if($colData['objectname'] == 'BeleggingscategoriePerFonds')
  {
    $joinBeleggingscategorie=" LEFT JOIN BeleggingscategoriePerFonds ON Fondsen.Fonds = BeleggingscategoriePerFonds.Fonds ";
  }
  if($colData['objectname'] == 'ZorgplichtPerFonds')
  {
    $joinZorgPlicht=" LEFT JOIN ZorgplichtPerFonds ON Fondsen.Fonds = ZorgplichtPerFonds.Fonds ";
  }
  if($colData['objectname'] == 'ActieveFondsen')
  {
    $joinActieveFondsen=" LEFT JOIN ActieveFondsen ON Fondsen.Fonds = ActieveFondsen.Fonds ";
  }
  if($colData['objectname'] == 'fondsRente')
  {
    $joinfondsRente=" LEFT JOIN fondsRente ON Fondsen.Fonds = fondsRente.Fonds ";
  }
  if($colData['objectname'] == 'fondskosten')
  {
    $joinfondsKosten=" LEFT JOIN (SELECT fondskosten.* FROM fondskosten
JOIN (SELECT fondskosten.fonds, max(fondskosten.datum)  as datum FROM fondskosten GROUP BY fonds) as maxdatum ON fondskosten.fonds=maxdatum.fonds AND fondskosten.datum=maxdatum.datum) as fondskosten ON Fondsen.Fonds = fondskosten.fonds ";
  }
  if($colData['objectname']=='doorkijk_categorieWegingenPerFonds')
  {
    $joinDoorkijk=" LEFT JOIN (SELECT doorkijk_categorieWegingenPerFonds.* FROM doorkijk_categorieWegingenPerFonds
JOIN (SELECT doorkijk_categorieWegingenPerFonds.fonds, max(doorkijk_categorieWegingenPerFonds.datumVanaf)  as datumVanaf FROM doorkijk_categorieWegingenPerFonds GROUP BY fonds) as maxdatum ON doorkijk_categorieWegingenPerFonds.fonds=maxdatum.fonds AND doorkijk_categorieWegingenPerFonds.datumVanaf=maxdatum.datumVanaf) as doorkijk_categorieWegingenPerFonds ON Fondsen.Fonds = doorkijk_categorieWegingenPerFonds.fonds ";
    $joinDoorkijk=" LEFT JOIN tmp_categorieWegingenPerFonds as doorkijk_categorieWegingenPerFonds ON doorkijk_categorieWegingenPerFonds.fonds=Fondsen.Fonds ";
  }
  if($colData['objectname'] == 'FondsExtraInformatie')
  {
    $joinfondsKosten=" LEFT JOIN FondsExtraInformatie ON Fondsen.Fonds = FondsExtraInformatie.fonds ";
  }
}
if($joinfondsRente<>'')
  createRenteTabel();
if($joinDoorkijk<>'')
  createCategorieWegingenPerFonds();

  $list->ownTables=array('Fondsen');
  $list->setJoin("$joinBeleggingssector $joinBeleggingscategorie $joinActieveFondsen $joinfondsRente $joinfondsKosten $joinZorgPlicht $joinDoorkijk");

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");

if(checkAccess($type)) 
{
	// superusers
	$allow_add = true;
}
else 
{
	// normale user
	$allow_add = false;
}

// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<?=$list->filterHeader();?>
<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
while($data = $list->getRow())
{
  if(isset($data['ActieveFondsen.Aantal']))
  {
   // if($data['ActieveFondsen.Aantal']['value'] > 0)
  //    $data['ActieveFondsen.Aantal']['value']=1;
  }
 echo $list->buildRow($data);
}
?>
</table>
<?
logAccess();
if($__debug) {
	echo getdebuginfo();
}

echo template($__appvar["templateRefreshFooter"],$content);

function createRenteTabel()
{
  //$dbdatum=getLaatsteValutadatum();
  $dbDatumJul=time();
  $dbdatum=date('Y-m-d');

  $db=new DB();
$query="SELECT
Fondsen.Fonds,
Fondsen.Renteperiode,
Fondsen.Lossingsdatum,
Fondsen.EindDatum,
Fondsen.Rentedatum,
Fondsen.Rentepercentage,
Fondsen.EersteRentedatum,
FondsParameterHistorie.id as historieId
FROM
Fondsen
LEFT JOIN FondsParameterHistorie ON Fondsen.Fonds=FondsParameterHistorie.Fonds
WHERE 
 Fondsen.fondssoort IN('OBL','OVERIG') AND 
(Fondsen.EindDatum>'$dbdatum' OR Fondsen.EindDatum='0000-00-00' OR Fondsen.EindDatum IS NULL) AND 
(Fondsen.Lossingsdatum > '$dbdatum' OR Fondsen.Lossingsdatum='0000-00-00' OR Fondsen.Lossingsdatum IS NULL)
GROUP BY Fondsen.Fonds
ORDER BY Fonds";
$db->SQL($query);
$db->Query();
while($data=$db->nextRecord())
{
  if($data['historieId']>0)
  {
    $data=getRenteParameters($data['Fonds'], date('Y-m-d'));
  }
  $fondsen[$data['Fonds']]=$data;
}
  
  foreach($fondsen as $fonds=>$fondsData)
  {
    $query="SELECT Rentepercentage FROM Rentepercentages WHERE Fonds='".mysql_real_escape_string($fonds)."' AND Datum < '$dbdatum' order by Datum desc, GeldigVanaf DESC LIMIT 1";
    $db->SQL($query);
    $rente=$db->lookupRecord();
    if($rente['Rentepercentage'])
      $fondsen[$fonds]['Rentepercentage']=$rente['Rentepercentage'];
    $timer=0;
    $start= db2jul($fondsData['EersteRentedatum']);

    $rentedatumJul=db2jul($fondsData['Rentedatum']);
    $renteDag=date('d',$rentedatumJul);
    $renteMaand=date('m',$rentedatumJul);
    if($fondsData['Renteperiode']>0 && $start > 1)
    {
      $eind=$dbDatumJul;
      $timer=$start;
      $maanden=0;
      while($timer<=$eind)
      {
        $maanden+=$fondsData['Renteperiode'];
        $timer=mktime(0,0,0,$renteMaand+$maanden,$renteDag,$jaar);

      }
    }
    $fondsen[$fonds]['VolgendeRentedatum']=adodb_date('Y-m-d',$timer);
    if($fondsen[$fonds]['VolgendeRentedatum']=='1970-01-01')
      $fondsen[$fonds]['VolgendeRentedatum']='';
  }

  $query="CREATE TEMPORARY TABLE `fondsRente` (`id` int(11) NOT NULL auto_increment,`Fonds` varchar(25) default NULL,`VolgendeRentedatum` datetime default NULL,`Rentepercentage` double default NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM";
  $db->SQL($query);
  $db->Query();
  foreach($fondsen as $fonds=>$fondsData)
  { 
    $query="INSERT INTO fondsRente values (null,'".mysql_real_escape_string($fonds)."','".$fondsData['VolgendeRentedatum']."','".$fondsData['Rentepercentage']."')";
    $db->SQL($query);
    $db->Query();
  }
  $query="ALTER TABLE fondsRente ADD INDEX( Fonds ); ";
  $db->SQL($query);
  $db->Query();
}

function createCategorieWegingenPerFonds()
{
  $db=new DB();
  $query="CREATE TEMPORARY TABLE tmp_categorieWegingenPerFonds like doorkijk_categorieWegingenPerFonds";
  $db->SQL($query);
  $db->Query();

  $query="INSERT INTO tmp_categorieWegingenPerFonds (	SELECT
		doorkijk_categorieWegingenPerFonds.*
	FROM
		doorkijk_categorieWegingenPerFonds
	JOIN (
		SELECT
			doorkijk_categorieWegingenPerFonds.fonds,
			max(
				doorkijk_categorieWegingenPerFonds.datumVanaf
			) AS datumVanaf
		FROM
			doorkijk_categorieWegingenPerFonds
		GROUP BY
			fonds
	) AS maxdatum ON doorkijk_categorieWegingenPerFonds.fonds = maxdatum.fonds
	AND doorkijk_categorieWegingenPerFonds.datumVanaf = maxdatum.datumVanaf)";
  $db->SQL($query);
  $db->Query();

  $query="ALTER TABLE tmp_categorieWegingenPerFonds ADD INDEX( fonds ); ";
  $db->SQL($query);
  $db->Query();


}

?>