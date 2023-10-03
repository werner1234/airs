<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2019/12/30 10:28:00 $
 		File Versie					: $Revision: 1.5 $
*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

$__appvar["tempdir"] = $__appvar["basedir"]."/temp/";

// if poster
if(isset($_POST['posted']))
{

  $_error=array();
	if(empty($_FILES['importfile']['name']))
	{
		$_error[] = "Fout: ongeldige bestandsnaam";
	}

	// check error
	if($_FILES['importfile']['error'] != 0)
	{
		$_error[] = "Fout: ".$_FILES['importfile']['error'];
	}
	

	if(count($_error)==0)
	{

	  ?>
    <html>
    <head>
      <title>
        AIRS vermogensregistratie
      </title>
      <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
      <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">
      <script language="JavaScript" TYPE="text/javascript">
      </script>
    </head>
  
  <body>
    <?
		$prb = new ProgressBar();	// create new ProgressBar
		$prb->pedding = 2;	// Bar Pedding
		$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
		$prb->setFrame();          	                // set ProgressBar Frame
		$prb->frame['left'] = 50;	                  // Frame position from left
		$prb->frame['top'] = 	80;	                  // Frame position from top
		$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
		$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
		$prb->show();
    
    $importeren=$_POST['import'];

		$prb->moveStep(0);
		$pro_step = 0;

    //$data=file_get_contents($_FILES['importfile']['tmp_name']);
    $handle = fopen($_FILES['importfile']['tmp_name'], "r");
    while ($line = fgetcsv($handle, 1000000, ";"))
      $csvdata[]=$line;
    

    $db=new DB();
    $aantalRegels=count($csvdata)+1;
		if($aantalRegels>0)
    {

 			$pro_step = 0;
			$pro_multiplier = (100 / ($aantalRegels+1));
			if($importeren==1)
      {
        $prb->setLabelValue('txt1', 'Importeren data');
        echo "start importeren... <br><br>";
      }
			else
      {
        $prb->setLabelValue('txt1', 'Testen data');
        echo "Testen data... <br><br>";
      }
			
			$tabelObjectNaam=$_POST['tabel'];
      $object=new $tabelObjectNaam();
     // listarray($object);
      $header=array();
      $errorArray=array();
      $csvVelden=array();
      $importVelden=array();
      $objectVelden=array();
      $objectKeyVelden=array();
      $updateRecords=array();
      $updateRegelCounter=0;
			foreach($csvdata as $lineIndex=>$velden)
      {
        $uitvoer=array();
        $pro_step += $pro_multiplier;
				$prb->moveStep($pro_step);
    
        if($index==0)
        {
          foreach($velden as $index=>$veld)
          {
            $header[$index] = strtolower($veld);
            $csvVelden[strtolower($veld)] = $veld;
          }
          

          foreach($object->data['fields'] as $veld=>$veldData)
          {
            $lVeld=strtolower($veld);
            if($veld=='id')
            {
              // Sleutelveldoverstlaan.
            }
            elseif($veldData['key_field']<>'')
            {
              $objectKeyVelden[$lVeld] = $veld;
            }
            else
            {
              $objectVelden[$lVeld] = $veld;
            }
          }
          foreach($csvVelden as $lVeld=>$veld)
          {
       
            if(isset($objectVelden[$lVeld]))
            {
             // echo "Veld $veld kan worden ingelezen.<br>\n";
              $importVelden[$lVeld]=$veld;
            }
            elseif(isset($objectKeyVelden[$lVeld]))
            {
              echo "Sleutelveld $veld kan niet worden ingelezen.<br>\n";
              
            }
            else
            {
              if($veld <> 'id')
              {
                echo "Veld $veld niet gevonden.<br>\n";
              }
            }
           
          }
          if(!in_array('id',$header))
          {
            echo "Geen id in de header gevonden. Import afgebroken.";
            listarray($header);
            exit;
          }
        }
        else
        {
          foreach($header as $headerIndex=>$headerVeld)
          {
            $uitvoer[strtolower($headerVeld)]=$velden[$headerIndex];
          }
          if($uitvoer['id']<1)
          {
            echo "Record mist een id.";
          }
          else
          {
            $updateRecord=false;
            $object=new $tabelObjectNaam();//Fonds();
            $editObject=new editObject($object);
            $editObject->__appvar=$__appvar;

            foreach($uitvoer as $lveld=>$veldData)
            {
              if($lveld=='id')
                $objectData['id']=$veldData;
              if($objectVelden[$lveld]<>'')
                $objectData[$objectVelden[$lveld]] = $veldData;
            }

            $editObject->controller('edit',$objectData);
            if($object->get('id')<1)
            {
              echo "Geen record met id ".$uitvoer['id']." gevonden.<br>\n";
              $errorArray[$uitvoer['id']]= "Geen record met id ".$uitvoer['id']." gevonden.<br>\n";
              continue;
            }
           // listarray($object->data['fields']);
            $meldingOmschrijving="id: ".$uitvoer['id'].", ";
            foreach($objectKeyVelden as $lVeld=>$veld)
            {
              $meldingOmschrijving.="$lVeld: ".$object->get($objectVelden[$veld])."";
            }
            foreach($importVelden as $lVeld=>$veld)
            {
              $huidigeWaarde=$object->get($objectVelden[$lVeld]);

              if($object->data['fields'][$veld]['db_type']=='datetime' || $object->data['fields'][$veld]['db_type']=='date')
              {
                $parts=explode("-",substr($huidigeWaarde,0,10));
                if(count($parts)==3)
                  $huidigeWaarde=$parts[2].'-'.$parts[1].'-'.$parts[0];
                if($uitvoer[$lVeld]=='0000-00-00 00:00:00' && ($huidigeWaarde=='00-00-0000'||$huidigeWaarde=='0000-00-00'))
                {
                  $huidigeWaarde='0000-00-00 00:00:00';
                }
              }

              if($huidigeWaarde <> $uitvoer[$lVeld])
              {
                $updateRecords[$uitvoer['id']][$veld]['oud']=$huidigeWaarde;
                $updateRecords[$uitvoer['id']][$veld]['nieuw']=$uitvoer[$lVeld];
                $updateRecord=true;
              }
            }
  
            if($updateRecord==true)
            {
              $editObject->setFields();
              if ($object->validate())
              {
                if($importeren==true)
                {
                  $object->save();
                }
                foreach ($updateRecords[$uitvoer['id']] as $veld => $waarden)
                {
                  echo "$meldingOmschrijving $veld van '".$waarden['oud']."' naar '".$waarden['nieuw']."'<br>\n";
                  if($importeren==true)
                    $updateRegelCounter++;
                }
              }
              else
              {
                if ($object->error == 1)
                {
                  $errorMelding="<span class='input_error'>$meldingOmschrijving ";
                  foreach ($updateRecords[$uitvoer['id']] as $veld => $waarden)
                  {
                    //$object->data['fields'][$veld]['form_class']
                    $errorMelding.= "$veld van '" . $waarden['oud'] . "' naar '" . $waarden['nieuw'] . "' : " . $object->data['fields'][$veld]['error'];
                   // $errorArray[$uitvoer['id']]= "<span class='input_error'>$meldingOmschrijving $veld van '" . $waarden['oud'] . "' naar '" . $waarden['nieuw'] . "' : " . $object->data['fields'][$veld]['error'] . "</span><br>\n";
                  }
                  foreach($object->data['fields'] as $veld=>$veldData)
                  {
                    if(isset($object->data['fields'][$veld]['error']))
                    {
                      $errorMelding.=",$veld ".$object->data['fields'][$veld]['error'];
                    }
                  }
                  $errorMelding.= " </span><br>\n";
  
                  echo $errorMelding;
                }
              }
            }
          }
        }
      }
      
		}
		else
		{
				$_error[] = "Fout: upload error.";
		}


	$prb->hide();
	echo "(".$updateRegelCounter.") regels bijgewerkt. <br>";
  }
	if(count($_error)>0)
	{
	  foreach($_error as $index=>$error)
			echo $error."<br>";
  }
	if(count($errorArray) > 0)
  {
    echo "Samenvatting van foutmeldingen:<br>\n";
    foreach($errorArray as $index=>$error)
      echo $index."|".$error;
  }
	
	exit;
}
else
{
  
  $tabelObjecten=$__appvar['tabelObjecten'];
  //$tabellen=array('Fondsen');
  
  $tabellen=array();
  $objectAantallen=array();
  foreach($tabelObjecten as $object)
  {
    if($object=='Rekeningmutaties_v2')
      continue;
    $tmp=new $object();
    $aantal=count($tmp->data['fields']);
    
    if(!isset($tabellen[$tmp->data['table']]))
    {
      $tabellen[$tmp->data['table']] = $object;
      $objectAantallen[$tmp->data['table']]=$aantal;
    }
    else
    {
       if($aantal>$objectAantallen[$tmp->data['table']])
       {
         $tabellen[$tmp->data['table']] = $object;
         $objectAantallen[$tmp->data['table']]=$aantal;
       }
    }
   // $tabellenOptions .= "<option value='$tabel'>".$object->data['table']."</option>";
  }
  $tabellenOptions='';
  natcasesort($tabellen);
  foreach($tabellen as $tabel=>$objectNaam)
  {
    $tabellenOptions .= "<option value='$objectNaam'>".$tabel."</option>";
  }
?>
<html>
  <head>
    <title>
      AIRS vermogensregistratie
    </title>
    <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
    <link href="style/workspace.css" rel="stylesheet" type="text/css" media="screen">
    <script language="JavaScript" TYPE="text/javascript">
    </script>
  </head>

 <body>
 <div class="form">
<form enctype="multipart/form-data" action="tabelDataImport.php" method="POST" target="importFrame">
<input type="hidden" name="MAX_FILE_SIZE" value="256000000" />
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->

<b><?= vt('Data uit bestand importeren'); ?></b><br><br>
  
  <div class="form">
    <div class="formblock">
      <div class="formlinks"><?= vt('Tabel'); ?></div>
      <div class="formrechts">
        <select type="select" name="tabel">
          <?=$tabellenOptions?>
        </select>
      </div>
    </div>

<div class="form">
<div class="formblock">
<div class="formlinks"><?= vt('Importbestand (.csv)'); ?></div>
<div class="formrechts">
<input type="file" name="importfile" size="50">
</div>
</div>


<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="verwerken"> <input type="checkbox" name="import" value="1" /> <?= vt('voorgestelde aanpassingen doorvoeren.'); ?>
</div>
</div>
  
  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <iframe width="600" height="400" name="importFrame"></iframe>
    </div>
  </div>

</form>

</div>

</body>
</html>
<?
}
?>