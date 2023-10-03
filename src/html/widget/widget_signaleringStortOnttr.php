<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/02/28 15:14:37 $
    File Versie         : $Revision: 1.2 $

    $Log: widget_signaleringStortOnttr.php,v $
    Revision 1.2  2020/02/28 15:14:37  cvs
    call 8443

    Revision 1.1  2020/02/28 14:27:18  cvs
    call 8440

    Revision 1.7  2019/02/04 13:38:58  cvs
    no message

    Revision 1.6  2018/09/28 12:53:14  cvs
    widget caching

    Revision 1.5  2018/02/09 10:51:46  cvs
    call 6572

    Revision 1.4  2018/02/01 12:42:03  cvs
    update naar airsV2

    Revision 1.3  2018/01/10 15:19:36  cvs
    no message



*/

include_once ("init.php");
$fmt = new AE_cls_formatter();
$db = new DB();

$dialogName = ("signaleringStort");

$cache = new AE_cls_WidgetsCaching($dialogName, 180);

if ($cache->useCache())
{
  $cont = str_replace("<!--ttl-->", $cache->updateStamp(),$cache->content);
  echo $cont;
  exit;
}


$cfg = new AE_config();

//$var_rows = $USR."_widget_var_".$dialogName."_rows";
$var_port = $USR."_widget_var_".$dialogName."_port";

$var_columns = $USR."_widget_var_".$dialogName."_colums";

if (!$showPort = $cfg->getData($var_port))
{
  $showPort = "Geen portefeuilleselectie";
}

$wFilt = new AE_cls_WidgetsFilter($showPort);
$wFilt->getPortefeuilleAccess();
$extraWhere = "";
$extraJoin  = "";
$extraWhere = $wFilt->extraQuery;
$extraJoin  = $wFilt->extraJoin;


$columnData = array(
  "portefeuille" => array(
    "dbField" => "portefeuille",
    "koptxt"  => vt("Portefeuille"),
    "title"   => vt("Portefeuille"),
    "btrTitle"   => "Portefeuille",
    "show"    => (int) $columnSettings["portefeuille"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "client" => array(
    "dbField" => "Client",
    "koptxt"  => vt("Client"),
    "title"   => vt("Client"),
    "btrTitle"   => "Client",
    "show"    => (int) $columnSettings["client"],
    "width"   => "20",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),

  "aanmaak" => array(
    "dbField" => "add_date",
    "koptxt"  => vt("Aangemaakt"),
    "title"   => vt("Aangemaakt"),
    "btrTitle"   => "Aangemaakt",
    "show"    => (int) $columnSettings["aanmaak"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ac",
    "format"  => "@D{form}"
  ),
  "datum" => array(
    "dbField" => "datum",
    "koptxt"  => vt("Datum"),
    "title"   => vt("Datum"),
    "btrTitle"   => "Datum",
    "show"    => (int) $columnSettings["datum"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ac",
    "format"  => "@D{form}"
  ),
  "bedrag" => array(
    "dbField" => "bedrag",
    "koptxt"  => vt("Bedrag"),
    "title"   => vt("Bedrag"),
    "btrTitle"   => "Bedrag",
    "show"    => (int) $columnSettings["bedrag"],
    "width"   => "10",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ar",
    "format"  => "@N{.2} "
  ),
  "omschrijving" => array(
    "dbField" => "Omschrijving",
    "koptxt"  => vt("Omschrijving"),
    "title"   => vt("Omschrijving"),
    "btrTitle"   => "Omschrijving",
    "show"    => (int) $columnSettings["omschrijving"],
    "width"   => "30",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "soortOvereenkomst" => array(
    "dbField" => "SoortOvereenkomst",
    "koptxt"  => vt("Overeenkomst"),
    "title"   => vt("Overeenkomst"),
    "btrTitle"   => "Overeenkomst",
    "show"    => (int) $columnSettings["SoortOvereenkomst"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "internDepot" => array(
    "dbField" => "InternDepot",
    "koptxt"  => vt("I.Depot"),
    "title"   => vt("Intern depot"),
    "btrTitle"   => "Intern depot",
    "show"    => (int) $columnSettings["internDepot"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ar",
    "format"  => "@checkbox"
  ),
  "accountmanager" => array(
    "dbField" => "Accountmanager",
    "koptxt"  => vt("AccMan"),
    "title"   => vt("Accountmanager"),
    "btrTitle"   => "Accountmanager",
    "show"    => (int) $columnSettings["accountmanager"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ac",
    "format"  => ""
  ),
  "tweedeAanspreekpunt" => array(
    "dbField" => "tweedeAanspreekpunt",
    "koptxt"  => vt("Tweede"),
    "title"   => vt("Tweede aanspreekpunt"),
    "btrTitle"   => "Tweede aanspreekpunt",
    "show"    => (int) $columnSettings["tweedeAanspreekpunt"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU ac ",
    "format"  => ""
  ),





);

$widgetHelp = new AE_cls_WidgetsHelper($columnData, $var_columns);




$query = "
SELECT
  signaleringStortingen.portefeuille,
  signaleringStortingen.periode,
  signaleringStortingen.status,
  signaleringStortingen.bedrag,
  signaleringStortingen.datum,
  signaleringStortingen.add_date,
  Portefeuilles.Accountmanager,
	Portefeuilles.tweedeAanspreekpunt,
	Portefeuilles.Client,
	Portefeuilles.SoortOvereenkomst,
	Portefeuilles.InternDepot,
	laatstePortefeuilleWaarde.laatsteWaarde,
  laatstePortefeuilleWaarde.beginWaarde,
  laatstePortefeuilleWaarde.rendement,
  Rekeningmutaties.Omschrijving
FROM
  signaleringStortingen
LEFT JOIN Portefeuilles ON 
  signaleringStortingen.portefeuille = Portefeuilles.Portefeuille
LEFT JOIN laatstePortefeuilleWaarde ON 
  signaleringStortingen.portefeuille = laatstePortefeuilleWaarde.portefeuille  
LEFT JOIN Rekeningmutaties ON 
	signaleringStortingen.rekeningmutatieId = Rekeningmutaties.id  
  {$extraJoin}
WHERE
   {$extraWhere}
   signaleringStortingen.status='0'
ORDER BY
  signaleringStortingen.datum DESC
";
//debug($query);
$parse = array(
  "header"   => vtb("Signalering stortingen/onttrekkingen (%s) ", array(vt($showPort))),
  "btnSetup" => "btn_".$dialogName,
  "btnCache" => $cache->btnCache().$cache->dataState());
//debug($parse);
$output = $tmpl->parseBlock("kop",$parse);

//$output = $tmpl->parseBlock("kop",array("header" => "Signalering rendement ($showPort) <!--ttl-->", "btnSetup" => "btn_".$dialogName));

$output .= "

  <div class='rTable'>

    <div class='rTableRow'>
";

      foreach ($widgetHelp->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $output .= "<div class='rTableHead' ".$widgetHelp->getWidth($v["width"]) ." btr-title='".$v['btrTitle']."' title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
      }
$output .= "      

    </div>
    <div style='clear: both'></div>


";
    $db->executeQuery($query);

    if ( $showPort == "Geen portefeuilleselectie")
    {
      $output .= vt("Configureer eerst deze widget.");
    }
    else if($db->records()> 0)
    {
    while( $data = $db->nextRecord() )
    {
$output .= "
    
    <div class='rTableRow'>
    ";

      foreach ($widgetHelp->columnData as $k=> $v)
      {
        if ($v["show"] != 1) {continue;}
        $negren = "";

        if ($v["format"] != "")
        {
          $value =  $fmt->format($v["format"], $data[$v["dbField"]]);
        }
        else
        {
          $value = $data[$v["dbField"]];
        }

//        if ($v["dbField"] == "portefeuille")
//        {
//          $value = "<a href='CRM_mutatieQueueVerwerken.php'><button><i class='fa fa-vcard' aria-hidden='true'></i></button></a> ".$data[$v["dbField"]];
//        }

        $output .= "<div btr-title='".$v['btrTitle']."' title='".$v["title"]."' class='rTableCell ".$v["class"]." $negren' ".$widgetHelp->getWidth($v["width"]) ."> ".$value."</div>\n";
      }

$output .= "
    </div>
";


  }

}
else
{
  $output .= "<div>" . vt('Geen items gevonden') . "</div>";
}

$output .= "
  </div>


<div id='setupWidget_{$dialogName}' title='" . vt('Instellen signaleringen') . "' class='setupWidget'>
  <div class='formblock'>
    <div class='formlinks'><label for='Titel'>" . vt('Portefeuilleselectie') . "</label> </div>
    <div class='formrechts'>
      <select name='port_{$dialogName}' id='port_{$dialogName}'>
        ".$widgetHelp->makeAccessOptions($showPort)."
      </select>
    </div>
  </div>
  <p></p>
  <p>
    ".$widgetHelp->makeHtmlInput()."
  </p>
</div>
      ";
$output .= "
    
<script>
  $(document).ready(function(){


     ".$cache->JSinit()."
    
    $('#btn_{$dialogName}').click(function(){
      setup{$dialogName}Dialog.dialog('open');
    });

    var setup{$dialogName}Dialog = $('#setupWidget_{$dialogName}').dialog(
    {
      autoOpen: false,
      height: 460,
      width: '50%',
      modal: true,
      position: {my: 'center', at: 'top', of: window},
      buttons:
      {
        '" . vt('Sluiten') . "': function()
        {
          $( this ).dialog( 'close' );
        },
        '" . vt('Opslaan') . "': function()
        {
          $( this ).dialog( 'close' );
          var rows = $('#showedRows_{$dialogName}').val();

          updateCFG('{$var_port}', $('#port_{$dialogName}').val(), '{$dialogName}' );
          $('.kolCheck{$widgetHelp->uid}').each(function()
          {
            var val = '{$var_columns}#' + $(this).attr('id') + '#' +  ($(this).prop( 'checked' )?'1':'0');
            updateCFG('kolom', val);
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
";

$output .= $tmpl->parseBlock("voet",array("stamp" => date("H:i")));
$cache->addToCache($output);
echo $output;

// lokale functies
function _julDag($dbDatum)
{
  $parts = explode("-",$dbDatum);
  $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
  return floor($julian / 86400);
}
