<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2020/03/04 09:40:59 $
    File Versie         : $Revision: 1.4 $

    $Log: AE_cls_toegangsControle.php,v $
    Revision 1.4  2020/03/04 09:40:59  cvs
    CALL 8362

    Revision 1.3  2018/09/28 06:53:25  cvs
    call 6784

    Revision 1.2  2017/12/04 14:04:01  cvs
    call 4983 bug in init

    Revision 1.1  2017/01/04 13:03:09  cvs
    call 5542, uitrol WWB en TGC



*/

Class AE_cls_toegangsControle
{
  var $myNetwork;
  var $loginTry      = 0;
  var $blacklistTTL  = 0;
  var $blacklisted   = 0;
  var $whitelisted   = 0;
  var $blacklistData = array();
  var $disabled      = true;
  var $AWS           = false;


  function AE_cls_toegangsControle()
  {
    global $__appvar;

    $this->AWS = ($__appvar["AWSbased"] == "loaded");
    $network = explode(".",$_SERVER["REMOTE_ADDR"]);
    if ($network[0] == "10"  OR
        $network[0] == "172"  OR
        $network[0] == "192")
    {
      // als via private IP-range dan complete C net toegang geven
      $this->myNetwork = $network[0].".".$network[1].".".$network[2]."."."0/24";
    }
    else
    {
      // als via publiek IP dan eigen IP toevoegen zodat de gebruiker zichzelf niet kan blokkeren
      $this->myNetwork = $_SERVER["REMOTE_ADDR"];
    }

    $cfg = new AE_config();
    if (trim($cfg->getData("tgc_initModule")) == "")  //als tgc_initModule niet gevonden dan tabellen etc aanmaken
    {
      $this->initModule();
    }
    $this->loginTry     = $cfg->getData("tgc_loginMaxTry");
    $this->blacklistTTL = $cfg->getData("tgc_blacklist_ttl");
    $this->blacklisted  = $this->ipOnBlacklist();
    $this->whitelisted  = $this->ipOnWhitelist();
    if ($__appvar["tgc"] == "enabled")
    {
      $this->disabled    = false;
    }
    else
    {
      $this->blacklisted = false;
    }
    
  }

  function trackLogins($try)
  {
    if ($this->disabled)
    {
      return true;
    }

    $this->refreshBlacklist();
    if ($try >= $this->loginTry )
    {
      return $this->addToBlacklist("na ".($try)." loginpogingen op lijst geplaatst, via logins: ".implode(", ",array_unique($_SESSION["loginNames"])));

    }
    return true;

  }

  function ipLoginAllowed()
  {
    $db = new DB();
    if ($this->disabled)
    {
      return true;
    }

    $split = explode(".",$this->myNetwork);
    if ((int)$plit[3] == 0)
    {
      $where = "`ip` LIKE '".$split[0].".".$split[1].".".$split[2].".%'";
    }
    else
    {
      $where = "`ip` = '".$_SERVER["REMOTE_ADDR"]."' ";
    }
    $where .= " OR ip = '*' ";

    $query = "
    SELECT
      `ip`,
      UNIX_TIMESTAMP(`onlineDatum`) AS `online`,
      UNIX_TIMESTAMP(`offlineDatum`) AS `offline`,
      loginVan,
      loginTot
    FROM
      `tgc_ipAccessList` 
    WHERE 
    ".$where;
//debug($query);
    $rec = $db->lookupRecordByQuery($query);
    $now = time();
    $ip = "[".$rec["ip"]."]";
    if ($rec["online"] < $now AND $rec["offline"] > $now)
    {
      $split = explode(":", $rec["loginVan"]);
      $van = (3600 * $split[0]) + (60 * $split[1]);
      $split = explode(":", $rec["loginTot"]);
      $tot = (3600 * $split[0]) + (60 * $split[1]);
      $nu = (3600 * date("G") + (60 * date("i")));
//      debug(array($nu,$van,$tot,$rec,$now ));
//   exit;
      if (($van > $nu OR $tot < $nu) AND ($van+$tot <> 0))
      {
        $this->logEntry("login", "loginpoging van buiten toegestane tijden");
        return "blockLogin";
      }
      else
      {
        return true;
      }

    }
    else
    {

      $this->logEntry("login", "loginpoging van IP adres waarvan de loginperiode niet geldig is");
      return "blockPeriod";
    }

    return ($this->blacklistData = $db->lookupRecordByQuery($query));

  }

  function ipOnBlacklist()
  {
    if ($this->disabled !== true)
    {
      return false;
    }
    $db = new DB();
    $query = "SELECT * FROM tgc_blacklist WHERE ip = '".$_SERVER[REMOTE_ADDR]."' ";
    return is_array($this->blacklistData = $db->lookupRecordByQuery($query));

  }

  function ipOnWhitelist()
  {
    if ($this->disabled !== true)
    {
      return false;
    }
    $db = new DB();
    $query = "SELECT * FROM tgc_ipAccessList WHERE ip = '".$this->myNetwork."' AND whitelist = 1 ";
    return is_array($db->lookupRecordByQuery($query));

  }

  function addToBlacklist($txt)
  {

    if ($this->disabled)
    {
      return true;
    }
    if ($this->whitelisted)
    {
      $this->logEntry("blacklist","IP niet toegevoegd wegens whitelist vermelding");
      return "userBlock";
    }

    $db = new DB();
    $offList = date("Y-m-d H:i:s",time() + (3600 * $this->blacklistTTL));
    $onList = date("Y-m-d H:i:s",time() );
    $query = "DELETE FROM tgc_blacklist WHERE ip = '".$_SERVER[REMOTE_ADDR]."'";
    $db->executeQuery($query);
    $query = "
      INSERT INTO tgc_blacklist SET 
        add_date = NOW(),
        change_date = NOW(),
        ip = '".$_SERVER[REMOTE_ADDR]."',
        onList = '$onList',
        offList = '$offList',
        memo = '$txt'
        ";
    if ($txt <> "")
    {
      $this->logEntry("blacklist",$txt);
    }
    $db->executeQuery($query);
    session_destroy();
    session_write_close();
    return "blockBlacklist";
  }

  function refreshBlacklist()
  {
    if ($this->disabled)
    {
      return true;
    }
    $db = new DB();
    $query = "DELETE FROM tgc_blacklist WHERE offList < NOW()";
    $db->executeQuery($query);
  }

  function logEntry($module="login",$txt="")
  {
    if ($this->disabled)
    {
      return true;
    }
    if (substr($_SERVER[REMOTE_ADDR],0,3) == "10." AND $this->AWS)
    {
      return true;   // keepalives van AWS niet loggen
    }
    $db = NEW DB();
    $query = "
      INSERT INTO `tgc_log` SET
        stamp = NOW(),
        ip    = '".$_SERVER[REMOTE_ADDR]."',
        memo  = '".mysql_real_escape_string($module."::".$txt)."'";
    $db->executeQuery($query);

  }

  function writeHTaccess()
  {
    if ($this->disabled)
    {
      return true;
    }

    global $USR;
    $cfg = new AE_config();
    $cfg->addItem("tgc_last_htaccessWrite",date("d-m-Y H:i:s")." ($USR)");
    $db = new DB();

    $query = "SELECT id FROM `tgc_ipAccessList` WHERE ip = '*' ";
    if ($testRec = $db->lookupRecordByQuery($query))
    {

      if ($this->AWS)
      {
        $htAccessContent = <<< EOB
# geen IP controle ingesteld door * entry
EOB;
      }
      else
      {


      $htAccessContent = <<< EOB
#RewriteEngine On
#RewriteCond %{HTTPS} off
#RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}


# geen IP controle ingesteld door * entry
EOB;
      }
    }
    else
    {

      if ($this->AWS)
      {
        $htAccessContent = <<< EOB

<Limit GET POST PUT>
  order deny,allow
  deny from all
#msg AWS loadbalancer
  allow from 10.51.0.0/16  
  allow from 10.52.0.0/16  
  allow from 10.53.0.0/16 
#msg AIRS kantoor Tricht
  allow from 212.121.116.132
#msg Eigen IP adres/netwerk van de gebruiker
EOB;
      }
      else
      {
        $htAccessContent = <<< EOB
#RewriteEngine On
#RewriteCond %{HTTPS} off
#RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}

<Limit GET POST PUT>
  order deny,allow
  deny from all
  
#msg AIRS kantoor Tricht
  allow from 212.121.116.132
#msg Eigen IP adres/netwerk van de gebruiker
EOB;
      }

      $htAccessContent .= "
  allow from " . $this->myNetwork . "
      
# gegenereerd d.d. " . date("d-m-Y H:i:s") . " door $USR
# IP adressen vanuit de AIRS toegangscontrole";

      $query = "SELECT ip, locatie FROM `tgc_ipAccessList` ORDER BY ip";

      $db->executeQuery($query);
      while ($rec = $db->nextRecord())
      {
        $htAccessContent .= "\n#msg ".$rec["locatie"]."\n  allow from " . $rec["ip"];
      }
      $htAccessContent .= "\n</limit>";
      $d = getcwd();
    }

    file_put_contents("../html/.htaccess", $htAccessContent);
    if ($this->AWS)
    {
      file_put_contents("../data/.htaccess", $htAccessContent);
    }

  }


  function initModule()
  {
    global $USR;
    $init = false;
    include_once("../classes/AE_cls_SQLman.php");
    $tst = new SQLman();
    $cfg = new AE_config();

    if ($cfg->getData("tgc_initModule") == "")
    {
      $cfg->addItem("tgc_initModule",date("d-m-Y H:i:s")." ($USR)");
      // set defaults voor module
      $cfg->addItem('tgc_blacklist_ttl',1);   // 1 uur op blacklist
      $cfg->addItem('tgc_loginMaxTry',3);     // 3 loginpogingen

      $init = true;
    }
    $tst->tableExist("tgc_ipAccessList",true);
    $tst->changeField("tgc_ipAccessList","ip",array("Type"=>" varchar(20)","Null"=>false));
    $tst->changeField("tgc_ipAccessList","locatie",array("Type"=>" varchar(60)","Null"=>false));
    $tst->changeField("tgc_ipAccessList","bedrijf",array("Type"=>" varchar(15)","Null"=>false));
    $tst->changeField("tgc_ipAccessList","onlineDatum",array("Type"=>" date","Null"=>false));
    $tst->changeField("tgc_ipAccessList","offlineDatum",array("Type"=>" date","Null"=>false));
    $tst->changeField("tgc_ipAccessList","loginVan",array("Type"=>" time","Null"=>false));
    $tst->changeField("tgc_ipAccessList","loginTot",array("Type"=>" time","Null"=>false));
    $tst->changeField("tgc_ipAccessList","logging",array("Type"=>" text","Null"=>false));
    $tst->changeField("tgc_ipAccessList","memo",array("Type"=>" text","Null"=>false));
    $tst->changeField("tgc_ipAccessList","whitelist",array("Type"=>" tinyint","Null"=>false));

    $tst->tableExist("tgc_blacklist",true);
    $tst->changeField("tgc_blacklist","ip",array("Type"=>" varchar(20)","Null"=>false));
    $tst->changeField("tgc_blacklist","memo",array("Type"=>" text","Null"=>false));
    $tst->changeField("tgc_blacklist","onList",array("Type"=>" datetime","Null"=>false));
    $tst->changeField("tgc_blacklist","offList",array("Type"=>" datetime","Null"=>false));

    $tst->tableExist("tgc_log",true);
    $tst->changeField("tgc_log","ip",array("Type"=>" varchar(20)","Null"=>false));
    $tst->changeField("tgc_log","dns",array("Type"=>" varchar(100)","Null"=>false));
    $tst->changeField("tgc_log","memo",array("Type"=>" text","Null"=>false));
    $tst->changeField("tgc_log","stamp",array("Type"=>" datetime","Null"=>false));
    if ($init)
    {
      $db = new DB();
      $query = "SELECT ip FROM tgc_ipAccessList WHERE ip='212.121.116.132'";
      if (!$db->lookupRecordByQuery($query))
      {
        $query = "INSERT INTO tgc_ipAccessList SET 
        ip='212.121.116.132', 
        locatie='AIRS support', 
        add_date=NOW(), 
        change_date=NOW(), 
        add_user='init', 
        change_user='init', 
        onlineDatum = NOW(), 
        whitelist = 1,
        offlineDatum= '2029-12-31',
        `memo`='Dit item NIET verwijderen, dit is een systeem record. U blokkeert uzelf als dit record niet bestaat' ";

        $db->executeQuery($query);
      }

      $split = explode("/",$this->myNetwork);
      $query = "SELECT ip FROM tgc_ipAccessList WHERE ip='".$this->myNetwork."'";
      if (!$db->lookupRecordByQuery($query))
      {
        $query = "INSERT INTO tgc_ipAccessList SET 
        ip='" . $this->myNetwork . "', 
        locatie='Beheerder/lokaal netwerk', 
        add_date=NOW(), 
        change_date=NOW(), 
        add_user='init', 
        change_user='init', 
        onlineDatum = NOW(), 
        whitelist = 1,
        `memo`='Dit item NIET verwijderen, dit is een systeem record. U blokkeert uzelf als dit record niet bestaat' ,
        offlineDatum= '2029-12-31'";

        $db->executeQuery($query);
      }
      $this->writeHTaccess();
    }
  }
}

