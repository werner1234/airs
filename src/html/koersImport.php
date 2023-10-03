<?php
/*
    AE-ICT sourcemodule created 11 sep. 2020
    Author              : Chris van Santen
    Filename            : koersImport.php

*/

include_once("wwwvars.php");
include_once('../classes/AE_cls_progressbar.php');

if($__appvar['bedrijf']=='RCN')
{
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
    if( $_FILES['importfile']['type'] != "text/comma-separated-values" &&
        $_FILES['importfile']['type'] != "text/x-csv" &&
        $_FILES['importfile']['type'] != "text/csv" &&
        $_FILES['importfile']['type'] != 'application/vnd.ms-excel' &&
        $_FILES['importfile']['type'] != "application/octet-stream" &&
        $_FILES['importfile']['type'] != "text/plain" )
    {
      $_error = vt("Fout").": ".vt("verkeerd bestandstype, alleen .csv bestanden zijn toegestaan. Huidige formaat").":'".$_FILES['importfile']['type']."'";
    }
    // check error
    if($_FILES['importfile']['error'] != 0)
    {
      $_error = vt("Fout").": ".$_FILES['error'];
    }

    if(empty($datum))
    {
      $_error = vt("Fout").": ".vt("geen datum opgegeven")."!";
    }
    else {
      $dd = explode($__appvar["date_seperator"],$datum);
      if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
      {
        $_error = vt("Fout").": ".vt("ongeldige datum opgegeven")."";
      }
    }

    if (empty($_error))
    {
      $prb = new ProgressBar();	// create new ProgressBar
      $prb->pedding = 2;	// Bar Pedding
      $prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
      $prb->setFrame();          	                // set ProgressBar Frame
      $prb->frame['left'] = 50;	                  // Frame position from left
      $prb->frame['top'] = 	80;	                  // Frame position from top
      $prb->addLabel('text','txt1','moment ...');	// add Text as Label 'txt1' and value 'Please wait'
      $prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'
      $prb->show();	                              // show the ProgressBar

      $prb->moveStep(0);
      $prb->setLabelValue('txt1',vt('Verwerken tijdelijke tabel'));
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
        $prb->setLabelValue('txt1',vt('Importeren uit CSV bestand').' ('.$csvRegels.' '.vt('records').')');

        $row = 1;
        $handle = fopen($importfile, "r");

        $DB = new DB();

        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
        {
          //echo print_r($data);


          $pro_step += $pro_multiplier;
          $prb->moveStep($pro_step);

          $skip = false;
          $num = count($data);
          $tel++;
          $extrawhere = "";
          $data[0] = trim($data[0]); // importcode
          $data[1] = trim($data[1]); // omschr.
          $data[2] = trim(str_replace(",",".",$data[2])); // koers.
          if(substr($data[0],0,3) == "EUR") {
            $table1 = "Valutas";
            $table1_importcode = "ValutaImportCode";
            $table1_data = "Valuta";

            $table2 = "Valutakoersen";
            $table2_data = "Valuta";
            $table2_koers = "Koers";
            $table2_datum = "Datum";
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

          if($start ==1 && $data[0] != "" && !empty($data[2]) && $data[2] <> 0)
          {
            //query if result, update ID.
            $query = "SELECT ".$table1_data." FROM ".$table1." WHERE ".$table1_importcode." = '".$data[0]."'";
            $DB->SQL($query);
            $DB->Query();

            if($DB->Records() > 0)
            {
              $fondsdata = $DB->NextRecord();
              $date = jul2sql(form2jul($datum),true);
              //NL0000113637;11% ING RC Fortis 05;102,9

              $query = "SELECT id FROM ".$table2." WHERE ".$table2_data." = '".$fondsdata[$table1_data]."' AND ".$table2_datum." = '".$date."'";
              $DB2 = new DB();
              $DB2->SQL($query);
              $DB2->Query();
              if($DB2->Records() > 0)
              {
                if($_POST['overschrijven'] == 1)
                {
                  $query = "UPDATE ".$table2." SET ";
                  $extrawhere = " ".$table2_data." = '".$fondsdata['Fonds']."' AND ".$table2_datum." = '".$date."'";
                  if($log_all)
                  {
                    echo "<br>koers " . $fondsdata[$table1_data] . " ( " . $data[2] . " ) op " . $datum . " bestaat al (wordt overschreven)";
                  }
                }
                else
                {
                  $skip = true;
                  if($log_all)
                    echo "<br>koers ".$fondsdata[$table1_data]." op ".$datum." bestaat al (wordt NIET overschreven)";
                }
              }
              else
              {
                if($log_all)
                  echo "<br>koers ".$fondsdata[$table1_data]." ( ".$data[2]." ) op ".$datum." geimporteerd";
                $query = "INSERT INTO ".$table2." SET ";
              }

              if (!$skip)
              {

                $query .= " $table2_data = '".$fondsdata[$table1_data]."', ";
                $query .= " $table2_datum = '".$date."', ";
                $query .= " $table2_koers = '".$data[2]."', ";

                $query .= " import = '".$importcode."', ";
                $query .= " add_date = NOW(), ";
                $query .= " add_user = '".$USR."', ";
                $query .= " change_date = NOW(), ";
                $query .= " change_user = '".$USR."' ";

                if($extrawhere)
                  $query .= " WHERE ".$extrawhere;

                $DB3 = new DB();
                $DB3->SQL($query);
                $DB3->Query();
              }
            }
            else
            {
              $onbekendekoers[] = $tel.". Onbekend ".$table1_data." ".$data[0]." ".$data[1]." ".$data[2];
            }
          }
          if($data[0] == "ISIN-Intern")
            $start = 1;
        }
        fclose($handle);

        if($log_error)
        {
          for ($a=0; $a < count($onbekendekoers); $a++)
          {
            echo "<br>".$onbekendekoers[$a];
          }
        }
        $prb->hide();
      }
      else {
        $_error = vt("Fout").": ".vt("upload fout");
      }
    }

    echo $_error;
    exit;
  }

  if(!$_FILES['importfile']['name'])
  {
  // get laatste valutaDatum
  $laatsteValuta = getLaatsteValutadatum();

  ?>

  <form enctype="multipart/form-data" action="koersImport.php" method="POST" target="importFrame">
  <!-- MAX_FILE_SIZE must precede the file input field -->
  <input type="hidden" name="posted" value="true" />
  <!-- Name of input element determines name in $_FILES array -->
  <b><?=vt("Koersimport")?></b><br><br>
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

  <div class="form">
  <div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
  <input type="checkbox" name="usedate" value="1" checked> <?=vt("Datum opgeven")?>
  <input type="text" name="datum" value="<?=date("d-m-Y",db2jul($laatsteValuta)+86400)?>" size="15">
  </div>
  </div>

  <div class="form">
  <div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
  <input type="checkbox" name="overschrijven" value="1" checked> <?=vt("Aanwezige koersen overschrijven")?>
  </div>
  </div>

  <div class="form">
  <div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
  <input type="checkbox" name="log_error" value="1" checked> <?=vt("Log fouten")?>
  </div>
  </div>

  <div class="form">
  <div class="formblock">
  <div class="formlinks"> &nbsp;</div>
  <div class="formrechts">
  <input type="checkbox" name="log_all" value="1"> <?=vt("Log alles")?>
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
}
else
{

  include_once("../classes/benchmarkverdelingBerekening.php");
  include_once("../classes/benchmarkverdelingBerekeningV2.php");

  session_start();
  $_SESSION["NAV"] = "";
  session_write_close();

  $content = array();
  $cfg=new AE_config();
  $fondskoersLockDatum=$cfg->getData('fondskoersLockDatum');

  $content['style'].=$editcontent['style'];
  $content['jsincludes'].=$editcontent['jsincludes'];
  
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
	if( $_FILES['importfile']['type'] != "text/comma-separated-values" &&
	    $_FILES['importfile']['type'] != "text/x-csv" &&
	    $_FILES['importfile']['type'] != "text/csv" &&
		  $_FILES['importfile']['type'] != 'application/vnd.ms-excel' &&
	    $_FILES['importfile']['type'] != "application/octet-stream" &&
	    $_FILES['importfile']['type'] != "text/plain" )
	{
		$_error = vt("Fout").": ".vt("verkeerd bestandstype, alleen .csv bestanden zijn toegestaan. Huidige formaat").":'".$_FILES['importfile']['type']."'";
	}
	// check error
	if($_FILES['importfile']['error'] != 0)
	{
		$_error = vt("Fout").": ".$_FILES['error'];
	}
  
  if($_POST['preImport']==1)
  {
    include('preImport.php');
    exit;
  }
  if($_POST['optiestatistieken']==1)
  {
    include('importOptiestatistieken.php');
    exit;
  }
  

/*
	if(empty($datum))
	{
		$_error = "Fout: geen datum opgegeven!";
	}
	else {
		$dd = explode($__appvar["date_seperator"],$datum);
		if(!checkdate(intval($dd[1]),intval($dd[0]),intval($dd[2])))
		{
			$_error = "Fout: ongeldige datum opgegeven";
		}
	}
*/
	if (empty($_error))
	{
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

		$importcode = date("YmdHi");  //datum als JJJJMMDDUUMM
		$importfile = $__appvar["basedir"]."/html/importdata/koersimport_".$importcode.".csv";
?>
    <b>Importlog (importcode: <?=$importcode?>)</b><br>
<?
		if(move_uploaded_file($_FILES['importfile']['tmp_name'],$importfile))
		{
			$csvRegels = count(file($importfile));
  		$pro_multiplier = 100 / $csvRegels;
			$prb->setLabelValue('txt1',vt("Importeren uit CSV bestand")." (".$csvRegels." ".vt("records").")");
      $html = vt("Koersimport")."<br>";
      $html.="<table>";

			$row = 1;
			$handle = fopen($importfile, "r");

			$DB = new DB();
      $statistieken=array();
      $gemuteerdeRecords=array();
      
      $query="SELECT Valuta FROM ActieveFondsen WHERE InPositie>0 GROUP BY Valuta";
 			$DB->SQL($query);
    	$DB->Query();
      while($valutadata = $DB->NextRecord())
      {
        $actieveValuta[]=$valutadata['Valuta'];
      }
      
      
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
        $label='geenLabel';
       
// 
        //if($start ==1 && $data[0] != "" && !empty($data[2]) && $data[2] <> 0)
   			if($start ==1 && $data[0] != "" && !empty($data[2]) && $data[2] <> 0 && $data[3] <> "")
   			{
   			  $statistieken[$table2]['totaal']++;
   			  //listarray($data);exit;
 					//query if result, update ID.
 					$query = "SELECT ".$table1_data." FROM ".$table1." WHERE ".$table1_importcode." = '".$data[0]."'";
    			$DB->SQL($query);
    			$DB->Query();
    			$query='';
          
   
 					if($DB->Records() > 0)
 					{
 						$fondsdata = $DB->NextRecord();
            $dateJul=form2jul($data[3]);
 						$date = jul2sql($dateJul,true);
 						$datum=$data[3];
            $oorspKrsDt=$date;
            
            $DB2 = new DB();
            $query = "SELECT max(oorspKrsDt) as laatsteOorspKrsDt FROM ".$table2." WHERE ".$table2_data." = '".$fondsdata[$table1_data]."'";
						$DB2->SQL($query);
            $tmp=$DB2->lookupRecord();
            $laatsteOorspKrsDt=$tmp['laatsteOorspKrsDt'];
            $laatsteOorspKrsDtJul=db2jul($laatsteOorspKrsDt);
 						//echo $fondsdata[$table1_data]." $date <br>\n";
            
            if($table1_data == "Fonds")
              $geimporteerdeFondsen[]=$fondsdata['Fonds'];
 						//$date = jul2sql(form2jul($datum),true);
						//NL0000113637;11% ING RC Fortis 05;102,9

	 					$query = "SELECT id, Koers, oorspKrsDt FROM ".$table2." WHERE ".$table2_data." = '".$fondsdata[$table1_data]."' AND ".$table2_datum." = '".$date."'";
  					$DB2->SQL($query);
  					$DB2->Query();
  					$query='';
						if($DB2->Records() > 0)
						{
              $koersData=$DB2->nextRecord();
					    //$oorspKrsDt=$koersData['oorspKrsDt'];
              //$oorspKrsDtJul=db2jul($oorspKrsDt);
						  //$statistieken[$table2]['aanwezig']++; 
		 					if($_POST['overschrijven'] == 1)
		 					{
								$query = "UPDATE ".$table2." SET ";
								$extrawhere = " ".$table2_data." = '".$fondsdata[$table1_data]."' AND ".$table2_datum." = '".$date."'";
                //$oorspKrsDt=$date;
								if($log_all)
                {
                  echo "<br>koers " . $fondsdata[$table1_data] . " ( " . $data[2] . " ) op " . $datum . " bestaat al (wordt overschreven)";
                }
                $label='NieuweKoersOudeDatum'; 
                //$statistieken[$table2]['overschreven']++;
							}
							else
							{
								$skip = true;
								if($log_all)
                {
                  echo "<br>koers " . $fondsdata[$table1_data] . " op " . $datum . " bestaat al (wordt NIET overschreven)";
                }
                 
								if($_POST['nieuweKoersenToevoegen'] == 1)
								{
								  if(db2jul($date) <= db2jul($fondskoersLockDatum))
								  {
                      if($dateJul<$laatsteOorspKrsDtJul)
                      {
                        $skip=true;
                        $label='OuderDanMeestRecente'; 
                      }
								      elseif(round($koersData['Koers'],4) != round($data[2],4))
								      {
								        $oldDate=$date;
								        $date= date("Y-m-d",db2jul($fondskoersLockDatum)+86400);
								        $query="SELECT id, Koers,oorspKrsDt FROM ".$table2." WHERE ".$table2_data." = '".$fondsdata[$table1_data]."' AND ".$table2_datum." = '".$date."'";
										  	$DB2->SQL($query);
  					            $DB2->Query();
                        $skip=false;
						            if($DB2->Records() > 0)
						            {
						              $tmp=$DB2->nextRecord();
                          //$oorspKrsDt=$tmp['oorspKrsDt'];
                          ////$oorspKrsDtJul=db2jul($oorspKrsDt);
                          if($dateJul<$laatsteOorspKrsDtJul)
                          {
                             $skip=true;
                             $label='OuderDanMeestRecente'; 
                          }
                          elseif(round($tmp['Koers'],4)==round($data[2],4))
                          {
                            $skip=true;
                            //$statistieken[$table2]['gemuteerdeRecordsOverslaan']++;
                            $label='GelijkeKoersVoorVastzettenOverslaan'; 
                          }
                          else
                          {
                           // $statistieken[$table2]['gemuteerdeRecordsUpdate']++;
                           $label='NieuweKoersVoorVastzettenUpdate'; 
//                         $logging[$table2][$label]="Nieuwe koers voor ".$fondsdata[$table1_data]." toegevoegd op ".date("d-m-Y",db2jul($date)+$extraDag)." ".substr($query,0,7);
                          }
                          $oorspKrsDt=$oldDate;
						              $query = "UPDATE ".$table2." SET ";
						              $extrawhere = " ".$table2_data." = '".$fondsdata['Fonds']."' AND ".$table2_datum." = '".$date."'";
						            }
						            else
                        {
                          $oorspKrsDt=$oldDate;
                          $label='NieuweKoersVoorVastzetten'; 
											    $query = "INSERT INTO ".$table2." SET ";

                        //$statistieken[$table2]['gemuteerdeRecordsInsert']++;
						            }
                        if(!$skip)
                        {
                          //$statistieken[$table2]['gemuteerdeRecords']++;
                          $gemuteerdeRecords[]="Nieuwe koers voor ".$fondsdata[$table1_data]." toegevoegd op ".date("d-m-Y",db2jul($date)+$extraDag)." ".substr($query,0,7);
						            }
                        if($log_all)
                        {
                          echo "<br>Nieuwe koers voor " . $fondsdata[$table1_data] . " toegevoegd op " . date("d-m-Y", db2jul($date) + $extraDag) . " " . substr($query, 0, 7);
                        }
						          }
                      else
                      {
                        $label='GelijkeKoersOverslaan'; 
                        //$statistieken[$table2]['gemuteerdeRecordsOverslaan']++;
                      }
                    
								  }
                  else
                    $label='aanwezigOvergeslagen'; 
								}
							}
		 				}
		 				else
		 				{
		 				  //$statistieken[$table2]['nieuw']++;
              //echo 	"Niets gevonden met => SELECT id, Koers FROM ".$table2." WHERE ".$table2_data." = '".$fondsdata[$table1_data]."' AND ".$table2_datum." = '".$date."' <br>\n";
		 				  if($_POST['nieuweKoersenToevoegen'] == 1)
							{ 
							 	//if(db2jul($date)<$oorspKrsDtJul)
                //{
                //   $skip=true;   
                //}
							  //else
                if(db2jul($date) <= db2jul($fondskoersLockDatum))
							  {
							    $date= date("Y-m-d",db2jul($fondskoersLockDatum)+86400);
							    $query="SELECT id, Koers,oorspKrsDt FROM ".$table2." WHERE ".$table2_data." = '".$fondsdata[$table1_data]."' AND ".$table2_datum." = '".$date."'";
								  $DB2->SQL($query);
  					      $DB2->Query();
  					      $query='';
                  $skip=false;
                  
                  if($dateJul<$laatsteOorspKrsDtJul)
                  {
                    $skip=true;
                    $label='OuderDanMeestRecente'; 
                  }
                  elseif($DB2->Records() > 0)
						      {
						        $tmp=$DB2->nextRecord();
                    //$oorspKrsDt=$tmp['oorspKrsDt'];
                    //$oorspKrsDtJul=db2jul($oorspKrsDt);
                    if(round($tmp['Koers'],4)==round($data[2],4))
                    {
                      $skip=true;
                     // $statistieken[$table2]['gemuteerdeRecordsOverslaan']++;
                      $label='GelijkeKoersVoorVastzettenOverslaan'; 
                    }
                    else
                    {
                      //$statistieken[$table2]['gemuteerdeRecordsUpdate']++;
                      $label='NieuweKoersVoorVastzettenUpdate'; 
//                      $logging[$table2][$label]="Nieuwe koers voor ".$fondsdata[$table1_data]." toegevoegd op ".date("d-m-Y",db2jul($date)+$extraDag)." ".substr($query,0,7);

                    } 
                    
						        $query = "UPDATE ".$table2." SET ";
						        $extrawhere = " ".$table2_data." = '".$fondsdata['Fonds']."' AND ".$table2_datum." = '".$date."'";
						      }
						      else
                  {
                    $label='NieuweKoersVoorVastzetten'; 
						        $query = "INSERT INTO ".$table2." SET ";
                   // $statistieken[$table2]['gemuteerdeRecordsInsert']++;
                  }
                  if(!$skip)
                  {
                    $gemuteerdeRecords[]="Nieuwe koers voor ".$fondsdata[$table1_data]." toegevoegd op ".date("d-m-Y",db2jul($date)+$extraDag)." ".substr($query,0,7);
						      //  $statistieken[$table2]['gemuteerdeRecords']++;
                  }
                  if($log_all)
							      echo "<br>Nieuwe koers voor ".$fondsdata[$table1_data]." toegevoegd op ".date("d-m-Y",db2jul($date)+$extraDag)." ".substr($query,0,7);
						     }
						     else
                 {
                   $label='NieuweKoers'; //na lockdatum
						       $query = "INSERT INTO ".$table2." SET ";
                 }
							}
							else
							{
							  $label='NieuweKoers';
		 					  if($log_all)
                {
                  echo "<br>koers " . $fondsdata[$table1_data] . " ( " . $data[2] . " ) op " . $datum . " geimporteerd";
                }
		 					  $query = "INSERT INTO ".$table2." SET ";
							}
		 				}

		 				if (!$skip)
		 				{
		 				 
              if(substr($query,0,6)=='INSERT')
                $type="INSERT";
              else
                $type="UPDATE";
                
			 				$query .= " $table2_data = '".$fondsdata[$table1_data]."', ";
			 				$query .= " $table2_datum = '".date("Y-m-d",db2jul($date)+$extraDag)."', ";
			 				$query .= " $table2_koers = '".$data[2]."', ";
              $query .= " oorspKrsDt = '".$oorspKrsDt."', ";
			 				$query .= " import = '1', ";
              if($type=='INSERT')
              {
			 				  $query .= " add_date = NOW(), ";
			 				  $query .= " add_user = '".$USR."', ";
			 				}
              $query .= " change_date = NOW(), ";
			 				$query .= " change_user = '".$USR."' ";
              
              if($extrawhere)
			 					$query .= " WHERE ".$extrawhere;
              

              
              $logging[$table2][$label][]="$type ;".$fondsdata[$table1_data].";".date("Y-m-d",db2jul($date)+$extraDag).";".$data[2];
              
							$DB3 = new DB();
							$DB3->SQL($query);
							//echo $query."<br>\n";
							$DB3->Query();
              $statistieken[$table2]['QueryUitgevoerd']++;
		 				}
            else
            {
              if($label=='geenLabel')
              {
                $label = 'overgeslagen';
              }
              $type='SKIP';
              $logging[$table2][$label][]="$type ;".$fondsdata[$table1_data].";".date("Y-m-d",db2jul($date)+$extraDag).";".$data[2];  
            }  //$statistieken[$table2]['QueryOvergeslagen']++;
              
            $statistieken[$table2][$label]++;
            $labelPerRegel[$fondsdata[$table1_data]]=$label;
 					}
 					else
 					{
 					  $type='SKIP';
            $label='ImportcodeOnbekend';
 						$onbekendekoers[] = $tel.". Onbekend ".$table1_data." ".$data[0]." ".$data[1]." ".$data[2];
            $logging[$table2][$label][]="$type ;".$data[0].";".$data[1].";".$data[2];  

            $statistieken[$table2][$label]++;
            $labelPerRegel[$data[1]]=$label;
 					}
          
	   		}
   			if($data[0] == "ISIN-Intern")
        {
          $start = 1;
        }
			}
			fclose($handle);

			if($log_error)
			{
				for ($a=0; $a < count($onbekendekoers); $a++)
				{
					echo "<br>".$onbekendekoers[$a];
				}
			}
			$prb->hide();
		}
		else {
			$_error = "Fout: upload error";
		}
	}
//listarray($statistieken);exit;
  //$cfg=new AE_config();
  //$vanaf=$cfg->getData('fondskoersLockDatum');
  
  if(date('j')<3)
    $extraMaand=-1;
  else
    $extraMaand=0;

  $vanaf=date('Y-m-d',mktime(0,0,0,date('m')+$extraMaand,0,date('Y')));

	//euribor toevoegen
	$berekening=new benchmarkverdelingBerekening();
	$berekening->calulateEuribor();

	$berekening=new benchmarkverdelingBerekening();
  $benchmarks=$berekening->getBenchmarks();
  foreach ($benchmarks as $benchmark)
    $berekening->bereken($benchmark,$vanaf);
  $berekening->updateKoersen();
	echo $berekening->toonOngecontroleerd();
 
  $berekening=new benchmarkverdelingBerekening();
  $benchmarks=$berekening->getBenchmarks();
  foreach ($benchmarks as $benchmark)
    $berekening->bereken($benchmark,$vanaf);
  $berekening->updateKoersen();
	echo $berekening->toonOngecontroleerd();
 
	// benchmarkverdelingBerekeningV2
  $berekening = new benchmarkverdelingBerekeningV2();
  $benchmarks=$berekening->getBenchmarks();
  foreach ($benchmarks as $benchmark)
    $berekening->bereken($benchmark,$vanaf);
  $berekening->updateKoersen();
  echo  $berekening->toonOngecontroleerd();
  
  $berekening = new benchmarkverdelingBerekeningV2();
  $benchmarks=$berekening->getBenchmarks();
  foreach ($benchmarks as $benchmark)
    $berekening->bereken($benchmark,$vanaf);
  $berekening->updateKoersen();
  echo  $berekening->toonOngecontroleerd();
  //
  
  $_POST['posted']=true;
  $_POST['ouderdom']=1;
  $_POST['noExit']=true;
  //$tmp = getLaatsteValutadatum();
  //$datum=substr($tmp,8,2)."-".substr($tmp,5,2)."-".substr($tmp,0,4);
  $datum=date('d-m-Y');
  include('koersControle.php');
  //listarray($logging);
  

  foreach($statistieken as $tabel=>$data)
    foreach($data as $var=>$aantal)
       $html.="<tr><td>$tabel $var </td><td>$aantal</td></tr>";
  $html.="</table>";  
  
  
  //$veldenOverslaan=array('aanwezigOvergeslagen','GelijkeKoersVoorVastzettenOverslaan','GelijkeKoersOverslaan','NieuweKoers');
  $txtLog='';
  $htmlLog='';
  foreach($logging as $tabel=>$tabelData)
  {
    foreach($tabelData as $veld=>$regels)
    {
      //if(!in_array($veld,$veldenOverslaan))
      //{
        $htmlLog.="<br\><b>$tabel $veld:</b><br>\n";
        $txtLog.="$tabel $veld:\n";
        foreach($regels as $regel)
        {
          $htmlLog.="$regel<br>\n";
          $txtLog.="$regel\n";
        }
      //}
    }
  }
  
  

  $htmlLog.="<br\><b>Onbekende import details:</b><br>\n";
  $txtLog.="\nOnbekende import details:\n";
  foreach($onbekendekoers as $regel)   
  {
    $htmlLog.="$regel<br>\n"; 
    $txtLog.="$regel\n"; 
  }
  $htmlLog.="<br\><b>Gemuteerde records:</b><br>\n";
  $txtLog.="\nGemuteerde records:\n"; 
  foreach($gemuteerdeRecords as $regel)
  {
    $htmlLog.="$regel<br>\n"; 
    $txtLog.="$regel\n"; 
  }
  
  $cfg=new AE_config();
  $mailserver=$cfg->getData('smtpServer');
  $fondsEmail=$cfg->getData('fondsEmail');
  //$fondsEmail='rvv@aeict.nl';
  if($fondsEmail !="" && $mailserver !='')
  {
    include_once('../classes/AE_cls_phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = $fondsEmail;
    $mail->FromName = "Airs";
    $mail->Body    = $html;
    $mail->AltBody = html_entity_decode(strip_tags($html));
    $mail->AddAddress($fondsEmail,$fondsEmail);
    $mail->Subject = "Koersimport ".date('d-m-Y H:i:s');
    $mail->Host=$mailserver;
    if($htmlLog<>'')
      $mail->AddStringAttachment($htmlLog,'htmlLog.html');
    if($txtLog<>'')
      $mail->AddStringAttachment($txtLog,'txtLog.txt');      
    if(is_file($__appvar['tempdir'].'ouderdom.xls'))
      $mail->AddAttachment($__appvar['tempdir'].'ouderdom.xls','ouderdom'.date("_Ymd_Hi").'.xls');
    else
      echo "Bestand ouderdom.xls niet gevonden";
    if(!$mail->Send())
      echo "Verzenden van e-mail mislukt";
    else
      echo "Email verzonden";
  }

	echo $_error;
	exit;
}

if(!$_FILES['importfile']['name'])
{
// get laatste valutaDatum
$laatsteValuta = getLaatsteValutadatum();

?>

<form enctype="multipart/form-data" name="selectForm" action="koersImport.php" method="POST" target="importFrame">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="posted" value="true" />
<!-- Name of input element determines name in $_FILES array -->
<b><?=vt("Koersimport")?></b><br><br>
<?php
if($_error) echo "<b style=\"color:red;\">".$_error."</b>";
?>
<div class="form">
  <div class="formblock">
    <div class="formlinks"><?=vt("Koersimportbestand")?> </div>
    <div class="formrechts">
      <input type="hidden" name="preImport" value="0" />
      <input type="hidden" name="optiestatistieken" value="0" />
      <input type="file" name="importfile" size="50">
    </div>
  </div>

<!--
<div class="form">
<div class="formblock">
<div class="formlinks"> &nbsp;</div>
<div class="formrechts">
<input type="checkbox" name="usedate" value="1" checked> Datum opgeven
<input type="text" name="datum" value="<?=date("d-m-Y",db2jul($laatsteValuta)+86400)?>" size="15">
</div>
</div>
-->


  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="checkbox" name="overschrijven" value="1" > <?=vt("Aanwezige koersen overschrijven")?>
    </div>
  </div>



  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="checkbox" name="nieuweKoersenToevoegen" value="1" checked> <?=vt("Nieuwe koersen tot en met")?> <?=dbdate2form($fondskoersLockDatum)?> <?=vt("datum op de volgende dag wegschrijven")?>.
    </div>
  </div>


  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="checkbox" name="log_error" value="1" checked> <?=vt("Log fouten")?>
    </div>
  </div>


  <div class="formblock">
    <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="checkbox" name="log_all" value="1"> <?=vt("Log alles")?>
    </div>
  </div>

  <div class="formblock">
  <div class="formlinks"> &nbsp;</div>
    <div class="formrechts">
      <input type="submit" value="<?=vt("importeren")?>">
      <input type="button" onclick="document.selectForm.preImport.value=1;document.selectForm.submit();document.selectForm.preImport.value=0;" value="<?=vt("Pre-import")?>">
  <?
  if($__appvar["bedrijf"] =='HOME')
  {
    echo '<script>
  
  </script>
  ';
    echo '<input type="button" onclick="$(\'#optiestatistiekenBox\').toggle();" value="'.vt("optiestatistieken").'"> <br>
      <div id="optiestatistiekenBox" style="display: none">
      
        <fieldset>
          <div class="form">
          <div class="formblock">
            <div class="formlinks"><input type="text" name="optiestatistiekenDatum" value="'.date("d-m-Y",time()-86400).'" size="15"></div>
            <div class="formrechts">
              <input  type="button" 
                      onclick="document.selectForm.optiestatistieken.value=1;document.selectForm.submit();document.selectForm.optiestatistieken.value=0;" 
                      value="'.vt("Import optiestatistieken").'">
            </div>
          </div>
        
        </fieldset>
    
    </div>
';
}
?>
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
}
