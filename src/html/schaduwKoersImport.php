<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/05/02 12:05:28 $
    File Versie         : $Revision: 1.2 $

    $Log: schaduwKoersImport.php,v $
    Revision 1.2  2018/05/02 12:05:28  cvs
    call 6749

    Revision 1.1  2018/04/04 14:44:13  cvs
    call 6749


*/

include_once("wwwvars.php");

$_SESSION["NAV"]='';
$tmpl = new AE_template();
$foutlog = array();
$tmpl->appendSubdirToTemplatePath("crmImport");
$succes = 0;
$fail   = 0;
$msg = "";
$dataArray = array();

echo template($__appvar["templateContentHeader"],$content);
?>
<link rel="stylesheet" href="widget/css/font-awesome.min.css">
<link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">


<style>
  <?=$tmpl->parseBlockFromFile("crmImport.css");?>
  ol{
    -webkit-margin-before: .3em;
    -webkit-margin-after: .1em;
  }
  legend{
    padding: 10px;
    background: rgba(20,60,90,1);
    color: white;
  }
  button {
    padding:10px 15px 10px 15px ;
    background: rgba(20,60,90,1);
    color: white;
    border: 0px;
  }


</style>
<?


if ($_POST["action"] == "import")
{
  $db = new DB();
  if ($_FILES["bestand"]["error"] != 0)
  {
    $foutlog[] = "FOUT: kan bestand niet openen";
  }
  else
  {
    $row = 0;
    $handle = fopen($_FILES["bestand"]["tmp_name"], "r");
    while ($data = fgetcsv($handle, 4096, ";"))
    {
      $row++;
      if ($row == 1)
      {
        if ($data[1] != "Datum" OR $data[0] != "Fonds")
        {
          $foutlog[] = "FOUT: geen schaduwimport bestand";
        }
        if (count($foutlog) != 0)
        {
          break;
        }
        else
        {
          continue;
        }
      }
      $data[99] = $row;
      addRecord($data);
    }
  }
?>
  <fieldset>
    <legend> Verwerking </legend>
    <li><?=(int)$succes?> regels toegevoegd</li>
    <li><?=(int)$fail?> regels overgeslagen</li>
  </fieldset>
<?


  if (count($foutlog) > 0)
  {
?>
    <h2>Foutlog/overgeslagen regels</h2>
    <fieldset>
    <legend> Verslag </legend>
<?
    foreach ($foutlog as $item)
    {
      echo "<li>$item</li>";
    }
?>
    </fieldset>
<?
  }
  else
    {
?>
  <h2>Het bestand is ingelezen</h2>
<?
    }
?>
  <br/>
  <br/>
  <button><a href="schaduwkoersenList.php" style="color: white;">Terug naar de schaduwlijst</a></button>
<?
  exit;
}

?>


<div class="container">

  <div>
    <h1 style="float:left">Schaduwkoers import, stap 1 </h1>
  </div>
  <div style="clear: both"></div>
  <div id="msg"></div>
  <form enctype="multipart/form-data" method="POST"  name="editForm" id="editForm">
    <input type="hidden" name="action" value="import" />

    <fieldset>
      <legend>Data bestand</legend>
      <div class="formblock">
        <div class="formlinks"><label for="bestand" title="bestand">Importbestand</label></div>
        <div class="formrechts">
          <input type="file" name="bestand" id="bestand" value="" />

        </div>
      </div>
    </fieldset>

    <br/>
    <br/>
    <div >

      <button style="float: right;" id="btnSubmit">volgende</button>
    </div>

  </form>

</div>
<script>
  $(document).ready(function(){


    $("#btnSubmit").click(function(e){
      e.preventDefault();
      if ($("#bestand").val() == "")
      {
        $("#msg").html("selecteer het te importeren bestand");
        $("#msg").show(300);
      }
      else
      {
        $("#editForm").submit();
      }
      setTimeout(function(){ $("#msg").hide(300); }, 3000);

    });
  });
</script>

<?

echo template($__appvar["templateRefreshFooter"],$content);


function addRecord($data)
{
  global $succes, $fail, $db, $foutlog, $USR;
  $dataSplit = explode("-",$data[1]);
  $query = "SELECT id FROM Schaduwkoersen WHERE Fonds = '{$data[0]}' AND `Datum` = '".$dataSplit[2]."-".$dataSplit[1]."-".$dataSplit[0]."' ";

  if ($rec = $db->lookupRecordByQuery($query))
  {
    $fail++;
    $foutlog[] = $data[99].": Fonds/datum bestaat al <b>{$data[0]} / {$data[1]}</b>";
  }
  else
  {
    $query = "SELECT id FROM Fondsen WHERE Fonds = '{$data[0]}' ";
    if ($rec = $db->lookupRecordByQuery($query))
    {
      $succes++;
      $query = " INSERT INTO Schaduwkoersen SET
         `Datum`         = '".$dataSplit[2]."-".$dataSplit[1]."-".$dataSplit[0]."',
         `Fonds`         = '{$data[0]}',
         `Koers`         = '".str_replace(",",".",$data[2])."',
         `add_user`      = NOW(),
         `add_date`      = '$USR',
         `change_date`   = NOW() ,
         `change_user`   = '$USR'
      ";
  debug($query);
      $db->executeQuery($query);
    }
    else
    {
      $fail++;
      $foutlog[] = $data[99].": Fonds onbekend {$data[0]}";
    }
  }






}