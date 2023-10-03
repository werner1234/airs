<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/28 12:53:14 $
    File Versie         : $Revision: 1.8 $

    $Log: widget_reviewdatum.php,v $
    Revision 1.8  2018/09/28 12:53:14  cvs
    widget caching

    Revision 1.7  2018/05/14 14:45:02  cvs
    call 6079

    Revision 1.6  2018/02/21 16:16:10  cvs
    target toegevoegd

    Revision 1.5  2018/02/09 10:51:46  cvs
    call 6572

    Revision 1.4  2018/02/01 12:42:03  cvs
    update naar airsV2

    Revision 1.3  2017/12/15 13:53:48  cvs
    call 6365

    Revision 1.2  2017/11/10 13:49:26  cvs
    call 6320

    Revision 1.1  2017/09/27 15:03:52  cvs
    call 6159



*/


include_once ("init.php");
$fmt = new AE_cls_formatter();
$cfg = new AE_config();
$db  = new DB();
$row = 0;
$dialogName = "reviewdatum";

$cache = new AE_cls_WidgetsCaching($dialogName, 180);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$cfg = new AE_config();
$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_dagen = $USR."_widget_var_".$dialogName."_dagen";
$var_port = $USR."_widget_var_".$dialogName."_port";
$var_columns = $USR."_widget_var_".$dialogName."_colums";

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = "Geen portefeuilleselectie";
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getCombiAccess();

$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


$dagen = $cfg->getData($var_dagen);

$rows = (int) $cfg->getData($var_rows);
if ($rows == 0) $rows = 10;


$columnData = array(
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
  "CRMReviewDatum" => array(
    "dbField" => "CRMReviewDatum",
    "koptxt"  => vt("Datum"),
    "title"   => vt("CRM review datum"),
    "show"    => (int) $columnSettings["CRMReviewDatum"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ",
    "format"  => "@D{form}"
  ),
  "Portefeuille " => array(
    "dbField" => "Portefeuille",
    "koptxt"  => vt("Portefeuille"),
    "title"   => vt("Portefeuille"),
    "show"    => (int) $columnSettings["Portefeuille"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
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
  "accountEigenaar" => array(
    "dbField" => "accountEigenaar",
    "koptxt"  => vt("Account eigenaar"),
    "title"   => vt("Account eigenaar"),
    "show"    => (int) $columnSettings["accountEigenaar"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "tweedeAanspreekpunt" => array(
    "dbField" => "tweedeAanspreekpunt",
    "koptxt"  => vt("Tweede aanspreekpunt"),
    "title"   => vt("Tweede aanspreekpunt"),
    "show"    => (int)$columnSettings["tweedeAanspreekpunt"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),


);

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);


$julTm = mktime(23,59,00,date("m"),date("d"),date("Y")) + (86400 * $dagen);

$tmSql = date("Y-m-d H:i:s",$julTm);

$parse = array(
  "btnExport" => "<button class='btn-new btn-default pull-right fa fa-file-excel-o headSetup' id='btnExport_{$dialogName}' aria-hidden='true' title='Exporteer'></button>",
  "header" => vtb("Reviewdatums (%s) ", array(vt($showPort))),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache());
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);
$out .= '
   <div class="rTable">

    <div class="rTableRow">
    ';

$row++;
foreach ($widgetHelp->columnData as $k=>$v)
{
  if ($v["show"] != 1) {continue;}
  $out .= "<div class='rTableHead' ".$widgetHelp->getWidth($v["width"]) ." title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";

  $widgetExportData[$row][$k] = $v;
}

$out .= '

    </div>
    <div style="clear: both"></div>
';

$query = "
SELECT
	CRM_naw.id as rel_id,
	CRM_naw.naam,
	CRM_naw.accountEigenaar,
	CRM_naw.CRMReviewDatum,
	Portefeuilles.Portefeuille,
	Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt
FROM
	CRM_naw

LEFT JOIN Portefeuilles ON 
  CRM_naw.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN VermogensbeheerdersPerGebruiker ON 
  Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR'
LEFT JOIN Gebruikers ON 
  VermogensbeheerdersPerGebruiker.Gebruiker = Gebruikers.Gebruiker
  
WHERE
  $extraWhere
  CRM_naw.CRMReviewDatum > '2010-01-01' AND 
  CRM_naw.CRMReviewDatum <= '$tmSql' AND
	(CRM_naw.aktief = 1 OR ISNULL(CRM_naw.aktief) ) 
ORDER BY
  CRM_naw.CRMReviewDatum ASC
";
//debug($query);


$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $result[] = $rec;
}

if ($showPort == "Geen portefeuilleselectie")
{
  $out .= vt("Configureer eerst deze widget.");
}
else if (count($result) == 0)
{
  $out .= "<div>Geen reviewdatums gevonden</div>";
}
else
{
  foreach ($result as $data)
  {
    $lnk = '
      <a target="content" href="CRM_nawEdit.php?action=edit&id={rel_id}&lastTab=1" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>
    ';

$out .= '
    <div class="rTableRow">
';
      $row++;
      foreach ($widgetHelp->columnData as $k=>$v)
      {
        if ($v["show"] != 1) {continue;}

        $widgetExportData[$row][$k] = $data[$v["dbField"]];
        if ($data["rel_id"] > 0 AND $k == "naam")
        {
          $data["naam"] = str_replace("{rel_id}", $data["rel_id"], $lnk)." ".$data["naam"];
        }

        $negren = "";

        if ($v["format"] != "")
        {
          $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
        }
        else
        {
          $value = $data[$v["dbField"]];
        }

        $out .= "<div title='".$v["title"]."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelp->getWidth($v["width"]) ."> ".$value."</div>\n";
      }
$out .= '
    </div>
    ';

  }

}

$_SESSION["widgetExport"][$dialogName] = $widgetExportData;

$selected = ($showPort == "alle")?"SELECTED":"";
$out .= '
</div> <!-- rTable -->
  <div id="setupWidget_'.$dialogName.'" title="Instellen reviewdatums" class="setupWidget">
    <div class="formblock">
      <div class="formlinks">' . vt('Portefeuilleselectie') . ' </div>
      <div class="formrechts">
        <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
          '.$widgetHelp->makeAccessOptions($showPort).'
        </select>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">' . vt('Reviewdatums t/m') . ' </div>
      <div class="formrechts">
        <input name="data_dagen" id="data_dagen" type="number" value="'.(int) $dagen.'" style="width: 50px"/> ' . vt('in de toekomst') . '
      </div>
    </div>
    <p><br/></p>
    <p>
      '.$widgetHelp->makeHtmlInput().'
    </p>
  </div>

</div>

<script>
  $(document).ready(function(){
    var prev_rows = '.$rows.';
    
    $("#btnExport_'.$dialogName.'").click(function(e){
        e.preventDefault();
        console.log("export '.$dialogName.' clicked");
        window.open("widget/widgetPushExport.php?module='.$dialogName.'","wExport");
      });

    '.$cache->JSinit().'
    
    $("#btn_'.$dialogName.'").click(function(){
      setup'.$dialogName.'Dialog.dialog("open");
    });

    var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
    {
      autoOpen: false,
      height: 410,
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

          updateCFG("'.$var_dagen.'", $("#data_dagen").val());
          updateCFG("'.$var_port.'", $("#port_'.$dialogName.'").val() );
          $(".kolCheck'.$widgetHelp->uid.'").each(function()
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





