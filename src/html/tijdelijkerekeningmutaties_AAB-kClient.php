<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2017/09/27 11:43:35 $
  File Versie					: $Revision: 1.2 $

  $log: tijdelijkerekeningmutatiesVerwerk.php,v $




 */
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
// SET in wwwvars ie:  $__appvar["userLevel"] = _READ;
// include wwwvars
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

$ajx = new AE_cls_ajaxLookup(array("portefeuille"));
$ajx->changeModuleTriggerID("portefeuille", "rk");

$DB = new DB();
$meldArray = array();
session_start();
$_SESSION["submenu"] = "";
//clear navigatie
$_SESSION["NAV"] = "";
session_write_close();
$cfg = new AE_config();
$content = array();
echo template($__appvar["templateContentHeader"], $editcontent);
?>


<style>
  #melding{
    color: maroon;
    font-weight: normal;
    border: 1px solid red;
    width: 50%;
    padding: 10px;
  }

</style>

<?

if ($_POST["doIt"] == 1)
{

  $d = explode("-", $_POST["datum"]);
  $rekeningen = array();
  $cfg->addItem("AAB-K-clienten", $_POST["portefeuilles"]);
  $db = new DB();
  $query = "SELECT Rekening FROM Rekeningen WHERE Portefeuille IN ('".str_replace(",", "','",$_POST["portefeuilles"])."')";
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $rekArray[] = $rec["Rekening"];
  }
  if (count($rekArray) > 0)
  {
    $query = "SELECT count(id) as aantal FROM TijdelijkeRekeningmutaties WHERE TijdelijkeRekeningmutaties.change_user = '$USR' AND Rekening IN ('".implode("','", $rekArray)."')";
    $tot = $db->lookupRecordByQuery($query);
    $query = "UPDATE TijdelijkeRekeningmutaties SET Boekdatum = '".$d[2]."-".$d[1]."-".$d[0]."' WHERE TijdelijkeRekeningmutaties.change_user = '$USR' AND Rekening IN ('".implode("','", $rekArray)."')";

    $db->executeQuery($query);
  }

?>
  <br/>
  <h3>Boekdatums bijgewerkt:</h3>
  <?=$tot["aantal"]?> records aangepast<br/><br/>
  <button><a href="tijdelijkerekeningmutatiesList.php">Ga naar tijdelijke rekeningmutatie overzicht</a></button>
  <br/><br/><br/><br/><br/>

<?

}
else
{


  $portefeuilles = explode(",",$cfg->getData("AAB-K-clienten"));


//debug($tmpRec);
  ?>
  <style>
    .formBlock {
      display: inline-block;
      width: 1200px;
    }

    .leftKol {
      width: 160px;
      float: left;

    }

    .rightKol {
      widows: 1000px;
      float: left;

    }

    .clear {
      clear: both;
    }
  </style>
  <br/><br/>
  <h3>ABN AMRO datum aanpassen K-clienten</h3>
  <form method="post" id="editForm">
    <input type="hidden" name="doIt" value="1"/>
    <div class="formBlock">
      <div class="leftKol">Geef de datum:</div>
      <div class="rightKol"><input name="datum" class="AIRSdatepicker" style="width: 100px" value="<?=date("d-m-Y")?>"/>
        (dd-mm-jjjj)
      </div>
    </div>
    <div class="clear"></div>
    <div class="formBlock">
      <div class="leftKol">Portefeuilles:</div>
      <div class="rightKol"><textarea name="portefeuilles" id="portefeuilles" rows="5" cols="80" readonly></textarea><br/><br/>
      portefeuille: <input id="rk" style="width: 150px"/> <button id="btnAdd">toevoegen</button> <button id="btnDel">verwijder</button><br/>
      </div>
    </div>
    <div class="clear"></div>

    <div class="formBlock">
      <div class="leftKol">&nbsp;</div>
      <div class="rightKol"><input type="submit" value="pas boekdatums aan"/></div>
    </div>
    <div class="clear"></div>
  </form>

  <script>
    <?
    if (count($portefeuilles) > 0)
    {
?>
    var portArray = ['<?=implode("','", $portefeuilles)?>'];
<?
    }
    else
    {
?>
    var portArray = [];
<?
    }

 echo $ajx->getJS();
?>

    function populateRekeningen()
    {
      $("#portefeuilles").html(portArray.join());
    }

    $(document).ready(function () {

      function saveRekeningen()
      {
        var field = "AAB-K-clienten";
        var value = portArray.join();
        console.log("in updateCFG met " + field + " v= " + value);
        $.ajax(
        {
          url:'ajax/AEconfig_updateField.php',
            data:{
              field: field,
              value: value
            },
            dataType:'json',
            success:function(data)
            {
              console.log("CFG: " + field + " bijgewerkt naar " + value);
            }
         });
      }

      populateRekeningen();
      $("#btnAdd").click(function (e)
      {
        e.preventDefault();
        var addRek = $("#rk").val();
        if (portArray.indexOf(addRek) != -1)
        {
          AEMessage("portefeuille is al aanwezig", "melding");
        }
        else
        {
          portArray.push(addRek);
          $("#rk").val("");
          populateRekeningen();
          saveRekeningen();
//          console.log(rekeningenArray);
        }

      });

      $("#btnDel").click(function (e)
      {
        e.preventDefault();
        var addRek = $("#rk").val();
        console.log(portArray.indexOf(addRek) + ' :' + addRek);
        if (portArray.indexOf(addRek) == -1)
        {
          AEMessage("portefeuille niet in lijst", "melding");
        }
        var tmpArray = [];
        var items = portArray.length;
        for (var i = 0; i < items; i++)
        {
          if (addRek != portArray[i] && portArray[i] != "")
          {
            tmpArray.push(portArray[i]);
          }
        }
        portArray  = tmpArray;
        populateRekeningen();
        saveRekeningen();
        delete  tmpArray;

      });
    });
  </script>

  <?
}
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"], $content);
