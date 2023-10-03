<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/03/19 11:45:16 $
    File Versie         : $Revision: 1.4 $

    $Log: taken_ImportFase2.php,v $
    Revision 1.4  2018/03/19 11:45:16  cvs
    call 6440

    Revision 1.3  2018/03/07 09:14:03  cvs
    call 6440

    Revision 1.2  2018/03/07 09:00:45  cvs
    call 6440

    Revision 1.1  2018/03/06 14:32:02  cvs
    call 6440

    Revision 1.5  2018/02/07 13:19:32  cvs
    call 6549

    Revision 1.4  2017/12/01 11:20:38  cvs
    check of tempdir bestaat, aanmaken indien niet aanwezig

    Revision 1.3  2017/11/17 08:03:57  cvs
    call 6145

    Revision 1.2  2017/11/13 13:31:21  cvs
    call 6145 bevindingen

    Revision 1.1  2017/11/08 07:31:26  cvs
    call 6145



*/

include_once("wwwvars.php");


$db = new DB();

$error = array();
include '../classes/simplexlsx.class.php';
$file = $_FILES['bestand']['name'];



if (substr(strtolower($_FILES['bestand']['name']),-5) != ".xlsx")
{
  $error[] = "Fout: bestand is geen .xlsx";
}


if(!move_uploaded_file($_FILES['bestand']['tmp_name'],$file))
{
   $error[] = "Fout kan bestand niet lezen";
}

if (count($error) == 0)
{
  include_once dirname(__FILE__) . '/../classes/PHPExcel/ZipArchive.php';
  require_once dirname(__FILE__) . '/../classes/PHPExcel/IOFactory.php';

  $objPHPExcel = PHPExcel_IOFactory::load($file);

  $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();

  foreach ($rowIterator as $row)
  {
    $c++;
    $cellIterator = $row->getCellIterator("A","I");

    foreach ($cellIterator as $cell)
    {

      if ($cell->getColumn() == "A" AND ($cell->getCalculatedValue() == null OR trim($cell->getCalculatedValue()) == ""))
      {
        break;
      }
      $data[$row->getRowIndex()][$cell->getColumn()] = $cell->getCalculatedValue();

    }

    if ($c > 500)
    {
      break;
    }

  }

}
if (strtolower($data[1]["A"]) != "gebruiker" OR strtolower($data[1]["B"]) != "zichtbaar na")
{
  $error[] = "Fout: eerste regel is geen correcte header";
}

echo template($__appvar["templateContentHeader"],$content);
?>
  <link rel="stylesheet" href="widget/css/font-awesome.min.css">
  <link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">



<style>
  legend{
    background: rgba(20,60,90,1);
    color: white;
    width:25%;
    padding:5px;
  }
  #msg{
    padding: 15px;
    color:red;
    font-weight: bold;
  }
  .container{
    width: 800px;
  }
  #msg{
    display: none;

    background: rgba(223,235,250,1);;
    border-radius: 5px;
    color: #333;
    font-weight: bold;
    padding: 30px;
    margin-bottom: 20px;
  }

  button{
    padding:10px 15px 10px 15px ;
    background: rgba(20,60,90,1);
    color: white;
    border: 0px;
  }

  h2{ font-size: 1.2em; font-weight: bold}
  tr:nth-child(even) {background: #EEE}
  tr:nth-child(odd) {background: #FFF}
  .k1{ min-width: 250px}
  .k2{ width: 30px}
  .k3{ min-width: 250px}
  td{ padding: 5px;}
  th{ padding: 5px;}
  .head{ background: rgba(20,60,90,1); color: white; }
  .container{
    width: 800px;
  }

  .filledRow td{
    background: #FFF;
    border-top:3px rgba(20,60,90,1) solid;

  }
.notOk{
  color:red;
}
</style>
<?

if (count($error) > 0)
{
?>
  <h2>Foutmelding:</h2>
  <ul>
<?
    foreach ($error as $e)
    {
      echo "<li>$e</li>";
    }
?>
  </ul>
<?
  exit();
}

$importOk = true;
$tel = 0;
?>

<h2>Validatie van het bestand</h2>
<br/>
<br/>
<table cellpadding="0" cellspacing="0" id="dataTable">
  <thead>
  <tr>
    <td class="head">#</td>
    <td class="head">Gebruiker</td>
    <td class="head">Zichtbaar na</td>
    <td class="head">Spoed</td>
    <td class="head">Clnt ID</td>
    <td class="head">Client</td>
    <td class="head">Soort</td>
    <td class="head">Betreft</td>
    <td class="head">Tekst</td>
  </tr>
  </thead>
<?
$dataOut = array();

foreach ($data as $row)
{
  $tel++;
  if ($tel == 1)//
  {
    continue;
  }
?>
  <tr>
    <td><?=$tel?></td>
    <td><?=taCheckUser($row["A"])?></td>
    <td><?=taCheckDate($row["B"])?></td>
    <td><?=taCheckSpoed($row["C"])?></td>
    <td><?=taCheckClient($row["D"])?></td>
    <td style="color: #999"><i><?=$row["E"]?></i></td>
    <td><?=taCheckSoort($row["F"])?></td>
    <td><?=$row["G"]?></td>
    <td><?=nl2br($row["H"])?></td>
  </tr>
<?
  $dataOut[] = $row;
}

?>
</table>
<?

if ( !$importOk )
{
  echo "<h2>Fouten in importbestand, import afgebroken</h2>";
  echo "<span style='color:red; font-weight: bold'>Foute waardes zijn in rood weergegeven</span>";
}
else
{
  echo "<h2>Validatie geslaagd</h2>";
  $_SESSION["taakImport"] = $dataOut;

  echo "<a href='taken_ImportFase3.php' ><button> importeer taken </button></a>";
}

function taCheckUser($user)
{
  global $importOk, $db;
  $query = "SELECT Gebruiker FROM Gebruikers WHERE Gebruiker = '$user'";
  if ($rec = $db->lookupRecordByQuery($query))
  {
    return $user;
  }
  else
  {
    $importOk = false;
    return "<span class='notOk'>$user</span>";
  }
}

function taCheckDate($date)
{
  global $importOk;
  if (checkdate ( substr($date,4,2), substr($date,6,2) , substr($date,0,4) ))
  {
    return substr($date,6,2)."-".substr($date,4,2)."-".substr($date,0,4);
  }
  else
  {
    $importOk = false;
    return "<span class='notOk'>$date</span>";
  }
}

function taCheckSpoed($spoed)
{
  global $importOk;
  if ((int)$spoed != 0 AND (int)$spoed != 1)
  {
    $importOk = false;
    return "<span class='notOk'>$date</span>";
  }
  else
  {
    return ($spoed == 1)?"ja":"nee";
  }
}


function taCheckClient($client)
{
  global $importOk, $db, $row, $tel;
  $query = "SELECT naam, id FROM CRM_naw WHERE id = '".(int)$client."' AND aktief = 1";
  if ($clnt = $db->lookupRecordByQuery($query))
  {
    $row["E"] = $clnt["naam"];
    return $clnt["id"];
  }
  else
  {
    $importOk = false;
    return "<span class='notOk'>$client</span>";
  }

}

function taCheckSoort($soort)
{
  global $importOk, $db;
  $soortArray = array();
  $query = "SELECT if(waarde<>'',waarde,omschrijving) AS waarde ,omschrijving FROM CRM_selectievelden WHERE module IN('agenda afspraak','standaardTaken') ORDER BY omschrijving";
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $soortArray[] = $rec["waarde"];
  }
  if (in_array($soort, $soortArray))
  {
    return $soort;
  }
  else
  {
    $importOk = false;
    return "<span class='notOk'>$soort</span>";
 }
}

echo template($__appvar["templateRefreshFooter"],$content);


