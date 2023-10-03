<?php
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/12/14 14:42:54 $
 		File Versie					: $Revision: 1.7 $
 		
 		$Log: koersImportAAB.php,v $
 		Revision 1.7  2011/12/14 14:42:54  cvs
 		*** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

session_start();
$_SESSION["NAV"] = "";
session_write_close();

$content = array();
echo template($__appvar["templateContentHeader"],$content);
// if poster
if($_POST['posted'])
{

	if(empty($_FILES['importfile']['name']))
	{
		$_error = vt("Fout").": ".vt("ongeldige bestandsnaam");
	}

	// check filetype
	// check filetype
	if( $_FILES['importfile']["type"] != "text/comma-separated-values" &&
	    $_FILES['importfile']["type"] != "text/x-csv" &&
	    $_FILES['importfile']["type"] != "text/csv" &&
	    $_FILES['importfile']["type"] != "application/octet-stream" &&
	    $_FILES['importfile']["type"] != "text/plain" )

	{
		$_error = vt("Fout").": ".vt("verkeerd bestandstype, alleen .csv bestanden zijn toegestaan");
	}
	// check error
	if($_FILES['importfile']["error"] != 0)
	{
		$_error = vt("Fout").": ".$_FILES["error"];
	}
	if($_FILES['importfile']["size"] < 20)
	{
		$_error = vt("Fout").": ".vt("bestand voldoet niet");
	}

	if(empty($datum))
	{
		$_error = vt("Fout").": ".vt("geen datum opgegeven!");
	}
	else
	{
		$dd = explode($__appvar["date_seperator"],$datum);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			$_error = vt("Fout").": ".vt("ongeldige datum opgegeven");
		}
	}

	if (empty($_error))
	{

 		$fondsen = array();
		$fondsenZonderAAB[] = array();
		$prb = new ProgressBar();	// create new ProgressBar
		$prb->pedding = 2;	// Bar Pedding
		$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
		$prb->setFrame();          	                // set ProgressBar Frame
		$prb->frame['left'] = 50;	                  // Frame position from left
		$prb->frame['top'] = 	80;	                  // Frame position from top
		$prb->addLabel('text','txt1','Moment ...');	// add Text as Label 'txt1' and value 'Please wait'
		$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
		$prb->show();	                              // show the ProgressBar

		$prb->moveStep(0);
		$prb->setLabelValue('txt1',vt('Verwerken tijdelijke tabel'));
		$pro_step = 0;

		$importcode = date("ymdH");  //datum als JJJJMMDDUUMM
		$importfile = $__appvar["basedir"]."/html/importdata/koersimport_".$importcode.".csv";
?>
    <b><?=vt("Importlog")?> (<?=vt("importcode")?>: <?=$importcode?>)</b><br>
<?

		$jaar = date("Y",form2jul($datum));
    $date = date("Y-m-d",form2jul($datum));

		$q = "
		SELECT
		  Rekeningmutaties.Fonds,
		  Fondsen.AABCode
		FROM
		 ( Rekeningmutaties,
		  Rekeningen,
		  Portefeuilles )
		JOIN
		  Fondsen ON Rekeningmutaties.Fonds = Fondsen.Fonds
		WHERE
		  Rekeningmutaties.Rekening = Rekeningen.Rekening AND
		  Rekeningmutaties.Fonds = Fondsen.Fonds AND
		  Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
		  Portefeuilles.Einddatum > '".$date."' AND
		  YEAR(Rekeningmutaties.Boekdatum) = '".$jaar."' AND
		  Rekeningmutaties.Verwerkt = '1' AND
		  Rekeningmutaties.Boekdatum <= '".$date."' AND
		  Rekeningmutaties.GrootboekRekening = 'FONDS' 
		  GROUP BY Rekeningmutaties.Fonds
		  ORDER BY Rekeningmutaties.Fonds ";

		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();


		while($fonds = $DB->NextRecord())
		{
		  if ($fonds["AABCode"] <> "")
			 $fondsen[] = $fonds;
			else
			 $fondsenZonderAAB[] = $fonds["Fonds"];
		}

		if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
		{
			$csvRegels = count(file($importfile));
  		$pro_multiplier = 100 / $csvRegels;
			$prb->setLabelValue('txt1',vt('Importeren uit AAB bestand').' ('.$csvRegels.' '.vt("records").')');

			$row = 0;
			$handle = fopen($importfile, "r");

			$ndx=0;
      $dataSet = Array();
			$in571 = false;

			while (($data = fgets($handle, 4096)) !== FALSE)
			{
			  if ($data[0] == " ") $data = substr($data,1);  // als eerste char een spatie deze wegknippen
				$row++;
				$pro_step += $pro_multiplier;
    		$prb->moveStep($pro_step);

    		switch (trim($data))
        {
          case "ABNANL2A":
          case "501":
          case "510":
            //cycle
   		      break;
          case "571":
           	$dataSet[$ndx]["type"] = $row." 571";
           	$in571 = true;
 			      break;
   	      case "-":  // einde record
            $ndx++;
            $in571 = false;
 			      break;
        	default:
        	  if ($in571)
        	  {
 	          if (substr($data,0,5) == ":33B:")
 	          {
   	          $data = str_replace(",",".",$data);
   	          $xx=4;
   	          $_bedrag = '';
   	          while ($xx<strlen($data))
   	          {
   	            $xx++;
   	            $chr = substr($data,$xx,1);
   	            if ($chr >= '0' AND $chr <= '9' OR $chr == ".")
   	              $_bedrag  .= $chr;
   	          }
   	          $dataSet[$ndx]["waarde"] = $_bedrag;
              $ndx++;
              $dataSet[$ndx]["type"] = $row." B571";

 	          }
 	          if (substr($data,0,5) == ":35B:")
 	          {
 	            $dataParts = explode(":",$data);
 	            $_val = explode(" ",$dataParts[2]);

  	          $xx=1;
		          while($xx < count($_val))
		          {
			          if ($_val[$xx] <> "")
			          {
				           $dataSet[$ndx]["fonds"]  = $_val[$xx];
				           break;
			          }
			          $xx++;
		          }
		       }
        	  }
  	       break;
        }
			}
			fclose($handle);
/*  aanpassing juli 2007, cvs
* eerste arrayItem werd overgeslagen in while loop, vervangen vooreen foeach loop			
*/
      foreach ($dataSet as $data) 
			{
			  if ($data["fonds"] <> "")
        {
          $fondsAray[$data["fonds"]] = $data["waarde"];
        }
			}
			$row = 0;

/*  aanpassing juli 2007, cvs
* eerste arrayItem werd overgeslagen in while loop, vervangen vooreen foeach loop			
*/
      foreach ($fondsen as $data) 
			{
			  $output[$row]["AABCode"] = $data["AABCode"];
			  $output[$row]["Fonds"] = $data["Fonds"];

			  if (!empty($fondsAray[$data["AABCode"]]))
        {
          $output[$row]["waarde"] = $fondsAray[$data["AABCode"]];
        }
			  else
        {
          $output[$row]["waarde"] = -1; // geen koers in AABbestand
        }
    	  $row++;
			}

      $errorArray = array();


			for ($c=0;$c < count($output);$c++)
			{
			  if ($output[$c]["waarde"] < 0 )
        {
          $onbekendekoers[] = vt("Geen koerswaarde bij") . " (" . $output[$c]["AABCode"] . ") " . $output[$c]["Fonds"];
        }
			  else
			  {
			    // bestaat koers al voor datum
			    $whereTxt = "WHERE Fonds = '".$output[$c]["Fonds"]."' AND Datum = '".$date."'";
 					$query    = "SELECT id FROM Fondskoersen ".$whereTxt;
					$DB2 = new DB();
 					$DB2->SQL($query);
 					$DB2->Query();
 					$skip = false;
					if($DB2->Records() > 0)
					{
	 					if($_POST["overschrijven"] == 1)
	 					{
							$query = "UPDATE Fondskoersen SET ";
						}
						else
						{
						  $skip = true;
						  $errorArray[] = "Fout: koers bij ".$output[$c]["Fonds"]." bestaat al";
						}
					}
					else
					{
						$query    = "INSERT INTO Fondskoersen SET ";
			 			$query .= " add_date = NOW(), ";
			 			$query .= " add_user = '".$USR."', ";

						$whereTxt = "";
					}

					if (!$skip)
		 			{

			 			$query .= " Fonds  = '".$output[$c]["Fonds"]."', ";
			 			$query .= " Datum  = '".$date."', ";
			 			$query .= " Koers  = '".$output[$c]["waarde"]."', ";
			 			$query .= " import = '".$importcode."', ";
			 			$query .= " change_date = NOW(), ";
			 			$query .= " change_user = '".$USR."' ";

			 			$query .= $whereTxt;
			      $DB3 = new DB();
//			      echo "<hr>".$query;
						$DB3->SQL($query);
						if (!$DB3->Query())
            {
              echo mysql_error();
            }

		 			}

				}
		 }

			if($_POST["log_error"])
			{
			  for ($a=0;$a < count($errorArray); $a++)
			  {
			    if ($a == 0) echo "<HR>";
			    echo "<br> ".$errorArray[$a];
			  }
			  for ($a=0;$a < count($fondsenZonderAAB); $a++)
			  {
			    if ($a == 0) echo "<HR>";
			    echo "<br> ".vt("Geen AABcode bij")." ".$fondsenZonderAAB[$a];
			  }

				for ($a=0; $a < count($onbekendekoers); $a++)
				{
				  if ($a == 0) echo "<HR>";
					echo "<br>".$onbekendekoers[$a];
				}
			}
			$prb->hide();
		}
		else
		{
			$_error = vt("Fout").": ".vt("upload error");
		}
	}

	echo "<BR><BR>".$_error;
	exit;
}

if(!$_FILES['importfile']['name'])
{
// get laatste valutaDatum
$laatsteValuta = getLaatsteValutadatum();

?>

<form enctype="multipart/form-data" action="koersImportAAB.php" method="POST" target="importFrame">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="log_error" value="1" />
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->
<b><?=vt("AAB Koersimport")?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
  <div class="form">
    <div class="formblock">
      <div class="formlinks"><?=vt("Koersimportbestand")?> </div>
      <div class="formrechts">
        <input type="file" name="importfile" size="50">
      </div>
    </div>


    <div class="formblock">
      <div class="formlinks"> &nbsp;</div>
      <div class="formrechts">
        <?=vt("Datum opgeven")?>
        <input type="text" name="datum" value="<?=date("d-m-Y", db2jul($laatsteValuta) + 86400)?>" size="15">
        (dd-mm-jjjj)
      </div>
    </div>


    <div class="formblock">
      <div class="formlinks"> &nbsp;</div>
      <div class="formrechts">
        <input type="checkbox" name="overschrijven" value="1" checked> <?=vt("Aanwezige koersen overschrijven")?>
      </div>
    </div>


    <div class="formblock">
      <div class="formlinks"> &nbsp;</div>
      <div class="formrechts">
        <input type="submit" value="<?=vt("importeren")?>">
      </div>
    </div>

</form>


  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <iframe width="600" height="400" name="importFrame"></iframe>
    </div>
  </div>

</div>
<?
}
echo template($__appvar["templateRefreshFooter"],$content);
