<?php
include_once ("init.php");
$fmt = new AE_cls_formatter();
$geenConfig = false;

$dialogName = "taken";

//$cache = new AE_cls_WidgetsCaching($dialogName, 60);
//
//if ($cache->useCache())
//{
//  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
//  echo $cont;
//  exit;
//}

$cfg = new AE_config();
$var_rows = $USR."_widget_var_".$dialogName."_rows";  //BTR: Fix rows data storage
$var_dagen = $USR."_widget_var_".$dialogName."_dagen";
$var_eigen = $USR."_widget_var_".$dialogName."_eigen";
$rows = (int) $cfg->getData($var_rows);
$eigen = $cfg->getData($var_eigen);
if ($rows == 0)
{
  $rows = 25;
  $geenConfig = true;
}
$data_dagen = (int) $cfg->getData($var_dagen);

$julTm = mktime(23,59,00,date("m"),date("d"),date("Y")) + (86400 * $data_dagen);

$tmSql = date("Y-m-d H:i:s",$julTm);

$wFilt = new AE_cls_WidgetsFilter();
//$wFilt->getCRMaccess(" AND ");



//$out = $tmpl->parseBlock("kop",array("header" => "Mijn takenlijst (top $rows) <!--ttl-->", "btnSetup" => "btnTakenConfig"));

$parse = array(
  "header" => vtb("Mijn takenlijst (top %s)", array($rows)),
  "btnSetup" => "btn_".$dialogName,
  );
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);
  $out .= '
<div class="rTable">
';

$kopData = array(
  "kol1" => vt("Relatie"),
  "kol2" => vt("Betreft"),
  "kol3" => vt("Soort"),
  "kol4" => vt("Zichtbaar na"),
  "kol5" => vt("Wie"),
  "kol6" => vt("Add"),

  "tit1" => vt("Relatie"),
  "tit2" => vt("Betreft"),
  "tit3" => vt("Soort"),
  "tit4" => vt("Zichtbaar na"),
  "tit5" => vt("Wie"),
  "tit6" => vt("Add user"),

);
$out .=  $tmpl->parseBlockFromFile("taken/taken_tableHeadf.html",$kopData);
include_once("../classes/mysqlList.php");

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $rows;//$__appvar['rowsPerPage'];
$list->addColumn("Taken","id",array("list_width"=>"100","search"=>false));
$list->addColumn("","relatie",array("list_order"=>true,"search"=>true,"sql_alias"=>"if(ISNULL(CRM_naw.zoekveld),taken.relatie,if(CRM_naw.zoekveld='',taken.relatie,CRM_naw.zoekveld))","list_width"=>"","search"=>true));
$list->addColumn("Taken","rel_id",array("list_width"=>"","search"=>true,"list_invisible"=>true));
$list->addColumn("Taken","kop",array("list_width"=>"30%","search"=>true));
$list->addColumn("Taken","soort",array("list_width"=>"100","search"=>true,"list_align"=>"center"));
$list->addColumn("Taken","zichtbaar",array("list_width"=>""));
$list->addColumn("Taken","gebruiker",array("list_width"=>"","search"=>true,"list_align"=>"center","description"=>"Wie"));
$list->addColumn("naw","zoekveld",array("list_width"=>"100","search"=>true,"list_align"=>"center","list_invisible"=>true));
$list->addColumn("Taken","spoed",array("list_width"=>"100","search"=>true,"list_align"=>"center","list_invisible"=>true));
$list->addColumn("Taken","rel_id",array("list_width"=>"100","search"=>true,"list_align"=>"center","list_invisible"=>true));
$list->addColumn("Taken","relatie",array("list_width"=>"20%","search"=>true,"list_align"=>"center","list_invisible"=>true));
$list->addColumn("Taken","add_user",array("list_width"=>"50","search"=>true,"list_align"=>"center"));
$list->forceFrom=" FROM taken ";
$list->setJoin("LEFT JOIN CRM_naw ON taken.rel_id=CRM_naw.id");
if ( $__appvar["bedrijf"] == "HOME" AND $eigen == "alle")
{
  $list->setWhere("afgewerkt = 0 AND zichtbaar <= '$tmSql' ");
}
else
{
  $list->setWhere("gebruiker='$USR' AND afgewerkt = 0 AND zichtbaar <= '$tmSql' ");
}




// set default sort
$_GET['sort'][]      = "taken.zichtbaar";
$_GET['direction'][] = "ASC";
$_GET['sort'][]      = "taken.relatie";
$_GET['direction'][] = "ASC";
// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);
//debug($list->getSQL());

if ($geenConfig)
{
  $out .= "Configureer eerst deze widget.";
}
else if($list->records() > 0)
{
  $DB = new DB();
  $DB->SQL("SELECT gebruiker,bgkleur FROM Gebruikers");
  $DB->Query();
  while ($usrData = $DB->nextRecord())
  {
    $usrColors[$usrData['gebruiker']] = $usrData['bgkleur'];
  }
  $dayOfTheYear = widJulDag(mktime());
  while($data = $list->getRow())
  {
//    debug($data);
    if ($data["zichtbaar"]["value"] == "0000-00-00")
    {
      $datum = vt("niet opgegeven");
    }
    else
    {
      $d = $data["zichtbaar"]["value"];
      if (substr($d,0,4) < date("Y"))
      {
        $alert = "red";
      }
      else
      {

        $p = explode("-", $d);
        $d = widJulDag(mktime(0,0,0,$p[1],$p[2],$p[0]) );

        if ($d >= $dayOfTheYear+3)
        {
          $alert = "green";
        }
        elseif ($d+1 >= $dayOfTheYear)
        {
          $alert = "orange";
        }
        else
        {
          $alert = "red";
        }
      }
      $datum = '<i class="fa fa-check-square" style="color:'.$alert.'" aria-hidden="true"></i> '.$fmt->format("@D{form}", $data["zichtbaar"]["value"]);
    }

    $spoed = ($data['spoed']['value'] <> 0)?"spoed":"";

    if ($wFilt->portefeuilleAccessAllowed($data["rel_id"]["value"]))
    {
//      $link = '<a href="CRM_nawEdit.php?action=edit&id='.$data["rel_id"]["value"].'&taakId='.$data["id"]["value"].'&frame=1&returnUrl=welcome.php&toHome=1"><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a>';
      $link = '<a target="content" href="CRM_nawEdit.php?action=edit&id='.$data["rel_id"]["value"].'&taakId='.$data["id"]["value"].'&frame=1&returnUrl=welcome.php&toHome=1" class="btn-new btn-default"><button><i class="fa fa-folder-open" aria-hidden="true"></i></button></a>';
    }
    else
    {
      $link = '<img src="images//16/leeg.gif" width="16" height="16" border="0" title="geen rechten voor deze relatie" align="absmiddle">&nbsp;';
    }
//debug ($data);
    $rowData = array(
      "kol1Class" => "bgEEE borderU ".$spoed,
      "kol2Class" => "bgFFF borderU ".$spoed,
      "kol3Class" => "bgEEE borderU ".$spoed,
      "kol4Class" => "bgFFF borderU",
      "kol5Class" => "bgEEE borderU",
      "kol6Class" => "bgEEE borderU",
      //"kol5Style" => "color: Whitesmoke; background-color:#".$usrColors[$data['gebruiker']['value']]."; ",
      "kol1"      => $link."  ".$data["relatie"]["value"],
      "kol2"      => $data["kop"]["value"],
      "kol3"      => $data["soort"]["value"],
      "kol4"      => $datum,
      "kol5"      => $data["gebruiker"]['value'],
      "kol6"      => $data["add_user"]['value'],

      "tit1" => "Relatie",
      "tit2" => "Betreft",
      "tit3" => "Soort",
      "tit4" => "Zichtbaar na",
      "tit5" => "Wie",
      "tit6" => "Add user",
    );

    $out .= $tmpl->parseBlockFromFile("taken/taken_tableRow.html",$rowData);

  }


}
else
{
  $out .= "<div>Geen taken gevonden</div>";
}

$out .= '
<!-- Dialoog '.$dialogName.' -->
</div> <!-- rTable -->
  <div id="setupTaken" title="' . vt('Instellen taken') . '" class="setupWidget">
    <div class="formblock">
      <div class="formlinks">' . vt('Maximaal aantal taken') . ' </div>
      <div class="formrechts">
        <input name="takenRows" id="takenRows" type="number" value="'.$rows.'" style="width: 50px"/>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">' . vt('Zichtbaar na t/m') . ' </div>
      <div class="formrechts">
        <input name="data_dagen" id="data_dagen" type="number" value="'.(int) $data_dagen.'" style="width: 50px"/> ' . vt('dagen in de toekomst') . '
      </div>
    </div>
';


    if ( $__appvar["bedrijf"] == "HOME" )
    {
      $out .= '
      <div class="formblock">
        <div class="formlinks">' . vt('HOME instelling: alle/eigen') . ' </div>
        <div class="formrechts">

          <select name="'.$var_eigen.'" id="'.$var_eigen.'">
            <option value="eigen" >' . vt('eigen') . '</option>
            <option value="alle" '.(($eigen == "alle")?"SELECTED":"").' >' . vt('alle') . '</option>
          </select>
        </div>
      </div>
';
   }
$out .= '

  </div>

</div>

<script>
  $(document).ready(function(){
    var prev_rows = '.$rows.';

    

    $("#btn_'. $dialogName.'").click(function(){
    console.log("config Clicked");
      setupTaken.dialog("open");
    });

    var setupTaken = $("#setupTaken").dialog(
    {
      autoOpen: false,
      height: 250,
      width: "50%",
      modal: true,
      position: {my: "center", at: "top", of: window},
      buttons:
      {
        "' . vt('Sluiten') . '": function()
        {
          $( this ).dialog( "close" );
        },
        "' . vt('Opslaan') . '": function()
        {
          $( this ).dialog( "close" );
          var rows = $("#takenRows").val();

          updateCFG("'.$var_rows.'", rows);
          updateCFG("'.$var_dagen.'", $("#data_dagen").val());
          updateCFG("'.$var_eigen.'", $("#'.$var_eigen.'").val());
          reloadPage();

        }
      },
      close: function ()
      {
      }
    });

  });
</script>
';

$out .= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));

//$cache->addToCache($out);
echo $out;


function widJulDag($datum=0)
{
  if (!is_numeric($datum) or $datum==0)  $datum = time();
  return floor($datum/86400);
}