<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2013/10/19 15:56:28 $
 		File Versie					: $Revision: 1.3 $
 		
 		$Log: runQueries.php,v $
 		Revision 1.3  2013/10/19 15:56:28  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/10/19 08:24:37  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/03/09 09:23:28  cvs
 		*** empty log message ***
 		
 	
*/
include_once("wwwvars.php");
include_once("../config/local_vars.php");
include_once('../classes/AE_cls_progressbar.php');

$__appvar["tempdir"] = $__appvar["basedir"]."/temp/";

if(file_exists('../config/custom_vars.inc'));
  include '../config/custom_vars.inc';

// if poster
if($_POST['posted'])
{
	echo "start importeren... <br><br>";
	if(empty($_FILES['importfile']['name']))
	{
		$_error[] = "Fout: ongeldige bestandsnaam";
	}

	// check error
	if($_FILES['importfile']['error'] != 0)
	{
		$_error[] = "Fout: ".$_FILES['importfile']['error'];
	}

	if (empty($_error))
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

		$importfile = $__appvar["tempdir"]."import".mktime().".gz";
		$tofile 		= $__appvar["tempdir"]."import".mktime().".sql";
		if(move_uploaded_file($_FILES['importfile']['tmp_name'],$tofile))
		{
      $db=new DB();
      echo "Importeren data. <br>"; 
			$prb->moveStep(0);
			$prb->setLabelValue('txt1','Importeren data' );
			$pro_step = 0;
			// tijdelijke import bestand inlezen

			$handle = @fopen($tofile, "r");
			$aantalRegels = (filesize($tofile) / 1000);

			if ($handle)
			{
				// get file size ?
				$pro_multiplier = (100 / ($aantalRegels+1));
				$prb->setLabelValue('txt1','Importeren data');

			   while (!feof($handle))
			   {
						$buffer = fgets($handle, 262144);//6144
            			if(!empty($buffer))
						{
							$pro_step += $pro_multiplier;
							$prb->moveStep($pro_step);
							$db->SQL($buffer);
							
							if(!$db->Query())
							{
								echo "FOUT: in query ".$buffer;
							}
						}
					}
   		}
			else
			{
				echo "FOUT: kan tijdelijke bestand ".$tofile." niet openen.";
			}

		}
		else
		{
				$_error[] = "Fout: upload error.";
		}
	}

	if(!unlink($tofile))
	{
		echo "FOUT: kan tijdelijke bestand ".$tofile." niet verwijderen.";
	}
	$prb->hide();
	echo "Importeren voltooid. <br>"; 
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

<form enctype="multipart/form-data" method="POST">
<input type="hidden" name="MAX_FILE_SIZE" value="64000000">
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->

<b>Importeren data uit bestand</b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>

<div class="form">
<div class="formblock">
<div class="formlinks">Sql file </div>
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