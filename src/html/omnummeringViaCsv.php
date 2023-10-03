<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2017/04/10 06:29:21 $
File Versie					: $Revision: 1.2 $

$Log: omnummeringViaCsv.php,v $
Revision 1.2  2017/04/10 06:29:21  rvv
*** empty log message ***

*/
include_once("wwwvars.php");

$data = array_merge($_GET,$_POST);

if($_POST['posted'])
{
  // check filetype
  $_error=array();
  if($_FILES['csvFile']['type'] != "text/comma-separated-values" &&
    $_FILES['csvFile']['type'] != "text/x-csv" &&
    $_FILES['csvFile']['type'] != "text/csv" &&
    $_FILES['csvFile']['type'] != "application/octet-stream" &&
    $_FILES['csvFile']['type'] != "application/vnd.ms-excel" &&
    $_FILES['csvFile']['type'] != "text/plain")
  {
    $_error[] = vt("FOUT").": ".vt("verkeerd bestandstype")."(".$_FILES['csvFile']['type']."), ".vt("alleen text bestanden zijn toegestaan");
  }
  // check error

  if($_FILES['csvFile']['error'] != 0)
  {
    $_error[] = vt("FOUT").": ".vt("CSV bestand niet ingevuld of bestaat niet")." (".$_FILES['csvFile']['name'].")";
  }

  if (count($_error) == 0)
  {
    //$csvData=file_get_contents ($_FILES['csvFile']['tmp_name']);
    $handle = fopen($_FILES['csvFile']['tmp_name'], "r");

    $tabel=$_POST['tabel'];
    $conversie=array();
    while ($row = fgetcsv($handle, 1000, ","))
    {

      $conversie[$tabel][]=array('oud'=>$row[0],'nieuw'=>$row[1]);
    }
    if (count($conversie)<>1)
      $_error[] = vt("FOUT").": ".vt("Geen data gevonden");

  }
  foreach($conversie as $tabel=>$regels)
  {
    echo vt("Aanmaken queries voor de mutaties op de tabel")." $tabel :<br>\n";
    foreach($regels as $regel)
    {
      $key='';
      if($tabel=='Portefeuilles')
      {
        $object = new Portefeuilles();
        $key='Portefeuille';
      }
      elseif($tabel=='Rekeningen')
      {
        $object = new Rekeningen();
        $key='Rekening';
      }
      else
      {
        echo "Geen tabel gekozen?";
        exit;
      }

      $object->getByField($key,$regel['oud']);
      $recordId=$object->get('id');
      if($recordId<1)
      {
        echo "<br /> -- ".$regel['oud']." ".vt("niet gevonden in")." $tabel";
      }
      else
      {

        $editObject = new editObject($object);
        $editObject->action = 'update';
        $editObject->dataBegin = $editObject->object->data;
        $editObject->data   = array($key =>$regel['nieuw'],'id'=>$recordId);
        $editObject->setFields();
        $queries=$editObject->updateKeys(true);
        $query="UPDATE $tabel SET $key = '".$regel['nieuw']."', change_date=now(),change_user='$USR' WHERE id=$recordId AND $key = '".$regel['oud']."'";
        echo "<pre>";
        echo $query.";\n";
        $onderdrukken=false;
        foreach($queries as $query)
        {
          if($_POST['uitsluiten']==1)
          {
            if (strpos($query, 'UPDATE GeconsolideerdePortefeuilles SET') !== false)
            {
              $onderdrukken = true;
            }
            elseif (strpos($query, 'UPDATE portefeuilleClusters SET') !== false)
            {
              $onderdrukken = true;
            }
            else
            {
              $onderdrukken = false;
            }
          }
            
          if($onderdrukken==false)
            echo $query . ";\n";
        }
        echo "</pre>";
       }

    }
  }

  if (count($_error) > 0)
  {
    echo vt("Foutmelding").": ";
    for ($x=0; $x < count($_error); $x++)
    {

    }
    exit();
  }

  echo template($__appvar["templateContentHeader"],$content);

  exit;
}


session_start();
$_SESSION['NAV'] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);

?>
  <form action="<?=$PHP_SELF?>" enctype="multipart/form-data" method="POST"   name="controleForm" target="convertFrame">
    <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="posted" value="true" />
    <input type="hidden" name="bank" value="<?=$bank?>" />

    <!-- Name of input element determines name in $_FILES array -->



    <?php

    echo "<b>".vt("Conversie starten")."</b><br><br>";
    if($_error) echo "<b style=\"color:red;\">".$_error."</b>";



    ?>

    <div class="form">
      <div class="formblock">
        <div class="formlinks"><?=vt("Bestand")?> </div>
        <div class="formrechts">
          <input type="file" name="csvFile" size="70" /> <?=vt("(oudewaarde,nieuwewaarde)")?>
        </div>
      </div>

      <div class="formblock">
        <div class="formlinks">Type</div>
        <div class="formrechts">
          <input type="radio" name="tabel" value="Portefeuilles"/><?=vt("Portefeuilles")?>
          <input type="radio" name="tabel" value="Rekeningen"/><?=vt("Rekeningen")?>
        </div>
      </div>
  
      <div class="formblock">
        <div class="formlinks"><?=vt('Consolidaties/clusters uitsluiten')?></div>
        <div class="formrechts">
          <input type="checkbox" name="uitsluiten" value="1"/>
        </div>
      </div>
      

      <div class="formblock">
        <div class="formlinks"> &nbsp;</div>
        <div class="formrechts">
          <?
          if(checkaccess())
          {
            echo '<input type="button" value="'.vt("Start conversie").'" onClick="document.controleForm.submit();">';
          }
          else
          {
            echo vt('Onvoldoende rechten om deze conversie te gebruiken.');
          }
           ?>
        </div>
      </div>
  </form>

  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <iframe width="800" height="400" name="convertFrame" ><?=vt("meldingen")?>..</iframe>
    </div>
  </div>

  </div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
