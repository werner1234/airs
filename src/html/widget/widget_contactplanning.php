<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/03/12 07:16:27 $
    File Versie         : $Revision: 1.9 $

    $Log: widget_contactplanning.php,v $
    Revision 1.9  2020/03/12 07:16:27  cvs
    update 2020.03.11

    Revision 1.8  2018/09/28 12:53:14  cvs
    widget caching

    Revision 1.7  2018/02/21 16:16:10  cvs
    target toegevoegd

    Revision 1.6  2018/02/09 10:51:46  cvs
    call 6572

    Revision 1.5  2018/02/01 12:42:03  cvs
    update naar airsV2

    Revision 1.4  2017/12/15 13:53:48  cvs
    call 6365

    Revision 1.3  2017/11/20 09:02:01  cvs
    no message

    Revision 1.2  2017/11/10 13:49:26  cvs
    call 6320

    Revision 1.1  2017/09/27 15:40:48  cvs
    call 6159



*/

include_once ("init.php");

$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = ("contactplanning");

$cache = new AE_cls_WidgetsCaching($dialogName, 180);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

$cfg = new AE_config();


$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";
$var_columns = $USR."_widget_var_".$dialogName."_colums";

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = ("Geen portefeuilleselectie");
}


$wFilt = new AE_cls_WidgetsFilter($showPort);

$wFilt->getCombiAccess();


$rows = (int) $cfg->getData($var_rows);
if ($rows == 0) $rows = 25;

$extraWhere = $wFilt->extraQuery;
//$extraJoin  = $wFilt->extraJoin;
//$extraWhere = "";
$extraJoin  = "";

$columnDataVermogen = array(
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
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "interval" => array(
    "dbField" => "contactTijd",
    "koptxt"  => vt("Contact interval"),
    "title"   => vt("Contact interval"),
    "show"    => (int) $columnSettings["interval"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ar",
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
  "soortOvereenkomst" => array(
    "dbField" => "SoortOvereenkomst",
    "koptxt"  => vt("SoortOvereenkomst"),
    "title"   => vt("SoortOvereenkomst"),
    "show"    => (int) $columnSettings["soortOvereenkomst"],
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
  "gesprekken" => array(
    "dbField" => "aantalRecenteGesprekken",
    "koptxt"  => vt("# gespr."),
    "title"   => vt("Aantal recente gesprekken"),
    "show"    => (int) $columnSettings["gesprekken"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "verwachteDatum" => array(
    "dbField" => "verwachteDag",
    "koptxt"  => vt("Datum"),
    "title"   => vt("Verwachte datum"),
    "show"    => (int) $columnSettings["verwachteDatum"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => "@D{form}"
  ),
  "client" => array(
    "dbField" => "Client",
    "koptxt"  => vt("Client"),
    "title"   => vt("Client"),
    "show"    => (int) $columnSettings["client"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ac",
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

$db=new DB();
$query="SELECT Omschrijving FROM CRM_selectievelden WHERE module='gesprekstypen' AND waarde='1'";
$db->executeQuery($query);

while($data=$db->nextRecord())
{
  $relaties[]=$data['Omschrijving'];
}

$categorieFilter = '';
if(count($relaties) == 0)
{
  //$categorieFilter = ' AND CRM_naw_dossier.ClientGesproken=1 ';
}
else
{
  $categorieFilter="AND CRM_naw_dossier.type IN('".implode("','",$relaties)."')";
}


if($type=='planning')
  $queryHaving="HAVING aantalRecenteGesprekken >= 1";
else
  $queryHaving="HAVING aantalRecenteGesprekken < 1";

$query="SELECT CRM_relatieSoorten FROM Gebruikers WHERE Gebruiker='$USR'";

$CRM_relatieSoorten=$db->lookupRecordByQuery($query);
$CRM_relatieSoorten=unserialize($CRM_relatieSoorten['CRM_relatieSoorten']);
$filter='';
//if(is_array($CRM_relatieSoorten))
//{
//  $crmVelden=array();
//  $allArray=array();
//
//  $query="DESC CRM_naw";
//  $db->executeQuery($query);
//  while($data=$db->nextRecord('num'))
//  {
//    $crmVelden[]=$data[0];
//  }
//
//  foreach($CRM_relatieSoorten as $key=>$value)
//  {
//    if($value <> 'all' AND $value <> 'inaktief' AND $value <> 'aktief' AND in_array($value,$crmVelden) )
//    {
//      $allArray[] = 'CRM_naw.'.$value;
//    }
//
//  }
//  $filter=" AND ( (".implode(' = 1 OR ',$allArray)." = 1) OR ( ".implode(' = 0 AND ',$allArray)." = 0 ) )";
//
//}
//
//$extraWhere=" AND aktief = 1 $filter";

$query = "
SELECT
	CRM_naw.id,
	CRM_naw.naam,
	CRM_naw.portefeuille,
	CRM_naw.contactTijd,
	Portefeuilles.Accountmanager AS Accountmanager,
	Portefeuilles.tweedeAanspreekpunt,
	Portefeuilles.Client,
  Portefeuilles.SoortOvereenkomst,
	Portefeuilles.InternDepot,
	(
		SELECT
			count(id)
		FROM
			CRM_naw_dossier
		WHERE
			CRM_naw_dossier.rel_id = CRM_naw.id
		$categorieFilter
		AND CRM_naw_dossier.ClientGesproken = 1
		AND datum > (
			now() - INTERVAL CRM_naw.contactTijd DAY
		)
	) AS aantalRecenteGesprekken,
	(
		(
			SELECT
				datum
			FROM
				CRM_naw_dossier
			WHERE
				CRM_naw_dossier.rel_id = CRM_naw.id
			$categorieFilter
			AND CRM_naw_dossier.ClientGesproken = 1
			ORDER BY
				datum DESC
			LIMIT 1
		) + INTERVAL CRM_naw.contactTijd DAY
	) AS verwachteDag
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
	CRM_naw.contactTijd > 0 AND
	(Portefeuilles.Einddatum IS NULL OR Portefeuilles.Einddatum > NOW() )AND
	(CRM_naw.aktief = 1 OR ISNULL(CRM_naw.aktief) )
  
ORDER BY
	verwachteDag
	
LIMIT 0, $rows	
	";
//debug($query);
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $result[] = $rec;
}

//echo $tmpl->parseBlock("kop",array("header" => "Top $rows, Contactplanning ($showPort)", "btnSetup" => "btn_".$dialogName));
//debug($query);
$parse = array(
  "btnExport" => "<button class='btn-new btn-default pull-right fa fa-file-excel-o headSetup' id='btnExport_{$dialogName}' aria-hidden='true' title='Exporteer'></button>",
  "header" => vtb("Top %s, Contactplanning (%s)", array($rows, vt($showPort))),
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


if ( $showPort == ("Geen portefeuilleselectie"))
{
  $out .= vt("Configureer eerst deze widget.");
}
else if (count($result) == 0)
{
  $out .= vt("Geen items gevonden");
}
else
{
  $row = 0;
  foreach ($result as $data)
  {
    $row++;
    $linkBlok = "";
    $lnk = '
      <a target="content" href="CRM_nawEdit.php?action=edit&id='.$data["id"].'&lastTab=9&frameSrc='.base64_encode("CRM_naw_dossierList.php?deb_id=".$data["id"]).'" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>
      
    ';

    $vergeten = ( db2jul($data["verwachteDag"]) < mktime() )?"bgRood":"bgEEE";
    $naamValue = $data["naam"];
    if ($data["id"] > 0)
    {
      $data["naam"] = str_replace("{relId}", $data["id"], $lnk)." ".$data["naam"];

    }
    $out .= '
    <div class="rTableRow">
    ';

      foreach ($widgetHelpVermogen->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $negren = "";
        if ($v["dbField"] == "verwachteDag")
        {

          $negren = ( db2jul($data["verwachteDag"]) < mktime() )?"bgRood":"bgEEE";
        }
        if ($v["format"] != "")
        {
          $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
        }
        else
        {
          $value = $data[$v["dbField"]];
        }
        if ($k == "naam")
        {
          $expValue = $naamValue;
        }
        else
        {
          $expValue = $data[$v["dbField"]];
        }

        $widgetExportData[$row][$k] = $expValue;
        $out .= "<div class='rTableCell ".$v["class"]." $negren' title='".$v["title"]."' ".$widgetHelpVermogen->getWidth($v["width"]) ."> ".$value."</div>\n";
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

<!-- Dialoog '.$dialogName.' -->
<div id="setupWidget_'.$dialogName.'" title="' . vt('Instellen contactplanning') . '" class="setupWidget">

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
      height: 450,
      width: "50%",
      modal: true,
      position:  { my: "center", at: "top", of: window },
      buttons:
      {
        "' . vt('Sluiten') . '": function()
        {
          $( this ).dialog( "close" );
        },
        "' . vt('Opslaan') . '": function()
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


