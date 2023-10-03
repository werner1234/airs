<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2019/10/14 07:59:59 $
    File Versie         : $Revision: 1.12 $

    $Log: widget_gespreksverslag.php,v $
    Revision 1.12  2019/10/14 07:59:59  cvs
    call 8075

    Revision 1.11  2019/10/11 08:57:59  cvs
    call 8075

    Revision 1.10  2018/09/28 12:53:14  cvs
    widget caching

    Revision 1.9  2018/02/21 16:16:10  cvs
    target toegevoegd

    Revision 1.8  2018/02/09 10:51:46  cvs
    call 6572

    Revision 1.7  2018/02/01 12:42:03  cvs
    update naar airsV2

    Revision 1.6  2017/12/15 13:53:48  cvs
    call 6365

    Revision 1.5  2017/11/15 14:19:17  cvs
    call 6320

    Revision 1.4  2017/11/15 14:05:13  cvs
    no message

    Revision 1.3  2017/11/10 13:49:26  cvs
    call 6320

    Revision 1.2  2017/10/20 11:41:38  cvs
    no message

    Revision 1.1  2017/09/27 15:40:48  cvs
    call 6159



*/

include_once ("init.php");

$fmt = new AE_cls_formatter();
$db = new DB();


$dialogName = "gesprekvs";

$cache = new AE_cls_WidgetsCaching($dialogName, 60);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$cfg = new AE_config();
$var_debet = $USR."_widget_var_".$dialogName."_debet";
$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";
$var_columns = $USR."_widget_var_".$dialogName."_colums";


//$wFilt->showSettings();

$result = array();

include_once 'portefeuilleToegang.php';

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = ("Geen portefeuilleselectie");
}

$debetDate = _julDag($cfg->getData($var_rows));
$debetBedrag = (float)$cfg->getData($var_debet);

$wFilt = new AE_cls_WidgetsFilter($showPort);




$rows = (int) $cfg->getData($var_rows);
if ($rows == 0) $rows = 10;


$columnDataVermogen = array(
  "datum" => array(
    "dbField" => "datum",
    "koptxt"  => vt("Datum"),
    "title"   => vt("Datum"),
    "show"    => (int) $columnSettings["datum"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ar",
    "format"  => "@D{form}"
  ),
  "portefeuille" => array(
    "dbField" => "portefeuille",
    "koptxt"  => vt("Portefeuille"),
    "title"   => vt("Portefeuille"),
    "show"    => (int) $columnSettings["portefeuille"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "naam" => array(
    "dbField" => "naam",
    "koptxt"  => vt("Client"),
    "title"   => vt("Client"),
    "show"    => (int) $columnSettings["naam"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),

  "kop" => array(
    "dbField" => "kop",
    "koptxt"  => vt("Onderwerp"),
    "title"   => vt("Onderwerp"),
    "show"    => (int) $columnSettings["kop"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ",
    "format"  => ""
  ),
  "accountmanager" => array(
    "dbField" => "Accountmanager",
    "koptxt"  => vt("Accountmanager"),
    "title"   => vt("Accountmanager"),
    "show"    => (int) $columnSettings["accountmanager"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "tweedeAanspreekpunt" => array(
    "dbField" => "tweedeAanspreekpunt",
    "koptxt"  => vt("Tweede aanspreekpunt"),
    "title"   => vt("Tweede aanspreekpunt"),
    "show"    => (int) $columnSettings["tweedeAanspreekpunt"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "internDepot" => array(
    "dbField" => "InternDepot",
    "koptxt"  => vt("I.Depot"),
    "title"   => vt("Intern depot"),
    "show"    => (int) $columnSettings["internDepot"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => "@checkbox"
  ),


);



$widgetHelpVermogen = new AE_cls_WidgetsHelper($columnDataVermogen, $var_columns);

$wFilt->getPortefeuilleAccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


$query = "
SELECT
  UNIX_TIMESTAMP(datum) AS sortdate,
	datum,
	kop,
	CRM_naw.id as rel_id,
	CRM_naw.naam,
	CRM_naw.portefeuille,
	Portefeuilles.Accountmanager,
	Portefeuilles.InternDepot,
  CRM_naw_dossier.id as gId,
	Portefeuilles.tweedeAanspreekpunt
FROM
	CRM_naw_dossier
LEFT JOIN CRM_naw ON 
  CRM_naw.id = CRM_naw_dossier.rel_id
LEFT JOIN Portefeuilles ON
  CRM_naw.portefeuille = Portefeuilles.Portefeuille  
  $extraJoin
WHERE
	$extraWhere 
	1
ORDER BY
  CRM_naw_dossier.datum DESC
LIMIT 0, ".$rows;

//debug($query);

$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $result[] = $rec;
}

$wFilt->getCRMaccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;

$query = "
SELECT
  UNIX_TIMESTAMP(datum) AS sortdate,
	datum,
	kop,
	CRM_naw.id as rel_id,
	CRM_naw.naam,
	CRM_naw.portefeuille,
	Portefeuilles.Accountmanager,
	Portefeuilles.InternDepot,
  CRM_naw_dossier.id as gId,
	Portefeuilles.tweedeAanspreekpunt
FROM
	CRM_naw_dossier
LEFT JOIN CRM_naw ON
  CRM_naw.id = CRM_naw_dossier.rel_id
LEFT JOIN Portefeuilles ON
  CRM_naw.portefeuille = Portefeuilles.Portefeuille
  $extraJoin
WHERE
	$extraWhere AND
	(CRM_naw.aktief = 1 OR ISNULL(CRM_naw.aktief) )
ORDER BY
  CRM_naw_dossier.datum DESC
LIMIT 0, ".$rows;


$db->executeQuery($query);
while ($rec = $db->nextRecord())
{

  $result[] = $rec;
}

rsort($result);
$result = array_slice($result, 0, $rows);
//echo $tmpl->parseBlock("kop",array("header" => "Top $rows, Recente gespreksverslagen ($showPort)", "btnSetup" => "btn_".$dialogName));
//debug($query);

$parse = array(
  "header" => vtb("Top %s, Recente gespreksverslagen (%s) ", array($rows, $showPort)),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache());
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);

$out .='

<div class="rTable">


<div class="rTableRow">
    ';
    foreach ($widgetHelpVermogen->columnData as $k=> $v)
    {
      if ($v["show"] != 1) {continue;}
      $out .= "<div class='rTableHead' ".$widgetHelpVermogen->getWidth($v["width"]) ." title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
    }
    $out .= '
</div>
  <div style="clear: both"></div>
';
    
if ( $showPort == "Geen portefeuilleselectie")
{
  $out .= vt("Configureer eerst deze widget.");
}
else if (count($result) == 0)
{
  $out .= vt("Geen items gevonden");
}
else
{
  foreach ($result as $data)
  {

    $linkBlok = "";
//    $lnk = '
//      <a target="content" href="CRM_nawEdit.php?action=edit&id={relId}" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>
//    ';
    //<a href="CRM_nawEdit.php?action=edit&id={relId}&lastTab=9&frameSrc='.base64_encode("CRM_naw_dossierList.php?deb_id=".$data["rel_id"]).'" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>
    $lnk = '
      <a target="content" href="CRM_nawEdit.php?action=edit&id={relId}&lastTab=9&frameSrc='.base64_encode("CRM_naw_dossierEdit.php?action=edit&id=".$data["gId"]."&toList=1&rel_id=".$data["rel_id"]).'" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>
    ';



    if ($data["rel_id"] > 0)
    {
      $data["naam"] = str_replace("{relId}", $data["rel_id"], $lnk)." ".$data["naam"];
    }

    $out .= "
    
    <div class='rTableRow'>
    ";

    foreach ($widgetHelpVermogen->columnData as $k=> $v)
    {
      if ($v["show"] != 1) {continue;}
      $negren = "";
      if ($v["dbField"] == "rendement")
      {
        $negren = $data["rendement"] <= 0?"negatief":"";
      }
      if ($v["format"] != "")
      {
        $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
      }
      else
      {
        $value = $data[$v["dbField"]];
      }
      $out .=  "<div title='". $v['title'] ."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelpVermogen->getWidth($v["width"]) ."> ".$value."</div>\n";
    }

    $out .= '
    </div>
    ';

  }

}

$selected = ($portValue == "alle")?"SELECTED":"";

$out .= '
</div> <!-- rTable -->

<!-- Dialoog '.$dialogName.' -->
<div id="setupWidget_'.$dialogName.'" title="Instellen recente gespreksverslagen" class="setupWidget">
  <div class="formblock">
    <div class="formlinks">' . vt('Portefeuilleselectie') . ' </div>
    <div class="formrechts">
      <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
        '.$widgetHelpVermogen->makeAccessOptions($showPort).'
      </select>
    </div>
  </div>
  <div class="formblock">
    <div class="formlinks">' . vt('Aantal getoonde regels:') . ' </div>
    <div class="formrechts">
      <input name="showedRows_'.$dialogName.'" id="showedRows_'.$dialogName.'" type="number" value="'.$rows.'" style="width: 50px"/>
    </div>
  </div>
  <p><br/></p>
  <p>
    '.$widgetHelpVermogen->makeHtmlInput().'
  </p>


</div>

</div>
<script>
  $(document).ready(function(){
    var prev_rows = '.$rows.';

    '.$cache->JSinit().'

    $("#btn_'.$dialogName.'").click(function(){
      setup'.$dialogName.'Dialog.dialog("open");
    });

    var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
    {
      autoOpen: false,
      height: 450,
      width: "50%",
      modal: true,
      position: {my: "center", at: "top", of: window},
      buttons:
      {
        "Sluiten": function()
        {
          $( this ).dialog( "close" );
        },
        "Opslaan": function()
        {
          $( this ).dialog( "close" );
          var rows = $("#showedRows_'.$dialogName.'").val();
          updateCFG("'.$var_rows.'", rows);
          updateCFG("'.$var_port.'", $("#port_'.$dialogName.'").val() );
          $(".kolCheck'.$widgetHelpVermogen->uid.'").each(function()
          {
            var val = "'.$var_columns.'#" + $(this).attr("id") + "#" +  ($(this).prop( "checked" )?"1":"0");
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
';
$out .= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));

$cache->addToCache($out);
echo $out;


// lokale functies
function _julDag($dbDatum)
{
  $parts = explode("-",$dbDatum);
  $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
  return floor($julian / 86400);
}

