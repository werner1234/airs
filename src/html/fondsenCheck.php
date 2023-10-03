<?php
/*
    AE-ICT sourcemodule created 26 apr. 2021
    Author              : Chris van Santen
    Filename            : fondsenCheck.php


*/

include_once("wwwvars.php");

$fmt = new AE_cls_formatter();


$data = array_merge($_GET,$_POST);

if ($data["action"] == "csv")
{

  header("Content-type: text/csv");
  header("Content-Disposition: attachment; filename=checkFonds_".date("Ymdhi").".csv");
  header("Pragma: no-cache");
  header("Expires: 0");
  foreach ($_SESSION["checkFonds"] as $row)
  {
    echo '"'.implode('";"', $row).'"'."\r\n";
  }
  exit;


}

$error = "";
$done = "";
$db = new DB();
$mainHeader   = "";
$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>".$mainHeader."</b>".$subHeader."</div>";
echo template($__appvar["templateContentHeader"],$content);


?>
  <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen"><link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">


  <style>

    fieldset{
      margin: 10px 20px;
    }

    fieldset div{
      line-height: 3;
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
      height: 18px;
      background: rgba(20,60,90,1);;
      color: white;
      font-size: 1rem;
      padding: 4px;
    }
    .pageContainer{
      width: 1050px;

    }
    #msgDialog{
      width: 600px;
      padding: 15px;
      background: maroon;
      color: white;
      font-size: 1.2em;
    }
    #msgDone{
      width: 600px;
      padding: 15px;
      background: whitesmoke;
      color: #333;
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
      margin: 5px 50px;
      padding: 10px 20px;
      background: rgba(20,60,90,1);;
      color: white;
      cursor: pointer;
      border:0;
      outline: none;
    }
    .colFile{
      background: cornsilk;
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
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <br/>
  <br/>

  <div class="pageContainer">
  <h2>Check Fondsen</h2>
<?
if ($data["action"] == "fase2" )
{
  $msg    = array();
  $import = true;
  if ($import)
  {
    if ($_FILES['uploadFile']["error"] == 0)
    {
      if (  $_FILES['uploadFile']["type"] != "text/comma-separated-values" &&
        $_FILES['uploadFile']["type"] != "text/x-csv" &&
        $_FILES['uploadFile']["type"] != "text/csv" &&
        $_FILES['uploadFile']["type"] != "application/octet-stream" &&
        $_FILES['uploadFile']["type"] != "application/vnd.ms-excel" &&
        $_FILES['uploadFile']["type"] != "text/plain")
      {
        $msg[] = "FOUT: verkeerd bestandstype(".$_FILES['uploadFile']["type"]."), alleen tekst bestanden zijn toegestaan.";
      }
      else
      {

      }

    }
    $importfile = $__appvar["basedir"]."/html/importdata/clientImport_{$USR}_".date("Ymdhis").".csv";
    move_uploaded_file($_FILES['uploadFile']["tmp_name"],$importfile);

    if (!$handle = @fopen($importfile, "r"))
    {
      echo "FOUT bestand $importfile is niet leesbaar";
      exit;
    }


      ?>
      <table>
      <thead>
      <tr>
        <td >#</td>
        <td >f.isin</td>
        <td >f.valuta</td>
        <td >f.fondsnaam</td>
        <td>a.id</td>
        <td>a.Combi</td>
        <td>a.Fonds</td>
        <td>a.Valuta</td>
        <td>a.FondsImportCode</td>
        <td>a.ISINCode</td>
      </tr>
      </thead>
      <?
      $row      = 0;
      $output = array();
      $output[] = array(
        "f.isin",
        "f.valuta",
        "f.fondsnaam",
        "a.id",
        "a.Combi",
        "a.Fonds",
        "a.Valuta",
        "a.FondsImportCode",
        "a.ISINCode"
      );

      while ($data = fgetcsv($handle, 4096, ";"))
      {
        $rec        = array();

        $isin       = trim($data[0]);
        $valuta     = trim($data[1]);
        $fondsnaam  = trim($data[2]);
        $row++;
        if ($row == 1 )
        {
          //continue; //skip header
        }
        ?>
        <tr>
          <td class="colFile"><?=$row?></td>
          <td class="colFile"><?=$isin?></td>
          <td class="colFile"><?=$valuta?></td>
          <td class="colFile"><?=$fondsnaam?></td>
          <?

          if ($isin != "" AND $valuta != "")
          {
            $query = "
          SELECT
            Fondsen.id,
            Concat(Left(Fondsen.ISINCode,12),Fondsen.Valuta) as 'Combi',
            Fondsen.Fonds,
            Fondsen.Valuta,
            Fondsen.FondsImportCode,
            Fondsen.ISINCode
          FROM
            Fondsen
          WHERE
            ISINcode = '{$isin}' AND Valuta = '{$valuta}'";
            $rec = $db->lookupRecordByQuery($query);
            $output[] = array(
              $isin,
              $valuta,
              $fondsnaam,
              $rec["id"],
              $rec["Combi"],
              $rec["Fonds"],
              $rec["Valuta"],
              $rec["FondsImportCode"],
              $rec["ISINCode"]

            );
          }
          ?>
          <td><?=$rec["id"]?></td>
          <td><?=$rec["Combi"]?></td>
          <td><?=$rec["Fonds"]?></td>
          <td><?=$rec["Valuta"]?></td>
          <td><?=$rec["FondsImportCode"]?></td>
          <td><?=$rec["ISINCode"]?></td>
        </tr>
        <?


      }


      ?>
      </table>
    <br/>
    <br/>
    <form >
      <input type="hidden" name="action" value="csv">
      <input type="submit" value="Export naar .csv" />
    </form>

    <?
    $_SESSION["checkFonds"] = $output;



  }
}
  else
  {

?>

  <br/>
  <br/>
  <?
  if (count($msg) > 0)
  {
  ?>
    <div id="msgDone">
      <h4>verwerking:</h4>
      <ul>
      <li><?=implode("<li>", $msg)?>
      </ul>
    </div>
    <?
  }
    ?>




    <?
    if ($error != "")
    {
      ?>
      <div id="msgDialog">
        <?=$error?>
      </div>
      <?
    }
    if ($done != "")
    {
      ?>
      <div id="msgDone">
        <?=$done?>
      </div>
      <?
    }
    ?>
    <h3>Controle</h3>
    <form enctype="multipart/form-data" method="post" id="theForm">
    <input type="hidden" name="action" value="fase2" />

      <fieldset>
        <legend>Bestand inlezen</legend>
        <br/>

        <div id="loadProducts">
          <div class="lDiv">bestand:</div>
          <input type="file" name="uploadFile">
        </div>
        <br/>
        <br/>
        <input type="submit" value="verwerk">
      </fieldset>

<?
  }

