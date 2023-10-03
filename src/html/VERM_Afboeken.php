<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2018/12/17 15:11:04 $
  File Versie					: $Revision: 1.8 $

  $Log: VERM_Afboeken.php,v $
  Revision 1.8  2018/12/17 15:11:04  cvs
  consolidatie = 0 toegevoegd

  Revision 1.7  2018/06/12 14:35:48  cvs
  call 6974

  Revision 1.6  2017/10/27 13:36:07  cvs
  call 6296

  Revision 1.5  2017/10/27 12:49:02  cvs
  call 6296

  Revision 1.4  2017/10/27 12:35:10  cvs
  call 6296

  Revision 1.3  2017/04/03 13:30:31  cvs
  call 5532

  Revision 1.2  2016/07/22 07:53:33  cvs
  call 4746

  Revision 1.1  2016/07/18 12:53:24  cvs
  update 20160718

  Revision 1.1  2016/06/14 06:17:14  cvs
  call 4564 naar TEST

 */

include_once("wwwvars.php");

session_start();
$_SESSION["NAV"] = "";


global $USR;

$content['style2'] .=
  '
  <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">
  <link rel="stylesheet" href="style/AIRS_default.css">
  ';
echo template($__appvar["templateContentHeader"], $content);


/*
 * Verwerk selectie
 */
$done = false;
if ($_POST["verwerk"])
{
  $data = $_SESSION["VERMpost"];

  $db = new DB();
  if ($data["soortVERM"] == "VERM")
  {
    foreach ($_SESSION["VERMitems"] as $item)
    {

      $id = intval($item);
      if ($id > 0 AND isNumeric($item))
      {
        $query = "SELECT * FROM Rekeningmutaties WHERE id = $id";
        $oldRec = $db->lookupRecordByQuery($query);
        addTrackAndTrace("Rekeningmutaties", $item, "Grootboekrekening", $oldRec["Grootboekrekening"], "VERM", $USR);
        addTrackAndTrace("Rekeningmutaties", $item, "Fonds", $oldRec["Fonds"], "", $USR);
        addTrackAndTrace("Rekeningmutaties", $item, "Aantal", $oldRec["Aantal"], "0", $USR);
        addTrackAndTrace("Rekeningmutaties", $item, "Fondskoers", $oldRec["Fondskoers"], "0", $USR);
        addTrackAndTrace("Rekeningmutaties", $item, "Debet", $oldRec["Debet"], "0", $USR);
        addTrackAndTrace("Rekeningmutaties", $item, "Credit", $oldRec["Credit"], "0", $USR);
        addTrackAndTrace("Rekeningmutaties", $item, "Bedrag", $oldRec["Bedrag"], "0", $USR);
        addTrackAndTrace("Rekeningmutaties", $item, "Transactietype", $oldRec["Transactietype"], "", $USR);

        $query = "
        UPDATE
          Rekeningmutaties
        SET
          `Grootboekrekening` = 'VERM',
          `Fonds` = '',
          `Aantal`  = 0,
          `Fondskoers` = 0,
          `Debet` = 0,
          `Credit` = 0,
          `Bedrag` = 0,
          `Transactietype` = '',
          `change_date` = NOW(),
          `change_user` = '$USR'
        WHERE 
          id = $id";

        $db->executeQuery($query);
      }
    }
  }
  else
  {

    foreach ($_SESSION["VERMitems"] as $item)
    {
      $id = intval($item);
      if ($id > 0 AND isNumeric($item))
      {
        $query = "SELECT * FROM Rekeningmutaties WHERE id = $id";

        $oldRec = $db->lookupRecordByQuery($query);

        if ($data["soortVERM"] == "GB")
        {
          $qExtra = "`Grootboekrekening` = '{$data["grootboek"]}', ";
          addTrackAndTrace("Rekeningmutaties", $item, "Grootboekrekening", $oldRec["Grootboekrekening"], $data["grootboek"], $USR);
        }
        else
        {
          $qExtra = "`Omschrijving` = '{$data["omschrijving"]}', ";
          addTrackAndTrace("Rekeningmutaties", $item, "Omschrijving", $oldRec["Omschrijving"], $data["omschrijving"], $USR);
        }

        $query = "  
        UPDATE
          Rekeningmutaties
        SET
          $qExtra
          `change_date` = NOW(),
          `change_user` = '$USR'
        WHERE 
          id = $id";

        $db->executeQuery($query);
      }
    }
  }

  $done = true;
}

/*
 *  validatie scherm
 */

if ($_POST['posted'] OR $done)
{
  if (!$done)
  {
    $data = $_POST;

  }

  $_SESSION["VERMpost"] = $_POST;
  $fmt  = new AE_cls_formatter();
  $tmpl = new AE_template();
  $tmpl->appendSubdirToTemplatePath("VERMboekingen");
  $_error = array();
  if (!$done)
  {
    if ($_FILES['importfile']["error"] != 0)
    {
      $_error[] = "" . vt('Fout: bestand niet ingevuld of bestaat niet') . " (" . $_FILES['importfile']['name'] . ")";
    }
    if (empty($_error))
    {
      $row = 0;
      $fileData = file_get_contents($_FILES['importfile']['tmp_name']);
      unlink($_FILES['importfile']['tmp_name']);
      $fileData = explode("\n", $fileData);

      foreach ($fileData as $i)
      {
        $row++;
        if (trim($i) <> "" AND (int) $i > 0)
        {
          $items[] = trim($i);
        }
        else
        {
          $_error[] = "" . vt('regel') . " $row " . vt('bevat ongeldige waarde') . " (".trim($i).")";
        }

      }


    }
    if (count($_error) > 0)
    {
      echo "<h2>" . vt('fouten in VERM bestand') . "</h2><li>";
      echo implode("<li>",$_error);
      exit;
    }
    else
    {
      $_SESSION["VERMitems"] = $items;
    }
  }

  $db = new DB();
  $query = " 
   SELECT
    Portefeuilles.Vermogensbeheerder,
    Portefeuilles.Portefeuille,
    Rekeningmutaties.Rekening,
    Portefeuilles.Client,
    Rekeningmutaties.Id,
    Rekeningmutaties.Boekdatum,
    Rekeningmutaties.Grootboekrekening,
    Rekeningmutaties.Omschrijving,
    Rekeningmutaties.Bedrag
  FROM
    Rekeningmutaties
  INNER JOIN Rekeningen ON
    Rekeningmutaties.Rekening = Rekeningen.Rekening AND consolidatie = 0
  INNER JOIN Portefeuilles ON
    Rekeningen.Portefeuille = Portefeuilles.Portefeuille
  WHERE
    Rekeningmutaties.id IN (".implode(",",$_SESSION["VERMitems"]).")
  ";

  $tmpl->loadTemplateFromFile("kop_gbOms.html","VERMkop");
  $tmpl->loadTemplateFromFile("row_gbOms.html","VERMrow");

  switch($data["soortVERM"])
  {
    case "GB":
      $txt = "Nieuwe grootboekrekening wordt: <b>".$data["grootboek"]."</b>";
      break;
    case "OMS":
      $txt = "Nieuwe omschrijving wordt: <b>".$data["omschrijving"]."</b>";
      break;
    default:  // standaard VERM boeking
      $txt = "standaard VERM boeking";
      $query = "SELECT * FROM Rekeningmutaties WHERE id IN (".implode(",",$_SESSION["VERMitems"]).") ORDER BY id";
      $tmpl->loadTemplateFromFile("kop_verm.html","VERMkop");
      $tmpl->loadTemplateFromFile("row_verm.html","VERMrow");
  }

?>
  <style>
    .feedbackVERM{
      background: rgba(225,225,225,.5);
      border-radius: 5px;
      color: red;
      font-weight: normal;
      padding: 10px;
      margin-bottom: 20px;
      font-size: 1.2em;
    }
  </style>
  <div class="contentContainer">
  <h1><?= vt('verificatie boekingen'); ?></h1>
  <div class="feedbackVERM" ><?=$txt?></div>
  <table class='listTable' style="width: 95%">
<?
    echo $tmpl->parseBlock("VERMkop");

    $db->executeQuery($query);
    $tel = 0;
    while($rec = $db->nextRecord() )
    {
       $tel++;
       $rec["Boekdatum"] = $fmt->format("@D{d}-{m}-{Y}", $rec["Boekdatum"] );
       $rec["Aantal"]    = $fmt->format("@N {.2}", $rec["Aantal"] );
       $rec["Debet"]     = $fmt->format("@N {.2}", $rec["Debet"] );
       $rec["Credit"]    = $fmt->format("@N {.2}", $rec["Credit"] );
       $rec["Bedrag"]    = $fmt->format("@N {.2}", $rec["Bedrag"] );
       echo $tmpl->parseBlock("VERMrow",$rec);
    }
?>
    <tr>
      <td>&nbsp;</td>
      <td colspan="10">
        <?= vt('Totaal'); ?> <b><?=$tel?></b> <?= vt('VERM regels'); ?>
      </td>
    </tr>
    </table>
    <br/>
    <br/>

  <br/>
  <br/>
  <script>
    $(document).ready(function() {
      $(".feedbackMsg").show(300);
    });
  </script>
<?
    if ($done)
    {
      echo "<h2> " . vt('De aanpassingen zijn verwerkt') . ".. </h2>";
    }
    else
    {
      if ($row != $tel)
      {
        echo "<h2>" . vt('FOUT!: Aantal gevonden mutaties niet gelijk aan importbestand') . "</h2>";
        echo "<br/>" . vt('import bestand') . " $row " . vt('regels') . "";
        echo "<br/>" . vt('rekeningmutaties') . " $tel " . vt('items') . "";
      }
      else
      {


?>
      <form method="post" id="editForm">
        <input type="hidden" name="verwerk" value="true" />
        <button class="btnGreen" style="float: right;" id="btnSubmit"> <?= vt('bovenstaande regels verwerken'); ?> </button>
      </form>
  <script>
    $(document).ready(function () {
      $("#btnSubmit").click(function(e) {
        e.preventDefault();
        $("#editForm").submit();
      });
    });
  </script>
<?
      }
    }

    echo template($__appvar["templateRefreshFooter"], $content);
    exit;


}
/*
 *  Invoerscherm
 */

$db = new DB();
$query = "SELECT * FROM Grootboekrekeningen ORDER BY Grootboekrekening";
$db->executeQuery($query);
while ($rec = $db->nextRecord())
{
  $gbOptions .= "\n\t<option value='{$rec["Grootboekrekening"]}'>{$rec["Grootboekrekening"]}, {$rec["Omschrijving"]}</option>";
}

?>
  <form enctype="multipart/form-data"  method="POST"  name="editForm" id="editForm">
  <!-- MAX_FILE_SIZE must precede the file input field -->
  <input type="hidden" name="posted" value="true" />

  <div class="contentContainer">
    <h1><?= vt('VERM boekingen aanmaken'); ?></h1>

    <div class="feedbackMsg" id="msg"></div>
    <form enctype="multipart/form-data" method="POST"  action="CRM_naw_ImportFase2.php" name="editForm" id="editForm">
      <!--    <input type="hidden" name="profile" value="--><?//=$profile?><!--" />-->
      <fieldset>
        <legend><?= vt('Profiel'); ?></legend>
        <div class="formblock">
          <div class="formlinks"><input type="radio" name="soortVERM"  value="VERM" id="soortVERMVERM"checked><label for="soortVERMVERM" > <?= vt('VERM-boekingen'); ?></label></div>
        </div>
        <div class="formblock">
          <div class="formlinks"><input type="radio" name="soortVERM" id="soortVERMGB" value="GB"><label for="soortVERMGB"> <?= vt('Aanpassen grootboek'); ?></label></div>
          <div class="formrechts">
            <label for="soortVERMGB"><select name="grootboek" id="grootboek"><?=$gbOptions?></select></label>
          </div>
        </div>
        <div class="formblock">
          <div class="formlinks"><input type="radio" name="soortVERM" id="soortVERMOMS" value="OMS"><label for="soortVERMOMS"> <?= vt('Aanpassen omschrijving'); ?></label></div>
          <div class="formrechts">
            <label for="soortVERMOMS"><input name="omschrijving" id="omschrijving" placeholder=" aangepaste omschrijving" /></label>
          </div>
        </div>
      </fieldset>
      <br/><br/>
      <fieldset>
        <legend><?= vt('Data bestand'); ?></legend>
        <div class="formblock">
          <div class="formlinks"><label for="bestand" title="bestand"><?= vt('import bestand'); ?></label></div>
          <div class="formrechts">
            <input type="file" name="importfile" id="bestand" value="" />
          </div>
        </div>
      </fieldset>

      <br/>
      <br/>
      <div >

        <button class="btnGreen" style="float: right;" id="btnSubmit"><?= vt('volgende'); ?></button>
      </div>

    </form>
  </div>
  <script>
    $(document).ready(function() {

      $("#grootboek").click(function ()
      {
        $("#soortVERMGB").prop('checked', true);
      });
      $("#omschrijving").focus(function ()
      {
        $("#soortVERMOMS").prop('checked', true);
      });
    });

    $("#btnSubmit").click(function(e){
      e.preventDefault();
      if ($("#bestand").val() == "")
      {
        $("#msg").html("selecteer het te importeren bestand");
        $("#msg").show(300);
      }
      else if ($("#omschrijving").val() == "" && $("#soortVERMOMS").is(':checked'))
      {
        $("#msg").html("geef een aangepaste omschrijving");
        $("#msg").show(300);
      }
      else
      {
        $("#editForm").submit();
      }
      setTimeout(function(){ $("#msg").hide(300); }, 3000);


    });
  </script>
<?
echo template($__appvar["templateRefreshFooter"], $content);

?>