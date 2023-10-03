<?php
include_once ("init.php");
include_once ("applicatie_functies.php");
include_once ("../../classes/AE_cls_WidgetsHelper.php");
$fmt = new AE_cls_formatter();

$dialogName = "importStatus";

$cfg = new AE_config();
$var_voor    = $USR."_widget_var_".$dialogName."_voor";
$var_columns = $USR."_widget_var_".$dialogName."_colums";
$data_voor   = $cfg->getData($var_voor);

if((int)$data_voor == 0)
{
  $data_voor = 14;
}

if(!isset($columnSettings["datum"]))
{
  $columnSettings["datum"] = 1;
}
if(!isset($columnSettings["depotBank"]))
{
  $columnSettings["depotBank"] = 1;
}

$columnData = array(
      "datum" => array(
        "dbField" => "datum",
        "koptxt"  => vt("datum"),
        "title"   => vt("datum"),
        "show"    => (int) $columnSettings["datum"],
        "width"   => "10",
        "fixed"   => 1,
        "class"   => "bgEEE borderU ",
        "format"  => "@D{d}-{m}-{Y}"
      ),
      "depotBank" => array(
        "dbField" => "depotBank",
        "koptxt"  => vt("bank"),
        "title"   => vt("bank"),
        "show"    => (int)$columnSettings["depotBank"],
        "width"   => "10",
        "fixed"   => 1,
        "class"   => "bgEEE borderU ",
        "format"  => ""
      ),

);

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);

if(!isset($widgetHelp->$columnData["datum"]["show"]))
{
  $widgetHelp->$columnData["datum"]["show"] = 1;
}

if(!isset($widgetHelp->$columnData["depotBank"]["show"]))
{
  $widgetHelp->$columnData["depotBank"]["show"] = 1;
}

echo $tmpl->parseBlock("kop",array("header" => vt("Import status"), "btnSetup" => "btn_".$dialogName));
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
SELECT DISTINCT datum, 
depotBank 
FROM MONITOR_importMatrix 
WHERE verwerkt <> 1
AND datum <> \'0000-00-00\'
AND datum > \'2021-01-01\'
AND datum >= now() - interval '.(int)$data_voor.' day
order by datum desc, depotBank asc
limit 500
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
  <div id="setupWidget_<?=$dialogName?>" title="<?= vt('Instellen import status'); ?>" class="setupWidget">

    <div class="formblock">
      <div class="formlinks"><?= vt('Hoeveel dagen tonen?'); ?> </div>
      <div class="formrechts">
        <input name="data_voor" id="data_voor" type="number" value="<?=$data_voor?>" style="width: 50px"/> <?= vt('aantal dagen'); ?>
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
