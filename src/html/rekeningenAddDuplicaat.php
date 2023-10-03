<?php
/*
    AE-ICT sourcemodule created 01 mrt. 2021
    Author              : Chris van Santen
    Filename            : rekeningenAddDuplicaat.php


*/


include("wwwvars.php");


$cfg=new AE_config();



echo template($__appvar["templateContentHeader"],$content);
?>
<link href="style/aeStyle.css" rel="stylesheet" type="text/css" media="screen">
<link rel="stylesheet" href="style/fontAwesome/font-awesome.min.css">
  <style>

    fieldset{
      margin: 10px 20px;
    }

    fieldset div{

      line-height: 2;
    }
    table{
      width: 90%;
      margin:0 auto;
    }
    td{
      border-bottom: 1px #999 solid;
    }

    thead td{
      background: rgba(20,60,90,1);;
      color: white;
      border:none;
      padding:5px 10px;
      vertical-align: sub;
    }
    tbody tr:hover{
      background: beige;
    }
    .vink,
    .vinkP{
      cursor: pointer;
    }


    legend{
      width: 200px;

      background: rgba(20,60,90,1);;
      color: white;
      font-size: 1rem;
      padding: 4px;
    }
    .pageContainer{
      width: 1050px;

    }
    #dlg{
      display: none;
      width: 600px;
      padding: 15px;
      background: maroon;
      color: white;
      font-size: 1.2em;
    }
    #msgDone{
      width: 600px;
      padding: 15px;
      background: darkgreen;
      color: white;
      font-size: 1.2em;
    }
    #messageBar{
      position: fixed;
      background: darkgreen;
      text-align: center;
      font-weight: bold;
      font-size:1.5em;
      color: white;
      top:0;
      height: 30px;
      left:0;
      right:0;
      padding-top:0.75em;

    }
    #btnSubmit{

      padding: 10px 20px;
      background: rgba(20,60,90,1);;
      color: white;
      cursor: pointer;
      border:0;
      outline: none;
    }

    .vDiv{
      margin-top:10px;
    }
    .lDiv{
      display: inline-block;
      width: 200px;
    }
    #itemSelected{
      font-width: bold;
      font-size: larger;
    }
    #itemCountRow{
      padding: 5px;
      text-align: center;
      font-size: 1em;
    }
    #fileSelect{
      display: none;
    }
  </style>

<?php
if ($_REQUEST["action"] == "fase2")
{

  ob_start();
  ob_implicit_flush();
  $db = new DB();
  $dbN = new DB();
  $copyArray = array();
  $error = array();
  msg("start duplicaat rekeningen maken..");
  msg("-------------------------------------------------------------------");
  $pOrg = $_REQUEST["orgPortefeuille"];
  msg("Orginele portefeuille: {$pOrg}");
  $pDup = $_REQUEST["dupPortefeuille"];
  msg("Duplicaat portefeuille: {$pDup}");
  $query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '{$pDup}' ";
  $pDupRec = $db->lookupRecordByQuery($query);
  $dupVB = $pDupRec["Vermogensbeheerder"];
  msg("Vermogenbeheerder voor duplicaat: {$dupVB}");
  $query = "SELECT * FROM Rekeningen WHERE Portefeuille = '{$pOrg}' ";
  $db->executeQuery($query);
  while ($rek = $db->nextRecord())
  {
    unset($rek["id"]);
    $orgRek = $rek["Rekening"];
    $dupRek = $dupVB.$orgRek;
    $query = "SELECT Portefeuille FROM Rekeningen WHERE Rekening = '{$dupRek}' ";
    if ($duplo = $dbN->lookupRecordByQuery($query))
    {
      msg("Kan rekening $dupRek niet toevoegen, bestaat al voor portefeuille {$duplo["Portefeuille"]}");
      continue;
    }
    $rek["add_date"]            = date("Y-m-d H:i:s");
    $rek["add_user"]            = $USR;
    $rek["change_date"]         = date("Y-m-d H:i:s");
    $rek["change_user"]         = $USR;
    $rek["Beleggingscategorie"] = "";
    $rek["Tenaamstelling"]      = "";
    $rek["AttributieCategorie"] = "";
    $rek["Portefeuille"]        = $pDup;
    $rek["Rekening"]            = $dupRek;
    $query = "INSERT INTO Rekeningen SET ";
    $queryValues = "";
    foreach ($rek as $k=>$v)
    {
      $queryValues .= ", `$k` = '$v'\n";
    }
    $q = $query.substr($queryValues,1);
    $copyArray[$orgRek] = $dupRek;
    if ($dbN->executeQuery($q))
    {
      $lastid = $dbN->last_id();
      addTrackAndTrace("Rekeningen", $lastid, "Rekening", "", $dupRek, $USR);
      msg("Rekening {$dupRek} toegevoegd voor portefeuile {$pDup}");
    }

  }
  foreach ($copyArray as $org=>$dup)
  {
    $query = "
      INSERT INTO 
        RekeningenDuplicaat 
      SET
        add_user = '{$USR}'
        , add_date = NOW()
        , change_user = '{$USR}'
        , change_date = NOW()
        , actief = 1
        , Rekening = '$org'
        , RekeningDuplicaat = '$dup'
    ";

    if ($dbN->executeQuery($query))
    {
      $lastid = $dbN->last_id();
      addTrackAndTrace("RekeningenDuplicaat", $lastid, "RekeningDuplicaat", "", $dup, $USR);
      msg("RekeningenDuplicaat {$dup} toegevoegd");
    }

  }

  foreach ($copyArray as $org=>$dup)
  {
    msg("Start kopieren Rekeningafschriften voor {$dup}");
    $query = "SELECT * FROM Rekeningafschriften WHERE Rekening = '{$org}'";
    $db->executeQuery($query);
    while ($rekAf = $db->nextRecord())
    {

      unset($rekAf["id"]);
      $rekAf["add_date"]            = date("Y-m-d H:i:s");
      $rekAf["add_user"]            = $USR;
      $rekAf["change_date"]         = date("Y-m-d H:i:s");
      $rekAf["change_user"]         = $USR;
      $rekAf["Rekening"]            = $dup;
      $query = "INSERT INTO Rekeningafschriften SET ";
      $queryValues = "";
      foreach ($rekAf as $k=>$v)
      {
        $queryValues .= ", `$k` = '$v'\n";
      }
      $q = $query.substr($queryValues,1);
      $dbN->executeQuery($q);

    }
    msg("Klaar met kopieren Rekeningafschriften voor {$dup}");
    msg("Start kopieren Rekeningmutaties voor {$dup}");
    $query = "SELECT * FROM Rekeningmutaties WHERE Rekening = '{$org}'";
    $db->executeQuery($query);
    $queryValues = "";
    while ($rekMut = $db->nextRecord())
    {
      unset($rekMut["id"]);
      $rekMut["add_date"]            = date("Y-m-d H:i:s");
      $rekMut["add_user"]            = $USR;
      $rekMut["change_date"]         = date("Y-m-d H:i:s");
      $rekMut["change_user"]         = $USR;
      $rekMut["Rekening"]            = $dup;
      $query = "INSERT INTO Rekeningmutaties SET ";
      $queryValues = "";
      foreach ($rekMut as $k=>$v)
      {
        $queryValues .= ", `$k` = '$v'\n";
      }
      $q = $query.substr($queryValues,1);
//      debug($q);
      $dbN->executeQuery($q);
    }
    msg("Klaar met kopieren Rekeningmutaties voor {$dup}");

  }

  msg("-------------------------------------------------------------------");
  msg("Klaar met inlezen..");


}
else
{
?>



<h1>Duplicaat rekening aanmaken</h1>

<form method="post" id="dupForm">
  <input type="hidden" name="action" value="fase2">
  <div id="dlg"></div>
<fieldset>
  <legend>Selecteer portefeuiles</legend>
  <div>
    <p>
    <div class="lDiv">Geef orginele portefeuille op:</div>
    <input name="orgPortefeuille" id="orgPortefeuille" size="60">
    </p>
    <p>
    <div class="lDiv">Geef duplicaat portefeuille op:</div>
    <input name="dupPortefeuille" id="dupPortefeuille" size="60">
    </p>
    <p><button id="btnSubmit">maak duplicaat aan</button>
  </div>
</fieldset>
</form>

<script>
  $(document).ready(function(){
    $("#btnSubmit").click(function(e)
    {
      $("#dlg").hide(100);
      e.preventDefault();
      const op = $("#orgPortefeuille").val();
      const dp = $("#dupPortefeuille").val();
      const same = (op == dp);
      const dupOk = dp.indexOf(op);
      console.log(dupOk);
      let error = "";
      if (op.trim() == "")                  {    error += "<li> orginele portefeuille is verplicht";     }
      if (dp.trim() == "")                  {    error += "<li> duplicaat portefeuille is verplicht";    }
      if (same)                             {    error += "<li> orgineel = dupiclaat "; }
      if (error == "" && (dupOk == -1))     {    error += "<li> duplicaat is ongeldig (geen orgineel prtnr)"; }

      if (error != "")
      {
        $("#dlg").html(error);
        $("#dlg").show(300);
      }
      else
      {
        $("#dupForm").submit();
      }
    });
    $("#orgPortefeuille").select();


    $("#orgPortefeuille").autocomplete(
      {

        source: "lookups/getPortefeuille.php",                // link naar lookup script
        create: function(event, ui)                           // onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
        {

        },
        close: function(event, ui)                            // controle of ID gevuld is anders reset naar onCreate waarden
        {

        },
        search: function(event, ui)                           // als zoeken gestart het ID veld leegmaken
        {
                                       // reset koppel pointer
        },
        select: function(event, ui)                           // bij selectie clientside vars updaten
        {
          $("#orgPortefeuille").val(ui.item.portefeuille);
          // $("#depot").val(ui.item.depot);
          // $("#portefeuilleInfo").html(ui.item.info + "(" + ui.item.depot + ")");

        },
        minLength: 2,                                         // pas na de tweede letter starten met zoeken
        delay: 0,
        autoFocus: true

      });


  $("#dupPortefeuille").autocomplete(
    {

      source: "lookups/getPortefeuille.php",                // link naar lookup script
      create: function(event, ui)                           // onCreate sla oude waardes op om te kunnen resetten in onClose bij geen selectie
      {

      },
      close: function(event, ui)                            // controle of ID gevuld is anders reset naar onCreate waarden
      {

      },
      search: function(event, ui)                           // als zoeken gestart het ID veld leegmaken
      {

      },
      select: function(event, ui)                           // bij selectie clientside vars updaten
      {
        $("#dupPortefeuille").val(ui.item.portefeuille);
        // $("#depot").val(ui.item.depot);
        // $("#portefeuilleInfo").html(ui.item.info + "(" + ui.item.depot + ")");

      },
      minLength: 2,                                         // pas na de tweede letter starten met zoeken
      delay: 0,
      autoFocus: true

    });


  });
</script>
<?php
}

echo template($__appvar["templateRefreshFooter"],$content);
exit;


function msg($txt)
{
  echo date("H:i:s")."::".$txt."<br/>";
  ob_flush();
}