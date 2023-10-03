<?php
/*
 		Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/12/30 10:28:00 $
 		File Versie					: $Revision: 1.5 $

 		$Log: CRM_rapportageInstellingImport.php,v $
*/
include_once("wwwvars.php");
// set module naam voor autenticatie leeg = iedereen.
$__appvar["module"] = "";
$_SESSION['submenu'] = "";
$_SESSION['NAV'] = "";

//$content = array();
echo template($__appvar["templateContentHeader"],$content);



$db=new DB();

if($_POST)
{

  $db=new DB();
  $query = "SELECT Export_data_frontOffice,Vermogensbeheerders.Vermogensbeheerder FROM Vermogensbeheerders";
  $db->SQL($query);
  $db->Query();
  $rapportCodes=array();
  $rapporten = array_keys($__appvar['Rapporten']);
  foreach($rapporten as $rapport)
  {
    $rapportCodes['default']['conversie'][$rapport]=$rapport;
  }
  while($data=$db->nextRecord())
  {
    $tmp=unserialize($data['Export_data_frontOffice']);

    $conversie=array();
    foreach($rapporten as $rapport)
    {
      if($tmp[$rapport]['shortName']<>'')
      {
        $conversie[$tmp[$rapport]['shortName']] = $rapport;
      }
      else
        $conversie[$rapport] = '';//$rapport;

    }
    $rapportCodes[$data['Vermogensbeheerder']]['conversie']=$conversie;
  }
  $bestandsnaam='';
  if($_FILES['instellingenBestand']['tmp_name']<>'')
  {
    $bestandsnaam=$_FILES['instellingenBestand']['name'];
    if(substr($_FILES['instellingenBestand']['name'],-4)=='.xls')
    {
      include_once($__appvar["basedir"] . '/classes/excel/XLSreader.php');
      $xls = new Spreadsheet_Excel_Reader();
      $xls->setOutputEncoding('CP1252');
      $xls->read($_FILES['instellingenBestand']['tmp_name']);
      $importData = $xls->sheets[0]['cells'];
    }
/*    elseif(substr($_FILES['instellingenBestand']['name'],-4)=='.csv')
    {
      $importDataRaw = file_get_contents($_FILES['instellingenBestand']['tmp_name']);
      $lines=explode("\n",$importDataRaw);
      foreach($lines as $lineIndex=>$line)
      {
        $parts=explode(";",$line);
        foreach($parts as $partIndex=>$value)
          $importData[$lineIndex][$partIndex]=trim($value);
      }
    }*/
    else
    {
      echo vt("Import file dient een .xls file te zijn.");
      exit;
    }
  }

  $indexedData=array();
  foreach($importData as $rowId=>$rowData)
  {
    $headerRow=false;
    $found=array();
    foreach($rowData as $fieldId=>$fieldData)
    {
      if($fieldData=='Periode' || $fieldData=='Portefeuille')
        $found[$fieldData]=$rowId;
    }
    if(count($found)==2)
    {
      $headerRow = $rowId;
      break;
    }
  }
  $header=$importData[$headerRow];
  $headerConvert=array();
  foreach($header as $index=>$value)
  {
    if(!isset($headerConvert[$value]))
      $headerConvert[$value] = $index;
    else
    {
      echo "$value " . vt('komt meerdere keren voor in de header') . ". (" . vt('Positie') . " ". $headerConvert[$value]." " . vt('en') . " $index). " . vt('Import afgebroken') . ".";exit;
    }
  }

  $periodeVertaling=array('rap_d'=>'Dag','rap_m'=>'Maand','rap_k'=>'Kwartaal','rap_h'=>'Half jaar','rap_j'=>'Jaar');
  $periodeTekstVertaling=array('rap_d'=>'dagrapportage','rap_m'=>'maandrapportage','rap_k'=>'kwartaalrapportage','rap_h'=>'halfjaarrapportage','rap_j'=>'jaarrapportage');
  $verzendMethoden=array('papier','email','portaal','geen');

  foreach($importData as $row=>$rowData)
  {
    if(isset($rowData[$headerConvert['Portefeuille']]))
    {
      $portefeuilleRaw=$rowData[$headerConvert['Portefeuille']];
      if(substr($portefeuilleRaw,0,3)=='P#_')
      {
        $portefeuille=substr($portefeuilleRaw,3);
        $rowData[$headerConvert['Portefeuille']]=$portefeuille;
      }
    }
    
    if(in_array($rowData[$headerConvert['Periode']],array('k','d','m','j','h')))
    {
      foreach($header as $index=>$omschrijving)
      {
        if(!isset($rowData[$index]))
          $rowData[$index]='';
        else
          $rowData[$index]=trim($rowData[$index]);
      }

      foreach ($rowData as $fieldId => $fielData)
      {
        $indexedData[$rowData[$headerConvert['Portefeuille']]]['bestandsInstellingen']['rap_'.$rowData[$headerConvert['Periode']]][$header[$fieldId]]=$fielData;
      }
    }
  }


  foreach($indexedData as $portefeuille=>$instellingen)
  {
    $query="SELECT CRM_naw.id,CRM_naw.rapportageVinkSelectie,Portefeuilles.Vermogensbeheerder
  FROM CRM_naw 
  LEFT JOIN Portefeuilles ON CRM_naw.Portefeuille=Portefeuilles.Portefeuille 
  WHERE CRM_naw.Portefeuille='$portefeuille'";
    if($db->QRecords($query)==1)
    {
      $crmData=$db->nextRecord();
      $indexedData[$portefeuille]['huidigeInstellingen']=unserialize($crmData['rapportageVinkSelectie']);
      $indexedData[$portefeuille]['crmId']=$crmData['id'];
      if($crmData['Vermogensbeheerder']<>'')
        $vermogensbeheerder=$crmData['Vermogensbeheerder'];
      else
        $vermogensbeheerder='default';

      $indexedData[$portefeuille]['Vermogensbeheerder']=$vermogensbeheerder;

      $rapportages=$rapportCodes[$vermogensbeheerder]['conversie'];

      $nieuwePeriodeRapporten=array();
      $nieuweVerzending=array();
      $nieuwAantal=array();

      foreach ($periodeTekstVertaling as $periode=>$omschrijving)
      {
        foreach($instellingen['bestandsInstellingen'][$periode] as $veld=>$waarde)
        {
          foreach($rapportages as $eigenRapport=>$airsRapport)
          {
            if(strtoupper($eigenRapport)==strtoupper($veld))
            {
              if(!isset($nieuwePeriodeRapporten[$periode]))
              {
                $nieuwePeriodeRapporten[$periode] = array();
              }
              if(strtoupper($waarde)=='X')
              {
                $nieuwePeriodeRapporten[$periode][]=$airsRapport;
              }
            }
          }

          foreach($verzendMethoden as $methode)
          {
            if(strtoupper($methode)==strtoupper($veld))
            {
              if(!isset($nieuweVerzending[$periode]))
              {
                $nieuweVerzending[$periode] = array();
              }
              if(strtoupper($waarde)=='X')
              {
                $nieuweVerzending[$periode][$methode] = 1;
              }
            }
            elseif(strtoupper($veld)=='AANTAL')
              $nieuwAantal[$periode]=$waarde;
          }
        }
      }

      $nieuweInstellingen=$indexedData[$portefeuille]['huidigeInstellingen'];
      foreach($nieuwePeriodeRapporten as $periode=>$instellingen)
      {
        if (is_array($instellingen))
          $nieuweInstellingen[$periode]=$instellingen;
      }
      foreach($nieuweVerzending as $periode=>$instellingen)
      {
        if (is_array($instellingen))
          $nieuweInstellingen['verzending'][$periode]=$instellingen;
      }
      foreach($nieuwAantal as $periode=>$aantal)
        $nieuweInstellingen['aantal'][substr($periode,-1)]=$aantal;

      $indexedData[$portefeuille]['nieuweInstellingen']=$nieuweInstellingen;


    }
    else
    {
      //echo $query;
    }
  }

  
  foreach($indexedData as $portefeuille=>$instellingen)
  {
    $query="SELECT id,Portefeuille FROM CRM_naw WHERE id='".$instellingen['crmId']."'";
    if($db->QRecords($query)==1 && is_array($instellingen['nieuweInstellingen']))
    {
      $idData=$db->nextRecord();
      $query="UPDATE CRM_naw SET rapportageVinkSelectie='".mysql_real_escape_string(serialize($instellingen['nieuweInstellingen']))."' WHERE id='".$idData['id']."'";
      $db->SQL($query);
      if($db->Query())
      {
        echo "" . vt('Instellingen voor') . " '$portefeuille' " . vt('aangepast') . ".<br>\n";
        $query="INSERT INTO trackAndTrace SET tabel='CRM_naw',recordId='".$idData['id']."', veld='rapportageVinkSelectie',oudeWaarde='',nieuweWaarde='".mysql_real_escape_string("Import vanuit $bestandsnaam")."',add_date=now(),add_user='$USR'";
        $db->SQL($query);
        $db->Query();
      }
    }
    else
    {
      echo "" . vt('Portefeuille') . " '$portefeuille' " . vt('niet gevonden') . ".".$db->QRecords($query)."<br>\n";
    }
  }
  echo "<br>\n<br>\n";
}




?>
<?= vt('Instellen van rapportages'); ?><br>
<br>
<form action="<?=$PHP_SELF?>" enctype="multipart/form-data" method="POST" name="importForm">
<input type="hidden" name="importeren" value="1">
<input type="file" name="instellingenBestand" value="">
<input type="submit" value="Importeren" />


</form>
<?
// print templateFooter (met default vars)
echo template($__appvar["templateRefreshFooter"],$content);
?>