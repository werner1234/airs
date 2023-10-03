<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2016/09/21 08:25:27 $
    File Versie         : $Revision: 1.1 $

    $Log: tijdelijkeReconPopup.php,v $
    Revision 1.1  2016/09/21 08:25:27  cvs
    call 5271

    Revision 1.6  2016/06/20 09:38:11  cvs
    aangepast grootboek selectie bij geldrekeningen popup



*/


include_once("wwwvars.php");

$db = new DB();
$fmt = new AE_cls_formatter(",",".");
$templ = new AE_template();
$templ->templatePath = getcwd()."/classTemplates/";
$templ->appendSubdirToTemplatePath("transactieOverzicht");

$query = "SELECT * FROM `tijdelijkeRecon` WHERE id = ".$_GET["id"];
$mainRec = $db->lookupRecordByQuery($query);
//debug($mainRec);
if ($mainRec["cashPositie"] == 1 AND !strstr($_GET["tab"],"R") )
{
  $_GET["tab"] .= "R";
}

?>
  <script>
    $("#ui-dialog-title-extraInfoPopup").html("Details <?=$mainRec["fondsOmschrijving"]?>");
  </script>
<?

switch ($_GET["tab"])
{
  case "1":
    /*
    **   Tab transacties
    */
    ?>
    <script>
      $(document).ready(function(){
        $("#titleTab1").text("Transacties");
      });
    </script>
    <?
    $templ->loadTemplateFromFile("getTransacties.sql","getTransacties");
    $query = $templ->parseBlock("getTransacties",array("fonds" => $mainRec["fonds"],
                                                       "portefeuille" => $mainRec["portefeuille"],
                                                       "boekDatum" => $mainRec["reconDatum"],
                                                       "ytd" => date("Y")."-01-01",
                                                       )
                                );
    $db->executeQuery($query);

    //debug($query);
    ?>
    <h1>transactie historie <?=$mainRec["portefeuille"]?>, fonds: <i> <?=$mainRec["fonds"]?></i> t/m <i><?=$fmt->format("@D{form}", $mainRec["reconDatum"])?></i></h1>
    <article class="extraInfoiArticle">
      <table class="extraInfoTable">
        <tr class="trHeader">
          <td>Datum</td>
          <td>TT</td>
          <td>Fonds</td>
          <td class="ar">Aantal</td>
          <td class="ar">Koers</td>
          <td class="ar">Saldo</td>
        </tr>
        <?
        $saldo = 0;
        while ($rec = $db->nextRecord())
        {
          $saldo += $rec["Aantal"];
          $out[] = "
      <tr>
        <td>".$fmt->format("@D {d}-{m}-{Y}", $rec["Boekdatum"])."</td>
        <td>".$rec["Transactietype"]."</td>
        <td class='al'>".$rec["rekeningOmschrijving"]."</td>
        <td class='ar'>".$fmt->format("@N{.4}", $rec["Aantal"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $rec["Fondskoers"])."</td>
        <td class='ar'>".$fmt->format("@N{.4}", $saldo)."</td>
      </tr>
      ";

        }
        echo implode("\n",array_reverse($out));  // ivm saldering kan de query ORDER niet DESC zijn
        ?>
      </table>
    </article>
    <?
    break;
  case "1R":
    /*
    **   Tab rekeningmutaties
    */
    ?>
    <script>
      $(document).ready(function(){
        $("#titleTab1").text("Rekenmutaties");

      });
    </script>
    <?
    $templ->loadTemplateFromFile("getRekeningmutaties.sql","getRekeningmutaties");
    $query = $templ->parseBlock("getRekeningmutaties",array("rekening" => $mainRec["rekeningnummer"], "boekDatum" => $mainRec["reconDatum"], "ytd" => date("Y")."-01-01")) ;
    //debug($query);
    $db->executeQuery($query);
    ?>
    <h1>Rekeningmutaties <?=$mainRec["rekeningnummer"]?> t/m  <i><?=$fmt->format("@D{form}", $mainRec["reconDatum"])?></i> (max 75 regels)</h1>
    <article class="extraInfoiArticle">
      <table class="extraInfoTable">
        <tr class="trHeader">
          <td>Datum</td>
          <td>Omschrijving</td>
          <td>GB</td>
          <td class="ar">Bedrag</td>
          <td class="ar">Saldo</td>
        </tr>
        <?
        $saldo = 0;
        $firstRec = true;
        while ($rec = $db->nextRecord())
        {
//          if ($firstRec)
//          {
//            $saldo = $rec["Bedrag"];
//            $firstRec = false;
//            $rec["rekeningOmschrijving"] = "Beginsaldo YTD";
//            $rec["Bedrag"] = 0;
//
//          }
//          else
//          {
            $saldo += $rec["Bedrag"];
//          }


          //debug($rec);

          $out[] = "
      <tr>
        <td>".$fmt->format("@D {d}-{m}-{Y}", $rec["Boekdatum"])."</td>
        <td class='al'>".$rec["rekeningOmschrijving"]."</td>
        <td class='ar'>".$fmt->format("@S{5}", $rec["Grootboekrekening"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $rec["Bedrag"])."</td>
        <td class='ar'>".$fmt->format("@N{.2}", $saldo)."</td>
      </tr>
      ";

        }
        echo implode("\n",array_reverse($out));  // ivm saldering kan de query ORDER niet DESC zijn
        ?>
      </table>
    </article>
    <?
    break;
  default:
    echo "foute aanroep (".$_GET["tab"].")";
}


?>