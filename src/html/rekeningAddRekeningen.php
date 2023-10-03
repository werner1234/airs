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
<?
if ($data["action"] == "fase3")
{
  $db = new DB();
  $msg = array();
  $portArray = array();
  foreach ($data as $k=>$v)
  {
    $fld = explode("__", $k);
    $port = $_SESSION["rekAanmaken"][$fld[1]];
    if ($fld[0] == "rek")
    {
      if ($fld[2] == "EUR")
      {
        $query = "INSERT INTO Rekeningen SET
            add_user            = '{$USR}'
          , add_date            = NOW()
          , change_user         = '{$USR}'
          , change_date         = NOW()
          , Rekening            = '{$port["portefeuille"]}EUR'
          , Portefeuille        = '{$port["portefeuille"]}'
          , consolidatie        = 0
          , Inactief            = 0
          , Memoriaal           = 0
          , Depotbank           = '{$port["Depotbank"]}'
          , Valuta              = 'EUR'
          , Beleggingscategorie = 'Liquiditeiten'

        ";
      }
      else
      {
        $query = "INSERT INTO Rekeningen SET 
            add_user            = '{$USR}'
          , add_date            = NOW()
          , change_user         = '{$USR}'
          , change_date         = NOW()
          , Rekening            = '{$port["portefeuille"]}MEM'
          , Portefeuille        = '{$port["portefeuille"]}'
          , consolidatie        = 0
          , Inactief            = 0
          , Memoriaal           = 1
          , Depotbank           = '{$port["Depotbank"]}'
          , Valuta              = 'EUR'
          , Beleggingscategorie = 'Liquiditeiten'

        ";
      }
//      debug($query);
      if ($db->executeQuery($query))
      {
        $msg[] = "Rekening {$fld[1]}{$fld[2]} is toegevoegd";
      }
      else
      {
        $msg[] = "Mislukt rekening {$fld[1]}{$fld[2]} toe te voegen";
      }
      $portArray[] = $fld[1];
    }
  }

  if (count($portArray) > 0)
  {
    foreach ($portArray as $portefeuille)
    {
      $msg[] = "Consolidatie controle voor $portefeuille";
      $con = new AIRS_consolidatie();
      $VPs = $con->ophalenVPsViaPortefeuille($portefeuille);
      if ( count($VPs) > 0 )
      {
        $con->bijwerkenConsolidaties($VPs);
      }
    }
  }
  ?>
  <h2><?= vt('Verslag rekeningen aanmaken bij nieuwe portefeuilles'); ?></h2>
  <?
  if (count($msg) > 0)
  {
  ?>
    <div id="msgDone">
      <ul>
      <li><?=implode("<li>", $msg)?>
      </ul>
    </div>
    <?
  }
    ?>
  <h3><?= vt('Klaar met verwerken'); ?></h3>
<?
//  debug($data);
//  debug($_SESSION["rekAanmaken"]);

  exit;
}

if ($data["action"] == "fase2")
{


  $d = explode("-", $data["datum"]);
  if ($d[0] < 1 OR $d[0] > 31 OR
    $d[1] < 1 OR $d[1] > 12 OR
    $d[2] != date("Y"))
  {
    $datum = date("Y-m-d");
  }
  else
  {
    $datum = "{$d[2]}-{$d[1]}-{$d[0]}";
  }
  $db  = new DB();
  $db1 = new DB();
  $query = "
    SELECT 
      * 
    FROM 
      Portefeuilles 
    WHERE 
      add_date >= '{$datum}' AND 
      Depotbank IN ('SAXO', 'AAB', 'BIN', 'TGB', 'INT', 'OVE' ) AND 
      consolidatie = 0
      ";
  $portRow = array();
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {


    $p = $rec["Portefeuille"];
    if ($rec["Depotbank"] == "AAB" AND strlen($p) != 9)
    {
      // alleen AAB als porteuille 9 lang is..
      continue;
    }

    $portRow[$p]["portefeuille"] =  $rec["Portefeuille"];
    $portRow[$p]["Vermogensbeheerder"] =  $rec["Vermogensbeheerder"];
    $portRow[$p]["Depotbank"] =  $rec["Depotbank"];
    $portRow[$p]["add_date"] =  $rec["add_date"];
    $portRow[$p]["add_user"] =  $rec["add_user"];


    $query = "
      SELECT 
        * 
      FROM 
        Rekeningen 
      WHERE 
        Portefeuille = '{$rec["Portefeuille"]}'    OR 
            Rekening = '{$rec["Portefeuille"]}MEM' OR
            Rekening = '{$rec["Portefeuille"]}EUR' 

        
        ";

    $db1->executeQuery($query);
    while ($rekRec = $db1->nextRecord())
    {
      if ($rekRec["Valuta"] == "EUR")
      {
        if ($rekRec["Memoriaal"] == 0)
        {
          $portRow[$p]["rekEUR"] = $rekRec["Rekening"]." (portefeuille: ".$rekRec["Portefeuille"].")";
        }
        else
        {
          $portRow[$p]["rekMEM"] = $rekRec["Rekening"]." (portefeuille: ".$rekRec["Portefeuille"].")";
        }
      }
    }
  }
//debug($portRow);
  ?>
  <h2><?= vt('Portefeuilles toegevoegd na'); ?> <?=$data["datum"]?></h2>
  <form method="post" >
    <input type="hidden" name="action" value="fase3">
  <table>
    <thead>
    <tr>
      <td><?= vt('portefeuille'); ?></td>
      <td><?= vt('depotbank'); ?></td>
      <td><?= vt('VB'); ?></td>
      <td><?= vt('toegevoegd'); ?></td>
      <td><?= vt('EUR rekening'); ?></td>
      <td><?= vt('MEM rekening'); ?></td>
    </tr>
    </thead>
    <?
    $_SESSION["rekAanmaken"] = $portRow;
    foreach ($portRow as $item)
    {
      $r .= "
        <tr>
          <td>{$item["portefeuille"]}</td>
          <td>{$item["Depotbank"]}</td>
          <td>{$item["Vermogensbeheerder"]}</td>
          <td>".$fmt->format("@D{form}", $item["add_date"])."({$item["add_user"]})</td>";
      if ($item["rekEUR"] != "")
      {
        $r .=  "<td>{$item["rekEUR"]}</td>";
      }
      else
      {
        $r .=  "<td><input type='checkbox' name='rek__{$item["portefeuille"]}__EUR'> {$item["portefeuille"]}EUR aanmaken </td>";
      }
      if ($item["rekMEM"] != "")
      {
        $r .=  "<td>{$item["rekMEM"]}</td>";
      }
      else
      {
        $r .=  "<td><input type='checkbox' name='rek__{$item["portefeuille"]}__MEM'> {$item["portefeuille"]}MEM aanmaken </td>";
      }
      $r .= "</tr>";
    }
    echo $r;
    ?>

  </table>
  <input type="submit" value="Verwerken">
  </form>
  <?php

  exit;
}



?>

    <h2><?= vt('Rekeningen aanmaken bij nieuwe portefeuilles'); ?></h2>
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
    <form method="post" id="theForm">
    <input type="hidden" name="action" value="fase2" />

      <fieldset>
        <legend><?= vt('Geef datum op'); ?></legend>
        <br/>

        <div>
          <div class="lDiv"><?= vt('Portefeuilles aangemaakt na'); ?>: </div>
          <input name="datum"  class="AIRSdatepicker" size="12" value="<?=date("d-m-Y", mktime()-86400)?>"/>

        </div>
        <br/>
        <br/>
        <input type="submit" value="verwerk">
      </fieldset>

      <script>
        $(document).ready(function(){
          $( ".AIRSdatepicker" ).datepicker({
            showOn: "button",
            buttonImage: "javascript/calendar/img.gif",//"images/datePicker.png",
            buttonImageOnly: true,
            dateFormat: "dd-mm-yy",
            dayNamesMin: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"],
            monthNames: ["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],
            monthNamesShort: [ "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ],
            nextText: "volgende maand",
            prevText: "vorige maand",
            currentText: "huidige maand",
            changeMonth: true,
            changeYear: true,
            yearRange: '1900:2050',
            closeText: "sluiten",
            showAnim: "slideDown",
            showButtonPanel: true,
            showOtherMonths: true,
            selectOtherMonths: true,
            numberOfMonths: 1,
            showWeek: true,
            firstDay: 1
          });
        });
      </script>
<?
