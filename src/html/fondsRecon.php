<?php
/*
  Author  						: $Author: cvs $
  Laatste aanpassing	: $Date: 2018/09/23 17:14:23 $
  File Versie					: $Revision: 1.2 $

  $Log: fondsRecon.php,v $
  Revision 1.2  2018/09/23 17:14:23  cvs
  call 7175

  Revision 1.1  2017/03/31 12:39:24  cvs
  eerste commit



 */

include_once("wwwvars.php");

session_start();
$_SESSION[NAV] = "";

//$content = array();
global $USR;


$content['style2'] .= '<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">';
echo template($__appvar["templateContentHeader"], $content);


?>
<style>
  .ar{
    text-align: right;
  }
  tr:nth-child(even) {background: #EEE}
  tr:nth-child(odd) {background: #FFF}
  table{
    border: 1px solid #333;
  }

  td{
    padding:13px;
  }
  .kop{
    background-color: #333;
    color: whitesmoke;
  }
</style>
  <br/>
  <div>Fonds Reconciliatie </div>
  <br/>
  <br/>
<?


if ($_POST['posted'] )
{

//  include_once "../classes/fondsReconClass.php";

  $fndReco = new fondsReconClass($_POST["depot"]);
  $fndReco->initModule();
  if ($_POST["clearTable"] == "new")
  {
    $fndReco->clearTable();
  }

  //debug($fndReco->getStats());


  if ($_FILES['importfile']["error"] != -0)
  {
    $_error = "Fout: bestand niet ingevuld of bestaat niet (" . $_FILES['importfile']['name'] . ")";
    echo $_error;
    exit;
  }
  else
  {
    $row = 0 ;
    $filename = $_FILES['importfile']['tmp_name'];
    $handle = fopen($filename, "r");
?>
    verwerken van <?=count(file($filename))?> dataregels.<br/><br/>
    Bezig met item: <span id="tel">0</span>
<?

ob_flush();
flush();
    while ($data = fgetcsv($handle, 8192, ";"))
    {
      $row++;
      if (trim($data[0]) == "") // lege regels overslaan
      {
        continue;
      }

      if ($row==1)
      {
        continue;
      }
//      debug($data);
      $fndReco->addBank($data[0],$data[1],$data[3],$data[2]);  // bankcode,ISIN,Valuta
      echo '<script>document.getElementById("tel").innerText="'.$row.'";</script>';

      flush();
    }

    fclose($handle);
    unlink($filename);

  }
?>
  <li>bankbestand ingelezen</li>

<?


  foreach ($fndReco->bankrow as $item)
  {
    $search[] = $item[1].";".$item[2];
    $search2[] = (int)$item[0];
  }



  $query = '
  SELECT 
    ISINCode,
    Valuta,
    stroeveCode,
    Fonds,
    beurs
  FROM
    Fondsen  
  WHERE
    CONCAT(ISINCode,";",Valuta) IN ("'.implode('","',$search).'")
    OR
    CAST(stroeveCode AS UNSIGNED) IN ("'.implode('","',$search2).'")';

debug($query);
  $db = new DB();
  $db->executeQuery($query);
  while ($rec = $db->nextRecord())
  {
    $fndReco->addAirs($rec["stroeveCode"],$rec["ISINCode"],$rec["Valuta"],$rec["beurs"],$rec["Fonds"]);
  }

  echo " <li>matchen bank <> AIRS</li>";
  $fndReco->matchRows();

  debug($fndReco->stats);
  echo " <li>start wegschrijven naar TFR tabel ".date("H:i:s")."</li>";
  //debug($fndReco->matchArray);
  $fndReco->generateTableData();
  echo " <li>afgerond wegschrijven naar TFR tabel ".date("H:i:s")."</li>";

echo "<br/><br/><br/><a href='tijdelijkefondsreconList.php'>ga naar tijdelijke fondsrecon</a><br/><br/><br/>";

exit;

}
//include_once "../classes/fondsReconClass.php";

$fndReco = new fondsReconClass("");
//$content = array("javascript"=>'<link rel="stylesheet" href="style/smoothness/jquery-ui-1.11.1.custom.css">');
?>
  <form enctype="multipart/form-data"  method="POST"  name="editForm">
  <!-- MAX_FILE_SIZE must precede the file input field -->
  <input type="hidden" name="posted" value="true" />
    <div class="formblock">
      <div class="formlinks">tabel </div>
      <div class="formrechts">
        <select name="clearTable">
          <option value="new">huidige waardes verwijderen</option>
          <option value="append">aanvullen aan huidige waardes</option>
        </select>
      </div>
    </div>
    <div class="formblock">
      <div class="formlinks">depotbank </div>
      <div class="formrechts">
        <select name="depot">
<?

  foreach ($fndReco->veldArray as $k=>$v)
  {
    echo "\n\t<option value='$k'>$k - $v</option>";
  }
?>

        </select>
      </div>
    </div>

    <div class="formblock">
    <div class="formlinks">bestand </div>
    <div class="formrechts">
      <input type="file" name="importfile"  id="importfile" value=""  >
    </div>
  </div>

  <div class="formblock">
    <br/>
    <br/>
    <input type="submit" value="bestand inlezen">
  </div>
</form>

<script>
  $(document).ready(function()
  {
    $("#importfile").select();
  });
</script>
<?
echo template($__appvar["templateRefreshFooter"], $content);



?>