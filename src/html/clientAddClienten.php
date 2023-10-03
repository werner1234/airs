<?php
/*
    AE-ICT sourcemodule created 14 apr. 2021
    Author              : Chris van Santen
    Filename            : rekeningAddRekeningen.php


*/

include_once("wwwvars.php");

$fmt = new AE_cls_formatter();

$portDigits = array("0","1","2","3","4","5","6","7","8","9","-");
$data = array_merge($_GET,$_POST);
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
  <h2>Clienten inlezen</h2>
<?
if ($data["action"] == "fase2" OR $data["action"] == "fase3")
{
  $msg    = array();
  $import = true;
  if ($import)
  {


  if ($data["action"] == "fase2")
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
    <form enctype="multipart/form-data" method="post" id="theForm">
      <input type="hidden" name="action" value="fase3" />
      <input type="hidden" name="file" value="<?=$importfile?>"/>
    <table>
    <thead>
    <tr>
      <td>Regel</td>
      <td>portefeuille</td>
      <td>client</td>
      <td>VB</td>
    </tr>
    </thead>
<?
    $row      = 0;
    while ($data = fgetcsv($handle, 4096, ";"))
    {
      $portOk   = "";
      $clientOk = "";
      $portefeuille       = trim($data[0]);
      $client             = trim($data[1]);
      $vermogensbeheerder = trim($data[2]);
      if ($portefeuille != "" AND $vermogensbeheerder != "")
      {
        $query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '{$portefeuille}' AND Vermogensbeheerder = '{$vermogensbeheerder}'";
        $portOk =  ($portRec = $db->lookupRecordByQuery($query))?"voldoet":"voldoet niet";
      }

      if (strlen($client) < 26 AND $client != "")
      {
        $query = "SELECT * FROM Clienten WHERE Client = '{$client}' ";
        $clientOk =  ($clntRec = $db->lookupRecordByQuery($query))?"bestaat":"nieuw";
      }
      $row++;
      if ($row == 1 )
      {
        continue; //skip header
      }
      ?>
      <tr>
        <td><?=$row?></td>
        <td><?=$portefeuille?> (<?=$portOk?>)</td>
        <td><?=$client?> (<?=$clientOk?>)</td>
        <td><?=$vermogensbeheerder?></td>
      </tr>
      <?


    }
?>
  </table>
  <input type="submit" value="Verwerken"/>
  </form>
  <?

  }
  else
  {
    $importfile = $data["file"];
    if (!$handle = @fopen($importfile, "r"))
    {
      echo "FOUT bestand $importfile is niet leesbaar";
      exit;
    }

?>
  <table>
  <thead>
  <tr>
    <td><?= vt('Regel'); ?></td>
    <td><?= vt('portefeuille'); ?></td>
    <td>client</td>
    <td>VB</td>
  </tr>
  </thead>
    <?
    $row      = 0;
    while ($data = fgetcsv($handle, 4096, ";"))
    {
      $clientOk           = false;
      $portOk             = false;
      $clientAdd          = false;
      $portefeuille       = trim($data[0]);
      $client             = trim($data[1]);
      $vermogensbeheerder = trim($data[2]);
      $row++;
      if ($row == 1 )
      {
        continue; //skip header
      }
?>
      <tr>
        <td><?=$row?></td>
        <td><?=$portefeuille?></td>
        <td><?=$client?></td>
        <td><?=$vermogensbeheerder?></td>
      </tr>
      <?

      if ($portefeuille != "" AND $vermogensbeheerder != "")
      {
        $query = "SELECT * FROM Portefeuilles WHERE Portefeuille = '{$portefeuille}' AND Vermogensbeheerder = '{$vermogensbeheerder}'";
        $portOk =  ($portRec = $db->lookupRecordByQuery($query));
      }

      if (strlen($client) < 26 AND $client != "")
      {
        $query = "SELECT * FROM Clienten WHERE Client = '{$client}' ";
        $clientOk =  ($clntRec = $db->lookupRecordByQuery($query));
      }
      else
      {
        $msg[] = "r:{$row}- Waarde client ({$client}) voldoet niet";
        continue;
      }

      if (!$clientOk)
      {
        $query = "
          INSERT INTO Clienten SET 
            add_user = '{$USR}', 
            add_date= NOW(), 
            change_user = '{$USR}', 
            change_date= NOW(), 
            Client = '{$client}'
            ";
        $clientOk =  ($clntRec = $db->executeQuery($query));
        $lastid = $db->last_id();
        $msg[] = "r:{$row}- Client {$client} aangemaakt";
        addTrackAndTrace("Clienten", $lastid, "Client", "", $client, $USR);
        $clientAdd = true;
      }
      if (!$clientOk)
      {
        $msg[] = "r:{$row}- Kon client {$client} niet aanmaken";
      }
      else
      {
        if (!$clientAdd)
        {
          $msg[] = "r:{$row}- Client {$client} bestaat al.";
        }

      }
      if (!$portOk AND $portefeuille != "" AND $clientOk)
      {
        $msg[] = "r:{$row}- Client {$client} niet kunnen koppelen omdat portefeuile niet voldoet {$portefeuille}/{$vermogensbeheerder}";
      }

      if ($portOk AND $clientOk)
      {
        $query = "
          UPDATE Portefeuilles SET 
            Client = '{$client}', 
            change_user = '{$USR}', 
            change_date = NOW() 
          WHERE  
            id = '{$portRec["id"]}'";
        if (!$db->executeQuery($query))
        {
          $msg[] = "r:{$row}- Mislukt client {$client} te koppelen aan portefeuille {$portefeuille}";
        }
        else
        {
          $msg[] = "r:{$row}- Client {$client} (was {$portRec["Client"]}) gekoppeld aan portefeuille {$portefeuille}";
          addTrackAndTrace("Portefeuilles", $portRec["id"], "Client", $portRec["Client"], $client, $USR);
        }
      }
    }
  }
  }
?>


  </table>
  <br/>
  <br/>
  <?
  if (count($msg) > 0)
  {
  ?>
    <div id="msgDone">
      <h4><?= vt('verwerking'); ?>:</h4>
      <ul>
      <li><?=implode("<li>", $msg)?>
      </ul>
    </div>
    <?
  }
    ?>
  <h3><?= vt('Klaar met verwerken'); ?></h3>

<?
  exit;
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
        <legend><?= vt('Bestand inlezen'); ?></legend>
        <br/>

        <div id="loadProducts">
          <div class="lDiv"><?= vt('bestand'); ?>:</div>
          <input type="file" name="uploadFile">
        </div>
        <br/>
        <br/>
        <input type="submit" value="verwerk">
      </fieldset>


<?
