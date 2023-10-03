<?php
/*
CREATE TABLE `TRM_verwerkLog` (
  `id` int(11) NOT NULL auto_increment,
  `add_date` datetime default '0000-00-00 00:00:00',
  `add_user` varchar(10) default NULL,
  `change_date` datetime default '0000-00-00 00:00:00',
  `change_user` varchar(10) default NULL,
  `batchid` varchar(20) NOT NULL,
  `regelnr` int NOT NULL,
  `rekeningnr` varchar(24) NOT NULL,
  `query` text NOT NULL,
  PRIMARY KEY  (`id`)
) ;
*/
class TRM_log
{
  var $batchid;
  var $user;
  var $db;
  var $enabled = false;
  function TRM_log($user="")
  {
    $this->batchid = date("YmdHis-").rand(1000,9999);
    $this->user = $user;
    $this->db = new DB();
  }
  
  function add($rekeningnr, $query, $regelnr=0)
  {
    $query = "
      INSERT INTO TRM_verwerkLog SET
        add_user = '{$this->user}',
        add_date = NOW(),  
        change_user = '{$this->user}',
        change_date = NOW(),  
        batchid = '{$this->batchid}',
        regelnr = '$regelnr',
        rekeningnr = '{$rekeningnr}',
        query = '".mysql_real_escape_string($query)."'
      ";
     if ($this->enabled)
     {
      $this->db->executeQuery($query);   
     } 
  }
}

