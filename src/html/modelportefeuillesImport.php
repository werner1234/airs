<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/05/25 16:20:52 $
 		File Versie					: $Revision: 1.6 $

 		$Log: modelportefeuillesImport.php,v $
 		Revision 1.6  2019/05/25 16:20:52  rvv
 		*** empty log message ***
 		

*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

session_start();
$_SESSION['NAV'] = "";
session_write_close();

$data=array();
//print_r($_FILES);
if (($handle = fopen($_FILES['modelFile']['tmp_name'], "r")) !== FALSE) 
{
  while (($row = fgetcsv($handle, 128000, ";")) !== FALSE)
     $data[]=$row;
  fclose($handle);
}
//debug($data);

$DB=new DB();
$percentages=array();
$stats=array();
$errorLog='';
$FondsOke=false;
foreach($data as $rowId=>$rowData)
{
  if($rowId==0) //header
  {
    foreach($rowData as $index=>$value)
    {
      if($index>0)
          $Modelportefeuilles[$index]=$value;
  }
}
else //data
{
  foreach($rowData as $index=>$value)
  {

    if($index==0)
    {
      $FondsOke=false;
      $isinCode=$value;
      $query="SELECT Fonds FROM Fondsen WHERE FondsImportCode='".mysql_real_escape_string($isinCode)."'"; //ISINCode
      $DB->SQL($query);
      $fonds=$DB->lookupRecord();
      if($fonds['Fonds'] <> '')
      {
        $FondsOke=true;
      }
      else
      {
        $FondsOke=false;
      }

      if($FondsOke==false)
      {
        if(strtolower($isinCode)=='cash' || strtolower($isinCode)=='liquiditeiten')
        {
          $fonds=array('Fonds'=>'Liquiditeiten');
          $FondsOke=true;
        }
        else
        $errorLog.= "Fonds <met> </met> FondsImportCode '$isinCode' niet gevonden<br>\n";
        }  
      }
      else
      {
        if($FondsOke==true)
        {
          $percentage=str_replace(array(',','%'),array('.',''),$value);
//          debug($percentage);
          if(floatval($percentage) > 0 && strlen($percentage) <= 8)
          {
            $percentages[$Modelportefeuilles[$index]][$fonds['Fonds']]=$percentage;
          }
          else
          {
            if ($percentage != 0)
            {
              $errorLog.= "percentage voldoet niet voor '$isinCode' = {$value} moet > 0 en korter dan 7 karakters <br>\n";
            }

          }

        }
        else
        {
          $errorLog.= "Fonds met FondsImportCode '$isinCode' niet gevonden<br>\n";
        }
      }
    }  
  }
}

//debug($Modelportefeuilles, "Modelportefeuilles");
$datum=$_POST['datum'];
if($datum=='')
  $datum=date('d-m-Y',db2jul(getLaatsteValutadatum()));
$datumDb=date('Y-m-d',form2jul($datum));
//debug($percentages, "percentages");
foreach($percentages as $modelportefeuille=>$modelData)
{
  $query="SELECT ModelPortefeuilles.Portefeuille FROM ModelPortefeuilles 
      Inner Join Portefeuilles ON ModelPortefeuilles.Portefeuille = Portefeuilles.Portefeuille
  WHERE ModelPortefeuilles.Fixed=1 AND ModelPortefeuilles.Portefeuille='".mysql_real_escape_string($modelportefeuille)."'";
  if($DB->QRecords($query) ==1)
  {
    $query="SELECT * FROM ModelPortefeuilleFixed WHERE Portefeuille='".mysql_real_escape_string($modelportefeuille)."' AND Datum='$datumDb'";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
      $huidigeRecords[$data['Fonds']]=$data;
    foreach($modelData as $fonds=>$percentage)
    {
      if(!isset($stats[$modelportefeuille]))
        $stats[$modelportefeuille]=0;
      
      if(isset($huidigeRecords[$fonds]))
      {
        $query="UPDATE ModelPortefeuilleFixed SET Percentage='$percentage',change_date=now(),change_user='$USR' WHERE id='".$huidigeRecords[$fonds]['id']."'";
        unset($huidigeRecords[$fonds]);
      }
      else
      {
        $query="INSERT INTO ModelPortefeuilleFixed SET 
             Portefeuille='".mysql_real_escape_string($modelportefeuille)."',
             Fonds='".mysql_real_escape_string($fonds)."',
             Percentage='".mysql_real_escape_string($percentage)."',
             Datum='$datumDb',
             add_date=now(),
             change_date=now(),
             add_user='$USR',
             change_user='$USR'";
      }
      $DB->SQL($query);
      if($DB->Query())
        $stats[$modelportefeuille]++;
    }
    foreach($huidigeRecords as $recordData)
    {
      $query="DELETE FROM ModelPortefeuilleFixed WHERE id='".$recordData['id']."'";
      $DB->SQL($query);
      $DB->Query();     
    }
    
    $query="UPDATE ModelPortefeuilles SET FixedDatum='$datumDb' WHERE Portefeuille='".mysql_real_escape_string($modelportefeuille)."' ";
    $DB->SQL($query);
    $DB->Query();   
  }
  else
  {
    $errorLog.= "Geen fixed modelportefeuille '$modelportefeuille' gevonden.<br>\n";
  }  

  
}

if($errorLog<>'' || count($stats)>0)
{
  echo template($__appvar["templateContentHeader"],$content);
  if($errorLog<>'')
    echo $errorLog;
  
  if(count($stats)>0)
  {
    echo "<br>\nSuccesvol ingelezen regels:<br>\n";
    echo "<table>\n<tr>\n<td><b>Modelportefeuille</b></td><td><b>Aantal regels</b></td></tr>\n";
    foreach($stats as $portefeuille=>$regels)
    {
     echo "<tr><td>$portefeuille</td><td>$regels</td></tr>\n";
    }
    echo "</tr>\n</table>\n";
  }
  else
  {
    echo "Geen records ingelezen.";
  }
  
  exit;
}

$kal = new DHTML_Calendar();
$content['calendar'] = $kal->get_load_files_code();

echo template($__appvar["templateContentHeader"],$content);

$inp = array ('name' =>"datum",'value' =>$datum,'size'  => "11");

      
?>

<form action="modelportefeuillesImport.php" enctype="multipart/form-data" method="POST" >
<input type="hidden" name="posted" value="true" />

<b>Importeren van fixed modelportefeuilles</b><br><br>
<?php
if($log) echo $log;
?>
<div class="form">
<div class="formblock">
<div class="formlinks"> Modelportefeuille bestand (.csv)</div>
<div class="formrechts">
<input type="file" name="modelFile" size="70">
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> Datum</div>
<div class="formrechts">
<?=$kal->make_input_field("",$inp,"")?>
</div>
</div>

<div class="form">
<div class="formblock">
<div class="formlinks"> Import</div>
<div class="formrechts">
<input type="submit" value="Import">
</div>
</div>

</form>

</div>
<?
echo template($__appvar["templateRefreshFooter"],$content);
?>