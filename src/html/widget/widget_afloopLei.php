<?php
include_once ("init.php");
include_once ("applicatie_functies.php");
include_once ("../../classes/AE_cls_WidgetsHelper.php");
$fmt = new AE_cls_formatter();

$dialogName = "Lei";

$cfg = new AE_config();
$var_voor    = $USR."_widget_var_".$dialogName."_voor";
$var_columns = $USR."_widget_var_".$dialogName."_colums";
$data_voor   = $cfg->getData($var_voor);

$var_port = $USR."_widget_var_".$dialogName."_port";

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = ("Geen portefeuilleselectie");
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;

if ((int)$data_voor == 0 )
{
  $data_voor = 90;
}

$columnData = array(
    "portefeuille" => array(
      "dbField" => "portefeuille",
      "koptxt"  => vt("Portefeuille"),
      "title"   => vt("Portefeuille"),
      "show"    => (int) $columnSettings["portefeuille"],
      "width"   => "15",
      "fixed"   => 1,
      "class"   => "bgEEE borderU ",
      "format"  => ""
    ),
    "zoekveld" => array(
      "dbField" => "zoekveld",
      "koptxt"  => vt("zoekveld"),
      "title"   => vt("zoekveld"),
      "show"    => (int) $columnSettings["zoekveld"],
      "width"   => "15",
      "fixed"   => 0,
      "class"   => "bgEEE borderU ",
      "format"  => ""
    ),
    "naam" => array(
    "dbField" => "naam",
    "koptxt"  => vt("Naam"),
    "title"   => vt("Naam"),
    "show"    => (int) $columnSettings["naam"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
    ),
    "LEInr" => array(
      "dbField" => "LEInr",
      "koptxt"  => vt("LEI nr"),
      "title"   => vt("LEI nr"),
      "show"    => (int) $columnSettings["LEInr"],
      "width"   => "30",
      "fixed"   => 1,
      "class"   => "bgEEE borderU ",
      "format"  => ""
      ),
    "leiGeldigTot" => array(
      "dbField" => "leiGeldigTot",
      "koptxt"  => vt("Einddatum"),
      "title"   => vt("Einddatum"),
      "show"    => (int)$columnSettings["leiGeldigTot"],
      "width"   => "10",
      "fixed"   => 1,
      "class"   => "bgEEE borderU bold ac ",
      "format"  => "@D{d}-{m}-{Y}"
    ),


);

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);

echo $tmpl->parseBlock("kop",array("header" => vtb("Afloop LEI-nummer (%s)", array($showPort)), "btnSetup" => "btn_".$dialogName));

?>
  <div class="rTable">

  <div class="rTableRow">
<?
    foreach ($widgetHelp->columnData as $k=> $v)
    {
      if ($v["show"] != 1) {continue;}
      echo "<div class='rTableHead' ".$widgetHelp->getWidth($v["width"]) ." title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
    }
?>

  </div>
  <div style="clear: both"></div>

<div class="rTable">

<?
$idx = 0;
$db = new DB();
$dataSet = array();

$query1 = '
SELECT
  CRM_naw.id as rel_id,
  CRM_naw.portefeuille,
  CRM_naw.zoekveld,
  CRM_naw.naam as naam,
  CRM_naw.LEInr as LEInr,
  CRM_naw.LeiGeldigTot as leiGeldigTot,
  Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt
FROM
  CRM_naw
JOIN Portefeuilles ON 
  CRM_naw.portefeuille = Portefeuilles.Portefeuille 
  '.$extraJoin.'
WHERE 
'.$extraWhere.'
  (CRM_naw.LeiGeldigTot < Now() + interval '.$data_voor.' DAY AND
  CRM_naw.LeiGeldigTot <> \'0000-00-00\' ) AND
  (Portefeuilles.Einddatum IS NULL OR Portefeuilles.Einddatum > NOW() )
ORDER BY 
  CRM_naw.LeiGeldigTot  
';

$db->executeQuery($query1);


while ($data = $db->nextRecord())
{
//  debug($data);

  //$data["text"] = str_replace("werd 1 dagen geleden", "werd gisteren", $data["text"]);
  if ($data["id"] != 0)
  {
    $data["checkbox"] .= '<a target="content"  href="CRM_nawEdit.php?action=edit&id='.$data["id"].'" class="btn-new btn-default pull-right"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>';
  }

?>
  <div class="rTableRow">
<?


$item["tit2"] = strip_tags($item["relatie"]);

foreach ($widgetHelp->columnData as $k=> $v)
{
//  debug($v,$k);
  if ($v["show"] != 1) {continue;}
  $negren = "";

  $lnk = '
      <a target="content" href="CRM_nawEdit.php?action=edit&id={relId}" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>
    ';
//    $lnk = '
//      <a href="CRM_nawEdit.php?action=edit&id={relId}&lastTab=9&frameSrc='.base64_encode("CRM_naw_dossierList.php?deb_id=".$data["rel_id"]).'" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>
//    ';
  if ($data["rel_id"] > 0 AND $k == "naam")
  {
    $data["naam"] = str_replace("{relId}", $data["rel_id"], $lnk)." ".$data["naam"];
  }

  if ($v["format"] != "")
  {
    $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
  }
  else
  {
    $value = $data[$v["dbField"]];
  }
  echo "<div title='".$v["title"]."'  class='rTableCell ".$v["class"]." $negren' ".$widgetHelp->getWidth($v["width"]) ."> ".$value."</div>\n";
}


?>
  </div>
  <?
}

?>
</div> <!-- rTable -->

  <!-- Dialoog <?=$dialogName?> -->
  <div id="setupWidget_<?=$dialogName?>" title="<?= vt('Instellen afloop LEI-nummer'); ?>" class="setupWidget">
    <div class="formblock">
      <div class="formlinks"><?= vt('Portefeuilleselectie'); ?> </div>
      <div class="formrechts">
        <select name="port_<?=$dialogName?>" id="port_<?=$dialogName?>">
          <?=$widgetHelp->makeAccessOptions($showPort);?>
        </select>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks"><?= vt('Lei die verlopen binnen'); ?> </div>
      <div class="formrechts">
        <input name="data_voor" id="data_voor" type="number" value="<?=$data_voor?>" style="width: 50px"/> <?= vt('dagen'); ?>
      </div>
    </div>
    <br/>

    <p>
      <?=$widgetHelp->makeHtmlInput()?>
    </p>
  </div>

  <script>
    $(document).ready(function(){
//    var prev_rows = <?//=$rows?>//;

      $("#btn_<?=$dialogName?>").click(function(){
        setup<?=$dialogName?>Dialog.dialog('open');
      });

      var setup<?=$dialogName?>Dialog = $('#setupWidget_<?=$dialogName?>').dialog(
        {
          autoOpen: false,
          height: 400,
          width: '50%',
          modal: true,
          position: {my: "center", at: "top", of: window},
          buttons:
          {
            "<?= vt('Sluiten'); ?>": function()
            {
              $( this ).dialog( "close" );
            },
            "<?= vt('Opslaan'); ?>": function()
            {
              $( this ).dialog( "close" );
              console.log("opslaan geclicked");
              updateCFG("<?=$var_voor?>", $("#data_voor").val());
              updateCFG("<?=$var_port?>", $("#port_<?=$dialogName?>").val() );
              $(".kolCheck<?=$widgetHelp->uid?>").each(function()
              {
                var val = "<?=$var_columns?>#" + $(this).attr("id") + "#" +  ($(this).prop( "checked" )?"1":"0");
                updateCFG("kolom", val);
              });
              reloadPage();
            }
          },
          close: function ()
          {
          }
        });

    });
  </script>
  <?= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));?>
