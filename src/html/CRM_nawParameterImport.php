<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/08/27 15:44:57 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: CRM_nawParameterImport.php,v $
 		Revision 1.1  2014/08/27 15:44:57  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2014/05/17 16:34:43  rvv
 		*** empty log message ***
 		

 	
*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

$__appvar["tempdir"] = $__appvar["basedir"]."/temp/";

// if poster
if(isset($_POST['posted']))
{
	echo "start importeren... <br><br>";
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

		$prb = new ProgressBar();	// create new ProgressBar
		$prb->pedding = 2;	// Bar Pedding
		$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
		$prb->setFrame();          	                // set ProgressBar Frame
		$prb->frame['left'] = 50;	                  // Frame position from left
		$prb->frame['top'] = 	80;	                  // Frame position from top
		$prb->addLabel('text','txt1','Bezig ...');	// add Text as Label 'txt1' and value 'Please wait'
		$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
		$prb->show();

		$prb->moveStep(0);
		$prb->setLabelValue('txt1','Start importeren..' );
		$pro_step = 0;

    //$data=file_get_contents($_FILES['importfile']['tmp_name']);
    $handle = fopen($_FILES['importfile']['tmp_name'], "r");
    while ($line = fgetcsv($handle, 1000, ";"))
      $csvdata[]=$line;
    
    $db=new DB();
    $aantalRegels=count($csvdata)+1;
		if($aantalRegels>0)
    {
 			$pro_step = 0;
			$pro_multiplier = (100 / ($aantalRegels+1));
			$prb->setLabelValue('txt1','Importeren data');

      $header=array();
      $beschikbareVelden=array();
      $updateCRM_nawCounter=0;
      $updateVeldenCounter=0;
			foreach($csvdata as $lineIndex=>$velden)
      {
        $uitvoer=array();
        $pro_step += $pro_multiplier;
				$prb->moveStep($pro_step);
    
        if($index==0)
        {
          foreach($velden as $index=>$veld)
            $header[$index]=strtolower($veld);
        
          $query="desc CRM_naw";
          $db->SQL($query);
          $db->Query();
          while($data=$db->nextRecord())
          {
            $veld=strtolower($data['Field']);
            $beschikbareVelden[$veld]=$veld;
          }
          $verwijderVelden=array('id','change_date','change_user');
          foreach($verwijderVelden as $veld)
            unset($beschikbareVelden[$veld]);
          


        }    
        else
        {
          foreach($header as $headerIndex=>$headerVeld)
          {
            $uitvoer[$headerVeld]=$velden[$headerIndex];
          }
          
          $recordId=$uitvoer['id'];
          if($recordId >0 && $db->QRecords("SELECT id FROM CRM_naw WHERE id='".$recordId."'") > 0)
          {
            
            $selectQuery="SELECT id ";
            foreach($uitvoer as $veld=>$nieuweWaarde)
            {
              if(in_array($veld,$beschikbareVelden))
              {
                $selectQuery.=",$veld ";
              }
              else
              {
                if($veld <> 'id')
                  $_error[]="Veld $veld is niet beschikbaar.";
                unset($uitvoer[$veld]);
              }
            }
            $selectQuery.=" FROM CRM_naw WHERE id='".$recordId."'";
            $db->SQL($selectQuery);
            $lastValues=$db->lookupRecord();
            
            //listarray($lastValues);
            $updateQuery="UPDATE CRM_naw SET change_date=now(),change_user='$USR' ";
            $updateNeeded=false;
            foreach($uitvoer as $veld=>$nieuweWaarde)
            {
              if($lastValues[$veld] <> $nieuweWaarde)
              {
                $updateQuery.=", $veld='".mysql_real_escape_string($nieuweWaarde)."'";
                $updateNeeded=true;
                if($__appvar['logAccess'])
                {
                  $trackQuery="INSERT INTO trackAndTrace SET 
                  tabel='CRM_naw',recordId='".$recordId."',veld='$veld',oudeWaarde='".mysql_real_escape_string($lastValues[$veld])."',nieuweWaarde='".mysql_real_escape_string($nieuweWaarde)."',
                  add_date=now(),add_user='$USR'";
                  $db->SQL($trackQuery);
                  $db->Query();
                  $updateVeldenCounter++;
                }
                echo "Update $veld, van (".$lastValues[$veld].") -> (".$nieuweWaarde.") <br>\n";
              } 
            }
            $updateQuery.=" WHERE id='".$recordId."'";
            
           
            if($updateNeeded==true)
            {
              // listarray($updateQuery);
              $db->SQL($updateQuery);
              $db->Query(); 
              $updateCRM_nawCounter++;
            }
          }
          else
            $_error[]="Ongeldige fonds id (".$recordId.").";
        }
        //sleep(1);
      }   
      
		}
		else
		{
				$_error[] = "Fout: upload error.";
		}
	}

	$prb->hide();
	echo "Importeren voltooid. <br>
  (".$updateCRM_nawCounter.") CRM_naw records bijgewerkt. <br>
  (".$updateVeldenCounter.") velden bijgewerkt.<br>"; 

	if(count($_error)>0)
	{
	  foreach($_error as $index=>$error)
			echo $error."<br>";
	}
	exit;
}
else
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
 <div class="form">

<form enctype="multipart/form-data" action="CRM_nawParameterImport.php" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="256000000" />
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->

<b>CRM gegevens uit bestand importeren</b><br><br>


<div class="form">
<div class="formblock">
<div class="formlinks">Importbestand (.csv)</div>
<div class="formrechts">
<input type="file" name="importfile" size="50">
</div>
</div>


<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="submit" value="importeren">
</div>
</div>

</form>

</div>

</body>
</html>
<?
}
?>