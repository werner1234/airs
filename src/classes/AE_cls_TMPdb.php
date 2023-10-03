<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2011/02/25 09:42:53 $
 		File Versie					: $Revision: 1.1 $

 		$Log: AE_cls_TMPdb.php,v $
 		Revision 1.1  2011/02/25 09:42:53  cvs
 		*** empty log message ***
 		


*/

//error_reporting(E_NONE);
class tempDB
{

  var $resultset;
  var $debug = true;
  var $errorstr;

  var $db;
  var $tableName;
  var $tableFields;

// ===================================================
  function tempDB($id=1)
  {
  	$this->db = new DB($id);
  	$this->tableName = "__TMP_table";
  	$this->resultset = "";

    $this->addTableField("portefeuille","varchar(100)");
    $this->addTableField("rekeningnr","varchar(100)");
    $this->addTableField("fonds","varchar(100)");
    $this->addTableField("aantal","decimal(15,5)");
    $this->addTableField("bron","varchar(20)");

  }

// ===================================================
  function exit_with_error($txt)
  {
	  $this->errorstr = $txt;
	  if ($this->debug)
	  {
      $out = addslashes("tempDB class foutmelding: $txt");
      echo "<script>alert('$out');</script>";
	  }
    return false;
  }

// ===================================================
  function getCreate()
  {
    $TempCreatequery .= "CREATE TABLE `".$this->tableName."` (\n  ";
    $TempCreatequery .= "`id` int(11) NOT NULL auto_increment,\n  ";
    $TempCreatequery .= implode("\n  ",$this->tableFields);
    $TempCreatequery .= "\nPRIMARY KEY  (`id`) );";
    return $TempCreatequery;
  }

// ===================================================
  function addTableField($name,$type)
  {
    $this->tableFields[] = "`".$name."` ".$type." default NULL,";
  }

// ===================================================
  function clearTableFields()
  {
    $this->tableFields = array();
  }


// ===================================================
//

  function setTableName($name)
  {
    $this->tableName = $name;
  }

// ===================================================
  function dropTable($tableName="")
  {
    if ($tableName == "") $tableName = $this->tableName;
    $query = "DROP TABLE IF EXISTS ".$tableName;
    $this->db->SQL($query);

    if ($this->db->Query())
      return true;
    else
      $this->exit_with_error("Fout tijdens verwijderen tijdelijke tabel: $tablename");
  }

// ===================================================
  function createTable()
  {
    $this->dropTable();                  //drop table before create..

    $q = $this->getCreate();             // get create statement
    $this->db->SQL($q);

    if ($this->db->Query())
      return true;
    else
      $this->exit_with_error("fout tijdens aanmaken tijdelijke tabel");
  }

}
?>