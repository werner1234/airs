<?php

	
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
		$prb->show();	                              // show the ProgressBar

		$prb->moveStep(0);
		$prb->setLabelValue('txt1','Verwerken tijdelijke tabel');
		$pro_step = 0;

		$importcode = date("YmdHi");  //datum als JJJJMMDDUUMM
		$importfile = $__appvar["basedir"]."/html/importdata/koersimport_".$importcode.".csv";
?>
<b>Importlog (importcode: <?=$importcode?>)</b><br>
<?
		if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
		{
			$csvRegels = count(file($importfile));
  		$pro_multiplier = 100 / $csvRegels;
			$prb->setLabelValue('txt1','Importeren uit CSV bestand ('.$csvRegels.' records)');
      $html="Koersimport<br>";
      $html.="<table>";

			$row = 1;
			$handle = fopen($importfile, "r");

			$DB = new DB();
      $statistieken=array();
      $gemuteerdeRecords=array();
      
      $xls = new AE_xls();
      $header=array('Fonds','FondsImportCode','datum Import','Koers Import',
      'voorLaatste datum','voorlaatste koers','meestRecente datum','meestRecente koers',
      'koersmethodiek','KoersVBH','identifierVWD','identifierFactSet');
      $xlsData=array($header);
      
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
			{
				//echo print_r($data);
  			$pro_step += $pro_multiplier;
    		$prb->moveStep($pro_step);

				$skip = false;
	   		$num = count($data);
  			$tel++;
  			$extrawhere = "";
  			$extraDag = 0;
 				$data[0] = trim($data[0]); // importcode
 				$data[1] = trim($data[1]); // omschr.
 				$data[2] = trim(str_replace(",",".",$data[2])); // koers.
 				$data[3] = trim($data[3]); // datum.

				if(substr($data[0],0,3) == "EUR") 
        {
					$table1 = "Valutas";
					$table1_importcode = "ValutaImportCode";
					$table1_data = "Valuta";

					$table2 = "Valutakoersen";
					$table2_data = "Valuta";
					$table2_koers = "Koers";
					$table2_datum = "Datum";
          continue;
				}
				else
				{
					$table1 = "Fondsen";
					$table1_importcode = "FondsImportCode";
					$table1_data = "Fonds";

					$table2 = "Fondskoersen";
					$table2_data  = "Fonds";
					$table2_koers  = "Koers";
					$table2_datum = "Datum";
				}

        //if($start ==1 && $data[0] != "" && !empty($data[2]) && $data[2] <> 0)
   			if($start ==1 && $data[0] != "" && !empty($data[2]) && $data[2] <> 0 && $data[3] <> "")
   			{
           $query="SELECT Fonds,koersmethodiek,KoersVBH,identifierVWD,identifierFactSet FROM Fondsen WHERE FondsImportCode='".$data[0]."'";
           $DB->SQL($query);
           $fondsdata=$DB->lookupRecord();
           
           $query="SELECT Datum,Koers FROM Fondskoersen WHERE Fonds='".mysql_real_escape_string($fondsdata['Fonds'])."' AND Datum<='".date("Y-m-d",form2jul($data[3]))."' order by Datum desc limit 1";
           $DB->SQL($query);
           $importKoers=$DB->lookupRecord();
               
           $query="SELECT Datum,Koers FROM Fondskoersen WHERE Fonds='".mysql_real_escape_string($fondsdata['Fonds'])."' order by Datum desc limit 1";
           $DB->SQL($query);
           $laatsteKoers=$DB->lookupRecord();
           
           if($fondsdata['Fonds']<>'')
             echo "Fonds:".$fondsdata['Fonds']."<br>\n";
           else
             echo "<b>Geen fonds voor ".$data[0]." gevonden.</b><br>\n";  

           $row=array($fondsdata['Fonds'],$data[0],$data[3],$data[2],
                date('d-m-Y',db2jul($importKoers['Datum'])),$importKoers['Koers'],
                date('d-m-Y',db2jul($laatsteKoers['Datum'])),$laatsteKoers['Koers'],
                $fondsdata['koersmethodiek'],$fondsdata['KoersVBH'],$fondsdata['identifierVWD'],$fondsdata['identifierFactSet']);
           
           $xlsData[]=$row;
            
        }    


		   
		 	 if($data[0] == "ISIN-Intern")
   		   $start = 1;
      }
     fclose($handle);
  	$xls->setData($xlsData);
    $filename='preImport.xls';
 	  $xls->OutputXls($__appvar['tempdir'].$filename,true);
   	$prb->hide();
	echo "Klaar.<br>\n";
      ?>
    <script type="text/javascript">
function pushpdf(file,save)
{
        var width='800';
        var height='600';
        var target = '_blank';
        var location = 'pushFile.php?file=' + file;
        if(save == 1)
        {
                // opslaan als bestand
                document.location = location + '&action=attachment';
        }
        else
        {
                // pushen naar PDF reader
                var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
                doc.document.location = location;
        }
}
pushpdf('<?=$filename?>',1);
</script>
  <?
  
  	}
		else 
    {
			$_error = "Fout: upload error.";
		}
	 
  
  }
	echo $_error;
	exit;


?>