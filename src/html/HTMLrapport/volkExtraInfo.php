<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: rm $
    Laatste aanpassing  : $Date: 2019/12/02 11:27:35 $
    File Versie         : $Revision: 1.28 $
*/


include_once "init.php";

$db = new DB();
$fmt = new AE_cls_formatter(",",".");
$templ = new AE_template();
$templ->templatePath = getcwd()."/classTemplates/";
$templ->appendSubdirToTemplatePath("transactieOverzicht");

$ms = new AE_cls_Morningstar();

$query = "SELECT * FROM Grootboekrekeningen ORDER BY Grootboekrekening";
$db->executeQuery($query);
while ($gbRec = $db->nextRecord())
{
  $gb[$gbRec["Grootboekrekening"]] = $gbRec["Omschrijving"];
}


$query = "SELECT * FROM `_htmlRapport_VOLK` WHERE id = ".$_GET["id"];
$mainRec = $db->lookupRecordByQuery($query);
if ($mainRec["type"] == "rekening" AND !strstr($_GET["tab"],"R") )
{
  $_GET["tab"] .= "R";
}

?>
<script>
  $("#ui-dialog-title-extraInfoPopup").html("Details <?=$mainRec["fondsOmschrijving"]?> <?=$mainRec["rekening"]?>");

</script>
<?

switch ($_GET["tab"])
{
  case "1":

?>
    <script>
      $(document).ready(function(){
        $("#titleTab1").text("Transacties");
        $("#titleTab2").text("Dividenden/Coupons");
        $("#titleTab3").text("Fondsgegevens");
        $("#titleTab4").text("Koersinformatie");
        $("#titleTab3").show();
        $("#titleTab4").show();
      });
    </script>
<?
    $templ->loadTemplateFromFile("getTransacties.sql","getTransacties");
    $query = $templ->parseBlock("getTransacties",array(
      "fonds" => $mainRec["fonds"],
      "portefeuille" => $mainRec["portefeuille"],
      "ytd" => "1971-01-01",
      "boekDatum" => $mainRec["rapportDatum"]
    ));
    $db->executeQuery($query);
?>
    <article class="extraInfoiArticle">

    <table class="extraInfoTable">
      <tr class="trHeader">
        <td><?= vt('Datum'); ?></td>
        <td><?= vt('Type'); ?></td>
        <td><?= vt('Fonds'); ?></td>
        <td><?= vt('Valuta'); ?></td>
        <td class="ar"><?= vt('Aantal'); ?></td>
        <td class="ar"><?= vt('Koers'); ?></td>
        <td class="ar"><?= vt('Waarde Valuta'); ?></td>
        <td class="ar"><?= vt('Valutakoers'); ?></td>
        <td class="ar"><?= vt('Waarde in EUR'); ?></td>
        <td class="ar"><?= vt('Cum. Aantal'); ?></td>
      </tr>
<?
    $saldo = 0;
    $bBoekSkip = false;
    while ($rec = $db->nextRecord())
    {
      if   ($bBoekSkip AND $rec["Transactietype"] == "B")  continue; // alleen de eerste B boeking meenemen
      $bBoekSkip = true;
      $saldo += $rec["Aantal"];
      $out[] = "
      <tr>
        <td>".$fmt->format("@D {d}-{m}-{Y}", $rec["Boekdatum"])."</td>
        <td>".$rec["Transactietype"]."</td>
        <td class='al'>".$rec["Fonds"]."</td>
        <td class='al'>".$rec["rekMutVal"]."</td>
        <td class='ar'>".$fmt->format("@N{0}", $rec["Aantal"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $rec["Fondskoers"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", ($rec["Debet"] <> 0)?$rec["Debet"]:$rec["Credit"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $rec["Valutakoers"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $rec["Bedrag"])."</td>
        
        <td class='ar'>".$fmt->format("@N{0}", $saldo)."</td>
      </tr>
      ";

    }
   echo implode("\n",array_reverse($out));  // ivm saldering kan de query ORDER niet DESC zijn
?>
   </table>
   </article>


<?
    break;
  case "2":
    /*
    **   Tab Dividenden
    */
    $query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '".$mainRec["portefeuille"]."'";
    $portRec = $db->lookupRecordByQuery($query);
    $rapportageValuta = (trim($portRec["RapportageValuta"]) == "")?"EUR":$portRec["RapportageValuta"];
    $rapportageDatum = $mainRec["rapportDatum"];

    $query = "SELECT * FROM Valutakoersen WHERE Valuta = '".$rapportageValuta."' AND Datum <= '".$rapportageDatum."' ORDER BY Datum DESC";
    $valutaRec = $db->lookupRecordByQuery($query);
    


    $templ->loadTemplateFromFile("getDividenden.sql","getDividenden");
    $query = $templ->parseBlock("getDividenden",array("fonds" => $mainRec["fonds"], "portefeuille" => $mainRec["portefeuille"]));
    $db->executeQuery($query);
    ?>

    <article class="extraInfoiArticle">
      <table class="extraInfoTable">
        <tr class="trHeader">
          <td><?=vt("Datum")?></td>
          <td><?=vt("Soort")?></td>
          <td class="ac"><?=vt("Valuta")?></td>
          <td class="ar"><?=vt("Bedrag")?></td>
          <td class="ar"><?=vt("Valutakoers")?></td>
          <td class="ar"><?=vtb("Rekening %s Valuta",array("<br/>")); ?></td>
          <td class="ar"><?= vtb('Afreken %s Bedrag',array("<br/>")); ?></td>

        </tr>

        <?
        $saldo = 0;
        while ($rec = $db->nextRecord())
        {

          $saldo += $rec["Aantal"];
          echo "
          <tr>
            
            <td>".$fmt->format("@D {d}-{m}-{Y}", $rec["Boekdatum"])."</td>
            <td >".$gb[$rec["Grootboekrekening"]]."</td>
            <td class='ac'>".$rec["Valuta"]."</td>
            
            <td class='ar'>".$fmt->format("@N{.2}", $rec["BedragInValuta"])."</td>
            <td class='ar'>".$fmt->format("@N{.4}", $rec["Valutakoers"])."</td>
            <td class='ar'>".$rec["rekValuta"]."</td>
            <td class='ar'>".$fmt->format("@N{.2}", $rec["Bedrag"])."</td>
            
          </tr>
          ";
          $sum += $rec["Bedrag"];
        }
        ?>
<!--
        <tr>
          <td>&nbsp;</td>
          <td colspan='2' class='ar'></td>
          <td class='ar'><hr/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td colspan='2' class='ar'>Totaal dividend: </td>
          <td class='ar'><?=$fmt->format("@N{.2}", $sum)?></td>
        </tr>
-->
      </table>
    </article>
    <?
    break;
  case "3":
    /*
    **   Tab Fonds statics
    */

    $query = "SELECT * FROM Fondsen WHERE fonds = '".$mainRec["fonds"]."'";
    $rec = $db->lookupRecordByQuery($query);
    if ($rec["fondssoort"] == "OBL" OR $rec["fondssoort"] == "OVERIGE")
    {
      $query = "SELECT * FROM Rentepercentages WHERE Fonds='".$mainRec["fonds"]."' ORDER BY Datum DESC";
      if ($renteRec = $db->lookupRecordByQuery($query))
      {
        $rente = $fmt->format("@N{.3}%",$renteRec["Rentepercentage"])." (Vanaf ".$fmt->format("@D {d}-{m}-{Y}",$renteRec["Datum"]).")";
      }
      else
      {
        $rente = "geen rente gevonden";
      }
      $extraData = "
      <tr>
        <td>Huidige rentepercentage:</td>
        <td>$rente</td>
      </tr>
      ";
    }
    if (trim($mainRec["beleggingscategorieOmschrijving"]) <> "")
    {
      $extraData .= "
      <tr>
        <td>Beleggingscategorie:</td>
        <td>".trim($mainRec["beleggingscategorieOmschrijving"])."</td>
      </tr>
      ";
    }
    if (trim($mainRec["afmCategorie"]) <> "")
    {
      $extraData .= "
      <tr>
        <td>AFM categorie:</td>
        <td>".trim($mainRec["afmCategorie"])."</td>
      </tr>
      ";
    }
    if (trim($mainRec["attributieCategorieOmschrijving"]) <> "")
    {
      $extraData .= "
      <tr>
        <td>Attributiecategorie:</td>
        <td>".trim($mainRec["attributieCategorieOmschrijving"])."</td>
      </tr>
      ";
    }
    if (trim($mainRec["regioOmschrijving"]) <> "")
    {
      $extraData .= "
      <tr>
        <td>Regio:</td>
        <td>".trim($mainRec["regioOmschrijving"])."</td>
      </tr>
      ";
    }
    if (trim($mainRec["beleggingssectorOmschrijving"]) <> "")
    {
      $extraData .= "
      <tr>
        <td>Sector:</td>
        <td>".vtbv(trim($mainRec["beleggingssectorOmschrijving"]))."</td>
      </tr>
      ";
    }
    if (trim($rec["rating"]) <> "")
    {
      $extraData .= "
      <tr>
        <td>Rating:</td>
        <td>".trim($rec["rating"])."</td>
      </tr>
      ";
    }

    if (trim($rec["KIDformulier"]) <> "" AND $ms->allowed(3,4)) // call 7630
    {
      $extraData .= "
      <tr>
        <td>KID formulier:</td>
        <td><a href='".trim($rec["KIDformulier"])."' target='_blank'><button> ophalen </button></a></td>
      </tr>
      ";
    }



?>
    <article class="extraInfoiArticle">
    <table class="extraInfoTable">
      <tr>
        <td>Fondscode:</td>
        <td><?=$rec["Fonds"]?></td>
      </tr>
      <tr>
        <td>Fondsomschrijving:</td>
        <td><?=$rec["Omschrijving"]?></td>
      </tr>
      <tr>
        <td>Valuta:</td>
        <td><?=$rec["Valuta"]?></td>
      </tr>
      <tr>
        <td>Fondseenheid:</td>
        <td><?=$rec["Fondseenheid"]?></td>
      </tr>
      <tr>
        <td>ISIN-code:</td>
        <td><?=$rec["ISINCode"]?></td>
      </tr>
      <tr>
        <td>Fondssoort:</td>
        <td><?=$rec["fondssoort"]?></td>
      </tr>

      <?=$extraData?>
      </table>
    </article>
<?
    break;
  case "4":
  case "4_2y":
  case "4_5y":

    /*
    **   Tab koersverloop
    */

    $query = "SELECT * FROM Fondsen WHERE Fonds = '".$mainRec["fonds"]."' ";
    $fondsRec = $db->lookupRecordByQuery($query);
    $query = "SELECT * FROM Fondskoersen WHERE Fonds = '".$mainRec["fonds"]."' ORDER BY Datum DESC";
    $koersRec = $db->lookupRecordByQuery($query);
    if ($mainRec["rapportDatum"] == "0000-00-00")
    {
      $thisYear = date("Y");
    }
    else
    {
      $thisYear = substr($mainRec["rapportDatum"],0,4);
    }

    switch ($_GET["tab"])
    {
      case "4_2y":
        $query = "SELECT Datum, Koers FROM Fondskoersen WHERE fonds = '".$mainRec["fonds"]."' AND  Datum >= '".($thisYear-2)."-01-01' ORDER BY Datum ";
        $title = vtb("Koersverloop afgelopen 2 jaar");
        break;
      case "4_5y":
        $query = "SELECT Datum, Koers FROM Fondskoersen WHERE fonds = '".$mainRec["fonds"]."' AND  Datum >= '".($thisYear-5)."-01-01' ORDER BY Datum ";
        $title = vtb("Koersverloop afgelopen 5 jaar");
        break;
      default:
        $query = "SELECT Datum, Koers FROM Fondskoersen WHERE fonds = '".$mainRec["fonds"]."' AND  Datum < '".$thisYear."-01-01' ORDER BY Datum DESC LIMIT 1";
        $db->executeQuery($query);
        while ($rec = $db->nextRecord())
        {
          $out[] = $rec;
          $dates["yearEnd"] = $rec;
        }
        $query = "SELECT Datum, Koers FROM Fondskoersen WHERE fonds = '".$mainRec["fonds"]."' AND  Datum >= '".$thisYear."-01-01' ORDER BY Datum ";
        $title = vtb("Koersverloop vanaf 1 januari");
    }


    $out = array();
    $db->executeQuery($query);
    $firstRec = true;
    while ($rec = $db->nextRecord())
    {
      if ($firstRec)
      {
        $firstRec = false;
        $dates["yearFirst"] = $rec;
      }
      $out[] = $rec;
    }

    $indx = 0;
    $minKoers = 999999;
    for ($x=0; $x<count($out);$x++)
    {
      $indx++;
      $labels[] = "          { label: '".$fmt->format("@D {d}-{m}-{Y}",$out[$x]["Datum"])."', x: $indx,  y: ".$fmt->format("@N{.2R}",$out[$x]["Koers"])." },";
      $minKoers = ($minKoers > $out[$x]["Koers"])?$out[$x]["Koers"]:$minKoers;
    }

    $query="SELECT year(Datum) as jaar FROM Fondskoersen WHERE Fonds='".$mainRec["fonds"]."' AND Datum >= '2004-01-01' GROUP BY jaar ORDER By jaar";
    $db->executeQuery($query);
    $jaren=array();

    while($data= $db->nextRecord())
    {
      $jaren[] = $data['jaar'];
    }

    $koersenHtml="
    <table class='extraInfoTable' style='width: 100%;'>
      <tr>
        <td class='al b' >" . vt('Datum') . "</td>
        <td class='al b' >" . vt('Koers') . "</td>
      </tr>
      ";

    foreach ($jaren as $jaar)
    {
      $query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='".$mainRec["fonds"]."' AND year(Datum)='$jaar' ORDER BY Datum DESC";
      $data= $db->lookupRecordByQuery($query);
      $koersenHtml.="
      <tr>
        <td>".$fmt->format("@D {form}",$data['Datum'])."</td>
        <td class='ar'>".$data['Koers']."</td>
      </tr>
      ";
    }
    $koersenHtml.="</table>\n";

    $laatsteKoers = getLaatsteValutadatum();

    $perioden =array(
      '1-jaars' => 'date("'.$laatsteKoers.'")-interval 1 year',
      '3-jaars' => 'date("'.$laatsteKoers.'")-interval 3 year',
      '5-jaars' => 'date("'.$laatsteKoers.'")-interval 5 year');

    $koersenHtml .= "
    <hr/>
    <table class='extraInfoTable' style='width: 100%;'  >
      <tr >
        <td class='b' >" . vt('Periode') . "</td>
        <td class='b ar'>" . vt('STDDEV') . "</td>
        <td class='b ar'>" . vt('Aantal') . "</td>
      </tr>
      ";

    foreach($perioden as $periode=>$wherePeriode)
    {
      $query = "SELECT Datum FROM Fondskoersen WHERE Fonds='".$mainRec["fonds"]."' AND Datum < $wherePeriode";
      $beschikbaar = $db->lookupRecordByQuery($query);

      $query = "SELECT koers FROM Fondskoersen WHERE Fonds='".$mainRec["fonds"]."' AND Datum > $wherePeriode";
      $db->executeQuery($query);

      unset($laatsteKoers);
      $koersRendementen = array();
      while($data= $db->nextRecord())
      {
        if(isset($laatsteKoers) && $laatsteKoers != 0)
        {
          $koersRendementen[] = ($data['koers']/$laatsteKoers)*100;
        }
        $laatsteKoers = $data['koers'];
      }

      $sdtev = standard_deviation($koersRendementen);

      if ($beschikbaar['Datum'] == '')
      {
        $stddeviatieJaar='na';
      }
      else
      {
        $jaren = substr($periode,0,1);
        $stddeviatieJaar=round($sdtev*pow((count($koersRendementen)/$jaren),0.5),2);
        //echo "$stddeviatieJaar=round($sdtev*pow((".count($koersRendementen)."/$jaren),0.5),2);<br>\n";
      }

      $koersenHtml.="
        <tr>
          <td>$periode</td>
          <td align=right>$stddeviatieJaar</td>
          <td align=right>".count($koersRendementen)."</td>
        </tr>
        ";
    }
    $koersenHtml.="</table>\n";


?>
    <article class="extraInfoiArticle" >
    <table class="extraInfoTable" >
      <tr>
        <td>
          <button id="btnYtd"><?= vt('vanaf 1 januari'); ?></button> &nbsp;&nbsp;<button id="btn2Y"><?= vt('2 jaar'); ?></button> &nbsp;&nbsp;<button id="btn5Y"><?= vt('5 jaar'); ?></button>
          <?= vtb('Laatste koers'); ?>: <?=$fmt->format("@N{.2}",$koersRec["Koers"])." ".$rec["Valuta"]." d.d.:".$fmt->format("@D{d}-{m}-{Y}",$koersRec["Datum"])?><br/>
          <br/>
<?
  if ($fondsRec["koersBron"] == 1 AND $fondsRec["identifierVWD"] <> "")
  {
    echo "VWD-code: ".$fondsRec["identifierVWD"]."<br/>";
  }
?>
          <div class="extraInfoChart" id="chartContainer"></div>

        </td>
        <td style="width: 210px">
          <?=$koersenHtml?>
        </td>
      </tr>
    </table>

    </div>

    </article>

    <script type="text/javascript" src="../javascript/canvasjs.min.js"></script>
    <script>
      $(document).ready(function ()
      {
<?
        switch($_GET["tab"])
        {
          case "4_2y":
?>
        $("#btnYtd").prop("disabled",false);
        $("#btn2Y").prop("disabled",true);
        $("#btn5Y").prop("disabled",false);

<?
            break;
          case "4_5y":
?>
        $("#btnYtd").prop("disabled",false);
        $("#btn2Y").prop("disabled",false);
        $("#btn5Y").prop("disabled",true);

<?
            break;
          default:
?>
        $("#btnYtd").prop("disabled",true);
        $("#btn2Y").prop("disabled",false);
        $("#btn5Y").prop("disabled",false);

<?
        }
?>
        $("#btnYtd").click(function ()
        {
          $("#infoTab4").load("volkExtraInfo.php?tab=4&id=<?=$_GET["id"]?>");
        });
        $("#btn2Y").click(function ()
        {
          $("#infoTab4").load("volkExtraInfo.php?tab=4_2y&id=<?=$_GET["id"]?>");
        });
        $("#btn5Y").click(function ()
        {
          $("#infoTab4").load("volkExtraInfo.php?tab=4_5y&id=<?=$_GET["id"]?>");
        });
      })

      var chart = new CanvasJS.Chart("chartContainer", {
        theme: "theme4",
        title:{
          text: "<?=$title?>"
        },
        animationEnabled: true,
        axisX:{
          labelAngle: -50,
        },
        axisY:{
          minimum: <?=.9*$minKoers?>,
          title: "koers",
        },
        data: [
          {
            type: "area",
            color: "#B9B9EB",
            dataPoints: [
              <?=implode("\n",$labels);?>
            ]
          }
        ]
      });

      chart.render();
//      var chart2 = new CanvasJS.Chart("chartContainer2", {
//        theme: "theme4",
//        title:{
//          text: "koersverloop laatste 24 maanden"
//        },
//        animationEnabled: true,
//        axisX:{
//          labelAngle: -50,
//        },
//        axisY:{
//          minimum: <?//=.9*$minKoers2?>//,
//          title: "koers",
//        },
//        data: [
//          {
//            type: "area",
//            color: "#2A31FF",
//            dataPoints: [
//              <?//=implode("\n",$labels2);?>
//            ]
//          }
//        ]
//      });

//      chart2.render();
    </script>
<?
    break;
  case "1R":
  case "1R_1":
  case "1R_H":
    /*
    **   Tab transacties
    */

    switch($_GET["tab"])
    {
      case "1R_1":
        $startDisp = (date("Y", db2jul($_GET['stop'])) - 1) . '-'. (date("m-d", db2jul($_GET['stop'])));;
        $startDate = __julDag($startDisp);
        $date = (date("Y", db2jul($_GET['stop'])) - 1) . '-01-01';
        $kopTxt = "vanaf ". date('d-m-Y', db2jul($startDisp));
        break;
      case "1R_H":
        $date = "1971-01-01";
        $kopTxt = "beschikbare historie";
        break;
      default:
        $date = (date("Y", db2jul($_GET['stop']))) . "-01-01";
        $kopTxt = "vanaf 01-01-".(date("Y", db2jul($_GET['stop'])));
    }
    $dateStop = $_GET['stop'];
    /*
    **   Tab rekeningmutaties
    */
    ?>
    <script>
      $(document).ready(function(){
        $("#titleTab1").text("Rekenmutaties");
        $("#titleTab2").text("Grootboekselectie");
        $("#titleTab3").hide();
        $("#titleTab4").hide();
      });
    </script>
    <?
    $templ->loadTemplateFromFile("getRekeningmutaties_YTD.sql","getRekeningmutaties_YTD");
    $query = $templ->parseBlock("getRekeningmutaties_YTD",array(
      "rekening" => $mainRec["rekening"],
      "ytd" => $date,
      "dateStop" => $dateStop,
    ));

    $db->executeQuery($query);
    ?>
    <table class="extraInfoTable" >
      <tr>
        <td>
          <button id="btnTRYtd">vanaf 1 januari</button> &nbsp;&nbsp;
          <button id="btnTR1Y">1 jaar</button> &nbsp;&nbsp;
          <button id="btnTRhis">hele historie</button>
          <br/><br/>
        </td>
      </tr>
    </table>
    <h1><?= vt('rekeningmutaties'); ?>, <?=$kopTxt?> </h1>

    <article class="extraInfoiArticle">
      <table class="extraInfoTable">
        <tr class="trHeader">
          <td><?= vt('Datum'); ?></td>
          <td><?= vt('Omschrijving'); ?></td>
          <td><?= vt('Grootboek'); ?></td>
          <td class="ar"><?= vt('Bedrag'); ?></td>
          <td class="ar"><?= vt('Saldo'); ?></td>
        </tr>
        <?
        $saldo = 0;
        $firstRec = true;
        $printFirstRow = false;

        while ($rec = $db->nextRecord())
        {
          $jDatum = __julDag($rec["Boekdatum"]);
          if (!$firstRec AND ($startDate > 0) AND ($jDatum >= $startDate) AND !$printFirstRow)
          {
            $printFirstRow = true;
            $out[] = "
      <tr>
        <td>".$fmt->format("@D {d}-{m}-{Y}", $pRec["Boekdatum"])."</td>
        <td class='al'>".$pRec["rekeningOmschrijving"]."</td>
        <td >&nbsp;</td>
        <td class='ar'>".$fmt->format("@N{.2}", $pRec["Bedrag"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $saldo)."</td>
      </tr>
      ";
          }
          if (!$firstRec AND ($startDate > 0) AND ($jDatum < $startDate))
          {
            $saldo += $rec["Bedrag"];
            $rec["rekeningOmschrijving"] = vt('Saldo per') . " ".$fmt->format("@D {d}-{m}-{Y}", $rec["Boekdatum"]);
            $rec["Bedrag"] = 0;

            $pRec = $rec;
            if (!$printFirstRow) continue;
          }

          if ( $firstRec )
          {

            $saldo = $rec["Bedrag"];
            $firstRec = false;

            if ( date('m-d', strtotime($rec["Boekdatum"])) == '01-01' ) {
              $rec["Bedrag"] = 0;
              $rec["rekeningOmschrijving"] = "Beginsaldo 1 januari";
            }

            if (($startDate > 0) AND ($jDatum < $startDate))
            {
              continue;
            }

          }
          else
          {
            if ($rec["Grootboekrekening"] == "VERM") continue;  // call 9684
            if ($rec["Transactietype"] == "B") continue; // verdere beginboekingen overslaan
            $saldo += $rec["Bedrag"];
          }



          $out[] = "
      <tr>
        <td>".$fmt->format("@D {d}-{m}-{Y}", $rec["Boekdatum"])."</td>
        <td class='al'>".$rec["rekeningOmschrijving"]."</td>
        <td >". $gb[$rec["Grootboekrekening"]]."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $rec["Bedrag"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $saldo)."</td>
      </tr>
      ";

        }
        echo implode("\n",array_reverse($out));  // ivm saldering kan de query ORDER niet DESC zijn
        ?>
      </table>
    </article>
  <script>
    $(document).ready(function()
    {
      <?

      switch($_GET["tab"])
      {
      case "1R_1":
      ?>
      $("#btnTRYtd").prop("disabled",false);
      $("#btnTR1Y").prop("disabled",true);
      $("#btnTRhis").prop("disabled",false);

      <?
      break;
      case "1R_H":
      ?>
      $("#btnTRYtd").prop("disabled",false);
      $("#btnTR1Y").prop("disabled",false);
      $("#btnTRhis").prop("disabled",true);

      <?
      break;
      default:
      ?>
      $("#btnTRYtd").prop("disabled",true);
      $("#btnTR1Y").prop("disabled",false);
      $("#btnTRhis").prop("disabled",false);

      <?
      }
      ?>
      $("#btnTRYtd").click(function ()
      {
        $("#infoTab1").load("volkExtraInfo.php?tab=1R&id=<?=$_GET["id"]?>&stop=<?=$_GET['stop'];?>");
      });
      $("#btnTR1Y").click(function ()
      {
        $("#infoTab1").load("volkExtraInfo.php?tab=1R_1&id=<?=$_GET["id"]?>&stop=<?=$_GET['stop'];?>");
      });
      $("#btnTRhis").click(function ()
      {
        $("#infoTab1").load("volkExtraInfo.php?tab=1R_H&id=<?=$_GET["id"]?>&stop=<?=$_GET['stop'];?>");
      });
    });
  </script>
    <?
    break;
  case "2R":
  case "2R_1":
  case "2R_H":
    if ($_GET["GBgrootboek"] == "")
    {
      $_GET["GBgrootboek"] = "DIV";

    }
    /*
    **   Tab grootboekselectie
    */
    ?>
    <script>
      $(document).ready(function(){
        $("#titleTab1").text("Rekenmutaties");
        $("#titleTab2").text("Grootboekselectie");
        $("#titleTab3").hide();
        $("#titleTab4").hide();
      });
    </script>
    <?
    $query = "
    SELECT DISTINCT
      Rekeningmutaties.Grootboekrekening AS Grootboekrekening,
      CONCAT('(', Grootboekrekeningen.Grootboekrekening, ') ', Grootboekrekeningen.Omschrijving ) AS oms
    FROM
      Rekeningmutaties
    INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
    WHERE
      Rekeningmutaties.Rekening = '".$mainRec["rekening"]."'
    AND Rekeningmutaties.Verwerkt = '1'
    ORDER BY
      Rekeningmutaties.Grootboekrekening
    ";
// todo: als de grootboekselectie goed gaat kan onderstaande weg
//    $query = "
//    SELECT
//      Grootboekrekening,
//      CONCAT('(',Grootboekrekening,') ',Omschrijving) AS oms
//    FROM
//      Grootboekrekeningen
//    WHERE   Grootboekrekening NOT IN ('kruis','mem')
//    ORDER BY
//      Grootboekrekening";

    $db->executeQuery($query);
    $gbArray = array();
    while($rec = $db->nextRecord())
    {

      $selected = ($rec["Grootboekrekening"] == $_GET["GBgrootboek"])?"selected":"";
      $options .= "
      <option value='".$rec["Grootboekrekening"]."' $selected>".$rec["oms"]."</option>";
      $gbArray[$rec["Grootboekrekening"]] = $rec["oms"];
    }

    switch($_GET["tab"])
    {
      case "2R_1":
        $startDisp = (date("Y", db2jul($_GET['stop'])) - 1) . '-'. (date("m-d", db2jul($_GET['stop'])));;
        $date = (date("Y", db2jul($_GET['stop'])) - 1) . '-01-01';
        $kopTxt = "vanaf ". date('d-m-Y', db2jul($startDisp));
        break;
      case "2R_H":
        $date = "1971-01-01";
        $kopTxt = "beschikbare historie";
        break;
      default:
        $date = (date("Y", db2jul($_GET['stop']))) . "-01-01";
        $kopTxt = "vanaf 01-01-".(date("Y", db2jul($_GET['stop'])));
    }
    $dateStop = $_GET['stop'];

    $templ->loadTemplateFromFile("getRekeningmutaties_per_GB.sql","getRekeningmutaties_per_GB");
    $query = $templ->parseBlock("getRekeningmutaties_per_GB",
                                 array("rekening"  => $mainRec["rekening"],
                                       "datum"     => $date,
                                       "dateStop" => $dateStop,
                                       "grootboek" => $_GET["GBgrootboek"]));

    $db->executeQuery($query);
    ?>
    <table class="extraInfoTable" >
      <tr>
        <td>
          Grootboek: <select id="GBgrootboek" name="GBgrootboek">
            <?=$options?>
          </select>&nbsp;&nbsp;&nbsp;
          <button id="btnGBYtd">vanaf 1 januari</button> &nbsp;&nbsp;
          <button id="btnGB1Y">1 jaar</button> &nbsp;&nbsp;
          <button id="btnGBhis">hele historie</button>
          <br/><br/>
        </td>
      </tr>
    </table>
    <h1><?= vtb('Mutaties van'); ?> <?=$mainRec["rekening"]?>, <?=$gbArray[$_GET["GBgrootboek"]]?>, <?=$kopTxt?> </h1>
    <article class="extraInfoiArticle">
      <table class="extraInfoTable">
        <tr class="trHeader">
          <td><?= vt('Datum'); ?></td>
          <td><?= vt('Omschrijving'); ?></td>
          <td><?= vt('Grootboek'); ?></td>
          <td class="ar"><?= vt('Bedrag'); ?></td>
          <td class="ar"><?= vt('Saldo'); ?></td>
        </tr>
        <?
        $saldo = 0;
        $firstRec = true;
        while ($rec = $db->nextRecord())
        {

          $saldo += $rec["Bedrag"];

          $out[] = "
      <tr>
        <td>".$fmt->format("@D {d}-{m}-{Y}", $rec["Boekdatum"])."</td>
        <td class='al'>".$rec["rekeningOmschrijving"]."</td>
        <td >".$gb[$rec["Grootboekrekening"]]."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $rec["Bedrag"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $saldo)."</td>
      </tr>
      ";

        }
        echo implode("\n",array_reverse($out));  // ivm saldering kan de query ORDER niet DESC zijn
        ?>
      </table>
    </article>
    <script>
      $(document).ready(function()
      {
        $("#GBgrootboek").change(function ()
        {

          $("#infoTab2").load("volkExtraInfo.php?tab=<?=$_GET["tab"]?>&id=<?=$_GET["id"]?>&stop=<?=$_GET['stop'];?>&GBgrootboek="+$(this).val());
//          $("#btnGBYtd").prop("disabled",false);
//          $("#btnGB1Y").prop("disabled",false);
//          $("#btnGBhis").prop("disabled",false);
        });
<?



      switch($_GET["tab"])
      {
        case "2R_1":
          ?>
          $("#btnGBYtd").prop("disabled",false);
          $("#btnGB1Y").prop("disabled",true);
          $("#btnGBhis").prop("disabled",false);

        <?
        break;
        case "2R_H":
        ?>
          $("#btnGBYtd").prop("disabled",false);
          $("#btnGB1Y").prop("disabled",false);
          $("#btnGBhis").prop("disabled",true);

        <?
        break;
        default:
        ?>
          $("#btnGBYtd").prop("disabled",true);
          $("#btnGB1Y").prop("disabled",false);
          $("#btnGBhis").prop("disabled",false);

        <?
        }
        ?>
          $("#btnGBYtd").click(function ()
          {
            $("#infoTab2").load("volkExtraInfo.php?tab=2R&id=<?=$_GET["id"]?>&stop=<?=$_GET['stop'];?>&GBgrootboek="+$("#GBgrootboek").val());
          });
          $("#btnGB1Y").click(function ()
          {
            $("#infoTab2").load("volkExtraInfo.php?tab=2R_1&id=<?=$_GET["id"]?>&stop=<?=$_GET['stop'];?>&GBgrootboek="+$("#GBgrootboek").val());
          });
          $("#btnGBhis").click(function ()
          {
            $("#infoTab2").load("volkExtraInfo.php?tab=2R_H&id=<?=$_GET["id"]?>&stop=<?=$_GET['stop'];?>&GBgrootboek="+$("#GBgrootboek").val());
          });
      });
    </script>
<?
    break;

  case '5':

    $DB = new DB();



    $query = "SELECT msCategoriesoort, date(max(datumVanaf)) as vanaf FROM doorkijk_categorieWegingenPerFonds WHERE fonds='" . mysql_real_escape_string($mainRec["fonds"]) . "' GROUP BY msCategoriesoort";
    $DB->SQL($query);
    $DB->Query();
    $categorieSoortVanaf = array();
    while ($doorkijk = $DB->nextRecord())
    {
      $categorieSoortVanaf[$doorkijk['vanaf']][] = $doorkijk['msCategoriesoort'];
    }
  


    foreach ($categorieSoortVanaf as $vanaf => $categorieSoorten)
    {
      $query = "SELECT 
        doorkijk_categorieWegingenPerFonds.msCategoriesoort,
        doorkijk_categorieWegingenPerFonds.msCategorie,
        doorkijk_categorieWegingenPerFonds.weging,
        doorkijk_categorieWegingenPerFonds.datumVanaf,
        doorkijk_msCategoriesoort.omschrijving,
        doorkijk_msCategoriesoort.grafiekKleur
      
          FROM doorkijk_categorieWegingenPerFonds
          
          LEFT JOIN doorkijk_msCategoriesoort ON doorkijk_categorieWegingenPerFonds.msCategorie=doorkijk_msCategoriesoort.msCategorie
            AND doorkijk_categorieWegingenPerFonds.msCategoriesoort=doorkijk_msCategoriesoort.msCategoriesoort
          
          WHERE doorkijk_categorieWegingenPerFonds.fonds='" . mysql_real_escape_string($mainRec["fonds"]) . "' 
          AND doorkijk_categorieWegingenPerFonds.datumVanaf>='$vanaf' 
          AND doorkijk_categorieWegingenPerFonds.msCategoriesoort IN('" . implode("','", $categorieSoorten) . "')
          
          ORDER BY 
            doorkijk_categorieWegingenPerFonds.msCategoriesoort,
            doorkijk_categorieWegingenPerFonds.msCategorie,doorkijk_categorieWegingenPerFonds.datumVanaf
      ";
      $DB->SQL($query);
      $DB->Query();
      $wegingTotaal = 0;

      $doorkijkCatNew = 1;
      $firstLoop = 1;

      while ($doorkijk = $DB->nextRecord())
      {
        list($r, $g, $b) = unserialize($doorkijk['grafiekKleur']);
        $doorkijk['grafiekKleur'] = sprintf('%.0f, %.0f, %.0f', $r, $g, $b);

        if ($doorkijk['omschrijving'] <> '')
        {
          $msOmschrijving = $doorkijk['omschrijving'];
        }
        else
        {
          $msOmschrijving = $doorkijk['msCategorie'];
        }

        $doorkijk['msOmschrijving'] = $msOmschrijving;
        $doorkijkArray[$doorkijk['msCategoriesoort']][] = $doorkijk;
      }
    }
      if (empty ($doorkijkArray))
      {
        echo '
        Geen gegevens gevonden!
        <script>
          $(function () {
            $("#titleTab5").hide();
          })
        </script>
      ';
      }
      else
      {
        echo '
        <script>
          $(function () {
            $("#titleTab5").show();
          })
        </script>
      ';
      }
  
   
    
      foreach ($doorkijkArray as $msCategoriesoort => $doorkijkDatas)
      {

        if (!$ms->allowed(2,4))  // call 7630
        {
          if (!in_array($msCategoriesoort, $ms->doorkijkStandaard))
          {
            continue;
          }
        }

        $wegingTotaal = 0;
        $editObject->formVars['doorkijkDetails'] .= '<div style="clear: both;min-height:250px; margin-bottom: 40px;    display: inline-block;  "><div class="doorkijkLeft" style="float: left;display: inline;width: 600px;">';
        $editObject->formVars['doorkijkDetails'] .= '<div class="formblock"><div class="formlinks" style="width: 400px;"> <b>' . $msCategoriesoort . '</b> </div> <div class="formrechts"><br>' . vt('vanaf') . ' ' . date('d-m-Y', strtotime($doorkijkDatas[0]['datumVanaf'])) . '</div></div>';
        $count = 1;
        $type = 'pi';
        foreach ($doorkijkDatas as $doorkijk)
        {
          $editObject->formVars['doorkijkDetails'] .= '<div class="formblock"><div class="formlinks" style="width: 400px;"><span class="fa fa-circle" style="font-size: 15px; color: rgb(' . $doorkijk['grafiekKleur'] . ')"></span> ' . $doorkijk['msOmschrijving'] . ' </div>
        <div class="formrechts"> <span style="text-align: right;display: inline-block;width:100px">' . formatGetalGlobal($doorkijk['weging'], 2) . '%</span> </div></div>';
      
          $catDatas[$doorkijk['msCategoriesoort']]['datapie'][] = array(
            'y'     => $doorkijk['weging'],
            'name'  => $doorkijk['msOmschrijving'],
            'color' => 'rgb(' . $doorkijk['grafiekKleur'] . ')'
          );
      
          $catDatas[$doorkijk['msCategoriesoort']]['datapcolumn'][] = array(
            'x'     => $count++,
            'y'     => number_format($doorkijk['weging'], 2),
            'label' => $doorkijk['msOmschrijving'],
            'color' => 'rgb(' . $doorkijk['grafiekKleur'] . ')'
          );
      
          if ($doorkijk['weging'] < 0)
          {
            $type = 'bar';
          }
          $catDatas[$doorkijk['msCategoriesoort']]['type'] = $type;
          $wegingTotaal += $doorkijk['weging'];
        }
    
        if ($wegingTotaal != 0)
        {
          $editObject->formVars['doorkijkDetails'] .= "<div class=\"formblock\"><div class=\"formlinks\" style=\"width: 400px;\"> Totaal </div> <div class=\"formrechts\"> <span style='text-align: right;display: inline-block;width:100px'>" . formatGetalGlobal($wegingTotaal, 2) . "%</span></div></div>\n";
        }
        $editObject->formVars['doorkijkDetails'] .= '</div><div class="doorkijkright" style="float: right; width: 300px;"><div  id="chartContainer_' . $msCategoriesoort . '" style="height: 200px; width: 100%;"></div><div id="test_' . $msCategoriesoort . '"></div></div></div>';
      }
  



echo $editObject->formVars['doorkijkDetails'];

?>

<script type="text/javascript" src="../javascript/canvasjs.min.js"></script>
  <script>
  $(document).ready(function ()
  {

    <?php

    foreach ( $catDatas as $catName => $catData ) {

      if ( $catData['type'] === 'bar' ) {
        echo '
        var jsonData = ' . json_encode($catData['datapcolumn']) . ';
          var dataPoints = [];
      
          for (var i = 0; i <= jsonData.length - 1; i++) {
            dataPoints.push({ 
              x: Number(jsonData[i].x),
              y: Number(jsonData[i].y),
              label: jsonData[i].label,
              color: jsonData[i].color,
              indexLabelFontColor: "black"
            });
          }

          var chart = new CanvasJS.Chart("chartContainer_' . $catName . '", {
            height:200,
            width:400,
            toolTip: {
    fontColor: "black",
 },
            data: [{
            
              type: "column",
              showInLegend: false,
              dataPoints: dataPoints,
               indexLabelFontColor: "black"
            }]
          });
          chart.render();
        ';
      }
      else
      {
        echo '
        
        var jsonData = ' . json_encode($catData['datapie']) . ';
		
          var dataPoints = [];
      
          for (var i = 0; i <= jsonData.length - 1; i++) {
            dataPoints.push({ 
              y: jsonData[i].y,
              name: jsonData[i].name,
              color: jsonData[i].color
            });
          }
       
          var chart = new CanvasJS.Chart("chartContainer_' . $catName . '", {
          
            height:200,
            width:400,
            exportEnabled: true,
             toolTip: {
                fontColor: "black",
             },
            data: [{
              type: "pie",
              showInLegend: false,
              dataPoints: dataPoints
            }]
          });
          chart.render();
        ';
      }
  }
    ?>
  });

  </script>


  <?php

    break;

  case '5R':
      echo '<script>$(function() {$("#titleTab5").hide();})</script>';
    break;
  default:
    echo "foute aanroep (".$_GET["tab"].")";
}

function __julDag($dbDatum)
{
  $parts = explode("-",$dbDatum);
  $julian = mktime(1,1,1,$parts[1],$parts[2],$parts[0]);
  return floor($julian / 86400);
}

?>



