<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 3 augustus 2014
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/01 12:57:33 $
    File Versie         : $Revision: 1.4 $
 		
    $Log: vragenlijstenperrelatie_vragenblok.php,v $
    Revision 1.4  2018/02/01 12:57:33  cvs
    update naar airsV2

    Revision 1.3  2018/01/24 15:01:58  cvs
    call 6527

    Revision 1.2  2018/01/19 15:44:04  cvs
    x

    Revision 1.1  2017/11/22 16:20:30  cvs
    call 6257

*/
include_once("wwwvars.php");
include_once("../classes/editObject.php");

include_once ("../classes/AIRS_vragen_helper.php");



//debug($_GET);
function makeInput($arg)
{
//  debug($arg);
  $db = new DB();
  $formTemplate.='<div class="formblock">
<div style="width:800px;">{description} </div>
<div>{out} </div>
<br>
</div>';

  $db->executeQuery($arg["query"]);
  while ($rec = $db->nextRecord())
  {
    $optArray[] = $rec;
  }
  $db->executeQuery($arg["query"]);
  $value = $db->lookupRecordByQuery($arg["value"]);
  $out = $arg["description"].": ";
  $out .= "<SELECT name='".$arg["name"]."' >";
  $out .= "<option value='' > --- </option>";
  foreach ($optArray as $item)
  {
    $selected = ($item["id"] == $value["antwoordId"])? "SELECTED":"";
    $out .= "<option value='".$item["id"]."' $selected>".$item["omschrijving"]."</option>";
  }
  $out .= "</SELECT><hr/>";

  //return $out;

//debug($data["vragen"]);

  $vraagIdArray = array();

  foreach ($data["vragen"] as $vraag)
  {
//  debug($vraag);
    $vraagId = "vraag_".$vraag["vraagId"];
    $vraagIdArray[] = $vraagId;
    ?>
    <fieldset class="vraagContainer">
      <legend ><?=$vraag["omschrijving"]?></legend>
      <p class="vraagTextContainer" ><?=$vraag["vraag"]?></p>
      <p class="antwoordContainer">
        <?

        foreach ($vraag["antwoorden"] as $val)
        {
          $veldId = $vraagId."_".$val["id"];
          ?>
          <input data-punten="<?=$val["punten"]?>"
                 name="<?=$vraagId?>"
                 id="<?=$veldId?>"
                 type="radio"
                 value="<?=$val["id"]?>" />
          <label for="<?=$veldId?>">&nbsp;&nbsp;<?=$val["antwoord"]?></label><br/>
          <?
        }
        ?>

      </p>
    </fieldset>
    <?
  }



}

if ((int)$_GET["vId"] + (int)$_GET["id"] < 1)
{
  echo vt("verkeerde aanroep van deze module");
  exit;
}

if ($_GET["vId"] > 0)
{
  $data = array(
    "vragenlijstId" => $_GET["vId"],
    "noSave"        => true,

  );
}

if ($_GET["id"] > 0)
{
  $vrg = new AIRS_vragen_helper($_GET["id"]);
  $crmRefRec  = $vrg->crmRefRec;
  if (count($crmRefRec) == 0)
  {
    echo "" . vt('FOUT: koppelrecord niet gevonden') . " (".$_GET["id"].")";
    exit;
  }
  $data = array(
    "vragenlijstId" => $crmRefRec["vragenLijstId"],
    "relatieId"     => $crmRefRec["nawId"],
    "datum"         => $crmRefRec["datum"],
    "crmRef_id"     => $crmRefRec["id"],
  );

}




if ($_POST["action"] == "update")
{

  $dba = new DB();
  foreach ($_POST as $k=>$v)
  {
    $split = explode("_",$k);
    if ($split[0] == "vraag")
    {
      $id = $split[1];
      $vrg->updateIngevuld($id,$v);
    }
  }
//  $vrg->showIngevuld(__LINE__);
//  $vrg->showIngevuld();
  $vrg->saveIngevuld();
  echo "<h2>" . vt('antwoorden opgeslagen') . "</h2>";

}

$db = new DB();

if($data['vragenlijstId'] > 0)
{

  ?>
  <html>
  <head>
    <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">
    <link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">
    <link rel="stylesheet" href="style/AIRS_default.css">
    <script type="text/javascript" src="javascript/jquery-min.js"></script>
    <script type="text/javascript" src="javascript/jquery-ui-min.js"></script>
    <script language=JavaScript src="javascript/algemeen.js" type=text/javascript></script>
<style>
  .vraagContainer{
    width: 97%!important;
  }
  #btnSubmit{
    background: darkgreen!important;
  }
</style>
  </head>
  <body>
  <?
  if ((int) $data['relatieId'] < 1)
  {
    echo "<h2 style='color:red'>" . vt('U kunt de antwoorden nog niet opslaan, sla eerst het hoofdrecord op') . " </h2>";
  }

  ?>
  <form name="vragenForm" id="vragenForm"  method="post">
  <input type="hidden" name="action" value="update">
  <input type="hidden" name="relatieId" value="<?=$data['relatieId']?>">
  <input type="hidden" name="vragenlijstId" value="<?=$data['vragenlijstId']?>">
  <input type="hidden" name="crmRef_id" value="<?=$data['crmRef_id']?>">
  <input type="hidden" name="datum" value="<?=$data['datum']?>">

  <?
  $vrgHelper = new AIRS_vragen_helper($data['crmRef_id']);
//  $vrgHelper->showIngevuld();
  $vragen = init($data['vragenlijstId']);
//  debug($vragen);
  $vragenlijst = $vragen["vragen"];

  foreach ($vragenlijst as $vraag)
  {

    $openVraag = (count($vraag["antwoorden"]) == 0)?"O":"C";
    $vraagId = "vraag_".$vraag["vraagId"].$openVraag;
    $vraagIdArray[] = $vraagId;
    ?>
    <br/>
    <fieldset class="vraagContainer">
      <legend ><?=$vraag["omschrijving"]?></legend>
      <p class="vraagTextContainer" ><?=$vraag["vraag"]?></p>
      <p class="antwoordContainer">
        <input type="hidden" name="<?=$vraagId?>" value="0"/>

        <?
        if (count($vraag["antwoorden"]) == 0)
        {
?>
          <textarea data-punten="<?=$val["punten"]?>"
                    style="width: 80%; height: 100px;"
                   name="<?=$vraagId?>"
                   id="<?=$veldId?>"
          ><?=$vraag["keuze"]?></textarea>
<?
        }
        else
        {
?>
          <input data-punten=""
                 name="<?=$vraagId?>"
                 type="radio"
                 value="0"/>
          <label>-- reset antwoord --</label><br/>
<?
          foreach ($vraag["antwoorden"] as $val)
          {
            $checked = ($vraag["keuze"] == $val["id"])?"CHECKED":"";
            $veldId = $vraagId."_".$val["id"];
            ?>
            <input data-punten="<?=$val["punten"]?>"
                   name="<?=$vraagId?>"
                   id="<?=$veldId?>"
                   type="radio"
                   value="<?=$val["id"]?>"
              <?=$checked?> />
            <label for="<?=$veldId?>">&nbsp;&nbsp;<?=$val["antwoord"]?></label><br/>
            <?
          }
        }

        ?>

      </p>
    </fieldset>
    <?
  }

}
?>
<br/>
<br/>
<?
  if ((int) $data['relatieId'] > 0)
  {
    echo '<button id="btnSubmit" >' . vt('wijzigingen opslaan') . '</button>';
  }
?>
  &nbsp;&nbsp;&nbsp;
</form>

<script>
  $(document).ready(function () {
    $("#btnSubmit").click(function (e) {
      e.preventDefault();
      $("#vragenForm").submit();
    });
  });
</script>
</body>
</html>

<?
function init()
{
  global $data;

  $id = $data["vragenlijstId"];
  $vrgHelper = new AIRS_vragen_helper($data["crmRef_id"]);
  $output = array();
  $db     = new DB();
  $db2    = new DB();
  $query = "SELECT * FROM `VragenVragenlijsten` WHERE id = $id";
  $vragenlijst = $db->lookupRecordByQuery($query);
  $query  = "SELECT * FROM `VragenVragen` WHERE vragenlijstId = $id AND offline = 0 ORDER BY volgorde";

  $db->executeQuery($query);
  $output["vragenLijstId"] = $id;
  $output["vragenLijst"]   = $vragenlijst["omschrijving"];
  while ($rec = $db->nextRecord())
  {
    $antwoordArray = array();
    $query = "SELECT * FROM `VragenAntwoorden` WHERE vraagId = ".$rec["id"];
    $db2->executeQuery($query);
    while ($aRec = $db2->nextRecord())
    {
      $antwoordArray[] = array(

        "id"       => $aRec["id"],
        "antwoord" => $aRec["omschrijving"],
        "punten"   => $aRec["punten"]
      );
    }

    $output["vragen"][$rec["vraagNummer"]] = array(
      "vraagId"      => $rec["id"],
      "omschrijving" => $rec["omschrijving"],
      "vraag"        => $rec["vraag"],
      "factor"       => $rec["factor"],
      "antwoorden"   => $antwoordArray,
      "keuze"        => $vrgHelper->getIngevuld($rec["id"]) //////aetodo
    );
  }
  return $output;
}