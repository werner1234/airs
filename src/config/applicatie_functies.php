<?php
/*
 		Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/07/08 14:56:50 $
 		File Versie					: $Revision: 1.389 $

 		$Log: applicatie_functies.php,v $
*/

//
//  in onderstaande include file staan de functie die nodig zijn om een de API interface te kunnen draaien
//
include_once "applicatie_functies_minimal.php";

/**
 * Autoload files
 *
 * @param class $class_name name of the class
 * @return type
 */
function __autoload($class_name)
{
  /** check if a class exists exit if it does else continue **/
  if (class_exists($class_name)) {
    return;
  }
  $baseDir = realpath(dirname(__FILE__)."/..");

  /** load default classes whith non standard class names **/
  if ( strtolower ($class_name) == 'table' ) {
    require_once($baseDir . '/classes/mysqlTable.php');
    return;
  }
  if ( isset($_GET['autoloadDebug']) ) {
    echo '<pre>'; print_r($class_name); echo '</pre>';
  }

  $directories = array(
    '/classes/',
    '/classes/records/',
  );

  $class_name_lower = $class_name;
  $class_name_lower[0] = strtolower($class_name_lower[0]); //sets first letter to lowercase

  $class_name_upper = $class_name;
  $class_name_lower[0] = strtoupper($class_name_upper[0]); //sets first letter to lowercase

  $pobibleFiles = array(
    $class_name_lower,
    $class_name,
    $class_name .'s',
    $class_name_lower,
    'CRM_' . $class_name_lower,
    'AE_cls_' . $class_name,
    'AE_cls_' . $class_name_lower,
    'AIRS_cls_' . $class_name,
    'AIRS_cls_' . $class_name_lower
  );

  /** fast one word class include like AE_cls_Class **/
  if ( substr($class_name, 0, 3) == 'AE_' || substr($class_name, 0, 3) == 'ae_') {
    $classdata = explode('_', $class_name);
    $pobibleFiles[] = 'AE_cls_' . $classdata[1];

    $class_name_upper = $classdata[1];
    $class_name_upper[0] = strtoupper($class_name_upper[0]); //sets first letter to lowercase

    $pobibleFiles[] = 'AE_cls_' . $class_name_upper;
  }

  /** fast one word class include like AIRS_cls_Class **/
  if ( substr($class_name, 0, 5) == 'AIRS_' || substr($class_name, 0, 5) == 'airs_') {
    $classdata = explode('_', $class_name);
    $pobibleFiles[] = 'AIRS_cls_' . $classdata[1];

    $class_name_upper = $classdata[1];
    $class_name_upper[0] = strtoupper($class_name_upper[0]); //sets first letter to lowercase

    $pobibleFiles[] = 'AIRS_cls_' . $class_name_upper;
  }

  /** loop directories and files if exist include them **/
  foreach($directories as $directory)
  {
    foreach ( $pobibleFiles as $file )
    {
      if ( isset($_GET['autoloadDebug']) )
      {
        echo '<pre>'; print_r($baseDir . $directory . $file . '.php'); echo '</pre>';
      }
      if(file_exists($baseDir . $directory.$file . '.php'))
      {
        if ( isset($_GET['autoloadDebug']) )
        {
          echo '<pre>Found:'; print_r($baseDir . $directory.$file . '.php'); echo '</pre>';
        }
        require_once($baseDir . $directory.$file . '.php');
        return;
      }
    }
  }

  /** backup for camelcase encrypted files **/
  foreach($directories as $directory)
  {
    /** load complete directory to session to increase spead **/
    if ( ! isset($_SESSION[$directory]) ) {
      $allFiles = array_map('basename', glob($baseDir . $directory.'*.php'));
      $_SESSION[$directory] = array_combine(array_map('strtolower', $allFiles), $allFiles);;
    }

    /** loop posible file names if exist in array include them **/
    foreach ( $pobibleFiles as $file )
    {
      if (array_key_exists(strtolower($file). '.php', $_SESSION[$directory])) {
        require_once($baseDir . $directory . $_SESSION[$directory][strtolower($file). '.php']);
        return;
      }
    }
  }

}

function loadDatabaseObjects($path)
{
//  $tmpObjecten = $_SESSION['tmpObjecten'];
//  if (!isset($tmpObjecten) || empty($tmpObjecten))
//  {
    $firstClasses = get_declared_classes();
    if ($handle = opendir($path))
    {
      while (false !== ($file = readdir($handle)))
      {
        if ($file != "." && $file != "..")
        {
          if (is_file($path . "/" . $file))
          {
            include_once($path . "/" . $file);
          }
        }
      }
      closedir($handle);
    }
    $endClasses = get_declared_classes();
    $tmpObjecten = array_diff($endClasses, $firstClasses);
//    $_SESSION['tmpObjecten'] = $tmpObjecten;
//  }
  global $__appvar;
  $__appvar['tabelObjecten'] = array_values($tmpObjecten);
}

function listarray($de_array)
{
	echo "<pre>\n";
	print_r($de_array);
	echo "</pre>\n";
}



function getLaatsteValutadatum()
{
	$q = "SELECT Datum FROM Valutakoersen WHERE Valuta = 'EUR' ORDER BY Datum DESC LIMIT 1;";
	$DB = new DB();
	$DB->SQL($q);
	$DB->Query();
	$data = $DB->NextRecord();

	return $data['Datum'];
}

function updateDataFromQueue($Bedrijf,$exportId,$queueData)
{
	global $prb,$_DB_resources;
  $melding='-';
	$nieuweFondsTransacties=array();
  $nieuweTransacties=array();
  $nieuweFondsen=array();
  $fondsen=array();
  
  $debug=false;
 
  if($Bedrijf=='ANO')
  {
   // $debug = true;
   // echo "Extra Debug info. <br>\n";
   // error_reporting(E_ALL);
   // ini_set('display_errors', 1);
  }

	if(!empty($prb))
	{
		$prb->setLabelValue('txt1','Start update ');
		flush();
	}

  if($queueData['type']=='correctie')
  {
    $db=new DB();
    $db2=new DB();
    //$query="ALTER TABLE importdata ADD INDEX  `tableIds` (`tableName`(1),`tableId`)";
    //$db->SQL($query);
    //$db->Query();

    $query="SELECT tableName FROM importdata WHERE tableId=0";
    $db->SQL($query);
    $db->Query();
    $tabellen=array();
    while($data=$db->nextRecord())
      $tabellen[]=$data['tableName'];

    $query="DELETE FROM importdata WHERE tableId=0";
    $db->SQL($query);
    $db->Query();

    $aantal=count($tabellen);
    if(!empty($prb))
    {
      $pro_multiplier = (100 / ($aantal*2+1));
      $prb->setLabelValue('txt1','Update data ( '.$aantal.' tabellen) ');
		  $prb->moveStep(0);
		  $pro_step = 0;
    }
    $melding.="\ncorrectie gestart";
    foreach($tabellen as $tabel)
    {
      $pro_step += $pro_multiplier;
      if(!empty($prb))
        $prb->moveStep($pro_step);
      $query="SELECT `$tabel`.id FROM `$tabel` LEFT JOIN  importdata ON importdata.tableId=`$tabel`.id AND importdata.tableName='$tabel' WHERE importdata.tableId is null";
      $db->SQL($query);
      $db->Query();
      $aantalRecords=$db->records();
      if($aantalRecords>0)
        $melding.="\nVerwijdren $tabel.id ";
      $ids=array();
      $n=0;
      while($data=$db->nextRecord())
      {
        $n++;
        $ids[]=$data['id'];
        $melding.=$data['id'].' ';
        if($n==$aantalRecords || count($ids)>10000)
        {
          $db2->SQL("DELETE FROM `$tabel` WHERE id IN(" . implode(",",$ids) . ")");
          $db2->Query();
          $ids=array();
        }
      }
    }
    foreach($tabellen as $tabel)
    {
      if(!empty($prb))
      {
      $pro_step += $pro_multiplier;
      $prb->moveStep($pro_step);
      }
      $query="SELECT importdata.tableId as id FROM importdata LEFT JOIN `$tabel` ON importdata.tableId=`$tabel`.id WHERE importdata.tableName='$tabel' AND `$tabel`.id is null";
      $db->SQL($query);
      $db->Query();
      if($db->records()>0)
        $melding.="\nMissende $tabel.id ";
      while($data=$db->nextRecord())
      {
        $melding.=$data['id'].' ';
      }
    }
    $melding.="\ncorrectie klaar.";
    $portefeuilles='geen';
    return array(true,$portefeuilles,$melding);
  }
  elseif($queueData['type']=='documenten')
  {
    if(class_exists('documentVerwerking'))
    {
      echo "documentVerwerking.";
      $verwerking=new documentVerwerking();
      $melding=$verwerking->importUpdate($Bedrijf,$exportId,$queueData);
      return array(true,'geen',$melding);
      
    }
    else
    {
      echo "Nog geen ondersteuning voor documentVerwerking.";
    }
  }
	$DB_queue = new DB();
	$query = "SELECT max(orderCheckMaxAge) as maxAge, max(check_module_portefeuilleWaarde) as check_module_portefeuilleWaarde, max(koersExport) as koersOnly FROM Vermogensbeheerders";
	$DB_queue->SQL($query);
	$DB_queue->Query();
	$maxAge=$DB_queue->nextRecord();

	if($maxAge['maxAge'] > 0)
	  $tonenVanaf=time()-($maxAge['maxAge']*86400);
	else
	  $tonenVanaf=time()-(90*86400);

  if($maxAge['koersOnly'] > 0)
    $koersOnly=true;
  else
    $koersOnly=false;

	$query = "SELECT * FROM importdata WHERE Bedrijf = '".$Bedrijf."' AND exportId = '".$exportId."'";

	$DB_queue->SQL($query);
	$DB_queue->Query();

	$aantal = $DB_queue->Records();
	if(!empty($prb))
	{
		$pro_multiplier = (100 / ($aantal+1)) *100;
		$prb->setLabelValue('txt1','Update data ( '.$aantal.' records) ');
		$prb->moveStep(0);
		$pro_step = 0;
	}
  $melding.="\nUpdate data ( '$aantal' records). $Bedrijf $exportId";

  $DB_local = new DB();
	$DB_local2 = new DB();
	$DB_queue_2 = new DB();
  $herrekeningUitvoeren=0;


  $keyLookup=array('Fondsen'=>'Fonds','Portefeuilles'=>'Portefeuille','Rekeningen'=>'Rekening','Beurzen'=>'beurs','BICcodes'=>'code','Accountmanagers'=>'Accountmanager',
                   'GeconsolideerdePortefeuilles'=>'VirtuelePortefeuille','Clienten'=>'Client','Orderredenen'=>'orderreden','toelichtingStortOnttr'=>'toelichting');
  $updateTabelen=array('Fondsen'=>array('Orders','BestandsvergoedingPerPortefeuille','ModelPortefeuilleFixed','OrdersV2','FondsExtraInformatie'),
                       'Portefeuilles'=>array('OrderRegels','CRM_naw','laatstePortefeuilleWaarde','ModelPortefeuilleFixed','OrderRegelsV2','FactuurBeheerfeeHistorie','Factuurregels','signaleringPortRend','feehistorie','signaleringStortingen','laatstePortefeuilleWaarde'),
                       'Rekeningen'=>array('OrderRegelsV2','VoorlopigeRekeningmutaties','VoorlopigeRekeningafschriften'),
                       'Beurzen'=>array('OrderRegels'),
                       'toelichtingStortOnttr'=>array('signaleringStortingen'),
                       'BICcodes'=>array('OrderRegels'),
                       'Clienten'=>array('OrderRegels','OrderRegelsV2'),
                       'Accountmanagers'=>array('Gebruikers'),
                       'Orderredenen'=>array('OrderRegelsV2'));
  $consolidatieLookup=array('Rekeningen'=>'Consolidatie');
  $checkTabellen=array_keys($updateTabelen);

  $deleteIds=array();
  $t=0;
	while($update = $DB_queue->NextRecord())
	{
	  $insert=false;
		if(!empty($prb)) {
			$t++;
			if($t > 100)
			{
				$pro_step += $pro_multiplier;
				$prb->moveStep($pro_step);
				$t=0;
			}
		}
		$data = unserialize($update['tableData']);

    $queries=array();
    $query = "";
    $geenQuery=false;
		if ($update['tableName'] != 'systeem')
		{
      if(in_array($update['tableName'],$checkTabellen))
      {
        $keyVeld=$keyLookup[$update['tableName']];
        $DB_local->SQL("SELECT id,$keyVeld FROM ".$update['tableName']." WHERE $keyVeld='".mysql_real_escape_string($data[$keyVeld])."'");
        
        if($debug)
        {
          echo  "check: SELECT id,$keyVeld  FROM ".$update['tableName']." WHERE $keyVeld='".mysql_real_escape_string($data[$keyVeld])."' <br>\n";
        }
        $fondsData=$DB_local->lookupRecord();
        if($fondsData['id'] <> $update['tableId'] && $fondsData[$keyVeld] <> '' && !isset($consolidatieLookup[$update['tableName']]) )
        {
          $melding.="$keyVeld ".$data[$keyVeld]." is al aanwezig onder id ".$fondsData['id']." bij $Bedrijf \n $keyVeld (".$fondsData[$keyVeld].").";
          if($debug)
          {
            echo "$keyVeld ".$data[$keyVeld]." is al aanwezig onder id ".$fondsData['id']." bij $Bedrijf \n $keyVeld (".$fondsData[$keyVeld].").";
          }
          $geenQuery=true;
        }
      }

		  $DB_local->SQL("SELECT id FROM ".$update['tableName']." WHERE id = '".$update['tableId']."'");
		  $DB_local->Query();
      $aantalRecords=$DB_local->Records();
		  if($aantalRecords > 0)
		  {
			  // do update
        if(in_array($update['tableName'],$checkTabellen))
		    {
		      $keyVeld=$keyLookup[$update['tableName']];
          $DB_local->SQL("SELECT id,$keyVeld FROM ".$update['tableName']." WHERE id = '".$update['tableId']."'");
          if($debug)
          {
            echo "check2: ($geenQuery) SELECT id,$keyVeld FROM ".$update['tableName']." WHERE id = '".$update['tableId']."'<br>\n";
          }
          $localData=$DB_local->lookupRecord();
          if($localData[$keyVeld] <> $data[$keyVeld] && $localData[$keyVeld] <> '')
          {
            $DB_local->SQL("SELECT * FROM ".$update['tableName']." WHERE id = '".$update['tableId']."'");
            $localData=$DB_local->lookupRecord();
            if($koersOnly==true)
            {
         	    global $__appvar;
              $USR='IMP';
 	            $updateTabel = array();
	            foreach ($__appvar['tabelObjecten'] as $tabel)
	            {
                if($debug)
                {
                  echo "check op tabel $tabel <br>\n";
                }

                if(class_exists($tabel))
                {
                  $tmpObject = new $tabel;
                  foreach($tmpObject->data['fields'] as $targetField=>$fieldData)
                  {
                    $parts=explode(',',$fieldData['keyIn']);
                    foreach($parts as $keyIn)
                    {
	                    if($keyIn == $update['tableName'])
	                    {
	                      $tmp=array('tabel'=>$tmpObject->data['table'],
	                                 'veld'=>$targetField,
	                                 'valueNew'=> $data[$keyVeld],
  	                                'valueOld'=>$localData[$keyVeld]);

                        if($fieldData['keyCondition'])
                          $tmp['extra_keys'][$fieldData['keyCondition']]=$keyIn;

  	                    $updateTabel[]=$tmp;
	                    }
                    }
	                }
                }
                else
                {
                  if($debug)
                  {
                    echo "Class $tabel niet aanwezig! <br>\n";
                    $melding.="Class $tabel niet aanwezig! <br>\n";
                  }
                }
	            }
	            foreach ($updateTabel as $updateV)
	            {
                $extraWhere='';
                if($DB_local->QRecords("show tables like '".$updateV['tabel']."'"))
                {
	                foreach($updateV['extra_keys'] as $key=>$value)
                    $extraWhere="AND `$key` = '$value' ";
 	                $queries[] = "UPDATE ".$updateV['tabel']." SET `".$updateV['veld']."` = '".mysql_real_escape_string($updateV['valueNew'])."',`change_date` = NOW(), `change_user` = '$USR' WHERE `".$updateV['veld']."` = '".mysql_real_escape_string($updateV['valueOld'])."' $extraWhere ";
                }
	            }
            }
            else
            {
              foreach($updateTabelen[$update['tableName']] as $tabel)
              {
                $updateVelden=array($keyVeld);
                if($tabel=='OrderRegels' && $keyVeld=='code')
                  $updateVelden=array('PSAF','PSET');

                if($tabel=='CRM_naw' && $keyVeld=='VirtuelePortefeuille')
                  $updateVelden=array('Portefeuille');

                foreach($updateVelden as $updateVeld)
                {
 		              $DB_local2->SQL("SELECT id,$updateVeld FROM $tabel WHERE $updateVeld='".mysql_real_escape_string($data[$keyVeld])."'");
                  if($debug)
                  {
                    echo "Check3: SELECT id,$updateVeld FROM $tabel WHERE $updateVeld='".mysql_real_escape_string($data[$keyVeld])."'<br>\n";
                  }

		              $DB_local2->Query();
                  if($DB_local2->Records()==0)
                  {
                    $queries[]="UPDATE $tabel SET $updateVeld='".mysql_real_escape_string($data[$keyVeld])."' WHERE $updateVeld='".mysql_real_escape_string($localData[$keyVeld])."'";
                  }
                  else
                  {
                     $geenQuery=true;
                     $melding.="$updateVeld ".$data[$keyVeld]." is al aanwezig in $tabel bij $Bedrijf \n $updateVeld (".$localData[$keyVeld].") niet aangepast naar ".$data[$keyVeld].".\n";
                  }
                }
              }
            }
            if(isset($_DB_resources[DBportaal]))//Portaal gebruiker
            {
              if($update['tableName']=='Portefeuilles') //Sleutelveld/inlogcode ook in de portaal aanpassen.
              {
                $portaalDB=new DB(DBportaal);
                $query="UPDATE clienten SET portefeuille='".mysql_real_escape_string($data[$keyVeld])."' WHERE portefeuille='".mysql_real_escape_string($localData[$keyVeld])."'";
                $portaalDB->SQL($query);
                $portaalDB->Query();
                logIt('PortaalQuery:'.$query);
                if($debug)
                {
                  echo "PortaalQuery: $query <br>\n";
                }
              }
            }
          }
          else
          {
            if($debug)
            {
              echo "Geen update $keyVeld (".$localData[$keyVeld].") gelijk aan (".$data[$keyVeld].") of (".$localData[$keyVeld].") is leeg. <br>\n";
            }
          }
        }
			  $query = "UPDATE ".$update['tableName']." SET ";
		  }
		  else
		  {
			  $query = "INSERT INTO ".$update['tableName']." SET ";
			  $insert=true;
		  }
		}

		$records = array();
		if(is_array($data))
		{
      foreach($data as $key=>$value)
			{
				$records[] = " `".$key."` = '".mysql_real_escape_string($value)."' ";
			}
			$query .= implode(", ",$records);
		}
		else
		 $query .= $data;


		if($aantalRecords > 0)
		{
			$query .= " WHERE id = '".$update['tableId']."' ";
		}

    //if($geenQuery==true) # 10617 altijd door laten gaan.
    //  $query="SELECT 1";

		$DB_local->SQL($query);
    if($debug)
    {
      echo "1.) Bijwerken: $query <br>\n";
    }
		if($DB_local->Query())
		{
		  foreach($queries as $query)
      {
        if($debug)
        {
          echo "Bijwerken: $query <br>\n";
        }
        $DB_local->SQL($query);
        $DB_local->Query();
      }
      $deleteIds[]=$update['id'];
			// delete record.
      if(count($deleteIds)>1000)
      {
        $query = "DELETE FROM importdata WHERE id IN (" . implode(',', $deleteIds) . ")";
        $DB_queue_2->SQL($query);
        if (!$DB_queue_2->Query())
        {
          $_error = true;
        }
        $deleteIds=array();
      }
		}
		else
		{
			$_error = true;
		}

    if($insert==true && $update['tableName']=='Fondsen')
      $nieuweFondsen[]=$data;

		if($update['tableName']=='Rekeningmutaties')
		{
		  $herrekeningUitvoeren=max($herrekeningUitvoeren,1);
		  if($insert==true && $data['Transactietype']!='B' && $data['Grootboekrekening']=='FONDS' && db2jul($data['change_date']) > $tonenVanaf )
		    $nieuweFondsTransacties[]=$data;

		  if($insert==true && $data['Transactietype']!='B' && db2jul($data['change_date']) > $tonenVanaf )//&& $data['Grootboekrekening']!='FONDS'
		    $nieuweTransacties[]=$data;

		  $query="SELECT id, Afschriftnummer FROM VoorlopigeRekeningmutaties WHERE Verwerkt <> 1 AND Boekdatum='".$data['Boekdatum']."' AND Rekening = '".$data['Rekening']."' AND Bedrag = '".$data['Bedrag']."'";
		  $DB_local->SQL($query);
		  $DB_local->Query();
		  while($voorlopigeRekeningmutatie = $DB_local->nextRecord())
		  {
		    if($voorlopigeRekeningmutatie['id'] > 0)
		    {
		      $q="UPDATE VoorlopigeRekeningmutaties SET Verwerkt='1', change_date=NOW() WHERE id = '".$voorlopigeRekeningmutatie['id']."'";
		      $DB_local2->SQL($q);
		      $DB_local2->Query();
  		  }
		  }
		}
    elseif($update['tableName']=='Valutakoersen')
    {
      $herrekeningUitvoeren=2;
    }
    elseif($update['tableName']=='Fondskoersen')
    {
      $herrekeningUitvoeren=max($herrekeningUitvoeren,1);
      $fondsen[$data['Fonds']]=$data['Fonds'];
    }
  }


	//update Verwerkt status voor VoorlopigeRekeningafschriften
	$query="SELECT Rekening,Afschriftnummer FROM VoorlopigeRekeningafschriften WHERE Verwerkt <> 1 AND add_date > NOW() - INTERVAL 60 DAY ";
	$DB_local->SQL($query);
	$DB_local->Query();
	while($afschrift = $DB_local->NextRecord())
	{
	  $query="SELECT (SELECT count(*) as aantal FROM VoorlopigeRekeningmutaties WHERE Rekening = '".$afschrift['Rekening']."' AND Afschriftnummer = '".$afschrift['Afschriftnummer']."') as totaal,
		               (SELECT count(*) as aantal FROM VoorlopigeRekeningmutaties WHERE Rekening = '".$afschrift['Rekening']."' AND Afschriftnummer = '".$afschrift['Afschriftnummer']."' AND Verwerkt = 1) as verwerkt ";
		 $DB_local2->SQL($query);
		 $DB_local2->Query();
		 $verwerkStatus = $DB_local2->nextRecord();

		 if($verwerkStatus['verwerkt'] == $verwerkStatus['totaal'] && $verwerkStatus['totaal'] > 0)
		    $query="UPDATE VoorlopigeRekeningafschriften SET Verwerkt='1', change_date=NOW() WHERE Rekening = '".$afschrift['Rekening']."' AND Afschriftnummer = '".$afschrift['Afschriftnummer']."'";
     elseif($verwerkStatus['verwerkt'] > 0)
		    $query="UPDATE VoorlopigeRekeningafschriften SET Verwerkt='3', change_date=NOW() WHERE Rekening = '".$afschrift['Rekening']."' AND Afschriftnummer = '".$afschrift['Afschriftnummer']."'";
	   $DB_local2->SQL($query);
		 $DB_local2->Query();
	}

	$query="UPDATE VoorlopigeRekeningafschriften SET Verwerkt='1', change_date=NOW() WHERE Verwerkt > 1 AND add_date < NOW() - INTERVAL 7 DAY";
	$DB_local2->SQL($query);
	$DB_local2->Query();
	$query="UPDATE VoorlopigeRekeningmutaties SET Verwerkt='1', change_date=NOW() WHERE Verwerkt > 1 AND add_date < NOW() - INTERVAL 7 DAY";
	$DB_local2->SQL($query);
	$DB_local2->Query();
  
  logIt("nieuweFondsTransacties (".count($nieuweFondsTransacties).")");
  logIt("nieuweTransacties (".count($nieuweTransacties).")");
  logIt("nieuweFondsen (".count($nieuweFondsen).")");

	if(count($nieuweFondsTransacties) > 0)
    orderCheck($nieuweFondsTransacties);
  if(count($nieuweTransacties) > 0)
    transactieCheck($nieuweTransacties);
  if(count($nieuweFondsen) > 0)
	  fondsenCheck($nieuweFondsen);

  if(file_exists('../classes/AIRS_consolidatie.php'))
  {
    include_once('../classes/AIRS_consolidatie.php');
    $con = new AIRS_consolidatie();
    $con->verwijderOudeConsolidaties();
  }

  $portefeuilles='geen';
  if($maxAge['check_module_portefeuilleWaarde'] > 0 && $herrekeningUitvoeren>0)
  {
    $portefeuilles='';
    if((count($nieuweTransacties) > 0 || count($fondsen)>0) && $herrekeningUitvoeren==1)
    {
      $rekeningen=array();
      foreach($nieuweTransacties as $data)
        $rekeningen[]=$data['Rekening'];

      if(count($fondsen)>0)
      {
        $fondsenWhere = "Rekeningmutaties.Fonds IN ('" . implode("','", array_map('mysql_real_escape_string', $fondsen)) . "')";
        logIt("Herrekening voor ".$fondsenWhere);
      }
      else
        $fondsenWhere='';

      if(count($rekeningen)>0)
      {
        $rekeningenwhere = "Rekeningmutaties.Rekening IN('" . implode("','", $rekeningen) . "')";
        logIt("Herrekening voor ".$rekeningenwhere);
      }
      else
        $rekeningenwhere='';

      if($rekeningenwhere<>''&&$fondsenWhere<>'')
        $where="$fondsenWhere OR $rekeningenwhere";
      else
        $where=$fondsenWhere.$rekeningenwhere;

      $query="SELECT Rekeningen.Portefeuille FROM Rekeningmutaties 
JOIN Rekeningen ON Rekeningmutaties.Rekening=Rekeningen.Rekening 
JOIN Portefeuilles ON Rekeningen.Portefeuille=Portefeuilles.Portefeuille
WHERE ( $where  ) AND Portefeuilles.Einddatum > now()
GROUP BY Rekeningen.Rekening";
      $DB_local2->SQL($query);
      $DB_local2->Query();
      if($DB_local2->records() > 0)
        $portefeuilles=array();
      while($portefeuille = $DB_local2->NextRecord())
      {
        $portefeuilles[]=$portefeuille['Portefeuille'];
      }
    }
    if($herrekeningUitvoeren==2)
      $portefeuilles='';
  }

  if($herrekeningUitvoeren==2)
    logIt("Herrekening voor alle portefeuilles");
  elseif($herrekeningUitvoeren==1)
  {
    if(is_array($portefeuilles))
      logIt("Herrekening voor (" . count($portefeuilles) . ") portefeuilles.");
    else
      logIt("Herrekening voor (" . $portefeuilles . ") portefeuilles.");
  }

	if(!$_error)
	{
		return array(true,$portefeuilles,$melding);
	}
	else
	{
		return array(false,$portefeuilles,$melding);
	}
}

function vulPortaalApi($portefeuille)
{
  global $__appvar,$USR,$_DB_resources,$_SERVER, $disable_auth;

  //ini_set("memory_limit",'1024M');
  $maxtime=ini_get('max_execution_time');
  $startTime=$_SERVER['REQUEST_TIME'];
  logIt("vulPortaalAPI gestart om ".date("Y-m-d H:i:s")." script draait momenteel ".(time()-$startTime)." seconden van de maximaal $maxtime seconden. Maxmimaal geheugen:".ini_get("memory_limit"));
  $db=new DB();
  $query="
    SELECT 
      max(autoPortaalVulling) as autoPortaalVulling , 
      max(check_portaalCrmVink) as CrmVink, 
      max(portaalPeriode) as periode ,
      max(portaalDailyClientSync) as clientSync 
    FROM Vermogensbeheerders";
  $db->SQL($query);
  $check=$db->lookupRecord();

  if($check['autoPortaalVulling']==0)
  {
    return 0;
  }
  $disable_auth = true;
  include_once($__appvar["basedir"]."/classes/portefeuilleSelectieClass.php");
  include_once($__appvar["basedir"]."/classes/AE_cls_digidoc.php");
  include_once($__appvar["basedir"]."/classes/backofficeAfdrukkenClass.php");
  include_once($__appvar["basedir"]."/classes/portefeuilleVerdieptClass.php");
  include_once($__appvar["basedir"]."/classes/templateEmail.php");
  include_once('../classes/AE_cls_phpmailer.php');
  include_once($__appvar["basedir"]."/classes/pdfMailing.php");

  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");
  include_once($__appvar["basedir"]."/html/rapport/rapportRekenClass.php");
  include_once($__appvar["basedir"]."/html/rapport/PDFRapport.php");


  $dbdate = getLaatsteValutadatum();
  $jaar  = intval(substr($dbdate,0,4));
  $maand = substr($dbdate,5,2);
  $dag   = substr($dbdate,8,2);
  $laatsteJul = mktime(0,0,0,$maand,$dag,$jaar);

  if($check['periode']==1)
  {
    $maand=((floor((date("n",$laatsteJul)-1)/3))*3)+1;
    $selectie['datumVan']=date('d-m-Y',mktime(0,0,0,$maand,0,date('Y',$laatsteJul)));
    if(substr($selectie['datumVan'],0,5)=='31-12')
      $selectie['datumVan']=date('01-01-Y',$laatsteJul);
  }
  else
  {
    $selectie['datumVan'] = date('01-01-Y', $laatsteJul);
  }

  $selectie['datumTm']= date('d-m-Y',$laatsteJul);
  $selectie['rapport_types']='FRONT';
  //$selectie['portefeuilleIntern']=0;
  $selectie['consolidatieToevoegen']=0;
//  $selectie['bedrijf']=$__appvar["bedrijf"];
  $selectie['periode']='Dagrapportage';
  $selectie['bestandsnaamEind']=date('Ymd',$laatsteJul);

  $_SESSION['usersession']['superuser']=true;

  if($check['CrmVink']==1)
  {
    $selectie['CRM_rapport_vink']=1;
    $selectie['type']='portaal';
  }
  $selectie["selectedPortefeuilles"] = array($portefeuille);

  $afdruk = new backofficeAfdrukken($selectie);

  $selectData=$afdruk->validate();
  $afdruk->getPortefeuilles(true);
  $afdruk->startdatum = jul2sql($selectData['datumVan']);
  $afdruk->einddatum = jul2sql($selectData['datumTm']);


  logIt("vulPortaalAPI ".count($afdruk->portefeuilles).' portefeuilles gevonden.');
  $fullLog='';

  foreach ($afdruk->portefeuilles as $portefeuille=>$pdata)
  {
    $tmp=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);

    foreach ($tmp as $key=>$value)
    {
      $pdata[$key]=$value;
    }

    //$tmp['consolidatieToevoegen']=$pdata['consolidatieToevoegen'];
    if($pdata['portefeuilles'])
    {
      $tmp['portefeuilles'] = $pdata['portefeuilles'];
    }
    $afdruk->portefeuilles[$portefeuille] = $tmp;

    logIt("vulPortaalAPI Runtijd:".(time()-$startTime)."s. Gebruikt geheugen : ".round(memory_get_usage()/1024/1024,3)." MB. Begin rapportage portefeuille : $portefeuille");
    verwijderTijdelijkeTabel($portefeuille);
    $afdruk->initPdf();
    $afdruk->setVolgorde($portefeuille);

    $afdruk->getCrmRapport($portefeuille);

    if(count($afdruk->rapport_type) > 0 || $check['clientSync'] == 1)
    {

      $querySet='';
      $query="SELECT Gebruikers.Naam as accountmanagerGebruikerNaam,Gebruikers.emailAdres as accountmanagerEmail,Accountmanagers.Naam as accountmanagerNaam, Gebruikers.mobiel as accountmanagerTelefoon,
               Portefeuilles.consolidatie FROM
               Portefeuilles LEFT JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
               WHERE Portefeuilles.Portefeuille='".$portefeuille."'";
      $db=new DB();
      $db->SQL($query);

      $accountManager=$db->lookupRecord();
      foreach($accountManager as $key=>$value)
      {
        $pdata[$key] = $value;
      }
      if(count($afdruk->rapport_type) > 0)
      {
        $afdruk->loadPdfSettings($portefeuille);
        $afdruk->addReports($portefeuille);
        $afdruk->filename = $afdruk->getFilename($portefeuille);
        $pdfString = $afdruk->pdf->Output($afdruk->filename, "S");
        $afdruk->verwijderTijdelijkeRapportage($portefeuille);

        logIt("Runtijd:" . (time() - $startTime) . "s. Gebruikt geheugen : " . round(memory_get_usage() / 1024 / 1024, 3) . " MB. Aangemaakt portefeuille : $portefeuille");

        return $pdfString;
      }
      else
      {
        logit("Geen rapporten voor $portefeuille . Alleen Client Sync.");
      }
    }
    else
    {
      logit("Geen rapporten voor $portefeuille .");
    }

    unset($afdruk->portefeuilles[$portefeuille]);
  }

  $_SESSION['usersession']['superuser']=false;
  return false;

}

function vulPortaal()
{
  //
  //  LET op er is eenzelfde functie voor API calls vanuit het portaal, pas hier zonodig dingen aan!! (vulPortaalApi)
  //

  global $__appvar,$USR,$_DB_resources,$_SERVER;
  //ini_set("memory_limit",'1024M');
  $maxtime=ini_get('max_execution_time');
  $startTime=$_SERVER['REQUEST_TIME'];
  logIt("vulPortaal gestart om ".date("Y-m-d H:i:s")." script draait momenteel ".(time()-$startTime)." seconden van de maximaal $maxtime seconden. Maxmimaal geheugen:".ini_get("memory_limit"));
  $db=new DB();
  $query="SELECT max(autoPortaalVulling) as autoPortaalVulling , max(check_portaalCrmVink) as CrmVink, max(portaalPeriode) as periode ,max(portaalDailyClientSync) as clientSync FROM Vermogensbeheerders";
  $db->SQL($query);
  $check=$db->lookupRecord();

  if($check['autoPortaalVulling']==0)
    return 0;

  if(!isset($_DB_resources[DBportaal]))
  {
    echo "Geen portaal database instellingen gevonden.";
    logIt('Geen portaal database instellingen gevonden.');
    return 0;
  }
  $dbPortaal = new DB(DBportaal);
  $query='DESC clienten';
  $dbPortaal->SQL($query);
  $dbPortaal->Query();
  while($data=$dbPortaal->nextRecord())
    $velden[]=$data['Field'];
  $veldenKoppeling=array('name'=>'naam','name1'=>'naam1','email'=>'email','password'=>'wachtwoord','verzendAanhef'=>'verzendAanhef','depotbank'=>'Depotbank',
                         'accountmanagerNaam'=>'accountmanagerNaam','accountmanagerGebruikerNaam'=>'accountmanagerGebruikerNaam','accountmanagerTelefoon'=>'accountmanagerTelefoon',
                         'accountmanagerEmail'=>'accountmanagerEmail','geblokkeerd'=>'aktief','rel_id'=>'crmId','vermogensbeheerder'=>'Vermogensbeheerder','consolidatie'=>'consolidatie',
                         'risicoKlasse'=>'Risicoklasse','soortOvereenkomst'=>'SoortOvereenkomst');

  include_once("../classes/portefeuilleSelectieClass.php");
  include_once("../classes/AE_cls_digidoc.php");
  include_once("../classes/backofficeAfdrukkenClass.php");
  include_once("../classes/portefeuilleVerdieptClass.php");
  include_once("../classes/templateEmail.php");
  include_once('../classes/AE_cls_phpmailer.php');
  include_once("../classes/pdfMailing.php");
  define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
  include_once("../classes/AE_cls_fpdf.php");
  include_once("../html/rapport/rapportRekenClass.php");
  include_once("../html/rapport/PDFRapport.php");

  $laatsteJul=db2jul(getLaatsteValutadatum());
  if($check['periode']==1)
  {
    $maand=((floor((date("n",$laatsteJul)-1)/3))*3)+1;
    $selectie['datumVan']=date('d-m-Y',mktime(0,0,0,$maand,0,date('Y',$laatsteJul)));
    if(substr($selectie['datumVan'],0,5)=='31-12')
      $selectie['datumVan']=date('01-01-Y',$laatsteJul);
  }
  else
    $selectie['datumVan']=date('01-01-Y',$laatsteJul);

  $selectie['datumTm']= date('d-m-Y',$laatsteJul);
  $selectie['rapport_types']='FRONT';
  //$selectie['portefeuilleIntern']=0;
  $selectie['geconsolideerd']=1;
  $selectie['bedrijf']=$__appvar["bedrijf"];
  $selectie['periode']='Dagrapportage';
  $selectie['bestandsnaamEind']=date('Ymd',$laatsteJul);

  $_SESSION['usersession']['superuser']=true;

  if($check['CrmVink']==1)
  {
    $selectie['CRM_rapport_vink']=1;
    $selectie['type']='portaal';
  }
  $afdruk = new backofficeAfdrukken($selectie);
  $selectData=$afdruk->validate();
  $afdruk->getPortefeuilles(true);
	$afdruk->startdatum = jul2sql($selectData['datumVan']);
	$afdruk->einddatum = jul2sql($selectData['datumTm']);

  /*
  foreach($afdruk->portefeuilles as $portefeuille=>$pdata)
  {
   $tmp=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);
   if($tmp['wachtwoord']=='' && $tmp['email']=='')
   {
     unset($afdruk->portefeuilles[$portefeuille]);
   }
   else
   {
     //$tmp['consolidatieToevoegen']=$pdata['consolidatieToevoegen'];
     //if($pdata['portefeuilles'])
     //  $tmp['portefeuilles']=$pdata['portefeuilles'];
     //$afdruk->portefeuilles[$portefeuille] = $tmp;
   }
  }
  */
  logIt(count($afdruk->portefeuilles).' portefeuilles gevonden.');
  $fullLog='';
  foreach ($afdruk->portefeuilles as $portefeuille=>$pdata)
 	{
    logit("Beginnen aan $portefeuille .");
    $tmp=$afdruk->portefeuilleSelectie->getAllFields($portefeuille);

    if($tmp['wachtwoord']=='' || $tmp['email']=='')
    {
      logit("$portefeuille overgeslagen, geen wachtwoord of email.");
      unset($afdruk->portefeuilles[$portefeuille]);
      continue;
    }
    foreach ($tmp as $key=>$value)
      $pdata[$key]=$value;
    $tmp['consolidatieToevoegen']=$pdata['consolidatieToevoegen'];
    if($pdata['portefeuilles'])
      $tmp['portefeuilles']=$pdata['portefeuilles'];
    $afdruk->portefeuilles[$portefeuille] = $tmp;

    logIt("Runtijd:".(time()-$startTime)."s. Gebruikt geheugen : ".round(memory_get_usage()/1024/1024,3)." MB. Begin rapportage portefeuille : $portefeuille");
 	  verwijderTijdelijkeTabel($portefeuille);
 	  $afdruk->initPdf();
    $afdruk->setVolgorde($portefeuille);
    $afdruk->getCrmRapport($portefeuille);
    if(count($afdruk->rapport_type) > 0 || $check['clientSync'] == 1)
    {

       //file_put_contents('/develop/php/robert/AIRS/temp/'.$portefeuille.'.pdf',$pdfString);
       $emails=explode(';',$pdata['email']);
       $pdata['email']=$emails[0];

       $querySet='';
       $query="SELECT Gebruikers.Naam as accountmanagerGebruikerNaam,Gebruikers.emailAdres as accountmanagerEmail,Accountmanagers.Naam as accountmanagerNaam, Gebruikers.mobiel as accountmanagerTelefoon,
               Portefeuilles.consolidatie FROM
               Portefeuilles LEFT JOIN Gebruikers ON Portefeuilles.Accountmanager = Gebruikers.Accountmanager LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
               WHERE Portefeuilles.Portefeuille='".$portefeuille."'";
       $db=new DB();
       $db->SQL($query);
       $accountManager=$db->lookupRecord();
       foreach($accountManager as $key=>$value)
         $pdata[$key]=$value;

       foreach($velden as $veld)
       {
         if($veldenKoppeling[$veld])
         {
           if($veld=='geblokkeerd')
           {
             if($pdata[$veldenKoppeling[$veld]]==1)
               $portaalWaarde=0;
             else
               $portaalWaarde=1;
           }
           else
             $portaalWaarde=$pdata[$veldenKoppeling[$veld]];

           $querySet .= ', ' . $veld . "= '" . mysql_real_escape_string($portaalWaarde). "'";
         }
       }

      if($__appvar["portaalDbReconnect"]==true)
      {
         if(isset($dbPortaal) && is_object($dbPortaal))
         {
           $closed=$dbPortaal->close();
           logit("Portaal database connectie gesloten. ($closed)");
         }
         else
         {
           logit("Nog geen connectie met de portaal.");
         }
      }
      $dbPortaal = new DB(DBportaal);
      $q="SELECT id FROM clienten WHERE portefeuille='".$portefeuille."'";
      if($dbPortaal->QRecords($q) == 0)
      {
        $q="INSERT INTO clienten SET change_user='$USR',change_date=now(),add_user='$USR',add_date=now() $querySet ,
            portefeuille='".$portefeuille."',passwordTimes=0,loginTimes=0,loginLast='0000-00-00 00:00:00'";
         $dbPortaal->SQL($q);
         if($dbPortaal->Query())
           logit("Nieuwe Client voor $portefeuille aangemaakt.");
         else
           logit("Nieuwe Client voor $portefeuille niet aangemaakt.");
         $clientId=$dbPortaal->last_id();
      }
      else
      {
        $clientId=$dbPortaal->lookupRecord();
        $clientId=$clientId['id'];
        $query="UPDATE clienten SET change_user='$USR',change_date=now() $querySet WHERE id='$clientId'";
        $dbPortaal->SQL($query);
        if(!$dbPortaal->Query())
        {
          logit("clienten.change_date voor voor $portefeuille aangemaakt met id $clientId niet aangepast. ($query)");
        }
      }
  
      if(count($afdruk->rapport_type) > 0)
      {
        $afdruk->loadPdfSettings($portefeuille);
        $afdruk->addReports($portefeuille);
        $afdruk->filename = $afdruk->getFilename($portefeuille);
        $pdfString = $afdruk->pdf->Output($afdruk->filename, "S");
        $afdruk->verwijderTijdelijkeRapportage($portefeuille);
        logIt("Runtijd:" . (time() - $startTime) . "s. Gebruikt geheugen : " . round(memory_get_usage() / 1024 / 1024, 3) . " MB. Aangemaakt portefeuille: $portefeuille ClientId: $clientId");
  
        if ($clientId > 0)
        {
          $q = "DELETE FROM datastoreDaily WHERE clientID='$clientId'";
          $dbPortaal->SQL($q);
          if(!$dbPortaal->Query())
            logit("Verwijderen oude datastoreDaily records voor $clientId mislukt ($q)");
            
          $q = "INSERT INTO  datastoreDaily SET change_user='$USR',change_date=now(),add_user='$USR',add_date=now(),
        clientID='$clientId',reportDate='" . $afdruk->einddatum . "',filename='" . $afdruk->filename . "',filesize='" . strlen($pdfString) . "',
        filetype='application/pdf',description='" . str_replace($portefeuille, 'rapport', $afdruk->filename) . "',
        blobdata=unhex('" . bin2hex($pdfString) . "') ";
          $dbPortaal->SQL($q);
          logIt("Runtijd:" . (time() - $startTime) . "s. Gebruikt geheugen : " . round(memory_get_usage() / 1024 / 1024, 3) . " MB. Verzenden portefeuille: $portefeuille ClientId: $clientId");
          if ($dbPortaal->Query())
          {
            $tmpLog = "<br>" . logTxt("Dag rapportage " . $portefeuille . " in portaal geplaatst.");
            echo $tmpLog;
            flush();
            ob_flush();
            $fullLog .= $tmpLog;
            logit("Rapporten voor $portefeuille in portaal gezet.");
          }
          else
          {
            logit("Rapporten voor $portefeuille niet in portaal gezet.");
          }
        }
      }
      else
      {
        logit("Geen rapporten voor $portefeuille . Alleen Client Sync.");
      }
    }
    else
    {
      logit("Geen rapporten voor $portefeuille .");
    }

    unset($afdruk->portefeuilles[$portefeuille]);
	}
  $dbPortaal = new DB(DBportaal);
  $q="DELETE FROM datastoreDaily WHERE change_date < now() - interval 15 day";
  $dbPortaal->SQL($q);
  if($dbPortaal->Query())
    $fullLog.="<br>".logTxt("Records ouder dan 15 dagen verwijderd.");
  else
    $fullLog.="<br>".logTxt("Verwijderen oude records mislukt.");

  include_once('../classes/portaalSync.php');
  $portaalSync=new portaalSync();
  $portaalUpdates=$portaalSync->CRM_syncPortaalPortefeuilleClusters(false,true);
  if(is_array($portaalUpdates) && (count($portaalUpdates['update']) > 0 || count($portaalUpdates['insert'])>0 || count($portaalUpdates['deletes'])>0))
    $fullLog.= $portaalSync->updatePortaalClusters($portaalUpdates);

  $db=new DB();
  $query="SELECT max(emailPortaalvulling) as emailPortaalvulling FROM Vermogensbeheerders limit 1";
  $db->SQL($query);
  $orderCheck=$db->lookupRecord();
  $cfg=new AE_config();
  $mailserver=$cfg->getData('smtpServer');
  $body="Portaal vulling log. $fullLog" ;
  storeControleMail('PortaalVulling',"Portaal vulling: ".date("d-m-Y H:i"),$body);
  $emailAddesses=getEmailAdressen('portaal');
  if (count($emailAddesses)>0 && $mailserver !='')
  {
    //$emailAddesses=explode(";",$orderCheck['emailPortaalvulling']);
    include_once('../classes/AE_cls_phpmailer.php');
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->From     = $emailAddesses[0];
    $mail->FromName = "Airs";
    $mail->Body    = $body;
    $mail->AltBody = html_entity_decode(strip_tags($body));
    foreach ($emailAddesses as $emailadres)
    {
    	$mail->AddAddress($emailadres,$emailadres);
    }
    $mail->Subject = "Portaal vulling: ".date("d-m-Y H:i");
    $mail->Host=$mailserver;
    if(!$mail->Send())
    {
      echo "Verzenden van e-mail mislukt.";
    }
  }
  $_SESSION['usersession']['superuser']=false;


}

function updateNawPortaal($data)
{
  global $USR;
  $query="SELECT naam as name,naam1 as name1,email,wachtwoord as password,portefeuille FROM CRM_naw WHERE id='".$data['id']."'";
  $db=new DB();
  $db->SQL($query);
  $db->Query();
  $oldRecord=$db->lookupRecord();
  $update=array();
  foreach($data as $key=>$value)
  {
    if($key <> 'id' && $oldRecord[$key] <> $value && $value <> '')
    {
      if($key=='password')
      {
        if(strlen($data['password'])>5)
        {
          $update[$key]="'".mysql_real_escape_string($data[$key])."'";
          $update['passwordChange']='now()';
        }
      }
      elseif($key=='email')
      {
        $emails=explode(';',$data['email']);
        $data['email']=$emails[0];
        if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$", $data[$key]))
          $update[$key]="'".mysql_real_escape_string($data[$key])."'";
      }
      else
        $update[$key]="'".mysql_real_escape_string($value)."'";

    }
  }

  if(count($update) > 0)
  {
    $dbPortaal = new DB(DBportaal);
    $q="SELECT id FROM clienten WHERE portefeuille='".$oldRecord['portefeuille']."'";
    //$q="SELECT id FROM clienten WHERE rel_id='".$data['id']."'"; Nog te activeren als we over willen gaan naar rel_id.
    if($dbPortaal->QRecords($q) > 0)
    {
      $clientId=$dbPortaal->lookupRecord();
      $clientId=$clientId['id'];
      $query="UPDATE clienten SET change_date=now(),change_user='$USR' ";
      foreach($update as $key=>$value)
        $query.=",$key=$value";
      $query.=" WHERE id='$clientId'";
      $dbPortaal->SQL($query);
      if(!$dbPortaal->Query())
      {
        echo "Client update in portaal mislukt.";exit;
      }
    }
  }
}

function updateNawPortaalById($data,$insert=false)
{
  global $USR;
  $query="SELECT naam as name,naam1 as name1,email,wachtwoord as password,portefeuille FROM CRM_naw WHERE id='".$data['id']."'";
  $db=new DB();
  $db->SQL($query);
  $db->Query();
  $oldRecord=$db->lookupRecord();
  $update=array();
  foreach($data as $key=>$value)
  {
    if($key <> 'id' && (($oldRecord[$key] <> $value && $value <> '' ) || $insert==true))
    {
      if($key=='password')
      {
        if(strlen($data['password'])>5)
        {
          $update[$key]="'".mysql_real_escape_string($data[$key])."'";
          $update['passwordChange']='now()';
        }
      }
      elseif($key=='email')
      {
        $emails=explode(';',$data['email']);
        $data['email']=$emails[0];
        if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$", $data[$key]))
          $update[$key]="'".mysql_real_escape_string($data[$key])."'";
      }
      else
        $update[$key]="'".mysql_real_escape_string($value)."'";
      
    }
  }
  
  if(count($update) > 0)
  {
    $dbPortaal = new DB(DBportaal);
    $q="SELECT id FROM clienten WHERE rel_id='".$data['id']."'";
    if($dbPortaal->QRecords($q) > 0)
    {
      $query="UPDATE clienten SET change_date=now(),change_user='$USR' ";
      $clientId=$dbPortaal->lookupRecord();
      $clientId=$clientId['id'];
      $where = " WHERE id='$clientId'";
    }
    else
    {
      $query="INSERT INTO clienten SET change_date=now(),change_user='$USR',add_date=now(),add_user='$USR', rel_id='".$data['id']."'";
      $where='';
    }
     
    foreach($update as $key=>$value)
      $query.=",$key=$value";
    $query.=$where;
    $dbPortaal->SQL($query);
    if(!$dbPortaal->Query())
    {
      echo "Client update in portaal mislukt.";exit;
    }
  }
}

function getModelRendement($modelPortefeuille,$afwijkendeDatum=false)
{
  
  if($afwijkendeDatum==false)
  {
    $tabel='laatstePortefeuilleWaarde';
  }
  else
  {
    $tabel='tempLaatstePortefeuilleWaarde';
  }
  $db=new DB();
  $query="SELECT Fixed FROM ModelPortefeuilles WHERE Portefeuille='".mysql_real_escape_string($modelPortefeuille)."'";
  $db->SQL($query);
  $fixed=$db->lookupRecord();
  if($fixed['Fixed']>0)
    return 0;

  $query="SELECT rendement FROM $tabel WHERE portefeuille='".mysql_real_escape_string($modelPortefeuille)."' AND change_date > (now() - interval 1 hour)";
  $db->SQL($query);
  $rendement=$db->lookupRecord();

  if(is_array($rendement))
  {
    //logIt("ModelPortefeuille rendement voor $modelPortefeuille opgehaald, ".$rendement['rendement']." %");
    return $rendement['rendement'];
  }
  portefeuilleWaardeHerrekening($modelPortefeuille,$afwijkendeDatum);
  $db->SQL($query);
  $rendement=$db->lookupRecord();

  if(is_array($rendement))
  {
    $query="UPDATE $tabel SET rendement='".$rendement['rendement']."',change_date=now() WHERE portefeuille='".mysql_real_escape_string($modelPortefeuille)."'";
    $db->SQL($query);
    $db->query();
    logIt("ModelPortefeuille rendement voor $modelPortefeuille opgeslagen in $tabel $afwijkendeDatum, ".$rendement['rendement']." %");
    return $rendement['rendement'];
  }
}

function portefeuilleWaardeHerrekening($portefeuille='',$afwijkendeDatum=false)
{
  global $USR,$__appvar;
  include_once('../html/rapport/rapportRekenClass.php');
  $db=new DB();
  $fondswaardenPerPortefeuille=array();
  $laatstePortefeuilleWaardenPerPortefeuille=array();
  if($afwijkendeDatum==false)
  {
    $datum = substr(getLaatsteValutadatum(), 0, 10);
    $laatstePortefeuilleWaardeTabel='laatstePortefeuilleWaarde';
    $crmLaatsteFondsWaardenTabel='crmLaatsteFondsWaarden';
  }
  else
  {
    $datum = $afwijkendeDatum;
    $laatstePortefeuilleWaardeTabel='tempLaatstePortefeuilleWaarde';
    $crmLaatsteFondsWaardenTabel='tempLaatsteFondsWaarden';
  }
  $jaar=substr($datum,0,4);
  $maand=substr($datum,5,2);

  if($maand==1)
    $dag=1;
  else
    $dag=0;
  $vorigeMaand = date('Y-m-d', mktime(0, 0, 0, $maand, $dag, $jaar));
  $vorigeKwartaal = date('Y-m-d', mktime(0, 0, 0, ceil($maand / 3) * 3 - 2, $dag, $jaar));

  $porteuilles=array();
  if(is_array($portefeuille))
  {
    $porteuilles=$portefeuille;
  }
  elseif($portefeuille == '')
  {
    $query="SELECT Portefeuille FROM Portefeuilles WHERE eindDatum > '$datum'";
    $db->SQL($query);
    $db->Query();
    while($data=$db->nextRecord())
    {
      $porteuilles[]=$data['Portefeuille'];
    }
  }
  else
   $porteuilles[]=$portefeuille;

  //if($__appvar["laatstePortefeuilleWaardeExtraLog"]==true||$__appvar["bedrijf"]=='TPA'||$__appvar["bedrijf"]=='AUR'||$__appvar["bedrijf"]=='HOME'||$__appvar["bedrijf"]=='ANO'||$__appvar["bedrijf"]=='SLV'||$__appvar["bedrijf"]=='EFI'||$__appvar["bedrijf"]=='DOO'||$__appvar["bedrijf"]=='JVN'||$__appvar["bedrijf"]=='BOX')
  //  $extraLog=true;
  //else
  //  $extraLog=false;
  
  $extraLog=true;
  //if($extraLog)
  logIt('Trigger:'.$USR.', portefeuilleWaardeHerrekening voor filter:'.implode(',',$porteuilles));

  include_once('../classes/portefeuilleSelectieClass.php');
  if($__appvar["bedrijf"]=='HOME')
    $selectie=new portefeuilleSelectie(array('selectedFields'=>$porteuilles,'geconsolideerd'=>true,'metConsolidatie'=>'10'),'Portefeuilles.Client',false,$afwijkendeDatum);
  else
    $selectie=new portefeuilleSelectie(array('selectedFields'=>$porteuilles,'geconsolideerd'=>true,'metConsolidatie'=>'10','bedrijf'=>$__appvar["bedrijf"]),'Portefeuilles.Client',false,$afwijkendeDatum);
  
  $selectie->addConsolidatiePortefeuilles();//consolidatieAanmaken(true,false,true);
  $porteuilles=$selectie->getSelectie();

  $query='DESC '.$laatstePortefeuilleWaardeTabel;
  $db->SQL($query);
  $db->Query();
  $dbFields=array();
  while($dbdata=$db->nextRecord())
  {
    $dbFields[]=$dbdata['Field'];
    $dbZeroFields[$dbdata['Field']]=0;
  }
  unset($dbZeroFields['add_date']);
  unset($dbZeroFields['add_user']);
  unset($dbZeroFields['change_user']);
  unset($dbZeroFields['change_date']);
  unset($dbZeroFields['Portefeuille']);
  unset($dbZeroFields['portefeuille']);
  unset($dbZeroFields['toelichting']);
  unset($dbZeroFields['id']);
  $dbZeroFields['zorgMeting']="";
  $queryZeroSet='';
  foreach($dbZeroFields as $key=>$value)
    $queryZeroSet.=", ".$laatstePortefeuilleWaardeTabel.".".$key."='".$value."'";

  //if($extraLog)
  logIt('na filtering portefeuilleWaardeHerrekening voor:'.implode(',',array_keys($porteuilles)));

  foreach($porteuilles as $portefeuille=>$portefeuilleData)
  {
    if($portefeuille=='')
      continue;
    /*
    if($portefeuilleData['consolidatieToevoegen']==1)
    {
      $tmp = $selectie->createVirtuelePortefeuille($portefeuille, $portefeuilleData,true);
      if($tmp['afgebroken']==true)
      {
        logit('Aanmaken consolidatie voor ('.$portefeuille.') afgebroken.');
        continue;
      }
    }
    */
    $beginTijd=getmicrotime();
    
    if($extraLog)
      logIt($portefeuille. ' | stap 1 | begin portefeuilleWaardeHerrekening. | '.(getmicrotime()-$beginTijd));
    $query="SELECT aktief,Portefeuille FROM CRM_naw WHERE Portefeuille='$portefeuille'";
    $db->SQL($query);
    $actief=$db->lookupRecord();
    if($actief['Portefeuille']==$portefeuille && $actief['aktief']==0 && $afwijkendeDatum==false)
    {
      $query="UPDATE $laatstePortefeuilleWaardeTabel SET change_date=now(),change_user='END' $queryZeroSet WHERE portefeuille='".$portefeuille."'";
      $db->SQL($query);
      $db->Query();
      logIt('portefeuilleWaardeHerrekening afgebroken voor:'.$portefeuille.' (crm record inactief)');
    }
    else
    {
    $query="SELECT Portefeuilles.Portefeuille,Portefeuilles.Startdatum,Vermogensbeheerders.PerformanceBerekening,Portefeuilles.ZpMethode, Portefeuilles.TijdelijkUitsluitenZp,Portefeuilles.Vermogensbeheerder, Vermogensbeheerders.jaarafsluitingPerBewaarder,
    Vermogensbeheerders.check_module_SCENARIO,Vermogensbeheerders.portefeuilleWaardeInclVkm, Portefeuilles.ModelPortefeuille, Portefeuilles.SpecifiekeIndex,
    CRM_naw.laatsteRapDatumSignalering
    FROM Portefeuilles LEFT JOIN CRM_naw ON Portefeuilles.Portefeuille=CRM_naw.portefeuille
    JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder
    WHERE Portefeuilles.Portefeuille = '$portefeuille'";
    $db->SQL($query);
    $pRec=$db->lookupRecord();
    
    $startJul=db2jul($pRec['Startdatum']);
    $beginJaarJul=mktime(0,0,0,1,1,$jaar);
    if($startJul > $beginJaarJul)
      $startDatum=date("Y-m-d",$startJul);
    else
      $startDatum=date("Y-m-d",$beginJaarJul);

    if(substr($startDatum,5,5)=='01-01')
      $startjaar=true;
    else
      $startjaar=false;

    if($pRec['jaarafsluitingPerBewaarder']==1)
    {
      $fondswaarden['a'] = berekenPortefeuilleWaardeBewaarders($portefeuille, $startDatum, $startjaar, 'EUR', $startDatum);
      $fondswaarden['b'] = berekenPortefeuilleWaardeBewaarders($portefeuille, $datum, 0, 'EUR', $startDatum);
    }
    else
    {
      $fondswaarden['a'] = berekenPortefeuilleWaarde($portefeuille, $startDatum, $startjaar, 'EUR', $startDatum);
      $fondswaarden['b'] = berekenPortefeuilleWaarde($portefeuille, $datum, 0, 'EUR', $startDatum);
    }
    vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$startDatum);
	  vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$datum);
    if($extraLog)
        logIt($portefeuille. ' | stap 2 | Tijdelijke rapportage gevuld. | '.(getmicrotime()-$beginTijd));
    if(runPreProcessor($portefeuille)==1)
    {
      if (!isset($portefeuilleData['portefeuilles']))//geen consolidatie
      {
        $fondswaardenPerPortefeuille[$portefeuille] = getDataFromTijdelijkeRapportage($portefeuille,$datum);
      }
    }
    else
    {
      if (!isset($portefeuilleData['portefeuilles']))//geen consolidatie
      {
        $fondswaardenPerPortefeuille[$portefeuille] = $fondswaarden['b'];
      }
    }
    $rendementProcent = performanceMeting($portefeuille, $startDatum, $datum, $pRec['PerformanceBerekening'],'EUR');
    if($extraLog)
        logIt($portefeuille. ' | stap 3 | performanceMeting YTD klaar. | '.(getmicrotime()-$beginTijd));
    $vorigeMaand = date('Y-m-d', mktime(0, 0, 0, $maand, $dag, $jaar));
    $vorigeKwartaal = date('Y-m-d', mktime(0, 0, 0, ceil($maand / 3) * 3 - 2, $dag, $jaar));
      
    if($startJul > db2jul($vorigeMaand))
      $vorigeMaand=date("Y-m-d",$startJul);
    if($startJul > db2jul($vorigeKwartaal))
      $vorigeKwartaal=date("Y-m-d",$startJul);

      if($pRec['PerformanceBerekening']<3)
      {
        if(substr($vorigeMaand,5,5)=='01-01')
          $startjaar=true;
        else
          $startjaar=false;
        if($startDatum <> $vorigeMaand)
          vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $vorigeMaand,$startjaar,'EUR',$startDatum) ,$portefeuille,$vorigeMaand);

        if(substr($vorigeKwartaal,5,5)=='01-01')
          $startjaar=true;
        else
          $startjaar=false;
        if($startDatum <> $vorigeKwartaal || $vorigeMaand <>$vorigeKwartaal)
          vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $vorigeKwartaal,$startjaar,'EUR',$startDatum) ,$portefeuille,$vorigeKwartaal);

      }
      $rendementMTD = performanceMeting($portefeuille, $vorigeMaand, $datum, $pRec['PerformanceBerekening'],'EUR');
      $rendementQTD = performanceMeting($portefeuille, $vorigeKwartaal, $datum, $pRec['PerformanceBerekening'],'EUR');
      if(db2jul($pRec['laatsteRapDatumSignalering'])>1)
      {
        if(substr($pRec['laatsteRapDatumSignalering'],5,5)=='12-31')
          $rtdStartDatum=date('Y-m-d',db2jul($pRec['laatsteRapDatumSignalering'])+86400);
        else
          $rtdStartDatum = $pRec['laatsteRapDatumSignalering'];
  
        if(substr($rtdStartDatum,5,5)=='01-01')
          $startjaar=true;
        else
          $startjaar=false;
        
        if($startDatum <> $rtdStartDatum || $vorigeMaand <>$rtdStartDatum || $vorigeKwartaal <>$rtdStartDatum)
          vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $pRec['laatsteRapDatumSignalering'],$startjaar,'EUR',$rtdStartDatum) ,$portefeuille,$pRec['laatsteRapDatumSignalering']);
        $rendementRTD = performanceMeting($portefeuille, $rtdStartDatum, $datum, $pRec['PerformanceBerekening'],'EUR');
      }
      else
      {
        $rendementRTD=0;
        $rtdStartDatum='';
      }
      if($extraLog)
      {
        logIt("YTD $rendementProcent = performanceMeting($portefeuille, $startDatum, $datum, ".$pRec['PerformanceBerekening']." ,'EUR')");
        logIt("MTD $rendementMTD = performanceMeting($portefeuille, $vorigeMaand, $datum, " . $pRec['PerformanceBerekening'] . " ,'EUR');");
        logIt("QTD $rendementQTD = performanceMeting($portefeuille, $vorigeKwartaal, $datum, " . $pRec['PerformanceBerekening'] . " ,'EUR');");
        if($rtdStartDatum<>'')
        logIt("RTD $rendementRTD = performanceMeting($portefeuille, $rtdStartDatum, $datum, " . $pRec['PerformanceBerekening'] . " ,'EUR');");
      }
      if($extraLog)
        logIt($portefeuille. ' | stap 4 | performanceMeting QTD/MTD klaar. | '.(getmicrotime()-$beginTijd));
      //echo "$rendementProcent  	= performanceMeting(".$portefeuille.", ".$startDatum.", ".$datum.", ".$pRec['PerformanceBerekening'].",EUR ";exit;
    $modelRendement=0;
    if($pRec['ModelPortefeuille']<>'' && $pRec['ModelPortefeuille'] <> $portefeuille)
    {
      $modelRendement = getModelRendement($pRec['ModelPortefeuille'],$afwijkendeDatum);
      if($extraLog)
        logIt("Voor Portefeulle $portefeuille heeft ModelPortefeuille ".$pRec['ModelPortefeuille']." een rendement van $modelRendement %");
    }
    $totaalWaarde=array();
 	  foreach ($fondswaarden['a'] as $regel)
	  {
      $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
      if($regel['type']=='rente' && $regel['fonds'] != '')
        $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
	  }
    $saldoGeldrekeningen=0;
    $categorieVerdeling=array();
    foreach ($fondswaarden['b'] as $regel)
	  {
      $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];

      if($regel['type']=='fondsen')
      {
        $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
        $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling[$regel['beleggingscategorie']] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rente' && $regel['fonds'] != '')
      {
        $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
        $categorieVerdeling['VAR'] += $regel['actuelePortefeuilleWaardeEuro'];
      }
      elseif($regel['type']=='rekening')
      {
        $categorieVerdeling['LIQ'] += $regel['actuelePortefeuilleWaardeEuro'];
        $saldoGeldrekeningen+= $regel['actuelePortefeuilleWaardeEuro'];
      }
	  }
    if($extraLog)
      logIt($portefeuille. ' | stap 5 | modelRendement klaar. | '.(getmicrotime()-$beginTijd));

	  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
    $koersResultaat=gerealiseerdKoersresultaat($portefeuille,$startDatum,$datum,'EUR',true);
    $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
		$stortingen = getStortingen($portefeuille,$startDatum, $datum);
		$onttrekkingen = getOnttrekkingen($portefeuille,$startDatum, $datum);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
    if($extraLog)
      logIt($portefeuille. ' | stap 6 | resultaatBepaling klaar. | '.(getmicrotime()-$beginTijd));
		$query = "SELECT Grootboekrekeningen.Grootboekrekening, SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers)  AS kosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$startDatum' AND Rekeningmutaties.Boekdatum <= '$datum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Grootboekrekening";
    $db->SQL($query);
    $db->Query();
    $kosten=array();
    while($data=$db->nextRecord())
    {
      $kosten[$data['Grootboekrekening']]=$data['kosten'];
      $kosten['Kosten']+=$data['kosten'];
    }

    $query = "SELECT Grootboekrekeningen.Grootboekrekening, SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers) AS  opbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$startDatum' AND Rekeningmutaties.Boekdatum <= '$datum' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Grootboekrekening";
    $db->SQL($query);
    $db->Query();
    $opbrengsten=array();
    while($data=$db->nextRecord())
    {
      $opbrengsten[$data['Grootboekrekening']]=$data['opbrengsten'];
      $opbrengsten['Opbrengsten']+=$data['opbrengsten'];
    }
    if($extraLog)
      logIt($portefeuille. ' | stap 7 | Kosten en Opbrengsten klaar. | '.(getmicrotime()-$beginTijd));

    $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
    $valutaResultaat=$resultaatVerslagperiode-($koersResultaat+$ongerealiseerd+$opbrengsten['Opbrengsten']+$kosten['Kosten']+$opgelopenRente);
    $ongerealiseerd+=$valutaResultaat;

    $data=$dbZeroFields;
    if($pRec['TijdelijkUitsluitenZp'] == 0 && $pRec['ZpMethode'] <> 0)
    {
      if(!defined('FPDF_FONTPATH'))
        define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
      include_once("../classes/AE_cls_fpdf.php");
      include_once("../html/rapport/Zorgplichtcontrole.php");
      $zorgplicht = new Zorgplichtcontrole();
      if($pRec['ZpMethode'] == 1)
        $zpwaarde=$zorgplicht->zorgplichtMeting($pRec,$datum);
      if($pRec['ZpMethode'] == 2)
        $zpwaarde=$zorgplicht->standaarddeviatieMeting($pRec,$datum);
      if($pRec['ZpMethode'] == 3)
        $zpwaarde=$zorgplicht->werkelijkeStandaarddeviatieMeting($pRec,$datum);

      if(trim($zpwaarde['zorgMeting']) == 'Voldoet')
        $data['zorgMeting']=$zpwaarde['zorgMeting'];
      else
        $data['zorgMeting']=$zpwaarde['zorgMeting']." | ".$zpwaarde['voldoetNietReden'];

    }
    else
    {
      if($pRec['TijdelijkUitsluitenZp'] == 1)
        $data['zorgMeting']="Tijdelijk uitgesloten";
      elseif($pRec['ZpMethode']==0)
        $data['zorgMeting']="Geen parameters";
    }
    if($extraLog)
      logIt($portefeuille. ' | stap 8 | zorgplichtMeting klaar. | '.(getmicrotime()-$beginTijd));
      
    $afm=AFMstd($portefeuille,$datum);
  
    if($extraLog)
      logIt($portefeuille. ' | stap 9 | AFM STDev klaar. | '.(getmicrotime()-$beginTijd));
  
      if($pRec['check_module_SCENARIO'] >0)
    {
      $query="SELECT id,doelvermogen,doeldatum,gewenstRisicoprofiel FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
 	  	$db->SQL($query);
	  	$db->Query();
		  $crmId = $db->nextRecord();
      if($crmId['doelvermogen'] <> 0 && $crmId['doeldatum'] <> '' && $crmId['gewenstRisicoprofiel'] <> '' )
      {
        include_once($__appvar["basedir"]."/classes/scenarioBerekening.php");
        $sc= new scenarioBerekening($crmId['id']);
        $sc->CRMdata['startvermogen']=$totaalWaarde['eind'];
        $sc->CRMdata['startdatum']=$datum;
        if(!$sc->loadMatrix())
          $sc->createNewMatix(true);
        $aantalSimulaties=10000;
        $sc->berekenSimulaties(0,$aantalSimulaties);
        $sc->berekenDoelKans();
        $data['kansOpDoelvermogen'] = round($sc->doelKans,1);
      }
      if($extraLog)
        logIt($portefeuille. ' | stap 10 | Scenario berekening klaar. | '.(getmicrotime()-$beginTijd));
    }

    //omzet
    $query = "SELECT
SUM(abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Debet)+abs(Rekeningmutaties.Valutakoers*Rekeningmutaties.Credit)) as omzet
FROM
Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE 
Rekeningen.Portefeuille='$portefeuille' AND Rekeningmutaties.Boekdatum > '$startDatum' AND Rekeningmutaties.Boekdatum <= '$datum' AND Rekeningen.Memoriaal = 0 
AND Rekeningmutaties.Grootboekrekening='FONDS' AND
Rekeningmutaties.Transactietype IN('A','A/O','A/S','V','V/O','V/S') ";
			  $db->SQL($query);
			  $db->Query();
			  $totaal = $db->nextRecord();

  			$data['omzet'] = round($totaal['omzet'],2);
        $data['gemVermogen'] = round(($totaalWaarde['eind']+$totaalWaarde['begin'])/2,2);
        $data['omzetsnelheid'] = round($data['omzet']/$data['gemVermogen']*100,2);

      if($pRec['SpecifiekeIndex']<>'')
        $benchmarkRendement=getFondsPerformance($pRec['SpecifiekeIndex'],$startDatum,$datum);
      else
        $benchmarkRendement=0;
  
      if($extraLog)
        logIt($portefeuille. ' | stap 11 | Omzet en SpecifiekeIndex berekening klaar. | '.(getmicrotime()-$beginTijd));
  
  
  
      if($pRec['portefeuilleWaardeInclVkm']==1)
      {
        include_once($__appvar["basedir"]."/html/rapport/RapportVKM.php");
        $vkm=new RapportVKM(null,$portefeuille,$datum,$datum);
        $vkm->writeRapport();
        $data['vkmDoorlKst'] = round($vkm->vkmWaarde['vkmPercentagePortefeuille'],2);
        $data['vkmDirK'] = round($vkm->vkmWaarde['kostenPercentage'],2);
        $data['vkm'] = round($vkm->vkmWaarde['vkmWaarde'],2);
        if($extraLog)
          logIt($portefeuille. ' | stap 12 | VKM berekening klaar. | '.(getmicrotime()-$beginTijd));
      }
      else
      {
        $data['vkmDoorlKst'] = -9999;
        $data['vkmDirK'] = -9999;
        $data['vkm'] = -9999;
      }

    foreach($kosten as $veld=>$waarde)
    {
      if(in_array($veld,$dbFields))
        $data[$veld]=round($waarde,2);
    }
    foreach($opbrengsten as $veld=>$waarde)
    {
      if(in_array($veld,$dbFields))
        $data[$veld]=round($waarde,2);
    }



    $data['afmstdev']=round($afm['std'],2);
    $data['beginWaarde']=round($totaalWaarde['begin'],2);
    $data['laatsteWaarde']=round($totaalWaarde['eind'],2);
    $data['Stortingen']=round($stortingen,2);
    $data['Onttrekkingen']=round($onttrekkingen,2);
    $data['rendement'] = round($rendementProcent,2);
    $data['rendementMTD'] = round($rendementMTD,2);
    $data['rendementQTD'] = round($rendementQTD,2);
    $data['SignRapDatumRend'] = round($rendementRTD,2);
    $data['rendementModel'] = round($modelRendement,2);
    $data['ongerealiseerd'] = round($ongerealiseerd,2);
    $data['mutatieOpgelopenRente'] = round($opgelopenRente,2);
    $data['gerealiseerd'] = round($koersResultaat,2);
    $data['benchmarkRendement'] = round($benchmarkRendement,2);
    $data['saldoGeldrek'] = round($saldoGeldrekeningen,2);
    $data['rapportageDatum'] = $datum;
  
    $signalering=bepaalSignaleringen($__appvar["bedrijf"],$portefeuille,$data);
    $data['ptfSignMethode']=$signalering['periode'];
  
    if($extraLog)
      logIt($portefeuille. ' | stap 13 | ophalen signaleringsmethode klaar. | '.(getmicrotime()-$beginTijd));
      
    $laatstePortefeuilleWaardenPerPortefeuille[$portefeuille]=$data;

    verwijderTijdelijkeTabel($portefeuille,$startDatum);
    verwijderTijdelijkeTabel($portefeuille,$datum);
    }
    if($extraLog)
      logIt($portefeuille. ' | stap 14 | portefeuilleWaardeHerrekening klaar. | '.(getmicrotime()-$beginTijd));

  }

 // if($extraLog)
  logIt('Begin bijwerken van laatstePortefeuilleWaarde tabel. Aantal('.count($laatstePortefeuilleWaardenPerPortefeuille).')');
  $n=0;
  foreach($laatstePortefeuilleWaardenPerPortefeuille as $portefeuille=>$laatsteWaarde)
  {
    $sqlSet='';
    foreach($laatsteWaarde as $key=>$value)
      $sqlSet.="`$key` = '$value',";

    $query="SELECT id FROM $laatstePortefeuilleWaardeTabel WHERE portefeuille='".$portefeuille."'";
    if($db->QRecords($query) > 0)
      $query="UPDATE laatstePortefeuilleWaarde SET $sqlSet
      change_date=now() WHERE portefeuille='".$portefeuille."'";
    else
      $query="INSERT INTO $laatstePortefeuilleWaardeTabel SET $sqlSet
      change_date=now(),add_date=now(),portefeuille='".$portefeuille."'";
    $db->SQL($query);
    if($db->Query())
      $n++;
  }
 // if($extraLog)
   logIt('Klaar bijwerken van laatstePortefeuilleWaarde tabel. Aantal('.$n.')');

 // if($extraLog)
   logIt('Begin vulCrmLaatsteFondsWaarden.');
  foreach($fondswaardenPerPortefeuille as $portefeuille=>$fondsWaarden)
  {
    foreach($fondsWaarden as $index=>$fondsData)
    {
      $fondsWaarden[$index]['weging']=($fondsData['actuelePortefeuilleWaardeEuro'] / $laatstePortefeuilleWaardenPerPortefeuille[$portefeuille]['laatsteWaarde'] * 100);
    }
    vulCrmLaatsteFondsWaarden($fondsWaarden, $portefeuille, $datum, $crmLaatsteFondsWaardenTabel);
  }
  //if($extraLog)
    logIt('Klaar vulCrmLaatsteFondsWaarden.');

  $query="UPDATE $laatstePortefeuilleWaardeTabel JOIN Portefeuilles ON $laatstePortefeuilleWaardeTabel.portefeuille=Portefeuilles.Portefeuille 
SET $laatstePortefeuilleWaardeTabel.change_date=now(),$laatstePortefeuilleWaardeTabel.change_user='END' $queryZeroSet
WHERE Portefeuilles.Einddatum < '$datum' ";
  $db->SQL($query);
  $db->Query();
  //$query="DELETE  $crmLaatsteFondsWaardenTabel FROM $crmLaatsteFondsWaardenTabel JOIN Portefeuilles ON $crmLaatsteFondsWaardenTabel.portefeuille=Portefeuilles.Portefeuille WHERE Portefeuilles.Einddatum < now()";
  $query="DELETE FROM $crmLaatsteFondsWaardenTabel WHERE $crmLaatsteFondsWaardenTabel".".add_date  < now() - interval 2 day ";
  $db->SQL($query);
  $db->Query();
  $query="DELETE FROM $laatstePortefeuilleWaardeTabel WHERE $laatstePortefeuilleWaardeTabel".".change_date  < now() - interval 2 day ";
  $db->SQL($query);
  $db->Query();
 // if($extraLog)
  logIt('Klaar met verwijderen oude records.');
}

function bepaalRendementsBerekeningDetails($portefeuille,$datumBegin,$datumEind)
{
  global $__appvar;
  $DB=new DB();

  $fondswaarden['a'] =  berekenPortefeuilleWaarde($portefeuille, $datumBegin,((substr($datumBegin,5,5)=='01-01')?true:false),'EUR',$datumBegin);
  $fondswaarden['b'] =  berekenPortefeuilleWaarde($portefeuille, $datumEind,((substr($datumEind,5,5)=='01-01')?true:false),'EUR',$datumBegin);

  vulTijdelijkeTabel($fondswaarden['a'] ,$portefeuille,$datumBegin);
  vulTijdelijkeTabel($fondswaarden['b'] ,$portefeuille,$datumEind);

  $totaalWaarde=array();
  foreach ($fondswaarden['a'] as $regel)
  {
    $totaalWaarde['begin'] += $regel['actuelePortefeuilleWaardeEuro'];
    if($regel['type']=='rente' && $regel['fonds'] != '')
      $totaalWaarde['renteBegin'] += $regel['actuelePortefeuilleWaardeEuro'];
  }
  $saldoGeldrekeningen=0;

  foreach ($fondswaarden['b'] as $regel)
  {
    $totaalWaarde['eind'] += $regel['actuelePortefeuilleWaardeEuro'];
    if($regel['type']=='fondsen')
    {
      $totaalWaarde['beginResultaat'] += $regel['beginPortefeuilleWaardeEuro'];
      $totaalWaarde['eindResultaat'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    elseif($regel['type']=='rente' && $regel['fonds'] != '')
    {
      $totaalWaarde['renteEind'] += $regel['actuelePortefeuilleWaardeEuro'];
    }
    elseif($regel['type']=='rekening')
    {
      $saldoGeldrekeningen+= $regel['actuelePortefeuilleWaardeEuro'];
    }
  }


  $ongerealiseerd=($totaalWaarde['eindResultaat']-$totaalWaarde['beginResultaat']);
  $gerealiseerdKoersresultaat=round(gerealiseerdKoersresultaat($portefeuille,$datumBegin,$datumEind,'EUR',true),2);
  $waardeMutatie = $totaalWaarde['eind'] - $totaalWaarde['begin'];
  $stortingen = getStortingen($portefeuille,$datumBegin, $datumEind);
  $onttrekkingen = getOnttrekkingen($portefeuille,$datumBegin, $datumEind);
  $resultaatVerslagperiode = round($waardeMutatie - $stortingen + $onttrekkingen,2);

  $query = "SELECT Grootboekrekeningen.Grootboekrekening,Grootboekrekeningen.Omschrijving, round(SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers),2)  AS kosten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Boekdatum <= '$datumEind' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Kosten = '1'
              GROUP BY Grootboekrekeningen.Grootboekrekening";
  $DB->SQL($query);
  $DB->Query();
  $kosten=array();
  $kostenTotaal=0;
  while($data=$DB->nextRecord())
  {
    $kosten[$data['Omschrijving']]+=$data['kosten'];
    $kostenTotaal+=$data['kosten'];
   // $kosten['Kosten']+=$data['kosten'];
  }

  $query = "SELECT Grootboekrekeningen.Grootboekrekening,Grootboekrekeningen.Omschrijving, round(SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers)-SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers),2) AS  opbrengsten
              FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen
              WHERE
              Rekeningmutaties.Rekening = Rekeningen.Rekening AND
              Rekeningen.Portefeuille = '$portefeuille' AND
              Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND
              Rekeningmutaties.Verwerkt = '1' AND
              Rekeningmutaties.Boekdatum > '$datumBegin' AND Rekeningmutaties.Boekdatum <= '$datumEind' AND
              Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND
              Grootboekrekeningen.Opbrengst = '1'
              GROUP BY Grootboekrekeningen.Grootboekrekening";
  $DB->SQL($query);
  $DB->Query();
  $opbrengsten=array();
  $opbrengstenTotaal=0;
  while($data=$DB->nextRecord())
  {
    $opbrengsten[$data['Omschrijving']]=$data['opbrengsten'];
    $opbrengstenTotaal+=$data['opbrengsten'];
  }
  $opgelopenRente=$totaalWaarde['renteEind']-$totaalWaarde['renteBegin'];
  $valutaResultaat=round($resultaatVerslagperiode-($gerealiseerdKoersresultaat+$ongerealiseerd+$opbrengstenTotaal+$kostenTotaal+$opgelopenRente),2);
  $opbrengstenTotaal+=$ongerealiseerd;
  $opbrengstenTotaal+=$gerealiseerdKoersresultaat;
  $opbrengstenTotaal+=$opgelopenRente;
  $opbrengstenTotaal+=$valutaResultaat;

  $query = "SELECT ".
    "SUM(((TO_DAYS('".$datumEind."') - TO_DAYS(Rekeningmutaties.Boekdatum)) / (TO_DAYS('".$datumEind."') - TO_DAYS('".$datumBegin."')) ".
    "  * ((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ) ))) AS totaal1, ".
    "SUM((ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers ) - (ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers ))  AS totaal2 ".
    "FROM  (Rekeningen, Portefeuilles)
	Left JOIN  Rekeningmutaties on Rekeningmutaties.Rekening = Rekeningen.Rekening ".
    "WHERE ".
    "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
    "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
    "Rekeningmutaties.Verwerkt = '1' AND ".
    "Rekeningmutaties.Boekdatum > '".$datumBegin."' AND ".
    "Rekeningmutaties.Boekdatum <= '".$datumEind."' AND ".
    "Rekeningmutaties.Grootboekrekening IN (SELECT Grootboekrekening FROM Grootboekrekeningen WHERE Grootboekrekeningen.Storting=1 OR Grootboekrekeningen.Onttrekking=1)";
  $DB->SQL($query);
  $DB->Query();
  $weging = $DB->NextRecord();


  $gemiddelde = $totaalWaarde['begin'] + $weging['totaal1'];
  $performance=0;
  if($gemiddelde <> 0)
    $performance = round(((($totaalWaarde['eind'] - $totaalWaarde['begin']) - $weging['totaal2']) / $gemiddelde) * 100,4);

  $data=array('datumBegin'=>$datumBegin,'datumEind'=>$datumEind,'beginwaarde'=>$totaalWaarde['begin'],'eindwaarde'=>$totaalWaarde['eind'],'mutatiewaarde'=>($totaalWaarde['eind']-$totaalWaarde['begin']),
              'stortingen'=>$stortingen,'onttrekkingen'=>$onttrekkingen,'resultaat'=>$resultaatVerslagperiode,'performanceMD'=>$performance,'ongerealiseerd'=>$ongerealiseerd,'gerealiseerdKoersresultaat'=>$gerealiseerdKoersresultaat,
              'opgelopenRente'=>$opgelopenRente,'valutaResultaat'=>$valutaResultaat,'opbrengstenTotaal'=>$opbrengstenTotaal,'kostenTotaal'=>$kostenTotaal,'opbrengsten'=>$opbrengsten,'kosten'=>$kosten);
  return $data;

}




function bepaalSignaleringenStortingen($bedrijf,$portefeuille='')
{
  global $USR;
  $db=new DB();
  $db2=new DB();
  $portefeuilleWhere='';
  if($portefeuille<>'')
  {
    $portefeuilleWhere="AND Portefeuilles.portefeuille='$portefeuille'";
  }
  
  $emailAddesses=getEmailAdressen('signaleringen');
  $logEmail=implode(";",$emailAddesses);
  if(count($emailAddesses)==0)
    $logEmail='geen';

  $query="SELECT
Portefeuilles.Portefeuille,
Portefeuilles.Client,
RM.Rekening,
RM.id,
RM.Omschrijving,
RM.Boekdatum,
RM.Valuta,
RM.Valutakoers,
RM.Credit,
RM.Bedrag,
round((RM.Credit-RM.Debet)*RM.Valutakoers,2) AS EURbedrag,
Vermogensbeheerders.bedragTransactiesignalering
FROM
Rekeningmutaties AS RM
INNER JOIN Rekeningen ON RM.Rekening = Rekeningen.Rekening
INNER JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille
INNER JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
INNER JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
where Rekeningen.Consolidatie = 0 and Grootboekrekening IN ('STORT','ONTTR') AND
Rekeningen.Memoriaal=0 AND Fonds='' AND abs(round((RM.Credit-RM.Debet)*RM.Valutakoers,2)) > Vermogensbeheerders.bedragTransactiesignalering AND RM.add_date> date(now()) - interval 30 DAY
AND VermogensbeheerdersPerBedrijf.Bedrijf='$bedrijf' $portefeuilleWhere AND Vermogensbeheerders.bedragTransactiesignalering>0";
  $db->SQL($query);
  $db->query();
  $logPerEmail=array();
  $signaleringsmail=array();

  while($data=$db->nextRecord())
  {
      $query="SELECT id,status,bedrag FROM signaleringStortingen WHERE portefeuille='".$data['Portefeuille']."' AND rekeningmutatieId='".$data['id']."'"; //"  AND status <> '2' order by datum desc";
      $db2->SQL($query);
      $db2->query();
      $recordData=$db2->nextRecord();
      if($recordData['id']<1)
      {
        $signaleringsmail[$logEmail]=true;
        $query="INSERT INTO signaleringStortingen SET portefeuille='".$data['Portefeuille']."', datum='".$data['Boekdatum']."',rekeningmutatieId='".$data['id']."', bedrag='".$data['EURbedrag']."', change_date=now(),add_date=now(),change_user='$USR',add_user='$USR'";
        $db2->SQL($query);
        if($db2->query())
          $logPerEmail[$logEmail].="Portefeuille '".$data['Portefeuille']."' (client: ".$data['Client'].") aan signaleringStortingen tabel toegevoegd met storting van ".$data['EURbedrag']." op ".$data['Boekdatum'].".<br>\n";
        else
          $logPerEmail[$logEmail].="Toevoegen van portefeuille '".$data['Portefeuille']."' (client: ".$data['Client'].") aan signaleringStortingen tabel met storting van ".$data['EURbedrag']." op ".$data['Boekdatum']." mislukt.<br>\n";
      }
      else
      {
        $statusDetail=array(0=>'Nieuw',1=>'Per mail Airs',2=>'Verwijderd',3=>'Zelf verstuurd');
        if (round($recordData['bedrag']) <> round($data['EURbedrag']))
        {
          $logPerEmail[$logEmail] .= "Portefeuille '" . $data['Portefeuille'] . "' (client: " . $data['Client'] . ") heeft al een record met status (" . $statusDetail[$recordData['status']] . ") in de signaleringStortingen tabel. Bedrag is aangepast van ".round($recordData['bedrag'],0)." naar ".round($data['EURbedrag'],0).". <br>\n";
          $query="UPDATE signaleringStortingen SET bedrag='".$data['EURbedrag']."' WHERE id='".$recordData['id']."'";
          $db2->SQL($query);
          $db2->query();
          $signaleringsmail[$logEmail]=true;
        }
        elseif($recordData['status']==0)
        {
          $logPerEmail[$logEmail] .= "Portefeuille '" . $data['Portefeuille'] . "' (client: " . $data['Client'] . ") heeft al een record met status (" . $statusDetail[$recordData['status']] . ") in de signaleringStortingen tabel voor ".$data['EURbedrag']." op ".$data['Boekdatum'].".<br>\n";
        }
      }
  }
  //return array($logPerEmail);
  foreach($logPerEmail as $emailAdres=>$logTxt)
  {
    if ($signaleringsmail[$emailAdres] == true && $emailAdres <>'geen')
    {
      $emailAddesses=explode(";",$emailAdres);
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = 'info@airs.nl';
      $mail->FromName = "Airs";
      $mail->Body    = $logTxt;
      $mail->AltBody = html_entity_decode(strip_tags($logTxt));
      foreach ($emailAddesses as $emailadres)
        $mail->AddAddress($emailadres,$emailadres);
      
      $mail->Subject = "Signalering Stortingen/Onttrekkingen ".date("d-m-Y H:i");
      if(!$mail->Send())
        echo "Verzenden van e-mail mislukt.";
    }
  }

//  logIt('BepaalSignaleringen klaar.');
//  $query="SELECT portefeuille FROM laatstePortefeuilleWaarde ";
  
  return array();
}

function bepaalSignaleringen($bedrijf,$portefeuille='',$laatstePortefeuilleWaarde=array())
{
  global $USR;
  $db=new DB();
  $db2=new DB();
  $filterWhere='';
  if($portefeuille<>'')
  {
    $filterWhere="AND Portefeuilles.portefeuille='$portefeuille'";
  }
  else
  {
    $filterWhere="AND laatstePortefeuilleWaarde.change_date > now() - interval 1 hour";
  }
  $emailAddesses=getEmailAdressen('signaleringen');

  $query = "SELECT laatstePortefeuilleWaarde.portefeuille, laatstePortefeuilleWaarde.rapportageDatum, laatstePortefeuilleWaarde.rendementQTD,laatstePortefeuilleWaarde.rendementMTD,laatstePortefeuilleWaarde.SignRapDatumRend,
Vermogensbeheerders.standaardRapportageFreq,
CRM_naw.rapportageVinkSelectie,CRM_naw.laatsteRapDatumSignalering, Vermogensbeheerders.emailSignaleringen,Portefeuilles.Client
            FROM Portefeuilles
	JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
	JOIN VermogensbeheerdersPerBedrijf ON VermogensbeheerdersPerBedrijf.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
	LEFT JOIN laatstePortefeuilleWaarde ON laatstePortefeuilleWaarde.portefeuille = Portefeuilles.Portefeuille
	LEFT JOIN CRM_naw ON laatstePortefeuilleWaarde.portefeuille = CRM_naw.portefeuille
            WHERE VermogensbeheerdersPerBedrijf.Bedrijf = '$bedrijf' $filterWhere AND Portefeuilles.Einddatum>now() AND Vermogensbeheerders.standaardRapportageFreq <> ''";
  $db->SQL($query);
  $db->query();
  $triggerRendement=-10;
  $logPerEmail=array();
  $signaleringsmail=array();
  $valutaDatum=getLaatsteValutadatum();

  while($data=$db->nextRecord())
  {
    if($portefeuille <> '' && $portefeuille==$data['portefeuille'] && count($laatstePortefeuilleWaarde)>0)
    {
      $data=array_merge($data,$laatstePortefeuilleWaarde);
    }
    $rapFreq=$data['standaardRapportageFreq'];
    if($rapFreq=='c')
    {
      $vinkSelectie=unserialize($data['rapportageVinkSelectie']);
      foreach($vinkSelectie['verzending'] as $periode=>$verzendMethoden)
      {
        $periodeParts=explode('_',$periode);
        if($periodeParts[1]=='m' && count($verzendMethoden)>0 && $verzendMethoden['geen']==0)
        {
          $rapFreq = 'm';
          break;
        }
        if($periodeParts[1]=='k' && count($verzendMethoden)>0 && $verzendMethoden['geen']==0)
          $rapFreq = 'k';
      }
    }

    if($rapFreq == 'c')
      $rapFreq = 'k';

    $jaar=substr( $data['rapportageDatum'],0,4);
    $maand=substr( $data['rapportageDatum'],5,2);
    
    $logEmail=implode(";",$emailAddesses);
    if(count($emailAddesses)==0)
      $logEmail='geen';

    if($rapFreq=='k')
    {
      $rendement = $data['rendementQTD'];
      $periode='QTD';
      $beginDatum = date('Y-m-d', mktime(0, 0, 0, ceil($maand / 3) * 3 - 2, 0, $jaar));
    }
    elseif($rapFreq=='m')
    {
      $rendement = $data['rendementMTD'];
      $periode='MTD';
      $beginDatum = date('Y-m-d', mktime(0, 0, 0, $maand, 0, $jaar));
    }
    elseif($rapFreq=='r')
    {
      $rendement = $data['SignRapDatumRend'];
      $periode='RAP';
      if(db2jul($data['laatsteRapDatumSignalering']) > 1)
      {
        if(substr($data['laatsteRapDatumSignalering'],5,5)=='12-31')
          $beginDatum=date('Y-m-d',db2jul($data['laatsteRapDatumSignalering'])+86400);
        else
          $beginDatum = $data['laatsteRapDatumSignalering'];
      }
      else
      {
        $rendement = $data['rendementQTD'];
        $periode='QTD';
        $beginDatum = date('Y-m-d', mktime(0, 0, 0, ceil($maand / 3) * 3 - 2, 0, $jaar));
      }
    }
    else
    {
      $beginDatum='';
      $rendement = 0;
      $periode='';
    }
    
    if($portefeuille<>'')
    {
      //logit("bepaalSignaleringen enkel $portefeuille $periode $beginDatum -> $valutaDatum => $rendement < $triggerRendement rapDatum: ".$data['rapportageDatum']);
      return array('rendement' => $rendement, 'periode' => $periode, 'beginDatum' => $beginDatum);
    }
    else
    {
      logit("bepaalSignaleringen ".$data['portefeuille']." $periode $beginDatum -> $valutaDatum => $rendement < $triggerRendement rapDatum: ".$data['rapportageDatum']);
    }
    
    if($data['portefeuille'] <> '' && $rendement < $triggerRendement && substr($data['rapportageDatum'],0,10) == substr($valutaDatum,0,10))
    {
      $query="SELECT id,status,signaleringsPercentage,add_date FROM signaleringPortRend
  WHERE portefeuille='".$data['portefeuille']."' AND datum>='$beginDatum' AND datum<='".$data['rapportageDatum']."'  AND status <> '2' order by datum desc";
      $db2->SQL($query);
      $db2->query();
      $recordData=$db2->nextRecord();

      if($recordData['id']<1 || ($recordData['id'] > 0 && ((ceil($recordData['signaleringsPercentage']/10)*10)-$rendement > 10))) // nog niet aanwezig voor deze periode of een nieuw 10-tal.
      {
        $details=bepaalRendementsBerekeningDetails($data['portefeuille'],$beginDatum,$data['rapportageDatum']);
        $details['signaleringsPercentage']=$rendement;
        $signaleringsmail[$logEmail]=true;
        $query="INSERT INTO signaleringPortRend SET portefeuille='".$data['portefeuille']."', periode='$periode',signaleringsPercentage='$rendement',datum='".$data['rapportageDatum']."', 
        rendementDetails='".mysql_real_escape_string(serialize($details))."', change_date=now(),add_date=now(),change_user='$USR',add_user='$USR'";
        $db2->SQL($query);
        if($db2->query())
          $logPerEmail[$logEmail].="Portefeuille '".$data['portefeuille']."' (client: ".$data['Client'].") aan signaleringPortRend tabel toegevoegd voor periode $beginDatum -> ".$data['rapportageDatum']." met $periode rendement van $rendement.<br>\n";
        else
          $logPerEmail[$logEmail].="Toevoegen van portefeuille '".$data['portefeuille']."' (client: ".$data['Client'].") aan signaleringPortRend tabel voor periode $beginDatum -> ".$data['rapportageDatum']." met $periode rendement van $rendement mislukt.<br>\n";
        logit("Portefeuille '".$data['portefeuille']."' (client: ".$data['Client'].") aan signaleringPortRend tabel toegevoegd voor periode $beginDatum -> ".$data['rapportageDatum']." met $periode rendement van $rendement.");
      }
      else
      {
        logit('Signalering aanwezig met status '.$recordData['status']);
        $statusDetail=array(0=>'Nieuw',1=>'Per mail Airs',2=>'Verwijderd',3=>'Zelf verstuurd');
        if($recordData['status']==0)
        {
          $signaleringsmail[$logEmail]=true;
          $logPerEmail[$logEmail] .= "Portefeuille '" . $data['portefeuille'] . "' (client: " . $data['Client'] . ") heeft al een record met status (" . $statusDetail[$recordData['status']] . ") in de signaleringPortRend tabel per ".date('d-m-Y',db2jul($recordData['add_date'])).". Het huidige rendement is $rendement voor de huidige periode $beginDatum -> " . $data['rapportageDatum'] . " .<br>\n";
        }
      }
    }
    else
    {
      if(substr($data['rapportageDatum'],0,10) <> substr($valutaDatum,0,10))
        logIt('Portefeuille '.$data['portefeuille'].' overgeslagen voor signaleringen omdat de rapportagedatum('.$data['rapportageDatum'].') niet overeenkomt met laatste valutadatum('.$valutaDatum.')');
    }
  }


  foreach($logPerEmail as $emailAdres=>$logTxt)
  {
    if ($signaleringsmail[$emailAdres] == true && $emailAdres <>'geen')
    {
      $emailAddesses=explode(";",$emailAdres);
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = 'info@airs.nl';
      $mail->FromName = "Airs";
      $mail->Body    = $logTxt;
      $mail->AltBody = html_entity_decode(strip_tags($logTxt));
      foreach ($emailAddesses as $emailadres)
        $mail->AddAddress($emailadres,$emailadres);

      $mail->Subject = "Rendement signalering ".date("d-m-Y H:i");
      if(!$mail->Send())
        echo "Verzenden van e-mail mislukt.";
    }
  }

//  logIt('BepaalSignaleringen klaar.');
//  $query="SELECT portefeuille FROM laatstePortefeuilleWaarde ";

  return array();
}

function getFondsPerformanceGestappeld2($fonds,$portefeuille,$beginDatum,$eindDatum,$stapeling='maanden',$portefeuilleVerdelingOpDatum=false,$uitsplitsingFonds=true,$returnObject=false,$verdeling='benchmark')
{
  global $__appvar;
  include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening2.php");
  $stdev=new rapportSDberekening2($portefeuille,$eindDatum,1);
  $stdev->setStartdatum($beginDatum);
  $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
  $stdev->settings['SdOpbouw']=0;
  if($stapeling=='maanden')
    $stdev->settings['SdFrequentie']='m';
  elseif($stapeling=='jaar')
    $stdev->settings['SdFrequentie']='j';
  $stdev->addReeks($verdeling, $fonds,$portefeuilleVerdelingOpDatum,$uitsplitsingFonds);
  $stdev->berekenWaarden();
  $benchmarkJaarPercentage=$stdev->getReeksRendement($verdeling);
//listarray($stdev);
 //listarray($benchmarkJaarPercentage);exit;
  if($returnObject==true)
    return $stdev;
  else
    return $benchmarkJaarPercentage;
}

function getFondsPerformanceGestappeld($fonds,$portefeuille,$beginDatum,$eindDatum,$stapeling='maanden',$verdelingOpDatum=true)
{
  global $__appvar;
  include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
  $stdev=new rapportSDberekening($portefeuille,$eindDatum,1);
  $stdev->setStartdatum($beginDatum);
  $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
  $stdev->addReeks('benchmark', $fonds,$verdelingOpDatum);
  
  $stdev->berekenWaarden();
  $benchmarkJaarPercentage=$stdev->getReeksRendement('benchmark');
  return $benchmarkJaarPercentage;

  $index=new indexHerberekening();
  $perioden=array();
  if($stapeling=='maanden')
    $perioden=$index->getMaanden(db2jul($beginDatum),db2jul($eindDatum));
  $perf=0;

  foreach($perioden as $periode)
  {
    $tmp=getFondsPerformance($fonds, $periode['start'], $periode['stop']);
    $perf=((1+$perf)*(1+$tmp/100))-1;
  }
  return $perf*100;
}

function getFondsPerformance($fonds,$beginDatum,$eindDatum,$debug=false)
{
  if(is_array($fonds))
  {
    $perf=0;
    foreach($fonds as $fondsDetail=>$percentage)
    {
      $beginKoers = globalGetFondsKoers($fondsDetail, $beginDatum);
      $eindKoers = globalGetFondsKoers($fondsDetail, $eindDatum);

      $perf += ($eindKoers - $beginKoers) / ($beginKoers) *$percentage;
      if($debug==true)
      {
        echo (($eindKoers - $beginKoers) / ($beginKoers) *$percentage)." = ($eindKoers - $beginKoers) / ($beginKoers) *$percentage <br>\n";
      }
     // echo "$beginDatum->$eindDatum  $fondsDetail ".(($eindKoers - $beginKoers) / ($beginKoers) )."  | $percentage;<br>\n";
     // echo "$eindDatum $fondsDetail |  som=$perf |  ".(($eindKoers - $beginKoers) / ($beginKoers) *$percentage)." = ($eindKoers - $beginKoers) / ($beginKoers) *$percentage;<br>\n";
    }
  }
  else
  {
    $beginKoers = globalGetFondsKoers($fonds, $beginDatum);
    $eindKoers = globalGetFondsKoers($fonds, $eindDatum);
    $perf = ($eindKoers - $beginKoers) / ($beginKoers / 100);
  }
  if($debug==true)
  {
    echo $perf . "<br>\n";
  }
  return $perf;
}

function getFondsverdeling($fonds)
{
  $DB=new DB();
  $query = "SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
  $DB->SQL($query);
  $DB->Query();
  $verdeling=array();
  if ($DB->records() == 0)
  {
    $verdeling[$fonds] = 100;
  }
  else
  {
    while ($data = $DB->nextRecord())
    {
      if (isset($verdeling[$data['fonds']]))
      {
        $verdeling[$data['fonds']] += $data['percentage'] ;
      }
      else
      {
        $verdeling[$data['fonds']] = $data['percentage'] ;
      }
    }
  }
  return $verdeling;
}

function getSpecifiekeIndex($portefeuille,$eindDatum)
{
  return  getPortefeuilleHistorischeParameters($portefeuille,$eindDatum,'specifiekeIndex');
}

function getPortefeuilleHistorischeParameters($portefeuille,$eindDatum,$field='specifiekeIndex')
{
  $eindJul=adodb_db2jul($eindDatum);
  $db=new DB();

  $query="SELECT waarde as $field,tot FROM PortefeuilleHistorischeParameters WHERE portefeuille='$portefeuille' AND veld='$field' ORDER BY tot";
  $db->SQL($query);
  $db->query();
  while($data=$db->nextRecord())
  {
    $data['totJul']=adodb_db2jul($data['tot']);
    if($data['totJul']>=$eindJul)
      return $data[$field];
  }

  $query="SELECT $field,einddatum FROM Portefeuilles WHERE portefeuille='$portefeuille'";
  $db->SQL($query);
  $index=$db->lookupRecord();
  return $index[$field];
}

function getSpecifikeIndexPerformance($portefeuille,$beginDatum,$eindDatum,$stapeling='maanden',$fondsDetails=false)
{
  $beginJul=adodb_db2jul($beginDatum);
  $eindJul=adodb_db2jul($eindDatum);
  $db=new DB();
  $query="SELECT specifiekeIndex,einddatum FROM Portefeuilles WHERE portefeuille='$portefeuille'";
  $db->SQL($query);
  $index=$db->lookupRecord();
  if($fondsDetails==true)
    $index['specifiekeIndex']=getFondsverdeling($index['specifiekeIndex']);
  $index['totJul']=adodb_db2jul($index['einddatum']);

  $query="SELECT waarde as specifiekeIndex,tot FROM PortefeuilleHistorischeParameters WHERE portefeuille='$portefeuille' AND veld='SpecifiekeIndex' ORDER BY tot";
  $db->SQL($query);
  $db->query();
  $indexTot=array();
  while($data=$db->nextRecord())
  {
    if($fondsDetails==true)
      $data['specifiekeIndex']=getFondsverdeling($data['specifiekeIndex']);
    $data['totJul']=adodb_db2jul($data['tot']);
    $indexTot[$data['totJul']]=$data;
  }
  $indexTot[$index['totJul']]=$index;

  if($stapeling=='maanden')
  {
    $index = new indexHerberekening();
    $stapelPerioden = array();
    if ($stapeling == 'maanden')
    {
      $stapelPerioden = $index->getMaanden(db2jul($beginDatum), db2jul($eindDatum));
    }
  }
  else
    $stapelPerioden=array('start'=>$beginDatum,'stop'=>$eindDatum);

  $perioden=array();
  foreach($stapelPerioden as $stapelPeriode)
  {
    $beginJul=db2jul($stapelPeriode['start']);
    $eindJul=db2jul($stapelPeriode['stop']);
    foreach ($indexTot as $totJul => $data)
    {
      if ($totJul >= $eindJul)
      {
        $perioden[] = array('fonds' => $data['specifiekeIndex'], 'vanaf' => date('Y-m-d', $beginJul), 'tot' => date('Y-m-d', $eindJul));
        break;
      }
      else
      {
       // if($beginJul <> $totJul)
        //  $perioden[] = array('fonds' => $data['specifiekeIndex'], 'vanaf' => date('Y-m-d', $beginJul), 'tot' => date('Y-m-d', $totJul));
       // $beginJul = $totJul;
      }
    }
  }
//  listarray($perioden);
  $perfTotaal=1;
  foreach($perioden as $periode)
  {
    $perf = getFondsPerformance($periode['fonds'], $periode['vanaf'], $periode['tot']);
   // listarray($periode['fonds']);
   // echo  $periode['tot']."  |  $perf<br>\n";
    $perf=1+($perf/100);
    $perfTotaal=(((1+($perfTotaal/100))*$perf)-1)*100;

  }
  $perfTotaal=$perfTotaal-1;
//echo "$perfTotaal <br>\n";
    return $perfTotaal;
}

function globalGetFondsKoers($fonds,$datum)
{
  $db=new DB();
  $query="SELECT Koers FROM Fondskoersen WHERE Fonds='$fonds' AND Datum <= '$datum' order by Datum desc limit 1";
  $db->SQL($query);
  $koers=$db->lookupRecord();
  return $koers['Koers'];
}

function globalGetValutaKoers($valuta,$datum)
{
  $db=new DB();
  $query="SELECT Koers FROM Valutakoersen WHERE Valuta='$valuta' AND Datum <= '$datum' order by Datum desc limit 1";
  $db->SQL($query);
  $koers=$db->lookupRecord();
  return $koers['Koers'];
}

function vulCrmLaatsteFondsWaarden ($data, $portefeuille, $datum,$tabel='crmLaatsteFondsWaarden')
{
	global $USR;
	$DB = new DB();
	$query = "DELETE FROM $tabel WHERE portefeuille = '".$portefeuille."'";
  $DB->SQL($query);
	$DB->Query();

  foreach($data as $regel)
  {
    unset($regel['voorgaandejarenActief']);
    $query = "INSERT INTO $tabel SET ";
    foreach ($regel as $key => $value)
    {
      $query .= "$key='" . mysql_real_escape_string($value) . "',";
    }
    $query .= " rapportageDatum='" . $datum . "', portefeuille='" . $portefeuille . "',add_date=NOW(),add_user='$USR'";
    $DB->SQL($query);
    if (!$DB->Query())
    {
      $_error = true;
    }
  }
	if(!empty($_error))
    return false;
	return true;
}

 	function OutputEmailXls($data,$filename)
	{
	  include_once('../classes/excel/Writer.php');
	  $workbook = new Spreadsheet_Excel_Writer($filename);

    $worksheet =& $workbook->addWorksheet();
	   for($regel = 0; $regel < count($data); $regel++ )
	   {
		   for($col = 0; $col < count($data[$regel]); $col++)
		   {
		       $worksheet->write($regel, $col, $data[$regel][$col]);
		   }
	   }
	   $workbook->close();
	}

function fondsenCheck($nieuweFondsen)
{
  global $__appvar;
  $db=new DB();
  $query="SELECT max(fondsenmeldingEmail) as fondsenmeldingEmail FROM Vermogensbeheerders";
  $db->SQL($query);
  $check=$db->lookupRecord();

  $overslaan=array('action','updateScript');

  if($check['fondsenmeldingEmail'])
  {
    $overslaan=array('id','key_Fonds','AABCode','ABRCode','aabbeCode','stroeveCode','snsCode','snsSecCode','TGBCode','binckCode','raboCode',
    'Garantiepercentage','FondsOverslaanInValutaRisico','valutaRisicoPercentage','EindDatum','Beurs','bbLandcode','koersControle',
    'HeeftOptie','optieCode','Huisfonds','Portefeuille','lossingskoers','forward','forwardReferentieKoers','forwardAfloopDatum');
    $body="<table>\n<tr><td><b>Veld</b></td><td><b>Waarde</b></td></tr>\n";
    foreach($nieuweFondsen as $data)
    {
      $body.="<tr><td><b>Fonds</b></td><td><b>".$data['Fonds']."</b></td></tr>\n";
      foreach ($data as $key=>$value)
      {
        if(!in_array($key,$overslaan))
          $body.="<tr><td>$key </td><td>$value</td></tr>\n";
      }
      $query="SELECT Fonds,Datum,Rentepercentage FROM Rentepercentages WHERE Fonds = '".$data['Fonds']."'";
      $db->SQL($query);
      $db->Query();
      $body.="<tr><td><b>Rentepercentages</b></td><td>".$data['Fonds']."</td></tr>\n";
      $body.="<tr><td>Datum</td><td>Rentepercentage</td></tr>\n";
      while($dbData=$db->nextRecord())
        $body.="<tr><td>".$dbData['Datum']."</td><td>".$dbData['Rentepercentage']."</td></tr>\n";


      $query="SELECT Vermogensbeheerder,Beleggingscategorie,afmCategorie FROM BeleggingscategoriePerFonds WHERE Fonds = '".$data['Fonds']."' ORDER BY Vermogensbeheerder";
      $db->SQL($query);
      $db->Query();
      $body.="<tr><td><b>BeleggingscategoriePerFonds</b></td><td>".$data['Fonds']."</td></tr>\n";
      while($dbData=$db->nextRecord())
      {
        $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>Beleggingscategorie:".$dbData['Beleggingscategorie']."</td></tr>\n";
        if($dbData['afmCategorie'] <> '')
          $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>afmCategorie:".$dbData['afmCategorie']."</td></tr>\n";
      }
      $query="SELECT Vermogensbeheerder,Beleggingssector,Regio,AttributieCategorie FROM BeleggingssectorPerFonds WHERE Fonds = '".$data['Fonds']."' ORDER BY Vermogensbeheerder";
      $db->SQL($query);
      $db->Query();
      $body.="<tr><td><b>BeleggingssectorPerFonds</b></td><td>".$data['Fonds']."</td></tr>\n";
      while($dbData=$db->nextRecord())
      {
        $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>Beleggingssector:".$dbData['Beleggingssector']."</td></tr>\n";
        if($dbData['Regio'] <> '')
          $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>Regio:".$dbData['Regio']."</td></tr>\n";
        if($dbData['AttributieCategorie'] <> '')
          $body.="<tr><td>".$dbData['Vermogensbeheerder']."</td><td>AttributieCategorie:".$dbData['AttributieCategorie']."</td></tr>\n";
      }
      $body.="<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
    }
    $body.="<tr><td colspan=2>Verzonden om: ".date("d-m-Y H:i")." </td></tr>\n</table>";

    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');

    storeControleMail('NieuweFondsen',"Nieuwe fondsen ".date("d-m-Y H:i"),$body);
    $emailAddesses=getEmailAdressen('nieuweFondsen');
    if (count($emailAddesses)>0 && $mailserver !='')
    {
      //$emailAddesses=explode(";",$check['fondsenmeldingEmail']);
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = $emailAddesses[0];
      $mail->FromName = "Airs";
      $mail->Body    = $body;
      $mail->AltBody = html_entity_decode(strip_tags($body));
      foreach ($emailAddesses as $emailadres)
        $mail->AddAddress($emailadres,$emailadres);
      $mail->Subject = "Nieuwe fondsen ".date("d-m-Y H:i");
      $mail->Host=$mailserver;
      if(!$mail->Send())
        echo "Verzenden van e-mail mislukt.";
    }
  }

}


function transactieCheck($nieuweTransacties)
{
  global $__appvar;
  $cfg=new AE_config();
  $mailserver=$cfg->getData('smtpServer');
  $xlsData=array();
  $html='';

  $db=new DB();
  $query="SELECT 
  max(transactiemeldingEmail) as transactiemeldingEmail, 
  max(transactiemeldingWaarde) as transactiemeldingWaarde, 
  max(OrderCheck) as OrderCheck,
  max(transactieMeldingType) as transactieMeldingType
  FROM Vermogensbeheerders";
  $db->SQL($query);
  $check=$db->lookupRecord();
  
  if($check['transactiemeldingEmail'])
  {

    if($check['transactieMeldingType'])
    {
      define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
      include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");
      include_once($__appvar["basedir"]."/classes/portefeuilleSelectieClass.php");
      include_once($__appvar["basedir"]."/html/rapport/PDFOverzicht.php");
      include_once($__appvar["basedir"]."/html/rapport/Transactieoverzicht.php");
      $trans=new Transactieoverzicht();
      $trans->writeRapport($nieuweTransacties);
      $outputfile=$__appvar['tempdir']."/transactieCheck.pdf";
      $trans->pdf->Output($outputfile,"F");
      $emailAttachement='transacties.pdf';
    }
    else
    {

    $velden=array('id','Client','Accountmanager','Rekening','Omschrijving','Boekdatum','Grootboekrekening','Valuta','Valutakoers','Fonds','Aantal','Fondskoers','Debet','Credit','Bedrag','Transactietype');
    $veldenXls=$velden;
    $veldenXls[]='ISINCode';
    $veldenXls[]='Depotbank';
    $veldenXls[]='SoortOvereenkomst';

    $bedragen=array('Debet','Credit','Bedrag');
    $html='<table border=1><tr>';
    foreach ($velden as $veld)
      $html.="<td>$veld</td>";
    $html.='</tr>';

    $xlsData[]=$veldenXls;
    foreach ($nieuweTransacties as $id=>$transactie)
    {
      $query="SELECT Portefeuilles.Client, Portefeuilles.Accountmanager , Portefeuilles.Depotbank , Portefeuilles.SoortOvereenkomst 
      FROM Portefeuilles 
      Join Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille 
      WHERE Rekeningen.Rekening='".$transactie['Rekening']."' AND Portefeuilles.consolidatie=0 AND Rekeningen.consolidatie=0";
      $db->SQL($query);
      $portefeuille=$db->lookupRecord();
      $transactie['Client']=$portefeuille['Client'];
      $transactie['Accountmanager']=$portefeuille['Accountmanager'];
      $transactie['Depotbank']=$portefeuille['Depotbank'];
      $transactie['SoortOvereenkomst']=$portefeuille['SoortOvereenkomst'];

      if($transactie['Fonds'] <> '')
      {
        $query = "SELECT Fondsen.ISINCode FROM Fondsen WHERE Fondsen.Fonds='" . $transactie['Fonds'] . "'";
        $db->SQL($query);
        $fonds = $db->lookupRecord();
        $transactie['ISINCode']=$fonds['ISINCode'];
      }

      $testBedag=$transactie['Bedrag'];
      if($testBedag < 0)
         $testBedag=$testBedag*-1;
      if($testBedag >= $check['transactiemeldingWaarde'])
      {
        if($check['OrderCheck']==1)
        {

          if(($transactie['Transactietype']=='V' && strpos(strtolower($transactie['Omschrijving']),"lossing") !==false) || $transactie['Transactietype']=='D' || $transactie['Transactietype']=='L' )
            $recordGeforceerdTonen=true;
          else
            $recordGeforceerdTonen=false;

          if($transactie['Grootboekrekening']!='FONDS' || $recordGeforceerdTonen)
          {
            $html.='<tr>';
            $regel=array();
            foreach ($velden as $veld)
            {
              if(in_array($veld,$bedragen))
                $html.='<td  align="right">'.number_format($transactie[$veld],2,',','.').'</td>';
              else
                $html.='<td>'.$transactie[$veld].'</td>';
            }
            foreach($veldenXls as $veld)
              $regel[]=$transactie[$veld];
            $html.='</tr>';
            $xlsData[]=$regel;
          }
        }
        else
        {
          $html.='<tr>';
          $regel=array();
          foreach ($velden as $veld)
          {
            $html.='<td>'.$transactie[$veld].'</td>';
          }
          foreach($veldenXls as $veld)
            $regel[]=$transactie[$veld];
          $html.='</tr>';
          $xlsData[]=$regel;
        }
      }
    }

    $html.='<table>';
    $outputfile=$__appvar['tempdir']."/transactieCheck.xls";
    OutputEmailXls($xlsData,$outputfile);
    $emailAttachement='transacties.xls';
    }

    if(count($xlsData) > 1 || $emailAttachement == 'transacties.pdf') // bij meer dan alleen de xls header of de pdf uitvoer een mail sturen.
    {
      storeControleMail('TransactieCheck', "Transactiewaarde > " . $check['transactiemeldingWaarde'] . " : " . date("d-m-Y H:i"), $html);
      $emailAddesses=getEmailAdressen('transactie');
      if (count($emailAddesses)>0 && $mailserver !='')
      {
        //$emailAddesses = explode(";", $check['transactiemeldingEmail']);
        include_once('../classes/AE_cls_phpmailer.php');
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->From = $emailAddesses[0];
        $mail->FromName = "Airs";
        $mail->Body = $html;
        $mail->AltBody = html_entity_decode(strip_tags($html));
        $mail->AddAttachment($outputfile, $emailAttachement);
        foreach ($emailAddesses as $emailadres)
        {
          $mail->AddAddress($emailadres, $emailadres);
        }
        $mail->Subject = "Transactiewaarde > " . $check['transactiemeldingWaarde'] . " : " . date("d-m-Y H:i");
        $mail->Host = $mailserver;
        if (!$mail->Send())
        {
          echo "Verzenden van e-mail mislukt.";
        }
      }
    }
    else
    {
      logIt('Geen records om te verzenden.');
    }
  }
}

function transactieCheckOld($nieuweTransacties)
{
  $db=new DB();
  $query="SELECT 
  max(transactiemeldingEmail) as transactiemeldingEmail, 
  max(transactiemeldingWaarde) as transactiemeldingWaarde, 
  max(OrderCheck) as OrderCheck  
  FROM Vermogensbeheerders";
  $db->SQL($query);
  $check=$db->lookupRecord();
  if($check['transactiemeldingEmail'])
  {
    $velden=array('Client','Accountmanager','Rekening','Omschrijving','Boekdatum','Grootboekrekening','Valuta','Valutakoers','Fonds','Aantal','Fondskoers','Debet','Credit','Bedrag','Transactietype');
    $bedragen=array('Debet','Credit','Bedrag');
    $html='<table border=1><tr>';
    foreach ($velden as $veld)
      $html.="<td>$veld</td>";
    $html.='</tr>';

    $xlsData[]=$velden;
    foreach ($nieuweTransacties as $id=>$transactie)
    {
      $query="SELECT Portefeuilles.Client, Portefeuilles.Accountmanager 
      FROM Portefeuilles 
      Join Rekeningen ON Portefeuilles.Portefeuille = Rekeningen.Portefeuille 
      WHERE Rekeningen.Rekening='".$transactie['Rekening']."'";
      $db->SQL($query);
      $portefeuille=$db->lookupRecord();
      $transactie['Client']=$portefeuille['Client'];
      $transactie['Accountmanager']=$portefeuille['Accountmanager'];

      $testBedag=$transactie['Bedrag'];
      if($testBedag < 0)
         $testBedag=$testBedag*-1;
      if($testBedag >= $check['transactiemeldingWaarde'])
      {
        if($check['OrderCheck']==1)
        {
          if($transactie['Grootboekrekening']!='FONDS')
          {
            $html.='<tr>';
            $regel=array();
            foreach ($velden as $veld)
            {
              $regel[]=$transactie[$veld];
              if(in_array($veld,$bedragen))
                $html.='<td  align="right">'.number_format($transactie[$veld],2,',','.').'</td>';
              else
                $html.='<td>'.$transactie[$veld].'</td>';
            }
            $html.='</tr>';
            $xlsData[]=$regel;
          }
        }
        else
        {
          $html.='<tr>';
          $regel=array();
          foreach ($velden as $veld)
          {
            $regel[]=$transactie[$veld];
            $html.='<td>'.$transactie[$veld].'</td>';
          }
          $html.='</tr>';
          $xlsData[]=$regel;
        }
      }
    }
    $html.='<table>';

    global $__appvar;
    $xlsOutputfile=$__appvar['tempdir']."/transactieCheck.xls";
    $xlsfile=OutputEmailXls($xlsData,$xlsOutputfile);
    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    storeControleMail('TransactieCheck',"Transactiewaarde > ".$check['transactiemeldingWaarde']." : ".date("d-m-Y H:i"),$html);
  
    $emailAddesses=getEmailAdressen('transactie');
    if (count($emailAddesses)>0 && $mailserver !='')
    {
     // $emailAddesses=explode(";",$check['transactiemeldingEmail']);
      include_once('../classes/AE_cls_phpmailer.php');
      $mail = new PHPMailer();
      $mail->IsSMTP();
      $mail->From     = $emailAddesses[0];
      $mail->FromName = "Airs";
      $mail->Body    = $html;
      $mail->AltBody = html_entity_decode(strip_tags($html));
      $mail->AddAttachment($xlsOutputfile,'transacties.xls');
      foreach ($emailAddesses as $emailadres)
        $mail->AddAddress($emailadres,$emailadres);
      $mail->Subject = "Transactiewaarde > ".$check['transactiemeldingWaarde']." : ".date("d-m-Y H:i");
      $mail->Host=$mailserver;
      if(!$mail->Send())
      {
        echo "Verzenden van e-mail mislukt.";
      }
    }
  }
}

function matchRecords($transactie,$orderRegel)
{
  $match=array();
  $mismatch=array();

  $orderRegel['koersLimiet']=round($orderRegel['koersLimiet'],3);
  $transactie['Fondskoers']=round($transactie['Fondskoers'],3);
  $orderRegel['uitvoeringsPrijs']=round($orderRegel['uitvoeringsPrijs'],3);
  $orderRegel['Aantal']=round($orderRegel['Aantal'],3);
  $transactie['Aantal']=round($transactie['Aantal'],3);

  if($orderRegel['giraleOrder']==1)
  {
    $transactie['Aantal']=$transactie['Fondskoers']*$transactie['Aantal'];
  }

  if(substr($orderRegel['transactieSoort'],0,1) == 'V')
    $orderRegel['Aantal']=$orderRegel['Aantal']*-1;

  if($orderRegel['Aantal'] == $transactie['Aantal'])
    $match['Aantal']="(".$orderRegel['Aantal']." == ".$transactie['Aantal'].")";
  else
    $mismatch['Aantal']="(".$orderRegel['Aantal']." != ".$transactie['Aantal'].")";

  if($orderRegel['transactieSoort'] == $transactie['Transactietype'])
    $match['Transactietype']="(".$orderRegel['transactieSoort']." == ".$transactie['Transactietype'].")";
  else
    $mismatch['Transactietype']="(".$orderRegel['transactieSoort']." != ".$transactie['Transactietype'].")";

  if($orderRegel['uitvoeringsPrijs'] <> 0)
  {
    if($orderRegel['uitvoeringsPrijs'] == $transactie['Fondskoers'])
      $match['Fondskoers']="(Krs ".$orderRegel['uitvoeringsPrijs']." == ".$transactie['Fondskoers'].")";
    else
       $mismatch['Fondskoers']="(Krs ".$orderRegel['uitvoeringsPrijs']." != ".$transactie['Fondskoers'].")";
  }
  elseif($orderRegel['transactieType']=='L' && $orderRegel['koersLimiet'] <> 0)
  {
    if(substr($orderRegel['transactieSoort'],0,1) == 'A')
    {
      if($orderRegel['koersLimiet'] >= $transactie['Fondskoers'])
        $match['Fondskoers']="(Lim ".$orderRegel['koersLimiet']." >= ".$transactie['Fondskoers'].")";
      else
        $mismatch['Fondskoers']="(Lim ".$orderRegel['koersLimiet']." < ".$transactie['Fondskoers'].")";
    }
    if(substr($orderRegel['transactieSoort'],0,1) == 'V')
    {
      if($orderRegel['koersLimiet'] <= $transactie['Fondskoers'])
        $match['Fondskoers']="(Lim ".$orderRegel['koersLimiet']." <= ".$transactie['Fondskoers'].")";
      else
        $mismatch['Fondskoers']="(Lim ".$orderRegel['koersLimiet']." > ".$transactie['Fondskoers'].")";
    }
  }
  else
  {
    $match['Fondskoers']="(".$transactie['Fondskoers'].")";
  }
  if($orderRegel['tijdsLimiet'] != '0000-00-00')
  {
    if(db2jul($orderRegel['tijdsLimiet']) >= db2jul($transactie['Boekdatum']))
      $match['tijdsLimiet']="(".$orderRegel['tijdsLimiet']." >= ".substr($transactie['Boekdatum'],0,10).")";
    else
      $mismatch['tijdsLimiet']="(".$orderRegel['tijdsLimiet']." < ".substr($transactie['Boekdatum'],0,10).")";
  }
  return (array('match'=>$match,'mismatch'=>$mismatch));
}

function getOrderName($portefeuille,$OrderCheckClientNaam)
{
  $db2=new DB();
  $client='';
  if($OrderCheckClientNaam==0)
  {
    $query="SELECT Portefeuilles.client FROM Portefeuilles WHERE portefeuille='".$portefeuille."'";
    $db2->SQL($query);
    $zoekveld=$db2->lookupRecord();
    $client=$zoekveld['client'];
  }
  else
  {
    $query="SELECT zoekveld,naam FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
    $db2->SQL($query);
    $zoekveld=$db2->lookupRecord();
    if($OrderCheckClientNaam==1)
      $client=$zoekveld['zoekveld'];
    elseif($OrderCheckClientNaam==2)
      $client=$zoekveld['naam'];
  }
  return $client;
}

function orderCheck($nieuweFondsTransacties,$debug=false)
{
  global $__appvar,$__ORDERvar;
  $debugLog=array();
  $db=new DB();
  $db2=new DB();
  $query="SELECT max(OrderCheck) as OrderCheck, max(orderControleEmail) as orderControleEmail, max(OrderCheckClientNaam) as OrderCheckClientNaam, max(check_module_ORDER) as orderVersie  FROM Vermogensbeheerders limit 1";
  $db->SQL($query);
  $orderCheck=$db->lookupRecord();
  $orderVersie=$orderCheck['orderVersie'];
  // $orderVersie=1;
  if($debug==true)
  {
    $debugLog['data'][] = "OrderVersie: $orderVersie";
  }
  if($orderCheck['OrderCheck'])
  {
    foreach ($nieuweFondsTransacties as $id=>$transactie)
    {
      if($transactie['Transactietype']=='V' && strpos(strtolower($transactie['Omschrijving']),"lossing") !==false)
      {
        unset($nieuweFondsTransacties[$id]);
        continue;
      }

      $query="SELECT Rekeningen.Portefeuille,Portefeuilles.Accountmanager, Portefeuilles.SoortOvereenkomst, Portefeuilles.Client FROM Rekeningen 
      JOIN Portefeuilles ON Rekeningen.Portefeuille = Portefeuilles.Portefeuille WHERE Rekeningen.Rekening='".$transactie['Rekening']."' AND Rekeningen.consolidatie=0";
      $db->SQL($query);
      $rekening=$db->lookupRecord();
      $nieuweFondsTransacties[$id]['Portefeuille']=$rekening['Portefeuille'];
      $nieuweFondsTransacties[$id]['Accountmanager']=$rekening['Accountmanager'];
      $nieuweFondsTransacties[$id]['SoortOvereenkomst']=$rekening['SoortOvereenkomst'];
      $nieuweFondsTransacties[$id]['Client']=$rekening['Client'];
    }
    if($debug==true)
    {
      $debugLog['data'][] = "TransactieRegels:";
      $debugLog['data'][] = $nieuweFondsTransacties;
    }
    if($orderVersie==2)
      $query='SELECT Portefeuilles.Depotbank, Fondsen.BinckValuta, OrderRegelsV2.id, OrdersV2.giraleOrder , OrdersV2.id as orderId, concat(\''.$__appvar['bedrijf'].'\',OrdersV2.id) as fullId,
       OrderRegelsV2.portefeuille, OrderRegelsV2.Aantal, OrdersV2.fonds, OrdersV2.fondsOmschrijving, OrderRegelsV2.`orderregelstatus` as `status` ,OrderRegelsV2.client,now() as nu,
       OrdersV2.`orderStatus`,OrdersV2.transactieType,OrdersV2.ISINCode as fondscode,OrdersV2.transactieSoort,OrdersV2.tijdsLimiet,OrdersV2.tijdsSoort,OrdersV2.koersLimiet,
      (SELECT SUM(OrderUitvoeringV2.uitvoeringsAantal * OrderUitvoeringV2.uitvoeringsPrijs) / SUM(OrderUitvoeringV2.uitvoeringsAantal) as uitvoeringsPrijs FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid ) as uitvoeringsPrijs,
      (SELECT SUM(OrderUitvoeringV2.uitvoeringsAantal) FROM OrderUitvoeringV2 WHERE OrderUitvoeringV2.orderid=OrderRegelsV2.orderid ) as AantalUitgevoerd,
      UNIX_TIMESTAMP(OrdersV2.add_date) as add_date, OrdersV2.fixOrder
       FROM OrderRegelsV2 Join OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id LEFT JOIN Portefeuilles ON OrderRegelsV2.portefeuille=Portefeuilles.Portefeuille LEFT JOIN Fondsen ON OrdersV2.fonds=Fondsen.Fonds WHERE OrderRegelsV2.`orderregelstatus` IN(0,1,2) ORDER BY fonds,portefeuille';
    else
      $query="SELECT OrderRegels.id, Orders.giraleOrder , Orders.id as orderId,Orders.orderid as fullId, OrderRegels.portefeuille, OrderRegels.Aantal, Orders.fonds, OrderRegels.`status`,OrderRegels.client,now() as nu,
        Orders.`status` as statusLog,Orders.laatsteStatus,Orders.transactieType,Orders.fondsCode,Orders.transactieSoort,Orders.tijdsLimiet,Orders.tijdsSoort,Orders.koersLimiet,
        (SELECT SUM(OrderUitvoering.uitvoeringsAantal * OrderUitvoering.uitvoeringsPrijs) / SUM(OrderUitvoering.uitvoeringsAantal) as uitvoeringsPrijs
        FROM OrderUitvoering WHERE OrderUitvoering.orderid=OrderRegels.orderid ) as uitvoeringsPrijs, UNIX_TIMESTAMP(Orders.add_date) as add_date
        FROM OrderRegels Join Orders ON OrderRegels.orderid = Orders.orderid WHERE OrderRegels.`status` IN(1,2) ORDER BY fonds,portefeuille";
    $db->SQL($query);
    $db->Query();
    $orderRegels=array();
    $orderIds=array();
    while($data=$db->nextRecord())
    {
  
      if(($data['Depotbank']=='BIN' || $data['Depotbank']=='BINB') && $data['BinckValuta']=='PNC' && $data['fixOrder'] == 1)
      {
        $data['uitvoeringsPrijs'] = $data['uitvoeringsPrijs'] / 100;
      }
      if($data['fonds']=='')
        $data['fonds']=$data['fondsOmschrijving'];
      if($orderCheck['OrderCheckClientNaam']>=1)
      {
        $data['client']=getOrderName($data['portefeuille'],$orderCheck['OrderCheckClientNaam']);
      }
      $query="SELECT Accountmanager, SoortOvereenkomst FROM Portefeuilles WHERE portefeuille='".$data['portefeuille']."'";
      $db2->SQL($query);
      $portefeuilleData=$db2->lookupRecord();
      $data['Accountmanager']=$portefeuilleData['Accountmanager'];
      $data['SoortOvereenkomst']=$portefeuilleData['SoortOvereenkomst'];
      $orderRegels[$data['id']]=$data;
      $orderIds[$data['orderId']]=$data['orderId'];
    }
    if($debug==true)
    {
      $debugLog['data'][] = "OrderRegels:";
      $debugLog['data'][] = $orderRegels;
    }
    $checks=array('Aantal','Transactietype','Fondskoers','tijdsLimiet');
    $matchTable="<table border=1 width=1000 style='font-family: sans-serif; font-size: 10px;'>\n<tr><td>Orderid</td><td>Client</td><td>Portefeuille</td><td>Accountmanager</td><td>SoortOvereenkomst</td><td>Fonds</td><td>aantal</td><td>Transactie</td><td>Koers</td><td>Datum</td></tr>\n";
    $mismatchTable=$matchTable;
    $mismatchNoUpdateTable=$matchTable;
    $verlopenTijdsLimietTable=$matchTable;
    $geenMatchTable="<table border=1 width=1000 style='font-family: sans-serif; font-size: 10px;'>\n<tr><td>recordId</td><td>Client</td><td>Rekening</td><td>Portefeuille</td><td>Accountmanager</td><td>SoortOvereenkomst</td><td>Fonds</td><td>aantal</td><td>Transactie</td><td>Koers</td><td>Datum</td></tr>\n";
    $usedIds=array();
    $matchedIds=array('transactie'=>array(),'order'=>array());
    $multipleMatch=array('transactie'=>array(),'order'=>array());
    $xlsData=array();
    $matchTableXls=array(array('Orderid','Client','Portefeuille','Accountmanager','SoortOvereenkomst','Fonds','aantal','Transactie','Koers','Datum'));
    $mismatchTableXls=$matchTableXls;
    $mismatchNoUpdateTableXls=$matchTableXls;
    $verlopenTijdsLimietTableXls=$matchTableXls;
    $geenMatchTableXls=array(array('recordId','Client','Rekening','Portefeuille','Accountmanager','SoortOvereenkomst','Fonds','aantal','Transactie','Koers','Datum'));
    foreach ($orderRegels as $orderregelId=>$orderRegel)
    {
      foreach ($nieuweFondsTransacties as $transactieId=>$transactie)
      {
        $transactie['Transactietype']=str_replace('/','',$transactie['Transactietype']);
        if($orderRegel['portefeuille']==$transactie['Portefeuille'] && $orderRegel['fonds']==$transactie['Fonds'])
        {
          $tmp=matchRecords($transactie,$orderRegel);
          $mismatch=$tmp['mismatch'];
          $match=$tmp['match'];
          if(count($mismatch) < 1)
          {
            if(in_array($transactieId,$matchedIds['transactie']))
              $multipleMatch['transactie'][$transactieId]++;
            if(in_array($orderregelId,$matchedIds['order']))
              $multipleMatch['order'][$orderregelId]++;

            $matchedIds['transactie'][]=$transactieId;
            $matchedIds['order'][]=$orderregelId;
            $matchTable .="<tr><td>".$orderRegel['fullId']."</td><td>".$orderRegel['client']."</td><td>".$transactie['Portefeuille']."</td><td>".$transactie['Accountmanager']."</td><td>".$transactie['SoortOvereenkomst']."</td><td>".$transactie['Fonds']."</td>";
            foreach ($checks as $check)
                $matchTable.="<td>".$match[$check]."</td>";
            $matchTable.="</tr>\n";
            $matchTableXls[]=array($orderRegel['fullId'],$orderRegel['client'],$transactie['Portefeuille'],$transactie['Accountmanager'],$transactie['SoortOvereenkomst'],$transactie['Fonds'],$match['Aantal'],$match['Transactietype'],$match['Fondskoers'],$match['tijdsLimiet']);
            if($orderVersie==2)
              $query="UPDATE OrderRegelsV2 SET OrderRegelsV2.`orderregelStatus`=4, change_user='upd',change_date=now() WHERE id='".$orderregelId."'";
            else
              $query="UPDATE OrderRegels SET OrderRegels.`status`=4, change_user='upd',change_date=now() WHERE id='".$orderregelId."'";
            if($debug==true)
            {
              $debugLog['data'][] = "UPDATE OrderRegels: $query";
            }
            else
            {
              $db->SQL($query);
              $db->Query();
            }
            $usedIds[]=$transactieId;
          }
        }
      }
    }

    foreach ($orderRegels as $orderregelId=>$orderRegel)
    {
      //if(!in_array($orderregelId,$matchedIds['order']))
      //{
        foreach ($nieuweFondsTransacties as $transactieId=>$transactie)
        {
            $transactie['Transactietype']=str_replace('/','',$transactie['Transactietype']);
            if($orderRegel['portefeuille']==$transactie['Portefeuille'] && $orderRegel['fonds']==$transactie['Fonds'])
            {
              $tmp=matchRecords($transactie,$orderRegel);
              $mismatch=$tmp['mismatch'];
              $match=$tmp['match'];
              if(count($mismatch) > 0)
              {
                if(in_array($transactieId,$matchedIds['transactie']))
                  $multipleMatch['transactie'][$transactieId]++;
                if(in_array($orderregelId,$matchedIds['order']))
                  $multipleMatch['order'][$orderregelId]++;
                $matchedIds['transactie'][]=$transactieId;
                $matchedIds['order'][]=$orderregelId;

                $mismatchRegel ="<tr><td>".$orderRegel['fullId']."</td><td>".$orderRegel['client']."</td><td>".$transactie['Portefeuille']."</td><td>".$transactie['Accountmanager']."</td><td>".$transactie['SoortOvereenkomst']."</td><td>".$transactie['Fonds']."</td>";
                $xlsCheck=array();
                foreach ($checks as $check)
                {
                  if(isset($mismatch[$check]))
                  {
                    $mismatchRegel .= "<td style=\"background-color: #DD6666;\"><b>" . $mismatch[$check] . "</b></td>";
                    $xlsCheck[$check]=$mismatch[$check];
                  }
                  else
                  {
                    $mismatchRegel .= "<td>" . $match[$check] . "</td>";
                    $xlsCheck[$check]=$match[$check];
                  }
                }
                $mismatchRegel.="</tr>\n";
                if($orderVersie==2)
                  $query="UPDATE OrderRegelsV2 SET OrderRegelsV2.`orderregelStatus`=3, change_user='upd',change_date=now() WHERE id='".$orderregelId."'";
                else
                  $query="UPDATE OrderRegels SET OrderRegels.`status`=3, change_user='upd',change_date=now() WHERE id='".$orderregelId."'";

                if(!isset($mismatch['Aantal']))
                {
                  $db->SQL($query);
                  if($debug==true)
                    $debugLog['data'][] = "UPDATE OrderRegels: $query";
                  else
                    $db->Query();
                  $mismatchTable.=$mismatchRegel;
                  $mismatchTableXls[]=array($orderRegel['fullId'],$orderRegel['client'],$transactie['Portefeuille'],$transactie['Accountmanager'],$transactie['SoortOvereenkomst'],$transactie['Fonds'],$xlsCheck['Aantal'],$xlsCheck['Transactietype'],$xlsCheck['Fondskoers'],$xlsCheck['tijdsLimiet']);

                }
                else
                {
                  $mismatchNoUpdateTable .= $mismatchRegel;
                  $mismatchNoUpdateTableXls[]=array($orderRegel['fullId'],$orderRegel['client'],$transactie['Portefeuille'],$transactie['Accountmanager'],$transactie['SoortOvereenkomst'],$transactie['Fonds'],$xlsCheck['Aantal'],$xlsCheck['Transactietype'],$xlsCheck['Fondskoers'],$xlsCheck['tijdsLimiet']);
                }

                $usedIds[]=$transactieId;

              }
            }
          //}
        }

      if($orderRegel['transactieType']=='L')
      {
        if($orderRegel['tijdsSoort'] <> 'GTC' && db2jul($orderRegel['tijdsLimiet']) < (db2jul($orderRegel['nu'])-86400) )
        {
          if(!in_array($orderregelId,$matchedIds['order']))
          {
            $verlopenTijdsLimietTable .= "<tr><td>" . $orderRegel['fullId'] . "</td><td>" . $orderRegel['client'] . "</td><td>" . $orderRegel['portefeuille'] . "</td><td>" . $orderRegel['Accountmanager'] . "</td><td>" . $orderRegel['SoortOvereenkomst'] . "</td><td>" . $orderRegel['fonds'] . "</td>";
            foreach ($checks as $check)
            {
              $verlopenTijdsLimietTable .= "<td>" . $orderRegel[$check] . "</td>";
            }
            $verlopenTijdsLimietTable .= "</tr>\n";
            $verlopenTijdsLimietTableXls[]=array($orderRegel['fullId'],$orderRegel['client'],$orderRegel['portefeuille'] ,$orderRegel['Accountmanager'],$orderRegel['SoortOvereenkomst'],$orderRegel['fonds'],$orderRegel['Aantal'],$orderRegel['Transactietype'],$orderRegel['Fondskoers'],$orderRegel['tijdsLimiet']);
            $matchedIds['order'][]=$orderregelId;
            /*
            if($orderVersie==2)//automatisch laten vervallen uitgeschakeld.
            {
              $query="UPDATE OrderRegelsV2 SET OrderRegelsV2.`orderregelStatus`=5, change_user='upd',change_date=now() WHERE id='".$orderregelId."' AND OrderRegelsV2.`orderregelStatus`<2";
              $db->SQL($query);
              if($debug==true)
                $debugLog['data'][] = "UPDATE OrderRegels: $query";
              else
                $db->Query();
            }
            */
          }
        }
      }
    }

    foreach ($nieuweFondsTransacties as $transactieId=>$transactie)
    {
       if(!in_array($transactieId,$usedIds))
       {  if($transactie['Transactietype'] <> 'D' && $transactie['Transactietype'] <> 'L')
          {
            //$query="SELECT Client FROM Portefeuilles WHERE Portefeuille='".$transactie['Portefeuille']."'";
            $query="SELECT Portefeuilles.Client, ModelPortefeuilles.Portefeuille as model FROM Portefeuilles 
                    LEFT JOIN ModelPortefeuilles ON Portefeuilles.Portefeuille=ModelPortefeuilles.Portefeuille 
                    WHERE Portefeuilles.Portefeuille='".$transactie['Portefeuille']."'";
            $db2->SQL($query);
            $client=$db2->lookupRecord();
            if($client['model']=='')
            {
              $geenMatchTable .= "<tr><td>" . $transactie['id'] . "</td><td>" . $client['Client'] . "</td><td>" . $transactie['Rekening'] . "</td><td>" . $transactie['Portefeuille'] . "</td><td>" . $transactie['Accountmanager'] . "</td><td>" . $transactie['SoortOvereenkomst'] . "</td><td>" . $transactie['Fonds'] . "</td>
            <td>" . $transactie['Aantal'] . "</td><td>" . $transactie['Transactietype'] . "</td><td>" . $transactie['Fondskoers'] . "</td><td>" . $transactie['Boekdatum'] . "</td></tr>\n";
              $geenMatchTableXls[] = array($transactie['id'], $client['Client'], $transactie['Rekening'], $transactie['Portefeuille'], $transactie['Accountmanager'], $transactie['SoortOvereenkomst'], $transactie['Fonds'],
                $transactie['Aantal'], $transactie['Transactietype'], $transactie['Fondskoers'], $transactie['Boekdatum']);
            }
          }
       }
    }

    $matchTable.="</table>\n";
    $mismatchTable.="</table>\n";
    $mismatchNoUpdateTable.="</table>\n";
    $verlopenTijdsLimietTable.="</table>\n";
    $geenMatchTable.="</table>\n";

    if($orderVersie==2)
      $query="SELECT OrdersV2.id, count(OrdersV2.orderStatus), 
      sum(if(OrderRegelsV2.`orderregelStatus`= OrdersV2.orderStatus,1,0)) as oudeAantal,
      sum(if(OrderRegelsV2.`orderregelStatus`=0,1,0)) as aantal0,
      sum(if(OrderRegelsV2.`orderregelStatus`=1,1,0)) as aantal1,
      sum(if(OrderRegelsV2.`orderregelStatus`=2,1,0)) as aantal2,
      sum(if(OrderRegelsV2.`orderregelStatus`=3,1,0)) as aantal3,
      sum(if(OrderRegelsV2.`orderregelStatus`=4,1,0)) as aantal4,
      sum(if(OrderRegelsV2.`orderregelStatus`=5,1,0)) as aantal5
      FROM OrderRegelsV2 Join OrdersV2 ON OrderRegelsV2.orderid = OrdersV2.id WHERE OrdersV2.id IN('".implode("','",$orderIds)."') GROUP BY OrdersV2.id";
    else
      $query="SELECT Orders.id, max(OrderRegels.`status`) as hoogsteStatus, Orders.laatsteStatus, count(Orders.laatsteStatus), 
      sum(if(OrderRegels.`status`= Orders.laatsteStatus,1,0)) as oudeAantal,
      sum(if(OrderRegels.`status`=0,1,0)) as aantal0,
      sum(if(OrderRegels.`status`=1,1,0)) as aantal1,
      sum(if(OrderRegels.`status`=2,1,0)) as aantal2,
      sum(if(OrderRegels.`status`=3,1,0)) as aantal3,
      sum(if(OrderRegels.`status`=4,1,0)) as aantal4
      FROM OrderRegels Join Orders ON OrderRegels.orderid = Orders.orderid WHERE Orders.id IN('".implode("','",$orderIds)."') GROUP BY Orders.id";
    $db->SQL($query);
    $db->Query();
    $db2=new DB();

    while($data=$db->nextRecord())
    {
      if($data['aantal0'] == 0 && $data['aantal1'] == 0 && $data['aantal2'] == 0)
      {
        $nieuweStatus=0;
        if($data['aantal3'] > 0)
          $nieuweStatus=3;
        elseif ($data['aantal4'] > 0)
          $nieuweStatus=4;
        elseif ($data['aantal5'] > 0)
          $nieuweStatus=5;

        if($nieuweStatus > 0)
        {
          if($orderVersie==2)
          {
            $query="UPDATE OrdersV2 SET change_user='upd',change_date=now(),orderStatus='$nieuweStatus' WHERE id='".$data['id']."'";
            if($debug==false)
            {
              $log=new orderLogs();
              $log->addToLog($data['id'],'',"Status naar ".$__ORDERvar['status'][$nieuweStatus],'','upd');
            }
          }
          else
          {
            $query="UPDATE Orders SET change_user='upd',change_date=now(),laatsteStatus='$nieuweStatus',status=concat('".date("Ymd_Hi")."/upd - Status naar ".$__ORDERvar['status'][$nieuweStatus]." \n',status) WHERE id='".$data['id']."'";
          }
          if($debug==true)
          {
            $debugLog['data'][] = "UPDATE Orders: $query";
          }
          else
          {
            $db2->SQL($query);
            $db2->Query();
          }
        }
      }
    }
    $multipleMatchTable='';

    $count=0;
    foreach($multipleMatch as $type=>$ids)
    {
      $count+= count($ids);
    }
    $multipleMatchTableXls=array(array('Type','id','Portefeuille','Fonds'));
    if($count > 0)
    {
      $multipleMatchTable="<br>\nMogelijke dubbele transacties<br>\n <table border=1 style='font-family: sans-serif; font-size: 10px;'>\n";
      $multipleMatchTable.="<tr><td>Type</td><td>id</td><td>Portefeuille</td><td>Client</td><td>Fonds</td></tr>\n";
      foreach($multipleMatch as $type=>$ids)
      {
        foreach($ids as $id=>$check)
        {
          if ($type == 'order')
          {
            $multipleMatchTable .= "<tr><td>Orderregel</td><td>" . $orderRegels[$id]['orderId'] . "</td><td>" . $orderRegels[$id]['portefeuille'] . "</td><td>" . $orderRegels[$id]['client'] . "</td><td>" . $orderRegels[$id]['fonds'] . "</td></tr>\n";
            $multipleMatchTableXls[]=array('Orderregel',$orderRegels[$id]['orderId'],$orderRegels[$id]['portefeuille'],$orderRegels[$id]['client'],$orderRegels[$id]['fonds']);
          }
          else
          {
            $multipleMatchTable .= "<tr><td>Rekeningmutatie</td><td>" . $nieuweFondsTransacties[$id]['id'] . "</td><td>" . $nieuweFondsTransacties[$id]['Portefeuille'] . "</td><td>" . $nieuweFondsTransacties[$id]['Client'] . "</td><td>" . $nieuweFondsTransacties[$id]['Fonds'] . "</td></tr>\n";
            $multipleMatchTableXls[]=array('Rekeningmutatie',$nieuweFondsTransacties[$id]['id'],$nieuweFondsTransacties[$id]['Portefeuille'],$nieuweFondsTransacties[$id]['Client'],$nieuweFondsTransacties[$id]['Fonds']);
          }
        }
      }
      $multipleMatchTable.="</table>\n";
    }

    $openstaandeOrdersTable="<table border=1 style='font-family: sans-serif; font-size: 10px;'>\n";
    $openstaandeOrdersTable.="<tr><td>orderid</td><td>Portefeuille</td><td>Aantal</td><td>Transactietype</td><td>Fonds</td><td>add_date</td><td>Status</td><td>Limiet</td><td>Limietdatum</td><td>aantalUitgevoerd</td></tr>\n";
    $openstaandeOrdersTableXls[]=array('orderid','Portefeuille','Aantal','Transactietype','Fonds','add_date','Status','Limiet','Limietdatum','aantalUitgevoerd');
    foreach($orderRegels as $id=>$orderData)
    {
      $vandaag=mktime(0,0,0,date("n") ,date("j") ,date("Y"));
      $gisteren=$vandaag-86400;
      if(!in_array($id,$matchedIds['order']))
      {
        if($orderData['add_date'] < $gisteren || ($orderData['add_date'] < $vandaag && $orderData['status']>1))
        {
          if(isset($__ORDERvar["status"][$orderData['status']]))
            $orderStatus=$__ORDERvar["status"][$orderData['status']];
          else
            $orderStatus=$orderData['status'];
          $openstaandeOrdersTable.="<tr><td>".$orderData['fullId']."</td><td>".$orderData['portefeuille']."</td><td>".$orderData['Aantal']."</td><td>".$orderData['Transactietype']."</td>
          <td>".$orderData['fonds']."</td><td>".date('Y-m-d H:i:s',$orderData['add_date'])."</td><td>".$orderStatus."</td>
          <td>".$orderData['koersLimiet']."</td><td>".$orderData['tijdsLimiet']."</td><td>".$orderData['AantalUitgevoerd']."</td></tr>\n";
          $openstaandeOrdersTableXls[]=array($orderData['fullId'],$orderData['portefeuille'],$orderData['Aantal'],$orderData['Transactietype'],$orderData['fonds'],date('Y-m-d H:i:s',$orderData['add_date']),$orderStatus,$orderData['koersLimiet'],$orderData['tijdsLimiet'],$orderData['AantalUitgevoerd']);
        }
      }
    }
    $openstaandeOrdersTable.="</table>\n";
    
    $cfg=new AE_config();
    $mailserver=$cfg->getData('smtpServer');
    $body="Volledige overeenkomst (nieuwe status: Uitgevoerd/verwerkt)<br>\n $matchTable <br>
Gedeeltelijke overeenkomst (nieuwe status: Uitgevoerd/gecontroleerd)<br>\n $mismatchTable <br>
Gedeeltelijke overeenkomst (status: Uitgevoerd)<br>\n $mismatchNoUpdateTable <br>
Mogelijk dubbele orders<br>\n $multipleMatchTable <br>
VerlopenTijdsLimiet (order wellicht op vervallen zetten)<br>\n $verlopenTijdsLimietTable <br>
Geen Match gevonden<br>\n $geenMatchTable <br>
Overige openstaande orders (ouder dan ".date('Y-m-d',$gisteren) .")<br>\n $openstaandeOrdersTable <br><br>
Bij de vergelijkingen komt de eerste waarde uit de order en de tweede waarde uit de rekeningmutaties." ;

    $geenRecords=false;
    if(count($matchTableXls) < 2 && count($mismatchTableXls) <2 && count($mismatchNoUpdateTableXls) < 2 &&
       count($multipleMatchTableXls) < 2 && count($verlopenTijdsLimietTableXls) < 2 &&
       count($geenMatchTableXls) < 2 && count($openstaandeOrdersTableXls) < 2)
    {
      $geenRecords=true;
    }
    $xlsData=array_merge(array(array('Volledige overeenkomst (nieuwe status: Uitgevoerd/verwerkt)')),$matchTableXls,
                         array(array(''),array('Gedeeltelijke overeenkomst (nieuwe status: Uitgevoerd/gecontroleerd)')),$mismatchTableXls,
                         array(array(''),array('Gedeeltelijke overeenkomst (status: Uitgevoerd)')),$mismatchNoUpdateTableXls,
                         array(array(''),array('Mogelijk dubbele orders')),$multipleMatchTableXls,
                         array(array(''),array('VerlopenTijdsLimiet (order wellicht op vervallen zetten)')),$verlopenTijdsLimietTableXls,
                         array(array(''),array('Geen Match gevonden')),$geenMatchTableXls,
                         array(array(''),array('Overige openstaande orders')),$openstaandeOrdersTableXls);

    storeControleMail('OrderControle',"Order check: ".date("d-m-Y H:i"),$body);
    if($debug==true)
    {
      $debugLog['html'][] = "$body";
    }
    else
    {
      $emailAddesses=getEmailAdressen('order');
      if (count($emailAddesses)>0 && $mailserver != '' && $geenRecords==false)
      {
        $outputfile=$__appvar['tempdir']."/orderControle.xls";
        OutputEmailXls($xlsData,$outputfile);
        $emailAttachement='orderControle.xls';
        //$emailAddesses = explode(";", $orderCheck['orderControleEmail']);
        //$emailAddesses[0]='rvv@aeict.nl';
        //listarray($emailAddesses);exit;
        include_once('../classes/AE_cls_phpmailer.php');
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->From = $emailAddesses[0];
        $mail->FromName = "Airs";
        $mail->Body = $body;
        $mail->AltBody = html_entity_decode(strip_tags($body));
        $mail->AddAttachment($outputfile,$emailAttachement);
        foreach ($emailAddesses as $emailadres)
        {
          $mail->AddAddress($emailadres, $emailadres);
        }
        $mail->Subject = "Order check: " . date("d-m-Y H:i");
        $mail->Host = $mailserver;
        if (!$mail->Send())
        {
          echo "Verzenden van e-mail mislukt.";
        }
      }
    }
  }
  if($debug==true)
    return $debugLog;
}


function checkdisplay($waarde="")
{
  global $pix;
  if ($waarde <> "1")
    return "<img src=$pix"."off.gif width=20 height=20 border=0 >";
  else
    return "<img src=$pix"."on.gif width=20 height=20 border=0 >";
}

function drawButton($name="default", $text="", $title="")
{
  global $ICONS16;
  $button = '<img src="'.$ICONS16[$name]['image'].'" width="16" height="16" border="0" alt="'.$title.'" align="absmiddle">&nbsp;'.$text;
  return $button;
}

function imagecheckbox($value)
{
  if ($value == 1)
    $str = makeButton("checkbox");
  else
    $str = makeButton("checkbox_leeg");
  return $str;
}


function objectTemplate($template,$objectData)
{
  $data = read_file($template);

	while ( list( $name, $field ) = each( $objectData ) )
	{

	  while ( list( $key, $val ) = each( $field ) )
	  {
	    $data = str_replace( "{".$name."_".$key."}", $val, $data);
	    echo "{".$name."_".$key."}";
  	}
 	}


  $data = eregi_replace( "\{[a-zA-Z0-9_-]+\}", "", $data);
  return $data;
}

function getdebuginfo()
{
	global $__load_start, $list;
	$__load_end = getmicrotime();
	$time = $__load_end - $__load_start;
  $bt = debug_backtrace();
	if(is_object($list))
	  $output .= "<b><br><br>:: Debug info </b><br>SQL : ".$list->getSQL()."<br>";
	$output .= "Query : ".getenv("QUERY_STRING")."<br>";
	$output .= "File : <b>".$bt[0]["file"].":".$bt[0]["line"]."</b> Exectime: ".round($time,3)."s <br><br><br><br><br>";
	return $output;
}

function logAccess($directQuery=false,$object='')
{
  global $list,$USR,$__appvar;
  if($__appvar['logAccess'])
  {
    $db=new DB();
    $query="INSERT INTO usageLog SET ";
    if($directQuery)
    {
        $query .= "query='".mysql_real_escape_string(preg_replace('~\s{2,}~', ' ', $directQuery))."',";
        $query .= "object='$object',";
    }
    else if(is_object($list))
    {
 	  $query .= "query='".mysql_real_escape_string(preg_replace('~\s{2,}~', ' ', $list->getSQL()))."',";
 	  if(is_array($list->objects))
 	  {
 	    $objectnamenArray=array_keys($list->objects);
 	    $objectnamen=implode(",",$objectnamenArray);
 	    $query .= "object='$objectnamen',";
 	  }
    }
    $bt = debug_backtrace();
    $query .= "filename='".$bt[0]["file"]."',";
   	$query .= "add_user='$USR',";
   	$query .= "add_date=NOW()";
    $db->SQL($query);
    $db->Query();
  }
  if($directQuery==false)
  {
  	global $__load_start;
	  $__load_end = getmicrotime();
   	$time = $__load_end - $__load_start;
    $duur=round($time,3)."s";
    echo '<script language="JavaScript" TYPE="text/javascript"> try{
    parent.document.getElementById(\'loadTime\').innerHTML=\''.$duur.'\';
    }catch(err) { }

    </script>';
  }
}


// nieuwe form 2 db functies voor datums < 1970 .
function formdate2db($date)
{
	// DD-MM-YYYY
	global $__appvar;
  if ($date != "")
  {
    $dd = explode($__appvar[date_seperator],$date);
    $date = uitnullen(intval($dd[2]),4)."-".uitnullen(intval($dd[1]),2)."-".uitnullen(intval($dd[0]),2);
  }
  else
  {
  	$date = "0000-00-00";
  }
//YYYY-MM-DD
  return $date;
}

function dbdate2form($date)
{
	// YYYY-MM-DD
	global $__appvar;
  if ($date != "" && substr($date,0,10) <> "0000-00-00")
  {
    $dd = explode("-",$date);
    $date = intval($dd[2]).$__appvar[date_seperator].intval($dd[1]).$__appvar[date_seperator].$dd[0];
  }
  else
  {
  	$date = "";
  }
  return $date;
}



function isNumeric($value)
{
	if(empty($value) || is_numeric($value))
		return true;

	return false;
}

function getmicrotime()
{
   list($usec, $sec) = explode(" ", microtime());
   return ((float)$usec + (float)$sec);
}

function getCSVSeperator($USR)
{
	$query = "SELECT csvSeperator FROM (Vermogensbeheerders) ".
					 " JOIN VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder =  Vermogensbeheerders.Vermogensbeheerder".
					 " WHERE VermogensbeheerdersPerGebruiker.Gebruiker = '".$USR."' LIMIT 1";

	$DB = new DB();
	$DB->SQL($query);
	$DB->Query();
	$data = $DB->nextRecord();
	return $data['csvSeperator'];
}

function generateCSV($field,$separator=",")
{
	global $USR;
	$sp = getCSVSeperator($USR);
	if($sp <> "")
		$separator = $sp;
  $txt='';
  for ($a=0;$a<count($field);$a++)
  {
    for($b=0;$b<count($field[$a]);$b++)
    {
      if(is_array($field[$a][$b]))
      {
        $txt .= "\"" . str_replace("\n", "", $field[$a][$b][0]) . "\"" . $separator;
      }
      else
      {
        $txt .= "\"" . str_replace("\n", "", $field[$a][$b]) . "\"" . $separator;
      }
    }
    $txt = substr($txt,0,-1);
    $txt .= "\r\n";
  }
  return $txt;
}

function checkOrderAcces($type="")
{
  if ( empty($type) ) {return false;}
  session_start();

  if ( ! isset ($_SESSION['usersession']['gebruiker']['orderVersie']) )
  {
    $db = new DB();
    $query="SELECT max(OrderOrderdesk) as OrderOrderdesk, max(check_module_ORDER) as orderVersie,
    max(orderGeenHervalidatie) as orderGeenHervalidatie  FROM Vermogensbeheerders limit 1";
    $db->SQL($query);
    $orderCheck=$db->lookupRecord();
    //$orderCheck['OrderOrderdesk']=0;
    $_SESSION['usersession']['gebruiker']['orderVersie'] = $orderCheck['orderVersie'];
    $_SESSION['usersession']['gebruiker']['VermogensbeheerderOrderOrderdesk'] = $orderCheck['OrderOrderdesk'];
    $_SESSION['usersession']['gebruiker']['orderGeenHervalidatie'] = $orderCheck['orderGeenHervalidatie'];
  }
  //versie 1 return altijd true
  if ( $_SESSION['usersession']['gebruiker']['orderVersie'] == 1 ) {return true;}

  if ( $type == 'VermogensbeheerderOrderOrderdesk' )
  {
    return $_SESSION['usersession']['gebruiker']['VermogensbeheerderOrderOrderdesk'];
  }
  elseif ( $type == 'orderGeenHervalidatie' )
  {
    return $_SESSION['usersession']['gebruiker']['orderGeenHervalidatie'];
  }

  if ( $type == 'orderVierOgen' || $type == 'orderFxToestaan' || $type == 'kasbankBrokerVerwerking' || $type == 'notaModule' || $type == 'orderAdviesNotificatie' || $type=='orderTransRep')
  {
    if ( ! isset ($_SESSION['usersession']['gebruiker']['orderVierOgen']))
    {
      $query  = "
        SELECT 
          max(orderVierOgen) as orderVierOgen, 
          max(orderFxToestaan) as orderFxToestaan, 
          max(kasbankBrokerVerwerking) as kasbankBrokerVerwerking, 
          max(check_module_ORDERNOTAS) as notaModule, 
          max(orderTransRep) as orderTransRep,
          max(orderAdviesNotificatie) as orderAdviesNotificatie  
         
          FROM (Vermogensbeheerders) 
       
          INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder 
          WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '".$_SESSION["USR"]."' 
          GROUP BY VermogensbeheerdersPerGebruiker.Gebruiker 
      ";

      $DB = new DB();
      $DB->SQL($query);
      if ($row = $DB->lookupRecord())
      {
        foreach($row as $key=>$value) {
          $_SESSION['usersession']['gebruiker'][$key] = ($value == 1 ? true : false);
          if ( $key === 'orderAdviesNotificatie' ) {
            $_SESSION['usersession']['gebruiker'][$key] = (int) $value;
          }
        }
      }
    }
    return $_SESSION['usersession']['gebruiker'][$type];
  }

  $orderRechten = $_SESSION['usersession']['gebruiker']['orderRechten'];
  if ( ! empty($orderRechten) && isset ($orderRechten[$type]) && $orderRechten[$type] == 1)
  {
    return true;
  }
  return false;
}



function checkAccess($type="")
{
  global $USR;
  if($type=='portefeuille')
  {
    $db = new DB;
    $query = "SELECT id FROM Gebruikers WHERE Gebruiker='$USR' ";
    $db->SQL($query);
    $db->Query();
    if($db->records() > 0)
      return false;
  }

	if($_SESSION['usersession']['superuser'])
		return true;
	else
		return false;
}

$__load_start = getmicrotime();

function tableExists($table)
{
      $db = new DB;
      $query = "show tables LIKE '".$table."'";
      $db->SQL($query);
      $db->Query();
      return ($db->records() > 0);
}

function GetModuleAccess($module)
{
  global $__appvar;

  if (!$_SESSION["USR"]) return false;

//  if($_SESSION['usersession']['superuser'])
//		return true;
//	else
//	{
	  $query  = "SELECT max(check_module_PORTAAL) as check_module_PORTAAL, max(CRM_alleenNAW) as CRM_alleenNAW, max(NAW_inclDocumenten) as NAW_inclDocumenten, max(check_module_CRM) as check_module_CRM,
  max(check_module_ORDER) as check_module_ORDER, max(check_module_BOEKEN) as check_module_BOEKEN, max(check_module_FACTUURHISTORIE) as check_module_FACTUURHISTORIE,
  max(check_participants) as check_participants, max(check_module_UREN) as check_module_UREN FROM (Vermogensbeheerders) ";
	  $query .= "INNER JOIN VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder ";
	  $query .= "WHERE VermogensbeheerdersPerGebruiker.Gebruiker =  '".$_SESSION["USR"]."' GROUP BY VermogensbeheerdersPerGebruiker.Gebruiker ";
    $DB = new DB();
    $DB->SQL($query);
    if ($row = $DB->lookupRecord())
    {
      switch ($module)
      {
        case 'Participaties':
          return $row['check_participants'];
          break;
        case 'ParticipatieGebruiker':
          if($_SESSION['usersession']['gebruiker']['participanten']==1 && $row['check_participants']==1)
            return 1;
          break;
        case "PORTAAL":
          return $row['check_module_PORTAAL'];
          break;
        case "CRM":
          return $row['check_module_CRM'];
          break;
        case "ORDER":
          return $row['check_module_ORDER'];
          break;
        case "BOEKEN":
          return $row['check_module_BOEKEN'];
          break;
        case "FACTUURHISTORIE":
          return $row['check_module_FACTUURHISTORIE'];
          break;
        case "UREN":
          if($row['check_module_UREN']>0)
          {
            if($_SESSION['usersession']['gebruiker']['urenregistratie']>0)
              return $_SESSION['usersession']['gebruiker']['urenregistratie'];
            else
              return $row['check_module_UREN'];
          }
          break;
        case "NAW_inclDocumenten":
          if($row['CRM_alleenNAW']==1 && $row['NAW_inclDocumenten']==1)
          {
            return 1;
          }
          else
          {
            return 0;
          }
          break;
        case "alleenNAW":
          if ($__appvar["bedrijf"]=="HOME")
             return false;
          return $row['CRM_alleenNAW'];
          break;
        default:
          return false;
      }
    }
    else
      return false;
//	}
}

function GetModuleAccessOld($module)
{
  global $__appvar;
  if (!tableExists("ae_modulecfg"))  // functie alleen aanroepen wanneer table bestaat
  {
    return true;
    exit;
  }
  $query = "SELECT * FROM ae_modulecfg WHERE moduleName ='".$module."' AND bedrijf = '".$__appvar["bedrijf"]."'";
  $DB = new DB();
  $DB->SQL($query);
  if ($row = $DB->lookupRecord())
  {
    // bereken checksum
    $dat = explode("-",$row[moduleExpires]);
    if (checkdate($dat[1],$dat[2],$dat[0]))
    {
      $dater = mktime(0,0,0,$dat[1],$dat[2],$dat[0]);
    }
    $part1 = substr($dater,1,4)*ord(substr($row['moduleName'],1,1));
    $part2 = substr($dater,4,4)*(ord(substr($row['moduleName'],1,1))+ord(substr($__appvar['bedrijf'],2,1)));

    for ($t=0;$t <3;$t++)
    {
      $sum += ord(substr($module,$t,1));
    }
    $multi = ord(substr($__appvar['bedrijf'],1,1)) + ord(substr($__appvar['bedrijf'],2,1));
    $checksum = $part1."-".($sum*$multi)."-".$part2;


    if ($checksum == $row[moduleChecksum])
      return true; // module is geautoriseerd voor gebruik
    else
    {
      logIt($module.", $checksum geen autorisatie omdat checksum niet klopt");
      return false; // geen autorisatie omdat checksum niet klopt
    }
  }
  else
  {
    return false;   // module niet gevonden dus geen autorisatie
  }
}

function logIt($txt="",$backTrace=0)
{
  global $USR,$__debug,$aelogAanwezig;
  if ($aelogAanwezig==true || tableExists("ae_log"))
  {
    $aelogAanwezig=true;
    $txt=addslashes($txt);
    $bt = debug_backtrace();
    $pid=getmypid();
    if($pid=='')
    {
      global $MYPID;
      if($MYPID=='')
        $MYPID=rand(0,99999);
      $pid=$MYPID;
    }
    $bron=addslashes($pid." ".basename($bt[$backTrace]["file"]).":".$bt[$backTrace]["line"]);
    $query = "INSERT INTO ae_log SET bron='$bron', txt='$txt', date = now() ,add_user='$USR'";
    $DB = new DB();
    $DB->SQL($query);
    $DB->Query();
  }
  else
  {
    if($__debug == true)
      echo "<script>alert('".$txt."');</script>";
  }
}

function arrayStripslashes($theArray = array())
{
  if (is_array($theArray))
  {
    while( list($k, $v) = each ($theArray))
    {
      if (is_string($v)) $theArray[$k] = stripslashes($v);
    }
    reset($theArray);
  }
  return $theArray;
}

function getCrmNaam($portefeuille,$orders=false)
{
  $DB=new DB();
  $query="SELECT CRM_naw.naam,CRM_naw.naam1,CRM_naw.zoekveld,Vermogensbeheerders.CrmClientNaam,Vermogensbeheerders.OrderCheckClientNaam
          FROM
          Portefeuilles
          JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
          JOIN CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
          WHERE Portefeuilles.Portefeuille='$portefeuille' ";
	$DB->SQL($query);
	$CrmClientNaam=$DB->lookupRecord();
  if($orders==true)
  {
    if($CrmClientNaam['OrderCheckClientNaam']==1)
      return array('naam'=>$CrmClientNaam['zoekveld']);
    if($CrmClientNaam['OrderCheckClientNaam']==2)
      return array('naam'=>$CrmClientNaam['naam']);
  }
  elseif($CrmClientNaam['CrmClientNaam'] == 1)
  {
    return $CrmClientNaam;
  }
  return array();
}

function getVermogensbeheerderField($field)
{
  global $USR, $__appvar;
  if ($__appvar["bedrijf"]=="HOME")
  {
    $query = "
    SELECT 
      {$field} as veld 
    FROM 
      Vermogensbeheerders
    WHERE 
      Vermogensbeheerder = 'ATT'";
  }
  else
  {
    $query = "
    SELECT 
      Vermogensbeheerders.{$field} as veld 
    FROM 
      Vermogensbeheerders
    Inner Join VermogensbeheerdersPerGebruiker ON 
      Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
    WHERE 
      VermogensbeheerdersPerGebruiker.Gebruiker='{$USR}'";
  }

  $db  = new DB();
  $tmp = $db->lookupRecordByQuery($query);
  return $tmp['veld'];
}

function get_eMailInlezenCheck()
{
  global $__appvar;
  if ($__appvar["bedrijf"]=="HOME")
  {
    return 1;
  }
  $db=new DB();
  $query = "
    SELECT 
      eMailInlezen
    FROM 
      `Vermogensbeheerders`
    ORDER BY 
      id
  ";
  $rec = $db->lookupRecordByQuery($query);

  return (int)$rec["eMailInlezen"];
}



if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}

function createVerjaardagslijst($checkboxDisabled=false)
{
  if($checkboxDisabled==true)
    $disabled='disabled';
  else
    $disabled='';  
	
  global $USR;
  
  $DB=new DB();
  $extraWhere='';
  $query="SELECT CRMeigenRecords FROM Gebruikers WHERE Gebruiker='$USR'";
  $DB->SQL($query);
  $gebruikersData = $DB->lookupRecord();
  if($gebruikersData['CRMeigenRecords']>0)
   $extraWhere=" AND CRM_naw.prospectEigenaar='$USR' ";
  $extraWhere.=getRelatieSoortenFilter();

/*
 $eigenaarFilter=" AND (CRM_naw.prospectEigenaar='$USR' OR CRM_naw.accountEigenaar='$USR') ";
  if(checkAccess($type))
    $beperktToegankelijk = "";
  else
  {
    if($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
    {
      $joinPortefeuilles = " LEFT Join VermogensbeheerdersPerGebruiker ON Portefeuilles.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR'  ";
  
      if ($_SESSION['usersession']['gebruiker']['Accountmanager'] <> '' && $_SESSION['usersession']['gebruiker']['overigePortefeuilles'] == 0)
      {
        $where .= " AND ( (Portefeuilles.Portefeuille IS NOT NULL AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' AND (Portefeuilles.Accountmanager='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "' OR 
                Portefeuilles.tweedeAanspreekpunt ='" . $_SESSION['usersession']['gebruiker']['Accountmanager'] . "')) OR (Portefeuilles.Portefeuille IS NULL  $eigenaarFilter ) ) ";
      }
      else
      {
        $where .= " AND ( (Portefeuilles.Portefeuille IS NOT NULL AND VermogensbeheerdersPerGebruiker.Gebruiker='$USR' ) OR ( Portefeuilles.Portefeuille IS NULL   $eigenaarFilter )  ) ";
      }
    }

  }
*/
$query = "
  SELECT CRM_naw.id,
    CRM_naw.naam,
    geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(geboortedatum),
      YEAR(now())-YEAR(geboortedatum)+1)
    as leeftijd,
    overlijdensdatum,
    part_overlijdensdatum,
    IF(kaartVerstuurd < ( NOW() - INTERVAL 50 DAY) ,0,1) as kaartVerstuurd,
    ondernemingsvorm,
    concat(voorletters,' ',tussenvoegsel,' ',achternaam,' ',achtervoegsel) as pnaam,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
      (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(geboortedatum),'-',DAY(geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
  FROM
    CRM_naw
LEFT Join Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
LEFT Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
LEFT Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    verjaardagLijst = 1 AND aktief=1 AND (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
    $extraWhere
  GROUP BY pnaam,geboortedatum
  HAVING
        (jarig_over_dagen < 14 AND jarig_over_dagen >= 0)
  ORDER BY
    jarig_over_dagen
  ";

  $DB->SQL($query);
  $DB->Query();
  if($DB->records())
  {
  $html= "<br><ul>";
  while ($crmRec = $DB->nextRecord())
  {
    $jdag = round($crmRec['jarig_over_dagen']);

    $key=sprintf("%03d", $jdag)."_".$crmRec['naam']."_1_".$crmRec['id'];
    if ($jdag > 1)
      $jdagStr = "wordt over ".$jdag." dagen ".$crmRec['leeftijd']." jaar.";
    else
      if ($jdag == 0 )
        $jdagStr = "wordt <font color=\"Maroon\"> vandaag ".$crmRec['leeftijd']." jaar.</font>";
      else
        $jdagStr = "wordt <font color=\"Navy\"> morgen ".$crmRec['leeftijd']." jaar.</font>";

    if($crmRec['overlijdensdatum'] != '0000-00-00')
      $jdagStr=' is '.dbdate2form($crmRec['overlijdensdatum']). ' overleden. ';

    if($crmRec['kaartVerstuurd'] == 0)
      $checkbox='<input type="checkbox" name="kaart_1_'.$crmRec['id'].'" value="1" '.$disabled.' >';
    else
      $checkbox='<input type="checkbox" name="kaart_1_'.$crmRec['id'].'" checked value="1" disabled>';
      
    if (strtolower($crmRec["ondernemingsvorm"]) == "particulier" || $crmRec["ondernemingsvorm"]=='')
      $Row[$key] = "<li>$checkbox<b>".$crmRec['pnaam']."</b>   ".$jdagStr;
    else
      $Row[$key] = "<li>$checkbox<b>".$crmRec['pnaam']."</b> van ".$crmRec['naam']." ".$jdagStr;
  }
  $html.="</ul>";
  }

$query = "
  SELECT
    CRM_naw.id,
    CRM_naw.naam,
    IF(kaartVerstuurdPartner < ( NOW() - INTERVAL 50 DAY) ,0,1) as kaartVerstuurd,
    part_geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(part_geboortedatum),
      YEAR(now())-YEAR(part_geboortedatum)+1)
    as leeftijd,
    ondernemingsvorm,
    overlijdensdatum,
    part_overlijdensdatum,
    concat(voorletters,' ',tussenvoegsel,' ',achternaam,' ',achtervoegsel) as pnaam,
    concat(part_voorletters,' ',part_tussenvoegsel,' ',part_achternaam,' ',part_achtervoegsel) as partnernaam,
    dayofyear(part_geboortedatum) as geboortedag,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
      (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(part_geboortedatum),'-',DAY(part_geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
    FROM
    CRM_naw
    LEFT Join Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
    LEFT Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
    LEFT Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    part_verjaardagLijst = 1 AND aktief=1 AND (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
    $extraWhere
  GROUP BY partnernaam,part_geboortedatum
  HAVING
    (jarig_over_dagen < 14 AND jarig_over_dagen >= 0)
  ORDER BY
    jarig_over_dagen
  ";
  $DB->SQL($query);
  $DB->Query();
  if($DB->records())
  {
  while ($crmRec = $DB->nextRecord())
  {
    $jdag = round($crmRec['jarig_over_dagen']);

    $key=sprintf("%03d", $jdag)."_".$crmRec['naam']."_2_".$crmRec['id'];
    if ($jdag > 1)
      $jdagStr = " over ".$jdag." dagen ".$crmRec['leeftijd']." jaar.";
    else
    if ($jdag == 0 )
      $jdagStr = "<font color=\"Maroon\"> vandaag ".$crmRec['leeftijd']." jaar.</font>";
    else
      $jdagStr = "<font color=\"Navy\"> morgen ".$crmRec['leeftijd']." jaar.</font>";

    if($crmRec['kaartVerstuurd'] == 0)
      $checkbox='<input type="checkbox" name="kaart_2_'.$crmRec['id'].'" value="1" '.$disabled.' >';
    else
      $checkbox='<input type="checkbox" name="kaart_2_'.$crmRec['id'].'" checked value="1" disabled>';

      
    if($crmRec['overlijdensdatum'] == '0000-00-00')
      $partner=='partner van '.$crmRec['pnaam'];

    if (strtolower($crmRec["ondernemingsvorm"]) == "particulier" || $crmRec["ondernemingsvorm"]=='')
      $Row[$key] = "<li>$checkbox<b>".$crmRec['partnernaam']."</b> $partner wordt ".$jdagStr;
    else
      $Row[$key] = "<li>$checkbox<b>".$crmRec['partnernaam']."</b> $partner van ".$crmRec['naam']." wordt ".$jdagStr;
  }
  $html.="</ul>";
  }




  $query = "
  SELECT CRM_naw.id,
    CRM_naw_adressen.id as adresId,
    CRM_naw_adressen.naam,
    CRM_naw_adressen.geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(CRM_naw_adressen.geboortedatum),
      YEAR(now())-YEAR(CRM_naw_adressen.geboortedatum)+1)
    as leeftijd,
    IF(CRM_naw_adressen.kaartVerstuurd < ( NOW() - INTERVAL 50 DAY) ,0,1) as kaartVerstuurd,
    ondernemingsvorm,
    concat(voorletters,' ',tussenvoegsel,' ',achternaam,' ',achtervoegsel) as pnaam,
    CRM_naw.naam as CRM_naam,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
      (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(CRM_naw_adressen.geboortedatum),'-',DAY(CRM_naw_adressen.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
  FROM
CRM_naw_adressen
   Join CRM_naw ON CRM_naw_adressen.rel_id=CRM_naw.id
LEFT Join Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
LEFT Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
LEFT Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    CRM_naw_adressen.verjaardagLijst = 1 AND aktief=1 AND (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
  GROUP BY pnaam,geboortedatum
  HAVING
        (jarig_over_dagen < 14 AND jarig_over_dagen >= 0)
  ORDER BY
    jarig_over_dagen
  ";

  $DB->SQL($query);
  $DB->Query();
  if($DB->records())
  {
  $html= "<br><ul>"; 
  while ($crmRec = $DB->nextRecord())
  {
    $jdag = round($crmRec['jarig_over_dagen']);

    $key=sprintf("%03d", $jdag)."_".$crmRec['naam']."_1_".$crmRec['id'];
    if ($jdag > 1)
      $jdagStr = "wordt over ".$jdag." dagen ".$crmRec['leeftijd']." jaar.";
    else
      if ($jdag == 0 )
        $jdagStr = "wordt <font color=\"Maroon\"> vandaag ".$crmRec['leeftijd']." jaar.</font>";
      else
        $jdagStr = "wordt <font color=\"Navy\"> morgen ".$crmRec['leeftijd']." jaar.</font>";

    if($crmRec['kaartVerstuurd'] == 0)
      $checkbox='<input type="checkbox" name="kaart_'.$crmRec['adresId'].'_'.$crmRec['id'].'_a" value="1" '.$disabled.' >';
    else
      $checkbox='<input type="checkbox" name="kaart_'.$crmRec['adresId'].'_'.$crmRec['id'].'_a" checked value="1" disabled>';

    $Row[$key] = "<li>$checkbox<b>".$crmRec['naam']."</b> (".$crmRec['CRM_naam'].") ".$jdagStr;
  }
  $html.="</ul>";
  }
//



  $query = "
  SELECT CRM_naw.id,
    CRM_naw_kontaktpersoon.id as adresId,
    CRM_naw_kontaktpersoon.naam,
    CRM_naw_kontaktpersoon.geboortedatum,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      YEAR(now())-YEAR(CRM_naw_kontaktpersoon.geboortedatum),
      YEAR(now())-YEAR(CRM_naw_kontaktpersoon.geboortedatum)+1)
    as leeftijd,
    ondernemingsvorm,
    concat(voorletters,' ',tussenvoegsel,' ',achternaam,' ',achtervoegsel) as pnaam,
    CRM_naw.naam as CRM_naam,
    dayofyear(NOW()) as vandaag,
    if((UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) >= UNIX_TIMESTAMP(CURDATE())),
      (UNIX_TIMESTAMP(concat(YEAR(NOW()),'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400,
      (UNIX_TIMESTAMP(concat(YEAR(NOW())+1,'-',MONTH(CRM_naw_kontaktpersoon.geboortedatum),'-',DAY(CRM_naw_kontaktpersoon.geboortedatum))) - UNIX_TIMESTAMP(CURDATE()))/86400)
    as jarig_over_dagen
  FROM
CRM_naw_kontaktpersoon
   Join CRM_naw ON CRM_naw_kontaktpersoon.rel_id=CRM_naw.id
LEFT Join Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille
LEFT Join Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
LEFT Join VermogensbeheerdersPerGebruiker ON Vermogensbeheerders.Vermogensbeheerder = VermogensbeheerdersPerGebruiker.Vermogensbeheerder
  WHERE
    CRM_naw_kontaktpersoon.verjaardagLijst = 1 AND aktief=1 AND (VermogensbeheerdersPerGebruiker.Gebruiker is null OR VermogensbeheerdersPerGebruiker.Gebruiker='$USR')
  GROUP BY pnaam,geboortedatum
  HAVING
        (jarig_over_dagen < 14 AND jarig_over_dagen >= 0)
  ORDER BY
    jarig_over_dagen
  ";

  $DB->SQL($query);
  $DB->Query();
  if($DB->records())
  {
    $html= "<br><ul>";
    while ($crmRec = $DB->nextRecord())
    {
      $jdag = round($crmRec['jarig_over_dagen']);

      $key=sprintf("%03d", $jdag)."_".$crmRec['naam']."_1_".$crmRec['id'];
      if ($jdag > 1)
        $jdagStr = "wordt over ".$jdag." dagen ".$crmRec['leeftijd']." jaar.";
      else
        if ($jdag == 0 )
          $jdagStr = "wordt <font color=\"Maroon\"> vandaag ".$crmRec['leeftijd']." jaar.</font>";
        else
          $jdagStr = "wordt <font color=\"Navy\"> morgen ".$crmRec['leeftijd']." jaar.</font>";

      if($crmRec['kaartVerstuurd'] == 0)
        $checkbox='<input type="checkbox" name="kaart_'.$crmRec['adresId'].'_'.$crmRec['id'].'_a" value="1" '.$disabled.' >';
      else
        $checkbox='<input type="checkbox" name="kaart_'.$crmRec['adresId'].'_'.$crmRec['id'].'_a" checked value="1" disabled>';

      $Row[$key] = "<li>$checkbox<b>".$crmRec['naam']."</b> (".$crmRec['CRM_naam'].") ".$jdagStr;
    }
    $html.="</ul>";
  }
////
  ksort($Row);
  if (count($Row) > 0)
  {
    $html.= "<ul>";
    foreach($Row as $key=>$value)
    {
      $html.= $value;
    }
    $html.= "</ul>";
  }

  return $html;
}

function getTableDef()
{
  $db=new DB();
  $query="SELECT Gebruiker FROM Gebruikers";
  $db->SQL($query);
  $db->Query();
  $filters=array();
  while($data=$db->nextRecord())
    $filters[]="_".$data['Gebruiker'];

  $filters[]='tmp';
  $filters[]='temp';
  $filters[]='_copy';
  $filters[]='dd_';

  $query="SHOW table status";
  $db->SQL($query);
  $db->Query();
  $tables=array();
  while($data=$db->nextRecord())
  {
    $skip=false;
    foreach ($filters as $filter)
    {
      if(stristr($data['Name'],$filter))
        $skip=true;
    }
    if($skip==false)
      $tables[]=$data['Name'];
  }

  foreach ($tables as $table)
  {
    $query="DESC `$table`";
    $db->SQL($query);
    $db->Query();
    $tables=array();
    while($data=$db->nextRecord())
      $tableDef[$table][$data['Field']]=$data['Type'];
  }
  return $tableDef;
}

function logScherm($txt,$onlyText=false)
{
  global $USR, $skipLogScherm;
  if ($skipLogScherm)
  {
    return;
  }
  echo "<br>\n".date("d-m-Y H:i:s")." ".$txt."";
  if($onlyText==false)
    echo "<script type=\"text/javascript\">if(parent.document.getElementById('generateFrame').height > 15){window.scrollTo(0, document.body.scrollHeight);}else{window.scrollTo(1,2);}</script>";
  flush();
  ob_flush();
  
  // Log ook naar ae_log
  logIt($txt,1);
}

function logTxt($txt)
{
  return "\n".date("d-m-Y H:i:s")." ".$txt;
}

function updateQueueMessage($messages,$queueId)
{
  $DB2 = new DB(2);
  $DB2->SQL("UPDATE updates SET terugmelding = '".mysql_escape_string($messages)."', change_date = NOW() WHERE id = '".$queueId."'");
  $DB2->Query();
}

function progressFrame()
{

return '
  <script type="text/javascript">
function openlog()
{

  var frameheight=document.getElementById(\'generateFrame\').height;

    if(frameheight < 300)
  {
    document.getElementById(\'generateFrame\').height=300;
    document.getElementById(\'generateFrame\').scrolling="yes";
    document.frames[\'generateFrame\'].scrollTo(0, document.body.scrollHeight);
  }
  else
  {
    document.getElementById(\'generateFrame\').height=15;
    document.frames[\'generateFrame\'].scrollTo(1, 1);
    document.getElementById(\'generateFrame\').scrolling="no";
  }


}
</script>
<iframe width="600" height="15" id="generateFrame" name="generateFrame" scrolling="no" frameborder="0" marginwidth="0" marginheight="0"></iframe>
<br><a style="display: block; 	width: 120px; color: Black; font: 11px \'Arial\'; text-decoration: NONE; background-color: #FFFFF0; border: 1px solid; border-color: #DCDCDC #DCDCDC #AAAAAA #AAAAAA; text-align: center;" href="javascript:void(0);" onclick="javascript:openlog();">'.vt("open/sluit log").'</a>
';
}

if (!function_exists('json_encode')) {
    function json_encode($data) {
        switch ($type = gettype($data)) {
            case 'NULL':
                return 'null';
            case 'boolean':
                return ($data ? 'true' : 'false');
            case 'integer':
            case 'double':
            case 'float':
                return $data;
            case 'string':
                return '"' . addslashes($data) . '"';
            case 'object':
                $data = get_object_vars($data);
            case 'array':
                $output_index_count = 0;
                $output_indexed = array();
                $output_associative = array();
                foreach ($data as $key => $value) {
                    $output_indexed[] = json_encode($value);
                    $output_associative[] = json_encode($key) . ':' . json_encode($value);
                    if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                        $output_index_count = NULL;
                    }
                }
                if ($output_index_count !== NULL) {
                    return '[' . implode(',', $output_indexed) . ']';
                } else {
                    return '{' . implode(',', $output_associative) . '}';
                }
            default:
                return ''; // Not supported
        }
    }
}

function maakKnop($name="default", $options=array())
{
  global $ICONS16;
  global $__appvar, $__icon;

  $size     = ($options["size"]=="")?"16":$options["size"];  // default size = 16
  $disabled = $options["disabled"];                          // grayed version of icon;
  $text     = $options["text"];
  $tooltip  = $options["tooltip"];
  //listarray($options);
    // bestaat het icon in de standaard tabel, anders is het een bestandsnaam
  if (array_key_exists($name, $__icon))
  {
    $icon  = $__icon[$name][0];
    if ($options["hideText"] <> true AND $text == "")     $text    = $__icon[$name][1];
    if ($options["hideTooltip"] <> true AND $tooltip == "")  $tooltip = $__icon[$name][2];
  }
  else
  {
    $icon  = $name;
  }

  $class = ($disabled)?"simbisIconGray":"simbisIcon";

  $path = $__appvar["iconBasePath"].$size."/";
  $button = "<span ".(($tooltip <> "")?"title='{$tooltip}'":"")."><img src='{$path}{$icon}' class='{$class}' />"; //style='border: none;'
  if ($text <> "")
    $button .= " ".$text;
  return $button."</span>";
}

function resize_pngimage($file, $maxWidth, $maxHeight) 
{
  list($width, $height) = getimagesize($file);
  $factor=1;
  $factorw=$maxWidth/$width;
  $factorh=$maxHeight/$height;

  if($factorw > $factorh)
    $factor=$factorh;
  else
    $factor=$factorw;  
  
  $newwidth=ceil($width*$factor);
  $newheight=ceil($height*$factor);
  $src = imagecreatefrompng($file);
  $dst = imagecreatetruecolor($maxWidth, $maxHeight);
  imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
  $white = imagecolorallocate($dst, 255, 255, 255);
  imagefill($dst, $maxWidth-1, $maxHeight-1, $white);

  return $dst;
}

if(!function_exists('standard_deviation'))
{
  function standard_deviation($aValues)
  {
    $fMean = array_sum($aValues) / count($aValues);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);
    }
    $fVariance /= count($aValues);
    return (float) sqrt($fVariance);
  }
}
/*
if(!function_exists('standard_deviation2'))
{
  function standard_deviation2($aValues)
  {
    $newValues=array();
    foreach($aValues as $index=>$value)
    {
      if($index > 0)
        $newValues[]=log(($value/$aValues[$index-1]));
    }

    return standard_deviation($newValues);

    $fSquareMean = pow(array_sum($aValues),2)/count($aValues);
    $fSquaredSum = 0.0;
    foreach ($aValues as $i)
    {
        $fSquaredSum += pow($i, 2);
    }
    $out=($fSquaredSum-$fSquareMean)/count($aValues);
   // $fVariance /= count($aValues);
    return (float) sqrt($out);
  }
}
*/

function runPreProcessor($portefeuille)
{
  $db=new DB();
  $query="SELECT Layout FROM Vermogensbeheerders JOIN Portefeuilles ON Portefeuilles.Vermogensbeheerder=Vermogensbeheerders.Vermogensbeheerder WHERE Portefeuille='$portefeuille'";
  $db->SQL($query);
  $data=$db->lookupRecord();
  if(file_exists('../html/rapport/include/PreProcessor_L'.$data['Layout'].'.php'))
  {
    include_once('../html/rapport/include/PreProcessor_L'.$data['Layout'].'.php');
    $processorStr='PreProcessor_L'.$data['Layout'];
    $processor=new $processorStr($portefeuille);
    return 1;
  }
  if(file_exists('../html/rapport/include/layout_'.$data['Layout'].'/PreProcessor_L'.$data['Layout'].'.php'))
  {
    include_once('../html/rapport/include/layout_'.$data['Layout'].'/PreProcessor_L'.$data['Layout'].'.php');
    $processorStr='PreProcessor_L'.$data['Layout'];
    $processor=new $processorStr($portefeuille);
    return 1;
  }
  return 0;
}

function getDataFromTijdelijkeRapportage($portefeuille,$datum)
{
  global $USR,$sessionId;
  $query = "SELECT * FROM TijdelijkeRapportage WHERE portefeuille = '".$portefeuille."' AND add_user = '".$USR."' AND sessionId='$sessionId' AND rapportageDatum = '".$datum."'";
  $db=new DB();
  $db->SQL($query);
  $db->query();
  $unset=array('id','add_date','add_user','sessionId','portefeuille','rapportageDatum');
  $newData=array();
  while($data=$db->nextRecord())
  {
    foreach($unset as $veld)
    {
      unset($data[$veld]);
    }
    $newData[]=$data;
  }
  return $newData;
}

function storeControleMail($categorie,$subject,$body)
{
  global $USR;
  $db=new DB();
  $query="INSERT INTO controleEmailHistorie SET 
  categorie='".mysql_real_escape_string($categorie)."', 
  onderwerp='".mysql_real_escape_string($subject)."',
  body='".mysql_real_escape_string($body)."',
  add_date=NOW(),
  change_date=NOW(),
  add_user='$USR',
  change_user='$USR'";
  $db->SQL($query);
  $db->Query();
}


 /**
   * rekent time om naar minuten na middernacht
   * 
   * @param $time  in UU:MM, 
   * 
   * geeft false bij geen of ongeldige tiid input
   */
function time2minutes($time="")  
{
  $output = -1;
  $parts = explode(":",$time);
  if (is_numeric($parts[0]) AND $parts[0]>=0 AND $parts[0] < 24)
  {
    if (is_numeric($parts[1]) AND $parts[1]>=0 AND $parts[1] < 59)
    {
      $output = ($parts[0] * 60) + $parts[1];
    }  
  }
  
  return ($output < 0)?false:$output;  
}

function minutes2time($minutes=0)  
{
  $hour = (int)($minutes/60);
  $min  = (int)$minutes - ($hour * 60);
  return substr("0{$hour}",-2) .":". substr("0{$min}",-2);
}


function inverse_ncdf($p) {
  //Inverse ncdf approximation by Peter John Acklam, implementation adapted to
  //PHP by Michael Nickerson, using Dr. Thomas Ziegler's C implementation as
	//a guide.  http://home.online.no/~pjacklam/notes/invnorm/index.html
	//I have not checked the accuracy of this implementation.  Be aware that PHP
	//will truncate the coeficcients to 14 digits.

	//You have permission to use and distribute this function freely for
	//whatever purpose you want, but please show common courtesy and give credit
	//where credit is due.

	//Input paramater is $p - probability - where 0 < p < 1.

  //Coefficients in rational approximations
  $a = array(1 => -3.969683028665376e+01, 2 => 2.209460984245205e+02,
    			 3 => -2.759285104469687e+02, 4 => 1.383577518672690e+02,
    			 5 => -3.066479806614716e+01, 6 => 2.506628277459239e+00);

  $b = array(1 => -5.447609879822406e+01, 2 => 1.615858368580409e+02,
          		 3 => -1.556989798598866e+02, 4 => 6.680131188771972e+01,
    			 5 => -1.328068155288572e+01);

  $c = array(1 => -7.784894002430293e-03, 2 => -3.223964580411365e-01,
    	 			 3 => -2.400758277161838e+00, 4 => -2.549732539343734e+00,
    			 5 => 4.374664141464968e+00, 6 => 2.938163982698783e+00);

  $d = array(1 => 7.784695709041462e-03, 2 => 3.224671290700398e-01,
    	 			 3 => 2.445134137142996e+00, 4 => 3.754408661907416e+00);

  //Define break-points.
  $p_low =  0.02425;									 //Use lower region approx. below this
  $p_high = 1 - $p_low;								 //Use upper region approx. above this

  //Define/list variables (doesn't really need a definition)
  //$p (probability), $sigma (std. deviation), and $mu (mean) are user inputs
  $q = NULL; $x = NULL; $y = NULL; $r = NULL;

  //Rational approximation for lower region.
  if (0 < $p && $p < $p_low) {
    $q = sqrt(-2 * log($p));
    $x = ((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) *
   	 	 	 $q + $c[6]) / (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) *
  	 		 $q + 1);
  }

  //Rational approximation for central region.
  elseif ($p_low <= $p && $p <= $p_high) {
    $q = $p - 0.5;
    $r = $q * $q;
    $x = ((((($a[1] * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) *
   	 	 	 $r + $a[6]) * $q / ((((($b[1] * $r + $b[2]) * $r + $b[3]) * $r +
  	 		 $b[4]) * $r + $b[5]) * $r + 1);
  }

  //Rational approximation for upper region.
  elseif ($p_high < $p && $p < 1) {
    $q = sqrt(-2 * log(1 - $p));
    $x = -((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q +
   	 	 	 $c[5]) * $q + $c[6]) / (((($d[1] * $q + $d[2]) * $q + $d[3]) *
  	 		 $q + $d[4]) * $q + 1);
  }

  //If 0 < p < 1, return a null value
  else {
  	$x = NULL;
  }

  return $x;
  //END inverse ncdf implementation.
}

function lastWorkday($format='d-m-Y')
{
  $now=time();
  $w=date('w',$now);
  if($w==0)
    $dagen=-2;
  elseif($w==1)
    $dagen=-3;
  else
    $dagen=-1;

  return date($format,$now+($dagen*86400));    
}

function getRelatieSoortenFilter()
{
  global $USR;
  
  if(isset($_SESSION['CRM_nawListRechtenFilter']))
    return $_SESSION['CRM_nawListRechtenFilter'];

  $db=new DB();
  $query="SELECT CRM_relatieSoorten FROM Gebruikers WHERE Gebruiker='$USR'";
  $db->SQL($query);
  $CRM_relatieSoorten=$db->lookupRecord();
  $CRM_relatieSoorten=unserialize($CRM_relatieSoorten['CRM_relatieSoorten']);
  
  $query="SELECT veldnaam,omschrijving FROM CRM_eigenVelden WHERE relatieSoort=1";
  $db->SQL($query);
  $db->Query();
  while ($data=$db->nextRecord())
  {
    $alleRelatieSoorten[]=$data['veldnaam'];
  }
     
  $filter='';
  if(is_array($CRM_relatieSoorten))
  {
     $query="DESC CRM_naw";
     $db->SQL($query);
     $db->Query();
     $crmVelden=array();
     while($data=$db->nextRecord('num'))
      $crmVelden[]=$data[0];
        
     $allowArray=array();
     $denyArray=array();
     $allArray=array();
     foreach($alleRelatieSoorten as $key=>$value)
     {
       if($value<>'all' && $value<>'inaktief' && $value<>'aktief' && in_array($value,$CRM_relatieSoorten) && in_array($value,$crmVelden))
       {
         $allowArray[]='CRM_naw.'.$value;
         $allArray[]='CRM_naw.'.$value;
       }
       else
       {
         $denyArray[]='CRM_naw.'.$value;
         $allArray[]='CRM_naw.'.$value;  
       }  
     }
      
        
     if(count($allowArray) < 1)
        $allowArray[]='-1';
     if(count($denyArray) < 1)
        $denyArray[]='-1';
      if(count($allArray) < 1)
        $allArray[]='-1';
        
     $filter="AND ( (".implode('=1 OR ',$allowArray)."=1)  or ( ".implode('=0 AND ',$allArray)."=0) ) ";  
     
   }
   $_SESSION['CRM_nawListRechtenFilter']=$filter;
   return $filter;
}

/**
 * Save track and trace
 * 
 * @since 7-10-2014
 * @author RM
 * 
 * @param type $tableName
 * @param type $recordId
 * @param type $fieldName
 * @param type $oldValue
 * @param type $newValue
 * @param type $addUser
 * @return type
 */
function addTrackAndTrace($tableName, $recordId, $fieldName, $oldValue, $newValue, $addUser)
{
  $db = new DB();
  $query  = "INSERT INTO trackAndTrace SET ";
  $query .= " `tabel` = '".mysql_real_escape_string($tableName)."', ";
  $query .= " `recordId` = '".mysql_real_escape_string($recordId)."', ";
  $query .= " `veld` = '".mysql_real_escape_string($fieldName)."', ";
  $query .= " `oudeWaarde` = '".mysql_real_escape_string($oldValue)."', ";
  $query .= " `nieuweWaarde` = '".mysql_real_escape_string($newValue)."', ";
  $query .= " `add_date` = NOW(), ";
  $query .= " `add_user` = '".mysql_real_escape_string($addUser)."' ";
  return $db->executeQuery($query);
}

function requestType ($type = '') {
  switch ($type) {
    case 'ajax':
      if( ! empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {return true;}
      break;
    case 'post':
      if(strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {return true;}
      break;
    case 'get':
      if(strtoupper($_SERVER['REQUEST_METHOD']) === 'GET') {return true;}
      break;
   }
   
   return false;
}



function getIcon ($name="default", $options = array() )
{
  global $__appvar, $__icon;

  $size     = ($options["size"]=="")?"16":$options["size"];  // default size = 16
  $disabled = $options["disabled"];                          // grayed version of icon;
  $text     = $options["text"];
  $tooltip  = $options["tooltip"];
  //listarray($options);
    // bestaat het icon in de standaard tabel, anders is het een bestandsnaam
  if (array_key_exists($name, $__icon))
  {
    $icon  = $__icon[$name]['image'];
    //check if hide test not isset, text = text or hidetext not == true and empty text text = text 
    if ( ! isset ($options["hideText"]) || ($options["hideText"] != true && empty($text )) ) {
      $text = $__icon[$name]['text'];
    }
    if ($options["hideTooltip"] <> true AND $tooltip == "")  $tooltip = $__icon[$name]['title'];
  }
  else
  {
    $icon  = $name;
  }
  
  $class = ($disabled) ? "simbisIconGray" : "simbisIcon";

  $path = $__appvar["newIconBasePath"].$size."/";
  $button = "<span ".(($tooltip <> "")?"title='{$tooltip}'":"")."><img src='{$path}{$icon}' class='{$class}' />";
  if ($text <> "")
    $button .= " ".$text;
  return $button."</span>";
}

  
  function updateVermogensbeheerderKleuren($type,$oud,$new)
  {
    global $USR;
    $DB=new DB();
    $query="SELECT id,grafiek_kleur,vermogensbeheerder FROM Vermogensbeheerders";
    $DB->SQL($query);
    $DB->Query();
    $veranderingen=array($oud=>$new);
    while($data=$DB->nextRecord())
    {
      $kleuren=unserialize($data['grafiek_kleur']);
      foreach($veranderingen as $oud=>$new)
      {
        if(isset($kleuren[$type][$oud]))
        {
          $kleuren[$type][$new]=$kleuren[$type][$oud];
          $newKleuren=serialize($kleuren);
          $queries[]="UPDATE Vermogensbeheerders SET change_date=now(),change_user='$USR', grafiek_kleur='".mysql_real_escape_string($newKleuren)."' WHERE id='".$data['id']."'";
          unset($kleuren[$type][$oud]);
        }
      }
    }
    foreach($queries as $query)
    {
      $DB->SQL($query);
      $DB->Query();     
    }
  }

function verwerkFondskoersaanvraag($aanvraagId)
{
  global $USR;
  if ($USR == '')
  {
    $USR = 'sys';
  }
  
  $DB=new DB();
  $aanvraag = new FondskoersAanvragen();
  $aanvraag->getById($aanvraagId);
  if($aanvraag->get('verwerkt') <> 0)
    return "Fondsaanvraag $aanvraagId is al verwerkt. <br>\n";
  $aanvraag->set('verwerkt',2);
  $aanvraag->save();
  $aanvraag->sendFondskoersaanvraagEmail();
  
  $fondskoers = new Fondskoersen();
  $kopieerVelden=array('Fonds','Datum','Koers');
  $query="SELECT id FROM Fondskoersen WHERE 1 ";
  foreach($aanvraag->data['fields'] as $veld=>$veldData)
  {
    if(in_array($veld,$kopieerVelden))
    {
      $query.=" AND $veld = '".mysql_real_escape_string($veldData['value'])."'";
      $fondskoers->set($veld, $veldData['value']);
    }
  }
  
  if($DB->QRecords($query) == 0)
  {
    $fondskoers->save();
    $id = $fondskoers->get('id');
    $query = "UPDATE Fondskoersen SET add_user='" . mysql_real_escape_string($aanvraag->get('add_user')) . "' WHERE id = '" . $id . "'";
    $DB->SQL($query);
    $DB->Query();
    $log = "Fondskoers voor (" . $aanvraag->get('Fonds') . ") op (" . $aanvraag->get('Datum') . ") aangemaakt.<br>\n";
    $aanvraag->createQueueUpdates($aanvraagId);
  }
  else
  {
    $log = "Fondskoers voor (" . $aanvraag->get('Fonds') . ") op (" . $aanvraag->get('Datum') . ") is al aanwezig.<br>\n";
  }
  return $log;
}

function verwerkFondsaanvraag($aanvraagId)
{
  global $USR;
  if($USR=='')
    $USR='sys';
  $log='';
  $DB=new DB();
  $query="SELECT id,ISINCode,Valuta,Vermogensbeheerder,Beleggingscategorie,Beleggingssector,Regio,Attributiecategorie,Zorgplicht,afmCategorie,Vermogensbeheerder,
Fonds,optieSymbool,OptieType,OptieExpDatum,OptieUitoefenPrijs,Beurs,standaardSector,Fondseenheid,OptieBovenliggendFonds,identifierVWD FROM fondsAanvragen WHERE id='".mysql_real_escape_string($aanvraagId)."'";
  $DB->SQL($query);
  $aanvraag=$DB->lookupRecord();
  $object = new FondsAanvragen();

  if($aanvraag['Fonds'] <> '' && $aanvraag['optieSymbool'] <>'')
  {
    $query = "SELECT Fonds FROM Fondsen WHERE Fonds='" . mysql_real_escape_string($aanvraag['Fonds']) . "'";
    $DB->SQL($query);
    $fonds = $DB->lookupRecord();
    if ($fonds['Fonds'] == '')
    {
      $log.= "Fonds bij (" . $aanvraag['Fonds'] . ") niet gevonden, fonds wordt aangemaakt.<br>\n";
      $setVelden=array('Fonds','Valuta','OptieType','OptieExpDatum','OptieUitoefenPrijs','Fondseenheid','Beurs','standaardSector','OptieBovenliggendFonds','identifierVWD');
      $setSql='';
      foreach($setVelden as $veld)
        $setSql.=", $veld='".mysql_real_escape_string($aanvraag[$veld]) ."'";
      $vertaal=array('C'=>'Call','P'=>'Put');
      $einddatum='';
      if($aanvraag['OptieExpDatum']  <> '')
      {
        $maand=substr($aanvraag['OptieExpDatum'],4,2)+1;
        $jaar=substr($aanvraag['OptieExpDatum'],0,4);
        $unixtime=adodb_mktime(0,0,0,$maand,0,$jaar);
        $einddatum=adodb_date('Y-m-d',$unixtime);
      }
      $query="INSERT INTO Fondsen SET add_date=NOW(),add_user='$USR', change_date=NOW(),change_user='$USR', fondssoort='OPT', 
       FondsImportCode='".str_replace(' ','',mysql_real_escape_string($aanvraag['Fonds']))."',
       optieCode='".mysql_real_escape_string($aanvraag['optieSymbool'])."',
       EindDatum='".$einddatum."',
       Omschrijving='".mysql_real_escape_string(str_replace($aanvraag['optieSymbool'].' '.$aanvraag['OptieType'].' ',$aanvraag['OptieBovenliggendFonds'].' '.$vertaal[$aanvraag['OptieType']].' ',$aanvraag['Fonds']))."' $setSql ";
      $DB->SQL($query);
      $DB->Query();
      $fonds['Fonds']=$aanvraag['Fonds'];
    }
  }
  else
  {
    $query = "SELECT Fonds FROM Fondsen WHERE ISINCode='" . mysql_real_escape_string($aanvraag['ISINCode']) . "' AND Valuta='" . mysql_real_escape_string($aanvraag['Valuta']) . "'";
    $DB->SQL($query);
    $fonds = $DB->lookupRecord();
    if ($fonds['Fonds'] == '')
    {
      return "Fonds bij (" . $aanvraag['ISINCode'] . ") en (" . $aanvraag['Valuta'] . ") niet gevonden.<br>\n";
    }
  }
  $query="SELECT Fonds,Beleggingscategorie FROM BeleggingscategoriePerFonds WHERE Vermogensbeheerder='".mysql_real_escape_string($aanvraag['Vermogensbeheerder'])."' AND Fonds='".mysql_real_escape_string($fonds['Fonds'])."'";
  $DB->SQL($query);
  $categorie=$DB->lookupRecord();
  if($categorie['Fonds']!='')
  {
    $log.="Bij ".$aanvraag['Vermogensbeheerder']." (".$categorie['Fonds'].") is al een  Beleggingscategorie (".$aanvraag['Beleggingscategorie'].") aanwezig.<br>\n";
    $query="UPDATE fondsAanvragen SET verwerkt=-1,change_date=now(),change_user='$USR' WHERE id='".$aanvraag['id']."'";
    $DB->SQL($query);
    if($DB->Query())
    {

      $object->getById($aanvraag['id']);
      $object->sendFondsaanvraagEmail();
    }
    $log.= 'Status van Fondsaanvraag op verwijderd gezet.<br>\n';
    return $log;
  }
  else
  {
    if($aanvraag['Beleggingssector'] <> '' || $aanvraag['Regio'] <>'' || $aanvraag['Attributiecategorie'] <>'')
    {
      $query="SELECT Fonds,Beleggingssector FROM BeleggingssectorPerFonds WHERE Vermogensbeheerder='".mysql_real_escape_string($aanvraag['Vermogensbeheerder'])."' AND Fonds='".mysql_real_escape_string($fonds['Fonds'])."'";
      if($DB->QRecords($query)<1)
      {
        $query="INSERT INTO BeleggingssectorPerFonds SET 
          Vermogensbeheerder='".mysql_real_escape_string($aanvraag['Vermogensbeheerder'])."',
          Fonds='".mysql_real_escape_string($fonds['Fonds'])."',
          Beleggingssector='".mysql_real_escape_string($aanvraag['Beleggingssector'])."',
          Regio='".mysql_real_escape_string($aanvraag['Regio'])."',
          Attributiecategorie='".mysql_real_escape_string($aanvraag['Attributiecategorie'])."',
          add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
        $DB->SQL($query);
        if($DB->Query())
          $log.= "Fonds ".$fonds['Fonds']." is toegevoegd aan BeleggingssectorPerFonds voor ".$aanvraag['Vermogensbeheerder'].".<br>\n";
      }
      else
        $log.= "Bij ".$aanvraag['Vermogensbeheerder']." (".$categorie['Fonds'].") is al een record in BeleggingssectorPerFonds aanwezig.<br>\n";
    }

    if($aanvraag['Zorgplicht'] <> '')
    {
      $query="SELECT Fonds,Zorgplicht FROM ZorgplichtPerFonds WHERE Vermogensbeheerder='".mysql_real_escape_string($aanvraag['Vermogensbeheerder'])."' AND Fonds='".mysql_real_escape_string($fonds['Fonds'])."'";
      if($DB->QRecords($query)<1)
      {
        $query="INSERT INTO ZorgplichtPerFonds SET 
          Vermogensbeheerder='".mysql_real_escape_string($aanvraag['Vermogensbeheerder'])."',
          Fonds='".mysql_real_escape_string($fonds['Fonds'])."',
          Zorgplicht='".mysql_real_escape_string($aanvraag['Zorgplicht'])."',
          Percentage='100',
          add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
        $DB->SQL($query);
        if($DB->Query())
          $log.= "Fonds ".$fonds['Fonds']." is toegevoegd aan ZorgplichtPerFonds voor ".$aanvraag['Vermogensbeheerder'].".<br>\n";
      }
      else
        $log.= "Bij ".$aanvraag['Vermogensbeheerder']." (".$categorie['Fonds'].") is al een record in ZorgplichtPerFonds aanwezig.<br>\n";
    }

    $query="INSERT INTO BeleggingscategoriePerFonds SET 
    Vermogensbeheerder='".mysql_real_escape_string($aanvraag['Vermogensbeheerder'])."',
    Fonds='".mysql_real_escape_string($fonds['Fonds'])."',
    Beleggingscategorie='".mysql_real_escape_string($aanvraag['Beleggingscategorie'])."',
    afmCategorie='".mysql_real_escape_string($aanvraag['afmCategorie'])."',
    duurzaamheid='".mysql_real_escape_string($aanvraag['Duurzaamheid'])."',
    add_date=now(),change_date=now(),add_user='$USR',change_user='$USR'";
    $DB->SQL($query);
    if($DB->Query())
    {
      $query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='".mysql_real_escape_string($fonds['Fonds'])."' ORDER BY Datum desc limit 1";
      $DB->SQL($query);
      $fondsKoers=$DB->lookupRecord();
      $query="UPDATE fondsAanvragen SET verwerkt=1, change_date=now(),change_user='$USR' WHERE id='".$aanvraag['id']."'";
      $DB->SQL($query);
      $DB->Query();
      $object->getById($aanvraag['id']);
      $object->sendFondsaanvraagEmail();
      $object->createQueueUpdates($aanvraag['id'],$fonds['Fonds']);
      $julDatum=db2jul($fondsKoers['Datum']);
      $log.= "Fonds ".$fonds['Fonds']." is toegevoegd aan BeleggingscategoriePerFonds voor ".$aanvraag['Vermogensbeheerder'].". - Laatste koers ".$fondsKoers['Koers']." per ".jul2form($julDatum).".<br>\n";;
      if($julDatum< (time()-864000)) //10 dagen oud
      {
        $log.= "Koers is ouder dan 10 dagen.<br>\n";
      }

    }
  }
  return $log;
}

function checkLock($table,$tableId)
{
  $query="SELECT user,change_date, 1 as `locked` FROM tableLocks WHERE `table`='".mysql_real_escape_string($table)."' AND tableId='$tableId'";
  $db=new DB();
  if($db->QRecords($query) > 0)
    return $db->nextRecord();
  else
    return array('locked'=>0);
}

function removeLocks($table,$tableId=0)
{
  global $USR;
  $db=new DB();
  $whereId='';
  if($tableId>0)
    $whereId="AND tableId='$tableId'";
  $query="DELETE FROM tableLocks WHERE `table`='$table' AND add_user='$USR' $whereId";
  $db->SQL($query);
  $db->Query();
}

function getRentePercentage($fonds,$rapportageDatum)
{
  $DB=new DB();
  $q = "SELECT Datum, Rentepercentage FROM Rentepercentages WHERE Datum <= '".$rapportageDatum."' AND GeldigVanaf < '".$rapportageDatum."' AND Fonds = '".mysql_real_escape_string($fonds)."' ORDER BY Datum DESC, GeldigVanaf DESC LIMIT 1";
  $DB->SQL($q);
  $DB->Query();
  $rente = $DB->NextRecord();
  return $rente;
}

function getEmailAdressen($type)
{
  $adressen=array();
  $DB=new DB();
  
  $query="SELECT max(orderControleEmail) as `order`,
 max(transactiemeldingEmail) as transactie,
 max(fondsenmeldingEmail) as nieuweFondsen,
 max(emailSignaleringen) as signaleringen,
 max(emailPortaalvulling) as portaal FROM Vermogensbeheerders limit 1";
 
  $DB->SQL($query);
  $basis=$DB->lookupRecord();
  if(isset($basis[$type]) && $basis[$type]<>'')
  {
    $tmp=explode(";", $basis[$type]);
    foreach($tmp as $adres)
    {
      if(!in_array($adres,$adressen))
        $adressen[] = $adres;
    }
  }
  
  $query="SELECT
Gebruikers.Naam,
Gebruikers.emailRechten,
Gebruikers.emailAdres
FROM
Gebruikers
WHERE Gebruikers.emailAdres<> '' GROUP BY Gebruikers.emailAdres ";
  $DB->SQL($query);
  $DB->Query();

  while($data = $DB->NextRecord())
  {
    $rechten=unserialize($data['emailRechten']);
    if(isset($rechten[$type]) && $rechten[$type]==1)
    {
      if(!in_array($data['emailAdres'],$adressen))
        $adressen[]=$data['emailAdres'];//$data['Naam'];
    }
  }
  return $adressen;
}


function simpleMail($to,$subject,$body)
{
  $cfg=new AE_config();
  $mailserver=$cfg->getData('smtpServer');
  $emailAddesses=explode(";",$to);
  include_once('../classes/AE_cls_phpmailer.php');
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->From     = $emailAddesses[0];
  $mail->FromName = "Airs";
  $mail->Body    = $body;
  $mail->AltBody = html_entity_decode(strip_tags($body));
  foreach ($emailAddesses as $emailadres)
  {
    $mail->AddAddress($emailadres,$emailadres);
  }
  $mail->Subject = $subject;
  $mail->Host=$mailserver;
  $mail->Send();
}

function formatGetalGlobal($getal, $decimals)
{
  return number_format($getal,$decimals,',','.');
}



/**
 *
 * Controlleren of portefeuille een adviesrelatie is
 * @param $portefeuille
 * @return bool
 */
function adviesRelatieCheck ($portefeuille) {
  // Wanneer orderAdviesNotificatie uitstaat standaard false
//  if ( checkOrderAcces ('orderAdviesNotificatie') === false ) {
//    return false;
//  }
  $db=new DB();
  $query = "
      SELECT SoortOvereenkomsten.* FROM `Portefeuilles`

      LEFT JOIN SoortOvereenkomsten
      ON SoortOvereenkomsten.SoortOvereenkomst = Portefeuilles.SoortOvereenkomst
      WHERE `portefeuille` = \"" . $portefeuille . "\"
    ";

  $db->executeQuery($query);
  $soortOvereenkomstData = $db->nextRecord();
  return ($soortOvereenkomstData['adviesRelatie'] === 'J' ? true:false);
}
/** einde adviesralatie */


/**
 *
 * Controlleren of portefeuille een adviesrelatie is
 * @param $portefeuille
 * @return bool
 */
function isAdviesRelatie ($portefeuille) {
  $db=new DB();
  $query = "
    SELECT
      SoortOvereenkomsten.adviesRelatie
    FROM SoortOvereenkomsten
    JOIN Portefeuilles ON SoortOvereenkomsten.SoortOvereenkomst = Portefeuilles.SoortOvereenkomst
    WHERE Portefeuilles.portefeuille = \"" . mysql_real_escape_string($portefeuille) . "\"
  ";
  $db->executeQuery($query);
  $soortOvereenkomstData = $db->nextRecord();

  return ($soortOvereenkomstData['adviesRelatie'] === 'J' ? true:false);
}
/** einde adviesralatie */


function verwerkFixQueue()
{
  global $__FIX,$__appvar;
  if(isset($__FIX["bedrijfscode"]) && $__FIX["bedrijfscode"] <> '')
    $useFix=true;
  else
    $useFix=false;

  if($useFix==false)
    return false;

  $cfg = new AE_config();
  $nu=time();
  $lastOrder=$cfg->getData("lastFixOrder",false);
  if(($nu-$lastOrder) < 300)
    $interval=3;
  else
    $interval=30;

  $lastPoll=$cfg->getData("lastFixPoll",true);
  $delay=$nu-$lastPoll;
  if($delay > $interval && $useFix == true)
  {
    $cfg->addItem("lastFixPoll",$nu);
    $lastPollLock=$cfg->getData("lastFixPollLock");
    if(($nu-$lastPollLock)>60)
    {
      $cfg->addItem("lastFixPollLock", $nu);

      if($lastPollLock <> 0)//Laatste lock was nog niet verwijderd.
        simpleMail('frank@airs.nl',"FixLock bij ".$__appvar["bedrijf"]." verlopen","FixLock bij ".$__appvar["bedrijf"]." verlopen. ($nu - $lastPollLock)");

      $fix = new AE_FIXtransport();
      $set = $fix->readMessagesFromQueue();
      foreach ($set as $bericht)
      {
        if (is_array($bericht['message']))
        {
          $data = $fix->decodeMessage($bericht);
          if (is_array($data))
          {
            $verwerkt = $fix->updateOrder($data);
          }
          else
          {
            $verwerkt = $data;
          }
          $fix->updateQueue($bericht['id'], $verwerkt);
        }
        else
        {
          $fix->addError('1', "Geen message in bericht.");
          $fix->updateQueue($bericht['id'], 0);
        }
      }
      $cfg->addItem("lastFixPollLock", 0);
    }
  }
}

function XlsStringFromColumnIndex($pColumnIndex = 0)
{
    if ($pColumnIndex < 26)
      return chr(65 + $pColumnIndex);
    elseif ($pColumnIndex < 702)
      return chr(64 + ($pColumnIndex / 26)) . chr(65 + $pColumnIndex % 26);
    else
      return chr(64 + (($pColumnIndex - 26) / 676)) . chr(65 + ((($pColumnIndex - 26) % 676) / 26)) . chr(65 + $pColumnIndex % 26);
}

function writeXlsx($xlsData,$filename)
{
  global $__appvar,$__debug;

  if(true == false && $__debug == true && file_exists($__appvar["basedir"] . '/classes/AE_cls_XLSXWriter.php'))
  {
    require_once $__appvar["basedir"] . '/classes/AE_cls_XLSXWriter.php';
    $writer = new XLSXWriter();
    $writer->setAuthor('AIRS');

    for ($regel = 0; $regel < count($xlsData); $regel++)
    {
      $tmpRow=array();
      for ($col = 0; $col < count($xlsData[$regel]); $col++)
      {
         if (is_array($xlsData[$regel][$col]))
         {
           $tmpRow[]=utf8_encode($xlsData[$regel][$col][0]);

         }
        else
        {

          $tmpRow[]=utf8_encode($xlsData[$regel][$col]);
        }
      }
      $writer->writeSheetRow('Sheet11', $tmpRow);
    }

    if($filename=='')
    { 
      header('Content-disposition: attachment; filename="file.xlsx"');
      header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
      header('Content-Transfer-Encoding: binary');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      $writer->writeToStdOut();
      exit(0);
    }
    else
    {
      $writer->writeToFile($filename);
    }
    //echo $writer->writeToString();
  }
  else
  {
  require_once $__appvar["basedir"] . '/classes/PHPExcel.php';
  require_once $__appvar["basedir"] . '/classes/PHPExcel/Writer/Excel2007.php';
  require_once $__appvar["basedir"] . '/classes/PHPExcel/ZipArchive.php';
  require_once $__appvar["basedir"] . '/classes/PHPExcel/Style.php';
  require_once $__appvar["basedir"] . '/classes/PHPExcel/Style/Font.php';
  require_once $__appvar["basedir"] . '/classes/PHPExcel/Style/Color.php';

  // Create new PHPExcel object
  $objPHPExcel = new PHPExcel();

  $fontObj = new PHPExcel_Style_Font();
  $fontObj->setBold(true);
  $styleObj = new PHPExcel_Style();
  $styleObj->setFont($fontObj);
  $styleObjecten=array('header'=>$styleObj);
  $styleObj = new PHPExcel_Style();
  $tmp=$styleObj->getNumberFormat();
  $tmp->setFormatCode('dd-mm-yyyy');
  $styleObjecten['date']=$styleObj;

  $activeSheetObject = $objPHPExcel->getActiveSheet();
  //$activeSheetObject->set

  $tmp = array();
  for ($regel = 0; $regel < count($xlsData); $regel++)
  {
    for ($col = 0; $col < count($xlsData[$regel]); $col++)
    {
      if (is_array($xlsData[$regel][$col]))
      {
        $tmp[$regel][$col] = utf8_encode($xlsData[$regel][$col][0]);
        $celOpmaak = $xlsData[$regel][$col][1]; //1=opmaak
        if(isset($styleObjecten[$celOpmaak]))
        {
          //echo "$col,$regel ->". $xlsObject->stringFromColumnIndex($col).($regel+1)."<br>\n";
          $activeSheetObject->setSharedStyle($styleObjecten[$celOpmaak], XlsStringFromColumnIndex($col). ($regel+1));
        }
      }  //0=waarde
      else
      {
        $waarde = $xlsData[$regel][$col];
        $datum = '';
        if (substr($waarde, 2, 1) == '-' && substr($waarde, 5, 1) == '-' && (substr($waarde, 6, 1) == '1' || substr($waarde, 6, 1) == '2') && strlen($waarde) == 10)
        {
          if ($waarde <> '')
          {
            $datum = round((adodb_form2jul($waarde) + (86400 * 25569)) / 86400);
          }
          $tmp[$regel][$col] = $xlsData[$regel][$col] = $datum;
          $celOpmaak='date';
          $activeSheetObject->setSharedStyle($styleObjecten[$celOpmaak], XlsStringFromColumnIndex($col). ($regel+1));

        }
        elseif (substr($waarde, 4, 1) == '-' && substr($waarde, 7, 1) == '-' && (substr($waarde, 0, 1) == '1' || substr($waarde, 0, 1) == '2') && strlen($waarde) == 10)
        {
          if ($waarde <> '')
          {
            $datum = round((adodb_db2jul($waarde) + (86400 * 25569)) / 86400);
          }
          $tmp[$regel][$col] = $xlsData[$regel][$col] = $datum;
          $celOpmaak='date';
          $activeSheetObject->setSharedStyle($styleObjecten[$celOpmaak], XlsStringFromColumnIndex($col). ($regel+1));
        }
        else
        {
          $tmp[$regel][$col] = utf8_encode($waarde);
        }
      }
    }
  }
  //$activeSheetObject->setSharedStyle($styleObj,'A1:C1');
  unset($xlsData);
  $activeSheetObject->fromArray($tmp);
  unset($tmp);
  //$objWriter = new PHPExcel_IOFactory();
  //$objWriter->createWriter($objPHPExcel, 'Excel2007');
  //$objWriter=PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
  $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

  if($filename)
    $objWriter->save($filename);//);
  else
  {
    header('Content-disposition: attachment; filename="file.xlsx"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    $objWriter->save('php://output');
    exit;
  }
  }

}

function pearson_correlation($x,$y)
{
  if(count($x)!==count($y)){return -1;}
  $x=array_values($x);
  $y=array_values($y);
  $xs=array_sum($x)/count($x);
  $ys=array_sum($y)/count($y);
  $a=0;$bx=0;$by=0;
  for($i=0;$i<count($x);$i++){
    $xr=$x[$i]-$xs;
    $yr=$y[$i]-$ys;
    $a+=$xr*$yr;
    $bx+=pow($xr,2);
    $by+=pow($yr,2);
  }
  $b = sqrt($bx*$by);
  return $a/$b;
}

//aetodo:
// dummy vertaalroutine
// verhuisd naar vars.php 20200525
//if (!function_exists("vt"))
//{
//  function vt($in)
//  {
//    return($in);
//  }
//}

function getCRMField($velden=array(),$where='1')
{
  $db=new DB();
  $query="DESC CRM_naw";
  $db->SQL($query);
  $db->Query();
  $dbVelden=array();
  while($data=$db->nextRecord('num'))
  {
    $dbVelden[]=$data[0];
  }
  $select="SELECT id";
  foreach($velden as $veld)
  {
    if(in_array($veld,$dbVelden))
    {
      $select.=",$veld ";
    }
  }
  $select.=" FROM CRM_naw WHERE $where limit 1";
  $db->SQL($select);
  $db->Query();
  $crmData=$db->nextRecord();
  return $crmData;
}



if ( !function_exists('sys_get_temp_dir')) {
  /**
   * Vervanging voor sys_get_temp_dir in php 5.1
   * @return bool|null|string
   */
  function sys_get_temp_dir() {
    if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
    if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
    if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
    $tempfile=tempnam(__FILE__,'');
    if (file_exists($tempfile)) {
      unlink($tempfile);
      return realpath(dirname($tempfile));
    }
    return null;
  }
}


if ( !function_exists('array_fill_keys')) {
  /**
   * Vervanging voor array_fill_keys in php 5.1
   * @param $keys
   * @param $value
   * @return array
   */
  function array_fill_keys($keys, $value) {
    $returnArray = array();
    foreach ( $keys as $key => $keysValue ) {
      $returnArray[$keysValue] = $value;
    }
    return $returnArray;
  }
}

function checkCrmTempPortefeuilles()
{
  global $__appvar;
  
  if($__appvar["bedrijf"] != 'ANO')
    return '';
    
  $melding="";
  $db=new DB();
  $query="SELECT id,tempPortefeuille FROM CRM_naw where tempPortefeuille<>''";
  $db->SQL($query);
  $db->query();
  $tempPortefeuilles=array();
  while($portefeuille=$db->nextRecord())
  {
    $tempPortefeuilles[]=$portefeuille['tempPortefeuille'];
  }
  $query="SELECT Portefeuille FROM Portefeuilles where Portefeuille IN('".implode("','",$tempPortefeuilles)."')";
  $db->SQL($query);
  $db->query();
  $nuAanwezigePortefeuilles=array();
  while($portefeuille=$db->nextRecord())
  {
    $nuAanwezigePortefeuilles[]=$portefeuille['Portefeuille'];
  }
  foreach ($nuAanwezigePortefeuilles as $portefeuille)
  {
    $query="SELECT id FROM CRM_naw WHERE portefeuille='".mysql_real_escape_string($portefeuille)."'";
    if($db->QRecords($query)>0)
    {
      $gekoppeld=$db->nextRecord();
      $melding.="Portefeuille $portefeuille is al gekoppeld aan CRM record ".$gekoppeld['id'].". tempPortefeuille voor $portefeuille wordt leeg gemaakt.\n";
      $query="UPDATE CRM_naw SET tempPortefeuille='' WHERE tempPortefeuille='".mysql_real_escape_string($portefeuille)."'";
      $db->SQL($query);
      $db->query();
    }
    else
    {
      $query="UPDATE CRM_naw SET Portefeuille='".mysql_real_escape_string($portefeuille)."', tempPortefeuille='' WHERE tempPortefeuille='".mysql_real_escape_string($portefeuille)."'";
      $db->SQL($query);
      $db->query();
      $melding.="TempPortefeuille $portefeuille nu omgezet naar Portefeuille.\n";
    }
  }
  return $melding;
}

function isSerialized($input)
{
  if ( ! is_string( $input ) ) {
    return false;
  }
  $data = @unserialize($input);
  return ($input === 'b:0;' || $data !== false);
}

if ( ! function_exists( 'getallheaders' ) ) {
  function getallheaders() {
    $headers = array();
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
  $headers = getallheaders();
}

function is_session_started()
{
  if ( php_sapi_name() !== 'cli' ) {
    if ( version_compare(phpversion(), '5.4.0', '>=') ) {
      return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
    } else {
      return session_id() === '' ? FALSE : TRUE;
    }
  }
  return FALSE;
}