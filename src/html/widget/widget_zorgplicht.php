<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/09/28 12:53:14 $
    File Versie         : $Revision: 1.12 $

    $Log: widget_zorgplicht.php,v $
    Revision 1.12  2018/09/28 12:53:14  cvs
    widget caching

    Revision 1.11  2018/02/21 16:16:10  cvs
    target toegevoegd

    Revision 1.10  2018/02/09 10:51:46  cvs
    call 6572

    Revision 1.9  2018/02/01 12:42:03  cvs
    update naar airsV2

    Revision 1.8  2017/12/15 13:53:48  cvs
    call 6365

    Revision 1.7  2017/11/30 07:39:55  cvs
    opmaak en knop naar rapport

    Revision 1.6  2017/11/10 13:49:26  cvs
    call 6320

    Revision 1.5  2017/09/27 14:18:19  cvs
    call 6159

    Revision 1.4  2017/09/25 14:46:21  cvs
    call 6205

    Revision 1.3  2017/06/26 11:43:16  cvs
    alle/eigen portefeuilles



*/

include_once ("init.php");

$dialogName = "zorgplicht";
$db = new DB();
$cfg = new AE_config();
$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";
$var_columns = $USR."_widget_var_".$dialogName."_colums";
$fmt = new AE_cls_formatter();

$cache = new AE_cls_WidgetsCaching($dialogName, 120);

if ($cache->useCache())
{

  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = "Geen portefeuilleselectie";
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;

$columnData = array(
  "portefeuille" => array(
    "dbField" => "portefeuille",
    "koptxt"  => vt("Portefeuille"),
    "title"  => vt("Portefeuille"),
    "btrTitle"   => "Portefeuille",
    "show"    => (int) $columnSettings["portefeuille"],
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "client" => array(
    "dbField" => "Client",
    "koptxt"  => vt("Client"),
    "title"  => vt("Client"),
    "btrTitle"   => "Client",
    "show"    => (int) $columnSettings["client"],
    "width"   => "20",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "ZpMethode" => array(
    "dbField" => "ZpMethode",
    "koptxt"  => vt("Zorgplicht methode"),
    "title"  => vt("Zorgplicht methode"),
    "btrTitle"   => "Zorgplicht methode",
    "show"    => (int) $columnSettings["ZpMethode"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold ",
    "format"  => ""
  ),
  "zorgMeting" => array(
    "dbField" => "zorgMeting",
    "koptxt"  => vt("ZorgMeting"),
    "title"  => vt("ZorgMeting"),
    "btrTitle"   => "ZorgMeting",
    "show"    => (int) $columnSettings["zorgMeting"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU bold al ",
    "format"  => ""
  ),
  "TijdelijkUitsluitenZp" => array(
    "dbField" => "TijdelijkUitsluitenZp",
    "koptxt"  => vt("TijdelijkUitsluitenZp"),
    "title"  => vt("TijdelijkUitsluitenZp"),
    "btrTitle"   => "TijdelijkUitsluitenZp",
    "show"    => (int) $columnSettings["TijdelijkUitsluitenZp"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ac",
    "format"  => ""
  ),
  "accountmanager" => array(
    "dbField" => "Accountmanager",
    "koptxt"  => vt("Accountmanager"),
    "title"  => vt("Accountmanager"),
    "btrTitle"   => "Accountmanager",
    "show"    => (int) $columnSettings["accountmanager"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "soortOvereenkomst" => array(
    "dbField" => "SoortOvereenkomst",
    "koptxt"  => vt("SoortOvereenkomst"),
    "title"  => vt("SoortOvereenkomst"),
    "btrTitle"   => "SoortOvereenkomst",
    "show"    => (int) $columnSettings["soortOvereenkomst"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "tweedeAanspreekpunt" => array(
    "dbField" => "tweedeAanspreekpunt",
    "koptxt"  => vt("Tweede aanspreekpunt"),
    "title"  => vt("Tweede aanspreekpunt"),
    "btrTitle"   => "Tweede aanspreekpunt",
    "show"    => (int)$columnSettings["tweedeAanspreekpunt"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "internDepot" => array(
    "dbField" => "InternDepot",
    "koptxt"  => vt("I.Depot"),
    "title"  => vt("Intern depot"),
    "btrTitle"   => "Intern depot",
    "show"    => (int) $columnSettings["internDepot"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ar",
    "format"  => "@checkbox"
  ),


);
$zpMethode    = array(0=>vt('niet opgegeven'),1=>vt('Via categorien'),2=>vt('Via AFM standaarddeviatie'),3=>vt('Via werkelijke standaarddeviatie'));
$zpUitsluiten =  array(0=>vt('Niet uitsluiten'),1=>vt('Geheel uitsluiten'),2=>vt('Tijdelijk akkoord'));

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);


//$out  = $tmpl->parseBlock("kop",array("header" => "Zorgplicht ($showPort) <!--ttl-->", "btnSetup" => "btn_".$dialogName));

$parse = array(
  "btnExport" => "<button class='btn-new btn-default pull-right fa fa-file-excel-o headSetup' id='btnExport_{$dialogName}' aria-hidden='true' title='" . vt('Exporteer') . "'></button>",
  "header" => vtb("Zorgplicht (%s) ", array(vt($showPort))),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache().$cache->dataState());
//debug($parse);
$out = $tmpl->parseBlock("kop",$parse);

$out .= '
<div class="rTable">

  <div class="rTableRow">
';

    foreach ($widgetHelp->columnData as $k=>$v)
    {
      if ($v["show"] != 1) {continue;}

      $out .= "<div class='rTableHead' ".$widgetHelp->getWidth($v["width"]) ." btr-title='".$v['btrTitle']."' title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
    }
$out .= '    
    

  </div>
  <div style="clear: both"></div>
';


//aetodo: gebruikers gebBase bij voorkeur verwijderen

//echo $tmpl->parseBlockFromFile("zorgplicht/zorgplicht_tableHeadf.html",$kopData);

$query="
SELECT
  laatstePortefeuilleWaarde.portefeuille,
  Portefeuilles.Client,
  Portefeuilles.ZpMethode,
  laatstePortefeuilleWaarde.zorgMeting,
  Portefeuilles.TijdelijkUitsluitenZp,
  Portefeuilles.Accountmanager,
  Portefeuilles.InternDepot,
	Portefeuilles.tweedeAanspreekpunt,
  Portefeuilles.SoortOvereenkomst
FROM
  Portefeuilles
-- LEFT JOIN Gebruikers gebBase ON 
--  Portefeuilles.Accountmanager = gebBase.Accountmanager
INNER JOIN laatstePortefeuilleWaarde ON 
  Portefeuilles.Portefeuille = laatstePortefeuilleWaarde.portefeuille
$extraJoin
WHERE 
  $extraWhere
   (
    (
      Portefeuilles.TijdelijkUitsluitenZp = 0 AND 
      laatstePortefeuilleWaarde.zorgMeting <> 'Voldoet'
    ) OR  
      Portefeuilles.TijdelijkUitsluitenZp=2
  ) AND 
    Portefeuilles.ZpMethode <> 0 AND
    laatstePortefeuilleWaarde.zorgMeting <> '' AND 
    Portefeuilles.Startdatum < now() AND 
    Portefeuilles.Startdatum > '0000-00-00' AND 
    Portefeuilles.Einddatum > now() ";


  $db->executeQuery($query);
  if ($showPort == "Geen portefeuilleselectie")
  {
    $out .= vt("Configureer eerst deze widget.");
  }
  else if($db->records() > 0)
  {

//debug($zpMethode);
//debug($zpUitsluiten);
    $row = -1;
    while ($zorgData = $db->nextRecord())
    {
      //debug($zorgData);


      $out .= '
      <!----------------------------------->
      <div class="rTableRow">
      ';
$row++;
      foreach ($widgetHelp->columnData as $k => $v)
      {

        $extraStyle = ($zorgData['TijdelijkUitsluitenZp'] == 2)?"bgGroen":"bgEEE";
//        debug($v,$k);
        if ($v["show"] != 1)
        {
          continue;
        }
        $negren = "";
        if ($v["dbField"] == "rendement")
        {
          $negren = $data["rendement"] <= 0?"negatief":"";
        }



        if ($v["format"] != "")
        {
          $value = $fmt->format($v["format"], $zorgData[$v["dbField"]]);
        }
        else
        {
          $value = $zorgData[$v["dbField"]];
        }

        if ($v["dbField"] == "ZpMethode")
        {

          $value = $zpMethode[$zorgData[$v["dbField"]]];
        }

        if ($v["dbField"] == "TijdelijkUitsluitenZp")
        {
          $value = $zpUitsluiten[$zorgData[$v["dbField"]]];
        }

//        if ($v["dbField"] == "Client")
//        {
//          $value = '<a href="CRM_nawEdit.php?action=edit&id={relId}" class="btn-new btn-default"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a> '.$zorgData['Client'] ;
//
//        }
        if ($v["dbField"] == "portefeuille")
        {
          $rapport = ($zorgData['ZpMethode'] == 2)?'AFM':'ZORG';
          $value = '<a target="content" href="rapportFrontofficeClientAfdrukken.php?portefeuille=' . $zorgData['portefeuille'] . '&rapport=' . $rapport . '" target="_blank"><button><i class="fa fa fa-line-chart" aria-hidden="true"></i></button></a> ' . $zorgData['portefeuille'] ;
          $widgetExportData[$row][$k] = $zorgData['portefeuille'];
        }
        else
        {
          $widgetExportData[$row][$k] = $value;
        }

        if ($v["format"] == "@checkbox")
        {
          $widgetExportData[$row][$k] = $zorgData[$v["dbField"]];
        }


        $out .= "\n\t\t<div btr-title='".$v['btrTitle']."' title='".$v["title"]."' class='rTableCell " . $v["class"] ." ".$extraStyle. " $negren' " . $widgetHelp->getWidth($v["width"]) . "> " . $value . "</div>\n";
      }
      $out .='
      
      </div>
      ';
    }
//    while ($zorgData = $db->nextRecord())
//    {
//      $rapport = ($zorgData['ZpMethode'] == 2)?'AFM':'ZORG';
//
//      $extraStyle = ($zorgData['TijdelijkUitsluitenZp'] == 2)?"bgGroen":"bgEEE";
//
//      $rowData = array(
//        "kol1Class" => "bgEEE borderU",
//        "kol2Class" => "bgFFF borderU",
//        "kol3Class" => "$extraStyle borderU",
//        "kol1"      => '<a href="rapportFrontofficeClientAfdrukken.php?portefeuille=' . $zorgData['portefeuille'] . '&rapport=' . $rapport . '" target="_blank">' . $zorgData['portefeuille'] . '</a>',
//        "kol2"      => $zorgData['Client'],
//        "kol3"      => $zorgData['zorgMeting'],
//      );
//
//
//      echo $tmpl->parseBlockFromFile("zorgplicht/zorgplicht_tableRow.html",$rowData);
//    }
//    debug($widgetExportData);
}
else
{
  $out .= "<div>" . vt('Geen afwijkingen gevonden') . "</div>";
}

$out .= '

</div> <!-- rTable -->

';

  $_SESSION["widgetExport"][$dialogName] = $widgetExportData;
  $selected = ($showPort == "alle")?"SELECTED":"";
$out .= '
  
  </div> <!-- rTable -->

  <!-- Dialoog <?=$dialogName?> -->
  <div id="setupWidget_'.$dialogName.'" title="' . vt('Instellen zorgplicht') . '" class="setupWidget">


    <div class="formblock">
      <div class="formlinks">' . vt('Portefeuilleselectie') . '</div>
      <div class="formrechts">
        <select name="port_'.$dialogName.'" id="port_'.$dialogName.'">
          '.$widgetHelp->makeAccessOptions($showPort).'
        </select>
      </div>
    </div>
    <p></p>

    <p>
      '.$widgetHelp->makeHtmlInput().'
    </p>
  </div>
  <script>
    $(document).ready(function(){
      
      $("#btnExport_'.$dialogName.'").click(function(e){
        e.preventDefault();
        console.log("export '.$dialogName.' clicked");
        window.open("widget/widgetPushExport.php?module='.$dialogName.'","wExport");
      });   
      
      '.$cache->JSinit().'

      $("#btn_'.$dialogName.'").click(function()
      {
        setup'.$dialogName.'Dialog.dialog("open");
      });

      var setup'.$dialogName.'Dialog = $("#setupWidget_'.$dialogName.'").dialog(
      {

        autoOpen: false,
        height: 410,
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