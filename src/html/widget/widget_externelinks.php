<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = "linksExt";

$cfg = new AE_config();
$var_data = $USR."_widget_var_".$dialogName."_data";

$data = unserialize($cfg->getData($var_data));

echo $tmpl->parseBlock("kop",array("header" => vt("Externe links"), "btnSetup" => "btn_".$dialogName));

?>

<div class="rTable">

<?
$kopData = array(
  "kol1" => vt("Website"),

  "tit1" => vt("Naam van de site"),
  "btrTit1" => "Naam van de site"
);

echo $tmpl->parseBlockFromFile("externeLinks/externeLinks_tableHead.html",$kopData);

for($x=0; $x < 10; $x++)
{
  $row = $data[$x];
  $title = $kopData['tit1'];
  if (trim($row["url"]) == "")
  {
    continue;
  }
    $rowData = array(
      "kol1Class" => "bgWsmoke ac",
      "title"     => $title,
      "btrTit1"   => $kopData['btrTit1'],
      "kol2Class" => "bgFFF borderU ",

      "kol1"      => "<a href='".$row["url"]."' target='_blank'><button class='kp100'>".$row["site"]."</button></a>",
      "kol2"      => $row["clicks"],

    );
    echo $tmpl->parseBlockFromFile("externeLinks/externeLinks_tableRow.html",$rowData);

}


?>
</div> <!-- rTable -->

<!-- Dialoog <?=$dialogName?> -->
<div id="setupWidget_<?=$dialogName?>" title="Instellen externe links" class="setupWidget">
  <div class="rTableRow" >
    <div class="kp50 fl setupColHead" ><?= vt('Url'); ?></div>
    <div class="kp50 fl setupColHead" ><?= vt('Website'); ?></div>

  </div>
  <div style="clear: both"></div>
<?
  for ($x=0; $x < 10; $x++)
  {
    $row = $data[$x];
?>
    <div class="kp100 rSetupRow">
      <div class="kp50 fl "><?=$x+1?>:<input class="inpUrl kp90"  id="url<?=$x?>"  type="text" value="<?=$row["url"]?>" /></div>
      <div class="kp50 fl "><input class="inpSite kp100" id="site<?=$x?>" type="text" value="<?=$row["site"]?>" /></div>
    </div>
<?
  }

  ?>

</div>

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
      height: 500,
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
          var s = new Array();
          var u = new Array();
          $( this ).dialog( "close" );
          console.log("opslaan geclicked");

          $('.inpSite').each(function() {
            mId = $(this).attr("id").substring(4);
            s[mId] = $(this).val();
          });
          $('.inpUrl').each(function() {
            mId = $(this).attr("id").substring(3);
            u[mId] = $(this).val();
          });

          var dta = "";

          for(i=0; i < s.length; i++)
          {
            console.log(s[i] + "|" + u[i] + "###");
            dta = dta +  s[i] + "|" + u[i] + "###";
          }
          updateCFG("<?=$var_data?>", dta);
          reloadPage();

//          var rows = $("#showedRows_<?//=$dialogName?>//").val();
//          if (prev_rows != rows && rows > 0)
//          {
//            updateCFG("<?//=$var_rows?>//", rows);
//            console.log("varname: <?//=$var_rows?>//");
//            reloadPage();
//
//          }
        }
      },
      close: function ()
      {
      }
    });




  });
</script>

<?= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));?>