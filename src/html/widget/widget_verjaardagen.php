<?php
include_once ("init.php");
include_once ("applicatie_functies.php");
include_once ("../../classes/AE_cls_WidgetsHelper.php");
$fmt = new AE_cls_formatter();

$dialogName = "verjaardag";

$cfg = new AE_config();
$var_voor    = $USR."_widget_var_".$dialogName."_voor";
$var_na      = $USR."_widget_var_".$dialogName."_na";
$var_columns = $USR."_widget_var_".$dialogName."_colums";
$data_voor   = (int) $cfg->getData($var_voor);
$data_na     = (int) $cfg->getData($var_na);
if ($data_na == 0 )
{
  $data_na = 14;
}

$columnDataVerjaardag = array(
  "checkbox" => array(
    "dbField" => "checkbox",
    "koptxt"  => vt("Checkbox"),
    "title"   => vt("klik om naar het CRM kaartje te gaan"),
    "show"    => (int) $columnSettings["checkbox"],
    "width"   => "5",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "relatie" => array(
    "dbField" => "relatie",
    "koptxt"  => vt("Relatie"),
    "title"   => vt("Relatie"),
    "show"    => (int) $columnSettings["relatie"],
    "width"   => "30",
    "fixed"   => 1,
    "class"   => "bgEEE borderU ",
    "format"  => ""
  ),
  "text" => array(
    "dbField" => "text",
    "koptxt"  => vt("Tekst"),
    "title"   => vt("Verjaardag tekst"),
    "show"    => (int) $columnSettings["text"],
    "width"   => "10",
    "fixed"   => 0,
    "class"   => "bgEEE borderU bold ac",
    "format"  => ""
  ),
  "geboortedatum" => array(
    "dbField" => "geboortedatum",
    "koptxt"  => vt("Geboortedatum"),
    "title"   => vt("Geboortedatum"),
    "show"    => (int) $columnSettings["geboortedatum"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU bold ac ",
    "format"  => "@D{form}"
  ),
  "query" => array(
    "dbField" => "query",
    "koptxt"  => vt("O"),
    "title"   => vt("Oorsprong"),
    "show"    => (int) $columnSettings["query"],
    "width"   => "5",
    "fixed"   => 0,
    "class"   => "bgEEE borderU bold ",
    "format"  => ""
  ),

);

$verjaardagwidgetHelp = new AE_cls_WidgetsHelper($columnDataVerjaardag, $var_columns);

echo $tmpl->parseBlock("kop",array("header" => vt("Verjaardagen"), "btnSetup" => "btn_".$dialogName));

?>
  <div class="rTable">

  <div class="rTableRow">
<?
    foreach ($verjaardagwidgetHelp->columnData as $k=> $v)
    {
      if ($v["show"] != 1) {continue;}
      echo "<div class='rTableHead' ".$verjaardagwidgetHelp->getWidth($v["width"]) ." title='".$v["title"]."' data-toggle='tooltip'> ".$v["koptxt"]."</div>\n";
    }
?>

  </div>
  <div style="clear: both"></div>

<div class="rTable">

<?

$list=widgetVerjaardagslijst(true);

//debug($list);

ksort($list);
foreach($list as $data)
{
//  debug($data);

  $data["text"] = str_replace("werd 1 dagen geleden", "werd gisteren", $data["text"]);
  if ($data["id"] != 0)
  {
    $data["checkbox"] .= '<a target="content"  href="CRM_nawEdit.php?action=edit&id='.$data["id"].'" class="btn-new btn-default pull-right"><button><i class="fa fa-address-card-o" aria-hidden="true"></i></button></a>';
  }

?>
  <div class="rTableRow">
<?


$item["tit2"] = strip_tags($item["relatie"]);

foreach ($verjaardagwidgetHelp->columnData as $k=> $v)
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
  echo "<div title='".$v["title"]."'  class='rTableCell ".$v["class"]." $negren' ".$verjaardagwidgetHelp->getWidth($v["width"]) ."> ".$value."</div>\n";
}


?>
  </div>
  <?
}

?>
</div> <!-- rTable -->

  <!-- Dialoog <?=$dialogName?> -->
  <div id="setupWidget_<?=$dialogName?>" title="Instellen verjaardagen" class="setupWidget">

    <?= vt('Geef aan voor welke periode verjaardagen moeten worden getoond.'); ?><br/>
    <br/>
    Periode loopt van <input name="data_voor" id="data_voor" type="number" value="<?=$data_voor?>" style="width: 50px"/>
    dagen voor <b>vandaag</b> t/m <input name="data_na" id="data_na" type="number" value="<?=$data_na?>" style="width: 50px"/>
    dagen na vandaag.
    <p>
      <?=$verjaardagwidgetHelp->makeHtmlInput()?>
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
              updateCFG("<?=$var_na?>", $("#data_na").val());
              $(".kolCheck<?=$verjaardagwidgetHelp->uid?>").each(function()
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
<?

function widgetVerjaardagslijst($checkboxDisabled=false)
{
  global $data_na, $data_voor;
  global $USR;
  $DB=new DB();
  $extraWhere='';

  $daysThisYear = date("z", mktime(0,0,0,12,31,date("Y"))) + 1;

  $disabled = ($checkboxDisabled==true)?'disabled':'';

  $query="SELECT CRMeigenRecords FROM Gebruikers WHERE Gebruiker='$USR'";
  $DB->SQL($query);

  $gebruikersData = $DB->lookupRecord();

  if($gebruikersData['CRMeigenRecords'] > 0)
  {
    $extraWhere=" AND CRM_naw.prospectEigenaar='$USR' ";
  }

  $extraWhere.= getRelatieSoortenFilter();

  $query = "
  SELECT 
    CRM_naw.id,
    CRM_naw.naam,
    geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(geboortedatum),
      YEAR(now())-YEAR(geboortedatum)+1)
    as leeftijd,
    IF(kaartVerstuurd < ( NOW() - INTERVAL 50 DAY) ,0,1) as kaartVerstuurd,
    ondernemingsvorm,
    concat(voorletters,' ',tussenvoegsel,' ',achternaam,' ',achtervoegsel) as pnaam,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
       (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
       (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
  FROM
    CRM_naw
  LEFT Join Portefeuilles ON 
    CRM_naw.portefeuille = Portefeuilles.Portefeuille
  LEFT Join Vermogensbeheerders ON 
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  LEFT Join VermogensbeheerdersPerGebruiker ON 
    Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    verjaardagLijst = 1 AND 
    aktief = 1 AND 
    (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
    $extraWhere
  GROUP BY 
    pnaam,
    geboortedatum
  HAVING
    ((jarig_over_dagen < $data_na AND jarig_over_dagen >= 0)  OR jarig_over_dagen >= ".($daysThisYear - $data_voor).")
  ORDER BY
    jarig_over_dagen
  ";

  $DB->executeQuery($query);

  while ($crmRec = $DB->nextRecord())
  {
    $crmRec["query"] = "eigenaar";
    $verjArray[] = $crmRec;
  }


  $query = "
  SELECT
    CRM_naw.id,
    CRM_naw.naam as naam,
    IF(kaartVerstuurdPartner < ( NOW() - INTERVAL 50 DAY) ,0,1) as kaartVerstuurd,
    part_geboortedatum AS geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(part_geboortedatum),
      YEAR(now())-YEAR(part_geboortedatum)+1)
    as leeftijd,
    ondernemingsvorm,
    concat(part_voorletters,' ',part_tussenvoegsel,' ',part_achternaam,' ',part_achtervoegsel) as pnaam,
    dayofyear(part_geboortedatum) as geboortedag,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
       (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
       (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
  FROM
    CRM_naw
  LEFT Join Portefeuilles ON 
    CRM_naw.portefeuille = Portefeuilles.Portefeuille
  LEFT Join Vermogensbeheerders ON 
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  LEFT Join VermogensbeheerdersPerGebruiker ON 
    Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    part_verjaardagLijst = 1 AND 
    aktief = 1               AND 
    (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
    $extraWhere
  GROUP BY 
    pnaam,
    part_geboortedatum
  HAVING
    ((jarig_over_dagen < $data_na AND jarig_over_dagen >= 0)  OR jarig_over_dagen >= ".($daysThisYear - $data_voor).")
  ORDER BY
    jarig_over_dagen
  ";

  $DB->executeQuery($query);
  while ($crmRec = $DB->nextRecord())
  {
    $crmRec["query"] = "partner";
    $verjArray[] = $crmRec;
  }

  $query = "
  SELECT CRM_naw.id,
    CRM_naw_adressen.id as adresId,
    CRM_naw_adressen.naam as pnaam,
    CRM_naw_adressen.geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(CRM_naw_adressen.geboortedatum),
      YEAR(now())-YEAR(CRM_naw_adressen.geboortedatum)+1)
    as leeftijd,
    IF(CRM_naw_adressen.kaartVerstuurd < ( NOW() - INTERVAL 50 DAY) ,0,1) as kaartVerstuurd,
    CRM_naw.ondernemingsvorm,
    concat(voorletters,' ',tussenvoegsel,' ',achternaam,' ',achtervoegsel) as pnaam2,
    CRM_naw.naam as naam,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
       (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
       (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
  FROM
    CRM_naw_adressen
  Join CRM_naw ON 
    CRM_naw_adressen.rel_id=CRM_naw.id
  LEFT Join Portefeuilles ON 
    CRM_naw.portefeuille = Portefeuilles.Portefeuille
  LEFT Join Vermogensbeheerders ON 
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  LEFT Join VermogensbeheerdersPerGebruiker ON 
    Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    CRM_naw_adressen.verjaardagLijst = 1 AND 
    aktief = 1                           AND 
    (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
    $extraWhere
  GROUP BY 
    pnaam,
    geboortedatum
  HAVING
    ((jarig_over_dagen < $data_na AND jarig_over_dagen >= 0)  OR jarig_over_dagen >= ".($daysThisYear - $data_voor).")
  ORDER BY
    jarig_over_dagen
  ";

  $DB->executeQuery($query);

  while ($crmRec = $DB->nextRecord())
  {
    $crmRec["query"] = "adres";
    $verjArray[] = $crmRec;
  }


  $query = "
  SELECT CRM_naw.id,
    CRM_naw_kontaktpersoon.id as adresId,
    CRM_naw_kontaktpersoon.naam as pnaam,
    CRM_naw_kontaktpersoon.geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(CRM_naw_kontaktpersoon.geboortedatum),
      YEAR(now())-YEAR(CRM_naw_kontaktpersoon.geboortedatum)+1)
    as leeftijd,
    CRM_naw.ondernemingsvorm,
    concat(voorletters,' ',tussenvoegsel,' ',achternaam,' ',achtervoegsel) as pnaam2,
    CRM_naw.naam as naam,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
       (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
       (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
  FROM
    CRM_naw_kontaktpersoon
  Join CRM_naw ON 
    CRM_naw_kontaktpersoon.rel_id=CRM_naw.id
  LEFT Join Portefeuilles ON 
    CRM_naw.portefeuille = Portefeuilles.Portefeuille
  LEFT Join Vermogensbeheerders ON 
    Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
  LEFT Join VermogensbeheerdersPerGebruiker ON 
    Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    CRM_naw_kontaktpersoon.verjaardagLijst = 1 AND 
    aktief = 1                                 AND 
    (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
    $extraWhere
  GROUP BY 
    pnaam,
    geboortedatum
  HAVING
    ((jarig_over_dagen < $data_na AND jarig_over_dagen >= 0)  OR jarig_over_dagen >= ".($daysThisYear - $data_voor).")
  ORDER BY
    jarig_over_dagen
  ";

  $DB->executeQuery($query);

  while ($crmRec = $DB->nextRecord())
  {

    $crmRec["query"] = "kontaktp.";
    $verjArray[] = $crmRec;
  }


  foreach ($verjArray as $item)
  {
    if ($item['leeftijd'] > 110) // als geen geboortedatum ingevuld dan is de leeftijd 2000+
    {
      continue;
    }

    $jdag = round($item['jarig_over_dagen']);

    $split = explode("-",$item['geboortedatum']);
    $daysInYear = date("z", mktime(0,0,0,$split[1],$split[2],date("Y")));

    $key=sprintf("%03d", $daysInYear)."_".$item['naam']."_1_".$item['id'];

    if ($jdag > 1)
    {
      $jdagStr = vtb("wordt over %s dagen %s jaar.", array($jdag, $item['leeftijd']));
    }
    else
    {
      $jdagStr = ($jdag == 0)?vtb("wordt vandaag %s jaar.", array($item['leeftijd'])):vtb("wordt morgen %s jaar.", array($item['leeftijd']));
    }

    if ($jdag >= ($daysThisYear - $data_voor - 1))
    {
      $jdagStr = vtb("werd %s dagen geleden %s jaar.", array(($daysThisYear - $jdag), ($item['leeftijd']-1)));
      $jdag = ($daysThisYear - $data_voor);
    }


    if ($item["query"] == "kontaktp." OR $item["query"] == "adres")
    {
      $Row[$key] = "<li>$checkbox<b>".$item['pnaam']."</b> van ".$item['naam']." ".$jdagStr;
      $naam = $item['pnaam']." (".$item['naam'].") ";
    }
    else
    {
      if (strtolower($item["ondernemingsvorm"]) == "particulier" || $item["ondernemingsvorm"]=='')
      {
        $Row[$key] = "<li>$checkbox<b>".$crmRec['pnaam']."</b> ".$jdagStr;
        $naam = $item['pnaam'];
      }
      else
      {
        $Row[$key] = "<li>$checkbox<b>".$item['pnaam']."</b> van ".$item['naam']." ".$jdagStr;
        $naam = $item['pnaam']." (".$item['naam'].") ";
      }
    }


    if($item['kaartVerstuurd'] == 0)
    {
      $checkbox='<input type="checkbox" name="kaart_'.$item['adresId'].'_'.$item['id'].'_a" value="1" '.$disabled.' >';
    }
    else
    {
      $checkbox='<input type="checkbox" name="kaart_'.$item['adresId'].'_'.$item['id'].'_a" checked value="1" disabled>';
    }

//    $Row[$key] = "<li>$checkbox<b>".$item['naam']."</b> (".$item['CRM_naam'].") ".$jdagStr;

    $verjaardagArray[$key] = array(
      "checkbox"      => $checkbox,
      "relatie"       => $naam,
      "text"          => $jdagStr,
      "dagen"         => $jdag,
      "leeftijd"      => $item['leeftijd'],
      "geboortedatum" => $item['geboortedatum'],
      "id"            => $item['id'],
      "query"         => $item["query"],
    );

  }


  return $verjaardagArray;
}
