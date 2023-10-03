<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2017/01/04 13:03:09 $
 		File Versie					: $Revision: 1.7 $

 		$Log: AE_cls_SQLman.php,v $
 		Revision 1.7  2017/01/04 13:03:09  cvs
 		call 5542, uitrol WWB en TGC
 		
 		Revision 1.6  2015/01/28 13:16:42  rm
 		Participanten
 		
 		Revision 1.5  2014/06/18 06:23:04  cvs
 		toevoegen changeIndex()
 		
 		Revision 1.4  2014/06/13 12:12:53  cvs
 		update 5-6-2014
 	

*/



class SQLman
{
  var $classResource;
  var $debug = true;
  var $error = false;
  var $counter = array();

  function SQLman($rId=1)
  {
     $this->classResource = new DB($rId);
  }

  function resetCounters()
  {
    $this->counter = array();
  }
  // ===================================================
  function exit_with_error($txt)
  {
	  $this->errorstr = $txt;
	  if ($this->debug)
	  {
      $out = addslashes("DB beheer class: $txt");
      echo "<script>alert('$out');</script>";
	  }
	  $this->error = true;
    return false;
  }

  /**
   * Haal gegevens van een table op
   *
   * @param bij output
   * @param "records" geeft het aantal records terug,
   * @param "size" geeft tablegrootte,
   * @param "create" aanmaakdatum,
   * @param "update" mutatiedatum
   */
  function tableStatus($tableName, $output="")
  {
    $db = &$this->classResource;
    $query = "SHOW TABLE STATUS LIKE '".$tableName."' ";
    $db->SQL($query);
    $rec = $db->LookupRecord();
    $output = strtolower($output);
    switch ($output)
    {
      case "records":
        $out = $rec["Rows"];
        break;
      case "size":
        $out = number_format((($rec["Data_length"]/1024)/1024),2)." Mb";
        break;
      case "create":
        $out = dbdatum($rec["Create_time"])." ".dbtijd($rec["Create_time"]);
        break;
      case "update":
        $out = dbdatum($rec["Update_time"])." ".dbtijd($rec["Update_time"]);
        break;

      default:
        $out = $rec;
    }
    return $out;

  }

  
  /**
   * function changeField
   * 
   * @param  "table"  => tabelnaam
   * @param  "fieldname" => veldnaam
   * @param  "options"  => optiearray  
   * "action" => "DROP" verwijdert de veld
   * "Default" => standaardwaarde v/h veld
   * "Type" => mysql veldtype
   * "Null" => null waarde toestaan
   * "Extra" => extra opties
   */
  function changeField($table,$fieldname,$options=array())
  {
    if (count($options) < 1 OR !is_array($options))         { $this->exit_with_error("Er moet minimaal 1 optie worden op gegeven"); }
    if (!$options["Type"] AND $options["action"] != "DROP") { $this->exit_with_error("Optie `Type` is verplicht");     }

    $db = &$this->classResource;

    $query = "SHOW TABLE STATUS LIKE '".$table."' ";
    $db->SQL($query);
    if (!$rec = $db->LookupRecord())                  { $this->exit_with_error("De tabelnaam is ongeldig ".$query); }

    if (!$this->error)  // alleen door als geen fouten tot nu toe
    {
      $query = "SHOW FIELDS FROM ".$table;
      $db->SQL($query);
      $db->query();
      $fieldExist = false;
      while ($row = $db->NextRecord())
      {
        if ($row["Field"] == $fieldname)
        {
          $fieldExist = true;
          $dbField = $row;
        }
      }
      
      /** if the field does not exist we dont have to drop it **/
      if ( $options['action']  == 'DROP' && $fieldExist === false ) {
        $this->counter['SQL'][] = 'Cannot drop field (' . $fieldname . ') it does not exist!';
        return false;
      }
        
      if ($fieldExist AND $options["action"]  == "DROP")
      {
        $query = "ALTER TABLE $table DROP `".$fieldname."` ";
        $this->counter['SQL'][] = $query;
        return $db->executeQuery($query);
      }
      
      $action = ($fieldExist)?"MODIFY":"ADD";
      $nullTxt = (!$options["Null"])?"NOT NULL":"";

      $skipModify = false;

      if ($action == "MODIFY")
      {
        $typeStr    = $dbField["Type"];
        if (stristr($typeStr,"int"))
        {
          $typeStr = trim(substr($typeStr,0,strpos($typeStr,"(")));
        }

        $nullStr    = ($options["Type"])?"NO":"YES";
        $defaultArray = explode("'",$options["Default"]);
        $test1 = ( $typeStr               == trim(strtolower($options["Type"]))     );
        $test2 = ( trim($dbField["Null"]) == $nullStr                               );
        $test3 = ( $dbField["Default"]    == $defaultArray[1]  );
        $test4 = ( $dbField["Extra"]      == trim(strtolower($options["Extra"]))    );

        $skipModify = ( $test1 AND $test2 AND $test3 AND $test4);

      }
      
      /** if we have a rename dont create the field **/
      if ( isset ($options['rename']) ) {
        $skipModify = true;
      }
      /** try to rename the field if it exists **/
      if ( isset ($options['rename']) && ! empty ($dbField) && $options['rename'] != $dbField ) {
        $skipModify = false; //reset the skip
        $action = 'CHANGE'; //change action to change
        $options["Type"] = $options['rename'] . ' ' .$options["Type"]; //set new field name
      }

      if ($skipModify)
        $this->counter["skipped"]++;
      else
      {
        $query = "ALTER TABLE $table $action `".$fieldname."` ".$options["Type"]." ".$options["Extra"]." ".$nullTxt." ".$options["Default"]." ";
        $db->SQL($query);
        $db->query();
        $this->counter["succes"]++;
        $this->counter["SQL"][] = "(".$test1."-".$test2."-".$test3."-".$test4.") ".$query;
       // $this->counter["SQL"][] = "(".$dbField["Default"]." // ".trim(strtolower($options["Default"]));
      }
    }
  }


  /**
   * function changeIndex
   * 
   * @param  "table"  => tabelnaam
   * @param  "indexname" => indexnaam
   * @param  "options"  => optiearray  
   * "action" => "DROP" verwijdert de index
   * "columns" => array() array met indexvelden
   * "unique" => true als de index unieke keys moet bevatten
   */
  function changeIndex($table,$indexname,$options=array())
  {

    if (count($options) < 1 OR !is_array($options))             { $this->exit_with_error("Er moet minimaal 1 optie worden op gegeven"); }
    if (!$options["columns"] AND $options["action"] != "DROP")  { $this->exit_with_error("Optie `columns` is verplicht");               }  
    
    $nonUnique = !$options["unique"];
    
    $db = &$this->classResource;

    $query = "SHOW TABLE STATUS LIKE '".$table."' ";
    $db->executeQuery($query);
    if (!$rec = $db->nextRecord() )                   { $this->exit_with_error("De tabelnaam is ongeldig ".$query);            }

    if (!$this->error)  // alleen door als geen fouten tot nu toe
    {
      $query = "SHOW INDEX FROM ".$table;
      $db->executeQuery($query);
      $fieldExist = false;
      while ($row = $db->NextRecord())
      {
        if ($row["Key_name"] == $indexname)
        {
          $fieldExist = true;
          $dbField = $row;
        }
      }
      
      if ($fieldExist)
      {
        // als index verwijderd moet worden
        if ($options["action"] == "DROP" )
        {
          $query = "ALTER TABLE $table DROP INDEX `".$indexname."` ";
          return $db->executeQuery($query);
        }
        
        // veldnamen bij de index ophalen
        $query = "SHOW INDEX FROM $table WHERE Key_name = '$indexname' ";
        $db->executeQuery($query);
        while($indexRec  = $db->nextRecord())
        {
          $dbField["columns"][$indexRec["Seq_in_index"]] = $indexRec["Column_name"];
        }
      }
      
      
      if (is_string($options["columns"]))
        $columnsArray = (array)$options["columns"];
      else  
        $columnsArray = $options["columns"];
      
      // testen of index al bestaat
      // test1 = is de indexsortering uniek?
      $test1 = ( $dbField["Non_unique"] == $nonUnique);
      // test2 = zijn de zoekvelden aanwezig en gelijk
      $test2 = true;
      for($x=0; $x < count($dbField["columns"]); $x++)
      {
        if ($dbField["columns"][$x+1] <> $columnsArray[$x]) $test2 = false;
      }
      // test3 = zijn het aantal zoekvelden gelijk?
      $test3 = count($dbField["columns"]) == count($columnsArray); 
      
      $skipModify = ( $test1 AND $test2 AND $test3 );
      
      if ($skipModify)
        $this->counter["skipped"]++;
      else
      {
        // bestaande index eerst droppen daarna opnieuw aanmaken
        if ($fieldExist)
        {
          $query = "ALTER TABLE $table DROP INDEX`" . $indexname . "` ";
          $db->executeQuery($query);
        }
        $columns = "(`".implode("`,`",$columnsArray)."`)";
        $unique = ($options["unique"])?"UNIQUE":"";
        $query = "ALTER TABLE $table ADD ".$unique." INDEX `".$indexname."` ".$columns;
        $db->executeQuery($query);
        $this->counter["succes"]++;
  
      }
    }
  }
  /**
   * Controleer of de tabel bestaat
   * @param $tableName = naam v/d table
   * @param $create = boolean true als tabel aangemaakt moet worden wanneer deze nog niet bestaat
   */
  function tableExist($tableName,$create=false)
  {
    $db = &$this->classResource;

    $query = "SHOW TABLE STATUS LIKE '".$tableName."' ";
    $db->SQL($query);
    if (!$rec = $db->LookupRecord())  // bestaat table
    {
      if ($create)  // als create dan een standaard lege tabel maken
      {
        $query = "
  CREATE TABLE ".$tableName." (
  `id` int(11) NOT NULL auto_increment,
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)  );
        ";
        //return $db->executeQuery($query);  // tabel aanmaken en result doorgeven en exit
        return ($db->executeQuery($query))?"added":false;  // tabel aanmaken en result doorgeven en exit
      }
      else
        return false;  // geen create dus false en exit
    }
    else
      return true; // tabel bestaat dus true en exit
  }

  /**
   * Haal alle tabelObjectNamen uit het AE framewerk
   * @param $path = afwijkend path
   */
  function GetDatabaseObjects($path="")
  {
    global $__appvar;
    $_DB = array();

    if ($path=="") $path =  $__appvar["basedir"].$__appvar["databaseObjects"];

	  if ($handle = opendir($path))
	  {
	  	while ($file = readdir($handle))
		  {
			  if ($file != "."              AND
			      $file != ".."             AND
			      is_file($path."/".$file)  AND
			      substr($file,-4) == ".php" )
			  {
				    $_DB[] = substr($file,0,-4);
			  }
		  }
		  closedir($handle);
	  }
	  return $_DB;
  }

  /**
   * Haal alle velddefinities uit een MYSQL tabel
   * @param $tableName = tabelnaam
   */
  function getFieldInfoFromTable($tableName)
  {
    $output = array();
    $db = &$this->classResource;
    $query = "SHOW FIELDS FROM ".$tableName;
    $db->SQL($query);
    $db->query();
    while ($row = $db->NextRecord())
    {
      $output[$row["Field"]] = array("Type"=>$row["Type"],"Key"=>$row["Key"],"Default"=>$row["Default"],"AllowNull"=>($row["Null"] == "YES"),"Extra"=>$row["Extra"]);
    }
    return $output;
  }

  /**
   * Haalt tabelinfo op vanuit de huidige MYSQL database
   * zoals aantal records, grootte en mutatiedatum
   *
   * @param $table = tabelnaam (geen tabelnaam geeft alle tabellen terug)
   *
   */
  function databaseInfo($table="")
  {
    $db = &$this->classResource;
    $oneTableStr = ($table <> "")?" LIKE '".$table."' ":"";
    $query = "SHOW TABLE STATUS".$oneTableStr;
    $db->SQL($query);
    $db->Query();
    while ($row = $db->NextRecord())
    {
      $size = $row["Data_length"]/1024;
      $sizeEenheid = "Kb";
      if ($size > 1024)   { $size = $size/1024 ; $sizeEenheid = "Mb"; };
      if ($size > 1024)   { $size = $size/1024 ; $sizeEenheid = "Gb"; };
      $size = round($size,2);
      $output[$row["Name"]] = array("rows"=>$row["Rows"],"size"=>$size." ".$sizeEenheid,"update"=>$row["Update_time"],"comment"=>$row["Comment"]);
    }
    return $output;
  }

  /**
   * Haalt DB info uit AE-framework object bestanden en controleert of de tabel bestaat en controleert/muteert de veld definities
   *
   * @param $table = tabelnaam (geen tabelnaam doe check op alle tabellen)
   */
  function updateDB($table="")
  {
    $this->resetCounters();
    if ($table == "")
      $tableArray = $this->GetDatabaseObjects();
    else
      $tableArray = array($table);

    for ($i=0; $i < count($tableArray); $i++)
    {

      $_tmpObject = new $tableArray[$i];
      $velden = $_tmpObject->data["fields"];
      $table  = $_tmpObject->data["table"];
      $identity = $_tmpObject->data["identity"];
      $this->tableExist($table,true);
      while (list($key,$value) = each($velden))
      {
        $specs = $this->constructChangeField($key,$value);
        $this->changeField("$table","$key",$specs);
      }

    }



  }

  function constructChangeField($key,$value)
  {
    $_varTypesFormatted = array("char","varchar","double","decimal");
    $type = strtolower($value["db_type"]);
    $size = $value["db_size"];
    if (in_array($type,$_varTypesFormatted))
    {
      $type .= "(".$size.")";  // als grootte vereist deze opgeven
    }
    if ($key == "id" AND $value["db_extra"] == "")  // autonummering aanzetten op id veld
      $value["db_extra"] = "auto_increment";
    $default = ($value["default_value"])?"DEFAULT '".$value["default_value"]."' ":"";
    $extra   = ($value["db_extra"])?$value["db_extra"]:"";
    return array("Type"=>" $type","Null"=>false, "Default"=>$default , "Extra"=>$extra);
  }
  
  function showCreate($table)
  {
     $db = &$this->classResource;
     $query = "SHOW CREATE TABLE `{$table}`";
     $db->executeQuery($query);
     $createRec = $db->nextRecord();
     return $createRec["Create Table"];
  }
  
  function showIndex($table)
  {
    
    $db = &$this->classResource;
     $query = "SHOW INDEX FROM `{$table}`";
     $db->executeQuery($query);
     while ($indexRec = $db->nextRecord())
     {
      $output[] = $indexRec;
     }
     return $output;
  }

}

?>