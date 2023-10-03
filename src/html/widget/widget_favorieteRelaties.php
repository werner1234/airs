<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = "favRelaties";

$cfg = new AE_config();
$var_data = $USR."_widget_var_".$dialogName."_data";
$var_columns = $USR."_widget_var_".$dialogName."_colums";

$result = unserialize($cfg->getData($var_data));


$ajx = new AE_cls_ajaxLookup("portefeuille");
$ajx->changeModuleTriggerID("portefeuille","Portefeuille");



$columnDataFav = array(
  "portefeuille" => array(
    "dbField" => "port",
    "koptxt"  => "Portefeuille",
    "title"   => "Portefeuille",
    "show"    => (int) $columnSettings["portefeuille"],
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "client" => array(
    "dbField" => "naam",
    "koptxt"  => "Client",
    "title"   => "Client",
    "show"    => (int) $columnSettings["client"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "emailLink" => array(
    "dbField" => "emaillink",
    "koptxt"  => "",
    "title"   => "E-mail link",
    "show"    => (int) $columnSettings["emaillink"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ac",
    "format"  => ""
  ),
  "email" => array(
    "dbField" => "email",
    "koptxt"  => "E-mail",
    "title"   => "E-mail",
    "show"    => (int) $columnSettings["email"],
    "width"   => "20",
    "fixed"   => 0,
    "class"   => "bgEEE borderU bold ",
    "format"  => ""
  ),
);


$widgetHelpVermogen = new AE_cls_WidgetsHelper($columnDataFav, $var_columns);






echo $tmpl->parseBlock("kop",array("header" => vt("Favoriete clienten"), "btnSetup" => "btn_".$dialogName));
?>
  <div class="rTable">

    <div class="rTableRow">
      <?
      foreach ($widgetHelpVermogen->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        echo "<div class='rTableHead' ".$widgetHelpVermogen->getWidth($v["width"]) ." btr-title='".$v["title"]."' title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
      }
      ?>
    </div>
    <div style="clear: both"></div>
<?
$lnk = '<a target="content" href="HTMLrapport/dashboard.php?port={port}" title="dashboard van '.$row["naam"].'" class="btn-new btn-default pull-left"><button><i class="fa fa-book" aria-hidden="true"></i></button></a>
        <a target="content" href="CRM_nawEdit.php?action=edit&id={relId}" title="CRM kaart van '.$row["naam"].'" class="btn-new btn-default pull-left"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>';
foreach ($result as $data)
{

  if (trim($data["port"]) == "")
  {
     continue; // lege items niet tonen..
  }
  ?>
  <div class="rTableRow">
    <?
    foreach ($widgetHelpVermogen->columnData as $k=> $v)
    {
      if ($v["show"] != 1) {continue;}
      $negren = "";
      if ($v["dbField"] == "port")
      {

        $linkBlok = str_replace("{port}", $data["port"], $lnk);
        $linkBlok = str_replace("{relId}", $data["relId"], $linkBlok);

        $data[$v["dbField"]] = $linkBlok." &nbsp;".$data["port"];
      }


      if ($v["format"] != "")
      {
        $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
      }
      else
      {
        $value = $data[$v["dbField"]];
      }

      if ($v["dbField"] == "emaillink" AND trim($data["email"]) != "" )
      {

        $value = '<a target="content" href="mailto:'.$data["email"].'" title="' . vt('maak een mailbericht aan') . '" class="btn-new btn-default ">
                  <button><i class="fa fa-at" aria-hidden="true"></i></button></a> ';
      }
      echo "<div title='".$v['title']."' btr-title='".$v["title"]."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelpVermogen->getWidth($v["width"]) ."> ".$value."</div>\n";
    }
    ?>
  </div>
  <?
}



?>
</div> <!-- rTable -->

<!-- Dialoog <?=$dialogName?> -->
<div id="setupWidget_<?=$dialogName?>" title="Instellen favoriete clienten" class="setupWidget">
  <div class="rTableRow" >
    <div class="kp30 fl setupColHead" ><?= vt('Portefeuille'); ?></div>
<!--    <div class="kp05 fl setupColHead" >volgorde</div>-->
    <div class="kp70 fl setupColHead" ><?= vt('Relatie'); ?></div>

  </div>
  <div style="clear: both"></div>
<?
  for ($x=0; $x < 10; $x++)
  {
    $row = $result[$x];
?>
    <div class="kp100 rSetupRow">
      <div class="kp30 fl "><?=$x+1?>:<input class="inpPort kp70" data-id="<?=$x?>" id="port<?=$x?>"  type="text" value="<?=$row["port"]?>" /></div>
      <div class="kp05 fl "><input class="inpPrio kp100" id="pio<?=$x?>" type="hidden" value="<?=(int)$row["prio"]?>" /></div>
      <div class="kp70 fl "><input class="kp100" id="naam<?=$x?>" type="text" value="<?=$row["naam"]?>"  READONLY/>
        <input id="relId<?=$x?>" type="hidden" value="<?=$row["relId"]?>"  />
        <input id="email<?=$x?>" type="hidden" value="<?=$row["email"]?>"  />
      </div>
    </div>
<?
  }

  ?>
  <p><br/></p>
  <p><br/></p>

  <p>
    <?=$widgetHelpVermogen->makeHtmlInput()?>
  </p>

</div>

</div>

<div id="popupFR"></div>

<script>
  $(document).ready(function(){
//    var prev_rows = <?//=$rows?>//;


    $("#btn_<?=$dialogName?>").click(function(){
      setup<?=$dialogName?>Dialog.dialog('open');
    });
    $(document).delegate(".inpPort", "focus", function() {

      $(this).autocomplete(
      {
        source: "lookups/getClientForWidgets.php",           // link naar lookup script
        change: function(e, ui)
        {
          if (!ui.item)
          {
            mId = $(this).attr("id").substring(4);
//            $( "#popupFR" ).dialog("open");
            $(this).val("");                                  // reset waarde als niet uit de lookup
            $("#naam"+mId).val("");
            $("#relId"+mId).val("");
            $("#email"+mId).val("");
          }
        },
        select: function(event, ui)                           // bij selectie clientside vars updaten
        {

          $(this).val(ui.item.portefeuille);
          mId = $(this).attr("id").substring(4);
          $("#naam"+mId).val(ui.item.naam);
          $("#relId"+mId).val(ui.item.relId);
          $("#email"+mId).val(ui.item.email);
          console.log($("#email"+mId).val());
        },
        open: function()
        {
          $(".ui-autocomplete").css("width", "500px");
        },
        minLength: 2,                                         // pas na de tweede letter starten met zoeken
        delay: 0,
        autoFocus: true
      });
    })

    var setup<?=$dialogName?>Dialog = $('#setupWidget_<?=$dialogName?>').dialog(
    {
      autoOpen: false,
      height: 500,
      width: '50%',
      modal: true,
      position: {my: "center", at: "top", of: window},
      buttons:
      {
        "<?=vt('Sluiten');?>": function()
        {
          $( this ).dialog( "close" );
        },
        "<?=vt('Opslaan');?>": function()
        {
          var port = new Array();
          var prio = new Array();
          var naam = new Array();
          var relId = new Array();
          var email = new Array();
          $( this ).dialog( "close" );
          $(".kolCheck<?=$widgetHelpVermogen->uid?>").each(function()
          {
            var val = "<?=$var_columns?>#" + $(this).attr("id") + "#" +  ($(this).prop( "checked" )?"1":"0");
            updateCFG("kolom", val);
          });
          console.log("opslaan geclicked");

          $('.inpPort').each(function() {
            mId = $(this).attr("id").substring(4);
            port[mId] = $(this).val();
            naam[mId] = $("#naam"+mId).val();
            relId[mId] = $("#relId"+mId).val();
            email[mId] = $("#email"+mId).val();
            console.log(mId + " = " + $(this).val() + " / " + $("#naam"+mId).val() + " / " + $("#relId"+mId).val() + " / " + $("#email"+mId).val());
          });
          $('.inpPrio').each(function() {
            mId = $(this).attr("id").substring(3);
            prio[mId] = $(this).val();
          });

          var dta = "";

          for(i=0; i < port.length; i++)
          {
            console.log(port[i] + "|" + prio[i] + "|" + naam[i] + "|" + relId[i] + "|" + email[i] +"###");
            dta = dta +  port[i] + "|" + prio[i] + "|" + naam[i] + "|" + relId[i] + "|" + email[i] +"###";
          }
          console.log(dta);
          updateCFG("<?=$var_data?>", dta);
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