<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/21 12:54:50 $
    File Versie         : $Revision: 1.7 $

    $Log: widget_internelinks.php,v $
    Revision 1.7  2018/03/21 12:54:50  cvs
    call 6727

    Revision 1.6  2018/02/21 16:16:10  cvs
    target toegevoegd

    Revision 1.5  2018/02/01 12:42:03  cvs
    update naar airsV2

    Revision 1.4  2017/07/21 13:57:29  cvs
    call 5953



*/

include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = "linksIntern";

$linkarray = array(
  "---" => "",
  "Alle prospects" => "CRM_nawList.php?sql=prospect",
  "Alle relaties" => "CRM_nawList.php",
  "Fondsaanvraag" => "fondsaanvragenEdit.php?action=new",
  "Fondsgegevens" => "fondsList.php",
  "Fondskoersen" => "fondskoersenList.php",
  "Fondskoers aanvragen" => "fondskoersaanvragenList.php?filterNew=1",
  "Frontoffice >> Fondsen" => "rapportFrontofficeFondsSelectie.php",
  "Frontoffice >> Management info" => "rapportFrontofficeManagementSelectie.php",
  "Handleidingen" => "handleidingenairsList.php",
  "Openstaande taken" => "takenList.php?filter=openstaand",
  "Updateinfo" => "updateinformatieList.php",
  "Valutakoersen" => "valutakoersenList.php",

);


$cfg = new AE_config();
$var_data = $USR."_widget_var_".$dialogName."_data";

$data = unserialize($cfg->getData($var_data));
function _getOptions($value)
{
  global $linkarray;

  $out = "";
  foreach($linkarray as $k=>$v)
  {

    $selected = ($v == $value)?"SELECTED":"";
    $out .= "<option value='$v' $selected >$k</option>";
  }
  return $out;
}

echo $tmpl->parseBlock("kop",array("header" => "Interne links", "btnSetup" => "btn_".$dialogName));

?>

<div class="rTable" xmlns="http://www.w3.org/1999/html">

<?
$kopData = array(
  "kol1" => "Ga naar:",


  "tit1" => "Naam van de pagina",
  "btrTit1" => "Naam van de pagina"

);

echo $tmpl->parseBlockFromFile("interneLinks/interneLinks_tableHead.html",$kopData);

for($x=0; $x < 10; $x++)
{
  $row = $data[$x];

  if (trim($row["url"]) == "")
  {
    continue;
  }
    $rowData = array(
      "kol1Class" => "bgWsmoke ac",
      "kol2Class" => "bgFFF borderU ",

      "kol1"      => "<a target='content' href='".$row["url"]."' target='content'><button class='kp100'>".$row["site"]."</button></a>",
      "kol2"      => $row["clicks"],

      "tit1"      => "Naam van de pagina",
      "btrTit1"      => "Naam van de pagina"

    );
    echo $tmpl->parseBlockFromFile("interneLinks/interneLinks_tableRow.html",$rowData);

}


?>
</div> <!-- rTable -->

<!-- Dialoog <?=$dialogName?> -->
<div id="setupWidget_<?=$dialogName?>" title="Instellen interne links" class="setupWidget">
  <div class="rTableRow" >
    <div class="kp50 fl setupColHead" >Url</div>
    <div class="kp50 fl setupColHead" >Naam</div>

  </div>
  <div style="clear: both"></div>
<?
  for ($x=0; $x < 10; $x++)
  {
    $row = $data[$x];
//    debug($row);
//    debug(_getOptions($row["url"]),$row["url"]);
?>
    <div class="kp100 rSetupRow">
      <div class="kp50 fl "><?=$x+1?>:<select class="inpUrl kp90"  id="ILurl<?=$x?>" data-id="<?=$x?>"><?=_getOptions($row["url"]);?>" </select></div>
      <div class="kp50 fl "><input class="inpSite kp100" id="ILsite<?=$x?>" type="text" value="<?=$row["site"]?>" /></div>
    </div>
<?
  }

  ?>

</div>


<script>
  function cl(val, tit)
  {
    if (tit == undefined) tit = "";
    console.log(tit + " :: " + val);
  }
  $(document).ready(function(){
//    var prev_rows = <?//=$rows?>//;

    var linkArray = [];

cl("test");
<?
    foreach ($linkarray as $k=>$v)
    {
      echo "\n\t\tlinkArray.push( {url: '$v', txt: '$k'} );";
    }
?>

    function getLinkTxt(val)
    {
      var index;

      for (index = 0; index < linkArray.length; ++index) {
        if (linkArray[index]["url"] == val)
        {
          return linkArray[index]["txt"]
        }
      }
      return "";
    }


    $(".inpUrl").change(function(){
       var omsId = "ILsite" + $(this).data("id");
       var txt = getLinkTxt($(this).val());
       console.log("val= " + txt);
       console.log("#"+omsId);
       $("#"+omsId).val(txt);
    });

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
          cl("opslaan");
          var s = new Array();
          var u = new Array();
          $( this ).dialog( "close" );
          console.log("opslaan geclicked");

          $('.inpSite').each(function() {
            mId = $(this).attr("id").substring(6);
            s[mId] = $(this).val();
          });
          $('.inpUrl').each(function() {
            mId = $(this).attr("id").substring(5);
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

<?php
