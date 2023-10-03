<?php
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
$AETemplate = new AE_template();
//$AERekeningmutaties = new AE_RekeningMutaties();

$data = array_merge($_POST, $_GET);
$editScript = "rekeningmutatiesAFvinken_Edit.php";

$vb = "VEC";
$dbg = new DB();
$query = "SELECT * FROM grootboeknummers WHERE vermogensbeheerder='$vb' ORDER BY grootboekrekening ";
$dbg->executeQuery($query);
while ($gbRec = $dbg->nextRecord())
{
  $gbOptions .= "\n\t<option value='".$gbRec["rekeningnummer"]."' >".$gbRec["grootboekrekening"]." (".$gbRec["rekeningnummer"].")</option>";
}

$mutationType = '';
$table = 'Rekeningmutaties';
$object = 'Rekeningafschriften';
$subHeader = "Filter: ";
if ($_GET["blockFilterAction"] == "nomatch")
{
  $extraWhere .= " AND ( isnull(RekeningmutatiesAfvink.status)) ";
  $subHeader  .= " ( niet verwerkt ) ";
}
else
{
  $subHeader .= "( allemaal )";
}


$list = new MysqlList2();
$list->idField = "id";
$list->idTable =$table;

$list->editScript = $editScript ;
$list->perPage = 1500;

$list->setFullEditScript();

// get afschriftgegevens.
$list->queryWhere = '
  Rekeningmutaties.Boekdatum > "2016-01-01"
    AND
  Portefeuilles.Vermogensbeheerder = "VEC"
  '.$extraWhere;

//$list->addFixedField($table,"id",array("list_align"=>"right","width"=>100,"search"=>false));

$list->addColumn("","icon",array("list_width"=>"85","description"=>" ",'list_nobreak'=>true,'list_order'=>false));
$list->addColumn("RekeningmutatiesAfvink","status",array("description"=>"ST","search"=>false));
$list->addFixedField($table,"Rekening",array("list_align"=>"left","search"=>true, "list_width" => "120"));
$list->addFixedField("Portefeuilles","Client",array("list_align"=>"left","search"=>true));
$list->addFixedField("Portefeuilles","Portefeuille",array("list_align"=>"left","search"=>true));
$list->addFixedField($table,"Boekdatum",array("list_align"=>"right","search"=>false,"list_width" => "", "description"=>"B. datum"));
$list->addFixedField($table,"Grootboekrekening",array("list_align"=>"center","search"=>true,"description" => "GB"));
$list->addFixedField($table,"Omschrijving",array("list_align"=>"left","search"=>true,"td_style" => "nowrap"));
$list->addFixedField($table,"Bedrag",array("list_align"=>"right","search"=>true));
$list->categorieVolgorde[$table]=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');
$list->categorieVolgorde['RekeningmutatiesAfvink']=array('Algemeen');
$html = $list->getCustomFields(array($table,'Portefeuilles', 'RekeningmutatiesAfvink'),'rekMut');
$list->ownTables=array($table);

$list->setJoin("
Inner Join Rekeningen ON 
  $table.Rekening = Rekeningen.Rekening
Inner Join Portefeuilles ON 
  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.interndepot=1 
LEFT JOIN  RekeningmutatiesAfvink ON 
 $table.id = RekeningmutatiesAfvink.rekmut_id ");

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem($html,"");




if(empty($_GET['sort']))
{
	$_GET['sort'] = array("Volgnummer");
	$_GET['direction'] = array("ASC");
}

$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

/** get editcontent style to content style else it gets broken **/
$content['style'] = $editcontent['style'];
$content['style'] .= $AETemplate->loadCss('colorCodingMutationsList', 'classTemplates/rekeningmutaties/css');


$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);

?>
<style>
  .afvinkHeader{
    background: #daa001;
    padding: 10px;
  }
  .vinkveld{
    background: #daa001;
    padding: 10px;
  }
  .btnAction{
    padding: 4px;
    margin-right: 10px;


  }
  #totSelectie{
    font-size: 2em;
  }
  .rowRed
  {
    background: #FDD;
  }
  .rowGreen{
    background: #DFD;
  }
  .selblok
  {
    width: 400px;
    margin-top: 7px;
    padding-top: 10px;
    padding-bottom: 10px;
    border: 1px #eee solid;
    border-radius: 5px;
    background: white;
    text-align: center;
    vertical-align: middle;
  }
</style>
<?

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $list->perPage,$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();

echo '<br><h2>'.$subHeader.'</h2>';

echo $list->filterHeader();
?>
<fieldset class="filterRow ">
  <span style="float: left"><b>Filter opties:</b></span>
  <button id="filterAll"> Alles </button>
  <button id="filterNomatch"> Niet verwerkt </button>
  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

  </fieldset>


  <form id="blockFilterForm">
    <input type="hidden" name="page" value="<?=$_GET["page"]?>" />
    <input type="hidden" name="selectie" value="<?=$_GET["selectie"]?>" />
    <input type="hidden" name="einddatum" id="einddatum" value="<?=$_GET["einddatum"]?>" />
    <input type="hidden" id="blockFilterAction" name="blockFilterAction" value="" />
  </form>
<?
echo '<table class="list_tabel" cellspacing="0">';
?>
<tr>
  <td colspan="20" class="afvinkHeader">
    <button id="btnAll" class="btnAction">alles selecteren</button>
    <button id="btnNone" class="btnAction">niets selecteren</button>
    <button id="btnMatch" class="btnAction">match selectie</button>

    <select name="nwGrootboek" id="nwGrootboek">
      <?=$gbOptions?>
    </select>
    <button id="btnGrootboek" class="btnAction">&nbsp;&nbsp;&nbsp;grootboek wijzigen</button>
    <div class="selblok">totaal selectie: <span id="totSelectie"></span> (<span id="cntSelectie">0</span>)</div>
  </td>
</tr>
<?
echo $list->printHeader(true);//true
while($data = $list->getRow())
{
//  debug($data);
  $data["noClick"] = true;

  if ($data["RekeningmutatiesAfvink.status"]["value"] == 1)
  {
    $data["tr_class"] = "rowRed";
  }
  if ($data["RekeningmutatiesAfvink.status"]["value"] == 2)
  {
    $data["tr_class"] = "rowGreen";
  }
  $data[".icon"]["value"] .= "<div class='vinkveld'><input type='checkbox' class='afvink'  name='id_".$data["id"]["value"]."' id='id_".$data["id"]["value"]."' value='1' /></span>";
  $data['disableEdit']=true;
  $data['Rekeningmutaties.Bedrag']['td_style'] = " id='totaal_".$data["id"]["value"]."' data-bedrag='".$data["Rekeningmutaties.Bedrag"]["value"]."' ";
  //$data['id']['value']=0;
 // $list->fullEditScript="rekeningmutaties_v2_Edit.php?action=edit&mutatieId=".$data['id']['value'];
  echo $list->buildRow($data);
}
echo '</table>';

?>
<script>
  var totaal;
  function berekenSelectie()
  {
    var b;
    totaal = 0;
    count = 0;
    $(".afvink:checked").each(function(index, elem)
    {
      var mId = $(elem).attr("id").substring(3);
      b = parseFloat(($("#totaal_"+mId)).data("bedrag"));
      console.log(b);
      totaal = totaal + b;
      count++;
    });
    if (!isNaN(totaal))
    {
      $("#totSelectie").text(totaal.toFixed(2));
    }
    else
    {
      $("#totSelectie").text("--");
    }

    $("#cntSelectie").text(count.toFixed(0));
    console.log("totaal: " + totaal);
  }

  $(document).ready(function()
  {
    $("#filterAll").click(function()
    {
      $("#blockFilterForm").submit();
    });

    $("#filterNomatch").click(function()
    {

      $("#blockFilterAction").val("nomatch");
      $("#blockFilterForm").submit();
    });

    $(".afvink").change(function(){
      berekenSelectie();
    });

    $(".btnAction").click(function()
    {
      var mId = $(this).attr("id");
      var item_id = "";
      var action = mId;
      var grootboek = $("#nwGrootboek").val();

      $(".afvink:checked").each(function(index, elem)
      {
        item_id += ($(elem).attr("name").substring(3)+";");
      });



      console.log("item: " + item_id);
      console.log("action: " + action);
      console.log("grootboek: " + grootboek);

      switch(mId)
      {
        case "btnAll":
          $(".afvink").prop('checked',true);
          berekenSelectie();
          break;
        case "btnNone":
          $(".afvink").prop('checked',false);
          berekenSelectie();
          break;
        case "btnGrootboek":
        case "btnMatch":
          $.ajax({
            url:'ajax/updateRekmutAfvink.php',
            type: "POST",
            data:{
              action: action,
              items: item_id,
              grootboek: grootboek,
            },
            dataType:'json',
            success:function(data)
            {
              console.log("reloading...");
              location.reload(true);
            }
          });
          break;
      }

    });
  });
</script>
<?


if($__debug)
  echo getdebuginfo();
echo template($__appvar["templateRefreshFooter"],$content);




//echo $AETemplate->parseFile('rekeningmutaties/colorCodingLegend.html');


logAccess();
if($__debug) {
	echo getdebuginfo();
}
?>
