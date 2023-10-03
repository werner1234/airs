<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/12/17 16:23:15 $
 		File Versie					: $Revision: 1.17 $

 		$Log: AE_cls_mysql.php,v $
 		Revision 1.17  2014/12/17 16:23:15  rvv
 		*** empty log message ***
 		
 		Revision 1.16  2014/02/09 11:06:14  rvv
 		*** empty log message ***
 		
 		Revision 1.15  2013/12/11 10:09:14  cvs
 		*** empty log message ***
 		
 		Revision 1.14  2013/01/23 16:44:11  rvv
 		*** empty log message ***
 		
 		Revision 1.13  2009/12/20 15:28:11  rvv
 		*** empty log message ***

 		Revision 1.12  2009/12/20 14:28:51  rvv
 		*** empty log message ***

 		Revision 1.11  2009/11/15 16:44:20  rvv
 		*** empty log message ***

 		Revision 1.10  2008/12/30 15:49:04  rvv
 		*** empty log message ***

 		Revision 1.9  2008/05/16 08:10:09  rvv
 		*** empty log message ***

 		Revision 1.8  2006/01/23 14:13:43  jwellner
 		no message

 		Revision 1.7  2005/12/28 07:40:57  jwellner
 		no message

 		Revision 1.6  2005/12/16 14:43:09  jwellner
 		classes aangepast

 		Revision 1.2  2005/12/14 08:33:16  cvs
 		*** empty log message ***

 		Revision 1.1.1.1  2005/11/09 15:16:16  cvs
 		no message

 		Revision 1.2  2005/11/09 15:09:56  cvs
 		*** empty log message ***


*/

//error_reporting(E_NONE);
class DB
{
  var $resource;
  var $querystr;
  var $resultset;
  var $dbId = 1;
  var $debug = true;
  var $connectiontimeout;
  var $errorstr;
// ===================================================
  function DB($id=1)
  {
    $this->resource  = "";
    $this->resultset = "";
    $this->dbId = $id;
    $this->connect($id);
  }

// ===================================================
  function exit_with_error($txt)
  {
    $this->errorstr = $txt;
    if ($this->debug)
    {
      $out = addslashes("DB class foutmelding: $txt");
      echo $out;
      //   echo "<script>alert('$out');</script>";
    }
    return false;
  }


// ===================================================
  function connect($id=1)
  {
    global $_DB_resources;

    if (!isset ($_DB_resources[$id]['server']))
    {
      echo $id;
      return $this->exit_with_error("Connectie variabelen niet gedefinieerd ".$id);
    }
    $this->resource = mysql_connect($_DB_resources[$id]['server'],
      $_DB_resources[$id]['user'],
      $_DB_resources[$id]['passwd']);
    if (!$this->resource)
      return $this->exit_with_error("Connectie met DB server mislukt");

    if (!mysql_select_db($_DB_resources[$id]['db'], $this->resource))
      return $this->exit_with_error("Connectie met tabel mislukt");
    return true;
  }
  
  function close()
  {
    if(isset($this->resource))
    {
      return mysql_close($this->resource);
    }
  }

  function SQL($txt="")
  {
    if (empty($txt))
      return $this->exit_with_error("Querytekst mag niet leeg zijn");
    $this->querystr = $txt;
  }

  function Query()
  {
    if (empty($this->querystr))
      return $this->exit_with_error("Geen query gedefinieerd");

    $this->resultset = mysql_query($this->querystr, $this->resource);

    if (mysql_error($this->resource))
      return $this->exit_with_error("Fout in Query :$this->querystr ".mysql_error($this->resource));
    return true;
  }

  function nextRecord($type ="",$stripslashes=true)
  {
    switch ($type)
    {
      case "num":
        $_data = mysql_fetch_array($this->resultset, MYSQL_NUM);
        break;
      case "both":
        $_data = mysql_fetch_array($this->resultset, MYSQL_BOTH);
        break;
      default:
        $_data = mysql_fetch_array($this->resultset, MYSQL_ASSOC);
        break;
    }
    if (mysql_error($this->resource))
      return $this->exit_with_error("Fout in Nextrecord :$this->querystr ".mysql_error($this->resource));
    //   if($stripslashes)
    //     $_data = arrayStripslashes($_data);

    return $_data;
  }

  function lookupRecord($type="",$stripslashes=true)
  {
    $this->query();
    return $this->nextRecord($type,$stripslashes);
  }

  function lookupRecordByQuery($query,$type="",$stripslashes=true)
  {
    $this->querystr = $query." LIMIT 1";
    $this->query();
    return $this->nextRecord($type, $stripslashes);
  }

  function records()
  {
    return mysql_num_rows($this->resultset);
  }

  function gotoRow($row)
  {
    return mysql_data_seek($this->resultset,$row);
  }

  function mutaties()
  {
    return mysql_affected_rows();
  }

  function last_id()
  {
    return mysql_insert_id();
  }

  function cleanUp()
  {
    mysql_free_result($this->resource);
  }

  function resultReady()
  {

    if ($this->resultset)
      return true;
  }

  function QRecords($Q,$id=1)
  {

    $this->SQL($Q);
    $this->query();
    return $this->records();
  }

  function executeQuery($query="")
  {
    if ($query == "")
      return $this->exit_with_error("Geen query gedefinieerd");

    $this->querystr = $query;
    $this->resultset = mysql_query($this->querystr, $this->resource);

    if (mysql_error($this->resource))
      return $this->exit_with_error("Fout in Query :$this->querystr ".mysql_error($this->resource));
    return true;
  }

}

?>