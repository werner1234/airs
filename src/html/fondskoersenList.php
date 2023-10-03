<?php
/*
    AE-ICT sourcemodule created 11 sep. 2020
    Author              : Chris van Santen
    Filename            : fondskoersenList.php


*/

include_once("wwwvars.php");
include_once("../classes/mysqlList.php");

$editScript = "fondskoersenEdit.php";
$data = array_merge($_GET, $_POST);

$list = new MysqlList2();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];
$list->noExport = true;

//$list->addField("Fondskoersen","id",array("width"=>100,"search"=>false));
$list->addFixedField("Fondskoersen", "Fonds", array("list_width" => 150, "search" => true));
$list->addFixedField("Fondskoersen", "Datum", array("search" => false));
$list->addFixedField("Fondskoersen", "Koers", array("list_width" => 100, "search" => false, "align" => "right"));
if($__appvar['bedrijf']=='BOX' || $__appvar['bedrijf']=='TEST')
{
  $list->addFixedField("Fondskoersen", "oorspKrsDt", array("list_width" => 100, "search" => false, "align" => "right"));
}

$html = $list->getCustomFields(array('Fondskoersen'));
$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("<br>", "");
$_SESSION['submenu']->addItem($html, "");
$content = $editcontent;

$allow_add = false; // normale user
if (checkAccess($type))
{
  $allow_add = true;
}// superusers


$fondsSoortActive = array('AAND', 'OBL', 'STOCKDIV', 'OVERIG', 'INDEX', '');
if ( isset($_GET['fondsSoort']) )
{
  $fondsSoortActive = explode(',', $_GET['fondsSoort']);
}



if (!empty($Fonds))
{
  if(strtolower(substr($Fonds,0,3))=='id:')
    $list->setWhere(" id = '" . substr($Fonds,3) . "' ");
  else
    $list->setWhere(" Fonds = '" . $Fonds . "' ");

  $db = new DB();
  $query = "SELECT year(Datum) as jaar FROM Fondskoersen WHERE Fonds='$Fonds' GROUP BY jaar ORDER By jaar";
  $db->SQL($query);
  $db->Query();
  $jaren = array();
  while ($data = $db->nextRecord())
    $jaren[] = $data['jaar'];

  $koersenHtml = "
  <table border=1>
    <tr class=\"list_kopregel\">
      <td class=\"list_kopregel_data\" width=100>".vt("Datum")."</td>
      <td class=\"list_kopregel_data\" width=100 align=right>".vt("Koers")."</td>
    </tr>\n";
  foreach ($jaren as $jaar)
  {
    $query = "SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='$Fonds' AND year(Datum)='$jaar' ORDER BY Datum desc limit 1";
    $db->SQL($query);
    $db->Query();
    $data = $db->nextRecord();
    $koersenHtml.="<tr><td>" . dbdate2form($data['Datum']) . "</td><td align=right>" . $data['Koers'] . "</td></tr>\n";
  }
  $koersenHtml.="</table>\n";

  $laatsteKoers = getLaatsteValutadatum();
  $perioden = array(
    '1-jaars' => 'date(\'' . $laatsteKoers . '\')-interval 1 year',
    '3-jaars' => 'date(\'' . $laatsteKoers . '\')-interval 3 year',
    '5-jaars' => 'date(\'' . $laatsteKoers . '\')-interval 5 year'
  );
  $koersenHtml.="
  <table border=1>
    <tr class=\"list_kopregel\">
      <td class=\"list_kopregel_data\" width=100>".vt("Periode")."</td>
      <td class=\"list_kopregel_data\" width=100 align=right>".vt("STDDEV")."</td>
      <td class=\"list_kopregel_data\" width=100 align=right>".vt("Aantal")."</td>
    </tr>\n";

  foreach ($perioden as $periode => $wherePeriode)
  {
    $query = "select Datum FROM Fondskoersen WHERE Fonds='$Fonds' AND Datum < $wherePeriode limit 1";
    $db->SQL($query);
    $beschikbaar = $db->lookupRecord();

    $query = "SELECT koers FROM Fondskoersen WHERE Fonds='$Fonds' AND Datum > $wherePeriode order by Datum";
    $db->SQL($query);
    $db->Query();
    unset($laatsteKoers);
    $koersRendementen = array();
    while ($data = $db->nextRecord())
    {
      if (isset($laatsteKoers))
      {
        $koersRendementen[] = ($data['koers'] / $laatsteKoers) * 100;
      }
      $laatsteKoers = $data['koers'];
    }
    $sdtev = standard_deviation($koersRendementen);
    if ($beschikbaar['Datum'] == '')
    {
      $stddeviatieJaar = 'na';
    }
    else
    {
      $jaren = substr($periode, 0, 1);
      $stddeviatieJaar = round($sdtev * pow((count($koersRendementen) / $jaren), 0.5), 2);
      //echo "$stddeviatieJaar=round($sdtev*pow((".count($koersRendementen)."/$jaren),0.5),2);<br>\n";
    }
    $koersenHtml.="
    <tr>
      <td>$periode</td>
      <td align=right>$stddeviatieJaar</td>
      <td align=right>" . count($koersRendementen) . "</td>
    </tr>\n";
  }
  $koersenHtml.="</table>\n";
}
else
{
  $list->setWhere(" Datum = '" . getLaatsteValutadatum() . "' ");
  
}

/** find active or inactive fonds * */
$actiefChecked = "checked";
$actief = "actief";
$alleenActief = "(Fondsen.EindDatum  >=  NOW() OR Fondsen.EindDatum = '0000-00-00')";

if ($_GET['actief'] == "inactief")
{
  $inactiefChecked = "checked";
  $actief = "inactief";
  $alleenActief = null;
}




/**
 * setup fonds selection
 */
$autocomplete = new Autocomplete();
$autocomplete->resetVirtualField('Fonds');
$autocompleteField = $autocomplete->addVirtuelField('Fonds', array(
  'autocomplete' => array(
    'table'   => 'Fondsen',
    'searchable' => array(
      'Fonds',
      'ISINCode',
      'Omschrijving'
    ),
//    'query'   => "SELECT * FROM `Fondsen` WHERE (`Fonds` LIKE '%{find}%' OR `ISINCode` LIKE '%{find}%' OR `omschrijving` LIKE '%{find}%')  " . $alleenActief . " ORDER BY `Fonds` ASC",
    'recordLimit' => 50,
    'label'   => array(
      'Fonds',
      'ISINCode'
    ),
    'value'   => 'Fonds',
    'actions' => array(
      'select' => '
        $("#Fonds").val(ui.item.data.Fonds);
       fondsChange();
      '// document.controleForm.submit();
    ),
    'conditions' => array(
      'AND' => array(
        'fondssoort' => $fondsSoortActive,
        $alleenActief
      )
    ),
    'order' => '`Fonds` ASC'
  ),
  'form_extra'   => 'value="' . $Fonds . '"',
  'form_size'    => '30',
));

$content['script_voet'] .= $autocomplete->getAutoCompleteVirtuelFieldScript('Fonds');

if (empty($_GET['sort']))
{
  $_GET['sort'] = array("Datum");
  $_GET['direction'] = array("DESC");
}

// set sort
$list->setOrder($_GET['sort'], $_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'], $allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();

$AETemplate = new AE_template();
$content['jsincludes'] .= $AETemplate->loadJs('query-string');


$content['javascript'] .= "
function addRecord() {
	parent.frames['content'].location = '" . $editScript . "?action=new&Fonds=" . $Fonds . "';
}
";
echo template($__appvar["templateContentHeader"], $content);
?>

<script>
$(function() {
   $('.inoractive').on('change', function () {
    var selected = $('.inoractive:checked').map(function(i,el){return el.value;}).get().join(','); // add 
    var parsed = queryString.parse(location.search);
    parsed.actief = selected;
    location.search = queryString.stringify(parsed);
  });
   
  $('.fondsSoortCheckbox').on('change', function () {
    var selected = $('.fondsSoortCheckbox:checked').map(function(i,el){return el.value;}).get().join(','); // add 
    var parsed = queryString.parse(location.search);
    parsed.fondsSoort = selected;
    location.search = queryString.stringify(parsed);
  });
  

});
    function fondsChange() {
    var selected = $('#Fonds').val();
    var parsed = queryString.parse(location.search);
    parsed.Fonds = selected;
    location.search = queryString.stringify(parsed);
  };
</script> 



<br>
<div id="SelectionHolder" style="height: 110px;">
  <div id="fondsSelectie" style="float: left;">
    <form action="fondskoersenList.php" method="GET"  name="controleForm">
      <div><?=vt("Fonds")?> : <?= $autocompleteField; ?></div>
      <br />

      <input class="inoractive" type="radio" name="actief" id="actief" value="actief" <?= $actiefChecked ?> >
      <label for="actief" title="actief"> <?=vt("Actieve fondsen")?>  </label>

      <input class="inoractive" type="radio" name="actief" id="inactief" value="inactief" <?= $inactiefChecked ?>>
      <label for="inactief" title="actief"> <?=vt("Alle fondsen")?> </label>

      <input type="submit" value="Overzicht">
    </form>
  </div>

  <div id="fondsSoortSelectie" style="float: left; margin-left: 50px; width: 210px;">
    <strong><?=vt("Fondssoorten")?>:</strong><br>
    <table>
      <tr>
        <td style="width:45%;"><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="AAND" <?=(in_array('AAND',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("Aandeel")?></td>
        <td><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="OBL" <?=(in_array('OBL',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("Obligatie")?></td>
      </tr>
      <tr>
        <td><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="OPT" <?=(in_array('OPT',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("Optie")?></td>
        <td><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="STOCKDIV" <?=(in_array('STOCKDIV',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("Stockdividend")?></td>
      </tr>
      <tr>
        <td><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="TURBO" <?=(in_array('TURBO',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("Turbo")?></td>
        <td><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="OVERIG" <?=(in_array('OVERIG',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("Overig")?></td>
      </tr>
      <tr>
        <td><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="INDEX" <?=(in_array('INDEX',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("")?></td>
        <td><input type="checkbox" class="fondsSoortCheckbox" name="fondsSoort[]" value="" <?=(in_array('',$fondsSoortActive)? 'CHECKED':'');?>><?=vt("Leeg")?></td>
      </tr>
    </table>
  </div>
</div>


<br>
<div style="clear: both"><?= $list->filterHeader(); ?></div>
<table>
  <tr>
    <td>
      <table class="list_tabel" cellspacing="0">
        <?= $list->printHeader(); ?>
        <?php
        while ($data = $list->printRow())
        {
          echo $data;
        }
        ?>
      </table>
    </td>
    <td> &nbsp;&nbsp;</td>
    <td valign="top">
    <?= $koersenHtml; ?>
    </td>
  </tr>
</table>
<?
logAccess();
if ($__debug)
{
  echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"], $content);
